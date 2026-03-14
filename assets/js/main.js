/* Eventora - Main JavaScript */

document.addEventListener('DOMContentLoaded', function () {

    // Navbar scroll effect
    var navbar = document.querySelector('.navbar-eventora');
    if (navbar) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                navbar.classList.add('shadow-lg');
            } else {
                navbar.classList.remove('shadow-lg');
            }
        });
    }

    // Set minimum date for event date field to today
    var eventDateField = document.getElementById('event_date');
    if (eventDateField) {
        var today = new Date().toISOString().split('T')[0];
        eventDateField.setAttribute('min', today);
    }

    // Booking form validation
    var bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function (e) {
            var name = document.getElementById('name').value.trim();
            var phone = document.getElementById('phone').value.trim();
            var eventType = document.getElementById('event_type').value.trim();
            var eventDate = document.getElementById('event_date').value;
            var location = document.getElementById('location').value.trim();

            if (!name || !phone || !eventType || !eventDate || !location) {
                e.preventDefault();
                showAlert('Please fill in all required fields.', 'danger');
                return false;
            }

            var phonePattern = /^[+]?[\d\s\-()]{7,20}$/;
            if (!phonePattern.test(phone)) {
                e.preventDefault();
                showAlert('Please enter a valid phone number.', 'danger');
                return false;
            }

            var selectedDate = new Date(eventDate);
            var now = new Date();
            now.setHours(0, 0, 0, 0);
            if (selectedDate < now) {
                e.preventDefault();
                showAlert('Event date cannot be in the past.', 'danger');
                return false;
            }
        });
    }

    // Admin: Confirm delete actions
    var deleteButtons = document.querySelectorAll('.btn-delete-confirm');
    deleteButtons.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });

    // Admin: Confirm status change
    var statusButtons = document.querySelectorAll('.btn-status-confirm');
    statusButtons.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            var newStatus = this.getAttribute('data-status');
            if (!confirm('Change booking status to "' + newStatus + '"?')) {
                e.preventDefault();
            }
        });
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var targetId = this.getAttribute('href');
            if (targetId !== '#') {
                var target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });

    // Auto-dismiss alerts after 5 seconds
    var alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

function showAlert(message, type) {
    var container = document.getElementById('alertContainer');
    if (!container) return;

    var alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-' + type + ' alert-dismissible fade show';
    alertDiv.setAttribute('role', 'alert');
    alertDiv.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    container.prepend(alertDiv);

    setTimeout(function () {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
