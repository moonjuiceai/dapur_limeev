<?php
$conn = mysqli_connect("localhost", "root", "", "dapur_limeev");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

?>