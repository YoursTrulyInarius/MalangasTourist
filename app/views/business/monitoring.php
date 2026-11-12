<?php
$title = 'Monitoring & Improvements - MTMS';
$activePage = 'monitoring';
$spots = $spots ?? [];
require_once '../app/views/partials/business-header.php';
?>

<style>
    .monitor-page { display: grid; gap: 1.5rem; }

    .monitor-card {
        background: white; border-radius: 16px;
        box-shadow: 0 8px 24px rgba(15,23,42,0.05);
        border: 1px solid #edf0ee; overflow: hidden;
    }
    .monitor-card.card-warning  { border-color: #fca5a5; box-shadow: 0 8px 24px rgba(220,38,38,0.09); }
    .monitor-card.card-declined { border-color: #f97316; box-shadow: 0 8px 24px rgba(249,115,22,0.09); }
    .monitor-card.card-pending  { border-color: #60a5fa; box-shadow: 0 8px 24px rgba(96,165,250,0.09); }
    .monitor-card.card-approved { border-color: #4ade80; box-shadow: 0 8px 24px rgba(74,222,128,0.09); }

    .monitor-card-top {
        padding: 1.25rem 1.4rem 1rem;
        display: flex; align-items: flex-start;
        justify-content: space-between; gap: 1rem;
        border-bottom: 1px solid #edf0ee;
    }
    .monitor-card-top h2 { margin: 0 0 0.35rem; font-size: 1.15rem; color: var(--primary-dark); }

    .status-pill {
        display: inline-flex; align-items: center; gap: 0.35rem;
        border-radius: 999px; padding: 0.32rem 0.75rem;
        font-size: 0.72rem; font-weight: 700; letter-spacing: 0.05em;
        text-transform: uppercase; white-space: nowrap; flex-shrink: 0;
    }
    .pill-normal   { background:#dcfce7; color:#166534; }
    .pill-warning  { background:#fef3c7; color:#92400e; }
    .pill-declined { background:#ffedd5; color:#9a3412; }
    .pill-pending  { background:#dbeafe; color:#1d4ed8; }
    .pill-improved { background:#e0f2fe; color:#0f766e; }

    .monitor-meta {
        display: flex; flex-wrap: wrap; gap: 0.35rem 1.1rem;
        font-size: 0.84rem; color: var(--text-muted); margin-top: 0.3rem;
    }
    .monitor-meta strong { color: var(--primary-dark); }

    /* Deadline bar */
    .deadline-bar {
        margin: 1rem 1.4rem 0;
        background: #fff7ed; border: 1px solid #fed7aa;
        border-radius: 10px; padding: 0.65rem 1rem;
        display: flex; align-items: center; gap: 0.75rem;
        font-size: 0.85rem; color: #9a3412;
    }
    .deadline-bar.deadline-urgent { background:#fff1f2; border-color:#fca5a5; color:#991b1b; }
    .deadline-bar strong { color: #7c2d12; }

    /* Notice boxes */
    .notice-box { margin: 1rem 1.4rem 0; border-radius: 12px; padding: 1rem 1.15rem; }
    .notice-warning  { background: linear-gradient(135deg,#fff1f2,#fff7f7); border: 1px solid #fca5a5; }
    .notice-declined { background: linear-gradient(135deg,#fff7ed,#fffbf5); border: 1px solid #fdba74; }
    .notice-pending  { background: linear-gradient(135deg,#eff6ff,#f8faff); border: 1px solid #93c5fd; }
    .notice-approved { background: linear-gradient(135deg,#f0fdf4,#f8fffb); border: 1px solid #86efac; }

    .notice-label {
        display: flex; align-items: center; gap: 0.5rem;
        font-size: 0.72rem; font-weight: 800; letter-spacing: 0.06em;
        text-transform: uppercase; margin-bottom: 0.55rem;
    }
    .lbl-warning  { color: #991b1b; }
    .lbl-declined { color: #9a3412; }
    .lbl-pending  { color: #1d4ed8; }
    .lbl-approved { color: #166534; }

    .notice-text { margin: 0; line-height: 1.75; font-size: 0.9rem; }
    .txt-warning  { color: #7f1d1d; white-space: pre-line; }
    .txt-declined { color: #7c2d12; white-space: pre-line; }
    .txt-pending  { color: #1e3a8a; }
    .txt-approved { color: #14532d; }

    /* Proof gallery */
    .proof-section { margin: 1rem 1.4rem 0; }
    .proof-section-label {
        font-size: 0.75rem; font-weight: 700; letter-spacing: 0.07em;
        text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem;
    }
    .proof-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 0.6rem; }
    .proof-thumb { border: 1px solid #edf0ee; border-radius: 10px; overflow: hidden; background: #f8fafc; }
    .proof-thumb a { display: block; }
    .proof-thumb img { display: block; width: 100%; height: 140px; object-fit: cover; transition: opacity 0.2s; }
    .proof-thumb img:hover { opacity: 0.85; }

    /* Submit section */
    .submit-section {
        margin: 1rem 1.4rem 1.4rem;
        background: #f8fafc; border: 1px solid #edf0ee;
        border-radius: 14px; padding: 1.25rem;
    }
    .submit-section-title {
        display: flex; align-items: center; gap: 0.6rem;
        font-size: 1rem; font-weight: 700; color: var(--primary-dark);
        margin: 0 0 1rem;
    }
    .submit-section-title i { color: var(--secondary-color); }

    .form-grid { display: grid; gap: 0.9rem; }
    .field-group { display: grid; gap: 0.35rem; }
    .field-group label {
        font-size: 0.78rem; font-weight: 700; color: var(--primary-dark);
        letter-spacing: 0.05em; text-transform: uppercase;
    }
    .field-group select,
    .field-group textarea {
        width: 100%; border: 1px solid #dfe7e1; border-radius: 10px;
        padding: 0.75rem 0.9rem; font: inherit;
        background: white; color: var(--text-main); transition: border-color 0.15s;
    }
    .field-group select:focus,
    .field-group textarea:focus { outline: none; border-color: var(--secondary-color); }
    .field-group textarea { min-height: 110px; resize: vertical; }
    .field-hint { font-size: 0.78rem; color: var(--text-muted); }

    /* Drag-and-drop dropzone */
    .file-dropzone {
        border: 2px dashed #d1d5db; border-radius: 12px; padding: 1.5rem;
        text-align: center; cursor: pointer; background: white;
        transition: border-color 0.2s, background 0.2s;
    }
    .file-dropzone:hover, .file-dropzone.dragover {
        border-color: var(--secondary-color); background: #f0fdf4;
    }
    .file-dropzone .dz-icon { font-size: 1.8rem; color: #9ca3af; margin-bottom: 0.5rem; }
    .file-dropzone p { margin: 0; font-size: 0.87rem; color: var(--text-muted); }

    .upload-preview-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 0.5rem; margin-top: 0.6rem;
    }
    .file-preview-thumb {
        position: relative; border-radius: 8px; overflow: hidden; border: 1px solid #e5e7eb;
    }
    .file-preview-thumb img { display: block; width: 100%; height: 90px; object-fit: cover; }
    .file-preview-thumb .remove-btn {
        position: absolute; top: 3px; right: 3px;
        background: rgba(0,0,0,0.6); color: white; border: none;
        border-radius: 50%; width: 20px; height: 20px; cursor: pointer;
        font-size: 0.7rem; display: flex; align-items: center; justify-content: center;
        padding: 0;
    }

    .form-actions { display: flex; gap: 0.75rem; flex-wrap: wrap; }

    /* Empty state */
    .empty-state {
        text-align: center; padding: 4rem 2rem; background: white;
        border-radius: 16px; border: 1px solid #edf0ee;
        box-shadow: 0 8px 24px rgba(15,23,42,0.04);
    }
    .empty-state i { font-size: 3rem; color: #d1d5db; display: block; margin-bottom: 1rem; }
    .empty-state h2 { margin: 0 0 0.5rem; color: var(--primary-dark); font-size: 1.3rem; }
    .empty-state p { margin: 0; color: var(--text-muted); }

    .spacer { height: 1rem; }

    @media (max-width: 600px) {
        .monitor-card-top { flex-direction: column; }
        .notice-box, .proof-section, .submit-section, .deadline-bar {
            margin-left: 1rem; margin-right: 1rem;
        }
    }
</style>

<div class="biz-topbar">
    <div>
        <div class="biz-topbar-title">Monitoring &amp; Improvements</div>
        <div class="biz-topbar-breadcrumb">Track LGU notices and submit your improvement updates</div>
    </div>
</div>

<div class="biz-page-body">
    <div class="monitor-page">

        <?php if (empty($spots)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-clipboard-check"></i>
                <h2>No monitoring notices yet</h2>
                <p>Your listings will appear here if the LGU issues a warning or improvement request.</p>
            </div>
        <?php else: ?>
            <?php foreach ($spots as $spot): ?>
                <?php
                    $spotId      = (int)($spot['id'] ?? 0);
                    $monStatus   = $spot['monitor_status'] ?? 'normal';
                    $subStatus   = $spot['monitor_submission_status'] ?? 'none';
                    $monitorNote = trim((string)($spot['monitor_note'] ?? ''));
                    $declineNote = trim((string)($spot['monitor_decline_reason'] ?? ''));
                    $renovNote   = trim((string)($spot['renovation_note'] ?? ''));
                    $deadline    = $spot['improvement_deadline'] ?? '';

                    $isPending  = ($subStatus === 'pending');
                    $isDeclined = ($subStatus === 'declined');
                    $isApproved = ($subStatus === 'approved' && $monStatus === 'normal');

                    // Card border/style
                    $cardClass = '';
                    $pillClass = 'pill-normal';
                    $pillLabel = 'Normal';
                    if ($isPending) {
                        $cardClass = 'card-pending'; $pillClass = 'pill-pending'; $pillLabel = 'Under Review';
                    } elseif ($isDeclined) {
                        $cardClass = 'card-declined'; $pillClass = 'pill-declined'; $pillLabel = 'Update Declined';
                    } elseif ($isApproved) {
                        $cardClass = 'card-approved'; $pillClass = 'pill-improved'; $pillLabel = 'Complied';
                    } elseif ($monStatus === 'warning') {
                        $cardClass = 'card-warning'; $pillClass = 'pill-warning'; $pillLabel = 'Warning';
                    } elseif ($monStatus === 'under_renovation') {
                        $cardClass = 'card-warning'; $pillClass = 'pill-warning'; $pillLabel = 'Under Renovation';
                    } elseif ($monStatus === 'improved') {
                        $cardClass = 'card-approved'; $pillClass = 'pill-improved'; $pillLabel = 'Improved';
                    }

                    // Owner can submit when warning/renovation/declined but NOT when already pending
                    $canSubmit = in_array($monStatus, ['warning', 'under_renovation', 'improved'], true) && !$isPending;

                    // Existing proof files
                    $proofFiles = [];
                    if (!empty($spot['renovation_proof_path'])) {
                        $proofFiles = array_values(array_filter(array_map('trim', explode('|', (string)$spot['renovation_proof_path']))));
                    }
                ?>

                <div class="monitor-card <?= $cardClass ?>">

                    <!-- Card header: name + pill -->
                    <div class="monitor-card-top">
                        <div>
                            <h2><?= htmlspecialchars($spot['name'] ?? '') ?></h2>
                            <div class="monitor-meta">
                                <span><strong>Category:</strong> <?= htmlspecialchars($spot['category_name'] ?? 'N/A') ?></span>
                                <span><strong>Barangay:</strong> <?= htmlspecialchars($spot['barangay'] ?? 'N/A') ?></span>
                                <span><strong>Rating:</strong> <?= !empty($spot['avg_rating']) ? number_format((float)$spot['avg_rating'], 1) . ' / 5' : 'No ratings yet' ?></span>
                            </div>
                        </div>
                        <span class="status-pill <?= $pillClass ?>">
                            <?php if ($isPending): ?>
                                <i class="fa-solid fa-hourglass-half"></i>
                            <?php elseif ($isDeclined): ?>
                                <i class="fa-solid fa-xmark"></i>
                            <?php elseif ($isApproved || $monStatus === 'improved'): ?>
                                <i class="fa-solid fa-check"></i>
                            <?php elseif ($monStatus === 'warning' || $monStatus === 'under_renovation'): ?>
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            <?php endif; ?>
                            <?= $pillLabel ?>
                        </span>
                    </div>

                    <!-- Deadline countdown -->
                    <?php if (!empty($deadline) && !$isApproved && $monStatus !== 'normal'): ?>
                        <?php
                            $daysLeft = (int)ceil((strtotime($deadline) - time()) / 86400);
                            $urgentCls = $daysLeft <= 30 ? 'deadline-urgent' : '';
                        ?>
                        <div class="deadline-bar <?= $urgentCls ?>">
                            <i class="fa-regular fa-clock"></i>
                            <span>
                                Improvement deadline: <strong><?= date('M d, Y', strtotime($deadline)) ?></strong>
                                &mdash;
                                <?php if ($daysLeft > 0): ?>
                                    <strong><?= $daysLeft ?> day<?= $daysLeft !== 1 ? 's' : '' ?> remaining</strong>
                                <?php else: ?>
                                    <strong style="color:#dc2626;">Deadline passed</strong>
                                <?php endif; ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <!-- LGU Warning Notice -->
                    <?php if ($monitorNote !== '' && !$isApproved): ?>
                        <div class="notice-box notice-warning">
                            <div class="notice-label lbl-warning">
                                <i class="fa-solid fa-triangle-exclamation"></i> LGU Warning Notice
                            </div>
                            <p class="notice-text txt-warning"><?= nl2br(htmlspecialchars($monitorNote)) ?></p>
                        </div>
                    <?php elseif (in_array($monStatus, ['warning', 'under_renovation'], true) && !$isPending && !$isApproved && $monitorNote === ''): ?>
                        <div class="notice-box notice-warning">
                            <div class="notice-label lbl-warning">
                                <i class="fa-solid fa-triangle-exclamation"></i> LGU Monitoring Notice
                            </div>
                            <p class="notice-text txt-warning">The LGU has flagged this listing for quality concerns. Please submit an improvement update with a description and proof of work done.</p>
                        </div>
                    <?php endif; ?>

                    <!-- Declined Feedback -->
                    <?php if ($isDeclined): ?>
                        <div class="notice-box notice-declined">
                            <div class="notice-label lbl-declined">
                                <i class="fa-solid fa-circle-xmark"></i> Update Declined &mdash; Action Required
                            </div>
                            <?php if ($declineNote !== ''): ?>
                                <p class="notice-text txt-declined"><?= nl2br(htmlspecialchars($declineNote)) ?></p>
                            <?php else: ?>
                                <p class="notice-text txt-declined">Your previous improvement update was insufficient. Please resubmit with clearer evidence and a more detailed description.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Pending Review -->
                    <?php if ($isPending): ?>
                        <div class="notice-box notice-pending">
                            <div class="notice-label lbl-pending">
                                <i class="fa-solid fa-hourglass-half"></i> Update Submitted &mdash; Awaiting LGU Review
                            </div>
                            <p class="notice-text txt-pending">Your improvement update has been submitted and is being reviewed by the LGU admin. You will receive an email notification once a decision is made.</p>
                        </div>
                    <?php endif; ?>

                    <!-- Approved -->
                    <?php if ($isApproved): ?>
                        <div class="notice-box notice-approved">
                            <div class="notice-label lbl-approved">
                                <i class="fa-solid fa-circle-check"></i> Improvement Approved &mdash; Complied
                            </div>
                            <p class="notice-text txt-approved">Congratulations! The LGU has verified and approved your improvement update. Your listing status has been restored to normal.</p>
                        </div>
                    <?php endif; ?>

                    <!-- Existing submitted proof images -->
                    <?php if (!empty($proofFiles)): ?>
                        <div class="proof-section">
                            <div class="proof-section-label">
                                <i class="fa-solid fa-images"></i>
                                Previously Submitted Proof &mdash; <?= count($proofFiles) ?> image<?= count($proofFiles) !== 1 ? 's' : '' ?>
                            </div>
                            <div class="proof-grid">
                                <?php foreach ($proofFiles as $pf): ?>
                                    <div class="proof-thumb">
                                        <a href="<?= BASE_URL ?>/<?= htmlspecialchars($pf) ?>" target="_blank" title="View full image">
                                            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($pf) ?>" alt="Renovation proof" loading="lazy">
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if ($renovNote !== ''): ?>
                                <p style="margin: 0.6rem 0 0; font-size: 0.85rem; color: var(--text-muted);">
                                    <strong>Description submitted:</strong>
                                    <?= htmlspecialchars(mb_strlen($renovNote) > 200 ? mb_substr($renovNote, 0, 197) . '…' : $renovNote) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Submit / Resubmit form -->
                    <?php if ($canSubmit): ?>
                        <div class="submit-section">
                            <h3 class="submit-section-title">
                                <i class="fa-solid fa-paper-plane"></i>
                                <?= $isDeclined ? 'Resubmit Improvement Update' : 'Submit Improvement Update' ?>
                            </h3>

                            <?php if ($isDeclined): ?>
                                <p style="margin: 0 0 1rem; font-size: 0.87rem; color: #9a3412; line-height: 1.6; background: #fff7ed; border: 1px solid #fed7aa; border-radius: 8px; padding: 0.65rem 0.9rem;">
                                    <i class="fa-solid fa-circle-info"></i>
                                    Your previous update was declined. Please address the issues above and resubmit with stronger evidence.
                                </p>
                            <?php endif; ?>

                            <form method="post" action="<?= BASE_URL ?>/business/monitoring/update"
                                  enctype="multipart/form-data" class="form-grid"
                                  id="monitorForm_<?= $spotId ?>">
                                <input type="hidden" name="spot_id" value="<?= $spotId ?>">

                                <div class="field-group">
                                    <label for="upd_status_<?= $spotId ?>">Update Type</label>
                                    <select name="status" id="upd_status_<?= $spotId ?>">
                                        <option value="under_renovation" <?= $monStatus === 'under_renovation' ? 'selected' : '' ?>>🔧 Under Renovation / Work in Progress</option>
                                        <option value="improved"         <?= $monStatus === 'improved'         ? 'selected' : '' ?>>✅ Improvement Completed</option>
                                    </select>
                                </div>

                                <div class="field-group">
                                    <label for="upd_note_<?= $spotId ?>">
                                        Improvement Description <span style="color:#dc2626;">*</span>
                                    </label>
                                    <textarea name="note" id="upd_note_<?= $spotId ?>"
                                        placeholder="Describe in detail the improvements or renovation work done. Include what was changed, fixed, or upgraded, materials used, dates, etc."
                                        required><?= htmlspecialchars($renovNote) ?></textarea>
                                    <span class="field-hint">Be as specific as possible to help the LGU verify your update.</span>
                                </div>

                                <div class="field-group">
                                    <label>Photo Proof of Improvements <span style="font-weight:400;text-transform:none;font-size:0.75rem;">(up to 10 photos, JPG/PNG)</span></label>
                                    <div class="file-dropzone" id="dz_<?= $spotId ?>"
                                         onclick="document.getElementById('upd_proof_<?= $spotId ?>').click()"
                                         ondragover="event.preventDefault();this.classList.add('dragover')"
                                         ondragleave="this.classList.remove('dragover')"
                                         ondrop="handleDrop(event,'upd_proof_<?= $spotId ?>','prev_<?= $spotId ?>','dz_<?= $spotId ?>')">
                                        <i class="fa-solid fa-cloud-arrow-up dz-icon"></i>
                                        <p><strong>Click to upload</strong> or drag &amp; drop photos here</p>
                                        <p style="font-size:0.75rem;margin-top:0.3rem;">JPG, JPEG, PNG &mdash; max 10 images</p>
                                    </div>
                                    <input type="file" name="proofs[]" id="upd_proof_<?= $spotId ?>"
                                           accept=".jpg,.jpeg,.png,image/jpeg,image/png" multiple
                                           style="display:none;"
                                           onchange="previewFiles(this,'prev_<?= $spotId ?>','dz_<?= $spotId ?>')">
                                    <div class="upload-preview-grid" id="prev_<?= $spotId ?>"></div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa-solid fa-paper-plane"></i>
                                        <?= $isDeclined ? 'Resubmit Update' : 'Submit Improvement Update' ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="spacer"></div>
                    <?php endif; ?>

                </div><!-- .monitor-card -->
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>

<script>
function previewFiles(input, gridId, dzId) {
    var grid = document.getElementById(gridId);
    var dz   = document.getElementById(dzId);
    if (!grid) return;
    var files = Array.from(input.files);
    if (files.length > 10) {
        alert('You can upload a maximum of 10 photos.');
        input.value = '';
        grid.innerHTML = '';
        return;
    }
    grid.innerHTML = '';
    files.forEach(function(file) {
        if (!file.type.match(/image\/(jpeg|png)/)) return;
        var reader = new FileReader();
        reader.onload = function(e) {
            var thumb = document.createElement('div');
            thumb.className = 'file-preview-thumb';
            thumb.innerHTML = '<img src="' + e.target.result + '" alt="Preview">'
                + '<button type="button" class="remove-btn" title="Remove"'
                + ' onclick="clearFiles(\'' + input.id + '\',\'' + gridId + '\',\'' + dzId + '\')">'
                + '<i class="fa-solid fa-xmark"></i></button>';
            grid.appendChild(thumb);
        };
        reader.readAsDataURL(file);
    });
    if (files.length && dz) {
        dz.style.borderColor = 'var(--secondary-color)';
        dz.style.background  = '#f0fdf4';
    }
}

function clearFiles(inputId, gridId, dzId) {
    var inp = document.getElementById(inputId);
    var gr  = document.getElementById(gridId);
    var dz  = document.getElementById(dzId);
    if (inp) inp.value = '';
    if (gr)  gr.innerHTML = '';
    if (dz)  { dz.style.borderColor = ''; dz.style.background = ''; }
}

function handleDrop(e, inputId, gridId, dzId) {
    e.preventDefault();
    var dz = document.getElementById(dzId);
    if (dz) dz.classList.remove('dragover');
    var inp = document.getElementById(inputId);
    if (!inp || !e.dataTransfer || !e.dataTransfer.files.length) return;
    try { inp.files = e.dataTransfer.files; } catch(ex) {}
    previewFiles(inp, gridId, dzId);
}
</script>

<?php require_once '../app/views/partials/business-footer.php'; ?>