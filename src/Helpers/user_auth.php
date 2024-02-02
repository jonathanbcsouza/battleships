<?php

session_start();
include_once '../../db_connection.php';
require_once '../Configs/Constants.php';

use App\Controllers\UserController;

$user_controller = new UserController($conn, $db_name);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sanitizedUserName = sanitizeInput($_POST['username']);
    $password = $_POST['password'];

    if ($_POST['action'] === 'login') {
        try {
            $userId = $user_controller->loginUser($sanitizedUserName, $password);
            loginAndRedirect($user_controller, $userId);
        } catch (Exception $e) {
            redirectWithError('../../index.php', $e->getMessage());
            exit();
        }
    } elseif ($_POST['action'] === 'register') {
        try {
            $userId = $user_controller->createNewUser($sanitizedUserName, $password);
            loginAndRedirect($user_controller, $userId);
        } catch (Exception $e) {
            redirectWithError('../../index.php', $e->getMessage());
            exit();
        }
    }
}

$conn->close();

function loginAndRedirect(UserController $user_controller, int $userId): void
{
    $username = $user_controller->getUserNameById($userId);
    $userConfigs = $user_controller->getUserConfig($userId);

    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $username;
    $_SESSION['user_configs'] = $userConfigs;

    header('Location: ../../src/Views/game.php?username=' . urlencode($username));
    exit();
}

function sanitizeInput(string $input): string
{
    return filter_var($input, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}

function redirectWithError(string $location, string $errorMessage): void
{
    header('Location: ' . $location . '?error=' . urlencode($errorMessage));
    exit();
}
