<?php

/**
 * Storeking API 
 * @author Madhan
 */

class Api extends Controller
{
	function __construct()
	{
		parent::Controller();
		header('Access-Control-Allow-Origin: *');
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
			$this->form_validation->set_message('_chk_username','Invalid Username entered');
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
	function get_franchise_menus()
	{
		$this->_is_validauthkey();
		
		$userid=$this->input->post('user_id');
		$franchise_id=$this->input->post('franchise_id');
		
		if(!$userid)
			$this->_output_handle('json',false,array('error_code'=>2001,'error_msg'=>"Invalid user"));
		
		$menu_list=array();
		
		$menu=$this->apim->get_menus_by_franchise($franchise_id);
		
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
		$full=$this->input->post('full');
		
		$brand_list=array();
		$full_brands=1;
		$menu_cat=0;
		
		if(!$start)
			$start=0;
		
		if(!$limit)
			$limit=10;
		
		if($menuid && $catid)
		{
			$full_brands=0;
			$menu_cat=1;
			
			$brands_list_det=$this->apim->get_brands_by_menu_cat($menuid,$catid,$start,$limit,$full); 
		}
		
		if(!$menu_cat)
		{
			if($menuid)
			{
				$full_brands=0;
				
				$brands_list_det=$this->apim->get_brands_by_menu($menuid,$start,$limit);
			}
			
			if($catid)
			{
				$full_brands=0;
				
				$brands_list_det=$this->apim->get_brands_by_cat($catid,$start,$limit);
			}
		}
		
		if($full_brands)
			$brands_list_det=$this->apim->get_brands($start,$limit);
		
		
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
		
		$menuid=$this->input->post('menu_id');
		$brandid=$this->input->post('brand_id');
		$start=$this->input->post('start');
		$limit=$this->input->post('limit');
		$userid=$this->input->post('user_id');
		$full=$this->input->post('full');
		
		$brand_list=array();
		$full_cat=1;
		$menu_brand=0;
		
		if(!$start)
			$start=0;
		
		if(!$limit)
			$limit=10;
		
		if($brandid && $menuid)
		{
			$full_cat=0;
			$menu_brand=1;
			
			$category_list_det=$this->apim->get_categories_by_brand_menu($brandid,$menuid,$start,$limit,$full);
		}
		
		
		if(!$menu_brand)
		{
			if($menuid)
			{
				$full_cat=0;
				
				$category_list_det=$this->apim->get_categories_by_menu($menuid,$start,$limit);
			}
			
			
			if($brandid)
			{
				$full_cat=0;
				
				$category_list_det=$this->apim->get_categories_by_brand($brandid,$start,$limit);
			}
		}
		
		if($full_cat)
		{
			$category_list_det=$this->apim->get_categories($start,$limit);
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
		
		$this->load->model('erpmodel','erpm');
		
		$cart_items_det=$this->apim->get_cart_items($fid,$pid);
		
		$cart_items_det=$this->check_cart_items_margin($fid,$cart_items_det);
		
		if($cart_items_det)
			$this->_output_handle('json',true,array("cart_items"=>$cart_items_det));
		else
			$this->_output_handle('json',false,array('error_code'=>2005,'error_msg'=>"No more items in cart"));
		
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
					$cart_item_list[$k]['place_order']=1;
	
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
		
		$member_details=$this->apim->get_member_details($memid);
		
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

			$this->_output_handle('json',true,$update_version_det['verions'][count($update_version_det['verions'])-1]['version']);
		}
	}
	
	
	
}
