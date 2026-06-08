<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['status_login']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Penyusup dilarang masuk!'); window.location='../login.php';</script>";
    exit;
}

if (isset($_GET['id'])) {
    $id_user_dihapus = $_GET['id'];

    if ($id_user_dihapus == $_SESSION['user_id']) {
        echo "<script>alert('Anda tidak bisa menghapus akun Anda sendiri!'); window.location='../user/admin_kelola_user.php';</script>";
        exit;
    }

    $query_resep = mysqli_query($conn, "SELECT gambar FROM resep WHERE user_id = '$id_user_dihapus'");
    while ($row = mysqli_fetch_assoc($query_resep)) {
        if (file_exists("../uploads/makanan/" . $row['gambar'])) {
            unlink("../uploads/makanan/" . $row['gambar']);
        }
    }

    mysqli_query($conn, "DELETE FROM favorit WHERE user_id = '$id_user_dihapus'");
 
    mysqli_query($conn, "DELETE FROM resep WHERE user_id = '$id_user_dihapus'");
  
    $hapus_user = mysqli_query($conn, "DELETE FROM users WHERE id = '$id_user_dihapus'");

    if ($hapus_user) {
        echo "<script>
                alert('Berhasil! 🧹 Akun user beserta seluruh resepnya telah dihapus bersih.');
                window.location='../user/admin_kelola_user.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menghapus akun user!');
                window.location='../user/admin_kelola_user.php';
              </script>";
    }
} else {
    header("Location: ../user/admin_kelola_user.php");
}
?>