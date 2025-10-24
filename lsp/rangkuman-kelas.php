<?php
include 'config/db.php';

$query_kelas = mysqli_query($koneksi, "SELECT kelas, COUNT(*) as jumlah FROM siswa_ujikom GROUP BY kelas ORDER BY kelas");
$data_kelas = [];
$total_siswa = 0;
while ($row = mysqli_fetch_assoc($query_kelas)) {
    $data_kelas[$row['kelas']] = $row['jumlah'];
    $total_siswa += $row['jumlah'];
}

$query_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM siswa_ujikom");
$total_siswa = mysqli_fetch_assoc($query_total)['total'];

$query_tingkat = mysqli_query($koneksi, "SELECT 
    CASE 
        WHEN kelas LIKE 'X%' THEN 'Kelas X'
        WHEN kelas LIKE 'XI%' THEN 'Kelas XI'
        WHEN kelas LIKE 'XII%' THEN 'Kelas XII'
        ELSE 'Lainnya'
    END as tingkat,
    COUNT(*) as jumlah
    FROM siswa_ujikom 
    GROUP BY tingkat 
    ORDER BY tingkat");
$data_tingkat = [];
while ($row = mysqli_fetch_assoc($query_tingkat)) {
    $data_tingkat[$row['tingkat']] = $row['jumlah'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rangkuman Kelas - SMKN 2 BANDUNG</title>
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
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 40px;
        }
        .stat-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            border-left: 5px solid;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .stat-card.x {
            border-left-color: #e74c3c;
            background: linear-gradient(135deg, #ffebee, #ffcdd2);
        }
        .stat-card.xi {
            border-left-color: #f39c12;
            background: linear-gradient(135deg, #fff3e0, #ffe0b2);
        }
        .stat-card.xii {
            border-left-color: #27ae60;
            background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
        }
        .stat-card.total {
            border-left-color: #2ecc71;
            background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
        }
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .stat-label {
            font-size: 1rem;
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
            margin-bottom: 20px;
        }
        .chart-title {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
            font-weight: 600;
        }
        .class-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .class-item {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            border-left: 4px solid;
            transition: all 0.3s ease;
        }
        .class-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .class-item.rpl {
            border-left-color: #3498db;
        }
        .class-item.tkj {
            border-left-color: #e74c3c;
        }
        .class-name {
            font-weight: 600;
            font-size: 1.1rem;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .class-count {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 5px;
        }
        .class-percentage {
            font-size: 0.9rem;
            color: #6c757d;
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
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
                padding: 20px;
            }
            .class-list {
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

    <div class="summary-container">
        <div class="container">
            <div class="summary-card">
                <div class="summary-header">
                <img src="assets/img/1.png" alt="Logo Sekolah" width="300" height="200" style="vertical-align: middle;">
                    <h1>Rangkuman Kelas</h1>
                    <p>Statistik jumlah siswa berdasarkan kelas dan tingkat</p>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-card x">
                        <div class="stat-icon">
                            <i class="fas fa-graduation-cap" style="color: #e74c3c;"></i>
                        </div>
                        <div class="stat-number" style="color: #e74c3c;"><?= isset($data_tingkat['Kelas X']) ? $data_tingkat['Kelas X'] : 0 ?></div>
                        <div class="stat-label">Kelas X</div>
                        <div class="stat-percentage">
                            <?= $total_siswa > 0 ? round((isset($data_tingkat['Kelas X']) ? $data_tingkat['Kelas X'] : 0) / $total_siswa * 100, 1) : 0 ?>%
                        </div>
                    </div>
                    
                    <div class="stat-card xi">
                        <div class="stat-icon">
                            <i class="fas fa-graduation-cap" style="color: #f39c12;"></i>
                        </div>
                        <div class="stat-number" style="color: #f39c12;"><?= isset($data_tingkat['Kelas XI']) ? $data_tingkat['Kelas XI'] : 0 ?></div>
                        <div class="stat-label">Kelas XI</div>
                        <div class="stat-percentage">
                            <?= $total_siswa > 0 ? round((isset($data_tingkat['Kelas XI']) ? $data_tingkat['Kelas XI'] : 0) / $total_siswa * 100, 1) : 0 ?>%
                        </div>
                    </div>
                    
                    <div class="stat-card xii">
                        <div class="stat-icon">
                            <i class="fas fa-graduation-cap" style="color: #27ae60;"></i>
                        </div>
                        <div class="stat-number" style="color: #27ae60;"><?= isset($data_tingkat['Kelas XII']) ? $data_tingkat['Kelas XII'] : 0 ?></div>
                        <div class="stat-label">Kelas XII</div>
                        <div class="stat-percentage">
                            <?= $total_siswa > 0 ? round((isset($data_tingkat['Kelas XII']) ? $data_tingkat['Kelas XII'] : 0) / $total_siswa * 100, 1) : 0 ?>%
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
                        <h3 class="chart-title">Detail Jumlah Siswa per Kelas</h3>
                        <div class="class-list">
                            <?php foreach ($data_kelas as $kelas => $jumlah): ?>
                                <div class="class-item <?= strpos($kelas, 'RPL') !== false ? 'rpl' : 'tkj' ?>">
                                    <div class="class-name">
                                        <i class="fas fa-chalkboard-teacher"></i> <?= $kelas ?>
                                    </div>
                                    <div class="class-count"><?= $jumlah ?> Siswa</div>
                                    <div class="class-percentage">
                                        <?= $total_siswa > 0 ? round(($jumlah / $total_siswa) * 100, 1) : 0 ?>% dari total
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="rangkuman-jenis-kelamin.php" class="btn-action btn-primary">
                        <i class="fas fa-chart-pie"></i> Lihat Rangkuman Jenis Kelamin
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
        const ctx = document.getElementById('classChart').getContext('2d');
        const classChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [<?php 
                    $labels = [];
                    foreach ($data_kelas as $kelas => $jumlah) {
                        $labels[] = "'" . $kelas . "'";
                    }
                    echo implode(', ', $labels);
                ?>],
                datasets: [{
                    label: 'Jumlah Siswa',
                    data: [<?php 
                        $values = [];
                        foreach ($data_kelas as $kelas => $jumlah) {
                            $values[] = $jumlah;
                        }
                        echo implode(', ', $values);
                    ?>],
                    backgroundColor: [
                        <?php 
                        $colors = [];
                        foreach ($data_kelas as $kelas => $jumlah) {
                            if (strpos($kelas, 'RPL') !== false) {
                                $colors[] = "'#3498db'";
                            } else {
                                $colors[] = "'#e74c3c'";
                            }
                        }
                        echo implode(', ', $colors);
                        ?>
                    ],
                    borderColor: [
                        <?php 
                        $borderColors = [];
                        foreach ($data_kelas as $kelas => $jumlah) {
                            if (strpos($kelas, 'RPL') !== false) {
                                $borderColors[] = "'#2980b9'";
                            } else {
                                $borderColors[] = "'#c0392b'";
                            }
                        }
                        echo implode(', ', $borderColors);
                        ?>
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = <?= $total_siswa ?>;
                                const value = context.parsed.y;
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return 'Jumlah: ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
