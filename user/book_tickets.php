<?php
session_start();
include __DIR__ . '/../root/db_connect.php';
include '../root/navbar.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../auth/login.php');
    exit();
}

if (!isset($_GET['event_id'])) {
    echo "No event selected.";
    exit();
}

$event_id = $_GET['event_id'];

// Fetch event details
try {
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        echo "Event not found.";
        exit();
    }

    // Available seats logic — generate dummy seats if none
    $totalSeats = 100;  // Change this as per capacity
    $bookedSeats = [];

    $stmtSeats = $conn->prepare("SELECT seat_number FROM tickets WHERE event_id = ?");
    $stmtSeats->execute([$event_id]);
    $bookedSeatsResult = $stmtSeats->fetchAll(PDO::FETCH_ASSOC);

    foreach ($bookedSeatsResult as $bs) {
        $bookedSeats[] = $bs['seat_number'];
    }

    $availableSeats = [];
    for ($i = 1; $i <= $totalSeats; $i++) {
        $seatLabel = 'S' . str_pad($i, 3, '0', STR_PAD_LEFT);
        if (!in_array($seatLabel, $bookedSeats)) {
            $availableSeats[] = $seatLabel;
        }
    }

} catch (PDOException $e) {
    echo "Error fetching event: " . $e->getMessage();
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['selected_seats'])) {
    $selectedSeats = $_POST['selected_seats'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $_SESSION['cart'][$event_id] = $selectedSeats;

    header('Location: cart.php');
    exit();
}
?>

<!-- Background container -->
<div style="
    background-image: url('../assets/book_tickets.jpg');
    background-size: cover;
    background-position: center;
    min-height: 100vh;
    padding: 40px;
    color: #fff;
">

    <div style="background-color: rgba(0, 0, 0, 0.6); padding: 20px; border-radius: 10px; max-width: 600px; margin: auto;">
        <h2>Book Tickets for <?= htmlspecialchars($event['title']); ?></h2>
        <p>Date: <?= htmlspecialchars($event['date']); ?></p>
        <p>Price per Ticket: $<?= number_format($event['price_per_ticket'], 2); ?></p>

        <form method="POST" action="">
            <p>Select Seats:</p>
            <?php foreach ($availableSeats as $seat): ?>
                <label>
                    <input type="checkbox" name="selected_seats[]" value="<?= htmlspecialchars($seat); ?>">
                    <?= htmlspecialchars($seat); ?>
                </label><br>
            <?php endforeach; ?>

            <br>
            <button type="submit" style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px;">Add to Cart</button>
        </form>
    </div>
</div>
