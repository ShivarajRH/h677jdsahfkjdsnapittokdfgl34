<?php

class Favmodel extends Model{
	
	function getfavcats()
	{
		return $this->db->query("select distinct m.id,m.id as catid,m.name as cat,m.url,item.pic from king_dealitems item join king_deals deal on deal.dealid=item.dealid and ".time()." between deal.startdate and deal.enddate and deal.publish=1 join king_categories cat on cat.id=deal.catid join king_categories m on (m.id=cat.type or m.id=cat.id) and m.type=0 where item.live=1 and item.favs=1 group by m.id")->result_array();
	}
	
	function getprods($curl)
	{
		return $this->db->query("select item.orgprice,cat.name as cat,item.name,item.id,item.pic from king_categories cat join king_categories scat on scat.id=cat.id or scat.type=cat.id join king_deals deal on deal.catid=scat.id and ".time()." between deal.startdate and deal.enddate and deal.publish=1 join king_dealitems item on item.dealid=deal.dealid and item.favs=1 and item.live=1 where cat.url=?",$curl)->result_array();
	}
	
	function getfavcatforprod($id)
	{
		$r=$this->db->query("select m.id from king_dealitems i join king_deals d on d.dealid=i.dealid and ".time()." between d.startdate and d.enddate and d.publish=1 join king_categories c on c.id=d.catid join king_categories m on (m.id=c.type or m.id=c.id) and m.type=0 where i.id=?",$id)->row();
		if(empty($r))
			return false;
		return $r->id;
	}
	
	function getprodforfavcatselcted($cat)
	{
		$user=$this->session->userdata("user");
		$r=$this->db->query("select item.name,item.price from king_favs f join king_dealitems item on item.id=f.itemid where f.catid=? and f.userid=? and f.expires_on>".time(),array($cat,$user['userid']))->row_array();
		return $r;
	}
	
	function setfav($id)
	{
		$user=$this->session->userdata("user");
		$cat=$this->db->query("select deal.catid from king_dealitems i join king_deals deal on deal.dealid=i.dealid where i.id=?",$id)->row()->catid;
		$type=$this->db->query("select type from king_categories where id=?",$cat)->row()->type;
		if($type!=0)
			$cat=$type;
		$inp=array($user['userid'],$id,$cat,time()+(FAV_EXPIRY*24*60*60),time());
		$this->db->query("insert into king_favs(userid,itemid,catid,expires_on,added_on) values(?,?,?,?,?)",$inp);
	}
	
	function getlockedcats()
	{
		$user=$this->session->userdata("user");
		if(!$user)
			return array();
		$favs=$this->db->query("select f.catid from king_favs f join king_dealitems i on i.id=f.itemid and i.live=1 join king_deals d on d.dealid=i.dealid and ".time()." between d.startdate and d.enddate and d.publish=1 where f.userid=? and f.expires_on>".time(),$user['userid'])->result_array();
		$cats=array();
		foreach($favs as $f)
			$cats[]=$f['catid'];
		return $cats;	
	}
	
	function getallfavs()
	{
		$user=$this->session->userdata("user");
		if(!$user)
			return array();
		return $this->db->query("select f.*,i.orgprice,i.name from king_favs f join king_dealitems i on i.id=f.itemid and i.favs=1 where userid=? and expires_on>".time(),$user['userid'])->result_array();
	}
	
	function getallfavids()
	{
		$user=$this->session->userdata("user");
		if(!$user)
			return array();
		$favs=$this->db->query("select f.*,i.orgprice,i.name from king_favs f join king_dealitems i on i.id=f.itemid where userid=? and expires_on>".time(),$user['userid'])->result_array();
		$favids=array();
		foreach($favs as $f)
			$favids[]=$f['itemid'];
		return $favids;
	}
	
}