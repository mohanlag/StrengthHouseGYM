<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutrition - Strength House</title>
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
                <li><a href="progress.php"><i class="fas fa-chart-line"></i> <span>Progress</span></a></li>
                <li><a href="nutrition.php" class="active"><i class="fas fa-apple-alt"></i> <span>Nutrition</span></a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="header">
                <h1>Nutrition Tracking</h1>
            </div>
            
            <div class="nutrition-grid">
                <div class="nutrition-card">
                    <h3>Today's Intake</h3>
                    <div class="nutrition-stats">
                        <div class="nutrient">
                            <h4>Calories</h4>
                            <p>1,850 / 2,200</p>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 84%"></div>
                            </div>
                        </div>
                        <div class="nutrient">
                            <h4>Protein</h4>
                            <p>120g / 150g</p>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 80%"></div>
                            </div>
                        </div>
                        <div class="nutrient">
                            <h4>Carbs</h4>
                            <p>210g / 250g</p>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 84%"></div>
                            </div>
                        </div>
                        <div class="nutrient">
                            <h4>Fat</h4>
                            <p>55g / 70g</p>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 78%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="nutrition-card">
                    <h3>Meal Plans</h3>
                    <div class="meal-list">
                        <div class="meal-item">
                            <h4>Breakfast</h4>
                            <p>Oatmeal with fruits - 350 cal</p>
                        </div>
                        <div class="meal-item">
                            <h4>Lunch</h4>
                            <p>Grilled chicken salad - 450 cal</p>
                        </div>
                        <div class="meal-item">
                            <h4>Dinner</h4>
                            <p>Salmon with vegetables - 550 cal</p>
                        </div>
                        <div class="meal-item">
                            <h4>Snacks</h4>
                            <p>Protein shake & nuts - 500 cal</p>
                        </div>
                    </div>
                    <button class="btn" style="margin-top: 15px; width: 100%;">View All Meal Plans</button>
                </div>
                
                <div class="nutrition-card">
                    <h3>Log Food</h3>
                    <form class="food-form">
                        <input type="text" placeholder="Food item" class="form-control">
                        <input type="number" placeholder="Calories" class="form-control">
                        <input type="number" placeholder="Protein (g)" class="form-control">
                        <input type="number" placeholder="Carbs (g)" class="form-control">
                        <input type="number" placeholder="Fat (g)" class="form-control">
                        <button type="submit" class="btn">Add Food</button>
                    </form>
                </div>
            </div>
        </div>