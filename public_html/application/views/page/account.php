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
						<li class="active">Account</li>
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
							<?php 
								// if($_SESSION['level']=='admin'){
									// $hak = array();
									// foreach($menulist[strtolower($this->router->fetch_method())] as $key=>$value){
										// array_push($hak, $key); 
									// }
								// }else{ 
									$hak = $hak_akses[$_SESSION['level']][strtolower($this->router->fetch_method())];
								// }
								if(array_search("baru",$hak)!=''){
									echo "<button type='submit' class='btn btn-success btn-flat m-b-30 m-t-30' name='tambah'>Tambah</button>";
								}
							?>
							<?=$this->session->flashdata('account');?>
							<?=$this->session->flashdata('message');?>
                        </div>
                        <div class="card-body">
							<div class='container'>
								<div class="table-responsive">
									<table class='table table-bordered table-hover compact example'>
										<thead>
											<tr>
												<th>ID_Account</th>
												<th>Nama_Lengkap</th>
												<th>Username</th>
												<th>Level</th>
												<th>Alamat</th>
												<th>NoTlpn</th>
												<th>Gambar</th>
												<th>Manage</th>
											</tr>
										</thead>
										<tbody>
										<?php
											foreach($load as $key=>$value){
												$ada = 0;
												if($value->gambar==''){
													$gambar='';
												}else{
													$base = base_url();
													$gambar="<a href='".$base."images/account/".$value->gambar."' data-fancybox><img src='".$base."images/account/".$value->gambar."' style='width:200px; height:100px' alt='' /></a>";
												}										
												echo "
												<tr>
													<td>".cetak($value->id_account)."</td>
													<td>".cetak($value->nama_lengkap)."</td>
													<td>".cetak($value->username)."</td>
													<td>".cetak($value->level)."</td>
													<td>".cetak($value->alamat)."</td>
													<td>".cetak($value->notlpn)."</td>
													<td>".$gambar."</td>
													<td>";
													
												if(array_search("edit",$hak)!=''){
													if($ada>0) echo "&nbsp;|&nbsp;";
													echo "<a href='#' class='edit' style='color:blue'>Edit</a>";
													$ada++;
												}
												if(array_search("hapus",$hak)!=''){
													if($ada>0) echo "&nbsp;|&nbsp;";
													echo "<a href='#' class='hapus' style='color:blue'>Hapus</a>";
													$ada++;
												}
												echo "</td>
												</tr>
												";
											}
										?>
										</tbody>
									</table>
								</div>
							</div>
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
	$('.example tbody').on('click', '.edit', function (e) {
		e.preventDefault();
		window.location.replace("<?=base_url('Admin/account_modif?id_account=');?>"+$(this).closest('tr').find("td:first").text());
	})
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
				$.post("<?=base_url('Admin/ajax_account_hapus'); ?>", {'hapus' : 1, 'id_account' : $(this).closest('tr').find("td:first").text(), <?php echo $this->security->get_csrf_token_name(); ?>: $("input[name=<?php echo $this->security->get_csrf_token_name(); ?>]").val()}, function(result){
					location.reload();
				});	
			}
		});	
	});
	$('button[name=tambah]').click(function(e){
		e.preventDefault();
		window.location.replace("<?=base_url('Admin/account_modif');?>");
	});
</script>