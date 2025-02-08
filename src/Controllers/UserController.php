<?php

namespace App\Controllers;

use App\Models\User;
use InvalidArgumentException;
use RuntimeException;
use PDO;
use Exception;

class UserController
{
    private User $model;
    private PDO $conn;

    public function __construct(PDO $conn, string $db_name)
    {
        $this->model = new User($conn);
        $this->conn = $conn;
    }

    public function createNewUser(string $username, string $password): int
    {
        $user_name_to_lower_case = strtolower($username);
        $user_exists = $this->doesUserExist($user_name_to_lower_case);

        if ($user_exists) {
            throw new Exception('User already exists');
        }

        $username_cleaned = htmlspecialchars($user_name_to_lower_case, ENT_QUOTES, 'UTF-8');

        return $this->model->createNewUser($username_cleaned, $password);
    }

    public function loginUser(string $username, string $password): int
    {
        $user_name_to_lower_case = strtolower($username);
        $user_exists = $this->doesUserExist($user_name_to_lower_case);

        if (!$user_exists) {
            throw new Exception('User does not exist');
        }

        $user_id = $this->getUserIdByUsername($user_name_to_lower_case);
        $is_password_correct = $this->verifyPassword($user_id, $password);

        if (!$is_password_correct) {
            throw new Exception('Incorrect password');
        }

        return $user_id;
    }

    public function doesUserExist(string $username): bool
    {
        return $this->model->doesUserExist($username);
    }

    public function getUserIdByUsername(string $username): int
    {
        return $this->model->getUserIdByUsername($username);
    }

    public function verifyPassword(int $user_id, string $password): bool
    {
        $hashed_password = $this->model->getHashedPasswordByUserId($user_id);
        return password_verify($password, $hashed_password);
    }

    public function setUserConfigs(int $user_id): void
    {
        $this->model->insertDefaultUserConfigs($user_id);
    }

    public function getUserNameById(int $user_id): string
    {
        return $this->model->getUserNameById($user_id);
    }

    public function getUserConfig(int $user_id): array
    {
        $user_configs = $this->model->getUserConfig($user_id);

        if (empty($user_configs)) {
            $this->setUserConfigs($user_id);
            $user_configs = $this->model->getUserConfig($user_id);
        }
        return $user_configs;
    }

    public function getTrophies(int $user_id): int
    {
        return $this->model->getTrophies($user_id);
    }

    public function updateData(int $user_id, string $action): string
    {
        $this->validateUserIdAndAction($user_id, $action);

        if ($action === 'add') {
            $result = $this->model->addTrophy($user_id);
        } else if ($action === 'reset') {
            $result = $this->model->resetTrophies($user_id);
        } else {
            throw new InvalidArgumentException("Invalid action.");
        }

        if (!$result) {
            throw new RuntimeException("Error updating data: " . $this->conn->errorInfo()[2]);
        }

        return "Data updated successfully. User id: " . $user_id;
    }

    private function validateUserIdAndAction(int $user_id, string $action): void
    {
        if (empty($user_id) || empty($action)) {
            throw new InvalidArgumentException("User ID and action are required.");
        }
    }
}
