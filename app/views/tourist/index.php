<?php require_once '../app/views/partials/header.php'; ?>

<!-- Hero Section -->
<div id="home" class="hero-section section-anchor" style="background-image: linear-gradient(rgba(6, 31, 20, 0.38), rgba(6, 31, 20, 0.6)), url('<?= BASE_URL ?>/assets/images/final.webp'); background-size: cover; background-position: center center; background-repeat: no-repeat;">
    <div class="hero-bg-overlay"></div>
    <div class="hero-decor-orb hero-orb-1" aria-hidden="true"></div>
    <div class="hero-decor-orb hero-orb-2" aria-hidden="true"></div>
    
    <div class="container hero-container">
        <div class="hero-content">
            <div class="hero-badge">
                <span class="hero-badge-pulse"></span>
                <span>Official Municipal Tourism Portal · Malangas, Zamboanga Sibugay</span>
            </div>
            <h1 class="hero-title">Discover the Hidden Wonders of <span>Malangas</span></h1>
            <p class="hero-sub">Experience untouched coastlines, emerald mangrove eco-trails, and vibrant island culture with real-time GPS navigation and verified local listings.</p>
            
            <div class="hero-actions">
                <a href="#tourist-directory" class="btn-hero-primary">
                    <i class="fa-solid fa-compass"></i> Explore Destinations
                </a>
                <a href="#about" class="btn-hero-secondary">
                    <i class="fa-solid fa-circle-info"></i> About Malangas
                </a>
            </div>

            <!-- Stats strip -->
            <div class="hero-stats-strip">
                <div class="hero-stat-item">
                    <div class="hero-stat-icon"><i class="fa-solid fa-map-location-dot"></i></div>
                    <div class="hero-stat-info">
                        <strong>25 Barangays</strong>
                        <span>Coastal & inland spots</span>
                    </div>
                </div>
                <div class="hero-stat-divider" aria-hidden="true"></div>
                <div class="hero-stat-item">
                    <div class="hero-stat-icon"><i class="fa-solid fa-shield-halved"></i></div>
                    <div class="hero-stat-info">
                        <strong>100% Verified</strong>
                        <span>LGU-moderated listings</span>
                    </div>
                </div>
                <div class="hero-stat-divider" aria-hidden="true"></div>
                <div class="hero-stat-item">
                    <div class="hero-stat-icon"><i class="fa-solid fa-route"></i></div>
                    <div class="hero-stat-info">
                        <strong>Live Directions</strong>
                        <span>Multi-modal GPS routing</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Index / Directory Section -->
<div class="directory-section section-anchor" id="tourist-directory">
    <div class="container">
        <div class="section-header-block reveal-item">
            <span class="section-badge"><i class="fa-solid fa-compass"></i> Tourist Directory</span>
            <h2 class="section-main-title">Explore Approved Spots & Resorts</h2>
            <p class="section-main-sub">Find pristine beaches, eco-parks, dining destinations, and coastal accommodations in Malangas.</p>
            
            <div class="directory-search-bar">
                <div class="search-input-wrap">
                    <i class="fa-solid fa-magnifying-glass search-icon" aria-hidden="true"></i>
                    <input type="text" id="search-spot" class="search-input" placeholder="Search by destination name or keyword...">
                </div>
                <div class="category-select-wrap">
                    <i class="fa-solid fa-filter filter-icon" aria-hidden="true"></i>
                    <select id="filter-category" class="category-select">
                        <option value="">All Categories</option>
                        <option value="Beach/Island">Beach / Island</option>
                        <option value="Waterfall">Waterfall</option>
                        <option value="Resort">Resort</option>
                        <option value="Restaurant">Restaurant</option>
                        <option value="Boat/Tour Operator">Boat / Tour Operator</option>
                        <option value="Historical/Cultural">Historical / Cultural</option>
                        <option value="Accommodation">Accommodation</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="category-select-wrap">
                    <i class="fa-solid fa-arrow-down-short-wide filter-icon" aria-hidden="true"></i>
                    <select id="sort-spots" class="category-select">
                        <option value="most-reviewed" selected>Most Reviewed</option>
                        <option value="top-rated">Top Rated</option>
                        <option value="newest">Newest</option>
                    </select>
                </div>
            </div>
        </div>
        
        <?php if (empty($spots)): ?>
            <div class="reveal-item directory-empty-card">
                <i class="fa-solid fa-compass"></i>
                <h3>No tourist spots available yet</h3>
                <p>Approved destinations will appear here once verified by the LGU.</p>
            </div>
        <?php else: ?>
            <div class="spots-grid-container" id="spots-grid">
                <?php foreach ($spots as $i => $spot): ?>
                    <?php $isFeatured = $i === 0; ?>
                    <div class="modern-spot-card reveal-card <?= $isFeatured ? 'featured-spot-card' : '' ?>"
                         data-delay="<?= $i ?>"
                         data-name="<?= strtolower(htmlspecialchars($spot['name'])) ?>"
                         data-category="<?= htmlspecialchars($spot['category_name']) ?>"
                         data-review-count="<?= (int)($spot['review_count'] ?? 0) ?>"
                         data-average-rating="<?= (float)($spot['avg_rating'] ?? 0) ?>"
                         data-created-at="<?= !empty($spot['created_at']) ? strtotime($spot['created_at']) : 0 ?>">
                        <!-- Image Container -->
                        <div class="spot-card-media">
                            <?php if (!empty($spot['cover_photo'])): ?>
                                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($spot['cover_photo']) ?>" alt="<?= htmlspecialchars($spot['name']) ?>" class="spot-card-img" loading="lazy">
                            <?php else: ?>
                                <div class="spot-card-placeholder">
                                    <i class="fa-solid <?= htmlspecialchars($spot['category_icon'] ?: 'fa-mountain-sun') ?>"></i>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Badges Overlay -->
                            <div class="spot-media-overlay">
                                <?php
                                    $monitorStatus = $spot['monitor_status'] ?? 'normal';
                                    $opMap = [
                                        'open' => ['Open','#16a34a','#dcfce7'],
                                        'closed' => ['Closed','#dc2626','#fee2e2'],
                                        'temp_closed' => ['Temp. Closed','#ca8a04','#fef3c7'],
                                        'maintenance' => ['Maintenance','#1e40af','#dbeafe'],
                                        'under_renovation' => ['Under Renovation','#1e40af','#dbeafe'],
                                        'warning' => ['Warning','#b45309','#fef3c7'],
                                        'improved' => ['Improved','#0f766e','#ccfbf1'],
                                        'normal' => ['Open','#16a34a','#dcfce7']
                                    ];
                                    $effectiveStatus = $monitorStatus === 'normal' ? ($spot['operational_status'] ?? 'open') : $monitorStatus;
                                    [$opLabel, $opTxt, $opBg] = $opMap[$effectiveStatus] ?? ['Open','#16a34a','#dcfce7'];
                                ?>
                                <span class="spot-status-pill" style="background:<?= $opBg ?>;color:<?= $opTxt ?>;">
                                    <i class="fa-solid fa-circle"></i> <?= $opLabel ?>
                                </span>

                                <?php if ($isFeatured): ?>
                                    <span class="featured-spot-badge">
                                        <i class="fa-solid fa-medal"></i> Recommended
                                    </span>
                                <?php endif; ?>
                                
                                <span class="spot-category-pill">
                                    <i class="fa-solid <?= htmlspecialchars($spot['category_icon'] ?: 'fa-tag') ?>"></i> <?= htmlspecialchars($spot['category_name']) ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="spot-card-body">
                            <h3 class="spot-card-title"><?= htmlspecialchars($spot['name']) ?></h3>
                            
                            <?php if (!empty($spot['barangay'])): ?>
                                <p class="spot-card-location">
                                    <i class="fa-solid fa-location-dot"></i> Barangay <?= htmlspecialchars($spot['barangay']) ?>
                                </p>
                            <?php endif; ?>

                            <!-- Star Rating -->
                            <div class="spot-card-rating" aria-label="<?= number_format((float)$spot['avg_rating'], 1) ?> out of 5 stars">
                                <div class="rating-stars">
                                    <?php
                                        $avg = (float)$spot['avg_rating'];
                                        for ($star = 1; $star <= 5; $star++) {
                                            if ($star <= $avg) echo '<i class="fa-solid fa-star" aria-hidden="true"></i>';
                                            else if ($star - 0.5 <= $avg) echo '<i class="fa-solid fa-star-half-stroke" aria-hidden="true"></i>';
                                            else echo '<i class="fa-regular fa-star" aria-hidden="true"></i>';
                                        }
                                    ?>
                                </div>
                                <span class="rating-value"><?= number_format($avg, 1) ?></span>
                                <span class="rating-count">(<?= (int)$spot['review_count'] ?>)</span>
                            </div>

                            <p class="spot-card-desc">
                                <?= htmlspecialchars(substr($spot['description'], 0, 110)) ?>...
                            </p>
                            
                            <a href="#" onclick="openSpotModal(<?= $spot['id'] ?>); return false;" class="spot-card-cta">
                                <span>View Details & Location</span>
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div id="spots-empty-state" class="directory-empty-card" style="display:none;">
                <i class="fa-solid fa-magnifying-glass"></i>
                <h3>No matching spots found</h3>
                <p>Try refining your search query or selecting a different category.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- About Section -->
<div id="about" class="about-section section-anchor">
    <div class="container">
        <div class="section-header-block reveal-item">
            <span class="section-badge"><i class="fa-solid fa-landmark-flag"></i> Municipal Tourism Office</span>
            <h2 class="section-main-title">About Malangas Tourism</h2>
            <p class="section-main-sub">Committed to developing sustainable eco-tourism, preserving cultural heritage, and empowering local communities.</p>
        </div>

        <div class="about-grid">
            <!-- Mission Card -->
            <div class="about-feature-card reveal-item">
                <div class="about-card-icon-wrap icon-mission">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
                <div class="about-card-badge">Our Mission</div>
                <h3 class="about-card-title">Promote Sustainable Tourism</h3>
                <p class="about-card-desc">To promote and develop sustainable tourism in Malangas, Zamboanga Sibugay by showcasing its natural beauty, cultural heritage, and warm coastal hospitality.</p>
            </div>

            <!-- Vision Card -->
            <div class="about-feature-card reveal-item">
                <div class="about-card-icon-wrap icon-vision">
                    <i class="fa-solid fa-eye"></i>
                </div>
                <div class="about-card-badge">Our Vision</div>
                <h3 class="about-card-title">A Thriving Destination</h3>
                <p class="about-card-desc">A thriving tourism destination that preserves the natural environment and cultural identity of Malangas while creating sustainable economic opportunities for local communities.</p>
            </div>
        </div>

        <!-- About Malangas Municipality Feature Card -->
        <div class="about-showcase-card reveal-item">
            <div class="about-showcase-header">
                <div class="about-showcase-icon">
                    <i class="fa-solid fa-monument"></i>
                </div>
                <div>
                    <span class="about-showcase-eyebrow">MUNICIPAL PROFILE</span>
                    <h3 class="about-showcase-title">Discover the Municipality of Malangas</h3>
                </div>
            </div>
            
            <p class="about-showcase-text">
                Malangas is a coastal municipality in the province of <strong>Zamboanga Sibugay, Philippines</strong>. Blessed with scenic shorelines along Dumanquilas Bay, lush mangrove eco-systems, and rich cultural traditions, Malangas offers travelers a genuine encounter with pristine nature and warm Sibugay hospitality. The <strong>Malangas Tourism Management System (MTMS)</strong> serves as the official digital gateway for tourists, business partners, and the Local Government Unit to collaborate and explore.
            </p>

            <div class="about-highlights-grid">
                <div class="about-highlight-box">
                    <div class="highlight-icon"><i class="fa-solid fa-water"></i></div>
                    <div>
                        <strong>Coastal Wonders</strong>
                        <p>Pristine island shores and serene marine waters.</p>
                    </div>
                </div>
                <div class="about-highlight-box">
                    <div class="highlight-icon"><i class="fa-solid fa-tree"></i></div>
                    <div>
                        <strong>Eco-Sanctuaries</strong>
                        <p>Protected mangrove trails and rich biodiversity.</p>
                    </div>
                </div>
                <div class="about-highlight-box">
                    <div class="highlight-icon"><i class="fa-solid fa-heart"></i></div>
                    <div>
                        <strong>Warm Hospitality</strong>
                        <p>Authentic local cuisine and welcoming communities.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ===== Scroll Reveal ===== */
.hero-icon, .hero-title, .hero-sub {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.7s ease, transform 0.7s ease;
}
.hero-icon { transition-delay: 0s; }
.hero-title { transition-delay: 0.15s; }
.hero-sub   { transition-delay: 0.3s; }
.hero-loaded .hero-icon,
.hero-loaded .hero-title,
.hero-loaded .hero-sub {
    opacity: 1;
    transform: translateY(0);
}

.reveal-item {
    opacity: 0;
    transform: translateY(40px);
    transition: opacity 0.65s ease, transform 0.65s ease;
}
.reveal-item.visible {
    opacity: 1;
    transform: translateY(0);
}

.reveal-card {
    opacity: 0;
    transform: translateY(50px) scale(0.97);
    transition: opacity 0.55s ease, transform 0.55s ease, box-shadow 0.2s, background 0.2s;
}
.reveal-card.visible {
    opacity: 1;
    transform: translateY(0) scale(1);
}
.featured-spot-card {
    position: relative;
    border: 1px solid rgba(245, 158, 11, 0.45);
    box-shadow: 0 22px 55px rgba(245, 158, 11, 0.18);
    transform: translateY(-2px);
}

.featured-spot-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.45rem;
    background: linear-gradient(135deg, #f59e0b, #f97316);
    color: #fff;
    padding: 0.5rem 0.85rem;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    box-shadow: 0 10px 20px rgba(245, 158, 11, 0.3);
    white-space: nowrap;
    margin-left: 0.25rem;
    margin-right: 0;
}

@media (max-width: 768px) {
    .featured-spot-badge {
        padding: 0.45rem 0.7rem;
        font-size: 0.65rem;
    }
}

@media (max-width: 480px) {
    .spot-media-overlay {
        top: 0.6rem;
        left: 0.6rem;
        right: 0.6rem;
        gap: 0.35rem;
    }

    .spot-status-pill,
    .spot-category-pill,
    .featured-spot-badge {
        font-size: 0.62rem;
        padding: 0.22rem 0.5rem;
    }

    .spot-category-pill {
        margin-left: 0;
    }
}

@media (max-width: 640px) {
    .spots-grid-container {
        grid-template-columns: minmax(0, 1fr);
    }

    .modern-spot-card,
    .spot-card-body {
        min-width: 0;
    }

    .spot-card-title,
    .spot-card-location,
    .spot-card-desc {
        overflow-wrap: anywhere;
    }

    .spot-media-overlay > * {
        min-width: 0;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .directory-search-bar,
    .search-input-wrap,
    .category-select-wrap {
        min-width: 0;
    }
}
</style>

<script>
    // Hero animate on load
    window.addEventListener('load', () => {
        document.querySelector('.hero-inner')?.classList.add('hero-loaded');
    });

    // Grid filters
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-spot');
        const categorySelect = document.getElementById('filter-category');
        const sortSelect = document.getElementById('sort-spots');
        const grid = document.getElementById('spots-grid');
        const cards = Array.from(document.querySelectorAll('.modern-spot-card'));

        function sortCards() {
            if (!grid || !sortSelect) return;

            const currentSort = sortSelect.value;
            const sortedCards = [...cards].sort((a, b) => {
                if (currentSort === 'most-reviewed') {
                    return Number(b.dataset.reviewCount) - Number(a.dataset.reviewCount)
                        || Number(b.dataset.averageRating) - Number(a.dataset.averageRating);
                }

                if (currentSort === 'newest') {
                    return Number(b.dataset.createdAt || 0) - Number(a.dataset.createdAt || 0);
                }

                return Number(b.dataset.averageRating) - Number(a.dataset.averageRating)
                    || Number(b.dataset.reviewCount) - Number(a.dataset.reviewCount);
            });

            sortedCards.forEach(card => grid.appendChild(card));
        }

        function filterGrid() {
            const query = searchInput ? searchInput.value.toLowerCase() : '';
            const category = categorySelect ? categorySelect.value : '';
            let visibleCount = 0;
            const emptyMessage = document.getElementById('spots-empty-state');

            cards.forEach(card => {
                const matchName = (card.dataset.name || '').includes(query);
                const matchCat = category === "" || card.dataset.category === category;
                
                if (matchName && matchCat) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (emptyMessage) {
                emptyMessage.style.display = visibleCount === 0 ? 'block' : 'none';
            }

            sortCards();
        }

        if (searchInput) searchInput.addEventListener('input', filterGrid);
        if (categorySelect) categorySelect.addEventListener('change', filterGrid);
        if (sortSelect) sortSelect.addEventListener('change', filterGrid);
        if (grid && cards.length) {
            sortCards();
        }

        // Scroll reveal observer
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        // Staggered card reveal
        const cardObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const delay = Math.min(parseInt(entry.target.dataset.delay || 0), 8) * 80;
                    setTimeout(() => entry.target.classList.add('visible'), delay);
                    cardObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08 });

        document.querySelectorAll('.reveal-item').forEach(el => revealObserver.observe(el));
        document.querySelectorAll('.reveal-card').forEach(el => cardObserver.observe(el));
    });
</script>

<script>
// Observe full-screen sections and add entrance animation when they enter view
document.addEventListener('DOMContentLoaded', function(){
    const sections = document.querySelectorAll('.full-screen-section');
    const opts = { threshold: 0.12 };
    const secObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting){
                entry.target.classList.add('visible');
                // don't unobserve so they can animate every time if needed
            } else {
                // optionally keep visible; comment next line to keep once-visible
                // entry.target.classList.remove('visible');
            }
        });
    }, opts);

    sections.forEach(s => secObserver.observe(s));
});
</script>

<!-- Removed single-section hide/show script to restore normal scrolling -->

<?php require_once '../app/views/partials/spot-modal.php'; ?>

<?php require_once '../app/views/partials/footer.php'; ?>
