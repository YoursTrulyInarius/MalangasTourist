<?php
$title = "Forgot Password - Malangas Tourism";
require_once '../app/views/partials/header.php';
?>
<style>
.auth-page {
    min-height: calc(100vh - 80px);
    background: linear-gradient(180deg, #0d2917 0%, #123821 48%, #1a4d2e 100%);
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}
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
    background: rgba(255, 255, 255, 0.98);
    border-radius: 1.5rem;
    padding: 3rem;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.18);
    border: 1px solid rgba(255, 255, 255, 0.55);
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
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    border: 2px solid var(--secondary-color);
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
    color: var(--primary-dark);
    margin: 0;
}
.auth-form-header-text p {
    font-size: 0.75rem;
    color: var(--secondary-color);
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin: 0.2rem 0 0 0;
    font-weight: 700;
}
.auth-form-box h3 {
    font-size: 1.9rem;
    font-weight: 900;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
    text-align: center;
}
.auth-form-box .auth-subtitle {
    color: #475569;
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
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--primary-color);
    letter-spacing: 0.02em;
}
.auth-form-box .form-control {
    border: 2px solid rgba(26, 77, 46, 0.18);
    border-radius: 1rem;
    padding: 1rem 1.2rem;
    font-size: 1rem;
    background: #f8fafc;
    transition: all 0.3s ease;
    color: #1f2937;
}
.auth-form-box .form-control:focus {
    border-color: rgba(26, 77, 46, 0.4);
    background: white;
    box-shadow: 0 0 0 4px rgba(26, 77, 46, 0.08);
    outline: none;
}
.auth-form-box .form-control::placeholder {
    color: #93c5fd;
}
.auth-submit-btn {
    width: 100%;
    padding: 1rem;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 1rem;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 1rem;
    letter-spacing: 0.03em;
    box-shadow: 0 10px 20px rgba(26, 77, 46, 0.22);
}
.auth-submit-btn:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: 0 12px 24px rgba(26, 77, 46, 0.24);
}
.auth-switch {
    text-align: center;
    margin-top: 2rem;
    font-size: 0.95rem;
    color: #475569;
}
.auth-switch a {
    color: var(--secondary-color);
    font-weight: 800;
    text-decoration: none;
    transition: color 0.2s;
}
.auth-switch a:hover {
    color: #cc8a16;
}
.auth-divider {
    border: none;
    border-top: 1px solid rgba(26, 77, 46, 0.12);
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
        font-size: 1.6rem;
    }
}
</style>

<div class="auth-page">
    <div class="auth-right">
        <div class="auth-form-box">
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

            <h3>Forgot Password</h3>
            <p class="auth-subtitle">Enter your email, and we will send a 6-digit reset code.</p>

            <?php if (isset($data['error'])): ?>
                <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?= htmlspecialchars($data['error']) ?>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/forgot-password" method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="you@example.com" required autofocus>
                </div>
                <button type="submit" class="auth-submit-btn">Send Reset Code</button>
            </form>

            <hr class="auth-divider">

            <p class="auth-switch">
                Remembered your password? <a href="<?= BASE_URL ?>/login">Sign in</a>
            </p>
        </div>
    </div>
</div>

<?php require_once '../app/views/partials/footer.php'; ?>