<?php 
	$paf_flags = array();
	$paf_flags[0] = '';
	$paf_flags[1] = 'Open';
	$paf_flags[2] = 'Closed';
	$paf_flags[3] = 'Cancelled';
	 
?>
<div class="container" >

	<?php 
	if($pafdata[0]['paf_status'] == 1)
		{
	?>
		<span style="float: right">
			<a href="javascript:void(0)" onclick="stock_intake(<?php echo $pafdata[0]['paf_id']?>)">Process Stock Intake</a> 
			|
			<a href="javascript:void(0)" onclick="cancel_paf(<?php echo $pafdata[0]['paf_id']?>)">Mark Cancelled</a>
			&nbsp;
			&nbsp;
			&nbsp;
			
			<a href="javascript:void(0)" onclick="gen_print(<?php echo $pafdata[0]['paf_id']?>)">Generate Print</a> 
			|
			<a href="javascript:void(0)" onclick="export_csv(<?php echo $pafdata[0]['paf_id']?>)">Export CSV</a>
		</span> 
	 <?php 
		} 
	?>

<h2>PAF Details</h2>

<?php 
	if($pafdata[0]['paf_status'] == 1)
	{
?>
<form method="post" id="poprodfrm" action="<?php echo site_url('admin/process_update_paf');?>" autocomplete="off">
<?php } ?>
	<input type="hidden" value="<?php echo $pafdata[0]['paf_id']?>" name="paf_id" />
	<div style="padding:5px;">
		<b>CreatedOn :</b> &nbsp;&nbsp;&nbsp;&nbsp; <?php echo date('d/m/Y h:i a',strtotime($pafdata[0]['created_on']));?>
	</div>
	<div style="padding:5px;">
		<b>Status :</b> &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  
			<?php 
				echo $paf_flags[$pafdata[0]['paf_status']];
			?>
	</div>
	<div style="padding:5px;">
		<b>Handled by : </b>&nbsp;&nbsp;&nbsp;
		<select name="handled_by">
			<option value="">Choose</option>
			<?php 
				$emp_list_res = $this->db->query('select id,name,mobile from m_employee_list where role = "DELIVARY_MANAGER" order by name ');
				if($emp_list_res->result_array())
				{
					foreach($emp_list_res->result_array() as $row)
					{
						$sel = (($pafdata[0]['handled_by'] == $row['id'])?'selected':'');
						echo '<option '.$sel.' value="'.$row['id'].'">'.$row['name'].'-'.$row['mobile'].'</option>';
					}
				}
			?>	
		</select>
	</div>
	<div style="padding:5px;">
		<b>Remarks :</b> &nbsp;&nbsp;&nbsp;&nbsp; <?php echo $pafdata[0]['remarks']?>
	</div>
	<?php 
	if($pafdata[0]['paf_status'] == 1)
	{
?>
	<div style="padding:5px;">
		<b>Search &amp; Add :</b> <input type="text" class="inp" id="po_search" style="width:400px;"> <input type="button" id="load_unavail" value="Load stock unavailable products">
		<div class="srch_result_pop closeonclick" id="po_prod_list"></div>
	</div>
	<?php 
	}
?>
	<table class="datagrid" id="pprods" >
	<thead>
		<tr>
			<th width="20">S.No</th>
			<th width="30">PAF Prod REFno</th>
			<th width="300">Product</th>
			<th width="50">Ordered in Past 90 Days</th>
			<th width="30">Current Stock</th>
			<th width="30">Pending Order Qty</th>
			<th width="30">Qty</th>
			<th width="30">MRP</th>
			<th width="200">Vendor</th>
			<th>Notify</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			$i=0;
			foreach($pafdata  as $paf)
			{ 
				
				$pen_order_qty = ($prod_stk_det[$paf['product_id']]['pending_qty']-$prod_stk_det[$paf['product_id']]['cur_stk']);
				if($pen_order_qty < 0)
					$pen_order_qty = 0;
		?>
			<tr class="barcode--barcode-- barcodereset oldpafdata">
				<td class="slno_indx"><?php echo ++$i;?></td>
				<td><?php echo $paf['prod_paf_id']?></td>
				<td>
					<input type="hidden" name="product[]" value="<?php echo $paf['product_id']?>">
					<input type="hidden" name="prod_paf_id[]" value="<?php echo $paf['prod_paf_id']?>">
					<span class="pname"><?php echo $paf['product_name']?></span>
				</td>
				<td><input type="text" class="inp" size=2 value="<?php echo $prod_stk_det[$paf['product_id']]['past_orders']?>"></td>
				<td><input type="text" class="inp" size=2 value="<?php echo $prod_stk_det[$paf['product_id']]['cur_stk']?>"></td>
				<td><input type="text" class="inp" size=2 value="<?php echo $pen_order_qty?>"></td>
				<td><input type="text" class="inp" size=2 name="qty[]" oqty="<?php echo $paf['qty']?>" value="<?php echo $paf['qty']?>"></td>
				<td><input type="text" class="inp" size=2 name="mrp[]" value="<?php echo $paf['mrp']?>"></td>
				<td >
					<select class="vendor" name="vendor[]">
						<option value="0">Unknown</option>
					<?php 
						$sel_ven_id = $paf['vendor_id'];
						$ven_list = explode(',',$paf['vendors']);
						foreach($ven_list as $ven)
						{
							list($ven_id,$ven_name,$ven_margin) = explode('::',$ven);
							
							$sel = ($sel_ven_id == $ven_id)?'selected':'';
							
							echo '<option  '.$sel.' margin="'.$ven_margin.'" value="'.$ven_id.'">'.$ven_name.'('.$ven_margin.'%)</option>';
						}
					?>
						
					</select>
				</td>
				<td><input type="checkbox" class="notify" value="1" style="visibility: hidden;"></td>
				<td>
					&nbsp;
				</td>
			</tr>
		<?php 		
			}
		?>
	</tbody>
	</table>
	<?php 
	if($pafdata[0]['paf_status'] == 1)
	{
?>
	<div style="padding:20px 0px;">
		
		<div id="notify_handler_block" >
				<b>Notify Changes to handler </b> 
				 
				<a href="javascript:void(0)" onclick="compose_msg()" >Refresh msg</a>
				<br />
			 <textarea rows=3 cols="70" style="width: 60%;" name="notify_msg" ></textarea>
		</div>
		<div>
			<input type="submit" value="Update PAF">
		</div>
		
	</div>
	</form>
	<?php 
	}
?>

<div>
	<b>PAF Notification Log</b>
	<table class="datagrid">
		<thead>
			<tr>
				<th>Message</th>
				<th>SentOn</th>
			</tr>
		</thead>
		<tbody>
			<?php 
				foreach($paf_smslog->result_array() as $row)
				{
			?>
				<tr>
					<td><?php echo $row['message']?></td>
					<td><?php echo format_datetime($row['logged_on'])?></td>
				</tr>
			<?php 		
				}
			?>
		</tbody>	
	</table>
</div>


<div style="display:none">
<table id="p_clone_template">
	<tr class="barcode--barcode-- barcodereset new_prods">
		<td class="slno_indx">%sno%</td>
		<td>NEW</td>
		<td><input type="hidden" name="product[]" value="%product_id%">
		<input type="hidden" name="prod_paf_id[]" value="0"><input type="hidden" class="brand" name="brand[]" value="%brand_id%"><span class="pname">%product_name%</span><br /><b>(%product_brand%)</b></td>
		<td><input type="text" class="inp" size=2 value="%past_order_qty%"></td>
		<td><input type="text" class="inp" size=2 value="%current_stock%"></td>
		<td><input type="text" class="inp" size=2 value="%pen_order_qty%"></td>
		<td><input type="text" class="inp" size=2 name="qty[]" value="%require_qty%"></td>
		<td><input type="text" class="inp" size=2 name="mrp[]" value="%mrpvalue%"></td>
		<td><select class="vendor" name="vendor[]">%vendorlist%</select></td>
		<td><input type="checkbox" class="notify" checked value="1"></td>
		<td><a href="javascript:void(0)" onclick='delete_tbl_row(this)'>remove</a></td>
	</tr>
</table>
</div>


</div>
<div style="display: none;">
	<iframe id="hndl_frame_action" name="hndl_frame_action" style="width: 1px;height: 1px;border:0px;"></iframe>
</div>

<script>

function gen_print(paf_id)
{
	$('#hndl_frame_action').attr('src',site_url+'/admin/print_paf/'+paf_id);
}

function stock_intake(paf_id)
{
	if(confirm("Do you want to proceed for stock-intake ? "))
	{
		location.href = site_url+'/admin/paf_stockintake/'+paf_id;
	}
}

function cancel_paf(paf_id)
{
	if(confirm("Are you sure want to cancel this paf ?"))
	{
		$.post(site_url+'/admin/jx_cancel_paf','id='+paf_id,function(resp){
			alert(resp.message);
			if(resp.status == 'success')
			{
				location.href = location.href;
			}
		},'json');
	}
}

function export_csv(paf_id)
{
	$('#hndl_frame_action').attr('src',site_url+'/admin/export_paf/'+paf_id);
}

$('input[name="notify_handler"]').change(function(){
	if($(this).attr('checked'))
	{
		compose_msg();
		$('textarea[name="notify_msg"]').show();
	}
	else
	{
		$('textarea[name="notify_msg"]').val('').hide();
	}
		
});

$('#poprodfrm').submit(function(){

	var block_frm_submit = 0;
	var qty_pending = 0;
	var ven_pending = 0;
		$('.datagrid tbody tr',this).each(function(){
			qty = $('input[name="qty[]"]',this).val()*1;
			ven = $('select[name="vendor[]"]',this).val()*1;

			if(!qty)
				qty_pending += 1;

			 
			
			 
		});

	if(qty_pending || ven_pending){
		alert("Unable to submit request, please check qty ");
		return false;
	}
		
	
});


function compose_msg()
{
	var msg_by_vendor = new Array();
	var msg = '';  
		$('#pprods .notify:checked').each(function(){
			var pname = $(this).parent().parent().find('.pname').text();
			var pmrp = $(this).parent().parent().find('input[name="mrp[]"]').val();
			var pqty = $(this).parent().parent().find('input[name="qty[]"]').val();
			var ven = $(this).parent().parent().find('select[name="vendor[]"] option:selected').text();
				msg_by_vendor.push(pname+'('+ven+')'+'-'+pmrp+'-'+pqty);
		});
		if($('#pprods .notify:checked').length)
			$('textarea[name="notify_msg"]').val(msg_by_vendor.join("\r\n")+"\r\n"+'added to your order list');
		else
			$('textarea[name="notify_msg"]').val('');
		
}

function delete_tbl_row(ele)
{
	$(ele).parent().parent().remove();
	$('.slno_indx').each(function(i,itm){
		$(this).text(i+1);
	});
}

var added_po=[];

function addproduct(id,name,mrp,require)
{
	$('#notify_handler_block').show();
	$('input[name="notify_handler"]').attr('checked',true);
	
	require = (typeof require === "undefined") ? "" : require;
	$("#po_prod_list").hide();
	if($.inArray(id,added_po)!=-1)
	{
		alert("Product already added to the current Order");
		return;
	}
	$.post("<?=site_url("admin/jx_productdetails")?>",{id:id},function(data){
		o=$.parseJSON(data);
		i=$('#pprods .slno_indx').length;
		template=$("#p_clone_template tbody").html();
		template=template.replace(/%sno%/g,i+1);
		template=template.replace(/%require_qty%/g,require);
		template=template.replace(/%pen_order_qty%/g,((o.pen_ord_qty-o.cur_stk)>0)?(o.pen_ord_qty-o.cur_stk):0);
		template=template.replace(/%product_id%/g,o.product_id);
		template=template.replace(/%brand_id%/g,o.brand_id);
		template=template.replace(/%product_name%/g,o.product_name);
		template=template.replace(/%product_brand%/g,o.brand_name);
		template=template.replace(/--barcode--/g,o.barcode);
		template=template.replace(/%mrpvalue%/g,o.mrp);
		template=template.replace(/%margin%/g,o.margin);
		template=template.replace(/%foc%/g,"foc"+i);
		template=template.replace(/%offer%/g,"offer"+i);
		template=template.replace(/%current_stock%/g,o.cur_stk);
		template=template.replace(/%past_order_qty%/g,o.orders);
		
		mrp=parseInt(o.mrp);
		pprice=mrp-(mrp*parseInt(o.margin)/100);
		template=template.replace(/%pprice%/g,pprice);
		vendors="";
		
		$.each(o.vendors,function(i,v){
			vendors=vendors+'<option value="'+v.vendor_id+'">'+v.vendor+'</option>';
		});
		vendors+="<option value='0'>Unknown</option>";
		template=template.replace(/%vendorlist/g,vendors);
		$("#pprods tbody").append(template);
		added_po.push(id);
		compose_msg();
	});
}
var search_timer=0;
var jHR=0;
$(function(){

	$("#srch_barcode").keyup(function(e){
		if(e.which==13)
		{
			$(".barcodereset").removeClass("highlightprow");
			if($(".barcode"+$(this).val()).length==0)
			{
				alert("Product not found on rising PO");
				return;
			}
			$(".barcode"+$(this).val()).addClass("highlightprow");
			$(document).scrollTop($(".barcode"+$(this).val()).offset().top);
		}
	});

	
	$("#load_unavail").click(function(){
		$(this).attr("disabled",true);
		$.post("<?=site_url("admin/jx_load_unavail_products")?>",{hash:<?=time()?>},function(data){
			os=$.parseJSON(data);
			$.each(os,function(i,o){
				addproduct(o.product_id, "", "",o.qty-o.available);
			});
		});
	}).attr("disabled",false);
	$("#po_search").keyup(function(){
		q=$(this).val();
		if(jHR!=0)
			jHR.abort();
		clearTimeout(search_timer);
		search_timer=setTimeout(function(){
		jHR=$.post("<?=site_url("admin/jx_searchproducts")?>",{q:q},function(data){
			$("#po_prod_list").html(data).show();
		});},100);
	}).focus(function(){
		if($("#po_prod_list a").length==0)
			return;
		$("#po_prod_list").show();
	}).click(function(e){
		e.stopPropagation();
	});
	$("#pprods .calc_pp").live("change",function(){
		$r=$(this).parents("tr").get(0);
		mrp=parseInt($(".mrp",$r).val());
		margin=parseInt($(".margin",$r).val());
		discount=parseInt($(".discount",$r).val());
		mmrp=mrp-(mrp*margin/100);
		if($(".type",$r).val()==1)
			mmrp=mmrp-(mrp*discount/100);
		else
			mmrp=mmrp-discount;
		if(isNaN(mmrp))
			mmrp="-";
		$(".pprice",$r).val(mmrp);
	});
	$("#pprods .vendor").live("change",function(){
		$r=$(this).parents("tr").get(0);
		$.post("<?=site_url("admin/jx_getbrandmargin")?>",{v:$(this).val(),b:$(".brand",$r).val()},function(data){
				$(".margin",$r).val(data).change();
		});
	});
});

$('input[name="qty[]"]').live('change',function(){
	if($(this).attr('oqty')*1 != $(this).val())
	{
		$(this).parent().parent().find('.notify').attr('checked',true);
	}else
	{
		$(this).parent().parent().find('.notify').attr('checked',false);
	}
	
 
	compose_msg();
});


$('input[name="product[]"]').each(function(){
	added_po.push($(this).val()); 
});

$('.notify').live('change',function(){
	compose_msg();
});

<?php
	if($pafdata[0]['paf_status'] != 1)
	{
?>
$('.container input[type="text"],.container select').attr('disabled',true);
<?php 
	} 
?>

</script>

<style>
.highlightprow{
background:#ff9900;
}
</style>


<?php
