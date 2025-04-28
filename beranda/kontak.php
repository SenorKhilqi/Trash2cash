<?php
// Path relatif yang benar ke template_engine.php
include_once '../libs/template_engine.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak - Trash2Cash</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Reset CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }
        
        a {
            text-decoration: none;
            color: #4CAF50;
        }
        
        a:hover {
            text-decoration: underline;
        }
        
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 15px;
        }
        
        h1 {
            font-size: 2.5rem;
            margin-bottom: 30px;
            color: #333;
            text-align: center;
            position: relative;
            padding-bottom: 15px;
        }
        
        h1:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: #4CAF50;
        }
        
        h2 {
            font-size: 2rem;
            color: #4CAF50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }
        
        h3 {
            font-size: 1.5rem;
            color: #4CAF50;
            margin-bottom: 15px;
        }
        
        p {
            margin-bottom: 20px;
        }
        
        section {
            margin-top: 40px;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        /* Contact Section - Side by Side Layout */
        .contact-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }
        
        .contact-info-container {
            flex: 1;
            min-width: 300px;
        }
        
        .contact-form-container {
            flex: 1;
            min-width: 300px;
        }
        
        /* Contact Info Cards */
        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .contact-card {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        }
        
        .contact-card h3 {
            color: #4CAF50;
            display: flex;
            align-items: center;
        }
        
        .contact-card i {
            margin-right: 10px;
        }
        
        /* Form Styles */
        .contact-form {
            background-color: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-group textarea {
            height: 150px;
            resize: vertical;
        }
        
        .btn-submit {
            background-color: #25d366;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-submit i {
            margin-right: 10px;
        }
        
        .btn-submit:hover {
            background-color: #128C7E;
        }
        
        /* Map Container */
        .map-container {
            margin-top: 20px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            height: 400px;
        }
        
        /* Drop Points */
        .drop-points {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }
        
        .drop-point-card {
            flex: 1;
            min-width: 300px;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        }
        
        .drop-point-card h3 {
            color: #4CAF50;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }
            
            h2 {
                font-size: 1.7rem;
            }
            
            h3 {
                font-size: 1.3rem;
            }
            
            section {
                padding: 20px;
            }
            
            .contact-info-container,
            .contact-form-container,
            .drop-point-card {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/home_navbar.php'; ?>

    <div class="container">
        <h1>Hubungi Kami</h1>
        
        <section>
            <h2>Kirim Pesan</h2>
            
            <div class="contact-container">
                <!-- Formulir Kontak (Kiri) -->
                <div class="contact-form-container">
                    <h3>Formulir Kontak</h3>
                    <form id="whatsappForm" class="contact-form">
                        <div class="form-group">
                            <label for="nama">Nama Lengkap</label>
                            <input type="text" id="nama" name="nama" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="subjek">Subjek</label>
                            <input type="text" id="subjek" name="subjek" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="pesan">Pesan</label>
                            <textarea id="pesan" name="pesan" required></textarea>
                        </div>
                        
                        <button type="button" onclick="sendWhatsApp()" class="btn-submit">
                            <i class="fab fa-whatsapp"></i> Kirim via WhatsApp
                        </button>
                    </form>
                </div>
                
                <!-- Informasi Kontak (Kanan) -->
                <div class="contact-info-container">
                    <h3>Informasi Kontak</h3>
                    <div class="contact-info">
                        <div class="contact-card">
                            <h3><i class="fas fa-map-marker-alt"></i> Alamat Kantor</h3>
                            <p>Jl. Lingkungan Hijau No. 123<br>
                               Kota Bersih, 12345<br>
                               Indonesia</p>
                        </div>
                        
                        <div class="contact-card">
                            <h3><i class="fas fa-phone-alt"></i> Kontak</h3>
                            <p><strong>Email:</strong> info@trash2cash.com<br>
                               <strong>Telepon:</strong> (021) 123-4567<br>
                               <strong>WhatsApp:</strong> +62 895-0689-2023</p>
                        </div>
                        
                        <div class="contact-card">
                            <h3><i class="fas fa-clock"></i> Jam Operasional</h3>
                            <p><strong>Senin - Jumat:</strong> 09.00 - 17.00<br>
                               <strong>Sabtu:</strong> 09.00 - 15.00<br>
                               <strong>Minggu:</strong> Tutup</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <section>
            <h2>Lokasi Kami</h2>
            <div class="map-container">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126438.53095662263!2d108.1571!3d-7.3274!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6f575ac931e417%3A0x5c2c7c09ab898fa1!2sTaskimalaya%2C%20Tasikmalaya%20City%2C%20West%20Java!5e0!3m2!1sen!2sid!4v1629856438974!5m2!1sen!2sid"
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </div>
        </section>
        
        <section>
            <h2>Drop Points</h2>
            <div class="drop-points">
                <div class="drop-point-card">
                    <h3>Drop Point A</h3>
                    <p><strong>Alamat:</strong> Jl. Raya Utama No. 45, Kota Bersih<br>
                       <strong>Kontak:</strong> 0812-1111-2222<br>
                       <strong>Jam Buka:</strong> 08.00 - 18.00</p>
                </div>
                
                <div class="drop-point-card">
                    <h3>Drop Point B</h3>
                    <p><strong>Alamat:</strong> Jl. Hijau Asri No. 78, Kota Bersih<br>
                       <strong>Kontak:</strong> 0812-3333-4444<br>
                       <strong>Jam Buka:</strong> 09.00 - 17.00</p>
                </div>
                
                <div class="drop-point-card">
                    <h3>Drop Point C</h3>
                    <p><strong>Alamat:</strong> Jl. Damai Sejahtera No. 12, Kota Bersih<br>
                       <strong>Kontak:</strong> 0812-5555-6666<br>
                       <strong>Jam Buka:</strong> 08.00 - 16.00</p>
                </div>
            </div>
        </section>
    </div>

    <script>
        function sendWhatsApp() {
            // Get form values
            const nama = document.getElementById('nama').value;
            const email = document.getElementById('email').value;
            const subjek = document.getElementById('subjek').value;
            const pesan = document.getElementById('pesan').value;
            
            // Check if all fields are filled
            if (!nama || !email || !subjek || !pesan) {
                alert('Mohon lengkapi semua field formulir');
                return;
            }
            
            // Format the message for WhatsApp
            const formattedMessage = 
                `*Pesan Dari Website Trash2Cash*%0A%0A` +
                `*Nama:* ${nama}%0A` +
                `*Email:* ${email}%0A` +
                `*Subjek:* ${subjek}%0A%0A` +
                `*Pesan:*%0A${pesan}`;
            
            // WhatsApp phone number (with country code)
            const phoneNumber = "6289506892023";
            
            // Create WhatsApp URL
            const whatsappURL = `https://wa.me/${phoneNumber}?text=${formattedMessage}`;
            
            // Open WhatsApp in a new tab
            window.open(whatsappURL, '_blank');
        }
    </script>

    <?php include '../includes/home_footer.php'; ?>
</body>
</html>
