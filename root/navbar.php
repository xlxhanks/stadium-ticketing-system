<?php
if (!isset($_SESSION)) {
    session_start();
}
?>

<link rel="stylesheet" href="../assets/styles.css">

<nav style="background: #3498db; color: white; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
    <strong>Stadium Ticketing System</strong> |
    <a href="../user/dashboard.php" style="color: white;">Dashboard</a> |
    <a href="../user/view_events.php" style="color: white;">View Events</a>

    <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') : ?>
        | <a href="../admin/add_event.php" style="color: white;">Add Event</a>
        | <a href="../admin/manage_events.php" style="color: white;">Manage Events</a>
    <?php endif; ?>

    | <a href="../auth/logout.php" style="color: white;">Logout</a>
</nav>
