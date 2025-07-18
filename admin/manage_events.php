<?php
session_start();
include '../root/db_connect.php';
include '../root/navbar.php';

// Only admin can access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$result = $conn->query("SELECT * FROM events ORDER BY event_date DESC");
?>

<h2>Manage Events</h2>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Venue</th>
        <th>Date</th>
        <th>Tickets</th>
        <th>Price</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()) : ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['venue']); ?></td>
            <td><?php echo $row['event_date']; ?></td>
            <td><?php echo $row['tickets_available']; ?></td>
            <td>$<?php echo $row['price_per_ticket']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td>
                <a href="edit_event.php?id=<?php echo $row['id']; ?>">Edit</a> |
                <a href="delete_event.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this event?');">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
