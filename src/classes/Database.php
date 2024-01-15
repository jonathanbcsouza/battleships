<?php

namespace App\Classes;

class Database
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function createDatabase($db_name)
    {
        $sql = "CREATE DATABASE IF NOT EXISTS $db_name";
        if (!$this->conn->query($sql)) {
            throw new \Exception("Error creating database: " . $this->conn->error);
        }
        $this->conn->select_db($db_name);
    }

    public function createTable($table_name)
    {
        $create_table = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(30) NOT NULL UNIQUE,
            trophies INT(6) NOT NULL
        )";

        if (!$this->conn->query($create_table)) {
            throw new \Exception("Error creating table: " . $this->conn->error);
        }
    }
}
