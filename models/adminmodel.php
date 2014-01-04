<?php
/**
 * Admin Model file
 * 
 * @author Vimal <vimal@viaadz.com>, Santhosh <santhosh@viaadz.com>
 * @version 0.9
 * @package viakingsale 
 * @subpackage admin panel
 *
 */
class Adminmodel extends Model {
	/**
	 * Calls parent constructor
	 */
	function Adminmodel() 
	{
		parent::Model ();
	}
	
	/**
	 * Get User details for a User
	 * @param int $id
	 * @return array|bool sometimes a array and sometimes a boolean false
	 */
	function getUser($id) 
	{
		$sql = "select * from king_admin where user_id=? and account_blocked = 0 ";
		$q = $this->db->query ( $sql, $id );
		if ($q->num_rows () == 1)
			return $q->row ();
		else
			return false;
	}
	
	/**
	 * Inserts a User in table
	 *
	 * @param int $userid
	 * @param string $uname
	 * @param string $pwd
	 * @param int $usertype
	 * @param int $brandid
	 * @return bool
	 */
	function insert_userdetails($userid, $uname, $pwd, $usertype, $brandid) {
		$sql = "Insert into king_admin(user_id,name,password,usertype,brandid) values(?,?,?,?,?)";
		$q = $this->db->query ( $sql, array ($userid, $uname, $pwd, $usertype, $brandid ) );
		if ($this->db->affected_rows () > 0) {
			return TRUE;
		}
		return FALSE;
	}
	
	function getpricereqscount()
	{
		return $this->db->query("select count(1) as len from king_pricereqs where status=0")->row()->len;
	}
	
	function getpricereqs($stat)
	{
		$sql="select  p.aprice as areqprice,ven.name as vendor,user.mobile,user.email,user.special,user.name as user,p.price as reqprice,p.quantity,p.status,p.time,p.url,p.id,item.name,item.price,item.orgprice,item.rsp from king_pricereqs as p join king_dealitems as item on item.id=p.itemid join king_deals as deal on deal.dealid=item.dealid join king_vendors as ven on ven.id=deal.vendorid join king_users as user on user.userid=p.userid where p.status=?";
		return $this->db->query($sql,$stat)->result_array();
	}
	
	function getpricereqcounts()
	{
		$c[0]=$this->db->query("select count(1) as len from king_pricereqs where status=0")->row()->len;
		$c[1]=$this->db->query("select count(1) as len from king_pricereqs where status=1")->row()->len;
		$c[2]=$this->db->query("select count(1) as len from king_pricereqs where status=2")->row()->len;
		$c[3]=$this->db->query("select count(1) as len from king_pricereqs where status=3")->row()->len;
		return $c;
	}
	
	function negotiateprice($id,$ap,$p)
	{
		$sql="update king_pricereqs set aprice=?, price=? where id=?";
		$this->db->query($sql,array($ap,$p,$id));
	}
	
	function getpr($stat)
	{
		$sql="select item.url as itemurl,user.special,user.email,user.name as user,p.price as reqprice,p.quantity,p.status,p.time,p.url,p.id,item.name,item.price,item.orgprice from king_pricereqs as p join king_dealitems as item on item.id=p.itemid join king_users as user on user.userid=p.userid where p.id=?";
		return $this->db->query($sql,$stat)->row_array();
	}
	
	function prchange($id,$s,$url)
	{
		$sql="update king_pricereqs set status=?, url=? where id=?";
		$this->db->query($sql,array($s,$url,$id));
	}
	
	function getmaincats()
	{
		$sql="select id,name from king_categories where type=0";
		return $this->db->query($sql)->result_array();
	}
	
	function getbrandsforcategory($cat)
	{
		$sql="select brand.id,brand.name from king_brands as brand join king_catbrand as c on c.catid=? where brand.id=c.brandid order by brand.name asc";
		return $this->db->query($sql,array($cat))->result_array();
	}
	
	function getsiteuserslen()
	{
		$sql="Select count(1) as len from king_users";
		return $this->db->query($sql)->row()->len;
	}
	
	function getallvendors()
	{
		$sql="select v.name,va.name as username from king_vendors as v join king_admin as va on va.brandid=v.id order by created_date desc";
		return $this->db->query($sql)->result_array();
	}
	
	function newvendor($username,$name,$password,$address,$email,$telephone,$mobile,$contact,$desc)
	{
		$vid=$this->genid(8);
		$sql="insert into king_admin(user_id,name,password,usertype,brandid) values(?,?,?,2,?)";
		$this->db->query($sql,array(md5(strtolower($username)),$username,md5($password),$vid));
		$sql="insert into king_vendors(id,name,address,email,telephone,mobile,contact,description,created_date) 
		values(?,?,?,?,?,?,?,?,?)";
		$this->db->query($sql,array($vid,$name,$address,$email,$telephone,$mobile,$contact,$desc,time()));
	}
	
	function getdealslenforbrand($bid)
	{
		$sql="select count(1) as len from king_deals where brandid=?";
		return $this->db->query($sql,array($bid))->row()->len;
	}
	
	function getuserslenbylogin($type)
	{
		$sql="select count(1) as len from king_users where special=?";
		return $this->db->query($sql,$type)->row()->len;
	}
	
	function getuserslenbycorp($corp)
	{
		$sql="select count(1) as len from king_users where corpid=?";
		return $this->db->query($sql,$corp)->row()->len;
	}
	
	function getreferrals($uid)
	{
		$sql="select name,userid from king_users where friendof=?";
		return $this->db->query($sql,$uid)->result_array();
	}
	
	function getsiteuser($uid)
	{
		$sql="select u.*,c.name as corp from king_users u left outer join king_corporates c on c.id=u.corpid where userid=?";
	    return $this->db->query($sql,array($uid))->row_array();
	}
	
	function blocksiteuser($uid,$block)
	{
		if($block=="block")
			$b=1;
		elseif($block=="unblock")
			$b=0;
		else
			return;
		$sql="update king_users set block=? where userid=? limit 1";
		$this->db->query($sql,array($b,$uid));
	}
	
	function getsiteusers($p=1,$sort,$order)
	{
		$sql="select * from king_users ";
		switch($sort)
		{
			case "name":
				$sql.="order by name ";
				break;
			case "email":
				$sql.="order by email ";
				break;
			case "login":
				$sql.="order by special ";
				break;
			case "created":
				$sql.="order by createdon ";
				break;
			default:
				$sql.="order by userid ";
				break;
		}
		if($order=="d")
			$sql.="desc ";
		else
			$sql.="asc ";
		$sql.="limit ".(($p-1)*20).",20";
		$q=$this->db->query($sql);
		return $q->result();
	}
	
	
	function getsiteusersbylogin($type,$p=1,$sort,$order)
	{
		$sql="select * from king_users where special=? ";
		switch($sort)
		{
			case "name":
				$sql.="order by name ";
				break;
			case "email":
				$sql.="order by email ";
				break;
			case "login":
				$sql.="order by special ";
				break;
			case "created":
				$sql.="order by createdon ";
				break;
			default:
				$sql.="order by userid ";
				break;
		}
		if($order=="d")
			$sql.="desc ";
		else
			$sql.="asc ";
		$sql.="limit ".(($p-1)*20).",20";
		$q=$this->db->query($sql,$type);
		return $q->result();
	}
	
	function getsiteusersbycorp($corp,$p=1,$sort,$order)
	{
		$sql="select * from king_users where corpid=? ";
		switch($sort)
		{
			case "name":
				$sql.="order by name ";
				break;
			case "email":
				$sql.="order by email ";
				break;
			case "login":
				$sql.="order by special ";
				break;
			case "created":
				$sql.="order by createdon ";
				break;
			default:
				$sql.="order by userid ";
				break;
		}
		if($order=="d")
			$sql.="desc ";
		else
			$sql.="asc ";
		$sql.="limit ".(($p-1)*20).",20";
		$q=$this->db->query($sql,$corp);
		return $q->result();
	}
	
	function searchdeals($q,$vid=0)
	{
		$q="%".$q."%";
		$sql="select a.*,b.*,b.id as itemid,c.name as brandname,d.name as catname,a.tagline 
						from king_deals a
						left join king_dealitems b on a.dealid = b.dealid   
						left join king_brands c on a.brandid = c.id 
						left join king_categories d on d.id = a.catid 
						where (a.tagline like ? or a.description like ? or a.keywords like ?)";
		if($vid!=0)
			$sql.=" and a.vendorid=?";
		$sql.=" order by a.tagline asc";
		$q=$this->db->query($sql,array($q,$q,$q,$vid));
		return $q->result_array(); 
	}
	
	function searchusers($q)
	{
		 
		$this->load->helper('email');
		
		$cond = array();
		
		$sql="Select * from king_users where 1 and ";
		
		if(validate_is_email($q))
		{
			$sql .= " email = ? ";
			$cond[] = $q;
		}
		else if(validate_is_mobile($q))
		{
			$sql .= " mobile = ? ";
			$cond[] = $q;
		}
		else
		{
			$sql .= " name like ? ";
			$cond[] = '%'.$q.'%'; 
		}
		
		$sql .=" order by userid desc ";
		
		$q=$this->db->query($sql,$cond);
		//echo $this->db->last_query();
		return $q->result_array(); 
	}
	
	function searchbrands($q)
	{
		$q="%".$q."%";
		$sql="select * from king_brands where name like ? or description like ? order by name asc";
		$q=$this->db->query($sql,array($q,$q));
		return $q->result_array();	
	}
	
	function searchitems($q)
	{
		$q="%".$q."%";
		$sql="select deal.dealid,item.name,item.id from king_dealitems as item join king_deals as deal on deal.dealid=item.dealid where item.name like ? order by item.name asc";
		$q=$this->db->query($sql,array($q));
		return $q->result_array();
	}
	
	function searchcategories($q)
	{
		$q="%".$q."%";
		$sql="select * from king_categories where name like ? or description like ?";
		$q=$this->db->query($sql,array($q,$q));
		return $q->result_array();
	}
	
	function searchtrans($q)
	{
		if(strlen($q)!=11)
			$q="%".$q."%";
		$data['trans']=$trans=$this->db->query("select * from king_transactions where transid like ?",$q)->result_array();
		if(strlen($q)==11 || strlen($q)==7 && count($trans)==1)
			redirect("admin/trans/{$trans[0]['transid']}");
	}
	
	function searchorders($q,$vid=0)
	{
		$q="%".$q."%";
		$sql="select user.name as username,ord.id,item.name from king_users as user join king_orders as ord on ord.userid=user.userid join king_dealitems as item on item.id=ord.itemid join king_deals as deal on deal.dealid=item.dealid where user.name like ?";
		if($vid!=0) 
			$sql.=" and deal.vendorid=?";
		$sql.=" order by user.name asc";
		$q=$this->db->query($sql,array($q,$vid));
		return $q->result_array();
	}
	function searchinvoices($q)
	{
		 
		$q="%".$q."%";
		$res=$this->db->query("select distinct invoice_no,invoice_status,transid from king_invoice where invoice_no like ?",$q)->result_array();
		return $res;
	}
	
	
		
	/**
	 * Gets User Details for a particular brandid 
	 *
	 * @param int $brandid
	 * @param int $p
	 * @return array|bool sometimes result array on success and sometimes boolean false on failure.
	 */
	function getuserdetails($brandid, $p) {
		//print_r($brandid);exit;
		if ($p == 1)
			$sql = "Select a.user_id,a.name as branduser,a.password,a.brandid,a.usertype,b.name as brandname
 from king_admin as a join
king_brands as b on b.id=a.brandid where usertype=3 and a.brandid=? limit 10";
		else
			$sql = "Select a.user_id,a.name as branduser,a.password,a.brandid,a.usertype,b.name as brandname
 from king_admin as a join
king_brands as b on b.id=a.brandid where usertype=3 and a.brandid=? limit " . (($p - 1) * 10) . ",10";
		$q = $this->db->query ( $sql, $brandid );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return false;
	}
	function getbrandslen($cat=null)
	{
		if($cat)
		$sql="select count(1) as len from king_brands as b join king_catbrand as cb on cb.catid=? where b.id=cb.brandid";
		else
		$sql="select count(1) as len from king_brands";
		$q=$this->db->query($sql,array($cat));
		$r=$q->row();
		return $r->len;
	}
	
	function getdealchanges($dealid,$data)
	{
		$sql="select * from king_deals where dealid=?";
		$q=$this->db->query($sql,array($dealid));
		$ret=array();
		if($q->num_rows()==1)
		{
			$r=$q->row_array();
			foreach($data as $name=>$val)
			{
				if(isset($r[$name]) && $r[$name]!=$val)
					array_push($ret,$name);
			}
		}
		return $ret;
	}
	
	function getitemchanges($itemid,$data)
	{
		$sql="select * from king_dealitems where id=?";
		$q=$this->db->query($sql,array($itemid));
		$ret=array();
		if($q->num_rows()==1)
		{
			$r=$q->row_array();
			foreach($data as $name=>$val)
			{
				if(isset($r[$name]) && $r[$name]!=$val)
					array_push($ret,$name);
			}
		}
		$i=0;
		foreach($ret as $name)
		{
			if($name=="orgprice")
				$ret[$i]="Original price";
			if($name=="description1")
				$ret[$i]="brief description";
			if($name=="description2")
				$ret[$i]="extra description";
			$i++;
		}
		return $ret;
	}
	
	function addactivity($userid,$msg,$dealid,$brandid)
	{
		$sql="insert into king_activity(userid,msg,dealid,brandid,time) values(?,?,?,?,?)";
		$this->db->query($sql,array($userid,$msg,$dealid,$brandid,time()));		
	}
	
	function getbrandadmindetailsforcat($cat,$p=1) {
		$p=$p+1-1;
			$sql = "select b.id as brandid,b.name as brandname
					from king_brands as b join king_catbrand as cb on cb.catid=? where b.id=cb.brandid";
			$sql .= " limit " . (($p - 1) * 10) . ",10";
		$q = $this->db->query ( $sql ,array($cat));
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return false;
	}
	/**
	 * Gets Brand Admin details
	 *
	 * @param int $p
	 * @return array|bool sometimes result array on success and sometimes boolean false on failure.
	 */
	function getbrandadmindetails($p=1) {
		$sql = "select b.id as brandid,b.name as brandname
					from king_brands as b ";
			$sql .= "limit " . (($p - 1) * 10) . ",10";
				$q = $this->db->query ( $sql );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return false;
	}
	/**
	 * Delete User details based on userid
	 *
	 * @param int $userid
	 */
	function delete_useriddetails($userid) {
		$sql = "delete from king_admin where user_id=?";
		$q = $this->db->query ( $sql, $userid );
		$sql = "delete from king_admin where user_id=?";
		$q2 = $this->db->query ( $sql, $userid );
	}
	/**
	 * Delete Admin details based on userid and usertype
	 *
	 * @param int $userid
	 * @param int $usertype
	 */
	function delete_admindetails($userid, $usertype) {
		$sql = "delete from king_admin where user_id=? and usertype=?";
		$q = $this->db->query ( $sql, array ($userid, $usertype ) );
	}
	/**
	 * Changepassword
	 *
	 * changes the password for a user
	 * @param string $pwd
	 * @param int $userid
	 * @return bool on success true and on failure False
	 */
	function changepassword($pwd, $userid) {
		$sql = "update king_admin set password=? where user_id=?";
		$q = $this->db->query ( $sql, array ($pwd, $userid ) );
		if ($this->db->affected_rows () > 0)
			return TRUE;
		else
			return FALSE;
	}
	/**
	 * Gets Password
	 *
	 * @param int $id
	 * @return array|bool sometimes result array on success and sometimes boolean false on failure.
	 */
	function getPassword($id) {
		$sql = "select password from king_admin where user_id=?";
		$q = $this->db->query ( $sql, $id );
		
		if ($q->num_rows () == 1) {
			return $q->row ()->password;
		} else
			return false;
	}
	/**
	 * Inserts hotel details
	 *
	 * @param int $dealid
	 * @param string $address
	 * @param string $latlong
	 * @param string $phone
	 * @param string $email
	 * @param string $city
	 * @param string $heading
	 * @param string $tagline
	 * @param string $amenities
	 * @return bool on success true and on failure False
	 */
	function inserthoteldetails($dealid,$latlong,$heading, $tagline, $amenities) {
		$sql = 'insert into king_hoteldeals(dealid,latlong,heading,tagline,amenities) values(?,?,?,?,?)';
		//print_r($sql);exit;
		$q = $this->db->query ( $sql, array ($dealid,$latlong,$heading, $tagline, $amenities ) );
		if ($this->db->affected_rows () > 0) {
			return True;
		} else
			return False;
	}
	/**
	 * Add Deals
	 *
	 * @param int $subcat
	 * @param int $brandid
	 * @param string $sdate
	 * @param string $edate
	 * @param string $imgname
	 * @param string $description
	 * @return array|bool sometimes result array on success and sometimes boolean false on failure.
	 */
	function addmainitems($subcat, $brandid, $sdate, $edate, $imgname, $description,$publish,$website,$email,$mobile,$phone,$address,$city,$state,$pincode) {
		$sql = 'call insert_mainitems(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
		$q = $this->db->query ( $sql, array ($subcat, $brandid, $sdate, $edate, $imgname, $description,$publish,$website,$email,$mobile,$phone,$address,$city,$state,$pincode ) );
		if ($q->num_rows () == 1) {
			$r = $q->result_array ();
			if ($r [0] ['@r1'] != 0)
				return $r [0] ['@r1'];
		} else
			return False;
	}
	
	function adddeal($dealid,$catid,$vendorid,$brandid,$tagline,$sdate,$edate,$img,$desc,$website,$email,$dealtype,$keywords,$menu,$menu2,$is_giftcard,$is_coup_applicable)
	{
		$dealid=$this->genid(10);
		$sql="insert into king_deals(dealid,catid,tagline,vendorid,brandid,startdate,enddate,pic,description,website,email,dealtype,publish,keywords,menuid,menuid2,is_giftcard,is_coupon_applicable) values(?,?,?,?,?,?,?,?,?,?,?,?,0,?,?,?,?,?)";
		$this->db->query($sql,array($dealid,$catid,$tagline,$vendorid,$brandid,$sdate,$edate,$img,$desc,$website,$email,$dealtype,$keywords,$menu,$menu2,$is_giftcard,$is_coup_applicable));
		return $dealid;
	}
	/**
	 *Add items to a deal
	 *
	 * @param int $dealid
	 * @param float $price
	 * @param float $originalprice
	 * @param string $name
	 * @param int $quantity
	 * @param string $imgname
	 * @param string $description
	 * @return array|bool sometimes result array on success and sometimes boolean false on failure.
	 */
	function addsubitems($dealid, $price, $originalprice, $name, $quantity, $imgname, $description) {
		$sql = 'call insert_subitems(?,?,?,?,?,?,?)';
		$q = $this->db->query ( $sql, array ($dealid, $price, $originalprice, $name, $quantity, $imgname, $description ) );
		if ($q->num_rows () == 1) {
			$r = $q->result_array ();
			if ($r [0] ['@r1'] != 0)
				return $r [0] ['@r1'];
		} else
			return False;
	}
	
	function getallmenu(){
		return $this->db->query("select * from king_menu order by name asc")->result_array();
	}
	
	function getmenu(){
		return $this->db->query("select * from king_menu where status=1 order by name asc")->result_array();
	}
	
	function addmenu($name){
		$url=str_replace(" ","-",$name);
		$this->db->query("insert into king_menu(name,url) values(?,?)",array($name,$url));
	}
	
	function additem($dealid,$price,$viaprice,$orgprice,$name,$quantity,$imgname,$desc1,$desc2,$itemcode,$model,$nlc,$phc,$shc,$rsp,$shipsin,$fcp,$tax,$service_tax,$service_tax_cod,$slots,$bp_expires,$shipsto,$barcode)
	{
		if(!empty($shipsto))
		{
			$sa=explode(",",$shipsto);
			foreach($sa as $i=>$s)
				$sa[$i]=strtolower(trim($s));
			$shipsto=implode(",",$sa);
		}
		$bp_expires=$bp_expires*24*60*60;
		$id=$this->genitemid();
		$url=$name;
		$blacks=array("?","#","@",")","(","[","]","/","\\","!","~","`","%","^","&","*","+","=","'",'"');
		foreach($blacks as $b)
		{
			$url=str_replace(" $b","",$url);
			$url=str_replace($b,"",$url);
		}
		$url=str_replace(" ","-",$url);
		$url.="-p".rand(10,99)."t";
		$sql="insert into king_dealitems(id,dealid,price,orgprice,viaprice,name,quantity,available,pic,description1,description2,created_on,url,itemcode,model,nlc,phc,shc,rsp,shipsin,created,modified,fcp) values(?,?,?,?,?,?,?,?,?,?,?,NOW(),?,?,?,?,?,?,?,?,?,0,?)";
		$this->db->query($sql,array($id,$dealid,$price,$orgprice,$viaprice,$name,$quantity,0,$imgname,$desc1,$desc2,$url,$itemcode,$model,$nlc,$phc,$shc,$rsp,$shipsin,time(),$fcp));
		$this->db->query("update king_dealitems set bp_expires=?,tax=?,service_tax=?,service_tax_cod=?,slots=?,shipsto=? where id=?",array($bp_expires,$tax,$service_tax,$service_tax_cod,$slots,$shipsto,$id));
		
		$cod=$this->input->post("cod");
		$sizing_type=$this->input->post("sizing");
		$types=array("none"=>0,"numbering"=>1,"wording"=>2);
		$type=$types[$sizing_type];
		if($type==1)
		{
			$numbers=explode(",",$this->input->post("sizing_numbers"));
			foreach($numbers as $i=>$n)
				$numbers[$i]=trim($n);
			$sizing="1:".implode(",",$numbers);
		}
		elseif($type==2)
			$sizing="2:".implode(",",$this->input->post("sizing_words"));
		else 
			$sizing="0";
		$groupbuy=$this->input->post("groupbuy");
		$this->db->query("update king_dealitems set sizing=?,cod=?,groupbuy=? where id=? limit 1",array($sizing,$cod,$groupbuy,$id));
		return $id;
	}
	
	function getcorporateslen()
	{
		return $this->db->query("select count(1) as l from king_corporates")->row()->l;
	}

	/**

	 * Add room details for a specific hotel deal
	 *
	 * @param int $roomid
	 * @param int $dealid
	 * @param string $heading
	 * @param string $tagline
	 * @param string $strdates
	 * @return bool on success true and on failure False
	 */
	function addroomdetails($roomid, $dealid, $heading, $tagline, $strdates) {
		$sql = 'Insert into  king_roomdeals(roomid,dealid,heading,tagline,availability) values(?,?,?,?,?)';
		$q = $this->db->query ( $sql, array ($roomid, $dealid, $heading, $tagline, $strdates ) );
		if ($this->db->affected_rows () > 0) {
			return True;
		} else
			return False;
	}
	/**
	 * Gets item details
	 *
	 * @param int $dealid
	 * @return array|bool sometimes result array on success and sometimes boolean false on failure.
	 */
	function getroomid($dealid) {
		$sql = 'select id,dealid,name from king_dealitems where dealid=?';
		$q = $this->db->query ( $sql, array ($dealid ) );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return False;
	}
	
	function getcategoryforid($catid)
	{
		$sql="select * from king_categories where id=?";
		$q=$this->db->query($sql,array($catid));
		if($q->num_rows()==1)
			return $q->row_array();
		return false;
	}
	/**
	 * Gets List of deals for a particular brand
	 *
	 * @param int $brandid
	 * @param int $p
	 * @return array|bool sometimes result array on success and sometimes boolean false on failure.
	 */
	function getdealslist($brandid, $p=1,$b=false) {
			$sql = 'select i.favs,m.name as menu,i.slots,a.dealtype,a.tagline,a.dealid,a.catid,a.brandid,a.startdate,a.enddate,a.pic,a.publish,b.name from king_deals
	as a join king_categories as b on b.id=a.catid join king_menu as m on m.id=a.menuid join king_dealitems as i on i.dealid=a.dealid';
			if($b==false)
				$sql.=" where a.brandid=?";
			else
				$sql.=" where a.vendorid=?";
			$sql.=" limit ".(($p-1)*5).",5";
		$q = $this->db->query ( $sql, $brandid );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return False;
	}
	
	function getdeal($dealid)
	{
		$sql = 'select i.slots,m.name as menu,a.dealtype,a.vendorid,a.tagline,a.dealid,a.catid,a.brandid,a.startdate,a.enddate,a.pic,a.publish,b.name from king_deals
	as a join king_categories as b on b.id=a.catid join king_menu as m on m.id=a.menuid join king_dealitems as i on i.dealid=a.dealid where a.dealid=?';
		$q = $this->db->query ( $sql, $dealid );
		if ($q->num_rows () ==1) 
			return $q->result ();
		return False;
	}
	
	function getdealslen($brandid=0)
	{
		$sql="select count(1) as len from king_deals";
			if($brandid!=0)
				$sql.=" where vendorid=?";
			$q=$this->db->query($sql,array($brandid));
			return $q->row()->len;
	}
	
	function moderatecomments($cids,$action)
	{
		if(empty($cids)||!is_array($cids))
			return;
		$sql="update king_comments set ";
		if($action=="accept")
			$sql.="new=1,flag=0";
		elseif($action=="flag")
			$sql.="new=1,flag=1";
		$sql.=" where ";
		$ccid=array();
		foreach($cids as $cid)
		{
			if(strlen(trim($cid))!=0)
				array_push($ccid,"id=".$cid);
		}
		$sql.=implode(" or ",$ccid);
		$q=$this->db->query($sql);
	}
	
	function getdealslenforcategory($category,$brandid=0)
	{
		$sql="select count(1) as len from king_deals where catid=?";
			if($brandid!=0)
				$sql.=" and vendorid=?";
			$q=$this->db->query($sql,array($category,$brandid));
			return $q->row()->len;
	}
	
	function getdealslenbystatus($status,$brandid=0)
	{
		switch($status)
		{
			case "active":
			$sql = 'select count(1) as len from king_deals where startdate<'.time().' and enddate>'.time();			
				break;
			case "inactive":
			$sql = 'select count(1) as len from king_deals where startdate>'.time();
				break;
			case "expired":
			$sql = 'select count(1) as len from king_deals where enddate<'.time();
				break;
			case "unpublished":
			$sql = 'select count(1) as len from king_deals where publish=0';
				break;
			case "published":
			$sql = 'select count(1) as len from king_deals where publish=1';
				break;
		}
			if($brandid!=0)
				$sql.=" and vendorid=?";
			$q=$this->db->query($sql,array($brandid));
			return $q->row()->len;
	}
	
	function getordersfordashboard($brandid)
	{
		$sql="select orders.id,item.name as itemname,orders.quantity,orders.status,orders.time,user.name from king_orders as orders join king_users as user on user.userid=orders.userid join king_dealitems as item on item.id=orders.itemid where orders.vendorid=? order by time desc limit 7";
		$q=$this->db->query($sql,array($brandid));
		if($q->num_rows()>0)
			return $q->result_array();
		return false;
	}
	
	function getbrandcategories($brandid=0,$b=false)
	{
		if($brandid==0)
			$sql="select * from king_categories order by name asc";
		else
		{
			if($b==false)
			$sql="select cat.id,cat.name from king_catbrand as cb join king_categories as cat on cat.id=cb.catid where cb.brandid=?";
			else
			$sql="select cat.id,cat.name from king_categories as cat order by cat.name asc";
		}
		$q=$this->db->query($sql,array($brandid));
		return $q->result();
	}
	
	function getcommentslen($p=1)
	{
		$sql="select count(1) as len from king_comments";
		$q=$this->db->query($sql);
		return $q->row()->len;
	}
	
	function getcommentslenbystatus($status,$p=1)
	{
		$sql="select count(1) as len from king_comments";
		if($status=="new")
			$sql.=" where new=0 and flag=0";
		elseif($status=="moderated")
			$sql.=" where new=1 and flag=0";
		else
			$sql.=" where new=1 and flag=1";
		$q=$this->db->query($sql);
		return $q->row()->len;
	}
	
	function getnewcommentscount()
	{
		$sql="select count(1) as len from king_comments where new=0";
		$q=$this->db->query($sql);
		return $q->row()->len;
	}
	
	function getcommentsbystatus($status,$p=1)
	{
		$sql="select com.comment,com.id,user.name as username,deal.tagline,item.name as itemname,com.time,com.flag,com.new from king_comments as com join king_users as user on user.userid=com.userid join king_dealitems as item on item.id=com.itemid join king_deals as deal on deal.dealid=com.dealid";
		if($status=="new")
			$sql.=" where com.new=0 and com.flag=0";
		elseif($status=="moderated")
			$sql.=" where com.new=1 and com.flag=0";
		else
			$sql.=" where com.new=1 and com.flag=1";
		$sql.=" limit ".(($p-1)*10).",10";
		$q=$this->db->query($sql);
		if($q->num_rows()>0)
			return $q->result();
		return false;
	}
	
	function getcomments($p=1)
	{
		$sql="select com.comment,com.id,user.name as username,deal.tagline,item.name as itemname,com.time,com.flag,com.new from king_comments as com join king_users as user on user.userid=com.userid join king_dealitems as item on item.id=com.itemid join king_deals as deal on deal.dealid=com.dealid limit ".(($p-1)*10).",10";
		$q=$this->db->query($sql);
		if($q->num_rows()>0)
			return $q->result();
		return false;
	}
	
	function getdealsbystatus($status,$p=1,$brandid=0) {
		switch($status)
		{
			case "active":
			$sql = 'select i.favs,i.slots,m.name as menu,a.dealtype,a.tagline,a.dealid,a.catid,a.brandid,a.startdate,a.enddate,a.pic,a.publish,b.name from king_deals
					as a join king_categories as b on b.id=a.catid join king_menu as m on m.id=a.menuid join king_dealitems as i on i.dealid=a.dealid where a.startdate<'.time().' and a.enddate>'.time();
			break;
			case "inactive":
			$sql = 'select i.favs,i.slots,m.name as menu,a.dealtype, a.tagline,a.dealid,a.catid,a.brandid,a.startdate,a.enddate,a.pic,a.publish,b.name from king_deals
					as a join king_categories as b on b.id=a.catid join king_menu as m on m.id=a.menuid join king_dealitems as i on i.dealid=a.dealid where a.startdate>'.time();
			break;
			case "expired":
			$sql = 'select  i.favs,i.slots,m.name as menu,a.dealtype,a.tagline,a.dealid,a.catid,a.brandid,a.startdate,a.enddate,a.pic,a.publish,b.name from king_deals
					as a join king_categories as b on b.id=a.catid join king_menu as m on m.id=a.menuid join king_dealitems as i on i.dealid=a.dealid where a.enddate<'.time();
			break;
			case "unpublished":
			$sql = 'select i.favs,i.slots,m.name as menu,a.dealtype,a.tagline,a.dealid,a.catid,a.brandid,a.startdate,a.enddate,a.pic,a.publish,b.name from king_deals
					as a join king_categories as b on b.id=a.catid join king_menu as m on m.id=a.menuid join king_dealitems as i on i.dealid=a.dealid where a.publish=0';
			break;
			case "published":
			$sql = 'select i.favs,i.slots,m.name as menu,a.dealtype,a.tagline,a.dealid,a.catid,a.brandid,a.startdate,a.enddate,a.pic,a.publish,b.name from king_deals
					as a join king_categories as b on b.id=a.catid join king_menu as m on m.id=a.menuid join king_dealitems as i on i.dealid=a.dealid where a.publish=1 and '.time().' between a.startdate and a.enddate';
			break;
		}
		if($brandid!=0)
			$sql.=" and a.vendorid=?";
		$sql.=" order by a.modified_on desc";
		if ($p == 1)
			$sql .= ' limit 5';
		else
			$sql .= " limit " . (($p - 1) * 5) . ",5";
		$q = $this->db->query ( $sql, $brandid );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return False;
	}
	
	function getdealslenbymenu($menu,$start,$brandid)
	{
		$sql="select count(1) as l from king_deals where menuid=? and startdate>=?";
		if($brandid!=0)
			$sql.=" and vendorid=?";
		return $this->db->query($sql,array($menu,strtotime($start),$brandid))->row()->l;
	}
	
	function getdealsbymenu($menu,$start,$brandid,$p=1)
	{
		$sql = 'select i.slots,m.name as menu,a.dealtype,a.tagline,a.dealid,a.catid,a.brandid,a.startdate,a.enddate,a.pic,a.publish,b.name from king_deals
					as a join king_categories as b on b.id=a.catid join king_menu as m on m.id=a.menuid join king_dealitems as i on i.dealid=a.dealid where a.menuid=?';
		if($start!=0)
			$sql.=" and a.startdate>=?";
		if($brandid!=0)
			$sql.=" and a.vendorid=?";
		$sql.=" order by a.modified_on desc";
		if ($p == 1)
			$sql .= ' limit 5';
		else
			$sql .= " limit " . (($p - 1) * 5) . ",5";
		$q = $this->db->query ( $sql, array($menu,strtotime($start),$brandid) );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return False;
	}
	
	function getdealsforcategory($category,$p=1,$brandid=0) {
			
		$sql = 'select i.favs,i.slots,m.name as menu,a.dealtype,a.tagline,a.dealid,a.catid,a.brandid,a.startdate,a.enddate,a.pic,a.publish,b.name from king_deals
					as a join king_categories as b on b.id=a.catid join king_menu as m on m.id=a.menuid join king_dealitems as i on i.dealid=a.dealid where a.catid=?';
			if($brandid!=0)
				$sql.=" and a.vendorid=?";
		$sql.=" order by a.modified_on desc";
		if ($p == 1)
			$sql .= ' limit 5';
		else
			$sql .= " limit " . (($p - 1) * 5) . ",5";
		$q = $this->db->query ( $sql, array($category,$brandid) );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return False;
	}
	/**
	 * Gets list of deals for superadmin
	 *`
	 * @param int $p
	 * @return array|bool sometimes result array on success and sometimes boolean false on failure.
	 */
	function getdealslistforsuperadmin($p) {
		/*$sql = 'select a.dealid,a.catid,a.brandid,a.startdate,a.enddate,a.pic,a.publish,b.name from king_deals
	as a join king_categories as b on b.id=a.catid';*/
		if ($p == 1)
			$sql = 'select i.favs,i.slots,m.name as menu,a.dealtype,a.tagline, c.name as brandname,a.dealid,a.catid,a.brandid,a.startdate,a.enddate,a.pic,a.publish,b.name from king_deals
	as a join king_categories as b on b.id=a.catid join king_brands as c on c.id=a.brandid join king_menu as m on m.id=a.menuid join king_dealitems as i on i.dealid=a.dealid order by a.enddate desc limit 5';
		else
			$sql = "select i.favs,i.slots,a.dealtype,a.tagline, c.name as brandname,a.dealid,a.catid,a.brandid,a.startdate,a.enddate,a.pic,a.publish,b.name from king_deals
	as a join king_categories as b on b.id=a.catid join king_brands as c on c.id=a.brandid join king_dealitems as i on i.dealid=a.dealid order by a.enddate desc limit " . (($p - 1) * 5) . ",5";
		$q = $this->db->query ( $sql );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return False;
	}
	
	function getpendingorderscount($brandid=0)
	{
		if($brandid==0)
			$sql="select count(1) as len from king_orders where status=0";
		else
			$sql="select count(1) as len from king_orders where status=0 and brandid=?";
		$q=$this->db->query($sql,array($brandid));
		return $q->row()->len;
	}
	
	function getsoldoutitemsfordashboard($brandid=0){
		$sql="select item.name as itemname,deal.tagline,brand.name as brandname from king_dealitems as item join king_deals as deal on deal.dealid=item.dealid join king_brands as brand on brand.id=deal.brandid where item.available=item.quantity and deal.enddate>?";
	if($brandid!=0)
		$sql.=" and deal.vendorid=?";
	$sql.=' limit 7';
		$q = $this->db->query ( $sql,array(time(),$brandid) );
		if ($q->num_rows () > 0) 
			return $q->result ();
		return False;
		
	}
	
	function getexpdealslistfordashboard($brandid=0) {
		/*$sql = 'select a.dealid,a.catid,a.brandid,a.startdate,a.enddate,a.pic,a.publish,b.name from king_deals
	as a join king_categories as b on b.id=a.catid';*/
			$sql = 'select a.tagline, c.name as brandname,a.dealid,a.catid,a.brandid,a.startdate,a.enddate,a.pic,a.publish,b.name from king_deals
	as a join king_categories as b on b.id=a.catid join king_brands as c on c.id=a.brandid where a.enddate<?';
	if($brandid!=0)
		$sql.=" and a.vendorid=?";
	$sql.=' order by a.enddate desc limit 7';
		$q = $this->db->query ( $sql,array(time(),$brandid) );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return False;
	}
	
	function getdealslistfordashboard($brandid=0) {
		/*$sql = 'select a.dealid,a.catid,a.brandid,a.startdate,a.enddate,a.pic,a.publish,b.name from king_deals
	as a join king_categories as b on b.id=a.catid';*/
			$sql = 'select m.name as menu,a.tagline, c.name as brandname,a.dealid,a.catid,a.brandid,a.startdate,a.enddate,a.pic,a.publish,b.name from king_deals
	as a join king_categories as b on b.id=a.catid join king_brands as c on c.id=a.brandid join king_menu as m on m.id=a.menuid';
	if($brandid!=0)
		$sql.=" where a.vendorid=?";
	$sql.=' order by a.modified_on desc limit 7';
		$q = $this->db->query ( $sql,array($brandid) );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return False;
	}
	/**
	 * Get Deal Items for a particular deal
	 *
	 * @param int $dealid
	 * @return array|bool sometimes result array on success and sometimes boolean false on failure.
	 */
	function getdealitems($dealid) {
		//$sql='select id,dealid,price,orgprice,name,quantity,pic from king_dealitems where dealid=?';
		$sql = 'select favs,live,agentcom,id,available,dealid,price,orgprice,name,quantity,pic from king_dealitems where 
dealid=?';
		$q = $this->db->query ( $sql, $dealid );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return False;
	}
	/**
	 * Get Hotel Details of a deal
	 *
	 * @param int $dealid
	 * @return array|bool sometimes result array on success and sometimes boolean false on failure.
	 */
	function gethoteldeals($dealid) {
		$sql = 'select a.dealtype,a.dealid,a.catid,a.brandid,a.startdate,a.enddate,a.pic,a.publish,
		a.website,a.email,a.mobile,a.phone,a.address,a.city,a.state,a.pincode,b.latlong,b.heading,b.tagline,b.amenities,c.name,d.name as brandname from king_deals as a
			join king_hoteldeals as b on b.dealid=a.dealid join king_categories as c on a.catid=c.id join king_brands as d on d.id=a.brandid
			where a.dealid=?';
		$q = $this->db->query ( $sql, $dealid );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return False;
	}
	/**
	 * Get room details for a particular hotel
	 *
	 * @param int $dealidforroom
	 * @return array|bool sometimes result array on success and sometimes boolean false on failure.
	 */
	function getroomdealsforhotel($dealidforroom) {
		$sql = 'select a.id,a.dealid,a.price,a.orgprice,a.name,a.quantity,a.pic,b.roomid,
			b.dealid,b.heading,b.tagline,b.availability from king_dealitems as a
			join king_roomdeals as b on b.roomid=a.id and b.dealid=a.dealid
			where a.dealid=?';
		$q = $this->db->query ( $sql, $dealidforroom );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return False;
	}
	/**
	 * Gets the list of rooms for a hotel deal
	 *
	 * @param int $dealid
	 * @return array|bool sometimes result array on success and sometimes boolean false on failure.
	 */
	function getroomslist($dealid) {
		$sql = 'select id,dealid,name from king_dealitems where dealid=?';
		$q = $this->db->query ( $sql, array ($dealid ) );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return False;
	}
	/**
	 * Add resources
	 *
	 * @param int $dealid
	 * @param int $id
	 * @param int $type
	 * @param string $imgname
	 * @return bool on success true and on failure false
	 */
	function addresources($dealid, $id, $type, $imgname) {
		$sql = 'Insert into  king_resources(dealid,itemid,type,id) values(?,?,?,?)';
		$q = $this->db->query ( $sql, array ($dealid, $id, $type, $imgname ) );
		if ($this->db->affected_rows () > 0) {
			return True;
		} else
			return False;
	}
	/**
	 *Get Deal details based on dealid
	 *
	 * @param int $dealid
	 * @return array|bool sometimes result array on success and sometimes boolean false on failure.
	 */
	function editdeals($dealid) {
		//$sql='select dealid,catid,brandid,startdate,enddate,pic,description from king_deals where dealid=?';
		$sql = 'select a.featured_start,a.featured_end,a.is_coupon_applicable,is_giftcard,
						item.*,item.cod,item.sizing,item.shipsto,item.bp_expires,item.slots,
						a.menuid2,a.menuid,item.tax,item.service_tax,item.service_tax_cod,
						item.fcp,item.nlc,item.phc,item.shc,item.shipsin,item.rsp,item.itemcode,
						item.model,a.keywords,item.quantity,item.description1,item.description2,
						item.price,item.orgprice,item.viaprice,a.tagline,a.description,a.dealid,a.catid,
						a.brandid,a.startdate,a.enddate,a.pic,a.website,a.email,
						a.mobile,a.phone,a.address,a.city,a.state,a.pincode,b.name 
					from king_deals as a 
					join king_categories as b on b.id=a.catid 
					join king_dealitems as item on item.dealid=a.dealid 
					where a.dealid=? limit 1
				';
		$q = $this->db->query ( $sql, $dealid );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return False;
	}
	/**
	 * Updates a deal and its profile image
	 *
	 * @param int $dealid
	 * @param int $catid
	 * @param int $brandid
	 * @param string $startdate
	 * @param string $enddate
	 * @param string $pic
	 * @param string $description
	 * @return Bool on success true and on failure false
	 */
	function updatedealsimg($brandid, $startdate, $enddate,$img,$tagline, $description,$website,$email,$dealid,$db,$d,$p,$mp,$vp,$qua,$keywords,$itemcode,$model,$nlc,$phc,$shc,$rsp,$shipsin,$fcp) {
			$sql = "update king_deals set tagline=?,startdate=?,enddate=?,description=?,website=?,email=?,pic=?,keywords=? where dealid=?";
		//print_r($sql);exit;
		$q = $this->db->query ( $sql ,array($tagline, $startdate, $enddate, $description,$website,$email,$img,$keywords,$dealid));
		$sql="update king_dealitems set name=?,pic=?,quantity=?,price=?,viaprice=?,orgprice=?,description1=?,description2=?,itemcode=?,model=?,nlc=?,phc=?,shc=?,rsp=?,shipsin=?,fcp=?,modified=? where dealid=?";
		$this->db->query($sql,array($tagline,$img,$qua,$p,$vp,$mp,$db,$d,$itemcode,$model,$nlc,$phc,$shc,$rsp,$shipsin,$fcp,time(),$dealid));
				if ($this->db->affected_rows () > 0)
			return TRUE;
		else
			return FALSE;
	}
	/**
	 * Updates a deal and not its profile image
	 *
	 * @param int $dealid
	 * @param int $catid
	 * @param int $brandid
	 * @param string $startdate
	 * @param string $enddate
	 * @param string $description
	 * @return bool on success true and on failure false
	 */
	function updatedealsnoimg($brandid, $startdate, $enddate,$tagline, $description,$website,$email,$dealid,$db,$d,$p,$mp,$vp,$qua,$keywords,$itemcode,$model,$nlc,$phc,$shc,$rsp,$shipsin,$fcp) {
		$sql = "update king_deals set tagline=?,startdate=?,tagline=?,enddate=?,description=?,website=?,email=?,keywords=? where dealid=?";
		//print_r($sql);exit;
		$q = $this->db->query ( $sql ,array($tagline,$startdate, $tagline,$enddate, $description,$website,$email,$keywords,$dealid));
//		print_r($this->db->affected_rows ());exit;
		$sql="update king_dealitems set name=?,quantity=?,price=?,viaprice=?,orgprice=?,description1=?,description2=?,itemcode=?,model=?,nlc=?,phc=?,shc=?,rsp=?,shipsin=?,fcp=?,modified=? where dealid=?";
		$this->db->query($sql,array($tagline,$qua,$p,$vp,$mp,$db,$d,$itemcode,$model,$nlc,$phc,$shc,$rsp,$shipsin,$fcp,time(),$dealid));
		if ($this->db->affected_rows () ==1)
			return TRUE;
		else
			return FALSE;
	}
	/**
	 * Updates hotel details for a deal
	 *
	 * @param int $dealid
	 * @param string $address
	 * @param string $latlong
	 * @param int $phone
	 * @param string $email
	 * @param string $city
	 * @param string $heading
	 * @param string $tagline
	 * @param string $amenities
	 * @return bool on sucess true and on failure false
	 */
	function updatehoteldetails($dealid,$latlong,$heading, $tagline, $amenities) {
		$sql = "update king_hoteldeals set dealid='" . $dealid . "',latlong='" . $latlong . "',heading='" . $heading . "',tagline='" . $tagline . "',amenities='" . $amenities . "' where dealid='" . $dealid . "'";
		//print_r($sql);exit;
		$q = $this->db->query ( $sql );
		if ($this->db->affected_rows () > 0)
			return TRUE;
		else
			return FALSE;
	}
	/**
	 * deletes a Hotel deal 
	 *
	 * @param int $dealid
	 * @return bool on success true and on failure false
	 */
	function deletehoteldeal($dealid) {
		//$sql = "call delete_hoteldetails(?);";
		$sql = "DELETE u, up, upc,upd,upe
				FROM king_deals AS u
				LEFT JOIN king_hoteldeals AS up ON up.dealid = u.dealid
				LEFT JOIN king_dealitems AS upc ON upc.dealid = up.dealid
				LEFT JOIN king_roomdeals AS upd ON upd.dealid = upc.dealid
				LEFT JOIN king_resources AS upe ON upe.itemid = upc.id
				WHERE u.dealid= ?";
		//print_r($sql);exit;
		$q = $this->db->query ( $sql, $dealid );
		if ($this->db->affected_rows () > 0)
			return TRUE;
		else
			return FALSE;
	}
	/**
	 * Delete an item of a deal
	 *
	 * @param int $dealid
	 * @return bool on success true and on failure false
	 */
	function deleteitemdeal($dealid) {
		$sql = "DELETE u, up, upc
				FROM king_deals AS u
				LEFT JOIN king_dealitems AS up ON up.dealid = u.dealid
				LEFT JOIN king_resources AS upc ON upc.itemid = up.id
				WHERE u.dealid= ?";
		$q = $this->db->query ( $sql, $dealid );
		if ($this->db->affected_rows () > 0)
			return TRUE;
		else
			return FALSE;
	}
	/**
	 * Gets Room details of a dealid
	 *
	 * @param int $id
	 * @return array|bool  sometimes result array on success and sometimes boolean false on failure.
	 */
	function getroomdetailsforid($id) {
		$sql = 'select b.dealtype,a.id,a.dealid,a.price,a.orgprice,a.name,a.quantity,a.pic,b.roomid,
			b.heading,b.tagline,b.availability from king_dealitems as a
			join king_roomdeals as b on b.roomid=a.id and b.dealid=a.dealid
			where a.id=?';
		//print_r($sql);exit;
		$q = $this->db->query ( $sql, array ($id ) );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return False;
	}
	/**
	 * Get details of a deal item
	 *
	 * @param int $id
	 * @return array|bool sometimes result array on success and sometimes boolean false on failure.
	 */
	function getdealitemdetailsforid($id) {
		$sql = 'select deal.dealtype,deal.brandid,item.id,item.dealid,item.available,item.price,item.orgprice,item.name,item.quantity,item.pic,item.description1,item.description2 from king_dealitems as item join king_deals as deal on deal.dealid=item.dealid where item.id=?';
		//print_r($sql);exit;
		$q = $this->db->query ( $sql, array ($id ) );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return False;
	}
	
	function getcouponshistory($brandid)
	{
		$sql="select cp.id,cp.start,cp.end,cp.num,cp.time,user.name,brand.name as brandname from king_coupons_history as cp join king_admin as user on user.user_id=cp.createdby join king_brands as brand on brand.id=cp.brandid";
		if($brandid!=0)
			$sql.=" where cp.brandid=?";
		$sql.=" order by cp.time desc";
		return $this->db->query($sql,array($brandid))->result_array();
	}
	
	function generatecoupons($num,$per,$cat,$brand,$userid)
	{
		$sql="select max(sno) as m from king_coupons";
		$from=$this->db->query($sql)->row()->m;
		if($from==null)
			$from=0;
		$from++;
		$sql="insert into king_coupons(id,value,catid,brandid,used) values";
		$ar=array();
		for($i=0;$i<$num;$i++)
		{
			if($i>0)
				$sql.=",";
			$sql.="(?,?,?,?,0)";
			$id="";
			for($j=0;$j<4;$j++)
				$id.=rand(1000,9999);
			array_push($ar,$id,$per,$cat,$brand);
		}
		$this->db->query($sql,$ar);
		$sql="select max(sno) as m from king_coupons";
		$to=$this->db->query($sql)->row()->m;
		$sql="insert into king_coupons_history(start,end,num,time,createdby,brandid) values(?,?,?,?,?,?)";
		$this->db->query($sql,array($from,$to,$num,time(),$userid,$brand));
		return array($from,$to);
	}
	
	function getcoupons($start,$end,$brandid)
	{
		if($brandid==0)
		$sql="select * from king_coupons where sno>=? and sno<=$end and used=0";
		else
		$sql="select * from king_coupons where sno>=? and sno<=$end and used=0 and brandid=?";
		return $this->db->query($sql,array($start,$end,$brandid))->result_array();
	}
	
	function getdetailedcoupons($start,$end,$brandid)
	{
		$sql="select coupon.sno,coupon.id,coupon.value,brand.name as brandname,cat.name as catname from king_coupons as coupon join king_brands as brand on brand.id=coupon.brandid join king_categories as cat on cat.id=coupon.catid where coupon.sno>=? and coupon.sno<=? and coupon.used=0";
		if($brandid!=0)
			$sql.=" and coupon.brandid=?";
		$data=$this->db->query($sql,array($start,$end,$brandid))->result_array();
//		$sql="select sno,id,value,'Any' as brandname,'Any' as catname from king_coupons where sno>=? and sno<=? and used=0 and brandid=0 and catid=0";
//		$data=array_merge($data,$this->db->query($sql,array($start,$end))->result_array());
//		$sql="select coupon.sno,coupon.id,coupon.value,'Any' as brandname,cat.name as catname from king_coupons as coupon join king_categories as cat on cat.id=coupon.catid where coupon.sno>=? and coupon.sno<=? and used=0 and coupon.brandid=0 and coupon.catid!=0";
//		$data=array_merge($data,$this->db->query($sql,array($start,$end))->result_array());
//		$sql="select coupon.sno,coupon.id,coupon.value,'Any' as catname,brand.name as brandname from king_coupons as coupon join king_brands as brand on brand.id=coupon.brandid where coupon.sno>=? and coupon.sno<=? and used=0 and coupon.brandid!=0 and coupon.catid=0";
//		$data=array_merge($data,$this->db->query($sql,array($start,$end))->result_array());
		return $data;
	}
	
	function getactivitylen()
	{
		$sql="select count(1) as len from king_activity";
		return $this->db->query($sql)->row()->len;
	}
	
	function getactivitylenbybrand($brandid)
	{
		$sql="select count(1) as len from king_activity as act join king_deals as deal on deal.brandid=? where act.dealid=deal.dealid";
		return $this->db->query($sql,array($brandid))->row()->len;
	}
	
	function getactivitys($p=1)			//Thats not a typo
	{
		$sql="select user.name as username, act.id,act.msg,deal.tagline,deal.dealid,brand.name as brandname,act.time from king_activity as act join king_admin as user on user.user_id=act.userid join king_deals as deal on deal.dealid=act.dealid join king_brands as brand on brand.id=deal.brandid  order by time desc limit ".(($p-1)*10).", 10";
		return $this->db->query($sql)->result();
	}
	
	function getactivity($id)
	{
		$sql="select user.name as username, act.id,act.msg,deal.tagline,brand.name as brandname,deal.dealid as dealid, act.time from king_activity as act join king_admin as user on user.user_id=act.userid join king_deals as deal on deal.dealid=act.dealid join king_brands as brand on brand.id=deal.brandid  where act.id=?";
		return $this->db->query($sql,array($id))->result();
	}
	
	function getactivityfordeal($dealid)
	{
		$sql="select user.name as username, act.id,act.msg,deal.tagline,deal.dealid,brand.name as brandname,act.time from king_activity as act join king_admin as user on user.user_id=act.userid join king_deals as deal on deal.dealid=act.dealid join king_brands as brand on brand.id=deal.brandid  where act.dealid=? order by time desc";
		return $this->db->query($sql,array($dealid))->result();
	}
	
	function getactivitybybrand($brandid,$p=1)
	{
		$sql="select user.name as username, act.id,act.msg,deal.dealid,deal.tagline,brand.name as brandname,act.time from king_activity as act join king_admin as user on user.user_id=act.userid join king_deals as deal on deal.dealid=act.dealid join king_brands as brand on brand.id=deal.brandid  where act.brandid=? order by time desc limit ".(($p-1)*10).", 10";
		return $this->db->query($sql,array($brandid))->result();
	}
	
	function getallusers()
	{
		$sql="select * from king_admin order by name asc";
		$q=$this->db->query($sql);
		return $q->result();		
	}
	
	/**
	 * Update Room details for a particular hotel deal
	 *
	 * @param int $id
	 * @param int $dealid
	 * @param string $heading
	 * @param string $tagline
	 * @param int $availability
	 * @return bool on success true and on failure false
	 */
	function updateroomdetailsimg($id, $dealid, $heading, $tagline, $availability) {
		$sql = "update king_roomdeals set heading='" . $heading . "',tagline='" . $tagline . "',availability='" . $availability . "' where roomid='" . $id . "' and dealid='" . $dealid . "'";
		$q = $this->db->query ( $sql );
		if ($this->db->affected_rows () > 0)
			return TRUE;
		else
			return FALSE;
	}
	/**
	 * Update deal item details with image
	 *
	 * @param int $id
	 * @param int $dealid
	 * @param float $price
	 * @param float $originalprice
	 * @param string $name
	 * @param int $quantity
	 * @param string $pic
	 * @param string $description
	 * @return bool on sucess true and on failure false
	 */
	function updatedealitemdetailsimg($id, $dealid, $price, $originalprice, $name, $quantity, $pic, $description1,$description2) {
		$sql = "update king_dealitems set price=?,orgprice=?,name=?,quantity=?,pic=?,description1=?,description2=? where id=? and dealid=?";
		//print_r($sql);exit;
		$q = $this->db->query ( $sql ,array($price,$originalprice,$name,$quantity,$pic,$description1,$description2,$id,$dealid));
		if ($this->db->affected_rows () > 0)
			return TRUE;
		else
			return FALSE;
	}
	
	function getunpublisheddealscount()
	{
		$sql="select count(1) as len from king_deals where publish=0";
		return $this->db->query($sql)->row()->len;
	}
	
	/**
	 * Updates dealitems without image
	 *
	 * @param int $id
	 * @param int $dealid
	 * @param float $price
	 * @param float $originalprice
	 * @param string $name
	 * @param int $quantity
	 * @param string $description
	 * @return bool on success true and on failure false
	 */
	function updatedealitemdetailsnoimg($id, $dealid, $price, $originalprice, $name, $quantity, $description1,$description2) {
		$sql = "update king_dealitems set price=?,orgprice=?,name=?,quantity=?,description1=?,description2=? where id=? and dealid=?";
				//print_r($sql);exit;
		$q = $this->db->query ( $sql ,array($price,$originalprice,$name,$quantity,$description1,$description2,$id,$dealid));
		if ($this->db->affected_rows () > 0)
			return TRUE;
		else
			return FALSE;
	}
	/**
	 * Delete Room details
	 *
	 * @param int $roomid
	 * @return bool on success true and on failure false
	 */
	function deleteroomdetails($roomid) {
		//$sql = "delete a,b from explo_roomdeals as a Left Join explo_resources as b ON b.roomid=a.roomid where a.roomid=?;";
		$sql = "delete a,b,c 
from king_dealitems as a 
Left Join king_roomdeals as b ON b.roomid=a.id 
Left Join king_resources as c ON c.itemid=b.roomid 
where a.id=?";
		//print_r($sql);exit;
		$q = $this->db->query ( $sql, $roomid );
		if ($this->db->affected_rows () > 0)
			return TRUE;
		else
			return FALSE;
	}
	/**
	 * Delete Rooms
	 *
	 * @param int $roomid
	 * @return bool on success true and on failure false
	 */
	function deleteitemdealdetails($roomid) {
		//$sql = "delete a,b from explo_roomdeals as a Left Join explo_resources as b ON b.roomid=a.roomid where a.roomid=?;";
		$sql = "delete a,b
				from king_dealitems as a 
				Left Join king_resources as b ON b.itemid=a.id 
				where a.id=?";
		//print_r($sql);exit;
		$q = $this->db->query ( $sql, $roomid );
		if ($this->db->affected_rows () > 0)
			return TRUE;
		else
			return FALSE;
	}
	/**
	 * Gets Hotel pics of a deal
	 *
	 * @param int $dealid
	 * @return array|bool sometimes result array on success and sometimes boolean false on failure.
	 */
	function getpicsforhotel($dealid) {
		//$sql = 'select dealid,itemid,type,id from king_resources where type=0 and dealid=?';
		$sql = 'select a.dealid,a.itemid,a.type,a.id,b.name from king_resources as a
JOIN king_dealitems as b on b.id=a.itemid  where type=0 and a.dealid=?';
		//print_r($sql);exit;
		$q = $this->db->query ( $sql, array ($dealid ) );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return False;
	}
/**
 * Gets Hotel videos of a deal
 *
 * @param int $dealid
 * @return array|bool sometimes result array on success and sometimes boolean false on failure.
 */	
	function getvideosforhotel($dealid) {
		//$sql = 'select dealid,itemid,type,id from king_resources where type=1 and dealid=?';
		$sql = 'select a.dealid,a.itemid,a.type,a.id,b.name from king_resources as a
JOIN king_dealitems as b on b.id=a.itemid  where type=1 and a.dealid=?';
		//print_r($sql);exit;
		$q = $this->db->query ( $sql, array ($dealid ) );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return False;
	}
/**
 * Deletes Pics and videos of a hotel deal
 *
 * @param string $id
 * @param int $itemid
 * @param int $dealid
 * @return bool on sucess true and on failure false.
 */	
	function deletepicandvideos($id, $itemid, $dealid) {
		$sql = "delete  from king_resources where id=? and itemid=? and dealid=?;";
		//print_r($sql);exit;
		$q = $this->db->query ( $sql, array ($id, $itemid, $dealid ) );
		if ($this->db->affected_rows () > 0)
			return TRUE;
		else
			return FALSE;
	}
/**
 * Gets Brands that have no admin assigned
 *
 * @return array|bool sometimes result array on success and sometimes boolean false on failure.
 */	
	function getbrands() {
		$sql = "Select * from king_brands where id not in(select brandid from king_admin)";
		$q = $this->db->query ( $sql );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return false;
	}
/**
 * Gets the List of main categories
 *
 * @return array|bool sometimes result array on success and sometimes boolean false on failure.
 */	
	function getcategory() {
		$sql = "Select * from king_categories where type=0 order by name asc";
		$q = $this->db->query ( $sql );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return false;
	}
/**
 * Gets the list of categories for a usertype
 *
 * @param int $brandid
 * @return array|bool sometimes result array on success and sometimes boolean false on failure.
 */	
	function getcategoryforusertype($brandid) {
		//$sql="Select * from king_categories where type=0";
		$sql = "select a.id as catid,a.name from king_categories as a order by a.name asc";
		$q = $this->db->query ( $sql, $brandid );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return false;
	}
/**
 * Gets the list of subcategories
 *
 * @param int $type
 * @return array|bool sometimes result array on success and sometimes boolean false on failure.
 */	
	function getsubcategories($type) {
		$sql = "Select * from king_categories where type=?";
		$q = $this->db->query ( $sql, $type );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return false;
	}
/**
 * Assigns a brand for a category
 *
 * @param string $brandname
 * @param int $catid
 * @return bool on success true and on failure false
 */	
	function insertbrand($brandname, $catid) {
		$sql = "call insert_brandstocat1(?,?)";
		$q = $this->db->query ( $sql, array ($brandname, $catid ) );
		if ($this->db->affected_rows () > 0)
			return TRUE;
		else
			return FALSE;
	}
/**
 * Inserts New Main Category
 *
 * @param string $categoryname
 * @return bool on success true and on failure false
 */	
	function insertcategories($categoryname,$description,$type) {
//		$type = 0;
		$url=str_replace(" ","-",$categoryname);
		$sql = "insert into king_categories(type,name,url,description) values(?,?,?,?)";
		$q = $this->db->query ( $sql, array ($type,$categoryname,$url,$description));
//		if ($this->db->affected_rows () > 0)
			return TRUE;
//		else
//			return FALSE;
	}
/**
 * Inserts New Sub category for a category
 *
 * @param int $type
 * @param string $categoryname
 * @return bool on success true and on failure false
 */	
	function insertsubcategories($type, $categoryname) {
		$sql = "insert into king_categories(type,name) values(?,?)";
		$q = $this->db->query ( $sql, array ($type, $categoryname ) );
		if ($this->db->affected_rows () > 0)
			return TRUE;
		else
			return FALSE;
	}
	function publish($dealid, $catid, $itemid,$publish,$agentcom,$live) {
		$sql = "update king_deals set publish=? where dealid=? and catid=?";
		$q = $this->db->query ( $sql ,array($publish,$dealid,$catid));
		if($publish==1)
		{
		$sql="update king_dealitems set agentcom=?,live=? where id=?";
		$this->db->query($sql,array($agentcom,$live,$itemid));
		}
	}
	
	function changecom($id,$com)
	{
		$this->db->query("update king_dealitems set agentcom=? where id=? limit 1",array($com,$id));
	}
	
	function livedeal($id,$live)
	{
		$this->db->query("update king_dealitems set live=? where id=? limit 1",array($live,$id));
	}
	
	function getadmindetails($brandid, $usertype) {
		$sql = "Select * from king_admin where brandid=? and usertype=?";
		$q = $this->db->query ( $sql, array ($brandid, $usertype ) );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return false;
	}
	
	function getdealdetailsforpic($pic) {
		$sql = "select a.dealid,a.catid,a.brandid,a.pic,b.name as brandname from king_deals as a join
king_brands as b on b.id=a.brandid where a.pic=?";
		$q = $this->db->query ( $sql, $pic );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return false;
	}
	function getdealdetailsforid($id) {
		$sql = "select m.name as menu,a.dealid,a.catid,a.brandid,a.pic,b.name as brandname from king_deals as a join
king_brands as b on b.id=a.brandid join king_menu as m on m.id=a.menuid where a.dealid=?";
		$q = $this->db->query ( $sql, $id );
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return false;
	}
	function getrecentactivityfordashboard()
	{
		$sql="select act.id,act.msg,user.name,time from king_activity as act join king_admin as user on user.user_id=act.userid order by act.time desc limit 7";
		$q=$this->db->query($sql);
		return $q->result_array();
	}
	
	function getbrand($bid)
	{
		$sql="select * FROM king_brands where id=?";
		$q=$this->db->query($sql,array($bid));
		if($q->num_rows()==1)
			return $q->row_array();
		return false;
	}
	
	function getbranddetails($bid)
	{
		$sql="select brand.name as brandname,brand.id from king_brands as brand where brand.id=?";
		$q=$this->db->query($sql,array($bid));
		if($q->num_rows()>0)
			return $q->row_array();
		return false;
	}
	
	function getorderslenbyuser($brandid=0,$uid)
	{
		$sql="select count(1) as len from king_orders where userid=?";
		if($brandid!=0)
			$sql.=" and vendorid=?";
		if(!empty($from) && !empty($to))
		{
			list($d,$m,$y)=explode("-",$from);
			$from=mktime(0,0,0,$m,$d,$y);
			list($d,$m,$y)=explode("-",$to);
			$to=mktime(23,59,59,$m,$d,$y);
			$sql.=" and time between $from and $to";
		}
		$q=$this->db->query($sql,array($uid,$brandid));
		return $q->row()->len;
	}
	
	function getorderslenbystatus($brandid=0,$status,$from="",$to="",$priority=0,$readytoship=0)
	{
		
		 
		 
		if($status=="priority")
		{
			$status="notshipped"; 
			$priority = 1;
		}	
		
		 
		
		
		$sql="select count(1) as len from king_orders o";
		if($readytoship)
			$sql.=" join king_stock s on s.itemid=o.itemid and (s.available+1)>=o.quantity";
			
		$sql.=" where ";
		switch($status)
		{
			case "pending" :
				$sql.="o.admin_order_status=0";
				break;
			case "notshipped" :
				$sql.="o.admin_order_status=1";
				break;
			 
			case "shipped":
				$sql.="o.admin_order_status=3";
				break;
			case "delivered" :
				$sql.="o.admin_order_status=4";
				break;	
			case "rejected" :
				$sql.="o.admin_order_status=6";
				break;
			case "returned" :
				$sql.="o.admin_order_status=5";
				break;  
		}
		if($priority)
			$sql.=" and o.priority=1";
		if($brandid!=0)
			$sql.=" and vendorid=?";
		if(!empty($from) && !empty($to))
		{
			list($d,$m,$y)=explode("-",$from);
			$from=mktime(0,0,0,$m,$d,$y);
			list($d,$m,$y)=explode("-",$to);
			$to=mktime(23,59,59,$m,$d,$y);
			$sql.=" and time between $from and $to";
		}
		$q=$this->db->query($sql,array($brandid));
		return $q->row()->len;
	}
	
	
function gettranslenbystatus($brandid=0,$status,$from="",$to="",$priority=0,$readytoship=0)
	{
		
		 
		 
		if($status=="priority")
		{
			$status="notshipped"; 
			$priority = 1;
		}	
		
		 
		
		
		$sql="select count(1) as len from king_orders o join king_transactions t on t.transid = o.transid ";
		if($readytoship)
			$sql.=" join king_stock s on s.itemid=o.itemid and (s.available+1)>=o.quantity";
			
		$sql.=" where ";
		switch($status)
		{
			case "pending" :
				$sql.="t.admin_trans_status=0";
				break;
			case "pinvoiced" :
				$sql.="t.admin_trans_status=1";
				break;
			case "invoiced" :
				$sql.="t.admin_trans_status=2";
				break;	
			 
			case "pshipped" :
				$sql.="t.admin_trans_status=3";
				break;
			case "shipped" :
				$sql.="t.admin_trans_status=4";
				break;	
			case "closed" :
				$sql.="t.admin_trans_status=5";
				break;	
			case "cancelled" :
				$sql.="t.admin_trans_status=6";
				break; 
		}
		if($priority)
			$sql.=" and o.priority=1";
		if($brandid!=0)
			$sql.=" and vendorid=?";
		if(!empty($from) && !empty($to))
		{
			list($d,$m,$y)=explode("-",$from);
			$from=mktime(0,0,0,$m,$d,$y);
			list($d,$m,$y)=explode("-",$to);
			$to=mktime(23,59,59,$m,$d,$y);
			$sql.=" and time between $from and $to";
		}
		$q=$this->db->query($sql,array($brandid));
		return $q->row()->len;
	}
	
	function getordersbystatus($status,$brandid=0,$p=1,$sort="customer",$order="a",$from="",$to="",$priority=0,$readytoship=0)
	{
		
		 
		 
		if($status=="priority")
		{
			$status="notshipped"; 
			$priority = 1;
		}	
		
		
		
		$sql="select orders.is_giftcard,orders.ship_city,t.status as trans_status,t.admin_trans_status,orders.admin_order_status,orders.transid,brand.name as brandname, item.name as itemname,orders.quantity,orders.id,orders.status,orders.actiontime,orders.time,user.name as username from king_orders as orders join king_users as user on user.userid=orders.userid join king_dealitems as item on item.id=orders.itemid join king_brands as brand on brand.id=orders.brandid 
				join king_transactions t on t.transid = orders.transid ";
		if($readytoship)
			$sql.=" join king_stock s on s.itemid=orders.itemid and (s.available+1)>=orders.quantity";
		$sql.=" where ";
		switch($status)
		{
			case "pending" :
				$sql.="orders.admin_order_status=0";
				break;
			case "notshipped" :
				$sql.="orders.admin_order_status=1";
				break;
			 
			case "shipped":
				$sql.="orders.admin_order_status=3";
				break;
			case "delivered" :
				$sql.="orders.admin_order_status=4";
				break;	
			case "rejected" :
				$sql.="orders.admin_order_status=6";
				break;
			case "returned" :
				$sql.="orders.admin_order_status=5";
				break; 
		}
		
		if($priority)
			$sql.=" and orders.priority=1";
			
		if($brandid!=0)
			$sql.=" and orders.vendorid=?";
			
		if(!empty($from) && !empty($to))
		{
			list($d,$m,$y)=explode("-",$from);
			$from=mktime(0,0,0,$m,$d,$y);
			list($d,$m,$y)=explode("-",$to);
			$to=mktime(23,59,59,$m,$d,$y);
			$sql.=" and orders.time between $from and $to";
		}
		$sql.=" order by ";
		switch($sort)
		{
			case "customer":
				$sql.="user.name";
				break;
			case "itemname":
				$sql.="item.name";
				break;
			case "brand":
				$sql.="brand.name";
				break;
			case "quantity":
				$sql.="orders.quantity";
				break;
			case "ordertime":
				$sql.="orders.time";
				break;
			case "actiontime":
				$sql.="orders.actiontime";
				break;
			case "status":
				$sql.="orders.admin_order_status";
				break;
			default :
				$sql.="orders.time";
		}
		switch($order)
		{
			case "a":
				$sql.=" ASC";
				break;
			case "d":
				$sql.=" desc";
				break;
			default:
				$sql.=" desc";
		}
		$sql.=" limit ".(($p-1)*20).",20";
		$q=$this->db->query($sql,array($brandid));
		
		//echo $this->db->last_query();
		return $q->result();
	}
	
	
	function gettransbystatus($status,$brandid=0,$p=1,$sort="customer",$order="a",$from="",$to="",$priority=0,$readytoship=0)
	{
		
		 
		 
		if($status=="priority")
		{
			$status="notshipped"; 
			$priority = 1;
		}	
		
		
		
		$sql="select orders.is_giftcard,orders.ship_city,t.status as trans_status,t.admin_trans_status,orders.admin_order_status,orders.transid,brand.name as brandname, item.name as itemname,orders.quantity,orders.id,orders.status,orders.actiontime,orders.time,user.name as username from king_orders as orders join king_users as user on user.userid=orders.userid join king_dealitems as item on item.id=orders.itemid join king_brands as brand on brand.id=orders.brandid
				join king_transactions t on t.transid = orders.transid 
		";
		if($readytoship)
			$sql.=" join king_stock s on s.itemid=orders.itemid and (s.available+1)>=orders.quantity";
		$sql.=" where ";
		
		 
		switch($status)
		{
			case "pending" :
				$sql.="t.admin_trans_status=0";
				break;
			case "pinvoiced" :
				$sql.="t.admin_trans_status=1";
				break;
			case "invoiced" :
				$sql.="t.admin_trans_status=2";
				break;	
			 
			case "pshipped" :
				$sql.="t.admin_trans_status=3";
				break;
			case "shipped" :
				$sql.="t.admin_trans_status=4";
				break;	
			case "closed" :
				$sql.="t.admin_trans_status=5";
				break;	
			case "cancelled" :
				$sql.="t.admin_trans_status=6";
				break;
			 
		}
		
		if($priority)
			$sql.=" and t.priority=1";
			
		if($brandid!=0)
			$sql.=" and orders.vendorid=?";
			
		if(!empty($from) && !empty($to))
		{
			list($d,$m,$y)=explode("-",$from);
			$from=mktime(0,0,0,$m,$d,$y);
			list($d,$m,$y)=explode("-",$to);
			$to=mktime(23,59,59,$m,$d,$y);
			$sql.=" and orders.time between $from and $to";
		}
		$sql.=" order by ";
		switch($sort)
		{
			case "customer":
				$sql.="user.name";
				break;
			case "itemname":
				$sql.="item.name";
				break;
			case "brand":
				$sql.="brand.name";
				break;
			case "quantity":
				$sql.="orders.quantity";
				break;
			case "ordertime":
				$sql.="orders.time";
				break;
			case "actiontime":
				$sql.="orders.actiontime";
				break;
			case "status":
				$sql.="orders.admin_order_status";
				break;
			default :
				$sql.="orders.time";
		}
		switch($order)
		{
			case "a":
				$sql.=" ASC";
				break;
			case "d":
				$sql.=" desc";
				break;
			default:
				$sql.=" desc";
		}
		$sql.=" limit ".(($p-1)*20).",20";
		$q=$this->db->query($sql,array($brandid));
		
		//echo $this->db->last_query();
		return $q->result();
	}
	
	function getordersbyuser($uid,$brandid=0,$p=1,$sort="customer",$order="a")
	{
		$sql="select orders.is_giftcard,orders.ship_city,t.status as trans_status,t.admin_trans_status,orders.admin_order_status,orders.ship_city,orders.transid,brand.name as brandname, item.name as itemname,orders.quantity,orders.id,orders.status,orders.actiontime,orders.time,user.name as username from king_orders as orders join king_users as user on user.userid=orders.userid join king_dealitems as item on item.id=orders.itemid join king_brands as brand on brand.id=orders.brandid
						join king_transactions t on t.transid = orders.transid 
				";
		$sql.=" where orders.userid=?";
		if($brandid!=0)
			$sql.=" and orders.vendorid=?";
		if(!empty($from) && !empty($to))
		{
			list($d,$m,$y)=explode("-",$from);
			$from=mktime(0,0,0,$m,$d,$y);
			list($d,$m,$y)=explode("-",$to);
			$to=mktime(23,59,59,$m,$d,$y);
			$sql.=" and orders.time between $from and $to";
		}
		$sql.=" order by ";
		switch($sort)
		{
			case "customer":
				$sql.="user.name";
				break;
			case "itemname":
				$sql.="item.name";
				break;
			case "brand":
				$sql.="brand.name";
				break;
			case "quantity":
				$sql.="orders.quantity";
				break;
			case "ordertime":
				$sql.="orders.time";
				break;
			case "actiontime":
				$sql.="orders.actiontime";
				break;
			case "status":
				$sql.="orders.admin_order_status";
				break;
			default :
				$sql.="orders.time";
		}
		switch($order)
		{
			case "a":
				$sql.=" ASC";
				break;
			case "d":
				$sql.=" desc";
				break;
			default:
				$sql.=" desc";
		}
		$sql.=" limit ".(($p-1)*20).",20";
		$q=$this->db->query($sql,array($uid,$brandid));
		return $q->result();
	}
	
	function getorderslenfordeal($brandid=0,$from="",$to="",$readytoship=false)
	{
		$sql="select count(1) as len from king_orders o";
		if($readytoship)
			$sql.=" join king_stock s on s.available+1>o.quantity and s.itemid=o.itemid";
		$sql.=" where itemid=?";

		if(!empty($from) && !empty($to))
		{
			list($d,$m,$y)=explode("-",$from);
			$from=mktime(0,0,0,$m,$d,$y);
			list($d,$m,$y)=explode("-",$to);
			$to=mktime(23,59,59,$m,$d,$y);
			$sql.=" and time between $from and $to";
		}
			
		$q=$this->db->query($sql,array($brandid));
		return $q->row()->len;
	}
	
	function getorderslen($brandid=0,$from="",$to="",$readytoship=false)
	{
		$sql="select count(1) as len from king_orders o";
		if($readytoship)
			$sql.=" join king_stock s on s.available+1>o.quantity and s.itemid=o.itemid";
		if($brandid!=0)
			$sql.=" where vendorid=?";
		else
			$sql.=" where 1";

		if(!empty($from) && !empty($to))
		{
			list($d,$m,$y)=explode("-",$from);
			$from=mktime(0,0,0,$m,$d,$y);
			list($d,$m,$y)=explode("-",$to);
			$to=mktime(23,59,59,$m,$d,$y);
			$sql.=" and time between $from and $to";
		}
			
		$q=$this->db->query($sql,array($brandid));
		return $q->row()->len;
	}
	
	function update_deal_product_links()
	{
		$itemid=$this->db->query("select id as itemid from king_dealitems where dealid=?",$_POST['dealid'])->row()->itemid;
		$this->db->query("delete from m_product_deal_link where itemid=?",$itemid);
		foreach($_POST['prods_id'] as $i=>$id)
		{
			$mrp=$this->db->query("select mrp from m_product_info where product_id=?",$id)->row()->mrp;
			$this->db->query("insert into m_product_deal_link(itemid,product_id,product_mrp,qty) values(?,?,?,?)",array($itemid,$id,$mrp,$_POST['prods_qty'][$i]));
		}
		$this->db->query("delete from m_product_group_deal_link where itemid=?",$itemid);
		foreach($_POST['prods_g_id'] as $i=>$id)
		{
			$mrp=$this->db->query("select p.mrp from products_group_pids g join m_product_info p on p.product_id=g.product_id where g.group_id=?",$id)->row()->mrp;
			$this->db->query("insert into m_product_deal_link(itemid,group_id,product_mrp,qty) values(?,?,?,?)",array($itemid,$id,$mrp,$_POST['prods_g_qty'][$i]));
		}
	}
	
	function recentordersbyuser($uid)
	{
		return $this->db->query("select distinct(transid),o.transid,o.time,o.actiontime from king_orders o where userid=? order by sno desc limit 10",$uid)->result_array();
	}
	
	function recentftransactionsbyuser($uid)
	{
		return $this->db->query("select distinct(transid) as transid,o.time from king_tmp_orders o where userid=? order by o.sno desc limit 10",$uid)->result_array();
	}
	
	function recentticketsbyuser($uid)
	{
		return $this->db->query("select * from support_tickets where user_id=? order by ticket_id desc limit 10",$uid)->result_array();
	}
	
	function sendmail($emails,$sub,$msg)
	{
		$this->load->library("email");
		$this->load->model("viakingmodel","emmodel");
		$this->emmodel->email(array_unique($emails),$sub,$msg,true);
	}
	
	function getordersfortrans($tid)
	{
		return $this->db->query("select o.*,
										i.name as item,
										inv.invoice_no
									from king_orders o 
									join king_dealitems as i on i.id=o.itemid
									left join king_invoice inv on inv.order_id = o.id and inv.invoice_status = 1    
									where o.transid=? 
									  group by o.id order by inv.invoice_no
								",$tid)->result_array();
	}
	
	function gettransaction($tid)
	{
		return $this->db->query("select * from king_transactions where transid=?",$tid)->row_array();
	}

	function email($emails,$sub,$msg)
	{
		$this->load->library("email");
		if(!is_array($emails))
			$emails=array($emails);
		foreach($emails as $email)
		{
			$config=array('mailtype'=>"html");
			$this->email->initialize($config);
			$this->email->from("support@snapittoday.com","Snapittoday");
			$this->email->to($email);
			$this->email->subject($sub);
			$this->email->message($msg);
			$this->email->send();
		}
	}
	
	function changetransstatus($transid,$status,$dmed="",$track="",$shipdate="",$orders=array())
	{
		
		 
		
		$trans=$this->db->query("select * from king_transactions where transid=?",$transid)->row_array();

		$t_amount=$trans['amount']-$trans['ship']-$trans['cod'];
			
		$transid=$trans['transid'];
 
		$o_amount=$this->db->query("select sum(i.price*o.quantity) as s from king_orders o join king_dealitems i on i.id=o.itemid where o.transid=?",$transid)->row()->s;

		$m_amount=$this->db->query("select sum(i.orgprice*o.quantity) as s from king_orders o join king_dealitems i on i.id=o.itemid where o.transid=?",$transid)->row()->s;
	
		$discount=$m_amount-$t_amount;
					
		$total_orders = count($orders);
		
		 
		
		
		$is_trans_invoiced = $this->db->query("select invoice_no from king_invoice where transid=? and invoice_status = 1 ",$transid)->num_rows();
		
		
		
		$total_invoiced_inselected_orders = $this->db->query("select invoice_no from king_invoice where transid=? and order_id in (".implode(',',$orders).") and invoice_status = 1 ",$transid)->num_rows(); 
		 
		//echo $total_invoiced_inselected_orders;
		
		if($total_invoiced_inselected_orders < $total_orders)
		{
			$invoice_no=$this->db->query("select invoice_no from king_invoice order by id desc limit 1")->row()->invoice_no+1;
			
			
			$order_list = $this->db->query("select * from king_orders where transid=? and id in (".implode(',',$orders).") and status = 0 ",$transid)->result_array();
			
			foreach($order_list as $in_ord)
			{
					$oid=$in_ord['id'];
				/*if($this->db->query("select 1 from king_invoice where order_id=? and transid=? and invoice_no=?",array($oid,$transid,$invoice_no))->num_rows()!=0)
					$this->db->query("delete from king_invoice where order_id=? and transid=? and invoice_no=? limit 3",array($oid,$transid,$invoice_no));*/

					$orgprice = $in_ord['i_orgprice'];
					$itemdet=$this->db->query("select orgprice,price,nlc,phc,tax,service_tax from king_dealitems where id=?",$in_ord['itemid'])->row_array();

					$o_discount=$orgprice*$discount/$m_amount;
					
					/*$nlc=($itemdet['orgprice']-$o_discount)*100/114;
					$phc=$itemdet['orgprice']-$o_discount-$nlc;*/
					
					if(!$is_trans_invoiced){
						$cod=$trans['cod'];
						$ship=$trans['ship'];	
					}else{
						$cod = $ship = 0;
					}
					 
					
					$o_discount = $in_ord['i_discount']+$in_ord['i_coup_discount'];
					
					$tmp_offer_price = ($in_ord['i_orgprice'])-$o_discount;
					
					
					$nlc = round(($tmp_offer_price*100/((1+PHC_PERCENT/100)*100)),2);
					$phc = $tmp_offer_price-$nlc;
					$tax = $itemdet['tax'];
									
					
					$this->db->query("insert into king_invoice(transid,order_id,invoice_no,phc,nlc,tax,service_tax,mrp,discount,cod,ship,invoice_status,createdon) values(?,?,?,?,?,?,?,?,?,?,?,1,?)",array($transid,$oid,$invoice_no,$phc,$nlc,$tax,PRODUCT_SERVICE_TAX*100,$orgprice,$o_discount,$cod,$ship,time()));
			}
		}
			
		
		
		 
		
		foreach($orders as $oid)
		{
			if($status==1)
			{
				$in_ord=$this->db->query("select quantity,itemid,invoice_no,transid from king_orders where id=? and status = 0 ",$oid)->row_array();
				if($in_ord){
					$s=$this->db->query("select id,available from king_stock where itemid=?",$in_ord['itemid'])->row_array();
					$stock=0;
					if(!empty($s))
						$stock=$s['available'];
					if($stock<$in_ord['quantity'])
						continue;
					$this->db->query("update king_stock set available=available-? where itemid=? limit 1",array($in_ord['quantity'],$in_ord['itemid']));
					$this->db->query("insert into king_stock_activity(stockids,type,remarks,reference_no,purchase_date,vendor) values(?,1,?,?,?,?)",array($s['id'],$transid,$oid,date("d-m-Y"),"snapittoday"));
					$transid=$in_ord['transid']; 
				}
			}
			
			if($status==3)
				$this->db->query("update king_invoice set invoice_status = 2 where order_id=? limit 1",$oid); // cancelled invoice 
			
			$sql="update king_orders set status=? , actiontime=?";
			if($status==2)
				$sql.=",shipped=1,medium=?,shipid=?,shiptime=?";
			$sql.=" where id=? and status!=2 limit 1";
			if($status==4)
			{
				$sql="update king_orders set status=?, actiontime=? where id=? and status=3 limit 1";
				$status=0;
			}
			if($status==2)
			{
				$this->db->query($sql,array($status,time(),$dmed,$track,strtotime($shipdate),$oid));
				$emails=$this->db->query("select itemid,transid, bill_email as email1, ship_email as email2 from king_orders where id=?",$oid)->row_array();
				$msg=$this->load->view("mails/ordership",array('orderid'=>$oid,'transid'=>$emails['transid'],'shippedon'=>strtotime($shipdate),'courier'=>$dmed,'awn'=>$track,'item'=>$this->db->query('select name from king_dealitems where id=?',$emails['itemid'])->row()->name),true);
				$this->sendmail(array($emails['email1'],$emails['email2']),"Alert: Your order (transid: {$emails['transid']}) was shipped!",$msg);
			}
			else
			$this->db->query($sql,array($status,time(),$oid));
		}
	}

	
	function changeorderstatus($oid,$status,$dmed="",$track="",$shipdate="")
	{
		if($status==1)
		{
			$in_ord=$this->db->query("select itemid,invoice_no,transid from king_orders where id=?",$oid)->row_array();
			$transid=$in_ord['transid'];
			$invoice_no=$in_ord['invoice_no'];
			if($this->db->query("select 1 from king_invoice where order_id=? and transid=? and invoice_no=?",array($oid,$transid,$invoice_no))->num_rows()==0)
			{
				$itemdet=$this->db->query("select nlc,phc,tax,service_tax from king_dealitems where id=?",$in_ord['itemid'])->row_array();
				$this->db->query("insert into king_invoice(transid,order_id,invoice_no,phc,nlc,tax,service_tax) values(?,?,?,?,?,?,?)",array($transid,$oid,$invoice_no,$itemdet['phc'],$itemdet['nlc'],$itemdet['tax'],$itemdet['service_tax']));
			}
		}
		
		$sql="update king_orders set status=? , actiontime=?";
		if($status==2)
			$sql.=",medium=?,shipid=?,shiptime=?";
		$sql.=" where id=? and status!=2 limit 1";
		if($status==4)
		{
			$sql="update king_orders set status=?, actiontime=? where id=? and status=3 limit 1";
			$status=0;
		}
		if($status==2)
		{
			$this->db->query($sql,array($status,time(),$dmed,$track,strtotime($shipdate),$oid));
			$emails=$this->db->query("select itemid,transid, bill_email as email1, ship_email as email2 from king_orders where id=?",$oid)->row_array();
			$msg=$this->load->view("mails/ordership",array('orderid'=>$oid,'transid'=>$emails['transid'],'shippedon'=>strtotime($shipdate),'courier'=>$dmed,'awn'=>$track,'item'=>$this->db->query('select name from king_dealitems where id=?',$emails['itemid'])->row()->name),true);
			$this->sendmail(array($emails['email1'],$emails['email2']),"Alert: Your order (transid: {$emails['transid']}) was shipped!",$msg);
		}
		else
		$this->db->query($sql,array($status,time(),$oid));
	}

	function getorder($orderid)
	{
		$sql="select o.admin_order_status,item.price,item.orgprice,o.transid,o.ship_email,o.bill_email,o.buyer_options,o.mode,o.shipid,o.shiptime,o.medium,o.actiontime,o.id,o.status,item.pic as itempic,o.quantity,item.phc,item.nlc,item.service_tax,item.tax,item.name,o.paid,o.time,o.bill_person,o.bill_address,o.bill_phone,o.bill_city,o.bill_pincode,o.ship_person,o.ship_address,o.ship_phone,o.ship_city,o.ship_pincode from king_orders as o join king_dealitems as item on item.id=o.itemid where o.id=?";
		$q=$this->db->query($sql,array($orderid));
		if($q->num_rows()==1)
			return $q->row();
		return false;
	}
	
	function getorders($brandid=0,$p=1,$sort="customer",$order="a",$vendorid=0,$from="",$to="")
	{
		$inps=array();
		$sql="select orders.is_giftcard,orders.ship_city,orders.transid,t.admin_trans_status,orders.admin_order_status,t.status as trans_status,brand.name as brandname, orders.id,item.name as itemname,
					orders.quantity,orders.status,orders.actiontime,orders.time,user.name as username 
					from king_orders as orders 
					join king_users as user on user.userid=orders.userid 
					join king_dealitems as item on item.id=orders.itemid 
					join king_transactions t  on t.transid = orders.transid 
					join king_brands as brand on brand.id=orders.brandid";
		if($brandid!=0 && $vendorid!=0)
		{
			$inps=array($brandid,$vendorid);
			$sql.=" where orders.brandid=? and orders.vendorid=?";
		}
		elseif($brandid!=0)
		{
			$inps=array($brandid); 
			$sql.=" where orders.brandid=? ";
		}
		elseif($vendorid!=0)
		{
			$inps=array($vendorid); 
			$sql.=" where orders.vendorid=?";
		}
		else
			$sql.=" where 1";
		if(!empty($from) && !empty($to))
		{
			list($d,$m,$y)=explode("-",$from);
			$from=mktime(0,0,0,$m,$d,$y);
			list($d,$m,$y)=explode("-",$to);
			$to=mktime(23,59,59,$m,$d,$y);
			$sql.=" and orders.time between $from and $to";
		}
		$sql.=" group by orders.transid order by ";
		switch($sort)
		{
			case "customer":
				$sql.="user.name";
				break;
			case "itemname":
				$sql.="item.name";
				break;
			case "brand":
				$sql.="brand.name";
				break;
			case "quantity":
				$sql.="orders.quantity";
				break;
			case "ordertime":
				$sql.="orders.time";
				break;
			case "actiontime":
				$sql.="orders.actiontime";
				break;
			case "status":
				$sql.="orders.admin_order_status";
				break;
			default :
				$sql.="orders.time";
		}
		switch($order)
		{
			case "a":
				$sql.=" ASC";
				break;
			case "d":
				$sql.=" desc";
				break;
			default:
				$sql.=" desc";
		}
		$sql.=" limit ".(($p-1)*20).",20";
		$q=$this->db->query($sql,$inps);
		
		
		//echo $this->db->last_query();
		
		return $q->result();
	}
	
	function getordersfordeal($brandid=0,$p=1,$sort="customer",$order="a",$vendorid=0,$from="",$to="")
	{
		$inps=array();
		$sql="select orders.is_giftcard,orders.ship_city,orders.transid,orders.admin_order_status,t.status as trans_status,brand.name as brandname, orders.id,item.name as itemname,
					orders.quantity,orders.status,orders.actiontime,orders.time,user.name as username 
					from king_orders as orders 
					join king_users as user on user.userid=orders.userid 
					join king_dealitems as item on item.id=orders.itemid 
					join king_transactions t  on t.transid = orders.transid 
					join king_brands as brand on brand.id=orders.brandid";
		if($brandid!=0 && $vendorid!=0)
		{
			$inps=array($brandid,$vendorid);
			$sql.=" where orders.itemid=? and orders.vendorid=?";
		}
		elseif($brandid!=0)
		{
			$inps=array($brandid); 
			$sql.=" where orders.itemid=? ";
		}
		elseif($vendorid!=0)
		{
			$inps=array($vendorid); 
			$sql.=" where orders.vendorid=?";
		}
		else
			$sql.=" where 1";
		if(!empty($from) && !empty($to))
		{
			list($d,$m,$y)=explode("-",$from);
			$from=mktime(0,0,0,$m,$d,$y);
			list($d,$m,$y)=explode("-",$to);
			$to=mktime(23,59,59,$m,$d,$y);
			$sql.=" and orders.time between $from and $to";
		}
		$sql.=" group by orders.transid order by ";
		switch($sort)
		{
			case "customer":
				$sql.="user.name";
				break;
			case "itemname":
				$sql.="item.name";
				break;
			case "brand":
				$sql.="brand.name";
				break;
			case "quantity":
				$sql.="orders.quantity";
				break;
			case "ordertime":
				$sql.="orders.time";
				break;
			case "actiontime":
				$sql.="orders.actiontime";
				break;
			case "status":
				$sql.="orders.status";
				break;
			default :
				$sql.="orders.time";
		}
		switch($order)
		{
			case "a":
				$sql.=" ASC";
				break;
			case "d":
				$sql.=" desc";
				break;
			default:
				$sql.=" desc";
		}
		$sql.=" limit ".(($p-1)*20).",20";
		$q=$this->db->query($sql,$inps);
		
		
		//echo $this->db->last_query();
		
		return $q->result();
	}
	
	function getallbrands()
	{
		$sql="Select * from king_brands order by name asc";
		$q=$this->db->query($sql);
		return $q->result();
	}
	
	function getbrandadminlistfordashboard()
	{
/*		$sql='Select a.name as brandadmin,a.user_id as brandadminid,a.usertype,b.catid,c.name as brandname,d.name
from king_admin as a join 
king_catbrand as b on a.brandid=b.brandid join
king_brands as c on c.id=b.brandid join
king_categories as d on d.id=b.catid and a.usertype=2 limit 5';*/
		$sql='Select a.name as brandadmin,a.user_id as brandadminid,a.usertype,b.id as brandid, b.name as brandname
from king_admin as a join 
king_brands as b on a.brandid=b.id where a.usertype=2 limit 7';
		$sql="select * from king_brands limit 7";
		$q = $this->db->query ( $sql);
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return false;
	}
	
	function getcategorybyname($name)
	{
		$sql="select * from king_categories where name=?";
		$q=$this->db->query($sql,array($name));
		if($q->num_rows()==1)
			return $q->row();
		return false;
	}
	function getcategoriesfordashboard($flag)
	{
		if($flag==0)
		$sql='Select * from king_categories order by id desc limit 7';
		else 
		$sql='Select * from king_categories order by name asc';
		$q = $this->db->query ( $sql);
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return false;
	}
	function getbrandsfordashboard($flag)
	{
		if($flag==0)
		$sql='Select * from king_brands order by id desc limit 7';
		else 
		$sql='Select * from king_brands order by id desc';
		$q = $this->db->query ( $sql);
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return false;
	}
	
	function getbrandslistforspecificcategory($catid)
	{
		$sql='select a.id as catid,a.name as catname,b.brandid,c.name as brandname
				from king_categories as a join 
				king_catbrand as b on b.catid=a.id join
				king_brands as c on c.id=b.brandid where a.id=?';
		$q = $this->db->query ( $sql,$catid);
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return false;
	}
	
	function getsubcatforcat($catid)
	{
		$sql="select * from king_categories where type=?";
		return $this->db->query($sql,array($catid))->result_array();
	}
	
	function getspecificcategorydetails($catid)
	{
		$sql='Select * from king_categories where id=?';
		$q = $this->db->query ( $sql,$catid);
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return false;
	}
	function getspecificcategorydetailsforcatname($catname)
	{
		$sql='Select * from king_categories where name=?';
		$q = $this->db->query ( $sql,$catname);
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return false;
	}
	function getspecificbranddetails($brandid)
	{
		$sql='Select * from king_brands where id=?';
		$q = $this->db->query ( $sql,$brandid);
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return false;
	}
	function getspecificbranddetailsforbrandname($brandname)
	{
		$sql='Select * from king_brands where name=?';
		$q = $this->db->query ( $sql,$brandname);
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return false;
	}

	function genid($len)
	{
		$st="";
		for($i=0;$i<$len;$i++)
			$st.=rand(1,9);
		return $st;
	}
	
	function genitemid()
	{
		$id=$this->genid(12);
		$q=$this->db->query("select 1 from king_dealitems where id=?",array($id));
		while($q->num_rows()!=0)
		{
			$id=$this->genid(8);
			$q=$this->db->query("select 1 from king_dealitems where id=?",array($id));
		}
		return $id;
	}
	
	function gendealid()
	{
		$id=$this->genid(12);
		$q=$this->db->query("select 1 from king_deals where dealid=?",array($id));
		while($q->num_rows()!=0)
		{
			$id=$this->genid(8);
			$q=$this->db->query("select 1 from king_deals where dealid=?",array($id));
		}
		return $id;
	}
	
	function genbrandid()
	{
		$id=$this->genid(8);
		$q=$this->db->query("select 1 from king_brands where id=?",array($id));
		while($q->num_rows()!=0)
		{
		$id=$this->genid(8);
		$q=$this->db->query("select 1 from king_brands where id=?",array($id));
		}
		return $id;
	}
	
	function insertnewbrand($brandid,$name,$description,$logoid,$website,$email,$cids,$userid, $uname, $pwd, $adminemail) {
		//$sql = "insert into king_brands(name,description,logoid,address,website,email) values(?,?,?,?,?,?)";
//		$sql='call insert_newbrandstocat(?,?,?,?,?,?,?,?,?,?,?)';
//		$q = $this->db->query ( $sql, array ($brandname,$branddescription,$logoid,$address,$website,$email,$catid,$userid, $uname, $pwd, $utype));
//		if ($this->db->affected_rows () > 0)
//			return TRUE;
//		else
//			return FALSE;
		
		$sql="insert into king_catbrand(catid,brandid) values";
		$i=0;
		foreach($cids as $cid)
		{
			if($i!=0)
				$sql.=",";
			$sql.="($cid,$brandid)";
			$i++;
		}
		$this->db->query($sql);
		
		$url=preg_replace('/[^a-zA-Z0-9_\-]/','',$name);
		$url=str_replace(" ","-",$url);
		$sql="insert into king_brands(id,name,url,description,logoid,website,email,createdon) values(?,?,?,?,?,?,?,NOW())";
		$q=$this->db->query($sql,array($brandid,$name,$url,$description,$logoid,$website,$email,$userid));

//		$sql="insert into king_admin(user_id,name,password,brandid,usertype,email,createdon) values(?,?,?,?,2,?,NOW())";
//		$q=$this->db->query($sql,array($userid,$uname,$pwd,$brandid,$adminemail));
	}
	
	function insertbrandtocat($brandid, $catid) {
		$sql = "insert into king_catbrand(catid,brandid) values(?,?)";
		$q = $this->db->query ( $sql, array ($catid,$brandid) );
		if ($this->db->affected_rows () > 0)
			return TRUE;
		else
			return FALSE;
	}
	
	function updatecategory($catname,$description,$catid,$type){
		$sql = 'update king_categories set name=?,description=?,type=? where id=?';
		$q = $this->db->query ($sql, array ($catname,$description,$type,$catid));
		if ($this->db->affected_rows () > 0)
			return TRUE;
		else
			return FALSE;
	}
	
	function updatecategorywithlogo($type,$catname,$description,$catpic,$catid){
		$type=0;
		$sql = 'update king_categories set type=?,name=?,description=?,catimage=? where id=?';
		$q = $this->db->query ($sql, array ($type,$catname,$description,$catpic,$catid));
		if ($this->db->affected_rows () > 0)
			return TRUE;
		else
			return FALSE;
	}
	
	function updatebrandwithlogo($brandname,$description,$logoid,$website,$email,$brandid)
	{
		$sql='update king_brands set name=?,description=?,logoid=?,website=?,email=? where id=?';
		$q=$this->db->query($sql,array($brandname,$description,$logoid,$website,$email,$brandid));
		if($this->db->affected_rows()>0)
		return TRUE;
		else
		return FALSE;
	}
	function updatebrand($brandname,$description,$website,$email,$brandid)
	{
		$sql='update king_brands set name=?,description=?,website=?,email=? where id=?';
		$q=$this->db->query($sql,array($brandname,$description,$website,$email,$brandid));
		if($this->db->affected_rows()>0)
		return TRUE;
		else
		return FALSE;
	}
	function getbrandlistnotunderacat($catid){
		$sql='select * from king_brands where id not in(select a.brandid as brandnamame 
from king_catbrand as a join king_brands as b on b.id=a.brandid where catid=?) order by name asc';
		$q=$this->db->query($sql,$catid);
		if ($q->num_rows () > 0) {
			return $q->result ();
		} else
			return false;
	}
	function deletebrandundercat($brandid){
		$sql='delete from king_catbrand where brandid=?';
		$q = $this->db->query ( $sql, $brandid );
		if ($this->db->affected_rows () > 0)
			return TRUE;
		else
			return FALSE;
	}
	function updatebrandadmin($changeuserid,$brandadminnamae,$userid)
	{
		$sql='update king_admin set user_id=?,name=? where user_id=?';
		$q=$this->db->query($sql,array($changeuserid,$brandadminnamae,$userid));
		if($this->db->affected_rows()>0)
		return TRUE;
		else
		return FALSE;
	}
	
	function audit($type,$name,$amount,$desc="",$user="")
	{
		$sql="insert into king_audit(name,credit,debit,time,description,user) values(?,?,?,?,?,?)";
		$inp=array($name,0,0,time(),$desc,$user);
		if($type==0)
			$inp[2]=$amount;
		else 
			$inp[1]=$amount;
		$this->db->query($sql,$inp);
	}
	
	function getcouponrefs()
	{
		$rs=$this->db->query("select distinct remarks from king_coupons order by remarks asc")->result_array();
		$ret=array();
		foreach($rs as $r)
			$ret[]=$r['remarks'];
		return $ret;
	}
	
	function getrecentcoupons()
	{
		return $this->db->query("select * from king_coupons order by id desc limit 20")->result_array();
	}
	
	function searchcoupons($ref,$status,$type,$mode,$unlimited)
	{
		$sql="";
		if($ref!="any")
			$sql.=" and remarks like ?";
		if($status!=0)
		{
					if($status-1==0)
					$sql.=" and used=".($status-1);
					else 
					$sql.=" and used>=".($status-1);
		}
		if($type!=0)
			$sql.=" and type=".($status-1);
		if($mode!=0)
			$sql.=" and status=".($mode-1);
		if($unlimited!=0)
			$sql.=" and status=".($unlimited-1);
		$sub=$sql;
			
		$sql="select count(1) as l from king_coupons where 1".$sub;
		$ret[0]=$this->db->query($sql,$ref)->row()->l;
		
		$sql="select * from king_coupons where 1 $sub limit 400";
		$ret[1]=$this->db->query($sql,$ref)->result_array();

		return $ret;
	}
	
	function createnewcashback()
	{
		foreach(array("cashback","min","coupons","c_valid","c_min","starts_h","starts_m","starts_d","expires_h","expires_m","expires_d") as $i)
			$$i=$this->input->post($i);
		@list($d,$m,$y)=explode("-",$starts_d);
		$starts=@mktime($starts_h,$starts_m,59,$m,$d,$y);
		@list($d,$m,$y)=explode("-",$expires_d);
		$expires=@mktime($expires_h,$expires_m,59,$m,$d,$y);
		if($expires<=$starts)
			show_error("Time always flow in clockwise direction");
		$inp=array($cashback,$starts,$expires,1,$min,$c_valid,$c_min,$coupons);
		$this->db->query("insert into king_cashback_campaigns(cashback,starts,expires,status,min_trans_amount,coupon_valid,coupon_min_order,coupons_num) 
															values(?,?,?,?,?,?,?,?)",$inp);
		redirect("admin/cashback_campaigns");
	}
	
	function cashbackdef($dealid)
	{
		$itemid=$this->db->query("select id from king_dealitems where dealid=?",$dealid)->row()->id;
		foreach(array("value","min","valid") as $inp)
			$$inp=$this->input->post($inp);
		$total=0;
		$this->db->query("delete from king_product_cashbacks where itemid=?",$itemid);
		foreach($value as $i=>$v)
		{
			if(empty($v))
				continue;
			$total+=$v;
			$this->db->query("insert into king_product_cashbacks(itemid,value,valid,min_order) values(?,?,?,?)",array($itemid,$v,$valid[$i],$min[$i]));
		}
		$this->db->query("update king_dealitems set cashback=? where dealid=?",array($total,$dealid));
		redirect("admin/deal/$dealid");
	}
	
	function proccreatecoupon()
	{
		foreach(array("num","many","code","type","autogen","value","mode","expires","min","brandcats","brands","cats","remarks","unlimited","gift") as $inp)
			$$inp=$this->input->post($inp);
		$coupons=array();
		if($many==0)
		{
			if($autogen)
				$coupon="ST".strtoupper(randomChars(8));
			else
			{
				if(empty($code))
					show_error("coupon code missing");
				$coupon=$code;
			}
			if($this->db->query("select 1 from king_coupons where code=?",$coupon['name'])->num_rows()!=0)
				show_error("Coupon code already exists {$coupon}");
			$coupons[]=$coupon;
		}
		else
		for($i=0;$i<$num;$i++)
			$coupons[]="ST".strtoupper(randomChars(8));
			
		$cbrands="";
		$ccats="";
		
		list($d,$m,$y)=explode("-",$expires);
		
		$cexpires=mktime(23,59,59,$m,$d,$y);
		
		
		if($brandcats=="brands")
		{
			if(empty($brands))
				show_error("there were no brands selected for brand based coupon");
			$cbrands=implode(",",$brands);
		}
		if($brandcats=="cats")
		{
			if(empty($cats))
				show_error("there were no categories selected for category based coupon");
			$ccats=implode(",",$cats);
		}

		if(empty($coupons))
			show_error("No coupons to create");
		
		foreach($coupons as $c)
		{
			$inp=array($c,$type,$value,$cbrands,$ccats,$mode,$min,$unlimited,time(),$cexpires,$remarks,$gift);
			$this->db->query("insert into king_coupons(code,type,value,brandid,catid,mode,status,min,unlimited,created,expires,remarks,gift_voucher)
															values(?,?,?,?,?,?,0,?,?,?,?,?,?)",$inp);
		}
		
		$inp=array($c,$type,$value,$mode,$cexpires,$min,$unlimited,time());
		$this->db->query("insert into king_coupon_activity(code,type,value,mode,expires,min,unlimited,time) values(?,?,?,?,?,?,?,?)",$inp);
		
		if(count($coupons)==1)
			redirect("admin/coupon/{$coupons[0]}");
		redirect("admin/coupons");
	}
	
}
?>