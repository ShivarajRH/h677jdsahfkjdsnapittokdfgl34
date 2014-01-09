<?php
//exit;
$e=null; 
if(isset($is_edit))
{
	$e=true;
	$d=$dealdetails;
	$slots=unserialize($d->slots);
	$nslots=array();
	$nslotprice=array();
	if(!empty($slots))
	foreach($slots as $no=>$rs)
	{
		$nslots[]=$no;
		$nslotprice[]=$rs;
	}
	$sizing_rw=explode(":",$dealdetails->sizing);
	$sizing_type=array_shift($sizing_rw);
	$sizing=array_pop($sizing_rw);
}
?>
<?php $user=$this->session->userdata("admin_user");?>
<!-- Skin CSS file -->
<link rel="stylesheet" type="text/css" href="<?=base_url()?>js/yahoo/skin.css">
<!-- Utility Dependencies -->
<script src="<?=base_url()?>js/yahoo/yahoo-dom-event.js"></script> 
<script src="<?=base_url()?>js/yahoo/element-min.js"></script> 
<!-- Needed for Menus, Buttons and Overlays used in the Toolbar -->
<script src="<?=base_url()?>js/yahoo/container_core-min.js"></script>
<script src="<?=base_url()?>js/yahoo/menu-min.js"></script>
<script src="<?=base_url()?>js/yahoo/button-min.js"></script>
<!-- Source file for Rich Text Editor-->
<script src="<?=base_url()?>js/yahoo/editor-min.js"></script>
 
<script>
var submit_form = 1;
function fcpcalc()
{
	t=0;
	$("#nlc,#phc,#shc").each(function(){
		if(t==-432)
			return;
		if(is_natural($(this).val()))
			t+=parseInt($(this).val());
		else if($(this).val().length!=0)
			t=-432;
	});
	if(t!=-432)
	$("#fcp").val(t);
	else
		$("#fcp").val("0");
}
$(function(){

	$("input[name=slot1],input[name=slot2],input[name=slot3],input[name=slot4]").change(function(){
		i=parseInt($(this).attr("name").charAt(4))+1;
		c=i-1;
		if(c>1)
		{
			if(parseInt($(this).val())-1<=parseInt($("input[name=slot"+parseInt(c-1)+"]").val()))
			{
				alert("quantity is lesser than previous slot or same as current slot!!!");
				$(this).val("").focus();
			}
		}
		if(!isNaN(parseInt($(this).val())))
		{
			$("."+$(this).attr("name")).html(parseInt($(this).val())+1);
			$("."+$(this).attr("name")+"price").attr('readonly',false);
			$("input[name=slot"+i+"price],input[name=slot"+i+"]").attr('readonly',false);
		}
		else{
			$("."+$(this).attr("name")).html("na");
			$("."+$(this).attr("name")+"price").val("");
			for(i=parseInt($(this).attr("name").charAt(4))+1;i<5;i++)
			{
				$(".slot"+i).html("na");
				$("input[name=slot"+i+"price],input[name=slot"+i+"]").val("").attr('readonly',true);
			}
		}
		if(isNaN(parseInt($(this).val())) && $(this).val()!="")
		{
			alert("enter a number");$(this).val("").focus();
		}
			
	}).change();
<?php if(!$e){?>
	$("input[name=slot1price],input[name=slot2price],input[name=slot3price],input[name=slot4price]").val("");
<?php } ?>
	$("#fcpcheck").attr("checked",false);
	$("#nlc,#phc,#shc").keyup(function(){
		if($(this).attr("readonly")==true)
			return;
		fcpcalc();
	});
	$("#nlc,#phc,#shc").change(function(){
		if($(this).attr("readonly")==true)
			return;
		fcpcalc();
	});
	$("#nlc,#phc,#shc").blur(function(){
		if($(this).attr("readonly")==true)
			return;
		fcpcalc();
	});
	$("#fcpcheck").change(function(){
		if($(this).attr("checked")==true)
		{
			$("#nlc,#phc,#shc").val("").attr("readonly",true);
			$("#fcp").attr("readonly",false);
		}
		else
		{
			$("#nlc,#phc,#shc").attr("readonly",false);
			$("#fcp").val("").attr("readonly",true);
		}
	});
	$("input[name=dealtype] option:first-child").attr("checked",true);
	$(".nolimit").change(function(){
		if($(this).attr("checked")==true)
			$("input[type=text]",$(this).parent().parent()).attr("disabled",true);
		else
			$("input[type=text]",$(this).parent().parent()).attr("disabled",false);
	});
	$("input[name=dealtype]").change(function(){
		if($(this).val()=="brandsale")
		{
			$("#qlab").html("Quanity");
			$("input[name=quantity]").attr("disabled",false);
			$("#qcheck").show();
			$(".nolimit").attr("checked",false);
		}
		else
		{
			$("#qlab").html("Activation Threshold");
			$("input[name=quantity]").attr("disabled",false);
			$("#qcheck").hide();
		}
	});
	$("#startdatepicker, #enddatepicker,#fstartdatepicker, #fenddatepicker").datepicker({showOn: 'both', dateFormat: 'yy-mm-dd' ,minDate: new Date(<?=date("Y")?>,<?=date("n")?>-1,<?=date("j")?>), buttonImage: '<?=base_url()?>images/calendar_old.png', buttonImageOnly: true});
//	$("#categoryname").change(function(){
//		$.get("<?=site_url("admin/jx_getbrand/")?>/"+$(this).val(),function(da){
//			$("#brandsel").html(da);
//		});
//	});

	$("#adddealform").submit(function(){
		msg="";
		if($("#categoryname").val()==0)
			msg+="<div>Choose category</div>";
			if($("#tagline").val().length==0)
				msg+="<div>Enter Item Name</div>";
				if($("#itemcode").val().length==0)
					msg+="<div>Enter Item Code</div>";
		if(!is_natural($("#fcp").val()))
			msg+="<div>Enter Fixed Cost Price as number</div>";
		if(!is_natural($("#mrp").val()))
			msg+="<div>Enter MRP as number</div>";
		if(!is_natural($("#price").val()))
			msg+="<div>Enter Offer Price as number</div>";
		if(!is_natural($("#viaprice").val()))
			msg+="<div>Enter VIA Price as number</div>";
		if(!is_natural($("#rsp").val()))
			msg+="<div>Enter Recommended Selling Price as number</div>";
		if($("#startdatepicker").val().length==0)
			msg+="<div>Enter start date</div>";
		if($("#enddatepicker").val().length==0)
			msg+="<div>Enter end date</div>";
		if(msg!="")
		{	$("#error").html(msg);
		alert("Please correct the errors shown above");
		return false;}
		return true;
	});
	});
function disable()
{
	if ($('#toggleElement').is(':checked')) {
	    $('#uploadpic').removeAttr('disabled');
	} else {
		 $('#uploadpic').attr('disabled', true);
	}  
}
var myEditor,ed;
(function() {
    //Setup some private variables
    var Dom = YAHOO.util.Dom,
        Event = YAHOO.util.Event;

        //The SimpleEditor config
        var myConfig = {
            height: '150px',
            width: '650px',
            dompath: true,
            handleSubmit: true
        };

        var myConfig2 = {
            height: '150px',
            width: '650px',
            dompath: true,
            handleSubmit: true
        };

        myEditor = new YAHOO.widget.Editor('bdescription', myConfig);
        myEditor.render();
        ed = new YAHOO.widget.Editor('description', myConfig2);
        ed.render();
        
})();

</script>
<?php 
if(isset($is_edit))
{
	echo '<script>';
	echo '$(function(){$("#uploadpic").attr("disabled",true);});';
	echo '</script>';
}
?>
<style>
#link_products_cont{
margin-top:20px;
}
#link_products_cont table{
width:400px;
}
.ui-datepicker{
font-size:10px;
}
#main{
font-family: arial;
font-size: 11px;
color:#000000;
}
.maindiv{
width: 750px;
margin-top: 40px;
}
<?php if(isset($is_edit)){?>
.innerdiv{
width: 500px;
float:left;
clear: both;
}
<?php } else {?>
.innerdiv{
width: 500px;
clear: both;
float:left;
}
<?php }?>
.innerdiv label{
font-weight: bold;
font-size: 13px;
}
.span{
width:150px;
float: left;
margin-bottom: 10px;
text-align:justify;
}
.span1{
width:350px;
float: left;
margin-bottom: 10px;
text-align:justify;
}
.innerdiv input{
border:1px solid #9BCD9B;
#background:#EBF8DC;
width:300px;
padding:5px;
}
.innerdiv textarea{
border:1px solid #9BCD9B;
#background: :#EBF8DC;
}
.innerdiv select{
padding:5px;
-moz-border-radius:5px;
border:1px solid #9BCD9B;
#background: #EBF8DC;
}
.amenities
{
float:left;
}
.forminfo{
 color:#999;font-size:10px;
 }
.mark{
color:#f00;
font-size:12px;
}
.form{
background: url(<?=base_url()?>images/title-bg-top-small.gif) repeat-x scroll left top;
border:#CCCCCC 1px solid;
-moz-border-radius:10px;
float: left;
padding: 10px;
margin-left: 120px;
}
</style>
<?php
$e=null; 
if(isset($is_edit))
{
	$e=true;
	$d=$dealdetails;
?>
<div class="heading" style="margin-bottom:20px;">
<div  class="headingtext container">Edit deal</div>
</div>
<?php }else{?>
<div class="heading" style="margin-bottom:20px;">
<div class="headingtext container">Add new deal
<div style="float:right; font-family:'trebuchet ms';font-size:18px;color:#ffffff;margin-top: 5px;" >Step 1</div>
</div>
</div>
<?php }?>
<div id="main" class="container yui-skin-sam" style="padding:0px">
<?php 
//print_r($dealdetails);
if(isset($dealdetails)){
$catid=$dealdetails->catid;
//echo $catid;
//echo date($dealdetails->startdate);
$selstartdate=date("G",$dealdetails->startdate);
$selenddate=date("G",$dealdetails->enddate);
/*print_r($selstartdate);
print_r($selenddate);exit;*/
}
?>
<div align="left" class="form_error" style="color: red;margin-left: 20px;"><?=validation_errors("<div>","</div>")?>
		<?php if(isset($autherror))
		{
		echo $autherror;
		}?>
</div>
<div style="margin-left:20px;text-align:left;color: red;" id="error"><?php if(isset($error)){?>
<?=$error?>
<?php }?>
</div>

<form id="adddealform" action="<?php 
if(isset($is_edit))
echo site_url('admin/updatedeal');
else
echo site_url('admin/finishdeal');?>" enctype="multipart/form-data" method="post">

<div class="tabs">
<ul>
<li><a href="#deal_details">Deal Details</a></li>
<li><a href="#product_details">Link<?=$e?"ed":""?> Products</a></li>
<li><a href="#descriptions">Descriptions</a></li>
<li><a href="#groupbuying">Group Buying</a></li>
<li><a href="#dealconfig">Config</a></li>
</ul>

<div id="deal_details">
<div class="form" style="width:97%;margin:10px;">

<input type="hidden" name="dealid" value="<?php if(isset($dealdetails)) echo $dealdetails->dealid?>">
<input type="hidden" name="brandid" value="<?=$brandid?>">
<input type="hidden" name="catid" value="<?php if(isset($dealdetails)) echo $dealdetails->catid?>">

<table style="clear:both" cellspacing=7 width="100%">
<tr><td colspan="4"><h3>Category details</h3></td></tr>
<tr>
<td>
<div class="itcont">
<div class="head"><label>Menu<span class="mark">*</span></label></div>
<div class="cont"><select name="menu" class="inp">
<?php foreach($menu as $m){?>
	<option value="<?=$m['id']?>" <?php if(isset($dealdetails) && $dealdetails->menuid==$m['id']) echo ' selected'?>><?=$m['name']?></option>
<?php } ?>
</select>
</div>
</div>
</td>
<td>
<div class="itcont">
<div class="head"><label>Secondary Menu</label></div>
<div class="cont"><select name="menu2" class="inp">
<option value=0>none</option>
<?php foreach($menu as $m){?>
	<option value="<?=$m['id']?>" <?php if(isset($dealdetails) && $dealdetails->menuid2==$m['id']) echo ' selected'?>><?=$m['name']?></option>
<?php } ?>
</select>
</div>
</div>
</td>
<td>
<?php if(isset($dealdetails)){?>
<div class="itcont">
<div class="head"><label>Category<span class="mark">*</span></label></div>
<div class="cont">
<select name="categoryname" id="categoryname" style="">
<option value="0">----Select----</option>
<?php 
foreach($adm_categories as $category){
?>
<option value="<?=$category->catid?>" <?php if($d->catid==$category->catid) echo 'selected';?>><?=$category->name?></option>
<?php }?>
</select>
</div>
</div>
</td>
<td>
<div class="itcont">
<div class="head"><label>Brand<span class="mark">*</span></label></div>
<div class="cont" id="brandsel">
<select name="brandname">
<?php foreach($brands as $b){?>
<option value="<?=$b['id']?>" <?php if($d->brandid==$b['id']) echo "selected";?>><?=$b['name']?></option>
<?php }?>
</select>
</div>
</div>
<div id="subcat" class="innerdiv" style="display: none;">
</div>
<?php } else {?>
<div class="itcont">
<div class="head"><label>Category<span class="mark">*</span></label></div>
<div class="cont">
<select name="categoryname" id="categoryname" style="">
<option value="0">----Select----</option>
<?php 
foreach($adm_categories as $category){
?>
<option value="<?=$category->catid?>"><?=$category->name?></option>
<?php }?>
</select>
</div>
</div>
</td>
<td>
<div class="itcont">
<div class="head"><label>Brand<span class="mark">*</span></label></div>
<div class="cont" id="brandsel">
<select name="brandname">
<?php foreach($this->db->query("select * from king_brands order by name asc")->result_array() as $b){?>
<option value="<?=$b['id']?>" <?php if($d->brandid==$b['id']) echo "selected";?>><?=$b['name']?></option>
<?php }?>
</select>

</div>
</div>
<div id="subcat" class="innerdiv" style="display: none;">
</div>
<?php }?>
</td>


</tr>

<tr><td colspan="4"><h3>Cost details</h3></td></tr>

<tr style="display:none">
<td>
<div class="itcont">
<div class="head"><label>Shipping Cost</label></div>
<div class="cont">Rs. <input type="text" name="shc" style="width:70px;text-align: right;" id="shc" value="<?php if($e) echo $d->shc; else echo $this->input->post("shc")?>"></div>
</div>
</td>
<td>
<div class="itcont">
<div class="head"><label>Fixed Cost Price<span class="mark">*</span></label></div>
<div class="cont">Rs. <input readonly type="text" name="fcp" style="width:70px;text-align: right;" id="fcp" value="<?php if($e) echo $d->fcp; else if($_POST) echo $this->input->post("fcp"); else echo '200';?>">
<label><input style="width:auto;min-width:0;" type="checkbox" id="fcpcheck"><br>I know this only</label>
</div>
</div>
</td>
</tr>
<tr style="display:none">
<td>
<div class="itcont">
<div class="head"><label>Price for SNP<span class="mark">*</span></label></div>
<div class="cont">Rs. <input type="text" name="viaprice" style="width:70px;text-align: right;" id="viaprice" value="<?php if($e) echo $d->viaprice; else echo "100";?>"></div>
</div>
</td>
<td>
<div class="itcont">
<div class="head"><label>Recommended SP<span class="mark">*</span></label></div>
<div class="cont">Rs. <input type="text" name="rsp" style="width:70px;text-align: right;" id="rsp" value="<?php if($e) echo $d->rsp; else echo "200";?>"></div>
</div>
</td>
</tr>
<tr>
<td>
<div class="itcont">
<div class="head"><label>Product Cost</label></div>
<div class="cont">Rs. <input type="text" name="nlc" class="inp" style="width:70px;text-align: right;" id="nlc" value="<?php if($e) echo $d->nlc; else echo $this->input->post("nlc")?>"></div>
</div>
</td>
<td>
<div class="itcont">
<div class="head"><label>Packaging & Hldg Cost</label></div>
<div class="cont">Rs. <input type="text" name="phc" class="inp" style="width:70px;text-align: right;" id="phc" value="<?php if($e) echo $d->phc; else echo $this->input->post("phc")?>"></div>
</div>
</td>
<td>
<div class="itcont">
<div class="headimp"><label>MRP<span class="mark">*</span></label></div>
<div class="cont">Rs. <input type="text" name="mrp" <?php echo ($this->uri->segment(2)=='edit')?"readonly":''?> class="inp" style="width:70px;text-align: right;" id="mrp"  value="<?php if($e) echo $d->orgprice; else echo $this->input->post("mrp")?>">
</div>
</div>
</td>
<tD>
<div class="itcont">
<div class="headimp"><label>Offer Price<span class="mark">*</span></label></div>
<div class="cont">Rs. <input type="text" name="price" class="inp" style="width:70px;text-align: right;"  id="price" value="<?php if($e) echo $d->price; else echo $this->input->post("price")?>"></div>
</div>
</tD>
</tr>

<tr style="display:none;">
<td>
<div class="itcont">
<div class="head"><label>Service tax<span class="mark">*</span></label></div>
<div class="cont"><input type="text" name="service_tax" style="width:70px;text-align: right;" id="service_tax" value="<?php if($e) echo $d->service_tax/100; else if($_POST) echo $this->input->post("service_tax"); else echo "10.3"?>">%</div>
</div>
<input type="hidden" name="service_tax_cod" id="service_tax_cod" value="10.3">
</td>
<?php /*?>
<td>
<div class="itcont">
<div class="head"><label>Service tax (if COD)<span class="mark">*</span></label></div>
<div class="cont">Rs. <input type="text" name="service_tax_cod" style="width:70px;text-align: right;" id="service_tax_cod" value="<?php if($e) echo $d->service_tax_cod; else echo $this->input->post("service_tax_cod")?>"></div>
</div>
</td>
*/?>
</tr>

 
 

 


<tr><td colspan="3"><h3>Deal details</h3></td><td><h3>Tax Details</h3></td></tr>
<tr>
<td colspan="2">
<div class="itcont">
<div class="headimp"><label>Deal Name<span class="mark">*</span></label></div>
<div class="cont"><input class="inp" style="width:300px;" name="tagline" id="tagline" value="<?php if(isset($dealdetails)) echo $dealdetails->tagline; else echo $this->input->post('tagline');?>">
<div class="forminfo">Eye-catching short line for your deal</div></div>
</div>
</td>
<td>
<div class="itcont">
<div class="headimp"><label>Profile Pic<span class="mark">*</span></label></div>
<?php if(isset($dealdetails)){?>
<div style="margin-top: 3px;margin-right:20px;float:left;">
<img style="width: 75px;height: 75px;" src="<?=base_url().'images/items/'.$dealdetails->pic.'.jpg'?>">
</div>
<?php }?>
<div class="cont"><?php if(isset($is_edit)){?><div><label style="font-weight: normal;"><input id="toggleElement" onclick="disable()" style="min-width:0;width:auto;" type="checkbox" name="check"> Check here to Update image</label></div><?php }?><input id="uploadpic" type="file" name="pic" size=1></div>
</div>
</td>
<td>
<div class="itcont">
<div class="head"><label>Sales Tax<span class="mark">*</span></label></div>
<div class="cont"><input type="text" name="tax" class="inp" style="width:70px;text-align: right;" id="tax" value="<?php if($e) echo $d->tax/100; else if($_POST) echo $this->input->post("tax"); else echo "14";?>">%</div>
</div>
</td>

</tr>

<tr>
<td colspan=1>
<div class="itcont">
<div class="head"><label>Deal Start Date<span class="mark">*</span></label></div>
<div class="cont"><select name="starthrs" id="starthrs">
	<?php 	for($i=0;$i<24;$i++)	{	?>
	<option <?php if(isset($dealdetails) && $selstartdate==$i) echo "selected"?> value="<?=$i?>"><?=date("g a",mktime($i))?></option>
	<?php }?>
		<option value="24">Midnight</option>
	</select>
	<input id="startdatepicker" class="inp" style="width:80px;margin-left: 6px;" type="text" name="startdate" id="startdate" value="<?php if(isset($dealdetails)) echo date("Y-m-d",$dealdetails->startdate); else echo $this->input->post("startdate");?>">
</div>
</div>
</td>
<td colspan=1>
<div class="itcont">
<div class="head"><label>Deal End Date<span class="mark">*</span></label></div>
<div class="cont"><select name="endhrs" id="endhrs">
	<?php 	for($i=0;$i<24;$i++)	{?>
	<option <?php if(isset($dealdetails) && $selenddate==$i) echo "selected"?> value="<?=$i?>"><?=date("g a",mktime($i))?></option>
	<?php }?>
	<option value="24">Midnight</option>
	</select>
	<input class="inp" id="enddatepicker" style="width:80px;margin-left: 2px;" type="text" name="enddate" id="enddate" value="<?php if(isset($dealdetails)) echo date("Y-m-d",$dealdetails->enddate); else echo $this->input->post("enddate");?>">
</div>
</div>
</td>

<td colspan=1>
<div class="itcont">
<div class="head"><label>Featured Start Date<span class="mark">*</span></label></div>
<div class="cont"><select name="fstarthrs" id="fstarthrs">
	<?php 	for($i=0;$i<24;$i++)	{?>
	<option <?php if(isset($dealdetails) && date("G",$dealdetails->featured_start)==$i) echo "selected"?> value="<?=$i?>"><?=date("g a",mktime($i))?></option>
	<?php }?>
	</select>
	<input class="inp" id="fstartdatepicker" style="width:80px;margin-left: 2px;" type="text" name="fstartdate" id="fstartdate" value="<?php if(isset($dealdetails)) echo date("Y-m-d",$dealdetails->featured_start); else echo $this->input->post("fenddate");?>">
</div>
</div>
</td>

<td colspan=1>
<div class="itcont">
<div class="head"><label>Featured End Date<span class="mark">*</span></label></div>
<div class="cont"><select name="fendhrs" id="fendhrs">
	<?php 	for($i=0;$i<24;$i++)	{?>
	<option <?php if(isset($dealdetails) && date("G",$dealdetails->featured_end)==$i) echo "selected"?> value="<?=$i?>"><?=date("g a",mktime($i))?></option>
	<?php }?>
	</select>
	<input class="inp" id="fenddatepicker" style="width:80px;margin-left: 2px;" type="text" name="fenddate" id="fenddate" value="<?php if(isset($dealdetails)) echo date("Y-m-d",$dealdetails->featured_end); else echo $this->input->post("fenddate");?>">
</div>
</div>
</td>


</tr>


<tr>
<td>
<div class="itcont">
<div class="head"><label>Product Ships in<span class="mark">*</span></label></div>
<div class="cont"><input class="inp" style="width:30px;text-align:right;" name="shipsin" id="shipsin" value="<?php if(isset($dealdetails)) echo $dealdetails->shipsin; else echo $this->input->post('shipsin');?>"> days</div>
</div>
</td>
<td>
<div class="itcont">
<div class="head"><label>Item Code<span class="mark">*</span></label></div>
<div class="cont"><input class="inp" style="width:150px;" name="itemcode" id="itemcode" value="<?php if(isset($dealdetails)) echo $dealdetails->itemcode; else echo $this->input->post('itemcode');?>">
<div class="forminfo">Your product item code</div></div>
</div>
</td>
<td>
<div class="itcont">
<div class="head"><label>Model No.</label></div>
<div class="cont">
<input class="inp" style="width:120px;" name="model" id="model" value="<?php if(isset($dealdetails)) echo $dealdetails->model; else echo $this->input->post('model');?>">
<div class="forminfo">Product model no.</div></div>
</div>
</td>
</tr>

<tr>
<td colspan="2">
<div class="itcont">
<div class="headimp"><label id="qlab">
Quantity
<span class="mark">*</span></label></div>
<div class="cont">
<input class="inp" type="text" name="quantity" style="width:70px;text-align: right;" value="<?php if($e && $d->quantity!=4294967295) echo $d->quantity; elseif(!$e) echo $this->input->post("quantity")?>"  <?php if($e &&  $d->quantity==4294967295) echo "disabled"?>> 
<label style="font-weight:normal" id="qcheck"><input class="inp" type="checkbox" class="nolimit" style="width:auto;min-width:0" <?php if($e &&  $d->quantity==4294967295) echo "checked"?>>No Limit</label>
<div style="color:#999;font-size:10px;">Quantity of this item available for sale (brand sale)<br>(or) Minimum number of buys required to trigger the deal (Group sale)</div>
</div>
</div>
</td>

<td colspan="2">
<div class="itcont">
<div class="headimp"><label id="qlab">
Ships to
<span class="mark">*</span></label></div>
<div class="cont">
<input class="inp" maxlength="100" style="width:98%;" name="shipsto" id="shipsto" value="<?php if(isset($dealdetails)) echo $dealdetails->shipsto; else echo $this->input->post('shipsto');?>">
<div class="forminfo">Enter city names separated by comma<br>Leave blank, if ships all over India</div>
</div>
</div>
</td>

</tr>

</table>

<?php 
if(!isset($is_edit))
{
?>
<?php /*?>
<div class="innerdiv" style="margin-top: 20px;">
<div class="span"><label id="nocount">Items for sale<span class="mark">*</span></label></div>
<div class="span1">
<select id="rooms" name="rooms">
<option value="0" selected="selected">--Select--</option>
<?php for($i=1;$i<=20;$i++){?>
<option value="<?=$i?>"><?=$i?></option>
<?php }?>
</select>
</div>
</div>
*/?>
<input type="hidden" name="rooms" value="1">
<?php }?>
</div>
<div class="clear"></div>
</div>

<div id="descriptions">
<div class="form" style="width:97%;margin:10px;">
<h2 style="margin-bottom:5px">Other details</h2>
<div class="innerdiv">
<div class="span"><label>Website link</label></div>
<div class="span1"><input class="inp" style="width:300px;" name="website" id="website" value="<?php if(isset($dealdetails)) echo $dealdetails->website; else echo $this->input->post('website');?>"></div>
</div>
<div class="innerdiv">
<div class="span"><label>Email</label></div>
<div class="span1"><input class="inp" style="width:300px;" name="email" id="email" value="<?php if(isset($dealdetails)) echo $dealdetails->email; else echo $this->input->post('email');?>"></div>
</div>
<?php if(!$e){?>
<div class="innerdiv" align="left" style="background:#eee;margin-bottom:10px;">
<div style="padding:10px 0px 0px 10px;"><label>Deal Type</label></div>
<div style="padding-left:90px;"><label style="font-weight:normal;font-size:inherit"><input checked type="radio" name="dealtype" value="brandsale" style="width:auto"><b>Brand Sale</b> - Deal is valid for all buyers</label></div>
<div style="padding-left:90px;padding-bottom:10px;"><label style="font-weight:normal;font-size:inherit"><input type="radio" name="dealtype" value="groupsale" style="width:auto"><b>Group Sale</b> - Deal is valid only after certain number of buys</label></div>
</div>
<?php }?>
<div class="innerdiv">
<div class="span"><label>Brief Description<span class="mark">*</span></label></div>
<div class="span1"><textarea style="width:300px;height:50px;" name="bdescription" id="bdescription"><?php if($e) echo $d->description1; else echo $this->input->post("bdescription")?></textarea><div style="color:#777;font-size:10px;">Displayed in bold letters. Please make it brief and sweet.</div></div>
</div>
<div class="innerdiv">
<div class="span"><label>Extra Description</label></div>
<div class="span1"><textarea style="width:300px;height:50px;" name="description" id="description"><?php if($e) echo $d->description2; else echo $this->input->post("description")?></textarea><div style="color:#777;font-size:10px;">Long description about the item goes here</div></div>
</div>
<div class="innerdiv">
<div class="span"><label>Keywords<span class="mark">*</span></label></div>
<div class="span1"><textarea class="inp" style="width:300px;height:50px;" name="keywords"><?php if($e) echo $d->keywords; else echo $this->input->post("keywords")?></textarea><div style="color:#777;font-size:10px;">Comma-separated,  max 150 chars</div></div>
</div>
</div>
<div class="clear"></div>
</div>

<div id="product_details">

	<?php $superadmin=$this->erpm->auth(TRUE,TRUE); ?>
	<?php if(!$e || ($e && $superadmin)){?>
	Search : <input type="text" size="40" class="inp prod_search_add">
	<?php }?>
	<div id="prods_list" class="srch_result_pop closeonclick"></div>
	<div id="link_products_cont">
		<table class="datagrid">
			<thead>
				<tr><th>Product</th><th>Qty</th><th>MRP</th></tR>
			</thead>
		<tbody>
		<?php if($e){
			$itemid=$this->db->query("select id from king_dealitems where dealid=?",$d->dealid)->row()->id;
			$prods=$this->db->query("select p.product_name,p.mrp,l.* from m_product_deal_link l join m_product_info p on p.product_id=l.product_id where l.itemid=?",$itemid)->result_array();
			foreach($prods as $p){
		?>
		<tr>
			<td><input type="hidden" name="prods_id[]" value='<?=$p['product_id']?>'><?=$p['product_name']?></td>
			<td><?=$p['qty']?></td>
			<td><?=$p['mrp']?></td>
			<td colspan="2" style="<?php echo ($superadmin)?'':'display:none;';?>">
					<a href="<?php echo site_url('/admin/remove_pnhdeal_linked_prd/'.$itemid.'/'.$p['product_id'].'/1')?>" class="remove_prd">Remove</a>
			</td>
		</tr>
		<?php } }?>
		</tbody>
		</table>
	</div>
</div>

<div id="groupbuying">
<table width="100%" cellpadding=7>
<tr><td colspan="4"><h3>Group Buying</h3></td></tr>

<tr style="font-weight:bold;">
<td colspan="">
<div class="itcont">
<div class="headimp"><label id="qlab">Slot 1<span class="mark">*</span></label></div>
<div class="cont">
1 - <input class="inp" type="text" name="slot1" style="min-width:20px;width:30px;" value="<?php if($e && isset($nslots[0])) echo $nslots[0]; else echo "5";?>"> : Rs <i>deal price</i>
</div>
</div>
</td>
<td colspan="">
<div class="itcont">
<div class="headimp"><label id="qlab">Slot 2<span class="mark">*</span></label></div>
<div class="cont">
<span class="slot1">5</span> - <input class="inp" type="text" name="slot2" style="min-width:20px;width:30px;" value="<?php if($e && isset($nslots[1])) echo $nslots[1]; else echo "10";?>"> : Rs <input type="text" name="slot2price" style="min-width:50px;width:50px" value="<?php if($e && isset($nslots[1])) echo $nslotprice[1];?>">
</div>
</div>
</td>
<td colspan="">
<div class="itcont">
<div class="headimp"><label id="qlab">Slot 3</label></div>
<div class="cont">
<span class="slot2">5</span> - <input type="text" name="slot3" style="min-width:20px;width:30px;" value="<?php if($e && isset($nslots[2])) echo $nslots[2]; else echo "";?>"> : Rs <input type="text" name="slot3price" style="min-width:50px;width:50px" value="<?php if($e && isset($nslots[2])) echo $nslotprice[2];?>">
</div>
</div>
</td>
<td colspan="">
<div class="itcont">
<div class="headimp"><label id="qlab">Slot 4</label></div>
<div class="cont">
<span class="slot3">5</span> - <input type="text" name="slot4" style="min-width:20px;width:30px;" value="<?php if($e && isset($nslots[3])) echo $nslots[3]; else echo "";?>"> : Rs <input type="text" name="slot4price" style="min-width:50px;width:50px" value="<?php if($e && isset($nslots[3])) echo $nslotprice[3];?>">
</div>
</div>
</td>
</tr>

<tr>
<td colspan="">
<div class="itcont">
<div class="headimp"><label id="qlab">Buy Process expiration</label></div>
<div class="cont">
<input type="text" name="bp_expires" class="inp" style="min-width:20px;width:30px;" value="<?php if($e) echo $dealdetails->bp_expires/24/60/60?>"> days
</div>
</div>
</td>
</tr>
</table>
</div>

<div id="dealconfig">

<table width="100%" cellpadding=7>

<tr><td colspan="4"><h3>Product Sizing</h3></td></tr>

<tr style="font-weight:bold;">

<td colspan="">
<div class="itcont">
<div class="headimp"><input class="inp" type="radio" name="sizing" value="none" <?php if($e && $sizing_type==0) echo 'checked'?>>None</div>
<div class="cont">Fit for all shapes, sizes<br>and knockers</div>
</div>
</td>

<td colspan="">
<div class="itcont">
<div class="headimp"><input type="radio" name="sizing" value="numbering" <?php if($e && $sizing_type==1) echo 'checked'?>>Number</div>
<div class="cont">
<input class="inp" type="text" name="sizing_numbers" value="<?php if($e && $sizing_type==1) echo $sizing;?>">
<div style="color:#655;font-size:10px;">comma separated</div>
</div>
</div>
</td>

<td colspan="2">
<div class="itcont">
<div class="headimp">
Product Sizing : <input class="inp" type="radio" name="sizing" value="wording" <?php if($e && $sizing_type==2) echo 'checked'?>>Word</div>
<div class="cont">
<?php 
$s=false;
$sizes=array("Small","Medium","Large","Extra Large","Extra Extra Large");
if($e && $sizing_type==2)
{
	$s=true;
	$sel_sizes=explode(",",$sizing);
}
?>
<?php foreach($sizes as $i=>$size){?>
<label><input type="checkbox" value="<?=$i?>" <?php if($s && array_search($i,$sel_sizes)!==false) echo  "checked";?> style="min-width:0px;" name="sizing_words[]"><?=$size?></label> &nbsp;&nbsp;
<?php }?>
</div>
</div>
</td>


</tr>

<tr><td colspan="4"><h3>Payment Options</h3></td></tr>

<tr>

<td colspan="">
<div class="itcont">
<div class="headimp"><label>Is COD available?</label></div>
<div class="cont">
<input type="checkbox" <?php if($e && $dealdetails->cod==1) echo "checked"; if(!$e) echo 'checked';?> name="cod" value="1" style="min-width:0px;"> COD Available
</div>
</div>
</td>

<td colspan="">
<div class="itcont">
<div class="headimp"><label>Is Group Buy?</label></div>
<div class="cont">
<input type="checkbox" <?php if($e && $dealdetails->groupbuy==1) echo "checked"?> name="groupbuy" value="1" style="min-width:0px;"> Available
</div>
</div>
</td>


<td>
<div class="itcont">
<div class="head"><label>Is Giftcard?</label></div>
<div class="cont"><input type="checkbox" name="is_giftcard" id="is_giftcard" style="min-width:0px;" value = 1 <?php if($e) echo ($d->is_giftcard?'checked':''); else echo ($this->input->post("is_giftcard")?'checked':'') ?> > Yes</div>
</div>
</td>
<td>
<div class="itcont">
<div class="head"><label>Is Coupon Applicable?</label></div>
<div class="cont"><input type="checkbox" name="is_coupon_applicable" style="min-width:0px;" id="is_coupon_applicable" value = 1 <?php if($e) echo ($d->is_coupon_applicable?'checked':''); else echo ($this->input->post("is_coupon_applicable")?'checked':''); if(!$e && !$_POST) echo 'checked'; ?>  >Yes</div>
</div>
</td>
</tr>

</table>

</div>

</div>

<div style="float:left;">

</div>

<div class="innerdiv"><input style="margin-top: 30px;margin-left:286px; width:90px; padding: 3px 5px; background:#2D6A2E none repeat scroll 0% 0%; font-family: verdana; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; color: rgb(239, 239, 239); font-weight: bold; font-size: 15px;" type="submit" name="continue" 
 value="<?php 
if(isset($is_edit))
echo 'Update';
else
echo 'Add  Deal';
?>">
</div>

</form>


</div>

<style>
.itcont{
border:1px solid #aaa;
padding:3px;
}
.itcont .head{
background:#F4AE77;
color:#000;
font-weight:bold;
padding:5px;
}
.itcont .headimp{
background:#F16D08;
color:#fff;
font-weight:bold;
padding:5px;
}
.itcont .cont{
padding:5px;
}
.itcont .cont input{
min-width:100px;
}
h3{
margin:0px;
margin-top:10px;
}
</style>
<div id="template_lp" style="display:none;">
<table>
<tbody>
<tr>
<td><input type="hidden" name="prods_id[]" value='%itemid%'>%itemname%</td>
<td><input type="text" name="prods_qty[]" class="inp" size=5 value="1"></td>
<td>%mrp%</td>
<td><a href="javascript:void(0)" onclick='$($(this).parents("tr").get(0)).remove()'>remove</a></td>
</tr>
</tbody>
</table>
</div>
<script>

$('a.remove_prd').click(function(e) {
    e.preventDefault();
    if (confirm('Are you sure want to remove this product?')) {
        window.location.href = $(this).attr('href');
    }
});


function addproduct(id,name,mrp)
{
	$("#prods_list").hide();
	temp=$("#template_lp tbody").html();
	temp=temp.replace(/%itemid%/g,id);
	temp=temp.replace(/%itemname%/g,name);
	temp=temp.replace(/%mrp%/g,mrp);
	$("#link_products_cont table tbody").append(temp);
}
$(function(){
<?php if(!$e){?>
	$("#adddealform").submit(function(){
		if($("#link_products_cont tbody").html().length==0)
		{
			alert("Link products to deal");
			return false;
		}
	});
<?php }?>
	
	$(".prod_search_add").keyup(function(){
		$.post('<?=site_url("admin/jx_searchproducts")?>',{q:$(this).val()},function(data){
			$("#prods_list").html(data).show();
		});
	}).attr("disabled",false);
});
</script>