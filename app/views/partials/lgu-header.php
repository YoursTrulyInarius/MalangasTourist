<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'LGU Admin - MTMS' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= filemtime(__DIR__ . '/../../../public/assets/css/style.css') ?>">
<?php
    // Always fetch pending count for the sidebar badge
    $_sidebarDb = Database::getInstance()->getConnection();
    require_once __DIR__ . '/../../models/Review.php';
    require_once __DIR__ . '/../../models/Spot.php';
    Review::ensureModerationSchema($_sidebarDb);
    Spot::ensureMonitoringSchema($_sidebarDb);
    Spot::ensurePhotoUpdateSchema($_sidebarDb);
    $pendingCount = $_sidebarDb->query("SELECT COUNT(*) FROM spots WHERE status = 'pending'")->fetchColumn();
    $pendingPhotoUpdateCount = $_sidebarDb->query("SELECT COUNT(*) FROM spot_photo_updates WHERE status = 'pending'")->fetchColumn();
    $pendingReviewCount = $_sidebarDb->query("SELECT COUNT(*) FROM reviews WHERE status = 'pending'")->fetchColumn();

    $activePage = $activePage ?? '';
    $_adminId = Auth::user()['id'] ?? 0;
    $monitorNotificationCount = 0;
    if ($_adminId && $activePage !== 'monitoring') {
        $_mstmt = $_sidebarDb->prepare(
            "SELECT COUNT(*) FROM spots s
             LEFT JOIN users u ON u.id = ?
             WHERE s.status = 'approved'
               AND s.monitor_submission_status = 'pending'
               AND COALESCE(s.monitor_submitted_at, s.warning_sent_at, '2099-01-01') > COALESCE(u.last_monitoring_read_at, '1970-01-01')"
        );
        $_mstmt->execute([$_adminId]);
        $monitorNotificationCount = (int)$_mstmt->fetchColumn();
    }
?>
    <style>
        /* ===== LGU ADMIN LAYOUT ===== */
        body { background-color: #f0f2f5; }

        .lgu-layout {
            display: flex;
            min-height: 100vh;
        }

        /* ===== SIDEBAR ===== */
        .lgu-sidebar {
            width: 260px;
            min-width: 260px;
            background-color: var(--primary-dark);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1200;
            overflow-y: auto;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-decoration: none;
        }

        .sidebar-brand-logo {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: contain;
            background: white;
            padding: 2px;
            border: 2px solid var(--secondary-color);
            flex-shrink: 0;
        }

        .sidebar-brand-logo-placeholder {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
            flex-shrink: 0;
        }

        .sidebar-brand-text { display: flex; flex-direction: column; line-height: 1.3; }
        .sidebar-brand-title { font-size: 0.95rem; font-weight: 700; color: white; }
        .sidebar-brand-sub { font-size: 0.65rem; font-weight: 500; color: var(--secondary-color); text-transform: uppercase; letter-spacing: 0.08em; }

        /* Nav sections */
        .sidebar-section-label {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.35);
            padding: 1.25rem 1.25rem 0.4rem;
        }

        .sidebar-nav { flex: 1; padding: 0.5rem 0.75rem; }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.7rem 0.9rem;
            border-radius: 0.5rem;
            text-decoration: none;
            color: rgba(255,255,255,0.7);
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.15s ease;
            margin-bottom: 0.15rem;
            position: relative;
        }

        .sidebar-link:hover {
            background: rgba(255,255,255,0.08);
            color: white;
        }

        .sidebar-link.active {
            background: var(--secondary-color);
            color: white;
            font-weight: 600;
        }

        .sidebar-link .sidebar-icon {
            width: 20px;
            text-align: center;
            font-size: 0.95rem;
            flex-shrink: 0;
        }

        .sidebar-badge {
            margin-left: auto;
            background: #ef4444;
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.1rem 0.45rem;
            border-radius: 999px;
            min-width: 18px;
            text-align: center;
        }

        /* Sidebar footer / user info */
        .sidebar-footer {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding: 1rem 1.25rem;
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .sidebar-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .sidebar-user-info { display: flex; flex-direction: column; line-height: 1.3; overflow: hidden; }
        .sidebar-user-name { font-size: 0.85rem; font-weight: 600; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-user-role { font-size: 0.7rem; color: var(--secondary-color); text-transform: uppercase; letter-spacing: 0.05em; }

        .sidebar-logout {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 0.9rem;
            border-radius: 0.5rem;
            text-decoration: none;
            color: rgba(255,255,255,0.6);
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.15s ease;
        }

        .sidebar-logout:hover {
            background: rgba(239,68,68,0.15);
            color: #fca5a5;
        }

        /* ===== MAIN CONTENT ===== */
        .lgu-main {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Top bar inside content */
        .lgu-topbar {
            background: white;
            padding: 1rem 2rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1100;
        }

        .lgu-topbar-title { font-size: 1.1rem; font-weight: 700; color: var(--primary-dark); }
        .lgu-topbar-breadcrumb { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.1rem; }

        .lgu-page-body { padding: 2rem; flex: 1; }

        .admin-sidebar-toggle {
            display: none;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 0.5rem;
            border: 1.5px solid #d1d5db;
            background: #fff;
            color: var(--primary-dark);
            cursor: pointer;
            font-size: 1.05rem;
            flex-shrink: 0;
            transition: all 0.15s ease;
        }

        .admin-sidebar-toggle:hover {
            background: #f3f4f6;
            border-color: #9ca3af;
        }

        .admin-sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1150;
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
        }

        body.sidebar-open .admin-sidebar-backdrop {
            display: block;
        }

        body.sidebar-open {
            overflow: hidden;
        }

        /* Stat Cards */
        .lgu-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .lgu-stat-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
            transition: transform 0.15s, box-shadow 0.15s;
        }

        .lgu-stat-label {
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .lgu-stat-value {
            font-size: 2.25rem;
            font-weight: 800;
        }

        /* Tourism Map Card */
        .map-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.15rem 1.5rem;
            border-bottom: 1px solid #f0f2f5;
            background: #fff;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .map-legend {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            flex-wrap: wrap;
        }

        .map-legend > span {
            display: inline-flex;
            align-items: center;
            font-size: 0.78rem;
            font-weight: 600;
            color: #4b5563;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 999px;
            padding: 0.2rem 0.65rem;
        }

        /* Table & Filters General */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }

        .admin-filter-form {
            display: flex;
            align-items: flex-end;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            background: #fff;
            padding: 1.15rem 1.25rem;
            border-radius: 0.75rem;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
        }

        /* ===== RESPONSIVE — TABLET & MOBILE ===== */
        @media (max-width: 900px) {
            .lgu-sidebar {
                left: -260px;
            }
            body.sidebar-open .lgu-sidebar {
                left: 0;
                box-shadow: 4px 0 24px rgba(0,0,0,0.3);
            }
            .lgu-main {
                margin-left: 0;
                width: 100%;
            }
            .admin-sidebar-toggle {
                display: inline-flex !important;
            }
            .lgu-topbar {
                padding: 0.85rem 1.25rem;
                justify-content: flex-start;
                gap: 0.85rem;
            }
            .lgu-topbar > div:first-of-type {
                flex: 1;
                min-width: 0;
            }
            .lgu-topbar-title {
                font-size: 1.05rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .lgu-topbar-breadcrumb {
                font-size: 0.75rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .lgu-page-body {
                padding: 1.25rem;
            }
            #map {
                height: 340px !important;
            }
        }

        @media (max-width: 768px) {
            .table-responsive table {
                min-width: 580px;
            }
            /* Auto unstack 2-column inline grids on mobile */
            [style*="grid-template-columns: 1fr 1fr"],
            [style*="grid-template-columns:1fr 1fr"],
            [style*="grid-template-columns: 1fr 1fr;"],
            [style*="grid-template-columns:1fr 1fr;"],
            [style*="grid-template-columns: repeat(2, 1fr)"],
            [style*="grid-template-columns:repeat(2,1fr)"] {
                grid-template-columns: 1fr !important;
            }
            .lgu-page-body form[style*="padding: 2rem;"],
            .lgu-page-body form[style*="padding:2rem;"] {
                padding: 1.15rem !important;
            }
        }

        @media (max-width: 640px) {
            .admin-filter-form {
                flex-direction: column;
                align-items: stretch;
                gap: 0.85rem;
                padding: 1rem;
            }
            .admin-filter-form > div {
                width: 100% !important;
                min-width: 0 !important;
            }
            .app-filter-bar {
                flex-direction: column;
                align-items: stretch;
                gap: 0.75rem;
                padding: 0.85rem 1rem;
            }
            .app-filter-bar > div,
            .app-filter-select {
                width: 100% !important;
                min-width: 0 !important;
            }
            .app-result-count {
                margin-left: 0;
                text-align: right;
            }
        }

        @media (max-width: 600px) {
            .lgu-topbar {
                padding: 0.75rem 1rem;
                flex-wrap: wrap;
                gap: 0.65rem;
            }
            .lgu-topbar > *:not(.admin-sidebar-toggle):not(:first-of-type) {
                width: 100%;
                display: flex;
                gap: 0.5rem;
                justify-content: flex-start;
            }
            .lgu-page-body {
                padding: 0.85rem;
            }

            /* Compact horizontal stat cards on phone screen */
            .lgu-stats-grid {
                grid-template-columns: 1fr !important;
                gap: 0.65rem !important;
                margin-bottom: 1.25rem !important;
            }
            .lgu-stat-card {
                padding: 0.85rem 1.15rem !important;
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
            }
            .lgu-stat-label {
                margin-bottom: 0 !important;
                font-size: 0.74rem !important;
            }
            .lgu-stat-value {
                font-size: 1.65rem !important;
                line-height: 1 !important;
            }

            /* Compact map header & legend */
            .map-card-header {
                padding: 0.85rem 1rem;
                flex-direction: column;
                align-items: flex-start;
                gap: 0.6rem;
            }
            .map-legend {
                gap: 0.45rem;
            }
            .map-legend > span {
                font-size: 0.72rem;
                padding: 0.18rem 0.55rem;
            }
            #map {
                height: 290px !important;
            }
            .report-card {
                padding: 0.85rem 1rem !important;
            }
            .lgu-page-body > div[style*="justify-content: space-between"],
            .lgu-page-body > div[style*="justify-content:space-between"] {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 0.75rem !important;
                padding: 1.15rem 1.25rem !important;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var topbar = document.querySelector('.lgu-topbar');
            var body = document.body;

            // Inject hamburger toggle button into topbar
            if (topbar) {
                var toggleBtn = document.createElement('button');
                toggleBtn.type = 'button';
                toggleBtn.className = 'admin-sidebar-toggle';
                toggleBtn.innerHTML = '<i class="fa-solid fa-bars" id="lgu-toggle-icon"></i>';
                toggleBtn.setAttribute('aria-label', 'Toggle sidebar');
                topbar.insertBefore(toggleBtn, topbar.firstChild);

                toggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    var isOpen = body.classList.toggle('sidebar-open');
                    var icon = document.getElementById('lgu-toggle-icon');
                    if (icon) {
                        icon.className = isOpen ? 'fa-solid fa-xmark' : 'fa-solid fa-bars';
                    }
                });
            }

            // Inject backdrop overlay
            var layout = document.querySelector('.lgu-layout');
            if (layout) {
                var backdrop = document.createElement('div');
                backdrop.className = 'admin-sidebar-backdrop';
                layout.appendChild(backdrop);
                backdrop.addEventListener('click', function() {
                    body.classList.remove('sidebar-open');
                    var icon = document.getElementById('lgu-toggle-icon');
                    if (icon) icon.className = 'fa-solid fa-bars';
                });
            }

            // Close sidebar when window resized above mobile breakpoint
            window.addEventListener('resize', function() {
                if (window.innerWidth > 900) {
                    body.classList.remove('sidebar-open');
                    var icon = document.getElementById('lgu-toggle-icon');
                    if (icon) icon.className = 'fa-solid fa-bars';
                }
            });

            // Close sidebar on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && body.classList.contains('sidebar-open')) {
                    body.classList.remove('sidebar-open');
                    var icon = document.getElementById('lgu-toggle-icon');
                    if (icon) icon.className = 'fa-solid fa-bars';
                }
            });

            // Auto-wrap all tables in scrollable containers
            document.querySelectorAll('table').forEach(function(table) {
                if (!table.parentElement.classList.contains('table-responsive')) {
                    var wrapper = document.createElement('div');
                    wrapper.className = 'table-responsive';
                    table.parentNode.insertBefore(wrapper, table);
                    wrapper.appendChild(table);
                }
            });

            // Close sidebar on clicking sidebar links on mobile
            document.querySelectorAll('.lgu-sidebar .sidebar-link, .lgu-sidebar .sidebar-brand').forEach(function(el) {
                el.addEventListener('click', function() {
                    if (window.innerWidth <= 900) {
                        body.classList.remove('sidebar-open');
                        var icon = document.getElementById('lgu-toggle-icon');
                        if (icon) icon.className = 'fa-solid fa-bars';
                    }
                });
            });
        });
    </script>
</head>
<body>
<div class="lgu-layout">

    <!-- Sidebar -->
    <aside class="lgu-sidebar">
        <!-- Brand -->
        <a href="<?= BASE_URL ?>/" class="sidebar-brand">
            <?php if (file_exists(__DIR__ . '/../../../public/assets/images/logo.png')): ?>
                <img src="<?= BASE_URL ?>/assets/images/logo.png" alt="Logo" class="sidebar-brand-logo">
            <?php else: ?>
                <div class="sidebar-brand-logo-placeholder"><i class="fa-solid fa-earth-asia"></i></div>
            <?php endif; ?>
            <div class="sidebar-brand-text">
                <span class="sidebar-brand-title">Malangas Tourism</span>
                <span class="sidebar-brand-sub">LGU Admin Panel</span>
            </div>
        </a>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <div class="sidebar-section-label">Main</div>
            <a href="<?= BASE_URL ?>/lgu" class="sidebar-link <?= (isset($activePage) && $activePage === 'dashboard') ? 'active' : '' ?>">
                <i class="fa-solid fa-gauge sidebar-icon"></i> Dashboard
            </a>

            <div class="sidebar-section-label">Content</div>
            <a href="<?= BASE_URL ?>/lgu/spots" class="sidebar-link <?= (isset($activePage) && $activePage === 'spots') ? 'active' : '' ?>">
                <i class="fa-solid fa-map-pin sidebar-icon"></i> Manage
            </a>

            <div class="sidebar-section-label">Business</div>
            <a href="<?= BASE_URL ?>/lgu/applications" class="sidebar-link <?= (isset($activePage) && $activePage === 'applications') ? 'active' : '' ?>">
                <i class="fa-solid fa-inbox sidebar-icon"></i> Applications
                <?php if (!empty($pendingCount) && $pendingCount > 0): ?>
                    <span class="sidebar-badge"><?= $pendingCount ?></span>
                <?php endif; ?>
                <?php if (!empty($pendingPhotoUpdateCount) && $pendingPhotoUpdateCount > 0): ?>
                    <span class="sidebar-badge" style="background:#b45309;" title="Pending photo updates"><?= $pendingPhotoUpdateCount ?> photos</span>
                <?php endif; ?>
            </a>

            <div class="sidebar-section-label">Community</div>
            <a href="<?= BASE_URL ?>/lgu/reviews" class="sidebar-link <?= (isset($activePage) && $activePage === 'reviews') ? 'active' : '' ?>">
                <i class="fa-solid fa-star sidebar-icon"></i> Reviews
                <?php if (!empty($pendingReviewCount) && $pendingReviewCount > 0): ?>
                    <span class="sidebar-badge"><?= $pendingReviewCount ?></span>
                <?php endif; ?>
            </a>

            <div class="sidebar-section-label">Monitoring</div>
            <a href="<?= BASE_URL ?>/lgu/monitoring" class="sidebar-link <?= (isset($activePage) && $activePage === 'monitoring') ? 'active' : '' ?>">
                <i class="fa-solid fa-triangle-exclamation sidebar-icon"></i> Monitoring
                <?php if (!empty($monitorNotificationCount) && (int)$monitorNotificationCount > 0): ?>
                    <span class="sidebar-badge"><?= (int)$monitorNotificationCount ?></span>
                <?php endif; ?>
            </a>

            <div class="sidebar-section-label">Reports</div>
            <a href="<?= BASE_URL ?>/lgu/reports" class="sidebar-link <?= (isset($activePage) && $activePage === 'reports') ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-line sidebar-icon"></i> Reports
            </a>
        </nav>

        <!-- Footer / User -->
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar"><i class="fa-solid fa-user"></i></div>
                <div class="sidebar-user-info">
                    <span class="sidebar-user-name"><?= htmlspecialchars(Auth::user()['name']) ?></span>
                    <span class="sidebar-user-role">LGU Administrator</span>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/logout" class="sidebar-logout">
                <i class="fa-solid fa-right-from-bracket"></i> Sign Out
            </a>
        </div>
    </aside>

    <!-- Main content wrapper -->
    <div class="lgu-main">
