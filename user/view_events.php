<?php
session_start();
include __DIR__ . '/../root/db_connect.php';
include '../root/navbar.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../auth/login.php');
    exit();
}

// Fetch active events
try {
    $stmt = $conn->query("SELECT e.*, s.name AS stadium_name FROM events e JOIN stadiums s ON e.stadium_id = s.id ORDER BY date DESC");
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
    <title>Available Events</title>
    <style>
        body {
            background-image: url('../assets/view_events.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .content {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            max-width: 900px;
            margin: 20px auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f8f9fa;
        }

        button {
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        h2 {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="content">
    <h2>Available Events</h2>

    <?php if (count($events) > 0): ?>
        <table>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Date</th>
                <th>Venue</th>
                <th>Category</th>
                <th>Status</th>
                <th>Price</th>
                <th>Tickets Available</th>
                <th>Action</th>
            </tr>
            <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= htmlspecialchars($event['title']); ?></td>
                    <td><?= htmlspecialchars($event['description']); ?></td>
                    <td><?= htmlspecialchars($event['date']); ?></td>
                    <td><?= htmlspecialchars($event['stadium_name']); ?></td>
                    <td><?= htmlspecialchars($event['category']); ?></td>
                    <td><?= htmlspecialchars($event['status']); ?></td>
                    <td>$<?= number_format($event['price_per_ticket'], 2); ?></td>
                    <td><?= (int)$event['tickets_available']; ?></td>
                    <td>
                        <a href="book_tickets.php?event_id=<?= $event['id']; ?>">
                            <button>Book Now</button>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No active events available.</p>
    <?php endif; ?>
</div>

</body>
</html>
