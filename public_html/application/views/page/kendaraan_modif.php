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
						<li><a href="<?=base_url('Admin/Kendaraan');?>">Kendaraan</a></li>
						<li class="active">Modif</li>
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
						<strong class="card-title">Kendaraan Modif</strong>
					</div>
					<div class="card-body">
						<?=$this->session->flashdata('kendaraan_modif');?>
						<?=$this->session->flashdata('message');?>
						<div style="color: red;"><?=validation_errors(); ?></div>
						<?=form_open_multipart('Admin/kendaraan_modif','id="form_temp"');?>
							<input type="hidden" id='token' name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
							<div class="form-group row">
								<label class="control-label col-md-4" for="plat">Plat</label>
								<div class="col-md-7">
									<input type='hidden' class='form-control' name='uniqid' value='<?=uniqid(); ?>'>
									<input type="hidden" class="form-control" name='id_kendaraan' value='<?=(isset($load[0]->id_kendaraan) ? $load[0]->id_kendaraan :(isset($_POST['id_kendaraan']) ? $_POST['id_kendaraan'] : (isset($_GET['id_kendaraan']) ? $_GET['id_kendaraan'] : ''))); ?>'>
									<input type="text" class="form-control" name='plat' value='<?=(isset($load[0]->plat) ? $load[0]->plat : (isset($_POST['plat']) ? $_POST['plat'] : '')); ?>'  
										data-validation="required length" 
										data-validation-length="max10">
									<small class="form-text text-danger" id='error'></small>
								</div>
							</div>
							<div class="form-group row">
								<label class="control-label col-md-4" for="merek">User</label>
								<div class="col-md-7">
									<select type="text" class="form-control" name='user' data-validation='required'>
									<?php
									if($_SESSION['level']=='manager'){
										echo "<option value='' selected></option>";
										foreach($user as $key=>$value){
											echo "<option value='".$value->id_account."' ".((isset($load[0]->id_account) && $load[0]->id_account==$value->id_account) ? 'selected' : ((isset($_POST['user']) && $_POST['user']==$value->id_account) ? 'selected' : '')).">".$value->nama_lengkap."</option>";
										}										
									}else{
										echo "<option value='".$_SESSION['id_account']."'>".$_SESSION['nama_lengkap']."</option>";
									} 
									?>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label class="control-label col-md-4" for="merek">Merek</label>
								<div class="col-md-7">
									<input type="text" class="form-control" name='merek' value='<?=(isset($load[0]->merek) ? $load[0]->merek : (isset($_POST['merek']) ? $_POST['merek'] : '')); ?>' 
										data-validation="required length" 
										data-validation-length="max255">
								</div>
							</div>
							<div class="form-group row">
								<label class="control-label col-md-4" for="tipe">Tipe</label>
								<div class="col-md-7">
									<input type="text" class="form-control" name='tipe' value='<?=(isset($load[0]->tipe) ? $load[0]->tipe : (isset($_POST['tipe']) ? $_POST['tipe'] : '')); ?>' 
										data-validation="required length"
										data-validation-length="max255">
								</div>
							</div>
							<div class="form-group row">
								<label class="control-label col-md-4" for="keterangan">Gambar</label>
								<div class="col-md-7">
									<input type="file" class="form-control" name='gambar'>
								</div>
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-success btn-flat m-b-30 m-t-30 col-md-11" name='submit' value=<?=(isset($load) ? 'edit' : 'baru');?>><?=(isset($load) ? 'EDIT' : 'INPUT BARU'); ?></button>
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
window.onload = function(e){
	abc();
}
$('input[name=plat]').change(function(e){
	abc();
});
abc = function(){
	$.post("<?=base_url('Admin/ajax_kendaraan_double'); ?>", {'check' : 1, 'id_kendaraan' : $('input[name=id_kendaraan]').val() , 'plat' : $('input[name=plat]').val(), <?php echo $this->security->get_csrf_token_name(); ?>: $("input[name=<?php echo $this->security->get_csrf_token_name(); ?>]").val()}, function(result){
		// console.log(result);
		$(".se-pre-con").fadeIn("slow");
		var result = JSON.parse(result);
		$("input[name=<?php echo $this->security->get_csrf_token_name(); ?>]").each(function() { 
			$(this).val(result.csrf.hash);
		});
		if(result.nilai){
			$("input[name=plat]").removeClass("is-invalid");
			$('#error').html('');
		}else{
			$("input[name=plat]").addClass("is-invalid");
			$('#error').html('Plat Sudah Ada');
		}
		$(".se-pre-con").fadeOut("slow");
	});		
}
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
$('button[name=submit]').click(function(e){
	if($('#error').html()!=''){
		swal({
		  title: "Error",
		  text: "Username Sudah Ada",
		  icon: "warning",
		});
		e.preventDefault();
	}
})
	$.validate();
</script>