<div class="container" >

<div style="margin-bottom:0px;margin-top: 40px;" class="heading">
<div class="headingtext container">
Offline orders </div>
<p style="margin: 2px;font-size: 11px;color: #000">
	Make / Place orders from this below form 
</p>
</div>
 

<br />
 
<table id="offline_orders_frm" width="70%" cellpadding="0" cellspacing="0">
	<tr>
		 <td valign="top" width="450">
			<div style="margin-bottom:20px;width: 300px;">
				<form method="post" id="searchlive">
					<b style="font-size: 14px;">Search for items/products:</b> <br />
					<input type="text" class="barcode" name="barcode" style="width:200px;padding:3px;">
					<input type="submit" value="Submit" style="background: #FFED00;color: #000;font-weight: bold;padding:3px;">
					<input type="button"  id="clear_search_results_button" value="Clear  Results" style="background: #FFED00;color: #000;font-weight: bold;padding:3px;display: none;">
				</form>
				<div id="search"></div>
				 
					
				 
			</div>
		</td>
		<td valign="top" style="padding-right: 10px;">
		
		
				
				
			<form method="post" style="width: 800px;" >
				<input class="couponfrm" name="coupon" type="hidden">
				
				<div id="cartform">
				
				
				
				
				
				
				<h3 style="margin: 5px 0px 0px 0px;">List Of Items</h3>
				<table class="stock_up" width="100%" cellpadding=5 cellspacing="0">
					<tr>
						<th>Item Name</th>
						<th style="display:none;">ID</th>
						<th>MRP</th>
						<th>Offer Price</th>
						<th>Qty</th>
					</tr>
					<tr id="clone" style="display:none">
						<td>
							<input type="hidden" class="itemid" name="itemid[]">
							<input type="text" class="itemname" readonly="readonly" style="width:300px;font-size:9px;padding:5px;"><img src="<?=base_url()?>images/loading.gif" style="margin-left:-26px;position:absolute;margin-bottom:-3px;display:none;">
						</td>
						<td style="display:none;">
						<input type="text" size="12" class="itemiddisp" readonly="readonly">
						</td>
						<td style="">
							<input type="text" size="6" class="itemmrp" readonly="readonly">
						</td>
						<td style="">
						<input type="text" size="10" class="itemprice" readonly="readonly">
						</td>
						<td style="">
							<select class="itemqty" name="qty[]">
							<?php for($i=1;$i<=5;$i++){?>
							<option value="<?=$i?>"><?=$i?></option>
							<?php }?>
							</select>
						</td>
						<td><a href="javascript:void(0)" class="remove">remove</a></td>
					</tr>
					
					
					 
					
				</table>
				<br />
				<table width=100%>
					<tr>
						<td align="left">	<b>COD Charge</b> <br /> 
							<input type="text" size=8 name="cod"></td>
					 
						<td> <b>Coupon Code</b><br />
							<input type="text" name="coupon" class="coupon">
							<input type="button" class="sbutton" value="Apply" id="applycoupon">
							<input type="button" class="sbutton" value="clear" id="clearcoupon">	
						</td>
						<td><b>Total MRP</b><br /><input type="text" size=10 id="t_mrp" readonly="readonly"></td>
					 
						<td><b>Discount</b><br /><input type="text" size=10 id="t_disc" readonly="readonly"></td>
					 
						<td><b>Order value</b><br /><input type="text" size=10 id="t_price" readonly="readonly"></td>
					 
						
					</tr>
				</table>
				
				</div>
				
				
				 
				
				
				<div id="coupondetails" style="display:none;">
				<img src="<?=IMAGES_URL?>loading.gif" id="couponloading">
			
				<table >
				<tr> 
				<td>Order value before coupon :</td><td><input type="text" size="10" id="t_bvalue" readonly="readonly"></td>
				</tr>
				<tr>
				<td>Coupon discount : </td><td><input type="text" size="10" id="t_cdisc" readonly="readonly"></td>
				</tr>
				<tr>
				<td>Order value after coupon :</td><td><input type="text" size="10" id="t_avalue" readonly="readonly"></td>
				</tr>
				 
				 
				</table>
				</div>
				
				<div style="border: 1px solid #e3e3e3;">
					<h3 style="margin: 5px 0px;">Customer Details</h3>
				<table style="background: #FFF;" cellpadding="0" cellspacing="10" width="100%">
				<tr>
				<td colspan="100%" style="color:#777;">If already registered, address will be auto-loaded</td></tr>
				<tr>
					<td>Email : </td>
					<td><input type="text" class="inputbox" name="email" id="email_inp"></td>
					<td>Name :</td>
					<td><input type="text" id="name_inp" class="inputbox" name="person"></td>
				</tr>
				
				<tr>
					<td>Address :</td>
					<td><textarea name="address" id="address_inp" class="inputbox" ></textarea></td>
					<td>Landmark :</td>
					<td><input type="text" id="landmark_inp" class="inputbox" name="landmark"></td>
				</tr>
				
				<tr>
					<td>City :</td>
					<td><input type="text" id="city_inp" class="inputbox" name="city"></td>
					<td>State :</td>
					<td><input type="text" id="state_inp" class="inputbox" name="state"></td>
				</tr>
				
				
				<tr>
					<td>Pincode :</td>
					<td><input type="text" id="pincode_inp" class="inputbox" name="pincode"></td>
				</tr>
				
				<tr>
					<td>Mobile :</td>
					<td><input type="text" id="mobile_inp" class="inputbox" name="mobile"></td>
					<td>Telephone :</td>
					<td><input type="text" id="telephone_inp" class="inputbox" name="telephone"></td>
				</tr>
				<tr>
				<td colspan=3><input type="checkbox" name="nomail">Don't send order confirmation mail</td>
				</tr>
				
				
				</table>
				</div>
				
				<div align="right" style="margin:20px 0px;">
					<input type="submit" value="Place an Order" style="background: #FFED00;color: #000;font-weight: bold;padding:3px;">
				</div>
				
				</form>
		</td>
		
	 </tr>
</table>











</div>

<style>
#search{
	display:none; 
	width:269px;
	background:#fff;
	padding:3px;
	max-height:350px;
	overflow:auto;
}
#search a{
display:block;
padding:5px;
color:#000;
text-decoration:none;
}
#search a:hover{
background:blue;
color:#fff;
}
.stock_up{
	background:#FFF;
}
.stock_up th{
	background:#ccc;
}
.stock_up td{
	background:#e3e3e3;
}
.item{
	border-bottom:1px dotted #e3e3e3;
	font-size:12px;
}
#offline_orders_frm h3{
	 
	padding:5px 10px;
	 
	font-size:15px;
}
.inputbox{
	padding:3px;
	width:200px;
}
.sbutton{
	background: #FFED00;color: #000;font-weight: bold;padding:3px;
}
</style>

<script>
var obj,srcht;
var deals=[];
function clone()
{
	c=$(".stock_up").append("<tr>"+$("#clone").html()+"</tr>");
	c=$(".stock_up tr:last");
	bindr();
	return c;
}
function bindr()
{
	$(".remove").unbind("click");
	$(".remove").click(function(){
		$(this).parent().parent().remove();
	});
}


function seldeal(i)
{
	id=deals[i].itemid;
	mrp=deals[i].orgprice;
	price=deals[i].price;
	name=deals[i].itemname;
	
	obj=clone();
	obj=$(".itemname",obj);
	$(".itemid",obj.parent()).val(id);
	$(".itemmrp",obj.parent().parent()).val(mrp);
	$(".itemprice",obj.parent().parent()).val(price);
	$(".itemiddisp",obj.parent().parent()).val(id);
	obj.val(name);
	$(".itemqty",obj.parent().parent()).val("1").focus();

	
	
}
var addrloaded=false;

$(function(){

	$("#email_inp").blur(function(){
		em=$(this).val();
		if(em.length==0 || addrloaded)
			return;
		$.post("<?=site_url("admin/jx_getshipaddr")?>",{email:em},function(data){
			if(!data || data.length==0)
				return;
			data=$.parseJSON(data);
			addrloaded=true;
			$("#name_inp").val(data.name);
			$("#address_inp").val(data.address);
			$("#landmark_inp").val(data.landmark);
			$("#city_inp").val(data.city);
			$("#state_inp").val(data.state);
			$("#pincode_inp").val(data.pincode);
			$("#telephone_inp").val(data.pincode);
			$("#mobile_inp").val(data.mobile);
			$("#telephone_inp").val(data.telephone);
		});
	});

	$("#clearcoupon").click(function(){
		$(".coupon,.couponfrm").val("");
		$("#cartform").show();
		$("#coupondetails,#couponloading").hide();
	});
	
	$("#date").datepicker({dateFormat:'dd-mm-yy'});
//	clone();
	$("#searchlive").submit(function(){
		deals=[];
		$("#search").html('<img src="<?=IMAGES_URL?>loading.gif">');
		$.post('<?=site_url("admin/jx_searchlive")?>',"p="+$("input",$(this)).val(),function(data){
			deals=$.parseJSON(data);
			$("#search").html("");
			h="";
			$.each(deals,function(i){
				h+='<div class="item"><a href="javascript:void(0)" onclick="seldeal('+i+')">'+this.itemname+'</a></div>';
			});
			$("#search").html(h).show();

			if($("#search div").length){
				$('#clear_search_results_button').show();
			}else{
				$('#clear_search_results_button').hide();
				$("#search").html('<b>No Items found for your search</b>');
			}	
			
		});
		return false;
	});

	$('#clear_search_results_button').click(function(e){
		$("#search").html('').hide();
		$(this).hide(); 
	});
	
	window.setTimeout(calc,1000);
	$("#applycoupon").click(function(){
		if($(".coupon").val().length==0)
		{
			alert("enter a coupon!!!");
			return;
		}
		if($(".itemid").parent().filter(":not(:hidden)").length==0)
		{
			alert("no products");return;
		}
		$("#coupondetails,#couponloading").show();
		iids=[];
		qtys=[];
		$(".itemid").each(function(){
			if($(this).parent().parent().filter(":hidden").length!=0)
				return;
			qtys.push($(".itemqty",$(this).parent().parent()).val());
			iids.push($(this).val());
		});
		pst={
				ids:iids.join(","),
				qty:qtys.join(","),
				c:$(".coupon").val()
		};
		//$("#cartform").hide();
		$(".couponfrm").val($(".coupon").val());
		$.post("<?=site_url("admin/jx_applycoupon")?>",pst,function(data){
			$("#couponloading").hide();
			p=$.parseJSON(data);
			if(!p.apply)
			{
				alert(p.msg);
				$("#clearcoupon").click();
				return;
			}
			alert(p.msg);
			$("#t_bvalue").val(p.btotal);
			$("#t_cdisc").val(p.disc);
			$("#t_avalue").val(p.atotal);
		});
	});
});
function calc(){
	t_mrp=t_price=0;
	$(".itemmrp").each(function(){
		t=parseInt($(this).val())*parseInt($(".itemqty",$(this).parent().parent()).val());
		if(!isNaN(t))
		t_mrp+=t;
	});
	$(".itemprice").each(function(){
		t=parseInt($(this).val())*parseInt($(".itemqty",$(this).parent().parent()).val());
		if(!isNaN(t))
		t_price+=t;
	});
	$("#t_mrp").val(t_mrp);
	$("#t_disc").val(t_mrp-t_price);


	var cod_charge = $('input[name="cod"]').val()*1;
	$("#t_price").val(t_price+cod_charge);
	window.setTimeout(calc,500);
}
</script>
<?php
