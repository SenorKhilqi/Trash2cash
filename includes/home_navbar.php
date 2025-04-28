<?php
// Menentukan base path untuk link
$current_dir = dirname($_SERVER['PHP_SELF']);
$project_root = '/project_root'; // Sesuaikan dengan nama folder project di server
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Navbar dengan Icon dan Spasi Kanan Kiri</title>
  <!-- Link Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Link Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom">
  <div class="container-fluid">
    <a class="navbar-brand ps-4" href="<?php echo $project_root; ?>">BrandKu</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto pe-4">
        <li class="nav-item">
          <a class="nav-link" href="<?php echo $project_root; ?>">
            <i class="fas fa-home me-1"></i> Beranda
          </a>
        </li>

        <!-- Dropdown Informasi -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="informasiDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-info-circle me-1"></i> Informasi
          </a>
          <ul class="dropdown-menu" aria-labelledby="informasiDropdown">
            <li><a class="dropdown-item" href="<?php echo $project_root; ?>/beranda/information_about.php">Informasi Umum</a></li>
            <li><a class="dropdown-item" href="<?php echo $project_root; ?>/beranda/information_team.php">Informasi Tim</a></li>
            <li><a class="dropdown-item" href="<?php echo $project_root; ?>/beranda/information_metodologi.php">Informasi Metodologi</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo $project_root; ?>/beranda/kontak.php">
            <i class="fas fa-phone me-1"></i> Kontak
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo $project_root; ?>/public/login.php">
            <i class="fas fa-sign-in-alt me-1"></i> Login
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo $project_root; ?>/public/register.php">
            <i class="fas fa-user-plus me-1"></i> Register
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<style>
  .navbar-custom {
    background-color: #444444; /* Warna abu-abu gelap */
  }
  .navbar-custom .nav-link,
  .navbar-custom .navbar-brand {
    color: #fff; /* Teks putih */
  }
  .navbar-custom .nav-link:hover {
    color: #f0e68c; /* Hover sedikit kuning */
  }
</style>

<!-- Link Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
