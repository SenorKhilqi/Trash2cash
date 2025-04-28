<?php
// Konfigurasi untuk pengiriman email
return [
    'smtp_host' => 'smtp.gmail.com', // Ganti dengan SMTP host yang Anda gunakan
    'smtp_port' => 587,              // Port SMTP
    'smtp_username' => 'your_email@gmail.com', // Ganti dengan email Anda
    'smtp_password' => 'your_app_password',    // Ganti dengan password/app password
    'smtp_encryption' => 'tls',      // Enkripsi (tls atau ssl)
    'from_email' => 'your_email@gmail.com',    // Email pengirim
    'from_name' => 'Trash2Cash',     // Nama pengirim
    'verification_subject' => 'Verifikasi Email - Trash2Cash',   // Subject email verifikasi
];
?>