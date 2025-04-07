<?php

namespace App\Controllers;

use App\Models\User;
use InvalidArgumentException;
use RuntimeException;
use Exception;

class UserController
{
    private User $model;
    private $conn;

    public function __construct($conn, string $db_name)
    {
        $this->model = new User($conn);
        $this->conn = $conn;
    }

    public function createNewUser(string $username, string $password): int
    {
        try {
            error_log("Starting user creation for username: " . $username);

            // Create the user and their configs
            $userId = $this->model->createNewUser($username, $password);
            error_log("User created with ID: " . $userId);

            return $userId;
        } catch (Exception $e) {
            error_log("Error in UserController createNewUser: " . $e->getMessage());
            throw new RuntimeException("Failed to create user: " . $e->getMessage());
        }
    }

    public function loginUser(string $username, string $password): int
    {
        try {
            error_log("Starting login for username: " . $username);

            $userId = $this->model->getUserIdByUsername($username);
            error_log("Found user ID: " . ($userId ?? 'null'));

            if (!$userId) {
                error_log("User does not exist");
                throw new InvalidArgumentException("User does not exist");
            }

            error_log("Verifying password for user ID: " . $userId);
            if (!$this->model->verifyPassword($userId, $password)) {
                error_log("Invalid password for user ID: " . $userId);
                throw new InvalidArgumentException("Invalid password");
            }
            error_log("Password verified successfully for user ID: " . $userId);

            return $userId;
        } catch (Exception $e) {
            error_log("Login failed: " . $e->getMessage());
            throw new RuntimeException("Login failed: " . $e->getMessage());
        }
    }

    public function doesUserExist(string $username): bool
    {
        try {
            return $this->model->doesUserExist($username);
        } catch (Exception $e) {
            throw new RuntimeException("Failed to check user existence: " . $e->getMessage());
        }
    }

    public function getUserIdByUsername(string $username): int
    {
        try {
            return $this->model->getUserIdByUsername($username);
        } catch (Exception $e) {
            throw new RuntimeException("Failed to get user ID: " . $e->getMessage());
        }
    }

    public function verifyPassword(int $user_id, string $password): bool
    {
        try {
            return $this->model->verifyPassword($user_id, $password);
        } catch (Exception $e) {
            throw new RuntimeException("Failed to verify password: " . $e->getMessage());
        }
    }

    public function setUserConfigs(int $user_id): void
    {
        try {
            $this->model->insertDefaultUserConfigs($user_id);
        } catch (Exception $e) {
            throw new RuntimeException("Failed to set user configs: " . $e->getMessage());
        }
    }

    public function getUserNameById(int $user_id): string
    {
        try {
            return $this->model->getUserNameById($user_id);
        } catch (Exception $e) {
            throw new RuntimeException("Failed to get username: " . $e->getMessage());
        }
    }

    public function getUserConfig(int $user_id): array
    {
        try {
            return $this->model->getUserConfig($user_id);
        } catch (Exception $e) {
            throw new RuntimeException("Failed to get user config: " . $e->getMessage());
        }
    }

    public function getTrophies(int $user_id): int
    {
        try {
            return $this->model->getTrophies($user_id);
        } catch (Exception $e) {
            throw new RuntimeException("Failed to get trophies: " . $e->getMessage());
        }
    }

    public function updateData(int $user_id, string $action): string
    {
        try {
            $this->validateUserIdAndAction($user_id, $action);
            return $this->model->updateData($user_id, $action);
        } catch (Exception $e) {
            throw new RuntimeException("Failed to update data: " . $e->getMessage());
        }
    }

    private function validateUserIdAndAction(int $user_id, string $action): void
    {
        if ($user_id <= 0) {
            throw new InvalidArgumentException("Invalid user ID");
        }

        if (!in_array($action, ['win', 'lose', 'draw'])) {
            throw new InvalidArgumentException("Invalid action");
        }
    }
}
