<?php
/**
 * includes/sidebar.php — Navigation fixe
 */
$currentFile = basename($_SERVER['PHP_SELF']);
$notifCountSidebar = isAdmin()
    ? (int) $pdo->query("SELECT COUNT(*) FROM notifications WHERE destinataire='all' AND lu=0")->fetchColumn()
    : 0;

// Messages non lus
$unreadMessages = (int) $pdo->query("
    SELECT COUNT(*) FROM conversation_participants cp 
    JOIN messages m ON m.conversation_id = cp.conversation_id 
    LEFT JOIN message_reads mr ON mr.message_id = m.id AND mr.utilisateur_id = cp.utilisateur_id
    WHERE cp.utilisateur_id = {$_SESSION['user_id']} 
    AND m.sender_id != {$_SESSION['user_id']} 
    AND mr.id IS NULL 
    AND m.deleted_at IS NULL
")->fetchColumn();

// Vérifier la photo de profil
$photoExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$sidebarPhoto = null;
foreach ($photoExtensions as $ext) {
    $path = BASE_PATH . '/uploads/profiles/profile_' . $_SESSION['user_id'] . '.' . $ext;
    if (file_exists($path)) {
        $sidebarPhoto = BASE_URL . '/uploads/profiles/profile_' . $_SESSION['user_id'] . '.' . $ext . '?v=' . filemtime($path);
        break;
    }
}
?>

<nav class="sidebar" id="appSidebar">
    <!-- Header -->
    <div class="sidebar-header">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <a href="<?= BASE_URL ?>/index.php" class="text-decoration-none text-white">
                <i class="bi bi-car-front-fill me-2"></i><span class="fw-bold">Auto École Pro</span>
            </a>
            <button class="btn btn-sm text-white d-md-none p-0" onclick="document.getElementById('appSidebar').classList.remove('show'); document.getElementById('sidebarBackdrop').classList.remove('show');">
                <i class="bi bi-x-lg fs-5"></i>
            </button>
        </div>
        <a href="<?= BASE_URL ?>/pages/profile.php" class="text-decoration-none">
            <div class="d-flex align-items-center gap-2 mt-2">
                <?php if ($sidebarPhoto): ?>
                    <img src="<?= $sidebarPhoto ?>" alt="Photo" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;border:2px solid rgba(255,255,255,0.3);">
                <?php else: ?>
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                        <span class="text-white fw-bold small"><?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?></span>
                    </div>
                <?php endif; ?>
                <div>
                    <div class="text-white small fw-medium lh-sm"><?= htmlspecialchars($_SESSION['username'] ?? '') ?></div>
                    <span class="badge <?= isAdmin() ? 'bg-warning text-dark' : 'bg-light text-dark' ?> mt-1" style="font-size:0.6rem;"><?= isAdmin() ? 'Admin' : 'Stagiaire' ?></span>
                </div>
            </div>
        </a>
    </div>

    <!-- Body -->
    <div class="sidebar-body">
        <div class="px-3 py-2"><small class="text-white-50 text-uppercase fw-bold" style="font-size:0.65rem;letter-spacing:1.5px;">Principal</small></div>
        <ul class="nav flex-column px-2">
            <li class="nav-item"><a class="nav-link <?= $currentFile==='index.php'?'active':'' ?>" href="<?= BASE_URL ?>/index.php"><i class="bi bi-grid-1x2-fill me-2"></i>Tableau de bord</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentFile==='search.php'?'active':'' ?>" href="<?= BASE_URL ?>/pages/search.php"><i class="bi bi-search me-2"></i>Recherche</a></li>
            <li class="nav-item">
                <a class="nav-link <?= $currentFile==='chat.php'?'active':'' ?> d-flex justify-content-between align-items-center" href="<?= BASE_URL ?>/pages/chat.php">
                    <span><i class="bi bi-chat-dots me-2"></i>Messages</span>
                    <?php if ($unreadMessages > 0): ?><span class="badge bg-danger rounded-pill"><?= $unreadMessages ?></span><?php endif; ?>
                </a>
            </li>
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