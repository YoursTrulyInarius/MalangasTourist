<?php
$title      = 'Reports - LGU Admin';
$activePage = 'reports';
require_once '../app/views/partials/lgu-header.php';
?>
<style>
@media print {
    .lgu-sidebar, .lgu-topbar, .no-print { display: none !important; }
    .lgu-main { margin-left: 0 !important; }
    .lgu-page-body { padding: 0 !important; }
    body { background: white !important; }
    .report-card { box-shadow: none !important; border: 1px solid #e5e7eb !important; break-inside: avoid; }
}
</style>

<!-- Top Bar -->
<div class="lgu-topbar no-print">
    <div>
        <div class="lgu-topbar-title">Reports & Analytics</div>
        <div class="lgu-topbar-breadcrumb">Tourism data summary as of <?= date('F d, Y') ?></div>
    </div>
    <div style="display:flex;gap:0.75rem;">
        <a href="<?= BASE_URL ?>/lgu/reports/export" class="btn btn-primary" style="padding:0.45rem 1rem;font-size:0.88rem;font-weight:600;">
            <i class="fa-solid fa-file-csv"></i> Export CSV
        </a>
        <button onclick="window.print()" class="btn" style="padding:0.45rem 1rem;font-size:0.88rem;font-weight:600;background:#f3f4f6;color:#374151;border:1.5px solid #d1d5db;">
            <i class="fa-solid fa-print"></i> Print Report
        </button>
    </div>
</div>

<div class="lgu-page-body">

    <!-- Summary Stat Cards -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;margin-bottom:1.75rem;">
        <?php
        $cards = [
            ['label' => 'Total Spots',      'value' => $report['totals']['total_spots'],     'color' => 'var(--primary-color)',       'icon' => 'fa-map-pin'],
            ['label' => 'Approved',          'value' => $report['totals']['approved'],         'color' => '#16a34a',                    'icon' => 'fa-circle-check'],
            ['label' => 'Pending Review',    'value' => $report['totals']['pending'],          'color' => 'var(--secondary-color)',     'icon' => 'fa-clock'],
            ['label' => 'LGU Official',      'value' => $report['totals']['lgu_spots'],        'color' => 'var(--complementary-color)', 'icon' => 'fa-shield-halved'],
            ['label' => 'Business Listed',   'value' => $report['totals']['business_spots'],   'color' => '#7c3aed',                    'icon' => 'fa-store'],
            ['label' => 'Total Reviews',     'value' => $report['review_totals']['total_reviews'], 'color' => '#db2777',               'icon' => 'fa-star'],
        ];
        foreach ($cards as $card): ?>
        <div class="report-card" style="background:white;border-radius:0.75rem;padding:1.25rem 1.5rem;box-shadow:0 1px 4px rgba(0,0,0,0.07);border-left:4px solid <?= $card['color'] ?>;">
            <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-muted);margin-bottom:0.5rem;">
                <i class="fa-solid <?= $card['icon'] ?>"></i> <?= $card['label'] ?>
            </div>
            <div style="font-size:2rem;font-weight:800;color:<?= $card['color'] ?>;"><?= $card['value'] ?? 0 ?></div>
        </div>
        <?php endforeach; ?>

        <!-- Overall Rating Card -->
        <div class="report-card" style="background:white;border-radius:0.75rem;padding:1.25rem 1.5rem;box-shadow:0 1px 4px rgba(0,0,0,0.07);border-left:4px solid #f59e0b;">
            <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-muted);margin-bottom:0.5rem;">
                <i class="fa-solid fa-star-half-stroke"></i> Avg Rating
            </div>
            <div style="font-size:2rem;font-weight:800;color:#f59e0b;"><?= number_format($report['review_totals']['overall_avg'] ?? 0, 1) ?> ★</div>
        </div>
    </div>

    <div class="admin-grid-2col">

        <!-- By Category -->
        <div class="report-card" style="background:white;border-radius:0.75rem;box-shadow:0 1px 4px rgba(0,0,0,0.07);overflow:hidden;">
            <div style="padding:1.25rem 1.5rem;border-bottom:1px solid #f0f0f0;background:#f8f9fb;font-weight:700;font-size:0.95rem;color:var(--primary-dark);">
                <i class="fa-solid fa-layer-group"></i> Spots by Category
            </div>
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:#f8f9fb;border-bottom:1px solid #e5e7eb;">
                        <th style="padding:0.65rem 1.25rem;font-size:0.78rem;text-transform:uppercase;color:var(--text-muted);font-weight:700;text-align:left;">Category</th>
                        <th style="padding:0.65rem 1rem;font-size:0.78rem;text-transform:uppercase;color:var(--text-muted);font-weight:700;text-align:center;">Total</th>
                        <th style="padding:0.65rem 1rem;font-size:0.78rem;text-transform:uppercase;color:var(--text-muted);font-weight:700;text-align:center;">Approved</th>
                        <th style="padding:0.65rem 1rem;font-size:0.78rem;text-transform:uppercase;color:var(--text-muted);font-weight:700;text-align:center;">Pending</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report['by_category'] as $cat): ?>
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        <td style="padding:0.75rem 1.25rem;font-size:0.88rem;color:var(--primary-dark);font-weight:600;">
                            <i class="fa-solid <?= htmlspecialchars($cat['icon']) ?>"></i>
                            <?= htmlspecialchars($cat['name']) ?>
                        </td>
                        <td style="padding:0.75rem 1rem;text-align:center;font-weight:700;color:var(--primary-dark);"><?= $cat['total'] ?></td>
                        <td style="padding:0.75rem 1rem;text-align:center;color:#16a34a;font-weight:600;"><?= $cat['approved'] ?></td>
                        <td style="padding:0.75rem 1rem;text-align:center;color:#ca8a04;font-weight:600;"><?= $cat['pending'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Monthly Applications -->
        <div class="report-card" style="background:white;border-radius:0.75rem;box-shadow:0 1px 4px rgba(0,0,0,0.07);overflow:hidden;">
            <div style="padding:1.25rem 1.5rem;border-bottom:1px solid #f0f0f0;background:#f8f9fb;font-weight:700;font-size:0.95rem;color:var(--primary-dark);">
                <i class="fa-solid fa-calendar-days"></i> Monthly Application Trends
            </div>
            <?php if (empty($report['monthly_apps'])): ?>
                <div style="padding:3rem;text-align:center;color:var(--text-muted);">No application data yet.</div>
            <?php else: ?>
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:#f8f9fb;border-bottom:1px solid #e5e7eb;">
                        <th style="padding:0.65rem 1.25rem;font-size:0.78rem;text-transform:uppercase;color:var(--text-muted);font-weight:700;text-align:left;">Month</th>
                        <th style="padding:0.65rem 1rem;font-size:0.78rem;text-transform:uppercase;color:var(--text-muted);font-weight:700;text-align:center;">Total</th>
                        <th style="padding:0.65rem 1rem;font-size:0.78rem;text-transform:uppercase;color:var(--text-muted);font-weight:700;text-align:center;">Approved</th>
                        <th style="padding:0.65rem 1rem;font-size:0.78rem;text-transform:uppercase;color:var(--text-muted);font-weight:700;text-align:center;">Rejected</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report['monthly_apps'] as $row): ?>
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        <td style="padding:0.75rem 1.25rem;font-size:0.88rem;font-weight:600;color:var(--primary-dark);"><?= $row['month'] ?></td>
                        <td style="padding:0.75rem 1rem;text-align:center;font-weight:700;"><?= $row['total'] ?></td>
                        <td style="padding:0.75rem 1rem;text-align:center;color:#16a34a;font-weight:600;"><?= $row['approved'] ?></td>
                        <td style="padding:0.75rem 1rem;text-align:center;color:#dc2626;font-weight:600;"><?= $row['rejected'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Top Rated Spots -->
    <div class="report-card" style="background:white;border-radius:0.75rem;box-shadow:0 1px 4px rgba(0,0,0,0.07);overflow:hidden;">
        <div style="padding:1.25rem 1.5rem;border-bottom:1px solid #f0f0f0;background:#f8f9fb;font-weight:700;font-size:0.95rem;color:var(--primary-dark);">
            <i class="fa-solid fa-trophy"></i> Top Rated Spots
        </div>
        <?php if (empty($report['top_rated'])): ?>
            <div style="padding:3rem;text-align:center;color:var(--text-muted);">No approved spots with reviews yet.</div>
        <?php else: ?>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f8f9fb;border-bottom:1px solid #e5e7eb;">
                    <th style="padding:0.65rem 1.25rem;font-size:0.78rem;text-transform:uppercase;color:var(--text-muted);font-weight:700;text-align:left;">#</th>
                    <th style="padding:0.65rem 1.25rem;font-size:0.78rem;text-transform:uppercase;color:var(--text-muted);font-weight:700;text-align:left;">Spot Name</th>
                    <th style="padding:0.65rem 1rem;font-size:0.78rem;text-transform:uppercase;color:var(--text-muted);font-weight:700;text-align:left;">Category</th>
                    <th style="padding:0.65rem 1rem;font-size:0.78rem;text-transform:uppercase;color:var(--text-muted);font-weight:700;text-align:center;">Status</th>
                    <th style="padding:0.65rem 1rem;font-size:0.78rem;text-transform:uppercase;color:var(--text-muted);font-weight:700;text-align:center;">Avg Rating</th>
                    <th style="padding:0.65rem 1rem;font-size:0.78rem;text-transform:uppercase;color:var(--text-muted);font-weight:700;text-align:center;">Reviews</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($report['top_rated'] as $idx => $row): ?>
                <?php
                    $opLabels = ['open' => ['label'=>'Open','bg'=>'#dcfce7','txt'=>'#166534'], 'closed' => ['label'=>'Closed','bg'=>'#fee2e2','txt'=>'#991b1b'], 'temp_closed' => ['label'=>'Temp. Closed','bg'=>'#fef3c7','txt'=>'#92400e'], 'maintenance' => ['label'=>'Maintenance','bg'=>'#dbeafe','txt'=>'#1e40af']];
                    $opStyle = $opLabels[$row['operational_status']] ?? ['label'=>'Open','bg'=>'#dcfce7','txt'=>'#166534'];
                ?>
                <tr style="border-bottom:1px solid #f3f4f6;" onmouseover="this.style.background='#f8f9fb'" onmouseout="this.style.background='white'">
                    <td style="padding:0.85rem 1.25rem;font-weight:700;color:var(--text-muted);"><?= $idx + 1 ?></td>
                    <td style="padding:0.85rem 1.25rem;font-weight:600;color:var(--primary-dark);"><?= htmlspecialchars($row['name']) ?></td>
                    <td style="padding:0.85rem 1rem;color:#4b5563;font-size:0.88rem;"><?= htmlspecialchars($row['category']) ?></td>
                    <td style="padding:0.85rem 1rem;text-align:center;">
                        <span style="background:<?= $opStyle['bg'] ?>;color:<?= $opStyle['txt'] ?>;padding:0.2rem 0.65rem;border-radius:999px;font-size:0.75rem;font-weight:700;"><?= $opStyle['label'] ?></span>
                    </td>
                    <td style="padding:0.85rem 1rem;text-align:center;">
                        <span style="color:#f59e0b;font-weight:700;font-size:1rem;">★ <?= $row['avg_rating'] ?? '—' ?></span>
                    </td>
                    <td style="padding:0.85rem 1rem;text-align:center;color:#4b5563;"><?= $row['review_count'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

</div><!-- /.lgu-page-body -->

<?php require_once '../app/views/partials/lgu-footer.php'; ?>
