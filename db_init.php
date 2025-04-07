<?php
require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/src/Configs/Constants.php';

class DatabaseInitializer
{
    private TursoConnection $conn;

    public function __construct(TursoConnection $conn)
    {
        $this->conn = $conn;
    }

    public function initialize(): void
    {
        $this->createUsersTable();
        $this->createUserConfigsTable();
        $this->createGameStatsTable();
    }

    private function createUsersTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            trophies INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";

        $this->conn->query($sql);
        echo "Users table checked/created successfully.\n";
    }

    private function createUserConfigsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS user_configs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            config_name TEXT NOT NULL,
            config_value TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE(user_id, config_name)
        )";

        $this->conn->query($sql);
        echo "User configs table checked/created successfully.\n";
    }

    private function createGameStatsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS game_stats (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            game_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            ships_destroyed INTEGER DEFAULT 0,
            rockets_used INTEGER DEFAULT 0,
            trophies_earned INTEGER DEFAULT 0,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )";

        $this->conn->query($sql);
        echo "Game stats table checked/created successfully.\n";
    }
}

// Initialize the database
try {
    $initializer = new DatabaseInitializer($conn);
    $initializer->initialize();
    echo "Database initialization completed successfully.\n";
} catch (Exception $e) {
    echo "Error initializing database: " . $e->getMessage() . "\n";
    exit(1);
}
