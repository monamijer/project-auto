<?php
/**
 * config/database.php
 * ──────────────────────────────────────────────────────────────────────────
 * Connexion PDO + constantes globales.
 * emulate_prepares = TRUE requis pour les appels CALL stored_procedure().
 */

// Charger les variables d'environnement depuis .env
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', '/project_auto');
define('ENVIRONMENT', $_ENV['ENVIRONMENT'] ?? 'development');

// Error reporting selon l'environnement
if (ENVIRONMENT === 'production') {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    ini_set('log_errors', 1);
    if (!is_dir(BASE_PATH . '/logs')) {
        mkdir(BASE_PATH . '/logs', 0755, true);
    }
    ini_set('error_log', BASE_PATH . '/logs/error.log');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
} else {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'jerome_auto_ecole');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_CHARSET', 'utf8mb4');

function getPDO()
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => true,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log('DB Connection failed: ' . $e->getMessage());
            if (ENVIRONMENT === 'production') {
                die('Erreur de connexion à la base de données.');
            }
            die(
                '<div style="padding:20px;background:#f8d7da;color:#842029;font-family:sans-serif;border-radius:8px;margin:20px;"><strong>❌ Erreur connexion PDO</strong><br><br>' .
                    htmlspecialchars($e->getMessage()) .
                    '</div>'
            );
        }
    }
    return $pdo;
}

$pdo = getPDO();
// Custom error handler pour les erreurs fatales
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        if (ENVIRONMENT === 'production') {
            error_log('Fatal error: ' . $error['message'] . ' in ' . $error['file'] . ' on line ' . $error['line']);
        }
        if (!headers_sent()) {
            header('Location: ' . BASE_URL . '/pages/404.php');
            exit();
        }
    }
});
