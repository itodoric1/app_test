<?php

declare(strict_types=1);

namespace App\Core;

class Response
{
    private string $base = '/finasport/public/index.php?route=';

    public function redirect(string $url): never
    {
        if ($url === '') {
            $url = '/';
        }

        if ($url[0] !== '/') {
            $url = '/' . $url;
        }

        header('Location: ' . $this->base . $url);
        exit;
    }

    public function json(array $payload, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}