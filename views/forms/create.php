<?php 
use App\Core\CSRF;
use App\Core\Session;
?>

<div style="max-width: 680px;">
    <div class="card fade-in">
        <div class="card-header">
            <h3 class="card-title">Buat Formulir Baru</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= url('forms/store') ?>">
                <?= CSRF::field() ?>

                <div class="form-group">
                    <label class="form-label" for="title">Judul Formulir <span class="required">*</span></label>
                    <input type="text" id="title" name="title" class="form-control"
                           placeholder="Contoh: Pendaftaran Ekstrakurikuler 2026" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Deskripsi / Petunjuk Pengisian</label>
                    <textarea id="description" name="description" class="form-control"
                              placeholder="Tuliskan keterangan singkat mengenai pengisian form ini..."></textarea>
                </div>

                <div class="flex gap-3 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Simpan & Buka Builder
                    </button>
                    <a href="<?= url('forms') ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
