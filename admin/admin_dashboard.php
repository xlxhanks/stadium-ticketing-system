<?php
session_start();
include __DIR__ . '/../root/db_connect.php';
include '../root/navbar.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
} 
?>

<div class="container">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success']); ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error']); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>


<div class="container mt-4">
    <h2>Admin Dashboard</h2>

    <div class="row">
        <div class="col-md-4">
            <a href="add_event.php" class="btn btn-primary w-100 mb-3">Add New Event</a>
        </div>
        <div class="col-md-4">
            <a href="manage_events.php" class="btn btn-info w-100 mb-3">Manage Events</a>
        </div>
        <div class="col-md-4">
            <a href="manage_orders.php" class="btn btn-success w-100 mb-3">Manage Orders</a>
        </div>
        <div class="col-md-4">
            <a href="manage_users.php" class="btn btn-secondary w-100 mb-3">Manage Users</a>
        </div>
        <div class="col-md-4">
            <a href="view_feedback.php" class="btn btn-warning w-100 mb-3">View Feedback</a>
        </div>
        <div class="col-md-4">
            <a href="payment_confirmations.php" class="btn btn-dark w-100 mb-3">View Payments</a>
        </div>
    </div>
</div>
