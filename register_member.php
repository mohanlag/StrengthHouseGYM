<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $plan = $_POST['plan'];
    $conn = new mysqli("localhost", "root", "", "gym_db");
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
    $sql = "INSERT INTO members (name, email, phone, plan) VALUES ('$name', '$email', '$phone', '$plan')";
    if ($conn->query($sql) === TRUE) echo "Member registered successfully!";
    else echo "Error: " . $conn->error;
    $conn->close();
}
?>
<form method="POST">
    Name: <input type="text" name="name"><br>
    Email: <input type="email" name="email"><br>
    Phone: <input type="text" name="phone"><br>
    Plan: <input type="text" name="plan"><br>
    <input type="submit" value="Register">
</form>