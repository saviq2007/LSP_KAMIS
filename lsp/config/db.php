<?php
// File koneksi database
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'siswa_smk'; // Sesuai dengan database yang ada

// Membuat koneksi
$koneksi = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set charset untuk mendukung karakter Indonesia
mysqli_set_charset($koneksi, "utf8mb4");
?>
