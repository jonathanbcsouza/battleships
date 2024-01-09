<?php
session_start();
require_once 'controllers/UserController.php';

// Load env variables
$env = array_reduce(
    explode("\n", file_get_contents('.env.local')),
    function ($carry, $item) {
        list($key, $value) = explode('=', $item, 2);
        $carry[$key] = $value;
        return $carry;
    },
    []
);

// DB Creddentials
$server_name = $env['SERVER_NAME'];
$db_username = $env['USERNAME'];
$password = $env['PASSWORD'];
$db_name = "battleship_db";
$table_name = "users";

$logged_user = $_SESSION['username'] ?? null;

// Create connection
$conn = new mysqli($server_name, $db_username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $db_name";
$conn->query($sql);
$conn->select_db($db_name);

// Create table if it doesn't exist
$create_table = "CREATE TABLE IF NOT EXISTS $table_name (
  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(30) NOT NULL UNIQUE,
  trophies INT(6) NOT NULL
)";
$conn->query($create_table);

$controller = new UserController($conn);
$trophies = $controller->getTrophies($logged_user);

// require_once 'views/user.php';
// require_once 'views/index.php';
