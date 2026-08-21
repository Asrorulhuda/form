<?php
use App\Core\CSRF;

$featureItems = json_decode($featuresSettings['page_features_items'] ?? '[]', true) ?: [];
$pricingItems = json_decode($pricingSettings['page_pricing_items'] ?? '[]', true) ?: [];
?>

<div style="max-width: 960px;">
    <div class="card fade-in">
        <div class="card-header">
            <h3 class="card-title">Kelola Halaman Publik</h3>
            <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">
                Atur konten halaman publik dengan mudah tanpa perlu menulis kode rumit.
            </p>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= url('settings/pages/update') ?>" id="pages-form">
                <?= CSRF::field() ?>

                <!-- Tab Navigation -->
                <div class="page-tabs" id="page-tabs">
                    <button type="button" class="page-tab active" data-tab="tab-features">✨ Fitur</button>
                    <button type="button" class="page-tab" data-tab="tab-pricing">🏷️ Paket & Harga</button>
                    <button type="button" class="page-tab" data-tab="tab-about">🏢 Tentang Kami</button>
                    <button type="button" class="page-tab" data-tab="tab-contact">📬 Kontak</button>
                    <button type="button" class="page-tab" data-tab="tab-privacy">🔒 Kebijakan Privasi</button>
                    <button type="button" class="page-tab" data-tab="tab-terms">📜 Syarat & Ketentuan</button>
                </div>

                <!-- ═══════════════════════════════════════════
                     TAB 1: FITUR (VISUAL REPEATER)
                     ═══════════════════════════════════════════ -->
                <div class="page-tab-content active" id="tab-features">
                    <div class="flex items-center justify-between mb-4 p-3" style="background: var(--bg-subtle); border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                        <div>
                            <div style="font-weight: 700; font-size: 14px;">Status Halaman Fitur</div>
                            <div style="font-size: 12px; color: var(--text-muted);">Tampilkan halaman /features di website publik</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="page_features_enabled" value="1" <?= ($featuresSettings['page_features_enabled'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="grid-2 mb-4">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Judul Halaman</label>
                            <input type="text" name="page_features_title" class="form-control" value="<?= e($featuresSettings['page_features_title'] ?? 'Fitur ASR FORM') ?>" placeholder="Fitur ASR FORM">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Subtitle / Deskripsi Singkat</label>
                            <input type="text" name="page_features_subtitle" class="form-control" value="<?= e($featuresSettings['page_features_subtitle'] ?? '') ?>" placeholder="Semua yang Anda butuhkan untuk membuat formulir digital...">
                        </div>
                    </div>

                    <div style="border-top: 1px solid var(--border-subtle); padding-top: 16px; margin-top: 20px;">
                        <div class="flex items-center justify-between mb-3">
                            <h4 style="font-size: 15px; font-weight: 700; color: var(--text-primary); margin: 0;">
                                📋 Daftar Item Fitur (<span id="features-count"><?= count($featureItems) ?></span>)
                            </h4>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="addFeatureItem()">
                                ➕ Tambah Fitur Baru
                            </button>
                        </div>

                        <div id="features-container" style="display: flex; flex-direction: column; gap: 14px;">
                            <?php foreach ($featureItems as $idx => $item): ?>
                                <div class="feature-item-card" style="background: #ffffff; border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 16px; position: relative; box-shadow: var(--shadow-xs);">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                            <span style="font-weight: 700; font-size: 13px; color: var(--primary-600);">Fitur #<span class="feature-number"><?= $idx + 1 ?></span></span>
                                        </div>
                                        <button type="button" class="btn btn-sm" style="color: var(--danger-500); background: transparent; padding: 2px 8px;" onclick="this.closest('.feature-item-card').remove(); updateFeatureCounts();" title="Hapus">
                                            🗑️ Hapus
                                        </button>
                                    </div>
                                    <div style="display: grid; grid-template-columns: 80px 1fr; gap: 12px; margin-bottom: 10px;">
                                        <div>
                                            <label class="form-label" style="font-size: 11px;">Icon / Emoji</label>
                                            <input type="text" name="features[<?= $idx ?>][icon]" class="form-control text-center" style="font-size: 20px;" value="<?= e($item['icon'] ?? '📝') ?>" maxlength="4">
                                        </div>
                                        <div>
                                            <label class="form-label" style="font-size: 11px;">Nama Fitur</label>
                                            <input type="text" name="features[<?= $idx ?>][title]" class="form-control" value="<?= e($item['title'] ?? '') ?>" placeholder="Contoh: Form Builder">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="form-label" style="font-size: 11px;">Penjelasan Singkat</label>
                                        <input type="text" name="features[<?= $idx ?>][desc]" class="form-control" value="<?= e($item['desc'] ?? '') ?>" placeholder="Penjelasan kegunaan fitur ini...">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- ═══════════════════════════════════════════
                     TAB 2: PAKET & HARGA (VISUAL REPEATER)
                     ═══════════════════════════════════════════ -->
                <div class="page-tab-content" id="tab-pricing">
                    <div class="flex items-center justify-between mb-4 p-3" style="background: var(--bg-subtle); border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                        <div>
                            <div style="font-weight: 700; font-size: 14px;">Status Halaman Pricing</div>
                            <div style="font-size: 12px; color: var(--text-muted);">Tampilkan halaman /pricing di website publik</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="page_pricing_enabled" value="1" <?= ($pricingSettings['page_pricing_enabled'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="grid-2 mb-4">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Judul Halaman</label>
                            <input type="text" name="page_pricing_title" class="form-control" value="<?= e($pricingSettings['page_pricing_title'] ?? 'Paket & Harga') ?>" placeholder="Paket & Harga">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Subtitle / Keterangan</label>
                            <input type="text" name="page_pricing_subtitle" class="form-control" value="<?= e($pricingSettings['page_pricing_subtitle'] ?? '') ?>" placeholder="Pilih paket yang sesuai dengan kebutuhan Anda.">
                        </div>
                    </div>

                    <div style="border-top: 1px solid var(--border-subtle); padding-top: 16px; margin-top: 20px;">
                        <div class="flex items-center justify-between mb-3">
                            <h4 style="font-size: 15px; font-weight: 700; color: var(--text-primary); margin: 0;">
                                🏷️ Daftar Kartu Paket (<span id="pricing-count"><?= count($pricingItems) ?></span>)
                            </h4>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="addPricingItem()">
                                ➕ Tambah Paket Baru
                            </button>
                        </div>

                        <div id="pricing-container" style="display: flex; flex-direction: column; gap: 16px;">
                            <?php foreach ($pricingItems as $idx => $plan): ?>
                                <div class="pricing-item-card" style="background: #ffffff; border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 18px; position: relative; box-shadow: var(--shadow-xs);">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                            <span style="font-weight: 700; font-size: 14px; color: var(--primary-600);">Paket #<span class="pricing-number"><?= $idx + 1 ?></span></span>
                                            <label style="font-size: 12px; display: flex; align-items: center; gap: 4px; cursor: pointer; margin-left: 12px;">
                                                <input type="checkbox" name="pricing[<?= $idx ?>][highlighted]" value="1" <?= !empty($plan['highlighted']) ? 'checked' : '' ?>>
                                                <span style="color: var(--primary-700); font-weight: 600;">⭐ Tandai Sebagai Populer / Highlight</span>
                                            </label>
                                        </div>
                                        <button type="button" class="btn btn-sm" style="color: var(--danger-500); background: transparent; padding: 2px 8px;" onclick="this.closest('.pricing-item-card').remove(); updatePricingCounts();" title="Hapus">
                                            🗑️ Hapus
                                        </button>
                                    </div>

                                    <div class="grid-3 mb-3">
                                        <div>
                                            <label class="form-label" style="font-size: 11px;">Nama Paket</label>
                                            <input type="text" name="pricing[<?= $idx ?>][name]" class="form-control" value="<?= e($plan['name'] ?? '') ?>" placeholder="Contoh: Gratis / Pro / Enterprise">
                                        </div>
                                        <div>
                                            <label class="form-label" style="font-size: 11px;">Harga</label>
                                            <input type="text" name="pricing[<?= $idx ?>][price]" class="form-control" value="<?= e($plan['price'] ?? '') ?>" placeholder="Contoh: Rp 0 / Rp 99.000 / Hubungi Kami">
                                        </div>
                                        <div>
                                            <label class="form-label" style="font-size: 11px;">Periode</label>
                                            <input type="text" name="pricing[<?= $idx ?>][period]" class="form-control" value="<?= e($plan['period'] ?? '') ?>" placeholder="Contoh: selamanya / per bulan">
                                        </div>
                                    </div>

                                    <div class="grid-2 mb-3">
                                        <div>
                                            <label class="form-label" style="font-size: 11px;">Deskripsi Singkat Paket</label>
                                            <input type="text" name="pricing[<?= $idx ?>][desc]" class="form-control" value="<?= e($plan['desc'] ?? '') ?>" placeholder="Untuk individu dan penggunaan dasar">
                                        </div>
                                        <div>
                                            <label class="form-label" style="font-size: 11px;">Teks Tombol Aksi (CTA)</label>
                                            <input type="text" name="pricing[<?= $idx ?>][cta]" class="form-control" value="<?= e($plan['cta'] ?? 'Mulai Gratis') ?>" placeholder="Mulai Gratis / Hubungi Kami">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label" style="font-size: 11px;">Daftar Fitur yang Didapat (Tulis 1 baris per fitur)</label>
                                        <textarea name="pricing[<?= $idx ?>][features]" class="form-control" rows="4" placeholder="Buat form tanpa batas&#10;Kumpulkan respons&#10;Template dokumen Word&#10;Tanpa iklan"><?php
                                            if (!empty($plan['features']) && is_array($plan['features'])) {
                                                echo e(implode("\n", $plan['features']));
                                            }
                                        ?></textarea>
                                        <small style="color: var(--text-muted); font-size: 11px;">💡 Cukup tekan Enter untuk menambah baris fitur baru.</small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- ═══════════════════════════════════════════
                     TAB 3: TENTANG KAMI (ABOUT)
                     ═══════════════════════════════════════════ -->
                <div class="page-tab-content" id="tab-about">
                    <div class="flex items-center justify-between mb-4 p-3" style="background: var(--bg-subtle); border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                        <div>
                            <div style="font-weight: 700; font-size: 14px;">Status Halaman About</div>
                            <div style="font-size: 12px; color: var(--text-muted);">Tampilkan halaman /about di website publik</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="page_about_enabled" value="1" <?= ($aboutSettings['page_about_enabled'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Judul Halaman</label>
                        <input type="text" name="page_about_title" class="form-control" value="<?= e($aboutSettings['page_about_title'] ?? 'Tentang ASR FORM') ?>">
                    </div>

                    <div class="form-group">
                        <div class="flex items-center justify-between mb-2">
                            <label class="form-label" style="margin-bottom: 0;">Konten Penjelasan Aplikasi</label>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="loadAboutTemplate()" style="font-size: 11px; padding: 2px 8px;">
                                📄 Muat Template Default
                            </button>
                        </div>
                        <textarea id="about-content" name="page_about_content" class="form-control" rows="12" style="line-height: 1.6;"><?= e($aboutSettings['page_about_content'] ?? '') ?></textarea>
                        <small style="color: var(--text-muted); font-size: 12px;">Mendukung teks biasa dan tag HTML sederhana seperti &lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Visi Produk (Opsional)</label>
                        <textarea name="page_about_vision" class="form-control" rows="3" placeholder="Tulis visi produk di sini..."><?= e($aboutSettings['page_about_vision'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- ═══════════════════════════════════════════
                     TAB 4: KONTAK
                     ═══════════════════════════════════════════ -->
                <div class="page-tab-content" id="tab-contact">
                    <div class="flex items-center justify-between mb-4 p-3" style="background: var(--bg-subtle); border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                        <div>
                            <div style="font-weight: 700; font-size: 14px;">Status Halaman Kontak</div>
                            <div style="font-size: 12px; color: var(--text-muted);">Tampilkan halaman /contact di website publik</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="page_contact_enabled" value="1" <?= ($contactSettings['page_contact_enabled'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Judul Halaman Kontak</label>
                        <input type="text" name="page_contact_title" class="form-control" value="<?= e($contactSettings['page_contact_title'] ?? 'Hubungi Kami') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Subtitle / Keterangan Pembuka</label>
                        <input type="text" name="page_contact_subtitle" class="form-control" value="<?= e($contactSettings['page_contact_subtitle'] ?? '') ?>" placeholder="Ada pertanyaan atau masukan? Kirim pesan kepada kami...">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Pesan Konfirmasi Setelah Sukses Mengirim Pesan</label>
                        <input type="text" name="page_contact_success_message" class="form-control" value="<?= e($contactSettings['page_contact_success_message'] ?? 'Pesan Anda telah berhasil dikirim. Kami akan menghubungi Anda secepatnya.') ?>">
                    </div>

                    <div class="alert alert-success mt-4" style="background: #f0fdf4; border-color: #bbf7d0; color: #166534; font-size: 13px;">
                        💡 <strong>Info Kontak Perusahaan/Instansi</strong> seperti Alamat Email, Nomor Telepon, dan Alamat Kantor dapat diatur di menu 
                        <a href="<?= url('settings/site') ?>" style="color: var(--primary-600); font-weight: 700; text-decoration: underline;">Pengaturan Situs &rarr;</a>
                    </div>
                </div>

                <!-- ═══════════════════════════════════════════
                     TAB 5: PRIVACY POLICY
                     ═══════════════════════════════════════════ -->
                <div class="page-tab-content" id="tab-privacy">
                    <div class="flex items-center justify-between mb-4 p-3" style="background: var(--bg-subtle); border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                        <div>
                            <div style="font-weight: 700; font-size: 14px;">Status Halaman Privacy Policy</div>
                            <div style="font-size: 12px; color: var(--text-muted);">Halaman ini wajib aktif untuk verifikasi Google AdSense</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="page_privacy_enabled" value="1" <?= ($privacySettings['page_privacy_enabled'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="grid-2 mb-3">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Judul Halaman</label>
                            <input type="text" name="page_privacy_title" class="form-control" value="<?= e($privacySettings['page_privacy_title'] ?? 'Kebijakan Privasi') ?>">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Tanggal Terakhir Diperbarui</label>
                            <input type="date" name="page_privacy_last_updated" class="form-control" value="<?= e($privacySettings['page_privacy_last_updated'] ?? date('Y-m-d')) ?>">
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <div class="flex items-center justify-between mb-2">
                            <label class="form-label" style="margin-bottom: 0;">Isi Kebijakan Privasi</label>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="loadPrivacyTemplate()" style="font-size: 11px; padding: 2px 8px;">
                                📄 Muat Template Standar AdSense
                            </button>
                        </div>
                        <textarea id="privacy-content" name="page_privacy_content" class="form-control" rows="14" style="line-height: 1.6; font-size: 13px;"><?= e($privacySettings['page_privacy_content'] ?? '') ?></textarea>
                        <small style="color: var(--text-muted); font-size: 12px;">Mendukung teks biasa dan tag HTML (&lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;). Sudah mencakup klausul Google AdSense & Cookies.</small>
                    </div>
                </div>

                <!-- ═══════════════════════════════════════════
                     TAB 6: TERMS OF SERVICE
                     ═══════════════════════════════════════════ -->
                <div class="page-tab-content" id="tab-terms">
                    <div class="flex items-center justify-between mb-4 p-3" style="background: var(--bg-subtle); border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                        <div>
                            <div style="font-weight: 700; font-size: 14px;">Status Halaman Terms of Service</div>
                            <div style="font-size: 12px; color: var(--text-muted);">Tampilkan halaman /terms di website publik</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="page_terms_enabled" value="1" <?= ($termsSettings['page_terms_enabled'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="grid-2 mb-3">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Judul Halaman</label>
                            <input type="text" name="page_terms_title" class="form-control" value="<?= e($termsSettings['page_terms_title'] ?? 'Syarat dan Ketentuan') ?>">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Tanggal Terakhir Diperbarui</label>
                            <input type="date" name="page_terms_last_updated" class="form-control" value="<?= e($termsSettings['page_terms_last_updated'] ?? date('Y-m-d')) ?>">
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <div class="flex items-center justify-between mb-2">
                            <label class="form-label" style="margin-bottom: 0;">Isi Syarat & Ketentuan</label>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="loadTermsTemplate()" style="font-size: 11px; padding: 2px 8px;">
                                📄 Muat Template Standar
                            </button>
                        </div>
                        <textarea id="terms-content" name="page_terms_content" class="form-control" rows="14" style="line-height: 1.6; font-size: 13px;"><?= e($termsSettings['page_terms_content'] ?? '') ?></textarea>
                        <small style="color: var(--text-muted); font-size: 12px;">Mendukung teks biasa dan tag HTML (&lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;).</small>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="flex items-center justify-between mt-4" style="padding-top: 16px; border-top: 1px solid var(--border-subtle);">
                    <div style="font-size: 13px; color: var(--text-muted);">
                        💡 Semua perubahan akan langsung diterapkan pada website publik.
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                        </svg>
                        Simpan Semua Halaman
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════
     DYNAMIC REPEATER & TEMPLATE SCRIPTS
     ═══════════════════════════════════════════ -->
<script>
// 1. Tab Switching
document.querySelectorAll('.page-tab').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.page-tab').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.page-tab-content').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        const target = document.getElementById(this.dataset.tab);
        if (target) target.classList.add('active');
    });
});

// 2. Feature Items Management
function updateFeatureCounts() {
    const items = document.querySelectorAll('#features-container .feature-item-card');
    document.getElementById('features-count').innerText = items.length;
    items.forEach((item, idx) => {
        const num = item.querySelector('.feature-number');
        if (num) num.innerText = idx + 1;
        item.querySelectorAll('input, textarea').forEach(inp => {
            if (inp.name.startsWith('features[')) {
                inp.name = inp.name.replace(/features\[\d+\]/, 'features[' + idx + ']');
            }
        });
    });
}

function addFeatureItem() {
    const container = document.getElementById('features-container');
    const idx = container.querySelectorAll('.feature-item-card').length;
    
    const div = document.createElement('div');
    div.className = 'feature-item-card fade-in';
    div.style.cssText = 'background: #ffffff; border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 16px; position: relative; box-shadow: var(--shadow-xs);';
    div.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <span style="font-weight: 700; font-size: 13px; color: var(--primary-600);">Fitur #<span class="feature-number">${idx + 1}</span></span>
            </div>
            <button type="button" class="btn btn-sm" style="color: var(--danger-500); background: transparent; padding: 2px 8px;" onclick="this.closest('.feature-item-card').remove(); updateFeatureCounts();" title="Hapus">
                🗑️ Hapus
            </button>
        </div>
        <div style="display: grid; grid-template-columns: 80px 1fr; gap: 12px; margin-bottom: 10px;">
            <div>
                <label class="form-label" style="font-size: 11px;">Icon / Emoji</label>
                <input type="text" name="features[${idx}][icon]" class="form-control text-center" style="font-size: 20px;" value="⭐" maxlength="4">
            </div>
            <div>
                <label class="form-label" style="font-size: 11px;">Nama Fitur</label>
                <input type="text" name="features[${idx}][title]" class="form-control" placeholder="Contoh: Fitur Baru">
            </div>
        </div>
        <div>
            <label class="form-label" style="font-size: 11px;">Penjelasan Singkat</label>
            <input type="text" name="features[${idx}][desc]" class="form-control" placeholder="Penjelasan kegunaan fitur ini...">
        </div>
    `;
    container.appendChild(div);
    updateFeatureCounts();
    div.querySelector('input[placeholder="Contoh: Fitur Baru"]')?.focus();
}

// 3. Pricing Items Management
function updatePricingCounts() {
    const items = document.querySelectorAll('#pricing-container .pricing-item-card');
    document.getElementById('pricing-count').innerText = items.length;
    items.forEach((item, idx) => {
        const num = item.querySelector('.pricing-number');
        if (num) num.innerText = idx + 1;
        item.querySelectorAll('input, textarea').forEach(inp => {
            if (inp.name.startsWith('pricing[')) {
                inp.name = inp.name.replace(/pricing\[\d+\]/, 'pricing[' + idx + ']');
            }
        });
    });
}

function addPricingItem() {
    const container = document.getElementById('pricing-container');
    const idx = container.querySelectorAll('.pricing-item-card').length;
    
    const div = document.createElement('div');
    div.className = 'pricing-item-card fade-in';
    div.style.cssText = 'background: #ffffff; border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 18px; position: relative; box-shadow: var(--shadow-xs);';
    div.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <span style="font-weight: 700; font-size: 14px; color: var(--primary-600);">Paket #<span class="pricing-number">${idx + 1}</span></span>
                <label style="font-size: 12px; display: flex; align-items: center; gap: 4px; cursor: pointer; margin-left: 12px;">
                    <input type="checkbox" name="pricing[${idx}][highlighted]" value="1">
                    <span style="color: var(--primary-700); font-weight: 600;">⭐ Tandai Sebagai Populer / Highlight</span>
                </label>
            </div>
            <button type="button" class="btn btn-sm" style="color: var(--danger-500); background: transparent; padding: 2px 8px;" onclick="this.closest('.pricing-item-card').remove(); updatePricingCounts();" title="Hapus">
                🗑️ Hapus
            </button>
        </div>
        <div class="grid-3 mb-3">
            <div>
                <label class="form-label" style="font-size: 11px;">Nama Paket</label>
                <input type="text" name="pricing[${idx}][name]" class="form-control" placeholder="Contoh: Pro">
            </div>
            <div>
                <label class="form-label" style="font-size: 11px;">Harga</label>
                <input type="text" name="pricing[${idx}][price]" class="form-control" placeholder="Contoh: Rp 99.000">
            </div>
            <div>
                <label class="form-label" style="font-size: 11px;">Periode</label>
                <input type="text" name="pricing[${idx}][period]" class="form-control" placeholder="Contoh: per bulan">
            </div>
        </div>
        <div class="grid-2 mb-3">
            <div>
                <label class="form-label" style="font-size: 11px;">Deskripsi Singkat Paket</label>
                <input type="text" name="pricing[${idx}][desc]" class="form-control" placeholder="Untuk tim dan organisasi...">
            </div>
            <div>
                <label class="form-label" style="font-size: 11px;">Teks Tombol Aksi (CTA)</label>
                <input type="text" name="pricing[${idx}][cta]" class="form-control" value="Pilih Paket" placeholder="Pilih Paket">
            </div>
        </div>
        <div>
            <label class="form-label" style="font-size: 11px;">Daftar Fitur yang Didapat (Tulis 1 baris per fitur)</label>
            <textarea name="pricing[${idx}][features]" class="form-control" rows="4" placeholder="Fitur 1&#10;Fitur 2&#10;Fitur 3"></textarea>
            <small style="color: var(--text-muted); font-size: 11px;">💡 Cukup tekan Enter untuk menambah baris fitur baru.</small>
        </div>
    `;
    container.appendChild(div);
    updatePricingCounts();
    div.querySelector('input[placeholder="Contoh: Pro"]')?.focus();
}

// 4. Template Loaders
function loadAboutTemplate() {
    if (!confirm('Apakah Anda ingin memuat template default Tentang Kami? Konten saat ini di kotak teks akan digantikan.')) return;
    document.getElementById('about-content').value = `<h2>Apa itu ASR FORM?</h2>
<p>ASR FORM adalah platform berbasis web untuk membuat formulir digital dan mengubah data yang terkumpul menjadi dokumen resmi secara otomatis. Dibangun dengan PHP Native yang ringan dan responsif, ASR FORM dirancang untuk mempermudah instansi, lembaga, dan bisnis dalam mengumpulkan data dan menerbitkan surat.</p>

<h2>Tujuan</h2>
<p>Menyederhanakan proses pembuatan formulir online dan otomatisasi dokumen sehingga pengguna tidak perlu membuat sistem dari awal. Dengan ASR FORM, Anda dapat membuat formulir custom, mengumpulkan respons, dan menghasilkan surat atau dokumen resmi lengkap dengan nomor surat otomatis dan QR Code verifikasi.</p>

<h2>Siapa yang Dapat Menggunakan?</h2>
<ul>
<li>Instansi pemerintah untuk surat keterangan dan formulir layanan publik</li>
<li>Lembaga pendidikan dan universitas untuk pendaftaran siswa dan administrasi</li>
<li>Organisasi dan komunitas untuk pendataan anggota dan registrasi event</li>
<li>Bisnis dan UMKM untuk formulir pesanan, survei kepuasan, dan pendaftaran</li>
<li>Siapa saja yang membutuhkan formulir digital dan dokumen otomatis</li>
</ul>`;
}

function loadPrivacyTemplate() {
    if (!confirm('Apakah Anda ingin memuat template Kebijakan Privasi AdSense-ready standar?')) return;
    document.getElementById('privacy-content').value = `<h2>1. Data yang Kami Kumpulkan</h2>
<h3>Data Akun</h3>
<p>Saat mendaftar, kami mengumpulkan nama, alamat email, dan kata sandi (disimpan dalam bentuk terenkripsi). Data ini diperlukan untuk mengidentifikasi dan mengautentikasi akun Anda.</p>

<h3>Data Formulir</h3>
<p>Pengguna yang membuat formulir menyimpan konfigurasi field, judul, dan deskripsi formulir. Data ini disimpan di server kami untuk menampilkan formulir kepada responden.</p>

<h3>Data Respons</h3>
<p>Ketika seseorang mengisi formulir publik, data yang dimasukkan (termasuk teks, pilihan, file upload, dan tanda tangan digital) disimpan dalam database kami. Data ini hanya dapat diakses oleh pemilik formulir dan administrator.</p>

<h3>File Upload</h3>
<p>File yang diunggah melalui formulir disimpan di server kami. File ini hanya dapat diakses oleh pemilik formulir dan administrator sistem.</p>

<h3>Data Teknis</h3>
<p>Kami mencatat alamat IP dan informasi user agent browser saat respons formulir dikirimkan, untuk keperluan keamanan dan audit.</p>

<h2>2. Penggunaan Data</h2>
<p>Data yang kami kumpulkan digunakan untuk:</p>
<ul>
<li>Menyediakan layanan formulir dan pembuatan dokumen</li>
<li>Mengelola akun pengguna</li>
<li>Menghasilkan dokumen berdasarkan template dan data respons</li>
<li>Keamanan sistem dan pencegahan penyalahgunaan</li>
<li>Audit dan pencatatan aktivitas sistem</li>
</ul>

<h2>3. Cookies</h2>
<p>ASR FORM menggunakan cookie sesi (session cookie) untuk menjaga autentikasi pengguna dan token keamanan CSRF. Cookie ini bersifat esensial untuk fungsi aplikasi dan tidak digunakan untuk pelacakan identitas.</p>

<h2>4. Layanan Pihak Ketiga & Google AdSense</h2>
<p>Jika fitur iklan diaktifkan, layanan pihak ketiga (seperti Google AdSense) dapat menggunakan cookie atau teknologi serupa untuk menampilkan iklan yang relevan sesuai dengan kebijakan dan pengaturan yang berlaku pada layanan tersebut. Anda dapat mengelola preferensi iklan melalui pengaturan browser atau melalui halaman pengaturan iklan Google.</p>

<h2>5. Keamanan Data</h2>
<p>Kami menerapkan langkah-langkah keamanan termasuk enkripsi kata sandi menggunakan bcrypt, proteksi CSRF pada semua formulir, prepared statements untuk mencegah SQL injection, dan validasi input yang ketat.</p>

<h2>6. Hak Pengguna</h2>
<p>Anda berhak untuk mengakses data akun Anda, memperbarui informasi akun, menghapus respons formulir yang Anda miliki, atau meminta penghapusan akun melalui administrator.</p>`;
}

function loadTermsTemplate() {
    if (!confirm('Apakah Anda ingin memuat template Syarat & Ketentuan standar?')) return;
    document.getElementById('terms-content').value = `<h2>1. Penggunaan Layanan</h2>
<p>ASR FORM menyediakan layanan pembuatan formulir digital dan pembuatan dokumen otomatis. Dengan menggunakan layanan ini, Anda menyetujui syarat dan ketentuan yang berlaku.</p>

<h2>2. Akun Pengguna</h2>
<p>Untuk menggunakan fitur lengkap ASR FORM, Anda perlu mendaftarkan akun. Anda bertanggung jawab untuk menjaga kerahasiaan informasi akun Anda, termasuk kata sandi. Akun baru memerlukan persetujuan administrator sebelum dapat digunakan.</p>

<h2>3. Konten Pengguna</h2>
<p>Anda bertanggung jawab penuh atas konten yang Anda buat melalui ASR FORM, termasuk formulir, template dokumen, dan data yang dikumpulkan. Pastikan konten Anda tidak melanggar hukum yang berlaku.</p>

<h2>4. Larangan Penyalahgunaan</h2>
<p>Anda dilarang menggunakan ASR FORM untuk:</p>
<ul>
<li>Mengumpulkan data secara ilegal atau tanpa persetujuan</li>
<li>Membuat formulir yang menyesatkan, phishing, atau penipuan</li>
<li>Mendistribusikan malware atau berkas berbahaya</li>
<li>Mengganggu atau merusak sistem atau layanan server</li>
<li>Melanggar hak cipta dan kekayaan intelektual pihak lain</li>
</ul>

<h2>5. Dokumen</h2>
<p>Dokumen yang dihasilkan oleh ASR FORM berdasarkan template dan data yang Anda berikan. Keakuratan dan keabsahan isi dokumen menjadi tanggung jawab penuh pembuat dokumen.</p>

<h2>6. Pembatasan Layanan</h2>
<p>ASR FORM disediakan "sebagaimana adanya". Kami berupaya menjaga ketersediaan layanan namun berhak melakukan pemeliharaan berkala untuk peningkatan performa dan keamanan.</p>`;
}
</script>
