<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Battleship Game</title>
    <link rel="stylesheet" type="text/css" href="/styles/styles.css">
    <link rel="icon" href="/assets/favicon.ico" type="image/x-icon">
</head>

<body>
    <?php if (isset($logged_user)) : ?>
        <header>
            <nav>
                <div class="user-info">
                    <span id="username" data-user="<?php echo htmlspecialchars($logged_user); ?>">
                        user: <?php echo htmlspecialchars($logged_user); ?>
                    </span>
                    <form action="login.php" method="post">
                        <button type="submit" name="logout">
                            Log out
                        </button>
                    </form>
                </div>
            </nav>
        </header>
    <?php endif; ?>