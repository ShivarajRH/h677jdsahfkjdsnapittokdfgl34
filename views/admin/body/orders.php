<style>.leftcont{display:none}</style>
<?php 
$sus_fran_list = array();
$sus_fran_list_res = $this->db->query('select franchise_id from pnh_m_franchise_info where is_suspended = 1');
if($sus_fran_list_res->num_rows())
{
	foreach($sus_fran_list_res->result_array() as $fr)
	{ 
		$sus_fran_list[] = $fr['franchise_id'];
	}
}

$finance_role_const_val=$this->db->query("select value from user_access_roles where const_name='FINANCE_ROLE'")->row()->value;

if(!isset($partial_list))
	$partial_list=false;
$trans=array();
foreach($orders as $o)
	$trans[]=$o['transid'];
$braw=$this->db->query("select b.batch_id,i.invoice_no,i.transid from king_invoice i join shipment_batch_process_invoice_link b on b.invoice_no=i.invoice_no where i.transid in ('".implode($trans,"','")."') group by i.invoice_no")->result_array();
foreach($braw as $b)
{
	if(!isset($batches[$b['transid']]))
		$batches[$b['transid']]=array();
	if(!isset($invoices[$b['transid']]))
		$invoices[$b['transid']]=array();
	$batches[$b['transid']][]=$b['batch_id'];
	$invoices[$b['transid']][]=$b['invoice_no'];
}
$pending_flag=false;
if($this->uri->segment(3)=="1")
	$pending_flag=true;
?>


<style>
	.subdatagrid{width: 100%}
	.subdatagrid th{padding:5px;font-size: 11px;background: #F4EB9A;color: maroon}
	.subdatagrid td{padding:3px;font-size: 12px;}
	.subdatagrid td a{color: #121213;}
	.processed_ord td,.shipped_ord td{text-decoration: line-through;color: green  !important;}
	.processed_ord td a,.shipped_ord td a{text-decoration: line-through;color: green !important;}
	.cancelled_ord td{text-decoration: line-through;color: #cd0000 !important;}
	.cancelled_ord td a{text-decoration: line-through;color: #cd0000 !important;}
	.tgl_ord_prod {display: block;min-width: 300px;padding:5px;background: #fafafa;}
	.tgl_ord_prod a{display: block;text-align: center;color: #333;font-size: 12px;text-decoration: underline;}
	.tgl_ord_prod_content {display: none;}
	.pagination{float: right;}
	.pagination a{background: #FFF;display: inline-block;color: #000;padding:3px;}
</style>
<div class="container">
<div>
<div style="float: right">
	
	<?php if(!$partial_list){?>
		
		<div class="dash_bar" style="cursor: pointer;width: 323px;text-align: left;">
			<h4 style="margin:4px;">Print Stock Unavailabilty Report</h4>
			<div style="text-align: left">
			<a style="float: none" target="_blank" href="<?=site_url("admin/stock_unavail_report/".($this->uri->segment(2)=="partial_shipment"?"1":"0")."/".($this->uri->segment(4)?$this->uri->segment(4):0)."/".($this->uri->segment(5)?$this->uri->segment(5):0)."/1")?>">PNH Orders</a>
			<a style="float: none" target="_blank" href="<?=site_url("admin/stock_unavail_report/".($this->uri->segment(2)=="partial_shipment"?"1":"0")."/".($this->uri->segment(4)?$this->uri->segment(4):0)."/".($this->uri->segment(5)?$this->uri->segment(5):0)."/2")?>">Snapittoday Orders</a>
			<a style="float: none" target="_blank" href="<?=site_url("admin/stock_unavail_report/".($this->uri->segment(2)=="partial_shipment"?"1":"0")."/".($this->uri->segment(4)?$this->uri->segment(4):0)."/".($this->uri->segment(5)?$this->uri->segment(5):0)."/0")?>">All Orders</a>
			</div>
		</div>
		<br>
	<?php }?>
	<?php if(!$partial_list){?>
	<div class="dash_bar" style="padding:7px;">
	Date range: <input type="text" size="8" class="inp" id="ds_range" value="<?=$this->uri->segment(4)?>"> to <input size="8" type="text" class="inp"id="de_range" value="<?=$this->uri->segment(5)?>"> <input type="button" value="Show" onclick='showrange()'>
	</div>
	<?php }else
	{
	?>
	<div class="dash_bar" style="padding:7px;">
		Date range: <input type="text" size="8" class="inp" id="ds_range" value="<?=$this->uri->segment(3)?>"> to <input size="8" type="text" class="inp"id="de_range" value="<?=$this->uri->segment(4)?>"> <input type="button" value="Show" onclick='showpartialrange()'>
	</div>	
	<?php 	
	}?>
	
</div>

<?php /*?>
<div class="dash_bar">
<span><?=$this->db->query("select count(distinct(transid)) as l from king_orders where status=3")->row()->l?></span>
Shipped Orders
</div>
*/ ?>

<div class="dash_bar">
<a href="<?=site_url("admin/orders")?>"></a>
<span><?=$this->db->query("select count(distinct(id)) as l from king_orders where time>?",mktime(0,0,0,date("n"),1))->row()->l?></span>
Orders this month
</div>

<?php 
	
	if($this->erpm->auth(FINANCE_ROLE,true))
	{
?>
<div class="dash_bar">
<span><?=$this->db->query("select count(distinct(id)) as l from king_orders where time between ? and ?",array(mktime(0,0,0,date("n")-1,1),mktime(0,0,0,date("n"),date("t"))))->row()->l?></span>
Orders prev month
</div>

<div class="dash_bar qtipblk" qtip-txt="Sales Value : Rs <?=format_price($this->db->query("select sum(i_orgprice-(i_discount+i_coup_discount)*quantity) as l from king_orders a join king_transactions b on a.transid = b.transid where date(from_unixtime(b.init)) >= ? ",date('Y-m-01'))->row()->l,0)?>" > 
<span>Rs <?=format_price($this->db->query("select sum(i_orgprice*quantity) as l from king_orders where time>?",mktime(0,0,0,date("n"),1))->row()->l,0)?></span>
 this month
</div>

<div class="dash_bar qtipblk" qtip-txt="Sales Value : Rs <?=format_price($this->db->query("select sum(i_orgprice-(i_discount+i_coup_discount)*quantity) as l from king_orders a join king_transactions b on a.transid = b.transid where b.init between ? and ?",array(mktime(0,0,0,date("n")-1,1),mktime(0,0,0,date("n"),1)))->row()->l,0)?>" >
<span>Rs <?=format_price($this->db->query("select sum(i_orgprice*quantity) as l from king_orders where time between ? and ?",array(mktime(0,0,0,date("n")-1,1),mktime(0,0,0,date("n"),1)))->row()->l,0)?></span>
 prev month
</div>

<?php } ?>


<script>
	$('.qtipblk').each(function(){
		var ele = $(this)
		$(this).qtip({
		   content: ele.attr('qtip-txt'),
		   show: 'mouseover',
		   hide: 'mouseout'
		});
	});
</script>

<div class="dash_bar_red" >
<a href="<?=site_url("admin/orders/1")?>"></a>
<span style="font-size: 138%"><?php $pending=$this->db->query("select count(distinct(a.id)) as l from king_orders a join king_transactions b on a.transid = b.transid where a.status=0")->row()->l;?><?=$pending?></span>
Pending Orders
</div>

<div class="dash_bar_red">
<a href="<?=site_url("admin/partial_shipment")?>"></a>
Partial Shipment Orders 

</div>

<div class="dash_bar">
<a href="<?=site_url("admin/disabled_but_possible_shipment")?>"></a>
Disabled but possible
</div>

<div class="clear"></div>
 

</div>





<div style="overflow: hidden;clear: both;">
	<h2 ><?=!isset($pagetitle)?"Recent 50 ":""?>Orders <?=isset($pagetitle)?$pagetitle:""?></h2>
</div>



<?php 
	if($this->erpm->auth(true,true) && !$partial_list)
	{
		
		$today = false;
		$ord_stat_cond = ' and a.init between ? and ? ';
		if($this->uri->segment("4"))
		{
			$st = strtotime($this->uri->segment("4").' 00:00:00');
			$en = strtotime($this->uri->segment("5").' 23:59:59');
		}else
		{
			if(!$pending_flag)
			{
				$st = strtotime(date('Y-m-d').' 00:00:00');
				$en = strtotime(date('Y-m-d').' 23:59:59');
				$today = true;
			}else{
				$st = strtotime('2012-01-01 00:00:00');
				$en = strtotime(date('Y-m-d').' 23:59:59');
			}
		}	
		
		if($pending_flag)
		{
			$ord_stat_cond .= ' and b.status = 0 ';
		}
		
		 
		$ttl_ord_summ = $this->db->query("select count(distinct(a.transid)) as total,is_pnh,partner_id,c.name as partner_name  
													from king_transactions a 
													join king_orders b on a.transid = b.transid 
													left join partner_info c on c.id = a.partner_id 
													where 1 $ord_stat_cond 
													group by is_pnh,partner_id  
													order by total,is_pnh,partner_id,partner_name ",array($st,$en))->result_array();
		
		//echo $this->db->last_query();
		
		$ord_sum_sites = array('snapittoday'=>0,'paynearhome'=>0); 
		foreach($ttl_ord_summ as $ttlords)
		{
			if($ttlords['partner_id'])
			{
				$ord_sum_sites[$ttlords['partner_name']] = $ttlords['total'];
			}else if(!$ttlords['partner_id'] && !$ttlords['is_pnh'])
			{
				$ord_sum_sites['snapittoday'] = $ttlords['total'];
			}
			else if(!$ttlords['partner_id'] && $ttlords['is_pnh'])
			{
				$ord_sum_sites['paynearhome'] = $ttlords['total'];
			}
		}
?>
<div style="background: #f9f9f9;overflow: hidden;text-align: right;border;1px solid #cfcfcf">
	<div style="clear: both;">
		<div style="float: left;background:#ccc;color:#000;padding:7px 10px;width: 80px;height:30px;;text-align: center;" >
			<b style="vertical-align: middle;position: relative;top:8px;"><?php echo (!$today)?'Total':'Today';?></b>
		</div>
	<?php 
		$colors = array('#cdFFFF','#fffff0','#cc11df','lightblue','lightgreen','lightblue','#cc11df','blue','green','lightblue');
		$c = 0;
		
		arsort($ord_sum_sites);
		
		foreach($ord_sum_sites as $oby=>$ttl_ord)
		{
			if($oby == 'paynearhome')
				$clr = '#ef4a37';
			else if($oby == 'snapittoday')
				$clr = '#feed00';
			else
				$clr = $colors[$c];
				
	?>
			<div style="float: left;background:<?php echo $clr?>;color:#000;padding:7px 10px;height:30px;width: 80px;text-align: center;" >
				<?php echo ucwords($oby)?> <br /><b style="font-size: 16px;"><?php echo $ttl_ord; ?></b>
			</div> 
	<?php
			$c++; 		
		}
	?>
	</div>
</div>
<?php } ?>
<?php
	$sel_orders_by = $this->uri->segment(6)?$this->uri->segment(6):'all';
?>
<div style="background:#eee;padding:5px;">
	<?php if(!$partial_list){ ?>
	<b>Order By :</b>  
	<select name="orders_by">
		<option value="all" <?php echo ($sel_orders_by == 'all')?'selected':'' ?>>All</option>
		<option value="pnh" <?php echo (($sel_orders_by=='pnh')?'selected':'') ?> >Paynearhome</option>
		<option value="snp" <?php echo (($sel_orders_by=='snp')?'selected':'') ?> >Snapittoday</option>
		<?php
			$partners_available_res = $this->db->query("select name,trans_prefix from partner_info order by name ");
			if($partners_available_res->num_rows())
			{
				foreach($partners_available_res->result_array() as $pa_row)
				{
		?>
					<option <?php echo (($sel_orders_by==$pa_row['trans_prefix'])?'selected':'') ?> value="<?php echo $pa_row['trans_prefix'] ?>"><?php echo $pa_row['name'] ?></option>
		<?php		
				}
			}
		?>
	</select>
	<?php 
		$orders_perpage = $this->uri->segment(7)?$this->uri->segment(7):50;
	?>
	<b>Orders Perpage </b>
	<select name="perpage">
		<option value="50" <?php echo (($orders_perpage==50)?'selected':'') ?> > 50 </option>
		<option value="100" <?php echo (($orders_perpage==100)?'selected':'') ?> > 100 </option>
		<option value="200" <?php echo (($orders_perpage==200)?'selected':'') ?> > 200 </option>
		<option value="500" <?php echo (($orders_perpage==500)?'selected':'') ?> > 500 </option>
		<option value="1000" <?php echo (($orders_perpage==1000)?'selected':'') ?> > 1000 </option>
	</select>
	
	
	
	<span class="pagination">
		Showing<b style="font-size: 14px;"> <?php echo ($cur_pg+1).'-'.($cur_pg+count($orders)).'/'.$total_orders; ?> </b> Orders
		&nbsp;
		&nbsp;
		&nbsp;
		&nbsp;  
		<?php echo $orders_pagination; ?>
	</span>
	
	<?php }else{
	?>
		<div style="background:#eee;padding:5px;">
			<span id="fil_pnhmenu" style="float: right;display:none">
				Filter by PNH Menu : <select name="sel_menuid">
					<option value="">Choose</option>
				</select>
			</span>
			Show : <label><input type="checkbox" class="pnh_o_c">PNH Orders</label> <label><input type="checkbox" class="n_o_c">Other Orders</label>
		</div>
	<?php 	
	} ?>
	
	
</div>


<script type="text/javascript">
	
	var uri_segments = new Array();
		uri_segments[1] = '<?php echo $this->uri->segment(1) ?>';;
		uri_segments[2] = '<?php echo $this->uri->segment(2) ?>';
		uri_segments[3] = '<?php echo $this->uri->segment(3)?$this->uri->segment(3):0 ?>';
		uri_segments[4] = '<?php echo $this->uri->segment(4)?$this->uri->segment(4):0 ?>';
		uri_segments[5] = '<?php echo $this->uri->segment(5)?$this->uri->segment(5):0 ?>';
		uri_segments[6] = '<?php echo $this->uri->segment(6)?$this->uri->segment(6):"all" ?>';
		uri_segments[7] = '<?php echo $this->uri->segment(7)?$this->uri->segment(7):0 ?>';
		uri_segments[8] = '<?php echo $this->uri->segment(8)?$this->uri->segment(8):0 ?>';
		
		
		function load_filter_page()
		{
			var tmp_uri_segs = new Array();
			$.each(uri_segments,function(a,b){
				if(b != '')
					tmp_uri_segs.push(b);
			});
			var rl_url = tmp_uri_segs.join('/');
			location.href = site_url+'/'+rl_url.replace('//','/');
		}
		
		$('select[name="perpage"]').change(function(){
			uri_segments[6] = $('select[name="orders_by"]').val();
			uri_segments[7] = $(this).val();
			uri_segments[8] = "0";
			load_filter_page();
		});
		
		$('select[name="orders_by"]').change(function(){
			uri_segments[6] = $(this).val();
			uri_segments[7] = $('select[name="perpage"]').val();
			uri_segments[8] = "0";
			
			load_filter_page();
		});
		
		
</script>


 

<form action="<?=site_url("admin/bulk_endisable_for_batch")?>" method="post">
<table class="datagrid datagridsort" width="100%">
<thead>
<tr>
<th><input type="checkbox" id="batch_en_disable_all" value="1"></th>
<th width="200">Trans ID</th>
<?php if($partial_list){?>
<th>Pending</th>
<th>Available</th>
<?php }?>
<th width="400">Deal/Product Details 
	<a href="javascript:void(0)" style="text-decoration: underline;font-size: 11px;color: #FFF;float: right" id="exp_col_list">Show/Hide orders</a>
	
</th>
<th width="120">Ship To</th>
<th width="120">Ordered on</th>
<th width="60">Status</th>
<th>Contact</th>

<?php if(!$partial_list){?>
<th>Invoices</th>
<th><nobr>Process Batches</nobr></th>
<?php }?>
<th style="padding:3px;" width="10"><span style="font-size:68%">Batch Enabled</span></th>
</tr>
</thead>
<tbody>
<?php 
$i_menu = array();
foreach($orders as $o){
	
	// process  only partial orders possible > 0  
	if($partial_list){
		if($o['possible'] <= 0)
			continue ; 	
	}
	
	$is_fran_suspended = 0;
	$fr_reg_level = '';
	$fr_reg_level_color = '';
	$o['franchise_name'] = '';
	if($o['is_pnh'])
	{
		$is_fran_suspended = in_array($o['franchise_id'], $sus_fran_list)?1:0;
		
		
		$fr_det = $this->db->query('select franchise_name,created_on from pnh_m_franchise_info where franchise_id = ? ',$o['franchise_id'])->row();
		
		$f_created_on = $fr_det->created_on;
		$o['franchise_name'] = $fr_det->franchise_name;
		
		
		
		$fr_reg_diff = ceil((time()-$f_created_on)/(24*60*60));
		 
		if($fr_reg_diff <= 30)
		{
			$fr_reg_level_color = '#cd0000';
			$fr_reg_level = 'Newbie';
		}
		else if($fr_reg_diff > 30 && $fr_reg_diff <= 60)
		{
			$fr_reg_level_color = 'orange';
			$fr_reg_level = 'Mid Level';
		}else if($fr_reg_diff > 60)
		{
			$fr_reg_level_color = 'green';
			$fr_reg_level = 'Experienced';
		}
	}	
	
	
?>
<tr <?=$o['priority']?"style='background:#ff8;'":""?> class="<?=$o['is_pnh']?"pnh_o":"n_o"?>">
<td>
	<input type="checkbox" class="batch_en_disable <?php echo ($is_fran_suspended?'sus_fran':''); ?>" name="trans[]" value="<?=$o['transid']?>">
	
</td>
<td style="line-height: 20px;">
<?php if($o['priority']){?>
<span class="order_high_priority"></span>
<?php }?>
<a target="_blank" href="<?=site_url("admin/trans/{$o['transid']}")?>" class="link"><?=$o['transid']?></a>
<br />
<a target="_blank" href="<?=site_url("admin/user/{$o['userid']}")?>"><?=$o['name']?></a>
<?php if($o['franchise_name']){?>
<br>
<a target="_blank" href="<?=site_url("admin/pnh_franchise/{$o['franchise_id']}")?>"><?=$o['franchise_name']?></a>
<?php } ?>
<?php
	if($is_fran_suspended)
	{
		echo "<br><b style='color:#FFF;font-size:9px;display:block;padding:3px;text-align:center;background:#cd0000 !important;'>Franchise Suspended</b>";
	}
	
	if($o['is_pnh'])
	{
		echo '<br><b style="font-size: 9px;background-color:'.$fr_reg_level_color.';color:#fff;padding:2px 3px;border-radius:3px;">'.$fr_reg_level.'</b>';
	}
	
	
?>
</td>


<?php if($partial_list){?>
<td><?=$o['pending']?></td>
<td><?=$o['possible']?></td>
<?php }?>

<td style="padding:0px;background: #fafafa !important;">
		<div class="tgl_ord_prod"><a href="tgl_ord_prods">Show Deals</a></div>
		<div class="tgl_ord_prod_content">
			<table class="subdatagrid" cellpadding="0" cellspacing="0">
				<thead>
					<th>Slno</th>
					<th>OID</th>
					<th width="200">ITEM</th>
					<th>QTY</th>
					<th>MRP</th>
					<th>Amount</th>
				</thead>
				<tbody>
					<?php 
						$o_item_list = $this->db->query("select a.status,a.id,a.itemid,b.name,a.quantity,i_orgprice,i_price,i_discount,i_coup_discount from king_orders a
															join king_dealitems b on a.itemid = b.id 
															where a.transid = ? order by a.status 
														",$o['transid'])->result_array();
						$oi = 0;
						foreach($o_item_list as $o_item)
						{
							$is_cancelled = ($o_item['status']==3)?1:0;
							$ord_stat_txt = '';
							if($o_item['status'] == 0)
								$ord_stat_txt = 'pending';
							else if($o_item['status'] == 1)
							 	$ord_stat_txt = 'processed';
							else if($o_item['status'] == 2)
							 	$ord_stat_txt = 'shipped';
							 else if($o_item['status'] == 3)
							 	$ord_stat_txt = 'cancelled';	
							 	
							 $i_menuid=$this->db->query("select menuid from king_deals a join king_dealitems b on a.dealid = b.dealid where b.id = ? ",$o_item['itemid'])->row()->menuid;
							 $i_menu[]= $i_menuid;
					?>
						<tr class="<?php echo $ord_stat_txt.'_ord'?>   <?php echo 'obymenu_'.$i_menuid;?>  ">
							<td width="20"><?php echo ++$oi; ?></td>
							<td width="40"><?php echo $o_item['id'] ?></td>
							<td><?php echo anchor('admin/pnh_deal/'.$o_item['itemid'],$o_item['name']) ?></td>
							<td width="20"><?php echo $o_item['quantity'] ?></td>
							<td width="40"><?php echo $o_item['i_orgprice'] ?></td>
							<td width="40"><?php echo round($o_item['i_orgprice']-($o_item['i_coup_discount']+$o_item['i_discount']),2) ?></td>
						</tr>	
					<?php 		
					
							
							
						}
					?>
				</tbody>
			</table>
		</div>
	</td>
	
	
	
<td><?=ucfirst($o['ship_city'])?></td>
<td><?=format_datetime_ts($o['init'])?></td>
<td><?php switch($o['status']){
case 0: echo "Pending"; break;
case 1: 
	if(isset($invoices[$o['transid']]) && $this->db->query("select 1 from shipment_batch_process_invoice_link where packed=1 and invoice_no in ('".implode("','",$invoices[$o['transid']])."')")->num_rows()==0) echo "Invoiced"; else echo "Packed"; break;
case 2: echo "Shipped"; break;
case 3: echo "Canceled"; break;
}?>
</td>
<td><?=$o['ship_phone']?></td>


<?php if(!$partial_list){?>
<td>
<?php 
if(!isset($invoices[$o['transid']]))
	echo '-';
else {
	foreach($invoices[$o['transid']] as $b){?>
<a href="<?=site_url("admin/invoice/{$b}")?>"><?=$b?></a>
<?php }		
}
?>
</td>
<td><?php 
if(!isset($batches[$o['transid']]))
	echo '-';
else {
	foreach($batches[$o['transid']] as $b){?>
<a href="<?=site_url("admin/batch/{$b}")?>">BATCH<?=$b?></a>
<?php }		
}
?>
</td>
<?php }?>
<td align="Center" style="font-size:75%"><?=$o['batch_enabled']?"<span class='green'>YES</span>":"<span class='red'>NO</span>"?></td>
</tr>
<?php } if(empty($orders)){?>
<tr><td colspan="100%">no orders to show</td></tr>
<?php }?>
</tbody>
</table>
<span style="padding:5px 10px 10px 10px;background:#eee;">Batch Process Flag : <input type="submit" name="enable" value="Enable selected"> <input type="submit" name="disable" value="Disable selected"></span>
</form>
</div>


<script>
<?php
	 
	if(count($i_menu))
	{
		$pmenu_list_res = $this->db->query("select * from pnh_menu where id in (".implode(',',$i_menu).") order by name ");
		if($pmenu_list_res->num_rows())
		{
			foreach($pmenu_list_res->result_array() as $pmenu)
			{
?>
			$('select[name="sel_menuid"]').append('<option value="<?php echo $pmenu['id'] ;?>"><?php echo $pmenu['name'] ;?></option>');
<?php 				
			}
		}
	}
		 
?>

function do_show_orders()
{
	if($(".n_o_c").attr("checked"))
		$(".n_o").show();
	else
		$(".n_o").hide();
	if($(".pnh_o_c").attr("checked"))
	{
		$(".pnh_o").show();
		$('#fil_pnhmenu').show();
	}
	else
	{
		$('#fil_pnhmenu').hide();
		$(".pnh_o").hide();
	}
		
}
$(function(){
	$(".n_o_c,.pnh_o_c").change(function(){
		do_show_orders();
	}).attr("checked",true);
	$("#ds_range,#de_range").datepicker();
	$("#batch_en_disable_all").click(function(){
		if($(this).attr("checked"))
			$(".batch_en_disable").attr("checked",true);
		else
			$(".batch_en_disable").attr("checked",false);
			
			$('.sus_fran').attr('checked',false);
			
	});
	
	$('.sus_fran').change(function(){
		$(this).attr('checked',false);
		alert("Unable to select the order,Franchise Suspended");
	});
});
function showrange()
{
	if($("#ds_range").val().length==0 ||$("#ds_range").val().length==0)
	{
		alert("Pls enter date range");
		return;
	}
	location='<?=site_url("admin/orders/".(!$this->uri->segment(3)?"0":$this->uri->segment(3)))?>/'+$("#ds_range").val()+"/"+$("#de_range").val(); 
}

function showpartialrange()
{
	if($("#ds_range").val().length==0 ||$("#ds_range").val().length==0)
	{
		alert("Pls enter date range");
		return;
	}
	
	location=site_url+'admin/partial_shipment/'+$("#ds_range").val()+"/"+$("#de_range").val();
}

$('select[name="sel_menuid"]').change(function(){
	var sel_menuid = $(this).val();
		$('.pnh_o').hide();
		if(sel_menuid)
		{
			$('.obymenu_'+sel_menuid).each(function(){
				$(this).parents('tr.pnh_o:first').show();
			});	
		}
		else
			$('.pnh_o').show();
});

$('.tgl_ord_prod a').click(function(e){
	e.preventDefault();
	if($(this).parent().next().is(':visible'))
	{
		$(this).text('Show Deals');
		$(this).parent().next().hide();
	}else
	{
		$(this).text('Hide Deals');
		$(this).parent().next().show();
	}
});

$('#exp_col_list').click(function(e){
	e.preventDefault();
	if($(this).data('collapse'))
	{
		$(this).data('collapse',false);
		$('.tgl_ord_prod a').text('Hide Deals');
		$('.tgl_ord_prod_content').show();
	}else
	{
		$(this).data('collapse',true);
		$('.tgl_ord_prod a').text('Show Deals');
		$('.tgl_ord_prod_content').hide();
	}
}).data('collapse',true);
$(function(){
	$('#exp_col_list').trigger('click');
	$('.datagridsort').tablesorter({headers:{0:{sorter:false}},sortList: [[4,0]]});
});

</script>


<?php
