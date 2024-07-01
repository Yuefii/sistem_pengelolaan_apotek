<!DOCTYPE html>
<html>
<head>
    <title>Transaksi PDF</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Daftar Transaksi</h1>
    <table>
        <thead>
            <tr>
                <th>Nama Obat</th>
                <th>Jumlah</th>
                <th>Total Harga</th>
                <th>Tanggal</th>
                <th>Pegawai</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksis as $transaksi)
                <tr>
                    <td>{{ $transaksi->obat->nama_obat }}</td>
                    <td>{{ $transaksi->jumlah }}</td>
                    <td>{{ $transaksi->total_harga }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('m/d/y') }}</td>
                    <td>{{ $transaksi->user->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
