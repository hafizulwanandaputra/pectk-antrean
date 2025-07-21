<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <span class="fw-medium fs-5 flex-fill text-truncate"><?= $headertitle; ?></span>
    <div id="loadingSpinner" class="px-2">
        <?= $this->include('spinner/spinner'); ?>
    </div>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="main-content-inside px-3 pt-3">
    <div class="no-fluid-content">
        <h5>Pengguna</h5>
        <ul class="list-group shadow-sm  mb-3">
            <?php if (session()->get('role') == "Admin") : ?>
                <li class="list-group-item p-1 list-group-item-action">
                    <div class="d-flex align-items-start">
                        <a href="<?= base_url('/settings/sessions'); ?>" class="stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                            <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-users-gear"></i></p>
                        </a>
                        <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                            <h5 class="card-title">Manajer Sesi</h5>
                        </div>
                        <div class="align-self-center" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                            <span class="text-body-tertiary"><i class="fa-solid fa-angle-right"></i></span>
                        </div>
                    </div>
                </li>
            <?php endif; ?>
            <li class="list-group-item p-1 list-group-item-action">
                <div class="d-flex align-items-start">
                    <a href="<?= base_url('/settings/edit'); ?>" class="stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-user-pen"></i></p>
                    </a>
                    <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                        <h5 class="card-title">Ubah Informasi Pengguna</h5>
                    </div>
                    <div class="align-self-center" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <span class="text-body-tertiary"><i class="fa-solid fa-angle-right"></i></span>
                    </div>
                </div>
            </li>
            <li class="list-group-item p-1 list-group-item-action">
                <div class="d-flex align-items-start">
                    <a href="<?= base_url('/settings/changepassword'); ?>" class="stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-key"></i></p>
                    </a>
                    <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                        <h5 class="card-title">Ubah Kata Sandi Pengguna</h5>
                    </div>
                    <div class="align-self-center" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <span class="text-body-tertiary"><i class="fa-solid fa-angle-right"></i></span>
                    </div>
                </div>
            </li>
        </ul>
        <h5>Sistem</h5>
        <ul class="list-group shadow-sm  mb-3">
            <li class="list-group-item p-1 list-group-item-action">
                <div class="d-flex align-items-start">
                    <a href="<?= base_url('/settings/about'); ?>" class="stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-circle-info"></i></p>
                    </a>
                    <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                        <h5 class="card-title">Tentang Sistem</h5>
                    </div>
                    <div class="align-self-center" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <span class="text-body-tertiary"><i class="fa-solid fa-angle-right"></i></span>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="modal modal-sheet p-4 py-md-5 fade" id="deleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-5 shadow-lg transparent-blur">
                <div class="modal-body p-4">
                    <h5 id="deleteMessage"></h5>
                    <h6 class="mb-0 fw-normal" id="deleteSubmessage"></h6>
                    <div class="row gx-2 pt-4">
                        <div class="col d-grid">
                            <button type="button" class="btn btn-lg btn-body bg-gradient fs-6 mb-0 rounded-4" data-bs-dismiss="modal">Batal</button>
                        </div>
                        <div class="col d-grid">
                            <button type="button" class="btn btn-lg btn-danger bg-gradient fs-6 mb-0 rounded-4" id="confirmDeleteBtn">Hapus</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    $(document).ready(function() {
        $('#loadingSpinner').hide();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>