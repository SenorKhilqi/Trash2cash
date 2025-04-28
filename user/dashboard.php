<?php
session_start();
require_once '../config/db_connection.php';
include_once '../libs/template_engine.php';

// Pastikan user sudah login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: /project_root/public/login.php");
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
    
    // Hitung total point dari semua laporan yang statusnya diterima
    $total_query = "SELECT SUM(total_point) as total_points FROM laporan_sampah WHERE user_id = ? AND status_verifikasi = 'diterima'";
    $total_stmt = $conn->prepare($total_query);
    $total_stmt->bind_param("i", $user_id);
    $total_stmt->execute();
    $total_result = $total_stmt->get_result();
    $total_row = $total_result->fetch_assoc();
    $total_points = $total_row['total_points'] ?: 0;
    
    // Hitung total poin yang sudah ditukarkan
    $used_query = "SELECT SUM(poin) as used_points FROM penukaran_poin WHERE user_id = ?";
    $used_stmt = $conn->prepare($used_query);
    $used_stmt->bind_param("i", $user_id);
    $used_stmt->execute();
    $used_result = $used_stmt->get_result();
    $used_row = $used_result->fetch_assoc();
    $used_points = $used_row['used_points'] ?: 0;
    
    // Hitung sisa poin yang dapat ditukarkan
    $available_points = $total_points - $used_points;

    // Ambil jumlah transaksi
    $transactions_query = "SELECT COUNT(*) as total_transactions FROM laporan_sampah WHERE user_id = ? AND status_verifikasi = 'diterima'";
    $transactions_stmt = $conn->prepare($transactions_query);
    $transactions_stmt->bind_param("i", $user_id);
    $transactions_stmt->execute();
    $transactions_result = $transactions_stmt->get_result();
    $transactions_row = $transactions_result->fetch_assoc();
    $total_transactions = $transactions_row['total_transactions'] ?: 0;
} else {
    echo "<script>alert('User tidak ditemukan!');</script>";
    header("Location: /project_root/public/login.php");
    exit();
}

$role = $_SESSION['role'] ?? 'user'; // default ke user jika tidak ada
renderTemplate($role, 'navbar');
?>

<style>
    /* CSS untuk Dashboard User */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f5f5f5;
        margin: 0;
        padding: 0;
        transition: all 0.3s ease;
    }

    .dashboard-container {
        transition: margin-left 0.3s ease;
        padding: 20px;
        margin: 20px;
        width: calc(100% - 40px);
        max-width: 1200px;
        margin-left: 260px; /* Sesuaikan dengan lebar sidebar */
    }

    .dashboard-header {
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .dashboard-header h2 {
        color: #333;
        margin-top: 0;
        border-left: 4px solid #D2B48C;
        padding-left: 15px;
    }

    .stats-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin: 20px 0;
    }

    .stats-card {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        flex: 1;
        min-width: 250px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px rgba(0,0,0,0.1);
    }

    .stats-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .stats-title {
        margin: 0;
        color: #333;
    }

    .stats-subtitle {
        margin: 5px 0 0;
        color: #777;
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

    .green {
        background-color: #FFFDD0;
        color: #6b5a3a;
    }

    .blue {
        background-color: #FFFDD0;
        color: #6b5a3a;
        border: 1px solid #D2B48C;
    }

    .orange {
        background-color: #FFFDD0;
        color: #6b5a3a;
        border: 1px solid #D2B48C;
    }

    .action-button {
        display: inline-block;
        background-color: #D2B48C;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        text-align: center;
        margin-top: 15px;
        transition: background-color 0.3s;
        width: 100%;
    }

    .action-button:hover {
        opacity: 0.9;
    }

    .green-button {
        background-color: #FFFDD0;
        color: #6b5a3a;
        border: 1px solid #D2B48C;
    }

    .blue-button {
        background-color: #FFFDD0;
        color: #6b5a3a;
        border: 1px solid #D2B48C;
    }

    .orange-button {
        background-color: #FFFDD0;
        color: #6b5a3a;
        border: 1px solid #D2B48C;
    }

    .info-card {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        margin: 30px 0;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .info-card h3 {
        color: #333;
        margin-top: 0;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }

    .point-exchange-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }

    .point-item {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        transition: transform 0.3s, background-color 0.3s;
    }

    .point-item:hover {
        transform: scale(1.05);
        background-color: #e9ecef;
    }

    .point-item strong {
        font-size: 18px;
        display: block;
        color: #D2B48C;
    }

    .point-item p {
        margin: 5px 0 0;
        color: #555;
    }

    .main-cta {
        text-align: center;
        margin-top: 25px;
    }

    .main-action-button {
        display: inline-block;
        background-color: #FFFDD0;
        color: #6b5a3a;
        padding: 12px 30px;
        text-decoration: none;
        border-radius: 5px;
        font-size: 16px;
        font-weight: bold;
        border: 1px solid #D2B48C;
        transition: transform 0.2s, background-color 0.2s;
    }

    .main-action-button:hover {
        transform: scale(1.05);
        background-color: #f5f3d7;
    }

    /* Responsive behavior for sidebar open/closed state */
    @media (max-width: 767px) {
        .dashboard-container {
            margin-left: 20px;
            max-width: calc(100% - 40px);
        }
    }

    /* Adjust dashboard container based on sidebar state */
    #sidebar.closed ~ .dashboard-container {
        margin-left: 20px;
    }
</style>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h2>Selamat Datang di Dashboard Trash2Cash</h2>
        <p>Halo, <strong><?= htmlspecialchars($username) ?></strong>! Berikut adalah informasi akun Anda:</p>
    </div>

    <!-- Card untuk statistik poin -->
    <div class="stats-container">
        <!-- Card untuk poin tersedia -->
        <div class="stats-card">
            <div class="stats-header">
                <div>
                    <h3 class="stats-title">Poin Tersedia</h3>
                    <p class="stats-subtitle">Poin yang dapat ditukar</p>
                </div>
                <div class="stats-value green">
                    <?= number_format($available_points) ?>
                </div>
            </div>
            <a href="tukar_poin.php" class="action-button green-button">Tukar Poin Sekarang</a>
        </div>
        
        <!-- Card untuk total transaksi -->
        <div class="stats-card">
            <div class="stats-header">
                <div>
                    <h3 class="stats-title">Total Transaksi</h3>
                    <p class="stats-subtitle">Jumlah laporan diterima</p>
                </div>
                <div class="stats-value blue">
                    <?= number_format($total_transactions) ?>
                </div>
            </div>
            <a href="history.php" class="action-button blue-button">Lihat Riwayat</a>
        </div>

        <!-- Card untuk poin yang sudah ditukar -->
        <div class="stats-card">
            <div class="stats-header">
                <div>
                    <h3 class="stats-title">Poin Ditukarkan</h3>
                    <p class="stats-subtitle">Poin yang sudah ditukar</p>
                </div>
                <div class="stats-value orange">
                    <?= number_format($used_points) ?>
                </div>
            </div>
            <a href="riwayat_penukaran.php" class="action-button orange-button">Riwayat Penukaran</a>
        </div>
    </div>

    <!-- Informasi nilai tukar poin -->
    <div class="info-card">
        <h3>Nilai Tukar Poin</h3>
        <p>Anda dapat menukarkan poin Anda dengan nilai uang sesuai ketentuan berikut:</p>
        
        <div class="point-exchange-grid">
            <div class="point-item">
                <strong>50 Poin</strong>
                <p>Rp 5.000</p>
            </div>
            <div class="point-item">
                <strong>100 Poin</strong>
                <p>Rp 10.000</p>
            </div>
            <div class="point-item">
                <strong>150 Poin</strong>
                <p>Rp 15.000</p>
            </div>
            <div class="point-item">
                <strong>190 Poin</strong>
                <p>Rp 20.000</p>
            </div>
            <div class="point-item">
                <strong>285 Poin</strong>
                <p>Rp 30.000</p>
            </div>
            <div class="point-item">
                <strong>380 Poin</strong>
                <p>Rp 40.000</p>
            </div>
        </div>
        
        <div class="main-cta">
            <a href="tukar_poin.php" class="main-action-button">Tukar Poin Sekarang</a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const dashboardContainer = document.querySelector('.dashboard-container');
        
        // Fungsi untuk memeriksa status sidebar dan menyesuaikan margin container
        function adjustContainerMargin() {
            if (sidebar && sidebar.classList.contains('closed')) {
                dashboardContainer.style.marginLeft = '20px';
            } else {
                dashboardContainer.style.marginLeft = '260px';
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
        
        // Tambahkan listener untuk tombol toggle di navbar
        const toggleBtn = document.getElementById('toggle-btn');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                // Sidebar toggle dilakukan pada file navbar, kita hanya perlu memastikan
                // margin dashboard container diatur dengan benar lewat observer di atas
            });
        }
    });
</script>

<?php
renderTemplate($role, 'footer');
?>
