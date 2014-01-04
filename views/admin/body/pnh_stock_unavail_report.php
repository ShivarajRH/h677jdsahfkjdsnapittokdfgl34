<style>.leftcont{display: none}</style>
<div class="container">
	<h2>PNH Stock Unavailable report </h2>
	<div style="clear:both">
		<table width="100%" cellpadding="5" cellspacing="0">
			<tr>
				<td width="260">
					<form action="<?php echo site_url('admin/pnh_stock_unavail_report');?>" id="gen_pnh_unavail_report_frm" method="post">
						<table cellpadding="5">
							<tr>
								<td colspan="2">
									<b>Orders</b> : <br />
									<select name="date_type" class="chzn-select" data-placeholder="Choose" style="width:200px;"  >
										<option value="0">Till Date</option>
										<option value="1">Date Range</option>
									</select>
								</td>
							</tr>
							<tr id="ordersby_daterange" style="display: none">
								<td colspan="2">
									<b>Order Date Range</b> : <br />
									<input type="text" id="from_date" size="10" name="from" value="<?php echo date('Y-m-01')?>" />
									<input type="text" id="to_date" size="10" name="to" value="<?php echo date('Y-m-d')?>" />
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<b>Territory</b> :
									<br />
									<select class="chzn-select" data-placeholder="Choose"  name="tids[]" style="width:250px;" >
										 
									</select>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<b>menu</b> :
									<br />
									<select class="chzn-select" data-placeholder="Choose"  name="mids[]" style="width:250px;" >
										 
									</select>
								</td>
							</tr>
							<tr>
								<td colspan="2" align="left">
									<input type="submit" value="Submit" />
								</td>
							</tr>
						</table>
					</form>
				</td>
				<td valign="top">
					<div id="pnh_unavail_prod_list">
						<div class="order_summ">
							<span>Total Products: <b>0</b></span>
						</div>
						<table class="datagrid" width="100%" cellpadding="0" cellspacing="0">
							<thead>
								<th width="20"><b>#</b></th>
								<th width="100"><b>Brand</b></th>
								<th ><b>Product</b></th>
								<th width="20"><b>Order</b></th>
								<th width="20"><b>Avail</b></th>
								<th width="20"><b>Required</b></th>
							</thead>
							<tbody>
								
							</tbody>
						</table>
						<div class="pagination"></div>
					</div>
					
				</td>
			</tr>
		</table>
		
		
	</div>
</div>	

<style>
	.pagination{
		display: block;
		margin:3px;
		text-align: left;
	}
	.pagination a{
		display: inline-block;
		padding:4px;
		background: #fcfcfc;
		color: #555;
	}
	.pagination strong{
		display: inline-block;
		padding:4px;
		background: #555;
		color: #FFF;
	}
	.datagrid th{border:0px;}
	.smalldatagrid {border-collapse: collapse}
	.smalldatagrid th{background: #fafafa !important;border:0px;text-align: left;}
	.prod_ord_det{float: right;font-weight: bold;font-size: 16px;color:#F1F1F1;background: #999;padding:2px 4px;display: inline-block;}
	.tbl_subgrid_content{display: none}
	.subdatagrid{background: #FFFFF0 !important;}
	.subdatagrid td{padding:5px;font-size: 12px !important;}
	.datagrid td{border:0px;}
	.datagrid td{border-bottom:1px dotted #ccc;} 
</style>

<script type="text/javascript">
function load_all_franchises(){
	$('select[name="tids[]"] option').each(function(){
		$(this).attr('selected','selected');
	});
	$('select[name="tids[]"]').trigger("liszt:updated");
}
var clear_terrlist = 1;
function get_pnh_unavailordersumm(stat){
	if(clear_terrlist)
		$('select[name="tids[]"] option').remove().trigger("liszt:updated");
	
	
	$('select[name="mids[]"] option').remove().trigger("liszt:updated");
	
	$('.order_summ b').html("");
	
	var param = $('#gen_pnh_unavail_report_frm').serialize();
		$.post(site_url+'/admin/jx_pnh_stock_unavail_terr_menu',param,function(resp){
			if(resp.status == 'error'){
				alert(resp.message);
			}else{
				
				var total_qty = 0;
				var terr_html_list = '';
					if(clear_terrlist)
					{
						$.each(resp.terr,function(terr_id,terr_det){
							total_qty+= terr_det[1]*1;
							terr_html_list += '<option value="'+terr_id+'">'+terr_det[0]+' </option>';
						});
						terr_html_list = '<option value="0">All Territories </option>'+terr_html_list;
						$('select[name="tids[]"]').append(terr_html_list).trigger("liszt:updated");
						clear_terrlist = 0;	
					}
					total_qty = 0;
				var menu_html_list = '';
					$.each(resp.menu,function(menu_id,menu_det){
						total_qty+= menu_det[1]*1;
						menu_html_list += '<option value="'+menu_id+'">'+menu_det[0]+' </option>';
					});
					menu_html_list = '<option value="0">All Menu </option>'+menu_html_list;
					$('select[name="mids[]"]').append(menu_html_list).trigger("liszt:updated");
					
					reset_form_action();	
			}
		},'json');
	
}

function reset_form_action()
{
	$('#gen_pnh_unavail_report_frm').attr('action',site_url+'/admin/pnh_stock_unavail_report');
	hndl_pnh_unvail_prod_report_frm();
}

function fmt_date_slash(dObj){
	var d = (dObj.getDate())>9?(dObj.getDate()):'0'+(dObj.getDate());
	var m = (dObj.getMonth()+1)>9?(dObj.getMonth()+1):'0'+(dObj.getMonth()+1);
	
	//return dObj.getFullYear()+'-'+m+'-'+d;
	return d+'/'+m+'/'+ dObj.getFullYear();
	
}



function get_franchise_level(d)
{
	if(d >= 0 && d <=30)
		return '<span style="font-size: 9px;background-color:#cd0000;color:#fff;padding:2px 3px;border-radius:3px;">Newbie</span>';
	else if(d > 30 && d <=60)
		return '<span style="font-size: 9px;background-color:orange;color:#fff;padding:2px 3px;border-radius:3px;">MidLevel</span>';
	else if(d > 60 )
		return '<span style="font-size: 9px;background-color:green;color:#fff;padding:2px 3px;border-radius:3px;">Experienced</span>';
}

function hndl_pnh_unvail_prod_report_frm()
{
	
	$('.order_summ b').html('');
	$('#pnh_unavail_prod_list table tbody').css('opacity',0.5);
	var param = $('#gen_pnh_unavail_report_frm').serialize();
		$.post($('#gen_pnh_unavail_report_frm').attr('action'),'a=b&'+param,function(resp){
			
			if(resp.status == 'error'){
				alert(resp.message);
				$('#pnh_unavail_prod_list table').hide();
				$('#pnh_unavail_prod_list .pagination').hide();
				$('#pnh_unavail_prod_list table tbody').css('opacity',1);
			}else{
				$('#pnh_unavail_prod_list table').show();
				$('#pnh_unavail_prod_list .pagination').show();
				
				$('.order_summ b').html(resp.total);
				
				var tbl_html = '';
				
					$.each(resp.data,function(i,d){
						tbl_html += '<tr >';
						tbl_html += '<td>'+((resp.pg*1)+i+1)+'</td>';
						tbl_html += '<td>'+d.brand_name+'</td>';
						
						//d.fran_order_det = 'A:2013-04-16:1:2452345235,B:2013-04-16:1:2452345235,C:2013-04-16:1:2452345235';
						
						var fr_order_list = d.fran_order_det.split(',');
						var pqty = 0;
						var fran_order_det_html = '<table width="100%" class="subdatagrid" cellpadding=0 cellspacing=0>';
						var fr_order_list_sorted = new Array();
							
							
							$.each(fr_order_list,function(a,b){
								var c = b.split(':');
								fran_order_det_html += '<tr>';
								fran_order_det_html += '	<td style="padding-left:0px;width:80px">'+get_franchise_level(c[6])+'</td>';
								fran_order_det_html += '	<td style="padding-left:0px;">'+c[4]+'</td>';
								fran_order_det_html += '	<td width="100">'+fmt_date_slash(new Date(c[1]*1000))+'</td>';
								fran_order_det_html += '	<td width="30">'+c[5]+'</td>';
								fran_order_det_html += '	<td width="100"><a href="'+site_url+'/admin/trans/'+c[0]+'">'+c[0]+'</a></td>';
								fran_order_det_html += '</tr>';
								pqty += c[5]*1; 
							});
							
							fran_order_det_html += '</table>';
							
						
						tbl_html += '<td class="row_click" pid="'+d.product_id+'"><a target="_blank" href="'+site_url+'/admin/product/'+d.product_id+'"><b>'+d.product_name+'</b></a> <span><a href="javascript:void(0)" id="prod_ord_det_'+d.product_id+'" class="prod_ord_det plus">&plus;</a></span><div class="tbl_subgrid_content">'+fran_order_det_html+'</div></td>';
						tbl_html += '<td align="right"><b>'+pqty+'<b></td>';
						tbl_html += '<td align="right"><b>'+d.avail_qty+'<b></td>';
						tbl_html += '<td align="right"><b>'+(pqty-d.avail_qty)+'<b></td>';
						tbl_html += '</tr>';
					});
					$('#pnh_unavail_prod_list table tbody').html(tbl_html);
					$('#pnh_unavail_prod_list .pagination').html(resp.pagination);
					$('#pnh_unavail_prod_list table tbody').css('opacity',1);
			}
		},'json');
	return false;
}

$('.prod_ord_det').live('click',function(e){
	e.preventDefault();
	if($(this).hasClass('plus'))
	{
		$(this).addClass('minus').removeClass('plus').html('&minus;');
		$(this).parent().parent().find('.tbl_subgrid_content').show();
	}else
	{
		$(this).removeClass('minus').addClass('plus').html('&plus;');
		$(this).parent().parent().find('.tbl_subgrid_content').hide();
	}
});

 
$('#gen_pnh_unavail_report_frm').submit(function(){
	reset_form_action()
	return false;
});
$('#pnh_unavail_prod_list .pagination a').live('click',function(e){
	e.preventDefault();
	$('#gen_pnh_unavail_report_frm').attr('action',$(this).attr('href'));
	hndl_pnh_unvail_prod_report_frm();
});

$('.row_click').live('click',function(){
	//var sel_pid = $(this).attr('pid');
		//$('#prod_ord_det_'+sel_pid).trigger('click'); 
});


$(function(){
	$('select[name="date_type"]').chosen();
	$('select[name="tids[]"]').chosen();
	$('select[name="mids[]"]').chosen();
	$('select[name="date_type"]').change(function(){
		if($(this).val() == 1)
		{
			$('#ordersby_daterange').show();
		}else
		{
			$('#ordersby_daterange').hide();
		}
		clear_terrlist = 1;
		get_pnh_unavailordersumm();
		
	});
	
	$('#from_date,#to_date').change(function(){
		clear_terrlist = 1;
		get_pnh_unavailordersumm();
	});
	clear_terrlist = 1;
	get_pnh_unavailordersumm();
	
	
	$('select[name="tids[]"]').change(function(){
		clear_terrlist = 0;
		get_pnh_unavailordersumm();
	});
	
	$('select[name="mids[]"]').change(function(){
		reset_form_action();
	});
		
	
	prepare_daterange('from_date','to_date');
	$('#gen_stat_frm').submit(function(){
		
		if(!$('select[name="tids[]"] option:selected').length){
			alert("Choose atleast one franchise from the list"); 		
			return false;		
		}

		if(!$('#from_date').val() || !$('#to_date').val()){
			alert("Please enter correct date range"); 		
			return false;		
		}		
		
		if(!confirm("Are you sure want to generate statement ")){
			return false;	
		}
	});
	
});

</script>