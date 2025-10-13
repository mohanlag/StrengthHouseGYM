<?php
// Start the PHP session to store temporary messages (like success/error)
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Strength House Gym</title>

    <!-- External CSS file link -->
    <link rel="stylesheet" href="css/style1.css">

    <!-- Inline CSS for page-specific styling -->
    <style>
        /* ====== Page Container ====== */
        .contact-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 20px;
        }

        /* ====== Contact Form Styling ====== */
        .contact-form {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        /* ====== Input Field Formatting ====== */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        /* Highlight active input field */
        .form-control:focus {
            outline: none;
            border-color: #3498db;
        }

        /* Textarea custom height */
        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        /* ====== Button Styling ====== */
        .btn {
            background: #3498db;
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: #2980b9;
        }

        /* ====== Alert Message Styles ====== */
        .alert {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* ====== Contact Info Box ====== */
        .contact-info {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 5px;
            margin-top: 2rem;
        }

        /* ====== Map Styling ====== */
        .contact-map iframe {
            width: 100%;
            height: 300px;
            border: none;
            margin-top: 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body>

    <!-- ===== Header Section ===== -->
    <header>
        <h1>Strength House Gym</h1>
        <p>Your ultimate destination for strength, fitness, and well-being!</p>

        <!-- ===== Navigation Menu ===== -->
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

    <!-- ===== Main Contact Section ===== -->
    <div class="contact-container">
        <h2>Contact Us</h2>
        <p>Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>

        <!-- ===== Success Message (Displayed if email sent) ===== -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; ?>
                <?php unset($_SESSION['success']); // Clear message after displaying ?>
            </div>
        <?php endif; ?>

        <!-- ===== Error Message (Displayed if sending failed) ===== -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php echo $_SESSION['error']; ?>
                <?php unset($_SESSION['error']); // Clear message after displaying ?>
            </div>
        <?php endif; ?>

        <!-- ===== Contact Form Section ===== -->
        <div class="contact-form">
            <form method="POST" action="send_message.php">
                <!-- Full Name -->
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" class="form-control" required 
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" class="form-control" required
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <!-- Subject -->
                <div class="form-group">
                    <label for="subject">Subject *</label>
                    <input type="text" id="subject" name="subject" class="form-control" required
                           value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>">
                </div>

                <!-- Message -->
                <div class="form-group">
                    <label for="message">Message *</label>
                    <textarea id="message" name="message" class="form-control" required
                              placeholder="Please describe your inquiry in detail..."><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn">Send Message</button>
            </form>
        </div>

        <!-- ===== Contact Info Section ===== -->
        <div class="contact-info">
            <h3>Other Ways to Reach Us</h3>
            <p><strong>Phone:</strong> (555) 123-4567</p>
            <p><strong>Email:</strong> info@strengthhouse.com</p>
            <p><strong>Address:</strong> 123 Fitness Street, Gym City, GC 12345</p>
            <p><strong>Hours:</strong> Monday-Friday: 5:00 AM - 11:00 PM, Weekends: 6:00 AM - 10:00 PM</p>
        </div>

        <!-- ===== Embedded Google Map Section ===== -->
        <div class="contact-map">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3313.156237499762!2d151.2092953!3d-33.868819!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6b12ae3d3e1a1f23%3A0x123456789!2sSydney%20NSW%2C%20Australia!5e0!3m2!1sen!2sau!4v000000000"
                allowfullscreen 
                loading="lazy">
            </iframe>
        </div>
    </div>

    <!-- ===== Footer Section ===== -->
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Strength House Gym. All rights reserved.</p>
    </footer>

</body>
</html>
