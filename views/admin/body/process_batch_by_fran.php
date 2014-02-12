<?php 

$is_pnh=false;
$inv=$invoices[0];

if(empty($inv['transid'])) 
	$inv['transid']=$inv['pi_transid'];
if($this->db->query("select is_pnh from king_transactions where transid=?",$inv['transid'])->row()->is_pnh==1)
	$is_pnh=true; 


//group the transactions by franchise id
$is_fran_suspended = 0;
$fr_reg_level = $fr_reg_level_color = '';
$trans_fran_link=array();
if($is_pnh)
{
	foreach($invoices as $inv)
	{
		
		if(empty($inv['transid'])) 
			$inv['transid']=$inv['pi_transid'];
		
		$fran=$this->db->query("select f.created_on as registered_on,f.is_suspended,f.franchise_name,f.franchise_id,t.territory_name,t.id as terry_id,tw.id as town_id from king_transactions ta join pnh_m_franchise_info f on f.franchise_id=ta.franchise_id join pnh_m_territory_info t on t.id=f.territory_id join pnh_towns tw on tw.id=f.town_id where ta.transid=?",$inv['transid'])->row_array();
		 
		
		if(!$fran)
			continue;
		if(!isset($trans_fran_link[$fran['franchise_id']]))
		{
			$trans_fran_link[$fran['franchise_id']]=array();
			$trans_fran_link[$fran['franchise_id']]['frans_det']='';
			$trans_fran_link[$fran['franchise_id']]['trans_det']=array();
			
		}
		
		if(!$fran)
			continue;
		
		$is_fran_suspended = $fran['is_suspended']?1:0;
		
		$fr_reg_diff = ceil((time()-$fran['registered_on'])/(24*60*60));
		
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
		
		$fran['fr_reg_level']=$fr_reg_level;
		$fran['fr_reg_level_color']=$fr_reg_level_color;
		$trans_fran_link[$fran['franchise_id']]['frans_det']=$fran;
		
		if($inv['p_invoice_status']==0)
		{
			continue;
		}
		
		array_push($trans_fran_link[$fran['franchise_id']]['trans_det'],$inv);
	}
	
}

?>

<div class="page_wrap container">
	<div class="clearboth">
		<div class="fl_left" >
			<h2 class="page_title">Shipment Batch Process by franchise: BATCH<?=$batch['batch_id']?> - 
				<b style="font-size: 90%">
				<?php
					if($batch['status'] == 0)
						echo '<span  style="color:#cd0000">Open</span>';
					else if($batch['status'] == 1)
					 	echo '<span  style="color:#cd0000">Partial</span>';
					else if($batch['status'] == 2)
					 	echo '<span  style="color:green">Closed</span>';
				?>
				</b>
			</h2>
		</div>
		<div class="fl_right stats" >
		</div>
	</div>
	
	<div class="page_topbar" >
		<div class="page_topbar_left fl_left" >
		</div>
		<div class="page_action_buttonss fl_right" align="right">
		</div>
	</div>
	
	<div style="clear:both">&nbsp;</div>
	
	<div class="page_content">
		<table class="datagrid" cellpadding="5" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>#</th>
					<th width="100">Total proforma invoices </th>
					<th width="100">Total transactions</th>
					<th>Franchise</th>
					<th width="100">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php 
					if($trans_fran_link)
					{
						$si=0;	
						foreach($trans_fran_link as $i=> $det)
						{
							$si+=1;
							?>
							<tr>
								<td><?php echo $si;?></td>
								<td><?php echo count($det['trans_det']);?></td>
								<td><?php echo count($det['trans_det']);?></td>
								<td>
									<a href="<?php echo site_url('admin/pnh_franchise/'.$det['frans_det']['franchise_id'])?>" target="_blank"><?php echo $det['frans_det']['franchise_name'];?></a><br>
									<span style="font-size: 11px;color:<?php echo $det['frans_det']['fr_reg_level_color'];?>">(<b><? echo $det['frans_det']['fr_reg_level'];?></b>)</span>
								</td>
								<td>
									<a href="<?php echo site_url('admin/pack_invoice/'.$bid.'/1/'.$det['frans_det']['franchise_id'])?>">Prepare invoice</a>
								</td>
							</tr>
							<?php 
						}
					}
				?>
			</tbody>
		</table>
	</div>
</div>

