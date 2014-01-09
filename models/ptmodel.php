<?php

class Ptmodel extends Model{
	
	protected $pdb;
	
	function __construct()
	{
		parent::Model();
		$this->pdb=$this->load->database("viadb",true);
	}
	
	
	function getuser($fid)
	{
		$sql="select * from fb_users where fid=?";
		return $this->pdb->query($sql,array($fid))->row_array();
	}
	
	function createuser($fid,$gender,$rfid,$friends,$email)
	{
		$sql="insert into fb_users(fid,gender,invite_id,lastlogin,referred_by,friends,email) values(?,?,?,?,?,?,?)";
		$this->pdb->query($sql,array($fid,$gender,random_string("unique"),time(),$rfid,$friends,$email));
	}
	
	function updateuser($fid,$friends,$email)
	{
		$sql="update fb_users set friends=?, lastlogin=?, email=? where fid=?";
		$this->pdb->query($sql,array($friends,time(),$email,$fid));
	}
	
	function getptsys()
	{
		$sql="select * from fb_loyalty_pts_system where status=1";
		return $this->pdb->query($sql)->row_array();
	}
	
	function friend_share_incr($fid,$n)
	{
		$sql="update fb_users set friend_shares=friend_shares+?,total_friend_shares=total_friend_shares+? where fid=? limit 1";
		$this->pdb->query($sql,array($n,$n,$fid));
	}
	
	function referral_incr($fid,$n=1)
	{
		$sql="update fb_users set referrals=referrals+?,total_referrals=total_referrals+? where fid=? limit 1";
		$this->pdb->query($sql,array($n,$n,$fid));
	}
	
	function getuserbyinviteid($inv)
	{
		$sql="select * from fb_users where invite_id=?";
		return $this->pdb->query($sql,array($inv))->row_array();
	}
	
	function share_incr($fid,$n)
	{
		$sql="update fb_users set shares=shares+?,total_shares=total_shares+? where fid=? limit 1";
		$this->pdb->query($sql,array($n,$n,$fid));
	}
	
	function invite_incr($fid,$n)
	{
		$this->pdb->query("update fb_users set invites=invites+?, total_invites=total_invites+? where fid=? limit 1",array($n,$n,$fid));
	}
	
	function gettransaction($sid,$tid)
	{
		$sql="select * from fb_transacts where trans_id=? and trip_id=? and used=0 limit 1";
		return $this->pdb->query()->row_array();
	}
	
	function updatetransaction($uid,$sid,$tid)
	{
		$sql="update fb_transacts set fid=?, used=1 where trans_id=? and trip_id=? limit 1";
		$this->pdb->query($sql,array($uid,$sid,$tid));
	}
	
	function rupees_incr($uid,$n)
	{
		$this->pdb->query("update fb_users set rupees=rupees+?, total_rupees=total_rupees+? where fid=? limit 1",array($n,$n,$uid));
	}
	
	function calcpts($fid)
	{
		$user=$this->pdb->query("select * from fb_users where fid=?",array($fid))->row_array();
		$ptsys=$this->getptsys();
		
		$opts=$pts=$user['loyalty_pts'];
		$otpts=$tpts=$user['total_loyalty_pts'];
		
		if($user['referrals']>$ptsys['referrals'])
		{
			$pt=floor($user['referrals']/$ptsys['referrals']);
			$user['referrals']-=$pt*$ptsys['referrals'];
			$pts+=$pt;
			$tpts+=$pt;
		}
		if($user['invites']>$ptsys['invites'])
		{
			$pt=floor($user['invites']/$ptsys['invites']);
			$user['invites']-=$pt*$ptsys['invites'];
			$pts+=$pt;
			$tpts+=$pt;
		}
			if($user['shares']>$ptsys['shares'])
		{
			$pt=floor($user['shares']/$ptsys['shares']);
			$user['shares']-=$pt*$ptsys['shares'];
			$pts+=$pt;
			$tpts+=$pt;
		}
			if($user['friend_shares']>$ptsys['friend_shares'])
		{
			$pt=floor($user['friend_shares']/$ptsys['friend_shares']);
			$user['friend_shares']-=$pt*$ptsys['friend_shares'];
			$pts+=$pt;
			$tpts+=$pt;
		}
			if($user['rupees']>$ptsys['rupees'])
		{
			$pt=floor($user['rupees']/$ptsys['rupees']);
			$user['rupees']-=$pt*$ptsys['rupees'];
			$pts+=$pt;
			$tpts+=$pt;
		}
		if($opts!=$pts)
		{
			$sql="update fb_users set friend_shares=?, shares=?, invites=?, rupees=?,referrals=?,loyalty_pts=?,total_loyalty_pts=? where fid=?";
			$this->pdb->query($sql,array($user['friend_shares'],$user['shares'],$user['invites'],$user['rupees'],$user['referrals'],$pts,$tpts,$fid));
			$sess=$this->session->userdata("fb_user");
			$sess['points']=$pts;
			$this->session->set_userdata("fb_user",$sess);
		}
	}
	
}