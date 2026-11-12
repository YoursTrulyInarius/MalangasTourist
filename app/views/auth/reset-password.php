<?php
$title = "Reset Password - Malangas Tourism";
require_once '../app/views/partials/header.php';
?>
<style>
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
.code-input-group {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: 0.5rem;
}
.code-digit {
    width: 100%;
    min-width: 0;
    height: 4rem;
    border: 2px solid #bae6fd;
    border-radius: 0.9rem;
    text-align: center;
    font-size: 1.8rem;
    font-weight: 800;
    background: #eff6ff;
    color: #1e293b;
}
.code-digit:focus {
    outline: none;
    border-color: #0369a1;
    box-shadow: 0 0 0 4px rgba(3, 105, 161, 0.1);
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
    padding: 0.95rem;
    background: linear-gradient(135deg, #0369a1 0%, #0b6aa8 100%);
    color: white;
    border: none;
    border-radius: 0.8rem;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 1rem;
    letter-spacing: 0.04em;
    box-shadow: 0 8px 22px rgba(3, 105, 161, 0.18);
}
.auth-submit-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 24px rgba(3, 105, 161, 0.22);
}
.auth-submit-btn:active {
    transform: translateY(0);
}
.auth-submit-btn.secondary {
    background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
    box-shadow: 0 8px 20px rgba(14, 165, 233, 0.18);
}
.auth-submit-btn.secondary:hover {
    box-shadow: 0 10px 24px rgba(14, 165, 233, 0.22);
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
.code-input-group {
    min-width: 0;
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

    .code-input-group {
        gap: 0.35rem;
    }

    .code-digit {
        height: 3.5rem;
        padding: 0.25rem;
        font-size: clamp(1.2rem, 6vw, 1.6rem);
    }

    .auth-form-box .form-control {
        min-width: 0;
        font-size: 16px;
    }
}
</style>

<script>
function setupCodeInputs() {
    const inputs = Array.from(document.querySelectorAll('.code-digit'));
    const hidden = document.getElementById('codeValue');

    inputs.forEach((input, index) => {
        input.addEventListener('input', () => {
            if (input.value.length > 1) {
                input.value = input.value.slice(0, 1);
            }
            if (input.value.match(/^[0-9]$/)) {
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            } else {
                input.value = '';
            }
            hidden.value = inputs.map(i => i.value).join('');
        });

        input.addEventListener('keydown', (event) => {
            if (event.key === 'Backspace' && !input.value && index > 0) {
                inputs[index - 1].focus();
            }
        });

        input.addEventListener('paste', (event) => {
            event.preventDefault();
            const pasted = (event.clipboardData || window.clipboardData).getData('text');
            const digits = pasted.replace(/\D/g, '').slice(0, 6);
            if (!digits) return;
            digits.split('').forEach((digit, i) => {
                if (inputs[i]) inputs[i].value = digit;
            });
            const nextEmpty = inputs[digits.length] || inputs[inputs.length - 1];
            nextEmpty.focus();
            hidden.value = inputs.map(i => i.value).join('');
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    setupCodeInputs();

    const form = document.getElementById('code-form');
    if (form) {
        form.addEventListener('submit', function() {
            const hidden = document.getElementById('codeValue');
            const digits = Array.from(document.querySelectorAll('.code-digit')).map(function(input) {
                return (input.value || '').trim();
            });
            const joined = digits.join('');
            hidden.value = joined;
        });
    }
});
</script>

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

            <h3>Verify Reset Code</h3>
            <p class="auth-subtitle">Enter the 6-digit code sent to your email.</p>

            <?php if (isset($data['error'])): ?>
                <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?= htmlspecialchars($data['error']) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($data['success'])): ?>
                <div class="alert alert-success" style="margin-bottom: 1.5rem;">
                    <i class="fa-solid fa-circle-check"></i>
                    <?= htmlspecialchars($data['success']) ?>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/reset-password/verify" method="POST" id="code-form">
                <div class="form-group">
                    <label>Reset Code</label>
                    <div class="code-input-group" id="codeInputs">
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="code-digit" id="code_digit_<?= $i ?>" name="code_digit_<?= $i ?>" required>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" name="code" id="codeValue">
                </div>
                <button type="submit" class="auth-submit-btn">Verify Code</button>
            </form>

            <form action="<?= BASE_URL ?>/reset-password/resend" method="POST" style="margin-top: 1rem;">
                <button type="submit" class="auth-submit-btn secondary">
                    Resend Code
                </button>
            </form>

            <hr class="auth-divider">

            <p class="auth-switch">
                Remembered your password? <a href="<?= BASE_URL ?>/login">Sign in</a>
            </p>
        </div>
    </div>
</div>


<?php require_once '../app/views/partials/footer.php'; ?>