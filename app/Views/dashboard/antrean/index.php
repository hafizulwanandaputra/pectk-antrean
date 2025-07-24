<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('title'); ?>
<style>
    .second-row-form {
        min-width: 15em;
    }

    @media (max-width: 767.98px) {
        .second-row-form {
            min-width: 0;
        }
    }
</style>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><span id="totalRecords">0</span> antrean</div>
        </div>
    </div>
    <div id="loadingSpinner" class="px-2">
        <?= $this->include('spinner/spinner'); ?>
    </div>
    <a id="toggleFilter" class="fs-6 mx-2 text-success-emphasis" href="#" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Pencarian"><i class="fa-solid fa-magnifying-glass"></i></a>
    <a id="refreshButton" class="fs-6 mx-2 text-success-emphasis" href="#" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Segarkan"><i class="fa-solid fa-sync"></i></a>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="main-content-inside">
    <div id="filterFields" class="sticky-top" style="z-index: 99; display: none;">
        <ul class="list-group shadow-sm rounded-0">
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-secondary transparent-blur">
                <div class="no-fluid-content">
                    <div class="d-flex flex-column flex-lg-row gap-2">
                        <div class="input-group input-group-sm flex-grow-1">
                            <select class="form-select form-select-sm" id="nama_jaminan">
                                <option value="UMUM" selected>UMUM</option>
                                <option value="BPJS KESEHATAN">BPJS KESEHATAN</option>
                                <option value="ASURANSI">ASURANSI</option>
                            </select>
                        </div>
                        <div class="input-group input-group-sm w-auto second-row-form">
                            <input type="date" id="tanggalFilter" class="form-control" value="<?= date('Y-m-d') ?>">
                            <button class="btn btn-primary btn-sm bg-gradient" type="button" id="setTodayTglButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Kembali ke Hari Ini"><i class="fa-solid fa-calendar-day"></i></button>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="px-3 mt-3">
        <div class="no-fluid-content">
            <h1 id="no-data-alert" class="text-center" style="display: none;">Tidak ada Antrean</h1>
            <div id="antreanContainer" class="row row-cols-1 row-cols-lg-2 g-2">
                <?php for ($i = 0; $i < 8; $i++) : ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title placeholder-glow">
                                    <span class="placeholder w-100" style="max-width: 256px;"></span>
                                </h5>
                                <p class="card-text mb-0 placeholder-glow" style="font-size: 0.75em;">
                                    <span class="placeholder w-100" style="max-width: 128px;"></span><br>
                                    <span class="placeholder w-100" style="max-width: 128px;"></span><br>
                                    <span class="placeholder w-100" style="max-width: 128px;"></span>
                                </p>
                                <p class="card-text placeholder-glow">
                                    <span class="placeholder w-100" style="max-width: 128px;"></span>
                                </p>
                            </div>
                            <div class="card-footer">
                                <div class="d-grid gap-2">
                                    <div class="btn-group" role="group">
                                        <a class="btn btn-primary bg-gradient btn-sm disabled placeholder" aria-disabled="true"></a>
                                        <a class="btn btn-success bg-gradient btn-sm disabled placeholder" aria-disabled="true"></a>
                                        <a class="btn btn-danger bg-gradient btn-sm disabled placeholder" aria-disabled="true"></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
            <nav id="paginationNav" class="d-flex justify-content-center justify-content-lg-end mt-3 overflow-auto w-100">
                <ul class="pagination pagination-sm"></ul>
            </nav>
        </div>
    </div>
    <div class="modal modal-sheet p-4 py-md-5 fade" id="addModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-5 shadow-lg transparent-blur">
                <?= form_open_multipart('/pasien/create', 'id="addForm"'); ?>
                <div class="modal-body p-4">
                    <h5 id="addMessage"></h5>
                    <h6 class="mb-0 fw-normal" id="addSubmessage"></h6>
                    <div class="row gy-2 pt-4">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-lg btn-primary bg-gradient fs-6 mb-0 rounded-4" id="confirmAddBtn">Tambah Pasien</button>
                        </div>
                        <div class="d-grid">
                            <button type="button" class="btn btn-lg btn-body bg-gradient fs-6 mb-0 rounded-4" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    let limit = 8;
    let currentPage = 1;
    let pembelianObatId = null;

    async function fetchAntrean() {
        const nama_jaminan = $('#nama_jaminan').val();
        const tanggal_antrean = $('#tanggalFilter').val();
        const offset = (currentPage - 1) * limit;

        // Show the spinner
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('antrean/list_antrean') ?>', {
                params: {
                    nama_jaminan: nama_jaminan,
                    tanggal_antrean: tanggal_antrean,
                    limit: limit,
                    offset: offset
                }
            });

            const data = response.data;
            $('#no-data-alert').hide();
            $('#antreanContainer').empty();
            $('#totalRecords').text(data.total.toLocaleString('id-ID'));

            if (data.total === 0) {
                $('#paginationNav ul').empty();
                $('#no-data-alert').show();
            } else {
                data.antrean.forEach(function(antrean) {
                    const loket = antrean.loket ? `${pasien.loket}` : `<em>Belum ada loket</em>`;
                    let status = antrean.status;
                    if (status === 'BELUM DIPANGGIL') {
                        status = `<span class="badge text-bg-warning">${antrean.status}</span>`;
                    } else if (status === 'SUDAH DIPANGGIL') {
                        status = `<span class="badge text-bg-success">${antrean.status}</span>`;
                    } else if (status === 'BATAL') {
                        status = `<span class="badge text-bg-danger">${antrean.status}</span>`;
                    }
                    const antreanElement = `
                        <div class="col">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title date">${antrean.kode_antrean}-${antrean.nomor_antrean}</h5>
                                    <p class="card-text mb-0" style="font-size: 0.75em;">
                                        ${antrean.nama_jaminan}<br>
                                        ${antrean.tanggal_antrean}<br>
                                        ${loket}
                                    </p>
                                    <p class="card-text">
                                        ${status}
                                    </p>
                                </div>
                                <div class="card-footer">
                                    <div class="d-grid gap-2">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-primary btn-sm bg-gradient btn-call" data-id="${antrean.kode_antrean}-${antrean.nomor_antrean}">
                                                Panggil
                                            </button>
                                            <button type="button" class="btn btn-success btn-sm bg-gradient btn-complete" data-id="${antrean.id_antrean}">
                                                Selesai
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm bg-gradient btn-cancel" data-id="${antrean.id_antrean}">
                                                Batal
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    $('#antreanContainer').append(antreanElement);
                });

                // Pagination logic with ellipsis for more than 3 pages
                const totalPages = Math.ceil(data.total / limit);
                $('#paginationNav ul').empty();

                if (currentPage > 1) {
                    $('#paginationNav ul').append(`
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${currentPage - 1}">
                            <i class="fa-solid fa-angle-left"></i>
                        </a>
                    </li>
                `);
                }

                if (totalPages > 5) {
                    $('#paginationNav ul').append(`
                    <li class="page-item ${currentPage === 1 ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="1">1</a>
                    </li>
                `);

                    if (currentPage > 3) {
                        $('#paginationNav ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">…</span></li>');
                    }

                    for (let i = Math.max(2, currentPage - 1); i <= Math.min(totalPages - 1, currentPage + 1); i++) {
                        $('#paginationNav ul').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                    }

                    if (currentPage < totalPages - 2) {
                        $('#paginationNav ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">…</span></li>');
                    }

                    $('#paginationNav ul').append(`
                    <li class="page-item ${currentPage === totalPages ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="${totalPages}">${totalPages}</a>
                    </li>
                `);
                } else {
                    // Show all pages if total pages are 3 or fewer
                    for (let i = 1; i <= totalPages; i++) {
                        $('#paginationNav ul').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                    }
                }

                if (currentPage < totalPages) {
                    $('#paginationNav ul').append(`
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${currentPage + 1}">
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </li>
                `);
                }
            }
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            $('#antreanContainer').empty();
            $('#paginationNav ul').empty();
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }

    $(document).on('click', '#paginationNav a', function(event) {
        event.preventDefault(); // Prevents default behavior (scrolling)
        const page = $(this).data('page');
        if (page) {
            currentPage = page;
            fetchAntrean();
        }
    });

    let voiceReady = false;

    // Siapkan suara Google Bahasa Indonesia
    let googleVoice = null;

    function loadVoices() {
        const voices = speechSynthesis.getVoices();
        googleVoice = voices.find(v => v.name === 'Google Bahasa Indonesia' && v.lang === 'id-ID');
        voiceReady = true;
    }

    // Cegah masalah jika suara belum tersedia saat awal
    if (speechSynthesis.onvoiceschanged !== undefined) {
        speechSynthesis.onvoiceschanged = loadVoices;
    }

    async function setNamaJaminanFromLocalStorage() {
        return new Promise((resolve) => {
            const savedJaminan = localStorage.getItem('nama_jaminan');
            if (savedJaminan) {
                $('#nama_jaminan').val(savedJaminan);
            }
            resolve(); // selesai, lanjut ke fetchAntrean()
        });
    }

    $(document).ready(async function() {
        const socket = new WebSocket('<?= env('WS-URL-JS') ?>'); // Ganti dengan domain VPS

        socket.onopen = () => {
            console.log("Connected to WebSocket server");
        };

        socket.onmessage = async function(event) {
            const data = JSON.parse(event.data);
            if (data.update) {
                console.log("Received update from WebSocket");
                fetchAntrean();
            }
        };

        socket.onclose = () => {
            console.log("Disconnected from WebSocket server");
        };

        $('[data-bs-toggle="popover"]').popover({
            html: true,
            template: '<div class="popover shadow-lg" role="tooltip">' +
                '<div class="popover-arrow"></div>' +
                '<h3 class="popover-header"></h3>' +
                '<div class="popover-body"></div>' +
                '</div>'
        });

        $('#nama_jaminan').on('change', function() {
            currentPage = 1;
            fetchAntrean();
        });

        $('#tanggalFilter').on('change', function() {
            currentPage = 1;
            fetchAntrean();
        });

        $('#setTodayTglButton').on('click', async function() {
            currentPage = 1;
            const today = new Date();
            const formattedDate = today.toISOString().split('T')[0];
            $('#tanggalFilter').val(formattedDate);
            fetchAntrean();
        });

        $(document).on('visibilitychange', function() {
            if (document.visibilityState === "visible") {
                fetchAntrean();
            }
        });
        // Menangani event klik pada tombol refresh
        $('#refreshButton').on('click', function(ə) {
            ə.preventDefault();
            fetchAntrean(); // Panggil fungsi untuk mengambil data pasien
        });

        // Simpan saat dipilih
        $('#nama_jaminan').on('change', function() {
            const selectedValue = $(this).val();
            localStorage.setItem('nama_jaminan', selectedValue);
        });

        $(document).on('click', '.btn-call', function() {
            const nomorAntrean = $(this).data('id');
            // Pisahkan menjadi huruf dan angka: "U" dan "001"
            const [huruf, angka] = nomorAntrean.split('-');
            const kalimat = `Nomor antrean, ${huruf}, ${angka}, silakan menuju <?= session()->get('fullname'); ?>.`;

            // Ucapkan kalimat
            const utterance = new SpeechSynthesisUtterance(kalimat);
            utterance.lang = 'id-ID';
            utterance.rate = 1; // Perlambat suara
            if (googleVoice) {
                utterance.voice = googleVoice;
            }

            speechSynthesis.speak(utterance);
        });

        // Panggil fungsi untuk mengambil data pasien saat dokumen siap
        await setNamaJaminanFromLocalStorage();
        fetchAntrean(); // dijalankan setelah dropdown diatur
        // $('#loadingSpinner').hide();
    });

    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>