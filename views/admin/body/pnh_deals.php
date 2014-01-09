<?php $type=$this->uri->segment(4);?>
<div class="container">

<div style="float:right">
<select id="sel_brand" data-placeholder="Choose Brand" style="width: 250px;">
<option value="0"></option>
<?php foreach($this->db->query("select id,name from king_brands order by name asc")->result_array() as $b){?>
<option value="<?=$b['id']?>"><?=$b['name']?></option>
<?php }?>
</select>
 
<select id="sel_cat" data-placeholder="Choose Category" style="width: 250px;">
</select>
</div>

<h2><?php if(isset($pagetitle)) echo $pagetitle; else{?>PNH Deals<?php }?></h2>

<?php if($this->uri->segment(3)){ $type=$this->uri->segment(4); if(!$type) $type=0;?>

<div style="float:left;padding:20px 7px;">Sort By : </div>
<div class="dash_bar<?=$type==0?" dash_bar_red":""?>"><a href="<?=site_url("admin/pnh_dealsby".($brand?"brand":"cat")."/".$this->uri->segment(3)."/".$this->uri->segment(4)."/0")?>"></a>Product Name</div>
<div class="dash_bar<?=$type==1?" dash_bar_red":""?>"><a href="<?=site_url("admin/pnh_dealsby".($brand?"brand":"cat")."/".$this->uri->segment(3)."/".$this->uri->segment(4)."/1")?>"></a>Latest sold</div>
<div class="dash_bar<?=$type==2?" dash_bar_red":""?>"><a href="<?=site_url("admin/pnh_dealsby".($brand?"brand":"cat")."/".$this->uri->segment(3)."/".$this->uri->segment(4)."/2")?>"></a>Most sold</div>
<div class="dash_bar<?=$type==3?" dash_bar_red":""?>"><a href="<?=site_url("admin/pnh_dealsby".($brand?"brand":"cat")."/".$this->uri->segment(3)."/".$this->uri->segment(4)."/3")?>"></a>Most sold in <b>90</b> days</div>

<div class="clear"></div>
<?php }?>

<div style="float:right">
<span><img src="<?=IMAGES_URL?>no_photo.png" style="float:left;margin-top:-2px;"> -No image</span> &nbsp; &nbsp; &nbsp;
</div>

<div style="float:right;padding:0px 20px;">
MRP Filter: <input type="text" class="inp" id="f_from" size=4> to <input type="text" class="inp" id="f_to" size=4> <input type="button" value="Filter" onclick='filter_deals()'> 
</div>

<a href="<?=site_url("admin/pnh_adddeal")?>">Add new deal</a>

<div style="padding:10px 0px;">
<table class="datagrid" width="100%">
<thead><tr><th width="20"><input type="checkbox" class="sel_all"></th><th>PNH ID</th><th>Deal Name</th><th>Brand</th><th>Category</th><th>MRP</th><th>Offer Price</th><th>Special Margin Price</th><th>Status</th><?php if($type==1){?><th>Last Order</th><th>Order date</th><?php }elseif($type==2 || $type==3){?><th>Sold</th><?php }?><th></th></tr></thead>
<tbody>
<?php foreach($deals as $d){
 	$d_sm = 0;
 	if($d['sm'])
 	{
 		$d_sm_arr = (explode(',',$d['sm']));
 		$d_sm = $d_sm_arr[0]; 
 	}
?>
<tr class="deal" ref_id="<?=$d['itemid']?>">
<td><input type="checkbox" class="sel" value="<?=$d['itemid']?>"></td>
<td><?=$d['pnh_id']?></td>
<td width="350">
	<?php if(empty($d['pic'])){?> <img style="float:right;" src="<?=IMAGES_URL?>no_photo.png"><?php }?>
<a href="<?=IMAGES_URL?>items/small/<?=$d['pic']?>.jpg" target="blank"><img alt="" height="100" src="<?=IMAGES_URL?>items/<?=$d['pic']?>.jpg" style="float:right;margin-right:20px;"></a>
	<a href="<?=site_url("admin/pnh_deal/{$d['itemid']}")?>"><?=$d['name']?></a>
	<div>
		<a href="javascript:void(0)" class="tgl_stock">View Stock</a>
		<div class="stock_det"></div>
	</div>
</td>
<td><a href="<?=site_url("admin/viewbrand/{$d['brandid']}")?>"><?=$d['brand']?></a></td>
<td><a href="<?=site_url("admin/viewcat/{$d['catid']}")?>"><?=$d['category']?></a></td>
<td><span class="mrp"><?=$d['orgprice']?></span></td>
<td><?=$d['price']?></td>
<td>
	<?php 
		if($d_sm)
		{
			echo $d['price']-(($d_sm*$d['price']/100));
			echo $d_sm?' (<b>'.$d_sm.'%'.'</b>)':'';
		}
		else
		{
			echo 0;
		}
	?>
</td>
<td><?=$d['publish']==1?"Enabled":"Disabled"?> 
<a class="danger_link" href="<?=site_url("admin/pnh_pub_deal/{$d['itemid']}/{$d['publish']}")?>">change</a>
</td>
<?php if($type==1){?>
<td><a href="<?=site_url("admin/trans/{$d['transid']}")?>"><?=$d['transid']?></a></td>
<td><?=$d['order_time']?date("g:ia d/m/y",$d['order_time']):"na"?></td>
<?php }elseif($type==2 || $type==3){?>
<td><?=$d['sold']?></td>
<?php }?>
<td>
<a href="<?=site_url("admin/pnh_editdeal/{$d['itemid']}")?>">Edit</a>
</td>
</tr>
<?php }?>
</tbody>
</table>
</div>
	<div>
		With selected : <input type="button" value="Disable" onclick='endisable_sel("0")'> <input type="button" value="Enable" onclick='endisable_sel("1")'>
	</div>
</div>

<form id="endisable_form" method="post" action="<?=site_url("admin/pnh_pub_unpub_deals")?>">
<input type="hidden" name="action" id="endis_act">
<input type="hidden" name="itemids" id="endis_ids">
</form>
<style type="text/css">
.tgl_stock{font-size: 9px;color: green;background: #f5f5f5;padding:2px;margin:2px 0px;display: inline-block;}
</style>
<script>

function endisable_sel(act)
{
	var ids=[];
	$(".sel:checked").each(function(){
		ids.push($(this).val());
	});
	ids=ids.join(",");
	$("#endis_act").val(act);
	$("#endis_ids").val(ids);
	$("#endisable_form").submit();
}

function filter_deals()
{
	from=$("#f_from").val();
	to=$("#f_to").val();
	if(!is_numeric(from) || !is_numeric(to))
	{		alert("Filter prices are not valid numbers");return;	}
	$(".deal").each(function(){
		m=parseInt($(".mrp",$(this)).text());
		if(m>=from && m<=to)
			$(this).show();
		else{
			$(this).hide();
			$(".sel",$(this)).attr("checked",false);
		}
	});
}

$(function(){
	$(".sel_all").click(function(){
		if($(".sel_all").attr("checked"))
		{
			$(".sel").attr("checked",true);
			$(".deal:hidden").each(function(){
				$(".sel",$(this)).attr("checked",false);
			});
		}
		else
			$(".sel").attr("checked",false);
	});
	$(".sel_all, .sel").attr("checked",false);
	$("#sel_cat").chosen();
	
	$("#sel_brand").chosen();
	/* $("#sel_brand").change(function(){
		if($(this).val()!=0)
			location="<?=site_url("admin/pnh_dealsbybrand")?>/"+$(this).val();
	});*/
	$(function(){
	$('#sel_brand').change(function(){
		var sel_brandid=$(this).val();
		$('#sel_cat').html('').trigger("liszt:updated");
		$.getJSON(site_url+'/admin/loadcat_bybrand/'+sel_brandid,'',function(resp){
		var brand_linkedcat_html='';
		if(resp.status=='error')
		{
			alert(resp.message);
		}
		else
		{
			brand_linkedcat_html+='<option value="">Choose Category </option>';
			$.each(resp.cat_list,function(i,b){
				brand_linkedcat_html +='<option value="'+b.catid+'">'+b.category_name+'</option>';
				});
		}
		 $('#sel_cat').html(brand_linkedcat_html).trigger("liszt:updated");
		 $('#sel_cat').trigger('change');
		});
	});

	$("#sel_cat").change(function(){
		if($(this).val()!=0)
			location="<?=site_url("admin/pnh_dealsbycat")?>/"+$(this).val()+'/'+$("#sel_brand").val()*1;
	});
	
});
	$('.deal a.tgl_stock').click(function(e){
		e.preventDefault();
		var ele  = $(this);

		if(!$('.stock_det',ele.parent().parent()).is(':hidden'))
		{
		 
			$.getJSON(site_url+'/admin/pnh_jx_dealstock_det/'+$(this).parent().parent().parent().attr('ref_id')+'/1',function(resp){
				if(resp.status == 'error')
				{
					$('.stock_det',ele.parent().parent()).html("No Details found").hide();
				}
				else
				{
					var qcktiphtml = '<div>';
						qcktiphtml += '<table width="55%" border=1 class="datagrid" cellpadding=3 cellspacing=0>';
						qcktiphtml += '<thead><tr><th>Product Name</th><th>Stock</th></tr></thead><tbody>';
						$.each(resp.itm_stk_det,function(a,b){
							qcktiphtml+='<tr>';
							qcktiphtml+='	<td>'+b.product_name+'</td>';
							qcktiphtml+='	<td>'+b.stk+'</td>';
							qcktiphtml+='</tr>';
						});
						qcktiphtml += '</tbody></table></div>';
						$('.stock_det',ele.parent().parent()).html(qcktiphtml).show();
				}
				
			});
			$('.stock_det',ele.parent().parent()).html("Loading...").show(); 
		}
		else
		{
			$('.stock_det',ele.parent().parent()).html("").hide(); 
		}
		
	});
	
});
</script>

<div id="qcktip" style="display: none;position: absolute;background: #f1f1f1;padding:5px;border:1px dotted #dfdfdf"></div>

<?php
