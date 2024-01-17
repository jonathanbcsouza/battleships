<?php

use PHPUnit\Framework\TestCase;
use App\Classes\Database;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

class DatabaseTest extends TestCase
{
    private $db;
    private $conn;
    private $testDbName = 'test';

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

        $stmt = $this->conn->query("SHOW DATABASES LIKE '{$this->testDbName}'");
        $result = $stmt->fetch_assoc();

        if ($result) {
            throw new Exception("The testing database '{$this->testDbName}' already exists. Please provide another name.");
        }
    }

    public function testCreateDatabaseAndTables()
    {

        $this->db->createDatabase($this->testDbName);
        $this->conn->select_db($this->testDbName);

        $this->db->createUsersTable();
        $this->assertTableExists('users');

        $this->db->createUsersConfigTable();
        $this->assertTableExists('user_configs');
    }

    private function assertTableExists($tableName)
    {
        $stmt = $this->conn->query("SHOW TABLES LIKE '$tableName'");
        $result = $stmt->fetch_assoc();
        $this->assertNotNull($result, "Table $tableName was not created successfully");
    }

    protected function tearDown(): void
    {
        $this->conn->query("DROP DATABASE IF EXISTS {$this->testDbName}");
    }
}
