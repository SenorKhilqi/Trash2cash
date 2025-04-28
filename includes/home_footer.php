<?php
// home_footer.php - Footer for home/landing pages
?>

<footer class="bg-dark text-white py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <h5 class="mb-3">Trash2Cash</h5>
                <p class="mb-3">Platform pengelolaan sampah berbasis digital dengan insentif untuk mendorong partisipasi masyarakat dalam menjaga lingkungan.</p>
                <div class="d-flex gap-3">
                    <a href="https://facebook.com" target="_blank" class="text-white"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://twitter.com" target="_blank" class="text-white"><i class="fab fa-twitter"></i></a>
                    <a href="https://instagram.com" target="_blank" class="text-white"><i class="fab fa-instagram"></i></a>
                    <a href="https://youtube.com" target="_blank" class="text-white"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="col-md-2 mb-4 mb-md-0">
                <h5 class="mb-3">Tautan</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?php echo dirname($_SERVER['PHP_SELF']) == '/beranda' ? '../public/index.php' : 'index.php'; ?>" class="text-white text-decoration-none">Beranda</a></li>
                    <li class="mb-2"><a href="<?php echo dirname($_SERVER['PHP_SELF']) == '/beranda' ? 'information_about.php' : '../beranda/information_about.php'; ?>" class="text-white text-decoration-none">Tentang Kami</a></li>
                    <li class="mb-2"><a href="<?php echo dirname($_SERVER['PHP_SELF']) == '/beranda' ? 'information_metodologi.php' : '../beranda/information_metodologi.php'; ?>" class="text-white text-decoration-none">Metodologi</a></li>
                    <li class="mb-2"><a href="<?php echo dirname($_SERVER['PHP_SELF']) == '/beranda' ? 'information_team.php' : '../beranda/information_team.php'; ?>" class="text-white text-decoration-none">Tim</a></li>
                    <li class="mb-2"><a href="<?php echo dirname($_SERVER['PHP_SELF']) == '/beranda' ? 'kontak.php' : '../beranda/kontak.php'; ?>" class="text-white text-decoration-none">Kontak</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-4 mb-md-0">
                <h5 class="mb-3">Lokasi Drop Point</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?php echo dirname($_SERVER['PHP_SELF']) == '/beranda' ? 'kontak.php' : '../beranda/kontak.php'; ?>#drop-points" class="text-white text-decoration-none">Kota Bersih - Jl. Raya Utama No. 45</a></li>
                    <li class="mb-2"><a href="<?php echo dirname($_SERVER['PHP_SELF']) == '/beranda' ? 'kontak.php' : '../beranda/kontak.php'; ?>#drop-points" class="text-white text-decoration-none">Kota Bersih - Jl. Hijau Asri No. 78</a></li>
                    <li class="mb-2"><a href="<?php echo dirname($_SERVER['PHP_SELF']) == '/beranda' ? 'kontak.php' : '../beranda/kontak.php'; ?>#drop-points" class="text-white text-decoration-none">Kota Bersih - Jl. Damai Sejahtera No. 12</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h5 class="mb-3">Hubungi Kami</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> <a href="<?php echo dirname($_SERVER['PHP_SELF']) == '/beranda' ? 'kontak.php' : '../beranda/kontak.php'; ?>#map" class="text-white">Jl. Lingkungan Hijau No. 123, Kota Bersih</a></li>
                    <li class="mb-2"><i class="fas fa-phone me-2"></i> <a href="tel:+62211234567" class="text-white">(021) 123-4567</a></li>
                    <li class="mb-2"><i class="fas fa-envelope me-2"></i> <a href="mailto:info@trash2cash.com" class="text-white">info@trash2cash.com</a></li>
                    <li class="mb-2"><i class="fab fa-whatsapp me-2"></i> <a href="https://wa.me/6285767124678" target="_blank" class="text-white">+62 857-671-24678</a></li>
                </ul>
            </div>
        </div>
        <hr class="my-4">
        <div class="text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Senor D Kilki</p>
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Trash2Cash. All Rights Reserved.</p>
        </div>
    </div>
</footer>