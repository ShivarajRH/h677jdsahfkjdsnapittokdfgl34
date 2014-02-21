<div class="module" width="100%">


<?php 
/*$sch_disc=$this->db->query("SELECT m.name AS menu,IF(a.brand_id,b.name,'All') AS brand,IF(a.cat_id,c.name,'All') AS cat,a.*
	FROM pnh_super_scheme a
	LEFT JOIN pnh_menu m ON m.id=a.menu_id
	LEFT JOIN king_brands b ON b.id = a.brand_id
	LEFT JOIN king_categories c ON c.id = a.cat_id
	WHERE a.franchise_id=? AND is_active=1 AND UNIX_TIMESTAMP(NOW()) BETWEEN a.valid_from AND a.valid_to",$fid);
	
	if($sch_disc->num_rows())
	{*/
?>
<div class="module"> 

	
<table class="datagrid noprint" id="super_scheme_sales" width="100%">
<h4>Super Scheme Sales Statics</h4>
<thead><th>Menu</th><th>Brand</th><th>Category</th><th>Total Sales achieved</th><th>Target</th></thead>
<tbody>	
<?php $i=1; foreach($items as $item){
$super_scheme_sales=$this->db->query("SELECT  d.menuid,d.brandid,d.catid,SUM(i_orgprice-(i_discount+i_coup_discount)) AS ttl_sales,f.name as brand_name,g.name as cat_name,m.name as menu_name,b.super_scheme_target 
											FROM king_transactions a
											JOIN king_orders b ON a.transid = b.transid 
											JOIN king_dealitems c ON c.id = b.itemid 
											JOIN king_deals d ON d.dealid = c.dealid
											JOIN pnh_super_scheme e ON e.franchise_id = a.franchise_id
											join king_brands f on f.id=d.brandid
											join king_categories g on g.id=d.catid
											join pnh_menu m on m.id=d.menuid
											WHERE a.franchise_id = ? AND a.is_pnh = 1 AND b.has_super_scheme = 1 AND a.init BETWEEN e.valid_from AND e.valid_to and d.menuid=? and d.brandid=? and d.catid=? 
											group by d.menuid,d.brandid",array($fid,$item['menuid'],$item['brandid'],$item['catid']));
if($super_scheme_sales->num_rows()){
foreach($super_scheme_sales->result_array() as $super_sch){
?>
<tr><td><?php echo $super_sch['menu_name']?></td><td><?php echo $super_sch['brand_name']?></td><td><?php echo $super_sch['cat_name']?></td><td><?php echo $super_sch['ttl_sales']?></td><td><?php echo $super_sch['super_scheme_target']?></td></tr>

<?php }}}?>
</tbody>
</table>
</div> 
<?php //}
?>
<div class="module"> 
<table class="datagrid noprint" width="100%">
<h4 class="module_title">Order Confirmation</h4>
<thead><Tr><th>Sno</th><th>Product Name</th><th>MRP</th><th>Offer price /Dealer price</th><th>Menu Margin (A)</th><th>Scheme discount (B)</th><th>Balance Discount (C)</th><th>Total Discount (A+B+C)</th><th>Unit Price</th><th>Qty</th><th>Order price</th></Tr></thead>
<tbody>

<?php $i=1; foreach($items as $item){
	$has_super_scheme = $this->db->query("select count(*) as t from pnh_super_scheme a  where a.menu_id = ? and a.brand_id = ? and a.cat_id = ? and a.is_active = 1 and a.franchise_id=?  ",array($item['menuid'],$item['brandid'],$item['catid'],$fid))->row()->t;
	if(empty($has_super_scheme))
		$has_super_scheme = $this->db->query("select count(*) as t from pnh_super_scheme a   where a.menu_id = ? and a.brand_id = ? and cat_id = 0 and a.is_active = 1 and a.franchise_id=? ",array($item['menuid'],$item['brandid'],$fid))->row()->t;
	if(empty($has_super_scheme))
		$has_super_scheme = $this->db->query("select count(*) as t from pnh_super_scheme a   where a.menu_id = ? and a.brand_id = 0 and cat_id = ? and a.is_active = 1 and a.franchise_id=? ",array($item['menuid'],$item['catid'],$fid))->row()->t;
	if(empty($has_super_scheme))
		$has_super_scheme = $this->db->query("select count(*) as t from pnh_super_scheme a   where a.menu_id = ? and a.brand_id = 0 and cat_id = 0 and a.is_active = 1 and a.franchise_id=? ",array($item['menuid'],$fid))->row()->t;
	
	if($has_super_scheme)
	{
		$has_scheme=$this->db->query("select count(*) as t from pnh_superscheme_deals where menuid=? and is_active=0 and itemid=? and ? between valid_from and valid_to ",array($item['menuid'],$item['itemid'],time()))->row()->t;
	}
		
?>
<tr>
<td><?=$i++?></td>
<td><?=$item['name']?>
<?php if($has_super_scheme && !$has_scheme){?><span style="background-color: yellow; display: block;padding: 2px 4px;width:250;font-size:10px;">
<?php echo  '<b>Part of super scheme</b>';?>
</span><?php }if($has_scheme){ echo "<span style='background-color:red;display:block;font-size:10px;'><b>Knocked from super scheme</b><span>";}?></td>
<td><?=$item['mrp']?></td>
<td><?=$item['price']?></td>
<td><?=$item['price']/100*$item['base_margin']?> (<?=$item['base_margin']?>%)</td>
<td><?=$item['price']/100*$item['sch_margin']?> (<?=$item['sch_margin']?>%)</td>
<td><?=$item['price']/100*$item['bal_discount']?> (<?=$item['bal_discount']?>%)</td>
<td><?=($item['price']/100*($item['sch_margin']+$item['base_margin']+$item['bal_discount']))?> (<?=$item['base_margin']+$item['sch_margin']+$item['bal_discount']?>%)</td>
<td><?=$item['final_price']?></td>
<td>x<?=$item['qty']?></td>
<td><?=$item['final_price']*$item['qty']?></td>
</tr>
<?php }?>
</tbody>
</table>
</div>

<div class="module"> 
<h4 class="module_title">Price changes</h4>
<table class="datagrid noprint" width="100%">
<thead><tr><th>PNH ID</th><th>Product</th><th>Old MRP</th><th>New MRP</th><th>Old Price</th><th>New Price</th><th>Change</th></tr></thead>
<tbody>
<?php $c=0; foreach($deals as $d){?>
<tr>
<td><?=$d['pnh_id']?></td><td><a href="<?=site_url("admin/pnh_deal/{$d['pnh_id']}")?>" target="_blank"><?=$d['name']?></a></td>
<td><?=$d['old_mrp']?></td>
<td style="background:#f37"><?=$d['new_mrp']?></td>
<td><?=$d['old_price']?></td>
<td style="background:#f37"><?=$d['new_price']?></td>
<td><?=$d['new_price']-$d['old_price']?></td>
</tr>
<?php $c+=$d['new_price']-$d['old_price']; } if(empty($deals)){?>
<tr>
<td colspan="100%">no price changes</td>
</tr>
<?php }else{?>
<tr>
<td colspan="100%"><input type="checkbox" class="price_c_com" value="yes"> Price change communicated to franchise</td>
</tr>
<?php }?>
</tbody>
</table>
</div>


<div class="module" style="<?php echo $mid?'':'display:none';?>"> 
<h4 class="module_title">Redeem loyalty points</h4> 
<?php $mpointsr=$this->db->query("select points,concat(first_name,' ',last_name) as m_name from pnh_member_info where pnh_member_id=?",$mid)->row_array(); $mpoints=0; if(!empty($mpointsr)) $mpoints=$mpointsr['points'];?>
<table class="datagrid noprint" width="100%">
<thead>
	<th>MemberID</th>
	<th>Name</th>
	<th>Points</th>
</thead>
<tbody>
	<tr>
		<td><?php echo $mid ?></td>
		<td><b><?=$mpointsr['m_name']?></b></td>
		<td>
			<div style="padding:5px;background: #FFF">
			<b><?=$mpoints?></b> Available 
			<br />
			<?php if($mpoints>=150){?>
					<span id=""><input type="checkbox" id="redeem_cont" name="redeem" value="1"></span>Redeem <input class="redeem_points" type="text" class="inp" size=4 name="redeem_points" value="150"> (max. 150)
				<?php }else echo 'Minimum of 150 points required to redeem';?>
			</div>	
		</td>
	</tr>
	<tr><td colspan=2>Total amount to be collected from member :</td><td><?=$total?></td></tr>		
</tbody>
</table>
</div>

<script>
function show_scheme_sales()
{
	fid=$("#i_fid").val();
	$('#super_scheme_sales tbody').html("");
	$.post("<?=site_url("admin/pnh_jx_show_super_scheme_sales_statics")?>",{fid:fid},function(result){
		if(result.status == 'success')
		{
			$.each(result.super_schsales,function(k,v){
				 var tblRow =
					 "<tr>"
					  +"<td>"+v.menu_name+"</td>"
					  +"<td>"+v.brand_name+"</td>"
					  +"<td>"+v.cat_name+"</td>"
					  +"<td>"+v.ttl_sales+"</td>"
					  +"<td>"+v.super_scheme_target+"</td>"
					  +"</tr>"
					  $(tblRow).appendTo("#super_scheme_sales tbody");
			});
		}else
		{
			$("#super_schsales").html(result.error);
			$("#super_schsales").html("loading...");
		}
	},'json');
}

</script>

<style>
.module{background: none;}	
.module h4{margin:4px 0px;}
.module{margin:0px;}
</style>



