<?php

include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $action = $_POST['action'];
    $_SESSION['username'] = $username;

    // Validate input
    if (empty($username) || empty($action)) {
        http_response_code(400);
        echo "Username and action are required.";
        exit();
    }

    $username_cleaned = $conn->real_escape_string($username);

    if ($action === 'add') {
        $sql = "INSERT INTO users (username, trophies) VALUES (?, 1) ON DUPLICATE KEY UPDATE trophies = trophies + 1";
    } else if ($action === 'reset') {
        $sql = "UPDATE users SET trophies = 0 WHERE username = ?";
    } else {
        http_response_code(400);
        echo "Invalid action.";
        exit();
    }

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $username);

    // Execute the prepared statement
    if ($stmt->execute()) {
        http_response_code(200);
        echo "Data updated successfully. Username: " . $username;
    } else {
        http_response_code(500);
        echo "Error updating data: " . $stmt->error;
    }

    $conn->close();
} else {
    http_response_code(400);
    echo "No data received";
}
