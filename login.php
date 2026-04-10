<?php
include 'koneksi.php';
session_start();

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $querySiswa = mysqli_query($koneksi, "SELECT * FROM siswa WHERE username='$username' AND password='$password'");
    
    $queryAdmin = mysqli_query($koneksi, "SELECT * FROM admin WHERE username='$username' AND password='$password'");

    if (mysqli_num_rows($querySiswa) > 0) {
        $dataSiswa = mysqli_fetch_array($querySiswa);
        $_SESSION['username'] = $dataSiswa['username'];
        $_SESSION['nis'] = $dataSiswa['nis'];
        session_write_close();
        header("Location: dashboard.php");
        exit();
    } else if (mysqli_num_rows($queryAdmin) > 0) {
        $dataAdmin = mysqli_fetch_array($queryAdmin);
        $_SESSION['crud_admin'] = $dataAdmin['username'];
        session_write_close();
        header("Location: dashboardAdmin.php");
        exit();
    } else {
        $error = "Username atau Password Salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pengaduan Sekolah</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-sm text-center">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Login</h2>
        
        <?php if(isset($error)): ?>
            <div class="bg-red-100 text-red-700 p-2 rounded mb-4 text-sm">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <input type="text" name="username" placeholder="Username" 
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-blue-500" required>
            </div>
            <div>
                <input type="password" name="password" placeholder="Password" 
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-blue-500" required>
            </div>
            <button type="submit" name="login" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition duration-300">
                Masuk
            </button>
        </form>
    </div>
</body>
</html>