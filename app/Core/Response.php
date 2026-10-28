<?php
/**
 * Multan Tech Accessories — Response Abstraction
 */

declare(strict_types=1);

namespace App\Core;

class Response
{
    private int $statusCode = 200;
    private array $headers = [];
    private string $content = '';

    public function status(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function json(mixed $data, int $status = 200): self
    {
        $this->statusCode = $status;
        $this->header('Content-Type', 'application/json; charset=UTF-8');
        $this->content = (string)json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return $this;
    }

    public function redirect(string $url, int $status = 302): self
    {
        $this->statusCode = $status;
        $this->header('Location', $url);
        return $this;
    }

    public function html(string $html, int $status = 200): self
    {
        $this->statusCode = $status;
        $this->header('Content-Type', 'text/html; charset=UTF-8');
        $this->content = $html;
        return $this;
    }

    public function body(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getBody(): string
    {
        return $this->content;
    }

    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->statusCode);
            foreach ($this->headers as $name => $value) {
                header("{$name}: {$value}");
            }
        }
        echo $this->content;
    }
}
