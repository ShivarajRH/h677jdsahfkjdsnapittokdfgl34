<?php
session_start();
include APPPATH.'/controllers/api.php';
Class Sms_api extends Api{

	protected  $logid=0;
	protected $fid=0;
	private $is_executive = 0;
	private $output_type=0; // 0:Normal output ,1:Formatted output
	private $is_franchise=0;
	private $franemp_auth=0;
	
	function __construct_123()
	{
		parent::__construct();

		if(empty($_POST))
			$_POST=$_GET;
		$this->load->model("erpmodel","erpm");
		$this->load->model("reservation_model","reservations");
		$this->load->model("api_model","apim");
		$this->load->model("franchise_model");
		$this->load->model("member_model");

		$this->output_type=$output_type;
	}

	function pdie($msg,$reply=1)
	{

		if($msg && $reply)
			echo $msg;

		$ob=ob_get_contents();

		$this->db->query("insert into pnh_sms_log(msg,franchise_id,reply_for,created_on) values(?,?,?,?)",array($ob,$this->fid,$this->logid,time()));

		ob_flush();

		die;
	}

	
	/**
	 * function to process formatted sms api requests
	 *
	 * @param unknown_type $from
	 * @param unknown_type $msg
	 */
	function process_formatted_sms($from,$msg,$fran)
	{	
		$msg = strtolower($msg);

		// identify message type by spliting
		$msg = str_replace('fmt:','',$msg);
		$msg_parts=explode(' ',$msg);
		$type = $msg_parts[0];
		$msg = trim(str_replace($type,'',$msg));

		switch($type)
		{
			case 'lg' : $this->_fmt_login($from,$msg,$fran);break;
			
			case 'pr' : $this->_fmt_product_det($from,$msg,$fran);break;
			
			case 'mi' : $this->_fmt_member_info($from,$msg,$fran);break;
			
			case 'mr' : $this->_fmt_member_reg($from,$msg,$fran);break;
			
			case 'cc' : $this->_fmt_check_cart($from,$msg_parts);break;
			
			case 'ord' : $this->_fmt_create_order($from,$msg_parts);break;
			
			case 'rotp' : $this->_fmt_request_otp($from,$msg,$fran);break;
			
			case 'votp' : $this->_fmt_validate_otp($from,$msg,$fran);break;
			
			case 'preq' : $this->_fmt_request_product();break;
			
			default : $this->pdie("Invalid Type Requested");
		}

	}
	
	/**
	 * function to Validate Auth Key
	 *
	 * @param unknown_type $key
	 */
	function _validate_authkey($key)
	{
		return $this->apim->get_authkey($key,$key);
	}

	/**
	 * funtion to compute pdie error message for sms response
	 */
	function pdie_error($err)
	{
		$msg = array();
		$msg[] = $err['type'];
		$msg[] = $err['tkn_no'];
		$msg[] = 'ER';
		$msg[] = $err['msg'];
		$msg[] = $err['code'];
		$msg[] = $err['wrap'];
		$this->pdie(implode('||',$msg));
	}

	/**
	 * function to check for valid login details 
	 * @param unknown_type $from
	 * @param unknown_type $msg
	 * @param unknown_type $fran
	 */
	function _fmt_login($from,$msg,$fran)
	{
		//FMT:LG 123 9740793973 12313
		$msg_parts=explode(" ",$msg);
		$tokn=@$msg_parts[0];
		$tokn=$tokn?$tokn:0;
		$username = @$msg_parts[1];
		$password = @$msg_parts[2];
		$msg_size=sizeof($msg_parts);

		$r_msg = array();
		
		if($msg_size!=3)
		{
			$r_msg[] = 'LG';
			$r_msg[] = $tokn;
			$r_msg[] = 'ER';
			$r_msg[] = 'Invalid Format';
			$r_msg[] = 200;
			$r_msg[] = 'LOGIN';
			$this->pdie(implode('||',$r_msg),1);
		}
		if(strlen($from)<10)
		{
			$r_msg[] = 'LG';
			$r_msg[] = $tokn;
			$r_msg[] = 'ER';
			$r_msg[] = 'Invalid mobile number';
			$r_msg[] = 200;
			$r_msg[] = 'LOGIN';
			$this->pdie(implode('||',$r_msg),1);
		}
		$username = @$msg_parts[1];
		$password = @$msg_parts[2];
		$userdet = $this->apim->is_valid_login($username,$password);
			
	
		if($userdet)
		{
			$franchise_id=$userdet['franchise_id'];
			// generate authkey
			$authkey = $this->apim->gen_authkey($userdet['user_id']);
		
			if($franchise_id)
			{
				$fran_det = $this->franchise_model->get_franchise_details($franchise_id);
				$resp_det = array('authkey'=>$authkey,'user_id'=>$userdet['user_id'],'emp_id'=>$empid,'franchise_name'=>$fran_det['franchise_name'],'franchise_mobno'=>$fran_det['login_mobile1'],'franchise_id'=>$franchise_id);
				$r_msg[] = 'LG';
				$r_msg[] = $tokn;
				$r_msg[] = 'OK';
				$r_msg[] = $authkey;
				$r_msg[] = $fran_det['franchise_id'];
				$r_msg[] = $fran_det['franchise_name'];
				$r_msg[] = $fran_det['login_mobile1'];
				$r_msg[] = 'LOGIN';
				//LG||1236||a9cc6694dc40736d7a2ec018ea566113||465||Bharath Enterprises-CKM||9448854623||LG
			}
		}else
		{
			$r_msg[] = 'LG';
			$r_msg[] = $tokn;
			$r_msg[] = 'ER';
			$r_msg[] = 'Please enter valid username and password';
			$r_msg[] = 500;
			$r_msg[] = 'LOGIN';
		//	LG||0||ER||Please enter valid username and password||500||LG
		}
		$this->pdie(implode('||',$r_msg),1);
	}
	
	/**
	 * function  for product enquiry
	 * @param unknown_type $from
	 * @param unknown_type $msg
	 * @param unknown_type $fran
	 */
	function _fmt_product_det($from,$msg,$fran)
	{
		
		//FMT:PR 123 AUTHKEY 123456 
		$msg_parts=explode(" ",$msg);
		$tokn = @$msg_parts[0];
		$tokn = $tokn?$tokn:0;
		$authkey = @$msg_parts[1];
		$pid = @$msg_parts[2];
		$fid = $fran['franchise_id'];
		
		$f_msg=array();
		if(sizeof($msg_parts)!=3)
		{
			//PR||123||ER||ERROR_MESSAGE||ERROR_CODE||PR
			$f_msg[]='PR';
			$f_msg[]=$tokn;
			$f_msg[]='ER';
			$f_msg[]='Invalid Format';
			$f_msg[]=500;
			$f_msg[]='PRODUCT';
			
			$this->pdie(implode('||',$f_msg),1);
		}else 
		{
			if(!$this->apim->get_authkey($authkey,$authkey))
			{
				$f_msg[]='PR';
				$f_msg[]=$tokn;
				$f_msg[]='ER';
				$f_msg[]='Invalid Authkey';
				$f_msg[]=200;
				$f_msg[]='PRODUCT';
				
				$this->pdie(implode('||',$f_msg),1);
			}
			
			$is_valid_pid=$this->db->query("select 1 as valid from king_dealitems where pnh_id=?",$pid)->row()->valid;
			
			if(empty($is_valid_pid))
			{
				
				$f_msg[]='PR';
				$f_msg[]=$tokn;
				$f_msg[]='ER';
				$f_msg[]='Invalid ProductID Entered';
				$f_msg[]=500;
				$f_msg[]='PRODUCT';
				$this->pdie(implode('||',$f_msg),1);
			}
			else 
			{
					$deal=$this->db->query("select group_concat(distinct p.product_id) as product_id,d.menuid,b.name as brand,c.name as cat,i.id,i.is_combo,i.pnh_id as pid,i.live,i.orgprice as mrp,i.price,i.member_price,i.name,i.pic,d.publish,p.is_sourceable,i.has_insurance,CONCAT(print_name,'-',pnh_id) AS print_name,group_id from king_dealitems i join king_deals d on d.dealid=i.dealid  left join king_brands b on b.id = d.brandid join king_categories c on c.id = d.catid LEFT JOIN `m_product_deal_link` l ON l.itemid=i.id LEFT JOIN m_product_info p ON p.product_id=l.product_id LEFT JOIN m_product_group_deal_link gl ON gl.itemid=i.id where pnh_id=? and is_pnh=1",$pid)->row_array();
						
					$productid=$deal['product_id'];
					
					$itemid=$deal['id'];
					
					$price_type=$this->erpm->get_fran_pricetype($fid);
					
					$mpricetype='';
//					if($price_type==1)
//					{
					$member_price_det=$this->erpm->get_memberprice($deal['id'],$fid);
					if($member_price_det['status'] == 'success')
					{
						if($member_price_det['price_type']==1)
						{
							$deal['price']=$member_price_det['price'];
							$price_type='MP';
							$mpricetype='Member Price';
						}
						else
						{
							$deal['price']=$member_price_det['price'];
							$price_type='DP/OP';
							$mpricetype='Offer Price';
						}
					}
//					}
//					else
//					{
//						$deal['price']=$deal['price'];
//						$price_type='DP/OP';
//						$mpricetype='Offer Price';
//					}
					
					$product_size_stkdet='';
					
					$avail_stat= ' ';
					
					$avail=$this->erpm->do_stock_check(array($deal['id']),array(1),true);
					
					if($avail[$deal['id']][0]['status']==0 && $avail[$deal['id']][0]['stk']==0)
						$avail_stat= 'Sold Out';
					else
						$avail_stat= 'Available';
					
					if($deal['group_id']!=null || $deal['group_id']!=0 && !empty($avail))
					{
						$product_size_stkdet='';
					
						$avail_stat ='Availability:';
					
						$sql="SELECT pg.group_name,p.product_id,REPLACE(p.product_name,pg.group_name,'') as product_name,gpl.qty,p.is_sourceable AS src
						FROM m_product_group_deal_link gpl
						JOIN products_group_pids g ON g.group_id = gpl.group_id
						JOIN m_product_info p ON p.product_id = g.product_id
						JOIN products_group pg ON pg.group_id=g.group_id
						WHERE itemid = $itemid ";
					
					}else
					{
						//size availability for deals which are not group deal(zovi/fashionera)
					
						$sql="SELECT p.is_sourceable as src,v.product_id,v.ven_stock_qty,REPLACE(p.product_name,i.name,'') AS product_name
						FROM m_vendor_product_link v
						JOIN m_product_info p ON p.product_id=v.product_id
						JOIN m_product_deal_link l ON l.product_id=p.product_id
						JOIN king_dealitems i ON i.id=l.itemid
						WHERE v.product_id IN($productid)
						GROUP BY v.product_id";
					}
					$group_prods=$this->db->query($sql);
					if($group_prods)
					{
						foreach($group_prods->result_array() as $gp)
						{
	
							$is_src=$gp['src']==1?'Yes':'No';
							$product_size_stkdet.=str_ireplace('-Size :','',$gp['product_name']).'-'.$is_src.',';
	
						}
					}
					$product_size_stkdet = !$product_size_stkdet?0:$product_size_stkdet;
					$margin=$this->erpm->get_pnh_margin($fid,$pid);
					
					if($deal['is_combo']=="1")
						$discount=$deal['price']/100*$margin['combo_margin'];
					else
						$discount=$deal['price']/100*$margin['margin'];
					
					$lcost=round($deal['price']-$discount,2);
					$mrp=$deal['mrp'];
					//$cost="$price_type:Rs ".$deal['price'];
					$cost = $deal['price'];
					//PR||123||OK||PID||MRP||PRICE||LANDING||STOCK||ATTRS||PR
					$f_msg[]='PR';
					$f_msg[]=$tokn;
					$f_msg[]='OK';
					$f_msg[]=$pid;
					$f_msg[]=$mrp;
					$f_msg[]=$cost;
					$f_msg[]=$lcost;
					$f_msg[]=$avail_stat;
					$f_msg[]=$product_size_stkdet;
					$f_msg[]='PRODUCT';
					
					$this->pdie(implode('||',$f_msg),1);
					//PR||123569||OK||12171717||2399||DP/OP:Rs 2399||2399||Available||0||PR
			}
		}
			
	}
	/**
	 * function to get member details
	 * @param unknown_type $from
	 * @param unknown_type $msg
	 * @param unknown_type $fran
	 */

	function _fmt_member_info($from,$msg,$fran)
	{
		//FMT:MI 123 AUTHKEY MEM_ID/MEM_MOBNO
		$msg_parts=explode(" ",$msg);
	
		$tokn = @$msg_parts[0];
		$tokn = $tokn?$tokn:0;
		$authkey = @$msg_parts[1];
		$membr_mobno = @$msg_parts[2];
		$fid = $fran['franchise_id'];
		$f_msg=array();
		
		if(sizeof($msg_parts)!=3)
		{
			$f_msg[]='MI';
			$f_msg[]=$tokn;
			$f_msg[]='ER';
			$f_msg[]='Invalid Format';
			$f_msg[]=500;
			$f_msg[]='MEMBER';
			
			$this->pdie(implode('||',$f_msg),1);
		}
		else
		{

			if(!$this->apim->get_authkey('XXXXX',$authkey))
			{
				$f_msg[]='MI';
				$f_msg[]=$tokn;
				$f_msg[]='ER';
				$f_msg[]='Invalid Authkey';
				$f_msg[]=200;
				$f_msg[]='MEMBER';
			
				$this->pdie(implode('||',$f_msg),1);
			}
			else 
			{
				$is_membr_regd=$this->db->query("select *,date(from_unixtime(created_on)) as reg_on from pnh_member_info where mobile=? or pnh_member_id=? limit 1",array($membr_mobno,$membr_mobno))->row_array();
				
				if($is_membr_regd)
				{
					$membr_id = $is_membr_regd['pnh_member_id'];
					$mbr_name=$is_membr_regd['first_name'].''.$is_membr_regd['last_name'];
					$membr_mobno = $is_membr_regd['mobile'];
					$membr_points = $is_membr_regd['points'];
					$reg_on=date("d M Y",$is_membr_regd['created_on']);
					
					//MI||123||OK||MID||MEM_NAME||MEM_MOBNO||POINTS||MI
					$f_msg[]='MI';
					$f_msg[]=$tokn;
					$f_msg[]='OK';
					$f_msg[]= $membr_id;
					$f_msg[]= $mbr_name;
					$f_msg[]= $membr_mobno;
					$f_msg[]=$membr_points;
					$f_msg[]='MEMBER';
					
					$this->pdie(implode('||',$f_msg),1);
					//MI||123569||OK||22001082||Roopa||9902505821||460||MI
				}
				else
				{
					$f_msg[]='MI';
					$f_msg[]=$tokn;
					$f_msg[]='ER';
					$f_msg[]='No,not Registered';
					$f_msg[]=200;
					$f_msg[]='MEMBER';
					
					$this->pdie(implode('||',$f_msg),1);
					//MI||123569||ER||No,not Registered||200||MI
				}
			}
				
		}
	}
	/**
	 * function to register member
	 * @param unknown_type $from
	 * @param unknown_type $msg
	 * @param unknown_type $fran
	 */
	function _fmt_member_reg($from,$msg,$fran)
	{
		
		//FMT:MR 123 AUTHKEY MEM_MOBNO MEM_NAME
		$msg_parts=explode(" ",$msg);
		
		$tokn = @$msg_parts[0];
		$tokn = $tokn?$tokn:0;
		$authkey = @$msg_parts[1];
		$membr_mobno = @$msg_parts[2];
		$membr_name = @$msg_parts[3] ;
		$fid = $fran['franchise_id'];
		$f_msg=array();
		if(sizeof($msg_parts)!=4)
		{
			$f_msg[]='MR';
			$f_msg[]=$tokn;
			$f_msg[]='ER';
			$f_msg[]='Invalid Format';
			$f_msg[]=500;
			$f_msg[]='MEMBER';
				
			$this->pdie(implode('||',$f_msg),1);
		}
		if(strlen($membr_mobno)!=10)
		{
			$f_msg[]='MR';
			$f_msg[]=$tokn;
			$f_msg[]='ER';
			$f_msg[]='Invalid Member Mobileno';
			$f_msg[]=500;
			$f_msg[]='MEMBER';
				
			$this->pdie(implode('||',$f_msg),1);
		}
		else
		{
			if(!$this->apim->get_authkey('XXXXX',$authkey))
			{
				$f_msg[]='MR';
				$f_msg[]=$tokn;
				$f_msg[]='ER';
				$f_msg[]='Invalid Authkey';
				$f_msg[]=200;
				$f_msg[]='MEMBER';
					
				$this->pdie(implode('||',$f_msg),1);
			}
			else
			{
				$is_membr_mobno_unique=$this->db->query("select * from pnh_member_info where mobile like ?","%$membr_mobno%");
				
				if($is_membr_mobno_unique->num_rows()!=0)
				{
					$f_msg[]='MR';
					$f_msg[]=$tokn;
					$f_msg[]='ER';
					$f_msg[]='Member Already Registered';
					$f_msg[]=200;
						$f_msg[]='MEMBER';
					
					$this->pdie(implode('||',$f_msg),1);
					}
					
					$membr_id=$this->erpm->_gen_uniquememberid();
					if($this->db->query("select 1 from pnh_member_info where pnh_member_id=?",$membr_id)->num_rows()==0)
					{
						$this->db->query("insert into king_users(name,is_pnh,createdon) values(?,1,?)",array("PNH Member: $membr_id",time()));
						$userid=$this->db->insert_id();
						$inp_data=array();
						$inp_data['pnh_member_id']=$membr_id;
						$inp_data['mobile']=$membr_mobno;
						$inp_data['franchise_id']=$fran['franchise_id'];
						$inp_data['first_name']=$membr_name;
						$inp_data['user_id']=$userid;
						$inp_data['created_on']=time();
						$this->db->insert('pnh_member_info',$inp_data);
						if($this->db->affected_rows()>=1)
						{
							//==================< Member REGISTER SMS >====================
							$fran_type = $this->erpm->fran_menu_type($fran['franchise_id']);
							if($fran_type['menu_type'] == 'electonics')
							{
								//order menu having electronics
								$mem_msg = "Hi $membr_name, Welcome to StoreKing - Hurry up!! Get Free Insurance on the 1st Electronic* product you buy. Your Member ID is $membr_id. Offer Valid only after Registration fee Of Rs ".PNH_MEMBER_FEE."/- is paid to Storeking Franchisee.";
							}
							else
							{
								$mem_msg = "Hi $membr_name,Welcome to StoreKing - Hurry up!! Get Free Talk Time worth Rs.".PNH_MEMBER_FREE_RECHARGE." on your 1st purchase above Rs ".MEM_MIN_ORDER_VAL.".Don't forget your Member ID is $membr_id.Offer Valid only after Registration fee Of Rs ".PNH_MEMBER_FEE."/- is paid to Storeking Franchisee.";
							}
							$this->erpm->pnh_sendsms($membr_mobno,$mem_msg,$fran['franchise_id'],$membr_id,0);
							
							//MR||123||OK||MID||MEM_NAME||MEM_MOBNO||POINTS||MR
							
							$f_msg[]='MR';
							$f_msg[]=$tokn;
							$f_msg[]='OK';
							$f_msg[]=$membr_id;
							$f_msg[]=$membr_name;
							$f_msg[]=$membr_mobno;
							$f_msg[]=0;
							$f_msg[]='MEMBER';
							
							$this->pdie(implode('||',$f_msg),1);
						}
					}
				
			}
		}
	}
	/**
	 * function to request otp
	 * @param unknown_type $from
	 * @param unknown_type $msg
	 * @param unknown_type $fran
	 */
	function _fmt_request_otp($from,$msg,$fran)
	{
		//FMT:ROTP 123 AUTHKEY 
		$msg_parts=explode(" ",$msg);
		$tokn = @$msg_parts[0];
		$tokn = $tokn?$tokn:0;
		$authkey = @$msg_parts[1];
		$f_msg=array();

		if(sizeof($msg_parts)!=2)
		{
			$f_msg[]='ROTP';
			$f_msg[]=$tokn;
			$f_msg[]='ER';
			$f_msg[]='Invalid Format';
			$f_msg[]=200;
			$f_msg[]='OTP';
		}
		else 
		{
			if(!$this->apim->get_authkey('XXXXX',$authkey))
			{
				$f_msg[]='ROTP';
				$f_msg[]=$tokn;
				$f_msg[]='ER';
				$f_msg[]='Invalid Authkey';
				$f_msg[]=200;
				$f_msg[]='OTP';
					
				$this->pdie(implode('||',$f_msg),1);
			}
			else 
			{
				$fid=$fran['franchise_id'];
				$ins_data=array();
				$otp_number=substr(number_format(time() * mt_rand(),0,'',''),0,6);
				$valid_till=time()+OTP_VALIDITY;
				$ins_data['valid_till']=$valid_till;
				$ins_data['fid']=$fid;
				$ins_data['otp_no']=$otp_number;
				$ins_data['logged_on']=time();
				$valid_till=format_datetime_ts($valid_till);
				$this->db->insert('t_franchise_otp',$ins_data);

				//ROTP||123||OK||OTP_VAL||EXPIRY_TIMESTAMP||ROTP
				
				$f_msg[]='ROTP';
				$f_msg[]=$tokn;
				$f_msg[]='OK';
				$f_msg[]=$otp_number;
				$f_msg[]=$valid_till;
				$f_msg[]='OTP';
				
				$this->pdie(implode('||',$f_msg),1);
			}
		}
	}
	
	function _fmt_validate_otp($from,$msg,$fran)
	{
		//FMT:VOTP 123 AUTHKEY OTP_VALUE
		
		$msg_parts=explode(" ",$msg);
		$tokn = @$msg_parts[0];
		$tokn = $tokn?$tokn:0;
		$authkey = @$msg_parts[1];
		$otp_val = @$msg_parts[2];
		$f_msg = array();
		$fid=$fran['franchise_id'];
		if(sizeof($msg_parts)!=3)
		{
			$f_msg[]='VOTP';
			$f_msg[]=$tokn;
			$f_msg[]='ER';
			$f_msg[]='Invalid Format';
			$f_msg[]=200;
			$f_msg[]='OTP';
			
			$this->pdie(implode('||',$f_msg),1);
		}
		else
		{
			if(!$this->apim->get_authkey('XXXXX',$authkey))
			{
				$f_msg[]='VOTP';
				$f_msg[]=$tokn;
				$f_msg[]='ER';
				$f_msg[]='Invalid Authkey';
				$f_msg[]=200;
				$f_msg[]='OTP';
					
				$this->pdie(implode('||',$f_msg),1);
			}
			else 
			{
				$is_valid_otp=$this->db->query("select * from t_franchise_otp where fid=? and otp_no=? and valid_till >= ? order by id desc limit 1",array($fid,$otp_val,time()));
			
				if($is_valid_otp->num_rows())
				{
					$is_valid_otp=$is_valid_otp->row_array();
					$f_msg[]='VOTP';
					$f_msg[]=$tokn;
					$f_msg[]='OK';
					$f_msg[]=$otp_val;
					$f_msg[]=format_datetime_ts($is_valid_otp['valid_till']);
					$f_msg[]='OTP';
					$this->pdie(implode('||',$f_msg),1);
				}
				else 
				{
					//VOTP||123||ER||ERROR_MESSAGE||ERROR_CODE||VOTP
					$f_msg[]='VOTP';
					$f_msg[]=$tokn;
					$f_msg[]='ER';
					$f_msg[]='Invalid OTP number/OTP number is Expired';
					$f_msg[]=500;
					$f_msg[]='OTP';
					
					$this->pdie(implode('||',$f_msg),1);
				}
			}
				
		}
	}

	/**
	 * Check cart items for sms api
	 */
	function _fmt_check_cart($from,$msg_parts)
	{
		
		$msg_parts_index = array('type','tkn_no','authkey','pid_list','mem_refno','fran_refno','mem_points','promo_code');
		//$msg_parts = explode(' ','CC 123 123123123 1316968*1,10017215*1 9740793973 9740793973 0 0');

		$ttl_parts = count($msg_parts);

		$tkn_no = isset($msg_parts[1])?$msg_parts[1]:0;

		// Check if template is fine.
		if($ttl_parts != 8)
			$this->pdie("CC||$tkn_no||ER||INVALID SMS TEMPLATE||20001||CART");

		$sms_cont = array();
		foreach($msg_parts_index as $k=>$v)
			$sms_cont[$v]=$msg_parts[$k];

		$auth_key = isset($msg_parts[2])?$msg_parts[2]:'';

		// Check if Valid Auth key
		if(!$this->_validate_authkey($auth_key))
			$this->pdie("CC||$tkn_no||ER||INVALID AUTH KEY||20002||CART");

		$cart_pid_text = $sms_cont['pid_list'];
		$cart_pid_arr  = explode(',',$cart_pid_text);

		$pids = array();
		$pqtys = array();
		foreach ($cart_pid_arr as $pid_det)
		{
			$pid_det_arr = explode('*',$pid_det);
			$pids[] = $pid_det_arr[0];
			$pqtys[] = $pid_det_arr[1];
		}

		$promo_code = $sms_cont['promo_code'];

		$fran_refno = $sms_cont['fran_refno'];
		$mem_refno = $sms_cont['mem_refno'];

		$op = array();

		if(!count($pids))
			$this->pdie_error(array('type'=>'CC','tkn_no'=>$tkn_no,'msg'=>'Please choose atleast once product','code'=>'20003','wrap'=>'CART'));

		if(!($fdet = $this->franchise_model->get_franchise_details($fran_refno)))
			$this->pdie_error(array('type'=>'CC','tkn_no'=>$tkn_no,'msg'=>'Invalid Franchise Mobileno','code'=>'20004','wrap'=>'CART'));

		$fid = (INT)$fdet['franchise_id'];
		$price_type = $fdet['price_type'];
		$fran_mobno = $fdet['login_mobile1'];

		// registere new member on confirmation.
		if(!$this->member_model->is_mem_reg($mem_refno))
			$this->pdie_error(array('type'=>'CC','tkn_no'=>$tkn_no,'msg'=>$mem_refno.' is not registered','code'=>'20005','wrap'=>'CART'));

		$member_det = $this->member_model->get_member_details($mem_refno);
		$mem_mobno = $member_det['mobile'];
		$mem_id = $member_det['pnh_member_id'];

		// check if member is registered.
		$op['franchise_id'] = $fid;
		$op['franchise_name'] = $fdet['franchise_name'];

		$fran_crdet = $this->erpm->get_fran_availcreditlimit($fid);
		$op['franchise_balance'] = $fran_crdet[3];

		$op['member_id'] = 0;
		$op['member_name'] = '';
		$op['member_mobno'] = 0;
		$op['fran_mobno'] = $fran_mobno;

		$op['member_id'] = 0;
		$op['is_new_mem'] = 0;
		$op['is_first_order'] = 0;
		$op['member_points'] = 0;
		// check for stock and insurance details for the products if applicable and associated with the member
		// check if member is registered
		$member_id = $this->member_model->is_mem_reg($mem_mobno);
		if($member_id)
		{
			$member_det = $this->member_model->get_member_details($mem_mobno);
			$op['member_name'] = $member_det['first_name'];
			$op['member_id'] = $member_id;
			$op['is_new_mem'] = $this->member_model->is_new_member($member_id);
			$op['member_points_avail'] = $member_det['points'];
			$op['member_points_redeem'] = $member_det['points'] >= 150?150:0;
			$op['member_id'] = $member_det['pnh_member_id'];
			$op['member_mobno'] = $member_det['mobile'];
			$op['member_dob'] = $member_det['dob']?date('m/d/Y',strtotime($member_det['dob'])):'';
		}

		if($op['is_new_mem'])
			$op['member_fee'] = 50;
		else
			$op['member_fee'] = 0;

		// check for insurance deals

		$ttl_cart_val = 0;
		$ttl_promo_code_prodval = 0;
		$is_valid_promocode = 0;
		if($promo_code)
			$is_valid_promocode = 1;

		$pid_nos = implode(',',$pids);

		$itemdet_res = $this->db->query("select free_frame,menuid,brandid,catid,b.id as itemid,lens_type,a.menuid,b.is_combo,b.pnh_id as pid,orgprice,price,publish,has_insurance,ifnull(insurance_value,0) as inv_val,ifnull(insurance_margin,0) as inv_marg,has_power
											from king_deals a
											join king_dealitems b on a.dealid = b.dealid
											left join pnh_member_insurance_menu c on c.menu_id = a.menuid and b.price between greater_than and less_than and c.is_active = 1
											where pnh_id in (".$pid_nos.")
										");
		$op['items'] = array();
		$op['promo_code_total_disc'] = 0;
		$op['promo_code_status'] = '';
		if($itemdet_res->num_rows())
		{
			foreach($itemdet_res->result_array() as $i=>$itemdet)
			{

				$member_price_det = $this->erpm->get_memberprice($itemdet['itemid'],$fid,$mem_id);
				//print_r($member_price_det);

				if($member_price_det['status'] == 'success')
				{
					$itemdet['price_type']=$member_price_det['price_type'];
					if($member_price_det['price_type'])
					{
						$itemdet['price']=($member_price_det['price']);
						$itemdet['mp_frn_max_qty']=$member_price_det['mp_frn_max_qty'];
						$itemdet['mp_mem_max_qty']=$member_price_det['mp_mem_max_qty'];
						$itemdet['max_order_qty']=$member_price_det['max_ord_qty'];
						$itemdet['logref_id']=$member_price_det['logref_id'];

						$mp_is_loyalty_point=$member_price_det['mp_is_loyalty_point'];
						if($mp_is_loyalty_point == '1') {
							$itemdet['price']= $price = $member_price_det['price'];
						}
					}else
					{
						$itemdet['price']=($member_price_det['price']);
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

					} 
					
					if($itemdet['price'] >= 10000 )
					{
						// compute new insurance amount
						if($op['is_new_mem'])
							$itemdet['mem_insurance_amt'] = $itemdet['inv_val'];
						else
							$itemdet['mem_insurance_amt'] = round($itemdet['price']*$itemdet['inv_marg']/100);
					}
				}

				$itemdet['free_frame'] = $itemdet['free_frame']*1;
				$itemdet['has_power'] = $itemdet['has_power']*1;

				$ttl_cart_val += $itemdet['price'];
				$op['items'][$i] = $itemdet;
			}

			if($promo_code)
			{
				// check if promo code is applicable for deal
				$pc_det_res = $this->db->query("select * from pnh_m_coupons where coupon_code = ? ",$promo_code);
				if($pc_det_res->num_rows())
				{
					$pc_det = $pc_det_res->row_array();
					foreach($op['items'] as $i=>$itemdet)
					{
						$itemdet['promo_code_applied'] = 0;
						$itemdet['promo_code_disc'] = 0;
						$pid = $itemdet['pid'];
						$apply_pc = 0;
						if($pc_det['min_cart_value'])
						{
							if($ttl_cart_val >= $pc_det['min_cart_value'])
							{
								$apply_pc = 1;
							}
						}

						// check if promocode menu matches
						if(($pc_det['menu_id'] = $itemdet['menuid']) || !$pc_det['menu_id'])
							$apply_pc = 1;

						// check if promocode brand matches
						if(($pc_det['brand_id'] = $itemdet['brandid']) || !$pc_det['brand_id'])
							$apply_pc = 1;

						// check if promocode cat matches
						if(($pc_det['cat_id'] = $itemdet['catid']) || !$pc_det['cat_id'])
							$apply_pc = 1;

						// check if promocode pnh_id matches
						if($pc_det['pnh_id'] = $itemdet['pid'])
							$apply_pc = 1;

						if($apply_pc)
						{
							$itemdet['promo_code_disc'] = $pc_det['value_type']?$pc_det['value']*$itemdet['price']:$pc_det['value'];
							$itemdet['promo_code_applied'] = 1;

							$ttl_promo_code_prodval += $itemdet['price'];

						}

						$op['items'][$i] = $itemdet;
					}

					$op['promo_code_total_disc'] = $pc_det['value_type']?$pc_det['value']*$ttl_promo_code_prodval/100:$pc_det['value'];
					$op['promo_code_status'] = 'valid';
				}else
				{
					$op['promo_code_status'] = 'Invalid Promo code entered';
				}
				$op['promo_code'] = $promo_code;
			}
		}

		// =========< Random  cart id for uniquenes >==============
		$rnd_key = random_num(6);  //lcfirst(random_alpha(1)).

		$op['cartid'] = $rnd_key.substr( time(),0,6);
		
		$resp_msg = array();
		$resp_msg[] = 'CC';
		$resp_msg[] = $tkn_no;
		$resp_msg[] = 'OK';
		$resp_msg[] = $op['cartid'];
		$item_list = array();
		foreach($op['items'] as $itm)
		{
			$disc = isset($itm['promo_code_disc'])?$itm['promo_code_disc']:0;
			$item_det = array();
			$item_det[] = $itm['pid'];
			$item_det[] = $itm['max_order_qty'];
			$item_det[] = $itm['orgprice'].'-'.$itm['price'].'-'.$disc;
			$item_det[] = isset($itm['attrs'])?$itm['attrs']:0;
			$item_det[] = $itm['has_insurance'].'-'.$itm['inv_val'];
			
			$item_list[] = implode(':',$item_det);
		}
		$resp_msg[] = implode(',',$item_list);
		$resp_msg[] = $op['member_id'];
		$resp_msg[] = $op['franchise_id'];
		$resp_msg[] = $op['member_points_avail'];
		$resp_msg[] = $op['member_points_redeem'];
		$resp_msg[] = ceil($op['franchise_balance']);
		if(isset($op['promo_code']))
			$resp_msg[] = $op['promo_code'].':'.$op['promo_code_total_disc'];
		else 
			$resp_msg[] = '0';
		$resp_msg[] = 'CART';
		
		$this->pdie(implode('||',$resp_msg));

	}
	
	
	
	/**
	 * function to add new order.
	 */
	function _fmt_create_order($from,$msg_parts)
	{
		
		$msg_parts_index = array('type','tkn_no','authkey','cartid','pid_list','mem_refno','fran_refno','mem_points','promo_code');
		//$msg_parts = explode(' ','ORD 11123 123123123 131311123 111111892*1,10017215*1 9740793973 9740793973 0 0');
		
		$ttl_parts = count($msg_parts);
		
		$tkn_no = isset($msg_parts[1])?$msg_parts[1]:0;
		
		// Check if template is fine.
		if($ttl_parts != 9)
			$this->pdie("ORD||$tkn_no||ER||INVALID SMS TEMPLATE||20001||ORDER");
		
		$sms_cont = array();
		foreach($msg_parts_index as $k=>$v)
			$sms_cont[$v]=$msg_parts[$k];
		
		$auth_key = isset($msg_parts[2])?$msg_parts[2]:'';
		
		// Check if Valid Auth key
		if(!$this->_validate_authkey($auth_key))
			$this->pdie("ORD||$tkn_no||ER||INVALID AUTH KEY||20002||ORDER");
		
		$cart_pid_text = $sms_cont['pid_list'];
		$cart_pid_arr  = explode(',',$cart_pid_text);
		
		$pids = array();
		$pqtys = array();
		foreach ($cart_pid_arr as $pid_det)
		{
			$pid_det_arr = explode('*',$pid_det);
			$pids[] = $pid_det_arr[0];
			$pqtys[] = $pid_det_arr[1];
		}
		
		$promo_code = $sms_cont['promo_code'];
		$fran_refno = $sms_cont['fran_refno'];
		$mem_refno = $sms_cont['mem_refno'];
		$cartid = $sms_cont['cartid'];
		
		$op = array();
		
		if(!count($pids))
			$this->pdie_error(array('type'=>'ORD','tkn_no'=>$tkn_no,'msg'=>'Please choose atleast once product','code'=>'20003','wrap'=>'ORDER'));
		
		if(!($fdet = $this->franchise_model->get_franchise_details($fran_refno)))
			$this->pdie_error(array('type'=>'ORD','tkn_no'=>$tkn_no,'msg'=>'Invalid Franchise Mobileno','code'=>'20004','wrap'=>'ORDER'));
		
		
		// Prepare post params for submittion
		$fran_mobno = $fran_refno;//$this->input->post('f_mobno');
		 
		 
		$insurance = 0;
		$member = 0;
		$offer_type = 0;
		$redeem = 0;
		$attr_pid = 0;
		$attr_pid_val = 0;
		$opt_insurance_pids = array();
		
		$member_det = $this->member_model->get_member_details($mem_refno);
		$mem_mobno = $member_det['mobile'];
		$member_id = $member_det['pnh_member_id'];
		
		
		$has_prescription_pids = false;
		$lens_item_package_pids = false;
	
		// need to update
		//$promo_code = $this->input->post('promo_code');
		$prescription_det = array();
		
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
		
		$resp_det = $this->to_process_order($fid,$member_id,$pid,$qty,$offr_sel_type,$redeem,1,$insurance,$d_attr,$lens_item_det,$cartid);
		 
		if(isset($resp_det['error_code']))
			$this->pdie_error(array('type'=>'ORD','tkn_no'=>$tkn_no,'msg'=>$resp_det['error_msg'],'code'=>'20003','wrap'=>'ORDER'));
		
		//ORD||123||OK||CARTID||TRANSID||AMOUNT||STATUS||FRID||MEMID||MESSAGE||ORDER
		
		$trans_det_res = @$this->db->query("(select a.transid,a.amount,a.franchise_id,pnh_member_id 
												from king_transactions a 
												join king_orders b on a.transid = b.transid 
												join pnh_member_info c on c.user_id = b.userid where a.transid = ? 
											group by transid )
											union
											(
												select a.transid,a.amount,a.franchise_id,pnh_member_id 
													from king_tmp_transactions a 
													join king_tmp_orders b on a.transid = b.transid 
													join pnh_member_info c on c.user_id = b.userid where a.transid = ? 
											group by transid
											)
				 ",array($resp_det['trans'],$resp_det['trans']));
		
		if($trans_det_res->num_rows())
		{
			$trans_det = $trans_det_res->row_array();
		
		$resp_msg = array();
		$resp_msg[] = 'ORD';
		$resp_msg[] = $tkn_no;
		$resp_msg[] = 'OK';
		$resp_msg[] = $cartid;
		$resp_msg[] = $trans_det['transid'];
		$resp_msg[] = $trans_det['amount']*1;
		$resp_msg[] = '1';
		$resp_msg[] = $trans_det['franchise_id'];
		$resp_msg[] = $trans_det['pnh_member_id'];
		$resp_msg[] = $resp_det['message'];
		$resp_msg[] = 'ORDER';
		
		$this->pdie(implode('||',$resp_msg));
			
		}
		
		//$resp = $this->order_model->create_order($order_params);
	
		// post response to via json
		//$this->_output_handle('json',(($resp['status'] != 'error')?true:false),$resp);
	
	}
	

}