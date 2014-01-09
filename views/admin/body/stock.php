<div class="container">

 
<h3>Stock Update</h3>

<div style="margin-bottom:20px;">
<form method="post" id="barcodefrm">
Enter barcode here: <input type="text" class="barcode inp" name="barcode" style="padding:7px;"> <img src="<?=base_url()?>images/loading.gif" id="bload" style="display:none;">
</form>
</div>



<form id="stock_upd_frm" method="post" action="<?php echo site_url('admin/stock');?>">
<table  class="datagrid stock_up" cellpadding=5 cellspacing=0>
<tr>
<th>Item Name</th>
<th style="display:none;">ID</th>
<th align="center">MRP</th>
<th align="center">Stock</th>
<th align="center">Qty Affected</th>
<th></th>
</tr>

<tr id="clone" style="display:none">
	<td >
		<input type="hidden" class="itemdealid">
		<input type="hidden" class="itemid" name="itemid[]">
		<input type="text" class="inp itemname" style="width:400px;font-size:9px;padding:5px;"><img src="<?=base_url()?>images/loading.gif" style="margin-left:-26px;position:absolute;margin-bottom:-3px;display:none;">
	</td>
	<td style="display:none;">
	<input type="text" size="12" class="inp itemiddisp" disabled="disabled">
	</td>
	<td >
		<input type="text" size="6" class="inp itemmrp" disabled="disabled">
	</td>
	<td >
	<input type="text" size="2" class="inp itemavail" disabled="disabled">
	</td>
	<td >
		<input type="text" size="8" class="inp itemqty" style="padding:3px;" name="qty[]">
	</td>
	<td class="row_opts" >
		<a href="javascript:void(0)" class="add_row" onclick="clone()" style="font-size: 14px;color:#cd0000;font-weight: bold;margin-right: 5px;">[+]</a>
		
		<a href="javascript:void(0)" class="remove_row" onclick="del_rowdata(this)" style="font-size: 14px;color:#cd0000;font-weight: bold;">[X]</a>
		&nbsp; 
		<a href="javascript:void(0)" style="display: none;" target="_blank" class="editdeal">Edit Deal</a> 
	</td>
</tr>
</table>

<table style="margin-top:10px;" cellpadding=5>
<tr>
<td valign="top">Remarks<br><textarea name="remarks"></textarea></td>
<td valign="top">Type<br><input type="radio" name="type" value="in" checked="checked">In <input type="radio" name="type" value="out">Out</td>
<td valign="top">Purchase Date<br><input type="text" class="inp" id="date" name="date"></td>
<td valign="top">Vendor<br><input type="text" name="vendor" class="inp"></td>
<td valign="top">Bill Amount<br><input type="text" name="amount" class="inp"></td>
<td valign="top">Reference No<br><input type="text" name="reference" class="inp"></td>
</tr>
</table>

<input type="submit" value="Update">

</form>

<div id="search"></div>

</div>

<style>
#search{
position:absolute;
width:400px;
background:#fff;
border:1px solid #aaa;
border-top:0px;
font-size:10px;
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
</style>

<script type="text/javascript">

function del_rowdata(ele)
{
	$(ele).parent().parent().remove();
	reset_row_opts();
}

$('.editdeal').live('click',function(e){
	if($(this).data('avail') != 1){
		alert("Item not selected");
		e.preventDefault();
	}
});


$('#stock_upd_frm').submit(function(){
	if(!confirm("Are you sure you want  to submit this form")){
		return false;
	}
});
 

var obj,srcht;
function clone()
{
	c=$(".stock_up").append("<tr>"+$("#clone").html()+"</tr>");
	c=$(".stock_up tr:last");
	bindr();

	reset_row_opts();
	return c;
}

function searchitem(o)
{
	v=$(o).val();
	if(v.length<3)
		return;


	$('editdeal',$(o).parent().parent()).data('avail',0);
	
	obj=$(o);
	$("img",$(o).parent()).show();
	$.post("<?=site_url("admin/jx_searchitem")?>","p="+v,function(data){

		off=$(o).offset();
		$("img",$(o).parent()).hide();
		$("#search").css("top",parseInt(off.top+$(o).height()+10)+"px");
		$("#search").css("left",off.left+"px");
		$("#search").html(data).show();

		if($('.itemname:first').data('trig_input')){
			if($('#search a').length == 1){
				$('#search a').trigger('click');
				$(".itemqty").focus();
				$("#search").hide();
			}
			
		}
		
		
	});
}
function bindr()
{
	$(".itemname").unbind("keypress");
	$(".itemname").keypress(function(){
		o=this;
		window.clearTimeout(srcht);
		srcht=window.setTimeout(function(){searchitem(o);},200);
	}).blur(function(){
		window.setTimeout(function(){$("#search").hide();},300);
	});
}

function selitem(id,mrp,name,ava,dealid)
{
	$(".itemid",obj.parent()).val(id);
	$(".itemmrp",obj.parent().parent()).val(mrp);
	$(".itemavail",obj.parent().parent()).val(ava);
	$(".itemiddisp",obj.parent().parent()).val(id);
	$('.itemdealid',obj.parent().parent()).val(dealid);
	$('.editdeal',obj.parent().parent()).attr('href',site_url+'/admin/edit/'+dealid);
	$('.editdeal',obj.parent().parent()).data('avail',1);
	$('.editdeal',obj.parent().parent()).show();
	obj.val(name);
	$(".itemqty").focus();
	$("#search").hide();

	
	
}


function reset_row_opts(){
	$('.add_row').hide();$('.add_row:last').show();
	$('.remove_row').show();

	if($('.remove_row').length == 2)
		$('.remove_row').hide();
}

$(function(){
	$("#date").datepicker({dateFormat:'dd-mm-yy'});
	clone();
	$("#barcodefrm").submit(function(){
		$("#bload").show();
		$.post("<?=site_url("admin/jx_searchbarcodes")?>","p="+$("input",$(this)).val(),function(data){
			$("#bload").hide();
			if(data.length==0)
			{
				alert("product not found");
				return;
			}
			if($(".itemname").length == 2)
			{
				if($(".itemname:last").val()=='')
				{
					bo=$(".itemname:last").parent().parent();	
				}
				else
				{
					bo=clone();	
				}
			}
			else
			{
				bo=clone();
			}
				
			
				
			obj=$(".itemname",bo);
			ret=$.parseJSON(data);
			selitem(ret.id,ret.mrp,ret.name,ret.ava,ret.dealid);
		});
		return false;
	});

	<?php 
			$item_name = '$item_name';
			if($this->uri->segment(3)){
				$itemname = addslashes(base64_decode($this->uri->segment(3)));
		?>
				$('.itemname').data('trig_input',1).val('<?php echo $itemname; ?>').trigger('keypress');
		<?php 
			}
		?>

	$('input[name="barcode"]').focus();	
		
});
</script>