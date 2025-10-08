<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workouts - Strength House</title>
    <link rel="stylesheet" href="css/style_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <h2>Strength<span>House</span></h2>
            </div>
            <ul class="nav-links">
                <li><a href="index.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> <span>Profile</span></a></li>
                <li><a href="workouts.php" class="active"><i class="fas fa-dumbbell"></i> <span>Workouts</span></a></li>
                <li><a href="schedule.php"><i class="fas fa-calendar-alt"></i> <span>Schedule</span></a></li>
                <li><a href="progress.php"><i class="fas fa-chart-line"></i> <span>Progress</span></a></li>
                <li><a href="nutrition.php"><i class="fas fa-apple-alt"></i> <span>Nutrition</span></a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="header">
                <h1>Your Workouts</h1>
            </div>
            
            <div class="workouts-grid">
                <div class="workout-card">
                    <h3>Strength Training</h3>
                    <p>Full body workout focusing on compound movements</p>
                    <p><strong>Duration:</strong> 60 minutes</p>
                    <p><strong>Difficulty:</strong> Intermediate</p>
                    <button class="btn">Start Workout</button>
                </div>
                
                <div class="workout-card">
                    <h3>Cardio Blast</h3>
                    <p>High-intensity interval training for fat burning</p>
                    <p><strong>Duration:</strong> 45 minutes</p>
                    <p><strong>Difficulty:</strong> Advanced</p>
                    <button class="btn">Start Workout</button>
                </div>
                
                <div class="workout-card">
                    <h3>Yoga & Flexibility</h3>
                    <p>Improve mobility, flexibility and recovery</p>
                    <p><strong>Duration:</strong> 30 minutes</p>
                    <p><strong>Difficulty:</strong> Beginner</p>
                    <button class="btn">Start Workout</button>
                </div>
                
                <div class="workout-card">
                    <h3>Upper Body Focus</h3>
                    <p>Chest, back, shoulders and arms workout</p>
                    <p><strong>Duration:</strong> 50 minutes</p>
                    <p><strong>Difficulty:</strong> Intermediate</p>
                    <button class="btn">Start Workout</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>