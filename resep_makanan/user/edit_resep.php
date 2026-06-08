<?php
session_start();
include '../config/koneksi.php';

$user_id = $_SESSION['user_id'];

$query_user = mysqli_query($conn, "SELECT foto_profile FROM users WHERE id='$user_id'");
$data_user = mysqli_fetch_assoc($query_user);

if (
    !empty($data_user['foto_profile']) &&
    file_exists("../uploads/profil/" . $data_user['foto_profile'])
) {
    $url_foto = "../uploads/profil/" . $data_user['foto_profile'];
} else {
    $url_foto = "https://ui-avatars.com/api/?name=" . urlencode($_SESSION['nama']);
}

if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== true) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}
$id_resep = $_GET['id'];

/* 3. AMBIL DATA LAMA RESEP BERDASARKAN ID */
$query_resep = mysqli_query($conn, "SELECT * FROM resep WHERE id = '$id_resep'");
$resep = mysqli_fetch_assoc($query_resep);

// Jika resep tidak ditemukan di database
if (!$resep) {
    header("Location: index.php");
    exit;
}

// KEAMANAN KETAT: Jika yang buka bukan pemilik resep, tendang kembali ke index
if ($resep['user_id'] != $_SESSION['user_id']) {
    echo "<script>alert('Anda tidak punya akses untuk mengedit resep ini!'); window.location='index.php';</script>";
    exit;
}

/* 4. PROSES UPDATE DATA SAAT TOMBOL SIMPAN DIKLIK */
if (isset($_POST['update'])) {
    $judul             = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi         = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $bahan             = mysqli_real_escape_string($conn, $_POST['bahan']);
    $langkah           = mysqli_real_escape_string($conn, $_POST['langkah']);
    $waktu_memasak     = mysqli_real_escape_string($conn, $_POST['waktu_memasak']);
    $porsi             = mysqli_real_escape_string($conn, $_POST['porsi']);
    $tingkat_kesulitan = mysqli_real_escape_string($conn, $_POST['tingkat_kesulitan']);

    $slug = strtolower(str_replace(" ", "-", $judul));

    // Logika Pengaman Gambar
    $gambar = $_FILES['gambar']['name'];
    $tmp    = $_FILES['gambar']['tmp_name'];

    if ($gambar != "") {
        // Jika user mengupload gambar baru
        $nama_gambar_baru = time() . '-' . $gambar;
        move_uploaded_file($tmp, "../uploads/makanan/" . $nama_gambar_baru);

        // Hapus gambar lama di folder fisik agar tidak memenuhi penyimpanan (opsional)
        if (file_exists("../uploads/makanan/" . $resep['gambar'])) {
            unlink("../uploads/makanan/" . $resep['gambar']);
        }
    } else {
        // Jika user TIDAK mengupload gambar baru, pakai nama gambar yang lama
        $nama_gambar_baru = $resep['gambar'];
    }

    /* QUERY UPDATE DATA */
    $update_query = mysqli_query($conn, "
        UPDATE resep SET 
            judul = '$judul',
            slug = '$slug',
            deskripsi = '$deskripsi',
            bahan = '$bahan',
            langkah = '$langkah',
            gambar = '$nama_gambar_baru',
            waktu_memasak = '$waktu_memasak',
            porsi = '$porsi',
            tingkat_kesulitan = '$tingkat_kesulitan'
        WHERE id = '$id_resep'
    ");

    /* NOTIFIKASI SWEETALERT2 */
    if ($update_query) {
        echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body>
        <script>
        Swal.fire({ icon: 'success', title: 'Berhasil Diperbarui! 🔄', text: 'Resep masakanmu berhasil diubah', confirmButtonColor: '#ff9f43' }).then(() => { window.location='index.php'; });
        </script></body></html>";
        exit;
    } else {
        echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body>
        <script>
        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal memperbarui data resep', confirmButtonColor: '#d33' }).then(() => { window.history.back(); });
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
    <title>Edit Resep - Limeev</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="main">
        <div class="topbar">
            <div class="user-box">
                <img src="<?= $url_foto; ?>"
                    style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                <span><?php echo $_SESSION['nama']; ?></span>
            </div>
        </div>

        <div class="recipe-form-container">
            <div class="recipe-form-box">
                <h2>Edit Resep Masakan 📝</h2>
                <p>Perbarui detail informasi resep kuliner pilihanmu</p>

                <form action="" method="POST" enctype="multipart/form-data">

                    <div class="input-group">
                        <label>Judul Resep</label>
                        <div class="input-wrapper">
                            <i class="fa fa-utensils"></i>
                            <input type="text" name="judul" value="<?php echo $resep['judul']; ?>" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Deskripsi Resep</label>
                        <div class="input-wrapper">
                            <textarea name="deskripsi" required><?php echo $resep['deskripsi']; ?></textarea>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Bahan-Bahan</label>
                        <div class="input-wrapper">
                            <textarea name="bahan" required><?php echo $resep['bahan']; ?></textarea>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Langkah Memasak</label>
                        <div class="input-wrapper">
                            <textarea name="langkah" required><?php echo $resep['langkah']; ?></textarea>
                        </div>
                    </div>

                    <div style="display: flex; gap: 20px;">
                        <div class="input-group" style="flex: 1;">
                            <label>Waktu Memasak (Menit)</label>
                            <div class="input-wrapper">
                                <i class="fa fa-clock"></i>
                                <input type="number" name="waktu_memasak" value="<?php echo $resep['waktu_memasak']; ?>" required>
                            </div>
                        </div>

                        <div class="input-group" style="flex: 1;">
                            <label>Jumlah Porsi (Orang)</label>
                            <div class="input-wrapper">
                                <i class="fa fa-users"></i>
                                <input type="number" name="porsi" value="<?php echo $resep['porsi']; ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Tingkat Kesulitan</label>
                        <div class="input-wrapper">
                            <select name="tingkat_kesulitan" required>
                                <option value="Mudah" <?php echo ($resep['tingkat_kesulitan'] == 'Mudah') ? 'selected' : ''; ?>>Mudah</option>
                                <option value="Sedang" <?php echo ($resep['tingkat_kesulitan'] == 'Sedang') ? 'selected' : ''; ?>>Sedang</option>
                                <option value="Sulit" <?php echo ($resep['tingkat_kesulitan'] == 'Sulit') ? 'selected' : ''; ?>>Sulit</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Foto Masakan *(Biarkan kosong jika tidak ingin diubah)</label>
                        <div style="margin-bottom: 10px; color: #64748b; font-size: 13px;">
                            Gambar saat ini: <strong><?php echo $resep['gambar']; ?></strong>
                        </div>
                        <div class="input-wrapper">
                            <input type="file" name="gambar" accept="image/*">
                        </div>
                    </div>

                    <button type="submit" name="update" class="btn-auth">
                        <i class="fa fa-save"></i> Simpan Perubahan
                    </button>

                </form>

                <div class="auth-link">
                    <a href="index.php">← Batalkan & Kembali</a>
                </div>
            </div>
        </div>
    </div>

</body>

</html>