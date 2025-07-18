<?php
session_start();
include '../root/db_connect.php';
include '../root/navbar.php';

// Allow only admin users
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['description']));
    $event_date = $_POST['event_date'];
    $venue = htmlspecialchars(trim($_POST['venue']));
    $tickets = (int) $_POST['tickets'];
    $price = (float) $_POST['price'];
    $category = htmlspecialchars(trim($_POST['category']));

    $stmt = $conn->prepare("INSERT INTO events (title, description, event_date, venue, tickets_available, price_per_ticket, category) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiis", $title, $description, $event_date, $venue, $tickets, $price, $category);

    if ($stmt->execute()) {
        echo "Event added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<h2>Add New Event (Admin Only)</h2>
<form action="add_event.php" method="POST">
    <input type="text" name="title" placeholder="Event Title" required><br>
    <textarea name="description" placeholder="Event Description"></textarea><br>
    <input type="date" name="event_date" required><br>
    <input type="text" name="venue" placeholder="Venue" required><br>
    <input type="number" name="tickets" placeholder="Tickets Available" required><br>
    <input type="number" step="0.01" name="price" placeholder="Price per Ticket" required><br>
    <input type="text" name="category" placeholder="Category" required><br>
    <button type="submit">Add Event</button>
</form>
