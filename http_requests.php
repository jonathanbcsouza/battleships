<?php

include_once 'db_connection.php';
require_once 'src/configs/constants.php';
session_start();

use App\Controllers\UserController;

$userController = new UserController($conn, $db_name);

if (isset($_SESSION['user_id'])) {
    $logged_user_id = (int)$_SESSION['user_id'];
    $logged_user_name = (string)$_SESSION['user_name'];
    $logged_user_configs = $_SESSION['user_configs'];
    $trophies = $userController->getTrophies($logged_user_id);

    echo "<script>window.phpSessions = " . json_encode($logged_user_configs) . ";</script>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['username_login_screen']) && !isset($_SESSION['user_id'])) {
        
        $sanitizedUserName = filter_var($_POST['username_login_screen'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $userId = $userController->createNewUserIfNotExists($sanitizedUserName);
        $username = $userController->getUserNameById($userId);
        $userConfigs = $userController->getUserConfig($userId);

        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $username;
        $_SESSION['user_configs'] = $userConfigs;

        header('Location: ../../src/views/login.php?username=' . urlencode($username));
        exit();
    }

    if (isset($_POST['logout'])) {
        session_start();
        session_unset();
        session_destroy();
        redirectToIndex();
    }

    if (isset($_POST['action'])) {
        $userController->updateData((int)$_POST['user_id'], (string)$_POST['action']);
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
