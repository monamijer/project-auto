<?php
/**
 * includes/sidebar.php — Navigation fixe
 */
$currentFile = basename($_SERVER['PHP_SELF']);
$notifCountSidebar = isAdmin()
    ? (int) $pdo->query("SELECT COUNT(*) FROM notifications WHERE destinataire='all' AND lu=0")->fetchColumn()
    : 0;
?>

<nav class="sidebar" id="appSidebar">
    <!-- Header -->
    <div class="sidebar-header">
        <div class="d-flex align-items-center justify-content-between">
            <a href="<?= BASE_URL ?>/index.php" class="text-decoration-none text-white">
                <i class="bi bi-car-front-fill me-2"></i><span class="fw-bold">Auto École Pro</span>
            </a>
            <button class="btn btn-sm text-white d-md-none p-0" onclick="document.getElementById('appSidebar').classList.remove('show'); document.getElementById('sidebarBackdrop').classList.remove('show');">
                <i class="bi bi-x-lg fs-5"></i>
            </button>
        </div>
        <div class="mt-2 d-flex align-items-center gap-2">
            <span class="badge <?= isAdmin() ? 'bg-warning text-dark' : 'bg-light text-dark' ?>"><?= isAdmin() ? 'Admin' : 'Stagiaire' ?></span>
            <small class="text-white-50"><?= htmlspecialchars($_SESSION['username'] ?? '') ?></small>
        </div>
    </div>

    <!-- Body -->
    <div class="sidebar-body">
        <div class="px-3 py-2"><small class="text-white-50 text-uppercase fw-bold" style="font-size:0.65rem;letter-spacing:1.5px;">Principal</small></div>
        <ul class="nav flex-column px-2">
            <li class="nav-item"><a class="nav-link <?= $currentFile==='index.php'?'active':'' ?>" href="<?= BASE_URL ?>/index.php"><i class="bi bi-grid-1x2-fill me-2"></i>Tableau de bord</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentFile==='search.php'?'active':'' ?>" href="<?= BASE_URL ?>/pages/search.php"><i class="bi bi-search me-2"></i>Recherche</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentFile==='profile.php'?'active':'' ?>" href="<?= BASE_URL ?>/pages/profile.php"><i class="bi bi-person-circle me-2"></i>Mon profil</a></li>
            <?php if (isAdmin()): ?>
            <li class="nav-item">
                <a class="nav-link <?= $currentFile==='notifications.php'?'active':'' ?> d-flex justify-content-between align-items-center" href="<?= BASE_URL ?>/pages/notifications.php">
                    <span><i class="bi bi-bell me-2"></i>Notifications</span>
                    <?php if ($notifCountSidebar > 0): ?><span class="badge bg-danger"><?= $notifCountSidebar ?></span><?php endif; ?>
                </a>
            </li>
            <?php endif; ?>
        </ul>

        <div class="px-3 py-2 mt-2"><small class="text-white-50 text-uppercase fw-bold" style="font-size:0.65rem;letter-spacing:1.5px;">Gestion</small></div>
        <ul class="nav flex-column px-2">
            <li class="nav-item"><a class="nav-link <?= $currentFile==='students.php'?'active':'' ?>" href="<?= BASE_URL ?>/pages/students.php"><i class="bi bi-people me-2"></i>Élèves</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentFile==='instructors.php'?'active':'' ?>" href="<?= BASE_URL ?>/pages/instructors.php"><i class="bi bi-person-badge me-2"></i>Moniteurs</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentFile==='vehicles.php'?'active':'' ?>" href="<?= BASE_URL ?>/pages/vehicles.php"><i class="bi bi-car-front me-2"></i>Véhicules</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentFile==='lessons.php'?'active':'' ?>" href="<?= BASE_URL ?>/pages/lessons.php"><i class="bi bi-calendar-check me-2"></i>Leçons</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentFile==='payments.php'?'active':'' ?>" href="<?= BASE_URL ?>/pages/payments.php"><i class="bi bi-cash me-2"></i>Paiements</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentFile==='enrollments.php'?'active':'' ?>" href="<?= BASE_URL ?>/pages/enrollments.php"><i class="bi bi-journal-text me-2"></i>Inscriptions</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentFile==='exams.php'?'active':'' ?>" href="<?= BASE_URL ?>/pages/exams.php"><i class="bi bi-clipboard-check me-2"></i>Examens</a></li>
            <?php if (hasPermission('gestion_documents')): ?>
            <li class="nav-item"><a class="nav-link <?= $currentFile==='documents.php'?'active':'' ?>" href="<?= BASE_URL ?>/pages/documents.php"><i class="bi bi-file-earmark-arrow-up me-2"></i>Documents</a></li>
            <?php endif; ?>
        </ul>

        <?php if (hasPermission('export_donnees')): ?>
        <div class="px-3 py-2 mt-2"><small class="text-white-50 text-uppercase fw-bold" style="font-size:0.65rem;letter-spacing:1.5px;">Rapports</small></div>
        <ul class="nav flex-column px-2">
            <li class="nav-item"><a class="nav-link <?= $currentFile==='export.php'?'active':'' ?>" href="<?= BASE_URL ?>/pages/export.php"><i class="bi bi-file-earmark-spreadsheet me-2"></i>Export Excel</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentFile==='rapport_pdf.php'?'active':'' ?>" href="<?= BASE_URL ?>/pages/rapport_pdf.php"><i class="bi bi-file-earmark-pdf me-2"></i>Rapport PDF</a></li>
        </ul>
        <?php endif; ?>

        <?php if (hasPermission('gestion_comptes') || hasPermission('voir_parametres')): ?>
        <div class="px-3 py-2 mt-2"><small class="text-white-50 text-uppercase fw-bold" style="font-size:0.65rem;letter-spacing:1.5px;">Administration</small></div>
        <ul class="nav flex-column px-2">
            <?php if (hasPermission('gestion_comptes')): ?>
            <li class="nav-item"><a class="nav-link <?= $currentFile==='corbeille.php'?'active':'' ?>" href="<?= BASE_URL ?>/pages/corbeille.php"><i class="bi bi-trash3 me-2"></i>Corbeille</a></li>
            <?php endif; ?>
            <?php if (hasPermission('voir_parametres')): ?>
            <li class="nav-item"><a class="nav-link <?= $currentFile==='settings.php'?'active':'' ?>" href="<?= BASE_URL ?>/pages/settings.php"><i class="bi bi-gear me-2"></i>Paramètres</a></li>
            <?php endif; ?>
        </ul>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <div class="sidebar-footer">
        <a href="<?= BASE_URL ?>/pages/actions/logout.php" class="btn btn-sm btn-outline-light w-100">
            <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
        </a>
    </div>
</nav>
