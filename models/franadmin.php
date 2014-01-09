<?php

class Franadmin extends Model{
	
	function login($username,$password)
	{
		$password=md5($password);
		$user=$this->db->query("select userid,id,username,name from king_franchisee where username=? and password=?",array($username,$password))->row_array();
		if(isset($user['username']))
		{
			$this->session->set_userdata("fran_auser",$user);
			return true;
		}
		return false;
	}
	
	function gettransactions($id)
	{
		$sql="select * from king_franch_transactions where franid=? order by time desc";
		return $this->db->query($sql,$id)->result_array();
	}
	
	function getfranchisee($id)
	{
		return $this->db->query("select * from king_franchisee where id=?",$id)->row_array();
	}
	
}