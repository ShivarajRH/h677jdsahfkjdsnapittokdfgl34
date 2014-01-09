<?php

class Apimodel extends Model{
	
	function api_auth($aid)
	{
		$sql="select * from king_api_logins where auth=?";
		$q=$this->db->query($sql,array(md5($aid)));
		if($q->num_rows()==1)
		{
			$this->db->query("update king_api_logins set last_login=? where id=?",array(time(),$q->row()->id));
			return true;
		}
		return false;
	}
	
	
	function getgroupdeals($limit=0)
	{
		$sql="select item.name,deal.dealtype,deal.startdate,deal.enddate,brand.name as brandname,item.orgprice as mrp,item.price,brand.logoid as brandlogo,replace(item.url,' ','-') as url,item.quantity,item.available as sold,item.pic from king_deals as deal join king_brands as brand on brand.id=deal.brandid join king_dealitems as item on item.dealid=deal.dealid where deal.dealtype='1' and deal.publish='1' and (".time()." between deal.startdate and deal.enddate) order by deal.enddate asc";
		if($limit!=0)
			$sql.=" limit $limit";
		return $this->db->query($sql)->result_array();
	}

}

