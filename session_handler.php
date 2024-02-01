<?php

session_start();
include_once 'db_connection.php';

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
    if (isset($_POST['logout'])) {
        session_start();
        session_unset();
        session_destroy();
        redirectToIndex();
    }

    if (isset($_POST['action'])) {
        $userController->updateData((int)$logged_user_id, (string)$_POST['action']);
    }
}

if (!isset($_SESSION['user_id'])) {
    redirectToIndex();
}

function redirectToIndex(): void
{
    header('Location: ../../index.php');
    exit();
}

$conn->close();
