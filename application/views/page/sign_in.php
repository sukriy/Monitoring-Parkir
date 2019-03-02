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
					<?=$this->session->flashdata('sign_in');?>
					<?=$this->session->flashdata('message');?>
                    <form method='post' action='<?=base_url('Admin/sign_in'); ?>' id="form_temp">
						<input type="hidden" id='token' name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" placeholder="Username" name='username' value="<?php echo (isset($_POST['username']) ? set_value('username') : '' ); ?>" data-validation="required">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" placeholder="Password" name='password' data-validation="required">
                        </div>
						
                        <button type="submit" class="btn btn-success btn-flat m-b-30 m-t-30" name='submit'>Sign in</button>
                        <div class="register-link m-t-15 text-center">
                            <p>Don't have account ? <a href="<?=base_url('Admin/sign_up'); ?>"> Sign Up Here</a></p>
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
	$.validate();
</script>