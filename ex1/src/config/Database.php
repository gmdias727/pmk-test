<?php
namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private $host;
    private $dbName;
    private $username;
    private $password;
    private $conn;

    public function __construct()
    {
        $this->host     = getenv('DB_HOST') ?: 'localhost';
        $this->dbName   = getenv('DB_NAME') ?: 'donors_db';
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASS') ?: '';
    }

    public function getConnection(): PDO
    {
        $this->conn = null;

        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset=utf8";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);

        } catch (PDOException $e) {
            echo "Database connection error: " . $e->getMessage();
            exit;
        }

        return $this->conn;
    }
}
