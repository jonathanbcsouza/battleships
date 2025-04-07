<?php

namespace App\Models;

use Exception;

// Remove namespace when requiring the constants file
require_once __DIR__ . '/../Configs/Constants.php';

class User
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    private function extractValue($result)
    {
        error_log("Extracting value from result: " . json_encode($result));

        // Handle simple array format: [[{"value":"10"}]]
        if (
            is_array($result) &&
            !empty($result[0]) &&
            is_array($result[0]) &&
            !empty($result[0][0]) &&
            isset($result[0][0]['value'])
        ) {
            $value = $result[0][0]['value'];
            error_log("Extracted value from simple array: " . $value);
            return $value;
        }

        // Handle full Turso response format
        if (!empty($result[0]['response']['result']['rows'][0][0]['value'])) {
            $value = $result[0]['response']['result']['rows'][0][0]['value'];
            error_log("Extracted value from Turso response: " . $value);
            return $value;
        }

        error_log("No value found in result");
        return null;
    }

    private function extractRows($result)
    {
        error_log("Extracting rows from result: " . json_encode($result));

        // Handle simple array format: [[{"value":"key"},{"value":"value"}],...]
        if (is_array($result) && !empty($result[0]) && isset($result[0][0]['value'])) {
            error_log("Using simple array format");
            return $result;
        }

        // Handle full Turso response format
        if (!empty($result[0]['response']['result']['rows'])) {
            $rows = $result[0]['response']['result']['rows'];
            error_log("Extracted rows from Turso response: " . json_encode($rows));
            return $rows;
        }

        error_log("No rows found in result");
        return [];
    }

    public function doesUserExist(string $username): bool
    {
        $sql = "SELECT id FROM users WHERE username = :username";
        $result = $this->conn->query($sql, [':username' => $username]);
        return !empty($this->extractValue($result));
    }

    public function getUserIdByUsername(string $username): ?int
    {
        error_log("Getting user ID for username: " . $username);
        $sql = "SELECT id FROM users WHERE username = :username";
        $result = $this->conn->query($sql, [':username' => $username]);
        error_log("Raw query result: " . json_encode($result));

        // Handle the actual response format: [[{"value":"10"}]]
        if (
            is_array($result) &&
            !empty($result[0]) &&
            is_array($result[0]) &&
            !empty($result[0][0]) &&
            isset($result[0][0]['value'])
        ) {
            $value = $result[0][0]['value'];
            error_log("Found user ID: " . $value);
            return (int) $value;
        }

        error_log("No user ID found for username: " . $username);
        return null;
    }

    public function createNewUser(string $username, string $password): int
    {
        try {
            error_log("Starting createNewUser in User model for username: " . $username);
            $username = html_entity_decode($username);
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            if ($this->doesUserExist($username)) {
                throw new Exception("User already exists.");
            }

            $sql = "INSERT INTO users (username, password, trophies) VALUES (:username, :password, 0)";
            $result = $this->conn->query($sql, [
                ':username' => $username,
                ':password' => $hashed_password
            ]);

            error_log("Full Turso response: " . json_encode($result));

            // Get the last insert ID from the response
            $lastInsertId = $result[0]['response']['result']['last_insert_rowid'];
            error_log("Last insert ID from Turso: " . $lastInsertId);

            if (!$lastInsertId) {
                throw new Exception("Failed to get user ID after creation.");
            }

            // After creating the user, immediately set up their configs
            $userId = (int)$lastInsertId;
            error_log("Starting to insert default configs for user ID: " . $userId);

            $default_configs = [
                'HIT_DIST' => '0',
                'HOT_DIST' => '1',
                'WARM_DIST' => '3',
                'COLD_DIST' => '5',
                'EMPTY_ICON' => '🪼',
                'SHIP_ICON' => '🚢',
                'ROCKET_ICON' => '🚀',
                'EXPLOSION_ICON' => '💥',
                'TROPHIE_ICON' => '🏆',
                'GRID_SIZE' => '8',
                'NUM_ROCKETS' => '20',
                'NUM_SHIPS' => '2'
            ];

            foreach ($default_configs as $config_name => $config_value) {
                try {
                    error_log("Inserting config {$config_name} = {$config_value}");
                    $sql = "INSERT OR REPLACE INTO user_configs (user_id, config_name, config_value) VALUES (:user_id, :config_name, :config_value)";
                    $this->conn->query($sql, [
                        ':user_id' => $userId,
                        ':config_name' => $config_name,
                        ':config_value' => $config_value
                    ]);
                    error_log("Config {$config_name} inserted successfully");
                } catch (Exception $e) {
                    error_log("Failed to insert config {$config_name}: " . $e->getMessage());
                    throw $e;
                }
            }
            error_log("All configs inserted successfully");

            return $userId;
        } catch (Exception $e) {
            error_log("Error in User model createNewUser: " . $e->getMessage());
            throw $e;
        }
    }

    public function getHashedPasswordByUserId(int $user_id): string
    {
        error_log("Getting hashed password for user ID: " . $user_id);
        $sql = "SELECT password FROM users WHERE id = :user_id";
        $result = $this->conn->query($sql, [':user_id' => $user_id]);
        $value = $this->extractValue($result);
        error_log("Retrieved hashed password: " . ($value ? 'yes' : 'no'));

        if ($value === null) {
            error_log("User not found when getting password");
            throw new Exception("User not found.");
        }

        return $value;
    }

    public function verifyPassword(int $user_id, string $password): bool
    {
        try {
            error_log("Verifying password for user ID: " . $user_id);
            $hashed_password = $this->getHashedPasswordByUserId($user_id);
            $result = password_verify($password, $hashed_password);
            error_log("Password verification result: " . ($result ? 'success' : 'failed'));
            return $result;
        } catch (Exception $e) {
            error_log("Error verifying password: " . $e->getMessage());
            return false;
        }
    }

    public function getUserNameById(int $user_id): string
    {
        $sql = "SELECT username FROM users WHERE id = :user_id";
        $result = $this->conn->query($sql, [':user_id' => $user_id]);
        $value = $this->extractValue($result);
        return $value !== null ? $value : "";
    }

    public function getUserConfig(int $user_id): array
    {
        $sql = "SELECT config_name, config_value FROM user_configs WHERE user_id = :user_id";
        $result = $this->conn->query($sql, [':user_id' => $user_id]);
        $rows = $this->extractRows($result);

        if (empty($rows)) {
            $this->insertDefaultUserConfigs($user_id);
            $result = $this->conn->query($sql, [':user_id' => $user_id]);
            $rows = $this->extractRows($result);
        }

        $configs = [];
        foreach ($rows as $row) {
            if (isset($row[0]['value']) && isset($row[1]['value'])) {
                $configs[$row[0]['value']] = $row[1]['value'];
            }
        }
        error_log("Processed configs: " . json_encode($configs));
        return $configs;
    }

    public function insertDefaultUserConfigs(int $user_id): void
    {
        error_log("Starting insertDefaultUserConfigs for user_id: " . $user_id);

        // Check if constants are defined
        error_log("Checking constants...");
        error_log("HIT_DIST defined: " . (defined('HIT_DIST') ? 'yes' : 'no'));
        error_log("HOT_DIST defined: " . (defined('HOT_DIST') ? 'yes' : 'no'));

        $default_configs = [
            'HIT_DIST' => defined('HIT_DIST') ? HIT_DIST : 0,
            'HOT_DIST' => defined('HOT_DIST') ? HOT_DIST : 1,
            'WARM_DIST' => defined('WARM_DIST') ? WARM_DIST : 3,
            'COLD_DIST' => defined('COLD_DIST') ? COLD_DIST : 5,
            'EMPTY_ICON' => defined('EMPTY_ICON') ? EMPTY_ICON : '🪼',
            'SHIP_ICON' => defined('SHIP_ICON') ? SHIP_ICON : '🚢',
            'ROCKET_ICON' => defined('ROCKET_ICON') ? ROCKET_ICON : '🚀',
            'EXPLOSION_ICON' => defined('EXPLOSION_ICON') ? EXPLOSION_ICON : '💥',
            'TROPHIE_ICON' => defined('TROPHIE_ICON') ? TROPHIE_ICON : '🏆',
            'GRID_SIZE' => defined('GRID_SIZE') ? GRID_SIZE : 8,
            'NUM_ROCKETS' => defined('NUM_ROCKETS') ? NUM_ROCKETS : 20,
            'NUM_SHIPS' => defined('NUM_SHIPS') ? NUM_SHIPS : 2
        ];

        error_log("Default configs prepared: " . json_encode($default_configs));

        foreach ($default_configs as $config_name => $config_value) {
            try {
                error_log("Inserting config {$config_name} = {$config_value}");
                $sql = "INSERT OR REPLACE INTO user_configs (user_id, config_name, config_value) VALUES (:user_id, :config_name, :config_value)";
                $this->conn->query($sql, [
                    ':user_id' => $user_id,
                    ':config_name' => $config_name,
                    ':config_value' => $config_value
                ]);
                error_log("Config {$config_name} inserted successfully");
            } catch (Exception $e) {
                error_log("Failed to insert config {$config_name}: " . $e->getMessage());
                throw $e;
            }
        }
        error_log("All configs inserted successfully");
    }

    public function getTrophies(int $user_id): int
    {
        $sql = "SELECT trophies FROM users WHERE id = :user_id";
        $result = $this->conn->query($sql, [':user_id' => $user_id]);
        $value = $this->extractValue($result);
        return $value !== null ? (int) $value : 0;
    }

    public function updateData(int $user_id, string $action): string
    {
        switch ($action) {
            case 'win':
                $sql = "UPDATE users SET trophies = trophies + 1 WHERE id = :user_id";
                break;
            case 'lose':
            case 'draw':
                $sql = "UPDATE users SET trophies = trophies WHERE id = :user_id";
                break;
            default:
                throw new Exception("Invalid action");
        }

        $this->conn->query($sql, [':user_id' => $user_id]);
        return "Data updated successfully";
    }
}
