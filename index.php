<!-- index.php -->
<?php
/**
 * Home Page - Auto Ecole Management System
 * Displays dashboard statistics and system overview
 */
session_start();
require_once 'config/database.php';

// Check if user is logged in
// if (!isset($_SESSION['user_id'])) {
//     header('Location: login.php');
//     exit();
// }

// Fetch dashboard statistics
$stats = [];

// Total students
$query = "SELECT COUNT(*) as total FROM utilisateurs";
$result = $conn->query($query);
$stats['students'] = $result->fetch_assoc()['total'];

// Total instructors
$query = "SELECT COUNT(*) as total FROM instructeurs";
$result = $conn->query($query);
$stats['instructors'] = $result->fetch_assoc()['total'];

// Available vehicles
$query = "SELECT COUNT(*) as total FROM vehicules WHERE disponibilite = 1";
$result = $conn->query($query);
$stats['vehicles'] = $result->fetch_assoc()['total'];

// Scheduled lessons
$query = "SELECT COUNT(*) as total FROM lecons WHERE statut = 'programmée'";
$result = $conn->query($query);
$stats['lessons'] = $result->fetch_assoc()['total'];

// Total revenue
$query = "SELECT SUM(montant) as total FROM paiement";
$result = $conn->query($query);
$stats['revenue'] = $result->fetch_assoc()['total'] ?? 0;

// Recent payments
$query = "SELECT p.*, u.nom, u.prenom 
          FROM paiement p 
          JOIN utilisateurs u ON p.utilisateur_id = u.id 
          ORDER BY p.date_paiement DESC LIMIT 5";
$recent_payments = $conn->query($query);

// Upcoming lessons
$query = "SELECT l.*, u.nom as student_nom, u.prenom as student_prenom, 
          i.nom as instructor_nom, i.prenom as instructor_prenom,
          v.marque, v.modele
          FROM lecons l
          JOIN utilisateurs u ON l.utilisateur_id = u.id
          JOIN instructeurs i ON l.instructeur_id = i.id
          JOIN vehicules v ON l.vehicule_id = v.id
          WHERE l.statut = 'programmée' AND l.date_lecon >= NOW()
          ORDER BY l.date_lecon ASC LIMIT 5";
$upcoming_lessons = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Ecole - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar { min-height: 100vh; background-color: #2c3e50; }
        .sidebar .nav-link { color: #ecf0f1; }
        .sidebar .nav-link:hover { background-color: #34495e; }
        .sidebar .nav-link.active { background-color: #1abc9c; }
        .stat-card { border-radius: 10px; padding: 20px; margin-bottom: 20px; color: white; }
        .stat-card i { font-size: 48px; opacity: 0.7; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <span class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Students</h6>
                                    <h2 class="mb-0"><?php echo $stats['students']; ?></h2>
                                </div>
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Instructors</h6>
                                    <h2 class="mb-0"><?php echo $stats['instructors']; ?></h2>
                                </div>
                                <i class="bi bi-person-badge-fill"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Vehicles</h6>
                                    <h2 class="mb-0"><?php echo $stats['vehicles']; ?></h2>
                                </div>
                                <i class="bi bi-car-front-fill"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Scheduled Lessons</h6>
                                    <h2 class="mb-0"><?php echo $stats['lessons']; ?></h2>
                                </div>
                                <i class="bi bi-calendar-check-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Recent Payments</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm mb-0">
                                        <thead>
                                            <tr><th>Date</th><th>Student</th><th>Amount</th><th>Method</th></tr>
                                        </thead>
                                        <tbody>
                                            <?php while($row = $recent_payments->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $row['date_paiement']; ?></td>
                                                <td><?php echo htmlspecialchars($row['prenom'] . ' ' . $row['nom']); ?></td>
                                                <td>$<?php echo number_format($row['montant'], 2); ?></td>
                                                <td><?php echo $row['methode']; ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Upcoming Lessons</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm mb-0">
                                        <thead>
                                            <tr><th>Date</th><th>Student</th><th>Instructor</th><th>Vehicle</th></tr>
                                        </thead>
                                        <tbody>
                                            <?php while($row = $upcoming_lessons->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo date('d/m/Y H:i', strtotime($row['date_lecon'])); ?></td>
                                                <td><?php echo htmlspecialchars($row['student_prenom'] . ' ' . $row['student_nom']); ?></td>
                                                <td><?php echo htmlspecialchars($row['instructor_prenom'] . ' ' . $row['instructor_nom']); ?></td>
                                                <td><?php echo htmlspecialchars($row['marque'] . ' ' . $row['modele']); ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <a href="pages/students.php" class="btn btn-outline-primary w-100 mb-2">
                                            <i class="bi bi-person-plus"></i> Register Student
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="pages/payments.php" class="btn btn-outline-success w-100 mb-2">
                                            <i class="bi bi-cash-stack"></i> Record Payment
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="pages/lessons.php" class="btn btn-outline-info w-100 mb-2">
                                            <i class="bi bi-calendar-plus"></i> Schedule Lesson
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="pages/vehicles.php" class="btn btn-outline-warning w-100 mb-2">
                                            <i class="bi bi-car-front"></i> Manage Vehicles
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>