<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

include 'db.php'; // PDO connection

$userId = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $month = $_POST['month'] ?? '';
    $weight = $_POST['weight'] ?? 0;
    $bodyfat = $_POST['bodyfat'] ?? 0;
    $muscle = $_POST['muscle'] ?? 0;

    if ($month && $weight && $bodyfat && $muscle) {
        // Check if this month already exists
        $checkStmt = $conn->prepare("SELECT id FROM user_progress WHERE user_id = ? AND month = ?");
        $checkStmt->execute([$userId, $month]);
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Update existing record
            $updateStmt = $conn->prepare("UPDATE user_progress SET weight = ?, bodyfat = ?, muscle = ? WHERE id = ?");
            $updateStmt->execute([$weight, $bodyfat, $muscle, $existing['id']]);
        } else {
            // Insert new record
            $insertStmt = $conn->prepare("INSERT INTO user_progress (user_id, month, weight, bodyfat, muscle) VALUES (?, ?, ?, ?, ?)");
            $insertStmt->execute([$userId, $month, $weight, $bodyfat, $muscle]);
        }

        header("Location: progress.php"); // refresh to show updated chart
        exit();
    }
}

// Fetch progress from database
$query = "SELECT month, weight, bodyfat, muscle FROM user_progress WHERE user_id = ? ORDER BY id ASC";
$stmt = $conn->prepare($query);
$stmt->execute([$userId]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$weight = [];
$bodyfat = [];
$muscle = [];

foreach ($result as $row) {
    $labels[] = $row['month'];
    $weight[] = $row['weight'];
    $bodyfat[] = $row['bodyfat'];
    $muscle[] = $row['muscle'];
}

$progressData = [
    'labels' => $labels,
    'weight' => $weight,
    'bodyfat' => $bodyfat,
    'muscle' => $muscle
];

$startingWeight = !empty($weight) ? $weight[0] : 0;
$currentWeight = !empty($weight) ? end($weight) : 0;
$weightLost = $startingWeight - $currentWeight;
$muscleGained = !empty($muscle) ? end($muscle) - $muscle[0] : 0;
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
    <div class="sidebar">
        <div class="logo"><h2>Strength<span>House</span></h2></div>
        <ul class="nav-links">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
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
        <div class="header"><h1>Your Progress</h1></div>

        <!-- =================== Add/Update Progress Form =================== -->
        <div class="progress-form">
            <form method="post" style="display:flex; gap:10px; flex-wrap:wrap;">
                <input type="text" name="month" placeholder="Month (e.g., Oct)" required>
                <input type="number" step="0.1" name="weight" placeholder="Weight (kg)" required>
                <input type="number" step="0.1" name="bodyfat" placeholder="Body Fat (%)" required>
                <input type="number" step="0.1" name="muscle" placeholder="Muscle Mass (kg)" required>
                <button type="submit">Add/Update</button>
            </form>
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
                <div class="stat-item"><h4>Starting Weight</h4><p><?= $startingWeight ?> kg</p></div>
                <div class="stat-item"><h4>Current Weight</h4><p><?= $currentWeight ?> kg</p></div>
                <div class="stat-item"><h4>Weight Lost</h4><p><?= $weightLost ?> kg</p></div>
                <div class="stat-item"><h4>Muscle Gained</h4><p><?= $muscleGained ?> kg</p></div>
            </div>
        </div>
    </div>
</div>

<script>
const weightCtx = document.getElementById('weightChart').getContext('2d');
new Chart(weightCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($progressData['labels']); ?>,
        datasets: [{
            label: 'Weight (kg)',
            data: <?= json_encode($progressData['weight']); ?>,
            borderColor: '#3498db',
            backgroundColor: 'rgba(52, 152, 219, 0.1)',
            tension: 0.3,
            fill: true
        }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});

const compCtx = document.getElementById('compositionChart').getContext('2d');
new Chart(compCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($progressData['labels']); ?>,
        datasets: [
            { label: 'Body Fat %', data: <?= json_encode($progressData['bodyfat']); ?>, borderColor: '#e74c3c', backgroundColor: 'rgba(231,76,60,0.1)', tension: 0.3, fill:true },
            { label: 'Muscle Mass (kg)', data: <?= json_encode($progressData['muscle']); ?>, borderColor: '#2ecc71', backgroundColor: 'rgba(46,204,113,0.1)', tension: 0.3, fill:true }
        ]
    },
    options: { responsive: true, maintainAspectRatio: false }
});
</script>
</body>
</html>
