<?php
session_start();
require_once '../config/db_connection.php';
include_once '../libs/template_engine.php';

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: /public/login.php");
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
    '#FF6384',
    '#36A2EB',
    '#FFCE56',
    '#4BC0C0',
    '#9966FF',
    '#FF9F40',
    '#8AC249',
    '#EA5545',
    '#F46A9B',
    '#EF9B20'
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

<?php include "admin_styles.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>

<?php include "admin_scripts.php"; ?>

<?php
renderTemplate($role, 'footer');
?>