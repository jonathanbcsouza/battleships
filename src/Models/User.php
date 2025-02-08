<?php

namespace App\Models;

use PDO;
use Exception;

class User
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function doesUserExist(string $username): bool
    {
        $sql = "SELECT id FROM users WHERE username = :username";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch() !== false;
    }

    public function getUserIdByUsername(string $username): ?int
    {
        $sql = "SELECT id FROM users WHERE username = :username";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? (int) $row['id'] : null;
    }

    public function createNewUser(string $username, string $password): int
    {
        $username = html_entity_decode($username);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        if ($this->doesUserExist($username)) {
            throw new Exception("User already exists.");
        }

        $sql = "INSERT INTO users (username, password, trophies) VALUES (:username, :password, 0)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->bindParam(":password", $hashed_password, PDO::PARAM_STR);
        $stmt->execute();

        return (int) $this->conn->lastInsertId();
    }

    public function getHashedPasswordByUserId(int $user_id): string
    {
        $sql = "SELECT password FROM users WHERE id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            throw new Exception("User not found.");
        }

        return $row['password'];
    }

    public function getUserNameById(int $user_id): string
    {
        $sql = "SELECT username FROM users WHERE id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['username'] ?? "";
    }

    public function getUserConfig(int $user_id): array
    {
        $sql = "SELECT * FROM user_configs WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertDefaultUserConfigs(int $user_id): void
    {
        $default_configs = [
            'HIT_DIST' => HIT_DIST,
            'HOT_DIST' => HOT_DIST,
            'WARM_DIST' => WARM_DIST,
            'COLD_DIST' => COLD_DIST,
            'EMPTY_ICON' => EMPTY_ICON,
            'SHIP_ICON' => SHIP_ICON,
            'ROCKET_ICON' => ROCKET_ICON,
            'EXPLOSION_ICON' => EXPLOSION_ICON,
            'TROPHIE_ICON' => TROPHIE_ICON,
            'GRID_SIZE' => GRID_SIZE,
            'NUM_ROCKETS' => NUM_ROCKETS,
            'NUM_SHIPS' => NUM_SHIPS
        ];

        $sql = "INSERT INTO user_configs (user_id, config_name, config_value) VALUES (:user_id, :config_name, :config_value)";
        $stmt = $this->conn->prepare($sql);

        foreach ($default_configs as $config_name => $config_value) {
            $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
            $stmt->bindParam(":config_name", $config_name, PDO::PARAM_STR);
            $stmt->bindParam(":config_value", $config_value, PDO::PARAM_STR);
            $stmt->execute();
        }
    }

    public function getTrophies(int $user_id): int
    {
        $sql = "SELECT trophies FROM users WHERE id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int) $row['trophies'] : 0;
    }

    public function addTrophy(int $user_id): bool
    {
        return $this->executeStatement("UPDATE users SET trophies = trophies + 1 WHERE id = :user_id", $user_id);
    }

    public function resetTrophies(int $user_id): bool
    {
        return $this->executeStatement("UPDATE users SET trophies = 0 WHERE id = :user_id", $user_id);
    }

    private function executeStatement(string $sql, int $user_id): bool
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
