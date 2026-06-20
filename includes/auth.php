<?php
/**
 * includes/auth.php
 * ──────────────────────────────────────────────────────────────────────────
 * Authentification, permissions par rôle, journal d'activités, notifications.
 * Inclure APRÈS database.php.
 *
 * COMMENT AJOUTER UN RÔLE OU CHANGER UNE PERMISSION :
 *   1. Ajouter la valeur dans la colonne ENUM `role` de expirations_utilisateurs.
 *   2. Ajouter un bloc dans le tableau $permissions de hasPermission().
 *   3. Utiliser isAdmin() / hasPermission() dans les pages concernées.
 */

/** Durée d'inactivité avant déconnexion automatique (secondes) */
define('SESSION_TIMEOUT', 20 * 60); // 20 minutes

/**
 * Vérifie la connexion + applique la déconnexion automatique par inactivité.
 */
function requireLogin(): void
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/pages/login.php');
        exit();
    }
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . '/pages/login.php?expired=1');
        exit();
    }
    $_SESSION['last_activity'] = time();
}

function isAdmin(): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Permissions disponibles :
 *   crud_eleves, crud_moniteurs, crud_vehicules, crud_lecons, crud_paiements,
 *   voir_parametres, gestion_comptes, gestion_documents, export_donnees
 */
function hasPermission(string $permission): bool
{
    $role = $_SESSION['role'] ?? 'stagiaire';

    $permissions = [
        'admin' => [
            'crud_eleves' => true, 'crud_moniteurs' => true, 'crud_vehicules' => true,
            'crud_lecons' => true, 'crud_paiements' => true, 'voir_parametres' => true,
            'gestion_comptes' => true, 'gestion_documents' => true, 'export_donnees' => true,
        ],
        'stagiaire' => [
            'crud_eleves' => false, 'crud_moniteurs' => false, 'crud_vehicules' => false,
            'crud_lecons' => false, 'crud_paiements' => false, 'voir_parametres' => false,
            'gestion_comptes' => false, 'gestion_documents' => false, 'export_donnees' => false,
        ],
        // Exemple de rôle intermédiaire (déjà présent dans ton projet) :
        'secretaire' => [
            'crud_eleves' => true, 'crud_moniteurs' => false, 'crud_vehicules' => false,
            'crud_lecons' => true, 'crud_paiements' => true, 'voir_parametres' => false,
            'gestion_comptes' => false, 'gestion_documents' => true, 'export_donnees' => false,
        ],
    ];

    if (!isset($permissions[$role])) return false;
    return $permissions[$role][$permission] ?? false;
}

function requirePermission(string $permission): void
{
    if (!hasPermission($permission)) {
        http_response_code(403);
        die(
            '<div style="padding:40px;text-align:center;font-family:sans-serif;">'
          . '<h2>🚫 Accès refusé</h2>'
          . '<p>Vous n\'avez pas la permission d\'effectuer cette action.</p>'
          . '<a href="' . BASE_URL . '/index.php" style="color:#0d6efd;">← Retour</a>'
          . '</div>'
        );
    }
}

/**
 * Appelle une procédure stockée avec OUT p_message / @msg.
 */
function callProcedure(string $sql, array $params = []): string
{
    global $pdo;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $stmt->closeCursor();
        $result = $pdo->query("SELECT @msg AS msg")->fetch();
        return $result['msg'] ?? 'OK';
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}

/**
 * Journalise une action CRUD (table journal_activites, via sp_journaliser_activite).
 * @param string $action 'AJOUT'|'MODIFICATION'|'SUPPRESSION'|'RESTAURATION'|'BLOCAGE'|'DEBLOCAGE'
 * @param string $module 'eleves'|'moniteurs'|'vehicules'|'lecons'|'paiements'|'comptes'|'documents'
 */
function logActivity(string $action, string $module, ?int $elementId = null, string $details = ''): void
{
    $user = $_SESSION['username'] ?? 'système';
    callProcedure("CALL sp_journaliser_activite(?,?,?,?,?,@msg)",
        [$user, $action, $module, $elementId, $details]);
}

/**
 * Crée une notification interne visible par tous les admins.
 */
function notifyAdmins(string $titre, string $message, string $lien = ''): void
{
    callProcedure("CALL sp_creer_notification(?,?,?,?,@msg)", ['all', $titre, $message, $lien]);
}
