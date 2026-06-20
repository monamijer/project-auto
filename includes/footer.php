</div><!-- /main-content -->

<script src="<?= BASE_URL ?>/node_modules/jquery/dist/jquery.min.js"></script>
<script src="<?= BASE_URL ?>/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/node_modules/datatables.net/js/dataTables.min.js"></script>
<script src="<?= BASE_URL ?>/node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>

<?php if (!empty($dataTableId)): ?>
<script>
$(document).ready(function () {
    $('#<?= htmlspecialchars($dataTableId) ?>').DataTable({
        responsive: true,
        language: {
            search: "Rechercher :", lengthMenu: "Afficher _MENU_ lignes",
            info: "_START_ à _END_ sur _TOTAL_", paginate: { previous: "Préc.", next: "Suiv." },
            zeroRecords: "Aucun résultat trouvé"
        },
        <?= $dataTableOpts ?? '' ?>
    });
});
</script>
<?php endif; ?>

<?php if (!empty($extraScript)): ?>
<script><?= $extraScript ?></script>
<?php endif; ?>

<script>
    // ── Déconnexion automatique par inactivité (avertissement à 18 min) ──
    (function () {
        const WARN_AFTER_MS = 18 * 60 * 1000;
        let warned = false;
        setTimeout(function () {
            if (!warned) {
                warned = true;
                if (confirm("Vous êtes inactif depuis longtemps. Cliquez OK pour rester connecté.")) {
                    location.reload();
                }
            }
        }, WARN_AFTER_MS);
    })();

    // Ferme la sidebar mobile après clic sur un lien
    document.querySelectorAll('#appSidebar .nav-link').forEach(function (link) {
        link.addEventListener('click', function () {
            document.getElementById('appSidebar').classList.remove('show');
            document.getElementById('sidebarBackdrop')?.classList.remove('show');
        });
    });
</script>

</body>
</html>
