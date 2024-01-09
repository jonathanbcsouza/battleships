<?php include 'db_connection.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Battleship Game</title>
    <link rel="stylesheet" type="text/css" href="./styles/styles.css">
    <link rel="icon" href="./assets/favicon.png" type="image/x-icon">
</head>

<body>
    <header>
        <h1>Battleship Game</h1>
    </header>

    <main>
        <form id="form" method="post" action="saveTrophy.php">
            <input id="username" name="username" placeholder="Enter your username" value="<?php echo $logged_user; ?>">
            <p id="welcome_msg"></p>
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
                    <input id="trophy" type="hidden" name="trophies">
                </div>
            </section>
        </form>

        <section class="game-controls">
            <button id="startButton" aria-label="Start game">Play</button>
            <button id="restart" aria-label="Restart game">Restart</button>
            <button id="resetScoreButton">Reset Score</button>
        </section>

        <section class="game-board">
            <p id="board"></p>
        </section>

    </main>

    <script type="module" src="index.js"></script>
</body>

</html>