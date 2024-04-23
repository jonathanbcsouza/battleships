# Battleships Challenge

This project implements a simplified version of the classic game Battleships.

## Overview

The computer randomly places two single-cell "ships" on a grid-based board. The user's objective is to locate and sink these ships within a limited number of rockets.

## Gameplay

1. The user has a fixed number of guesses (X) to find and sink the ships (Y).
2. The user inputs/selects coordinates in the format `row,column`, such as `3,5`.
3. Based on the user's input/selection, the computer provides feedback:
   - "Hot" if the guessed coordinate is 1 to 2 cells away from the nearest ship.
   - "Warm" if the guessed coordinate is 3 to 4 cells away.
   - "Cold" if the guessed coordinate is farther away.
4. For example, if the user inputs `3,5`, and the nearest ship is at `2,7`, they receive a "warm" response because the distance is three cells.
5. If the user correctly guesses a ship's location, they receive a "hit" message, and that ship is removed from the board.
6. The game continues until the user sinks both ships or exhausts their available rockets.

---

#### Prerequisites

- PHP and MySQL installed on your system.
- Composer for managing PHP dependencies.

#### Setup and Execution

1. Clone the repository to your local machine.
2. Install Composer if you haven't already. You can download it from [here](https://getcomposer.org/download/). After downloading, you can install it globally on your system by following the instructions [here](https://getcomposer.org/doc/00-intro.md#globally).
3. Run `composer install` to install the PHP dependencies. This will install the following dependencies:

   - `vlucas/phpdotenv` for loading environment variables from a `.env` file.
   - `phpunit/phpunit` for running tests.
   - `aws/aws-sdk-php` for integrating with AWS services.

   It will also set up autoloading for the `App` namespace.

4. Run `npm install` to install the javascript dependencies.
5. Start a local PHP server using `php -S localhost:8000`.
6. Open your browser and navigate to `http://localhost:8000` to play the game.

#### Alternative Setup: AWS Parameter Store

It is an alternative to using a `.env` file for managing database credentials. Some of the benefits:

- **Security**: Allows encrypted storage of sensitive data.

- **Centralized Management**: Enable centralized secret management, simplifying application development.

- **Auditability**: Integration with AWS CloudTrail and supports audit, improving compliance.

To use this option, you need to create the parameters at AWS, then replace the content of `db_connection.php` with the content from `aws_connection.php`. This will allow the app to retrieve credentials from AWS SSM / Parameter Store instead of from the `.env` file.

#### Verifying Installed Libraries

You can verify the installed PHP libraries using Composer:

```bash
composer show
```

This will list all the installed PHP packages along with their versions.

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

   This command will find and execute all tests located in the `tests` directory.

2. **Running individual test files**

   If you want to run a specific test file, you can do so by specifying the path to the file.

   ```bash
   ./vendor/bin/phpunit tests/DatabaseTest.php
   ```

#### AWS services that can be used:

- **Amazon Route 53**: Used for domain name management and DNS routing.
- **AWS SSM Parameter Store**: Used for secure, centralized management of application configuration data and secrets.
- **Elastic Load Balancing (ELB)**: Using for managing the traffic between ports 443/8080. Also distributes incoming application traffic across multiple EC2 instances.
- **AWS Certificate Manager**: Handles the creation, storage, and renewal of the SSL certificate.
- **Amazon EC2 (Elastic Compute Cloud)**: Used to host the application servers.