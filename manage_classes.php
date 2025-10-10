<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

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
    <title>Manage classes</title>
    <link rel="stylesheet" href="css/style_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header>
  <div class="container">
    <h1>Strength House Gym - Admin</h1>
    <p class="meta">Welcome back, <?= htmlspecialchars($adminName) ?>!</p>
    <nav>
      <a href="admin_dashboard.php">Dashboard</a> |
      <a href="manage_members.php">Manage Members</a> |
      <a href="manage_trainers.php">Manage Trainers</a> |
      <a href="logout.php">Logout</a>
    </nav>
  </div>
</header>
        
        <div class="main-content">
            <div class="header">
                <h1>Class Schedule</h1>
            </div>
            
            <div class="schedule-container">
                <?php foreach($schedule as $day): ?>
                <div class="schedule-day">
                    <h3><?php echo $day['day']; ?></h3>
                    <div class="classes-list">
                        <?php foreach($day['classes'] as $class): ?>
                        <div class="schedule-class">
                            <div class="class-info">
                                <h4><?php echo $class['name']; ?></h4>
                                <p>with <?php echo $class['instructor']; ?></p>
                            </div>
                            <div class="class-time"><?php echo $class['time']; ?></div>
                            <button class="btn btn-small">Book</button>
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