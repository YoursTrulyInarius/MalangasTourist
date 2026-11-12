<?php
$title = "Tourist Register - Malangas Tourism";
$hide_navbar = true;
$hide_footer = true;
require_once '../app/views/partials/header.php';
?>
<style>
.input-icon-wrap { position: relative; }
.input-icon-wrap .form-control { padding-right: 2.8rem; }
.toggle-password {
    position: absolute;
    right: 0.9rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: var(--text-muted);
    font-size: 1rem;
    padding: 0;
    transition: color 0.2s;
}
.toggle-password:hover { color: #0369a1; }

.auth-page {
    min-height: calc(100vh - 80px);
    background: #f0f9ff;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.auth-left { display: none; }

.auth-right {
    flex: 1;
    background: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    position: relative;
    z-index: 10;
}

.auth-form-box {
    width: 100%;
    max-width: 480px;
    background: white;
    border-radius: 1.5rem;
    padding: 3rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    max-height: 90vh;
    overflow-y: auto;
}

.auth-form-header {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 2.5rem;
    gap: 1rem;
}

.auth-form-header-logo {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.auth-form-header-logo img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: contain;
}

.auth-form-header-text h2 {
    font-size: 1.3rem;
    font-weight: 800;
    color: #0369a1;
    margin: 0;
}

.auth-form-header-text p {
    font-size: 0.75rem;
    color: #06b6d4;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin: 0.2rem 0 0 0;
    font-weight: 600;
}

.auth-form-box h3 {
    font-size: 1.8rem;
    font-weight: 800;
    color: #0369a1;
    margin-bottom: 0.5rem;
    text-align: center;
}

.auth-form-box .auth-subtitle {
    color: var(--text-muted);
    font-size: 0.95rem;
    margin-bottom: 2.5rem;
    text-align: center;
}

.auth-form-box .form-group {
    margin-bottom: 1.5rem;
}

.auth-form-box label {
    display: block;
    margin-bottom: 0.6rem;
    font-weight: 600;
    font-size: 0.9rem;
    color: #0369a1;
    letter-spacing: 0.02em;
}

.auth-form-box .form-control {
    border: 2px solid #e0f2fe;
    border-radius: 0.8rem;
    padding: 0.95rem 1.2rem;
    font-size: 1rem;
    background: #f0f9ff;
    transition: all 0.3s ease;
    color: #1e293b;
}

.auth-form-box .form-control:focus {
    border-color: #0369a1;
    background: white;
    box-shadow: 0 0 0 4px rgba(3, 105, 161, 0.1);
    outline: none;
}

.auth-form-box .form-control::placeholder {
    color: #cbd5e1;
}

.auth-submit-btn {
    width: 100%;
    padding: 1rem;
    background: #0369a1;
    color: white;
    border: none;
    border-radius: 0.8rem;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 1rem;
    letter-spacing: 0.03em;
    box-shadow: 0 4px 12px rgba(3, 105, 161, 0.2);
}

.auth-submit-btn:hover {
    background: #0284c7;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(3, 105, 161, 0.3);
}

.auth-submit-btn:active {
    transform: translateY(0);
}

.auth-switch {
    text-align: center;
    margin-top: 2rem;
    font-size: 0.9rem;
    color: var(--text-muted);
}

.auth-switch a {
    color: #0369a1;
    font-weight: 700;
    text-decoration: none;
    transition: color 0.2s;
}

.auth-switch a:hover {
    color: #0284c7;
}

.auth-divider {
    border: none;
    border-top: 1px solid #e2e8f0;
    margin: 2rem 0;
}

@media (max-width: 768px) {
    .auth-form-box {
        padding: 2rem;
        max-width: 100%;
    }

    .auth-form-header {
        margin-bottom: 2rem;
    }

    .auth-form-header-logo {
        width: 70px;
        height: 70px;
    }

    .auth-form-header-text h2 {
        font-size: 1.1rem;
    }

    .auth-form-box h3 {
        font-size: 1.5rem;
    }
}

.auth-page,
.auth-page * {
    box-sizing: border-box;
}

.auth-page {
    overflow: visible;
}

.auth-right,
.auth-form-box,
.auth-form-box .form-group {
    min-width: 0;
}

.auth-form-box {
    max-height: none;
    overflow: visible;
}

@media (max-width: 480px) {
    .auth-page {
        align-items: flex-start;
        padding: 1rem 0.75rem;
    }

    .auth-right {
        width: 100%;
    }

    .auth-form-box {
        max-width: 100%;
        padding: 1.5rem 1rem;
        border-radius: 1rem;
    }

    .auth-form-header {
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .auth-form-header-logo {
        width: 60px;
        height: 60px;
        flex: 0 0 60px;
    }

    .auth-form-box .form-control {
        min-width: 0;
        font-size: 16px;
    }
}
</style>

<div class="auth-page">
    <div class="auth-right">
        <div class="auth-form-box">
            <!-- Header with Logo -->
            <div class="auth-form-header">
                <div class="auth-form-header-logo">
                    <?php if (file_exists(__DIR__ . '/../../../public/assets/images/logo.png')): ?>
                        <img src="<?= BASE_URL ?>/assets/images/logo.png" alt="Logo" style="width: 100%; height: 100%; border-radius: 50%; object-fit: contain;">
                    <?php else: ?>
                        <i class="fa-solid fa-wave"></i>
                    <?php endif; ?>
                </div>
                <div class="auth-form-header-text">
                    <h2>Malangas</h2>
                    <p>Tourist Portal</p>
                </div>
            </div>

            <h3>Create Account</h3>
            <p class="auth-subtitle">Join us to discover amazing destinations</p>

            <?php if (isset($data['error'])): ?>
                <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?= htmlspecialchars($data['error']) ?>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/tourist/register" method="POST">
                <input type="hidden" name="next" value="<?= isset($data['next']) ? htmlspecialchars($data['next']) : '' ?>">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="John Doe" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="you@example.com" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="form-control" placeholder="Optional, 11 digits"
                        inputmode="numeric" pattern="\d*" maxlength="11"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-icon-wrap">
                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('password', this)" tabindex="-1">
                            <i class="fa-solid fa-eye" id="password-icon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="auth-submit-btn">Create Account</button>
            </form>

            <hr class="auth-divider">

            <p class="auth-switch">
                Already have an account? <a href="<?= BASE_URL ?>/tourist/login<?= !empty($data['next']) ? '?next=' . urlencode($data['next']) : '' ?>">Sign in</a>
            </p>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>

<?php require_once '../app/views/partials/footer.php'; ?>
