<?php
error_reporting(E_ALL);
/**
 * Storeking API 
 * @author Madhan
 */
class Api extends Controller
{
	function __construct()
	{
		parent::Controller();
		
		//cross domain fix 
		header('Access-Control-Allow-Origin: *');
		
		$this->load->model('erpmodel','erpm');
		
		$this->load->model('order_model');
		$this->load->model('resource_model');
		$this->load->model('franchise_model');
		$this->load->model('member_model');
		$this->load->model('api_model','apim');
		
		
	}

	/**
	 * function to view storeking api documentation
	 */
	function doc()
	{
		$data['title']='Storeking API Documentation';
		$this->load->view('api_doc',$data);
	}

	/**
	 * function to handle output details [json]
	 *
	 * @param unknown_type $type [json]
	 * @param unknown_type $status [true|false]
	 * @param unknown_type $response [Array]
	 *
	 * @retrun json content
	 */
	function _output_handle($type='json',$status=false,$response=array())
	{
		$op = array();
		if($status)
		{
			$op['status'] = 'success';
			$op['response'] = $response;
		}else
		{
			$op['status'] = 'error';
			$op['error_code'] = $response['error_code'];
			$op['error_msg'] = strip_tags($response['error_msg']);
			$op['response'] = $response;
			
		}
		
		echo json_encode($op);
		die();
	}

	/**
	 * function to check if username is valid
	 */
	function _chk_username($name)
	{
		$userdet = $this->apim->get_userdet($name,'name');
		if(!$userdet)
		{
			$this->form_validation->set_message('_chk_username','Invalid Username or mobile no entered');
			return false;
		}
		return true;
	}

	/**
	 * function to check if username is valid
	 */
	function _chk_userlogin($password)
	{
		$username = $this->input->post('username');
		$userdet = $this->apim->get_userdet($username,'name');
		if($userdet)
		{
			if(!$this->apim->is_valid_login($username,$password))
			{
				$this->form_validation->set_message('_chk_userlogin','Invalid Password entered');
				return false;
			}
		}
		return true;
	}

	/**
	 * function to process login authentication
	 *
	 */
	function login()
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');

		$this->load->library('form_validation');
		$this->form_validation->set_rules('username','Username','required|callback__chk_username');
		$this->form_validation->set_rules('password','Password','required|callback__chk_userlogin');
		if($this->form_validation->run() === FALSE)
		{
			$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>validation_errors()));
		}else
		{
			$empid='';
			$franchise_id='';
			
			$userdet = $this->apim->is_valid_login($username,$password);

			//check logged type [Exp:franchise or employe ] and set the loged type flag
			if($userdet['type']==0)
				$franchise_id=$userdet['franchise_id'];
			else
				$empid=$userdet['employee_id'];
			
			// generate authkey
			$authkey = $this->apim->gen_authkey($userdet['user_id']);
				
			$this->_output_handle('json',true,array('authkey'=>$authkey,'user_id'=>$userdet['user_id'],'emp_id'=>$empid,'franchise_id'=>$franchise_id));
		}

	}

	/**
	 * Request to handle forgot password - sms will be sent with password to mobile no.
	 * @author Shivaraj <shivaraj@storeking.in>
	 */
	function forgot_pwd()
	{
		$username = $this->input->post('username');// or i.e Username=Mobile number
		$this->load->library('form_validation');
		$this->form_validation->set_rules('username','Username','required|callback__chk_username');
		
		if($this->form_validation->run() === FALSE)
		{
			$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>validation_errors()));
		}else
		{
				$userdet = $this->apim->get_userdet($username,'name');
				$user_id = $userdet['user_id'];

				//update new pwd to db
				$resp = $this->apim->update_password($user_id);
				
				if($resp['status']==true)
				{
					$this->_output_handle('json',true,$resp['msg']);
				}
				else
				{
					$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>$resp['msg']));
				}
		}
	}
	
	/**
	 * function to validation
	 */
	function _is_validauthkey()
	{
		$authkey = (string)$this->input->post('authkey');

		if(!$this->apim->get_authkey('XXXXX',$authkey))
			$this->_output_handle('json',false,array('error_code'=>2001,'error_msg'=>"Invalid Authkey"));

	}


	/**
	 * fucntion to get user details by auth key
	 */
	function userdet()
	{
		// check for validf auth key
		$this->_is_validauthkey();
		
		$authkey = $this->input->post('authkey');
		$uid = $this->input->post('user_id');

		$userdet = $this->apim->get_userdet($uid,'id');
		if($userdet)
			$this->_output_handle('json',true,array('authkey'=>$authkey,'user_id'=>$userdet));
		else
			$this->_output_handle('json',false,array('error_code'=>2002,'error_msg'=>"Invalid UserID Provided"));
	}
	
	/**
	 * fucntion to get user details by auth key
	 */
	function validauth()
	{
		// check for validf auth key
		$authkey = $this->input->post('authkey');
		if($this->apim->get_authkey('XXXXX',$authkey))
			$this->_output_handle('json',true,array('authkey'=>$authkey));
		else
			$this->_output_handle('json',false,array('error_code'=>2001,'error_msg'=>"Invalid AuthKey"));
	}
	
	/**
	 * function for get the franchise deatils
	 */
	function get_franchise_info()
	{
		$this->_is_validauthkey();
		
		$userid=$this->input->post('user_id');
		$franchise_id=$this->input->post('franchise_id');
		$this->load->model('erpmodel','erpm');
		
		$franchise_details=array();
		
		//get the franchise basic details
		$franchise=$this->apim->get_franchise($franchise_id);
		
		if($franchise)
		{
			$franchise_details['franchise_id']=$franchise['franchise_id'];
			$franchise_details['franchise_name']=$franchise['franchise_name'];
			$franchise_details['contact_no']=$franchise['login_mobile1'];
			$franchise_details['territory']=$franchise['territory_name'];;
			$franchise_details['town_name']=$franchise['town_name'];
			$franchise_details['is_prepaid']=$franchise['is_prepaid'];
			
		}else{
			$this->_output_handle('json',false,array('error_code'=>2003,'error_msg'=>"No data found"));
		}
		
		// get the franchie menu list
		$franchise_details['menus']=array();
		$menu=$this->apim->get_menus_by_franchise($franchise_id);
		foreach($menu as $m)
			array_push($franchise_details['menus'],$m);
		
		//get franchise account summary
		$franchise_details['payment_det']=array();
		$account_summary=$this->erpm->get_franchise_account_stat_byid($franchise_id);
		$credit_note_amt = $account_summary['credit_note_amt'];
		$shipped_tilldate = $account_summary['shipped_tilldate'];
		$paid_tilldate = $account_summary['paid_tilldate'];
		$acc_adjustments_val = $account_summary['acc_adjustments_val'];
		$franchise_details['payment_det']['pending']=$shipped_tilldate-($paid_tilldate+$acc_adjustments_val+$credit_note_amt);
		$franchise_details['payment_det']['uncleared']=$account_summary['uncleared_payment'];
		
		//get franchise cart details
		$franchise_details['cart_total']=$this->apim->get_franchise_cart_info($franchise_id);
		$this->_output_handle('json',true,array("franchise_details"=>$franchise_details));
	}
	
	/**
	 * function for get franchise menu
	 */
	function get_menus()
	{
		$this->_is_validauthkey();
		
		$userid=$this->input->post('user_id');
		$franchise_id=$this->input->post('franchise_id');
		
		if(!$userid)
			$this->_output_handle('json',false,array('error_code'=>2001,'error_msg'=>"Invalid user"));
		
		$menu_list=array();
		
		$menu=$this->apim->get_menus($franchise_id);
		
		if($menu)
		{
			foreach($menu as $m)
				array_push($menu_list,$m);
			
			$this->_output_handle('json',true,array("menu_list"=>$menu_list));
		}else{
			$this->_output_handle('json',false,array('error_code'=>2003,'error_msg'=>"No data found pls contact admin"));
		}
		
	}
	
	/**
	 * function for get franchise menu
	 */
	function get_franchise_menus()
	{
		$this->_is_validauthkey();
		
		$userid=$this->input->post('user_id');
		$franchise_id=$this->input->post('franchise_id');
		
		if(!$userid)
			$this->_output_handle('json',false,array('error_code'=>2001,'error_msg'=>"Invalid user"));
		
		$menu=$this->apim->get_menus_by_franchise($franchise_id);
		
		if($menu)
		{
			$this->_output_handle('json',true,array("menu_list"=>$menu));
		}else
		{
			$this->_output_handle('json',false,array('error_code'=>2003,'error_msg'=>"No data found pls contact admin"));
		}
		
	}
	
	/**
	 * function get the brand list
	 * return brands list or brands cat or brands by menu
	 */
	function get_brand_list()
	{
		$this->_is_validauthkey();
		
		$menuid=$this->input->post('menu_id');
		$catid=$this->input->post('cat_id');
		$start=$this->input->post('start');
		$limit=$this->input->post('limit');
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		$full=$this->input->post('full');
		$alpha_sort=$this->input->post('alpha_sort');
		$pid=$this->input->post('pnh_id');
		$top_brands=$this->input->post('top_brands');
		
		$brand_list=array();
		$full_brands=1;
		$menu_cat=0;
		
		if(!$start)
			$start=0;
		
		if(!$limit)
			$limit=10;
		
		if(!$fid)
			$fid=0;

		if(!$pid)
			$pid=0;
		
		
		
		if($menuid && $catid)
		{
			$full_brands=0;
			$menu_cat=1;
			
			$brands_list_det=$this->apim->get_brands_by_menu_cat($fid,$menuid,$catid,$start,$limit,$full,$alpha_sort,$top_brands); 
		}
		
		if(!$menu_cat)
		{
			if($menuid)
			{
				$full_brands=0;
				
				$brands_list_det=$this->apim->get_brands_by_menu($fid,$menuid,$start,$limit,$alpha_sort,$top_brands);
				
			}
			
			if($catid)
			{
				$full_brands=0;
				
				$brands_list_det=$this->apim->get_brands_by_cat($fid,$catid,$start,$limit,$alpha_sort,$top_brands);
			}
		}
		
		if($full_brands)
			$brands_list_det=$this->apim->get_brands($start,$limit,$alpha_sort,$top_brands);
		
		
		if(!$brands_list_det['brand_list'])
			$this->_output_handle('json',true,array("brand_list"=>''));
		
		$brand_list=$brands_list_det['brand_list'];
		
		$this->_output_handle('json',true,array("brand_list"=>$brand_list));
		
	}
	
	/**
	 * function for get the category list
	 */
	
	function get_category_list()
	{
		$this->_is_validauthkey();
		
		$fid=$this->input->post('franchise_id');
		$menuid=$this->input->post('menu_id');
		$brandid=$this->input->post('brand_id');
		$start=$this->input->post('start');
		$limit=$this->input->post('limit');
		$userid=$this->input->post('user_id');
		$full=$this->input->post('full');
		$alpha_sort=$this->input->post('alpha_sort');
		$top_cats=$this->input->post('top_cats');
		$brand_list=array();
		$full_cat=1;
		$menu_brand=0;
		
		if(!$start)
			$start=0;
		
		if(!$limit)
			$limit=10;
		
		if(!$fid)
			$fid=0;
		
		
		if($brandid && $menuid)
		{
			$full_cat=0;
			$menu_brand=1;
			
			$category_list_det=$this->apim->get_categories_by_brand_menu($fid,$brandid,$menuid,$start,$limit,$full,$alpha_sort,$top_cats);
		}
		
		
		if(!$menu_brand)
		{
			if($menuid)
			{
				$full_cat=0;
				
				$category_list_det=$this->apim->get_categories_by_menu($fid,$menuid,$start,$limit,$alpha_sort,$full,$top_cats);
			}
			
			
			if($brandid)
			{
				$full_cat=0;
				
				$category_list_det=$this->apim->get_categories_by_brand($fid,$brandid,$start,$limit,$alpha_sort,$full,$top_cats);
			}
		}
		
		if($full_cat)
		{
			$category_list_det=$this->apim->get_categories($fid,$start,$limit,$alpha_sort,$full);
		}
		
		
		//format the category list by parent cat and child cat
		$category_cofig=array();
		if($category_list_det['cat_list'])
		{
			foreach($category_list_det['cat_list'] as $cat)
			{
				if(!isset($category_cofig[$cat['id']]))
				{
					$category_cofig[$cat['id']]=array();
					$category_cofig[$cat['id']]['id']='';
					$category_cofig[$cat['id']]['name']='';
					$category_cofig[$cat['id']]['subcat']=array();
				}
			
				$category_cofig[$cat['id']]['id']=$cat['id'];
				$category_cofig[$cat['id']]['name']=$cat['name'];
			
				if($cat['sub_catids'])
				{
					$chil_catids=array_filter(explode('::',trim(str_ireplace(",","",$cat['sub_catids']))));
					$chil_catname=array_filter(explode('::',trim(str_ireplace(",","",$cat['sub_cat_names']))));
			
					foreach($chil_catids as $i=>$id)
					{
						if(!isset($category_cofig[$cat['id']]['subcat'][$id]))
						{
							$category_cofig[$cat['id']]['subcat'][$id]=array();
							$category_cofig[$cat['id']]['subcat'][$id]['id']=$id;
							$category_cofig[$cat['id']]['subcat'][$id]['name']=$chil_catname[$i];
						}
					}
				}
			}
			
			$this->_output_handle('json',true,array("category_list"=>$category_cofig));
		
		}else{
			$this->_output_handle('json',true,array("category_list"=>''));
		}
	}
	
	/**
	 * function for get the deal list
	 */
	function get_deal_list()
	{
		$this->_is_validauthkey();
		
		$menuid=$this->input->post('menu_id');
		$brandid=$this->input->post('brand_id');
		$catid=$this->input->post('category_id');
		$start=$this->input->post('start');
		$limit=$this->input->post('limit');
		$userid=$this->input->post('user_id');
		$gender=$this->input->post('gender');
		$min_price=$this->input->post("min_price");
		$max_price=$this->input->post("max_price");
		$pids=$this->input->post("prd_id");
		
		if(!$start)
			$start=0;
		
		if(!$limit)
			$limit=10;
		
		$deal_det=$this->apim->get_deals($brandid,$catid,$menuid,$start,$limit,$pids,$srch_data=array(),$gender,$min_price,$max_price);
		
		if($deal_det['deals_list'])
			$this->_output_handle('json',true,array("deal_list"=>$deal_det['deals_list'],'total_deals'=>$deal_det['ttl_deals']));
		else
			$this->_output_handle('json',true,array("deal_list"=>'','total_deals'=>0));
	}
	
	/**
	 * function to get the deal details
	 */
	function get_deal_info()
	{
		$this->_is_validauthkey();
		
		$pid=$this->input->post('pid');
		$userid=$this->input->post('user_id');
		
		$deal_info=$this->apim->get_deal($pid);
		
		if($deal_info)
			$this->_output_handle('json',true,array("deal_info"=>$deal_info));
		else
			$this->_output_handle('json',false,array('error_code'=>2003,'error_msg'=>"No data found"));
	}
	
	/**
	 * function to add the products to cart
	 */
	function cart_add()
	{
		$this->_is_validauthkey();
		$pid=$this->input->post('pid');
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		$attributes=$this->input->post('attributes');
		$qty=$this->input->post('qty');
		$multi=$this->input->post("multiple");
		$cart_items=$this->input->post('cart_items');
		
		if($multi)
		{
			$this->cart_add_multi($cart_items);
		}else{
		
			if($attributes)
				$attributes=implode("|",$attributes);
			else
				$attributes='';
			
			//check this item already in cart for same franchise
			$existing_res=$this->db->query("select * from pnh_api_franchise_cart_info where pid=? and franchise_id=? and status=1",array($pid,$fid));
			
			if($existing_res->num_rows())
			{
				$existing_item_det=$existing_res->row_array();
				
				//attributes are same
				$attr_deff=array_diff(explode("|",$attributes),explode("|",$existing_item_det["attributes"]));
				
				if(!count($attr_deff))
				{
					if($this->apim->update_cart($pid,$userid,$fid,$qty,$attributes))
						$this->_output_handle('json',true,array("cart_msg"=>"Your item added to cart"));
					else
						$this->_output_handle('json',false,array('error_code'=>2004,'error_msg'=>"Sorry!Please try again"));
						
				}
			}
			
			
			if($this->apim->add_to_cart($pid,$userid,$fid,$qty,$attributes))
			{
				$this->_output_handle('json',true,array("cart_msg"=>"Your item added to cart"));
			}else{
				$this->_output_handle('json',false,array('error_code'=>2004,'error_msg'=>"Sorry!Please try again"));
			}
		}
	}
	
	/**
	 * function for add the multiple items to cart
	 */
	protected  function cart_add_multi($cart_items)
	{
		$this->_is_validauthkey();
		$fid=$this->input->post('franchise_id');
		$userid=$this->input->post('user_id');
		$add_count=0;
		$attributes='';
		$ttl_items=count($cart_items);
		
		if(!is_array($cart_items ))
			$this->_output_handle('json',false,array('error_code'=>2041,'error_msg'=>"Your given multiple items input not valid"));
		
		foreach($cart_items as $i=>$cart)
		{
			if(!isset($cart['pid']) || !isset($cart['qty']))
				$this->_output_handle('json',false,array('error_code'=>2042,'error_msg'=>"Your given multiple items input array index $i not valid"));
			
			if(isset($cart['attributes']))
			{
				if(is_array($cart['attributes']))
					$attributes=$cart['attributes'];
				else{
					$attributes='';
					$this->_output_handle('json',false,array('error_code'=>2042,'error_msg'=>"Your given multiple items input array index $i not valid"));
				}
					
			}
			
			
			$pid=$cart['pid'];
			$qty=$cart['qty'];
			
			if($attributes)
				$attributes=implode("|",$attributes);
			else
				$attributes='';
			
			//check this item already in cart for same franchise
			$existing_res=$this->db->query("select * from pnh_api_franchise_cart_info where pid=? and franchise_id=? and status=1",array($pid,$fid));
			
			if($existing_res->num_rows())
			{
				$existing_item_det=$existing_res->row_array();
			
				//attributes are same
				$attr_deff=array_diff(explode("|",$attributes),explode("|",$existing_item_det["attributes"]));
			
				if(!count($attr_deff))
				{
					if($this->apim->update_cart($pid,$userid,$fid,$qty,$attributes))
						$add_count+=1;
				}
					
			}else{
				
				if($this->apim->add_to_cart($pid,$userid,$fid,$qty,$attributes))
					$add_count+=1;
			}
		}
		
		if($add_count==$ttl_items)
		{
			$this->_output_handle('json',true,array("cart_msg"=>"Your item added to cart"));
		}else{
			$this->_output_handle('json',false,array('error_code'=>2004,'error_msg'=>"Sorry!Please try again"));
		}
	}
	
	
	/**
	 * function for revoce the item from cart
	 */
	function cart_remove()
	{
		$this->_is_validauthkey();
		$pid=$this->input->post('pid');
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		
		if($this->apim->remove_to_cart($pid,$fid))
		{
			$this->_output_handle('json',true,array("cart_msg"=>"Your item successfully removed from cart"));
		}else{
			$this->_output_handle('json',false,array('error_code'=>2004,'error_msg'=>"Sorry!Please try again"));
		}
	}
	
	/**
	 * function for update the cart items
	 */
	function cart_update()
	{
		$this->_is_validauthkey();
		
		$pid=$this->input->post('pid');
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		$attributes=$this->input->post('attributes');
		$qty=$this->input->post('qty');
		
		$cart_updated=$this->apim->update_cart_item_attr($pid,$userid,$fid,$qty,$attributes);
		
		if($cart_updated)
			$this->_output_handle('json',true,array("cart_update_status"=>'success'));
		else
			$this->_output_handle('json',false,array('error_code'=>2006,'error_msg'=>"Cart item not updated"));
		
	}
	
	/**
	 * function get the cart items list
	 */
	function get_cart_item_list()
	{
		$this->_is_validauthkey();
		
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		$pid=$this->input->post('pid');
		$member_type=$this->input->post('member_type');
		$mem_id=$this->input->post("member_id");
		$new_mem=0;
	
		//member validation
		if($member_type)
		{
			if($member_type==1)
			{
				if(!$mem_id)
					$this->_output_handle('json',false,array('error_code'=>2046,'error_msg'=>"Member id require"));
				else{
					$m_userid=$this->db->query("select user_id from pnh_member_info where pnh_member_id=?",$mem_id)->row()->user_id;
					
					if(!$m_userid)
						$this->_output_handle('json',false,array('error_code'=>2007,'error_msg'=>"No member available for given member id"));
					else{
						$ttl_orders=$this->db->query("SELECT COUNT(DISTINCT(transid)) AS l FROM king_orders WHERE userid=? AND STATUS NOT IN (3)",$m_userid)->row()->l;
						
						if($ttl_orders==0)
							$new_mem=1;
					}
				}
			}else if($member_type==2)
			{
				$new_mem=1;
			}else if($member_type==3){
				$mem_id=0;
			}
		}
		
		
		$this->load->model('erpmodel','erpm');
		
		$cart_items_det=$this->apim->get_cart_items($fid,$pid);
		
		$cart_items_det=$this->check_cart_items_margin($fid,$cart_items_det);
		
		if($cart_items_det)
		{
			foreach($cart_items_det as $i=>$c)
			{
				$cart_items_det[$i]['new_mem']=$new_mem;
				$cart_items_det[$i]['mem_type']=$member_type;
				$cart_items_det[$i]['mem_type_config']=array("1"=>'Old member',"2"=>"New member","3"=>"key member");
				$deal_det=$this->get_cart_deal_configs($c['deal_deails'],$fid,$mem_id);
				
				//validate max order qty
				if($deal_det['max_allowed_qty'])
				{
					if($c['qty'] > $deal_det['max_ord_qty'])
					{
						$cart_items_det[$i]['place_order']=0;
						$cart_items_det[$i]['cart_msg']='Product Id '.$deal_det['pid'].' Maximum '.$deal_det['max_ord_qty'].' Qty can be Ordered';
					}
				}
				
				//validate product is sourceable or not sourceable
				if(!count($deal_det['allow_order']) && $deal_det['is_sourceable']==0)
				{
					$cart_items_det[$i]['place_order']=0;
					$cart_items_det[$i]['cart_msg']='Product Id '.$deal_det['pid'].' is disabled';
				}
				
				//validate key member products in cart
				if($member_type==3 && $deal_det['menu_id']!="112")
				{
					$cart_items_det[$i]['place_order']=0;
					$cart_items_det[$i]['cart_msg']='Member Registration is required because Other than Electronic items are there in the Cart';
				}
				
				$deal_det['new_member_value']=0;
				$deal_det['existing_member_margin']=0;
				$deal_det['insurance_types']='';
				//get the insurance value
				if($deal_det['has_insurance'])
				{
					$deal_det['new_member_value']=$deal_det['insurance_value'];
					$deal_det['new_member_margin']=$deal_det['insurance_margin'];
					$deal_det['insurance_types']=$this->db->query("select * from insurance_m_types order by name asc")->result_array();
				}
				
				$cart_items_det[$i]['deal_deails']=$deal_det;
				
				/*//insurance and member fee details preparation
				$cart_items_det[$i]['insurance_det']=array("insurance_fee"=>'',"msg"=>"","ins_confirm_need"=>"");
				$cart_items_det[$i]['member_fee_det']=array("member_fee"=>'',"msg"=>"");
				
				if($deal_det["has_insurance"]==1)
				{
					$cart_items_det[$i]['insurance_det']['msg']="yes";
					if($deal_det["insurance_value"]!=null && $deal_det["insurance_margin"]!=null && $deal_det["key_member"]==0)
					{
						$cart_items_det[$i]['insurance_det']['ins_confirm_need']=1;
						$cart_items_det[$i]['insurance_det']['insurance_fee']=0;
					}
				}
				
				
				if($member_type==3)//key member
				{
					//member fee det
					$cart_items_det[$i]['member_fee_det']['member_fee']=$deal_det["mem_fee"];
					if($deal_det["mem_fee"]==0)
					{
						$cart_items_det[$i]['member_fee_det']['member_fee']=0;
						$cart_items_det[$i]['member_fee_det']['msg']='No member fee';
					
					}else if($deal_det["mem_fee"] > 0)
					{
						$cart_items_det[$i]['member_fee_det']['member_fee']=$deal_det["mem_fee"]*$c['qty'];
						$cart_items_det[$i]['member_fee_det']['msg']=$deal_det["mem_fee"].'/qty';
					}
					
					//insurance det
					$cart_items_det[$i]['insurance_det']['insurance_fee']=$deal_det["insurance_fee"];
					
					if($deal_det["insurance_fee"]==0)
					{
						$cart_items_det[$i]['insurance_det']['insurance_fee']=$deal_det["insurance_fee"]*$c['qty'];
						$cart_items_det[$i]['insurance_det']['msg']="free insurance";
					}else if($deal_det["insurance_fee"]==null)
					{
						$deal_det["insurance_fee"]=0;
						$cart_items_det[$i]['insurance_det']['insurance_fee']=$deal_det["insurance_fee"]*$c['qty'];
						$cart_items_det[$i]['insurance_det']['msg']="no insurance";
					}else if($deal_det["insurance_fee"] > 0)
					{
						$cart_items_det[$i]['insurance_det']['insurance_fee']=$deal_det["insurance_fee"]*$c['qty'];
						$cart_items_det[$i]['insurance_det']['msg']=$deal_det["insurance_fee"] ."/qty";
					}
					
				}*/
			}
			
			$this->_output_handle('json',true,array("cart_items"=>$cart_items_det));
		}
		else
			$this->_output_handle('json',false,array('error_code'=>2005,'error_msg'=>"No more items in cart"));
		
	}
	
	/**
	 * function for get the deal configs like deal insurance settings and margins
	 * this function want to update if erp controller pnh_jx_loadpnhprod function updated.
	 */
	private function get_cart_deal_configs($prod,$fid,$mid='')
	{
		$allow_for_fran=1;
		$pid=$prod['pid'];
		$prod['pid'];
		$prod['mem_fee']=0;
		$prod['insurance_fee']=null;
		
		$sql="select p.is_sourceable from m_product_info p 
						left join m_product_deal_link dl on dl.product_id=p.product_id
						LEFT JOIN `m_product_group_deal_link` g ON g.itemid=?
						LEFT JOIN `products_group_pids` q ON q.product_id=p.product_id
						where dl.itemid=?  group by dl.itemid,g.itemid";
		
		$source_det_res=$this->db->query($sql,array($prod['itemid'],$prod['itemid'])) or die(mysql_error());
		
		if($source_det_res->num_rows())
		{
			$source_det=$source_det_res->row_array();
			$prod['is_sourceable']=$source_det['is_sourceable'];
		}else{
			$prod['is_sourceable']=0;
		}
		
		if($allow_for_fran)
		{
			$stock=$this->erpm->do_stock_check(array($prod['itemid']),array(1),true);
	
			$stock_tmp = array();
			$stock_tmp[0] = array();
			$stock_tmp[0][0] = array('stk'=>0);
			foreach($stock as $plist)
				foreach($plist as $pdet)
				{
	
					if(!$pdet['status'])
					{
						$prod['live']=0;
						$stock_tmp[0][0] = array('stk'=>$pdet['stk']);
					}
					else
					{
						$prod['live']=1;
						$stock_tmp[0][0] = array('stk'=>$pdet['stk']);
					}
	
				}
	
	
				/*$prod_mrp_changelog_res = $this->db->query("select * from deal_price_changelog where itemid=? order by id desc limit 1",$prod['id']);
				 if($prod_mrp_changelog_res->num_rows())
				 {
				$prod_mrp_changelog = $prod_mrp_changelog_res->row_array();
				$prod['oldmrp']=$prod_mrp_changelog['old_mrp'];
				}
				else
				{
				$prod['oldmrp']='-';
				}*/
	
	
	
				//get insurance range details by menu and deal mrp
	
				$insurance_det=$this->erpm->get_insurance_value_det($prod['menu_id'],$prod['mrp']);
				
				if($insurance_det)
				{
					$prod['insurance_value']=$insurance_det['insurance_value'];
					$prod['insurance_margin']=$insurance_det['insurance_margin'];
				}else{
					$prod['insurance_value']='';
					$prod['insurance_margin']='';
				}
	
				$margin=$this->erpm->get_pnh_margin($fid,$pid);
	
				if($prod['is_combo'])
					$prod['margin']=$margin['combo_margin'];
				else
					$prod['margin']=$margin['margin'];
				$attr="";
				/*foreach($this->db->query("select group_id from m_product_group_deal_link where itemid=?",$prod['id'])->result_array() as $g)
				{
					$group=$this->db->query("select group_id,group_name from products_group where group_id=?",$g['group_id'])->row_array();
					$attr.="";
					$anames=$this->db->query("select attribute_name_id,attribute_name from products_group_attributes where group_id=?",$g['group_id'])->result_array();
					foreach($anames as $a)
					{
						$attr.="<b>{$a['attribute_name']} :</b><span><select class='attr' name='{$pid}_{$a['attribute_name_id']}'>";
						$avalues=$this->db->query("select * from products_group_attribute_values where attribute_name_id=?",$a['attribute_name_id'])->result_array();
						foreach($avalues as $v)
							$attr.="<option value='{$v['attribute_value_id']}'>{$v['attribute_value']}</option>";
						$attr.='</select></span>';
					}
	
				}*/
				$prod['imei_disc'] = $this->erpm->get_franimeischdisc_pid($fid,$pid);
	
				if($mid==0)
				{
					if($prod['menu_id'] == 112 )
					{
						if($prod['imei_disc']==0 && $prod['price']<=5000 && $prod['margin']>=0.5)
						{
	
							$prod['margin']=$prod['margin']-0.5;
							$prod['base_margin']=$prod['base_margin']-0.5;
							$prod['key_imei_disc']=0.5;
						}
						else
						{
	
							$prod['key_imei_disc']=0;
						}
					}
				}
				
	
				$prod['lcost']=round($prod['price']-($prod['price']/100*$prod['margin']),2);
				//$prod['attr']=$attr;
	
				$prod['confirm_stock'] = '';
	
				//confirmation for prod stock check for shoes
				/*if($prod['menuid'] == 123)
					$prod['confirm_stock'] = '<b><input type="checkbox" name="confirm_stock" value="1" > : </b><span>Footwear Stock Available</span>';*/
	
				$prod['stock']=(($stock_tmp[0][0]['stk']>0)?$stock_tmp[0][0]['stk']:0);
	
				$prod['max_allowed_qty'] = $this->db->query("select max_allowed_qty from king_dealitems where pnh_id = ? ",$pid)->row()->max_allowed_qty;
	
	
	
				//to save the updated cart quantity
				$prod['svd_cartqty']=$this->db->query("select qty as cart_qty from pnh_api_franchise_cart_info where pid=? and franchise_id=? and status=1",array($pid,$fid))->row()->cart_qty;
	
				// get pid super scheme
				$prod['super_sch']= $this->erpm->get_fransuperschdisc_pid($fid,$pid);
	
				// get pid ordered total for today.
				$prod['max_ord_qty'] = $this->erpm->get_maxordqty_pid($fid,$pid);
	
				$prod['allow_order'] = $this->erpm->do_stock_check(array($prod['itemid']));
	
				$prod['is_publish']=$prod['publish'];
	
				$prod['is_sourceable']=$prod['is_sourceable'];
	
				$prod['has_insurance']=$prod['has_insurance'];
	
				//Condition to check  for key member-franchise is member
				if($mid==0)
				{
					$prod['key_member']=1;
	
					$insurance_fee=0;$member_fee_applicable=0;
	
					if($prod['price']<=5000)
					{
						$insurance_fee=0;
						$member_fee_applicable=0;
						$prod['mem_fee']=0;
						$prod['insurance_fee']=0;
					}
					if($prod['price']>5000 && $prod['price']<=10000)
					{
						$insurance_fee=0;
						$member_fee_applicable=1;
						$prod['mem_fee']=PNH_MEMBER_FEE;
						$prod['insurance_fee']=0;
						$prod['imei_disc']=0;
					}
					if($prod['price']>10000)
					{
						$insurance_fee=1;
						$member_fee_applicable=1;
						$insuranc_cost=(($prod['price']-10000)*1)/100;
						$prod['mem_fee']=PNH_MEMBER_FEE;
						$prod['insurance_fee']=$insuranc_cost;
						$prod['imei_disc']=0;
					}
	
				}else
					$prod['key_member']=0;
	
				//unset($prod['is_combo']);
				
				return $prod;
		}
	}
	
	/**
	 * function for check the deals margin details and stock details by franchise
	 * @param unknown_type $fid
	 * @param unknown_type $deal_det
	 */
	function check_cart_items_margin($fid,$cart_item_list)
	{
		$total=$d_total=$bal=$abal=0;
		$this->load->model('erpmodel','erpm');
		if($cart_item_list)
		{
	
			$fran_crdet = $this->erpm->get_fran_availcreditlimit($fid);
			$current_balance=$fran_crdet[3];
			$abal=$current_balance-$d_total;
	
			foreach($cart_item_list as $k=> $c)
			{
				$cart_item_list[$k]['cart_msg']='';
				$cart_item_list[$k]['place_order']=0;
				$qty=$c['qty'];
				$deal_det=$c['deal_deails'];
				
				$margin=$this->erpm->get_pnh_margin($fid,$deal_det['pid']);
	
				if($deal_det['is_combo']==1)
					$deal_det['discount']=$deal_det['price']/100*$margin['combo_margin'];
				else
					$deal_det['discount']=$deal_det['price']/100*$margin['margin'];
	
				$total+=$deal_det['price']*$qty;
				$deal_det['qty']=$qty;
				$d_total+=($deal_det['price']-$deal_det['discount'])*$qty;
				$deal_det['final_price']=($deal_det['price']-$deal_det['discount']);
	
				$cart_item_list[$k]['deal_deails']=$deal_det;
	
				if($current_balance<$d_total)
				{
					$cart_item_list[$k]['place_order']=0;
					$cart_item_list[$k]['cart_msg']='Insufficient balance! Balance in your account Rs '.formatInIndianStyle($current_balance);
				}
				else
				{
					$cart_item_list[$k]['place_order']=1;
					$cart_item_list[$k]['cart_msg']='Balance in your account Rs '.formatInIndianStyle($current_balance);
				}
			}
			return 	$cart_item_list;
		}
	}
	
	/**
	 * function for validate the the cart items by franchise and member
	 */
	function validate_cart_items($fid=0,$validatation_type=array(),$mem_id=0,$vcodes=0,$rtn=0)
	{
		$this->_is_validauthkey();
		$val_menus=array('112');
		$error=0;
		$userid=$this->input->post('user_id');
		
		if(!$fid ||!$validatation_type)
		{
			$fid=$this->input->post('franchise_id');
			$validatation_type=$this->input->post('validation_type');
			$mem_id=$this->input->post('member_id');
			$vcodes=$this->input->post('voucher_code');
		}
		
		if(!empty($validatation_type) && is_array($validatation_type))
		{
			
			foreach($validatation_type as $type)
			{
				//One member can place order for only one electronic item in one order 
				if($type==1)
				{
					$cart_items=$this->apim->get_cart_items($fid);
					
					foreach($cart_items as $c)
					{
						if($c['qty'] > 1 && in_array($c['deal_deails']['menu_id'],$val_menus))
						{
							$this->_output_handle('json',false,array('error_code'=>2010,'error_msg'=>"More than 1 qty of Electronic Items for 1 member can't be processed"));
							$error=1;
							break;
						}
					}
				}
				
				
				//flag type 2 is to validate the voucher code and memeber link
				if($type==2)
				{
					if($mem_id && $vcodes)
					{
						$vcodes=explode(',',$vcodes);
						
						//get the cart producs menu ids
						$ttl_ord_val=0;
						$voucher_value=0;
						$dealmenu=array();
						$cart_items=$this->apim->get_cart_items($fid);
						foreach($cart_items as $c)
						{
							if(!in_array($c['deal_deails']['menu_id'], $dealmenu))
									$dealmenu[]=$c['deal_deails']['menu_id'];
							
							$ttl_ord_val+=$c['qty']*$c['deal_deails']['price'];
						}
						 
						$vmenu_list=array();
						foreach($vcodes as $v=> $vcode)
						{
							$vdet=$this->apim->get_voucher_det_by_mem($mem_id,$vcode);
							if(!$vdet)
							{
								$this->_output_handle('json',false,array('error_code'=>2019,'error_msg'=>"Voucher code $vcode is invalid"));
								$error=1;
								break;
							}

							//voucher validation start here
							$vdet=$this->db->query("select b.book_id,d.franchise_id,customer_value
																from pnh_t_voucher_details a
																join pnh_t_book_voucher_link b on b.voucher_slno_id = a.id
																join pnh_t_book_allotment d on d.book_id=b.book_id
																where voucher_code=? and member_id=?  and is_activated=1 and a.status in(3,5)
																group by b.book_id",array($vcode,$mem_id))->row_array();
							
							
							
							if(!empty($vdet))
								$voucher_value+=$vdet['customer_value'];
							else{
								
								$this->_output_handle('json',false,array('error_code'=>2026,'error_msg'=>"Voucher code $vcode is fully redeemed"));
								$error=1;
								break;
							}
							
							$vmenus=$this->db->query("select id,name from  pnh_menu where find_in_set (id,(select menu_ids from pnh_t_book_details a join pnh_m_book_template b on b.book_template_id=a.book_template_id where a.book_id=?)) and id in (".implode(',',$dealmenu).") ",$vdet['book_id'])->result_array();
							
							//check the cart product menu and voucher menu is same
							if(empty($vmenus))
							{
								$this->_output_handle('json',false,array('error_code'=>2020,'error_msg'=>"Cart product menu and voucher menu is not matching"));
								$error=1;
								break;
							}
							
						
							foreach($vmenus as $m)
							{
								//check if using different menus at a time
								if($v > 0)
								{
									if(!in_array($m['id'],$vmenu_list))
									{
										$this->_output_handle('json',false,array('error_code'=>2021,'error_msg'=>"Sorry not able to use the different menu vouchers at time"));
										$error=1;
										break;
									}
								}
								
								$vmenu_list[]=$m['id'];
								
								$fmenu=$this->db->query("select * from pnh_franchise_menu_link where fid=? and menuid=? and status=1",array($vdet['franchise_id'],$m['id']))->row_array();
								if(empty($fmenu))
								{
									$this->_output_handle('json',false,array('error_code'=>2022,'error_msg'=>"Sorry not able to place a order voucher menu not assigned for voucher franchise"));
									$error=1;
									break;
								}
								
								//check voucher menu orderd deal menu same
								if(!in_array($m['id'],$dealmenu))
								{
									$this->_output_handle('json',false,array('error_code'=>2023,'error_msg'=>"Voucher ".$vcode." only for ".$m['name']." products"));
									$error=1;
									break;
								}
								
								//check ordered deal menus are same
								if(count(array_unique($dealmenu))>1)
								{
									$this->_output_handle('json',false,array('error_code'=>2023,'error_msg'=>"Voucher ".$vcode." only for ".$m['name']." products"));
									$error=1;
									break;
								}
								
							}
							
							foreach($dealmenu as $mk=>$dm)
							{
								//check ordered deal menu linked to franchise
								$fmenu=$this->db->query("select * from pnh_franchise_menu_link where fid=? and menuid=? and status=1",array($vdet['franchise_id'],$dm))->row_array();
								if(empty($fmenu))
								{
									$this->_output_handle('json',false,array('error_code'=>2024,'error_msg'=>"Sorry not able to  place a order product menu not assined for voucher franchise "));
									$error=1;
									break;
								}
							}
							
						}
						
						//check the voucher balance and cart item total
						if($voucher_value < $ttl_ord_val)
						{
							$this->_output_handle('json',false,array('error_code'=>2025,'error_msg'=>"Insufficient credit! Total value of purchase is Rs $ttl_ord_val and voucher value is Rs $voucher_value"));
							$error=1;
							break;
						}
						
					}else
						$this->_output_handle('json',false,array('error_code'=>2018,'error_msg'=>"Member id and voucher code must be require"));
						
				}
			}
			
			if(!$error && !$rtn)
				$this->_output_handle('json',true,array('Validation'=>true));
			else
				if(!$error)
					return true;
			else 
				return false;	
			
		}else{
				$this->_output_handle('json',false,array('error_code'=>2009,'error_msg'=>"No validation type found"));
		}
	
	}
	
	/**
	 * function for get the member details
	 */
	function get_member_info()
	{
		$this->_is_validauthkey();
		
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		$memid=$this->input->post('member_id');
		$mem_mob_no=$this->input->post('mem_mob_no');
		
		$member_details=$this->apim->get_member_details($memid,$mem_mob_no);
		
		if($member_details)
			$this->_output_handle('json',true,array("member_details"=>$member_details));
		else
			$this->_output_handle('json',false,array('error_code'=>2007,'error_msg'=>"No member available for given member id"));
	}
	
	/**
	 * function for get the member by mobile number
	 */
	function get_member_by_mobileno()
	{
		$this->_is_validauthkey();
		
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		$member_name=$this->input->post("member_name");
		$mobile_no=$this->input->post("mobile_no");
		
		$mem_det=$this->apim->get_member_by_mob($mobile_no);
		
		if($mem_det)
			$this->_output_handle('json',true,array("member_details"=>$mem_det));
		else
			$this->_output_handle('json',false,array('error_code'=>2010,'error_msg'=>"No member found for given mobile number"));
	}
	
	/**
	 * function add a new member
	 */
	function add_member()
	{
		$this->_is_validauthkey();
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		$member_name=$this->input->post("member_name");
		$mobile_no=$this->input->post("mobile_no");
		
		$member_id=$this->apim->add_new_member($fid,$member_name,$mobile_no);
		
		if($member_id)
			$this->_output_handle('json',true,array("member_details"=>array('member_name'=>$member_name,'member_id'=>$member_id)));
		else
			$this->_output_handle('json',false,array('error_code'=>2008,'error_msg'=>"Mobile no already registered"));
	}
	
	/**
	 * function for validate the member detais when order placing
	 * @param unknown_type $mobno
	 * @return boolean
	 */
	function _check_order_member_det($mobno)
	{
		$mobile_no=$this->input->post("mobile_no");
		$member_id=$this->input->post("member_id");
		$entry_type=$this->input->post("mem_entry_type");
		
		if($entry_type==1)
		{
			$n_mem=$this->apim->get_member_by_mob($mobile_no);
			if($n_mem)
			{
				$this->form_validation->set_message('_check_order_member_det','Given mobile no already registered');
				return false;
			}
		}else{
			$mem=$this->apim->get_member_details($member_id);
			if(!$mem)
			{
				$this->form_validation->set_message('_check_order_member_det','No member found for given member id');
				return false;
			}
			
		}
		
		return true;
	}
	
	/**
	 * function for validate the cart items while placing a order
	 * @param unknown_type $franchise_id
	 * @return boolean
	 */
	function _check_cart_items($franchise_id)
	{
		$validatation_type=array(1);
		$fid=$this->input->post('franchise_id');
		$is_voucher_order=$this->input->post("is_voucher_order");
		$member_id=$this->input->post("member_id");
		$vocher_codes=$this->input->post("voucher_code");
		$vocher_codes=$this->input->post("voucher_code");
		
		if($is_voucher_order)
			$validatation_type[]=2;
			
		$v=$this->validate_cart_items($fid,$validatation_type,$member_id,$vocher_codes,1);
		
		if(!$v)
		{
			$this->form_validation->set_message('_check_cart_items','Please check your cart items');
			return false;
		}
		
		return true;
	}
	
	/**
	 * function for placing a order
	 */
	function place_order()
	{
		$this->_is_validauthkey();
		
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('franchise_id','Franchise id','required|callback__check_cart_items');
		$this->form_validation->set_rules('member_id','Member Id','required|callback__check_order_member_det');
		if($this->form_validation->run() === FALSE)
		{
			$this->_output_handle('json',false,array('error_code'=>2044,'error_msg'=>strip_tags(validation_errors())));
		}else{
		
				$userid=$this->input->post('user_id');
				$fid=$this->input->post('franchise_id');
				$member_name=$this->input->post("member_name");
				$mobile_no=$this->input->post("mobile_no");
				$member_id=$this->input->post("member_id");
				$entry_type=$this->input->post("mem_entry_type");
				$is_voucher_order=$this->input->post("is_voucher_order");
				$vocher_codes=$this->input->post("voucher_code");
				
				$this->load->model('erpmodel','erpm');
				
				if($entry_type==1)
				{
					$member_id=$this->apim->add_new_member($fid,$member_name,$mobile_no);
				
					if(!$member_id)
						$this->_output_handle('json',false,array('error_code'=>2008,'error_msg'=>"Mobile no already registered"));
				}
				
				if(!$this->apim->get_member_details($member_id ))
					$this->_output_handle('json',false,array('error_code'=>2007,'error_msg'=>"No member available for given member id"));
				
				$this->db->query("update pnh_api_franchise_cart_info set member_id=? where franchise_id=? and status=1",array($member_id,$fid));
				
				
				
				$fid=$fid;
				$pid=array();
				$qty=array();
				$prd_attrs=array();
				$mid=$member_id;
				$redeem=0;
				$redeem_points=150;
				$mid_entrytype=0;
				
				
				//get cart info 
				$cart_details=$this->apim->get_cart_items($fid);
				
				if(!$cart_details)
					$this->_output_handle('json',true,array("order_msg"=>"No more items in your cart"));
				
				foreach($cart_details as $cart)
				{
					array_push($pid,$cart['pid']);
					array_push($qty,$cart['qty']);
					
					if($cart['attributes'])
					{
						foreach($cart['attributes'] as $attrs)
						{
							$attr_details=explode("|",$attrs[0]);
							foreach($attr_details as $attr)
							{
								$a=explode(":",$attr);
								$prd_attrs[$a[0]]=$a[1];
							}
						}
					}
				}
				
				//check order placing type
				if($is_voucher_order)
				{
					if(!$vocher_codes || !array_filter(explode(',',$vocher_codes)))
						$this->_output_handle('json',false,array('error_code'=>2018,'error_msg'=>"Member id and voucher code must be require"));
					
					$member=$this->apim->get_member_details($member_id );
					
					$this->coupon_redeemtion($vocher_codes,$member[0]['mobile'],$pid,$qty,$fid,$member_id);
				}
				
				//franchise suspending type flag
				$fran_status_arr=array();
				$fran_status_arr[0]="Live";
				$fran_status_arr[1]="Permanent Suspension";
				$fran_status_arr[2]="Payment Suspension";
				$fran_status_arr[3]="Temporary Suspension";
				$admin =$this->apim->get_api_user();
				
				$updated_by=$admin["userid"];
				
				if($redeem)
					$redeem_points = 150;
				
				
				
				$menuid=$this->db->query("select d.menuid,m.default_margin as margin from king_dealitems i join king_deals d on d.dealid=i.dealid JOIN pnh_menu m ON m.id=d.menuid where i.is_pnh=1 and i.pnh_id=?",$pid)->row_array();
				$fran=$this->db->query("select * from pnh_m_franchise_info where franchise_id=?",$fid)->row_array();
				$fran1=$this->db->query("select * from pnh_franchise_menu_link where fid=? and menuid=?",array($fid,$menuid['menuid']))->row_array();
				$margin=$this->db->query("select margin,combo_margin from pnh_m_class_info where id=?",$fran['class_id'])->row_array();
				
				//franchise suspension status
				$fran_status=$fran['is_suspended'];
				
				$has_super_scheme=0;
				$has_scheme_discount=0;
				$has_member_scheme=0;
				$has_offer=0;
				
				
				if($fran1['sch_discount_start']<time() && $fran1['sch_discount_end']>time() && $fran1['is_sch_enabled'])
				{
					$fran1['sch_type']=1;
					$has_scheme_discount=1;
					$menuid['margin']+=$fran1['sch_discount'];
				}
				
				$super_scheme=$this->db->query("select * from pnh_super_scheme where menu_id=? and is_active=1 and franchise_id = ? limit 1",array($menuid['menuid'],$fid))->row_array();
				//super scheme enabled for scheme discount
				if(!empty($super_scheme))
				{
					if($super_scheme['valid_from']<time() && $super_scheme['valid_to']>time() && $super_scheme['is_active'] == 1)
						$has_super_scheme=1;
				}
				
				$member_scheme=$this->db->query("select * from imei_m_scheme where is_active=1 and franchise_id=? and ? between sch_apply_from and scheme_to order by created_on desc limit 1",array($fid,time()))->row_array();
				//member scheme enabled for scheme discount
				if(!empty($member_scheme))
				{
					$has_member_scheme=1;
				}
				
				
				$offer_scheme=$this->db->query("select * from pnh_m_offers where menu_id=? and franchise_id=? and ? between offer_start and offer_end order by id desc limit 1",array($menuid['menuid'],$fid,time()))->row_array();
				
				if(!empty($offer_scheme))
				{
					$has_offer=1;
				}
				
				$items=array();
				foreach($pid as $i=>$p)
					$items[]=array("pid"=>$p,"qty"=>$qty[$i]);
				$total=0;$d_total=0;
				$itemnames=$itemids=array();
				
				//compute redeem val per item : total_items
				$ordered_menus_list=array();
				$item_pnt =  $redeem_points/count($items);
				$redeem_value = 0;
				foreach($items as $i=>$item)
				{
					$prod=$this->db->query("select i.*,d.publish,c.loyality_pntvalue,d.menuid from king_dealitems i join king_deals d on d.dealid=i.dealid JOIN pnh_menu c ON c.id = d.menuid where i.is_pnh=1 and  i.pnh_id=? and i.pnh_id!=0",$item['pid'])->row_array();
					$ordered_menus_list[]=$prod['menuid'];
					//check the product list found
					if(empty($prod))
						$this->_output_handle('json',false,array('error_code'=>2011,'error_msg'=>"There is no product with ID : ".$item['pid']));
						
					if($prod['publish']!=1)
						$this->_output_handle('json',false,array('error_code'=>2012,'error_msg'=>"Product {$prod['name']} is not available"));
						
					$items[$i]['tax']=$prod['tax'];
					$items[$i]['mrp']=$prod['orgprice'];
					if($fran['is_lc_store'])
						$items[$i]['price']=$prod['store_price'];
					else
						$items[$i]['price']=$prod['price'];
					$items[$i]['itemid']=$prod['id'];
					
					$margin=$this->erpm->get_pnh_margin($fran1['fid'],$item['pid']);
					
					if($prod['is_combo']=="1")
						$items[$i]['discount']=$items[$i]['price']/100*$margin['combo_margin'];
					else
						$items[$i]['discount']=$items[$i]['price']/100*$margin['margin'];
				
					$items[$i]['margin']=$margin;
					$total+=$items[$i]['price']*$items[$i]['qty'];
					$d_total+=($items[$i]['price']-$items[$i]['discount'])*$items[$i]['qty'];
					$itemids[]=$prod['id'];
					$itemnames[]=$prod['name'];
					$loyalty_pntvalue=$prod['loyality_pntvalue'];
				
					if($redeem)
						$redeem_value += $item_pnt_value = $item_pnt*$prod['loyality_pntvalue'];
				}
				
				$avail=$this->erpm->do_stock_check($itemids);
				
				foreach($itemids as $i=>$itemid)
					if(!in_array($itemid,$avail))
						$this->_output_handle('json',false,array('error_code'=>2013,'error_msg'=>"{$itemnames[$i]} is out of stock"));
					
				//check group products have attributes
				foreach($itemids as $itemid)
				{
					foreach($this->db->query("select group_id from m_product_group_deal_link where itemid=?",$itemid)->result_array() as $g)
					{
						foreach($this->db->query("select attribute_name_id from products_group_attributes where group_id=?",$g['group_id'])->result_array() as $p)
						{
							if(!isset($prd_attrs[$p['attribute_name_id']]))
							{
								$this->_output_handle('json',false,array('error_code'=>2043,'error_msg'=>"Product attributes not found"));
							}
						}
					}
				}
				
				
				$fran_crdet = $this->erpm->get_fran_availcreditlimit($fran['franchise_id']);
				$fran['current_balance'] = $fran_crdet[3];
				
				//check if it is prepaid franchise block
				$is_prepaid_franchise=$this->erpm->is_prepaid_franchise($fid);
				if($is_prepaid_franchise)
				{
					if(count(array_unique($ordered_menus_list))==1)
					{
						if($ordered_menus_list[0]!=VOUCHERMENU)
							$is_prepaid_franchise=false;
					}else{
						$is_prepaid_franchise=false;
					}
				}
				
				//check if it is prepaid franchise block end
				
				if($fran['current_balance']<$d_total && !$is_prepaid_franchise)
					$this->_output_handle('json',false,array('error_code'=>2014,'error_msg'=>"Insufficient balance! Balance in your account Rs {$fran['current_balance']} Total order amount : Rs $d_total"));
					
				$rand_mid = 0;
				if(!$mid)
				{
					$mid=$this->erpm->_gen_uniquememberid();
					$rand_mid = 1;
				}
				
				if($mid)
				{
				
					if($this->db->query("select 1 from pnh_member_info where pnh_member_id=?",$mid)->num_rows()==0)
					{
						if($rand_mid==0)
							if($this->db->query("select 1 from pnh_m_allotted_mid where ? between mid_start and mid_end and franchise_id=?",array($mid,$fran['franchise_id']))->num_rows()==0)
							$this->_output_handle('json',false,array('error_code'=>2014,'error_msg'=>"Member ID $mid is not allotted to you"));
							
						$this->db->query("insert into king_users(name,is_pnh,createdon) values(?,1,?)",array("PNH Member: $mid",time()));
						$userid=$this->db->insert_id();
						$this->db->insert("pnh_member_info",array("user_id"=>$userid,"pnh_member_id"=>$mid,"franchise_id"=>$fran['franchise_id'],"first_name"=>$this->input->post("m_name"),"mobile"=>$this->input->post("m_mobile")));
						$npoints=$this->db->query("select points from pnh_member_info where user_id=?",$userid)->row()->points+PNH_MEMBER_FEE;
						$this->db->query("update pnh_member_info set points=? where user_id=? limit 1",array($npoints,$userid));
						$this->db->query("insert into pnh_member_points_track(user_id,transid,points,points_after,created_on) values(?,?,?,?,?)",array($userid,"",PNH_MEMBER_FEE,$npoints,time()));
						$this->erpm->pnh_fran_account_stat($fran['franchise_id'],1,PNH_MEMBER_FEE-PNH_MEMBER_BONUS,"50 Credit Points purchase for Member $mid","member",$mid);
					}
					else
						$userid=$this->db->query("select user_id from pnh_member_info where pnh_member_id=?",$mid)->row()->user_id;
				}else
				{
					$userid = 0;
				}
				
				
				
				
				$bal_discount_amt = 0;
				
				if($redeem)
				{
					$total-=$redeem_value;
					$d_total-=$redeem_value;
				}
				
				//check if franchise is in suspension
				if($fran_status==0)
					$batch_enabled = 1;
				else
					$batch_enabled = 0;
				
				$transids = array();
				$transids[0] = array();
				$transids[1] = array();
				foreach($items as $item)
				{
					$item['name'] = $this->db->query("select name from king_dealitems where id = ? ",$item['itemid'])->row()->name;
				
					// check if belongs to split invoice condiciton config
					$split_trans=$this->db->query("SELECT i.*,d.publish,c.loyality_pntvalue FROM king_dealitems i JOIN king_deals d ON d.dealid=i.dealid JOIN pnh_menu c ON c.id = d.menuid WHERE i.is_pnh=1 AND  i.pnh_id=? AND i.pnh_id!=0 AND c.id IN(112,118,122)",$item['pid'])->row_array();
					//$split_order = 0;
					$item['split_order'] = 0;
					if($split_trans)
					{
						$ttl_qty = $item['qty'];
						for($k=0;$k<$ttl_qty;$k++)
						{
							$item['split_order'] = 1;
							$item['qty'] = 1;
							$transids[1][] = $item;
						}
					}else
					{
							$transids[0][] = $item;
					}
				}
				
				$split_transid_list = array();
				if(count($transids[0]))
					$split_transid_list[] = $transids[0];
				
				if(count($transids[1]))
					foreach($transids[1] as $tr_items)
						$split_transid_list[] = array($tr_items);
				
				$batch_enabled=1;
				$trans_grp_ref_no = ($this->db->query("select if(max(trans_grp_ref_no),max(trans_grp_ref_no)+1,400001) as n from king_transactions where trans_grp_ref_no >= 0")->row()->n);
				
				foreach($split_transid_list as $items)
				{
					$transid=strtoupper("PNH".random_string("alpha",3).$this->erpm->p_genid(5));
				
					//do packing process
					$ttl_num_orders=count($items);
					$batch_remarks='Created by pnh offline order system';
							
					if($redeem && count($split_transid_list) == 1)
					{
							$total-=$redeem_value;
							$d_total-=$redeem_value;
							$apoints=$this->db->query("select points from pnh_member_info where user_id=?",$userid)->row()->points-$redeem_points;
							$this->db->query("update pnh_member_info set points=points-? where user_id=? limit 1",array($redeem_points,$userid));
							$this->db->query("insert into pnh_member_points_track(user_id,transid,points,points_after,created_on) values(?,?,?,?,?)",array($userid,$transid,-$redeem_points,$apoints,time()));
							$this->erpm->do_trans_changelog($transid,"$redeem_points Loyalty points redeemed");
					}
				
					$this->db->query("insert into king_transactions(transid,amount,paid,mode,init,actiontime,is_pnh,franchise_id,trans_created_by,batch_enabled,trans_grp_ref_no) values(?,?,?,?,?,?,?,?,?,?,?)",array($transid,$d_total,$d_total,3,time(),time(),1,$fran['franchise_id'],$admin['userid'],$batch_enabled,$trans_grp_ref_no));
				
					foreach($items as $item)
					{
				
						$inp=array("id"=>$this->erpm->p_genid(10,'order'),"transid"=>$transid,"userid"=>$userid,"itemid"=>$item['itemid'],"brandid"=>"");
				
						$inp["brandid"]=$this->db->query("select d.brandid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?",$item['itemid'])->row()->brandid;
						$brandid=$inp["brandid"];
						$catid=$this->db->query("select d.catid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?",$item['itemid'])->row()->catid;
						$menuid=$this->db->query("select d.menuid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=? and menuid2=0",$item['itemid'])->row()->menuid;
						$inp["bill_person"]=$inp['ship_person']=$fran['franchise_name'];
						$inp["bill_address"]=$inp['ship_address']=$fran['address'];
						$inp["bill_city"]=$inp['ship_city']=$fran['city'];
						$inp['bill_pincode']=$inp['ship_pincode']=$fran['postcode'];
						$inp['bill_phone']=$inp['ship_phone']=$fran['login_mobile1'];
						$inp['bill_email']=$inp['ship_email']=$fran['email_id'];
						$inp['bill_state']=$inp['ship_state']=$fran['state'];
						$inp['quantity']=$item['qty'];
						$inp['time']=time();
						$inp['ship_landmark']=$inp['bill_landmark']=$fran['locality'];
						$inp['bill_country']=$inp['ship_country']="India";
						$inp['i_orgprice']=$item['mrp'];
						$inp['i_price']=$item['price'];
						$inp['i_discount']=$item['mrp']-$item['price'];
						$inp['redeem_value']=($item['price']/($total+$redeem_value))*$redeem_value;
				
						if($item['split_order'] && $mid_entrytype == 1)
						{
							$membr_id=$this->erpm->_gen_uniquememberid();
							if($this->db->query("select * from pnh_member_info where pnh_member_id=?",$membr_id)->num_rows()==0);
								$inp['member_id']=$membr_id;
							
							$inp['is_ordqty_splitd']=1;
				
							$this->db->query("insert into king_users(name,is_pnh,createdon) values(?,1,?)",array("PNH Member: $membr_id",time()));
							$userid=$this->db->insert_id();
							$inp['userid']=$userid;
							$this->db->query("insert into pnh_member_info(pnh_member_id,user_id,franchise_id,created_by,created_on)values(?,?,?,?,?)",array($membr_id,$userid,$fid,$admin['userid'],time()));
						}
				
						//if super scheme is enabled
						if($has_super_scheme!=0)
						{
							//check item enabled for super scheme
							$check_superschdisableditem=$this->db->query("select * from pnh_superscheme_deals where is_active=0 and ? between valid_from and valid_to and itemid=? order by created_on desc limit 1",array(time(),$item['itemid']))->row_array();
				
							//$super_scheme_brand=$this->db->query("select * from pnh_super_scheme where brand_id=? and franchise_id = ? ",array($brandid,$fid))->result_array();
							$super_scheme_brand=$this->db->query("select * from pnh_super_scheme where menu_id=? and cat_id=? and brand_id=? and franchise_id = ? and is_active=1",array($menuid,$catid,$brandid,$fid))->result_array();
							
							if(empty($super_scheme_brand))
								$super_scheme_brand=$this->db->query("select * from pnh_super_scheme where menu_id=? and cat_id=0 and brand_id=? and franchise_id = ? and is_active=1 order by id desc limit 1",array($menuid,$brandid,$fid))->result_array();
							
							if(empty($super_scheme_brand))
								$super_scheme_brand=$this->db->query("select * from pnh_super_scheme where menu_id=? and cat_id=? and brand_id=0 and franchise_id = ? and is_active=1 order by id desc limit 1",array($menuid,$catid,$fid))->result_array();
							
							if(empty($super_scheme_brand))
								$super_scheme_brand=$this->db->query("select * from pnh_super_scheme where menu_id=? and cat_id=0 and brand_id=0 and franchise_id = ? and is_active=1 order by id desc limit 1",array($menuid,$fid))->result_array();
								
							if(!empty($super_scheme_brand) && empty($check_superschdisableditem))
							{
								foreach($super_scheme_brand as $super_scheme)
								{
									if($super_scheme['valid_from']<time() && $super_scheme['valid_to']>time() && $super_scheme['is_active'] == 1)
									{
										$inp['super_scheme_logid']=$super_scheme['id'];
										$inp['has_super_scheme']=1;
										$inp['super_scheme_target']=$super_scheme['target_value'];
										$inp['super_scheme_cashback']=$super_scheme['credit_prc'];
									}
								}
							}
						}
				
						if($has_member_scheme==1)
						{
						
							//check item enabled for member scheme
							$check_mbrschdisableditem=$this->db->query("select * from pnh_membersch_deals where is_active=0 and ? between valid_from and valid_to and itemid=? order by created_on desc limit 1",array(time(),$item['itemid']))->row_array();
				
							$member_scheme_brand=$this->db->query("select * from imei_m_scheme where menuid=? and categoryid=? and brandid=? and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$catid,$brandid,$fid))->result_array();
							
							if(empty($member_scheme_brand))
								$member_scheme_brand=$this->db->query("select * from imei_m_scheme where menuid=? and categoryid=? and brandid=0 and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$catid,$fid))->result_array();
							
							if(empty($member_scheme_brand))
								$member_scheme_brand=$this->db->query("select * from imei_m_scheme where menuid=? and categoryid=0 and brandid=? and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$brandid,$fid))->result_array();
							
							if(empty($member_scheme_brand))
								$member_scheme_brand=$this->db->query("select * from imei_m_scheme where menuid=? and categoryid=0 and brandid=0 and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$fid))->result_array();
				
							if(!empty($member_scheme_brand)  && empty($check_mbrschdisableditem))
								foreach($member_scheme_brand as $member_scheme)
								{
									$inp['imei_scheme_id']=$member_scheme['id'];
									if($member_scheme['scheme_type']==0)
										$inp['imei_reimbursement_value_perunit']=$member_scheme['credit_value'];
									else
										$inp['imei_reimbursement_value_perunit']=$item['price']-($item['price']-$item['price']*$member_scheme['credit_value']/100);
								}
						}
				
						if($has_offer==1)
						{
							$offer_det=$this->db->query("select * from pnh_m_offers where menu_id=? and cat_id=? and brand_id=? and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$catid,$brandid,$fid))->result_array();
							
							if(empty($offer_det))
								$offer_det=$this->db->query("select * from pnh_m_offers where menu_id=? and cat_id=? and brand_id=0 and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$catid,$fid))->result_array();
									
							if(empty($offer_det))
								$offer_det=$this->db->query("select * from pnh_m_offers where menu_id=? and cat_id=0 and brand_id=? and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$brandid,$fid))->result_array();
									
							if(empty($offer_det))
								$offer_det=$this->db->query("select * from pnh_m_offers where menu_id=? and cat_id=0 and brand_id=0 and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$fid))->result_array();
							
							if(!empty($offer_det))
							{
								foreach($offer_det as $offer)
								{
									$inp['has_offer']=1;
									$inp['offer_refid']=$offer['id'];
								}
							}
						}
				
						if($redeem)
								$inp['i_coup_discount']=$item['discount']+$inp['redeem_value'];
							else
								$inp['i_coup_discount']=$item['discount'];
				
						$inp['i_tax']=$item['tax'];
						$this->db->insert("king_orders",$inp);
						
						//disble the product in api cart table
						if($this->db->affected_rows())
							$this->db->query("update pnh_api_franchise_cart_info set status=0 where status=1 and franchise_id=? and pid=? and member_id=?",array($fid,$item['pid'],$member_id));
						
						foreach($this->db->query("select group_id from m_product_group_deal_link where itemid=?",$inp['itemid'])->result_array() as $g)
						{
							$attr_n=array();
							$attr_v=array();
							
							foreach($this->db->query("select attribute_name_id from products_group_attributes where group_id=?",$g['group_id'])->result_array() as $p)
							{
								$attr_n[]=$p['attribute_name_id'];
								$attr_v[]=$prd_attrs[$p['attribute_name_id']];
							}
							$sql="select product_id from products_group_pids where attribute_name_id=? and attribute_value_id=?";
							foreach($this->db->query($sql,array($attr_n[0],$attr_v[0]))->result_array() as $p)
							{
								$f=true;
								foreach($attr_n as $i=>$an)
									if($this->db->query("select 1 from products_group_pids where product_id=? and attribute_name_id=? and attribute_value_id=?",array($p['product_id'],$an,$attr_v[$i]))->num_rows()==0)
										$f=false;
								if($f)
									break;
							}
							$this->db->insert("products_group_orders",array("transid"=>$transid,"order_id"=>$inp['id'],"product_id"=>$p['product_id']));
						}
				
						$bal_discount_amt = ($item['price']*$item['margin']['bal_discount']/100)*$item['qty'];
						$m_inp=array("transid"=>$transid,"itemid"=>$item['itemid'],"mrp"=>$item['mrp'],"price"=>$item['price'],"base_margin"=>$item['margin']['base_margin'],"sch_margin"=>$item['margin']['sch_margin'],"bal_discount"=>$item['margin']['bal_discount'],"qty"=>$item['qty'],"final_price"=>$item['price']-$item['discount']);
						$this->db->insert("pnh_order_margin_track",$m_inp);
				
				
				}
				
				// check if franchise is suspended
				if($fran_status==0)
					$this->erpm->do_trans_changelog($transid,"PNH Offline order created",$admin['userid']);
				else
					$this->erpm->do_trans_changelog($transid,"Batch Disabled as Franchise is on ".$fran_status_arr[$fran_status ],$admin['userid']);
				
				
				$trans_amt = $this->db->query("select sum((i_orgprice-(i_discount+i_coup_discount))*quantity) as amt from king_orders where transid = ? and status = 0 ",$transid)->row()->amt;
				
				$this->db->query("update king_transactions set amount = ?,paid=? where transid=?",array($trans_amt,$trans_amt,$transid));
				/*imp*/
				// Process to batch this transaction
				//$this->reservations->do_batching_process($transid,$ttl_num_orders,$batch_remarks,$updated_by);
				//$this->session->set_flashdata("erp_pop_info","$transid is processed for batch");
				
				}
				//die("TESTING");
				
				$bal_discount_amt_msg = '';
				if($bal_discount_amt)
					$bal_discount_amt_msg = ', Topup Damaka Applied : Rs'.$bal_discount_amt;
				
				$this->erpm->pnh_fran_account_stat($fran['franchise_id'],1, $d_total,"Order $transid - Total Amount: Rs $total".$bal_discount_amt_msg,"transaction",$transid);
				
				$balance=$this->db->query("select current_balance from pnh_m_franchise_info where franchise_id=?",$fran['franchise_id'])->row()->current_balance;
				
				$this->erpm->sendsms_franchise_order($transid,$d_total);
				$points=0;
				if(!$redeem)
				{
					$rpoints=$this->db->query("select points from pnh_loyalty_points where amount<? order by amount desc limit 1",$total)->row_array();
					if(!empty($rpoints))
						$points=$rpoints['points'];
				}
				
				$apoints=$this->db->query("select points from pnh_member_info where user_id=?",$userid)->row()->points+$points;
				$this->db->query("update pnh_member_info set points=points+? where user_id=? limit 1",array($points,$userid));
				$this->db->query("insert into pnh_member_points_track(user_id,transid,points,points_after,created_on) values(?,?,?,?,?)",array($userid,$transid,$points,$apoints,time()));
				
				$franid=$fran['franchise_id'];
				$billno=10001;
				$nbill=$this->db->query("select bill_no from pnh_cash_bill where franchise_id=? order by bill_no desc limit 1",$franid)->row_array();
				if(!empty($nbill))
					$billno=$nbill['bill_no']+1;
				
				$inp=array("bill_no"=>$billno,"franchise_id"=>$franid,"transid"=>$transid,"user_id"=>$userid,"status"=>1);
				$this->db->insert("pnh_cash_bill",$inp);
				
				$this->_output_handle('json',true,array("order_msg"=>"Order placed successfully"));
		}
	}
	
	
	
	/**
	 * function to redeem coupon
	 */
	protected function coupon_redeemtion($voucher_code,$mem_mobno,$pid,$qty,$fid,$member_id)
	{
		$this->_is_validauthkey();
				
		$this->load->model('erpmodel','erpm');
		$admin =$this->apim->get_api_user();
		
		$is_vcode_int=ctype_alpha($voucher_code);
		$voucher_codes=explode(',',$voucher_code);
	
		if($is_vcode_int==1)
		{
			$voucher_codes=$this->db->query("select group_concat(voucher_code) as voucher_code from pnh_t_voucher_details where status=5")->row_array();
			$voucher_codes=explode(',',$voucher_codes['voucher_code']);
		}
	
		$is_strkng_membr=$this->db->query("select * from pnh_member_info where mobile=?",$mem_mobno)->row_array();
		$mem_det=$this->db->query("select mobile,first_name,last_name from pnh_member_info where pnh_member_id=?",$is_strkng_membr['pnh_member_id'])->row_array();
	
		$items=array();
		$pnh_ids=array();
		foreach($pid as $i=>$p)
		{
			if(!is_numeric($p))
				continue;
			$items[]=array("pid"=>$p,"qty"=>$qty[$i]);
			$pnh_ids[]=$p;
		}
	
		$total=0;$d_total=0;
		$itemnames=$itemids=array();
	
		//validate the voucher menu link block
		$dealmenu=array();
		$total_ordered_val=0;
		foreach($pnh_ids as $pid)
		{
	
			$menu_det=$this->db->query("select c.id,c.name,b.orgprice from king_deals a join king_dealitems b on b.dealid=a.dealid join pnh_menu c on c.id=a.menuid where b.pnh_id=?",$pid)->row_array();
	
			if(!in_array($menu_det['id'], $dealmenu))
				$dealmenu[]=$menu_det['id'];
			$total_ordered_val+=$menu_det['orgprice'];
		}
	
		if($voucher_codes)
		{
	
			$vmenu_list=array();
			foreach($voucher_codes as $v=> $vscode)
			{
	
				/* if($this->db->query("select count(1) as ttl from pnh_t_voucher_details where voucher_code=? and status=3",$vscode)->row()->ttl==0)
				 show_error("Partially redeemed voucher cannot be processed using voucher secret code");
				*/
				
				$vdet=$this->apim->get_voucher_det_by_mem($is_strkng_membr['pnh_member_id'],$vscode);
				if(!$vdet)
					$this->_output_handle('json',false,array('error_code'=>2019,'error_msg'=>"Voucher code $vscode is invalid"));
	
				$vdet=$this->db->query("select b.book_id,d.franchise_id
												from pnh_t_voucher_details a
												join pnh_t_book_voucher_link b on b.voucher_slno_id = a.id
												join pnh_t_book_allotment d on d.book_id=b.book_id
												where voucher_code=? and member_id=?  and is_activated=1 and a.status in(3,5)
												group by b.book_id",array($vscode,$is_strkng_membr['pnh_member_id']))->row_array();
	
				if(empty($vdet))
					$this->_output_handle('json',false,array('error_code'=>2026,'error_msg'=>"Voucher code $vscode is fully redeemed"));
				
				//get the book menus
				//$vmenus=$this->db->query("select id,name from  pnh_menu where id in (select menu_ids from pnh_t_book_details a join pnh_m_book_template b on b.book_template_id=a.book_template_id where a.book_id=?)",$vdet['book_id'])->result_array();
	
				$vmenus=$this->db->query("select id,name from  pnh_menu where find_in_set (id,(select menu_ids from pnh_t_book_details a join pnh_m_book_template b on b.book_template_id=a.book_template_id where a.book_id=?)) and id in (".implode(',',$dealmenu).") ",$vdet['book_id'])->result_array();
	
				if(empty($vmenus))
					$this->_output_handle('json',false,array('error_code'=>2020,'error_msg'=>"Cart product menu and voucher menu is not matching"));
					
				foreach($vmenus as $m)
				{
					//check used multiple voucher same menu
					if($v > 0)
					{
						if(!in_array($m['id'],$vmenu_list))
							$this->_output_handle('json',false,array('error_code'=>2021,'error_msg'=>"Sorry not able to use the different menu vouchers at time"));
					}
	
					//check voucher menu linked to franchise
					$vmenu_list[]=$m['id'];
					$fmenu=$this->db->query("select * from pnh_franchise_menu_link where fid=? and menuid=? and status=1",array($vdet['franchise_id'],$m['id']))->row_array();
					if(empty($fmenu))
						$this->_output_handle('json',false,array('error_code'=>2022,'error_msg'=>"Sorry not able to place a order voucher menu not assigned for voucher franchise"));
					
					//check voucher menu orderd deal menu same
					if(!in_array($m['id'],$dealmenu))
						$this->_output_handle('json',false,array('error_code'=>2023,'error_msg'=>"Voucher ".$vscode." only for ".$m['name']." products"));
	
					//check ordered deal menus are same
					if(count(array_unique($dealmenu))>1)
						$this->_output_handle('json',false,array('error_code'=>2023,'error_msg'=>"Voucher ".$vscode." only for ".$m['name']." products"));
				}
	
				foreach($dealmenu as $mk=>$dm)
				{
					//check ordered deal menu linked to franchise
					$fmenu=$this->db->query("select * from pnh_franchise_menu_link where fid=? and menuid=? and status=1",array($vdet['franchise_id'],$dm))->row_array();
					if(empty($fmenu))
						$this->_output_handle('json',false,array('error_code'=>2024,'error_msg'=>"Sorry not able to  place a order product menu not assined for voucher franchise "));
				}
			}
		}
	
		//validate the voucher menu link block
	
		$fid=$this->db->query("SELECT GROUP_CONCAT(franchise_id) AS franchise_id,IF(is_alloted=1,1,0),IF(is_activated=1,1,0),IF(status=3,3,0)FROM pnh_t_voucher_details WHERE voucher_code IN(?)",$voucher_codes)->row()->franchise_id;
		if(empty($fid))
			$this->_output_handle('json',false,array('error_code'=>2027,'error_msg'=>"Sorry your not able to place a order"));
	
		$fran=$this->db->query("select * from pnh_m_franchise_info where franchise_id in(?)",$fid)->row_array();
	
	
		$total=0;$d_total=0;$c_total=0;
		$itemids=array();
		$itemnames=array();
	
	
		$voucher_code_used = array();
	
		foreach($voucher_codes as $v_code)
		{
	
			$voucher_margin=$this->db->query("select voucher_margin from pnh_t_voucher_details where voucher_code=?",$v_code);
	
			$is_voucher_activated=$this->db->query("select * from pnh_t_voucher_details where voucher_code=? and member_id=?  and is_activated=1 and status in (3,5)",array($v_code,$is_strkng_membr['pnh_member_id']))->row_array();
			if(!empty($is_voucher_activated))
			{
	
	
				$voucher_offran=$is_voucher_activated['franchise_id'];
				$voucher_value+=$is_voucher_activated['customer_value'];
				$voucher_code_used[$v_code] = $is_voucher_activated['customer_value'];
				$valid_voucher_count++;//count of correct coupon code entered by user
			}
	
		}
		
		foreach($items as $i=>$item)
		{
			//check the order product menu is linked to coupon assigned franchise
			$prod=$this->db->query("select i.*,d.publish,m.menu_margin from king_dealitems i join king_deals d on d.dealid=i.dealid JOIN `pnh_prepaid_menu_config`m ON m.menu_id=d.menuid where i.is_pnh=1 and i.pnh_id=? and i.pnh_id!=0 AND 1",$item['pid'])->row_array();
	
	
			$items[$i]['mrp']=$prod['orgprice'];
			if(@$fran['is_lc_store'])
				$items[$i]['price']=$prod['store_price'];
			else
				$items[$i]['price']=$prod['price'];
	
			$voucher_margin=@$is_voucher_activated['voucher_margin'];
			$items[$i]['itemid']=$prod['id'];
			if($prod['is_combo']=="1")
				$items[$i]['discount']=$items[$i]['price']/100*$margin['combo_margin'];
			else
				$items[$i]['discount']=$items[$i]['price']/100*$voucher_margin;
			//$items[$i]['discount']=$voucher_margin;
			$total+=$items[$i]['price']*$items[$i]['qty'];
			$d_total+=($items[$i]['price']-$items[$i]['discount'])*$items[$i]['qty'];
			$c_total+=($items[$i]['price'])*$items[$i]['qty'];
			$itemids[]=$prod['id'];
			$itemnames[]=$prod['name'];
			$items[$i]['margin']=$voucher_margin;
			$items[$i]['tax']=$prod['tax'];
		}
		
		
		$avail=$this->erpm->do_stock_check($itemids);
		$nonredeemed_value=0;
		$non_redeemed_vdetails=$this->db->query("select b.book_id,d.franchise_id,a.voucher_serial_no,a.customer_value,a.status as voucher_status
														from pnh_t_voucher_details a
														join pnh_t_book_voucher_link b on b.voucher_slno_id = a.id
														join pnh_t_book_allotment d on d.book_id=b.book_id
														where voucher_code!=? and member_id=?  and is_activated=1 and a.status=3
														group by voucher_serial_no",array($v_code,$is_strkng_membr['pnh_member_id']));
	
		if($non_redeemed_vdetails)
		{
			foreach($non_redeemed_vdetails->result_array() as $non_redeemed_vdet)
			{
				$non_redeemed_vmenu=$this->db->query("select id,name from  pnh_menu where id in (select menu_ids from pnh_t_book_details a join pnh_m_book_template b on b.book_template_id=a.book_template_id where a.book_id=? and menu_ids=?)",array($non_redeemed_vdet['book_id'],$m['id']));
				if($non_redeemed_vmenu->num_rows())
				{
					$nonredeemed_value+=$non_redeemed_vdet['customer_value'];
				}
			}
		}
		if(!$nonredeemed_value)
			$nonredeemed_value=0;
	
		$part_redeemed_vouch_amt=$this->db->query("select sum(customer_value) as amt from pnh_t_voucher_details where status=5 and customer_value!=0 and member_id=?",$is_strkng_membr['pnh_member_id'])->row()->amt;
	
		$voucher_fifo=0;      //flag to redeem if available voucher balance>0
		$non_redeemed_voucher_consideration=0;
		$part_redeemed_voucher_codes = array();
	
		$ts_vouchervalue=$voucher_value; //given secret code voucher value
		if($voucher_value<$c_total && $nonredeemed_value!=0)
		{
			$voucher_value=$voucher_value+$nonredeemed_value;
			$non_redeemed_voucher_consideration=1;
	
			$vcodetest=$ts_vouchervalue-$c_total;
			if($vcodetest<0)
	
				$required_amt=$vcodetest*-1;
	
			$sql=$this->db->query("select b.book_id,d.franchise_id,a.voucher_serial_no,a.customer_value,a.status as voucher_status
											from pnh_t_voucher_details a
											join pnh_t_book_voucher_link b on b.voucher_slno_id = a.id
											join pnh_t_book_allotment d on d.book_id=b.book_id
											where voucher_code!=? and member_id=?  and is_activated=1 and a.status in (3,5)
											group by voucher_serial_no",array($v_code,$is_strkng_membr['pnh_member_id']));
			if($sql)
			{
				$cc=array();
				$cc['code']=array();
				$cc['value']=array();
				$value=0;
				foreach($sql->result_array() as $s)
				{
					$s_vmenu=$this->db->query("select id,name from  pnh_menu where id in (select menu_ids from pnh_t_book_details a join pnh_m_book_template b on b.book_template_id=a.book_template_id where a.book_id=? and menu_ids=?)",array($non_redeemed_vdet['book_id'],$m['id']));
					if($s_vmenu->num_rows())
					{
						if($value>=$required_amt)
							break;
						$value+=$s['customer_value'];
						array_push($cc['code'],$s['voucher_serial_no']);
						array_push($cc['value'],$s['customer_value']);
					}
				}
	
			}
	
		}
		if($ts_vouchervalue<$c_total)
		{
	
			$non_redeemed_voucher_consideration=1;
			$req_voucher_val = $c_total-$ts_vouchervalue; //200
	
	
			//check if pending vouchers can be used for redeem for current order
			$part_redeemed_vouch_amt=$this->db->query("select sum(customer_value) as amt from pnh_t_voucher_details where status=5 and customer_value!=0 and member_id=?",$is_strkng_membr['pnh_member_id'])->row()->amt;
			$vbal='';
			if(($part_redeemed_vouch_amt+$voucher_value) < $c_total)
			{
				if($part_redeemed_vouch_amt!=0)
				{
					$vbal="+ bal(".$part_redeemed_vouch_amt.")";
				}
				$this->_output_handle('json',false,array('error_code'=>2025,'error_msg'=>"Insufficient credit! Total value of purchase is Rs $c_total and voucher value is Rs $voucher_value $vbal"));
			}
		}
	
	
		$userid=$is_strkng_membr['user_id'];
		$transid=strtoupper("PNH".random_string("alpha",3).$this->erpm->p_genid(5));
		$this->db->query("insert into king_transactions(transid,amount,paid,mode,init,actiontime,is_pnh,franchise_id,voucher_payment,trans_created_by) values(?,?,?,?,?,?,?,?,1,?)",array($transid,$c_total,$d_total,3,time(),time(),1,$fran['franchise_id'],$admin['userid']));
	
		
		foreach($items as $item)
		{
	
			$inp=array("id"=>$this->erpm->p_genid(10),"transid"=>$transid,"userid"=>$userid,"itemid"=>$item['itemid'],"brandid"=>"");
			$inp["brandid"]=$this->db->query("select d.brandid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?",$item['itemid'])->row()->brandid;
			$inp["bill_person"]=$inp['ship_person']=$fran['franchise_name'];
			$inp["bill_address"]=$inp['ship_address']=$fran['address'];
			$inp["bill_city"]=$inp['ship_city']=$fran['city'];
			$inp['bill_pincode']=$inp['ship_pincode']=$fran['postcode'];
			$inp['bill_phone']=$inp['ship_phone']=$fran['login_mobile1'];
			$inp['bill_email']=$inp['ship_email']=$fran['email_id'];
			$inp['bill_state']=$inp['ship_state']=$fran['state'];
			$inp['quantity']=$item['qty'];
			$inp['time']=time();
			$inp['ship_landmark']=$inp['bill_landmark']=$fran['locality'];
			$inp['bill_country']=$inp['ship_country']="India";
			$inp['i_orgprice']=$item['mrp'];
			$inp['i_price']=$item['price'];
			$inp['i_discount']=$item['mrp']-$item['price'];
			$inp['i_coup_discount']=$item['discount'];
			$inp['i_tax']=$item['tax'];
			if($item['itemid']!=null)
			{
				$this->db->insert("king_orders",$inp);
				
				//disble the product in api cart table
				if($this->db->affected_rows())
					$this->db->query("update pnh_api_franchise_cart_info set status=0 where status=1 and franchise_id=? and pid=? and member_id=?",array($fid,$item['pid'],$member_id));
				
				$last_order_ids=$this->db->insert_id();
				$last_trans_id=$this->db->query("select transid from king_orders where sno=? limit 1",$last_order_ids)->row()->transid;
				$order_ids=$this->db->query("select group_concat(id) as order_ids from king_orders where transid=?",$last_trans_id)->row()->order_ids;
	
				$m_inp=array("transid"=>$transid,"itemid"=>$item['itemid'],"mrp"=>$item['mrp'],"price"=>$item['mrp'],"base_margin"=>0,"sch_margin"=>0,"bal_discount"=>0,"qty"=>$item['qty'],"final_price"=>$item['price']-$item['discount'],"voucher_margin"=>$voucher_margin);
				$this->db->insert("pnh_order_margin_track",$m_inp);
			}
		}
		$this->erpm->pnh_fran_account_stat($fran['franchise_id'],1, $d_total,"Order $transid - Total Amount: Rs $total","order",$transid);
		$points=$this->db->query("select points from pnh_loyalty_points where amount<? order by amount desc limit 1",$total)->row_array();
		if(!empty($points))
			$points=$points['points'];
		else $points=0;
	
	
		$ttl_voucher_amt_req = $c_total;
		$is_partially_redeemed=0;
	
		//echo $c_total;
	
	
	
		if($voucher_codes)
		{
			$voucher_code_used['gven_secretcode']=array();
			$voucher_code_used['othr_secretcode']=array();
	
	
			foreach($voucher_codes as $v_code)
			{
	
				if(!$ttl_voucher_amt_req)
					continue ;
	
				if($is_partially_redeemed)
					break;
	
				$is_alloted=$this->db->query("select * from pnh_t_voucher_details where franchise_id=? and voucher_code=? and is_alloted=1 and is_activated=1 and status in (3,5) ",array($fran['franchise_id'],$v_code))->row_array();
	
				$cus_value = $is_alloted['customer_value'];
				$new_cus_value=($is_alloted['customer_value']-$ttl_voucher_amt_req);
	
				//echo $new_cus_value.'<br>';
	
				//get available voucher value from db
				//$db_voucher_value = $this->db->query("select customer_value from pnh_t_voucher_details where voucher_code = ? ",$v_code)->row()->customer_value;
				$is_fully_redeemed = 0;
				if($new_cus_value<=0)
				{
					$is_fully_redeemed = 1;
					$new_cus_value = 0;
				}
				else
				{
					$is_partially_redeemed = 1;
				}
	
				$fran_value=$new_cus_value-($new_cus_value*$is_alloted['voucher_margin']/100);
	
				$this->db->query("update pnh_t_voucher_details set customer_value=?,franchise_value=?,status=?,redeemed_on=now() where voucher_code=?",array($new_cus_value,$fran_value,($is_fully_redeemed?4:5),$v_code));
				$this->db->query("insert into pnh_voucher_activity_log(voucher_slno,franchise_id,member_id,transid,debit,credit,order_ids,status)values(?,?,?,?,?,?,?,?)",array($is_alloted['voucher_serial_no'],$fran['franchise_id'],$is_strkng_membr['pnh_member_id'],$transid,$cus_value-$new_cus_value,0,$order_ids,1));
				array_push($voucher_code_used['gven_secretcode'],$is_alloted['voucher_serial_no']);
	
				$ttl_voucher_amt_req = $ttl_voucher_amt_req-($cus_value-$new_cus_value);
			}
	
	
			//exit;
	
			if($is_fully_redeemed && $non_redeemed_voucher_consideration==1)
			{
	
	
				$required_secretcode=$cc['code'];
	
	
				//echo $required_amt;
				//print_r($required_secretcode);
	
				if($required_secretcode)
				{
					foreach($required_secretcode as $required_vcode)
					{
	
						$vsres=$this->db->query("select * from pnh_t_voucher_details where voucher_serial_no=?",$required_vcode);
	
						if($vsres)
						{
							foreach($vsres->result_array() as $v)
							{
								if($is_partially_redeemed)
									break;
	
								$v_slno=$v['voucher_serial_no'];
								$v_margin=$v['voucher_margin'];
								$v_cusvalue=$required_amt-$v['customer_value'];//500-300
								$is_fully_redeemed=0;
								$is_partially_redeemed=0;
	
								$used_vouchval = 0;
	
								if($v_cusvalue < 0)
								{
									$v_cusvalue=$required_amt;
									$is_partially_redeemed = 1;
								}
								else
								{
									$is_fully_redeemed = 1;
									$v_cusvalue = $v['customer_value'];
								}
	
	
	
								$fran_value=($v['customer_value']-$v_cusvalue)-(($v['customer_value']-$v_cusvalue)*$v['voucher_margin']/100);
								$this->db->query("update pnh_t_voucher_details set customer_value=?,franchise_value=?,status=?,redeemed_on=now() where voucher_serial_no=?",array(($v['customer_value']-$v_cusvalue),$fran_value,($is_fully_redeemed?4:5),$v_slno));
								$this->db->query("insert into pnh_voucher_activity_log(voucher_slno,franchise_id,member_id,transid,debit,credit,order_ids,status)values(?,?,?,?,?,?,?,?)",array($v_slno,$fran['franchise_id'],$is_strkng_membr['pnh_member_id'],$transid,$v_cusvalue,0,$order_ids,1));
								$required_amt=$required_amt-$v_cusvalue;
								array_push($voucher_code_used['othr_secretcode'],$v['voucher_serial_no']);
							}
						}
	
					}
	
				}
			}
	
		}
		else
		{
	
			$usebal_v=array();
			$usebal_v['pre_redeemed']=array();
			$bal_details=$this->db->query("select * from pnh_t_voucher_details where status=5 and customer_value!=0 and member_id=?",$is_strkng_membr['pnh_member_id'])->result_array();
			$v_prebal=0;
			foreach($bal_details as $bal_det)
			{
	
				$fran_value=$c_total-($c_total*$bal_det['voucher_margin']/100);
				$v_prebal+=$bal_det['customer_value'];
				if($v_prebal>=$c_total)
				{
					array_push($usebal_v['pre_redeemed'],$bal_det['voucher_serial_no']);
					break;
				}else
					continue;
			}
			if($usebal_v['pre_redeemed'])
			{
	
				foreach ($usebal_v as $u_voucher)
				{
	
					$v_histry=$this->db->query("select * from pnh_t_voucher_details where voucher_serial_no=?",$u_voucher)->row_array();
					$v_slno=$v_histry['voucher_serial_no'];
					$v_margin=$v_histry['voucher_margin'];
					$v_cusvalue=$v_histry['customer_value']-$c_total;
					$fran_value=$c_total-($c_total*$v_histry['voucher_margin']/100);
					$is_fully_redeemed=0;
					$is_partially_redeemed=0;
					if($v_cusvalue<=0)
						$is_fully_redeemed = 1;
					else
						$is_partially_redeemed = 1;
	
					$this->db->query("update pnh_t_voucher_details set customer_value=?,franchise_value=?,status=?,redeemed_on=now() where voucher_serial_no=?",array($v_cusvalue,$fran_value,($is_fully_redeemed?4:5),$v_slno));
					$this->db->query("insert into pnh_voucher_activity_log(voucher_slno,franchise_id,member_id,transid,debit,credit,order_ids,status)values(?,?,?,?,?,?,?,?)",array($v_slno,$fran['franchise_id'],$is_strkng_membr['pnh_member_id'],$transid,$c_total,0,$order_ids,1));
				}
			}
	
		}
	
	
		$franid=$fran['franchise_id'];
		$billno=10001;
		$nbill=$this->db->query("select bill_no from pnh_cash_bill where franchise_id=? order by bill_no desc limit 1",$franid)->row_array();
		if(!empty($nbill))
			$billno=$nbill['bill_no']+1;
		$inp=array("bill_no"=>$billno,"franchise_id"=>$franid,"transid"=>$transid,"user_id"=>$userid,"status"=>1);
		$this->db->insert("pnh_cash_bill",$inp);
		$this->erpm->do_trans_changelog($transid,"PNH offline Order placed using vouchers ",$admin['userid']);
	
		$v_balamt=$this->db->query("SELECT SUM(customer_value) AS voucher_bal FROM pnh_t_voucher_details WHERE STATUS in(3,5) AND member_id=?",$is_strkng_membr['pnh_member_id'])->row()->voucher_bal;
		$from=trim($mem_det['mobile']);
		if( $voucher_code_used['othr_secretcode'])
			$given_vslnos=implode(',',$voucher_code_used['gven_secretcode']);
		$othr_vslnos=implode(',',$voucher_code_used['othr_secretcode']);
	
		$this->erpm->pnh_sendsms($from,"your balance after purchase is Rs $v_balamt,redeemed vouchers for rs $c_total are $given_vslnos,$othr_vslnos .Happy Shopping",$from,0,1);
	
	
		$this->_output_handle('json',true,array("order_msg"=>"Order placed successfully"));
	}
	
	
	
	
	/**
	 * function for searching the deals
	 */
	
	function search()
	{
		$this->_is_validauthkey();
		
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		$search_tag=$this->input->post('search_data');
		$start=$this->input->post('start');
		$limit=$this->input->post('limit');
		$gender=$this->input->post('gender');
		$brand_id=$this->input->post('brand_id');
		$cat_id=$this->input->post('category_id');
		$min_price=$this->input->post("min_price");
		$max_price=$this->input->post("max_price");
		$pids=$this->input->post('prd_id');
		
		if(!$start)
			$start=0;
		
		if(!$limit)
			$limit=10;
		
		if(!$brand_id)
			$brand_id=0;
		if(!$cat_id)
			$cat_id=0;
		
		if($search_tag=='' || strlen($search_tag)==0)
			$this->_output_handle('json',false,array('error_code'=>2016,'error_msg'=>"Please enter search keyword"));
		
		if(strlen($search_tag) < 3)
			$this->_output_handle('json',false,array('error_code'=>2015,'error_msg'=>"Please enter search keyword, min 3 chars required"));
		
		$menu_by_franchise=$this->apim->get_menus_by_franchise($fid);
		
		if($menu_by_franchise)
		{
			$menuid=array();
			foreach($menu_by_franchise as $m)
				array_push($menuid,$m['id']);
			
			$srch_data=array("tag"=>$search_tag,"menuid"=>implode(',',$menuid));
			
			$deal_det=$this->apim->get_deals($brand_id,$cat_id,$menu_id=0,$start,$limit,$pids,$srch_data,$gender,$min_price,$max_price);
			
			$brand_list=array();
			$category_list=array();
			
			if($deal_det['deals_list'])
			{
				foreach($deal_det['deals_list'] as $d)
				{
					if(!isset($brand_list[$d['brand_id']]))
						$brand_list[$d['brand_id']]=array('brandid'=>$d['brand_id'],"brandname"=>$d['brand']);
					
					if(!isset($category_list[$d['category_id']]))
						$category_list[$d['category_id']]=array('id'=>$d['category_id'],"name"=>$d['category']);
					
					
					if(!isset($category_list[$d['main_category_id']]) && $d['main_category_id'])
						$category_list[$d['main_category_id']]=array('id'=>$d['main_category_id'],"name"=>$d['main_category']);
					
				}
			}
			
			
			if($deal_det['deals_list'])
				$this->_output_handle('json',true,array("deal_list"=>$deal_det['deals_list'],'total_deals'=>$deal_det['ttl_deals'],'brand_list'=>$brand_list,'category_list'=>$category_list));
			else
				$this->_output_handle('json',true,array("deal_list"=>'','total_deals'=>0));
			
		}
	}
	
	/**
	 * function to get the voucher details
	 */
	function get_voucher_details()
	{
		$this->_is_validauthkey();
		
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		$mem_id=$this->input->post('member_id');
		$vcodes=$this->input->post('vouchers_code');
		
		if(!$mem_id || !$vcodes || !array_filter(explode(',',$vcodes)))
			$this->_output_handle('json',false,array('error_code'=>2018,'error_msg'=>"Member id and voucher code must be require"));
		
		if(!$this->apim->get_member_details($mem_id ))
			$this->_output_handle('json',false,array('error_code'=>2007,'error_msg'=>"No member available for given member id"));
		
		$voucher_det=array($mem_id=>array(),'status_config'=>array());
		$vcodes=array_filter(explode(',',$vcodes));
		foreach($vcodes as $vcode)
		{
		
			$vdet=$this->db->query("select b.book_id,d.franchise_id,a.voucher_serial_no,a.customer_value,a.status as voucher_status
												from pnh_t_voucher_details a
												join pnh_t_book_voucher_link b on b.voucher_slno_id = a.id
												join pnh_t_book_allotment d on d.book_id=b.book_id
												where voucher_code=? and member_id=?  and is_activated=1
												group by voucher_serial_no",array($vcode,$mem_id))->row_array();
			if($vdet)
			{
			
				if(!isset($voucher_det[$mem_id][$vcode]))
					$voucher_det[$mem_id][$vcode]=array();
				
				$voucher_det[$mem_id][$vcode]['voucher_code']=$vcode;
				$voucher_det[$mem_id][$vcode]['member_balance']=$vdet['customer_value'];
				
				//get the book menus
				$vmenus=$this->db->query("select id,name from  pnh_menu where id in (select menu_ids from pnh_t_book_details a join pnh_m_book_template b on b.book_template_id=a.book_template_id where a.book_id=?)",$vdet['book_id'])->row_array();
				$voucher_det[$mem_id][$vcode]['menu']=$vmenus['name'];
				
				if($vdet['voucher_status']>3)
				{
					if($vdet['voucher_status']==4)
						$voucher_det[$mem_id][$vcode]['status'] =4;
					
					if($vdet['voucher_status']==5)
						$voucher_det[$mem_id][$vcode]['status']=5;
				}
			
				if($vdet['voucher_status']==3)
					$voucher_det[$mem_id][$vcode]['status']=3;
			}else{
				$voucher_det[$mem_id][$vcode]['voucher_code']=$vcode;
				$voucher_det[$mem_id][$vcode]['member_balance']=0;
				$voucher_det[$mem_id][$vcode]['menu']='';
				$voucher_det[$mem_id][$vcode]['status']=0;
			}
		}
		
		$voucher_det['status_config'][0]='Deatails not found';
		$voucher_det['status_config'][4]='fully redeemed';
		$voucher_det['status_config'][3]='Activated';
		$voucher_det['status_config'][5]='partially redeemed';
		
		$this->_output_handle('json',true,array("voucher_details"=>$voucher_det));
	}
	
	/**
	 * function get the transactiond detauils
	 */
	function get_transaction_list()
	{
		$this->_is_validauthkey();
		
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		$from=$this->input->post('date_from');
		$to=$this->input->post('date_to');
		
		
		$start=$this->input->post('start');
		$limit=$this->input->post('limit');
		
		if(!$start)
			$start=0;
		
		if(!$limit)
			$limit=10;
		
		if(!$fid)
			$this->_output_handle('json',false,array('error_code'=>2017,'error_msg'=>"franchise id not found"));
		
		$transction_det=$this->apim->get_transactions($start,$limit,$fid,$from,$to);
		
		if($transction_det)
		{
			$this->_output_handle('json',true,array("transactio_list"=>$transction_det['trans_det'],"ttl_trans"=>$transction_det['ttl_trans']));
		}else{
			$this->_output_handle('json',true,array("transactio_list"=>'',"ttl_trans"=>''));
		}
	}
	
	/**
	 * function for get the franchise recceipts
	 */
	function get_franchise_receipts()
	{
		$this->_is_validauthkey();
		$this->load->model('erpmodel','erpm');
		
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		$type=$this->input->post('receipt_type');
		$start=$this->input->post('start');
		$limit=$this->input->post('limit');
		
		$receipts=array();
		$r_start=0;
		$r_limit=10;
		$modes=array("cash","Cheque","DD","Transfer");
		$receipt_status=array('Pending','Activated','Cancelled','Reversed');
		$receipt_type=array("Deposit","Topup");
		$full=1;
		$pending=0;
		$processed=0;
		$realized=0;
		$cancelled=0;
		
		if($type)
		{
			if($type=='pending')
				$pending=1;$full=0;
			
			if($type=='processed')
				$processed=1;$full=0;
			
			if($type=='realized')
				$realized=1;$full=0;
			
			if($type=='cancelled')
				$cancelled=1;$full=0;
		}
		
		if(!$start)
			$start=0;
		else
			$r_start=$start;
		
		if(!$limit)
			$limit=10;
		
		if(!$fid)
			$this->_output_handle('json',false,array('error_code'=>2017,'error_msg'=>"franchise id not found"));
		
		//get franchise account summary details
		$account_summary=$this->erpm->get_franchise_account_stat_byid($fid);
		$receipts['franchise_account_summary']=array('pending_payement'=>'','uncleared_payement'=>'','adjustments'=>'','paid_till_date'=>'','credit_note_rised'=>'','cancelled'=>'','unshipped'=>'','shipped'=>'','ordered'=>'','credit_limit'=>'');
		
		if($account_summary)
		{
			$receipts['franchise_account_summary']['pending_payement']=formatInIndianStyle($account_summary['shipped_tilldate']-($account_summary['paid_tilldate']+$account_summary['acc_adjustments_val']+$account_summary['credit_note_amt']),2);
			$receipts['franchise_account_summary']['uncleared_payement']=formatInIndianStyle($account_summary['uncleared_payment'],2);
			$receipts['franchise_account_summary']['adjustments']=formatInIndianStyle($account_summary['acc_adjustments_val'],2);
			$receipts['franchise_account_summary']['paid_till_date']=formatInIndianStyle($account_summary['paid_tilldate'],2);
			$receipts['franchise_account_summary']['credit_note_rised']=formatInIndianStyle($account_summary['cancelled_tilldate'],2);
			$receipts['franchise_account_summary']['cancelled']=formatInIndianStyle($account_summary['cancelled_tilldate'],2);
			$receipts['franchise_account_summary']['unshipped']=formatInIndianStyle($account_summary['not_shipped_amount'],2);
			$receipts['franchise_account_summary']['shipped']=formatInIndianStyle($account_summary['shipped_tilldate'],2);
			$receipts['franchise_account_summary']['ordered']=formatInIndianStyle($account_summary['ordered_tilldate'],2);
			
		}
		
		//franchise receipts details
		$receipts['receipt_list']=array('pending'=>array('total_receipts'=>'','total_value'=>'','receipts'=>'','payment_mode_config'=>''),'processed'=>array('total_receipts'=>'','total_value'=>'','receipts'=>'','payment_mode_config'=>''),'realized'=>array('total_receipts'=>'','total_value'=>'','receipts'=>'','payment_mode_config'=>''),'cancelled'=>array('total_receipts'=>'','total_value'=>'','receipts'=>'','payment_mode_config'=>''));
		
		//pending receipts
		if($full || $pending)
		{
			$pending_receipts_det=$this->apim->get_pending_receipts($fid,$r_start,$r_limit);
			
			if($pending_receipts_det['total_receipts'])
			{
				$receipts['receipt_list']['pending']['total_receipts']=$pending_receipts_det['total_receipts'];
				$receipts['receipt_list']['pending']['total_value']=$pending_receipts_det['receipts_total_value'];
				$receipts['receipt_list']['pending']['receipts']=$pending_receipts_det['pending_receipts'];
				$receipts['receipt_list']['pending']['payment_mode_config']=$modes;
			}
		}
		
		//processed receipts
		if($full || $processed)
		{
			$processed_receipts_det=$this->apim->get_processed_receipts($fid,$r_start,$r_limit);
			
			if($processed_receipts_det['total_receipts'])
			{
				$receipts['receipt_list']['processed']['total_receipts']=$processed_receipts_det['total_receipts'];
				$receipts['receipt_list']['processed']['total_value']=$processed_receipts_det['receipts_total_value'];
				$receipts['receipt_list']['processed']['receipts']=$processed_receipts_det['processed_receipts'];
				$receipts['receipt_list']['processed']['payment_mode_config']=$modes;
			}
		}
		
		//realized receipts
		if($full || $realized)
		{
			$realized_receipts=$this->apim->get_realized_receipts($fid,$r_start,$r_limit);
			
			if($realized_receipts['total_receipts'])
			{
				$receipts['receipt_list']['realized']['total_receipts']=$realized_receipts['total_receipts'];
				$receipts['receipt_list']['realized']['total_value']=$realized_receipts['receipts_total_value'];
				$receipts['receipt_list']['realized']['receipts']=$realized_receipts['realized_receipts'];
				$receipts['receipt_list']['realized']['payment_mode_config']=$modes;
			}
		}
		
		//cancelled receipts
		if($full || $cancelled)
		{
			$cancelled_reeipts_det=$this->apim->get_cancelled_receipts($fid,$r_start,$r_limit);
			if($cancelled_reeipts_det['total_receipts'])
			{
				$receipts['receipt_list']['cancelled']['total_receipts']=$cancelled_reeipts_det['total_receipts'];
				$receipts['receipt_list']['cancelled']['total_value']=$cancelled_reeipts_det['receipts_total_value'];
				$receipts['receipt_list']['cancelled']['receipts']=$cancelled_reeipts_det['cancelled_receipts'];
				$receipts['receipt_list']['cancelled']['payment_mode_config']=$modes;
			}
		}
		
		$receipts['receipt_status_config']=$receipt_status;
		$receipts['receipt_type_config']=$receipt_type;
		
		$this->_output_handle('json',true,array("receipts_detail"=>$receipts));
		
	}
	
	/**
	 * validate the given imei number
	 * @param unknown_type $imei_no
	 * @return boolean
	 */
	function _chk_imei_exist($imei_no)
	{
		$imei_det_res = $this->db->query("select * from t_imei_no where imei_no = ? ",$imei_no);
		
		if(!$imei_det_res->num_rows())
		{
			$this->form_validation->set_message('_chk_imei_exist','Invalid IMEINO Entered or IMEINO not sold to any franchise');
			return false;
		}
		return true;
	}
	
	/**
	 * validate given imei numbers belongs to given franchise
	 */
	function _chk_imei_belongsto_fnch($imei_no)
	{
		$imei_no=$this->input->post('imei_no');
		$fid=$this->input->post('franchise_id');
		
		//check given imei number belongs to loged franchise
		$imei_franchise_res=$this->db->query("select c.franchise_id from t_imei_no a
													join king_orders b on b.id=a.order_id
													join king_transactions c on c.transid=b.transid
												where a.imei_no=? and c.franchise_id =?;",array($imei_no,$fid));
		if(!$imei_franchise_res->num_rows)
		{
			$this->form_validation->set_message('_chk_imei_belongsto_fnch','Sorry this imei not belongs to yours');
			return false;
		}
		
		return true;
	}
	
	/**
	 * function for get the imei details
	 */
	function get_imei_details()
	{
		$this->_is_validauthkey();
		
		$imei_no=$this->input->post('imei_no');
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('imei_no','IMEI no','required|callback__chk_imei_exist|callback__chk_imei_belongsto_fnch');
		$this->form_validation->set_rules('franchise_id','franchise_id','required');
		if($this->form_validation->run() === FALSE)
		{
			$this->_output_handle('json',false,array('error_code'=>2033,'error_msg'=>strip_tags(validation_errors())));
		}else{
			
			//check imei sheme
			$this->apim->check_imei_scheme($imei_no);
			
			//get imei details
			$imei_details=	$this->apim->get_imeidet($imei_no);
			
			if(!$imei_details)
				$this->_output_handle('json',false,array('error_code'=>2031,'error_msg'=>"IMEINO Details not found"));
			else
				$this->_output_handle('json',true,array("imei_detail"=>$imei_details));
			
		}
	}
	
	/**
	 * function for validate mobile number while imei activation time
	 */
	function validate_imei_mobileno()
	{
		$this->_is_validauthkey();
		
		$mobile_no=$this->input->post('mobile_no');
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('mobile_no','Mobile no','required|integer|exact_length[10]');
		
		if($this->form_validation->run() === FALSE)
		{
			$this->_output_handle('json',false,array('error_code'=>2032,'error_msg'=>strip_tags(validation_errors())));
		}else{
			
			$output=array('member_id'=>'','pending_activation'=>0,'status'=>'0','status_config'=>array('1'=>'Existing member','2'=>'New mobileno'));
			
			
			$mem_det=$this->apim->get_member_by_mob($mobile_no);
			
			if($mem_det)
			{
				$output['member_id']=$mem_det['pnh_member_id'];
				$output['status']=1;
			}else{
				$output['member_id']='';
				$output['status']=2;
			}
			
			$output['pending_activation'] = $this->apim->get_mem_pending_imei_act_det($mobile_no);
			
			$this->_output_handle('json',true,array("validation_res"=>$output));
			
		}
	}
	
	
	/**
	 * function for check the pending imei activaion for given mobile number member.
	 * @param unknown_type $mobno
	 */
	function _chk_pending_imei_acts($mobno)
	{
		$mobno = $this->input->post('mobile_no');
		
		$pend=$this->apim->get_mem_pending_imei_act_det($mobno);
		
		if($pend==0 && $pend!=false)
		{
			$this->form_validation->set_message('_chk_pending_imei_acts','Activation Limit Ended for this mobileno');
			return false;
		}
		
		return true;
	}
	
	/**
	 * function for used to activate the franchise imei to given member details
	 */
	function franchise_imei_activation()
	{
		$this->_is_validauthkey();
		
		$this->load->model('erpmodel','erpm');
		
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		$mobno = $this->input->post('mobile_no');
		$imeino = $this->input->post('imei_no');
		$actv_confrim = $this->input->post('actv_confrim');
		$mem_name = $this->input->post('member_name');
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('imei_no','IMEI no','required|callback__chk_imei_exist|callback__chk_imei_belongsto_fnch|callback__chk_pending_imei_acts');
		$this->form_validation->set_rules('franchise_id','franchise_id','required');
		$this->form_validation->set_rules('mobile_no','Mobile no','required|integer|exact_length[10]');
		if($this->form_validation->run() === FALSE)
		{
			$this->_output_handle('json',false,array('error_code'=>2034,'error_msg'=>strip_tags(validation_errors())));
		
		}else{
			
			$member_details='';
			$member_id='';
			$imeino_list = array($imeino);
			//check imei sheme
			$this->apim->check_imei_scheme($imeino);
			
			//get imei details
			$imei_details=	$this->apim->get_imeidet($imeino);
			
			if($actv_confrim)
			{
				$mem_det=$this->apim->get_member_by_mob($mobno);//get the member details for confirmed existing given member mobile number
				$member_id=$mem_det['pnh_member_id'];
			}else{
				$mem_det=$this->apim->get_member_by_mob($mobno);
				
				if($mem_det)//if not confirmed then check give mobile number member exist,if exist then rise the require confirmation error
					$this->_output_handle('json',false,array('error_code'=>2035,'error_msg'=>"Confirmation require for given IMEI activation details"));
				else{
					//if given mobile number member not exist then check the imei first purchased member mobile number if exist,not exist then given mobile number update to imei purchased member id and send member id through sms to updated mobile number
					if(!$imei_details['mobileno'] || $imei_details['mobileno']=='' || strlen($imei_details['mobileno']) < 10)
					{
						$this->db->query("update pnh_member_info set first_name=?,mobile = ? where pnh_member_id = ? limit 1",array($mem_name,$mobno,$imei_details['member_id']));
						
						if($this->db->affected_rows()>=1)
						{
							$this->erpm->pnh_sendsms($mobno,"Congratulation & Welcome to StoreKing Family,Your Member id is:".$imei_details['member_id'],$fid,$imei_details['member_id'],0);
							$member_id=$imei_details['member_id'];
						}
					}else{
						$member_id=$this->apim->add_new_member($fid,$mem_name,$mobno);//creating new member
						
						if($member_id)
							$this->erpm->pnh_sendsms($mobno,"Congratulation & Welcome to StoreKing Family,Your Member id is:".$member_id,$fid,$member_id,0);
						else
							$this->_output_handle('json',false,array('error_code'=>2036,'error_msg'=>"Sorry not able to process IMEI activation"));
					}
				}
			}
			
			if($member_id)
			{
				$user_det=$this->apim->get_api_user();
				$imei_act=$this->apim->update_imei_activation($mobno,$member_id,$user_det['userid'],$imeino);
				
				$ttl_imei_actv_credit = 0;
				
				if($imei_act)
				{
					$oid = $this->db->query('select order_id from t_imei_no where status = 1 and order_id != 0 and imei_no = ? ',$imeino)->row()->order_id;
					$imei_ref_id = $this->db->query('select id from t_imei_no where status = 1 and order_id != 0 and imei_no = ? ',$imeino)->row()->id;
					$invno = $this->db->query('select invoice_no from king_invoice where invoice_status = 1 and order_id = ? ',$oid)->row()->invoice_no;
					$imei_credit = $this->db->query('select imei_reimbursement_value_perunit as amt from king_orders a join t_imei_no b on a.id = b.order_id where b.imei_no = ? ',$imeino)->row()->amt;
					$ttl_imei_actv_credit += $imei_credit;
					
					$member_userid = $this->db->query("select user_id from pnh_member_info where pnh_member_id = ? ",$member_id)->row()->user_id;
					
					$this->db->query("update king_orders set userid=?,member_scheme_processed=1 where id=? and member_scheme_processed=0",array($member_userid,$oid));
					
					// create creditnote document entry
					$arr = array($fid,$imei_ref_id,$invno,$imei_credit,cur_datetime(),$user_det['userid']);
					$this->apim->insert_credit_notes($arr);
					$credit_note_id = $this->db->insert_id();
					
					//update credit note to account summary
					$arr = array($fid,7,$credit_note_id,$invno,$imei_credit,'imeino : '.$imeino,1,cur_datetime(),$user_det['userid']);
					$this->db->query("insert into pnh_franchise_account_summary (franchise_id,action_type,credit_note_id,invoice_no,credit_amt,remarks,status,created_on,created_by) values(?,?,?,?,?,?,?,?,?)",$arr);
					
					$this->db->query("update t_imei_no set ref_credit_note_id = ? where imei_no = ? and ref_credit_note_id = 0 ",array($credit_note_id,$imeino));
				
					$fran_det = $this->db->query("select franchise_id as id,login_mobile1 as mob from pnh_m_franchise_info where franchise_id = ? ",$fid)->row_array();
				
					//Compose IMEI/Serialno Activation Message
					$sms_msg = 'Congratulations!!! Your IMEINO : '.implode(',',$imeino_list).' Activated';
					if($ttl_imei_actv_credit)
					{
						$sms_msg .= ' and Amount of Rs '.($ttl_imei_actv_credit).' has been credited to your account';
						// create franchise credit note and update the same to pnh franchise account summary.
						$this->erpm->pnh_sendsms($fran_det['mob'],$sms_msg,$fran_det['id']);
					}
					
					$this->_output_handle('json',true,array("IMEI_activation_res"=>"IMEI/Serailno Activation processed"));
				}else{
					$this->_output_handle('json',false,array('error_code'=>2036,'error_msg'=>"Sorry not able to process IMEI activation"));
				}
			}else{
				$this->_output_handle('json',false,array('error_code'=>2036,'error_msg'=>"Sorry not able to process IMEI activation"));
			}	
		
		}//form validation
		
	}
	
	/**
	 * function for posting a complaints
	 */
	function post_complaints()
	{
		$this->_is_validauthkey();
		
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		$complaint_name=$this->input->post('complaint_name');
		$complaint=$this->input->post('complaint');
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('franchise_id','FranchiseID','required');
		$this->form_validation->set_rules('complaint_name','Complaint name','required');
		$this->form_validation->set_rules('complaint','Complaint','required');
		if($this->form_validation->run() === FALSE)
		{
			$this->_output_handle('json',false,array('error_code'=>2037,'error_msg'=>strip_tags(validation_errors())));
		
		}else{
			$user_det=$this->apim->get_api_user();
			$post=$this->apim->insert_franchise_complaints($complaint_name,$complaint,$fid,$user_det['userid']);
			
			if($post)
				$this->_output_handle('json',true,array("complaint_post_res"=>"Your complaint posted successfully"));
			else
				$this->_output_handle('json',false,array('error_code'=>2038,'error_msg'=>"Your complaint not posted"));
				
		}
	}
	
	/**
	 * function for get the offers list
	 */
	function get_offer_list()
	{
		$this->_is_validauthkey();
		
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		$start=$this->input->post('start');
		$limit=$this->input->post('limit');
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('franchise_id','FranchiseID','required');
		if($this->form_validation->run() === FALSE)
		{
			$this->_output_handle('json',false,array('error_code'=>2039,'error_msg'=>strip_tags(validation_errors())));
		
		}else{
			
			if(!$start)
				$start=0;
			if(!$limit)
				$limit=10;
			
			$offer_list=$this->apim->get_offers($fid,$start*1,$limit*1);
			
			if($offer_list['offers'])
			{
				foreach($offer_list['offers'] as $i=>$o)
				{
					if(strtotime($o['offer_start']) < time()  && strtotime($o['offer_end']) > time() && $o['is_active']==1)
						$o['status']='Offer running';
					else
						$o['status']='Offer expired';
					
					$o['offer_start']=format_datetime($o['offer_start']);
					$o['offer_end']=format_datetime($o['offer_end']);
					
					$offer_list['offers'][$i]=$o;
				}
			}
			
			$this->_output_handle('json',true,array("offer_details"=>$offer_list));
		}
	}
	
	/**
	 * function for get the franchise returnd details
	 */
	function get_returns_details()
	{
		$this->_is_validauthkey();
		
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		$start=$this->input->post('start');
		$limit=$this->input->post('limit');
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('franchise_id','FranchiseID','required');
		if($this->form_validation->run() === FALSE)
		{
			$this->_output_handle('json',false,array('error_code'=>2040,'error_msg'=>strip_tags(validation_errors())));
		
		}else{
		
			if(!$start)
				$start=0;
			if(!$limit)
				$limit=10;
			
			$return_details=$this->apim->get_inv_returns_details($fid,$start,$limit);
			
			$this->_output_handle('json',true,array("return_details"=>$return_details));
		}
	}
	
	/**
	 * function for check the price updates
	 */
	function check_updates()
	{
		//$this->_is_validauthkey();
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		$version=$this->input->post('version');
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('version','Version','required');
		if($this->form_validation->run() === FALSE)
		{
			$this->_output_handle('json',false,array('error_code'=>2045,'error_msg'=>strip_tags(validation_errors())));
		
		}else{
			
			$update_version_det=$this->apim->get_new_update_versions($version);
			
			if($update_version_det['error'])
				$this->_output_handle('json',false,array('error_code'=>2046,'error_msg'=>"Given version details not found"));
			
			$updating_products=$this->apim->get_updated_deals($update_version_det['verions']);
			
			$this->_output_handle('json',true,array("updates_details"=>$updating_products,'site_img_path'=>'http://static.snapittoday.com/items',"latest_version"=>$update_version_det['verions'][count($update_version_det['verions'])-1]['version']));
		}
	}

	/**
	 * function for check for version updates.
	 */
	function check_version_update()
	{
		$version=$this->input->post('version');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('version','Version','required');
		if($this->form_validation->run() === FALSE)
		{
			$this->_output_handle('json',false,array('error_code'=>2045,'error_msg'=>strip_tags(validation_errors())));
		}else{
			
			$update_version_det=$this->apim->get_new_update_versions($version);
			
			if($update_version_det['error'])
				$this->_output_handle('json',false,array('error_code'=>2046,'error_msg'=>"Given version details not found"));

			$this->_output_handle('json',true,@$update_version_det['verions'][count($update_version_det['verions'])-1]['version']);
		}
	}
	
	/**
	 * function to check deal updates 
	 */
	function check_deal_update()
	{
		
		$pid=$this->input->post('pid');
		$item_id=$this->input->post('item_id');
		
		$d_res = $this->db->query("select a.id as item_id,pnh_id as pid,orgprice,price,publish from king_dealitems a join king_deals b on a.dealid = b.dealid where pnh_id = ? and a.id = ? ",array($pid,$item_id));
		if(!$d_res->num_rows())
		{
			// check if deal is unlimited stock 
			$this->_output_handle('json',false,array('error_code'=>2046,'error_msg'=>"Deal has been expired"));
		}else
		{
			$deal_info = $d_res->row_array();
			$res_d = $this->db->query("select b.id as item_id,b.pnh_id as pid,menuid as pmenu_id,e.name as pmenu,b.name as pname,catid as pcat_id,c.name as pcat,
					cp.id as parent_cat_id,cp.name as parent_cat,brandid as pbrand_id,d.name as pbrand,
					b.orgprice as pmrp,b.price as pprice,publish as is_sourceable,
					b.gender_attr,b.url,b.is_combo,a.description as pdesc,a.keywords as kwds,
					a.pic as pimg,menuid as pimg_path,b.shipsin as shipin
					from king_deals a
					join king_dealitems b on a.dealid = b.dealid
					join king_categories c on c.id = a.catid
					left join king_categories cp on cp.id = c.type
					join king_brands d on d.id = a.brandid
					join pnh_menu e on e.id = a.menuid
					where b.id = ? and b.pnh_id != 0
					",$deal_info['item_id']);
			if($res_d->num_rows())
			{
				$row_d = $res_d->row_array();
			
				foreach($row_d as $k1=>$v1)
					$tmp[$k1] = htmlspecialchars($v1);
			
				$deal_info['pimages'] = @$this->db->query("select group_concat(distinct id) as images from king_resources where itemid = ? order by id desc",$row['item_id'])->row()->images;
				$deal_info['attributes'] = "";
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
				where b.is_group = 1 and a.itemid = ? and a.is_active = 1 and d.is_sourceable = 1 
				group by a.product_id
				)
				) as g ";
				$attr_res = $this->db->query($sql_a,array($deal_info['item_id'],$deal_info['item_id']));
				if($attr_res->num_rows())
				{
					$deal_info['attributes'] = @$attr_res->row()->attrs;
				}
			}
			
			$deal_info['attributes'] = (is_null($deal_info['attributes'])?'':$deal_info['attributes']);
			$deal_info['max_order_qty'] = 10;//$this->erpm->get_maxordqty_pid(0,$deal_info['pid']); 
			
			$this->_output_handle('json',true,$deal_info);
		}
	}
	
	
	function update_apk_version()
	{
		$version=$this->input->post('version');

		$cur_version = array();
		$cur_version['version'] = $version;
		$cur_version['download_link'] = 'http://cloud.github.com/downloads/stephanenicolas/RoboDemo/robodemo-sample-1.0.1.apk';
		
		$this->_output_handle('json',true,$apk_updates_info);
		
	}
	
	/**
	  * function to check if cart items are available 
	 */
	function check_cart_items()
	{
		//print_r($_POST);
		$pids = $this->input->post('pids');
		$pqtys = $this->input->post('pqtys');
		
		$fid = $this->input->post('fid');
		$fran_mobno = $this->input->post('f_mobno');
		
		$mem_id = $this->input->post('m_id');
		$mem_name = $this->input->post('m_name');
		$mem_mobno = $this->input->post('m_mobno');
		
		if(!valid_mobileno($mem_mobno))
			$this->_output_handle('json',false,array('error_code'=>2046,'error_msg'=>"Please enter valid member mobileno."));
		
		$op = array();
		
		if(!count($pids))
			$this->_output_handle('json',false,array('error_code'=>2046,'error_msg'=>"Deal has been expired"));
		
		if(!($fdet = $this->franchise_model->get_franchise_details($fran_mobno)))
			$this->_output_handle('json',false,array('error_code'=>2046,'error_msg'=>"Invalid Franchise Mobileno"));
		else
			$fid = $fdet['franchise_id'];

		$fid = $fid*1;
		
		// registere new member on confirmation.
		if($mem_id == '0' && !$this->member_model->is_mem_reg($mem_mobno))
		{
			if(strlen($mem_name))
			{
				// register new member by name and mobile.
				$mem_det = array();
				$mem_det['name'] = $mem_name;
				$mem_det['mobno'] = $mem_mobno;
				$this->member_model->register_memberbydet($fid,$mem_det);
				 
			}
		}
		
		// check if member is registered.
		
		$op['franchise_id'] = $fid;
		
		if($fid)
		{
			$fran_crdet = $this->erpm->get_fran_availcreditlimit($fid);
			$op['purchase_limit'] = format_price($fran_crdet[3],0);
		}
		
		$op['member_id'] = 0;
		$op['member_name'] = '';
		$op['member_mobno'] = 0;
		$op['fran_mobno'] = $fran_mobno;
		
		$op['member_id'] = 0;
		$op['is_new_mem'] = 0;
		$op['is_first_order'] = 0;
		// check for stock and insurance details for the products if applicable and associated with the member 
		// check if member is registered 
		$member_id = $this->member_model->is_mem_reg($mem_mobno);
		if($member_id)
		{
			$member_det = $this->member_model->get_member_details($mem_mobno);
			$op['member_name'] = $member_det['first_name'];
			$op['member_id'] = $member_id;
			$op['is_new_mem'] = ($this->member_model->get_memberorders_ttl($member_id))?0:1;
			
			$op['member_id'] = $member_det['pnh_member_id'];
			$op['member_mobno'] = $member_det['mobile'];
		}
		
		if($op['is_new_mem'])
			$op['member_fee'] = 50;
		else
			$op['member_fee'] = 0;
		
		// check for insurance deals 
		
		$pid_nos = implode(',',$pids);

		$itemdet_res = $this->db->query("select b.id as itemid,b.pnh_id as pid,orgprice,price,publish,has_insurance,ifnull(insurance_value,0) as inv_val,ifnull(insurance_margin,0) as inv_marg 
											from king_deals a 
											join king_dealitems b on a.dealid = b.dealid 
											left join pnh_member_insurance_menu c on c.menu_id = a.menuid and b.price between greater_than and less_than and c.is_active = 1   
											where pnh_id in (".$pid_nos.") 	
										");
		$op['items'] = array();
		if($itemdet_res->num_rows())
		{
			foreach($itemdet_res->result_array() as $itemdet)
			{
				
				if($itemdet['price'] < 5000)
					$itemdet['has_insurance'] = 0;
				else if($itemdet['price'] > 5000 && $itemdet['price'] < 10000)
					$itemdet['mem_insurance_amt'] = $itemdet['inv_val'];
				else if($itemdet['has_insurance'])
				{
					// compute new insurance amount 
					if($op['is_new_mem'])
						$itemdet['mem_insurance_amt'] = $itemdet['inv_val'];
					else
						$itemdet['mem_insurance_amt'] = round($itemdet['orgprice']*$itemdet['inv_marg']/100);
				}
				
				$itemdet['max_order_qty'] = 10;//$this->erpm->get_maxordqty_pid($fid,$itemdet['pid']);
				
				$op['items'][$itemdet['pid']] = $itemdet;
			}
		}
		
		$this->_output_handle('json',true,$op);
		
	}
	
	/**
	 * function to add new order. 
	 */
	function create_order()
	{
		// Prepare post params for submittion 
		$coupon_code = $this->input->post('coupon_code');
		$fran_mobno = $this->input->post('f_mobno');
		$member_id = $this->input->post('m_id');
		$pids = $this->input->post('pids');
		$pqtys = $this->input->post('pqtys');
		$insurance = $this->input->post('insurance');
		$member = $this->input->post('member');
		$offer_type = (int)$this->input->post('offer_type');
		$redeem = (int)$this->input->post('redeem');
		$attr_pid = $this->input->post('attr_pid');
		$attr_pid_val = $this->input->post('attr_pid_val');
		$opt_insurance_pids = $this->input->post('opt_insurance');
		$member = $this->input->post('member');
		
		if($opt_insurance_pids)
		{
			$insurance['opted_insurance'] = 1;
			$insurance_deals = array();
			foreach($opt_insurance_pids as $opt_pid)
			{
				//$itmid = $this->apim->get_itemdetbypnhid($opt_pid);
				array_push($insurance_deals,$opt_pid);
			}
			$insurance['insurance_deals'] = implode(',',$insurance_deals);
		}else
		{
			$insurance['opted_insurance'] = 0;
		}
		
		// prepare selected attributes to supoort with create order function
		$d_attr = array();
		if(!empty($attr_pid))
		{
			foreach($attr_pid as $k=>$pnh_id)
			{
				$itmid = $this->apim->get_itemdetbypnhid($pnh_id);
				$d_attr[$itmid] = $attr_pid_val[$k];
			}
		}
		
		$insurance['opted_insurance'] = isset($insurance['opted_insurance'])?$insurance['opted_insurance']:0;
		$insurance['insurance_deals'] = isset($insurance['insurance_deals'])?$insurance['insurance_deals']:'';
		$insurance['proof_type'] = isset($insurance['proof_type'])?$insurance['proof_type']:0;
		$insurance['proof_id'] = isset($insurance['proof_id'])?$insurance['proof_id']:0;
		$insurance['first_name'] = isset($insurance['first_name'])?$insurance['first_name']:'';
		$insurance['last_name'] = isset($insurance['last_name'])?$insurance['last_name']:'';
		$insurance['mob_no'] = isset($insurance['mob_no'])?$insurance['mob_no']:'';
		$insurance['address'] = isset($insurance['address'])?$insurance['address']:'';
		$insurance['city'] = isset($insurance['city'])?$insurance['city']:'';
		$insurance['pincode'] = isset($insurance['pincode'])?$insurance['pincode']:'';
		$insurance['proof_name'] = isset($insurance['proof_name'])?$insurance['proof_name']:'';
		$insurance['proof_address'] = isset($insurance['proof_address'])?$insurance['proof_address']:'';
		
		// get franchise details 
		$fran_detail = $this->franchise_model->get_franchise_details($fran_mobno);
		
		// prepare data to submit to create order module  
		$order_params = array();
		$order_params['fid'] = $fid = $fran_detail['franchise_id'];
		$order_params['mid'] = $mid = $member_id;
		$order_params['pid'] = $pid = implode(',',$pids);
		$order_params['qty'] = $qty = implode(',',$pqtys);
		$order_params['member'] = $member = $member;
		$order_params['offer_type'] = $offr_sel_type = $offer_type;
		$order_params['redeem'] = $redeem;
		$order_params['d_attr'] = $d_attr;
		$order_params['insurance'] = $insurance;
		
		
		$offr_sel_type = 0;
		
		$this->to_process_order($fid,$mid,$pid,$qty,$offr_sel_type,$redeem,0,$insurance,$d_attr);
		
		//$resp = $this->order_model->create_order($order_params);
		
		// post response to via json 
		//$this->_output_handle('json',(($resp['status'] != 'error')?true:false),$resp);
		
	}

	function chk_sms()
	{
		
		$to = '09740793973';
		$msg = 'hi shariff';
		
		$exotel_sid='snapittoday';
		$exotel_token='491140e9fbe5c507177228cf26cf2f09356e042c';
		$post = array(
				'From'   => '09243404342',
				'To'    => $to,
				'Body'  => $msg,
				'StatusCallback'=>'http://snapittoday.com/api/sms_state'
		);
		$url = "https://".$exotel_sid.":".$exotel_token."@twilix.exotel.in/v1/Accounts/".$exotel_sid."/Sms/send";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FAILONERROR, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
		$http_result = curl_exec($ch);
		$error = curl_error($ch);
		$http_code = curl_getinfo($ch ,CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		$this->load->plugin('xml2array');
		$sms_det_arr = xml2array($xmlStr,1);
		
		$sms_sid = '';
		$sms_status = '';
		if(isset($sms_det_arr['TwilioResponse']['SMSMessage']['Sid']))
		{
			$sms_sid = $sms_det_arr['TwilioResponse']['SMSMessage']['Sid'];
			$sms_status = $sms_det_arr['TwilioResponse']['SMSMessage']['Status'];
			$sms_status_feed = $sms_det_arr['TwilioResponse']['SMSMessage']['Status'].':'.cur_datetime();
		}
		
		$ins_det = array();
		$ins_det['sms_sid'] = $sms_sid;
		$ins_det['sms_to'] = $to;
		$ins_det['sms_msg'] = $msg;
		$ins_det['status'] = $sms_status;
		$ins_det['status_update_feed'] = $sms_status_feed;
		$ins_det['sms_response'] = json_encode($sms_det_arr);
		$ins_det['logged_on'] = cur_datetime();
		$this->db->insert("t_raw_sms_log",$ins_det);
		
	}
	
	
	
	function SmsSid()
	{
		
		$xmlStr = '
<?xml version="1.0" encoding="UTF-8"?>
<TwilioResponse>
 <SMSMessage>
  <Sid>41690593a7b8ee2f80cd08f3392d671a</Sid>
  <DateUpdated>2014-05-02 18:53:39</DateUpdated>
  <DateCreated>2014-05-02 18:53:39</DateCreated>
  <DateSent>1970-01-01 05:30:00</DateSent>
  <AccountSid>snapittoday</AccountSid>
  <To>09740793973</To>
  <From>/snapittoday</From>
  <Body>hi shariff</Body>
  <BodyIndex/>
  <Status>queued</Status>
  <Direction>outbound-api</Direction>
  <Price/>
  <ApiVersion/>
  <Uri>/v1/Accounts/snapittoday/Sms/Messages/41690593a7b8ee2f80cd08f3392d671a</Uri>
 </SMSMessage>
</TwilioResponse>
	';
		$xmlarr = xml2array($xmlStr,1);
		print_r($xmlarr);
	}
	
	function chk_margin_pnh($fid=0,$pid=0)
	{
		error_reporting(E_ALL);
		$this->load->model('erpmodel',"erpm");
		$mrg = $this->erpm->get_pnh_margin($fid,$pid);
		print_r($mrg);
	}
	
	
	/**
	 * function for process the order
	 */
	private function to_process_order($fid=0,$mid=0,$pid=0,$qty=0,$offr_sel_type=0,$redeem='',$return=0,$insurance,$d_attr=array())
	{
		$min_ord = 500;
		//http://localhost/snapittoday_live/api/to_process_order?fid=17&mid=22006889&pid=11314779&qty=1&offer_type=1&insurance[opted_insurance]=&insurance[insurance_deals]=14967769&insurance[proof_type]=&insurance[proof_id]=&insurance[first_name]=&insurance[last_name]=&insurance[mob_no]=&insurance[address]=&insurance[city]=&insurance[pincode]=&insurance[proof_name]=&insurance[proof_address]=&redeem=
		//$this->_is_validauthkey();
		$this->load->model('erpmodel','erpm');
		$admin['userid'] = 6;
		$updated_by=$admin['userid'];
		if(!$insurance)
		{
			$insurance=array();
			$insurance['opted_insurance']=$insurance['insurance_deals']=$insurance['proof_type']=$insurance['proof_id']=$insurance['first_name']=$insurance['last_name']=$insurance['mob_no']=$insurance['address']=$insurance['city']=$insurance['pincode']=$insurance['proof_name']=$insurance['proof_address']='';
		}
		//if set get variable data the parameters are receiving from get
	
		if($_GET && !empty($_GET))
		{
			$_POST=$_GET;
			$fid=$this->input->post("fid");
			$mid=$this->input->post('mid');
			$pid=$this->input->post('pid');
			$qty=$this->input->post('qty');
			$offr_sel_type=$_GET['offer_type'];
			$insurance=$_GET['insurance'];
			$redeem=$_GET['redeem'];
			$return=0;
		}
	
		$pid=explode(',', $pid);
		$qty=explode(',',$qty);
	
		if($redeem)
			$redeem_points = 150;
	
		$fran=$this->db->query("select * from pnh_m_franchise_info where franchise_id=?",$fid)->row_array();
	
		$fran_status=$fran['is_suspended'];//franchise suspension status
		$mem_info = $this->erpm->get_memberinfo_byid($mid);
		$has_super_scheme=0;
		$has_scheme_discount=0;
		$has_member_scheme=0;
		$has_offer=0;
	
		if($fran['is_suspended']==1 || $fran['is_suspended']==3)
		{
			if($return)
				return array('error_code'=>2046,'error_msg'=>"Franchisee is suspended");
			else
				$this->_output_handle('json',false,array('error_code'=>2046,'error_msg'=>"Franchisee is suspended"));
			exit;
		}
		$mem_det_res=$this->db->query("select * from pnh_member_info where (pnh_member_id=? or mobile=?)",array($mid,$mid));
	
		$fran_det_res=$this->db->query("select * from pnh_m_franchise_info where (login_mobile1=? or login_mobile2=?)",array($mid,$mid));
		if($fran_det_res->num_rows())
			$order_for=2;
	
		if($mem_det_res->num_rows==0 && $fran_det_res->num_rows()==0)
		{
			if($return)
				return array('error_code'=>2046,'error_msg'=>"Invalid Member ID");
			else
				$this->_output_handle('json',false,array('error_code'=>2046,'error_msg'=>"Invalid Member ID"));
			exit;
		}
		else
		{
			$mem_det=$mem_det_res->row_array();
			$mid=@$mem_det['pnh_member_id'];
		}
	
		$userid=@$this->db->query("select user_id from pnh_member_info where pnh_member_id=?",$mid)->row()->user_id;
	
		$new_member=$is_new_member=0;  //flag to check is new member
		if($mem_det)
		{
			$ttl_orders=$this->db->query("select count(*) as ttl from king_orders where userid=? and status!=3",$userid)->row()->ttl;
			if($ttl_orders==0)
			{
				$new_member=$is_new_member=1;
				$order_for=1;
			}
			else
			{
				$new_member=$is_new_member=0;
				$order_for=0;
			}
		}
		$is_new_member=$new_member;
	
		$key_member=$order_for==2?1:0;
	
		if($fran_status==0)
			$batch_enabled = 1;
		else
			$batch_enabled = 0;
	
	
	
		$pids=array();
		$pids['available']=array();
		$pids['not_available']=array();
		$h_insurance = 0;
		$ttl_order_value = 0;
		foreach($pid as $ki=>$p)
		{
			$deal=$this->db->query("select d.menuid,b.name as brand,c.name as cat,i.id,i.is_combo,i.pnh_id as pid,i.live,i.orgprice as mrp,i.price,i.name,i.pic,d.publish,i.has_insurance,CONCAT(print_name,'-',pnh_id) AS print_name from king_dealitems i join king_deals d on d.dealid=i.dealid  left join king_brands b on b.id = d.brandid join king_categories c on c.id = d.catid where pnh_id=? and is_pnh=1",$p)->row_array();
			
			if($deal['publish'])
				array_push($pids['available'], $p);
			else
				array_push($pids['not_available'], $p);
			
			/*
			$avail=$this->erpm->do_stock_check(array($deal['id']),array(1),true);
			$avail_det = array_values($avail);
			if($avail_det[0][0]['stk']==0 && $deal['is_sourceable']==0)
				array_push($pids['not_available'], $p);
			else
				array_push($pids['available'], $p);
			*/
			
			$menuid=$this->db->query("select d.menuid,m.default_margin as margin from king_dealitems i join king_deals d on d.dealid=i.dealid JOIN pnh_menu m ON m.id=d.menuid where i.is_pnh=1 and i.pnh_id=?",$p)->row_array();
	
			if($deal['has_insurance'])
				$h_insurance = 1;
	
			$super_scheme=$this->db->query("select * from pnh_super_scheme where menu_id=? and is_active=1 and franchise_id = ? limit 1",array($menuid['menuid'],$fid))->row_array();
			//super scheme enabled for scheme discount
			if(!empty($super_scheme))
			{
				if($super_scheme['valid_from']<time() && $super_scheme['valid_to']>time() && $super_scheme['is_active'] == 1)
					$has_super_scheme=1;
			}
	
			$member_scheme=$this->db->query("select * from imei_m_scheme where is_active=1 and franchise_id=? and ? between sch_apply_from and scheme_to order by created_on desc limit 1",array($fid,time()))->row_array();
			//member scheme enabled for scheme discount
			if(!empty($member_scheme))
			{
				$has_member_scheme=1;
			}
	
	
			$offer_scheme=$this->db->query("select * from pnh_m_offers where menu_id=? and franchise_id=? and ? between offer_start and offer_end order by id desc limit 1",array($menuid['menuid'],$fid,time()))->row_array();
			if(!empty($offer_scheme))
			{
				$has_offer=1;
			}
			
			
			$ttl_order_value += ($deal['price']*$qty[$ki]);
	
		}

		$insurance_selected=0; 
		//------------offer type setting block---------------
		if($h_insurance)
		{
			if($insurance)
			{
				if(isset($insurance['opted_insurance']))
					if($insurance['opted_insurance'])
						$insurance_selected=1;
			}
		
			//if insurance exist but new member if not select insurance then offer type to set 3
			if($insurance_selected==0 && $new_member==1)
			{
				if($ttl_order_value >= 10000)
					$offr_sel_type=1;
				else
					$offr_sel_type=3;
			}
		
			//if insurance exist but old member if not select insurance then offer type to set 0
			if($insurance_selected==0 && $new_member==0)
				$offr_sel_type=0;
		
			//if insurance exist and new member if select insurance then offer type to set 2
			if($new_member==1 && $insurance_selected!=0)
				$offr_sel_type=2;
		
			//if insurance exist and old member if select insurance then offer type to set 0
			if($new_member==0 && $h_insurance && $insurance_selected!=0)
				$offr_sel_type=0;
		
		}else{
			if($new_member==1 && $h_insurance==0 &&  $ttl_order_value >= $min_ord)
				$offr_sel_type=1;
		
			if($new_member==1 && $h_insurance==0 &&  $ttl_order_value < $min_ord)
				$offr_sel_type=3;
		}
		//------------offer type setting block end---------------
	
		if($pids['not_available'])
		{
			$resp_error = array('error_code'=>2046,'error_msg'=>"Sorry For inconvenience \r\n ProductID : ".implode(',',$pids['not_available'])." is out of stock at this time.");
			if($return)
				return $resp_error;
			else
				$this->_output_handle('json',false,$resp_error);
		}
		if($pids['available'])
			$pids=$pids['available'];
		$menu_ofr_pricevalue=array();
		$items=array();
		foreach($pids as $i=>$p)
			$items[]=array("pid"=>$p,"qty"=>$qty[$i]);
	
		$total=0;
		$d_total=0;
		$o_total=0;
		$commision=0;
		$item_pnt = @$redeem_points/count($items);
		$redeem_value = 0;
		
		
		
		
	
		foreach($items as $i=>$item)
		{
	
			$prod=$this->db->query("select i.*,d.publish,c.loyality_pntvalue,d.menuid from king_dealitems i join king_deals d on d.dealid=i.dealid JOIN pnh_menu c ON c.id = d.menuid where i.is_pnh=1 and  i.pnh_id=? and i.pnh_id!=0",$item['pid'])->row_array();
	
			$ordered_menus_list[]=$prod['menuid'];
	
			if(empty($prod))
				die("There is no product with ID : ".$item['pid']);
	
			$items[$i]['tax']=$prod['tax'];
			$items[$i]['mrp']=$prod['orgprice'];
			if($fran['is_lc_store'])
				$items[$i]['price']=$prod['store_price'];
			else
				$items[$i]['price']=$prod['price'];
			$items[$i]['itemid']=$prod['id'];
	
			$margin=$this->erpm->get_pnh_margin($fran['franchise_id'],$item['pid']);
	
			$imei_disc=$this->erpm->get_franimeischdisc_pid($fran['franchise_id'],$item['pid']);
			if($imei_disc==0 && $key_member==1 && $prod['price']<=5000 && $prod['menuid']==112)
			{
				if($margin['margin']>=0.5)
				{
					$margin['margin']=$margin['margin']-0.5;
					$margin['base_margin']=$margin['base_margin']-0.5;
				}
			}
	
			if($prod['is_combo']=="1")
				$items[$i]['discount']=$items[$i]['price']/100*$margin['combo_margin'];
			else
				$items[$i]['discount']=$items[$i]['price']/100*$margin['margin'];
	
			$items[$i]['billon_orderprice']=$prod['billon_orderprice'];
			$items[$i]['margin']=$margin;
			$total+=$items[$i]['price']*$items[$i]['qty'];
	
			$menu_ofr_pricevalue[$prod['menuid']][]=$items[$i]['price']*$items[$i]['qty'];
	
			$d_total+=($items[$i]['price']-$items[$i]['discount'])*$items[$i]['qty'];
			$o_total+=$items[$i]['price']*$items[$i]['qty'];
			$commision+=$items[$i]['discount']*$items[$i]['qty'];
			$itemids[]=$prod['id'];
			$itemnames[]=$prod['name'];
			$loyalty_pntvalue=$prod['loyality_pntvalue'];
	
			// offers check
			if($offr_sel_type == 2 || $insurance['opted_insurance'] == 1 || $key_member==1 )
			{
				$insurance['menuids'][$item['pid']] = $prod['menuid'];
				$insurance['order_value'][$item['pid']] = $items[$i]['price'];
			}
		}
		
		
		
	
		 
		
		foreach($menu_ofr_pricevalue as $i=>$t)
		{
			$menu_ttlval[$i]=array_sum($t);
		}
	
		$l_points=array();
		foreach($menu_ttlval as $l_menu_id=>$ttl_l_amt)
		{
			$points=@$this->db->query("SELECT points FROM pnh_loyalty_points WHERE menu_id=? AND ?>=amount AND is_active=1  ORDER BY amount DESC LIMIT 1",array($l_menu_id,$ttl_l_amt))->row()->points;
			$l_points[$l_menu_id]=$points*1;
		}
		if($redeem)
			$redeem_value += $item_pnt_value = $item_pnt*$prod['loyality_pntvalue'];
	
		$fran_crdet = $this->erpm->get_fran_availcreditlimit($fran['franchise_id']);
		$fran['current_balance'] = $fran_crdet[3];
	
		if($fran['current_balance']<$d_total)
		{
			$error_resp = array('error_code'=>2046,'purchase_limit'=>(format_price($fran_crdet[3],0)),'error_msg'=>"111Insufficient balance! Balance in your account Rs {$fran['current_balance']} Total order amount : Rs $d_total");
			if($return)
				return $error_resp;
			else
				$this->_output_handle('json',false,$error_resp);
		}
		$transid=strtoupper("PNH".random_string("alpha",3).$this->erpm->p_genid(5));
	
		$pnh_member_fee=0;
		if($is_new_member == 1 && $key_member == 0 )
		{
			$pnh_member_fee=PNH_MEMBER_FEE;
			$fee_det = array($mid,$transid,'',$pnh_member_fee,1,$updated_by);
			$this->db->query("insert into pnh_member_fee (member_id,transid,invoice_no,amount,status,created_on,created_by) VALUES(?,?,?,?,?,now(),?)",$fee_det);
		}
	
		$this->db->query("insert into king_transactions(transid,amount,paid,mode,init,actiontime,is_pnh,franchise_id,trans_created_by,batch_enabled,order_for,pnh_member_fee)
				values(?,?,?,?,?,?,?,?,?,?,?,?)"
				,array($transid,$d_total,$d_total,3,time(),time(),1,$fran['franchise_id'],$admin['userid'],$batch_enabled,$order_for,$pnh_member_fee)) or die(mysql_error());
	
	
		foreach($items as $item)
		{
			// check if belongs to split invoice condiciton config
			$split_order=$this->db->query("SELECT i.*,d.publish,c.loyality_pntvalue FROM king_dealitems i JOIN king_deals d ON d.dealid=i.dealid JOIN pnh_menu c ON c.id = d.menuid WHERE i.is_pnh=1 AND  i.pnh_id=? AND i.pnh_id!=0 AND c.id IN(112,118,122)",$item['pid'])->row_array();
			//$split_order = 0;
			if($split_order)
			{
				$ttl_qty = $item['qty'];
				$p_qty = 1;
			}else
			{
				$ttl_qty = 1;
				$p_qty = $item['qty'];
			}
	
			for($qi=0;$qi<$ttl_qty;)
			{
	
				$qi = $qi+$p_qty;
	
				$inp=array("id"=>$this->erpm->p_genid(10,'order'),"transid"=>$transid,"userid"=>$userid,"itemid"=>$item['itemid'],"brandid"=>"");
	
				$item['qty'] = $p_qty;
	
				$inp["brandid"]=$this->db->query("select d.brandid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?",$item['itemid'])->row()->brandid;
				$brandid=$inp["brandid"];
				$catid=$this->db->query("select d.catid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?",$item['itemid'])->row()->catid;
				$menuid=$this->db->query("select d.menuid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=? and menuid2=0",$item['itemid'])->row()->menuid;
				$inp["bill_person"]=$inp['ship_person']=$fran['franchise_name'];
				$inp["bill_address"]=$inp['ship_address']=$fran['address'];
				$inp["bill_city"]=$inp['ship_city']=$fran['city'];
				$inp['bill_pincode']=$inp['ship_pincode']=$fran['postcode'];
				$inp['bill_phone']=$inp['ship_phone']=$fran['login_mobile1'];
				$inp['bill_email']=$inp['ship_email']=$fran['email_id'];
				$inp['bill_state']=$inp['ship_state']=$fran['state'];
				$inp['quantity']=$p_qty;//$item['qty'];
				$inp['time']=time();
				$inp['ship_landmark']=$inp['bill_landmark']=$fran['locality'];
				$inp['bill_country']=$inp['ship_country']="India";
				$inp['i_orgprice']=$item['mrp'];
				$inp['i_price']=$item['price'];
				$inp['i_discount']=$item['mrp']-$item['price'];
				$inp['i_coup_discount']=$item['discount'];
				$inp['redeem_value']=($item['price']/($total+$redeem_value))*$redeem_value;
				$inp['billon_orderprice']=$item['billon_orderprice'];
				$inp['member_id']=$mid;
	
				if($split_order && $key_member==1)
				{
	
					$membr_id=$this->erpm->_gen_uniquememberid();
					if($this->db->query("select * from pnh_member_info where pnh_member_id=?",$membr_id)->num_rows()==0);
					$inp['member_id']=$membr_id;
					$inp['is_ordqty_splitd']=1;
	
					$this->db->query("insert into king_users(name,is_pnh,createdon) values(?,1,?)",array("PNH Member: $membr_id",time()));
					$userid=$this->db->insert_id();
					$inp['userid']=$userid;
					$this->db->query("insert into pnh_member_info(pnh_member_id,user_id,franchise_id,created_by,created_on)values(?,?,?,?,?)",array($membr_id,$userid,$fid,$admin['userid'],time()));
	
					//KEY MEMBER
	
					if($key_member==1)
					{
	
						//echo $this->db->last_query();die();
						$item_total=$item['price']*$p_qty;
						if($item_total<=5000 && $menuid==112)
						{
							$inp['pnh_member_fee']=0;
							$inp['insurance_amount']=0;
							if($imei_disc==0)
							{
								$key_mem_imei=0.5;
								$this->db->query("insert into imei_m_scheme(franchise_id,menuid,categoryid,brandid,scheme_type,credit_value,scheme_from,scheme_to,sch_apply_from,created_on,created_by,is_active)values(?,?,?,?,?,?,unix_timestamp(curdate()),unix_timestamp(curdate()),unix_timestamp(curdate()),unix_timestamp(curdate()),?,1)",array($fid,$menuid,$catid,$brandid,1,$key_mem_imei,$updated_by));
								$key_imei_id=$this->db->insert_id();
								$inp['imei_reimbursement_value_perunit']=(($inp['i_orgprice']-($inp['i_discount']+$inp['i_coup_discount']))*$key_mem_imei/100);
								$inp['imei_scheme_id']=$key_imei_id;
	
								//Disabling IMEI scheme after key member order is placed;
								if($key_imei_id)
									$this->db->query("Update imei_m_scheme set is_active=0 where is_active=1 and id=?",$key_imei_id);
							}else
							{
								$inp['imei_scheme_id']=$imei_disc['id'];
	
								if($imei_disc['scheme_type']==0)
									$inp['imei_reimbursement_value_perunit']=$imei_disc['credit_value'];
								else
									$inp['imei_reimbursement_value_perunit']=(($inp['i_orgprice']-($inp['i_discount']+$inp['i_coup_discount']))*$imei_disc['credit_value']/100);
	
							}
						}
						if($item_total>5000 && $item_total<=10000)
						{
							$insurance_id = random_string("nozero", $len=2).time(); //$this->get_insurance_id();
							$insurance_deals=$item['itemid'];
							$inp['pnh_member_fee']=PNH_MEMBER_FEE;
							$inp['insurance_amount']=0;
							$inp['insurance_id']=$insurance_id;
							$d_total+=$inp['pnh_member_fee']+$inp['insurance_amount'];
							$pnh_member_fee+=$inp['pnh_member_fee'];
							$inp['imei_scheme_id']=0;
							$inp['imei_reimbursement_value_perunit']=0;
						}
	
						if($item_total>10000)
						{
							$insurance_id = random_string("nozero", $len=2).time();
							$inp['pnh_member_fee']=PNH_MEMBER_FEE;
							$insuranc_cost=(($item['price']-10000)*1)/100;
							$inp['insurance_amount']=$insuranc_cost;
							$inp['insurance_id']=$insurance_id;
							$inp['has_insurance']=1;
							$d_total+=$insuranc_cost+PNH_MEMBER_FEE;
							$pnh_member_fee+=$inp['pnh_member_fee'];
							$inp['imei_scheme_id']=0;
							$inp['imei_reimbursement_value_perunit']=0;
						}
					}
				}
	
				$inp['i_tax']=$item['tax'];
	
				if($has_member_scheme==1 && $key_member!=1)
				{
					//check item enabled for member scheme
					$check_mbrschdisableditem=$this->db->query("select * from pnh_membersch_deals where is_active=0 and ? between valid_from and valid_to and itemid=? order by created_on desc limit 1",array(time(),$item['itemid']))->row_array();
	
					$member_scheme_brand=$this->db->query("select * from imei_m_scheme where menuid=? and categoryid=? and brandid=? and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$catid,$brandid,$fid))->result_array();
					if(empty($member_scheme_brand))
						$member_scheme_brand=$this->db->query("select * from imei_m_scheme where menuid=? and categoryid=? and brandid=0 and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$catid,$fid))->result_array();
					if(empty($member_scheme_brand))
						$member_scheme_brand=$this->db->query("select * from imei_m_scheme where menuid=? and categoryid=0 and brandid=? and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$brandid,$fid))->result_array();
					if(empty($member_scheme_brand))
						$member_scheme_brand=$this->db->query("select * from imei_m_scheme where menuid=? and categoryid=0 and brandid=0 and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$fid))->result_array();
	
					if(!empty($member_scheme_brand)  && empty($check_mbrschdisableditem))
						foreach($member_scheme_brand as $member_scheme)
						{
							$inp['imei_scheme_id']=$member_scheme['id'];
							if($member_scheme['scheme_type']==0)
								$inp['imei_reimbursement_value_perunit']=$member_scheme['credit_value'];
							else
								$inp['imei_reimbursement_value_perunit']=(($inp['i_orgprice']-($inp['i_discount']+$inp['i_coup_discount']))*$member_scheme['credit_value']/100);
						}
				}
				//if super scheme is enabled
	
				if($has_super_scheme!=0)
				{
	
					//check item enabled for super scheme
					$check_superschdisableditem=$this->db->query("select * from pnh_superscheme_deals where is_active=0 and ? between valid_from and valid_to and itemid=? order by created_on desc limit 1",array(time(),$item['itemid']))->row_array();
	
					//$super_scheme_brand=$this->db->query("select * from pnh_super_scheme where brand_id=? and franchise_id = ? ",array($brandid,$fid))->result_array();
					$super_scheme_brand=$this->db->query("select * from pnh_super_scheme where menu_id=? and cat_id=? and brand_id=? and franchise_id = ? and is_active=1",array($menuid,$catid,$brandid,$fid))->result_array();
					if(empty($super_scheme_brand))
						$super_scheme_brand=$this->db->query("select * from pnh_super_scheme where menu_id=? and cat_id=0 and brand_id=? and franchise_id = ? and is_active=1 order by id desc limit 1",array($menuid,$brandid,$fid))->result_array();
					if(empty($super_scheme_brand))
						$super_scheme_brand=$this->db->query("select * from pnh_super_scheme where menu_id=? and cat_id=? and brand_id=0 and franchise_id = ? and is_active=1 order by id desc limit 1",array($menuid,$catid,$fid))->result_array();
					if(empty($super_scheme_brand))
						$super_scheme_brand=$this->db->query("select * from pnh_super_scheme where menu_id=? and cat_id=0 and brand_id=0 and franchise_id = ? and is_active=1 order by id desc limit 1",array($menuid,$fid))->result_array();
					//print_r($super_scheme_brand);
					if(!empty($super_scheme_brand) && empty($check_superschdisableditem))
					{
	
						foreach($super_scheme_brand as $super_scheme)
						{
							if($super_scheme['valid_from']<time() && $super_scheme['valid_to']>time() && $super_scheme['is_active'] == 1)
							{
	
								$inp['super_scheme_logid']=$super_scheme['id'];
								$inp['has_super_scheme']=1;
								$inp['super_scheme_target']=$super_scheme['target_value'];
								$inp['super_scheme_cashback']=$super_scheme['credit_prc'];
							}
						}
					}
				}
	
				if($has_offer==1)
				{
					$offer_det=$this->db->query("select * from pnh_m_offers where menu_id=? and cat_id=? and brand_id=? and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$catid,$brandid,$fid))->result_array();
	
					if(empty($offer_det))
						$offer_det=$this->db->query("select * from pnh_m_offers where menu_id=? and cat_id=? and brand_id=0 and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$catid,$fid))->result_array();
					if(empty($offer_det))
						$offer_det=$this->db->query("select * from pnh_m_offers where menu_id=? and cat_id=0 and brand_id=? and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$brandid,$fid))->result_array();
	
					if(empty($offer_det))
						$offer_det=$this->db->query("select * from pnh_m_offers where menu_id=? and cat_id=0 and brand_id=0 and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$fid))->result_array();
	
					if(!empty($offer_det))
					{
						foreach($offer_det as $offer)
						{
							$inp['has_offer']=1;
							$inp['offer_refid']=$offer['id'];
						}
					}
	
				}
				$this->db->insert("king_orders",$inp);
				foreach($this->db->query("select group_id from m_product_group_deal_link where itemid=?",$inp['itemid'])->result_array() as $g)
				{
					/*
					$attr_n=array();
					$attr_v=array();
					foreach($this->db->query("select attribute_name_id from products_group_attributes where group_id=?",$g['group_id'])->result_array() as $p)
					{
						$attr_n[]=$p['attribute_name_id'];
						$attr_v[]=$this->input->post($item['pid']."_".$p['attribute_name_id']);
					}
					$sql="select product_id from products_group_pids where attribute_name_id=? and attribute_value_id=?";
					foreach($this->db->query($sql,array($attr_n[0],$attr_v[0]))->result_array() as $p)
					{
						$f=true;
						foreach($attr_n as $i=>$an)
							if($this->db->query("select 1 from products_group_pids where product_id=? and attribute_name_id=? and attribute_value_id=?",array($p['product_id'],$an,$attr_v[$i]))->num_rows()==0)
							$f=false;
						if($f)
							break;
					}
					*/
					$this->db->insert("products_group_orders",array("transid"=>$transid,"order_id"=>$inp['id'],"product_id"=>$d_attr[$inp['itemid']]));
				}
				
				// new
				if($this->db->query("select is_group from king_dealitems where id=? and is_group = 1 ",$inp['itemid'])->num_rows())
				{
					if(isset($d_attr[$inp['itemid']]))
					{
						// check if the product has default vendor linked
						$ven_id = @$this->db->query("select vendor_id from m_vendor_product_link where product_id = ? ",$d_attr[$inp['itemid']])->row()->vendor_id;
						$ven_id = $ven_id*1;
						// update ordered product_id
						$this->db->query("update king_orders set order_product_id = ?,order_from_vendor=? where id = ? ",array($d_attr[$inp['itemid']],$ven_id,$inp['id']));
					}
				}
	
				$bal_discount_amt = ($item['price']*$item['margin']['bal_discount']/100)*$item['qty'];
				$m_inp=array("transid"=>$transid,"itemid"=>$item['itemid'],"mrp"=>$item['mrp'],"price"=>$item['price'],"base_margin"=>$item['margin']['base_margin'],"sch_margin"=>$item['margin']['sch_margin'],"qty"=>$item['qty'],"final_price"=>$item['price']-$item['discount']);
				$this->db->insert("pnh_order_margin_track",$m_inp);
				$id=$this->db->insert_id();
			}
		}
		$bal_discount_amt_msg = '';
		if($bal_discount_amt)
			$bal_discount_amt_msg = ', Topup Damaka Applied : Rs'.$bal_discount_amt;
	
		$this->erpm->pnh_fran_account_stat($fran['franchise_id'],1, $d_total,"Order $transid - Total Amount: Rs $total".$bal_discount_amt_msg,"transaction",$transid);
	
		$balance=$this->db->query("select current_balance from pnh_m_franchise_info where franchise_id=?",$fran['franchise_id'])->row()->current_balance;
	
		
		// ======================< MEMBER ORDER SMS STATRS >===================================
		$ttl_trans_cost = $this->erpm->trans_fee_insu_value($transid);
		$this->erpm->sendsms_franchise_order($transid,$ttl_trans_cost,$o_total);
		// ======================< MEMBER ORDER SMS ENDS >===================================
		
		//Alotting Loyalty Points
		$trans_order_det_res = $this->db->query("SELECT o.id AS order_id,o.itemid,d.menuid,o.i_price,o.quantity,t.order_for
				FROM king_dealitems i
				JOIN king_deals d ON d.dealid=i.dealid
				JOIN king_orders o ON o.itemid = i.id
				join king_transactions t on t.transid=o.transid
				WHERE o.transid=?
				",$transid);
		if($trans_order_det_res->num_rows())
		{
			foreach($trans_order_det_res->result_array() as $trans_ord_det)
			{
				$menuid = $trans_ord_det['menuid'];
				$order_amt=$trans_ord_det['i_price']*$trans_ord_det['quantity'];
				if($key_member==0)
					$ord_loyal_pnt = (($trans_ord_det['i_price']*$trans_ord_det['quantity'])*$l_points[$menuid])/$menu_ttlval[$menuid];
				else
					$ord_loyal_pnt = $this->db->query("select points from pnh_loyalty_points where amount < ? order by amount desc limit 1",$order_amt)->row()->points;
	
				$this->db->query("update king_orders set loyality_point_value=? where  transid=? and id=? ",array($ord_loyal_pnt,$transid,$trans_ord_det['order_id']));
			}
		}
		// Process to batch this transaction
		$ttl_num_orders=count($items);
		$batch_remarks='Created by pnh offline order system';
	
		//$this->reservations->do_batching_process($transid,$ttl_num_orders,$batch_remarks,$updated_by);
	
	
	
		//===================< Implement the member offers START>============================//
		if($key_member==1)
		{
			$datetime=date("Y-m-d H:i:s",time());
			//check_for_insurancance applicable order
			$key_mem_insurance_items=$this->db->query("select * from king_orders where transid=? and insurance_id is not null",$transid);
			if($key_mem_insurance_items)
			{
				foreach($key_mem_insurance_items->result_array() as $kydl)
				{
					$key_mem_insu_id=$kydl['insurance_id'];
					$kydl['fid']=$fid;
					$key_mid=$kydl['member_id'];
					$itemid=$kydl['itemid'];
					$order_id=$kydl['id'];
					$insurance_value=$kydl['insurance_amount'];
					$ofr_towords=$kydl['i_price']-$kydl['i_coup_discount'];
	
					$this->db->query("insert into pnh_member_insurance(fid,mid,offer_type,opted_insurance,order_id,itemid,insurance_value,created_by,created_on)values(?,?,2,1,?,?,?,?,?)",array($fid,$key_mid,$order_id,$itemid,$insurance_value,$updated_by,$datetime));
					$insurance_id=$this->db->insert_id();
					$this->db->query("insert into pnh_member_offers(insurance_id,franchise_id,member_id,offer_type,order_id,transid_ref,offer_value,created_by,created_on,process_status,delivery_status,feedback_status,offer_towards,pnh_pid) values(?,?,?,2,?,?,?,?,?,0,0,0,?,?)",array($insurance_id,$fid,$key_mid,$order_id,$transid,$insurance_value,$updated_by,$datetime,$ofr_towords,$itemid));
					$this->db->query("update king_orders set insurance_id=? where transid=? and insurance_id=? and id=?",array($insurance_id,$kydl['transid'],$key_mem_insu_id,$kydl['id']));
				}
			}
		}
		$menu_list=array_unique($ordered_menus_list);
		$insurance['mid'] =$mid;
		$insurance['fid'] =$fid;
		$insurance['offer_type'] =$offr_sel_type;
		$insurance['transid'] = $transid;
		$insurance['created_by'] = $updated_by;
	
		// check is member fee paid?
		$insurance['pnh_member_fee'] = PNH_MEMBER_FEE;
	
		// =================< Check total member orders >======================
		$orders=$this->db->query("SELECT COUNT(DISTINCT(a.transid)) AS l FROM king_orders a
				join pnh_member_info b on b.user_id=a.userid
				WHERE b.pnh_member_id=?  AND a.status NOT IN (3)",$insurance['mid'])->row()->l;
		if($orders > 1)
			$insurance['mem_fee_applicable'] = 0;
		else
			$insurance['mem_fee_applicable'] = 1;
	
		$insurance['new_member']=$new_member;
	
		if($offr_sel_type == 2 && $insurance['opted_insurance'] == 1 && $new_member == 1)
		{
			//process insurance document and address details & get insurance process id
			$insu_id = $this->erpm->process_insurance_details($insurance);
			//echo '<pre>';print_r($insurance);die();
		}elseif($offr_sel_type == 3 && $new_member == 1)
		{
			$insurance['offer_type'] = 3;
			$insu_id = $this->erpm->process_insurance_details($insurance);
		}
		elseif($offr_sel_type == 2  && $new_member == 1)
		{
			$insurance['offer_type'] = 3;
			$offer_ret = $this->erpm->pnh_member_fee($d_total,$insurance);
		}
		elseif($offr_sel_type == 0 && $insurance['opted_insurance'] == 1 && $new_member == 0)
		{
			//process insurance document and address details & get insurance process id
			$insurance['mem_fee_applicable'] = 0;
			$insurance['pnh_member_fee'] = 0;
			$insu_id = $this->erpm->process_insurance_details($insurance);
	
		}
	
		elseif($offr_sel_type == 1 && $o_total >= MEM_MIN_ORDER_VAL && $new_member == 1)
		{
	
			$offer_ret = $this->erpm->pnh_member_recharge($o_total,$insurance);
		}
	
		//===================< Implement the member offers END>============================
		$ttl_insurance_amt=$this->db->query("select ifnull(sum(insurance_amount),0) as insurance_amount from king_orders where transid=?",$transid)->row()->insurance_amount;
	
		$order_res=$this->db->query("select order_for,o.*,di.name as item_name,pnh_id from king_orders o join king_transactions t on t.transid = o.transid join king_dealitems di on di.id = o.itemid where o.transid=?",$transid);
		$transid= '';
		$trans_amt = 0;
		$ttl_member_fee = 0;
		$order_det_arr=array();
		foreach($order_res->result_array() as $i=>$order_det)
		{
	
			$transid= $order_det['transid'];
	
			$order = array();
			$order['order_id'] = $order_det['id'];
			$order['product_id'] = $order_det['pnh_id'];
			$order['item_name'] = $order_det['item_name'];
			$order['mrp'] = $order_det['i_orgprice'];
			$order['offer_price'] = $order_det['i_orgprice']-$order_det['i_discount'];
			$order['has_insurance'] = $order_det['has_insurance'];
			$order['insurance_fee'] = $order_det['insurance_amount'];
			$order['franchise_price'] = $order_det['i_orgprice']-$order_det['i_discount']-$order_det['i_coup_discount'];
			$order['franchise_price_percentage'] = 100-($order['franchise_price']/$order['offer_price'])*100 .'%';
	
			// che ck if its key member order to process member for individual members by member id
			if($order_det['order_for'] != 2)
				$order['member_fee'] = @$this->db->query("select amount from pnh_member_fee where member_id = ? and transid = ? ",array($order_det['member_id'],$order_det['transid']))->row()->amount*1;
			else
				$order['member_fee'] = $order_det['pnh_member_fee']*(!$i); // reset for memberid for member fee
	
			$trans_amt += $order['franchise_price'];
			$ttl_member_fee += $order['member_fee'] ;
	
			$order_det_arr[] = $order;//array('transid'=>$order_det['transid'],'product_name'=>$prod_name,'Order_amt'=>$ordr_amt,'insurance_amt'=>$insu_fee,'member_fee'=>$mem_fee,'commission'=>$commission,'avail_bal'=>$avail_bal)
	
		}
	
		$trans_amt += $ttl_member_fee;
	
		//$this->_output_handle('json',true,array('transid'=>$order_det['transid'],'order_details'=>$memfee));
		$order_for=$order_for==2?'Key Member':'Non key Member';
		//print_r(array('trans'=>$transid,'orders'=>$order_det_arr,'total_member_fee'=>$ttl_member_fee));
	
		if($return)
		{
			return array('trans'=>$transid,'orders'=>$order_det_arr,'total_member_fee'=>$ttl_member_fee,'order_for'=>$order_for);
		}else
			$this->_output_handle('json',true,array('trans'=>$transid,'orders'=>$order_det_arr,'total_member_fee'=>$ttl_member_fee,'order_for'=>$order_for));
	
	}


	/**
	 * Function for get the franchise deatils by mobile number
	 * @author Shivaraj <shivaraj@storeking.in>
	 */
	function get_franchise_details()
	{
		$this->_is_validauthkey();
		
		$username=$this->input->post('username');// or i.e Username=Mobile number
		$franchise_id='';//$this->input->post('franchise_id');//optional
		$this->load->model('erpmodel','erpm');
		
		$franchise_details=array();
		
		//get the franchise basic details
		$franchise=$this->apim->do_get_franchise_details($franchise_id,$username);
		
		if($franchise)
		{
			//============< Franchise Basic Details >===========================
			$franchise_id = $franchise_details['franchise_id']=$franchise['franchise_id'];
			$franchise_details['franchise_name']=$franchise['franchise_name'];
			$franchise_details['contact_no']=$franchise['login_mobile1'];
			$franchise_details['contact_no_2']=$franchise['login_mobile2'];
			$franchise_details['territory']=$franchise['territory_name'];;
			$franchise_details['town_name']=$franchise['town_name'];
			$franchise_details['is_prepaid']=$franchise['is_prepaid'];
			$franchise_details['address']=$franchise['address'].', '.$franchise['locality'].', '.$franchise['city'].'-'.$franchise['postcode'].' '.$franchise['state'].'.';
			$franchise_details['credit_limit']=$franchise['credit_limit'];
			$franchise_details['contact_person']=$this->apim->get_contact_person($franchise['franchise_id']);
			
			//============< Franchise Account Details >===========================
			$franchise_details['payment_det']=array();
			$account_summary=$this->erpm->get_franchise_account_stat_byid($franchise_id);
			$credit_note_amt = $account_summary['credit_note_amt'];
			$shipped_tilldate = $account_summary['shipped_tilldate'];
			$paid_tilldate = $account_summary['paid_tilldate'];
			$acc_adjustments_val = $account_summary['acc_adjustments_val'];
			$franchise_details['payment_det']['pending']=$shipped_tilldate-($paid_tilldate+$acc_adjustments_val+$credit_note_amt);
			$franchise_details['payment_det']['uncleared']=$account_summary['uncleared_payment'];
			

			//============< Franchise Cart details >===========================
			$franchise_details['cart_total']=$this->apim->get_franchise_cart_info($franchise_id);
			
			// Output
			$this->_output_handle('json',true,array("franchise_details"=>$franchise_details));
			
		}else{
			$this->_output_handle('json',false,array('error_code'=>2003,'error_msg'=>"No data found"));
		}
		
	}
		
	/**
	 * Function to get invoice transit log of transid
	 * @author Shivaraj <shivaraj@storeking.in>
	 * @param type $transid mixed
	 */
	function view_transit_log()
	{
		$this->_is_validauthkey();
		$transid = $this->input->post('transid');
		$batch_det = $this->erpm->getbatchesstatusfortransid($transid);
		
		if(empty($batch_det))
		{
			$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>"No invoice or batch found") );
		}
		else
		{
			foreach($batch_det as $b)
			{
				$log=$this->apim->get_transit_status($b['invoice_no']);
				if($log != FALSE)
					$transit_log[$b['invoice_no']] = $log;
				else
					$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>"No shipment information found") );
						
			}
			$this->_output_handle('json',true,array('transit_log'=>$transit_log));
		}
	}
	
	/**
	 * function to get similar products of similar catagory
	 */
	function similar_products()
	{
		$_POST=$_GET;
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		$prod_id=$this->input->post('pnh_id');
		
		$prod_res=$this->db->query("select * from king_dealitems where pnh_id=?",$prod_id);
		
		if($prod_res->num_rows()==0)
		{
			$this->_output_handle('json',false,array('error_code'=>2018,'error_msg'=>"Invalid Product ID entered"));
		}
		else
		{	
			$deal_info=$this->apim->get_deal($prod_id);
			
			$similar_prods_det=$this->apim->get_similar_products($deal_info['itemid']);
			
			$brand_list=array();
			
			if($similar_prods_det['similar_products'])
			{
				foreach($similar_prods_det['similar_products'] as $s)
				{
					if(!isset($brand_list[$s['brandid']]))
						$brand_list[$s['brandid']]=array('brandid'=>$s['brandid'],"brandname"=>$s['brand_name']);
				}
			}
			
			if($similar_prods_det['error'])
				$this->_output_handle('json',false,array('error_code'=>2018,'error_msg'=>"No Deals found"));
			else
				$this->_output_handle('json',true,array('similar_products'=>$similar_prods_det['similar_products'],'category'=>$deal_info['category'],'brand_list'=>$brand_list));
		}
	}
	
	/**
	 * function to get popular products
	 */
	function popular_products()
	{
		 //$_POST=$_GET;
		
		 $this->_is_validauthkey();  
		
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		$menuid=$this->input->post('menu_id');
		$catid=$this->input->post('cat_id');
		$brandid=$this->input->post('brand_id');
		$start=$this->input->post('start');
		$limit=$this->input->post('limit');
		
	//	echo '<pre>';print_r($_POST);exit;
		$popular_prod_det=$this->apim->get_popular_products($fid,$menuid,$catid,$brandid,$start,$limit);
		
		if($popular_prod_det['popular_prod_res'])
		{
			$category_list=array();
			$brand_list=array();
			$menu_list=array();
			foreach($popular_prod_det['popular_prod_res'] as $p)
			{
				if(!isset($brand_list[$p['brandid']]))
					$brand_list[$p['brandid']]=array('brandid'=>$p['brandid'],"brandname"=>$p['brandname']);
				
				if(!isset($category_list[$p['catid']]))
					$category_list[$p['catid']]=array('catid'=>$p['catid'],"catname"=>$p['catname']);
				
				if(!isset($menu_list[$p['menuid']]))
					$menu_list[$p['menuid']]=array('menuid'=>$p['menuid'],"menuname"=>$p['menuname']);
			}
		}
		
		if(@$popular_prod_det['error'])
			$this->_output_handle('json',false,array('error_code'=>2018,'error_msg'=>"No Records found"));
		else
			$this->_output_handle('json',true,array('popular_products'=>$popular_prod_det['popular_prod_res'],'menu_list'=>$menu_list,'category_list'=>$category_list,'brand_list'=>$brand_list));
			
			
	}

	/**
	 * function to get pending payment of franchisee
	 */
   function fran_pending_payment()
   {
   		$_POST=$_GET;
   		
   //	$this->_is_validauthkey();
   		
   //	$userid=$this->input->post('user_id'); 
   		$fid=$this->input->post('franchise_id');
   		
   		if(!$fid)
   		{
   			$this->_output_handle('json',false,array('error_code'=>2018,'error_msg'=>"Please enter franchise id"));
   		}
   		else
   		{
   			$acc_statement=$this->erpm->get_franchise_payment_details($fid);
   			
   			$pending_payment=format_price($acc_statement['pending_payment']);
			
   			$this->_output_handle('json',true,array('Pending payment'=>$pending_payment));
   		
   		}
   }
	/**
	 * function to get uncleared payment of franchisee
	 */
   function fran_uncleared_payment()
   {
	   	$_POST=$_GET;
	   	 
	   	//	$this->_is_validauthkey();
	   	 
	   	//	$userid=$this->input->post('user_id');
	   	$fid=$this->input->post('franchise_id');
	   	 
	   	if(!$fid)
	   	{
	   		$this->_output_handle('json',false,array('error_code'=>2018,'error_msg'=>"Please enter franchise id"));
	   	}
	   	else
	   	{
	   		$sql="SELECT f.franchise_name,r.receipt_id,r.receipt_amount,r.receipt_type,r.payment_mode,r.bank_name,r.instrument_no,FROM_UNIXTIME(r.instrument_date) AS instrument_date,FROM_UNIXTIME(r.created_on) AS created_on
					 FROM pnh_t_receipt_info r
					JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id
					WHERE r.franchise_id = ? AND STATUS = 0 AND receipt_type = 1 AND in_transit=0
					ORDER BY created_on DESC";
	   		
	   		$uncleared_payment_det=$this->db->query($sql,$fid);
	   		
	   		$uncleared_payment_res=$uncleared_payment_det->result_array();
	   		
	   		$franchise_det=$uncleared_payment_det->row_array();
	   		
	   		$franchise_name=$franchise_det['franchise_name'];
	   		
	   		$acc_statement=$this->erpm->get_franchise_account_stat_byid($fid);
	   		
	   		$uncleared_payment_amt = format_price($acc_statement['uncleared_payment'],2);
	   		
	   	
	   		$this->_output_handle('json',true,array('franchise name'=>$franchise_name,'Uncleared payment'=>$uncleared_payment_amt,'uncleared payment details'=>$uncleared_payment_res));
	   	}
   }
   
   /**
    * function to get recent payments of franchisee
    */
   function fran_recent_payments()
   {
	   	$_POST=$_GET;
	   	
	   	//	$this->_is_validauthkey();
	   	
	   	//	$userid=$this->input->post('user_id');
	   	$fid=$this->input->post('franchise_id');
	   	$st_date=$this->input->post('start_date');
	   	$en_date=$this->input->post('end_date');
	   	
	   	if(!$fid)
	   	{
	   		$this->_output_handle('json',false,array('error_code'=>2018,'error_msg'=>"Please enter franchise id"));
	   	}
	   	else 
	   	{
	   		$recent_payment_res=$this->apim->get_recent_payments_byfran($fid,$st_date,$en_date);
	   		
	   		if(@$recent_payment_res['error'])
	   			$this->_output_handle('json',false,array('error_code'=>2018,'error_msg'=>"No recent payments"));
	   		else 
	   			$this->_output_handle('json',true,array('Recent payments'=>$recent_payment_res));
	   	}
   }
   
   /**
    * function to make payment
    */
   function make_payment()
   {
   		$_POST=$_GET;
   		
   	//	$this->_is_validauthkey();
   		 
   		$userid=$this->input->post('user_id');
   		
   		$fid=$this->input->post('franchise_id');
   		$payment_mode=$this->input->post('payment_type');
   		$instrument_no=$this->input->post('instrument_no');
   		$instrument_date=$this->input->post('instrument_date');
   		$receipt_amount=$this->input->post('receipt_amt');
   		$bank_name=$this->input->post('bank_name');
   		$remarks=$this->input->post('remarks');
   		$transit_type=$this->input->post('transit_type');
   		
   		$this->load->library('form_validation');
		$this->form_validation->set_rules('franchise_id','Franchise id','required');
		$this->form_validation->set_rules('receipt_amt','Amount','required');
		$this->form_validation->set_rules('bank_name','Bank','required');
		$this->form_validation->set_rules('payment_type','Payment type','required');
		$this->form_validation->set_rules('remarks','Remarks','required');
		$this->form_validation->set_rules('transit_type','Transit type','required');
		
		if($payment_mode==1 || $payment_mode==2)
		{
			$this->form_validation->set_rules('instrument_no','Instrument number','required');
			
			$this->form_validation->set_rules('instrument_date','Instrument Date','required');
		}
			
		if($this->form_validation->run() === FALSE)
		{
			$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>validation_errors()));
		}
		else
		{
			$inp_param=array();
			$inp_param['franchise_id']=$fid;
			$inp_param['payment_mode']=$payment_mode;
			$inp_param['receipt_amount']=$receipt_amount;
			$inp_param['instrument_no']=$instrument_no;
			$inp_param['instrument_date']=strtotime($instrument_date);
			$inp_param['bank_name']=$bank_name;
			$inp_param['remarks']=$remarks;
			$inp_param['created_by']=$userid;
			$inp_param['created_on']=time();
			$inp_param['in_transit']=$transit_type;
			$inp_param['receipt_type']=1;
			$this->db->insert("pnh_t_receipt_info",$inp_param);
			$id=$this->db->insert_id();
			if($id)
			{
				$receipt_det=$this->db->query("select * from pnh_t_receipt_info where receipt_id=?",$id);
				if($receipt_det->num_rows())
				{
					$receipt_res=$receipt_det->row_array();
					
					$this->_output_handle('json',true,array('Receipt Info'=>$receipt_res));
				}	
			}
			else
				$this->_output_handle('json',false,array('error_code'=>2018,'error_msg'=>"Error!!Receipt not added"));
		}
   }
   
   function order_det()
   {
	   $this->_is_validauthkey();
	   $orderid = $this->input->post('orderid');
	   
	   $order_det = $this->orders_model->get_order_det_by_orderid($orderid);
	   if(!$order_det)
	   {
		   $this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>"No orders found." ) );
	   }
	   
	   //====================
	   /*$is_prepaid=$this->erpm->is_prepaid_franchise($trans_fid);
	   if(!$is_prepaid){ 
				echo 'Paid for 1 qty : Rs '.$o['i_price']-$o['i_coup_discount'];
		}else{
				echo 'Paid for 1 qty : Rs '.$o['i_orgprice']-$o['i_coup_discount'];
		}*/
		//===================
		/*if($tran['order_for'] == 2)
			$pnh_member_fee=PNH_MEMBER_FEE;
		else
			$pnh_member_fee=0;*/
		#==============
		/*
		 <td>			$prods=array(); 
						if(!empty($o['order_product_id']))
						{
							$order_product_id = $o['order_product_id'];
							$order_product_msg = ' and p.product_id = '.$order_product_id;
						}
						else 
							$order_product_msg='';

						foreach($this->db->query("select l.qty,p.product_id,p.product_name 
														from m_product_deal_link l 
														join m_product_info p on p.product_id=l.product_id 
														where l.itemid=? ".$order_product_msg,$o['itemid'])->result_array() as $p)
						{ 
							$prods[]=$p['product_id'];
				
							<a href="<?=site_url("admin/product/{$p['product_id']}")?>" style="color:#000"><?=$p['product_name']?></a> <span style="font-size: 11px;font-weight: bold;color:#cd0000"> (<?php echo $p['qty'].'x'.$o['quantity']?>)</span> <br>
				
							if(!empty($o['order_product_id']))
							{
								echo '<span style="background:#f1f1f1;padding:3px;display:inline-block;width:auto">'.($this->db->query("select group_concat(concat('<b>',attr_name,'</b> : ',attr_value) order by attr_id desc SEPARATOR '<br>' ) as p_attr_det 
													from m_product_info a 
													join m_product_deal_link b on a.product_id = b.product_id  
													join m_product_attributes c on c.pid = b.product_id 
													join m_attributes d on d.id = c.attr_id 
													where b.itemid = ?  and a.product_id = ? 
													group by a.product_id ",array($o['itemid'],$o['order_product_id']))->row()->p_attr_det).'</span>';
							}
						}

						foreach($this->db->query("select d.qty,p.product_name,p.product_id 
														from products_group_orders o 
														join king_orders o1 on o1.id = o.order_id 
														join m_product_group_deal_link d on d.itemid = o1.itemid 
														join m_product_info p on p.product_id=o.product_id 
														where o.order_id=? ",$o['id'])->result_array() as $p)
						{ 
							$prods[]=$p['product_id']; 
				
							<a href="<?=site_url("admin/product/{$p['product_id']}")?>" style="color:#000"><?=$p['product_name']?></a> <span style="font-size: 11px;font-weight: bold;color:#cd0000"> (<?php echo $p['qty'].'x'.$o['quantity']?>)</span> <br>
				}
				</td>
		 */
   }
   
	/**
	 * Function to view orders of the login franchise-view between date range or recent 10 orders
	 * @author Shivaraj <shivaraj@storeking.in>
	 * @param type $transid mixed
	 */
	function view_franchise_orders()
	{
		$this->_is_validauthkey();
		$username=$this->input->post('username'); // or i.e Username=Mobile number
		$from=$this->input->post('from'); //option or specify both fields
		$to=$this->input->post('to'); //option or specify both fields
		$fid=$this->input->post('franchise_id');
		$status=$this->input->post('status');// Order status
				
		$this->load->model('reservation_model','reservations');//$this->load->model('erpmodel','erpm');
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('username','Username','required|callback__chk_username');
		
		if($this->form_validation->run() === FALSE)
		{
			$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>validation_errors()));
		}else
		{
				$cond = $limit_cond = '';
				$output = array();

				if($from=='' && $to =='')
				{
					// Show default 10 orders
					$cond .= '';
					$limit_cond .= ' LIMIT 10 ';
				}
				elseif($from!='' && $to!='')
				{
					// process orders by date range
					$cond .= ' AND actiontime BETWEEN UNIX_TIMESTAMP("'.$from.' 00:00:00") AND UNIX_TIMESTAMP("'.$to.' 23:59:59") ';
				}
				else
				{
					$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>"Invalid date range given." ) );
				}
				
				//Default orders
				$sql = "SELECT tr.transid,tr.actiontime,f.franchise_name,tr.trans_created_by FROM king_transactions tr 
								JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
								WHERE f.login_mobile1 = ? $cond
								ORDER BY tr.id DESC $limit_cond";
				$trans_det_rslt = $this->db->query($sql,$username);
				
				if($trans_det_rslt->num_rows() > 0)
				{
						$trans_det_res = $trans_det_rslt->result_array();
						foreach($trans_det_res as $trans_det )
						{
							$order_det=array();
							$transid = $trans_det['transid'];
							$output[$transid]['transid'] = $transid;
							$output[$transid]['actiontime'] = format_datetime_ts($trans_det['actiontime']);
							$output[$transid]['franchise_name'] = $trans_det['franchise_name'];
							$output[$transid]['created_by'] = $this->reservations->get_username_byid($trans_det['trans_created_by']);

							$trans_orders = $this->reservations->get_orders_of_trans($transid,'all');
							foreach($trans_orders as $j=>$order_i)
							{
									$order_det[$j]['itemid'] = $order_i['itemid'];
									$order_det[$j]['orderid'] = $order_i['id'];
									$order_det[$j]['pnh_id'] = $order_i['pnh_id'];
									$order_det[$j]['name'] = $order_i['name'];
									$order_det[$j]['quantity'] = $order_i['quantity'];
									$order_det[$j]['i_orgprice'] = $order_i['i_orgprice'];
									$order_det[$j]['amount']=round($order_i['i_orgprice']-($order_i['i_coup_discount']+$order_i['i_discount']),2);
									$order_det[$j]['o_status'] = ($order_i['status']?"yes":"no");
							}
							$output[$transid]['orders'] = $order_det;
						}
						$this->_output_handle('json',true,$output);
				}
				else
				{
					$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>"No orders found." ) );
				}
		}
	}
	
	/**
	* Function to get returns list
	*/
	function franchise_returns_list()
	{
	   
	   	 
	   	$this->_is_validauthkey();
	   	$userid=$this->input->post('user_id');
	   	$fid=$this->input->post('franchise_id');
   		$start_date=$this->input->post('start_date');
   		$end_date=$this->input->post('end_date');
   		$start=$this->input->post('start');
   		$limit=$this->input->post('limit');
   		
	   	$this->load->library('form_validation');

	   	$this->form_validation->set_rules('franchise_id','franchise id','required');
	   	
	   	if($this->form_validation->run() === FALSE)
	   	{
	   		$this->_output_handle('json',false,array('error_code'=>2033,'error_msg'=>strip_tags(validation_errors())));
	   	}
	   	else
	   	{
	   		$returns_list_res=$this->apim->get_returns_list($fid,$start_date,$end_date);
	   		
	   		if(@$returns_list_res['error'])
	   			$this->_output_handle('json',false,array('error_code'=>2018,'error_msg'=>"No Returns found"));
	   		else 
	   			$this->_output_handle('json',true,array('Returns list'=>$returns_list_res));
	   	}
   }
	 /**
    * function to get order details for the given invoice_no
    */
   function orders_details_byinvoiceno()
   {
   		$_POST=$_GET;
   		
	   	//	$this->_is_validauthkey();
	   	$userid=$this->input->post('user_id');
	   	
	   	$fid=$this->input->post('franchise_id');
		
	   	$invoice_no=$this->input->post('invoice_no');   	
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('franchise_id','franchise id','required');
		
		$this->form_validation->set_rules('invoice_no','Invoice no','required');
		
		if($this->form_validation->run() === FALSE)
		{
			$this->_output_handle('json',false,array('error_code'=>2033,'error_msg'=>strip_tags(validation_errors())));
		}
		else 
		{
			
			$valid_invno=$this->apim->_check_is_validinvno($fid,$invoice_no);
			if(!$valid_invno)
			{
				$this->_output_handle('json',false,array('error_code'=>2018,'error_msg'=>'Invalid invoice no '));
				return false;
			}
			
			$order_det_res=$this->erpm->get_invoicedet_forreturn($valid_invno);

			$return_cond=array();
			  $return_cond= $this->config->item('return_cond');
		
			if($order_det_res)
				$this->_output_handle('json',true,array('Items list'=>$order_det_res,'returns condition'=>$return_cond));
		}
   	}
   	
	function bank_list()
   	{
   		$_POST=$_GET;
   		 
   		$this->_is_validauthkey();
   		$userid=$this->input->post('user_id');
   		$bank_info=$this->apim->get_bank_list();
   		
   			if($bank_info)
   				$this->_output_handle('json',true,array('Bank list'=>$bank_info));
   			else
   				$this->_output_handle('json',false,array('error_code'=>2018,'error_msg'=>'No Data found'));
   	}
   	
	/**
	 * Funtion to store the franchise search keywords to db
	 * @author Shivaraj <shivaraj@storeking.in>
	 */
	/*function post_request()
	{
		// franchise request of a product
		$this->_is_validauthkey();

			
		$username = $this->input->post('username');// or i.e Username=Mobile number
		$type = $this->input->post('type'); // request from web,mobile,
		$title = $this->input->post('title'); // request from web,mobile,
		$desc = $this->input->post('desc');  // request desc
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('username','Username','required');//|callback__chk_username
		$this->form_validation->set_rules('type','Request Type','required');
		$this->form_validation->set_rules('title','Title','required|callback__chk_title_size');
		$this->form_validation->set_rules('desc','Description','required');
		
		if($this->form_validation->run() === FALSE)
		{
			$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>validation_errors()));
		}else
		{
			
			if($type == 'product')
			{
				
			}
			elseif($type == 'payment')
			{
				
			}
			elseif($type == 'service')
			{
				
			}
		}
		
	}
	
	function insert_request($username,$type)
	{
			// get franchise det by mobile number
			$fran_det_res = $this->db->query("SELECT franchise_id FROM pnh_m_franchise_info where login_mobile1 = ?",array($username));
			if($fran_det_res->num_rows())
			{
				$fran_det = $fran_det_res->row_array();
				$franchise_id = $fran_det['franchise_id'];
				$time = date("Y-m-d H:i:s",time());
				$status = 1;
				
				// insert
				$in_array = array("franchise_id"=>$franchise_id,"from" => '','type'=>$type,"desc"=>$desc,"status"=>$status,"created_on"=>$time);
				$this->db->insert("pnh_franchise_requests",$in_array);
				if($this->db->insert_id())
				{
					$this->_output_handle('json',true,"Request submitted");
				}
				else {
					$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>"Request not submitted.") );
				}
			}
			else
			{
				$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>"Franchise information not found." ) );
			}	
	}
	
	function _chk_userlogin()
	{
		//$userdet = $this->apim->get_userdet($name,'name');
	
		//if(!$userdet)
			//{
		$this->form_validation->set_message('_chk_username','Invalid Username or mobile no entered');
		return false;
		//}
		//return true;
	}*/
	
	/**
	 * Function to register a franchise complaints about our service,product,payment or insurance and return ticket number
	 * @author Shivaraj <shivaraj@storeking.in>
	 */
	function post_complaint()
	{
		//	franchise give complaint on our service,product,payment or insurance
		//	return ticketid
		$this->_is_validauthkey();
		$username = $this->input->post('username'); // or i.e Username=Mobile number
		$complaint_from = $this->input->post('complaint_from'); // request from web,mobile,
		$complaint_desc = $this->input->post('complaint_desc');  // request desc
		
		// get franchise det by mobile number
		$fran_det_res = $this->db->query("SELECT franchise_id FROM pnh_m_franchise_info where login_mobile1 = ?",array($username));
		if($fran_det_res->num_rows())
		{
			$fran_det = $fran_det_res->row_array();
			$franchise_id = $fran_det['franchise_id'];
			$token_id = "TKN".time();
			
			$time = date("Y-m-d H:i:s",time());
			$status = 1;
			$in_array = array("token_id"=>$token_id,"franchise_id"=>$franchise_id,"complaint_from" => $complaint_from,"complaint_desc"=>$complaint_desc,"priority"=>'high',"status"=>$status,"created_on"=>$time);
			$this->db->insert("pnh_franchise_complaints",$in_array);
			if($this->db->insert_id())
			{
				$this->_output_handle('json',true,array("token_id"=>$token_id,"msg"=>"Complaint registered"));
			}
			else {
				$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>"Request not submitted.") );
			}
		}
		else
		{
			$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>"Franchise information not found." ) );
		}
	}
	
	function global_search()
	{
		
		$_POST=$_GET;
		/* $this->_is_validauthkey();
		
		$userid=$this->input->post('user_id'); */
		$output=array();
		$fid=$this->input->post('franchise_id');
		$search_tag=$this->input->post('search_data');
		
		$search_tag=trim($search_tag);
		$search_tag=str_ireplace('  ', '', $search_tag); //replace double space with single space between a string
		
		$kwds=explode(' ',$search_tag);
		
		//word building
		$kwds_arr = array();
		for($i=0;$i<count($kwds);$i++)
		{
			for($j=0;$j<count($kwds);$j++)
			{
				$tmp = array($kwds[$i]);
				array_push($tmp,$kwds[$j]);
				$kwds_arr[] = implode(' ',array_unique($tmp));
				
				// plural fix
				$kwds_arr[] = implode(' ',array_unique($tmp)).'s';
			}
		}
		
		// prepare kwds for query 
		$kwds_str = '"'.implode('","',$kwds_arr).'"';
		
		$new_kwds_str = $kwds_str; 
		 
		$cond = '';
		
		$brand_list = array();
		// check kwd for valid brand 
		$brand_sugg_res=$this->db->query("select id,name from king_brands where name in (".$kwds_str.") ");
		if($brand_sugg_res->num_rows())
			foreach($brand_sugg_res->result_array() as $brand_det)
			{
				$brand_list[$brand_det['id']] = $brand_det['name'];
				$new_kwds_str = str_ireplace($brand_det['name'],'',$new_kwds_str);
			}
		
		$cat_list = array();
		// check kwd for valid category
		$cat_sugg_res=$this->db->query("select id,name from king_categories where name in (".$kwds_str.") ");
		if($cat_sugg_res->num_rows())
			foreach($cat_sugg_res->result_array() as $cat_det)
			{
				$cat_list[$cat_det['id']] = $cat_det['name'];
				$new_kwds_str = str_ireplace($cat_det['name'],'',$new_kwds_str);
			}
			
			
		$new_kwds_str = str_replace('"',"",$new_kwds_str);
		$new_kwds_str = str_replace('  '," ",$new_kwds_str);
		
		
		$cond_arr = array();
		
		$brand_ids = implode(',',array_keys($brand_list));
		$cat_ids = implode(',',array_keys($cat_list));
		$possible_kwds = '"'.implode('","',array_filter(explode(',',$new_kwds_str))).'"';
		
		$str_cond='';
		
		if($brand_ids)
			$sql_cond = " and d.brandid in (".$brand_ids.")  ";
		
		if($cat_ids)
			$sql_cond .= " and d.catid in (".$cat_ids.")  ";
				
//		if($possible_kwds)
	//		$sql_cond .= " or keywords in (".$possible_kwds.")  ";
		
		$possible_srch_kwd=explode(",",$possible_kwds);
		
		$posible_srch_kwd_count=sizeof($possible_srch_kwd);
		
		foreach($possible_srch_kwd as $i=>$srch_kwd)
		{
			
			if($srch_kwd)
			{
				$str.="if(instr(i.name,$srch_kwd),1,0)";
				$str.="+";
			}
			$sql="select i.name as dealname,i.price as offerprice,i.orgprice as mrp,$str 0 as relavance,d.brandid,b.name as brand_name,d.catid,c.name as cat_name,
					i.is_combo, concat('".IMAGES_URL."items/',d.pic,'.jpg') as image_url,concat('".IMAGES_URL."items/small/',d.pic,'.jpg') as small_image_url,i.shipsin as ships_in
					from king_deals d
					join king_dealitems i on i.dealid=d.dealid 
					join king_brands b on b.id=d.brandid
					join king_categories c on c.id=d.catid
					$sql_cond 
					order by relavance desc limit 50  ";

		}
		
		$search_results=$this->db->query($sql);
		$brands=array();
		$catgorys=array();
		if($search_results)
		{
			foreach($search_results->result_array() as $s)
			{
				$brands[$s['brandid']]=$s['brand_name'];
				$catgorys[$s['catid']]=$s['cat_name'];
			}
		}
				
		$this->_output_handle('json',true,array('tmp'=>$new_kwds_str,'brand_list'=>$brands,'cat_list'=>$catgorys,'search_results'=>$search_results->result_array()));
		
		
	}
	
}
