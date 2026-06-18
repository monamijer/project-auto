<?php
/**
 * includes/auth.php
 * ──────────────────────────────────────────────────────────────────────────
 * Fonctions d'authentification et de contrôle des rôles.
 * Inclure ce fichier dans TOUTES les pages protégées, APRÈS database.php.
 *
 * Rôles disponibles :
 *   'admin'     → accès complet (CRUD + paramètres)
 *   'stagiaire' → lecture seule (pas de boutons CRUD)
 *
 * COMMENT AJOUTER UN RÔLE OU CHANGER UNE PERMISSION :
 *   1. Ajouter la valeur dans la colonne ENUM `role` de la table
 *      expirations_utilisateurs (via phpMyAdmin ou ALTER TABLE).
 *   2. Ajouter le cas dans les fonctions ci-dessous.
 *   3. Utiliser isAdmin() / hasPermission() dans les pages concernées.
 *
 * Exemple pour un futur rôle 'secretaire' :
 *   ALTER TABLE expirations_utilisateurs
 *     MODIFY COLUMN role ENUM('admin','stagiaire','secretaire');
 *   Puis dans hasPermission() ajouter les droits de 'secretaire'.
 */

/**
 * Vérifie si l'utilisateur est connecté.
 * Redirige vers login.php si ce n'est pas le cas.
 */
function requireLogin(): void
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/pages/login.php');
        exit();
    }
}

/**
 * Retourne true si l'utilisateur connecté est administrateur.
 */
function isAdmin(): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Vérifie une permission spécifique selon le rôle.
 * Centralise toutes les règles d'accès du projet.
 *
 * Permissions disponibles :
 *   'crud_eleves'       — ajouter/modifier/supprimer des élèves
 *   'crud_moniteurs'    — ajouter/modifier/supprimer des moniteurs
 *   'crud_vehicules'    — ajouter/modifier/supprimer des véhicules
 *   'crud_lecons'       — planifier/annuler/supprimer des leçons
 *   'crud_paiements'    — enregistrer/supprimer des paiements
 *   'voir_parametres'   — accéder à la page Paramètres
 *   'gestion_comptes'   — CRUD des comptes utilisateurs
 *
 * @param  string $permission  Identifiant de la permission
 * @return bool
 */
function hasPermission(string $permission): bool
{
    $role = $_SESSION['role'] ?? 'stagiaire';

    // ── Table des permissions par rôle ─────────────────────────────────
    // Pour ajouter un nouveau rôle : copier le bloc 'stagiaire',
    // changer la clé, et ajuster les valeurs true/false.
    $permissions = [
        'admin' => [
            'crud_eleves'     => true,
            'crud_moniteurs'  => true,
            'crud_vehicules'  => true,
            'crud_lecons'     => true,
            'crud_paiements'  => true,
            'voir_parametres' => true,
            'gestion_comptes' => true,
        ],
        'stagiaire' => [
            'crud_eleves'     => false,
            'crud_moniteurs'  => false,
            'crud_vehicules'  => false,
            'crud_lecons'     => false,
            'crud_paiements'  => false,
            'voir_parametres' => false,
            'gestion_comptes' => false,
        ],
        // ──  rôle 'secretaire' ─────────────────────────
        'secretaire' => [
            'crud_eleves'     => true,   // peut gérer les élèves
            'crud_moniteurs'  => false,
            'crud_vehicules'  => false,
            'crud_lecons'     => true,   // peut planifier les leçons
            'crud_paiements'  => true,   // peut enregistrer les paiements
            'voir_parametres' => false,
            'gestion_comptes' => false,
        ],
    ];

    // Si le rôle est inconnu, aucun accès
    if (!isset($permissions[$role])) {
        return false;
    }

    return $permissions[$role][$permission] ?? false;
}

/**
 * Bloque l'accès si la permission n'est pas accordée.
 * Affiche un message d'erreur et arrête l'exécution.
 *
 * @param string $permission
 */
function requirePermission(string $permission): void
{
    if (!hasPermission($permission)) {
        http_response_code(403);
        die(
            '<div style="padding:40px;text-align:center;font-family:sans-serif;">'
          . '<h2>🚫 Accès refusé</h2>'
          . '<p>Vous n\'avez pas la permission d\'effectuer cette action.</p>'
          . '<p>Seuls les administrateurs peuvent faire cela.</p>'
          . '<a href="' . BASE_URL . '/index.php" style="color:#0d6efd;">← Retour au tableau de bord</a>'
          . '</div>'
        );
    }
}

/**
 * Appelle une stored procedure avec paramètre OUT p_message.
 * Retourne le message de la procédure ('OK' ou message d'erreur).
 *
 * Usage :
 *   $msg = callProcedure("CALL sp_ajouter_eleve(?,?,?,?,?,?,@msg)", [...]);
 *   if ($msg === 'OK') { ... }
 *
 * @param  string $sql    Requête CALL avec @msg en OUT
 * @param  array  $params Paramètres IN de la procédure
 * @return string         'OK' ou message d'erreur MySQL
 */
function callProcedure(string $sql, array $params = []): string
{
    global $pdo;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $stmt->closeCursor(); // important pour libérer le handle avant SELECT @msg

        // Lire le paramètre OUT
        $result = $pdo->query("SELECT @msg AS msg")->fetch();
        return $result['msg'] ?? 'OK';

    } catch (PDOException $e) {
        return $e->getMessage();
    }
}
