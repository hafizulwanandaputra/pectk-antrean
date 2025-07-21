<?php
$db = db_connect();
?>
<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<style>
    .ratio-onecol {
        --bs-aspect-ratio: 33%;
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
        <div class="d-flex justify-content-start align-items-start pt-3">
            <h1 class="h2 mb-0 me-3"><i class="fa-regular fa-face-smile-beam"></i></h1>
            <h1 class="h2 mb-0"><?= $txtgreeting . ', ' . session()->get('fullname') . '!'; ?></h1>
        </div>
        <hr>
        <?php if (session()->get('role') == "Admin") : ?>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Admin</div>
                <div class="row row-cols-1 row-cols-lg-3 g-2 mb-2">
                    <div class="col">
                        <div class="card w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Pengguna Keseluruhan</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_user, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-danger-subtle border-danger-subtle text-danger-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-danger-subtle w-100 text-truncate">Pengguna Nonaktif</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_user_inactive, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-success-subtle border-success-subtle text-success-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-success-subtle w-100 text-truncate">Pengguna Aktif</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_user_active, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-lg-3 g-2 mb-2">
                    <div class="col">
                        <div class="card w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Sesi Keseluruhan Selain Anda</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_sessions, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-danger-subtle border-danger-subtle text-danger-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-danger-subtle w-100 text-truncate">Sesi Kedaluwarsa Selain Anda</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_sessions_expired, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-success-subtle border-success-subtle text-success-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-success-subtle w-100 text-truncate">Sesi Aktif Selain Anda</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_sessions_active, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if (session()->get('role') == "Admin" || session()->get('role') == "Satpam" || session()->get('role') == "Admisi") : ?>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">PLACEHOLDER</div>
            </div>
        <?php endif; ?>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>

</script>
<?= $this->endSection(); ?>
<?= $this->section('chartjs'); ?>
<script>
    // Array to keep track of chart instances
    const chartInstances = [];

    // Function to initialize a chart and add it to the instances array
    function createChart(ctx, config) {
        const chart = new Chart(ctx, config);
        chartInstances.push(chart);
        return chart;
    }

    // Function to update chart configurations based on the color scheme
    function updateChartOptions() {
        // Cek apakah data-bs-theme ada dan bernilai "dark"
        const themeAttribute = document.documentElement.getAttribute("data-bs-theme");
        const isDarkMode = themeAttribute === "dark";

        const colorSettings = {
            color: isDarkMode ? "#FFFFFF" : "#000000",
            borderColor: isDarkMode ? "rgba(255,255,255,0.1)" : "rgba(0,0,0,0.1)",
            backgroundColor: isDarkMode ? "rgba(255,255,0,0.1)" : "rgba(0,255,0,0.1)",
            lineBorderColor: isDarkMode ? "rgba(255,255,0,0.4)" : "rgba(0,255,0,0.4)",
            gridColor: isDarkMode ? "rgba(255,255,255,0.2)" : "rgba(0,0,0,0.2)"
        };

        chartInstances.forEach(chart => {
            if (chart.options.scales) {
                // Update X-axis
                if (chart.options.scales.x) {
                    if (chart.options.scales.x.ticks) {
                        chart.options.scales.x.ticks.color = colorSettings.color;
                    }
                    if (chart.options.scales.x.title) {
                        chart.options.scales.x.title.color = colorSettings.color;
                    }
                    if (chart.options.scales.x.grid) {
                        chart.options.scales.x.grid.color = colorSettings.gridColor;
                    }
                }

                // Update Y-axis
                if (chart.options.scales.y) {
                    if (chart.options.scales.y.ticks) {
                        chart.options.scales.y.ticks.color = colorSettings.color;
                    }
                    if (chart.options.scales.y.title) {
                        chart.options.scales.y.title.color = colorSettings.color;
                    }
                    if (chart.options.scales.y.grid) {
                        chart.options.scales.y.grid.color = colorSettings.gridColor;
                    }
                }
            }

            // Update line chart specific settings
            if (chart.options.elements && chart.options.elements.line) {
                chart.options.elements.line.borderColor = colorSettings.lineBorderColor;
            }

            // Update doughnut and pie chart legend
            if ((chart.config.type === 'doughnut' || chart.config.type === 'pie') && chart.options.plugins && chart.options.plugins.legend) {
                chart.options.plugins.legend.labels.color = colorSettings.color;
            }

            // Redraw the chart with updated settings
            chart.update();
        });
    }
    Chart.defaults.font.family = '"Helvetica Neue", Helvetica, Arial, Arimo, "Liberation Sans", sans-serif';

    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Admisi") : ?>
        const data_antreanpiegraph = [];
        const label_antreanpiegraph = [];
        const data_antreankodegraph = [];
        const label_antreankodegraph = [];
        const data_antreangraph = [];
        const label_antreangraph = [];
    <?php endif; ?>

    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Admisi") : ?>
        <?php foreach ($antreanpiegraph->getResult() as $key => $antreanpiegraph) : ?>
            data_antreanpiegraph.push(<?= $antreanpiegraph->total_rajal; ?>);
            label_antreanpiegraph.push('<?= $antreanpiegraph->dokter; ?>');
        <?php endforeach; ?>
        <?php foreach ($antreangraph->getResult() as $key => $antreangraph) : ?>
            data_antreangraph.push(<?= $antreangraph->total_rajal; ?>);
            label_antreangraph.push('<?= $antreangraph->bulan; ?>');
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Admisi") : ?>
        var data_content_antreanpiegraph = {
            labels: label_antreanpiegraph,
            datasets: [{
                label: 'Total Rawat Jalan',
                pointStyle: 'circle',
                pointRadius: 6,
                pointHoverRadius: 12,
                borderWidth: 0,
                fill: true,
                data: data_antreanpiegraph
            }]
        }
        var data_content_antreankodegraph = {
            labels: <?= $labels_antreankode ?>,
            datasets: <?= $datasets_antreankode ?>
        }
        var data_content_antreangraph = {
            labels: label_antreangraph,
            datasets: [{
                label: 'Total Rawat Jalan',
                pointRadius: 6,
                pointHoverRadius: 12,
                fill: false,
                data: data_antreangraph
            }]
        }
    <?php endif; ?>

    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Admisi") : ?>
        var chart_antreanpiegraph = createChart(document.getElementById('antreanpiegraph').getContext('2d'), {
            type: 'pie',
            data: data_content_antreanpiegraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    filler: {
                        drawTime: 'beforeDraw'
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
        var chart_antreankodegraph = createChart(document.getElementById('antreankodegraph').getContext('2d'), {
            type: 'line',
            data: data_content_antreankodegraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Rawat Jalan'
                        }
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
        var chart_antreangraph = createChart(document.getElementById('antreangraph').getContext('2d'), {
            type: 'line',
            data: data_content_antreangraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Rawat Jalan'
                        }
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
    <?php endif; ?>

    // Initial setup
    updateChartOptions();

    // Watch for changes in color scheme preference
    const mediaQueryList = window.matchMedia("(prefers-color-scheme: dark)");
    mediaQueryList.addEventListener("change", () => {
        updateChartOptions();
    });
</script>
<?= $this->endSection(); ?>