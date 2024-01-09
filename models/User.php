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
}
