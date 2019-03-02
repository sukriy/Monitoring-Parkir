<?php
	include('assets.php');
?> 
<body class="bg-dark">
    <div class="sufee-login d-flex align-content-center flex-wrap">
        <div class="container">
            <div class="login-content">
                <div class="login-logo">
                    <a href="<?=base_url('Home'); ?>">
                        <img class="align-content" src="<?=base_url(); ?>images/logo.png" alt="">
                    </a>
                </div>
                <div class="login-form">
					<div style="color: red;"><?=validation_errors(); ?></div>
					<?=$this->session->flashdata('tiket');?>
					<?=$this->session->flashdata('message');?>
					<?=form_open('Home','id="form_temp"');?>
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
<?php
	include('footer.php');
?>
<script type="text/javascript">
$.validate();
window.onload = function(e){
	$(".se-pre-con").fadeIn("slow");
	var transaksi_print = "<?=$this->session->flashdata('transaksi_print'); ?>";
	if(transaksi_print!=""){
		$.post("<?=base_url('Home/ajax_transaksi_id'); ?>", {'print' : 1, 'id_transaksi' : transaksi_print, <?php echo $this->security->get_csrf_token_name(); ?>: $("input[name=<?php echo $this->security->get_csrf_token_name(); ?>]").val()}, function(result){
			var list = JSON.parse(result);
			$("input[name=<?php echo $this->security->get_csrf_token_name(); ?>]").each(function() { 
				$(this).val(list.csrf.hash);
			}); 
			if(list.nilai == true)
				window.open("<?=base_url('Home/transaksi_print?print=');?>"+transaksi_print);
		});
	} 
	$(".se-pre-con").fadeOut("slow");
}
</script>