<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== true) {
    header("Location: ../login.php");
    exit;
}

$id_komentar = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$id_resep    = isset($_GET['resep']) ? (int)$_GET['resep'] : 0;
$id_user     = $_SESSION['user_id'];

/* Cek apakah komentar milik user yang login */
$cek = mysqli_query($conn, "
    SELECT *
    FROM komentar
    WHERE id = '$id_komentar'
    AND user_id = '$id_user'
");

if (mysqli_num_rows($cek) > 0) {
    mysqli_query($conn, "
        DELETE FROM komentar
        WHERE id = '$id_komentar'
    ");
}

header("Location: detail_resep.php?id=$id_resep");
exit;
?>