<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    // Basic validation
    $errors = [];
    
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
    
    if (empty($errors)) {
        try {
            // Ensure contact_messages table exists
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
            
            $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message]);
            
            $_SESSION['success'] = "Thank you for your message! We'll get back to you soon.";
            
        } catch(PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $_SESSION['error'] = "Sorry, there was an error sending your message. Please try again.";
            $_SESSION['form_data'] = $_POST; // Preserve form data
        }
    } else {
        $_SESSION['error'] = implode("<br>", $errors);
        $_SESSION['form_data'] = $_POST; // Preserve form data
    }
    
    header("Location: contact.php");
    exit();
} else {
    // If someone tries to access this page directly
    header("Location: contact.php");
    exit();
}
?>