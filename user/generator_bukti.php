<?php
session_start();
require_once '../config/db_connection.php';

// Pastikan user sudah login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: public/login.php");
    exit();
}

// Periksa parameter id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('ID Laporan tidak valid!'); window.location.href='history.php';</script>";
    exit();
}

$laporan_id = $_GET['id'];
$username = $_SESSION['username'];

// Dapatkan user_id dari username
$user_query = $conn->prepare("SELECT id FROM users WHERE username = ?");
$user_query->bind_param("s", $username);
$user_query->execute();
$user_result = $user_query->get_result();
$user_row = $user_result->fetch_assoc();
$user_id = $user_row['id'];

// Ambil data laporan
$query = "SELECT l.*, u.username, k.nama_kategori as kategori
          FROM laporan_sampah l 
          JOIN users u ON l.user_id = u.id 
          JOIN kategori_sampah k ON l.kategori_id = k.id
          WHERE l.id = ? AND l.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $laporan_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Data laporan tidak ditemukan atau Anda tidak memiliki akses!'); window.location.href='history.php';</script>";
    exit();
}

$row = $result->fetch_assoc();

// Fungsi untuk menghasilkan nomor bukti unik
function generateReceiptNumber($laporan_id) {
    $date = date('Ymd');
    return "TC-" . $date . "-" . str_pad($laporan_id, 5, '0', STR_PAD_LEFT);
}

// Tentukan status warna
$status_color = '';
if ($row['status_verifikasi'] == 'diterima') {
    $status_color = 'green';
} elseif ($row['status_verifikasi'] == 'ditolak') {
    $status_color = 'red';
} else {
    $status_color = 'orange';
}

// Ubah format tanggal
$tanggal_pengumpulan = date('d-m-Y', strtotime($row['tanggal_pengumpulan']));
$tanggal_cetak = date('d-m-Y H:i');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pengumpulan Sampah - <?= generateReceiptNumber($laporan_id) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 24px;
            color: #2C3E50;
            margin-bottom: 10px;
        }
        .receipt-number {
            text-align: right;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .info-section {
            margin-bottom: 25px;
        }
        .info-section h2 {
            font-size: 18px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            width: 180px;
            font-weight: bold;
        }
        .info-value {
            flex: 1;
        }
        .divider {
            border-top: 1px dashed #ccc;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #777;
            font-style: italic;
        }
        .photo-section {
            margin: 20px 0;
        }
        .photo-section img {
            max-width: 300px;
            max-height: 300px;
            border: 1px solid #ddd;
        }
        .print-btn {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            display: block;
            margin: 20px auto;
            text-align: center;
            text-decoration: none;
            width: 150px;
        }
        .back-btn {
            background-color: #6C757D;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            display: block;
            margin: 20px auto;
            text-align: center;
            text-decoration: none;
            width: 150px;
        }
        @media print {
            .print-btn, .back-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BUKTI PENGUMPULAN SAMPAH</h1>
    </div>
    
    <div class="receipt-number">
        <div>Nomor Bukti: <?= generateReceiptNumber($laporan_id) ?></div>
        <div>Tanggal Cetak: <?= $tanggal_cetak ?></div>
    </div>
    
    <div class="divider"></div>
    
    <div class="info-section">
        <h2>Informasi Pengguna</h2>
        <div class="info-row">
            <div class="info-label">Username</div>
            <div class="info-value">: <?= htmlspecialchars($row['username']) ?></div>
        </div>
    </div>
    
    <div class="info-section">
        <h2>Detail Pengumpulan Sampah</h2>
        <div class="info-row">
            <div class="info-label">Tanggal Pengumpulan</div>
            <div class="info-value">: <?= $tanggal_pengumpulan ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Kategori Sampah</div>
            <div class="info-value">: <?= ucfirst(htmlspecialchars($row['kategori'])) ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Jumlah</div>
            <div class="info-value">: <?= htmlspecialchars($row['jumlah_kg']) ?> kg</div>
        </div>
        <div class="info-row">
            <div class="info-label">Drop Point</div>
            <div class="info-value">: <?= htmlspecialchars($row['drop_point']) ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Status Verifikasi</div>
            <div class="info-value">: <span style="color: <?= $status_color ?>;"><?= ucfirst(htmlspecialchars($row['status_verifikasi'])) ?></span></div>
        </div>
        <div class="info-row">
            <div class="info-label">Poin yang Didapatkan</div>
            <div class="info-value">: <strong><?= htmlspecialchars($row['total_point']) ?> poin</strong></div>
        </div>
        
        <?php if (!empty($row['catatan'])): ?>
        <div class="info-row">
            <div class="info-label">Catatan</div>
            <div class="info-value">: <?= nl2br(htmlspecialchars($row['catatan'])) ?></div>
        </div>
        <?php endif; ?>
    </div>
    
    <?php if (!empty($row['foto'])): ?>
    <div class="info-section photo-section">
        <h2>Bukti Foto</h2>
        <img src="uploads/<?= htmlspecialchars($row['foto']) ?>" alt="Bukti Foto">
    </div>
    <?php endif; ?>
    
    <div class="divider"></div>
    
    <div class="footer">
        <p>Dokumen ini dihasilkan secara otomatis oleh sistem dan tidak memerlukan tanda tangan.</p>
        <p>Trash2Cash - Mengubah Sampah Menjadi Nilai</p>
    </div>
    
    <button class="print-btn" onclick="window.print();">Cetak Bukti</button>
    <a href="history.php" class="back-btn">Kembali</a>
</body>
</html>