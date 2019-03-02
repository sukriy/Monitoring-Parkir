		</div>
	</div>
	<div class="se-pre-con"></div>
	<style>	
		.no-js #loader { display: none;  }
		.js #loader { display: block; position: absolute; left: 100px; top: 0; }
		.se-pre-con {
			position: fixed;
			left: 0px;
			top: 0px;
			width: 100%;
			height: 100%;
			z-index: 9999;
			background: url(<?=base_url('images/Preloader.gif'); ?>) center no-repeat;
		}
	</style>
	
	<script src='https://code.jquery.com/jquery-3.3.1.js'></script>

	<script src="<?=base_url(); ?>assets/js/popper.min.js"></script>
	<script src="<?=base_url(); ?>assets/js/plugins.js"></script>
	<script src="<?=base_url(); ?>assets/js/main.js"></script>
	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

	<script src="<?=base_url(); ?>assets/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	
	<script src='https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js'></script>
	<script src='https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js'></script>
	<script src='https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js'></script>
	<script src='https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js'></script>
	<script src='https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js'></script>
	<script src='https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js'></script>
	<script src='https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js'></script>
	<script src='https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js'></script>


	<script src="<?=base_url('assets/datepicker/');?>js/jquery.plugin.min.js"></script>
	<script src="<?=base_url('assets/datepicker/');?>js/jquery.datepick.js"></script>	

	<script src="<?=base_url(); ?>assets/js/lib/chosen/chosen.jquery.min.js"></script>	
	<script src="<?=base_url('assets/datepicker/');?>js/jquery.plugin.min.js"></script>
	<script src="<?=base_url('assets/datepicker/');?>js/jquery.datepick.js"></script>	
	
	<script type="text/JavaScript" src="<?=base_url('assets/'); ?>jQuery.print.js" /></script>
  
	<script src="<?=base_url(); ?>assets/modernizr.js"></script>	
	
    <script type="text/javascript">
        $(".tgl").datepick({dateFormat: 'dd-mm-yyyy'});
		var table = $('.example').DataTable({
			responsive: true,
			scrollx : true,
			dom: 'Bfrtip',
			buttons: [
				'copy', 'csv', 'excel', 'pdf', 'print'
			]			
		});	
		$(window).load(function(){
			// Animate loader off screen
			$(".se-pre-con").fadeOut("slow");;
		});	
        $(document).ready(function() {
			$('.angka').change(function(){
				var temp = Number($(this).val().replace(/,/g, "")).toString();
				$(this).val(temp.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
			});		
			$('.angka').keypress(function(evt){
				var charCode = (evt.which) ? evt.which : evt.keyCode;
				if (charCode != 46 && charCode > 31	&& (charCode < 48 || charCode > 57))
					return false;
				return true;
			});	
			$('.only_angka').keypress(function(evt){
				var charCode = (evt.which) ? evt.which : evt.keyCode;
				if (charCode != 46 && charCode > 31	&& (charCode < 48 || charCode > 57))
					return false;
				return true;
			});	
		});
    </script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>
	
</body>
</html>
<?php

?>