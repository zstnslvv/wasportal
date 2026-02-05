<?php

namespace App;

use Predis\Client;

class RedisClient
{
    private Client $client;
    private int $ttl;

    public function __construct(Client $client, int $ttl = 86400)
    {
        $this->client = $client;
        $this->ttl = $ttl;
    }

    public static function fromEnv(): self
    {
        $host = getenv('REDIS_HOST') ?: 'redis';
        $port = (int) (getenv('REDIS_PORT') ?: 6379);
        $client = new Client([
            'scheme' => 'tcp',
            'host' => $host,
            'port' => $port,
        ]);
        return new self($client);
    }

    public function initJob(string $jobId): void
    {
        $this->client->hset("job:$jobId", 'status', 'queued');
        $this->client->hset("job:$jobId", 'progress', 0);
        $this->client->expire("job:$jobId", $this->ttl);
    }

    public function setJobStatus(string $jobId, array $fields): void
    {
        foreach ($fields as $key => $value) {
            $this->client->hset("job:$jobId", $key, $value);
        }
        $this->client->expire("job:$jobId", $this->ttl);
    }

    public function getJobStatus(string $jobId): ?array
    {
        $data = $this->client->hgetall("job:$jobId");
        if ($data === [] || $data === null) {
            return null;
        }
        return [
            'status' => $data['status'] ?? 'unknown',
            'progress' => isset($data['progress']) ? (int) $data['progress'] : 0,
            'download_url' => $data['download_url'] ?? null,
            'error_message' => $data['error_message'] ?? null,
        ];
    }

    public function enqueueJob(array $job): void
    {
        $this->client->rpush('jobs', json_encode($job, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public function blockingPopJob(int $timeout = 5): ?array
    {
        $result = $this->client->brpop(['jobs'], $timeout);
        if (!$result) {
            return null;
        }
        $payload = json_decode($result[1], true);
        return is_array($payload) ? $payload : null;
    }
}
