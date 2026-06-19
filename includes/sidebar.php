<?php
/**
 * includes/sidebar.php — Navigation (responsive off-canvas sur mobile)
 */
$currentFile = basename($_SERVER['PHP_SELF']);
?>
<nav class="col-md-3 col-lg-2 d-md-block sidebar" id="appSidebar">
    <div class="brand d-flex justify-content-between align-items-center">
        <div>
            <h5><i class="bi bi-car-front-fill"></i> Auto École Pro</h5>
            <small>
                <?= htmlspecialchars($_SESSION['username'] ?? '') ?>
                <span class="badge <?= isAdmin() ? 'bg-warning text-dark' : 'bg-secondary' ?> ms-1">
                    <?= isAdmin() ? 'Admin' : 'Stagiaire' ?>
                </span>
            </small>
        </div>
        <!-- Bouton fermer, visible uniquement en mode mobile (off-canvas) -->
        <button class="btn-close btn-close-white d-md-none"
                onclick="document.getElementById('appSidebar').classList.remove('show'); document.getElementById('sidebarBackdrop').classList.remove('show');"></button>
    </div>

    <div class="position-sticky pt-2">
        <ul class="nav flex-column px-2">

            <li class="nav-item">
                <a class="nav-link <?= $currentFile==='index.php' ? 'active':'' ?>" href="<?= BASE_URL ?>/index.php">
                    <i class="bi bi-speedometer2 me-2"></i> Tableau de bord
                </a>
            </li>

            <!-- Recherche globale -->
            <li class="nav-item">
                <a class="nav-link <?= $currentFile==='search.php' ? 'active':'' ?>" href="<?= BASE_URL ?>/pages/search.php">
                    <i class="bi bi-search me-2"></i> Recherche
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $currentFile==='students.php' ? 'active':'' ?>" href="<?= BASE_URL ?>/pages/students.php">
                    <i class="bi bi-people me-2"></i> Élèves
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentFile==='instructors.php' ? 'active':'' ?>" href="<?= BASE_URL ?>/pages/instructors.php">
                    <i class="bi bi-person-badge me-2"></i> Moniteurs
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentFile==='vehicles.php' ? 'active':'' ?>" href="<?= BASE_URL ?>/pages/vehicles.php">
                    <i class="bi bi-car-front me-2"></i> Véhicules
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentFile==='lessons.php' ? 'active':'' ?>" href="<?= BASE_URL ?>/pages/lessons.php">
                    <i class="bi bi-calendar-event me-2"></i> Leçons
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentFile==='payments.php' ? 'active':'' ?>" href="<?= BASE_URL ?>/pages/payments.php">
                    <i class="bi bi-cash-stack me-2"></i> Paiements
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentFile==='enrollments.php' ? 'active':'' ?>" href="<?= BASE_URL ?>/pages/enrollments.php">
                    <i class="bi bi-journal-bookmark-fill me-2"></i> Inscriptions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentFile==='exams.php' ? 'active':'' ?>" href="<?= BASE_URL ?>/pages/exams.php">
                    <i class="bi bi-file-text me-2"></i> Examens
                </a>
            </li>

            <?php if (hasPermission('gestion_comptes')): ?>
            <!-- Corbeille : admin uniquement -->
            <li class="nav-item">
                <a class="nav-link <?= $currentFile==='corbeille.php' ? 'active':'' ?>" href="<?= BASE_URL ?>/pages/corbeille.php">
                    <i class="bi bi-trash3 me-2"></i> Corbeille
                </a>
            </li>
            <?php endif; ?>

            <?php if (hasPermission('voir_parametres')): ?>
            <li class="nav-item">
                <a class="nav-link <?= $currentFile==='settings.php' ? 'active':'' ?>" href="<?= BASE_URL ?>/pages/settings.php">
                    <i class="bi bi-gear me-2"></i> Paramètres
                </a>
            </li>
            <?php endif; ?>

            <li class="nav-item mt-3">
                <a class="nav-link text-danger" href="<?= BASE_URL ?>/pages/actions/logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i> Déconnexion
                </a>
            </li>
        </ul>
    </div>
</nav>
