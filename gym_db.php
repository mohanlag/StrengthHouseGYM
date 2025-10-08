<?php
// gym_db.php - Complete Database Setup for Strength House Gym
session_start();

// Database configuration
$servername = "localhost";
$username = "root";  // Change to your database username
$password = "";      // Change to your database password
$dbname = "gym_db";

try {
    // Create connection
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $conn->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $conn->exec("USE $dbname");
    
    echo "✅ Database connected successfully<br>";
    
} catch(PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}

// Function to setup all tables
function setupDatabaseTables($conn) {
    try {
        // Users table - Members, Trainers, Admins
        $conn->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            fullname VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            phone VARCHAR(20),
            password VARCHAR(255) NOT NULL,
            role ENUM('member', 'trainer', 'admin') DEFAULT 'member',
            plan VARCHAR(50),
            goals TEXT,
            height DECIMAL(5,2),
            weight DECIMAL(5,2),
            date_of_birth DATE,
            gender ENUM('male', 'female', 'other'),
            emergency_contact_name VARCHAR(100),
            emergency_contact_phone VARCHAR(20),
            medical_conditions TEXT,
            fitness_level ENUM('beginner', 'intermediate', 'advanced'),
            membership_start_date DATE,
            membership_end_date DATE,
            status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
            profile_image VARCHAR(255),
            last_login DATETIME,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB");
        echo "✅ Users table created/verified<br>";

        // Trainers table (separate for trainer-specific data)
        $conn->exec("CREATE TABLE IF NOT EXISTS trainers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            phone VARCHAR(20),
            password VARCHAR(255) NOT NULL,
            specialty VARCHAR(100),
            experience INT,
            certification TEXT,
            bio TEXT,
            hourly_rate DECIMAL(8,2),
            availability TEXT,
            profile_image VARCHAR(255),
            role ENUM('trainer') DEFAULT 'trainer',
            status ENUM('active', 'inactive') DEFAULT 'active',
            rating DECIMAL(3,2) DEFAULT 0.00,
            total_sessions INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB");
        echo "✅ Trainers table created/verified<br>";

        // Membership Plans table
        $conn->exec("CREATE TABLE IF NOT EXISTS membership_plans (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            price DECIMAL(8,2) NOT NULL,
            duration_days INT NOT NULL,
            features TEXT,
            max_classes_per_week INT,
            personal_training_sessions INT DEFAULT 0,
            gym_access BOOLEAN DEFAULT TRUE,
            group_classes BOOLEAN DEFAULT FALSE,
            pool_access BOOLEAN DEFAULT FALSE,
            sauna_access BOOLEAN DEFAULT FALSE,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB");
        echo "✅ Membership plans table created/verified<br>";

        // Classes table
        $conn->exec("CREATE TABLE IF NOT EXISTS classes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            trainer_id INT,
            day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
            start_time TIME,
            end_time TIME,
            duration INT,
            max_capacity INT DEFAULT 20,
            current_enrollment INT DEFAULT 0,
            price DECIMAL(8,2) DEFAULT 0.00,
            difficulty_level ENUM('beginner', 'intermediate', 'advanced', 'all'),
            room VARCHAR(50),
            status ENUM('active', 'inactive', 'cancelled') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB");
        echo "✅ Classes table created/verified<br>";

        // Bookings table
        $conn->exec("CREATE TABLE IF NOT EXISTS bookings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            class_id INT,
            trainer_id INT,
            booking_date DATE,
            booking_time TIME,
            duration INT,
            status ENUM('confirmed', 'pending', 'cancelled', 'completed') DEFAULT 'confirmed',
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
        ) ENGINE=InnoDB");
        echo "✅ Bookings table created/verified<br>";

        // Contact Messages table
        $conn->exec("CREATE TABLE IF NOT EXISTS contact_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            subject VARCHAR(200) NOT NULL,
            message TEXT NOT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            status ENUM('new', 'read', 'replied', 'archived') DEFAULT 'new',
            admin_notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            replied_at DATETIME,
            archived_at DATETIME
        ) ENGINE=InnoDB");
        echo "✅ Contact messages table created/verified<br>";

        // Payments table
        $conn->exec("CREATE TABLE IF NOT EXISTS payments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            plan_id INT,
            amount DECIMAL(10,2) NOT NULL,
            payment_method ENUM('credit_card', 'debit_card', 'paypal', 'cash', 'bank_transfer'),
            transaction_id VARCHAR(100),
            status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
            payment_date DATE,
            next_payment_date DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (plan_id) REFERENCES membership_plans(id) ON DELETE SET NULL
        ) ENGINE=InnoDB");
        echo "✅ Payments table created/verified<br>";

        // Attendance table
        $conn->exec("CREATE TABLE IF NOT EXISTS attendance (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            class_id INT,
            check_in DATETIME,
            check_out DATETIME,
            duration_minutes INT,
            status ENUM('present', 'absent', 'late') DEFAULT 'present',
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL
        ) ENGINE=InnoDB");
        echo "✅ Attendance table created/verified<br>";

        // Workout Plans table
        $conn->exec("CREATE TABLE IF NOT EXISTS workout_plans (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            trainer_id INT,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            goal ENUM('weight_loss', 'muscle_gain', 'endurance', 'strength', 'general_fitness'),
            difficulty ENUM('beginner', 'intermediate', 'advanced'),
            duration_weeks INT,
            status ENUM('active', 'completed', 'paused') DEFAULT 'active',
            start_date DATE,
            end_date DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (trainer_id) REFERENCES trainers(id) ON DELETE SET NULL
        ) ENGINE=InnoDB");
        echo "✅ Workout plans table created/verified<br>";

        // Exercises table
        $conn->exec("CREATE TABLE IF NOT EXISTS exercises (
            id INT AUTO_INCREMENT PRIMARY KEY,
            workout_plan_id INT,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            sets INT,
            reps VARCHAR(50),
            weight VARCHAR(50),
            rest_time VARCHAR(20),
            day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
            order_index INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (workout_plan_id) REFERENCES workout_plans(id) ON DELETE CASCADE
        ) ENGINE=InnoDB");
        echo "✅ Exercises table created/verified<br>";

        // Progress Tracking table
        $conn->exec("CREATE TABLE IF NOT EXISTS progress_tracking (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            weight DECIMAL(5,2),
            height DECIMAL(5,2),
            chest_cm DECIMAL(5,2),
            waist_cm DECIMAL(5,2),
            hips_cm DECIMAL(5,2),
            biceps_cm DECIMAL(5,2),
            body_fat_percentage DECIMAL(4,2),
            notes TEXT,
            measurement_date DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB");
        echo "✅ Progress tracking table created/verified<br>";

        // Equipment table
        $conn->exec("CREATE TABLE IF NOT EXISTS equipment (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            category VARCHAR(50),
            quantity INT DEFAULT 1,
            status ENUM('available', 'in_use', 'maintenance', 'out_of_service') DEFAULT 'available',
            last_maintenance DATE,
            next_maintenance DATE,
            purchase_date DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB");
        echo "✅ Equipment table created/verified<br>";

        // Notifications table
        $conn->exec("CREATE TABLE IF NOT EXISTS notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            title VARCHAR(200) NOT NULL,
            message TEXT NOT NULL,
            type ENUM('info', 'warning', 'success', 'error', 'reminder'),
            is_read BOOLEAN DEFAULT FALSE,
            related_url VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB");
        echo "✅ Notifications table created/verified<br>";

        // Insert default membership plans
        $plans = [
            ['Basic Plan', 'Gym access only', 29.00, 30, 'Access to all gym equipment|Locker room access|Free fitness assessment', 0, 0, true, false, false, false],
            ['Standard Plan', 'Gym + Classes', 49.00, 30, 'Everything in Basic Plan|Unlimited group classes|Access to nutrition guidance', 7, 0, true, true, false, false],
            ['Premium Plan', 'All Access + Personal Trainer', 79.00, 30, 'Everything in Standard Plan|Personal trainer sessions|Custom fitness plan|Priority support', 14, 4, true, true, true, true],
            ['Family Plan', '2 Adults + 2 Kids', 99.00, 30, 'Family package for 2 adults and 2 children|All gym facilities|Group classes access', 14, 0, true, true, true, true],
            ['Student Plan', 'Discounted rate for students', 39.00, 30, 'Gym access|Student discount|Valid student ID required', 7, 0, true, true, false, false]
        ];

        $stmt = $conn->prepare("INSERT IGNORE INTO membership_plans (name, description, price, duration_days, features, max_classes_per_week, personal_training_sessions, gym_access, group_classes, pool_access, sauna_access) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($plans as $plan) {
            $stmt->execute($plan);
        }
        echo "✅ Default membership plans inserted<br>";

        // Insert sample classes
        $classes = [
            ['HIIT Training', 'High Intensity Interval Training for maximum calorie burn', 'Monday', '06:00:00', '07:00:00', 60, 20, 15.00, 'intermediate', 'Studio A'],
            ['Yoga Flow', 'Relaxing yoga session for flexibility and mindfulness', 'Tuesday', '09:00:00', '10:00:00', 60, 15, 12.00, 'beginner', 'Yoga Room'],
            ['CrossFit', 'Functional fitness with high intensity', 'Wednesday', '17:00:00', '18:00:00', 60, 15, 18.00, 'advanced', 'Main Floor'],
            ['Spin Class', 'Cardio cycling session with great music', 'Thursday', '18:00:00', '19:00:00', 60, 20, 15.00, 'intermediate', 'Spin Studio'],
            ['Strength Training', 'Weight lifting and strength building', 'Friday', '07:00:00', '08:00:00', 60, 25, 10.00, 'all', 'Weight Room'],
            ['Zumba Dance', 'Fun dance workout for all levels', 'Saturday', '10:00:00', '11:00:00', 60, 30, 12.00, 'beginner', 'Studio B'],
            ['Pilates', 'Core strengthening and body alignment', 'Sunday', '11:00:00', '12:00:00', 60, 15, 14.00, 'intermediate', 'Studio A']
        ];

        $stmt = $conn->prepare("INSERT IGNORE INTO classes (name, description, day_of_week, start_time, end_time, duration, max_capacity, price, difficulty_level, room) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($classes as $class) {
            $stmt->execute($class);
        }
        echo "✅ Sample classes inserted<br>";

        // Insert sample equipment
        $equipment = [
            ['Treadmill Pro 5000', 'Commercial grade treadmill with incline', 'Cardio', 5],
            ['Stationary Bike Elite', 'Advanced stationary bike with digital display', 'Cardio', 8],
            ['Bench Press Station', 'Adjustable bench press with weights', 'Strength', 3],
            ['Dumbbell Set', 'Rubber coated dumbbells 5-50 lbs', 'Strength', 10],
            ['Yoga Mats', 'High quality non-slip yoga mats', 'Accessories', 25],
            ['Resistance Bands', 'Set of 5 resistance bands', 'Accessories', 15],
            ['Leg Press Machine', 'Commercial leg press machine', 'Strength', 2],
            ['Rowing Machine', 'Water resistance rowing machine', 'Cardio', 4]
        ];

        $stmt = $conn->prepare("INSERT IGNORE INTO equipment (name, description, category, quantity) VALUES (?, ?, ?, ?)");
        
        foreach ($equipment as $item) {
            $stmt->execute($item);
        }
        echo "✅ Sample equipment inserted<br>";

        // Create default admin user if not exists
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT IGNORE INTO users (fullname, email, password, role, status) VALUES (?, ?, ?, 'admin', 'active')");
        $stmt->execute(['Administrator', 'admin@strengthhousegym.com', $adminPassword]);
        echo "✅ Default admin user created (email: admin@strengthhousegym.com, password: admin123)<br>";

        echo "<h3 style='color: green;'>🎉 Database setup completed successfully!</h3>";
        echo "<p><strong>All tables have been created and sample data inserted.</strong></p>";

    } catch(PDOException $e) {
        echo "<h3 style='color: red;'>❌ Error setting up database: " . $e->getMessage() . "</h3>";
    }
}

// Auto-setup database tables (run once)
setupDatabaseTables($conn);

// Function to get database connection for other files
function getDBConnection() {
    global $servername, $username, $password, $dbname;
    
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// For backward compatibility
$conn = getDBConnection();
?>