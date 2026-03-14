<?php
require_once __DIR__ . '/../config/db.php';

// Fetch services
$stmt = $pdo->query("SELECT * FROM services ORDER BY created_at DESC LIMIT 4");
$services = $stmt->fetchAll();

// Fetch packages
$stmt = $pdo->query("SELECT * FROM packages ORDER BY price ASC");
$packages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventora - Plan Your Perfect Event</title>
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
                <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="packages.php">Packages</a></li>
                <li class="nav-item"><a class="nav-link" href="book_event.php">Book Event</a></li>
                <li class="nav-item"><a class="nav-link" href="../admin/login.php">Admin</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <h1>Create Unforgettable Events<br>with <span style="color: #f0c948;">Eventora</span></h1>
        <p class="lead">Plan Your Perfect Event — From weddings to corporate gatherings, we bring your vision to life.</p>
        <a href="book_event.php" class="btn btn-eventora btn-lg">Book Your Event</a>
    </div>
</section>

<!-- Services Section -->
<section class="section-padding" id="services">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title">Our Services</h2>
            <div class="section-divider"></div>
            <p class="section-subtitle">We offer a wide range of professional event planning services</p>
        </div>
        <div class="row g-4">
            <?php if (count($services) > 0): ?>
                <?php foreach ($services as $service): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="card service-card">
                        <div class="card-img-wrapper">
                            <?php if ($service['image'] && $service['image'] !== 'default-service.jpg'): ?>
                                <img src="../assets/images/<?= htmlspecialchars($service['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($service['name']) ?>">
                            <?php else: ?>
                                <div class="service-img-placeholder">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($service['name']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars(substr($service['description'], 0, 100)) . (strlen($service['description']) > 100 ? '...' : '') ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">No services available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
        <div class="text-center mt-4">
            <a href="services.php" class="btn btn-eventora-outline">View All Services</a>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section class="section-padding gallery-section" id="gallery">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title">Event Gallery</h2>
            <div class="section-divider"></div>
            <p class="section-subtitle">A glimpse into our beautifully managed events</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="gallery-item">
                    <div style="height: 280px; background: linear-gradient(135deg, #9c4dcc, #6a1b9a); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; border-radius: 15px;">
                        <i class="fas fa-ring"></i>
                    </div>
                    <div class="gallery-overlay"><span>Elegant Wedding Setup</span></div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="gallery-item">
                    <div style="height: 280px; background: linear-gradient(135deg, #d4a017, #f0c948); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; border-radius: 15px;">
                        <i class="fas fa-birthday-cake"></i>
                    </div>
                    <div class="gallery-overlay"><span>Birthday Celebration</span></div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="gallery-item">
                    <div style="height: 280px; background: linear-gradient(135deg, #4a148c, #7b1fa2); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; border-radius: 15px;">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="gallery-overlay"><span>Corporate Conference</span></div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="gallery-item">
                    <div style="height: 280px; background: linear-gradient(135deg, #6a1b9a, #d4a017); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; border-radius: 15px;">
                        <i class="fas fa-hand-sparkles"></i>
                    </div>
                    <div class="gallery-overlay"><span>Mehndi Night</span></div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="gallery-item">
                    <div style="height: 280px; background: linear-gradient(135deg, #f0c948, #9c4dcc); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; border-radius: 15px;">
                        <i class="fas fa-glass-cheers"></i>
                    </div>
                    <div class="gallery-overlay"><span>Reception Party</span></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Packages Section -->
<section class="section-padding" id="packages">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title">Our Packages</h2>
            <div class="section-divider"></div>
            <p class="section-subtitle">Choose the perfect package for your event</p>
        </div>
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
                        <a href="book_event.php?package=<?= $package['id'] ?>" class="btn btn-purple">Book Now</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">No packages available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h2>Plan Your Dream Event With Our Expert Team</h2>
        <p>From intimate gatherings to grand celebrations, Eventora makes every moment special.</p>
        <a href="book_event.php" class="btn btn-eventora btn-lg">Book Event Now</a>
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
