<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: manage_events.php');
    exit();
}

require __DIR__ . '/../root/db_connect.php';
$id = (int) $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['description']));
    $event_date = $_POST['event_date'];
    $stadium_id = (int) $_POST['stadium_id'];
    $tickets = (int) $_POST['tickets'];
    $price = (float) $_POST['price'];
    $category = htmlspecialchars(trim($_POST['category']));
    $status = htmlspecialchars(trim($_POST['status']));

    try {
        $stmt = $conn->prepare("UPDATE events 
                                SET title = ?, description = ?, date = ?, stadium_id = ?, tickets_available = ?, price_per_ticket = ?, category = ?, status = ?
                                WHERE id = ?");
        $stmt->execute([$title, $description, $event_date, $stadium_id, $tickets, $price, $category, $status, $id]);

        $_SESSION['success'] = "Event updated successfully!";
        header('Location: manage_events.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating event: " . $e->getMessage();
        header("Location: edit_event.php?id=$id");
        exit();
    }
}

try {
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        echo "Event not found!";
        exit();
    }
} catch (PDOException $e) {
    echo "Error fetching event: " . $e->getMessage();
    exit();
}
?>

<!-- HTML starts here -->
<?php include '../root/navbar.php'; ?>

<!-- Your edit event form HTML goes below as usual -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('../assets/add_event.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            max-width: 600px;
            margin: 100px auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2 class="text-center">Edit Event</h2>

    <form action="edit_event.php?id=<?= $id; ?>" method="POST">
        <div class="mb-3">
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($event['title'] ?? ''); ?>" required>
        </div>

        <div class="mb-3">
            <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($event['description'] ?? ''); ?></textarea>
        </div>

        <div class="mb-3">
            <input type="datetime-local" name="event_date" class="form-control" value="<?= htmlspecialchars($event['date'] ?? ''); ?>" required>
        </div>

        <div class="mb-3">
           <label for="stadium_id">Venue:</label>
           <select name="stadium_id" class="form-select" required>
             <?php
              $stadiums = $conn->query("SELECT id, name FROM stadiums");
              while ($s = $stadiums->fetch(PDO::FETCH_ASSOC)) {
                $selected = $s['id'] == $event['stadium_id'] ? 'selected' : '';
                echo "<option value='{$s['id']}' $selected>{$s['name']}</option>";
              }
            ?>
            </select>
        </div>

        <div class="mb-3">
            <input type="number" name="tickets" class="form-control" value="<?= htmlspecialchars($event['tickets_available'] ?? '0'); ?>" required>
        </div>

        <div class="mb-3">
            <input type="number" step="0.01" name="price" class="form-control" value="<?= htmlspecialchars($event['price_per_ticket'] ?? '0.00'); ?>" required>
        </div>

        <div class="mb-3">
            <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($event['category'] ?? ''); ?>" required>
        </div>

        <div class="mb-3">
            <select name="status" class="form-select" required>
                <option value="open" <?= ($event['status'] ?? '') === 'open' ? 'selected' : ''; ?>>Open</option>
                <option value="closed" <?= ($event['status'] ?? '') === 'closed' ? 'selected' : ''; ?>>Closed</option>
                <option value="cancelled" <?= ($event['status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-100">Update Event</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
