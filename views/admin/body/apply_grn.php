<style>
	.leftcont{display:none}
</style>
<div class="container" >

<?php 
	$notify_grn = $this->session->flashdata("notify_grn");
if($notify_grn){?>
<div style="background:#ff9900;padding:5px;color:#fff;" align="center"><h3><?php echo $notify_grn;?></h3></div>
<?php }?>
<span style="float: right"> Min PO Date:<input type="text" size="8" style="padding;2px;" name="min_po_date" value="<?php echo $min_po_date; ?>"> </span>
<h2>Stock In</h2>
<h4>Stock Intake No : <?=$this->db->query("select grn_id from t_grn_info order by grn_id desc")->row()->grn_id+1?></h4>

<div id="max_po_cont_size" style="margin:10px 0px;padding:5px;background:#eee;">
<h3 style="margin:0px;">Ready for stock intake</h3>
<div id="fixed_cont_size" style="overflow:auto;">
	<table cellspacing=5 >
	<tr>
	<?php foreach($pos as $vendor=>$ps){?>
	<td style="border:1px solid #bbb;background:#F7EFB9;" class="vendor_list venl<?=$ps[0]['vendor_id']?>">
	<span class="vid" style="display:none;"><?=$ps[0]['vendor_id']?></span>
		<h4 style="margin:3px;background:#555;color:#fff;padding:5px;height: 40px;text-align: center;"><span style="vertical-align: middle;display: block;"><?=$vendor?></span></h4>
	<div class="ajax_loadresult static_pos">
	<?php foreach($ps as $p){?>
	<div><a href="javascript:loadpo('<?=$p['po_id']?>')"><?=$p['remarks']?> : PO<?=$p['po_id']?> <span><?=date("d/m/y",strtotime($p['created_on']))?></span></a></div>
	<?php }?>
	</div>
	</td>
	<?php }?>
	</tr>
	</table>
</div>
<?php if(empty($pos)){?>POs are not available for stock intake<?php }?>
</div>

<script type="text/javascript">
	$(function(){
		var rw =$(window).width()-$('.leftcont').width();
		$('#fixed_cont_size').css('width',rw-50+'px');
	});
	$('input[name="min_po_date"]').datepicker({onSelect:function(d){
		location.href = site_url+'/admin/apply_grn/0/'+d;
	}});
	
</script>



<div style="margin:10px 0px;padding:5px;background:#eee;">
<b>Load Purchase Order By Vendor</b><br>
Vendor : <select id="grn_vendor">
<?php foreach($this->db->query("select * from m_vendor_info order by vendor_name asc")->result_array() as $v){?>
<option value="<?=$v['vendor_id']?>" <?php if($v['vendor_id']==$this->uri->segment(3)){?>selected<?php }?>><?=$v['vendor_name']?></option>
<?php }?>
</select><input type="button" id="grn_po_load" value="Load">
		<img src="<?=IMAGES_URL?>loading_maroon.gif" id="po_loading" style="display:none;">
	<div id="pending_pos" class="ajax_loadresult">
	</div>
</div>



<div id="loadafterselect" style="display:none">
<div style="margin:5px 0px;position:fixed;right:0px;bottom:40px;background:#F7EFB9;padding:15px;border:1px solid #aaa;">Highlight product of barcode : <input type="text" id="srch_barcode"></div> 
<form method="post" id="apply_grn_form" enctype="multipart/form-data">
<div id="grn_pids">
<?php if(isset($po)){?>
<input type="hidden" name="poids[]" value="<?=$po['po_id']?>">
<?php }?>
</div>
<div id="grn">
	<table class="datagrid">
		<thead><tr><th>S.no</th><th>Product</th><th>PO Qty</th><th>Invoice Qty<div style="font-size:80%;">reset to<br><input type="text" id="reset_inv" value="0" style="width:20px;font-size:90%;padding:0px;"><input type="button" value="ok" style="font-size:85%;padding:0px;" onclick='reset_inv_f()'></div></th><th>Receiving Qty<div style="font-size:80%">reset to<br><input type="text" id="reset_rec" value="0" style="width:20px;font-size:90%;padding:0px;"><input type="button" value="ok" onclick='reset_rec_f()' style="font-size:85%;padding:0px;"></div></th><th>PO MRP</th><th>MRP</th><th>PO DP Price</th><th>PO Purchase Price</th><th>Purchase Price</th><th>Storage</th><th>Received<br>Till Date</th><th>Pending Qty</th><th>FOC</th><th>Has Offer</th><th>Expiry date</th></tr></thead>
		<tbody>
<?php if(isset($po)){?>
		<?php foreach($po_items as $i){?>
		<tr>
		<td></td>
		<td><span style="font-size:80%"><?=$i['product_name']?><input type="hidden" class="prod_addcheck" name="pid<?=$i['po_id']?>[]" value="<?=$i['product_id']?>"></span></td>
		<td><?=$i['order_qty']?><input type="hidden"  class="popqty" value="<?=$i['order_qty']-$i['received_qty']?>"></td>
		<td><input type="text" class="inp iqty" name="oqty<?=$i['po_id']?>[]" size=3 value="<?=$i['order_qty']-$i['received_qty']?>"></td>
		<td><input type="text" class="inp rqty qtychange" name="rqty<?=$i['po_id']?>[]" size=3 value="<?=$i['order_qty']-$i['received_qty']?>"></td>
		<td><?=$i['mrp']?></td>
		<td><input type="text" class="inp" name="mrp<?=$i['po_id']?>[]" size=5 value="<?=$i['mrp']?>"></td>
		<td><input type="text" class="inp" name="dp_price<?=$i['po_id']?>[]" size=5 value="<?=$i['dp_price']?>"><?=$i['dp_price']?></td>
		<td><?=$i['purchase_price']?></td>
		<td><input type="text" class="inp" name="price<?=$i['po_id']?>[]" size=5 value="<?=$i['purchase_price']?>"></td>
		<td>
		<select class="stkloc" name="storage<?=$i['po_id']?>[]">
		<?php foreach($this->db->query("select * from m_rack_bin_info")->result_array() as $r){?>
		<option value="<?=$r['id']?>" <?php if($i['default_rackbin_id']==$r['id']) echo 'selected';?>><?=$r['rack_name']?>-<?=$r['bin_name']?></option>
		<?php }?>
		</select>
		</td>
		<td><?=$i['received_qty']?></td>
		<td class="pqty">0</td>
		<td><?=$i['is_foc']?"YES":"NO"?></td>
		<td><?=$i['has_offer']?"YES":"NO"?></td>
		<td><input type="text" class="inp edate expdate" name="expdate[]"></td>
		</tr>
		<?php }?>
<?php }?>
		</tbody>
	</table>
</div>

<div style="margin:10px;clear:both;">
<h3>Total value of receiving quantity : <span style="font-size:140%;" id="value_receiving"></span></h3>
</div>

<div style="float:right;margin-top:20px;padding:10px;background:#F7EFB9;">
<h4>Remarks</h4>
<textarea name="remarks" cols=40 rows=5></textarea>
</div>
<div>
<h3>Invoice Details</h3>
<a href="javascript:void(0)" onclick='cloneinvoice()'>link another invoice</a>
<table class="datagrid invoice_tab">
<thead>
<tr>
<th>Invoice No</th>
<th>Date</th>
<th>Invoice Amount</th>
<th>Scanned Copy</th>
</tr>
</thead>
<tbody>
<tr>
<td><input type="text" name="invno[]" class="inp"></td>
<td><input type="text" name="invdate[]" class="inp datepick"></td>
<td>Rs. <input size=7 type="text" class="inp" name="invamount[]"></td>
<td><input type="file" name="scan_0" class="scan_file"></td>
</tr>
</tbody>
</table>
</div>
<div style="padding:20px 0px;">
<input type="submit" value="Submit Stock" id="form_submit">
</div>
</form>
</div>

<div id="grn_template" style="display:none">
	<div class="right">
		<table>
		<tbody>
			<tr class="barcode%bcode% barcodereset " po_id="%pid%" prod_id="%prodid%" >
			<td>%sno%</td>
			<td>
				<input type="hidden" name="imei%prodid%" class="imeisvvv imei%prodid%" value="">
				<span style="font-size:80%"><span class="name">%name%</span><input type="hidden" name="pid%pid%[]" class="prod_addcheck" value="%prodid%"></span>
				<div class="imei_cont"></div>
				<div style="padding:5px;background: #ccc;">
					<input type="hidden" style="padding:2px;font-size: 9px;width: 95%;" class="scan_pbarcode pbcodecls%prodid%" value="" name="pbarcode%pid%[]" />
					<span style="font-size:70%;"><a href="javascript:void(0)"  onclick='show_add_barcode(event,"%pid%","%prodid%")'>%update_barcode%</a></span>
				</div>
				<span style="font-size:70%" class="imei_upd_cntrl_%prodid%"><a href="javascript:void(0)" style="color:red" onclick='show_add_imei(event,"%prodid%")'>%add_serial%</a></span>
			</td>
			<td class="poqty">%qty%<input type="hidden" class="popqty" value="%qty%"></td>
			<td><input type="text" class="inp iqty" name="oqty%pid%[]" size=3 value="%pqty%"></td>
			<td><input type="text" class="inp rqty qtychange" name="rqty%pid%[]" size=3 value="%pqty%"></td>
			<td>%po_mrp%</td>
			<td><input type="text" class="inp prod_mrp" name="mrp%pid%[]" size=5 pmrp="%mrp%" value="">
				<div class="upd_pmrp_blk" align="center"> 
					<input type="checkbox" value="1" class="upd_pmrp" name="upd_pmrp%pid%[%prodid%]" >
					<div>Update Deal and product MRP</div>
				</div>
			</td>
			<td>
				<div style="visibility:%dp_price_inp%">
					<input type="text" class="inp dp_price" name="dp_price%pid%[]" size=5 dp_price="%dp_price%" value="%dp_price%">
					<div class="upd_dp_price_blk" align="center"> 
						<input type="checkbox" value="1" class="upd_dp_price" name="upd_dp_price%pid%[%prodid%]" >
						<div>Update DP Price</div>
					</div>
				</div>
			</td>
			<td>%price%</td>
			<td>
				<input type="text" class="inp pprice" name="price%pid%[]" size=5 value="%ppur_price%">
			</td>
			<td>
			<select class="stkloc" name="storage%pid%[]">
			==rbs==
			</select>
			</td>
			<td>%rqty%</td>
			<td class="pqty">0</td>
			<td>%foc%</td>
			<td>%offer%</td>
			<td><input type="text" name="expdate[]" class="inp edate expdate%dpe%"></td>
			</tr>
		</tbody>
		</table>
	</div>
</div>

<div id="invoice_template" style="display:none">
<table>
<tbody>
<tr>
<td><input type="text" name="invno[]" class="inp" class="invno"></td>
<td><input type="text" name="invdate[]" class="inp datepick%dpi%" class="invdate"></td>
<td>Rs. <input size=7 type="text" class="inp" name="invamount[]" class="invamount"></td>
<td><input type="file" name="scan_%dpi%"></td>
<td>
	<a href="javascript:void(0)" onclick="$(this).parent().parent().remove()">Remove</a>	
</td>
</tr>
</tbody>
</table>
</div>

</div>

<div id="grn_color_legends" style="position: fixed;bottom:0px;background: #FFF;left:0px;width:135px;;padding:4px;font-size: 10px;font-weight: bold;display: none;">
	<h4 style="margin:0px;background: #000;color: #FFF;padding:0px;text-align: center;font-size: 12px;">Color Legends</h4>
	<span style="background: #b4defe;padding:5px;">UnScanned</span>
	<span style="background: #ff9900;padding:5px;">Row Highlight</span>
	<span style="background: tomato;padding:5px;">Last Scanned</span>
	<span style="background: rgb(149, 238, 195);padding:5px;">Scanned</span>
</div>

<style>
#grn_color_legends span{display: block;}
.highlightprow{
background:#ff9900;
}
.upd_pmrp_blk,.upd_dp_price_blk {display: block;background: #f1f1f1;font-weight: bold;font-size: 10px;text-align:center;display: none;padding:2px;}
.unscanned{
	background: #b4defe !important;
}
.bcode_scanned{background: rgb(149, 238, 195) !important;}
.lastScanned{background: tomato !important; }
</style>
<script>
var added_pos=[];
$(function(){

	$(document).keydown(function(e){
		if(e.which==27)
			$("#add_barcode_dialog,#add_imei_dialog").hide();
		return true;
	});

	$("#srch_barcode").keyup(function(e){
		if(e.which==13)
		{
			var scan_bc =  $(this).val();
			$(".barcodereset").removeClass("highlightprow");
			
			$('.lastScanned').removeClass('lastScanned');
			
			var bcodeRow = $(".barcode"+scan_bc);
			
			if(bcodeRow.length==0)
			{
				alert("Product not found on loaded PO");
				return;
			}
			bcodeRow.addClass("highlightprow").removeClass("bcode_scanned").removeClass("unscanned");
			
			if(bcodeRow.length > 1)
			{
				alert(bcodeRow.length+" Products found with same barcode");
			}
			
			bcodeRow.addClass("lastScanned");
			
			setTimeout(function(){
				$(".barcode"+scan_bc).addClass("bcode_scanned");
			},500);
			
			
			$(".barcode"+$(this).val()+' .scan_pbarcode').val($(this).val());
			$(document).scrollTop($(".barcode"+$(this).val()).offset().top);
		}
	});
	
	$(".static_pos a").click(function(){
			$("td",$($(this).parents("td").get(0)).parent()).hide();
			$($(this).parents("td").get(0)).show();
			vid=$("span.vid",$(this).parents("td").get(0)).text();
			$("#grn_vendor").val(vid).attr("disabled",true);
			$("#grn_po_load").attr("disabled",true);
	});

	$("#apply_grn_form input").live("keydown",function(e){
		if(e.which==13)
		{
			e.stopPropagation();
			e.preventDefault();
			return false;
		}
	});

	$('#apply_grn_form .prod_mrp').live('keyup',function(){
		
		var pmrp = $(this).attr('pmrp')*1;
		var upd_mrp_blk = $(this).parent().find('.upd_pmrp_blk');
			if($(this).attr('pmrp')*1 != $(this).val()*1 && $(this).val() != ""){
				$('.upd_pmrp',upd_mrp_blk).attr('checked',true);
				upd_mrp_blk.show();
			}
			else
			{
				$('.upd_pmrp',upd_mrp_blk).attr('checked',false);
				upd_mrp_blk.hide();	
			}
	});
	
	$('#apply_grn_form .prod_mrp').live('blur',function(){
		var upd_mrp_blk = $(this).parent().find('.upd_pmrp_blk');
		var pmrp_ele = $(this);
			if(pmrp_ele.val() != "")
			{
				var pmrp = $(this).val()*1;
				
				var dp_price_ele = $(this).parents('tr:first').find('.dp_price');
					if(dp_price_ele.length){
						dp_price = dp_price_ele.val()*1;
						if(pmrp < dp_price)
						{
							alert("MRP cannot be less than DP Price");
							pmrp_ele.select();
						}
					}
			}else
			{
				$('.upd_pmrp',upd_mrp_blk).attr('checked',false);
				upd_mrp_blk.hide();	
			}
	});
	
	$('#apply_grn_form .dp_price').live('keyup',function(){
		
		var dp_price = $(this).attr('dp_price')*1;
		var upd_dp_price_blk = $(this).parent().find('.upd_dp_price_blk');
			if($(this).attr('dp_price')*1 != $(this).val()*1 && $(this).val() != ""){
				$('.upd_dp_price',upd_dp_price_blk).attr('checked',true);
				upd_dp_price_blk.show();
			}
			else
			{
				$('.upd_dp_price',upd_dp_price_blk).attr('checked',false);
				upd_dp_price_blk.hide();	
			}
	});
	
	$('#apply_grn_form .dp_price').live('blur',function(){
		var upd_dp_price_blk = $(this).parent().find('.upd_dp_price_blk');
		var dp_price_ele = $(this);
		if(dp_price_ele.val() != "")
		{
			var dp_price = $(this).val()*1;
			var mrp_ele = $(this).parents('tr:first').find('.prod_mrp');
				if(mrp_ele.length){
					pmrp = mrp_ele.val()*1;
					if(pmrp < dp_price)
					{
						alert("DP Price cannot be more than MRP");
						dp_price_ele.select();
					}
				}
		}else
		{
			$('.upd_dp_price',upd_dp_price_blk).attr('checked',false);
			upd_dp_price_blk.hide();
		}
	});
	
	var chk_for_vendor_ids = 1;
	$("#apply_grn_form").submit(function(){
		flag=true;
		if($(".prod_addcheck").length==0)
		{
			alert("Please Load a PO");
			flag=false;
			return flag;
		}
		var stkloc_flag = true;
		$('.stkloc',this).each(function(){
			if($(this).val() == "")
			{
				stkloc_flag=false;
			}
		});

		if(!stkloc_flag)
		{
			alert("Please choose rackbin");
			return false;
		}
		
		var mrp_flag = true;
		$('.prod_mrp',this).each(function(){
			if($(this).val() == "" || $(this).val() == "0" || isNaN($(this).val()))
			{
				mrp_flag=false;
			}
		});

		if(!mrp_flag)
		{
			alert("Please enter valid mrp details");
			return false;
		}
			
		
		$(".invno,.invdate,.invamount").each(function(){
			if($(this).val().length==0)
			{
				alert("Enter invoice details");
				flag=false;
				return false;
			}
		});
		
		$('input[name="invno[]"],input[name="invdate[]"],input[name="invamount[]"]',this).each(function(){
			if($(this).val().length==0)
			{
				alert("Enter valid invoice details");
				flag=false;
				return false;
			}
		});
		
		
		
		
		
		$(".imeis").each(function(){
			o=$(this);
			p=$($(this).parents("tr").get(0));
			if(o.val().length==0 && $(".rqty",p).val()!="0")
			{
				alert("Serial no is required for '"+$(".name",p).text()+"'");
				flag=false;
				return false;
			}
			if(o.val().length==0)
				return;
			imeis=$(".imeis",p).val().split(",");
			rqty=$(".rqty",p).val();
			if(imeis.length!=rqty)
			{
				alert("No of Serial No. entered for '"+$(".name",p).text()+"' is not equal to the receiving qty");
				flag=false;
				if(confirm("Do you want to clear the IMEIs for the product?"))
				{
					$(".imeis",p).val("");
					$(".imei_cont",p).html("");
				}
				return false;
			}
		});
		
		
		if($('#apply_grn_form .unscanned').length)
		{
			alert("Please scan products");
			return false;
		}
		
		if(chk_for_vendor_ids && flag)
		{
			var frm_ele = $(this);
			$.post(site_url+'/admin/check_vendor_invs',$(this).serialize(),function(resp){
				if(resp.error)
				{
					alert(resp.error);
				}else
				{
					chk_for_vendor_ids = 0;
					flag = false;
					frm_ele.submit();
				}
			},'json');
			flag = false;
		}
		if(flag)
			$("#form_submit").attr("disabled",true);
		return flag;
	});
	
	$(".expdate, .datepick").datepicker();
	$("#grn_vendor").attr("disabled",false);
<?php if(isset($po)){?>
	$("#grn_vendor").attr("disabled",true);
	added_pos.push(<?=$po['po_id']?>);
<?php } ?>
	$("#grn_po_load").click(function(){
		$("#po_loading").show();
		$.post('<?=site_url('admin/jx_getpos')?>',{v:$("#grn_vendor").val()},function(d){
			$("#po_loading").hide();
			$("#pending_pos").html(d);
			$("#grn_vendor").attr("disabled",true);
		});
	});
	$("#grn .datagrid .qtychange").live("change",function(){
		$p=$(this).parents("tr").get(0);
		q=parseInt($(".popqty",$p).val())-parseInt($(".rqty",$p).val());
		if(q<0)
			q="("+(q*-1)+")";
		$(".pqty",$p).html(q);
	});
	$("#grn .datagrid .pprice, #grn .datagrid .rqty").live("change",function(){
		calc_rec_value();
	});
	$("#abd_barcode").keydown(function(e){
		if(e.which==13)
		{
			// deprecated for losing logical errors  
			/*
			$.post("<?=site_url("admin/update_barcode")?>",{pid:$('#abd_pid').val(),barcode:$('#abd_barcode').val()},function(resp){
				$("#add_barcode_dialog").prepend();	
			});
			*/
			var chk_prodid = $('#abd_pid').data('prodid');
				
			$('.pbcodecls'+chk_prodid).val($('#abd_barcode').val());
			//$('input[name="pbarcode'+$('#abd_pid').val()+'[]"]').val($('#abd_barcode').val());
			$("#add_barcode_dialog").hide();
			
			if($('#abd_barcode').val())
				$('.pbcodecls'+chk_prodid).parents('tr:first').addClass('bcode_scanned');
			else
				$('.pbcodecls'+chk_prodid).parents('tr:first').removeClass('bcode_scanned');
				
			$('.pbcodecls'+chk_prodid).parents('tr:first').removeClass('lastScanned').removeClass('unscanned');
			
		}
		return true;
	});
	$("#aid_imei").keydown(function(e){
		if(e.which==13)
			add_imei();
		return true;
	});
});

function remove_imei(imei,pid)
{
	c_imei=$(".imei"+pid).val();
	imeis=c_imei.split(",");
	t=imeis;
	imeis=[];
	for(i=0;i<t.length;i++)
		if(imei!=t[i])
			imeis.push(t[i]);
	c=imeis.join(",");
	$(".imei"+pid).val(c);
	$("#aid_imei").val("").focus();
	p=$($(".imei"+pid).parents("tr").get(0));
	h="";
	for(i=0;i<imeis.length;i++)
	{
		h=h+'<span onclick="remove_imei(\''+imeis[i]+'\','+pid+')" class="scn_inp_imei_'+pid+'" style="cursor:pointer;"> '+(i+1)+') '+imeis[i]+'</span>';
	}
	$(".imei_cont",p).html(h).show();
	
	po_id = p.attr('po_id');
	if($('.scn_inp_imei_'+pid,p).length < $('input[name="rqty'+po_id+'[]"]',p).val()*1)
	{
		$('.imei_upd_cntrl_'+pid,p).show();
	}
}

function check_dup_imei(imei)
{
	var imeis=[];
	$(".imeis").each(function(){
		c=$(this).val().split(",");
		imeis=imeis.concat(c);
	});
	if($.inArray(imei,imeis)!=-1)
	{
		alert("This Serial no is already entered");return false;
	}
	return true;
}

function add_imei()
{
	imei=$("#aid_imei").val();
	if(imei.length==0)
		return;
	if(!check_dup_imei(imei))
		return;		
	pid=$("#aid_pid").val();
	
	$.post(site_url+'/admin/jx_chkimeiforgrn',{'imeino':imei,'pid':pid},function(resp){
		if(resp.status == 'error')
		{
			alert(resp.error);
		}else
		{
			c_imei=$(".imei"+pid).val();
			if(c_imei.length==0)
				imeis=[];
			else
				imeis=c_imei.split(",");
			imeis.push(imei);
			c=imeis.join(",");
			$(".imei"+pid).val(c);
			$("#aid_imei").val("").focus();
			p=$($(".imei"+pid).parents("tr").get(0));
			h="";
			for(i=0;i<imeis.length;i++)
			{
				h=h+' <span onclick="remove_imei(\''+imeis[i]+'\','+pid+')" class="scn_inp_imei_'+pid+'" style="cursor:pointer;"> '+(i+1)+') '+imeis[i]+'</span>';
			}
			$(".imei_cont",p).html(h).show();
			
			po_id = p.attr('po_id');
			if($('.scn_inp_imei_'+pid,p).length >= $('input[name="rqty'+po_id+'[]"]',p).val()*1)
			{
				$('.imei_upd_cntrl_'+pid,p).hide();
				$('#add_imei_dialog').hide();
			}else
			{
				$('.imei_upd_cntrl_'+pid,p).show();
			}
			
		}
	},'json')
	
}

function calc_rec_value()
{
	r_total=0;
	$("#grn .datagrid tr").each(function(){
		$p=$(this);
		rqty=parseInt($(".rqty",$p).val());
		rqty=isNaN(rqty)?"0":rqty;
		pprice=parseFloat($(".pprice",$p).val());
		pprice=isNaN(pprice)?"0":pprice;
		r_total+=rqty*pprice;
		
		if(!$.trim($('.scan_pbarcode',this).val()))
		{
			if(rqty == 0)
			{	
				if($(this).hasClass('unscanned'))
				{
					$(this).addClass('revscan').removeClass('unscanned');
				}
			}else
			{
				if($(this).hasClass('revscan'))
				{
					$(this).addClass('unscanned').removeClass('revscan');
				}
			}
		}
		
	});
	$("#value_receiving").html("Rs "+r_total.toFixed(2));
}
var dpi=0,dpe=0;
function cloneinvoice()
{
	dpi++;
	temp=$("#invoice_template tbody tr").html();
	temp=temp.replace(/%dpi%/g,dpi);
	$(".invoice_tab tbody").append("<tr>"+temp+"</tr>");
	$(".datepick"+dpi).datepicker();
}

function loadpo(pid)
{
	if($.inArray(pid,added_pos)!=-1)
	{
		alert("This purchase order already loaded");
		return;
	}
	
	$('#grn_color_legends').show();
	
	$("#po_loading").show();
	$(".vendor_list").hide();
	$(".venl"+$("#grn_vendor").val()).show();
	$.post('<?=site_url('admin/jx_grn_load_po')?>',{p:pid},function(data){
		pois=$.parseJSON(data);
		g_rows="";
		dpes=[];
		$.each(pois,function(i,poi){
			dpe++;
			grow=$("#grn_template .right table tbody").html();
			need_scan = 0;
			update_barcode='';
			if(poi.bcodes.length==0)
				update_barcode="add barcode";
			else
			{
				update_barcode="Update barcode";
				need_scan = 1;
			}
			var add_imei='';
			if(poi.is_serial_required==1)
			{
				add_imei="add serial no.";
				grow=grow.replace(/imeisvvv/g,"imeis");
			}

			var prodbcodes = '';
				if(poi.barcode)
					poi.bcodes.push(poi.barcode);
				if(poi.bcodes.length)
					prodbcodes += poi.bcodes.join(' barcode');
					
			if(need_scan)
				prodbcodes +=' unscanned ';
			
			grow=grow.replace(/%update_barcode%/g,update_barcode);
			grow=grow.replace(/%add_serial%/g,add_imei);
			grow=grow.replace(/%bcode%/g,prodbcodes);
			grow=grow.replace(/%prodid%/g,poi.product_id);
			grow=grow.replace(/%sno%/g,dpe);
			grow=grow.replace(/%pid%/g,pid);
			grow=grow.replace(/%name%/g,'<a href="'+site_url+'/admin/product/'+poi.product_id+'" target="_blank">'+poi.product_name+'</a>');
			grow=grow.replace(/%qty%/g,poi.order_qty);
			
			grow=grow.replace(/%pqty%/g,parseInt(poi.order_qty)-parseInt(poi.received_qty));
			grow=grow.replace(/%po_mrp%/g,poi.mrp);
			
			grow=grow.replace(/%mrp%/g,poi.prod_mrp);
			if(poi.is_serial_required*1)
			{
				grow=grow.replace(/%dp_price%/g,poi.dp_price);
				grow=grow.replace(/%dp_price_inp%/g,'visible');
			}
			else
			{
				grow=grow.replace(/%dp_price%/g,0);
				grow=grow.replace(/%dp_price_inp%/g,'hidden');
			}
				
				
			grow=grow.replace(/%price%/g,poi.purchase_price);
			grow=grow.replace(/%rqty%/g,poi.received_qty);
			
			grow=grow.replace(/%ppur_price%/g,(poi.prod_mrp*poi.purchase_price/poi.mrp));
			
			if(poi.rbs)
				grow=grow.replace(/==rbs==/g,poi.rbs);
			else
				grow=grow.replace(/==rbs==/g,'<option value="10">A11-Default Rack</option>');
			
			grow=grow.replace(/%dpe%/g,dpe);
			offer=foc="NO";
			if(poi.is_foc=="1")
				foc="YES";
			if(poi.has_offer=="1")
				offer="YES";
			grow=grow.replace(/%foc%/g,foc);
			grow=grow.replace(/%offer%/g,offer);
			g_rows=g_rows+grow;
			$(".expdate"+dpe).datepicker();
			dpes.push(".expdate"+dpe);
		});
		$("#grn .datagrid tbody").append(g_rows);
		
		$(dpes.join(", ")).datepicker();
		$("#grn_pids").append('<input type="hidden" name="poids[]" value="'+pid+'">');
		added_pos.push(pid);
		$("#po_loading").hide();
		$("#loadafterselect").show();
		calc_rec_value();
	});
}

function reset_rec_f()
{
	v=parseInt($("#reset_rec").val());
	if(isNaN(v))
	{
		alert("Not a number");return;
	}
	if(confirm("Are you sure want to reset all receiving qty to "+v+" ?"))
		$("#apply_grn_form .rqty").val(v);
	calc_rec_value();
}

function reset_inv_f()
{
	v=parseInt($("#reset_inv").val());
	if(isNaN(v))
	{
		alert("Not a number");return;
	}
	if(confirm("Are you sure want to reset all invoice qty to "+v+" ?"))
		$("#apply_grn_form .iqty").val(v).change();
}

function show_add_imei(e,pid)
{
	x=e.clientX;
	y=e.clientY;
	$("#add_barcode_dialog").hide();
	$("#add_imei_dialog").css("top",y+"px").css("left",x+"px").show();
	$("#aid_imei").focus().val("");
	$("#aid_pid").val(pid);
}

function show_add_barcode(e,pid,prodid)
{
	x=e.clientX;
	y=e.clientY;
	$("#add_imei_dialog").hide();
	$("#add_barcode_dialog").css("top",y+"px").css("left",x+"px").show();
	$("#abd_barcode").focus().val("");
	$("#abd_pid").val(pid);
	$("#abd_pid").data('prodid',prodid);
}


</script>

<div id="add_barcode_dialog">
<input type="hidden" value="" id="abd_pid">
Enter Barcode : <input type="text" class="inp" style="width:200px;" id="abd_barcode">
</div>

<div id="add_imei_dialog">
<input type="hidden" value="" id="aid_pid">
Enter IMEI No : <input type="text" class="inp" style="width:200px;" id="aid_imei">
</div>

<style>
#add_barcode_dialog,#add_imei_dialog{
position:fixed;
top:0px;
left:0px;
display:none;
padding:5px;
background:#eee;
border:1px solid #f90;
}
.edate{
width:70px;
}
#grn{
margin-left:10px;
background:#eee;
padding:5px;
}
.grn_po{
background:#eee;
padding-top:5px;
}
.grn_po .datagrid, #grn .datagrid{
width:100%;
}
.imei_cont{
max-width:150px;
overflow:auto;
margin:0px -7px;
background:#eee;
padding:2px;
font-size:80%;
display:none;
}
.imei_cont span{
display:block;
background:#f90;
padding:0px 2px;
margin:3px;
}
</style>

<?php
