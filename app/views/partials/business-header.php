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

        /* ===== RESPONSIVE — MOBILE ===== */
        @media (max-width: 900px) {
            .biz-sidebar {
                left: -260px;
            }
            body.sidebar-open .biz-sidebar {
                left: 0;
                box-shadow: 4px 0 20px rgba(0,0,0,0.25);
            }
            .biz-main {
                margin-left: 0;
                width: 100%;
            }
            .admin-sidebar-toggle {
                display: inline-flex !important;
            }
            .biz-page-body {
                padding: 1.25rem;
            }
        }

        @media (max-width: 600px) {
            .biz-topbar {
                padding: 0.85rem 1rem;
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            .biz-page-body {
                padding: 1rem;
            }
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
                toggleBtn.setAttribute('aria-label', 'Toggle sidebar');
                topbar.insertBefore(toggleBtn, topbar.firstChild);

                toggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    var isOpen = body.classList.toggle('sidebar-open');
                    var icon = document.getElementById('biz-toggle-icon');
                    if (icon) {
                        icon.className = isOpen ? 'fa-solid fa-xmark' : 'fa-solid fa-bars';
                    }
                });
            }

            // Inject backdrop overlay
            var layout = document.querySelector('.biz-layout');
            if (layout) {
                var backdrop = document.createElement('div');
                backdrop.className = 'admin-sidebar-backdrop';
                layout.appendChild(backdrop);
                backdrop.addEventListener('click', function() {
                    body.classList.remove('sidebar-open');
                    var icon = document.getElementById('biz-toggle-icon');
                    if (icon) icon.className = 'fa-solid fa-bars';
                });
            }

            // Close sidebar when window resized above mobile breakpoint
            window.addEventListener('resize', function() {
                if (window.innerWidth > 900) {
                    body.classList.remove('sidebar-open');
                    var icon = document.getElementById('biz-toggle-icon');
                    if (icon) icon.className = 'fa-solid fa-bars';
                }
            });

            // Close sidebar on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && body.classList.contains('sidebar-open')) {
                    body.classList.remove('sidebar-open');
                    var icon = document.getElementById('biz-toggle-icon');
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
