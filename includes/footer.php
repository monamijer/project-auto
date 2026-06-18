        </main><!-- /#main -->
    </div><!-- /.row -->
</div><!-- /.container-fluid -->

<!--
    jQuery — requis par DataTables, installé localement via npm.
    Chemin : node_modules/jquery/dist/jquery.min.js
-->
<script src="<?= BASE_URL ?>/node_modules/jquery/dist/jquery.min.js"></script>

<!-- Bootstrap JS bundle (inclut Popper.js) — installé localement -->
<script src="<?= BASE_URL ?>/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables core — installé localement -->
<script src="<?= BASE_URL ?>/node_modules/datatables.net/js/dataTables.min.js"></script>

<!-- DataTables intégration Bootstrap 5 — installé localement -->
<script src="<?= BASE_URL ?>/node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>

<?php if (!empty($dataTableId)): ?>
<script>
    /**
     * Initialisation de DataTables sur la table #<?= $dataTableId ?>
     * Options : <?= $dataTableOpts ?? 'aucune option supplémentaire' ?>
     */
    $(document).ready(function () {
        $('#<?= htmlspecialchars($dataTableId) ?>').DataTable({
            <?= $dataTableOpts ?? '' ?>
        });
    });
</script>
<?php endif; ?>

<?php if (!empty($extraScript)): ?>
<script>
    /* Script spécifique à cette page */
    <?= $extraScript ?>
</script>
<?php endif; ?>

</body>
</html>
