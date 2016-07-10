$(document).ready(function() {
	var a = 0;

	/** ******************************
    * Time Picker
    ****************************** **/
	$('[name="etimeIn"]').each(function() {
		$('#etimeIn'+a).timepicker({
			format:'24',
			showAllDay: false,
			showSecs: false,
			min_increment: 1
		});
		$('#etimeOut'+a).timepicker({
			format:'24',
			showAllDay: false,
			showSecs: false,
			min_increment: 1
		});
		a++;
	});

});