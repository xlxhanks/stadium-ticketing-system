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

<h2>Manage Events</h2>

<?php
if (isset($_SESSION['success'])) {
    echo '<p style="color:green;">' . $_SESSION['success'] . '</p>';
    unset($_SESSION['success']);
}
?>

<a href="add_event.php"><button>Add New Event</button></a><br><br>

<?php if (count($events) > 0): ?>
    <table border="1">
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Event Date</th>
            <th>Price</th>
            <th>Tickets</th>
        </tr>
        <?php foreach ($events as $event): ?>
            <tr>
                <td><?= htmlspecialchars($event['title'] ?? ''); ?></td>
                <td><?= htmlspecialchars($event['description'] ?? ''); ?></td>
                <td><?= htmlspecialchars($event['date'] ?? ''); ?></td>
                <td>$<?= htmlspecialchars($event['price_per_ticket'] ?? '0.00'); ?></td>
                <td><?= htmlspecialchars($event['tickets_available'] ?? '0'); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No events found.</p>
<?php endif; ?>
