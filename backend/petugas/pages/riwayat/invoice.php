<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include koneksi database
include '../../app.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID tidak valid");
}

$id = (int)$_GET['id'];

// Query data peminjaman lengkap
$qPeminjaman = "SELECT p.*, 
                u.username, u.role as user_role,
                b.nama_barang, b.kode_barang, b.harga_sewa_perhari, b.jenis, b.merk,
                pg.tgl_kembali as tgl_pengembalian, pg.kondisi, pg.denda, pg.keterangan as keterangan_pengembalian
                FROM peminjaman p 
                LEFT JOIN users u ON p.user_id = u.id 
                LEFT JOIN barang b ON p.barang_id = b.id 
                LEFT JOIN pengembalian pg ON p.id = pg.peminjaman_id 
                WHERE p.id = $id";

$result = mysqli_query($connect, $qPeminjaman);
if (!$result || mysqli_num_rows($result) == 0) {
    die("Data tidak ditemukan");
}

$data = mysqli_fetch_assoc($result);

// Hitung total jika belum ada
if (empty($data['total_harga']) && !empty($data['harga_sewa_perhari']) && !empty($data['lama_pinjam'])) {
    $data['total_harga'] = $data['harga_sewa_perhari'] * $data['lama_pinjam'];
}

// Format tanggal
$tgl_pinjam = date('d/m/Y', strtotime($data['tgl_pinjam']));
$tgl_kembali_rencana = date('d/m/Y', strtotime($data['tgl_kembali_rencana']));
$tgl_kembali_aktual = !empty($data['tgl_kembali_aktual']) ? date('d/m/Y', strtotime($data['tgl_kembali_aktual'])) : '-';
$tgl_pengembalian = !empty($data['tgl_pengembalian']) ? date('d/m/Y', strtotime($data['tgl_pengembalian'])) : '-';
$created_at = date('d/m/Y H:i', strtotime($data['created_at']));

// Status text
$status_text = ucfirst($data['status']);

// Kondisi text
$kondisi_text = !empty($data['kondisi']) ? ucfirst(str_replace('_', ' ', $data['kondisi'])) : '-';

// Hitung subtotal, denda, dan total akhir
$subtotal = $data['total_harga'] ?? 0;
$denda = $data['denda'] ?? 0;
$total_akhir = $subtotal + $denda;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= $data['kode_peminjaman'] ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f8f9fc;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 2px solid #4e73df;
            padding-bottom: 20px;
        }
        
        .company-info h1 {
            color: #4e73df;
            margin: 0 0 5px 0;
        }
        
        .company-info p {
            margin: 0;
            color: #666;
        }
        
        .invoice-info h2 {
            color: #333;
            margin: 0 0 10px 0;
        }
        
        .invoice-meta {
            background: #f8f9ff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .row {
            display: flex;
            margin-bottom: 10px;
        }
        
        .col {
            flex: 1;
        }
        
        .label {
            font-weight: 600;
            color: #555;
            min-width: 150px;
        }
        
        .value {
            color: #333;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .table th {
            background: #4e73df;
            color: white;
            padding: 12px;
            text-align: left;
        }
        
        .table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        
        .table tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .totals {
            margin-top: 30px;
            text-align: right;
        }
        
        .total-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
        }
        
        .total-label {
            width: 150px;
            font-weight: 600;
            text-align: right;
            padding-right: 10px;
        }
        
        .total-value {
            width: 150px;
            text-align: right;
            font-weight: 600;
        }
        
        .grand-total {
            font-size: 1.2em;
            color: #4e73df;
            border-top: 2px solid #4e73df;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #666;
            font-size: 0.9em;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9em;
        }
        
        .badge-pending { background: #6c757d; color: white; }
        .badge-disetujui { background: #28a745; color: white; }
        .badge-ditolak { background: #dc3545; color: white; }
        .badge-dipinjam { background: #f093fb; color: white; }
        .badge-selesai { background: #43e97b; color: white; }
        .badge-terlambat { background: #ff5858; color: white; }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .invoice-container {
                box-shadow: none;
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
            
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <div class="company-info">
                <h1>Alat Berat Sejahtera</h1>
                <p>Jl. Industri No. 123, Jakarta</p>
                <p>Telp: (021) 12345678 | Email: info@alatberat.com</p>
            </div>
            <div class="invoice-info">
                <h2>INVOICE</h2>
                <p><strong>No: </strong><?= $data['kode_peminjaman'] ?></p>
                <p><strong>Tanggal: </strong><?= $created_at ?></p>
            </div>
        </div>
        
        <div class="invoice-meta">
            <div class="row">
                <div class="col">
                    <div class="label">Status:</div>
                    <div class="value">
                        <span class="status-badge badge-<?= $data['status'] ?>">
                            <?= $status_text ?>
                        </span>
                    </div>
                </div>
                <div class="col">
                    <div class="label">Peminjam:</div>
                    <div class="value"><?= htmlspecialchars($data['username']) ?> (<?= $data['user_role'] ?>)</div>
                </div>
            </div>
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Deskripsi</th>
                    <th>Kuantitas</th>
                    <th>Durasi</th>
                    <th>Harga/Hari</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($data['nama_barang']) ?></strong><br>
                        <small><?= $data['kode_barang'] ?> | <?= $data['jenis'] ?> | <?= $data['merk'] ?></small>
                    </td>
                    <td><?= $data['jumlah'] ?> unit</td>
                    <td><?= $data['lama_pinjam'] ?> hari</td>
                    <td>Rp <?= number_format($data['harga_sewa_perhari'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                </tr>
            </tbody>
        </table>
        
        <div class="totals">
            <div class="total-row">
                <div class="total-label">Subtotal:</div>
                <div class="total-value">Rp <?= number_format($subtotal, 0, ',', '.') ?></div>
            </div>
            
            <?php if ($denda > 0): ?>
            <div class="total-row">
                <div class="total-label">Denda:</div>
                <div class="total-value text-danger">Rp <?= number_format($denda, 0, ',', '.') ?></div>
            </div>
            <?php endif; ?>
            
            <div class="total-row grand-total">
                <div class="total-label">TOTAL:</div>
                <div class="total-value">Rp <?= number_format($total_akhir, 0, ',', '.') ?></div>
            </div>
        </div>
        
        <div class="footer">
            <div style="margin-bottom: 20px; text-align: left;">
                <h4>Informasi Pengembalian:</h4>
                <p><strong>Tanggal Kembali Rencana:</strong> <?= $tgl_kembali_rencana ?></p>
                <p><strong>Tanggal Kembali Aktual:</strong> <?= $tgl_kembali_aktual ?></p>
                <p><strong>Tanggal Pengembalian:</strong> <?= $tgl_pengembalian ?></p>
                <p><strong>Kondisi Barang:</strong> <?= $kondisi_text ?></p>
                <?php if (!empty($data['keterangan_pengembalian'])): ?>
                    <p><strong>Keterangan:</strong> <?= htmlspecialchars($data['keterangan_pengembalian']) ?></p>
                <?php endif; ?>
            </div>
            
            <p>Invoice ini sah dan dikeluarkan oleh sistem.</p>
            <p>Terima kasih telah menggunakan jasa kami.</p>
            
            <div style="margin-top: 40px;">
                <div style="width: 200px; display: inline-block; margin: 0 50px;">
                    <p>Peminjam,</p>
                    <br><br><br>
                    <p><strong><?= htmlspecialchars($data['username']) ?></strong></p>
                </div>
                <div style="width: 200px; display: inline-block; margin: 0 50px;">
                    <p>Petugas,</p>
                    <br><br><br>
                    <p><strong>___________________</strong></p>
                </div>
            </div>
            
            <p style="margin-top: 30px; font-size: 0.8em; color: #999;">
                Invoice dicetak pada: <?= date('d/m/Y H:i:s') ?>
            </p>
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 20px;" class="no-print">
        <button onclick="window.print()" class="print-btn" style="padding: 10px 20px; background: #4e73df; color: white; border: none; border-radius: 5px; cursor: pointer;">
            <i class="fas fa-print"></i> Cetak Invoice
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>
    
    <script>
        // Auto print ketika halaman dimuat (opsional)
        // window.onload = function() {
        //     window.print();
        // }
    </script>
</body>
</html>