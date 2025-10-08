<?php
echo "<h1>Setting up Database Tables</h1>";

try {
    include 'db.php';
    echo "<p>Database connected successfully...</p>";
    
    // Create classes table
    $sql = "CREATE TABLE IF NOT EXISTS classes (
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
    )";
    
    $conn->exec($sql);
    echo "<p style='color: green;'>✓ Classes table created successfully</p>";
    
    // Create trainer_clients table
    $sql = "CREATE TABLE IF NOT EXISTS trainer_clients (
        id INT PRIMARY KEY AUTO_INCREMENT,
        trainer_id INT NOT NULL,
        client_id INT NOT NULL,
        join_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $conn->exec($sql);
    echo "<p style='color: green;'>✓ Trainer clients table created successfully</p>";
    
    // Add trainer columns to users table
    $columns = [
        'specialization' => "ALTER TABLE users ADD COLUMN specialization VARCHAR(100)",
        'bio' => "ALTER TABLE users ADD COLUMN bio TEXT", 
        'experience' => "ALTER TABLE users ADD COLUMN experience INT"
    ];
    
    foreach ($columns as $name => $sql) {
        try {
            $conn->exec($sql);
            echo "<p style='color: green;'>✓ Column '$name' added successfully</p>";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "<p style='color: orange;'>! Column '$name' already exists</p>";
            } else {
                echo "<p style='color: red;'>✗ Error adding column '$name': " . $e->getMessage() . "</p>";
            }
        }
    }
    
    // Insert sample trainer
    $checkTrainer = $conn->prepare("SELECT id FROM users WHERE email = 'trainer@strengthhouse.com'");
    $checkTrainer->execute();
    
    if ($checkTrainer->rowCount() == 0) {
        $sql = "INSERT INTO users (fullname, email, phone, password, role, specialization, bio, experience) 
                VALUES ('John Trainer', 'trainer@strengthhouse.com', '041624851', 'trainer123', 'trainer', 'Strength Training', 'Certified personal trainer with 5 years of experience', 5)";
        $conn->exec($sql);
        echo "<p style='color: green;'>✓ Sample trainer created successfully</p>";
    } else {
        echo "<p style='color: orange;'>! Trainer already exists</p>";
    }
    
    echo "<h2 style='color: green;'>Setup completed successfully!</h2>";
    echo "<p><a href='trainer_dashboard.php'>Go to Trainer Dashboard</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>