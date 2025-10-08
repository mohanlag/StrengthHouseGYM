<?php
session_start();
include 'db.php';

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    
    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

if(password_verify($password, $user['password'])){
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['fullname'] = $user['fullname'];
    $_SESSION['role'] = $user['role'];

    // Redirect based on role
    if($user['role'] === 'admin'){
        header("Location: admin_dashboard.php");
        exit();
    } elseif($user['role'] === 'trainer'){
        header("Location: trainer_dashboard.php");
        exit();
    } else {
        header("Location: index.html");
        exit();
    }
    
}

}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Strength House Gym</title>
    <!-- Link to external stylesheet -->
    <link rel="stylesheet" href="css/style1.css">
</head>
<body>
    <!-- ================= Header ================= -->
    <header>
        <h1>Strength House Gym</h1>
        <p>Your ultimate destination for strength, fitness, and well-being!</p>
        
        <!-- Navigation menu -->
        <nav>
            <a href="index.html">Home</a> |
            <a href="about.html">About</a> |
            <a href="schedule.html">Schedule</a> |
            <a href="register_member.php">Register Member</a> |
            <a href="register_trainer.php">Register Trainer</a> |
            <a href="membership_plans.html">Plans</a> |
            <a href="contact.html">Contact</a> |
            <a href="login.php">Login</a>
        </nav>
    </header>

    <!-- ================= Login Section ================= -->
    <section class="login-form">
        <h2>Login</h2>
        
        <!-- Login form: sends data to process_login.php -->
        <form action="process_login.php" method="POST">
            <!-- Email input -->
            <input type="email" name="email" placeholder="Enter your email" required>
            
            <!-- Password input -->
            <input type="password" name="password" placeholder="Enter your password" required>
            
            <!-- Submit button -->
            <button type="submit">Login</button>
        </form>

        <!-- Extra links: Forgot password and registration -->
        <p class="extra-links">
            <a href="forgot_password.html" class="forgot-link">Forgot Password?</a><br>
            Don’t have an account? <a href="register_member.php">Register here</a>
        </p>
    </section>

    <!-- ================= Footer ================= -->
    <footer>
        <!-- PHP dynamically generates the current year -->
        <p>&copy; <?php echo date("Y"); ?> Strength House Gym. All rights reserved.</p>
    </footer>
</body>
</html>

