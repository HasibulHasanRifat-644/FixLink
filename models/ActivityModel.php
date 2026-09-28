<?php

require_once __DIR__ . "/db.php";

class ActivityModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getActivityData($tab)
    {
        $sql = "";
        switch ($tab) {
            case 'part_requests':
                $sql = "SELECT id, part_name AS display_text, status FROM part_requests ORDER BY created_at DESC";
                break;
            case 'repair_requests':
                $sql = "SELECT id, equipment_name AS display_text, status FROM repair_requests ORDER BY created_at DESC";
                break;
            case 'rentals':
                $sql = "SELECT id, equipment_name AS display_text, status FROM rentals ORDER BY created_at DESC";
                break;
            case 'orders':
                $sql = "SELECT id, item_details AS display_text, status FROM orders ORDER BY created_at DESC";
                break;
            default:
                $sql = "SELECT id, part_name AS display_text, status FROM part_requests ORDER BY created_at DESC";
        }
        
        return mysqli_query($this->conn, $sql);
    }
}
?>