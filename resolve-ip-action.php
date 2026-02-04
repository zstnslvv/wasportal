<?php
require __DIR__ . '/auth.php';

header('Content-Type: application/json; charset=utf-8');

$payload = json_decode(file_get_contents('php://input'), true);
$rawIps = $payload['ips'] ?? ($_POST['ips'] ?? '');

function extract_ips($input): array
{
    $ips = [];
    if (is_array($input)) {
        foreach ($input as $item) {
            if (is_string($item)) {
                $ips = array_merge($ips, extract_ips($item));
            }
        }
        return array_values(array_unique($ips));
    }

    if (!is_string($input)) {
        return [];
    }

    $pattern = '/\b(?:\d{1,3}\.){3}\d{1,3}\b|\b(?:[A-Fa-f0-9]{1,4}:){1,7}[A-Fa-f0-9]{0,4}\b/';
    if (preg_match_all($pattern, $input, $matches)) {
        foreach ($matches[0] as $match) {
            $match = trim($match);
            if (filter_var($match, FILTER_VALIDATE_IP)) {
                $ips[] = $match;
            }
        }
    }

    return array_values(array_unique($ips));
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
            'status' => 'error',
            'message' => $error ?: 'Ошибка запроса',
        ];
    }

    $data = json_decode($response, true);
    if (!is_array($data)) {
        return [
            'ip' => $ip,
            'status' => 'error',
            'message' => 'Не удалось разобрать ответ',
        ];
    }

    if (($data['status'] ?? '') !== 'success') {
        return [
            'ip' => $ip,
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

$ips = extract_ips($rawIps);
if (empty($ips)) {
    http_response_code(400);
    echo json_encode([
        'message' => 'Не найдено валидных IP-адресов.',
        'results' => [],
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$results = [];
foreach ($ips as $ip) {
    $results[] = fetch_ip_data($ip);
}

echo json_encode([
    'results' => $results,
], JSON_UNESCAPED_UNICODE);
