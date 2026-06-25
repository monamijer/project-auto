<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

$paymentId = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT p.*, u.nom, u.prenom, f.nom AS formation_nom FROM paiement p JOIN utilisateurs u ON u.id = p.utilisateur_id JOIN formations f ON f.id = u.formation_id WHERE p.id = ?');
$stmt->execute([$paymentId]);
$payment = $stmt->fetch();
if (!$payment) {
    die('Paiement introuvable');
}

$pageTitle = 'Reçu #' . $paymentId;
include BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4"><i class="bi bi-receipt me-2"></i>Reçu de paiement #<?= $paymentId ?></h1>
    <button class="btn btn-primary btn-sm" onclick="generatePDF()"><i class="bi bi-printer me-1"></i>Imprimer / PDF</button>
</div>

<div class="card shadow-sm border-0" id="receipt">
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <h4 class="text-primary mb-0">Auto École Pro</h4>
            <small class="text-muted">Reçu de paiement</small>
        </div>
        <hr>
        <table class="table table-borderless">
            <tr><td class="text-muted">Élève</td><td class="fw-bold"><?= htmlspecialchars($payment['prenom'] . ' ' . $payment['nom']) ?></td></tr>
            <tr><td class="text-muted">Formation</td><td><?= htmlspecialchars($payment['formation_nom']) ?></td></tr>
            <tr><td class="text-muted">Date</td><td><?= date('d/m/Y', strtotime($payment['date_paiement'])) ?></td></tr>
            <tr><td class="text-muted">Mode</td><td><?= htmlspecialchars($payment['methode']) ?></td></tr>
        </table>
        <div class="text-center my-3">
            <h2 class="text-primary"><?= number_format($payment['montant'], 2) ?> $</h2>
        </div>
        <hr>
        <p class="text-center text-muted small">Document généré le <?= date('d/m/Y à H:i') ?></p>
    </div>
</div>

<script src="<?= BASE_URL ?>/node_modules/jspdf/dist/jspdf.umd.min.js"></script>
<script>
function generatePDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    const receipt = document.getElementById('receipt');
    
    doc.html(receipt, {
        callback: function(doc) {
            doc.save('recu_<?= $paymentId ?>.pdf');
        },
        x: 10,
        y: 10,
        width: 180,
        windowWidth: 800
    });
}
</script>

<?php include BASE_PATH . '/includes/footer.php'; ?>
