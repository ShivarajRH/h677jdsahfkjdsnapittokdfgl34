<h3 id="dr_loading">Loading...</h3>


<div class="container" id="dr_show_after" style="display:none;">
<?php $cnt=$this->pettakam->get("deals_report"); 




if(!$cnt){
$cnt['time']=time();
ob_start();

?>

<h2>Products report</h2>
<div style="max-height:400px;overflow:auto;display:inline-block;">
<table class="datagrid">
<thead class="fixed"><tr><Th>Brand Name</Th><th>Total products</th><Th>Sourcable</Th><th>Not sourcable</th><th>SNP deals</th><th>PNH deals</th><th>Orphan products</th><th>products not linked to PNH deals</th></tr></thead>
<tbody>
<tr class="fake"><Th>Brand Name</Th><th>Total products</th><Th>Sourcable</Th><th>Not sourcable</th><th>SNP deals</th><th>PNH deals</th><th>Orphan products</th><th>products not linked to PNH deals</th></tr>
<?php foreach($this->db->query("select id,name from king_brands order by name asc")->result_array() as $b){?>
<tr>
<td><a target="_blank" href="<?=site_url("admin/viewbrand/{$b['id']}")?>"><?=$b['name']?></a></td>
<td><a href="javascript:void(0)" onclick='show_products("select * from m_product_info where brand_id=<?=$b['id']?>")'><?=$this->db->query("select count(1) as l from m_product_info where brand_id=?",$b['id'])->row()->l?></a></td>
<td><a href="javascript:void(0)" onclick='show_products("select * from m_product_info where is_sourceable=1 and brand_id=<?=$b['id']?>")'><?=$this->db->query("select count(1) as l from m_product_info where brand_id=? and is_sourceable=1",$b['id'])->row()->l?></a></td>
<td><a href="javascript:void(0)" onclick='show_products("select * from m_product_info where is_sourceable=0 and brand_id=<?=$b['id']?>")'><?=$this->db->query("select count(1) as l from m_product_info where brand_id=? and is_sourceable=0",$b['id'])->row()->l?></a></td>
<td>
<a href="javascript:void(0)" onclick='show_products("select p.* from m_product_info p join m_product_deal_link l on l.product_id=p.product_id join king_dealitems i on i.is_pnh=0 and i.id=l.itemid where brand_id=<?=$b['id']?> group by l.product_id")'><?=$this->db->query("select count(distinct p.product_id) as l from m_product_info p join m_product_deal_link dl on dl.product_id=p.product_id join king_dealitems i on i.id=dl.itemid where i.is_pnh=0 and p.brand_id=?",$b['id'])->row()->l?></a> products to
<a href="javascript:void(0)" onclick='show_deals("select i.name,i.orgprice as mrp,i.price,i.pnh_id,i.id from m_product_info p join m_product_deal_link dl on dl.product_id=p.product_id join king_dealitems i on i.id=dl.itemid where i.is_pnh=0 and p.brand_id=<?=$b['id']?>")'><?=$this->db->query("select count(distinct i.id) as l from m_product_info p join m_product_deal_link dl on dl.product_id=p.product_id join king_dealitems i on i.id=dl.itemid where i.is_pnh=0 and p.brand_id=?",$b['id'])->row()->l?></a> deals
</td>
<td>
<a href="javascript:void(0)" onclick='show_products("select p.* from m_product_info p join m_product_deal_link l on l.product_id=p.product_id join king_dealitems i on i.is_pnh=1 and i.id=l.itemid where brand_id=<?=$b['id']?> group by l.product_id")'><?=$this->db->query("select count(distinct p.product_id) as l from m_product_info p join m_product_deal_link dl on dl.product_id=p.product_id join king_dealitems i on i.id=dl.itemid where i.is_pnh=1 and p.brand_id=?",$b['id'])->row()->l?></a> products to
<a href="javascript:void(0)" onclick='show_deals("select i.name,i.orgprice as mrp,i.price,i.pnh_id,i.id from m_product_info p join m_product_deal_link dl on dl.product_id=p.product_id join king_dealitems i on i.id=dl.itemid where i.is_pnh=1 and p.brand_id=<?=$b['id']?>")'><?=$this->db->query("select count(distinct i.id) as l from m_product_info p join m_product_deal_link dl on dl.product_id=p.product_id join king_dealitems i on i.id=dl.itemid where i.is_pnh=1 and p.brand_id=?",$b['id'])->row()->l?></a> deals
</td>
<td><a href="javascript:void(0)" onclick='show_products("select p.* from m_product_info p where p.brand_id=<?=$b['id']?> and p.product_id not in (select product_id from m_product_deal_link)")'><?=$this->db->query("select count(1) as l from m_product_info p where p.brand_id=? and p.product_id not in (select product_id from m_product_deal_link)",$b['id'])->row()->l?></a></td>
<td><a href="javascript:void(0)" onclick='show_products("select * from m_product_info where brand_id=<?=$b['id']?> and product_id not in  (select product_id from m_product_deal_link dl join king_dealitems i on i.id=dl.itemid where i.is_pnh=1)")'><?=$this->db->query("select count(1) as l from m_product_info p where p.brand_id=? and p.product_id not in (select product_id from m_product_deal_link dl join king_dealitems i on i.id=dl.itemid where i.is_pnh=1)",$b['id'])->row()->l?></a></td>
</tr>
<?php }?>
</tbody>
</table>
</div>

<br><br>

<h2>Deals Report</h2>


<h3 style="margin-bottom:0px;">Snapittoday deals</h3>

<div class="dash_bar">Active &amp; published Deals : <span><?=$this->db->query("select count(1) as l from king_deals d join king_dealitems i on i.dealid=d.dealid and i.is_pnh=0 where ".time()." between startdate and enddate and publish=1")->row()->l?></span></div>

<div class="dash_bar">Expired but published Deals : <span><?=$this->db->query("select count(1) as l from king_deals d join king_dealitems i on i.dealid=d.dealid and i.is_pnh=0 where enddate < ".time()." and publish=1")->row()->l?></span></div>

<div class="dash_bar">Active but Unpublished Deals : <span><?=$this->db->query("select count(1) as l from king_deals d join king_dealitems i on i.dealid=d.dealid and i.is_pnh=0 where ".time()." between startdate and enddate and publish=0")->row()->l?></span></div>

<div class="clear"></div>

<div class="dash_bar">Active, stock available marked &amp; published : <span><?=$this->db->query("select count(1) as l from king_deals d join king_dealitems i on i.dealid=d.dealid and i.is_pnh=0 and i.live=1 where ".time()." between startdate and enddate and publish=1")->row()->l?></span></div>

<div class="dash_bar">Active &amp; published but marked as out of stock : <span><?=$this->db->query("select count(1) as l from king_deals d join king_dealitems i on i.dealid=d.dealid and i.is_pnh=0 and i.live=0 where ".time()." between startdate and enddate and publish=1")->row()->l?></span></div>

<div class="clear"></div>

<div style="float:left">
<h4 style="margin-bottom:0px;">Brands report</h4>
<div style="overflow:auto;max-height:400px;">
<table class="datagrid">
<thead class="fixed"><Tr><th>Sno</th><th>Brand</th><th>Published</th><th>Unpublished</th><th>Marked as <br>stock available</th><th>Marked as <bR>out of stock</th></Tr></thead>
<tbody>
<Tr class="fake"><th>Sno</th><th>Brand</th><th>Published</th><th>Unpublished</th><th>Marked as <br>stock available</th><th>Marked as <bR>out of stock</th></Tr>
<?php $i=1; foreach($this->db->query("select id,name from king_brands order by name asc")->result_array() as $b){?>
<tR>
<td><?=$i++?></td>
<td><a target="_blank" href="<?=site_url("admin/viewbrand/{$b['id']}")?>"><?=$b['name']?></a></td>
<td><a href="javascript:void(0)" onclick='show_deals("select i.name,i.orgprice as mrp,i.price,i.pnh_id,i.id from king_deals d join king_dealitems i on i.dealid=d.dealid where d.brandid=<?=$b['id']?> and d.publish=1 and i.is_pnh=0")'><?=$this->db->query("select count(1) as l from king_deals d join king_dealitems i on i.dealid=d.dealid and i.is_pnh=0 where publish=1 and brandid=?",$b['id'])->row()->l?></a></td>
<td><a href="javascript:void(0)" onclick='show_deals("select i.name,i.orgprice as mrp,i.price,i.pnh_id,i.id from king_deals d join king_dealitems i on i.dealid=d.dealid where d.brandid=<?=$b['id']?> and d.publish=0 and i.is_pnh=0")'><?=$this->db->query("select count(1) as l from king_deals d join king_dealitems i on i.dealid=d.dealid and i.is_pnh=0 where publish=0 and brandid=?",$b['id'])->row()->l?></a></td>
<td><a href="javascript:void(0)" onclick='show_deals("select i.name,i.orgprice as mrp,i.price,i.pnh_id,i.id from king_deals d join king_dealitems i on i.dealid=d.dealid where d.brandid=<?=$b['id']?> and d.publish=1 and i.is_pnh=0 and i.live=1")'><?=$this->db->query("select count(1) as l from king_deals d join king_dealitems i on i.dealid=d.dealid and i.live=1 and i.is_pnh=0 where publish=1 and brandid=?",$b['id'])->row()->l?></a></td>
<td><a href="javascript:void(0)" onclick='show_deals("select i.name,i.orgprice as mrp,i.price,i.pnh_id,i.id from king_deals d join king_dealitems i on i.dealid=d.dealid where d.brandid=<?=$b['id']?> and d.publish=1 and i.is_pnh=0 and i.live=0")'><?=$this->db->query("select count(1) as l from king_deals d join king_dealitems i on i.dealid=d.dealid and i.live=0 and i.is_pnh=0 where publish=1 and brandid=?",$b['id'])->row()->l?></a></td>
</tR>
<?php }?>
</tbody>
</table>
</div>
</div>

<div style="float:left;padding-left:10px;">
<h4 style="margin-bottom:0px;">Categories report</h4>
<div style="overflow:auto;max-height:400px;">
<table class="datagrid">
<thead class="fixed"><Tr><th>Sno</th><th>Category</th><th>Published</th><th>Unpublished</th><th>Marked as <br>stock available</th><th>Marked as <bR>out of stock</th></Tr></thead>
<tbody>
<Tr class="fake"><th>Sno</th><th>Category</th><th>Published</th><th>Unpublished</th><th>Marked as <br>stock available</th><th>Marked as <bR>out of stock</th></Tr>
<?php $i=1; foreach($this->db->query("select id,name from king_categories order by name asc")->result_array() as $b){?>
<tR>
<td><?=$i++?></td>
<td><a target="_blank" href="<?=site_url("admin/viewcat/{$b['id']}")?>"><?=$b['name']?></a></td>
<td><a href="javascript:void(0)" onclick='show_deals("select i.name,i.orgprice as mrp,i.price,i.pnh_id,i.id from king_deals d join king_dealitems i on i.dealid=d.dealid where d.catid=<?=$b['id']?> and d.publish=1 and i.is_pnh=0")'><?=$this->db->query("select count(1) as l from king_deals d join king_dealitems i on i.dealid=d.dealid and i.is_pnh=0 where publish=1 and catid=?",$b['id'])->row()->l?></a></td>
<td><a href="javascript:void(0)" onclick='show_deals("select i.name,i.orgprice as mrp,i.price,i.pnh_id,i.id from king_deals d join king_dealitems i on i.dealid=d.dealid where d.catid=<?=$b['id']?> and d.publish=0 and i.is_pnh=0")'><?=$this->db->query("select count(1) as l from king_deals d join king_dealitems i on i.dealid=d.dealid and i.is_pnh=0 where publish=0 and catid=?",$b['id'])->row()->l?></a></td>
<td><a href="javascript:void(0)" onclick='show_deals("select i.name,i.orgprice as mrp,i.price,i.pnh_id,i.id from king_deals d join king_dealitems i on i.dealid=d.dealid where d.catid=<?=$b['id']?> and d.publish=1 and i.is_pnh=0 and i.live=1")'><?=$this->db->query("select count(1) as l from king_deals d join king_dealitems i on i.dealid=d.dealid and i.live=1 and i.is_pnh=0 where publish=1 and catid=?",$b['id'])->row()->l?></a></td>
<td><a href="javascript:void(0)" onclick='show_deals("select i.name,i.orgprice as mrp,i.price,i.pnh_id,i.id from king_deals d join king_dealitems i on i.dealid=d.dealid where d.catid=<?=$b['id']?> and d.publish=1 and i.is_pnh=0 and i.live=0")'><?=$this->db->query("select count(1) as l from king_deals d join king_dealitems i on i.dealid=d.dealid and i.live=0 and i.is_pnh=0 where publish=1 and catid=?",$b['id'])->row()->l?></a></td>
</tR>
<?php }?>
</tbody>
</table>
</div>
</div>

<div class="clear"></div>

<h3 style="margin-bottom:0px;">Paynearhome deals</h3>

<div class="dash_bar">Active &amp; published Deals : <span><?=$this->db->query("select count(1) as l from king_deals d join king_dealitems i on i.dealid=d.dealid and i.is_pnh=1 where ".time()." between startdate and enddate and publish=1")->row()->l?></span></div>

<div class="dash_bar">Expired but published Deals : <span><?=$this->db->query("select count(1) as l from king_deals d join king_dealitems i on i.dealid=d.dealid and i.is_pnh=1 where enddate < ".time()." and publish=1")->row()->l?></span></div>

<div class="dash_bar">Active but Unpublished Deals : <span><?=$this->db->query("select count(1) as l from king_deals d join king_dealitems i on i.dealid=d.dealid and i.is_pnh=1 where ".time()." between startdate and enddate and publish=0")->row()->l?></span></div>

<div class="clear"></div>

<div class="dash_bar">Active, stock available marked &amp; published : <span><?=$this->db->query("select count(1) as l from king_deals d join king_dealitems i on i.dealid=d.dealid and i.is_pnh=1 and i.live=1 where ".time()." between startdate and enddate and publish=1")->row()->l?></span></div>

<div class="dash_bar">Active &amp; published but marked as out of stock : <span><?=$this->db->query("select count(1) as l from king_deals d join king_dealitems i on i.dealid=d.dealid and i.is_pnh=1 and i.live=0 where ".time()." between startdate and enddate and publish=1")->row()->l?></span></div>

<div class="clear"></div>

<div style="float:left">
<h4 style="margin-bottom:0px;">Brands report</h4>
<div style="overflow:auto;max-height:400px;">
<table class="datagrid">
<thead class="fixed"><Tr><th>Sno</th><th>Brand</th><th>Enabled</th><th>Disabled</th></Tr></thead>
<tbody>
<Tr class="fake"><th>Sno</th><th>Brand</th><th>Enabled</th><th>Disabled</th></Tr>
<?php $i=1; foreach($this->db->query("select id,name from king_brands order by name asc")->result_array() as $b){?>
<tR>
<td><?=$i++?></td>
<td><a target="_blank" href="<?=site_url("admin/viewbrand/{$b['id']}")?>"><?=$b['name']?></a></td>
<td><a href="javascript:void(0)" onclick='show_deals("select i.name,i.orgprice as mrp,i.price,i.pnh_id,i.id from king_deals d join king_dealitems i on i.dealid=d.dealid where d.brandid=<?=$b['id']?> and d.publish=1 and i.is_pnh=1")'><?=$this->db->query("select count(1) as l from king_deals d join king_dealitems i on i.dealid=d.dealid and i.is_pnh=1 where publish=1 and brandid=?",$b['id'])->row()->l?></a></td>
<td><a href="javascript:void(0)" onclick='show_deals("select i.name,i.orgprice as mrp,i.price,i.pnh_id,i.id from king_deals d join king_dealitems i on i.dealid=d.dealid where d.brandid=<?=$b['id']?> and d.publish=0 and i.is_pnh=1")'><?=$this->db->query("select count(1) as l from king_deals d join king_dealitems i on i.dealid=d.dealid and i.is_pnh=1 where publish=0 and brandid=?",$b['id'])->row()->l?></a></td>
</tR>
<?php }?>
</tbody>
</table>
</div>
</div>

<div style="float:left;padding-left:10px;">
<h4 style="margin-bottom:0px;">Categories report</h4>
<div style="overflow:auto;max-height:400px;">
<table class="datagrid">
<thead class="fixed"><Tr><th>Sno</th><th>Category</th><th>Enabled</th><th>Disabled</th></Tr></thead>
<tbody>
<Tr class="fake"><th>Sno</th><th>Category</th><th>Enabled</th><th>Disabled</th></Tr>
<?php $i=1; foreach($this->db->query("select id,name from king_categories order by name asc")->result_array() as $b){?>
<tR>
<td><?=$i++?></td>
<td><a target="_blank" href="<?=site_url("admin/viewcat/{$b['id']}")?>"><?=$b['name']?></a></td>
<td><a href="javascript:void(0)" onclick='show_deals("select i.name,i.orgprice as mrp,i.price,i.pnh_id,i.id from king_deals d join king_dealitems i on i.dealid=d.dealid where d.catid=<?=$b['id']?> and d.publish=1 and i.is_pnh=1")'><?=$this->db->query("select count(1) as l from king_deals d join king_dealitems i on i.dealid=d.dealid and i.is_pnh=1 where publish=1 and catid=?",$b['id'])->row()->l?></a></td>
<td><a href="javascript:void(0)" onclick='show_deals("select i.name,i.orgprice as mrp,i.price,i.pnh_id,i.id from king_deals d join king_dealitems i on i.dealid=d.dealid where d.catid=<?=$b['id']?> and d.publish=0 and i.is_pnh=1")'><?=$this->db->query("select count(1) as l from king_deals d join king_dealitems i on i.dealid=d.dealid and i.is_pnh=1 where publish=0 and catid=?",$b['id'])->row()->l?></a></td>
</tR>
<?php }?>
</tbody>
</table>
</div>
</div>

<div class="clear"></div>



<div id="deals_report_frag" style="opacity:0.9;background:#fff;padding:20px;position:fixed;top:140px;left:200px;width:900px;border:1px solid #aaa;height:450px;display:none;">
<div id="deals_report_frag_cont" style="opacity:1;overflow:auto;height:420px;"></div>
<a href="javascript:void(0)" onclick='$("#deals_report_frag").hide()'>close</a>
</div>


<div id="prods_report_frag" style="opacity:0.9;background:#fff;padding:20px;position:fixed;top:140px;left:200px;width:900px;border:1px solid #aaa;height:450px;display:none;">
<div id="prods_report_frag_cont" style="opacity:1;overflow:auto;height:420px;"></div>
<a href="javascript:void(0)" onclick='$("#prods_report_frag").hide()'>close</a>
</div>

<?php 
$cnt['echo']=ob_get_contents();
$this->pettakam->store('deals_report',$cnt,30*24*60*60);
ob_end_clean();
}
$sec=time()-$cnt['time'];
?>
<div class="dash_bar">
Data cache Age : <span><?php $d2 = new DateTime(); $d2->add(new DateInterval('PT'.$sec.'S')); $iv = $d2->diff(new DateTime()); echo $iv->format("%a days %i mins %s secs");?></span>
<input type="button" value="Update" onclick='location="<?=site_url("admin/clear_dealsrep_cache")?>"'>
</div>


<div class="clear"></div>

<?=$cnt['echo'];?>

</div>

<script>
function show_deals(p)
{
	$("#prods_report_frag").hide();
	
	$("#deals_report_frag_cont").html("Loading...");
	$("#deals_report_frag").show();
	
	$.post('<?=site_url("admin/jx_deals_report")?>',{p:p},function(data){
		$("#deals_report_frag_cont").html(data);
		$("#deals_report_frag").show();
	});
}

function show_products(p)
{
	$("#deals_report_frag").hide();
	
	$("#prods_report_frag_cont").html("Loading...");
	$("#prods_report_frag").show();
	
	$.post('<?=site_url("admin/jx_deals_report_prod")?>',{p:p},function(data){
		$("#prods_report_frag_cont").html(data);
		$("#prods_report_frag").show();
	});
}

$(function(){
	$("#dr_loading").hide();
	$("#dr_show_after").show();
	$(document).keyup(function(e){
		if(e.which==27)
			$("#deals_report_frag,#prods_report_frag").hide();
	});
	$("#dr_show_after .fixed").each(function(){
		$("th",$(this)).each(function(i){
			w=$($(".fake th",$(this).parents("table").get(0)).get(i)).width();
			if($(this).next().length==0)
				w=w+15;
			$(this).css("width",w+"px");
		});
		$(this).css("position","absolute");
		$(this).css("top",$(this).parent().parent().position().top+"px");
//		$(this).parent().css("width",$(this).width()+"px");
	});
	
});
</script>
<style>
.fake th{
background:#fff;
}
#dr_show_after thead{
}
</style>

<?php

