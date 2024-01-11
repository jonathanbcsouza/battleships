<?php include 'http_requests.php';

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}
?>

<?php include './partials/header.php'; ?>

<main>
    <input id="username" name="username" placeholder="Enter your username" value="<?php echo $logged_user; ?>">
    <p id="msgElement"></p>
    <section class="game-stats">
        <div class="stat-item">
            <span id="rocketsIcon"></span>
            <span id="rockets"></span>
        </div>
        <div class="stat-item">
            <span id="shipsDestroyedIcon"></span>
            <span id="shipsDestroyed"></span>
        </div>
        <div class="stat-item">
            <span id="trophiesIcon"></span>
            <span id="trophies"><?php echo $trophies; ?></span>
        </div>
    </section>

    <section class="game-controls">
        <div class="btn-container">
            <button id="startButton" aria-label="Start game">Start Game</button>
            <button id="restart" aria-label="Restart game">Restart</button>
            <?php if ($trophies > 0) : ?>
                <button id="resetScoreButton">Reset Score</button>
            <?php endif; ?>
        </div>
    </section>

    <section class="game-board">
        <p id="board"></p>
    </section>

</main>

<script type="module" src="index.js"></script>

<?php include './partials/footer.php'; ?>