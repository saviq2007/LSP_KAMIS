<?php
include 'config/db.php';
$debug_query = mysqli_query($koneksi, "SELECT * FROM siswa_ujikom LIMIT 5");
$debug_data = [];
while ($row = mysqli_fetch_assoc($debug_query)) {
    $debug_data[] = $row;
}

$query_jk = mysqli_query($koneksi, "SELECT jenis_kelamin, COUNT(*) as jumlah FROM siswa_ujikom GROUP BY jenis_kelamin");
$data_jk = [];
while ($row = mysqli_fetch_assoc($query_jk)) {
    $data_jk[$row['jenis_kelamin']] = $row['jumlah'];
}

$query_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM siswa_ujikom");
$total_siswa = mysqli_fetch_assoc($query_total)['total'];

$laki_laki = 0;
$perempuan = 0;

foreach ($data_jk as $jk => $jumlah) {
    $jk_lower = strtolower(trim($jk));
    if (in_array($jk_lower, ['laki-laki', 'laki', 'male', 'pria'])) {
        $laki_laki = $jumlah;
    } elseif (in_array($jk_lower, ['perempuan', 'wanita', 'female', 'cewek'])) {
        $perempuan = $jumlah;
    }
}

if ($laki_laki == 0 && $perempuan == 0) {
    $manual_query = mysqli_query($koneksi, "SELECT 
        SUM(CASE WHEN jenis_kelamin LIKE '%laki%' OR jenis_kelamin LIKE '%pria%' OR jenis_kelamin LIKE '%male%' THEN 1 ELSE 0 END) as laki,
        SUM(CASE WHEN jenis_kelamin LIKE '%perempuan%' OR jenis_kelamin LIKE '%wanita%' OR jenis_kelamin LIKE '%female%' THEN 1 ELSE 0 END) as perempuan
        FROM siswa_ujikom");
    $manual_data = mysqli_fetch_assoc($manual_query);
    $laki_laki = $manual_data['laki'] ?? 0;
    $perempuan = $manual_data['perempuan'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rangkuman Jenis Kelamin - SMKN 2 BANDUNG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .summary-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 100px 0 50px;
        }
        .summary-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }
        .summary-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
        }
        .summary-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="0,100 1000,0 1000,100"/></svg>');
            background-size: cover;
        }
        .summary-header h1 {
            position: relative;
            z-index: 2;
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
        }
        .summary-header p {
            position: relative;
            z-index: 2;
            margin: 10px 0 0;
            font-size: 1.2rem;
            opacity: 0.9;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            padding: 40px;
        }
        .stat-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            border-left: 5px solid;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .stat-card.male {
            border-left-color: #3498db;
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
        }
        .stat-card.female {
            border-left-color: #e91e63;
            background: linear-gradient(135deg, #fce4ec, #f8bbd9);
        }
        .stat-card.total {
            border-left-color: #2ecc71;
            background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
        }
        .stat-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .stat-label {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
        }
        .stat-percentage {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 5px;
        }
        .chart-container {
            padding: 40px;
            background: #f8f9fa;
        }
        .chart-wrapper {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .chart-title {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
            font-weight: 600;
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
        .progress-bar-custom {
            height: 20px;
            border-radius: 10px;
            overflow: hidden;
            background: #e9ecef;
            margin: 15px 0;
        }
        .progress-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s ease;
        }
        .progress-male {
            background: linear-gradient(135deg, #3498db, #2980b9);
        }
        .progress-female {
            background: linear-gradient(135deg, #e91e63, #c2185b);
        }
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
                padding: 20px;
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

    <div class="summary-container">
        <div class="container">
            <div class="summary-card">
                <div class="summary-header">
                <img src="assets/img/1.png" alt="Logo Sekolah" width="300" height="200" style="vertical-align: middle;">
                <h1> Rangkuman Jenis Kelamin</h1>
                    <p>Statistik jumlah siswa berdasarkan jenis kelamin</p>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-card male">
                        <div class="stat-icon">
                            <i class="fas fa-male" style="color: #3498db;"></i>
                        </div>
                        <div class="stat-number" style="color: #3498db;"><?= $laki_laki ?></div>
                        <div class="stat-label">Laki-laki</div>
                        <div class="stat-percentage">
                            <?= $total_siswa > 0 ? round(($laki_laki / $total_siswa) * 100, 1) : 0 ?>%
                        </div>
                    </div>
                    
                    <div class="stat-card female">
                        <div class="stat-icon">
                            <i class="fas fa-female" style="color: #e91e63;"></i>
                        </div>
                        <div class="stat-number" style="color: #e91e63;"><?= $perempuan ?></div>
                        <div class="stat-label">Perempuan</div>
                        <div class="stat-percentage">
                            <?= $total_siswa > 0 ? round(($perempuan / $total_siswa) * 100, 1) : 0 ?>%
                        </div>
                    </div>
                    
                    <div class="stat-card total">
                        <div class="stat-icon">
                            <i class="fas fa-users" style="color: #2ecc71;"></i>
                        </div>
                        <div class="stat-number" style="color: #2ecc71;"><?= $total_siswa ?></div>
                        <div class="stat-label">Total Siswa</div>
                        <div class="stat-percentage">100%</div>
                    </div>
                </div>


                <div class="chart-container">
                    <div class="chart-wrapper">
                        <h3 class="chart-title">Progress Bar Jenis Kelamin</h3>
                        <div class="progress-bar-custom">
                            <div class="progress-fill progress-male" style="width: <?= $total_siswa > 0 ? ($laki_laki / $total_siswa) * 100 : 0 ?>%"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <span><i class="fas fa-male text-primary"></i> Laki-laki: <?= $laki_laki ?> (<?= $total_siswa > 0 ? round(($laki_laki / $total_siswa) * 100, 1) : 0 ?>%)</span>
                        </div>
                        <div class="progress-bar-custom mt-3">
                            <div class="progress-fill progress-female" style="width: <?= $total_siswa > 0 ? ($perempuan / $total_siswa) * 100 : 0 ?>%"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <span><i class="fas fa-female text-danger"></i> Perempuan: <?= $perempuan ?> (<?= $total_siswa > 0 ? round(($perempuan / $total_siswa) * 100, 1) : 0 ?>%)</span>
                        </div>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="rangkuman-kelas.php" class="btn-action btn-primary">
                        <i class="fas fa-chart-bar"></i> Lihat Rangkuman Kelas
                    </a>
                    <a href="index.php" class="btn-action btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
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

        // Chart.js configuration
        const ctx = document.getElementById('genderChart').getContext('2d');
        const genderChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{
                    data: [<?= $laki_laki ?>, <?= $perempuan ?>],
                    backgroundColor: [
                        '#3498db',
                        '#e91e63'
                    ],
                    borderColor: [
                        '#2980b9',
                        '#c2185b'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = <?= $total_siswa ?>;
                                const value = context.parsed;
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return context.label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
