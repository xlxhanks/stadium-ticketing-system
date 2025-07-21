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
    $stmt = $conn->query("SELECT * FROM events WHERE status = 'active' ORDER BY date DESC");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching events: " . $e->getMessage();
    exit();
}
?>

<h2>Available Events</h2>

<?php if (count($events) > 0): ?>
    <table border="1">
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Date</th>
            <th>Price</th>
            <th>Tickets Available</th>
            <th>Action</th>
        </tr>
        <?php foreach ($events as $event): ?>
            <tr>
                <td><?= htmlspecialchars($event['title']); ?></td>
                <td><?= htmlspecialchars($event['description']); ?></td>
                <td><?= htmlspecialchars($event['date']); ?></td>
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
