<?php
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
		$userdet_res = $this->db->query("select * from pnh_api_users where username = ? and password = md5(?) ",array($username,$password));
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
			$cond = ' and username = ? ';
		else if($by == 'id')
			$cond = ' and user_id = ? ';
		
		$userdet_res = $this->db->query("select user_id,username,type from pnh_api_users where 1 ".$cond,array($inp));
		if($userdet_res->num_rows())
			return $userdet_res->row_array();
		return false; 
	}

	/**
	 * function to get user auth key by userid 
	 * @param unknown_type $userid
	 */
	function get_authkey($userid,$auth_key)
	{
		$authkey_res = $this->db->query('select auth_key from pnh_api_user_auth where (user_id = ? or auth_key = ? ) and unix_timestamp(expired_on) > unix_timestamp() ',array($userid,$auth_key));
		
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
		$inp['expired_on'] = date('Y-m-d H:i:s',time()+(24*60*60));
		$inp['created_by'] = 0;
		$inp['created_on'] = date('Y-m-d H:i:s');
		$this->db->insert('pnh_api_user_auth',$inp);

		return $authkey;
	}
	
	/**
	 * function for get the erp api login user details
	 * @return number
	 */
	function get_api_user()
	{
		$api_userdet = $this->db->query("select * from king_admin where username = 'api'");
		if($api_userdet->num_rows())
			return array("userid"=>$api_userdet->row()->id);
		else
			die("Api USER NOT FOUND");
	}
	
	/**
	 * function for get the franchise basic details
	 * @param unknown_type $user_id
	 */
	function get_franchise($fid)
	{
		$sql="select a.franchise_id,a.pnh_franchise_id,a.franchise_name,a.address,a.locality,a.city,a.postcode,a.state,a.credit_limit,
				t.town_name,tt.territory_name,a.login_mobile1,is_prepaid
					from pnh_m_franchise_info a 
					join pnh_api_users b on b.franchise_id=a.franchise_id
					join pnh_towns as t on t.id=a.town_id
					join pnh_m_territory_info tt on tt.id=a.territory_id
					where a.franchise_id=?";
		
		$franchise_det_res=$this->db->query($sql,array($fid));
		
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
	 * function for get the franchise menu list
	 */
	function get_menus_by_franchise($fid)
	{
		$sql="SELECT m.id,m.name AS menu FROM `pnh_franchise_menu_link`a
					JOIN pnh_m_franchise_info b ON b.franchise_id=a.fid
					join pnh_api_users c on c.franchise_id=b.franchise_id
					JOIN pnh_menu m ON m.id=a.menuid
					WHERE a.status=1 AND b.franchise_id=? and m.id not in(124,125) and a.status=1";
	
		$menu_res=$this->db->query($sql,$fid);
	
		if($menu_res->num_rows())
			return $menu_res->result_array();
		else
			return false;
	}
	
	
	
	/**
	 * function for get the brands by menu id
	 */
	function get_brands_by_menu($menu_id,$pg,$bransdlimit)
	{
		$menu_details=array();
		
		$sql="select a.brandid,b.name as brandname from king_deals a 
					join king_brands b on b.id=a.brandid
					join king_dealitems c on c.dealid=a.dealid
				where a.menuid=? and a.publish=1 and c.is_pnh=1 
				group by a.brandid 
				order by b.name";
		
		$menu_res=$this->db->query($sql,$menu_id);
		
		if($menu_res->num_rows())
		{
			$menu_details['ttl_brands']=$menu_res->num_rows();
			
			$sql.=" limit $pg,$bransdlimit";
			
			$menu_details['brand_list']=$this->db->query($sql,$menu_id)->result_array();
			
			
			return $menu_details;
		}
		else
			return false;
	}
	
	/**
	 * function get brand list by category
	 */
	function get_brands_by_cat($catid,$start,$limit)
	{
		$brand_det=array();
		
		$sql="select a.brandid,b.name as brandname from king_deals a 
					join king_brands b on b.id=a.brandid
					join king_dealitems c on c.dealid=a.dealid
				where a.catid=? and a.publish=1 and c.is_pnh=1 
				group by a.brandid 
				order by b.name";
		
		$cat_res=$this->db->query($sql,$catid);
		
		if($cat_res->num_rows())
		{
			$brand_det['total_brands']=$cat_res->num_rows();
			
			$sql.=" limit $start,$limit";
			
			$brand_det['brand_list']=$this->db->query($sql,$catid)->result_array();
			
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
	function get_brands($start,$limit)
	{
		$brand_det=array();
		
		$sql="select id as brandid,name as brandname from king_brands order by name";
		
		$brand_res=$this->db->query($sql);
		
		if($brand_res->num_rows())
		{
			$brand_det['total_brands']=$brand_res->num_rows();
			
			$sql.=" limit $start,$limit";
			
			$brand_det['brand_list']=$this->db->query($sql)->result_array();
			
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
	function get_brands_by_menu_cat($menu_id,$catid,$start,$limit,$full=0)
	{
		$sql="select a.brandid,b.name as brandname from king_deals a 
					join king_brands b on b.id=a.brandid
					join king_dealitems c on c.dealid=a.dealid
				where a.catid=? and a.menuid=? and a.publish=1 and c.is_pnh=1 
				group by a.brandid 
				order by b.name;";
		
		$brand_res=$this->db->query($sql,array($catid,$menu_id));
		
		if($brand_res->num_rows())
		{
			$brand_det['total_brands']=$brand_res->num_rows();
		
			if(!$full)
				$sql.=" limit $start,$limit";
		
			$brand_det['brand_list']=$this->db->query($sql,array($catid,$menu_id))->result_array();
		
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
	function get_categories($start,$limit)
	{
		$cat_det=array();
		$cat_det['ttl_cat']='';
		$cat_det['cat_list']='';
		
		$sql='select a.id,a.name,group_concat(b.id,"::") as sub_catids,group_concat(b.name,"::") as sub_cat_names
					from king_categories a 
					left join king_categories b on b.type=a.id
					group by a.id
				order by a.name';
		
		$cate_res=$this->db->query($sql);
		
		if($cate_res->num_rows())
		{
			$cat_det['ttl_cat']=$cate_res->num_rows();
			
			$sql.=" limit $start,$limit";
			
			$cat_det['cat_list']=$this->db->query($sql)->result_array();
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
	function get_categories_by_menu($menuid,$start,$limit)
	{
		$cat_det=array();
		$cat_det['ttl_cat']='';
		$cat_det['cat_list']='';
		$menu_catids=array();
		
		$sql="select catid from king_deals where menuid=? group by catid";
		
		$menu_cat_res=$this->db->query($sql,$menuid);
		
		if($menu_cat_res->num_rows())
		{
			foreach($menu_cat_res->result_array() as $cid)
				array_push($menu_catids,$cid['catid']);
			
			$sql='select a.id,a.name,group_concat(b.id,"::") as sub_catids,group_concat(b.name,"::") as sub_cat_names
						from king_categories a
						left join king_categories b on b.type=a.id
						where a.id in ('.implode(',',$menu_catids).') or b.id in ('.implode(',',$menu_catids).')
						or a.type in ('.implode(',',$menu_catids).') or b.type in ('.implode(',',$menu_catids).')
					group by a.id
					order by a.name';
			
			$cate_res=$this->db->query($sql);
			
			if($cate_res->num_rows())
			{
				$cat_det['ttl_cat']=$cate_res->num_rows();
			
				$sql.=" limit $start,$limit";
			
				$cat_det['cat_list']=$this->db->query($sql)->result_array();
			}
		}
		
		return $cat_det;
	}
	
	function get_categories_by_brand($brandid,$start,$limit)
	{
		$cat_det=array();
		$cat_det['ttl_cat']='';
		$cat_det['cat_list']='';
		$menu_catids=array();
		
		$sql="select catid from king_deals where brandid=? group by catid";
		
		$brand_cat_res=$this->db->query($sql,$brandid);
		
		if($brand_cat_res->num_rows())
		{
			foreach($brand_cat_res->result_array() as $cid)
				array_push($menu_catids,$cid['catid']);
			
			$sql='select a.id,a.name,group_concat(b.id,"::") as sub_catids,group_concat(b.name,"::") as sub_cat_names
						from king_categories a
						left join king_categories b on b.type=a.id
						where a.id in ('.implode(',',$menu_catids).') or b.id in ('.implode(',',$menu_catids).')
						or a.type in ('.implode(',',$menu_catids).') or b.type in ('.implode(',',$menu_catids).')
						group by a.id
						order by a.name';
			
			$cate_res=$this->db->query($sql);
			
			if($cate_res->num_rows())
			{
				$cat_det['ttl_cat']=$cate_res->num_rows();
			
				$sql.=" limit $start,$limit";
			
				$cat_det['cat_list']=$this->db->query($sql)->result_array();
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
	function get_categories_by_brand_menu($brandid,$menuid,$start,$limit,$full=0)
	{
		$cat_det=array();
		$cat_det['ttl_cat']='';
		$cat_det['cat_list']='';
		$menu_catids=array();
		
		$sql="select catid from king_deals where brandid=? and menuid=? group by catid";
		
		$brandmenu_cat_res=$this->db->query($sql,array($brandid,$menuid));
		
		if($brandmenu_cat_res->num_rows())
		{
			foreach($brandmenu_cat_res->result_array() as $cid)
				array_push($menu_catids,$cid['catid']);
		
			$sql='select a.id,a.name,group_concat(b.id,"::") as sub_catids,group_concat(b.name,"::") as sub_cat_names
						from king_categories a
						left join king_categories b on b.type=a.id
						where a.id in ('.implode(',',$menu_catids).') or b.id in ('.implode(',',$menu_catids).')
						or a.type in ('.implode(',',$menu_catids).') or b.type in ('.implode(',',$menu_catids).')
						group by a.id
						order by a.name';
		
			$cate_res=$this->db->query($sql);
		
			if($cate_res->num_rows())
			{
				$cat_det['ttl_cat']=$cate_res->num_rows();
		
				if(!$full)
					$sql.=" limit $start,$limit";
		
				$cat_det['cat_list']=$this->db->query($sql)->result_array();
			}
		}
		
		return $cat_det;
	}
	
	/**
	 * function for get the deal list
	 * @param unknown_type $brand_id
	 * @param unknown_type $cat_id
	 * @param unknown_type $menu_id
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @return multitype:NULL Ambigous <multitype:, unknown>
	 */
	function get_deals($brand_id=0,$cat_id=0,$menu_id=0,$start,$limit,$pids=0,$srch_data=array(),$gender='',$min_price=0,$max_price=0)
	{
		$cond='';
		$param=array();
		$deal_list=array();
		
		if($brand_id)
		{
			$cond.=" and d.brandid=? ";
			$param[]=$brand_id;
		}
		
		if($cat_id)
		{
			$cond.=" and d.catid=? ";
			$param[]=$cat_id;
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
			$cond.=" and i.orgprice >= ? ";
			$param[]=$min_price;
		}
		
		if($max_price)
		{
			$cond.=" and i.orgprice <= ? ";
			$param[]=$max_price;
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
		
		
		$sql="select pnh_id as pid,i.id as itemid,i.name,m.name as menu,m.id as menu_id,
				i.gender_attr,c.name as category,d.catid as category_id,mc.name as main_category,
				c.type as main_category_id,b.name as brand,d.brandid as brand_id,i.orgprice as mrp,i.price as price,i.store_price,
				i.is_combo, concat('".IMAGES_URL."items/',d.pic,'.jpg') as image_url,concat('".IMAGES_URL."items/small/',d.pic,'.jpg') as small_image_url,d.description,i.shipsin as ships_in,d.keywords 
			from king_deals d 
			join king_dealitems i on i.dealid=d.dealid 
			join king_brands b on b.id=d.brandid 
			join king_categories c on c.id=d.catid 
			left outer join pnh_menu m on m.id=d.menuid 
			left outer join king_categories mc on mc.id=c.type 
			where d.publish=1 and is_pnh=1 $cond order by d.sno asc";
		
		$deal_list_res=$this->db->query($sql,$param);
		
		if($deal_list_res->num_rows())
		{
			$deal_list['ttl_deals']=$deal_list_res->num_rows();
			
			$sql.=" limit $start,$limit";
			
			$deal_det=$this->db->query($sql,$param)->result_array();
			
			foreach($deal_det as $i=>$d)
			{
				$itemid=$d['itemid'];
				$prods=$this->db->query("select p.product_name as name,l.qty from m_product_deal_link l join m_product_info p on p.product_id=l.product_id where l.itemid=?",$d['itemid'])->result_array();
				$deal_det[$i]['products']=array("product"=>$prods);
				$deal_det[$i]['images']=$this->db->query("select CONCAT('".IMAGES_URL."items/',id,'.jpg') as url from king_resources where itemid=? and type=0",$d['itemid'])->result_array();
				$deal_det[$i]['attributes']=array();
				foreach($this->db->query("select group_concat(concat(a.attribute_name,':',v.attribute_value)) as a from m_product_group_deal_link l join products_group_pids p on p.group_id=l.group_id join products_group_attributes a on a.attribute_name_id=p.attribute_name_id join products_group_attribute_values v on v.attribute_value_id=p.attribute_value_id where l.itemid=? group by p.product_id",$itemid)->result_array() as $i2=>$p)
					$deal_det[$i]['attributes']['attr'.($i2+1)]=$p['a'];
				
				$min_max_price_det=$this->get_min_max_price($brand_id,$cat_id,$menu_id,$start,$limit,$pids,$srch_data,$gender);
				$deal_det[$i]['min_price']=$min_max_price_det['min_price'];
				$deal_det[$i]['max_price']=$min_max_price_det['max_price'];
			}
			
			$min_max_price_det=$this->get_min_max_price($brand_id,$cat_id,$menu_id,$start,$limit,$pids,$srch_data,$gender);
			
			$deal_list['deals_list']=$deal_det;
		}
		
		return $deal_list; 
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
			$cond.=" and d.brandid=?";
			$param[]=$brand_id;
		}
		
		if($cat_id)
		{
			$cond.=" and d.catid=?";
			$param[]=$cat_id;
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
	 */
	function get_deal($pid=0)
	{
		$sql="select pnh_id as pid,i.gender_attr,i.id as itemid,i.name,d.tagline,c.name as category,m.name as menu,d.menuid as menu_id,
					d.catid as category_id,mc.name as main_category,c.type as main_category_id,b.name as brand,d.brandid as brand_id,
					i.orgprice as mrp,i.price as price,i.store_price,i.is_combo,concat('".IMAGES_URL."items/',d.pic,'.jpg') as image_url,
					d.description,i.shipsin as ships_in,d.keywords,i.live as is_stock,d.publish as is_enabled,concat('".IMAGES_URL."items/small/',d.pic,'.jpg') as small_image_url 
					from king_deals d 
					join king_dealitems i on i.dealid=d.dealid 
					join king_brands b on b.id=d.brandid 
					join king_categories c on c.id=d.catid 
					left outer join pnh_menu m on m.id=d.menuid 
					left outer join king_categories mc on mc.id=c.type 
					where is_pnh=1 and i.pnh_id =? and i.pnh_id!=0 order by d.sno asc";
		
		$deal_res=$this->db->query($sql,$pid);
		
		if($deal_res->num_rows())
		{
			$deal_info=$deal_res->row_array();
			
			$deal_info['images']=$this->db->query("select CONCAT('".IMAGES_URL."items/',id,'.jpg') as url,CONCAT('".IMAGES_URL."items/small/',id,'.jpg') as small_img_url from king_resources where itemid=? and type=0",$deal_info['itemid'])->result_array();
			
			$attr_det=$this->db->query("select group_concat(concat(LOWER(a.attribute_name),':',v.attribute_value)) as a,group_concat(concat(LOWER(a.attribute_name_id),':',v.attribute_value_id)) as aid from m_product_group_deal_link l join products_group_pids p on p.group_id=l.group_id join products_group_attributes a on a.attribute_name_id=p.attribute_name_id join products_group_attribute_values v on v.attribute_value_id=p.attribute_value_id where l.itemid=? group by p.product_id",$deal_info['itemid'])->result_array();
			
			$attr_id_config=array();
			$attr_config=array();
			if($attr_det)
			{
				foreach($attr_det as $attr)
				{
					$attr_name=explode(":",$attr['a']);
					$attr_ids=explode(":",$attr['aid']);
					
					if(!isset($attr_config[$attr_name[0]]))
					{
						$attr_config[strtolower($attr_name[0])]=array();
						$attr_config[strtolower($attr_name[0])]['attribute_name']=$attr_name[0];
						$attr_config[strtolower($attr_name[0])]['attribute_id']=$attr_ids[0];
						$attr_config[strtolower($attr_name[0])]['attribute_values']=array();
					}
					
					$attr_config[strtolower($attr_name[0])]['attribute_values'][$attr_ids[1]]=$attr['a'];
				}
				
				$deal_info['attributes']=$attr_config;
				
				//attribute ids group config
				foreach($attr_det as $attr)
				{
					$attr_nameid=explode(":",$attr['aid']);
					
					if(!isset($attr_id_config[$attr_nameid[0]]))
						$attr_id_config[$attr_nameid[0]]=array();
					
					array_push($attr_id_config[$attr_nameid[0]],$attr_nameid[1]);
				}
				
				$deal_info['attributes_id']=$attr_id_config;
			}
			
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
		$this->db->query("update pnh_api_franchise_cart_info set status=0,updated_on=? where franchise_id=? and pid=? and status=1",array(cur_datetime(),$fid,$pid));
		
		if($this->db->affected_rows())
			return true;
		else
			return false;
	}
	
	/**
	 * get the cart items
	 * @param unknown_type $fid
	 */
	function get_cart_items($fid,$pid=0)
	{
		$cond='';
		$param=array();
		$param[]=$fid;
		if($pid)
		{
			$cond=' and pid=? ';
			$param[]=$pid;
		}
		
		
		
		$sql="select pid,qty,attributes from pnh_api_franchise_cart_info where franchise_id=? and status=1 $cond";
		
		$cart_item_res=$this->db->query($sql,$param);
		
		if($cart_item_res->num_rows())
		{
			$cart_item_det=$cart_item_res->result_array();
			
			foreach($cart_item_det as $i=>$cart)
			{
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
				}
				
				//get the cart product details
				$cart_item_det[$i]['deal_deails']=$this->get_deal($cart['pid']);
			}
			
			return $cart_item_det;
			
		}else{
			return false;
		}
	}
	
	/**
	 * function for get the memebers details
	 */
	function get_member_details($memid)
	{
		$sql="select * from pnh_member_info where pnh_member_id=?";
		
		$mem_det_res=$this->db->query($sql,$memid);
		
		if($mem_det_res->num_rows())
		{
			$member_det=$mem_det_res->result_array();
			foreach($member_det as $i=> $m)
			{
				$order=$this->db->query("select count(1) as n,sum(amount) as t from king_transactions where transid in (select transid from king_orders where userid=?)",$m['user_id'])->row_array();
				$member_det[$i]['total_orders']=$order['n'];
				$member_det[$i]['total_ordered_amount']=$order['t'];
			}
			
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
	 */
	function add_new_member($fid,$member_name,$mobile_no)
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
			$this->db->query("insert into pnh_member_info(pnh_member_id,user_id,first_name,last_name,mobile,franchise_id,created_by,created_on)values(?,?,?,?,?,?,?,?)",array($membr_id,$userid,$member_name,'',$mobile_no,$fid,0,time()));
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
	 */
	function get_inv_returns_details($fid,$start,$limit)
	{
		$output=array("return_details"=>'',"ttl_returns"=>0);
		$param=array($fid,$start*1,$limit*1);
		
		$sql="select c.transid,e.franchise_id,e.franchise_name,a.return_id,a.invoice_no,return_by,returned_on,
					b.name as handled_by_name,a.status,a.order_from,d.partner_id  
					from pnh_invoice_returns a 
					join king_admin b on a.handled_by = b.id 
					join king_invoice c on c.invoice_no = a.invoice_no
					join king_transactions d on d.transid = c.transid
					left join pnh_m_franchise_info e on e.franchise_id = d.franchise_id
					where 1 and e.franchise_id=? 
					order by returned_on desc";
		
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
				foreach($return_det as $d)
				{
					if(!isset($output['return_details'][$d['return_id']]))
						$output['return_details'][$d['return_id']]=array();
					
					$output['return_details'][$d['return_id']]['return_id']=$d['return_id'];
					$output['return_details'][$d['return_id']]['transid']=$d['transid'];
					$output['return_details'][$d['return_id']]['invoice_no']=$d['invoice_no'];
					$output['return_details'][$d['return_id']]['returned_date']=format_datetime($d['returned_on']);
					$output['return_details'][$d['return_id']]['status']=$d['status'];
					$output['return_details'][$d['return_id']]['status_config']=$this->config->item('return_request_cond');
					
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
					
					$output['return_details'][$d['return_id']]['products']=$this->db->query($sql,$d['return_id'])->result_array();
					$output['return_details'][$d['return_id']]['prd_status_config']=$this->config->item('return_process_cond');
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
	 * function for get the updated deals
	 */
	function get_updated_deals($versions=array())
	{
		$output=array();
		$version_ids=array();
		
		if($versions)
		{
			$latest_vesrion_det=end($versions);
			$version_detatils=array("version"=>'',"created_on"=>'',"latest_version"=>'',"products"=>'');
			
			foreach($versions as $v)
				array_push($version_ids,$v['id']);

			$sql="select * from ( 
									select b.id,a.version,b.item_id,b.mrp,b.price,b.is_publish,b.is_new 
											from m_apk_version a
											join m_apk_version_deal_link b on a.id=b.version_id
											where b.version_id in (".implode(",",$version_ids).")
									order by b.id desc 
								) as g
								group by g.item_id ";
			
			$deals_res=$this->db->query($sql);
			
			if($deals_res->num_rows())
			{
				$version_detatils["latest_version"]=$latest_vesrion_det['version'];
				$version_detatils["created_on"]=$latest_vesrion_det["created_on"];
				
				$deal_list = array();
				foreach($deals_res->result_array() as $row)
				{
					$tmp = array();
					$tmp = $row;
					
					// if deal is new then fetch deal name,mp,price,status,image,cat,parent,parent_linked_cats
					if($tmp['is_new'])
					{
						$res_d = $this->db->query("select b.id as item_id,b.pnh_id as pid,menuid as pmenu_id,e.name as pmenu,b.name as pname,catid as pcat_id,c.name as pcat,
															cp.id as parent_cat_id,cp.name as parent_cat,brandid as pbrand_id,d.name as pbrand,
															b.orgprice as pmrp,b.price as pprice,publish as is_sourceable,
															b.gender_attr,b.url,b.is_combo,a.description as pdesc,a.keywords as kwds,
															a.pic as pimg,b.pic as pimages,menuid as pimg_path,'' as attributes,
															b.shipsin as shipin
														from king_deals a
														join king_dealitems b on a.dealid = b.dealid 
														join king_categories c on c.id = a.catid 
														left join king_categories cp on cp.id = c.type
														join king_brands d on d.id = a.brandid 
														join pnh_menu e on e.id = a.menuid 
														where b.id = ? and b.pnh_id != 0   ",$row['item_id']);
						if($res_d->num_rows())
						{
							$row_d = $res_d->row_array();
							
							foreach($row_d as $k1=>$v1)
								$tmp[$k1] = htmlspecialchars($v1);
							 
						}
					}
					$deal_list[] = $tmp;
				}
								
				$version_detatils["products"]=$deal_list;
				array_push($output,$version_detatils);
			}
		}
		
		return $output;
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

} 

?>