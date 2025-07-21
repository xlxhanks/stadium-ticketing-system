<?php
session_start();
include __DIR__ . '/../root/db_connect.php';
include '../root/navbar.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];

if (empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty.</p>";
    exit();
}

// Start transaction
$conn->beginTransaction();

try {
    $grandTotal = 0;

    // Calculate total amount
    foreach ($_SESSION['cart'] as $event_id => $seats) {
        $stmt = $conn->prepare("SELECT price_per_ticket, tickets_available FROM events WHERE id = ?");
        $stmt->execute([$event_id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($event['tickets_available'] < count($seats)) {
            throw new Exception("Not enough tickets available for event ID $event_id.");
        }

        $grandTotal += $event['price_per_ticket'] * count($seats);
    }

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
    $stmt->execute([$user_id, $grandTotal]);
    $order_id = $conn->lastInsertId();

    // Insert tickets and update events
    foreach ($_SESSION['cart'] as $event_id => $seats) {
        foreach ($seats as $seat) {
            $stmt_ticket = $conn->prepare("INSERT INTO tickets (user_id, event_id, seat_number, purchase_time) VALUES (?, ?, ?, NOW())");
            $stmt_ticket->execute([$user_id, $event_id, $seat]);
        }

        $stmt_update = $conn->prepare("UPDATE events SET tickets_available = tickets_available - ? WHERE id = ?");
        $stmt_update->execute([count($seats), $event_id]);
    }

    $conn->commit();

    // Empty cart
    $_SESSION['cart'] = [];

    echo "<h2>Checkout Successful!</h2>";
    echo "<p>Your order has been placed successfully.</p>";
    echo '<a href="view_orders.php"><button>View My Orders</button></a>';

} catch (Exception $e) {
    $conn->rollBack();
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
