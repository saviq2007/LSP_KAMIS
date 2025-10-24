<?php
include 'config/db.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$query = mysqli_query($koneksi, "SELECT * FROM siswa_ujikom WHERE id_siswa = $id");
if (!$query || mysqli_num_rows($query) == 0) {
    header("Location: index.php?error=Data tidak ditemukan");
    exit();
}
$siswa = mysqli_fetch_assoc($query);
$tanggal_lahir = new DateTime($siswa['tanggal_lahir']);
$tanggal_sekarang = new DateTime();
$umur = $tanggal_sekarang->diff($tanggal_lahir)->y;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Siswa - <?= $siswa['nama'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .detail-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 100px 0 50px;
        }
        .detail-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .detail-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
        }
        .detail-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="0,100 1000,0 1000,100"/></svg>');
            background-size: cover;
        }
        .detail-header h1 {
            position: relative;
            z-index: 2;
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
        }
        .detail-header p {
            position: relative;
            z-index: 2;
            margin: 10px 0 0;
            font-size: 1.2rem;
            opacity: 0.9;
        }
        .photo-section {
            text-align: center;
            padding: 40px;
            background: #f8f9fa;
        }
        .student-photo {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            border: 6px solid #667eea;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            margin-bottom: 20px;
        }
        .default-photo {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        .default-photo i {
            font-size: 4rem;
            color: white;
        }
        .info-section {
            padding: 40px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        .info-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            border-left: 5px solid #667eea;
            transition: all 0.3s ease;
        }
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .info-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-value {
            color: #495057;
            font-size: 1.1rem;
            margin: 0;
        }
        .action-buttons {
            padding: 30px 40px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn-action {
            padding: 12px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-edit {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
        }
        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(240, 147, 251, 0.4);
            color: white;
        }
        .btn-delete {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
        }
        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
            color: white;
        }
        .btn-back {
            background: #6c757d;
            color: white;
        }
        .btn-back:hover {
            background: #5a6268;
            color: white;
            transform: translateY(-2px);
        }
        .age-badge {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
            margin-top: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            .btn-action {
                width: 100%;
                max-width: 200px;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="index.php">SMKN 2 BANDUNG</a>
            </div>
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

    <div class="detail-container">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="detail-card">
                        <div class="photo-section">
                            <?php if (!empty($siswa['foto'])): ?>
                                <img src="uploads/<?= $siswa['foto'] ?>" alt="Foto <?= $siswa['nama'] ?>" class="student-photo">
                            <?php else: ?>
                                <div class="default-photo">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                            <h4><?= $siswa['nama'] ?></h4>
                            <p class="text-muted"><?= $siswa['NIS'] ?></p>
                            <div class="age-badge">
                                <i class="fas fa-birthday-cake"></i> <?= $umur ?> Tahun
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="detail-card">
                        <div class="detail-header">
                            <h1><i class="fas fa-user-graduate"></i> Detail Siswa</h1>
                            <p>Informasi lengkap data siswa</p>
                        </div>
                        
                        <div class="info-section">
                            <div class="info-grid">
                                <!-- Data Pribadi -->
                                <div class="info-card">
                                    <div class="info-label"><i class="fas fa-id-card"></i> NIS</div>
                                    <p class="info-value"><?= $siswa['NIS'] ?></p>
                                </div>

                                <div class="info-card">
                                    <div class="info-label"><i class="fas fa-user"></i> Nama Lengkap</div>
                                    <p class="info-value"><?= $siswa['nama'] ?></p>
                                </div>

                                <div class="info-card">
                                    <div class="info-label"><i class="fas fa-map-marker-alt"></i> Tempat Lahir</div>
                                    <p class="info-value"><?= $siswa['tempat_lahir'] ?></p>
                                </div>

                                <div class="info-card">
                                    <div class="info-label"><i class="fas fa-calendar"></i> Tanggal Lahir</div>
                                    <p class="info-value"><?= date('d F Y', strtotime($siswa['tanggal_lahir'])) ?></p>
                                </div>

                                <div class="info-card">
                                    <div class="info-label"><i class="fas fa-venus-mars"></i> Jenis Kelamin</div>
                                    <p class="info-value">
                                        <?= $siswa['jenis_kelamin'] ?>
                                        <?php if ($siswa['jenis_kelamin'] == 'Laki-laki'): ?>
                                            <i class="fas fa-male text-primary"></i>
                                        <?php else: ?>
                                            <i class="fas fa-female text-danger"></i>
                                        <?php endif; ?>
                                    </p>
                                </div>

                                <div class="info-card">
                                    <div class="info-label"><i class="fas fa-graduation-cap"></i> Kelas</div>
                                    <p class="info-value"><?= $siswa['kelas'] ?></p>
                                </div>

                                <!-- Data Alamat -->
                                <div class="info-card">
                                    <div class="info-label"><i class="fas fa-home"></i> Alamat Lengkap</div>
                                    <p class="info-value"><?= $siswa['alamat'] ?></p>
                                </div>

                                <?php if (!empty($siswa['kelurahan'])): ?>
                                <div class="info-card">
                                    <div class="info-label"><i class="fas fa-map"></i> Kelurahan</div>
                                    <p class="info-value"><?= $siswa['kelurahan'] ?></p>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($siswa['kecamatan'])): ?>
                                <div class="info-card">
                                    <div class="info-label"><i class="fas fa-map"></i> Kecamatan</div>
                                    <p class="info-value"><?= $siswa['kecamatan'] ?></p>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($siswa['kabupaten_kota'])): ?>
                                <div class="info-card">
                                    <div class="info-label"><i class="fas fa-city"></i> Kabupaten/Kota</div>
                                    <p class="info-value"><?= $siswa['kabupaten_kota'] ?></p>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($siswa['propinsi'])): ?>
                                <div class="info-card">
                                    <div class="info-label"><i class="fas fa-flag"></i> Provinsi</div>
                                    <p class="info-value"><?= $siswa['propinsi'] ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <a href="edit.php?id=<?= $siswa['id_siswa'] ?>" class="btn-action btn-edit">
                                <i class="fas fa-edit"></i> Edit Data
                            </a>
                            <a href="hapus.php?id=<?= $siswa['id_siswa'] ?>" class="btn-action btn-delete" 
                               onclick="return confirm('Yakin ingin menghapus data siswa <?= $siswa['nama'] ?>?')">
                                <i class="fas fa-trash"></i> Hapus Data
                            </a>
                            <a href="index.php" class="btn-action btn-back">
                                <i class="fas fa-arrow-left"></i> oke saya mengerti
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
