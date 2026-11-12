<?php
$title = 'Manage Spots - LGU Admin';
$activePage = 'spots';
require_once '../app/views/partials/lgu-header.php';
?>

<!-- Top Bar -->
<div class="lgu-topbar">
    <div>
        <div class="lgu-topbar-title">Manage Spots</div>
        <div class="lgu-topbar-breadcrumb">All applicant and business-submitted tourist spots</div>
    </div>
</div>

<div class="lgu-page-body">
    <form method="GET" action="<?= BASE_URL ?>/lgu/spots" class="admin-filter-form" id="filterForm">
        <div style="flex:1; min-width:220px;">
            <label style="display:block; font-size:0.85rem; color:#6b7280; margin-bottom:0.35rem;">Search by name</label>
            <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Search spot name..." style="width:100%; padding:0.85rem 1rem; border:1px solid #d1d5db; border-radius:0.75rem; font-size:0.95rem;" />
        </div>
        <div>
            <label style="display:block; font-size:0.85rem; color:#6b7280; margin-bottom:0.35rem;">Source</label>
            <select name="applicant_type" style="width:100%; padding:0.85rem 1rem; border:1px solid #d1d5db; border-radius:0.75rem; font-size:0.95rem;" onchange="document.getElementById('filterForm').submit();">
                <option value="" <?= (isset($sourceFilter) && $sourceFilter === '') ? 'selected' : '' ?>>All Sources</option>
                <option value="private" <?= (isset($sourceFilter) && $sourceFilter === 'private') ? 'selected' : '' ?>>Private Sector</option>
                <option value="public" <?= (isset($sourceFilter) && $sourceFilter === 'public') ? 'selected' : '' ?>>Public Sector</option>
            </select>
        </div>
    </form>
    <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 4px rgba(0,0,0,0.07); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: #f8f9fb; border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 1rem 1.25rem; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 700;">Name</th>
                    <th style="padding: 1rem 1.25rem; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 700;">Category</th>
                    <th style="padding: 1rem 1.25rem; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 700;">Source</th>
                    <th style="padding: 1rem 1.25rem; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 700;">Status</th>
                    <th style="padding: 1rem 1.25rem; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 700;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($spots)): ?>
                    <tr>
                        <td colspan="5" style="padding: 3rem; text-align: center; color: var(--text-muted);">
                            <i class="fa-solid fa-map-pin" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                            <?= isset($filterApplied) && $filterApplied ? 'No available at the moment.' : 'No spots added yet.' ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($spots as $spot): ?>
                        <?php
                            $statusColors = [
                                'pending'        => ['bg' => '#fef3c7', 'text' => '#92400e'],
                                'approved'       => ['bg' => '#dcfce7', 'text' => '#166534'],
                                'rejected'       => ['bg' => '#fee2e2', 'text' => '#991b1b'],
                                'needs_revision' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
                            ];
                            $sc = $statusColors[$spot['status']] ?? ['bg' => '#f3f4f6', 'text' => '#374151'];
                        ?>
                        <tr style="border-bottom: 1px solid #f3f4f6; transition: background 0.15s;" onmouseover="this.style.background='#f8f9fb'" onmouseout="this.style.background='white'">
                            <td style="padding: 1rem 1.25rem; font-weight: 600; color: var(--primary-dark);"><?= htmlspecialchars($spot['name']) ?></td>
                            <td style="padding: 1rem 1.25rem; color: #4b5563;"><?= htmlspecialchars($spot['category_name']) ?></td>
                            <td style="padding: 1rem 1.25rem;">
                                <?php 
                                    $applicantType = $spot['applicant_type'] ?? 'private';
                                    if ($applicantType === 'public'):
                                        $badge = ['icon' => 'fa-landmark', 'label' => 'Public Sector', 'bg' => '#ecfdf5', 'color' => '#065f46'];
                                    else:
                                        $badge = ['icon' => 'fa-store', 'label' => 'Private Sector', 'bg' => '#fef3c7', 'color' => '#92400e'];
                                    endif;
                                ?>
                                <span style="background: <?= $badge['bg'] ?>; color: <?= $badge['color'] ?>; padding: 0.25rem 0.65rem; border-radius: 999px; font-size: 0.78rem; font-weight: 600;">
                                    <i class="fa-solid <?= $badge['icon'] ?>"></i> <?= $badge['label'] ?>
                                </span>
                            </td>
                            <td style="padding: 1rem 1.25rem;">
                                <span style="background: <?= $sc['bg'] ?>; color: <?= $sc['text'] ?>; padding: 0.25rem 0.65rem; border-radius: 999px; font-size: 0.78rem; font-weight: 600; text-transform: capitalize;">
                                    <?= htmlspecialchars($spot['status']) ?>
                                </span>
                            </td>
                            <td style="padding: 1rem 1.25rem; white-space: nowrap;">
                                <a href="<?= BASE_URL ?>/lgu/spots/<?= $spot['id'] ?>/edit" class="btn btn-primary" style="padding: 0.35rem 0.85rem; font-size: 0.82rem; margin-right: 0.35rem;">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </a>
                                <form method="POST" action="<?= BASE_URL ?>/lgu/spots/<?= $spot['id'] ?>/delete" style="display:inline;" class="delete-spot-form" data-delete-confirm="true">
                                    <button type="submit" class="btn" style="padding:0.35rem 0.85rem;font-size:0.82rem;background:#fee2e2;color:#991b1b;">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../app/views/partials/lgu-footer.php'; ?>

<script>
document.addEventListener('submit', function (event) {
    if (event.target && event.target.classList.contains('delete-spot-form')) {
        event.preventDefault();
        const form = event.target;

        Swal.fire({
            title: 'Are you sure?',
            text: 'Are you sure you want to delete this spot? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#4b5563',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then(function (result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
});
</script>
