<?php
include 'config/db.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: index.php?error=ID tidak valid");
    exit();
}
$query_siswa = mysqli_query($koneksi, "SELECT * FROM siswa_ujikom WHERE id_siswa = $id");
if (!$query_siswa || mysqli_num_rows($query_siswa) == 0) {
    header("Location: index.php?error=Data siswa tidak ditemukan");
    exit();
}

$siswa = mysqli_fetch_assoc($query_siswa);
if (isset($_POST['confirm_delete'])) {
    if (!empty($siswa['foto']) && file_exists("uploads/" . $siswa['foto'])) {
        unlink("uploads/" . $siswa['foto']);
    }
    $query_hapus = mysqli_query($koneksi, "DELETE FROM siswa_ujikom WHERE id_siswa = $id");
    
    if ($query_hapus) {
        header("Location: index.php?sukses=Data siswa " . $siswa['nama'] . " berhasil dihapus");
        exit();
    } else {
        header("Location: index.php?gagal=Gagal menghapus data siswa");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Siswa - <?= $siswa['nama'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .delete-container {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            min-height: 100vh;
            padding: 100px 0 50px;
            display: flex;
            align-items: center;
        }
        .delete-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 600px;
            margin: 0 auto;
        }
        .delete-header {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
        }
        .delete-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="0,100 1000,0 1000,100"/></svg>');
            background-size: cover;
        }
        .delete-header h1 {
            position: relative;
            z-index: 2;
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
        }
        .delete-header p {
            position: relative;
            z-index: 2;
            margin: 10px 0 0;
            font-size: 1.2rem;
            opacity: 0.9;
        }
        .warning-icon {
            font-size: 4rem;
            color: #ff6b6b;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .student-info {
            padding: 40px;
            text-align: center;
        }
        .student-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #ff6b6b;
            margin: 0 auto 20px;
            display: block;
        }
        .default-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 30px rgba(255, 107, 107, 0.3);
        }
        .default-photo i {
            font-size: 3rem;
            color: white;
        }
        .student-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .student-details {
            color: #6c757d;
            margin-bottom: 30px;
        }
        .warning-message {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        .warning-message h5 {
            color: #856404;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .warning-message p {
            color: #856404;
            margin: 0;
            font-size: 0.9rem;
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
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
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
        .btn-cancel {
            background: #6c757d;
            color: white;
        }
        .btn-cancel:hover {
            background: #5a6268;
            color: white;
            transform: translateY(-2px);
        }
        .form-delete {
            display: inline;
        }
        .danger-zone {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .danger-zone h6 {
            color: #721c24;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .danger-zone p {
            color: #721c24;
            margin: 0;
            font-size: 0.9rem;
        }
        @media (max-width: 768px) {
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

    <div class="delete-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="delete-card">
                        <div class="delete-header">
                            <h1><i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus</h1>
                            <p>Anda akan menghapus data siswa berikut</p>
                        </div>
                        
                        <div class="student-info">
                            <div class="warning-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            
                            <?php if (!empty($siswa['foto'])): ?>
                                <img src="uploads/<?= $siswa['foto'] ?>" alt="Foto <?= $siswa['nama'] ?>" class="student-photo">
                            <?php else: ?>
                                <div class="default-photo">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="student-name"><?= $siswa['nama'] ?></div>
                            <div class="student-details">
                                <p><strong>NIS:</strong> <?= $siswa['NIS'] ?></p>
                                <p><strong>Kelas:</strong> <?= $siswa['kelas'] ?></p>
                                <p><strong>Jenis Kelamin:</strong> <?= $siswa['jenis_kelamin'] ?></p>
                            </div>

                            <div class="warning-message">
                                <h5><i class="fas fa-warning"></i> Peringatan!</h5>
                                <p>Tindakan ini akan menghapus data siswa secara permanen dari database. Data yang sudah dihapus tidak dapat dikembalikan.</p>
                            </div>

                            <div class="danger-zone">
                                <h6><i class="fas fa-skull-crossbones"></i> Zona Bahaya</h6>
                                <p>Pastikan Anda benar-benar yakin ingin menghapus data siswa <strong><?= $siswa['nama'] ?></strong> sebelum melanjutkan.</p>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <form class="form-delete" method="POST">
                                <button type="submit" name="confirm_delete" class="btn-action btn-delete" 
                                        onclick="return confirm('Apakah Anda YAKIN ingin menghapus data siswa <?= $siswa['nama'] ?>?\\n\\nTindakan ini TIDAK DAPAT DIBATALKAN!')">
                                    <i class="fas fa-trash"></i> Ya, Hapus Data
                                </button>
                            </form>
                            
                            <a href="read.php?id=<?= $siswa['id_siswa'] ?>" class="btn-action btn-cancel">
                                <i class="fas fa-times"></i> Batal
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

        // Double confirmation for delete
        document.querySelector('form').addEventListener('submit', function(e) {
            const studentName = '<?= $siswa['nama'] ?>';
            const confirmMessage = `PERINGATAN TERAKHIR!\n\nAnda akan menghapus data siswa:\n"${studentName}"\n\nTindakan ini TIDAK DAPAT DIBATALKAN!\n\nApakah Anda BENAR-BENAR YAKIN?`;
            
            if (!confirm(confirmMessage)) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
