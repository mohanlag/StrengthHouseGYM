<?php
echo "<h1>Testing Database Connection</h1>";

try {
    include 'db.php';
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
    
    // Test if users table exists
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result->rowCount() > 0) {
        echo "<p style='color: green;'>✓ Users table exists</p>";
    } else {
        echo "<p style='color: red;'>✗ Users table does not exist</p>";
    }
    
    // Test if classes table exists
    $result = $conn->query("SHOW TABLES LIKE 'classes'");
    if ($result->rowCount() > 0) {
        echo "<p style='color: green;'>✓ Classes table exists</p>";
    } else {
        echo "<p style='color: orange;'>! Classes table does not exist (this is normal for first setup)</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
}
?>