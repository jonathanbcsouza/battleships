<?php

namespace App\Controllers;

use App\Models\User;
use InvalidArgumentException;
use RuntimeException;
use mysqli;
use Exception;

class UserController
{
    private User $model;
    private mysqli $conn;

    public function __construct(mysqli $conn, string $db_name)
    {
        $this->model = new User($conn);
        $this->conn = $conn;
        $this->conn->select_db($db_name);
    }

    public function createNewUser(string $username, string $password): int
    {

        $userNameToLowerCase = strtolower($username);
        $userExists = $this->doesUserExist($userNameToLowerCase);

        if ($userExists) {
            throw new Exception('User already exists');
        }

        $usernameCleaned = $this->conn->real_escape_string($userNameToLowerCase);

        $userId =  $this->model->createNewUser($usernameCleaned, $password);
        return $userId;
    }

    public function loginUser(string $username, string $password): int
    {
        $userNameToLowerCase = strtolower($username);
        $userExists = $this->doesUserExist($userNameToLowerCase);

        if (!$userExists) {
            throw new Exception('User does not exist');
        }

        $userId = $this->getUserIdByUsername($userNameToLowerCase);
        $isPasswordCorrect = $this->verifyPassword($userId, $password);

        if (!$isPasswordCorrect) {
            throw new Exception('Incorrect password');
        }

        return $userId;
    }

    public function doesUserExist(string $username): bool
    {
        return $this->model->doesUserExist($username);
    }

    public function getUserIdByUsername(string $username): int
    {
        return $this->model->getUserIdByUsername($username);
    }

    public function verifyPassword(int $userId, string $password): bool
    {
        $hashedPassword = $this->model->getHashedPasswordByUserId($userId);
        $password = $password;

        $isPasswordCorrect = password_verify($password, $hashedPassword);

        return $isPasswordCorrect;
    }

    public function setUserConfigs(int $userId): void
    {
        $this->model->insertDefaultUserConfigs($userId);
    }

    public function getUserNameById(int $userId): string
    {
        return $this->model->getUserNameById($userId);
    }

    public function getUserConfig(int $userId): array
    {
        $user_configs = $this->model->getUserConfig($userId);

        if (empty($user_configs)) {
            $this->setUserConfigs($userId);
            $user_configs = $this->model->getUserConfig($userId);
        }
        return $user_configs;
    }

    public function getTrophies(int $userId): int
    {
        return $this->model->getTrophies($userId);
    }

    public function updateData(int $userId, string $action): string
    {
        $this->validateUserIdAndAction($userId, $action);

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

    private function validateUserIdAndAction(int $userId, string $action): void
    {
        if (empty($userId) || empty($action)) {
            throw new InvalidArgumentException("User ID and action are required.");
        }
    }
}
