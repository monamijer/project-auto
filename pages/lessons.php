<?php
/**
 * pages/lessons.php — Leçons avec détection de conflits horaires
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

$message = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    if (!verify_csrf()) { $error = 'Session expirée, rechargez la page.'; }
    else {
        requirePermission('crud_lecons');
        $msg = callProcedure('CALL sp_planifier_lecon(?,?,?,?,@msg)', [(int)$_POST['student_id'],(int)$_POST['instructor_id'],(int)$_POST['vehicle_id'],$_POST['date_lecon']]);
        if ($msg === 'OK') { $message = 'Leçon planifiée !'; logActivity('AJOUT','lecons'); }
        else { $error = $msg; }
    }
}
if (isset($_GET['complete'])) { requirePermission('crud_lecons'); callProcedure('CALL sp_completer_lecon(?,@msg)',[(int)$_GET['complete']]); $message = 'Leçon marquée effectuée !'; }
if (isset($_GET['cancel'])) { requirePermission('crud_lecons'); callProcedure('CALL sp_annuler_lecon(?,@msg)',[(int)$_GET['cancel']]); $message = 'Leçon annulée.'; }
if (isset($_GET['delete'])) { requirePermission('crud_lecons'); callProcedure('CALL sp_supprimer_lecon(?,@msg)',[(int)$_GET['delete']]); $message = 'Leçon supprimée.'; }

$lessons = $pdo->query('SELECT * FROM v_lecons ORDER BY date_lecon DESC')->fetchAll();
$students = $pdo->query('SELECT * FROM v_eleves_select')->fetchAll();
$instructors = $pdo->query('SELECT * FROM v_moniteurs_select')->fetchAll();
$vehicles = $pdo->query('SELECT * FROM v_vehicules_disponibles')->fetchAll();

$perPage = 10; $page = isset($_GET['page'])?max(1,(int)$_GET['page']):1;
$search = trim($_GET['search']??''); $filter = $_GET['filter']??'';
if ($search!=='') { $lessons = array_values(array_filter($lessons,fn($l)=>stripos($l['student_nom'],$search)!==false||stripos($l['instructor_nom'],$search)!==false||stripos($l['vehicle_nom'],$search)!==false)); }
if ($filter!=='') { $lessons = array_values(array_filter($lessons,fn($l)=>$l['statut']===$filter)); }
$total = count($lessons); $totalPages = ceil($total/$perPage);
$offset = ($page-1)*$perPage; $lessonsPage = array_slice($lessons,$offset,$perPage);

$pageTitle = 'Leçons — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div><h1 class="h3 mb-1"><i class="bi bi-calendar-check me-2 text-primary"></i>Leçons</h1><p class="text-muted mb-0"><?= $total ?> leçon(s)</p></div>
    <?php if (hasPermission('crud_lecons')): ?><button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-calendar-plus me-1"></i>Planifier</button><?php endif; ?>
</div>
<?php if($message):?><div class="alert alert-success alert-dismissible fade show d-flex align-items-center"><i class="bi bi-check-circle-fill me-2"></i><?=htmlspecialchars($message)?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif;?>
<?php if($error):?><div class="alert alert-danger alert-dismissible fade show d-flex align-items-center"><i class="bi bi-exclamation-triangle-fill me-2"></i><?=htmlspecialchars($error)?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif;?>

<div class="card shadow-sm border-0 mb-3"><div class="card-body py-2"><form method="GET" class="row g-2 align-items-center">
    <div class="col-md-4"><div class="input-group input-group-sm"><span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span><input type="text" name="search" class="form-control" placeholder="Rechercher..." value="<?=htmlspecialchars($search)?>"></div></div>
    <div class="col-md-3"><select name="filter" class="form-select form-select-sm"><option value="">Tous les statuts</option><option value="programmée" <?=$filter==='programmée'?'selected':''?>>Programmées</option><option value="effectuée" <?=$filter==='effectuée'?'selected':''?>>Effectuées</option><option value="annulée" <?=$filter==='annulée'?'selected':''?>>Annulées</option></select></div>
    <div class="col-auto"><button type="submit" class="btn btn-sm btn-outline-primary">Filtrer</button><?php if($search||$filter):?><a href="?" class="btn btn-sm btn-outline-secondary">Réinitialiser</a><?php endif;?></div>
</form></div></div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3"><h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Liste des leçons</h5><span class="badge bg-primary rounded-pill"><?=$total?></span></div>
    <div class="card-body p-0"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th class="ps-3">#ID</th><th>Date & Heure</th><th>Élève</th><th>Moniteur</th><th>Véhicule</th><th>Statut</th><th class="text-end pe-3">Actions</th></tr></thead>
        <tbody>
        <?php if(empty($lessonsPage)):?><tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-inbox display-4 d-block mb-2"></i>Aucune leçon</td></tr>
        <?php else: foreach($lessonsPage as $row): $badge=match($row['statut']){'effectuée'=>'bg-success bg-opacity-10 text-success','annulée'=>'bg-danger bg-opacity-10 text-danger',default=>'bg-warning bg-opacity-10 text-warning'};?>
        <tr><td class="ps-3"><span class="badge bg-secondary bg-opacity-10 text-secondary">#<?=$row['id']?></span></td>
            <td><i class="bi bi-clock text-muted me-2"></i><?=date('d/m/Y H:i',strtotime($row['date_lecon']))?></td>
            <td><span class="fw-medium"><?=htmlspecialchars($row['student_nom'])?></span></td>
            <td><?=htmlspecialchars($row['instructor_nom'])?></td><td><small><?=htmlspecialchars($row['vehicle_nom'])?></small></td>
            <td><span class="badge <?=$badge?>"><?=htmlspecialchars($row['statut'])?></span></td>
            <td class="text-end pe-3"><?php if(hasPermission('crud_lecons')&&$row['statut']==='programmée'):?><div class="btn-group btn-group-sm">
                <a href="?complete=<?=$row['id']?>&page=<?=$page?>" class="btn btn-outline-success" onclick="return confirm('Marquer effectuée ?')" title="Effectuée"><i class="bi bi-check-lg"></i></a>
                <a href="?cancel=<?=$row['id']?>&page=<?=$page?>" class="btn btn-outline-warning" onclick="return confirm('Annuler ?')" title="Annuler"><i class="bi bi-x-lg"></i></a>
                <a href="?delete=<?=$row['id']?>&page=<?=$page?>" class="btn btn-outline-danger" onclick="return confirm('Supprimer ?')" title="Supprimer"><i class="bi bi-trash"></i></a>
            </div><?php endif;?></td></tr>
        <?php endforeach; endif;?>
        </tbody>
    </table></div></div>
    <?php if($totalPages>1):?><div class="card-footer bg-white"><nav><ul class="pagination pagination-sm justify-content-center mb-0">
        <li class="page-item <?=$page<=1?'disabled':''?>"><a class="page-link" href="?page=<?=$page-1?>&search=<?=urlencode($search)?>&filter=<?=urlencode($filter)?>">Précédent</a></li>
        <?php for($i=1;$i<=$totalPages;$i++):?><li class="page-item <?=$i===$page?'active':''?>"><a class="page-link" href="?page=<?=$i?>&search=<?=urlencode($search)?>&filter=<?=urlencode($filter)?>"><?=$i?></a></li><?php endfor;?>
        <li class="page-item <?=$page>=$totalPages?'disabled':''?>"><a class="page-link" href="?page=<?=$page+1?>&search=<?=urlencode($search)?>&filter=<?=urlencode($filter)?>">Suivant</a></li>
    </ul></nav></div><?php endif;?>
</div>

<?php if(hasPermission('crud_lecons')):?>
<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="POST">
    <div class="modal-header bg-primary text-white"><h5 class="modal-title"><i class="bi bi-calendar-plus me-2"></i>Planifier une leçon</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="add">
        <div class="mb-3"><label class="form-label">Élève</label><select name="student_id" class="form-select" required><option value="">-- Choisir --</option><?php foreach($students as $s):?><option value="<?=$s['id']?>"><?=htmlspecialchars($s['nom_complet'])?></option><?php endforeach;?></select></div>
        <div class="mb-3"><label class="form-label">Moniteur</label><select name="instructor_id" class="form-select" required><option value="">-- Choisir --</option><?php foreach($instructors as $i):?><option value="<?=$i['id']?>"><?=htmlspecialchars($i['nom_complet'])?></option><?php endforeach;?></select></div>
        <div class="mb-3"><label class="form-label">Véhicule</label><select name="vehicle_id" class="form-select" required><option value="">-- Choisir --</option><?php foreach($vehicles as $v):?><option value="<?=$v['id']?>"><?=htmlspecialchars($v['label'])?></option><?php endforeach;?></select></div>
        <div class="mb-3"><label class="form-label">Date et heure</label><input type="datetime-local" name="date_lecon" class="form-control" required></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Planifier</button></div>
</form></div></div></div>
<?php endif;?>

<?php include BASE_PATH . '/includes/footer.php'; ?>