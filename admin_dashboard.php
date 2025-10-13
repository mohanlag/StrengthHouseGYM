<?php
// Start session for user authentication
session_start();

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Include database connection file
include 'db.php';

// ==========================
// Fetch Admin Profile
// ==========================
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// ==========================
// Dashboard Statistics
// ==========================

// Total number of members
$total_members = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='member'")->fetchColumn();

// Total number of trainers
$total_trainers = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='trainer'")->fetchColumn();

// Total number of gym classes
$total_classes = $conn->query("SELECT COUNT(*) as count FROM classes")->fetchColumn();

// Total number of admins
$total_admins = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='admin'")->fetchColumn();

// ==========================
// Recent Contact Messages
// ==========================
$contact_messages = [];
try {
    // Get latest 10 contact messages from users
    $stmt = $conn->prepare("
        SELECT id, name, email, subject, message, created_at, status 
        FROM contact_messages 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $stmt->execute();
    $contact_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Handle case where table might not exist yet
    $contact_messages = [];
}

// ==========================
// Admin Booking Management
// ==========================

// Assign class to member
if (isset($_POST['assign_class'])) {
    $member_id = $_POST['member_id'];
    $class_name = $_POST['class_name'];
    $day = $_POST['class_day'];
    $time = $_POST['class_time'];
    $instructor = $_POST['instructor'];

    // Prevent duplicate booking
    $check = $conn->prepare("SELECT * FROM class_bookings WHERE user_id = :user_id AND class_day = :day AND class_name = :class_name AND class_time = :time");
    $check->execute([
        ':user_id' => $member_id,
        ':day' => $day,
        ':class_name' => $class_name,
        ':time' => $time
    ]);

    if ($check->rowCount() === 0) {
        $stmt = $conn->prepare("INSERT INTO class_bookings (user_id, class_day, class_name, class_time, instructor, booked_at) VALUES (:user_id, :day, :class_name, :time, :instructor, NOW())");
        $stmt->execute([
            ':user_id' => $member_id,
            ':day' => $day,
            ':class_name' => $class_name,
            ':time' => $time,
            ':instructor' => $instructor
        ]);
    }
}

// Cancel booking
if (isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];
    $stmt = $conn->prepare("DELETE FROM class_bookings WHERE id = :id");
    $stmt->execute([':id' => $booking_id]);
}

// ==========================
// Recent Members & Trainers
// ==========================

// Recent 5 members
$recent_members = $conn->query("SELECT fullname, email, created_at FROM users WHERE role='member' ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Recent 5 trainers
$recent_trainers = $conn->query("SELECT fullname, email, created_at FROM users WHERE role='trainer' ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - Strength House Gym</title>

<!-- External Stylesheets -->
<link rel="stylesheet" href="css/admin_panel.css">
<link rel="stylesheet" href="css/style1.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>
<!-- ===============================
     Header Section
     =============================== -->
<header>
  <div class="container">
    <h1>Strength House Gym</h1>
    <p class="meta">Welcome back, <?= htmlspecialchars($admin['fullname']) ?>!</p>

    <!-- Admin Navigation Links -->
    <nav>
      <a href="admin_dashboard.php">Dashboard</a> |
      <a href="manage_members.php">Manage Members</a> |
      <a href="manage_trainers.php">Manage Trainers</a> |
      <a href="admin_bookings">Bookings</a> |
      <a href="logout.php">Logout</a>
    </nav>
  </div>
</header>

<!-- ===============================
     Main Dashboard Content
     =============================== -->
<main class="container">
  <h2>Admin Dashboard</h2>
  <p class="meta">Role: <strong>Administrator</strong></p>

  <!-- Dashboard Statistics Cards -->
  <div class="stats-grid">
    <div class="stat-card">
      <i class="fas fa-users"></i>
      <h3><?= $total_members ?></h3>
      <p>Total Members</p>
    </div>
    <div class="stat-card">
      <i class="fas fa-dumbbell"></i>
      <h3><?= $total_trainers ?></h3>
      <p>Total Trainers</p>
    </div>
    <div class="stat-card">
      <i class="fas fa-calendar"></i>
      <h3><?= $total_classes ?></h3>
      <p>Total Classes</p>
    </div>
    <div class="stat-card">
      <i class="fas fa-user-shield"></i>
      <h3><?= $total_admins ?></h3>
      <p>Administrators</p>
    </div>
  </div>

  <!-- ===============================
       Section Cards
       =============================== -->
  <div class="sections">

    <!-- Admin Profile -->
    <section class="card">
      <h3><i class="fas fa-user-shield"></i> Admin Profile</h3>
      <?php if ($admin): ?>
        <div class="grid-2">
          <div><span class="label">Name:</span> <div class="value"><?= htmlspecialchars($admin['fullname']) ?></div></div>
          <div><span class="label">Email:</span> <div class="value"><?= htmlspecialchars($admin['email']) ?></div></div>
          <div><span class="label">Phone:</span> <div class="value"><?= htmlspecialchars($admin['phone'] ?? 'Not set') ?></div></div>
          <div><span class="label">Role:</span> <div class="value"><?= htmlspecialchars(ucfirst($admin['role'])) ?></div></div>
          <div style="grid-column:1 / -1;">
            <span class="label">Admin Since:</span>
            <div class="value"><?= htmlspecialchars($admin['created_at'] ?? 'Unknown') ?></div>
          </div>
        </div>
      <?php else: ?>
        <p class="empty">No admin profile found.</p>
      <?php endif; ?>
    </section>

    <!-- Recent Members -->
    <section class="card">
      <h3><i class="fas fa-user-plus"></i> Recent Members</h3>
      <?php if (!empty($recent_members)): ?>
        <table>
          <thead>
            <tr><th>Name</th><th>Email</th><th>Joined</th></tr>
          </thead>
          <tbody>
            <?php foreach ($recent_members as $member): ?>
              <tr>
                <td><?= htmlspecialchars($member['fullname']) ?></td>
                <td><?= htmlspecialchars($member['email']) ?></td>
                <td><?= date('M j, Y', strtotime($member['created_at'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="empty">No members registered yet.</p>
      <?php endif; ?>
      <div class="message-actions">
        <a href="manage_members.php" class="btn btn-small btn-success">View All Members</a>
        <a href="manage_members.php?action=add" class="btn btn-small">Add Member</a>
      </div>
    </section>

    <!-- Recent Trainers -->
    <section class="card">
      <h3><i class="fas fa-dumbbell"></i> Recent Trainers</h3>
      <?php if (!empty($recent_trainers)): ?>
        <table>
          <thead>
            <tr><th>Name</th><th>Email</th><th>Joined</th></tr>
          </thead>
          <tbody>
            <?php foreach ($recent_trainers as $trainer): ?>
              <tr>
                <td><?= htmlspecialchars($trainer['fullname']) ?></td>
                <td><?= htmlspecialchars($trainer['email']) ?></td>
                <td><?= date('M j, Y', strtotime($trainer['created_at'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="empty">No trainers registered yet.</p>
      <?php endif; ?>
      <div class="message-actions">
        <a href="manage_trainers.php" class="btn btn-small btn-warning">View All Trainers</a>
        <a href="manage_trainers.php?action=add" class="btn btn-small">Add Trainer</a>
      </div>
    </section>

    <!-- Contact Messages -->
    <section class="card">
      <h3><i class="fas fa-envelope"></i> Website Contact Messages</h3>
      <?php 
      // Fetch total and unread message counts
      $total_messages = $conn->query("SELECT COUNT(*) as count FROM contact_messages")->fetchColumn();
      $unread_messages = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'pending'")->fetchColumn();
      ?>
      
      <div style="display:flex;justify-content:space-between;margin-bottom:1rem;">
        <div>
          <strong>Total Messages:</strong> <?= $total_messages ?> | 
          <strong>Unread:</strong> <span style="color:#e74c3c;"><?= $unread_messages ?></span>
        </div>
        <a href="manage_messages.php" class="btn btn-small">Manage All Messages</a>
      </div>

      <?php if (!empty($contact_messages)): ?>
        <table>
          <thead>
            <tr><th>Name</th><th>Email</th><th>Subject</th><th>Preview</th><th>Date</th><th>Status</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <?php foreach ($contact_messages as $message): ?>
              <tr style="<?= $message['status'] === 'pending' ? 'background: #fff3cd;' : '' ?>">
                <td><strong><?= htmlspecialchars($message['name']) ?></strong></td>
                <td><?= htmlspecialchars($message['email']) ?></td>
                <td><?= htmlspecialchars($message['subject']) ?></td>
                <td title="<?= htmlspecialchars($message['message']) ?>">
                    <?= nl2br(htmlspecialchars(substr($message['message'], 0, 30) . (strlen($message['message']) > 30 ? '...' : ''))) ?>
                </td>
                <td><?= date('M j, g:i A', strtotime($message['created_at'])) ?></td>
                <td><span class="status-<?= $message['status'] === 'read' ? 'read' : 'pending' ?>"><?= ucfirst($message['status']) ?></span></td>
                <td><a href="view_message.php?id=<?= $message['id'] ?>" class="btn btn-small" style="padding:0.25rem 0.5rem;font-size:0.7rem;">View</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="empty">No contact messages received yet.</p>
      <?php endif; ?>
    </section>

    <!-- Manage Bookings Section -->
<section class="card">
    <h3><i class="fas fa-calendar-alt"></i> Manage Bookings</h3>

    <?php
    // Fetch all bookings with member info
    $bookings = $conn->query("
        SELECT cb.id, cb.user_id, cb.class_day, cb.class_name, cb.class_time, cb.instructor, cb.booked_at, u.fullname AS member_name
        FROM class_bookings cb
        JOIN users u ON cb.user_id = u.id
        ORDER BY cb.booked_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all members for assigning classes
    $members = $conn->query("SELECT id, fullname FROM users WHERE role='member' ORDER BY fullname")->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <!-- Assign Class Form -->
    <div class="schedule-day" style="margin-bottom:1.5rem;">
        <h4>Assign Class to Member</h4>
        <form method="post" style="display:flex;gap:0.5rem;flex-wrap:wrap;">
            <select name="member_id" required>
                <option value="">Select Member</option>
                <?php foreach($members as $member): ?>
                    <option value="<?= $member['id'] ?>"><?= htmlspecialchars($member['fullname']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="class_name" placeholder="Class Name" required>
            <input type="text" name="class_day" placeholder="Day (e.g., Monday)" required>
            <input type="text" name="class_time" placeholder="Time (e.g., 6:00 AM)" required>
            <input type="text" name="instructor" placeholder="Instructor Name" required>
            <button type="submit" name="assign_class" class="btn btn-small">Assign Class</button>
        </form>
    </div>

    <!-- Existing Bookings Table -->
    <?php if (!empty($bookings)): ?>
    <table>
        <thead>
            <tr>
                <th>Member</th>
                <th>Class</th>
                <th>Day</th>
                <th>Time</th>
                <th>Instructor</th>
                <th>Booked At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($bookings as $b): ?>
            <tr>
                <td><?= htmlspecialchars($b['member_name']) ?></td>
                <td><?= htmlspecialchars($b['class_name']) ?></td>
                <td><?= htmlspecialchars($b['class_day']) ?></td>
                <td><?= htmlspecialchars($b['class_time']) ?></td>
                <td><?= htmlspecialchars($b['instructor']) ?></td>
                <td><?= date('M j, Y g:i A', strtotime($b['booked_at'])) ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                        <button type="submit" name="cancel_booking" class="btn btn-small" style="background:#e74c3c;">Cancel</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p class="empty">No bookings yet.</p>
    <?php endif; ?>
</section>

  </div>
</main>

<!-- ===============================
     Footer Section
     =============================== -->
<footer>
  <div class="container">
    <p>&copy; <?= date("Y"); ?> Strength House Gym. All rights reserved.</p>
  </div>
</footer>
</body>
</html>
