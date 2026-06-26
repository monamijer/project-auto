<?php
/**
 * pages/backup.php — Sauvegarde & Restauration (#39 #40)
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin(); requirePermission('gestion_comptes');

$message = ''; $error = '';
$backupDir = BASE_PATH . '/uploads/backups';
if (!is_dir($backupDir)) @mkdir($backupDir, 0755, true);

if (isset($_POST['action']) && $_POST['action'] === 'backup') {
    $nom = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    $fichier = $backupDir . '/' . $nom;
    $cmd = sprintf('mysqldump --user=%s --password=%s --host=%s %s > %s 2>&1', escapeshellarg(DB_USER), escapeshellarg(DB_PASS), escapeshellarg(DB_HOST), escapeshellarg(DB_NAME), escapeshellarg($fichier));
    exec($cmd, $out, $code);
    if ($code === 0 && file_exists($fichier)) {
        $taille = round(filesize($fichier)/1024);
        $message = "Sauvegarde créée : $nom ($taille Ko)";
    } else { $error = 'Erreur mysqldump.'; }
}
if (isset($_GET['dl'])) { $f = $backupDir.'/'.basename($_GET['dl']); if(file_exists($f)){header('Content-Type:application/octet-stream');header('Content-Disposition:attachment;filename="'.basename($f).'"');readfile($f);exit();} }
if (isset($_GET['del'])) { $f = $backupDir.'/'.basename($_GET['del']); if(file_exists($f)){@unlink($f);$message='Sauvegarde supprimée.';} }
if (isset($_POST['action']) && $_POST['action'] === 'restore' && isset($_FILES['sql_file'])) {
    if ($_FILES['sql_file']['error']===UPLOAD_ERR_OK && pathinfo($_FILES['sql_file']['name'],PATHINFO_EXTENSION)==='sql') {
        $tmp = $_FILES['sql_file']['tmp_name'];
        $cmd = sprintf('mysql --user=%s --password=%s --host=%s %s < %s 2>&1', escapeshellarg(DB_USER), escapeshellarg(DB_PASS), escapeshellarg(DB_HOST), escapeshellarg(DB_NAME), escapeshellarg($tmp));
        exec($cmd, $out, $code);
        if ($code===0) $message='Base restaurée !'; else $error='Erreur restauration.';
    } else { $error = 'Fichier .sql requis.'; }
}

$fichiers = array_reverse(glob($backupDir.'/*.sql') ?: []);
$pageTitle = 'Backup — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="page-header mb-4"><h1 class="h4 mb-1"><i class="bi bi-cloud-arrow-up me-2 text-primary"></i>Sauvegarde</h1><p class="text-muted mb-0"><?= count($fichiers) ?> fichier(s)</p></div>
<?php if($message):?><div class="alert alert-success"><?=$message?></div><?php endif;?>
<?php if($error):?><div class="alert alert-danger"><?=$error?></div><?php endif;?>

<div class="row g-4 mb-4">
    <div class="col-md-6"><div class="card shadow-sm border-0"><div class="card-header bg-white py-3"><h5 class="mb-0"><i class="bi bi-download me-2 text-success"></i>Créer</h5></div><div class="card-body">
        <form method="POST"><input type="hidden" name="action" value="backup"><button type="submit" class="btn btn-success w-100">Lancer la sauvegarde</button></form>
    </div></div></div>
    <div class="col-md-6"><div class="card shadow-sm border-0 border-danger border-opacity-25"><div class="card-header bg-white py-3"><h5 class="mb-0"><i class="bi bi-upload me-2 text-danger"></i>Restaurer</h5></div><div class="card-body">
        <div class="alert alert-danger small mb-3">⚠️ Écrase toutes les données.</div>
        <form method="POST" enctype="multipart/form-data"><input type="hidden" name="action" value="restore"><div class="mb-3"><input type="file" name="sql_file" class="form-control" accept=".sql" required></div><button type="submit" class="btn btn-danger w-100" onclick="return confirm('Confirmer ?')">Restaurer</button></form>
    </div></div></div>
</div>

<div class="card shadow-sm border-0"><div class="card-header bg-white py-3"><h5 class="mb-0">Fichiers disponibles</h5></div><div class="card-body p-0"><table class="table table-hover mb-0"><thead class="table-light"><tr><th class="ps-3">Fichier</th><th>Taille</th><th>Date</th><th></th></tr></thead><tbody>
<?php foreach($fichiers as $f): $bn=basename($f); ?><tr><td class="ps-3"><?= htmlspecialchars($bn) ?></td><td><?= round(filesize($f)/1024,1) ?> Ko</td><td><?= date('d/m/Y H:i',filemtime($f)) ?></td><td><a href="?dl=<?= urlencode($bn) ?>" class="btn btn-sm btn-outline-success"><i class="bi bi-download"></i></a> <a href="?del=<?= urlencode($bn) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ?')"><i class="bi bi-trash"></i></a></td></tr><?php endforeach; ?>
<?php if(empty($fichiers)):?><tr><td colspan="4" class="text-center text-muted py-4">Aucune sauvegarde.</td></tr><?php endif;?>
</tbody></table></div></div>
<?php include BASE_PATH . '/includes/footer.php'; ?>