<?php

namespace App\Models;

class User
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    private function executeStatement($sql, $username)
    {
        $stmt = $this->conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } else {
            return false;
        }
    }

    public function getTrophies($username)
    {
        $trophies = 0;
        $sql = "SELECT trophies FROM users WHERE username = ?";

        $stmt = $this->conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $username);
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

    public function addTrophy($username)
    {
        var_dump('from Model');
        var_dump($username);
        $sql = "INSERT INTO users (username, trophies) VALUES (?, 1) ON DUPLICATE KEY UPDATE trophies = trophies + 1";
        return $this->executeStatement($sql, $username);
    }

    public function resetTrophies($username)
    {
        $sql = "UPDATE users SET trophies = 0 WHERE username = ?";
        return $this->executeStatement($sql, $username);
    }
}
