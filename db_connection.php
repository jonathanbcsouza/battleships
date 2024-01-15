<?php
session_start();

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

bootstrapDbTableAndUser($conn, $db_name, $table_name, $logged_user);

function bootstrapDbTableAndUser($conn, $db_name, $table_name, $logged_user)
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

  // Create user if not exists
  if (isset($logged_user)) {
    $stmt = $conn->prepare("SELECT id FROM $table_name WHERE username = ?");
    $stmt->bind_param("s", $logged_user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
      $stmt = $conn->prepare("INSERT INTO $table_name (username, trophies) VALUES (?, 0)");
      $stmt->bind_param("s", $logged_user);
      $result = $stmt->execute();

      if (!$result) {
        throw new Exception("Error creating user: " . $conn->error);
      }
    }

    $stmt->close();
  }
}
