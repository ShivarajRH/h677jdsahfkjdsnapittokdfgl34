<?php
class franchise_model extends Model
{
	function __construct()
	{
		parent::Model();
		$this->load->model('resource_model');
	}

	/**
	 * function to get franchise details.
	 * @param MIXED $q
	 */
	function get_franchise_details($q)
	{
		return $this->db->query('select * from pnh_m_franchise_info where franchise_id=? or login_mobile1 = ? or login_mobile2 = ? ',array($q,$q,$q))->row_array();
	}

	/**
	 * Function to return franchise menu details
	 * @param type $fran_id int
	 * @return string array
	 * @author Shivaraj
	 */
	function fran_menu_type($fran_id)
	{
		$is_fran_type_electronic = $this->resource_model->_get_config_param("FRAN_TYPE_ELECTRONIC");
		$arr_frn_menus_res = $this->db->query("SELECT m.id,m.name AS menu,find_in_set(m.id,?) as status FROM `pnh_franchise_menu_link`a JOIN pnh_m_franchise_info b ON b.franchise_id=a.fid JOIN pnh_menu m ON m.id=a.menuid WHERE a.status=1 AND b.franchise_id=? ORDER BY status DESC",array($is_fran_type_electronic,$fran_id));

		if($arr_frn_menus_res->num_rows() > 0 )
		{
			$arr_frn_menus = $arr_frn_menus_res->result_array();

			// check if status is set
			if($arr_frn_menus[0]["status"])
			{
				$data =  array('status'=>"success","menus"=>$arr_frn_menus,"menu_type"=>'electonics',"menu_msg"=>"Only electronic items alloted");
			}
			else
			{
				$data =  array('status'=>"success","menus"=>$arr_frn_menus,"menu_type"=>'beauty',"menu_msg"=>"Beauty products");
			}
		}
		else
		{
			$data =  array('status'=>"error","menu_type"=>0,"menu_msg"=>"No menus");
		}
		return $data;
	}
}