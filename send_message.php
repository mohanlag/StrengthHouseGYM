<?php
// =================== Start Session ===================
// Start session to store messages and preserve form data
session_start();

// =================== Include Database Connection ===================
include 'db.php';

// =================== Handle POST Request ===================
// Only process the form if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // =================== Sanitize and Trim Form Inputs ===================
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    // =================== Basic Validation ===================
    $errors = []; // Array to hold validation errors
    
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    
    if (empty($subject)) {
        $errors[] = "Subject is required.";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required.";
    }
    
    // =================== Process Valid Data ===================
    if (empty($errors)) {
        try {
            // =================== Ensure Table Exists ===================
            // Create the contact_messages table if it doesn't exist
            $conn->exec("
                CREATE TABLE IF NOT EXISTS contact_messages (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    subject VARCHAR(255) NOT NULL,
                    message TEXT NOT NULL,
                    status ENUM('pending', 'read') DEFAULT 'pending',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
            
            // =================== Insert Form Data ===================
            $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message]);
            
            // =================== Set Success Message ===================
            $_SESSION['success'] = "Thank you for your message! We'll get back to you soon.";
            
        } catch(PDOException $e) {
            // =================== Handle Database Errors ===================
            error_log("Database error: " . $e->getMessage());
            $_SESSION['error'] = "Sorry, there was an error sending your message. Please try again.";
            $_SESSION['form_data'] = $_POST; // Preserve form data for re-population
        }
    } else {
        // =================== Handle Validation Errors ===================
        $_SESSION['error'] = implode("<br>", $errors);
        $_SESSION['form_data'] = $_POST; // Preserve form data
    }
    
    // =================== Redirect Back to Contact Page ===================
    header("Location: contact.php");
    exit();
} else {
    // =================== Redirect if Accessed Directly ===================
    // Prevent direct access without submitting the form
    header("Location: contact.php");
    exit();
}
?>
