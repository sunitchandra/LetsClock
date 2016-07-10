//Get resource name and display data for Estimate and Future Planning
function getAppName(app) {
	
	var month = document.getElementById("ddl_month").value;
	var year = document.getElementById("ddl_year").value;
	var res_id = document.getElementById("ddl_resource_name").value;
	$.ajax({
		data:'res_id='+res_id+'&yr='+year+'&month='+month+'&app='+app.value,
		url: "getDataEsti.php",
		success: function(data){
			//console.log(data);
			$('#div_esti_data').empty();
			//$('#div_esti_data').show();
			$('#div_esti_data').append(data);
		}
	})
}

function getResourceName(res_id) {
	
	var month = document.getElementById("ddl_month").value;
	var year = document.getElementById("ddl_year").value;
	var app = document.getElementById("ddl_app").value;
	
	$.ajax({
		data:'res_id='+res_id.value+'&yr='+year+'&month='+month+'&app='+app,
		url: "getDataEsti.php",
		success: function(data){
			//console.log(data);
			$('#div_esti_data').empty();
			$('#div_esti_data').append(data);
		}
	})
}

function getMonthName(month) {
	
	var res_id = document.getElementById("ddl_resource_name").value;
	var year = document.getElementById("ddl_year").value;
	var app = document.getElementById("ddl_app").value;
	
	$.ajax({
		data:'res_id='+res_id+'&yr='+year+'&month='+month.value+'&app='+app,
		url: "getDataEsti.php",
		success: function(data){
			//console.log(data);
			$('#div_esti_data').empty();
			//$('#div_esti_data').show();
			$('#div_esti_data').append(data);
		}
	})
}

function getYearName(year) {
	var res_id = document.getElementById("ddl_resource_name").value;
	var month = document.getElementById("ddl_month").value;
	var app = document.getElementById("ddl_app").value;
	
	$.ajax({
		data:'res_id='+res_id+'&yr='+year.value+'&month='+month+'&app='+app,
		url: "getDataEsti.php",
		success: function(data){
			//console.log(data);
			$('#div_esti_data').empty();
			//$('#div_esti_data').show();
			$('#div_esti_data').append(data);
		}
	})
}

//Get PR Num 
function getPRNumber(res_dt,team) {

	console.log(res_dt.value);
	console.log('pr team: '+team)
	$.ajax({
		data:'res_dt='+res_dt.value+'&team='+team,
		url: "getDataEsti.php",
		success: function(data){
			//console.log(data);
			$(res_dt).parent().next().find('select').empty();
			$(res_dt).parent().next().find('select').append(data);
			/*$("#row[0][ddl_pr_num][]").empty();
			$("#row[0][ddl_pr_num][]").append(data);*/
		}
	})
}