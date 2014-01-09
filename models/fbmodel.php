<?php
/**
 * Model for front end of viakingsale
 * 
 * @author Vimal <vimal@viaadz.com>
 * @package viakingsale
 * @subpackage frontend
 * @since 2009/12
 * @version 0.9.3
 */

/**
 * Model class for front end
 * 
 * Contains all database functions needed for viakingsale
 * 
 * @author Vimal <vimal@viaadz.com>
 * @version 0.9
 */
class Fbmodel extends Model
{
	
	/**
	 * Check user account for given email and password match
	 * 
	 * @param string $email Email address
	 * @param string $password Password
	 * @return bool true on correct match and authentication
	 */
	function checkuser($email,$password)
	{
		$sql="select password from king_users where email=? and special=0";
		$q=$this->db->query($sql,array($email));
		if($q->num_rows()==1)
		{
			$r=$q->row();
			if($r->password==md5($password))
				return true;
		}
		return false;
	}
	
	function getallreadydeals()
	{
		$sql="select replace(item.url,' ','-') as url,brand.logoid as brandlogo,brand.name as brand,cat.name as category,item.name,item.pic,item.price,item.orgprice,item.dealid,item.id,deal.startdate,deal.enddate from king_dealitems as item join king_deals as deal on ? between deal.startdate and deal.enddate and deal.publish=1 join king_categories as cat on deal.catid=cat.id join king_brands as brand on brand.id=deal.brandid where item.dealid=deal.dealid and item.live=1 order by cat.prior asc";
		return $this->db->query($sql,array(time()))->result_array();
	}
	
	function getallactivedeals()
	{
//		$sql="select url,brand.logoid as brandlogo,brand.name as brand,cat.name as category,item.name,item.pic,item.price,item.orgprice,item.dealid,item.id,deal.startdate,deal.enddate from king_dealitems as item join king_deals as deal on ? between deal.startdate and deal.enddate and deal.publish=1 join king_categories as cat on deal.catid=cat.id and item.live=1 join king_brands as brand on brand.id=deal.brandid where item.dealid=deal.dealid and deal.dealtype=1 order by cat.prior asc, deal.enddate asc";
//		return $this->db->query($sql,array(time()))->result_array();
		$sql="select replace(item.url,' ','-') as url,brand.logoid as brandlogo,brand.name as brand,cat.name as category,item.name,item.pic,item.price,item.orgprice,item.dealid,item.id,deal.startdate,deal.enddate from king_dealitems as item join king_deals as deal on ? between deal.startdate and deal.enddate and deal.publish=1 and deal.dealtype=1 join king_categories as cat on deal.catid=cat.id join king_brands as brand on brand.id=deal.brandid where item.dealid=deal.dealid and item.live=1 order by cat.prior asc";
		return $this->db->query($sql,array(time()))->result_array();
	}
	
	function getallactivegroupdeals()
	{
		$sql="select replace(item.url,' ','-') as url,brand.logoid as brandlogo,brand.name as brand,cat.name as category,item.name,item.pic,item.price,item.orgprice,item.dealid,item.id,deal.startdate,deal.enddate from king_dealitems as item join king_deals as deal on ? between deal.startdate and deal.enddate and deal.publish=1 and deal.dealtype=2 join king_categories as cat on deal.catid=cat.id join king_brands as brand on brand.id=deal.brandid where item.dealid=deal.dealid and item.live=1 order by cat.prior asc";
		return $this->db->query($sql,array(time()))->result_array();
	}
	
	/**
	 * Get all category names with id from db
	 * 
	 * @return array combination of category id and name pair
	 */
	function getcategories()
	{
		$sql="select distinct(cat.id) as id,cat.name,cat.type from king_categories as cat join king_deals as deal on ".time()." between deal.startdate and deal.enddate and deal.publish=1 where cat.id=deal.catid or cat.id in (select type from king_categories as c join king_deals as d on ".time()." between d.startdate and d.enddate and d.publish=1 where c.id=d.catid) order by cat.name asc";
		$q=$this->db->query($sql);
		$data=array();
//		print_r($q->result_array());
		if($q->num_rows()>0)
		{
			foreach($q->result() as $r)
			{
				$cat=$r->type;
				if(!isset($data[$cat][0]))
					$data[$cat]=array();
				$d['id']=$r->id;
				$d['name']=$r->name;
//				print_r($r);
				array_push($data[$cat],$d);
			}
		}
		return $data;
	}
	
	function fb_getuidforinvite($inv)
	{
		$sql="select userid from king_users where SHA1(inviteid)=?";
		$q=$this->db->query($sql,array($inv));
		if($q->num_rows()==1)
			return $q->row()->userid;
		return 0;
	}
	
	function donespecialcot($url)
	{
		$this->db->query("update king_pricereqs set status=3 where url=?",array($url));
	}
	
	function getapi($salt)
	{
		return $this->db->query("select * from widgets where salt=?",array($salt))->row_array();
	}
	
	function getpr($id,$uid)
	{
		$sql="select p.userid,p.itemid,item.url as itemurl,p.price as reqprice,p.quantity,p.status,p.time,p.url,p.id,item.name,item.price,item.orgprice from king_pricereqs as p join king_dealitems as item on item.id=p.itemid where p.url=? and p.status=1 and p.userid=?";
		return $this->db->query($sql,array($id,$uid))->row_array();
	}
	
	function getmenu()
	{
		$menu[0]=$this->getcategories();
		$sql="select distinct(brand.id) as id,brand.name from king_brands as brand join king_deals as deal on deal.brandid=brand.id and ".time()." between deal.startdate and deal.enddate and deal.publish=1 order by name asc";
		$q=$this->db->query($sql);
		$menu[1]=$q->result_array();
		return $menu;
	}
	
	/**
	 * Get user details from account table for specific facebook user id
	 * 
	 * This function is no longer used!
	 * 
	 * @deprecated
	 * @param string $fb facebook user id
	 * @return bool|array Array with user details. False on failure
	 */
	function getuserbyfb($fb)
	{
		 $sql="select userid,name,email,inviteid from king_users where fb_userid=?";
		 $q=$this->db->query($sql,array($fb));
		 if($q->num_rows()==1)
		 	return $q->row_array();
		 return false;
	}
	
	/**
	 * Get category name for given category id
	 * 
	 * @param int $id category id
	 * @return string category name
	 */
	function getcategoryname($id)
	{
		$sql="select name from king_categories where id=?";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()==1)
			return $q->row()->name;
		return false;
	}
	
	/**
	 * Get details of active and inactive deals of all categories
	 * 
	 * @return bool|array array of deal details from DB. False on failure
	 */
	function getalldeals()
	{
		$sql="select distinct(brand.name),replace(item.url,' ','-') as url,item.orgprice,item.name as itemname,item.available,item.id as itemid,item.quantity,brand.name as brandname,item.price,cat.name as category,deal.tagline,deal.description,deal.dealid,deal.startdate,deal.enddate,item.pic,brand.id as brandid,brand.logoid as brandlogoid,deal.dealtype,brand.name from king_deals as deal join king_categories as cat on cat.id=deal.catid join king_brands as brand on brand.id=deal.brandid join king_dealitems as item on item.dealid=deal.dealid where deal.publish=1 and ".time()." between deal.startdate and deal.enddate order by deal.enddate asc limit 25";
//		$sql="select cat.name as category,deal.dealid,deal.startdate,deal.enddate,deal.pic,brand.name from king_deals as deal join king_brands as brand on brand.id=deal.brandid join king_categories as cat on cat.id=deal.catid where deal.publish=1 and deal.enddate>".time()." order by deal.enddate asc";
		$q=$this->db->query($sql);
		if($q->num_rows()>0)
			return $q->result_array();
		return false;
	}
	
	function getdealsforwidget()
	{
		$sql="select rand()*100 as rid,brand.logoid,item.url,item.orgprice,item.name,item.pic,item.price,item.id as id,brand.name as brandname,cat.name as category from king_deals as deal join king_dealitems as item on item.dealid=deal.dealid and item.live=1 join king_brands as brand on brand.id=deal.brandid join king_categories as cat on cat.id=deal.catid where deal.publish=1 and ".time()." between deal.startdate and deal.enddate order by rid asc";
		return $this->db->query($sql)->result_array();
	}
	
	function getcoupon($cid)
	{
		$sql="select * from king_coupons where id=?";
		return $this->db->query($sql,array($cid))->row_array();
	}
	
	function getminmaxprice($id)
	{
		$sql="select min(price) as min, min(orgprice) as minorg, max(orgprice) as maxorg, max(price) as max from king_dealitems where dealid=?";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()==1)
			return $q->row_array();
		return array();
	} 
	
	/**
	 * Get deal details for given deal id
	 * 
	 * Makes a join DB query to get details of deal and items with respect to given deal id 
	 * 
	 * @param int $id deal id
	 * @return bool|array Deal details as array. False on failure
	 */
	function getdealdetails($id)
	{
		$sql="select replace(item.url,' ','-') as url,deal.dealtype,deal.tagline,deal.brandid,cat.name as category,deal.startdate,deal.enddate,brand.name as brandname,brand.logoid as brandlogoid,deal.pic as dealpic,item.quantity,item.available,item.quantity,item.id,item.price,item.orgprice,item.name as itemname,item.pic,deal.description from king_dealitems as item join king_deals as deal on deal.dealid=item.dealid join king_brands as brand on brand.id=deal.brandid join king_categories as cat on cat.id=deal.catid where item.dealid=? order by item.price asc";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()>0)
			return $q->result_array();
		return false;
	}
	
	/**
	 * Get all orders made by given user
	 * 
	 * Also contains all details of the order made including item details
	 * 
	 * @param int $uid user id
	 * @return array order details as array
	 */
	function getorders($uid,$agent=false,$p=1)
	{
		if(!$agent)
		$sql="select ordert.id,ordert.shiptime,ordert.shipid,ordert.paid,ordert.quantity,ordert.status,ordert.time,item.name,brand.name as brandname from king_orders as ordert join king_dealitems as item on item.id=ordert.itemid join king_deals as deal on deal.dealid=item.dealid join king_brands as brand on brand.id=deal.brandid where ordert.userid=? order by ordert.time desc limit ".(($p-1)*20).",20";
		else
		$sql="select trans.via_transid,ordert.id,ordert.shiptime,ordert.shipid,ordert.paid,ordert.quantity,ordert.status,ordert.time,item.name,brand.name as brandname from king_orders as ordert join king_dealitems as item on item.id=ordert.itemid join king_agent_transactions as trans on trans.orderid=ordert.id join king_deals as deal on deal.dealid=item.dealid join king_brands as brand on brand.id=deal.brandid where ordert.userid=? order by ordert.time desc limit ".(($p-1)*20).",20";
		$q=$this->db->query($sql,array($uid));
		return $q->result_array();
	}
	
	function getorder($oid,$uid)
	{
		$sql="select ordert.medium,ordert.shipid,ordert.id,ordert.shiptime,ordert.ship_person,ordert.ship_address,ordert.ship_city,ordert.ship_pincode,ordert.ship_phone,ordert.shipid,ordert.paid,ordert.quantity,ordert.status,ordert.time,item.name,brand.name as brandname from king_orders as ordert join king_dealitems as item on item.id=ordert.itemid join king_deals as deal on deal.dealid=item.dealid join king_brands as brand on brand.id=deal.brandid where ordert.userid=? and ordert.id=?";
		$q=$this->db->query($sql,array($uid,$oid));
		return $q->row_array();
	}
	
	/**
	 * Get brand name and id for given brand name
	 * 
	 * @param string $name brand name
	 * @return bool|array array indexed with name and id. False on failure
	 */
	function getbrand($name)
	{
		$sql="select description,website,logoid,name,id from king_brands where name=?";
		$q=$this->db->query($sql,array($name));
		if($q->num_rows()==1)
			return $q->row_array();
		return false;
	}
	
	/**
	 * Get category name and id for given category name
	 * 
	 * @param string $name category name
	 * @return bool|array array indexed with name and id. False on failure
	 */
	function getcategory($name)
	{
		$sql="select name,id from king_categories where name=?";
		$q=$this->db->query($sql,array($name));
		if($q->num_rows()==1)
			return $q->row_array();
		return false;
	}
	
	/**
	 * Get all active and inactive deals for given brand id
	 * 
	 * @param int $brandid brand id
	 * @return bool|array details of all deals for brand. False on failure
	 */
	function getdealsforbrand($brandid)
	{
		$sql="select deal.dealtype,category.name as categoryname,deal.startdate,deal.enddate,deal.dealid,deal.brandid,deal.pic,deal.description,deal.tagline from king_deals as deal join king_categories as category on category.id=deal.catid where deal.brandid=? and deal.publish=1 and deal.enddate>".time()." order by deal.enddate desc";
//		$sql="select deal.dealid,deal.startdate,deal.enddate,deal.pic,brand.name from king_deals as deal join king_brands as brand on brand.id=deal.brandid where deal.catid=? and deal.publish=1 and deal.enddate>".time()." order by deal.enddate asc";
		$q=$this->db->query($sql,array($brandid));
		if($q->num_rows()>0)
			return $q->result_array();
		return false;
	}
	
	/**
	 * Get all active and inactive deals for given category id
	 * 
	 * @param int $catid category id
	 * @return bool|array details of all deals in category. False on failure
	 */
	function getdealsbycategory($catid)
	{
		$sql="select replace(item.url,' ','-') as url,item.id as itemid,item.quantity,item.available,item.name as itemname,deal.dealtype,deal.tagline,deal.dealid,deal.startdate,deal.enddate,item.pic,brand.name from king_deals as deal join king_brands as brand on brand.id=deal.brandid join king_dealitems as item on item.dealid=deal.dealid where (deal.catid=? or deal.catid in (select id from king_categories where type=?)) and deal.publish=1 and deal.enddate>".time()." order by deal.enddate asc";
		$q=$this->db->query($sql,array($catid,$catid));
		if($q->num_rows()>0)
			return $q->result_array();
		return false;
	}
	
	function getgroupdeals($limit=0)
	{
		$sql="select brand.name as brandname,brand.logoid,replace(item.url,' ','-') as url,item.id as itemid,item.quantity,item.available,item.name as itemname,deal.dealtype,deal.tagline,deal.dealid,deal.startdate,deal.enddate,item.pic,brand.name from king_deals as deal join king_brands as brand on brand.id=deal.brandid join king_dealitems as item on item.dealid=deal.dealid where deal.dealtype='1' and deal.publish='1' and (".time()." between deal.startdate and deal.enddate) order by deal.enddate asc";
		if($limit!=0)
			$sql.=" limit $limit";
		return $this->db->query($sql)->result_array();
	}
	
	function searchdeals($key)
	{
		$key="%$key%";
		$sql="select replace(item.url,' ','-') as url,item.orgprice,item.name as itemname,item.available,item.id as itemid,item.quantity,brand.name as brandname,item.price,cat.name as category,deal.tagline,deal.description,deal.dealid,deal.startdate,deal.enddate,item.pic,brand.id as brandid,brand.logoid as brandlogoid,deal.dealtype,brand.name from king_deals as deal join king_categories as cat on cat.id=deal.catid join king_brands as brand on brand.id=deal.brandid join king_dealitems as item on item.dealid=deal.dealid where (deal.keywords like ? or item.name like ?) and deal.publish=1 and deal.enddate>".time()." order by deal.enddate asc limit 25";
		return $this->db->query($sql,array($key,$key))->result_array();
	}
	
	function searchcats($key)
	{
		$key="%$key%";
		$sql="select * from king_categories where name like ?";
		return $this->db->query($sql,array($key,$key))->result_array();
	}
	
	function searchbrands($key)
	{
		$key="%$key%";
		$sql="select * from king_brands where name like ?";
		return $this->db->query($sql,array($key,$key))->result_array();
	}
	
	/**
	 * Get extra details of item (photos & videos)
	 * 
	 * @param int $id item id
	 * @return bool|array False on failure
	 */
	function getitemresources($id)
	{
		$sql="select type,id from king_resources where itemid=?";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()>0)
			return $q->result_array();
		return false;
	}
	
	/**
	 * Saves cart to DB
	 * 
	 * @param int $uid user id
	 * @param string $name cart name
	 * @return int cart id
	 */
	function savecart($uid,$name)
	{
		$sql="insert into king_savedcarts(userid,name) values(?,?)";
		$this->db->query($sql,array($uid,$name));
		return $this->db->insert_id();
	}
	
	/**
	 * Save cart items to DB
	 * 
	 * @param int $cartid cart id
	 * @param int $uid user id
	 * @param array $items array of item ids to save
	 * @return void
	 */
	function savecartitems($cartid,$uid,$items)
	{
//		print_r($items);
		if(!is_array($items) || count($items)==0)
			return;
		$sql="insert into king_savedcartitems(cartid,itemid,quantity) values";
		$i=0;
		$arr=array();
		foreach($items as $item)
		{
			if($i==0)
				$sql.='(?,?,?)';
			else
				$sql.=',(?,?,?)';
			$i++;
			array_push($arr,$cartid);
			array_push($arr,$item['id']);
			array_push($arr,$item['qty']);
		}
		$this->db->query($sql,$arr);
	}
	
	/**
	 * Get all saved carts for given user id
	 * 
	 * @param int $uid user id
	 * @return array
	 */
	function getsavedcarts($uid)
	{
		$sql="select * from king_savedcarts where userid=?";
		$q=$this->db->query($sql,array($uid));
		return $q->result_array();
	}
	
	/**
	 * Get all items for saved cart for given cart id
	 * 
	 * @param int $id cart id
	 * @return array
	 */
	function getsavedcart($id)
	{
		$sql="select item.id,cart.cartid,carti.quantity as qty,item.name,cart.name as cartname,item.price from king_savedcartitems as carti join king_savedcarts as cart on cart.cartid=carti.cartid join king_dealitems as item on item.id=carti.itemid where carti.cartid=?";
		$q=$this->db->query($sql,array($id));
		return $q->result_array();
	}
	
	/**
	 * Get item details for given item id
	 * 
	 * Details include category,brand and deal details
	 * 
	 * @param int $id item id
	 * @return bool|array false on failure
	 */
	function getitemdetails($id)
	{
		$sql="select item.live,item.shipsin,replace(item.url,' ','-') as url,deal.vendorid,deal.catid,deal.dealtype,deal.brandid,deal.startdate,deal.enddate,cat.name as category,brand.logoid as brandlogoid,brand.name as brandname,item.id,item.price,item.orgprice,item.name,item.quantity,item.pic,item.tagline,item.available,item.description1,item.description2,item.dealid,deal.catid,deal.startdate,deal.enddate from king_dealitems as item join king_deals as deal on deal.dealid=item.dealid and deal.publish=1 join king_brands as brand on brand.id=deal.brandid join king_categories as cat on cat.id=deal.catid where item.id=? and deal.startdate<?";
		$q=$this->db->query($sql,array($id,time()));
		if($q->num_rows()==1)
			return $q->row_array();
		return false;
	}
	
	function checkpricereq($uid,$deal)
	{
		$sql="select 1 from king_pricereqs where userid=? and itemid=? and status=0";
		if($this->db->query($sql,array($uid,$deal))->num_rows()==0)
			return false;
		return true;
	}
	
	function newpricereq($uid,$iid,$price,$qua)
	{
		$sql="insert into king_pricereqs(userid,itemid,price,quantity,time,status) values(?,?,?,?,?,0)";
		$this->db->query($sql,array($uid,$iid,$price,$qua,time()));
	}
	
	function getextradeals($catid,$brandid,$vendorid,$id)
	{
		$sql="select replace(item.url,' ','-') as url,deal.catid,deal.dealtype,deal.brandid,deal.startdate,deal.enddate,cat.name as category,brand.logoid as brandlogoid,brand.name as brand,item.id,item.price,item.orgprice,item.name,item.quantity,item.pic,item.tagline,item.available,item.description1,item.description2,item.dealid,deal.catid,deal.startdate,deal.enddate from king_dealitems as item join king_deals as deal on deal.dealid=item.dealid and deal.publish=1 join king_brands as brand on brand.id=deal.brandid join king_categories as cat on cat.id=deal.catid where (deal.catid=? or deal.brandid=?) and ".time()." between deal.startdate and deal.enddate and item.id!=? and deal.publish=1 limit 4";
		$ret=$this->db->query($sql,array($catid,$brandid,$id))->result_array();
		$ar=array($id);
		if(count($ret)<4)
		{
			$sql="select replace(item.url,' ','-') as url,deal.catid,deal.dealtype,deal.brandid,deal.startdate,deal.enddate,cat.name as category,brand.logoid as brandlogoid,brand.name as brand,item.id,item.price,item.orgprice,item.name,item.quantity,item.pic,item.tagline,item.available,item.description1,item.description2,item.dealid,deal.catid,deal.startdate,deal.enddate from king_dealitems as item join king_deals as deal on deal.dealid=item.dealid and deal.publish=1 join king_brands as brand on brand.id=deal.brandid join king_categories as cat on cat.id=deal.catid where ".time()." between deal.startdate and deal.enddate and item.id!=?";
			foreach($ret as $r)
			{
				$sql.=" and item.id!=?";
				$ar[]=$r['id'];
			} 
			$sql.=" and deal.publish=1 order by rand() asc limit 4";
			$r2=$this->db->query($sql,$ar)->result_array();
			foreach($r2 as $r)
				$ret[]=$r;
		}
		return $ret;
	}
	
	function getitemdetailsforcomments($id)
	{
		$sql="select deal.dealtype,item.url,deal.startdate,deal.enddate,cat.name as category,brand.name as brandname,item.id,item.price,item.orgprice,item.name,item.quantity,item.pic,item.tagline,item.description1,item.description2,item.dealid,deal.catid,deal.startdate,deal.enddate from king_dealitems as item join king_deals as deal on deal.dealid=item.dealid and deal.publish=1 join king_brands as brand on brand.id=deal.brandid join king_categories as cat on cat.id=deal.catid where item.id=? and deal.startdate<?";
		$q=$this->db->query($sql,array($id,time()));
		if($q->num_rows()==1)
			return $q->row_array();
		return false;
	}
	
	function getagent($uid)
	{
		$sql="select user.mobile,user.email,user.inviteid,user.special,agent.via_uid as aid,user.name,user.userid from king_agents as agent join king_users as user on user.userid=agent.userid where agent.via_uid=?";
		return $this->db->query($sql,array($uid))->row_array();
	}
	function updateagent($uid,$aid,$name,$balance,$mobile,$email,$address,$city,$state,$pincode)
	{
		$sql="update king_users set name=?,mobile=?,email=?,address=?,city=?,state=?,pincode=? where userid=?";
		$this->db->query($sql,array($name,$mobile,$email,$address,$city,$state,$pincode,$uid));
		$sql="update king_agents set name=?,balance=? where via_uid=?";
		$this->db->query($sql,array($name,$balance,$aid));
	}
	
	function getwidget($salt)
	{
		$sql="select * from king_widgets where salt=? limit 1";
		return $this->db->query($sql,array($salt))->row_array();
	}
	
	function createagent($uid,$name,$balance)
	{
		$sql="insert into king_users(name,special,createdon,inviteid) values(?,?,?,?)";
		$this->db->query($sql,array($name,4,time(),randomChars(10)));
		$userid=$this->db->insert_id();
		$sql="insert into king_agents(userid,via_uid,name,balance,created_date,last_login) values(?,?,?,?,?,?)";
		$this->db->query($sql,array($userid,$uid,$name,$balance,time(),time()));
	}
	
	/**
	 * Check for special (fb,google,twitter) user in DB
	 * 
	 * @param mixed $uid user id from facebook or google or twitter
	 * @param int $type special user type (facebook=1 twitter=2 google=30
	 * @return bool|int Internal user id on success. False on failure. 
	 */
	function checkspecialuser($uid,$type)
	{
		$sql="select userid from king_specialusers where suid=? and type=?";
		$q=$this->db->query($sql,array($uid,$type));
		if($q->num_rows()==1)
		{
			$r=$q->row_array();
			return $r['userid'];
		}
		return false;
	}
	
	/**
	 * Update view deals order for ksale page
	 * 
	 * @param int $uid userid
	 * @param int $did deal id
	 * @return void
	 */
	function updateviewdeal($uid,$did)
	{
		$sql="select viewdeals from king_miscusers where userid=?";
		$q=$this->db->query($sql,array($uid));
		if($q->num_rows()==1)
		{
			$r=$q->row();
			$deals=explode("/",$r->viewdeals);
			if(isset($deals[0]))
			{
				if($deals[count($deals)-1]!=$did)
				{
					if(count($deals)>=5)
					array_shift($deals);
					array_push($deals,$did);
					$sdeals=implode("/",$deals);
					$sql="update king_miscusers set viewdeals=? where userid=? limit 1";
					$this->db->query($sql,array($sdeals,$uid));
				}
			}
		}
	}
	
	function getitemforurl($url)
	{
		$sql="select id from king_dealitems where url=?";
		$q=$this->db->query($sql,array($url));
		if($q->num_rows()==1)
			return $q->row()->id;
		return 0;
	}
	
	/**
	 * Creates new special user by inserting user details to users & specialusers table
	 *   
	 * @param mixed $uid userid from google or twitter or facebook
	 * @param string $name user name
	 * @param int $type special user type
	 * @param string $inviteId invitation id for the use
	 * @return void
	 */
	function newspecialuser($uid,$name,$email,$type,$inviteId,$inv)
	{
		$sql="insert into king_users(name,email,inviteid,special,friendof,createdon) values(?,?,?,?,?,?)";
		$this->db->query($sql,array($name,$email,$inviteId,$type,$inv,time()));
		$sql="select max(userid) from king_users";
		$q=$this->db->query($sql);
		$r=$q->row_array();
		$userid=$r['max(userid)'];
		$sql="insert into king_specialusers values(?,?,?)";
		$this->db->query($sql,array($userid,$uid,$type));
		$sql="insert into fb_miscusers(id) values(?)";
		$this->db->query($sql,array($userid));
	}
	
	function newfbuser($name,$email,$inviteid,$friendof,$fb_user)
	{
		$sql="insert into king_users(name,email,inviteid,friendof,fb_userid,createdon) values(?,?,?,?,?,?)";
		$q=$this->db->query($sql,array($name,$email,$inviteid,$friendof,$fb_user,time()));
	}
	
	/**
	 * Function to update user address and mobile after check out
	 * 
	 * @param int $uid userid
	 * @param int $mobile mobile number
	 * @param string $address address
	 * @return void
	 */
	function updateuseraddress($uid,$mobile,$address)
	{
		$sql="update king_users set mobile=?, address=? where userid=? limit 1";
		$q=$this->db->query($sql,array($mobile,$address,$uid));
	}
	
	/**
	 * Checkout function
	 * 
	 * Places order for given items and address with respect to user id
	 * 
	 * @param int $uid userid
	 * @param array $items array of item ids
	 * @param string $address delivery address
	 * @return bool|int Order id on success. False on failure
	 */
	function checkout($uid,$items,$data)
	{
		$id=array();
		$vid=array();
		foreach($items as $i)
		{
			$sql="select d.brandid,d.vendorid from king_deals as d join king_dealitems as i on i.id=? where d.dealid=i.dealid";
			$r=$this->db->query($sql,array($i['id']))->row();
			$id[]=$r->brandid;
			$vid[]=$r->vendorid;
		}
		$sql="insert into king_orders(id,userid,itemid,quantity,brandid,status,time,ship_person,ship_address,ship_city,ship_pincode,ship_phone,bill_person,bill_address,bill_city,bill_pincode,bill_phone,vendorid,paid,email) values";
		$i=0;
		$inp=array();
		$itemid=array();
		foreach($items as $item)
		{
			if($i!=0)
				$sql.=",";
			$sql.="(?,$uid,?,?,?,0,".time().",?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$inp[]=$orderid=rand(10000,44033302);
			array_push($inp,$item['id']);
			$itemid[]=$item['id'];
			array_push($inp,$item['qty']);
//			array_push($inp,$address);
			$inp[]=$id[$i];
			$da=array("person","address","city","pincode","phone","bill_person","bill_address","bill_city","bill_pincode","bill_phone");
//			$dv=array("ship_person","ship_address","ship_city","ship_pincode","ship_phone","bill_person","bill_address","bill_city","bill_pincode","bill_phone");
			foreach($da as $d)
				$inp[]=$data[$d];
			$inp[]=$vid[$i];
			$inp[]=$item['price']*$item['qty'];
			$inp[]=$data['email'];
			$i++;
		}
		$this->db->query($sql,$inp);	
		if($this->db->affected_rows()==0)
			return false;
		foreach($items as $item)
		{
			$this->db->query("update king_dealitems set available=available+".$item['qty']." where id=".$item['id']);
		}
//		$sql="update king_dealitems set available=available+1 where id=".implode(" or id=",$itemid);
//		$this->db->query($sql);
		return $orderid;
	}
	
	function getitemforcheckout($id)
	{
		$sql="select agentcom,orgprice,price,viaprice from king_dealitems where id=?";
		return $this->db->query($sql,array($id))->row_array();
	}
	
	function getsalesreport($aid,$start,$end)
	{
		$sql="select a.via_transid,a.orderid,a.qty,a.price,a.com,a.paid,a.time,o.ship_person,o.ship_address,o.ship_city,o.ship_pincode,i.name from king_agent_transactions as a join king_dealitems as i on i.id=a.itemid join king_orders as o on o.id=a.orderid where a.agentid=? and a.time between ? and ? order by a.time desc";
		return $this->db->query($sql,array($aid,$start,$end))->result_array();
	}

	function newagenttrans($aid,$qty,$price,$com,$amount,$tran,$oid,$itemid)
	{
		$sql="insert into king_agent_transactions(agentid,itemid,orderid,via_transid,price,com,time,qty,paid) values(?,?,?,?,?,?,?,?,?)";
		$this->db->query($sql,array($aid,$itemid,$oid,$tran,$price,$com,time(),$qty,$amount));
	}
	
	/**
	 * Get quantity available for specific item
	 * 
	 * @param array $items array of item ids
	 * @return array array combination of id and quantity pair
	 */
	function getitemsquantity($items)
	{
		$data=array();
		if(count($items)==0)
			return $data;
		$sql="select item.agentcom,item.shipsin,item.available,item.orgprice,item.price,item.id,item.quantity,item.pic,deal.enddate,deal.publish from king_dealitems as item join king_deals as deal on deal.dealid=item.dealid where item.id=?";
		$ids=array();
		$i=0;
		foreach($items as $item)
		{
			array_push($ids,$item['id']);
			if($i!=0)
			$sql.=' or id=?';
			$i=1;
		}
		$q=$this->db->query($sql,$ids);
		foreach($q->result_array() as $r)
		{
			$na=false;
			if($r['enddate']<time() || $r['publish']==0)
				$na=true;
			$data[$r['id']]=array('available'=>$r['available'],'quantity'=>$r['quantity'],'pic'=>$r['pic'],'na'=>$na,"orgprice"=>$r['orgprice'],"shipsin"=>$r['shipsin'],"agentcom"=>$r['agentcom']);
		}
		return $data;
	}
	
	function getaddress($id)
	{
		return $this->db->query("select * from king_address where id=?",array($id))->row_array();
	}
	
	function createaddress($data,$bool,$userid)
	{
		if($bool==0)
			$this->db->query("insert into king_address(userid,name,address,city,pincode,phone,shipbill) values(?,?,?,?,?,?,0)",array($userid,$data['person'],$data['address'],$data['city'],$data['pincode'],$data['phone']));
		else
			$this->db->query("insert into king_address(userid,name,address,city,pincode,phone,shipbill) values(?,?,?,?,?,?,1)",array($userid,$data['bill_person'],$data['bill_address'],$data['bill_city'],$data['bill_pincode'],$data['bill_phone']));
	}
	
	function getaddresses($uid)
	{
		$sql="select * from king_address where userid=? order by time desc";
		$adr=$this->db->query($sql,array($uid))->result_array();
		$ret[0]=array();
		$ret[1]=array();
		foreach($adr as $ad)
			$ret[$ad['shipbill']][]=$ad;
		return $ret;
	}
	
	/**
	 * Creates new user account in DB
	 * 
	 * @param string $email Email address
	 * @param string $name User name
	 * @param string $password Password
	 * @param int $mobile Mobile number
	 * @param string $inviteid Invitation id
	 * @param int $friendof friend of user id
	 * @return bool true on success
	 */
	function newuser($email,$name,$password,$mobile,$inviteid,$friendof)
	{
		$password=md5($password);
		$sql="insert into king_users(email,name,password,mobile,inviteid,friendof,createdon) values(?,?,?,?,?,?,?)";
		$q=$this->db->query($sql,array($email,$name,$password,$mobile,$inviteid,$friendof,time()));
		$bool=false;
		if($this->db->affected_rows()==1)
			$bool=true;
		if($bool==true)
		{
			$sql="insert into king_miscusers(logins,invites) values(0,0)";
			$this->db->query($sql);
		}
		return $bool;
	}
	
	/**
	 * get user detail by invite id
	 * 
	 * @param string $id invite id
	 * @return bool|array False on failure
	 */
	function getuserbyinviteid($id)
	{
		$sql="select * from king_users where inviteid=?";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()==1)
			return $q->row_array();
		return false;
	}
	
	function getresources($id)
	{
		$sql="select * from king_resources_old where roomid=? order by type asc";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()>0)
		{
			$data[0]=array();
			$data[1]=array();
			$res=$q->result_array();
			foreach($res as $r)
					array_push($data[$r['type']],$r['id']);
			return $data;
		}
		return false;
	}
	
	function getroom($id)
	{
		$sql="select * from king_roomdeals_old where roomid=? limit 1";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()==1)
			return $q->row_array();
		return false;
	}
	
	function getrooms($id)
	{
		$sql="select * from king_roomdeals_old where dealid=? order by price asc";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()>0)
			return $q->result_array();
		return false;
	}
	
	function gethoteldeal($id)
	{
		$sql="select * from king_hoteldeals_old where dealid=? limit 1";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()==1)
			return $q->row_array();
		return false;
	}
	
	function updateext($uid,$email,$mobile)
	{
		$sql="update king_users set email=?, mobile=? where userid=?";
		$this->db->query($sql,array($email,$mobile,$uid));
	}
	
	function gethoteldeals()
	{
		$sql="select * from king_hoteldeals_old order by enddate ASC";
		$q=$this->db->query($sql);
		if($q->num_rows()>0)
			return $q->result_array();
		return false;
	}
	
	/**
	 * Get user details for given user id
	 * 
	 * @param int $id userid
	 * @return bool|array False on failure
	 */
	function getuserbyid($id)
	{
		$sql="select m.age,m.gender,u.userid,u.mobile,u.name,u.email,u.inviteid,u.special from king_users as u join fb_miscusers as m on m.id=u.userid where userid=?";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()==1)
			return $q->row_array();
		return false;
	}
	
	/**
	 * Update user login count
	 * 
	 * @param int $id userid
	 * @return void
	 */
	function updateuserlogin($id,$me)
	{
		if($me['hometown']['name']==null)
			$me['hometown']['name']="";
		if($me['location']['name']==null)
			$me['location']['name']="";
		if(!isset($me['gender']) || $me['gender']==null)
			$me['gender']="na";
		$sql="update king_miscusers set `logins`=`logins`+1, lastlogin=NOW() where userid=?";
		$this->db->query($sql,array($id));
		$sql="update fb_miscusers set birthday=?,age=?,home=?,location=?,gender=?,lastupdate=? where id=?";
		list($d,$m,$y)=explode("/",$me['birthday']);
		$age=date("Y")-$y;
		$this->db->query($sql,array($me['birthday'],$age,$me['hometown']['name'],$me['location']['name'],$me['gender'],time(),$id));
		if($this->db->affected_rows()!=1)
		{
			$sql="insert into fb_miscusers(fid,birthday,age,home,location,gender,id) values(?,?,?,?,?,?,?)";
			$this->db->query($sql,array($me['id'],$me['birthday'],$age,$me['hometown']['name'],$me['location']['name'],$me['gender'],$id));
			return true;
		}
		return false;
	}
	
	/**
	 * Update user profile
	 * 
	 * @param int $uid userid
	 * @param string $name user name
	 * @param string $password password
	 * @param int $mobile mobile number
	 * @return bool true on success
	 */
	function edituser($uid,$name,$password,$mobile)
	{
		$sql="update king_users set name=?, password=?, mobile=? where userid=? limit 1";
		$q=$this->db->query($sql,array($name,$password,$mobile,$uid));
		if($this->db->affected_rows()==1)
			return true;
		return false;
	}
	
	/**
	 * Get referals for given user id
	 * 
	 * @param int $id userid
	 * @return bool|array false on zero
	 */
	function getreferals($id)
	{
		$sql="select * from king_users where friendof=? order by userid asc";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()>0)
			return $q->result_array();
		return false;
	}
	
	/**
	 * Get user details by given email
	 * 
	 * @param string $email email address
	 * @return bool|array false on failure
	 */
	function getuserbyemail($email)
	{
		$sql="select userid,mobile,name,email,inviteid,special from king_users where email=? and special=0";
		$q=$this->db->query($sql,array($email));
		if($q->num_rows()==1)
			return $q->row_array();
		return false;
	}
	
	/**
	 * Get last comment for given deal id
	 * 
	 * @param $dealid deal id
	 * @return bool|array false on no comments
	 */
	function getlastcommentfordeal($dealid)
	{
		return false;
		$sql="select COUNT('id') as countc, comment from king_comments where dealid=? order by id desc";
		$q=$this->db->query($sql,array($dealid));
		$r=$q->row_array();
		if(isset($r['comment']))
		return $q->row_array();
		return false;
	}

	function postcomment($comment,$user,$item,$deal)
	{
		$sql="insert into king_comments(comment,userid,dealid,itemid,time,flag) values(?,?,?,?,?,?)";
		$q=$this->db->query($sql,array($comment,$user,$deal,$item,time(),1));
	}
	
	function getlastcomments()
	{
		$sql="select com.id,user.special,com.itemid,item.pic,item.name as itemname,user.name as username,com.time,com.comment from king_comments as com join king_users as user on user.userid=com.userid join king_dealitems as item on item.id=com.itemid order by com.time desc limit 4";
		$q=$this->db->query($sql);
		if($q->num_rows()>0)
		return $q->result_array();
		else
		return array();
	}
	
	function newpreview($id,$dealid,$userid)
	{
		$sql="insert into king_dealpreviews(id,dealid,userid,time) values(?,?,?,?)";
		$q=$this->db->query($sql,array($id,$dealid,$userid,time()));
	}
	
	function getpreview($id)
	{
		$sql="select * from king_dealpreviews where id=?";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()==1)
			return $q->row_array();
		return false;
	}
	
	function getupcoming()
	{
		$sql="select cat.name as category,item.url,deal.dealid,deal.pic,deal.enddate,deal.startdate,brand.name from king_deals as deal join king_categories as cat on cat.id=deal.catid join king_brands as brand on brand.id=deal.brandid join king_dealitems as item on item.dealid=deal.dealid where deal.startdate>? and deal.publish=1 order by deal.startdate asc";
		$q=$this->db->query($sql,array(time()));
		if($q->num_rows()>0)
			return $q->result_array();
		else
			return array(); 
	}
	
	function api_auth($aid)
	{
		$sql="select * from king_api_logins where auth=?";
		$q=$this->db->query($sql,array(md5($aid)));
		if($q->num_rows()==1)
			return true;
		return false;
	}
	
	/**
	 * Get last comment for given item id
	 * 
	 * @param $itemid item id for which comments is queried
	 * @return bool|array false on no comments
	 */
	function getlastcommentforitem($itemid)
	{
		$sql="select com.id,user.special,user.name,com.time,com.comment from king_comments as com join king_users as user on user.userid=com.userid where com.itemid=? order by com.time desc limit 1";
		$q=$this->db->query($sql,array($itemid));
		if($q->num_rows()==1)
			return $q->row_array();
		return false;
	}
	
	function getcomments($id)
	{
		$sql="select com.id,user.special,user.name,com.time,com.comment from king_comments as com join king_users as user on user.userid=com.userid where com.itemid=? order by com.time desc";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()>0)
			return $q->result_array();
		return array();
	}
}
?>