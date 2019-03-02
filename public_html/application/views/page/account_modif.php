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
						<li><a href="<?=base_url('Admin/Account');?>">Account</a></li>
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
						<strong class="card-title">Account Modif</strong>
					</div>
					<div class="card-body">
						<?=$this->session->flashdata('account_modif');?>
						<?=$this->session->flashdata('message');?>
						<div style="color: red;"><?=validation_errors(); ?></div>
						<?=form_open_multipart('Admin/account_modif','id="form_temp"');?>
							<input type="hidden" id='token' name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
							<div class="form-group row">
								<label class="control-label col-md-4" for="nama_lengkap">Nama Lengkap</label>
								<div class="col-md-7">
									<input type='hidden' class='form-control' name='uniqid' value='<?=uniqid(); ?>'>
									<input type="hidden" class="form-control" name='id_account' value='<?=(isset($load[0]->id_account) ? $load[0]->id_account :(isset($_POST['id_account']) ? $_POST['id_account'] : (isset($_GET['id_account']) ? $_GET['id_account'] : ''))); ?>'>
									<input type="text" class="form-control" name='nama_lengkap' value='<?=(isset($load[0]->nama_lengkap) ? $load[0]->nama_lengkap : (isset($_POST['nama_lengkap']) ? $_POST['nama_lengkap'] : '')); ?>'  
										data-validation="required length" 
										data-validation-length="6-255">
								</div>
							</div>
							<div class="form-group row">
								<label class="control-label col-md-4" for="username">Username</label>
								<div class="col-md-7">
									<input type="text" class="form-control" name='username' value='<?=(isset($load[0]->username) ? $load[0]->username : (isset($_POST['username']) ? $_POST['username'] : '')); ?>' 
										data-validation="required length" 
										data-validation-length="6-255">
									<small class="form-text text-danger" id='error'></small>
								</div>
							</div>
							<div class="form-group row">
								<label class="control-label col-md-4" for="password">Password</label>
								<div class="col-md-7">
									<input type="password" class="form-control" name='password' value='<?=(isset($load[0]->password) ? $load[0]->password : (isset($_POST['password']) ? $_POST['password'] : '')); ?>' 
										data-validation="required length" 
										data-validation-length="6-255">
								</div>
							</div>
							<div class="form-group row">
								<label class="control-label col-md-4" for="password">Re-Password</label>
								<div class="col-md-7">
									<input type="password" class="form-control" name='re_password' value='<?=(isset($load[0]->password) ? $load[0]->password : (isset($_POST['re_password']) ? $_POST['re_password'] : '')); ?>' 
										data-validation="required">
								</div>
							</div>
							<div class="form-group row">
								<label class="control-label col-md-4" for="level">Level</label>
								<div class="col-md-7">
									<select type='text' class='form-control' name='level' data-validation="required">
										<option value='' selected></option>
										<?php
											foreach($level as $key=>$value){
												echo 'value = '.$value->nama.'<br>';
												echo "<option value='".cetak($value->nama)."' ".((isset($load) && ($load[0]->level==$value->nama)) ? 'selected' : ((isset($_POST['level']) && (cetak($_POST['level'])==cetak($value->nama))) ? 'selected' : '')).">".cetak($value->nama)."</option>";
											}
										?>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label class="control-label col-md-4" for="notlpn">NoTlpn</label>
								<div class="col-md-7">
									<input type="text" class="form-control only_angka" name='notlpn' value='<?=(isset($load[0]->notlpn) ? $load[0]->notlpn : (isset($_POST['notlpn']) ? $_POST['notlpn'] : '')); ?>' 
										data-validation="length" 
										data-validation-length="max20">
								</div>
							</div>
							<div class="form-group row">
								<label class="control-label col-md-4" for="alamat">Alamat</label>
								<div class="col-md-7">
									<input type="text" class="form-control" name='alamat' value='<?=(isset($load[0]->alamat) ? $load[0]->alamat : (isset($_POST['alamat']) ? $_POST['alamat'] : '')); ?>' 
										data-validation="length" 
										data-validation-length="max1000">
								</div>
							</div>
							<div class="form-group row">
								<label class="control-label col-md-4" for="alamat">Email</label>
								<div class="col-md-7">
									<input type="text" class="form-control" name='email' value='<?=(isset($load[0]->email) ? $load[0]->email : (isset($_POST['email']) ? $_POST['email'] : '')); ?>' 
										data-validation="required" >
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
$('input[name=username]').change(function(e){
	abc();
});
abc = function(){
	$.post("<?=base_url('Admin/ajax_account_double'); ?>", {'check' : 1, 'id_account' : $('input[name=id_account]').val(), 'username' : $('input[name=username]').val(), <?php echo $this->security->get_csrf_token_name(); ?>: $("input[name=<?php echo $this->security->get_csrf_token_name(); ?>]").val(), 'username' : $('input[name=username]').val()}, function(result){
		// console.log(result);
		$(".se-pre-con").fadeIn("slow");
		var result = JSON.parse(result);
		$("input[name=<?php echo $this->security->get_csrf_token_name(); ?>]").each(function() { 
			$(this).val(result.csrf.hash);
		});
		if(result.nilai){
			$("input[name=username]").removeClass("is-invalid");
			$('#error').html('');
		}else{
			$("input[name=username]").addClass("is-invalid");
			$('#error').html('Username Sudah Ada');
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