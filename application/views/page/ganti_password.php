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
						<li><a href="<?=base_url('Admin/Custom');?>">Custom</a></li>
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
						<strong class="card-title">Custom Modif</strong>
					</div>
					<div class="card-body">
						<?=$this->session->flashdata('message');?>
						<?=$this->session->flashdata('ganti_password');?>
						<div style="color: red;"><?=validation_errors(); ?></div>
						<?=form_open('Admin/Ganti_Password','id="form_temp"');?>
							<input type="hidden" id='token' name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
							<input type="hidden" name="uniqid" value="<?=uniqid(); ?>" />
							<div class="form-group row">
								<label class="control-label col-md-4" for="nilai">Password Lama</label>
								<div class="col-md-7">
									<input type="password" class="form-control" name='password_lama' value="<?=(isset($_POST['password_lama']) ? $_POST['password_lama'] : ''); ?>" 
										data-validation="required"
									>
								</div>
							</div>
							<div class="form-group row">
								<label class="control-label col-md-4" for="nilai">Password Baru</label>
								<div class="col-md-7">			
									<input type="password" class="form-control" name='pass_confirmation' value="<?=(isset($_POST['pass_confirmation']) ? $_POST['pass_confirmation'] : ''); ?>" 
										data-validation="required length"
										data-validation-length='6-255'
									>
								</div>
							</div>
							<div class="form-group row">
								<label class="control-label col-md-4" for="nilai">Password retype</label>
								<div class="col-md-7">
									<input type="password" class="form-control" name='pass' value="<?=(isset($_POST['pass']) ? $_POST['pass'] : ''); ?>" 
										data-validation="confirmation"
									>
								</div>
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-success btn-flat m-b-30 m-t-30 col-md-11" name='submit' value='<?=(isset($detail) ? 'edit' : 'baru'); ?>'><?=(isset($detail) ? 'EDIT' : 'INPUT BARU'); ?></button>
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
	$.validate({
		modules : 'security'
	});
</script>