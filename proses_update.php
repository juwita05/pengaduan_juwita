<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['crud_admin'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = mysqli_real_escape_string($koneksi, $_POST['id_laporan']);
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);
    $feedback = mysqli_real_escape_string($koneksi, $_POST['feedback']);

    $query = "UPDATE aspirasi SET 
              status = '$status', 
              feedback = '$feedback' 
              WHERE id_aspirasi = '$id'";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>
                alert('Data berhasil diperbarui!');
                window.location.href = 'dashboardAdmin.php';
              </script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
} else {
    header("Location: dashboardAdmin.php");
}
?>