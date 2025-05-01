<?php
include_once '../libs/template_engine.php';

// Untuk halaman utama
renderTemplate('home', 'navbar');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trash2Cash</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Reset CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }
        
        a {
            text-decoration: none;
            color: #4CAF50;
        }

        .hero-section {
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../assets/images/hero.png');
            background-size: cover;
            background-position: center;
            color: white;
            height: 500px;
            display: flex;
            align-items: center;
            text-align: center;
        }
        
        /* Section Styles */
        .section {
            padding: 60px 0;
        }
        
        .content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .section-title {
            text-align: center;
            font-size: 2.2rem;
            margin-bottom: 30px;
            color: #333;
            position: relative;
            padding-bottom: 15px;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: #4CAF50;
        }
        
        .lead {
            font-size: 1.2rem;
            line-height: 1.8;
            margin-bottom: 30px;
        }
        
        .text-center {
            text-align: center;
        }
        
        /* Background Colors */
        .bg-light {
            background-color: #f8f9fa;
        }
        
        .bg-danger {
            background-color: #dc3545;
        }
        
        .text-white {
            color: white;
        }
        
        /* Feature Cards */
        .features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            margin-top: 40px;
        }
        
        .feature-card {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            flex: 1;
            min-width: 250px;
            max-width: 350px;
            transition: transform 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
        }
        
        .card-icon {
            height: 70px;
            width: 70px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background-color: #e8f5e9;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .card-icon i {
            font-size: 28px;
            color: #4CAF50;
        }
        
        .feature-card h4 {
            margin-bottom: 15px;
            font-size: 1.5rem;
            color: #333;
        }
        
        /* Impact Cards */
        .impacts {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            margin-top: 40px;
        }
        
        .impact-card {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            flex: 1;
            min-width: 250px;
            max-width: 350px;
        }
        
        .impact-card i {
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        /* Benefit Cards */
        .benefits {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            margin-top: 40px;
        }
        
        .benefit-card {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            flex: 1;
            min-width: 300px;
            max-width: 500px;
        }
        
        .benefit-icon {
            height: 60px;
            width: 60px;
            margin-bottom: 20px;
            border-radius: 50%;
            background-color: #e8f5e9;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .benefit-icon i {
            font-size: 24px;
            color: #4CAF50;
        }
        
        .benefit-card h4 {
            margin-bottom: 15px;
            font-size: 1.5rem;
            color: #333;
        }
        
        .benefit-card ul {
            padding-left: 20px;
        }
        
        .benefit-card ul li {
            margin-bottom: 10px;
        }
        
        /* Steps */
        .steps {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            margin-top: 40px;
        }
        
        .step-card {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            flex: 1;
            min-width: 200px;
            max-width: 250px;
            position: relative;
        }
        
        .step-number {
            height: 40px;
            width: 40px;
            background-color: #4CAF50;
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.2rem;
            font-weight: bold;
            margin: 0 auto 15px;
        }
        
        /* CTA Section */
        .cta-section {
            background-color: #4CAF50;
            color: white;
            padding: 80px 0;
        }
        
        .cta-section h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        
        .cta-buttons {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .btn-success {
            background-color: white;
            color: #4CAF50;
            padding: 12px 30px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .btn-success:hover {
            background-color: #f8f9fa;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .btn-outline-success {
            background-color: transparent;
            color: white;
            padding: 12px 30px;
            border-radius: 4px;
            border: 2px solid white;
            font-weight: bold;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .btn-outline-success:hover {
            background-color: white;
            color: #4CAF50;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        /* Objectives */
        .objectives {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            margin-top: 40px;
        }
        
        .objective-card {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            flex: 1;
            min-width: 300px;
            max-width: 500px;
        }
        
        .objective-card h3 {
            margin-bottom: 20px;
            color: #333;
        }
        
        .objective-card i {
            color: #4CAF50;
            margin-right: 10px;
        }
        
        .objective-card ul {
            padding-left: 20px;
        }
        
        .objective-card ul li {
            margin-bottom: 10px;
        }
        
        /* Footer */
        .footer {
            background-color: #333;
            color: white;
            padding: 60px 0 30px;
        }
        
        .footer-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .footer-about, .footer-links, .footer-contact, .footer-social {
            flex: 1;
            min-width: 250px;
        }
        
        .footer h5 {
            margin-bottom: 20px;
            font-size: 1.2rem;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer h5:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background-color: #4CAF50;
        }
        
        .footer ul {
            padding-left: 0;
            list-style-type: none;
        }
        
        .footer ul li {
            margin-bottom: 10px;
        }
        
        .footer a {
            color: #ccc;
            transition: color 0.3s ease;
        }
        
        .footer a:hover {
            color: white;
        }
        
        .footer-social a {
            display: inline-block;
            height: 40px;
            width: 40px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        
        .footer hr {
            border: none;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin: 20px 0;
        }
        
        .footer-bottom {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        /* Floating Action Button */
        .floating-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 999;
        }
        
        .help-button {
            height: 60px;
            width: 60px;
            border-radius: 50%;
            background-color: #4CAF50;
            color: white;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        
        .help-button:hover {
            transform: scale(1.1);
            background-color: #3e8e41;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .section {
                padding: 50px 0;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .cta-section h2 {
                font-size: 2.2rem;
            }
        }
        
        @media (max-width: 768px) {
            .section {
                padding: 40px 0;
            }
            
            .section-title {
                font-size: 1.8rem;
            }
            
            .feature-card, .impact-card, .benefit-card, .step-card, .objective-card {
                min-width: 100%;
            }
            
            .footer-grid > div {
                min-width: 100%;
            }
            
            .cta-section h2 {
                font-size: 1.8rem;
            }
            
            .floating-button {
                bottom: 20px;
                right: 20px;
            }
            
            .help-button {
                height: 50px;
                width: 50px;
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>

    <section class="hero-section">
        <div class="container">
            <div class="row justify-content-center" data-aos="fade-up">
                <div class="col-md-8">
                    <h1 class="display-4 fw-bold">Trash2Cash</h1>
                    <p class="lead">Trash2Cash adalah aplikasi inovatif yang mengintegrasikan sistem drop point untuk menukarkan sampah dengan fitur pemetaan lokasi titik drop point terdekat. Kami memberikan insentif berupa poin yang dapat ditukar dengan uang atau voucher untuk mendorong masyarakat berpartisipasi aktif dalam pengelolaan sampah.</p>
                </div>
            </div>
        </div>
    </section>

<!-- Tentang Trash2Cash -->
<!-- <section class="section" data-aos="fade-up">
    <div class="content">
        <h2 class="section-title">Tentang </h2>
        <p class="lead text-center">
            Trash2Cash adalah aplikasi inovatif yang mengintegrasikan sistem drop point untuk menukarkan sampah dengan fitur pemetaan lokasi titik drop point terdekat. Kami memberikan insentif berupa poin yang dapat ditukar dengan uang atau voucher untuk mendorong masyarakat berpartisipasi aktif dalam pengelolaan sampah.
        </p>
    </div>
</section> -->

<!-- Fitur Utama -->
<section class="section bg-light" data-aos="fade-up">
    <div class="content">
        <h2 class="section-title">Fitur Utama</h2>
        <div class="features">
            <div class="feature-card">
                <div class="card-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h4>Pemetaan Drop Point</h4>
                <p>Temukan lokasi drop point terdekat dengan mudah melalui peta interaktif</p>
            </div>
            <div class="feature-card">
                <div class="card-icon">
                    <i class="fas fa-qrcode"></i>
                </div>
                <h4>Pemindaian QR Code</h4>
                <p>Pindai kode QR di lokasi untuk menukarkan sampah dengan poin</p>
            </div>
            <div class="feature-card">
                <div class="card-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <h4>Sistem Penukaran Poin</h4>
                <p>Kumpulkan poin dan tukarkan dengan uang atau voucher menarik</p>
            </div>
        </div>
    </div>
</section>

<!-- Masalah Pengelolaan Sampah -->
<section class="section bg-danger text-white" data-aos="fade-up">
    <div class="content">
        <h2 class="section-title">Masalah Pengelolaan Sampah</h2>
        <div class="impacts">
            <div class="impact-card">
                <i class="fas fa-trash-alt"></i>
                <h4>Akumulasi Sampah</h4>
                <p>68,5 juta ton sampah dihasilkan di Indonesia setiap tahun dengan 17% berupa sampah plastik</p>
            </div>
            <div class="impact-card">
                <i class="fas fa-frown"></i>
                <h4>Kesadaran Rendah</h4>
                <p>Rendahnya kesadaran masyarakat dalam memilah dan mendaur ulang sampah</p>
            </div>
            <div class="impact-card">
                <i class="fas fa-building"></i>
                <h4>Infrastruktur Terbatas</h4>
                <p>Kekurangan infrastruktur yang memadai untuk pengelolaan sampah yang efektif</p>
            </div>
        </div>
    </div>
</section>

<!-- Manfaat Program -->
<section class="section" data-aos="fade-up">
    <div class="content">
        <h2 class="section-title">Manfaat Program Trash2Cash</h2>
        <div class="benefits">
            <div class="benefit-card">
                <div class="benefit-icon">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <h4>Bagi Masyarakat</h4>
                <ul>
                    <li>Meningkatkan kesadaran akan pentingnya pengelolaan sampah</li>
                    <li>Akses mudah ke fasilitas pengelolaan sampah melalui sistem digital</li>
                    <li>Mendapatkan insentif dari sampah yang dikumpulkan</li>
                </ul>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <h4>Bagi Mitra</h4>
                <ul>
                    <li>Memberikan peluang bisnis bagi mitra pengelola sampah</li>
                    <li>Sistem terintegrasi untuk manajemen sampah</li>
                    <li>Meningkatkan keberlanjutan lingkungan</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Cara Kerja -->
<section class="section bg-light" data-aos="fade-up">
    <div class="content">
        <h2 class="section-title">Cara Kerja Trash2Cash</h2>
        <div class="steps">
            <div class="step-card">
                <div class="step-number">1</div>
                <h5>Pilah Sampah</h5>
                <p>Pisahkan sampah berdasarkan jenisnya (plastik, kertas, logam, dll.)</p>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <h5>Cari Drop Point</h5>
                <p>Temukan lokasi drop point terdekat melalui fitur peta</p>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <h5>Setor Sampah</h5>
                <p>Serahkan sampah dan pindai kode QR di lokasi</p>
            </div>
            <div class="step-card">
                <div class="step-number">4</div>
                <h5>Dapatkan Poin</h5>
                <p>Terima poin yang dapat ditukarkan dengan uang atau voucher</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section cta-section" data-aos="fade-up">
    <div class="content text-center">
        <h2>Siap Bergabung dengan Trash2Cash?</h2>
        <p class="lead">Mari bersama-sama mengelola sampah dengan bijak dan menjaga keberlanjutan lingkungan</p>
        <div class="cta-buttons">
            <a href="public/register.php" class="btn-success">Daftar Sekarang</a>
            <a href="beranda/information.php" class="btn-outline-success">Pelajari Lebih Lanjut</a>
        </div>
    </div>
</section>

<!-- Tujuan & Luaran -->
<section class="section bg-light" data-aos="fade-up">
    <div class="content">
        <div class="objectives">
            <div class="objective-card">
                <h3><i class="fas fa-bullseye"></i> Tujuan Program</h3>
                <ul>
                    <li>Meningkatkan kesadaran masyarakat dalam memilah dan mendaur ulang sampah</li>
                    <li>Meningkatkan partisipasi masyarakat dalam memilah</li>
                    <li>Menciptakan sistem drop point yang mudah diakses oleh masyarakat</li>
                    <li>Menjalin kemitraan dengan bank sampah dan perusahaan daur ulang</li>
                </ul>
            </div>
            <div class="objective-card">
                <h3><i class="fas fa-file-alt"></i> Luaran Program</h3>
                <ul>
                    <li>Laporan kemajuan pengembangan aplikasi Trash2Cash</li>
                    <li>Laporan akhir implementasi dan evaluasi efektivitas sistem</li>
                    <li>Desain aplikasi dengan fitur utama: pendaftaran, drop point, QR Code, dan penukaran poin</li>
                    <li>Akun media sosial sebagai platform edukasi dan promosi</li>
                </ul>
            </div>
        </div>
    </div>
</section>

</body>
</html>

<?php
renderTemplate('home', 'footer');
?>


