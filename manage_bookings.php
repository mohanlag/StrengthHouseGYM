<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

require_once 'db.php';
$user_id = $_SESSION['user_id'];

// Cancel booking
if (isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];
    $stmt = $conn->prepare("DELETE FROM class_bookings WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => $booking_id, ':user_id' => $user_id]);
}

// Get bookings
$stmt = $conn->prepare("SELECT * FROM class_bookings WHERE user_id = :user_id ORDER BY booked_at DESC");
$stmt->execute([':user_id' => $user_id]);
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Strength House</title>
    <link rel="stylesheet" href="css/style_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            <li><a href="progress.php"><i class="fas fa-chart-line"></i> <span>Progress</span></a></li>
            <li><a href="nutrition.php"><i class="fas fa-apple-alt"></i> <span>Nutrition</span></a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Manage Your Bookings</h1>
        </div>

        <div class="schedule-container">
            <?php if(count($bookings) === 0): ?>
                <p>You have no bookings.</p>
            <?php else: ?>
                <?php foreach($bookings as $booking): ?>
                    <div class="schedule-day">
                        <div class="schedule-class">
                            <div class="class-info">
                                <h4><?php echo $booking['class_name']; ?></h4>
                                <p>with <?php echo $booking['instructor']; ?></p>
                            </div>
                            <div class="class-time"><?php echo $booking['class_time']; ?> | <?php echo $booking['class_day']; ?></div>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                <button type="submit" name="cancel_booking" class="btn btn-small">Cancel</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <div style="margin-top:10px;">
    <a href="schedule.php" class="btn btn-small" style="display:inline-block; width:auto;">Back to Schedule</a>
</div>


        </div>
    </div>
</div>
</body>
</html>
