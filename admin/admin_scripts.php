<script>
    // Data untuk Grafik Sampah Bulanan
    const labelsBulanan = <?= json_encode($labels_bulanan) ?>;
    const dataBulanan = <?= json_encode($data_bulanan) ?>;

    // Data untuk Grafik Kategori Sampah
    const labelsKategori = <?= json_encode($labels_kategori) ?>;
    const dataKategori = <?= json_encode($data_kategori) ?>;
    const colorsKategori = <?= json_encode(array_slice($colors_kategori, 0, count($labels_kategori))) ?>;

    // Inisialisasi Grafik Sampah Bulanan (Bar Chart)
    const ctxBulanan = document.getElementById('chartSampahBulanan').getContext('2d');
    const chartSampahBulanan = new Chart(ctxBulanan, {
        type: 'bar',
        data: {
            labels: labelsBulanan,
            datasets: [{
                label: 'Total Sampah (kg)',
                data: dataBulanan,
                backgroundColor: 'rgba(78, 115, 223, 0.7)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false // Hide legend for minimalist look
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return value + ' kg';
                        },
                        font: {
                            size: 9 // Even smaller font
                        }
                    },
                    grid: {
                        display: false // Remove grid lines for cleaner look
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 8 // Even smaller font
                        },
                        maxRotation: 45, // Allow rotation for long month names
                        minRotation: 45
                    },
                    grid: {
                        display: false // Remove grid lines for cleaner look
                    }
                }
            },
            layout: {
                padding: {
                    left: 5,
                    right: 5,
                    top: 5,
                    bottom: 5
                }
            },
            devicePixelRatio: 2 // Sharper rendering on all devices
        }
    });

    // Inisialisasi Grafik Kategori Sampah (Pie Chart)
    const ctxKategori = document.getElementById('chartKategoriSampah').getContext('2d');
    const chartKategoriSampah = new Chart(ctxKategori, {
        type: 'doughnut',
        data: {
            labels: labelsKategori,
            datasets: [{
                data: dataKategori,
                backgroundColor: colorsKategori,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: {
                            size: 9 // Smaller legend text
                        },
                        boxWidth: 8 // Smaller legend color boxes
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const label = context.label || '';
                            const value = context.raw;
                            const total = context.dataset.data.reduce((acc, data) => acc + data, 0);
                            const percentage = Math.round((value / total * 100) * 10) / 10;
                            return `${label}: ${value} kg (${percentage}%)`;
                        }
                    }
                }
            },
            cutout: '60%' // Slightly larger hole for more minimal look
        }
    });

    // Script to properly handle sidebar toggling and content adjustment
    document.addEventListener('DOMContentLoaded', function () {
        // References
        const dashboardContainer = document.querySelector('.admin-dashboard-container');
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggle-btn');

        // Initialize dashboard position based on sidebar state
        function updateDashboardLayout() {
            if (sidebar) {
                const isSidebarClosed = sidebar.classList.contains('closed');

                // Add or remove a helper class to the body
                document.body.classList.toggle('sidebar-closed', isSidebarClosed);

                if (isSidebarClosed) {
                    dashboardContainer.style.marginLeft = '20px';
                    dashboardContainer.style.width = 'calc(100% - 30px)';
                } else {
                    dashboardContainer.style.marginLeft = '260px';
                    dashboardContainer.style.width = 'calc(100% - 270px)';
                }

                // Force chart resize after layout changes
                setTimeout(() => {
                    chartSampahBulanan.resize();
                    chartKategoriSampah.resize();
                }, 300);
            }
        }

        // Initial setup
        updateDashboardLayout();

        // Listen for toggle button clicks
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                // Allow time for the sidebar transition to begin
                setTimeout(updateDashboardLayout, 50);
            });
        }

        // Update on window resize too
        window.addEventListener('resize', updateDashboardLayout);

        // Ensure charts are properly sized initially
        setTimeout(() => {
            chartSampahBulanan.resize();
            chartKategoriSampah.resize();
        }, 100);
    });
</script>