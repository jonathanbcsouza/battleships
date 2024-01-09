
<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];

    if (empty($username)) {
        http_response_code(400);
        echo "Username is required.";
        exit();
    }

    $username_cleaned = $conn->real_escape_string($username);

    $sql = "UPDATE users SET trophies = 0 WHERE username = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $username);

    // Execute the prepared statement
    if ($stmt->execute()) {
        http_response_code(200);
        echo "Data reset successfully";
    } else {
        http_response_code(500);
        echo "Error resetting data: " . $stmt->error;
    }

    $conn->close();
} else {
    http_response_code(400);
    echo "No data received";
}
