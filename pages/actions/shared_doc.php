<?php
/**
 * pages/actions/shared_doc.php — Accès public à un document partagé (#23)
 */
require_once __DIR__ . '/../../config/database.php';

$token = trim($_GET['token'] ?? '');
if (!$token || strlen($token) !== 32) {
    http_response_code(404);
    die('Lien invalide.');
}

$stmt = $pdo->prepare('SELECT * FROM documents WHERE share_token=? AND share_expire > NOW() AND deleted_at IS NULL');
$stmt->execute([$token]);
$doc = $stmt->fetch();

if (!$doc) {
    http_response_code(410);
    die('Lien expiré ou invalide.');
}

$file = BASE_PATH . '/uploads/documents/' . $doc['nom_fichier'];
if (!file_exists($file)) {
    http_response_code(404);
    die('Fichier introuvable.');
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($doc['nom_original']) . '"');
header('Content-Length: ' . filesize($file));
readfile($file);
exit();
