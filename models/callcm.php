<?php
/*
 * Created on Jun 21, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

 
 class Callcm extends Model{
 	
 	function login()
 	{
 		$user=$this->input->post("username");
 		$password=md5($this->input->post("password"));
 		$user=$this->db->query("select * from king_callcenter where username=? and password=?",array($user,$password))->row_array();
 		if(empty($user))
 			return false;
 		unset($user['password']);
 		$this->session->set_userdata("callc_user",$user);
 		return true;
 	}
 	
 	function getrecenttrans()
 	{
 		return $this->db->query("select * from king_transactions order by id desc limit 35")->result_array();
 	}
 	
 	function gettrans($transid)
 	{
 		return $this->db->query("select * from king_transactions where transid=?",$transid)->row_array();
 	}
 	
 	function getpendingorders($transid)
 	{
 		return $this->db->query("select o.*,i.name as item from king_tmp_orders o join king_dealitems i on i.id=o.itemid where o.transid=?",$transid)->result_array();
 	}
 	
 	function getorders($transid)
 	{
 		return $this->db->query("select o.*,i.name as item from king_orders o join king_dealitems i on i.id=o.itemid where o.transid=?",$transid)->result_array();
 	}
 	
 }