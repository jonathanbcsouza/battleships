<?php

session_start();
include_once 'db_connection.php';
require_once 'src/configs/constants.php';

use App\Controllers\UserController;

$userController = new UserController($conn, $db_name);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sanitizedUserName = sanitizeInput($_POST['username']);
    $password = $_POST['password'];

    if ($_POST['action'] === 'login') {
        try {
            $userId = $userController->loginUser($sanitizedUserName, $password);
            loginAndRedirect($userController, $userId);
        } catch (Exception $e) {
            redirectWithError('../../index.php', $e->getMessage());
            exit();
        }
    } elseif ($_POST['action'] === 'register') {
        try {
            $userId = $userController->createNewUser($sanitizedUserName, $password);
            loginAndRedirect($userController, $userId);
        } catch (Exception $e) {
            redirectWithError('../../index.php', $e->getMessage());
            exit();
        }
    }
}

$conn->close();

function loginAndRedirect($userController, $userId)
{
    $username = $userController->getUserNameById($userId);
    $userConfigs = $userController->getUserConfig($userId);

    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $username;
    $_SESSION['user_configs'] = $userConfigs;

    header('Location: ../../src/views/game.php?username=' . urlencode($username));
    exit();
}

function sanitizeInput($input)
{
    return filter_var($input, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}

function redirectWithError($location, $errorMessage)
{
    header('Location: ' . $location . '?error=' . urlencode($errorMessage));
    exit();
}
