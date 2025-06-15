<?php
include_once '../config/db_connection.php';
include_once '../libs/template_engine.php';
session_start();

// Cek apakah user sudah login dan memiliki role user
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: public/login.php");
    exit;
}

// Ambil daftar kategori sampah dari database
$kategori_query = "SELECT id, nama_kategori, poin_per_kg FROM kategori_sampah WHERE status = 'aktif'";
$kategori_result = $conn->query($kategori_query);
$kategoris = [];
while ($row = $kategori_result->fetch_assoc()) {
    $kategoris[] = $row;
}

// Proses jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['username'];
    
    // Dapatkan user_id dari username
    $user_query = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $user_query->bind_param("s", $username);
    $user_query->execute();
    $user_result = $user_query->get_result();
    
    if ($user_row = $user_result->fetch_assoc()) {
        $user_id = $user_row['id'];
        $kategori_id = $_POST['kategori_id']; // Menggunakan kategori_id dari form
        $jumlah_kg = floatval($_POST['jumlah']);
        $tanggal = $_POST['tanggal'];
        $drop_point = $_POST['drop_point'];
        $catatan = $_POST['catatan'];
        
        // Hitung poin berdasarkan kategori dan jumlah kg
        $poin_query = $conn->prepare("SELECT poin_per_kg FROM kategori_sampah WHERE id = ?");
        $poin_query->bind_param("i", $kategori_id);
        $poin_query->execute();
        $poin_result = $poin_query->get_result();
        $poin_row = $poin_result->fetch_assoc();
        $poin_per_kg = $poin_row ? $poin_row['poin_per_kg'] : 0;
        $total_point = $poin_per_kg * $jumlah_kg;

        // Validasi file upload
        $foto = '';
        if ($_FILES['foto']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
            $max_size = 2 * 1024 * 1024; // 2MB

            if (in_array($_FILES['foto']['type'], $allowed_types) && $_FILES['foto']['size'] <= $max_size) {
                $upload_dir = '../uploads/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $foto = time() . '_' . basename($_FILES['foto']['name']);
                move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $foto);
            } else {
                echo "<script>alert('Foto tidak valid atau melebihi 2MB!');</script>";
            }
        }

        // Simpan ke database - sesuai dengan struktur tabel
        $stmt = $conn->prepare("INSERT INTO laporan_sampah (user_id, kategori_id, jumlah_kg, tanggal_pengumpulan, drop_point, foto, catatan, total_point) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisdsssi", $user_id, $kategori_id, $jumlah_kg, $tanggal, $drop_point, $foto, $catatan, $total_point);
        
        if ($stmt->execute()) {
            echo "<script>alert('Laporan berhasil dikirim!'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('User tidak ditemukan!');</script>";
    }
    $user_query->close();
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
      .report-container {
        transition: margin-left 0.3s ease;
        padding: 20px;
        margin: 20px;
        width: calc(100% - 40px);
        max-width: 800px;
    }
    
    .form-card {
        background-color: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .form-header {
        margin-bottom: 30px;
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
    }
    
    .form-header h2 {
        color: #333;
        margin: 0;
        font-weight: 600;
        border-left: 4px solid #D2B48C;
        padding-left: 15px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #555;
    }
    
    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        transition: border-color 0.3s;
    }
    
    .form-control:focus {
        border-color: #D2B48C;
        outline: none;
    }
    
    .file-upload {
        background-color: #f9f9f9;
        border: 1px dashed #ddd;
        padding: 15px;
        border-radius: 5px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .file-upload:hover {
        border-color: #D2B48C;
        background-color: #f8f5e6;
    }
    
    .file-upload input[type="file"] {
        display: none;
    }
    
    .file-name {
        margin-top: 8px;
        font-size: 14px;
        color: #666;
    }
    
    .upload-icon {
        font-size: 24px;
        color: #D2B48C;
        margin-bottom: 8px;
    }
    
    .submit-btn {
        background-color: #FFFDD0;
        color: #6b5a3a;
        border: 1px solid #D2B48C;
        padding: 14px 20px;
        font-size: 16px;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
        font-weight: 600;
        transition: background-color 0.3s;
    }
    
    .submit-btn:hover {
        background-color: #f5f3d7;
    }
    
    .point-info {
        background-color: #f8f5e6;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        border-left: 4px solid #D2B48C;
    }
    
    .point-info p {
        margin: 0;
        color: #333;
    }
    
    .point-info strong {
        color: #D2B48C;
    }
    
    /* Responsive */
    @media (max-width: 767px) {
        .report-container {
            margin-left: 20px;
            max-width: calc(100% - 40px);
        }
    }
    
    /* Adjust container margin based on sidebar state */
    #sidebar.closed ~ .report-container {
        margin-left: 20px;
    }
</style>

<div class="report-container">
    <div class="form-card">
        <div class="form-header">
            <h2>Lapor Pengumpulan Sampah</h2>
        </div>
        
        <div class="point-info">
            <p>Kumpulkan sampah daur ulang dan dapatkan poin yang bisa ditukarkan dengan uang! Setiap kategori sampah memiliki nilai poin yang berbeda per kg.</p>
        </div>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="kategori">Kategori Sampah:</label>
                <select name="kategori_id" id="kategori" class="form-control" required>
                    <option value="">Pilih Kategori</option>
                    <?php foreach($kategoris as $kategori): ?>
                    <option value="<?= $kategori['id'] ?>" data-point="<?= $kategori['poin_per_kg'] ?>">
                        <?= ucfirst($kategori['nama_kategori']) ?> (<?= $kategori['poin_per_kg'] ?> poin/kg)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah (kg):</label>
                <input type="number" name="jumlah" id="jumlah" step="0.1" min="0.1" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="estimasi">Estimasi Poin:</label>
                <input type="text" id="estimasi" class="form-control" readonly value="0 poin">
            </div>

            <div class="form-group">
                <label for="tanggal">Tanggal Pengumpulan:</label>
                <input type="date" name="tanggal" id="tanggal" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="drop_point">Lokasi Drop Point:</label>
                <select name="drop_point" id="drop_point" class="form-control" required>
                    <option value="">Pilih Drop Point</option>
                    <option value="Drop Point A">Drop Point A</option>
                    <option value="Drop Point B">Drop Point B</option>
                    <option value="Drop Point C">Drop Point C</option>
                </select>
            </div>

            <div class="form-group">
                <label>Foto Sampah (Max 2MB):</label>
                <div class="file-upload" id="file-upload-container">
                    <div class="upload-icon">📷</div>
                    <p>Klik untuk unggah gambar</p>
                    <p class="file-name">Tidak ada file yang dipilih</p>
                    <input type="file" name="foto" id="foto" accept="image/*">
                </div>
            </div>

            <div class="form-group">
                <label for="catatan">Catatan:</label>
                <textarea name="catatan" id="catatan" rows="4" class="form-control"></textarea>
            </div>

            <button type="submit" class="submit-btn">Kirim Laporan</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Perbarui estimasi poin saat kategori atau jumlah berubah
        const kategoriSelect = document.getElementById('kategori');
        const jumlahInput = document.getElementById('jumlah');
        const estimasiInput = document.getElementById('estimasi');
        
        function updateEstimasi() {
            const selectedOption = kategoriSelect.options[kategoriSelect.selectedIndex];
            const pointPerKg = selectedOption ? parseFloat(selectedOption.dataset.point) || 0 : 0;
            const jumlah = parseFloat(jumlahInput.value) || 0;
            const totalPoint = pointPerKg * jumlah;
            estimasiInput.value = `${Math.round(totalPoint)} poin`;
        }
        
        kategoriSelect.addEventListener('change', updateEstimasi);
        jumlahInput.addEventListener('input', updateEstimasi);
        
        // Tampilkan nama file yang dipilih
        const fileUpload = document.getElementById('foto');
        const fileContainer = document.getElementById('file-upload-container');
        const fileName = document.querySelector('.file-name');
        
        fileContainer.addEventListener('click', function() {
            fileUpload.click();
        });
        
        fileUpload.addEventListener('change', function() {
            if (this.files.length > 0) {
                fileName.textContent = this.files[0].name;
                fileContainer.style.borderColor = '#D2B48C';
            } else {
                fileName.textContent = 'Tidak ada file yang dipilih';
                fileContainer.style.borderColor = '#ddd';
            }
        });
          // Sesuaikan container margin berdasarkan status sidebar
        const sidebar = document.getElementById('sidebar');
        const reportContainer = document.querySelector('.report-container');
        
        function adjustContainerMargin() {
            if (sidebar && sidebar.classList.contains('closed')) {
                reportContainer.style.marginLeft = '20px';
            } else {
                reportContainer.style.marginLeft = '0'; // No extra margin since we're inside #content
            }
        }
        
        // Panggil sekali pada load
        adjustContainerMargin();
        
        // Tambahkan observer untuk perubahan pada sidebar
        if (sidebar) {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        adjustContainerMargin();
                    }
                });
            });
            
            observer.observe(sidebar, { attributes: true });}
        
        // Set tanggal hari ini sebagai default hanya jika belum dipilih
        const tanggalInput = document.getElementById('tanggal');
        if (!tanggalInput.value) {
            const today = new Date().toISOString().split('T')[0];
            tanggalInput.value = today;
            
            // Atur batas maksimal tanggal adalah hari ini (tidak bisa memilih tanggal masa depan)
            tanggalInput.max = today;
        }
    });
</script>

</div> <!-- Close content div -->
