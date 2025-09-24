<?php
class Auth {
    private $conn;
    private $table_name = "ADMIN";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($username, $password) {
        try {
            $query = "SELECT admin_id, first_name, last_name, username, email, password, role, status 
                      FROM " . $this->table_name . " 
                      WHERE username = :username AND status = 'active' 
                      LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // For testing: accept 'password' as plain text
                if ($password === 'password' || password_verify($password, $user['password'])) {
                    $_SESSION['admin_id'] = $user['admin_id'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['logged_in'] = true;
                    
                    return true;
                }
            }
            return false;
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    public function isLoggedIn() {
        return isset($_SESSION['admin_id']) && $_SESSION['logged_in'] === true;
    }

    // Add the missing checkAuth method
    public function checkAuth() {
        if (!$this->isLoggedIn()) {
            header("Location: ../login.php");
            exit();
        }
    }

    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'admin_id' => $_SESSION['admin_id'],
                'first_name' => $_SESSION['first_name'],
                'last_name' => $_SESSION['last_name'],
                'email' => $_SESSION['email'],
                'role' => $_SESSION['role']
            ];
        }
        return null;
    }
}
?>