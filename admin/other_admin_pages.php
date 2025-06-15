<?php
session_start();
require_once '../config/db_connection.php';
include_once '../libs/template_engine.php';

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: public/login.php");
    exit();
}

// Hapus user jika ada request delete
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // Pastikan tidak menghapus admin sendiri
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && $user['role'] !== 'admin') {
        // Hapus user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Pengguna berhasil dihapus!";
        } else {
            $_SESSION['error_message'] = "Gagal menghapus pengguna!";
        }
    } else {
        $_SESSION['error_message'] = "Tidak dapat menghapus akun admin!";
    }
    
    header("Location: other_admin_pages.php");
    exit();
}

// Mendapatkan statistik pengguna
// 1. Total pengguna (kecuali admin)
$query_total_users = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
$result_total = $conn->query($query_total_users);
$total_users = $result_total->fetch_assoc()['total'];

// 2. Pengguna aktif (yang pernah melaporkan sampah)
$query_active_users = "SELECT COUNT(DISTINCT user_id) as active FROM laporan_sampah";
$result_active = $conn->query($query_active_users);
$active_users = $result_active->fetch_assoc()['active'];

// 3. Jumlah laporan menunggu verifikasi
$query_pending = "SELECT COUNT(*) as pending FROM laporan_sampah WHERE status_verifikasi = 'menunggu'";
$result_pending = $conn->query($query_pending);
$pending_reports = $result_pending->fetch_assoc()['pending'];

// 4. Rata-rata kontribusi per pengguna (dalam kg)
$query_avg_contribution = "SELECT AVG(total_per_user) as avg_contribution 
                          FROM (
                              SELECT user_id, SUM(jumlah_kg) as total_per_user 
                              FROM laporan_sampah 
                              WHERE status_verifikasi = 'diterima' 
                              GROUP BY user_id
                          ) as user_contributions";
$result_avg = $conn->query($query_avg_contribution);
$avg_contribution = $result_avg->fetch_assoc()['avg_contribution'];
$avg_contribution = $avg_contribution ? number_format($avg_contribution, 2) : '0.00';

// Mendapatkan data semua pengguna dengan total kontribusi dan total poin
$query_users = "SELECT u.id, u.username, u.created_at,
               (SELECT SUM(jumlah_kg) FROM laporan_sampah WHERE user_id = u.id AND status_verifikasi = 'diterima') as total_kontribusi,
               (SELECT SUM(total_point) FROM laporan_sampah WHERE user_id = u.id AND status_verifikasi = 'diterima') as total_point,
               CASE 
                   WHEN EXISTS (SELECT 1 FROM laporan_sampah WHERE user_id = u.id) THEN 'Aktif' 
                   ELSE 'Belum Aktif' 
               END as status
               FROM users u
               WHERE u.role = 'user'
               ORDER BY total_kontribusi DESC";

$result_users = $conn->query($query_users);
$users_data = [];
while ($row = $result_users->fetch_assoc()) {
    // Handle NULL values
    $row['total_kontribusi'] = $row['total_kontribusi'] ?? 0;
    $row['total_point'] = $row['total_point'] ?? 0;
    $users_data[] = $row;
}

// Template data
$data = [
    'title' => 'Manajemen Pengguna',
    'total_users' => $total_users,
    'active_users' => $active_users,
    'pending_reports' => $pending_reports,
    'avg_contribution' => $avg_contribution,
    'users_data' => $users_data
];

// Tampilkan pesan sukses/error jika ada
if (isset($_SESSION['success_message'])) {
    $data['success_message'] = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $data['error_message'] = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Render halaman
$content = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$data['title']} - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    body {
        font-family: Times New Roman;
        background-color: #f8f9fa;
    } 
    </style>
</head>
<body>

<!-- Navbar -->
HTML;

// Include navbar - replacing direct include with template engine
$role = $_SESSION['role'] ?? 'admin'; // default to admin if not set
renderTemplate($role, 'navbar');

$content .= <<<HTML
<div class="admin-content">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fs-4">Manajemen Pengguna</h2>
            <p class="text-muted small">Statistik dan data pengguna aplikasi Trash2Cash</p>
        </div>
    </div>

    <!-- Alert Messages -->
HTML;

if (isset($data['success_message'])) {
    $content .= <<<HTML
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {$data['success_message']}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
HTML;
}

if (isset($data['error_message'])) {
    $content .= <<<HTML
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {$data['error_message']}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
HTML;
}

$content .= <<<HTML
    <!-- Statistik Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card card-stats stats-card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <div class="col-9 text-end">
                            <h3>{$data['total_users']}</h3>
                            <p class="mb-0">Total Pengguna</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card card-stats stats-card bg-success text-white h-100">
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <i class="fas fa-user-check fa-2x"></i>
                        </div>
                        <div class="col-9 text-end">
                            <h3>{$data['active_users']}</h3>
                            <p class="mb-0">Pengguna Aktif</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card card-stats stats-card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <div class="col-9 text-end">
                            <h3>{$data['pending_reports']}</h3>
                            <p class="mb-0">Menunggu Verifikasi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card card-stats stats-card bg-info text-white h-100">
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <i class="fas fa-balance-scale fa-2x"></i>
                        </div>
                        <div class="col-9 text-end">
                            <h3>{$data['avg_contribution']} kg</h3>
                            <p class="mb-0">Rata-rata Kontribusi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data Pengguna -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h5 class="mb-0 fs-5">Daftar Pengguna</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Tanggal Bergabung</th>
                                    <th>Total Kontribusi (kg)</th>
                                    <th>Total Poin</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
HTML;

// Tampilkan data pengguna
foreach ($data['users_data'] as $user) {
    $status_class = $user['status'] == 'Aktif' ? 'success' : 'secondary';
    $tanggal_bergabung = date('d-m-Y', strtotime($user['created_at']));
    
    $content .= <<<HTML
                                <tr>
                                    <td>{$user['id']}</td>
                                    <td>{$user['username']}</td>
                                    <td>{$tanggal_bergabung}</td>
                                    <td>{$user['total_kontribusi']}</td>
                                    <td>{$user['total_point']}</td>
                                    <td><span class="badge bg-{$status_class}">{$user['status']}</span></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{$user['id']}">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                        
                                        <!-- Modal Konfirmasi Hapus -->
                                        <div class="modal fade" id="deleteModal{$user['id']}" tabindex="-1" aria-labelledby="deleteModalLabel{$user['id']}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel{$user['id']}">Konfirmasi Hapus</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Apakah Anda yakin ingin menghapus pengguna <strong>{$user['username']}</strong>?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <a href="other_admin_pages.php?delete_id={$user['id']}" class="btn btn-danger">Hapus</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
HTML;
}

$content .= <<<HTML
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
HTML;

// Include footer - replacing direct include with template engine
renderTemplate($role, 'footer');

$content .= <<<HTML
<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto close alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Handle sidebar toggle for responsive content
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggle-btn');
        const adminContent = document.querySelector('.admin-content');
        
        function updateContentLayout() {
            const isSidebarClosed = sidebar ? sidebar.classList.contains('closed') : false;
            document.body.classList.toggle('sidebar-closed', isSidebarClosed);
            
            // Update margins based on sidebar state
            if (adminContent) {
                if (isSidebarClosed) {
                    adminContent.style.marginLeft = '20px';
                    adminContent.style.width = 'calc(100% - 30px)';
                } else {
                    adminContent.style.marginLeft = '260px';
                    adminContent.style.width = 'calc(100% - 270px)';
                }
            }
        }
        
        // Initial check
        updateContentLayout();
        
        // Listen for sidebar toggle
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                setTimeout(updateContentLayout, 50);
            });
        }
        
        // Also check on resize
        window.addEventListener('resize', updateContentLayout);
    });
</script>
</body>
</html>
HTML;

echo $content;
?>