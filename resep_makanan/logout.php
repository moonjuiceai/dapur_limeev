<?php
session_start();

session_destroy();

/* KEMBALI KE HALAMAN AWAL */

header("Location: index.php");

exit;
?>