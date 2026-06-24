<?php
/**
 * pages/search.php — Recherche globale
 * SELECT → v_recherche_globale, v_pays_nationalites (Views SQL)
 * Recherche instantanée avec AJAX, filtres avancés, design SaaS moderne
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

// ── Paramètres de recherche (GET) ─────────────────────────────────────────
$q = trim($_GET['q'] ?? '');
$type = $_GET['type'] ?? '';
$pays = $_GET['pays'] ?? '';

// ── Liste des pays pour le filtre déroulant (via VIEW) ────────────────────
$paysListe = $pdo->query('SELECT pays FROM v_pays_nationalites')->fetchAll(PDO::FETCH_COLUMN);

// ── Si requête AJAX, renvoyer JSON ────────────────────────────────────────
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');

    $sql = 'SELECT * FROM v_recherche_globale WHERE 1=1';
    $params = [];

    if ($q !== '') {
        $sql .= ' AND (nom_complet LIKE ? COLLATE utf8mb4_unicode_ci OR detail1 LIKE ? COLLATE utf8mb4_unicode_ci OR detail2 LIKE ? COLLATE utf8mb4_unicode_ci)';
        $like = "%$q%";
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }
    if ($type !== '') {
        $sql .= ' AND type = ? COLLATE utf8mb4_unicode_ci';
        $params[] = $type;
    }
    if ($pays !== '') {
        $sql .= ' AND pays = ? COLLATE utf8mb4_unicode_ci';
        $params[] = $pays;
    }
    $sql .= ' ORDER BY nom_complet LIMIT 50';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll();

    echo json_encode(['results' => $results, 'count' => count($results)]);
    exit();
}

// ── Résultats initiaux (si recherche normale) ─────────────────────────────
$results = [];
$hasSearched = $q !== '' || $type !== '' || $pays !== '';
if ($hasSearched) {
    $sql = 'SELECT * FROM v_recherche_globale WHERE 1=1';
    $params = [];

    if ($q !== '') {
        $sql .= ' AND (nom_complet LIKE ? COLLATE utf8mb4_unicode_ci OR detail1 LIKE ? COLLATE utf8mb4_unicode_ci OR detail2 LIKE ? COLLATE utf8mb4_unicode_ci)';
        $like = "%$q%";
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }
    if ($type !== '') {
        $sql .= ' AND type = ? COLLATE utf8mb4_unicode_ci';
        $params[] = $type;
    }
    if ($pays !== '') {
        $sql .= ' AND pays = ? COLLATE utf8mb4_unicode_ci';
        $params[] = $pays;
    }
    $sql .= ' ORDER BY nom_complet LIMIT 50';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll();
}

$pageTitle = 'Recherche — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1"><i class="bi bi-search me-2 text-primary"></i>Recherche globale</h1>
        <p class="text-muted mb-0">Recherchez un élève, moniteur ou véhicule instantanément</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <span id="resultCount" class="badge bg-primary bg-opacity-10 text-primary fs-6 px-3 py-2 rounded-pill d-none">
            <i class="bi bi-list-ul me-1"></i><span id="countNumber">0</span> résultat(s)
        </span>
        <button id="clearAllHeaderBtn" class="btn btn-outline-danger btn-sm d-none" title="Effacer tous les résultats">
            <i class="bi bi-x-circle me-1"></i>Effacer tout
        </button>
    </div>
</div>

<!-- Barre de recherche principale -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body p-3">
        <form id="searchForm" method="GET" class="row g-2 align-items-end">
            <div class="col-lg-5 col-md-6">
                <label class="form-label fw-medium small text-muted">
                    <i class="bi bi-search me-1"></i>Rechercher
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" 
                           id="searchInput" 
                           name="q" 
                           class="form-control border-start-0 ps-0" 
                           placeholder="Nom, téléphone, email, immatriculation..."
                           value="<?= htmlspecialchars($q) ?>" 
                           autofocus 
                           autocomplete="off">
                    <button id="clearSearch" class="btn btn-outline-secondary border-start-0 d-none" type="button" title="Effacer">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div id="suggestions" class="dropdown-menu w-100 shadow-sm" style="max-height: 200px; overflow-y: auto;"></div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <label class="form-label fw-medium small text-muted">
                    <i class="bi bi-funnel me-1"></i>Type
                </label>
                <select id="typeFilter" name="type" class="form-select">
                    <option value="">📋 Tous les types</option>
                    <option value="eleve" <?= $type === 'eleve' ? 'selected' : '' ?>>👤 Élèves</option>
                    <option value="moniteur" <?= $type === 'moniteur' ? 'selected' : '' ?>>👨‍🏫 Moniteurs</option>
                    <option value="vehicule" <?= $type === 'vehicule' ? 'selected' : '' ?>>🚗 Véhicules</option>
                </select>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <label class="form-label fw-medium small text-muted">
                    <i class="bi bi-globe me-1"></i>Pays / Nationalité
                </label>
                <select id="paysFilter" name="pays" class="form-select">
                    <option value="">🌍 Tous les pays</option>
                    <?php foreach ($paysListe as $p): ?>
                    <option value="<?= htmlspecialchars($p) ?>" <?= $pays === $p ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-lg-1 col-md-6 d-flex gap-2">
                <button type="button" id="searchBtn" class="btn btn-primary w-100 shadow-sm" title="Rechercher">
                    <i class="bi bi-search"></i>
                </button>
                <button id="resetFiltersBtn" type="button" class="btn btn-outline-secondary shadow-sm" title="Réinitialiser les filtres">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Zone de résultats -->
<div id="resultsContainer">
    <?php if (!$hasSearched): ?>
    <!-- État initial -->
    <div class="text-center py-5" id="initialState">
        <div class="mb-4">
            <i class="bi bi-search display-1 text-muted opacity-25"></i>
        </div>
        <h4 class="text-muted mb-2">Prêt à rechercher</h4>
        <p class="text-muted mb-4">Saisissez un terme ou utilisez les filtres pour trouver ce que vous cherchez</p>
        <div class="row g-3 justify-content-center">
            <div class="col-md-3 col-6">
                <div class="card border border-primary border-opacity-25 h-100 cursor-pointer quick-search-card" data-type="eleve">
                    <div class="card-body text-center py-3">
                        <i class="bi bi-people-fill text-primary fs-3 mb-2 d-block"></i>
                        <small class="fw-medium">Élèves</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border border-info border-opacity-25 h-100 cursor-pointer quick-search-card" data-type="moniteur">
                    <div class="card-body text-center py-3">
                        <i class="bi bi-person-badge-fill text-info fs-3 mb-2 d-block"></i>
                        <small class="fw-medium">Moniteurs</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border border-secondary border-opacity-25 h-100 cursor-pointer quick-search-card" data-type="vehicule">
                    <div class="card-body text-center py-3">
                        <i class="bi bi-car-front-fill text-secondary fs-3 mb-2 d-block"></i>
                        <small class="fw-medium">Véhicules</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- Résultats initiaux -->
    <?php if (empty($results)): ?>
    <div class="text-center py-5" id="noResults">
        <div class="mb-3">
            <i class="bi bi-emoji-frown display-1 text-muted opacity-25"></i>
        </div>
        <h5 class="text-muted">Aucun résultat trouvé</h5>
        <p class="text-muted">Essayez avec d'autres termes ou modifiez les filtres</p>
        <button id="clearNoResultsBtn" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-x-circle me-1"></i>Effacer la recherche
        </button>
    </div>
    <?php else: ?>
    <div class="card shadow-sm border-0" id="resultsCard">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Résultats de recherche</h5>
            <div class="d-flex gap-2">
                <span class="badge bg-primary rounded-pill"><?= count($results) ?> trouvé(s)</span>
                <button id="clearResultsBtn" class="btn btn-outline-danger btn-sm" title="Effacer tous les résultats">
                    <i class="bi bi-x-circle me-1"></i>Effacer
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Type</th>
                        <th>Nom</th>
                        <th>Pays</th>
                        <th>Détail 1</th>
                        <th>Détail 2</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($results as $r):

                    $icon = match ($r['type']) {
                        'eleve' => 'bi-person-fill',
                        'moniteur' => 'bi-person-badge-fill',
                        'vehicule' => 'bi-car-front-fill',
                        default => 'bi-question-circle-fill',
                    };
                    $badgeClass = match ($r['type']) {
                        'eleve' => 'bg-primary bg-opacity-10 text-primary',
                        'moniteur' => 'bg-info bg-opacity-10 text-info',
                        'vehicule' => 'bg-secondary bg-opacity-10 text-secondary',
                        default => 'bg-dark bg-opacity-10 text-dark',
                    };
                    $link = match ($r['type']) {
                        'eleve' => BASE_URL . '/pages/student_profile.php?id=' . $r['id'],
                        default => BASE_URL .
                            '/pages/' .
                            match ($r['type']) {
                                'moniteur' => 'instructors',
                                'vehicule' => 'vehicles',
                            } .
                            '.php',
                    };
                    ?>
                <tr>
                    <td class="ps-3">
                        <span class="badge <?= $badgeClass ?> px-3 py-2">
                            <i class="bi <?= $icon ?> me-1"></i>
                            <?= ucfirst($r['type']) ?>
                        </span>
                    </td>
                    <td><span class="fw-medium"><?= htmlspecialchars($r['nom_complet']) ?></span></td>
                    <td>
                        <?php if ($r['pays']): ?>
                            <span class="badge bg-light text-dark"><?= htmlspecialchars($r['pays']) ?></span>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td><small><?= htmlspecialchars($r['detail1'] ?? '—') ?></small></td>
                    <td><small><?= htmlspecialchars($r['detail2'] ?? '—') ?></small></td>
                    <td class="text-end pe-3">
                        <a href="<?= $link ?>" class="btn btn-sm btn-outline-primary" title="Voir détails">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php
                endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Loader -->
<div id="searchLoader" class="text-center py-4 d-none">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Recherche...</span>
    </div>
    <p class="text-muted mt-2">Recherche en cours...</p>
</div>

<!-- Script AJAX -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const typeFilter = document.getElementById('typeFilter');
    const paysFilter = document.getElementById('paysFilter');
    const clearSearchBtn = document.getElementById('clearSearch');
    const clearAllHeaderBtn = document.getElementById('clearAllHeaderBtn');
    const resetFiltersBtn = document.getElementById('resetFiltersBtn');
    const suggestions = document.getElementById('suggestions');
    const resultsContainer = document.getElementById('resultsContainer');
    const searchLoader = document.getElementById('searchLoader');
    const resultCount = document.getElementById('resultCount');
    const countNumber = document.getElementById('countNumber');
    let debounceTimer;

    // Vérifier l'état initial
    <?php if ($hasSearched): ?>
    resultCount.classList.remove('d-none');
    clearAllHeaderBtn.classList.remove('d-none');
    <?php endif; ?>

    // Fonction pour effacer tous les résultats
    function clearAllResults() {
        searchInput.value = '';
        typeFilter.value = '';
        paysFilter.value = '';
        clearSearchBtn.classList.add('d-none');
        clearAllHeaderBtn.classList.add('d-none');
        suggestions.classList.remove('show');
        resultCount.classList.add('d-none');
        
        resultsContainer.innerHTML = `
            <div class="text-center py-5" id="initialState">
                <div class="mb-4">
                    <i class="bi bi-search display-1 text-muted opacity-25"></i>
                </div>
                <h4 class="text-muted mb-2">Prêt à rechercher</h4>
                <p class="text-muted mb-4">Saisissez un terme ou utilisez les filtres pour trouver ce que vous cherchez</p>
                <div class="row g-3 justify-content-center">
                    <div class="col-md-3 col-6">
                        <div class="card border border-primary border-opacity-25 h-100 cursor-pointer quick-search-card" data-type="eleve">
                            <div class="card-body text-center py-3">
                                <i class="bi bi-people-fill text-primary fs-3 mb-2 d-block"></i>
                                <small class="fw-medium">Élèves</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card border border-info border-opacity-25 h-100 cursor-pointer quick-search-card" data-type="moniteur">
                            <div class="card-body text-center py-3">
                                <i class="bi bi-person-badge-fill text-info fs-3 mb-2 d-block"></i>
                                <small class="fw-medium">Moniteurs</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card border border-secondary border-opacity-25 h-100 cursor-pointer quick-search-card" data-type="vehicule">
                            <div class="card-body text-center py-3">
                                <i class="bi bi-car-front-fill text-secondary fs-3 mb-2 d-block"></i>
                                <small class="fw-medium">Véhicules</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Réattacher les événements aux cartes de recherche rapide
        attachQuickSearchEvents();
        searchInput.focus();
    }

    // Fonction pour attacher les événements de recherche rapide
    function attachQuickSearchEvents() {
        document.querySelectorAll('.quick-search-card').forEach(card => {
            card.addEventListener('click', function() {
                const type = this.getAttribute('data-type');
                typeFilter.value = type;
                performSearch();
            });
        });
    }

    // Attacher les événements initiaux
    attachQuickSearchEvents();

    // Événements pour les boutons
    searchBtn.addEventListener('click', function(e) {
        e.preventDefault();
        performSearch();
    });

    resetFiltersBtn.addEventListener('click', function() {
        clearAllResults();
    });

    clearAllHeaderBtn.addEventListener('click', clearAllResults);

    // Événement pour le bouton Effacer dans l'input
    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        clearSearchBtn.classList.add('d-none');
        suggestions.classList.remove('show');
        searchInput.focus();
        clearAllResults();
    });

    // Afficher/masquer bouton clear
    searchInput.addEventListener('input', function() {
        clearSearchBtn.classList.toggle('d-none', this.value === '');
        handleLiveSearch();
    });

    // Recherche avec la touche Entrée
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            performSearch();
        }
        if (e.key === 'Escape') {
            suggestions.classList.remove('show');
        }
    });

    // Live search avec debounce
    function handleLiveSearch() {
        clearTimeout(debounceTimer);
        const query = searchInput.value.trim();
        
        if (query.length < 2) {
            suggestions.classList.remove('show');
            return;
        }

        debounceTimer = setTimeout(() => {
            fetchSuggestions(query);
        }, 300);
    }

    // Autocomplétion
    async function fetchSuggestions(query) {
        try {
            const params = new URLSearchParams({
                q: query,
                type: typeFilter.value,
                pays: paysFilter.value
            });
            
            const response = await fetch(`<?= BASE_URL ?>/pages/search.php?${params}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            
            if (data.results.length > 0) {
                renderSuggestions(data.results.slice(0, 5));
            } else {
                suggestions.classList.remove('show');
            }
        } catch (error) {
            console.error('Erreur suggestions:', error);
        }
    }

    function renderSuggestions(items) {
        suggestions.innerHTML = '';
        
        items.forEach(item => {
            const a = document.createElement('a');
            a.className = 'dropdown-item d-flex align-items-center py-2';
            a.style.cursor = 'pointer';
            const icon = item.type === 'eleve' ? 'bi-person' : item.type === 'moniteur' ? 'bi-person-badge' : 'bi-car-front';
            a.innerHTML = `
                <i class="bi ${icon} me-2 text-muted"></i>
                <div>
                    <div class="small fw-medium">${escapeHtml(item.nom_complet)}</div>
                    <div class="small text-muted">${escapeHtml(item.detail1 || item.detail2 || '')} · ${item.type}</div>
                </div>
            `;
            a.addEventListener('click', function() {
                searchInput.value = item.nom_complet;
                suggestions.classList.remove('show');
                performSearch();
            });
            suggestions.appendChild(a);
        });
        
        suggestions.classList.add('show');
    }

    // Recherche instantanée lors du changement de filtres
    typeFilter.addEventListener('change', performSearch);
    paysFilter.addEventListener('change', performSearch);

    // Recherche AJAX
    async function performSearch() {
        const query = searchInput.value.trim();
        
        if (!query && !typeFilter.value && !paysFilter.value) {
            clearAllResults();
            return;
        }

        resultsContainer.classList.add('opacity-50');
        searchLoader.classList.remove('d-none');

        try {
            const params = new URLSearchParams({
                q: query,
                type: typeFilter.value,
                pays: paysFilter.value
            });
            
            const response = await fetch(`<?= BASE_URL ?>/pages/search.php?${params}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            
            renderResults(data.results);
            resultCount.classList.remove('d-none');
            clearAllHeaderBtn.classList.remove('d-none');
            countNumber.textContent = data.count;
        } catch (error) {
            console.error('Erreur recherche:', error);
        } finally {
            searchLoader.classList.add('d-none');
            resultsContainer.classList.remove('opacity-50');
        }
    }

    function renderResults(results) {
        if (results.length === 0) {
            resultsContainer.innerHTML = `
                <div class="text-center py-5" id="noResults">
                    <div class="mb-3">
                        <i class="bi bi-emoji-frown display-1 text-muted opacity-25"></i>
                    </div>
                    <h5 class="text-muted">Aucun résultat trouvé</h5>
                    <p class="text-muted">Essayez avec d'autres termes ou modifiez les filtres</p>
                    <button id="clearNoResultsBtn" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-x-circle me-1"></i>Effacer la recherche
                    </button>
                </div>
            `;
            
            // Attacher l'événement au bouton dynamique
            document.getElementById('clearNoResultsBtn').addEventListener('click', clearAllResults);
            return;
        }

        const iconMap = { 'eleve': 'person-fill', 'moniteur': 'person-badge-fill', 'vehicule': 'car-front-fill' };
        const badgeMap = { 
            'eleve': 'bg-primary bg-opacity-10 text-primary', 
            'moniteur': 'bg-info bg-opacity-10 text-info', 
            'vehicule': 'bg-secondary bg-opacity-10 text-secondary' 
        };

        let html = `
            <div class="card shadow-sm border-0" id="resultsCard">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Résultats de recherche</h5>
                    <div class="d-flex gap-2">
                        <span class="badge bg-primary rounded-pill">${results.length} trouvé(s)</span>
                        <button id="clearResultsBtn" class="btn btn-outline-danger btn-sm" title="Effacer tous les résultats">
                            <i class="bi bi-x-circle me-1"></i>Effacer
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Type</th>
                                <th>Nom</th>
                                <th>Pays</th>
                                <th>Détail 1</th>
                                <th>Détail 2</th>
                                <th class="text-end pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>`;

        results.forEach(r => {
            const icon = iconMap[r.type] || 'question-circle-fill';
            const badgeClass = badgeMap[r.type] || 'bg-dark bg-opacity-10 text-dark';
            const link = r.type === 'eleve' 
                ? `<?= BASE_URL ?>/pages/student_profile.php?id=${r.id}`
                : `<?= BASE_URL ?>/pages/${r.type === 'moniteur' ? 'instructors' : 'vehicles'}.php`;
            
            html += `
                <tr>
                    <td class="ps-3">
                        <span class="badge ${badgeClass} px-3 py-2">
                            <i class="bi bi-${icon} me-1"></i>
                            ${r.type.charAt(0).toUpperCase() + r.type.slice(1)}
                        </span>
                    </td>
                    <td><span class="fw-medium">${escapeHtml(r.nom_complet)}</span></td>
                    <td>${r.pays ? `<span class="badge bg-light text-dark">${escapeHtml(r.pays)}</span>` : '<span class="text-muted">—</span>'}</td>
                    <td><small>${escapeHtml(r.detail1 || '—')}</small></td>
                    <td><small>${escapeHtml(r.detail2 || '—')}</small></td>
                    <td class="text-end pe-3">
                        <a href="${link}" class="btn btn-sm btn-outline-primary" title="Voir détails">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>`;
        });

        html += '</tbody></table></div></div>';
        resultsContainer.innerHTML = html;
        
        // Attacher l'événement au bouton dynamique
        document.getElementById('clearResultsBtn').addEventListener('click', clearAllResults);
    }

    // Fermer suggestions au clic extérieur
    document.addEventListener('click', function(e) {
        if (!suggestions.contains(e.target) && e.target !== searchInput) {
            suggestions.classList.remove('show');
        }
    });

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>

<style>
.cursor-pointer { cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; }
.cursor-pointer:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
.quick-search-card { cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; }
.quick-search-card:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
#resultsContainer { transition: opacity 0.2s; }
#searchInput:focus { box-shadow: none; }
.dropdown-menu { display: none; }
.dropdown-menu.show { display: block; }
</style>

<?php include BASE_PATH . '/includes/footer.php'; ?>
