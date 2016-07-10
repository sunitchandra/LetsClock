$(document).ready(function() {
	var a = 0;

    $(".slidePanelRow").click(function () {
        $(this).siblings(".slidePanel").slideToggle("fast");
    });

	/** ******************************
    * Date Picker
    ****************************** **/
	var entryDate = new Calendar({
		element: 'entryDate',
		months: 1,
		weekNumbers: true,
		dateFormat: 'Y-m-d'
	});

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

	$('#timeIn').timepicker({
		format:'24',
		showAllDay: false,
		showSecs: false,
		min_increment: 15
	});

	$('#timeOut').timepicker({
		format:'24',
		showAllDay: false,
		showSecs: false,
		min_increment: 15
	});

});