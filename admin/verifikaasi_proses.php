<?php
require_once '../config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status_verifikasi'];

    $query = "UPDATE laporan_sampah SET status_verifikasi = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
}

header("Location: verifikasi_laporan.php");
exit();
