<?php
session_start();
include '../root/db_connect.php';
include '../root/navbar.php';


// Optional: Restrict to logged-in users
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$result = $conn->query("SELECT * FROM events WHERE status = 'open' ORDER BY event_date ASC");
?>

<h2>Available Events</h2>

<?php while ($row = $result->fetch_assoc()) : ?>
    <div style="border:1px solid #ccc; padding:10px; margin:10px;">
        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
        <p><?php echo htmlspecialchars($row['description']); ?></p>
        <p><strong>Date:</strong> <?php echo $row['event_date']; ?></p>
        <p><strong>Venue:</strong> <?php echo htmlspecialchars($row['venue']); ?></p>
        <p><strong>Tickets Available:</strong> <?php echo $row['tickets_available']; ?></p>
        <p><strong>Price per Ticket:</strong> $<?php echo $row['price_per_ticket']; ?></p>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($row['category']); ?></p>
        <a href="book_ticket.php?event_id=<?php echo $row['id']; ?>">Book a Seat</a>
    </div>
<?php endwhile; ?>
