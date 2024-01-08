<?php
$env = array_reduce(
    explode("\n", file_get_contents('.env.local')),
    function ($carry, $item) {
        list($key, $value) = explode('=', $item, 2);
        $carry[$key] = $value;
        return $carry;
    },
    []
);

$server_name = $env['SERVER_NAME'];
$username = $env['USERNAME'];
$password = $env['PASSWORD'];

$db_name = "battleship_db";
$table_name = "users";

// Create connection
$conn = new mysqli($server_name, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected successfully to the database.<br><br>Host info:<br>{$conn->host_info}<br><br>";
}
// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $db_name";

if ($conn->query($sql) !== TRUE) {
    echo "Database " . $db_name . " already exists";
}

// Select the database
$conn->select_db($db_name);

// Create table
$sql = "CREATE TABLE IF NOT EXISTS $table_name (
  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(30) NOT NULL,
  trophies INT(6) NOT NULL
)";

if ($conn->query($sql) !== TRUE) {
    echo "Table " . $table_name . " already exists";
}

$conn->close();
