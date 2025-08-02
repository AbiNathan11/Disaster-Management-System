<?php
require_once 'Database.php';

class User {
    public $conn;

    
    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

  
    public function register($username, $email, $password) {
       
        $stmt = $this->conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
      
            $stmt->close();
            return false;
        }
        $stmt->close();

       
        $hashed_password = $password; 

 
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashedPassword, $email);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

}
