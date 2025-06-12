<?php
$host = "localhost";
$user = "root"; // Ganti jika user MySQL kamu berbeda
$pass = "";     // Ganti jika ada password
$db   = "art_platform"; // Atau "kanvaskita" jika nama database kamu itu

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
