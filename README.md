# Battleships Challenge

This project implements a simplified version of the classic game Battleships.

## Overview

The computer randomly places two single-cell "ships" on a grid-based board. The user's objective is to locate and sink these ships within a limited number of rockets.

## Gameplay

1. The player starts with a fixed number of rockets to find and sink enemy ships (default: 2).
2. The player selects coordinates on the grid to launch rockets.
3. After each launch, the radar provides feedback. For example, if the user inputs `3,5`, and the nearest ship is at `2,7`, they receive a "warm" response because the distance is three cells.
   - "Hit! Boom! Ship destroyed!" if the rocket directly hits a ship.
   - "Hot! if the rocket lands 1-2 cells away from the nearest ship.
   - "Warm! if the rocket lands 3-4 cells away from the nearest ship.
   - "Cold! if the rocket lands 5 or more cells away from the nearest ship.
4. With each successful hit, a ship is destroyed and removed from the board.
5. The game tracks:
   - Remaining rockets
   - Ships destroyed
   - Trophies (wins accumulated over time)
6. Victory is achieved by destroying all enemy ships before running out of rockets.

### Game Conditions

- Win: All enemy ships are destroyed
- Lose: Player runs out of rockets with enemy ships still remaining

---

## Development Setup

#### Prerequisites

- PHP 8.3+ and MySQL installed on your system.
- Composer for managing PHP dependencies.
- Node.js for managing JavaScript dependencies.

#### Local Setup and Execution

1. Clone the repository to your local machine.
2. Install Composer if you haven't already. You can download it from [here](https://getcomposer.org/download/).
3. Run `composer install` to install the PHP dependencies. This will install the following dependencies:

   - `vlucas/phpdotenv` for loading environment variables from a `.env` file.
   - `phpunit/phpunit` for running tests.
   - `aws/aws-sdk-php` for integrating with AWS services.

   It will also set up autoloading for the `App` namespace.

4. Run `npm install` to install the JavaScript dependencies.
5. Set up your MySQL database and configure the connection in a `.env` file.
6. Start a local PHP server using `php -S localhost:8000`.
7. Open your browser and navigate to `http://localhost:8000` to play the game.

#### Database Schema

The database `battleship_db` consists of the following tables:

`user_configs`

| Column Name    | Data Type    | Constraints                                    |
| -------------- | ------------ | ---------------------------------------------- |
| `id`           | int unsigned | NOT NULL, AUTO_INCREMENT, PRIMARY KEY          |
| `user_id`      | int unsigned | NOT NULL, FOREIGN KEY REFERENCES `users`(`id`) |
| `config_name`  | varchar(30)  | NOT NULL                                       |
| `config_value` | varchar(30)  | NOT NULL                                       |

`users`

| Column Name | Data Type    | Constraints                           |
| ----------- | ------------ | ------------------------------------- |
| `id`        | int unsigned | NOT NULL, AUTO_INCREMENT, PRIMARY KEY |
| `username`  | varchar(30)  | NOT NULL, UNIQUE                      |
| `password`  | varchar(255) | NOT NULL                              |
| `trophies`  | int          | NOT NULL                              |

You can also preview the database schema using this interactive diagram: https://dbdiagram.io/d/65bae00cac844320ae2c8765

#### Running Tests

1. **Running all tests**

   Use the following command to run all tests:

   ```bash
   ./vendor/bin/phpunit tests
   ```

2. **Running individual test files**

   If you want to run a specific test file, you can do so by specifying the path to the file.

   ```bash
   ./vendor/bin/phpunit tests/DatabaseTest.php
   ```
