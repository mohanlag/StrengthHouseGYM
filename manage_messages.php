<?php
// =================== Start Session and Access Control ===================
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

// =================== Handle Delete Requests ===================
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    if ($stmt->execute([$delete_id])) {
        $_SESSION['success'] = "Message deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete message.";
    }
    header("Location: manage_messages.php");
    exit();
}

// =================== Handle Mark as Read/Unread ===================
if (isset($_GET['toggle_status'])) {
    $toggle_id = intval($_GET['toggle_status']);
    
    // Get current status
    $stmt = $conn->prepare("SELECT status FROM contact_messages WHERE id = ?");
    $stmt->execute([$toggle_id]);
    $msg = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($msg) {
        $new_status = ($msg['status'] === 'read') ? 'pending' : 'read';
        $update_stmt = $conn->prepare("UPDATE contact_messages SET status = ? WHERE id = ?");
        $update_stmt->execute([$new_status, $toggle_id]);
    }
    header("Location: manage_messages.php");
    exit();
}

// =================== Fetch All Messages ===================
try {
    $stmt = $conn->prepare("SELECT * FROM contact_messages ORDER BY created_at DESC");
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    $messages = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Messages - Strength House Gym</title>
    <link rel="stylesheet" href="css/style1.css">
    <style>
        .container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 0.8rem 1rem;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #f5f5f5;
        }
        tr.read {
            background: #e0f7e9;
        }
        tr.pending {
            background: #f9f9f9;
        }
        .actions a {
            margin-right: 0.5rem;
            padding: 0.3rem 0.6rem;
            text-decoration: none;
            color: white;
            border-radius: 3px;
            font-size: 0.85rem;
        }
        .view-btn { background: #3498db; }
        .delete-btn { background: #e74c3c; }
        .toggle-btn { background: #2ecc71; }
        .message-flash {
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <header>
        <h1>Strength House Gym - Admin</h1>
        <nav>
            <a href="admin_dashboard.php">Dashboard</a> |
            <a href="manage_messages.php">Manage Messages</a> |
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <h2>All Contact Messages</h2>

        <!-- Flash Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="message-flash success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message-flash error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Messages Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>From</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Received</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($messages): ?>
                    <?php foreach ($messages as $msg): ?>
                        <tr class="<?= htmlspecialchars($msg['status']); ?>">
                            <td><?= $msg['id']; ?></td>
                            <td><?= htmlspecialchars($msg['name']); ?></td>
                            <td><?= htmlspecialchars($msg['email']); ?></td>
                            <td><?= htmlspecialchars($msg['subject']); ?></td>
                            <td><?= ucfirst($msg['status']); ?></td>
                            <td><?= date('F j, Y g:i A', strtotime($msg['created_at'])); ?></td>
                            <td class="actions">
                                <a href="view_message.php?id=<?= $msg['id']; ?>" class="view-btn">View</a>
                                <a href="manage_messages.php?toggle_status=<?= $msg['id']; ?>" class="toggle-btn">
                                    <?= ($msg['status'] === 'read') ? 'Mark Pending' : 'Mark Read'; ?>
                                </a>
                                <a href="manage_messages.php?delete_id=<?= $msg['id']; ?>" class="delete-btn" 
                                   onclick="return confirm('Are you sure you want to delete this message?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center;">No messages found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <footer>
        <p>&copy; <?= date("Y"); ?> Strength House Gym. All rights reserved.</p>
    </footer>
</body>
</html>
