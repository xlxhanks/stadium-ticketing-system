<?php
session_start();
include __DIR__ . '/../root/db_connect.php';
include '../root/navbar.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../auth/login.php');
    exit();
}

// Initialize cart if not set
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty.</p>";
    exit();
}
?>

<h2>Your Cart</h2>

<table border="1">
    <tr>
        <th>Event</th>
        <th>Seats Selected</th>
        <th>Price per Ticket</th>
        <th>Total Price</th>
    </tr>

    <?php
    $grandTotal = 0;

    foreach ($_SESSION['cart'] as $event_id => $seats):
        $stmt = $conn->prepare("SELECT title, price_per_ticket FROM events WHERE id = ?");
        $stmt->execute([$event_id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        $quantity = count($seats);
        $totalPrice = $quantity * $event['price_per_ticket'];
        $grandTotal += $totalPrice;
    ?>
        <tr>
            <td><?= htmlspecialchars($event['title']); ?></td>
            <td><?= implode(', ', $seats); ?> (<?= $quantity; ?>)</td>
            <td>$<?= number_format($event['price_per_ticket'], 2); ?></td>
            <td>$<?= number_format($totalPrice, 2); ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<p><strong>Grand Total: $<?= number_format($grandTotal, 2); ?></strong></p>

<a href="checkout.php"><button>Proceed to Checkout</button></a>
