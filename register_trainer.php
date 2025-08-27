<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $conn = new mysqli("localhost", "root", "", "gym_db");
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
    $sql = "INSERT INTO trainers (name, specialization, email, phone) VALUES ('$name', '$specialization', '$email', '$phone')";
    if ($conn->query($sql) === TRUE) echo "Trainer registered successfully!";
    else echo "Error: " . $conn->error;
    $conn->close();
}
?>
<form method="POST">
    Name: <input type="text" name="name"><br>
    Specialization: <input type="text" name="specialization"><br>
    Email: <input type="email" name="email"><br>
    Phone: <input type="text" name="phone"><br>
    <input type="submit" value="Register">
</form>