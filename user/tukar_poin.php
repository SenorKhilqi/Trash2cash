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
$message = '';
$showForm = true;

// Dapatkan user_id dan total poin dari username
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
    
    // Proses penukaran poin jika form disubmit
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tukar_poin'])) {
        $poin_pilihan = intval($_POST['poin_pilihan']);
        
        // Tentukan nominal berdasarkan poin
        $nominals = [
            50 => 5000,
            100 => 10000,
            150 => 15000,
            190 => 20000,
            285 => 30000,
            380 => 40000
        ];
        
        if (!array_key_exists($poin_pilihan, $nominals)) {
            $message = '<div class="alert alert-danger">Pilihan poin tidak valid!</div>';
        } elseif ($poin_pilihan > $available_points) {
            $message = '<div class="alert alert-danger">Poin Anda tidak mencukupi! Anda memiliki ' . $available_points . ' poin tersedia.</div>';
        } else {
            $nominal = $nominals[$poin_pilihan];
            
            // Insert ke database
            $insert_query = $conn->prepare("INSERT INTO penukaran_poin (user_id, poin, nominal, waktu_penukaran) VALUES (?, ?, ?, NOW())");
            $insert_query->bind_param("iii", $user_id, $poin_pilihan, $nominal);
            
            if ($insert_query->execute()) {
                $penukaran_id = $conn->insert_id;
                $message = '<div class="alert alert-success">Penukaran berhasil! Poin Anda telah berkurang sebanyak ' . $poin_pilihan . '.</div>';
                $showForm = false;
                
                // Redirect ke halaman bukti penukaran
                header("Location: generate_bukti_tukar.php?id=" . $penukaran_id);
                exit;
            } else {
                $message = '<div class="alert alert-danger">Terjadi kesalahan: ' . $conn->error . '</div>';
            }
        }
    }
} else {
    echo "<script>alert('User tidak ditemukan!');</script>";
    header("Location: public/login.php");
    exit();
}

// Include the direct sidebar instead of using the template engine
include_once 'user_sidebar.php';
?>

<!-- Main Content Container -->
<div id="content">

<style>
    body {
        background-color: #f5f5f5;
        margin: 0;
        padding: 0;
    }
      .exchange-container {
        transition: margin-left 0.3s ease;
        padding: 20px;
        margin: 20px;
        width: calc(100% - 40px);
        max-width: 1200px;
    }
    
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .card {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .points-card {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .points-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .points-title {
        margin: 0;
        color: #343a40;
    }
    
    .points-subtitle {
        margin: 5px 0 0;
        color: #6c757d;
    }
    
    .points-value {
        background-color: #FFFDD0;
        color: #6b5a3a;
        padding: 15px 25px;
        border-radius: 50px;
        font-size: 24px;
        font-weight: bold;
        border: 1px solid #D2B48C;
    }
    
    .exchange-options {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-top: 20px;
    }
    
    .option-card {
        width: calc(33.333% - 14px);
        min-width: 250px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border: 1px solid #e0e0e0;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .option-card:hover:not(.disabled) {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }
    
    .disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .option-header {
        background-color: #f8f9fa;
        padding: 15px;
        text-align: center;
    }
    
    .option-title {
        margin-top: 0;
        color: #343a40;
        font-size: 22px;
    }
    
    .option-body {
        padding: 15px;
        text-align: center;
    }
    
    .option-value {
        font-size: 18px;
        font-weight: bold;
        color: #D2B48C;
        margin-bottom: 15px;
    }
    
    .exchange-button {
        background-color: #FFFDD0;
        color: #6b5a3a;
        border: 1px solid #D2B48C;
        padding: 12px 15px;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
        font-weight: 600;
        font-size: 16px;
        transition: background-color 0.3s;
    }
    
    .exchange-button:hover:not([disabled]) {
        background-color: #f5f3d7;
    }
    
    .exchange-button[disabled] {
        background-color: #cccccc;
        cursor: not-allowed;
    }
    
    .history-link {
        color: #007BFF;
        text-decoration: none;
        font-weight: 500;
        display: inline-block;
        margin-top: 15px;
        transition: color 0.3s;
    }
    
    .history-link:hover {
        color: #0056b3;
        text-decoration: underline;
    }
    
    .center-text {
        text-align: center;
    }
    
    /* Responsive */
    @media (max-width: 767px) {
        .exchange-container {
            margin-left: 20px;
            max-width: calc(100% - 40px);
        }
        
        .points-header {
            flex-direction: column;
            text-align: center;
        }
        
        .points-value {
            margin-top: 15px;
        }
        
        .option-card {
            width: 100%;
        }
    }
    
    /* Adjust container margin based on sidebar state */
    #sidebar.closed ~ .exchange-container {
        margin-left: 20px;
    }
</style>

<div class="exchange-container">
    <h2>Penukaran Poin</h2>
    
    <!-- Card untuk menampilkan total poin tersedia -->
    <div class="points-card">
        <div class="points-header">
            <div>
                <h3 class="points-title">Poin Tersedia</h3>
                <p class="points-subtitle">Total poin yang dapat ditukarkan</p>
            </div>
            <div class="points-value">
                <?= number_format($available_points) ?> Poin
            </div>
        </div>
    </div>
    
    <?= $message ?>
    
    <?php if ($showForm): ?>
    <div class="card">
        <h3>Pilih Jumlah Poin</h3>
        <p>Silakan pilih jumlah poin yang ingin Anda tukarkan:</p>

        <div class="exchange-options">
            <!-- Card untuk opsi 50 poin -->
            <div class="option-card <?= $available_points < 50 ? 'disabled' : '' ?>">
                <div class="option-header">
                    <h4 class="option-title">50 Poin</h4>
                </div>
                <div class="option-body">
                    <div class="option-value">Rp 5.000</div>
                    <?php if ($available_points >= 50): ?>
                    <form method="post">
                        <input type="hidden" name="poin_pilihan" value="50">
                        <button type="submit" name="tukar_poin" class="exchange-button">Tukar Sekarang</button>
                    </form>
                    <?php else: ?>
                    <button disabled class="exchange-button">Tukar Sekarang</button>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Card untuk opsi 100 poin -->
            <div class="option-card <?= $available_points < 100 ? 'disabled' : '' ?>">
                <div class="option-header">
                    <h4 class="option-title">100 Poin</h4>
                </div>
                <div class="option-body">
                    <div class="option-value">Rp 10.000</div>
                    <?php if ($available_points >= 100): ?>
                    <form method="post">
                        <input type="hidden" name="poin_pilihan" value="100">
                        <button type="submit" name="tukar_poin" class="exchange-button">Tukar Sekarang</button>
                    </form>
                    <?php else: ?>
                    <button disabled class="exchange-button">Tukar Sekarang</button>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Card untuk opsi 150 poin -->
            <div class="option-card <?= $available_points < 150 ? 'disabled' : '' ?>">
                <div class="option-header">
                    <h4 class="option-title">150 Poin</h4>
                </div>
                <div class="option-body">
                    <div class="option-value">Rp 15.000</div>
                    <?php if ($available_points >= 150): ?>
                    <form method="post">
                        <input type="hidden" name="poin_pilihan" value="150">
                        <button type="submit" name="tukar_poin" class="exchange-button">Tukar Sekarang</button>
                    </form>
                    <?php else: ?>
                    <button disabled class="exchange-button">Tukar Sekarang</button>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Card untuk opsi 190 poin -->
            <div class="option-card <?= $available_points < 190 ? 'disabled' : '' ?>">
                <div class="option-header">
                    <h4 class="option-title">190 Poin</h4>
                </div>
                <div class="option-body">
                    <div class="option-value">Rp 20.000</div>
                    <?php if ($available_points >= 190): ?>
                    <form method="post">
                        <input type="hidden" name="poin_pilihan" value="190">
                        <button type="submit" name="tukar_poin" class="exchange-button">Tukar Sekarang</button>
                    </form>
                    <?php else: ?>
                    <button disabled class="exchange-button">Tukar Sekarang</button>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Card untuk opsi 285 poin -->
            <div class="option-card <?= $available_points < 285 ? 'disabled' : '' ?>">
                <div class="option-header">
                    <h4 class="option-title">285 Poin</h4>
                </div>
                <div class="option-body">
                    <div class="option-value">Rp 30.000</div>
                    <?php if ($available_points >= 285): ?>
                    <form method="post">
                        <input type="hidden" name="poin_pilihan" value="285">
                        <button type="submit" name="tukar_poin" class="exchange-button">Tukar Sekarang</button>
                    </form>
                    <?php else: ?>
                    <button disabled class="exchange-button">Tukar Sekarang</button>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Card untuk opsi 380 poin -->
            <div class="option-card <?= $available_points < 380 ? 'disabled' : '' ?>">
                <div class="option-header">
                    <h4 class="option-title">380 Poin</h4>
                </div>
                <div class="option-body">
                    <div class="option-value">Rp 40.000</div>
                    <?php if ($available_points >= 380): ?>
                    <form method="post">
                        <input type="hidden" name="poin_pilihan" value="380">
                        <button type="submit" name="tukar_poin" class="exchange-button">Tukar Sekarang</button>
                    </form>
                    <?php else: ?>
                    <button disabled class="exchange-button">Tukar Sekarang</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="center-text">
        <a href="riwayat_penukaran.php" class="history-link">Lihat Riwayat Penukaran Poin</a>
    </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const exchangeContainer = document.querySelector('.exchange-container');
        
        // Fungsi untuk menyesuaikan margin container berdasarkan status sidebar        function adjustContainerMargin() {
            if (sidebar && sidebar.classList.contains('closed')) {
                exchangeContainer.style.marginLeft = '20px';
            } else {
                exchangeContainer.style.marginLeft = '0'; // No extra margin needed since we're inside #content
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

</div> <!-- Close content div -->
