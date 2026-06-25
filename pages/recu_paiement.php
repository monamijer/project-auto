<?php
/**
 * pages/recu_paiement.php — Reçu de paiement imprimable
 * Utilise la vue v_recu_paiement
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

$paiementId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM v_recu_paiement WHERE id = ?");
$stmt->execute([$paiementId]);
$recu = $stmt->fetch();

if (!$recu) {
    header('Location: ' . BASE_URL . '/pages/payments.php');
    exit();
}

// Config école
$configStmt = $pdo->query("SELECT cle, valeur FROM config_systeme");
$config = $configStmt->fetchAll(PDO::FETCH_KEY_PAIR);

$pageTitle = 'Reçu #' . $recu['id'] . ' — ' . $recu['nom_complet'];
include BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <h1 class="h4 mb-0"><i class="bi bi-receipt me-2"></i>Reçu de paiement</h1>
    <div>
        <button onclick="window.print()" class="btn btn-primary btn-sm">
            <i class="bi bi-printer me-1"></i>Imprimer
        </button>
        <a href="<?= BASE_URL ?>/pages/payments.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Retour
        </a>
    </div>
</div>

<div class="card shadow-sm border-0" id="recu-print">
    <div class="card-body p-4">
        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col-6">
                <h3 class="fw-bold mb-1"><?= htmlspecialchars($config['nom_ecole'] ?? 'Auto École Pro') ?></h3>
                <p class="text-muted mb-0 small"><?= htmlspecialchars($config['adresse'] ?? '') ?></p>
                <p class="text-muted mb-0 small"><?= htmlspecialchars($config['telephone'] ?? '') ?></p>
                <p class="text-muted mb-0 small"><?= htmlspecialchars($config['email'] ?? '') ?></p>
            </div>
            <div class="col-6 text-end">
                <h2 class="text-uppercase text-muted fw-bold">Reçu</h2>
                <p class="mb-0"><strong>N° <?= str_pad($recu['id'], 5, '0', STR_PAD_LEFT) ?></strong></p>
                <p class="text-muted small">Date : <?= date('d/m/Y', strtotime($recu['date_paiement'])) ?></p>
            </div>
        </div>

        <hr>

        <!-- Infos élève -->
        <div class="row mb-3">
            <div class="col-6">
                <small class="text-muted text-uppercase">Reçu de</small>
                <p class="fw-medium mb-0"><?= htmlspecialchars($recu['nom_complet']) ?></p>
                <p class="text-muted small mb-0"><?= htmlspecialchars($recu['email']) ?></p>
                <p class="text-muted small mb-0"><?= htmlspecialchars($recu['telephone'] ?? '') ?></p>
            </div>
            <div class="col-6 text-end">
                <small class="text-muted text-uppercase">Formation</small>
                <p class="fw-medium mb-0"><?= htmlspecialchars($recu['formation_nom']) ?></p>
                <p class="text-muted small mb-0">Prix total : <?= number_format($recu['formation_prix'], 2) ?> <?= htmlspecialchars($config['devise'] ?? '$') ?></p>
            </div>
        </div>

        <!-- Détail paiement -->
        <div class="bg-light rounded-3 p-3 mb-3">
            <div class="row align-items-center">
                <div class="col-4">
                    <small class="text-muted d-block">Montant payé</small>
                    <span class="fs-4 fw-bold text-success">
                        <?= number_format($recu['montant'], 2) ?> <?= htmlspecialchars($config['devise'] ?? '$') ?>
                    </span>
                </div>
                <div class="col-4">
                    <small class="text-muted d-block">Mode de paiement</small>
                    <span class="fw-medium"><?= htmlspecialchars($recu['methode']) ?></span>
                </div>
                <div class="col-4">
                    <small class="text-muted d-block">Date</small>
                    <span class="fw-medium"><?= date('d/m/Y', strtotime($recu['date_paiement'])) ?></span>
                </div>
            </div>
        </div>

        <hr>

        <!-- Signature -->
        <div class="row mt-4">
            <div class="col-6">
                <p class="text-muted small mb-1">Signature de l'élève</p>
                <div style="border-bottom:1px solid #ccc;width:150px;margin-top:40px;"></div>
            </div>
            <div class="col-6 text-end">
                <p class="text-muted small mb-1">Cachet de l'école</p>
                <div style="border-bottom:1px solid #ccc;width:150px;margin-top:40px;margin-left:auto;"></div>
            </div>
        </div>

        <p class="text-muted small text-center mt-4 mb-0">
            Document généré le <?= date('d/m/Y à H:i') ?> — Auto École Pro
        </p>
    </div>
</div>

<style>
@media print {
    body { background: #fff !important; }
    .sidebar, .mobile-topbar, .sidebar-backdrop, .no-print, .main-content { 
        margin-left: 0 !important; padding: 0 !important; 
    }
    .no-print { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>

<?php include BASE_PATH . '/includes/footer.php'; ?>