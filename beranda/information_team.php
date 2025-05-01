<?php
// Path relatif yang benar ke template_engine.php
include_once '../libs/template_engine.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tim Trash2Cash</title>
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

        /* Hero Section Styling */
        .hero-section-small {
            background: linear-gradient(135deg, #28a745 0%, #208838 100%);
            padding: 80px 0 60px;
            position: relative;
            overflow: hidden;
        }

        .hero-section-small::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='rgba(255,255,255,.075)' fill-rule='evenodd'/%3E%3C/svg%3E");
            opacity: 0.4;
        }

        .hero-section-small .display-4 {
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .hero-section-small .lead {
            font-size: 1.25rem;
            font-weight: 300;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        /* Team Section Styling */
        .team-grid {
            padding: 80px 0;
            background-color: #ffffff;
        }

        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-subtitle {
            display: inline-block;
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
            padding: 6px 20px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 12px;
        }

        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 15px;
        }

        .section-header h2::after {
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

        .section-header p {
            max-width: 700px;
            margin: 0 auto;
            font-size: 1.1rem;
        }

        /* Team Member Card Styling */
        .team-member {
            background-color: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            margin-bottom: 20px;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .team-member:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }

        .member-img {
            position: relative;
            overflow: hidden;
            height: 280px;
        }

        .member-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .team-member:hover .member-img img {
            transform: scale(1.1);
        }

        .member-info {
            padding: 25px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .member-info h4 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .member-role {
            color: #28a745;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 15px;
        }

        .member-desc {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 20px;
            flex: 1;
        }

        .member-social {
            display: flex;
            gap: 10px;
        }

        .member-social a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #f8f9fa;
            color: #28a745;
            transition: all 0.3s ease;
        }

        .member-social a:hover {
            background-color: #28a745;
            color: #fff;
            transform: translateY(-3px);
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .team-grid {
                padding: 60px 0;
            }

            .section-header {
                margin-bottom: 40px;
            }

            .section-header h2 {
                font-size: 2rem;
            }
        }

        @media (max-width: 768px) {
            .hero-section-small {
                padding: 60px 0 40px;
            }

            .hero-section-small .display-4 {
                font-size: 2.2rem;
            }

            .section-header h2 {
                font-size: 1.8rem;
            }

            .section-header p {
                font-size: 1rem;
            }

            .member-img {
                height: 220px;
            }
        }

        @media (max-width: 576px) {
            .hero-section-small .display-4 {
                font-size: 1.8rem;
            }

            .hero-section-small .lead {
                font-size: 1rem;
            }

            .section-header h2 {
                font-size: 1.5rem;
            }

            .section-subtitle {
                font-size: 12px;
                padding: 4px 15px;
            }
        }
    </style>
</head>

<body>
    <?php include '../includes/home_navbar.php'; ?>

    <!-- Hero Section dengan Background Modern -->
    <div class="hero-section-small position-relative">
        <div class="container py-5 mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center text-white">
                    <h1 class="display-4 fw-bold mb-3">Tim Trash2Cash</h1>
                    <p class="lead">Mengenal lebih dekat dengan tim hebat yang berdedikasi untuk mengembangkan platform
                        pengelolaan sampah digital</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Section -->
    <section class="team-grid">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <span class="section-subtitle">PERKENALKAN</span>
                <h2 class="mb-4">Tim Pengembang Trash2Cash</h2>
                <p class="text-muted">Kami adalah mahasiswa Universitas Siliwangi jurusan Informatika yang berkolaborasi
                    untuk menciptakan solusi pengelolaan sampah berkelanjutan</p>
            </div>

            <!-- Baris Pertama - 3 Kartu -->
            <div class="row g-4 justify-content-center">
                <!-- Anggota 1 - Delely as Ketua Tim -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="team-member">
                        <div class="member-img">
                            <img src="../assets/images/team/delely.jpg" alt="Delely Rahmawati Hidayah">
                        </div>
                        <div class="member-info">
                            <h4>Delely Rahmawati Hidayah</h4>
                            <p class="member-role">Ketua Tim</p>
                            <p class="member-desc">237006006 - Memimpin dan mengkoordinasikan seluruh aspek pengembangan
                                platform Trash2Cash</p>
                            <div class="member-social">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-github"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Anggota 2 - Desti as UI/UX Designer -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="team-member">
                        <div class="member-img">
                            <img src="../assets/images/team/desti.jpg" alt="Desti Karmini Nurdatillah">
                        </div>
                        <div class="member-info">
                            <h4>Desti Karmini Nurdatillah</h4>
                            <p class="member-role">UI/UX Designer</p>
                            <p class="member-desc">237006001 - Merancang antarmuka pengguna yang intuitif dan pengalaman
                                pengguna yang optimal</p>
                            <div class="member-social">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-github"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Anggota 3 - Shinvi as System Analyst -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="team-member">
                        <div class="member-img">
                            <img src="../assets/images/team/shinvi.jpg" alt="Shinvi Nur Najmil Jannah">
                        </div>
                        <div class="member-info">
                            <h4>Shinvi Nur Najmil Jannah</h4>
                            <p class="member-role">System Analyst</p>
                            <p class="member-desc">237006008 - Menganalisis kebutuhan sistem dan merencanakan
                                pengembangan platform</p>
                            <div class="member-social">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-github"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Baris Kedua - 2 Kartu -->
            <div class="row g-4 justify-content-center mt-4">
                <!-- Anggota 4 - Rovi as Frontend Developer -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="team-member">
                        <div class="member-img">
                            <img src="../assets/images/team/rovi.jpg" alt="Rovi Fauzan">
                        </div>
                        <div class="member-info">
                            <h4>Rovi Fauzan</h4>
                            <p class="member-role">Frontend Developer</p>
                            <p class="member-desc">237006022 - Mengembangkan antarmuka web responsif dan aplikasi mobile
                                Trash2Cash</p>
                            <div class="member-social">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-github"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Anggota 5 - Khilqi as Backend Developer -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                    <div class="team-member">
                        <div class="member-img">
                            <img src="../assets/images/team/khilqi.jpg" alt="Muhamad Khilqi Alfadillah">
                        </div>
                        <div class="member-info">
                            <h4>Muhamad Khilqi Alfadillah</h4>
                            <p class="member-role">Backend Developer</p>
                            <p class="member-desc">237006036 - Mengembangkan sistem backend dan database untuk platform
                                Trash2Cash</p>
                            <div class="member-social">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-github"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/home_footer.php'; ?>

    <!-- Load JavaScript libraries at the end for better performance -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS animation library
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });
    </script>
</body>

</html>