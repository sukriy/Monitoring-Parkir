<?php
	include('assets.php');
?>
<body class="bg-dark">
    <div class="sufee-login d-flex align-content-center flex-wrap">
        <div class="container">
            <div class="login-content">
                <div class="login-logo">
                    <a href="<?=base_url('Admin'); ?>">
                        <img class="align-content" src="<?=base_url(); ?>images/logo.png" alt="">
                    </a>
                </div>
                <div class="login-form">
					<div style="color: red;"><?=validation_errors(); ?></div>
					<?=$this->session->flashdata('register');?>
					<?=$this->session->flashdata('message');?>
                    <form method='post' action='<?=base_url('Admin/sign_up'); ?>' id="form_temp">
						<input type="hidden" id='token' name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
						<input type='hidden' class='form-control' name='uniqid' value='<?=uniqid(); ?>'>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" placeholder="Username" name='username' value="<?php echo (isset($_POST['username']) ? set_value('username') : '' ); ?>" 
								data-validation="required length"
								data-validation-length='6-255'
							>
							<small class="form-text text-danger" id='error'></small>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" placeholder="Password" name='pass_confirmation' 
								data-validation="required length"
								data-validation-length='6-255'
							>
                        </div>
                        <div class="form-group">
                            <label>Re-Password</label>
                            <input type="password" class="form-control" placeholder="Password" name='pass'
								data-validation="confirmation"
							>
                        </div>
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" class="form-control" placeholder="nama_lengkap" name='nama_lengkap' value="<?php echo (isset($_POST['nama_lengkap']) ? set_value('nama_lengkap') : '' ); ?>"
								data-validation="required length"
								data-validation-length='6-255'
							>
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <input type="text" class="form-control" placeholder="Alamat" name='alamat' value="<?php echo (isset($_POST['alamat']) ? set_value('alamat') : '' ); ?>"
								data-validation="length"
								data-validation-length='max255'
							>
                        </div>
                        <div class="form-group">
                            <label>NoTelepon</label>
                            <input type="text" class="form-control only_angka" placeholder="NoTelepon" name='notlpn' value="<?php echo (isset($_POST['notlpn']) ? set_value('notlpn') : '' ); ?>"
								data-validation="length"
								data-validation-length='max255'
							>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" placeholder="Email" name='email' value="<?php echo (isset($_POST['email']) ? set_value('email') : '' ); ?>"
								data-validation="required"							
							>
							<small class="form-text text-danger" id='error0'></small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-flat m-b-30 m-t-30" name='submit'>Register</button>
                        <div class="register-link m-t-15 text-center">
                            <p>Already have account ? <a href="<?=base_url('Admin'); ?>"> Sign in</a></p>
                        </div>
					
					
											
                    </form>
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
		abcd();
	}
	$('input[name=username]').change(function(e){
		abc();
	});
	$('input[name=email]').change(function(e){
		abcd();
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
	abcd = function(){
		$.post("<?=base_url('Admin/ajax_email_double'); ?>", {'check' : 1, 'email' : $('input[name=email]').val(), <?php echo $this->security->get_csrf_token_name(); ?>: $("input[name=<?php echo $this->security->get_csrf_token_name(); ?>]").val()}, function(result){
			// console.log(result);
			$(".se-pre-con").fadeIn("slow");
			var result = JSON.parse(result);
			$("input[name=<?php echo $this->security->get_csrf_token_name(); ?>]").each(function() { 
				$(this).val(result.csrf.hash);
			});
			if(result.nilai){
				$("input[name=username]").removeClass("is-invalid");
				$('#error0').html('');
			}else{
				$("input[name=username]").addClass("is-invalid");
				$('#error0').html('Email Sudah Ada');
			}
			$(".se-pre-con").fadeOut("slow");
		});		
	}
	$('button[name=submit]').click(function(e){
		if($('#error').html()!=''){
			swal({
			  title: "Error",
			  text: "Username Sudah Ada",
			  icon: "warning",
			});
			e.preventDefault();
		}
		if($('#error0').html()!=''){
			swal({
			  title: "Error",
			  text: "Email Sudah Ada",
			  icon: "warning",
			});
			e.preventDefault();
		}
	})	
	$.validate({
		modules : 'security'
	});
</script>