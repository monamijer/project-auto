</div><!-- /main-content -->

<script src="<?= BASE_URL ?>/node_modules/jquery/dist/jquery.min.js"></script>
<script src="<?= BASE_URL ?>/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/node_modules/datatables.net/js/dataTables.min.js"></script>
<script src="<?= BASE_URL ?>/node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>

<?php if (!empty($dataTableId)): ?>
<script>
$(document).ready(function () {
    $('#<?= htmlspecialchars($dataTableId) ?>').DataTable({
        <?= $dataTableOpts ?? '' ?>
    });
});
</script>
<?php endif; ?>

<?php if (!empty($extraScript)): ?>
<script><?= $extraScript ?></script>
<?php endif; ?>

<script>
/* ── Dark mode : persistance via localStorage ── */
function toggleTheme() {
    var current = document.documentElement.getAttribute('data-theme') || 'light';
    var next = current === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
}
</script>

</body>
</html>