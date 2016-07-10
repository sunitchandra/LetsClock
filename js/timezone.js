// Fix for the following known Bootstrap bugs
// 		https://github.com/twbs/bootstrap/issues/10044
// 		https://github.com/twbs/bootstrap/issues/5566
// 		https://github.com/twbs/bootstrap/pull/7692
// 		https://github.com/twbs/bootstrap/issues/8423
// 		https://github.com/twbs/bootstrap/issues/7318
// 		https://github.com/twbs/bootstrap/issues/8423
if (navigator.userAgent.toLowerCase().indexOf('firefox') > -1) {
	document._oldGetElementById = document.getElementById;
	document.getElementById = function(id) {
		if(id === undefined || id === null || id === '') {
			return undefined;
		}
		return document._oldGetElementById(id);
	};
}

jQuery(function($) {

	/** ******************************
    * Current Time
    ****************************** **/
	setInterval(function() {
		var date = new Date(),
		time = date.toLocaleTimeString();
		$(".clock").html(time);
	}, 1000);

	/** ******************************
    * Alert Message Boxes
    ****************************** **/
    $('.alertMsg .alert-close').each(function() {
        $(this).click(function(event) {
            event.preventDefault();
            $(this).parent().fadeOut("slow", function() {
                $(this).css('diplay', 'none');
            });
        });
    });
	
	/** ******************************
	* Tooltips
	****************************** **/
	$("[data-toggle=tooltip]").tooltip();
	
	/** ******************************
	* Accordion Icon Toggle
	****************************** **/
	var iconOpen = 'icon-chevron-right',
		iconClose = 'icon-chevron-down';

	$(document).on('show.bs.collapse hide.bs.collapse', '.accordion', function (e) {
		var $target = $(e.target)
		$target.siblings('.accordion-heading').find('em').toggleClass(iconOpen + ' ' + iconClose);
		if(e.type == 'show') {
			$target.prev('.accordion-heading').find('.accordion-toggle').addClass('active');
		}
		if(e.type == 'hide') {
			$(this).find('.accordion-toggle').not($target).removeClass('active');
		}
	});
	
	/** ******************************
	* Tabs
	****************************** **/
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		e.target // activated tab
		e.relatedTarget // previous tab
	});
	
});