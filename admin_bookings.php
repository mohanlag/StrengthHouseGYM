<?php
session_start();
include 'db.php';

// Only admins can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Cancel booking if requested
if (isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];
    $stmt = $conn->prepare("DELETE FROM class_bookings WHERE id = :id");
    $stmt->execute([':id' => $booking_id]);
}

// Fetch all bookings with member info
$stmt = $conn->prepare("
    SELECT cb.*, u.fullname AS member_name, u.email
    FROM class_bookings cb
    JOIN users u ON cb.user_id = u.id
    ORDER BY cb.booked_at DESC
");
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>All Bookings - Admin</title>
<link rel="stylesheet" href="css/style1.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}
table th, table td {
    padding: 0.75rem;
    border: 1px solid #ccc;
    text-align: left;
}
table th {
    background-color: #2c3e50;
    color: white;
}
.btn-cancel {
    background-color: #e74c3c;
    color: white;
    border: none;
    padding: 0.3rem 0.6rem;
    cursor: pointer;
    border-radius: 3px;
}
.btn-cancel:hover {
    background-color: #c0392b;
}
</style>
</head>
<body>
<header>
  <div class="container">
    <h1>Strength House Gym</h1>
    <p class="meta">Administrator View</p>
    <nav>
      <a href="admin_dashboard.php">Dashboard</a> |
      <a href="admin_bookings.php" class="active">View Bookings</a> |
      <a href="logout.php">Logout</a>
    </nav>
  </div>
</header>

<main class="container">
  <h2>All Class Bookings</h2>

  <?php if (!empty($bookings)): ?>
  <table>
    <thead>
      <tr>
        <th>Member Name</th>
        <th>Email</th>
        <th>Class</th>
        <th>Day</th>
        <th>Time</th>
        <th>Instructor</th>
        <th>Booked On</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($bookings as $b): ?>
      <tr>
        <td><?= htmlspecialchars($b['member_name']) ?></td>
        <td><?= htmlspecialchars($b['email']) ?></td>
        <td><?= htmlspecialchars($b['class_name']) ?></td>
        <td><?= htmlspecialchars($b['class_day']) ?></td>
        <td><?= htmlspecialchars($b['class_time']) ?></td>
        <td><?= htmlspecialchars($b['instructor']) ?></td>
        <td><?= date('M j, Y g:i A', strtotime($b['booked_at'])) ?></td>
        <td>
            <form method="post" style="margin:0;">
                <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                <button type="submit" name="cancel_booking" class="btn-cancel">Cancel</button>
            </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?>
    <p>No bookings found.</p>
  <?php endif; ?>

</main>
</body>
</html>
