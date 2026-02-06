<?php

$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(
        ['error' => 'composer autoload missing', 'hint' => 'run composer install in backend'],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
}

require $autoloadPath;

use App\RedisClient;
use App\Response;
use App\Uploads;
use Ramsey\Uuid\Uuid;

$redis = RedisClient::fromEnv();

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if (!str_starts_with($uri, '/api')) {
    Response::json(['error' => 'Not Found'], 404);
}

$path = substr($uri, 4) ?: '/';

if ($method === 'POST' && $path === '/upload') {
    if (!isset($_FILES['file'])) {
        Response::json(['error' => 'file is required'], 422);
    }
    $file = $_FILES['file'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        Response::json(['error' => 'upload failed'], 400);
    }

    $uploadId = Uuid::uuid4()->toString();
    $originalName = basename($file['name']);
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    $uploadDir = Uploads::uploadDir($uploadId);
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
        Response::json(['error' => 'cannot create upload dir'], 500);
    }

    $destination = $uploadDir . '/' . $originalName;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        Response::json(['error' => 'cannot move upload'], 500);
    }

    $meta = [
        'file_id' => $uploadId,
        'original_name' => $originalName,
        'ext' => $ext,
        'size' => filesize($destination),
        'path' => $destination,
    ];
    Uploads::writeMeta($uploadId, $meta);

    Response::json([
        'file_id' => $uploadId,
        'original_name' => $originalName,
        'ext' => $ext,
        'size' => $meta['size'],
    ]);
}

if ($method === 'POST' && $path === '/convert') {
    $payload = json_decode(file_get_contents('php://input'), true);
    if (!is_array($payload)) {
        Response::json(['error' => 'invalid json'], 400);
    }

    $fileId = $payload['file_id'] ?? '';
    $target = strtolower($payload['target_format'] ?? '');
    if ($fileId === '' || $target === '') {
        Response::json(['error' => 'file_id and target_format required'], 422);
    }

    $meta = Uploads::readMeta($fileId);
    if ($meta === null) {
        Response::json(['error' => 'file not found'], 404);
    }

    $allowed = Uploads::conversionMapping();
    $sourceExt = strtolower($meta['ext'] ?? '');
    if (!isset($allowed[$sourceExt]) || !in_array($target, $allowed[$sourceExt], true)) {
        Response::json(['error' => 'conversion not allowed'], 422);
    }

    $jobId = Uuid::uuid4()->toString();
    $job = [
        'type' => 'convert',
        'job_id' => $jobId,
        'file_id' => $fileId,
        'source_ext' => $sourceExt,
        'target_format' => $target,
        'options' => $payload['options'] ?? [],
    ];

    $redis->initJob($jobId);
    $redis->enqueueJob($job);

    Response::json(['job_id' => $jobId]);
}

if ($method === 'POST' && $path === '/merge-images-to-pdf') {
    if (!isset($_FILES['files'])) {
        Response::json(['error' => 'files[] is required'], 422);
    }

    $separatePages = filter_var($_POST['separate_pages'] ?? 'true', FILTER_VALIDATE_BOOLEAN);
    $files = $_FILES['files'];

    $jobId = Uuid::uuid4()->toString();
    $uploadDir = Uploads::uploadDir($jobId);
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
        Response::json(['error' => 'cannot create upload dir'], 500);
    }

    $stored = [];
    foreach ($files['name'] as $index => $name) {
        if ($files['error'][$index] !== UPLOAD_ERR_OK) {
            continue;
        }
        $safeName = basename($name);
        $destination = $uploadDir . '/' . $safeName;
        if (move_uploaded_file($files['tmp_name'][$index], $destination)) {
            $stored[] = $destination;
        }
    }

    if ($stored === []) {
        Response::json(['error' => 'no files uploaded'], 422);
    }

    $job = [
        'type' => 'merge_images',
        'job_id' => $jobId,
        'files' => $stored,
        'separate_pages' => $separatePages,
    ];

    $redis->initJob($jobId);
    $redis->enqueueJob($job);

    Response::json(['job_id' => $jobId]);
}

if ($method === 'GET' && preg_match('#^/job/([a-f0-9\-]+)$#', $path, $matches)) {
    $jobId = $matches[1];
    $status = $redis->getJobStatus($jobId);
    if ($status === null) {
        Response::json(['error' => 'job not found'], 404);
    }
    Response::json($status);
}

if ($method === 'GET' && preg_match('#^/download/([a-f0-9\-]+)$#', $path, $matches)) {
    $jobId = $matches[1];
    $outputDir = __DIR__ . '/../storage/outputs/' . $jobId;
    if (!is_dir($outputDir)) {
        Response::json(['error' => 'result not found'], 404);
    }
    $files = glob($outputDir . '/result.*');
    if ($files === false || $files === []) {
        Response::json(['error' => 'result not found'], 404);
    }
    $filePath = $files[0];
    $filename = basename($filePath);

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
}

Response::json(['error' => 'Not Found'], 404);
