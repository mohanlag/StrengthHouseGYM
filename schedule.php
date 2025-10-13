<?php
// =================== Start Session ===================
// Start session to track user login and role
session_start();

// =================== Check if User is Logged In ===================
// Only members can access this page, otherwise redirect to login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

// =================== Demo Schedule Data ===================
// Array representing weekly classes and their details
$schedule = [
    ['day' => 'Monday', 'classes' => [
        ['name' => 'HIIT Training', 'time' => '6:00 AM', 'instructor' => 'Sarah'],
        ['name' => 'Yoga', 'time' => '5:30 PM', 'instructor' => 'Michael']
    ]],
    ['day' => 'Tuesday', 'classes' => [
        ['name' => 'Strength Training', 'time' => '7:00 AM', 'instructor' => 'David'],
        ['name' => 'Spin Class', 'time' => '6:00 PM', 'instructor' => 'Lisa']
    ]],
    ['day' => 'Wednesday', 'classes' => [
        ['name' => 'Cardio Blast', 'time' => '6:30 AM', 'instructor' => 'Sarah'],
        ['name' => 'Pilates', 'time' => '5:00 PM', 'instructor' => 'Emma']
    ]],
    ['day' => 'Thursday', 'classes' => [
        ['name' => 'Strength Training', 'time' => '7:00 AM', 'instructor' => 'David'],
        ['name' => 'Yoga', 'time' => '6:30 PM', 'instructor' => 'Michael']
    ]],
    ['day' => 'Friday', 'classes' => [
        ['name' => 'HIIT Training', 'time' => '6:00 AM', 'instructor' => 'Sarah'],
        ['name' => 'Boxing', 'time' => '5:00 PM', 'instructor' => 'John']
    ]],
    ['day' => 'Saturday', 'classes' => [
        ['name' => 'Full Body Workout', 'time' => '8:00 AM', 'instructor' => 'David'],
        ['name' => 'Yoga', 'time' => '10:00 AM', 'instructor' => 'Emma']
    ]]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule - Strength House</title>
    <!-- =================== External CSS =================== -->
    <link rel="stylesheet" href="css/style_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <!-- =================== Sidebar Navigation =================== -->
        <div class="sidebar">
            <div class="logo">
                <h2>Strength<span>House</span></h2>
            </div>
            <ul class="nav-links">
                <li><a href="dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> <span>Profile</span></a></li>
                <li><a href="workouts.php"><i class="fas fa-dumbbell"></i> <span>Workouts</span></a></li>
                <li><a href="schedule.php" class="active"><i class="fas fa-calendar-alt"></i> <span>Schedule</span></a></li>
                <li><a href="progress.php"><i class="fas fa-chart-line"></i> <span>Progress</span></a></li>
                <li><a href="nutrition.php"><i class="fas fa-apple-alt"></i> <span>Nutrition</span></a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </div>
        
        <!-- =================== Main Content =================== -->
        <div class="main-content">
            <div class="header">
                <h1>Class Schedule</h1>
            </div>
            
            <!-- =================== Schedule Container =================== -->
            <div class="schedule-container">
                <!-- Loop through each day of the week -->
                <?php foreach($schedule as $day): ?>
                <div class="schedule-day">
                    <!-- Day Header -->
                    <h3><?php echo $day['day']; ?></h3>
                    <div class="classes-list">
                        <!-- Loop through each class for the day -->
                        <?php foreach($day['classes'] as $class): ?>
                        <div class="schedule-class">
                            <div class="class-info">
                                <!-- Class Name and Instructor -->
                                <h4><?php echo $class['name']; ?></h4>
                                <p>with <?php echo $class['instructor']; ?></p>
                            </div>
                            <!-- Class Time -->
                            <div class="class-time"><?php echo $class['time']; ?></div>
                            <!-- Book Button -->
<a class="btn btn-small" 
   href="book_class.php?day=<?php echo urlencode($day['day']); ?>&class=<?php echo urlencode($class['name']); ?>&time=<?php echo urlencode($class['time']); ?>&instructor=<?php echo urlencode($class['instructor']); ?>">
   Book
</a>

                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
