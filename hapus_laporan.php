<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['crud_admin'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);

    mysqli_query($koneksi, "DELETE FROM aspirasi WHERE id_aspirasi = '$id'");
    $query = mysqli_query($koneksi, "DELETE FROM input_aspirasi WHERE id_pelaporan = '$id'");

    if ($query) {
        echo "<script>alert('Laporan berhasil dihapus!'); window.location='dashboardAdmin.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus laporan.'); window.location='dashboardAdmin.php';</script>";
    }
} else {
    header("Location: dashboardAdmin.php");
}
?>