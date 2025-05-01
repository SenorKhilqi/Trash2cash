<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Sidebar Hide Show</title>
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
            background-color: #FFFDD0;
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
            color: #6b5a3a;
            font-size: 18px;
            display: block;
            transition: 0.3s;
        }

        #sidebar ul li a:hover {
            background-color: #f5f3d7;
            border-radius: 5px;
        }

        /* Tombol Toggle */
        #toggle-btn {
            position: fixed;
            top: 15px;
            left: 260px;
            background-color: #FFFDD0;
            color: #6b5a3a;
            border: 1px solid #D2B48C;
            font-size: 24px;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
            transition: all 0.3s ease;
            z-index: 1001;
        }

        /* Saat sidebar ditutup */
        #sidebar.closed+#toggle-btn {
            left: 10px;
        }

        /* Konten utama */
        #content {
            margin-left: 260px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        #sidebar.closed~#content {
            margin-left: 20px;
        }
    </style>
</head>

<body>

    <nav id="sidebar">
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="lapor_sampah.php">Lapor Sampah</a></li>
            <li><a href="history.php">Riwayat</a></li>
            <li><a href="tukar_poin.php">Tukar Poin</a></li>
            <li><a href="profil_user.php">Profil</a></li>
            <li><a href="card_generator.php">Cetak Kartu</a></li>
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
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggle-btn');
            const content = document.getElementById('content');

            // Fungsi untuk toggle sidebar
            toggleBtn.addEventListener('click', function () {
                sidebar.classList.toggle('closed');

                // Animasi pergerakan konten
                if (sidebar.classList.contains('closed')) {
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