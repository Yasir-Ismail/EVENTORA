<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../config/db.php';

$success = '';
$error = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $booking_id = (int) ($_POST['booking_id'] ?? 0);
    $new_status = $_POST['new_status'] ?? '';

    if ($booking_id > 0 && in_array($new_status, ['Pending', 'Confirmed', 'Cancelled'])) {
        // Date conflict check when confirming
        if ($new_status === 'Confirmed') {
            $dateStmt = $pdo->prepare("SELECT event_date FROM bookings WHERE id = ?");
            $dateStmt->execute([$booking_id]);
            $bookingDate = $dateStmt->fetchColumn();

            if ($bookingDate) {
                $conflictStmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE event_date = ? AND status = 'Confirmed' AND id != ?");
                $conflictStmt->execute([$bookingDate, $booking_id]);
                $conflictCount = $conflictStmt->fetchColumn();

                if ($conflictCount > 0) {
                    $error = 'Warning: There is already a confirmed booking on ' . htmlspecialchars($bookingDate) . '. Cannot confirm this booking to prevent double booking.';
                }
            }
        }

        if (empty($error)) {
            $updateStmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
            $updateStmt->execute([$new_status, $booking_id]);
            $success = 'Booking #' . $booking_id . ' has been updated to "' . htmlspecialchars($new_status) . '".';
        }
    } else {
        $error = 'Invalid action.';
    }
}

// Fetch all bookings
$bookings = $pdo->query("SELECT b.*, p.name as package_name FROM bookings b LEFT JOIN packages p ON b.package_id = p.id ORDER BY b.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Eventora Admin</title>
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
        <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a class="nav-link active" href="bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a>
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
        <h2><i class="fas fa-calendar-check me-2"></i>Manage Bookings</h2>
        <span class="text-muted"><?= count($bookings) ?> total bookings</span>
    </div>

    <?php if ($success): ?>
    <div class="alert alert-success-eventora alert-eventora alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i><?= $success ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert alert-danger-eventora alert-eventora alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="admin-table">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Event Type</th>
                        <th>Event Date</th>
                        <th>Location</th>
                        <th>Package</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($bookings) > 0): ?>
                        <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?= $booking['id'] ?></td>
                            <td><strong><?= htmlspecialchars($booking['name']) ?></strong></td>
                            <td><?= htmlspecialchars($booking['phone']) ?></td>
                            <td><?= htmlspecialchars($booking['event_type']) ?></td>
                            <td><?= date('M d, Y', strtotime($booking['event_date'])) ?></td>
                            <td><?= htmlspecialchars($booking['location']) ?></td>
                            <td><?= $booking['package_name'] ? htmlspecialchars($booking['package_name']) : '<span class="text-muted">None</span>' ?></td>
                            <td>
                                <?php
                                $badgeClass = 'badge-pending';
                                if ($booking['status'] === 'Confirmed') $badgeClass = 'badge-confirmed';
                                elseif ($booking['status'] === 'Cancelled') $badgeClass = 'badge-cancelled';
                                ?>
                                <span class="<?= $badgeClass ?>"><?= $booking['status'] ?></span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <?php if ($booking['status'] !== 'Confirmed'): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                        <input type="hidden" name="new_status" value="Confirmed">
                                        <button type="submit" class="btn btn-success btn-sm btn-status-confirm" data-status="Confirmed" title="Confirm">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>

                                    <?php if ($booking['status'] !== 'Pending'): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                        <input type="hidden" name="new_status" value="Pending">
                                        <button type="submit" class="btn btn-warning btn-sm btn-status-confirm" data-status="Pending" title="Mark Pending">
                                            <i class="fas fa-clock"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>

                                    <?php if ($booking['status'] !== 'Cancelled'): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                        <input type="hidden" name="new_status" value="Cancelled">
                                        <button type="submit" class="btn btn-danger btn-sm btn-status-confirm" data-status="Cancelled" title="Cancel">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>

                                    <!-- View Details -->
                                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#bookingModal<?= $booking['id'] ?>" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>

                                <!-- Modal -->
                                <div class="modal fade" id="bookingModal<?= $booking['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Booking #<?= $booking['id'] ?> Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Customer:</strong> <?= htmlspecialchars($booking['name']) ?></p>
                                                <p><strong>Phone:</strong> <?= htmlspecialchars($booking['phone']) ?></p>
                                                <p><strong>Event Type:</strong> <?= htmlspecialchars($booking['event_type']) ?></p>
                                                <p><strong>Event Date:</strong> <?= date('F d, Y', strtotime($booking['event_date'])) ?></p>
                                                <p><strong>Location:</strong> <?= htmlspecialchars($booking['location']) ?></p>
                                                <p><strong>Package:</strong> <?= $booking['package_name'] ? htmlspecialchars($booking['package_name']) : 'None' ?></p>
                                                <p><strong>Message:</strong> <?= $booking['message'] ? htmlspecialchars($booking['message']) : 'No message' ?></p>
                                                <p><strong>Status:</strong> <span class="<?= $badgeClass ?>"><?= $booking['status'] ?></span></p>
                                                <p><strong>Submitted:</strong> <?= date('M d, Y H:i', strtotime($booking['created_at'])) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center py-4 text-muted">No bookings found.</td></tr>
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
