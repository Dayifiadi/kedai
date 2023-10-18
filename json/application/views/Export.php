<!DOCTYPE html>

<html>
<head>
	<title>Laporan Data Penjualan Barang</title>
</head>
<body>
	<style type="text/css">
	body{
		font-family: sans-serif;
	}
	table{
		margin: 20px auto;
		border-collapse: collapse;
	}
	table th,
	table td{
		border: 1px solid #3c3c3c;
		padding: 3px 8px;

	}
	a{
		background: blue;
		color: #fff;
		padding: 8px 10px;
		text-decoration: none;
		border-radius: 2px;
	}
	</style>

	<?php
	header("Content-type: application/vnd-ms-excel");
	header("Content-Disposition: attachment; filename=Laporan_Absensi.xls");
	?>


	<table class="table" border="1">
		<h1 text-align="center">Laporan Data Penjualan Barang</h1>
  	<tr>
  		<th>No</th>
  		<th>Tanggal</th>
  		<th>Nama Penjual</th>
  		<th>Nama Barang</th>
  		<th>Harga Terjual /pc</th>
  		<th>Total Harga</th>
  		<th>Jumlah</th>
  		<th>Laba</th>
  	</tr>

  </table>
</body>
</html>
