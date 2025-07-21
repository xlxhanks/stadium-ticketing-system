<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Bootstrap & Custom Styles -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="../assets/styles.css">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-3">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Stadium Ticketing System</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="../admin/admin_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="../admin/manage_events.php">Manage Events</a></li>
                    <li class="nav-item"><a class="nav-link" href="../admin/manage_orders.php">Manage Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="../admin/view_feedback.php">Feedback</a></li>
                    <li class="nav-item"><a class="nav-link" href="../admin/payment_confirmations.php">Payments</a></li>
                    <li class="nav-item"><a class="nav-link" href="../admin/reports.php">Reports</a></li>
                <?php elseif (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'user'): ?>
                    <li class="nav-item"><a class="nav-link" href="../user/user_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="../user/view_events.php">View Events</a></li>
                    <li class="nav-item"><a class="nav-link" href="../user/cart.php">My Cart</a></li>
                    <li class="nav-item"><a class="nav-link" href="../user/view_orders.php">My Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="../user/feedback.php">Feedback</a></li>
                <?php endif; ?>
            </ul>
            <?php if (isset($_SESSION['user'])): ?>
                <span class="navbar-text me-2">Hi, <?= htmlspecialchars($_SESSION['user']['name']); ?>!</span>
                <a href="../logout.php" class="btn btn-light btn-sm">Logout</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
