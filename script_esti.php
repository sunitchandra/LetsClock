<!-- <script src="js/jquery.js" type="text/javascript"></script>
<script type='text/javascript' src='js/jquery-1.8.3.js'></script>
<script src="js/jquery.min.js" type="text/javascript"></script>
<script src="js/jquery.maskedinput.js" type="text/javascript"></script>
 -->
<script type="text/javascript">

/* jQuery(function($){
	$(document).find('input[type=text]').mask('99:99');
	//.mask("99:99");
}); */
function addMore_esti(){
	i = $('#tbl_esti tr').length;
	var $clone = $('#tbl_esti tr:nth-child(3)').clone();
	$clone.find('input,select').val('');
	$().parent().next().find('select').removeData();
	$clone.appendTo('#tbl_esti tbody');
}

function deleteRow_esti(){
	var boxLength = $('#tbl_esti tr input[type="checkbox"]').length;
	var checkedLength = $('#tbl_esti tr input[type="checkbox"]:checked').length;
	if(checkedLength >= boxLength){
		$('#tbl_esti tr input[type="checkbox"]:checked').not(':first').parent().parent().remove();
	} else {
		$('#tbl_esti tr input[type="checkbox"]:checked').parent().parent().remove()
	}
}
</script>

