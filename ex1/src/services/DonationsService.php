<?php

require_once __DIR__ . '/../database/db.php';

class DonationsService {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAll(): array {
        $sql = "SELECT * FROM tasks";
        $result = $this->db->query($sql);
        $tasks = [];
        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row;
        }
        return $tasks;
    }

    /**
     * @param mixed $id
     */
    public function getById($id): array {
        $sql = "SELECT * FROM donations WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * @param mixed $data
     */
    public function create($data): bool {
        $sql = "INSERT INTO donations (name, description) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ss', $data['name'], $data['description']);
        return $stmt->execute();
    }

    /**
     * @param string $id
     * @param array $data
     */
    public function update($id, $data): bool {
        $sql = "UPDATE donations SET name = ?, description = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ssi', $data['name'], $data['description'], $id);
        return $stmt->execute();
    }

    /**
     * @param string $id
     */
    public function delete($id): bool {
        $sql = "DELETE FROM donations WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}
