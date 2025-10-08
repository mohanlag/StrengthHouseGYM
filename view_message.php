<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

$message_id = $_GET['id'] ?? 0;

try {
    // Get the message
    $stmt = $conn->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $stmt->execute([$message_id]);
    $message = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$message) {
        $_SESSION['error'] = "Message not found.";
        header("Location: admin_dashboard.php");
        exit();
    }
    
    // Mark as read
    $update_stmt = $conn->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?");
    $update_stmt->execute([$message_id]);
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Database error.";
    header("Location: admin_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Message - Strength House Gym</title>
    <link rel="stylesheet" href="css/style1.css">
    <style>
        .message-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 20px;
        }
        .message-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .message-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
        .message-meta {
            color: #666;
            font-size: 0.9rem;
        }
        .message-body {
            line-height: 1.6;
            white-space: pre-wrap;
        }
        .btn-group {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
        }
    </style>
</head>
<body>
    <header>
        <h1>Strength House Gym</h1>
        <nav>
            <a href="admin_dashboard.php">Dashboard</a> |
            <a href="manage_messages.php">All Messages</a> |
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="message-container">
        <h2>Contact Message</h2>
        
        <div class="message-card">
            <div class="message-header">
                <h3><?= htmlspecialchars($message['subject']) ?></h3>
                <div class="message-meta">
                    <strong>From:</strong> <?= htmlspecialchars($message['name']) ?> (<?= htmlspecialchars($message['email']) ?>)<br>
                    <strong>Received:</strong> <?= date('F j, Y \a\t g:i A', strtotime($message['created_at'])) ?><br>
                    <strong>Status:</strong> <span style="color: #27ae60;">Read</span>
                </div>
            </div>
            
            <div class="message-body">
                <?= nl2br(htmlspecialchars($message['message'])) ?>
            </div>
            
            <div class="btn-group">
                <a href="admin_dashboard.php" class="btn">Back to Dashboard</a>
                <a href="manage_messages.php" class="btn" style="background: #3498db;">All Messages</a>
                <a href="mailto:<?= htmlspecialchars($message['email']) ?>?subject=Re: <?= htmlspecialchars($message['subject']) ?>" 
                   class="btn" style="background: #2ecc71;">Reply via Email</a>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; <?= date("Y"); ?> Strength House Gym. All rights reserved.</p>
    </footer>
</body>
</html>