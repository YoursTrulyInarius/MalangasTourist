<?php
$title = 'Dashboard - Business Panel';
$activePage = 'dashboard';
$spots = $spots ?? [];
require_once '../app/views/partials/business-header.php';
?>

<!-- Top Bar -->
<div class="biz-topbar">
    <div>
        <div class="biz-topbar-title">Dashboard</div>
        <div class="biz-topbar-breadcrumb">Overview of your listings and activity</div>
    </div>
    <a href="<?= BASE_URL ?>/business/apply" class="btn btn-primary">
        <i class="fa-solid fa-file-signature"></i> Apply for Listing
    </a>
</div>

<style>
    .business-dashboard {
        display: grid;
        gap: 1.5rem;
    }

    .dashboard-section-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 0.9rem;
    }

    .dashboard-section-heading h2 {
        margin: 0;
        color: var(--primary-dark);
        font-size: 1.1rem;
        font-weight: 800;
    }

    .dashboard-section-heading p {
        margin: 0.25rem 0 0;
        color: var(--text-muted);
        font-size: 0.84rem;
    }

    .dashboard-sector-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.45rem 0.75rem;
        border: 1px solid #d6eee4;
        border-radius: 999px;
        background: #f0faf5;
        color: #16735d;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.03em;
        white-space: nowrap;
    }

    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
    }

    .dashboard-stat {
        --stat-color: #0f766e;
        --stat-tint: #eaf7f2;
        position: relative;
        min-width: 0;
        padding: 1.15rem 1.2rem;
        overflow: hidden;
        background: #fff;
        border: 1px solid #e8eeeb;
        border-radius: 16px;
        box-shadow: 0 5px 18px rgba(15, 23, 42, 0.035);
    }

    .dashboard-stat::after {
        position: absolute;
        top: -25px;
        right: -24px;
        width: 92px;
        height: 92px;
        border-radius: 50%;
        background: var(--stat-tint);
        content: "";
        opacity: 0.75;
    }

    .dashboard-stat.stat-approved { --stat-color: #13805f; --stat-tint: #e4f6ed; }
    .dashboard-stat.stat-pending { --stat-color: #b7791f; --stat-tint: #fff5df; }
    .dashboard-stat.stat-rejected { --stat-color: #c2413b; --stat-tint: #fff0ef; }

    .dashboard-stat-top {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }

    .dashboard-stat-label {
        color: #64748b;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.075em;
        text-transform: uppercase;
    }

    .dashboard-stat-icon {
        display: grid;
        width: 36px;
        height: 36px;
        flex: 0 0 36px;
        place-items: center;
        border-radius: 11px;
        background: var(--stat-tint);
        color: var(--stat-color);
    }

    .dashboard-stat-value {
        position: relative;
        z-index: 1;
        margin-top: 0.55rem;
        color: #163d36;
        font-size: 2rem;
        font-weight: 850;
        line-height: 1;
    }

    .dashboard-stat-note {
        position: relative;
        z-index: 1;
        margin-top: 0.45rem;
        color: #7b8794;
        font-size: 0.76rem;
    }

    .dashboard-listings {
        overflow: hidden;
        background: #fff;
        border: 1px solid #e8eeeb;
        border-radius: 16px;
        box-shadow: 0 5px 18px rgba(15, 23, 42, 0.035);
    }

    .dashboard-listings-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.15rem 1.25rem;
        border-bottom: 1px solid #edf1ef;
    }

    .dashboard-listings-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .dashboard-listings-title-icon {
        display: grid;
        width: 40px;
        height: 40px;
        flex: 0 0 40px;
        place-items: center;
        border-radius: 12px;
        background: #eaf7f2;
        color: #13805f;
    }

    .dashboard-listings-title h2 {
        margin: 0;
        color: var(--primary-dark);
        font-size: 1rem;
        font-weight: 800;
    }

    .dashboard-listings-title p {
        margin: 0.2rem 0 0;
        color: var(--text-muted);
        font-size: 0.8rem;
    }

    .dashboard-listings-count {
        padding: 0.35rem 0.65rem;
        border-radius: 999px;
        background: #f1f5f3;
        color: #53645d;
        font-size: 0.75rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .dashboard-listing-table-wrap {
        padding: 0 0.45rem 0.45rem;
    }

    .dashboard-listing-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .dashboard-listing-table th {
        padding: 0.75rem 0.8rem;
        background: #f8faf9;
        border-bottom: 1px solid #e9efec;
        color: #718078;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.07em;
        text-transform: uppercase;
    }

    .dashboard-listing-table td {
        padding: 0.9rem 0.8rem;
        border-bottom: 1px solid #f0f3f1;
        color: #3e4b46;
        font-size: 0.88rem;
        vertical-align: middle;
    }

    .dashboard-listing-table tbody tr:last-child td { border-bottom: 0; }
    .dashboard-listing-table tbody tr:hover { background: #fbfdfc; }

    .dashboard-business-name {
        color: var(--primary-dark);
        font-size: 0.92rem;
        font-weight: 800;
    }

    .dashboard-view-link {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        margin-top: 0.3rem;
        color: #13805f;
        font-size: 0.78rem;
        font-weight: 700;
        text-decoration: none;
    }

    .dashboard-view-link:hover { text-decoration: underline; }

    .dashboard-not-published {
        color: #8a9690;
        font-size: 0.76rem;
    }

    .dashboard-status {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.32rem 0.65rem;
        border-radius: 999px;
        font-size: 0.74rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .dashboard-empty {
        padding: 3.5rem 1.5rem;
        text-align: center;
    }

    .dashboard-empty-icon {
        display: grid;
        width: 54px;
        height: 54px;
        margin: 0 auto 0.9rem;
        place-items: center;
        border-radius: 16px;
        background: #eaf7f2;
        color: #13805f;
        font-size: 1.35rem;
    }

    .dashboard-empty h3 {
        margin: 0;
        color: var(--primary-dark);
        font-size: 1rem;
    }

    .dashboard-empty p {
        margin: 0.35rem 0 1.1rem;
        color: var(--text-muted);
        font-size: 0.86rem;
    }

    @media (max-width: 1100px) {
        .dashboard-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 700px) {
        .dashboard-stats { gap: 0.7rem; }
        .dashboard-stat { padding: 0.95rem; border-radius: 14px; }
        .dashboard-stat-value { font-size: 1.75rem; }
        .dashboard-section-heading { align-items: flex-start; }
        .dashboard-sector-chip { display: none; }
        .dashboard-listings-header { padding: 0.95rem; }
        .dashboard-listing-table-wrap { padding: 0.65rem; }
        .dashboard-listing-table-wrap .table-responsive { overflow: visible; }
        .dashboard-listing-table-wrap .table-responsive > table {
            display: block;
            min-width: 0 !important;
        }
        .dashboard-listing-table thead { display: none; }
        .dashboard-listing-table tbody { display: grid; gap: 0.7rem; }
        .dashboard-listing-table tbody tr {
            display: block;
            overflow: hidden;
            border: 1px solid #e8eeeb;
            border-radius: 12px;
            background: #fff;
        }
        .dashboard-listing-table tbody td {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.65rem 0.75rem;
            text-align: right;
        }
        .dashboard-listing-table tbody td::before {
            content: attr(data-label);
            flex: 0 0 32%;
            color: #718078;
            font-size: 0.67rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-align: left;
            text-transform: uppercase;
        }
    }

    @media (max-width: 420px) {
        .dashboard-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .dashboard-stat { padding: 0.8rem; }
        .dashboard-stat-label { font-size: 0.64rem; }
        .dashboard-stat-note { font-size: 0.68rem; }
    }

    @media (max-width: 340px) {
        .dashboard-stats { grid-template-columns: 1fr; }
    }
</style>

<div class="biz-page-body">
    <?php
        $totalListings = count($spots);
        $approvedCount = 0;
        $pendingCount = 0;
        $rejectedCount = 0;
        foreach ($spots as $s) {
            if ($s['status'] === 'approved') {
                $approvedCount++;
            } elseif ($s['status'] === 'pending') {
                $pendingCount++;
            } elseif ($s['status'] === 'rejected') {
                $rejectedCount++;
            }
        }
        $applicantType = (Auth::user()['applicant_type'] ?? 'private') === 'public'
            ? 'Public Sector'
            : 'Private Sector';
    ?>
    <div class="business-dashboard">
        <section aria-labelledby="dashboard-overview-title">
            <div class="dashboard-section-heading">
                <div>
                    <h2 id="dashboard-overview-title">Your listing overview</h2>
                    <p>Keep track of your accreditation progress at a glance.</p>
                </div>
                <span class="dashboard-sector-chip">
                    <i class="fa-solid fa-briefcase"></i>
                    <?= htmlspecialchars($applicantType) ?> Applicant
                </span>
            </div>

            <div class="dashboard-stats">
                <article class="dashboard-stat">
                    <div class="dashboard-stat-top">
                        <span class="dashboard-stat-label">Total Listings</span>
                        <span class="dashboard-stat-icon"><i class="fa-solid fa-store"></i></span>
                    </div>
                    <div class="dashboard-stat-value"><?= $totalListings ?></div>
                    <div class="dashboard-stat-note">Businesses submitted to the directory</div>
                </article>
                <article class="dashboard-stat stat-approved">
                    <div class="dashboard-stat-top">
                        <span class="dashboard-stat-label">Approved</span>
                        <span class="dashboard-stat-icon"><i class="fa-solid fa-circle-check"></i></span>
                    </div>
                    <div class="dashboard-stat-value"><?= $approvedCount ?></div>
                    <div class="dashboard-stat-note">Live on the tourism directory</div>
                </article>
                <article class="dashboard-stat stat-pending">
                    <div class="dashboard-stat-top">
                        <span class="dashboard-stat-label">Pending Review</span>
                        <span class="dashboard-stat-icon"><i class="fa-solid fa-hourglass-half"></i></span>
                    </div>
                    <div class="dashboard-stat-value"><?= $pendingCount ?></div>
                    <div class="dashboard-stat-note">Waiting for accreditation review</div>
                </article>
                <article class="dashboard-stat stat-rejected">
                    <div class="dashboard-stat-top">
                        <span class="dashboard-stat-label">Rejected</span>
                        <span class="dashboard-stat-icon"><i class="fa-solid fa-circle-exclamation"></i></span>
                    </div>
                    <div class="dashboard-stat-value"><?= $rejectedCount ?></div>
                    <div class="dashboard-stat-note">Review feedback in your listing</div>
                </article>
            </div>
        </section>

        <?php if (empty($spots)): ?>
            <section class="dashboard-listings" aria-labelledby="dashboard-listings-title">
                <div class="dashboard-listings-header">
                    <div class="dashboard-listings-title">
                        <span class="dashboard-listings-title-icon"><i class="fa-solid fa-list-check"></i></span>
                        <div>
                            <h2 id="dashboard-listings-title">Your listings</h2>
                            <p>Manage your submitted tourism businesses</p>
                        </div>
                    </div>
                    <span class="dashboard-listings-count">0 listings</span>
                </div>
                <div class="dashboard-empty">
                    <span class="dashboard-empty-icon"><i class="fa-solid fa-store"></i></span>
                    <h3>No listings yet</h3>
                    <p>Start by applying to add your business to the tourism directory.</p>
                    <a href="<?= BASE_URL ?>/business/apply" class="btn btn-primary">
                        <i class="fa-solid fa-file-signature"></i> Apply for Listing
                    </a>
                </div>
            </section>
        <?php else: ?>
            <section class="dashboard-listings" aria-labelledby="dashboard-listings-title">
                <div class="dashboard-listings-header">
                    <div class="dashboard-listings-title">
                        <span class="dashboard-listings-title-icon"><i class="fa-solid fa-list-check"></i></span>
                        <div>
                            <h2 id="dashboard-listings-title">Your listings</h2>
                            <p>All submitted spots and their current status</p>
                        </div>
                    </div>
                    <span class="dashboard-listings-count"><?= $totalListings ?> listing<?= $totalListings !== 1 ? 's' : '' ?></span>
                </div>

                <div class="dashboard-listing-table-wrap">
                    <div class="table-responsive">
                        <table class="dashboard-listing-table">
                            <thead>
                                <tr>
                                    <th>Business Name</th>
                                    <th>Category</th>
                                    <th>Submitted</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($spots as $spot): ?>
                                    <?php
                                        $statusMap = [
                                            'pending'        => ['bg' => '#fff5df', 'txt' => '#a16207', 'label' => 'Pending'],
                                            'approved'       => ['bg' => '#e4f6ed', 'txt' => '#16734f', 'label' => 'Approved'],
                                            'rejected'       => ['bg' => '#fff0ef', 'txt' => '#b42318', 'label' => 'Rejected'],
                                            'needs_revision' => ['bg' => '#eaf2ff', 'txt' => '#1d4ed8', 'label' => 'Needs Revision'],
                                        ];
                                        $st = $statusMap[$spot['status']] ?? ['bg' => '#f1f5f3', 'txt' => '#53645d', 'label' => ucfirst($spot['status'])];
                                    ?>
                                    <tr>
                                        <td data-label="Business">
                                            <div>
                                                <div class="dashboard-business-name"><?= htmlspecialchars($spot['name']) ?></div>
                                                <?php if ($spot['status'] === 'approved'): ?>
                                                    <a class="dashboard-view-link" href="<?= BASE_URL ?>/spot/<?= (int)$spot['id'] ?>" target="_blank" rel="noopener noreferrer">
                                                        <i class="fa-solid fa-arrow-up-right-from-square"></i> View live listing
                                                    </a>
                                                <?php else: ?>
                                                    <span class="dashboard-not-published">Not published yet</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td data-label="Category"><?= htmlspecialchars($spot['category_name'] ?? 'Uncategorized') ?></td>
                                        <td data-label="Submitted"><?= date('M d, Y', strtotime($spot['created_at'])) ?></td>
                                        <td data-label="Status">
                                            <span class="dashboard-status" style="background: <?= $st['bg'] ?>; color: <?= $st['txt'] ?>;">
                                                <?= htmlspecialchars($st['label']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </div>
</div><!-- /.biz-page-body -->

<?php require_once '../app/views/partials/business-footer.php'; ?>
