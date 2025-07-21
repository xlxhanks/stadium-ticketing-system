<?php
session_start();
include __DIR__ . '/../root/db_connect.php';

if (isset($_SESSION['success'])) {
    echo '<p style="color: green; font-weight: bold;">' . htmlspecialchars($_SESSION['success']) . '</p>';
    unset($_SESSION['success']); // Clear the message so it only shows once
}

if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit();
}

$role = $_SESSION['user']['role'];
?>

<?php include '../root/navbar.php'; ?>

<div class="container mt-4">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</h2>
    <p>Your role is: <?php echo htmlspecialchars($role); ?></p>

    <ul>
        <li><a href="view_events.php">View Events</a></li>

        <?php if ($role === 'admin') : ?>
            <li><a href="../admin/add_event.php">Add Event</a></li>
            <li><a href="../admin/manage_events.php">Manage Events</a></li>
        <?php endif; ?>

        <li><a href="../logout.php">Logout</a></li>
    </ul>
</div>
