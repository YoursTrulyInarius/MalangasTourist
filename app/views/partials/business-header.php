<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Business Panel - MTMS' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= filemtime(__DIR__ . '/../../../public/assets/css/style.css') ?>">
<?php
    $activePage = $activePage ?? '';
    // Count unread monitoring notifications for this owner:
    // - Admin sent a warning (warning_sent_at > last_monitoring_read_at)
    // - Admin reviewed / approved / declined an improvement submission (monitor_reviewed_at > last_monitoring_read_at)
    $_bizSidebarDb = Database::getInstance()->getConnection();
    $_bizOwnerId   = Auth::user()['id'] ?? 0;
    $bizMonitorNotifCount = 0;
    if ($_bizOwnerId && $activePage !== 'monitoring') {
        require_once __DIR__ . '/../../models/Spot.php';
        Spot::ensureMonitoringSchema($_bizSidebarDb);
        $__bstmt = $_bizSidebarDb->prepare(
            "SELECT COUNT(*) FROM spots s
             JOIN users u ON u.id = s.owner_user_id
             WHERE s.owner_user_id = ?
               AND s.status = 'approved'
               AND (
                   (s.monitor_status = 'warning' AND COALESCE(s.warning_sent_at, s.monitor_last_triggered_at, '1970-01-01') > COALESCE(u.last_monitoring_read_at, '1970-01-01'))
                   OR
                   (s.monitor_submission_status IN ('declined', 'approved') AND s.monitor_reviewed_at IS NOT NULL AND s.monitor_reviewed_at > COALESCE(u.last_monitoring_read_at, '1970-01-01'))
               )"
        );
        $__bstmt->execute([$_bizOwnerId]);
        $bizMonitorNotifCount = (int)$__bstmt->fetchColumn();
    }
?>
    <style>
        /* ===== BUSINESS PANEL LAYOUT (mirrors LGU layout) ===== */
        body { background-color: #f0f2f5; }

        .biz-layout {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* ===== SIDEBAR ===== */
        .biz-sidebar {
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
        .biz-main {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-width: 0;
        }

        /* Top bar inside content */
        .biz-topbar {
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

        .biz-topbar-title { font-size: 1.1rem; font-weight: 700; color: var(--primary-dark); }
        .biz-topbar-breadcrumb { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.1rem; }

        .biz-page-body { padding: 2rem; flex: 1; }

        .biz-main,
        .biz-page-body,
        .biz-main > * { min-width: 0; }

        .biz-main { overflow-wrap: anywhere; }

        .biz-main img,
        .biz-main input,
        .biz-main select,
        .biz-main textarea { max-width: 100%; }

        .biz-main #map-picker { box-sizing: border-box; }

        .biz-main input,
        .biz-main select,
        .biz-main textarea { box-sizing: border-box; }

        .biz-main .table-responsive {
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .biz-main .table-responsive > table { min-width: 700px; }
        .biz-main .admin-sidebar-backdrop {
            padding: 0;
            border: 0;
            appearance: none;
            cursor: pointer;
            touch-action: none;
        }

        /* ===== RESPONSIVE — MOBILE ===== */
        @media (max-width: 900px) {
            .biz-sidebar {
                left: 0;
                width: min(86vw, 320px);
                min-width: 0;
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                z-index: 1202;
            }
            body.sidebar-open .biz-sidebar {
                transform: translateX(0);
                box-shadow: 4px 0 20px rgba(0,0,0,0.25);
            }
            body.sidebar-open { overflow: hidden; }
            .biz-main {
                margin-left: 0;
                width: 100%;
            }
            .admin-sidebar-toggle {
                display: inline-flex !important;
                flex: 0 0 auto;
            }
            .biz-page-body {
                padding: 1.25rem;
            }
            .biz-topbar > div {
                min-width: 0;
                flex: 1 1 0;
            }
            .biz-topbar .biz-topbar-breadcrumb { overflow-wrap: anywhere; }
        }

        @media (max-width: 600px) {
            .biz-topbar {
                padding: 0.85rem 1rem;
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            .biz-topbar > div { flex-basis: calc(100% - 3rem); }
            .biz-topbar > a,
            .biz-topbar > .btn {
                max-width: 100%;
                white-space: normal;
                overflow-wrap: anywhere;
            }
            .biz-page-body {
                padding: 1rem;
            }
            .biz-main .biz-form-content { padding: 1rem !important; }
            .biz-main .biz-form-panel-header { padding: 1rem !important; }
            .biz-main .biz-form-grid,
            .biz-main .biz-edit-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
            .biz-main .biz-form-grid > *,
            .biz-main .biz-edit-grid > * { min-width: 0; }
            .biz-main .biz-form-panel { width: 100%; }
            .biz-main .biz-form-actions { flex-direction: column; }
            .biz-main .biz-form-actions > * {
                width: 100%;
                justify-content: center;
            }
            .biz-main .table-responsive > table th,
            .biz-main .table-responsive > table td {
                white-space: normal;
                overflow-wrap: anywhere;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .biz-sidebar { transition: none; }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var topbar = document.querySelector('.biz-topbar');
            var body = document.body;

            // Inject hamburger toggle button into topbar
            if (topbar) {
                var toggleBtn = document.createElement('button');
                toggleBtn.type = 'button';
                toggleBtn.className = 'admin-sidebar-toggle';
                toggleBtn.innerHTML = '<i class="fa-solid fa-bars" id="biz-toggle-icon"></i>';
                toggleBtn.setAttribute('aria-label', 'Open navigation menu');
                toggleBtn.setAttribute('aria-controls', 'biz-sidebar');
                toggleBtn.setAttribute('aria-expanded', 'false');
                topbar.insertBefore(toggleBtn, topbar.firstChild);

                var sidebar = document.querySelector('.biz-sidebar');
                var backdrop;
                var originalBodyOverflow = body.style.overflow;
                function setSidebarOpen(isOpen) {
                    body.classList.toggle('sidebar-open', isOpen);
                    toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    toggleBtn.setAttribute('aria-label', isOpen ? 'Close navigation menu' : 'Open navigation menu');
                    var icon = document.getElementById('biz-toggle-icon');
                    if (icon) icon.className = isOpen ? 'fa-solid fa-xmark' : 'fa-solid fa-bars';
                    body.style.overflow = isOpen ? 'hidden' : originalBodyOverflow;
                    if (backdrop) backdrop.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
                    if (sidebar) {
                        var sidebarHidden = !isOpen && window.innerWidth <= 900;
                        sidebar.setAttribute('aria-hidden', sidebarHidden ? 'true' : 'false');
                        sidebar.inert = sidebarHidden;
                    }
                    if (isOpen && sidebar) {
                        var firstLink = sidebar.querySelector('a');
                        if (firstLink) firstLink.focus();
                    } else if (!isOpen && window.innerWidth <= 900 && document.activeElement !== toggleBtn) {
                        toggleBtn.focus();
                    }
                }

                toggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    setSidebarOpen(!body.classList.contains('sidebar-open'));
                });

                var layout = document.querySelector('.biz-layout');
                if (layout) {
                    backdrop = document.createElement('button');
                    backdrop.type = 'button';
                    backdrop.className = 'admin-sidebar-backdrop';
                    backdrop.setAttribute('aria-label', 'Close navigation menu');
                    backdrop.setAttribute('aria-hidden', 'true');
                    layout.appendChild(backdrop);
                }
                if (backdrop) {
                    backdrop.addEventListener('click', function() {
                        setSidebarOpen(false);
                    });
                }

                if (sidebar) {
                    sidebar.id = 'biz-sidebar';
                    sidebar.setAttribute('aria-label', 'Business panel navigation');
                    var sidebarHidden = window.innerWidth <= 900;
                    sidebar.setAttribute('aria-hidden', sidebarHidden ? 'true' : 'false');
                    sidebar.inert = sidebarHidden;
                    sidebar.querySelectorAll('a').forEach(function(link) {
                        link.addEventListener('click', function() {
                            if (window.innerWidth <= 900) setSidebarOpen(false);
                        });
                    });
                }

                window.addEventListener('resize', function() {
                    if (window.innerWidth > 900 && body.classList.contains('sidebar-open')) {
                        setSidebarOpen(false);
                    } else if (sidebar) {
                        var sidebarHidden = window.innerWidth <= 900 && !body.classList.contains('sidebar-open');
                        sidebar.setAttribute('aria-hidden', sidebarHidden ? 'true' : 'false');
                        sidebar.inert = sidebarHidden;
                    }
                });

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && body.classList.contains('sidebar-open')) {
                        setSidebarOpen(false);
                    }
                });
            }

            // Auto-wrap all tables in scrollable containers
            document.querySelectorAll('table').forEach(function(table) {
                if (!table.parentElement.classList.contains('table-responsive')) {
                    var wrapper = document.createElement('div');
                    wrapper.className = 'table-responsive';
                    table.parentNode.insertBefore(wrapper, table);
                    wrapper.appendChild(table);
                }
            });
        });
    </script>
</head>
<body>
<div class="biz-layout">

    <!-- Sidebar -->
    <aside class="biz-sidebar">
        <!-- Brand -->
        <a href="<?= BASE_URL ?>/" class="sidebar-brand">
            <?php if (file_exists(__DIR__ . '/../../../public/assets/images/logo.png')): ?>
                <img src="<?= BASE_URL ?>/assets/images/logo.png" alt="Logo" class="sidebar-brand-logo">
            <?php else: ?>
                <div class="sidebar-brand-logo-placeholder"><i class="fa-solid fa-earth-asia"></i></div>
            <?php endif; ?>
            <div class="sidebar-brand-text">
                <span class="sidebar-brand-title">Malangas Tourism</span>
                <span class="sidebar-brand-sub">Business Panel</span>
            </div>
        </a>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <div class="sidebar-section-label">Main</div>
            <a href="<?= BASE_URL ?>/business" class="sidebar-link <?= $activePage === 'dashboard' ? 'active' : '' ?>">
                <i class="fa-solid fa-gauge sidebar-icon"></i> Dashboard
            </a>

            <div class="sidebar-section-label">Accreditation</div>
            <a href="<?= BASE_URL ?>/business/apply" class="sidebar-link <?= $activePage === 'apply' ? 'active' : '' ?>">
                <i class="fa-solid fa-file-signature sidebar-icon"></i> Apply for Accreditation
            </a>
            <a href="<?= BASE_URL ?>/business/businesses" class="sidebar-link <?= $activePage === 'businesses' ? 'active' : '' ?>">
                <i class="fa-solid fa-store sidebar-icon"></i> View Businesses
            </a>
            <a href="<?= BASE_URL ?>/business/monitoring" class="sidebar-link <?= $activePage === 'monitoring' ? 'active' : '' ?>">
                <i class="fa-solid fa-clipboard-check sidebar-icon"></i> Monitoring
                <?php if ($bizMonitorNotifCount > 0): ?>
                    <span class="sidebar-badge"><?= $bizMonitorNotifCount ?></span>
                <?php endif; ?>
            </a>
        </nav>

        <!-- Footer / User -->
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar"><i class="fa-solid fa-briefcase"></i></div>
                <div class="sidebar-user-info">
                    <span class="sidebar-user-name"><?= htmlspecialchars(Auth::user()['name']) ?></span>
                    <span class="sidebar-user-role"><?= htmlspecialchars(((Auth::user()['applicant_type'] ?? 'private') === 'public') ? 'Public Sector Applicant' : 'Private Sector Applicant') ?></span>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/logout" class="sidebar-logout">
                <i class="fa-solid fa-right-from-bracket"></i> Sign Out
            </a>
        </div>
    </aside>

    <!-- Main content wrapper -->
    <div class="biz-main">
