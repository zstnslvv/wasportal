<?php

$autoload = __DIR__ . '/../backend/vendor/autoload.php';
if (!file_exists($autoload)) {
    fwrite(STDERR, "Missing vendor/autoload.php. Run composer install in /var/www/backend.\n");
    exit(1);
}
require $autoload;

use App\RedisClient;
use App\Uploads;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$redis = RedisClient::fromEnv();

while (true) {
    $job = $redis->blockingPopJob(5);
    if ($job === null) {
        continue;
    }

    $jobId = $job['job_id'] ?? '';
    if ($jobId === '') {
        continue;
    }

    try {
        $redis->setJobStatus($jobId, ['status' => 'running', 'progress' => 5]);

        if (($job['type'] ?? '') === 'merge_images') {
            handleMergeImages($job, $redis);
        } else {
            handleConvert($job, $redis);
        }

        $redis->setJobStatus($jobId, [
            'status' => 'done',
            'progress' => 100,
            'download_url' => '/api/download/' . $jobId,
        ]);
    } catch (Throwable $e) {
        $redis->setJobStatus($jobId, [
            'status' => 'error',
            'error_message' => $e->getMessage(),
        ]);
    }
}

function handleConvert(array $job, RedisClient $redis): void
{
    $jobId = $job['job_id'];
    $fileId = $job['file_id'];
    $target = $job['target_format'];

    $meta = Uploads::readMeta($fileId);
    if ($meta === null) {
        throw new RuntimeException('file not found');
    }

    $sourcePath = $meta['path'];
    $sourceExt = strtolower($meta['ext']);

    $outputDir = __DIR__ . '/../backend/storage/outputs/' . $jobId;
    if (!is_dir($outputDir) && !mkdir($outputDir, 0775, true) && !is_dir($outputDir)) {
        throw new RuntimeException('cannot create output dir');
    }

    $outputPath = $outputDir . '/result.' . $target;

    switch ($sourceExt) {
        case 'xls':
        case 'xlsx':
        case 'doc':
        case 'docx':
        case 'ppt':
        case 'pptx':
            convertWithLibreOffice($sourcePath, $outputDir, $target);
            break;
        case 'pdf':
            if ($target === 'txt') {
                runProcess('pdftotext ' . escapeshellarg($sourcePath) . ' ' . escapeshellarg($outputPath), 60);
            } elseif ($target === 'xlsx') {
                $redis->setJobStatus($jobId, ['progress' => 35]);
                convertPdfToXlsx($sourcePath, $outputPath);
            }
            break;
        case 'png':
        case 'jpg':
        case 'jpeg':
            if ($target !== 'pdf') {
                throw new RuntimeException('unsupported image conversion');
            }
            imageToPdf([$sourcePath], $outputPath, true);
            break;
        case 'txt':
        case 'csv':
            if ($target !== 'xlsx') {
                throw new RuntimeException('unsupported text conversion');
            }
            textToXlsx($sourcePath, $outputPath);
            break;
        default:
            throw new RuntimeException('unsupported conversion');
    }
}

function handleMergeImages(array $job, RedisClient $redis): void
{
    $jobId = $job['job_id'];
    $files = $job['files'] ?? [];
    $separatePages = (bool) ($job['separate_pages'] ?? true);

    if ($files === []) {
        throw new RuntimeException('no images to merge');
    }

    $outputDir = __DIR__ . '/../backend/storage/outputs/' . $jobId;
    if (!is_dir($outputDir) && !mkdir($outputDir, 0775, true) && !is_dir($outputDir)) {
        throw new RuntimeException('cannot create output dir');
    }

    $outputPath = $outputDir . '/result.pdf';
    imageToPdf($files, $outputPath, $separatePages);

    $redis->setJobStatus($jobId, ['progress' => 85]);
}

function convertWithLibreOffice(string $input, string $outputDir, string $target): void
{
    $command = sprintf(
        'soffice --headless --convert-to %s --outdir %s %s',
        escapeshellarg($target),
        escapeshellarg($outputDir),
        escapeshellarg($input)
    );
    runProcess($command, 180);

    $base = pathinfo($input, PATHINFO_FILENAME);
    $converted = $outputDir . '/' . $base . '.' . $target;
    if (is_file($converted)) {
        rename($converted, $outputDir . '/result.' . $target);
    }
}

function convertPdfToXlsx(string $input, string $outputPath): void
{
    $tempText = tempnam(sys_get_temp_dir(), 'pdf');
    runProcess('pdftotext ' . escapeshellarg($input) . ' ' . escapeshellarg($tempText), 60);
    $text = trim(file_get_contents($tempText));
    unlink($tempText);

    $source = $input;
    if ($text === '') {
        $ocrPath = sys_get_temp_dir() . '/ocr-' . uniqid() . '.pdf';
        runProcess('ocrmypdf --sidecar /dev/null ' . escapeshellarg($input) . ' ' . escapeshellarg($ocrPath), 300);
        $source = $ocrPath;
    }

    $command = sprintf(
        'java -jar /opt/tabula/tabula.jar -o %s --spreadsheet %s',
        escapeshellarg($outputPath),
        escapeshellarg($source)
    );
    runProcess($command, 180);

    if ($source !== $input && is_file($source)) {
        unlink($source);
    }
}

function imageToPdf(array $files, string $outputPath, bool $separatePages): void
{
    $mpdf = new Mpdf(['tempDir' => sys_get_temp_dir()]);

    foreach ($files as $index => $file) {
        if ($index > 0 && $separatePages) {
            $mpdf->AddPage();
        }
        $mpdf->Image($file, 10, 10, 190);
    }

    $mpdf->Output($outputPath, 'F');
}

function textToXlsx(string $input, string $outputPath): void
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $handle = fopen($input, 'r');
    if ($handle === false) {
        throw new RuntimeException('cannot open text file');
    }

    $row = 1;
    $delimiter = detectDelimiter($handle);
    rewind($handle);

    while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
        $col = 1;
        foreach ($data as $value) {
            $sheet->setCellValueByColumnAndRow($col, $row, $value);
            $col++;
        }
        $row++;
    }

    fclose($handle);

    $writer = new Xlsx($spreadsheet);
    $writer->save($outputPath);
}

function detectDelimiter($handle): string
{
    $line = fgets($handle);
    if ($line === false) {
        return ',';
    }
    $delimiters = [',', ';', "\t"];
    $best = ',';
    $maxCount = 0;
    foreach ($delimiters as $delimiter) {
        $count = substr_count($line, $delimiter);
        if ($count > $maxCount) {
            $maxCount = $count;
            $best = $delimiter;
        }
    }
    return $best;
}

function runProcess(string $command, int $timeout): void
{
    $descriptor = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];
    $process = proc_open($command, $descriptor, $pipes);
    if (!is_resource($process)) {
        throw new RuntimeException('cannot start process');
    }

    $start = time();
    foreach ($pipes as $pipe) {
        stream_set_blocking($pipe, false);
    }

    $stdout = '';
    $stderr = '';

    while (true) {
        $status = proc_get_status($process);
        if (!$status['running']) {
            break;
        }

        $read = [$pipes[1], $pipes[2]];
        $write = null;
        $except = null;
        stream_select($read, $write, $except, 1);

        foreach ($read as $r) {
            $data = fread($r, 8192);
            if ($r === $pipes[1]) {
                $stdout .= $data;
            } else {
                $stderr .= $data;
            }
        }

        if ((time() - $start) > $timeout) {
            proc_terminate($process, 9);
            throw new RuntimeException('process timeout');
        }
    }

    foreach ($pipes as $pipe) {
        fclose($pipe);
    }

    $exit = proc_close($process);
    if ($exit !== 0) {
        throw new RuntimeException(trim($stderr) ?: 'process failed');
    }
}
