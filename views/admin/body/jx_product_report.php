<?php 
		if($pnh_menu_id)
		{
			$sql="select distinct a.brandid as id,c.name from king_deals a 
						join king_dealitems b on a.dealid = b.dealid and is_pnh = 1 
						join king_brands c on c.id=a.brandid where menuid=?";
			if($alpha)
				$sql.=" and c.name like '$alpha%' ";
			
			$sql.=' order by name asc ';
			
			$brand_list=$this->db->query($sql,$pnh_menu_id)->result_array();
		}else if($sit_menu_id){
			$sql="select distinct a.brandid as id,c.name from king_deals a 
						join king_dealitems b on a.dealid = b.dealid and is_pnh!=1 
						join king_brands c on c.id=a.brandid where menuid=?";
			if($alpha)
				$sql.=" and c.name like '$alpha%' ";
			
			$sql.=' order by c.name asc ';
			
			$brand_list=$this->db->query($sql,$sit_menu_id)->result_array();
		}else{
			
			$sql="select id,name from king_brands where 1";
			if($alpha)
				$sql.=" and name like '$alpha%' ";
			
			$sql.=' order by name asc ';
			
			$brand_list=$this->db->query($sql)->result_array();
		}
		
		
	?>

<div class="stats_bar" style="overflow:hidden;">
	
	<table width="100%" cellpadding="0" cellspacing="0" style="font-size: 12px;"  >
		<tr>
			<td width="10%" align="left">
				<span class="stats_summary" style="padding:5px;"><b>Total Brands :</b> <b style="font-size: 14px;"></span><?php echo count($brand_list); ?></b></span>
			</td>
			<td  align="center">
				<div id="franby_aphabets" class="fil_alpha" >
					<a href="javascript:void(0)" onclick=showby_aplha('') class="<?php echo (($alpha===0)?'selected':'');?>" >ALL</a>
					<?php
						$chrs = 'abcdefghijklmnopqrstuvwxyz';
						for($i=0;$i<strlen($chrs);$i++)
						{
							echo '<a href="javascript:void(0)" onclick=showby_aplha("'.$chrs[$i].'") class="'.(($alpha===$chrs[$i])?'selected':'').'">'.$chrs[$i].'</a>';
						}
					?>
				</div>
			</td>
		</tr>
	</table>
	
</div>





<div style="max-height:400px;overflow:auto;display:inline-block;width:100% !important;">
<table class="datagrid" width="100%" >
	<thead class="fixed"><tr><Th>Brand Name</Th><th>Total products</th><Th>Sourcable</Th><th>Not sourcable</th><th>SNP deals</th><th>PNH deals</th><th>Orphan products</th><th>products not linked to PNH deals</th></tr></thead>
	<tbody>
	<?php foreach($brand_list as $b){?>
	<tr>
	<td><a target="_blank" href="<?=site_url("admin/viewbrand/{$b['id']}")?>"><?=$b['name']?></a></td>
	<td><a href="javascript:void(0)" onclick='show_products("select * from m_product_info where brand_id=<?=$b['id']?>")'><?=$this->db->query("select count(1) as l from m_product_info where brand_id=?",$b['id'])->row()->l?></a></td>
	<td><a href="javascript:void(0)" onclick='show_products("select * from m_product_info where is_sourceable=1 and brand_id=<?=$b['id']?>")'><?=$this->db->query("select count(1) as l from m_product_info where brand_id=? and is_sourceable=1",$b['id'])->row()->l?></a></td>
	<td><a href="javascript:void(0)" onclick='show_products("select * from m_product_info where is_sourceable=0 and brand_id=<?=$b['id']?>")'><?=$this->db->query("select count(1) as l from m_product_info where brand_id=? and is_sourceable=0",$b['id'])->row()->l?></a></td>
	<td>
	<a href="javascript:void(0)" onclick='show_products("select p.* from m_product_info p join m_product_deal_link l on l.product_id=p.product_id join king_dealitems i on i.is_pnh=0 and i.id=l.itemid where brand_id=<?=$b['id']?> group by l.product_id")'><?=$this->db->query("select count(distinct p.product_id) as l from m_product_info p join m_product_deal_link dl on dl.product_id=p.product_id join king_dealitems i on i.id=dl.itemid where i.is_pnh=0 and p.brand_id=?",$b['id'])->row()->l?></a> products to
	<a href="javascript:void(0)" onclick='show_deals("select i.dealid,i.name,i.orgprice as mrp,i.price,i.pnh_id,i.id from m_product_info p join m_product_deal_link dl on dl.product_id=p.product_id join king_dealitems i on i.id=dl.itemid where i.is_pnh=0 and p.brand_id=<?=$b['id']?>")'><?=$this->db->query("select count(distinct i.id) as l from m_product_info p join m_product_deal_link dl on dl.product_id=p.product_id join king_dealitems i on i.id=dl.itemid where i.is_pnh=0 and p.brand_id=?",$b['id'])->row()->l?></a> deals
	</td>
	<td>
	<a href="javascript:void(0)" onclick='show_products("select p.* from m_product_info p join m_product_deal_link l on l.product_id=p.product_id join king_dealitems i on i.is_pnh=1 and i.id=l.itemid where brand_id=<?=$b['id']?> group by l.product_id")'><?=$this->db->query("select count(distinct p.product_id) as l from m_product_info p join m_product_deal_link dl on dl.product_id=p.product_id join king_dealitems i on i.id=dl.itemid where i.is_pnh=1 and p.brand_id=?",$b['id'])->row()->l?></a> products to
	<a href="javascript:void(0)" onclick='show_deals("select i.dealid,i.name,i.orgprice as mrp,i.price,i.pnh_id,i.id from m_product_info p join m_product_deal_link dl on dl.product_id=p.product_id join king_dealitems i on i.id=dl.itemid where i.is_pnh=1 and p.brand_id=<?=$b['id']?>")'><?=$this->db->query("select count(distinct i.id) as l from m_product_info p join m_product_deal_link dl on dl.product_id=p.product_id join king_dealitems i on i.id=dl.itemid where i.is_pnh=1 and p.brand_id=?",$b['id'])->row()->l?></a> deals
	</td>
	<td><a href="javascript:void(0)" onclick='show_products("select p.* from m_product_info p where p.brand_id=<?=$b['id']?> and p.product_id not in (select product_id from m_product_deal_link)")'><?=$this->db->query("select count(1) as l from m_product_info p where p.brand_id=? and p.product_id not in (select product_id from m_product_deal_link)",$b['id'])->row()->l?></a></td>
	<td><a href="javascript:void(0)" onclick='show_products("select * from m_product_info where brand_id=<?=$b['id']?> and product_id not in  (select product_id from m_product_deal_link dl join king_dealitems i on i.id=dl.itemid where i.is_pnh=1)")'><?=$this->db->query("select count(1) as l from m_product_info p where p.brand_id=? and p.product_id not in (select product_id from m_product_deal_link dl join king_dealitems i on i.id=dl.itemid where i.is_pnh=1)",$b['id'])->row()->l?></a></td>
	</tr>
	<?php }?>
	</tbody>
</table>
</div>
