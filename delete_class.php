<?php
session_start();
require_once 'db.php';

// Only trainers can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainer') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("No class ID provided.");
}

$trainer_id = $_SESSION['user_id'];
$class_id = $_GET['id'];

// Delete the class
$stmt = $conn->prepare("DELETE FROM classes WHERE id = ? AND trainer_id = ?");
$stmt->execute([$class_id, $trainer_id]);

header("Location: my_classes.php?success=Class deleted successfully");
exit();
?>
