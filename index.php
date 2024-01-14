<?php include './src/views/partials/header.php'; ?>

<main class="login-form">

    <h1>Battleship Game</h1>

    <form action="http_requests.php" method="post">
        <input id="username" name="username" placeholder="Enter your username" required>
        <button type="submit">Start Game</button>
    </form>

</main>

<?php include './src/views/partials/footer.php'; ?>