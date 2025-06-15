<?php
session_start();
include '../config/db_connection.php';
include_once '../libs/template_engine.php';

// Check if user is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header('Location: ../public/login.php');
    exit;
}

$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'] ?? 0;

// Get current user data
$stmt = $conn->prepare("SELECT username, email, no_telepon, alamat FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Get user stats for the card
$stats_query = $conn->prepare("
    SELECT 
        IFNULL(SUM(jumlah_kg), 0) as total_kg,
        IFNULL(SUM(total_point), 0) as total_points
    FROM laporan_sampah
    WHERE user_id = ? AND status_verifikasi = 'diterima'
");
$stats_query->bind_param("i", $user_id);
$stats_query->execute();
$stats_result = $stats_query->get_result();
$stats = $stats_result->fetch_assoc();

// Generate a unique card ID based on username and timestamp
$card_id = strtoupper(substr(md5($username . time()), 0, 8));

// QR code content - encode user information
$qr_content = json_encode([
    'username' => $username,
    'user_id' => $user_id,
    'card_id' => $card_id
]);

// QR code URL using api.qrserver.com
$qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qr_content) . "&bgcolor=FFFFFF&color=6b5a3a";


$role = $_SESSION['role'] ?? 'user';
?>

<!DOCTYPE html>
<html lang="id">

<head>    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Anggota - Trash2Cash</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>        /* Admin content with sidebar integration */
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        .admin-content {
            transition: margin-left 0.3s ease, width 0.3s ease;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        @media (min-width: 768px) {
            .admin-content {
                width: calc(100% - 30px);
                margin-left: 0;
            }
            
            body.sidebar-closed .admin-content {
                width: calc(100% - 30px);
                margin-left: 0;
            }
        }

        @media (max-width: 767px) {
            .admin-content {
                width: 100%;
                padding: 15px;
                margin-left: 0;
            }
        }

        /* Page header */
        .page-title {
            color: #2C3E50;
            font-size: 28px;
            margin-bottom: 20px;
            font-weight: 600;
            border-left: 4px solid #D2B48C;
            padding-left: 15px;
        }

        /* Card container */
        .card-container {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        /* Member card styling */
        .member-card {
            width: 100%;
            max-width: 600px;
            background: linear-gradient(135deg, #FFFDD0 0%, #f5f3d7 100%);
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
            position: relative;
        }

        .card-header {
            background-color: #D2B48C;
            color: white;
            padding: 15px 20px;
            position: relative;
        }

        .card-header h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
        }

        .card-id {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 14px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.8);
        }

        .card-body {
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
        }

        .qr-section {
            flex: 0 0 40%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }

        .qr-code {
            width: 200px;
            height: 200px;
            background-color: white;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .qr-code img {
            width: 100%;
            height: 100%;
        }

        .qr-label {
            margin-top: 10px;
            font-size: 14px;
            color: #6b5a3a;
            text-align: center;
        }

        .info-section {
            flex: 0 0 60%;
            padding: 10px 20px;
        }

        .logo-section {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .logo-img {
            width: 50px;
            height: 50px;
            margin-right: 15px;
        }

        .logo-text {
            font-size: 24px;
            font-weight: bold;
            color: #6b5a3a;
        }

        .user-info {
            margin-bottom: 20px;
        }

        .info-item {
            margin-bottom: 12px;
            display: flex;
        }

        .info-label {
            font-weight: 600;
            color: #6b5a3a;
            width: 100px;
        }

        .info-value {
            color: #333;
            flex: 1;
        }

        .stats-section {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            background-color: rgba(255, 255, 255, 0.5);
            padding: 10px;
            border-radius: 8px;
        }

        .stat-item {
            text-align: center;
            flex: 1;
        }

        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #D2B48C;
        }

        .stat-label {
            font-size: 12px;
            color: #6b5a3a;
        }

        .card-footer {
            background-color: #6b5a3a;
            color: white;
            padding: 10px 20px;
            font-size: 12px;
            text-align: center;
        }

        /* Print button */
        .print-container {
            text-align: center;
            margin: 20px 0;
        }

        .print-btn {
            background-color: #D2B48C;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }

        .print-btn:hover {
            background-color: #c3a679;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .print-btn i {
            margin-right: 8px;
        }

        /* Print styles */
        @media print {
            body * {
                visibility: hidden;
            }

            .member-card,
            .member-card * {
                visibility: visible;
            }

            .member-card {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                box-shadow: none;
            }

            .admin-content {
                margin: 0;
                padding: 0;
                width: 100%;
            }

            #sidebar,
            #toggle-btn,
            .print-container,
            .page-title {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <?php include_once 'user_sidebar.php'; ?>
    
    <div id="content">

    <div class="admin-content">
        <h1 class="page-title">Kartu Anggota Trash2Cash</h1>

        <div class="card-container">
            <div class="member-card">
                <div class="card-header">
                    <h2>KARTU ANGGOTA</h2>
                    <div class="card-id">ID: <?= $card_id ?></div>
                </div>
                <div class="card-body">
                    <div class="qr-section">
                        <div class="qr-code">
                            <img src="<?= $qr_url ?>" alt="QR Code">
                        </div>
                        <div class="qr-label">Scan untuk verifikasi</div>
                    </div>
                    <div class="info-section">
                        <div class="logo-section">
                            <img src="../assets/images/logo-brand.png" alt="Trash2Cash Logo" class="logo-img">
                            <div class="logo-text">Trash2Cash</div>
                        </div>
                        <div class="user-info">
                            <div class="info-item">
                                <div class="info-label">Username</div>
                                <div class="info-value">: <?= htmlspecialchars($user['username']) ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Alamat</div>
                                <div class="info-value">: <?= htmlspecialchars($user['alamat']) ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">No. Telp</div>
                                <div class="info-value">: <?= htmlspecialchars($user['no_telepon']) ?></div>
                            </div>
                        </div>
                        <div class="stats-section">
                            <div class="stat-item">
                                <div class="stat-value"><?= number_format($stats['total_kg'], 1) ?> kg</div>
                                <div class="stat-label">Total Sampah</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value"><?= number_format($stats['total_points']) ?></div>
                                <div class="stat-label">Total Poin</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    Kartu ini adalah identitas resmi anggota Trash2Cash. Tunjukkan saat menukarkan sampah di drop point.
                </div>
            </div>
        </div>

        <div class="print-container">
            <button class="print-btn" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak Kartu
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle sidebar toggle for responsive content layout
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggle-btn');
            const adminContent = document.querySelector('.admin-content');

            function updateContentLayout() {
                const isSidebarClosed = sidebar ? sidebar.classList.contains('closed') : false;
                document.body.classList.toggle('sidebar-closed', isSidebarClosed);

                if (adminContent) {
                    if (window.innerWidth >= 768) {
                        if (isSidebarClosed) {
                            adminContent.style.marginLeft = '20px';
                            adminContent.style.width = 'calc(100% - 30px)';
                        } else {
                            adminContent.style.marginLeft = '260px';
                            adminContent.style.width = 'calc(100% - 270px)';
                        }
                    } else {
                        adminContent.style.marginLeft = '0';
                        adminContent.style.width = '100%';
                    }
                }
            }

            // Initial setup
            updateContentLayout();

            // Listen for toggle button clicks
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function () {
                    setTimeout(updateContentLayout, 50);
                });
            }

            // Update on window resize too
            window.addEventListener('resize', updateContentLayout);
        });
    </script>    </div> <!-- Close content div -->
</body>

</html>