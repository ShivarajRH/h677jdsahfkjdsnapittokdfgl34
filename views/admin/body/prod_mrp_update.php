<div class="container">
	<h2>Product Update -- Price/Sourceability</h2>
		<div class="filters_block">
			<div class="filter">
				<select id="sel_menu_for" rel="<?php echo $menu_for?>">
					<option value="0" <?php echo !$menu_for?'selected':'' ?>>Snapittoday</option>
					<option value="1" <?php echo $menu_for?'selected':'' ?> >Storeking</option>
				</select>
			</div>
		</div>	
		<div class="filters_block">
			<div class="filter">
				Menu : <select id="sel_menu" rel="<?php echo $mid?>">
							<option value="">All</option>
						</select>
			</div>
		</div>	
		<div class="filters_block">
			<div class="filter">
				Brand : <select id="sel_brand" rel="<?php echo $bid?>">
							<option value="">All</option>
						</select>
			</div>
		</div>	
		<div class="filters_block">
			<div class="filter">
				Category : <select id="sel_category"  rel="<?php echo $cid?>">
								<option value="">All</option>
							</select>
			</div>
		</div>	
		<div class="filters_block">
			<div class="filter">
				Sourceable : <select id="sel_sourceable"  rel="<?php echo $src?>">
								<option value="-1">All</option>
								<option value="1" <?php if($this->uri->segment(7)=='1'){?>selected='selected' <?php } ?>>Yes</option>
								<option value="0" <?php if($this->uri->segment(7)=='0'){?>selected='selected' <?php } ?>>No</option>
							</select>
			</div>
		</div>
		<div class="filters_block">
			<div class="filter">
				<input type="button"  value="Submit" onclick="load_filtered_products()" > 
			</div>
		</div>			
		
		<?php if($prods) { ?>
			<div style="padding:20px 0px;">
				<form id="prodmrpnstat_frm" method="post">
					<?php if(isset($prods)){?>
						<div class="products_total">Total Products : <?=count($prods)?></div>
						<span class="fl_right" style="margin:10px 0px">
								<input type="submit" class="button button-flat-action button-rounded button-tiny" value="Update MRPs">
								<button type="button" class="button button-flat-action button-rounded button-tiny src_btn" value="1">Mark as Sourceable</button>
								<button type="button" class="button button-flat-action button-rounded button-tiny src_btn" value="0">Mark as Not Sourceable</button>
								<button type="button" class="button button-flat-action button-rounded button-tiny eol_btn">Mark as End of Life</button>
								Select All<input type="checkbox" class="chkall_sel" value="1">
							</span>
						<table class="datagrid datagridsort" width="100%">
							<theaD>
								<tr>
									<th width="20px">Sl.No</th>
									<th style="text-align:left !important">Product Name</th>
									<th width="44px" >Stock</th>
									<th width="120px">Pending Order Qty</th>
									<th width="150px" align="center">MRP</th>
									<th width="150px">New Mrp</th>
									<th width="150px">Sourceable</th>
									<th width="80px" align="center">
									</th>
								</tr>
							</theaD>
						<tbody>
					<?php foreach($prods as $k=>$p)
							{?>
								
								<tr class="<?php echo ($p['is_sourceable']*1)?'src':'nsrc' ?>" >
									<td align="center"><?=(++$k)?></td>
									<td  style="text-align:left !important"><a href="<?php echo site_url('admin/product/'.$p['product_id']) ?>" target="_blank"><?=$p['product_name']?></a>
										<span>
											<a class="prod_del_det plus" id="prod_del_det_<?php echo $p['product_id'];?>" product_id="<?php echo $p['product_id'];?>" href="javascript:void(0)">+</a>
										</span>
										<div class="deal_det_<?php echo $p['product_id'];?>">
										</div>	
										
									</td>
																			
									<?php $stk=$this->erpm->get_product_stock($p['product_id']); 
										  $pending_ord_qty=$this->erpm->_get_product_pending_orderqty($p['product_id']); 	
									?>
									<td align="center"><?=$stk['current_stock']?></td>
									<td align="center"> 
										<?php	
											if($pending_ord_qty>0)
											{
										?>
												<a href="javascript:void(0)" onclick="load_openpolist(<?php echo $p['product_id'];?>)" ><?=$pending_ord_qty?> </b></a>
										<?php		
											}else
											{
										?>		
												<?=$pending_ord_qty?> 
										<?php
											}	
										?>
										
									</td>
									<td align="right"><?=($p['mrp']*1)?></td>
									<td><input type="hidden" tabindex="<?php echo $k+1;?>" name="pid[]" value="<?=$p['product_id']?>"><input type="text" name="mrp[]" size=8 value=""></td>
									<td class="psrcstat"><span class="src_stat"><?php echo $p['is_sourceable']?'Yes':'No' ?></span> 
										<!--
										<a href="javascript:void(0)" prod_id="<?php echo $p['product_id'];?>" onclick="upd_prdsourceablestat(this)" nsrc="<?php echo $p['is_sourceable']*1;?>" >Change</a>
										--> 
									</td>
									<td>
										<input type="hidden" value="<?php echo $p['is_sourceable'];?>" id="prod_stat_<?php echo $p['product_id'];?>" class="prodnstat" name="prod[<?php echo $p['product_id'];?>]" >
										<input type="checkbox"  class="hndl_pstat sel" pid="<?php echo $p['product_id'];?>" value="<?php echo $p['product_id'];?>">
									</td>
								</tr>
							<?php }?>
						</tbody>
					</table>
						<div class="fl_right" style="margin-top:10px">
							<input type="submit" class="button button-flat-action button-rounded button-tiny" value="Update MRPs">
						</div>	
					<?php }?>		
				</form>
			</div>
		<?php } else{
						if($this->uri->segment(5))
							echo "<div style='margin-top:25px;color:red;width:100%;float:left;text-align:center'>No Data Found</div>"; 
						
					}?>	 
	</div>
	<div style="display:none">
		<!-- Modal for PO products selected display-->
		<div id="dlg_openpolist" title="Open PO List"></div>
	</div>
	<div id="change_status_dlg" title="Change Status">
		<div class="prd_dets"></div>
		<b style="vertical-align: top">Remarks :</b>  <textarea name="remarks" class="remarks" placeholder="Please enter minimum 20 characters"></textarea>
		
	</div>
<style>
	.products_total 
	{
	    display: inline-block;
	    font-weight: bold;
	    margin-bottom: 15px;
	    margin-top: 10px;
	    width: 600px;
  	}
	.src{	background:#afa; }
	.nsrc{ background:#faa; }
	#sl_products td {text-align: center;}
	.psrcstat a{font-size: 10px;color: blue;}
	select
	{
		width:150px;
	}
	.datagrid th,td
	{
		text-align:center !important;
	}
	.prod_del_det{float: right;font-weight: bold;font-size: 16px;color:#F1F1F1;background: #999;padding:2px 4px;display: inline-block;}
</style>


<script>
 $(".datagrid thead th:eq(7)").data("sorter", false);
$('.datagrid').tablesorter({sortList:[[4,0]]});

$('.src_btn').live('click',function(){
	var v=$(this).val();
	var count=0;
	$(".sel:checked").each(function(){
		count++;
	});
	
	if(count>0)
		endisable_sel(v);
	else
	{
		alert('Please Choose atleast one product');return false;
	}
			
});

$('.eol_btn').live('click',function(){
	var count=0;
	$(".sel:checked").each(function(){
		count++;
	});
	
	if(count>0)
		close_prd_sel();
	else
	{
		alert('Please Choose atleast one product');return false;
	}
			
});

//Function to publish and unpublish deals
function endisable_sel(act)
{
	var ids=[];
	var c=0;
	$(".sel:checked").each(function(){
		ids.push($(this).val());
	});
	ids=ids.join(",");
	if(act==1)
		act=0;
	else if(act==0)
		act=1;	
	
	$('#change_status_dlg').data("pro_data",{'pids': ids,'status': act}).dialog('open');
}

//Function to publish and unpublish deals
function close_prd_sel()
{
	var ids=[];
	var c=0;
	var act=2;
	$(".sel:checked").each(function(){
		ids.push($(this).val());
	});
	ids=ids.join(",");
	$('#change_status_dlg').data("pro_data",{'pids': ids,'status': act}).dialog('open');
}

$("#change_status_dlg" ).dialog({
		modal:true,
		autoOpen:false,
		width:'300',
		height:'200',
		autoResize:true,
		open:function(){
	},
	buttons: {
		"Proceed": function() {
			var dlgData = $("#change_status_dlg").data("pro_data");
			var pid=dlgData.pids;
			var pstatus=dlgData.status;
			var remarks=$('.remarks').val();
			
			if(!remarks || remarks.length < 20)
			{
				alert("Please enter minimum 20 characters");
				return false;
			}
			change_status(pid,pstatus,remarks);
			
	    	$(this).dialog('close');
	    },	
	    "Close": function() {
	    	$(this).dialog('close');
	   }
	} 
});

function change_status(pid,pstatus,remarks)
{
	menu_for = $('#sel_menu_for').val()*1;
	mid = $('#sel_menu').val()*1;
	bid = $('#sel_brand').val()*1;
	cid = $('#sel_category').val()*1;
	src = $('#sel_sourceable').val();
		
	$.post('<?=site_url('admin/jx_change_prd_status')?>',{pids:pid,status:pstatus,remarks:remarks},function(resp){
		
		if(resp.status=='error')
		{
			
		}else
		{
			alert("Product status successfully changed");
			location.href = site_url+'/admin/prod_mrp_update/'+menu_for+'/'+mid+'/'+bid+'/'+cid+'/'+src;
		}
	},'json');
}
$('.prod_del_det').click(function(e){
	e.preventDefault();
	var pid=$(this).attr('product_id');
	if($(this).hasClass('plus'))
	{
		$(this).addClass('minus').removeClass('plus').html('&minus;');
		$('.deal_det_'+pid).html('Loading....');
		$.post(site_url+'/admin/jx_deal_det_byprod',{product_id:pid},function(resp){
			if(resp.status=='error')
			{
				$('.deal_det_'+pid).html('No Deals Found');
				return false;
			}
			else
			{
				var html='';
				html+='<h4 style="margin:5px 0;">Deals</h4>';
				html+='<table class="datagrid" width="100%">';
				html+='	<thead>';
				html+='		<th>Sl.No</th>';
				html+='		<th>Deal Name</th>';
				html+='		<th>Deal Type</th>';
				html+='	</thead>';
				html+='	<tbody>	';
				
				$.each(resp.deal_det,function(i,d){
					var type='';
					html+='			<tr>';
					html+='				<td>'+(++i)+'</td>';
					html+='				<td><a target="_blank" href="'+site_url+'/admin/pnh_deal/'+d.itemid+'">'+d.dealname+'</a></td>';
					if(d.is_pnh==1)
						type='SK';
					else if(d.is_pnh==0)
						type='SIT';
					html+='				<td>'+type+'</td>';
					html+='			</tr>';
				});											
					
				html+='	</tbody>';
				html+='</table>';
				
				$('.deal_det_'+pid).html(html);
			}	
		},'json');
	}else
	{
		$(this).removeClass('minus').addClass('plus').html('&plus;');
		$('.deal_det_'+pid).html('');
	}
});



$('.hndl_pstat').change(function(){
	var sel_pid = $(this).attr('pid');
		if($(this).attr('checked'))
			$('#prod_stat_'+sel_pid).val(1);
		else
			$('#prod_stat_'+sel_pid).val(0);
});

$('.chkall_sel').change(function(){
	$('.hndl_pstat').attr('checked',$(this).attr('checked')?true:false).trigger('change');
});


function load_filtered_products()
{
	menu_for = $('#sel_menu_for').val()*1;
	mid = $('#sel_menu').val()*1;
	bid = $('#sel_brand').val()*1;
	cid = $('#sel_category').val()*1;
	src = $('#sel_sourceable').val();
	
	if(!cid && !bid)
		alert("Please select Brand or category");
	else
		location.href = site_url+'/admin/prod_mrp_update/'+menu_for+'/'+mid+'/'+bid+'/'+cid+'/'+src;
}
 
$(function(){


	$("#sel_brand").change(function(){
		var bid = $(this).val();
		var mid = $('#sel_menu').val();
			$("#sel_category").html('<option>loading...</option>');
			if(bid)
			{
				$.getJSON(site_url+'/admin/jx_getcatbybrand/'+bid+'/'+mid,'',function(resp){
					var catlist_html = '';//
					catlist_html+='<option value="0">All</option>';
						if(resp.cat_list.length)
						{
							$.each(resp.cat_list,function(a,b){
								catlist_html += '<option value="'+b.id+'" '+((b.id==$('#sel_category').attr('rel'))?'selected':'')+' >'+b.name+'</option>';
							});
						}
					$("#sel_category").html(catlist_html);	
				});
			}else
			{
				
			}
	});
	
	$('#sel_menu').change(function(){
		$('#sel_brand').val('<option value="">Loading...</option>');
		$('#sel_category').val('<option value="">Loading...</option>');
		
		$.post(site_url+'/admin/jx_getbrandsbymenu/'+$(this).val(),'',function(resp){
			var brand_list_html = '';//<option value="">ALL</option>
				$.each(resp.brand_list,function(a,b){
					brand_list_html+= '<option value="'+b.brandid+'"  '+((b.brandid==$('#sel_brand').attr('rel'))?'selected':'')+'>'+b.name+'</option>';
				});
				$('#sel_brand').html(brand_list_html).trigger('change');
				$('#sel_category').html('<option value="">ALL</option>');
		},'json');
		
	});
	
	


	$('#sel_menu_for').change(function(){
		$('#sel_menu').val('<option value="">ALL</option>');
		$('#sel_brand').val('<option value="">ALL</option>');
		$('#sel_category').val('<option value="">ALL</option>');
		var mid_uri='<?php echo $this->uri->segment('4'); ?>';
		
		$.post(site_url+'/admin/jx_getmenubytype/'+$(this).val(),'',function(resp){
			var menu_list_html = '';
				$.each(resp.menu_list,function(a,b){
					if(b.id==mid_uri)
					{
						menu_list_html+= '<option value="'+b.id+'" '+((b.id==$('#sel_menu').attr('rel'))?'selected':'')+'  selected>'+b.name+'</option>';
					}else
					{
						menu_list_html+= '<option value="'+b.id+'" '+((b.id==$('#sel_menu').attr('rel'))?'selected':'')+'  >'+b.name+'</option>';
					}
				});
				$('#sel_menu').html(menu_list_html).trigger('change');
				$('#sel_brand').html('<option value="">ALL</option>');
				$('#sel_category').html('<option value="">ALL</option>');
		},'json');
		
	}).trigger('change');
	
	
});

//Function call to display open PO details for a product
function load_openpolist(pid)
{
	$('#dlg_openpolist').data('pid',pid).dialog('open');
}

//Open PO product view block
$('#dlg_openpolist').dialog({'width':800,autoOpen:false,height:600,modal:true,open:function(){
	var pid = $(this).data('pid');
	$('#dlg_openpolist').html("<h3 align='center'>Loading...</h3>");
	$.post(site_url+'/admin/jx_getopenpolistbypid/'+pid,{},function(resp){
		var html = '<h3 class="pname">'+resp.product_name+'</h3>';
			html += '	<div class="pttl">Total open qty : <b>'+resp.ttl_open_qty+'</b></div>';
			html += '	<div id="openpolist_tbl">';
			html += '	<table width="100%" class="datagrid" cellpadding="5" cellspacing="0">';
			html += '		<thead>';
			html += '			<th><input type="checkbox" class="all_po_chk"></th><th>Slno</th><th>Remove product in PO</th><th>Vendor</th><th>PO Date</th><th>POID</th><th>Total Qty</th><th>Action</th>';
			html += '		</thead>';
			html += '		<tbody>';
			$.each(resp.vendor_po_list,function(a,b){
				html += '		<tr poid="'+b.po_id+'" productid="'+pid+'"><td><input type="checkbox" class="sl_sel_po" value="'+pid+'" po_id="'+b.po_id+'"></td><td>'+(a*1+1)+'</td><td><a href="javascript:void(0)" onclick="remove_prodfrmpo(this)" poid="'+b.po_id+'" pid="'+pid+'" style="font-size:11px;color:blue;" >Cancel product in PO</a></td><td><a target="_blank" href="'+site_url+'/admin/vendor/'+b.vendor_id+'">'+b.vendor_name+'</a></td><td>'+get_unixtimetodate(b.po_date)+'</td><td>'+b.po_id+'</td><td>'+b.qty+'</td><td><a class="inline_trig" style="color:blue;font-weight:bold" target="_blank" href="'+(site_url+'/admin/viewpo/'+b.po_id)+'" >View</a></td></tr>';	
			});
			html += '		</tbody>';
			html += '	</table>';
			html += '	</div>';
			$('#dlg_openpolist').html(html);
	},'json');
},

buttons:{
	'Cancel selected product in PO':function()
	{
		var dlg = $(this);
		var poids=[];
		$(".sl_sel_po:checked").each(function(){
			poids.push($(this).attr('po_id'));
			
		});
		poids=poids.join(",");
		var pid=$('.sl_sel_po').val();
		if($(".sl_sel_po:checked").length!=0){
			 $.post(site_url+'/admin/jx_update_poprod_status',{poid:poids,pid:pid},function(resp){
			if(resp.status)
			{
				$("#dlg_openpolist").dialog('close');
           		$("#dlg_openpolist").dialog('open');
          		$("#is_po_raised_"+pid).html('<a href="javascript:void(0)" onclick="load_openpolist('+pid+')" ><b>'+resp.ttl_open_qty+'</b></a>');
   			 
			} 
	                   
            },'json');
        }
	}
}

}).load(function() {
                $(this).dialog("option", "position", ['center', 'center'] );
            });

function get_unixtimetodate(utime)
{
	var date = new Date(utime * 1000);
	var y=date.getFullYear();
    var m=date.getMonth()+1;
    var d=date.getDate();
    var h=(date.getHours() > 9)?date.getHours()-12:date.getHours();
    var mi=date.getMinutes();
    var s=date.getSeconds();
    var datetime=d+'/'+m+'/'+y;
    return datetime;
} 
</script>
<?php
