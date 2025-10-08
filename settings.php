<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

include 'db.php';

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submissions
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        // Update profile settings
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        
        $stmt = $conn->prepare("UPDATE users SET fullname = ?, email = ?, phone = ? WHERE id = ?");
        if ($stmt->execute([$fullname, $email, $phone, $_SESSION['user_id']])) {
            $message = "Profile settings updated successfully!";
            // Refresh user data
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
    
    if (isset($_POST['change_password'])) {
        // Change password
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (password_verify($current_password, $user['password'])) {
            if ($new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                if ($stmt->execute([$hashed_password, $_SESSION['user_id']])) {
                    $message = "Password changed successfully!";
                }
            } else {
                $message = "New passwords do not match!";
            }
        } else {
            $message = "Current password is incorrect!";
        }
    }
    
    if (isset($_POST['update_notifications'])) {
        // Update notification settings
        $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
        $sms_notifications = isset($_POST['sms_notifications']) ? 1 : 0;
        $workout_reminders = isset($_POST['workout_reminders']) ? 1 : 0;
        $class_reminders = isset($_POST['class_reminders']) ? 1 : 0;
        
        $message = "Notification settings updated successfully!";
    }
    
    if (isset($_POST['update_privacy'])) {
        // Update privacy settings
        $profile_visibility = $_POST['profile_visibility'];
        $show_progress = isset($_POST['show_progress']) ? 1 : 0;
        $show_workouts = isset($_POST['show_workouts']) ? 1 : 0;
        
        $message = "Privacy settings updated successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Strength House</title>
    <link rel="stylesheet" href="css/style_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <h2>Strength<span>House</span></h2>
            </div>
            <ul class="nav-links">
                <li><a href="index.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> <span>Profile</span></a></li>
                <li><a href="workouts.php"><i class="fas fa-dumbbell"></i> <span>Workouts</span></a></li>
                <li><a href="schedule.php"><i class="fas fa-calendar-alt"></i> <span>Schedule</span></a></li>
                <li><a href="progress.php"><i class="fas fa-chart-line"></i> <span>Progress</span></a></li>
                <li><a href="nutrition.php"><i class="fas fa-apple-alt"></i> <span>Nutrition</span></a></li>
                <li><a href="settings.php" class="active"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="header">
                <h1>Account Settings</h1>
            </div>
            
            <?php if ($message): ?>
                <div class="success-message"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <div class="settings-grid">
                <!-- Profile Settings -->
                <div class="settings-section">
                    <h3><i class="fas fa-user-cog"></i> Profile Settings</h3>
                    <form method="POST">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="fullname" class="form-control" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Membership Plan</label>
                            <select class="form-control" disabled>
                                <option><?php echo htmlspecialchars(ucfirst($user['plan'])); ?> Plan</option>
                            </select>
                            <small style="color: #666; font-size: 0.85rem;">Contact support to change your plan</small>
                        </div>
                        
                        <button type="submit" name="update_profile" class="btn">Update Profile</button>
                    </form>
                </div>
                
                <!-- Password Settings -->
                <div class="settings-section">
                    <h3><i class="fas fa-lock"></i> Change Password</h3>
                    <form method="POST">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" class="form-control" required minlength="6">
                        </div>
                        
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" required minlength="6">
                        </div>
                        
                        <button type="submit" name="change_password" class="btn">Change Password</button>
                    </form>
                </div>
                
                <!-- Notification Settings -->
                <div class="settings-section">
                    <h3><i class="fas fa-bell"></i> Notification Settings</h3>
                    <form method="POST">
                        <div class="toggle-switch">
                            <span class="toggle-label">Email Notifications</span>
                            <label class="switch">
                                <input type="checkbox" name="email_notifications" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        
                        <div class="toggle-switch">
                            <span class="toggle-label">SMS Notifications</span>
                            <label class="switch">
                                <input type="checkbox" name="sms_notifications">
                                <span class="slider"></span>
                            </label>
                        </div>
                        
                        <div class="toggle-switch">
                            <span class="toggle-label">Workout Reminders</span>
                            <label class="switch">
                                <input type="checkbox" name="workout_reminders" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        
                        <div class="toggle-switch">
                            <span class="toggle-label">Class Reminders</span>
                            <label class="switch">
                                <input type="checkbox" name="class_reminders" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        
                        <button type="submit" name="update_notifications" class="btn">Save Notifications</button>
                    </form>
                </div>
                
                <!-- Privacy Settings -->
                <div class="settings-section">
                    <h3><i class="fas fa-shield-alt"></i> Privacy Settings</h3>
                    <form method="POST">
                        <div class="form-group">
                            <label>Profile Visibility</label>
                            <select name="profile_visibility" class="form-control">
                                <option value="public">Public</option>
                                <option value="members">Members Only</option>
                                <option value="private">Private</option>
                            </select>
                        </div>
                        
                        <div class="toggle-switch">
                            <span class="toggle-label">Show Progress to Others</span>
                            <label class="switch">
                                <input type="checkbox" name="show_progress" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        
                        <div class="toggle-switch">
                            <span class="toggle-label">Show Workout History</span>
                            <label class="switch">
                                <input type="checkbox" name="show_workouts" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        
                        <button type="submit" name="update_privacy" class="btn">Save Privacy Settings</button>
                    </form>
                </div>
                
                <!-- Account Actions -->
                <div class="settings-section">
                    <h3><i class="fas fa-exclamation-triangle"></i> Account Actions</h3>
                    <div class="account-actions">
                        <div class="action-item">
                            <h4>Download Your Data</h4>
                            <p>Export all your personal data and workout history</p>
                            <button class="btn btn-outline">
                                <i class="fas fa-download"></i> Export Data
                            </button>
                        </div>
                        
                        <div class="action-item">
                            <h4>Delete Account</h4>
                            <p>Permanently delete your account and all data</p>
                            <button class="btn" style="background: var(--danger);">
                                <i class="fas fa-trash"></i> Delete Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add confirmation for delete account
        document.addEventListener('DOMContentLoaded', function() {
            const deleteBtn = document.querySelector('[style*="background: var(--danger)"]');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
                        e.preventDefault();
                    }
                });
            }
            
            // Password strength indicator
            const newPassword = document.querySelector('input[name="new_password"]');
            const confirmPassword = document.querySelector('input[name="confirm_password"]');
            
            if (newPassword && confirmPassword) {
                confirmPassword.addEventListener('input', function() {
                    if (newPassword.value !== confirmPassword.value) {
                        confirmPassword.style.borderColor = 'var(--danger)';
                    } else {
                        confirmPassword.style.borderColor = 'var(--success)';
                    }
                });
            }
        });
    </script>
</body>
</html>