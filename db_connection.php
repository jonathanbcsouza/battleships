<?php
session_start();
require_once 'controllers/UserController.php';

// Load env variables
require 'vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$server_name = $_ENV['SERVER'];
$db_username = $_ENV['USERNAME'];
$password = $_ENV['PASSWORD'];
$db_name = "battleship_db";
$table_name = "users";

$logged_user = isset($_SESSION['username']) ? $_SESSION['username'] : $_ENV['LOGGED_USER_TESTING'];

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

// Start session
$trophies = 0;
$userController = new UserController($conn);

if ($_SERVER["REQUEST_METHOD"] == "GET" && $logged_user) {
    $trophies = $userController->getTrophies($logged_user);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userController->updateData($_POST['username'], $_POST['action']);
}

$conn->close();
