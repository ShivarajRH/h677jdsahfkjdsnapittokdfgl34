<style>
	.leftcont{display:none}
</style>

<div class="container page_wrap">
	<div class="page_topbar">
		<div class="page_topbar_left fl_left">
			<h2 style="margin:5px 0px;">Advanced Stock Correction</h2>
		</div>
		<div class="page_topbar_right fl_right" align="right" style="padding:5px;">
			<form id="searchprodbytag_frm" method="post">
				<input id="searchprodbytag" type="text" size="40" placeholder="Search by Product Name or ID Or Barcode " name="tag" value="" style="padding:5px;font-size: 13px;">
				<input type="submit" value="Submit" class="button button-action button-small" style="top:-3px;position: relative" >
			</form>
		</div>
	</div>
	<div class="page_content">
		<table id="prod_list" class="datagrid  datagridsort nofooter" width="100%" cellpadding="5" cellspacing="0">
			<thead>
				<th width="20">Slno</th>
				<th width="100">Brand Name</th>
				<th width="50">Product ID</th>
				<th width="300">Product Name</th>
				<th width="50">MRP</th>
				<th width="50">Sourceable</th>
				<th width="50">Stock</th>
				<th width="300">Stock Details</th>
			</thead>
			<tbody>
				
			</tbody>
		</table>
	</div>
</div>
  
<div style="display:none">
	<div id="prod_stock_corr_dlg" title="Product Stock Details" >
		<div style="padding:5px;">
			<div>
				<div>
					<div style="margin:0px;float: right"><b id="dlg_pidsrc_status"></b> &nbsp; <a href='javascript:void(0)"' prod_id="0" id="dlg_pidsrc_link" onclick="upd_prdsourceablestat(this)" nsrc="0">Change</a></div>
					Product ID : <b id="dlg_pid"></b> 
				</div>
				<div style="margin:5px 0px">Name : <b id="dlg_pname"></b></div>
				<div>
					DNC Status: <input type="checkbox" id="dnc_status_upd" name="is_active" value="1" >
				</div>
			</div>
			<div id="pstk_tbl"></div>
			<br>
			<br>
		</div>
	</div>	
</div>

<style>
  .ui-autocomplete-loading {
    background: white url('<?php echo base_url() ?>/images/ui-anim_basic_16x16.gif') right center no-repeat;
  }
  .tbl_condensed{border-collapse: collapse;color:#444;font-size: 12px;}
  .tbl_condensed th{padding:3px;background: #DFDFDF;color:#444}
  .tbl_condensed td{padding:3px;background: #f8f8f8;color:#444}
  .tbl_condensed input[type="text"]{font-size: 12px;}
  .cell_error{border:1px solid #cd0000 !important;color:#cd0000}
  #ttl_stk_qty {font-weight: bold;font-size: 13px;}
  .corr_done td{background: rgba(0, 118, 173, 0.2) !important}
  .readonly {background: #f7f7f7}
  .legend_warn{background: #cd0000;color:#FFF;padding:2px;border-radius: 2px;font-size: 10px;}
  .legend_success{background: green;color:#FFF;padding:2px;border-radius: 2px;font-size: 10px;}
  legend{float: left;}
  </style>
  <script>
  
  	$('#dnc_status_upd').live('change',function(){
  		var dnc_ele = $(this);
  		var is_active = $(this).attr('checked')?0:1;
  		var pid = $('#dlg_pid').text()*1;
  		
  			if(pid)
  			{
  				dnc_ele.attr('disabled',true);
  				$('#dlg_pname').html("Updating please wait...");
  				$.post(site_url+'/admin/jx_updproddncstatus/'+pid+'/'+is_active,{},function(resp){
		  			$('#dlg_pname').html(resp.pname);
		  			dnc_ele.attr('disabled',false);
		  		},'json');	
  			}
  			
  	});
  	
 	$('#searchprodbytag_frm').submit(function(){
 		
 		$('#searchprodbytag').val($.trim($('#searchprodbytag').val()));
 		
 		$('#prod_list tbody').html('<tr><td colspan="12" align="center">Loading....</td></tr>');
 		$.post(site_url+'/admin/jx_suggestprodsbytag','tag='+$('#searchprodbytag').val(),function(resp){
 			if(resp.status == 'error')
 			{
 				$('#prod_list tbody').html('<tr><td colspan="12" align="center">NO Products found</td></tr>');
 			}else
 			{
 				var prodHTML = '';
 				$.each(resp.prod_list,function(a,b){
 					prodHTML += '<tr id="prod_'+b.product_id+'" class="'+((b.corr_status*1)?'corr_done':'')+'">';
 					prodHTML += '	<td>'+(a*1+1)+'</td>';
 					prodHTML += '	<td>'+(b.brand_name)+'</td>';
 					prodHTML += '	<td>'+(b.product_id)+'</td>';
 					prodHTML += '	<td><a target="_blank" href="'+(site_url+'/admin/product/'+b.product_id)+'">'+(b.product_name)+'</a></td>';
 					prodHTML += '	<td>'+(b.mrp)+'</td>';
 					prodHTML += '	<td>'+((b.is_sourceable*1)?'<b style="color:green">Sourceable</b>':'<b style="color:#cd0000">Not Sourceable</b>')+'</td>';
 					prodHTML += '	<td>'+(b.stock)+'</td>';
 					prodHTML += '	<td><a class="link" href="javascript:load_product_stockdet('+b.product_id+') ">'+(b.corr_status*1?'Stock Corrected':'Correct Stock')+'</a></td>';
 					prodHTML += '</tr>';
 				});
 				
 				$('#prod_list tbody').html(prodHTML);
 				
 				$(".datagridsort").trigger("update"); 
 			}
 			
 		},'json');
 		return false;	
 	});
 	
 	$('input[name="p_qty[]"]').live('keyup',function(){
 		var qty = 0;
 		$('input[name="p_qty[]"]').each(function(){
 			
 			$(this).removeClass('cell_error');
 			
 			if(isNaN($(this).val()*1))
 				$(this).addClass('cell_error');
 			else if($(this).val() < 0)
 					$(this).addClass('cell_error');
 			
 			qty += $(this).val()*1;
 		});
 		$('#ttl_stk_qty').val(qty);
 	});
 	
 	$('.upd_pstockdet').live('keyup',function(){
 		$(".ui-dialog-buttonpane button:contains('Update')").button("enable");
 	});
 	
 	function add_newstkentry_func(ele)
 	{
 		var corr_tbl = '';
			corr_tbl += '	<td><input type="hidden" size="4" name="p_stkid[]" value="0" ><input type="text" class="upd_pstockdet" style="width:95%" name="p_bc[]" value="" ></td>';
			corr_tbl += '	<td><input type="text"  style="width:100px" name="p_mrp[]"  value="" ></td>';
			corr_tbl += '	<td><input type="text"  style="width:95%" name="p_loc[]" value="0" ></td>';
			corr_tbl += '	<td><input type="text" style="width:50px" class="upd_pstockdet" name="p_qty[]"  value="" ></td>';
			corr_tbl += '	<td><input type="button" onclick="remove_newpstockdet(this)" value="X" ></td>';
			corr_tbl += '';
 			
 		$('#pstk_frm #tr_newstkentry').html(corr_tbl);
 		$("#add_newstkentry").attr('disabled',true);
 	}
 	
 	function remove_newpstockdet(ele)
 	{
 		$(ele).parents('tr:first').html('');
 		$("#add_newstkentry").attr('disabled',false);
 	}
 	
 	$('#prod_stock_corr_dlg').dialog({
 										autoOpen:false,
 										width:500,
 										modal:true,
 										height:'auto',
 										open:function(){
											var pid = $(this).data('pid');
												$.post(site_url+"/admin/jx_loadprodstockdet/"+pid,{},function(resp){
													if(resp.status == 'success')
													{
														var corr_tbl = '<form id="pstk_frm"><input type="hidden" value="'+pid+'" name="pid" > <div align="right" ><input type="button" id="add_newstkentry" value="ADD MRP" onclick="add_newstkentry_func(this)" ></div> <table border="0" width="100%" cellpadding=5 cellspacing=0 class="tbl_condensed">';
															corr_tbl += '<thead><th width="100" >Barcode</th><th width="50" >MRP</th><th  width="30" >Location</th><th  width="20" >Stock</th><th width="10">&nbsp;</th></thead>';
															corr_tbl += '<tbody>';
															var stk_ttl = 0; 
															$.each(resp.pstk_list,function(a,b){
																corr_tbl += '<tr>';
																corr_tbl += '	<td><input type="hidden" size="4" name="p_stkid[]" value="'+b.stock_id+'" ><input type="text" '+(b.product_barcode.length?' class="readonly" readonly ':' class="upd_pstockdet" ')+'  style="width:95%" name="p_bc[]" value="'+b.product_barcode+'" ></td>';
																corr_tbl += '	<td><input type="text" readonly style="width:100px" name="p_mrp[]" class="readonly" value="'+b.mrp+'" ></td>';
																corr_tbl += '	<td><input type="text" readonly style="width:95%" name="p_loc[]" class="readonly" value="'+b.location+'" ></td>';
																corr_tbl += '	<td><input type="text" style="width:50px" class="upd_pstockdet" name="p_qty[]"  value="'+b.qty+'" ></td>';
																corr_tbl += '	<td>&nbsp;</td>';
																corr_tbl += '</tr>';
																stk_ttl += b.qty*1;
															});
															corr_tbl += '<tr id="tr_newstkentry"><td colspan="5"></td></tr>';
															corr_tbl += '<tr><td colspan="3" align="right">Total Stock</td><td><input readonly id="ttl_stk_qty" type="text"  style="width:50px" value="'+stk_ttl+'"></td><tr>';
															corr_tbl += '</tbody>';
															corr_tbl += '</table></form>';
														$('#pstk_tbl').html(corr_tbl);
														$('#dlg_pid').html(resp.pid);
 														$('#dlg_pname').html(resp.pname);
 														
 														$('#dnc_status_upd').attr('checked',((resp.is_active*1)?false:true));
 														
 														$('#dlg_pidsrc_link').attr('prod_id',resp.pid);
 														$('#dlg_pidsrc_link').attr('nsrc',resp.is_sourceable*1);
 														
 														$('#dlg_pidsrc_status').html('<legend class="'+(resp.is_sourceable*1?'legend_success':'legend_warn')+'">'+(resp.is_sourceable*1?'Sourceable':'Not Sourceable')+'</legend>');
 														
													}
												},'json');
												
 										},
 										buttons:{
 											'Update': function(btn)
 											{
 												
 												$('#pstk_frm input[name="p_qty[]"]').each(function(){
 													
 													$(this).removeClass('cell_error');
 													
 													var p_qtyval = $(this).val()*1;
 														if(isNaN(p_qtyval))	
 															$(this).addClass('cell_error');
 														else if(p_qtyval < 0)
 															$(this).addClass('cell_error');
 													
 												});
 												
 												if(!$('.cell_error').length)
 												{
 													$(".ui-dialog-buttonpane button:contains('Update')").attr("disabled", true);
 													dlg = $(this);
	 												$.post(site_url+'/admin/jx_updprodstkdet',$('#pstk_frm').serialize(),function(resp){
	 													$(".ui-dialog-buttonpane button:contains('Update')").attr("disabled", false);
	 													
	 													if(resp.status !='success')
	 													{
	 														alert(resp.message);
	 													}
	 													
	 													var worked_pid = $('#pstk_frm input[name="pid"]').val();
	 														
	 															reload_productbyid(worked_pid,function(){
	 																dlg.dialog('close');
	 															});
	 															
	 												},'json');	
 												}else
 												{
 													alert("Invalid inputs entered");
 												}
 												
 											}
 										},
 										close : function()
 										{
 											$('#dlg_pid').text('');
 											$('#dlg_pname').text('');
 											$('#pstk_tbl').html('');
 										}
 								});
 								
 								
 	function reload_productbyid(pid,cb)
 	{
 		$.post(site_url+'/admin/jx_suggestprodsbytag','pid='+pid+'&tag=',function(resp){
 			if(resp.status == 'success')
 			{
 				var prodHTML = '';
 				$.each(resp.prod_list,function(a,b){
 					//prodHTML += '<tr id="prod_'+b.product_id+'" class="'+((b.corr_status*1)?'corr_done':'')+'">';
 					prodHTML += '	<td>'+$('#prod_'+pid+' td:first').text()+'</td>';
 					prodHTML += '	<td>'+(b.brand_name)+'</td>';
 					prodHTML += '	<td>'+(b.product_id)+'</td>';
 					prodHTML += '	<td><a target="_blank" href="'+(site_url+'/admin/product/'+b.product_id)+'">'+(b.product_name)+'</a></td>';
 					prodHTML += '	<td>'+(b.mrp)+'</td>';
 					prodHTML += '	<td>'+((b.is_sourceable*1)?'<b style="color:green">Sourceable</b>':'<b style="color:#cd0000">Not Sourceable</b>')+'</td>';
 					prodHTML += '	<td>'+(b.stock)+'</td>';
 					prodHTML += '	<td><a class="link" href="javascript:load_product_stockdet('+b.product_id+') ">'+(b.corr_status*1?'Stock Corrected':'Correct Stock')+'</a></td>';
 					//prodHTML += '</tr>';													 					
 				});
 				
 				$('#prod_'+pid).html(prodHTML);
 				$('#prod_'+pid).addClass('corr_done');
 			}
 			
 		},'json');
													 		
 		return cb();
 	}							
 	
 	function load_product_stockdet(pid)
 	{
 		$('#prod_stock_corr_dlg').data('pid',pid).dialog('open');
 	}
 	
 	function upd_prdsourceablestat(ele)
	{
		nsrc = $(ele).attr('nsrc');
		prod_id = $(ele).attr('prod_id');
	
		if(confirm("Are you sure want to mark this product "+((nsrc*1==1)?'Not':'')+' Sourceable ?'))
		{
	
			$.post(site_url+'/admin/jx_upd_prodsrcstatus',{pid:prod_id,stat:nsrc},function(resp){
				
					$('#dlg_pidsrc_link').attr('nsrc',!(resp.src*1));
					if(!(resp.src*1))
					{
						$('#dlg_pidsrc_status').html('<legend class="legend_warn">Not Sourceable</legend>');
						
					}else
					{
						$('#dlg_pidsrc_status').html('<legend class="legend_success">Sourceable</legend>');
					}
					reload_productbyid(prod_id,function(){});
				
			},'json');
				
		}
	}

 	
 	$('.datagridsort').tablesorter();
  </script>
  
  