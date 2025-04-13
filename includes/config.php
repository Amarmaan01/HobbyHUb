<?php
// Strict error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if headers have been sent (for debugging)
if (headers_sent($filename, $linenum)) {
    die("Headers already sent in $filename on line $linenum");
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'hobby_platform');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    // Create PDO connection
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    // Log error and display user-friendly message
    error_log("Connection failed: " . $e->getMessage());
    die("Sorry, there was a problem connecting to the database. Please try again later.");
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to redirect with message
function redirect($location, $message = '', $type = 'info') {
    if ($message) {
        $_SESSION['flash'] = [
            'message' => $message,
            'type' => $type
        ];
    }
    
    // Check if headers have already been sent
    if (headers_sent()) {
        // Fallback using JavaScript if headers already sent
        echo "<script>window.location.href='$location';</script>";
        exit();
    } else {
        header("Location: $location");
        exit();
    }
}