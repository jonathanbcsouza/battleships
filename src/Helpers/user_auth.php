<?php
ob_start();

// Configure session
ini_set('session.cookie_path', '/');
ini_set('session.cookie_domain', '');
ini_set('session.cookie_secure', '0');
ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
ini_set('session.use_cookies', '1');
ini_set('session.use_only_cookies', '1');

session_start();

include_once __DIR__ . '/../../db_connection.php';
require_once __DIR__ . '/../Configs/Constants.php';

use App\Controllers\UserController;

$user_controller = new UserController($conn, $_ENV['DATABASE']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sanitizedUserName = sanitizeInput($_POST['username']);
    $password = $_POST['password'];

    if ($_POST['action'] === 'login') {
        try {
            $userId = $user_controller->loginUser($sanitizedUserName, $password);
            loginAndRedirect($user_controller, $userId);
        } catch (Exception $e) {
            redirectWithError($e->getMessage());
        }
    } elseif ($_POST['action'] === 'register') {
        try {
            $userId = $user_controller->createNewUser($sanitizedUserName, $password);
            loginAndRedirect($user_controller, $userId);
        } catch (Exception $e) {
            redirectWithError($e->getMessage());
        }
    }
}

function sanitizeInput(string $input): string
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function loginAndRedirect(UserController $user_controller, int $userId): void
{
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $user_controller->getUserNameById($userId);

    // Force session write
    session_write_close();

    // Redirect with absolute path
    header('Location: /src/Views/game.php');
    exit();
}

function redirectWithError(string $error): void
{
    $_SESSION['error'] = $error;

    // Force session write
    session_write_close();

    header('Location: /');
    exit();
}

ob_end_flush();
