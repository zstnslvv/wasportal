<?php

namespace App;

class Uploads
{
    public static function uploadDir(string $fileId): string
    {
        return __DIR__ . '/../storage/uploads/' . $fileId;
    }

    public static function writeMeta(string $fileId, array $meta): void
    {
        $path = self::metaPath($fileId);
        file_put_contents($path, json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public static function readMeta(string $fileId): ?array
    {
        $path = self::metaPath($fileId);
        if (!is_file($path)) {
            return null;
        }
        $data = json_decode(file_get_contents($path), true);
        return is_array($data) ? $data : null;
    }

    public static function metaPath(string $fileId): string
    {
        return self::uploadDir($fileId) . '/meta.json';
    }

    public static function conversionMapping(): array
    {
        return [
            'xls' => ['docx', 'pdf'],
            'xlsx' => ['docx', 'pdf'],
            'doc' => ['pdf'],
            'docx' => ['pdf'],
            'ppt' => ['pdf'],
            'pptx' => ['pdf'],
            'png' => ['pdf'],
            'jpg' => ['pdf'],
            'jpeg' => ['pdf'],
            'pdf' => ['xlsx', 'txt'],
            'txt' => ['xlsx'],
            'csv' => ['xlsx'],
        ];
    }
}
