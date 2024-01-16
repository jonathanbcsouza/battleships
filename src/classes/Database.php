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

    public function createUsersTable()
    {
        $create_table = "CREATE TABLE IF NOT EXISTS users (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(30) NOT NULL UNIQUE,
            trophies INT(6) NOT NULL
        )";

        if (!$this->conn->query($create_table)) {
            throw new \Exception("Error creating table: " . $this->conn->error);
        }
    }

      public function createUsersConfigTable()
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
