<?php
/**
 * pages/calendar.php — Calendrier des leçons
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

$pageTitle = 'Calendrier — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<style>
#calendar { max-width: 100%; margin: 0 auto; }
.fc-event { cursor: pointer; border-radius: 4px; font-size: 0.85rem; }
.fc-toolbar-title { font-size: 1.2rem !important; }
@media (max-width: 767.98px) { .fc-toolbar { flex-direction: column; gap: 0.5rem; } }
</style>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div><h1 class="h4 mb-0"><i class="bi bi-calendar-week me-2 text-primary"></i>Calendrier des leçons</h1></div>
    <?php if (hasPermission('crud_lecons')): ?>
    <a href="<?= BASE_URL ?>/pages/lessons.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Planifier</a>
    <?php endif; ?>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body"><div id="calendar"></div></div>
</div>

<script src="<?= BASE_URL ?>/node_modules/fullcalendar/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'timeGridWeek',
        locale: 'fr',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
        events: '<?= BASE_URL ?>/pages/actions/calendar_events.php',
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
        eventClick: function(info) {
            window.location.href = '<?= BASE_URL ?>/pages/lessons.php';
        }
    });
    calendar.render();
});
</script>

<?php include BASE_PATH . '/includes/footer.php'; ?>