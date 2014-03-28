<?php $tkt_status=array();
//$tkt_status[0]='Unassigned';
$tkt_status[1]='Opened';
$tkt_status[2]='In Progress';
$tkt_status[3]='Closed';
$tkt_status[4]='unknown';


?>
<div class="container">
<h1 class="page_title">PNH Executive Ship Log</h1>
<div align="right" style="background: #e6e6e6;padding:4px;margin:2px;">
<span style="float: left;font-weight: bold;font-size: 13px;">
		Total Listed : <?php echo $ship_log_res->num_rows() ;?>
		
		Showing :<?php echo $ttl_rows .'/'. $ttl ;?>
</span>
<b>Date</b><input type="text" id="inp_date" size="10" name="inp_date" value="<?php echo $inp_date?>" >
<input type="button" value="submit" onclick="show_shiplog()">

<b>Employee</b>
<select name="fil_byemp" id="fil_byemp">
<option value="0">All</option>
<?php foreach($shipsms_emps as $s_emp){ if($s_emp['name']!=NULL){?>
<option value = "<?php echo $s_emp['emp_id']?>"
<?php echo $s_emp['emp_id']==$emp_id?"selected":""?>><?php echo $s_emp['name'] ;?></option>
<?php }}?>
</select>

<b>Ticket Status</b>
<select name="fil_bytktstatus" id="fil_bytktstatus">
<option value="">All</option>
<?php foreach($shiptkt_status as $s_tk){ if($s_tk['status']!=null){?>
<option value="<?php echo $s_tk['status']?>" <?php echo $s_tk['status']==$tiket_status?"selected":""?> ><?php echo $tkt_status[$s_tk['status']]?></option>
<?php }}?>
</select>

<b>Ticket Assigned to</b>
<select name="fil_byassignedto" id="fil_byassignedto">
<option value="">All</option>
<?php foreach($tkt_assignedto as $s_assignedto){ if($s_assignedto['name']!=NULL){?>
<option value = "<?php echo $s_assignedto['assigned_to']?>" <?php echo $s_assignedto['assigned_to']==$tkt_asigndto?"selected":""?> ><?php echo $s_assignedto['name']?></option>
<?php }}?>
</select>
</div>
<div style="clear: both;"></div>
<div>
<?php if($ship_log_res->num_rows()){?>
<table class="datagrid datagrid1 datagridsort" width="100%">
<thead><th>Sl no</th><th width="8%">Employee Name</th><th>Territory</th><th>Town</th><th>Message</th><th width="15%">Ticket status</th><th width="10%">Logged On</th></thead>
<tbody>
<?php $i=1; foreach($ship_log_res->result_array() as $s){?>
<tr><td><?=$i ?></td><td><a  target="_blank" href="<?php echo site_url("admin/view_employee/{$s['emp_id']}")?>" ><? echo $s['employee_name'] ?></a></td><td><?=$s['territory_name'] ?></td><td><?=$s['town_name']?$s['town_name']:'All Towns'; ?></td><td><?=$s['msg']?></td><td><?php if($s['is_ticket_created']==0){?><a href="<?php echo site_url("admin/createtiketby_ship/{$s['id']}")?>" class="button button-tiny button-flat-caution button-rounded create_ticket" >Create Ticket</a><?php }else{?><a href="<?php echo site_url("admin/ticket/{$s['ticket_id']}")?>" target="_blank" class="button button-tiny button-rounded " id="view_msgdet">View ticket</a><p>Ticket createdby : <b><?php echo $s['ticket_createdby']?></b></p><p>Created On : <b><?php echo $s['ticket_createdon']?></b></p><p>Ticket status : <b><?php echo $tkt_status[$s['ticket_status']]?></b></p><p>Assigned To : <b><?php echo $s['assigned_toname']?$s['assigned_toname']:'Na';?></b></p>
<p>
<?php $msg_log_res=$this->db->query("SELECT m.msg,m.created_on,a.name,from_customer FROM support_tickets_msg m JOIN king_admin a ON a.id=m.support_user WHERE m.ticket_id=? group by m.id order by created_on asc ",$s['ticket_id']);
$msg_det = '';
foreach($msg_log_res->result_array() as $msg_log)
{
if($msg_log['from_customer']== 1)
		$sent_by=$s['employee_name'];
	else
		$sent_by=$msg_log['name'];

$msg_det.= ('<b>'.$sent_by .'</b>:'.$msg_log['msg'] .' loggedon '.format_datetime($msg_log['created_on']))."<br/>";

}?>

 <!--  <a  href="javascript:void(0)" class="tip_popup" title="<?php echo $msg_det ?>">View Update</a>-->&nbsp;&nbsp;
 <span class="tip_popup" title="<?php echo $msg_det ?>" >View Update</span>
</p><?php }?>
</td><td><?=$s['loggedon'] ?></td>
</tr>
<?php $i++;}?>
</tbody>
</table>
<?php }else
{
	echo '<h3 align="left">No data found for - '.format_date($inp_date).'</h3>';
}?>
	<div class="pagination"  align="right">
		<?php echo $pagination ;?>
	</div>
</div>


</div>

<script>
/*$('.tip_popup').tooltip({
	position: {
	my: "center bottom-20",
	at: "center top",
	using: function( position, feedback ) {
		$( this ).css( position );
		$( "<div>" )
		.addClass( "arrow" )
		.addClass( feedback.vertical )
		.addClass( feedback.horizontal )
		.appendTo( this );
	}
	}
	});*/

	jQuery(document).ready(function() {
	 	Tipped.create('.tip_popup',{
	 	 skin: 'black',
	 	  hook: 'topleft',
	 	  hideOn: false,
	 	  closeButton: true,
	 	 	opacity: .5,
	 	 	hideAfter: 200,
		 });
	 });
	
/*	$('.tip_popup').tooltip({
		animation: 'fade',
		   arrow: true,
		   arrowColor: '#000',
		   content: '',
		   delay: 100,
		   fixedWidth: 0,
		   maxWidth: 0,
		   functionBefore: function(origin, continueTooltip) {
		      continueTooltip();
		      
		   },
		 
		   functionReady: function(origin, tooltip) {},
		   functionAfter: function(origin) {},
		   icon: '(?)',
		   iconDesktop: false,
		   iconTouch: false,
		   iconTheme: '.tooltipster-icon',
		   interactive: false,
		   interactiveTolerance: 350,
		   offsetX: 0,
		   offsetY: 0,
		   onlyOne: true,
		   position: 'absolute',
		   speed: 350,
		   timer: 0,
		   theme: '.tooltipster-default',
		   touchDevices: true,
		   trigger: 'hover',
		   updateAnimation: true

		});*/

//$('.tip_popup').tippy({ showheader: true ,showtitle : false,hoverpopup:true,closetext :'close',showclose :true });	




		
$('#inp_date').datepicker({
	changeMonth: true,
	dateFormat:'yy-mm-dd',
	numberOfMonths: 1
});
$('.leftcont').hide();


function show_shiplog()
{	
	location.href=site_url+'/admin/pnh_ship_log/'+$('#inp_date').val();
}



$(".create_ticket").click(function(e){
	if(!confirm("Are you sure want to create ticket?"))
	{
		e.preventDefault();
		return false;
	}else
	{
	return true;
	}
});


$(function(){
	var emp_id = $('#fil_byemp').val() ? $('#fil_byemp').val():'0';
	var ship_date=$('#inp_date').val() ? $('#inp_date').val():'0';
	var tkt_status=$('#fil_bytktstatus').val() ? $('#fil_bytktstatus').val():'';
	var tkt_assignedto=$('#fil_byassignedto').val() ? $('#fil_byassignedto').val():'0';	



$("#fil_byemp").change(function(){
	var ship_date=$('#inp_date').val() ? $('#inp_date').val():'0';
		location.href=site_url+'/admin/pnh_ship_log/'+ship_date+'/'+$(this).val();
		
});

$('#fil_bytktstatus').change(function(){
	var emp_id = $('#fil_byemp').val() ? $('#fil_byemp').val():'0';
	var ship_date=$('#inp_date').val() ? $('#inp_date').val():'0';
	location.href=site_url+'/admin/pnh_ship_log/'+ship_date+'/'+emp_id+'/'+$(this).val();
});

$('#fil_byassignedto').change(function(){
	var emp_id = $('#fil_byemp').val() ? $('#fil_byemp').val():'0';
	var ship_date=$('#inp_date').val() ? $('#inp_date').val():'0';
	var tkt_status=$('#fil_bytktstatus').val() ? $('#fil_bytktstatus').val():'0';
	location.href=site_url+'/admin/pnh_ship_log/'+ship_date+'/'+emp_id+'/'+tkt_status+'/'+$(this).val();
});



});


</script>

<style>
.datagrid1 {border-collapse: collapse;border:none !important}
.datagrid1 th{border:none !important;font-size: 15px;padding:5px;}
.datagrid1 td{border-right:none;border-left:none;border-bottom:none;font-size: 12px;padding:2px;color: #444;text-transform: capitalize}
.datagrid1 td a{text-transform: capitalize}
.datagrid1 td b{font-weight: bold;font-size: 11px;}

 .ui-tooltip, .arrow:after {
background: black;
border: 2px solid white;
}
.ui-tooltip {
padding: 10px 20px;
color: white;
border-radius: 20px;
font:12px "Helvetica Neue", Sans-Serif;

box-shadow: 0 0 7px black;
}
.arrow {
width: 70px;
height: 16px;
overflow: hidden;
position: absolute;
left: 50%;
margin-left: -35px;
bottom: -16px;
}
.arrow.top {
top: -16px;
bottom: auto;
}
.arrow.left {
left: 20%;
}
.arrow:after {
content: "";
position: absolute;
left: 20px;
top: -20px;
width: 125px;
height: 125px;
box-shadow: 6px 5px 9px -9px black;
-webkit-transform: rotate(45deg);
-moz-transform: rotate(45deg);
-ms-transform: rotate(45deg);
-o-transform: rotate(45deg);
tranform: rotate(45deg);
}
.arrow.top:after {
bottom: -20px;
top: auto;
}



</style>