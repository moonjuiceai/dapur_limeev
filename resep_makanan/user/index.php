<?php
session_start();
include '../config/koneksi.php';

// 1. Perbaikan: Cek Login yang benar (menghapus variabel $cek_login yang error)
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== true) {
    header("Location: ../login.php");
    exit;
}

$user_id_aktif = $_SESSION['user_id'];

// Ambil data user untuk profil
$query_user = mysqli_query($conn, "SELECT foto_profile, nama FROM users WHERE id = '$user_id_aktif'");
$data_user = mysqli_fetch_assoc($query_user);
$url_foto = ($data_user['foto_profile'] != "" && file_exists("../uploads/profil/" . $data_user['foto_profile']))
    ? "../uploads/profil/" . $data_user['foto_profile']
    : "https://ui-avatars.com/api/?name=" . urlencode($data_user['nama']) . "&background=FFC800&color=fff&bold=true";

// 2. Pencarian & Query Resep (Hanya resep milik user login)
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : "";
$query = mysqli_query($conn, "
    SELECT resep.*, users.nama AS nama_penulis 
    FROM resep 
    JOIN users ON resep.user_id = users.id 
    WHERE resep.user_id = '$user_id_aktif' 
    AND (resep.judul LIKE '%$search%' OR resep.bahan LIKE '%$search%' OR resep.deskripsi LIKE '%$search%') 
    ORDER BY resep.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Dapur Limeev</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow-y: auto;
            background: #FFFFFF;
        }

        body {
            background: #FFFFFF;
        }

        .sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;

            background: linear-gradient(180deg, #FFFFFF 0%, #FFFFC5 100%) !important;
            border-right: 4px solid #ffc800 !important;

            padding: 30px 20px;

            overflow-y: scroll !important;
            overflow-x: hidden;
        }

        .sidebar .menu li a.active {
            background-color: #ffc800 !important;
            color: white !important;
            border-radius: 12px;
        }

        .sidebar .menu li a:hover:not(.active) {
            background-color: rgba(255, 159, 67, 0.1) !important;
            color: #ffc800 !important;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .recipe-card {
            border: 1px solid #f1f5f9;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            transition: 0.3s;
            padding: 15px;
        }

        .recipe-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(255, 159, 67, 0.15);
        }

        .user-box {
            background: #FFFFC5;
            padding: 5px 15px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Perbaikan: Menggabungkan style agar rapi dan memaksa 3 kolom */
        .recipe-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            padding: 20px;
        }

        .recipe-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 15px;
        }

        .menu {
            list-style: none;
            padding: 0 15px;
            padding-bottom: 150px;
        }

        .sidebar ul {
            min-height: max-content;
        }
    </style>
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="main">
        <div class="topbar" style="display: flex; justify-content: flex-end; padding: 20px;">
            <div class="user-box">
                <img src="<?= $url_foto; ?>" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                <span><?= $_SESSION['nama']; ?></span>
            </div>
        </div>

        <div class="search-box" style="width: 100%; max-width: 600px; margin: 20px auto; background: #FFFFC5; padding: 8px; border-radius: 50px; display: flex; box-shadow: 0 4px 10px rgba(0,0,0,0.03);">
            <form method="GET" style="display: flex; width: 100%; align-items: center;">
                <i class="fa fa-search" style="color: #FFC800; padding-left: 20px;"></i>
                <input type="text" name="search" placeholder="Cari resep milik saya..."
                    value="<?= htmlspecialchars($search); ?>"
                    style="background: transparent; border: none; padding: 10px 15px; flex: 1; outline: none; color: #333;">
                <button type="submit" style="background:#FFC800; color:white; border:none; padding:10px 30px; border-radius:50px; cursor:pointer; font-weight:600;">Search</button>
            </form>
        </div>

        <div class="recipe-container">
            <?php while ($data = mysqli_fetch_assoc($query)) { ?>
                <div class="recipe-card">
                    <img src="../uploads/makanan/<?php echo $data['gambar']; ?>">
                    <h3><?php echo $data['judul']; ?></h3>
                    <p style="font-size: 13px; color: #ffc800; margin: -5px 0 10px 0; font-weight: 600;">
                        <i class="fa fa-user-pen"></i> Oleh: <?= $data['nama_penulis']; ?>
                    </p>
                    <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">
                        <?php echo substr($data['deskripsi'], 0, 50); ?>...
                    </p>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 25px; color: #666; font-size: 13px;">
                        <span>⏱ <?php echo isset($data['waktu_memasak']) ? $data['waktu_memasak'] : '0'; ?> menit</span>
                        <span>🍽 <?php echo isset($data['porsi']) ? $data['porsi'] : '0'; ?> porsi</span>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <a href="detail_resep.php?id=<?= $data['id']; ?>" style="flex: 2; text-align:center; padding: 10px; border-radius: 12px; background: #FFC800; color: white; text-decoration:none;">Detail</a>
                        <a href="edit_resep.php?id=<?= $data['id']; ?>" style="flex: 1; text-align:center; padding: 10px; border-radius: 12px; background: #3b82f6; color: white;"><i class="fa fa-edit"></i></a>
                        <a href="../process/hapus_resep.php?id=<?= $data['id']; ?>" style="flex: 1; text-align:center; padding: 10px; border-radius: 12px; background: #ef4444; color: white;" onclick="return confirm('Hapus?')"><i class="fa fa-trash"></i></a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</body>

</html>