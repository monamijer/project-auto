<?php
/**
 * includes/sidebar.php — Navigation principale
 * Utilise getRoleLabel() et getRoleBadgeClass() définis dans auth.php
 */
$currentFile = basename($_SERVER['PHP_SELF']);
$notifCount = isAdmin() ? (int) $pdo->query("SELECT COUNT(*) FROM notifications WHERE destinataire='all' AND lu=0")->fetchColumn() : 0;
$currentRole = $_SESSION['role'] ?? 'stagiaire';

function navLink(string $file, string $icon, string $label, string $current): string
{
    $active = $current === $file ? ' active' : '';
    return sprintf('<li class="nav-item"><a class="nav-link%s" href="%s/pages/%s">' . '<i class="bi %s me-2"></i>%s</a></li>', $active, BASE_URL, $file, $icon, $label);
}

// Récupérer la photo de profil depuis le dossier
$photoUrl = null;
foreach (['jpg', 'jpeg', 'png', 'gif', 'webp'] as $ext) {
    $path = BASE_PATH . '/uploads/profiles/profile_' . $_SESSION['user_id'] . '.' . $ext;
    if (file_exists($path)) {
        $photoUrl = BASE_URL . '/uploads/profiles/profile_' . $_SESSION['user_id'] . '.' . $ext . '?v=' . filemtime($path);
        break;
    }
}

// Messages non lus
$unreadMessages = 0;
try {
    $unreadMessages = (int) $pdo
        ->query(
            "
        SELECT COUNT(*) FROM conversation_participants cp 
        JOIN messages m ON m.conversation_id = cp.conversation_id 
        LEFT JOIN message_reads mr ON mr.message_id = m.id AND mr.utilisateur_id = cp.utilisateur_id
        WHERE cp.utilisateur_id = {$_SESSION['user_id']} 
        AND m.sender_id != {$_SESSION['user_id']} 
        AND mr.id IS NULL AND m.deleted_at IS NULL
    "
        )
        ->fetchColumn();
} catch (Exception $e) {
    $unreadMessages = 0;
}
?>
<nav class="sidebar" id="appSidebar">

    <!-- ── Header sidebar ── -->
    <div class="sidebar-header">
        <div class="d-flex align-items-center justify-content-between">
            <a href="<?= BASE_URL ?>/index.php" class="text-decoration-none text-white fw-bold">
                <i class="bi bi-car-front-fill me-2"></i>Auto École Pro
            </a>
            <button class="btn btn-sm text-white d-md-none p-0"
                    onclick="document.getElementById('appSidebar').classList.remove('show'); document.getElementById('sidebarBackdrop')?.classList.remove('show');">
                <i class="bi bi-x-lg fs-5"></i>
            </button>
        </div>
        <!-- Profil utilisateur -->
        <div class="mt-2 d-flex align-items-center gap-2">
            <a href="<?= BASE_URL ?>/pages/profile.php" style="text-decoration:none;flex-shrink:0;">
                <?php if ($photoUrl): ?>
                <img src="<?= htmlspecialchars($photoUrl) ?>" alt="Photo"
                     style="width:34px;height:34px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,.3);">
                <?php else: ?>
                <div style="width:34px;height:34px;border-radius:50%;background:rgba(255,255,255,.15);
                            display:flex;align-items:center;justify-content:center;color:#fff;
                            font-weight:700;font-size:.9rem;border:2px solid rgba(255,255,255,.3);">
                    <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?>
                </div>
                <?php endif; ?>
            </a>
            <div>
                <div class="text-white small fw-medium" style="line-height:1.2;">
                    <?= htmlspecialchars($_SESSION['username'] ?? '') ?>
                </div>
                <span class="badge <?= getRoleBadgeClass() ?>" style="font-size:.6rem;">
                    <?= getRoleLabel() ?>
                </span>
            </div>
        </div>
    </div>

    <!-- ── Body sidebar ── -->
    <div class="sidebar-body">

        <div class="px-3 py-2">
            <small class="text-white-50 text-uppercase fw-bold" style="font-size:.63rem;letter-spacing:1.5px;">Principal</small>
        </div>
        <ul class="nav flex-column px-2">
            <li class="nav-item">
                <a class="nav-link <?= $currentFile === 'index.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php">
                    <i class="bi bi-grid-1x2-fill me-2"></i>Tableau de bord
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentFile === 'search.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/pages/search.php">
                    <i class="bi bi-search me-2"></i>Recherche
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentFile === 'profile.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/pages/profile.php">
                    <i class="bi bi-person-circle me-2"></i>Mon profil
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentFile === 'chat.php' ? 'active' : '' ?> d-flex justify-content-between align-items-center" href="<?= BASE_URL ?>/pages/chat.php">
                    <span><i class="bi bi-chat-dots me-2"></i>Messages</span>
                    <?php if ($unreadMessages > 0): ?>
                    <span class="badge bg-danger rounded-pill"><?= $unreadMessages ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <?= navLink('calendar.php', 'bi-calendar-week', 'Calendrier', $currentFile) ?>
            <?php if ($notifCount > 0 || isAdmin()): ?>
            <li class="nav-item">
                <a class="nav-link <?= $currentFile === 'notifications.php' ? 'active' : '' ?> d-flex justify-content-between align-items-center"
                   href="<?= BASE_URL ?>/pages/notifications.php">
                    <span><i class="bi bi-bell me-2"></i>Notifications</span>
                    <?php if ($notifCount > 0): ?>
                    <span class="badge bg-danger"><?= $notifCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <?php endif; ?>
        </ul>

        <div class="px-3 py-2 mt-1">
            <small class="text-white-50 text-uppercase fw-bold" style="font-size:.63rem;letter-spacing:1.5px;">Gestion</small>
        </div>
        <ul class="nav flex-column px-2">
            <?= navLink('students.php', 'bi-people', 'Élèves', $currentFile) ?>
            <?php if (in_array($currentRole, ['admin', 'directeur', 'secretaire'])): ?>
            <?= navLink('instructors.php', 'bi-person-badge', 'Moniteurs', $currentFile) ?>
            <?php endif; ?>
            <?php if (in_array($currentRole, ['admin', 'directeur'])): ?>
            <?= navLink('vehicles.php', 'bi-car-front', 'Véhicules', $currentFile) ?>
            <?php endif; ?>
            <?php if (in_array($currentRole, ['admin', 'directeur', 'secretaire', 'moniteur'])): ?>
            <?= navLink('lessons.php', 'bi-calendar-check', 'Leçons', $currentFile) ?>
            <?php endif; ?>
            <?php if (in_array($currentRole, ['admin', 'directeur', 'secretaire', 'caissier'])): ?>
            <?= navLink('payments.php', 'bi-cash', 'Paiements', $currentFile) ?>
            <?php endif; ?>
            <?= navLink('enrollments.php', 'bi-journal-text', 'Inscriptions', $currentFile) ?>
            <?= navLink('exams.php', 'bi-clipboard-check', 'Examens', $currentFile) ?>
            <?php if (hasPermission('gestion_documents')): ?>
            <?= navLink('documents.php', 'bi-file-earmark-arrow-up', 'Documents', $currentFile) ?>
            <?php endif; ?>
        </ul>

        <?php if (hasPermission('export_donnees')): ?>
        <div class="px-3 py-2 mt-1">
            <small class="text-white-50 text-uppercase fw-bold" style="font-size:.63rem;letter-spacing:1.5px;">Rapports</small>
        </div>
        <ul class="nav flex-column px-2">
            <?= navLink('export.php', 'bi-file-earmark-spreadsheet', 'Export Excel', $currentFile) ?>
            <?= navLink('rapport_pdf.php', 'bi-file-earmark-pdf', 'Rapport PDF', $currentFile) ?>
        </ul>
        <?php endif; ?>

        <?php if (hasPermission('gestion_comptes') || hasPermission('voir_parametres')): ?>
        <div class="px-3 py-2 mt-1">
            <small class="text-white-50 text-uppercase fw-bold" style="font-size:.63rem;letter-spacing:1.5px;">Administration</small>
        </div>
        <ul class="nav flex-column px-2">
            <?php if (hasPermission('gestion_comptes')): ?>
            <?= navLink('corbeille.php', 'bi-trash3', 'Corbeille', $currentFile) ?>
            <?php endif; ?>
            <?php if (hasPermission('voir_parametres')): ?>
            <?= navLink('settings.php', 'bi-gear', 'Paramètres', $currentFile) ?>
            <?php endif; ?>
        </ul>
        <?php endif; ?>

    </div>

    <!-- ── Footer sidebar ── -->
    <div class="sidebar-footer">
        <a href="<?= BASE_URL ?>/pages/actions/logout.php" class="btn btn-sm btn-outline-light w-100">
            <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
        </a>
    </div>
</nav>