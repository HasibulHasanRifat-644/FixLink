<?php

/**
 * Equipment model.
 * All SQL for the `equipment` table lives here only.
 */
class Equipment
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Get every piece of equipment for this owner, with availability
     * COMPUTED on the fly instead of read from a manually-set column.
     *
     * If there is any 'Accepted' rental_requests row for a piece of
     * equipment, it's "Rented". Otherwise it's "Available". This is the
     * beyond-CRUD part: the status is derived from real data (is someone
     * actually renting it right now?) instead of the owner just picking
     * a value from a dropdown, which could easily go stale/wrong.
     */
    public function getAllByOwner($ownerId)
    {
        $stmt = $this->conn->prepare(
            "SELECT e.*,
                CASE WHEN EXISTS (
                    SELECT 1 FROM rental_requests rr
                    WHERE rr.equipment_id = e.id
                    AND rr.status = 'Accepted'
                ) THEN 'Rented' ELSE 'Available' END AS computed_availability
             FROM equipment e
             WHERE e.owner_id = ?"
        );
        $stmt->bind_param("i", $ownerId);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function find($id, $ownerId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM equipment WHERE id = ? AND owner_id = ?");
        $stmt->bind_param("ii", $id, $ownerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // ADDED: character-matching search, scoped to this owner. Uses a
    // prepared statement -- the %term% wildcard is bound as data, so this
    // is not SQL-injectable. Keeps the same computed_availability logic
    // as getAllByOwner() so the badge stays correct while searching.
    public function search($ownerId, $term)
    {
        $likeTerm = "%" . $term . "%";

        $stmt = $this->conn->prepare(
            "SELECT e.*,
                CASE WHEN EXISTS (
                    SELECT 1 FROM rental_requests rr
                    WHERE rr.equipment_id = e.id
                    AND rr.status = 'Accepted'
                ) THEN 'Rented' ELSE 'Available' END AS computed_availability
             FROM equipment e
             WHERE e.owner_id = ?
             AND (e.equipment_name LIKE ? OR e.category LIKE ? OR e.brand LIKE ? OR e.model LIKE ?)"
        );
        $stmt->bind_param("issss", $ownerId, $likeTerm, $likeTerm, $likeTerm, $likeTerm);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function create($ownerId, $data)
    {
        // Availability is no longer a field the owner picks -- every new
        // listing starts "Available" by definition, since nobody has
        // rented it yet. The real status is computed by getAllByOwner().
        $stmt = $this->conn->prepare(
            "INSERT INTO equipment
                (owner_id, equipment_name, category, brand, model, description, rental_price, availability)
             VALUES (?, ?, ?, ?, ?, ?, ?, 'Available')"
        );
        $stmt->bind_param(
            "isssssd",
            $ownerId,
            $data["equipment_name"],
            $data["category"],
            $data["brand"],
            $data["model"],
            $data["description"],
            $data["rental_price"]
        );
        return $stmt->execute();
    }

    public function update($id, $ownerId, $data)
    {
        $stmt = $this->conn->prepare(
            "UPDATE equipment SET
                equipment_name = ?,
                category = ?,
                brand = ?,
                model = ?,
                description = ?,
                rental_price = ?
             WHERE id = ? AND owner_id = ?"
        );
        $stmt->bind_param(
            "sssssdii",
            $data["equipment_name"],
            $data["category"],
            $data["brand"],
            $data["model"],
            $data["description"],
            $data["rental_price"],
            $id,
            $ownerId
        );
        return $stmt->execute();
    }

    public function delete($id, $ownerId)
    {
        $stmt = $this->conn->prepare("DELETE FROM equipment WHERE id = ? AND owner_id = ?");
        $stmt->bind_param("ii", $id, $ownerId);
        return $stmt->execute();
    }
}
