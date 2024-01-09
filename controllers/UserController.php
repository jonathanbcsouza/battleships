<?php
require_once 'models/User.php';

class UserController
{
    private $model;
    private $conn;

    public function __construct($conn)
    {
        $this->model = new User($conn);
        $this->conn = $conn;
    }

    public function getTrophies($username)
    {
        return $this->model->getTrophies($username);
    }

    public function handleRequest()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'];
            $action = $_POST['action'];
            $_SESSION['username'] = $username;

            if (empty($username) || empty($action)) {
                http_response_code(400);
                echo "Username and action are required.";
                exit();
            }

            $username_cleaned = $this->conn->real_escape_string($username);

            if ($action === 'add') {
                $result = $this->model->addTrophy($username_cleaned);
            } else if ($action === 'reset') {
                $result = $this->model->resetTrophies($username_cleaned);
            } else {
                http_response_code(400);
                echo "Invalid action.";
                exit();
            }

            if ($result) {
                http_response_code(200);
                echo "Data updated successfully. Username: " . $username;
            } else {
                http_response_code(500);
                echo "Error updating data: " . $this->conn->error;
            }
        } else {
            http_response_code(400);
            echo "No data received";
        }
    }
}
