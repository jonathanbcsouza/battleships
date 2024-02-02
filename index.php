<?php
session_start();

if (isset($_SESSION['user_name'])) {
    $username = (string)$_SESSION['user_name'];

    header('Location: src/Views/game.php?username=' . $username);
    exit();
}

if (isset($_GET['error'])) : ?>
    <script>
        alert("<?php echo $_GET['error']; ?>");
    </script>
<?php endif; ?>

<?php include './src/Views/partials/header.php'; ?>

<main class="login-form">

    <h1>Battleship Game</h1>

    <button id="toggleFormButton">Sign Up</button>

    <form id="loginForm" action="./src/Helpers/user_auth.php" method="post">
        <input type="hidden" name="action" value="login">
        <input type="text" name="username" placeholder="Enter your username" autofocus required>
        <input type="password" name="password" placeholder="Enter your password" required>
        <button type="submit">Login</button>
    </form>

    <form id="registerForm" action="./src/Helpers/user_auth.php" method="post" style="display: none;">
        <input type="hidden" name="action" value="register">
        <input type="text" name="username" placeholder="Create your username" autofocus required>
        <input type="password" name="password" placeholder="Create your password" required>
        <button type="submit">Register</button>
    </form>

</main>

<script type="module" src="./src/Helpers/handle_signup_btn.js"></script>

<?php include './src/Views/partials/footer.php'; ?>