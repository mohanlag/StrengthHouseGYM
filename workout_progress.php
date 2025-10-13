<?php
session_start();

// Access control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

// Get workout details from query parameters
$workout_name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : 'Workout';
$duration_minutes = isset($_GET['duration']) ? (int)$_GET['duration'] : 30;
$duration_seconds = $duration_minutes * 60; // convert minutes to seconds
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $workout_name; ?> - In Progress</title>
    <link rel="stylesheet" href="css/style_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background: #121212;
            color: #fff;
            font-family: 'Poppins', sans-serif;
        }
        .workout-progress {
            text-align: center;
            background: #1e1e1e;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
            max-width: 500px;
        }
        .timer {
            font-size: 3rem;
            margin: 20px 0;
            color: #00ff88;
        }
        .btn {
            display: inline-block;
            background: #00ff88;
            color: #000;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: bold;
            text-decoration: none;
            transition: 0.3s;
        }
        .btn:hover {
            background: #00cc6a;
        }
    </style>
</head>
<body>
    <div class="workout-progress">
        <h1><?php echo $workout_name; ?></h1>
        <p>Workout in progress...</p>
        <div class="timer" id="timer">00:00</div>
        <a href="workouts.php" class="btn">End Workout</a>
    </div>

    <script>
        // Timer logic
        let totalSeconds = <?php echo $duration_seconds; ?>;
        const timerDisplay = document.getElementById('timer');

        function updateTimer() {
            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;
            timerDisplay.textContent =
                `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            
            if (totalSeconds > 0) {
                totalSeconds--;
            } else {
                clearInterval(timerInterval);
                timerDisplay.textContent = "Workout Complete!";
            }
        }

        const timerInterval = setInterval(updateTimer, 1000);
        updateTimer(); // initial call
    </script>
</body>
</html>
