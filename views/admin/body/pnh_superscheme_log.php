<style>.leftcont{display:none}</style>
<div id="container">
<h2><?=isset($pagetitle)?$pagetitle:"Super Scheme Log"?></h2>

<input type="text" class="monthpicker" placeholder="Select Month" style="width: 140px;height:22px;">
<br><br>
<div>
<div style="float: right;margin-top: -47px;margin-right: 12px;margin-bottom: -4px;">
<div>
<select name="menu" id="menu" style="width:250px;" data-placeholder="Choose Menu">
<option value=""></option>
<?php foreach($this->db->query("select id,name from pnh_menu where status = 1 order by name asc")->result_array() as $menu){?>
<option value="<?php echo $menu['id']?>"><?php echo $menu['name']?></option>
<?php }?>
</select>
<select name="franchise" id="franchise" style="width:250px;" data-placeholder="Choose Franchise">
</select>
</div>


</div>
<table class="datagrid" width="100%">
<thead>
	<th><input class="chk_all" type="checkbox"></th>
	<th>Sl no</th>
	<th>Franchise</th>
	<th>Menu</th>
	<th>Category</th>
	<th>Brand</th>
	<th>Total Sales acheived</th>
	<th>Target</th>
	<th>Credit(%)</th>
	<th>Valid from</th>
	<TH>Valid upto</TH>
	<th>Added on</th>
	<th>Cash Back</th>
	
</thead>
<?php if($super_schlist->num_rows()){?>
<?php $i=1; foreach($super_schlist->result_array() as $s){?>
<tbody>
<tr class="sales_det "  fran_id="<?php echo $s['franchise_id'] ;?>"  menu_id="<?php echo  $s['menuid']?>" supersch_logid="<?php echo $s['super_scheme_logid']?>">
<td><?php if($s['valid_to']<=time()){?><input type="checkbox" value="<?php echo$s['super_scheme_logid'];?>" class="sales_det"></td><?php }?>
<td><?php echo $i;?></td>
<td><a href="<?php echo site_url('admin/pnh_franchise/'.$s['franchise_id'].'#super_sch_expired')?>"><?=$s['franchise_name']?></a></td>
<td><?=$s['menuname']?>
	<div id="sales">
		<a href="javascript:void(0)" fran_id="<?php echo $s['franchise_id'] ;?>" menu_id="<?php echo  $s['menuid']?>"  supersch_logid="<?php echo $s['super_scheme_logid']?>"  status='1' class="tgl_viewsales">View sales</a>
		<div class="sales_det">
		</div>
	</div>
</td>

  <td><?=empty($s['catname'])?"All categories":$s['catname']?>
  <td><?=empty($s['brand'])?"All brands":$s['brand']?></td>
</td>
<td><?php echo formatInIndianStyle($s['total_sales']) ;?></td>
<td><?php echo $s['super_scheme_target'] ;?></td>
<td><?php echo $s['super_scheme_cashback'].'%'?></td>
<td><?=date("d/m/Y",$s['valid_from'])?></td>
<td><?=date("d/m/Y",$s['valid_to'])?></td>
<td><?=date("d/m/Y",$s['created_on'])?></td>
<td><?php echo $s['total_sales']>=$s['super_scheme_target']?'<b> Rs '.$s['total_sales']*$s['super_scheme_cashback']/100:" na </b>";?></td>
</tr>
<?php $i++; } ?>

<?php //}else echo '<b>No Data Found</b>';?>
</tbody>
<?php }else
		
			echo "<div align='center'><h3 style='margin:2px;'>No Data found</h3></div>";	
		?>
</table>
<br>
<form action="<?php echo site_url('admin/pnh_superscheme_give_cash_back')?>" id="add_cashback" method="post">
<div id="give_cashback" class="sales_det"  fran_id="<?php echo $s['franchise_id'] ;?>" menu_id="<?php echo  $s['menuid']?>"  supersch_logid="<?php echo $s['super_scheme_logid']?>">With Selected:<input type="button" value="Give Cash Back" onclick="account_correction()"></div>
<input type="hidden" name="super_scheme_logid" value="">
</form>
</div>
<script>
$("#franchise,#menu,#month").chosen();
$("#year").chosen();
$(function(){

	$("#menu").change(function()
	{
		var sel_menuid=$(this).val();
		
		$('#franchise').html('').trigger('liszt:updated');
		var franlist_html='';
		$.getJSON(site_url+'/admin/get_franchisebymenu_id/'+sel_menuid+'',function(resp){
			if(resp.status=='error')
			{
				
			}
			else
			{
				franlist_html+='<option value="">Choose Franchise </option>';
				$.each(resp.menu_fran_list,function(i,b){
					franlist_html +='<option value="'+b.fid+'">'+b.franchise_name+'</option>';
					});
			}
			 $('#franchise').html(franlist_html).trigger("liszt:updated");
			
			});

	});

	$("#franchise").change(function()
	{
		var sel_menuid=$("#menu").val();
		var sel_fran_id=$(this).val();
		
		if(sel_menuid!=0 && sel_fran_id!=0)
		{
			location='<?=site_url("admin/list_activesuperscheme")?>/'+$(this).val()*1+'/'+sel_menuid;
		}
		if(sel_menuid==0 && sel_fran_id!=0)
		{
			location='<?=site_url("admin/list_activesuperscheme")?>/'+sel_fran_id*1;
		}
	});


	$(".monthpicker").change(function(){
		var pickd_monthyr=$(this).val();
		location='<?=site_url("admin/list_activesuperscheme")?>/'+0+'/'+0+'/'+pickd_monthyr;
	});
});	

$('.monthpicker').monthpicker({ StartYear: 2013, ShowIcon: false });


$('.sales_det a.tgl_viewsales ').click(function(e){
		e.preventDefault();
		var ele  = $(this);
		var super_sch_logid = $(this).attr('supersch_logid');
		var sel_fid = $(this).attr('fran_id');
		if($(this).attr('status') == 1)
		{
			var qcktiphtml='';
			$(this).attr('status',0);
			$(this).text('close');
			$.getJSON(site_url+'/admin/to_get_salesdeatails_bymenufran/'+sel_fid+'/'+super_sch_logid,function(resp){
				if(resp.status == 'error')
				{
					$('.sales_det',ele.parent().parent()).html("No Details found").hide();
				}
				else
				{
					 	qcktiphtml += '<span style="float:right"><a class="danger_link" onclick="print_sales(this)" supersch_logid="'+super_sch_logid+'" fran_id="'+sel_fid+'"  href="javascript:void(0)" >Print</a>';
						qcktiphtml += '<a class="danger_link" onclick="export_sales(this)" supersch_logid="'+super_sch_logid+'" fran_id="'+sel_fid+'"  href="javascript:void(0)" >Export</a></span>';
						qcktiphtml += '<div style="max-height:200px;overflow:auto;clear:both">';
						qcktiphtml += '<table width="100%" border=1 class="datagrid" >';
						qcktiphtml += '<thead><tr><th>Product</th><th>Orderd Date</th><th>Amount</th><th>Qty</th></tr></thead><tbody>';
						$.each(resp.sales,function(a,b){
							
							qcktiphtml+='<tr>';
							qcktiphtml+='	<td>'+b.deal+'</a><br>(<b>Inv No:'+b.invoice_no+'</b>)'+'</td>';
							qcktiphtml+='	<td>'+b.order_date+'</td>';
							qcktiphtml+='	<td>'+b.sub_total+ '</td>';
							qcktiphtml+='	<td>'+b.deal_qty+ '</td>';
							qcktiphtml+='</tr>';
						});
						qcktiphtml += '</tbody></table></div>';
						$('.sales_det',ele.parent().parent()).html(qcktiphtml).show();
				}
				
			});
			$('.sales_det',ele.parent().parent()).html("Loading...").hide(); 
		}
		else
		{
			$(this).attr('status',1);
			$(this).text('View sales');
			$('.sales_det',ele.parent().parent()).html(qcktiphtml).hide(); 
		}
	
	});

function print_sales(ele)
{
	var super_scheme_logid=$(ele).attr('supersch_logid');
	var franchise_id=$(ele).attr('fran_id');
	var print_url = site_url+'/admin/pnh_print_superscheme_by_menu/'+super_scheme_logid+'/'+franchise_id;
		window.open(print_url);
}

function export_sales(ele)
{
	var super_scheme_logid=$(ele).attr('supersch_logid');
	var franchise_id=$(ele).attr('fran_id');
	var print_url = site_url+'/admin/pnh_export_superschemesales_by_menu/'+super_scheme_logid+'/'+franchise_id;
		window.open(print_url);
}

$(".chk_all").click(function(){
	if($(this).attr("checked"))
		$(".sales_det").attr("checked",true);
	else
		$(".sales_det").attr("checked",false);
});

function account_correction()
{
	
	if($('.sales_det:checked').length == 0 )
	{
	    alert('Select cash back to be submitted');
	    return false;
    }
	else
	{
		var give_cashback=confirm('Are you Sure');
		if(give_cashback)
		{
			var super_scheme_logids=[];
			$(".sales_det:checked").each(function(){
				super_scheme_logids.push($(this).val());
			});
			super_scheme_logids=super_scheme_logids.join(",");
			$('#add_cashback input[name="super_scheme_logid"]').val(super_scheme_logids);
			$("#add_cashback").submit();
		}
			
		
	}
}
</script>
<style>
.tgl_viewsales {
    background: none repeat scroll 0 0 rgb(245, 245, 245);
    color: green;
    display: inline-block;
    font-size: 9px;
    margin: 2px 0;
    padding: 2px;
    }
</style>