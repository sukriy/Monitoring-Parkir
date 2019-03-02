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
						<li class="active">Custom</li>
					</ol>
				</div>
			</div>
		</div>
	</div>
	<div class="content mt-3">	
		<div class="animated fadeIn">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<?php 
							if($_SESSION['level']=='admin'){
								$hak = array();
								foreach($menulist[strtolower($this->router->fetch_method())] as $key=>$value){
									array_push($hak, $key); 
								} 
							}else{ 
								$hak = $hak_akses[$_SESSION['level']][strtolower($this->router->fetch_method())];
							}
						?>					
						<?=$this->session->flashdata('custom');?>
						<?=$this->session->flashdata('message');?>
						<div class="form-group row">
							<label for="text-input" class="form-control-label col-md-1"><strong class="card-title">Jenis</strong></label>
							<div class="col-md-2">
								<input type="hidden" id='token' name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
								<select data-placeholder="Pilih..." class="form-control" name='jenis'>
									<option value="" selected></option>
									<?php
										foreach($list->result() as $row){
											echo "<option value='".$row->id_kategori."'>".$row->nama_kategori."</option>";
										}
									?>
								</select>
							</div>
							<div class="col-md-2" id='tambah'>
								
							</div>
						</div>
					</div>
					<div class="card-body">

					</div>
				</div>
			</div>
		</div>
	</div>
<?php
	include('footer.php');
?>
<script type="text/javascript">
window.onload = function(e){
	$(".se-pre-con").fadeIn("slow");
	var jenis = "<?=$this->session->flashdata('id_kategori'); ?>";
	if(jenis!=""){
		$('select[name=jenis]').val(jenis);
		$('select[name=jenis]').change();
	} 
	$(".se-pre-con").fadeOut("slow");
}
abc = function(obj){
	// console.log('obj = '+obj);
	if(obj==''){
		$('.card-body').html('');
		$('#tambah').html('');		
	}else{
		$.post("<?=base_url('Admin/ajax_custom'); ?>", {'load':1, 'id_kategori':obj, <?=$this->security->get_csrf_token_name(); ?>: $("input[name=<?=$this->security->get_csrf_token_name(); ?>]").val()}, function(result){
			$(".se-pre-con").fadeIn("slow");
			var list = JSON.parse(result);
			$("input[name=<?php echo $this->security->get_csrf_token_name(); ?>]").each(function() { 
				$(this).val(list.csrf.hash);
			}); 

			var txt2 = "<?php if(array_search("baru",$hak)!='') echo "<button type='submit' class='btn btn-success btn-flat m-b-30 m-t-30' name='tambah'>Tambah</button>"; ?>";
			var txt = "<div class='container'><div class='table-responsive'><table id='table_temp' class='table table-striped table-bordered compact example' style='width:100%'><thead><tr><th>Jenis</th><th>Nama</th><th>Nilai</th><th>Keterangan</th><th>Manage</th></tr></thead><tbody>";				
			var edit;
			var hapus;
			$.each(list.nilai, function (index, value) {
				txt += "<tr><td>"+value.nama_kategori+"</td><td>"+value.nama+"</td><td>"+value.nilai+"</td><td>"+value.keterangan+"</td><td>";
				edit = 
					<?php
					if(array_search("edit",$hak)!='')
						echo 1;
					else
						echo 0;
					?>;
				hapus = 
					<?php
					if(array_search("hapus",$hak)!='')
						echo 1;
					else
						echo 0;
					?>;
				if(edit==1)
					txt += "<a href='#' class='edit' data-id='"+value['id_custom']+"' style='color:blue'>Edit</a>";
				if(hapus==1){
					if(edit==1)
						txt += "&nbsp;|&nbsp;";
					txt += "<a href='#' class='hapus' data-id='"+value['id_custom']+"' style='color:blue'>Hapus</a>";
				}
				txt += "</td></tr>";
			});
			txt += "</tbody></table></div></div>";
			$('.card-body').html(txt);
			$('#tambah').html(txt2);
			
			$('.example tbody').on('click', '.edit', function (e) {
				e.preventDefault();
				window.location.replace("<?=base_url('Admin/custom_modif?id=');?>"+$('select[name=jenis]').val()+"&id_custom="+$(this).attr('data-id'));
			});
			$('button[name=tambah]').click(function(e){
				e.preventDefault();
				window.location.replace("<?=base_url('Admin/custom_modif?id=');?>"+$('select[name=jenis]').val());
			});

			$('.example tbody').on('click', '.hapus', function (e) {
				e.preventDefault();
				swal({
					title: "Apakah Anda yakin?",
					text: "Sekali dihapus, Anda tidak dapat lagi mengembalikannya!",
					icon: "warning",
					buttons: true,
					dangerMode: true,
				}).then((willDelete) => {
					if (willDelete) {
						$(".se-pre-con").fadeIn("slow");
						$.post("<?=base_url('Admin/ajax_custom_hapus'); ?>", {'hapus' : 1, 'id_kategori' : $('select[name=jenis]').val(), 'id_custom' : $(this).attr('data-id'), <?php echo $this->security->get_csrf_token_name(); ?>: $("input[name=<?php echo $this->security->get_csrf_token_name(); ?>]").val()}, function(result){
							var list2 = JSON.parse(result);
							$("input[name=<?php echo $this->security->get_csrf_token_name(); ?>]").each(function() { 
								$(this).val(list2.csrf.hash);
							});
							abc($('select[name=jenis]').val());						
						});	
					}
					$(".se-pre-con").fadeOut("slow");
				});	
			});
			$('#table_temp').DataTable({
				"scrollX": true
			});		
			$(".se-pre-con").fadeOut("slow");
		});
	}		
}
	$(document).ready(function(){
		$('select[name=jenis]').change(function(e){
			abc($('select[name=jenis]').val());
		});
		$('button[type=submit]').click(function(e){
			e.preventDefault();
			window.location.replace("<?=base_url('Admin/Custom_Modif?id=');?>"+$('select[name=jenis]').val());
		});
	});
</script>