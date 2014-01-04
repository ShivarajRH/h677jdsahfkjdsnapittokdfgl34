<script type="text/javascript" src="<?php echo base_url().'/js/jquery.jOrgChart.js'?>"></script>
<script type="text/javascript" src="<?php echo base_url().'/js/prettify.js'?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url().'/css/jquery.jOrgChart.css'?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url().'/css/custom.css'?>">

<?php 
	function build_subs($obj,$role,$emp_id,$job_title2,$html)
	{
		$sql = "SELECT a.employee_id,a.job_title AS role_id,c.job_title2 ,a.name,c.employee_id AS sub_emp_id,b.parent_emp_id AS sup_emp_id,c.job_title AS sub_role_id,c.name AS sub_emp_name
					FROM m_employee_info a
					LEFT JOIN `m_employee_rolelink` b ON a.employee_id = b.parent_emp_id  AND b.is_active = 1 
					LEFT JOIN m_employee_info c ON c.employee_id = b.employee_id
					WHERE  a.job_title = ? AND a.employee_id = ? and a.is_suspended = 0 ";   
		
		$res = $obj->db->query($sql,array($role,$emp_id,$job_title2));
		
		$html = '';
		
		$sub_roles_data = $res->result_array();
		
		if($res->num_rows())
		{
			if($role > 2)
			{
				$trr_name="SELECT b.territory_name
							FROM m_town_territory_link a
							JOIN pnh_m_territory_info b ON b.id=a.territory_id
							WHERE employee_id=? and a.is_active=1 
							GROUP BY territory_id";
             	$trr_name_res = $obj->db->query($trr_name,array($sub_roles_data[0]['employee_id']));
             	
             
				 $twn_name="SELECT b.town_name
							FROM m_town_territory_link a
							JOIN pnh_towns b ON b.id=a.town_id
							WHERE employee_id=? and a.is_active=1
							GROUP BY town_id";
             	$twn_name_res = $obj->db->query($twn_name,array($sub_roles_data[0]['employee_id']));
              
             
             
			}else{
				$trr_name_res=$obj->db->query("select territory_name from pnh_m_territory_info");
             	$twn_name_res=$obj->db->query("select town_name from pnh_towns");
			}
			
			$total_terr = $trr_name_res->num_rows();
			$total_twn = $twn_name_res->num_rows();
			
			$s=$t='';
			if(!$trr_name_res->num_rows())
				return ;
			$d = $trr_name_res->num_rows();
			foreach($trr_name_res->result_array() as $t1){
				$s.= $t1['territory_name'];
				
				$d--;
				if($d) $s.= ' , ';
				
			}
			
			$d = $twn_name_res->num_rows();
			/* 	$twn_nms = $twn_names; */
			foreach($twn_name_res->result_array() as $twn){
				$t.=$twn['town_name'];
				$d--;
				if($d) $t.= ' , ';
			}

			 $link= site_url("admin/view_employee/".$sub_roles_data[0]['employee_id']);
             $name = $sub_roles_data[0]['name'];
             $title  = "  $total_terr Territories :$s ";
             
             // if($role > 4 && $job_title2==0)
              	if($job_title2>=5 )
             {
             	$title  .= "</br> $total_twn Towns : $t ";
             	$total_twn = "($total_twn)";
             }else
             {
             	$total_twn = '';
             }
                       
			 
             $html = '<li class="tooltip role_swatch role_swatch_'.$role.'_'.$job_title2.'">';  
             if($role >= 4 && $job_title2<7)
             	$html .= "<a class=\"tootip\" title=\"$title\" href='$link'>$name [$s] $total_twn  </a>";
             else
			 	$html .= "<a class=\"tootip\" title=\"$title\" href='$link'>$name ($total_terr) $total_twn  </a>";
             
			 $html .= '<ul>';
				foreach($sub_roles_data as $subrole){
					$html .= build_subs($obj,$subrole['sub_role_id'],$subrole['sub_emp_id'],$subrole['job_title2'],$html)."\r\n";
				}
			$html .= '</ul>';
			
			$html .= '</li>'."\r\n";
		}	
			
		
		return $html; 	
	}	
?>

<div id="container">
	<div class="color_legend_1" >
				<span style="background: #90CA77;">&nbsp;</span> SuperAdmin  
				<span style="background: #81C6DD;">&nbsp;</span> Business Head  
				<span style="background: #E9B64D;">&nbsp;</span> Manager  
				<span style="background: #FF9900;">&nbsp;</span> Territory Manager 
				<span style="background: #E48743;">&nbsp;</span> Bussiness Executive 
				<span style="background: #b4defe">&nbsp;</span> Freight Co-ordinator
				<!--  <span style="background: #f1f1f1">&nbsp;</span> Driver-->
	</div>
	<h2 class="mainbox-title">Employees Role Tree</h2>
	<div id="main_column" class="clear">
		
		<table width="100%">
		<tr>
		<td>
		<div width="70%">
			<ul id="org" style="display:none">
	        	<?php echo build_subs($this,1,1,'','');?>
			</ul>
			<div id="chart" class="orgChart" style="overflow-x: scroll;width:998px;scrollbar-base-color:#ffeaff"></div> 
		</div>      
		</td>
		<td>
			<?php 
					$un_assighned_emp_list=$this->db->query("select a.employee_id,a.name,b.role_name from m_employee_info a join m_employee_roles b on a.job_title = b.role_id where job_title >1 and a.is_suspended=0 order by job_title,name");
					if($un_assighned_emp_list->num_rows())
					{
			?>
			<h3 style="font-size: 15px;text-align:left">Unassigned Employees</h3>
			<table class="datagrid smallheader noprint"  cellpadding="5" cellspacing="0" style="float:left;">
				<thead>
					<th>Role</th>
					<th>Employee Name</th>
				</thead>
				<tbody>
					<?php 
						foreach($un_assighned_emp_list->result_array() as $emp_det)
						{
							$emp_assign_det_res = $this->db->query("SELECT is_active  FROM m_town_territory_link WHERE employee_id = ? ORDER BY id DESC LIMIT 1 ",$emp_det['employee_id']);
							if($emp_assign_det_res->num_rows())
								if($emp_assign_det_res->row()->is_active)
									continue;
							
					?>
					<tr>
						<td width="150"><?php echo $emp_det['role_name']?></td>
						<td><?php echo anchor_popup('admin/edit_employee/'.$emp_det['employee_id'],ucwords($emp_det['name']))?></td>
					</tr>
					<?php }?>
				</tbody>
			</table>
			<?php }?>
				
		</td>
		</tr>							
	</table>
</div>
</div>

<style>
.color_legend_1{float: right;font-size: 11px;padding:2px;margin-right: 10px;}
.color_legend_1 span{height: 10px; width: 10px; display: inline-block;margin-left:10px}
.role_swatch{
	border:2px solid #f1f1f1 !important;
	padding:5px;
	color: #454545 !important;
	text-transform: capitalize;
	font-size: 11px !important;
	font-weight: bold;
	min-width: 100px;
	min-height: 50px;
	
}
.role_swatch a{
	display: inline-block;
	vertical-align: middle;
	margin:5px;
	font-size: 12px;
	color: #000;
}
.role_swatch_1_1{
	background: #90CA77 !important;
}
.role_swatch_2_2{
	background: #81C6DD !important;
}
.role_swatch_3_3{
	background: #E9B64D !important;
}
.role_swatch_4_4{
	background: #FF9900  !important;
}
.role_swatch_5_5{
	background:#E48743   !important;
}
.role_swatch_5_6{
	background:#b4defe   !important;
}

.role_swatch_5_7{
	background:#f1f1f1   !important;
}
.tools-container span{margin-left: 10px;}
.jOrgChart .node{background: #f1f1f1;}
</style>

<script>

$("#org").jOrgChart({
    chartElement : '#chart',
    dragAndDrop : false
});


$(document).ready(function() {
    $('#container a[href][title]').qtip({
        content: {
            text: true // Use each elements title attribute
         },
         style: 'cream' // Give it some style
      });
});

$('.leftcont').hide();
</script>
