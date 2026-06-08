<?php
session_start();

include '../config/koneksi.php';

/* CEK LOGIN */

if(!isset($_SESSION['user_id'])){

    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$resep_id = $_GET['id'];

/* CEK SUDAH FAVORIT BELUM */

$cek = mysqli_query($conn,
"SELECT * FROM favorit
WHERE user_id='$user_id'
AND resep_id='$resep_id'");

if(mysqli_num_rows($cek) > 0){

    /* HAPUS FAVORIT */

    mysqli_query($conn,
    "DELETE FROM favorit
    WHERE user_id='$user_id'
    AND resep_id='$resep_id'");

}else{

    /* TAMBAH FAVORIT */

    mysqli_query($conn,
    "INSERT INTO favorit
    (user_id,resep_id)
    VALUES
    ('$user_id','$resep_id')");

}

/* KEMBALI */

header("Location: ../user/index.php");
exit;
?>