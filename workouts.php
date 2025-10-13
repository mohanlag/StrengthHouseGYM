<?php
// =================== Start session and access control ===================
session_start();

// Only allow logged-in members to access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php"); // Redirect unauthorized users to login page
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workouts - Strength House</title>
    
    <!-- Link to dashboard CSS -->
    <link rel="stylesheet" href="css/style_dashboard.css">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js for future charts (optional) -->
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
                <!-- Dashboard Link -->
                <li><a href="dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                
                <!-- Profile Link -->
                <li><a href="profile.php"><i class="fas fa-user"></i> <span>Profile</span></a></li>
                
                <!-- Workouts Link (active) -->
                <li><a href="workouts.php" class="active"><i class="fas fa-dumbbell"></i> <span>Workouts</span></a></li>
                
                <!-- Other navigation links -->
                <li><a href="schedule.php"><i class="fas fa-calendar-alt"></i> <span>Schedule</span></a></li>
                <li><a href="progress.php"><i class="fas fa-chart-line"></i> <span>Progress</span></a></li>
                <li><a href="nutrition.php"><i class="fas fa-apple-alt"></i> <span>Nutrition</span></a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </div>
        
        <!-- =================== Main Content =================== -->
        <div class="main-content">
            
            <!-- Page Header -->
            <div class="header">
                <h1>Your Workouts</h1>
            </div>
            
            <!-- =================== Workouts Grid =================== -->
            <div class="workouts-grid">
                
                <!-- Individual Workout Card -->
            <div class="workout-card">
                <h3>Strength Training</h3>
                <p>Full body workout focusing on compound movements</p>
                <p><strong>Duration:</strong> 60 minutes</p>
                <p><strong>Difficulty:</strong> Intermediate</p>
                <a href="workout_progress.php?name=Strength%20Training&duration=60" class="btn">Start Workout</a>
            </div>

            <div class="workout-card">
                <h3>Cardio Blast</h3>
                <p>High-intensity interval training for fat burning</p>
                <p><strong>Duration:</strong> 45 minutes</p>
                <p><strong>Difficulty:</strong> Advanced</p>
                <a href="workout_progress.php?name=Cardio%20Blast&duration=45" class="btn">Start Workout</a>
            </div>

            <div class="workout-card">
                <h3>Yoga & Flexibility</h3>
                <p>Improve mobility, flexibility and recovery</p>
                <p><strong>Duration:</strong> 30 minutes</p>
                <p><strong>Difficulty:</strong> Beginner</p>
                <a href="workout_progress.php?name=Yoga%20%26%20Flexibility&duration=30" class="btn">Start Workout</a>
            </div>

            <div class="workout-card">
                <h3>Upper Body Focus</h3>
                <p>Chest, back, shoulders and arms workout</p>
                <p><strong>Duration:</strong> 50 minutes</p>
                <p><strong>Difficulty:</strong> Intermediate</p>
                <a href="workout_progress.php?name=Upper%20Body%20Focus&duration=50" class="btn">Start Workout</a>
            </div>

                
            </div> <!-- End Workouts Grid -->
        </div> <!-- End Main Content -->
    </div> <!-- End Dashboard Container -->
</body>
</html>
