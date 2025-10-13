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
    
   
    
    
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>