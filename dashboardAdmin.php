<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['crud_admin'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Pengaduan Sarana</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <nav class="bg-blue-600 p-4 text-white shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">Panel Admin</h1>
            <div class="flex items-center gap-4">
                <span>Halo, Admin (<?= htmlspecialchars($_SESSION['crud_admin']); ?>)</span>
                <button onclick="logout()" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg text-sm transition font-bold">
                    Logout
                </button>
            </div>
        </div>
    </nav>

    <main class="container mx-auto mt-8 p-4 space-y-10">
        
        <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-yellow-500">
            <h2 class="text-lg font-bold mb-4 text-gray-700 flex items-center gap-2">
                <span>⏳</span> Laporan Perlu Tindakan
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b text-gray-500 text-sm">
                            <th class="p-2">Tanggal</th>
                            <th class="p-2">NIS/Pengirim</th> 
                            <th class="p-2">Sarana / Lokasi</th>
                            <th class="p-2 text-center">Bukti</th>
                            <th class="p-2">Status</th>
                            <th class="p-2">Feedback Admin</th>
                            <th class="p-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // JOIN sesuai skema gambar kerja: input_aspirasi ke aspirasi
                        $query_aktif = mysqli_query($koneksi, "SELECT i.*, a.status, a.feedback 
                                                              FROM input_aspirasi i 
                                                              JOIN aspirasi a ON i.id_pelaporan = a.id_aspirasi 
                                                              WHERE a.status != 'Selesai' 
                                                              ORDER BY i.id_pelaporan DESC");
                        
                        if (mysqli_num_rows($query_aktif) == 0) {
                            echo "<tr><td colspan='7' class='text-center py-10 text-gray-400 font-medium'>Semua laporan sudah dikerjakan.</td></tr>";
                        }

                        while ($row = mysqli_fetch_array($query_aktif)) {
                        ?>
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="p-2 text-sm whitespace-nowrap"><?= $row['tgl_laporan']; ?></td>
                            <td class="p-2 text-sm">
                                <span class="font-semibold text-blue-600">@<?= htmlspecialchars($row['username']); ?></span><br>
                                <span class="text-[10px] text-gray-400">NIS: <?= $row['nis']; ?></span>
                            </td>
                            <td class="p-2 text-sm">
                                <span class="block font-medium"><?= htmlspecialchars($row['ket']); ?></span>
                                <span class="text-xs text-gray-400"><?= htmlspecialchars($row['lokasi']); ?></span>
                            </td>
                            <td class="p-2 text-center">
                                <?php if (!empty($row['foto'])): ?>
                                    <a href="uploads/<?= $row['foto']; ?>" target="_blank" class="text-blue-500 text-xs font-bold underline">🖼️ Lihat</a>
                                <?php else: ?>
                                    <span class="text-gray-300 text-xs">-</span>
                                <?php endif; ?>
                            </td>
                            <form action="proses_update.php" method="POST">
                                <input type="hidden" name="id_laporan" value="<?= $row['id_pelaporan']; ?>">
                                <td class="p-2 text-sm">
                                    <select name="status" class="p-1 border rounded text-xs font-bold focus:outline-blue-500 <?= ($row['status'] == 'Proses') ? 'bg-blue-50 text-blue-700' : 'bg-yellow-50 text-yellow-700'; ?>">
                                        <option value="Menunggu" <?= ($row['status'] == 'Menunggu') ? 'selected' : ''; ?>>🟡 Menunggu</option>
                                        <option value="Proses" <?= ($row['status'] == 'Proses') ? 'selected' : ''; ?>>🔵 Proses</option>
                                        <option value="Selesai">🟢 Selesai</option>
                                    </select>
                                </td>
                                <td class="p-2 text-sm">
                                    <textarea name="feedback" rows="1" class="w-full p-2 border rounded text-xs focus:outline-blue-500" placeholder="Beri tanggapan..."><?= htmlspecialchars($row['feedback'] ?? ''); ?></textarea>
                                </td>
                                <td class="p-2 text-sm text-center">
                                    <div class="flex gap-1 justify-center">
                                        <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded text-xs font-bold hover:bg-green-700">Simpan</button>
                                        <a href="hapus_laporan.php?id=<?= $row['id_pelaporan']; ?>" onclick="return confirm('Hapus?')" class="bg-red-500 text-white px-3 py-1 rounded text-xs font-bold hover:bg-red-600">Hapus</a>
                                    </div>
                                </td>
                            </form>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-gray-50 p-6 rounded-xl shadow-inner border border-gray-200">
            <h2 class="text-lg font-bold mb-4 text-green-700 flex items-center gap-2">
                <span>✅</span> Arsip Laporan Selesai
            </h2>
            <div class="overflow-x-auto opacity-90">
                <table class="w-full text-left border-collapse bg-white rounded-lg">
                    <thead>
                        <tr class="bg-green-50 border-b text-green-800 text-sm">
                            <th class="p-3">Tanggal</th>
                            <th class="p-3">NIS/Pengirim</th>
                            <th class="p-3">Sarana / Lokasi</th>
                            <th class="p-3 text-center">Bukti</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3">Feedback Admin</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query_selesai = mysqli_query($koneksi, "SELECT i.*, a.status, a.feedback 
                                                                FROM input_aspirasi i 
                                                                JOIN aspirasi a ON i.id_pelaporan = a.id_aspirasi 
                                                                WHERE a.status = 'Selesai' 
                                                                ORDER BY i.id_pelaporan DESC");
                        
                        while ($row = mysqli_fetch_array($query_selesai)) {
                        ?>
                        <tr class="border-b text-gray-500 italic">
                            <td class="p-3 text-xs"><?= $row['tgl_laporan']; ?></td>
                            <td class="p-3 text-sm">@<?= htmlspecialchars($row['username']); ?></td>
                            <td class="p-3 text-sm">
                                <b><?= htmlspecialchars($row['ket']); ?></b><br>
                                <span class="text-xs"><?= htmlspecialchars($row['lokasi']); ?></span>
                            </td>
                            <td class="p-3 text-center text-xs">
                                <?php if (!empty($row['foto'])): ?>
                                    <a href="uploads/<?= $row['foto']; ?>" target="_blank" class="text-blue-400 underline">Lihat Foto</a>
                                <?php else: ?> - <?php endif; ?>
                            </td>
                            <td class="p-3 text-center">
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase not-italic">Selesai</span>
                            </td>
                            <td class="p-3 text-xs max-w-xs break-words">
                                <?= !empty($row['feedback']) ? htmlspecialchars($row['feedback']) : '-'; ?>
                            </td>
                            <td class="p-3 text-center">
                                <a href="hapus_laporan.php?id=<?= $row['id_pelaporan']; ?>" onclick="return confirm('Hapus?')" class="text-red-400 hover:text-red-600 text-xs font-bold underline not-italic">Hapus</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
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