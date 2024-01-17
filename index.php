<?php
session_start();

if (isset($_SESSION['user_name'])) {

    header('Location: src/views/login.php?username=' . $_SESSION['user_name']);

    exit(); 
}
?>

<?php include './src/views/partials/header.php'; ?>

<main class="login-form">

    <h1>Battleship Game</h1>

    <form action="./src/views/login.php" method="post">
        <input id="username" name="username_login_screen" placeholder="Enter your username" required>
        <button type="submit">Start Game</button>
    </form>

</main>

<?php include './src/views/partials/footer.php'; ?>