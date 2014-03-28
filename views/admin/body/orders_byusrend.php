<?php 
$sus_fran_list = array();
$sus_fran_list_res = $this->db->query('select franchise_id from pnh_m_franchise_info where is_suspended = 1');
if($sus_fran_list_res->num_rows())
{
	foreach($sus_fran_list_res->result_array() as $fr)
	{
		$sus_fran_list[] = $fr['franchise_id'];
	}
}

?>


<style>
	.subdatagrid{width: 100%}
	.subdatagrid th{padding:5px;font-size: 11px;background: #F4EB9A;color: maroon}
	.subdatagrid td{padding:3px;font-size: 12px;}
	.subdatagrid td a{color: #121213;}
	.processed_ord td,.shipped_ord td{text-decoration: line-through;color: green  !important;}
	.processed_ord td a,.shipped_ord td a{text-decoration: line-through;color: green !important;}
	.cancelled_ord td{text-decoration: line-through;color: #cd0000 !important;}
	.cancelled_ord td a{text-decoration: line-through;color: #cd0000 !important;}
	.tgl_ord_prod {display: block;min-width: 300px;padding:5px;background: #fafafa;}
	.tgl_ord_prod a{display: block;text-align: center;color: #333;font-size: 12px;text-decoration: underline;}
	.tgl_ord_prod_content {display: none;}
	.pagination{float: right;}
	.pagination a{background: #FFF;display: inline-block;color: #000;padding:3px;}
</style>
<div class="container">

<div style="overflow: hidden;clear: both;clear: left;margin-top: 12px;">
	<h2 ><?=!isset($pagetitle)?"Recent 50 ":""?>Orders By Users Via SMS <?=isset($pagetitle)?$pagetitle:""?></h2>
</div>

<div style="float: right">
	<div class="dash_bar" style="padding:7px;margin-top: -43px;">
		Date range: <input type="text" size="8" class="inp" id="ds_range" value="<?=$this->uri->segment(4)?>"> to <input size="8" type="text" class="inp"id="de_range" value="<?=$this->uri->segment(5)?>"> <input type="button" value="Show" onclick='showrange()'>
	</div>
	
<div style="float: right">
	<div class="dash_bar" style="padding:7px;margin-top: -43px;">
		Confirmed:
		<select name="o_confirm_status" id="o_confirm_status">
			<option value="0">All</option>
			<option value="1">Yes</option>
			<option value="2">No</option>
		</select>
	</div>
</div>
</div>
<div>
<table class="datagrid datagridsort" width="100%" id="usr_ordr">
<thead>
<tr>
<th>Sl no</th>
<th>Order From(Member Details)</th>
<th>Franchise Details</th>
<th width="400">Deal/Product Details 
	<a href="javascript:void(0)" style="text-decoration: underline;font-size: 11px;color: #FFF;float: right" id="exp_col_list">Show/Hide orders</a>
</th>
<th width="120">Ordered on</th>
<th width="60">Status</th>
<th>Action</th>

</tr>
</thead>
<tbody>

<?php  $i=1; foreach($user_orders as $uo){
	
	if($uo['is_pnh'])
	{
		$is_fran_suspended = in_array($uo['franchise_id'], $sus_fran_list)?1:0;
	
	
		$fr_det = $this->db->query('select franchise_name,created_on from pnh_m_franchise_info where franchise_id = ? ',$uo['franchise_id'])->row();
	
		$f_created_on = $fr_det->created_on;
		$uo['franchise_name'] = $fr_det->franchise_name;
	
	
	
		$fr_reg_diff = ceil((time()-$f_created_on)/(24*60*60));
			
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
	}
?>
<tr <?=$uo['priority']?"style='background:#ff8;'":""?> class="<?=$uo['status']!=0?"y_c":"n_c";?>">
<td><?=$i; ?></td>

<td>
	<a target="_blank" href="<?=site_url("admin/user/{$uo['userid']}")?>"><?=$uo['name']?></a>
	<?php $mem_det=$this->db->query("select first_name,last_name,mobile,pnh_member_id from pnh_member_info where user_id=?",$uo['userid'])->row_array();?>
	<br>
		MID:<?php echo  $mem_det['pnh_member_id'];?>
	<br>
		Mobile : <?php echo  $mem_det['mobile'];?>
</td>

<td>
	<a target="_blank" href="<?=site_url("admin/pnh_franchise/{$uo['franchise_id']}")?>"><?=$uo['franchise_name']?></a>
</td>
<td style="padding:0px;background: #fafafa !important;">
		<div class="tgl_ord_prod"><a href="tgl_ord_prods">Show Deals</a></div>
		<div class="tgl_ord_prod_content">
			<table class="subdatagrid" cellpadding="0" cellspacing="0">
				<thead>
					<th>Slno</th>
					<th>OID</th>
					<th width="200">ITEM</th>
					<th>QTY</th>
					<th>MRP</th>
					<th>Amount</th>
				</thead>
				<tbody>
					<?php 
						$uo_item_list = $this->db->query("select a.status,a.id,a.itemid,b.name,a.quantity,i_orgprice,i_price,i_discount,i_coup_discount from king_user_orders a
															join king_dealitems b on a.itemid = b.id 
															where a.transid = ? order by a.status 
														",$uo['transid'])->result_array();
						
						$uoi = 0;
						foreach($uo_item_list as $uo_item)
						{
							$is_cancelled = ($uo_item['status']==3)?1:0;
							$uord_stat_txt = '';
							if($uo_item['status'] == 0)
								$uord_stat_txt = 'pending';
							else if($uo_item['status'] == 1)
							 	$uord_stat_txt = 'processed';
							else if($uo_item['status'] == 2)
							 	$uord_stat_txt = 'shipped';
							 else if($uo_item['status'] == 3)
							 	$uord_stat_txt = 'cancelled';	
							 	
							 $i_menuid=$this->db->query("select menuid from king_deals a join king_dealitems b on a.dealid = b.dealid where b.id = ? ",$uo_item['itemid'])->row()->menuid;
							 $i_menu[]= $i_menuid;
					?>
						<tr class="<?php echo $uord_stat_txt.'_ord'; $uo['status']!=0?"y_c":"n_c" ;?>   <?php echo 'obymenu_'.$i_menuid;?>  ">
							<td width="20"><?php echo ++$uoi; ?></td>
							<td width="40"><?php echo $uo_item['id'] ?></td>
							<td><?php echo anchor('admin/pnh_deal/'.$uo_item['itemid'],$uo_item['name']) ?></td>
							<td width="20"><?php echo $uo_item['quantity'] ?></td>
							<td width="40"><?php echo $uo_item['i_orgprice'] ?></td>
							<td width="40"><?php echo round($uo_item['i_orgprice']-($uo_item['i_coup_discount']+$uo_item['i_discount']),2) ?></td>
						</tr>	
					<?php 		
						}
					?>
				</tbody>
			</table>
		</div>
	</td>
	<td><?=format_datetime_ts($uo['init'])?></td>
	<td>
	<?php switch($uo['status']){
			case 0: echo "Pending"; break;
			case 1: 
				if(isset($invoices[$uo['transid']]) && $this->db->query("select 1 from shipment_batch_process_invoice_link where packed=1 and invoice_no in ('".implode("','",$invoices[$uo['transid']])."')")->num_rows()==0) echo "Invoiced"; else echo "Packed"; break;
			case 2: echo "Shipped"; break;
			case 3: echo "Canceled"; break;
		}?>
		<?php $c_transid= @$this->db->query("select transid from king_orders where transid=?",$uo['transid'])->row()->transid;?>
		
		<a  target="_blank" href="<?php echo site_url("admin/trans/{$c_transid}")?>"><?php echo $c_transid?></a>
	</td>
	<td>
	<?php if($uo['status'] !=0){?>
		<b>Confirmed</b>
		<?php }else{?>
		<a href="javascript:void(0)" class="button button-tiny_wrap cursor button-primary clone_rows_invoice" onclick=confirm_trans("<?php echo $uo['transid']?>") >Confirm</a>
		<?php } ?>
	</td>
	

</tr>
<?php $i++; } if(empty($user_orders)){?>
<tr><td colspan="100%">no orders to show</td></tr>
<?php }?>
</tbody>
</table>

</div>

<div id="confirm_ordrstatus" title="Confirm Order Status">
<form  method="post" form-validate="parsley"  id="confirm_order">
<input type="hidden" name="confrmd_transid" id="confrmd_transid" value="">
<table cellspacing="5">
<tr>
	<td><b>Order Status</b></td><td><b>:</b></td>
	<td>
		<select name="order_status" data-required="true">
			<option value=" ">Please Select</option>
			<option value="3">Cancel</option>
			<option value="1">Process</option>
		</select>
	</td>
</tr>
<tr>
	<td><b>Remarks</b></td><td><b>:</b></td><td><textarea name="ordr_confrm_remarks" id="ordr_confrm_remarks" data-required="true" style="width: 316px; height: 86px;"></textarea></td>
</tr>
</table>
</form>
</div>

</div>
<script>

$('.tgl_ord_prod a').click(function(e){
	e.preventDefault();
	if($(this).parent().next().is(':visible'))
	{
		$(this).text('Show Deals');
		$(this).parent().next().hide();
	}else
	{
		$(this).text('Hide Deals');
		$(this).parent().next().show();
	}
});
					
$('#exp_col_list').click(function(e){
	e.preventDefault();
	if($(this).data('collapse'))
	{
		$(this).data('collapse',false);
		$('.tgl_ord_prod a').text('Hide Deals');
		$('.tgl_ord_prod_content').show();
	}else
	{
		$(this).data('collapse',true);
		$('.tgl_ord_prod a').text('Show Deals');
		$('.tgl_ord_prod_content').hide();
	}
}).data('collapse',true);
$(function(){
	$('#exp_col_list').trigger('click');
	$('.datagridsort').tablesorter({headers:{0:{sorter:false}},sortList: [[4,0]]});
});

function confirm_trans(transid)
{
	$("#confirm_ordrstatus").data('transid',transid).dialog('open');
}

$("#confirm_ordrstatus").dialog({
modal:true,
autoOpen:false,
width:'515',
height:'280',
open:function()
{
	var dlg=$(this);
	$('select[name="order_status"]').val(" ");
	$('input[name="confrmd_transid"]').val(dlg.data('transid'));
	$("#ordr_confrm_remarks").val(" ");
},
buttons:{
'Confirm':function()
	{
	var update_form=$("#confirm_order",this);
	if(update_form.parsley('validate'))
	{
		$.post(site_url+'admin/jx_upd_ordrstatus',$('#confirm_order').serialize(),function(resp){
			
				$("#confirm_ordrstatus").dialog('close');
				window.location.reload();
			
		},'json');
	}
	},
'Cancel':function()
	{
		$(this).dialog('close');
	}
}
});
$(function(){
	$(".n_o_c,.pnh_o_c,.y_c,.n_c").change(function(){
		do_show_orders();
	}).attr("checked",true);
	$("#ds_range,#de_range").datepicker();
	
	
	$('.sus_fran').change(function(){
		$(this).attr('checked',false);
		alert("Unable to select the order,Franchise Suspended");
	});
});

function showrange()
{
	if($("#ds_range").val().length==0 ||$("#ds_range").val().length==0)
	{
		alert("Pls enter date range");
		return;
	}
	location='<?=site_url("admin/userend_orders/".(!$this->uri->segment(3)?"0":$this->uri->segment(3)))?>/'+$("#ds_range").val()+"/"+$("#de_range").val(); 
}

$("#o_confirm_status").change(function(){
	if($(this).val() == 0)
		$('#usr_ordr tbody tr').show();
	else if($(this).val() == 1)
	{
		$('#usr_ordr tbody tr').hide();
		$('#usr_ordr tbody tr.y_c').show();
	}
	else
	{
		$('#usr_ordr tbody tr').hide();
		$('#usr_ordr tbody tr.n_c').show();
	}
	
});
</script>