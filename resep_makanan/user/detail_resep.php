<?php
session_start();

$ref = isset($_GET['ref']) ? $_GET['ref'] : 'index';

if ($ref == 'home') {
    $link_kembali = '../home.php'; // Kembali ke Home (karena posisi detail_resep di dalam folder user/)
} elseif ($ref == 'favorit') {
    $link_kembali = 'favorit.php';
} elseif ($ref == 'admin') {
    $link_kembali = 'admin_kelola_resep.php';
} else {
    $link_kembali = 'index.php'; // Default ke Dashboard
}

include '../config/koneksi.php';

if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== true) {
    header("Location: ../login.php");
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

if (isset($_POST['kirim_komentar'])) {
    $isi_komentar = mysqli_real_escape_string($conn, $_POST['isi_komentar']);
    $id_resep_komen = $_GET['id'];
    $id_user_komen = $_SESSION['user_id'];

    // Sudah pakai nama kolom 'komentar' yang benar
    $simpan = mysqli_query($conn, "INSERT INTO komentar (resep_id, user_id, komentar) VALUES ('$id_resep_komen', '$id_user_komen', '$isi_komentar')");

    if ($simpan) {
        header("Location: detail_resep.php?id=$id_resep_komen");
        exit;
    }
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    header("Location: index.php");
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM resep WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Resep tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Resep - <?php echo $data['judul']; ?></title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/detail_resep.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif !important;
            background-color: #FFFFFF !important;
        }

        /* Tombol Kembali */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 20px;
            background-color: #FFFFC5;
            color: #FFC800;
            border: 1px solid #FFC800;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            margin: 20px;
        }

        .btn-back:hover {
            background-color: #FFC800;
            color: white;
            border-color: #FFC800;
            transition: all 0.3s ease;
        }

        .sidebar .menu li a:hover:not(.active) {
            background-color: rgba(255, 159, 67, 0.1) !important;
            color: #ffc800 !important;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        /* Card Detail */
        .recipe-detail-card {
            background: #FFFFFF;
            padding: 30px;
            border-radius: 20px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin: 0 20px;
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

        .info-text {
            line-height: 0.5;
            margin-top: 2px;
            white-space: pre-line;
        }

        .info-title {
            margin-top: 15px;
            margin-bottom: 8px;
        }

        .recipe-meta-info {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-top: 30px;
        }

        .recipe-meta-info span {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
            
        }
    </style>

</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="main">

        <div class="topbar">
            <div class="user-box">
                <img src="<?= $url_foto; ?>" style="object-fit: cover; width: 45px; height: 45px; border-radius: 50%;">
                <span><?= isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Guest'; ?></span>
            </div>
        </div>

        <div style="padding: 20px;">
            <a href="<?= $link_kembali; ?>" class="btn-back" style="...">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="recipe-detail-card">
            <img src="../uploads/makanan/<?php echo $data['gambar']; ?>" alt="<?php echo $data['judul']; ?>" class="recipe-banner">

            <div class="recipe-body-content">
                <h1><?php echo $data['judul']; ?></h1>
                <p class="sub-desc"><?php echo $data['deskripsi']; ?></p>

                <div class="info-title">Bahan</div>
                <div class="info-text">
                    <?= !empty($data['bahan']) ? nl2br($data['bahan']) : '-'; ?>
                </div>

                <div class="info-title">Langkah Memasak</div>
                <div class="info-text">
                    <?= !empty($data['langkah']) ? nl2br($data['langkah']) : '-'; ?>
                </div>

                <div class="recipe-meta-info">
                    <span><i class="fa fa-clock" style="color: #45aaf2;"></i> <?php echo isset($data['waktu_memasak']) ? $data['waktu_memasak'] : '0'; ?> menit</span>
                    <span><i class="fa fa-utensils" style="color: #ffc800;"></i> <?php echo isset($data['porsi']) ? $data['porsi'] : '0'; ?> porsi</span>
                </div>
            </div>
        </div>

        <div class="komentar-section" style="margin-top: 40px; background: #fff; padding: 25px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <h3 style="margin-bottom: 20px; color: #333;">💬 Komentar Diskusi</h3>

            <form method="POST" style="display: flex; gap: 10px; margin-bottom: 30px;">
                <input type="text" name="isi_komentar" placeholder="Tulis komentar lezatmu di sini..." required style="flex: 1; padding: 12px 15px; border: 1px solid #ddd; border-radius: 10px; outline: none; font-family: 'Poppins', sans-serif;">
                <button type="submit" name="kirim_komentar" style="background: #ffc800; color: white; border: none; padding: 0 25px; border-radius: 10px; cursor: pointer; font-weight: 600; transition: 0.3s;">
                    <i class="fa fa-paper-plane"></i> Kirim
                </button>
            </form>

            <div class="daftar-komentar">
                <?php
                $id_resep = $_GET['id'];
                $q_komen = mysqli_query($conn, "
                SELECT komentar.*, users.nama, users.foto_profile 
                FROM komentar 
                JOIN users ON komentar.user_id = users.id 
                WHERE resep_id = '$id_resep' 
                ORDER BY komentar.id DESC
            ");

                if (mysqli_num_rows($q_komen) > 0) {
                    while ($komen = mysqli_fetch_assoc($q_komen)) {
                        // Cek apakah user pengomen punya foto profil atau pakai inisial
                        $nama_komen = $komen['nama'];
                        $foto_komen_db = $komen['foto_profile'];

                        if ($foto_komen_db != "" && $foto_komen_db != "default.png" && file_exists("../uploads/profil/" . $foto_komen_db)) {
                            $url_foto_komen = "../uploads/profil/" . $foto_komen_db;
                        } else {
                            $url_foto_komen = "https://ui-avatars.com/api/?name=" . urlencode($nama_komen) . "&background=4CAF50&color=fff&bold=true";
                        }
                ?>

                        <div style="display: flex; gap: 15px; margin-bottom: 20px; border-bottom: 1px solid #f0f0f0; padding-bottom: 15px;">
                            <img src="<?= $url_foto_komen ?>" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #eee;">
                            <div>
                                <h4 style="margin: 0; font-size: 14px; color: #333;">
                                    <?= $komen['nama'] ?>

                                    <span style="font-size: 11px; color: #999; font-weight: normal; margin-left: 8px;">
                                        <?= date('d M Y - H:i', strtotime($komen['created_at'])) ?>
                                    </span>

                                    <?php if ($komen['user_id'] == $_SESSION['user_id']) { ?>
                                        <a href="hapus_komentar.php?id=<?= $komen['id']; ?>&resep=<?= $id_resep; ?>"
                                            onclick="return confirm('Yakin ingin menghapus komentar ini?')"
                                            style="color:red; margin-left:10px; text-decoration:none;">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    <?php } ?>
                                </h4>
                                <p style="margin: 5px 0 0; font-size: 14px; color: #555; line-height: 1.5;">
                                    <?= $komen['komentar'] ?>
                                </p>
                            </div>
                        </div>

                    <?php
                    }
                } else {
                    ?>
                    <div style="text-align: center; padding: 20px; color: #888;">
                        <i class="fa fa-comments" style="font-size: 30px; color: #ddd; margin-bottom: 10px;"></i>
                        <p style="font-style: italic; font-size: 14px;">Belum ada komentar. Jadilah yang pertama mencicipi dan berkomentar!</p>
                    </div>
                <?php } ?>
            </div>
        </div>

    </div>

</body>

</html>