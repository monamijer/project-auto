<!-- vehicles.php -->
<?php
/**
 * Vehicles Management Page
 * CRUD operations for driving school vehicles
 */
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$message = '';
$error = '';

// CREATE - Add new vehicle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $marque = trim($_POST['marque']);
    $modele = trim($_POST['modele']);
    $immatriculation = strtoupper(trim($_POST['immatriculation']));
    $disponibilite = isset($_POST['disponibilite']) ? 1 : 0;
    
    $query = "INSERT INTO vehicules (marque, modele, immatriculation, disponibilite) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $marque, $modele, $immatriculation, $disponibilite);
    
    if ($stmt->execute()) {
        $message = "Vehicle added successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// DELETE - Remove vehicle
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $query = "DELETE FROM vehicules WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Vehicle deleted successfully!";
    } else {
        $error = "Cannot delete - vehicle is assigned to lessons";
    }
}

// UPDATE - Edit vehicle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = intval($_POST['id']);
    $marque = trim($_POST['marque']);
    $modele = trim($_POST['modele']);
    $immatriculation = strtoupper(trim($_POST['immatriculation']));
    $disponibilite = isset($_POST['disponibilite']) ? 1 : 0;
    
    $query = "UPDATE vehicules SET marque=?, modele=?, immatriculation=?, disponibilite=? WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssii", $marque, $modele, $immatriculation, $disponibilite, $id);
    
    if ($stmt->execute()) {
        $message = "Vehicle updated successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// Toggle availability
if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $query = "UPDATE vehicules SET disponibilite = NOT disponibilite WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $message = "Vehicle status updated!";
}

// Fetch all vehicles
$vehicles = $conn->query("SELECT * FROM vehicules ORDER BY id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicles - Auto Ecole</title>
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
                    <h1 class="h2">Vehicles Management</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-car-front"></i> Add New Vehicle
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
                        <table id="vehiclesTable" class="table table-striped">
                            <thead>
                                <tr><th>ID</th><th>Brand</th><th>Model</th><th>License Plate</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php while($row = $vehicles->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['marque']); ?></td>
                                    <td><?php echo htmlspecialchars($row['modele']); ?></td>
                                    <td><?php echo htmlspecialchars($row['immatriculation']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $row['disponibilite'] ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo $row['disponibilite'] ? 'Available' : 'Unavailable'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal-<?php echo $row['id']; ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <a href="?toggle=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </a>
                                        <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this vehicle?')">
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
                                                    <h5 class="modal-title">Edit Vehicle</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="action" value="edit">
                                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                    <div class="mb-3">
                                                        <label>Brand</label>
                                                        <input type="text" name="marque" class="form-control" value="<?php echo htmlspecialchars($row['marque']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Model</label>
                                                        <input type="text" name="modele" class="form-control" value="<?php echo htmlspecialchars($row['modele']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>License Plate</label>
                                                        <input type="text" name="immatriculation" class="form-control" value="<?php echo htmlspecialchars($row['immatriculation']); ?>" required>
                                                    </div>
                                                    <div class="mb-3 form-check">
                                                        <input type="checkbox" name="disponibilite" class="form-check-input" <?php echo $row['disponibilite'] ? 'checked' : ''; ?>>
                                                        <label class="form-check-label">Available</label>
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

    <!-- Add Vehicle Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Vehicle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label>Brand</label>
                            <input type="text" name="marque" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Model</label>
                            <input type="text" name="modele" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>License Plate</label>
                            <input type="text" name="immatriculation" class="form-control" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="disponibilite" class="form-check-input" checked>
                            <label class="form-check-label">Available</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Vehicle</button>
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
            $('#vehiclesTable').DataTable();
        });
    </script>
</body>
</html>