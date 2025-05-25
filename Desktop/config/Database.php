<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

class Database {
    private $host = 'localhost:3307';
    private $db_name = 'fitlogsync1';
    private $username = 'root';
    private $password = '';
    private $conn = null;
    private $error = null;

    public function __construct() {
        try {
            error_log("Attempting database connection to {$this->host}/{$this->db_name}");
            
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
            // Test the connection
            $result = $this->conn->query("SELECT 1")->fetch();
            if ($result) {
                error_log("Database connection test successful");
            }
            
            // Test if required tables exist
            $this->checkTables();
            
            error_log("Database connection and schema verification completed successfully");
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            error_log("Database Connection Error: " . $this->error);
            throw new Exception("Database connection failed: " . $this->error);
        }
    }

    private function checkTables() {
        // List of required tables
        $requiredTables = ['users', 'roles', 'user_roles'];
        
        // Get list of existing tables
        $stmt = $this->conn->query("SHOW TABLES");
        $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        error_log("Existing tables: " . implode(", ", $existingTables));
        
        // Check each required table
        foreach ($requiredTables as $table) {
            if (!in_array($table, $existingTables)) {
                error_log("Missing required table: {$table}");
                throw new Exception("Required table '{$table}' is missing");
            }
            
            // Check table structure
            $stmt = $this->conn->query("DESCRIBE {$table}");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            error_log("{$table} columns: " . implode(", ", $columns));
        }
    }

    public function getConnection() {
        if ($this->conn === null) {
            error_log("Attempting to get null database connection");
            throw new Exception("No database connection available");
        }
        return $this->conn;
    }

    public function getError() {
        return $this->error;
    }

    public function __destruct() {
        error_log("Closing database connection");
        $this->conn = null;
    }
} 