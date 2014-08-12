<?php
//error_reporting(E_ALL);
//ini_set('display_errors',1);
//ini_set('memory_limit','128M');

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
		$this->load->model("employee_model","employee");
		
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
				
			if($franchise_id)
			{
				$fran_det = $this->franchise_model->get_franchise_details($franchise_id);
				$resp_det = array('authkey'=>$authkey,'user_id'=>$userdet['user_id'],'emp_id'=>$empid,'franchise_name'=>$fran_det['franchise_name'],'franchise_mobno'=>$fran_det['login_mobile1'],'franchise_id'=>$franchise_id); 
			}else
			{
				$resp_det = array('authkey'=>$authkey,'user_id'=>$userdet['user_id'],'emp_id'=>$empid,'franchise_id'=>$franchise_id);
			}
			
			$this->_output_handle('json',true,$resp_det);

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

		if(isset($_POST['authkey']))
		{
		if(!$this->apim->get_authkey('XXXXX',$authkey))
			$this->_output_handle('json',false,array('error_code'=>2001,'error_msg'=>"Invalid Authkey"));
		}
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
	 * @last modified by Roopashree <roopashree@storeking.in>
	 */
	function get_franchise_menus()
	{
		$this->_is_validauthkey();
	
		$userid=$this->input->post('user_id');
		$franchise_id=$this->input->post('franchise_id');
	
		if(!$userid)
			$this->_output_handle('json',false,array('error_code'=>2001,'error_msg'=>"Invalid user"));
	
		$grp_menu_list=array();
	
		$menu=$this->apim->get_groups();
	
		if($menu)
		{
			foreach($menu as $gm)
				array_push($grp_menu_list,$gm);
	
			$this->_output_handle('json',true,array("menu_list"=>$grp_menu_list));
		}else{
			$this->_output_handle('json',false,array('error_code'=>2003,'error_msg'=>"No data found pls contact admin"));
		}
	
	}
	
	
	
	/**
	 * function to get all menus of the group menu
	 * @last_modified Roopashree <roopashree@storeking.in>
	 */
	function menus_cat_bygroupmenu()
	{
		$this->_is_validauthkey();
	
		$userid=$this->input->post('user_id');
		$grp_menuid=$this->input->post('group_menuid');
		$limit=$this->input->post('limit');
		$start=$this->input->post('start');
		
		if(!$userid)
			$this->_output_handle('json',false,array('error_code'=>2001,'error_msg'=>"Invalid user"));
		
		$menu_list=array();
		
		$menu_list_arr=$this->apim->get_menucat_bygroupmenuid($grp_menuid,$start,$limit);
		
		if($menu_list_arr)
		{
			foreach($menu_list_arr as $m)
			{
				array_push($menu_list,$m);
			}
			
	//		print_r($menu_list);exit;
			$this->_output_handle('json',true,array("menu_list"=>$menu_list,"menu_cat_list"=>$menu_list_arr));
		}
		else
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
			
			$brands_list_det=$this->apim->get_brands_by_menu_cat($menuid,$catid,$start,$limit,$full,$alpha_sort,$top_brands); 
		}
		
		if(!$menu_cat)
		{
			if($menuid)
			{
				$full_brands=0;
				
				$brands_list_det=$this->apim->get_brands_by_menu($menuid,$start,$limit,$alpha_sort,$top_brands);
				
			}
			
			if($catid)
			{
				$full_brands=0;
				
				$brands_list_det=$this->apim->get_brands_by_cat($catid,$start,$limit,$alpha_sort,$top_brands);
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
	 * @last_modified by Roopashree <roopashree@storeking.in>
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
			$category_list_det=$this->apim->get_categories_by_brand_menu($brandid,$menuid,$start,$limit,$full,$alpha_sort,$top_cats);
		}
		
		
		if(!$menu_brand)
		{
			if($menuid)
			{
				$full_cat=0;
				
				$category_list_det=$this->apim->get_categories_by_menu($menuid,$start,$limit,$alpha_sort,$full,$top_cats);
				//$category_list_det=$this->apim->get_group_cats($menuid,$start,$limit,$alpha_sort,$full,$top_cats);
			}
			
			
			if($brandid)
			{
				$full_cat=0;
				
				$category_list_det=$this->apim->get_categories_by_brand($brandid,$start,$limit,$alpha_sort,$full,$top_cats);
			}
		}
		
		if($full_cat)
		{
			$category_list_det=$this->apim->get_categories($start,$limit,$alpha_sort,$full);
		}
		
		
		//format the category list by parent cat and child cat
		$category_cofig=array();
		if($category_list_det['cat_list'])
		{
			$this->_output_handle('json',true,array("category_list"=>$category_list_det['cat_list']));
		
		}else{
			$this->_output_handle('json',true,array("category_list"=>''));
		}
	}
	
	/**
	 * function for get the deal list
	 * @last_modified roopashree@storeking.in
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
		$fid=$this->input->post("franchise_id");
		$publish=$this->input->post('publish_status');
		$sort_by=$this->input->post('sort_by');
		$type=$this->input->post('type');
		
		if(!$type)
			$type='deals';
		
		if(!$start)
			$start=0;
		
		if(!$limit)
			$limit=10;
		
		
		$deal_det=$this->apim->get_deals($brandid,$catid,$menuid,$start,$limit,$publish,$pids,$srch_data=array(),$gender,$min_price,$max_price,$fid,$sort_by,$type);
		//print_r($deal_det);exit;
		if(!$deal_det)
			$this->_output_handle('json',true,array('deal_list'=>'','price_list'=>'','brand_list'=>'','cat_list'=>'','total_deals'=>''));
		
		if($deal_det['deals_list'])
			$this->_output_handle('json',true,array('deal_list'=>$deal_det['deals_list'],'total_deals'=>$deal_det['ttl_deals']));
		else if($type == 'total')
			$this->_output_handle('json',true,array('deal_list'=>'','total_deals'=>$deal_det['ttl_deals']));
		else if($type == 'refine')
			$this->_output_handle('json',true,array('deal_list'=>'','price_list'=>$deal_det['price_list'],'brand_list'=>$deal_det['brand_list'],'cat_list'=>$deal_det['cat_list']));
		else 
			$this->_output_handle('json',true,array('deal_list'=>$deal_det['deals_list'],'price_list'=>'','brand_list'=>'','cat_list'=>'','total_deals'=>$deal_det['ttl_deals']));
		
	}
	
	/**
	 * function to get the deal details
	 */
	function get_deal_info()
	{
		//$this->_is_validauthkey();
		
		$pid=$this->input->post('pid');
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		
		$deal_info=$this->apim->get_deal($pid,$fid);
		$status='';
		if($deal_info['availability']==1)
		 	$status='In Stock';
		else 
			$status='Sold Out';
			
		if($deal_info)
			$this->_output_handle('json',true,array("deal_info"=>$deal_info,"stock_status"=>$status));
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
		
	 
		  
		$cart_items_det=$this->apim->get_cart_items($fid,$pid);
		 
		$cart_items_det=$this->check_cart_items_margin($fid,$cart_items_det);
		 
		if($cart_items_det)
		{
			foreach($cart_items_det as $i=>$c)
			{
				$cart_items_det[$i]['new_mem']=$new_mem;
				$cart_items_det[$i]['mem_type']=$member_type;
				
				if($new_mem == 1)
					$cart_items_det[$i]['mem_fee']=PNH_MEMBER_FEE;
				else 
					$cart_items_det[$i]['mem_fee']=0;
					
				$cart_items_det[$i]['mem_type_config']=array("1"=>'Old member',"2"=>"New member","3"=>"key member");
				$deal_det=$this->get_cart_deal_configs($c['deal_deails'],$fid,$mem_id);
				
				//validate max order qty
				if($deal_det['max_allowed_qty'])
				{
					if($c['qty'] > $deal_det['max_ord_qty'])
					{
						$cart_items_det[$i]['place_order']=0;
						$cart_items_det[$i]['cart_msg']=''.$deal_det['name'].' Currently '.$deal_det['max_ord_qty'].' Stock Available. Please Change Order Quantity';
					}
				}
				
				//validate product is sourceable or not sourceable
				//if(!count($deal_det['allow_order']) && $deal_det['is_sourceable']==0)
				//{
				//	$cart_items_det[$i]['place_order']=0;
				//	$cart_items_det[$i]['cart_msg']='Product Id '.$deal_det['pid'].' is disabled';
				//}
				
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
		
		$price_type=$this->erpm->get_fran_pricetype($fid);
		
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
							//$prod['base_margin']=$prod['base_margin']-0.5;
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
				$prod['max_ord_qty'] = $this->erpm->get_maxordqty_pid($fid,$pid,$price_type);
	
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
			
			$acc_statement = $this->erpm->get_franchise_account_stat_byid($fid);	
			$net_payable_amt = $acc_statement['net_payable_amt'];
			$credit_note_amt = $acc_statement['credit_note_amt'];
			$shipped_tilldate = $acc_statement['shipped_tilldate'];
			$paid_tilldate = $acc_statement['paid_tilldate'];
			$uncleared_payment = $acc_statement['uncleared_payment'];		
			$cancelled_tilldate = $acc_statement['cancelled_tilldate'];
			$ordered_tilldate = $acc_statement['ordered_tilldate'];
			$not_shipped_amount = $acc_statement['not_shipped_amount'];
			$acc_adjustments_val = $acc_statement['acc_adjustments_val'];
			
			
			$pending_payment = $shipped_tilldate-($paid_tilldate+$acc_adjustments_val+$credit_note_amt);
	
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
					$cart_item_list[$k]['credit_msg']='<div class="type_title">Credit Available : </div> <div class="type_value alert-danger">Rs '.formatInIndianStyle($current_balance).'</div>';
					$cart_item_list[$k]['pending_msg']='<div class="type_title">Payment Pending : </div> <div class="type_value alert-danger">Rs '.formatInIndianStyle($pending_payment).'</div>';
				}
				else
				{
					$cart_item_list[$k]['place_order']=1;
					$cart_item_list[$k]['credit_msg']='<div class="type_title">Credit Available : </div> <div class="type_value alert-proceed">Rs '.formatInIndianStyle($current_balance).'</div>';
					$cart_item_list[$k]['pending_msg']='<div class="type_title">Payment Pending : </div> <div class="type_value alert-proceed">Rs '.formatInIndianStyle($pending_payment).'</div>';
				}
			}
			return 	$cart_item_list;
		}
	}
	
	/**
	 * function for validate the the cart items by franchise and member
	 */
	function validate_cart_items($fid=0,$validatation_type=array(),$mem_id=0,$vcodes=0,$rtn=0,$member_type='')
	{
		$this->_is_validauthkey();
		$error=0;
		$val_menus=array('112');
		$val_qtyby_menu=array('112'=>'1');
		
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
					//new
					$cart_items_det=$this->apim->get_cart_items($fid);
					$cart_items_det=$this->check_cart_items_margin($fid,$cart_items_det);
					
					if($cart_items_det)
					{
						foreach($cart_items_det as $i=>$c)
						{
							$deal_det=$this->get_cart_deal_configs($c['deal_deails'],$fid);
						 
							//validate max order qty
							if($deal_det['max_allowed_qty'])
							{
								if($c['qty'] > $deal_det['max_ord_qty'])
								{
									$cart_items_det[$i]['place_order']=0;
									$cart_items_det[$i]['cart_msg']=''.$deal_det['name'].' Currently '.$deal_det['max_ord_qty'].' Stock Available. Please Change Order Quantity';
								}
							}
					
							////validate product is sourceable or not sourceable
							//if(!count($deal_det['allow_order']) && $deal_det['is_sourceable']==0)
							//{
							//	$cart_items_det[$i]['place_order']=0;
							//	$cart_items_det[$i]['cart_msg']='Product Id '.$deal_det['pid'].' is disabled';
							//}
							
							if($mem_id==0)
							{
								if($member_type==3 && $deal_det['menu_id']!="112")
								{
									$cart_items_det[$i]['place_order']=0;
									$cart_items_det[$i]['cart_msg']='Member Registration is required because Other than Electronic items are there in the Cart';
								}
							}
							
							if(!$cart_items_det[$i]['place_order'])
							{
								$this->_output_handle('json',false,array('error_code'=>2010,'error_msg'=>$cart_items_det[$i]['cart_msg']));
							$error=1;
							break;
						}
					}
				}
					//new end
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
			$this->_output_handle('json',true,array("member_details"=>array('member_name'=>$member_name,'member_id'=>$member_id,'mobile_no'=>$mobile_no)));
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
		
		if($entry_type==2)
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
		$member_type=$this->input->post("member_type");
		
		if($is_voucher_order)
			$validatation_type[]=2;
			
		$v=$this->validate_cart_items($fid,$validatation_type,$member_id,$vocher_codes,1,$member_type);
		
		if(!$v)
		{
			$this->form_validation->set_message('_check_cart_items','Please check your cart items');
			return false;
		}
		
		return true;
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
	
	function search($type='deals')
	{	
		//$this->_is_validauthkey();
		$this->benchmark->mark('code_start');
		
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
		$srt_by_discount=$this->input->post("discount");
		
		$attrs=$this->input->post("attrs");
		
		$pids=$this->input->post('prd_id');
		$sortby=$this->input->post("sortby");
		if($srt_by_discount)
			$sortby = 'discount';

		$publish = 1;
		
		$fid = $fid*1;
		
		if(!$start)
			$start=0;
		
		if(!$limit)
			$limit=10;
		
		if(!$brand_id)
			$brand_id=0;
		if(!$cat_id)
			$cat_id=0;
		
		$s_inp = array();
		$s_inp[] = $fid;
		$s_inp[] = json_encode($search_tag);
		$s_inp[] = json_encode($_POST);
		$this->db->query('insert into pnh_m_franchise_search_log (fid,srch_kwd,logged_on,req_data) values (?,?,now(),?) ',$s_inp);
		$srch_log_id = $this->db->insert_id(); 
		
		/*
		if($search_tag=='' || strlen($search_tag)==0)
			$this->_output_handle('json',false,array('error_code'=>2016,'error_msg'=>"Please enter search keyword"));
		
		
		if(strlen($search_tag) < 3)
			$this->_output_handle('json',false,array('error_code'=>2015,'error_msg'=>"Please enter search keyword, min 3 chars required"));
		*/
		
		if($search_tag == 'show_offers')
			$search_tag = 'offers';
		
		$srch_data=array("tag"=>$search_tag,'menuid'=>0);
		
		$menuid=array();
		
		if($fid)
		{
			$menu_by_franchise=$this->apim->get_menus_by_franchise($fid);
			
			if($menu_by_franchise)
			{
				foreach($menu_by_franchise as $m)
					array_push($menuid,$m['id']);

				$srch_data['menuid']=implode(',',$menuid);
			}
		}
		$menu_id=0;
		$srch_data['attrs'] = $attrs;
			
		$deal_det=$this->apim->get_deals($brand_id,$cat_id,$menu_id,$start,$limit,$publish,$pids,$srch_data,$gender,$min_price,$max_price,$fid,$sortby,$type);		

		if( isset($deal_det['error']) ) {
			$this->benchmark->mark('code_end');
			$etime = $this->benchmark->elapsed_time('code_start', 'code_end');
			$memory_usage = $this->benchmark->memory_usage();
			$this->_output_handle('json',true,array('total_deals'=>0,'message'=>$deal_det['error'],"elapsed_time"=>$etime,"memory_usage"=>$memory_usage) );
		}
		else 
		{

			if($type == 'deals' || $type == 'refine' )
			{
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

				$deal_resp = array("deal_list"=>$deal_det['deals_list'],'total_deals'=>$deal_det['ttl_deals'],'brand_list'=>$brand_list,'category_list'=>$category_list);

				$deal_resp['cat_list'] = array_values($deal_det['cat_list']);
				$deal_resp['brand_list'] = array_values($deal_det['brand_list']);
				$deal_resp['price_list'] = $deal_det['price_list'];
				$deal_resp['attr_list'] = $deal_det['attr_list'];
				$deal_resp['gender_list'] = $deal_det['gender_list'];
				$this->benchmark->mark('code_end');
			
				$etime = $this->benchmark->elapsed_time('code_start', 'code_end');


				$times = $this->db->query_times;
				$dbs    = array();
				$d_output = NULL;
				$queries = $this->db->queries;
				
				if (count($queries) == 0){
					$d_output .= "no queries\n";
				}else{
					foreach ($queries as $key=>$query){
						$d_output .= $query . "\n";
					}
					$took = round(doubleval($times[$key]), 3);
					$d_output .= "===[took:{$took}]\n\n";
				}
				
				$e_memory_usage = $this->benchmark->memory_usage();
				
				$this->db->query('update pnh_m_franchise_search_log set loaded_queries=?,memory_usage=?,elapsed_time = ?,total_results=?,responded_on = now() where id = ? ',array($d_output,$e_memory_usage,$etime,$deal_det['ttl_deals'],$srch_log_id)) or die(mysql_error());

				$this->_output_handle('json',true,$deal_resp);

			}else if($type == 'total')
			{
				$this->benchmark->mark('code_end');
				$etime = $this->benchmark->elapsed_time('code_start', 'code_end');

				$this->db->query('update pnh_m_franchise_search_log set elapsed_time = ?,total_results=?,responded_on = now() where id = ? ',array($etime,$deal_det['ttl_deals'],$srch_log_id)) or die(mysql_error());

				$this->_output_handle('json',true,array('total_deals'=>$deal_det['ttl_deals']));
			}
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
	 * function for get the franchise returnd details/returns list
	 * @author Roopa <roopashree@storeking.in>
	 */
	function get_returns()
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
		
		}else
		{
		
			if(!$start)
				$start=0;
			if(!$limit)
				$limit=20;
			
			$return_details=$this->apim->get_pnh_invreturns($fid,$start,$limit);
			
			$this->_output_handle('json',true,array("returns_list"=>$return_details['return_details'],"ttl_returns"=>$return_details['ttl_returns']));
		}
	}
	
	/**
	 * function to get return details for the input return id
	 * @return boolean
	 * @author Roopa<roopashree@storeking.in>
	 */
	function return_details()
	{
		//$_POST=$_GET;
		$this->_is_validauthkey();
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		$returnid=$this->input->post('return_id');
		
		$return_info=$this->apim->get_inv_returns_details($fid,$returnid);
		if($return_info)
			$this->_output_handle('json',true,array("return_details"=>$return_info));
		else 
			return false;
			
	}
	
	/**
	 * function for check the price updates
	 */
	function check_updates($update_type='live')
	{
		$this->_is_validauthkey();
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		$version=$this->input->post('version');
		
		$menuid=$this->input->post('menuid');
		$catids=$this->input->post('catids');
		$brandids=$this->input->post('brandids');
		
		$update_flag=$this->input->post('update_flag');   //update flag:price_update=1,dealpublish_update=2,newproduct_update=3
		$request_type = $this->input->post('request_type');
		
		if($request_type == '')
			$request_type = 'deals';
		
		if(!$menuid)
			$menuid=0;
		if(!$catids)
			$catids=0;
		else
			$catids = implode(',',$catids);
		if(!$brandids)
			$brandids=0;
		else
			$brandids = implode(',',$brandids);
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('version','Version','required');
		if($this->form_validation->run() === FALSE)
		{
			$this->_output_handle('json',false,array('error_code'=>2045,'error_msg'=>strip_tags(validation_errors())));
		
		}else{
			
			$update_version_det=$this->apim->get_new_update_versions($version);
		
			if($update_version_det['error'])
				$this->_output_handle('json',false,array('error_code'=>2046,'error_msg'=>"Given version details not found"));
			
			if($request_type=='total')
				$ttl_available_update=$this->apim->get_ttlupdate_catmenu($update_version_det['verions'],$update_flag);
			else 
				$ttl_available_update=$this->apim->get_updated_deals($update_version_det['verions'],$menuid,$catids,$brandids,$update_flag,$fid);
			
			$this->_output_handle('json',true,array("total_updates"=>$ttl_available_update,'site_img_path'=>'http://static.snapittoday.com/items',"latest_version"=>$update_version_det['verions'][count($update_version_det['verions'])-1]['version'],'timestamp'=>time()));

			//	$this->_output_handle('json',true,array('site_img_path'=>'http://static.snapittoday.com/items',"latest_version"=>$update_version_det['verions'][count($update_version_det['verions'])-1]['version']));
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
	 * function for sent a sms for give type
	 * @param unknown_type $msg_type
	 * @param unknown_type $msg_det
	 */
	function send_sms($msg_type,$msg_det=array())
	{
		if(!is_array($msg_det))
		{
			return false;
		}
		
		$fran_msg='';
		$mem_msg='';
		$fran_mob=0;
		$mem_mob=0;
		$fid=0;
		$empid='';
		$type='';
		switch($msg_type)
		{
			case 1://new member registration msg
			{
				if(isset($msg_det['name']) &&  isset($msg_det['fid']) && isset($msg_det['mobileno']) && isset($msg_det['member_id']) )
				{	$mem_name=$msg_det['name'];
					$mem_mob=$msg_det['mobileno'];
					$fid=$msg_det['fid'];
					$empid=$membr_id=$msg_det['member_id'];
					$fran_det = $this->erpm->fran_det_byid($fid);
					$fran_mob=$fran_det['login_mobile1'];
					$type='MEM_REG';
					
					$fran_msg= "Hello ".$fran_det['franchise_name'].", Congrats!! [$mem_name $mem_mob] has been Registered Successfully and has been assigned Member ID: $membr_id. Please make sure Registration fee of Rs ".PNH_MEMBER_FEE."/- has been collected.";
					$mem_msg= "Hi $mem_name, Welcome to StoreKing - Hurry up!! Get Free Insurance on the 1st Electronic* product you buy. Your Member ID is $membr_id.Please deposit Rs".PNH_MEMBER_FEE."/- Registration fee with Storeking Franchisee to avail this offer";
				}else{
					return false;
				}
			}
			default:{
				return false;
			}
		}
		
		if($fran_mob && $fran_msg)
			$this->erpm->pnh_sendsms($fran_mob,$fran_msg,$fid,0,$type);
		
		if($mem_mob && $mem_msg)
		$this->erpm->pnh_sendsms($mem_mob,$mem_msg,$fid,$empid,$type);
	}
	
	/**
	 * function to check deal updates
	 * @last_modify Shivaraj<Shivaraj@storeking.in>_Aug_09_2014
	 */
	function check_deal_update($fid=0,$pid=0,$item_id=0)
	{
		$this->_is_validauthkey();
		if($fid==0 && $pid==0 && $item_id==0)
		{
			$fid=$this->input->post('fid');
			$fid = $fid*1;
			$pid=$this->input->post('pid');
			$item_id=$this->input->post('item_id');
		}
		$price_type=0;
		if($fid)
			$price_type=$this->erpm->get_fran_pricetype($fid);
		
		$mp_det=array();
		
		$d_res = $this->db->query("select a.id as item_id,pnh_id as pid,orgprice,price,publish,live from king_dealitems a join king_deals b on a.dealid = b.dealid where (pnh_id = ? OR a.id = ?) and publish = 1  ",array($pid,$item_id));
//		echo $this->db->last_query(); //die()
		if(!$d_res->num_rows())
		{
			// check if deal is unlimited stock 
			$this->_output_handle('json',false,array('error_code'=>2046,'error_msg'=>"Sold Out"));
		}else
		{
			$deal_info = $d_res->row_array();
			
			$this->_get_vendor_realtime_stock($deal_info['item_id']);
			
			if(!$deal_info['live'])
				$this->_output_handle('json',false,array('error_code'=>2046,'error_msg'=>"Sold Out"));
			
			/*
			$avail=$this->erpm->do_stock_check(array($deal_info['item_id']));
			foreach(array($deal_info['item_id']) as $i=>$itemid)
				if(!in_array($itemid,$avail))
					$this->_output_handle('json',false,array('error_code'=>2046,'error_msg'=>"Sold Out"));
			*/		
			
			$res_d = $this->db->query("select free_frame,has_power,b.shipsin,mp_offer_note,b.id as item_id,b.pnh_id as pid,menuid as pmenu_id,e.name as pmenu,b.name as pname,catid as pcat_id,c.name as pcat,
											cp.id as parent_cat_id,cp.name as parent_cat,brandid as pbrand_id,d.name as pbrand,
											b.orgprice as pmrp,b.price as pprice,publish as is_sourceable,
											b.gender_attr,b.url,b.is_combo,a.description as pdesc,a.keywords as kwds,
											a.pic as pimg,menuid as pimg_path,b.shipsin as shipin,
											b.member_price,b.mp_frn_max_qty,b.mp_mem_max_qty
										from king_deals a
										join king_dealitems b on a.dealid = b.dealid
										join king_categories c on c.id = a.catid
										left join king_categories cp on cp.id = c.type
										join king_brands d on d.id = a.brandid
										join pnh_menu e on e.id = a.menuid
										where b.id = ? and b.pnh_id != 0 and publish = 1 and live = 1 
					",$deal_info['item_id']);
			//echo "=>".$res_d->num_rows();
			if($res_d->num_rows())
			{
				$row_d = $res_d->row_array();
			
				foreach($row_d as $k1=>$v1)
					$tmp[$k1] = htmlspecialchars($v1);
			
				$deal_info['pimages'] = @$this->db->query("select group_concat(distinct id) as images from king_resources where itemid = ? order by id desc",$row['item_id'])->row()->images;
				$deal_info['attributes'] = "";
				$deal_info['offer_note'] = "";
				$deal_info['pseller_id'] = "";
				$deal_info['lens_pkg_list'] = "";
				$deal_info['has_power'] = $tmp['has_power'];
				$deal_info['free_frame'] = $tmp['free_frame'];
				
				if($tmp['has_power'])
					$deal_info['lens_pkg_list'] = $this->db->query('select package_id,package_name,package_desc,package_price from m_lens_package where package_for = "Full-Rim" ')->result_array();
				
				$deal_info['shipsin'] = $tmp['shipsin'];
				
				$deal_info['max_order_qty'] = $this->erpm->get_maxordqty_pid($fid,$tmp['pid'],$price_type);
				
				// get deal attributes if available
			
				$sql_a = "select group_concat(concat(product_id,':',ifnull(stk,0))) as p_attr_stk,group_concat(a SEPARATOR '||') as attrs
				from (
									select itemid,h.product_id,a,member_price,mp_frn_max_qty,mp_mem_max_qty,ifnull(ven_stock_qty,if(is_sourceable,max_allowed_qty,ifnull(sum(available_qty),0))) as stk from (
				(
				select l.itemid,max_allowed_qty,p.product_id,concat(group_concat(concat(a.attribute_name,':',v.attribute_value)),',ProductID:',p.product_id) as a,di.member_price,di.mp_frn_max_qty,di.mp_mem_max_qty
				from m_product_group_deal_link l
				JOIN king_dealitems di ON di.id=l.itemid
				join products_group_pids p on p.group_id=l.group_id
				join products_group_attributes a on a.attribute_name_id=p.attribute_name_id
				join products_group_attribute_values v on v.attribute_value_id=p.attribute_value_id
				join m_product_info p1 on p1.product_id = p.product_id and p1.is_sourceable = 1
											where di.id=?
				group by p.product_id
				)
				union
				(
				select a.itemid,max_allowed_qty,a.product_id,concat(group_concat(concat(attr_name,':',attr_value) order by f.id desc ),',ProductID:',a.product_id) as a,b.member_price,b.mp_frn_max_qty,b.mp_mem_max_qty
				from m_product_deal_link a
				join king_dealitems b on a.itemid = b.id
				join king_deals c on c.dealid = b.dealid
				join m_product_info d on d.product_id = a.product_id
				join m_product_attributes e on e.pid = d.product_id
				join m_attributes f on f.id = e.attr_id
											where b.is_group = 1 and b.id = ? and a.is_active = 1 and d.is_sourceable = 1 and length(attr_value)   
				group by a.product_id
				)
									) as h
									join m_product_info p on p.product_id = h.product_id 
									left join m_vendor_product_link vl on vl.product_id = h.product_id
									left join t_stock_info pstk on pstk.product_id = h.product_id
									group by h.product_id 
									having stk > 0  
							) as g 
							having attrs is not null  
	
				";

				$attr_res = $this->db->query($sql_a,array($deal_info['item_id'],$deal_info['item_id']));
				if($attr_res->num_rows())
				{
					$deal_info['attributes'] = @$attr_res->row()->attrs;
					$deal_info['p_attr_stk'] = @$attr_res->row()->p_attr_stk;
				}
				
				$member_price_det = $this->erpm->get_memberprice($deal_info['item_id'],$fid);
				//print_r($member_price_det);

				if($member_price_det['status'] == 'success')
				{
					$deal_info['price_type']=$member_price_det['price_type'];
					if($member_price_det['price_type'])
					{
						if($member_price_det['mp_is_loyalty_point'] == 1) {
							$deal_info['price']=($member_price_det['price']);
						}
						else {
							$deal_info['price']=($member_price_det['member_price']);
						}
						$deal_info['mp_frn_max_qty']=$member_price_det['mp_frn_max_qty'];
						$deal_info['mp_mem_max_qty']=$member_price_det['mp_mem_max_qty'];
						$deal_info['max_order_qty']=$member_price_det['max_ord_qty'];
						$deal_info['offer_note']=$member_price_det['mp_offer_note'];
						$deal_info['logref_id']=$member_price_det['logref_id'];
					}else
					{
						$deal_info['price']=($member_price_det['offer_price']);
						$deal_info['max_order_qty']=$member_price_det['max_ord_qty'];
					}
				}else
				{
					$this->_output_handle('json',false,array('error_code'=>2046,'error_msg'=>$member_price_det['message']));
				}
			
			$deal_info['attributes'] = (is_null($deal_info['attributes'])?'':$deal_info['attributes']);
				
			if($deal_info['max_order_qty'] > 0)
			{
			$this->_output_handle('json',true,$deal_info);
			}else
			{
				$this->_output_handle('json',false,array('error_code'=>2046,'error_msg'=>"Sold Out"));
			}
			}else
			{
				
				$this->_output_handle('json',false,array('error_code'=>2046,'error_msg'=>"Sold Out"));
				
			}
			
			
			
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
		$price_type = $fdet['price_type'];
		
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
		$op['franchise_name'] = $fdet['franchise_name'];
		
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
			$op['is_new_mem'] = $this->member_model->is_new_member($member_id);
			
			$op['member_id'] = $member_det['pnh_member_id'];
			$op['member_mobno'] = $member_det['mobile'];
			$op['member_dob'] = $member_det['dob']?date('m/d/Y',strtotime($member_det['dob'])):'';
		}
		
		if($op['is_new_mem'])
			$op['member_fee'] = 50;
		else
			$op['member_fee'] = 0;
		
		// check for insurance deals 
		
		$pid_nos = implode(',',$pids);

		$itemdet_res = $this->db->query("select free_frame,b.id as itemid,lens_type,a.menuid,b.is_combo,b.pnh_id as pid,orgprice,price,publish,has_insurance,ifnull(insurance_value,0) as inv_val,ifnull(insurance_margin,0) as inv_marg,has_power 
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
				
				$member_price_det = $this->erpm->get_memberprice($itemdet['itemid'],$fid,$mem_id);
				//print_r($member_price_det);
				
					if($member_price_det['status'] == 'success')
					{
						$itemdet['price_type']=$member_price_det['price_type'];
						if($member_price_det['price_type'])
						{
							$itemdet['price']=($member_price_det['member_price']);
							$itemdet['mp_frn_max_qty']=$member_price_det['mp_frn_max_qty'];
							$itemdet['mp_mem_max_qty']=$member_price_det['mp_mem_max_qty'];
							$itemdet['max_order_qty']=$member_price_det['max_ord_qty'];
							$itemdet['logref_id']=$member_price_det['logref_id'];
						}else
						{
							$itemdet['price']=($member_price_det['offer_price']);
							$itemdet['max_order_qty']=$member_price_det['max_ord_qty'];
						}
					}else
					{
						$itemdet['max_order_qty']=0;
						$itemdet['error']=$member_price_det['message'];
					}
				
				$itemdet['lens_package_list'] = array();
				// check if deal is lens and has power status to capture prescriotion info.
				if($itemdet['has_power'])
				{
					$itemdet['lens_package_list'] = $this->db->query("select * from m_lens_package where package_for = 'Full-Rim' ",$itemdet['lens_type'])->result_array();
				}
				
				if($itemdet['is_combo'])
					$itemdet['has_insurance'] = 0;
				
				if($itemdet['has_insurance'])
				{
				if($itemdet['price'] < 5000 )
					$itemdet['has_insurance'] = 0;
				else if($itemdet['price'] > 5000 && $itemdet['price'] < 10000)
					{
						// compute new insurance amount 
						if($op['is_new_mem'])
							$itemdet['mem_insurance_amt'] = 0;
						else
							$itemdet['mem_insurance_amt'] = round($itemdet['price']*$itemdet['inv_marg']/100);

					} if($itemdet['price'] >= 10000 )
				{
					// compute new insurance amount 
					if($op['is_new_mem'])
						$itemdet['mem_insurance_amt'] = $itemdet['inv_val'];
					else
						$itemdet['mem_insurance_amt'] = round($itemdet['price']*$itemdet['inv_marg']/100);
				}
				}
				
				
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
		$has_prescription_pids = $this->input->post('has_prescription');
		$lens_item_package_pids = $this->input->post('lens_item_package');
		
		$prescription_det = $this->input->post('prescription_det');
		$lens_item_det = array();
		if($has_prescription_pids)
		{
			foreach($has_prescription_pids as $k=>$prc_pid)
			{
				$lens_item_det[$prc_pid] = $prescription_det[$k];
			}
		}
		
		if($lens_item_package_pids)
		{
			foreach($lens_item_package_pids as $pckg_pid)
			{
				list($pid,$package_id) = explode('_',$pckg_pid);
				
				if(!isset($lens_item_det[$pid]))
					$lens_item_det[$pid] = array();
				$lens_item_det[$pid]['package_id'] = $package_id;
			}
		}
		
		
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
		 
		
		 
		
		$this->to_process_order($fid,$mid,$pid,$qty,$offr_sel_type,$redeem,0,$insurance,$d_attr,$lens_item_det);
		
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
	 *api function for place a order 
	 */
	function place_order()
	{
		/*ini_set('memory_limit','256M');*/
		$this->_is_validauthkey();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('franchise_id','Franchise id','required|callback__check_cart_items');
		$entry_type=$this->input->post("mem_entry_type");
		if($entry_type==1)
			$this->form_validation->set_rules('member_id','Member Id','required|callback__check_order_member_det');
		$this->form_validation->set_rules('mem_entry_type','Member type','required');
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
			$insurance_details=$this->input->post("insurance_details");
			
			$key_member=0;
			
			$insurance_details['opted_insurance'] = isset($insurance_details['opted_insurance'])?$insurance_details['opted_insurance']:0;
			$insurance_details['insurance_deals'] = isset($insurance_details['insurance_deals'])?$insurance_details['insurance_deals']:'';
			$insurance_details['proof_type'] = isset($insurance_details['proof_type'])?$insurance_details['proof_type']:0;
			$insurance_details['proof_id'] = isset($insurance_details['proof_id'])?$insurance_details['proof_id']:0;
			$insurance_details['first_name'] = isset($insurance_details['first_name'])?$insurance_details['first_name']:'';
			$insurance_details['last_name'] = isset($insurance_details['last_name'])?$insurance_details['last_name']:'';
			$insurance_details['mob_no'] = isset($insurance_details['mob_no'])?$insurance_details['mob_no']:'';
			$insurance_details['address'] = isset($insurance_details['address'])?$insurance_details['address']:'';
			$insurance_details['city'] = isset($insurance_details['city'])?$insurance_details['city']:'';
			$insurance_details['pincode'] = isset($insurance_details['pincode'])?$insurance_details['pincode']:'';
			$insurance_details['proof_name'] = isset($insurance_details['proof_name'])?$insurance_details['proof_name']:'';
			$insurance_details['proof_address'] = isset($insurance_details['proof_address'])?$insurance_details['proof_address']:'';	
					
			if($entry_type==2)
			{
				$member_id=$this->apim->add_new_member($fid,$member_name,$mobile_no);
		
				if(!$member_id)
					$this->_output_handle('json',false,array('error_code'=>2008,'error_msg'=>"Mobile no already registered"));
				else{
					$this->send_sms(1,array("name"=>$member_name,"fid"=>$fid,"mobileno"=>$mobile_no,"member_id"=>$member_id));
				}
			}else if($entry_type==3)
			{
				$key_member=1;
			}
		
			if(!$this->apim->get_member_details($member_id ))
				$this->_output_handle('json',false,array('error_code'=>2007,'error_msg'=>"No member available for given member id"));
		
			$this->db->query("update pnh_api_franchise_cart_info set member_id=? where franchise_id=? and status=1",array($member_id,$fid));
		
			$fid=$fid;
			$pid=array();
			$qty=array();
			$attr=array();
			$attributes=array();
			$prd_attrs=array();
			$mid=$member_id;
			$redeem=0;
			$redeem_points=150;
			$mid_entrytype=0;
			$new_mem=0;
			$offr_sel_type=0;
			$h_insurance=0;
			$insurance_selected=0;
			$ttl_order_value=0;
			$min_ord=500;
			
			
			//check member is new member or old member
			$m_userid=$this->db->query("select user_id from pnh_member_info where pnh_member_id=?",$mid)->row()->user_id;
			
			if(!$m_userid)
				$this->_output_handle('json',false,array('error_code'=>2007,'error_msg'=>"No member available for given member id"));
			else
				$ttl_orders=$this->db->query("SELECT COUNT(DISTINCT(transid)) AS l FROM king_orders WHERE userid=? AND STATUS NOT IN (3)",$m_userid)->row()->l;
			
			if($ttl_orders==0)
				$new_mem=1;
		
			//get cart info
			$cart_details=$this->apim->get_cart_items($fid);
			
		
			if(!$cart_details)
				$this->_output_handle('json',true,array("order_msg"=>"No more items in your cart"));
			
			
			
			foreach($cart_details as $cart)
			{
				array_push($pid,$cart['pid']);
				array_push($qty,$cart['qty']);
				if($cart['attributes'])
					array_push($attributes,$cart['attributes']);
				//compute total order value
				$ttl_order_value+=($cart['deal_deails']['price']*$cart['qty'])*1;
				
				//set the insurance flag
				if($cart['deal_deails']['has_insurance'])
					$h_insurance=1;
				
				if($attributes)
				{
					foreach($attributes as $attrs)
					{
						$attr_details=explode(",",$cart['attributes']);
						foreach($attr_details as $attr)
						{
							$a=explode(":",$attr);
							$prd_attrs[$a[0]]=$a[1];
						}
					}
					//$prod_id=$this->db->query("select pid,group_concat(attr_id,",",attr_value) from m_product_attributes where attr_id=? and attr_value=?",array($prd_attrs['color'],$prd_attrs['size']))->row_array();
					//echo $this->db->last_query();exit;
				}
			}
		
			if(!empty($insurance_details['insurance_deals']))
			{
				$insurance['opted_insurance'] = 1;
				$insurance_deals = array();
				$ins_deals=explode(",",$insurance_details['insurance_deals']);
				
				foreach($ins_deals as $ins_pid)
				{
					array_push($insurance_deals,$ins_pid);
				}
				$insurance_details['insurance_deals'] = implode(',',$insurance_deals);
				
				//if insurance exist but new member if not select insurance then offer type to set 3
				/*if($insurance_selected==0 && $new_mem==1)
				{
					if($ttl_order_value >= 10000)
						$offr_sel_type=1;
					else
						$offr_sel_type=3;
				}*/
				
				//if insurance exist but old member if not select insurance then offer type to set 0
				if($insurance_selected==0 && $new_mem==0)
					$offr_sel_type=0;
				
				//if insurance exist and new member if select insurance then offer type to set 2
				if($new_mem==1 && $insurance_selected!=0)
					$offr_sel_type=2;
				
				//if insurance exist and old member if select insurance then offer type to set 0
				if($new_mem==0 && $h_insurance && $insurance_selected!=0)
					$offr_sel_type=0;
			}else
			{
				$insurance_details['opted_insurance'] = 0;
				$insurance_details['insurance_deals'] = '';
				
				if($new_mem==1 && $h_insurance==0 &&  $ttl_order_value >= $min_ord)
					$offr_sel_type=1;
				
				//echo ("$new_mem==1 && $h_insurance==0 &&  $ttl_order_value < $min_ord");
				if($new_mem==1 && $h_insurance==0 &&  $ttl_order_value < $min_ord)
				{
					$offr_sel_type=2;
				}
				
				//die($new_mem.'<=TEST');
			}
			
			//------------offer type setting block---------------
			/*if($h_insurance)
			{
				if($insurance_details)
				{
					if(isset($insurance_details['opted_insurance']))
						if($insurance_details['opted_insurance'])
							$insurance_selected=1;
				}
				
				
				
			}else{
				
			}*/
			//------------offer type setting block end---------------
			
			//check order placing type
			if($is_voucher_order)
			{
				if(!$vocher_codes || !array_filter(explode(',',$vocher_codes)))
					$this->_output_handle('json',false,array('error_code'=>2018,'error_msg'=>"Member id and voucher code must be require"));
		
				$member=$this->apim->get_member_details($member_id );
		
				$this->coupon_redeemtion($vocher_codes,$member[0]['mobile'],$pid,$qty,$fid,$member_id);
			}else{
				$order_status=$this->to_process_order($fid,$mid,implode(",",$pid),implode(",",$qty),$offr_sel_type,'',1,$insurance_details);
				
				if(is_array($order_status))
				{
					if(isset($order_status['error_code']))
						$this->_output_handle('json',false,$order_status);
					else if(isset($order_status['trans']) && $order_status['trans']){
						
						$this->db->query("update pnh_api_franchise_cart_info set status=0 where status=1 and franchise_id=? and member_id=? and pid in (".implode(",",$pid).") ",array($fid,$mid));
						$this->_output_handle('json',true,array("order_msg"=>"Order placed successfully.Transaction Id:".$order_status['trans']));
					}
					
				}else{
					$this->_output_handle('json',false,array('error_code'=>2047,'error_msg'=>"Your order not placed due to error found"));
				}
			}
		}
	}
	
	/**
	 * function for process the order
	 */
	function to_process_order($fid=0,$mid=0,$pid=0,$qty=0,$offr_sel_type=0,$redeem='',$return=0,$insurance,$d_attr=array(),$lens_item_det=array())
	{
		$min_ord = 500;
		//http://localhost/snapittoday_live/api/to_process_order?fid=17&mid=22006889&pid=11314779&qty=1&offer_type=1&insurance[opted_insurance]=&insurance[insurance_deals]=14967769&insurance[proof_type]=&insurance[proof_id]=&insurance[first_name]=&insurance[last_name]=&insurance[mob_no]=&insurance[address]=&insurance[city]=&insurance[pincode]=&insurance[proof_name]=&insurance[proof_address]=&redeem=
		//$this->_is_validauthkey();
		$this->load->model('erpmodel','erpm');
		$admin['userid'] = 6;
		$admin =$this->apim->get_api_user();
		$updated_by=$admin["userid"];
		if(!$insurance)
		{
			$insurance=array();
			$insurance['opted_insurance']=$insurance['insurance_deals']=$insurance['proof_type']=$insurance['proof_id']=$insurance['first_name']=$insurance['last_name']=$insurance['mob_no']=$insurance['address']=$insurance['city']=$insurance['pincode']=$insurance['proof_name']=$insurance['proof_address']='';
		}
		//if set get variable data the parameters are receiving from get
	
		if(isset($_GET) && !empty($_GET))
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
		$fran_price_type=$fran['price_type'];
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
	
		
		// is any offer given? like recharge, insurance,etc
		$is_recharge_given = $this->db->query("SELECT COUNT(*) AS is_rechrge_gv FROM pnh_member_offers mo
													LEFT JOIN king_orders o ON o.id=mo.order_id AND o.status!=3
													WHERE mo.offer_type=1 AND mo.offer_type NOT IN (0,2,3) AND mo.process_status!=1 AND mo.member_id=?
													HAVING is_rechrge_gv IS NOT NULL"
											,$mid)->row()->is_rechrge_gv;
		// if recharge not given set offer type=1 (recharge)
		if($is_recharge_given==0)
			$offr_sel_type=1;
		
		
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
			
			/*
			if($deal['publish'])
				array_push($pids['available'], $p);
			else
				array_push($pids['not_available'], $p);
			*/
			
			$avail=$this->erpm->do_stock_check(array($deal['id']),array(1),true);
			
			if($avail)
			{
				if($avail[$deal['id']][0]['stk']==0 && $avail[$deal['id']][0]['status']==0)
					array_push($pids['not_available'], $p);
				else
					array_push($pids['available'], $p);
			}
			
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
				elseif($ttl_order_value <= 500)
					$offr_sel_type=2;
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
			$mem_orders_ttl=0;
			$ttl_orders_res =$this->db->query("SELECT COUNT(transid) as l
												FROM king_orders 
												WHERE member_id=? AND STATUS NOT IN (3) 
											    HAVING SUM(i_price*quantity) >= ?",array($mid,MEM_MIN_ORDER_VAL) );
			if($ttl_orders_res->num_rows())
				$mem_orders_ttl=$ttl_orders_res->row()->l;
			
			$is_rechrge_given=0;
			$is_rechrge_given_res = $this->db->query("select * from pnh_member_offers where offer_type=1 and member_id=?",$mid);
			if( $is_rechrge_given_res->num_rows() )
				$is_rechrge_given=1;
				
			if($new_member==1 && $h_insurance==0 &&  $ttl_order_value >= $min_ord)
				$offr_sel_type=1;
			elseif($new_member==1 && $h_insurance==0 &&  $ttl_order_value < $min_ord){
				$offr_sel_type=2;
			}
			elseif($new_member==0 && $h_insurance==0 &&  $mem_orders_ttl==0 && $is_rechrge_given==0){
				$offr_sel_type=1;
			}
		}
		
		//echo $mem_orders_ttl.'=='.$offr_sel_type;die();
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
	
			$member_price_det = $this->erpm->get_memberprice($items[$i]['itemid'],$fid,$mid);
	
				if($member_price_det['status'] == 'success')
				{
					$items[$i]['price_type']=$member_price_det['price_type'];
					if($member_price_det['price_type'])
					{
							$items[$i]['price']=($member_price_det['member_price']);
							$items[$i]['mp_frn_max_qty']=$member_price_det['mp_frn_max_qty'];
							$items[$i]['mp_mem_max_qty']=$member_price_det['mp_mem_max_qty'];
							$items[$i]['max_order_qty']=$member_price_det['max_ord_qty'];
							$items[$i]['logref_id']=$member_price_det['logref_id'];
							$items[$i]['other_price']=$prod['price'];
							$items[$i]['logref_id']=$member_price_det['logref_id'];
					}
					else
					{
						$items[$i]['price']=($member_price_det['offer_price']);
						$items[$i]['other_price']=0;
						$items[$i]['max_order_qty']=$member_price_det['max_ord_qty'];
						$items[$i]['logref_id']=0;
					}
				}else
				{
					$this->_output_handle('json',false,array('error_code'=>2046,'error_msg'=>$member_price_det['message']));
				}
				
			if($prod['is_combo']=="1")
			{
				$items[$i]['discount']=$items[$i]['price']/100*$margin['combo_margin'];
			}
			else
			{
				$items[$i]['discount']=$items[$i]['price']/100*$margin['margin'];
			}
			
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

			$items[$i]['lens_item_det'] = isset($lens_item_det[$item['pid']])?$lens_item_det[$item['pid']]:array(); 
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
			$error_resp = array('error_code'=>2046,'purchase_limit'=>(format_price($fran_crdet[3],0)),'error_msg'=>"Insufficient balance! Balance in your account Rs {$fran['current_balance']} Total order amount : Rs $d_total");
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
			$item['qty'] = (int)$item['qty'];
			$item['qty'] = $item['qty']?$item['qty']:1;
			
			//$item['qty'] = 1;
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
				$inp['is_memberprice']=$fran_price_type;
				$inp['other_price']=$item['other_price'];
				
				$inp['lens_item_orderdet']= $item['lens_item_det']?json_encode($item['lens_item_det']):'';
				
				$inp['mp_logid']=$item['logref_id'];
	
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
		/*elseif($offr_sel_type == 1 && $new_member == 0 ) {
			$offer_ret = $this->erpm->pnh_member_recharge($o_total,$insurance);
		}*/
		elseif($offr_sel_type == 1 && $o_total >= MEM_MIN_ORDER_VAL && $new_member == 0 && $is_recharge_given==0)//  && $new_member == 0
        {
        	$offer_ret = $this->pnh_member_recharge($o_total,$insurance);
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
			
			if($order['offer_price'])
				$order['franchise_price_percentage'] = 100-($order['franchise_price']/$order['offer_price'])*100 .'%';
			else
				$order['franchise_price_percentage'] = 100 .'%';
	
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
	 * @author Roopa <roopashree@storeking.in>
	 */
	function similar_products()
	{
		$this->benchmark->mark('code_start');
		
		$this->_is_validauthkey();
		$_POST=$_REQUEST;
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		$prod_id=$this->input->post('pnh_id');
		$start=$this->input->post('start');
		$end=$this->input->post('end');

		if(!$start)
			$start=0;

		if(!$end)
			$end=10;

		$prod_res=$this->db->query("select * from king_dealitems where pnh_id=?",$prod_id);

		if($prod_res->num_rows()==0)
		{
			$this->_output_handle('json',false,array('error_code'=>2018,'error_msg'=>"Invalid Product ID entered"));
		}
		else
		{
			$deal_info=$this->apim->get_deal($prod_id);

			$similar_prods_det=$this->apim->get_similar_products($deal_info['itemid'],$start,$end);

			$brand_list=array();

			if($similar_prods_det['similar_products'])
			{
				foreach($similar_prods_det['similar_products'] as $s)
				{
					if(!isset($brand_list[$s['brandid']]))
						$brand_list[$s['brandid']]=array('brandid'=>$s['brandid'],"brandname"=>$s['brand_name']);
				}
			}
			
			$this->benchmark->mark('code_end');
			$elapsed_time=$this->benchmark->elapsed_time('code_start', 'code_end');
			
			if($similar_prods_det['error'])
				$this->_output_handle('json',false,array('error_code'=>2018,'error_msg'=>"No Deals found",'elapsed_time'=>$elapsed_time));
			else
				$this->_output_handle('json',true,array('similar_products'=>$similar_prods_det['similar_products'],'category'=>$deal_info['category'],'brand_list'=>$brand_list,'elapsed_time'=>$elapsed_time ) );
		}
	}
	
	/**
	 * function to get popular products
	 * @author Roopa <roopashree@storeking.in>
	 */
	function popular_products()
	{
		 //$_POST=$_GET;
		$this->benchmark->mark('code_start');
		 $this->_is_validauthkey();  
		
		$userid=$this->input->post('user_id');
		$fid=$this->input->post('franchise_id');
		$menuid=$this->input->post('menu_id');
		$catid=$this->input->post('cat_id');
		$brandid=$this->input->post('brand_id');
		$publish=$this->input->post('publish_status');
		
		
		if(is_array($brandid))
			$brandid=implode(",",$brandid);
		
		$start=$this->input->post('start');
		$limit=$this->input->post('limit');
		
		
		$popular_prod_det=$this->apim->get_popular_products($menuid,$catid,$brandid,$start,$limit,$publish,$fid);
		if( !isset($popular_prod_det['error']) && $popular_prod_det['popular_prod_res'])
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
				
				//if(!isset($menu_list[$p['menuid']]))
				//	$menu_list[$p['menuid']]=array('menuid'=>$p['menuid'],"menuname"=>$p['menuname']);
			}
		}
		else {
			$this->benchmark->mark('code_end');
			$elapsed_time=$this->benchmark->elapsed_time('code_start', 'code_end');
			$this->_output_handle('json',true,array("deal_list"=>'','total_deals'=>0,'elapsed_time'=>$elapsed_time));
		}
		
		$this->benchmark->mark('code_end');
		$elapsed_time=$this->benchmark->elapsed_time('code_start', 'code_end');

		if(!$popular_prod_det['popular_prod_res'])
			$this->_output_handle('json',true,array("deal_list"=>'','total_deals'=>0,'elapsed_time'=>$elapsed_time));
		else
			$this->_output_handle('json',true,array('popular_products'=>$popular_prod_det['popular_prod_res'],'deal_list'=>$popular_prod_det['popular_prod_res'],'total_deals'=>$popular_prod_det['total_deals'],'elapsed_time'=>$elapsed_time));
	}

	/**
	 * function to get pending payment of franchisee
	 * @author Roopa <roopashree@storeking.in>
	 */
   function fran_pending_payment()
   {
   		//$_POST=$_GET;
   		
   		$this->_is_validauthkey();
   		//$userid=$this->input->post('user_id'); 
   		$fid=$this->input->post('franchise_id');
   		
   		if(!$fid)
   		{
   			$this->_output_handle('json',false,array('error_code'=>2018,'error_msg'=>"Please enter franchise id"));
   		}
   		else
   		{
   			$fr_accdet=$this->erpm->get_franchise_account_stat_byid($fid);
   			
   			$pending_payment = ($fr_accdet['shipped_tilldate']-($fr_accdet['paid_tilldate']+$fr_accdet['acc_adjustments_val']+$fr_accdet['credit_note_amt']));
   			
   			$pending_payment=format_price($pending_payment);
			
   			$this->_output_handle('json',true,array('pending_payment'=>$pending_payment));
   		
   		}
   }
	/**
	 * function to get uncleared payment of franchisee
	 * @author Roopa <roopashree@storeking.in>
	 * @last_modified 19_june_2014
	 */
   function fran_uncleared_payment()
   {
	   	$this->_is_validauthkey();
	   	$userid=$this->input->post('user_id');
	   	$fid=$this->input->post('franchise_id');
	   	$receipt_id=$this->input->post('receipt_id');
	   	$intransit=$this->input->post('in_transit');
	   	if($intransit==0)
	   		$intransit="in_transit=0";
	   	else 
	   		$intransit="in_transit!=0";
		$cond=''; 
		
		if(!$receipt_id)
			$receipt_id=0;
		
		if($receipt_id != 0)
		 	$cond .=" and r.receipt_id=$receipt_id";
	   	if(!$fid)
	   	{
	   		$this->_output_handle('json',false,array('error_code'=>2018,'error_msg'=>"Please enter franchise id"));
	   	}
	   	else
	   	{
	   		$sql="SELECT f.franchise_name,r.receipt_id,r.receipt_amount,r.receipt_type,IF(payment_mode=0,'cash', IF(payment_mode=1,'cheque',IF(payment_mode=2,'dd',IF(payment_mode=3,'trans','na')))) AS payment_mode,r.bank_name,r.remarks,r.instrument_no,FROM_UNIXTIME(r.instrument_date) AS instrument_date,
	   				DATE_FORMAT(FROM_UNIXTIME(r.created_on) ,'%d/%m/%Y') AS created_on,DATE_FORMAT(FROM_UNIXTIME(r.created_on) ,'%h:%i %p') AS created_on_time
					 FROM pnh_t_receipt_info r
					JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id
					WHERE r.franchise_id = ? $cond AND STATUS = 0 AND receipt_type = 1 AND $intransit
					ORDER BY r.receipt_id DESC";
	   		
	   		$uncleared_payment_det=$this->db->query($sql,$fid);
	   		
	   		$uncleared_payment_res=$uncleared_payment_det->result_array();
	   		
	   		$franchise_det=$uncleared_payment_det->row_array();
	   		
	   		//$franchise_name=$franchise_det['franchise_name'];
	   		
	   		$acc_statement=$this->erpm->get_franchise_account_stat_byid($fid);
	   		
	   		$uncleared_payment_amt = format_price($acc_statement['uncleared_payment'],2);
	   		
	   	
	   		$this->_output_handle('json',true,array('uncleared_payment'=>$uncleared_payment_amt,'uncleared_payment_details'=>$uncleared_payment_res));
	   	}
   }
   
   /**
    * function to get recent payments of franchisee
    * @author Roopa <roopashree@storeking.in>
    */
   function fran_recent_payments()
   {
	   	$this->_is_validauthkey();
	   	
	   	//	$userid=$this->input->post('user_id');
	   	$fid=$this->input->post('franchise_id');
	   	$st_date=$this->input->post('start_date');
	   	$en_date=$this->input->post('end_date');
	   	$receipt_id=$this->input->post('receipt_id');
 
	  if(!$receipt_id)
	 	 $receipt_id=0;
	 
	  if(!$fid)
	  {
	  	$this->_output_handle('json',false,array('error_code'=>2018,'error_msg'=>"Please enter franchise id"));
	  }
	  else 
	  {
	  	$recent_payment_res=$this->apim->get_recent_payments_byfran($fid,$st_date,$en_date,$receipt_id);
	  
	   		
   		if(@$recent_payment_res['error'])
   			$this->_output_handle('json',false,array('error_code'=>2018,'error_msg'=>"No recent payments"));
   		else 
   			$this->_output_handle('json',true,array('recent_payments'=>$recent_payment_res));
	  }
   }
   
    /**
    * function to make payment
    * @author Roopa <roopashree@storeking.in>
    */
   function make_payment()
   {
   		$this->_is_validauthkey();
   		 
   		//$userid=$this->input->post('user_id');
		$admin =$this->apim->get_api_user();
		$userid=$admin['userid'];
   		
   		$fid=$this->input->post('franchise_id');
   		$payment_mode=$this->input->post('payment_type');
   		$instrument_no=$this->input->post('instrument_no');
   		$instrument_date=$this->input->post('instrument_date');
   		$receipt_amount=$this->input->post('receipt_amt');
   		$bank_name=$this->input->post('bank_name');
   		$remarks=$this->input->post('remarks');
   		$transit_type=$this->input->post('transit_type');
   		$courier=$this->input->post('courier_name');
   		$awb=$this->input->post('r_awb');
   		$emp_name=$this->input->post('emp_name');
   		$emp_phno=$this->input->post('emp_phno');
   		
   		$this->load->library('form_validation');
		$this->form_validation->set_rules('franchise_id','Franchise id','required');
		$this->form_validation->set_rules('receipt_amt','Amount','required');
		$this->form_validation->set_rules('payment_type','Payment type','required');
		$this->form_validation->set_rules('remarks','Remarks','required');
		$this->form_validation->set_rules('transit_type','Transit type','required');
		
		if($payment_mode==1 || $payment_mode==2)
		{
			//echo 3;die();
			$this->form_validation->set_rules('instrument_no','Instrument number','required');
			
			$this->form_validation->set_rules('bank_name','Bank','required');
				
			$this->form_validation->set_rules('instrument_date','Instrument Date','required');
		}
			
		if($transit_type==1)   //via courier
		{
			$this->form_validation->set_rules('courier_name','courier','required');
			
			$this->form_validation->set_rules('r_awb','AWB','required');
				
		}
		
		if($transit_type==2)   //via executive
		{
			$this->form_validation->set_rules('emp_name','Employee name','required');
			
			$this->form_validation->set_rules('emp_phno','Executive contact number','required');
			
			//check for employee number validation
		
			$is_valid_contactno=$this->apim->is_valid_emp_phno($fid,$emp_phno);
		
			if(!$is_valid_contactno)
			{
				$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>"Invalid employee number"));
			}
				
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
			$inp_param['courier_name']=$courier;
			$inp_param['awb']=$awb;
			$inp_param['emp_name']=$emp_name;
			$inp_param['contact_no']=$emp_phno;
				
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

	/**
	 * Function to view orders of the login franchise-view between date range or recent 10 orders
	 * @author Shivaraj <shivaraj@storeking.in>
	 * @param type $transid mixed
	 */
	function view_franchise_orders()
	{
		$this->_is_validauthkey();
		$username=$this->input->post('username');// or i.e Username=Mobile number
		$from=$this->input->post('from'); //option or specify both fields
		$to=$this->input->post('to'); //option or specify both fields
		$fid=$this->input->post('franchise_id');
		$status=$this->input->post('status');// optional/Order status ~0,1,2,3,4
		if(!$status)
			$status='';
		
		$admin =$this->apim->get_api_user();
		$userid=$admin['userid'];

		$this->load->model('reservation_model','reservations');//$this->load->model('erpmodel','erpm');
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('username','Username','required|callback__chk_username');
		
		if($this->form_validation->run() === FALSE)
		{
			$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>validation_errors()));
		}else
		{
				$cond = $limit_cond = $status_cond= $join_cond ='';
				$output = array();

				if($from==0 && $to ==0)
				{
					// Show default 10 orders
					$cond .= '';
					$limit_cond .= ' LIMIT 0,20 ';
				}
				elseif($from!=0 && $to!=0)
				{
					$from = date("Y-m-d H:i:s",strtotime($from));
					$to = date("Y-m-d H:i:s",strtotime($to));
					// process orders by date range
					$cond .= ' AND tr.actiontime BETWEEN UNIX_TIMESTAMP("'.$from.' 00:00:00") AND UNIX_TIMESTAMP("'.$to.' 23:59:59") ';
				}
				else
				{
					$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>"Invalid date range given." ) );
				}
				
				if( match_in_list($status, '0,1,2,3,4') ) // 1:ordered 2:shipped 3:cancelled 4:delivered
				{
					if($status == 1)
						$cond.=' AND o.status in (0,1)';
					elseif($status == 4) {
						$join_cond .= ' join king_invoice i on i.order_id = o.id
										join shipment_batch_process_invoice_link sd on sd.invoice_no = i.invoice_no ';
						$cond.=' AND o.status in (2) and sd.is_delivered = 1';
					}
					else
						$cond.=' AND o.status='.$status;
				}
				
				$ttl_sql = "SELECT COUNT(distinct tr.transid) as ttl
								FROM king_transactions tr 
								JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
								JOIN king_orders o on o.transid = tr.transid
								$join_cond
								WHERE f.login_mobile1 = ? OR f.franchise_id = ? $cond ";
//				die('<pre>'.$ttl_sql);
				$ttl_res = $this->db->query($ttl_sql,array($username,$fid));
				$ttl_rows = $ttl_res->row()->ttl;
				if($ttl_rows <= 0)
				{
					$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>"No orders found." ) );
				}
				else
				{
						//Default orders
						$sql = "SELECT tr.transid,tr.actiontime,f.franchise_name,tr.trans_created_by FROM king_transactions tr 
										JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
										join king_orders o on o.transid = tr.transid
										$join_cond
										WHERE f.login_mobile1 = ? OR f.franchise_id = ? $cond
										GROUP BY tr.transid ORDER BY tr.id DESC $limit_cond";
						$trans_det_rslt = $this->db->query($sql,array($username,$fid) );

						$o_status_array = array('Pending',"Ordered","Shipped","Cancelled","Delivered");
						$trans_det_res = $trans_det_rslt->result_array();
						
						foreach($trans_det_res as $trans_det )
						{
							$shipment_status = $this->config->item("shipment_status");
							$order_det=array();
							$transid = $trans_det['transid'];
							
							$trans_ship_det = $this->apim->get_transit_status_transid($transid,$status);
							$invoice_status=$trans_ship_det['status'];
							
							if( $trans_ship_det ) {
								
								if($status === 2) {// Shipped
									
									if( $invoice_status=='2' )	{ 
										$output[$transid]['transit_msg'] = 'Shipped';
									}
									else {
										continue;
									}
								}
								elseif($status === 4) { //Delivered
									
									if( $invoice_status=="3" || $invoice_status=="5" )	{ 
										$output[$transid]['transit_msg'] = 'Delivered';
									}
									else {
										continue;
									}
								}
								$output[$transid]['transit_status'] = $shipment_status[$invoice_status]; //
							}
							
								
							$output[$transid]['transid'] = $transid;
							$output[$transid]['actiontime'] = format_datetime_ts($trans_det['actiontime']);
							$output[$transid]['franchise_name'] = $trans_det['franchise_name'];
							$output[$transid]['created_by'] = $this->reservations->get_username_byid($trans_det['trans_created_by']);
							$output[$transid]['trans_ttl_cost'] = $this->erpm->trans_fee_insu_value($transid);
							
							$status_ord=$status;
							if($status==2 || $status == 4)
							{
								$status_ord='';
							}
							
							$trans_orders = $this->reservations->get_orders_of_trans($transid,'all',$status_ord);
								
							foreach($trans_orders as $j=>$order_i)
							{
								$orderid=$order_i['id'];
								$delivered=0;
								$log=$this->apim->get_order_ship_det($orderid);
								
								if(!empty($log)){
									if(search_in_array('Delivered',$log))
										$delivered=1;
								}
								if($status == 1 && $log )
									continue;
								
								if($status==2){
									if($order_i['status'] != 2)
										continue;
								}
								if($status==4) {
									if(!$log)
										continue;

									if($delivered != 1)
										continue;
								}
									
									$order_det[$j]['itemid'] = $order_i['itemid'];
									$order_det[$j]['orderid'] = $orderid;
									$order_det[$j]['pnh_id'] = $order_i['pnh_id'];
									$order_det[$j]['name'] = $order_i['name'];
									$order_det[$j]['quantity'] = $order_i['quantity'];
									$order_det[$j]['i_orgprice'] = $order_i['i_orgprice'];
									$order_det[$j]['image_url'] = $order_i['image_url'];
									$order_det[$j]['amount']=round($order_i['i_orgprice']-($order_i['i_coup_discount']+$order_i['i_discount']),2);
									$order_det[$j]['status'] = $o_status_array[$order_i['status']];
									$order_det[$j]['is_delivered'] = $delivered;
									
									$order_det[$j]['transit_det'] = $log;
									
							}
							$output[$transid]['orders'] = $order_det;
							if(empty($order_det) )
								unset($output[$transid]);
							
						}
//						print_r($output);exit;echo count($output);exit;
						$this->_output_handle('json',true,array('order_det'=>$output,'total_rows' => $ttl_rows) );
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
    * @author Roopa <roopashree@storeking.in>
    */
   function orders_details_byinvoiceno()
   {
   		//$_POST=$_GET;
   		
	   	$this->_is_validauthkey();
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
			/*
			else if($valid_invno)
			{
				if($this->db->query("select invoice_no from pnh_invoice_returns where invoice_no=? and status=0 and transit_type=1",$invoice_no)->num_rows()!=0)
				{
					$this->_output_handle('json',false,array('error_code'=>2018,'error_msg'=>'Invoice already updated for return'));
					return false;
				}
					
			}
			*/
			
			$order_det_res=$this->erpm->get_invoicedet_forreturn($valid_invno);

			$return_cond=array();
			  $return_cond= $this->config->item('return_cond');
		
			if($order_det_res)
				$this->_output_handle('json',true,array('items_list'=>$order_det_res,'returns_condition'=>$return_cond));
		}
   	}

   	/**
   	 * function to view bank details
   	 * @author Roopa <roopashree@storeking.in>
   	 */
   	function bank_list()
   	{
   		//$this->_is_validauthkey();
   		$userid=$this->input->post('user_id');
   		$bank_info=$this->apim->get_bank_list();
   		
		if($bank_info)
			$this->_output_handle('json',true,array('bank_list'=>$bank_info));
		else
			$this->_output_handle('json',false,array('error_code'=>2018,'error_msg'=>'No Data found'));
   	}
	
	/**
	 * Funtion to store the franchise search keywords to db
	 * @author Shivaraj <shivaraj@storeking.in>
	 */
	function post_request()
	{
		// franchise request of a product
		$this->_is_validauthkey();
		
		// ticket_from={web,api}
		$this->load->library('form_validation');
		//$this->form_validation->set_rules('username','Username','required');//|callback__chk_username
		$this->form_validation->set_rules('type','Request Type','required');
		$this->form_validation->set_rules('franchise_id','Franchise Id','required');
		//$this->form_validation->set_rules('title','Title','required|callback__chk_title_size');
		$this->form_validation->set_rules('desc','Description','required');
		
		if($this->form_validation->run() === FALSE)
		{
			$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>validation_errors()));
		}else
		{
			$username = $this->input->post('username');// or i.e Username=Mobile number
			$franchise_id = $this->input->post('franchise_id');// franchise_id
			$type = $this->input->post('type'); //10:Request, 11:Complaint
			$related_to = $this->input->post('related_to'); // 0:not specific, 1:Product, 2:Payment, 3:Shipment, 4:Insurance, 5:Technical
//			$title = $this->input->post('title'); 
			$pid = $this->input->post('pid'); 
			$desc = $this->input->post('desc');  // request desc
			$priority = $this->input->post('priority');  // request priority - 0:Low, 1:Medium, 2:High, 3:Urgent
			$medium = $this->input->post('medium');  // request priority - 0:email, 1:Phone, 2:other
			
			$req_mem_name = $this->input->post('req_mem_name');  // Name of request holder
			$req_mem_mobile = $this->input->post('req_mem_mobile');  // Mobile of request holder
			
			if($pid)
			{
				$pname = @$this->db->query("select name from king_dealitems where pnh_id = ? ",$pid)->row()->name;
				if($pname)
					$desc = $pname." \n".$desc;
			}
			
			// get franchise det by mobile number
			$fran_det_res = $this->db->query("SELECT f.franchise_id,f.franchise_name,f.login_mobile1 FROM pnh_m_franchise_info f where f.login_mobile1 = ? OR franchise_id = ? ",array($username,$franchise_id) );
			if($fran_det_res->num_rows())
			{
				// ================< CREATE TICKET CODE STARTS >=============================
				$fran_det = $fran_det_res->row_array();
				$franchise_id = $fran_det['franchise_id'];
				$franchise_name = $fran_det['franchise_name'];
				$franchise_mobile = $fran_det['login_mobile1'];
				
				$email=$transid='';
				if($priority=='')
					$priority = 2; // 0:Low, 1:Medium, 2:High, 3:Urgent
				
				if($medium=='')
					$medium=0; // 0:email, 1:Phone, 2:other
				
				$ticket_det = $this->apim->do_ticket($franchise_name,$franchise_mobile,$desc,$email,$transid,$type,$priority,$franchise_id,$related_to,$medium,$req_mem_name,$req_mem_mobile);
				// ================< CREATE TICKET CODE ENDS >=============================
				if($ticket_det)
				{
					$ticket_id = $ticket_det['ticket_id'];
					$ticket_no = $ticket_det['ticket_no'];
					
					$tkt_status = 'Open';
					$username = $franchise_name;
					$resp = $this->employee->send_dept_emails($related_to,$ticket_id,$ticket_no,$franchise_name,$req_mem_name,$desc,$tkt_status);
					if( isset($resp['error'] ) ) {
						$this->_output_handle('json',false,array('error_code'=>2009,'error_msg'=>$resp['error']) );
					}
					else {
						$this->_output_handle('json',true,array("ticket_id"=>$ticket_id,'message'=>$resp['message']) );
					}
				}
				else {
					$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>"Request not submitted.") );
				}
			}
			else
			{
				$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>"Unknown Franchise." ) );
			}
			//$ticket_id = $this->insert_request($username,$franchise_id,$type,$title,$desc);//old method
		}
		
	}
	
	/**
	 * API function to list request lists by franchise_id
	 * @author Shivaraj <shivaraj@storeking.in>
	 * @return JSON
	 */
	function get_request_list()
	{
		$this->_is_validauthkey();
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('franchise_id','Franchise Id','required');//|callback__chk_username
		
		
		if($this->form_validation->run() === FALSE)
		{
			$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>validation_errors()));
		}else
		{
			$franchise_id = $this->input->post("franchise_id");
			$type = $this->input->post("type"); // type is 7:request, 8:complaint
			$s = $this->input->post("date_from"); // date filter
			$e = $this->input->post("date_to"); // date filter
			$limit = $this->input->post("limit");
			
			$tickets_det = $this->apim->gettickets($franchise_id,$s,$e,$type,$limit);
			if($tickets_det)
			{
				$this->_output_handle('json',true,array('tickets_det'=>$tickets_det) );
			}
			else
			{
				$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>"No tickets found.") );
			}
		}
	}
	
	/**
	 * API to return individual ticket details by ticket id
	 * @author Shivaraj <shivaraj@storeking.in>
	 * @return JSON
	 */
	function get_request_byid()
	{
		$this->_is_validauthkey();
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('ticket_id','Ticket Id','required');//|callback__chk_username
		
		
		if($this->form_validation->run() === FALSE)
		{
			$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>validation_errors()));
		}else
		{

			$ticket_id = $this->input->post("ticket_id");
			
			$tickets_det = $this->apim->getticket($ticket_id);
			if($tickets_det)
			{
				$this->_output_handle('json',true,array('tickets_det'=>$tickets_det) );
			}
			else
			{
				$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>"No tickets found.") );
			}
		}
	}
	
	/**
	 * function to add returns
	 * @author Roopa <roopashree@storeking.in>
	 */
	function add_pnh_invoice_return()
	{
		$this->_is_validauthkey();
		$prod_rcvd_pid=$this->input->post('prod_rcvd_pid');
		$prod_rcvd_qty=$this->input->post('prod_rcvd_qty');
		$prod_rcvd_imeino=$this->input->post('prod_rcvd_imeino');
		$prod_rcvd_cond=$this->input->post('prod_rcvd_cond');
		$prod_rcvd_rmrks=$this->input->post('prod_rcvd_rmrks');
		$prod_rcvd_transtype=$this->input->post('prod_rcvd_transtype');
		$prod_rcvd_courier=$this->input->post('prod_rcvd_courier');
		$prod_rcvd_awb=$this->input->post('prod_rcvd_awb');
		$prod_rcvd_emp_name=$this->input->post('prod_rcvd_empname');
		$prod_rcvd_emp_ph=$this->input->post('prod_rcvd_empph');
		$franchise_id=$this->input->post('franchise_id');
		$invno=$this->input->post('invoice_no');
		$orderids=$this->input->post('order_id');
		$prod_rcvd_empphno=$this->input->post('prod_rcvd_empphno');

		//print_r($_POST);exit;
		
		$this->load->library('form_validation');
	
		if($prod_rcvd_transtype == 1)   //transit type="via Courier";
		{
			$this->form_validation->set_rules('prod_rcvd_courier','Courier','required');
			$this->form_validation->set_rules('prod_rcvd_awb','Air way bill','required');
		}
	
		if($prod_rcvd_transtype==2)   //transit type="via employee";
		{
			$this->form_validation->set_rules('prod_rcvd_empname','Employee name','required');
			$this->form_validation->set_rules('prod_rcvd_empph','Employee phone number','required');
		}
	
		$fran_details=$this->db->query('select * from pnh_m_franchise_info where franchise_id=? or login_mobile1 = ? or login_mobile2 = ? ',array($franchise_id,$franchise_id,$franchise_id))->row_array();
	
		$name=$fran_details['franchise_name'];
		$mobile=$fran_details['login_mobile1'];
		
		//check for is serial number required for return product
		$is_serial_required=$this->db->query("select is_serial_required from m_product_info where product_id=?",$prod_rcvd_pid)->row()->is_serial_required;

		if($prod_rcvd_pid)
		{
			
				
				if($is_serial_required)
				{
					
					$this->form_validation->set_rules('prod_rcvd_imeino','IMEI number','required');
					
					$product_info=$this->db->query("SELECT i.imei_no,i.status,i.order_id,i.product_id  FROM t_imei_no i
														JOIN m_product_info p ON p.product_id=i.product_id
								                        join king_orders o on o.id=i.order_id and o.status!=3
														WHERE i.product_id=? and i.imei_no=? AND is_serial_required=1 AND i.status=1 and is_returned=0",array($prod_rcvd_pid,$prod_rcvd_imeino));
								
					if($product_info->num_rows()==0)
							$this->_output_handle('json',false,array('error_code'=>2033,'error_msg'=>"Invalid IMEI number entered"));
					
					if($this->form_validation->run() == FALSE)
					{
						$this->_output_handle('json',false,array('error_code'=>2033,'error_msg'=>strip_tags(validation_errors())));
					}	
					
					$this->db->query("update t_imei_no set is_returned = 1 where imei_no = ? and product_id = ? and is_returned = 0 ",array($prod_rcvd_imeino,$prod_rcvd_pid));
					
				}	
					$this->db->query("insert into pnh_invoice_returns (invoice_no,return_by,handled_by,status,transit_type,returned_on) values (?,?,?,?,?,now()) ",array($invno,$name,0,0,$prod_rcvd_transtype));
					$return_id = $this->db->insert_id();
				
				
			$this->db->query("insert into pnh_invoice_returns_product_link (return_id,order_id,product_id,qty,imei_no,condition_type,status,created_on) values (?,?,?,?,?,?,0,now()) ",array($return_id,$orderids,$prod_rcvd_pid,$prod_rcvd_qty,$prod_rcvd_imeino,$prod_rcvd_cond));
			$return_prod_id = $this->db->insert_id();
			
			$this->db->query("insert into pnh_invoice_returns_remarks (return_prod_id,remarks,parent_id,created_by,created_on) values(?,?,?,?,now()) ",array($return_prod_id,$prod_rcvd_rmrks,0,0));
			$inp_param=array();
			$inp_param['return_id']=$return_id;
			$inp_param['order_id']=$orderids;
			$inp_param['transit_mode']=$prod_rcvd_transtype;
			$inp_param['courier']=$prod_rcvd_courier;
			$inp_param['awb']=$prod_rcvd_awb;
			$inp_param['emp_name']=$prod_rcvd_emp_name;
			$inp_param['emp_phno']=$prod_rcvd_emp_ph;
			$inp_param['status']=0;
			$inp_param['logged_on']=time();
			$inp_param['logged_by']=0;
			$this->db->insert("pnh_t_returns_transit_log",$inp_param);
						
		}
		// ==============< CREATE TICKET CODE START >====================
		$no=rand(1000000000,9999999999);
		$transid=$this->db->query("select * from king_invoice where invoice_no=?",$invno)->row()->transid;
		$type=5;
		$priority=1;
		//Raising ticket for the returns
		$this->db->query("insert into support_tickets(ticket_no,name,mobile,transid,type,priority,created_on) values(?,?,?,?,?,?,now())",array($no,$name,$mobile,$transid,$type,$priority));
		$ticket_id=$this->db->insert_id();
		// ==============< CREATE TICKET CODE ENDS >====================
		
		//$this->erpm->addnotesticket($ticket_id,1,1,$p_remarks,1);
		
		if($ticket_id)
			$this->db->query("update pnh_t_returns_transit_log set ticket_id=? where return_id=?",array($ticket_id,$return_id));
		
		return $this->_output_handle('json',true,array('Return ID'=>$return_id,'Ticket ID'=>$ticket_id));
		
	}
	
	/**
	 * function to view order details for given transid
	 * @author Roopa <roopashree@storeking.in>
	 * @last_modified_by Shivaraj
	 * @last_modified_by Roopa <roopashree@storeking.in>_12_june_2014
	 */
	function orders_bytransid()
	{
		$this->_is_validauthkey();
		
		$this->load->model('reservation_model','reservations');
		//$franchise_id=$this->input->post('franchise_id');
		$transid=$this->input->post('transid');

		$this->load->library('form_validation');
		$this->form_validation->set_rules('transid','Transid','required');
		
		if($this->form_validation->run() === FALSE)
		{
			$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>validation_errors()));
		}else
		{
				//Get trans and order details
				$sql = "SELECT tr.transid,tr.actiontime,tr.trans_created_by,f.franchise_name,f.address,if(tr.order_for=0,'Member order',if(tr.order_for=1,'New Member',if(tr.order_for=2,'Key Member','na'))) as orderfor,tr.order_for,tr.pnh_member_fee,
						m.first_name,m.last_name,m.address,m.pincode,m.email,m.mobile,pnh_member_id,o.ship_person,o.ship_address
								FROM king_transactions tr 
								JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
								JOIN king_orders o on o.transid=tr.transid
								join pnh_member_info m on m.user_id=o.userid
								WHERE tr.transid = ? 
								ORDER BY tr.id DESC";
				$trans_det_rslt = $this->db->query($sql,array($transid) );
				
				if($trans_det_rslt->num_rows() > 0)
				{
						$o_status_array = array('Pending',"Ordered","Shipped","Cancelled","Delivered");
						$trans_det_res = $trans_det_rslt->result_array();
						foreach($trans_det_res as $trans_det )
						{
							$order_det=array();
							$transid = $trans_det['transid'];
							$output[$transid]['transid'] = $transid;
							$output[$transid]['actiontime'] = format_datetime_ts($trans_det['actiontime']);
							$output[$transid]['franchise_name'] = $trans_det['franchise_name'];
							$output[$transid]['created_by'] = $this->reservations->get_username_byid($trans_det['trans_created_by']);
							$output[$transid]['trans_ttl_cost'] = $this->erpm->trans_fee_insu_value($transid);
							$output[$transid]['franchise_address'] = $trans_det['address'];
							$output[$transid]['order_for'] = $trans_det['pnh_member_id'];
							$output[$transid]['member_name'] = $trans_det['first_name'].''.$trans_det['last_name'];
							$output[$transid]['member_address'] = $trans_det['address'];
							$output[$transid]['pincode'] = $trans_det['pincode'];
							$output[$transid]['member_email'] = $trans_det['email'];
							$output[$transid]['member_mobile'] = $trans_det['mobile'];
							$output[$transid]['pincode'] = $trans_det['pincode'];
							$output[$transid]['order_for'] = $trans_det['orderfor'];
							$output[$transid]['orderfor_status'] = $trans_det['order_for'];

							if($trans_det['order_for'] != 2 && $trans_det['pnh_member_fee'] > 0) {
								$output[$transid]['pnh_member_fee'] = $trans_det['pnh_member_fee'];
						    }else 
								$output[$transid]['pnh_member_fee'] = "0";
							
							$output[$transid]['ship_person'] = $trans_det['ship_person'];
							$output[$transid]['ship_address'] = $trans_det['ship_address'];
							
							$trans_orders = $this->reservations->get_orders_of_trans($transid,'all','','',1);
							
							foreach($trans_orders as $j=>$order_i)
							{
									$orderid=$order_i['id'];
									$order_det[$j]['itemid'] = $order_i['itemid'];
									$order_det[$j]['orderid'] = $order_i['id'];
									$order_det[$j]['pnh_id'] = $order_i['pnh_id'];
									$order_det[$j]['name'] = $order_i['name'];
									$order_det[$j]['status'] = $order_i['status'];
									$order_det[$j]['quantity'] = $order_i['quantity'];
									$order_det[$j]['i_orgprice'] = $order_i['i_orgprice'];
									$order_det[$j]['image_url'] = $order_i['image_url'];
									$order_det[$j]['amount']=round($order_i['i_orgprice']-($order_i['i_coup_discount']+$order_i['i_discount']),2);
									$order_det[$j]['status_msg'] = $o_status_array[$order_i['status']];
									$order_det[$j]['member_id'] = $order_i['member_id'];
									$order_det[$j]['member_name'] = $order_i['mem_name'];
									$order_det[$j]['member_phno'] = $order_i['mobile'];
									$order_det[$j]['member_address'] = $order_i['address'];
									$order_det[$j]['mem_add_pincode'] = $order_i['pincode'];
									$order_det[$j]['member_email'] = $order_i['email'];
									$order_det[$j]['commission'] = $order_i['commission'];
									$order_det[$j]['invoice_no'] = $order_i['invoice_no'];
									$order_det[$j]['member_fee'] = $order_i['pnh_member_fee'];
									$order_det[$j]['has_insurance'] = $order_i['has_insurance'];
									$order_det[$j]['insurance_amount'] = $order_i['insurance_amount'];
									$order_det[$j]['order_for'] = $order_i['orderfor'];
									$order_det[$j]['orderfor_status'] = $order_i['order_for'];
									$log=$this->apim->get_order_ship_det($orderid);
									$order_det[$j]['transit_det'] = $log;
							}
							$output[$transid]['orders'] = $order_det;
							//print_r($output);exit;
						}
						$this->_output_handle('json',true,array('order_details'=>$output));
				}
				else
				{
					$this->_output_handle('json',false,array('error_code'=>2002,'error_msg'=>"Invalid transid"));
				}

		}
	}
	

	
	/**
	 * function to cancel order
	 * @author Roopa <roopashree@storeking.in>
	 */
	function cancel_order()
	{
		$this->load->model('reservation_model','reservations');

		$this->_is_validauthkey();

		$franchise_id=$this->input->post('franchise_id');
		$transid=$this->input->post('transid');
		$orderids=$this->input->post('order_id');
		$orderids=explode(',',$orderids);
		$orderids=array_unique(array_filter($orderids));
		$canceled_order=0;
		$order_count=sizeof($orderids);
		$this->load->library('form_validation');

		$this->form_validation->set_rules('transid','Transid','required');

		if($this->form_validation->run() === FALSE)
		{
			$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>validation_errors()));
		}else
		{
			foreach($orderids as $o)
			{
				$this->db->query("update king_orders set status=3,actiontime=time() where id=? and transid=?",array($o,$transid));
				if($this->db->affected_rows()>0)
					$canceled_order++;
			}
				
			if($order_count==$canceled_order)
			{
				$this->_output_handle('json',true,array('canceled orders'=>$orderids,'transid'=>$transid));
			}
		}
	}
	
	/**
	 * function to get latest 
	 */
	function sync_menucatbrands()
	{
		//$this->_is_validauthkey();
		
		$data = array();
		$data['cat_list'] = $this->apim->get_all_categories();
		$data['brand_list'] = $this->apim->get_all_brands();
		$data['menu_list'] = $this->apim->get_all_menu();
		$data['menu_group_list'] = $this->apim->get_menu_groups();
		
		$this->_output_handle('json',true,$data);
	}
	
	/**
	 * function to get groups 
	 */
	function get_groups()
	{
		$this->_is_validauthkey();
		
		$data['menu_list'] = $this->apim->get_groups();
		$this->_output_handle('json',true,$data);
	}
	
	/**
	 * function to get groups categories 
	 */
	function get_group_cats($group_id=0)
	{
		$this->_is_validauthkey();
	
		$data['cat_list'] = $this->apim->get_group_cats($group_id);
		
		$this->_output_handle('json',true,$data);
	}
	
	/**
	 * API to get service request list
	 * @author Shivaraj <shivaraj@storeking.in>_Jun_26_2014
	 */
	function get_services_req_list()
	{
		$this->_is_validauthkey();
		$data['request_list'] = $this->employee->get_services_req_list();
		$this->_output_handle('json',true,$data);
	}
	
	/**
	 * API to get member price details
	 * @author Shivaraj <shivaraj@storeking.in>_Jun_26_2014
	 */
	function get_memberprice($itemid=0,$fid=0,$mid=0,$send_menumargin=0)
	{
		$this->_is_validauthkey();
		$data=array();
		$mp_det_res = $this->erpm->get_memberprice($itemid,$fid,$mid,$send_menumargin); //apim
		if($mp_det_res['status']=='success')
		{
			//$data['mp_det'] = $mp_det_res;
			$this->_output_handle('json',true,$mp_det_res);
		}
		else
		{
			$this->_output_handle('json',FALSE,$mp_det_res);
		}
	}
	
	/**
	 * function to fetch offes content [banners and current offers]
	 * 
	 */
	function get_store_offers()
	{
		//$this->_is_validauthkey();
		$store_menu_id = $this->input->post('store_menu_id');
		//$franchise_id = $this->input->post('franchise_id');
		
		$offer_data = array('banners'=>array(),'offer_product_list'=>array());
		$store_banners_res = $this->db->query("select banner_name,banner_link from m_apk_store_banners where store_id = ? and is_active = 1 ",$store_menu_id);
		if($store_banners_res->num_rows())
		{
			$offer_data['banners'] = $store_banners_res->result_array();
		}else
		{
			$offer_data['banners'] = array();
			$offer_data['banners'][] = array('banner_name'=>'Default Banner','banner_link'=>'http://img6a.flixcart.com/www/promos/new/20140701-131446-230-105moto.gif');
		}
		
		$store_offer_prod_res = $this->db->query("SELECT c.id,pnh_id AS pid,a.name,orgprice AS mrp,member_price as price,ROUND(((orgprice-member_price)/orgprice)*100) AS disc,CONCAT('http://static.snapittoday.com/items/small/',b.pic,'.jpg') AS pimg_link,a.mp_offer_to
													FROM king_dealitems a 
													JOIN king_deals b ON a.dealid = b.dealid
														JOIN pnh_menu_group_link c ON c.menu_id = b.menuid  
														WHERE is_pnh=1 and group_id = ? AND publish = 1 and live = 1 AND a.mp_is_offer=1  AND UNIX_TIMESTAMP() BETWEEN UNIX_TIMESTAMP(a.mp_offer_from) AND UNIX_TIMESTAMP(a.mp_offer_to)
													GROUP BY a.id
													LIMIT 30
												 ",$store_menu_id);
		if($store_offer_prod_res->num_rows())
		{
			$offer_data['offer_product_list'] = $store_offer_prod_res->result_array();
		} 
		
		$this->_output_handle('json',true,$offer_data);
		
	}

	/**
	 * function to process http request get and post supported
	 *
	 * @param $type
	 * @param $url
	 * @param $params
	 */
	function request_http($type = 'GET', $url, $params = array()) {
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		if ($type == 'POST') {
			curl_setopt ( $ch, CURLOPT_POST, true );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $params );
		}
		$output = curl_exec ( $ch );
		$info = curl_getinfo ( $ch );
		curl_close ( $ch );
		return $output;
	}
	
	
	
	/**
	 * function for get the vendor realtime stock
	 */
	function _get_vendor_realtime_stock($item_id)
	{
		//$this->_is_validauthkey();
		
		if(!$item_id)
			return false;
		
		$prd_list=array();
		$item_detail=array($item_id=>array());
		
		//get vendor
		$sql="select vp.* from m_product_deal_link pd
					join m_vendor_product_link vp on vp.product_id=pd.product_id
					where pd.itemid=?";
		
		$vendor_det_res=$this->db->query($sql,$item_id);
		
		if($vendor_det_res->num_rows())
		{
			$vendor_details=$vendor_det_res->result_array();
			
			$vendor_id=$vendor_details[0]['vendor_id'];
			$vendor_grp_no=$vendor_details[0]['vendor_group_no'];
			
			foreach($vendor_details as $vd)
				$prd_list[]=$vd['product_id'];
			
			//get vendor product current status api
			$vendor_prd_crnt_status_api_det_res=$this->db->query("select api_link from m_vendor_api_resources where vendor_id=? and module_type='product_current_status'",$vendor_id);
			
			if($vendor_prd_crnt_status_api_det_res->num_rows())
			{
				$vendor_prd_crnt_status_api_det=$vendor_prd_crnt_status_api_det_res->row()->api_link;
				
				switch($vendor_id)
				{
					case 225://zovi vendor
					{
						$vendor_prd_crnt_status_api_det=str_ireplace('|?|',$vendor_grp_no, $vendor_prd_crnt_status_api_det);
									$prd_det_pag=$this->request_http('GET',$vendor_prd_crnt_status_api_det);
						$prd_det=json_decode($prd_det_pag);
						
						if($prd_det)
						{
							//get product size attr values
							
							$prd_size_attr_det_res=$this->db->query("select * from m_product_attributes where pid in (".implode(",",$prd_list).") and attr_id=1;");
							
							if($prd_size_attr_det_res->num_rows())
							{
								$prd_size_attr_det=$prd_size_attr_det_res->result_array();
								
								$prd_det=$prd_det->$vendor_grp_no;
								
								//get size details
								$prd_det_size_det=$prd_det->sizing_info;
								
								foreach($prd_size_attr_det as $pa)
								{
									if(!isset($item_detail[$item_id][$pa['pid']]))
											$item_detail[$item_id][$pa['pid']]=array();
									
									$prd_status_det=array('avb'=>0,'sku'=>'','size'=>$pa['attr_value'],'avb_qty'=>0);
									$pa['attr_value']=strtoupper($pa['attr_value']);
									
									if(isset($prd_det_size_det->$pa['attr_value']))
									{
										$status_det=$prd_det_size_det->$pa['attr_value'];
										$prd_status_det['avb']=($status_det->avl)?$status_det->avl:0;
										$prd_status_det['sku']=($status_det->sku)?$status_det->sku:0;
										
										//check vendor have available qty api
										$vendor_prd_avl_qty_api_det_res=$this->db->query("select api_link from m_vendor_api_resources where vendor_id=? and module_type='product_available_qty'",$vendor_id);
										
										if($vendor_prd_avl_qty_api_det_res->num_rows())
										{
											$vendor_prd_avl_qty_api_det=$vendor_prd_avl_qty_api_det_res->row()->api_link;
											
											$vendor_prd_avl_qty_api_det=str_ireplace('|?|',$prd_status_det['sku'], $vendor_prd_avl_qty_api_det);
														$vendor_prd_avl_qty_det_pg=$this->request_http('GET',$vendor_prd_avl_qty_api_det);
											$vendor_prd_avl_qty_det=json_decode($vendor_prd_avl_qty_det_pg);
											
											if($vendor_prd_avl_qty_det)
												$prd_status_det['avb_qty']=($vendor_prd_avl_qty_det->$prd_status_det['sku']->total_available_quantity)?$vendor_prd_avl_qty_det->$prd_status_det['sku']->total_available_quantity:0;
							
										}
									}
									
									array_push($item_detail[$item_id][$pa['pid']],$prd_status_det);
								}
								
								if(!empty($item_detail[$item_id]))
								{
												$d_pids = array();
												$user=$this->apim->get_api_user();
									foreach($item_detail[$item_id] as $pid=>$det)
									{
													// update current vendor stock 
													$this->db->query("update m_vendor_product_link set ven_stock_qty=?,modified_on=? where product_id=? limit 1 ",array($det[0]['avb_qty'],cur_datetime(),$pid));
													$d_pids[] = $pid;
													$p_src_stat = (($det[0]['avb_qty']*1)?1:0);
													$this->db->query("update m_product_info set sku_code = ?, is_sourceable = ? where product_id = ? and is_sourceable != ? limit 1 ",array($det[0]['sku'],$p_src_stat,$pid,$p_src_stat));
													if($this->db->affected_rows())
													{
														$t_inp=array("product_id"=>$pid,"is_sourceable"=>$p_src_stat,"created_on"=>time(),"created_by"=>$user['userid']);
														$this->db->insert("products_src_changelog",$t_inp);
													}
									}
									
												if(count($d_pids))
												{
													// check if deal is sourceable
													$this->erpm->_upd_product_deal_statusbyproduct($d_pids[0],$user['userid']);
												}
									return true;
								}	
							}
						}
					}
					break;
				}
			}
		}
		return false;
	}
	
}
