<?php
// db.php - make sure you include this
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $food_name = $_POST['food_name'];
    $calories = $_POST['calories'];
    $protein = $_POST['protein'];
    $carbs = $_POST['carbs'];
    $fat = $_POST['fat'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO food_logs (user_id, food_name, calories, protein, carbs, fat) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isiiii", $user_id, $food_name, $calories, $protein, $carbs, $fat);
    $stmt->execute();
    $stmt->close();

    header("Location: nutrition.php"); // refresh page after submission
    exit();
}
?>
