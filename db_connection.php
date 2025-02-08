<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use PDO;
use PDOException;

// Load env variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$turso_db_url = $_ENV['SERVER'] ?? null;
$turso_auth_token = $_ENV['TURSO_AUTH_TOKEN'] ?? null;
$db_name = $_ENV['DATABASE'] ?? 'battleships';

if (!$turso_db_url) {
  die("Error: SERVER (Turso DB URL) is missing from .env file.");
}
if (!$turso_auth_token) {
  die("Error: TURSO_AUTH_TOKEN is missing from .env file.");
}

$turso_db_path = str_replace("libsql://", "sqlite:", $turso_db_url);

try {
  $conn = new PDO($turso_db_path, "", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ]);

  // ✅ Ensure tables exist
  $conn->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            trophies INTEGER DEFAULT 0
        );
        
        CREATE TABLE IF NOT EXISTS user_configs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            config_name TEXT NOT NULL,
            config_value TEXT NOT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id)
        );
    ");
} catch (PDOException $e) {
  die("Connection failed: " . $e->getMessage());
}
