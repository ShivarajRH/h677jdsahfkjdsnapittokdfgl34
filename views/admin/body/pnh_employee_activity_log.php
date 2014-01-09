<div class="page_wrap container">
	
	
	<div class="page_topbar" >
		<div class="page_topbar_left fl_left" >
			<h2 class="page_title">Employee sms activity log for - <span id="title_det"></span></h2>
		</div>
		<div class="page_action_buttonss fl_right" align="right">
			&nbsp;Month : <input type="text" class="monthpicker" size="8" id="month_year_fill">
		</div>
		<div class="fl_right">
			Employee status:<select name="emp_status" id="emp_status">
				<option value=''>Any</option>
				<option value='1' selected>Active</option>
				<option value='2'>Suspended</option>
			</select>
		</div>
	</div>
	
	<div style="clear:both">&nbsp;</div>
	
	<div class="page_content">
		<div id="log_details"></div>
	</div>
</div>

<script>

$(".monthpicker").monthpicker({
    selectedYear: (new Date()).getFullYear(),
    startYear: 2012,
    finalYear: (new Date()).getFullYear()
});

function reset_months(year)
{
	$(".monthpicker").monthpicker('disableMonths', []); // (re)enables all
    if (year == (new Date()).getFullYear()) {
    	var disable_mnths = [];
    	for(var i=(new Date()).getMonth();i<11;i++)
    	    disable_mnths.push(i+2);
    	$(".monthpicker").monthpicker('disableMonths', disable_mnths);
    }
}

$(".monthpicker").bind('monthpicker-change-year', function (e, year) {
	reset_months(year);
});

reset_months((new Date()).getFullYear());

function load_emp_sms_activity_log()
{
	$("#log_details").html('<div align="center" style="padding:10px;"><img src="'+base_url+'/images/loading_bar.gif'+'"></div>');
	var month_det=$("#month_year_fill").val();
	var emp_status=$("#emp_status").val();
	$.post(site_url+"/admin/jx_get_emp_sms_activity",{month:month_det,emp_status:emp_status},function(resp){
		$("#log_details").html(resp.page);
		$("#title_det").html(resp.title_det);
		$(".monthpicker").val(resp.title_det);
		
		},'json');
}

load_emp_sms_activity_log();

$("#month_year_fill").change(function(){
	load_emp_sms_activity_log();
	
});

$("#emp_status").change(function(){
	load_emp_sms_activity_log();
});

$(document).ready(function () {

    function exportTableToCSV($table, filename) {

    	var $rows = $table.find('thead,tr:has(td)'),

            // Temporary delimiter characters unlikely to be typed by keyboard
            // This is to avoid accidentally splitting the actual contents
            tmpColDelim = String.fromCharCode(11), // vertical tab character
            tmpRowDelim = String.fromCharCode(0), // null character

            // actual delimiter characters for CSV format
            colDelim = '","',
            rowDelim = '"\r\n"', 
            
            // Grab text from table into CSV formatted string
            csv = '"' + $rows.map(function (i, row) {
                var $row = $(row),
                    $cols = $row.find('th,td');
                	

                return $cols.map(function (j, col) {
                    var $col = $(col),
                        text = $.trim($col.text());
                    return text.replace('"', '""'); // escape double quotes

                }).get().join(tmpColDelim);

            }).get().join(tmpRowDelim)
                .split(tmpRowDelim).join(rowDelim)
                .split(tmpColDelim).join(colDelim) + '"',

            // Data URI
            csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

        $(this)
            .attr({
            'download': filename,
                'href': csvData,
                'target': '_blank'
        });
    }
   
    $("#export_btn").live('click', function (event) {
        exportTableToCSV.apply(this, [$('#log_details table'), "employee_sms_activity_log"+(new Date())+'.csv']);
    });
});
</script>

<style>
.leftcont{display: none;}
.warning_txt{color:#cd0000}
</style>