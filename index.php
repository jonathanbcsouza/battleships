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

<?php include './partials/header.php'; ?>

<main>
    <form action="index.php" method="post">
        <input id="username" name="username" placeholder="Enter your username" required>
        <button type="submit">Start Game</button>
    </form>
</main>

<?php include './partials/footer.php'; ?>