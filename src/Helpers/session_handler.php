<?php

session_start();
include_once __DIR__ . '/../../db_connection.php';

use App\Controllers\UserController;

$user_controller = new UserController($conn, $_ENV['DATABASE']);

if (!isset($_SESSION['user_id'])) {
    session_unset();
    session_destroy();
    header('Location: /');
    exit();
}

$logged_user_id = (int)$_SESSION['user_id'];
$logged_user_name = (string)$_SESSION['user_name'];
$trophies = $user_controller->getTrophies($logged_user_id);

// Get user configs if not already in session
if (!isset($_SESSION['user_configs'])) {
    $configs = $user_controller->getUserConfig($logged_user_id);
    error_log("Retrieved configs from database: " . json_encode($configs));
    $_SESSION['user_configs'] = $configs;
}
$logged_user_configs = $_SESSION['user_configs'];

// Ensure all required configs are present
$default_configs = [
    'HIT_DIST' => '0',
    'HOT_DIST' => '1',
    'WARM_DIST' => '3',
    'COLD_DIST' => '5',
    'EMPTY_ICON' => '🪼',
    'SHIP_ICON' => '🚢',
    'ROCKET_ICON' => '🚀',
    'EXPLOSION_ICON' => '💥',
    'TROPHIE_ICON' => '🏆',
    'GRID_SIZE' => '8',
    'NUM_ROCKETS' => '20',
    'NUM_SHIPS' => '2'
];

foreach ($default_configs as $key => $value) {
    if (!isset($logged_user_configs[$key])) {
        $logged_user_configs[$key] = $value;
    }
}

echo "<script>window.PHP_SESSIONS = " . json_encode($logged_user_configs) . ";</script>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        header('Location: /');
        exit();
    }

    if (isset($_POST['action'])) {
        $user_controller->updateData($logged_user_id, (string)$_POST['action']);
    }
}

$conn = null;
