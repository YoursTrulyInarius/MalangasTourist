<?php 
$is_modal = isset($_GET['modal']) && $_GET['modal'] == '1';
if ($is_modal) {
    $hide_navbar = true;
    $hide_footer = true;
}
require_once '../app/views/partials/header.php'; 
?>

<style>
/* ── Spot Details Enhanced ── */
.spot-detail-wrap {
    max-width: 900px;
    margin: 0 auto;
    background: #fff;
    border-radius: <?= $is_modal ? '0' : '1.25rem' ?>;
    overflow: hidden;
    box-shadow: <?= $is_modal ? 'none' : '0 20px 60px rgba(0,0,0,0.10)' ?>;
}

/* Photo carousel */
.spot-carousel {
    position: relative;
    width: 100%;
    height: <?= $is_modal ? '280px' : '380px' ?>;
    overflow: hidden;
    background: #e2ebe5;
}

.spot-carousel img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: opacity 0.6s ease;
}

.carousel-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0,0,0,0.45);
    backdrop-filter: blur(4px);
    color: white;
    border: none;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    transition: background 0.2s;
    font-size: 0.9rem;
}
.carousel-btn:hover { background: rgba(0,0,0,0.7); }
.carousel-btn.prev { left: 12px; }
.carousel-btn.next { right: 12px; }

/* Dot indicators */
.carousel-dots {
    position: absolute;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 6px;
    z-index: 10;
}
.carousel-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: rgba(255,255,255,0.5);
    border: none;
    cursor: pointer;
    transition: background 0.2s, transform 0.2s;
    padding: 0;
}
.carousel-dot.active {
    background: #fff;
    transform: scale(1.3);
}

/* Body */
.spot-body {
    padding: 1.75rem 2rem;
}

/* Title row */
.spot-title-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.spot-name {
    font-size: 2rem;
    font-weight: 900;
    color: var(--primary-dark);
    margin: 0 0 0.6rem 0;
    line-height: 1.15;
}

.spot-badges {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    flex-wrap: wrap;
    margin-bottom: 0.75rem;
}

.badge-category {
    background: var(--primary-color);
    color: white;
    padding: 0.3rem 0.85rem;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}

.badge-status {
    padding: 0.3rem 0.85rem;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}

.spot-rating {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1rem;
    color: #fbbf24;
    margin-bottom: 0;
}

.spot-rating-text {
    color: var(--text-muted);
    font-size: 0.85rem;
}

/* Description */
.spot-description {
    font-size: 1rem;
    line-height: 1.75;
    color: #374151;
    margin-bottom: 1.75rem;
    padding: 1rem 1.25rem;
    background: #f8faf9;
    border-left: 3px solid var(--primary-color);
    border-radius: 0 0.5rem 0.5rem 0;
}

/* Info grid */
.spot-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    gap: 1rem;
    margin-bottom: 1.75rem;
}

.info-card {
    background: #f8faf9;
    border: 1px solid #e2ebe5;
    border-radius: 0.85rem;
    padding: 1rem 1.1rem;
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
    transition: box-shadow 0.2s;
}
.info-card:hover { box-shadow: 0 4px 14px rgba(26,77,46,0.08); }

.info-card-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: linear-gradient(135deg, var(--primary-color), #1a5c35);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    margin-bottom: 0.4rem;
}

.info-card-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-muted);
}

.info-card-value {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--primary-dark);
}

/* Map section */
.spot-map-section {
    margin-bottom: 2rem;
}

.spot-map-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    margin-bottom: 0.85rem;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--primary-dark);
    margin-bottom: 0;
}

.section-title i {
    color: var(--primary-color);
    font-size: 1rem;
}

#mini-map {
    height: 300px;
    width: 100%;
    border-radius: 0.85rem;
    border: 1.5px solid #d4e4d9;
    overflow: hidden;
}

.spot-nearby-panel {
    margin-top: 1rem;
    padding-top: 0.85rem;
    border-top: 1px solid #e1e9e5;
}

.spot-nearby-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
}

.spot-nearby-heading h3 {
    margin: 0;
    color: #173d35;
    font-size: 0.95rem;
}

.spot-nearby-heading p {
    margin: 0.2rem 0 0;
    color: #64746f;
    font-size: 0.72rem;
}

.spot-nearby-refresh {
    display: grid;
    flex: 0 0 38px;
    place-items: center;
    width: 38px;
    height: 38px;
    border: 1px solid #cbdad5;
    border-radius: 5px;
    background: #f4f8f6;
    color: #176354;
    cursor: pointer;
}

.spot-nearby-refresh:disabled {
    cursor: wait;
    opacity: 0.6;
}

.spot-nearby-div-icon {
    border: 0;
    background: transparent;
}

.spot-nearby-map-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border: 2px solid #fff;
    border-radius: 50%;
    background: var(--spot-nearby-color, #176354);
    box-shadow: 0 3px 8px rgba(18, 42, 34, 0.4);
    color: #fff;
}

.spot-nearby-map-icon i {
    font-size: 14px;
    line-height: 1;
    color: #fff;
}

.spot-nearby-type-icon,
.spot-nearby-category-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    width: 1.55rem;
    height: 1.55rem;
    border-radius: 50%;
    background: var(--spot-nearby-color, #176354);
    color: #fff;
}

.spot-nearby-type-icon i,
.spot-nearby-category-icon i {
    font-size: 0.82rem;
    line-height: 1;
    color: #fff;
}

.spot-nearby-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem 0.85rem;
    margin: 0.7rem 0;
}

.spot-nearby-filters label {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    color: #35574f;
    font-size: 0.76rem;
}

.spot-nearby-filters input {
    accent-color: #176354;
}

.spot-nearby-status {
    margin: 0.45rem 0;
    color: #64746f;
    font-size: 0.76rem;
    line-height: 1.4;
}

.spot-nearby-results {
    display: grid;
    max-height: 250px;
    margin: 0;
    padding: 0;
    overflow-y: auto;
    list-style: none;
}

.spot-nearby-results li {
    min-width: 0;
    border-bottom: 1px solid #e8efeb;
}

.spot-nearby-result {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    width: 100%;
    min-height: 48px;
    padding: 0.5rem 0.35rem;
    border: 0;
    background: transparent;
    color: #203b35;
    cursor: pointer;
    text-align: left;
}

.spot-nearby-result:hover {
    background: #f5f9f6;
}

.spot-nearby-result-name,
.spot-nearby-result-category {
    display: block;
}

.spot-nearby-result-name {
    overflow-wrap: anywhere;
    font-size: 0.8rem;
    font-weight: 700;
}

.spot-nearby-result-category {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    margin-top: 0.1rem;
    color: #64746f;
    font-size: 0.7rem;
}

.spot-nearby-category-icon {
    color: var(--spot-nearby-color, #176354);
}

.spot-nearby-distance {
    flex: 0 0 auto;
    color: #526760;
    font-size: 0.74rem;
    font-weight: 700;
}

.spot-nearby-note {
    margin: 0.45rem 0 0;
    color: #6b7c76;
    font-size: 0.68rem;
    line-height: 1.4;
}

.spot-map-address {
    margin: -0.35rem 0 0.75rem;
    color: #526760;
    font-size: 0.86rem;
}

.spot-route-panel {
    margin-top: 0.75rem;
}

.spot-directions-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    min-height: 38px;
    padding: 0.5rem 0.8rem;
    border: 0;
    border-radius: 5px;
    background: var(--primary-color);
    color: #fff;
    cursor: pointer;
    font: inherit;
    font-size: 0.82rem;
    font-weight: 700;
}

.spot-directions-button:disabled {
    cursor: wait;
    opacity: 0.65;
}

.spot-map-status {
    margin: 0;
    color: #526760;
    font-size: 0.82rem;
    line-height: 1.45;
}

.spot-map-status:empty {
    display: none;
}

.spot-route-options {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.65rem;
    margin-top: 0.65rem;
}

.spot-route-options[hidden] {
    display: none;
}

.spot-route-card {
    display: grid;
    grid-template-columns: 24px minmax(0, 1fr);
    align-items: center;
    gap: 0.25rem 0.55rem;
    min-width: 0;
    padding: 0.7rem;
    border: 1px solid #d4e4d9;
    border-radius: 5px;
    background: #fff;
    color: var(--primary-dark);
    cursor: pointer;
    text-align: left;
}

.spot-route-card[aria-pressed="true"] {
    border-color: var(--primary-color);
    background: #f2f8f4;
}

.spot-route-swatch {
    display: grid;
    grid-row: span 2;
    place-items: center;
    color: var(--primary-color);
    font-size: 1rem;
}

.spot-route-card-label,
.spot-route-card-detail,
.spot-route-card-time {
    display: block;
}

.spot-route-card-label {
    font-size: 0.8rem;
    font-weight: 700;
}

.spot-route-card-detail {
    margin-top: 0.1rem;
    color: #64746f;
    font-size: 0.68rem;
}

.spot-route-card-time {
    grid-column: 2;
    margin-top: 0.15rem;
    color: #173d35;
    font-size: 1rem;
    font-weight: 700;
}

.spot-trip-summary {
    display: grid;
    gap: 0;
    margin: 0.65rem 0;
    padding: 0.75rem;
    border: 1px solid #dce8e2;
    border-radius: 5px;
    background: #f5f9f6;
}

.spot-trip-summary[hidden] {
    display: none;
}

.spot-trip-point {
    display: grid;
    grid-template-columns: 28px minmax(0, 1fr);
    align-items: center;
    gap: 0.6rem;
}

.spot-trip-icon {
    display: grid;
    place-items: center;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #e8f1ed;
    color: #176354;
    font-size: 0.8rem;
}

.spot-trip-icon-start {
    background: #e8f1fb;
    color: #2172b8;
}

.spot-trip-point small,
.spot-trip-point strong {
    display: block;
}

.spot-trip-point small {
    margin-bottom: 0.2rem;
    color: #64746f;
    font-size: 0.65rem;
    font-weight: 700;
}

.spot-trip-point strong {
    color: #173d35;
    font-size: 0.78rem;
    line-height: 1.35;
    overflow-wrap: anywhere;
}

.spot-trip-connector {
    height: 15px;
    margin-left: 12px;
    border-left: 1px solid #aebfb6;
}

.spot-route-help {
    margin: 0.55rem 0 0;
    color: #526760;
    font-size: 0.78rem;
    line-height: 1.45;
}

.spot-route-guidance[hidden] {
    display: none;
}

.spot-route-note {
    margin: 0.5rem 0 0;
    color: #6b7c76;
    font-size: 0.7rem;
    line-height: 1.4;
}

.spot-live-controls {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.6rem 0.8rem;
    margin-top: 0.8rem;
    padding-top: 0.75rem;
    border-top: 1px solid #e1e9e5;
}

.spot-live-controls[hidden] {
    display: none;
}

.spot-live-toggle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.45rem;
    min-height: 40px;
    padding: 0.55rem 0.8rem;
    border: 1px solid #176354;
    border-radius: 5px;
    background: #176354;
    color: #fff;
    cursor: pointer;
    font: inherit;
    font-size: 0.82rem;
    font-weight: 700;
}

.spot-live-toggle[aria-pressed="true"] {
    border-color: #8c3e2b;
    background: #8c3e2b;
}

.spot-live-status {
    flex: 1 1 200px;
    margin: 0;
    color: #526760;
    font-size: 0.76rem;
    line-height: 1.4;
}

@media (max-width: 560px) {
    #mini-map {
        height: clamp(220px, 42vw, 280px);
    }

    .spot-route-options {
        grid-template-columns: 1fr;
    }

    .spot-map-heading {
        align-items: flex-start;
    }

    .spot-live-controls {
        align-items: stretch;
        flex-direction: column;
    }

    .spot-nearby-results {
        max-height: 180px;
    }
}

@media (max-width: 360px) {
    .spot-map-heading {
        align-items: stretch;
        flex-wrap: wrap;
    }

    .spot-map-heading .section-title {
        min-height: 38px;
    }

    #spot-view-location {
        flex: 1 1 100%;
        width: 100%;
    }

    .spot-nearby-filters {
        gap: 0.5rem 0.65rem;
    }
}

/* Reviews section */
.reviews-section {
    border-top: 2px solid #f1f5f9;
    padding-top: 1.75rem;
}

.review-form-box {
    background: linear-gradient(135deg, #f8faf9 0%, #f1f7f2 100%);
    padding: 1.5rem;
    border-radius: 0.85rem;
    margin-bottom: 1.75rem;
    border: 1px solid #d4e4d9;
}

.review-card {
    padding: 1.1rem 0;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    gap: 0.9rem;
    align-items: flex-start;
}

.review-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    font-weight: 800;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.review-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.3rem;
}

.review-author {
    font-weight: 700;
    color: #1e293b;
    font-size: 0.92rem;
}

.review-date {
    color: #94a3b8;
    font-size: 0.78rem;
}

.review-stars { color: #fbbf24; font-size: 0.82rem; margin-bottom: 0.4rem; }

.review-edit-btn {
    border: none;
    background: transparent;
    color: var(--primary-color);
    font-weight: 700;
    font-size: 0.8rem;
    cursor: pointer;
    padding: 0;
}

.review-edit-form {
    display: none;
    margin-top: 0.85rem;
    padding: 0.9rem;
    border: 1px solid #dbeafe;
    border-radius: 0.75rem;
    background: #f8fbff;
}

.review-header,
.review-action-row,
.review-promo-box,
.review-edit-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    align-items: center;
}

.review-header {
    margin-bottom: 1rem;
}

.review-error-box {
    margin-bottom: 1rem;
    padding: 0.9rem 1rem;
    border: 1px solid #f87171;
    background: #fef2f2;
    border-radius: 0.75rem;
    color: #b91c1c;
}

.review-file-input {
    width: 100%;
    border: 2px solid #e0f2fe;
    border-radius: 0.65rem;
    padding: 0.75rem;
}

.review-submit-btn,
.review-edit-actions .btn {
    padding: 0.75rem 1.2rem;
    font-weight: 700;
    border-radius: 0.65rem;
}

.review-promo-box {
    background: #f8faf9;
    padding: 1.25rem 1.5rem;
    border-radius: 0.85rem;
    margin-bottom: 1.5rem;
    border: 1px solid #d4e4d9;
    align-items: flex-start;
}

.review-promo-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.review-empty-state {
    text-align: center;
    padding: 2.5rem 1rem;
}

.review-card-content {
    flex: 1;
    min-width: 0;
}

.review-action-row {
    justify-content: space-between;
}

.review-photos {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 0.85rem;
}

.review-photo-img {
    width: calc(50% - 0.375rem);
    max-height: 180px;
    object-fit: cover;
    border-radius: 0.75rem;
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
    cursor: zoom-in;
    transition: transform 0.2s ease;
}

.review-photos {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 0.85rem;
}

.review-photo-img {
    width: calc(50% - 0.375rem);
    max-height: 180px;
    object-fit: cover;
    border-radius: 0.75rem;
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
    cursor: zoom-in;
    transition: transform 0.2s ease;
}
.review-photo-img:hover {
    transform: scale(1.02);
}

.review-lightbox-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 11000;
    background: rgba(15, 23, 42, 0.85);
    align-items: center;
    justify-content: center;
    padding: 1rem;
}
.review-lightbox-overlay.open {
    display: flex;
}
.review-lightbox-content {
    position: relative;
    max-width: 95%;
    max-height: 95%;
    width: 100%;
    display: grid;
    gap: 1rem;
    justify-items: center;
}
.review-lightbox-image {
    max-width: 100%;
    max-height: calc(100vh - 140px);
    object-fit: contain;
    border-radius: 1rem;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
    transform-origin: center center;
    transition: transform 0.2s ease;
}
.review-lightbox-close {
    position: absolute;
    top: -0.65rem;
    right: -0.65rem;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: none;
    background: rgba(255,255,255,0.9);
    color: #0f172a;
    font-size: 1.25rem;
    cursor: pointer;
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}
.review-lightbox-controls {
    display: flex;
    gap: 0.75rem;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
}
.review-lightbox-action {
    min-width: 44px;
    min-height: 44px;
    border: none;
    border-radius: 999px;
    background: rgba(255,255,255,0.15);
    color: white;
    font-size: 1.2rem;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s ease, transform 0.2s ease;
}
.review-lightbox-action:hover {
    background: rgba(255,255,255,0.28);
    transform: translateY(-1px);
}
.review-lightbox-zoom-label {
    color: white;
    font-size: 0.95rem;
    font-weight: 700;
}

/* Star rating input */
.star-rating { display: flex; gap: 0.35rem; flex-direction: row-reverse; justify-content: flex-end; color: #d4d4d8; font-size: 1.75rem; }
.star-rating label { cursor: pointer; transition: transform 0.15s; }
.star-rating label:hover ~ label i,
.star-rating label:hover i,
.star-rating input:checked ~ label i { font-weight: 900; color: #fbbf24; }
.star-rating label:hover { transform: scale(1.15); }

/* Tourist auth modal */
.auth-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    z-index: 10010;
    align-items: flex-start;
    justify-content: center;
    padding: 2rem 1.5rem;
    overflow-y: auto;
    backdrop-filter: blur(4px);
}
.auth-modal-overlay.open {
    display: flex;
}
.auth-modal {
    width: 100%;
    max-width: 520px;
    background: white;
    border-radius: 1.2rem;
    overflow: hidden;
    box-shadow: 0 28px 80px rgba(15, 23, 42, 0.28);
    position: relative;
}
.auth-modal-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    border: none;
    background: rgba(15, 23, 42, 0.05);
    color: #0f172a;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    font-size: 1.1rem;
    cursor: pointer;
}
.auth-modal-header {
    padding: 2rem 2rem 1.5rem;
    background: linear-gradient(135deg, #10301c 0%, #1a4d2e 100%);
    color: white;
}
.auth-modal-brand {
    display: flex;
    align-items: center;
    gap: 0.9rem;
    margin-bottom: 1rem;
}
.auth-modal-brand-logo {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: white;
    display: grid;
    place-items: center;
    color: #1a4d2e;
}
.auth-modal-brand-text h3 {
    margin: 0;
    font-size: 1.35rem;
    font-weight: 800;
}
.auth-modal-brand-text p {
    margin: 0.25rem 0 0;
    font-size: 0.82rem;
    color: #d1fae5;
    letter-spacing: 0.05em;
}
.auth-modal-tabs {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}
.auth-modal-tab {
    flex: 1;
    background: rgba(255,255,255,0.15);
    color: #d1fae5;
    border: none;
    padding: 0.85rem 0.9rem;
    border-radius: 0.85rem;
    cursor: pointer;
    font-weight: 700;
    transition: background 0.2s;
}
.auth-modal-tab.active,
.auth-modal-tab:hover {
    background: rgba(255,255,255,0.32);
}
.auth-modal-body {
    padding: 1.75rem 2rem 2rem;
}
.auth-modal-panel { display: none; }
.auth-modal-panel.active { display: block; }
.auth-modal-panel .form-group { margin-bottom: 1.25rem; }
.auth-modal-panel label { display: block; margin-bottom: 0.45rem; font-weight: 700; color: #0f172a; }
.auth-modal-panel .form-control {
    width: 100%;
    padding: 0.95rem 1rem;
    border-radius: 0.85rem;
    border: 1px solid #d1d5db;
    background: #f8fafc;
    color: #0f172a;
}
.auth-modal-panel .form-control:focus { outline: none; border-color: #1a4d2e; box-shadow: 0 0 0 3px rgba(26, 77, 46, 0.12); }
.auth-modal-submit {
    width: 100%;
    padding: 0.95rem 1rem;
    border: none;
    border-radius: 0.85rem;
    background: #1a4d2e;
    color: white;
    font-weight: 700;
    cursor: pointer;
    transition: transform 0.2s, background 0.2s;
}
.auth-modal-submit:hover { background: #16432a; transform: translateY(-1px); }
.auth-modal-switch {
    margin-top: 1rem;
    text-align: center;
    color: #475569;
    font-size: 0.92rem;
}
.auth-modal-switch a {
    color: #1a4d2e;
    font-weight: 700;
    text-decoration: none;
}
.auth-modal-switch a:hover { text-decoration: underline; }

@media (max-width: 900px) {
    .spot-detail-wrap { width: 100%; box-sizing: border-box; }
    .spot-carousel { height: <?= $is_modal ? '240px' : '320px' ?>; }
    .spot-body { padding: 1.5rem 1.25rem; }
    .spot-info-grid { gap: 0.85rem; }
    .review-lightbox-image { max-height: calc(100vh - 120px); }
}

@media (max-width: 768px) {
    .spot-name { font-size: 1.75rem; }
    .spot-carousel { height: <?= $is_modal ? '220px' : '300px' ?>; }
    .spot-body { padding: 1.4rem 1rem; }
    .spot-description { padding: 0.95rem 1rem; }
    .review-card { flex-direction: column; align-items: stretch; }
    .review-meta { width: 100%; flex-wrap: wrap; gap: 0.5rem; }
    .review-action-row { width: 100%; }
    .review-edit-actions { width: 100%; justify-content: flex-start; }
    .review-form-box { padding: 1.35rem; }
    .review-promo-box { align-items: flex-start; }
    .auth-modal { max-width: 100%; margin: 0 0.75rem; }
    .auth-modal-header { padding: 1.5rem 1.5rem 1.25rem; }
    .auth-modal-body { padding: 1.25rem 1.5rem 1.5rem; }
}

@media (max-width: 600px) {
    .spot-name { font-size: 1.5rem; }
    .spot-body { padding: 1.1rem 0.85rem; }
    .spot-carousel { height: <?= $is_modal ? '200px' : '260px' ?>; }
    .carousel-btn { width: 34px; height: 34px; font-size: 0.85rem; }
    .review-card { gap: 0.75rem; }
    .review-photos { gap: 0.55rem; }
    .review-photo-img { width: 100%; }
    .review-form-box { padding: 1.1rem; }
    .review-promo-box { padding: 1rem 1rem; }
    .review-promo-avatar { width: 36px; height: 36px; }
    .review-empty-state { padding: 1.75rem 0.85rem; }
    .auth-modal-overlay { padding: 1rem 0.75rem; }
    .auth-modal { width: 100%; }
    .auth-modal-header { padding: 1.25rem 1.25rem 1rem; }
    .auth-modal-body { padding: 1rem 1.25rem 1.25rem; }
}

@media (max-width: 640px) {
    .spot-detail-wrap,
    .spot-body,
    .spot-title-row > div,
    .spot-info-grid,
    .info-card,
    .review-form-box,
    .review-card-content {
        min-width: 0;
    }

    .spot-name,
    .info-card-value,
    .spot-map-address,
    .spot-trip-point strong,
    .review-text {
        overflow-wrap: anywhere;
    }

    .spot-info-grid {
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 150px), 1fr));
    }

    .spot-directions-button,
    .spot-nearby-refresh,
    .carousel-btn,
    .spot-live-toggle,
    .review-submit-btn {
        min-height: 44px;
    }

    .spot-directions-button {
        padding-inline: 0.65rem;
    }

    .spot-route-card {
        min-height: 64px;
    }

    .review-file-input {
        min-width: 0;
        font-size: 0.85rem;
    }
}

@media (max-width: 400px) {
    .spot-title-row {
        gap: 0.5rem;
    }

    .spot-name {
        font-size: 1.35rem;
    }

    .spot-body {
        padding-inline: 0.75rem;
    }

    .review-form-box {
        padding: 0.9rem;
    }
}
</style>

<div style="padding-top: <?= $is_modal ? '0' : '2rem' ?>; padding-bottom: <?= $is_modal ? '0' : '4rem' ?>;">
    <?php if (!$is_modal): ?>
    <div class="container" style="margin-bottom: 1rem;">
        <a href="<?= BASE_URL ?>/" class="btn btn-primary" style="background-color: var(--text-muted);">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>
    <?php endif; ?>

    <div class="<?= $is_modal ? '' : 'container' ?>">
        <div class="spot-detail-wrap">

            <!-- Photo Carousel -->
            <?php if (!empty($photos)): ?>
            <div class="spot-carousel" id="spotCarousel">
                <?php foreach($photos as $index => $photo): ?>
                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($photo['file_path']) ?>"
                         class="carousel-img"
                         style="opacity: <?= $index === 0 ? '1' : '0' ?>;"
                         data-index="<?= $index ?>">
                <?php endforeach; ?>
                <?php if (count($photos) > 1): ?>
                <button class="carousel-btn prev" id="prevBtn"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="carousel-btn next" id="nextBtn"><i class="fa-solid fa-chevron-right"></i></button>
                <div class="carousel-dots" id="carouselDots">
                    <?php foreach($photos as $index => $photo): ?>
                        <button class="carousel-dot <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>"></button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Body -->
            <div class="spot-body">

                <!-- Title Row -->
                <div class="spot-title-row">
                    <div>
                        <h1 class="spot-name"><?= htmlspecialchars($spot['name']) ?></h1>
                        <div class="spot-badges">
                            <span class="badge-category">
                                <i class="fa-solid <?= htmlspecialchars($spot['category_icon'] ?? 'fa-map-marker-alt') ?>"></i>
                                <?= htmlspecialchars($spot['category_name']) ?>
                            </span>
                            <?php
                                $monitorStatus = $spot['monitor_status'] ?? 'normal';
                                $opMap = [
                                    'open' => ['Open','#16a34a','#dcfce7'],
                                    'closed' => ['Closed','#dc2626','#fee2e2'],
                                    'temp_closed' => ['Temporarily Closed','#ca8a04','#fef3c7'],
                                    'maintenance' => ['Under Maintenance','#1e40af','#dbeafe'],
                                    'under_renovation' => ['Under Renovation','#1e40af','#dbeafe'],
                                    'warning' => ['Warning','#b45309','#fef3c7'],
                                    'improved' => ['Improved','#0f766e','#ccfbf1'],
                                    'normal' => ['Open','#16a34a','#dcfce7']
                                ];
                                $effectiveStatus = $monitorStatus === 'normal' ? ($spot['operational_status'] ?? 'open') : $monitorStatus;
                                [$opLabel, $opTxt, $opBg] = $opMap[$effectiveStatus] ?? ['Open','#16a34a','#dcfce7'];
                            ?>
                            <span class="badge-status" style="background:<?= $opBg ?>;color:<?= $opTxt ?>;">
                                <i class="fa-solid fa-circle" style="font-size:0.5rem;"></i> <?= $opLabel ?>
                            </span>
                        </div>
                        <div class="spot-rating">
                            <?php
                                $avg = floatval($spot['avg_rating']);
                                for($i=1; $i<=5; $i++) {
                                    if ($i <= $avg) echo '<i class="fa-solid fa-star"></i>';
                                    else if ($i - 0.5 <= $avg) echo '<i class="fa-solid fa-star-half-stroke"></i>';
                                    else echo '<i class="fa-regular fa-star" style="color:#e2e8f0;"></i>';
                                }
                            ?>
                            <span class="spot-rating-text">
                                <?= number_format($avg, 1) ?> (<?= $spot['review_count'] ?> reviews)
                            </span>
                        </div>
                    </div>
                </div><!-- /.spot-title-row -->

                <!-- Description -->
                <?php if (!empty($spot['description'])): ?>
                <p class="spot-description">
                    <?= nl2br(htmlspecialchars($spot['description'])) ?>
                </p>
                <?php endif; ?>

                <!-- Info Cards -->
                <div class="spot-info-grid">
                    <?php if (!empty($spot['barangay'])): ?>
                    <div class="info-card">
                        <div class="info-card-icon"><i class="fa-solid fa-location-dot"></i></div>
                        <span class="info-card-label">Location</span>
                        <span class="info-card-value"><?= htmlspecialchars($spot['barangay']) ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($spot['hours'])): ?>
                    <div class="info-card">
                        <div class="info-card-icon"><i class="fa-solid fa-clock"></i></div>
                        <span class="info-card-label">Operating Hours</span>
                        <span class="info-card-value"><?= htmlspecialchars($spot['hours']) ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($spot['contact_number'])): ?>
                    <div class="info-card">
                        <div class="info-card-icon"><i class="fa-solid fa-phone"></i></div>
                        <span class="info-card-label">Contact Number</span>
                        <span class="info-card-value"><?= htmlspecialchars($spot['contact_number']) ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($spot['price_range'])): ?>
                    <div class="info-card">
                        <div class="info-card-icon"><i class="fa-solid fa-peso-sign"></i></div>
                        <span class="info-card-label">Price Range</span>
                        <span class="info-card-value"><?= htmlspecialchars($spot['price_range']) ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($spot['fb_link'])): ?>
                    <div class="info-card">
                        <div class="info-card-icon" style="background: linear-gradient(135deg,#1877f2,#0c5fc7);"><i class="fa-brands fa-facebook"></i></div>
                        <span class="info-card-label">Social Media</span>
                        <a href="<?= htmlspecialchars($spot['fb_link']) ?>" target="_blank"
                           class="info-card-value" style="color: var(--complementary-color); text-decoration: none;">
                            View Page <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:0.7rem;"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Map -->
                <div class="spot-map-section">
                    <div class="spot-map-heading">
                        <div class="section-title">
                            <i class="fa-solid fa-map-pin"></i> Location Map
                        </div>
                        <button type="button" id="spot-view-location" class="spot-directions-button">
                            <i class="fa-solid fa-location-crosshairs" aria-hidden="true"></i> View Location
                        </button>
                    </div>
                    <p class="spot-map-address">
                        <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                        <?= !empty($spot['barangay']) ? 'Barangay ' . htmlspecialchars($spot['barangay']) . ' · ' : '' ?>
                        <?= number_format((float)$spot['latitude'], 6) ?>, <?= number_format((float)$spot['longitude'], 6) ?>
                    </p>
                    <div id="spot-trip-summary" class="spot-trip-summary" hidden>
                        <div class="spot-trip-point">
                            <span class="spot-trip-icon spot-trip-icon-start" aria-hidden="true"><i class="fa-solid fa-location-crosshairs"></i></span>
                            <div>
                                <small>FROM</small>
                                <strong>Your current location</strong>
                            </div>
                        </div>
                        <div class="spot-trip-connector" aria-hidden="true"></div>
                        <div class="spot-trip-point">
                            <span class="spot-trip-icon" aria-hidden="true"><i class="fa-solid fa-location-dot"></i></span>
                            <div>
                                <small>TO</small>
                                <strong><?= htmlspecialchars($spot['name']) ?><?= !empty($spot['barangay']) ? ', ' . htmlspecialchars($spot['barangay']) : '' ?></strong>
                            </div>
                        </div>
                    </div>
                    <div id="mini-map"></div>
                    <section class="spot-nearby-panel" aria-label="Nearby places at this spot">
                        <div class="spot-nearby-heading">
                            <div>
                                <h3>Near this spot</h3>
                                <p>Places within 3 km, nearest first. Tap a pin for details.</p>
                            </div>
                            <button type="button" id="spot-nearby-refresh" class="spot-nearby-refresh" title="Refresh nearby places" aria-label="Refresh nearby places">
                                <i class="fa-solid fa-rotate" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div class="spot-nearby-filters" aria-label="Filter nearby place types">
                            <label><input type="checkbox" name="spot-nearby-type" value="restroom" checked><span class="spot-nearby-type-icon" style="--spot-nearby-color:#0284c7;"><i class="fa-solid fa-restroom"></i></span> Restrooms</label>
                            <label><input type="checkbox" name="spot-nearby-type" value="food" checked><span class="spot-nearby-type-icon" style="--spot-nearby-color:#ea580c;"><i class="fa-solid fa-utensils"></i></span> Food & Dining</label>
                            <label><input type="checkbox" name="spot-nearby-type" value="stay" checked><span class="spot-nearby-type-icon" style="--spot-nearby-color:#059669;"><i class="fa-solid fa-hotel"></i></span> Stays & Lodging</label>
                            <label><input type="checkbox" name="spot-nearby-type" value="attraction" checked><span class="spot-nearby-type-icon" style="--spot-nearby-color:#d97706;"><i class="fa-solid fa-landmark"></i></span> Attractions</label>
                            <label><input type="checkbox" name="spot-nearby-type" value="other" checked><span class="spot-nearby-type-icon" style="--spot-nearby-color:#7c3aed;"><i class="fa-solid fa-store"></i></span> Other Services</label>
                        </div>
                        <p id="spot-nearby-status" class="spot-nearby-status" role="status" aria-live="polite">Loading places near this spot...</p>
                        <ul id="spot-nearby-results" class="spot-nearby-results" aria-label="Nearby places"></ul>
                        <p class="spot-nearby-note">Data from OpenStreetMap & Malangas Tourism Directory. Distances are straight-line estimates.</p>
                    </section>
                    <div class="spot-route-panel">
                        <p id="spot-map-status" class="spot-map-status" role="status" aria-live="polite">Select View Location to find your current location and routes to this spot.</p>
                        <div id="spot-route-guidance" class="spot-route-guidance" hidden>
                            <p class="spot-route-help">Choose how you plan to travel. Select a route to highlight it on the map.</p>
                            <div id="spot-route-options" class="spot-route-options" hidden></div>
                            <p class="spot-route-note">Live tracking refreshes the selected mode only. Times are estimates without live traffic; motorcycles use the car profile.</p>
                            <div id="spot-live-controls" class="spot-live-controls" hidden>
                                <button type="button" id="spot-live-toggle" class="spot-live-toggle" aria-pressed="false">
                                    <i class="fa-solid fa-stop" aria-hidden="true"></i> Stop live tracking
                                </button>
                                <p id="spot-live-status" class="spot-live-status" role="status" aria-live="polite">Keep this page open while tracking is active.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reviews -->
                <div class="reviews-section">
                    <div class="section-title" style="margin-bottom: 1.25rem;">
                        <i class="fa-solid fa-comments"></i> Reviews
                        <span style="font-size: 0.85rem; font-weight: 500; color: var(--text-muted); margin-left: 0.25rem;">(<?= $spot['review_count'] ?>)</span>
                    </div>

                    <div id="reviewSection" class="review-form-box">
                        <div class="review-header">
                            <i class="fa-solid fa-pen-to-square" style="color:var(--primary-color);"></i>
                            <h4 style="margin:0; font-size:0.95rem; color:var(--primary-dark);">Leave a Review</h4>
                        </div>
                        <?php if (isset($_SESSION['review_error'])): ?>
                            <div class="review-error-box">
                                <?= htmlspecialchars($_SESSION['review_error']) ?>
                            </div>
                            <?php unset($_SESSION['review_error']); ?>
                        <?php endif; ?>
                        <form action="<?= BASE_URL ?>/api/submit-review" method="POST" enctype="multipart/form-data" id="reviewForm">
                            <input type="hidden" name="spot_id" value="<?= $spot['id'] ?>">
                            <input type="hidden" name="modal" value="<?= $is_modal ? '1' : '0' ?>">
                            <div style="margin-bottom:1rem;">
                                <label style="display:block; font-size:0.82rem; font-weight:600; color:#475569; margin-bottom:0.4rem;">Your Rating</label>
                                <div class="star-rating">
                                    <input type="radio" id="star5" name="rating" value="5" style="display:none;" required><label for="star5"><i class="fa-regular fa-star"></i></label>
                                    <input type="radio" id="star4" name="rating" value="4" style="display:none;"><label for="star4"><i class="fa-regular fa-star"></i></label>
                                    <input type="radio" id="star3" name="rating" value="3" style="display:none;"><label for="star3"><i class="fa-regular fa-star"></i></label>
                                    <input type="radio" id="star2" name="rating" value="2" style="display:none;"><label for="star2"><i class="fa-regular fa-star"></i></label>
                                    <input type="radio" id="star1" name="rating" value="1" style="display:none;"><label for="star1"><i class="fa-regular fa-star"></i></label>
                                </div>
                            </div>
                            <div style="margin-bottom:1rem;">
                                <label for="comment" style="display:block; font-size:0.82rem; font-weight:600; color:#475569; margin-bottom:0.4rem;">Your Comment</label>
                                <textarea id="comment" name="comment" class="form-control" rows="3" required
                                    placeholder="Share your experience about this place..."
                                    style="resize:vertical; border-radius:0.65rem;"></textarea>
                            </div>
                            <div style="margin-bottom:1rem;">
                                <label for="review_photos" style="display:block; font-size:0.82rem; font-weight:600; color:#475569; margin-bottom:0.4rem;">Add Photos (required, up to 3)</label>
                                <input type="file" id="review_photos" name="review_photos[]" class="form-control review-file-input" accept="image/*" multiple required>
                                <p style="margin:0.5rem 0 0; font-size:0.8rem; color:#64748b;">Please upload at least 1 image with your review. You may upload up to 3 images.</p>
                            </div>
                            <button type="submit" class="btn btn-primary review-submit-btn">
                                <i class="fa-solid fa-paper-plane" style="margin-right:0.4rem;"></i> Submit Review
                            </button>
                        </form>
                    </div>

                    <!-- Review list -->
                    <?php if (empty($reviews)): ?>
                    <div style="text-align:center; padding:2.5rem 1rem;">
                        <i class="fa-regular fa-comment-dots" style="font-size:2.5rem; color:#cbd5e1; display:block; margin-bottom:0.75rem;"></i>
                        <p style="color:#94a3b8; font-size:0.95rem; margin:0; font-weight:600;">No reviews yet</p>
                        <p style="color:#cbd5e1; font-size:0.82rem; margin-top:0.25rem;">Be the first to share your experience!</p>
                    </div>
                    <?php else: ?>
                        <?php foreach($reviews as $review): ?>
                        <div class="review-card">
                            <div class="review-avatar"><?= htmlspecialchars(strtoupper(substr($review['user_name'], 0, 1))) ?></div>
                            <div class="review-card-content">
                                <div class="review-meta">
                                    <span class="review-author"><?= htmlspecialchars($review['user_name']) ?></span>
                                    <span class="review-date"><?= date('M d, Y', strtotime($review['created_at'])) ?></span>
                                </div>
                                <div class="review-action-row">
                                    <div class="review-stars">
                                        <?php for($i=1; $i<=5; $i++) {
                                            echo $i <= $review['rating'] ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star" style="color:#e2e8f0;"></i>';
                                        } ?>
                                    </div>
                                </div>
                                <p class="review-text"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                                <?php if (!empty($review['photos'])): ?>
                                    <div class="review-photos">
                                        <?php foreach ($review['photos'] as $photo): ?>
                                            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($photo) ?>" alt="Review photo" class="review-photo-img">
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div><!-- /spot-body -->
        </div><!-- /spot-detail-wrap -->
    </div>
</div>

<script>
function initSpotDetailsPage() {
    if (window.__spotDetailsPageInitialized) {
        return;
    }
    window.__spotDetailsPageInitialized = true;

    if (typeof L === 'undefined' || !document.getElementById('mini-map')) {
        return;
    }

    const spotLat = <?= json_encode((float)$spot['latitude']) ?>;
    const spotLng = <?= json_encode((float)$spot['longitude']) ?>;
    const currentSpotId = <?= json_encode((int)$spot['id']) ?>;
    const spotName = <?= json_encode($spot['name']) ?>;
    const spotBarangay = <?= json_encode($spot['barangay'] ?? '') ?>;
    const approvedSpotsUrl = <?= json_encode(BASE_URL . '/api/get-spots') ?>;
    const nearbyPlacesUrl = <?= json_encode(BASE_URL . '/api/get-nearby-places') ?>;
    const spotLocation = L.latLng(spotLat, spotLng);

    const map = L.map('mini-map', { zoomControl: true }).setView([spotLat, spotLng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap contributors</a>'
    }).addTo(map);

    const destinationMarker = L.marker([spotLat, spotLng]).addTo(map)
        .bindPopup("<b><?= htmlspecialchars(addslashes($spot['name'])) ?></b>")
        .openPopup();
    const viewLocationButton = document.getElementById('spot-view-location');
    const mapStatus = document.getElementById('spot-map-status');
    const routeGuidance = document.getElementById('spot-route-guidance');
    const tripSummary = document.getElementById('spot-trip-summary');
    const routeOptions = document.getElementById('spot-route-options');
    const nearbyPlaceLayer = L.layerGroup().addTo(map);
    const nearbyRefreshButton = document.getElementById('spot-nearby-refresh');
    const nearbyStatus = document.getElementById('spot-nearby-status');
    const nearbyResults = document.getElementById('spot-nearby-results');
    const liveControls = document.getElementById('spot-live-controls');
    const liveToggle = document.getElementById('spot-live-toggle');
    const liveStatus = document.getElementById('spot-live-status');
    const routeProfiles = [
        { id: 'car', service: 'routed-car', label: 'Car / motorcycle', icon: 'fa-car-side', color: '#176354' },
        { id: 'bike', service: 'routed-bike', label: 'Bicycle', icon: 'fa-bicycle', color: '#d66b28' },
        { id: 'foot', service: 'routed-foot', label: 'Walking', icon: 'fa-person-walking', color: '#2172b8' }
    ];
    const routeLayers = new Map();
    const routeCards = new Map();
    let currentLocationMarker = null;
    let locationAccuracyCircle = null;
    let currentLocation = null;
    let locationAccuracy = 0;
    let initialRoutesLoaded = false;
    let selectedRouteId = null;
    let lastRoutedLocation = null;
    let gpsWatchId = null;
    let liveRouteRequestActive = false;
    let queuedLiveLocation = null;
    let lastLiveRouteAttemptAt = 0;
    let nearbyPlaces = [];
    let nearbyRequestActive = false;
    let nearbyBoundsFitted = false;
    let hasMapInteraction = false;
    let pendingNearbyBounds = null;
    const nearbyCategoryInfo = {
        restroom: { label: 'Restroom', color: '#0284c7', icon: 'fa-solid fa-restroom' },
        food: { label: 'Food & dining', color: '#ea580c', icon: 'fa-solid fa-utensils' },
        stay: { label: 'Place to stay', color: '#059669', icon: 'fa-solid fa-hotel' },
        attraction: { label: 'Attraction', color: '#d97706', icon: 'fa-solid fa-mountain-sun' },
        other: { label: 'Other service', color: '#7c3aed', icon: 'fa-solid fa-store' }
    };

    map.on('dragstart zoomstart', function () {
        hasMapInteraction = true;
    });

    function nearbyCategory(tags) {
        if (['toilets', 'public_bath'].includes(tags.amenity)) return 'restroom';
        if (['restaurant', 'cafe', 'fast_food', 'food_court', 'bar', 'pub'].includes(tags.amenity) || tags.shop === 'bakery') return 'food';
        if (['hotel', 'guest_house', 'hostel', 'motel', 'camp_site', 'resort'].includes(tags.tourism)) return 'stay';
        if (tags.tourism || tags.historic || tags.leisure) return 'attraction';
        return 'other';
    }

    function getOsmTypeLabel(tags) {
        if (tags.amenity === 'toilets') return 'Public Restroom / CR';
        if (tags.amenity === 'restaurant') return 'Restaurant';
        if (tags.amenity === 'cafe') return 'Cafe / Coffee';
        if (tags.amenity === 'fast_food') return 'Eatery / Fast Food';
        if (tags.shop === 'bakery') return 'Bakery';
        if (tags.tourism === 'hotel' || tags.tourism === 'resort') return 'Hotel & Resort';
        if (tags.tourism === 'guest_house') return 'Pension House / Lodge';
        if (tags.amenity === 'pharmacy') return 'Pharmacy / Drugstore';
        if (tags.amenity === 'hospital' || tags.amenity === 'clinic') return 'Clinic / Medical';
        if (tags.amenity === 'bank') return 'Bank';
        if (tags.amenity === 'atm') return 'ATM / Cash Services';
        if (tags.amenity === 'fuel') return 'Fuel / Gas Station';
        if (tags.amenity === 'ferry_terminal') return 'Port / Ferry Terminal';
        if (tags.amenity === 'bus_station') return 'Bus Terminal';
        if (tags.amenity === 'police') return 'Police / Checkpoint';
        if (tags.shop) return 'Convenience / Retail Store';
        return 'Public Facility';
    }

    function nearbyCategoryLabel(category) {
        return nearbyCategoryInfo[category]?.label || 'Nearby place';
    }

    function approvedSpotNearbyCategory(categoryName) {
        const category = String(categoryName || '').toLowerCase();
        if (category.includes('restaurant') || category.includes('food')) return 'food';
        if (category.includes('resort') || category.includes('accommodation') || category.includes('hotel')) return 'stay';
        if (category.includes('beach') || category.includes('waterfall') || category.includes('boat') || category.includes('historical') || category.includes('cultural')) return 'attraction';
        return 'other';
    }

    function nearbyCategoryCounts(places) {
        const counts = { restroom: 0, food: 0, stay: 0, attraction: 0, other: 0 };
        places.forEach(place => { counts[place.category] = (counts[place.category] || 0) + 1; });
        return counts;
    }

    function nearbyCategorySummary(places) {
        const counts = nearbyCategoryCounts(places);
        return `${counts.restroom} restrooms · ${counts.food} food places · ${counts.stay} places to stay · ${counts.attraction} attractions · ${counts.other} other services`;
    }

    function buildNearbyQuery() {
        const around = `(around:15000,${spotLat},${spotLng})`;
        return `[out:json][timeout:15];(nwr${around}["amenity"~"toilets|public_bath|restaurant|cafe|fast_food|food_court|bar|pub|fuel|pharmacy|hospital|clinic|bank|atm|bus_station|marketplace|post_office|place_of_worship|police|fire_station|ferry_terminal"];nwr${around}["tourism"~"hotel|guest_house|hostel|motel|camp_site|resort|attraction|museum|viewpoint|information|theme_park"];nwr${around}["historic"];nwr${around}["leisure"~"park|marina|nature_reserve"];nwr${around}["shop"];);out center tags 40;`;
    }

    async function requestNearbyData(query) {
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 8000);
        try {
            const response = await fetch('https://overpass-api.de/api/interpreter', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
                body: `data=${encodeURIComponent(query)}`,
                signal: controller.signal
            });
            if (!response.ok) throw new Error(`Nearby search returned ${response.status}.`);
            const data = await response.json();
            if (data.remark && !Array.isArray(data.elements)) throw new Error(data.remark);
            return data;
        } finally {
            clearTimeout(timeout);
        }
    }

    function renderNearbyPlaces() {
        const selectedCategories = new Set(Array.from(document.querySelectorAll('input[name="spot-nearby-type"]:checked'))
            .map(input => input.value));
        const filteredPlaces = nearbyPlaces
            .filter(place => selectedCategories.has(place.category))
            .sort((first, second) => first.distance - second.distance);
        const availabilitySummary = nearbyCategorySummary(nearbyPlaces);

        nearbyPlaceLayer.clearLayers();
        nearbyResults.replaceChildren();
        if (!filteredPlaces.length) {
            nearbyStatus.textContent = selectedCategories.size
                ? `No matching places listed within 5 km. Available nearby: ${availabilitySummary}.`
                : 'Choose at least one place type to show on the map.';
            return;
        }

        filteredPlaces.forEach(function (place) {
            const distanceLabel = formatDistance(place.distance);
            const categoryInfo = nearbyCategoryInfo[place.category] || nearbyCategoryInfo.other;
            const iconClass = place.iconClass || categoryInfo.icon;
            const markerIcon = document.createElement('span');
            markerIcon.className = 'spot-nearby-map-icon';
            markerIcon.style.setProperty('--spot-nearby-color', categoryInfo.color);
            markerIcon.innerHTML = `<i class="${iconClass}"></i>`;
            const marker = L.marker([place.lat, place.lng], {
                icon: L.divIcon({
                    className: 'spot-nearby-div-icon',
                    html: markerIcon,
                    iconSize: [32, 32],
                    iconAnchor: [16, 16]
                })
            }).addTo(nearbyPlaceLayer);
            const popup = document.createElement('div');
            const popupName = document.createElement('strong');
            popupName.textContent = place.name;
            const popupDetails = document.createElement('div');
            const typeText = place.typeLabel || categoryInfo.label;
            popupDetails.textContent = `${typeText} · ${distanceLabel} from this spot`;
            popup.append(popupName, popupDetails);
            marker.bindPopup(popup);

            const item = document.createElement('li');
            const resultButton = document.createElement('button');
            resultButton.type = 'button';
            resultButton.className = 'spot-nearby-result';
            const details = document.createElement('span');
            const name = document.createElement('strong');
            name.className = 'spot-nearby-result-name';
            name.textContent = place.name;
            const category = document.createElement('span');
            category.className = 'spot-nearby-result-category';
            const categoryCode = document.createElement('span');
            categoryCode.className = 'spot-nearby-category-icon';
            categoryCode.style.setProperty('--spot-nearby-color', categoryInfo.color);
            categoryCode.innerHTML = `<i class="${iconClass}"></i>`;
            category.append(categoryCode, document.createTextNode(typeText));
            details.append(name, category);
            const distance = document.createElement('span');
            distance.className = 'spot-nearby-distance';
            distance.textContent = distanceLabel;
            resultButton.append(details, distance);
            resultButton.addEventListener('click', function () {
                map.setView([place.lat, place.lng], 16);
                marker.openPopup();
            });
            item.appendChild(resultButton);
            nearbyResults.appendChild(item);
        });

        if (!nearbyBoundsFitted) {
            nearbyBoundsFitted = true;
            if (!hasMapInteraction) {
                const bounds = L.latLngBounds([spotLat, spotLng], [spotLat, spotLng]);
                filteredPlaces.forEach(place => bounds.extend([place.lat, place.lng]));
                pendingNearbyBounds = bounds;
                requestAnimationFrame(() => {
                    map.invalidateSize({ pan: false });
                    if (pendingNearbyBounds && map.getSize().x > 100 && map.getSize().y > 100 && !hasMapInteraction) {
                        map.fitBounds(pendingNearbyBounds, { padding: [34, 34], maxZoom: 15 });
                        pendingNearbyBounds = null;
                    }
                });
            }
        }
        nearbyStatus.textContent = `${filteredPlaces.length} places & spots found, nearest first.`;
    }

    async function loadNearbyPlaces() {
        if (nearbyRequestActive) return;
        nearbyRequestActive = true;
        nearbyRefreshButton.disabled = true;
        nearbyStatus.textContent = 'Loading nearby places & amenities...';

        let localDirectoryPlaces = [];
        let osmPlaces = [];

        // 1. Instantly fetch real approved MTMS registered spots
        try {
            const endpoint = `${nearbyPlacesUrl}?lat=${encodeURIComponent(spotLat)}&lng=${encodeURIComponent(spotLng)}&spot_id=${encodeURIComponent(currentSpotId)}`;
            const resp = await fetch(endpoint);
            if (resp.ok) {
                const data = await resp.json();
                if (data.success && Array.isArray(data.places)) {
                    localDirectoryPlaces = data.places.map(place => ({
                        id: place.id,
                        lat: Number(place.lat),
                        lng: Number(place.lng),
                        name: place.name,
                        category: place.category,
                        typeLabel: place.type_label || nearbyCategoryInfo[place.category]?.label || 'Amenity',
                        iconClass: place.icon_class || null,
                        distance: spotLocation.distanceTo([Number(place.lat), Number(place.lng)]),
                        source: place.source || 'Local Directory'
                    }));

                    if (localDirectoryPlaces.length > 0) {
                        nearbyPlaces = [...localDirectoryPlaces].sort((a, b) => a.distance - b.distance);
                        renderNearbyPlaces();
                    }
                }
            }
        } catch (e) {
            console.warn('Local directory fetch error:', e);
        }

        // 2. Also fetch OpenStreetMap POIs if available
        try {
            const data = await requestNearbyData(buildNearbyQuery());
            osmPlaces = (data.elements || []).map(function (element) {
                const tags = element.tags || {};
                const lat = Number(element.lat ?? element.center?.lat);
                const lng = Number(element.lon ?? element.center?.lon);
                const name = tags.name || tags['name:en'] || tags.amenity || tags.shop || tags.tourism || tags.historic || tags.leisure;
                if (!Number.isFinite(lat) || !Number.isFinite(lng) || !name) return null;
                const cat = nearbyCategory(tags);
                return {
                    id: `osm-${element.type}-${element.id}`,
                    lat,
                    lng,
                    name,
                    category: cat,
                    typeLabel: getOsmTypeLabel(tags),
                    iconClass: nearbyCategoryInfo[cat]?.icon || 'fa-solid fa-location-dot',
                    distance: spotLocation.distanceTo([lat, lng]),
                    source: 'OpenStreetMap'
                };
            }).filter(Boolean);
        } catch (osmErr) {
            console.warn('OSM POI search skipped/timed out:', osmErr);
        }

        const combinedPlaces = localDirectoryPlaces.concat(osmPlaces);
        const uniquePlaces = [];
        combinedPlaces.forEach(function (place) {
            const duplicate = uniquePlaces.some(existing =>
                existing.category === place.category
                && existing.name.trim().toLowerCase() === place.name.trim().toLowerCase()
                && spotLocation.distanceTo([existing.lat, existing.lng]) < 50
            );
            if (!duplicate) uniquePlaces.push(place);
        });

        nearbyPlaces = uniquePlaces.sort((a, b) => a.distance - b.distance);
        renderNearbyPlaces();

        nearbyRequestActive = false;
        nearbyRefreshButton.disabled = false;
    }

    document.querySelectorAll('input[name="spot-nearby-type"]').forEach(input => {
        input.addEventListener('change', renderNearbyPlaces);
    });
    nearbyRefreshButton.addEventListener('click', loadNearbyPlaces);
    loadNearbyPlaces();

    function formatDistance(meters) {
        return meters >= 1000 ? `${(meters / 1000).toFixed(1)} km` : `${Math.round(meters)} m`;
    }

    function formatDuration(seconds) {
        const minutes = Math.max(1, Math.round(seconds / 60));
        return minutes >= 60
            ? `${Math.floor(minutes / 60)} h ${minutes % 60} min`
            : `${minutes} min`;
    }

    function updateLocationMarker(position) {
        currentLocation = L.latLng(position.coords.latitude, position.coords.longitude);
        locationAccuracy = Number(position.coords.accuracy) || 0;
        if (!currentLocationMarker) {
            currentLocationMarker = L.circleMarker(currentLocation, {
                radius: 8,
                color: '#ffffff',
                weight: 3,
                fillColor: '#1976d2',
                fillOpacity: 1
            }).addTo(map).bindPopup('<b>Your Live GPS Location</b>');
            locationAccuracyCircle = L.circle(currentLocation, {
                radius: Math.max(locationAccuracy, 10),
                color: '#2172b8',
                weight: 1,
                fillColor: '#2172b8',
                fillOpacity: 0.1
            }).addTo(map);
        } else {
            currentLocationMarker.setLatLng(currentLocation);
            locationAccuracyCircle.setLatLng(currentLocation);
            locationAccuracyCircle.setRadius(Math.max(locationAccuracy, 10));
        }
        return currentLocation;
    }

    async function fetchRoute(profile, origin) {
        const coordinates = `${origin.lng},${origin.lat};${spotLng},${spotLat}`;
        const primaryUrl = `https://routing.openstreetmap.de/${profile.service}/route/v1/driving/${coordinates}?overview=full&geometries=geojson`;
        const fallbackUrl = `https://router.project-osrm.org/route/v1/driving/${coordinates}?overview=full&geometries=geojson`;

        let response = await fetch(primaryUrl).catch(() => null);
        if (!response || !response.ok) {
            response = await fetch(fallbackUrl);
        }
        if (!response.ok) throw new Error('Route service unavailable');
        const data = await response.json();
        if (data.code !== 'Ok' || !data.routes || !data.routes.length) {
            throw new Error('No route found');
        }
        return data.routes[0];
    }

    function selectRoute(profileId) {
        selectedRouteId = profileId;
        routeOptions.querySelectorAll('.spot-route-card').forEach(function (card) {
            card.setAttribute('aria-pressed', String(card.dataset.routeId === profileId));
        });
        routeLayers.forEach(function (layer, id) {
            layer.setStyle({ opacity: id === profileId ? 0.95 : 0.3, weight: id === profileId ? 7 : 4 });
        });

        const selectedLayer = routeLayers.get(profileId);
        if (selectedLayer) {
            selectedLayer.bringToFront();
            map.fitBounds(selectedLayer.getBounds(), { padding: [28, 28] });
        }

        if (gpsWatchId !== null && currentLocation) {
            refreshLiveRoute(currentLocation, true);
        }
    }

    async function loadRouteComparisons(origin) {
        mapStatus.textContent = 'Calculating drive, bicycle, and walking directions...';
        routeOptions.replaceChildren();
        routeOptions.hidden = false;
        routeLayers.forEach(layer => map.removeLayer(layer));
        routeLayers.clear();
        routeCards.clear();

        const results = await Promise.all(routeProfiles.map(async function (profile) {
            try {
                return { profile, route: await fetchRoute(profile, origin) };
            } catch (error) {
                return { profile, error };
            }
        }));

        const availableRoutes = results.filter(result => result.route);
        if (!availableRoutes.length) {
            routeOptions.hidden = true;
            mapStatus.textContent = 'Could not calculate road routes. Check your internet connection.';
            return;
        }

        const routeBounds = L.latLngBounds();
        availableRoutes.forEach(function (result, index) {
            const routeLayer = L.geoJSON(result.route.geometry, {
                style: { color: result.profile.color, weight: 6, opacity: 0.8, lineCap: 'round', lineJoin: 'round' }
            }).addTo(map);
            routeLayers.set(result.profile.id, routeLayer);
            routeBounds.extend(routeLayer.getBounds());

            const card = document.createElement('button');
            card.type = 'button';
            card.className = 'spot-route-card';
            card.dataset.routeId = result.profile.id;
            card.setAttribute('aria-pressed', index === 0 ? 'true' : 'false');
            card.innerHTML = `<span class="spot-route-swatch" style="color:${result.profile.color}"><i class="fa-solid ${result.profile.icon}" aria-hidden="true"></i></span><span><span class="spot-route-card-label">${result.profile.label}</span><span class="spot-route-card-detail">${result.profile.id === 'foot' ? 'On foot' : result.profile.id === 'bike' ? 'By bicycle' : 'By road'}</span><span class="spot-route-card-time">${formatDuration(result.route.duration)}</span><span class="spot-route-distance">${formatDistance(result.route.distance)}</span></span>`;
            card.addEventListener('click', () => selectRoute(result.profile.id));
            routeOptions.appendChild(card);
            routeCards.set(result.profile.id, card);
        });

        selectedRouteId = availableRoutes[0].profile.id;
        lastRoutedLocation = origin;
        routeLayers.forEach((layer, id) => {
            layer.setStyle({ opacity: id === selectedRouteId ? 0.95 : 0.3, weight: id === selectedRouteId ? 7 : 4 });
        });
        map.fitBounds(routeBounds, { padding: [28, 28] });
        liveStatus.textContent = 'Live GPS active. Routes & remaining ETA update in real-time as you move.';
        mapStatus.textContent = 'Directions loaded. Select your travel mode below.';
    }

    async function refreshLiveRoute(origin, force = false) {
        const movementThreshold = Math.max(50, Math.min(200, locationAccuracy || 50));
        if (!force && lastRoutedLocation && lastRoutedLocation.distanceTo(origin) < movementThreshold) return;
        if (!force && Date.now() - lastLiveRouteAttemptAt < 5000) return;
        if (liveRouteRequestActive) {
            queuedLiveLocation = origin;
            return;
        }

        const profile = routeProfiles.find(item => item.id === selectedRouteId);
        if (!profile) return;
        liveRouteRequestActive = true;
        lastLiveRouteAttemptAt = Date.now();

        try {
            const route = await fetchRoute(profile, origin);
            const previousLayer = routeLayers.get(profile.id);
            if (previousLayer) map.removeLayer(previousLayer);
            const routeLayer = L.geoJSON(route.geometry, {
                style: { color: profile.color, weight: 7, opacity: 0.95, lineCap: 'round', lineJoin: 'round' }
            }).addTo(map);
            routeLayers.set(profile.id, routeLayer);
            if (routeCards.get(profile.id)) {
                routeCards.get(profile.id).querySelector('.spot-route-card-time').textContent = formatDuration(route.duration);
                routeCards.get(profile.id).querySelector('.spot-route-distance').textContent = formatDistance(route.distance);
            }
            lastRoutedLocation = origin;
            const accuracy = locationAccuracy ? ` Accuracy: ±${Math.round(locationAccuracy)} m.` : '';
            liveStatus.textContent = `Live GPS tracking active.${accuracy}`;
        } catch (error) {
            console.warn('Live route update warning:', error);
        } finally {
            liveRouteRequestActive = false;
            if (queuedLiveLocation) {
                const nextLocation = queuedLiveLocation;
                queuedLiveLocation = null;
                refreshLiveRoute(nextLocation);
            }
        }
    }

    function stopLiveTracking(statusMessage) {
        if (gpsWatchId !== null) {
            navigator.geolocation.clearWatch(gpsWatchId);
            gpsWatchId = null;
        }
        queuedLiveLocation = null;
        liveToggle.setAttribute('aria-pressed', 'false');
        liveControls.hidden = true;
        viewLocationButton.disabled = false;
        if (statusMessage) mapStatus.textContent = statusMessage;
    }

    function handleLivePosition(position) {
        const origin = updateLocationMarker(position);
        if (!initialRoutesLoaded) {
            initialRoutesLoaded = true;
            tripSummary.hidden = false;
            routeGuidance.hidden = false;
            lastRoutedLocation = origin;
            mapStatus.textContent = 'GPS location found. Calculating directions...';
            loadRouteComparisons(origin).catch(function (error) {
                mapStatus.textContent = error.message || 'Could not load routes. Check your connection and try again.';
            });
            return;
        }
        refreshLiveRoute(origin);
    }

    function handleLiveLocationError(error) {
        const messages = {
            1: 'Location access was denied. Allow GPS location access in your browser to get live directions.',
            2: 'GPS location is unavailable. Check your device location settings.',
            3: 'GPS request timed out. Please try again.'
        };
        stopLiveTracking(messages[error.code] || 'Could not get your location.');
    }

    liveToggle.addEventListener('click', function () {
        stopLiveTracking('Live tracking stopped.');
    });

    window.addEventListener('pagehide', function () {
        if (gpsWatchId !== null) navigator.geolocation.clearWatch(gpsWatchId);
    });

    viewLocationButton.addEventListener('click', function () {
        if (gpsWatchId !== null) {
            // Already tracking, recenter on user location
            if (currentLocation) {
                map.setView(currentLocation, 16);
            }
            return;
        }
        hasMapInteraction = true;
        if (!navigator.geolocation) {
            mapStatus.textContent = 'Geolocation is not available in your browser.';
            return;
        }

        viewLocationButton.disabled = true;
        liveControls.hidden = false;
        liveToggle.setAttribute('aria-pressed', 'true');
        liveStatus.textContent = 'Acquiring GPS location...';
        mapStatus.textContent = 'Finding your real-time location...';

        // 1. Fast initial location fix
        navigator.geolocation.getCurrentPosition(handleLivePosition, handleLiveLocationError, {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 5000
        });

        // 2. Real-time live continuous watch
        try {
            gpsWatchId = navigator.geolocation.watchPosition(handleLivePosition, handleLiveLocationError, {
                enableHighAccuracy: true,
                maximumAge: 1000,
                timeout: 15000
            });
        } catch (error) {
            stopLiveTracking('Could not start GPS tracking. Check browser permissions.');
        }
    });

    const refreshMapSize = () => {
        requestAnimationFrame(() => {
            map.invalidateSize({ pan: false });
            if (map.getSize().x <= 100 || map.getSize().y <= 100) return;

            const activeRoute = gpsWatchId !== null ? routeLayers.get(selectedRouteId) : null;
            if (activeRoute) {
                map.fitBounds(activeRoute.getBounds(), { padding: [28, 28] });
                pendingNearbyBounds = null;
                return;
            }

            const bounds = L.latLngBounds([spotLat, spotLng], [spotLat, spotLng]);
            nearbyPlaces.forEach(place => bounds.extend([place.lat, place.lng]));
            map.fitBounds(pendingNearbyBounds || bounds, { padding: [34, 34], maxZoom: 15 });
            pendingNearbyBounds = null;
        });
    };
    const mapResizeObserver = new ResizeObserver(() => {
        refreshMapSize();
    });
    mapResizeObserver.observe(document.getElementById('mini-map'));
    window.addEventListener('resize', refreshMapSize);
    setTimeout(refreshMapSize, 250);

    function initSpotCarousel() {
        const slides = document.querySelectorAll('.carousel-img');
        const dots = document.querySelectorAll('.carousel-dot');
        if (slides.length <= 1) return;

        let current = 0;
        let timer;

        function showSlide(idx) {
            slides.forEach(s => s.style.opacity = '0');
            dots.forEach(d => d.classList.remove('active'));
            slides[idx].style.opacity = '1';
            if (dots[idx]) dots[idx].classList.add('active');
            current = idx;
        }

        function next() { showSlide((current + 1) % slides.length); }
        function prev() { showSlide((current - 1 + slides.length) % slides.length); }

        function resetTimer() { clearInterval(timer); timer = setInterval(next, 3000); }

        const nextBtn = document.getElementById('nextBtn');
        const prevBtn = document.getElementById('prevBtn');

        nextBtn?.addEventListener('click', () => { next(); resetTimer(); });
        prevBtn?.addEventListener('click', () => { prev(); resetTimer(); });

        dots.forEach(dot => {
            dot.addEventListener('click', () => {
                const idx = Number(dot.dataset.index);
                if (!Number.isNaN(idx)) {
                    showSlide(idx);
                    resetTimer();
                }
            });
        });

        resetTimer();
    }

    initSpotCarousel();
}

window.addEventListener('load', initSpotDetailsPage);
if (document.readyState !== 'loading') {
    initSpotDetailsPage();
}
</script>

<script>
    function openReviewLightbox(src) {
        const overlay = document.getElementById('reviewImageLightbox');
        const image = document.getElementById('reviewLightboxImage');
        const zoomLabel = document.getElementById('reviewLightboxZoomLabel');
        overlay.classList.add('open');
        image.src = src;
        image.dataset.zoom = '1';
        image.style.transform = 'scale(1)';
        zoomLabel.textContent = '100%';
        document.body.style.overflow = 'hidden';
    }

    function closeReviewLightbox() {
        const overlay = document.getElementById('reviewImageLightbox');
        const image = document.getElementById('reviewLightboxImage');
        overlay.classList.remove('open');
        image.src = '';
        document.body.style.overflow = '';
    }

    function updateReviewLightboxZoom(delta) {
        const image = document.getElementById('reviewLightboxImage');
        const zoomLabel = document.getElementById('reviewLightboxZoomLabel');
        if (!image || !image.dataset.zoom) return;
        let zoom = parseFloat(image.dataset.zoom) || 1;
        zoom = Math.min(3, Math.max(0.5, zoom + delta));
        image.dataset.zoom = zoom.toFixed(2);
        image.style.transform = `scale(${zoom})`;
        zoomLabel.textContent = `${Math.round(zoom * 100)}%`;
    }

    function onReady(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
        } else {
            callback();
        }
    }

    onReady(function() {
        const params = new URLSearchParams(window.location.search);
        
        if (params.get('focus') === 'review') {
            const reviewSection = document.getElementById('reviewSection');
            if (reviewSection) {
                reviewSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        // Check if review was submitted
        if (params.get('review_submitted') === '1' || params.get('review_updated') === '1') {
            const isUpdate = params.get('review_updated') === '1';
            
            // Show SweetAlert when it's available
            const showAlert = (attempts = 0) => {
                if (typeof Swal === 'undefined') {
                    if (attempts < 50) {  // Try for up to 10 seconds
                        setTimeout(() => showAlert(attempts + 1), 200);
                        return;
                    } else {
                        // Fallback to native alert
                        if (isUpdate) {
                            alert('✓ Updated!\nYour comment has been updated successfully.');
                        } else {
                            alert('✓ Thank You!\nYour review was submitted for LGU approval. It will appear publicly once approved.');
                        }
                        return;
                    }
                }
                
                if (isUpdate) {
                    Swal.fire({
                        title: 'Updated!',
                        text: 'Your comment has been updated successfully.',
                        icon: 'success',
                        confirmButtonColor: '#166534',
                        allowOutsideClick: false
                    });
                } else {
                    Swal.fire({
                        title: 'Thank You!',
                        text: 'Your review was submitted for LGU approval. It will appear publicly once approved.',
                        icon: 'success',
                        confirmButtonColor: '#166534',
                        allowOutsideClick: false
                    });
                }
            };
            
            // Start checking for Swal with immediate first check
            showAlert();
            
            // Clean up the URL
            if (window.history && window.history.replaceState) {
                const cleanUrl = window.location.pathname + (params.get('modal') === '1' ? '?modal=1' : '');
                window.history.replaceState(null, '', cleanUrl);
            }
        }

        document.querySelectorAll('.review-photo-img')?.forEach(img => {
            img.addEventListener('click', function() {
                openReviewLightbox(this.src);
            });
        });

        document.querySelectorAll('.review-edit-btn')?.forEach(btn => {
            btn.addEventListener('click', function() {
                const reviewId = this.dataset.reviewId;
                document.querySelectorAll('.review-edit-form').forEach(form => {
                    form.style.display = 'none';
                });
                const targetForm = document.getElementById('review-edit-form-' + reviewId);
                if (targetForm) {
                    targetForm.style.display = 'block';
                }
            });
        });

        document.querySelectorAll('[data-cancel-edit]')?.forEach(btn => {
            btn.addEventListener('click', function() {
                const reviewId = this.dataset.cancelEdit;
                const targetForm = document.getElementById('review-edit-form-' + reviewId);
                if (targetForm) {
                    targetForm.style.display = 'none';
                }
            });
        });

        const lightbox = document.getElementById('reviewImageLightbox');
        lightbox?.addEventListener('click', function(e) {
            if (e.target === this || e.target.classList.contains('review-lightbox-close')) {
                closeReviewLightbox();
            }
        });

        const lightboxImage = document.getElementById('reviewLightboxImage');
        lightboxImage?.addEventListener('click', function(e) {
            e.stopPropagation();
            const zoom = parseFloat(this.dataset.zoom) || 1;
            updateReviewLightboxZoom(zoom > 1 ? -0.25 : 0.25);
        });

        document.getElementById('reviewLightboxZoomInBtn')?.addEventListener('click', function(e) {
            e.stopPropagation();
            updateReviewLightboxZoom(0.25);
        });

        document.getElementById('reviewLightboxZoomOutBtn')?.addEventListener('click', function(e) {
            e.stopPropagation();
            updateReviewLightboxZoom(-0.25);
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (document.getElementById('reviewImageLightbox')?.classList.contains('open')) {
                    closeReviewLightbox();
                }
            }
        });

    });
</script>

<div id="reviewImageLightbox" class="review-lightbox-overlay" aria-hidden="true">
    <div class="review-lightbox-content" role="dialog" aria-modal="true" aria-label="Review image preview">
        <button type="button" class="review-lightbox-close" aria-label="Close review image">×</button>
        <img id="reviewLightboxImage" src="" alt="Review image preview" class="review-lightbox-image" role="button" aria-label="Click to zoom">
        <div class="review-lightbox-controls">
            <button type="button" id="reviewLightboxZoomOutBtn" class="review-lightbox-action" aria-label="Zoom out">−</button>
            <span id="reviewLightboxZoomLabel" class="review-lightbox-zoom-label">100%</span>
            <button type="button" id="reviewLightboxZoomInBtn" class="review-lightbox-action" aria-label="Zoom in">+</button>
        </div>
    </div>
</div>

<?php require_once '../app/views/partials/footer.php'; ?>