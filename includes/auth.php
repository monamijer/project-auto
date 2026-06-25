<?php
/**
 * includes/auth.php — Authentification, permissions, journalisation
 * ──────────────────────────────────────────────────────────────────────────
 *
 * RÔLES DISPONIBLES (valeurs dans la colonne `role` de expirations_utilisateurs) :
 *
 *   admin      → Accès complet (gestion comptes, paramètres, tout)
 *   directeur  → Comme admin SAUF la gestion des comptes utilisateurs
 *   secretaire → Élèves, inscriptions, documents, paiements, leçons
 *   caissier   → Paiements uniquement + consultation
 *   moniteur   → Leçons + consultation
 *   stagiaire  → Lecture seule (aucun CRUD)
 *
 * COMMENT AJOUTER UN RÔLE :
 *   1. ALTER TABLE expirations_utilisateurs MODIFY COLUMN role ENUM(...) → ajouter la valeur
 *   2. Ajouter un bloc dans $permissions ci-dessous
 *   3. Mettre à jour le label dans hasRoleLabel() si besoin
 */

define('SESSION_TIMEOUT', 20 * 60); // 20 min d'inactivité → déconnexion auto

function requireLogin(): void
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/pages/login.php');
        exit();
    }
    if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . '/pages/login.php?expired=1');
        exit();
    }
    $_SESSION['last_activity'] = time();
}

function isAdmin(): bool
{
    return in_array($_SESSION['role'] ?? '', ['admin', 'directeur']);
}

/**
 * Retourne le libellé français du rôle.
 */
function getRoleLabel(string $role = ''): string
{
    return match($role ?: ($_SESSION['role'] ?? '')) {
        'admin'     => 'Administrateur',
        'directeur' => 'Directeur',
        'secretaire'=> 'Secrétaire',
        'caissier'  => 'Caissier',
        'moniteur'  => 'Moniteur',
        'stagiaire' => 'Stagiaire',
        default     => 'Utilisateur',
    };
}

/**
 * Retourne la couleur Bootstrap du badge pour le rôle.
 */
function getRoleBadgeClass(string $role = ''): string
{
    return match($role ?: ($_SESSION['role'] ?? '')) {
        'admin'     => 'bg-danger',
        'directeur' => 'bg-dark',
        'secretaire'=> 'bg-info text-dark',
        'caissier'  => 'bg-success',
        'moniteur'  => 'bg-warning text-dark',
        'stagiaire' => 'bg-secondary',
        default     => 'bg-secondary',
    };
}

/**
 * Vérifie si l'utilisateur connecté a une permission donnée.
 *
 * Permissions disponibles :
 *   crud_eleves       — Ajouter/modifier/supprimer des élèves
 *   crud_moniteurs    — Ajouter/modifier/supprimer des moniteurs
 *   crud_vehicules    — Ajouter/modifier/supprimer des véhicules
 *   crud_lecons       — Planifier/annuler/supprimer des leçons
 *   crud_paiements    — Enregistrer/supprimer des paiements
 *   voir_parametres   — Accéder à la page Paramètres
 *   gestion_comptes   — CRUD des comptes utilisateurs (admin uniquement)
 *   gestion_documents — Upload/suppression de documents
 *   export_donnees    — Exporter en Excel / générer des rapports PDF
 *   voir_chat         — Accéder au chat/messagerie
 */
function hasPermission(string $permission): bool
{
    $role = $_SESSION['role'] ?? 'stagiaire';

    $permissions = [
        // ── Admin : tout ────────────────────────────────────────────────
        'admin' => [
            'crud_eleves'       => true,  'crud_moniteurs'    => true,
            'crud_vehicules'    => true,  'crud_lecons'       => true,
            'crud_paiements'    => true,  'voir_parametres'   => true,
            'gestion_comptes'   => true,  'gestion_documents' => true,
            'export_donnees'    => true,  'voir_chat'         => true,
        ],
        // ── Directeur : comme admin mais ne peut pas gérer les comptes ──
        'directeur' => [
            'crud_eleves'       => true,  'crud_moniteurs'    => true,
            'crud_vehicules'    => true,  'crud_lecons'       => true,
            'crud_paiements'    => true,  'voir_parametres'   => true,
            'gestion_comptes'   => false, 'gestion_documents' => true,
            'export_donnees'    => true,  'voir_chat'         => true,
        ],
        // ── Secrétaire : gestion métier complète sauf véhicules/comptes ─
        'secretaire' => [
            'crud_eleves'       => true,  'crud_moniteurs'    => false,
            'crud_vehicules'    => false, 'crud_lecons'       => true,
            'crud_paiements'    => true,  'voir_parametres'   => false,
            'gestion_comptes'   => false, 'gestion_documents' => true,
            'export_donnees'    => false, 'voir_chat'         => true,
        ],
        // ── Caissier : paiements uniquement + consultation ───────────────
        'caissier' => [
            'crud_eleves'       => false, 'crud_moniteurs'    => false,
            'crud_vehicules'    => false, 'crud_lecons'       => false,
            'crud_paiements'    => true,  'voir_parametres'   => false,
            'gestion_comptes'   => false, 'gestion_documents' => false,
            'export_donnees'    => false, 'voir_chat'         => true,
        ],
        // ── Moniteur : leçons uniquement + consultation ─────────────────
        'moniteur' => [
            'crud_eleves'       => false, 'crud_moniteurs'    => false,
            'crud_vehicules'    => false, 'crud_lecons'       => true,
            'crud_paiements'    => false, 'voir_parametres'   => false,
            'gestion_comptes'   => false, 'gestion_documents' => false,
            'export_donnees'    => false, 'voir_chat'         => true,
        ],
        // ── Stagiaire : lecture seule ────────────────────────────────────
        'stagiaire' => [
            'crud_eleves'       => false, 'crud_moniteurs'    => false,
            'crud_vehicules'    => false, 'crud_lecons'       => false,
            'crud_paiements'    => false, 'voir_parametres'   => false,
            'gestion_comptes'   => false, 'gestion_documents' => false,
            'export_donnees'    => false, 'voir_chat'         => true,
        ],
    ];

    if (!isset($permissions[$role])) return false;
    return $permissions[$role][$permission] ?? false;
}

function requirePermission(string $permission): void
{
    if (!hasPermission($permission)) {
        http_response_code(403);
        die('<div style="padding:40px;text-align:center;font-family:sans-serif;">'
          . '<h2>🚫 Accès refusé</h2>'
          . '<p>Votre rôle (' . htmlspecialchars(getRoleLabel()) . ') '
          . 'ne permet pas d\'effectuer cette action.</p>'
          . '<a href="' . BASE_URL . '/index.php">← Retour</a></div>');
    }
}

/** Appelle une procédure stockée avec OUT @msg. Retourne 'OK' ou le message d'erreur. */
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

/** Journalise une action CRUD. */
function logActivity(string $action, string $module, ?int $elementId = null, string $details = ''): void
{
    $user = $_SESSION['username'] ?? 'système';
    callProcedure("CALL sp_journaliser_activite(?,?,?,?,?,@msg)",
        [$user, $action, $module, $elementId, $details]);
}

/** Crée une notification interne pour les admins. */
function notifyAdmins(string $titre, string $message, string $lien = ''): void
{
    callProcedure("CALL sp_creer_notification(?,?,?,?,@msg)", ['all', $titre, $message, $lien]);
}
