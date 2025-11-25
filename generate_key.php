<?php
header("Content-Type: application/json; charset=UTF-8");
require_once "config/Database.php";

$db = (new Database())->getConnection();

$newKey = bin2hex(random_bytes(32));
$hashedKey = password_hash($newKey, PASSWORD_DEFAULT);

$stmt = $db->prepare("INSERT INTO api_keys (api_key, user_id, is_active) VALUES (:key, 1, 1)");
$stmt->execute([':key' => $hashedKey]);

echo json_encode([
    "message" => "API key generated successfully.",
    "api_key" => $newKey
]);