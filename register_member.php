<?php
session_start();
include 'db.php';

$success_message = "";
$error_message = "";

if(isset($_POST['register'])){
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $plan = $_POST['plan'];
    $goals = $_POST['goals'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = 'member';

    try {
        // Prevent duplicate emails using PDO
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        
        if($check->rowCount() > 0){
            $error_message = "Email already registered. Please use another one.";
        } else {
            // Insert using PDO
            $sql = "INSERT INTO users (fullname, email, phone, plan, goals, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$fullname, $email, $phone, $plan, $goals, $password, $role]);

            if($stmt->rowCount() > 0){
                $success_message = "Registration successful! Welcome to Strength House Gym!";
                // Clear form
                $_POST = array();
            } else {
                $error_message = "Error: Registration failed. Please try again.";
            }
        }
    } catch(PDOException $e) {
        $error_message = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Member - Strength House Gym</title>
    <link rel="stylesheet" href="css/style1.css">
    <style>
        .message-box {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 30px;
            border-radius: 10px;
            width: 80%;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .modal-success {
            border-top: 5px solid #28a745;
        }
        
        .modal-error {
            border-top: 5px solid #dc3545;
        }
        
        .modal h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .modal p {
            margin-bottom: 20px;
            font-size: 16px;
        }
        
        .modal-btn {
            background-color: #e74c3c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .modal-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body class="register-member">
    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content modal-success">
            <h3>🎉 Registration Successful!</h3>
            <p id="successMessage"></p>
            <button class="modal-btn" onclick="closeModal()">Continue</button>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" class="modal">
        <div class="modal-content modal-error">
            <h3>❌ Registration Failed</h3>
            <p id="errorMessage"></p>
            <button class="modal-btn" onclick="closeModal()">Try Again</button>
        </div>
    </div>

    <header>
        <h1>Strength House Gym</h1>
        <p>Your ultimate destination for strength, fitness, and well-being!</p>
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

    <section class="register-form">
        <h2>Register as a Member</h2>

        <!-- Inline Messages -->
        <?php if($success_message): ?>
            <div class="message-box success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if($error_message): ?>
            <div class="message-box error">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="fullname">Full Name</label>
            <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>" required>

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>

            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" placeholder="e.g. +61 400 123 456" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required>

            <label for="plan">Select Membership Plan</label>
            <select id="plan" name="plan" required>
                <option value="" disabled selected>-- Choose a Membership Plan --</option>
                <option value="basic" <?php echo (isset($_POST['plan']) && $_POST['plan'] == 'basic') ? 'selected' : ''; ?>>💪 Basic Plan – $29/month | Gym access only</option>
                <option value="standard" <?php echo (isset($_POST['plan']) && $_POST['plan'] == 'standard') ? 'selected' : ''; ?>>🔥 Standard Plan – $49/month | Gym + Group Classes</option>
                <option value="premium" <?php echo (isset($_POST['plan']) && $_POST['plan'] == 'premium') ? 'selected' : ''; ?>>👑 Premium Plan – $79/month | All Access + Personal Trainer</option>
                <option value="family" <?php echo (isset($_POST['plan']) && $_POST['plan'] == 'family') ? 'selected' : ''; ?>>👨‍👩‍👧 Family Plan – $99/month | 2 Adults + 2 Kids</option>
                <option value="student" <?php echo (isset($_POST['plan']) && $_POST['plan'] == 'student') ? 'selected' : ''; ?>>🎓 Student Plan – $39/month | Discounted rate for students</option>
            </select>

            <label for="goals">Fitness Goals</label>
            <textarea id="goals" name="goals" rows="4" placeholder="Tell us about your fitness goals..."><?php echo isset($_POST['goals']) ? htmlspecialchars($_POST['goals']) : ''; ?></textarea>

            <label for="password">Create Password</label>
            <input type="password" id="password" name="password" placeholder="Enter a secure password" required>

            <button type="submit" name="register">Register Now</button>
        </form>
        
        <p style="text-align: center; margin-top: 20px;">
            Already have an account? <a href="login.php">Login here</a>
        </p>
    </section>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Strength House Gym. All rights reserved.</p>
    </footer>

    <script>
        // Show modal messages
        <?php if($success_message): ?>
            document.getElementById('successMessage').textContent = "<?php echo $success_message; ?>";
            document.getElementById('successModal').style.display = 'block';
        <?php endif; ?>
        
        <?php if($error_message): ?>
            document.getElementById('errorMessage').textContent = "<?php echo $error_message; ?>";
            document.getElementById('errorModal').style.display = 'block';
        <?php endif; ?>

        function closeModal() {
            document.getElementById('successModal').style.display = 'none';
            document.getElementById('errorModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const successModal = document.getElementById('successModal');
            const errorModal = document.getElementById('errorModal');
            
            if (event.target == successModal) {
                successModal.style.display = 'none';
            }
            if (event.target == errorModal) {
                errorModal.style.display = 'none';
            }
        }
    </script>
</body>
</html>