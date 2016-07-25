<script src="js/jquery.js" type="text/javascript"></script>
<script type='text/javascript' src='js/jquery-1.8.3.js'></script>
<script src="js/jquery.min.js" type="text/javascript"></script>
<script src="js/bootstrap.min.js" type="text/javascript"></script>
<script type="text/javascript" src="js/includes/dashboard.js"></script>
<script src="js/custom.js" type="text/javascript"></script>


<script src="js/jquery.maskedinput.js" type="text/javascript"></script>
<!-- 
<script src="js/datetimepicker1.js" type="text/javascript"></script>
 -->
<!-- <link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css" /> -->
<!-- <script src="js/jquery.datetimepicker.js" type="text/javascript"></script>
<script src="js/jquery.datetimepicker.min.js" type="text/javascript"></script>
<script src="js/jquery.datetimepicker.full.min.js" type="text/javascript"></script> -->
		  <link rel="stylesheet" href="css/jquery-ui.css">
		  <script src="js/jquery-1.10.2.js"></script>
		  <script src="js/jquery-ui.js"></script>
	
<!-- <script src="js/jquery.validate.min.js" type="text/javascript"></script> -->
		 
<script type="text/javascript">	
$("#txt_start_date").datepicker({
    minDate: '-1970/12/99', //adjust min date display on calender
    maxDate: "1970/12/99", //adjust max possible date values
    onSelect: function(selected) {
      $("#txt_end_date").datepicker("option","minDate", selected)
    }
});

$("#txt_end_date").datepicker({
    minDate: 0,
    maxDate:"1970/12/99",
    onSelect: function(selected) {
       $("#txt_start_date").datepicker("option","maxDate", selected)
    }
});

var today = new Date();
var tomorrow = new Date(today.getTime() + 24 * 60 * 60 * 1000);
$("#txt_vp_date").datepicker({
	minDate: tomorrow, // yesterday is minimum date
	maxDate:'1970/12/31', // and tommorow is maximum date calendar
	dateFormat: "yy-mm-dd",
	onSelect: function(date){cdate(date)}
});

$("#txt_date").datepicker({
	minDate:'-1970/12/99', // yesterday is minimum date
	maxDate:'today', // and tommorow is maximum date calendar
	dateFormat: "yy-mm-dd",
	onSelect: function(date){cdate(date)}
});
	
	function cdate(date){
		$.ajax({
			data:'date='+date,
			url: "getData2.php",
			success: function(data){
				console.log(data);
				$('#show_data').empty();
				$('#show_data').append(data); 
				$('#old_style_entry').hide();
			}
		})
	}

</script>

<script type='text/javascript'>
 	function addMore(){
 		i = $('#claim tr').length;
		var $clone = $('#claim tr:first').clone();
		//$clone.find('input,select').val('');
		$clone.find('input[type="radio"],input[type="checkbox"]').attr('checked',false);
		$().parent().next().find('select').removeData();
		$clone.find('input,select').each(function() {
		    this.name = this.name.replace('row[0]', 'row['+i+']');
		});
		$clone.appendTo('#claim tbody');
	} 
	function deleteRow(){
		var boxLength = $('#claim tr input[type="checkbox"]').length;
		var checkedLength = $('#claim tr input[type="checkbox"]:checked').length;
		if(checkedLength >= boxLength){
			$('#claim tr input[type="checkbox"]:checked').not(':first').parent().parent().remove();
		} else {
			$('#claim tr input[type="checkbox"]:checked').parent().parent().remove()
		}
	}

 	function uncheck_radio_before_click(radio) {
	    if(radio.prop('checked'))
	        radio.one('click', function(){ radio.prop('checked', false); } );
	}
	$('body').on('mouseup', 'input[type="radio"]', function(){
	    var radio=$(this);
	    uncheck_radio_before_click(radio);
	}) 

	//Select only one radio button per column
	$(document).on('change',"input[type='radio']",function() {
		var id = $(this).attr('id');
		id = id.replace(/\[/g,"\\[");
		id = id.replace(/\]/g,"\\]");
		$(document).find('input#'+id).prop('checked',false);
		$(this).prop('checked',true);
	  });

	
	jQuery(function($){
		  /*  $("#txt_date").mask("9999/99/99",{placeholder:"yyyy/mm/dd"}); */
		}); 

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

<!-- Validation Starts -->
<script type="text/javascript">
/* $(document).ready(function(){
	$('#myform').removeAttr("novalidate");
});

 $(document).ready(function() {
    $('#myform').validate({
        rules: {
        	txt_date: {
                required: true
            },
            ddl_application: {
                required: true
            },
            ddl_release_dt: {
                required: true
            },
            ddl_pr_num: {
                required: true
            },
            ddl_pr_subtask: {
                required: true
            }
        },
        messages: {
        	txt_date: {
                required: 'Please select a date'
            },
            ddl_application: {
                required: 'Please select an Application'
            },
            ddl_release_dt: {
                required: 'Please select a Release Date'
            },
            ddl_pr_num: {
                required: 'Please select a PR Num'
            },
            ddl_pr_subtask: {
                required: 'Please select a PR Subtask'
            }
        },
        submitHandler: function(form) { // blocks submission for demo
            return false;
        }
    });
}); */ 
</script>
<!-- Validation Ends -->

<script type="text/javascript">
/* jQuery(function($){ */
/* 	$(":radio").change(function() {
  // find column
  var tdColumn = $(this).closest("td");
  // find row
  var trRow = tdColumn.closest("tr");

  // index of current column
  var i = tdColumn.index();
  
  // uncheck all radios in current column, except the selected radio
  trRow.siblings("tr").each(function(key, value) { $(value).find(":radio")[i].checked = false; } );
  }); */
/* 	function getValue(data)
	{
		return data;
	} */
</script>
