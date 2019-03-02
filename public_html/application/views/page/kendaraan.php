<?php
	date_default_timezone_set("Asia/Bangkok");
	$tgl=date("Y-m-d");
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
						<li class="active">Account</li>
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
						<?php
							// if($_SESSION['level']=='manager'){
								// $hak = array();
								// foreach($menulist[strtolower($this->router->fetch_method())] as $key=>$value){
									// array_push($hak, $key); 
								// } 
							// }else{
								$hak = $hak_akses[$_SESSION['level']][strtolower($this->router->fetch_method())];
							// }
							if(array_search("baru",$hak)!=''){
								echo "<button type='submit' class='btn btn-success btn-flat m-b-30 m-t-30' name='tambah'>Tambah</button>";
							}
						?>
							<?=$this->session->flashdata('kendaraan');?>
							<?=$this->session->flashdata('message');?>
                        </div>
                        <div class="card-body">
							<div class='container'>
								<div class="table-responsive">
									<table class='table table-bordered table-hover compact example'>
										<thead>
											<tr>
												<th>ID_Kendaraan</th>
												<th>Account</th>
												<th>Plat</th>
												<th>Merek</th>
												<th>Tipe</th>
												<th>Aktif Sampai</th>
												<th>Gambar</th>
												<th>Manage</th>
											</tr>
										</thead>
										<tbody>
										<?php
											foreach($load as $key=>$value){
												$ada = 0;
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
													<td>".cetak_tgl($value->tgl_aktif_sampai)."</td>
													<td>".$gambar."</td>
													<td>";
												
												if(array_search("konfirmasi",$hak)!='' && $value->id_pembayaran!=''){
													if($ada>0) echo "&nbsp;|&nbsp;";													
													echo "<a href='#' class='konfirmasi' style='color:blue' data-toggle='modal' data-target='#myModal'>Konfirmasi</a>";
													$ada++;
												}
												if(array_search("bayar",$hak)!=''){
													if($ada>0) echo "&nbsp;|&nbsp;";
													echo "<a href='#' class='bayar' style='color:blue' data-toggle='modal' data-target='#myModal'>Bayar</a>";
													$ada++;
												}
												if(array_search("edit",$hak)!='' && $value->tgl_aktif_sampai<$tgl){
													if($ada>0) echo "&nbsp;|&nbsp;";
													echo "<a href='#' class='edit' style='color:blue'>Edit</a>";
													$ada++;
												}
												if(array_search("hapus",$hak)!='' && $value->tgl_aktif_sampai<$tgl){
													if($ada>0) echo "&nbsp;|&nbsp;";
													echo "<a href='#' class='hapus' style='color:blue'>Hapus</a>";
													$ada++;
												}
												echo "
													</td>
												</tr>
												";
											}
										?>
										</tbody>
									</table>
								</div>
							</div>
                        </div>
                    </div>
                </div>
                </div>
            </div><!-- .animated -->
        </div><!-- .content -->

		<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title" id="exampleModalLabel">Pembayaran Member Kendaraan</h3>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<?=form_open_multipart('Admin/kendaraan_bayar','id="form_temp"');?>
					<div class="form-group row">
						<label class="control-label col-md-4" for="plat">Plat</label>
						<div class="col-md-7">
							<input type="hidden" id='token' name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
							<input type='hidden' class='form-control' name='uniqid' value='<?=uniqid(); ?>'>
							<input type="hidden" class="form-control" name='id_kendaraan' 
								data-validation='required'>
							<input type="text" class="form-control" name='plat' readonly
								data-validation="required">
						</div>
					</div>
					<div class="form-group row">
						<label class="control-label col-md-4" for="bayar">Bayar</label>
						<div class="col-md-7">
							<input type="text" class="form-control" name='bayar' readonly
								data-validation="required">
						</div>
					</div>
					<div class="form-group row">
						<label class="control-label col-md-4" for="gambar_temp">Gambar</label>
						<div class="col-md-7" id='gambar_temp'>
							
						</div>
					</div>
					<div class="form-group row">
						<label class="control-label col-md-4" for="keterangan">Keterangan</label>
						<div class="col-md-7">
							<textarea class="form-control" rows="5" id="keterangan" name='keterangan'></textarea>
						</div>
					</div>					
					<div class="form-group row">
						<div class="col-md-offset-1 col-md-11">						
							<button type="submit" class="btn btn-success col-md-12" name='submit' value='submit'>Submit</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php
	include('footer.php');
?>
<script type="text/javascript">
	$('.example tbody').on('click', '.konfirmasi', function (e) {
		e.preventDefault();
		$(".se-pre-con").fadeIn("slow");
		$('input[type=file]').val('');
		$('input[name=id_kendaraan]').val($(this).closest('tr').find("td:first").text());
		$('input[name=plat]').val($(this).closest('tr').find('td:eq(2)').text());			
		$('input[name=lokasi]').val($(this).closest('tr').find("td:eq(5)").text());
		$('button[name=submit]').val('konfirmasi');

		$.post("<?=base_url('Admin/ajax_transaksi_konfirmasi'); ?>", {'check' : 1, 'id_kendaraan' : $(this).closest('tr').find("td:first").text()}, function(result){
			console.log(result);
			var result = JSON.parse(result);
			$("input[name=<?php echo $this->security->get_csrf_token_name(); ?>]").each(function() { 
				$(this).val(result.csrf.hash);
			});
			$('#gambar_temp').html("<a href='<?=base_url('images/bayar/');?>"+result.nilai[0]['gambar']+"' data-fancybox><img src='<?=base_url('images/bayar/');?>"+result.nilai[0]['gambar']+"' style='width:200px; height:100px' alt='' /></a>");
			$('input[name=bayar]').val(result.nilai[0]['bayar'].toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
			$('textarea[name=keterangan]').val(result.nilai[0]['keterangan']);		
			$('textarea[name=keterangan]').prop('disabled',true);
			$(".se-pre-con").fadeOut("slow");
		})
	});
	$('.example tbody').on('click', '.bayar', function (e) {
		e.preventDefault();
		$(".se-pre-con").fadeIn("slow");
		$('input[type=file]').val('');
		$('input[name=id_kendaraan]').val($(this).closest('tr').find("td:first").text());
		$('input[name=plat]').val($(this).closest('tr').find('td:eq(2)').text());			
		$('button[name=submit]').val('bayar');

		$.post("<?=base_url('Admin/ajax_transaksi_bayar'); ?>", {'check' : 1, 'id_kendaraan' : $(this).closest('tr').find("td:first").text()}, function(result){
			var result = JSON.parse(result);
			$("input[name=<?php echo $this->security->get_csrf_token_name(); ?>]").each(function() { 
				$(this).val(result.csrf.hash);
			});
			$('#gambar_temp').html('<input type="file" class="form-control" name="gambar">');
			$('input[name=bayar]').val(result.nilai[0]['nilai'].toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
			$('textarea[name=keterangan]').prop('disabled',false);
			$(".se-pre-con").fadeOut("slow");
		})
	})
	$('.example tbody').on('click', '.edit', function (e) {
		e.preventDefault();
		window.location.replace("<?=base_url('Admin/kendaraan_modif?id_kendaraan=');?>"+$(this).closest('tr').find("td:first").text());
	})
	$('.example tbody').on('click', '.hapus', function (e) {
		e.preventDefault();
		swal({
			title: "Apakah Anda yakin?",
			text: "Sekali dihapus, Anda tidak dapat lagi mengembalikannya!",
			icon: "warning",
			buttons: true,
			dangerMode: true,
		}).then((willDelete) => {
			if (willDelete) {
				$(".se-pre-con").fadeIn("slow");
				$.post("<?=base_url('Admin/ajax_kendaraan_hapus'); ?>", {'hapus' : 1, 'id_kendaraan' : $(this).closest('tr').find("td:first").text(), <?php echo $this->security->get_csrf_token_name(); ?>: $("input[name=<?php echo $this->security->get_csrf_token_name(); ?>]").val()}, function(result){
					location.reload();
				});	
			}
		});	
	});
	$('button[type=submit]').click(function(e){
		if($('input[type=file]').val()==''){
			e.preventDefault();
			swal({
			  title: "Error",
			  text: "Harap Upload file",
			  icon: "warning",
			});
		}
	});
	$('input[type=file]').bind('change', function() {
		var ukuran = this.files[0].size/1024/1024;
		if(ukuran>2){
			swal({
			  title: "Error",
			  text: "Ukuran Gambar melebih batas yang ditentukan",
			  icon: "warning",
			});		
			$(this).val('');
		}else{
			var arr = $(this).val().split('.');
			var tipe = arr[(arr.length)-1];
			if(!(tipe=='jpg' || tipe=='jpeg' || tipe=='png')){
				alert('Tipe data tidak sesuai ketentuan');
				$(this).val('');
			}	
		}			
	});	
	$('button[name=tambah]').click(function(e){
		e.preventDefault();
		window.location.replace("<?=base_url('Admin/kendaraan_modif');?>");
	});
</script>