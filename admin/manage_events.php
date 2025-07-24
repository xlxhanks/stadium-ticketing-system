<?php
session_start();
include __DIR__ . '/../root/db_connect.php';
include '../root/navbar.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Fetch events
try {
    $stmt = $conn->query("SELECT * FROM events ORDER BY date DESC");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching events: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Events</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('../assets/manage_events.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .content-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            max-width: 1000px;
            margin: 100px auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .btn-add {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="content-container">
    <h2>Manage Events</h2>

    <?php
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
        unset($_SESSION['success']);
    }
    ?>

    <a href="add_event.php" class="btn btn-primary btn-add">Add New Event</a>

    <?php if (count($events) > 0): ?>
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Venue</th>
                    <th>Event Date</th>
                    <th>Price</th>
                    <th>Tickets</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?= htmlspecialchars($event['title'] ?? ''); ?></td>
                        <td><?= htmlspecialchars($event['category'] ?? ''); ?></td>
                        <td><?= htmlspecialchars($event['venue'] ?? ''); ?></td>
                        <td><?= htmlspecialchars($event['date'] ?? ''); ?></td>
                        <td>$<?= htmlspecialchars($event['price_per_ticket'] ?? '0.00'); ?></td>
                        <td><?= htmlspecialchars($event['tickets_available'] ?? '0'); ?></td>
                        <td>
                          <a href="edit_event.php?id=<?= $event['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                          <a href="delete_event.php?id=<?= $event['id']; ?>" class="btn btn-danger btn-sm" 
                             onclick="return confirm('Are you sure you want to delete this event?');">
                             Delete
                           </a>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-center">No events found.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
