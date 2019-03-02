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
						<li class="active">Dashboard</li>
					</ol>
				</div>
			</div>
		</div>
	</div>
	<div class="content mt-3">	
		<div class="animated fadeIn">
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-header">
							<?=$this->session->flashdata('hak_akses');?>
							<?=$this->session->flashdata('message');?>
							<?=$this->session->flashdata('check_kendaraan');?>
							<div class="form-group row">
								<label class="control-label col-md-1" for="merek">Level</label>
								<div class="col-md-2">
									<select type="text" class="form-control" id='level'>
										<option value='1' selected>1</option>
										<option value='2'>2</option>
									</select>
								</div>
							</div>						
						</div>
						<div class="card-body">
							<div class='container' id='temp_isi'>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- .animated -->	
	</div>
<?php
	include('footer.php');
?>
<script type="text/javascript">
$(document).ready(function(){
	refreshTable();
});

function refreshTable(){
	$('#temp_isi').load("<?=base_url('Admin/monitoring?level=');?>"+$('#level').val(), function(result){
		console.log(result);
		setTimeout(refreshTable, 4000);
    });
}
</script>