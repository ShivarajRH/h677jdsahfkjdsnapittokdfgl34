<?php
class Member_model extends Model
{
	function Member_model()
	{
		parent::Model();
		$this->load->model('resource_model');
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

	/**
	 * function to check if member is already registered
	 * @param unknown_type $m
	 * @return boolean
	 */
	function is_mem_reg($m)
	{
		$m_res = $this->db->query("select pnh_member_id from pnh_member_info where (pnh_member_id = ? or mobile = ?) ",array($m,$m));
		if($m_res->num_rows())
			return $m_res->row()->pnh_member_id;
		return false;
	}
	
	
	/**
	 * fucntion to get total orders placed by member
	 * @param unknown_type $mid
	 */
	function is_new_member($mid)
	{
		return ($this->db->query("select count(distinct transid) as t from king_orders a join pnh_member_info b on a.userid = b.user_id where b.pnh_member_id = ? and a.status != 3 ",array($mid))->row()->t?0:1);
	}
	
	/**
	 * fucntion to get total orders placed by member
	 * @param unknown_type $mid
	 */
	function get_memberorders_ttl($mid)
	{
		return $this->db->query("select count(distinct transid) as t from king_orders a join pnh_member_info b on a.userid = b.user_id where b.pnh_member_id = ? ",array($mid))->row()->t;
	}
	
	/**
	 * function to get member details by mobileno or id
	 * @param unknown_type $m
	 * @return mixed
	 */
	function get_member_details($m)
	{
		$m_res = $this->db->query("select * from pnh_member_info where (pnh_member_id = ? or mobile = ?) ",array($m,$m));
		if($m_res->num_rows())
			return $m_res->row_array();
		return false;
	}
	
	/**
	 * function to generate new member id for dynamic allocation
	 * @return number
	 */
	function _gen_uniquememberid()
	{
		$lastmem_id = $this->resource_model->_get_config_param('LAST_MEMBERID_ALLOTED');
		$member_id = $lastmem_id+1;
		$this->resource_model->_set_config_param('LAST_MEMBERID_ALLOTED',$member_id);
		// check if member id is already alloted
		if($this->db->query("select count(*) as t from pnh_member_info where pnh_member_id = ? ",$member_id)->row()->t)
			return $this->_gen_uniquememberid();
		else
			return $member_id;
	}
	
	/**
	 * function to registere new member
	 * @param unknown_type $fid
	 * @param unknown_type $member_det
	 * @return Ambigous <string, unknown>
	 */
	function register_memberbydet($fid,$member_det,$created_by=0)
	{
		$mem_name=$member_det['name'];
		$mem_mobno=$member_det['mobno'];
		$dob= isset($member_det['dob'])?$member_det['dob']:'';
		$address=isset($member_det['address'])?$member_det['address']:'';
		$gender=isset($member_det['gender'])?$member_det['gender']:'';
		$marital=isset($member_det['marital'])?$member_det['marital']:'';
		$pincode=isset($member_det['pincode'])?$member_det['pincode']:'';
		
		$fran_det = $this->franchise_model->get_franchise_details($fid);
		
		if(!$this->is_mem_reg($mem_mobno))
		{
			$membr_id=$this->_gen_uniquememberid();
			
			$this->db->query("insert into king_users(name,is_pnh,createdon) values(?,1,?)",array("PNH Member: $membr_id",time()));
			$userid=$this->db->insert_id();
			
			$this->db->query("insert into pnh_member_info(pnh_member_id,user_id,first_name,last_name,mobile,gender,dob,address,pincode,marital_status,franchise_id,created_by,created_on)values(?,?,?,?,?,?,?,?,?,?,?,?,?)",array($membr_id,$userid,$mem_name,'',$mem_mobno,$gender,$dob,$address,$pincode,$marital,$fid,$created_by,time()));
			
			// ============< New Member Register SMS To Member >===================
			$fran_type = $this->franchise_model->fran_menu_type($fid);
			if($fran_type['menu_type'] == 'electonics')
				$mem_msg = "Hi $mem_name, Welcome to StoreKing - Hurry up!! Get Free Insurance on the 1st Electronic* product you buy. Your Member ID is $membr_id Offer Valid only after Registration fee Of Rs ".PNH_MEMBER_FEE."/- is paid to Storeking Franchisee.";
			else
				$mem_msg = "Hi $mem_name,Welcome to StoreKing - Hurry up!! Get Free Talk Time worth Rs.".PNH_MEMBER_FREE_RECHARGE." on your 1st purchase above Rs ".MEM_MIN_ORDER_VAL.".Don't forget your Member ID is $membr_id.Offer Valid only after Registration fee Of Rs ".PNH_MEMBER_FEE."/- is paid to Storeking Franchisee.";
			
			$this->resource_model->sendsms($mem_mobno,$mem_msg,$fid,$membr_id,'MEM_REG');
			
			
			// ============< New Member Register SMS To Franchise >===================
			$fran_msg= "Hello ".$fran_det['franchise_name'].", Congrats!! [$mem_name $mem_mobno] has been Registered Successfully and has been assigned Member ID: $membr_id. Please make sure Registration fee of Rs ".PNH_MEMBER_FEE."/- has been collected.";
			$this->resource_model->sendsms($fran_det['login_mobile1'],$fran_msg,$fid,0,'MEM_REG');
		
			$output['status'] = 'success';
			$output['mid'] = $membr_id;
		}else
		{
			$output['status'] = 'error';
			$output['error'] = 'Mobile no already registered';
		}
		
		return $output;
	}
	
}