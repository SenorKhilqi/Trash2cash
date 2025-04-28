<?php
session_start();
require_once '../config/db_connection.php';
include_once '../libs/template_engine.php';

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: /project_root/public/login.php");
    exit();
}

// Query untuk statistik card view
// 1. Total sampah terkumpul (kg)
$query_total_sampah = "SELECT SUM(jumlah_kg) as total_kg FROM laporan_sampah WHERE status_verifikasi = 'diterima'";
$result_sampah = $conn->query($query_total_sampah);
$total_sampah = $result_sampah->fetch_assoc()['total_kg'] ?: 0;

// 2. Total pengguna terdaftar
$query_total_users = "SELECT COUNT(*) as total_users FROM users WHERE role = 'user'";
$result_users = $conn->query($query_total_users);
$total_users = $result_users->fetch_assoc()['total_users'] ?: 0;

// 3. Total poin yang sudah didistribusikan
$query_total_poin = "SELECT SUM(total_point) as total_poin FROM laporan_sampah WHERE status_verifikasi = 'diterima'";
$result_poin = $conn->query($query_total_poin);
$total_poin = $result_poin->fetch_assoc()['total_poin'] ?: 0;

// Query untuk grafik pengumpulan sampah bulanan
$query_sampah_bulanan = "
    SELECT 
        DATE_FORMAT(tanggal_pengumpulan, '%Y-%m') as bulan,
        DATE_FORMAT(tanggal_pengumpulan, '%b %Y') as nama_bulan,
        SUM(jumlah_kg) as total_berat
    FROM 
        laporan_sampah
    WHERE 
        status_verifikasi = 'diterima'
        AND tanggal_pengumpulan >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY 
        DATE_FORMAT(tanggal_pengumpulan, '%Y-%m')
    ORDER BY 
        bulan ASC
";
$result_sampah_bulanan = $conn->query($query_sampah_bulanan);

$labels_bulanan = [];
$data_bulanan = [];
while ($row = $result_sampah_bulanan->fetch_assoc()) {
    $labels_bulanan[] = $row['nama_bulan'];
    $data_bulanan[] = floatval($row['total_berat']);
}

// Query untuk grafik kategori sampah (pie chart)
$query_kategori_sampah = "
    SELECT 
        ks.nama_kategori,
        SUM(ls.jumlah_kg) as total_berat
    FROM 
        laporan_sampah ls
    JOIN
        kategori_sampah ks ON ls.kategori_id = ks.id
    WHERE 
        ls.status_verifikasi = 'diterima'
    GROUP BY 
        ks.nama_kategori
    ORDER BY 
        total_berat DESC
";
$result_kategori_sampah = $conn->query($query_kategori_sampah);

$labels_kategori = [];
$data_kategori = [];
$colors_kategori = [
    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', 
    '#FF9F40', '#8AC249', '#EA5545', '#F46A9B', '#EF9B20'
];

$i = 0;
while ($row = $result_kategori_sampah->fetch_assoc()) {
    $labels_kategori[] = $row['nama_kategori'];
    $data_kategori[] = floatval($row['total_berat']);
    $i++;
}

$role = $_SESSION['role'] ?? 'admin'; // default ke admin jika tidak ada
renderTemplate($role, 'navbar');
?>

<!-- Mulai container untuk dashboard -->
<div class="admin-dashboard-container">
    <div class="admin-dashboard-header">
        <h1>Dashboard Admin</h1>
        <p>Selamat datang di panel admin Trash2Cash</p>
    </div>

    <!-- Statistik Ringkasan (Card View) -->
    <div class="stats-cards">
        <!-- Card untuk Total Sampah -->
        <div class="stat-card stat-card-blue">
            <div class="stat-card-content">
                <div class="stat-card-text">
                    <h3>Total Sampah</h3>
                    <p><?= number_format($total_sampah, 1) ?> kg</p>
                </div>
                <div class="stat-card-icon">
                    <i class="fas fa-trash"></i>
                </div>
            </div>
        </div>
        
        <!-- Card untuk Total Pengguna -->
        <div class="stat-card stat-card-green">
            <div class="stat-card-content">
                <div class="stat-card-text">
                    <h3>Pengguna</h3>
                    <p><?= number_format($total_users) ?></p>
                </div>
                <div class="stat-card-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <!-- Card untuk Total Poin -->
        <div class="stat-card stat-card-yellow">
            <div class="stat-card-content">
                <div class="stat-card-text">
                    <h3>Total Poin</h3>
                    <p><?= number_format($total_poin) ?></p>
                </div>
                <div class="stat-card-icon">
                    <i class="fas fa-star"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik-grafik -->
    <div class="charts-container">
        <!-- Grafik Pengumpulan Sampah Bulanan -->
        <div class="chart-card">
            <div class="chart-header">
                <h4>Pengumpulan Sampah Bulanan</h4>
            </div>
            <div class="chart-body">
                <canvas id="chartSampahBulanan"></canvas>
            </div>
        </div>

        <!-- Grafik Kategori Sampah -->
        <div class="chart-card">
            <div class="chart-header">
                <h4>Kategori Sampah</h4>
            </div>
            <div class="chart-body">
                <canvas id="chartKategoriSampah"></canvas>
            </div>
        </div>
    </div>
</div>

<style>
    /* Dashboard-specific styles */
    .admin-dashboard-container {
        padding: 20px;
        transition: all 0.3s ease;
        max-width: 1200px; /* Reduced from 1400px */
        margin: 0 auto;
    }
    
    .admin-dashboard-header {
        margin-bottom: 20px;
    }
    
    .admin-dashboard-header h1 {
        color: #2C3E50;
        font-size: 22px; /* Slightly smaller */
        margin-bottom: 5px;
        font-weight: 500;
    }
    
    .admin-dashboard-header p {
        color: #7b8a8b;
        font-size: 13px; /* Smaller */
    }
    
    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); /* Smaller cards */
        gap: 12px; /* Smaller gap */
        margin-bottom: 20px;
    }
    
    .stat-card {
        border-radius: 6px;
        padding: 14px; /* Smaller padding */
        color: white;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
    }
    
    .stat-card-blue {
        background: linear-gradient(135deg, #4e73df, #224abe);
    }
    
    .stat-card-green {
        background: linear-gradient(135deg, #1cc88a, #13855c);
    }
    
    .stat-card-yellow {
        background: linear-gradient(135deg, #f6c23e, #dda20a);
    }
    
    .stat-card-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .stat-card-text h3 {
        font-size: 12px; /* Smaller */
        font-weight: 500;
        text-transform: uppercase;
        margin: 0;
        opacity: 0.9;
        letter-spacing: 0.5px;
    }
    
    .stat-card-text p {
        font-size: 20px; /* Smaller */
        font-weight: 600;
        margin: 6px 0 0; /* Smaller */
    }
    
    .stat-card-icon {
        font-size: 24px; /* Smaller */
        opacity: 0.5;
    }
    
    .charts-container {
        display: grid;
        grid-template-columns: 1.5fr 1fr; /* Adjusted ratio */
        gap: 12px; /* Smaller gap */
        margin-top: 15px; /* Smaller */
        height: 260px; /* Reduced height from 300px */
    }
    
    .chart-card {
        background-color: white;
        border-radius: 6px;
        padding: 14px; /* Smaller padding */
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        overflow: hidden; /* Ensure nothing spills out */
    }
    
    .chart-header {
        border-bottom: 1px solid #edf2f7;
        padding-bottom: 8px; /* Smaller */
        margin-bottom: 12px; /* Smaller */
    }
    
    .chart-header h4 {
        margin: 0;
        color: #2d3748;
        font-size: 14px; /* Smaller */
        font-weight: 500;
    }
    
    .chart-body {
        height: 210px; /* Reduced from 250px */
    }
    
    /* Responsive adjustments for smaller screens */
    @media (max-width: 992px) {
        .charts-container {
            grid-template-columns: 1fr;
            height: auto;
        }
        
        .chart-card {
            margin-bottom: 15px;
            height: 240px; /* Fixed height for mobile */
        }
        
        .chart-body {
            height: 190px; /* Smaller for mobile */
        }
    }
    
    @media (max-width: 768px) {
        .admin-dashboard-container {
            padding: 12px 8px; /* Even smaller padding on mobile */
            width: 100%; /* Full width on small screens */
        }
        
        .stats-cards {
            grid-template-columns: 1fr;
        }
        
        .stat-card {
            padding: 12px;
        }
    }
    
    /* Ensure content fits within sidebar layout */
    @media (min-width: 769px) {
        .admin-dashboard-container {
            width: calc(100% - 270px); /* Account for sidebar */
            margin-left: 260px; /* Match sidebar width */
            transition: margin-left 0.3s ease, width 0.3s ease;
        }
        
        /* When sidebar is closed */
        .sidebar-closed .admin-dashboard-container {
            width: calc(100% - 30px);
            margin-left: 20px;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>

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
                    callback: function(value) {
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
                    label: function(context) {
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
document.addEventListener('DOMContentLoaded', function() {
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
        toggleBtn.addEventListener('click', function() {
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

<?php
renderTemplate($role, 'footer');
?>
