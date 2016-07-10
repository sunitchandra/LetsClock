$(document).ready(function () {

	/** ******************************
    * Date Picker
    ****************************** **/
    var eventDate = new Calendar({
		element: 'eventDate',
		months: 1,
		weekNumbers: true,
		dateFormat: 'Y-m-d'
	});
	
	/** ******************************
    * Time Picker
    ****************************** **/
	$("#eventTime").timepicker({
		format:"24",
		showAllDay: true,
		showSecs: false,
		min_increment: 15
	});

});