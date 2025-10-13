<?php
session_start();

// Only trainers can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainer') {
    header("Location: login.php");
    exit();
}

include 'db.php';

try {
    $stmt = $conn->prepare("SELECT * FROM classes WHERE trainer_id = ? ORDER BY class_date, class_time");
    $stmt->execute([$_SESSION['user_id']]);
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Classes - Strength House</title>
<link rel="stylesheet" href="css/style_dashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="dashboard-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <h2>Strength<span>House</span></h2>
            <p class="role-badge">Trainer</p>
        </div>
        <ul class="nav-links">
            <li><a href="trainer_dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
            <li><a href="trainer_dashboard.php#create-class" onclick="showSection('create-class')"><i class="fas fa-plus-circle"></i> <span>Create Class</span></a></li>
            <li><a href="my_classes.php#my-class" onclick="showSection('my_classes')"><i class="fas fa-dumbbell"></i> <span>My Classes</span></a></li>
            <li class="logout-item"><a href="logout.php" class="logout-link" onclick="return confirmLogout()"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>My Classes</h1>
        </div>

        <div class="classes-container">
            <?php if (empty($classes)): ?>
                <p>No classes created yet.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Class Name</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Duration</th>
                            <th>Max Capacity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($classes as $class): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($class['class_name']); ?></td>
                            <td><?php echo htmlspecialchars($class['class_type']); ?></td>
                            <td><?php echo date('d M Y', strtotime($class['class_date'])); ?></td>
                            <td><?php echo date('g:i A', strtotime($class['class_time'])); ?></td>
                            <td><?php echo htmlspecialchars($class['duration']); ?> mins</td>
                            <td><?php echo htmlspecialchars($class['max_capacity']); ?></td>
                            <td>
                                <a href="edit_class.php?id=<?php echo $class['id']; ?>" class="btn btn-small">Edit</a>
                                <a href="delete_class.php?id=<?php echo $class['id']; ?>" class="btn btn-small" style="background: #e74c3c;" onclick="return confirm('Delete this class?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="form-actions" style="margin-top:20px;">
            <a href="trainer_dashboard.php" class="btn btn-outline">Back to Dashboard</a>
        </div>
    </div>
</div>
</body>
</html>
