<?php
	$po_stats_flags = array(); 
	$po_stats_flags['-1'] = 'All';
	$po_stats_flags['0'] = 'Open';
	$po_stats_flags['1'] = 'Partial';
	$po_stats_flags['2'] = 'Close';
	$po_stats_flags['4'] = 'UnApproved';
	
	$cond = '';
	$cond1 = '';
	$pg=0;
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
	.color_red{background: #ff0000;cursor: pointer}
	.color_red{background: #FA8B9D}
	.color_grey{background: #f1f1f1}
	.color_orange{background: #FFA500}
	.color_green{background: #9EDB9E}
	.count{
		color:black;
		font-weight:bold;
		font-size:14px;
		margin:20px 0;
		 display: inline-block !important;
	}
	.align_wrap
	{
		margin:20px 0;
	}
	.row_select
	{
		background:#F5F592;
	}
	#sel_vendor
	{
		margin-right:10px;
	}
	.select_filter
	{
		 font-weight: bold;
   		 margin: 20px 0 0;
	}
	.bulk_cancel_po
	{
		font-size: 12px;
		font-weight: bold;
	}
</style>

<div class="page_wrap container">
	<div class="page_topbar">
		<div class="page_topbar_left fl_left" style="width: 30%">
			<h2 class="page_title">Manage Purchase Orders</h2>
		</div>
		<div class="page_topbar_right fl_right" align="right" style="width: 70%">
			<span>
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
					<option value="4" <?php echo ($status == "4"?'selected':'') ?> >UnApproved</option>
				</select>
			</span>
			<span>
				Show : 
				<select name="po_type" style="width: 100px;" onchange="rl_pgbyfilters()">
					<option value="-1" <?php echo ($type == ""?'selected':'') ?> >All</option>
					<option value="1" <?php echo ($type == "1"?'selected':'') ?> >DOA</option>
				</select>
			</span>
		</div>	
	</div>
	<br>
	<br>
	<br>
	<div class="page_topbar" align="left">
			<div class="stat_block color_green"><b>UnApproved</b> <span><?=$this->db->query("select count(1) as l from t_po_info where po_status=4 $cond ")->row()->l?></span> 
			<?php /*?><div style="font-size:9px;">(Rs <?=format_price($this->db->query("select sum(total_value) as l from t_po_info a where 1 $cond1 and po_status = 2 ")->row()->l,0)?>)</div><?php /*/?>
			</div>
			<div class="stat_block color_red"><b>Open</b> <span><?=$this->db->query("select count(1) as l from t_po_info where po_status=0 $cond ")->row()->l?></span>
			<?php /*?> 
			<div style="font-size:9px;">(Rs <?=format_price($this->db->query("select sum(total_value) as l from t_po_info a where 1 $cond1 and po_status = 0  ")->row()->l,0)?>)</div>
			<?php /*/?>
			</div>
		<div class="stat_block color_grey"><b>DOA</b> <span><?=$this->db->query("select count(1) as l from t_po_info where is_doa_po=1 $cond ")->row()->l?></span>
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
			</span> 
	</div>
		<button class="button button-flat-action button-tiny bulk_cancel_po fl_right">Cancel Po
				<?php /*?><div style="font-size:9px;">(Rs <?=format_price($this->db->query("select sum(total_value) as l from t_po_info a where 1 $cond1 ")->row()->l,0)?>)</div><?php /*/?>
			</span> 
		</button>
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
	
	<div id="bulk_cancel_po_dlg" title="Bulk Cancel POs">
		<div class="select_filter ">
		Select Date : <select name="sel_date">
							<option value="-1">Choose</option>
							<option value="3">Till 3 days before</option>
							<option value="5">Till 5 days before</option>
					  </select>
		</div>			  
		<div class="bulk_po_det">
			<span class="count align_wrap"></span>
			<span class="pagination_block"></span>
			<span class="fl_right align_wrap" id="status"  style="display:none">
				Status : <select name="status" id="sel_status">
							<option value="-1">Choose</option>
							<option value="0">Open</option>
							<option value="1">Partial</option>
						 </select>
			</span>
			
			<?php $vendors=$this->db->query("select vendor_id,vendor_name  from m_vendor_info order by vendor_name asc")->result_array(); ?>
			<span class="fl_right align_wrap" id="vendor" style="display:none">
				Vendor : <select name="vendor" id="sel_vendor" >
					<option value="-1">Choose</option>
					<?php 
						foreach($vendors as $v)					
						{
					?>		
							<option value="<?=$v['vendor_id']?>"><?=$v['vendor_name']?></option>
					<?php		
						}
					?>
							
						 </select>
			</span>	
			<div class="po_cont"></div>
		</div>			  
	</div>	
	
	<div id="po_cancel_remarks" title="Remarks">
		<b style="vertical-align: top">Remarks : </b><textarea name="remarks" class="cancel_remarks"></textarea>
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
					}
					if($p['is_doa_po']==1)
					{
						echo '<span style="color:red;margin-left:15px;">DOA PO</span>';
					}
					?></td>
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
					<?php }?>&nbsp;<a onclick="print_po(<?=$p['po_id']?>,'acct')" >Print po</a>
					
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
	var type = $('select[name="po_type"]').val();
	var ven_id = $('select[name="ven_id"]').val();
	
	var ds = $('#ds_range').val();
	var de = $('#de_range').val();
		
	
		ds = ds?ds:'0';
		de = de?de:'0';
		
		
		ven_id = ven_id?ven_id:0;
		stat = stat?stat:'';
	
		location=site_url+'/admin/purchaseorders/'+ds+"/"+de+'/'+ven_id+'/'+stat+'/'+type;
	
}
$(function(){
	$("#ds_range,#de_range").datepicker();
	 $('.datagrid').jq_fix_header_onscroll();	
	
}); 

$('.bulk_cancel_po').click(function(){
	$('#bulk_cancel_po_dlg').dialog('open');
	
	if($('select[name="sel_date"]').val() != -1)
		load_pos($('select[name="sel_date"]').val(),$('select[name="vendor"]').val(),$('select[name="status"]').val());
});

function print_po(poid,type)
{
	var print_url = site_url+'/admin/print_po/'+poid+'/acct';
		window.open(print_url);
}

//Dialog to proceed to place PO for insufficient stock products
$('#bulk_cancel_po_dlg').dialog({
		modal:true,
		autoOpen:false,
		width:750,
		height:650,
		autoResize:true,
		open:function(){
		//$('.ui-dialog-buttonpane').find('button:contains("Update")').addClass('placeorder_btn');
		//$('.ui-dialog-title').html('PO List');
		//load_pos($('select[name="vendor"]').val(),$('select[name="status"]').val());
		dlg = $(this);
	},
	buttons:{
		'Cancel':function(){
			$('#bulk_cancel_po_dlg').dialog('close');
		},
		'Close PO':function(){
			var po_product_arr=[];
			$('.sel_po').each(function(){
				var trEle=$(this).parents('tr:first');
				var chk=$(this).attr('checked');
				if(chk == 'checked')
				{
					var poid=$(this).attr('poid');
					
					if($.inArray(poid, po_product_arr) === -1) 
					 	po_product_arr.push(poid);
				}
			});
			$('#po_cancel_remarks').data('po_product_arr',po_product_arr).dialog('open');
			//$('#bulk_cancel_po_dlg').dialog('close');
		}
	}
}); 

//Dialog to proceed to place PO for insufficient stock products
$('#po_cancel_remarks').dialog({
		modal:true,
		autoOpen:false,
		width:300,
		height:200,
		autoResize:true,
		open:function(){
		//$('.ui-dialog-buttonpane').find('button:contains("Update")').addClass('placeorder_btn');
		//$('.ui-dialog-title').html('Remarks');
		//load_pos($('select[name="vendor"]').val(),$('select[name="status"]').val());
		dlg = $(this);
	},
	buttons:{
		'Cancel':function(){
			$('#bulk_cancel_po_dlg').dialog('close');
		},
		'Submit':function(){
			dlg = $(this);
			var po_arr=$(this).data('po_product_arr');
			var remarks=$('.cancel_remarks').val();
			$('#po_cancel_remarks').dialog('close');
			$.post(site_url+'/admin/jx_close_oldpos_bydate',{poids:po_arr,remarks:remarks},function(resp){
				if(resp.status != 'error')
				{
					alert("POs Closed Successfully");
					$('.bulk_po_det .pagination_block').html('<button type="button" class="button button-flat-action button-rounded button-primary button-tiny po_next_btn" start="0">Next</button>');
					load_pos($('select[name="sel_date"]').val(),$('select[name="vendor"]').val(),$('select[name="status"]').val());
				}
			});	
			//$('#bulk_cancel_po_dlg').dialog('close');
		}
	}
}); 

$('select[name="sel_date"]').live('change',function(){
	var d=$(this).val();
	if(d!=0)
		load_pos(d,$('select[name="vendor"]').val(),$('select[name="status"]').val());
});

function load_pos(d,ven,status)
{
	$('.bulk_po_det .po_cont').html('<div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
	var count=0;
	var start=$('.po_next_btn').attr('start');
	if(!start)
		start=0;	
	$('.bulk_po_det .count').hide();
	$.post(site_url+'/admin/jx_load_pos_bydate',{d:d,venid:ven,status:status,pg:start},function(resp){
		$('#vendor').show();
		$('#status').show();
		var po_html='';
		
		if(resp.status == 'error')
		{
			$('.bulk_po_det .count').html("");
			$('.bulk_po_det .po_cont').html("<div class='page_alert_wrap' style='margin-top:50px;color:red'>No POs Found</div>");
			$('.bulk_po_det .pagination_block').html("");
			return false;
		}
		else if (resp.status == 'success') 
		{
			var k=parseInt(resp.start)+1;
			$('.bulk_po_det .count').show();
			po_html+='<table class="datagrid" width="100%"><thead><th>Sl.No</th><th>PO</th><th>Vendor</th><th>Created On</th><th>Current Status</th><th>Action <span>All <input type="checkbox" class="all" ></span></th></thead><tbody>';
			$.each(resp.pos,function(i,p){
				++count;
				po_html+='<tr class="po_filter" vid="'+p.vendor_id+'" status="'+p.po_status+'">';
				po_html+='<td>'+(k++)+'</td>';
				po_html+='<td><a href="'+site_url+'/admin/viewpo/'+p.po_id+'" target="_blank">'+p.po_id+'</a></td>';
				po_html+='<td><a href="'+site_url+'/admin/viewpo/'+p.vendor_id+'" target="_blank">'+p.vendor_name+'</a></td>';
				po_html+='<td>'+p.created_on+'</td>';
				if(p.po_status == 0)
					p.po_status='Open';
				else if(p.po_status == 1)
					p.po_status='Partial';
						
				po_html+='<td>'+p.po_status+'</td>';
				po_html+='<td><input type="checkbox" class="sel_po" poid="'+p.po_id+'"></td>';
				po_html+='</tr>';
			});
			po_html+='</tbody></table>';
			$('.bulk_po_det .count').html("Showing "+(parseInt(resp.start)+parseInt(count))+"/"+resp.total_pos+" pos");
			resp.start=parseInt(start)+parseInt(count);
			
			if(resp.start == resp.total_pos )
			{
				$('.bulk_po_det .pagination_block').html('<button type="button" class="button button-flat-action button-rounded button-primary  button-tiny po_next_btn" start="0">Back</button>');
			}else
			{
				$('.bulk_po_det .pagination_block').html('<button type="button" class="button button-flat-action button-rounded button-primary button-tiny po_next_btn" start="'+resp.start+'">Next</button>');
			}
			if(ven!=-1 || status!=-1)
			{
				$('.bulk_po_det .pagination_block').html("");
			}
		}
		
		
		$('.bulk_po_det .po_cont').html(po_html);
	},'json');		
}

//Checkbox check and uncheck all action 
$('.all').live('click',function(){
	if($('.all').attr('checked'))
		$('.sel_po').attr('checked',true);
	else
		$('.sel_po').attr('checked',false);
	
	$('.sel_po').each(function(){
		var trEle=$(this).parents('tr:first');
		var chk=$(this).attr('checked');
		if(chk == 'checked')
			trEle.addClass('row_select');
		else
			trEle.removeClass('row_select');
	});
});

$('.po_next_btn').live('click',function(){
	var start=$(this).attr('start');
	load_pos($('select[name="sel_date"]').val(),$('select[name="vendor"]').val(),$('select[name="status"]').val());
});

//Checkbox action for place po option
$('.sel_po').live('click',function(){
	var trEle=$(this).parents('tr:first');
	var chk=$(this).attr('checked');
	if(chk == 'checked')
		trEle.addClass('row_select');
	else
		trEle.removeClass('row_select');	
});

$('#sel_vendor').live('change',function(){
	
	$('.bulk_po_det .pagination_block').html('<button type="button" class="button button-flat-action button-rounded button-primary button-tiny po_next_btn" start="0">Next</button>');
	
	if($('select[name="sel_date"]').val()!=-1)
		load_pos($('select[name="sel_date"]').val(),$('select[name="vendor"]').val(),$('select[name="status"]').val());
});
$('#sel_status').live('change',function(){
	$('.bulk_po_det .pagination_block').html('<button type="button" class="button button-flat-action button-rounded button-primary button-tiny po_next_btn" start="0">Next</button>');
	if($('select[name="sel_date"]').val()!=-1)
		load_pos($('select[name="sel_date"]').val(),$('select[name="vendor"]').val(),$('select[name="status"]').val());
});

$('.leftcont').hide();
</script>
<style>
.warn td{background: #FDD2D2 !important;}

</style>
<?php
