<?php

// ── Affichage des erreurs PHP (désactiver en production) ──────────────────
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ── Constantes globales ────────────────────────────────────────────────────

/** Chemin absolu vers la racine du projet (pour les includes PHP) */
define('BASE_PATH', dirname(__DIR__));

/**
 * URL racine du projet dans le navigateur.
 * À adapter si le projet est dans un sous-dossier différent.
 */
define('BASE_URL', '/project_auto');

// ── Paramètres de connexion ────────────────────────────────────────────────
define('DB_HOST',    'localhost');
define('DB_NAME',    'jerome_auto_ecole');
define('DB_USER',    'root');
define('DB_PASS',    'jer000');
define('DB_CHARSET', 'utf8mb4');

// ── Connexion PDO (singleton) ──────────────────────────────────────────────
/**
 * Retourne l'instance PDO partagée.
 * Créée une seule fois grâce au mot-clé static.
 *
 * @return PDO
 */
function getPDO()
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST
             . ';dbname=' . DB_NAME
             . ';charset=' . DB_CHARSET;

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die(
                '<div style="padding:20px;background:#f8d7da;color:#842029;'
              . 'font-family:sans-serif;border-radius:8px;margin:20px;">'
              . '<strong>❌ Erreur de connexion PDO</strong><br><br>'
              . htmlspecialchars($e->getMessage()) . '<br><br>'
              . '<em>Vérifiez DB_HOST, DB_NAME, DB_USER, DB_PASS dans config/database.php</em>'
              . '</div>'
            );
        }
    }

    return $pdo;
}

// Alias pratique disponible dans tous les fichiers qui incluent celui-ci
$pdo = getPDO();
