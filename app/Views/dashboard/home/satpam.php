<?php
$db = db_connect();
?>
<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<style>
    .ratio-onecol {
        --bs-aspect-ratio: 33%;
    }

    #img_bpjs {
        color: inherit;
    }

    @media (max-width: 991.98px) {
        .ratio-onecol {
            --bs-aspect-ratio: 75%;
        }
    }
</style>
<?= $this->endSection(); ?>
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
<main class="main-content-inside px-3">
    <div class="no-fluid-content">
        <div class="text-center">
            <div class="my-4">
                <span class="navbar-brand mx-0 text-start text-md-center lh-sm d-flex justify-content-center align-items-center" style="font-size: 28pt;">
                    <img src="<?= base_url('/assets/images/pec-klinik-logo.png'); ?>" alt="KLINIK MATA PECTK" height="96px">
                    <div class="ps-4 text-start text-success-emphasis fw-bold d-none d-lg-block">PADANG EYE CENTER<br>TELUK KUANTAN</div>
                </span>
            </div>
            <hr>
            <h4><strong>Selamat Datang di Klinik Utama Mata Padang Eye Center Teluk Kuantan</strong></h4>
            <h6><em>Melayani dengan Hati</em></h6>
            <hr>
            <div class="my-4">
                <h4>Silakan ambil nomor antrean bagi pasien yang ingin berobat</h4>
            </div>
        </div>
        <div class="mb-3" id="form_antrean">
            <div class="row row-cols-1 row-cols-lg-3 g-2">
                <div class="col">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-body bg-gradient btn-lg rounded-4" id="btn_umum">
                            <div style="font-size: 3em;"><i class="fa-solid fa-users"></i></div>
                            <div class="fs-4 fw-bold">UMUM</div>
                        </button>
                    </div>
                </div>
                <div class="col">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-body bg-gradient btn-lg rounded-4" id="btn_umum">
                            <div style="font-size: 3em;">
                                <?= file_get_contents(FCPATH . 'assets/images/logo-bpjs.svg') ?>
                            </div>
                            <div class="fs-4 fw-bold">BPJS KESEHATAN</div>
                        </button>
                    </div>
                </div>
                <div class="col">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-body bg-gradient btn-lg rounded-4" id="btn_umum">
                            <div style="font-size: 3em;"><i class="fa-solid fa-user-shield"></i></div>
                            <div class="fs-4 fw-bold">ASURANSI</div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="alert alert-success text-center rounded-top-4 rounded-bottom-5" role="alert" id="antrean_sukses" style="display: none;">
            <h4 class="alert-heading">Nomor antrean berhasil dibuat!</h4>
            <p class="mb-0">Nomor antrean Anda adalah:</p>
            <h1 class="mb-0 display-1" id="antrean"></h1>
            <p class="mb-0">Jaminan: <span id="nama_jaminan"></span></p>
            <p>Tanggal: <span id="tanggal_antrean"></span></p>
            <hr>
            <div class="d-grid gap-2">
                <button type="button" class="btn btn-success bg-gradient btn-lg rounded-4" id="cetak-btn">Cetak Nomor Antrean</button>
            </div>
            <iframe id="print_frame" style="display: none;"></iframe>
        </div>
        <div class="d-grid gap-2 mb-3">
            <button type="button" class="btn btn-body bg-gradient btn-lg rounded-4" id="list_antrean_btn" data-bs-toggle="modal" data-bs-target="#listAntreanModal">Lihat Nomor Antrean Sebelumnya</button>
        </div>
    </div>
    <div class="modal fade" id="listAntreanModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="listAntreanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable ">
            <div id="rajaldiv" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <div class="d-flex flex-row gap-2 me-2 w-100">
                        <select class="form-select form-select-sm w-auto" id="length-menu">
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="75">75</option>
                            <option value="100">100</option>
                        </select>
                        <div class="input-group input-group-sm flex-grow-1">
                            <input type="date" class="form-control form-control-sm" id="externalSearch">
                            <button class="btn btn-danger btn-sm bg-gradient " type="button" id="clearTglButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Bersihkan Tanggal"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    </div>
                    <button id="listAntreanCloseBtn" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-2">
                    <table id="tabel" class="table table-sm table-hover m-0 p-0" style="width:100%; font-size: 0.75rem;">
                        <thead>
                            <tr class="align-middle">
                                <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">No.</th>
                                <th scope="col" class="bg-body-secondary border-secondary text-nowrap" style="border-bottom-width: 2px;">Tindakan</th>
                                <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Nomor Antrean</th>
                                <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Tanggal Antrean</th>
                                <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Satpam</th>
                            </tr>
                        </thead>
                        <tbody class="align-top">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer pt-2 pb-2 d-flex justify-content-between" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <div id="loading"></div>
                    <button id="refreshButton" type="button" class="btn btn-primary btn-sm bg-gradient"><i class="fa-solid fa-arrows-rotate"></i></button>
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
    })
</script>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
<script>
    async function fetchJaminanOptions(selectedJaminan = null) {
        $('#loadingSpinner').show();
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('home/list_jaminan') ?>`);

            if (response.data.success) {
                const options = response.data.data;

                // Simpan nilai yang saat ini dipilih untuk masing-masing elemen
                const currentSelectionFilter = selectedJaminan || $('#kode_antrean').val();

                // Hapus opsi yang ada, kecuali opsi pertama (default)
                $('#kode_antrean').find('option:not(:first)').remove();

                // Tambahkan opsi ke elemen select
                options.forEach(option => {
                    $('#kode_antrean').append(`<option value="${option.value}">${option.text}</option>`);
                });

                // Mengatur ulang pilihan sebelumnya
                if (currentSelectionFilter) {
                    $('#kode_antrean').val(currentSelectionFilter);
                }
            } else {
                showFailedToast('Gagal mendapatkan jaminan.');
            }
        } catch (error) {
            console.error(error);
            showFailedToast(`${error}`);
        }
    }

    $(document).ready(async function() {
        var table = $('#tabel').DataTable({
            "oLanguage": {
                "sDecimal": ",",
                "sEmptyTable": 'Silakan pilih tanggal untuk melihat daftar antrean',
                "sInfo": "Menampilkan _START_ hingga _END_ dari _TOTAL_ antrean",
                "sInfoEmpty": "Menampilkan 0 hingga 0 dari 0 antrean",
                "sInfoFiltered": "(di-filter dari _MAX_ antrean)",
                "sInfoPostFix": "",
                "sThousands": ".",
                "sLengthMenu": "Tampilkan _MENU_ antrean",
                "sLoadingRecords": "Memuat...",
                "sProcessing": "",
                "sSearch": "Cari:",
                "sZeroRecords": "Antrean yang Anda cari tidak ditemukan",
                "oAria": {
                    "sOrderable": "Urutkan menurut kolom ini",
                    "sOrderableReverse": "Urutkan terbalik kolom ini"
                },
                "oPaginate": {
                    "sFirst": '<i class="fa-solid fa-angles-left"></i>',
                    "sLast": '<i class="fa-solid fa-angles-right"></i>',
                    "sPrevious": '<i class="fa-solid fa-angle-left"></i>',
                    "sNext": '<i class="fa-solid fa-angle-right"></i>'
                }
            },
            'dom': "<'row'<'col-md-12'tr>>" + "<'d-flex justify-content-center align-items-center'<'text-md-center text-lg-start'><'d-md-flex justify-content-md-center d-lg-block'p>>",
            'initComplete': function(settings, json) {
                $("#tabel").wrap("<div class='card shadow-sm  mb-3 overflow-auto position-relative datatables-height'></div>");
                $('.dataTables_filter input[type="search"]').css({
                    'width': '220px'
                });
                $('.dataTables_info').css({
                    'padding-top': '0',
                    'font-variant-numeric': 'tabular-nums'
                });
            },
            "drawCallback": function() {
                $(".pagination").wrap("<div class='overflow-auto'></div>");
                $(".pagination").addClass("pagination-sm");
                $(".page-item .page-link").addClass("bg-gradient date");
                var pageInfo = this.api().page.info();
                var infoText = `${pageInfo.recordsDisplay}`;
                $('#total_datatables').html(infoText);
            },
            "search": {
                "caseInsensitive": true
            },
            "searching": false, // Disable the internal search bar
            'pageLength': 25,
            'lengthMenu': "",
            "autoWidth": true,
            "processing": false,
            "serverSide": true,
            "ajax": {
                // URL endpoint untuk melakukan permintaan AJAX
                "url": "<?= base_url('/home/list_antrean') ?>",
                "type": "POST", // Metode HTTP yang digunakan untuk permintaan (POST)
                "data": function(d) {
                    // Menambahkan parameter tambahan pada data yang dikirim
                    d.search = {
                        "value": $('#externalSearch').val() // Mengambil nilai input pencarian
                    };
                },
                beforeSend: function() {
                    $('#loading').html(`<?= $this->include('spinner/spinner'); ?> Memuat...`);
                },
                complete: function() {
                    $('#loading').html(``);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#loading').html(``);
                    // Menampilkan toast error Bootstrap ketika permintaan AJAX gagal
                    showFailedToast('Gagal memuat data. Silakan coba lagi.'); // Menampilkan pesan kesalahan
                }
            },
            columns: [{
                    data: 'no',
                    render: function(data, type, row) {
                        return `<span class="date" style="display: block; text-align: center;">${data}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `<div class="d-grid">
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-body text-nowrap bg-gradient cetak-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${row.id_antrean}" data-bs-toggle="tooltip" data-bs-title="Cetak"><i class="fa-solid fa-print"></i></button>
                            </div>
                            </div>`;
                    }
                },
                {
                    data: 'kode_antrean',
                    render: function(data, type, row) {
                        return `<span class="date">${data}-${row.nomor_antrean}</span>`;
                    }
                },
                {
                    data: 'tanggal_antrean',
                    render: function(data, type, row) {
                        return `<span class="date">${data}</span>`;
                    }
                },
                {
                    data: 'satpam'
                },
            ],
            "order": [
                [3, 'desc']
            ],
            "columnDefs": [{
                "target": [1],
                "orderable": false
            }, {
                "target": [0, 1],
                "width": "0%"
            }, {
                "target": [2, 3, 4],
                "width": "33%"
            }],
        });
        // Menginisialisasi tooltip untuk elemen dengan atribut data-bs-toggle="tooltip"
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Memperbarui tooltip setiap kali tabel digambar ulang
        table.on('draw', function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        // Bind the external search input to the table search
        $('#externalSearch').on('input', function() {
            table.search(this.value).draw(); // Trigger search on the table
        });

        // Kendalikan jumlah baris dengan dropdown custom
        $('#length-menu').on('change', function() {
            var length = $(this).val(); // Ambil nilai dari dropdown
            table.page.len(length).draw(); // Atur jumlah baris dan refresh tabel
        });
        $('#refreshButton').on('click', function(e) {
            e.preventDefault();
            table.ajax.reload(null, false);
        });
        $('#clearTglButton').on('click', function() {
            $('#externalSearch').val('');
            table.ajax.reload(null, false);
        });
        $(document).on('visibilitychange', function() {
            if (document.visibilityState === "visible") {
                table.ajax.reload(null, false); // Reload data tanpa reset paging
            }
        });
        $('#listAntreanModal').on('shown.bs.modal', function() {
            table.columns.adjust();
            table.ajax.reload(null, false);
        });
        $('#listAntreanModal').on('hidden.bs.modal', function() {
            $('#externalSearch').val('');
            table.ajax.reload(null, false);
        });
        $('#cetak-btn').on('click', function() {
            const id = $(this).data('id');

            // Tampilkan loading di tombol cetak
            const $btn = $(this);
            $btn.prop('disabled', true).html(`<?= $this->include('spinner/spinner'); ?> Silakan tunggu...`);

            // Muat PDF ke iframe
            var iframe = $('#print_frame');
            iframe.attr('src', `<?= base_url("home/cetak_antrean") ?>/${id}`);

            // Saat iframe selesai memuat, jalankan print
            iframe.off('load').on('load', function() {
                try {
                    this.contentWindow.focus();
                    this.contentWindow.print();
                } catch (e) {
                    showFailedToast("Peramban memblokir pencetakan otomatis. Harap izinkan pop-up atau pastikan file berasal dari domain yang sama.");
                } finally {
                    // Kembalikan tampilan tombol cetak
                    $btn.prop('disabled', false).html('Cetak Nomor Antrean');
                }
            });
        });
        $(document).on('click', '.cetak-btn', function() {
            const id = $(this).data('id');

            // Tampilkan loading di tombol cetak
            const $btn = $(this);
            $btn.prop('disabled', true).html(`<?= $this->include('spinner/spinner'); ?>`);

            // Muat PDF ke iframe
            var iframe = $('#print_frame');
            iframe.attr('src', `<?= base_url("home/cetak_antrean") ?>/${id}`);

            // Saat iframe selesai memuat, jalankan print
            iframe.off('load').on('load', function() {
                try {
                    this.contentWindow.focus();
                    this.contentWindow.print();
                } catch (e) {
                    showFailedToast("Peramban memblokir pencetakan otomatis. Harap izinkan pop-up atau pastikan file berasal dari domain yang sama.");
                } finally {
                    // Kembalikan tampilan tombol cetak
                    $btn.prop('disabled', false).html('<i class="fa-solid fa-print"></i>');
                }
            });
        });
        $('#form_antrean').submit(async function(ə) {
            ə.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#form_antrean .is-invalid').removeClass('is-invalid');
            $('#form_antrean .invalid-feedback').text('').hide();
            $('#submit_btn').prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?> Silakan tunggu...
            `);

            // Disable form inputs
            $('#form_antrean input, #form_antrean textarea, #form_antrean select, #form_antrean button').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/home/buat_antrean') ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    // Simpan dulu opsi yang disabled
                    const disabledOptions = $('#kode_antrean option:disabled').map(function() {
                        return this.value;
                    }).get();

                    // Aktifkan sementara
                    $('#kode_antrean option').prop('disabled', false);

                    // Reset nilai
                    $('#kode_antrean').val('');

                    // Kembalikan opsi yang tadi disabled
                    disabledOptions.forEach(val => {
                        $(`#kode_antrean option[value="${val}"]`).prop('disabled', true);
                    });
                    const data = response.data.data;
                    $('#antrean_sukses').show();
                    $('#antrean').text(data.antrean);
                    $('#nama_jaminan').text(data.nama_jaminan);
                    $('#tanggal_antrean').text(data.tanggal_antrean);
                    console.log(data.id_antrean);
                    $('#cetak-btn').attr('data-id', data.id_antrean);
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#form_antrean .is-invalid').removeClass('is-invalid');
                    $('#form_antrean .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);

                            // Handle radio button group separately
                            if (["alergi", "keadaan_umum"].includes(field)) {
                                const radioGroup = $(`input[name='${field}']`); // Ambil grup radio berdasarkan nama
                                const feedbackElement = radioGroup.closest('.radio-group').find('.invalid-feedback'); // Gunakan pembungkus dengan class tertentu

                                if (radioGroup.length > 0 && feedbackElement.length > 0) {
                                    radioGroup.addClass('is-invalid');
                                    feedbackElement.text(response.data.errors[field]).show();

                                    // Remove error message when the user selects any radio button in the group
                                    radioGroup.on('change', function() {
                                        radioGroup.removeClass('is-invalid');
                                        feedbackElement.text('').hide();
                                    });
                                } else {
                                    console.warn("Radio group tidak ditemukan untuk field:", field);
                                }
                            } else {
                                let feedbackElement = fieldElement.siblings('.invalid-feedback');

                                // Handle input-group cases
                                if (fieldElement.closest('.input-group').length) {
                                    feedbackElement = fieldElement.closest('.input-group').find('.invalid-feedback');
                                }

                                if (fieldElement.length > 0 && feedbackElement.length > 0) {
                                    fieldElement.addClass('is-invalid');
                                    feedbackElement.text(response.data.errors[field]).show();

                                    // Remove error message when the user corrects the input
                                    fieldElement.on('input change', function() {
                                        $(this).removeClass('is-invalid');
                                        feedbackElement.text('').hide();
                                    });
                                } else {
                                    console.warn("Elemen tidak ditemukan pada field:", field);
                                }
                            }
                        }
                    }
                    console.error('Perbaiki kesalahan pada formulir.');
                }
            } catch (error) {
                if (error.response.request.status === 422 || error.response.request.status === 401) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#submit_btn').prop('disabled', false).html(`
                    Buat Nomor Antrean
                `);
                $('#form_antrean input, #form_antrean textarea, #form_antrean select, #form_antrean button').prop('disabled', false);
            }
        });
        await fetchJaminanOptions()
        $('#loadingSpinner').hide();
    });
</script>
<?= $this->endSection(); ?>