<?php

class Sociomodel extends Model{
	
	function get_fb_friends($userid)
	{
		$flist=$this->db->query("select friends from king_fb_friends where userid=?",$userid)->row_array();
		if(empty($flist))
			return array();
		$flist=$flist['friends'];
		if(empty($flist))
			return array();
		$friends=$this->db->query("select fbid as id,name,username,status from king_facebookers where fbid in (".$flist.")")->result_array();
		return $friends;
	}
	
	function do_fb_friends($uid)
	{
		$on=$this->db->query("select update_on from king_fb_friends where userid=?",$uid)->row_array();
		if(empty($on))
			return true;
		if($on['update_on']+(3*24*60*60)<time())
			return false;
		return true;
	}
	
}