<!-- logout.php -->
<?php
/**
 * Logout Page - Destroy user session
 */
session_start();

// Log the logout action
if (isset($_SESSION['username'])) {
    require_once 'config/database.php';
    $username = $_SESSION['username'];
    $log = "INSERT INTO journal_connexions (utilisateur, heure_connexion, statut, message) VALUES (?, NOW(), 'AUTORISÉE', 'User logged out')";
    $stmt = $conn->prepare($log);
    $stmt->bind_param("s", $username);
    $stmt->execute();
}

// Destroy session
session_destroy();

// Redirect to login page
header('Location: login.php');
exit();
?>