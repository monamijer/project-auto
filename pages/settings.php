<!-- settings.php -->
<?php
/**
 * Settings Page - System settings and user management
 */
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle user account management
$message = '';
$error = '';

// Add/Edit user account
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_user') {
        $utilisateur = trim($_POST['utilisateur']);
        $date_expiration = $_POST['date_expiration'];
        $statut = $_POST['statut'];
        $commentaire = trim($_POST['commentaire']);
        
        $query = "INSERT INTO expirations_utilisateurs (utilisateur, date_expiration, statut, commentaire) 
                  VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssss", $utilisateur, $date_expiration, $statut, $commentaire);
        
        if ($stmt->execute()) {
            $message = "User account created successfully!";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
    
    // Update connection schedule
    if ($_POST['action'] === 'update_schedule') {
        $id = intval($_POST['id']);
        $heure_debut = $_POST['heure_debut'];
        $heure_fin = $_POST['heure_fin'];
        $actif = isset($_POST['actif']) ? 1 : 0;
        $commentaire = trim($_POST['commentaire']);
        
        $query = "UPDATE plages_connexion SET heure_debut=?, heure_fin=?, actif=?, commentaire=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssisi", $heure_debut, $heure_fin, $actif, $commentaire, $id);
        $stmt->execute();
        $message = "Schedule updated!";
    }
}

// Delete user account
if (isset($_GET['delete_user'])) {
    $id = intval($_GET['delete_user']);
    $query = "DELETE FROM expirations_utilisateurs WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "User account deleted!";
    }
}

// Fetch all user accounts
$users = $conn->query("SELECT * FROM expirations_utilisateurs ORDER BY date_expiration");

// Fetch connection schedules
$schedules = $conn->query("SELECT * FROM plages_connexion ORDER BY utilisateur");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Auto Ecole</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">System Settings</h1>
                </div>

                <?php if($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <!-- User Accounts Management -->
                <div class="card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">User Accounts</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="bi bi-person-plus"></i> Add User
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr><th>Username</th><th>Expiration Date</th><th>Status</th><th>Comment</th><th>Actions</th></tr>
                                </thead>
                                <tbody>
                                    <?php while($user = $users->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['utilisateur']); ?></td>
                                        <td><?php echo $user['date_expiration']; ?></td>
                                        <td>
                                            <span class="badge <?php echo $user['statut'] == 'actif' ? 'bg-success' : 'bg-danger'; ?>">
                                                <?php echo $user['statut']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($user['commentaire']); ?></td>
                                        <td>
                                            <a href="?delete_user=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                         </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Connection Schedules -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Connection Schedules</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr><th>User</th><th>Start Time</th><th>End Time</th><th>Days</th><th>Status</th><th>Actions</th></tr>
                                </thead>
                                <tbody>
                                    <?php while($sch = $schedules->fetch_assoc()): ?>
                                    <tr>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="update_schedule">
                                            <input type="hidden" name="id" value="<?php echo $sch['id']; ?>">
                                            <td><?php echo htmlspecialchars($sch['utilisateur']); ?></td>
                                            <td><input type="time" name="heure_debut" class="form-control form-control-sm" value="<?php echo $sch['heure_debut']; ?>"></td>
                                            <td><input type="time" name="heure_fin" class="form-control form-control-sm" value="<?php echo $sch['heure_fin']; ?>"></td>
                                            <td><?php echo $sch['jours_autorises']; ?></td>
                                            <td>
                                                <div class="form-check">
                                                    <input type="checkbox" name="actif" class="form-check-input" <?php echo $sch['actif'] ? 'checked' : ''; ?>>
                                                    <label class="form-check-label">Active</label>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" name="commentaire" class="form-control form-control-sm" value="<?php echo htmlspecialchars($sch['commentaire']); ?>" placeholder="Comment">
                                            </td>
                                            <td>
                                                <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                            </td>
                                        </form>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- System Information -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">System Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Database Version:</span>
                                        <strong>MariaDB 10.4.32</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>PHP Version:</span>
                                        <strong><?php echo phpversion(); ?></strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Server Time:</span>
                                        <strong><?php echo date('Y-m-d H:i:s'); ?></strong>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Total Students:</span>
                                        <strong><?php echo $conn->query("SELECT COUNT(*) as total FROM utilisateurs")->fetch_assoc()['total']; ?></strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Total Instructors:</span>
                                        <strong><?php echo $conn->query("SELECT COUNT(*) as total FROM instructeurs")->fetch_assoc()['total']; ?></strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Total Vehicles:</span>
                                        <strong><?php echo $conn->query("SELECT COUNT(*) as total FROM vehicules")->fetch_assoc()['total']; ?></strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New User Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_user">
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="utilisateur" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Expiration Date</label>
                            <input type="datetime-local" name="date_expiration" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Status</label>
                            <select name="statut" class="form-control">
                                <option value="actif">Active</option>
                                <option value="expiré">Expired</option>
                                <option value="suspendu">Suspended</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Comment</label>
                            <textarea name="commentaire" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>