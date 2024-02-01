<?php

namespace App\Models;

use mysqli;

class User
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function doesUserExist(string $username): bool
    {
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->num_rows > 0;
    }

    public function getUserIdByUsername(string $username): ?int
    {
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $userId = $row['id'];
        } else {
            $userId = null;
        }

        return $userId;
    }

    public function createNewUser(string $username, string $password): int
    {
        $username = html_entity_decode($username);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt = $this->conn->prepare("INSERT INTO users (username, password, trophies) VALUES (?, ?, 0)");
            $stmt->bind_param("ss", $username, $hashedPassword);
            $result = $stmt->execute();

            if (!$result) {
                throw new \Exception("Error creating user: " . $this->conn->error);
            }

            $userId = $this->conn->insert_id;
        } else {
            $row = $result->fetch_assoc();
            $userId = $row['id'];
        }

        $stmt->close();

        return $userId;
    }

    public function getHashedPasswordByUserId(int $userId): string
    {
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new \Exception("User not found.");
        }

        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];

        $stmt->close();

        return $hashedPassword;
    }

    public function getUserNameById(int $userId): string
    {
        $sql = "SELECT username FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['username'];
    }

    public function getUserConfig(int $userId): array
    {
        $sql = "SELECT * FROM user_configs WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $configs = [];
        while ($row = $result->fetch_assoc()) {
            $configs[] = $row;
        }

        $stmt->close();

        return $configs;
    }

    public function insertDefaultUserConfigs(int $userId): void
    {
        $defaultConfigs = [
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

        foreach ($defaultConfigs as $configName => $configValue) {
            $stmt = $this->conn->prepare("INSERT INTO user_configs (user_id, config_name, config_value) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $userId, $configName, $configValue);
            $stmt->execute();
        }
    }

    public function getTrophies(int $userId): int
    {
        $sql = "SELECT trophies FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $trophies = 0;
        if ($row = $result->fetch_assoc()) {
            $trophies = $row['trophies'];
        }

        $stmt->close();

        return $trophies;
    }

    public function addTrophy(int $userId): bool
    {
        $sql = "UPDATE users SET trophies = trophies + 1 WHERE id = ?";
        return $this->executeStatement($sql, $userId);
    }

    public function resetTrophies(int $userId): bool
    {
        $sql = "UPDATE users SET trophies = 0 WHERE id = ?";
        return $this->executeStatement($sql, $userId);
    }

    private function executeStatement(string $sql, int $userId): bool
    {
        $stmt = $this->conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $userId);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } else {
            return false;
        }
    }
}
