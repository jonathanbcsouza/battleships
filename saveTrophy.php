<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $trophies = (int)$_POST['trophies'];

    $username_cleaned = $conn->real_escape_string($username);

    $sql = "INSERT INTO users (username, trophies) VALUES (?, ?) ON DUPLICATE KEY UPDATE trophies = trophies + VALUES(trophies)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("si", $username, $trophies);

    // Execute the prepared statement
    if ($stmt->execute()) {
        header('Location: index.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $conn->close();
} else {
    echo "No data received";
}
