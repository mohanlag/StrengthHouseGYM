<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Get admin profile
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Get statistics
$total_members = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='member'")->fetchColumn();
$total_trainers = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='trainer'")->fetchColumn();
$total_classes = $conn->query("SELECT COUNT(*) as count FROM classes")->fetchColumn();
$total_admins = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='admin'")->fetchColumn();

// Get recent contact messages from website
$contact_messages = [];
try {
    $stmt = $conn->prepare("
        SELECT id, name, email, subject, message, created_at, status 
        FROM contact_messages 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $stmt->execute();
    $contact_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Table might not exist yet
    $contact_messages = [];
}

// Get recent members
$recent_members = $conn->query("SELECT fullname, email, created_at FROM users WHERE role='member' ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Get recent trainers
$recent_trainers = $conn->query("SELECT fullname, email, created_at FROM users WHERE role='trainer' ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - Strength House Gym</title>
<link rel="stylesheet" href="css/style1.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root {
    --primary: #2c3e50;
    --secondary: #3498db;
    --accent: #e74c3c;
    --success: #2ecc71;
    --warning: #f39c12;
    --light: #ecf0f1;
    --dark: #2c3e50;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background-color: #f5f7fa;
    color: #333;
    line-height: 1.6;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Header Styles */
header {
    background: var(--primary);
    color: white;
    padding: 1rem 0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

header h1 {
    margin-bottom: 0.5rem;
    font-size: 2rem;
}

.meta {
    color: var(--light);
    margin-bottom: 1rem;
}

nav {
    margin-top: 1rem;
}

nav a {
    color: var(--light);
    text-decoration: none;
    margin: 0 10px;
    transition: color 0.3s ease;
}

nav a:hover {
    color: var(--secondary);
}

/* Main Content */
main {
    padding: 2rem 0;
}

main h2 {
    color: var(--primary);
    margin-bottom: 1rem;
    font-size: 1.8rem;
}

/* Sections Grid */
.sections {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

/* Card Styles */
.card {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    border-left: 4px solid var(--secondary);
}

.card h3 {
    color: var(--primary);
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--light);
    display: flex;
    align-items: center;
    gap: 10px;
}

.card h3 i {
    color: var(--secondary);
}

/* Grid Layouts */
.grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.grid-2 .label {
    font-weight: 600;
    color: var(--primary);
    display: block;
    margin-bottom: 0.25rem;
}

.grid-2 .value {
    color: #666;
    background: var(--light);
    padding: 0.5rem;
    border-radius: 5px;
    min-height: 2.5rem;
    display: flex;
    align-items: center;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin: 2rem 0;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    border-top: 4px solid var(--secondary);
}

.stat-card i {
    font-size: 2rem;
    color: var(--secondary);
    margin-bottom: 0.5rem;
}

.stat-card h3 {
    font-size: 1.8rem;
    color: var(--primary);
    margin-bottom: 0.5rem;
}

/* Table Styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

table th {
    background: var(--primary);
    color: white;
    padding: 0.75rem;
    text-align: left;
}

table td {
    padding: 0.75rem;
    border-bottom: 1px solid var(--light);
}

table tr:hover {
    background: #f8f9fa;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
    padding: 0.25rem 0.5rem;
    border-radius: 3px;
    font-size: 0.8rem;
}

.status-read {
    background: #d4edda;
    color: #155724;
    padding: 0.25rem 0.5rem;
    border-radius: 3px;
    font-size: 0.8rem;
}

/* Empty State */
.empty {
    text-align: center;
    color: #666;
    font-style: italic;
    padding: 2rem;
    background: var(--light);
    border-radius: 5px;
}

/* Buttons */
.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    background: var(--secondary);
    color: white;
    text-decoration: none;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    transition: background 0.3s ease;
}

.btn:hover {
    background: #2980b9;
}

.btn-small {
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
}

.btn-success {
    background: var(--success);
}

.btn-success:hover {
    background: #27ae60;
}

.btn-warning {
    background: var(--warning);
}

.btn-warning:hover {
    background: #e67e22;
}

.btn-danger {
    background: var(--accent);
}

.btn-danger:hover {
    background: #c0392b;
}

/* Footer */
footer {
    background: var(--dark);
    color: white;
    text-align: center;
    padding: 1rem 0;
    margin-top: 2rem;
}

/* Message Actions */
.message-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

/* Responsive */
@media (max-width: 768px) {
    .sections {
        grid-template-columns: 1fr;
    }
    
    .grid-2 {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    table {
        font-size: 0.8rem;
    }
    
    nav {
        text-align: center;
    }
    
    nav a {
        display: inline-block;
        margin: 0 5px 5px 0;
    }
}
</style>
</head>
<body>
<header>
  <div class="container">
    <h1>Strength House Gym</h1>
    <p class="meta">Welcome back, <?= htmlspecialchars($admin['fullname']) ?>!</p>
    <nav>
      
      <a href="admin_dashboard.php">Dashboard</a> |
      <a href="manage_members.php">Manage Members</a> |
      <a href="manage_trainers.php">Manage Trainers</a> |
      <a href="manage_classes.php">Manage Classes</a> |
      <a href="logout.php">Logout</a>
    </nav>
  </div>
</header>

<main class="container">
  <h2>Admin Dashboard</h2>
  <p class="meta">Role: <strong>Administrator</strong></p>

  <!-- Statistics -->
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

  <div class="sections">
    <!-- Admin Profile Section -->
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

    <!-- Recent Members Section -->
    <section class="card">
      <h3><i class="fas fa-user-plus"></i> Recent Members</h3>
      <?php if (!empty($recent_members)): ?>
        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Joined</th>
            </tr>
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

    <!-- Recent Trainers Section -->
    <section class="card">
      <h3><i class="fas fa-dumbbell"></i> Recent Trainers</h3>
      <?php if (!empty($recent_trainers)): ?>
        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Joined</th>
            </tr>
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

    <!-- Contact Messages Section -->
<section class="card">
    <h3><i class="fas fa-envelope"></i> Website Contact Messages</h3>
    <?php 
    // Get contact messages count
    $total_messages = $conn->query("SELECT COUNT(*) as count FROM contact_messages")->fetchColumn();
    $unread_messages = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'pending'")->fetchColumn();
    ?>
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <div>
            <strong>Total Messages:</strong> <?= $total_messages ?> | 
            <strong>Unread:</strong> <span style="color: #e74c3c;"><?= $unread_messages ?></span>
        </div>
        <a href="manage_messages.php" class="btn btn-small">Manage All Messages</a>
    </div>

    <?php 
    // Get recent contact messages
    $contact_messages = [];
    try {
        $stmt = $conn->prepare("
            SELECT id, name, email, subject, message, status, created_at 
            FROM contact_messages 
            ORDER BY created_at DESC 
            LIMIT 5
        ");
        $stmt->execute();
        $contact_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Table might not exist yet
        $contact_messages = [];
    }
    ?>

    <?php if (!empty($contact_messages)): ?>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Preview</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
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
                        <td>
                            <span class="status-<?= $message['status'] === 'read' ? 'read' : 'pending' ?>">
                                <?= ucfirst($message['status']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="view_message.php?id=<?= $message['id'] ?>" class="btn btn-small" style="padding: 0.25rem 0.5rem; font-size: 0.7rem;">
                                View
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="empty">No contact messages received yet.</p>
    <?php endif; ?>
</section>

    
  </div>
</main>

<footer>
  <div class="container">
    <p>&copy; <?= date("Y"); ?> Strength House Gym. All rights reserved.</p>
  </div>
</footer>
</body>
</html>