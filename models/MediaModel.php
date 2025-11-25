<?php
class MediaModel {
    private $conn;
    private $table = "media";

    public $id;
    public $filename;
    public $original_name;
    public $mime_type;
    public $size;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table} (filename, original_name, mime_type, size)
             VALUES (:filename, :original_name, :mime_type, :size)"
        );
        $stmt->execute([
            ':filename' => $data['filename'],
            ':original_name' => $data['original_name'],
            ':mime_type' => $data['mime_type'],
            ':size' => $data['size']
        ]);
        return $this->conn->lastInsertId();
    }

    public function update($id, $data) {
        $stmt = $this->conn->prepare(
            "UPDATE {$this->table} 
             SET filename = :filename, original_name = :original_name, mime_type = :mime_type, size = :size 
             WHERE id = :id"
        );
        return $stmt->execute([
            ':filename' => $data['filename'],
            ':original_name' => $data['original_name'],
            ':mime_type' => $data['mime_type'],
            ':size' => $data['size'],
            ':id' => $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}