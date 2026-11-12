    </div><!-- /.biz-main -->
</div><!-- /.biz-layout -->

<script>
    const BASE_URL = '<?= BASE_URL ?>';
    const MAP_CENTER_LAT = <?= MAP_CENTER_LAT ?>;
    const MAP_CENTER_LNG = <?= MAP_CENTER_LNG ?>;
    const MAP_DEFAULT_ZOOM = <?= MAP_DEFAULT_ZOOM ?>;
</script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (isset($_SESSION['swal_success'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({ title: 'Success!', text: <?= json_encode($_SESSION['swal_success']) ?>, icon: 'success', confirmButtonColor: '#166534' });
    });
</script>
<?php unset($_SESSION['swal_success']); endif; ?>
<?php if (isset($_SESSION['swal_error'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({ title: 'Error!', text: <?= json_encode($_SESSION['swal_error']) ?>, icon: 'error', confirmButtonColor: '#991b1b' });
    });
</script>
<?php unset($_SESSION['swal_error']); endif; ?>

</body>
</html>
 
