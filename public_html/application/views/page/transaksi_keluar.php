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
						<li class='active'>Transaksi_Keluar</li>
					</ol>
				</div>
			</div>
		</div>
	</div>
	<div class="content mt-3">	
		<div class="animated fadeIn">
			<div class="col-md-6">
				<div class="card">
					<div class="card-header">
						<strong class="card-title">Transaksi Keluar</strong>
					</div>
					<div class="card-body">
						<?=$this->session->flashdata('message');?>
						<div style="color: red;"><?=validation_errors(); ?></div>
						<?=form_open('Admin/transaksi_keluar','id="form_temp"');?>
							<div class="form-group row">
								<label class="control-label col-md-4" for="notlpn">Plat</label>
								<div class="col-md-7">
									<input type='text' class='form-control' name='plat'
										data-validation="required"
									>
								</div>
							</div>
							<div class="form-group row">
								<label class="control-label col-md-4" for="notlpn">Transaksi</label>
								<div class="col-md-7">
									<input type="hidden" id='token' name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
									<input type='hidden' class='form-control' name='uniqid' value='<?=uniqid(); ?>'>
									<input type='text' class='form-control only_angka' name='id_transaksi' value="<?=(isset($_POST['username']) ? $_POST['username'] : ''); ?>"
										data-validation="required"
									>
								</div>
							</div>
							<div class="form-group row">
								<label class="control-label col-md-4" for="notlpn">Waktu</label>
								<div class="col-md-7">
									<input type='text' readonly class='form-control' name='waktu'
										data-validation="required"
									>
								</div>
							</div>
							<div class="form-group row">
								<label class="control-label col-md-4" for="notlpn">Lama</label>
								<div class="col-md-7">
									<input type='text' readonly class='form-control' name='lama'
										data-validation="required"
									>
								</div>
							</div>
							<div class="form-group row">
								<label class="control-label col-md-4" for="notlpn">Bayar</label>
								<div class="col-md-7">
									<input type='text' readonly class='form-control' name='bayar'
										data-validation="required"
									>
								</div>
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-success btn-flat m-b-30 m-t-30 col-md-11" name='submit' value='submit'>Bayar</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
	include('footer.php');
?>
<script>
$(window).load(function() {
	$(".se-pre-con").fadeIn("slow");
	var transaksi_keluar = "<?=$this->session->flashdata('transaksi_keluar'); ?>";
	if(transaksi_keluar!=""){
		window.open("<?=base_url('Admin/transaksi_print?print=');?>"+transaksi_keluar);
	}
	$(".se-pre-con").fadeOut("slow");
});
$('input[name=id_transaksi], input[name=plat]').change(function(e){
	e.preventDefault();
	if($('input[name=id_transaksi]').val()!='' && $('input[name=plat]').val()!=''){
		$(".se-pre-con").fadeIn("slow");	
		$.post("<?=base_url('Admin/ajax_transaksi_keluar'); ?>", {'check' : 1, 'id_transaksi' : $('input[name=id_transaksi]').val(), 'plat' : $('input[name=plat]').val()}, function(result){
			// console.log(result);
			var result = JSON.parse(result);
			$("input[name=<?php echo $this->security->get_csrf_token_name(); ?>]").each(function() { 
				$(this).val(result.csrf.hash);
			});
			if(result.nilai == false){
				swal({
					title: "Error",
					text: "Tidak Ditemukan",
					icon: "warning",
				});
			}else{
				$('input[name=waktu]').val(result.nilai[0].tgl_masuk);
				$('input[name=lama]').val(result.nilai[0].lama);
				$('input[name=bayar]').val(result.nilai[0].nominal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
			}
			$(".se-pre-con").fadeOut("slow");
		});
	}
});
$.validate();
</script>