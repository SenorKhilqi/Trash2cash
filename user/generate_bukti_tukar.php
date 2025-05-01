<?php
session_start();
require_once '../config/db_connection.php';
include_once '../libs/template_engine.php';

// Pastikan user sudah login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: public/login.php");
    exit();
}

// Periksa parameter id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('ID Penukaran tidak valid!'); window.location.href='tukar_poin.php';</script>";
    exit();
}

$penukaran_id = $_GET['id'];
$username = $_SESSION['username'];

// Dapatkan user_id dari username
$user_query = $conn->prepare("SELECT id FROM users WHERE username = ?");
$user_query->bind_param("s", $username);
$user_query->execute();
$user_result = $user_query->get_result();
$user_row = $user_result->fetch_assoc();
$user_id = $user_row['id'];

// Ambil data penukaran poin
$query = "SELECT p.*, u.username 
          FROM penukaran_poin p 
          JOIN users u ON p.user_id = u.id 
          WHERE p.id = ? AND p.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $penukaran_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Data penukaran tidak ditemukan atau Anda tidak memiliki akses!'); window.location.href='tukar_poin.php';</script>";
    exit();
}

$row = $result->fetch_assoc();

// Update status menjadi dicetak jika belum
if ($row['status'] === 'menunggu') {
    $update_query = "UPDATE penukaran_poin SET status = 'dicetak' WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("i", $penukaran_id);
    $update_stmt->execute();
    $row['status'] = 'dicetak'; // Update status dalam array data juga
}

// Fungsi untuk menghasilkan nomor voucher unik
function generateVoucherNumber($penukaran_id) {
    $date = date('Ymd');
    return "VC-" . $date . "-" . str_pad($penukaran_id, 5, '0', STR_PAD_LEFT);
}

$voucher_number = generateVoucherNumber($penukaran_id);
$formatted_date = date('d F Y H:i:s', strtotime($row['waktu_penukaran']));
$formatted_nominal = "Rp " . number_format($row['nominal'], 0, ',', '.');

// Tentukan warna status
$status_color = '';
if ($row['status'] === 'diterima') {
    $status_color = 'green';
} elseif ($row['status'] === 'ditolak') {
    $status_color = 'red';
} elseif ($row['status'] === 'dicetak') {
    $status_color = 'blue';
} else {
    $status_color = 'orange';
}

// Render navbar
renderTemplate('user', 'navbar');
?>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        color: #333;
        margin: 0;
        padding: 0;
        background-color: #f5f5f5;
    }
    
    .voucher-main-container {
        transition: margin-left 0.3s ease;
        padding: 20px;
        margin: 20px;
        width: calc(100% - 40px);
        max-width: 800px;
        margin-left: 260px;
    }
    
    .page-title {
        text-align: center;
        margin-bottom: 20px;
    }
    
    .page-title h1 {
        margin-bottom: 5px;
        color: #28a745;
        font-size: 24px;
    }
    
    .voucher-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        padding: 30px;
        margin-bottom: 30px;
        border: 1px solid #e0e0e0;
    }
    
    .header {
        text-align: center;
        padding-bottom: 20px;
        border-bottom: 2px solid #28a745;
        margin-bottom: 30px;
    }
    
    .header h1 {
        color: #28a745;
        font-size: 24px;
        margin-bottom: 5px;
    }
    
    .header p {
        color: #777;
        margin-top: 0;
        font-size: 14px;
    }
    
    .voucher-info {
        text-align: center;
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 25px;
    }
    
    .voucher-number {
        font-size: 20px;
        font-weight: bold;
        color: #28a745;
        margin-bottom: 5px;
    }
    
    .details {
        margin-bottom: 30px;
    }
    
    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }
    
    .detail-label {
        font-weight: bold;
        color: #555;
    }
    
    .amount {
        text-align: center;
        background-color: #f0f8f0;
        padding: 20px;
        border-radius: 8px;
        margin: 30px 0;
    }
    
    .amount p {
        margin-top: 0;
        margin-bottom: 10px;
        color: #555;
    }
    
    .amount .nominal {
        font-size: 32px;
        font-weight: bold;
        color: #28a745;
    }
    
    .status {
        text-align: center;
        margin: 25px 0;
        padding: 12px;
        border-radius: 5px;
        font-weight: bold;
    }
    
    .barcode {
        text-align: center;
        margin: 30px 0;
    }
    
    .barcode-box {
        border: 2px solid #000;
        padding: 15px;
        display: inline-block;
        font-family: monospace;
        font-size: 16px;
        letter-spacing: 2px;
        font-weight: bold;
    }
    
    .contact-info {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
    }
    
    .contact-info p {
        margin: 5px 0;
        color: #555;
    }
    
    .footer {
        text-align: center;
        margin-top: 40px;
        padding-top: 20px;
        border-top: 1px solid #eee;
        color: #777;
        font-size: 13px;
        font-style: italic;
    }
    
    .btn-container {
        text-align: center;
        margin-top: 30px;
        margin-bottom: 50px;
    }
    
    .btn {
        display: inline-block;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        margin: 5px;
        transition: background-color 0.3s;
    }
    
    .btn-primary {
        background-color: #28a745;
        color: white;
    }
    
    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }
    
    .btn-primary:hover {
        background-color: #218838;
    }
    
    .btn-secondary:hover {
        background-color: #5a6268;
    }
    
    /* Responsive styles */
    @media (max-width: 767px) {
        .voucher-main-container {
            margin-left: 20px;
            max-width: calc(100% - 40px);
            padding: 10px;
        }
        
        .voucher-container {
            padding: 15px;
        }
        
        .header h1 {
            font-size: 20px;
        }
        
        .amount .nominal {
            font-size: 24px;
        }
    }
    
    /* Adjust container margin based on sidebar state */
    #sidebar.closed ~ .voucher-main-container {
        margin-left: 20px;
    }
    
    @media print {
        body {
            background-color: white;
            padding: 0;
        }
        
        .voucher-main-container {
            margin-left: 0;
            padding: 0;
            width: 100%;
            max-width: 100%;
        }
        
        .voucher-container {
            box-shadow: none;
            border: none;
        }
        
        .btn-container, #sidebar, #toggle-btn {
            display: none !important;
        }
    }
</style>

<div class="voucher-main-container">
    <div class="page-title">
        <h1>Bukti Penukaran Poin</h1>
        <p>Cetak atau simpan bukti ini sebagai referensi Anda</p>
    </div>
    
    <div class="voucher-container">
        <div class="header">
            <h1>BUKTI PENUKARAN POIN</h1>
            <p>Trash2Cash - Mengubah Sampah Menjadi Nilai</p>
        </div>
        
        <div class="voucher-info">
            <div class="voucher-number"><?= $voucher_number ?></div>
            <div class="transaction-date"><?= $formatted_date ?></div>
        </div>
        
        <div class="details">
            <div class="detail-row">
                <span class="detail-label">Username</span>
                <span><?= htmlspecialchars($row['username']) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Jumlah Poin Ditukar</span>
                <span><?= htmlspecialchars($row['poin']) ?> Poin</span>
            </div>
        </div>
        
        <div class="amount">
            <p>Nominal Penukaran</p>
            <div class="nominal"><?= $formatted_nominal ?></div>
        </div>
        
        <div class="status" style="color: <?= $status_color ?>; background-color: <?= $status_color === 'blue' ? '#cfe2ff' : ($status_color === 'green' ? '#d1e7dd' : ($status_color === 'red' ? '#f8d7da' : '#fff3cd')) ?>;">
            Status: <?= ucfirst($row['status']) ?>
        </div>
        
        <div class="barcode">
            <p>Tunjukkan bukti ini kepada petugas untuk pencairan dana</p>
            <div class="barcode-box">
                <?= $voucher_number ?>
            </div>
        </div>
        
        <div class="contact-info">
            <p>Nomor petugas : 085123456789</p>
            <p>Instagram : Trash2Cash.official</p>
            <p>Email : Trash2Cash_official@gmail.com</p>
        </div>
        
        <div class="footer">
            <p>Dokumen ini dihasilkan secara otomatis oleh sistem dan tidak memerlukan tanda tangan.</p>
            <p>Tanggal cetak: <?= date('d F Y H:i:s') ?></p>
        </div>
    </div>
    
    <div class="btn-container">
        <button class="btn btn-primary" onclick="window.print();">Cetak Bukti</button>
        <a href="riwayat_penukaran.php" class="btn btn-secondary">Lihat Riwayat Penukaran</a>
        <a href="tukar_poin.php" class="btn btn-secondary">Kembali</a>
        <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const voucherContainer = document.querySelector('.voucher-main-container');
        
        // Fungsi untuk menyesuaikan margin container berdasarkan status sidebar
        function adjustContainerMargin() {
            if (sidebar && sidebar.classList.contains('closed')) {
                voucherContainer.style.marginLeft = '20px';
            } else {
                voucherContainer.style.marginLeft = '260px';
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