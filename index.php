<?php
session_start();

if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] === 'admin') {
        header('Location: admin/manage_events.php');
    } else {
        header('Location: user/dashboard.php');
    }
    exit();
}
?>

<link rel="stylesheet" href="assets/styles.css">

<div class="centered-container">
    <h1>Welcome to the Stadium Ticketing System</h1>

    <div class="button-group">
        <a href="auth/login.php"><button class="primary-btn">Login</button></a>
        <a href="auth/register.php"><button class="secondary-btn">Register</button></a>
    </div>
</div>
