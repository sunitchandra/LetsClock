$(document).ready(function() {
	var a = 0;

	$('[name="noticeStart"]').each(function() {
	  var noticeStart = new Calendar({
			element: 'noticeStart['+a+']',
			months: 1,
			weekNumbers: true,
			dateFormat: 'Y-m-d'
		});

		var noticeExpires = new Calendar({
			element: 'noticeExpires['+a+']',
			months: 1,
			weekNumbers: true,
			dateFormat: 'Y-m-d'
		});

		a++;
	});
	
	var startDate = new Calendar({
		element: 'startDate',
		months: 1,
		weekNumbers: true,
		dateFormat: 'Y-m-d'
	});
	
	var endDate = new Calendar({
		element: 'endDate',
		months: 1,
		weekNumbers: true,
		dateFormat: 'Y-m-d'
	});

});