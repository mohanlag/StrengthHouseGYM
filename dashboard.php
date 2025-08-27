<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'admin') {
    header('Location: login.php');
    exit();
}
?>
<h2>Welcome to Admin Dashboard</h2>
<ul>
    <li><a href="register_member.php">Add Member</a></li>
    <li><a href="register_trainer.php">Add Trainer</a></li>
    <li><a href="membership_plans.php">View Plans</a></li>
</ul>
<a href="logout.php">Logout</a>