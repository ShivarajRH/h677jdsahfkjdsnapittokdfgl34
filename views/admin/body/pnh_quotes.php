<div class="container">
	<div style="background: #ffffd0;overflow: hidden;padding:5px;font-size: 12px;float: right">
		Date range : <input type="text" class="inp fil_style" size=10 id="s_from"> to <input type="text" class="inp fil_style" size=10 id="s_to"> <input type="button" class="fil_style" style="padding:3px 6px;" value="Go" onclick='go_date()'>
		&nbsp;
		&nbsp;
		Brand:	<select id="sel_brand">
					<option value=0>select</option>
						<?php foreach($brand_res as $b)
						{?>
							<option value="<?=$b['brandid']?>" <?php echo (($brand == $b['brandid'])?'selected':'');?> ><?=$b['brand_name']?>
							</option>
						<?php }?>
				</select>
	Franchise: <select id="sel_franchisee">
					<option value=0>select</option>
						<?php foreach($franch_res as $f)
						{?>
							<option value="<?=$f['franchise_id']?>" <?php echo (($fra == $f['franchise_id'])?'selected':'');?> ><?=$f['franchise_name']?></option>
						<?php }?>
				</select>  
	</div>
	<div style="overflow: hidden">	
		<h2 style="font-size: 16px;"><?=$pagetitle?></h2>
		<h3>Total Franchise Requests : <?php echo $total_rows ?></h3>
	</div>

	<table class="datagrid" style="width: 100%">
		<thead>
			<tr>
				<th>Quote</th>
				<th>Franchise<br><span style="font-size:10px;font-weight:normal;">sort</span><a href="<?php echo site_url("admin/pnh_quotes/0/$st_d/$en_d/fid/a");?>"><img src="<?=base_url()?>images/desc.gif"></a><a href="<?php echo site_url("admin/pnh_quotes/0/$st_d/$en_d/fid/d");?>"><img src="<?=base_url()?>images/asc.gif"></a></th>
				<th>Products</th>
				<th>New Product</th>
				<th>Respond Time</th>
				<th>Status</th>
				<th>Created On</th>
				<th>Created By</th>
				<th>Updated On</th>
				<th>Updated By</th>
			</tr>
		</thead>
		<tbody>
			<?php 
				//$fr_ids = array();
				//$brand_ids = array();
			?>
			<?php  $colors=array("#AAFFAA","#11EE11","#FFAAAA"); foreach($quotes as $q){
					$status=array("Orders placed",'Price authorized','pending');?>
			<tr style="background:<?=$colors[$s]?>;" class="quotes <?php echo 'fr_'.$q['franchise_id'];?>   <?php echo !$q['quote_status']?'pending_call':'';?>">
				<td><a href="<?=site_url("admin/pnh_quote/{$q['quote_id']}")?>" class="link"><?=$q['quote_id']?></a></td>
				<td><?=$q['franchise_name']?></td>
				<td>
					<?php foreach($this->db->query("select b.name as brand_name,d.brandid,i.name,i.orgprice as mrp,i.price,q.pnh_id,q.* from pnh_quotes_deal_link q join king_dealitems i on i.pnh_id=q.pnh_id and i.is_pnh=1 join king_deals d on d.dealid = i.dealid join king_brands b on b.id = d.brandid  where q.quote_id=?",$q['quote_id'])->result_array() as $d){
					?>
						<div class="<?php echo 'br_'.$d['brandid'];?>"><?=$d['name']?> : <b>Rs <?=$d['dp_price']==0?"na":$d['dp_price']?></b></div>
					<?php }?>
				</td>
				<td><?=$q['new_product']?></td>
				<td width="130">
						<div r_ts="<?php echo $q['created_on']+($q['respond_in_min']*60) ?>" class="resp_in_min"></div>
						<?= !$q['quote_status']?'':'Responded' ?></td>
				<td><?=$status[$s]?></td>
				<td><?=date("d/m/Y h:i a",$q['created_on'])?></td>
				<td><?=$q['created_by']?></td>
				<td><?=$q['created_on']==0?"na":date("g:ia d/m/y",$q['created_on'])?></td>
				<td><?=$q['updated_by']?></td>
			</tr>
			<?php }?>
		</tbody>
	</table>

	<div class="pagination">
		<?php echo $links;?>
	</div> 
	</div>


	<link rel="stylesheet" href="<?php echo base_url();?>/css/jquery.countdown.css" type="text/css">
	<script type="text/javascript" src="<?php echo base_url();?>/js/jquery.countdown.js"></script>
<style>
	.fil_style{font-size: 11px;padding:3px !important;}
	.pending_call{background: tomato !important}
	.pending_call_flash{background: #FFFFA0 !important}
	.resp_in_min {font-size: 11px;font-weight: bold;}
</style>

<style>
.pagination {
    margin-top: 9px;
    float:right;
}
.pagination a{
    background-color: #CCCCCC;
    border-radius: 0 0 0 0;
    box-shadow: 0 1px 2px #CCCCCC;
    display: inline-block;
    margin-bottom: 0;
    margin-left: 0;
    padding: 4px 8px;
    color:#000;
   }
.pagination strong{
     background-color: #777777;
    border-radius: 0 0 0 0;
    box-shadow: 0 1px 2px #CCCCCC;
    color: #FFFFFF;
    display: inline-block;
    margin-bottom: 0;
    margin-left: 0;
    padding: 4px 8px;
   }

.bottom_wrapper
{
	float: right;
    font-size: 13px;
    font-weight: bold;
    margin: 3px 8px 0 9px;
}
.leftcont{display:none}
</style>

<script>

$(function(){
	$("#sel_brand").change(function(){
		location='<?=site_url("admin/pnh_quotes")?>/0/0/0/'+$(this).val()+'/0/0/0';
	});
});
$(function(){
	$("#sel_franchisee").change(function(){
		location='<?=site_url("admin/pnh_quotes")?>/0/0/0/0/'+$(this).val()+'/0/0';
	});
});


$(function () {
	$('.pending_call').each(function(){
		var r_ts = (($('.resp_in_min',this).attr('r_ts')*1)*1000);
			if(r_ts > (new Date()).getTime())
				$('.resp_in_min',this).countdown({until:new Date(r_ts),format: 'dHMS'});
			else
				$('.resp_in_min',this).countdown({since:new Date(r_ts),format: 'dHMS'});
		
	});
});

function flash_pending_call(ele)
{
	if($(ele).hasClass('pending_call_flash'))
	{
		$(ele).removeClass('pending_call_flash');	
	}else
	{
		$(ele).addClass('pending_call_flash');
	}
	setTimeout(function(){
		flash_pending_call(ele);
	},500)
}
$('.pending_call').each(function(){
		flash_pending_call(this);
});


$('#fil_franchise').html('<?php echo $fr_list?>');
$('#fil_franchise').change(function(){
	$('#fil_brand').val('');
	$('.quotes').hide();
	if(!$(this).val())
		$('.quotes').show();
	else
		$('.fr_'+$(this).val()).show();
});

$('#fil_brand').html('<?php echo $br_list?>');
$('#fil_brand').change(function(){
	$('#fil_franchise').val('');
	$('.quotes').hide();
	if(!$(this).val())
		$('.quotes').show();
	else
	{
		$('.br_'+$(this).val()).parent().parent().show();
	}
		
});

function go_date()
{
	from=$("#s_from").val();
	to=$("#s_to").val();
	if(from.length==0 || to.length==0)
	{
		alert("Check date");return;
	}
	location="<?=site_url("admin/pnh_quotes")?>/<?=$this->uri->segment(3)?$this->uri->segment(3):0?>/"+from+"/"+to;
}

$(function(){
	$("#s_from,#s_to").datepicker();
});
</script>

<?php

