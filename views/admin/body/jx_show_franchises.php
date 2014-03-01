<div class="stats_bar" style="overflow: hidden">
	
	<table width="100%" cellpadding="0" cellspacing="0" style="font-size: 12px;">
		<tr>
			<td width="10%" align="left">
				<span class="stats_summary" style="padding:5px;"><b>Total :</b> <b style="font-size: 14px;"></span><?php echo $total_frans; ?></b></span>
			</td>
			<td  align="center">
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
			<td  align="right">
				<?php if($type != 1){ ?>
					<div class="fil_options" >
						<span class="fil_option">
							<b>Menu</b> :  
							<select class="inp" name="fil_menu" style="width: 250px;">
								<option value="">All</option>
								<?php 
									foreach($menu_list as $menu) 
										echo '<option value="'.$menu['menuid'].'" '.(($sel_menuid==$menu['menuid'])?'selected':'').' >'.$menu['menuname'].'</option>';
								?>
							</select>
						</span>
						<div>
							<span class="fil_option">
								<b>Territory</b> :  
								<select class="inp" name="fil_terr" style="width: 150px;">
									<option value="">All</option>
									<?php 
										foreach($terr_list as $terr) 
											echo '<option value="'.$terr['territory_id'].'" '.(($sel_terr_id==$terr['territory_id'])?'selected':'').' >'.$terr['territory_name'].'</option>';
									?>
								</select>
							</span>
							<span class="fil_option">
								<b>Town</b> :
								<select class="inp" name="fil_town" style="width: 150px;">
									<option value="">Choose </option>
									<?php 
										foreach($town_list as $town) 
											echo '<option value="'.$town['town_id'].'"  '.(($sel_town_id==$town['town_id'])?'selected':'').'  >'.$town['town_name'].'</option>';
									?>
								</select>
							</span>
						</div>
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
			<thead><tr><th>Sno</th><th>FID</th><th>Franchise Level</th><th>Franchise Name</th><!--  <th>Type</th>--><th>City | Territory</th>
				<?php /*?><th>Current Balance</th> */?><th>Assigned to</th><!--  <th>Class</th>--><?php /*?><th>Last OrderedOn</th><?php /*/?><th>RegisteredOn</th><th></th></tr></thead>
			<tbody>
			<?php $i=0; foreach($frans as $f){
				$fr_payment_det = $this->erpm->get_franchise_account_stat_byid($f['franchise_id']);
				$last_ordered_on = @$this->db->query("select from_unixtime(a.init) as last_ordered_on from king_transactions a where a.franchise_id = ? order by last_ordered_on desc limit 1",$f['franchise_id'])->row()->last_ordered_on;
				
				$fr_reg_diff = ceil((time()-$f['created_on'])/(24*60*60));
	 
				if($fr_reg_diff <= 30)
				{
					$fr_reg_level_color = '#cd0000';
					$fr_reg_level = 'Newbie';
				}
				else if($fr_reg_diff > 30 && $fr_reg_diff <= 60)
				{
					$fr_reg_level_color = 'orange';
					$fr_reg_level = 'Mid Level';
				}else if($fr_reg_diff > 60)
				{
					$fr_reg_level_color = 'green';
					$fr_reg_level = 'Experienced';
				}
				
			?>
			<tr class="<?php echo $f['is_suspended']?'row_warn':''?>" >
			<td>
			<?=++$i+($pg)?></td>
			<td><?=$f['pnh_franchise_id']?></td>
			<td>
				<span style="font-size: 11px;color: <?php echo $fr_reg_level_color;?>"><b><?php echo $fr_reg_level;?></b></span>
			</td>
			
			<td>
			<a class="link" href="<?=site_url("admin/pnh_franchise/{$f['franchise_id']}")?>"></a><?=$f['franchise_name']?></td>
			<!--  <td><?=$f['is_lc_store']?"LC Store":"Franchise"?></td>-->
			<td><?=$f['city']?> | <?=$f['territory_name']?></td>
			
			<?php /*?>
			<td>
				Rs <?=formatInIndianStyle($fr_payment_det['shipped_tilldate']-($fr_payment_det['paid_tilldate']+$fr_payment_det['acc_adjustments_val']+$fr_payment_det['credit_note_amt']),2)?>
			</td>
			<?php /*/?>
			<td><?=$f['owners']?></td>
			<!--  <td><?=$f['class_name']?></td>-->
			<?php /*?>
			<td><?=format_date($last_ordered_on)?></td>
			<?php /*/?>
			<td><?=format_date(date('Y-m-d',$f['created_on']))?></td>
			<td>
			<a style="white-space:nowrap" href="<?=site_url("admin/pnh_franchise/{$f['franchise_id']}")?>">view</a> &nbsp;&nbsp;&nbsp; 
			<a style="white-space:nowrap" href="<?=site_url("admin/pnh_edit_fran/{$f['franchise_id']}")?>">edit</a> &nbsp;&nbsp;&nbsp; 
			<a style="white-space:nowrap" href="<?=site_url("admin/pnh_manage_devices/{$f['franchise_id']}")?>">manage devices</a> &nbsp;&nbsp;&nbsp;
			<a style="white-space:nowrap" href="<?=site_url("admin/pnh_assign_exec/{$f['franchise_id']}")?>">assign executives</a>
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
</style>
