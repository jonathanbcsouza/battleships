<?php
session_start();

require 'vendor/autoload.php';

use App\Classes\Database;
use App\Controllers\UserController;
use Dotenv\Dotenv;

// Load env variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$server_name = $_ENV['SERVER'];
$db_username = $_ENV['USERNAME'];
$password = $_ENV['PASSWORD'];
$db_name = "battleship_db";
$table_name = "users";

if (isset($_SESSION['username'])) {
  $logged_user = $_SESSION['username'];
}

// Create connection
$conn = new mysqli($server_name, $db_username, $password);

if ($conn->connect_error) {
  throw new Exception("Connection failed: " . $conn->connect_error);
}

// Bootstrapping a new DB adn table
$database = new Database($conn);
$database->createDatabase($db_name);
$database->createTable($table_name);

if (isset($logged_user)) {
  $userController = new UserController($conn, $db_name);
  $userController->createNewUser($logged_user);
}
