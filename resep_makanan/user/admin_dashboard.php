<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== true) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>
            alert('Akses Ditolak! 🛑 Anda tidak memiliki izin untuk masuk ke halaman Admin.'); 
            window.location='index.php';
          </script>";
    exit;
}

$id_user_login = $_SESSION['user_id'];
$query_user = mysqli_query($conn, "SELECT foto_profile, nama FROM users WHERE id = '$id_user_login'");
$data_user = mysqli_fetch_assoc($query_user);

$foto_db = $data_user['foto_profile'];
$nama_user = $data_user['nama'];

if ($foto_db != "" && $foto_db != "default.png" && file_exists("../uploads/profil/" . $foto_db)) {
    $url_foto = "../uploads/profil/" . $foto_db;
} else {
    $url_foto = "https://ui-avatars.com/api/?name=" . urlencode($nama_user) . "&background=4CAF50&color=fff&bold=true";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Limeev</title>
    
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        .admin-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-align: center;
            height: 100%;
            transition: all 0.3s ease; */
        }

        .admin-card-resep:hover {
            background-color: #f0fdf4; 
            transform: translateY(-5px); 
            box-shadow: 0 8px 20px rgba(76, 175, 80, 0.15); 
        }
        .admin-card-resep:active {
            transform: translateY(0); 
        }

        .admin-card-user:hover {
            background-color: #fff7ed; 
            transform: translateY(-5px); 
            box-shadow: 0 8px 20px rgba(255, 159, 67, 0.15); 
        }
        .admin-card-user:active {
            transform: translateY(0); 
        }
    </style>
</head>
<body>

<div class="sidebar">
    <ul class="menu">
        <li><a href="../home.php"><i class="fa fa-house"></i> Home</a></li>
        <li><a href="index.php"><i class="fa fa-chart-line"></i> Dashboard Biasa</a></li>
        <li><a href="tambah_resep.php"><i class="fa fa-plus"></i> Tambah Resep</a></li>
        <li><a href="favorit.php"><i class="fa fa-heart"></i> Favorit</a></li>
        
        <li style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
            <a href="admin_dashboard.php" class="active" style="font-weight: 600;">
                <i class="fa fa-user-shield"></i> Admin Panel
            </a>
        </li>

        <li><a href="../logout.php"><i class="fa fa-right-from-bracket"></i> Logout</a></li>
    </ul>
</div>

<div class="main">

    <div class="topbar">
        <div class="user-box">
            <img src="<?= $url_foto; ?>" style="object-fit: cover; width: 45px; height: 45px; border-radius: 50%;">
            <span>
                <?= isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Guest'; ?> <strong style="color:#ff9f43;">(Admin)</strong>
            </span>
        </div>
    </div>

    <div class="hero">
        <h1>🛡️ Ruang Kendali Admin</h1>
        <p>Selamat datang, Penguasa Dapur Limeev! Kelola seluruh data website di sini.</p>
    </div>

    <div style="display: flex; gap: 20px; margin-bottom: 30px;">
        
        <a href="admin_kelola_resep.php" style="flex: 1; text-decoration: none; color: inherit;">
            <div class="admin-card admin-card-resep">
                <i class="fa fa-utensils" style="font-size: 50px; color: #4CAF50; margin-bottom: 20px;"></i>
                <h3 style="color: #333; font-size: 26px;">Kelola Resep</h3>
                <p style="color: #777; font-size: 15px; margin-top: 10px;">Hapus atau edit resep milik semua user.</p>
            </div>
        </a>

        <a href="admin_kelola_user.php" style="flex: 1; text-decoration: none; color: inherit;">
            <div class="admin-card admin-card-user">
                <i class="fa fa-users" style="font-size: 50px; color: #ff9f43; margin-bottom: 20px;"></i>
                <h3 style="color: #333; font-size: 26px;">Kelola User</h3>
                <p style="color: #777; font-size: 15px; margin-top: 10px;">Lihat semua akun atau hapus akun bermasalah.</p>
            </div>
        </a>

    </div>

</div>

</body>
</html>