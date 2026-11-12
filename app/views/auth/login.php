<?php
$title = "Login - Malangas Tourism";
require_once '../app/views/partials/header.php';
?>
<style>
/* ===== Auth Page Layout ===== */
.auth-page {
    min-height: calc(100vh - 5rem);
    width: 100%;
    display: flex;
    align-items: stretch;
}

/* ===== Left Panel ===== */
.auth-left {
    flex: 0 0 65%;
    max-width: 65%;
    min-width: 300px;
    background: linear-gradient(160deg, #1a5c35 0%, #123821 55%, #0d2917 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 2rem;
    padding: 3rem 2.5rem;
    position: relative;
    overflow: hidden;
}

/* decorative circles */
.auth-left::before {
    content: '';
    position: absolute;
    width: 420px;
    height: 420px;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,0.06);
    top: -120px;
    left: -120px;
}
.auth-left::after {
    content: '';
    position: absolute;
    width: 340px;
    height: 340px;
    border-radius: 50%;
    border: 1px solid rgba(232, 158, 27, 0.12);
    bottom: -100px;
    right: -100px;
}

.auth-left-inner {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 1.8rem;
}

.auth-logo-ring {
    width: 160px;
    height: 160px;
    border-radius: 50%;
    background: rgba(255,255,255,0.07);
    border: 2px solid rgba(232, 158, 27, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 0 12px rgba(232,158,27,0.06), 0 20px 50px rgba(0,0,0,0.3);
    padding: 12px;
}

.auth-logo-ring img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 50%;
}

.auth-logo-icon {
    font-size: 4rem;
    color: var(--secondary-color);
}

.auth-left-title {
    font-size: 2.6rem;
    font-weight: 900;
    color: #ffffff;
    line-height: 1.1;
    margin: 0;
    letter-spacing: -0.01em;
}

.auth-left-subtitle {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--secondary-color);
    text-transform: uppercase;
    letter-spacing: 0.18em;
    margin: 0.2rem 0 0 0;
}

.auth-left-divider {
    width: 48px;
    height: 3px;
    border-radius: 2px;
    background: linear-gradient(90deg, var(--secondary-color), transparent);
    margin: 0 auto;
}

.auth-left-tagline {
    color: rgba(255,255,255,0.65);
    font-size: 0.92rem;
    line-height: 1.75;
    max-width: 26rem;
}

/* ===== Right Panel ===== */
.auth-right {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1.5rem;
    background: #f4f8fc;
}

.auth-form-box {
    width: 100%;
    max-width: 380px;
    background: white;
    border-radius: 1.5rem;
    padding: 2.2rem 2rem 2rem;
    box-shadow: 0 20px 70px rgba(15, 23, 42, 0.10);
    border-top: 5px solid var(--primary-color);
}

.auth-form-title {
    font-size: 1.7rem;
    font-weight: 900;
    color: var(--primary-dark);
    margin: 0 0 0.35rem 0;
    text-align: center;
}

.auth-subtitle {
    color: var(--text-muted);
    font-size: 0.88rem;
    text-align: center;
    margin-bottom: 1.8rem;
}

.auth-subtitle a {
    color: var(--primary-color);
    font-weight: 600;
    text-decoration: none;
}

.auth-subtitle a:hover {
    text-decoration: underline;
}

/* Form fields */
.form-group {
    margin-bottom: 1.35rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    font-size: 0.85rem;
    color: var(--primary-dark);
    letter-spacing: 0.03em;
}

.form-control {
    width: 100%;
    padding: 0.85rem 1.1rem;
    border: 1.5px solid #dde4ed;
    border-radius: 0.85rem;
    font-size: 0.97rem;
    background: #f8fafc;
    transition: all 0.25s ease;
    color: #1e293b;
    box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04);
}

.form-control:focus {
    border-color: var(--primary-color);
    background: white;
    box-shadow: 0 0 0 3px rgba(26, 77, 46, 0.1);
    outline: none;
}

.form-control::placeholder {
    color: #b8c4d0;
}

/* Password toggle */
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
    font-size: 0.95rem;
    padding: 0;
    transition: color 0.2s;
}
.toggle-password:hover { color: var(--primary-color); }

.auth-form-aux {
    display: flex;
    justify-content: flex-end;
    margin-top: 0.35rem;
    margin-bottom: 1rem;
}
.auth-form-aux a {
    color: var(--primary-color);
    font-weight: 700;
    text-decoration: none;
}
.auth-form-aux a:hover {
    text-decoration: underline;
}

/* Submit button */
.auth-submit-btn {
    width: 100%;
    padding: 0.95rem;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    border: none;
    border-radius: 0.85rem;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 0.75rem;
    letter-spacing: 0.04em;
    box-shadow: 0 8px 22px rgba(26, 77, 46, 0.25);
}

.auth-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(26, 77, 46, 0.3);
}
.auth-submit-btn:active { transform: translateY(0); }

/* Switch link */
.auth-switch {
    text-align: center;
    margin-top: 1.8rem;
    font-size: 0.88rem;
    color: var(--text-muted);
}
.auth-switch a {
    color: var(--primary-color);
    font-weight: 700;
    text-decoration: none;
    transition: color 0.2s;
}
.auth-switch a:hover { color: var(--primary-dark); }

.auth-divider {
    border: none;
    border-top: 1px solid #e8edf3;
    margin: 1.8rem 0;
}

/* ===== Responsive ===== */
@media (max-width: 900px) {
    .auth-left {
        display: none;
    }
    .auth-page {
        min-height: calc(100vh - var(--navbar-height));
        align-items: center;
        justify-content: center;
        background: #f4f8fc;
    }
    .auth-right {
        flex: 1;
        width: 100%;
        padding: 2rem 1.25rem;
    }
}

@media (max-width: 520px) {
    .auth-right { padding: 1.5rem 1rem; }
    .auth-form-box { padding: 1.75rem 1.25rem; border-radius: 1.25rem; max-width: 100%; }
    .auth-form-title { font-size: 1.5rem; }
}
</style>

<div class="auth-page">
    <!-- LEFT: Logo Panel -->
    <div class="auth-left">
        <div class="auth-left-inner">
            <div class="auth-logo-ring">
                <?php if (file_exists(__DIR__ . '/../../../public/assets/images/logo.png')): ?>
                    <img src="<?= BASE_URL ?>/assets/images/logo.png" alt="Malangas Logo">
                <?php else: ?>
                    <i class="fa-solid fa-city auth-logo-icon"></i>
                <?php endif; ?>
            </div>

            <div>
                <h1 class="auth-left-title">Malangas</h1>
                <p class="auth-left-subtitle">Tourism Portal</p>
            </div>

            <div class="auth-left-divider"></div>

            <p class="auth-left-tagline">
                Your gateway to managing Malangas tourist spots, listings, and visitor activity.
            </p>
        </div>
    </div>

    <!-- RIGHT: Login Form -->
    <div class="auth-right">
        <div class="auth-form-box">
            <h2 class="auth-form-title">Welcome Back</h2>
            <p class="auth-subtitle">
                <a href="<?= BASE_URL ?>/">Sign in</a> to explore Malangas tourism spots
            </p>

            <?php if (isset($data['error'])): ?>
                <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?= htmlspecialchars($data['error']) ?>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/login" method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="you@example.com" required autofocus>
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
                <div class="auth-form-aux">
                    <a href="<?= BASE_URL ?>/forgot-password">Forgot password?</a>
                </div>
                <button type="submit" class="auth-submit-btn">Sign In</button>
            </form>

            <hr class="auth-divider">

            <p class="auth-switch">
                Don't have an account? <a href="<?= BASE_URL ?>/register">Register here</a>
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
