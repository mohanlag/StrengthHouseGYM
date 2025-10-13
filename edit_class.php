<?php
session_start();
require_once 'db.php';

// Only trainers can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainer') {
    header("Location: login.php");
    exit();
}

$trainer_id = $_SESSION['user_id'];

// Make sure a class ID is provided
if (!isset($_GET['id'])) {
    die("No class ID provided.");
}

$class_id = $_GET['id'];

// Fetch the class details
$stmt = $conn->prepare("SELECT * FROM classes WHERE id = ? AND trainer_id = ?");
$stmt->execute([$class_id, $trainer_id]);
$class = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$class) {
    die("Class not found or unauthorized access.");
}

// Update class if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = $_POST['class_name'];
    $class_type = $_POST['class_type'];
    $class_date = $_POST['class_date'];
    $class_time = $_POST['class_time'];
    $duration = $_POST['duration'];
    $max_capacity = $_POST['max_capacity'];

    $update = $conn->prepare("UPDATE classes 
        SET class_name=?, class_type=?, class_date=?, class_time=?, duration=?, max_capacity=? 
        WHERE id=? AND trainer_id=?");
    $update->execute([$class_name, $class_type, $class_date, $class_time, $duration, $max_capacity, $class_id, $trainer_id]);

    header("Location: my_classes.php?success=Class updated successfully");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Class</title>
<link rel="stylesheet" href="css/style_dashboard.css">
<link rel="stylesheet" href="css/admin_panel.css">
<link rel="stylesheet" href="css/style1.css">
</head>
<body>
<div class="dashboard-container">
    <div class="main-content">
        <h2>Edit Class</h2>
        <form method="POST" class="form-container">
            <label>Class Name</label>
            <input type="text" name="class_name" value="<?= htmlspecialchars($class['class_name']) ?>" required>

            <label>Type</label>
            <input type="text" name="class_type" value="<?= htmlspecialchars($class['class_type']) ?>" required>

            <label>Date</label>
            <input type="date" name="class_date" value="<?= htmlspecialchars($class['class_date']) ?>" required>

            <label>Time</label>
            <input type="time" name="class_time" value="<?= htmlspecialchars($class['class_time']) ?>" required>

            <label>Duration (minutes)</label>
            <input type="number" name="duration" value="<?= htmlspecialchars($class['duration']) ?>" required>

            <label>Max Capacity</label>
            <input type="number" name="max_capacity" value="<?= htmlspecialchars($class['max_capacity']) ?>" required>

            <button type="submit" class="btn">Save Changes</button>
            <a href="my_classes.php" class="btn btn-outline">Cancel</a>
        </form>
    </div>
</div>
</body>
</html>
