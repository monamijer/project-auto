<?php
/**
 * config/database.php
 * ──────────────────────────────────────────────────────────────────────────
 * Connexion PDO + constantes globales.
 * emulate_prepares = TRUE requis pour les appels CALL stored_procedure().
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', '/project_auto');

define('DB_HOST', 'localhost');
define('DB_NAME', 'jerome_auto_ecole');
define('DB_USER', 'root');
define('DB_PASS', 'jer000');
define('DB_CHARSET', 'utf8mb4');

function getPDO()
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // TRUE obligatoire pour CALL … @out_param
            PDO::ATTR_EMULATE_PREPARES => true,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die(
                '<div style="padding:20px;background:#f8d7da;color:#842029;font-family:sans-serif;border-radius:8px;margin:20px;">' .
                    '<strong>❌ Erreur connexion PDO</strong><br><br>' .
                    htmlspecialchars($e->getMessage()) .
                    '</div>'
            );
        }
    }
    return $pdo;
}

$pdo = getPDO();
