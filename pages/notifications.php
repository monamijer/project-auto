<?php
/**
 * pages/notifications.php — Notifications internes (admin)
 * SELECT → v_notifications_admin | Actions → sp_marquer_notification_lue(s)
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();
requirePermission('voir_parametres'); // réservé aux admins

if (isset($_GET['lue'])) {
    callProcedure("CALL sp_marquer_notification_lue(?,@msg)", [(int)$_GET['lue']]);
}
if (isset($_GET['tout_lire'])) {
    callProcedure("CALL sp_marquer_toutes_notifications_lues(?,@msg)", ['all']);
}

$notifications = $pdo->query("SELECT * FROM v_notifications_admin")->fetchAll();

$pageTitle = 'Notifications — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="h2"><i class="bi bi-bell me-2"></i>Notifications</h1>
    <a href="?tout_lire=1" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-check2-all"></i> Tout marquer comme lu
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            <?php foreach ($notifications as $n): ?>
            <div class="list-group-item d-flex justify-content-between align-items-start <?= !$n['lu']?'bg-light':'' ?>">
                <div>
                    <h6 class="mb-1">
                        <?php if (!$n['lu']): ?><span class="badge bg-primary me-1">Nouveau</span><?php endif; ?>
                        <?= htmlspecialchars($n['titre']) ?>
                    </h6>
                    <p class="mb-1 text-muted"><?= htmlspecialchars($n['message']) ?></p>
                    <small class="text-muted"><?= htmlspecialchars($n['date_creation']) ?></small>
                </div>
                <div class="text-nowrap ms-2">
                    <?php if (!empty($n['lien'])): ?>
                    <a href="<?= BASE_URL . htmlspecialchars($n['lien']) ?>" class="btn btn-sm btn-info"><i class="bi bi-box-arrow-up-right"></i></a>
                    <?php endif; ?>
                    <?php if (!$n['lu']): ?>
                    <a href="?lue=<?= $n['id'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-check"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($notifications)): ?>
            <div class="text-center text-muted py-5">Aucune notification.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>
