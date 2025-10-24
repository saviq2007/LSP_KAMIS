<?php
include 'config/db.php';
$query = mysqli_query($koneksi, "SELECT * FROM siswa_ujikom ORDER BY nama ASC");
if (!$query) {
    die("Error dalam query: " . mysqli_error($koneksi));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>sekolah</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/index.css">
  <link rel="stylesheet" href="assets/css/navbar.css">
</head>
<body>
  <section class="hero">
    <div class="hero-text">
      <h1>SMKN 2 BANDUNG</h1>
      <p>AHMAD SAVIQ FIRDA</p>
    </div>
    <div class="hero-image">
      <img src="assets/img/1.png" alt="Ilustrasi CRUD">
    </div>
  </section>
  <nav class="navbar">
    <div class="nav-container">
        <div class="nav-menu">
            <div class="nav-item dropdown">
                <a href="#" class="nav-link">File</a>
                <div class="dropdown-content">
                    <a href="index.php">Daftar Siswa</a>
                    <a href="tambah.php">Tambah Data</a>
                    <a href="edit.php">Perbaiki Data</a>
                    <a href="hapus.php">Hapus Data</a>
                </div>
            </div>

            <div class="nav-item dropdown">
                <a href="#" class="nav-link">Rangkuman</a>
                <div class="dropdown-content">
                    <a href="rangkuman-jenis-kelamin.php">Jumlah Pria / Wanita</a>
                    <a href="rangkuman-kelas.php">Jumlah Setiap Kelas</a>
                </div>
            </div>
            
            <div class="nav-item">
                <a href="tentang.php" class="nav-link">Tentang Aplikasi</a>
            </div>
        </div>
        
        <div class="nav-toggle">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
    </div>
  </nav>
  <div class="container mt-4">
    <h3 class="mb-4">Daftar Siswa</h3>
    <div class="mt-3">
        <a href="tambah.php" class="btn btn-primary">Tambah Data Siswa</a>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-primary text-center">
        <tr>
            <th>NIS</th>
            <th>Nama</th>
            <th>Umur</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($query) > 0) {
            while ($row = mysqli_fetch_assoc($query)) { 
                // Hitung umur dari tanggal lahir
                $tanggal_lahir = new DateTime($row['tanggal_lahir']);
                $tanggal_sekarang = new DateTime();
                $umur = $tanggal_sekarang->diff($tanggal_lahir)->y;
                ?>
                <tr>
                    <td><?= $row['NIS'] ?></td>
                    <td><?= $row['nama'] ?></td>
                    <td><?= $umur ?> tahun</td>
                    <td>
                        <a href="read.php?id=<?= $row['id_siswa'] ?>" class="btn btn-success btn-sm">Lihat</a>
                        <a href="edit.php?id=<?= $row['id_siswa'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="hapus.php?id=<?= $row['id_siswa'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                    </td>
                </tr>
            <?php }
        } else { ?>
            <tr>
                <td colspan="4" class="text-center">Belum ada data siswa.</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

  </div>

  <script>
    // Mobile menu toggle
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');

    navToggle.addEventListener('click', () => {
        navMenu.classList.toggle('active');
        navToggle.classList.toggle('active');
    });
  </script>
</body>
</html>
