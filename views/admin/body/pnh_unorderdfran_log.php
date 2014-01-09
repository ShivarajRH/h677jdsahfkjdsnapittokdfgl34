<h2 class="page_title">Unordered Franchise Log</h2>
<table class="datagrid datagridsort" width="100%">
<thead>
<th>Franchise Id</th><th>Name</th><th>Contact Details</th><th>Last Ordered</th><th>Enquiry</th>
</thead>
<tbody>
<?php 
//$i=1;

if($orderd_frans_list->num_rows())
{
	$i = 0;
	foreach($orderd_frans_list->result_array() as $ord_frans)
	{
		$last_orderdate = '-na-';
		$stat = 0;
		$orderd_dates=explode(',',$ord_frans['ordered_on']);
		$last_orderdate = $orderd_dates[0];
		if($last_orderdate)
		{
			// if last order date is less that 3 days
			if(strtotime($last_orderdate) < time()-60*24*60*60)
				$stat = 1;
		}
		
		if(in_array($ord_frans['franchise_id'], $frids))
			$stat = 1;
		

		if($stat){

?>

		 		
<tr><td><input type="hidden" name="franchise_id" value="<?php echo $ord_frans['franchise_id']; ?>"><?php echo $ord_frans['pnh_franchise_id'];?></td><td><?php echo $ord_frans['franchise_name'];?></td>
<td><?php echo $ord_frans['login_mobile1'].','.$ord_frans['login_mobile2']?></td>
<td><input type="hidden" name="last_orderd" value="<?php echo $last_orderdate ;?>"><?php echo $last_orderdate;?></td>

<td width="400">
<div style="margin-bottom: 10px;">
	<a href="javascript:void(0)" style="font-size:85%;" onclick='$("form",$(this).parent()).toggle()'>add msg</a>
	<form method="post" style="display:none;"  action="<?php echo site_url("admin/pnh_update_unorderd_log/{$ord_frans['franchise_id']}")?>">
		<input type="hidden" name="last_orderon" value="<?php echo $last_orderdate ?>" >
		<textarea style="width: 98% " name="msg"></textarea>
		<br />
		<input type="checkbox" name="admin_notify" value="1">admin notify <input style="float: right" type="submit" value="add">
	</form>
		<?php 
		if(1)
		{
			$fr_unorder_log=$this->db->query("SELECT a.*,b.name,DATE_FORMAT(a.created_on,'%d/%m/%Y %h:%i %p') as created_by,DATE_FORMAT(a.last_orderd,'%d/%m/%Y %h:%i %p') as last_orderd 
												FROM pnh_franchise_unorderd_log a
		 										JOIN king_admin b ON b.id=a.created_by
				 								WHERE franchise_id=? order by id desc ",$ord_frans['franchise_id']);
			if($fr_unorder_log->num_rows())
			{
		?>
				<ul class="item_list">
		<?php 		
				foreach ($fr_unorder_log->result_array() as $log)
				{
		?>
					<li>
						<p><?php echo $log['msg']?></p>
						<div align="right" style="font-size: 11px;padding:5px;">
							<span style="float: left">Is Admin Notified : <?php echo ($log['is_notify']==1)?'Yes':'No' ?></span>
							<b><?php echo "By  ".ucwords($log['name']);?></b> - <?php echo $log['created_by']?> 
						</div>
					
					</li>
		<?php 					
				}
		?>
				</ul>
		<?php 		
			}	
		?>
	

</div>
<div>
</div>
		
</td>
</tr>
<?php	
	}
	}
	}
	}?>

</tbody>	
</table>



<style>
.item_list{padding:5px;background: #FFF;}
.item_list li{border-bottom:1px solid #cdcdcd;list-style: none;background: #FFF;}
.item_list li p{margin:5px 6px;width: 300px;text-align: justify;}
</style>

<script>
$('.datagridsort').tablesorter( {sortList: [[2,0]]} ); 
</script>