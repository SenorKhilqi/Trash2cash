<?php
// Path relatif yang benar ke template_engine.php
include_once '../libs/template_engine.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Trash2Cash</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        /* Global Styles */
        body {
            font-family: 'Poppins', sans-serif;
            color: #333;
        }
        
        .section {
            padding: 80px 0;
        }
        
        .section-title {
            text-align: center;
            color: #198754;
            margin-bottom: 50px;
            position: relative;
            font-weight: 700;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -12px;
            left: 50%;
            transform: translateX(-50%);
            width: 70px;
            height: 4px;
            background-color: #198754;
        }
        
        /* Hero Section */
        .hero-section-small {
            padding: 80px 0;
            background-color: #f8f9fa;
            position: relative;
        }
        
        /* Content Cards */
        .content-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            padding: 30px;
            height: 100%;
            transition: all 0.3s ease;
        }
        
        .content-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        
        /* Mission List */
        .mission-list {
            list-style: none;
            padding-left: 0;
        }
        
        .mission-list li {
            padding: 8px 0;
            position: relative;
            padding-left: 28px;
        }
        
        .mission-list li::before {
            content: '\f00c';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            color: #198754;
            position: absolute;
            left: 0;
            top: 8px;
        }
        
        /* Timeline */
        .timeline {
            position: relative;
            padding: 40px 0;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            width: 4px;
            background-color: #e9ecef;
            top: 0;
            bottom: 0;
            left: 50%;
            margin-left: -2px;
        }
        
        .timeline-item {
            margin-bottom: 50px;
            position: relative;
            width: 100%;
        }
        
        .timeline-item:nth-child(even) {
            left: 50%;
            padding-left: 40px;
            padding-right: 0;
        }
        
        .timeline-item:nth-child(odd) {
            left: 0;
            padding-right: 40px;
            text-align: right;
        }
        
        .timeline-badge {
            position: absolute;
            top: 0;
            width: 60px;
            height: 60px;
            line-height: 1.4;
            text-align: center;
            background-color: #198754;
            color: #fff;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            z-index: 1;
        }
        
        .timeline-item:nth-child(even) .timeline-badge {
            left: -20px;
        }
        
        .timeline-item:nth-child(odd) .timeline-badge {
            right: -20px;
        }
        
        .date {
            font-size: 18px;
            font-weight: bold;
            line-height: 1;
        }
        
        .month, .year {
            font-size: 12px;
            line-height: 1;
        }
        
        .timeline-content {
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        /* Statistics Section */
        .stat-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            padding: 30px 15px;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #198754;
            margin-bottom: 10px;
        }
        
        .stat-label {
            font-size: 1rem;
            color: #6c757d;
        }
        
        /* Impact List */
        .impact-list {
            margin-top: 30px;
        }
        
        .impact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        
        .impact-item i {
            font-size: 24px;
            margin-right: 15px;
            margin-top: 2px;
        }
        
        /* Custom Card */
        .card {
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
        }
        
        /* Responsive Media Queries */
        @media (max-width: 991px) {
            .timeline::before {
                left: 31px;
            }
            
            .timeline-item:nth-child(even), .timeline-item:nth-child(odd) {
                left: 0;
                width: 100%;
                padding-left: 70px;
                padding-right: 0;
                text-align: left;
            }
            
            .timeline-item:nth-child(even) .timeline-badge, .timeline-item:nth-child(odd) .timeline-badge {
                left: 0;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/home_navbar.php'; ?>
    
    <div class="hero-section-small bg-light" data-aos="fade-up">
        <div class="container">
            <div class="row">
                <div class="col-md-8 mx-auto text-center">
                    <h1 class="text-success mb-4">Tentang Trash2Cash</h1>
                    <p class="lead">Platform digital inovatif untuk pengelolaan sampah berbasis insentif yang memudahkan masyarakat berkontribusi dalam menjaga kebersihan lingkungan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Vision Mission Section -->
    <section class="section" data-aos="fade-up">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-6" data-aos="fade-right" data-aos-delay="100">
                    <div class="content-card">
                        <h3 class="mb-4"><i class="fas fa-eye text-success me-2"></i>Visi</h3>
                        <p>Mewujudkan sistem pengelolaan sampah yang terintegrasi dengan teknologi digital untuk menciptakan lingkungan yang bersih dan berkelanjutan melalui partisipasi aktif masyarakat.</p>
                    </div>
                </div>
                <div class="col-md-6" data-aos="fade-left" data-aos-delay="200">
                    <div class="content-card">
                        <h3 class="mb-4"><i class="fas fa-bullseye text-success me-2"></i>Misi</h3>
                        <ul class="mission-list">
                            <li>Meningkatkan kesadaran masyarakat dalam memilah dan mendaur ulang sampah</li>
                            <li>Membangun sistem drop point yang mudah diakses oleh masyarakat</li>
                            <li>Memberikan insentif kepada masyarakat untuk berpartisipasi dalam pengelolaan sampah</li>
                            <li>Menjalin kemitraan dengan bank sampah, perusahaan daur ulang, dan pihak terkait</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Timeline Section -->
    <section class="section bg-light" data-aos="fade-up">
        <div class="container">
            <h2 class="section-title">Perjalanan Kami</h2>
            <div class="timeline">
                <div class="timeline-item" data-aos="fade-right">
                    <div class="timeline-badge">
                        <span class="date">10</span>
                        <span class="month">Jan</span>
                        <span class="year">2025</span>
                    </div>
                    <div class="timeline-content">
                        <h4>Awal Mula</h4>
                        <p>Identifikasi masalah pengelolaan sampah dan konseptualisasi platform Trash2Cash</p>
                    </div>
                </div>
                <div class="timeline-item" data-aos="fade-left">
                    <div class="timeline-badge">
                        <span class="date">15</span>
                        <span class="month">Mar</span>
                        <span class="year">2025</span>
                    </div>
                    <div class="timeline-content">
                        <h4>Riset & Pengembangan</h4>
                        <p>Studi literatur dan pembuatan desain aplikasi Trash2Cash dengan fitur utama</p>
                    </div>
                </div>
                <div class="timeline-item" data-aos="fade-right">
                    <div class="timeline-badge">
                        <span class="date">20</span>
                        <span class="month">April</span>
                        <span class="year">2025</span>
                    </div>
                    <div class="timeline-content">
                        <h4>Pengembangan Kemitraan</h4>
                        <p>Identifikasi dan menjalin kemitraan dengan bank sampah dan perusahaan daur ulang</p>
                    </div>
                </div>
                <div class="timeline-item" data-aos="fade-left">
                    <div class="timeline-badge">
                        <span class="date">05</span>
                        <span class="month">Mei</span>
                        <span class="year">2025</span>
                    </div>
                    <div class="timeline-content">
                        <h4>Peluncuran Platform</h4>
                        <p>Peluncuran aplikasi Trash2Cash dan implementasi sistem drop point</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="section" data-aos="fade-up">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-3 col-6">
                    <div class="stat-card h-100" data-aos="zoom-in">
                        <div class="stat-number" data-target="2500">0</div>
                        <div class="stat-label">Kg Sampah Terkumpul</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card h-100" data-aos="zoom-in" data-aos-delay="100">
                        <div class="stat-number" data-target="100">0</div>
                        <div class="stat-label">Drop Points</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card h-100" data-aos="zoom-in" data-aos-delay="200">
                        <div class="stat-number" data-target="1500">0</div>
                        <div class="stat-label">Pengguna Aktif</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card h-100" data-aos="zoom-in" data-aos-delay="300">
                        <div class="stat-number" data-target="25">0</div>
                        <div class="stat-label">Mitra Daur Ulang</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Rumusan Masalah Section -->
    <section class="section bg-light" data-aos="fade-up">
        <div class="container">
            <h2 class="section-title">Rumusan Masalah</h2>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title mb-3 text-success"><i class="fas fa-question-circle me-2"></i>Permasalahan</h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item bg-transparent">Bagaimana meningkatkan kesadaran masyarakat dalam memilah dan mendaur ulang sampah secara efektif?</li>
                                <li class="list-group-item bg-transparent">Bagaimana memanfaatkan teknologi digital untuk memberikan insentif kepada masyarakat?</li>
                                <li class="list-group-item bg-transparent">Bagaimana menciptakan sistem drop point yang terintegrasi dan mudah diakses oleh masyarakat?</li>
                                <li class="list-group-item bg-transparent">Bagaimana membangun kerja sama dengan mitra pengelola sampah?</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title mb-3 text-success"><i class="fas fa-lightbulb me-2"></i>Tantangan</h4>
                            <p>Di Indonesia, sekitar 68,5 juta ton sampah dihasilkan setiap tahunnya, dengan sampah plastik menyumbang 17% dari total tersebut (KLHK, 2023). Tantangan utama terletak pada:</p>
                            <ul>
                                <li>Rendahnya kesadaran masyarakat tentang pemilahan sampah</li>
                                <li>Kurangnya infrastruktur pendukung pengolahan sampah</li>
                                <li>Keterbatasan akses ke fasilitas pengelolaan sampah</li>
                                <li>Rendahnya motivasi masyarakat untuk berpartisipasi aktif</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Impact Section -->
    <section class="section" data-aos="fade-up">
        <div class="container">
            <h2 class="section-title">Dampak Program</h2>
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img src="img/impact-image.jpg" alt="Dampak Program" class="img-fluid rounded-3 shadow">
                </div>
                <div class="col-md-6">
                    <div class="impact-list">
                        <div class="impact-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <p>Mengurangi volume sampah yang terbuang ke TPA</p>
                        </div>
                        <div class="impact-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <p>Meningkatkan partisipasi masyarakat dalam pemilahan sampah</p>
                        </div>
                        <div class="impact-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <p>Memberikan peluang bisnis bagi mitra pengelola sampah</p>
                        </div>
                        <div class="impact-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <p>Mendukung terciptanya ekonomi sirkular dalam pengelolaan sampah</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tujuan & Luaran Section -->
    <section class="section bg-light" data-aos="fade-up">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title text-success mb-4"><i class="fas fa-bullseye me-2"></i>Tujuan Kegiatan</h3>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item bg-transparent">Meningkatkan kesadaran masyarakat dalam memilah dan mendaur ulang sampah</li>
                                <li class="list-group-item bg-transparent">Meningkatkan partisipasi masyarakat dalam memilah</li>
                                <li class="list-group-item bg-transparent">Menciptakan sistem drop point yang mudah diakses oleh masyarakat</li>
                                <li class="list-group-item bg-transparent">Mengidentifikasi dan menjalin kemitraan dengan bank sampah, perusahaan daur ulang, dan pihak terkait</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title text-success mb-4"><i class="fas fa-file-alt me-2"></i>Luaran</h3>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item bg-transparent">Laporan kemajuan pengembangan aplikasi Trash2Cash</li>
                                <li class="list-group-item bg-transparent">Laporan akhir implementasi dan evaluasi efektivitas sistem</li>
                                <li class="list-group-item bg-transparent">Desain aplikasi dengan fitur utama: pendaftaran, drop point, QR code, dan penukaran poin</li>
                                <li class="list-group-item bg-transparent">Akun media sosial sebagai platform edukasi dan promosi</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- JavaScript Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true
        });
        
        // Animate statistics counter
        document.addEventListener('DOMContentLoaded', function() {
            const countElements = document.querySelectorAll('.stat-number');
            
            countElements.forEach(item => {
                const target = parseInt(item.getAttribute('data-target'));
                const duration = 2000; // 2 seconds
                const step = target / duration * 10; // Update every 10ms
                let current = 0;
                
                const counter = setInterval(() => {
                    current += step;
                    if (current >= target) {
                        item.textContent = target;
                        clearInterval(counter);
                    } else {
                        item.textContent = Math.floor(current);
                    }
                }, 10);
            });
        });
    </script>
    
    <?php include '../includes/user_footer.php'; ?>
</body>
</html>