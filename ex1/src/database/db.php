<?php

class Database
{
    private $connection;
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $db = "donations";
    private $port = "3306";
    private static $instance = null;

    public function __construct()
    {
        if (!extension_loaded('mysqli')) {
            throw new Exception('mysqli extension is not loaded');
        }

        $this->connection = new \mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->db,
            $this->port
        );

        if ($this->connection->connect_error) {
            http_response_code(500);
            throw new Exception("Connection failed: " . $this->connection->connect_error);
        }
    }

    public static function getConnection(): \mysqli
    {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance->connection;
    }
}
