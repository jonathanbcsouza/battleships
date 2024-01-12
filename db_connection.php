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

if (isset($_SESSION['username'])) {
  $logged_user = $_SESSION['username'];
}

// Create connection
$conn = new mysqli($server_name, $db_username, $password);

// Check connection
if ($conn->connect_error) {
  throw new Exception("Connection failed: " . $conn->connect_error);
}

// Setup database and table
setupDatabaseAndTable($conn, $db_name, $table_name);

function setupDatabaseAndTable($conn, $db_name, $table_name)
{
  // Create database if it doesn't exist
  $sql = "CREATE DATABASE IF NOT EXISTS $db_name";
  if (!$conn->query($sql)) {
    throw new Exception("Error creating database: " . $conn->error);
  }
  $conn->select_db($db_name);

  // Create table if it doesn't exist
  $create_table = "CREATE TABLE IF NOT EXISTS $table_name (
      id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      username VARCHAR(30) NOT NULL UNIQUE,
      trophies INT(6) NOT NULL
    )";

  if (!$conn->query($create_table)) {
    throw new Exception("Error creating table: " . $conn->error);
  }
}
