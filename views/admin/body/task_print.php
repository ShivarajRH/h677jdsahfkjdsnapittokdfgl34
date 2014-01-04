<?php
$cfg_task_types_arr = array(); 
$task_type_list=$this->db->query("SELECT * FROM `pnh_m_task_types` ")->result_array();
foreach($task_type_list as $tsk_type_det)
{
	$cfg_task_types_arr[$tsk_type_det['id']] =$tsk_type_det['task_for']; 
}
 
?>
<html>
	<head>
		<title>Week Tasks For PNH Employees</title>
		<style>
			body{font-family: arial;margin:10px;}
			.print_tbl{font-size: 14px;}
			.print_tbl th{font-size: 14px;padding:5px}
			.print_tbl td{font-size: 12px;vertical-align: top;}
			.task_info{margin:0px;}
			.task_info .task_town_name{border-bottom: 1px solid #000;text-align: center;padding:3px;}
			.task_info .task_name{border-bottom: 1px solid #000;text-align: center;padding:3px;}
			.print_tbl table{font-size: 14px;border-collapse: collapse;border-collapse: collapse;margin-bottom: 5px;}
			.print_tbl table th{font-size: 12px;}
			.print_tbl table td{padding:5px;}
			.tsk_blck{margin:5px;}
		</style>
	</head>
	<body onload="window.print()">
	<div id="header">
			<input style="float: right;" type="button" value="Close" onClick="window.close()" class="hide" id="noprint">
			<input style="float: right;margin-right: 10px;" type="button" value="Print" onClick="window.print()" class="hide" id="noprint" >
	</div>	
			
		
		<div id="container">
		<div align="center">
		
		<?php echo date('D m/d',strtotime($start_date)) .' to '. date('D m/d',strtotime($end_date)) ;?>
		
		</div>
		<div align="left">

			<b>Employee : <?php $a = array_values($emp_list);echo $a[0];?></b>
			&nbsp;
			&nbsp;
			&nbsp;
			&nbsp;
			<b>Territory : <?php echo $territory_name;?></b>
		</div>
			<table class="print_tbl" cellpadding=0 cellspacing=0 border=1 width="100%">
		
				<thead>
					
					<?php foreach($week_dts as $dt=>$empdet){?>
					<th><?php echo date('D m/d',strtotime($dt))?></th>
					<?php }?>
					<th>Achievments</th>
					<th>Remarks</th>
				</thead>
				<tbody>
				<?php foreach($emp_list as $tsk_empid=>$employee_name){?>
					<tr>
					
				<?php foreach ($week_dts as $tsk_ondt=>$empdet){
				?>
					<td width="300">
					<?php 
						if(isset($week_dts[$tsk_ondt][$tsk_empid]))
						{
							foreach($week_dts[$tsk_ondt][$tsk_empid]  as $task_det)
							{
								echo '<div class="task_info">';
								echo '<div class="task_town_name">Task id:<b>'.$task_det['ref_no'].'</b></div>';
								echo '	<div class="task_town_name"><b>'.$task_det['town_name'].'</b></div>';
							
								$task_types_arr = explode(',',$task_det['task_type']);
								foreach($task_types_arr as $task_type)
									{
								if($task_type==1)
								{ 
									
									echo '	<div class="task_name"><b>Sales Target</b></div>';
									
									$sales_details=$this->db->query(" SELECT task_type,b.f_id,b.avg_amount,b.target_amount,c.franchise_name
																	FROM `pnh_m_task_info`a
																	LEFT JOIN `pnh_m_sales_target_info`b ON b.task_id=a.id
																	LEFT JOIN `pnh_m_franchise_info`c ON c.franchise_id=b.f_id 
																	where a.id = ? and a.is_active=1 
																	group by b.id",$task_det['id'])->result_array();
									
									echo ' <table cellpadding=3 cellspacing=0 border=1 width="100%">';
									echo ' <thead>';
									echo ' <th width="100">Franchise</th>';
									echo ' <th width="100">Target Amt</th>';
									echo ' </thead>';
									echo ' <tbody>';
									foreach($sales_details as $sales_det)
									{
										echo  '<tr>';
										echo'<td>'.$sales_det['franchise_name'].'</td><td>'.$sales_det['target_amount'].'</td>';
										echo '</tr>';
									}
									echo '</tbody>';
									echo '</table>';
									
								}
								else{
								
					$task_description=$this->db->query('SELECT task_id,request_msg,task_type_id,b.task_type AS task_type_name,b.task_for FROM `pnh_task_type_details`a
														JOIN `pnh_m_task_types`b ON b.id=a.task_type_id
														JOIN pnh_m_task_info c ON c.id=a.task_id
														WHERE a.task_id=? AND c.is_active=1 AND c.task_status=1 and task_type_id=?
														GROUP BY task_type_id',array($task_det['id'],$task_type))->result_array();
									foreach($task_description as $task_details)
									{
										echo'<div class="tsk_blck">';
									//	echo '<b>'.'['.$this->task_for[$task_details['task_for']].']'.'</b>'.'</br>';
										echo '<b>'.$task_details['task_type_name'].'</b>' .' - '.$task_details['request_msg'] .'</br>';
										echo'</div>';
									}
								}
					}
					echo '</div>';
							}	
							
						}
						else{
							echo '&nbsp;';
						}
					?>
					</td>
					
			<?php
			  }	
			?>
			
			<td width="300">&nbsp;</td>
			<td width="300">&nbsp;</td>
			<?php 
		 		}
			?>
		
				</tr>
			</tbody>
		</table>
	</div>
</body>
</html>
 <style>
 @media print 
{
  
#noprint
{
	display:none;
	visibility: hidden;
}	
}	
</style>