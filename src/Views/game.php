<?php include './../Helpers/session_handler.php'; ?>
<?php include './partials/header.php'; ?>

<section id="alertModal" class="modal-content">
    <div class="alert-body">
        <p id="alertMessage"></p>
        <div id="inputGroup" class="input-group">
            <button id="alertSubmit">Ok</button>
        </div>
    </div>
</section>

<main class="game-start-page">

    <h1>Battleship Game</h1>

    <section class="game-msgs">
        <div id="msgContainer" class="msgContainer"></div>
    </section>

    <section class="game-stats">
        <div class="stat-item">
            <span id="rocketsIcon"><?php echo $logged_user_configs['ROCKET_ICON']; ?></span>
            <span id="rockets"><?php echo $logged_user_configs['NUM_ROCKETS']; ?></span>
        </div>
        <div class="stat-item">
            <span id="shipsDestroyedIcon"><?php echo $logged_user_configs['EXPLOSION_ICON']; ?></span>
            <span id="shipsDestroyed">0</span>
        </div>
        <div class="stat-item">
            <span id="trophiesIcon"><?php echo $logged_user_configs['TROPHIE_ICON']; ?></span>
            <span id="trophies"><?php echo $trophies; ?></span>
        </div>
    </section>

    <section class="game-board">
        <p id="board"></p>
    </section>

    <section class="game-controls">
        <div class="btn-container">
            <button id="startButton" aria-label="Start game" autofocus>Start Mission</button> <button id="restart" aria-label="Restart game">Restart</button>
            <?php if ($trophies > 0) : ?>
                <button id="resetScoreButton">Reset Score</button>
            <?php endif; ?>
        </div>
    </section>

</main>

<script>
    // Make user configs available to JavaScript as an array of objects
    window.PHP_SESSIONS = <?php
                            $configArray = [];
                            foreach ($logged_user_configs as $name => $value) {
                                $configArray[] = [
                                    'config_name' => $name,
                                    'config_value' => $value
                                ];
                            }
                            echo json_encode($configArray);
                            ?>;
</script>
<script type="module" src="../index.js"></script>

<?php include './partials/footer.php'; ?>