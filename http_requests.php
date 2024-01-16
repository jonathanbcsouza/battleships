<?php

include_once 'db_connection.php';

use App\Controllers\UserController;

$userController = new UserController($conn, $db_name);

$trophies = 0;

if (isset($_SESSION['user_id'])) {
    $logged_user_id = $_SESSION['user_id'];
    $logged_user_name = $_SESSION['user_name'];
}

if ($_SERVER["REQUEST_METHOD"] === 'GET' && $logged_user_id) {
    $trophies = $userController->getTrophies($logged_user_id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['username_login_screen']) && !isset($_SESSION['user_id'])) {

        $sanitizedUserName = filter_var($_POST['username_login_screen'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $userId = $userController->createNewUser($sanitizedUserName);
        $username = $userController->getUserNameById($userId);
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $sanitizedUserName;

        header('Location: src/views/login.php?username=' . urlencode($sanitizedUserName));
        exit();
    }

    if (isset($_POST['logout'])) {
        session_start();
        session_unset();
        session_destroy();
        redirectToIndex();
    }

    if (isset($_POST['action'])) {
        $userController->updateData($_POST['user_id'], $_POST['action']);
    }
}

if (!isset($_SESSION['user_id'])) {
    redirectToIndex();
}

function redirectToIndex()
{
    header('Location: ../../index.php');
    exit();
}

$conn->close();
