<?php
session_start();

// Hubungkan ke file koneksi bawaan proyek
include '../config/koneksi.php';

if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== true) {
    header("Location: ../login.php");
    exit;
}

// Jika user_id kosong di session, kita cari paksa berdasarkan email aktif
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    $email_aktif = $_SESSION['email'];
    $ambil_id    = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email_aktif'");
    $data_id     = mysqli_fetch_assoc($ambil_id);
    $user_id     = array_values($data_id)[0];
} else {
    $user_id     = $_SESSION['user_id'];
}

$query_user = mysqli_query($conn, "SELECT foto_profile, nama FROM users WHERE id = '$user_id'");
$data_user = mysqli_fetch_assoc($query_user);

$foto_db = $data_user['foto_profile'];
$nama_user = $data_user['nama'];

if ($foto_db != "" && $foto_db != "default.png" && file_exists("../uploads/profil/" . $foto_db)) {
    $url_foto = "../uploads/profil/" . $foto_db;
} else {
    $url_foto = "https://ui-avatars.com/api/?name=" . urlencode($nama_user) . "&background=4CAF50&color=fff&bold=true";
}

$query = mysqli_query($conn, "
    SELECT resep.*, users.nama AS nama_penulis
    FROM favorit
    JOIN resep ON favorit.resep_id = resep.id
    JOIN users ON resep.user_id = users.id
    WHERE favorit.user_id='$user_id'
    ORDER BY favorit.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorit Saya - Limeev</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif !important;
            background-color: #FFFFFF !important;
        }

        .sidebar {
            background: linear-gradient(180deg, #FFFFFF 0%, #FFFFC5 100%) !important;
            border-right: 4px solid #ffc800 !important;
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

        /* Layout Favorit 3 Kolom */
        .favorit-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .recipe-card {
            border: 1px solid #f1f5f9;
            border-radius: 15px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            transition: 0.3s;
        }

        .recipe-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(255, 159, 67, 0.15);
        }

        .recipe-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 15px;
        }

        .user-box {
            background: #FFFFC5 !important;
            /* Warna kuning muda */
            border: 1px solid #fef3c7 !important;
            /* Border senada */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03) !important;
            padding: 5px 15px 5px 5px !important;
            border-radius: 30px !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>

</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="main">

        <div class="topbar">
            <div class="user-box" style="background: #FFFFC5 !important; border: 1px solid #fef3c7 !important; border-radius: 30px !important;">
                <img src="<?= $url_foto; ?>" style="object-fit: cover; width: 45px; height: 45px; border-radius: 50%;">
                <span><?= $_SESSION['nama']; ?></span>
            </div>
        </div>

        <div class="hero" style="padding-bottom: 20px;">
            <h1>❤️ Resep Favorit</h1>
            <p>Kumpulan resep makanan yang paling kamu sukai</p>
        </div>

        <div class="recipe-container">

            <?php
            // Pengecekan: Jika user memiliki resep favorit, tampilkan datanya
            if (mysqli_num_rows($query) > 0) {
                while ($data = mysqli_fetch_assoc($query)) {
            ?>

                    <div class="recipe-card">

                        <img src="../uploads/makanan/<?php echo $data['gambar']; ?>" alt="<?php echo $data['judul']; ?>">

                        <div class="recipe-body">
                            <h3>
                                <?php echo $data['judul']; ?>
                            </h3>

                            <p style="font-size: 13px; color: #ffc800; margin-top: -5px; margin-bottom: 10px; font-weight: 600;">
                                <i class="fa fa-user-pen"></i> Oleh: <?= $data['nama_penulis']; ?>
                            </p>

                            <p>
                                <?php echo substr($data['deskripsi'], 0, 80); ?>...
                            </p>

                            <div class="recipe-action" style="display: flex; gap: 8px; margin-top: 15px;">
                                <a href="detail_resep.php?id=<?= $data['id']; ?>&ref=favorit" class="btn-detail"
                                    style="flex: 1; text-align: center; text-decoration: none; background-color: #ffc800; color: white; padding: 8px 12px; border-radius: 6px; font-size: 14px; font-weight: 500;">
                                    Detail
                                </a>

                                <a href="../process/favorit_process.php?id=<?= $data['id']; ?>"
                                    style="flex: 1; text-align: center; background-color: #94a3b8 !important; border: 1px solid #94a3b8 !important; color: white !important; text-decoration: none; padding: 8px 12px; border-radius: 6px; font-size: 14px; font-weight: 500; transition: 0.3s;"
                                    onmouseover="this.style.setProperty('background-color', '#64748b', 'important'); this.style.setProperty('border-color', '#64748b', 'important');"
                                    onmouseout="this.style.setProperty('background-color', '#94a3b8', 'important'); this.style.setProperty('border-color', '#94a3b8', 'important');"
                                    onclick="return confirm('Keluarkan resep ini dari daftar favorit Anda?');">
                                    💔 Unfav
                                </a>
                            </div>
                        </div>

                    </div>

                <?php
                }
            } else {
                // Tampilan Cadangan yang Cantik jika User belum pernah klik tombol favorit apa pun
                ?>
                <div style="width: 100%; text-align: center; padding: 50px 0; color: #94a3b8;">
                    <i class="fa fa-heart-crack" style="font-size: 48px; margin-bottom: 15px; color: #cbd5e1;"></i>
                    <h3 style="color: #64748b; font-weight: 500;">Belum ada resep favorit</h3>
                    <p>Jelajahi resep menarik di Dashboard dan berikan tanda suka!</p>
                </div>
            <?php } ?>

        </div>

    </div>

</body>

</html>