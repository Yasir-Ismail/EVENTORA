<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../config/db.php';

$success = '';
$error = '';

// Handle Add Package
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $name = trim($_POST['name'] ?? '');
        $price = (float) ($_POST['price'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        if (empty($name) || $price <= 0) {
            $error = 'Package name and a valid price are required.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO packages (name, price, description) VALUES (?, ?, ?)");
            $stmt->execute([$name, $price, $description]);
            $success = 'Package added successfully!';
        }
    }

    if ($_POST['action'] === 'edit') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $price = (float) ($_POST['price'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        if (empty($name) || $price <= 0 || $id <= 0) {
            $error = 'Package name and a valid price are required.';
        } else {
            $stmt = $pdo->prepare("UPDATE packages SET name = ?, price = ?, description = ? WHERE id = ?");
            $stmt->execute([$name, $price, $description, $id]);
            $success = 'Package updated successfully!';
        }
    }

    if ($_POST['action'] === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            // Set package_id to NULL for related bookings before deleting
            $pdo->prepare("UPDATE bookings SET package_id = NULL WHERE package_id = ?")->execute([$id]);
            $stmt = $pdo->prepare("DELETE FROM packages WHERE id = ?");
            $stmt->execute([$id]);
            $success = 'Package deleted successfully!';
        }
    }
}

// Fetch packages
$packages = $pdo->query("SELECT * FROM packages ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Packages - Eventora Admin</title>
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
        <a class="nav-link" href="bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a>
        <a class="nav-link" href="services.php"><i class="fas fa-concierge-bell"></i> Services</a>
        <a class="nav-link active" href="packages.php"><i class="fas fa-box"></i> Packages</a>
        <hr style="border-color: rgba(255,255,255,0.1); margin: 15px 20px;">
        <a class="nav-link" href="../public/index.php"><i class="fas fa-globe"></i> View Website</a>
        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>

<!-- Content -->
<div class="admin-content">
    <div class="admin-header">
        <h2><i class="fas fa-box me-2"></i>Manage Packages</h2>
        <button class="btn btn-purple" data-bs-toggle="modal" data-bs-target="#addPackageModal">
            <i class="fas fa-plus me-1"></i> Add Package
        </button>
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
                        <th>Package Name</th>
                        <th>Price</th>
                        <th>Included Services</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($packages) > 0): ?>
                        <?php foreach ($packages as $package): ?>
                        <tr>
                            <td><?= $package['id'] ?></td>
                            <td><strong><?= htmlspecialchars($package['name']) ?></strong></td>
                            <td>$<?= number_format($package['price'], 2) ?></td>
                            <td>
                                <?php
                                $features = explode("\n", $package['description']);
                                $shown = array_slice($features, 0, 3);
                                echo htmlspecialchars(implode(', ', array_map('trim', $shown)));
                                if (count($features) > 3) echo '...';
                                ?>
                            </td>
                            <td><?= date('M d, Y', strtotime($package['created_at'])) ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editPackageModal<?= $package['id'] ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $package['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm btn-delete-confirm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editPackageModal<?= $package['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Package</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="action" value="edit">
                                            <input type="hidden" name="id" value="<?= $package['id'] ?>">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Package Name</label>
                                                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($package['name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Price ($)</label>
                                                <input type="number" class="form-control" name="price" value="<?= $package['price'] ?>" step="0.01" min="0.01" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Included Services (one per line)</label>
                                                <textarea class="form-control" name="description" rows="5"><?= htmlspecialchars($package['description']) ?></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-purple">Update Package</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">No packages found. Add your first package!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Package Modal -->
<div class="modal fade" id="addPackageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Package</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Package Name</label>
                        <input type="text" class="form-control" name="name" placeholder="e.g., Premium Wedding Package" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Price ($)</label>
                        <input type="number" class="form-control" name="price" placeholder="e.g., 999.99" step="0.01" min="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Included Services (one per line)</label>
                        <textarea class="form-control" name="description" rows="5" placeholder="Stage decoration&#10;Lighting setup&#10;Floral arrangements"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-purple">Add Package</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
