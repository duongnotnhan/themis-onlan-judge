<?php

use PHPUnit\Framework\TestCase;
use Doctrine\DBAL\DriverManager;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

class DatabaseTest extends TestCase
{
    private $conn;

    protected function setUp(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->safeLoad();

        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = [
            'dbname'   => $_ENV['DB_NAME'] ?? 'online_judge',
            'user'     => $_ENV['DB_USER'] ?? 'root',
            'password' => $_ENV['DB_PASS'] ?? 'root',
            'host'     => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'driver'   => 'pdo_mysql',
        ];
        $this->conn = DriverManager::getConnection($connectionParams, $config);
    }

    public function testConnection()
    {
        $this->assertTrue($this->conn->connect());
    }

    public function testTableExists()
    {
        $schemaManager = $this->conn->createSchemaManager();
        $tables = $schemaManager->listTableNames();
        $this->assertContains('contest_settings', $tables);
        $this->assertContains('problems', $tables);
    }
}
