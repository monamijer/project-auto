<!-- student_profile.php -->
<?php
/**
 * Student Profile Page - View detailed student information
 */
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$student_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch student details
$student = $conn->query("
    SELECT u.*, f.nom as formation_nom, f.prix as formation_prix, f.duree_mois
    FROM utilisateurs u
    JOIN formations f ON u.formation_id = f.id
    WHERE u.id = $student_id
")->fetch_assoc();

if (!$student) {
    header('Location: students.php');
    exit();
}

// Fetch student payments
$payments = $conn->query("
    SELECT * FROM paiement 
    WHERE utilisateur_id = $student_id 
    ORDER BY date_paiement DESC
");

// Fetch student lessons
$lessons = $conn->query("
    SELECT l.*, 
           CONCAT(i.prenom, ' ', i.nom) as instructor_name,
           CONCAT(v.marque, ' ', v.modele, ' (', v.immatriculation, ')') as vehicle_name
    FROM lecons l
    JOIN instructeurs i ON l.instructeur_id = i.id
    JOIN vehicules v ON l.vehicule_id = v.id
    WHERE l.utilisateur_id = $student_id
    ORDER BY l.date_lecon DESC
");

// Calculate totals
$total_paid = $conn->query("SELECT SUM(montant) as total FROM paiement WHERE utilisateur_id = $student_id")->fetch_assoc()['total'] ?? 0;
$balance = $student['formation_prix'] - $total_paid;
$completed_lessons = $conn->query("SELECT COUNT(*) as total FROM lecons WHERE utilisateur_id = $student_id AND statut = 'effectuée'")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile - <?php echo htmlspecialchars($student['prenom'] . ' ' . $student['nom']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Student Profile</h1>
                    <a href="students.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Students
                    </a>
                </div>

                <!-- Student Information -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Personal Information</h5>
                            </div>
                            <div class="card-body">
                                <h4><?php echo htmlspecialchars($student['prenom'] . ' ' . $student['nom']); ?></h4>
                                <hr>
                                <p><strong>Nationality:</strong> <?php echo htmlspecialchars($student['nationalite'] ?? 'N/A'); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($student['telephone'] ?? 'N/A'); ?></p>
                                <p><strong>Registration Date:</strong> <?php echo $student['date_inscription']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Formation Details</h5>
                            </div>
                            <div class="card-body">
                                <h4><?php echo $student['formation_nom']; ?></h4>
                                <hr>
                                <p><strong>Price:</strong> $<?php echo number_format($student['formation_prix'], 2); ?></p>
                                <p><strong>Duration:</strong> <?php echo $student['duree_mois']; ?> months</p>
                                <p><strong>Total Paid:</strong> $<?php echo number_format($total_paid, 2); ?></p>
                                <p><strong>Balance:</strong> $<?php echo number_format($balance, 2); ?></p>
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: <?php echo ($total_paid / $student['formation_prix']) * 100; ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Lesson Summary</h5>
                            </div>
                            <div class="card-body">
                                <h4><?php echo $completed_lessons; ?> Lessons</h4>
                                <hr>
                                <p><strong>Status:</strong> 
                                    <?php if($balance <= 0): ?>
                                        <span class="badge bg-success">Paid in Full</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Outstanding Balance</span>
                                    <?php endif; ?>
                                </p>
                                <p><strong>Exam Eligibility:</strong>
                                    <?php if($completed_lessons >= 3): ?>
                                        <span class="badge bg-success">Eligible</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Need <?php echo 3 - $completed_lessons; ?> more lessons</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Payment History</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr><th>Date</th><th>Amount</th><th>Method</th></tr>
                                </thead>
                                <tbody>
                                    <?php while($payment = $payments->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $payment['date_paiement']; ?></td>
                                        <td>$<?php echo number_format($payment['montant'], 2); ?></td>
                                        <td><?php echo $payment['methode']; ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php if($payments->num_rows == 0): ?>
                                    <tr><td colspan="3" class="text-center">No payments recorded</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Lesson History -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Lesson History</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr><th>Date & Time</th><th>Instructor</th><th>Vehicle</th><th>Status</th></tr>
                                </thead>
                                <tbody>
                                    <?php while($lesson = $lessons->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y H:i', strtotime($lesson['date_lecon'])); ?></td>
                                        <td><?php echo htmlspecialchars($lesson['instructor_name']); ?></td>
                                        <td><?php echo htmlspecialchars($lesson['vehicle_name']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $lesson['statut'] == 'effectuée' ? 'bg-success' : ($lesson['statut'] == 'programmée' ? 'bg-warning' : 'bg-danger'); ?>">
                                                <?php echo $lesson['statut']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php if($lessons->num_rows == 0): ?>
                                    <tr><td colspan="4" class="text-center">No lessons scheduled</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>