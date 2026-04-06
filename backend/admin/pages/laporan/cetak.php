<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../../app.php';

// Memastikan user sudah login dan role admin/petugas sesuai
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] == 'peminjam') {
    die("Akses Ditolak!");
}

// Memproses input filter
$tgl_mulai = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : '';
$tgl_sampai = isset($_GET['tgl_sampai']) ? $_GET['tgl_sampai'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

$where_clauses = [];
if (!empty($tgl_mulai)) {
    $where_clauses[] = "p.tgl_pinjam >= '" . mysqli_real_escape_string($connect, $tgl_mulai) . "'";
}
if (!empty($tgl_sampai)) {
    $where_clauses[] = "p.tgl_pinjam <= '" . mysqli_real_escape_string($connect, $tgl_sampai) . "'";
}
if (!empty($status)) {
    $where_clauses[] = "p.status = '" . mysqli_real_escape_string($connect, $status) . "'";
}

$where_sql = "";
if (count($where_clauses) > 0) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

// Terkait laporan, biasanya juga perlu melihat data denda jika ada dari pengembalian, kita join pengembalian secara opsional
$query = "SELECT p.*, u.username, b.nama_barang, b.kode_barang 
          FROM peminjaman p 
          LEFT JOIN users u ON p.user_id = u.id 
          LEFT JOIN barang b ON p.barang_id = b.id 
          $where_sql 
          ORDER BY p.tgl_pinjam DESC";

$result = mysqli_query($connect, $query);
$laporan = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $laporan[] = $row;
    }
}
$totalData = count($laporan);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan - Peminjaman Alat Berat</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .kop-surat {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .kop-surat h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            text-transform: uppercase;
        }
        .kop-surat p {
            margin: 5px 0 0 0;
            font-size: 14px;
            color: #555;
        }
        .laporan-title {
            text-align: center;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .laporan-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            margin-bottom: 30px;
        }
        table, th, td {
            border: 1px solid #333;
        }
        th, td {
            padding: 8px 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: 600;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .ttd-box {
            width: 300px;
            float: right;
            text-align: center;
            margin-top: 30px;
        }
        .ttd-box p.jabatan {
            margin-bottom: 80px;
        }
        .ttd-box p.nama {
            font-weight: 600;
            text-decoration: underline;
            margin: 0;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
                -webkit-print-color-adjust: exact;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #4e73df; color: white; border: none; border-radius: 5px; cursor: pointer; font-family: 'Inter', sans-serif; font-weight: 600;">
            Cetak Sekarang
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #e74a3b; color: white; border: none; border-radius: 5px; cursor: pointer; font-family: 'Inter', sans-serif; font-weight: 600; margin-left: 10px;">
            Tutup
        </button>
    </div>

    <!-- KOP SURAT -->
    <div class="kop-surat">
        <h1>Sistem Peminjaman Alat Berat</h1>
        <p>Jl. Pembangunan Jaya No. 99, Gedong Panjang, Kota Nusantara</p>
        <p>Email: admin@alatberat.com | Telp: (021) 888-9999</p>
    </div>

    <div class="laporan-title">
        LAPORAN TRANSAKSI PEMINJAMAN ALAT BERAT
    </div>

    <div class="laporan-info">
        <div>
            <strong>Periode:</strong> 
            <?= !empty($tgl_mulai) ? date('d/m/Y', strtotime($tgl_mulai)) : 'Awal' ?> 
            s/d 
            <?= !empty($tgl_sampai) ? date('d/m/Y', strtotime($tgl_sampai)) : 'Sekarang' ?>
            <br>
            <strong>Filter Status:</strong> <?= !empty($status) ? ucfirst($status) : 'Semua Status' ?>
        </div>
        <div>
            <strong>Tanggal Laporan:</strong> <?= date('d/m/Y') ?>
            <br>
            <strong>Dicetak Oleh:</strong> Administrator
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Kode Pinjam</th>
                <th style="width: 20%;">Peminjam</th>
                <th style="width: 25%;">Alat Berat</th>
                <th style="width: 10%;">Tgl Pinjam</th>
                <th style="width: 10%;">Tgl Kembali</th>
                <th style="width: 15%;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($totalData > 0): ?>
                <?php foreach ($laporan as $index => $item): ?>
                <tr>
                    <td class="text-center"><?= $index + 1 ?></td>
                    <td class="text-center"><strong><?= htmlspecialchars($item['kode_peminjaman'] ?? '-') ?></strong></td>
                    <td><?= htmlspecialchars($item['username'] ?? 'User Dihapus') ?></td>
                    <td>
                        <?= htmlspecialchars($item['nama_barang'] ?? 'Barang Dihapus') ?>
                        <br>
                        <small>Kode: <?= htmlspecialchars($item['kode_barang'] ?? '-') ?></small>
                    </td>
                    <td class="text-center"><?= date('d/m/Y', strtotime($item['tgl_pinjam'])) ?></td>
                    <td class="text-center"><?= date('d/m/Y', strtotime($item['tgl_kembali_rencana'])) ?></td>
                    <td class="text-center"><?= ucfirst(strtolower($item['status'])) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data transaksi peminjaman pada periode ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="ttd-box">
        <p>Kota Nusantara, <?= date('d F Y') ?></p>
        <p class="jabatan">Mengetahui,<br>Kepala Administrasi</p>
        <p class="nama">Ir. Farel Hidayat</p>
        <p class="nip" style="margin: 0; font-size: 12px; color: #555;">NIP. 19850101 201001 1 001</p>
    </div>

</body>
</html>
