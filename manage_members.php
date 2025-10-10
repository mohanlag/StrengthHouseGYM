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

// Handle add member
if (isset($_POST['add_member'])) {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $plan = trim($_POST['plan']);
    $goals = trim($_POST['goals']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if (!empty($fullname) && !empty($email) && !empty($phone) && !empty($plan) && !empty($password)) {
        try {
            $stmt = $conn->prepare("INSERT INTO users (fullname, email, phone, plan, goals, password, role, created_at) VALUES (?, ?, ?, ?, ?, ?, 'member', NOW())");
            $stmt->execute([$fullname, $email, $phone, $plan, $goals, $password]);
            $success = "Member added successfully.";
        } catch (Exception $e) {
            $error = "Error adding member: " . $e->getMessage();
        }
    } else {
        $error = "All fields are required.";
    }
}

// Handle delete member
if (isset($_POST['delete_member'])) {
    $memberId = (int)$_POST['member_id'];
    try {
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

// Fetch members
$members = [];
try {
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
  <h2>Manage Members</h2>

  <?php if ($success): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
  <?php endif; ?>
  <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <!-- Add New Member Form -->
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

  <!-- Members List -->
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
              <td><?= htmlspecialchars($m['fullname']) ?></td>
              <td><?= htmlspecialchars($m['email']) ?></td>
              <td><?= htmlspecialchars($m['phone']) ?></td>
              <td><?= htmlspecialchars(ucfirst($m['plan'])) ?></td>
              <td><?= htmlspecialchars(substr($m['goals'], 0, 50)) ?>...</td>
              <td><?= date('Y-m-d', strtotime($m['created_at'])) ?></td>
              <td>
                <a href="#" class="btn">Edit</a>
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
      <p class="empty">No members found.</p>
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