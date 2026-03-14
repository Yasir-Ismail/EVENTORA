<?php
require_once __DIR__ . '/../config/db.php';

// Fetch all packages
$stmt = $pdo->query("SELECT * FROM packages ORDER BY price ASC");
$packages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Packages - Eventora</title>
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
                <li class="nav-item"><a class="nav-link active" href="packages.php">Packages</a></li>
                <li class="nav-item"><a class="nav-link" href="book_event.php">Book Event</a></li>
                <li class="nav-item"><a class="nav-link" href="../admin/login.php">Admin</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Event Packages</h1>
        <p>Choose the perfect package for your event</p>
    </div>
</section>

<!-- Packages -->
<section class="section-padding">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <?php if (count($packages) > 0): ?>
                <?php foreach ($packages as $index => $package): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="package-card <?= $index === 1 ? 'featured' : '' ?>">
                        <h4 class="package-name"><?= htmlspecialchars($package['name']) ?></h4>
                        <div class="package-price">
                            $<?= number_format($package['price'], 2) ?>
                            <span>/ event</span>
                        </div>
                        <ul class="package-features">
                            <?php
                            $features = explode("\n", $package['description']);
                            foreach ($features as $feature):
                                $feature = trim($feature);
                                if ($feature):
                            ?>
                            <li><?= htmlspecialchars($feature) ?></li>
                            <?php endif; endforeach; ?>
                        </ul>
                        <a href="book_event.php?package=<?= $package['id'] ?>" class="btn btn-purple">Book Package</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No Packages Available</h4>
                    <p class="text-muted">Check back soon for our event packages.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <h2>Need a Custom Package?</h2>
        <p>We can create a tailored package for your specific needs and budget.</p>
        <a href="book_event.php" class="btn btn-eventora btn-lg">Contact Us</a>
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
