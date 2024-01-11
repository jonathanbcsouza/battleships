<?php

include_once 'db_connection.php';

$trophies = 0;
$userController = new UserController($conn);

if ($_SERVER["REQUEST_METHOD"] == "GET" && $logged_user) {
    $trophies = $userController->getTrophies($logged_user);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userController->updateData($_POST['username'], $_POST['action']);
}

$conn->close();
