<!-- lessons.php -->
<?php
/**
 * Lessons Management Page
 * CRUD operations for driving lessons scheduling
 */
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$message = '';
$error = '';

// CREATE - Schedule new lesson
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $utilisateur_id = intval($_POST['student_id']);
    $instructeur_id = intval($_POST['instructor_id']);
    $vehicule_id = intval($_POST['vehicle_id']);
    $date_lecon = $_POST['date_lecon'];
    $statut = 'programmée';
    
    $query = "INSERT INTO lecons (utilisateur_id, instructeur_id, vehicule_id, date_lecon, statut) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiiss", $utilisateur_id, $instructeur_id, $vehicule_id, $date_lecon, $statut);
    
    if ($stmt->execute()) {
        $message = "Lesson scheduled successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// UPDATE - Cancel or complete lesson
if (isset($_GET['cancel'])) {
    $id = intval($_GET['cancel']);
    $query = "UPDATE lecons SET statut = 'annulée' WHERE id = ? AND statut = 'programmée'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Lesson cancelled!";
    }
}

if (isset($_GET['complete'])) {
    $id = intval($_GET['complete']);
    $query = "UPDATE lecons SET statut = 'effectuée' WHERE id = ? AND statut = 'programmée'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Lesson marked as completed!";
    }
}

// DELETE - Remove lesson
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $query = "DELETE FROM lecons WHERE id = ? AND statut = 'programmée'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Lesson deleted!";
    } else {
        $error = "Cannot delete - lesson already completed";
    }
}

// Fetch all lessons with details
$lessons = $conn->query("
    SELECT l.*, 
           CONCAT(u.prenom, ' ', u.nom) as student_name,
           CONCAT(i.prenom, ' ', i.nom) as instructor_name,
           CONCAT(v.marque, ' ', v.modele) as vehicle_name,
           f.nom as formation_name
    FROM lecons l
    JOIN utilisateurs u ON l.utilisateur_id = u.id
    JOIN instructeurs i ON l.instructeur_id = i.id
    JOIN vehicules v ON l.vehicule_id = v.id
    JOIN formations f ON u.formation_id = f.id
    ORDER BY l.date_lecon DESC
");

// Fetch dropdown data
$students = $conn->query("SELECT id, prenom, nom FROM utilisateurs ORDER BY prenom");
$instructors = $conn->query("SELECT id, prenom, nom FROM instructeurs ORDER BY prenom");
$vehicles = $conn->query("SELECT id, marque, modele, immatriculation FROM vehicules WHERE disponibilite = 1 ORDER BY marque");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lessons - Auto Ecole</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Lessons Management</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-calendar-plus"></i> Schedule Lesson
                    </button>
                </div>

                <?php if($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <table id="lessonsTable" class="table table-striped">
                            <thead>
                                <tr><th>ID</th><th>Date & Time</th><th>Student</th><th>Instructor</th><th>Vehicle</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php while($row = $lessons->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($row['date_lecon'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['student_name']); ?><br><small class="text-muted"><?php echo $row['formation_name']; ?></small></td>
                                    <td><?php echo htmlspecialchars($row['instructor_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['vehicle_name']); ?></td>
                                    <td>
                                        <?php
                                        $status_class = '';
                                        switch($row['statut']) {
                                            case 'programmée': $status_class = 'bg-warning'; break;
                                            case 'effectuée': $status_class = 'bg-success'; break;
                                            case 'annulée': $status_class = 'bg-danger'; break;
                                        }
                                        ?>
                                        <span class="badge <?php echo $status_class; ?>"><?php echo $row['statut']; ?></span>
                                    </td>
                                    <td>
                                        <?php if($row['statut'] == 'programmée'): ?>
                                            <a href="?complete=<?php echo $row['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Mark this lesson as completed?')">
                                                <i class="bi bi-check-lg"></i>
                                            </a>
                                            <a href="?cancel=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Cancel this lesson?')">
                                                <i class="bi bi-x-lg"></i>
                                            </a>
                                            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-secondary" onclick="return confirm('Delete this lesson?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Schedule Lesson Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Schedule New Lesson</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label>Student</label>
                            <select name="student_id" class="form-control" required>
                                <option value="">Select Student</option>
                                <?php while($s = $students->fetch_assoc()): ?>
                                <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['prenom'] . ' ' . $s['nom']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Instructor</label>
                            <select name="instructor_id" class="form-control" required>
                                <option value="">Select Instructor</option>
                                <?php 
                                $instructors->data_seek(0);
                                while($i = $instructors->fetch_assoc()): ?>
                                <option value="<?php echo $i['id']; ?>"><?php echo htmlspecialchars($i['prenom'] . ' ' . $i['nom']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Vehicle</label>
                            <select name="vehicle_id" class="form-control" required>
                                <option value="">Select Vehicle</option>
                                <?php 
                                $vehicles->data_seek(0);
                                while($v = $vehicles->fetch_assoc()): ?>
                                <option value="<?php echo $v['id']; ?>"><?php echo htmlspecialchars($v['marque'] . ' ' . $v['modele'] . ' (' . $v['immatriculation'] . ')'); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Date & Time</label>
                            <input type="datetime-local" name="date_lecon" class="form-control" required>
                            <small class="text-muted">Nocturne lessons must be after 18:00</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Schedule Lesson</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#lessonsTable').DataTable({
                order: [[1, 'desc']]
            });
        });
    </script>
</body>
</html>