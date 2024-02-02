<?php

use PHPUnit\Framework\TestCase;
use App\Classes\Database;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

class DatabaseTest extends TestCase
{
    private Database $db;
    private mysqli $conn;
    private string $test_db_name = 'test';

    protected function setUp(): void
    {
        $server_name = $_ENV['SERVER'];
        $db_username = $_ENV['USERNAME'];
        $password = $_ENV['PASSWORD'];
        $db_name = $_ENV['DATABASE'];

        $this->conn = new mysqli($server_name, $db_username, $password, $db_name);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        $this->db = new Database($this->conn);

        $stmt = $this->conn->query("SHOW DATABASES LIKE '{$this->test_db_name}'");
        $result = $stmt->fetch_assoc();

        if ($result) {
            throw new Exception("The testing database '{$this->test_db_name}' already exists. Please provide another name.");
        }
    }

    public function testCreateDatabaseAndTables(): void
    {

        $this->db->createDatabase($this->test_db_name);
        $this->conn->select_db($this->test_db_name);

        $this->db->createUsersTable();
        $this->assertTableExists('users');

        $this->db->createUsersConfigTable();
        $this->assertTableExists('user_configs');
    }

    private function assertTableExists(string $table_name): void
    {
        $stmt = $this->conn->query("SHOW TABLES LIKE '$table_name'");
        $result = $stmt->fetch_assoc();
        $this->assertNotNull($result, "Table $table_name was not created successfully");
    }

    protected function tearDown(): void
    {
        $this->conn->query("DROP DATABASE IF EXISTS {$this->test_db_name}");
    }
}
