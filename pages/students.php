<!-- students.php -->
<?php
/**
 * Students Management Page
 * CRUD operations for driving school students
 */
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle CRUD operations
$message = '';
$error = '';

// CREATE - Add new student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $nom = strtoupper(trim($_POST['nom']));
    $prenom = trim($_POST['prenom']);
    $nationalite = trim($_POST['nationalite']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $formation_id = intval($_POST['formation_id']);
    
    $query = "INSERT INTO utilisateurs (nom, prenom, nationalite, email, telephone, formation_id) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssi", $nom, $prenom, $nationalite, $email, $telephone, $formation_id);
    
    if ($stmt->execute()) {
        $message = "Student added successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// DELETE - Remove student
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $query = "DELETE FROM utilisateurs WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Student deleted successfully!";
    } else {
        $error = "Cannot delete - student has lessons scheduled";
    }
}

// UPDATE - Edit student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = intval($_POST['id']);
    $nom = strtoupper(trim($_POST['nom']));
    $prenom = trim($_POST['prenom']);
    $nationalite = trim($_POST['nationalite']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $formation_id = intval($_POST['formation_id']);
    
    $query = "UPDATE utilisateurs SET nom=?, prenom=?, nationalite=?, email=?, telephone=?, formation_id=? WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssii", $nom, $prenom, $nationalite, $email, $telephone, $formation_id, $id);
    
    if ($stmt->execute()) {
        $message = "Student updated successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// Fetch all students with their formations
$query = "SELECT u.*, f.nom as formation_nom, f.prix as formation_prix 
          FROM utilisateurs u 
          LEFT JOIN formations f ON u.formation_id = f.id 
          ORDER BY u.date_inscription DESC";
$students = $conn->query($query);

// Fetch formations for dropdown
$formations = $conn->query("SELECT * FROM formations ORDER BY id");

// Get student data for edit modal
$edit_student = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $query = "SELECT * FROM utilisateurs WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_student = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students - Auto Ecole</title>
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
                    <h1 class="h2">Students Management</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-person-plus"></i> Add New Student
                    </button>
                </div>

                <?php if($message): ?>
                    <div class="alert alert-success alert-dismissible fade show"><?php echo $message; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>
                <?php if($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show"><?php echo $error; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="studentsTable" class="table table-striped">
                                <thead>
                                    <tr><th>ID</th><th>Name</th><th>Nationality</th><th>Email</th><th>Phone</th><th>Formation</th><th>Registration Date</th><th>Actions</th></tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $students->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['prenom'] . ' ' . $row['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nationalite']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['telephone']); ?></td>
                                        <td>
                                            <span class="badge bg-info"><?php echo htmlspecialchars($row['formation_nom']); ?></span>
                                            <small class="text-muted">($<?php echo number_format($row['formation_prix'], 2); ?>)</small>
                                        </td>
                                        <td><?php echo $row['date_inscription']; ?></td>
                                        <td>
                                            <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal-<?php echo $row['id']; ?>">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this student?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                            <a href="student_profile.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    
                                    <!-- Edit Modal for each student -->
                                    <div class="modal fade" id="editModal-<?php echo $row['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Student</h5>
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
                                                            <label>Email</label>
                                                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Phone</label>
                                                            <input type="text" name="telephone" class="form-control" value="<?php echo htmlspecialchars($row['telephone']); ?>">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Formation</label>
                                                            <select name="formation_id" class="form-control" required>
                                                                <?php 
                                                                $f = $conn->query("SELECT * FROM formations");
                                                                while($form = $f->fetch_assoc()): ?>
                                                                <option value="<?php echo $form['id']; ?>" <?php echo $form['id'] == $row['formation_id'] ? 'selected' : ''; ?>>
                                                                    <?php echo $form['nom']; ?> - $<?php echo $form['prix']; ?>
                                                                </option>
                                                                <?php endwhile; ?>
                                                            </select>
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
                </div>
            </main>
        </div>
    </div>

    <!-- Add Student Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Student</h5>
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
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Phone</label>
                            <input type="text" name="telephone" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Formation</label>
                            <select name="formation_id" class="form-control" required>
                                <option value="">Select Formation</option>
                                <?php 
                                $formations->data_seek(0);
                                while($form = $formations->fetch_assoc()): ?>
                                <option value="<?php echo $form['id']; ?>">
                                    <?php echo $form['nom']; ?> - $<?php echo $form['prix']; ?> (<?php echo $form['duree_mois']; ?> months)
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Student</button>
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
            $('#studentsTable').DataTable({
                pageLength: 25,
                language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/en-GB.json' }
            });
        });
    </script>
</body>
</html>