<?php
// Debug file untuk memeriksa masalah upload
echo "<h2>Debug Upload System</h2>";

// Cek apakah folder uploads ada
$upload_dir = 'uploads/';
echo "<h3>1. Cek Folder Uploads</h3>";
echo "Path: " . $upload_dir . "<br>";
echo "Exists: " . (file_exists($upload_dir) ? "Ya" : "Tidak") . "<br>";
echo "Writable: " . (is_writable($upload_dir) ? "Ya" : "Tidak") . "<br>";

// Buat folder jika belum ada
if (!file_exists($upload_dir)) {
    if (mkdir($upload_dir, 0777, true)) {
        echo "✅ Folder uploads berhasil dibuat<br>";
    } else {
        echo "❌ Gagal membuat folder uploads<br>";
    }
} else {
    echo "✅ Folder uploads sudah ada<br>";
}

// Cek permission folder
if (file_exists($upload_dir)) {
    $perms = fileperms($upload_dir);
    echo "Permission: " . substr(sprintf('%o', $perms), -4) . "<br>";
}

// Test upload form
echo "<h3>2. Test Upload Form</h3>";
echo "<form method='POST' enctype='multipart/form-data'>";
echo "<input type='file' name='test_upload' accept='image/*'><br><br>";
echo "<button type='submit' name='test_submit'>Test Upload</button>";
echo "</form>";

// Proses test upload
if (isset($_POST['test_submit']) && isset($_FILES['test_upload'])) {
    echo "<h3>3. Hasil Test Upload</h3>";
    
    $file = $_FILES['test_upload'];
    echo "File name: " . $file['name'] . "<br>";
    echo "File size: " . $file['size'] . " bytes<br>";
    echo "File type: " . $file['type'] . "<br>";
    echo "File error: " . $file['error'] . "<br>";
    echo "Temp name: " . $file['tmp_name'] . "<br>";
    
    if ($file['error'] == 0) {
        $target_file = $upload_dir . 'test_' . time() . '_' . $file['name'];
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            echo "✅ Upload berhasil ke: " . $target_file . "<br>";
        } else {
            echo "❌ Upload gagal<br>";
        }
    } else {
        echo "❌ File error: " . $file['error'] . "<br>";
    }
}

// Cek isi folder uploads
echo "<h3>4. Isi Folder Uploads</h3>";
if (file_exists($upload_dir)) {
    $files = scandir($upload_dir);
    echo "Files in uploads folder:<br>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "- " . $file . " (" . filesize($upload_dir . $file) . " bytes)<br>";
        }
    }
} else {
    echo "Folder uploads tidak ada<br>";
}

// Cek PHP settings
echo "<h3>5. PHP Upload Settings</h3>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "<br>";
echo "file_uploads: " . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "<br>";
?>
