<?php
// This file contains the standard sidebar code for user pages
// Include this at the top of each user file instead of using the template engine

// Get current page for highlighting active menu
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- User Sidebar -->
<nav id="sidebar">
    <ul>
        <li><a href="dashboard.php" <?php echo $current_page == 'dashboard.php' ? 'class="active"' : ''; ?>>Dashboard</a></li>
        <li><a href="lapor_sampah.php" <?php echo $current_page == 'lapor_sampah.php' ? 'class="active"' : ''; ?>>Lapor Sampah</a></li>
        <li><a href="history.php" <?php echo $current_page == 'history.php' ? 'class="active"' : ''; ?>>Riwayat</a></li>
        <li><a href="tukar_poin.php" <?php echo $current_page == 'tukar_poin.php' ? 'class="active"' : ''; ?>>Tukar Poin</a></li>
        <li><a href="profil_user.php" <?php echo $current_page == 'profil_user.php' ? 'class="active"' : ''; ?>>Profil</a></li>
        <li><a href="card_generator.php" <?php echo $current_page == 'card_generator.php' ? 'class="active"' : ''; ?>>Cetak Kartu</a></li>
        <li><a href="../logout.php">Logout</a></li>
    </ul>
</nav>

<!-- Tombol Toggle -->
<button id="toggle-btn">&#9776;</button>

<!-- CSS untuk sidebar -->
<style>
    /* Reset CSS dasar */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f5f5f5;
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

    #sidebar ul li a:hover,
    #sidebar ul li a.active {
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

    #sidebar.closed ~ #content {
        margin-left: 20px;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        #sidebar {
            transform: translateX(-250px);
        }
        #sidebar.closed {
            transform: translateX(0);
        }
        #toggle-btn {
            left: 10px;
        }
        #sidebar.closed+#toggle-btn {
            left: 260px;
        }
        #content {
            margin-left: 20px;
        }
        #sidebar.closed~#content {
            margin-left: 260px;
        }
    }
</style>

<!-- JavaScript untuk sidebar -->
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

        // Responsive behavior
        function checkWindowSize() {
            if (window.innerWidth <= 768) {
                if (!sidebar.classList.contains('closed')) {
                    sidebar.classList.add('closed');
                    content.style.marginLeft = '20px';
                    toggleBtn.style.left = '10px';
                }
            }
        }

        // Initial check
        checkWindowSize();

        // Listen for window resize
        window.addEventListener('resize', checkWindowSize);
    });
</script>
