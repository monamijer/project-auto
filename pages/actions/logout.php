<?php
/**
 * pages/actions/logout.php — Déconnexion
 */
session_start();
require_once __DIR__ . '/../../config/database.php';

if (isset($_SESSION['username'])) {
    try {
        $pdo->prepare("INSERT INTO journal_connexions (utilisateur, heure_connexion, statut, message)
                       VALUES (?, NOW(), 'AUTORISÉE', 'Déconnexion')")
            ->execute([$_SESSION['username']]);
    } catch (PDOException $ignored) {}
}

session_destroy();
header('Location: ' . BASE_URL . '/pages/login.php');
exit();
