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
						<li><a href="<?=base_url('Admin/Transaksi');?>">Transaksi</a></li>
						<li class="active">Transaksi_Modif</li>					
					</ol>
				</div>
			</div>
		</div>
	</div>
        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">
					<div class="col-lg-6">
						<div class="card">
							<div class="card-header">
								<strong class="card-title">Transaksi Modif</strong>
							</div>
							<div class="card-body">
								<?=$this->session->flashdata('transaksi_modif');?>
								<?=$this->session->flashdata('message');?>
								<div style="color: red;"><?=validation_errors(); ?></div>
								<?=form_open('Admin/transaksi_modif','id="form_temp"');?>
									<input type="hidden" id='token' name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
									<div class="form-group row">
										<label class="control-label col-md-4" for="plat">Lantai</label>
										<div class="col-md-7">
											<input type='hidden' class='form-control' name='uniqid' value='<?=uniqid(); ?>'>
											<select name='lokasi' class='form-control'>
												<option value='' selected></option>
												<?php
													foreach($lantai as $key=>$value){
														if((array_count_values($byk[$value->nama])[0])>0){
															$temp=array_count_values($byk[$value->nama])[0];
														}else{
															$temp=0;
														}
														echo "<option value='".$value->nama."'>".$value->nama." (available space : ".$temp.")</option>";
													}
												?>
											</select>
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
            </div><!-- .animated -->
        </div><!-- .content -->

<?php
	include('footer.php');
?>
<script type="text/javascript">
window.onload = function(e){
	$(".se-pre-con").fadeIn("slow");
	var transaksi_print = "<?=$this->session->flashdata('transaksi_print'); ?>";
	if(transaksi_print!=""){
		$.post("<?=base_url('Admin/ajax_transaksi_id'); ?>", {'print' : 1, 'id_transaksi' : transaksi_print, <?php echo $this->security->get_csrf_token_name(); ?>: $("input[name=<?php echo $this->security->get_csrf_token_name(); ?>]").val()}, function(result){
			var list = JSON.parse(result);
			$("input[name=<?php echo $this->security->get_csrf_token_name(); ?>]").each(function() { 
				$(this).val(list.csrf.hash);
			}); 
			if(list.nilai == true)
				window.open("<?=base_url('Admin/transaksi_print?print=');?>"+transaksi_print);
		});
	} 
	$(".se-pre-con").fadeOut("slow");
}
</script>