<?php
$title = 'Review Moderation - LGU';
$activePage = 'reviews';
require_once '../app/views/partials/lgu-header.php';
?>

<style>
    :root {
        --lgu-bg: #edf0ee;
        --lgu-panel: #ffffff;
        --lgu-panel-soft: #f6f8f7;
        --lgu-border: #dfe7e1;
        --lgu-text: #24332d;
        --lgu-subtle: #67766f;
        --lgu-green: #1f6d55;
        --lgu-green-dark: #15513d;
        --lgu-gold: #f5b93c;
        --lgu-gold-soft: #fff4d7;
        --lgu-gold-dark: #8a5a08;
        --lgu-red: #b63e35;
        --lgu-red-soft: #f8d9d6;
    }

    .review-queue-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
        margin: 0 0 1.2rem;
    }

    .review-queue-heading h1 {
        margin: 0;
        color: var(--lgu-text);
        font-size: clamp(2rem, 2.5vw, 2.75rem);
        line-height: 1.1;
        letter-spacing: -0.04em;
        font-weight: 800;
    }

    .review-queue-heading p {
        margin: 0.35rem 0 0;
        color: var(--lgu-subtle);
        font-size: 0.95rem;
    }

    .review-queue-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 72px;
        padding: 0.42rem 0.7rem;
        border-radius: 999px;
        background: var(--lgu-gold-soft);
        color: var(--lgu-gold-dark);
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        box-shadow: inset 0 0 0 1px rgba(180, 132, 38, 0.2);
    }

    .review-queue-list {
        display: grid;
        gap: 1.2rem;
    }

    .review-queue-card {
        min-width: 0;
        padding: 1.3rem 1.4rem 1.2rem;
        border: 1px solid var(--lgu-border);
        border-radius: 14px;
        background: var(--lgu-panel);
        box-shadow: 0 8px 18px rgba(20, 35, 27, 0.04);
    }

    .review-queue-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.9rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #edf0ee;
    }

    .review-queue-card-header h2 {
        margin: 0;
        color: var(--lgu-text);
        font-size: 1.1rem;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .review-queue-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.2rem 0.7rem;
        margin-top: 0.4rem;
        color: var(--lgu-subtle);
        font-size: 0.78rem;
    }

    .review-queue-pending {
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 82px;
        padding: 0.35rem 0.6rem;
        border-radius: 999px;
        background: var(--lgu-gold-soft);
        color: var(--lgu-gold-dark);
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .review-queue-rating {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin: 1rem 0 0.7rem;
        color: #e3ae35;
        font-weight: 700;
    }

    .review-queue-stars {
        display: inline-flex;
        align-items: center;
        gap: 0.14rem;
        font-size: 1.05rem;
        letter-spacing: 0.04em;
    }

    .review-queue-score {
        color: var(--lgu-text);
        font-size: 0.88rem;
        font-weight: 800;
    }

    .review-queue-comment {
        margin: 0;
        color: #30453f;
        font-size: 0.96rem;
        line-height: 1.65;
        overflow-wrap: anywhere;
        white-space: pre-wrap;
    }

    .review-queue-photos {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .review-queue-photos a {
        display: block;
        width: 96px;
        height: 96px;
        overflow: hidden;
        border: 1px solid var(--lgu-border);
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(36, 51, 45, 0.06);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .review-queue-photos a:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(36, 51, 45, 0.1);
    }

    .review-queue-photos img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .review-queue-actions {
        display: flex;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 0.85rem;
        margin-top: 1.15rem;
        padding-top: 1rem;
        border-top: 1px solid #edf0ee;
    }

    .review-queue-actions form {
        display: flex;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 0.7rem;
        margin: 0;
    }

    .review-queue-actions textarea {
        width: min(100%, 360px);
        min-height: 44px;
        padding: 0.75rem 0.85rem;
        border: 1px solid #d1d9d4;
        border-radius: 10px;
        background: #fff;
        color: var(--lgu-text);
        font: inherit;
        font-size: 0.82rem;
        resize: vertical;
        box-shadow: inset 0 1px 2px rgba(16, 24, 40, 0.02);
    }

    .review-queue-actions textarea:focus {
        outline: 2px solid rgba(31, 109, 85, 0.18);
        border-color: rgba(31, 109, 85, 0.4);
    }

    .review-decision-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        min-height: 44px;
        padding: 0.8rem 1.2rem;
        border: 0;
        border-radius: 10px;
        color: #fff;
        cursor: pointer;
        font: inherit;
        font-size: 0.95rem;
        font-weight: 800;
        transition: transform 0.12s ease, box-shadow 0.12s ease, filter 0.12s ease;
    }

    .review-decision-button:hover {
        transform: translateY(-1px);
        filter: brightness(0.98);
    }

    .review-decision-approve {
        background: linear-gradient(180deg, var(--lgu-green), var(--lgu-green-dark));
        box-shadow: 0 8px 16px rgba(21, 81, 61, 0.18);
    }

    .review-decision-reject {
        background: linear-gradient(180deg, #c24d43, var(--lgu-red));
        box-shadow: 0 8px 16px rgba(166, 61, 47, 0.18);
    }

    .review-queue-empty {
        padding: 2.5rem 1.25rem;
        border: 1px dashed #cbd8d1;
        border-radius: 14px;
        background: var(--lgu-panel);
        color: #586962;
        text-align: center;
        box-shadow: 0 8px 18px rgba(20, 35, 27, 0.03);
    }

    .review-queue-empty i {
        display: block;
        margin-bottom: 0.65rem;
        color: var(--lgu-green);
        font-size: 1.5rem;
    }

    @media (max-width: 600px) {
        .review-queue-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .review-queue-card {
            padding: 1rem;
        }

        .review-queue-card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .review-queue-actions,
        .review-queue-actions form {
            align-items: stretch;
            flex-direction: column;
        }

        .review-queue-actions form,
        .review-queue-actions textarea,
        .review-decision-button {
            width: 100%;
        }
    }
</style>

<div class="lgu-topbar">
    <div>
        <div class="lgu-topbar-title">Review Moderation</div>
        <div class="lgu-topbar-breadcrumb">Approve or reject tourist reviews before publication</div>
    </div>
</div>

<div class="lgu-page-body">
    <div class="review-queue-heading">
        <div>
            <h1>Pending reviews</h1>
            <p>Check each rating, comment, and photo before it appears publicly.</p>
        </div>
        <span class="review-queue-count"><?= count($pendingReviews) ?> pending</span>
    </div>

    <?php if (empty($pendingReviews)): ?>
        <div class="review-queue-empty">
            <i class="fa-regular fa-circle-check" aria-hidden="true"></i>
            No reviews are waiting for approval.
        </div>
    <?php else: ?>
        <div class="review-queue-list">
            <?php foreach ($pendingReviews as $review): ?>
                <article class="review-queue-card">
                    <header class="review-queue-card-header">
                        <div>
                            <h2><?= htmlspecialchars($review['spot_name']) ?></h2>
                            <div class="review-queue-meta">
                                <span>By <?= htmlspecialchars($review['reviewer_name'] ?: 'Anonymous tourist') ?></span>
                                <span><?= date('M j, Y · g:i a', strtotime($review['created_at'])) ?></span>
                            </div>
                        </div>
                        <span class="review-queue-pending">PENDING</span>
                    </header>

                    <div class="review-queue-rating" aria-label="<?= (int)$review['rating'] ?> out of 5 stars">
                        <span class="review-queue-stars" aria-hidden="true">
                            <?php for ($star = 1; $star <= 5; $star++): ?>
                                <i class="<?= $star <= (int)$review['rating'] ? 'fa-solid' : 'fa-regular' ?> fa-star" <?= $star > (int)$review['rating'] ? 'style="color:#d8dfdb;"' : '' ?>></i>
                            <?php endfor; ?>
                        </span>
                        <span class="review-queue-score"><?= (int)$review['rating'] ?>/5</span>
                    </div>

                    <p class="review-queue-comment"><?= htmlspecialchars($review['comment']) ?></p>

                    <?php if (!empty($review['photos'])): ?>
                        <div class="review-queue-photos" aria-label="Uploaded review photos">
                            <?php foreach ($review['photos'] as $photo): ?>
                                <a href="<?= BASE_URL ?>/<?= htmlspecialchars($photo) ?>" target="_blank" rel="noopener">
                                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($photo) ?>" alt="Photo submitted with review">
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="review-queue-actions">
                        <form method="POST" action="<?= BASE_URL ?>/lgu/reviews/<?= (int)$review['id'] ?>/moderate">
                            <input type="hidden" name="decision" value="approved">
                            <button type="submit" class="review-decision-button review-decision-approve">
                                <i class="fa-solid fa-check" aria-hidden="true"></i> Approve and publish
                            </button>
                        </form>
                        <form method="POST" action="<?= BASE_URL ?>/lgu/reviews/<?= (int)$review['id'] ?>/moderate">
                            <input type="hidden" name="decision" value="rejected">
                            <textarea name="moderation_note" aria-label="Optional rejection note" placeholder="Optional note for the record"></textarea>
                            <button type="submit" class="review-decision-button review-decision-reject">
                                <i class="fa-solid fa-xmark" aria-hidden="true"></i> Reject
                            </button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../app/views/partials/lgu-footer.php'; ?>
