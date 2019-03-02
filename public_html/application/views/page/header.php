<?php
	include('assets.php');
?>
<body class='noprint'>
<aside id="left-panel" class="left-panel"> 
	<nav class="navbar navbar-expand-sm navbar-default">
		<div class="navbar-header">
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
				<i class="fa fa-bars"></i>
			</button>
			<a class="navbar-brand" href="./"><img src="<?=base_url(); ?>images/logo.png" alt="Logo"></a>
		</div>
		<div id="main-menu" class="main-menu collapse navbar-collapse">
			<ul class="nav navbar-nav">
				<li><?=anchor(base_url(),                 '<i class="menu-icon fa fa-dashboard"></i>Home'); ?></li>
				<?php
					// print_r($_SESSION);
					if($_SESSION['level']=='admin'){
						foreach($menulist as $key=>$value){
							foreach($value as $key2=>$value2){
								if($value2['link']!=''){
									echo $value2['link'];
								}
							}
						}						
					}else{
						foreach($hak_akses[$_SESSION['level']] as $key=>$value){
							foreach($value as $key2=>$value2){
								if($menulist[$key][$value2]['link']!=''){
									echo $menulist[$key][$value2]['link'];
								}
							}
						}						
					}
				?>
			</ul>
		</div>
	</nav>
</aside>
<div id="right-panel" class="right-panel">
	<header id="header" class="header">
		<div class="header-menu">
			<div class="col-sm-7">
				<a id="menuToggle" class="menutoggle pull-left"><i class="fa fa fa-tasks"></i></a>
				<div class="header-left">
				</div>
			</div>
			<div class="col-sm-5">
				<div class="user-area dropdown float-right">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<?php
						if($_SESSION['gambar']!=''){
							$gmbr = base_url('images/account/').$_SESSION['gambar'];
						}else{
							$gmbr = base_url('images/account/images.png');
						}
					?>					
						<img class="user-avatar rounded-circle" src="<?=$gmbr; ?>" alt="User Avatar">
					</a>
					<div class="user-menu dropdown-menu"> 
						<a class="nav-link" href="#"><i class="fa fa- user"></i><?=$_SESSION['username']; ?></a>
						<a class="nav-link" href="<?=base_url('Admin/Ganti_Password'); ?>"><i class="fa fa -cog"></i>Ganti Password</a>
						<a class="nav-link" href="<?=base_url('Admin/Logout'); ?>"><i class="fa fa-power -off"></i>Logout</a>
					</div>
				</div>
				<div class="language-select dropdown" id="language-select">

				</div>
			</div>
		</div>
	</header>