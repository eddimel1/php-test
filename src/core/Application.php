<?php

namespace App\core;

use App\core\Router;
use App\core\Request;
use App\core\Response;
use App\core\Database;
use Exception;

class Application {

    public static Application $app;
    public static string $ROOT_DIR;
    public Router $router;
    public Request $request;
    public Response $response;
    public Database $db;
    public Session $session;
    public View $view;
    
    public function __construct()
    {
        $this->prepare();
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        $this->db = new Database();
        $this->session = new Session();
        $this->view = new View($this->session);
    }

    protected function prepare() {
        define('ROOT_DIR', dirname(__DIR__)); 
        $this->loadEnv(ROOT_DIR . '/.env');
    }

    protected function loadEnv($filePath) {
        if (!file_exists($filePath)) {
            throw new Exception(".env file not found.");
        }
    
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '#') === 0) continue;
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value)); 
        }
    }
    
}