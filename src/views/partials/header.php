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
    <?php if (isset($logged_user_id)) : ?>
        <header>
            <nav>
                <div class="user-info">
                    <span id="username" data-id="<?php echo $logged_user_id; ?>" data-user="<?php echo $logged_user_name; ?>">
                        user: <?php echo $logged_user_name; ?>
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