<!-- login.php -->
<?php
/**
 * Login Page - Authenticate users
 */
session_start();
require_once 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Simple authentication (in production, use proper password hashing)
    $query = "SELECT * FROM expirations_utilisateurs WHERE utilisateur = ? AND statut = 'actif'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['user_id'] = $username;
        $_SESSION['username'] = $username;
        
        // Log successful connection
        $log = "INSERT INTO journal_connexions (utilisateur, heure_connexion, statut, message) VALUES (?, NOW(), 'AUTORISÉE', 'Successful login')";
        $stmt_log = $conn->prepare($log);
        $stmt_log->bind_param("s", $username);
        $stmt_log->execute();
        
        header('Location: index.php');
        exit();
    } else {
        $error = 'Invalid username or account expired';
        
        // Log failed attempt
        $log = "INSERT INTO journal_connexions (utilisateur, heure_connexion, statut, message) VALUES (?, NOW(), 'REFUSÉE', 'Failed login attempt')";
        $stmt_log = $conn->prepare($log);
        $stmt_log->bind_param("s", $username);
        $stmt_log->execute();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Auto Ecole</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .login-card { border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-4">
                <div class="card login-card">
                    <div class="card-body p-5">
                        <h3 class="text-center mb-4">Auto Ecole</h3>
                        <h5 class="text-center text-muted mb-4">Login to Dashboard</h5>
                        
                        <?php if($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>