<?php
// =================== Session & Access Control ===================
// Start session to track logged-in users
session_start();

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Redirect non-admin users to login page
    header("Location: login.php");
    exit();
}

// =================== Database Connection ===================
include 'db.php'; // Include database connection file

// =================== Admin Details ===================
$adminId = (int)$_SESSION['user_id'];  // Cast user ID to integer for security
$adminName = $_SESSION['name'] ?? 'Admin'; // Default name if session variable not set

// =================== Feedback Messages ===================
$success = '';
$error = '';

// =================== Handle Add Trainer ===================
if (isset($_POST['add_trainer'])) {
    // Sanitize and trim user input
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $specialty = trim($_POST['specialty']);
    $experience = trim($_POST['experience']);
    $schedule = trim($_POST['schedule']);
    
    // Hash password before storing
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Validate required fields
    if (!empty($fullname) && !empty($email) && !empty($phone) && !empty($specialty) && !empty($experience) && !empty($password)) {
        try {
            // Insert into users table for login credentials (role = 'trainer')
            $stmt = $conn->prepare("INSERT INTO users (fullname, email, phone, password, role, created_at) VALUES (?, ?, ?, ?, 'trainer', NOW())");
            $stmt->execute([$fullname, $email, $phone, $password]);

            // Get last inserted ID for linking with trainers table
            $trainerId = $conn->lastInsertId();

            // Insert into trainers table with specialty, experience, and schedule
            $stmt = $conn->prepare("INSERT INTO trainers (id, specialty, experience, schedule) VALUES (?, ?, ?, ?)");
            $stmt->execute([$trainerId, $specialty, $experience, $schedule]);

            $success = "Trainer added successfully.";
        } catch (Exception $e) {
            // Catch any database errors
            $error = "Error adding trainer: " . $e->getMessage();
        }
    } else {
        $error = "All fields are required.";
    }
}

// =================== Handle Delete Trainer ===================
if (isset($_POST['delete_trainer'])) {
    $trainerId = (int)$_POST['trainer_id']; // Cast ID to integer for security
    try {
        // Delete trainer info from trainers table
        $stmt = $conn->prepare("DELETE FROM trainers WHERE id = ?");
        $stmt->execute([$trainerId]);

        // Delete login info from users table (role must be 'trainer')
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

// =================== Fetch All Trainers ===================
$trainers = [];
try {
    // Fetch trainers info along with their specialty, experience, and schedule
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

// =================== Helper Function ===================
// Function to safely display values and optionally truncate text
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

<!-- Main Stylesheets -->
<link rel="stylesheet" href="css/style1.css">
<link rel="stylesheet" href="css/admin.css">
</head>
<body>

<!-- =================== Header =================== -->
<header>
  <div class="container">
    <h1>Strength House Gym - Admin</h1>
    <!-- Display logged-in admin name -->
    <p class="meta">Welcome back, <?= htmlspecialchars($adminName) ?>!</p>
    <!-- Navigation menu -->
    <nav>
      <a href="admin_dashboard.php">Dashboard</a> |
      <a href="manage_members.php">Manage Members</a> |
      <a href="manage_trainers.php">Manage Trainers</a> |
      <a href="admin_bookings">Bookings</a> |
      <a href="logout.php">Logout</a>
    </nav>
  </div>
</header>

<!-- =================== Main Content =================== -->
<main class="container">
  <h2>Manage Trainers</h2>

  <!-- Display success message -->
  <?php if ($success): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
  <?php endif; ?>

  <!-- Display error message -->
  <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <!-- =================== Add New Trainer Form =================== -->
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

  <!-- =================== Trainers List =================== -->
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
              <!-- Display trainer info safely -->
              <td><?= safeDisplay($t['fullname']) ?></td>
              <td><?= safeDisplay($t['email']) ?></td>
              <td><?= safeDisplay($t['phone']) ?></td>
              <td><?= safeDisplay($t['specialty']) ?></td>
              <td><?= safeDisplay($t['experience']) ?></td>
              <td><?= safeDisplay($t['schedule'], 50) ?></td>
              <td>
                <!-- Edit button (can link to edit_trainer.php) -->
                <a href="#" class="btn">Edit</a>
                <!-- Delete button with confirmation -->
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
      <!-- Display if no trainers exist -->
      <p class="empty">No trainers found.</p>
    <?php endif; ?>
  </section>
</main>

<!-- =================== Footer =================== -->
<footer>
  <div class="container">
    <p>&copy; <?= date("Y"); ?> Strength House Gym. All rights reserved.</p>
  </div>
</footer>
</body>
</html>
