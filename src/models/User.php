<?php

namespace App\Models;

class User
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function createNewUser($username)
    {
        $username = html_entity_decode($username);
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt = $this->conn->prepare("INSERT INTO users (username, trophies) VALUES (?, 0)");
            $stmt->bind_param("s", $username);
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

    public function getUserNameById($userId)
    {
        $stmt = $this->conn->prepare("SELECT username FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['username'];
        }

        return null;
    }

    private function executeStatement($sql, $userId)
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

    public function getTrophies($userId)
    {
        $trophies = 0;
        $sql = "SELECT trophies FROM users WHERE id = ?";

        $stmt = $this->conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $userId);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $trophies = $row['trophies'];
                }
            }
            $stmt->close();
        } else {
            return false;
        }

        return $trophies;
    }

    public function addTrophy($userId)
    {
        $sql = "UPDATE users SET trophies = trophies + 1 WHERE id = ?";
        return $this->executeStatement($sql, $userId);
    }

    public function resetTrophies($userId)
    {
        $sql = "UPDATE users SET trophies = 0 WHERE id = ?";
        return $this->executeStatement($sql, $userId);
    }
}
