<?php
/**
 * pages/rapports.php — Rapports (#29 #30 #31 #32)
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();
requirePermission('export_donnees');

$annee = (int) ($_GET['annee'] ?? date('Y'));
$date_debut = $_GET['date_debut'] ?? date('Y-01-01');
$date_fin = $_GET['date_fin'] ?? date('Y-m-d');
$formation = (int) ($_GET['formation'] ?? 0);

$mensuel = $pdo->prepare('SELECT MONTH(date_paiement) AS mois, COALESCE(SUM(montant),0) AS total FROM paiement WHERE YEAR(date_paiement)=? GROUP BY mois ORDER BY mois');
$mensuel->execute([$annee]);
$mensuelData = $mensuel->fetchAll();
$moisLabels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
$moisTotaux = array_fill(1, 12, 0);
foreach ($mensuelData as $r) {
    $moisTotaux[$r['mois']] = $r['total'];
}

$parFormation = $pdo->query('SELECT * FROM v_rapport_formation')->fetchAll();
$perso = $pdo->prepare(
    'SELECT COUNT(DISTINCT p.utilisateur_id) AS nb_eleves, COUNT(p.id) AS nb_paiements, COALESCE(SUM(p.montant),0) AS recettes FROM paiement p WHERE p.date_paiement BETWEEN ? AND ?' .
        ($formation ? ' AND EXISTS(SELECT 1 FROM utilisateurs u WHERE u.id=p.utilisateur_id AND u.formation_id=?)' : '')
);
$params = [$date_debut, $date_fin];
if ($formation) {
    $params[] = $formation;
}
$perso->execute($params);
$persoData = $perso->fetch();

$tendance = $pdo->query('SELECT COALESCE(SUM(montant),0) FROM paiement WHERE date_paiement >= DATE_SUB(NOW(), INTERVAL 6 MONTH)')->fetchColumn();
$prevision3m = round(($tendance / 6) * 3, 2);
$prevision6m = round($tendance, 2);
$prevision12m = round(($tendance / 6) * 12, 2);
$moyMens = round($tendance / 6, 2);

$formations = $pdo->query('SELECT id, nom FROM formations ORDER BY id')->fetchAll();
$annees = $pdo->query('SELECT DISTINCT YEAR(date_paiement) AS a FROM paiement ORDER BY a DESC')->fetchAll(PDO::FETCH_COLUMN);
if (empty($annees)) {
    $annees = [date('Y')];
}

$pageTitle = 'Rapports — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="page-header d-flex justify-content-between mb-4">
    <div><h1 class="h4 mb-1"><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Rapports</h1></div>
    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer me-1"></i>Imprimer</button>
</div>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#t1">📅 Mensuel</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#t2">🔍 Personnalisé</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#t3">⚖️ Comparatif</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#t4">📈 Prévisions</button></li>
</ul>

<div class="tab-content">

<div class="tab-pane fade show active" id="t1">
    <div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white d-flex justify-content-between py-3"><h5 class="mb-0">Rapport mensuel</h5><form class="d-flex gap-2"><select name="annee" class="form-select form-select-sm" onchange="this.form.submit()"><?php foreach (
        $annees
        as $a
    ): ?><option value="<?= $a ?>" <?= $a == $annee
    ? 'selected'
    : '' ?>><?= $a ?></option><?php endforeach; ?></select></form></div><div class="card-body"><canvas id="chartMensuel" height="100"></canvas></div></div>
    <div class="card border-0 shadow-sm"><div class="card-body p-0"><table class="table table-hover mb-0"><thead class="table-light"><tr><th class="ps-3">Mois</th><th>Total</th></tr></thead><tbody>
    <?php foreach ($moisLabels as $i => $label):
        $m = $i + 1; ?><tr><td class="ps-3"><?= $label ?></td><td><strong><?= number_format($moisTotaux[$m], 2) ?> $</strong></td></tr><?php
    endforeach; ?>
    <tr class="table-dark"><td class="ps-3 fw-bold">TOTAL</td><td><strong><?= number_format(array_sum($moisTotaux), 2) ?> $</strong></td></tr>
    </tbody></table></div></div>
</div>

<div class="tab-pane fade" id="t2">
    <div class="card border-0 shadow-sm"><div class="card-header bg-white py-3"><h5 class="mb-0">Rapport personnalisé</h5></div><div class="card-body">
        <form method="GET" class="row g-3 mb-4"><input type="hidden" name="annee" value="<?= $annee ?>">
            <div class="col-md-3"><label class="form-label small">Début</label><input type="date" name="date_debut" class="form-control" value="<?= $date_debut ?>"></div>
            <div class="col-md-3"><label class="form-label small">Fin</label><input type="date" name="date_fin" class="form-control" value="<?= $date_fin ?>"></div>
            <div class="col-md-4"><label class="form-label small">Formation</label><select name="formation" class="form-select"><option value="0">Toutes</option><?php foreach (
                $formations
                as $f
            ): ?><option value="<?= $f['id'] ?>" <?= $f['id'] == $formation ? 'selected' : '' ?>><?= htmlspecialchars($f['nom']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-primary w-100">Filtrer</button></div>
        </form>
        <div class="row g-3"><?php foreach (
            [
                ['Élèves', 'nb_eleves', 'people-fill', 'primary'],
                ['Paiements', 'nb_paiements', 'cash', 'success'],
                ['Recettes', 'recettes', 'graph-up', 'info'],
                ['Leçons', 'lecons_ok', 'check-circle', 'warning'],
            ]
            as [$label, $key, $icon, $color]
        ):
            $val =
                $key === 'recettes'
                    ? number_format($persoData[$key] ?? 0, 2) . ' $'
                    : $persoData[$key] ??
                        0; ?><div class="col-md-3"><div class="card border-0 bg-<?= $color ?> bg-opacity-10 text-center p-3"><i class="bi bi-<?= $icon ?> text-<?= $color ?> fs-2 mb-2"></i><h3 class="fw-bold mb-0"><?= $val ?></h3><small class="text-muted"><?= $label ?></small></div></div><?php
        endforeach; ?></div>
    </div></div>
</div>

<div class="tab-pane fade" id="t3">
    <div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white py-3"><h5 class="mb-0">Comparatif par formation</h5></div><div class="card-body"><canvas id="chartCompar" height="100"></canvas></div></div>
    <div class="card border-0 shadow-sm"><div class="card-body p-0"><table class="table table-hover mb-0"><thead class="table-light"><tr><th class="ps-3">Formation</th><th>Inscrits</th><th>Recettes</th><th>Potentiel</th></tr></thead><tbody>
    <?php foreach ($parFormation as $r): ?><tr><td class="ps-3 fw-medium"><?= htmlspecialchars($r['formation']) ?></td><td><?= $r['nb_inscrits'] ?></td><td><?= number_format(
    $r['recettes'],
    2
) ?> $</td><td><?= number_format($r['potentiel'], 2) ?> $</td></tr><?php endforeach; ?>
    </tbody></table></div></div>
</div>

<div class="tab-pane fade" id="t4">
    <div class="card border-0 shadow-sm"><div class="card-header bg-white py-3"><h5 class="mb-0">Prévisions</h5><small class="text-muted">Basées sur 6 derniers mois (moy. <?= number_format(
        $moyMens,
        2
    ) ?> $/mois)</small></div><div class="card-body">
        <div class="row g-3 mb-4"><div class="col-md-4"><div class="card bg-success bg-opacity-10 text-center p-4"><h6>3 mois</h6><h2 class="text-success"><?= number_format(
            $prevision3m,
            2
        ) ?> $</h2></div></div><div class="col-md-4"><div class="card bg-primary bg-opacity-10 text-center p-4"><h6>6 mois</h6><h2 class="text-primary"><?= number_format(
     $prevision6m,
     2
 ) ?> $</h2></div></div><div class="col-md-4"><div class="card bg-warning bg-opacity-10 text-center p-4"><h6>12 mois</h6><h2 class="text-warning"><?= number_format(
     $prevision12m,
     2
 ) ?> $</h2></div></div></div>
        <canvas id="chartPrev" height="100"></canvas>
    </div></div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
const moisLabels = <?= json_encode(array_values($moisLabels)) ?>;
const moisData = <?= json_encode(array_values($moisTotaux)) ?>;
const moyMens = <?= $moyMens ?>;
new Chart(document.getElementById('chartMensuel'),{type:'bar',data:{labels:moisLabels,datasets:[{label:'Recettes ($)',data:moisData,backgroundColor:'#4f46e5',borderRadius:6}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}});
new Chart(document.getElementById('chartCompar'),{type:'bar',data:{labels:<?= json_encode(array_column($parFormation, 'formation')) ?>,datasets:[{label:'Recettes',data:<?= json_encode(
    array_column($parFormation, 'recettes')
) ?>,backgroundColor:'#22c55e',borderRadius:4},{label:'Potentiel',data:<?= json_encode(
    array_column($parFormation, 'potentiel')
) ?>,backgroundColor:'rgba(239,68,68,.3)',borderRadius:4}]},options:{responsive:true,scales:{y:{beginAtZero:true}}}});
const prevMois=['M+1','M+2','M+3','M+4','M+5','M+6','M+7','M+8','M+9','M+10','M+11','M+12'];
new Chart(document.getElementById('chartPrev'),{type:'line',data:{labels:prevMois,datasets:[{label:'Projection ($)',data:prevMois.map((_,i)=>Math.round(moyMens*(i+1)*100)/100),borderColor:'#4f46e5',backgroundColor:'rgba(79,70,229,.1)',fill:true,tension:.4}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}});
</script>
<?php include BASE_PATH . '/includes/footer.php'; ?>
