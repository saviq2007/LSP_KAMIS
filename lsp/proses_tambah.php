<?php
include 'config/db.php';

// Cek apakah form dikirim dengan method POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: tambah.php?gagal=Method tidak diizinkan");
    exit();
}

// Ambil data dari form
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

// Validasi data yang wajib diisi
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

// Validasi format NIS (harus angka)
if (!empty($NIS) && !is_numeric($NIS)) {
    $errors[] = "NIS harus berupa angka";
}

// Validasi format tanggal
if (!empty($tanggal_lahir)) {
    $date = DateTime::createFromFormat('Y-m-d', $tanggal_lahir);
    if (!$date || $date->format('Y-m-d') !== $tanggal_lahir) {
        $errors[] = "Format tanggal lahir tidak valid";
    }
}

// Cek apakah NIS sudah ada
if (!empty($NIS)) {
    $cek_nis = mysqli_query($koneksi, "SELECT id_siswa FROM siswa_ujikom WHERE NIS = '$NIS'");
    if (mysqli_num_rows($cek_nis) > 0) {
        $errors[] = "NIS sudah terdaftar, gunakan NIS yang berbeda";
    }
}

// Jika ada error, redirect kembali ke form
if (!empty($errors)) {
    $error_message = implode(", ", $errors);
    header("Location: tambah.php?gagal=" . urlencode($error_message));
    exit();
}

// Proses upload foto
$foto = '';
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $upload_dir = 'uploads/';
    
    // Buat folder uploads jika belum ada
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            header("Location: tambah.php?gagal=Gagal membuat folder uploads");
            exit();
        }
    }
    
    // Debug: Cek apakah folder bisa ditulis
    if (!is_writable($upload_dir)) {
        header("Location: tambah.php?gagal=Folder uploads tidak bisa ditulis. Cek permission folder");
        exit();
    }
    
    $file_name = $_FILES['foto']['name'];
    $file_tmp = $_FILES['foto']['tmp_name'];
    $file_size = $_FILES['foto']['size'];
    $file_type = $_FILES['foto']['type'];
    
    // Validasi file
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $max_size = 2 * 1024 * 1024; // 2MB
    
    if (!in_array($file_type, $allowed_types)) {
        header("Location: tambah.php?gagal=Format file tidak didukung. Gunakan JPG, PNG, atau GIF");
        exit();
    }
    
    if ($file_size > $max_size) {
        header("Location: tambah.php?gagal=Ukuran file terlalu besar. Maksimal 2MB");
        exit();
    }
    
    // Generate nama file unik
    $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
    $new_file_name = 'siswa_' . $NIS . '_' . time() . '.' . $file_extension;
    $upload_path = $upload_dir . $new_file_name;
    
    // Upload file
    if (move_uploaded_file($file_tmp, $upload_path)) {
        $foto = $new_file_name;
        // Debug: Cek apakah file benar-benar ada
        if (!file_exists($upload_path)) {
            header("Location: tambah.php?gagal=File tidak tersimpan di server");
            exit();
        }
    } else {
        // Debug: Tampilkan error detail
        $error_msg = "Gagal mengupload foto. ";
        $error_msg .= "Temp file: " . $file_tmp . " ";
        $error_msg .= "Target: " . $upload_path . " ";
        $error_msg .= "Upload dir writable: " . (is_writable($upload_dir) ? 'Yes' : 'No');
        header("Location: tambah.php?gagal=" . urlencode($error_msg));
        exit();
    }
}

// Escape string untuk mencegah SQL injection
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

// Query untuk insert data
$query = "INSERT INTO siswa_ujikom (
    NIS, 
    nama, 
    tempat_lahir, 
    tanggal_lahir, 
    jenis_kelamin, 
    kelas, 
    alamat, 
    kelurahan, 
    kecamatan, 
    kabupaten_kota, 
    propinsi, 
    foto
) VALUES (
    '$NIS',
    '$nama',
    '$tempat_lahir',
    '$tanggal_lahir',
    '$jenis_kelamin',
    '$kelas',
    '$alamat',
    '$kelurahan',
    '$kecamatan',
    '$kabupaten_kota',
    '$propinsi',
    '$foto'
)";

$result = mysqli_query($koneksi, $query);

if ($result) {
    $success_message = "Data siswa " . $nama . " berhasil ditambahkan";
    header("Location: index.php?sukses=" . urlencode($success_message));
    exit();
} else {
    $error_message = "Gagal menambahkan data siswa: " . mysqli_error($koneksi);
    header("Location: tambah.php?gagal=" . urlencode($error_message));
    exit();
}
?>
