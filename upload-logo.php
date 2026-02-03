<?php
require __DIR__ . '/auth.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'method_not_allowed']);
    exit;
}

if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'upload_failed']);
    exit;
}

$file = $_FILES['logo'];
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);
$allowed = [
    'image/png' => 'png',
    'image/jpeg' => 'jpg',
];

if (!isset($allowed[$mime])) {
    http_response_code(415);
    echo json_encode(['ok' => false, 'error' => 'invalid_type']);
    exit;
}

$uploadsDir = __DIR__ . '/uploads';
if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0755, true);
}

$filename = sprintf('logo-%s.%s', date('YmdHis'), $allowed[$mime]);
$destination = $uploadsDir . '/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $destination)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'save_failed']);
    exit;
}

echo json_encode(['ok' => true, 'url' => '/uploads/' . $filename]);
