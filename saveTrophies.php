<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];

    $username_cleaned = $conn->real_escape_string($username);

    $sql = "INSERT INTO users (username, trophies) VALUES (?, 1) ON DUPLICATE KEY UPDATE trophies = trophies + 1";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $username);

    // Execute the prepared statement
    if ($stmt->execute()) {
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $conn->close();
} else {
    echo "No data received";
}
