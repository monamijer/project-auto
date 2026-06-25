<?php
/**
 * pages/actions/logout.php — Déconnexion
 * Journalise via sp_journaliser() .
 */
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/includes/auth.php';

if (isset($_SESSION['username'])) {
    callProcedure('CALL sp_journaliser(?,?,?,@msg)', [$_SESSION['username'], 'AUTORISÉE', 'Déconnexion']);
}

session_destroy();
header('Location: ' . BASE_URL . '/pages/login.php');
exit();
