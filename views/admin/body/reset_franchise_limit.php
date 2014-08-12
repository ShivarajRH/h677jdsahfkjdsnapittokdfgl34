<div class="page_wrap">
	<h2 class="page_title">Reset Franchise Limit</h2>
	<div class="page_content">
		<form id="reset_franchise_limit_frm" action="<?php echo site_url('admin/process_reset_franchise_limit');?>" method="post">
		<table>
			<tr>
				<td><b>Franchise</b></td>
				<td>
					<select name="fran_id[]">
						<option value="0">All</option>
						<?php foreach($franchise_list as $fran){?>
							<option value="<?php echo $fran['franchise_id']?>"><?php echo $fran['franchise_name']?></option>
						<?php }?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" value="Reset">
				</td>
			</tr>
		</table>	
		</form>	
	</div>
</div>
 

<script type="text/javascript">
	 $('#reset_franchise_limit_frm').submit(function(){
		 if(confirm("Are you sure want to reset limit ?")
		 	return true;
		 return false;
	 });
</script>