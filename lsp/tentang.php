<?php
include 'config/db.php';

$query_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM siswa_ujikom");
$total_siswa = mysqli_fetch_assoc($query_total)['total'];

$query_kelas = mysqli_query($koneksi, "SELECT COUNT(DISTINCT kelas) as total_kelas FROM siswa_ujikom");
$total_kelas = mysqli_fetch_assoc($query_kelas)['total_kelas'];

$query_jk = mysqli_query($koneksi, "SELECT jenis_kelamin, COUNT(*) as jumlah FROM siswa_ujikom GROUP BY jenis_kelamin");
$data_jk = [];
while ($row = mysqli_fetch_assoc($query_jk)) {
    $data_jk[$row['jenis_kelamin']] = $row['jumlah'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Aplikasi - SMKN 2 BANDUNG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .about-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 100px 0 50px;
        }
        .about-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }
        .about-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 60px 40px;
            text-align: center;
            position: relative;
        }
        .about-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="0,100 1000,0 1000,100"/></svg>');
            background-size: cover;
        }
        .about-header h1 {
            position: relative;
            z-index: 2;
            margin: 0;
            font-size: 3rem;
            font-weight: 700;
        }
        .about-header p {
            position: relative;
            z-index: 2;
            margin: 20px 0 0;
            font-size: 1.3rem;
            opacity: 0.9;
        }
        .app-icon {
            position: relative;
            z-index: 2;
            font-size: 4rem;
            margin-bottom: 20px;
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .stats-section {
            padding: 40px;
            background: #f8f9fa;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #667eea;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .stat-label {
            font-size: 1rem;
            color: #6c757d;
            font-weight: 500;
        }
        .content-section {
            padding: 40px;
        }
        .section-title {
            font-size: 2rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 30px;
            text-align: center;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        .feature-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            border-left: 5px solid #667eea;
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .feature-icon {
            font-size: 2rem;
            color: #667eea;
            margin-bottom: 15px;
        }
        .feature-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .feature-desc {
            color: #6c757d;
            line-height: 1.6;
        }
        .tech-stack {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
        }
        .tech-item {
            display: inline-block;
            background: white;
            padding: 10px 20px;
            border-radius: 25px;
            margin: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            font-weight: 500;
            color: #2c3e50;
        }
        .developer-info {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .developer-info h3 {
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        .developer-info p {
            margin: 10px 0;
            opacity: 0.9;
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
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #5a6268;
            color: white;
            transform: translateY(-2px);
        }
        .version-info {
            background: #e8f5e8;
            border: 1px solid #c8e6c9;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .version-info h5 {
            color: #2e7d32;
            margin-bottom: 10px;
        }
        .version-info p {
            color: #388e3c;
            margin: 5px 0;
        }
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .feature-grid {
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

    <div class="about-container">
        <div class="container">
            <div class="about-card">
                <div class="about-header">
                    <div class="app-icon">
                    <img src="assets/img/1.png" alt="Logo Sekolah" width="300" height="200" style="vertical-align: middle;">

                    </div>
                    <h1>Sistem Manajemen Siswa</h1>
                    <p>SMKN 2 BANDUNG - Ahmad Saviq Firda</p>
                </div>

                <div class="stats-section">
                    <h2 class="section-title">📊 Statistik Aplikasi</h2>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-number"><?= $total_siswa ?></div>
                            <div class="stat-label">Total Siswa</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-chalkboard"></i>
                            </div>
                            <div class="stat-number"><?= $total_kelas ?></div>
                            <div class="stat-label">Total Kelas</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-database"></i>
                            </div>
                            <div class="stat-number">13</div>
                            <div class="stat-label">Field Data</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="stat-number">2</div>
                            <div class="stat-label">Laporan</div>
                        </div>
                    </div>
                </div>

                <div class="content-section">
                    <h2 class="section-title">🚀 Fitur Aplikasi</h2>
                    <div class="feature-grid">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <div class="feature-title">Tambah Data Siswa</div>
                            <div class="feature-desc">
                                Form lengkap untuk menambah data siswa baru dengan validasi yang ketat dan upload foto.
                            </div>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-list"></i>
                            </div>
                            <div class="feature-title">Daftar Siswa</div>
                            <div class="feature-desc">
                                Tampilan tabel yang rapi dengan fitur pencarian, sorting, dan pagination untuk kemudahan navigasi.
                            </div>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="feature-title">Detail Siswa</div>
                            <div class="feature-desc">
                                Halaman detail lengkap dengan foto, informasi pribadi, dan data akademik siswa.
                            </div>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="feature-title">Edit Data</div>
                            <div class="feature-desc">
                                Form edit yang user-friendly dengan pre-filled data dan validasi real-time.
                            </div>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-trash"></i>
                            </div>
                            <div class="feature-title">Hapus Data</div>
                            <div class="feature-desc">
                                Sistem hapus dengan konfirmasi ganda untuk mencegah penghapusan tidak sengaja.
                            </div>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            <div class="feature-title">Laporan & Statistik</div>
                            <div class="feature-desc">
                                Dashboard dengan chart interaktif untuk analisis data siswa berdasarkan jenis kelamin dan kelas.
                            </div>
                        </div>
                    </div>

                    <h2 class="section-title">🛠️ Teknologi yang Digunakan</h2>
                    <div class="tech-stack">
                        <div class="tech-item">PHP 8.0+</div>
                        <div class="tech-item">MySQL</div>
                        <div class="tech-item">HTML5</div>
                        <div class="tech-item">CSS3</div>
                        <div class="tech-item">JavaScript</div>
                        <div class="tech-item">Bootstrap 5</div>
                        <div class="tech-item">Chart.js</div>
                        <div class="tech-item">Font Awesome</div>
                        <div class="tech-item">Responsive Design</div>
                    </div>

                    <h2 class="section-title">📋 Data yang Dikelola</h2>
                    <div class="feature-grid">
                        <div class="feature-card">
                            <div class="feature-title">Data Pribadi</div>
                            <div class="feature-desc">
                                • NIS (Nomor Induk Siswa)<br>
                                • Nama Lengkap<br>
                                • Tempat & Tanggal Lahir<br>
                                • Jenis Kelamin<br>
                                • Foto Siswa
                            </div>
                        </div>
                        <div class="feature-card">
                            <div class="feature-title">Data Akademik</div>
                            <div class="feature-desc">
                                • Kelas<br>
                                • Jurusan (RPL/TKJ)<br>
                                • Tingkat (X, XI, XII)
                            </div>
                        </div>
                        <div class="feature-card">
                            <div class="feature-title">Data Alamat</div>
                            <div class="feature-desc">
                                • Alamat Lengkap<br>
                                • Kelurahan<br>
                                • Kecamatan<br>
                                • Kabupaten/Kota<br>
                                • Provinsi
                            </div>
                        </div>
                    </div>

                    <div class="version-info">
                        <h5><i class="fas fa-info-circle"></i> Informasi Versi</h5>
                        <p><strong>Versi:</strong> 1.0.0</p>
                        <p><strong>Tanggal Rilis:</strong> <?= date('d F Y') ?></p>
                        <p><strong>Status:</strong> <span class="text-success">Aktif & Stabil</span></p>
                    </div>

                    <div class="developer-info">
                        <h3><i class="fas fa-user-tie"></i> Developer Information</h3>
                        <p><strong>Nama:</strong> AHMAD SAVIQ FIRDA</p>
                        <p><strong>Sekolah:</strong> SMKN 2 BANDUNG</p>
                        <p><strong>Jurusan:</strong> Rekayasa Perangkat Lunak (RPL)</p>
                        <p><strong>Email:</strong> ahmadsaviq9@gmail.com</p>
                        <p><strong>GitHub:</strong> github.com/Saviq2007</p>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="index.php" class="btn-action btn-primary">
                        <i class="fas fa-home"></i> Kembali ke Beranda
                    </a>
                    <a href="tambah.php" class="btn-action btn-secondary">
                        <i class="fas fa-plus"></i> Tambah Data Siswa
                    </a>
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

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>
