<?php
class User
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getTrophies($username)
    {
        $trophies = 0;
        if ($stmt = $this->conn->prepare("SELECT trophies FROM users WHERE username = ?")) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $trophies = $row['trophies'];
            }
            $stmt->close();
        }

        return $trophies;
    }

    public function addTrophy($username) {
        $sql = "INSERT INTO users (username, trophies) VALUES (?, 1) ON DUPLICATE KEY UPDATE trophies = trophies + 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        return $stmt->execute();
    }

    public function resetTrophies($username) {
        $sql = "UPDATE users SET trophies = 0 WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        return $stmt->execute();
    }
}
