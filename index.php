<?php
header("Content-Type: application/json; charset=UTF-8");

require_once "config/Database.php";
require_once "config/Auth.php";
require_once "models/MediaModel.php";

$db = (new Database())->getConnection();

// Проверяем ключ
$headers = array_change_key_case(getallheaders(), CASE_UPPER);
(new Auth($db))->checkApiKey($headers);

// Модуль работы с медиа
$media = new MediaModel($db);

// Парсим маршрут
$method = $_SERVER['REQUEST_METHOD'];
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$parts = explode('/', $uri);

if (count($parts) >= 2 && $parts[0] === 'ipr2' && $parts[1] === 'media') {
  $id = $parts[2] ?? null;

  switch ($method) {
    case 'GET':
      if ($id) {
        $record = $media->getOne($id);
        if ($record) {
            echo json_encode($record);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Media not found']);
        }
      } else {
        echo json_encode($media->getAll());
      }
      break;

    case 'POST':
      $data = json_decode(file_get_contents("php://input"), true);
      if (!$data || !isset($data['filename'], $data['original_name'], $data['mime_type'], $data['size'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        break;
      }
      $id = $media->create($data);
      http_response_code(201);
      echo json_encode(["message" => "Media created", "id" => $id]);
      break;

    case 'PUT':
      if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing ID']);
        break;
      }
      $data = json_decode(file_get_contents("php://input"), true);
      if ($media->update($id, $data)) {
        echo json_encode(["message" => "Media updated"]);
      } else {
        http_response_code(404);
        echo json_encode(["error" => "Media not found or not updated"]);
      }
      break;

    case 'DELETE':
      if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing ID']);
        break;
      }
      if ($media->delete($id)) {
        echo json_encode(["message" => "Media deleted"]);
      } else {
        http_response_code(404);
        echo json_encode(["error" => "Media not found"]);
      }
      break;

    default:
      http_response_code(405);
      echo json_encode(["error" => "Method not allowed"]);
  }
} else {
  http_response_code(404);
  echo json_encode(["error" => "Invalid endpoint"]);
}