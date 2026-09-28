<?php

/**
 * IncomeReport model.
 * This is pure aggregation/reporting logic -- no HTML, no session handling.
 * The Controller just asks for a summary; this class knows how to build one.
 */
class IncomeReport
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getSummary($supplierId)
    {
        $stmt = $this->conn->prepare(
            "SELECT
                pr.quantity,
                pr.request_date,
                p.price,
                p.security_deposit,
                p.rental_duration_days,
                (p.price * pr.quantity * p.rental_duration_days) AS revenue
             FROM supplier_part_requests pr
             INNER JOIN supplier_parts p ON pr.part_id = p.id
             WHERE pr.supplier_id = ?
             AND pr.status = 'Accepted'"
        );
        $stmt->bind_param("i", $supplierId);
        $stmt->execute();
        $result = $stmt->get_result();

        $summary = array(
            "total_income"   => 0,
            "total_deposits" => 0,
            "total_rentals"  => 0,
            "monthly"        => array(),
        );

        while ($row = $result->fetch_assoc()) {
            $summary["total_income"] += $row["revenue"];
            $summary["total_deposits"] += ($row["security_deposit"] * $row["quantity"]);
            $summary["total_rentals"] += 1;

            $monthKey = date("Y-m", strtotime($row["request_date"]));

            if (!isset($summary["monthly"][$monthKey])) {
                $summary["monthly"][$monthKey] = 0;
            }

            $summary["monthly"][$monthKey] += $row["revenue"];
        }

        ksort($summary["monthly"]);

        return $summary;
    }
}
