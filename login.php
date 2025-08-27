<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if ($username === 'admin' && $password === 'fitzone123') {
        $_SESSION['user'] = 'admin';
        header('Location: dashboard.php');
        exit();
    } else {
        echo "Invalid credentials";
    }
}
?>
<form method="POST">
    Username: <input type="text" name="username"><br>
    Password: <input type="password" name="password"><br>
    <input type="submit" value="Login">
</form>