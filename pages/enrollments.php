<!-- enrollments.php -->
<?php
/**
 * Enrollments Page - View student enrollments and formation statistics
 */
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch formation statistics
$formation_stats = $conn->query("
    SELECT f.*, 
           COUNT(u.id) as enrolled_count,
           COALESCE(SUM(p.montant), 0) as total_paid,
           (f.prix * COUNT(u.id)) as expected_revenue,
           (f.prix * COUNT(u.id)) - COALESCE(SUM(p.montant), 0) as outstanding
    FROM formations f
    LEFT JOIN utilisateurs u ON f.id = u.formation_id
    LEFT JOIN paiement p ON u.id = p.utilisateur_id
    GROUP BY f.id
");

// Fetch all enrollments with details
$enrollments = $conn->query("
    SELECT u.*, f.nom as formation_nom, f.prix as formation_prix, f.duree_mois,
           COALESCE(SUM(p.montant), 0) as total_paid
    FROM utilisateurs u
    JOIN formations f ON u.formation_id = f.id
    LEFT JOIN paiement p ON u.id = p.utilisateur_id
    GROUP BY u.id
    ORDER BY u.date_inscription DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollments - Auto Ecole</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Enrollments Overview</h1>
                </div>

                <!-- Formation Statistics Charts -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Students per Formation</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="formationChart" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Revenue by Formation</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="revenueChart" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formation Stats Cards -->
                <div class="row mb-4">
                    <?php 
                    $formation_stats->data_seek(0);
                    while($stat = $formation_stats->fetch_assoc()): 
                        $percentage = $stat['total_paid'] > 0 ? ($stat['total_paid'] / $stat['expected_revenue']) * 100 : 0;
                    ?>
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $stat['nom']; ?></h5>
                                <p class="card-text">
                                    <strong>Price:</strong> $<?php echo number_format($stat['prix'], 2); ?><br>
                                    <strong>Duration:</strong> <?php echo $stat['duree_mois']; ?> months<br>
                                    <strong>Enrolled:</strong> <?php echo $stat['enrolled_count']; ?> students<br>
                                    <strong>Collected:</strong> $<?php echo number_format($stat['total_paid'], 2); ?><br>
                                    <strong>Outstanding:</strong> $<?php echo number_format($stat['outstanding'], 2); ?>
                                </p>
                                <div class="progress">
                                    <div class="progress-bar" style="width: <?php echo $percentage; ?>%">
                                        <?php echo round($percentage, 1); ?>%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>

                <!-- Enrollments Table -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">All Enrollments</h5>
                    </div>
                    <div class="card-body">
                        <table id="enrollmentsTable" class="table table-striped">
                            <thead>
                                <tr><th>ID</th><th>Student</th><th>Email</th><th>Phone</th><th>Formation</th><th>Price</th><th>Paid</th><th>Balance</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                <?php while($row = $enrollments->fetch_assoc()): 
                                    $balance = $row['formation_prix'] - $row['total_paid'];
                                    $status_class = $balance <= 0 ? 'success' : ($balance < ($row['formation_prix'] / 2) ? 'warning' : 'danger');
                                    $status_text = $balance <= 0 ? 'Paid in Full' : ($balance < ($row['formation_prix'] / 2) ? 'Partial' : 'Outstanding');
                                ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['prenom'] . ' ' . $row['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['telephone']); ?></td>
                                    <td><?php echo $row['formation_nom']; ?></td>
                                    <td>$<?php echo number_format($row['formation_prix'], 2); ?></td>
                                    <td>$<?php echo number_format($row['total_paid'], 2); ?></td>
                                    <td>$<?php echo number_format($balance, 2); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#enrollmentsTable').DataTable();
        });

        // Formation Chart
        <?php 
        $formation_stats->data_seek(0);
        $formations_names = [];
        $formations_counts = [];
        while($stat = $formation_stats->fetch_assoc()) {
            $formations_names[] = $stat['nom'];
            $formations_counts[] = $stat['enrolled_count'];
        }
        ?>
        new Chart(document.getElementById('formationChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($formations_names); ?>,
                datasets: [{
                    label: 'Number of Students',
                    data: <?php echo json_encode($formations_counts); ?>,
                    backgroundColor: ['#667eea', '#f093fb', '#4facfe', '#43e97b']
                }]
            }
        });

        <?php 
        $formation_stats->data_seek(0);
        $revenues = [];
        while($stat = $formation_stats->fetch_assoc()) {
            $revenues[] = $stat['total_paid'];
        }
        ?>
        new Chart(document.getElementById('revenueChart'), {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($formations_names); ?>,
                datasets: [{
                    label: 'Revenue Collected',
                    data: <?php echo json_encode($revenues); ?>,
                    backgroundColor: ['#667eea', '#f093fb', '#4facfe', '#43e97b']
                }]
            }
        });
    </script>
</body>
</html>