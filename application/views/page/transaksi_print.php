<table>
	<tr>
		<th colspan='3' style="font-size:20px">Tiket Parkir</th>
	</tr>
	<tr>
		<td>Waktu</td>
		<td>:</td>
		<td><?=$load[0]->tgl_masuk; ?></td>
	</tr>
<?php
	if($load[0]->flag==2){
		echo "
			<tr>
				<td>Lama</td>
				<td>:</td>
				<td>".$load[0]->lama."</td>
			</tr>
			<tr>
				<td>Bayar</td>
				<td>:</td>
				<td>".number_format($load[0]->bayar,0)."</td>
			</tr>
		";
	}
?>
	<tr>
		<td>Lokasi</td>
		<td>:</td>
		<td><?=strtoupper($load[0]->lokasi); ?></td>
	</tr>
	<td colspan='3'><img class="barcode" src="<?=base_url('assets/barcode.php?text=').$_GET['print']; ?>&size=40&print=true"></td>
	</tr>
</table>
<script src='https://code.jquery.com/jquery-3.3.1.js'></script>
<script type="text/javascript">
window.onload = function(e){
	$(".se-pre-con").fadeIn("slow");
	window.print();
	$(".se-pre-con").fadeOut("slow");
}
</script>