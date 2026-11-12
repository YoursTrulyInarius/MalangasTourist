<?php
// app/services/EmailService.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Require the Composer autoloader
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}

// Fallback for environments where Composer autoload is incomplete
if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
    $phpMailerBase = __DIR__ . '/../../vendor/PHPMailer/src/';
    require_once $phpMailerBase . 'Exception.php';
    require_once $phpMailerBase . 'PHPMailer.php';
    require_once $phpMailerBase . 'SMTP.php';
}

class EmailService {
    private $mail;

    // ─── System Colors (matching style.css) ───────────────────────
    private const C_PRIMARY      = '#1a4d2e'; // Deep Green
    private const C_PRIMARY_DARK = '#123821'; // Darker Green
    private const C_GOLD         = '#e89e1b'; // Golden Yellow
    private const C_GOLD_DARK    = '#cc8a16'; // Darker Gold
    private const C_BLUE         = '#2172b8'; // Ocean Blue
    private const C_BG           = '#f4f7f4'; // Light green-tinted bg
    private const C_CARD_BG      = '#ffffff';
    private const C_TEXT         = '#1a4d2e';
    private const C_TEXT_MUTED   = '#5a6a5a';
    private const C_BORDER       = '#d4e4d4';

    public function __construct() {
        $this->mail = new PHPMailer(true);

        // Server settings
        $this->mail->isSMTP();
        $this->mail->Host       = SMTP_HOST;
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = SMTP_USER;
        $this->mail->Password   = SMTP_PASS;
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = SMTP_PORT;

        // Default Sender
        $this->mail->setFrom(SMTP_USER, 'Malangas LGU Tourism');
    }

    // ─── Shared: Email Wrapper (header + footer) ─────────────────
    private function buildWrapper(string $innerContent): string {
        // Try to embed logo
        $logoPath = __DIR__ . '/../../public/assets/images/logo.png';
        $logoHtml = '';
        if (file_exists($logoPath)) {
            $this->mail->addEmbeddedImage($logoPath, 'mto_logo', 'logo.png', 'base64', 'image/png');
            $logoHtml = '<img src="cid:mto_logo" alt="Malangas Municipal Tourism Office" width="114" height="114" style="width:114px;height:114px;display:block;margin:0 auto;border-radius:50%;border:0;outline:none;text-decoration:none;">';
        }

        $primary      = self::C_PRIMARY;
        $primaryDark  = self::C_PRIMARY_DARK;
        $gold         = self::C_GOLD;
        $bg           = self::C_BG;
        $cardBg       = self::C_CARD_BG;
        $border       = self::C_BORDER;
        $textMuted    = self::C_TEXT_MUTED;

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Malangas Tourism</title>
</head>
<body style="margin:0;padding:0;background-color:{$bg};font-family:'Segoe UI',Arial,sans-serif;">

  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:{$bg};padding:30px 0;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;width:100%;">

          <!-- ── HEADER ────────────────────────────────────────── -->
          <tr>
            <td align="center" style="background:linear-gradient(135deg,{$primaryDark} 0%,{$primary} 60%,#25683f 100%);border-radius:16px 16px 0 0;padding:36px 30px 28px;">

              <!-- Logo round badge -->
              <table cellpadding="0" cellspacing="0" border="0" align="center" style="margin:0 auto 16px auto;">
                <tr>
                  <td align="center" style="line-height:0;padding:0;">
                    {$logoHtml}
                  </td>
                </tr>
              </table>

              <div style="color:#ffffff;font-size:20px;font-weight:700;letter-spacing:0.5px;margin-top:4px;">
                Municipal Tourism Office
              </div>
              <div style="color:rgba(255,255,255,0.75);font-size:13px;margin-top:4px;letter-spacing:1px;text-transform:uppercase;">
                Malangas, Zamboanga Sibugay
              </div>

              <!-- Gold divider -->
              <div style="width:60px;height:3px;background:{$gold};border-radius:2px;margin:16px auto 0;"></div>
            </td>
          </tr>

          <!-- ── BODY ──────────────────────────────────────────── -->
          <tr>
            <td style="background:{$cardBg};padding:36px 40px;border-left:1px solid {$border};border-right:1px solid {$border};">
              {$innerContent}
            </td>
          </tr>

          <!-- ── FOOTER ────────────────────────────────────────── -->
          <tr>
            <td style="background:{$primaryDark};border-radius:0 0 16px 16px;padding:24px 30px;text-align:center;">
              <p style="margin:0;color:rgba(255,255,255,0.6);font-size:12px;line-height:1.8;">
                This is an automated message from the<br>
                <strong style="color:rgba(255,255,255,0.9);">Malangas Tourism Management System</strong><br>
                For inquiries, contact the Malangas LGU Tourism Office.
              </p>
              <div style="margin-top:14px;">
                <span style="color:{$gold};font-size:11px;letter-spacing:1px;font-style:italic;">
                  Discover &bull; Experience &bull; Love Malangas
                </span>
              </div>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>
</html>
HTML;
    }

    // ─── Application Result Email ─────────────────────────────────
    public function sendApplicationResult($recipientEmail, $businessName, $status, $reason = '') {
        try {
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            $this->mail->addAddress($recipientEmail);
            $this->mail->isHTML(true);

            $primary    = self::C_PRIMARY;
            $gold       = self::C_GOLD;
            $blue       = self::C_BLUE;
            $textMuted  = self::C_TEXT_MUTED;

            if ($status === 'approved') {
                $this->mail->Subject = 'Application Approved – Malangas Tourism';
                $badgeBg    = '#e8f5ee';
                $badgeColor = $primary;
                $badgeText  = '✔ Approved';
                $accentColor = $primary;
                $headline   = 'Congratulations!';
                $bodyText   = 'We are pleased to inform you that your application for <strong style="color:' . $primary . ';">' . htmlspecialchars($businessName) . '</strong> has been <strong style="color:' . $primary . ';">approved</strong>.';
                $subText    = 'Your listing is now publicly visible on the Malangas Tourist Map. Visitors can discover your establishment!';
                $btnColor   = $primary;
                $btnText    = '🗺 View on Public Map';
                $btnHref    = BASE_URL . '/';
                $extraBox   = '';
            } else {
                $this->mail->Subject = 'Application Update – Malangas Tourism';
                $badgeBg    = '#fff4e5';
                $badgeColor = '#a35c00';
                $badgeText  = '✖ Not Approved';
                $accentColor = '#c0392b';
                $headline   = 'Application Update';
                $bodyText   = 'We regret to inform you that your application for <strong style="color:#c0392b;">' . htmlspecialchars($businessName) . '</strong> has <strong style="color:#c0392b;">not been approved</strong> at this time.';
                $subText    = 'Please review the reason below, make the necessary changes, and resubmit your application.';
                $btnColor   = $blue;
                $btnText    = '↩ Go to Dashboard';
                $btnHref    = BASE_URL . '/business';
                $reasonHtml = nl2br(htmlspecialchars($reason));
                $extraBox   = <<<BOX
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:24px 0;">
  <tr>
    <td style="background:#fff4f4;border-left:4px solid #c0392b;border-radius:4px;padding:18px 20px;">
      <div style="font-size:12px;font-weight:700;color:#c0392b;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">
        Reason for Rejection
      </div>
      <div style="font-size:14px;color:#7f1d1d;line-height:1.7;">{$reasonHtml}</div>
    </td>
  </tr>
</table>
BOX;
            }

            $innerContent = <<<HTML
<!-- Status Badge -->
<div style="text-align:center;margin-bottom:28px;">
  <span style="display:inline-block;background:{$badgeBg};color:{$badgeColor};font-size:13px;font-weight:700;padding:6px 18px;border-radius:999px;border:1.5px solid {$badgeColor};">
    {$badgeText}
  </span>
</div>

<!-- Headline -->
<h1 style="margin:0 0 8px;font-size:28px;font-weight:800;color:{$accentColor};text-align:center;">
  {$headline}
</h1>

<!-- Divider -->
<div style="width:50px;height:3px;background:{$gold};border-radius:2px;margin:0 auto 24px;"></div>

<!-- Greeting -->
<p style="margin:0 0 12px;font-size:15px;color:{$textMuted};">Dear Business Owner,</p>

<!-- Main message -->
<p style="margin:0 0 12px;font-size:15px;color:{$textMuted};line-height:1.7;">{$bodyText}</p>
<p style="margin:0 0 20px;font-size:14px;color:{$textMuted};line-height:1.7;">{$subText}</p>

{$extraBox}

<!-- CTA Button -->
<div style="text-align:center;margin:28px 0 4px;">
  <a href="{$btnHref}" style="display:inline-block;padding:14px 32px;background:{$btnColor};color:#ffffff;border-radius:999px;text-decoration:none;font-size:15px;font-weight:700;letter-spacing:0.3px;box-shadow:0 4px 14px rgba(26,77,46,0.3);">
    {$btnText}
  </a>
</div>
HTML;

            $this->mail->Body = $this->buildWrapper($innerContent);
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email could not be sent. Mailer Error: {$this->mail->ErrorInfo}");
            return false;
        }
    }

    // ─── Password Reset Email ─────────────────────────────────────
    public function sendPasswordReset($recipientEmail, $recipientName, $resetLink, $code) {
        try {
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            $this->mail->addAddress($recipientEmail);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Reset Your Malangas Tourism Password';

            $primary    = self::C_PRIMARY;
            $gold       = self::C_GOLD;
            $textMuted  = self::C_TEXT_MUTED;
            $name       = htmlspecialchars($recipientName);
            $codeHtml   = htmlspecialchars($code);

            $innerContent = <<<HTML
<!-- Icon -->
<div style="text-align:center;margin-bottom:20px;">
  <div style="display:inline-block;width:64px;height:64px;background:linear-gradient(135deg,{$primary},#25683f);border-radius:50%;line-height:64px;font-size:28px;color:#fff;box-shadow:0 4px 16px rgba(26,77,46,0.3);">
    🔐
  </div>
</div>

<!-- Headline -->
<h1 style="margin:0 0 8px;font-size:26px;font-weight:800;color:{$primary};text-align:center;">
  Password Reset
</h1>
<div style="width:50px;height:3px;background:{$gold};border-radius:2px;margin:0 auto 24px;"></div>

<!-- Greeting -->
<p style="margin:0 0 12px;font-size:15px;color:{$textMuted};">Hello, <strong style="color:{$primary};">{$name}</strong>,</p>
<p style="margin:0 0 24px;font-size:14px;color:{$textMuted};line-height:1.7;">
  We received a request to reset your Malangas Tourism account password. Use the 6-digit code below to proceed.
</p>

<!-- Code Box -->
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:24px;">
  <tr>
    <td align="center">
      <div style="background:linear-gradient(135deg,#eaf5ee,#d4edde);border:2px dashed {$primary};border-radius:16px;padding:28px 20px;display:inline-block;min-width:220px;">
        <div style="font-size:11px;font-weight:700;color:{$primary};text-transform:uppercase;letter-spacing:1px;margin-bottom:12px;">
          Your Reset Code
        </div>
        <div style="font-size:38px;font-weight:900;letter-spacing:10px;color:{$primary};font-family:'Courier New',monospace;">
          {$codeHtml}
        </div>
        <div style="font-size:12px;color:{$textMuted};margin-top:10px;">
          ⏱ Expires in <strong>1 hour</strong>
        </div>
      </div>
    </td>
  </tr>
</table>

<p style="margin:0 0 20px;font-size:14px;color:{$textMuted};line-height:1.7;text-align:center;">
  Enter this code on the password reset page to create a new password.
</p>

<!-- CTA Button -->
<div style="text-align:center;margin:4px 0 24px;">
  <a href="{$resetLink}" style="display:inline-block;padding:14px 32px;background:{$primary};color:#ffffff;border-radius:999px;text-decoration:none;font-size:15px;font-weight:700;letter-spacing:0.3px;box-shadow:0 4px 14px rgba(26,77,46,0.3);">
    Go to Reset Page
  </a>
</div>

<!-- Security note -->
<div style="background:#fff9e6;border:1px solid #f0d080;border-radius:10px;padding:14px 18px;text-align:center;">
  <p style="margin:0;font-size:13px;color:#7a5c00;">
    ⚠️ If you did not request this, you can safely ignore this email. Your account remains secure.
  </p>
</div>
HTML;

            $this->mail->Body = $this->buildWrapper($innerContent);
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Password reset email could not be sent. Mailer Error: {$this->mail->ErrorInfo}");
            return false;
        }
    }

    public function sendMonitoringUpdateApproval($recipientEmail, $spotName, $reason, $approvalSummary = '') {
        if (empty($recipientEmail)) {
            return false;
        }

        try {
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            $this->mail->addAddress($recipientEmail);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Monitoring Update Approved – Malangas Tourism';

            $name = htmlspecialchars($spotName);
            $reasonHtml = nl2br(htmlspecialchars($reason !== '' ? $reason : 'Your improvement update met the required standards and was approved by the LGU.'));
            $summaryHtml = nl2br(htmlspecialchars($approvalSummary !== '' ? $approvalSummary : 'The submitted repair and improvement work has been accepted and the monitoring requirement has been cleared.'));

            $innerContent = <<<HTML
<div style="text-align:center;margin-bottom:20px;">
  <div style="display:inline-block;width:64px;height:64px;background:linear-gradient(135deg,#166534,#22c55e);border-radius:50%;line-height:64px;font-size:28px;color:#fff;box-shadow:0 4px 16px rgba(22,101,52,0.3);">
    ✓
  </div>
</div>

<h1 style="margin:0 0 8px;font-size:26px;font-weight:800;color:#14532d;text-align:center;">
  Improvement Update Approved
</h1>
<div style="width:50px;height:3px;background:#22c55e;border-radius:2px;margin:0 auto 24px;"></div>

<p style="margin:0 0 12px;font-size:15px;color:#5a6a5a;">Dear Business Owner,</p>
<p style="margin:0 0 20px;font-size:14px;color:#5a6a5a;line-height:1.7;">
  The LGU reviewed your submitted improvement update for <strong>{$name}</strong> and has approved it.
</p>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
  <tr>
    <td style="background:#ecfdf5;border:1px solid #bbf7d0;border-left:4px solid #166534;border-radius:10px;padding:18px 20px;">
      <div style="font-size:12px;font-weight:700;color:#166534;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">
        LGU Note
      </div>
      <div style="font-size:14px;color:#14532d;line-height:1.7;">{$reasonHtml}</div>
    </td>
  </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
  <tr>
    <td style="background:#f0fdf4;border:1px solid #dcfce7;border-left:4px solid #15803d;border-radius:10px;padding:18px 20px;">
      <div style="font-size:12px;font-weight:700;color:#166534;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">
        Update Summary
      </div>
      <div style="font-size:14px;color:#14532d;line-height:1.7;">{$summaryHtml}</div>
    </td>
  </tr>
</table>

<p style="margin:0;font-size:14px;color:#5a6a5a;line-height:1.7;">
  You may continue monitoring your listing and keep your documents updated as needed.
</p>
HTML;

            $this->mail->Body = $this->buildWrapper($innerContent);
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Monitoring update approval email could not be sent. Mailer Error: {$this->mail->ErrorInfo}");
            return false;
        }
    }

    public function sendMonitoringUpdateDecline($recipientEmail, $spotName, $reason, $requiredImprovements = '') {
        if (empty($recipientEmail)) {
            return false;
        }

        try {
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            $this->mail->addAddress($recipientEmail);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Monitoring Update Declined – Malangas Tourism';

            $name = htmlspecialchars($spotName);
            $reasonHtml = nl2br(htmlspecialchars($reason));
            $requiredHtml = nl2br(htmlspecialchars($requiredImprovements !== '' ? $requiredImprovements : 'Please provide clearer repair evidence, more complete improvement details, and any additional documentation needed to verify compliance.'));

            $innerContent = <<<HTML
<div style="text-align:center;margin-bottom:20px;">
  <div style="display:inline-block;width:64px;height:64px;background:linear-gradient(135deg,#b91c1c,#ef4444);border-radius:50%;line-height:64px;font-size:28px;color:#fff;box-shadow:0 4px 16px rgba(185,28,28,0.3);">
    ⚠️
  </div>
</div>

<h1 style="margin:0 0 8px;font-size:26px;font-weight:800;color:#991b1b;text-align:center;">
  Improvement Update Declined
</h1>
<div style="width:50px;height:3px;background:#e89e1b;border-radius:2px;margin:0 auto 24px;"></div>

<p style="margin:0 0 12px;font-size:15px;color:#5a6a5a;">Dear Business Owner,</p>
<p style="margin:0 0 20px;font-size:14px;color:#5a6a5a;line-height:1.7;">
  The LGU reviewed your submitted improvement update for <strong>{$name}</strong> and found that it is not yet sufficient for approval.
</p>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
  <tr>
    <td style="background:#fff1f2;border:1px solid #fecdd3;border-left:4px solid #991b1b;border-radius:10px;padding:18px 20px;">
      <div style="font-size:12px;font-weight:700;color:#991b1b;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">
        Decline Reason
      </div>
      <div style="font-size:14px;color:#7f1d1d;line-height:1.7;">{$reasonHtml}</div>
    </td>
  </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
  <tr>
    <td style="background:#fff7ed;border:1px solid #fed7aa;border-left:4px solid #c2410c;border-radius:10px;padding:18px 20px;">
      <div style="font-size:12px;font-weight:700;color:#9a4d00;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">
        Required Improvements
      </div>
      <div style="font-size:14px;color:#7c2d12;line-height:1.7;">{$requiredHtml}</div>
    </td>
  </tr>
</table>

<p style="margin:0;font-size:14px;color:#5a6a5a;line-height:1.7;">
  Please submit a stronger and clearer improvement update with supporting evidence before the monitoring deadline.
</p>
HTML;

            $this->mail->Body = $this->buildWrapper($innerContent);
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Monitoring update decline email could not be sent. Mailer Error: {$this->mail->ErrorInfo}");
            return false;
        }
    }
}
