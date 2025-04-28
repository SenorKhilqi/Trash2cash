<?php
session_start();
include '../config/db_connection.php';
require_once '../libs/email_service.php';

// Periksa apakah email yang perlu diverifikasi ada dalam session
if (!isset($_SESSION['verification_email'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['verification_email'];
$verification_type = $_SESSION['verification_type'] ?? 'existing_user';
$msg = '';
$alert_type = 'danger'; // default alert type

// Jika form submit untuk verifikasi kode OTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
    $otp_code = trim($_POST['otp_code']);
    
    // Verifikasi OTP
    $verify_stmt = $conn->prepare("SELECT * FROM email_verification WHERE email = ? AND verification_code = ? AND expiry_time > NOW() AND is_verified = 0");
    $verify_stmt->bind_param("ss", $email, $otp_code);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();
    
    if ($verify_result->num_rows > 0) {
        $verification_data = $verify_result->fetch_assoc();
        $user_data = json_decode($verification_data['user_data'], true);
        
        // Update status verifikasi di tabel email_verification
        $update_stmt = $conn->prepare("UPDATE email_verification SET is_verified = 1 WHERE id = ?");
        $update_stmt->bind_param("i", $verification_data['id']);
        $update_stmt->execute();
        $update_stmt->close();
        
        // Update status verifikasi user
        if ($verification_type == 'existing_user') {
            // Untuk user yang sudah ada
            $user_update_stmt = $conn->prepare("UPDATE users SET email_verified = 1, verification_time = NOW() WHERE id = ?");
            $user_update_stmt->bind_param("i", $user_data['id']);
            
            if ($user_update_stmt->execute()) {
                $msg = "Email berhasil diverifikasi! Anda sekarang dapat login.";
                $alert_type = 'success';
                
                // Clear session verification data
                unset($_SESSION['verification_email']);
                unset($_SESSION['verification_type']);
                unset($_SESSION['unverified_email']);
                unset($_SESSION['unverified_username']);
                
                // Redirect ke halaman login setelah 3 detik
                echo '<script>
                    setTimeout(function() {
                        window.location.href = "login.php";
                    }, 3000);
                </script>';
            } else {
                $msg = "Gagal memperbarui status verifikasi: " . $user_update_stmt->error;
            }
            $user_update_stmt->close();
        }
    } else {
        $msg = "Kode verifikasi tidak valid atau sudah kedaluwarsa.";
    }
    $verify_stmt->close();
}

// Jika pengguna ingin mengirim ulang kode OTP
if (isset($_GET['resend_otp'])) {
    // Get user data for this email
    $get_stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $get_stmt->bind_param("s", $email);
    $get_stmt->execute();
    $get_result = $get_stmt->get_result();
    
    if ($get_result->num_rows > 0) {
        $user_data = $get_result->fetch_assoc();
        $username = $user_data['username'];
        
        // Generate new verification code
        $verification_code = sprintf('%06d', mt_rand(0, 999999));
        
        // Set new expiry time (15 minutes from now)
        $expiry_time = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        // Store user data as JSON
        $user_json = json_encode([
            'id' => $user_data['id'],
            'username' => $user_data['username'],
            'email' => $user_data['email']
        ]);
        
        // Delete any existing verification codes for this email
        $delete_stmt = $conn->prepare("DELETE FROM email_verification WHERE email = ?");
        $delete_stmt->bind_param("s", $email);
        $delete_stmt->execute();
        $delete_stmt->close();
        
        // Insert new verification code
        $stmt = $conn->prepare("INSERT INTO email_verification (email, verification_code, expiry_time, user_data) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $email, $verification_code, $expiry_time, $user_json);
        
        if ($stmt->execute()) {
            try {
                // Send verification email again
                $emailService = new EmailService();
                if ($emailService->sendVerificationEmail($email, $username, $verification_code)) {
                    $msg = "Kode verifikasi baru telah dikirim ke email Anda.";
                    $alert_type = 'success';
                } else {
                    $msg = "Gagal mengirim email verifikasi. Silakan coba lagi.";
                }
            } catch (Exception $e) {
                $msg = "Error: " . $e->getMessage();
            }
        } else {
            $msg = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $msg = "Email tidak terdaftar.";
        
        // Redirect kembali ke login
        header("Location: login.php");
        exit;
    }
    $get_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - Trash2Cash</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .verify-container {
            max-width: 500px;
            margin: 80px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .otp-input {
            width: 100%;
            max-width: 200px;
            padding: 15px;
            margin: 0 auto;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 24px;
            letter-spacing: 10px;
            font-weight: bold;
            text-align: center;
        }
        .btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            min-width: 150px;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
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
        h2 {
            color: #333;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }
        .email-display {
            background-color: #e9ecef;
            padding: 8px 15px;
            border-radius: 4px;
            font-weight: bold;
        }
        .resend-link {
            display: inline-block;
            margin-top: 15px;
            color: #4CAF50;
            text-decoration: none;
        }
        .back-link {
            display: block;
            margin-top: 20px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <?php include '../includes/home_navbar.php'; ?>
    
    <div class="verify-container">
        <h2>Verifikasi Email</h2>
        <p class="subtitle">Masukkan kode OTP yang telah dikirim ke email Anda</p>
        
        <p>Email: <span class="email-display"><?= htmlspecialchars($email) ?></span></p>
        
        <?php if (!empty($msg)): ?>
            <div class="alert alert-<?= $alert_type ?>">
                <?= $msg ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="otp_code">Kode Verifikasi</label>
                <input type="text" id="otp_code" name="otp_code" required placeholder="XXXXXX" maxlength="6" class="otp-input" pattern="[0-9]{6}" autocomplete="off">
            </div>
            
            <button type="submit" name="verify_otp" class="btn">Verifikasi</button>
        </form>
        
        <a href="?resend_otp=1" class="resend-link">Kirim ulang kode verifikasi</a>
        <a href="login.php" class="back-link">Kembali ke halaman login</a>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Format OTP input
        const otpInput = document.getElementById('otp_code');
        if (otpInput) {
            otpInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '').substring(0, 6);
            });
            
            // Auto focus
            otpInput.focus();
        }
    });
    </script>
</body>
</html>