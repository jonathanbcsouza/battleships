<?php

namespace App\Classes;

class Database
{
    private \mysqli $conn;

    public function __construct(\mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function createDatabase(string $db_name): void
    {
        $sql = "CREATE DATABASE IF NOT EXISTS $db_name";
        if (!$this->conn->query($sql)) {
            throw new \Exception("Error creating database: " . $this->conn->error);
        }
        $this->conn->select_db($db_name);
    }

    public function createUsersTable(): void
    {
        $create_table = "CREATE TABLE IF NOT EXISTS users (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(30) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            trophies INT(6) NOT NULL
        )";

        if (!$this->conn->query($create_table)) {
            throw new \Exception("Error creating table: " . $this->conn->error);
        }
    }

    public function createUsersConfigTable(): void
    {
        $create_table = "CREATE TABLE IF NOT EXISTS user_configs (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT(6) UNSIGNED NOT NULL,
            config_name VARCHAR(30) NOT NULL,
            config_value VARCHAR(30) NOT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )";

        if (!$this->conn->query($create_table)) {
            throw new \Exception("Error creating table: " . $this->conn->error);
        }
    }
}
