<?php
// Path relatif yang benar ke template_engine.php
include_once '../libs/template_engine.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metodologi Trash2Cash</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        /* Global Styles */
        body {
            font-family: 'Poppins', sans-serif;
            color: #333;
            background-color: #f8f9fa;
            line-height: 1.6;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-weight: 600;
            color: #212529;
        }

        .section {
            padding: 80px 0;
        }

        .section-title {
            text-align: center;
            font-weight: 700;
            margin-bottom: 50px;
            position: relative;
            padding-bottom: 15px;
            color: #28a745;
        }

        .section-title::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: 0;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: #28a745;
            border-radius: 2px;
        }

        /* Hero Section */
        .hero-section-small {
            padding: 100px 0 60px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            margin-bottom: 0;
        }

        .hero-section-small h1 {
            font-weight: 700;
            margin-bottom: 1.5rem;
            font-size: 3rem;
        }

        /* Progress Tracker */
        .progress-tracker {
            background-color: #28a745;
            padding: 25px 0;
            position: relative;
            margin-bottom: 80px;
        }

        .progress-tracker .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .progress-step {
            text-align: center;
            position: relative;
            flex: 1;
            max-width: 20%;
        }

        .progress-step small {
            color: white;
            display: block;
            margin-top: 8px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .step-number {
            width: 40px;
            height: 40px;
            background-color: white;
            border-radius: 50%;
            color: #28a745;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            position: relative;
            z-index: 2;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .progress-step.active .step-number {
            background-color: #ffc107;
            color: #212529;
            transform: scale(1.2);
        }

        /* Timeline */
        .timeline {
            position: relative;
            max-width: 900px;
            margin: 0 auto;
        }

        .timeline::before {
            content: '';
            position: absolute;
            width: 4px;
            background-color: #28a745;
            top: 0;
            bottom: 0;
            left: 36px;
            margin-left: -2px;
            border-radius: 4px;
        }

        .timeline-item {
            padding: 20px 40px 20px 80px;
            position: relative;
            background-color: inherit;
            width: 100%;
            margin-bottom: 30px;
        }

        .timeline-number {
            position: absolute;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #28a745;
            color: white;
            text-align: center;
            line-height: 50px;
            font-size: 20px;
            font-weight: bold;
            z-index: 1;
            left: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .timeline-content {
            padding: 20px 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .timeline-content h3 {
            margin-top: 0;
            color: #28a745;
            font-weight: 600;
            font-size: 1.4rem;
        }

        .timeline-list {
            padding-left: 20px;
        }

        .timeline-list li {
            margin-bottom: 8px;
            position: relative;
        }

        /* Standard Cards */
        .standard-card {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            height: 100%;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .standard-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: rgba(40, 167, 69, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .icon-circle i {
            font-size: 32px;
        }

        .standard-card h4 {
            text-align: center;
            margin-bottom: 15px;
            color: #333;
            font-weight: 600;
        }

        .standard-card p {
            text-align: center;
            color: #6c757d;
        }

        .details {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed #dee2e6;
        }

        /* FAQ Accordion */
        .accordion-item {
            margin-bottom: 15px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e9ecef;
        }

        .accordion-button {
            font-weight: 600;
            padding: 15px 20px;
            background-color: white;
            color: #333;
        }

        .accordion-button:not(.collapsed) {
            background-color: #e8f5e9;
            color: #28a745;
        }

        .accordion-button:focus {
            box-shadow: none;
            border-color: rgba(40, 167, 69, 0.25);
        }

        .accordion-body {
            padding: 20px;
            background-color: white;
        }

        /* Responsive Adjustments */
        @media (max-width: 991px) {
            .section {
                padding: 60px 0;
            }

            .hero-section-small {
                padding: 80px 0 40px;
            }

            .hero-section-small h1 {
                font-size: 2.5rem;
            }

            .timeline::before {
                left: 31px;
            }
        }

        @media (max-width: 767px) {
            .progress-tracker {
                padding: 20px 0;
                margin-bottom: 40px;
                overflow-x: auto;
            }

            .progress-step {
                min-width: 100px;
            }

            .timeline-item {
                padding: 15px 15px 15px 60px;
            }

            .timeline-number {
                width: 40px;
                height: 40px;
                line-height: 40px;
                font-size: 16px;
                left: 5px;
            }

            .timeline-content {
                padding: 15px;
            }

            .timeline::before {
                left: 24px;
            }

            .hero-section-small h1 {
                font-size: 2rem;
            }

            .section-title {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 575px) {
            .progress-tracker .row {
                flex-wrap: nowrap;
                margin: 0;
            }

            .progress-step small {
                font-size: 0.7rem;
            }

            .step-number {
                width: 30px;
                height: 30px;
                font-size: 14px;
            }

            .timeline::before {
                left: 19px;
            }

            .timeline-number {
                width: 30px;
                height: 30px;
                line-height: 30px;
                font-size: 14px;
                left: 4px;
            }

            .timeline-item {
                padding: 10px 10px 10px 50px;
            }
        }
    </style>

</head>

<body>
    <?php include '../includes/home_navbar.php'; ?>
    <!-- Methodology Hero Section -->
    <div class="hero-section-small bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-8 mx-auto text-center">
                    <h1 class="text-success mb-4">Metodologi</h1>
                    <p class="lead">Sistem dan proses pengelolaan sampah berbasis digital dengan insentif untuk
                        mendorong partisipasi masyarakat</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Process Flow Section -->
    <section class="section">
        <div class="container">
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-number">1</div>
                    <div class="timeline-content">
                        <h3>Pemilahan Sampah</h3>
                        <p>Pengguna memilah sampah berdasarkan kategori:</p>
                        <ul class="timeline-list">
                            <li>Sampah plastik (botol, kemasan, dll)</li>
                            <li>Sampah kertas dan kardus</li>
                            <li>Sampah logam dan kaleng</li>
                        </ul>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-number">2</div>
                    <div class="timeline-content">
                        <h3>Pencarian Drop Point</h3>
                        <p>Proses menemukan lokasi penyetoran sampah:</p>
                        <ul class="timeline-list">
                            <li>Menggunakan fitur peta di aplikasi</li>
                            <li>Melihat detail lokasi dan jam operasional</li>
                            <li>Mendapatkan petunjuk arah ke drop point terdekat</li>
                        </ul>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-number">3</div>
                    <div class="timeline-content">
                        <h3>Pemberian Insentif</h3>
                        <p>Mekanisme pemberian poin atau reward:</p>
                        <ul class="timeline-list">
                            <li>Perhitungan poin berdasarkan jenis dan berat sampah</li>
                            <li>Poin dikirimkan ke akun pengguna</li>
                            <li>Notifikasi perolehan poin</li>
                        </ul>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-number">4</div>
                    <div class="timeline-content">
                        <h3>Pengelolaan Sampah oleh Mitra</h3>
                        <p>Sampah yang terkumpul kemudian dikelola:</p>
                        <ul class="timeline-list">
                            <li>Pengumpulan dan sortir lanjutan</li>
                            <li>Proses daur ulang sesuai jenis sampah</li>
                            <li>Pembuatan produk daur ulang</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quality Standards Section -->
    <section class="section bg-light">
        <div class="container">
            <h2 class="section-title">Standar Pengelolaan</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="standard-card">
                        <div class="icon-circle mb-4">
                            <i class="fas fa-mobile-alt text-success"></i>
                        </div>
                        <h4>Sistem Digital</h4>
                        <p>Menggunakan teknologi untuk memudahkan pemilahan dan penukaran sampah</p>
                        <div class="details">
                            <p>Platform berbasis mobile dan web yang terintegrasi dengan sistem drop point.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="standard-card">
                        <div class="icon-circle mb-4">
                            <i class="fas fa-recycle text-success"></i>
                        </div>
                        <h4>Proses Daur Ulang</h4>
                        <p>Bekerja sama dengan mitra yang memiliki standar pengelolaan sampah berkelanjutan</p>
                        <div class="details">
                            <p>Menjamin sampah diproses dengan metode ramah lingkungan dan efisien.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="standard-card">
                        <div class="icon-circle mb-4">
                            <i class="fas fa-chart-line text-success"></i>
                        </div>
                        <h4>Transparansi Data</h4>
                        <p>Pelacakan dan pelaporan jumlah sampah yang dikelola dan poin yang diberikan</p>
                        <div class="details">
                            <p>Laporan berkala tentang dampak lingkungan dan ekonomi dari program.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sistem Insentif Section -->
    <section class="section">
        <div class="container">
            <h2 class="section-title mb-5">Sistem Insentif</h2>
            <div class="row align-items-center">
                <div class="col-md-6 mb-4">
                    <img src="img/rewards.jpg" alt="Sistem Insentif" class="img-fluid rounded-3 shadow-lg">
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h3 class="card-title text-success mb-4">Mekanisme Penghargaan</h3>
                            <div class="d-flex align-items-start mb-3">
                                <div class="me-3">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px;">
                                        <i class="fas fa-coins"></i>
                                    </div>
                                </div>
                                <div>
                                    <h5>Perolehan Poin</h5>
                                    <p>Setiap jenis sampah memiliki nilai poin berbeda berdasarkan berat dan kategori
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start mb-3">
                                <div class="me-3">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px;">
                                        <i class="fas fa-exchange-alt"></i>
                                    </div>
                                </div>
                                <div>
                                    <h5>Penukaran Reward</h5>
                                    <p>Poin dapat ditukar dengan uang elektronik, voucher belanja, atau donasi</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px;">
                                        <i class="fas fa-trophy"></i>
                                    </div>
                                </div>
                                <div>
                                    <h5>Program Loyalitas</h5>
                                    <p>Pengguna aktif mendapatkan status dan keuntungan tambahan</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tambahkan FAQ Section sebelum footer -->
    <section class="section bg-light">
        <div class="container">
            <h2 class="section-title mb-5">Pertanyaan Umum</h2>
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            Bagaimana cara menemukan drop point terdekat?
                        </button>
                    </h3>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Anda dapat menggunakan fitur peta pada aplikasi Trash2Cash untuk menemukan lokasi drop point
                            terdekat dari lokasi Anda.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faq2">
                            Apa saja jenis sampah yang dapat ditukarkan dengan poin?
                        </button>
                    </h3>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Jenis sampah yang dapat ditukarkan meliputi sampah plastik (botol, kemasan), sampah kertas
                            dan kardus, serta sampah logam dan kaleng.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faq3">
                            Bagaimana cara menukarkan poin yang sudah dikumpulkan?
                        </button>
                    </h3>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Poin dapat ditukarkan melalui menu "Tukar Poin" di aplikasi. Anda dapat memilih berbagai
                            opsi penukaran seperti uang elektronik, voucher belanja, atau donasi.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- JavaScript Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true
        });
    </script>

    <?php include '../includes/home_footer.php'; ?>
</body>

</html>