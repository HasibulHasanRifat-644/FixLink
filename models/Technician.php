<?php
require_once __DIR__ . "/../db.php";

class Technician {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function findByEmail($email) {
        $safe_email = mysqli_real_escape_string($this->conn, $email);
        $sql = "SELECT * FROM users WHERE email = '$safe_email' LIMIT 1";
        $result = mysqli_query($this->conn, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
        return null;
    }

    public function updateProfile($email, $specialization, $experience, $availability) {
        $safe_email = mysqli_real_escape_string($this->conn, $email);
        $safe_spec = mysqli_real_escape_string($this->conn, $specialization);
        $safe_exp = mysqli_real_escape_string($this->conn, $experience);
        $safe_avail = mysqli_real_escape_string($this->conn, $availability);

        $sql = "UPDATE users SET specialization = '$safe_spec', experience = '$safe_exp', availability = '$safe_avail' WHERE email = '$safe_email'";
        return mysqli_query($this->conn, $sql);
    }

    public function uploadCertificate($email, $title, $target_path) {
        $safe_email = mysqli_real_escape_string($this->conn, $email);
        $safe_title = mysqli_real_escape_string($this->conn, $title);
        $safe_path = mysqli_real_escape_string($this->conn, $target_path);

        $sql = "INSERT INTO technician_certifications (technician_email, cert_title, cert_image) VALUES ('$safe_email', '$safe_title', '$safe_path')";
        return mysqli_query($this->conn, $sql);
    }

    public function getCertificateCount($email) {
        $safe_email = mysqli_real_escape_string($this->conn, $email);
        $sql = "SELECT COUNT(*) as total FROM technician_certifications WHERE technician_email = '$safe_email'";
        $result = mysqli_query($this->conn, $sql);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            return $row['total'] ?? 0;
        }
        return 0;
    }

    public function getActiveRepairRequests($username) {
        $safe_username = mysqli_real_escape_string($this->conn, $username);
        $sql = "SELECT * FROM repair_requests WHERE customer_name = '$safe_username' OR issue_description LIKE '%$safe_username%' ORDER BY id DESC";
        return mysqli_query($this->conn, $sql);
    }

    public function getAllCustomers() {
        $sql = "SELECT DISTINCT id, name, email FROM users WHERE role = 'customer'";
        return mysqli_query($this->conn, $sql);
    }

    public function getCustomerNameById($cust_id) {
        $id = intval($cust_id);
        $sql = "SELECT name FROM users WHERE id = $id LIMIT 1";
        $result = mysqli_query($this->conn, $sql);
        if ($result && $row = mysqli_fetch_assoc($result)) {
            return $row['name'];
        }
        return "Customer";
    }

    public function getMessages($tech_id, $cust_id) {
        $t_id = intval($tech_id);
        $c_id = intval($cust_id);
        $sql = "SELECT * FROM messages 
                WHERE (sender_id = $t_id AND receiver_id = $c_id) 
                   OR (sender_id = $c_id AND receiver_id = $t_id) 
                ORDER BY sent_at ASC";
        return mysqli_query($this->conn, $sql);
    }

    public function sendMessage($tech_id, $receiver_id, $message_text) {
        $t_id = intval($tech_id);
        $r_id = intval($receiver_id);
        $safe_msg = mysqli_real_escape_string($this->conn, trim($message_text));

        if (!empty($safe_msg) && $r_id > 0) {
            $sql = "INSERT INTO messages (sender_id, sender_role, receiver_id, message, sent_at) 
                    VALUES ($t_id, 'Technician', $r_id, '$safe_msg', NOW())";
            return mysqli_query($this->conn, $sql);
        }
        return false;
    }
}
?>