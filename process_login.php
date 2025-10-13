<?php
// =================== Start Session ===================
// Starts the session to store user login information
session_start();

// Include database connection
include 'db.php';

// =================== Process Login Form ===================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and trim input values from the form
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    try {
        // =================== Check Users Table ===================
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch user data as associative array
        
        if ($user) {
            $valid_password = false; // Flag to track if password is correct
            
            // =================== Check Hashed Password ===================
            if (password_verify($password, $user['password'])) {
                $valid_password = true;
            }
            // =================== Demo Plain Text Passwords ===================
            else {
                $demo_passwords = [
                    'admin@strengthhouse.com' => 'admin123',
                    'trainer@strengthhouse.com' => 'trainer123', 
                    'a@yahoo.com' => 'password'
                ];
                
                // Check if email exists in demo passwords and matches input
                if (isset($demo_passwords[$email]) && $password === $demo_passwords[$email]) {
                    $valid_password = true;
                }
            }
            
            // =================== Login Successful ===================
            if ($valid_password) {
                // Store user info in session
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
        
        // =================== Check Trainers Table ===================
        // For cases where trainers may have separate table
        $stmt = $conn->prepare("SELECT * FROM trainers WHERE email = ?");
        $stmt->execute([$email]);
        $trainer = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verify password if trainer found
        if ($trainer && password_verify($password, $trainer['password'])) {
            // Store trainer info in session
            $_SESSION['user_id'] = $trainer['id'];
            $_SESSION['fullname'] = $trainer['name'];
            $_SESSION['role'] = 'trainer';
            $_SESSION['email'] = $trainer['email'];
            
            header("Location: trainer_dashboard.php");
            exit();
        }
        
        // =================== Invalid Login ===================
        $_SESSION['error'] = "Invalid email or password.";
        header("Location: login.php");
        exit();
        
    } catch(PDOException $e) {
        // =================== Database Error ===================
        $_SESSION['error'] = "Database error. Please try again.";
        header("Location: login.php");
        exit();
    }
}

// =================== Display Error Message ===================
// Fetch error from session if exists and then clear it
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);
?>
