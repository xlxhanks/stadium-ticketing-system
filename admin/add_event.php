<?php
session_start();
include __DIR__ . '/../root/db_connect.php';
include '../root/navbar.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['description']));
    $event_date = $_POST['event_date']; // Will go into `date` column
    $price = (float) $_POST['price'];
    $tickets = (int) $_POST['tickets'];

    try {
        $stmt = $conn->prepare("INSERT INTO events (title, description, date, price_per_ticket, tickets_available, status) VALUES (?, ?, ?, ?, ?, 'active')");
        $stmt->execute([$title, $description, $event_date, $price, $tickets]);

        $_SESSION['success'] = "Event added successfully!";
        header('Location: manage_events.php');
        exit();
    } catch (PDOException $e) {
        echo "Error adding event: " . $e->getMessage();
        exit();
    }
}
?>

<h2>Add New Event</h2>

<?php
if (isset($_SESSION['success'])) {
    echo '<p style="color:green;">' . $_SESSION['success'] . '</p>';
    unset($_SESSION['success']);
}
?>

<form method="POST" action="">
    <input type="text" name="title" placeholder="Event Title" required><br><br>

    <textarea name="description" placeholder="Description" required></textarea><br><br>

    <input type="datetime-local" name="event_date" required><br><br>

    <input type="number" name="price" step="0.01" placeholder="Price per Ticket (USD)" required><br><br>

    <input type="number" name="tickets" placeholder="Total Tickets Available" required><br><br>

    <button type="submit">Add Event</button>
</form>
