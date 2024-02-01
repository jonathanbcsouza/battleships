<?php
require 'vendor/autoload.php';

use App\Classes\Database;
use Dotenv\Dotenv;

// Load env variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$server_name = (string)$_ENV['SERVER'];
$db_username = (string)$_ENV['USERNAME'];
$password = (string)$_ENV['PASSWORD'];
$db_name = (string)$_ENV['DATABASE'];
$table_name = "users";

// Create connection
$conn = new mysqli($server_name, $db_username, $password);

if ($conn->connect_error) {
  throw new Exception("Connection failed: " . $conn->connect_error);
}

// Bootstrapping a new DB and table
$database = new Database($conn);
$database->createDatabase($db_name);
$database->createUsersTable();
$database->createUsersConfigTable();