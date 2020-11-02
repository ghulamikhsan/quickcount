<!DOCTYPE html>
<html>
<head>
	<title>Report Point</title>
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
	header("Content-Disposition: attachment; filename=Report Poin $month  $year.xls");
	?>

	<center>
		<h1>Report Data Excel Bulan {{$month}} tahun {{$year}}</h1>
	</center>

	<table border="1">
		<tr>
			<th>Kode Produk</th>
			<th>Nama Produk</th>
			<th>Poin Produk</th>
			<th>Penjualan</th>
			<th>Total Poin</th>
        </tr>
        @foreach ($transactions as $transaction)
            <tr>
                <td colspan="4">{{$transaction['name']}} ({{$transaction['code']}} {{ $transaction['referral_code'] ? " -> ".$transaction['referral_code'] : '' }} )</td>
                <td>{{ "P : " . $transaction['self_point'] . " PG : " . $transaction['group_point'] . " (".($transaction['self_point']+$transaction['group_point']).") "}}</td>
            </tr>
			@if (isset($transaction['details']))
                @foreach ($transaction['details'] as $detail)
                    <tr>
                        <td>{{$detail['inventory_code']}}</td>
                        <td>{{$detail['inventory_name']}}</td>
                        <td>{{$detail['pv']}}</td>
                        <td>{{$detail['qty']}}</td>
                        <td>{{$detail['point']}}</td>
                    </tr>
                @endforeach
            @endif
            <tr>
                <td colspan="5"></td>
            </tr>
        @endforeach
	</table>
</body>
</html>