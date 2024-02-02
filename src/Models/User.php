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
            $user_id = $row['id'];
        } else {
            $user_id = null;
        }

        return $user_id;
    }

    public function createNewUser(string $username, string $password): int
    {
        $username = html_entity_decode($username);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt = $this->conn->prepare("INSERT INTO users (username, password, trophies) VALUES (?, ?, 0)");
            $stmt->bind_param("ss", $username, $hashed_password);
            $result = $stmt->execute();

            if (!$result) {
                throw new \Exception("Error creating user: " . $this->conn->error);
            }

            $user_id = $this->conn->insert_id;
        } else {
            $row = $result->fetch_assoc();
            $user_id = $row['id'];
        }

        $stmt->close();

        return $user_id;
    }

    public function getHashedPasswordByUserId(int $user_id): string
    {
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new \Exception("User not found.");
        }

        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        $stmt->close();

        return $hashed_password;
    }

    public function getUserNameById(int $user_id): string
    {
        $sql = "SELECT username FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['username'];
    }

    public function getUserConfig(int $user_id): array
    {
        $sql = "SELECT * FROM user_configs WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $configs = [];
        while ($row = $result->fetch_assoc()) {
            $configs[] = $row;
        }

        $stmt->close();

        return $configs;
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

        foreach ($default_configs as $config_name => $config_value) {
            $stmt = $this->conn->prepare("INSERT INTO user_configs (user_id, config_name, config_value) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user_id, $config_name, $config_value);
            $stmt->execute();
        }
    }

    public function getTrophies(int $user_id): int
    {
        $sql = "SELECT trophies FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $trophies = 0;
        if ($row = $result->fetch_assoc()) {
            $trophies = $row['trophies'];
        }

        $stmt->close();

        return $trophies;
    }

    public function addTrophy(int $user_id): bool
    {
        $sql = "UPDATE users SET trophies = trophies + 1 WHERE id = ?";
        return $this->executeStatement($sql, $user_id);
    }

    public function resetTrophies(int $user_id): bool
    {
        $sql = "UPDATE users SET trophies = 0 WHERE id = ?";
        return $this->executeStatement($sql, $user_id);
    }

    private function executeStatement(string $sql, int $user_id): bool
    {
        $stmt = $this->conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $user_id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } else {
            return false;
        }
    }
}
