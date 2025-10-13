<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

require_once 'db.php';
$user_id = $_SESSION['user_id'];

if (isset($_POST['book_class'])) {
    $day = $_POST['day'];
    $class_name = $_POST['class_name'];
    $time = $_POST['time'];
    $instructor = $_POST['instructor'];

    // Prevent duplicate booking
    $check = $conn->prepare("SELECT * FROM class_bookings WHERE user_id = :user_id AND class_day = :day AND class_name = :class_name AND class_time = :time");
    $check->execute([
        ':user_id' => $user_id,
        ':day' => $day,
        ':class_name' => $class_name,
        ':time' => $time
    ]);

    if ($check->rowCount() === 0) {
        $stmt = $conn->prepare("
            INSERT INTO class_bookings 
            (user_id, class_day, class_name, class_time, instructor, booked_at) 
            VALUES 
            (:user_id, :day, :class_name, :time, :instructor, NOW())
        ");
        $stmt->execute([
            ':user_id' => $user_id,
            ':day' => $day,
            ':class_name' => $class_name,
            ':time' => $time,
            ':instructor' => $instructor
        ]);
    }

    // Redirect to manage bookings page
    header("Location: manage_bookings.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Booking - Strength House</title>
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
                <h1>Confirm Booking</h1>
            </div>
            
            <div class="schedule-container">
                <?php if(isset($_GET['day'], $_GET['class'], $_GET['time'], $_GET['instructor'])): ?>
                <form method="post" class="booking-form">
                    <input type="hidden" name="day" value="<?php echo htmlspecialchars($_GET['day']); ?>">
                    <input type="hidden" name="class_name" value="<?php echo htmlspecialchars($_GET['class']); ?>">
                    <input type="hidden" name="time" value="<?php echo htmlspecialchars($_GET['time']); ?>">
                    <input type="hidden" name="instructor" value="<?php echo htmlspecialchars($_GET['instructor']); ?>">
                    
                    <p>Booking: <strong><?php echo htmlspecialchars($_GET['class']); ?></strong> on <?php echo htmlspecialchars($_GET['day']); ?> at <?php echo htmlspecialchars($_GET['time']); ?> with <?php echo htmlspecialchars($_GET['instructor']); ?></p>
                    <button type="submit" name="book_class" class="btn btn-small">Confirm Booking</button>
                    <a href="schedule.php" class="btn btn-small">Exit</a>
                </form>
                <?php else: ?>
                    <p>No class selected.</p>
                    <a href="schedule.php" class="btn btn-small">Back to Schedule</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
