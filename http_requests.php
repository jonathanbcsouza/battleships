<?php

include_once 'db_connection.php';

use App\Controllers\UserController;

$userController = new UserController($conn);
$trophies = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['username']) && !isset($_POST['action'])) {
    $_SESSION['username'] = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    header('Location: src/views/login.php?username=' . urlencode($_SESSION['username']));
    exit();
}

if (!isset($_SESSION['username'])) {
    header('Location: ../../index.php');
    exit();
}

if (isset($_POST['logout'])) {
    session_start();
    session_unset();
    session_destroy();
    var_dump(`session destroyed`);
    header('Location: ../../index.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && $logged_user) {
    // var_dump('test3');
    // die();
    $trophies = $userController->getTrophies($logged_user);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $userController->updateData($_POST['username'], $_POST['action']);
}

$conn->close();
