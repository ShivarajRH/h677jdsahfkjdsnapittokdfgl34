<div class="container page_wrap">
	<h2>Vendor Margin Bulk Update</h2>
	<table cellspacing="5" >
		<tr>
			<td><b>Category</b></td>
		</tr>
		
		<tr>	
			<td>
				<select name="cat" class="cat chk_entry" data-placeholder="Select Category" style="width: 250px;"data-required="true">
					<option value="">Select Category</option>
					<?php $cats=$this->db->query("select id,name from king_categories  GROUP BY name order by name asc")->result_array();?>
					<?php foreach($cats as $c){?>
					<option value="<?php echo $c['id']?>"><?php echo $c['name']?></option>
					<?php }?>
				</select> 
			</td>
		</tr>

		<tr>
			<td><b>Brand</b></td>
		</tr>
			
		<tr>
			<td>
				<select name="brand" class="brand chk_entry" data-placeholder="Select Brand" style="width: 250px;"data-required="true" ></select>
			</td>
		</tr>
		
		<tr>
			<td>
				<b>Vendor</b>
			</td>
		</tr>
		
		<tr>
			<td>
				<select name="vendor[]" class="vendor chk_entry" data-placeholder="Select Vendor" style="width: 250px;" data-required="true" multiple="true"></select>
			</td>
		</tr>
		
		<tr>
			<td style="float: right;"><input type="button" id="load_vdrbrnd_link" value="Load" class="button button-rounded button-flat-secondary button-small"></td>
		</tr>
	</table>
	
	<div id="brand_vndr_link" >
		<h3 class="margin_update_header">Vendor Margin</h3>
		<form action="<?php echo site_url('admin/vmargin_bulk_update')?>" method="post" id="vendor_mrgin_bulk_update_form">
			<table class="datagrid" width="100%" id="brand_vndr_link_tbl">
				<thead>
					<th>&nbsp;</th>
					<th>Category</th>
					<th>Brand</th>
					<th>Vendor</th>
					<th> Margin</th>
					<th>Applicable From</th>
					<th>Applicable To</th>
				</thead>
				<tbody></tbody>
			</table>
			<br><br>
			<input type='button' onclick='update_margin()' value='Submit' style='float:right;' class="button button-action button-rounded button-small">
		</form>
	</div>
</div>

<script>
	$(".l_applicabledate_frm").datepicker({minDate:0});
	
	$('#brand_vndr_link').hide();
	$('.brand').chosen();
	$('.cat').chosen();
	$('.vendor').chosen(); 	


$('.cat').change(function(){
	var sel_catid=$(this).val();
	if(sel_catid!='0')
	{
		$(".brand").html('').trigger("liszt:updated");
		$.getJSON(site_url+'/admin/jx_load_allbrandsbycat/'+sel_catid,'',function(resp){
		var brands_html='';
		if(resp.status=='error')
		{
			alert(resp.message);
		}
		else
		{
			brands_html+='<option value=""></option>';
			brands_html+='<option value="0">All</option>';
			$.each(resp.brand_list,function(i,b){
			brands_html+='<option value="'+b.brandid+'">'+b.name+'</option>';
			});
		}
		 $('.brand').html(brands_html).trigger("liszt:updated");
		
		});
	}
	
});



$('.brand').change(function(){

	var sel_brandid=$(this).val();
	var sel_catid = $('.cat').val();
	if(sel_brandid!='0')
	{
		$(".vendor").html('').trigger("liszt:updated");
		$.getJSON(site_url+'/admin/jx_load_allvendorsbybrandcat/'+sel_brandid+'/'+sel_catid,'',function(resp){
			var vendor_html='';
				if(resp.status=='error')
				{
					alert(resp.message);
				}
				else
				{
					vendor_html+='<option value=""></option>';
					
					$.each(resp.vendor_list,function(i,b){
					vendor_html+='<option value="'+b.vendor_id+'">'+b.vendor_name+'</option>';
					});
				}
		 	$('.vendor').html(vendor_html).trigger("liszt:updated");
		
		});
	}
	
});



$('.edit_vblink_chk').live('change',function(){
	var tds = $(this).parent().parent().find('td');
	if($(this).attr("checked")){
		tds.addClass('selected');
		tds.find(".inp").attr("disabled",false);
		
	}else
	{
		tds.removeClass('selected');
		tds.find(".inp").attr("disabled",true);
	}
});



function remove_vblink(ele){
	
	var trEle = $(ele).parent().parent();

	ven_id=$('.vendor').val();

	brand_id=$('.brand').val();
	
	
	if(ven_id)
	{
		if(confirm("Want to remove "+$('td:eq(2)',trEle).text()+" from this vendor ?"))
		{
			brand_id = $('input[name="l_brand[]"]',trEle).val();
			$.post(site_url+'/admin/jx_remove_vendor_brand_link','vendor_id='+ven_id+'&brand_id='+brand_id,function(resp){
				if(resp.status == 'error')
				{
					alert(resp.error);
				}else
				{
					trEle.fadeOut().remove();
				}
			},'json');
		}
	}
	else
	{
		trEle.fadeOut().remove();
	}		
}
$("#load_vdrbrnd_link").click(function(){
	
		catid = $('.cat').val();
		brandid = $('.brand').val();
		vendorid= $('.vendor').val();
		
		//$(this).attr('disabled',true);
		$.post(site_url+'/admin/bulk_update_vndrmrg',{catid:catid,brandid:brandid,vendorid:vendorid},function(resp){
			if(resp.status=="success")
			{
				$('#brand_vndr_link').show();
				$.each(resp.v_brnd,function(k,v){
					if(!$(".vmcfg_"+v.vendor_id+"_"+v.cat_id+"_"+v.brand_id).length)
					{
					 var tblRow =
						 		"<tr class='vmcfg_"+v.vendor_id+"_"+v.cat_id+"_"+v.brand_id+"' >"
						 		+"	<td><input type='checkbox' value='1' class='edit_vblink_chk'></td>"
						 		+"	<td><input type='hidden' disabled='disabled' class='inp' name='l_cat[]' value="+v.cat_id+">"+v.category_name+"</td>"
						  		+"	<td><input type='hidden' disabled='disabled' class='inp' name='l_brand[]' value="+v.brand_id+">"+v.brand_name+"</td>"
						  		+"	<td><input type='hidden' disabled='disabled' class='inp' name='l_vendor[]' value="+v.vendor_id+"><a  href='"+site_url+'/admin/vendor/'+v.vendor_id+"' target='_blank'>"+v.vendor_name+"</a></td>"
						  		+"	<td><input type='text' disabled='disabled'  class='inp' name='l_margin[]' value="+v.brand_margin+"></td>"
						  		+"	<td><input type='text' disabled='disabled'  class='inp datepic l_applicabledate_frm' name='l_from[]' value="+v.applicable_from+"  readonly='readonly'></td>"
						  		+"	<td><input type='text' disabled='disabled'  class='inp datepic l_applicabledate_frm'  name='l_untill[]' value="+v.applicable_till+" readonly='readonly'></td>"
						  		//+"	<td><a href='javascript:void(0)'  onclick='remove_vblink(this)' >remove</a></td>"+
						  		+"</tr>";
						$(tblRow).appendTo("#brand_vndr_link_tbl tbody");
					}
				});

				$('#brand_vndr_link_tbl .l_applicabledate_frm ').each(function(i,dpEle){
					if(!$(this).hasClass('hasDatepicker'))
						$(this).datepicker({minDate:0});
					
					if(!$('#cat_brandtable .to_date:eq('+i+')').hasClass('hasDatepicker'))
						$('#cat_brandtable .to_date:eq('+i+')').datepicker({minDate:0});
					
				});
			}
			
			else
			{
				$('#brand_vndr_link').show();
				 var tblRow =
					 "<tr>"
					 +"<td><div style='align:center;'><b>No Data Found</b></div></td>"+
					 +"</tr>"
					  $(tblRow).appendTo("#brand_vndr_link_tbl tbody");
			}
						
		},'json');
	});



function update_margin()
{
	 /*if($('.datepic').val() == 0)
	  {
    	 alert('Applicable  date must be filled');
 	    return false;
	        
	     }*/
	if($('.edit_vblink_chk:checked').length == 0 )
	{
	    alert('Select Margin to be updated');
	    return false;
    }
	var bm_error_status = 0;
	
	$('.edit_vblink_chk:checked').each(function(){
		var $r = $(this).parents('tr:first');
		var margin=$('input[name="l_margin[]"]',$r).val()*1;
		var appl_frm=$('input[name="l_from[]"]',$r).val();
		var appl_to=$('input[name="l_untill[]"]',$r).val();
		
			if(isNaN(margin))
			{
				bm_error_status = 1;
				$('input[name="l_margin[]"]',$r).addClass('error_inp');
			}else if(margin == 0)
			{
				bm_error_status = 1;
				$('input[name="l_margin[]"]',$r).addClass('error_inp');
			}

			if(appl_frm == "")
			{
				bm_error_status = 1;
				$('input[name="l_from[]"]',$r).addClass('error_inp');
			}
			if(appl_to == "")
			{
				bm_error_status = 1;
				$('input[name="l_untill[]"]',$r).addClass('error_inp');
			}
	});
	if(bm_error_status)
	{
		$('.error_inp:first').focus();
		alert("Invalid Margin value and Dates selected");
		return false;
	}
	else
	{
		$('#vendor_mrgin_bulk_update_form').submit();
	}
}
</script>
<style>
.error_inp{border:1px solid #cd0000 !important;}
td.selected{
	background: #b4defe !important;
}
.header-right > .segmented, .header-right > .btn {
    margin-left: 5px;
}

.brand_vndr_link {
	background-color: rgb(223, 224, 240);
	cursor: pointer;
	float: right;
	width: 100%;
}
.margin_update_header
{margin-top:30px;}
</style>