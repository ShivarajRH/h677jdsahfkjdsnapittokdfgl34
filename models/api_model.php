<?php
/*ini_set('memory_limit','1024M');
ini_set('max_exection_time','3600');*/
ini_set('display_errors',1);
error_reporting(E_ALL); 
/**
 * Api Model Class
 * @author Madhan
 *
 */
class Api_model extends Model
{
	/**
	 * Default Constructor 
	 */
	function Api_model()
	{
		parent::Model();
	}
	
	
	/**
	 * function to check if login details avaiable  
	 */
	function is_valid_login($username,$password)
	{
		$userdet_res = $this->db->query("SELECT * FROM pnh_users u
										LEFT JOIN pnh_m_franchise_info f ON f.franchise_id = u.reference_id
										WHERE (u.username = ? OR f.login_mobile1=? ) AND u.password = MD5(?)
									",array($username,$username,$password));
		// query to table to check if user exists 
		if($userdet_res->num_rows())
			return $userdet_res->row_array();
		return false;
	}
	
	/**
	 * function to get user details by input type [name OR id]
	 * @param String $inp 
	 * @param String $by [name,id]
	 * @return boolean,Array
	 */
	function get_userdet($inp='',$by='id')
	{
		if($by == 'name')
			$cond = ' and au.username = ? ';
		else if($by == 'id')
			$cond = ' and au.user_id = ? ';
		
		$userdet_res = $this->db->query("select 
												user_id,username,type
											from pnh_users au 
											where 1 
										".$cond,array($inp));
		if($userdet_res->num_rows())
			return $userdet_res->row_array();
		else
		{
			$frandet_res = $this->db->query("SELECT franchise_id,login_mobile1 FROM pnh_m_franchise_info f WHERE 1 AND login_mobile1=? ",array($inp) );
			if($frandet_res->num_rows()==0)
			{
				return false;
			}
			else
			{
				$frandet = $frandet_res->row_array();
				$mobile_no = $frandet['login_mobile1'];
				$in_arr=array('type'=>0,"reference_id"=>$frandet['franchise_id'],"username"=>$mobile_no,"password"=>'',"is_logged_in"=>0,"created_by"=>68,"created_on"=>cur_datetime() );
				$this->db->insert("pnh_users",$in_arr);
				$user_id = $this->db->insert_id();
				
				$resp = $this->update_password($user_id);
				if($resp['status']==true)
				{
					output_data($resp['msg']);
				}
				else
				{
					output_error( array('error_code'=>2000,'error_msg'=>$resp['msg']) );
				}
			}
		}
	}

	/**
	 * function to get user auth key by userid 
	 * @param unknown_type $userid
	 */
	function get_authkey($userid,$auth_key)
	{
		$authkey_res = $this->db->query('select auth_key from pnh_user_auth where (user_id = ? or auth_key = ? ) and unix_timestamp(expired_on) > unix_timestamp() ',array($userid,$auth_key));
		
		if($authkey_res->num_rows())
			return $authkey_res->row()->auth_key;
		return false;
	}
	
	/**
	 * function to generate access key for user 
	 * @param unknown_type $userid
	 */
	function gen_authkey($userid)
	{
		$authkey = $this->get_authkey($userid,'XXXXXX');
		if($authkey)
			return $authkey;
		
		// generate new authkey 
		$inp = array();
		$inp['user_id'] = $userid;
		$inp['auth_key'] = $authkey = md5(rand(1000, 1000000));
		$inp['expired_on'] = date('Y-m-d H:i:s',time()+(24*60*60) ); //1 day, half an hour=strtotime("+30 minutes")
		$inp['created_by'] = 0;
		$inp['created_on'] = date('Y-m-d H:i:s');
		$this->db->insert('pnh_user_auth',$inp);

		return $authkey;
	}
	
	/**
	 * function for get the erp api login user details
	 * @return number
	 */
	function get_api_user()
	{
		$api_userdet = $this->db->query("select * from king_admin where username = 'Franchisee/API' ");
		if($api_userdet->num_rows())
			return array("userid"=>$api_userdet->row()->id);
		else
			die("Api USER NOT FOUND");
	}
	
	/**
	 * function for get the franchise basic details
	 * @param unknown_type $user_id
	 * last_modified by suresh<suresh.n@storeking.in>
	 */
	function get_franchise($fid)
	{
		$cond='';
		
		//if($town_id)
		//	$cond.=' a.franchise_id='.$fid.' and t.id='.$town_id.'';
		//else
		$cond.=' a.franchise_id='.$fid.'';

		$sql="select a.franchise_id,a.pnh_franchise_id,a.franchise_name,a.address,a.locality,a.city,a.postcode,a.state,a.credit_limit,
				t.id as town_id,t.town_name,tt.territory_name,a.login_mobile1,is_prepaid,a.franchise_type,assigned_rmfid
					from pnh_m_franchise_info a 
					left join pnh_users b on b.reference_id=a.franchise_id
					join pnh_towns as t on t.id=a.town_id
					join pnh_m_territory_info tt on tt.id=a.territory_id
					where $cond";
		
		$franchise_det_res=$this->db->query($sql);
		
		if($franchise_det_res->num_rows())
		{
			return $franchise_det_res->row_array();	
		
		}else{
			return false;
		}
	}
	
	/**
	 * function for get the franchise cart details
	 * @param unknown_type $franchise_id
	 */
	function get_franchise_cart_info($franchise_id)
	{
		$cart_ttl=$this->db->query("select count(*) as ttl_items from pnh_api_franchise_cart_info where franchise_id=? and status=1",array($franchise_id))->row()->ttl_items;	
		
		return $cart_ttl;
	}
	
	/**
	 * function for get  menu list
	 */
	function get_menulist()
	{
		$sql="SELECT id,name as menu from pnh_menu order by name asc";
	
		$menu_res=$this->db->query($sql);
	
		if($menu_res->num_rows())
			return $menu_res->result_array();
		else
			return false;
	}
	
	/**
	 * function for get the franchise menu list
	 */
	function get_menus_by_franchise($fid)
	{
		$sql="SELECT m.id,m.name AS menu FROM `pnh_franchise_menu_link`a
					JOIN pnh_m_franchise_info b ON b.franchise_id=a.fid
					join pnh_users c on c.reference_id=b.franchise_id
					JOIN pnh_menu m ON m.id=a.menuid
					WHERE a.status=1 AND b.franchise_id=? and m.id not in(124,125) and a.status=1";
	
		$menu_res=$this->db->query($sql,$fid);
	
		if($menu_res->num_rows())
			return $menu_res->result_array();
		else
			return false;
	}
	
	/**
	 * function for get  menu list
	 */
	function get_menus($franchise_id)
	{
		$sql="SELECT id,name as menu from pnh_menu order by name asc";
	
		$menu_res=$this->db->query($sql);
	
		if($menu_res->num_rows())
			return $menu_res->result_array();
		else
			return false;
	}
	
	/**
	 * function for get the categories list
	 */
	function get_categories_by_menuid($menuid)
	{
		$sql="SELECT b.id,b.name AS menu,COUNT(o.quantity) AS sold_qty FROM king_deals a  JOIN king_dealitems i ON i.dealid=a.dealid JOIN king_categories b ON b.id=a.catid LEFT JOIN king_orders o ON o.itemid=i.id WHERE a.menuid=? GROUP BY b.name ORDER BY sold_qty DESC";
	
		$cat_res=$this->db->query($sql,$menuid);
	
		if($cat_res->num_rows())
			return $cat_res->result_array();
		else
			return false;
	}
	
	/**
	 * function for get the brands by menu id
	 */
	function get_brands_by_menu($fid,$menu_id,$pg,$brandslimit,$alpha_sort,$top_brands)
	{
		$menu_details=array();
		$param=array();
		$cond='';
		$param[]=$menu_id;

		if($alpha_sort)
		{
			$cond=' and b.name like ? ';
			$param[]=$alpha_sort.'%';
		}
		
		$join_cond=''; $order_by='';

		if($fid!=0)
		{
			$is_menu_linked=$this->apim->check_if_menu_islinked_tofran($fid,$menu_id,0,0,0);
		
			if(!$is_menu_linked)
				return false;
			else 
			{
				if($top_brands)
				{
				
					$join_cond="join king_dealitems i on i.dealid=a.dealid 
								join king_orders o on o.itemid=i.id 
								join king_transactions t on t.transid=o.transid and t.is_pnh=1 
							";
					$order_by=	"order by count(o.id) desc ";
					
					$cond.="and franchise_id=".$fid;
					
					$limit=5;
				}
				
			}
		
		}
		
		$sql="select a.brandid,b.name as brandname from king_deals a 
					join king_brands b on b.id=a.brandid
					$join_cond
					where a.menuid=? and a.publish=1  $cond
					group by a.brandid
					$order_by"; 
		
		$menu_res=$this->db->query($sql,$param);

		if($menu_res->num_rows())
		{
			$menu_details['ttl_brands']=$menu_res->num_rows();
			
			if(!$top_brands)
				$sql.=" limit $pg,$brandslimit";
			else 
				$sql.=" limit $pg,$limit";
			
			$menu_details['brand_list']=$this->db->query($sql,$param)->result_array();
			
			
			return $menu_details;
		}
		else
			return false;
	}
	
	/**
	 * function get brand list by category
	 */
	function get_brands_by_cat($catid,$start,$limit,$alpha_sort,$top_brands)
	{
		$brand_det=array();
		$param=array();
		$cond='';
		$param[]=$catid;
		
		if($alpha_sort)
		{
			$cond=' and b.name like ? ';
			$param[]=$alpha_sort.'%';
		}
		
		$join_cond='';$order_by='';
		
		if($top_brands)
		{
			$join_cond="join king_orders o on o.itemid=c.id 
						join king_transactions t on t.transid=o.transid
					";
			$order_by=	"order by count(o.id) desc ";
			
			$limit=5;
		}
	
		$sql="select a.brandid,b.name as brandname from king_deals a 
					join king_brands b on b.id=a.brandid
					join king_dealitems c on c.dealid=a.dealid
					$join_cond
				where a.catid=? and a.publish=1 and c.is_pnh=1 $cond
				group by a.brandid
				$order_by 
				";
		
		$cat_res=$this->db->query($sql,$param);
		
		if($cat_res->num_rows())
		{
			$brand_det['total_brands']=$cat_res->num_rows();
			
			if(!$top_brands)
				$sql.=" limit $start,$limit";
			else 
				$sql.=" limit $start,$limit";
			
			$brand_det['brand_list']=$this->db->query($sql,$param)->result_array();
			
			return $brand_det;
		}else{
			return false;
		}
	}
	
	/**
	 * function get the brands list
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 */
	function get_brands($start,$limit,$alpha_sort,$top_brands)
	{
		$brand_det=array();
		
		$param=array();
		$cond='';
		$join_cond ='';
		$order_by='';
		
		if($alpha_sort)
		{
			$cond=' and name like ? ';
			$param[]=$alpha_sort.'%';
		}
		if($top_brands)
		{
			$join_cond="join king_deals d on d.brandid=a.id
						join king_dealitems c c.id=on d.dealid
						join king_orders o on o.itemid=c.id
						join king_transactions t on t.transid=o.transid
							";
			$order_by =	"order by sum(o.quantity) desc ";
		}
		$sql="select a.id as brandid,a.name as brandname from king_brands a  $join_cond where 1 $cond  $order_by";
		
		$brand_res=$this->db->query($sql,$param);
		
		if($brand_res->num_rows())
		{
			$brand_det['total_brands']=$brand_res->num_rows();
			
			$sql.=" limit $start,$limit";
			
			$brand_det['brand_list']=$this->db->query($sql,$param)->result_array();
			
			return $brand_det;
		}else{
			return false;
		}
	}
	
	/**
	 * get the brands list by menu and category
	 * @param unknown_type $menu_id
	 * @param unknown_type $catid
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 */
	function get_brands_by_menu_cat($menu_id,$catid,$start,$limit,$full=0,$alpha_sort,$top_brands)
	{
		$param=array();
		$cond='';
		
		$param[]=$catid;
		$param[]=$menu_id;
		
		if($alpha_sort)
		{
			$cond=' and b.name like  ?  '; 
			$param[]=$alpha_sort.'%';
		}
		
		$join_cond='';$order_by='';
		
		
		if($top_brands)
		{
			$join_cond='join king_orders o on o.itemid=c.id join king_transactions t on t.transid=o.transid';
			
			$order_by='order by count(o.id) desc';
			
			$full=0;
			
			$limit=5;
		}
		
		$sql="select a.brandid,b.name as brandname from king_deals a 
					join king_brands b on b.id=a.brandid
					join king_dealitems c on c.dealid=a.dealid
					$join_cond
				where a.catid=? and a.menuid=? and a.publish=1 and c.is_pnh=1 $cond 
				group by a.brandid 
				$order_by";
		
		$brand_res=$this->db->query($sql,$param);
		
		if($brand_res->num_rows())
		{
			$brand_det['total_brands']=$brand_res->num_rows();
		
			if(!$full && !$top_brands)
				$sql.=" limit $start,$limit";
			
			if($top_brands)
				$sql.=" limit $start,$limit";
		
			$brand_det['brand_list']=$this->db->query($sql,$param)->result_array();
		
			return $brand_det;
		}else{
			return false;
		}
		
	}
	
	/**
	 * function for get the categories list
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @return unknown|boolean
	 */
	function get_categories($fid,$start,$limit,$alpha_sort=0,$full)
	{
		$cat_det=array();
		$cat_det['ttl_cat']='';
		$cat_det['cat_list']='';
		
		$cond='';
		$param=array();
		
		if($alpha_sort)
		{
			$cond=" and a.name like ? ";
			$param[]=$alpha_sort.'%';
		}
		
		$join_cond=''; $order_by='';
		
		if($fid!=0)
		{
			$is_menu_linked=$this->apim->check_if_menu_islinked_tofran($fid,0,0,0,0);
				
			if(!$is_menu_linked)
				return false;
			else
			{
				$join_cond='join king_deals d on d.catid=a.id
							join king_dealitems i on i.dealid=d.dealid
							join king_orders o on o.itemid=i.id join king_transactions t on t.transid=o.transid';
			
				$order_by='order by a.name asc';
			
				$cond.="AND franchise_id=".$fid;
			}
		}
		$sql='select a.id,a.name,group_concat(b.id,"::") as sub_catids,group_concat(b.name,"::") as sub_cat_names
					from king_categories a 
					left join king_categories b on b.type=a.id
					'.$join_cond.'
					'.$cond.'	
					group by a.id
				'.$order_by;
		
		$cate_res=$this->db->query($sql,$param);
		
		if($cate_res->num_rows())
		{
			$cat_det['ttl_cat']=$cate_res->num_rows();
			
			if(!$full)
				$sql.=" limit $start,$limit";
			
			$cat_det['cat_list']=$this->db->query($sql,$param)->result_array();
		}
		
		return $cat_det;
	}
	
	/**
	 * function for get the categories by menu
	 * @param unknown_type $menuid
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @return multitype:string NULL
	 */
	function get_categories_by_menu($menuid,$start,$limit,$alpha_sort=0,$full=0,$top_cats)
	{
		$cat_det=array();
		$cat_det['ttl_cat']='';
		$cat_det['cat_list']='';
		$menu_catids=array();
		
		$cond='';
		$param=array();
		
		if($alpha_sort)
		{
			$cond=" and a.name like ? ";
			$param[]=$alpha_sort.'%';
		}
		
		$join_cond='';  $order_by='';
		
		if($top_cats)
		{
			$join_cond.=" join king_deals d on d.catid=a.id JOIN king_dealitems i ON i.dealid=d.dealid  JOIN king_orders o ON o.itemid=i.id JOIN king_transactions t ON t.transid=o.transid AND t.is_pnh=1";
				
			$order_by.=" ORDER BY COUNT(o.id) DESC";
			
			//$limit=5;
		}
			
		
		$sql="select catid from king_deals d where menuid=? group by catid ";
		
		$menu_cat_res=$this->db->query($sql,$menuid);
		
		if($menu_cat_res->num_rows())
		{
			foreach($menu_cat_res->result_array() as $cid)
				array_push($menu_catids,$cid['catid']);
			
			$sql='select a.id,a.name,group_concat(b.id,"::") as sub_catids,group_concat(b.name,"::") as sub_cat_names
						from king_categories a
						left join king_categories b on b.type=a.id
						'. $join_cond.
						' where a.id in ('.implode(',',$menu_catids).') or b.id in ('.implode(',',$menu_catids).')
						or a.type in ('.implode(',',$menu_catids).') or b.type in ('.implode(',',$menu_catids).')
						'.$cond .'
						group by a.id
						'.$order_by.'
					';
			$cate_res=$this->db->query($sql,$param);
		//	echo $this->db->last_query();exit;
			if($cate_res->num_rows())
			{
				$cat_det['ttl_cat']=$cate_res->num_rows();
			
				//if(!$full)
					//$sql.=" limit $start,$limit";
			
				$cat_det['cat_list']=$this->db->query($sql,$param)->result_array();
			}
		}
		
		return $cat_det;
	}
	
	function get_categories_by_brand($brandid,$start,$limit,$alpha_sort=0,$full=0,$top_cats)
	{
		$cat_det=array();
		$cat_det['ttl_cat']='';
		$cat_det['cat_list']='';
		$menu_catids=array();
		
		$cond='';
		$param=array();
		
		if($alpha_sort)
		{
			$cond=" and a.name like ? ";
			$param[]=$alpha_sort.'%';
		}
		$join_cond=''; $order_by='';
		
		if($top_cats)
		{
			$join_cond="join king_deals d on d.catid=a.id
						join king_dealitems i on i.dealid=d.dealid
						join king_orders o on o.itemid=i.id
						join king_transactions t on t.transid=o.transid and t.is_pnh=1
					";
			$order_by=	"order by count(o.id) desc ";
		
			$limit=5;
		}

		$sql="select catid from king_deals where brandid=? group by catid";
		
		$brand_cat_res=$this->db->query($sql,$brandid);
		
		if($brand_cat_res->num_rows())
		{
			foreach($brand_cat_res->result_array() as $cid)
				array_push($menu_catids,$cid['catid']);
			
			$sql='select a.id,a.name,group_concat(b.id,"::") as sub_catids,group_concat(b.name,"::") as sub_cat_names
						from king_categories a
						left join king_categories b on b.type=a.id
						'.$join_cond.'
						where a.id in ('.implode(',',$menu_catids).') or b.id in ('.implode(',',$menu_catids).')
						or a.type in ('.implode(',',$menu_catids).') or b.type in ('.implode(',',$menu_catids).')
						'.$cond.'
						group by a.id '.$order_by ;
						
			
			$cate_res=$this->db->query($sql,$param);
			
			if($cate_res->num_rows())
			{
				$cat_det['ttl_cat']=$cate_res->num_rows();
			
				if(!$full)
					$sql.=" limit $start,$limit";
			
				$cat_det['cat_list']=$this->db->query($sql,$param)->result_array();
			}
		}
		
		return $cat_det;
	}
	
	/**
	 * function for get the categories by brand and menu
	 * @param unknown_type $brandid
	 * @param unknown_type $menuid
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 */
	function get_categories_by_brand_menu($brandid,$menuid,$start,$limit,$full=0,$alpha_sort=0,$top_cats)
	{
		$cat_det=array();
		$cat_det['ttl_cat']='';
		$cat_det['cat_list']='';
		$menu_catids=array();
		$cond='';
		$param=array();
		
		$join_cond=''; $order_by='';
		
		
		if($top_cats)
		{
			$join_cond="join king_deals d on d.catid=a.id
						join king_dealitems i on i.dealid=d.dealid
						join king_orders o on o.itemid=i.id
						join king_transactions t on t.transid=o.transid and t.is_pnh=1
					";
			$order_by=	"order by count(o.id) desc ";
	
			$limit=5;
			
			$full=0;
		}

		if($alpha_sort)
		{
			$cond=" and a.name like ? ";
			$param[]=$alpha_sort.'%';
		}
		
		$sql="select catid from king_deals where brandid=? and menuid=? group by catid";
		
		$brandmenu_cat_res=$this->db->query($sql,array($brandid,$menuid)); 
		
		if($brandmenu_cat_res->num_rows())
		{
			foreach($brandmenu_cat_res->result_array() as $cid)
				array_push($menu_catids,$cid['catid']);
		
			$sql='select a.id,a.name,group_concat(b.id,"::") as sub_catids,group_concat(b.name,"::") as sub_cat_names
						from king_categories a
						left join king_categories b on b.type=a.id '
						.$join_cond.'
						where a.id in ('.implode(',',$menu_catids).') or b.id in ('.implode(',',$menu_catids).')
						or a.type in ('.implode(',',$menu_catids).') or b.type in ('.implode(',',$menu_catids).')
						'.$cond.'
						group by a.id
						'.$order_by;
		
			$cate_res=$this->db->query($sql,$param);
		
			if($cate_res->num_rows())
			{
				$cat_det['ttl_cat']=$cate_res->num_rows();
		
				if(!$full)
					$sql.=" limit $start,$limit";
				
				$cat_det['cat_list']=$this->db->query($sql,$param)->result_array();
			}
		}
		
		return $cat_det;
	}
	
	/**
	 * function for get the menu list by brand and cat
	 * @param unknown_type $brand_id
	 * @param unknown_type $catid
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 */
	function get_menu($brand_id=0,$catid=0,$menuid=0,$start,$limit)
	{
		$cond='';
		$param=array();
		$menu_list=array('total_menus'=>0,"menu_list"=>'');
		
		if($brand_id)
		{
			$cond.=" and a.brandid=? ";
			$param[]=$brand_id;
		}
		
		if($catid)
		{
			$cond.=" and a.catid=?  ";
			$param[]=$catid;
		}
		
		if($menuid)
		{
			$cond.=" and id=?  ";
			$param[]=$menuid;
		}
		
		
		if($brand_id || $catid)
		{
		
			$sql="select b.id as menu_id,b.name as menu
						from king_deals a
						join pnh_menu b on b.id=a.menuid
						join king_dealitems c on c.dealid=a.dealid
					where 1 a.publish=1 and c.is_pnh=1 and b.status=1  $cond
					group by a.menuid
					order by b.name ";
		}else{
			$sql="select id as menu_id,name as menu from pnh_menu where 1 and status=1  $cond order by name asc ";
		}
		
		$menu_list_res=$this->db->query($sql,$param);
		
		if($menu_list_res->num_rows())
		{
			$menu_list['total_menus']=$menu_list_res->num_rows();
			$sql.=" limit $start , $limit ";
			
			$menu_list_res1=$this->db->query($sql,$param);
			
			$menu_list['menu_list']=$menu_list_res1->result_array();
		}
		
		return $menu_list;
	}
	
	/**
	 * function for check the array values are number
	 * @param unknown_type $array
	 * @param unknown_type $predicate
	 */
	function check_isarray_num($array) {
		return ctype_digit(implode('',$array));
	}
	
	/**
	 * function for get the deal list
	 * @param unknown_type $brand_id
	 * @param unknown_type $cat_id
	 * @param unknown_type $menu_id
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @return multitype:NULL Ambigous <multitype:, unknown>
	 * @last_modified roopashree@storeking.in
	 */
	
	function get_deals($brand_id=0,$cat_id=0,$menu_id=0,$start,$limit,$publish,$pids=0,$srch_data=array(),$gender='',$min_price=0,$max_price=0,$fid=0,$sortby,$type='deals')
	{
		$cond='';
		//echo $sortby;
		$cond=' and publish = 1 ';
		$publish_cond='';
		$order_cond='';
		$limit_cond='';

		if($publish==0)
			$publish_cond='';
		else if($publish==1)
			$publish_cond=' and d.publish=1';
		else if($publish==2)
			$publish_cond=' and d.publish=0';
		
		$publish_cond=' and d.publish=1';
		
		$param=array();
		$deal_list=array("ttl_deals"=>0,"deals_list"=>'');
		$deal_list['cat_list'] = array();
		$deal_list['brand_list'] = array();
		$deal_list['price_list'] = array();
		$deal_list['gender_list'] = array();
		$deal_list['attr_list'] = array();


		if($brand_id)
		{
			if(is_array($brand_id))
			{
				if($this->check_isarray_num($brand_id,'is_int'))
					$cond.=" and d.brandid in (".implode(",",array_filter($brand_id)).") ";
			}else{
				$cond.=" and d.brandid=? ";
				$param[]=$brand_id;
			}

		}

		if($cat_id)
		{
			if(is_array($cat_id))
			{
				if($this->check_isarray_num($cat_id))
					$cond.=" and d.catid in (".implode(",",array_filter($cat_id)).")";
			}else{
				$cond.=" and d.catid=? ";
				$param[]=$cat_id;
			}
		}

		if($menu_id)
		{
			$cond.=" and m.id=? ";
			$param[]=$menu_id;
		}

		if($gender)
		{
			$cond.=" and i.gender_attr like ? ";
			$param[]='%'.$gender.'%';
		}

		if($pids)
		{
			$cond.=" and pnh_id in ($pids) ";
		}

		if($min_price)
		{
			if(is_array($min_price))
			{
				$cond.=" and ( ";
				foreach($min_price as $i=>$m)
				{
					$cond.="(i.price >= ? ";
					$param[]=$m;

					//if max price set
					if($max_price)
					{
						if(is_array($max_price))
						{
							if(isset($max_price[$i]))
							{
								$cond.=" and i.price <= ? ) ";
								$cond.=(count($min_price)!=($i+1))?' or ':'';
							}
							$param[]=$max_price[$i];		
						}		
					}else{
						$cond.=" ) ";
						$cond.=(count($min_price)!=($i+1))?' or ':'';
					}
				}
				$cond.=" ) ";
			}else{
				$cond.=" and i.price >= ? ";
				$param[]=$min_price;
			}

			if($max_price)
			{
				if(is_array($max_price) && !is_array($min_price))
				{
					$cond.=" and ( ";
					foreach($max_price as $i=>$m)
					{
						$cond.="(i.price <= ? )  ";
						$cond.=(count($max_price)!=($i+1))?' or ':'';
						$param[]=$m;
					}
					$cond.=" ) ";
				}else if(!is_array($max_price))
				{
					$cond.=" and i.price <= ? ";
					$param[]=$max_price;
				}
			}
		}

		if($max_price && !$min_price)
		{
			if(is_array($max_price))
			{
				$cond.=" and ( ";
				foreach($max_price as $i=> $m)
				{
					$cond.="(i.price <= ? )  ";
					$cond.=(count($max_price)!=($i+1))?' or ':'';
					$param[]=$m;
				}
				$cond.=" ) ";
			}else{
				$cond.=" and i.price <= ? ";
				$param[]=$max_price;
			}
		}

		$join_tbl = '';
		$rel_filter_tag = ',0 as rel_ttl';

		//thi condition used for search api
		if(!empty($srch_data))
		{
			$tag=trim($srch_data['tag']);
			$menuid=$srch_data['menuid'];

			if($tag == 'offers')
			{
				$cond.=' and mp_is_offer = 1 and unix_timestamp() between unix_timestamp(mp_offer_from) and unix_timestamp(mp_offer_to)  ';
				$tag = '';
			}
			
			$tag = str_ireplace('  ', ' ', $tag);

			$attrs=$srch_data['attrs'];

			if($attrs)
			{
				$join_tbl .= '  join m_product_deal_link dl on dl.itemid = i.id and dl.is_active = 1  
								join m_product_attributes pa on pa.pid = dl.product_id 
								join m_attributes atr on atr.id = pa.attr_id 
								
							'; 
				foreach($attrs as $adet)
				{
					$cond.=' and attr_name = "'.$adet['name'].'" and  attr_value = "'.$adet['val'].'" ';
				}
			}

			if($tag)
			{

				/*if($menuid)
					$cond.=' and m.id in ('.$menuid.') ';*/

				/*if($this->db->query("select count(*) as t from king_dealitems where pnh_id = ?  ",$tag)->row()->t && is_int($tag))
				{
					$cond.=' and pnh_id=?  ';
					$param[]=$tag;
				}else if($this->db->query("select count(*) as t from king_brands where name = ? ",$tag)->row()->t)
				{
					$cond.=' and b.name = ?  ';
					$param[]=$tag;
				}else
				*/
				{

					$cond.=' and (pnh_id=? or  i.name like ? or mc.name like ? or b.name like ? or c.name like ? or d.keywords  like  ? )  ';
					$param[]=$tag;
					$param[]='%'.$tag.'%';
					$param[]='%'.$tag.'%';
					$param[]='%'.$tag.'%';
					$param[]='%'.$tag.'%';
					$param[]='%'.$tag.'%';

					$tag_list =  explode(' ',$tag);
					$tag_list[] = $tag;
					foreach($tag_list as $tg)
					{
						// check if tag is brand name
						/*$bid = @$this->db->query("select * from king_brands where name = ? ",$tg)->row()->id;
							
						if($bid)
						{
							$cond .= ' and d.brandid = '.$bid;
						}*/
						
						if($tg*1)
						{
							// check if tag is brand name
							$t_pnh_id = @$this->db->query("select * from king_dealitems where pnh_id = ? ",$tg)->row()->pnh_id;
							
							if($t_pnh_id)
							{
								$cond .= ' and pnh_id = '.$t_pnh_id;
							}
						}
					}

					
					/*
					if($this->db->query("select count(*) as t from king_dealitems where is_pnh = 1 and  (pnh_id = ? or name = ? )",array($tag*1,$tag))->row()->t)
					{
						// recheck - name and id diffs 
						$cond .= ' and ( pnh_id = "'.$tag.'" or  name = "'.$tag.'" ) ';
					}else
					{
						$tag_list =  explode(' ',$tag);
						$tag_list[] = $tag;
						$kwd_srch = array(); 
						$rel_filter = array();
						foreach($tag_list as $tg)
						{
							// check if tag is brand name
							$bid = @$this->db->query("select * from king_brands where name = ? ",$tg)->row()->id;
							
							if($bid)
							{
								$cond .= ' and d.brandid = '.$bid;
								continue ;
							}
							
							// recheck for only one find_in_set - need to check with match function in mysql for relavency 
							$tg = strtolower($tg);
							$kwd_srch[] = " find_in_set('$tg',concat(pnh_id,',',i.name,',',ifnull(mc.name,''),',',b.name,',',c.name,',',d.keywords)) ";
							$rel_filter[] =  " if(find_in_set('$tg',concat(pnh_id,',',i.name,',',ifnull(mc.name,''),',',b.name,',',c.name,',',d.keywords)),1,0) ";
							
							/*
							$tg = strtoupper($tg);
							$kwd_srch[] = " find_in_set('$tg',concat(pnh_id,',',i.name,',',ifnull(mc.name,''),',',b.name,',',c.name,',',d.keywords)) ";
							$rel_filter[] =  " if(find_in_set('$tg',concat(pnh_id,',',i.name,',',ifnull(mc.name,''),',',b.name,',',c.name,',',d.keywords)),1,0) ";
							
							$tg = strtolower($tg);
							$kwd_srch[] = " find_in_set('$tg',concat(pnh_id,',',i.name,',',ifnull(mc.name,''),',',b.name,',',c.name,',',d.keywords)) ";
							$rel_filter[] =  " if(find_in_set('$tg',concat(pnh_id,',',i.name,',',ifnull(mc.name,''),',',b.name,',',c.name,',',d.keywords)),1,0) ";
							*/
					/*
						}
						if(count($kwd_srch))
						{
							$cond .= ' and ('.implode(' or ',$kwd_srch).' ) ';
							$rel_filter_tag = ','.implode('+',$rel_filter).' as rel_ttl';
						}
					}
					*/

				}

			}

		}

		$show_instock='';
		//$cond .= ' and publish = 1 '; 
		
		$order_by_cond = '';
		if($sortby == 'popular')
		{
			$order_by_cond = ' total_orders desc, ';
		}else if($sortby == 'new')
		{
			$order_by_cond = ' d_sno desc, ';
		}elseif($sortby == 'recently_sold')
		{
			$order_by_cond = ' o.time desc, ';
		}elseif($sortby == 'max_price')
		{
			$order_by_cond = ' price desc, ';
		}elseif($sortby == 'min_price')
		{
			$order_by_cond = 'price asc, ';
		}
		elseif($sortby == 'instock')
		{
			$show_instock=1;
		}
		elseif($sortby == 'soldout')
		{
			$show_instock=0;
		}
		else
		{
			$order_by_cond = ' name asc, ';
		}

		$price_type = 0;

		if($fid)
			$price_type=$this->erpm->get_fran_pricetype($fid);
			
		 
		if($type == 'total')
		{
			 
			$sql="select count(distinct i.id) as total
						from king_deals d	
						join king_dealitems i on i.dealid=d.dealid	
						join king_brands b on b.id=d.brandid	
						join king_categories c on c.id=d.catid	
						$join_tbl	
						left outer join pnh_menu m on m.id=d.menuid	
						left outer join king_categories mc on mc.id=c.type	
						where is_pnh=1	
						$cond	
			";

			$deal_list['ttl_deals']=$this->db->query($sql,$param)->row()->total;
		
		}else
		{

			if($type == 'refine')
			{
				$sql="select i.id as itemid,i.is_group,i.pnh_id as pid,orgprice,price,gender_attr,c.name as cat,b.name as brand,d.catid,d.brandid 
							from king_deals d 
							join king_dealitems i on i.dealid=d.dealid 
							join king_brands b on b.id=d.brandid 
				JOIN king_categories c ON c.id=d.catid
							$join_tbl 
							left outer join pnh_menu m on m.id=d.menuid 
							left outer join king_categories mc on mc.id=c.type 
							where  is_pnh=1 
							$cond
							group by i.id 
							order by c.name,b.name
							
					";
				
				$deal_list_res=$this->db->query($sql,$param) or die(mysql_error());
				
				if($deal_list_res->num_rows())
				{
					$deal_list['ttl_deals']=$deal_list_res->num_rows();
					
					foreach($deal_list_res->result_array() as $d_det)
					{
						
						$deal_list['cat_list'][$d_det['catid']] = array('id'=>$d_det['catid'],'name'=>$d_det['cat']);
						$deal_list['brand_list'][$d_det['brandid']] = array('id'=>$d_det['brandid'],'name'=>$d_det['brand']);
						$deal_list['price_list'][] = $d_det['price'];
						$deal_list['gender_list'][] = $d_det['gender_attr'];
						
					
						if($d_det['is_group'])
						{
							$sql_q = "
										(
											select l.itemid,p.product_id,concat(group_concat(concat(a.attribute_name,':',v.attribute_value)),',ProductID:',p.product_id) as a
												from m_product_group_deal_link l
												join products_group_pids p on p.group_id=l.group_id
												join products_group_attributes a on a.attribute_name_id=p.attribute_name_id
												join products_group_attribute_values v on v.attribute_value_id=p.attribute_value_id
												join m_product_info p1 on p1.product_id = p.product_id and p1.is_sourceable = 1
											where l.itemid=?
											group by p.product_id
										)
										union
										(
											select a.itemid,a.product_id,concat(group_concat(concat(attr_name,':',attr_value) order by f.id desc ),',ProductID:',a.product_id) as a
												from m_product_deal_link a
												join king_dealitems b on a.itemid = b.id
												join m_product_info d on d.product_id = a.product_id
												join m_product_attributes e on e.pid = d.product_id
												join m_attributes f on f.id = e.attr_id
											where b.is_group = 1 and a.itemid = ? and a.is_active = 1  and d.is_sourceable = 1 and length(attr_value)  
											group by a.product_id
										)";
									foreach($this->db->query($sql_q,array($d_det['itemid'],$d_det['itemid']))->result_array() as $p)
										$deal_list['attr_list'][]=$p['a'];
						}
					}
					
						sort($deal_list['price_list']);

						$min_price = 0;
						$max_price = 0;
						if($deal_list['price_list'] > 1)
						{
							$min_price = $deal_list['price_list'][0];
							$max_price = $deal_list['price_list'][count($deal_list['price_list'])-1];
						}

						$deal_list['price_list'] = array('min'=>$min_price,'max'=>$max_price);

						$deal_list['gender_list'] = array_filter(array_unique($deal_list['gender_list']));
						$deal_list['attr_list'] = array_filter(array_unique($deal_list['attr_list']));
					
				}
			}else if($type == 'deals')
			{
				$sql="select free_frame,has_power,pnh_id as pid,i.size_chart,i.id as itemid,i.name,m.name as menu,m.id as menu_id,
						i.gender_attr,c.name as category,d.catid as category_id,mc.name as main_category,i.member_price,
						c.type as main_category_id,b.name as brand,d.brandid as brand_id,Round(i.orgprice,0) as mrp,i.price,i.store_price,
						i.is_combo,ifnull(v.main_image,concat('".IMAGES_URL."items/',d.pic,'.jpg')) as image_url,ifnull(v.main_image,concat('".IMAGES_URL."items/small/',d.pic,'.jpg')) as small_image_url,d.description,i.shipsin as ships_in,d.keywords,
						count(o.id) as total_orders,
							(if($price_type,i.price,i.member_price))/orgprice as p_discount,
						i.sno as d_sno,i.live $rel_filter_tag
						from king_deals d
						join king_dealitems i on i.dealid=d.dealid
						join king_brands b on b.id=d.brandid
						join king_categories c on c.id=d.catid
						$join_tbl
						left outer join pnh_menu m on m.id=d.menuid
						left join m_vendor_product_images v on v.item_id=i.id
						left outer join king_categories mc on mc.id=c.type
						left join king_orders o on o.itemid = i.id
						where  is_pnh=1 $cond
						group by i.id
						order by $order_by_cond p_discount asc,rel_ttl desc
					limit $start,$limit";
				
				$deal_det_res=$this->db->query($sql,$param) or die(mysql_error());
				$deal_det = array();
				if($deal_det_res->num_rows())
				{
					$deal_det = $deal_det_res->result_array();
					foreach($deal_det as $i=>$d)
					{
						$itemid=$d['itemid'];
						$prods=$this->db->query("select p.product_name as name,l.qty from m_product_deal_link l join m_product_info p on p.product_id=l.product_id where l.itemid=?",$d['itemid'])->result_array();
						$deal_det[$i]['products']=array("product"=>$prods);
						
						$v_imgs = @$this->db->query("select other_images from m_vendor_product_images where item_id = ? ",$d['itemid'])->row()->other_images;
						if($v_imgs)
						{
							$v_imgs = explode(',',$v_imgs);
							foreach($v_imgs as $vimg)
								$deal_det[$i]['images'][] = array('url'=>$vimg);
						}else
						{
							$deal_det[$i]['images']=$this->db->query("select CONCAT('".IMAGES_URL."items/',r.id,'.jpg')  as url from king_resources r where r.itemid=? and type=0",$d['itemid'])->result_array();
						}
						
						$deal_det[$i]['attributes']=array();
						$deal_det[$i]['pseller_id'] = "";
						
						$is_live=$deal_det[$i]['live'];
						/*foreach($this->db->query("select group_concat(concat(a.attribute_name,':',v.attribute_value)) as a from m_product_group_deal_link l join products_group_pids p on p.group_id=l.group_id join products_group_attributes a on a.attribute_name_id=p.attribute_name_id join products_group_attribute_values v on v.attribute_value_id=p.attribute_value_id where l.itemid=? group by p.product_id",$itemid)->result_array() as $i2=>$p)
							$deal_det[$i]['attributes']['attr'.($i2+1)]=$p['a'];
							*/
						
						/*
							$min_max_price_det=$this->get_min_max_price($brand_id,$cat_id,$menu_id,$start,$limit,$pids,$srch_data,$gender);
							$deal_det[$i]['min_price']=$min_max_price_det['min_price'];
							$deal_det[$i]['max_price']=$min_max_price_det['max_price'];
						*/
						$margin = 0;
						if($fid)
						{
							$margin_det=$this->erpm->get_pnh_margin($fid,$d['pid']);
								
							if($d['is_combo'])
								$margin=$margin_det['combo_margin'];
							else
								$margin=$margin_det['margin'];
						}
						
						$lcost=0;
						$deal_det[$i]['mp_frn_max_qty']=$deal_det[$i]['mp_mem_max_qty']=$member_price_det['member_price']=0;
						
						
						$member_price_det=$this->erpm->get_memberprice($itemid,$fid,0);
						if($member_price_det['status'] == 'success')
						{
							$deal_det[$i]['price_type']=$member_price_det['price_type'];
							if($member_price_det['price_type'])
							{
								//echo 3;print_r($member_price_det);die();
								$deal_det[$i]['mp_frn_max_qty']=@$member_price_det['mp_frn_max_qty']?$member_price_det['mp_frn_max_qty']:0;
								$deal_det[$i]['mp_mem_max_qty']=@$member_price_det['mp_mem_max_qty']?$member_price_det['mp_mem_max_qty']:0;
								$deal_det[$i]['price']=@$member_price_det['price']?round($member_price_det['price'],0):'';
								$deal_det[$i]['max_ord_qty']=$member_price_det['max_ord_qty']?$member_price_det['max_ord_qty']:0;
								$lcost=round($member_price_det['price']-($member_price_det['price']/100*$margin),0);
							}
							else
							{
								$deal_det[$i]['max_ord_qty'] = $member_price_det['max_ord_qty'];
								$deal_det[$i]['price']=round($member_price_det['price'],0);
								$lcost=round($member_price_det['price']-($member_price_det['price']/100*$margin),0);
							}
						}else
						{
							return $member_price_det;
						}
							
	
						$deal_det[$i]['landing_cost']=$lcost;
						
						$deal_det[$i]['availability']=0;

						$avail=$this->erpm->do_stock_check(array($itemid),array(1),true);
						if(count($avail))
						{
								//print_r($avail_det);exit;
								if($avail[$itemid][0]['stk']==0 && $avail[$itemid][0]['status']==0)	
									$deal_det[$i]['availability']=0;
								else
									$deal_det[$i]['availability']=1;
						}
						
						//=============< Stock Detail >===============
						$chk_deal_stk = $this->erpm->check_deal_stock(array($itemid));
			
						if($chk_deal_stk->num_rows())
						{
							foreach($chk_deal_stk->result_array() as $pstk) {
								$i_itemid = $pstk['itemid'];
								$deal_stk['deal_stk_det'][$i_itemid]['is_limited_stk'] = $pstk['is_limited_stk'];
								$deal_stk['deal_stk_det'][$i_itemid]['d_stk'] = $pstk['d_stk'];
							}
							$limited_stk=$deal_stk['deal_stk_det'][$itemid]['is_limited_stk'];
							$stock_qty=$deal_stk['deal_stk_det'][$itemid]['d_stk'];
							$deal_det[$i]['is_limited_stk']=$limited_stk;
							$deal_det[$i]['stock_qty']=$stock_qty;
							if($stock_qty<=5)
								$deal_det[$i]['stock_msg']='Last '.$deal_det[$i]['stock_qty'].' stock';
							else
								$deal_det[$i]['stock_msg']='Stock : '.$deal_det[$i]['stock_qty'];
						}else 
						{
							$deal_det[$i]['stock_qty'] = 0;
							$deal_det[$i]['is_limited_stk'] = 0;
							$deal_det[$i]['stock_msg']='';
						}
						//=============< Stock Detail >===============
						
						//========< Instock filter >===================
						if(match_in_list($show_instock,'0,1') )
						{
							if($show_instock==1)
							{
								if($is_live==0) {
									unset($deal_det[$i]);	//echo 'NOT AVAILABLE';
									$i--;
									continue;
								}
							}
							elseif($show_instock==0)
							{
								if($is_live==1) {
									unset($deal_det[$i]);  //echo 'AVAILABLE';
									$i--;
									continue;
								}
							}
						}
						//========< Instock filter >===================
					}
				}
				else
				{
					$deal_det=array();
				}
				$deal_list['deals_list']=$deal_det;
			}
			
		}
		//echo $this->db->last_query();exit;
		return $deal_list; 
	}
	
	function get_local_updated_deals($version)
	{
		$output=array();
		$version_details=array();
		
		$deals_res = $this->db->query("
										select vdl.item_id,ifnull(publish,0) as publish,c.orgprice,if(1,c.price,c.member_price) as price
											from m_apk_version a
											join m_apk_version_deal_link vdl on vdl.version_id = a.id 
											left join king_dealitems c on c.id = vdl.item_id 
											left join king_deals d on d.dealid = c.dealid 
										where version=?  
								",$version);
		
		if($deals_res->num_rows())
		{
			$deal_list = array();
			foreach($deals_res->result_array() as $row)
			{
				$row['pseller_id'] = '';
				$deal_list[] = $row;
			}
			$version_details["products"]=$deal_list;
			array_push($output,$version_details);
		}
		return $output;
	}
	
	/**
	 * function get the min price max price for deal list
	 * @param unknown_type $brand_id
	 * @param unknown_type $cat_id
	 * @param unknown_type $menu_id
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $pids
	 * @param unknown_type $srch_data
	 * @param unknown_type $gender
	 * @return multitype:string unknown
	 */
	function get_min_max_price($brand_id=0,$cat_id=0,$menu_id=0,$start,$limit,$pids=0,$srch_data=array(),$gender='')
	{
		$cond='';
		$param=array();
		$deal_list=array();
		$result_set=array('min_price'=>'','max_price'=>'');
		
		if($brand_id)
		{
			if(is_array($brand_id))
			{
				if($this->check_isarray_num($brand_id,'is_int'))
					$cond.=" and d.brandid in (".implode(",",array_filter($brand_id)).") ";
			}else{
				$cond.=" and d.brandid=? ";
				$param[]=$brand_id;
			}	
		}
		
		if($cat_id)
		{
			if(is_array($cat_id))
			{
				if($this->check_isarray_num($cat_id))
					$cond.=" and d.catid in (".implode(",",array_filter($cat_id)).")";
			}else{
				$cond.=" and d.catid=? ";
				$param[]=$cat_id;
			}
		}
		
		if($menu_id)
		{
			$cond.=" and m.id=?";
			$param[]=$menu_id;
		}
		
		if($gender)
		{
			$cond.=" and i.gender_attr like ?";
			$param[]='%'.$gender.'%';
		}
		
		if($pids)
		{
			$cond.=" and pnh_id in ($pids)";
		
		}
		
		
		//thi condition used for search api
		if(!empty($srch_data))
		{
			$tag=$srch_data['tag'];
			$menuid=$srch_data['menuid'];
		
			if($tag)
			{
				$cond.=' and (pnh_id=? or  i.name like ? or mc.name like ? or b.name like ? or c.name like ? or d.keywords  like  ? ) and m.id in ('.$menuid.')';
				$param[]=$tag;
				$param[]='%'.$tag.'%';
				$param[]='%'.$tag.'%';
				$param[]='%'.$tag.'%';
				$param[]='%'.$tag.'%';
				$param[]='%'.$tag.'%';
		
			}
		
		}
		
		$sql="select min(i.orgprice) as min_price,max(i.orgprice) as max_price from king_deals d
				join king_dealitems i on i.dealid=d.dealid
				join king_brands b on b.id=d.brandid
				join king_categories c on c.id=d.catid
				left outer join pnh_menu m on m.id=d.menuid
				left outer join king_categories mc on mc.id=c.type
				where d.publish=1 and is_pnh=1 $cond order by d.sno asc";
		
		$deal_list_res=$this->db->query($sql,$param);
		
		if($deal_list_res->num_rows)
		{
			$deal_list=$deal_list_res->row_array();
			$result_set['min_price']=$deal_list['min_price'];
			$result_set['max_price']=$deal_list['max_price'];
		}
		
		return $result_set;
	}
	
	/**
	 * function to get the deal info by pid
	 * @param unknown_type $pid
	 * @last_modify Shivaraj_Nov_21_2014 member id update
	 */
	function get_deal($pid=0,$fid=0,$mem_id=0)
	{
		$sql="select free_frame,has_power,i.id as item_id,i.size_chart,pnh_id as pid,i.is_group,i.gender_attr,i.id as itemid,i.name,d.tagline,c.name as category,m.name as menu,d.menuid as menu_id,
					d.catid as category_id,mc.name as main_category,c.type as main_category_id,b.name as brand,d.brandid as brand_id,round(i.member_price,0) as member_price,
					round(i.orgprice,0) as mrp,round(i.price,0) as price,i.store_price,i.is_combo,i.powered_by,
					ifnull(v.main_image,concat('".IMAGES_URL."items/',d.pic,'.jpg')) as image_url,
					d.description,i.shipsin as ships_in,d.keywords,i.live as is_stock,d.publish as is_enabled,
					ifnull(v.main_image,concat('".IMAGES_URL."items/small/',d.pic,'.jpg')) as small_image_url, 
					i.has_insurance,d.publish
					from king_deals d 
					join king_dealitems i on i.dealid=d.dealid 
					join king_brands b on b.id=d.brandid 
					join king_categories c on c.id=d.catid 
					left outer join pnh_menu m on m.id=d.menuid 
					left outer join king_categories mc on mc.id=c.type 
					left join m_vendor_product_images v on v.item_id=i.id  
					where is_pnh=1 and i.pnh_id =? and i.pnh_id!=0
					order by d.sno asc";
		
		$deal_res=$this->db->query($sql,$pid);
		
		$price_type = 0;
		if($fid)
		$price_type=$this->erpm->get_fran_pricetype($fid);
		 
		if($deal_res->num_rows())
		{
			$deal_info=$deal_res->row_array();
			$deal_info['images'] = array();
			$v_imgs = @$this->db->query("select other_images from m_vendor_product_images where item_id = ? ",$deal_info['itemid'])->row()->other_images;
			if($v_imgs)
			{
				$v_imgs = explode(',',$v_imgs);
				foreach($v_imgs as $vimg)
					$deal_info['images'][] = array('url'=>$vimg,'small_img_url'=>$vimg);
			}else
			{
				$deal_info['images']=$this->db->query("select CONCAT('".IMAGES_URL."items/',r.id,'.jpg')  as url,CONCAT('".IMAGES_URL."items/small/',r.id,'.jpg')  as small_img_url from king_resources r where r.itemid=? and type=0",$deal_info['itemid'])->result_array();
			}
			//	$deal_info['images']=$this->db->query("SELECT IFNULL(CONCAT('".IMAGES_URL."items/',r.id,'.jpg'),v.main_image)AS url,IFNULL(CONCAT('".IMAGES_URL."items/small/',r.id,'.jpg'),v.other_images) AS small_img_url  FROM king_resources r  LEFT JOIN m_vendor_product_images v ON v.item_id=r.itemid WHERE r.itemid=?  AND TYPE=0",$deal_info['itemid'])->result_array();
			
			if($deal_info['is_combo'])
				$deal_info['products']=$this->db->query("select group_concat(concat(product_name,',',qty) SEPARATOR '||') as products from m_product_deal_link a join m_product_info b on a.product_id = b.product_id where a.itemid = ? and a.is_active = 1 ",$deal_info['itemid'])->row()->products;
			
			$deal_info['pseller_id'] = "";
			$deal_info['offer_note'] = "";
			$deal_info['attributes']="";
			$deal_info['p_attr_stk'] = "";
			$margin_det = array();
			$deal_info['lens_package_list'] = array();
			
			// check if deal is lens and has power status to capture prescriotion info.
			if($deal_info['has_power'])
			{
				$deal_info['lens_package_list'] = $this->db->query("select * from m_lens_package where package_for = 'Full-Rim' ",$itemdet['lens_type'])->result_array();
			}
			
			$margin = 0;
			if($fid)
			{
				$margin_det=$this->erpm->get_pnh_margin($fid,$pid);
			
				if($deal_info['is_combo'])
					$margin=$margin_det['combo_margin'];
				else
					$margin=$margin_det['margin'];
			}
			
			
			$member_price_det=$this->erpm->get_memberprice($deal_info['item_id'],$fid,$mem_id);
			if($member_price_det['status'] == 'success')
			{
				$deal_info['price_type']= $member_price_det['price_type'];
				if($member_price_det['price_type'])
				{
					$deal_info['offer_note']= @$member_price_det['mp_offer_note'];
					$deal_info['mp_frn_max_qty']=@$member_price_det['mp_frn_max_qty'];
					$deal_info['mp_mem_max_qty']=@$member_price_det['mp_mem_max_qty'];
					$deal_info['price']=round(@$member_price_det['price'],0);
					$deal_info['max_ord_qty']=$member_price_det['max_ord_qty'];
				}else
				{
					$deal_info['price']=round($member_price_det['price'],0);
					$deal_info['max_ord_qty']=$member_price_det['max_ord_qty'];
				}
			}
			else
			{
				return false;
			}
			
			$lcost=round($deal_info['price']-($deal_info['price']/100*$margin),0);
			
//			$deal_info['max_ord_qty'] = $this->erpm->get_maxordqty_pid($fid,$pid,0);
//			$deal_info['price']=round($deal_info['price'],0);
//			$lcost=round($deal_info['price']-($deal_info['price']/100*$margin),0);
			
			
			$deal_info['landing_cost']=$lcost;
			$avail=$this->erpm->do_stock_check(array($deal_info['item_id']),array(1),true);
			if(count($avail))
			{
				//print_r($avail_det);exit;
				if($avail[$deal_info['item_id']][0]['stk']==0 && $avail[$deal_info['item_id']][0]['status']==0)	
					$deal_info['availability']=0;
				else
					$deal_info['availability']=1;
			}
			//===========< Deal Stock Check >==========
			$chk_deal_stk = $this->erpm->check_deal_stock(array($deal_info['item_id']));
			
			$itemid = $deal_info['item_id'];
			if($chk_deal_stk->num_rows())
			{
				foreach($chk_deal_stk->result_array() as $pstk) {
					$itemid = $pstk['itemid'];
					$deal_stk['deal_stk_det'][$itemid]['is_limited_stk'] = $pstk['is_limited_stk'];
					$deal_stk['deal_stk_det'][$itemid]['d_stk'] = $pstk['d_stk'];
				}
				$limited_stk=$deal_stk['deal_stk_det'][$deal_info['item_id']]['is_limited_stk'];
				$stock_qty=$deal_stk['deal_stk_det'][$deal_info['item_id']]['d_stk'];
				$deal_info['is_limited_stk']=$limited_stk;
				$deal_info['stock_qty']=$stock_qty;
				if($stock_qty<=5)
					$deal_info['stock_msg']='Last '.$deal_info['stock_qty'].' stock';
				else
					$deal_info['stock_msg']='Stock : '.$deal_info['stock_qty'];
					
			}else 
			{
				$deal_info['stock_qty'] = 0;
				$deal_info['is_limited_stk'] = 0;
				$deal_info['stock_msg']='';
			}
			//===========< End Deal Stock Check >==========
			
			if($deal_info['is_group'])
			{
				/*
				$sql_q = "
				(
				select l.itemid,p.product_id,concat(group_concat(concat(a.attribute_name,':',v.attribute_value)),',ProductID:',p.product_id) as a
				from m_product_group_deal_link l
				join products_group_pids p on p.group_id=l.group_id
				join products_group_attributes a on a.attribute_name_id=p.attribute_name_id
				join products_group_attribute_values v on v.attribute_value_id=p.attribute_value_id
				where l.itemid=?
				group by p.product_id
				)
				union
				(
				select a.itemid,a.product_id,concat(group_concat(concat(attr_name,':',attr_value) order by f.id desc ),',ProductID:',a.product_id) as a
				from m_product_deal_link a
				join king_dealitems b on a.itemid = b.id
				join m_product_info d on d.product_id = a.product_id
				join m_product_attributes e on e.pid = d.product_id
				join m_attributes f on f.id = e.attr_id
				where b.is_group = 1 and a.itemid = ? and a.is_active = 1
				group by a.product_id
				)";
				foreach($this->db->query($sql_q,array($deal_info['itemid'],$deal_info['itemid']))->result_array() as $p)
					$deal_info['attributes']=$p['a'];
				*/
				
				$sql_a = "select group_concat(concat(product_id,':',ifnull(stk,0))) as p_attr_stk,group_concat(a SEPARATOR '||') as attrs
				from (
				select itemid,h.product_id,a,member_price,mp_frn_max_qty,mp_mem_max_qty,ifnull(ven_stock_qty,sum(available_qty)) as stk from (
				(
				select l.itemid,p.product_id,concat(group_concat(concat(a.attribute_name,':',v.attribute_value)),',ProductID:',p.product_id) as a,di.member_price,di.mp_frn_max_qty,di.mp_mem_max_qty
				from m_product_group_deal_link l
				JOIN king_dealitems di ON di.id=l.itemid
				join products_group_pids p on p.group_id=l.group_id
				join products_group_attributes a on a.attribute_name_id=p.attribute_name_id
				join products_group_attribute_values v on v.attribute_value_id=p.attribute_value_id
				join m_product_info p1 on p1.product_id = p.product_id and p1.is_sourceable = 1
				where l.itemid=?
				group by p.product_id
				)
				union
				(
				select a.itemid,a.product_id,concat(group_concat(concat(attr_name,':',attr_value) order by f.id desc ),',ProductID:',a.product_id) as a,b.member_price,b.mp_frn_max_qty,b.mp_mem_max_qty
				from m_product_deal_link a
				join king_dealitems b on a.itemid = b.id
				join king_deals c on c.dealid = b.dealid
				join m_product_info d on d.product_id = a.product_id
				join m_product_attributes e on e.pid = d.product_id
				join m_attributes f on f.id = e.attr_id
				where b.is_group = 1 and a.itemid = ? and a.is_active = 1 and d.is_sourceable = 1 and length(attr_value) 
				group by a.product_id
				)
				) as h
				left join m_vendor_product_link vl on vl.product_id = h.product_id
				left join t_stock_info pstk on pstk.product_id = h.product_id
				group by h.product_id
				) as g
				having attrs is not null
				";
				
				$attr_res = $this->db->query($sql_a,array($deal_info['item_id'],$deal_info['item_id']));
				if($attr_res->num_rows())
				{
					$deal_info['attributes'] = @$attr_res->row()->attrs;
					$deal_info['p_attr_stk'] = @$attr_res->row()->p_attr_stk;
				}
				
			}
			
			$deal_info['max_order_qty'] = $deal_info['max_ord_qty']; 
			
			$deal_info['description']=htmlspecialchars($deal_info['description']);
			$asin_res=$this->db->query("SELECT IFNULL(partner_ref_no,'N/A') AS asin  
											FROM m_partner_deal_link a 
											JOIN king_dealitems b ON b.id = a.itemid 
											WHERE a.itemid = ? AND a.partner_id = 7 ",$deal_info['itemid']);
			$deal_info['asin']=0;
			if($asin_res->num_rows())
				$deal_info['asin']=$asin_res->row()->asin;
			
			return $deal_info;
		}else{
			return false;
		}
	}
	
	/**
	 * function for add the item to cart table
	 * @param unknown_type $pid
	 * @param unknown_type $userid
	 * @param unknown_type $fid
	 * @param unknown_type $qty
	 * @param unknown_type $attributes
	 * @return boolean
	 */
	function add_to_cart($pid,$userid,$fid,$qty,$attributes)
	{
		$inp=array();
		$inp['user_id']=$userid;
		$inp['franchise_id']=$fid;
		$inp['pid']=$pid;
		$inp['qty']=$qty;
		$inp['attributes']=$attributes;
		$inp['status']=1;
		$inp['added_on']=cur_datetime();
		
		$this->db->insert("pnh_api_franchise_cart_info",$inp);
		if($this->db->affected_rows())
			return true;
		else
			return false;
			
	}
	
	/**
	 * update the cart item qty
	 * @param unknown_type $pid
	 * @param unknown_type $userid
	 * @param unknown_type $fid
	 * @param unknown_type $qty
	 * @param unknown_type $attributes
	 * @return boolean
	 */
	
	function update_cart($pid,$userid,$fid,$qty,$attributes)
	{
		$this->db->query("update pnh_api_franchise_cart_info set qty=?,updated_on=? where franchise_id=? and pid=? and status=1",array($qty,cur_datetime(),$fid,$pid));

		if($this->db->affected_rows())
			return true;
		else
			return false;
	}
	
	/**
	 * function to update the cart item attributeas like qty size;
	 * @param unknown_type $pid
	 * @param unknown_type $userid
	 * @param unknown_type $fid
	 * @param unknown_type $qty
	 * @param unknown_type $attributes
	 */
	function update_cart_item_attr($pid,$userid,$fid,$qty,$attributes)
	{
		$update_field='';
		$update_vl=array();
		
		$deal_det_res=$this->db->query("select * from pnh_api_franchise_cart_info where franchise_id=? and pid=? and status=1",array($fid,$pid));
		
		if($deal_det_res->num_rows()==1)
		{
			$deal_det=$deal_det_res->row_array();
			
			if($attributes)
			{
				$attr_deff=array_diff($attributes, explode('|',$deal_det['attributes']));
				
				//compare the db db attr and update input attr are same
				if(count($attr_deff))
				{	
					$update_field.=" attributes=? ,";
					$update_vl[]=implode("|",$attributes);
				}
					
			}
			
			$update_vl[]=$qty;
			$update_vl[]=cur_datetime();
			$update_vl[]=$pid;
			$update_vl[]=$fid;
			
			$sql="update pnh_api_franchise_cart_info set $update_field qty=?,updated_on=? 
					where pid=? and franchise_id=? and status=1;";
			
			$this->db->query($sql,$update_vl);
				
			if($this->db->affected_rows())
				return true;
			else
				return false;
		
		
		}else if($deal_det_res->num_rows() > 1)
		{
			$deal_det=$deal_det_res->row_array();
			$id=0;
			
			if($attributes)
			{
				foreach($deal_det as $d)
				{
					$attr_deff=array_diff($attributes, explode('|',$d['attributes']));
					
					if(!count($attr_deff))
					{
						$id=$d['id'];
						break;
					}
				}
				
				$sql="update pnh_api_franchise_cart_info set 1 attributes=?,qty=?,updated_on=?
							where pid=? and franchise_id=? and status=1 and id=?;";
				
				$this->db->query($sql,array(implode("|",$attributes),$qty,cur_datetime(),$pid,$fid,$id));
				
				if($this->db->affected_rows())
					return true;
				else
					return false;
				
			
			}else{
				return false;
			}
		}
		
		return false;
	}
	
	/**
	 * function for remove the item to cart
	 * @param unknown_type $pid
	 * @param unknown_type $fid
	 */
	function remove_to_cart($pid,$fid)
	{
		if($pid!=0)
		$this->db->query("update pnh_api_franchise_cart_info set status=0,updated_on=? where franchise_id=? and pid=? and status=1",array(cur_datetime(),$fid,$pid));
		else if($pid==0)
			$this->db->query("update pnh_api_franchise_cart_info set status=0,updated_on=? where franchise_id=? and status=1",array(cur_datetime(),$fid));
		
		if($this->db->affected_rows())
			return true;
		else
			return false;
	}
	
	/**
	 * get the cart items
	 * @param unknown_type $fid
	 * @last_modify Shivaraj_Nov_21_2014 member id, cart qty update
	 */
	function get_cart_items($fid,$pid=0,$mem_id=0)
	{
		$cond='';
		$param=array();
		$param[]=$fid;
		if($pid)
		{
			$cond=' and pid=? ';
			$param[]=$pid;
		}
		
		
		
		$sql="select id as cartid,pid,qty,attributes from pnh_api_franchise_cart_info where franchise_id=? and status=1 $cond";
		
		$cart_item_res=$this->db->query($sql,$param);
		
		if($cart_item_res->num_rows())
		{
			$cart_item_det=$cart_item_res->result_array();
			
			foreach($cart_item_det as $i=>$cart)
			{
				$deal_det=false;
				/*
				//group the attributes
				if($cart['attributes'])
				{
					$attr_details=explode("|",$cart['attributes']);
					
					$attr_group=array();
					foreach($attr_details as $attr)
					{
						$attr_field_det=explode(":",$attr);
						
						if(!isset($attr_group[$attr_field_det[0]]))
							$attr_group[$attr_field_det[0]]=array();
						
						array_push($attr_group[$attr_field_det[0]],$attr);
					}
					
					$cart_item_det[$i]['attributes']=$attr_group;
				}*/
				//get the cart product details
				$deal_det=$this->get_deal($cart['pid'],$fid,$mem_id);
				
				if($deal_det)
				{
					$qty_cng=false;
					$cart_item_det[$i]['deal_deails']=$deal_det;
					
					$price_type_msg='';
					if($deal_det['price_type'])
					{
						$price_type_msg='Price type is Member Price.';
					}
					else
					{
						$price_type_msg='Price type is Offer Price.';
					}
					
					if($cart['qty']>$deal_det['max_ord_qty'])
					{
						
						$cart_item_det[$i]['deal_deails']['max_ord_qty']=$deal_det['max_ord_qty'];
						
						$cart_item_det[$i]['max_ord_qty_prv']=$cart['qty'];
						
						if($deal_det['max_ord_qty'] > 0 )
						{
							$cart_item_det[$i]['max_ord_qty_msg']= $price_type_msg.' '.$cart['qty'].' quantity is reduced to '.($deal_det['max_ord_qty']).' quantity for this member.';
						}
						else
						{
							
							$cart_item_det[$i]['max_ord_qty_msg']= $price_type_msg.' All quantities Sold Out.';
						}
						
						$qty_cng=true;
					}
					elseif($cart['qty']<$deal_det['max_ord_qty']) {
						$cart_item_det[$i]['max_ord_qty_prv']=$cart['qty'];
						$cart_item_det[$i]['max_ord_qty_msg']=$price_type_msg;
					}
					else {
						$cart_item_det[$i]['max_ord_qty_prv']=$cart['qty'];
						$cart_item_det[$i]['max_ord_qty_msg']="";
					}
					
					if($qty_cng)
					{
						$this->db->query("update pnh_api_franchise_cart_info set qty=?,updated_on=now() where id=? limit 1",array($deal_det['max_ord_qty'],$cart['cartid']));
						$cart_item_det[$i]['qty']=$deal_det['max_ord_qty'];
					}
				}
			}
			return $cart_item_det;
			
		}else{
			return false;
		}
	}
	
	/**
	 * function for get the memebers details
	 */
	function get_member_details($memid=0,$mob_no=0)
	{
		$cond=1;
		$param=array();
		
		if($memid)
		{
			$cond.=" and pnh_member_id=?";
			$param[]=$memid;
		}
		
		if($mob_no)
		{
			$cond.=" and mobile=?";
			$param[]=$mob_no;
		}
		
		$sql="select *,DATE_FORMAT(dob,'%d-%m-%Y') AS dob from pnh_member_info where $cond";
		
		$mem_det_res=$this->db->query($sql,$param);
		
		if($mem_det_res->num_rows())
		{
			$member_det=$mem_det_res->result_array();
			/*
			foreach($member_det as $i=> $m)
			{
				$order=$this->db->query("select count(1) as n,sum(amount) as t from king_transactions where transid in (select transid from king_orders where userid=?)",$m['user_id'])->row_array();
				$member_det[$i]['total_orders']=$order['n'];
				$member_det[$i]['total_ordered_amount']=$order['t'];
			}
			*/
			return $member_det;
		}else{
			return false;
		}
	}
	
	
	/**
	 * function to get config parameters from Database
	 */
	function _get_config_param($name)
	{
		return $this->db->query("select value from m_config_params where name = ?  ",$name)->row()->value;
	}
	
	/**
	 * function to set config paramaters based on name and value pair
	 */
	function _set_config_param($name,$value)
	{
		$this->db->query("update m_config_params set value = ? where name = ? limit 1",array($value,$name));
	}
	
	/**
	 * function to generate new member id for dynamic allocation
	 */
	function _gen_uniquememberid()
	{
		$lastmem_id = $this->_get_config_param('LAST_MEMBERID_ALLOTED');
		$member_id = $lastmem_id+1;
		$this->_set_config_param('LAST_MEMBERID_ALLOTED',$member_id);
		// check if member id is already alloted
		if($this->db->query("select count(*) as t from pnh_member_info where pnh_member_id = ? ",$member_id)->row()->t)
			return $this->_gen_uniquememberid();
		else
			return $member_id;
	}
	
	/**
	 * function to add a new member
	 * @param unknown_type $fid
	 * @param unknown_type $member_name
	 * @param unknown_type $member_id
	 * @param unknown_type $mobile_no
	 * @param unknown_type $dob
	 * @param unknown_type $city
	 * @param unknown_type $address
	 * @param unknown_type $pincode 
	 * @param unknown_type $email  
	 */
	function add_new_member($fid,$member_name,$mobile_no,$mem_image='',$dob='',$addr='',$city='',$pincode='',$email='')
	{
		if($this->db->query("select * from pnh_member_info where mobile=?",$mobile_no)->num_rows()==0)
		{
			$membr_id=$this->_gen_uniquememberid();
			
			if(!$member_name)
				$member_name="PNH Member: $membr_id";
			else
				$member_name=$member_name;
			
			$this->db->query("insert into king_users(name,is_pnh,createdon) values(?,1,?)",array($member_name,time()));
			$userid=$this->db->insert_id();
			$this->db->query("insert into pnh_member_info(pnh_member_id,user_id,first_name,last_name,mobile,dob,address,city,pincode,email,franchise_id,created_by,created_on)values(?,?,?,?,?,?,?,?,?,?,?,?,?)",array($membr_id,$userid,$member_name,'',$mobile_no,$dob,$addr,$city,$pincode,$email,$fid,0,time()));
			return $membr_id;
		}else
		{
			return false;
		}
	}
	
	/**
	 * get the member by mobile number
	 * @param unknown_type $fid
	 * @param unknown_type $member_name
	 * @param unknown_type $mobile_no
	 */
	function get_member_by_mob($mobile_no)
	{
		$sql="select * from pnh_member_info where mobile=?";
		
		$mem_res=$this->db->query($sql,$mobile_no);
		
		if($mem_res->num_rows())
			return $mem_res->row_array();
		else
			return false;
	}
	
	
	/**
	 * function for get the transaction details
	 */
	function get_transactions($start,$limit,$fid,$date_from,$date_to)
	{
		$output=array();
		$trans_details=array();
		$cond='';
		$param=array();
		
		$param[]=$fid;
		
		if($date_from && $date_to)
		{
			$cond.=" and c.init between ? and ? ";
			$param[]=strtotime($date_from);
			$param[]=strtotime($date_to);
		}
		
		
		
		$sql="select distinct c.transid,c.batch_enabled,sum(o.i_coup_discount) as com,
						c.amount,o.transid,o.status,o.time,o.actiontime,pu.user_id as userid,pu.pnh_member_id 
				from king_orders o 
				join king_transactions c on o.transid = c.transid 
				join pnh_member_info pu on pu.user_id=o.userid 
				where 1 and c.franchise_id = ? $cond
				group by c.transid  
				order by c.init desc ";
		
		$trans_res=$this->db->query($sql,$param);
		
		if($trans_res->num_rows())
		{
			$output['ttl_trans']=$trans_res->num_rows();
			
			$sql.=" limit $start,$limit";
			
			$trans_list=$this->db->query($sql,$param)->result_array();
			
			foreach($trans_list as $i=>$t)
			{
				if(!isset($trans_details[$t['transid']]))
				{
					$trans_details[$t['transid']]=array();
					$trans_details[$t['transid']]['orders']=array();
				}
				
				$trans_details[$t['transid']]['trans_id']=$t['transid'];
				$trans_details[$t['transid']]['trans_date']=format_datetime(date('Y-m-d H:i:s',$t['time']));
				$trans_details[$t['transid']]['amount']=round($t['amount'],2);
				$trans_details[$t['transid']]['commission']=round($t['com'],2);
				
				
				$sql="select a.member_id,a.status as ord_status,a.transid,e.invoice_no,d.packed,d.shipped,e.invoice_status,d.shipped_on,
							a.status,a.id,a.itemid,b.name,a.quantity,i_orgprice,i_price,i_discount,i_coup_discount 
						from king_orders a
						join king_dealitems b on a.itemid = b.id
						join king_transactions t on t.transid = a.transid   
						left join proforma_invoices c on c.order_id = a.id 
						left join shipment_batch_process_invoice_link d on d.p_invoice_no = c.p_invoice_no 
						left join king_invoice e on e.invoice_no = d.invoice_no and d.packed = 1 and d.shipped = 1
	 				where a.transid = ? 
					order by a.status,b.name;";
				
				$order_prddt_res=$this->db->query($sql,$t['transid']);
				
				$trans_ttl_shipped = 0;
				$trans_ttl_cancelled = 0;
				$trans_ttl_orders=0;
				$processed_oids = array();
				
				if($order_prddt_res->num_rows())
				{
					$order_prddt_res=$order_prddt_res->result_array();
					
					//pepared the ordered products
					foreach($order_prddt_res as $op)
					{
						if(!isset($processed_oids[$op['id']]))
							$processed_oids[$op['id']]=1;
						else 
							continue;
						
						if($op['ord_status'] == 1)
						{
							if($op['shipped'])
								$op['ord_status'] = 2;
							else if($op['invoice_status'])
								$op['ord_status'] = 5;
						}
						
						
						$is_cancelled = ($op['status']==3)?1:0;
						
						if($is_cancelled)
						{
							$trans_ttl_cancelled+=1;//if order cancelled then increase the cancelled count
							
						}
						else
						{
							$op['ord_status']= ($op['shipped'])?2:0;
							if($op['shipped'] && $op['invoice_status'])
								$trans_ttl_shipped += 1;
						}
						
						
						array_push($trans_details[$t['transid']]['orders'],array('order_id'=>$op['id'],'member_id'=>$op['member_id']?$op['member_id']:$t['pnh_member_id'],'deal_name'=>$op['name'],'item_id'=>$op['itemid'],'qty'=>$op['quantity'],'mrp'=>$op['i_orgprice'],'price'=>round($op['i_orgprice']-($op['i_coup_discount']+$op['i_discount']),2),"status"=>$op['ord_status'] ));
					}
					
					$trans_ttl_orders = count($processed_oids);
				}
				
				
				//generate the trasaction stauts;
				if($trans_ttl_orders == $trans_ttl_cancelled)
					$trans_details[$t['transid']]['status']=3;
				else
				{
					if(($trans_ttl_orders-$trans_ttl_cancelled) == $trans_ttl_shipped)
						$trans_details[$t['transid']]['status']=2;
					else if($trans_ttl_shipped)
							$trans_details[$t['transid']]['status']=1;
					else 
						$trans_details[$t['transid']]['status']=0;
					
				}
			
				$trans_details[$t['transid']]['order_status_config']=array('Pending','Pending','Shipped','Cancelled','Returned','Invoiced');
				$trans_details[$t['transid']]['trans_status_config']=array('UnShipped','Partitally Shipped','Shipped','Cancelled');
			}
			
			$output['trans_det']=$trans_details;
			
			return $output;
		}else{
			return false;
		}
	}
	
	/**
	 * 
	 * @param unknown_type $result_liat
	 * @param unknown_type $field_name
	 */
	function update_datetime_format($result_list=array(),$field_names=array())
	{
		if($result_list)
		{
			foreach($result_list as  $i=>$r)
			{
				if($field_names)
				{
					foreach($field_names as $n)
					{
						if($r[$n])
						{
							$is_date=0;
							$is_date=stripos($r[$n],':');
							
							if($is_date>0)
								$r[$n]=strtotime($r[$n]);
							
							$result_list[$i][$n]=date("d/m/y",$r[$n]);
						}
					}
				}
				
				
			}
		}
		
		return $result_list;
	}
	
	/**
	 * function for get the pending receipts by franchise
	 * @param unknown_type $r_start
	 * @param unknown_type $r_limit
	 */
	function get_pending_receipts($fid,$r_start,$r_limit)
	{
		$pending_receipt_det=array('total_receipts'=>'','pending_receipts'=>'','receipts_total_value'=>'');
		$param=array();
		$param[]=$fid;
		
		$sql="SELECT r.*,m.name AS modifiedby,f.franchise_name,a.name AS admin
					FROM pnh_t_receipt_info r
					JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id
					LEFT OUTER JOIN king_admin a ON a.id=r.created_by
					LEFT OUTER JOIN king_admin m ON m.id=r.modified_by
					WHERE r.status=0 AND r.is_active=1 and is_submitted=0 and r.status=0 and r.franchise_id=?
					ORDER BY instrument_date asc";
		
		$receipt_res=$this->db->query($sql,$param);
		
		if($receipt_res->num_rows())
		{
			$pending_receipt_det['total_receipts']=$receipt_res->num_rows();
			
			$sql.=" limit $r_start,$r_limit";
			
			$pending_receipt_det['pending_receipts']=$this->update_datetime_format($this->db->query($sql,$param)->result_array(),array('instrument_date'));
			$pending_receipt_det['receipts_total_value']=$this->db->query("SELECT sum(r.receipt_amount) as total FROM pnh_t_receipt_info r JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id LEFT OUTER JOIN king_admin a ON a.id=r.created_by WHERE r.status=0 AND r.is_active=1  AND is_submitted=0 and r.status=0 and r.franchise_id=?  ORDER BY instrument_date asc",$fid)->row_array();
		}
		
		return $pending_receipt_det;
	}
	
	/**
	 * function for get the processed receipts
	 * @param unknown_type $fid
	 * @param unknown_type $r_start
	 * @param unknown_type $r_limit
	 */
	function get_processed_receipts($fid,$r_start,$r_limit)
	{
		$processed_receipt_det=array('total_receipts'=>'','processed_receipts'=>'','receipts_total_value'=>'');
		$param=array();
		$param[]=$fid;
		
		$sql="SELECT r.*,b.bank_name AS submit_bankname,s.name AS submittedby,a.name AS admin,f.franchise_name,d.remarks AS submittedremarks,DATE(d.submitted_on) AS submitted_on
					FROM pnh_t_receipt_info r
					LEFT JOIN `pnh_m_deposited_receipts`d ON d.receipt_id=r.receipt_id
					LEFT JOIN `pnh_m_bank_info` b ON b.id=d.bank_id
					LEFT JOIN king_admin s ON s.id=d.submitted_by
					JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id
					left outer join king_admin a on a.id=r.created_by
					WHERE r.is_submitted=1 AND r.status=0  and r.is_active=1 and r.franchise_id=?
					group by r.receipt_id
					order by d.submitted_on desc";
		
		$receipt_res=$this->db->query($sql,$param);
		
		if($receipt_res->num_rows())
		{
			$processed_receipt_det['total_receipts']=$receipt_res->num_rows();
		
			$sql.=" limit $r_start,$r_limit";
		
			$processed_receipt_det['processed_receipts']=$this->update_datetime_format($this->db->query($sql,$param)->result_array(),array('instrument_date','submitted_on'));
			$processed_receipt_det['receipts_total_value']=$this->db->query("SELECT sum(r.receipt_amount) as total FROM pnh_t_receipt_info r LEFT JOIN `pnh_m_deposited_receipts`d ON d.receipt_id=r.receipt_id LEFT JOIN `pnh_m_bank_info` b ON b.id=d.bank_id LEFT JOIN king_admin s ON s.id=d.submitted_by JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id left outer join king_admin a on a.id=r.created_by WHERE  r.is_submitted=1 AND r.status=0  and r.is_active=1 and r.franchise_id=?  order by d.submitted_on desc",$fid)->row_array();
		}
		
		return $processed_receipt_det;
	}
	
	/**
	 * function for get the realized receipts
	 * @param unknown_type $fid
	 * @param unknown_type $r_start
	 * @param unknown_type $r_limit
	 */
	function get_realized_receipts($fid,$r_start,$r_limit)
	{
		$realized_receipt_det=array('total_receipts'=>'','realized_receipts'=>'','receipts_total_value'=>'');
		$param=array();
		$param[]=$fid;
		
		$sql="SELECT r.*,f.franchise_name,a.name AS admin,d.username AS activated_by
					FROM pnh_t_receipt_info r
					JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id
					LEFT OUTER JOIN king_admin a ON a.id=r.created_by
					LEFT OUTER JOIN king_admin d ON d.id=r.activated_by
					WHERE r.status=1 AND r.is_active=1 AND (is_submitted=1 or r.activated_on!=0) and r.is_active=1 and r.franchise_id=?
					group by r.receipt_id
					ORDER BY activated_on desc";
		
		$receipt_res=$this->db->query($sql,$param);
		
		if($receipt_res->num_rows())
		{
			$realized_receipt_det['total_receipts']=$receipt_res->num_rows();
		
			$sql.=" limit $r_start,$r_limit";
		
			$realized_receipt_det['realized_receipts']=$this->update_datetime_format($this->db->query($sql,$param)->result_array(),array('instrument_date','activated_on'));
			$realized_receipt_det['receipts_total_value']=$this->db->query("SELECT sum(r.receipt_amount) as total FROM pnh_t_receipt_info r JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id LEFT OUTER JOIN king_admin a ON a.id=r.created_by LEFT OUTER JOIN king_admin d ON d.id=r.activated_by WHERE r.status=1 AND r.is_active=1 AND (is_submitted=1 or r.activated_on!=0) and r.is_active=1 and r.franchise_id=? ORDER BY activated_on desc",$fid)->row_array();
		}
		
		return $realized_receipt_det;
	}
	
	/**
	 * function for get the cancelled receipts
	 * @param unknown_type $fid
	 * @param unknown_type $r_start
	 * @param unknown_type $r_limit
	 */
	function get_cancelled_receipts($fid,$r_start,$r_limit)
	{
		$cancelled_receipt_det=array('total_receipts'=>'','cancelled_receipts'=>'','receipts_total_value'=>'');
		$param=array();
		$param[]=$fid;
		
		$sql="SELECT r.*,f.franchise_name,a.name AS admin,d.username AS activated_by ,c.cancel_reason,c.cancelled_on
						FROM pnh_t_receipt_info r
						JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id
						left JOIN `pnh_m_deposited_receipts`c ON c.receipt_id=r.receipt_id
						LEFT OUTER JOIN king_admin a ON a.id=r.created_by
						LEFT OUTER JOIN king_admin d ON d.id=r.activated_by
						WHERE r.status in (2,3) AND r.is_active=1 AND r.is_active=1 AND  r.franchise_id=?
						group by r.receipt_id
						ORDER BY cancelled_on DESC";
		
		$receipt_res=$this->db->query($sql,$param);
		
		if($receipt_res->num_rows())
		{
			$realized_receipt_det['total_receipts']=$receipt_res->num_rows();
		
			$sql.=" limit $r_start,$r_limit";
		
			$cancelled_receipt_det['cancelled_receipts']=$this->update_datetime_format($this->db->query($sql,$param)->result_array(),array('instrument_date','cancelled_on'));
			$cancelled_receipt_det['receipts_total_value']=$this->db->query("SELECT sum(r.receipt_amount) as total FROM pnh_t_receipt_info r JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id left JOIN `pnh_m_deposited_receipts`c ON c.receipt_id=r.receipt_id LEFT OUTER JOIN king_admin a ON a.id=r.created_by LEFT OUTER JOIN king_admin d ON d.id=r.activated_by WHERE r.status in (2,3) AND r.is_active=1 AND r.is_active=1 AND  r.franchise_id=? ORDER BY cancelled_on DESC",$fid)->row_array();
		}
		
		return $cancelled_receipt_det;
		
	}
	
	/**
	 * get voucher details by member id and voucher code
	 * @param unknown_type $mem_id
	 * @param unknown_type $vcode
	 * @return boolean
	 */
	function get_voucher_det_by_mem($mem_id,$vcode)
	{
		$cond='';
		$param=array();
		
		$param[]=$mem_id;
		
		if($vcode)
		{
			$cond.=" and voucher_code=? ";
			$param[]=$vcode;
		}
		
		$sql="select b.book_id,d.franchise_id,a.voucher_serial_no,a.customer_value,a.status as voucher_status
					from pnh_t_voucher_details a
					join pnh_t_book_voucher_link b on b.voucher_slno_id = a.id
					join pnh_t_book_allotment d on d.book_id=b.book_id
					where 1  and member_id=?  and is_activated=1 $cond
					group by voucher_serial_no";
		
		$voucher_det_res=$this->db->query($sql,$param);
		
		if($voucher_det_res->num_rows())
			return $voucher_det_res->row_array();
		else
			return false;
		
	}
	
	/**
	 * function for check the imei no
	 * @param unknown_type $imei_no
	 */
	function check_imei_scheme($imei_no)
	{
		// update imei_scheme details for alloted imei if not set from config
		$imei_sch_res = $this->db->query("select imei_schid,imei_no,imei_order_id,dmenuid,smenuid,dcatid,scatid,dbrandid,sbrandid,if(scheme_type,landprice*credit_value/100,credit_value) as cr_amt from (
											select g.id as imei_schid,b.id as imei_order_id,imei_no,d.menuid as dmenuid,d.catid as dcatid,d.brandid as dbrandid,
												g.menuid as smenuid,g.categoryid as scatid,g.brandid as sbrandid,scheme_type,credit_value,(b.i_orgprice-(b.i_discount+b.i_coup_discount)) as landprice
												from t_imei_no a
												join king_orders b on a.order_id = b.id
												join king_dealitems c on c.id = b.itemid
												join king_deals d on d.dealid = c.dealid
												join king_transactions e on e.transid = b.transid
												join pnh_m_franchise_info f on f.franchise_id = e.franchise_id
												join imei_m_scheme g on g.franchise_id = e.franchise_id and g.menuid = d.menuid
												and d.catid = if(g.categoryid,g.categoryid,d.catid)
												and d.brandid = if(g.brandid,g.brandid,d.brandid)
												and b.time between scheme_from and scheme_to and g.is_active = 1
												where b.imei_scheme_id = 0 and imei_no = ? and b.time > unix_timestamp('2013-10-10')
												and b.status in (1,2)
												group by a.id
											) as g
										;",$imei_no);
		if($imei_sch_res->num_rows())
		{
			$imei_sch_det = $imei_sch_res->row_array();
		
			$this->db->query("update king_orders set imei_scheme_id = ?,imei_reimbursement_value_perunit=? where id = ? limit 1 ",array($imei_sch_det['imei_schid'],$imei_sch_det['cr_amt'],$imei_sch_det['imei_order_id']));
			
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * function for get the imei no details by given imei
	 * @param unknown_type $imei_no
	 */
	function get_imeidet($imei_no)
	{
	
		$sql = "select b.userid,f.invoice_no,product_name,b.transid,c.franchise_id,franchise_name,e.name,a.imei_no,b.status,imei_scheme_id,imei_reimbursement_value_perunit,member_id,date(from_unixtime(c.init)) as ordered_on,a.is_imei_activated
					from t_imei_no a 
					join king_orders b on a.order_id = b.id  
					join king_transactions c on b.transid = c.transid 
					join pnh_m_franchise_info d on d.franchise_id = c.franchise_id 
					join king_dealitems e on e.id = b.itemid 
					join king_invoice f on f.order_id = a.order_id 
					join m_product_info g on g.product_id = a.product_id  
					where a.imei_no = ? ";
								
		$res = $this->db->query($sql,$imei_no);
		
		if($res->num_rows())
		{
			$imei_det = $res->row_array();
			if($imei_det['member_id'] == 0)
			{
				$mem_det= $this->db->query("select pnh_member_id,mobile from pnh_member_info where user_id = ? ",$imei_det['userid'])->row_array();
				$imei_det['member_id']=$mem_det['pnh_member_id'];
				$imei_det['mobileno']=$mem_det['mobile'];
			}
			
			return $imei_det; 
		}else
			return false;
			
	}
	
	/**
	 * function to get the member pending imei activations
	 */
	function get_mem_pending_imei_act_det($mobile_no)
	{
		$process_total_imei = MAX_MEMBER_IMEI_ACTIVATIONS;
		
		// get no of days passed from last activation
		$last_actv_res = $this->db->query("select datediff(curdate(),b.imei_activated_on) as d
												from t_imei_no b
												join pnh_member_info c on c.pnh_member_id = b.activated_member_id
												where c.mobile = ? and is_imei_activated = 1
												order by imei_activated_on desc
												limit ".MAX_MEMBER_IMEI_ACTIVATIONS,$mobile_no);
		
		if($last_actv_res->num_rows())
		{
			foreach($last_actv_res->result_array() as $last_actv)
			{
				if($last_actv['d'] < BLOCK_MEMBER_IMEI_ACTIVATION_DAYS)
				{
					$process_total_imei--;
				}
			}
			
			return $process_total_imei;
		}else{
			return false;
		}
	}
	
	/**
	 * function for update query for imei activation
	 */
	function update_imei_activation($mobno,$member_id,$user,$imeino)
	{
		$sql="update t_imei_no set 
					activated_mob_no=?,activated_member_id=?,activated_by=?,is_imei_activated = 1,imei_activated_on = ?
				where is_imei_activated = 0 and status = 1 and imei_no = ? limit 1  ";
		
		$this->db->query($sql,array($mobno,$member_id,$user,cur_datetime(),$imeino));
		
		if($this->db->affected_rows())
			return true;
		else 
			return false;
	}
	
	/**
	 * function for to insert the credit notes
	 * @param unknown_type $cred_dt
	 */
	function insert_credit_notes($cred_dt=array())
	{
		if(!empty($cred_dt))
		{
			$this->db->query("insert into t_invoice_credit_notes (franchise_id,type,ref_id,invoice_no,amount,created_on,created_by) values(?,2,?,?,?,?,?)",$cred_dt);
		
			if($this->db->affected_rows())
				return true;
			else
				return false;
			
		}else{
			return false;
		}
	}
	
	/**
	 * function for insert the franchise complaints
	 */
	function insert_franchise_complaints($cmp_name='',$cmp='',$fid=0,$cr_by=0)
	{
		if(!$fid)
			return false;
		
		$ins=array("franchise_id"=>$fid,"name"=>$cmp_name,"complaint"=>$cmp,"created_by"=>$cr_by,"created_on"=>cur_datetime());
		
		$this->db->insert("pnh_api_franchise_compaints",$ins);
		
		if($this->db->affected_rows())
			return true;
		else
			return false;
	}
	
	/**
	 * function for get offers
	 */
	function get_offers($fid,$start,$limit)
	{
		if(!$fid)
			return false;
		
		$output=array("ttl_offers"=>0,"offers"=>'');
		$param=array($fid,$start,$limit);
		
		$sql="select a.offer_text,b.name as menu,c.name as brand,d.name as category,
						from_unixtime(offer_start) as offer_start ,from_unixtime(offer_end) as offer_end,a.is_active 
				from pnh_m_offers a 
				join pnh_menu b on b.id=a.menu_id and b.status=1
				left join king_brands c on c.id=a.brand_id
				left join king_categories d on d.id=a.cat_id
				where 1 and a.franchise_id=?
				order by a.created_on desc";
		
		$offer_res=$this->db->query($sql,array($fid));
		
		if($offer_res->num_rows())
		{
			$output['ttl_offers']=$offer_res->num_rows();
			
			$sql.=" limit ?,?";
			
			$offer_res2=$this->db->query($sql,$param);
			
			if($offer_res2->num_rows())
			{
				$output['offers']=$offer_res2->result_array();
			}
			
			return $output;
		}else{
			return $output;
		}
	}
	
	/**
	 * function for get the return details
	 * @last_modified roopashree<roopashree@storeking_in>
	 */
	function get_inv_returns_details($fid,$returnid)
	{
		$output=array("return_details"=>'',"ttl_returns"=>0);
		$param=array($returnid,$fid);
		
		$sql="select c.transid,e.franchise_id,e.franchise_name,a.return_id,a.invoice_no,return_by,returned_on,
					b.name as handled_by_name,a.status,a.order_from,d.partner_id  
					from pnh_invoice_returns a 
					left join king_admin b on a.handled_by = b.id 
					join king_invoice c on c.invoice_no = a.invoice_no
					join king_transactions d on d.transid = c.transid
					left join pnh_m_franchise_info e on e.franchise_id = d.franchise_id
					where 1 and a.return_id=? and e.franchise_id=?
					order by returned_on desc";
		
		$return_det_res=$this->db->query($sql,$param);
		//echo $this->db->last_query();exit;
		if($return_det_res->num_rows())
		{
			$output['ttl_returns']=$return_det_res->num_rows();
			
		//	$sql.=" limit ?,? ";
			
			$return_det_res2=$this->db->query($sql,$param);
			
			if($return_det_res2->num_rows())
			{
				$return_det=$return_det_res2->result_array();
				$output['return_details']=array();
				foreach($return_det as $i=>$d)
				{
					if(!isset($output['return_details'][$i]))
						$output['return_details'][$i]=array();
					
					$output['return_details'][$i]['return_id']=$d['return_id'];
					$output['return_details'][$i]['transid']=$d['transid'];
					$output['return_details'][$i]['invoice_no']=$d['invoice_no'];
					$output['return_details'][$i]['returned_date']=format_datetime($d['returned_on']);
					$output['return_details'][$i]['status']=$d['status'];
					$output['return_details'][$i]['status_config']=$this->config->item('return_request_cond');
					
					//get the returned prd details
					$sql="select a.is_packed,a.readytoship,d.franchise_id,e.franchise_name,IF(a.is_refunded=0,'No',IF(a.is_refunded=1,'Yes','na')) AS is_refunded,IF(a.is_shipped=0,'No',IF(a.is_shipped=1,'Yes','na')) AS is_shipped,IF(a.is_stocked=0,'No',IF(a.is_stocked=1,'Yes','na')) AS is_stocked,
															a.id as return_product_id,a.return_id,a.order_id,a.product_id,
															b.product_name,qty,a.barcode,a.imei_no,IF(condition_type=1,'Good condition',IF(condition_type=2,'Duplicate product',IF(condition_type=3,'UnOrdered Product',IF(condition_type=4,'Late Shipment',IF(condition_type=6,'Faulty and needs service','na'))))) AS condition_type,
															IF(a.status=0,'pending',IF(a.status=1,'updated',IF(a.status=2,'closed','na'))) AS status,transit_mode,courier,awb,emp_phno,emp_name,logged_by 
															from pnh_invoice_returns_product_link a
															join pnh_invoice_returns f on f.return_id = a.return_id  
															join m_product_info b on a.product_id = b.product_id 
															join king_invoice c on c.invoice_no = f.invoice_no
															join king_transactions d on d.transid = c.transid
															LEFT JOIN pnh_t_returns_transit_log t ON t.return_id=a.return_id
															left join pnh_m_franchise_info e on e.franchise_id = d.franchise_id  
															where a.return_id = ?
															group by a.id";
					
					$return_remarks_update_res=$this->db->query("SELECT IF(product_status=0,'Pending',IF(product_status=1,'Out for Service',IF(product_status=2,'Move to Warehouse Stock',IF(product_status=3,'Ready to ship',IF(product_status=4,' Return From Service','na'))))) AS product_status,
																	p.return_id,DATE_FORMAT(r.created_on,'%d/%m/%Y %h:%i %p') AS created_on,r.remarks,transit_mode,courier,awb,emp_phno,emp_name,logged_by,IF(p.is_refunded=0,'No',IF(p.is_refunded=1,'Yes','na')) AS is_refunded,IF(p.is_shipped=0,'No',IF(p.is_shipped=1,'Yes','na')) AS is_shipped,
																	IF(p.is_stocked=0,'No',IF(p.is_stocked=1,'Yes','na')) AS is_stocked
																	FROM  pnh_invoice_returns_remarks r
																	JOIN pnh_invoice_returns_product_link p ON p.id=r.return_prod_id
																	LEFT JOIN pnh_t_returns_transit_log t ON t.return_id=p.return_id
																	WHERE p.return_id=?
																	ORDER BY r.created_on desc",$d['return_id']);
					
					$output['return_details'][$i]['return_remarks_update_res']=$return_remarks_update_res->result_array();
						//
			//		$sql.=" limit $start,$limit";
					$output['return_details'][$i]['products']=$this->db->query($sql,$d['return_id'])->result_array();
					//echo $this->db->last_query();exit;
					$output['return_details'][$i]['prd_status_config']=$this->config->item('return_process_cond');
					
				}
			}
			
		}
		
		return $output;
		
	}
	
	/**
	 * function for get the return details
	 */
	function get_pnh_invreturns($fid,$start,$limit)
	{
		$output=array("return_details"=>'',"ttl_returns"=>0);
		$param=array($fid,$start*1,$limit*1);
		
		$sql="select c.transid,e.franchise_id,e.franchise_name,a.return_id,a.invoice_no,return_by,returned_on,
					b.name as handled_by_name,a.status,a.order_from,d.partner_id  
					from pnh_invoice_returns a 
					left join king_admin b on a.handled_by = b.id 
					join king_invoice c on c.invoice_no = a.invoice_no
					join king_transactions d on d.transid = c.transid
					left join pnh_m_franchise_info e on e.franchise_id = d.franchise_id
					where 1 and e.franchise_id=? group by a.return_id
					order by a.return_id desc";
		
		$return_det_res=$this->db->query($sql,$param);
		
		if($return_det_res->num_rows())
		{
			$output['ttl_returns']=$return_det_res->num_rows();
			
			$sql.=" limit ?,? ";
			
			$return_det_res2=$this->db->query($sql,$param);
			
			if($return_det_res2->num_rows())
			{
				$return_det=$return_det_res2->result_array();
				$output['return_details']=array();
				foreach($return_det as $i=>$d)
				{
					if(!isset($output['return_details'][$d['return_id']]))
						$output['return_details'][$i]=array();
					
					$output['return_details'][$i]['return_id']=$d['return_id'];
					$output['return_details'][$i]['transid']=$d['transid'];
					$output['return_details'][$i]['invoice_no']=$d['invoice_no'];
					$output['return_details'][$i]['returned_date']=format_datetime($d['returned_on']);
					$output['return_details'][$i]['status']=$d['status'];
					$output['return_details'][$i]['status_config']=$this->config->item('return_request_cond');
					
					//get the returned prd details
					$sql="select a.is_packed,a.readytoship,d.franchise_id,e.franchise_name,a.is_refunded,a.is_shipped,a.is_stocked,
															a.id as return_product_id,a.return_id,a.order_id,a.product_id,
															b.product_name,qty,a.barcode,a.imei_no,condition_type,a.status 
														from pnh_invoice_returns_product_link a
														join pnh_invoice_returns f on f.return_id = a.return_id  
														join m_product_info b on a.product_id = b.product_id 
														join king_invoice c on c.invoice_no = f.invoice_no
														join king_transactions d on d.transid = c.transid
														left join pnh_m_franchise_info e on e.franchise_id = d.franchise_id  
														where a.return_id = ?
														group by a.id;";
					
					$output['return_details'][$i]['products']=$this->db->query($sql,$d['return_id'])->result_array();
					$output['return_details'][$i]['prd_status_config']=$this->config->item('return_process_cond');
				}
			}
			
		}
		return $output;
		
	}
	
	/**
	 * function for get the new update verions
	 */
	function get_new_update_versions($version)
	{
		$output=array('verions'=>array(),'error'=>'');
		
		$v_id_res=$this->db->query("select id,release_version,code_version,db_version from m_apk_version where version=? and version like ?",array($version,'%'.$version.'%'));	
		
		if($v_id_res->num_rows())
		{
			$v_id=$v_id_res->row_array();

			$sql="select id,version,created_on from m_apk_version 
						where id > ? and release_version=?  order by id asc";
			
			$versions_res=$this->db->query($sql,array($v_id['id'],$v_id['release_version']));

			if($versions_res->num_rows())
				$output['verions']=$versions_res->result_array();
				
			return $output;
			
		}else{
			return $output['error']=1;
		}
	}
	
	/**
	 * function to get total update for catgory and menu
	 * @last_modified by roopashree<roopashree@storeking.in>
	 */
	function get_ttlupdate_catmenu($versions=array(),$update_flag=0)
	{
		$output=array();
		$version_ids=array();
		if($versions)
		{
			$latest_vesrion_det=end($versions);
			$version_detatils=array("version"=>'',"created_on"=>'',"latest_version"=>'',"products"=>'');
			$updates=array();

			foreach($versions as $v)
				array_push($version_ids,$v['id']);

			$version_detatils["latest_version"]=$latest_vesrion_det['version'];
			$version_detatils["created_on"]=$latest_vesrion_det["created_on"];
			$cond = '';
			
			if($update_flag==2)
				$cond.=' and is_publish = 0 ';
			else
				$cond.=' and i.price != h.price ';

			$menu_cat_res=$this->db->query("
											select is_publish,menuid,m.name as menu_name,catid,h.mrp,h.price,c.name as cat_name,count(*) as ttl 
												from (
															select *,max(new_stat) as is_new 
																from 
																(
														select b.id,a.version,b.item_id,b.mrp,b.price,b.is_publish,b.is_new as new_stat
															from m_apk_version a
															join m_apk_version_deal_link b on a.id=b.version_id
															where b.version_id in (".implode(",",$version_ids).") 						
															order by b.id desc
														) as g
														group by g.item_id,g.id
												) as h 
											JOIN king_dealitems i ON i.id=h.item_id
											JOIN king_deals d ON d.dealid=i.dealid
											join pnh_menu m on m.id = d.menuid
											join king_categories c on c.id = d.catid
											where 1 $cond 
											group by menuid,catid 
											order by menu_name,cat_name
										") or die(mysql_error());
			$menu_cat_list = array();
			if($menu_cat_res->num_rows())
			{
				foreach ($menu_cat_res->result_array() as $mc_det)
				{
					$mc_mid = $mc_det['menuid'];
					$mc_cid = $mc_det['catid'];
					if(!isset($menu_cat_list[$mc_mid]))
						$menu_cat_list[$mc_mid] = array('id'=>$mc_det['menuid'],'name'=>$mc_det['menu_name'],'total'=>0,'cat_list'=>array());
					
					$menu_cat_list[$mc_mid]['total']+=$mc_det['ttl'];
					
					if(!isset($menu_cat_list[$mc_mid]['cat_list'][$mc_cid]))
						$menu_cat_list[$mc_mid]['cat_list'][$mc_cid] = array('id'=>$mc_det['catid'],'name'=>$mc_det['cat_name'],'total'=>$mc_det['ttl']);
					
				}
			}
			
			$output['mc'] = $menu_cat_list;
			
			/* 
			det => menu - id - 100 
			            - name - beauty
			            - total - 30 
			            - cat - 1 - id 
			            		1 - name
			            		1 - total  */
			            		
			
			/*

			$menu_det=$this->db->query("select *,max(new_stat) as is_new from (
					select b.id,a.version,b.item_id,b.mrp,b.price,b.is_publish,b.is_new as new_stat,d.catid,d.menuid,m.name as menuname
					from m_apk_version a
					join m_apk_version_deal_link b on a.id=b.version_id
					JOIN king_dealitems i ON i.id=b.item_id
					JOIN king_deals d ON d.dealid=i.dealid
					join pnh_menu m on m.id=d.menuid
					where b.version_id in (".implode(",",$version_ids).")
					order by b.id desc
			) as g
					group by menuid
					order by id asc");

			if($menu_det)
			{
				foreach ($menu_det->result_array() as $m)
				{
					$catres=$this->db->query("select catid,c.name as catname from king_deals d join king_categories c on c.id=d.catid where menuid=?",$m['menuid']);
					if($catres->num_rows())
					{
						foreach($catres->result_array() as $c)
						{
							if(!isset($updates[$m['menuid']][$m['menuname']][$c['catid']]))
							{
								$cat_id=$c['catid'];
								$menu_id=$m['menuid'];
								$sql="select *,max(new_stat) as is_new from (
										select b.id,a.version,b.item_id,b.mrp,b.price,b.is_publish,b.is_new as new_stat,d.catid,d.menuid
										from m_apk_version a
										join m_apk_version_deal_link b on a.id=b.version_id
										JOIN king_dealitems i ON i.id=b.item_id
										JOIN king_deals d ON d.dealid=i.dealid
										where b.version_id in (".implode(",",$version_ids).") and d.catid=? and d.menuid=?
												order by b.id desc
												) as g
												group by g.item_id,g.id
												order by id asc";

								$deals_res=$this->db->query($sql,array($cat_id,$menu_id));
									
								$ttl_updates=$deals_res->num_rows();
									
								if($ttl_updates)
								{
									$updates[$m['menuid']][$m['menuname']][$c['catid']]=array();
								$updates[$m['menuid']][$m['menuname']][$c['catid']]=array("catid"=>$c['catid'],"catname"=>$c['catname'],"updates"=>$ttl_updates);
							}

						}

					}
					}
				}
			}
			
			*/

		}

		return $output;
	}

	/**
	 * function to get updated deal 
	 * @param unknown_type $versions
	 * @param unknown_type $menuid
	 * @param unknown_type $catid
	 * @param unknown_type $brandid
	 * @param unknown_type $update_flag
	 * @author roopashree<roopashree@storeking.in>
	 */
	function get_updated_deals($versions=array(),$menuid,$catid,$brandid,$update_flag,$fid)
	{
		 
		$output=array();
		$version_ids=array();
		$param='';
		$cond='';
		$select_cond='';
		if($menuid!=0)
			$param.="and menuid=$menuid";
		if($catid!=0)
			$param.=" and catid in ($catid)";
		if($brandid!=0)
			$param.=" and brandid in ($brandid)";
		
		if($update_flag==1)
			$cond.=' and price != pprice  ';
		if($update_flag==3)
			$cond.=' and is_new=1';
		if($update_flag==2)
			$cond.=' and is_publish = 0 ';
		if($update_flag==3)
			$cond.=' and is_new=1';
		
		$price_type=$this->erpm->get_fran_pricetype($fid);
	//	echo $this->db->last_query();exit;
		if($price_type==1)
			$select_cond='b.member_price';
		else
			$select_cond='b.price';
		if($versions)
		{
			$latest_vesrion_det=end($versions);
			$version_detatils=array("version"=>'',"created_on"=>'',"latest_version"=>'',"products"=>'');
			$updates=array();
		
			foreach($versions as $v)
				array_push($version_ids,$v['id']);
			
			$sql="SELECT *,MAX(new_stat) AS is_new FROM (
					SELECT l.id,k.version,l.item_id,l.mrp,l.price,l.is_publish,l.is_new AS new_stat,b.pnh_id AS pid,menuid AS pmenu_id,e.name AS pmenu,b.name AS pname,catid AS pcat_id,c.name AS pcat,
					cp.id AS parent_cat_id,cp.name AS parent_cat,brandid AS pbrand_id,d.name AS pbrand,
					b.orgprice AS pmrp,$select_cond AS pprice,publish AS is_sourceable,
					b.gender_attr,b.url,b.is_combo,a.description AS pdesc,a.keywords AS kwds,
					a.pic AS pimg,menuid AS pimg_path,b.shipsin AS shipin
					FROM m_apk_version k
					JOIN m_apk_version_deal_link l ON k.id=l.version_id
					JOIN king_dealitems b ON b.id = l.item_id
					JOIN king_deals a ON a.dealid=b.dealid
					JOIN king_categories c ON c.id = a.catid
					LEFT JOIN king_categories cp ON cp.id = c.type
					JOIN king_brands d ON d.id = a.brandid
					JOIN pnh_menu e ON e.id = a.menuid
					WHERE l.version_id IN (".implode(",",$version_ids).") $param 
					ORDER BY l.id DESC
					) AS g
					where 1 $cond 
					GROUP BY g.item_id,g.id
					ORDER BY id ASC";
			
			$updated_deals_res=$this->db->query($sql);
				
			if($updated_deals_res)
			{
				$version_detatils['ttl_updated_deals']=$updated_deals_res->num_rows();
				$version_detatils['updated_deals']=$updated_deals_res->result_array();
				
				$deal_list = array();
				foreach($updated_deals_res->result_array() as $i=>$row)
				{
					$tmp = array();
					$tmp = $row;

					// if deal is new then fetch deal name,mp,price,status,image,cat,parent,parent_linked_cats
					//if($tmp['is_new'])
					if(1)
					{
						$res_d = $this->db->query("select b.id as item_id,b.pnh_id as pid,menuid as pmenu_id,e.name as pmenu,b.name as pname,catid as pcat_id,c.name as pcat,
													cp.id as parent_cat_id,cp.name as parent_cat,brandid as pbrand_id,d.name as pbrand,
													b.orgprice as pmrp,$select_cond AS pprice,publish as is_sourceable,
													b.gender_attr,b.url,b.is_combo,a.description as pdesc,a.keywords as kwds,
													a.pic as pimg,menuid as pimg_path,b.shipsin as shipin
													from king_deals a
													join king_dealitems b on a.dealid = b.dealid
													join king_categories c on c.id = a.catid
													left join king_categories cp on cp.id = c.type
													join king_brands d on d.id = a.brandid
													join pnh_menu e on e.id = a.menuid
														where b.id = ? and b.pnh_id != 0 
													",$row['item_id']);
						if($res_d->num_rows())
						{
							$row_d = $res_d->row_array();
							
							
							foreach($row_d as $k1=>$v1)
								$tmp[$k1] = htmlspecialchars($v1);

							$tmp['pimages'] = @$this->db->query("select group_concat(distinct id) as images from king_resources where itemid = ? order by id desc",$row['item_id'])->row()->images;
							$tmp['attributes'] = "";
							// get deal attributes if available

							$sql_a = "select group_concat(a SEPARATOR '||') as attrs
									from (
									(
									select l.itemid,p.product_id,concat(group_concat(concat(a.attribute_name,':',v.attribute_value)),',ProductID:',p.product_id) as a
									from m_product_group_deal_link l
									join products_group_pids p on p.group_id=l.group_id
									join products_group_attributes a on a.attribute_name_id=p.attribute_name_id
									join products_group_attribute_values v on v.attribute_value_id=p.attribute_value_id
									where l.itemid=?
									group by p.product_id
									)
									union
									(
									select a.itemid,a.product_id,concat(group_concat(concat(attr_name,':',attr_value) order by f.id desc ),',ProductID:',a.product_id) as a
									from m_product_deal_link a
									join king_dealitems b on a.itemid = b.id
									join king_deals c on c.dealid = b.dealid
									join m_product_info d on d.product_id = a.product_id
									join m_product_attributes e on e.pid = d.product_id
									join m_attributes f on f.id = e.attr_id
									where b.is_group = 1 and a.itemid = ? and a.is_active = 1
									group by a.product_id
									)
									) as g ";
							$attr_res = $this->db->query($sql_a,array($row['item_id'],$row['item_id']));
							if($attr_res->num_rows())
							{
								$tmp['attributes'] = @$attr_res->row()->attrs;
							}

							$tmp['attributes'] = (is_null($tmp['attributes'])?'':$tmp['attributes']);
							$tmp['parent_cat_id'] = $tmp['parent_cat_id']*1;
							$tmp['parent_cat'] = (String)$tmp['parent_cat'];
						}
					}
					$deal_list[] = $tmp;
				}
				
				$this->db->query("insert into m_apk_version_update_log(franchise_id,menu_id,brand_id,cat_id,updated_version,updated_on)values(?,?,?,?,?,now())",array($fid,$menuid,$brandid,$catid,$latest_vesrion_det['version']));
				$version_detatils["products"]=$deal_list;
				
			}
			array_push($output,$version_detatils);
		}
		return($version_detatils);
	}


	/**
	 * Function to build content update versions.
	**/
	function cron_build_app_updates()
	{
		// 1.1.1.4
		// 2.1.1.2

		// store menu link beauty - menus  - 100,103,106
		// check for price  updates and status updates in existing deals
		// check for new deals added to menu 
		$sql = "select * from (
					select id,release_version,db_version,db_changes_v
						from m_apk_version 
						order by id desc 
					) as g
					group by release_version";
		$res = $this->db->query($sql);
		if(!$res->num_rows())
			die();

		// Iterate each release latest versions
		foreach($res->result_array() as $row_v)
		{
			$store_id = $row_v['release_version'];
			$db_version = $row_v['db_version'];
			$db_changes_v = $row_v['db_changes_v'];

			//get store linked menus.
			$res_m = $this->db->query("select menu_id from m_apk_store_menu_link a where store_id = ? ",$row_v['release_version']);
			if($res_m->num_rows())
			{
				foreach($res_m->result_array() as $row_m)
				{
					// get menu version deals for price updates and status updates.
					$linked_deals_res = $this->db->query("select * 
																from (
																	select a.id,a.item_id,a.mrp,a.price,a.is_publish,a.is_new,a.created_on
																	from m_apk_version_deal_link a 
																	join m_apk_version b on a.version_id = b.id 
																	where b.release_version = ?  
																order by a.id desc ) as g
															group by g.item_id",$res_m);
					 
					
				}
			}

		}


	}
	
	function get_menudet($mid)
	{
		return $this->db->query("select * from pnh_menu where id=?",$mid)->row_array();
		
	}
	
	function get_catdet($mid)
	{
		return $this->db->query("select * from king_categories a join king_deals b on b.catid=a.id where a.id=?",$mid)->row_array();
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
	function get_memberorders_ttl($mid)
	{
		return $this->db->query("select count(distinct transid) as t from king_orders a join pnh_member_info b on a.userid = b.user_id where b.pnh_member_id = ? ",array($mid))->row()->t;
	}
	
	/**
	 * Function to update old password with new password & send sms to franchise
	 * @param type $rnd_key string
	 * @param type $user_id string
	 * @author Shivaraj <shivaraj@storeking.in>
	 * @return boolean true/false
	 */
	function update_password($user_id)
	{
		$fid = $this->db->query("select reference_id from pnh_users where user_id = ? ",$user_id)->row()->reference_id;
		
		if($fid == 498)
			$rnd_key = 7729227;
		else
			$rnd_key = random_num(7);  //lcfirst(random_alpha(1)).

		if($this->_chk_pwd_exists($rnd_key,$user_id))
		{
			$this->update_password($user_id);
		}
		else
		{
			$this->db->query("UPDATE `pnh_users` SET `password` = MD5(?) WHERE `user_id` = ?",array($rnd_key,$user_id));

			if( $this->db->affected_rows() ){
				$sql="SELECT f.franchise_id,f.pnh_franchise_id,f.franchise_name,f.login_mobile1,f.address,f.city,f.postcode,f.state,u.user_id
						FROM pnh_m_franchise_info f 
						JOIN pnh_users u ON u.reference_id=f.franchise_id
						WHERE u.user_id=?";
				$fran_det_res=$this->db->query($sql,array($user_id));
				$fran_det = $fran_det_res->row_array();
				
				$sms_txt="Dear {$fran_det['franchise_name']}, As per your request new password {$rnd_key} has been generated. You can now log in with this password  -Storeking Team";
				$this->resource_model->sendsms($fran_det['login_mobile1'],$sms_txt,$fran_det['franchise_id'],0,"FORGOT_PWD");
				
				$output['status'] = TRUE;
				$output['msg']="Password has been generated and sent your registered mobile number -Storeking";
			}
			else {
				$output['status'] = FALSE;
				$output['msg'] = 'Unable to reset your password, please contact our customer care executive.';
			}
			return $output;
		}
	}
	
	/**
	 * function to get similar products of all brands and DP price range
	 * @param unknown_type $pid
	 */
	function get_similar_products($itemid,$start,$end)
	{
		$output=array('similar_products'=>array(),'error'=>'');
		
		$sql="SELECT i.id AS itemid,i.orgprice,i.price,IF((i.price BETWEEN (p1.price-(p1.price*10/100)) AND (p1.price+(p1.price*10/100))),10,25) AS perc,d.catid,i.name,pnh_id,i.id AS itemid,c.name as cat_name,b.name as brand_name,d.brandid,
				pnh_id AS pid,i.gender_attr,mc.name AS main_category,
				c.type AS main_category_id,i.orgprice AS mrp,i.price AS price,i.store_price,
				i.is_combo, CONCAT('".IMAGES_URL."items/',d.pic,'.jpg') AS image_url,CONCAT('".IMAGES_URL."items/small/',d.pic,'.jpg') AS small_image_url,i.shipsin AS ships_in,d.keywords 
									FROM king_dealitems i 
									JOIN king_deals d ON d.dealid=i.dealid 
									JOIN king_categories c ON c.id=d.catid 
									JOIN king_brands b ON b.id=d.brandid 
									LEFT OUTER JOIN king_categories mc ON mc.id=c.type 
									JOIN (
										SELECT d.catid,i.price
											FROM king_dealitems i 
											JOIN king_deals d ON d.dealid=i.dealid 
											JOIN king_categories c ON c.id=d.catid 
											WHERE i.id = ? AND d.publish=1
									) AS p1 ON p1.catid = c.id 
									AND ( (i.price BETWEEN (p1.price-(p1.price*10/100)) AND (p1.price+(p1.price*10/100))) OR (i.price BETWEEN (p1.price-(p1.price*25/100)) AND (p1.price+(p1.price*25/100))))
									where publish = 1  AND pnh_id!=0 
									GROUP BY i.pnh_id 
									ORDER BY i.price ASC
									Limit $start,$end";
		$similar_prod_res=$this->db->query($sql,$itemid);
		
		if($similar_prod_res->num_rows())
		{
			$output['similar_products']=$similar_prod_res->result_array();
			return $output;
		}
		else
		{ 
			return $output['error']='1';
		}
	}
	
	/**
	 * function to get popular products for selected menu,category,brand
	 * @param unknown_type $fid
	 * @param unknown_type $menuid
	 * @param unknown_type $catid
	 * @param unknown_type $brandid
	 * @return multitype:unknown |string
	 * @author Roopa <roopashree@storeking.in>
	 */
	function get_popular_products($menuid,$catid,$brandid,$start,$limit,$publish,$fid,$pid=0)
	{
		
		$output=array();
		
		$cond=$publish_cond='';
		
		if($publish==0)
			$publish_cond='';
		else if($publish==1)
			$publish_cond=' and d.publish=1';
		else if($publish==2)
			$publish_cond=' and d.publish=0';
			
		if($catid)
			$cond.=" and d.catid=".$catid;
		
		if($brandid)
			$cond.=" and d.brandid in ($brandid)";
		
		if($menuid)
			$cond.=" and d.menuid=".$menuid;
		
		if(!$start)
			$start=0;
		
		if(!$limit)
			$limit=5;
		
		$date = $this->db->query("select CURRENT_DATE - INTERVAL '2' MONTH as d")->row()->d;//current Date
		$from  = strtotime($date.' 00:00:00');
		$to  = strtotime(date('Y-m-d').' 23:59:59');
		
		$sql="
			SELECT o.itemid,o.brandid,d.catid,d.menuid,i.name,i.orgprice as mrp,i.price,SUM(o.quantity) AS sold_qty,
					pnh_id as pid,i.gender_attr,
					i.orgprice as mrp,i.price as price,i.store_price,
					i.is_combo,concat('".IMAGES_URL."items/',d.pic,'.jpg') as image_url,concat('".IMAGES_URL."items/small/',d.pic,'.jpg') as small_image_url,i.shipsin as ships_in,d.keywords,i.member_price 
				FROM king_orders o
				JOIN king_transactions t ON t.transid=o.transid
				JOIN king_dealitems i ON i.id=o.itemid
				JOIN king_deals d ON d.dealid=i.dealid
					WHERE 1  AND o.status!=3 and i.pnh_id!=? $cond
					group by itemid		
					ORDER BY sold_qty DESC limit $start,$limit;";
				
		$popular_prod_det=$this->db->query($sql,$pid);
				
		$output['popular_prod_det']=@$popular_prod_det->result_array();
				
			return $output;
		}
	
	/**
	 * Function to check new password is same as old password
	 * @author Shivaraj <shivaraj@storeking.in>
	 * @param type $rnd_key random password
	 * @param type $user_id string
	 * @return boolean 
	 */
	function _chk_pwd_exists($rnd_key,$user_id)
	{
		// if rand key is already used by him
		// redirect to same function
		$user_det = $this->db->query("SELECT * FROM pnh_users WHERE `password`=MD5(?) AND user_id=?",array($rnd_key,$user_id));
		if($user_det->num_rows() > 0)
			return TRUE;
		else
			return false;
	}
	
	/**
	 * Function to get the franchise basic details
	 * @author Shivaraj <shivaraj@storeking.in>
	 * @param unknown_type $user_id
	 * @param type $username string
	 @return boolean array/false
	 */
	function do_get_franchise_details($fid,$username)
	{
		$sql="select a.franchise_id,a.pnh_franchise_id,a.franchise_name,a.login_mobile1,a.login_mobile2,a.address,a.locality,a.city,a.postcode,a.state,a.credit_limit,
				t.town_name,tt.territory_name,is_prepaid
					from pnh_m_franchise_info a 
					join pnh_users b on b.reference_id=a.franchise_id
					join pnh_towns as t on t.id=a.town_id
					join pnh_m_territory_info tt on tt.id=a.territory_id
					where a.franchise_id=? or a.login_mobile1 = ?";
		
		$franchise_det_res=$this->db->query($sql,array($fid,$username));
		
		if($franchise_det_res->num_rows())
		{
			return $franchise_det_res->row_array();	
		
		}else{
			return false;
		}
	}
	
	/**
	 * Function to return franchise contact person details
	 * @author Shivaraj <shivaraj@storeking.in>
	 * @return string
	 */
	function get_contact_person($franchise_id)
	{
		$contact_det_res = $this->db->query("SELECT GROUP_CONCAT(DISTINCT contact_name) AS contact_names FROM pnh_m_franchise_contacts_info WHERE franchise_id=? GROUP BY franchise_id",array($franchise_id));
		if($contact_det_res->num_rows()) {
			$contact_det = $contact_det_res->row_array();
			return trim($contact_det['contact_names'],',');
		}
		else{
			return '';
		}
	}

	/**
	 * Function to return detailed status of a invoice
	 * @author Shivaraj <shivaraj@storeking.in>
	 * @param type $invoice_no bigint
	 * @return string|boolean
	 */
	function get_transit_status($invoice_no)
	{
		$inv_transit_log_res = $this->db->query("select a.id,a.ref_id,a.sent_log_id,a.invoice_no,a.logged_on,a.status,if(ref_id,b.name,d.name) as hndleby_name,
													if(ref_id,b.contact_no,d.contact_no) as hndleby_contactno,c.id as manifesto_id,c.hndleby_empid,c.bus_id,c.bus_destination,
													c.office_pickup_empid,c.pickup_empid,b.job_title2,c.hndlby_type,c.hndleby_courier_id,a.logged_by,c.alternative_contactno,a.received_on
														from pnh_invoice_transit_log a
														left join m_employee_info b on a.ref_id = b.employee_id
														join pnh_m_manifesto_sent_log c on c.id = a.sent_log_id
														left join pnh_transporter_info d on d.id=c.bus_id
														where invoice_no = ?
														order by a.id desc ",$invoice_no);
		if($inv_transit_log_res->num_rows())
		{
			$inv_transit_log = $inv_transit_log_res->row_array();

			if($inv_transit_log['status'] <= 2)
			{
				$inv_last_status = 'In Transit';
			}else if($inv_transit_log['status'] == 3)
			{
				$inv_last_status = 'Delivered'; 
			}else if($inv_transit_log['status'] == 4)
			{
				$inv_last_status = 'Marked for Return'; 
			} else if ($inv_transit_log['status'] == 5)
			{
				$inv_last_status = 'Picked';
			}
			
			
			//========
			$inv['inv_last_status'] = $inv_last_status;
			$inv['last_updated_on'] = format_datetime($inv_transit_log['logged_on']);
			$inv_last_updated_by = $inv_transit_log['hndleby_name'];
			$contact_no = $inv_transit_log['hndleby_contactno'];

			$franchise_details=$this->db->query("select a.invoice_no,d.franchise_name,e.town_name from shipment_batch_process_invoice_link a 
														join king_invoice b on b.invoice_no=a.invoice_no 
														join king_transactions c on c.transid=b.transid
														join pnh_m_franchise_info d on d.franchise_id=c.franchise_id
														join pnh_towns e on e.id = d.town_id
													where a.invoice_no=? group by a.invoice_no",$invoice_no)->row_array();




			$inv['Franchise_name']=$franchise_details['franchise_name'];
			$inv['town_name']=$franchise_details['town_name'];
			$inv['manifesto_id']=$inv_transit_log['sent_log_id'];

			// EXTRA INFO
			$alternative_number='';
			if($inv_transit_log['alternative_contactno'])
				$inv['alternative_number'] = $inv_transit_log['alternative_contactno'];

			//transport type manage
			if($inv_transit_log['hndlby_type']==4)
			{
				$courier_det=$this->db->query("select * from m_courier_info where courier_id=?",$inv_transit_log['hndleby_courier_id'])->row_array();
				$inv_last_updated_by = $courier_det['courier_name'];
				$contact_no = '';
				if(isset($courier_det['contact_no']))
					$contact_no=$courier_det['contact_no'];
			}
			else if(($inv_transit_log['hndlby_type']==1 || $inv_transit_log['hndlby_type']==2) && $inv_last_updated_by=='')
			{
				$emp_det=$this->db->query("select * from m_employee_info where employee_id=?",$inv_transit_log['hndleby_empid'])->row_array();
				$inv_last_updated_by=$emp_det['name'];
				$contact_no=$emp_det['contact_no'];
			}
			$inv['last_updated_by'] = $inv_last_updated_by;
			$inv['contact_no'] = $contact_no;


			$from_det='';
			$log_msg='';
			$sms='';
			//track message manage
			if($inv_transit_log['status'] <= 1)
			{
				$inv_last_status = 'In Transit';

				//if it is transportaion msg
				if($inv_transit_log['bus_id'])
				{
					//get the bus_details
					$bus_details=$this->db->query("select b.name as bus_name,b.contact_no,a.short_name,a.contact_no as ds_contact_no,a.type from pnh_transporter_dest_address a
												join pnh_transporter_info b on b.id=a.transpoter_id
												where a.id=?",$inv_transit_log['bus_destination'])->row_array();

					//get the office pick up details
					$office_pickiup_det=$this->db->query("select * from m_employee_info where employee_id=?",$inv_transit_log['office_pickup_empid'])->row_array();

					//get the destinaton pick up
					$destination_pickiup_det=$this->db->query("select * from m_employee_info where employee_id=?",$inv_transit_log['pickup_empid'])->row_array();

					$type='';
					if($bus_details['type']==1)
						$type='Bus transport';
					else if($bus_details['type']==2)
						$type='Cargo transport';
					else if($bus_details['type']==3)
						$type='General package transport';

					$transport_name=$bus_details['bus_name'].' '.$type.' ('.$bus_details['contact_no'].')';
					$destination_name=$bus_details['short_name'].' ('.$bus_details['ds_contact_no'].')';

					if(!$office_pickiup_det)
					{
						$office_pickiup_det=$this->db->query("select b.* from pnh_m_manifesto_sent_log a join king_admin b on b.id=a.modified_by where a.id=?",$inv_transit_log['manifesto_id'])->row_array();
						$log_msg="<b>Shipment Sent via : </b>".$transport_name." to ".$destination_name."<br>"."<b>by : </b>".$office_pickiup_det['name']."(".$office_pickiup_det['phone'].")<br> <b>To be collected by @ destination :</b>".$destination_pickiup_det['name'].'('.$destination_pickiup_det['contact_no'].')';
					}else{

						$log_msg="<b>Shipment Sent via :</b>  ".$transport_name." to ".$destination_name."<br>"."<b>office pickup by : </b>".$office_pickiup_det['name']."(".$office_pickiup_det['contact_no'].")<br> <b>To be collected by @ destination :</b>".$destination_pickiup_det['name'].'('.$destination_pickiup_det['contact_no'].')';
					}

				}else if($inv_transit_log['hndleby_empid'])
				{
					//get the office pick up details
					$driver_det=$this->db->query("select * from m_employee_info where employee_id=?",$inv_transit_log['hndleby_empid'])->row_array();

					$type=($driver_det['job_title2']==7)?'Driver ':'Fright Co-oridnator ';
					$driver_name=$type."<b>".$driver_det['name'].'('.$driver_det['contact_no'].')</b>';

					$log_msg="<b>Shipment Sent via : </b>".$driver_name." to "."<b>".$franchise_details['town_name']."</b>";

				}else if($inv_transit_log['hndlby_type']==4)
				{
					$log_msg="<b>Shipment Sent via Courier : </b>".$courier_det['courier_name']." to "."<b>".$franchise_details['town_name']."</b>";
					$inv_last_updated_by = $courier_det['courier_name'];
					$contact_no = '';
			}
			}
			
			if($inv_transit_log['status'] == 2 || $inv_transit_log['status'] == 5  )
			{
				if($inv_transit_log['status']==2)
					$inv_last_status = 'Handed over';
				else
					$inv_last_status='Picked';

				if($inv_transit_log['hndleby_empid'])
				{
					if($inv_transit_log['hndleby_empid']!=$inv_transit_log['ref_id'])
					{
						//get the sms 
						$last_ref_id='';
						$last_ref_id=$this->db->query("select * from pnh_invoice_transit_log where status=2 and ref_id!=? and invoice_no=? and id < ? order by logged_on desc limit 1",array($inv_transit_log['ref_id'],$invoice_no,$inv_transit_log['id']))->row_array();

						if(!$last_ref_id)
						{
							$last_ref_id=$this->db->query("select * from pnh_invoice_transit_log where status=1 and ref_id=? and invoice_no=? order by logged_on desc limit 1",array($inv_transit_log['hndleby_empid'],$invoice_no))->row_array();
						}

						$sms=$this->erpm->get_shipements_update_sms(array($last_ref_id['ref_id'],9));
						$from_det=$this->erpm->get_shipements_update_sms_from(array($last_ref_id['ref_id'],9));

						$destination_pickiup_det=$this->db->query("select * from m_employee_info where employee_id=?",$inv_transit_log['ref_id'])->row_array();
						$log_msg="Shipment Collected by ".$destination_pickiup_det['name'].'('.$destination_pickiup_det['contact_no'].')';

					}else{


						$driver_det=$this->db->query("select * from m_employee_info where employee_id=?",$inv_transit_log['hndleby_empid'])->row_array();

						$sms=$this->erpm->get_shipements_update_sms(array($inv_transit_log['hndleby_empid'],9));
						$from_det=$this->erpm->get_shipements_update_sms_from(array($inv_transit_log['hndleby_empid'],9));

						$type=($driver_det['job_title2']==7)?'Driver ':'Fright Co-oridnator ';
						$driver_name=$type.$driver_det['name'].'('.$driver_det['contact_no'].')';

						$handover_det=$this->db->query("select * from m_employee_info where employee_id=?",$inv_transit_log['ref_id'])->row_array();

						// Shipment handed over to Kiran(91991) |  234124 | Kumar
						$log_msg=$driver_name." Shipment handed over to ".$handover_det['name'].'('.$handover_det['contact_no'].')';
					}

				}else if($inv_transit_log['bus_id']){

					if($inv_transit_log['pickup_empid']!=$inv_transit_log['ref_id'])
					{
						$last_ref_id='';
						$last_ref_id=$this->db->query("select * from pnh_invoice_transit_log where (status=2 or status=5)  and ref_id!=? and invoice_no=? and id <= ? order by logged_on desc limit 1",array($inv_transit_log['ref_id'],$invoice_no,$inv_transit_log['id']))->row_array();

						$sms=$this->erpm->get_shipements_update_sms(array($last_ref_id['ref_id'],9));
						$from_det=$this->erpm->get_shipements_update_sms_from(array($last_ref_id['ref_id'],9));
					}
					else
					{
						$last_ref_id='';
						$last_ref_id=$this->db->query("select * from pnh_invoice_transit_log where status=2 and ref_id=? and invoice_no=? and id <= ? order by logged_on desc limit 1",array($inv_transit_log['pickup_empid'],$invoice_no,$inv_transit_log['id']))->row_array();

						$msg_type=0;
						if($last_ref_id)
						{
							$last_ref_id='';
							$last_ref_id=$this->db->query("select * from pnh_invoice_transit_log where status=2  and ref_id!=? and invoice_no=? and id <= ? order by logged_on desc limit 1",array($inv_transit_log['ref_id'],$invoice_no,$inv_transit_log['id']))->row_array();
							$msg_type=9;
							$last_ref_id=$last_ref_id['ref_id'];
						}else{

							$msg_type=8;
							$last_ref_id=$inv_transit_log['ref_id'];
						}


						$sms=$this->erpm->get_shipements_update_sms(array($last_ref_id,$msg_type));
						$from_det=$this->erpm->get_shipements_update_sms_from(array($last_ref_id,$msg_type));
					}
	
					$destination_pickiup_det=$this->db->query("select * from m_employee_info where employee_id=?",$inv_transit_log['ref_id'])->row_array();
					$log_msg="Shipment Collected by ".$destination_pickiup_det['name'].'('.$destination_pickiup_det['contact_no'].')';
					// Shipment Collected by Kumar | 234124 | Kumar
				}

			}
			else if($inv_transit_log['status'] == 3)
			{
				$inv_last_status = 'Delivered';

				if($inv_transit_log['received_on'] && $inv_transit_log['received_on']!=null)
					$inv_last_status.="<br>[".format_date($inv_transit_log['received_on'])."]";

				$driver_det=$this->db->query("select b.name,b.contact_no,b.job_title2 from pnh_invoice_transit_log  a join m_employee_info b on b.employee_id=a.ref_id where status=3 and invoice_no=?",$invoice_no)->row_array();

				if($driver_det)
				{
						if($driver_det['job_title2']==4)
							$type=" Territory Manager ";
						else if($driver_det['job_title2']==5)
							$type=" Bussiness Executive ";
						else if($driver_det['job_title2']==6)
							$type=" Fright Co-oridnator ";
						else if($driver_det['job_title2']==7)
							$type=" Driver ";

						$driver_name=$type."<b>".$driver_det['name'].'('.$driver_det['contact_no'].')</b>';

						$log_msg=$driver_name." Shipment <span style='color:green;'><b>Delivered</b></span> to <b>".$franchise_details['franchise_name']."</b>";

						$sms=$this->erpm->get_shipements_update_sms(array($inv_transit_log['ref_id'],10));
						$from_det=$this->erpm->get_shipements_update_sms_from(array($inv_transit_log['ref_id'],10));

				}else if(($inv_transit_log['logged_by'] && $inv_transit_log['ref_id']==0) || $inv_transit_log['hndlby_type']==4)
				{
					$user=$this->db->query("select * from king_admin where id=?",$inv_transit_log['logged_by'])->row_array();
					$log_msg=" Shipment  manully marked as <span style='color:green;'><b>Delivered</b></span> to <b>".$franchise_details['franchise_name']."</b><br> Marked by <b>".$user['username'];
				}


				// Shipment Delivered to Sri Sai Medicals |  234124 | Kiran
			}else if($inv_transit_log['status'] == 4)
			{
				$log_msg="Shipment Marked as Returned";
				$emp_det=$this->db->query("select * from m_employee_info where employee_id=?",$inv_transit_log['ref_id'])->row_array();
				$inv_last_updated_by = $emp_det['name'];
				$contact_no=$emp_det['contact_no'];

				$inv_last_status = 'Marked for Return';

				$sms=$this->erpm->get_shipements_update_sms(array($inv_transit_log['ref_id'],11));
				$from_det=$this->erpm->get_shipements_update_sms_from(array($inv_transit_log['ref_id'],11));
				// Shipment Marked  as Returned |  234124 | Kiran
			}

			$from='';
			if($from_det)
				$from='from : '.$from_det['contact_no'];

			if(!$sms)
			{
				$sms='system';
				$from='';
			}

			$inv['from'] = $from;
			$inv['sms'] = $sms;

			return $inv;
		}
		else
		{
			return false;
		}
			
	}

	/**
	 * 
	 * @param unknown_type $fid
	 * @param unknown_type $menuid
	 * @param unknown_type $brandid
	 * @param unknown_type $catid
	 * @param unknown_type $pid
	 * @author Roopa <roopashree@storeking.in>
	 */
	function check_if_menu_islinked_tofran($fid,$menuid,$catid,$brandid,$pid)
	{
		$cond='';
		
		if(!$menuid)
			$menuid=0;
		else 
			$cond.=" and a.menuid=".$menuid;
		
		if(!$brandid)
			$brandid=0;
		else 
			$cond.=" and d.brandid=".$brandid;
		
		
		if(!$catid)
			$catid=0;
		else
			$cond.=" and d.catid=".$catid;
		
		if(!$pid)
			$pid=0;
		else 
			$cond.=" and i.pnh_id=".$pid;
			
		$sql="SELECT m.id,m.name AS menu FROM `pnh_franchise_menu_link`a
					JOIN pnh_m_franchise_info b ON b.franchise_id=a.fid
					join pnh_users c on c.reference_id=b.franchise_id
					JOIN pnh_menu m ON m.id=a.menuid
					JOIN king_deals d on d.menuid=a.menuid
					JOIN king_dealitems i on i.dealid=d.dealid
					WHERE a.status=1 AND b.franchise_id=? $cond and a.status=1 GROUP by m.id";
	//	echo $this->db->last_query();exit;
		
		$is_menulinked=$this->db->query($sql,$fid);
	
		if($is_menulinked->num_rows())
			return true;
		else 
			return false;
		
	}
	
	/**
	* function to get recent payments
	* @param unknown_type $fid
	* @param unknown_type $st_date
	* @param unknown_type $en_date
	* @return string
	* @author Roopa <roopashree@storeking.in>
	*/
	function get_recent_payments_byfran($fid,$st_date,$en_date,$receipt_id,$start,$limit=25)
	{
		$cond='';
		$output=array();
		if($st_date)
		{
			$st_date=strtotime($st_date.' 00:00:00');
			$en_date=strtotime($en_date.' 23:59:59');
			$cond = " AND created_on BETWEEN $st_date AND $en_date  ";
		}	
	
		if($receipt_id!=0)
			$cond.=" and receipt_id = $receipt_id";
			
			$sql="SELECT receipt_id,bank_name,IF(payment_mode=0,'cash', IF(payment_mode=1,'cheque',IF(payment_mode=2,'dd',IF(payment_mode=3,'trans','na')))) AS payment_mode,receipt_amount,receipt_type,instrument_no,instrument_date,`status`,
			DATE_FORMAT(FROM_UNIXTIME(created_on) ,'%d/%m/%Y') AS created_on,DATE_FORMAT(FROM_UNIXTIME(created_on) ,'%h:%i %p') AS created_on_time,remarks
			FROM pnh_t_receipt_info r WHERE franchise_id=? and r.status=1 AND r.is_active=1 AND (r.is_submitted=1 OR r.activated_on!=0) AND r.is_active=1 $cond
			ORDER BY receipt_id DESC LIMIT $start,$limit";
	
		$recent_payment_det=$this->db->query($sql,$fid);
		
		if($recent_payment_det->num_rows())
			return $output['recent_payment']=$recent_payment_det->result_array();
		else
		return $output['error']='1';
	}
	
	/**
	 * function to get returns list
	 * @param unknown_type $fid
	 */
	function get_returns_list($fid,$start_date="0000-00-00",$end_date="0000-00-00",$start,$limit)
	{
		$cond='';
		$output = array();
		
		if($start_date && $end_date)
			$cond = "and date(returned_on) >= '$start_date' and date(returned_on)<= '$end_date'";
		
		if(!$start)
			$start=0;
		
		if(!$limit)
			$limit=10;
		
		$sql="SELECT i.*,a.name AS handled_byname,f.franchise_name
				FROM pnh_invoice_returns i
				JOIN king_invoice b ON b.invoice_no=i.invoice_no
				JOIN king_transactions t ON t.transid=b.transid
				JOIN king_admin a ON a.id=i.handled_by
				JOIN pnh_m_franchise_info f ON f.franchise_id=t.franchise_id
				WHERE t.franchise_id=? $cond
				ORDER BY returned_on DESC";
		
		$sql.=" limit $start,$limit";
		
		$return_list_res=$this->db->query($sql,$fid);
		
		if($return_list_res->num_rows())
		{
			 $output['returns_res']=$return_list_res->result_array();
			 
			 return $output; 
		}
		else
			return $output['error']='1';
	}

	/**
	 * function to get itemid by pnh id 
	 * @param INTEGER $pnh_id
	 */
	function get_itemdetbypnhid($pnh_id)
	{
		return $this->db->query("select id from king_dealitems where pnh_id = ? ",$pnh_id)->row()->id;
	}

	/**
	 * function to check for valid invoice number
	 * @param unknown_type $fid
	 * @param unknown_type $invoice_no
	 * @return boolean
	 */
	function _check_is_validinvno($fid,$invoice_no)
	{
		// check if the entered no is imeino
		$imei_inv_res = $this->db->query('select b.invoice_no from t_imei_no a join king_invoice b on a.order_id = b.order_id and b.invoice_status = 1 where imei_no = ? ',$invoice_no);
		if($imei_inv_res->num_rows())
		{
			$invoice_no = $imei_inv_res->row()->invoice_no;
		} 
		$fr_det = $this->db->query("select a.franchise_id,franchise_name from pnh_m_franchise_info a join king_transactions b on a.franchise_id = b.franchise_id join king_invoice c on c.transid = b.transid where c.invoice_no = ? and a.franchise_id=? ",array($invoice_no,$fid));
	
		if($fr_det->num_rows())
			return $invoice_no;
		else 
			return false;
		
	}
	
	/**
	 * function returns list of banks with details
	 */
  	function get_bank_list()
	{
		return $this->db->query("Select * from pnh_m_bank_info group by account_number order by bank_name asc")->result_array();
	}	
	
	/**
	 * function to get all raw menu list 
	 */
	function get_all_menu()
	{
		return $this->db->query("select id,name from pnh_menu order by name ")->result_array();
	}
	
	/**
	 * function to get all raw brand list
	 */
	function get_all_brands()
	{
		return $this->db->query("select id,name from king_brands order by name ")->result_array();
	}
	
	/**
	 * function to get all raw brand list
	 */
	function get_all_categories()
	{
		return $this->db->query("select id,name,type as parent_id from king_categories order by name ")->result_array();
	}
	
	/**
	 * function to get all raw groups list
	 * @last_modified by Roopashree <roopashree@storeking.in>
	 */
	function get_menu_groups()
	{
		return $this->db->query("select a.id,a.name,b.menu_id from pnh_menu_groups a join pnh_menu_group_link b on a.id = b.group_id order by name ")->result_array();
	}
	
	/**
	 * function to get all raw groups list
	 * @last_modify Shivaraj_Nov_17_2014 group_icon field added
	 */
	function get_groups()
	{
		$g_res = $this->db->query("SELECT g.id AS group_id,g.name AS group_name,GROUP_CONCAT(l.menu_id ) AS menuid,concat('http://sndev13.snapittoday.com/images/group_icons/',g.id,'.png') as group_icon
			FROM `pnh_menu_groups`g JOIN `pnh_menu_group_link`l ON l.group_id=g.id 
			JOIN pnh_menu m ON m.id=l.menu_id GROUP BY g.id order by group_name");
		
		if($g_res->num_rows())
			return $g_res->result_array();
		return false;
	}
	
	/**
	 * function to get all raw groups list
	 */
	function get_group_cats($group_id,$start,$limit,$alpha_sort=0,$full=0,$top_cats)
	{
		$gc['cat_list']='';
		if($top_cats)
		{
			$limit=5;		
		}		
		$sql = "select a.id as group_id,a.name as group_name,catid as catid,d.name as cat_name,count(ifnull(o.id,0)) as ttl_orders 
										from pnh_menu_groups a 
										join pnh_menu_group_link b on a.id = b.group_id 
										join king_deals c on c.menuid = b.menu_id 
										join king_categories d on d.id = c.catid
										join king_dealitems di on di.dealid = c.dealid 
										left join king_orders o on o.itemid = di.id  
										where a.id = ? and o.status!=3
									group by catid 
									order by ttl_orders desc ";
		$gc_res=$this->db->query($sql,$group_id);
									
		if($gc_res->num_rows())
		{
			$gc['ttl_cat']=$gc_res->num_rows();
			
			$sql.=" limit $start,$limit";
			
			$gc['cat_list']=$this->db->query($sql,$group_id)->result_array();
			return $gc;
		}
		else
		{
			return false;
		}
		
	}
	function is_valid_emp_phno($fid,$emp_phno)
	{
		$sql="SELECT f.territory_id,l.employee_id,e.contact_no,NAME
		FROM pnh_m_franchise_info f
		JOIN `pnh_m_territory_info` t ON f.territory_id=t.id
		JOIN m_town_territory_link l ON l.territory_id=t.id
		JOIN m_employee_info e ON e.employee_id=l.employee_id
		WHERE franchise_id=? AND e.is_suspended=0 AND e.contact_no IN(?)";
		
		$is_valid=$this->db->query($sql,array($fid,$emp_phno));
		if($is_valid->num_rows())
		return true;
		else
		return false;
	}
	
	/**
	 * function to get all menus of the group menu
	 * @param unknown_type $grp_menuid
	 * @return boolean
	 */
	function get_menu_bygroupmenuid($grp_menuid)
	{
		$sql="SELECT group_id,m.name AS menu,menu_id FROM pnh_menu_groups a JOIN  pnh_menu_group_link b ON a.id = b.group_id  JOIN pnh_menu m ON m.id=menu_id WHERE group_id= ?";
		$grp_menu_list=$this->db->query($sql,$grp_menuid);
		if($grp_menu_list)
			return $grp_menu_list->result_array();
		else
			return false;
	}
	/**
	 * Function create customer tickets
	 * @author Shivaraj <shivaraj@storeking.in>
	 * @param type $name
	 * @param type $mobile
	 * @param type $msg
	 * @param type $email
	 * @param type $transid
	 * @param type $type
	 * @param type $priority
	 * @param type $franchise_id
	 * @param type $related_to
	 * @param type $medium
	 * @return type Array
	 */
	function do_ticket($name='',$mobile='',$msg='',$email='',$transid='',$type='',$priority='',$franchise_id=0,$related_to='',$medium=0,$req_mem_name=0,$req_mem_mobile=0)
	{
		$no=rand(1000000000,9999999999);
		$user_det =  $this->get_api_user();
		$created_by = $user_det['userid'];

		$from_app=1;
		$msg=nl2br($msg);
		$this->db->query("insert into support_tickets(ticket_no,name,mobile,user_id,email,transid,type,priority,created_on,franchise_id,related_to,req_mem_name,req_mem_mobile,from_app,created_by)
							values(?,?,?,?,?,?,?,?,now(),?,?,?,?,?,?)"
							,array($no,$name,$mobile,'0',$email,$transid,$type,$priority,$franchise_id,$related_to,$req_mem_name,$req_mem_mobile,$from_app,$created_by));
		$tid=$this->db->insert_id();
		$from_customer=1;
		$this->erpm->addnotesticket($tid,$type,$medium,$msg,$from_customer);
		return array('ticket_id'=>$tid,'ticket_no'=>$no);
	}
	
	/**
	 * Function to return individual ticket details by ticket id
	 * @author Shivaraj <shivaraj@storeking.in>
	 * @param type $ticket_id int
	 * @return Array|boolean
	 */
	function getticket($ticket_id)//$filter
	{
		
		$sql = "SELECT u.name AS USER,t.*,a.name AS assignedto
					FROM support_tickets t 
					LEFT OUTER JOIN king_admin a ON a.id=t.assigned_to 
					LEFT OUTER JOIN king_users u ON u.userid=t.user_id WHERE t.ticket_id= ?";
		
//		die('<pre>'.$sql );
		$ticket_det_res = $this->db->query($sql,$ticket_id);
		if($ticket_det_res->num_rows())
		{
			//die('<pre>'.$this->db->last_query() );
			$ticket = $ticket_det_res->row_array();
			$output = $ticket;
			$related_to_array = array(0=>'Not specific',1=>"Product",2=>"Payment",3=>"Service",4=>"insurance",5=>"Shipment",6=>"Technical");
			$type_array = array(10=>'Request',11=>"Complaint");
			$priority_array = array(0=>'Low',1=>"Medium",2=>"High",3=>"Urgent");
			//$ticket_status=array("all"=>-1,"unassigned"=>0,"opened"=>1,"inprogress"=>2,"closed"=>3);
			$ticket_status=array(0=>"Open",1=>"Open",2=>"Open",3=>"Closed");
			
			$output['related_to'] = @$related_to_array[$ticket['related_to']];
			$output['type'] = @$type_array[$ticket['type']];
			$output['priority'] = $priority_array[$ticket['priority']];
			$output['status'] = $ticket_status[$ticket['status']];
			$output['created_on'] = format_datetime($ticket['created_on']);//format_datetime(date( 'Y/m/d H:i:s',strtotime($ticket['created_on']) ) ; //format_datetime( ,'d/M/Y H:i A')
			$output['updated_on'] = format_datetime($ticket['updated_on']);
			$output['messages'] = $this->get_ticket_messages($ticket['ticket_id']);
			$output['image_path'] = @$this->db->query("select ifnull(concat('".SNDBOX_RESOURCE_IMAGES."',pic,'.jpg'),'n/a') as image_url from pnh_invoice_returns_images where ticket_id=?",$ticket['ticket_id'])->result_array();
			return $output;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Function to return tickets list array by franchise id
	 * @author Shivaraj <shivaraj@storeking.in>
	 * @param type $franchise_id int
	 * @param type $s date_format
	 * @param type $e date_format
	 * @param type $limit int
	 * @return string|boolean
	 */
	function gettickets($franchise_id=false,$s=false,$e=false,$type=false,$limit=100)//$filter
	{
		$cond = $cond_order_by = $cond_limit = '';
		$filter=-1;
		
		if($franchise_id){
			$cond .= ' AND f.franchise_id= '.$franchise_id;
		}
		if($filter!=-1)
		{
			$filters=array("all"=>-1,"unassigned"=>0,"opened"=>1,"inprogress"=>2,"closed"=>3);
			$filter=$filters[$filter];
			$cond.=" and t.status=$filter and";
		}
		
		if($e)
		{
			$cond.=" AND t.created_on BETWEEN ? AND ? ";
		}
		
		if($type)
		{
			$cond.=" AND t.type= ".$type;
		}
		
		$cond_order_by .=" ORDER BY t.updated_on DESC, t.created_on DESC";
		
		
		if(!$e)
		{
			if($limit) {
				$cond_limit.=" limit ".$limit;
			}
			
			$tom=mktime(0,0,0,date("m"),date("d")+1);
			$s=date("Y-m-d",time()-(30*24*60*60));
			$e=date("Y-m-d",time());
		}
		$sql="SELECT t.ticket_id,t.ticket_no,t.name,t.mobile,t.email,t.transid,t.type,t.status,t.priority,t.assigned_to,t.franchise_id,t.related_to,t.req_mem_name,t.req_mem_mobile,DATE_FORMAT(t.created_on,'%d/%b/%Y %h:%i:%s %p') AS created_on,DATE_FORMAT(t.updated_on,'%d/%b/%Y %h:%i:%s %p') AS updated_on,t.from_app
								,t.user_id,a.name AS USER,u.name AS assignedto,a2.name AS created_by,f.franchise_name
								,rt.id AS related_to_id,rt.name AS related_to_name
								,GROUP_CONCAT(DISTINCT ' ',dept.name ORDER BY dept.name) AS dept_dets,IF(t.franchise_id=0,0,1) AS source
				FROM support_tickets t 

				LEFT OUTER JOIN king_admin a ON a.id=t.user_id 
				LEFT OUTER JOIN king_admin u ON u.id=t.assigned_to
				LEFT OUTER JOIN king_admin a2 ON a2.id=t.created_by
				LEFT OUTER JOIN pnh_m_franchise_info f ON f.franchise_id=t.franchise_id

				LEFT OUTER JOIN m_dept_request_types rt ON rt.id=t.related_to
				LEFT OUTER JOIN m_dept_request_type_link dlnk ON dlnk.type_id = t.related_to AND dlnk.is_active='1'
				LEFT OUTER JOIN m_departments dept ON dept.id = dlnk.dept_id

				WHERE 1 $cond
				GROUP BY t.ticket_id
				$cond_order_by $cond_limit
				";
				
		$ticket_det_res = $this->db->query($sql,array($s,$e));
		if($ticket_det_res->num_rows())
		{
			//die('<pre>'.$this->db->last_query() );
			$ticket_det = $ticket_det_res->result_array();
			foreach($ticket_det as $i=>$ticket)
			{
				$output[$i] = $ticket;
				
				$related_to='Unknown';
				if($ticket['related_to']==0)
					$related_to='Not specific';
				else {
					$related_to_array=$this->erpm->get_request_types();
					foreach($related_to_array as $related)
						if($related['id']==$ticket['related_to'])
							$related_to=$related['name'];
				}
				
				$type_array = array(10=>'Request',11=>"Complaint");
				$priority_array = array(0=>'Low',1=>"Medium",2=>"High",3=>"Urgent");
				//$ticket_status=array("all"=>-1,"unassigned"=>0,"opened"=>1,"inprogress"=>2,"closed"=>3);
				$ticket_status=array(0=>"Open",1=>"Open",2=>"Open",3=>"Closed");
				
				$output[$i]['related_to'] = $related_to;
				$output[$i]['type'] = @$type_array[$ticket['type']];
				$output[$i]['priority'] = $priority_array[$ticket['priority']];
				$output[$i]['status'] = $ticket_status[$ticket['status']];
				$output[$i]['created_on'] = format_datetime($ticket['created_on']) ; //format_datetime( ,'d/M/Y H:i A')
				$output[$i]['updated_on'] = format_datetime($ticket['updated_on']);
				$output[$i]['messages'] = $this->get_ticket_messages($ticket['ticket_id']);
				$output[$i]['image_path'] = @$this->db->query("select ifnull(concat('".SNDBOX_RESOURCE_IMAGES."',pic,'.jpg'),'n/a') as image_url from pnh_invoice_returns_images where ticket_id=?",$ticket['ticket_id'])->result_array();
			}
			return $output;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Function to return message information of ticket id, including reply messages
	 * @author Shivaraj <shivaraj@storeking.in>
	 * @param type $ticket_id int
	 * @return type array
	 */
	function get_ticket_messages($ticket_id)
	{
		//,DATE_FORMAT(m.created_on,'%d/%b/%Y %H:%i %p') as created_on
		return $this->db->query("select a.name as admin_user,m.ticket_id,m.msg,m.msg_type,m.medium,m.from_customer,m.support_user,DATE_FORMAT(m.created_on,'%d/%m/%Y %H:%i %p') as created_on
									from support_tickets_msg m
									left outer join king_admin a on a.id=m.support_user
									where m.ticket_id=? order by m.id desc",$ticket_id)->result_array();
	}

	/**
	 * function to get all menus of the group menu
	 * @param unknown_type $grp_menuid
	 * @return boolean
	 * @author roopashree@storeking.in
	 * @last_modified_on_10_sept_2014 @ suresh
	 * @last_modify Shivaraj_Nov_15_2014 Update: Menu icon field
	 */
	function get_menucat_bygroupmenuid($grp_menuid,$start,$end)
	{
		$sql="SELECT group_id,m.name AS menu,menu_id FROM pnh_menu_groups a JOIN  pnh_menu_group_link b ON a.id = b.group_id  JOIN pnh_menu m ON m.id=menu_id WHERE group_id= ?";
		$grp_menu_list=$this->db->query($sql,$grp_menuid);
		
		$menu_list=array();
		if($grp_menu_list)
		{
			foreach($grp_menu_list->result_array() as $i=>$m)
			{
				if(!isset($menu_list[$m['group_id']][$i]))
				{
					$menu_list[$m['group_id']]['menu_list'][$i]=array();
					$menu_list[$m['group_id']]['menu_list'][$i]=array("menuid"=>$m['menu_id'],"menu_name"=>$m['menu'],"menu_icon"=>'http://sndev13.snapittoday.com/images/menu_icons/'.$m['menu_id'].".png");
				}
					
				$sql="SELECT a.id,group_id,m.name AS menu,menu_id,c.id as catid,c.name AS category_name,COUNT(IFNULL(o.id,0)) AS ttl_orders  
						FROM pnh_menu_groups a 
						JOIN  pnh_menu_group_link b ON a.id = b.group_id  
						JOIN pnh_menu m ON m.id=menu_id 
						JOIN king_deals d ON d.menuid=m.id
						JOIN king_categories c ON c.id=d.catid
						JOIN king_dealitems di ON di.dealid = d.dealid 
						LEFT JOIN king_orders o ON o.itemid = di.id  
						WHERE menu_id= ? AND a.id=?
						GROUP BY c.id
						ORDER BY ttl_orders DESC";
			
				
				$ttl_cats=$this->db->query($sql,array($m['menu_id'],$grp_menuid));
				$menu_list[$m['group_id']]['menu_list'][$i]['ttl_cats']=$ttl_cats->num_rows(); 
				$cat_list=$this->db->query($sql,array($m['menu_id'],$grp_menuid));

				if($cat_list)
				{
					/*
					foreach($cat_list->result_array() as $c)
										{
											$menu_list[$m['group_id']][$m['menu_id']]['ttl_cats']=sizeof($menu_list[$m['group_id']][$m['menu_id']]);
										}*/
					if($end!=0)
						$sql.=" limit $start,$end";
					
					$cat_list=$this->db->query($sql,array($m['menu_id'],$grp_menuid));

					foreach($cat_list->result_array() as $j=>$c)
					{
						if(!isset($menu_list[$c['group_id']][$i][$j]))
						{
							$menu_list[$c['group_id']]['menu_list'][$i]['cat_list'][$j]=array();
							$menu_list[$c['group_id']]['menu_list'][$i]['cat_list'][$j]=array("catid"=>$c['catid'],"cat_name"=>$c['category_name']);
						}
						
					}
				}

			}
			
			return $menu_list;
		}
		else 
			return false;
			
	}
	
	/**
	 * Function to return breif info of order delivery and transit status and date info
	 * @author Shivaraj <shivaraj@storeking.in>
	 * @param type $orderid bigint
	 * @return boolean|Array
	 */
	function get_order_ship_det($orderid)
	{
		/*select a.id,a.ref_id,a.sent_log_id,a.invoice_no,a.logged_on,a.status,if(ref_id,b.name,d.name) as hndleby_name,
													if(ref_id,b.contact_no,d.contact_no) as hndleby_contactno,c.id as manifesto_id,c.hndleby_empid,c.bus_id,c.bus_destination,
													c.office_pickup_empid,c.pickup_empid,b.job_title2,c.hndlby_type,c.hndleby_courier_id,a.logged_by,c.alternative_contactno,a.received_on
														from pnh_invoice_transit_log a
														left join m_employee_info b on a.ref_id = b.employee_id
														join pnh_m_manifesto_sent_log c on c.id = a.sent_log_id
														left join pnh_transporter_info d on d.id=c.bus_id
														where order_id = ? order by a.id desc*/
		$inv_transit_log_res = $this->db->query("SELECT tlog.id,tlog.ref_id,tlog.sent_log_id,tlog.invoice_no,tlog.logged_on,tlog.status,tlog.received_on
													,mlog.id AS manifesto_id
													FROM king_invoice i 
													LEFT JOIN pnh_invoice_transit_log tlog ON tlog.invoice_no = i.invoice_no
													LEFT JOIN pnh_m_manifesto_sent_log mlog ON mlog.id = tlog.sent_log_id
													WHERE order_id = ? ORDER BY tlog.logged_on DESC",$orderid);
//		return ($this->db->last_query());
		if($inv_transit_log_res->num_rows())
		{
			$inv_transit_log = $inv_transit_log_res->result_array();
			$inv = array();
			foreach($inv_transit_log as $i=>$tlog)
			{
				if($tlog['status'] <= 2)
				{
					$inv_last_status = 'Shipped';//'In Transit';
				}else if($tlog['status'] == 3)
				{
					$inv_last_status = 'Delivered'; 
				}else if($tlog['status'] == 4)
				{
					$inv_last_status = 'Marked for Return'; 
				} else if ($tlog['status'] == 5)
				{
					$inv_last_status = 'Picked';
				}

				//========
				$inv[$i]['invoice_no'] = $tlog['invoice_no'];
				$inv[$i]['inv_last_status'] = $inv_last_status;
				$inv[$i]['last_updated_on'] = format_datetime($tlog['logged_on']);
			}
			return $inv;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Function to return transit log & its invoice numbers by transid
	 * @author Shivaraj <shivaraj@storeking.in>_Jun_14_2014
	 * @param type $transid Mixed
	 * @return boolean|Array
	 */
	function get_transit_status_transid($transid,$status='')
	{
		$cond = '';
		if($status == "2")
		{
			$cond .= ' AND tlog.status in (2)';
		}
		elseif($status == "4")
		{
			$cond .= ' AND tlog.status in (3,5)';
		}
		
		$transit_sts_res = $this->db->query("SELECT DISTINCT tlog.invoice_no,tlog.status,tlog.logged_on FROM king_invoice i
								JOIN pnh_invoice_transit_log tlog ON tlog.invoice_no = i.invoice_no
								WHERE i.transid = ? $cond
								ORDER BY tlog.logged_on DESC LIMIT 1",array($transid) );
		if($transit_sts_res->num_rows())
		{
			$transit_sts = $transit_sts_res->row_array();
			$inv['invoice_no']=$transit_sts['invoice_no'];
			$inv['status']=$transit_sts['status'];
			$inv['logged_on']=$transit_sts['logged_on'];
			return $inv;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Function to check cart items already processed or not
	 * @author Shivaraj <shivaraj@storeking.in>_Aug_23_2014
	 * @param type $cartid
	 * @return type
	 */
	function _chk_cartid_exists($cartid)
	{
		return ( $this->db->query("SELECT COUNT(*) AS t FROM king_transactions WHERE api_cartid=?",$cartid)->row()->t ? true : false );
	}

	/**
	 * Function to return trans details by cart id
	 * @author Shivaraj <shivaraj@storeking.in>_Aug_23_2014
	 * @param type $cartid
	 * @return boolean
	 */
	function _get_transdet_by_cartid($cartid)
	{
		if($cartid)
		{
		$transdet = $this->db->query("SELECT transid,order_for FROM king_transactions WHERE api_cartid=?",$cartid);
		if($transdet->num_rows())
			{
			return $transdet->row();
			}
		else
		{
			return false;
		}
		}else
		{
			return false;
		}
	}
	/**
	 * function to get menu brand and catgory list with linking by versions 
	 *  
	 * @param unknown_type $versions
	 */
	function get_menucatbrandbyversion($versions)
	{
		$version_ids = array();
		foreach($versions as $v)
			array_push($version_ids,$v['id']);
		
		$latest_version_id = max($version_ids);
		
		$sql = "select * from (select ifnull(parent_cat_id,0) as parent_cat_id,parent_cat,pcat,pbrand,pmenu,pcat_id,pbrand_id,pmenu_id,max(new_stat) as is_new from (
					SELECT l.id,k.version,l.item_id,l.is_new AS new_stat,menuid AS pmenu_id,e.name AS pmenu,b.name AS pname,catid AS pcat_id,c.name AS pcat,
						cp.id AS parent_cat_id,cp.name AS parent_cat,brandid AS pbrand_id,d.name AS pbrand
					FROM m_apk_version k
					JOIN m_apk_version_deal_link l ON k.id=l.version_id
					JOIN king_dealitems b ON b.id = l.item_id
					JOIN king_deals a ON a.dealid=b.dealid
					JOIN king_categories c ON c.id = a.catid
					LEFT JOIN king_categories cp ON cp.id = c.type
					JOIN king_brands d ON d.id = a.brandid
					JOIN pnh_menu e ON e.id = a.menuid
					WHERE l.version_id IN (".implode(',',$version_ids).")
					ORDER BY l.id DESC 
				) as g
				where 1 
				group by parent_cat_id,g.pcat_id,pbrand,pmenu_id 
				order by id asc ) as k 
				order by pcat,pbrand
			";
		$res = $this->db->query($sql);
		$data = array();
		$data['cat_list'] = array();
		$data['brand_list'] = array();
		
		if($res->num_rows())
		{
			foreach($res->result_array() as $row)
			{
				// build category brand and menu list 
				$data['cat_list'][$row['pcat_id']] = array('id'=>$row['pcat_id'],'name'=>$row['pcat'],'menu_id'=>$row['pmenu_id'],'menu_name'=>$row['pmenu'],'parent_id'=>$row['parent_cat_id'],'is_active'=>1);
				
				if($row['parent_cat_id'])
					$data['cat_list'][$row['parent_cat_id']] = array('id'=>$row['parent_cat_id'],'name'=>$row['parent_cat'],'menu_id'=>$row['pmenu_id'],'menu_name'=>$row['pmenu'],'parent_id'=>0,'is_active'=>1);
				
				$data['brand_list'][$row['pbrand_id']] = array('id'=>$row['pbrand_id'],'name'=>$row['pbrand'],'menu_id'=>$row['pmenu_id'],'menu_name'=>$row['pmenu'],'is_active'=>1);
			}
			
			$data['version'] = $this->db->query('select version from m_apk_version where id = ? ',$latest_version_id)->row()->version;
		}
		
		$data['cat_list'] = array_values($data['cat_list']);
		$data['brand_list'] = array_values($data['brand_list']);
		
		return $data;
	}
	
	function get_sub_franchise_details($fids,$ftype)
	{
		
		$subfrandet=array();
		//$subfrandet['basic_det']=array();
		//$subfrandet['sales_det']=array();
		//$subfrandet['pmt_det']=array();
		//$subfrandet['shipment_det']=array();
		
		if($fids)
		{
			foreach($fids as $i=>$fid)
			{
				if($srch)
					$cond.=" and (t.town_name like '%$srch%' or a.login_mobile1 like '%$srch%' or a.franchise_name like '%srch%')";
				$basic_det=$this->db->query("select a.franchise_id,a.pnh_franchise_id,a.franchise_name,a.address,a.locality,a.city,a.postcode,a.state,a.credit_limit,
											t.town_name,t.id as town_id,tt.territory_name,a.login_mobile1,is_prepaid,a.franchise_type,assigned_rmfid
												from pnh_m_franchise_info a 
												left join pnh_users b on b.reference_id=a.franchise_id
												join pnh_towns as t on t.id=a.town_id
												join pnh_m_territory_info tt on tt.id=a.territory_id
												where a.franchise_id=? and franchise_type=1 $cond",$fid);
				
				$subfrandet[$i]['basic_det']=$basic_det->row_array();
			
				//last month sales value
				$last_mnthsales=$this->db->query("SELECT IFNULL(ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2),0) AS ttl_sales  FROM king_transactions a 
						JOIN king_orders b ON a.transid = b.transid
						JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id
						WHERE   MONTH(DATE(FROM_UNIXTIME(a.init)))=MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(DATE(FROM_UNIXTIME(a.init))) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))  AND a.franchise_id=? AND b.status!=3",$fid)->row()->ttl_sales;
				
				$subfrandet[$i]['sales_det']['lastmnth_sales']=$last_mnthsales;
				
				//current mnth sales value
				$currmnth_sales=$this->db->query("SELECT IFNULL(ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2),0) AS ttl_sales  FROM king_transactions a 
												JOIN king_orders b ON a.transid = b.transid
												JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id
												WHERE   MONTH(DATE(FROM_UNIXTIME(a.init)))=MONTH(CURDATE()) AND YEAR(DATE(FROM_UNIXTIME(a.init))) = YEAR(CURDATE())  AND a.franchise_id=? AND b.status!=3",$fid)->row()->ttl_sales;
				$subfrandet[$i]['sales_det']['currmnth_sales']=$currmnth_sales;
				
				//current date sales value
				$curedate_sales=$this->db->query("SELECT IFNULL(ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2),0) AS ttl_sales  FROM king_transactions a 
													JOIN king_orders b ON a.transid = b.transid
													JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id
													WHERE   DATE(FROM_UNIXTIME(a.init))=CURDATE() AND YEAR(DATE(FROM_UNIXTIME(a.init))) = YEAR(CURDATE())  AND a.franchise_id=? AND b.status!=3",$fid)->row()->ttl_sales;
				$subfrandet[$i]['sales_det']['currday_sales']=$curedate_sales;
				
				$account_summary=$this->erpm->get_franchise_account_stat_byid($fid);
				$credit_note_amt = $account_summary['credit_note_amt'];
				$shipped_tilldate = $account_summary['shipped_tilldate'];
				$paid_tilldate = $account_summary['paid_tilldate'];
				$acc_adjustments_val = $account_summary['acc_adjustments_val'];
				$subfrandet[$i]['pmt_det']['pending_pmt']=format_price($shipped_tilldate-($paid_tilldate+$acc_adjustments_val+$credit_note_amt),0);
				$subfrandet[$i]['pmt_det']['uncleard_pmt']=format_price($account_summary['uncleared_payment'],2);
				
				$ttl_open_orders=$this->db->query("SELECT COUNT(*) as ttl FROM king_orders o JOIN king_transactions t ON t.transid=o.transid WHERE o.status=0 AND franchise_id=? AND batch_enabled=1",$fid)->row()->ttl;
				$subfrandet[$i]['shipment_det']['ttl_opnorders']=$ttl_open_orders;
	}
			return $subfrandet;
		}else 
			return false;

	}

	function brands_list($ch)
	{
		if($ch == '09')
		{
			return $this->db->query("Select id,name from king_brands where name REGEXP '^[0-9]' group by id order by name" );
		}
		else if($ch == '20')
		{
			return $this->db->query("select b.id,b.name from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories c on c.id=d.catid join king_orders o on o.itemid=i.id join king_transactions t on t.transid=o.transid and t.is_pnh=1 where i.is_pnh=1 group by c.name order by count(o.id) desc limit 20");
		}else if($ch == '10')
		{
			return $this->db->query("select b.id,b.name from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories c on c.id=d.catid join king_orders o on o.itemid=i.id join king_transactions t on t.transid=o.transid and t.is_pnh=1 where i.is_pnh=1 group by c.name order by count(o.id) desc limit 10");
		}else
		{
			return $this->db->query("Select id,name from king_brands where name like '".$ch."%' group by id order by name" );
		}
		
	}
	
	function pnh_gettowns($tid=0)
	{
		$sql="select t.id as town_id,territory_id,t.town_name,tr.territory_name 
					from pnh_towns t 
					join pnh_m_territory_info tr on tr.id=t.territory_id
				";
		if($tid!=0)
			$sql.=" where t.territory_id=".$tid."";
			
		$sql.=" order by t.town_name asc";
		return $this->db->query($sql);
	}
	
	/**
	 * function to get invoice details with order list by invoice no 
	 *
	 * @param unknown_type $invno
	 * @return unknown
	 */
	function get_invoicedet_forreturn($invno)
	{
		$invdet = array();
		
		$sql = "select  a.invoice_no,b.id as order_id,b.itemid,c.name,b.quantity,d.packed,d.shipped,d.shipped_on,ifnull(transit_type,0) as transit_type,
						a.invoice_status,a.transid  
					from king_invoice a
					join king_orders b on a.order_id = b.id 
					join king_dealitems c on c.id = b.itemid 
					join shipment_batch_process_invoice_link d on d.invoice_no = a.invoice_no
					join king_transactions t on t.transid = b.transid
					left join pnh_invoice_returns_product_link e on e.order_id = b.id  
					left join pnh_invoice_returns r on r.return_id=e.return_id 
					where a.invoice_no = ? and b.status in (1,2) 
					group by a.order_id  
				";
		$res = $this->db->query($sql,$invno);
		
		if($res->num_rows())
		{
			$invord_list = $res->result_array();
			if(!$invord_list[0]['shipped'])
			{
				$invdet['error'] = 'Invoice is not shipped';
			}else 
			{
				$invdet['itemlist'] = array();
				foreach ($invord_list as  $i=>$ord)
				{
					$sql = "(select e.is_shipped,e.shipped_on,e.is_refunded,is_stocked,is_serial_required,a.id as order_id,b.product_id,product_name,b.qty from king_orders a join m_product_deal_link b on a.itemid = b.itemid join m_product_info c on c.product_id = b.product_id join king_dealitems d on d.id = a.itemid  left join pnh_invoice_returns_product_link e on e.product_id = b.product_id and e.order_id = a.id where a.id = ? and if(d.is_group,(order_product_id = b.product_id),1) group by e.id )
								union
							(select e.is_shipped,e.shipped_on,e.is_refunded,is_stocked,is_serial_required,a.order_id,b.product_id,product_name,d.qty from products_group_orders a join m_product_info b on b.product_id = a.product_id join king_orders c on c.id = a.order_id join m_product_group_deal_link d on d.itemid = c.itemid left join pnh_invoice_returns_product_link e on e.product_id = a.product_id and e.order_id = a.order_id where a.order_id = ? group by e.id )
							";
					$prod_list_res = $this->db->query($sql,array($ord['order_id'],$ord['order_id']));
					
					if(!isset($invdet['itemlist'][$i]))
						$invdet['itemlist'][$i] = array();
					
					$invdet['itemlist'][$i] = $ord;
					
					if(!isset($invdet['itemlist'][$i]['product_list']))
						$invdet['itemlist'][$i]['product_list'] = array();
						
					$prod_list = array();	
					foreach($prod_list_res->result_array() as $prod_det)
					{
						$ttl_pending_inreturn_qty = $this->db->query("select ifnull(sum(qty),0) as t from pnh_invoice_returns_product_link where order_id = ? and product_id = ? and status != 3 ",array($ord['order_id'],$prod_det['product_id']))->row()->t;
						
						$prod_det['pen_return_qty'] = $ttl_pending_inreturn_qty; 
						$prod_det['has_barcode'] = $this->db->query("select count(*) as t from t_stock_info where product_id = ? and product_barcode != '' ", $prod_det['product_id'] )->row()->t;
						$prod_det['mrp'] = $this->db->query("select group_concat(distinct ts.mrp) as mrp from shipment_batch_process_invoice_link a
											join proforma_invoices b on b.p_invoice_no=a.p_invoice_no
											join t_reserved_batch_stock t on t.p_invoice_no=b.p_invoice_no
											join t_stock_info ts on ts.stock_id=t.stock_info_id
											where a.invoice_no=? and t.status=1 and t.product_id=?", array($invno,$prod_det['product_id']) )->row()->mrp;
						$prod_list[] = $prod_det;
					}	
					$invdet['itemlist'][$i]['product_list'] = $prod_list;
					
					$ord_imei_list_res = $this->db->query("select * from t_imei_no where order_id = ? and status = 1 and is_returned = 0 and return_prod_id = 0 ",$ord['order_id']);
					if($ord_imei_list_res->num_rows())
					{
						foreach($ord_imei_list_res->result_array() as $k=>$p_imei_det)
						{
							if(!isset($invdet['itemlist'][$i]['imei_list'][$k]))
								$invdet['itemlist'][$i]['imei_list'][$k] = array();
								
							$invdet['itemlist'][$i]['imei_list'][$k][] = $p_imei_det['imei_no'];  
						}						
					}
					
					// check if the order qty already processedIn 
					
				}
			}
		}else
		{
			$invdet['error'] = 'Invoice not found';
		}
		return $invdet;
	}
}
?>