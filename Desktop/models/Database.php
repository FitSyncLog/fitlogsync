<?php
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "fitlogsync";
    private $conn;

    public function __construct() {
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }

            // Set charset to utf8mb4
            $this->conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function executeQuery($query, $params = [], $types = "") {
        try {
            $stmt = $this->conn->prepare($query);
            
            if ($stmt === false) {
                throw new Exception("Failed to prepare statement: " . $this->conn->error);
            }

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            
            return $stmt;
        } catch (Exception $e) {
            error_log("Query execution error: " . $e->getMessage());
            throw new Exception("Failed to execute query");
        }
    }

    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>