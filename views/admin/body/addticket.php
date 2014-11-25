<?php
$trans=false; 
if($this->uri->segment(3))
	$trans=$this->db->query("select distinct(o.transid) as transid, u.name,u.mobile,u.email from king_orders o join king_users u on u.userid=o.userid where o.transid=?",$this->uri->segment(3))->row_array();
?>
<div class="container">
<h2>Add new support ticket</h2>

<form method="post" class="add_ticket_form">

<b class="type">Type :</b>
	<select name="type">
	<option value="0">Query</option>
	<option value="1">Order Issue</option>
	<option value="2">Bug</option>
	<option value="3">Suggestion</option>
	<option value="4">Common</option>
	<!--<option value="5">PNH Returns</option>-->
	<option value="6">Courier Followups</option>
	<!--new services-->
	<option value="10">Request</option>
	<option value="11">Complaint</option>
	</select>
	<table cellpadding=7 class="form_inputs">
		<tr><td>Name :</td><td><input type="text" class="inp" name="name" size=30 value="<?=$trans?$trans['name']:""?>"></td></tr>
		<tr><td>Email :</td><td><input type="text" class="inp" name="email" size=50 value="<?=$trans?$trans['email']:""?>"></td></tr>
		<tr><td>Mobile :</td><td><input type="text" class="inp" name="mobile" size=20 value="<?=$trans?$trans['mobile']:""?>"></td></tr>
		<tr><td>Transaction ID :</td><td><input type="text" class="inp" name="transid" value="<?=$trans?$trans['transid']:""?>"></td></tr>
		<tr>
	<td>Related To :</td><td><select name="related_to">
		<!--new services-->
		<option value="0">Not specific</option>
		<?php
		$request_list = $this->employee->get_services_req_list();
		foreach($request_list as $request)
		{
?>
			<option value="<?=$request['id'];?>"><?=$request['name'];?></option>
<?php
		}
?>
	</select></td>
		</tr>
		<tr><td>Medium :</td><td><select name="medium">
		<option value="0">Email</option>
		<option value="1">Phone</option>
		<option value="2">Other</option>
		</select></td></tr>
		<tr><td>Priority :</td><td><select name="priority">
		<option value=0>Low</option>
		<option value=1>Medium</option>
		<option value=2>High</option>
		<option value=3>Urgent</option>
		</select>
		</td></tr>
		<tr><td>Message :</td><td><textarea name="msg" cols=90 rows=20></textarea></td></tr>
		
	</table>
	<div class="return_inputs" style="display:none">
		<b class="type">Invoice/IMEI :</b><input type="text" name="invoice" class="sel_invoice">
		<button type="button" class="button button-flat-action button-tiny srch_invoice">Go</button>
	</div>	
	<div class="prod_det">
	</div>
<!--		  style="display:none"-->
	<input type="submit" value="Add ticket" class="form_submit"> 
</form>

</div>
<style>
.add_ticket_form b.type
{
    display: inline-block;
    padding-left: 10px;
    width: 104px;
}
.inv_prd_list
{
    background: none repeat scroll 0 0 #fcfcfc;
    border: 1px solid #f1f1f1;
    margin: 15px 0;
    padding: 8px;
    width: 50%;
}
.inv_prd_list a
{
    color: #000;
    font-size: 13px;
}
.inv_prd_list input.sel_prd_box
{
	float: right;
}
.inv_prd_list input.sel_prd_qty
{
    margin-right: 15px;
    width: 70px;
}
.add_ticket_form div
{
	margin: 25px 0;
}
.prd_qty_block
{
	border-top: 1px solid #ff9600;
    margin: 15px 0 6px !important;
    padding: 12px 0 0;
}
</style>
<script>
$('select[name="type"]').change(function(){
	var v=$(this).val();
	if(v==5)
	{
		$('.form_inputs').hide();
		$('.return_inputs').show();
	}
	else
	{
		$('.return_inputs').hide();
		$('.form_inputs').show();
	}
});

$('.srch_invoice').click(function(){
		var i=$('.sel_invoice').val();
		
		$.post(site_url+'/admin/jx_getinvoiceorditems',{scaninp:i},function(resp){
			if(resp.invdet.error)
			{
				
				$('.bulk_po_det .po_cont').html("<div class='page_alert_wrap'>Invoice Details not Found</div>");
				return false;
			}
			else 
			{
				var prd_html='';
				invdet=resp.invdet;
				prd_html+='<div><b class="type">Franchise :</b><a target="_blank" href="'+site_url+'/admin/pnh_franchise/'+resp.franchise_id+'">'+resp.franchise_name+'</a><div>';
				prd_html+='<b class="type">Products List :</b>';
				$.each(invdet.itemlist,function(i,v){
					$.each(v.product_list,function(k,s){
						$.each(s,function(j,p){	
							prd_html+='<div class="inv_prd_list"><a target="_blank" href="'+site_url+'/admin/product/'+p.product_id+'">'+p.product_name+'</a>';
							prd_html+='<input type="checkbox" class="sel_prd_box" product_id="'+p.product_id+'" order_id="'+v.order_id+'" name="prod_rcvd_pid['+v.invoice_no+']['+v.order_id+']['+p.product_id+'][]" value="'+p.product_id+'" onclick="inp_prd_det(this,'+v.order_id+')">';
							//prd_html+='Condition : <select class="inp prod_cond" style="width:200px;font-size:11px;" name="prod_rcvd_cond['+itm_inv_no+']['+itm_ord_id+']['+itm_prod_id+'][]" ><option value="">Choose</option>'+return_condhtml+'</select>';
							prd_html+='<div class="prd_qty_block prd_qty_'+v.order_id+'" style="display:none">'; 
							prd_html+='	Quantity : <input type="text" class="sel_prd_qty sel_prd_qty_'+v.order_id+'" quantity="'+v.quantity+'" has_bc="'+(p.has_barcode)+'" order_id="'+v.order_id+'" name="prod_rcvd_qty['+v.invoice_no+']['+v.order_id+']['+p.product_id+'][]" placeholder="Quantity"></div>';
							prd_html+='</div>';
					});
				});
				});
				prd_html+='<input type="hidden" name="return_by" value="'+resp.franchise_name+'"></div>';
				prd_html+='<div><b class="type" style="vertical-align:top">Remarks :</b><textarea name="prod_rcvd_remarks" cols=67 rows=10></textarea></div>';
				
				$('.form_submit').show();
			}
			$('.prod_det').html(prd_html);
			//$('.bulk_po_det .count').html("Showing "+count+"/"+resp.total_pos+" pos");
			//$('.bulk_po_det .count').show();
			//$('.bulk_po_det .po_cont').html(po_html);
		},'json');
	});
	
function inp_prd_det(e,oid)
{
	if(e.checked)
		$('.prd_qty_'+oid).show();
	else
		$('.prd_qty_'+oid).hide();
}

$('.sel_prd_qty').live('change',function(){
	var oid=$(this).attr('order_id');
	var qty=$(this).attr('quantity');
	var v=$(this).val();
	if(v>qty)
	{
		alert("Invalid Quantity entered");
		$(this).val('');
	}
});

$('.add_ticket_form').submit(function(){
	/*var i=0;
	var j=0;
	$('.sel_prd_box').each(function(){
		var chk=$(this).attr('checked');
		if(chk == 'checked')
		{
			i++;
		}
	});
	
	if(i==0)
	{
		alert('Please Choose products before proceed');
		return false;
	}
	
	$('.sel_prd_box').each(function(){
		var chk=$(this).attr('checked');
		var oid=$(this).attr('order_id');
		
		if(chk == 'checked')
		{
			var q=$('.sel_prd_qty_'+oid).val();
			if(!q)
			{
				j++;
			}
		}
	});
	
	if(j>0)
	{
		alert('Please enter quantity for selected inputs');
		return false;
	}
	if(!$('textarea[name="prod_rcvd_remarks"]').val())
	{
		alert('Please enter Remarks');
		return false;
	}*/
	
});
</script>
<?php
