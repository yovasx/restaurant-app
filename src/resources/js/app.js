import './bootstrap';
import ApexCharts from 'apexcharts';

const MODAL_OPEN_CLASS = 'overflow-hidden';

function initDropzones(root = document) {
    root.querySelectorAll('[data-dropzone="true"]').forEach((zone) => {
        if (zone.dataset.dropzoneBound === 'true') {
            return;
        }

        zone.dataset.dropzoneBound = 'true';

        const inputId = zone.dataset.input;
        const previewId = zone.dataset.preview;
        const placeholderId = zone.dataset.placeholder;
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const placeholder = placeholderId ? document.getElementById(placeholderId) : null;

        if (!input || !preview) {
            return;
        }

        zone.addEventListener('click', () => input.click());

        zone.addEventListener('dragover', (event) => {
            event.preventDefault();
            zone.classList.add('border-[#9e2016]', 'bg-red-50', 'scale-[1.01]');
        });

        zone.addEventListener('dragleave', () => {
            zone.classList.remove('border-[#9e2016]', 'bg-red-50', 'scale-[1.01]');
        });

        zone.addEventListener('drop', (event) => {
            event.preventDefault();
            zone.classList.remove('border-[#9e2016]', 'bg-red-50', 'scale-[1.01]');

            const files = event.dataTransfer?.files;
            if (!files?.[0]) {
                return;
            }

            const transfer = new DataTransfer();
            transfer.items.add(files[0]);
            input.files = transfer.files;
            showPreview(files[0], preview, placeholder);
        });

        input.addEventListener('change', () => {
            if (input.files?.[0]) {
                showPreview(input.files[0], preview, placeholder);
            }
        });
    });
}

function showPreview(file, preview, placeholder) {
    const reader = new FileReader();

    reader.onload = (event) => {
        preview.src = event.target?.result ?? '';
        preview.classList.remove('hidden');
        placeholder?.classList.add('hidden');
    };

    reader.readAsDataURL(file);
}

function openModal(modal) {
    if (!modal) {
        return;
    }

    const alreadyOpen = document.querySelector('[data-modal][data-open="true"]');
    if (alreadyOpen && alreadyOpen !== modal) {
        closeModal(alreadyOpen);
    }

    modal.classList.remove('hidden');
    modal.dataset.open = 'true';
    document.body.classList.add(MODAL_OPEN_CLASS);
    initDropzones(modal);

    const autoFocusTarget = modal.querySelector('[data-modal-initial-focus], input, select, textarea, button');
    autoFocusTarget?.focus();
}

function closeModal(modal) {
    if (!modal) {
        return;
    }

    modal.classList.add('hidden');
    modal.dataset.open = 'false';

    if (!document.querySelector('[data-modal][data-open="true"]')) {
        document.body.classList.remove(MODAL_OPEN_CLASS);
    }
}

function initModals() {
    document.addEventListener('click', (event) => {
        const openTrigger = event.target.closest('[data-modal-open]');
        if (openTrigger) {
            const modal = document.getElementById(openTrigger.dataset.modalOpen);
            if (modal) {
                openModal(modal);
            }
            return;
        }

        const closeTrigger = event.target.closest('[data-modal-close]');
        if (!closeTrigger) {
            return;
        }

        const modal = closeTrigger.closest('[data-modal]');
        closeModal(modal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        const modal = document.querySelector('[data-modal][data-open="true"]');
        if (modal) {
            closeModal(modal);
        }
    });

    document.querySelectorAll('[data-modal-auto-open="true"]').forEach((modal) => {
        openModal(modal);
    });
}

function initToasts() {
    const toast = document.querySelector('[data-screen-toast]');
    if (!toast) {
        return;
    }

    const dismiss = () => {
        toast.classList.add('opacity-0', 'translate-x-4', 'scale-95');
        window.setTimeout(() => toast.remove(), 250);
    };

    toast.classList.remove('hidden');
    window.requestAnimationFrame(() => {
        toast.classList.remove('opacity-0', 'translate-x-4', 'scale-95');
    });

    toast.querySelector('[data-toast-close]')?.addEventListener('click', dismiss);
    window.setTimeout(dismiss, Number(toast.dataset.timeout || 3500));
}

function initSingleChart(chartEl) {
    function buildOpts(series) {
        var categories;
        try { categories = JSON.parse(chartEl.dataset.categories); } catch(e) { categories = []; }
        var chartType = chartEl.dataset.chartType || 'area';
        var stacking = chartType === 'bar' ? { bar: { columnWidth: '60%' } } : {};
        return {
            chart: { type: chartType, height: 280, toolbar: { show: false }, zoom: { enabled: false }, fontFamily: 'Inter, sans-serif', stacked: chartEl.dataset.stacked === 'true', ...stacking },
            series: series,
            colors: ['#6366f1', '#10b981', '#f97316', '#7c3aed', '#ef4444'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: chartType === 'bar' ? 0 : 2.5 },
            fill: { type: 'solid', opacity: chartType === 'area' ? 0.08 : 1 },
            markers: { size: chartType === 'area' ? 0 : 4, hover: { size: 5 } },
            xaxis: { categories: categories, labels: { show: chartEl.dataset.showXLabels === 'true', style: { fontSize: '10px', colors: '#a1a1aa' } }, axisBorder: { show: false }, axisTicks: { show: false } },
            yaxis: { labels: { style: { fontSize: '10px', colors: '#a1a1aa' } } },
            grid: { borderColor: '#f0f0f0', strokeDashArray: 4 },
            tooltip: { shared: true, intersect: false, x: { format: 'dd/MM' } },
            legend: { position: 'top', horizontalAlign: 'left', fontSize: '12px', fontWeight: 600, markers: { width: 8, height: 8, radius: 2 } },
        };
    }

    var currentTab = chartEl.dataset.defaultTab || 'actividad';
    var activeSeries;
    try { activeSeries = JSON.parse(chartEl.dataset['series' + currentTab.charAt(0).toUpperCase() + currentTab.slice(1)]); } catch(e) { activeSeries = []; }
    var chart = new ApexCharts(chartEl, buildOpts(activeSeries));
    chart.render();

    var tabContainer = chartEl.parentElement.querySelector('.chart-tabs, [data-chart-tabs]');
    if (!tabContainer) tabContainer = document.getElementById('chartTabs');
    if (tabContainer) {
        tabContainer.addEventListener('click', function(e) {
            var btn = e.target.closest('[data-tab]');
            if (!btn) return;
            var tab = btn.dataset.tab;
            if (tab === currentTab) return;

            tabContainer.querySelectorAll('[data-tab]').forEach(function(b) {
                b.classList.remove('bg-indigo-100', 'text-indigo-700');
                b.classList.add('bg-stone-100', 'text-stone-500');
            });
            btn.classList.remove('bg-stone-100', 'text-stone-500');
            btn.classList.add('bg-indigo-100', 'text-indigo-700');

            currentTab = tab;
            var key = 'series' + tab.charAt(0).toUpperCase() + tab.slice(1);
            var newSeries;
            try { newSeries = JSON.parse(chartEl.dataset[key]); } catch(e) { newSeries = []; }
            chart.updateSeries(newSeries);
        });
    }
}

function initDashboardCharts() {
    document.querySelectorAll('[data-dashboard-chart]').forEach(initSingleChart);
    // fallback for legacy #mainChart
    var legacy = document.getElementById('mainChart');
    if (legacy && !legacy.hasAttribute('data-dashboard-chart')) {
        legacy.setAttribute('data-dashboard-chart', '');
        initSingleChart(legacy);
    }

    // Sparklines
    document.querySelectorAll('[data-sparkline]').forEach(function(el) {
        var data;
        try { data = JSON.parse(el.dataset.sparkline); } catch(e) { data = []; }
        if (data.length === 0) return;
        var color = el.dataset.color || '#6366f1';
        var options = {
            chart: { type: 'line', height: 32, width: '100%', sparkline: { enabled: true }, fontFamily: 'Inter, sans-serif' },
            series: [{ data: data }],
            stroke: { curve: 'smooth', width: 1.5 },
            colors: [color],
            fill: { opacity: 0 },
            markers: { size: 0 },
            tooltip: { enabled: false },
        };
        new ApexCharts(el, options).render();
    });
}

function initForecastCharts() {
    document.querySelectorAll('[data-heatmap]').forEach(function(chartEl) {
        var series;
        try { series = JSON.parse(chartEl.dataset.heatmap); } catch(e) { series = []; }
        if (series.length === 0) return;

        var categories;
        try { categories = JSON.parse(chartEl.dataset.categories); } catch(e) { categories = []; }

        var totalCells = series.reduce(function(sum, s) { return sum + s.data.length; }, 0);
        var showLabels = totalCells < 80;

        var options = {
            chart: { type: 'heatmap', height: 360, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
            series: series,
            dataLabels: {
                enabled: showLabels,
                formatter: function(val) { return val > 0 ? Math.round(val * 100) + '%' : ''; },
                style: { fontSize: '10px', fontWeight: 600, colors: ['#1e1b18'] }
            },
            plotOptions: {
                heatmap: {
                    radius: 4,
                    enableShades: false,
                    colorScale: {
                        ranges: [
                            { from: 0, to: 0.29, color: '#f5f5f4', name: 'Baja' },
                            { from: 0.30, to: 0.59, color: '#fde68a', name: 'Media' },
                            { from: 0.60, to: 1.0, color: '#22c55e', name: 'Alta' },
                        ]
                    }
                }
            },
            tooltip: {
                custom: function({ series, seriesIndex, dataPointIndex, w }) {
                    var data = w.config.series[seriesIndex].data[dataPointIndex];
                    var pct = Math.round(data.y * 100);
                    var confMap = { alta: 'Alta', media: 'Media', baja: 'Baja' };
                    var conf = confMap[data.confidence] || '—';
                    var html = '<div class="p-2 text-sm">' +
                        '<b>' + w.config.series[seriesIndex].name + ' ' + data.x + '</b><br/>' +
                        'Probabilidad: <b>' + pct + '%</b><br/>' +
                        'Confianza: ' + conf;
                    if (data.observations) html += ' · ' + data.observations + ' muestras';
                    html += '</div>';
                    return html;
                }
            },
            xaxis: {
                categories: categories.length > 0 ? categories : undefined,
                labels: {
                    show: true,
                    style: { fontSize: '10px', colors: '#a1a1aa' },
                    rotate: categories.length > 10 ? -45 : 0,
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: { style: { fontSize: '10px', fontWeight: 600, colors: '#57534e' } }
            },
            grid: { show: false },
            legend: { show: false },
        };
        new ApexCharts(chartEl, options).render();
    });
}

function boot() {
    initDropzones();
    initModals();
    initToasts();
    initDashboardCharts();
    initForecastCharts();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}
