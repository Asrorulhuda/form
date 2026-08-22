<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\AuditLog;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use App\Services\MailService;
use App\Services\WhatsAppService;

/**
 * Payment Controller
 * Handles user payment checkout, proof submission, and admin verification.
 */
class PaymentController
{
    private Payment $paymentModel;
    private Setting $settingModel;
    private User $userModel;

    public function __construct()
    {
        $this->paymentModel = new Payment();
        $this->settingModel = new Setting();
        $this->userModel    = new User();
    }

    /**
     * Public Checkout / Payment Instructions Page
     */
    public function showCheckout(string $userId): void
    {
        $user = $this->userModel->find((int) $userId);
        if (!$user) {
            Response::redirect(url('login'));
            return;
        }

        // Get pricing plans to find the price
        $plans = json_decode($this->settingModel->get('page_pricing_items', '[]'), true) ?: [];
        $selectedPlanInfo = null;
        foreach ($plans as $p) {
            if (strcasecmp($p['name'] ?? '', $user->plan ?? '') === 0) {
                $selectedPlanInfo = $p;
                break;
            }
        }

        // Payment configurations
        $qrisEnabled   = (int) $this->settingModel->get('payment_qris_enabled', '1') === 1;
        $qrisMerchant  = $this->settingModel->get('payment_qris_merchant', 'ASR FORM DIGITAL');
        $qrisImage     = $this->settingModel->get('payment_qris_image', '');
        $qrisInstr     = $this->settingModel->get('payment_qris_instructions', '');

        $tfEnabled     = (int) $this->settingModel->get('payment_transfer_enabled', '1') === 1;
        $bankAccounts  = json_decode($this->settingModel->get('payment_bank_accounts', '[]'), true) ?: [];
        $tfInstr       = $this->settingModel->get('payment_transfer_instructions', '');

        View::guest('payment.checkout', [
            'title'            => 'Pembayaran & Konfirmasi Paket — ASR FORM',
            'user'             => $user,
            'planInfo'         => $selectedPlanInfo,
            'qrisEnabled'      => $qrisEnabled,
            'qrisMerchant'     => $qrisMerchant,
            'qrisImage'        => $qrisImage,
            'qrisInstr'        => $qrisInstr,
            'tfEnabled'        => $tfEnabled,
            'bankAccounts'     => $bankAccounts,
            'tfInstr'          => $tfInstr,
        ]);
    }

    /**
     * Process Uploaded Payment Proof
     */
    public function submitProof(): void
    {
        CSRF::check();

        $userId       = (int) ($_POST['user_id'] ?? 0);
        $user         = $this->userModel->find($userId);
        if (!$user) {
            Response::redirect(url('login'));
            return;
        }

        $method       = $_POST['payment_method'] ?? 'qris';
        $senderName   = trim($_POST['sender_name'] ?? '');
        $senderPhone  = trim($_POST['sender_phone'] ?? '');
        $bankName     = trim($_POST['bank_name'] ?? '');
        $accountNum   = trim($_POST['account_number'] ?? '');
        $amount       = (float) ($_POST['amount'] ?? 0);
        $notes        = trim($_POST['notes'] ?? '');

        // Validation for file upload
        if (empty($_FILES['proof_file']['name']) || $_FILES['proof_file']['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Mohon unggah berkas bukti transfer / pembayaran Anda.');
            Response::redirect(url("payment/{$userId}"));
            return;
        }

        $file = $_FILES['proof_file'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'application/pdf'];
        if (!in_array($file['type'], $allowedTypes)) {
            Session::flash('error', 'Format berkas tidak valid. Gunakan gambar (JPG, PNG, WEBP) atau berkas PDF.');
            Response::redirect(url("payment/{$userId}"));
            return;
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            Session::flash('error', 'Ukuran berkas terlalu besar. Maksimal 5MB.');
            Response::redirect(url("payment/{$userId}"));
            return;
        }

        // Upload folder
        $uploadDir = BASE_PATH . '/public/uploads/payments';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'proof_' . $userId . '_' . time() . '_' . substr(md5(uniqid()), 0, 6) . '.' . $ext;
        $destPath = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            Session::flash('error', 'Gagal mengunggah berkas ke server.');
            Response::redirect(url("payment/{$userId}"));
            return;
        }

        $relPath = 'uploads/payments/' . $filename;

        // Save payment record
        $paymentId = $this->paymentModel->create([
            'user_id'        => $userId,
            'plan'           => $user->plan ?? 'Pro',
            'amount'         => $amount,
            'payment_method' => $method,
            'bank_name'      => $bankName,
            'account_number' => $accountNum,
            'sender_name'    => $senderName,
            'sender_phone'   => $senderPhone,
            'proof_file'     => $relPath,
            'notes'          => $notes,
            'status'         => 'pending',
        ]);

        AuditLog::log('payment', 'payments', (int)$paymentId, "User {$user->name} mengunggah bukti pembayaran paket {$user->plan}");

        // ─── Trigger WhatsApp Gateway Notification (Parallel Dispatch) ───
        $wa = WhatsAppService::getInstance();
        $siteName = $this->settingModel->get('site_name', 'ASR FORM');
        
        $adminWaMsg = "🔔 *PEMBAYARAN BARU MASUK — {$siteName}*\n\n"
                    . "👤 *Nama:* {$user->name}\n"
                    . "📧 *Email:* {$user->email}\n"
                    . "📦 *Paket:* {$user->plan}\n"
                    . "💳 *Metode:* " . strtoupper($method) . "\n"
                    . "📱 *No. WhatsApp:* {$senderPhone}\n"
                    . "📅 *Waktu:* " . date('d/m/Y H:i') . " WIB\n\n"
                    . "Silakan periksa dan verifikasi pembayaran di dashboard admin:\n" . url('admin/payments');

        $userWaMsg = "Halo *{$user->name}*,\n\n"
                   . "Terima kasih! Bukti pembayaran untuk paket *{$user->plan}* di *{$siteName}* telah kami terima.\n\n"
                   . "Status: ⏳ *Menunggu Verifikasi Admin*\n"
                   . "Kami akan segera memverifikasi pembayaran Anda dan mengaktifkan akun Anda secepatnya.";

        $wa->notifyBoth($senderPhone, $userWaMsg, $adminWaMsg);

        // ─── Trigger Gmail SMTP Gateway Notification to Admin ───
        $mail = MailService::getInstance();
        $emailSubject = "[Pembayaran Baru] {$user->name} - Paket {$user->plan}";
        $emailBody = "<h2>Pembayaran Baru Masuk</h2>"
                   . "<p>Pengguna berikut telah mengunggah bukti pembayaran:</p>"
                   . "<ul>"
                   . "<li><strong>Nama:</strong> {$user->name}</li>"
                   . "<li><strong>Email:</strong> {$user->email}</li>"
                   . "<li><strong>Paket:</strong> {$user->plan}</li>"
                   . "<li><strong>Metode:</strong> " . strtoupper($method) . "</li>"
                   . "<li><strong>No. WhatsApp:</strong> {$senderPhone}</li>"
                   . "<li><strong>Waktu:</strong> " . date('d/m/Y H:i') . " WIB</li>"
                   . "</ul>"
                   . "<p>Silakan login ke admin panel untuk memverifikasi pembayaran.</p>";
        $mail->notifyAdmin($emailSubject, $emailBody);

        Response::redirect(url("payment/{$userId}/success"));
    }

    /**
     * Payment Submission Success Page
     */
    public function showSuccess(string $userId): void
    {
        $user = $this->userModel->find((int) $userId);
        if (!$user) {
            Response::redirect(url('login'));
            return;
        }

        $payment = $this->paymentModel->findByUser((int) $userId);

        View::guest('payment.success', [
            'title'   => 'Konfirmasi Pembayaran Terkirim — ASR FORM',
            'user'    => $user,
            'payment' => $payment,
        ]);
    }

    // ──────────────────────────────────────────
    // ADMIN PAYMENT MANAGEMENT
    // ──────────────────────────────────────────

    /**
     * Admin: Payments List & Verification Panel
     */
    public function index(): void
    {
        $page = (int) ($_GET['page'] ?? 1);
        $filters = [
            'status' => $_GET['status'] ?? '',
            'method' => $_GET['method'] ?? '',
            'search' => trim($_GET['search'] ?? ''),
        ];

        $result = $this->paymentModel->getAll($filters, $page, 15);

        View::page('payments.index', [
            'title'     => 'Kelola Pembayaran',
            'pageTitle' => 'Kelola & Verifikasi Pembayaran',
            'payments'  => $result['data'],
            'total'     => $result['total'],
            'page'      => $result['page'],
            'lastPage'  => $result['lastPage'],
            'filters'   => $filters,
        ]);
    }

    /**
     * Admin: Verify Payment (Approve & Activate User)
     */
    public function verify(string $id): void
    {
        CSRF::check();

        $paymentId = (int) $id;
        $payment = $this->paymentModel->find($paymentId);
        if (!$payment) {
            Response::redirectWith(url('payments'), 'error', 'Data pembayaran tidak ditemukan.');
            return;
        }

        $admin = Auth::user();
        $this->paymentModel->verify($paymentId, (int)$admin->id);
        AuditLog::log('verify', 'payments', $paymentId, "Verifikasi pembayaran ID #{$paymentId} untuk user {$payment->user_name}");

        // ─── Trigger WhatsApp Gateway Notification to User ───
        $wa = WhatsAppService::getInstance();
        $siteName = $this->settingModel->get('site_name', 'ASR FORM');
        $loginUrl = url('login');

        if (!empty($payment->sender_phone)) {
            $msg = "🎉 *Selamat {$payment->user_name}!*\n\n"
                 . "Pembayaran Anda untuk paket *{$payment->plan}* di *{$siteName}* telah *DIVERIFIKASI (ACC)* oleh Administrator.\n\n"
                 . "✅ *Status Akun:* AKTIF\n"
                 . "Silakan login ke akun Anda sekarang:\n"
                 . "🔗 {$loginUrl}\n\n"
                 . "Selamat membuat formulir & dokumen otomatis!";
            $wa->notifyUser($payment->sender_phone, $msg);
        }

        // ─── Trigger Gmail SMTP Gateway Notification to User ───
        $mail = MailService::getInstance();
        if (!empty($payment->user_email)) {
            $subj = "Pembayaran Diverifikasi — Akun {$siteName} Anda Telah Aktif!";
            $html = "<h2>Selamat, Pembayaran Anda Telah Diverifikasi!</h2>"
                  . "<p>Halo <strong>{$payment->user_name}</strong>,</p>"
                  . "<p>Pembayaran Anda untuk paket <strong>{$payment->plan}</strong> telah diverifikasi oleh Administrator.</p>"
                  . "<p>Akun Anda kini telah <strong>AKTIF</strong> dan siap digunakan.</p>"
                  . "<p><a href='{$loginUrl}' style='display:inline-block;padding:10px 20px;background:#4f46e5;color:#ffffff;text-decoration:none;border-radius:6px;font-weight:bold;'>Masuk ke Dashboard</a></p>";
            $mail->notifyUser($payment->user_email, $subj, $html);
        }

        Response::redirectWith(url('payments'), 'success', 'Pembayaran berhasil diverifikasi dan akun pengguna telah aktif.');
    }

    /**
     * Admin: Reject Payment
     */
    public function reject(string $id): void
    {
        CSRF::check();

        $paymentId = (int) $id;
        $payment = $this->paymentModel->find($paymentId);
        if (!$payment) {
            Response::redirectWith(url('payments'), 'error', 'Data pembayaran tidak ditemukan.');
            return;
        }

        $notes = trim($_POST['notes'] ?? 'Bukti pembayaran tidak sesuai atau tidak valid.');
        $admin = Auth::user();

        $this->paymentModel->reject($paymentId, (int)$admin->id, $notes);
        AuditLog::log('reject', 'payments', $paymentId, "Menolak pembayaran ID #{$paymentId} untuk user {$payment->user_name}");

        // ─── Trigger WhatsApp Gateway Notification to User ───
        if (!empty($payment->sender_phone)) {
            $wa = WhatsAppService::getInstance();
            $siteName = $this->settingModel->get('site_name', 'ASR FORM');
            $msg = "Halo *{$payment->user_name}*,\n\n"
                 . "Mohon maaf, bukti pembayaran paket *{$payment->plan}* Anda di *{$siteName}* ditolak oleh Administrator.\n\n"
                 . "Alasan: *{$notes}*\n\n"
                 . "Silakan hubungi administrator atau lakukan pembayaran ulang jika diperlukan.";
            $wa->notifyUser($payment->sender_phone, $msg);
        }

        Response::redirectWith(url('payments'), 'success', 'Pembayaran telah ditolak.');
    }
}
