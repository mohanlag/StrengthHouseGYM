<?php
session_start();

// For testing - comment this out after setup
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Use the actual trainer ID from your database
    $_SESSION['role'] = 'trainer';
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainer') {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Initialize variables
$classes = [];
$clients = [];
$message = '';
$classesTableExists = false;
$clientsTableExists = false;

try {
    // Get trainer profile
    $stmt = $conn->prepare("SELECT * FROM trainers WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $trainer = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if classes table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'classes'");
    $classesTableExists = $tableCheck->rowCount() > 0;

    if ($classesTableExists) {
        // Verify the table has the required columns
        $columnCheck = $conn->query("SHOW COLUMNS FROM classes LIKE 'class_date'");
        if ($columnCheck->rowCount() > 0) {
            // Table exists and has class_date column, so we can query it
            $stmt = $conn->prepare("SELECT * FROM classes WHERE trainer_id = ? ORDER BY class_date, class_time");
            $stmt->execute([$_SESSION['user_id']]);
            $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $message = "Classes table exists but is missing required columns. Please run the setup again.";
        }
    }

    // Check if trainer_clients table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'trainer_clients'");
    $clientsTableExists = $tableCheck->rowCount() > 0;

    if ($clientsTableExists) {
        $stmt = $conn->prepare("
            SELECT u.*, m.join_date 
            FROM users u 
            INNER JOIN trainer_clients m ON u.id = m.client_id 
            WHERE m.trainer_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    $message = "Database error: " . $e->getMessage();
    
    // If there's an error with classes table, try a simpler query
    if ($classesTableExists) {
        try {
            $stmt = $conn->prepare("SELECT * FROM classes WHERE trainer_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e2) {
            $message .= " Also failed simple query: " . $e2->getMessage();
        }
    }
}

// Handle class creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_class'])) {
    try {
        $class_name = $_POST['class_name'];
        $class_description = $_POST['class_description'];
        $class_date = $_POST['class_date'];
        $class_time = $_POST['class_time'];
        $duration = $_POST['duration'];
        $max_capacity = $_POST['max_capacity'];
        $class_type = $_POST['class_type'];
        
        // Ensure classes table exists with correct structure
        if (!$classesTableExists) {
            $conn->exec("CREATE TABLE IF NOT EXISTS classes (
                id INT PRIMARY KEY AUTO_INCREMENT,
                trainer_id INT NOT NULL,
                class_name VARCHAR(255) NOT NULL,
                description TEXT,
                class_date DATE NOT NULL,
                class_time TIME NOT NULL,
                duration INT NOT NULL,
                max_capacity INT NOT NULL,
                class_type VARCHAR(50) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            $classesTableExists = true;
        }
        
        $stmt = $conn->prepare("
            INSERT INTO classes (trainer_id, class_name, description, class_date, class_time, duration, max_capacity, class_type, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        if ($stmt->execute([$_SESSION['user_id'], $class_name, $class_description, $class_date, $class_time, $duration, $max_capacity, $class_type])) {
            header("Location: trainer_dashboard.php?success=Class created successfully!");
            exit();
        }
    } catch (PDOException $e) {
        $message = "Error creating class: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainer Dashboard - Strength House</title>
    <link rel="stylesheet" href="css/style_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <h2>Strength<span>House</span></h2>
                <p class="role-badge">Trainer</p>
            </div>
            <ul class="nav-links">
                <li><a href="trainer_dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
                <li><a href="trainer_dashboard.php#create-class" onclick="showSection('create-class')"><i class="fas fa-plus-circle"></i> <span>Create Class</span></a></li>
                <li><a href="trainer_dashboard.php#classes" onclick="showSection('classes')"><i class="fas fa-dumbbell"></i> <span>My Classes</span></a></li>
                <li><a href="trainer_dashboard.php#clients" onclick="showSection('clients')"><i class="fas fa-users"></i> <span>My Clients</span></a></li>
                <li class="logout-item"><a href="logout.php" class="logout-link" onclick="return confirmLogout()"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="header">
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($trainer['fullname'], 0, 1)); ?>
                    </div>
                    <div class="user-details">
                        <h3><?php echo htmlspecialchars($trainer['fullname']); ?></h3>
                        <p class="text-gray-500">
						<?= htmlspecialchars($trainer['title'] ?? $trainer['specialty'] ?? 'Trainer') ?>
						</p>
                    </div>
                </div>
                <div class="date-display">
                    <h2><?php echo date('l, F j'); ?></h2>
                    <p><?php echo date('g:i A'); ?></p>
                </div>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="success-message"><?php echo htmlspecialchars($_GET['success']); ?></div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="error-message">
                    <strong>Error:</strong> <?php echo htmlspecialchars($message); ?>
                    <br><br>
                    <a href="setup_database.php" class="btn btn-small" style="background: #e74c3c; color: white;">
                        <i class="fas fa-database"></i> Fix Database Setup
                    </a>
                </div>
            <?php endif; ?>

            <!-- Dashboard Overview -->
            <div id="dashboard" class="section active">
                <div class="stats-grid">
                    <div class="stat-card">
                        <i class="fas fa-dumbbell"></i>
                        <h3><?php echo count($classes); ?></h3>
                        <p>Total Classes</p>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-users"></i>
                        <h3><?php echo count($clients); ?></h3>
                        <p>Active Clients</p>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-calendar-check"></i>
                        <h3><?php 
                            $upcoming = 0;
                            foreach($classes as $class) {
                                if ($class['class_date'] >= date('Y-m-d')) {
                                    $upcoming++;
                                }
                            }
                            echo $upcoming;
                        ?></h3>
                        <p>Upcoming Classes</p>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-database"></i>
                        <h3><?php echo $classesTableExists ? '✓' : '✗'; ?></h3>
                        <p>Database Ready</p>
                    </div>
                </div>

                <?php if (!$classesTableExists): ?>
                <div class="setup-required">
                    <div class="setup-card">
                        <h3><i class="fas fa-exclamation-triangle"></i> Setup Required</h3>
                        <p>The database tables need to be set up before you can use the trainer dashboard.</p>
                        <a href="setup_database.php" class="btn">
                            <i class="fas fa-database"></i> Run Database Setup
                        </a>
                    </div>
                </div>
                <?php else: ?>
                <div class="dashboard-grid">
                    <!-- Today's Classes -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3>Today's Classes</h3>
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="classes-list">
                            <?php 
                            $today_classes = array_filter($classes, function($class) { 
                                return $class['class_date'] == date('Y-m-d'); 
                            });
                            
                            if (empty($today_classes)): ?>
                                <p class="no-data">No classes scheduled for today</p>
                            <?php else: ?>
                                <?php foreach($today_classes as $class): ?>
                                <div class="class-item">
                                    <div class="class-info">
                                        <h4><?php echo htmlspecialchars($class['class_name']); ?></h4>
                                        <p><?php echo date('g:i A', strtotime($class['class_time'])); ?> • <?php echo htmlspecialchars($class['duration']); ?> mins</p>
                                        <span class="class-type"><?php echo htmlspecialchars($class['class_type']); ?></span>
                                    </div>
                                    <div class="class-actions">
                                        <span class="attendance">0/<?php echo htmlspecialchars($class['max_capacity']); ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3>Quick Actions</h3>
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div class="quick-actions">
                            <button class="btn" onclick="showSection('create-class')">
                                <i class="fas fa-plus"></i> Create New Class
                            </button>
                            <button class="btn btn-outline" onclick="showSection('classes')">
                                <i class="fas fa-dumbbell"></i> View All Classes
                            </button>
                            <button class="btn btn-outline" onclick="showSection('clients')">
                                <i class="fas fa-users"></i> View Clients
                            </button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Create Class Section -->
            <div id="create-class" class="section">
                <div class="section-header">
                    <h2>Create New Class</h2>
                    <p>Schedule a new training class</p>
                </div>
                
                <?php if (!$classesTableExists): ?>
                <div class="setup-required">
                    <div class="setup-card">
                        <h3><i class="fas fa-exclamation-triangle"></i> Database Setup Required</h3>
                        <p>You need to set up the database tables before creating classes.</p>
                        <a href="setup_database.php" class="btn">
                            <i class="fas fa-database"></i> Run Database Setup
                        </a>
                    </div>
                </div>
                <?php else: ?>
                <div class="create-class-form">
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Class Name</label>
                                <input type="text" name="class_name" class="form-control" placeholder="e.g., Morning HIIT, Yoga Flow" required>
                            </div>
                            <div class="form-group">
                                <label>Class Type</label>
                                <select name="class_type" class="form-control" required>
                                    <option value="">Select Type</option>
                                    <option value="HIIT">HIIT</option>
                                    <option value="Yoga">Yoga</option>
                                    <option value="Strength">Strength Training</option>
                                    <option value="Cardio">Cardio</option>
                                    <option value="Pilates">Pilates</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Class Description</label>
                            <textarea name="class_description" class="form-control" rows="3" placeholder="Describe the class..."></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Class Date</label>
                                <input type="date" name="class_date" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Class Time</label>
                                <input type="time" name="class_time" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Duration (minutes)</label>
                                <input type="number" name="duration" class="form-control" min="15" max="180" value="60" required>
                            </div>
                            <div class="form-group">
                                <label>Maximum Capacity</label>
                                <input type="number" name="max_capacity" class="form-control" min="1" max="50" value="20" required>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn btn-outline" onclick="showSection('dashboard')">Cancel</button>
                            <button type="submit" name="create_class" class="btn">Create Class</button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
            </div>

            <!-- My Classes Section -->
            <div id="classes" class="section">
                <div class="section-header">
                    <h2>My Classes</h2>
                    <p>Manage your training classes</p>
                </div>
                
                <?php if (!$classesTableExists): ?>
                <div class="setup-required">
                    <div class="setup-card">
                        <h3><i class="fas fa-exclamation-triangle"></i> Database Setup Required</h3>
                        <p>You need to set up the database tables before viewing classes.</p>
                        <a href="setup_database.php" class="btn">
                            <i class="fas fa-database"></i> Run Database Setup
                        </a>
                    </div>
                </div>
                <?php elseif (empty($classes)): ?>
                    <div class="no-data">
                        <p>No classes created yet.</p>
                        <button class="btn" onclick="showSection('create-class')">Create Your First Class</button>
                    </div>
                <?php else: ?>
                    <div class="classes-table">
                        <div class="table-header">
                            <h3>All Classes (<?php echo count($classes); ?>)</h3>
                        </div>
                        
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Class Name</th>
                                        <th>Date & Time</th>
                                        <th>Type</th>
                                        <th>Duration</th>
                                        <th>Capacity</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($classes as $class): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($class['class_name']); ?></strong>
                                            <?php if ($class['description']): ?>
                                            <br><small><?php echo htmlspecialchars($class['description']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo date('M j, Y', strtotime($class['class_date'])); ?>
                                            <br><small><?php echo date('g:i A', strtotime($class['class_time'])); ?></small>
                                        </td>
                                        <td><span class="class-type"><?php echo htmlspecialchars($class['class_type']); ?></span></td>
                                        <td><?php echo htmlspecialchars($class['duration']); ?> mins</td>
                                        <td>0/<?php echo htmlspecialchars($class['max_capacity']); ?></td>
                                        <td>
                                            <span class="status-<?php echo $class['class_date'] >= date('Y-m-d') ? 'active' : 'completed'; ?>">
                                                <?php echo $class['class_date'] >= date('Y-m-d') ? 'Upcoming' : 'Completed'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="edit_class.php?id=<?php echo $class['id']; ?>" class="btn btn-small">Edit</a>
                                            <a href="delete_class.php?id=<?php echo $class['id']; ?>" class="btn btn-small" style="background: #e74c3c;" onclick="return confirm('Delete this class?')">Delete</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Clients Section -->
            <div id="clients" class="section">
                <div class="section-header">
                    <h2>My Clients</h2>
                    <p>Your client relationships</p>
                </div>
                
                <?php if (empty($clients)): ?>
                    <div class="no-data">
                        <p>No clients assigned yet.</p>
                    </div>
                <?php else: ?>
                    <div class="clients-grid">
                        <?php foreach($clients as $client): ?>
                        <div class="client-card">
                            <div class="client-header">
                                <div class="client-avatar large">
                                    <?php echo strtoupper(substr($client['fullname'], 0, 1)); ?>
                                </div>
                                <div class="client-info">
                                    <h3><?php echo htmlspecialchars($client['fullname']); ?></h3>
                                    <p><?php echo htmlspecialchars($client['email']); ?></p>
                                    <span class="member-since">Member since <?php echo date('M Y', strtotime($client['join_date'])); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Show selected section
            document.getElementById(sectionId).classList.add('active');
            
            // Update URL hash
            window.location.hash = sectionId;
        }

        function confirmLogout() {
            return confirm('Are you sure you want to logout?');
        }

        // Set today's date as default for class date
        document.addEventListener('DOMContentLoaded', function() {
            const classDateInput = document.querySelector('input[name="class_date"]');
            if (classDateInput && !classDateInput.value) {
                classDateInput.value = '<?php echo date("Y-m-d"); ?>';
            }
            
            // Handle URL hash
            const hash = window.location.hash.substring(1);
            if (hash) {
                showSection(hash);
            }
        });
    </script>
</body>
</html>