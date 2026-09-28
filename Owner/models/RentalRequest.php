<?php

/**
 * RentalRequest model.
 * Handles the rental_requests table (requests from technicians to rent equipment).
 */
class RentalRequest
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAllByOwner($ownerId)
    {
        $stmt = $this->conn->prepare(
            "SELECT rr.*, e.equipment_name
             FROM rental_requests rr
             LEFT JOIN equipment e ON rr.equipment_id = e.id
             WHERE rr.owner_id = ?
             ORDER BY rr.id DESC"
        );
        $stmt->bind_param("i", $ownerId);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function updateStatus($id, $ownerId, $status)
    {
        $stmt = $this->conn->prepare(
            "UPDATE rental_requests SET status = ? WHERE id = ? AND owner_id = ?"
        );
        $stmt->bind_param("sii", $status, $id, $ownerId);
        return $stmt->execute();
    }
}
