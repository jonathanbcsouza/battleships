<?php include './src/views/partials/header.php'; ?>

<main>
    <form action="http_requests.php" method="post">
        <input id="username" name="username" placeholder="Enter your username" required>
        <button type="submit">Start Game</button>
    </form>
</main>

<?php include './src/views/partials/footer.php'; ?>