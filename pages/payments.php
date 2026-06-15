<!-- payments.php -->
<?php
/**
 * Payments Management Page
 * CRUD operations for student payments
 */
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$message = '';
$error = '';

// CREATE - Record new payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $utilisateur_id = intval($_POST['student_id']);
    $montant = intval($_POST['montant']);
    $date_paiement = $_POST['date_paiement'];
    $methode = $_POST['methode'];
    
    $query = "INSERT INTO paiement (utilisateur_id, montant, date_paiement, methode) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiss", $utilisateur_id, $montant, $date_paiement, $methode);
    
    if ($stmt->execute()) {
        $message = "Payment recorded successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// DELETE - Remove payment
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $query = "DELETE FROM paiement WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Payment deleted successfully!";
    } else {
        $error = "Cannot delete payment";
    }
}

// Fetch all payments with student details
$payments = $conn->query("
    SELECT p.*, u.nom, u.prenom, u.email, f.nom as formation_name
    FROM paiement p
    JOIN utilisateurs u ON p.utilisateur_id = u.id
    JOIN formations f ON u.formation_id = f.id
    ORDER BY p.date_paiement DESC
");

// Fetch students for dropdown
$students = $conn->query("SELECT u.id, u.prenom, u.nom, f.nom as formation, f.prix 
                          FROM utilisateurs u 
                          JOIN formations f ON u.formation_id = f.id 
                          ORDER BY u.prenom");

// Get payment summary
$summary = $conn->query("
    SELECT 
        SUM(montant) as total_revenue,
        COUNT(*) as total_payments,
        AVG(montant) as avg_payment,
        methode,
        COUNT(*) as method_count
    FROM paiement
    GROUP BY methode WITH ROLLUP
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments - Auto Ecole</title>
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
                    <h1 class="h2">Payments Management</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-cash-stack"></i> Record Payment
                    </button>
                </div>

                <?php if($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h6 class="card-title">Total Revenue</h6>
                                <h3>$<?php echo number_format($conn->query("SELECT SUM(montant) as total FROM paiement")->fetch_assoc()['total'] ?? 0, 2); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h6 class="card-title">Total Payments</h6>
                                <h3><?php echo $conn->query("SELECT COUNT(*) as total FROM paiement")->fetch_assoc()['total']; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h6 class="card-title">Average Payment</h6>
                                <h3>$<?php echo number_format($conn->query("SELECT AVG(montant) as avg FROM paiement")->fetch_assoc()['avg'] ?? 0, 2); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h6 class="card-title">Outstanding Balance</h6>
                                <h3>$<?php 
                                    $total_due = $conn->query("SELECT SUM(prix) as total FROM formations f JOIN utilisateurs u ON u.formation_id = f.id")->fetch_assoc()['total'];
                                    $total_paid = $conn->query("SELECT SUM(montant) as total FROM paiement")->fetch_assoc()['total'];
                                    echo number_format(($total_due ?? 0) - ($total_paid ?? 0), 2);
                                ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <table id="paymentsTable" class="table table-striped">
                            <thead>
                                <tr><th>ID</th><th>Date</th><th>Student</th><th>Formation</th><th>Amount</th><th>Method</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php while($row = $payments->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo $row['date_paiement']; ?></td>
                                    <td><?php echo htmlspecialchars($row['prenom'] . ' ' . $row['nom']); ?><br><small class="text-muted"><?php echo $row['email']; ?></small></td>
                                    <td><?php echo $row['formation_name']; ?></td>
                                    <td>$<?php echo number_format($row['montant'], 2); ?></td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo $row['methode']; ?></span>
                                    </td>
                                    <td>
                                        <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this payment?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
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

    <!-- Record Payment Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Record New Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label>Student</label>
                            <select name="student_id" class="form-control" required>
                                <option value="">Select Student</option>
                                <?php while($s = $students->fetch_assoc()): ?>
                                <option value="<?php echo $s['id']; ?>">
                                    <?php echo htmlspecialchars($s['prenom'] . ' ' . $s['nom']); ?> - 
                                    <?php echo $s['formation']; ?> ($<?php echo $s['prix']; ?>)
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Amount ($)</label>
                            <input type="number" name="montant" class="form-control" min="1" max="900" required>
                        </div>
                        <div class="mb-3">
                            <label>Payment Date</label>
                            <input type="date" name="date_paiement" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Payment Method</label>
                            <select name="methode" class="form-control" required>
                                <option value="Carte">Credit Card</option>
                                <option value="Espèces">Cash</option>
                                <option value="Mobile Money">Mobile Money</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Record Payment</button>
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
            $('#paymentsTable').DataTable({
                order: [[1, 'desc']]
            });
        });
    </script>
</body>
</html>