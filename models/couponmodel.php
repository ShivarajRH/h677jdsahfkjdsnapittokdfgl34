<?php

class Couponmodel extends Model{
	
	function Couponmodel()
	{
		parent::Model();
	}
	
	function logincheck($user,$pass)
	{
		$sql="select userid,password,usertype,username from cou_admin where username=? and status=0";
		$q=$this->db->query($sql,array($user));
		if($q->num_rows()==1)
		{
			$r=$q->row_array();
			if($r['password']==md5($pass))
			{
				$r['name']=$r['username'];
				unset($r['username']);
				return $r;
			}
		}
		return false;
	}
	
	function getretailersfordist($dis)
	{
		$sql="select admin.username,admind.name,admind.city,admin.mobile from cou_admin as admin join cou_admin_details as admind on admind.userid=admin.userid where admin.usertype=3 and admin.created_by=? order by admin.created_date desc";
		return $this->db->query($sql,array($dis))->result_array();
	}
	
	function getskusfordistributor($dis)
	{
		$sql="select concat(sku1,sku2) as sku from cou_coupon_details where distributor=? and retailer=0 and user=0";
		return $this->db->query($sql,array($dis))->result_array();
	}
	
	function getdenominations()
	{
		$sql="select id,value from cou_denominations order by value asc";
		return $this->db->query($sql)->result_array();
	}
	
	function getdenominationlist()
	{
		$sql="select deno.id,deno.value,adm.username from cou_denominations as deno join cou_admin as adm on adm.userid=deno.created_by order by deno.value asc";
		return $this->db->query($sql)->result_array();
	}
	
	function adddenomination($value,$userid)
	{
		$sql="insert into cou_denominations(value,status,created_by) values(?,1,?)";
		$this->db->query($sql,array($value,$userid));
	}
	
	private function nextsku($sku)
	{
		$sku1=$sku[0];
		$sku2=$sku[1];
		$sku2++;
		if($sku2>99999)
		{
			$sku1=incr_char($sku1);
			$sku2="10001";
		}
		return array($sku1,$sku2);
	}
	
	function generatecoupon($num,$deno,$valid,$distributor)
	{
		$sql="select * from cou_coupon_details order by created_date desc";
		$q=$this->db->query($sql);
		if($q->num_rows()==0)
			$sku=array("AAA","10000");
		else
		{
			$r=$q->row_array();
			$sku=array($r['sku1'],$r['sku2']);
		}
		
		$cids=array();
		$q=array();
		for($i=0;$i<$num;$i++)
		{
			$q[]="?";
			$cids[]=random_alpha(4).random_num(12);
		}
		
		$sql="select COUNT(1) as len from cou_coupon_details where coupon=".implode(" or coupon=",$q);
		
		while($this->db->query($sql,$cids)->row()->len!=0)
		{
			$cids=array();
			for($i=0;$i<$num;$i++)
				$cids[]=random_alpha(4).random_num(12);
		}
		$ar=array();
		$sql="insert into cou_coupon(coupon,status,denomination,valid_upto,used_on) values";
		foreach($cids as $i=>$cid)
		{
			if($i!=0)
				$sql.=",";
			$sql.="(?,0,?,?,0)";
			$ar[]=$cid;
			$ar[]=$deno;
			$ar[]=$valid;
		}
		$this->db->query($sql,$ar);
		$ar=array();
		$sql="insert into cou_coupon_details(coupon,sku1,sku2,distributor,retailer,user,created_date) values";
		foreach($cids as $i=>$cid)
		{
			$sku=$this->nextsku($sku);
			if($i!=0)
				$sql.=",";
			else
				$ret[0]=$sku[0].$sku[1];
			$sql.="(?,?,?,?,0,0,?)";
			$ar[]=$cid;
			$ar[]=$sku[0];
			$ar[]=$sku[1];
			$ar[]=$distributor;
			$ar[]=time();
		}
		$this->db->query($sql,$ar);
		$ret[1]=$sku[0].$sku[1];
		$sql="insert into cou_coupon_history(num,start,end,distributor,time) values(?,?,?,?,?)";
		$this->db->query($sql,array($num,$ret[0],$ret[1],$distributor,time()));

		return $ret;
	}
	
	function getalldistributors()
	{
		$sql="select admin.userid,admind.name,admind.city from cou_admin as admin join cou_admin_details as admind on admind.userid=admin.userid where admin.usertype=2 order by admind.name asc";
		return $this->db->query($sql)->result_array();
	}
	
	function getallcouponhistory()
	{
		$sql="select ch.num,ch.start,ch.end,admind.name,ch.time,admind.city from cou_coupon_history as ch join cou_admin_details as admind on admind.userid=ch.distributor order by ch.time desc";
		return $this->db->query($sql)->result_array();
	}
	
	function getcouponhistory($len)
	{
		$sql="select ch.num,ch.start,ch.end,admind.name,ch.time,admind.city from cou_coupon_history as ch join cou_admin_details as admind on admind.userid=ch.distributor order by ch.time desc limit $len";
		return $this->db->query($sql)->result_array();
	}
	
	function getdistributors()
	{
		$sql="select admin.username,admind.name,admind.city,admin.mobile from cou_admin as admin join cou_admin_details as admind on admind.userid=admin.userid where admin.usertype=2 order by admin.created_date desc";
		return $this->db->query($sql)->result_array();
	}
	
	function createdistri($username,$name,$mobile,$email,$address,$area,$city,$pincode,$telephone)
	{
		$sql="insert into cou_admin(usertype,username,password,mobile,created_by,created_date,status) values(2,?,?,?,?,?,0)";
		$this->db->query($sql,array($username,md5("password"),$mobile,1,time()));
		$id=$this->db->query("select MAX(userid) as id from cou_admin")->row()->id;
		$sql="insert into cou_admin_details(userid,name,email,address,area,city,pincode,telephone) values(?,?,?,?,?,?,?,?)";
		$this->db->query($sql,array($id,$name,$email,$address,$area,$city,$pincode,$telephone));
	}
	
	function getcoupons($start,$end)
	{
		$sql="select cd.created_date,co.coupon,co.status,d.value,co.valid_upto as valid,cd.sku1,cd.sku2 from cou_coupon as co join cou_coupon_details as cd on cd.sku1 between ? and ? and cd.sku2 between ? and ? join cou_denominations as d on d.id=co.denomination where co.coupon=cd.coupon order by Concat(cd.sku1,cd.sku2) asc";
		return $this->db->query($sql,array(substr($start,0,3),substr($end,0,3),substr($start,3),substr($end,3)))->result_array();
	}
	
}