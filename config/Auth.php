<?php
class Auth {
  private $conn;

  public function __construct($db) {
    $this->conn = $db;
  }

 public function checkApiKey($headers) {
    $headers = array_change_key_case($headers, CASE_LOWER);

    if (!isset($headers['x-api-key'])) {
        http_response_code(401);
        die(json_encode(["error" => "Missing API key."]));
    }

    $apiKey = trim($headers['x-api-key']);

    $stmt = $this->conn->prepare("SELECT * FROM api_keys WHERE is_active = 1");
    $stmt->execute();
    $keys = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($keys as $row) {
        if (password_verify($apiKey, $row['api_key'])) {
            return true;
        }
    }

    http_response_code(401);
    die(json_encode(["error" => "Invalid API key."]));
}
}