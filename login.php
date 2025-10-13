<?php
// Start a session to track logged-in users
session_start();

// Include database connection
include 'db.php';

// Check if the login form has been submitted
if(isset($_POST['login'])){
    // Get email and password input from form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL statement to fetch user with matching email
    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email); // bind the email parameter
    $stmt->execute();
    $result = $stmt->get_result();

    // NOTE: $user is not defined in your original code
    // You need to fetch the user from the result
    $user = $result->fetch_assoc();

    // Verify password
    if($user && password_verify($password, $user['password'])){
        // Store user info in session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role'] = $user['role'];

        // Redirect user based on their role
        if($user['role'] === 'admin'){
            header("Location: admin_dashboard.php");
            exit();
        } elseif($user['role'] === 'trainer'){
            header("Location: trainer_dashboard.php");
            exit();
        } else {
            header("Location: member_dashboard.php");
            exit();
        }
    } else {
        // Optional: Set an error message if login fails
        $login_error = "Invalid email or password.";
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
    <!-- ================= Header Section ================= -->
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
            <a href="contact.php">Contact</a> |
            <a href="login.php">Login</a>
        </nav>
    </header>

    <!-- ================= Login Form Section ================= -->
    <section class="login-form">
        <h2>Login</h2>

        <!-- Display login error if it exists -->
        <?php if(isset($login_error)): ?>
            <p style="color:red;"><?php echo $login_error; ?></p>
        <?php endif; ?>

        <!-- Login form sends POST request to process_login.php -->
        <form action="process_login.php" method="POST">
            <!-- Email input -->
            <input type="email" name="email" placeholder="Enter your email" required>
            
            <!-- Password input -->
            <input type="password" name="password" placeholder="Enter your password" required>
            
            <!-- Submit button -->
            <button type="submit" name="login">Login</button>
        </form>

        <!-- Additional links for password recovery and registration -->
        <p class="extra-links">
            <a href="forgot_password.html" class="forgot-link">Forgot Password?</a><br>
            Don’t have an account? <a href="register_member.php">Register here</a>
        </p>
    </section>

    <!-- ================= Footer Section ================= -->
    <footer>
        <!-- Dynamically display the current year -->
        <p>&copy; <?php echo date("Y"); ?> Strength House Gym. All rights reserved.</p>
    </footer>
</body>
</html>
