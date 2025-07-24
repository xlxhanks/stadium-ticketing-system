<?php
session_start();
include __DIR__ . '/../root/db_connect.php';
include '../root/navbar.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Total Revenue
try {
    $stmt = $conn->query("SELECT SUM(total_amount) AS total_revenue FROM orders WHERE status = 'paid'");
    $totalRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'] ?? 0;
} catch (PDOException $e) {
    echo "Error fetching total revenue: " . $e->getMessage();
    exit();
}

// Top 5 Events by Tickets Sold
try {
    $stmt = $conn->query("
        SELECT e.title, COUNT(t.id) AS tickets_sold
        FROM tickets t
        JOIN events e ON t.event_id = e.id
        GROUP BY t.event_id
        ORDER BY tickets_sold DESC
        LIMIT 5
    ");
    $topEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching top events: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports & Analytics</title>
    <style>
        body {
            background-image: url('../assets/reports.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 80px auto;
            padding: 30px;
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 12px;
            box-shadow: 0 0 10px #000;
        }

        h2, h4 {
            text-align: center;
            margin-bottom: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .table th, .table td {
            padding: 12px;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid #ccc;
            color: #fff;
        }

        .table-striped tbody tr:nth-child(odd) {
            background-color: rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Reports & Analytics</h2>

    <div class="mb-4">
        <h4>Total Revenue: $<?= number_format($totalRevenue, 2); ?></h4>
    </div>

    <div>
        <h4>Top 5 Events (By Tickets Sold)</h4>

        <?php if (count($topEvents) > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Tickets Sold</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topEvents as $event): ?>
                        <tr>
                            <td><?= htmlspecialchars($event['title']); ?></td>
                            <td><?= htmlspecialchars($event['tickets_sold']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No tickets sold yet.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
