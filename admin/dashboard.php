<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../config/db.php';

// Dashboard statistics
$totalBookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$pendingBookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'Pending'")->fetchColumn();
$confirmedBookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'Confirmed'")->fetchColumn();
$upcomingEvents = $pdo->query("SELECT COUNT(*) FROM bookings WHERE event_date >= CURDATE() AND status = 'Confirmed'")->fetchColumn();

// Recent bookings
$recentBookings = $pdo->query("SELECT b.*, p.name as package_name FROM bookings b LEFT JOIN packages p ON b.package_id = p.id ORDER BY b.created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Eventora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Sidebar -->
<div class="admin-sidebar">
    <div class="sidebar-brand">
        <a href="dashboard.php"><i class="fas fa-star"></i> Event<span>ora</span></a>
    </div>
    <nav class="nav flex-column mt-3">
        <a class="nav-link active" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a class="nav-link" href="bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a>
        <a class="nav-link" href="services.php"><i class="fas fa-concierge-bell"></i> Services</a>
        <a class="nav-link" href="packages.php"><i class="fas fa-box"></i> Packages</a>
        <hr style="border-color: rgba(255,255,255,0.1); margin: 15px 20px;">
        <a class="nav-link" href="../public/index.php"><i class="fas fa-globe"></i> View Website</a>
        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>

<!-- Content -->
<div class="admin-content">
    <div class="admin-header">
        <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
        <span class="text-muted">Welcome, <?= htmlspecialchars($_SESSION['admin_username']) ?></span>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="dashboard-card bg-purple">
                <div class="card-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="card-number"><?= $totalBookings ?></div>
                <div class="card-label">Total Bookings</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="dashboard-card bg-gold">
                <div class="card-icon"><i class="fas fa-clock"></i></div>
                <div class="card-number"><?= $pendingBookings ?></div>
                <div class="card-label">Pending Bookings</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="dashboard-card bg-success-grad">
                <div class="card-icon"><i class="fas fa-check-circle"></i></div>
                <div class="card-number"><?= $confirmedBookings ?></div>
                <div class="card-label">Confirmed Bookings</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="dashboard-card bg-info-grad">
                <div class="card-icon"><i class="fas fa-calendar-day"></i></div>
                <div class="card-number"><?= $upcomingEvents ?></div>
                <div class="card-label">Upcoming Events</div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="admin-table">
        <div class="p-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold" style="color: var(--primary-dark);">Recent Bookings</h5>
            <a href="bookings.php" class="btn btn-purple btn-sm">View All</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Event Type</th>
                        <th>Event Date</th>
                        <th>Location</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($recentBookings) > 0): ?>
                        <?php foreach ($recentBookings as $booking): ?>
                        <tr>
                            <td><?= $booking['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($booking['name']) ?></strong><br>
                                <small class="text-muted"><?= htmlspecialchars($booking['phone']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($booking['event_type']) ?></td>
                            <td><?= date('M d, Y', strtotime($booking['event_date'])) ?></td>
                            <td><?= htmlspecialchars($booking['location']) ?></td>
                            <td>
                                <?php
                                $badgeClass = 'badge-pending';
                                if ($booking['status'] === 'Confirmed') $badgeClass = 'badge-confirmed';
                                elseif ($booking['status'] === 'Cancelled') $badgeClass = 'badge-cancelled';
                                ?>
                                <span class="<?= $badgeClass ?>"><?= $booking['status'] ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">No bookings yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
