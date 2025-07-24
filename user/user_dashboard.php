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

<!-- Background Style -->
<style>
    body {
        background-image: url('../assets/user_dashboard.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        min-height: 100vh;
        margin: 0;
        font-family: Arial, sans-serif;
    }

    .container {
        background: rgba(255, 255, 255, 0.9);
        padding: 20px;
        border-radius: 10px;
        margin-top: 30px;
        max-width: 600px;
    }

    ul {
        list-style: none;
        padding: 0;
    }

    ul li {
        margin-bottom: 10px;
    }

    ul li a {
        text-decoration: none;
        color: #007bff;
    }

    ul li a:hover {
        text-decoration: underline;
    }
</style>

<div class="container mx-auto">
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
