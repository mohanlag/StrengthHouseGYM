<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM bookings WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Bookings - Strength House</title>
<link rel="stylesheet" href="css/style_dashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="dashboard-container">
    <div class="sidebar">
        <div class="logo">
            <h2>Strength<span>House</span></h2>
        </div>
        <ul class="nav-links">
            <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="schedule.php"><i class="fas fa-calendar-alt"></i> Schedule</a></li>
            <li><a href="my_bookings.php" class="active"><i class="fas fa-list"></i> My Bookings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="header">
            <h1>My Booked Classes</h1>
        </div>
        <div class="content">
            <?php if ($result->num_rows > 0): ?>
                <table class="table">
                    <tr>
                        <th>Class Name</th>
                        <th>Day</th>
                        <th>Time</th>
                        <th>Instructor</th>
                        <th>Booked On</th>
                    </tr>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['class_name']; ?></td>
                        <td><?php echo $row['class_day']; ?></td>
                        <td><?php echo $row['class_time']; ?></td>
                        <td><?php echo $row['instructor']; ?></td>
                        <td><?php echo $row['booking_date']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>No classes booked yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
