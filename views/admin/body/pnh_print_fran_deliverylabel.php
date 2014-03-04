
		<?php if($fr_details){
		?>
			<span class="noprint" style="position:fixed;right:0px;top:0px;padding:3px;">
				<a href="javascript:void(0)" onclick="window.print()">Print</a>
			</span>	
		<?php	
			foreach($fr_details as $details){
		?>
		<style>
		body{font-family: arial;margin:5%;font-size: 125%;line-height: 1.3em;}
		@media print {.noprint{display:none}}
		</style>
		<div align="center" style="page-break-after: always">
			<br >
			<h1 style="font-size:260%;line-height: 100%"><?php echo $details['franchise_name']; ?></h1>
			
			<?php
				$courier_res = $this->db->query('select a.*,b.courier_name as courier_name_pro_1,c.courier_name as courier_name_pro_2,d.courier_name as courier_name_pro_3
														from pnh_town_courier_priority_link a 
														left join m_courier_info b on a.courier_priority_1 = b.courier_id
														left join m_courier_info c on a.courier_priority_2 = c.courier_id
														left join m_courier_info d on a.courier_priority_3 = d.courier_id
														where town_id = ? and a.is_active = 1 order by a.id desc limit 1 
								',$details['town_id']);
				
				if($courier_res->num_rows())
				{
					$courier_det = $courier_res->row_array();
					if($courier_det['courier_priority_1'])
					{
						echo '<h3 style="margin:10px 0px">Courier : '.$courier_det['courier_name_pro_1'].' - Mode : '.(($courier_det['delivery_type_priority1']==0)?'By Hand':'To Door').' - Delivery in '.($courier_det['delivery_hours_1']).'Hrs </h3>';
					}else if($courier_det['courier_priority_2'])
					{
						echo '<h3 style="margin:10px 0px">Courier : '.$courier_det['courier_name_pro_2'].' - Mode : '.(($courier_det['delivery_type_priority2']==0)?'By Hand':'To Door').' - Delivery in '.($courier_det['delivery_hours_2']).'Hrs </h3>';
					}else if($courier_det['courier_priority_3'])
					{
						echo '<h3 style="margin:10px 0px">Courier : '.$courier_det['courier_name_pro_3'].' - Mode : '.(($courier_det['delivery_type_priority3']==0)?'By Hand':'To Door').' - Delivery in '.($courier_det['delivery_hours_3']).'Hrs </h3>';
					}else
					{
						echo '<br>';
					} 
				}else
				{
					echo '<br>';
				}
			?>
			
			<div style="width: 100%">
				<fieldset style="padding:3%;border:2px solid #000;">
					<div style="margin:5px;text-align: left;font-size: 140%;line-height: 140%;">
					<?php echo $details['address'];?>
					<br>
					<?php echo $details['city'].' - '.$details['postcode'];?>
					<br>
					Mob - <?php echo $details['login_mobile1'];?>
					</div>
				</fieldset>
			</div>
		
			<br >
			
			<div style="font-size:150%;line-height: 130%">
				<span style="font-size: 70%">FROM</span>	
				<h2 style="margin-bottom: 3px;font-weight: normal;margin-top: 5px;">Storeking</h2>
				<p style="margin-top: 0px;font-size:70%;">
					Plot 3B1,KIADB Industrial area,Kumbalgudu 1st Phase ,Bangalore - 560074<br />
					<b>PH - 08028437605 / 18002001996 </b> 
				</p>		
			</div>
		</div>
	<?php } ?>
	<script>
		window.print();
	</script>
	<?php }
	else
	{?>
		
		<div class="container">
		<div style="float:left;width:100%">
			<h2>Franchise Delivary Label</h4>
		 </div>
		 <table width="100%">
		 	<tr>
		 		<td width="250">
		 			<form id="franch_delvry_id" style="width:100%">
						<table cellpadding="3" class="datagrid">
							<tr>
								<td><b>Territory</b></td>
								<td>
									<select name="territory" class="territory" style="margin-top:10px;width: 250px;">
										<option value="0">All</option>
										<?php foreach($this->db->query("select id,territory_name from pnh_m_territory_info order by territory_name asc")->result_array() as $t){?>
											<option value="<?=$t['id']?>" <?=$t['id']==$this->uri->segment(3)?"selected":""?>><?=$t['territory_name']?></option>
										<?php }?>
									</select>
								</td>
							</tr>
							<tr>
								<td><b>Town</b></td>
								<td>
									<select name="town[]" class="town" multiple="true" data-placeholder="All" style="width:250px">
										<option value="0">All</option>
									</select>
								</td>
							</tr>
							<tr>
								<td colspan="2" align="right">
									<input type="submit" class="button button-rounded button-small button-action" value="generate" >
								</td>
							</tr>
						</table>
					</form>
					<style>
						tfoot{display: none !important;}
					</style>
		 		</td>
		 		<td valign="top" width="900" style="border:1px solid #ccc">
		 			<iframe id="hndl_load_franlabels" name="hndl_load_franlabels" style="width:100%;border:0px;height:400px;"></iframe>
		 		</td>
		 	</tr>
		 </table>
		 
		 
		</div>
		<style>.leftcont{display:none}</style>
		
<script>
	$('.territory').chosen();
	$('.town').chosen();
	$('select[name="territory"]').change(function(){
  		if($('select[name="territory"]').val() == '0')
		{
			 $('select[name="town[]"]').val() == '0'
		}
        $('select[name="town[]"]').html('').trigger("liszt:updated");
      	
      	
      
		var sel_territory_id = $(this).val();
		$.post("<?=site_url("admin/pnh_fran_delvry_label_bytown")?>",{sel_territory_id:$('select[name="territory"]').val()},function(resp){
			if(resp.status == 'error'){
				alert(resp.message);
				$('select[name="territory"]').val('0');
			}else{
				var town_list_html = '';
				
					$.each(resp.town_list,function(i,itm){
						town_list_html += '<option value="'+itm.id+'">'+itm.town_name+'</option>';
					});
					$('select[name="town[]"]').html(town_list_html).trigger("liszt:updated");
			}
		},'json');
		
		
     
	});
			
	$('#franch_delvry_id').submit(function(){
		
	  var town_id = $('select[name="town[]"]').val();
	  var terr_id = $('select[name="territory"]').val();
	  	
	  		if(town_id == null)
	  			town_id = 0;
	  		if(terr_id == null)
	  			terr_id = 0;
	  
	  		$('#hndl_load_franlabels').attr('src',"<?=site_url("admin/pnh_fran_delivary_label")?>/"+terr_id+'/'+town_id);
			
		return false;	
	});

	
</script>
	<?php
	}
	?>
		
	
	
	
