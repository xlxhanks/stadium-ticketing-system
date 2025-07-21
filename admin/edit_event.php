<?php
session_start();
include __DIR__ . '/../root/db_connect.php';

include '../root/navbar.php';

// Only admin can access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: manage_events.php');
    exit();
}

$id = (int) $_GET['id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['description']));
    $event_date = $_POST['event_date'];
    $venue = htmlspecialchars(trim($_POST['venue']));
    $tickets = (int) $_POST['tickets'];
    $price = (float) $_POST['price'];
    $category = htmlspecialchars(trim($_POST['category']));
    $status = htmlspecialchars(trim($_POST['status']));

    $stmt = $conn->prepare("UPDATE events SET title = ?, description = ?, event_date = ?, venue = ?, tickets_available = ?, price_per_ticket = ?, category = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssssisisi", $title, $description, $event_date, $venue, $tickets, $price, $category, $status, $id);

    if ($stmt->execute()) {
        header('Location: manage_events.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Fetch event data
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    echo "Event not found!";
    exit();
}
?>

<h2>Edit Event</h2>

<form action="edit_event.php?id=<?php echo $id; ?>" method="POST">
    <input type="text" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required><br>
    <textarea name="description"><?php echo htmlspecialchars($event['description']); ?></textarea><br>
    <input type="date" name="event_date" value="<?php echo $event['event_date']; ?>" required><br>
    <input type="text" name="venue" value="<?php echo htmlspecialchars($event['venue']); ?>" required><br>
    <input type="number" name="tickets" value="<?php echo $event['tickets_available']; ?>" required><br>
    <input type="number" step="0.01" name="price" value="<?php echo $event['price_per_ticket']; ?>" required><br>
    <input type="text" name="category" value="<?php echo htmlspecialchars($event['category']); ?>" required><br>
   <select name="status" required>
    <option value="open" <?php if ($event['status'] == 'open') echo 'selected'; ?>>Open</option>
    <option value="closed" <?php if ($event['status'] == 'closed') echo 'selected'; ?>>Closed</option>
    <option value="cancelled" <?php if ($event['status'] == 'cancelled') echo 'selected'; ?>>Cancelled</option>
</select><br>
    <button type="submit">Update Event</button>
</form>
