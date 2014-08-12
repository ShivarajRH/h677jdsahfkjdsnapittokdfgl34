<?php
/**
 * View departments and assined request list and updation page
 * @author Shivaraj <Shivaraj@storeking.in>_Jul_12_2014
 */
//print_r($req_list);
//print_r($dept_list);
?>
<div class="container">
<h2>Departments Requests</h2>
	<table class="datagrid" width="100%">
		<thead>
			<tr>
				<th width="2%">Slno</th>
				<th width="5%">Dept ID</th>
				<th width="20%">Department</th>
				<th width="70%">Request List</th>
				<th width="10%">Actions</th>
			</tr>
		</thead>
		<tbody>
<?php
		foreach($dept_list as $i=>$dept)
		{
?>
		
			<form method="post">
			<tr>
				
				<td>
					<?=(++$i);?>
				</td>
				<td>
					<?=$dept['dept_id'];?>
				</td>
				<td>
					<input type="hidden" name="sel_dept_id" size="3" value="<?=$dept['dept_id'];?>"/>
						<?=$dept['name'];?>
				</td>
				<td>
<?php
					foreach($req_list as $req)
					{
						$chk_sts = " ";
						//echo "<br> {$req['id']}, {$dept['type_ids']}"."";
						
						if(match_in_list($req['id'], "{$dept['type_ids']}"))
								$chk_sts = " checked ";
						
?>
					
					<span class="fl_left"><input type="checkbox" name="sel_type_ids[]" value="<?=$req['id'];?>" size="50" class="fl_left inpadding" <?=$chk_sts;?> /><?=$req['name'];?></span>
<?php
					}
?>
				</td>
				<td align="center">
					<input type="submit" value="Update" class="button button-tiny button-action">
				</form>
				</td>
			</tr>
	
<?php
		}
?>

		</tbody>
	</table>
</div>
<?php /*<input type="text" name="ifsc_code" value="<?php echo set_value('ifsc_code');?>" size="20"><?php echo form_error('ifsc_code','<div class="error">','</div>')?>*/ ?>

<style>
	.inpadding { padding: 5px;}
</style>