<?php
require __DIR__ . '/auth.php';

header('Content-Type: application/json; charset=utf-8');

$payload = json_decode(file_get_contents('php://input'), true);
$action = is_array($payload) ? ($payload['action'] ?? 'resolve') : 'resolve';

function respond(int $status, array $data): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function is_valid_ip(string $ip): bool
{
    return filter_var($ip, FILTER_VALIDATE_IP) !== false;
}

function fetch_ip_data(string $ip): array
{
    $fields = 'status,message,country,regionName,city,isp,org,as,reverse,query';
    $url = sprintf('http://ip-api.com/json/%s?fields=%s&lang=ru', urlencode($ip), $fields);

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_FAILONERROR => false,
    ]);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        return [
            'ip' => $ip,
            'domain' => '—',
            'country' => '—',
            'region' => '—',
            'city' => '—',
            'isp' => '—',
            'org' => '—',
            'asn' => '—',
            'status' => 'error',
            'message' => $error ?: 'Ошибка запроса',
        ];
    }

    $data = json_decode($response, true);
    if (!is_array($data)) {
        return [
            'ip' => $ip,
            'domain' => '—',
            'country' => '—',
            'region' => '—',
            'city' => '—',
            'isp' => '—',
            'org' => '—',
            'asn' => '—',
            'status' => 'error',
            'message' => 'Не удалось разобрать ответ',
        ];
    }

    if (($data['status'] ?? '') !== 'success') {
        return [
            'ip' => $ip,
            'domain' => '—',
            'country' => '—',
            'region' => '—',
            'city' => '—',
            'isp' => '—',
            'org' => '—',
            'asn' => '—',
            'status' => 'error',
            'message' => $data['message'] ?? 'Нет данных',
        ];
    }

    return [
        'ip' => $data['query'] ?? $ip,
        'domain' => $data['reverse'] ?? '—',
        'country' => $data['country'] ?? '—',
        'region' => $data['regionName'] ?? '—',
        'city' => $data['city'] ?? '—',
        'isp' => $data['isp'] ?? '—',
        'org' => $data['org'] ?? '—',
        'asn' => $data['as'] ?? '—',
        'status' => 'ok',
    ];
}

if ($action === 'resolve') {
    $ip = is_array($payload) ? trim((string) ($payload['ip'] ?? '')) : '';
    if ($ip === '' || !is_valid_ip($ip)) {
        respond(400, [
            'message' => 'Передан некорректный IP-адрес.',
        ]);
    }

    $result = fetch_ip_data($ip);
    respond(200, [
        'result' => $result,
    ]);
}

if ($action === 'store') {
    $results = is_array($payload) ? ($payload['results'] ?? []) : [];
    if (!is_array($results) || count($results) === 0) {
        respond(400, [
            'message' => 'Нет данных для сохранения.',
        ]);
    }
    if (count($results) > 100) {
        respond(400, [
            'message' => 'Превышен лимит в 100 адресов.',
        ]);
    }

    $tableId = bin2hex(random_bytes(6));
    $_SESSION['resolve_tables'] = $_SESSION['resolve_tables'] ?? [];
    $_SESSION['resolve_tables'][$tableId] = $results;

    respond(200, [
        'tableId' => $tableId,
    ]);
}

if ($action === 'delete') {
    $tableId = is_array($payload) ? trim((string) ($payload['tableId'] ?? '')) : '';
    if ($tableId === '') {
        respond(400, [
            'message' => 'Не указан идентификатор таблицы.',
        ]);
    }

    if (isset($_SESSION['resolve_tables'][$tableId])) {
        unset($_SESSION['resolve_tables'][$tableId]);
    }

    respond(200, [
        'status' => 'deleted',
    ]);
}

respond(400, [
    'message' => 'Неизвестное действие.',
]);
