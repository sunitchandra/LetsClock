(function($) {
	$.fn.timepicker = function(options) {
		var variable = {
			'inpHrs' 	: -1,
			'inpMins' 	: -1,
			'inpSecs'	: -1,
			'inpAmPm'	: -1,
			'divID' 	: 'div_t_p'
		};
		var config = $.extend({},$.fn.timepicker.defaults,options);
		time_function.create(this,config,variable);
	};

	$.fn.timepicker.defaults =  {
		left:           '', 	// Specify the position of left most pixel
		top:            '', 	// Specify the position of top most pixel
		format:         'AMPM', // Specify '24' for 24 hr format
		showAllDay:     true, 	// Specify whether All Day btn is to be shown
		showHrs:        true, 	// Specify whether Hr is to be shown
		showMins:       true, 	// Specify whether Min is to be shown
		showSecs:       true, 	// Specify whether Sec is to be shown
		min_increment:  1,	 	// Increment of the minutes to show
		sec_increment:  1, 		// Increment of the seconds to show
		hr_increment:   1       // Increment of the hours to show
	};

	time_function = {
		timePickId : '123',
		create : function(timepick,config,variable) {
			this.timePickId = variable.divID+'_'+$(timepick).attr('id');
			this.time_parser(timepick,variable,config);
			var timePickDiv = '<br><div id="'+ this.timePickId+'" style="display:none; position:absolute; z-index:100"></div>';
			$(timepick).parent().append(timePickDiv);
			$("#"+this.timePickId).css('margin-left','0px');
			$("#"+this.timePickId).css('left',this.left_pos(timepick,config)+'+15');
			$("#"+this.timePickId).css('top','55px');
			$("#"+this.timePickId).css('width',this.width(config));
			$("#"+this.timePickId).addClass("container-bkg");
			$("#"+this.timePickId).append(this.header(timepick,config,variable));
			$("#"+this.timePickId+"_hdr").addClass("header-bkg");
			$("#"+this.timePickId+"_hdr").children('div').addClass("header-div");
			$("#"+this.timePickId+"_hdr").find('span').addClass("header-text");
			$("#"+this.timePickId+"_hdr").css('clear','both');
			this.display_data.display_hrs(timepick,config,variable);
			this.display_data.display_mins(timepick,config,variable);
			this.display_data.display_secs(timepick,config,variable);
			this.display_data.display_ampm(timepick,variable);
			this.display_data.display_ok(timepick,config,variable);

			$(timepick).bind('click',function() {
				$("#"+variable.divID+'_'+$(timepick).attr('id')).show('fast');
			});
		},
		left_pos: function(timepick,config) {
			var lftPos = config.left;
			if (lftPos=='') {
				lftPos = $(timepick).position().left;
			}
			return lftPos;
		},
		top_pos: function(timepick,config) {
			var topPos =  config.top;
			if (topPos=='') {
				topPos = $(timepick).position().top + $(timepick).outerHeight();
			}
			return topPos;
		},
		header: function(timepick,config,variable) {
			var headerDivs = '<div id=\"'+this.timePickId+'_hdr\">';
			if ( config.showHrs == true) {
				headerDivs = headerDivs + '<div id=\"'+this.timePickId+'_hdr_hrs\">' +
				'<span >Hours</span><br></div>';
			}
			if (config.showMins == true) {
				headerDivs = headerDivs + '<div id=\"'+this.timePickId+'_hdr_mins\">' +
				'<span >Minutes</span><br></div>';
			}
			if (config.showSecs == true) {
				headerDivs = headerDivs + '<div id=\"'+this.timePickId+'_hdr_secs\">' +
				'<span >Seconds</span><br></div>';
			}
			if (config.format == 'AMPM') {
				headerDivs = headerDivs + '<div id=\"'+this.timePickId+'_hdr_ampm\">' +
				'<span >AM/PM</span><br></div>';
			}
			if (config.showAllDay == true) {
				headerDivs = headerDivs + '<div id=\"'+this.timePickId+'_allDay\">' +
				'<span >&nbsp;</span><br></div>';
			}
			headerDivs = headerDivs + '<div id=\"'+this.timePickId+'_ok\">' +
			'<span >&nbsp;</span><br></div>';
			headerDivs = headerDivs + '</div>';

			return headerDivs;
		},
		width: function(config) {
			var width = 'auto';
			return width;
		},
		time_format: function(config) {
			return  config.format;
		},
		display_data: {
			display_hrs: function(timepick,config,variable) {
				var hrsTable = '';
				if ( time_function.time_format(config) == 'AMPM') {
					hrsTable = this.get_table(1,12,config.hr_increment,variable.inpHrs);

				}
				else if ( time_function.time_format(config) == '24') {
					hrsTable = this.get_table(0,23,config.hr_increment,variable.inpHrs);
				}
				$("#"+time_function.timePickId+"_hdr_hrs").append(hrsTable);
			},
			display_mins: function(timepick,config,variable) {
				var minsTable = this.get_table(0,59,config.min_increment,variable.inpMins);
				$("#"+time_function.timePickId+"_hdr_mins").append(minsTable);
			},
			display_secs: function(timepick,config,variable) {
				var secsTable = this.get_table(0,59,config.sec_increment,variable.inpSecs);
				$("#"+time_function.timePickId+"_hdr_secs").append(secsTable);
			},
			display_ampm: function(timepick,variable) {
				var ampmList = new Array("AM","PM");

				var table = '<select class="form-control" >';
				var row = '';
				for(var i = 0; i<ampmList.length;i++) {
					row = row + '<option value = "' + ampmList[i] + '"';
					if (ampmList[i] == variable.inpAmPm) {
							row = row + ' selected '
					}
					row = row + '>' + ampmList[i] + '</option>';
				}

				table = table + row;
				table = table + '</select>';

				$("#"+time_function.timePickId+"_hdr_ampm").append(table);
			},
			display_ok: function(timepick,config,variable) {
				var allDayButton = '<button type="button" class="btn btn-default btn-sm" id="'+variable.divID+'_'+$(timepick).attr('id')+'_allDay_but">All Day</button>';
				var okButton = '<button type="button" class="btn btn-default btn-sm" id="'+variable.divID+'_'+$(timepick).attr('id')+'_ok_but"><i class="icon-ok"></i></button>';
				$("#"+variable.divID+'_'+$(timepick).attr('id')+"_allDay").append(allDayButton);
				$("#"+variable.divID+'_'+$(timepick).attr('id')+"_ok").append(okButton);
				$("#"+variable.divID+'_'+$(timepick).attr('id')+"_allDay_but").bind('click', function() {
					time_function.update_time(timepick,variable,config);
				});
				$("#"+variable.divID+'_'+$(timepick).attr('id')+"_ok_but").bind('click', function() {
					time_function.update_time(timepick,variable,config);
				});
			},
			get_table: function(start,stop,increment,selOpt) {
				var table = '<select class="form-control" >';
				var rows = this.get_rows(start,stop,increment,selOpt);
				table = table + rows;
				table = table + '</select>';
				return table;
			},
			get_rows: function(start,stop,increment,selOpt) {
				var row = '';
				for(var i=start; i<=stop; i=i+increment ) {
					var rowVal = i;
					if (rowVal<10) {
							rowVal = '0'+rowVal;
					}
					row = row + '<option value="'+rowVal+'"';

					if (rowVal == selOpt ) {
							row = row + ' selected ';
					}
					row = row + '>' + rowVal+'</option>';
				}
				return row;
			}
		},
		update_time: function(timepick,variable,config) {
			var selTime = '';
			if ( config.showAllDay == true ) {
				selTime = '';
			}
			
			if ( config.showHrs == true ) {
				selTime = selTime + $("#"+variable.divID+'_'+$(timepick).attr('id')+"_hdr_hrs option:selected").val();
			}

			if ( config.showMins  == true) {
				if (config.showHrs == true ) {
						selTime = selTime  + ':';
				}
				selTime = selTime+ $("#"+variable.divID+'_'+$(timepick).attr('id')+"_hdr_mins option:selected").val();
			}

			if ( config.showSecs  == true) {
				if (config.showHrs == true || config.showMins  == true) {
						selTime = selTime  + ':';
				}
				selTime = selTime+ $("#"+variable.divID+'_'+$(timepick).attr('id')+"_hdr_secs option:selected").val();
			}

			if ( this.time_format(config) == 'AMPM') {
				selTime = selTime  + ' ' + $("#"+variable.divID+'_'+$(timepick).attr('id')+"_hdr_ampm option:selected").val();
			}
			$(timepick).val(selTime);
			$("#"+variable.divID+'_'+$(timepick).attr('id')).hide('fast');
			return false;
		},
		time_parser: function(timepick,variable,config) {
			if ($(timepick).val()!=undefined && $(timepick) != null && $(timepick).val()!='' ) {
				var inputVal 	= $(timepick).val();
				var charList 	= '';
				var isAllDaySet = false;
				var isHrSet 	= false;
				var isMinSet 	= false;
				var isSecset 	= false;
				var isAmPmSet	= false;

				if ( config.showAllDay == false ) {
						isAllDaySet = true;
				}
				if ( config.showHrs == false ) {
						isHrSet = true;
				}
				if ( config.showMins == false ) {
						isMinSet = true;
				}
				if ( config.showSecs == false ) {
						isSecset = true;
				}
				if ( config.format == '24' ) {
						isAmPmSet = true;
				}

				var isNumParseComplete = false;
				for(var i=0; i<(inputVal.length); i++) {
					var singleChar = inputVal.substring(i,i+1);

					if (singleChar == 'A' || singleChar == 'P') {
						if (!isAmPmSet) {
							variable.inpAmPm = singleChar + 'M';
							isAmPmSet = true;
							return false;
						}
					}

					if (isNumeric(singleChar) == true && singleChar!= ' ') {
						charList = charList + singleChar;
						if (i!=(inputVal.length-1)) {
								continue;
						}
						isNumParseComplete = true;
					} else {
						isNumParseComplete = true;
					}

					if (isNumParseComplete == true) {
						isNumParseComplete = false;
						if (charList<10 && charList.length == 1) {
							charList = '0' + charList;
						}

						if (!isAllDaySet) {
							variable.inpHrs = charList;
							isAllDaySet = true;
							charList = '';
							continue;
						}
						if (!isHrSet) {
							variable.inpHrs = charList;
							isHrSet = true;
							charList = '';
							continue;
						}
						if (!isMinSet) {
							variable.inpMins = charList;
							isMinSet = true;
							charList = '';
							continue;
						}
						if (!isSecset) {
							variable.inpSecs = charList;
							isSecset = true;
							charList = '';
							continue;
						}
					}
				}
			}
			return false;
		}
	}

	// To check whether an input is a number. Return false if not a number.
	function isNumeric(num) {
		return !isNaN(num);
	};
})(jQuery);