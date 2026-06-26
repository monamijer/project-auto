</div><!-- /main-content -->

<script src="<?= BASE_URL ?>/node_modules/jquery/dist/jquery.min.js"></script>
<script src="<?= BASE_URL ?>/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/node_modules/datatables.net/js/dataTables.min.js"></script>
<script src="<?= BASE_URL ?>/node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>

<?php if (!empty($dataTableId)): ?>
<script>
$(document).ready(function () {
    $('#<?= htmlspecialchars($dataTableId) ?>').DataTable({<?= $dataTableOpts ?? '' ?>});
});
</script>
<?php endif; ?>

<?php if (!empty($extraScript)): ?>
<script><?= $extraScript ?></script>
<?php endif; ?>

<script>
/* Tri universel */
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('th.sortable').forEach(th => {
        th.style.cursor = 'pointer';
        th.style.userSelect = 'none';
        th.addEventListener('click', function() {
            const table = this.closest('table');
            const tbody = table.querySelector('tbody');
            if (!tbody) return;
            const rows = Array.from(tbody.querySelectorAll('tr')).filter(r => r.cells.length > 1);
            const colIndex = Array.from(this.parentNode.children).indexOf(this);
            const isAsc = this.classList.contains('asc');
            table.querySelectorAll('th.sortable').forEach(t => { t.classList.remove('asc','desc'); const i = t.querySelector('.sort-icon'); if(i) i.remove(); });
            rows.sort((a, b) => {
                let va = (a.cells[colIndex]?.textContent || '').trim();
                let vb = (b.cells[colIndex]?.textContent || '').trim();
                return isAsc ? vb.localeCompare(va, 'fr', {numeric: true}) : va.localeCompare(vb, 'fr', {numeric: true});
            });
            const icon = document.createElement('i');
            icon.className = 'bi bi-sort-' + (isAsc ? 'down' : 'up') + ' sort-icon ms-1 small text-muted';
            th.appendChild(icon);
            th.classList.add(isAsc ? 'desc' : 'asc');
            rows.forEach(r => tbody.appendChild(r));
        });
    });
});
</script>

<script>
/* Dark mode */
(function() {
    const saved = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-bs-theme', saved);
})();
function toggleTheme() {
    const current = document.documentElement.getAttribute('data-bs-theme') || 'light';
    const next = current === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-bs-theme', next);
    localStorage.setItem('theme', next);
}
</script>
</body>
</html>