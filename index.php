<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

include 'db.php';

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get workout stats
$workoutStats = [
    'completed' => 12,
    'remaining' => 8,
    'streak' => 5
];

// Get upcoming classes
$upcomingClasses = [
    ['name' => 'HIIT Training', 'time' => 'Today, 6:00 PM', 'instructor' => 'Sarah Johnson'],
    ['name' => 'Yoga Flow', 'time' => 'Tomorrow, 7:00 AM', 'instructor' => 'Michael Chen'],
    ['name' => 'Strength Training', 'time' => 'Wednesday, 5:30 PM', 'instructor' => 'David Wilson']
];

// Get progress data
$progressData = [
    'weight' => [75, 74, 73, 72, 71, 70],
    'bodyfat' => [18, 17.5, 17, 16.8, 16.5, 16.2],
    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Strength House Gym</title>
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
                <li><a href="index.php" class="active"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> <span>Profile</span></a></li>
                <li><a href="workouts.php"><i class="fas fa-dumbbell"></i> <span>Workouts</span></a></li>
                <li><a href="schedule.php"><i class="fas fa-calendar-alt"></i> <span>Schedule</span></a></li>
                <li><a href="progress.php"><i class="fas fa-chart-line"></i> <span>Progress</span></a></li>
                <li><a href="nutrition.php"><i class="fas fa-apple-alt"></i> <span>Nutrition</span></a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($user['fullname'], 0, 1)); ?>
                    </div>
                    <div class="user-details">
                        <h3><?php echo htmlspecialchars($user['fullname']); ?></h3>
                        <p>Member since <?php echo date('M Y', strtotime($user['created_at'] ?? 'now')); ?></p>
                    </div>
                </div>
                <div class="date-display">
                    <h2><?php echo date('l, F j'); ?></h2>
                    <p><?php echo date('g:i A'); ?></p>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-dumbbell"></i>
                    <h3><?php echo $workoutStats['completed']; ?></h3>
                    <p>Workouts Completed</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-fire"></i>
                    <h3><?php echo $workoutStats['streak']; ?> days</h3>
                    <p>Current Streak</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-trophy"></i>
                    <h3><?php echo $workoutStats['remaining']; ?></h3>
                    <p>Workouts to Goal</p>
                </div>
            </div>
            
            <!-- Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- Profile Card -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Your Profile</h3>
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="profile-info">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['fullname']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                        <p><strong>Plan:</strong> <span class="badge"><?php echo htmlspecialchars(ucfirst($user['plan'])); ?></span></p>
                        <p><strong>Goals:</strong> <?php echo htmlspecialchars($user['goals']); ?></p>
                    </div>
                    <a href="profile.php" class="btn btn-outline" style="margin-top: 15px; display: inline-block;">Edit Profile</a>
                </div>
                
                <!-- Progress Card -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Your Progress</h3>
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="progress-chart">
                        <canvas id="progressChart"></canvas>
                    </div>
                </div>
                
                <!-- Upcoming Classes -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Upcoming Classes</h3>
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <ul class="classes-list">
                        <?php foreach($upcomingClasses as $class): ?>
                        <li class="class-item">
                            <div class="class-info">
                                <h4><?php echo $class['name']; ?></h4>
                                <p>with <?php echo $class['instructor']; ?></p>
                            </div>
                            <div class="class-time"><?php echo $class['time']; ?></div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="schedule.php" class="btn" style="margin-top: 15px; display: inline-block; width: 100%; text-align: center;">View Full Schedule</a>
                </div>
                
                <!-- Quick Actions -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Quick Actions</h3>
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="quick-actions">
                        <a href="workouts.php" class="btn" style="margin-bottom: 10px; display: block; text-align: center;">
                            <i class="fas fa-dumbbell"></i> Start Workout
                        </a>
                        <a href="nutrition.php" class="btn btn-outline" style="margin-bottom: 10px; display: block; text-align: center;">
                            <i class="fas fa-apple-alt"></i> Log Nutrition
                        </a>
                        <a href="progress.php" class="btn btn-outline" style="display: block; text-align: center;">
                            <i class="fas fa-weight"></i> Update Measurements
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Progress Chart
        const ctx = document.getElementById('progressChart').getContext('2d');
        const progressChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($progressData['labels']); ?>,
                datasets: [
                    {
                        label: 'Weight (kg)',
                        data: <?php echo json_encode($progressData['weight']); ?>,
                        borderColor: '#3498db',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Body Fat %',
                        data: <?php echo json_encode($progressData['bodyfat']); ?>,
                        borderColor: '#e74c3c',
                        backgroundColor: 'rgba(231, 76, 60, 0.1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
    </script>
</body>
</html>