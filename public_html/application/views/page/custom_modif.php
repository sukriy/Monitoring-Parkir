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
						<div style="color: red;"><?=validation_errors(); ?></div>
						<?=form_open('Admin/Custom_Modif/','id="form_temp"');?>
							<input type="hidden" id='token' name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
							<div class="form-group row">
								<label class="control-label col-md-4" for="nilai">Nama</label>
								<div class="col-md-7">
									<?php
										$id_kategori = (isset($_POST['id_kategori']) ? $_POST['id_kategori'] : (isset($_GET['id']) ? $_GET['id'] : ''));
										echo "<input type='hidden' class='form-control' name='id_kategori' value='".$id_kategori."'>";
										$id_custom = (isset($_POST['id_custom']) ? $_POST['id_custom'] : (isset($_GET['id_custom']) ? $_GET['id_custom'] : ''));
										echo "<input type='hidden' class='form-control' name='id_custom' value='".$id_custom."'>";
										echo "<input type='hidden' class='form-control' name='uniqid' value='".uniqid()."'>";
									?>
									<input type="text" class="form-control" name='nama' value="<?=(isset($detail[0]) ? $detail[0]->nama : (isset($_POST['nama']) ? $_POST['nama'] : '')); ?>" data-validation="required">
									<small class="form-text text-danger" id='error'></small>
								</div>
							</div>
							<div class="form-group row">
								<label class="control-label col-md-4" for="nilai">Nilai</label>
								<div class="col-md-7">
									<input type='text' class="form-control angka" name='nilai' value="<?=(isset($detail) ? $detail[0]->nilai : (isset($_POST['nilai']) ? $_POST['nilai'] : '')); ?>"
										data-validation="angka_9"
									>
								</div>
							</div>
							<div class="form-group row">
								<label class="control-label col-md-4" for="keterangan">Keterangan</label>
								<div class="col-md-7">
									<textarea class="form-control" rows="5" name='keterangan' data-validation="length" data-validation-length="max1000"><?=(isset($detail) ? $detail[0]->keterangan : (isset($_POST['keterangan']) ? $_POST['keterangan'] : '')); ?></textarea>
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
window.onload = function(e){
	abc();
}
abc = function(){
	$.post("<?=base_url('Admin/ajax_custom_double'); ?>", {'hapus' : 1, 'id_kategori' : $('input[name=id_kategori]').val(), 'id_custom' : $('input[name=id_custom]').val(), <?php echo $this->security->get_csrf_token_name(); ?>: $("input[name=<?php echo $this->security->get_csrf_token_name(); ?>]").val(), 'nama' : $('input[name=nama]').val()}, function(result){
		$(".se-pre-con").fadeIn("slow");
		var result = JSON.parse(result);
		$("input[name=<?php echo $this->security->get_csrf_token_name(); ?>]").each(function() { 
			$(this).val(result.csrf.hash);
		});
		if(result.nilai){
			$("input[name=nama]").removeClass("is-invalid");
			$('#error').html('');
		}else{
			$("input[name=nama]").addClass("is-invalid");
			$('#error').html('Nama Sudah Ada');
		}
		$(".se-pre-con").fadeOut("slow");
	});		
}
$.formUtils.addValidator({
	name : 'angka_9',
	validatorFunction : function(value, $el, config, language, $form) {
		var nilai = value.replace(/,/g , '').length;
		if(nilai>9)
			return false;
		else
			return true;
	},
	errorMessage : 'maksimal 9 digit angka',
	errorMessageKey: 'badEvenNumber'
});
	$('input[name=nama]').change(function(e){
		abc();
	});
	$('button[name=submit]').click(function(e){
		if($('#error').html()!=''){
			swal({
			  title: "Error",
			  text: "Nama Sudah Ada",
			  icon: "warning",
			});
			e.preventDefault();
		}
	});
	$.validate();
</script>