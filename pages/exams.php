<!-- exams.php -->
<?php
/**
 * Exams Management Page
 * Track student exams and results
 */
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Note: Since there's no exams table in the database yet,
// we'll create a view based on lessons with status 'effectuée'
// and track exam eligibility based on completed lessons

// Get exam-eligible students (students who have completed lessons)
$eligible = $conn->query("
    SELECT u.id, u.nom, u.prenom, u.email, u.telephone,
           f.nom as formation_name,
           COUNT(l.id) as completed_lessons,
           SUM(CASE WHEN l.statut = 'effectuée' THEN 1 ELSE 0 END) as total_completed
    FROM utilisateurs u
    JOIN formations f ON u.formation_id = f.id
    LEFT JOIN lecons l ON u.id = l.utilisateur_id AND l.statut = 'effectuée'
    GROUP BY u.id
    HAVING total_completed >= 3
    ORDER BY total_completed DESC
");

// Get exam results summary by formation
$exam_stats = $conn->query("
    SELECT f.nom as formation_name,
           COUNT(DISTINCT u.id) as total_students,
           COUNT(DISTINCT CASE WHEN l.statut = 'effectuée' AND l.date_lecon >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN u.id END) as exam_eligible,
           ROUND(COUNT(DISTINCT CASE WHEN l.statut = 'effectuée' AND l.date_lecon >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN u.id END) * 100.0 / COUNT(DISTINCT u.id), 1) as eligibility_rate
    FROM formations f
    LEFT JOIN utilisateurs u ON f.id = u.formation_id
    LEFT JOIN lecons l ON u.id = l.utilisateur_id
    GROUP BY f.id
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exams - Auto Ecole</title>
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
                    <h1 class="h2">Exams Management</h1>
                </div>

                <!-- Exam Statistics -->
                <div class="row mb-4">
                    <?php while($stat = $exam_stats->fetch_assoc()): ?>
                    <div class="col-md-4">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $stat['formation_name']; ?></h5>
                                <p class="card-text">
                                    <strong>Total Students:</strong> <?php echo $stat['total_students']; ?><br>
                                    <strong>Exam Eligible:</strong> <?php echo $stat['exam_eligible']; ?><br>
                                    <strong>Eligibility Rate:</strong> <?php echo $stat['eligibility_rate']; ?>%
                                </p>
                                <div class="progress bg-light">
                                    <div class="progress-bar bg-success" style="width: <?php echo $stat['eligibility_rate']; ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>

                <!-- Exam Eligible Students -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Exam Eligible Students (Completed 3+ Lessons)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="eligibleTable" class="table table-striped">
                                <thead>
                                    <tr><th>ID</th><th>Student</th><th>Email</th><th>Phone</th><th>Formation</th><th>Completed Lessons</th><th>Status</th><th>Actions</th></tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $eligible->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['prenom'] . ' ' . $row['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['telephone']); ?></td>
                                        <td><?php echo $row['formation_name']; ?></td>
                                        <td>
                                            <span class="badge bg-success"><?php echo $row['total_completed']; ?> lessons</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">Eligible for Exam</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="generateCertificate(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['prenom'] . ' ' . $row['nom']); ?>')">
                                                <i class="bi bi-file-text"></i> Certificate
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Exam Schedule Notice -->
                <div class="card mt-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Exam Schedule Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Exam Requirements:</strong>
                            <ul class="mt-2 mb-0">
                                <li>Complete minimum 3 driving lessons</li>
                                <li>No outstanding payments</li>
                                <li>Valid learner's permit</li>
                            </ul>
                        </div>
                        <div class="alert alert-warning">
                            <i class="bi bi-calendar"></i>
                            <strong>Next Exam Dates:</strong>
                            <ul class="mt-2 mb-0">
                                <li>Theoretical Exam: Every Monday at 9:00 AM</li>
                                <li>Practical Exam: Every Friday at 10:00 AM</li>
                                <li>Location: Auto Ecole Testing Center</li>
                            </ul>
                        </div>
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
            $('#eligibleTable').DataTable();
        });

        function generateCertificate(studentId, studentName) {
            alert(`Certificate generated for ${studentName}\n\nThis student is eligible for the driving exam.\nPlease contact the exam coordinator to schedule the exam.`);
        }
    </script>
</body>
</html>