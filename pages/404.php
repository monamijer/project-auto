<?php
http_response_code(404);
// PAS de session_start() ici — c'est ce qui cause la boucle
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page introuvable — Auto École Pro</title>
    <link rel="stylesheet" href="/project_auto/node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/project_auto/node_modules/bootstrap-icons/font/bootstrap-icons.css">
    <style>body{background:#f5f6fa;display:flex;align-items:center;justify-content:center;min-height:100vh;font-family:sans-serif;}</style>
</head>
<body>
<div class="text-center">
    <i class="bi bi-emoji-frown display-1 text-muted mb-3 d-block"></i>
    <h1 class="display-3 fw-bold text-muted">404</h1>
    <p class="lead text-muted mb-4">Page introuvable</p>
    <a href="/project_auto/index.php" class="btn btn-primary btn-lg"><i class="bi bi-house me-2"></i>Accueil</a>
    <a href="/project_auto/pages/login.php" class="btn btn-outline-secondary btn-lg ms-2"><i class="bi bi-box-arrow-in-right me-2"></i>Connexion</a>
</div>
</body>
</html>