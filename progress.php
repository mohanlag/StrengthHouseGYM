<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

$progressData = [
    'weight' => [75, 74, 73, 72, 71, 70],
    'bodyfat' => [18, 17.5, 17, 16.8, 16.5, 16.2],
    'muscle' => [35, 35.5, 36, 36.5, 37, 37.5],
    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress - Strength House</title>
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
                <li><a href="workouts.php"><i class="fas fa-dumbbell"></i> <span>Workouts</span></a></li>
                <li><a href="schedule.php"><i class="fas fa-calendar-alt"></i> <span>Schedule</span></a></li>
                <li><a href="progress.php" class="active"><i class="fas fa-chart-line"></i> <span>Progress</span></a></li>
                <li><a href="nutrition.php"><i class="fas fa-apple-alt"></i> <span>Nutrition</span></a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="header">
                <h1>Your Progress</h1>
            </div>
            
            <div class="progress-grid">
                <div class="progress-card">
                    <h3>Weight Progress</h3>
                    <div class="progress-chart">
                        <canvas id="weightChart"></canvas>
                    </div>
                </div>
                
                <div class="progress-card">
                    <h3>Body Composition</h3>
                    <div class="progress-chart">
                        <canvas id="compositionChart"></canvas>
                    </div>
                </div>
                
                <div class="progress-stats">
                    <div class="stat-item">
                        <h4>Starting Weight</h4>
                        <p>75 kg</p>
                    </div>
                    <div class="stat-item">
                        <h4>Current Weight</h4>
                        <p>70 kg</p>
                    </div>
                    <div class="stat-item">
                        <h4>Weight Lost</h4>
                        <p>5 kg</p>
                    </div>
                    <div class="stat-item">
                        <h4>Muscle Gained</h4>
                        <p>2.5 kg</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Weight Chart
        const weightCtx = document.getElementById('weightChart').getContext('2d');
        const weightChart = new Chart(weightCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($progressData['labels']); ?>,
                datasets: [{
                    label: 'Weight (kg)',
                    data: <?php echo json_encode($progressData['weight']); ?>,
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Composition Chart
        const compCtx = document.getElementById('compositionChart').getContext('2d');
        const compChart = new Chart(compCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($progressData['labels']); ?>,
                datasets: [
                    {
                        label: 'Body Fat %',
                        data: <?php echo json_encode($progressData['bodyfat']); ?>,
                        borderColor: '#e74c3c',
                        backgroundColor: 'rgba(231, 76, 60, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Muscle Mass (kg)',
                        data: <?php echo json_encode($progressData['muscle']); ?>,
                        borderColor: '#2ecc71',
                        backgroundColor: 'rgba(46, 204, 113, 0.1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>
</body>
</html>