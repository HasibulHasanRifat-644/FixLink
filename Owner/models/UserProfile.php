<?php

/**
 * UserProfile model.
 * Reads/updates the current user's own row in the shared `users` table.
 * Only touches name/phone/address -- never email, role, or status.
 */
class UserProfile
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function find($id)
    {
        $stmt = $this->conn->prepare("SELECT ID, name, email, phone, address FROM users WHERE ID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($id, $data)
    {
        $stmt = $this->conn->prepare("UPDATE users SET name = ?, phone = ?, address = ? WHERE ID = ?");
        $stmt->bind_param("sssi", $data["name"], $data["phone"], $data["address"], $id);
        return $stmt->execute();
    }
}
