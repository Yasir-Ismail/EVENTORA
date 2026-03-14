-- Eventora Database Schema
-- Run this SQL in phpMyAdmin or MySQL CLI to set up the database

CREATE DATABASE IF NOT EXISTS eventora_db;
USE eventora_db;

-- Admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Services table
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255) DEFAULT 'default-service.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Packages table
CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    event_type VARCHAR(100) NOT NULL,
    event_date DATE NOT NULL,
    location VARCHAR(255) NOT NULL,
    package_id INT DEFAULT NULL,
    message TEXT,
    status ENUM('Pending', 'Confirmed', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_event_date (event_date),
    INDEX idx_status (status),
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin (password: admin123)
INSERT INTO admins (username, password) VALUES
('admin', '$2y$10$3uBmMy3l4P59MIIEu2Ru2uiGccgO33M3G0PaZPgVdRr4qEd/eTnMS');

-- Insert sample services
INSERT INTO services (name, description, image) VALUES
('Wedding Decoration', 'Transform your wedding venue into a breathtaking paradise with our premium decoration services including floral arrangements, lighting, and themed setups.', 'wedding-decoration.jpg'),
('Birthday Party Setup', 'Make every birthday unforgettable with our creative party setups featuring themed decorations, balloon arrangements, and party essentials.', 'birthday-party.jpg'),
('Mehndi Event Setup', 'Create a vibrant and colorful mehndi celebration with traditional decor, seating arrangements, and cultural touches.', 'mehndi-event.jpg'),
('Corporate Events', 'Professional event management for corporate functions including conferences, seminars, product launches, and team-building events.', 'corporate-event.jpg');

-- Insert sample packages
INSERT INTO packages (name, price, description) VALUES
('Basic Event Package', 499.99, 'Basic decoration setup\nStandard lighting\nChair covers and sashes\nBasic floral arrangements\nEvent coordination'),
('Premium Wedding Package', 1499.99, 'Stage decoration\nPremium lighting setup\nFloral arrangements\nPhotography setup\nBridal room decor\nGuest seating arrangement\nEntrance decor'),
('Corporate Event Package', 999.99, 'Professional stage setup\nAV equipment setup\nBranded backdrop\nRegistration desk\nRefreshment arrangement\nEvent coordination\nParking management');

-- Insert sample bookings
INSERT INTO bookings (name, phone, event_type, event_date, location, package_id, message, status) VALUES
('Sarah Ahmed', '+1 234-567-8901', 'Wedding', DATE_ADD(CURDATE(), INTERVAL 30 DAY), 'Grand Ballroom, City Hotel', 2, 'Looking for a grand wedding setup with 200 guests.', 'Confirmed'),
('Ali Hassan', '+1 234-567-8902', 'Birthday Party', DATE_ADD(CURDATE(), INTERVAL 15 DAY), 'Sunset Garden, Downtown', 1, 'My daughter is turning 5, need a princess theme.', 'Pending'),
('Tech Corp Ltd', '+1 234-567-8903', 'Corporate Event', DATE_ADD(CURDATE(), INTERVAL 45 DAY), 'Convention Center, Business District', 3, 'Annual company meeting for 500 attendees.', 'Pending');
