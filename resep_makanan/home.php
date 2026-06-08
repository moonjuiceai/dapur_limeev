<?php

session_start();
include 'config/koneksi.php';

$is_logged_in = isset($_SESSION['status_login']) && $_SESSION['status_login'] === true;

if ($is_logged_in) {
    $user_id = $_SESSION['user_id'];
    $query_user = mysqli_query($conn, "SELECT foto_profile, nama FROM users WHERE id = '$user_id'");
    $data_user = mysqli_fetch_assoc($query_user);

    $foto_db = $data_user['foto_profile'];
    $nama_user = $data_user['nama'];

    if ($foto_db != "" && $foto_db != "default.png" && file_exists("uploads/profil/" . $foto_db)) {
        $url_foto = "uploads/profil/" . $foto_db;
    } else {
        $url_foto = "https://ui-avatars.com/api/?name=" . urlencode($nama_user) . "&background=fff&color=333333&bold=true";
    }
} else {
    $url_foto = "https://ui-avatars.com/api/?name=Guest&background=fff&color=333333&bold=true";
}

$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$query = mysqli_query($conn, "
    SELECT resep.*, users.nama AS nama_penulis 
    FROM resep 
    JOIN users ON resep.user_id = users.id
    WHERE resep.judul LIKE '%$search%'
    OR resep.bahan LIKE '%$search%'
    OR resep.deskripsi LIKE '%$search%'
    ORDER BY resep.id DESC
");

$user_id_aktif = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Resep - Limeev</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .sidebar {
            background: linear-gradient(180deg, #FFFFFF 0%, #FFFFC5 100%) !important;
            border-right: 4px solid #ffc800 !important;
        }

        body,
        .main {
            background-color: #FFFFFF !important;
        }

        .sidebar .menu li a.active {
            background-color: #ffc800 !important;
            color: white !important;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(255, 159, 67, 0.3);
        }

        .sidebar .menu li a:hover:not(.active) {
            background-color: rgba(255, 159, 67, 0.1) !important;
            color: #ffc800 !important;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .recipe-card {
            border: 1px solid #f1f5f9 !important;
            border-radius: 15px !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04) !important;
            transition: transform 0.3s ease, box-shadow 0.3s ease !important;
        }

        .recipe-card:hover {
            transform: translateY(-5px) !important;
            box-shadow: 0 12px 20px rgba(255, 159, 67, 0.15) !important;
        }

        .user-box {
            background: #FFFFC5;
            border: 1px solid #fef3c7;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            padding: 5px 15px 5px 5px !important;
            border-radius: 30px !important;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="logo" style="text-align: center; padding-top: 30px; margin-bottom: 5px;">
            <img src="assets/logo_limeev.png" alt="Logo Limeev" style="width: 180px; height: auto; filter: drop-shadow(0px 4px 6px rgba(255,159,67,0.2));">
        </div>

        <ul class="menu" style="padding: 0 15px; margin-top: 10px;">
            <li>
                <a href="home.php" class="active">
                    <i class="fa fa-house"></i> Home
                </a>
            </li>

            <?php if ($is_logged_in): ?>
                <li>
                    <a href="user/index.php">
                        <i class="fa fa-chart-line"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="logout.php">
                        <i class="fa fa-right-from-bracket"></i> Logout
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <a href="login.php">
                        <i class="fa fa-right-to-bracket"></i> Login
                    </a>
                </li>
                <li>
                    <a href="register.php">
                        <i class="fa fa-user-plus"></i> Register
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="main">

        <div class="topbar">
            <div class="user-box" style="display: flex; align-items: center; gap: 10px;">
                <img src="<?= $url_foto; ?>" style="object-fit: cover; width: 40px; height: 40px; border-radius: 50%;">
                <span style="font-weight: 500; font-size: 14px; color: #333;">
                    <?php echo $is_logged_in ? $_SESSION['nama'] : 'Guest'; ?>
                    <?= ($is_logged_in && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') ? '<strong style="color:#ff9f43;">(Admin)</strong>' : ''; ?>
                </span>
            </div>
        </div>

        <div class="hero" style="text-align: center; margin-bottom: 30px;">

            <h1 style="color: #1e293b; display: flex; align-items: center; justify-content: center; gap: 10px;">
                Dapur Limeev
                <img src="assets/lemon_slice.png" alt="Lemon Slice" style="width: 45px; height: auto;">
            </h1>

            <p style="color: #64748b; margin-bottom: 25px;">Temukan dan bagikan resep favoritmu</p>

            <div class="search-box" style="display: flex; width: 100%;">
                <form method="GET" id="searchForm" style="display: flex; align-items: center; background-color: #FFFFC5; border-radius: 50px; padding: 6px; width: 100%; box-shadow: 0 4px 10px rgba(0,0,0,0.03);">
                    <i class="fa fa-search" style="color: #FFC800; padding-left: 18px; font-size: 16px;"></i>

                    <input type="text" id="searchInput" name="search" placeholder="Cari resep makanan..." value="<?php echo $search; ?>"
                        style="background: transparent; border: none; outline: none; padding: 12px 15px; flex: 1; font-size: 15px; color: #333;">

                    <button type="submit"
                        style="background-color: #FFC800; color: #F5EBE1; border-radius: 50px; border: none; padding: 10px 35px; font-weight: 600; font-size: 14px; cursor: pointer; transition: 0.3s;"
                        onmouseover="this.style.backgroundColor='#FFA500';"
                        onmouseout="this.style.backgroundColor='#FFC800';">
                        Search
                    </button>
                </form>
            </div>

        </div>

        <div class="recipe-container">
            <?php while ($data = mysqli_fetch_assoc($query)) { ?>
                <div class="recipe-card">
                    <img src="uploads/makanan/<?php echo $data['gambar']; ?>" alt="<?php echo $data['judul']; ?>" style="border-radius: 15px 15px 0 0;">

                    <div class="recipe-body">
                        <h3 style="color: #1e293b;">
                            <?php echo $data['judul']; ?>
                        </h3>

                        <p style="font-size: 13px; color: #ffc800; margin-top: -5px; margin-bottom: 10px; font-weight: 600;">
                            <i class="fa fa-user-pen"></i> Oleh: <?= $data['nama_penulis']; ?>
                        </p>

                        <p style="color: #64748b;">
                            <?php echo substr($data['deskripsi'], 0, 80); ?>...
                        </p>

                        <div class="recipe-info">
                            <span>
                                ⏱ <?php echo isset($data['waktu_memasak']) ? $data['waktu_memasak'] : '0'; ?> menit
                            </span>
                            <span>
                                🍽 <?php echo isset($data['porsi']) ? $data['porsi'] : '0'; ?> porsi
                            </span>
                        </div>

                        <div class="recipe-action" style="display: flex; gap: 8px; margin-top: 15px;">

                            <?php

                            $link_detail = $is_logged_in ? "user/detail_resep.php?id=" . $data['id'] . "&ref=home" : "detail_public.php?id=" . $data['id'];
                            ?>

                            <a class="detail-btn" href="<?= $link_detail; ?>"
                                style="flex: 1; text-align: center; text-decoration: none; border-radius: 8px; background-color: #FFC800 !important; color: white !important; padding: 10px 15px; font-weight: 600; font-size: 14px; transition: 0.3s;"
                                onmouseover="this.style.setProperty('background-color', '#FFA500', 'important');"
                                onmouseout="this.style.setProperty('background-color', '#FFC800', 'important');">
                                Detail
                            </a>

                            <?php
                            if ($is_logged_in && $user_id_aktif > 0) {
                                $id_resep_ini = $data['id'];
                                $cek_fav = mysqli_query($conn, "SELECT id FROM favorit WHERE user_id = '$user_id_aktif' AND resep_id = '$id_resep_ini'");

                                if (mysqli_num_rows($cek_fav) > 0) {
                            ?>
                                    <a href="process/favorit_process.php?id=<?= $data['id']; ?>" class="detail-btn"
                                        style="background-color: #94a3b8 !important; border: 1px solid #94a3b8 !important; color: white !important; text-decoration: none; padding: 10px 15px; font-size: 14px; transition: 0.3s; border-radius: 8px;"
                                        onmouseover="this.style.setProperty('background-color', '#64748b', 'important');"
                                        onmouseout="this.style.setProperty('background-color', '#94a3b8', 'important');">
                                        💔
                                    </a>
                                <?php } else { ?>
                                    <a href="process/favorit_process.php?id=<?= $data['id']; ?>" class="detail-btn"
                                        style="background-color: #ff66b2 !important; border: 1px solid #ff66b2 !important; color: white !important; text-decoration: none; padding: 10px 15px; font-size: 14px; transition: 0.3s; border-radius: 8px;"
                                        onmouseover="this.style.setProperty('background-color', '#ff3399', 'important');"
                                        onmouseout="this.style.setProperty('background-color', '#ff66b2', 'important');">
                                        💖
                                    </a>
                                <?php }
                            } else {
                                ?>
                                <a href="login.php" class="detail-btn"
                                    style="background-color: #ff66b2 !important; border: 1px solid #ff66b2 !important; color: white !important; text-decoration: none; padding: 10px 15px; font-size: 14px; transition: 0.3s; border-radius: 8px;"
                                    onmouseover="this.style.setProperty('background-color', '#ff3399', 'important');"
                                    onmouseout="this.style.setProperty('background-color', '#ff66b2', 'important');"
                                    onclick="return confirm('Silakan login terlebih dahulu untuk menyimpan resep favorit!');">
                                    💖
                                </a>
                            <?php } ?>

                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

    </div>

    <script>
        document.getElementById("searchInput").addEventListener("keyup", function() {

            let filter = this.value.toLowerCase();

            let cards = document.querySelectorAll(".recipe-card");

            cards.forEach(function(card) {

                let judul = card.querySelector("h3").textContent.toLowerCase();

                if (judul.includes(filter)) {
                    card.style.display = "block";
                } else {
                    card.style.display = "none";
                }

            });

        });
    </script>

</body>

</html>