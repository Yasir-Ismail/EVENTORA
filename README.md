# ⭐ Eventora - Event Management & Booking Website

**Plan Your Perfect Event.**

Eventora is a full-featured event management and booking platform built with PHP, MySQL, and Bootstrap. Customers can browse event services, explore packages, and book event dates online. Admins can manage services, packages, and bookings through a dedicated dashboard.

---

## 🚀 Features

### Customer Features
- **Browse Services** — View all event services (Wedding Decoration, Birthday Parties, Mehndi Events, Corporate Events)
- **Explore Packages** — Compare event packages with pricing and included services
- **Book Events** — Submit booking requests with event details, date, location, and optional package selection
- **Date Conflict Prevention** — System prevents booking on dates that already have confirmed events

### Admin Features
- **Secure Login** — Session-protected admin authentication with password hashing
- **Dashboard** — Overview with total/pending/confirmed bookings and upcoming events
- **Booking Management** — View, confirm, mark pending, or cancel bookings with date conflict warnings
- **Service Management** — Add, edit, and delete event services with image uploads
- **Package Management** — Add, edit, and delete event packages with pricing

### Security
- Password hashing (bcrypt)
- Prepared SQL statements (PDO)
- Input validation and sanitization
- Session-based authentication
- XSS protection with `htmlspecialchars()`

---

## 📁 Folder Structure

```
/eventora
├── /config
│   └── db.php              # Database connection
├── /public
│   ├── index.php           # Homepage
│   ├── services.php        # Services page
│   ├── packages.php        # Packages page
│   └── book_event.php      # Booking form
├── /admin
│   ├── login.php           # Admin login
│   ├── logout.php          # Admin logout
│   ├── dashboard.php       # Admin dashboard
│   ├── bookings.php        # Booking management
│   ├── services.php        # Service management
│   └── packages.php        # Package management
├── /assets
│   ├── /css
│   │   └── style.css       # Custom styles
│   ├── /js
│   │   └── main.js         # JavaScript functionality
│   └── /images             # Uploaded images
├── /database
│   └── schema.sql          # Database schema & seed data
└── README.md
```

---

## 🛠️ Setup Instructions

### Prerequisites
- **XAMPP** or **WAMP** installed
- PHP 7.4+ with PDO extension
- MySQL 5.7+

### Step 1: Clone or Download
```bash
git clone https://github.com/Yasir-Ismail/EVENTORA.git
```

Place the project folder in your web server's root directory:
- **XAMPP**: `C:/xampp/htdocs/`
- **WAMP**: `C:/wamp64/www/`

### Step 2: Create the Database
1. Start Apache and MySQL in XAMPP/WAMP
2. Open **phpMyAdmin** at `http://localhost/phpmyadmin`
3. Import the database schema:
   - Click **Import**
   - Select the file `database/schema.sql`
   - Click **Go**

Or run via MySQL CLI:
```sql
source /path/to/database/schema.sql
```

### Step 3: Configure Database Connection
Edit `config/db.php` if your MySQL credentials differ from the defaults:
```php
$host = 'localhost';
$dbname = 'eventora_db';
$username = 'root';
$password = '';
```

### Step 4: Access the Website
- **Homepage**: `http://localhost/EVENTORA/public/index.php`
- **Admin Login**: `http://localhost/EVENTORA/admin/login.php`

### Default Admin Credentials
| Username | Password |
|----------|----------|
| admin    | admin123 |

---

## 🗄️ Database Schema

### `admins`
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Auto-increment |
| username | VARCHAR(50) | Unique username |
| password | VARCHAR(255) | Bcrypt hashed password |
| created_at | TIMESTAMP | Creation date |

### `services`
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Auto-increment |
| name | VARCHAR(100) | Service name |
| description | TEXT | Service description |
| image | VARCHAR(255) | Image filename |
| created_at | TIMESTAMP | Creation date |

### `packages`
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Auto-increment |
| name | VARCHAR(100) | Package name |
| price | DECIMAL(10,2) | Package price |
| description | TEXT | Included services (one per line) |
| created_at | TIMESTAMP | Creation date |

### `bookings`
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Auto-increment |
| name | VARCHAR(100) | Customer name |
| phone | VARCHAR(20) | Phone number |
| event_type | VARCHAR(100) | Type of event |
| event_date | DATE | Event date |
| location | VARCHAR(255) | Event location |
| package_id | INT (FK) | References packages(id) |
| message | TEXT | Additional message |
| status | ENUM | Pending/Confirmed/Cancelled |
| created_at | TIMESTAMP | Submission date |

---

## 🎨 Design

- **Primary Color**: Purple (#6a1b9a)
- **Accent Color**: Gold (#d4a017)
- **Background**: White (#ffffff)
- **Framework**: Bootstrap 5.3
- **Icons**: Font Awesome 6
- **Responsive**: Mobile-friendly layout

---

## 📋 Tech Stack

| Technology | Usage |
|-----------|-------|
| HTML5 | Structure |
| CSS3 | Custom styling |
| Bootstrap 5 | Responsive UI framework |
| JavaScript | Client-side validation & interactivity |
| PHP (Core) | Server-side logic |
| MySQL | Database |
| PDO | Database abstraction |

---

## 📄 License

This project is open source and available for educational purposes.
