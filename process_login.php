<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    try {
        // Check in users table
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // For demo purposes - check both hashed and plain text passwords
            $valid_password = false;
            
            // Check if password is hashed
            if (password_verify($password, $user['password'])) {
                $valid_password = true;
            }
            // Check demo passwords (plain text)
            else {
                $demo_passwords = [
                    'admin@strengthhouse.com' => 'admin123',
                    'trainer@strengthhouse.com' => 'trainer123', 
                    'a@yahoo.com' => 'password'
                ];
                
                if (isset($demo_passwords[$email]) && $password === $demo_passwords[$email]) {
                    $valid_password = true;
                }
            }
            
            if ($valid_password) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $user['email'];
                
                // Redirect based on role
                switch($user['role']) {
                    case 'admin':
                        header("Location: admin_dashboard.php");
                        exit();
                    case 'trainer':
                        header("Location: trainer_dashboard.php");
                        exit();
                    case 'member':
                    default:
                        header("Location: dashboard.php");
                        exit();
                }
            }
        }
        
        // Check trainers table
        $stmt = $conn->prepare("SELECT * FROM trainers WHERE email = ?");
        $stmt->execute([$email]);
        $trainer = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($trainer && password_verify($password, $trainer['password'])) {
            $_SESSION['user_id'] = $trainer['id'];
            $_SESSION['fullname'] = $trainer['name'];
            $_SESSION['role'] = 'trainer';
            $_SESSION['email'] = $trainer['email'];
            
            header("Location: trainer_dashboard.php");
            exit();
        }
        
        $_SESSION['error'] = "Invalid email or password.";
        header("Location: login.php");
        exit();
        
    } catch(PDOException $e) {
        $_SESSION['error'] = "Database error. Please try again.";
        header("Location: login.php");
        exit();
    }
}

// Display error if exists
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);
?>