/**
 * 
 */
// JavaScript Document

//To get the week days for the selected month
function getShowDay1(claim_dt, element_id) {
	//alert(claim_dt);
	if (window.XMLHttpRequest) {
		try {
			xmlhttp = new XMLHttpRequest();
		} catch (e) {
			xmlhttp = false;
		}
		// branch for IE/Windows ActiveX version
	} else if (window.ActiveXObject) {
		try {
			xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {
				xmlhttp = false;
			}
		}
	}

	var element = document.getElementById(element_id);
	
	element.innerHTML = 'Loading...';
	//wait(1);
	//xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	//alert(xmlhttp.open("GET",fragment_url));
	xmlhttp.open("GET", "ajax_show_day1.php?date_sel=" + claim_dt);
	xmlhttp.onreadystatechange = function() {
		//alert(xmlhttp.status); 
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			element.innerHTML = xmlhttp.responseText;
		}
	};
	xmlhttp.send(null);
}

//To get the releases for selected application
function getReleaseDate1(application) {
	$.ajax({
		data:'id='+application.value,
		url: "getData.php",
		success: function(data){
			$(application).parent().next().find('select').empty();
			$(application).parent().next().find('select').append(data);
		}
	})
}

//To get the PR num for the selected application and release date.
function getProjectNo(release_dt) {
	var application = $(release_dt).parent().prev().find('select').val();
	$.ajax({
		data:'release='+release_dt.value+'&application='+application,
		url: "getData.php",
		success: function(data){
		console.log(data);
		$(release_dt).parent().next().find('select').empty();
		//$(release_dt).parent().previous().find('select').removeData();
		$(release_dt).parent().next().find('select').append(data);
		}
	})
}

//To get the PR subtask for the selected application and release date.
function getProjectSubtask(pr_num) {
	var application = $(pr_num).parent().prev().prev().find('select').val();
	var pr_number = $(pr_num).val();
	$.ajax({
		data:'application_id='+application+'&pr_num='+pr_number,
		url: "getData.php",
		success: function(data){
		console.log(data);
		$(pr_num).parent().next().find('select').empty();
		$(pr_num).parent().next().find('select').append(data);
		}
	})
}

//To get PTS release dates from pts year
function getPTSReleaseDate(year, res_slno) {
	$.ajax({
		data:'id='+year.value+'&rslno='+res_slno,
		url: "getData3.php",
		success: function(data){
			//console.log(data);
			$('#ddl_release_date_start').empty();
			$('#ddl_release_date_start').append(data);
			$('#ddl_release_date_end').empty();
			$('#ddl_release_date_end').append(data);
		}
	})
}


//To get data based on release dates selected from pts year
function getPTSData(res_slno) {
	
	var start_rel_dt = document.getElementById("ddl_release_date_start").value;
	var end_rel_dt = document.getElementById("ddl_release_date_end").value;
	$.ajax({
		data:'start_rel_dt='+start_rel_dt+'&end_rel_dt='+end_rel_dt+'&res_slno='+res_slno,
		url: "getData3.php",
		success: function(data){
			console.log(data);
			$('#div_pts_data').empty();
			$('#div_pts_data').append(data);
		}
	})
}


//To get data based on release dates selected from pts year
function getClaimData(res_slno) {
	
	var year = document.getElementById("ddl_claim_year").value;
	var month = document.getElementById("ddl_claim_month").value;
	$.ajax({
		data:'cyear='+year+'&cmonth='+month+'&res_slno='+res_slno,
		url: "getData4.php",
		success: function(data){
			console.log(data);
			$('#div_pts_data1').empty();
			$('#div_pts_data1').append(data);
		}
	})
}


//To get data based on release dates selected from pts year
function getClaimDataAll(res_npt, res_slno) {
	
	var year = document.getElementById("ddl_claim_year").value;
	var month = document.getElementById("ddl_claim_month").value;
	$.ajax({
		data:'cyear='+year+'&cmonth='+month+'&res_npt='+res_npt+'&res_slno='+res_slno,
		url: "getData5.php",
		success: function(data){
			console.log(data);
			$('#div_pts_data2').empty();
			$('#div_pts_data2').append(data);
		}
	})
}


//To get data based on release dates selected from pts year
function getReleaseDatePTS(application_id) {
	$.ajax({
		data:'application_id='+application_id.value,
		url: "getData6.php",
		success: function(data){
			//console.log(data);
			$('#ddl_release_year').empty();
			$('#ddl_release_year').append(data);
		}
	})
}

//To get data based on release dates selected from pts year
function getPTSReleaseDates(release_year) {
	var app_id = document.getElementById("ddl_application").value;
	$.ajax({
		data:'ryear='+release_year.value+'&application_id1='+app_id,
		url: "getData6.php",
		success: function(data){
			console.log(data);
			$('#ddl_release_date_start').empty();
			$('#ddl_release_date_start').append(data);
			$('#ddl_release_date_end').empty();
			$('#ddl_release_date_end').append(data);
		}
	})
}


//To get data based on release dates selected from pts year
function getAllPTSData(notA) {
	var app_id = document.getElementById("ddl_application").value;
	var release_year = document.getElementById("ddl_release_year").value;
	var start_rdt = document.getElementById("ddl_release_date_start").value;
	var end_rdt = document.getElementById("ddl_release_date_end").value;
	$.ajax({
		data:'ryear='+release_year+'&app_id1='+app_id+'&srdt='+start_rdt+'&erdt='+end_rdt,
		url: "getData6.php",
		success: function(data){
			//console.log(data);
			$('#div_pts_data').empty();
			$('#div_pts_data').append(data);
		}
	})
}
//To get the previous claim data of selected date
/*function getClaimData(claim_dt) {
	var application = $(pr_num).parent().prev().prev().find('select').val();
	var pr_number = $(pr_num).val();
	alert(claim_dt);
	$.ajax({
		data:'claim_dt='+claim_dt,
		url: "getData.php",
		success: function(data){
		console.log(data);
		$(pr_num).parent().next().find('select').empty();
		$(pr_num).parent().next().find('select').append(data);
		}
	})
}*/