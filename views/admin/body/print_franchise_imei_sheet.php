<div class="page_wrap container">
	<div class="page_topbar">
		<h3 class="page_title">Print Franchise IMEI Activation Sheet</h3>
	</div>
	<div class="page_content">
		<form>
			<table cellpadding="5" cellspacing="0">
				<tr>
					<td colspan="4">
						<b>Start Date</b> <input type="text" size="10" id="st_date" name="st_date" value="">
						<b>End Date</b><input type="text" size="10"  id="en_date" name="en_date" value="">
					</td>
				</tr>
				<tr>
					<td><b>Territory</b></td>
					<td>
						<select name="tr_id[]" class="chossen" style="width: 250px;" >
							<option value="">Choose</option>
							<?php
								$tr_list_res = $this->db->query("select id,territory_name from pnh_m_territory_info order by territory_name asc ");
								if($tr_list_res->num_rows())
								{
									foreach($tr_list_res->result_array() as $tr_det)
									{
										echo '<option value="'.($tr_det['id']).'">'.($tr_det['territory_name']).'</option>';
									}
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td><b>Franchises</b></td>
					<td>
						<select name="fr_id[]" class="chossen" data-placeholder="Choose Franchises" style="width: 300px;">
							
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="right">
						<input type="submit" value="Generate" >
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
<script type="text/javascript">

	function fetch_pending_imei_frdet()
	{
		
	}
	
	$(function(){
		$('.chossen').chosen();	
		
		$('select[name="tr_id[]"]').change(function(){
			trid = $(this).val();
			$('select[name="fr_id[]"]').html('').trigger('liszt:updated');
			$.post(site_url+'/admin/jx_getfranchisebytrid',{trid:trid},function(resp){
				if(resp.status == 'error')
				{
					alert(resp.msg);
				}else
				{
					var fr_list = '<option value="" selected>All</option>';
						$.each(resp.fr_list,function(a,b){
							fr_list += '<option value="'+b.franchise_id+'">'+b.franchise_name+'</option>';
						});
						$('select[name="fr_id[]"]').html(fr_list).trigger('liszt:updated');
				}
			},'json');
		});
		
		prepare_daterange('st_date','en_date');
		
		$('#st_date,#en_date').change(function(){
			var sdate = $('#st_date').val();
			var edate = $('#en_date').val();
				if(sdate != '' && edate != '')
				{
					$.post()
				}
		});
		
	});
	
</script>
