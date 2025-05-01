<?php
session_start();
require_once '../config/db_connection.php';
include_once '../libs/template_engine.php';

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: public/login.php");
    exit();
}

// Set halaman aktif (default: verifikasi)
$active_tab = $_GET['tab'] ?? 'verifikasi';

// Ambil daftar kategori sampah dari database
$kategori_query = "SELECT * FROM kategori_sampah ORDER BY nama_kategori";
$kategori_result = $conn->query($kategori_query);
$kategori_data = [];
while ($row = $kategori_result->fetch_assoc()) {
    $kategori_data[] = $row;
}

// Jika ada request untuk menambah/update/delete kategori
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    // Tambah kategori baru
    if ($action === 'add') {
        $nama_kategori = trim($_POST['nama_kategori']);
        $deskripsi = trim($_POST['deskripsi']);
        $poin_per_kg = (int)$_POST['poin_per_kg'];
        $status = $_POST['status'];
        
        // Validasi input
        if (!empty($nama_kategori) && $poin_per_kg >= 0) {
            $stmt = $conn->prepare("INSERT INTO kategori_sampah (nama_kategori, deskripsi, poin_per_kg, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssis", $nama_kategori, $deskripsi, $poin_per_kg, $status);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Kategori sampah berhasil ditambahkan!";
            } else {
                $_SESSION['error_message'] = "Gagal menambahkan kategori sampah: " . $conn->error;
            }
            $stmt->close();
        } else {
            $_SESSION['error_message'] = "Nama kategori harus diisi dan poin harus lebih dari atau sama dengan 0!";
        }
    }
    
    // Update kategori
    else if ($action === 'update' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $nama_kategori = trim($_POST['nama_kategori']);
        $deskripsi = trim($_POST['deskripsi']);
        $poin_per_kg = (int)$_POST['poin_per_kg'];
        $status = $_POST['status'];
        
        // Validasi input
        if (!empty($nama_kategori) && $poin_per_kg >= 0) {
            $stmt = $conn->prepare("UPDATE kategori_sampah SET nama_kategori = ?, deskripsi = ?, poin_per_kg = ?, status = ? WHERE id = ?");
            $stmt->bind_param("ssisi", $nama_kategori, $deskripsi, $poin_per_kg, $status, $id);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Kategori sampah berhasil diperbarui!";
            } else {
                $_SESSION['error_message'] = "Gagal memperbarui kategori sampah: " . $conn->error;
            }
            $stmt->close();
        } else {
            $_SESSION['error_message'] = "Nama kategori harus diisi dan poin harus lebih dari atau sama dengan 0!";
        }
    }
    
    // Delete kategori
    else if ($action === 'delete' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        
        // Periksa apakah kategori masih digunakan dalam laporan
        $check_query = "SELECT COUNT(*) as used_count FROM laporan_sampah WHERE kategori_id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $result = $check_stmt->get_result()->fetch_assoc();
        $check_stmt->close();
        
        if ($result['used_count'] > 0) {
            $_SESSION['error_message'] = "Kategori ini tidak dapat dihapus karena masih digunakan dalam laporan sampah. Gunakan status 'nonaktif' sebagai gantinya.";
        } else {
            $stmt = $conn->prepare("DELETE FROM kategori_sampah WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Kategori sampah berhasil dihapus!";
            } else {
                $_SESSION['error_message'] = "Gagal menghapus kategori sampah: " . $conn->error;
            }
            $stmt->close();
        }
    }
    
    // Redirect setelah proses selesai
    header("Location: verifikasi_laporan.php?tab=kategori");
    exit();
}

// Untuk Tab Verifikasi - Inisialisasi filter pencarian
if ($active_tab === 'verifikasi') {
    $where_conditions = [];
    $params = [];
    $param_types = "";

    // Filter berdasarkan nama akun/username
    if (isset($_GET['username']) && !empty($_GET['username'])) {
        $where_conditions[] = "users.username LIKE ?";
        $params[] = "%" . $_GET['username'] . "%";
        $param_types .= "s";
    }

    // Filter berdasarkan tanggal
    if (isset($_GET['tanggal_mulai']) && !empty($_GET['tanggal_mulai'])) {
        $where_conditions[] = "tanggal_pengumpulan >= ?";
        $params[] = $_GET['tanggal_mulai'];
        $param_types .= "s";
    }

    if (isset($_GET['tanggal_akhir']) && !empty($_GET['tanggal_akhir'])) {
        $where_conditions[] = "tanggal_pengumpulan <= ?";
        $params[] = $_GET['tanggal_akhir'];
        $param_types .= "s";
    }

    // Filter berdasarkan jenis sampah/kategori
    if (isset($_GET['kategori_id']) && !empty($_GET['kategori_id'])) {
        $where_conditions[] = "laporan_sampah.kategori_id = ?";
        $params[] = $_GET['kategori_id'];
        $param_types .= "i";
    }

    // Filter berdasarkan status verifikasi
    if (isset($_GET['status']) && !empty($_GET['status'])) {
        $where_conditions[] = "status_verifikasi = ?";
        $params[] = $_GET['status'];
        $param_types .= "s";
    }

    // Membuat WHERE clause jika ada filter yang diterapkan
    $where_clause = "";
    if (count($where_conditions) > 0) {
        $where_clause = " WHERE " . implode(" AND ", $where_conditions);
    }

    // Query dengan filter
    $query = "SELECT laporan_sampah.*, users.username, kategori_sampah.nama_kategori 
              FROM laporan_sampah 
              JOIN users ON laporan_sampah.user_id = users.id 
              JOIN kategori_sampah ON laporan_sampah.kategori_id = kategori_sampah.id
              $where_clause
              ORDER BY tanggal_pengumpulan DESC";

    // Siapkan dan eksekusi query dengan parameter
    $stmt = $conn->prepare($query);
    if (count($params) > 0) {
        $stmt->bind_param($param_types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
}

$role = $_SESSION['role'] ?? 'admin'; // default to admin if not set
?>

<?php renderTemplate($role, 'navbar'); ?>

<!-- Integrate admin content with responsive sidebar -->
<div class="admin-content">
    <h2 class="page-title">Data Sampah</h2>
    
    <!-- Tab Navigation -->
    <div class="tab-navigation">
        <ul>
            <li>
                <a href="?tab=verifikasi" class="<?= $active_tab === 'verifikasi' ? 'active' : '' ?>">
                   <i class="fas fa-clipboard-check"></i> Verifikasi Laporan
                </a>
            </li>
            <li>
                <a href="?tab=kategori" class="<?= $active_tab === 'kategori' ? 'active' : '' ?>">
                   <i class="fas fa-tags"></i> Manajemen Kategori
                </a>
            </li>
        </ul>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success">
        <?= $_SESSION['success_message'] ?>
        <?php unset($_SESSION['success_message']); ?>
    </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger">
        <?= $_SESSION['error_message'] ?>
        <?php unset($_SESSION['error_message']); ?>
    </div>
    <?php endif; ?>
    
    <?php if ($active_tab === 'verifikasi'): ?>
    <!-- Tab Verifikasi Laporan -->
    
    <!-- Form Pencarian -->
    <div class="search-form">
        <h4>Pencarian Data</h4>
        <form method="GET" action="">
            <input type="hidden" name="tab" value="verifikasi">
            <div class="form-row">
                <div class="form-group">
                    <label for="username">Nama Akun:</label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($_GET['username'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="tanggal_mulai">Tanggal Mulai:</label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="<?= htmlspecialchars($_GET['tanggal_mulai'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="tanggal_akhir">Tanggal Akhir:</label>
                    <input type="date" id="tanggal_akhir" name="tanggal_akhir" value="<?= htmlspecialchars($_GET['tanggal_akhir'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="kategori_id">Jenis Sampah:</label>
                    <select id="kategori_id" name="kategori_id">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($kategori_data as $kat): ?>
                            <option value="<?= $kat['id'] ?>" <?= (isset($_GET['kategori_id']) && $_GET['kategori_id'] == $kat['id']) ? 'selected' : '' ?>>
                                <?= ucfirst($kat['nama_kategori']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="status">Status Verifikasi:</label>
                    <select id="status" name="status">
                        <option value="">Semua Status</option>
                        <option value="menunggu" <?= (isset($_GET['status']) && $_GET['status'] === 'menunggu') ? 'selected' : '' ?>>Menunggu</option>
                        <option value="diterima" <?= (isset($_GET['status']) && $_GET['status'] === 'diterima') ? 'selected' : '' ?>>Diterima</option>
                        <option value="ditolak" <?= (isset($_GET['status']) && $_GET['status'] === 'ditolak') ? 'selected' : '' ?>>Ditolak</option>
                    </select>
                </div>
                
                <div class="form-group button-group">
                    <div class="button-container">
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        <a href="?tab=verifikasi" class="reset-btn">
                            <i class="fas fa-sync-alt"></i> Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Informasi Hasil Pencarian -->
    <?php if(isset($_GET['username']) || isset($_GET['tanggal_mulai']) || isset($_GET['tanggal_akhir']) || isset($_GET['kategori_id']) || isset($_GET['status'])): ?>
    <div class="search-results-info">
        <strong>Filter Aktif:</strong>
        <?php 
        $filters = [];
        if(!empty($_GET['username'])) $filters[] = "Akun: " . htmlspecialchars($_GET['username']);
        if(!empty($_GET['tanggal_mulai'])) $filters[] = "Dari tanggal: " . htmlspecialchars($_GET['tanggal_mulai']);
        if(!empty($_GET['tanggal_akhir'])) $filters[] = "Sampai tanggal: " . htmlspecialchars($_GET['tanggal_akhir']);
        if(!empty($_GET['kategori_id'])) {
            foreach ($kategori_data as $kat) {
                if ($kat['id'] == $_GET['kategori_id']) {
                    $filters[] = "Jenis Sampah: " . ucfirst($kat['nama_kategori']);
                    break;
                }
            }
        }
        if(!empty($_GET['status'])) $filters[] = "Status: " . ucfirst(htmlspecialchars($_GET['status']));
        echo implode(" | ", $filters);
        ?>
        <div class="results-count">
            Menampilkan <?= $result->num_rows ?> hasil pencarian.
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Tabel hasil verifikasi -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Jumlah (kg)</th>
                    <th>Poin</th>
                    <th>Drop Point</th>
                    <th>Foto</th>
                    <th>Catatan</th>
                    <th>Status</th>
                    <th>Verifikasi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td>
                            <?php 
                            $tanggal_pengumpulan = $row['tanggal_pengumpulan'];
                            $bulan = [
                                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', 
                                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', 
                                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                            ];
                            
                            // Pecah tanggal berdasarkan format YYYY-MM-DD
                            $date_parts = explode('-', $tanggal_pengumpulan);
                            
                            // Validasi tanggal dan pastikan bukan 0000-00-00
                            if (count($date_parts) == 3 && $date_parts[0] != '0000') {
                                $tahun = $date_parts[0];
                                $bulan_angka = $date_parts[1];
                                $tanggal = $date_parts[2];
                                
                                // Hanya tampilkan jika bulan ada dalam array dan nilainya valid
                                if (isset($bulan[$bulan_angka]) && $bulan_angka != '00') {
                                    echo $tanggal . ' ' . $bulan[$bulan_angka] . ' ' . $tahun;
                                } else {
                                    echo date('d F Y'); // Tampilkan tanggal hari ini sebagai alternatif
                                }
                            } else {
                                echo date('d F Y'); // Tampilkan tanggal hari ini jika format tidak valid
                            }
                            ?>
                        </td>
                        <td><?= ucfirst($row['nama_kategori']) ?></td>
                        <td><?= $row['jumlah_kg'] ?> kg</td>
                        <td><?= $row['total_point'] ?> poin</td>
                        <td><?= $row['drop_point'] ?></td>
                        <td>
                            <?php if ($row['foto']): ?>
                                <a href="../uploads/<?= htmlspecialchars($row['foto']) ?>" target="_blank">
                                    <img src="../uploads/<?= htmlspecialchars($row['foto']) ?>" width="80" class="thumbnail">
                                </a>
                            <?php else: ?>
                                Tidak ada foto
                            <?php endif; ?>
                        </td>
                        <td><?= nl2br(htmlspecialchars($row['catatan'])) ?></td>
                        <td>
                            <?php
                            $status = $row['status_verifikasi'];
                            $badge_class = $status == 'diterima' ? 'badge-success' : ($status == 'ditolak' ? 'badge-danger' : 'badge-warning');
                            echo "<span class='status-badge $badge_class'>$status</span>";
                            ?>
                        </td>
                        <td>
                            <form method="POST" action="verifikaasi_proses.php">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <div class="verify-controls">
                                    <select name="status_verifikasi">
                                        <option value="menunggu" <?= $status == 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                        <option value="diterima" <?= $status == 'diterima' ? 'selected' : '' ?>>Diterima</option>
                                        <option value="ditolak" <?= $status == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                                    </select>
                                    <button type="submit" class="update-btn">Update</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="no-data">Tidak ada data yang ditemukan</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php else: ?>
    <!-- Tab Manajemen Kategori -->
    <div class="section-header">
        <h3>Daftar Kategori Sampah</h3>
        <button id="btnTambahKategori" class="add-category-btn">
            <i class="fas fa-plus"></i> Tambah Kategori
        </button>
    </div>
    
    <!-- Form tambah kategori (hidden by default) -->
    <div id="formTambahKategori" class="form-container" style="display: none;">
        <h4>Tambah Kategori Baru</h4>
        <form method="POST" action="" class="category-form">
            <input type="hidden" name="action" value="add">
            
            <div class="form-field">
                <label for="nama_kategori">Nama Kategori:<span class="required">*</span></label>
                <input type="text" id="nama_kategori" name="nama_kategori" required>
            </div>
            
            <div class="form-field">
                <label for="deskripsi">Deskripsi:</label>
                <textarea id="deskripsi" name="deskripsi"></textarea>
            </div>
            
            <div class="form-field">
                <label for="poin_per_kg">Poin per kg:<span class="required">*</span></label>
                <input type="number" id="poin_per_kg" name="poin_per_kg" min="0" required>
            </div>
            
            <div class="form-field">
                <label for="status">Status:</label>
                <select id="status" name="status">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Tidak Aktif</option>
                </select>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <button type="button" id="btnBatalTambah" class="btn-cancel">
                    <i class="fas fa-times"></i> Batal
                </button>
            </div>
        </form>
    </div>
    
    <!-- Tabel kategori -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Poin per kg</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($kategori_data) > 0): ?>
                    <?php foreach ($kategori_data as $kategori): ?>
                    <tr id="kategori-row-<?= $kategori['id'] ?>">
                        <td><?= htmlspecialchars(ucfirst($kategori['nama_kategori'])) ?></td>
                        <td><?= htmlspecialchars($kategori['deskripsi']) ?></td>
                        <td><?= $kategori['poin_per_kg'] ?></td>
                        <td class="text-center">
                            <?php
                            $status_text = $kategori['status'] == 'aktif' ? 'Aktif' : 'Tidak Aktif';
                            $badge_class = $kategori['status'] == 'aktif' ? 'badge-success' : 'badge-secondary';
                            echo "<span class='status-badge $badge_class'>$status_text</span>";
                            ?>
                        </td>
                        <td class="text-center">
                            <button class="btn-edit" data-id="<?= $kategori['id'] ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn-delete" data-id="<?= $kategori['id'] ?>" data-kategori="<?= htmlspecialchars($kategori['nama_kategori']) ?>">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                    <!-- Form edit kategori (hidden by default) -->
                    <tr id="edit-form-<?= $kategori['id'] ?>" class="edit-row" style="display: none;">
                        <td colspan="5">
                            <form method="POST" action="" class="category-form">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" value="<?= $kategori['id'] ?>">
                                
                                <div class="form-field">
                                    <label for="edit_nama_kategori_<?= $kategori['id'] ?>">Nama Kategori:<span class="required">*</span></label>
                                    <input type="text" id="edit_nama_kategori_<?= $kategori['id'] ?>" name="nama_kategori" required value="<?= htmlspecialchars($kategori['nama_kategori']) ?>">
                                </div>
                                
                                <div class="form-field">
                                    <label for="edit_deskripsi_<?= $kategori['id'] ?>">Deskripsi:</label>
                                    <textarea id="edit_deskripsi_<?= $kategori['id'] ?>" name="deskripsi"><?= htmlspecialchars($kategori['deskripsi']) ?></textarea>
                                </div>
                                
                                <div class="form-field">
                                    <label for="edit_poin_per_kg_<?= $kategori['id'] ?>">Poin per kg:<span class="required">*</span></label>
                                    <input type="number" id="edit_poin_per_kg_<?= $kategori['id'] ?>" name="poin_per_kg" min="0" required value="<?= $kategori['poin_per_kg'] ?>">
                                </div>
                                
                                <div class="form-field">
                                    <label for="edit_status_<?= $kategori['id'] ?>">Status:</label>
                                    <select id="edit_status_<?= $kategori['id'] ?>" name="status">
                                        <option value="aktif" <?= $kategori['status'] == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                        <option value="nonaktif" <?= $kategori['status'] == 'nonaktif' ? 'selected' : '' ?>>Tidak Aktif</option>
                                    </select>
                                </div>
                                
                                <div class="form-buttons">
                                    <button type="submit" class="btn-save">
                                        <i class="fas fa-save"></i> Simpan
                                    </button>
                                    <button type="button" class="btn-cancel-edit" data-id="<?= $kategori['id'] ?>">
                                        <i class="fas fa-times"></i> Batal
                                    </button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="no-data">Tidak ada kategori sampah yang ditemukan</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Modal Konfirmasi Hapus -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Konfirmasi Hapus</h4>
                <span id="closeModal" class="close-modal">&times;</span>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kategori <strong id="deleteKategoriName"></strong>?</p>
            </div>
            <div class="modal-footer">
                <form method="POST" action="" id="deleteForm">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="deleteKategoriId" value="">
                    <button type="button" id="btnCancelDelete" class="btn-cancel">
                        Batal
                    </button>
                    <button type="submit" class="btn-delete-confirm">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle form tambah kategori
        const btnTambahKategori = document.getElementById('btnTambahKategori');
        const formTambahKategori = document.getElementById('formTambahKategori');
        const btnBatalTambah = document.getElementById('btnBatalTambah');
        
        btnTambahKategori.addEventListener('click', function() {
            formTambahKategori.style.display = 'block';
            btnTambahKategori.style.display = 'none';
        });
        
        btnBatalTambah.addEventListener('click', function() {
            formTambahKategori.style.display = 'none';
            btnTambahKategori.style.display = 'block';
        });
        
        // Toggle form edit kategori
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                document.getElementById(`kategori-row-${id}`).style.display = 'none';
                document.getElementById(`edit-form-${id}`).style.display = 'table-row';
            });
        });
        
        document.querySelectorAll('.btn-cancel-edit').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                document.getElementById(`kategori-row-${id}`).style.display = 'table-row';
                document.getElementById(`edit-form-${id}`).style.display = 'none';
            });
        });
        
        // Modal hapus kategori
        const deleteModal = document.getElementById('deleteModal');
        const closeModal = document.getElementById('closeModal');
        const btnCancelDelete = document.getElementById('btnCancelDelete');
        const deleteKategoriName = document.getElementById('deleteKategoriName');
        const deleteKategoriId = document.getElementById('deleteKategoriId');
        
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const kategori = this.dataset.kategori;
                
                deleteKategoriName.textContent = kategori;
                deleteKategoriId.value = id;
                deleteModal.style.display = 'block';
            });
        });
        
        closeModal.addEventListener('click', function() {
            deleteModal.style.display = 'none';
        });
        
        btnCancelDelete.addEventListener('click', function() {
            deleteModal.style.display = 'none';
        });
        
        window.addEventListener('click', function(event) {
            if (event.target == deleteModal) {
                deleteModal.style.display = 'none';
            }
        });
        
        // Auto close alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.display = 'none';
            });
        }, 5000);
        
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
    });
    </script>
    <?php endif; ?>
</div>

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
    font-size: 24px;
    margin-bottom: 20px;
    color: #2C3E50;
}

/* Tab navigation */
.tab-navigation {
    margin-bottom: 20px;
    border-bottom: 1px solid #dee2e6;
}

.tab-navigation ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
    display: flex;
}

.tab-navigation li a {
    display: inline-block;
    padding: 10px 20px;
    text-decoration: none;
    color: #495057;
    transition: all 0.2s ease;
}

.tab-navigation li a.active {
    border-bottom: 2px solid #2C3E50;
    color: #2C3E50;
    font-weight: bold;
}

/* Alerts */
.alert {
    padding: 10px 15px;
    border-radius: 4px;
    margin-bottom: 15px;
}

.alert-success {
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.alert-danger {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

/* Search form */
.search-form {
    background-color: #f8f9fa;
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid #dee2e6;
}

.search-form h4 {
    margin-top: 0;
    margin-bottom: 15px;
}

.form-row {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.form-group {
    flex: 1;
    min-width: 200px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ced4da;
    border-radius: 4px;
}

.button-group {
    display: flex;
    align-items: flex-end;
}

.button-container {
    display: flex;
    gap: 10px;
    width: 100%;
}

.search-btn,
.reset-btn {
    flex: 1;
    padding: 8px 15px;
    border-radius: 4px;
    cursor: pointer;
    text-align: center;
}

.search-btn {
    background-color: #2C3E50;
    color: white;
    border: none;
}

.reset-btn {
    background-color: #6c757d;
    color: white;
    text-decoration: none;
    display: inline-block;
}

/* Search results info */
.search-results-info {
    margin-bottom: 15px;
    padding: 10px;
    background-color: #e9f5fd;
    border-radius: 5px;
    border-left: 5px solid #0d6efd;
}

.results-count {
    margin-top: 10px;
    font-weight: bold;
}

/* Table styles */
.table-container {
    overflow-x: auto;
    margin-bottom: 20px;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    background-color: #2C3E50;
    color: white;
    padding: 12px;
    text-align: left;
}

.data-table td {
    padding: 12px;
    border-bottom: 1px solid #dee2e6;
}

.data-table tr:hover {
    background-color: #f8f9fa;
}

.no-data {
    padding: 20px;
    text-align: center;
}

.thumbnail {
    border-radius: 4px;
}

.text-center {
    text-align: center;
}

/* Status badges */
.status-badge {
    padding: 5px 10px;
    color: white;
    border-radius: 30px;
    font-size: 12px;
    display: inline-block;
}

.badge-success {
    background-color: #28a745;
}

.badge-warning {
    background-color: #ffc107;
}

.badge-danger {
    background-color: #dc3545;
}

.badge-secondary {
    background-color: #6c757d;
}

/* Verify controls */
.verify-controls {
    display: flex;
    gap: 5px;
}

.verify-controls select {
    padding: 5px;
    border: 1px solid #ced4da;
    border-radius: 4px;
}

.update-btn {
    padding: 5px 10px;
    background-color: #2C3E50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

/* Section header with buttons */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.section-header h3 {
    margin: 0;
}

.add-category-btn {
    padding: 8px 15px;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

/* Form container for categories */
.form-container {
    background-color: #f8f9fa;
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid #dee2e6;
}

.category-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.form-field label {
    display: block;
    margin-bottom: 5px;
}

.form-field .required {
    color: red;
}

.form-field input,
.form-field select,
.form-field textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #ced4da;
    border-radius: 4px;
}

.form-field textarea {
    min-height: 100px;
    resize: vertical;
}

.form-buttons {
    display: flex;
    gap: 10px;
}

.btn-save,
.btn-cancel,
.btn-edit,
.btn-delete,
.btn-delete-confirm {
    padding: 8px 15px;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    flex: 1;
}

.btn-save {
    background-color: #28a745;
}

.btn-cancel {
    background-color: #6c757d;
}

.btn-edit {
    background-color: #2C3E50;
    margin-right: 5px;
}

.btn-delete,
.btn-delete-confirm {
    background-color: #dc3545;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: white;
    margin: 15% auto;
    padding: 0;
    border: 1px solid #888;
    width: 400px;
    border-radius: 5px;
    max-width: 90%;
}

.modal-header {
    padding: 15px;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h4 {
    margin: 0;
}

.close-modal {
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
}

.modal-body {
    padding: 15px;
}

.modal-footer {
    padding: 15px;
    border-top: 1px solid #dee2e6;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .form-row {
        flex-direction: column;
    }
    
    .btn-edit,
    .btn-delete {
        padding: 6px 10px;
        font-size: 12px;
    }
}

@media (max-width: 576px) {
    .page-title {
        font-size: 20px;
    }
    
    .tab-navigation li a {
        padding: 8px 10px;
        font-size: 14px;
    }
    
    .data-table th,
    .data-table td {
        padding: 8px;
        font-size: 13px;
    }
}
</style>

<!-- Tambahkan FontAwesome untuk ikon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<?php renderTemplate($role, 'footer'); ?>
