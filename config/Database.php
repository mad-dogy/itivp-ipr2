<?php
class Database {
  private $host = "localhost";
  private $db_name = "api_db";
  private $username = "root";
  private $password = "";
  public $conn;

  public function getConnection() {
    $this->conn = null;
    try {
        $this->conn = new PDO(
            "mysql:host={$this->host};dbname={$this->db_name};charset=utf8",
            $this->username,
            $this->password
        );
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        http_response_code(500);
        die(json_encode(["error" => "Database connection failed: " . $e->getMessage()]));
    }
    return $this->conn;
  }
}