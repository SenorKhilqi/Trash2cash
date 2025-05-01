<?php
session_start();
include '../config/db_connection.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Regular login processing
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header("Location: ../admin/dashboard.php");
                } else {
                    header("Location: ../user/dashboard.php");
                }
                exit;
            } else {
                $msg = "Password salah!";
            }
        } else {
            $msg = "Username tidak ditemukan!";
        }
        $stmt->close();
    }
    // QR code login processing
    elseif (isset($_POST['qr_data'])) {
        $qr_data = json_decode($_POST['qr_data'], true);

        if (json_last_error() === JSON_ERROR_NONE && isset($qr_data['username'])) {
            $username = trim($qr_data['username']);

            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();

            $result = $stmt->get_result();
            if ($user = $result->fetch_assoc()) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                $stmt->close();
                // Return success response for AJAX
                echo json_encode(['success' => true, 'role' => $user['role']]);
                exit;
            } else {
                $stmt->close();
                echo json_encode(['success' => false, 'message' => 'Username tidak ditemukan!']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'QR code tidak valid!']);
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Trash2Cash</title>
    <!-- <link rel="stylesheet" href="../assets/css/style.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .login-container {
            max-width: 400px;
            margin: 80px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        .btn:hover {
            background-color: #45a049;
        }

        .message {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
        }

        .register-link a {
            color: #4CAF50;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        /* QR Scanner Styles */
        .login-options {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .login-option {
            padding: 10px 15px;
            margin: 0 5px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .login-option.active {
            background-color: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }

        .login-option i {
            margin-right: 5px;
        }

        /* QR Scanner Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            position: relative;
        }

        .close-modal {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #aaa;
        }

        .close-modal:hover {
            color: #333;
        }

        #scanner-container {
            width: 100%;
            height: 300px;
            overflow: hidden;
            margin-bottom: 20px;
            position: relative;
        }

        #preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .scan-status {
            text-align: center;
            margin-top: 10px;
            font-weight: bold;
        }

        .scan-error {
            color: #dc3545;
        }

        .scan-success {
            color: #28a745;
        }

        .camera-select {
            margin-bottom: 15px;
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <?php include '../includes/home_navbar.php'; ?>

    <div class="login-container">
        <h2>Login Akun</h2>

        <?php if (!empty($msg)): ?>
            <div class="message">
                <?= $msg ?>
            </div>
        <?php endif; ?>

        <div class="login-options">
            <div class="login-option active" data-target="password-login">
                <i class="fas fa-key"></i> Password
            </div>
            <div class="login-option" data-target="qr-login">
                <i class="fas fa-qrcode"></i> QR Code
            </div>
        </div>

        <form method="POST" action="" id="password-login-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required placeholder="Masukkan username"
                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Masukkan password">
            </div>

            <button type="submit" class="btn">Login</button>
        </form>

        <div id="qr-login-form" style="display: none; text-align: center;">
            <p>Scan QR code dari kartu anggota Anda untuk login cepat.</p>
            <button type="button" class="btn" id="open-scanner">
                <i class="fas fa-camera"></i> Buka Scanner QR
            </button>
        </div>

        <div class="register-link">
            <p>Belum punya akun? <a href="register.php">Daftar Sekarang</a></p>
        </div>
    </div>

    <!-- QR Scanner Modal -->
    <div id="scanner-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3>Scan QR Code</h3>
            <select class="camera-select" id="camera-select">
                <option value="">Pilih Kamera</option>
            </select>
            <div id="scanner-container">
                <video id="preview"></video>
            </div>
            <div id="scan-status" class="scan-status"></div>
        </div>
    </div>

    <!-- Include Instascan Library -->
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Login option tabs
            const loginOptions = document.querySelectorAll('.login-option');
            const passwordForm = document.getElementById('password-login-form');
            const qrForm = document.getElementById('qr-login-form');

            loginOptions.forEach(option => {
                option.addEventListener('click', function () {
                    // Remove active class from all options
                    loginOptions.forEach(opt => opt.classList.remove('active'));
                    // Add active class to clicked option
                    this.classList.add('active');

                    // Show the selected form
                    const target = this.getAttribute('data-target');
                    if (target === 'password-login') {
                        passwordForm.style.display = 'block';
                        qrForm.style.display = 'none';
                    } else {
                        passwordForm.style.display = 'none';
                        qrForm.style.display = 'block';
                    }
                });
            });

            // QR Scanner Modal
            const modal = document.getElementById('scanner-modal');
            const openScannerBtn = document.getElementById('open-scanner');
            const closeModal = document.querySelector('.close-modal');
            const scanStatus = document.getElementById('scan-status');
            const cameraSelect = document.getElementById('camera-select');

            let scanner = null;
            let currentCamera = null;

            // Open scanner modal
            openScannerBtn.addEventListener('click', function () {
                modal.style.display = 'block';
                initScanner();
            });

            // Close scanner modal
            closeModal.addEventListener('click', function () {
                modal.style.display = 'none';
                stopScanner();
            });

            window.addEventListener('click', function (event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                    stopScanner();
                }
            });

            // Initialize scanner
            function initScanner() {
                scanStatus.innerHTML = 'Initializing camera...';
                scanStatus.className = 'scan-status';

                // Create a new scanner
                scanner = new Instascan.Scanner({
                    video: document.getElementById('preview'),
                    mirror: false
                });

                // Handle successful scans
                scanner.addListener('scan', function (content) {
                    handleQRCode(content);
                });

                // Get available cameras
                Instascan.Camera.getCameras().then(function (cameras) {
                    if (cameras.length > 0) {
                        // Clear camera select options
                        cameraSelect.innerHTML = '';

                        // Add camera options
                        cameras.forEach((camera, index) => {
                            const option = document.createElement('option');
                            option.value = index;
                            option.text = camera.name || `Camera ${index + 1}`;
                            cameraSelect.appendChild(option);
                        });

                        // Try to find a back camera
                        let selectedCameraIndex = 0;
                        for (let i = 0; i < cameras.length; i++) {
                            if (cameras[i].name && cameras[i].name.toLowerCase().includes('back')) {
                                selectedCameraIndex = i;
                                break;
                            }
                        }

                        // Select the camera
                        cameraSelect.value = selectedCameraIndex;
                        currentCamera = cameras[selectedCameraIndex];

                        // Start scanning
                        scanner.start(currentCamera);
                        scanStatus.innerHTML = 'Scanning QR Code...';
                    } else {
                        scanStatus.innerHTML = 'No cameras found!';
                        scanStatus.className = 'scan-status scan-error';
                    }
                }).catch(function (e) {
                    scanStatus.innerHTML = 'Error accessing camera: ' + e;
                    scanStatus.className = 'scan-status scan-error';
                    console.error(e);
                });

                // Handle camera selection change
                cameraSelect.addEventListener('change', function () {
                    if (scanner) {
                        Instascan.Camera.getCameras().then(function (cameras) {
                            const cameraIndex = parseInt(cameraSelect.value);
                            if (cameras[cameraIndex]) {
                                currentCamera = cameras[cameraIndex];
                                scanner.start(currentCamera);
                            }
                        });
                    }
                });
            }

            // Stop scanner
            function stopScanner() {
                if (scanner) {
                    scanner.stop();
                }
            }

            // Handle QR code data
            function handleQRCode(content) {
                try {
                    // Try to parse the QR code content as JSON
                    const qrData = JSON.parse(content);

                    // Check if it has the expected format
                    if (qrData.username && qrData.user_id && qrData.card_id) {
                        scanStatus.innerHTML = 'QR Code valid! Logging in...';
                        scanStatus.className = 'scan-status scan-success';

                        // Send QR data to server for login
                        loginWithQR(content);
                    } else {
                        scanStatus.innerHTML = 'QR Code tidak valid!';
                        scanStatus.className = 'scan-status scan-error';
                    }
                } catch (e) {
                    scanStatus.innerHTML = 'QR Code tidak valid!';
                    scanStatus.className = 'scan-status scan-error';
                    console.error('Error parsing QR code:', e);
                }
            }

            // Login with QR code
            function loginWithQR(qrData) {
                // Create form data to send
                const formData = new FormData();
                formData.append('qr_data', qrData);

                // Send AJAX request
                fetch('login.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            scanStatus.innerHTML = 'Login berhasil! Mengalihkan...';
                            scanStatus.className = 'scan-status scan-success';

                            // Redirect based on user role
                            setTimeout(() => {
                                if (data.role === 'admin') {
                                    window.location.href = '../admin/dashboard.php';
                                } else {
                                    window.location.href = '../user/dashboard.php';
                                }
                            }, 1000);
                        } else {
                            scanStatus.innerHTML = data.message || 'Login gagal!';
                            scanStatus.className = 'scan-status scan-error';
                        }
                    })
                    .catch(error => {
                        scanStatus.innerHTML = 'Terjadi kesalahan saat login!';
                        scanStatus.className = 'scan-status scan-error';
                        console.error('Error:', error);
                    });
            }
        });
    </script>
</body>

</html>