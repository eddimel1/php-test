<?php

namespace App\core;

class Response {


    public function setStatusCode(int $code) {
        http_response_code($code);
    }

   
    public function setHeader(string $name, string $value) {
        header("{$name}: {$value}");
    }

   
    public function json(array $data, int $statusCode = 200) {
        $this->setStatusCode($statusCode);
        $this->setHeader('Content-Type', 'application/json');
        return  json_encode($data);
    }


    public function text(string $content, int $statusCode = 200) {
        $this->setStatusCode($statusCode);
        $this->setHeader('Content-Type', 'text/plain');
        return $content;
        
    }


    public function redirect(string $url, int $statusCode = 302) {
        $this->setStatusCode($statusCode);
        $this->setHeader('Location', $url);
        exit;
    }
}
