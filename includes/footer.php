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

</body>
</html>