<?php
session_start();
include 'db.php';

$success_message = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect inputs
    $fullname    = trim($_POST['fullname'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $phone       = trim($_POST['phone'] ?? '');
    $specialty   = trim($_POST['specialty'] ?? '');
    $experience  = intval($_POST['experience'] ?? 0);
	$schedule	 = trim($_POST['schedule'] ?? '');
    $password    = password_hash($_POST['password'] ?? '', PASSWORD_BCRYPT);
    $role        = 'trainer';

    // Basic required check
    if ($fullname === '' || $email === '' || $phone === '' || $specialty === '' || $specialty === '' || empty($_POST['password'])) {
        $error_message = "All fields are required.";
    } else {
        try {
            // Check for duplicate emails using PDO
            $check1 = $conn->prepare("SELECT id FROM trainers WHERE email = ?");
            $check1->execute([$email]);
            
            $check2 = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $check2->execute([$email]);

            if ($check1->rowCount() > 0 || $check2->rowCount() > 0) {
                $error_message = "Email already registered. Please use another one.";
            } else {
                // Insert trainer using PDO
                $sql = "INSERT INTO trainers (fullname, email, phone, specialty, experience, schedule, password, role) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$fullname, $email, $phone, $specialty, $experience, $schedule, $password, $role]);

                if ($stmt->rowCount() > 0) {
                    $success_message = "Trainer registration successful! Welcome to Strength House Gym team!";
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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Trainer - Strength House Gym</title>
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
        
        .register-trainer {
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .register-trainer form {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .register-trainer label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .register-trainer input,
        .register-trainer select {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .register-trainer input[type="submit"] {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 1rem;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .register-trainer input[type="submit"]:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
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

    <section class="register-trainer">
        <h2>Register as a Trainer</h2>

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

        <form method="post">
            <label for="fullname">Full Name</label>
            <input type="text" id="fullname" name="fullname" placeholder="Enter full name" value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>

            <label for="phone">Phone</label>
            <input type="tel" id="phone" name="phone" placeholder="Enter phone number" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required>

            <label for="specialty">Specialty</label>
            <input type="text" id="specialty" name="specialty" placeholder="E.g., Strength Training, Nutrition" value="<?php echo isset($_POST['specialty']) ? htmlspecialchars($_POST['specialty']) : ''; ?>" required>

            <label for="experience">Years of Experience</label>
            <input type="number" id="experience" name="experience" placeholder="Enter years of experience" value="<?php echo isset($_POST['experience']) ? htmlspecialchars($_POST['experience']) : ''; ?>" required min="0">
			
			<label for="schedule">Available Schedule</label>
            <input type="text" id="schedule" name="schedule" placeholder="Enter Available Schedule" value="<?php echo isset($_POST['schedule']) ? htmlspecialchars($_POST['schedule']) : ''; ?>" required>
			
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Create a password" required>

            <input type="submit" value="Register as Trainer" class="btn">
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