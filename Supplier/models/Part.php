<?php

/**
 * Part model.
 * Every method here is the ONLY place that touches the `parts` table.
 * Controllers and Views never write SQL directly.
 */
class Part
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Get every part belonging to a supplier (used by the "My Parts" list)
    public function getAllBySupplier($supplierId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM supplier_parts WHERE supplier_id = ?");
        $stmt->bind_param("i", $supplierId);
        $stmt->execute();
        return $stmt->get_result();
    }

    // ADDED: character-matching search, scoped to this supplier.
    // Still a prepared statement -- the %term% wildcards are bound as data,
    // never concatenated into the SQL string, so this is not SQL-injectable.
    public function search($supplierId, $term)
    {
        $likeTerm = "%" . $term . "%";

        $stmt = $this->conn->prepare(
            "SELECT * FROM supplier_parts
             WHERE supplier_id = ?
             AND (part_name LIKE ? OR category LIKE ? OR description LIKE ?)"
        );
        $stmt->bind_param("isss", $supplierId, $likeTerm, $likeTerm, $likeTerm);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Get a single part, scoped to the owning supplier (used by Edit)
    public function find($id, $supplierId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM supplier_parts WHERE id = ? AND supplier_id = ?");
        $stmt->bind_param("ii", $id, $supplierId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($supplierId, $data)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO supplier_parts
                (supplier_id, part_name, category, description, price, security_deposit, rental_duration_days, stock)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "isssddii",
            $supplierId,
            $data['part_name'],
            $data['category'],
            $data['description'],
            $data['price'],
            $data['security_deposit'],
            $data['rental_duration_days'],
            $data['stock']
        );
        return $stmt->execute();
    }

    public function update($id, $supplierId, $data)
    {
        $stmt = $this->conn->prepare(
            "UPDATE supplier_parts SET
                part_name = ?,
                category = ?,
                description = ?,
                price = ?,
                security_deposit = ?,
                rental_duration_days = ?,
                stock = ?
             WHERE id = ? AND supplier_id = ?"
        );
        $stmt->bind_param(
            "sssddiiii",
            $data['part_name'],
            $data['category'],
            $data['description'],
            $data['price'],
            $data['security_deposit'],
            $data['rental_duration_days'],
            $data['stock'],
            $id,
            $supplierId
        );
        return $stmt->execute();
    }

    public function delete($id, $supplierId)
    {
        $stmt = $this->conn->prepare("DELETE FROM supplier_parts WHERE id = ? AND supplier_id = ?");
        $stmt->bind_param("ii", $id, $supplierId);
        return $stmt->execute();
    }
}
