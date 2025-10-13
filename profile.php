<?php
// =================== Start Session ===================
// Start session to check if user is logged in
session_start();

// =================== Access Control ===================
// If user is not logged in or role is not 'member', redirect to login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

// =================== Demo User Data ===================
// Normally, this data would be fetched from the database
$user = [
    'fullname' => 'amish.gurung',
    'email' => 'a@yahoo.com',
    'phone' => '041624850',
    'plan' => 'premium',
    'goals' => 'asaa',
    'created_at' => '2024-01-01'
];

// =================== Handle Form Submission ===================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // In a real application, here you would update the database
    // Update the demo user array with submitted form values
    $user['fullname'] = $_POST['fullname'];
    $user['email'] = $_POST['email'];
    $user['phone'] = $_POST['phone'];
    $user['goals'] = $_POST['goals'];

    // Set a success message to display to the user
    $message = "Profile updated successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Strength House</title>
    <!-- Link to CSS files for dashboard styles -->
    <link rel="stylesheet" href="css/style_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <!-- =================== Sidebar =================== -->
        <div class="sidebar">
            <div class="logo">
                <h2>Strength<span>House</span></h2>
            </div>
            <ul class="nav-links">
                <!-- Navigation links with active highlighting for current page -->
                <li><a href="dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                <li><a href="profile.php" class="active"><i class="fas fa-user"></i> <span>Profile</span></a></li>
                <li><a href="workouts.php"><i class="fas fa-dumbbell"></i> <span>Workouts</span></a></li>
                <li><a href="schedule.php"><i class="fas fa-calendar-alt"></i> <span>Schedule</span></a></li>
                <li><a href="progress.php"><i class="fas fa-chart-line"></i> <span>Progress</span></a></li>
                <li><a href="nutrition.php"><i class="fas fa-apple-alt"></i> <span>Nutrition</span></a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </div>
        
        <!-- =================== Main Content =================== -->
        <div class="main-content">
            <div class="header">
                <h1>Your Profile</h1>
            </div>
            
            <!-- Display success message if profile updated -->
            <?php if (isset($message)): ?>
                <div class="success-message"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <!-- =================== Profile Form =================== -->
            <div class="profile-form">
                <form method="POST">
                    <!-- Full Name -->
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                    </div>
                    
                    <!-- Email -->
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    
                    <!-- Phone -->
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                    </div>
                    
                    <!-- Membership Plan -->
                    <div class="form-group">
                        <label>Membership Plan</label>
                        <select name="plan" class="form-control">
                            <option value="basic" <?php echo $user['plan'] == 'basic' ? 'selected' : ''; ?>>Basic</option>
                            <option value="premium" <?php echo $user['plan'] == 'premium' ? 'selected' : ''; ?>>Premium</option>
                            <option value="elite" <?php echo $user['plan'] == 'elite' ? 'selected' : ''; ?>>Elite</option>
                        </select>
                    </div>
                    
                    <!-- Fitness Goals -->
                    <div class="form-group">
                        <label>Fitness Goals</label>
                        <textarea name="goals" class="form-control"><?php echo htmlspecialchars($user['goals']); ?></textarea>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="btn">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
