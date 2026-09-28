<?php

/**
 * IncomeReport model.
 * Pure aggregation logic, no HTML. Revenue per accepted rental request
 * is rental_price * rental_days (both already exist in your schema --
 * no new columns needed for this feature at all).
 */
class IncomeReport
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getSummary($ownerId)
    {
        $stmt = $this->conn->prepare(
            "SELECT
                rr.rental_days,
                rr.request_date,
                e.rental_price,
                (e.rental_price * rr.rental_days) AS revenue
             FROM rental_requests rr
             INNER JOIN equipment e ON rr.equipment_id = e.id
             WHERE rr.owner_id = ?
             AND rr.status = 'Accepted'"
        );
        $stmt->bind_param("i", $ownerId);
        $stmt->execute();
        $result = $stmt->get_result();

        $summary = array(
            "total_income"  => 0,
            "total_rentals" => 0,
            "monthly"       => array(),
        );

        while ($row = $result->fetch_assoc()) {
            $summary["total_income"] += $row["revenue"];
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
