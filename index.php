<?php
session_start();

if (isset($_SESSION['username'])) {
    header('Location: login.php?username=' . urlencode($_SESSION['username']));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['username'])) {
    $_SESSION['username'] = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    header('Location: login.php?username=' . urlencode($_SESSION['username']));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Battleship Game</title>
    <link rel="stylesheet" type="text/css" href="./styles/styles.css">
    <link rel="icon" href="./assets/favicon.ico" type="image/x-icon">
</head>

<body>
    <header>
        <h1>Battleship Game</h1>
    </header>

    <main>
        <form action="index.php" method="post">
            <input id="username" name="username" placeholder="Enter your username" required>
            <button type="submit">Start Game</button>
        </form>
    </main>
</body>

</html>