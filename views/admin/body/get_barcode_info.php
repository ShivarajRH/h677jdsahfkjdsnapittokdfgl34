<style>
.prod_container, .po_det_prod_container
{
	margin:15px 0;
}
.hide
{
	display:none !important;
}
.barc
{
	margin-top:10px;
}
.barc span
{
	font-weight:bold;
	font-size: 13px;
}
.barc_det_inp_blk .alert_msg
{
    color: red;
    font-size: 13px;
    font-weight: bold;
    margin: 20px 0;
}
.po_barc_det_inp_blk .po_alert_msg
{
    color: red;
    font-size: 13px;
    font-weight: bold;
    margin: 20px 0;
}
.prod_filter_wrap td
{
	border-bottom: 1px solid #cacaca;
    padding: 9px 4px;
}
.sk_deal_blk_wrap th {
    background: none repeat scroll 0 0 #cccccc !important;
    font-size: 12px;
    padding: 7px;
}
.filters_wrap, .po_filters_wrap 
{
    display: inline-block;
    margin-top: -1px;
    text-align: left;
}
.filters_wrap div, .po_filters_wrap div
{
	display: inline-block;
    margin-left: 15px;
}
.search_img_wrap {
    background: none repeat scroll 0 0 #ffffff;
    border: 1px solid #cccccc;
    float: right;
}
.deal_prd_blk, .prd_barcode_srch {
    border-radius: 0 !important;
    float: right;
    font-size: 10px;
    height: 18px;
    margin-right: -1px;
    margin-top:0px;
    width: 265px;
}
.sk_deal_filter_blk_wrap
{
	text-align:right;
}
.barc_det_inp_blk .alert_msg
{
	color:red;
}
.page_alert_wrap
{
	background: none repeat scroll 0 0 #f1f1f1;
    color: red;
    font-size: 16px;
    font-weight: bold;
    padding: 50px 0;
    text-align: center;
}
#po_sel_brand,#sel_brand
{
	font-size: 13px;
}
.prd_selected
{
	font-size:11px !important;
}
.po_notify_msg, .notify_msg
{
	color: green;
    font-size: 13px;
    font-weight: bold;
    margin: 13px 0;
}
.product_remove
{
	color:red;
}
.barc
{
	border: 1px solid #cacaca;
    box-shadow: 2px 3px 5px #888888;
    display: inline-block;
    margin-right: 15px;
    padding: 15px;
    text-align: center;
    width: 362px;
}

</style>
<div class="container">
	<!-- Page Title -->
	<h2>Product Details by Barcode</h2>
	
	<div class="tab_view">
		<ul class="fran_tabs">
			<li><a href="#cmn_brcode">Barcode Details</a></li>
			<li><a href="#open_po_brcode">Open PO Barcode Details</a></li>
			<!--<li><a href="<?=site_url("admin/pnh_addfranchise/$fid")?>">SMS Log</a></li>-->
		</ul>
		<div id="cmn_brcode">
			<!-- Input for Barcode -->
			<div class="barc"><span>Please Enter Barcode : </span><input type="text" name="barcode" id="inp_barcode"></div>
			
			<!-- Choose Brand and Product Block to allot a barcode -->
			<div class="barc_det_inp_blk hide">
				<div class="alert_msg">Barcode Details not found..Please choose product to allot barcode</div>
					<div class="filters_wrap">
						<div>
							Brand : <select name="sel_brand" id="sel_brand">
								<?php foreach($brands as $b ){ ?>
									<option value="<?=$b['id'] ?>"><?=$b['name'] ?></option>						
								<?php }?>
							</select>
						</div>	
						<div>
							Product : <img class="search_img_wrap" src="<?php echo base_url().'images/search_icon.png'?>">
							<input type="text" name="srch_product" id="srch_product" class="deal_prd_blk inp" placeholder="Search by Product Name" >
							<style id="search_style"></style>
						</div>
					</div>
				</div>	
					
			<!-- Container to display product information  -->
			<div class="notify_msg"></div>
			<div class="prod_container"></div>
		</div>	
		<div id="open_po_brcode">
			<!-- Input for Barcode -->
			<div class="barc"><span>Please Enter Barcode for open PO product search : </span><input type="text" name="barcode" id="open_po_inp_barcode"></div>
			
			<!-- Choose Brand and Product Block to allot a barcode -->
			<div class="po_barc_det_inp_blk hide">
				<div class="po_alert_msg">Barcode Details not found in any of open PO's..Please choose product to allot barcode</div>
					<div class="po_filters_wrap">
						<div>
							Brand : <select name="sel_brand" id="po_sel_brand">
								<?php foreach($brands as $b ){ ?>
									<option value="<?=$b['id'] ?>"><?=$b['name'] ?></option>						
								<?php }?>
							</select>
						</div>
						<div class="sel_product hide">
							Product : <select name="sel_product" id="po_sel_prd" >
								
							</select>
						</div>
						<!--	
						<div>
							Product : <img class="search_img_wrap" src="<?php echo base_url().'images/search_icon.png'?>">
							<input type="text" name="srch_product" class="deal_prd_blk inp" placeholder="Search by Product Name" >
						</div> -->
					</div>
				</div>	
					
			<!-- Container to display product information  -->
			<div class="po_notify_msg"></div>
			<div class="po_det_prod_container"></div>
			<div id="upd_stk_prodbc_dlg" title="Update Product Stock Barcode">
				<form id="upd_stk_prodbc_frm" method="post">
					<table>
						<tr><td><b>Old Barcode</b></td><td><input id="upd_old_bc" class="inp" type="text" disabled="disabled"></td></tr>
						<tr><td><b>New Barcode</b></td><td><input id="upd_new_bc" class="inp" autocomplete="off" type="text" value=""><input type="submit" value="Submit" style="visibility: hidden;" ></td></tr>
					</table>
				</form>	
			</div>
		</div>
	</div>
</div>

<script>
var searchStyle = document.getElementById('search_style');
$('.tab_view').tabs();
var is_brand_change=0;

//Barcode Key up event for all products
$("#inp_barcode").live('keyup',function(e){
	var b=$(this).val();//Entered Barcode
	$('.notify_msg').hide();
	$('.po_notify_msg').hide();
	if(!b)
	{
		alert("Please enter Barcode");return false;
	}
	if(e.which==13)
	{
		$('.prod_container').html('<div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
		barcode_det(b,0);
	}
});	

//Barcode Key up event for open po products
$("#open_po_inp_barcode").live('keyup',function(e){
	var b=$(this).val();//Entered Barcode
	$('.notify_msg').hide();
	$('.po_notify_msg').hide();
	$('#po_sel_brand').val(0);
	$('#po_sel_prd').html('');
	if(!b)
	{
		alert("Please enter Barcode");return false;
	}
	if(e.which==13)//Check whether barcode is 13 characters long
	{
		$('.po_det_prod_container').html('<div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
		barcode_det(b,1);
	}
});	

//Load Products on brands select 
$('#sel_brand').live('change',function(){
	var brand=$(this).val();
	searchStyle.innerHTML='';
	product_list(brand,0,0,0);
});

//Load Products on brands select 
$('#po_sel_prd').live('change',function(){
	var p=$(this).val();
	searchStyle.innerHTML='';
	product_list(0,0,p,1);
});

//Load Products on brands select 
$('#po_sel_brand').live('change',function(){
	var brand=$(this).val();
	searchStyle.innerHTML='';
	is_brand_change=1;
	product_list(brand,0,0,1);
	if(brand != 0)
		$('.sel_product').removeClass('hide');
	else
		$('.sel_product').addClass('hide');
});

//Function to load products
function product_list(brand,catid,product_id,is_open_po_srch)
{
	if(is_open_po_srch == 0)
		$('.prod_container').html('<div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
	else
		$('.po_det_prod_container').html('<div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
	
	$.post(site_url+'admin/jx_product_list',{brandid:brand,catid:catid,pid:product_id,is_open_po_srch:is_open_po_srch},function(resp){
	var prd_html='';
	var filter_html='';
	if(resp.status=='error')
	{
		if(is_open_po_srch == 0)
		{
			$('.prod_container').html('<div class="page_alert_wrap">No Products Found</div>');
			$('#inp_barcode').select();
		}
		else
		{
			$('.po_det_prod_container').html('<div class="page_alert_wrap">No Products Found</div>');
			$('#po_sel_prd').html('');
			$('#open_po_inp_barcode').select();
		}
			
		filter_html+='';
		return false;
	}
	else
	{
		prd_html+='<div class="sk_deal_container" style="overflow:inherit">';
		prd_html+='<div class="sk_deal_header_wrap">';	
		prd_html+='<table class="sk_deal_blk_wrap" cellpadding="0" cellspacing="0" width="100%">';
		prd_html+='<thead><tr>';
		prd_html+='<th width="8%">Product ID</th><th width="56%">Product Name</th><th width="7%" >Old MRP</th><th width="11%" >New MRP</th><th width="11%" >Location</th><th width="10%" >Action</th>';
		prd_html+='</tr></thead></table>';
	 	prd_html+='</div>';
	 	
	 	prd_html+='<div class="sk_deal_content_wrap">';
	 		prd_html+='<table cellpadding="0" cellspacing="0" width="100%">';	
	 		filter_html+='<option value="0">Choose</option>'
		 	$.each(resp.prds_lst,function(i,p){
				var srch_indx_prd_name='';
				var srch_indx_prd_id='';
				if(p.is_sourceable == 1)
					var background='background:none repeat scroll 0 0 rgba(170, 255, 170, 0.8) !important'/*#40FC36*/;
				else
					var background='background:none repeat scroll 0 0 #FFAAAA !important';
				
				//Convert product name and id to lower case for client side search 
				srch_indx_prd_name=p.product_name;
				srch_indx_prd_id=p.product_id;
				srch_indx_prd_name=srch_indx_prd_name.toLowerCase();
				srch_indx_prd_id=srch_indx_prd_id.toLowerCase();
				
				prd_html+='<tr style="'+background+'" class="searchable prod_filter_wrap" data-index="'+srch_indx_prd_name+''+srch_indx_prd_id+'" prdid_'+p.product_id+' sourceable="'+p.is_sourceable+'" name='+p.product_name+' pid='+p.id+' bid='+p.brand_id+' cid='+p.product_cat_id+' mrp='+p.mrp+'>';
		 			prd_html+='<td width="8%"><span style="font-weight:bold;font-size:12px;color:green">'+p.product_id+'</span></td>';
 					prd_html+='<td width="55%"><span class="title"><a class="product_name" href="'+site_url+'/admin/product/'+p.product_id+'" target="_blank">'+p.product_name+'</a><span style="float:right;font-size:9px;color:green"></span></td>';
					prd_html+='<td width="6%"><span class="prod_det_font_wrap">'+p.mrp+'</span></td>';
					prd_html+='<td width="6%"><span class="prod_det_font_wrap"><input type="text" class="new_mrp_'+p.product_id+'" pid="'+p.product_id+'"  bid='+p.brand_id+' name="new_mrp"></span></td>';
					prd_html+='<td width="6%"><span class="prod_det_font_wrap"><select name="storage" class="loc loc_'+p.product_id+'" product_id="'+p.product_id+'" bid='+p.brand_id+'>';
					prd_html+=''+p.rbs+'';		
					prd_html+='</select></span></td>';					
					prd_html+='<td width="10%"><button type="buton" class="button button-tiny button-action prd_selected" prodct_id="'+p.product_id+'" brand_id="'+p.brand_id+'" mrp="'+p.mrp+'" is_open_po_srch="'+is_open_po_srch+'">Allot Barcode</button></td>';
				prd_html+='</tr>';
				
				
				filter_html+='<option value="'+p.product_id+'">'+p.product_name+'</option>'
			});
		prd_html+='</table>';
		prd_html+='</div>';
		prd_html+='</div>';
		
		//check if products html are open po products before load html to container 
		if(is_open_po_srch == 0)//If not 
		{
			$('.prod_container').html(prd_html);
			$('#inp_barcode').select();
		}
		else if(is_open_po_srch == 1)
		{
			if(is_brand_change==1)
			{
				$('#po_sel_prd').html(filter_html);
			}
			is_brand_change=0;
			$('.po_det_prod_container').html(prd_html);	
			$('#open_po_inp_barcode').select();
		}
		$("#sel_cat").chosen();
	}
	},'json');	
}

//Details of products based on barcode
function barcode_det(b,is_open_po_srch)
{
	var prod_cnt='';
	//Ajax function to check whether details found for a input barcode
	$.post(site_url+'/admin/jx_prd_srch_bybarcode',{chr:b,is_open_po_srch:is_open_po_srch},function(resp){
		var prod_det=resp.p_det;	
		if(resp.status == 'error')//If response status is error[barcode not found]
		{
			//alert("Barcode not found");
			//return false;
			if(is_open_po_srch==0)
			{
				$('.barc_det_inp_blk').removeClass('hide');
			}
			else
			{
				$('.po_barc_det_inp_blk').removeClass('hide');
			}
		}
		else
		{
			prod_cnt+='<table class="datagrid" width="100%">';
			prod_cnt+='<thead><tr><th>Product Id</th><th>Product Name</th><th>Brand</th><th>Category</th>';
			prod_cnt+='<th>Quantity</th><th>MRP</th><th>Purchase Cost</th><th>Expiry Date</th><th>Self Life</th><th>Action</th></tr></thead>';
			prod_cnt+='<tbody>';
			$.each(prod_det,function(i,p){
				prod_cnt+='<tr><td>'+p.product_id+'</td>';
				prod_cnt+='<td><a class="product_name" href="'+site_url+'/admin/product/'+p.product_id+'" target="_blank">'+p.product_name+'</a></td>';
				prod_cnt+='<td><a class="product_name" href="'+site_url+'/admin/viewbrand/'+p.brand_id+'" target="_blank">'+p.brand_name+'</a></td>';
				prod_cnt+='<td><a href="'+site_url+'/admin/viewcat/'+p.product_cat_id+'" target="_blank">'+p.cat_name+'</a></td>';
				prod_cnt+='<td>'+p.available_qty+'</td>';
				prod_cnt+='<td>'+p.mrp+'</td>';
				prod_cnt+='<td>'+p.purchase_cost+'</td>';
				if(p.expiry_on == null)
					prod_cnt+='<td>-N/A-</td>';
				else 		
					prod_cnt+='<td>'+p.expiry_on+'</td>';
				
				if(p.self_life == -1)
					p.self_life='No Expiry';
				else
					p.self_life=p.self_life +' months';
					
				prod_cnt+='<td>'+p.self_life+'</td>';
				
				if(p.available_qty > 0)
				prod_cnt+='<td><a class="upd_stk_prodbc" stock_id="'+p.stock_id+'" barc="'+p.product_barcode+'"><button class="button button-tiny button-action">Update Barcode</button></a></td></tr>';
			});
			
			prod_cnt+='</tbody>';
			prod_cnt+='</table>';
		}
	
		if(is_open_po_srch==0)
		{
			$('.prod_container').html(prod_cnt);//Append Product Details to container
			$('#inp_barcode').select();
		}
		else
		{
			$('.po_det_prod_container').html(prod_cnt);//Append Product Details to container
			$('#open_po_inp_barcode').select();
		}
			
	},'json');
}

//Function to search products by product name
$('#srch_product').live('keyup',function(){
	document.getElementById('srch_product').addEventListener('input', function() {
		if (!this.value) {
			searchStyle.innerHTML = "";
			return;
		}
		searchStyle.innerHTML = ".searchable:not([data-index*=\"" + this.value.toLowerCase() + "\"]) { display: none; }";
	});
	
	//Server Side Search
	search_othr_products();
});

//Server Side Search
function search_othr_products()
{
	$('input[name="srch_product"]').autocomplete({
		source:site_url+'/admin/jx_searchprds_json/',
		minLength: 2,
		select:function(event, ui ){
			product_list(ui.item.brand,0,ui.item.id,0);
		}
	});
}

//Action on click allot barcode button
$('.prd_selected').live('click',function(){
	var pid=$(this).attr('prodct_id');
	var brand_id=$(this).attr('brand_id');
	var is_open_po_srch=$(this).attr('is_open_po_srch');
	var mrp=$('.new_mrp_'+pid).val();
	var storage=$('.loc_'+pid).val();
	$(this).attr('disabled',true);

	
	if(is_open_po_srch == 0)
		var barcode=$('#inp_barcode').val();	
	else
		var barcode=$('#open_po_inp_barcode').val();
	
	//MRP validation
	if(!mrp)
	{
		alert("Please enter MRP Value");
		$('.new_mrp_'+pid).focus();
		$(this).attr('disabled',false);
		return false;
	}
	//if number is not a integer 
	if(isNaN(mrp))
	{
		alert("Please enter Valid MRP");
		$('.new_mrp_'+pid).select();
		$(this).attr('disabled',false);
		return false;
	}
	
	//Location validation
	if(storage == 0)
	{
		alert("Please enter Location");
		$(this).attr('disabled',false);
		return false;
	}
	
	//Ajax function to get product details by barcode
	$.post(site_url+'admin/jx_allot_barcode_toproduct',{barcode:barcode,pid:pid,mrp:mrp,brand_id:brand_id,storage:storage},function(resp){
		var prd_html='';
		if(resp.status=='error')
		{
			if(is_open_po_srch == 0)
				$('.prod_container').html('<div class="page_alert_wrap">No Products Found</div>');
			else
				$('.po_det_prod_container').html('<div class="page_alert_wrap">No Products Found</div>');	
			return false;
		}
		else
		{
			barcode_det(barcode,is_open_po_srch);
			
			if(is_open_po_srch == 0)
				$('.notify_msg').html('Barcode Allotted Successfully').show();
			else
				$('.po_notify_msg').html('Barcode Allotted Successfully').show();
				
			$('.barc_det_inp_blk').addClass('hide');
			$('.po_barc_det_inp_blk').addClass('hide');
		}
	});
});

$('.upd_stk_prodbc').live('click',function(){
	$('#upd_stk_prodbc_dlg').data('stk_id',$(this).attr("stock_id")).dialog('open');
});

$('#upd_stk_prodbc_dlg').dialog({
				autoOpen:false,
				width:400,
				height:200,
				modal:true,
				open:function(){
					stk_id = $(this).data('stk_id');
					$.getJSON(site_url+'/admin/jx_get_stkprobyid/'+stk_id,'',function(resp){
						if(resp.status == 'error')
						{
							alert(resp.error);
						}else
						{
							$('#upd_old_bc').val(resp.stkdet.product_barcode);
							$('#upd_new_bc').val('');
						}
					});
				},
				buttons:{
					'Cancel':function(){
						$('#upd_stk_prodbc_dlg').dialog('close');
					},
					'Update':function(){
						$(".ui-dialog-buttonpane button:contains('Update')").button().button("disable");
						var newbc = $('#upd_new_bc').val();
						
							$.post(site_url+'/admin/jx_upd_stkprodbc','stk_id='+stk_id+'&newbc='+newbc,function(resp){
								if(resp.status == 'error')
								{
									$(".ui-dialog-buttonpane button:contains('Update')").button().button("enable");
									alert(resp.error);
								}else
								{
									alert("Barcode updated successfully");
									location.href = location.href;
								}
							},'json');
						 
					}
				}
		});
</script>