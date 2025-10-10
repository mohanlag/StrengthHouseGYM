<?php
session_start();
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

$adminId = (int)$_SESSION['user_id'];
$adminName = $_SESSION['name'] ?? 'Admin';

$success = '';
$error = '';

// Handle add trainer
if (isset($_POST['add_trainer'])) {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $specialty = trim($_POST['specialty']);
    $experience = trim($_POST['experience']);
    $schedule = trim($_POST['schedule']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if (!empty($fullname) && !empty($email) && !empty($phone) && !empty($specialty) && !empty($experience) && !empty($password)) {
        try {
            // Insert into users for login (role='trainer')
            $stmt = $conn->prepare("INSERT INTO users (fullname, email, phone, password, role, created_at) VALUES (?, ?, ?, ?, 'trainer', NOW())");
            $stmt->execute([$fullname, $email, $phone, $password]);
            $trainerId = $conn->lastInsertId();

            // Insert into trainers
            $stmt = $conn->prepare("INSERT INTO trainers (id, specialty, experience, schedule) VALUES (?, ?, ?, ?)");
            $stmt->execute([$trainerId, $specialty, $experience, $schedule]);
            $success = "Trainer added successfully.";
        } catch (Exception $e) {
            $error = "Error adding trainer: " . $e->getMessage();
        }
    } else {
        $error = "All fields are required.";
    }
}

// Handle delete trainer
if (isset($_POST['delete_trainer'])) {
    $trainerId = (int)$_POST['trainer_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM trainers WHERE id = ?");
        $stmt->execute([$trainerId]);
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'trainer'");
        $stmt->execute([$trainerId]);
        if ($stmt->rowCount() > 0) {
            $success = "Trainer deleted successfully.";
        } else {
            $error = "Trainer not found.";
        }
    } catch (Exception $e) {
        $error = "Error deleting trainer: " . $e->getMessage();
    }
}

// Fetch trainers
$trainers = [];
try {
    $stmt = $conn->prepare("
        SELECT t.id, t.fullname, t.email, t.phone, tr.specialty, tr.experience, tr.schedule 
        FROM users t 
        LEFT JOIN trainers tr ON t.id = tr.id 
        WHERE t.role = 'trainer' 
        ORDER BY t.created_at DESC
    ");
    $stmt->execute();
    $trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $trainers = [];
}

// Helper function to safely display values
function safeDisplay($value, $maxLength = null) {
    if ($value === null) {
        return '';
    }
    if ($maxLength !== null && is_string($value)) {
        $value = substr($value, 0, $maxLength) . (strlen($value) > $maxLength ? '...' : '');
    }
    return htmlspecialchars($value);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Trainers - Strength House Gym</title>
<link rel="stylesheet" href="css/style1.css">
<link rel="stylesheet" href="css/admin.css">
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
      <a href="schedule.html">Schedule</a> |
      <a href="logout.php">Logout</a>
    </nav>
  </div>
</header>

<main class="container">
  <h2>Manage Trainers</h2>

  <?php if ($success): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
  <?php endif; ?>
  <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <!-- Add New Trainer Form -->
  <section class="card">
    <h3>Add New Trainer</h3>
    <form method="POST">
      <input type="text" name="fullname" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="tel" name="phone" placeholder="Phone" required>
      <input type="text" name="specialty" placeholder="Specialty" required>
      <input type="text" name="experience" placeholder="Years of Experience" required>
      <textarea name="schedule" placeholder="Schedule (e.g., Mon-Fri 9-5)"></textarea>
      <input type="password" name="password" placeholder="Password (will be hashed)" required>
      <button type="submit" name="add_trainer" class="btn">Add Trainer</button>
    </form>
  </section>

  <!-- Trainers List -->
  <section class="card">
    <h3>Trainers List (<?= count($trainers) ?>)</h3>
    <?php if (!empty($trainers)): ?>
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Specialty</th>
            <th>Experience</th>
            <th>Schedule</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($trainers as $t): ?>
            <tr>
              <td><?= safeDisplay($t['fullname']) ?></td>
              <td><?= safeDisplay($t['email']) ?></td>
              <td><?= safeDisplay($t['phone']) ?></td>
              <td><?= safeDisplay($t['specialty']) ?></td>
              <td><?= safeDisplay($t['experience']) ?></td>
              <td><?= safeDisplay($t['schedule'], 50) ?></td>
              <td>
                <a href="#" class="btn">Edit</a>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this trainer?');">
                  <input type="hidden" name="trainer_id" value="<?= $t['id'] ?>">
                  <button type="submit" name="delete_trainer" class="btn btn-danger">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p class="empty">No trainers found.</p>
    <?php endif; ?>
  </section>
</main>

<footer>
  <div class="container">
    <p>&copy; <?= date("Y"); ?> Strength House Gym. All rights reserved.</p>
  </div>
</footer>
</body>
</html>