<?php
session_start();
include '../root/db_connect.php';
include '../root/navbar.php';

if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit();
}

$role = $_SESSION['user']['role'];
?>

<h2>Welcome, <?php echo $_SESSION['user']['name']; ?>!</h2>
<p>Your role is: <?php echo $role; ?></p>

<ul>
    <li><a href="view_events.php">View Events</a></li>

    <?php if ($role === 'admin') : ?>
        <li><a href="../admin/add_event.php">Add Event</a></li>
        <li><a href="../admin/manage_events.php">Manage Events</a></li>
    <?php endif; ?>

    <li><a href="../auth/logout.php">Logout</a></li>
</ul>
