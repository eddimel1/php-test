<?php

namespace App\core;

use PDO;
use PDOException;

class Database {

    private string $port;
    private string $user;
    private string $password;
    private string $dbName;
    private string $dbHost;
    public PDO $pdo;

    public function __construct()
    {
        $this->prepareConfig();
        $this->dbConnect();
    }

    protected function prepareConfig()
    {
        $this->port = getenv('DB_OUT_PORT');
        $this->user = getenv('POSTGRES_USER');
        $this->password = getenv('POSTGRES_PASSWORD');
        $this->dbName = getenv('POSTGRES_DB');
        $this->dbHost = getenv('DB_CONTAINER_NAME');
    }

    protected function dbConnect()
    {
        $dsn = "pgsql:host={$this->dbHost};dbname={$this->dbName}";

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC 
            ]);
        } catch (PDOException $e) {
            die("❌ Database connection failed: " . $e->getMessage());
        }
    }
}
