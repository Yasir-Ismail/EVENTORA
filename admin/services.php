<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../config/db.php';

$success = '';
$error = '';

// Handle Add Service
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $image = 'default-service.jpg';

        if (empty($name)) {
            $error = 'Service name is required.';
        } else {
            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, $allowed)) {
                    $image = 'service-' . time() . '.' . $ext;
                    $uploadPath = __DIR__ . '/../assets/images/' . $image;
                    move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath);
                }
            }

            $stmt = $pdo->prepare("INSERT INTO services (name, description, image) VALUES (?, ?, ?)");
            $stmt->execute([$name, $description, $image]);
            $success = 'Service added successfully!';
        }
    }

    if ($_POST['action'] === 'edit') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name) || $id <= 0) {
            $error = 'Service name is required.';
        } else {
            // Handle image upload
            $newImage = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, $allowed)) {
                    $newImage = 'service-' . time() . '.' . $ext;
                    $uploadPath = __DIR__ . '/../assets/images/' . $newImage;
                    move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath);
                }
            }

            if ($newImage) {
                $stmt = $pdo->prepare("UPDATE services SET name = ?, description = ?, image = ? WHERE id = ?");
                $stmt->execute([$name, $description, $newImage, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE services SET name = ?, description = ? WHERE id = ?");
                $stmt->execute([$name, $description, $id]);
            }
            $success = 'Service updated successfully!';
        }
    }

    if ($_POST['action'] === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
            $stmt->execute([$id]);
            $success = 'Service deleted successfully!';
        }
    }
}

// Fetch services
$services = $pdo->query("SELECT * FROM services ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - Eventora Admin</title>
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
        <a class="nav-link active" href="services.php"><i class="fas fa-concierge-bell"></i> Services</a>
        <a class="nav-link" href="packages.php"><i class="fas fa-box"></i> Packages</a>
        <hr style="border-color: rgba(255,255,255,0.1); margin: 15px 20px;">
        <a class="nav-link" href="../public/index.php"><i class="fas fa-globe"></i> View Website</a>
        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>

<!-- Content -->
<div class="admin-content">
    <div class="admin-header">
        <h2><i class="fas fa-concierge-bell me-2"></i>Manage Services</h2>
        <button class="btn btn-purple" data-bs-toggle="modal" data-bs-target="#addServiceModal">
            <i class="fas fa-plus me-1"></i> Add Service
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
                        <th>Image</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($services) > 0): ?>
                        <?php foreach ($services as $service): ?>
                        <tr>
                            <td><?= $service['id'] ?></td>
                            <td>
                                <?php if ($service['image'] && $service['image'] !== 'default-service.jpg'): ?>
                                    <img src="../assets/images/<?= htmlspecialchars($service['image']) ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                <?php else: ?>
                                    <div style="width: 50px; height: 50px; background: var(--primary-light); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($service['name']) ?></strong></td>
                            <td><?= htmlspecialchars(substr($service['description'], 0, 80)) . (strlen($service['description']) > 80 ? '...' : '') ?></td>
                            <td><?= date('M d, Y', strtotime($service['created_at'])) ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editServiceModal<?= $service['id'] ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $service['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm btn-delete-confirm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editServiceModal<?= $service['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Service</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <input type="hidden" name="action" value="edit">
                                            <input type="hidden" name="id" value="<?= $service['id'] ?>">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Service Name</label>
                                                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($service['name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Description</label>
                                                <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($service['description']) ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Image (optional)</label>
                                                <input type="file" class="form-control" name="image" accept="image/*">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-purple">Update Service</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">No services found. Add your first service!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Service Modal -->
<div class="modal fade" id="addServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Service Name</label>
                        <input type="text" class="form-control" name="name" placeholder="e.g., Wedding Decoration" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Describe the service..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Image (optional)</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-purple">Add Service</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
