<?php
session_start();
include '../config/db_connection.php';
include_once '../libs/template_engine.php';

// Check if user is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../public/login.php');
    exit;
}

$username = $_SESSION['username'];
$msg = '';
$success_msg = '';

// Get current user data
$stmt = $conn->prepare("SELECT username, email, no_telepon, alamat FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $no_telepon = trim($_POST['no_telepon']);
    $alamat = trim($_POST['alamat']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Check if current password is required (for password change)
    $change_password = !empty($new_password);
    
    if ($change_password) {
        // Verify current password
        $check_pwd = $conn->prepare("SELECT password FROM users WHERE username = ?");
        $check_pwd->bind_param("s", $username);
        $check_pwd->execute();
        $pwd_result = $check_pwd->get_result();
        $user_data = $pwd_result->fetch_assoc();
        $check_pwd->close();
        
        if (empty($current_password) || !password_verify($current_password, $user_data['password'])) {
            $msg = "Password saat ini salah!";
        } else if (empty($new_password) || empty($confirm_password)) {
            $msg = "Password baru dan konfirmasi password harus diisi!";
        } else if ($new_password !== $confirm_password) {
            $msg = "Password baru dan konfirmasi password tidak sama!";
        }
    }
    
    // If no errors and password verification passed (if needed)
    if (empty($msg)) {
        if ($change_password) {
            // Update profile with new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE users SET email = ?, no_telepon = ?, alamat = ?, password = ? WHERE username = ?");
            $update_stmt->bind_param("sssss", $email, $no_telepon, $alamat, $hashed_password, $username);
        } else {
            // Update profile without changing password
            $update_stmt = $conn->prepare("UPDATE users SET email = ?, no_telepon = ?, alamat = ? WHERE username = ?");
            $update_stmt->bind_param("ssss", $email, $no_telepon, $alamat, $username);
        }
        
        if ($update_stmt->execute()) {
            $success_msg = "Profil berhasil diperbarui!";
            
            // Refresh user data after update
            $stmt = $conn->prepare("SELECT username, email, no_telepon, alamat FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();
        } else {
            $msg = "Error: " . $update_stmt->error;
        }
        $update_stmt->close();
    }
}

$role = $_SESSION['role'] ?? 'admin'; // default ke admin jika tidak ada
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin - Trash2Cash</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Admin content with sidebar integration */
        .admin-content {
            transition: margin-left 0.3s ease, width 0.3s ease;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        @media (min-width: 768px) {
            .admin-content {
                width: calc(100% - 270px);
                margin-left: 260px;
            }
            
            body.sidebar-closed .admin-content {
                width: calc(100% - 30px);
                margin-left: 20px;
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
            border-left: 4px solid #2ecc71;
            padding-left: 15px;
        }
        
        /* Alert messages */
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            position: relative;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-left: 5px solid #dc3545;
            color: #721c24;
        }
        
        .alert-success {
            background-color: #d4edda;
            border-left: 5px solid #28a745;
            color: #155724;
        }
        
        /* Profile card styling */
        .profile-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .profile-sidebar {
            flex: 1;
            min-width: 250px;
            max-width: 300px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: #2C3E50;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            margin: 0 auto 15px;
        }
        
        .profile-username {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #2C3E50;
        }
        
        .profile-role {
            display: inline-block;
            background-color: #2C3E50;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .profile-info {
            text-align: left;
            margin-top: 20px;
        }
        
        .profile-info-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            color: #6c757d;
        }
        
        .profile-info-item i {
            width: 30px;
            color: #2C3E50;
        }
        
        .profile-form-container {
            flex: 2;
            min-width: 300px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        
        .form-section {
            margin-bottom: 30px;
        }
        
        .form-section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #2C3E50;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 15px;
        }
        
        .form-group {
            flex: 1;
            min-width: 250px;
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #495057;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .form-control:focus {
            border-color: #2ecc71;
            box-shadow: 0 0 0 0.2rem rgba(46, 204, 113, 0.25);
            outline: none;
        }
        
        .form-control:read-only {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }
        
        .form-text {
            display: block;
            margin-top: 5px;
            font-size: 14px;
        }
        
        .text-muted {
            color: #6c757d;
        }
        
        .form-divider {
            height: 1px;
            background-color: #e9ecef;
            margin: 20px 0;
        }
        
        .btn {
            display: inline-block;
            font-weight: 500;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            padding: 10px 20px;
            border-radius: 5px;
            transition: all 0.2s ease;
            border: none;
        }
        
        .btn-primary {
            background-color: #2C3E50;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #1a252f;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        @media (max-width: 768px) {
            .profile-sidebar {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php renderTemplate($role, 'navbar'); ?>
    
    <div class="admin-content">
        <h1 class="page-title">Profil Admin</h1>
        
        <?php if (!empty($msg)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?= $msg ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= $success_msg ?>
            </div>
        <?php endif; ?>
        
        <div class="profile-container">
            <div class="profile-sidebar">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <h2 class="profile-username"><?= htmlspecialchars($user['username']) ?></h2>
                <span class="profile-role">Administrator</span>
                
                <div class="profile-info">
                    <div class="profile-info-item">
                        <i class="fas fa-envelope"></i>
                        <span><?= htmlspecialchars($user['email']) ?></span>
                    </div>
                    <div class="profile-info-item">
                        <i class="fas fa-phone"></i>
                        <span><?= htmlspecialchars($user['no_telepon']) ?></span>
                    </div>
                    <div class="profile-info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?= htmlspecialchars($user['alamat']) ?></span>
                    </div>
                </div>
            </div>
            
            <div class="profile-form-container">
                <form method="POST" action="">
                    <div class="form-section">
                        <h3 class="form-section-title">Informasi Dasar</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" id="username" value="<?= htmlspecialchars($user['username']) ?>" readonly class="form-control">
                                <small class="form-text text-muted">Username tidak dapat diubah.</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required class="form-control">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="no_telepon">Nomor Telepon:</label>
                                <input type="text" id="no_telepon" name="no_telepon" value="<?= htmlspecialchars($user['no_telepon']) ?>" required class="form-control">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="alamat">Alamat:</label>
                            <textarea id="alamat" name="alamat" required class="form-control" rows="3"><?= htmlspecialchars($user['alamat']) ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-divider"></div>
                    
                    <div class="form-section">
                        <h3 class="form-section-title">Ubah Password</h3>
                        <p class="text-muted">Kosongkan jika tidak ingin mengubah password</p>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="current_password">Password Saat Ini:</label>
                                <input type="password" id="current_password" name="current_password" class="form-control">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="new_password">Password Baru:</label>
                                <input type="password" id="new_password" name="new_password" class="form-control">
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm_password">Konfirmasi Password:</label>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group" style="text-align: right; margin-top: 20px;">
                        <button type="reset" class="btn btn-secondary" style="margin-right: 10px;">Reset</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle sidebar toggle for responsive content layout
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggle-btn');
            const adminContent = document.querySelector('.admin-content');
            
            function updateContentLayout() {
                const isSidebarClosed = sidebar ? sidebar.classList.contains('closed') : false;
                document.body.classList.toggle('sidebar-closed', isSidebarClosed);
                
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
            
            // Initial setup
            updateContentLayout();
            
            // Listen for toggle button clicks
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    setTimeout(updateContentLayout, 50);
                });
            }
            
            // Update on window resize too
            window.addEventListener('resize', updateContentLayout);
            
            // Auto hide alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    if (alert) {
                        alert.style.display = 'none';
                    }
                });
            }, 5000);
        });
    </script>
    
    <?php renderTemplate($role, 'footer'); ?>
</body>
</html>