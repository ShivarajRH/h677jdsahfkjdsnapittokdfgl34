<?php
	
	$po_stats_flags = array(); 
	$po_stats_flags[''] = 'All';
	$po_stats_flags['0'] = 'Open';
	$po_stats_flags['1'] = 'Partial';
	$po_stats_flags['2'] = 'Close';
	
	$cond = '';
	$cond1 = '';
	if($sdate && $edate)
	{
		$cond .= ' and date(created_on) between "'.$sdate.'" and "'.$edate.'" ';
		$cond1 = ' and date(a.created_on) between "'.$sdate.'" and "'.$edate.'" ';
	}
		
	if($vid)
		$cond .= ' and vendor_id = "'.$vid.'" ';
	
	if($status == "")
		$status = -1;
?>
<style>
	.stat_block {display: inline-table;background: #F4F7EC;text-align: center;font-size: 16px;font-weight: bold;padding:5px 15px 2px 15px;line-height:20px;text-align: center }
	.stat_block b{font-size: 11px;display:block;}
	.color_blue{background: #E4EFF5}
	.color_red{background: #FA8B9D}
	.color_orange{background: #FFA500}
	.color_green{background: #9EDB9E}
</style>
<div class="page_wrap container">
	<div class="page_topbar">
		<div class="page_topbar_left fl_left" style="width: 30%">
			<h2 class="page_title">Manage Purchase Orders</h2>
		</div>
		<div class="page_topbar_right fl_right" align="right" style="width: 70%">
			<span >
				Date range: <input type="texT" size="8" class="inp" id="ds_range" value="<?=$sdate?>"> to <input size="8" type="text" class="inp"id="de_range" value="<?=$edate?>">
				<input type="button" value="Filter" onclick='rl_pgbyfilters()'> 
			</span>
			<span >
				Vendor : 
				<select name="ven_id" style="width: 200px;" onchange="rl_pgbyfilters()">
					<option value="">All</option>
					<?php
						$ven_list_res = $this->db->query("select b.vendor_id,vendor_name from t_po_info a join m_vendor_info b on a.vendor_id = b.vendor_id where 1 $cond1 group by vendor_id order by vendor_name ");
						if($ven_list_res->num_rows())
							foreach($ven_list_res->result_array() as $ven_det)
								echo '<option '.($vid == $ven_det['vendor_id']?'selected':'').' value="'.($ven_det['vendor_id']).'">'.($ven_det['vendor_name']).'</option>';
					
					?>
				</select>
			</span>
			<span>
				Status : 
				<select name="po_status" style="width: 100px;" onchange="rl_pgbyfilters()">
					<option value="-1" <?php echo ($status == ""?'selected':'') ?> >All</option>
					<option value="0" <?php echo ($status == "0"?'selected':'') ?> >Open</option>
					<option value="1" <?php echo ($status == "1"?'selected':'') ?> >Partial</option>
					<option value="2" <?php echo ($status == "2"?'selected':'') ?> >Close</option>
				</select>
			</span>
			
		</div>	
	</div>
	<br>
	<br>
	<br>
	<div class="page_topbar" align="left">
		 
			
			<div class="stat_block color_red"><b>Open</b> <span><?=$this->db->query("select count(1) as l from t_po_info where po_status=0 $cond ")->row()->l?></span>
			<?php /*?> 
			<div style="font-size:9px;">(Rs <?=format_price($this->db->query("select sum(total_value) as l from t_po_info a where 1 $cond1 and po_status = 0  ")->row()->l,0)?>)</div>
			<?php /*/?>
			</div>
			<div class="stat_block color_orange"><b>Partial</b><span><?=$this->db->query("select count(1) as l from t_po_info where po_status=1 $cond ")->row()->l?></span> 
			<?php /*?><div style="font-size:9px;">(Rs <?=format_price($this->db->query("select sum(total_value) as l from t_po_info a where 1 $cond1  and po_status = 1 ")->row()->l,0)?>)</div><?php /*/?>
			</div>
			<div class="stat_block color_green"><b>Closed</b> <span><?=$this->db->query("select count(1) as l from t_po_info where po_status=2 $cond ")->row()->l?></span> 
			<?php /*?><div style="font-size:9px;">(Rs <?=format_price($this->db->query("select sum(total_value) as l from t_po_info a where 1 $cond1 and po_status = 2 ")->row()->l,0)?>)</div><?php /*/?>
			</div>
			<div class="stat_block color_blue"><b>Total</b> <span><?=$this->db->query("select count(1) as l from t_po_info where 1 $cond ")->row()->l?>
				<?php /*?><div style="font-size:9px;">(Rs <?=format_price($this->db->query("select sum(total_value) as l from t_po_info a where 1 $cond1 ")->row()->l,0)?>)</div><?php /*/?>
			</span> </div>
			
		 
	</div>
	<div class="page_topbar" align="right">
		<div class="log_pagination fl_right">
			<?php echo $pagination?>
		</div>
		<div class="fl_left">
			<b style="vertical-align: baseline;display: inline-block;font-size:16px;padding:10px 0px;padding-top:14px">
				<?php if(count($pos)) { ?>
				Showing <?php echo ($pg+1).'-'.($pg+count($pos)).'/'.$total_po ?> <?php echo (($status!="")?$po_stats_flags[$status]:'');?> POs
				<?php }else{
				?>
				No POs Found
				<?php 	
				} ?>
			</b>
		</div>	
	</div>
	
	
<div class="page_content ">

<table class="datagrid" style="width: 100%">
<thead>
<tr>
<th>Reference</th>
<th>Order Date</th>
<th>Supplier</th>
<th>Expected Date</th>
<th>PO Value</th>
<th>Purchase Status</th>
<th>Stock Status</th>
<th></th>

</tr>
</thead>
<tbody>
<?php foreach($pos as $p){?>
<tr class="<?php echo $p['po_status']==3?'warn':''?>" >
<td>PO<?=$p['po_id']?></td>
<td><?=date("d/m/y g:ia ",strtotime($p['created_on']))?></td>
<td><a target="_blank" href="<?php echo site_url("admin/vendor/{$p['vendor_id']}")?>"><?=$p['vendor_name']?></a><br><?=$p['city']?></td>
<td><?php echo format_date($p['date_of_delivery'])?></td>
<td>Rs <?=number_format($p['total_value'])?></td>
<td><?php switch($p['po_status']){
	case 1:
	case 0: echo 'Open'; break;
	case 2: echo 'Complete'; break;
	case 3: echo 'Cancelled';
}?></td>
<td>
<?php switch($p['po_status']){
	case 0: echo 'Not received'; break;
	case 1: echo 'Partially received'; break;
	case 2: echo 'Fully received'; break;
	case 3: echo 'NA';
}?>
</td>
<td>
<a class="link" href="<?=site_url("admin/viewpo/{$p['po_id']}")?>">view</a>
<?php if($p['po_status']!=2 && $p['po_status']!=3){?>
&nbsp;&nbsp;&nbsp;<a href="<?=site_url("admin/apply_grn/{$p['po_id']}")?>">Stock Intake</a>
<?php }?>&nbsp;<a onclick="print_po(<?=$p['po_id']?>)" >Print po</a>

</td>

</tr>
<?php } if(empty($pos)){?><tr><td colspan="100%">no POs to show</td></tr><?php }?>
<?php if($pagination){ ?>
<tr>
	<td colspan="12" align="right"><div class="log_pagination"><?php echo $pagination;?></div></td>
</tr>
<?php } ?>

</tbody>
</table>

</div>
</div>


<script type="text/javascript">
function rl_pgbyfilters()
{
	var stat = $('select[name="po_status"]').val();
	var ven_id = $('select[name="ven_id"]').val();
	
	var ds = $('#ds_range').val();
	var de = $('#de_range').val();
		
	
		ds = ds?ds:'0';
		de = de?de:'0';
		
		
		ven_id = ven_id?ven_id:0;
		stat = stat?stat:'';
	
		location=site_url+'/admin/purchaseorders/'+ds+"/"+de+'/'+ven_id+'/'+stat;
	
}
$(function(){
	$("#ds_range,#de_range").datepicker();
	 $('.datagrid').jq_fix_header_onscroll();	
	
}); 

function print_po(poid)
{
	var print_url = site_url+'/admin/print_po/'+poid;
		window.open(print_url);
}
$('.leftcont').hide();
</script>
<style>
.warn td{background: #FDD2D2 !important;}

</style>
<?php
