<?php
session_start();
include '../config/koneksi.php';

/* CEK LOGIN */
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

/* CEK SUBMIT */
if (isset($_POST['tambah'])) {

    // JALUR PENGAMAN: Jika user_id session kosong, kita tembak langsung cari ID-nya ke database pakai email session
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        $email_aktif = $_SESSION['email'];
        $ambil_id    = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email_aktif'");
        $data_id     = mysqli_fetch_assoc($ambil_id);
        $user_id     = array_values($data_id)[0]; // Mengambil kolom ID utama
    } else {
        $user_id     = $_SESSION['user_id'];
    }

    /* INPUT */
    $judul             = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi         = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $bahan             = mysqli_real_escape_string($conn, $_POST['bahan']);
    $langkah           = mysqli_real_escape_string($conn, $_POST['langkah']);
    $waktu_memasak     = mysqli_real_escape_string($conn, $_POST['waktu_memasak']);
    $porsi             = mysqli_real_escape_string($conn, $_POST['porsi']);
    $tingkat_kesulitan = mysqli_real_escape_string($conn, $_POST['tingkat_kesulitan']);
    $status            = "published";

    /* SLUG */
    $slug = strtolower(str_replace(" ", "-", $judul));

    /* UPLOAD GAMBAR */
    $gambar      = $_FILES['gambar']['name'];
    $tmp         = $_FILES['gambar']['tmp_name'];
    $nama_gambar = time() . '-' . $gambar;

    move_uploaded_file($tmp, "../uploads/makanan/" . $nama_gambar);

    /* INSERT DATABASE */
    $query = mysqli_query($conn, "
        INSERT INTO resep (
            user_id, judul, slug, deskripsi, bahan, langkah, gambar, waktu_memasak, porsi, tingkat_kesulitan, status
        ) VALUES (
            '$user_id', '$judul', '$slug', '$deskripsi', '$bahan', '$langkah', '$nama_gambar', '$waktu_memasak', '$porsi', '$tingkat_kesulitan', '$status'
        )
    ");

    /* NOTIFIKASI SWEETALERT */
    if ($query) {
        echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body>
        <script>
        Swal.fire({ icon: 'success', title: 'Berhasil 🎉', text: 'Resep berhasil diupload 🍜', confirmButtonColor: '#ff9f43' }).then(() => { window.location='index.php'; });
        </script></body></html>";
        exit;
    } else {
        echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body>
        <script>
        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Resep gagal diupload', confirmButtonColor: '#d33' }).then(() => { window.history.back(); });
        </script></body></html>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Resep - Limeev</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">

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

    <?php include 'sidebar.php'; ?>

    <div class="main">

        <div class="topbar">
            <div class="user-box">
                <img src="<?= $url_foto; ?>" style="object-fit: cover; width: 45px; height: 45px; border-radius: 50%;">
                <span>
                    <?= isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Guest'; ?>
                    <?= (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') ? '<strong style="color:#ff9f43;">(Admin)</strong>' : ''; ?>
                </span>
            </div>
        </div>

        <div class="recipe-form-container">
            <div class="recipe-form-box">
                <h2>Upload Resep 🍜</h2>
                <p>Bagikan resep favoritmu ke Dapur Limeev</p>

                <form action="" method="POST" enctype="multipart/form-data">

                    <div class="input-group">
                        <label>Judul Resep</label>
                        <div class="input-wrapper">
                            <i class="fa fa-utensils"></i>
                            <input type="text" name="judul" placeholder="Judul Resep" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Deskripsi Resep</label>
                        <div class="input-wrapper">
                            <textarea name="deskripsi" placeholder="Deskripsi resep..." required></textarea>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Bahan-Bahan</label>
                        <div class="input-wrapper">
                            <textarea name="bahan" placeholder="Bahan-bahan..." required></textarea>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Langkah Memasak</label>
                        <div class="input-wrapper">
                            <textarea name="langkah" placeholder="Langkah memasak..." required></textarea>
                        </div>
                    </div>

                    <div style="display: flex; gap: 20px;">
                        <div class="input-group" style="flex: 1;">
                            <label>Waktu Memasak (Menit)</label>
                            <div class="input-wrapper">
                                <i class="fa fa-clock"></i>
                                <input type="number" name="waktu_memasak" placeholder="Waktu Memasak (menit)" required>
                            </div>
                        </div>

                        <div class="input-group" style="flex: 1;">
                            <label>Jumlah Porsi (Orang)</label>
                            <div class="input-wrapper">
                                <i class="fa fa-users"></i>
                                <input type="number" name="porsi" placeholder="Jumlah Porsi" required>
                            </div>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Tingkat Kesulitan</label>
                        <div class="input-wrapper">
                            <select name="tingkat_kesulitan" required>
                                <option value="">Pilih Tingkat Kesulitan</option>
                                <option value="Mudah">Mudah</option>
                                <option value="Sedang">Sedang</option>
                                <option value="Sulit">Sulit</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Foto Masakan</label>
                        <div class="input-wrapper">
                            <input type="file" name="gambar" accept="image/*" required>
                        </div>
                    </div>

                    <button type="submit" name="tambah" class="btn-auth">
                        <i class="fa fa-upload"></i> Upload Resep
                    </button>

                </form>

                <div class="auth-link">
                    <a href="index.php">← Kembali ke Dashboard</a>
                </div>
            </div>
        </div>
    </div>

</body>

</html>