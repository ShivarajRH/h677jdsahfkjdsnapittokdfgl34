<?php
class Member_model extends Model
{
	function Member_model()
	{
		parent::Model();
	}

	/**
	 * function to get member det by registered mobile no
	 */
	function check_valid_membermobile($mob)
	{
		return $this->db->query('select count(*) as t from pnh_member_info where mobile = ? ',$mob)->row()->t;
	}

	/**
	 * function to get member det by registered mobile no
	 */
	function get_memberbymob($mob)
	{
		if($this->check_valid_membermobile($mob))
			return $this->db->query('select * from pnh_member_info where mobile = ? ',$mob)->row_array();

		return false;
	}

}