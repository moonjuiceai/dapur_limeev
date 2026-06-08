<?php

include '../config/session_user.php';
include '../config/koneksi.php';

$id = $_GET['id'];

mysqli_query($conn, "
DELETE FROM resep
WHERE id='$id'
");

header("Location: resep_saya.php");

?>