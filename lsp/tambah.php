<?php
include 'config/db.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <style>
        .form-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 100px 0 50px;
        }
        .form-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            padding: 30px;
        }
        .form-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 25px;
            text-align: center;
            border-radius: 15px 15px 0 0;
        }
        .photo-upload {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
        }
        .photo-upload:hover {
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
        .form-label {
            font-weight: 600;
            color: #2c3e50;
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
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        .btn-back {
            background: #6c757d;
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            color: white;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-back:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        .required {
            color: #e74c3c;
        }
    </style>
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
    </div>
</nav>

<div class="form-container">
    <div class="container">
        <div class="form-card">
            <div class="form-header">
                <h3>📝 Form Pendaftaran Siswa Baru</h3>
                <p class="mb-0">Lengkapi data siswa dengan benar</p>
            </div>

            <form action="proses_tambah.php" method="POST" enctype="multipart/form-data" class="mt-4">
                <div class="row g-4">
                    <!-- Kolom kiri: upload foto -->
                    <div class="col-md-4">
                        <div class="photo-upload" id="photoUpload">
                            <img id="photoPreview" class="photo-preview" alt="Preview Foto">
                            <div id="uploadText">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #667eea; margin-bottom: 15px;"></i>
                                <h5>Upload Foto Siswa</h5>
                                <p>Klik untuk memilih foto atau drag & drop</p>
                                <input type="file" name="foto" id="photoInput" accept="image/*" style="display: none;">
                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('photoInput').click()">Pilih Foto</button>
                            </div>
                        </div>
                        <div class="mt-3 text-muted small">
                            <i class="fas fa-info-circle"></i> Format: JPG, PNG. Max 2MB
                        </div>
                    </div>

                    <!-- Kolom kanan: form data -->
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIS <span class="required">*</span></label>
                                <input type="number" name="NIS" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="">Pilih</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kelas</label>
                                <select name="kelas" class="form-select" required>
                                    <option value="">Pilih</option>
                                    <option value="X RPL 1">X RPL 1</option>
                                    <option value="XI RPL 1">XI RPL 1</option>
                                    <option value="XII RPL 1">XII RPL 1</option>
                                    <option value="X TKJ 1">X TKJ 1</option>
                                    <option value="XI TKJ 1">XI TKJ 1</option>
                                    <option value="XII TKJ 1">XII TKJ 1</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Alamat Lengkap</label>
                                <textarea name="alamat" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kelurahan</label>
                                <input type="text" name="kelurahan" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kecamatan</label>
                                <input type="text" name="kecamatan" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kabupaten/Kota</label>
                                <input type="text" name="kabupaten_kota" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Provinsi</label>
                                <input type="text" name="propinsi" class="form-control">
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-3">
                            <button type="submit" class="btn btn-submit">💾 Simpan Data</button>
                            <a href="index.php" class="btn btn-back">⬅ Kembali</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const photoInput = document.getElementById('photoInput');
    const photoPreview = document.getElementById('photoPreview');
    const uploadText = document.getElementById('uploadText');
    const photoUpload = document.getElementById('photoUpload');

    photoInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                photoPreview.src = e.target.result;
                photoPreview.style.display = 'block';
                uploadText.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    });

    photoUpload.addEventListener('dragover', (e) => {
        e.preventDefault();
        photoUpload.style.borderColor = '#667eea';
    });

    photoUpload.addEventListener('dragleave', (e) => {
        e.preventDefault();
        photoUpload.style.borderColor = '#dee2e6';
    });

    photoUpload.addEventListener('drop', (e) => {
        e.preventDefault();
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            photoInput.files = files;
            photoInput.dispatchEvent(new Event('change'));
        }
    });
</script>
</body>
</html>
