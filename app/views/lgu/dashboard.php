<?php
$title = 'Dashboard - LGU Admin';
$activePage = 'dashboard';
require_once '../app/views/partials/lgu-header.php';
?>

<!-- Top Bar -->
<div class="lgu-topbar">
    <div>
        <div class="lgu-topbar-title">Dashboard</div>
        <div class="lgu-topbar-breadcrumb">Overview of tourism activity</div>
    </div>
</div>

<div class="lgu-page-body">

    <!-- Stat Cards -->
    <div class="lgu-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        <div class="lgu-stat-card" style="background: white; border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 1px 4px rgba(0,0,0,0.07); border-left: 4px solid var(--primary-color);">
            <div class="lgu-stat-label" style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-muted); margin-bottom: 0.5rem;">Total Spots (Live)</div>
            <div class="lgu-stat-value" style="font-size: 2.25rem; font-weight: 800; color: var(--primary-dark);"><?= $totalApproved ?? 0 ?></div>
        </div>

        <a href="<?= BASE_URL ?>/lgu/applications" style="text-decoration: none;">
            <div class="lgu-stat-card" style="background: white; border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 1px 4px rgba(0,0,0,0.07); border-left: 4px solid var(--secondary-color); transition: box-shadow 0.2s;">
                <div class="lgu-stat-label" style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-muted); margin-bottom: 0.5rem;">Pending Applications</div>
                <div class="lgu-stat-value" style="font-size: 2.25rem; font-weight: 800; color: var(--secondary-color);"><?= $totalPending ?? 0 ?></div>
            </div>
        </a>

        <div class="lgu-stat-card" style="background: white; border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 1px 4px rgba(0,0,0,0.07); border-left: 4px solid var(--complementary-color);">
            <div class="lgu-stat-label" style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-muted); margin-bottom: 0.5rem;">Total Spots Listed</div>
            <div class="lgu-stat-value" style="font-size: 2.25rem; font-weight: 800; color: var(--complementary-color);"><?= ($totalApproved ?? 0) + ($totalPending ?? 0) ?></div>
        </div>
    </div>

    <!-- Tourism Map -->
    <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 4px rgba(0,0,0,0.07); overflow: hidden;">
        <div class="map-card-header">
            <div>
                <div style="font-weight: 700; font-size: 1rem; color: var(--primary-dark);">Tourism Map</div>
                <div style="font-size: 0.8rem; color: var(--text-muted);">All registered spots by status</div>
            </div>
            <div class="map-legend">
                <span><span style="display:inline-block;width:10px;height:10px;background:#22c55e;border-radius:50%;margin-right:4px;"></span>Approved</span>
                <span><span style="display:inline-block;width:10px;height:10px;background:var(--secondary-color);border-radius:50%;margin-right:4px;"></span>Pending</span>
                <span><span style="display:inline-block;width:10px;height:10px;background:var(--complementary-color);border-radius:50%;margin-right:4px;"></span>Official</span>
            </div>
        </div>
        <div id="map" style="height: 480px; width: 100%;"></div>
    </div>

</div><!-- /.lgu-page-body -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var map = L.map('map').setView([MAP_CENTER_LAT, MAP_CENTER_LNG], MAP_DEFAULT_ZOOM);
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri'
        }).addTo(map);

        L.tileLayer('https://services.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri'
        }).addTo(map);

        fetch(`${BASE_URL}/api/lgu/get-all-spots`)
            .then(res => res.json())
            .then(data => {
                if(data.features) {
                    L.geoJSON(data, {
                        pointToLayer: function(feature, latlng) {
                            let color = '#2172b8';
                            if (feature.properties.status === 'approved' && feature.properties.source === 'business') color = '#22c55e';
                            if (feature.properties.status === 'pending') color = '#e89e1b';
                            if (feature.properties.status === 'rejected') color = '#ef4444';

                            const icon = L.divIcon({
                                className: 'custom-pin',
                                html: `<span style="background:${color};width:14px;height:14px;display:block;border-radius:50%;border:2px solid white;box-shadow:0 1px 4px rgba(0,0,0,0.3);"></span>`
                            });
                            return L.marker(latlng, {icon});
                        },
                        onEachFeature: function(feature, layer) {
                            layer.bindPopup(`<strong>${feature.properties.name}</strong><br>Status: ${feature.properties.status}`);
                        }
                    }).addTo(map);
                }
            });
    });
</script>

<?php require_once '../app/views/partials/lgu-footer.php'; ?>
