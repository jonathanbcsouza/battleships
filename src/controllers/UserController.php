<?php

namespace App\Controllers;

use App\Models\User;
use InvalidArgumentException;
use RuntimeException;

class UserController
{
    private $model;
    private $conn;

    public function __construct($conn, $db_name)
    {
        $this->model = new User($conn);
        $this->conn = $conn;
        $this->conn->select_db($db_name);
    }

    public function createNewUser($username)
    {
        $username_cleaned = $this->conn->real_escape_string($username);
        $this->model->createNewUser($username_cleaned);
    }

    public function getTrophies($username)
    {
        return $this->model->getTrophies($username);
    }

    public function updateData($username, $action)
    {
        $_SESSION['username'] = $username;

        $this->validateInput($username, $action);

        $username_cleaned = $this->conn->real_escape_string($username);

        if ($action === 'add') {
            $result = $this->model->addTrophy($username_cleaned);
        } else if ($action === 'reset') {
            $result = $this->model->resetTrophies($username_cleaned);
        } else {
            throw new InvalidArgumentException("Invalid action.");
        }

        if (!$result) {
            throw new RuntimeException("Error updating data: " . $this->conn->error);
        }

        return "Data updated successfully. Username: " . $username;
    }

    private function validateInput($username, $action)
    {
        if (empty($username) || empty($action)) {
            throw new InvalidArgumentException("Username and action are required.");
        }
    }
}
