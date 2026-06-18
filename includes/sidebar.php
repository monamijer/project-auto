<?php
/**
 * includes/sidebar.php — Navigation latérale avec contrôle des rôles
 * Les items CRUD sont masqués pour les stagiaires.
 * isAdmin() et hasPermission() sont définis dans includes/auth.php
 */
$currentFile = basename($_SERVER['PHP_SELF']);
?>
<nav class="col-md-3 col-lg-2 d-md-block sidebar">
    <div class="brand">
        <h5><i class="bi bi-car-front-fill"></i> Auto École Pro</h5>
        <small>
            <?= htmlspecialchars($_SESSION['username'] ?? '') ?>
            <span class="badge <?= isAdmin() ? 'bg-warning text-dark' : 'bg-secondary' ?> ms-1">
                <?= isAdmin() ? 'Admin' : 'Stagiaire' ?>
            </span>
        </small>
    </div>
    <div class="position-sticky pt-2">
        <ul class="nav flex-column px-2">

            <li class="nav-item">
                <a class="nav-link <?= $currentFile==='index.php' ? 'active':'' ?>"
                   href="<?= BASE_URL ?>/index.php">
                    <i class="bi bi-speedometer2 me-2"></i> Tableau de bord
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentFile==='students.php' ? 'active':'' ?>"
                   href="<?= BASE_URL ?>/pages/students.php">
                    <i class="bi bi-people me-2"></i> Élèves
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentFile==='instructors.php' ? 'active':'' ?>"
                   href="<?= BASE_URL ?>/pages/instructors.php">
                    <i class="bi bi-person-badge me-2"></i> Moniteurs
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentFile==='vehicles.php' ? 'active':'' ?>"
                   href="<?= BASE_URL ?>/pages/vehicles.php">
                    <i class="bi bi-car-front me-2"></i> Véhicules
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentFile==='lessons.php' ? 'active':'' ?>"
                   href="<?= BASE_URL ?>/pages/lessons.php">
                    <i class="bi bi-calendar-event me-2"></i> Leçons
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentFile==='payments.php' ? 'active':'' ?>"
                   href="<?= BASE_URL ?>/pages/payments.php">
                    <i class="bi bi-cash-stack me-2"></i> Paiements
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentFile==='enrollments.php' ? 'active':'' ?>"
                   href="<?= BASE_URL ?>/pages/enrollments.php">
                    <i class="bi bi-journal-bookmark-fill me-2"></i> Inscriptions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentFile==='exams.php' ? 'active':'' ?>"
                   href="<?= BASE_URL ?>/pages/exams.php">
                    <i class="bi bi-file-text me-2"></i> Examens
                </a>
            </li>

            <?php if (hasPermission('voir_parametres')): ?>
            <!-- Visible uniquement pour les admins -->
            <li class="nav-item">
                <a class="nav-link <?= $currentFile==='settings.php' ? 'active':'' ?>"
                   href="<?= BASE_URL ?>/pages/settings.php">
                    <i class="bi bi-gear me-2"></i> Paramètres
                </a>
            </li>
            <?php endif; ?>

            <li class="nav-item mt-3">
                <a class="nav-link text-danger"
                   href="<?= BASE_URL ?>/pages/actions/logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i> Déconnexion
                </a>
            </li>
        </ul>
    </div>
</nav>
