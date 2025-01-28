<?php

class Donor
{
    private $db;
    private $id;
    private $name;
    private $email;
    private $phone;
    private $address;
    protected $table = 'donors';

    public function __construct($db) {
        $this->db = $db;
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function save() {
        if (!$this->validate()) {
            return false;
        }

        if ($this->id) {
            return $this->update();
        }
        return $this->insert();
    }

    private function insert() {
        $sql = "INSERT INTO {$this->table} (name, email, phone, address) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$this->name, $this->email, $this->phone, $this->address]);
    }

    private function update() {
        $sql = "UPDATE {$this->table} SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$this->name, $this->email, $this->phone, $this->address, $this->id]);
    }

    private function validate() {
        if (empty($this->name) || empty($this->email)) {
            return false;
        }
        return filter_var($this->email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // Getters and setters
    public function setName($name) { $this->name = $name; }
    public function setEmail($email) { $this->email = $email; }
    public function setPhone($phone) { $this->phone = $phone; }
    public function setAddress($address) { $this->address = $address; }
}