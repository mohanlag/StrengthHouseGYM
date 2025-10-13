<?php
// Start session and verify member access
session_start();

// Redirect non-logged-in users or non-members to the login page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

// Include database connection file
include 'db.php';

// Fetch user information from the database using their session ID
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Simulated workout stats (replace with real DB data if needed)
$workoutStats = [
    'completed' => 12,
    'remaining' => 8,
    'streak' => 5
];

// Simulated upcoming class data
$upcomingClasses = [
    ['name' => 'HIIT Training', 'time' => 'Today, 6:00 PM', 'instructor' => 'Sarah Johnson'],
    ['name' => 'Yoga Flow', 'time' => 'Tomorrow, 7:00 AM', 'instructor' => 'Michael Chen'],
    ['name' => 'Strength Training', 'time' => 'Wednesday, 5:30 PM', 'instructor' => 'David Wilson']
];

// Simulated progress data for chart visualization
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

    <!-- External Styles and Icons -->
    <link rel="stylesheet" href="css/style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Chart.js for displaying progress chart -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* ===============================
           Global Variables and Resets
        =============================== */
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --success: #2ecc71;
            --warning: #f39c12;
            --danger: #e74c3c;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* ===============================
           Sidebar Section
        =============================== */
        .sidebar {
            width: 250px;
            background: var(--primary);
            color: white;
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .logo {
            text-align: center;
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .logo h2 {
            color: white;
            font-size: 1.5rem;
        }

        .logo span {
            color: var(--secondary);
        }

        .nav-links {
            list-style: none;
            padding: 0 15px;
        }

        .nav-links li {
            margin-bottom: 10px;
        }

        /* Sidebar navigation links */
        .nav-links a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .nav-links a:hover, .nav-links a.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .nav-links i {
            margin-right: 10px;
        }

        /* ===============================
           Main Content Section
        =============================== */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
        }

        /* Header displaying user info and current date */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 15px;
        }

        /* ===============================
           Dashboard Statistics Section
        =============================== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            text-align: center;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .stat-card i {
            color: var(--secondary);
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        /* ===============================
           Dashboard Grid (Cards)
        =============================== */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .dashboard-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        /* Chart container */
        .progress-chart {
            height: 250px;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .sidebar { width: 70px; }
            .logo h2, .nav-links span { display: none; }
            .main-content { margin-left: 70px; }
        }

        @media (max-width: 768px) {
            .stats-grid, .dashboard-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- ===============================
             Sidebar Navigation
        =============================== -->
        <div class="sidebar">
            <div class="logo">
                <h2>Strength<span>House</span></h2>
            </div>
            <ul class="nav-links">
                <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> <span>Profile</span></a></li>
                <li><a href="workouts.php"><i class="fas fa-dumbbell"></i> <span>Workouts</span></a></li>
                <li><a href="schedule.php"><i class="fas fa-calendar-alt"></i> <span>Schedule</span></a></li>
                <li><a href="progress.php"><i class="fas fa-chart-line"></i> <span>Progress</span></a></li>
                <li><a href="nutrition.php"><i class="fas fa-apple-alt"></i> <span>Nutrition</span></a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </div>
        
        <!-- ===============================
             Main Dashboard Content
        =============================== -->
        <div class="main-content">
            <!-- Header Section: User info + current date -->
            <div class="header">
                <div class="user-info">
                    <!-- Avatar (first letter of user's name) -->
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

            <!-- Member stats overview -->
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

            <!-- ===============================
                 Dashboard Cards Section
            =============================== -->
            <div class="dashboard-grid">
                <!-- Profile Summary Card -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Your Profile</h3>
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="profile-info">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['fullname']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                        <p><strong>Plan:</strong> <?php echo htmlspecialchars(ucfirst($user['plan'])); ?></p>
                        <p><strong>Goals:</strong> <?php echo htmlspecialchars($user['goals']); ?></p>
                    </div>
                    <a href="profile.php" class="btn btn-outline" style="margin-top: 15px;">Edit Profile</a>
                </div>

                <!-- Progress Chart Card -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Your Progress</h3>
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="progress-chart">
                        <canvas id="progressChart"></canvas>
                    </div>
                </div>

                <!-- Upcoming Classes Card -->
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
                    <a href="schedule.php" class="btn" style="margin-top: 15px; width: 100%;">View Full Schedule</a>
                </div>

                <!-- Quick Actions Card -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Quick Actions</h3>
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="quick-actions">
                        <a href="workouts.php" class="btn" style="display:block; margin-bottom:10px;">
                            <i class="fas fa-dumbbell"></i> Start Workout
                        </a>
                        <a href="nutrition.php" class="btn btn-outline" style="display:block; margin-bottom:10px;">
                            <i class="fas fa-apple-alt"></i> Log Nutrition
                        </a>
                        <a href="progress.php" class="btn btn-outline" style="display:block;">
                            <i class="fas fa-weight"></i> Update Measurements
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===============================
         Chart.js Configuration Script
    =============================== -->
    <script>
        // Create a progress chart using Chart.js
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
                    legend: { position: 'top' },
                    title: { display: true, text: 'Fitness Progress' }
                }
            }
        });

        // Auto-update the time displayed in the header every minute
        function updateTime() {
            const now = new Date();
            document.querySelector('.date-display h2').textContent =
                now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' });
            document.querySelector('.date-display p').textContent =
                now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
        }
        setInterval(updateTime, 60000); // Refresh time every minute
    </script>
</body>
</html>
