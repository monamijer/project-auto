<?php

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <h2>Auto-École Pro</h2>
        <p>Système de Gestion</p>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li class="<?php echo $currentPage == 'dashboard' ? 'active' : ''; ?>">
                <a href="dashboard.php"><span class="menu-icon">📊</span> Tableau de bord</a>
            </li>
            <li class="<?php echo $currentPage == 'eleves' ? 'active' : ''; ?>">
                <a href="eleves.php"><span class="menu-icon">👨‍🎓</span> Élèves</a>
            </li>
            <li class="<?php echo $currentPage == 'moniteurs' ? 'active' : ''; ?>">
                <a href="moniteurs.php"><span class="menu-icon">👨‍🏫</span> Moniteurs</a>
            </li>
            <li class="<?php echo $currentPage == 'vehicules' ? 'active' : ''; ?>">
                <a href="vehicules.php"><span class="menu-icon">🚗</span> Véhicules</a>
            </li>
            <li class="<?php echo $currentPage == 'lecons' ? 'active' : ''; ?>">
                <a href="lecons.php"><span class="menu-icon">📚</span> Leçons</a>
            </li>
            <li class="<?php echo $currentPage == 'examens' ? 'active' : ''; ?>">
                <a href="examens.php"><span class="menu-icon">✍️</span> Examens</a>
            </li>
            <li class="<?php echo $currentPage == 'paiements' ? 'active' : ''; ?>">
                <a href="paiements.php"><span class="menu-icon">💰</span> Paiements</a>
            </li>
            <li class="<?php echo $currentPage == 'parametres' ? 'active' : ''; ?>">
                <a href="parametres.php"><span class="menu-icon">⚙️</span> Paramètres</a>
            </li>
        </ul>
    </nav>
    
    <div style="padding: var(--spacing-lg); border-top: 1px solid rgba(255,255,255,0.1); margin-top: auto;">
        <a href="../actions/logout.php" style="color: rgba(255,255,255,0.7);">
            <span class="menu-icon">🚪</span> Déconnexion
        </a>
    </div>
</aside>