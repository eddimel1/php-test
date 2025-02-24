<?php

namespace App\core;

class Request {

    
    public function getMethod(): string {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

   
    public function getUri(): string {
        return strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
    }

  
    public function all(): array {
        return $this->sanitize(array_merge($_GET, $_POST));
    }

    
    public function input(string $key, $default = null) {
        $value = $_GET[$key] ?? $_POST[$key] ?? $default;
        return is_array($value) ? $this->sanitize($value) : $this->sanitizeValue($value);
    }

   
    public function headers(): array {
        return getallheaders() ?: [];
    }

    
    public function header(string $key, $default = null) {
        $headers = $this->headers();
        return $headers[$key] ?? $default;
    }

  
    public function json(): array {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true) ?? [];
        return $this->sanitize($data);
    }

   
    public function isAjax(): bool {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    
    private function sanitize(array $data): array {
        return array_map(fn($value) => is_array($value) ? $this->sanitize($value) : $this->sanitizeValue($value), $data);
    }

   
    private function sanitizeValue($value) {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }
}
