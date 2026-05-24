# Makazi254 — Real Estate Listing Platform for Kenya

Makazi254 is a web-based real estate listing platform that connects private property sellers and landlords directly with buyers and tenants across Kenya. Sellers pay to list their properties and get linked to genuine buyers without needing an agent.

---

## Project Purpose

This project was built to demonstrate full-stack web development skills using core web technologies without frameworks. It mirrors how real platforms like BuyRentKenya work but is designed specifically for private sellers — ordinary Kenyans selling a shamba or renting out a room — rather than large agencies.

---

## Features

### Public (No login required)
- Browse all approved property listings
- Filter listings by type, location, minimum price, and maximum price
- View full listing details including image, description, price, and location
- Contact seller directly via Call, WhatsApp, or Email
- Submit an inquiry form on any listing

### Sellers
- Register and create a seller account
- Log in and manage listings from a personal dashboard
- Add property listings with image upload
- View all submitted listings and their approval status
- View all inquiries received from interested buyers and tenants
- Secure logout

### Admin
- Log in to a separate admin dashboard
- View platform wide statistics — pending, approved, rejected listings and total sellers
- Approve or reject pending listings
- View and manage all registered sellers
- Delete seller accounts

---

## Technologies Used

| Technology | Purpose |
|------------|---------|
| PHP | Server-side logic, sessions, file uploads |
| MySQL | Database |
| MySQLi | Database connection with prepared statements |
| HTML | Page structure |
| CSS | Styling and layout |
| XAMPP | Local development server |
| Git | Version control |
| GitHub | Code hosting |

---

## Database Schema

Three tables connected through foreign keys.

### `users`
Stores sellers and the admin only. Buyers do not register.
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('seller', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### `properties`
Stores all property listings linked to the seller who created them.
```sql
CREATE TABLE properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    type ENUM('sale', 'rental', 'bnb', 'hostel') NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    location VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### `inquiries`
Stores contact messages from visitors. No account required.
```sql
CREATE TABLE inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
);
```

---

## Project Structure
makazi254/
├── config/
│   └── db.php                  # Database connection
├── includes/
│   ├── navbar.php              # Reusable navigation component
│   └── footer.php              # Reusable footer component
├── seller/
│   ├── dashboard.php           # Seller dashboard with statistics
│   ├── add-listing.php         # Add property listing form
│   ├── my-listings.php         # View all submitted listings
│   └── inquiries.php           # View inquiries from buyers
├── admin/
│   ├── dashboard.php           # Admin dashboard with statistics
│   ├── listings.php            # Approve or reject listings
│   └── users.php               # Manage seller accounts
├── assets/
│   ├── css/
│   │   ├── style.css           # Global styles
│   │   ├── auth.css            # Register and login pages
│   │   ├── dashboard.css       # Dashboard pages
│   │   ├── listing.css         # Listing form and table
│   │   ├── listing-single.css  # Single listing page
│   │   └── home.css            # Homepage
│   └── uploads/                # Uploaded property images
├── index.php                   # Public homepage
├── listing.php                 # Single listing page
├── register.php                # Seller registration
├── login.php                   # Login page
├── logout.php                  # Session destruction
└── README.md                   # Project documentation

---

## Setup Instructions

### Requirements
- XAMPP (PHP 8.0+ and MySQL)
- Web browser
- Git

### Installation

**Step 1 — Clone the repository**
```bash
git clone https://github.com/Ayesa46/makazi254.git
```

**Step 2 — Move to XAMPP**

Move the `makazi254` folder to: C:\xampp\htdocs\

**Step 3 — Create the database**

Open phpMyAdmin at `http://localhost/phpmyadmin` and create a database called `makazi254`. Then run the three CREATE TABLE statements from the schema above.

** Create the config file**

Create `config/db.php` with your database credentials:
```php
<?php
$host = "localhost";
$dbname = "makazi254";
$username = "root";
$password = "";

$conn = mysqli_connect($host, $username, $password, $dbname);

if(!$conn){
    die("Connection failed: " . mysqli_connect_error());
}
?>
```

**Step 5 — Create admin account**

Go to phpMyAdmin, open the `users` table and insert one row:
- name: Admin
- email: admin@makazi254.com
- password: (generate a hash using `password_hash('yourpassword', PASSWORD_DEFAULT)`)
- role: admin

**Step 6 — Run the project**

Start Apache and MySQL in XAMPP. Open your browser and go to:


---

## Security Features

- All database queries use prepared statements preventing SQL injection
- Passwords hashed with `password_hash()` using bcrypt never stored as plain text
- All output sanitised with `htmlspecialchars()` preventing XSS attacks
- File uploads validate MIME type and size server side
- Uploaded files renamed with `uniqid()` preventing filename collisions
- Every protected page checks session and role at the top
- Admin account can only be created directly in phpMyAdmin

---

## Success Criteria

-  Seller can register and log in
-  Seller can add a listing with image upload
-  Seller can view their listings and status
-  Seller can view inquiries from buyers
- Buyer can browse and filter approved listings
- Buyer can view full listing details and contact seller
- Buyer can submit an inquiry without registering
- Admin can approve and reject listings
- Admin can manage seller accounts
-  All forms validate server side
-  All queries use prepared statements
- Project is on GitHub

---

## Author

Prudence Ayesa
Makazi254 — Built as a full-stack web development project
