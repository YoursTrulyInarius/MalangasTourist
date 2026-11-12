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

<div class="biz-page-body">

    <!-- Stat Cards -->
    <?php
        $totalListings = count($spots);
        $approvedCount = 0;
        $pendingCount = 0;
        $rejectedCount = 0;
        foreach ($spots as $s) {
            if ($s['status'] === 'approved') $approvedCount++;
            elseif ($s['status'] === 'pending') $pendingCount++;
            elseif ($s['status'] === 'rejected') $rejectedCount++;
        }
    ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        <div style="background: white; border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 1px 4px rgba(0,0,0,0.07); border-left: 4px solid var(--primary-color);">
            <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-muted); margin-bottom: 0.5rem;">Total Listings</div>
            <div style="font-size: 2.25rem; font-weight: 800; color: var(--primary-dark);"><?= $totalListings ?></div>
        </div>

        <div style="background: white; border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 1px 4px rgba(0,0,0,0.07); border-left: 4px solid #22c55e;">
            <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-muted); margin-bottom: 0.5rem;">Approved</div>
            <div style="font-size: 2.25rem; font-weight: 800; color: #22c55e;"><?= $approvedCount ?></div>
        </div>

        <div style="background: white; border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 1px 4px rgba(0,0,0,0.07); border-left: 4px solid var(--secondary-color);">
            <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-muted); margin-bottom: 0.5rem;">Pending</div>
            <div style="font-size: 2.25rem; font-weight: 800; color: var(--secondary-color);"><?= $pendingCount ?></div>
        </div>

        <div style="background: white; border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 1px 4px rgba(0,0,0,0.07); border-left: 4px solid #ef4444;">
            <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-muted); margin-bottom: 0.5rem;">Rejected</div>
            <div style="font-size: 2.25rem; font-weight: 800; color: #ef4444;"><?= $rejectedCount ?></div>
        </div>
    </div>

    <!-- Listings Table -->
    <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 4px rgba(0,0,0,0.07); overflow: hidden;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-weight: 700; font-size: 1rem; color: var(--primary-dark);">Your Listings</div>
                <div style="font-size: 0.8rem; color: var(--text-muted);">All submitted spots and their current status</div>
            </div>
        </div>

        <?php if (empty($spots)): ?>
            <div style="text-align: center; padding: 4rem 2rem;">
                <i class="fa-solid fa-store" style="font-size: 3rem; color: #d1d5db; margin-bottom: 1rem;"></i>
                <p style="color: var(--text-muted); font-size: 1rem; margin-bottom: 1.5rem;">You don't have any listings yet.</p>
                <a href="<?= BASE_URL ?>/business/apply" class="btn btn-primary">
                    <i class="fa-solid fa-file-signature"></i> Apply for Listing
                </a>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background: #f8f9fb; border-bottom: 2px solid #e5e7eb;">
                            <th style="padding: 0.85rem 1.25rem; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted);">Business Name</th>
                            <th style="padding: 0.85rem 1.25rem; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted);">Category</th>
                            <th style="padding: 0.85rem 1.25rem; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted);">Submitted</th>
                            <th style="padding: 0.85rem 1.25rem; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted);">Status</th>
                            <th style="padding: 0.85rem 1.25rem; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted);">Open/Closed</th>
                            <th style="padding: 0.85rem 1.25rem; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted);">LGU Comment</th>
                            <th style="padding: 0.85rem 1.25rem; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted);">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($spots as $spot): ?>
                        <tr style="border-bottom: 1px solid #f0f0f0; transition: background 0.1s;">
                            <td style="padding: 1rem 1.25rem;">
                                <strong style="color: var(--primary-dark);"><?= htmlspecialchars($spot['name']) ?></strong>
                                <?php if ($spot['status'] === 'approved'): ?>
                                    <br><a href="<?= BASE_URL ?>/spot/<?= $spot['id'] ?>" target="_blank" style="font-size: 0.8rem; color: var(--secondary-color);"><i class="fa-solid fa-arrow-up-right-from-square"></i> View Live</a>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem 1.25rem; color: var(--text-main); font-size: 0.9rem;"><?= htmlspecialchars($spot['category_name']) ?></td>
                            <td style="padding: 1rem 1.25rem; color: var(--text-muted); font-size: 0.9rem;"><?= date('M d, Y', strtotime($spot['created_at'])) ?></td>
                            <td style="padding: 1rem 1.25rem;">
                                <?php
                                    $statusMap = [
                                        'pending'        => ['bg' => '#fef3c7', 'txt' => '#92400e', 'label' => 'Pending'],
                                        'approved'       => ['bg' => '#dcfce7', 'txt' => '#166534', 'label' => 'Approved'],
                                        'rejected'       => ['bg' => '#fee2e2', 'txt' => '#991b1b', 'label' => 'Rejected'],
                                        'needs_revision' => ['bg' => '#dbeafe', 'txt' => '#1e40af', 'label' => 'Needs Revision'],
                                    ];
                                    $st = $statusMap[$spot['status']] ?? ['bg' => '#f3f4f6', 'txt' => '#374151', 'label' => ucfirst($spot['status'])];
                                ?>
                                <span style="background:<?= $st['bg'] ?>;color:<?= $st['txt'] ?>;padding:0.2rem 0.65rem;border-radius:999px;font-size:0.8rem;font-weight:700;"><?= $st['label'] ?></span>
                                <?php if (!empty($spot['pending_photo_update'])): ?>
                                    <br><span style="display:inline-block;margin-top:0.35rem;background:#fef3c7;color:#92400e;padding:0.2rem 0.55rem;border-radius:999px;font-size:0.72rem;font-weight:700;"><i class="fa-regular fa-images"></i> Photo Update Pending</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem 1.25rem;">
                                <?php
                                    $monitorStatus = $spot['monitor_status'] ?? 'normal';
                                    $opLabels = [
                                        'open' => ['label'=>'Open','bg'=>'#dcfce7','txt'=>'#166534'],
                                        'closed' => ['label'=>'Closed','bg'=>'#fee2e2','txt'=>'#991b1b'],
                                        'temp_closed' => ['label'=>'Temp. Closed','bg'=>'#fef3c7','txt'=>'#92400e'],
                                        'maintenance' => ['label'=>'Under Renovation','bg'=>'#dbeafe','txt'=>'#1e40af'],
                                        'warning' => ['label'=>'Warning','bg'=>'#fef3c7','txt'=>'#92400e'],
                                        'under_renovation' => ['label'=>'Under Renovation','bg'=>'#dbeafe','txt'=>'#1e40af'],
                                        'improved' => ['label'=>'Improved','bg'=>'#e0f2fe','txt'=>'#0f766e'],
                                    ];
                                    $displayStatus = $monitorStatus === 'normal' ? ($spot['operational_status'] ?? 'open') : $monitorStatus;
                                    $opStyle = $opLabels[$displayStatus] ?? ['label'=>'Open','bg'=>'#dcfce7','txt'=>'#166534'];
                                ?>
                                <span style="background:<?= $opStyle['bg'] ?>;color:<?= $opStyle['txt'] ?>;padding:0.2rem 0.65rem;border-radius:999px;font-size:0.8rem;font-weight:700;"><?= $opStyle['label'] ?></span>
                            </td>
                            <td style="padding: 1rem 1.25rem;">
                                <?php if (!empty($spot['rejection_reason'])): ?>
                                    <span style="color: #ef4444; font-size: 0.85rem;"><?= htmlspecialchars($spot['rejection_reason']) ?></span>
                                <?php else: ?>
                                    <span style="color: var(--text-muted);">—</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem 1.25rem;">
                                <a href="<?= BASE_URL ?>/business/edit/<?= $spot['id'] ?>" style="padding:0.4rem 0.85rem;font-size:0.82rem;border-radius:0.4rem;background:var(--primary-color);color:white;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:0.35rem;">
                                    <i class="fa-solid fa-pen-to-square"></i> Manage
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</div><!-- /.biz-page-body -->

<?php require_once '../app/views/partials/business-footer.php'; ?>
