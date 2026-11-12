<?php
$title = 'Applications - LGU Admin';
$activePage = 'applications';
require_once '../app/views/partials/lgu-header.php';

$filterCategory = trim($_GET['category'] ?? '');
$filterStatus   = trim($_GET['status'] ?? '');

$statusOptions = [
    'pending'        => ['label' => 'Pending',         'color' => '#f59e0b'],
    'approved'       => ['label' => 'Approved',        'color' => '#10b981'],
    'rejected'       => ['label' => 'Rejected',        'color' => '#ef4444'],
    'needs_revision' => ['label' => 'Needs Revision',  'color' => '#3b82f6'],
    'photo_update'   => ['label' => 'Photo Update',    'color' => '#b45309'],
];
?>

<style>
/* ── Filter Bar ──────────────────────────────────────────────────── */
.app-filter-bar {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
    background: #fff;
    border-radius: 0.85rem;
    box-shadow: 0 1px 4px rgba(0,0,0,0.07);
    padding: 1rem 1.25rem;
    margin-bottom: 1.1rem;
}
.app-filter-bar label {
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-muted);
    white-space: nowrap;
}
.app-filter-select {
    appearance: none;
    -webkit-appearance: none;
    background: #f8f9fb url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") no-repeat right 0.75rem center;
    border: 1.5px solid #e5e7eb;
    border-radius: 0.55rem;
    padding: 0.45rem 2.2rem 0.45rem 0.9rem;
    font-size: 0.86rem;
    font-weight: 500;
    color: #374151;
    cursor: pointer;
    transition: border-color 0.15s, box-shadow 0.15s;
    min-width: 170px;
}
.app-filter-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(var(--primary-rgb, 22,101,52), 0.12);
}
.app-filter-select.active-filter {
    border-color: var(--primary);
    background-color: #f0fdf4;
    color: var(--primary-dark);
    font-weight: 600;
}
.app-filter-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.45rem 1.1rem;
    border-radius: 0.55rem;
    font-size: 0.84rem;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: background 0.15s, transform 0.1s;
}
.app-filter-btn-apply {
    background: var(--primary);
    color: #fff;
}
.app-filter-btn-apply:hover { background: var(--primary-dark); }
.app-filter-btn-clear {
    background: #f3f4f6;
    color: #6b7280;
    text-decoration: none;
}
.app-filter-btn-clear:hover { background: #e5e7eb; color: #374151; }

/* active-filter pills */
.app-filter-active-pills {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-bottom: 0.6rem;
}
.app-filter-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: #f0fdf4;
    border: 1.5px solid var(--primary);
    color: var(--primary-dark);
    border-radius: 999px;
    padding: 0.2rem 0.7rem;
    font-size: 0.78rem;
    font-weight: 600;
}
.app-filter-pill a {
    color: inherit;
    text-decoration: none;
    font-size: 0.72rem;
    opacity: 0.7;
    margin-left: 0.15rem;
}
.app-filter-pill a:hover { opacity: 1; }
.app-result-count {
    font-size: 0.82rem;
    color: var(--text-muted);
    font-weight: 500;
    margin-left: auto;
}
</style>

<!-- Top Bar -->
<div class="lgu-topbar">
    <div>
        <div class="lgu-topbar-title">Applications Inbox</div>
        <div class="lgu-topbar-breadcrumb">Business listing applications awaiting review</div>
    </div>
</div>

<div class="lgu-page-body">

    <!-- ── Filter Bar ──────────────────────────────────────────────── -->
    <form method="GET" action="<?= BASE_URL ?>/lgu/applications" id="applicationsFilterForm">
        <div class="app-filter-bar">
            <label><i class="fa-solid fa-filter" style="margin-right:0.3rem;"></i>Filter</label>

            <!-- Category -->
            <div>
                <select name="category" id="filterCategory"
                        class="app-filter-select <?= $filterCategory ? 'active-filter' : '' ?>"
                        onchange="document.getElementById('applicationsFilterForm').submit()">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"
                            <?= $filterCategory == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Status -->
            <div>
                <select name="status" id="filterStatus"
                        class="app-filter-select <?= $filterStatus ? 'active-filter' : '' ?>"
                        onchange="document.getElementById('applicationsFilterForm').submit()">
                    <option value="">All Statuses</option>
                    <?php foreach ($statusOptions as $val => $opt): ?>
                        <option value="<?= $val ?>"
                            <?= $filterStatus === $val ? 'selected' : '' ?>>
                            <?= $opt['label'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ($filterCategory || $filterStatus): ?>
                <a href="<?= BASE_URL ?>/lgu/applications" class="app-filter-btn app-filter-btn-clear">
                    <i class="fa-solid fa-xmark"></i> Clear
                </a>
            <?php endif; ?>

            <span class="app-result-count">
                <?= count($applications) ?> <?= count($applications) === 1 ? 'result' : 'results' ?>
            </span>
        </div>
    </form>

    <!-- Active filter pills -->
    <?php if ($filterCategory || $filterStatus): ?>
        <div class="app-filter-active-pills">
            <?php
            // Category pill
            if ($filterCategory) {
                $catName = '';
                foreach ($categories as $c) {
                    if ($c['id'] == $filterCategory) { $catName = $c['name']; break; }
                }
                $clearCatUrl = BASE_URL . '/lgu/applications?' . http_build_query(['status' => $filterStatus]);
            ?>
            <span class="app-filter-pill">
                <i class="fa-solid fa-tag"></i>
                <?= htmlspecialchars($catName) ?>
                <a href="<?= $clearCatUrl ?>">✕</a>
            </span>
            <?php } ?>

            <?php
            // Status pill
            if ($filterStatus) {
                $clearStatusUrl = BASE_URL . '/lgu/applications?' . http_build_query(['category' => $filterCategory]);
                $statusLabel = $statusOptions[$filterStatus]['label'] ?? ucfirst($filterStatus);
            ?>
            <span class="app-filter-pill">
                <i class="fa-solid fa-circle-dot"></i>
                <?= htmlspecialchars($statusLabel) ?>
                <a href="<?= $clearStatusUrl ?>">✕</a>
            </span>
            <?php } ?>
        </div>
    <?php endif; ?>

    <!-- ── Applications Table ──────────────────────────────────────── -->
    <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 4px rgba(0,0,0,0.07); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: #f8f9fb; border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 1rem 1.25rem; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 700;">Business Name</th>
                    <th style="padding: 1rem 1.25rem; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 700;">Owner</th>
                    <th style="padding: 1rem 1.25rem; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 700;">Category</th>
                    <th style="padding: 1rem 1.25rem; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 700;">Submitted</th>
                    <th style="padding: 1rem 1.25rem; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 700;">Status</th>
                    <th style="padding: 1rem 1.25rem; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 700;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($applications)): ?>
                    <tr>
                        <td colspan="6" style="padding: 3.5rem; text-align: center; color: var(--text-muted);">
                            <i class="fa-solid fa-inbox" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                            <?= ($filterCategory || $filterStatus) ? 'No applications match the selected filters.' : 'No applications yet.' ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($applications as $app): ?>
                        <?php
                            $statusColors = [
                                'pending'        => ['bg' => '#fef3c7', 'text' => '#92400e'],
                                'approved'       => ['bg' => '#dcfce7', 'text' => '#166534'],
                                'rejected'       => ['bg' => '#fee2e2', 'text' => '#991b1b'],
                                'needs_revision' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
                            ];
                            $sc = $statusColors[$app['status']] ?? ['bg' => '#f3f4f6', 'text' => '#374151'];
                        ?>
                        <tr style="border-bottom: 1px solid #f3f4f6; transition: background 0.15s;" onmouseover="this.style.background='#f8f9fb'" onmouseout="this.style.background='white'">
                            <td style="padding: 1rem 1.25rem; font-weight: 600; color: var(--primary-dark);">
                                <?= htmlspecialchars($app['name']) ?>
                                <?php if (!empty($app['pending_photo_update'])): ?>
                                    <br><span style="display:inline-block;margin-top:0.35rem;background:#fef3c7;color:#92400e;padding:0.2rem 0.55rem;border-radius:999px;font-size:0.72rem;font-weight:700;"><i class="fa-regular fa-images"></i> Photo Update</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem 1.25rem; color: #4b5563;"><?= htmlspecialchars($app['business_name']) ?></td>
                            <td style="padding: 1rem 1.25rem;">
                                <span style="background: #f0fdf4; color: var(--primary-dark); padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.78rem; font-weight: 600; white-space: nowrap;">
                                    <?= htmlspecialchars($app['category_name']) ?>
                                </span>
                            </td>
                            <td style="padding: 1rem 1.25rem; color: #4b5563;"><?= date('M d, Y', strtotime($app['created_at'])) ?></td>
                            <td style="padding: 1rem 1.25rem;">
                                <span style="background: <?= $sc['bg'] ?>; color: <?= $sc['text'] ?>; padding: 0.25rem 0.65rem; border-radius: 999px; font-size: 0.78rem; font-weight: 600; text-transform: capitalize;">
                                    <?= htmlspecialchars(str_replace('_', ' ', $app['status'])) ?>
                                </span>
                            </td>
                            <td style="padding: 1rem 1.25rem;">
                                <a href="<?= BASE_URL ?>/lgu/applications/<?= $app['id'] ?>" class="btn btn-primary" style="padding: 0.35rem 0.85rem; font-size: 0.82rem;">
                                    <i class="fa-solid fa-eye"></i> Review
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../app/views/partials/lgu-footer.php'; ?>
