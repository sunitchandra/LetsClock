$(document).ready(function () {

	/** ******************************
    * Time Clock
    ****************************** **/
	var isRunning = $('#running').val();
	var empFullName = $('#empFullName').val();
	if (isRunning == '0') {
		if ($('#timetrack').hasClass('btn-warning')) {
			$('#timetrack').removeClass('btn-warning');
			$('#timetrack').addClass('btn-success');
		}
		if ($('#timetrack i').hasClass('icon-signout')) {
			$('#timetrack i').removeClass('icon-signout');
			$('#timetrack i').addClass('icon-signin');
		}
		$('#timetrack').addClass('btn-success');
		$('#timetrack i').addClass('icon-signin');
		$('.workStatus').html('Clocked Out');
		$('#timetrack span').html('Clock '+empFullName+' In');
	} else {
		if ($('#timetrack').hasClass('btn-success')) {
			$('#timetrack').removeClass('btn-success');
			$('#timetrack').addClass('btn-warning');
		}
		if ($('#timetrack i').hasClass('icon-signin')) {
			$('#timetrack i').removeClass('icon-signin');
			$('#timetrack i').addClass('icon-signout');
		}
		$('#timetrack').addClass('btn-warning');
		$('#timetrack i').addClass('icon-signout');
		$('.workStatus').html('Clocked In');
		$('#timetrack span').html('Clock '+empFullName+' Out');
	}

	/** ******************************
    * Date Picker
    ****************************** **/
    var dob = new Calendar({
		element: 'dob',
		months: 1,
		weekNumbers: true,
		dateFormat: 'Y-m-d'
	});

	var hireDate = new Calendar({
		element: 'hireDate',
		months: 1,
		weekNumbers: true,
		dateFormat: 'Y-m-d'
	});

});