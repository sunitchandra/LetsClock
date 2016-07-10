<script type="text/javascript">

$("#txt_date").datepicker({
	minDate:'-1970/12/99', // yesterday is minimum date
	maxDate:'today', // and tommorow is maximum date calendar
	dateFormat: "yy-mm-dd",
	onSelect: function(date){cdate(date)}
});
	
	function cdate(date){
		$.ajax({
			data:'date='+date,
			url: "getData2_2.php",
			success: function(data){
				//console.log(data);
				$('#show_data').empty();
				$('#show_data').append(data); 
				$('#old_style_entry').hide();
				$("#old_style_entry").children().attr("disabled","disabled");
			}
		})
	}

function addMore_2(){
		i = $('#claimedData tr').length;
	var $clone = $('#claimedData tr:first').clone();
	$clone.find('select').val('');

	//$clone.find('input[type="select"]').attr('selected', false);
	
	$clone.find('input[type="radio"],input[type="checkbox"]').attr('checked',false);
	$().parent().next().find('select').removeData();
	$clone.find('input,select').each(function() {
	    this.name = this.name.replace('row[0]', 'row['+i+']');
	   // this.value = '';
	});
	$clone.appendTo('#claimedData tbody');
} 

function deleteRow_2(){
	var boxLength = $('#claimedData tr input[type="checkbox"]').length;
	var checkedLength = $('#claimedData tr input[type="checkbox"]:checked').length;
	var checkedBox = $('#claimedData tr input[type="checkbox"]:checked');
	
	var selectedDate = $('#txt_date').val();
	var hidDate = $('#claimedData tr input[type="hidden"]').val();
	console.log('selected Date: '+selectedDate+'hidden date: '+hidDate);

	var selectedCheckbox = [];
	var i = 0;
	$.each(checkedBox,function(k,v){
			selectedCheckbox[i] = $(v).val();
			i++;
		})
	console.log(selectedCheckbox);
	
	if(checkedLength >= boxLength){
		$('#claimedData tr input[type="checkbox"]:checked').not(':first').parent().parent().remove();
	} 
	else {
		if(selectedDate != hidDate)
		{
			console.log('just remove row not data');
			$('#claimedData tr input[type="checkbox"]:checked').parent().parent().remove();
		}
		else if(selectedDate == hidDate)
		{
			console.log('delete row from db');
			var ans = confirm("sure? ");
			console.log(ans);
			if(ans)
			{
				//ajax
				console.log('inside ajax');
				$.ajax({
					data:'cd_slno='+selectedCheckbox+'&date='+selectedDate,
					url: "getData2_2.php",
					success: function(data){
						//console.log(data);
						cdate(selectedDate);
					}
				})
			}
			else
			{
				return false;
			}
		}
	}
}

//Watermark function for comments textbox
$(document).ready(function() {
 var watermark = '--Comments--';
 $('input[type="search"]').blur(function(){
  if ($(this).val().length == 0)
    $(this).val(watermark).addClass('watermark');
 }).focus(function(){
  if ($(this).val() == watermark)
    $(this).val('').removeClass('watermark');
 }).val(watermark).addClass('watermark');
});
</script>

<!-- 
var checkedBox = $('#claimedData tr input[type="checkbox"]:checked');
	var selectedDate = $('#txt_date').val();
	console.log(selectedDate);
	var selectedCheckbox = [];
	var i = 0;
	$.each(checkedBox,function(k,v){
			selectedCheckbox[i] = $(v).val();
			i++;
		})
	console.log(selectedCheckbox);
	if(checkedLength >= boxLength){
		$('#claimedData tr input[type="checkbox"]:checked').not(':first').parent().parent().remove();
	} 
	else {
		var ans = confirm("sure? ");
		console.log(ans);
		if(ans)
		{
			//ajax
			$.ajax({
				data:'cd_slno='+selectedCheckbox,
				url: "getData_2_2_2.php",
				success: function(data){
					$('#claimedData tr input[type="checkbox"]:checked').parent().parent().remove();
					//console.log(data);
					$('#div_esti_data').empty();
					//$('#div_esti_data').show();
					$('#div_esti_data').append(data);
				}
			})
		}
		else
		{
			return false;
		}
	}


 -->