<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['status_login']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Penyusup dilarang masuk!'); window.location='../login.php';</script>";
    exit;
}

if (isset($_GET['id'])) {
    $id_resep = $_GET['id'];

    $query_gambar = mysqli_query($conn, "SELECT gambar FROM resep WHERE id = '$id_resep'");
    $data_gambar = mysqli_fetch_assoc($query_gambar);

    if ($data_gambar && file_exists("../uploads/makanan/" . $data_gambar['gambar'])) {
        unlink("../uploads/makanan/" . $data_gambar['gambar']); 
    }

    mysqli_query($conn, "DELETE FROM favorit WHERE resep_id = '$id_resep'");

    $hapus = mysqli_query($conn, "DELETE FROM resep WHERE id = '$id_resep'");

    if ($hapus) {
        echo "<script>
                alert('Berhasil! 🗑️ Resep beserta gambarnya telah dimusnahkan.');
                window.location='../user/admin_kelola_resep.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menghapus resep!');
                window.location='../user/admin_kelola_resep.php';
              </script>";
    }
} else {
    header("Location: ../user/admin_kelola_resep.php");
}
?>