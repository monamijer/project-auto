<?php
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/mailer.php';

$lecons = $pdo->query("SELECT * FROM v_lecons WHERE statut='programmée' AND date_lecon BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR)")->fetchAll();

foreach ($lecons as $l) {
    $stmt = $pdo->prepare('SELECT email FROM utilisateurs WHERE id = ?');
    $stmt->execute([$l['utilisateur_id']]);
    $email = $stmt->fetchColumn();

    if ($email) {
        sendMail($email, 'Rappel de leçon', '<p>Vous avez une leçon prévue le ' . date('d/m/Y à H:i', strtotime($l['date_lecon'])) . '.</p>');
    }
}

echo count($lecons) . " rappels envoyés.\n";
