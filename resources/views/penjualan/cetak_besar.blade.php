<!DOCTYPE doctype html>
<html lang="en">
<head>
	<title>
		War-Mart.id
	</title>
	<!-- Bootstrap core CSS     -->
	<link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet"/>
	<link href="{{ asset('css/selectize.bootstrap3.css') }}" rel="stylesheet">
</head>
<style type="text/css">
th,td{
	padding: 1px;
}


.table1, .th, .td {
	border: 1px solid black;
	font-size: 15px;
	font: verdana;
}


</style>
<body>

	<div class="container">
		<div class="row"><!--row1-->
			<div class="col-sm-2"></div><!--penutup colsm2-->
			<div class="col-sm-8">
				<center> <h4> <b> {{$penjualan->nama_warung}} </b> </h4> 
					<p> {{$penjualan->alamat_warung}}<br>
						No.Telp : {{$penjualan->no_telp_warung}} </p> </center>

					</div><!--penutup colsm5-->
				</div><!--penutup row1-->


				<div class="row">
					<div class="col-sm-8">				
						<table>
							<tbody>
								<tr><td width="25%"><font class="satu">No Transaksi</font></td> <td> :&nbsp;</td> <td><font class="satu">{{$penjualan->id}}</font> </tr>
									<tr><td  width="25%"><font class="satu">Pelanggan</font></td> <td> :&nbsp;</td> <td><font class="satu"> {{$penjualan->pelanggan}} </font></td></tr>
									<tr><td  width="25%"><font class="satu">Alamat</font></td> <td> :&nbsp;</td> <td><font class="satu"> {{$penjualan->alamat_pelanggan}} </font></td></tr>
								</tbody>
							</table>
						</div>

						<div class="col-sm-4">
							<table>
								<tbody>
									<tr><td width="25%"><font class="satu"> Waktu</td> <td> :&nbsp;&nbsp;</td> <td>{{$penjualan->waktu_jual}}</font> </td></tr> 
									<tr><td width="25%"><font class="satu"> Kasir</td> <td> :&nbsp;&nbsp;</td> <td>{{$penjualan->kasir}}</font></td></tr> 
									<tr><td width="25%"><font class="satu"> Status </td> <td> :&nbsp;&nbsp;</td> <td>{{$penjualan->status_penjualan}}</font></td></tr> 

								</tbody>
							</table>

						</div> <!--end col-sm-2-->
					</div> <!--end row-->  
					<br>
					<table class="table table-bordered">
						<thead>
							<th class="table1" style="width: 35%"> Nama Produk  </th>
							<th class="table1" style="width: 5%"> <center> Satuan </center> </th>
							<th class="table1" style="width: 5%"> <center> Qty </center> </th>
							<th class="table1" style="width: 15%"> <center> Harga </center> </th>
							<th class="table1" style="width: 5%"> <center> Disc. </center> </th>
							<th class="table1" style="width: 12%"> <center> Subtotal </center> </th>

						</thead>
						<tbody>

							@foreach ($detail_penjualan as $detail_penjualans)	
							<tr>
								<td class='table1'>{{title_case($detail_penjualans->produk->nama_barang)}} </td>
								<td class='table1' align='right'>{{$detail_penjualans->produk->satuan->nama_satuan}} </td>
								<td class='table1' align='right'>{{number_format($detail_penjualans->jumlah_produk, 0, ',', '.')}} </td>
								<td class='table1' align='right'>{{number_format($detail_penjualans->harga_produk, 0, ',', '.')}}</td>
								<td class='table1' align='right'>{{number_format($detail_penjualans->potongan, 0, ',', '.')}}</td>
								<td class='table1' align='right'>{{number_format($detail_penjualans->subtotal, 0, ',', '.')}}</td>
							</tr>
							@endforeach

						</tbody>

					</table>
					<br>

					<div class="row"><!--row1-->
						<div class="col-sm-6">
							<i><b><font class="satu">Terbilang :</font></b>{{$terbilang}}</i> <br>
						</div><!--penutup colsm2-->

						<div class="col-sm-3">

							<table>
								<tbody>
									<tr><td width="50%"><font class="satu">Sub Total</font></td> <td> :&nbsp;</td> <td><font class="satu"> {{number_format($subtotal, 0, ',', '.')}} </font></tr>
										<tr><td width="50%"><font class="satu">Diskon</font></td> <td> :&nbsp;</td> <td><font class="satu"> {{number_format($penjualan->potongan, 0, ',', '.')}}</font> </tr>
											<tr><td  width="50%"><font class="satu">Total Akhir</font></td> <td> :&nbsp;</td> <td><font class="satu"> {{number_format($penjualan->total, 0, ',', '.')}}</font>  </td></tr>
										</tbody>
									</table>

								</div>

								<div class="col-sm-3">
									<table>
										<tbody>

											<tr><td  width="40%"><font class="satu">Bayar</font></td> <td> :&nbsp;</td> <td><font class="satu"> {{number_format($penjualan->tunai, 0, ',', '.')}}</font> </td></tr>
											<tr><td  width="40%"><font class="satu">Kembali</font></td> <td> :&nbsp;</td> <td><font class="satu">{{number_format($penjualan->kembalian, 0, ',', '.')}}</font> </td></tr>
											<tr><td  width="40%"><font class="satu">Kas</font></td> <td> :&nbsp;</td> <td><font class="satu">{{$penjualan->nama_kas}}</font> </td></tr>   

										</tbody>
									</table>

								</div><!--penutup colsm5-->
							</div><!--penutup row1-->
							<br>
							<div class="row">
								<div class="col-sm-9">

									<font class="satu"><b>Pelanggan<br><br><br> <font class="satu">{{$penjualan->pelanggan}}</font> </b></font>

								</div> <!--/ col-sm-6-->

								<div class="col-sm-3">

									<font class="satu"><b>Petugas <br><br><br> <font class="satu">{{$penjualan->kasir}}</font></b></font>

								</div> <!--/ col-sm-6-->
							</div>

						</div>
					</body>
					<!--   Core JS Files   -->
					<script src="{{ asset('js/app.js?v=1.51')}}" type="text/javascript"></script>

					<script>
						$(document).ready(function(){
							window.print();
						});
					</script>
					@yield('scripts')
					</html>