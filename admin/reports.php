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

<div class="container mt-4">
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
