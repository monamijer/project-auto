<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();
header('Content-Type: application/json');

$events = $pdo->query("SELECT * FROM v_lecons_calendrier")->fetchAll();
$result = [];
foreach ($events as $e) {
    $colors = ['programmée' => '#4f46e5', 'effectuée' => '#22c55e', 'annulée' => '#ef4444'];
    $result[] = [
        'id' => $e['id'],
        'title' => $e['student_nom'] . ' - ' . $e['vehicle_nom'],
        'start' => $e['date_lecon'],
        'end' => date('Y-m-d\TH:i:s', strtotime($e['date_lecon']) + 3600),
        'backgroundColor' => $colors[$e['statut']] ?? '#6b7280',
        'borderColor' => $colors[$e['statut']] ?? '#6b7280',
    ];
}
echo json_encode($result);