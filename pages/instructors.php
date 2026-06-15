<!-- instructors.php -->
<?php
/**
 * Instructors Management Page
 * CRUD operations for driving instructors
 */
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$message = '';
$error = '';

// CREATE - Add new instructor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $nom = strtoupper(trim($_POST['nom']));
    $prenom = trim($_POST['prenom']);
    $nationalite = trim($_POST['nationalite']);
    $telephone = trim($_POST['telephone']);
    $experience = intval($_POST['experience']);
    
    $query = "INSERT INTO instructeurs (nom, prenom, nationalite, telephone, experience) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $nom, $prenom, $nationalite, $telephone, $experience);
    
    if ($stmt->execute()) {
        $message = "Instructor added successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// DELETE - Remove instructor
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $query = "DELETE FROM instructeurs WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Instructor deleted successfully!";
    } else {
        $error = "Cannot delete - instructor has lessons scheduled";
    }
}

// UPDATE - Edit instructor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = intval($_POST['id']);
    $nom = strtoupper(trim($_POST['nom']));
    $prenom = trim($_POST['prenom']);
    $nationalite = trim($_POST['nationalite']);
    $telephone = trim($_POST['telephone']);
    $experience = intval($_POST['experience']);
    
    $query = "UPDATE instructeurs SET nom=?, prenom=?, nationalite=?, telephone=?, experience=? WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssii", $nom, $prenom, $nationalite, $telephone, $experience, $id);
    
    if ($stmt->execute()) {
        $message = "Instructor updated successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// Fetch all instructors
$instructors = $conn->query("SELECT * FROM instructeurs ORDER BY id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructors - Auto Ecole</title>
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
                    <h1 class="h2">Instructors Management</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-person-badge-plus"></i> Add New Instructor
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
                        <table id="instructorsTable" class="table table-striped">
                            <thead>
                                <tr><th>ID</th><th>Name</th><th>Nationality</th><th>Phone</th><th>Experience (years)</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php while($row = $instructors->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['prenom'] . ' ' . $row['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nationalite']); ?></td>
                                    <td><?php echo htmlspecialchars($row['telephone']); ?></td>
                                    <td><?php echo $row['experience']; ?> years</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal-<?php echo $row['id']; ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this instructor?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                
                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal-<?php echo $row['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Instructor</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="action" value="edit">
                                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                    <div class="mb-3">
                                                        <label>First Name</label>
                                                        <input type="text" name="prenom" class="form-control" value="<?php echo htmlspecialchars($row['prenom']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Last Name</label>
                                                        <input type="text" name="nom" class="form-control" value="<?php echo htmlspecialchars($row['nom']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Nationality</label>
                                                        <input type="text" name="nationalite" class="form-control" value="<?php echo htmlspecialchars($row['nationalite']); ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Phone</label>
                                                        <input type="text" name="telephone" class="form-control" value="<?php echo htmlspecialchars($row['telephone']); ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Experience (years)</label>
                                                        <input type="number" name="experience" class="form-control" value="<?php echo $row['experience']; ?>" min="1" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Instructor Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Instructor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label>First Name</label>
                            <input type="text" name="prenom" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Last Name</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Nationality</label>
                            <input type="text" name="nationalite" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Phone</label>
                            <input type="text" name="telephone" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Experience (years)</label>
                            <input type="number" name="experience" class="form-control" min="1" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Instructor</button>
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
            $('#instructorsTable').DataTable();
        });
    </script>
</body>
</html>