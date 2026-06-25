<?php
/**
 * includes/auth.php — Authentification, permissions, journalisation
 *
 * RÔLES : admin, directeur, secretaire, caissier, moniteur, stagiaire
 */

define('SESSION_TIMEOUT', 20 * 60);

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

// CSRF Protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

function verify_csrf(): bool
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            return false;
        }
    }
    return true;
}

function isAdmin(): bool
{
    return in_array($_SESSION['role'] ?? '', ['admin', 'directeur']);
}

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

function hasPermission(string $permission): bool
{
    $role = $_SESSION['role'] ?? 'stagiaire';
    $permissions = [
        'admin' => ['crud_eleves'=>true,'crud_moniteurs'=>true,'crud_vehicules'=>true,'crud_lecons'=>true,'crud_paiements'=>true,'voir_parametres'=>true,'gestion_comptes'=>true,'gestion_documents'=>true,'export_donnees'=>true,'voir_chat'=>true],
        'directeur' => ['crud_eleves'=>true,'crud_moniteurs'=>true,'crud_vehicules'=>true,'crud_lecons'=>true,'crud_paiements'=>true,'voir_parametres'=>true,'gestion_comptes'=>false,'gestion_documents'=>true,'export_donnees'=>true,'voir_chat'=>true],
        'secretaire' => ['crud_eleves'=>true,'crud_moniteurs'=>false,'crud_vehicules'=>false,'crud_lecons'=>true,'crud_paiements'=>true,'voir_parametres'=>false,'gestion_comptes'=>false,'gestion_documents'=>true,'export_donnees'=>false,'voir_chat'=>true],
        'caissier' => ['crud_eleves'=>false,'crud_moniteurs'=>false,'crud_vehicules'=>false,'crud_lecons'=>false,'crud_paiements'=>true,'voir_parametres'=>false,'gestion_comptes'=>false,'gestion_documents'=>false,'export_donnees'=>false,'voir_chat'=>true],
        'moniteur' => ['crud_eleves'=>false,'crud_moniteurs'=>false,'crud_vehicules'=>false,'crud_lecons'=>true,'crud_paiements'=>false,'voir_parametres'=>false,'gestion_comptes'=>false,'gestion_documents'=>false,'export_donnees'=>false,'voir_chat'=>true],
        'stagiaire' => ['crud_eleves'=>false,'crud_moniteurs'=>false,'crud_vehicules'=>false,'crud_lecons'=>false,'crud_paiements'=>false,'voir_parametres'=>false,'gestion_comptes'=>false,'gestion_documents'=>false,'export_donnees'=>false,'voir_chat'=>true],
    ];
    return $permissions[$role][$permission] ?? false;
}

function requirePermission(string $permission): void
{
    if (!hasPermission($permission)) {
        http_response_code(403);
        die('<div style="padding:40px;text-align:center;font-family:sans-serif;"><h2>🚫 Accès refusé</h2><p>Votre rôle (' . htmlspecialchars(getRoleLabel()) . ') ne permet pas cette action.</p><a href="' . BASE_URL . '/index.php">← Retour</a></div>');
    }
}

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
        error_log('Procedure error: ' . $e->getMessage());
        return $e->getMessage();
    }
}

function logActivity(string $action, string $module, ?int $elementId = null, string $details = ''): void
{
    $user = $_SESSION['username'] ?? 'système';
    callProcedure("CALL sp_journaliser_activite(?,?,?,?,?,@msg)", [$user, $action, $module, $elementId, $details]);
}

function notifyAdmins(string $titre, string $message, string $lien = ''): void
{
    callProcedure("CALL sp_creer_notification(?,?,?,?,@msg)", ['all', $titre, $message, $lien]);
}