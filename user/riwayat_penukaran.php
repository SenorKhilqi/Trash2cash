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
    
    // Ambil riwayat penukaran poin
    $query = "SELECT * FROM penukaran_poin WHERE user_id = ? ORDER BY waktu_penukaran DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Hitung total poin yang sudah ditukarkan
    $total_query = "SELECT SUM(poin) as total_exchanged_points, SUM(nominal) as total_nominal FROM penukaran_poin WHERE user_id = ?";
    $total_stmt = $conn->prepare($total_query);
    $total_stmt->bind_param("i", $user_id);
    $total_stmt->execute();
    $total_result = $total_stmt->get_result();
    $total_row = $total_result->fetch_assoc();
    $total_exchanged_points = $total_row['total_exchanged_points'] ?: 0;
    $total_nominal = $total_row['total_nominal'] ?: 0;
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
    
    .exchange-history-container {
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
        min-width: 80px;
        padding: 10px 15px;
        border-radius: 6px;
        color: white;
        font-size: 18px;
        font-weight: bold;
        text-align: center;
    }
    
    .blue {
        background-color: #17a2b8;
    }
    
    .green {
        background-color: #28a745;
    }
    
    .action-button {
        text-align: right;
        margin-bottom: 20px;
    }
    
    .exchange-button {
        display: inline-block;
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        font-weight: 500;
        transition: background-color 0.3s;
    }
    
    .exchange-button:hover {
        background-color: #45a049;
        transform: translateY(-2px);
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
    
    .status-processing {
        color: #17a2b8;
        font-weight: 500;
    }
    
    .status-rejected {
        color: #dc3545;
        font-weight: 500;
    }
    
    .bukti-button {
        display: inline-block;
        padding: 8px 12px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-size: 14px;
        transition: background-color 0.3s;
    }
    
    .bukti-button:hover {
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
    
    /* Responsive */
    @media (max-width: 767px) {
        .exchange-history-container {
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
    #sidebar.closed ~ .exchange-history-container {
        margin-left: 20px;
    }
</style>

<div class="exchange-history-container">
    <div class="history-header">
        <h2>Riwayat Penukaran Poin</h2>
    </div>
    
    <!-- Card untuk menampilkan total poin yang sudah ditukarkan dan total nominal -->
    <div class="stats-cards">
        <!-- Card untuk menampilkan total poin ditukarkan -->
        <div class="stats-card">
            <div class="stats-header">
                <div>
                    <h3 class="stats-title">Total Poin Ditukarkan</h3>
                    <p class="stats-subtitle">Jumlah poin yang telah ditukarkan</p>
                </div>
                <div class="stats-value blue">
                    <?= number_format($total_exchanged_points) ?>
                </div>
            </div>
        </div>
        
        <!-- Card untuk menampilkan total nominal -->
        <div class="stats-card">
            <div class="stats-header">
                <div>
                    <h3 class="stats-title">Total Nominal</h3>
                    <p class="stats-subtitle">Total nilai rupiah yang didapatkan</p>
                </div>
                <div class="stats-value green">
                    Rp <?= number_format($total_nominal / 1000) ?>K
                </div>
            </div>
        </div>
    </div>
    
    <div class="action-button">
        <a href="tukar_poin.php" class="exchange-button">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="button-icon" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M1 11.5a.5.5 0 0 0 .5.5h11.793l-3.147 3.146a.5.5 0 0 0 .708.708l4-4a.5.5 0 0 0 0-.708l-4-4a.5.5 0 0 0-.708.708L13.293 11H1.5a.5.5 0 0 0-.5.5zm14-7a.5.5 0 0 1-.5.5H2.707l3.147 3.146a.5.5 0 1 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 4H14.5a.5.5 0 0 1 .5.5z"/>
            </svg>
            Tukar Poin Lagi
        </a>
    </div>
    
    <?php if ($result->num_rows > 0): ?>
    <div class="history-table-container">
        <table class="history-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Poin</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($row = $result->fetch_assoc()): 
                    // Format tanggal
                    $tanggal = date('d F Y H:i', strtotime($row['waktu_penukaran']));
                    
                    // Format nominal
                    $nominal = "Rp " . number_format($row['nominal']);
                    
                    // Set status class
                    $status_class = '';
                    if ($row['status'] == 'diterima') {
                        $status_class = 'status-accepted';
                    } elseif ($row['status'] == 'menunggu') {
                        $status_class = 'status-waiting';
                    } elseif ($row['status'] == 'diproses') {
                        $status_class = 'status-processing';
                    } else {
                        $status_class = 'status-rejected';
                    }
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $tanggal ?></td>
                    <td><?= number_format($row['poin']) ?> Poin</td>
                    <td><?= $nominal ?></td>
                    <td><span class="<?= $status_class ?>"><?= ucfirst($row['status']) ?></span></td>
                    <td>
                        <a href="generate_bukti_tukar.php?id=<?= $row['id'] ?>" class="bukti-button">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="button-icon" viewBox="0 0 16 16">
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
        <p>Belum ada riwayat penukaran poin.</p>
        <a href="tukar_poin.php" class="exchange-button">Tukar Poin Sekarang</a>
    </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const exchangeContainer = document.querySelector('.exchange-history-container');
        
        // Fungsi untuk menyesuaikan margin container berdasarkan status sidebar
        function adjustContainerMargin() {
            if (sidebar && sidebar.classList.contains('closed')) {
                exchangeContainer.style.marginLeft = '20px';
            } else {
                exchangeContainer.style.marginLeft = '260px';
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