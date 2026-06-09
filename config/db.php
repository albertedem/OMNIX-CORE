<?php
/**
 * OMNIX OS - Database Configuration
 * Configure your database connection here
 */

// Database Credentials
define('DB_HOST', 'localhost');          // Your hosting domain or localhost
define('DB_USER', 'omnix_user');         // Your database username
define('DB_PASS', 'your_password_here'); // Your database password
define('DB_NAME', 'omnix_core');         // Your database name

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8");

// Enable error reporting for development (disable in production)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

?>
