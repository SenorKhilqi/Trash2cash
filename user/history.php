<?php
session_start();
require_once '../config/db_connection.php';
include_once '../libs/template_engine.php';

// Pastikan user sudah login dan memiliki role user
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: public/login.php");
    exit();
}

$username = $_SESSION['username'];

// Dapatkan user_id dari username
$user_query = $conn->prepare("SELECT id FROM users WHERE username = ?");
$user_query->bind_param("s", $username);
$user_query->execute();
$user_result = $user_query->get_result();

if ($user_row = $user_result->fetch_assoc()) {
    $user_id = $user_row['id'];
    
    // Ambil riwayat laporan sampah user
    $query = "SELECT ls.*, ks.nama_kategori 
              FROM laporan_sampah ls 
              JOIN kategori_sampah ks ON ls.kategori_id = ks.id 
              WHERE ls.user_id = ? ORDER BY ls.tanggal_pengumpulan DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Hitung total jumlah transaksi
    $total_transaksi = $result->num_rows;
    
    // Hitung total point dari semua laporan
    $total_query = "SELECT SUM(total_point) as total_points FROM laporan_sampah WHERE user_id = ?";
    $total_stmt = $conn->prepare($total_query);
    $total_stmt->bind_param("i", $user_id);
    $total_stmt->execute();
    $total_result = $total_stmt->get_result();
    $total_row = $total_result->fetch_assoc();
    $total_points = $total_row['total_points'] ?: 0;
    
    // Hitung statistik per kategori
    $stats_query = "SELECT 
                        ks.nama_kategori, 
                        COUNT(*) as jumlah_laporan, 
                        SUM(ls.jumlah_kg) as total_kg,
                        SUM(ls.total_point) as kategori_points
                    FROM laporan_sampah ls
                    JOIN kategori_sampah ks ON ls.kategori_id = ks.id
                    WHERE ls.user_id = ? 
                    GROUP BY ls.kategori_id, ks.nama_kategori";
    $stats_stmt = $conn->prepare($stats_query);
    $stats_stmt->bind_param("i", $user_id);
    $stats_stmt->execute();
    $stats_result = $stats_stmt->get_result();
} else {
    echo "<script>alert('User tidak ditemukan!');</script>";
    header("Location: public/login.php");
    exit();
}

// Render navbar
renderTemplate('user', 'navbar');
?>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f5f5f5;
        margin: 0;
        padding: 0;
        transition: all 0.3s ease;
    }
    
    .history-container {
        transition: margin-left 0.3s ease;
        padding: 20px;
        margin: 20px;
        width: calc(100% - 40px);
        max-width: 1200px;
        margin-left: 260px; /* Sesuaikan dengan lebar sidebar */
    }
    
    .history-header {
        margin-bottom: 20px;
    }
    
    .history-header h2 {
        color: #333;
        margin-top: 0;
        border-left: 4px solid #4CAF50;
        padding-left: 15px;
    }
    
    .stats-cards {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    
    .stats-card {
        background-color: #ffffff;
        border-radius: 10px;
        padding: 20px;
        flex: 1;
        min-width: 250px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }
    
    .stats-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .stats-title {
        margin: 0;
        color: #343a40;
        font-size: 18px;
    }
    
    .stats-subtitle {
        margin: 5px 0 0;
        color: #6c757d;
        font-size: 14px;
    }
    
    .stats-value {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 70px;
        height: 70px;
        border-radius: 50%;
        color: white;
        font-size: 20px;
        font-weight: bold;
    }
    
    .blue {
        background-color: #17a2b8;
    }
    
    .green {
        background-color: #28a745;
    }
    
    .category-stats {
        background-color: #ffffff;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .category-title {
        margin-top: 0;
        color: #343a40;
        font-size: 18px;
        margin-bottom: 15px;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }
    
    .category-cards {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .category-card {
        color: white;
        padding: 15px;
        border-radius: 8px;
        min-width: 200px;
        flex: 1;
        transition: transform 0.3s ease;
    }
    
    .category-card:hover {
        transform: translateY(-5px);
    }
    
    .category-card h4 {
        margin-top: 0;
        margin-bottom: 10px;
        font-size: 16px;
    }
    
    .category-card p {
        margin: 5px 0;
        font-size: 14px;
    }
    
    .history-table-container {
        background-color: #ffffff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        overflow-x: auto;
    }
    
    .history-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    
    .history-table th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
        text-align: left;
        padding: 12px 15px;
        border-bottom: 2px solid #dee2e6;
    }
    
    .history-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }
    
    .history-table tr:hover {
        background-color: #f9f9f9;
    }
    
    .status-waiting {
        color: #fd7e14;
        font-weight: 500;
    }
    
    .status-accepted {
        color: #28a745;
        font-weight: 500;
    }
    
    .status-rejected {
        color: #dc3545;
        font-weight: 500;
    }
    
    .thumbnail {
        width: 80px;
        height: 60px;
        object-fit: cover;
        border-radius: 5px;
        cursor: pointer;
        transition: transform 0.3s ease;
    }
    
    .thumbnail:hover {
        transform: scale(1.1);
    }
    
    .download-button {
        display: inline-block;
        padding: 8px 12px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-size: 14px;
        transition: background-color 0.3s;
    }
    
    .download-button:hover {
        background-color: #0056b3;
    }
    
    .button-icon {
        vertical-align: text-bottom;
        margin-right: 5px;
    }
    
    .empty-state {
        padding: 30px;
        background-color: #f8f9fa;
        text-align: center;
        margin-top: 20px;
        border-radius: 10px;
        border: 1px dashed #dee2e6;
    }
    
    .empty-state p {
        color: #6c757d;
        margin-bottom: 15px;
    }
    
    .report-button {
        display: inline-block;
        margin-top: 10px;
        padding: 10px 20px;
        background-color: #4CAF50;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: 500;
        transition: background-color 0.3s;
    }
    
    .report-button:hover {
        background-color: #45a049;
    }
    
    /* Responsive */
    @media (max-width: 767px) {
        .history-container {
            margin-left: 20px;
            max-width: calc(100% - 40px);
        }
        
        .stats-cards {
            flex-direction: column;
        }
        
        .stats-card {
            width: 100%;
        }
    }
    
    /* Adjust container margin based on sidebar state */
    #sidebar.closed ~ .history-container {
        margin-left: 20px;
    }
</style>

<div class="history-container">
    <div class="history-header">
        <h2>Riwayat Laporan Sampah Saya</h2>
    </div>
    
    <!-- Card untuk menampilkan total transaksi dan poin -->
    <div class="stats-cards">
        <!-- Card untuk menampilkan jumlah transaksi -->
        <div class="stats-card">
            <div class="stats-header">
                <div>
                    <h3 class="stats-title">Total Transaksi</h3>
                    <p class="stats-subtitle">Jumlah pengumpulan sampah</p>
                </div>
                <div class="stats-value blue">
                    <?= number_format($total_transaksi) ?>
                </div>
            </div>
        </div>
        
        <!-- Card untuk menampilkan total poin -->
        <div class="stats-card">
            <div class="stats-header">
                <div>
                    <h3 class="stats-title">Total Poin</h3>
                    <p class="stats-subtitle">Hasil pengumpulan sampah</p>
                </div>
                <div class="stats-value green">
                    <?= number_format($total_points) ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Ringkasan statistik sampah per kategori -->
    <?php if ($stats_result->num_rows > 0): ?>
    <div class="category-stats">
        <h3 class="category-title">Statistik Pengumpulan Sampah</h3>
        <div class="category-cards">
            <?php
            // Reset pointer stats_result ke awal
            $stats_stmt->execute();
            $stats_result = $stats_stmt->get_result();
            
            $colors = [
                'plastik' => '#3498db', 
                'kertas' => '#f1c40f', 
                'logam' => '#95a5a6',
                'kaca' => '#1abc9c', 
                'elektronik' => '#e74c3c', 
                'organik' => '#2ecc71'
            ];
            
            while ($stat = $stats_result->fetch_assoc()): ?>
            <div class="category-card" style="background-color: <?= $colors[strtolower($stat['nama_kategori'])] ?? '#6c757d' ?>;">
                <h4><?= ucfirst($stat['nama_kategori']) ?></h4>
                <p>Jumlah: <?= $stat['jumlah_laporan'] ?> laporan</p>
                <p>Total: <?= number_format($stat['total_kg'], 1) ?> kg</p>
                <p style="font-weight: bold;">Poin: <?= number_format($stat['kategori_points']) ?></p>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($result->num_rows > 0): ?>
    <!-- Reset pointer result ke awal -->
    <?php 
    $stmt->execute();
    $result = $stmt->get_result();
    ?>
    <div class="history-table-container">
        <table class="history-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Jumlah (kg)</th>
                    <th>Drop Point</th>
                    <th>Status</th>
                    <th>Poin</th>
                    <th>Foto</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php 
                        $tanggal_pengumpulan = $row['tanggal_pengumpulan'];
                        $bulan = [
                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', 
                            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', 
                            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                        ];
                        
                        // Pecah tanggal berdasarkan format YYYY-MM-DD
                        $date_parts = explode('-', $tanggal_pengumpulan);
                        
                        // Validasi tanggal dan pastikan bukan 0000-00-00
                        if (count($date_parts) == 3 && $date_parts[0] != '0000') {
                            $tahun = $date_parts[0];
                            $bulan_angka = $date_parts[1];
                            $tanggal = $date_parts[2];
                            
                            // Hanya tampilkan jika bulan ada dalam array dan nilainya valid
                            if (isset($bulan[$bulan_angka]) && $bulan_angka != '00') {
                                echo $tanggal . ' ' . $bulan[$bulan_angka] . ' ' . $tahun;
                            } else {
                                echo date('d F Y'); // Tampilkan tanggal hari ini sebagai alternatif
                            }
                        } else {
                            echo date('d F Y'); // Tampilkan tanggal hari ini jika format tidak valid
                        }
                        ?>
                    </td>
                    <td><?= ucfirst(htmlspecialchars($row['nama_kategori'])) ?></td>
                    <td><?= htmlspecialchars($row['jumlah_kg']) ?> kg</td>
                    <td><?= htmlspecialchars($row['drop_point']) ?></td>
                    <td>
                        <?php if (isset($row['status_verifikasi'])): ?>
                            <?php if ($row['status_verifikasi'] == 'menunggu'): ?>
                                <span class="status-waiting">Menunggu</span>
                            <?php elseif ($row['status_verifikasi'] == 'diterima'): ?>
                                <span class="status-accepted">Diterima</span>
                            <?php else: ?>
                                <span class="status-rejected">Ditolak</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="status-waiting">Menunggu</span>
                        <?php endif; ?>
                    </td>
                    <td><strong><?= htmlspecialchars($row['total_point']) ?></strong></td>
                    <td>
                        <?php if (!empty($row['foto'])): ?>
                            <img class="thumbnail" src="uploads/<?= htmlspecialchars($row['foto']) ?>" 
                                 onclick="window.open('uploads/<?= htmlspecialchars($row['foto']) ?>', '_blank')">
                        <?php else: ?>
                            Tidak ada foto
                        <?php endif; ?>
                    </td>
                    <td><?= nl2br(htmlspecialchars($row['catatan'] ?? '')) ?></td>
                    <td>
                        <a href="generator_bukti.php?id=<?= $row['id'] ?>" class="download-button">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="button-icon">
                                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                            </svg>
                            Bukti
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <p>Belum ada riwayat pengumpulan sampah.</p>
        <a href="user/lapor_sampah.php" class="report-button">Laporkan Sampah Sekarang</a>
    </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const historyContainer = document.querySelector('.history-container');
        
        // Fungsi untuk menyesuaikan margin container berdasarkan status sidebar
        function adjustContainerMargin() {
            if (sidebar && sidebar.classList.contains('closed')) {
                historyContainer.style.marginLeft = '20px';
            } else {
                historyContainer.style.marginLeft = '260px';
            }
        }
        
        // Panggil sekali pada load
        adjustContainerMargin();
        
        // Tambahkan listener untuk perubahan kelas pada sidebar
        if (sidebar) {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        adjustContainerMargin();
                    }
                });
            });
            
            observer.observe(sidebar, { attributes: true });
        }
    });
</script>

<?php
// Render footer
renderTemplate('user', 'footer');
?>
