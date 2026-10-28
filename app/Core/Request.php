<?php
/**
 * Multan Tech Accessories — Request Abstraction
 */

declare(strict_types=1);

namespace App\Core;

class Request
{
    private string $method;
    private string $path;
    private array $get;
    private array $post;
    private array $files;
    private array $server;
    private ?array $json = null;

    public function __construct()
    {
        $this->server = $_SERVER;
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;

        // HTTP Method spoofing (_method field in POST)
        $method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
        if ($method === 'POST' && isset($this->post['_method'])) {
            $spoofed = strtoupper((string)$this->post['_method']);
            if (in_array($spoofed, ['PUT', 'PATCH', 'DELETE'], true)) {
                $method = $spoofed;
            }
        }
        $this->method = $method;

        // Clean Path
        $uri = $this->server['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $this->path = '/' . trim($path, '/');
        if ($this->path === '') {
            $this->path = '/';
        }
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    public function isGet(): bool
    {
        return $this->method === 'GET';
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function query(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->get;
        }
        return $this->get[$key] ?? $default;
    }

    public function input(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return array_merge($this->get, $this->post);
        }
        return $this->post[$key] ?? $this->get[$key] ?? $default;
    }

    public function post(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->post;
        }
        return $this->post[$key] ?? $default;
    }

    public function json(?string $key = null, mixed $default = null): mixed
    {
        if ($this->json === null) {
            $raw = file_get_contents('php://input');
            $this->json = (array)(json_decode($raw, true) ?? []);
        }
        if ($key === null) {
            return $this->json;
        }
        return $this->json[$key] ?? $default;
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function ip(): string
    {
        if (!empty($this->server['HTTP_CF_CONNECTING_IP'])) {
            return $this->server['HTTP_CF_CONNECTING_IP'];
        }
        if (!empty($this->server['HTTP_X_FORWARDED_FOR'])) {
            $parts = explode(',', $this->server['HTTP_X_FORWARDED_FOR']);
            return trim($parts[0]);
        }
        return $this->server['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function userAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }

    public function isAjax(): bool
    {
        return (isset($this->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (isset($this->server['HTTP_ACCEPT']) && str_contains($this->server['HTTP_ACCEPT'], 'application/json'));
    }
}
