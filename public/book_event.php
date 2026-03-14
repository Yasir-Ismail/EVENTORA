<?php
require_once __DIR__ . '/../config/db.php';

$success = '';
$error = '';

// Fetch packages for dropdown
$stmt = $pdo->query("SELECT id, name FROM packages ORDER BY name");
$packages = $stmt->fetchAll();

// Pre-select package if passed via URL
$selectedPackage = isset($_GET['package']) ? (int) $_GET['package'] : '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $event_type = trim($_POST['event_type'] ?? '');
    $event_date = trim($_POST['event_date'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $package_id = !empty($_POST['package_id']) ? (int) $_POST['package_id'] : null;
    $message = trim($_POST['message'] ?? '');

    // Validation
    if (empty($name) || empty($phone) || empty($event_type) || empty($event_date) || empty($location)) {
        $error = 'Please fill in all required fields.';
    } elseif (!preg_match('/^[+]?[\d\s\-()]{7,20}$/', $phone)) {
        $error = 'Please enter a valid phone number.';
    } elseif (strtotime($event_date) < strtotime('today')) {
        $error = 'Event date cannot be in the past.';
    } else {
        // Check for date conflict (already confirmed booking on this date)
        $conflictStmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE event_date = ? AND status = 'Confirmed'");
        $conflictStmt->execute([$event_date]);
        $conflictCount = $conflictStmt->fetchColumn();

        if ($conflictCount > 0) {
            $error = 'Sorry, the selected date (' . htmlspecialchars($event_date) . ') already has a confirmed event. Please choose a different date.';
        } else {
            // Insert booking
            $stmt = $pdo->prepare("INSERT INTO bookings (name, phone, event_type, event_date, location, package_id, message, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
            $result = $stmt->execute([$name, $phone, $event_type, $event_date, $location, $package_id, $message]);

            if ($result) {
                $success = 'Your event booking has been submitted successfully! We will review your request and get back to you soon.';
                // Clear form data
                $name = $phone = $event_type = $event_date = $location = $message = '';
                $package_id = null;
                $selectedPackage = '';
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book an Event - Eventora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-eventora fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-star"></i> Event<span>ora</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="packages.php">Packages</a></li>
                <li class="nav-item"><a class="nav-link active" href="book_event.php">Book Event</a></li>
                <li class="nav-item"><a class="nav-link" href="../admin/login.php">Admin</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Book Your Event</h1>
        <p>Fill in the details below and we'll take care of the rest</p>
    </div>
</section>

<!-- Booking Form -->
<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div id="alertContainer">
                    <?php if ($success): ?>
                    <div class="alert alert-success-eventora alert-eventora alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> <?= $success ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                    <div class="alert alert-danger-eventora alert-eventora alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> <?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="booking-form">
                    <h3 class="text-center mb-4" style="color: var(--primary-dark); font-weight: 700;">
                        <i class="fas fa-calendar-plus me-2"></i>Event Booking Form
                    </h3>

                    <form id="bookingForm" method="POST" action="book_event.php">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name" value="<?= htmlspecialchars($name ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="e.g., +1 234-567-8900" value="<?= htmlspecialchars($phone ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="event_type" class="form-label">Event Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="event_type" name="event_type" required>
                                    <option value="">Select Event Type</option>
                                    <option value="Wedding" <?= (isset($event_type) && $event_type === 'Wedding') ? 'selected' : '' ?>>Wedding</option>
                                    <option value="Birthday Party" <?= (isset($event_type) && $event_type === 'Birthday Party') ? 'selected' : '' ?>>Birthday Party</option>
                                    <option value="Mehndi Event" <?= (isset($event_type) && $event_type === 'Mehndi Event') ? 'selected' : '' ?>>Mehndi Event</option>
                                    <option value="Corporate Event" <?= (isset($event_type) && $event_type === 'Corporate Event') ? 'selected' : '' ?>>Corporate Event</option>
                                    <option value="Other" <?= (isset($event_type) && $event_type === 'Other') ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="event_date" class="form-label">Event Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="event_date" name="event_date" value="<?= htmlspecialchars($event_date ?? '') ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="location" class="form-label">Event Location <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="location" name="location" placeholder="Enter event venue / location" value="<?= htmlspecialchars($location ?? '') ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="package_id" class="form-label">Package (Optional)</label>
                                <select class="form-select" id="package_id" name="package_id">
                                    <option value="">No Package Selected</option>
                                    <?php foreach ($packages as $pkg): ?>
                                    <option value="<?= $pkg['id'] ?>" <?= ($selectedPackage == $pkg['id'] || (isset($package_id) && $package_id == $pkg['id'])) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($pkg['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="message" class="form-label">Additional Message</label>
                                <textarea class="form-control" id="message" name="message" rows="4" placeholder="Tell us more about your event requirements..."><?= htmlspecialchars($message ?? '') ?></textarea>
                            </div>
                            <div class="col-12 text-center mt-4">
                                <button type="submit" class="btn btn-eventora btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Booking
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <a href="index.php" class="footer-brand"><i class="fas fa-star"></i> Eventora</a>
                <p class="mt-3">Plan Your Perfect Event. From weddings to corporate gatherings, we bring your vision to life with elegance and precision.</p>
            </div>
            <div class="col-lg-2 col-md-4 mb-4">
                <h5>Quick Links</h5>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="services.php">Services</a></li>
                    <li><a href="packages.php">Packages</a></li>
                    <li><a href="book_event.php">Book Event</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-4 mb-4">
                <h5>Our Services</h5>
                <ul class="footer-links">
                    <li><a href="services.php">Wedding Decoration</a></li>
                    <li><a href="services.php">Birthday Parties</a></li>
                    <li><a href="services.php">Corporate Events</a></li>
                    <li><a href="services.php">Mehndi Events</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-4 mb-4">
                <h5>Contact Us</h5>
                <ul class="footer-links">
                    <li><i class="fas fa-map-marker-alt me-2"></i> 123 Event Street, City</li>
                    <li><i class="fas fa-phone me-2"></i> +1 234-567-8900</li>
                    <li><i class="fas fa-envelope me-2"></i> info@eventora.com</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Eventora. All Rights Reserved. | Plan Your Perfect Event.</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
