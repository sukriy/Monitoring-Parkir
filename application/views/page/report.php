<?php
	include('header.php');
?>
	<div class="breadcrumbs">
		<div class="col-sm-4">
			<div class="page-header float-left">
				<div class="page-title">
					<h1>Dashboard</h1>
				</div>
			</div>
		</div>
		<div class="col-sm-8">
			<div class="page-header float-right">
				<div class="page-title">
					<ol class="breadcrumb text-right">
						<li><a href="<?=base_url();?>">Dashboard</a></li>
						<li class="active">Laporan</li>
					</ol>
				</div>
			</div>
		</div>
	</div>
        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
							<h3>Laporan</h3>
							<?=$this->session->flashdata('report');?>
							<?=$this->session->flashdata('message');?>
                        </div>
                        <div class="card-body">
							<div style="color: red;"><?=validation_errors(); ?></div>
							<?=form_open('Admin/report','id="form_temp"');?>
								<input type="hidden" id='token' name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
								<input type='hidden' class='form-control' name='uniqid' value='<?=uniqid(); ?>'>
								<div class="row form-group">
									<label class="control-label col-md-2">Jenis</label>
									<div class="col col-md-9">
										<div class="form-check">
											<div class="radio">
												<label for="radio1" class="form-check-label ">
													<input data-validation="required" type="radio" name="jenis" value="kendaraan" class="form-check-input" <?=((isset($_POST['jenis']) && $_POST['jenis']=='kendaraan') ? 'checked' : ''); ?>>Pembayaran Member
												</label>
											</div>
											<div class="radio">
												<label for="radio2" class="form-check-label ">
													<input data-validation="required" type="radio" name="jenis" value="transaksi" class="form-check-input" <?=((isset($_POST['jenis']) && $_POST['jenis']=='transaksi') ? 'checked' : ''); ?>>Tiket Parkir
												</label>
											</div>
											<div class="radio">
												<label for="radio2" class="form-check-label ">
													<input data-validation="required" type="radio" name="jenis" value="member" class="form-check-input" <?=((isset($_POST['jenis']) && $_POST['jenis']=='member') ? 'checked' : ''); ?>>Member
												</label>
											</div>
										</div>
									</div>
								</div>
								<div class="row form-group opsi1" >
									<label class="control-label col-md-2">Tipe</label>
									<div class="col col-md-9"> 
										<div class="form-check">
											<div class="radio">
												<label for="radio1" class="form-check-label ">
													<input data-validation="required" type="radio" name="tipe" value="aktif" class="form-check-input" <?=((isset($_POST['tipe']) && $_POST['tipe']=='aktif') ? 'checked' : ''); ?>>Aktif
												</label>
											</div>
											<div class="radio">
												<label for="radio2" class="form-check-label ">
													<input data-validation="required" type="radio" name="tipe" value="tidak_aktif" class="form-check-input" <?=((isset($_POST['tipe']) && $_POST['tipe']=='tidak_aktif') ? 'checked' : ''); ?>>Tidak Aktif
												</label>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group row opsi2" >
									<label class="control-label col-md-2" for="plat">Tanggal</label>
									<div class="col-md-2">
										<input type="text" class="form-control tgl" name='dari' placeholder='Dari...' value="<?=(isset($_POST['dari']) ? $_POST['dari'] : ''); ?>">
									</div>
									<div class="col-md-2">
										<input type="text" class="form-control tgl" name='sampai' placeholder='Sampai...' value="<?=(isset($_POST['sampai']) ? $_POST['sampai'] : ''); ?>">
									</div>
								</div>
								<div class="form-group">
									<button type="submit" class="btn btn-success btn-flat m-b-30 m-t-30 col-md-6" name='submit' value='submit'>Cari</button>
								</div>
							</form>
							<div class='container'>
								<div class="table-responsive">
								<?php
									if(isset($load)){
										if($_POST['jenis']=='transaksi'){
											if($_SESSION['level']=='manager'){
												echo "<button type='button' id='approve'>Approve</button>";
											}
										echo "
										<table class='table table-bordered table-hover compact example'>
											<thead>
												<tr>
													<th>ID_transaksi</th>
													<th>Staff</th>
													<th>Masuk</th>
													<th>Keluar</th>
													<th>Plat</th>
													<th>Lama</th>
													<th>Bayar</th>";
										
										if($_SESSION['level']=='manager'){
											echo '<th>Tgl_Approve</th>';
											echo '<th>Manager</th>';
										}
										echo "
												</tr>
											</thead>
											<tbody>
													";
											foreach($load as $key=>$value){
												echo "
												<tr>
													<td>".cetak($value->id_transaksi)."</td>
													<td>".cetak($value->nama_staff)."</td>
													<td>".cetak_tglwkt($value->tgl_masuk)."</td>
													<td>".cetak_tglwkt($value->tgl_keluar)."</td>
													<td>".cetak($value->plat)."</td>
													<td>".cetak($value->lama)."</td>
													<td>".number_format(cetak($value->bayar),0)."</td>";

												if($_SESSION['level']=='manager'){
													echo "<td>".cetak_tglwkt($value->tgl_approve)."</td>";
													echo "<td>".cetak($value->nama_approve)."</td>";
												}
													
												echo "
												</tr>
												";
											}
											echo "
											</tbody>
										</table>
										";
											
										}else if($_POST['jenis']=='kendaraan'){										
											echo "
										<table class='table table-bordered table-hover compact example'>
											<thead>
												<tr>
													<th>ID_Pembayaran</th>
													<th>Tgl Input</th>
													<th>Plat</th>
													<th>Konfirmasi</th>
													<th>Aktif dari</th>
													<th>Aktif sampai</th>
													<th>Bayar</th>
													<th>Gambar</th>
												</tr>
											</thead>
											<tbody>
											";
											foreach($load as $key=>$value){
												if($value->gambar==''){
													$gambar='';
												}else{
													$base = base_url();
													$gambar="<a href='".$base."images/bayar/".$value->gambar."' data-fancybox><img src='".$base."images/bayar/".$value->gambar."' style='width:200px; height:100px' alt='' /></a>";
												}
												echo "
												<tr>
													<td>".cetak($value->id_pembayaran)."</td>
													<td>".cetak_tglwkt($value->tgl_input)."</td>
													<td>".cetak($value->plat)."</td>
													<td>".cetak($value->nama_lengkap)."</td>
													<td>".cetak_tgl($value->tgl_aktif_dari)."</td>
													<td>".cetak_tgl($value->tgl_aktif_sampai)."</td>
													<td>".cetak(number_format($value->bayar))."</td>
													<td>".$gambar."</td>
												</tr>
												";
											}											
											echo "
											</tbody>
										</table>
											";
										}else if($_POST['jenis']=='member'){
											echo "
										<table class='table table-bordered table-hover compact example'>
											<thead>
												<tr>
													<th>ID_Kendaraan</th>
													<th>Nama Lengkap</th>
													<th>Plat</th>
													<th>Merek</th>
													<th>Tipe</th>
													<th>Aktif sampai</th>
													<th>Aktif sampai</th>
													<th>Gambar</th>
												</tr>
											</thead>
											<tbody>
											";
											foreach($load as $key=>$value){
												if($value->gambar==''){
													$gambar='';
												}else{
													$base = base_url();
													$gambar="<a href='".$base."images/kendaraan/".$value->gambar."' data-fancybox><img src='".$base."images/kendaraan/".$value->gambar."' style='width:200px; height:100px' alt='' /></a>";
												}
												echo "
												<tr>
													<td>".cetak($value->id_kendaraan)."</td>
													<td>".cetak($value->nama_lengkap)."</td>
													<td>".cetak($value->plat)."</td>
													<td>".cetak($value->merek)."</td>
													<td>".cetak($value->tipe)."</td>
													<td>".cetak_tgl($value->tgl_aktif_dari)."</td>
													<td>".cetak_tgl($value->tgl_aktif_sampai)."</td>
													<td>".$gambar."</td>
												</tr>
												";
											}
											echo "
											</tbody>
										</table>";
										}
									}
								?>
								</div>
							</div>							
                        </div>
                    </div>
                </div>
                </div>
            </div><!-- .animated -->
        </div><!-- .content -->
<?php
	include('footer.php');
?>
<script>
$('.opsi1').hide();
$('.opsi2').hide();
$('#approve').click(function(e){
	e.preventDefault();
	swal({
		title: "Apakah Anda yakin?",
		text: "Sekali Approve tidak bisa dibatalkan",
		icon: "warning",
		buttons: true,
		dangerMode: true,
	}).then((willDelete) => {
		if (willDelete) {
			$(".se-pre-con").fadeIn("slow");
			$.post("<?=base_url('Admin/ajax_report_approve'); ?>", {'approve' : 1, 'dari' : $('input[name=dari]').val(), 'sampai' : $('input[name=sampai]').val(), <?php echo $this->security->get_csrf_token_name(); ?>: $("input[name=<?php echo $this->security->get_csrf_token_name(); ?>]").val()}, function(result){
				swal({
				  title: "INFO",
				  text: "Berhasil Approve",
				  icon: "success",
				});
				// console.log(result);
				location.reload();
			});	
		}
	});	
});
$('input[name=jenis]').change(function(e){
	if($(this).val()=='kendaraan' || $(this).val()=='transaksi'){
		$('.opsi2').show();
		$('.opsi1').hide();
	}
	if($(this).val()=='member'){
		$('.opsi2').hide();
		$('.opsi1').show();
	}
});
$(window).load(function(){
	var jenis = "<?=(isset($_POST['jenis'])) ? $_POST['jenis'] : ''?>";
	if(jenis=='kendaraan' || jenis=='transaksi'){
		$('.opsi2').show();
		$('.opsi1').hide();
	}
	if(jenis=='member'){
		$('.opsi2').hide();
		$('.opsi1').show();
	}
});
$('button[type=submit]').click(function(e){
	// var dari = Date.parse($('input[name=dari]').val());
	// var sampai = Date.parse($('input[name=sampai]').val());
	// if( dari > sampai){
		// swal({
		  // title: "Error",
		  // text: "Tanggal dari dan sampai tidak sesuai",
		  // icon: "warning",
		// });
		// e.preventDefault();
	// }
});
$.validate();
</script>