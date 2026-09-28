<?php

/**
 * PartRequest model.
 * Handles the part_requests table (rental requests from technicians).
 */
class PartRequest
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Get every request for this supplier, with the part name already joined in
    // (this also fixes the old "Trying to access array offset on null" bug --
    // a LEFT JOIN just returns NULL instead of failing when a part was deleted)
    //
    // ADDED: also joins in the customer's original request, if this
    // supplier request was linked to one from Technician_parts.php --
    // lets the supplier see WHY a request came in, not just what/how much.
    public function getAllBySupplier($supplierId)
    {
        $stmt = $this->conn->prepare(
            "SELECT pr.*, p.part_name,
                cr.device_name AS customer_device_name,
                cr.part_name AS customer_requested_part_name
             FROM supplier_part_requests pr
             LEFT JOIN supplier_parts p ON pr.part_id = p.id
             LEFT JOIN customer_part_requests cr ON pr.customer_request_id = cr.id
             WHERE pr.supplier_id = ?
             ORDER BY pr.id DESC"
        );
        $stmt->bind_param("i", $supplierId);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function updateStatus($id, $supplierId, $status)
    {
        $stmt = $this->conn->prepare(
            "UPDATE supplier_part_requests SET status = ? WHERE id = ? AND supplier_id = ?"
        );
        $stmt->bind_param("sii", $status, $id, $supplierId);
        return $stmt->execute();
    }
}
