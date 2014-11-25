<?php 
$fran_type=array();
$fran_type[0]='Normal Franchise';
$fran_type[1]='Rural Franchise';
$fran_type[2]='Rural Master Franchise';

?>
<div class="stats_bar" style="overflow: hidden">
	
	<table width="100%" cellpadding="0" cellspacing="0" style="font-size: 12px;">
		<tr>
			<td  align="left">
				<?php if($type != 1){ ?>
				<div id="franby_aphabets" class="fil_alpha" >
					<a href="javascript:void(0)" onclick=showby_aplha('') class="<?php echo (($alpha===0)?'selected':'');?>" >ALL</a>
					<?php
						$chrs = 'abcdefghijklmnopqrstuvwxyz';
						for($i=0;$i<strlen($chrs);$i++)
						{
							echo '<a href="javascript:void(0)" onclick=showby_aplha("'.$chrs[$i].'") class="'.(($alpha===$chrs[$i])?'selected':'').'">'.$chrs[$i].'</a>';
						}
					?>
				</div>
				<?php } ?>
			</td>
			<td width="7%" align="right">
				<span class="stats_summary" style="padding:5px;"><b>Total :</b> <b style="font-size: 14px;"></span><?php echo $total_frans; ?></b></span>
			</td>
		</tr>
		<tr>	
			<td  align="right">
				<?php if($type != 1){ ?>
					<div class="fil_options" >
						<div class="fl_left sel_opt">
							Menu :  
							<select class="inp" name="fil_menu" style="width: 200px;">
								<option value="">All</option>
								<?php 
									foreach($menu_list as $menu) 
										echo '<option value="'.$menu['menuid'].'" '.(($sel_menuid==$menu['menuid'])?'selected':'').' >'.$menu['menuname'].'</option>';
								?>
							</select>
						</div>
						
						<div class="fl_left sel_opt">
							Territory :  
							<select class="inp" name="fil_terr" style="width: 200px;">
									<option value="">All</option>
									<?php 
										foreach($terr_list as $terr) 
											echo '<option value="'.$terr['territory_id'].'" '.(($sel_terr_id==$terr['territory_id'])?'selected':'').' >'.$terr['territory_name'].'</option>';
									?>
								</select>
						</div>
						<div class="fl_left sel_opt">
							Town :
							<select class="inp" name="fil_town" style="width: 200px;">
									<option value="">Choose </option>
									<?php 
										foreach($town_list as $town) 
											echo '<option value="'.$town['town_id'].'"  '.(($sel_town_id==$town['town_id'])?'selected':'').'  >'.$town['town_name'].'</option>';
									?>
								</select>
						</div>
						<?php if($type != 6){?>
						<div class="fl_left sel_opt">
							Franchise Type :
							<select class="inp" name="fil_ftype" style="width: 200px;">
								<option value="-1">Choose</option>
									<?php $franchise_types=$this->config->item("franchise_type"); 
								if($franchise_types) {
									foreach($franchise_types as $ft=>$ftype) { ?>
										<option value="<?=$ft;?>" <?php echo ($ft==$sel_ftype)?'selected':"";?> ><?=$ftype;?></option>
							<?php   }
								} ?>								
							</select>
						</div>
						<?php }?>
						<?php /* if($type == 6){?>
						<div class="fl_left sel_opt">
							<a style="cursor: pointer;margin-top:3px;" target="_blank" href="<?php echo site_url('/admin/add_bulk_rf_franchise');?>" class="button button-tiny button-flat-action"><b>Link RMF</b></a> 
						</div>
						<?php }*/?>
					</div>
					<?php } ?>
			</td>
		</tr>
	</table>
</div>



<div id="jx_frlist" style="clear:both">
	<?php 
		if(!count($frans))
		{
			echo '<div align="center" style="margin:10px;">No franchises found</div>';
		}else
		{
	?>
		<table class="datagrid" width="100%" >
			<thead><tr><th>Sno</th><th>FID</th><th>Franchise Level</th><th>Franchise Name</th> <th>Type</th><th>City | Territory</th>
				<?php /*?><th>Current Balance</th> */?><!--<th>Assigned to</th>  <th>Class</th>--><?php /*?><th>Last OrderedOn</th><?php /*/?><th>RegisteredOn</th><th></th></tr></thead>
			<tbody>
			<?php $i=0; foreach($frans as $f){
				$fr_payment_det = $this->erpm->get_franchise_account_stat_byid($f['franchise_id']);
				$last_ordered_on = @$this->db->query("select from_unixtime(a.init) as last_ordered_on from king_transactions a where a.franchise_id = ? order by last_ordered_on desc limit 1",$f['franchise_id'])->row()->last_ordered_on;
				
				$fran_exp = $this->erpm->fran_experience_info($f['created_on']);
			?>
			<tr class="<?php echo $f['is_suspended']?'row_warn':''?>" >
			<td>
			<?=++$i+($pg)?></td>
			<td><?=$f['pnh_franchise_id']?></td>
			<td>
				<span style="font-size: 11px;color: <?php echo $fran_exp['f_color'];?>"><b><?php echo $fran_exp['f_level'];?></b></span>
			</td>
			
			<td>
			<a class="link" href="<?=site_url("admin/pnh_franchise/{$f['franchise_id']}")?>"></a><?=$f['franchise_name']?></td>
			 <td><?='<b>'.$fran_type[$f['franchise_type']].'<b>';?></td>
			<td><?=$f['city']?> | <?=$f['territory_name']?></td>
			
			<?php /*?>
			<td>
				Rs <?=formatInIndianStyle($fr_payment_det['shipped_tilldate']-($fr_payment_det['paid_tilldate']+$fr_payment_det['acc_adjustments_val']+$fr_payment_det['credit_note_amt']),2)?>
			</td>
			<?php /*/?>
			<!--<td><?=$f['owners']?></td>
			  <td><?=$f['class_name']?></td>-->
			<?php /*?>
			<td><?=format_date($last_ordered_on)?></td>
			<?php /*/?>
			<td><?=format_date(date('Y-m-d',$f['created_on']))?></td>
			<td>
			<a style="white-space:nowrap" href="<?=site_url("admin/pnh_franchise/{$f['franchise_id']}")?>">view</a> &nbsp;&nbsp;&nbsp; 
			<a style="white-space:nowrap" href="<?=site_url("admin/pnh_edit_fran/{$f['franchise_id']}")?>">edit</a> &nbsp;&nbsp;&nbsp; 
			<!--  <a style="white-space:nowrap" href="<?=site_url("admin/pnh_manage_devices/{$f['franchise_id']}")?>">manage devices</a> &nbsp;&nbsp;&nbsp;
			<a style="white-space:nowrap" target="_blank" href="<?=site_url("admin/pnh_assign_exec/{$f['franchise_id']}")?>">assign executives</a> &nbsp;&nbsp;&nbsp;-->
			<?php if($type==6){?>
				<a style="white-space:nowrap" href="<?=site_url("admin/pnh_edit_fran/{$f['franchise_id']}#v_pnh_details")?>">Assign RMF</a>
			<?php }?>
			</td>
			</tr>
			<?php }?>
			</tbody>
		</table>
	<?php
		} 
	?>
</div>
<div class="pagination">
	<?php echo $pagination;?>
</div>

<style>
.stats_bar{padding:5px;background: #f2f2f2;}
.row_warn td{background: #FFCECA !important}
.sel_opt
{
	 margin: 8px 15px 8px 2px;
}
</style>
