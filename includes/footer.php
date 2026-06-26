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
/* ── Dark mode persistant ── */
(function(){
    var saved = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-bs-theme', saved);
})();

function toggleTheme() {
    var html = document.documentElement;
    var current = html.getAttribute('data-bs-theme') || 'light';
    var next = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-bs-theme', next);
    localStorage.setItem('theme', next);
}
</script>

</body>
</html>