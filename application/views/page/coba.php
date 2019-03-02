<script src='https://code.jquery.com/jquery-3.3.1.js'></script>
<div id='temp_isi'>
</div>
<script type="text/javascript">
$(document).ready(function(){
	refreshTable();
});

function refreshTable(){
	$('#temp_isi').load("<?=base_url('Admin/ajax_coba'); ?>", function(result){
		console.log(result);
		setTimeout(refreshTable, 4000);
    });
}
</script>