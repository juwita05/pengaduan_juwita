<?php
session_start();
include 'koneksi.php';

// Proteksi halaman
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$user = $_SESSION['username'];
$nis  = $_SESSION['nis']; // NIS diambil dari session saat login
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengaduan Sarana</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <nav class="bg-blue-600 p-4 text-white shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">Sarana Sekolah</h1>
            <div class="flex items-center gap-4">
                <span>Halo, <b><?= htmlspecialchars($user); ?></b> (<?= $nis; ?>)</span>
                <button onclick="logout()" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg text-sm transition font-bold">Logout</button>
            </div>
        </div>
    </nav>

    <main class="container mx-auto mt-8 p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="md:col-span-1 bg-white p-6 rounded-xl shadow-md h-fit">
                <h2 class="text-lg font-semibold mb-4 text-gray-700">Buat Laporan Baru</h2>
                <form action="simpan_pengaduan.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-600">Kategori</label>
                        <select name="id_kategori" class="w-full p-2 border rounded-md focus:outline-blue-500" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php
                            $kat = mysqli_query($koneksi, "SELECT * FROM kategori");
                            while($k = mysqli_fetch_array($kat)) {
                                echo "<option value='".$k['id_kategori']."'>".$k['ket_kategori']."</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600">Sarana / Fasilitas</label>
                        <input type="text" name="facility" placeholder="Contoh: Kursi Kelas 10A" class="w-full p-2 border rounded-md focus:outline-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600">Lokasi</label>
                        <input type="text" name="place" placeholder="Contoh: Lab Komputer" class="w-full p-2 border rounded-md focus:outline-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600">Deskripsi</label>
                        <textarea name="description" rows="3" class="w-full p-2 border rounded-md focus:outline-blue-500" required></textarea>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600">Foto Bukti</label>
                        <input type="file" name="foto" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    <button type="submit" name="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-bold">
                        Kirim Laporan
                    </button>
                </form>
            </div>

            <div class="md:col-span-2 bg-white p-6 rounded-xl shadow-md">
                <h2 class="text-lg font-semibold mb-4 text-gray-700">Riwayat Pengaduan Anda</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b text-gray-500 text-sm">
                                <th class="p-2">Tanggal</th>
                                <th class="p-2">Sarana</th>
                                <th class="p-2 text-center">Foto</th>
                                <th class="p-2">Status</th>
                                <th class="p-2">Feedback</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Menggunakan JOIN sesuai skema gambar kerja
                            $query = mysqli_query($koneksi, "SELECT i.*, a.status, a.feedback 
                                                            FROM input_aspirasi i 
                                                            LEFT JOIN aspirasi a ON i.id_pelaporan = a.id_aspirasi 
                                                            WHERE i.username = '$user' 
                                                            ORDER BY i.id_pelaporan DESC");

                            if (mysqli_num_rows($query) > 0) {
                                while ($row = mysqli_fetch_array($query)) {
                                    $status = $row['status'] ?? 'Menunggu';
                                    $color = ($status == "Selesai") ? "bg-green-100 text-green-700" : (($status == "Proses") ? "bg-blue-100 text-blue-700" : "bg-yellow-100 text-yellow-700");
                            ?>
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="p-2 text-xs"><?= $row['tgl_laporan']; ?></td>
                                    <td class="p-2 text-sm font-medium">
                                        <?= htmlspecialchars($row['ket']); ?><br>
                                        <span class="text-xs text-gray-400"><?= htmlspecialchars($row['lokasi']); ?></span>
                                    </td>
                                    <td class="p-2 text-center">
                                        <?php if(!empty($row['foto'])): ?>
                                            <a href="uploads/<?= $row['foto'] ?>" target="_blank" class="text-blue-500 text-xs underline">Lihat</a>
                                        <?php else: ?>
                                            <span class="text-gray-300">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-2">
                                        <span class="<?= $color ?> px-2 py-1 rounded-full text-[10px] font-bold uppercase"><?= $status ?></span>
                                    </td>
                                    <td class="p-2 text-sm italic text-gray-500">
                                        <?= !empty($row['feedback']) ? htmlspecialchars($row['feedback']) : '-'; ?>
                                    </td>
                                </tr>
                            <?php 
                                } 
                            } else {
                                echo "<tr><td colspan='5' class='text-center py-10 text-gray-400'>Belum ada riwayat laporan.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <script>
        function logout() {
            if(confirm('Apakah Anda yakin ingin logout?')) {
                window.location.href = 'logout.php';
            }
        }
    </script>
</body>
</html>