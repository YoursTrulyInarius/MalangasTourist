<nav class="navbar">
    <div class="container navbar-container">
        <div class="navbar-header">
            <!-- Brand / Logo -->
            <a href="<?= BASE_URL ?>/#home" class="navbar-brand">
                <?php if (file_exists(__DIR__ . '/../../../public/assets/images/logo.png')): ?>
                    <img src="<?= BASE_URL ?>/assets/images/logo.png" alt="Malangas Logo" class="navbar-logo">
                <?php else: ?>
                    <div class="navbar-logo-placeholder">
                        <i class="fa-solid fa-earth-asia"></i>
                    </div>
                <?php endif; ?>
                <div class="navbar-brand-text">
                    <span class="navbar-brand-title">Malangas Tourism</span>
                    <span class="navbar-brand-subtitle">Municipal Tourism Office</span>
                </div>
            </a>

            <!-- Navigation Toggle -->
            <button type="button" class="navbar-toggle" aria-label="Toggle navigation" aria-controls="navbarMenu" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>

        <div class="navbar-menu" id="navbarMenu">
            <a href="<?= BASE_URL ?>/#home" class="nav-link"><i class="fa-solid fa-house-chimney nav-link-icon"></i> Home</a>
            <a href="<?= BASE_URL ?>/#tourist-directory" class="nav-link"><i class="fa-solid fa-compass nav-link-icon"></i> Tourist Spots</a>
            <a href="<?= BASE_URL ?>/#about" class="nav-link"><i class="fa-solid fa-circle-info nav-link-icon"></i> About</a>

            <?php if (Auth::isLoggedIn()): ?>
                <?php if (Auth::user()['role'] === 'lgu'): ?>
                    <a href="<?= BASE_URL ?>/lgu" class="nav-link nav-link-highlight"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
                <?php elseif (Auth::user()['role'] === 'business'): ?>
                    <a href="<?= BASE_URL ?>/business" class="nav-link nav-link-highlight"><i class="fa-solid fa-store"></i> My Listing</a>
                <?php endif; ?>

                <div class="nav-dropdown">
                    <button type="button" class="nav-user-btn">
                        <i class="fa-solid fa-circle-user"></i>
                        <span><?= htmlspecialchars(Auth::user()['name']) ?></span>
                        <i class="fa-solid fa-chevron-down" style="font-size: 0.7rem;"></i>
                    </button>
                    <div class="dropdown-content">
                        <a href="<?= BASE_URL ?>/logout">
                            <i class="fa-solid fa-right-from-bracket"></i> Logout
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/login" class="nav-login-btn">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Login
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="navbar-backdrop" id="navbarBackdrop" aria-hidden="true"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggle = document.querySelector('.navbar-toggle');
    var menu = document.getElementById('navbarMenu');
    var backdrop = document.getElementById('navbarBackdrop');
    if (!toggle || !menu) return;

    function closeMenu() {
        menu.classList.remove('open');
        toggle.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
        if (backdrop) backdrop.classList.remove('open');
        document.body.classList.remove('navbar-open');
    }

    function openMenu() {
        menu.classList.add('open');
        toggle.classList.add('open');
        toggle.setAttribute('aria-expanded', 'true');
        if (backdrop) backdrop.classList.add('open');
        document.body.classList.add('navbar-open');
    }

    toggle.addEventListener('click', function(e) {
        e.stopPropagation();
        var isOpen = menu.classList.contains('open');
        if (isOpen) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    // Close when clicking the backdrop
    if (backdrop) {
        backdrop.addEventListener('click', function(e) {
            e.stopPropagation();
            closeMenu();
        });
    }

    // Smooth scroll helper for on-page anchors
    function scrollToTarget(targetId) {
        var targetElement = document.getElementById(targetId);
        if (!targetElement) return false;

        closeMenu();

        // Delay lets the mobile menu close and unfreezes body scroll
        setTimeout(function() {
            var navbar = document.querySelector('.navbar');
            var navHeight = navbar ? navbar.offsetHeight : 68;
            var elementTop = targetElement.getBoundingClientRect().top + window.pageYOffset;
            var finalOffset = Math.max(0, elementTop - navHeight - 12);

            window.scrollTo({
                top: finalOffset,
                behavior: 'smooth'
            });

            if (window.history && window.history.pushState) {
                window.history.pushState(null, '', '#' + targetId);
            } else {
                window.location.hash = targetId;
            }
        }, 80);

        return true;
    }

    // Handle all nav links (Home, Tourist Spots, About, Login, etc.)
    var navLinks = menu.querySelectorAll('a');
    navLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            var href = link.getAttribute('href') || '';
            if (!href || href === '#') return;

            // If already on this exact page (e.g. tapping Login while on /login)
            if (href === window.location.href || href === window.location.pathname) {
                e.preventDefault();
                closeMenu();
                return;
            }

            var hashIndex = href.indexOf('#');
            if (hashIndex !== -1) {
                var targetId = href.substring(hashIndex + 1);
                var targetElement = document.getElementById(targetId);

                if (targetElement) {
                    // Target section exists on the CURRENT page (e.g. index page): smooth scroll to it
                    e.preventDefault();
                    scrollToTarget(targetId);
                    return;
                } else {
                    // Target section is on ANOTHER page (e.g. currently on /login, link is /#home):
                    // Explicitly navigate so mobile browsers do not cancel the jump
                    e.preventDefault();
                    closeMenu();
                    window.location.href = href;
                    return;
                }
            }

            // For regular full-page links (e.g. /login, /register, /lgu, /logout)
            e.preventDefault();
            closeMenu();
            window.location.href = href;
        });
    });

    // Also support clicking the brand logo to navigate or scroll
    var brandLink = document.querySelector('.navbar-brand');
    if (brandLink) {
        brandLink.addEventListener('click', function(e) {
            var href = brandLink.getAttribute('href') || '';
            if (!href) return;

            var hashIndex = href.indexOf('#');
            if (hashIndex !== -1) {
                var targetId = href.substring(hashIndex + 1);
                var targetElement = document.getElementById(targetId);
                if (targetElement) {
                    e.preventDefault();
                    scrollToTarget(targetId);
                    return;
                }
            }

            e.preventDefault();
            closeMenu();
            window.location.href = href;
        });
    }

    // If arriving from another page with a hash (e.g. from /login to /#about)
    if (window.location.hash) {
        var initialHash = window.location.hash.substring(1);
        var initialTarget = document.getElementById(initialHash);
        if (initialTarget) {
            setTimeout(function() {
                var navbar = document.querySelector('.navbar');
                var navHeight = navbar ? navbar.offsetHeight : 68;
                var elementTop = initialTarget.getBoundingClientRect().top + window.pageYOffset;
                window.scrollTo({
                    top: Math.max(0, elementTop - navHeight - 12),
                    behavior: 'smooth'
                });
            }, 120);
        }
    }

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && menu.classList.contains('open')) {
            closeMenu();
        }
    });

    // Close on resize past 900px
    window.addEventListener('resize', function() {
        if (window.innerWidth > 900 && menu.classList.contains('open')) {
            closeMenu();
        }
    });
});
</script>
