<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Sidebar</title>
    <style>
        /* Reset CSS dasar */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Sidebar */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background-color: #2C3E50; /* Different color for admin */
            padding-top: 60px;
            transition: all 0.3s ease;
            overflow-x: hidden;
            z-index: 1000;
            transform: translateX(0);
        }

        #sidebar.closed {
            transform: translateX(-250px);
            width: 0;
            padding: 0;
        }

        #sidebar ul {
            list-style: none;
        }

        #sidebar ul li {
            padding: 15px 20px;
            white-space: nowrap;
        }

        #sidebar ul li a {
            text-decoration: none;
            color: white;
            font-size: 18px;
            display: block;
            transition: 0.3s;
        }

        #sidebar ul li a:hover {
            background-color: #34495E;
            border-radius: 5px;
        }

        /* Tombol Toggle */
        #toggle-btn {
            position: fixed;
            top: 15px;
            left: 260px;
            background-color: #2C3E50;
            color: white;
            border: none;
            font-size: 24px;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
            transition: all 0.3s ease;
            z-index: 1001;
        }

        /* Saat sidebar ditutup */
        #sidebar.closed + #toggle-btn {
            left: 10px;
        }

        /* Konten utama */
        #content {
            margin-left: 260px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        #sidebar.closed ~ #content {
            margin-left: 20px;
        }
    </style>
</head>
<body>

<nav id="sidebar">
    <ul>
        <li><a href="dashboard.php">Dashboard Admin</a></li>
        <li><a href="other_admin_pages.php">Pengguna</a></li>
        <li><a href="verifikasi_laporan.php">Data Sampah</a></li>
        <li><a href="profil_admin.php">Profil</a></li>
        <li><a href="../logout.php">Logout</a></li>
    </ul>
</nav>

<!-- Tombol Toggle -->
<button id="toggle-btn">&#9776;</button>

<!-- Main Content -->
<div id="content">
    <!-- Content akan diisi oleh halaman yang menggunakan navbar ini -->
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggle-btn');
        const content = document.getElementById('content');
        
        // Fungsi untuk toggle sidebar
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('closed');
            
            // Animasi pergerakan konten
            if(sidebar.classList.contains('closed')) {
                // Sidebar tertutup, konten bergeser ke kiri
                content.style.marginLeft = '20px';
                toggleBtn.style.left = '10px';
            } else {
                // Sidebar terbuka, konten bergeser ke kanan
                content.style.marginLeft = '260px';
                toggleBtn.style.left = '260px';
            }
        });
    });
</script>

</body>
</html>
