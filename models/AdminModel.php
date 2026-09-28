<?php

require_once __DIR__ . "/db.php";

class AdminModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getTotalUsers()
    {
        $res = mysqli_query($this->conn, "SELECT COUNT(*) as count FROM users");
        return $res ? (mysqli_fetch_assoc($res)['count'] ?? 0) : 0;
    }

    public function getPendingVerifications()
    {
        $res = mysqli_query($this->conn, "SELECT COUNT(*) as count FROM users WHERE status = 'pending'");
        return $res ? (mysqli_fetch_assoc($res)['count'] ?? 0) : 0;
    }

    public function getOpenDisputes()
    {
        $res = mysqli_query($this->conn, "SELECT COUNT(*) as count FROM disputes WHERE status = 'open'");
        return $res ? (mysqli_fetch_assoc($res)['count'] ?? 0) : 0;
    }

    public function getActiveListings()
    {
        $res = mysqli_query($this->conn, "SELECT COUNT(*) as count FROM listings WHERE status = 'active'");
        return $res ? (mysqli_fetch_assoc($res)['count'] ?? 0) : 0;
    }

    public function getRecentComplaints($limit = 5)
    {
        $query = "SELECT *, 'Complaint' as type FROM complaints ORDER BY id DESC LIMIT $limit";
        return @mysqli_query($this->conn, $query);
    }
}
?>