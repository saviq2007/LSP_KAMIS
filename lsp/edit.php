<?php
include 'config/db.php';


$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php?error=ID tidak valid");
    exit();
}

$query = mysqli_query($koneksi, "SELECT * FROM siswa_ujikom WHERE id_siswa = $id");


if (!$query || mysqli_num_rows($query) == 0) {
    header("Location: index.php?error=Data siswa tidak ditemukan");
    exit();
}

$siswa = mysqli_fetch_assoc($query);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $NIS = isset($_POST['NIS']) ? trim($_POST['NIS']) : '';
    $nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
    $tempat_lahir = isset($_POST['tempat_lahir']) ? trim($_POST['tempat_lahir']) : '';
    $tanggal_lahir = isset($_POST['tanggal_lahir']) ? trim($_POST['tanggal_lahir']) : '';
    $jenis_kelamin = isset($_POST['jenis_kelamin']) ? trim($_POST['jenis_kelamin']) : '';
    $kelas = isset($_POST['kelas']) ? trim($_POST['kelas']) : '';
    $alamat = isset($_POST['alamat']) ? trim($_POST['alamat']) : '';
    $kelurahan = isset($_POST['kelurahan']) ? trim($_POST['kelurahan']) : '';
    $kecamatan = isset($_POST['kecamatan']) ? trim($_POST['kecamatan']) : '';
    $kabupaten_kota = isset($_POST['kabupaten_kota']) ? trim($_POST['kabupaten_kota']) : '';
    $propinsi = isset($_POST['propinsi']) ? trim($_POST['propinsi']) : '';


    $errors = [];

    if (empty($NIS)) {
        $errors[] = "NIS harus diisi";
    }

    if (empty($nama)) {
        $errors[] = "Nama lengkap harus diisi";
    }

    if (empty($tempat_lahir)) {
        $errors[] = "Tempat lahir harus diisi";
    }

    if (empty($tanggal_lahir)) {
        $errors[] = "Tanggal lahir harus diisi";
    }

    if (empty($jenis_kelamin)) {
        $errors[] = "Jenis kelamin harus dipilih";
    }

    if (empty($kelas)) {
        $errors[] = "Kelas harus dipilih";
    }

    if (empty($alamat)) {
        $errors[] = "Alamat lengkap harus diisi";
    }


    if (!empty($NIS) && !is_numeric($NIS)) {
        $errors[] = "NIS harus berupa angka";
    }

    if (!empty($tanggal_lahir)) {
        $date = DateTime::createFromFormat('Y-m-d', $tanggal_lahir);
        if (!$date || $date->format('Y-m-d') !== $tanggal_lahir) {
            $errors[] = "Format tanggal lahir tidak valid";
        }
    }


    if (!empty($NIS)) {
        $cek_nis = mysqli_query($koneksi, "SELECT id_siswa FROM siswa_ujikom WHERE NIS = '$NIS' AND id_siswa != $id");
        if (mysqli_num_rows($cek_nis) > 0) {
            $errors[] = "NIS sudah terdaftar, gunakan NIS yang berbeda";
        }
    }

    if (!empty($errors)) {
        $error_message = implode(", ", $errors);
        header("Location: edit.php?id=$id&gagal=" . urlencode($error_message));
        exit();
    }

    $foto = $siswa['foto']; 
    
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $upload_dir = 'uploads/';
     
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = $_FILES['foto']['name'];
        $file_tmp = $_FILES['foto']['tmp_name'];
        $file_size = $_FILES['foto']['size'];
        $file_type = $_FILES['foto']['type'];
        
   
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; 
        
        if (!in_array($file_type, $allowed_types)) {
            header("Location: edit.php?id=$id&gagal=Format file tidak didukung. Gunakan JPG, PNG, atau GIF");
            exit();
        }
        if ($file_size > $max_size) {
            header("Location: edit.php?id=$id&gagal=Ukuran file terlalu besar. Maksimal 2MB");
            exit();
        }
        if (!empty($siswa['foto']) && file_exists("uploads/" . $siswa['foto'])) {
            unlink("uploads/" . $siswa['foto']);
        }
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_file_name = 'siswa_' . $NIS . '_' . time() . '.' . $file_extension;
        $upload_path = $upload_dir . $new_file_name;
        if (move_uploaded_file($file_tmp, $upload_path)) {
            $foto = $new_file_name;
        } else {
            header("Location: edit.php?id=$id&gagal=Gagal mengupload foto");
            exit();
        }
    }
    $NIS = mysqli_real_escape_string($koneksi, $NIS);
    $nama = mysqli_real_escape_string($koneksi, $nama);
    $tempat_lahir = mysqli_real_escape_string($koneksi, $tempat_lahir);
    $tanggal_lahir = mysqli_real_escape_string($koneksi, $tanggal_lahir);
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $jenis_kelamin);
    $kelas = mysqli_real_escape_string($koneksi, $kelas);
    $alamat = mysqli_real_escape_string($koneksi, $alamat);
    $kelurahan = mysqli_real_escape_string($koneksi, $kelurahan);
    $kecamatan = mysqli_real_escape_string($koneksi, $kecamatan);
    $kabupaten_kota = mysqli_real_escape_string($koneksi, $kabupaten_kota);
    $propinsi = mysqli_real_escape_string($koneksi, $propinsi);
    $foto = mysqli_real_escape_string($koneksi, $foto);

    $query_update = "UPDATE siswa_ujikom SET 
        NIS = '$NIS',
        nama = '$nama',
        tempat_lahir = '$tempat_lahir',
        tanggal_lahir = '$tanggal_lahir',
        jenis_kelamin = '$jenis_kelamin',
        kelas = '$kelas',
        alamat = '$alamat',
        kelurahan = '$kelurahan',
        kecamatan = '$kecamatan',
        kabupaten_kota = '$kabupaten_kota',
        propinsi = '$propinsi',
        foto = '$foto'
        WHERE id_siswa = $id";


    $result = mysqli_query($koneksi, $query_update);

    if ($result) {
        $success_message = "Data siswa " . $nama . " berhasil diperbarui";
        header("Location: read.php?id=$id&sukses=" . urlencode($success_message));
        exit();
    } else {
        $error_message = "Gagal memperbarui data siswa: " . mysqli_error($koneksi);
        header("Location: edit.php?id=$id&gagal=" . urlencode($error_message));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Siswa - <?= $siswa['nama'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .edit-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 100px 0 50px;
        }
        .edit-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .edit-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
        }
        .edit-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="0,100 1000,0 1000,100"/></svg>');
            background-size: cover;
        }
        .edit-header h1 {
            position: relative;
            z-index: 2;
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
        }
        .edit-header p {
            position: relative;
            z-index: 2;
            margin: 10px 0 0;
            font-size: 1.2rem;
            opacity: 0.9;
        }
        .form-body {
            padding: 40px;
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
        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-submit {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 25px;
            padding: 12px 40px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .btn-cancel {
            background: #6c757d;
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            color: white;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-cancel:hover {
            background: #5a6268;
            color: white;
            transform: translateY(-2px);
        }
        .required {
            color: #e74c3c;
        }
        .form-row {
            display: flex;
            gap: 20px;
        }
        .form-col {
            flex: 1;
        }
        .current-photo {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
        }
        .current-photo:hover {
            border-color: #667eea;
            background: #f0f2ff;
        }
        .photo-preview {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #667eea;
            margin: 0 auto 20px;
            display: none;
        }
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
            }
            .photo-section {
                margin-top: 30px;
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

    <div class="edit-container">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="edit-card">
                        <div class="edit-header">
                            <h1><i class="fas fa-edit"></i> Edit Data Siswa</h1>
                            <p>Perbarui informasi siswa: <?= $siswa['nama'] ?></p>
                        </div>
                        
                        <div class="form-body">
                            <?php
                            if(isset($_GET['sukses'])){?>
                                <div class="alert alert-success" role="alert">
                                    <i class="fas fa-check-circle"></i> <?php echo $_GET['sukses'];?>
                                </div>
                            <?php } ?>
                            <?php
                            if(isset($_GET['gagal'])){?>
                                <div class="alert alert-danger" role="alert">
                                    <i class="fas fa-exclamation-circle"></i> <?php echo $_GET['gagal'];?>
                                </div>
                            <?php } ?>

                            <form action="edit.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
                                <div class="form-row">
                                    <div class="form-col">
                                        <div class="mb-3">
                                            <label class="form-label">NIS <span class="required">*</span></label>
                                            <input type="number" name="NIS" class="form-control" value="<?= $siswa['NIS'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="form-col">
                                        <div class="mb-3">
                                            <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                                            <input type="text" name="nama" class="form-control" value="<?= $siswa['nama'] ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-col">
                                        <div class="mb-3">
                                            <label class="form-label">Tempat Lahir <span class="required">*</span></label>
                                            <input type="text" name="tempat_lahir" class="form-control" value="<?= $siswa['tempat_lahir'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="form-col">
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal Lahir <span class="required">*</span></label>
                                            <input type="date" name="tanggal_lahir" class="form-control" value="<?= $siswa['tanggal_lahir'] ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-col">
                                        <div class="mb-3">
                                            <label class="form-label">Jenis Kelamin <span class="required">*</span></label>
                                            <select name="jenis_kelamin" class="form-select" required>
                                                <option value="">Pilih Jenis Kelamin</option>
                                                <option value="Laki-laki" <?= $siswa['jenis_kelamin'] == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                                <option value="Perempuan" <?= $siswa['jenis_kelamin'] == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-col">
                                        <div class="mb-3">
                                            <label class="form-label">Kelas <span class="required">*</span></label>
                                            <select name="kelas" class="form-select" required>
                                                <option value="">Pilih Kelas</option>
                                                <option value="X RPL 1" <?= $siswa['kelas'] == 'X RPL 1' ? 'selected' : '' ?>>X RPL 1</option>
                                                <option value="X RPL 2" <?= $siswa['kelas'] == 'X RPL 2' ? 'selected' : '' ?>>X RPL 2</option>
                                                <option value="X TKJ 1" <?= $siswa['kelas'] == 'X TKJ 1' ? 'selected' : '' ?>>X TKJ 1</option>
                                                <option value="X TKJ 2" <?= $siswa['kelas'] == 'X TKJ 2' ? 'selected' : '' ?>>X TKJ 2</option>
                                                <option value="XI RPL 1" <?= $siswa['kelas'] == 'XI RPL 1' ? 'selected' : '' ?>>XI RPL 1</option>
                                                <option value="XI RPL 2" <?= $siswa['kelas'] == 'XI RPL 2' ? 'selected' : '' ?>>XI RPL 2</option>
                                                <option value="XI TKJ 1" <?= $siswa['kelas'] == 'XI TKJ 1' ? 'selected' : '' ?>>XI TKJ 1</option>
                                                <option value="XI TKJ 2" <?= $siswa['kelas'] == 'XI TKJ 2' ? 'selected' : '' ?>>XI TKJ 2</option>
                                                <option value="XII RPL 1" <?= $siswa['kelas'] == 'XII RPL 1' ? 'selected' : '' ?>>XII RPL 1</option>
                                                <option value="XII RPL 2" <?= $siswa['kelas'] == 'XII RPL 2' ? 'selected' : '' ?>>XII RPL 2</option>
                                                <option value="XII TKJ 1" <?= $siswa['kelas'] == 'XII TKJ 1' ? 'selected' : '' ?>>XII TKJ 1</option>
                                                <option value="XII TKJ 2" <?= $siswa['kelas'] == 'XII TKJ 2' ? 'selected' : '' ?>>XII TKJ 2</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Alamat Lengkap <span class="required">*</span></label>
                                    <textarea name="alamat" class="form-control" rows="3" required><?= $siswa['alamat'] ?></textarea>
                                </div>

                                <div class="form-row">
                                    <div class="form-col">
                                        <div class="mb-3">
                                            <label class="form-label">Kelurahan</label>
                                            <input type="text" name="kelurahan" class="form-control" value="<?= $siswa['kelurahan'] ?>">
                                        </div>
                                    </div>
                                    <div class="form-col">
                                        <div class="mb-3">
                                            <label class="form-label">Kecamatan</label>
                                            <input type="text" name="kecamatan" class="form-control" value="<?= $siswa['kecamatan'] ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-col">
                                        <div class="mb-3">
                                            <label class="form-label">Kabupaten/Kota</label>
                                            <input type="text" name="kabupaten_kota" class="form-control" value="<?= $siswa['kabupaten_kota'] ?>">
                                        </div>
                                    </div>
                                    <div class="form-col">
                                        <div class="mb-3">
                                            <label class="form-label">Provinsi</label>
                                            <input type="text" name="propinsi" class="form-control" value="<?= $siswa['propinsi'] ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-3 mt-4">
                                    <button type="submit" class="btn btn-submit">
                                        <i class="fas fa-save"></i> Simpan Perubahan
                                    </button>
                                    <a href="read.php?id=<?= $id ?>" class="btn-cancel">
                                        <i class="fas fa-times"></i> Batal
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="edit-card">
                        <div class="edit-header">
                            <h3><i class="fas fa-camera"></i> Foto Siswa</h3>
                            <p>Update foto siswa (opsional)</p>
                        </div>
                        <div class="photo-section">
                            <?php if (!empty($siswa['foto'])): ?>
                                <img src="uploads/<?= $siswa['foto'] ?>" alt="Foto <?= $siswa['nama'] ?>" class="student-photo">
                                <h5><?= $siswa['nama'] ?></h5>
                                <p class="text-muted">Foto saat ini</p>
                            <?php else: ?>
                                <div class="default-photo">
                                    <i class="fas fa-user"></i>
                                </div>
                                <h5><?= $siswa['nama'] ?></h5>
                                <p class="text-muted">Belum ada foto</p>
                            <?php endif; ?>
                            
                            <div class="current-photo" id="photoUpload">
                                <img id="photoPreview" class="photo-preview" alt="Preview Foto">
                                <div id="uploadText">
                                    <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: #667eea; margin-bottom: 15px;"></i>
                                    <h6>Update Foto</h6>
                                    <p>Klik untuk memilih foto baru</p>
                                    <input type="file" name="foto" id="photoInput" accept="image/*" style="display: none;">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('photoInput').click()">
                                        Pilih Foto Baru
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Format: JPG, PNG, GIF. Maksimal 2MB.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const navToggle = document.querySelector('.nav-toggle');
        const navMenu = document.querySelector('.nav-menu');

        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            navToggle.classList.toggle('active');
        });

        document.getElementById('photoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('photoPreview');
                    const uploadText = document.getElementById('uploadText');
                    
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    uploadText.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });


        const photoUpload = document.getElementById('photoUpload');
        
        photoUpload.addEventListener('dragover', function(e) {
            e.preventDefault();
            photoUpload.style.borderColor = '#667eea';
            photoUpload.style.background = '#f0f2ff';
        });
        
        photoUpload.addEventListener('dragleave', function(e) {
            e.preventDefault();
            photoUpload.style.borderColor = '#dee2e6';
            photoUpload.style.background = '#f8f9fa';
        });
        
        photoUpload.addEventListener('drop', function(e) {
            e.preventDefault();
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                document.getElementById('photoInput').files = files;
                document.getElementById('photoInput').dispatchEvent(new Event('change'));
            }
        });
    </script>
</body>
</html>
