<?php
include 'koneksi.php';
session_start();

if (isset($_POST['submit'])) {
    $user        = $_SESSION['username']; 
    $nis         = $_SESSION['nis']; 
    $id_kategori = mysqli_real_escape_string($koneksi, $_POST['id_kategori']);
    $facility    = mysqli_real_escape_string($koneksi, $_POST['facility']);
    $place       = mysqli_real_escape_string($koneksi, $_POST['place']);
    $desc        = mysqli_real_escape_string($koneksi, $_POST['description']);
    $namaFoto    = null;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $ekstensi = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $namaFoto = time() . "_" . $user . "." . $ekstensi;
        $targetFilePath = $targetDir . $namaFoto;
        move_uploaded_file($_FILES['foto']['tmp_name'], $targetFilePath);
    }

    $query_input = "INSERT INTO input_aspirasi (nis, username, id_kategori, ket, lokasi, deskripsi, foto, tgl_laporan) 
                    VALUES ('$nis', '$user', '$id_kategori', '$facility', '$place', '$desc', '$namaFoto', NOW())";
    
    if (mysqli_query($koneksi, $query_input)) {
        $id_pelaporan = mysqli_insert_id($koneksi);

        $query_aspirasi = "INSERT INTO aspirasi (id_aspirasi, status, id_kategori) 
                           VALUES ('$id_pelaporan', 'Menunggu', '$id_kategori')";
        
        mysqli_query($koneksi, $query_aspirasi);

        header("Location: dashboard.php");
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>