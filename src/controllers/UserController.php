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

    public function createNewUserIfNotExists($username)
    {
        if (!$this->model->checkUserExists($username)) {
            return $this->createNewUser($username);
        } else {
            return $this->model->getUserIdByUsername($username);
        }
    }

    public function createNewUser($username)
    {
        $username_cleaned = $this->conn->real_escape_string($username);

        $userId =  $this->model->createNewUser($username_cleaned);
        return $userId;
    }

    public function setUserConfigs($userId)
    {
        $createdConfigs = $this->model->insertDefaultUserConfigs($userId);
        return $createdConfigs;
    }

    public function getUserNameById($userId)
    {
        return $this->model->getUserNameById($userId);
    }

    public function getUserConfig($userId)
    {
        return $this->model->getUserConfig($userId);
    }

    public function getTrophies($userId)
    {
        return $this->model->getTrophies($userId);
    }

    public function updateData($userId, $action)
    {
        $this->validateInput($userId, $action);

        if ($action === 'add') {
            $result = $this->model->addTrophy($userId);
        } else if ($action === 'reset') {
            $result = $this->model->resetTrophies($userId);
        } else {
            throw new InvalidArgumentException("Invalid action.");
        }

        if (!$result) {
            throw new RuntimeException("Error updating data: " . $this->conn->error);
        }

        return "Data updated successfully. User id: " . $userId;
    }

    private function validateInput($userId, $action)
    {
        if (empty($userId) || empty($action)) {
            throw new InvalidArgumentException("User ID and action are required.");
        }
    }
}
