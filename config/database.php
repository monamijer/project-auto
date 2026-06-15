<!-- config/database.php -->
<?php
/**
 * Database Configuration File
 * Contains database connection settings and connection function
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'jer000');
define('DB_NAME', 'jerome_auto_ecole');

/**
 * Create database connection
 * @return mysqli Connection object
 */
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to UTF-8
    $conn->set_charset("utf8mb4");
    
    return $conn;
}

// Create connection instance
$conn = getConnection();
?>