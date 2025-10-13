<?php
// =================== Session and Access Control ===================
// Start the session to track logged-in admin
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Redirect non-admin users to login page
    header("Location: login.php");
    exit();
}

// =================== Database Connection ===================
include 'db.php'; // Include database connection

// =================== Admin Details ===================
$adminId = (int)$_SESSION['user_id'];  // Ensure the ID is integer for security
$adminName = $_SESSION['name'] ?? 'Admin'; // Default name if session variable missing

// =================== Feedback Messages ===================
$success = '';
$error = '';

// =================== Handle Add Member ===================
if (isset($_POST['add_member'])) {
    // Sanitize and trim input values
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $plan = trim($_POST['plan']);
    $goals = trim($_POST['goals']);
    
    // Hash the password securely before storing
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if all required fields are filled
    if (!empty($fullname) && !empty($email) && !empty($phone) && !empty($plan) && !empty($password)) {
        try {
            // Insert new member into database with role 'member'
            $stmt = $conn->prepare("
                INSERT INTO users 
                (fullname, email, phone, plan, goals, password, role, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 'member', NOW())
            ");
            $stmt->execute([$fullname, $email, $phone, $plan, $goals, $password]);
            $success = "Member added successfully.";
        } catch (Exception $e) {
            // Handle any database errors
            $error = "Error adding member: " . $e->getMessage();
        }
    } else {
        $error = "All fields are required.";
    }
}

// =================== Handle Delete Member ===================
if (isset($_POST['delete_member'])) {
    $memberId = (int)$_POST['member_id']; // Cast to integer for security

    try {
        // Delete member from database (only if role is 'member')
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'member'");
        $stmt->execute([$memberId]);

        if ($stmt->rowCount() > 0) {
            $success = "Member deleted successfully.";
        } else {
            $error = "Member not found.";
        }
    } catch (Exception $e) {
        $error = "Error deleting member: " . $e->getMessage();
    }
}

// =================== Fetch All Members ===================
$members = [];
try {
    // Select all members ordered by newest first
    $stmt = $conn->prepare("
        SELECT id, fullname, email, phone, plan, goals, created_at 
        FROM users 
        WHERE role = 'member' 
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $members = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Members - Strength House Gym</title>

<!-- Main styles -->
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
    <!-- Admin navigation menu -->
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
  <h2>Manage Members</h2>

  <!-- Display success message -->
  <?php if ($success): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
  <?php endif; ?>

  <!-- Display error message -->
  <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <!-- =================== Add New Member Form =================== -->
  <section class="card">
    <h3>Add New Member</h3>
    <form method="POST">
      <input type="text" name="fullname" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="tel" name="phone" placeholder="Phone" required>
      <select name="plan" required>
        <option value="">Select Plan</option>
        <option value="basic">Basic</option>
        <option value="premium">Premium</option>
        <option value="elite">Elite</option>
      </select>
      <textarea name="goals" placeholder="Fitness Goals"></textarea>
      <input type="password" name="password" placeholder="Password (will be hashed)" required>
      <button type="submit" name="add_member" class="btn">Add Member</button>
    </form>
  </section>

  <!-- =================== Members List =================== -->
  <section class="card">
    <h3>Members List (<?= count($members) ?>)</h3>
    
    <?php if (!empty($members)): ?>
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Plan</th>
            <th>Goals</th>
            <th>Joined</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($members as $m): ?>
            <tr>
              <!-- Display member info -->
              <td><?= htmlspecialchars($m['fullname']) ?></td>
              <td><?= htmlspecialchars($m['email']) ?></td>
              <td><?= htmlspecialchars($m['phone']) ?></td>
              <td><?= htmlspecialchars(ucfirst($m['plan'])) ?></td>
              <td><?= htmlspecialchars(substr($m['goals'], 0, 50)) ?>...</td>
              <td><?= date('Y-m-d', strtotime($m['created_at'])) ?></td>
              <td>
                <!-- Edit button (can link to edit_member.php) -->
                <a href="#" class="btn">Edit</a>
                <!-- Delete button with confirmation -->
                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this member?');">
                  <input type="hidden" name="member_id" value="<?= $m['id'] ?>">
                  <button type="submit" name="delete_member" class="btn btn-danger">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <!-- Display if no members exist -->
      <p class="empty">No members found.</p>
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
