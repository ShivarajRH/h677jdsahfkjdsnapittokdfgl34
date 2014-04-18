<?php

class Pnh extends Controller{

	private $logid=0;
	private $fid=0;
	private $is_executive = 0;

	function __construct()
	{
		parent::__construct();
		if(empty($_POST))
			$_POST=$_GET;
		$this->load->model("erpmodel","erpm");
		$this->load->model("reservation_model","reservations");
	}

	function pdie($msg)
	{
		if($msg)
			echo $msg;
		$ob=ob_get_contents();
		
		$this->db->query("insert into pnh_sms_log(msg,franchise_id,reply_for,created_on) values(?,?,?,?)",array($ob,$this->fid,$this->logid,time()));
		ob_flush();
		die;
	}

	function auth($from,$msg,$call='')
	{
		$call = trim($call);
		$this->db->query("insert into pnh_sms_log(msg,sender,created_on) values(?,?,?)",array($msg,$from,time()));
		$this->logid=$logid=$this->db->insert_id();
		$from=substr($from,1);
		$from=trim($from);

		if(empty($from) || strlen($from)<10)
			$this->pdie("Invalid mobile number");

		$fran=$this->db->query("select * from pnh_m_franchise_info where login_mobile1=? or login_mobile2=?",array($from,$from))->row_array();

		if($call == 'bank')
		{
			$fran_ex=$this->db->query("SELECT * FROM m_employee_info WHERE  contact_no LIKE ? ",'%'.$from.'%')->row_array();
		}else
		{
			$fran_ex=$this->db->query("SELECT * FROM m_employee_info WHERE  contact_no LIKE ? AND (job_title>=4 or job_title2 in (6,7))",'%'.$from.'%')->row_array();
		}
		$membr=$this->db->query("select * from pnh_member_info where mobile=?",$from)->row_array();
		// check if both are blank
		if(empty($fran) && empty($fran_ex) && empty($membr))
		{
			$this->member_register($from,$msg);
			exit;
		}
		
		if(!empty($fran) && !empty($fran_ex))
			$this->pdie("Sorry, franchise and pnh employee has same mobile nos.");

		if(!empty($fran))
		{
			if($fran['is_suspended']==1)
				$this->pdie("Your account is suspended. Please contact customer support");

			$this->fid=$fran['franchise_id'];
			$this->db->query("update pnh_sms_log set franchise_id=? where id=? limit 1",array($fran['franchise_id'],$logid));
			return $fran;
		}else if(!empty($fran_ex))
		{
			$this->is_executive = 1;
			return $fran_ex;
		}


	}

	function test($a,$b)
	{
		$_POST['From'] = $a;
		$_POST['Body'] = $b;
		$this->process();
	}

	function process()
	{

		ob_start();
			
		$msg=$this->input->post("Body");
		$from=$this->input->post("From");
		
		$msg = preg_replace('/\s+/',' ',trim($msg));
		
		list($call)=explode(" ",$msg);
		$call=strtolower($call);

		$fran=$this->auth($from, $msg,$call);

		$from=substr($from,1);

		if($this->is_executive)
		{
			if($call == 'paid' || $call == 'p' || $call == 'ship' || $call == 'new' || $call == 'existing' || $call == 't'|| $call == 'd'|| $call == 'r' || $call == 'e'|| $call == 'm' || $call == 'bank' || $call=='lr')
			{
				if($call == 'bank')
					$this->chk_pnh_bank($from,$msg);
				else if($call == 'm')
					$this->manifesto_pickup($from,$msg,$call);
				else if($call == 't')
					$this->task_sms_log($from,$msg,$call);
				else if($call == 'd')
					$this->driver_sms($from,$msg,$call);
				else if($call == 'r')
					$this->returnd_invoice_frm_driver($from,$msg,$call);
				else if($call == 'e')
					$this->invoice_frm_driver_toex($from,$msg,$call);
				else if($call=='lr')
					$this->update_lr_number($from,$msg,$call);
				else if($call=='p')
					$this->price_details($from,$msg,$call);
				else
					$this->executive_sms_log($from,$msg,$call);
			}
			else
				$this->pdie("Invalid Message");

		}else{

			switch($call)
			{
				case 'bal':
					$this->balance($from,$msg,$fran);
					break;
				case 'bank':
					$this->chk_pnh_bank($from,$msg,$fran);
					break;
				case 'sta':
					$this->status($from,$msg,$fran);
					break;
				case 'cnl':
					$this->cancelOrder($from,$msg,$fran);
					break;
				case 'rcn':
					$this->recent($from,$msg,$fran);
					break;
				case 'che':
				case 'chk':
					$this->stock_check($from,$msg,$fran);
					break;
				case 'p':
				case 'price':
					$this->price_details($from,$msg,$fran);
					break;
				case 'call';
				$this->call_sms($from,$msg,$fran);
				break;
				case 'm';
				$this->reg_new_mem($from,$msg,$fran);
				break;
				case 'c';
				$this->fran_alloting_voucher_tomem($from,$msg,$fran);
				break;
				case 'chm';
				$this->is_mbr_regd($from,$msg,$fran);
				break;
                                case 'r':
                                        $this->process_memrating($from,$msg,$fran);
                                        break;
				default :
					$this->createOrder($from,$msg,$fran);
					break;
			}
		}
		$this->pdie(false);
	}

	function recent($from,$msg)
	{
		$fran=$this->db->query("select * from pnh_m_franchise_info where (login_mobile1=? and login_mobile1!=0) or (login_mobile2=? and login_mobile2!=0)",array($from,$from))->row_array();
		$orders=$this->db->query("select t.transid,t.amount,o.status,o.time,o.actiontime from king_transactions t join king_orders o on o.transid=t.transid where t.franchise_id=? group by o.transid order by o.sno desc,o.status desc limit 5",$fran['franchise_id'])->result_array();
		$stat=array("Confirmed","Confirmed","Shipped","Cancelled");
		if(empty($orders))
			$this->pdie("No orders made by you");
		foreach($orders as $o)
			echo "{$o['transid']} Rs {$o['amount']} ".($o['actiontime']==0?date("jS M",$o['time']):date("jS M",$o['actiontime']))." {$stat[$o['status']]}\n";
	}

	function status($from,$msg)
	{
		$inp=explode(" ",$msg);
		if(count($inp)<2)
			$this->pdie("No Order ID provided");
		list($call,$transid)=$inp;
		if(empty($transid))
			$this->pdie("No Order ID provided");
		$order=$this->db->query("select transid,status,actiontime from king_orders where transid=? order by status desc, actiontime desc limit 1",$transid)->row_array();

		if(isset($order['transid']))
		{
			if($order['status']==3)
				$this->pdie("Your order {$order['transid']} was cancelled on ".date("d/m/y",$order['actiontime']));
			else if($order['status']==2)
				$this->pdie("Your order {$order['transid']} was shipped on ".date("d/m/y",$order['actiontime']));
			else if($order['status'] == 0 || $order['status'] == 1)
				$this->pdie("Your order {$order['transid']} is confirmed and is yet to be shipped");
		}else
		{
			$this->pdie("Invalid Orderno $transid entered.");
		}
				
		
		
	}

	function stock_check($from,$msg)
	{
		$a=explode(" ",$msg);
		foreach($a as $i=>$s)
			if($i>0)
			$raw[]=$s;
		if(empty($raw))
			$this->pdie("No Product ID mentioned");
		$ppids=array();
		foreach($raw as $r)
			foreach(explode(",",$r) as $i)
			$ppids[]=$i;
		$ppids=array_unique($ppids);
		$itemids=array();
		foreach($this->db->query("select id,pnh_id from king_dealitems where pnh_id in ('".implode("','",$ppids)."') and is_pnh=1")->result_array() as $i)
			$itemids[$i['pnh_id']]=$i['id'];
		$avail=$this->erpm->do_stock_check($itemids);
		foreach($itemids as $ppid=>$itemid)
		 if(in_array($itemid,$avail))
			echo "$ppid-Y, \n";
		else
			echo "$ppid-N, \n";
	}

	function balance($from,$msg)
	{
		$fran=$this->db->query("select current_balance from pnh_m_franchise_info where (login_mobile1=? and login_mobile1!=0) or (login_mobile2=? and login_mobile2!=0)",array($from,$from))->row_array();
		echo 'Dear Franchise, your current balance is Rs '.number_format($fran['current_balance'],2);
	}

	/**
	 * @author Roopa
	 * SMS functionality to check Price details for Each product - Franchisee derived
	 * @param unknown_type $from
	 * @param unknown_type $msg
	 * @param unknown_type $fran
	 */

	function price_details($from,$msg,$fran)
	{
		

		$fran=@$this->db->query("select franchise_id from pnh_m_franchise_info where (login_mobile1=? and login_mobile1!=0) or (login_mobile2=? and login_mobile2!=0) and is_suspended=0",array($from,$from))->row()->franchise_id;
		if(empty($fran))
			$mid=@$this->db->query("select * from pnh_member_info where mobile=?",$from)->row_array();
		
		
		if(empty($fran) && empty($mid))
			$this->pdie("Not Authorized");
		
		$frags=trim($msg);
		
		$frags=explode(" ",$frags);
		
		if(count($frags)<2)
			$this->pdie("Product ID is not entered. Please check your msg");
		
		$pid=$pnhid=$frags[1];

		$deal=$this->db->query("select d.menuid,b.name as brand,c.name as cat,i.id,i.is_combo,i.pnh_id as pid,i.live,i.orgprice as mrp,i.price,i.name,i.pic,d.publish,p.is_sourceable,i.has_insurance,CONCAT(print_name,'-',pnh_id) AS print_name from king_dealitems i join king_deals d on d.dealid=i.dealid  left join king_brands b on b.id = d.brandid join king_categories c on c.id = d.catid JOIN `m_product_deal_link` l ON l.itemid=i.id JOIN m_product_info p ON p.product_id=l.product_id where pnh_id=? and is_pnh=1",$pid)->row_array();

		$allow_for_fran = $this->db->query("select count(*) as t from pnh_franchise_menu_link where status = 1 and fid = ? and menuid in (select menuid
											from king_dealitems a
											join king_deals b on a.dealid = b.dealid
											where pnh_id = ? )",array($fran,$pid))->row()->t;
		
		
		if(empty($deal))
			$this->pdie("There is no product with entered PID $pid -StoreKing Team");

		if($fran == 'p')
		{
			$discount = 0;
		}
		else if(!empty($fran))
		{
			
			$margin=$this->erpm->get_pnh_margin($fran,$pid);

			if($deal['is_combo']=="1")
				$discount=$deal['price']/100*$margin['combo_margin'];
			else
				$discount=$deal['price']/100*$margin['margin'];
			
			$lcost=round($deal['price']-$discount,2);
		}
		$mrp=$deal['mrp'];

		if($deal['menuid']==112 || $deal['menuid']==116 || $deal['menuid']==118)
			$cost='DP Price:Rs '.$deal['price'];
		else
			$cost='MRP Price:Rs '.$deal['mrp'];

		$name=ucfirst($deal['name']);

		$avail=$this->erpm->do_stock_check(array($deal['id']),array(1),true);
		
		$avail_det = array_values($avail);
		$avail_stat= ' ';
	
			if($deal['is_sourceable']==1)
					$avail_stat = ' Available';
			else 
					$avail_stat = 'Not Available';
				
			if($avail_det[0][0]['stk']==0 && $deal['is_sourceable']==0 &&  $allow_for_fran!=0 && !empty($fran))
				$this->pdie("{$name} - Not Available");
			
			if($avail_det[0][0]['stk']==0 && $deal['is_sourceable']==0 &&  $allow_for_fran==0 && !empty($fran))
				$this->pdie("Thanks for your enquiry !{$name} - Not Available, Please try another product -For more details contact Storeking Toll Free Customer Care no '18002001996'");
		
		if(!empty($fran) && $allow_for_fran!=0 && empty($mid))
		{
			
			$sms_msg= "Thanks for your enquiry !{$name} - {$avail_stat}\n {$cost},Landing Price:Rs {$lcost} -For more details contact Storeking Toll Free Customer Care no '18002001996'";
			echo $sms_msg;
		}
		if(!empty($fran) && $allow_for_fran==0 && empty($mid))
		{
			
			$sms_msg= "Thanks for your enquiry ! -{$name} {$avail_stat}\n {$cost},Landing Price:Rs {$lcost} -For more details contact Storeking Toll Free Customer Care no '18002001996'";
			echo $sms_msg;
		}
		if(!empty($mid) && empty($fran))
		{
			
			$fran=$mid['franchise_id'];
			$sms_msg="Thanks for your enquiry !{$name}-{$avail_stat}\n MRP Price:Rs {$mrp} -StoreKing Team";
			echo $sms_msg;
		}
		$this->erpm->pnh_sendsms($from,$sms_msg,$fran,0,0);

	}



	/**
	 * Process the Product Rating with SMS Feedback
	 */
	function process_memrating($from,$msg,$fran)
	{
		//E.g: r {5}
		$frags=explode(" ",trim($msg) );

		if(count($frags) < 2)
		{
                    $this->pdie("Invalid entry Please enter a number in the following format R&lt;SPACE&gt;[1 to ".MAX_RATE_VAL."]. Ex:R 4");
		}

                $rate_value = $frags[1];

                if(!is_numeric($rate_value))
                    $this->pdie("Invalid entry Please enter a number in the following format R&lt;SPACE&gt;[1-".MAX_RATE_VAL."]. Ex:R 4");
                // if rate value is greater than max_rate_val then take max_rate_val
                if($rate_value > MAX_RATE_VAL)
                    $rate_value = MAX_RATE_VAL;
                
                // if rate value is less than 1 then take 1
                if($rate_value < 1)
                    $this->pdie("Invalid number Please enter a number in the following format R&lt;SPACE&gt;[1-".MAX_RATE_VAL."]. Ex:R 4");
                
                if( $this->db->query("SELECT COUNT(*) AS t FROM pnh_member_info WHERE mobile= ?",$from)->row()->t == 0 )
		{
                    $this->pdie("Unregistered Member, please register with storeking.");
		}

                $mem_set = $this->db->query("SELECT GROUP_CONCAT('\'',mo.sno,'\'') AS snos 
                                                    FROM pnh_member_offers mo
                                                    JOIN pnh_member_info mi ON mi.pnh_member_id = mo.member_id
                                                    WHERE mo.feedback_status=0 AND mo.delivery_status=1 AND mi.mobile=? ",$from);
                if($mem_set->row()->snos == null)
		{
                    $this->pdie("No Offers found or Order is waiting for delivery - StoreKing.");
		}
		else
		{
			//found
			$snos = $mem_set->row()->snos;
                }
                $this->db->query("UPDATE pnh_member_offers SET feedback_status = 1,feedback_value=? WHERE sno IN (".$snos.") ",$rate_value);
                $this->pdie("Thank you for your valuable feedback.");
	}



	/* function createOrder($from,$msg)
	 {

	if(empty($from) || strlen($from)<10)
		$this->pdie("Invalid mobile number");

	//Flag to check order is from user end/franchisee end
	$ordr_frm_usr=0;

	//check if sms is from member
	$is_member=@$this->db->query("select * from pnh_member_info where mobile=?",$from)->row_array();

	if($is_member)
	{
	$mem_name=$is_member['first_name'].''.$is_member['last_name'];
	$mem_mob=$is_member['mobile'];
	$fran_id=$fid=$is_member['franchise_id'];
	$mem_id=$is_member['pnh_member_id'];
	$mem_name=$is_member['first_name'].''.$is_member['last_name'];
	$fran_mob_det=$this->db->query("select login_mobile1,login_mobile2,franchise_name from pnh_m_franchise_info where franchise_id=? and is_suspended=0",$fran_id)->row_array();
	$fran_mobile=$fran_mob_det['login_mobile1'];
	$fran_name=$fran_mob_det['franchise_name'];
	$ordr_frm_usr=1;
	}

	$tmp_var = explode(' ',$msg);

	$tmp_code = @$tmp_var[1];

	// check if tmp code is valid voucher code for creating order by voucher.
	$tmp_code_vc = $this->db->query("select * from pnh_t_voucher_details where voucher_code=?",$tmp_code);


	if($tmp_code_vc->num_rows()!=0 || $tmp_code=='usebal')
	{

	$membr=$this->db->query("select * from pnh_member_info where mobile=?",$from);

	if($membr->num_rows()!=0)
	{

	$this->create_prepaidorder($from,$msg,$membr);
	}
	else{

	$this->pdie("Your not authorized");
	}
	die();
	}
	if($ordr_frm_usr==1 && ($tmp_var[0]!='Y' && $tmp_var[0]!='y' && $tmp_var[0]!='N' && $tmp_var[0]!='n'))
		$from=$fran_mobile;
	$fran=$this->db->query("select *,current_balance as balance from pnh_m_franchise_info where (login_mobile1=? and login_mobile1!=0) or (login_mobile2=? and login_mobile2!=0)",array($from,$from))->row_array();
	$fid=@$fran['franchise_id'];
	if(empty($fran) && $ordr_frm_usr==0)
	{
	$this->pdie("Invalid Voucher Code/Member ID ");
	}

	$margin=@$this->db->query("select margin,combo_margin from pnh_m_class_info where id=?",$fran['class_id'])->row_array();
	if($fran['sch_discount_start']<time() && $fran['sch_discount_end']>time() && $fran['is_sch_enabled'])

		$margin['margin']+=$fran['sch_discount'];
	$msg=str_replace("* ","*",$msg);
	$msg=str_replace(" *","*",$msg);

	$payload=explode(" ",$msg);

	if((empty($payload) || count($payload)==1) && empty($is_member))
		$this->pdie("Invalid syntax");

	$npayload=array();

	foreach($payload as $p)
	{
	if(@$p{0}=="2" ||($p[0]=='Y' || $p[0]=='y' || $p[0]=='N'|| $p[0]=='n') )
		$mid=$p;
	else
		$npayload[]=$p;
	}

	$payload=$npayload;
	$npayload=array();
	foreach($payload as $p)
	{
	if(stripos($p,",")===false)
	{
	$npayload[]=$p;
	continue;
	}
	$npp=explode(",",$p);
	foreach($npp as $np)
		$npayload[]=$np;
	}
	$payload=$npayload;

	if(!isset($mid) && $ordr_frm_usr==0)
	{
	$this->pdie("No Member ID available");
	}
	else
		$mid=@$mem_id;

	$items=array();
	foreach($payload as $p)
	{
	$pi=explode("*",$p);
	$it['pid']=trim($pi[0])+1-1;
	if(count($pi)!=2)
		$it['qty']=1;
	else
		$it['qty']=$pi[1];
	$items[]=$it;
	}
	$total=0;$d_total=0;
	$itemids=array();
	$itemnames=array();
	$menu_ofr_pricevalue=array();
	$ordered_menus_ttl=array();
	$menu_ttlval=array();
	$commision=0;

	$fran_alloted_menu=$this->db->query("SELECT GROUP_CONCAT(menuid) as alloted_menu FROM pnh_franchise_menu_link WHERE STATUS=1 AND fid=?",$fid)->row()->alloted_menu;
	foreach($items as $i=>$item)
	{
	$prod=$this->db->query("select i.*,d.publish,d.menuid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.is_pnh=1 and i.pnh_id=? and i.pnh_id!=0",$item['pid'])->row_array();
	if(empty($prod) && $ordr_frm_usr == 0 && ($tmp_var[0]=='Y' || $tmp_var[0]=='y' || $tmp_var[0]=='N'|| $tmp_var[0]=='n'))
	{
	echo 3;die;
	$this->confirm_usrordr($from,$msg);
	exit;
	}
	if(empty($prod))
		$this->pdie("There is no product with ID : ".$item['pid']);

	$ordered_menus_list[]=$prod['menuid'];

	if($prod['publish']!=1)
		$this->pdie("Product {$prod['name']} is not available,please place order for the available product");
	$items[$i]['mrp']=$prod['orgprice'];
	if($fran['is_lc_store'])
		$items[$i]['price']=$prod['store_price'];
	else
		$items[$i]['price']=$prod['price'];
	$margin=$this->erpm->get_pnh_margin($fran['franchise_id'],$item['pid']);

	//echo '<pre>'.print_r($margin);exit;
	$items[$i]['itemid']=$prod['id'];
	if($prod['is_combo']=="1")
		$items[$i]['discount']=$items[$i]['price']/100*$margin['combo_margin'];
	else
		$items[$i]['discount']=$items[$i]['price']/100*$margin['margin'];

	$total+=$items[$i]['price']*$items[$i]['qty'];
	$d_total+=($items[$i]['price']-$items[$i]['discount'])*$items[$i]['qty'];
	$commision+=$items[$i]['discount']*$items[$i]['qty'];
	$itemids[]=$prod['id'];
	$itemnames[]=$prod['name'];
	$items[$i]['margin']=$margin;
	$items[$i]['tax']=$prod['tax'];
	}


	$avail=$this->erpm->do_stock_check($itemids);
	foreach($itemids as $i=>$itemid)
		if(!in_array($itemid,$avail))
		$this->pdie("{$itemnames[$i]} is out of stock");
	$fran_crdet = $this->erpm->get_fran_availcreditlimit($fran['franchise_id']);
	$fran['balance'] = $fran_crdet[3];
	if($fran['balance']<$d_total && $ordr_frm_usr==0 )
		$this->pdie("Insufficient balance! Balance in your account Rs {$fran['current_balance']} Total order amount : Rs $d_total");
	if($this->db->query("select 1 from pnh_member_info where pnh_member_id=?",$mid)->num_rows()==0)
	{
	if($this->db->query("select 1 from pnh_m_allotted_mid where ? between mid_start and mid_end and franchise_id=?",array($mid,$fran['franchise_id']))->num_rows()==0)
		$this->pdie("Member ID $mid is not allotted to you");
	$this->db->query("insert into king_users(name,is_pnh,createdon) values(?,1,?)",array("PNH Member: $mid",time()));
	$userid=$this->db->insert_id();
	$this->db->query("insert into pnh_member_info(user_id,pnh_member_id,franchise_id) values(?,?,?)",array($userid,$mid,$fran['franchise_id']));
	$npoints=$this->db->query("select points from pnh_member_info where user_id=?",$userid)->row()->points+PNH_MEMBER_FEE;
	$this->db->query("update pnh_member_info set points=? where user_id=? limit 1",array($npoints,$userid));
	$this->db->query("insert into pnh_member_points_track(user_id,transid,points,points_after,created_on) values(?,?,?,?,?)",array($userid,"",PNH_MEMBER_FEE,$npoints,time()));
	$this->erpm->pnh_fran_account_stat($fran['franchise_id'],1,PNH_MEMBER_FEE-PNH_MEMBER_BONUS,"50 Credit Points purchase for Member $mid","member",$mid);
	}
	else
		$userid=$this->db->query("select user_id from pnh_member_info where pnh_member_id=?",$mid)->row()->user_id;
	$transid=strtoupper("PNH".random_string("alpha",3).$this->p_genid(5));
	if($ordr_frm_usr==0)
		$this->db->query("insert into king_transactions(transid,amount,paid,mode,init,actiontime,is_pnh,franchise_id) values(?,?,?,?,?,?,?,?)",array($transid,$d_total,$d_total,3,time(),time(),1,$fran['franchise_id']));
	else
		$this->db->query("insert into user_order_transactions(transid,amount,paid,mode,init,actiontime,is_pnh,franchise_id) values(?,?,?,?,?,?,?,?)",array($transid,$d_total,$d_total,3,time(),time(),1,$fran_id));
	foreach($items as $item)
	{
	$inp=array("id"=>$this->p_genid(10),"transid"=>$transid,"userid"=>$userid,"itemid"=>$item['itemid'],"brandid"=>"");
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
	$m_inp=array("transid"=>$transid,"itemid"=>$item['itemid'],"mrp"=>$item['mrp'],"price"=>$item['price'],"base_margin"=>$item['margin']['base_margin'],"sch_margin"=>$item['margin']['sch_margin'],"bal_discount"=>$item['margin']['bal_discount'],"qty"=>$item['qty'],"final_price"=>$item['price']-$item['discount']);
	if($ordr_frm_usr==0)
	{
	$this->db->insert("king_orders",$inp);
	$this->db->insert("pnh_order_margin_track",$m_inp);
	}
	else
	{
	$this->db->insert("king_user_orders",$inp);
	$this->db->insert("user_order_margin_track",$m_inp);
	}
	}
	if($ordr_frm_usr==0)
	{
	$this->erpm->pnh_fran_account_stat($fran['franchise_id'],1, $d_total,"Order $transid - Total Amount: Rs $total","order",$transid);
	$balance=$this->db->query("select current_balance from pnh_m_franchise_info where franchise_id=?",$fran['franchise_id'])->row()->current_balance;
	echo "Your order is placed successfully! Total order amount :Rs $total. Amount deducted is Rs $d_total. Your order ID is $transid Balance in your account Rs $balance";
	$this->erpm->sendsms_franchise_order($transid,$d_total);


	$points=$this->db->query("select points from pnh_loyalty_points where amount<? order by amount desc limit 1",$total)->row_array();
	if(!empty($points))
		$points=$points['points'];
	else $points=0;
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

	//do packing process
	$ttl_num_orders=count($items);
	$batch_remarks="PNH Order placed through SMS by $from";
	$updated_by=$userid;

	// Process to batch this transaction
	$this->reservations->do_batching_process($transid,$ttl_num_orders,$batch_remarks,$updated_by);

	$this->erpm->do_trans_changelog($transid,$batch_remarks);
	}
	if($ordr_frm_usr==1)
	{
	$transid=str_replace("PNH", " ", $transid);
	$transid=trim($transid);
	$this->erpm->pnh_sendsms($mem_mob,"Congrats !Your Order $transid has been sucessfully placed.Please contact ($fran_name-$fran_mobile) for any queries.- Storeking - Lets Talk Shoppingg..",$fran_id,0,'MEM_ORDER');
	echo "Congrats !Your Order $transid has been sucessfully placed.Please contact your  ($fran_name-$fran_mobile) for any queries.- Storeking - Lets Talk Shoppingg..";
	$this->erpm->pnh_sendsms($fran_mobile,"Hello  $fran_name,$mem_name $mem_mob has placed order for Rs $d_total.order ID is $transid.Please Confirm order to proceed.Order Commision:$commision",$fran_id,0,'MEM_ORDER');
	echo "Hello  $fran_name,$mem_name $mem_mob has placed order for Rs $d_total.order ID is $transid.Please Confirm order to proceed.Order Commision:$commision";
	}
	}

	*/
        
	function cancelOrder($from,$msg)
	{

		$fran=$this->db->query("select * from pnh_m_franchise_info where (login_mobile1=? and login_mobile1!=0) or (login_mobile2=? and login_mobile2!=0)",array($from,$from))->row_array();
		if(empty($fran))
		{
			$membr=$this->db->query("select * from pnh_member_info where mobile=?",$from)->row_array();
			$this->cancel_prepaidorder($from,$msg,$membr);
			exit;
		}
		list($call,$transid)=explode(" ",$msg);
		if(empty($transid))
			$this->pdie("No Order ID provided");
		if($this->db->query("select 1 from king_transactions where transid=? and franchise_id=?",array($transid,$fran['franchise_id']))->num_rows()==0)
			$this->pdie("Invalid Order ID");
		if($this->db->query("select 1 from king_orders where transid=? and status=3",$transid)->num_rows()!=0)
			$this->pdie("One or more of the items in Order $transid is already cancelled. Please contact customer support");
		if($this->db->query("select 1 from king_orders where transid=? and status=2",$transid)->num_rows()!=0)
			$this->pdie("Order $transid is already invoiced and cannot be cancelled");
		if($this->db->query("select 1 from king_orders where transid=? and status=1",$transid)->num_rows()!=0)
			$this->pdie("Order $transid is already shipped and cannot be cancelled");
		$this->erpm->do_trans_changelog($transid,"PNH Order cancelled through SMS");
		$this->db->query("update king_orders set status=3 where transid=?",$transid);
		$this->erpm->do_trans_changelog($transid,"PNH Order cancelled through SMS");
		$trans=$this->db->query("select * from king_transactions where transid=?",$transid)->row_array();
		$this->db->query("insert into t_refund_info(transid,amount,status,created_on,created_by) values(?,?,?,?,?)",array($transid,$trans['amount'],1,time(),1));
		$rid=$this->db->insert_id();
		foreach($this->db->query("select id,quantity from king_orders where transid=?",$transid)->result_array() as $i=>$ord)
		{
			$o=$ord['id'];
			$qty=$ord['quantity'];
			$this->db->query("insert into t_refund_order_item_link(refund_id,order_id,qty) values(?,?,?)",array($rid,$o,$qty));
			$this->db->query("update king_orders set status=3,actiontime=".time()." where id=? limit 1",$o);
		}
		$this->erpm->pnh_fran_account_stat($fran['franchise_id'],0,$trans['amount'],"Refund - Order $transid cancelled","refund",$transid);
		$nbalance=$this->db->query("select current_balance as b from pnh_m_franchise_info where franchise_id=?",$fran['franchise_id'])->row()->b;
		echo "Order $transid is cancelled and Rs {$trans['amount']} is credited back to your account. Your new balance is Rs $nbalance";
	}


	function p_genid($len)
	{
		$st="";
		for($i=0;$i<$len;$i++)
			$st.=rand(1,9);
		return $st;
	}

	function executive_sms_log($from,$msg,$type)
	{
		//$from=trim($from);
		$emp_id = $this->db->query('select employee_id from m_employee_info where is_suspended=0 AND job_title2>=4 and contact_no like ? ','%'.$from.'%')->row()->employee_id;
		$this->db->query('insert into pnh_executive_accounts_log(emp_id,type,msg,sender,reciept_status,logged_on)values(?,?,?,?,0,now())',array($emp_id,$type,$msg,$from));
		//$this->pdie("Message updated successfully");

	}

	function task_sms_log($from,$msg)
	{

		$emp_id=$this->db->query('select employee_id,name from m_employee_info where is_suspended=0 AND contact_no like ? ','%'.$from.'%')->row()->employee_id;

		// check if user is alloted with this task, if not throw back error msg.
		$taskid=$this->db->query("select ref_no from pnh_m_task_info where is_active=1 and task_status<3 and assigned_to=?",$emp_id)->result_array();
		if($taskid)
			$t=implode(",",$taskid[0]);
			

		// split msg for identifying taskid and msg
		$msg=trim(str_ireplace('t',"",$msg));

		list($t)=explode(" ",$msg);
		if(empty($t))
			$this->pdie("No Task id Provided");
			
		// check for second part of the arr for valid task id if not throw error msg.
		if($this->db->query("select 1 from pnh_m_task_info where ref_no in (?) and assigned_to=?",array($t,$emp_id))->num_rows()==0)
			$this->pdie("TaskID #".$t." is invalid ");

			
		// if valid task , if closed then throw error.
		if($this->db->query("select task from pnh_m_task_info where task_status=3 and ref_no in(?) and assigned_to=?",array($t,$emp_id))->num_rows()!=0)
		{
			$this->pdie("Task #".$t." is Already closed");
		}
		else
		{
			// else insert 3rd part of msg into task remarks and post greetings msg.

			$m=trim(str_ireplace($t,"",$msg));
			if($m)
			{
				//insert
				$this->db->query("update pnh_m_task_info set task_status=2 where ref_no in (?) and assigned_to=?",array($t,$emp_id));
				$this->db->query("insert into pnh_task_remarks(emp_id,task_id,remarks,posted_by,posted_on)values(?,?,?,?,now())",array($emp_id,$t,$m,$emp_id));
				if($this->db->affected_rows()>=1)
					$this->pdie("Task Remarks updated Successfully");
			}
			else
			{
				$this->pdie("Task Remarks is missing.");
			}
		}
	}
	function call_sms($from,$msg,$fran)
	{
		$fran=$this->db->query("select * from pnh_m_franchise_info where (login_mobile1=? and login_mobile1!=0) or (login_mobile2=? and login_mobile2!=0) and is_suspended=0",array($from,$from))->row_array();
		$this->db->query('insert into pnh_sms_log(sender,msg,franchise_id,type,created_on)values(?,?,?,"CALL",unix_timestamp(now()))',array($from,$msg,$fran['franchise_id']));
		//$this->pdie("Call request placed successfully");
	}

	/**
	 * Invoices delivered to franchise from driver
	 * @param unknown_type $from
	 * @param unknown_type $msg
	 * @param unknown_type $fran
	 */

	function driver_sms($from,$msg,$fran)
	{

		$emp_id=$this->db->query("select employee_id from m_employee_info where is_suspended=0 AND (job_title=5 or job_title2 in (6,7)) and contact_no like ? ",'%'.$from.'%')->row()->employee_id;
		if(empty($emp_id))
			$this->pdie("Invalid Account in Action,Please process with the registered driver mobile.");

			
		$fr_id=$fran['franchise_id'];
		$fr_name=$fran['franchise_name'];
		$fr_contactno=$fran['login_mobile1'];
		list($call,$invoice_nos)=explode(" ",$msg);

		if(empty($invoice_nos))
			$this->pdie("Invoice need to be entered");


		// populate invoice_nos array

		$inv_nos_str = str_replace($call,'',$msg);
		$inv_nos_str = str_replace(' ','',$inv_nos_str);
		$inv_nos_str = trim(str_replace('\n',' ',$inv_nos_str));
		$invoice_nos = array_filter(array_unique(explode(',',$inv_nos_str)));

		// check if the $invoice_nos provided are dispatch IDs
		$tmp_invnos = array();
		foreach($invoice_nos as $dispatch_id)
		{
			$dis_inv_list_res = $this->db->query("select distinct invoice_no
					from shipment_batch_process_invoice_link a
					join proforma_invoices b on a.p_invoice_no = b.p_invoice_no
					where dispatch_id = ? ",$dispatch_id);
			if($dis_inv_list_res->num_rows())
			{
				foreach($dis_inv_list_res->result_array() as $row)
				{
					$tmp_invnos[] = $row['invoice_no'];
				}
			}
		}

		if(count($tmp_invnos))
			$invoice_nos = $tmp_invnos;

		//print_r($invoice_nos);
		$total_delivered = 0;
		$delivered_invnos = array();
		$invoice_upd_status = array();
		$fran_invoice_delivered = array();
		if(count($invoice_nos))
		{
			foreach($invoice_nos as $invno)
			{
				//check manifesto_driver_log weather that driver is related to the invoice

				if(!isset($invoice_upd_status[$invno]))
					$invoice_upd_status[$invno] = array('error'=>array(),'is_delivered'=>0,'fran'=>'');

				// check if invoice is marked to process delivery
				$inv_transit_det_res = $this->db->query("select sent_log_id,invoice_no,status,ref_id from pnh_invoice_transit_log where invoice_no = ? order by id desc limit 1",$invno);
				if(!$inv_transit_det_res->num_rows())
				{
					array_push($invoice_upd_status[$invno]['error'],'invalid'); // unassigned
				}
				else
				{

					$inv_transit_det = $inv_transit_det_res->row_array();

					$process_delivery = 1;
					// check if the invoice is carried by autorised person / employee
					if($inv_transit_det['ref_id'] != $emp_id)
					{
						$alt_mob_no = $this->db->query("select alternative_contactno from pnh_m_manifesto_sent_log where id = ? ",$inv_transit_det['sent_log_id'])->row()->alternative_contactno;

						if($alt_mob_no == $from)
						{
							$emp_id = $inv_transit_det['ref_id'];
						}else
						{
							$process_delivery = 0;
							array_push($invoice_upd_status[$invno]['error'],'unassigned'); // unassigned
						}
							
					}

					// check if invoice status is valid to update delivery status
					if(($inv_transit_det['status'] == 1 || $inv_transit_det['status'] == 2 || $inv_transit_det['status'] == 5 ) && $process_delivery)
					{
						$process_delivery = 1;
					}
					else
					{
						$process_delivery = 0;
						if($inv_transit_det['status'] == 3)
							array_push($invoice_upd_status[$invno]['error'],'already delivered'); // already delivered
						else if($inv_transit_det['status'] == 4)
							array_push($invoice_upd_status[$invno]['error'],'marked for return'); // marked for return
					}

					// check if ready to be updated
					if($process_delivery)
					{
						// update invoice delivered status
						$f_id = @$this->db->query("select franchise_id from king_invoice a join king_transactions b on a.transid = b.transid where a.invoice_no = ? Group BY franchise_id",$invno)->row()->franchise_id;
							
						// update invoice status to delivered
						$this->db->query("update shipment_batch_process_invoice_link set is_delivered = 1,delivered_on = now() where invoice_no = ? ",$invno);

						// Insert into sms_invoice_log table
						$this->db->query("insert into sms_invoice_log(type,fid,invoice_no,emp_id1,emp_id2,status,logged_on,logged_by)values(1,?,?,?,0,1,now(),?)",array($f_id,$invno,$emp_id,$emp_id));

						$this->db->query("insert into pnh_invoice_transit_log(sent_log_id,invoice_no,ref_id,status,logged_on,logged_by,received_on)values(?,?,?,3,now(),0,now())",array($inv_transit_det['sent_log_id'],$invno,$emp_id));

						$log_prm2=array();
						$log_prm2['emp_id']=$emp_id;
						$log_prm2['contact_no']=$from;
						$log_prm2['type']=10;
						$log_prm2['grp_msg']=$msg;
						$log_prm2['created_on']=cur_datetime();

						$this->db->insert("pnh_employee_grpsms_log",$log_prm2);

						//	echo $this->db->last_query();
						$total_delivered ++;
						$delivered_invnos[] = $invno;
						$invoice_upd_status[$invno]['is_delivered'] = 1;

						if(!isset($fran_invoice_delivered[$f_id]))
							$fran_invoice_delivered[$f_id] = array();

						array_push($fran_invoice_delivered[$f_id],$invno);

					}
				}
			}


			if(count($fran_invoice_delivered))
			{
				foreach($fran_invoice_delivered as $f_id=>$invoice_nos)
				{
					//$fran_det = $this->db->query("select franchise_id,franchise_name,login_mobile1 from pnh_m_franchise_info where franchise_id = ? ",$f_id)->row_array();
					//$this->erpm->pnh_sendsms($fran_det['login_mobile1'],"Dear ".$fran_det['franchise_name'].",Invoice no-  ".implode(',',$invoice_nos)."is delivered.Happy Franchising!!!",$f_id,$emp_id,11);

					// notify member about the delivery
					foreach($invoice_nos as $inv_n)
						$this->erpm->notify_shipment_delivery_sms($inv_n,1,1,0);

				}
			}

			$error_msg_arr = array();

			foreach($invoice_upd_status as $invno => $invno_delivery_det)
			{
				if(!$invno_delivery_det['is_delivered'])
					$error_msg_arr[] = $invno.":".implode(':',$invno_delivery_det['error']);
			}

			$resp_msg = '';
			if(count($delivered_invnos))
			{
				$resp_msg .= "Invoice no-  ".implode(",",$delivered_invnos)." marked delivered";
			}

			if(count($error_msg_arr))
			{
				$resp_msg .= $resp_msg?"\r\n":'';
				$resp_msg .= "Invoice nos-".implode(",",$error_msg_arr);
			}

			if($resp_msg)
				$resp_msg .= ",any doubts please call 1800 200 1996";

			if($resp_msg)
				$this->pdie($resp_msg);

		}
	}
	/**
	 * Invoice returned from driver
	 * @param unknown_type $from
	 * @param unknown_type $msg
	 * @param unknown_type $fran_ex
	 */
	function returnd_invoice_frm_driver($from,$msg,$fran_ex)
	{
		$from=trim($from);
		$emp_id=$this->db->query("select employee_id from m_employee_info where is_suspended=0 AND (job_title=5 or job_title2 in (6,7)) and contact_no like ? ",'%'.$from.'%')->row()->employee_id;
		if(empty($emp_id))
			$this->pdie("Invalid Account - $from in Action,Please process with the registered driver mobile.");

		list($call,$invoice_nos)=explode(" ",$msg);

		if(empty($invoice_nos))
			$this->pdie("Invoice need to be entered");

		// populate invoice_nos array
		$inv_nos_str = str_replace($call,'',$msg);
		$inv_nos_str = str_replace(' ','',$inv_nos_str);
		$inv_nos_str = trim(str_replace('\n',' ',$inv_nos_str));
		$invoice_nos = array_filter(array_unique(explode(',',$inv_nos_str)));

		//print_r($invoice_nos);
		$total_returned = 0;
		$returned_invnos = array();
		$invoice_upd_status = array();
		$fran_invoice_returned = array();
		if(count($invoice_nos))
		{
			foreach($invoice_nos as $invno)
			{
				//check manifesto_driver_log weather that driver is related to the invoice

				if(!isset($invoice_upd_status[$invno]))
					$invoice_upd_status[$invno] = array('error'=>array(),'is_returned'=>0,'fran'=>'');

				// get invoice latest status
				$inv_transit_det_res = $this->db->query("select sent_log_id,invoice_no,status,ref_id from pnh_invoice_transit_log where invoice_no = ? order by id desc limit 1",$invno);
				if(!$inv_transit_det_res->num_rows())
				{
					// check if the invoice is old shipped invoice

					$ship_invdet_res = $this->db->query("select * from shipment_batch_process_invoice_link where invoice_no = ? and shipped = 1 ",$invno);
					if($ship_invdet_res->num_rows())
					{
						$process_return = 1;
					}else
					{
						$process_return = 0;
						array_push($invoice_upd_status[$invno]['error'],'invalid'); // unknown invoice
					}

				}
				else
				{
					$inv_transit_det = $inv_transit_det_res->row_array();
					$process_return = 1;

					// check if invoice status is valid to update delivery status
					if(($inv_transit_det['status'] < 4) && $process_return)
					{
						$process_return = 1;
					}
					else
					{
						$process_return = 0;
						if($inv_transit_det['status'] == 4)
							array_push($invoice_upd_status[$invno]['error'],'already returned'); // already returned
						else
							array_push($invoice_upd_status[$invno]['error'],'invalid for return'); // already returned

					}
				}

				// check if ready to be updated
				if($process_return)
				{
					// update invoice returned status
					$f_id = @$this->db->query("select franchise_id from king_invoice a join king_transactions b on a.transid = b.transid where a.invoice_no = ? Group BY franchise_id",$invno)->row()->franchise_id;

					// Insert into sms_invoice_log table
					$this->db->query("insert into sms_invoice_log(type,fid,invoice_no,emp_id1,emp_id2,status,logged_on,logged_by)values(2,?,?,?,0,1,now(),?)",array($f_id,$invno,$emp_id,$emp_id));

					$send_log_id = isset($inv_transit_det['sent_log_id'])?$inv_transit_det['sent_log_id']:0;

					$this->db->query("insert into pnh_invoice_transit_log(sent_log_id,invoice_no,ref_id,status,logged_on,logged_by)values(?,?,?,4,now(),0)",array($send_log_id,$invno,$emp_id));

					$log_prm2=array();
					$log_prm2['emp_id']=$emp_id;
					$log_prm2['contact_no']=$from;
					$log_prm2['type']=11;
					$log_prm2['grp_msg']=$msg;
					$log_prm2['created_on']=cur_datetime();

					$this->db->insert("pnh_employee_grpsms_log",$log_prm2);

					//	echo $this->db->last_query();
					$total_returned ++;
					$returned_invnos[] = $invno;
					$invoice_upd_status[$invno]['is_returned'] = 1;

					if(!isset($fran_invoice_returned[$f_id]))
						$fran_invoice_returned[$f_id] = array();

					array_push($fran_invoice_returned[$f_id],$invno);

				}


			}
		}
			
			
		if(count($fran_invoice_returned))
		{
			foreach($fran_invoice_returned as $f_id=>$invoice_nos)
			{
				$fran_det = $this->db->query("select franchise_id,franchise_name,login_mobile1 from pnh_m_franchise_info where franchise_id = ? ",$f_id)->row_array();
				$this->erpm->pnh_sendsms($fran_det['login_mobile1'],"Dear ".$fran_det['franchise_name'].",Invoice no-  ".implode(',',$invoice_nos)."is added in return list.Happy Franchising!!!",$f_id,$emp_id,13);
			}
		}
			
		$error_msg_arr = array();
		foreach($invoice_upd_status as $invno => $invno_delivery_det)
		{
			if(!$invno_delivery_det['is_returned'])
				$error_msg_arr[] = $invno.":".implode(':',$invno_delivery_det['error']);
		}

		$resp_msg = '';
		if(count($returned_invnos))
		{
			$resp_msg .= "Invoice no-  ".implode(",",$returned_invnos)." marked returned";
		}
		if(count($error_msg_arr))
		{
			$resp_msg .= $resp_msg?"\r\n":'';
			$resp_msg .= "Invoice nos-".implode(",",$error_msg_arr);
		}

		if($resp_msg)
			$resp_msg .= ",any doubts please call 1800 200 1996";
			
		if($resp_msg)
			$this->pdie($resp_msg);
			
	}

	// M 10009201
	function manifesto_pickup($from,$msg,$fran_ex)
	{
		$emp_id=$this->db->query("select employee_id from m_employee_info where is_suspended=0 AND (job_title2 in (4,5,6,7)) and contact_no like ? ",'%'.$from.'%')->row()->employee_id;
		if(empty($emp_id))
			$this->pdie("Invalid Account in Action,Please process with the registered mobile.");

			
		list($call,$sent_man_id)=explode(" ",$msg);

		if(empty($sent_man_id))
			$this->pdie("Please enter manifesto slno, any doubts please call 1800 200 1996");


		// check if manifesto already pickedup
		$sent_invdet_res = $this->db->query("select * from pnh_invoice_transit_log  where sent_log_id = ? order by id desc",$sent_man_id);
		if(!$sent_invdet_res->num_rows())
		{
			$this->pdie("Invalid Manifesto slno entered, any doubts please call 1800 200 1996");
		}


		// get employee assigned territory
		$emp_terrid = @$this->db->query("select territory_id from m_town_territory_link where employee_id = ? ",$emp_id)->row()->territory_id*1;

		// Check if manifesto is assigned to pickup employee_id.
		$pickup_empdet_res = $this->db->query('select pickup_empid,job_title2,c.territory_id,c.town_id
				from pnh_m_manifesto_sent_log a
				join m_employee_info b on b.employee_id = a.pickup_empid and b.is_suspended = 0
				left join m_town_territory_link c on c.employee_id = b.employee_id and c.is_active = 1
				where a.id = ? ',$sent_man_id);

		if(!$pickup_empdet_res->num_rows())
		{
			$this->pdie("Unable to assign manifesto for delivery, any doubts please call 1800 200 1996");
		}else
		{
			//get pickup employee id
			$pickup_empdet = $pickup_empdet_res->row_array();

			//check if invalid user in making pickup
			if($emp_id != $pickup_empdet['pickup_empid'])
			{
				/*if($emp_terrid != $pickup_empdet['territory_id'])
				 {
				// invalid user , checking from mismatch in territory assignment and pickup employee
				$this->pdie("Manifesto is not assigned to You/Your Territory, any doubts please call 1800 200 1996");
				}*/

				//check if have alternative number
				$alt_mob_no = $this->db->query("select alternative_contactno from pnh_m_manifesto_sent_log where id = ? ",$sent_man_id)->row()->alternative_contactno;

				if($alt_mob_no == $from)
				{
					$emp_id = $pickup_empdet['pickup_empid'];
				}else
				{
					$this->pdie("Manifesto is not assigned to You/Your Territory, any doubts please call 1800 200 1996");
				}
			}
		}
			

		$assigned_invoices = array();
		foreach($sent_invdet_res->result_array() as $sent_invdet)
		{
			// check if status = 1 and ref_id = 0
			if($sent_invdet['status'] == 1 && $sent_invdet['ref_id'] == 0)
			{
				$invno = $sent_invdet['invoice_no'];
				array_push($assigned_invoices,$invno);
				$this->db->query("insert into pnh_invoice_transit_log(sent_log_id,invoice_no,ref_id,status,logged_on,logged_by)values(?,?,?,5,now(),0)",array($sent_invdet['sent_log_id'],$invno,$emp_id));

				$log_prm2=array();
				$log_prm2['emp_id']=$emp_id;
				$log_prm2['contact_no']=$from;
				$log_prm2['type']=8;
				$log_prm2['grp_msg']=$msg;
				$log_prm2['created_on']=cur_datetime();

				$this->db->insert("pnh_employee_grpsms_log",$log_prm2);
			}else
			{
				break;
			}
		}


		$resp_msg = '';
		if(count($assigned_invoices))
			$resp_msg = "Invoice no- ".implode(",",$assigned_invoices)." assigned for delivery, any doubts please call 1800 200 1996";
		else
			$resp_msg = "Manifesto({$sent_man_id}) already pickedup, any doubts please call 1800 200 1996";

		$this->pdie($resp_msg);

	}

	/**
	 * Invoice handled from driver to executive
	 * @param unknown_type $from
	 * @param unknown_type $msg
	 * @param unknown_type $fran
	 */
	function invoice_frm_driver_toex($from,$msg,$fran)
	{
		$emp_id=$this->db->query("select employee_id from m_employee_info where is_suspended=0 AND(job_title=5 or job_title2 in (6,7)) and contact_no like ? ",'%'.$from.'%')->row()->employee_id;

		if(empty($emp_id))
			$this->pdie("Invalid Account - $from in Action,Please process with the registered driver mobile.");

		list($call,$invoice_no)=explode(" ",$msg);
			
		//split invoice number
		$inv_nos_str = str_replace($call,'',$msg);
		$inv_nos_str = str_replace(' ','',$inv_nos_str);
		$invoice_nost=explode('-', $inv_nos_str);
		$invoice_nos=explode(',',$invoice_nost[0]);   // split invoiceno
		$exchange_emp_mobno = @trim($invoice_nost[1]);        // split executive contactno

		if(empty($invoice_no))
			$this->pdie("Invalid Call please insert invoice nos and executive contact number ");

		$exchange_emp_id = 0;

		// check if valid employee mob no
		if($exchange_emp_mobno)
		{
			$exchange_emp_res = @$this->db->query("select employee_id,name as employee_name,job_title,job_title2 from m_employee_info where is_suspended=0 AND job_title2 in (5,6)  and contact_no like ? ",'%'.$exchange_emp_mobno.'%');
			if(!$exchange_emp_res->num_rows())
			{
				$this->pdie("Invalid Executive or Field Coordinator mobile no entered");
			}
		}
		else
			$this->pdie("Invalid Call please insert executive contact number to whom u have handled");

		$exchange_emp_det = $exchange_emp_res->row_array();

			

		$invoice_nos = array_filter(array_unique($invoice_nos));

		//print_r($invoice_nos);
		$total_exchanged = 0;
		$exchanged_invnos = array();
		$invoice_upd_status = array();
		$fran_invoice_exchanged = array();
		if(count($invoice_nos))
		{
			foreach($invoice_nos as $invno)
			{
				//check manifesto_driver_log weather that driver is related to the invoice

				if(!isset($invoice_upd_status[$invno]))
					$invoice_upd_status[$invno] = array('error'=>array(),'is_exchanged'=>0,'fran'=>'');

				// check if invoice is marked to process exchange
				$inv_transit_det_res = $this->db->query("select sent_log_id,invoice_no,status,ref_id from pnh_invoice_transit_log where invoice_no = ? order by id desc limit 1",$invno);
				if(!$inv_transit_det_res->num_rows())
				{
					array_push($invoice_upd_status[$invno]['error'],'invalid'); // unassigned
				}
				else
				{
					$inv_transit_det = $inv_transit_det_res->row_array();

					$process_exchange = 1;
					// check if the invoice is carried by autorised person / employee
					if($inv_transit_det['ref_id'] != $emp_id)
					{
						$alt_mob_no = $this->db->query("select alternative_contactno from pnh_m_manifesto_sent_log where id = ? ",$inv_transit_det['sent_log_id'])->row()->alternative_contactno;

						if($alt_mob_no == $from)
						{
							$emp_id = $inv_transit_det['ref_id'];
						}else
						{
							$process_exchange = 0;
							array_push($invoice_upd_status[$invno]['error'],'unassigned'); // unassigned
						}
					}

					// check if invoice status is valid to update exchange status
					if(($inv_transit_det['status'] == 1 || $inv_transit_det['status'] == 2 || $inv_transit_det['status'] == 5) && $process_exchange)
					{
						$process_exchange = 1;
					}
					else
					{
						$process_exchange = 0;
						if($inv_transit_det['status'] == 3)
							array_push($invoice_upd_status[$invno]['error'],'already delivered'); // already exchanged
						else if($inv_transit_det['status'] == 4)
							array_push($invoice_upd_status[$invno]['error'],'already in return'); // marked for return
							
					}

					// check if ready to be updated
					if($process_exchange)
					{
						// update invoice exchanged status
						$fran_det = @$this->db->query("select b.franchise_id,town_id,territory_id from king_invoice a join king_transactions b on a.transid = b.transid join pnh_m_franchise_info c on c.franchise_id = b.franchise_id where a.invoice_no = ? Group BY franchise_id",$invno)->row_array();
							
						// check if executive is working in franchise territory
						if($exchange_emp_det['job_title2'] == 5 )
						{
							if(!$this->db->query("SELECT count(*) as t FROM m_town_territory_link where employee_id = ? and town_id = ? and is_active = 1 ",array($exchange_emp_det['employee_id'],$fran_det['town_id']))->row()->t)
							{
								array_push($invoice_upd_status[$invno]['error'],'invalid employee town'); // invalid employee town
								$process_exchange = 0;
								continue;
							}
						}

						$this->db->query("insert into pnh_invoice_transit_log(sent_log_id,invoice_no,ref_id,status,logged_on,logged_by)values(?,?,?,2,now(),0)",array($inv_transit_det['sent_log_id'],$invno,$exchange_emp_det['employee_id']));

						$log_prm2=array();
						$log_prm2['emp_id']=$emp_id;
						$log_prm2['contact_no']=$from;
						$log_prm2['type']=9;
						$log_prm2['grp_msg']=$msg;
						$log_prm2['created_on']=cur_datetime();

						$this->db->insert("pnh_employee_grpsms_log",$log_prm2);


						$f_id = $fran_det['franchise_id'];
						//	echo $this->db->last_query();
						$total_exchanged ++;
						$exchanged_invnos[] = $invno;
						$invoice_upd_status[$invno]['is_exchanged'] = 1;

						if(!isset($fran_invoice_exchanged[$f_id]))
							$fran_invoice_exchanged[$f_id] = array();

						array_push($fran_invoice_exchanged[$f_id],$invno);

					}
				}
			}


			if(count($fran_invoice_exchanged))
			{
				foreach($fran_invoice_exchanged as $f_id=>$invoice_nos)
				{
					$fran_det = $this->db->query("select franchise_id,franchise_name,login_mobile1 from pnh_m_franchise_info where franchise_id = ? ",$f_id)->row_array();
					$this->erpm->pnh_sendsms($fran_det['login_mobile1'],"Dear ".$fran_det['franchise_name'].",Invoice no-  ".implode(',',$invoice_nos)." handed over to Paynearhome Executive - {$exchange_emp_det['employee_name']}($exchange_emp_mobno)  Happy Franchising!!!",$f_id,$emp_id);
				}
			}

			$error_msg_arr = array();
			foreach($invoice_upd_status as $invno => $invno_exchange_det)
			{
				if(!$invno_exchange_det['is_exchanged'])
					$error_msg_arr[] = $invno.":".implode(':',$invno_exchange_det['error']);
			}

			$resp_msg = '';
			if(count($exchanged_invnos))
			{
				$resp_msg .= "Invoice no-  ".implode(",",$exchanged_invnos)." handedover to ".$exchange_emp_det['employee_name']."(".$exchange_emp_mobno.")";
			}
			if(count($error_msg_arr))
			{
				$resp_msg .= $resp_msg?"\r\n":'';
				$resp_msg .= "Invoice nos-".implode(",",$error_msg_arr);
			}

			if($resp_msg)
				$resp_msg .= ",any doubts please call 1800 200 1996";

			if($resp_msg)
				$this->pdie($resp_msg);

		}
			
			
	}

	function goods_shipment_sms()
	{
		$franchise_info_res=$this->db->query("SELECT franchise_id,franchise_name,current_balance,login_mobile1,login_mobile2 FROM pnh_m_franchise_info WHERE is_suspended=0");

		if($franchise_info_res->num_rows())
		{
			foreach($franchise_info_res->result_array() as $franchise_det)
			{
				$franchise_name=$franchise_det['franchise_name'];
				$invoice_no_det=$this->db->query("SELECT d.invoice_no,h.name
						FROM king_transactions a
						JOIN king_orders b ON a.transid = b.transid
						JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id
						JOIN king_invoice d ON d.order_id = b.id
						JOIN shipment_batch_process_invoice_link e ON e.invoice_no = d.invoice_no
						JOIN pnh_towns f ON f.town_name=c.city
						JOIN m_town_territory_link g ON g.town_id=f.id
						JOIN m_employee_info h ON h.employee_id=g.employee_id
						AND e.shipped =1 AND e.packed =1
						WHERE a.franchise_id = ? AND d.invoice_status = 1 AND b.status != 0 AND c.is_suspended=0 AND h.is_suspended=0 AND job_title2<=5 AND DATE(FROM_UNIXTIME(createdon))=CURDATE()
						GROUP BY d.invoice_no",$franchise_det['franchise_id'])->result_array();

				$executive_name=$invoice_no_det['name'];
				$ttl_shipped_amt=$this->db->query("SELECT round(sum((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) as amt
						FROM king_transactions a
						JOIN king_orders b ON a.transid = b.transid
						JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id
						JOIN king_invoice d ON d.order_id = b.id
						JOIN shipment_batch_process_invoice_link e ON e.invoice_no = d.invoice_no
						AND e.shipped =1 AND e.packed =1
						WHERE a.franchise_id = ? AND d.invoice_status = 1 and b.status != 0 AND c.is_suspended=0 AND job_title2<=5 AND DATE(FROM_UNIXTIME(createdon))=CURDATE()
						",$franchise_det['franchise_id'])->row()->amt;

				$this->erpm->pnh_sendsms($login_mobile1,"Dear $franchise_name your orders ($invoice_no) are delivered to your store.Order value Rs.$ttl_shipped_amt,please make timely payments. PayNearHome");
			}
		}
	}

	/**
	 * function to send bank details to franchise or executive
	 */
	function chk_pnh_bank($from,$msg)
	{
		list($call,$bank_short_code)=explode(" ",$msg);
		$bank_short_code = strtolower($bank_short_code);

		$bank_det_arr = array();
		$bank_det_arr['hdfc'] = 'Acc no:50200000198194,Accname:Local Cube Commerce Pvt Ltd,Branch:BSK 2nd stage,IFSC code:HDFC0002858';
		//$bank_det_arr['icici'] = 'Acc no:025105006977 ,Accname:Local Cube Commerce Pvt Ltd,Branch:JAYANAGAR 7th BLOCK,IFSC code:ICIC0000251';
		$bank_det_arr['icici'] = 'Acc no:263705000001 ,Accname:Local Cube Commerce Pvt Ltd,Branch:Kumbalagodu,IFSC code:ICIC0002637';
		$bank_det_arr['kotak'] = 'Acc no:2211241104 ,Accname:Local Cube Commerce Pvt Ltd,Branch:BSK 3rd stage,IFSC code:KKBK0000427';

		$resp_msg = '';
		if(isset($bank_det_arr[$bank_short_code]))
			$resp_msg = $bank_det_arr[$bank_short_code];
		else
			$resp_msg = 'Please Check you bank short code, available short codes ['.implode('|',array_keys($bank_det_arr)).']';

		$this->pdie($resp_msg);

	}

	/**
	 * function to update lr number for bus transport pnh shipments
	 * @param unknown_type $from
	 * @param unknown_type $msg
	 * @param unknown_type $call
	 */
	function update_lr_number($from,$msg,$call)
	{
		list($call,$sent_man_id)=explode(" ",$msg);
			
		//check the mobile number is valid
		$contact='%'.$from.'%';
		$use_det=$this->db->query("select b.employee_id,b.user_id,b.name,b.contact_no from pnh_m_manifesto_sent_log a
				join m_employee_info b on find_in_set(b.employee_id ,a.office_pickup_empid)
				where a.id=? and b.contact_no like ?;",array($sent_man_id,$contact))->row_array();
			
			
		if(empty($use_det['employee_id']))
			$this->pdie("Invalid Account in Action,Please process with the registered employee mobile.");
			
		$msg_detail=explode(" ",$msg);
		if(count($msg_detail)!=4)
			$this->pdie("Invalid message");
			
		if (!is_numeric($msg_detail[3]))
			$this->pdie("Invalid amount");
			
			
			
		if(empty($sent_man_id))
			$this->pdie("Please enter manifesto slno, any doubts please call 1800 200 1996");
			
		//check manifsto exist or not
		$is_manifesto=@$this->db->query("select id from pnh_m_manifesto_sent_log where id=?",$sent_man_id)->row()->id;
			
		if(empty($is_manifesto))
			$this->pdie("Invalid manifesto id");
			
		//check this is bus transport
		$it_bus_tranport=@$this->db->query("select id from pnh_m_manifesto_sent_log where id=? and hndlby_roleid=0 and bus_id!='' and bus_id is not null",$sent_man_id)->row()->id;
			
		if(empty($it_bus_tranport))
			$this->pdie("This manifesto not for bus shipment");
			
		//checl lr number already updatesd or not
		$lrno_updated=@$this->db->query("select lrno from pnh_m_manifesto_sent_log where id=?",$sent_man_id)->row()->lrno;
			
		if(!empty($lrno_updated))
			$this->pdie("This manifesto already updated");
			
		$is_scanned=@$this->db->query("select id from pnh_m_manifesto_sent_log where id=? and status=1",$sent_man_id)->row()->id;
			
		if(!empty($is_scanned))
			$this->pdie("This manifesto not outscanned");
			
		$isal_updated=@$this->db->query("select id from pnh_m_manifesto_sent_log where id=? and status=3",$sent_man_id)->row()->id;
			
		if(!empty($isal_updated))
			$this->pdie("This manifesto shipped");
			
		//update lrn nnumber
		if($sent_man_id)
		{
			$emp_id=$use_det['employee_id'];
			$user_id=$use_det['user_id'];

			$log_param3=array("emp_id"=>$emp_id,"contact_no"=>$from,"type"=>6,"grp_msg"=>$msg,"created_on"=>cur_datetime());
			$this->erpm->insert_pnh_employee_grpsms_log($log_param3);

			$this->db->query("update pnh_m_manifesto_sent_log set status=3,modified_on=?,modified_by=?,lrno=?,amount=?,lrn_updated_on=? where id=?",array(cur_datetime(),$user_id,$msg_detail[2],$msg_detail[3],cur_datetime(),$sent_man_id));

			//update shiped status
			$manifesto_id_det=$this->db->query("select manifesto_id,pickup_empid from pnh_m_manifesto_sent_log where id=?",$sent_man_id)->result_array();
			$manifesto_id=$manifesto_id_det[0]['manifesto_id'];
			$pick_up_emp_id=$manifesto_id_det[0]['pickup_empid'];

			$prama3=array();
			$prama3['shipped']=1;
			$prama3['shipped_on']=cur_datetime();
			$prama3['shipped_by']=$user_id;
			$prama3['awb']=$msg_detail[2];

			$this->db->where('inv_manifesto_id',$manifesto_id);
			$this->db->update("shipment_batch_process_invoice_link",$prama3);

			//sent a sms
			$sent_invoices=$this->db->query('select a.hndlby_type,a.hndlby_roleid,a.sent_invoices,b.name,a.hndleby_name,b.contact_no,a.hndleby_contactno,a.hndleby_vehicle_num,
					c.name as  bus_name,d.contact_no as des_contact,a.id
					from pnh_m_manifesto_sent_log a
					left join m_employee_info b on b.employee_id = a.hndleby_empid
					left join pnh_transporter_info c on c.id=a.bus_id
					left join pnh_transporter_dest_address d on d.id=a.bus_destination and d.transpoter_id=a.bus_id
					where a.id=?',$sent_man_id)->result_array();

			$invoices=$sent_invoices[0]['sent_invoices'];
			$hndbyname=$sent_invoices[0]['name'];
			$hndbycontactno=$sent_invoices[0]['contact_no'];
			$vehicle_num=$sent_invoices[0]['hndleby_vehicle_num'];

			if($sent_invoices[0]['hndlby_roleid']==6)
			{
				$hndbyname='Driver '.$sent_invoices[0]['hndleby_name'];
				$hndbycontactno=$sent_invoices[0]['hndleby_contactno'];
			}else if($sent_invoices[0]['hndlby_roleid']==7)
			{
				$hndbyname='Fright Co-ordinator '.$sent_invoices[0]['hndleby_name'];
				$hndbycontactno=$sent_invoices[0]['hndleby_contactno'];
			}else if($sent_invoices[0]['hndlby_type']==4)
			{
				$hndbyname='Courier '.$sent_invoices[0]['courier_name'];
			}else if($sent_invoices[0]['hndlby_type']==3 && $sent_invoices[0]['bus_name'])
			{
				$hndbyname='Transporter '.$sent_invoices[0]['bus_name'];
				$hndbycontactno=$sent_invoices[0]['des_contact'];
			}

			$this->db->query("update pnh_invoice_transit_log set status = 1,logged_on=now() where sent_log_id = ? and status = 0 ",$sent_man_id);

			if($this->db->affected_rows())
			{
				$pick_up_by_details=$this->db->query("select a.employee_id,a.job_title2,a.name,a.contact_no from m_employee_info a where employee_id=?",$pick_up_emp_id)->row_array();
					
				$employees_list=$this->erpm->get_emp_by_territory_and_town($invoices);
					
				$lrn=$msg_detail[2];
					
				if ($employees_list)
				{
					$town_list=array();
					foreach($employees_list as $emp)
					{
						$emp_name=$emp['name'];
						$emp_id=$emp['employee_id'];
						$town_name=$emp['town_name'];
						$territory_id=$emp['territory_id'];
						$town_id=$emp['town_id'];
						$town_list[]=$emp['town_name'];
						$emp_contact_nos = explode(',',$emp['contact_no']);
							
						$sms_msg = 'Dear '.$emp_name.',Shipment for the town '.$town_name.' sent via '.ucwords($hndbyname).'('.$hndbycontactno.') Lr no: '.$lrn.'.Manifesto Id : '.$sent_invoices[0]['id'];
						$temp_emp=array();
						foreach($emp_contact_nos as $emp_mob_no)
						{
							if(isset($temp_emp[$emp_id]))
								continue;
							$temp_emp[$emp_id]=1;

							$this->erpm->pnh_sendsms($emp_mob_no,$sms_msg);
							//	echo $emp_mob_no,$sms_msg;
							$log_prm=array();
							$log_prm['emp_id']=$emp_id;
							$log_prm['contact_no']=$emp_mob_no;
							$log_prm['type']=4;
							$log_prm['territory_id']=$territory_id;
							$log_prm['town_id']=$town_id;
							$log_prm['grp_msg']=$sms_msg;
							$log_prm['created_on']=cur_datetime();
							$this->erpm->insert_pnh_employee_grpsms_log($log_prm);
						}
							
					}
				}
					
					
				//pick up by
				if($pick_up_by_details)
				{
					$pemp_name=$pick_up_by_details['name'];
					$pemp_id=$pick_up_by_details['employee_id'];
					$pemp_contact_nos = explode(',',$pick_up_by_details['contact_no']);

					if($town_list)
					{
						$town_list=array_unique($town_list);
						$town_list=array_filter($town_list);
					}else{
						$town_list=array();
					}

					$sms_msg = 'Dear '.$pemp_name.',Shipment for the town '.implode(',',$town_list).' sent via '.ucwords($hndbyname).'('.$hndbycontactno.') Lr no:'.$lrn.'.Manifesto Id : '.$sent_invoices[0]['id'];

					$temp_emp=array();
					foreach($pemp_contact_nos as $emp_mob_no)
					{
						if(isset($temp_emp[$pemp_id]))
							continue;
						$temp_emp[$pemp_id]=1;

						$this->erpm->pnh_sendsms($emp_mob_no,$sms_msg);
							
						$log_prm2=array();
						$log_prm2['emp_id']=$pemp_id;
						$log_prm2['contact_no']=$emp_mob_no;
						$log_prm2['type']=4;
						$log_prm2['grp_msg']=$sms_msg;
						$log_prm2['created_on']=cur_datetime();
							
						$this->db->insert("pnh_employee_grpsms_log",$log_prm2);
					}
				}
			}
		}
	}

	/**
	 * function to register new members by sms
	 * @param unknown_type $from
	 * @param unknown_type $msg
	 */
	function reg_new_mem($from,$msg)
	{
		if(empty($from) || strlen($from)<10)
			$this->pdie("Invalid mobile number");
		$fran=$this->db->query("select * from pnh_m_franchise_info where (login_mobile1=? and login_mobile1!=0) or (login_mobile2=? and login_mobile2!=0) and is_suspended=0",array($from,$from))->row_array();
		if(empty($fran))
			$this->pdie("Invalid mobile number");
		else
			list($call,$mbr_id)=explode(" ",$msg);
		$msg=trim(str_ireplace('m',"",$msg));
		$member_id_arr=explode(" ",$msg);

		$membr_mobno = @trim($member_id_arr[0]);
		$membr_name=@trim($member_id_arr[1]);
		$ttl_regmemstr=sizeof($member_id_arr);

		if($ttl_regmemstr!=2)
		{
			$this->credit_on_imei_newmem($from,$msg,$fran);
			exit;
		}
		if(empty($membr_mobno))
			$this->pdie("Please enter Member Mobile number");
		if(empty($membr_name))
			$this->pdie("Member Name is Missing");

		$membr_id=$this->erpm->_gen_uniquememberid(); 			//generate random member id
		//check if the generated memberid is already registered
		$is_membr_id_valid=$this->db->query("SELECT * from pnh_member_info WHERE pnh_member_id=?",$membr_id)->row_array();

		//check the member mobile number is already registered
		if($membr_mobno)
			$is_membr_mobno_unique=$this->db->query("select * from pnh_member_info where mobile like ?","%$membr_mobno%");
			
		if(strlen($membr_mobno)!=10)
			$this->pdie("Invalid member mobile number");
		if($is_membr_mobno_unique->num_rows()!=0)
			$this->pdie("This mobile no $membr_mobno is already registered");
			
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
				 
				
				$this->pdie("Hello {$fran['franchise_name']}, Congrats!! $membr_name $membr_mobno has been Registered Successfully and has been assigned Member ID: $membr_id. Please make sure Registration fee of Rs ".PNH_MEMBER_FEE."/ has been collected.");

                                //==================< Member REGISTER SMS >====================
                                $fran_type = $this->fran_menu_type($fran['franchise_id']);
                                if($fran_type['menu_type'] == 'electonics')
                                {
                                    //order menu having electronics
                                    $mem_msg = "Hi $membr_name, Welcome to StoreKing – Hurry up!! Get Free Insurance on the 1st Electronic* product you buy. Your Member ID is $membr_id. Offer Valid only after Registration fee Of Rs ".PNH_MEMBER_FEE."/- is paid to Storeking Franchisee.";
                                            echo "Hi $membr_name, Welcome to StoreKing – Hurry up!! Get Free Insurance on the 1st Electronic* product you buy. Your Member ID is $membr_id. Offer Valid only after Registration fee Of Rs ".PNH_MEMBER_FEE."/- is paid to Storeking Franchisee.";
                                }
                                else
                                {
                                    $mem_msg = "Hi $membr_name, Welcome to StoreKing – Hurry up!! Get Free Talk Time worth Rs.".PNH_MEMBER_FREE_RECHARGE." on your 1st purchase above Rs ".MEM_MIN_ORDER_VAL.". Don’t forget your Member ID is $membr_id. Please deposit Rs".PNH_MEMBER_FEE."/- Registration fee with Storeking Franchisee to avail this offer";
                                            echo "Hi $membr_name, Welcome to StoreKing – Hurry up!! Get Free Talk Time worth Rs.".PNH_MEMBER_FREE_RECHARGE." on your 1st purchase above Rs ".MEM_MIN_ORDER_VAL.". Don’t forget your Member ID is $membr_id. Please deposit Rs".PNH_MEMBER_FEE."/- Registration fee with Storeking Franchisee to avail this offer";
                                }
				$this->erpm->pnh_sendsms($membr_mobno,$mem_msg,$fran['franchise_id'],$membr_id,0);
                                
			}
		}

	}

	/**
	 * function to check wheather member is registered
	 * @param unknown_type $from
	 * @param unknown_type $msg
	 */
	function is_mbr_regd($from,$msg)
	{
		if(empty($from) || strlen($from)<10)
			$this->pdie("Invalid mobile number");
		$fran=$this->db->query("select * from pnh_m_franchise_info where (login_mobile1=? and login_mobile1!=0) or (login_mobile2=? and login_mobile2!=0) and is_suspended=0",array($from,$from))->row_array();
		if(empty($fran))
			$this->pdie("Invalid mobile number");
		list($call,$mbrmob)=explode(" ",$msg);
		$membr_mobno=@trim($mbrmob);
		if(empty($membr_mobno)|| strlen($membr_mobno)<10)
			$this->pdie("Please enter a valid mobile number");

		//check is member registerd
		$is_membr_regd=$this->db->query("select *,date(from_unixtime(created_on)) as reg_on from pnh_member_info where mobile=? limit 1",$membr_mobno)->row_array();

		if($is_membr_regd)
		{
			$mbr_name=$is_membr_regd['first_name'].''.$is_membr_regd['last_name'];
			//$reg_on=$is_membr_regd['reg_on'];
			$reg_on=date("d M Y",$is_membr_regd['created_on']);
			$this->pdie("Yes,$mbr_name is  member with Store King from ".$reg_on );
		}
		else
			$this->pdie("No,not Registered");

	}
	/**Prepaid coupon activating SMS---START
	 * function to allot coupen to members/customers by sms
	* @param unknown_type $from
	* @param unknown_type $msg
	*/
	function fran_alloting_voucher_tomem($from,$msg,$fran)
	{
		if(empty($from) || strlen($from)<10)
			$this->pdie("Invalid mobile number");
		$fran=$this->db->query("select * from pnh_m_franchise_info where (login_mobile1=? and login_mobile1!=0) or (login_mobile2=? and login_mobile2!=0) and is_suspended=0",array($from,$from))->row_array();
		if(empty($fran))
			$this->pdie("Invalid mobile number");
		$is_prepaid_fran=$this->erpm->is_prepaid_franchise($fran['franchise_id']);
		if(empty($is_prepaid_fran))
			$this->pdie("Not Autherised");
			
		list($call,$coupon_code)=explode(" ",$msg);
		$voucher_det_str = str_replace($call,'',$msg);
		$voucher_det_str = explode("/n",$voucher_det_str);
		$voucher_act_det = explode(" ",$voucher_det_str[0]);
		$voucher_slno=@trim($voucher_act_det[1]);
		$member_phno=@trim($voucher_act_det[2]);
		$memid=@trim($voucher_act_det[3]);
		$memname=@trim($voucher_act_det[4]);
		$valid_memdet=0;
		$new_mem=0;
		if(empty($voucher_slno))
			$this->pdie("Invalid Syntax.Enter Voucher slno.");
		if(empty($member_phno))
			$this->pdie("Enter Member Mobile no");
		if(strlen($member_phno)!=10)
			$this->pdie("Invalid Mobile no entered");
		//check is coupon alloted to franchise
		$is_voucher_assigned_tofran=$this->db->query("SELECT * FROM pnh_t_book_allotment where franchise_id=? and status=2",$fran['franchise_id']);

		if(empty($is_voucher_assigned_tofran))
			$this->pdie("Not Authorized");

		//check member is registerd
		$is_membr_valid=$this->db->query("select * from pnh_member_info where mobile=?",$member_phno)->row_array();
			
			
		if($is_membr_valid=$this->db->query("select * from pnh_member_info where mobile=?",$member_phno)->num_rows()==0)
		{
			$new_mem=1;
			if(empty($memid))
				$this->pdie("Since the mobile no is not registered,Please enter member id of the alloted range.");
			if(empty($memname))
				$this->pdie("Enter the member name");

			$is_valid_midrange=$this->db->query("SELECT * FROM pnh_m_allotted_mid WHERE ? BETWEEN mid_start AND mid_end AND franchise_id=? group by franchise_id order by id desc",array($memid,$fran['franchise_id']));
			if($is_valid_midrange->num_rows()!=0)
			{
				$is_valid_mid=$this->db->query("select * from pnh_member_info where pnh_member_id=?",$memid);
				if($is_valid_mid->num_rows()!=0)
				{
					$this->pdie("This member id $memid is already alloted");
					$valid_memdet=0;
				}
				else
					$valid_memdet=1;
			}
			else
					
				$this->pdie("Please enter member id of the alloted range");
		}
		else
			$new_mem=0;
			
		if($is_membr_valid=$this->db->query("select * from pnh_member_info where mobile=?",$member_phno)->num_rows()!=0 && $memid)
			$this->pdie("Member Mobile Number $member_phno is already registered, please input a new Mobile number");
		else
			$valid_memdet=1;

			
		$voucher_slnos=array_filter(array_unique(explode(',',$voucher_slno)));
		$voucher_count=sizeof($voucher_slnos);
		$redeemd_voucher_count=0;
		$voucher_value=0;
		$updated_voucher=array();
		$updated_voucher['valid']=array();
		$updated_voucher['invalid']=array();
		$updated_voucher['already_activated']=array();
		$updated_voucher['not_alloted']=array();
		$updated_voucher['valid_scratchcode']=array();

		if($valid_memdet==1)
		{

			foreach($voucher_slnos as $v)
			{
				$valid_voucher_res=$this->db->query("SELECT * FROM pnh_t_voucher_details WHERE voucher_serial_no=? AND franchise_id=?",array($v,$fran['franchise_id']));
				if($valid_voucher_res->num_rows())
				{
					foreach($valid_voucher_res->result_array() as $voucher_res)
					{

						$v_slno=$voucher_res['voucher_serial_no'];
						$v_code=$voucher_res['voucher_code'];
						if(!$voucher_res['is_activated'] && $voucher_res['is_alloted'] && $voucher_res['status']==2)
						{
							$voucher_value+=$voucher_res['value'];
							$voucher_scratchcode=$voucher_res['voucher_code'];
							$v_cusvalue=$voucher_res['customer_value'];
							if ($new_mem)
							{
									
								if($this->db->query("select * from pnh_member_info where pnh_member_id=?",$memid)->num_rows()==0)
									$this->db->query("insert into pnh_member_info(pnh_member_id,mobile,first_name)values(?,?,?)",array($memid,$member_phno,$memname));
								$this->db->query("update pnh_t_voucher_details set is_activated=1,member_id=?,activated_on=now(),status=3 where voucher_serial_no = ? and franchise_id=?",array($memid,$v_slno,$fran['franchise_id']));
								$this->db->query("insert into pnh_voucher_activity_log(voucher_slno,franchise_id,member_id,transid,debit,credit,status)values(?,?,?,?,?,?,0)",array($v_slno,$fran['franchise_id'],$memid,0,0,$v_cusvalue));
									
							}
							else
							{
									
								$is_membr_valid=$this->db->query("select * from pnh_member_info where mobile=?",$member_phno)->row_array();
								$mid=$is_membr_valid['pnh_member_id'];
								$this->db->query("update pnh_t_voucher_details set is_activated=1,member_id=?,activated_on=now(),status=3 where voucher_serial_no = ? and franchise_id=?",array($mid,$v_slno,$fran['franchise_id']));
								$this->db->query("insert into pnh_voucher_activity_log(voucher_slno,franchise_id,member_id,transid,debit,credit,status)values(?,?,?,?,?,?,0)",array($v_slno,$fran['franchise_id'],$mid,0,0,$v_cusvalue));
							}

							array_push($updated_voucher['valid'],$voucher_res['voucher_serial_no']);
							array_push($updated_voucher['valid_scratchcode'], $voucher_res['voucher_code']);
							$redeemd_voucher_count++;
						}
						if($voucher_res['is_activated']==1)
						{
							array_push($updated_voucher['already_activated'],$voucher_res['voucher_serial_no']);
						}

						if($voucher_res['status']<=1)
						{
							array_push($updated_voucher['not_alloted'], $voucher_res['voucher_serial_no']);
						}
					}
				}
				else
					array_push($updated_voucher['invalid'], $v);
			}

			if ($redeemd_voucher_count==$voucher_count)
			{
				/*if ($new_mem)
				 {
					
				if($this->db->query("select * from pnh_member_info where pnh_member_id=?",$memid)->num_rows()==0)
					$this->db->query("insert into pnh_member_info(pnh_member_id,mobile,first_name)values(?,?,?)",array($memid,$member_phno,$memname));
				}
				else
				{
					
				$is_membr_valid=$this->db->query("select * from pnh_member_info where mobile=?",$member_phno)->row_array();
				$mid=$is_membr_valid['pnh_member_id'];
				$this->db->query("update pnh_t_voucher_details set is_activated=1,member_id=?,activated_on=now(),status=3 where voucher_serial_no = ? and franchise_id=?",array($mid,$v_slno,$fran['franchise_id']));
				$this->db->query("insert into pnh_voucher_activity_log(voucher_slno,franchise_id,member_id,transid,debit,credit,status)values(?,?,?,?,?,?,0)",array($v_slno,$fran['franchise_id'],$mid,0,0,$v_cusvalue));
				}*/
				if($updated_voucher['valid'] && $updated_voucher['valid_scratchcode'])
				{
					if(!$new_mem)
						$membr_name=$is_membr_valid['first_name'].' '.$is_membr_valid['last_name'];
					else
						$membr_name=$memname;
					$this->erpm->pnh_sendsms($member_phno,"Dear $membr_name,Congragulation on purchase of Rs $voucher_value StoreKing Voucher, Your Secret Code  is $voucher_scratchcode for Voucher Id $voucher_slno.Happy Shopping",$member_phno,0,1);
					echo "Dear $membr_name,Congragulation on purchase of Rs $voucher_value StoreKing Voucher, Your Secret Code  is ".implode(',',$updated_voucher['valid_scratchcode'])." for Voucher Id  ".implode(',',$updated_voucher['valid']).".Happy Shopping";
					$this->erpm->pnh_sendsms($member_phno,"Sample order sms is [Productid]*[Qty],[Productid]*[Qty] " .implode(',',$updated_voucher['valid_scratchcode']). " enter your product & qty and forward to 92 4340 4342.",$member_phno,0,1);

					//$this->pdie("Voucher  ".implode(',',$updated_voucher['valid'])." is alloted to member successfully.Happy Franchising");
					$this->erpm->pnh_sendsms($from,"Voucher  ".implode(',',$updated_voucher['valid'])." is alloted to member successfully.Happy Franchising",$from,0,2);
				}
			}
			else
			{
				if($updated_voucher['not_alloted'])
				{
					//$this->pdie("The alloted Voucher Book ".implode(',',$updated_voucher['valid'])." is not activated yet!!! Please make a voucher value payment and continue franchising");
					$this->erpm->pnh_sendsms($from,"The alloted Voucher Book ".implode(',',$updated_voucher['valid'])." is not activated yet!!! Please make a voucher value payment and continue franchising",$from,0,2);
					echo "The alloted Voucher Book ".implode(',',$updated_voucher['not_alloted'])." is not activated yet!!! please make a voucher value payment and continue franchising";
				}
				if($updated_voucher['already_activated'])
				{
					$this->pdie("Voucher ".implode(',',$updated_voucher['already_activated']). " is already activated,please use another voucher number.");
				}
				if($updated_voucher['invalid'])
				{
					$this->pdie("Voucher ".implode(',',$updated_voucher['invalid'])." is invalid, please enter valid Serial Number.");
				}
			}
		}
	}
	/**
	 * function to place order of prepaid menu via SMS by customer/member
	 * @param unknown_type $from
	 * @param unknown_type $msg
	 */
	function create_prepaidorder($from,$msg,$membr)
	{
			
		//check is_membr
		$is_strkng_membr=$this->db->query("select * from pnh_member_info where mobile=? limit 1",$from)->row_array();
		if(empty($is_strkng_membr) )
			$this->pdie("Invalid Mobile number");
		$msg=str_replace("* ","*",$msg);
		$msg=str_replace(" *","*",$msg);
		$msg_det=explode(' ',$msg);
		$prod_det=$msg_det[0];//product n qty
		$voucher_code=$msg_det[1];//coupon code

		$v_type= strlen($voucher_code);
		$secret_codetype=ctype_alpha($voucher_code);//cheks the entered secret code is charecters
			
		if($secret_codetype)
			$vcode=0;
		else
			$vcode=1;
			
		if(empty($prod_det))
			$this->pdie("Invalid syntax");
		if(empty($voucher_code))
			$this->pdie("Please Enter Voucher Scratch Code");
			
		$this->db->query('insert into pnh_sms_log(sender,msg,franchise_id,type,created_on)values(?,?,?,"VOUCHER",unix_timestamp(now()))',array($from,$msg,$is_strkng_membr['franchise_id']));

		if($vcode==0)
		{
			if($voucher_code!='usebal')
				$this->pdie("Invalid syntax,please input 'usebal' to redeem your balance.");
		}
		//split coupon scratch codes
		$voucher_codes=array_filter(array_unique(explode(',',$voucher_code)));
		$voucher_count=sizeof($voucher_codes);//count of coupon codes enterd by user
		$valid_voucher_count=0;
		$voucher_value=0;
			
		if($vcode==1)
		{

			$fid=$this->db->query("SELECT GROUP_CONCAT(franchise_id) AS franchise_id,IF(is_alloted=1,1,0),IF(is_activated=1,1,0),IF(status=3,3,0)FROM pnh_t_voucher_details WHERE voucher_code IN(?)",$voucher_codes)->row()->franchise_id;
			if(empty($fid))
				$this->pdie("Not Authorized");
			$fran=$this->db->query("select * from pnh_m_franchise_info where franchise_id in(?)",$fid)->row_array();
			$is_voucheroffran=$this->db->query("select * from pnh_t_voucher_details where voucher_code in($voucher_code)group by franchise_id");
			if($is_voucheroffran->num_rows()>1)
				$this->pdie("Vouchers of two different stores can't be activated");

			$voucher_code_used = array();
			foreach($voucher_codes as $v_code)
			{

				$voucher_margin=$this->db->query("select voucher_margin from pnh_t_voucher_details where voucher_code=?",$v_code);

				$is_voucher_activated=$this->db->query("select * from pnh_t_voucher_details where voucher_code=? and member_id=?  and is_activated=1 and status=3",array($v_code,$is_strkng_membr['pnh_member_id']))->row_array();

				if(!empty($is_voucher_activated))
				{
					$voucher_offran=$is_voucher_activated['franchise_id'];
					$voucher_value+=$is_voucher_activated['customer_value'];
					$voucher_code_used[$v_code] = $is_voucher_activated['customer_value'];
					$valid_voucher_count++;//count of correct coupon code entered by user
				}
			}

			if($voucher_count!=$valid_voucher_count)
				$this->pdie("Please Enter Correct Voucher Code");
		}
		else
			$fran=$this->db->query("select f.* from pnh_t_voucher_details a join pnh_m_franchise_info f on f.franchise_id=a.franchise_id where member_id=? and status=5 and is_activated=1",$is_strkng_membr['pnh_member_id'])->row_array();
		//split productids-qty
		$pnh_ids=array();
		$prod_det=explode(',',$prod_det);
		$pid_count=sizeof($prod_det);
		$items=array();
		foreach($prod_det as $p)
		{
			$pi=explode("*",$p);

			$it['pid']=trim($pi[0])+1-1;

			if(count($pi)!=2)
				$it['qty']=1;
			else
				$it['qty']=$pi[1];
			$items[]=$it;
			$pnh_ids[]=$it['pid'];
		}

		//validate the voucher menu link block
		$voucher_status=3;
		$dealmenu=array();
		$total_ordered_val=0;
		foreach($pnh_ids as $pid)
		{
			$menu_det=$this->db->query("select c.id,c.name,b.orgprice from king_deals a join king_dealitems b on b.dealid=a.dealid join pnh_menu c on c.id=a.menuid where b.pnh_id=?",$pid)->row_array();
			$dealmenu[]=$menu_det['id'];
			$total_ordered_val+=$menu_det['orgprice'];
		}

		if($vcode==0)
		{
			$voucher_codes=array();
			$balance_voucher_val=0;
			$voucher_status=5;
			$balance_vouchers=$this->db->query("select * from pnh_t_voucher_details where status=5 and customer_value!=0 and member_id=?",$is_strkng_membr['pnh_member_id'])->result_array();
			if($balance_vouchers)
			{
				foreach($balance_vouchers as $bv)
				{
					$balance_voucher_val+=$bv['customer_value'];
					if($balance_voucher_val>=$total_ordered_val)
						break;
					$voucher_codes[]=$bv['voucher_code'];
				}
			}
		}

		if($voucher_codes)
		{
			$vmenu_list=array();
			foreach($voucher_codes as $v=> $vscode)
			{
				$vdet=$this->db->query("select b.book_id,d.franchise_id
						from pnh_t_voucher_details a
						join pnh_t_book_voucher_link b on b.voucher_slno_id = a.id
						join pnh_t_book_allotment d on d.book_id=b.book_id
						where voucher_code=? and member_id=?  and is_activated=1 and a.status=?
						group by b.book_id",array($vscode,$is_strkng_membr['pnh_member_id'],$voucher_status))->row_array();
					
				//get the book menus
				$vmenus=$this->db->query("select id,name from  pnh_menu where id in (select menu_ids from pnh_t_book_details a join pnh_m_book_template b on b.book_template_id=a.book_template_id where a.book_id=?)",$vdet['book_id'])->result_array();
					
				foreach($vmenus as $m)
				{
					//check used multiple voucher same menu
					if($v > 0)
					{
						if(!in_array($m['id'],$vmenu_list))
							$this->pdie("Sorry not able to use the different menu vouchers at time");
					}

					//check voucher menu linked to franchise
					$vmenu_list[]=$m['id'];
					$fmenu=$this->db->query("select * from pnh_franchise_menu_link where fid=? and menuid=? and status=1",array($vdet['franchise_id'],$m['id']))->row_array();
					if(empty($fmenu))
					{
						$this->pdie("Sorry not able to place a order voucher menu not assigned for voucher franchise");
					}

					//check voucher menu orderd deal menu same
					if(!in_array($m['id'],$dealmenu))
					{
						$this->pdie("Voucher ".$vscode." only for ".$m['name']." products");
					}

					//check ordered deal menus are same
					if(count(array_unique($dealmenu))>1)
						$this->pdie("Voucher ".$vscode." only for ".$m['name']." products");
				}
					
				foreach($dealmenu as $mk=>$dm)
				{
					//check ordered deal menu linked to franchise
					$fmenu=$this->db->query("select * from pnh_franchise_menu_link where fid=? and menuid=? and status=1",array($vdet['franchise_id'],$dm))->row_array();
					if(empty($fmenu))
					{
						$this->pdie("Sorry not able to  place a order product menu not assined for voucher franchise ");
					}

					//check ordered deal menus are same
					if(count(array_unique($dealmenu))>1)
						$this->pdie("Sorry not able to place the different menu orders at time");

					//check if the deal is voucher book
					if($dm==VOUCHERMENU)
					{
						$this->pdie("Sorry not able to place the order");
					}
				}
			}
		}
		//validate the voucher menu link block
			
		$fid=$this->db->query("SELECT GROUP_CONCAT(franchise_id) AS franchise_id,IF(is_alloted=1,1,0),IF(is_activated=1,1,0),IF(status=3,3,0)FROM pnh_t_voucher_details WHERE voucher_code IN(?)",$voucher_codes)->row()->franchise_id;
		if(empty($fid))
			$this->pdie("Not Authorized");


		$fran=$this->db->query("select * from pnh_m_franchise_info where franchise_id in(?)",$fid)->row_array();


		$total=0;$d_total=0;$c_total=0;
		$itemids=array();
		$itemnames=array();


		$voucher_code_used = array();


		foreach($items as $i=>$item)
		{
			//check the order product menu is linked to coupon assigned franchise
			$prod=$this->db->query("select i.*,d.publish,m.menu_margin from king_dealitems i join king_deals d on d.dealid=i.dealid JOIN `pnh_prepaid_menu_config`m ON m.menu_id=d.menuid where i.is_pnh=1 and i.pnh_id=? and i.pnh_id!=0 AND is_active=1",$item['pid'])->row_array();
			if(empty($prod))
				$this->pdie("There is no product with ID : ".$item['pid']);
			if($prod['publish']!=1)
				$this->pdie("Product {$prod['name']} is not available");
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
				$items[$i]['discount']=$items[$i]['mrp']/100*$voucher_margin;
			//$items[$i]['discount']=$voucher_margin;
			$total+=$items[$i]['price']*$items[$i]['qty'];
			$d_total+=($items[$i]['mrp']-$items[$i]['discount'])*$items[$i]['qty'];
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
				where voucher_code not in (?) and member_id=?  and is_activated=1 and a.status=3
				group by voucher_serial_no",array($voucher_code,$is_strkng_membr['pnh_member_id']));

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
					where voucher_code not in (?) and member_id=?  and is_activated=1 and a.status in (3,5)
					group by voucher_serial_no",array($voucher_code,$is_strkng_membr['pnh_member_id']));

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
				$this->pdie("Insufficient credit! Total value of purchase is Rs $c_total and voucher value is Rs $ts_vouchervalue and overall activated voucher value is $voucher_value $vbal");
			}
		}


		$userid=$is_strkng_membr['user_id'];
		$transid=strtoupper("PNH".random_string("alpha",3).$this->erpm->p_genid(5));
		$this->db->query("insert into king_transactions(transid,amount,paid,mode,init,actiontime,is_pnh,franchise_id,voucher_payment) values(?,?,?,?,?,?,?,?,1)",array($transid,$c_total,$d_total,3,time(),time(),1,$fran['franchise_id']));


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

				$is_alloted=$this->db->query("select * from pnh_t_voucher_details where franchise_id=? and voucher_code=? and is_alloted=1 and is_activated=1 and status=3 ",array($fran['franchise_id'],$v_code))->row_array();

				$cus_value = $is_alloted['customer_value'];
				$new_cus_value=($is_alloted['customer_value']-$ttl_voucher_amt_req);
					
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
								//108-300 = -192
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
		$this->erpm->do_trans_changelog($transid,"PNH Order placed through SMS by $from",$userid);
		if($part_redeemed_vouch_amt)
			$v_bal=$part_redeemed_vouch_amt;
		else
			$v_bal=0;

		$given_vslnos=implode(',',$voucher_code_used['gven_secretcode']);
		$othr_vslnos=implode(',',$voucher_code_used['othr_secretcode']);
		$v_balamt=$this->db->query("SELECT SUM(customer_value) AS voucher_bal FROM pnh_t_voucher_details WHERE STATUS in(3,5) AND member_id=?",$is_strkng_membr['pnh_member_id'])->row()->voucher_bal;
                
                // =======================< MEMBER ORDER SMS >====================================
		$this->erpm->pnh_sendsms($from,"your balance after purchase is Rs $v_balamt,redeemed vouchers for rs $c_total are $given_vslnos,$othr_vslnos .Happy Shopping",$from,0,1);
		//echo "your balance after purchase is Rs $v_balamt,redeemed vouchers for rs $c_total are $given_vslnos,$othr_vslnos.Happy Shopping";
		// =======================< MEMBER ORDER SMS >====================================
	}

	function cancel_prepaidorder($from,$msg,$membr)
	{
		//check is_membr
		$is_strkng_membr=$this->db->query("select * from pnh_member_info where mobile=? limit 1",$from)->row_array();
		if(empty($is_strkng_membr) )
			$this->pdie("Invalid Mobile number");
			
		list($call,$transid)=explode(" ",$msg);
		if(empty($transid))
			$this->pdie("No Order ID provided");
		if($this->db->query("select 1 from king_transactions where transid=?",$transid)->num_rows()==0)
			$this->pdie("Invalid Order ID");
		if($this->db->query("select 1 from king_orders where transid=? and status=3",$transid)->num_rows()!=0)
			$this->pdie("One or more of the items in Order $transid is already cancelled. Please contact customer support");
		if($this->db->query("select 1 from king_orders where transid=? and status=2",$transid)->num_rows()!=0)
			$this->pdie("Order $transid is already invoiced and cannot be cancelled");
		if($this->db->query("select 1 from king_orders where transid=? and status=1",$transid)->num_rows()!=0)
			$this->pdie("Order $transid is already shipped and cannot be cancelled");
		$this->erpm->do_trans_changelog($transid,"PNH Order cancelled through SMS");
		$this->db->query("update king_orders set status=3 where transid=?",$transid);
		$this->erpm->do_trans_changelog($transid,"PNH Order cancelled through SMS",$is_strkng_membr['pnh_member_id']);
		$trans=$this->db->query("select * from king_transactions where transid=?",$transid)->row_array();
		$this->db->query("insert into t_refund_info(transid,amount,status,created_on,created_by) values(?,?,?,?,?)",array($transid,$trans['amount'],1,time(),1));
		$rid=$this->db->insert_id();
		//in voucher_activity log table
		foreach($this->db->query("select * from pnh_voucher_activity_log  where transid=?",$transid)->result_array() as $v=>$vdet)
		{
			$v_slno=$vdet['voucher_slno'];
			$transid=$vdet['transid'];
			$ord_debit=$vdet['credit'];
			$ord_credit=$vdet['debit'];

			$v_details=$this->db->query("select * from pnh_t_voucher_details where voucher_serial_no=?",$v_slno)->row_array();
			$cus_value=$v_details['customer_value']+$vdet[$v_slno]['debit'];
			$fran_value=$cus_value-($cus_value*$v_margin/100);
			$this->db->query("update pnh_voucher_activity_log set debit=?,credit=?,status=2 where transid=?",array($ord_debit,$ord_credit,$transid));
			$this->db->query("update pnh_t_voucher_details set status=3,customer_value=?,franchise_value=? where voucher_serial_no=?",array($ord_credit,$fran_value,$v_slno));
		}
		//in orders table
		foreach($this->db->query("select id,quantity from king_orders where transid=?",$transid)->result_array() as $i=>$ord)
		{
			$o=$ord['id'];
			$qty=$ord['quantity'];
			$this->db->query("insert into t_refund_order_item_link(refund_id,order_id,qty) values(?,?,?)",array($rid,$o,$qty));
			$this->db->query("update king_orders set status=3,actiontime=".time()." where id=? limit 1",$o);
		}
		$this->erpm->pnh_fran_account_stat($vdet['franchise_id'],0,$trans['amount'],"Refund - Order $transid cancelled","refund",$transid);
		$nbalance=$this->db->query("select current_balance as b from pnh_m_franchise_info where franchise_id=?",$vdet['franchise_id'])->row()->b;
		echo "Order $transid is cancelled and Rs {$trans['amount']} is credited back to your account. Your new balance is Rs $nbalance";
	}
	//Prepaid coupon activating sms END

	/**
	 * Member Scheme Activation SMS--START
	 *function credit  to franchise on imei_slno sms
	 * @param unknown_type $from
	 * @param unknown_type $msg
	 * @param unknown_type $fran
	 */
	function credit_on_imei($from,$msg,$fran)
	{
		if(empty($from) || strlen($from)<10)
			$this->pdie("Invalid mobile number");

		$fran=$this->db->query("select * from pnh_m_franchise_info where (login_mobile1=? and login_mobile1!=0) or (login_mobile2=? and login_mobile2!=0)",array($from,$from))->row_array();
		if(empty($fran))
			$this->pdie("Not Authorized");
		$is_membrsch_active=$this->db->query("select * from imei_m_scheme where is_active=1 and franchise_id=? and unix_timestamp(now()) between sch_apply_from and scheme_to order by created_on desc limit 1",$fran['franchise_id'])->row_array();
		if(empty($is_membrsch_active))
			$this->pdie("Member Scheme is not activated");

		//	$msg=trim(str_ireplace('imei',"",$msg));
		$imei_det=explode(" ",$msg);
		$imei_mem_mobno=@$imei_det[0];
		$imei_slnos_str=@$imei_det[1];
		$imei_slnos_authno=@$imei_det[2];

		if(empty($imei_det))
			$this->pdie("Invalid Syntax");
		if(empty($imei_mem_mobno))
			$this->pdie("Member Mobile number not provided");
		if(empty($imei_slnos_str))
			$this->pdie("IMEI number not provided");
		if(strlen($imei_slnos_authno)!=10)
			$this->pdie("Invalid party mobile number");
		$is_mem_reg=$this->db->query("select * from pnh_member_info where mobile=?",$imei_mem_mobno)->row_array();
		if(empty($is_mem_reg))
			$this->pdie("Mobileno '$imei_mem_mobno' not registered");
		$imei_slnos=array_filter(array_unique(explode(',',$imei_slnos_str)));
		$imei_slno_cnt=sizeof($imei_slnos);
		$valid_imeislno=0;
		$mem_mobno = $is_mem_reg['mobile'];
		$updated_imei_list = array();
		$updated_imei_list['invalid'] = array();
		$updated_imei_list['valid'] = array();
		$updated_imei_list['already_updated'] = array();
		$updated_imei_list['membersch_knocked'] = array();

		$partial_activation = 0;
		$total_margin_cr = 0;
		$ttl_activated = 0;
		foreach ($imei_slnos as $imei_slno)
		{
			$is_imei_knockedfrm_msch=$this->db->query("SELECT 1 FROM t_imei_no t JOIN king_orders o ON o.id=t.order_id WHERE imei_no=? AND o.imei_scheme_id=0",$imei_slno)->row();
			if(empty($is_imei_knockedfrm_msch))
			{
				$is_valid_imeino_res=$this->db->query("SELECT i.id as imei_ref_id,o.id as orderid,i.is_imei_activated,i.imei_no,p.product_id,o.time,l.product_name,o.quantity,o.imei_reimbursement_value_perunit,o.id,inv.invoice_no,t.paid,t.franchise_id,o.imei_reimbursement_value_perunit as msch_amt
						FROM king_orders o
						JOIN king_transactions t ON t.transid=o.transid
						JOIN m_product_deal_link p ON p.itemid=o.itemid
						JOIN t_imei_no i ON i.order_id=o.id
						JOIN king_dealitems d ON d.id=o.itemid
						JOIN king_deals s ON s.dealid=d.dealid
						JOIN m_product_info l ON l.product_id=p.product_id
						JOIN king_invoice inv ON inv.transid=o.transid
						JOIN imei_m_scheme m ON m.menuid=s.menuid
						JOIN shipment_batch_process_invoice_link bi ON bi.invoice_no = inv.invoice_no AND bi.shipped = 1
						WHERE o.status in (1,2) AND imei_no=?
						AND t.franchise_id=? and inv.invoice_status = 1 and i.status = 1
						GROUP BY i.id
						ORDER BY l.product_name ASC",array($imei_slno,$fran['franchise_id']));
				if($is_valid_imeino_res->num_rows())
				{
					foreach($is_valid_imeino_res->result_array() as $valid_imei)
					{
							
						if(!$valid_imei['is_imei_activated'])
						{

							$this->db->query("update king_orders set member_scheme_processed=1 where id=? and member_scheme_processed=0",$valid_imei['orderid']);
							$this->db->query("insert into t_invoice_credit_notes(franchise_id,type,ref_id,invoice_no,amount,created_on)value(?,2,?,?,?,now())",array($valid_imei['franchise_id'],$valid_imei['imei_ref_id'],$valid_imei['invoice_no'],$valid_imei['msch_amt']));
							$imei_creditid=$this->db->insert_id();
							if($imei_slnos_authno)
								$this->db->query("update t_imei_no set is_imei_activated = 1,activated_mob_no=?,imei_activated_on = now(),ref_credit_note_id=? where imei_no = ? and product_id = ? and status = 1 ",array($imei_slnos_authno,$imei_creditid,$valid_imei['imei_no'],$valid_imei['product_id']));
							else
								$this->db->query("update t_imei_no set is_imei_activated = 1,activated_mob_no=?,imei_activated_on = now(),ref_credit_note_id=? where imei_no = ? and product_id = ? and status = 1 ",array($mem_mobno,$imei_creditid,$valid_imei['imei_no'],$valid_imei['product_id']));

							$this->db->query("insert into pnh_franchise_account_summary(franchise_id,action_type,credit_note_id,member_id,invoice_no,credit_amt,status,remarks,created_on)value(?,?,?,?,?,?,?,?,now())",array($valid_imei['franchise_id'],7,$imei_creditid,$is_mem_reg['pnh_member_id'],$valid_imei['invoice_no'],$valid_imei['msch_amt'],1,'imeino : '.$valid_imei['imei_no']));
							$total_margin_cr += $valid_imei['msch_amt'];
							array_push($updated_imei_list['valid'],$valid_imei['imei_no']);
							$ttl_activated ++;
						}else
						{
							array_push($updated_imei_list['already_updated'],$valid_imei['imei_no']);
							$partial_activation = 1;
						}

					}
				}
			}else
			{
				array_push($updated_imei_list['membersch_knocked'],$imei_slno);
				$partial_activation = 1;
			}
		}

		$fr_reply_msg = '';

		if($updated_imei_list['valid'])
		{
			$fr_reply_msg .= "Congratulations!!! Your IMEINO : ".implode(',',$updated_imei_list['valid'])." Activated ";
		}
		if($ttl_activated!=$imei_slno_cnt)
		{
			if($updated_imei_list['already_updated'])
			{
				$fr_reply_msg .= "IMEINO Activation Failed, IMEINO : ".implode(',',$updated_imei_list['already_updated'])." Already Activated.";
			}

			if($updated_imei_list['invalid'])
			{
				$fr_reply_msg .= "IMEINO Activation Failed, IMEINO : ".implode(',',$updated_imei_list['invalid'])." Not Available for Activation.";
			}

			if($updated_imei_list['membersch_knocked'])
			{
				$fr_reply_msg .= "IMEINO Activation Failed, IMEINO : ".implode(',',$updated_imei_list['membersch_knocked'])." Activation Knocked For this deal.";
					
			}
		}
		if($total_margin_cr)
			$fr_reply_msg .= "and Amount of Rs ".$total_margin_cr." has been credited to your account.";

		if($fr_reply_msg)
			$this->erpm->pnh_sendsms($from,$fr_reply_msg,$fran['franchise_id']);


		echo $fr_reply_msg;
	}

	function credit_on_imei_newmem($from,$msg,$fran)
	{
			
		if(empty($from) || strlen($from)<10)
			$this->pdie("Invalid mobile number");
			
		$fran=$this->db->query("select * from pnh_m_franchise_info where (login_mobile1=? and login_mobile1!=0) or (login_mobile2=? and login_mobile2!=0)",array($from,$from))->row_array();
		if(empty($fran))
			$this->pdie("Not Authorized");
		$is_membrsch_active=$this->db->query("select * from imei_m_scheme where is_active=1 and franchise_id=? and ? between sch_apply_from and scheme_to order by created_on desc limit 1",array($fran['franchise_id'],time()))->row_array();
		if(empty($is_membrsch_active))
			$this->pdie("Member Scheme is not activated");
		$msg=trim(str_ireplace('a',"",$msg));
		$imei_det=explode(" ",$msg);
		$imei_memid=@$imei_det[0];
		$imei_slno=@$imei_det[1];
		$imei_mobno=@$imei_det[2];
		$imei_name=@$imei_det[3];
			
		if(empty($imei_det))
			$this->pdie("Invalid Syntax");
		if(empty($imei_memid))
			$this->pdie("Member Id not provided");
		//check the member id entered is in the range between alloted member id of the franchise
		if($imei_memid)
			$is_membr_id_inrange=$this->db->query("SELECT * from pnh_m_allotted_mid  WHERE franchise_id=? and ? between mid_start and mid_end",array($fran['franchise_id'],$imei_memid));

		if($is_membr_id_inrange->num_rows()!=0)
			$is_membr_id_valid=$this->db->query("SELECT * from pnh_member_info WHERE pnh_member_id=?",$imei_memid);
			
		if($is_membr_id_inrange->num_rows()==0 )
			$this->pdie("Invalid Member ID Entered");
		if($is_membr_id_valid->num_rows()!=0)
			$this->pdie("This Member ID is already alloted");

		if(empty($imei_slno))
			$this->pdie("IMEI number not provided");


		$imei_slnos=array_filter(array_unique(explode(',',$imei_slno)));
		$imei_slno_cnt=sizeof($imei_slnos);
		$valid_imeislno=0;
		$mem_mobno =$imei_mobno;
		$updated_imei_list = array();
		$updated_imei_list['invalid'] = array();
		$updated_imei_list['valid'] = array();
		$updated_imei_list['already_updated'] = array();
		$updated_imei_list['membersch_knocked'] = array();
			
		$partial_activation = 0;
		$total_margin_cr = 0;
		$ttl_activated = 0;
		if($imei_slnos)
		{

			foreach ($imei_slnos as $imei_slno)
			{
				$is_imei_knockedfrm_msch=$this->db->query("SELECT 1 FROM t_imei_no t JOIN king_orders o ON o.id=t.order_id WHERE imei_no=? AND o.imei_scheme_id=0",$imei_slno)->row();
					
				if(empty($is_imei_knockedfrm_msch))
				{
					$is_valid_imeino_res=$this->db->query("SELECT i.id as imei_ref_id,o.id as orderid,i.is_imei_activated,i.imei_no,p.product_id,o.time,l.product_name,o.quantity,o.imei_reimbursement_value_perunit,o.id,inv.invoice_no,t.paid,t.franchise_id,o.imei_reimbursement_value_perunit as msch_amt
							FROM king_orders o
							JOIN king_transactions t ON t.transid=o.transid
							JOIN m_product_deal_link p ON p.itemid=o.itemid
							JOIN t_imei_no i ON i.order_id=o.id
							JOIN king_dealitems d ON d.id=o.itemid
							JOIN king_deals s ON s.dealid=d.dealid
							JOIN m_product_info l ON l.product_id=p.product_id
							JOIN king_invoice inv ON inv.transid=o.transid
							JOIN imei_m_scheme m ON m.menuid=s.menuid
							JOIN shipment_batch_process_invoice_link bi ON bi.invoice_no = inv.invoice_no AND bi.shipped = 1
							WHERE o.status in (1,2) AND imei_no=?
							AND t.franchise_id=? and inv.invoice_status = 1 and i.status = 1
							GROUP BY i.id
							ORDER BY l.product_name ASC",array($imei_slno,$fran['franchise_id']));
					if($is_valid_imeino_res->num_rows() )
					{

						if(!$valid_imei['is_imei_activated'])
						{
							$total_margin_cr += $valid_imei['msch_amt'];
							array_push($updated_imei_list['valid'],$valid_imei['imei_no']);
							$ttl_activated ++;
						}else
						{
							array_push($updated_imei_list['already_updated'],$valid_imei['imei_no']);
							$partial_activation = 1;
						}
					}
					else
					{
						array_push($updated_imei_list['invalid'],$imei_slno);
						$partial_activation = 1;
					}

				}
				else
				{
					array_push($updated_imei_list['membersch_knocked'],$imei_slno);
					$partial_activation = 1;
				}
				$fr_reply_msg = '';
				if($updated_imei_list['valid'])
				{
					$fr_reply_msg .="Congratulations!!! IMEINO : ".implode(',',$updated_imei_list['valid'])." Activated ";
				}
				if($ttl_activated!=$imei_slno_cnt)
				{
					if($updated_imei_list['already_updated'])
					{
						$fr_reply_msg .= "IMEINO Activation Failed, IMEINO : ".implode(',',$updated_imei_list['already_updated'])." Already Activated.";
					}

					if($updated_imei_list['invalid'])
					{
						$fr_reply_msg .= "IMEINO Activation Failed, IMEINO : ".implode(',',$updated_imei_list['invalid'])." Not Available for Activation.";
					}

					if($updated_imei_list['membersch_knocked'])
					{
						$fr_reply_msg .= "IMEINO Activation Failed, IMEINO : ".implode(',',$updated_imei_list['membersch_knocked'])." Activation Knocked For this deal.";
							
					}
				}
			}
		}
		if($total_margin_cr)
			$fr_reply_msg .= "and Amount of Rs ".$total_margin_cr." has been credited to your account.";
			
		if($fr_reply_msg)
			$this->erpm->pnh_sendsms($from,$fr_reply_msg,$fran['franchise_id']);

		if($total_margin_cr)
		{
			//check the member mobile number is already registered
			if($imei_mobno)
				$is_membr_mobno_unique=$this->db->query("select * from pnh_member_info where mobile like ?","%$imei_mobno%")->row_array();

			if(!empty($is_membr_mobno_unique))
				$this->pdie("Mobile no $imei_mobno is already registered to another Member, please use different mobile number");

			if( strlen($imei_mobno)<10)
				$this->pdie("Member mobile no is invalid");

			if(empty($imei_mobno))
				$this->pdie("Member mobile no not provided");
			if(empty($imei_name))
				$this->pdie("Member Name not provided");

			if($this->db->query("select 1 from pnh_member_info where pnh_member_id=?",$imei_memid)->num_rows()==0)
			{
				$this->db->query("insert into king_users(name,is_pnh,createdon) values(?,1,?)",array("PNH Member: $imei_memid",time()));
				$userid=$this->db->insert_id();
				$inp_data=array();
				$inp_data['pnh_member_id']=$imei_memid;
				$inp_data['mobile']=$imei_mobno;
				$inp_data['franchise_id']=$fran['franchise_id'];
				$inp_data['first_name']=$imei_name;
				$inp_data['user_id']=$userid;
				$inp_data['created_on']=time();
				$this->db->insert('pnh_member_info',$inp_data);
				if($this->db->affected_rows()>=1)
					$this->erpm->pnh_sendsms($imei_mobno,"Congratulatons!!!you are Registered as StoreKing Member Successfully.",$imei_memid);
				foreach($is_valid_imeino_res->result_array() as $valid_imei)
				{
					$this->db->query("update king_orders set member_scheme_processed=1 where id=? and member_scheme_processed=0",$valid_imei['orderid']);
					$this->db->query("insert into t_invoice_credit_notes(franchise_id,type,ref_id,invoice_no,amount,created_on)value(?,2,?,?,?,now())",array($valid_imei['franchise_id'],$valid_imei['imei_ref_id'],$valid_imei['invoice_no'],$valid_imei['msch_amt']));
					$imei_creditid=$this->db->insert_id();
					$this->db->query("update t_imei_no set is_imei_activated = 1,activated_mob_no=?,imei_activated_on = now(),ref_credit_note_id=? where imei_no = ? and product_id = ? and status = 1 ",array($mem_mobno,$imei_creditid,$valid_imei['imei_no'],$valid_imei['product_id']));
					$this->db->query("insert into pnh_franchise_account_summary(franchise_id,action_type,credit_note_id,member_id,invoice_no,credit_amt,status,remarks,created_on)value(?,?,?,?,?,?,?,?,now())",array($valid_imei['franchise_id'],7,$imei_creditid,$membr_id,$valid_imei['invoice_no'],$valid_imei['msch_amt'],1,'imeino : '.$valid_imei['imei_no']));
				}
			}

		}
			
		echo $fr_reply_msg;

	}
	
	function extract_numbers($string)
	 {
	 	preg_match_all('/([\d]+)/', $string, $match); 
	 	return $match[0][0]; 
	 } 
	/**
	 * @author Roopa
	 * @param unknown_type $from
	 * @param unknown_type $msg
	 * @param unknown_type $fran
	 * function to confirm usr ordr by franchisee
	 */
	function confirm_usrordr($from,$msg)
	{
		//	echo $from;die;
		$msg_arr=explode(" ",$msg);
			
		$confirm_msg=@trim($msg_arr['0']);
		$confirm_oid=@trim($msg_arr['1']);
			
		$inputs=sizeof($msg_arr);
		if(empty($confirm_msg))
			$this->pdie("Confirm Type Y or N missing");
		if(empty($confirm_oid))
			$this->pdie("Order Id is missing");

		$fran=$this->db->query("select * from pnh_m_franchise_info where login_mobile1=? or login_mobile2=?",array($from,$from))->row_array();
			
		if(empty($fran))
			$this->pdie("Not Authorized");
			
		if($confirm_oid)
		{
			//$transid="PNH".$confirm_oid;
			$transid=$confirm_oid;

			$trans_det=@$this->db->query("SELECT o.* FROM king_user_orders o JOIN user_order_transactions t ON t.transid=o.transid WHERE t.transid like  '%$transid%'  AND t.franchise_id=? LIMIT 1",$fran['franchise_id'])->row_array();
			//echo $this->db->last_query();exit;
			$mem_usrid=@$trans_det['userid'];
			$transid=$trans_det['transid'];
			
			if(empty($trans_det))
				$this->pdie("Invalid Order ID entered");
			if($trans_det['status']!=0)
				$this->pdie("Order status is already confirmed");

			$meminfo=$this->db->query("select * from pnh_member_info where user_id=?",$mem_usrid)->row_array();
			$userid=$meminfo['user_id'];
			$mem_name=$meminfo['first_name'].' '.$meminfo['last_name'];
			$mem_mob=$meminfo['mobile'];
			$fran_id=$meminfo['franchise_id'];
			$fran_name=$fran['franchise_name'];
			$mid=$meminfo['pnh_member_id'];
			if($confirm_msg=='Y' || $confirm_msg=='y')
			{
				$this->db->query("update king_user_orders set status=1 where transid = ? and userid=?",array($transid,$mem_usrid));
				if($this->db->affected_rows()>=1)
				{
					$inp=array();
					$usr_ordr_det=$this->db->query("select * from king_user_orders where transid=? and userid=? and status=1",array($transid,$mem_usrid));
					if($usr_ordr_det)
					{
						$d_total=0;
						foreach($usr_ordr_det->result_array() as $o)
						{
							$inp['id']=$o['id'];
							$inp['transid']=$o['transid'];
							$inp['userid']=$o['userid'];
							$inp['brandid']=$o['brandid'];
							$inp['itemid']=$o['itemid'];
							$inp["bill_person"]=$inp['ship_person']=$o['bill_person'];
							$inp["bill_address"]=$inp['ship_address']=$o['bill_address'];
							$inp["bill_city"]=$inp['ship_city']=$o['bill_city'];
							$inp['bill_pincode']=$inp['ship_pincode']=$o['ship_pincode'];
							$inp['bill_phone']=$inp['ship_phone']=$o['bill_phone'];
							$inp['bill_email']=$inp['ship_email']=$o['bill_email'];
							$inp['bill_state']=$inp['ship_state']=$o['bill_state'];
							$inp['quantity']=$o['quantity'];
							$inp['time']=time();
							$inp['ship_landmark']=$inp['bill_landmark']=$o['ship_landmark'];
							$inp['bill_country']=$inp['ship_country']="India";
							$inp['i_orgprice']=$o['i_orgprice'];
							$inp['i_price']=$o['i_price'];
							$inp['i_discount']=$o['i_discount'];
							$inp['i_coup_discount']=$o['i_coup_discount'];
							$d_total+=($o['i_price']-$o['i_discount'])*$o['quantity'];
							$inp['i_tax']=$o['i_tax'];
							$inp['vendorid']=0;
							$inp['loyality_point_value']=$o['loyality_point_value'];

							$this->db->insert("king_orders",$inp);

						}
					}
					$usr_ordr_mrgn_track_det=$this->db->query("select * from user_order_margin_track where transid=?",$transid);
					if($usr_ordr_mrgn_track_det)
					{
						foreach($usr_ordr_mrgn_track_det->result_array() as $ordr_mrgn_det)
						{
							$m_inp=array("transid"=>$transid,"itemid"=>$ordr_mrgn_det['itemid'],"mrp"=>$ordr_mrgn_det['mrp'],"price"=>$ordr_mrgn_det['price'],"base_margin"=>$ordr_mrgn_det['base_margin'],"sch_margin"=>$ordr_mrgn_det['sch_margin'],"bal_discount"=>$ordr_mrgn_det['bal_discount'],"qty"=>$ordr_mrgn_det['qty'],"final_price"=>$ordr_mrgn_det['final_price']);
							$this->db->insert("pnh_order_margin_track",$m_inp);
						}
					}
					$fran_crdet = $this->erpm->get_fran_availcreditlimit($fran_id);
					$fran['balance'] = $fran_crdet[3];
					$batch_status=0;
					if($fran['balance']<$d_total)
					{
						$batch_status=1;
						$this->erpm->pnh_sendsms($from,"Member placed an order of Rs $d_total We wont be able to process your order further since your account balance is Rs {$fran['current_balance']} Total order amount : Rs $d_total,Please TopUp your account to proceed",$fran_id,0,0);
					}	
						
					$this->db->query("update user_order_transactions set batch_enabled=1 where transid=? and batch_enabled=0",$transid);

					$usr_trans_det=$this->db->query("select * from user_order_transactions where transid=? and batch_enabled=1",$transid)->row_array();

					if($usr_trans_det)
					{

						$transid=$usr_trans_det['transid'];
						$d_total=$usr_trans_det['amount'];
						$d_total=$usr_trans_det['paid'];
						$fran_id=$usr_trans_det['franchise_id'];
						if($batch_status==0)
							$batch_enabled = 1;
						else
							$batch_enabled = 0;
						$this->db->query("insert into king_transactions(transid,amount,paid,mode,init,actiontime,is_pnh,franchise_id,batch_enabled) values(?,?,?,?,?,?,?,?,?)",array($transid,$d_total,$d_total,3,time(),time(),1,$fran_id,$batch_enabled));
					}
					// =======================< MEMBER CONFIRMED ORDER SMS >====================================
					$this->erpm->pnh_sendsms($mem_mob,"$mem_name,Congrats!Your order [$confirm_oid] has been sucessfully placed,Thanks for Shopping with StoreKing ({$fran['franchise_name']},$from).",$fran_id,$mid,0);
					echo "$mem_name,Congrats!Your order [$confirm_oid] has been sucessfully placed.Thanks for Shopping with StoreKing ({$fran['franchise_name']},$from)".'<br>';
					$this->erpm->pnh_sendsms($from,"Thanks for your reply. With Reference to [$mem_name,$mem_mob], Order [$confirm_oid] has been sucessfully placed.",$fran_id,0,0);
					echo "Thanks for your reply. With Reference to [$mem_name,$mem_mob], Order [$confirm_oid] has been sucessfully placed.";
					// =======================< MEMBER CONFIRMED ORDER SMS >====================================
				}
					
				$franid=$fran_id;
				$billno=10001;
				$nbill=$this->db->query("select bill_no from pnh_cash_bill where franchise_id=? order by bill_no desc limit 1",$franid)->row_array();
				if(!empty($nbill))
					$billno=$nbill['bill_no']+1;
				$inp=array("bill_no"=>$billno,"franchise_id"=>$franid,"transid"=>$transid,"user_id"=>$userid,"status"=>1);
				$this->db->insert("pnh_cash_bill",$inp);

				//do packing process
				$ttl_num_orders=sizeof($usr_ordr_det);
				$batch_remarks="StoreKing Order placed through SMS by $from";
				$updated_by='';

				//echo $transid.'<br>'.$ttl_num_orders.'<br>'.$batch_remarks.'<br>'.$updated_by;die;

				// Process to batch this transaction

				$this->reservations->do_batching_process($transid,$ttl_num_orders,$batch_remarks,$updated_by);
					
				$this->erpm->do_trans_changelog($transid,$batch_remarks);
			}
			if($confirm_msg=='N' || $confirm_msg=='n')
			{
				$this->db->query("update king_user_orders set status=3 where transid=?",$transid);
                                
				// =======================< MEMBER CANCEL ORDER SMS >====================================
				$this->erpm->pnh_sendsms($mem_mob,"Hi $mem_name Sorry to inform you ! your order[$confirm_oid] has been Cancelled. Please contact your ($fran_name-$from) for any queries.",$fran_id,$mid,0);
					echo "Hi $mem_name Sorry to inform you ! your order[$confirm_oid] has been Cancelled. Please contact your ($fran_name-$from) for any queries.";
				
				$this->erpm->pnh_sendsms($from,"Thanks for your reply.  With Reference to[$mem_name,$mem_mob] order[$confirm_oid] has been Cancelled.",$fran_id,0,0);
					echo "Thanks for your reply.  With Reference to[$mem_name,$mem_mob] order[$confirm_oid] has been Cancelled.";
				// =======================< MEMBER CANCEL ORDER SMS >====================================

			}
		}
	}
	/**
	 * SMS functionality FOR unregistered to self register -STOREKING MEMBER
	 * @param unknown_type $from
	 * @param unknown_type $msg
	 */
	function member_register($from,$msg)
	{
		
		$msg=trim($msg);
		$inp_arr=explode(' ',$msg);
		$inp_fid=@$inp_arr['0'];
		$inp_fid=trim($inp_fid);
		$inp_fname=@$inp_arr['1'];
		$inp_lname=@$inp_arr['2']?$inp_arr['2']:'';
		$inp_mname=$inp_fname .''.$inp_lname;
		//echo '<pre>';print_r($inp_arr);die();
		if(empty($inp_fid))
			$this->pdie("Please enter StoreKing Retailers Franchisee ID. ");
		$valid_fid=$this->db->query("select * from pnh_m_franchise_info where pnh_franchise_id=?",$inp_fid)->row_array();
		
		if(empty($valid_fid) || !is_numeric($inp_fid))
			$this->pdie("Invalid Franchisee ID Entered");
		if(empty($inp_mname))
			$this->pdie("Please enter your name");

		if($valid_fid)
		{
			$fran_id=$valid_fid['franchise_id'];
			$fran_name=$valid_fid['franchise_name'];
			$fran_mob1=$valid_fid['login_mobile1'];
			$fran_mob2=$valid_fid['login_mobile2'];

			if($this->db->query("select * from pnh_member_info where mobile=?",$from)->num_rows()==0)
			{
				$membr_id=$this->erpm->_gen_uniquememberid();
				$this->db->query("insert into king_users(name,is_pnh,createdon) values(?,1,?)",array($inp_mname,time()));
				$userid=$this->db->insert_id();

				$this->db->query("insert into pnh_member_info(user_id,first_name,last_name,mobile,franchise_id,pnh_member_id,created_on)values(?,?,?,?,?,?,?)",array($userid,$inp_fname,$inp_lname,$from,$fran_id,$membr_id,time()));
				
                                //==================< Franchise MEMBER REGISTER SMS >====================
				$this->erpm->pnh_sendsms($fran_mob1,"Hello $fran_name, Congrats !! $inp_mname,has been Registered Successfully and has been assigned Member ID :$membr_id Please make sure Registration fee of Rs 50/ has been collected. -StoreKing Team",$fran_id,0,0);
				
                                echo "Hello $fran_name, Congrats !! $inp_mname,has been Registered Successfully and has been assigned Member ID :$membr_id Please make sure Registration fee of Rs 50/ has been collected. -StoreKing Team";
				
                                //==================< Member REGISTER SMS >====================
                                $fran_type = $this->fran_menu_type($fran_id);
                                if($fran_type['menu_type'] == 'electonics')
                                {
                                    //order menu having electronics
                                    $mem_msg = "$inp_mname, Welcome to StoreKing – Hurry up!! Get Free Insurance on the 1st Electronic* product you buy. Your Member ID is $membr_id. Offer Valid only after Registration fee Of Rs ".PNH_MEMBER_FEE."/- is paid to Storeking Franchisee.";
                                            echo "$inp_mname, Welcome to StoreKing – Hurry up!! Get Free Insurance on the 1st Electronic* product you buy. Your Member ID is $membr_id. Offer Valid only after Registration fee Of Rs ".PNH_MEMBER_FEE."/- is paid to Storeking Franchisee.";
                                }
                                else
                                {
                                    $mem_msg = "Hi $inp_mname, Welcome to StoreKing – Hurry up!! Get Free Talk Time worth Rs.".PNH_MEMBER_FREE_RECHARGE." on your 1st purchase above Rs ".MEM_MIN_ORDER_VAL.". Don’t forget your Member ID is $membr_id. Please deposit Rs".PNH_MEMBER_FEE."/- Registration fee with Storeking Franchisee to avail this offer";
                                        echo "Hi $inp_mname, Welcome to StoreKing – Hurry up!! Get Free Talk Time worth Rs.".PNH_MEMBER_FREE_RECHARGE." on your 1st purchase above Rs ".MEM_MIN_ORDER_VAL.". Don’t forget your Member ID is $membr_id. Please deposit Rs".PNH_MEMBER_FEE."/- Registration fee with Storeking Franchisee to avail this offer";
                                }
				$this->erpm->pnh_sendsms($from,$mem_msg,$fran_id,$membr_id,0);
                                #=================
				
			}
			else
			{
				$this->pdie("Dear Customer you have already registered with StoreKing, Please proceed to place orders -StoreKing Team");
			}
		}
	}

        
        /**
         * Function to return franchise menu details
         * @param type $fran_id int
         * @return string array
         */
        function fran_menu_type($fran_id)
        {
            $is_fran_type_electronic = $this->erpm->_get_config_param("FRAN_TYPE_ELECTRONIC");
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
    //            echo "<pre>"; print_r($data);
        }
        
	function createOrder($from,$msg)
	{
		
		$is_fran=$this->db->query("select * from pnh_m_franchise_info where login_mobile1=? or login_mobile2=? and  is_suspended=0",array($from,$from))->row_array();

		$is_mem=@$this->db->query("select * from pnh_member_info where mobile=?",$from)->row_array();
		
		$tmp_var=trim($msg);
		
		$tmp_var = explode(' ',$msg);
		
		
	 	if(in_array('p', $tmp_var))
		{ 
			@$this->price_details($from,$msg);
			die;
		} 
		
		$ordr_frm_usr=0;

		if(empty($is_fran) && empty($is_mem))
			$this->pdie("Not Authorized to use this Format, please refer manual");
		if($is_fran)
		{
			$fran_name=$is_fran['franchise_name'];
			$fran_mobile=$is_fran['login_mobile1'];
			$ordr_frm_usr=0;
		}
		if($is_mem)
		{
			$mem_mob=$is_mem['mobile'];
			$mem_name=$is_mem['first_name'].''.$is_mem['last_name'];
			$fran=$this->db->query("select * from pnh_m_franchise_info where franchise_id=? and is_suspended=0",$is_mem['franchise_id'])->row_array();
			$mid=$is_mem['pnh_member_id'];
			$fran_name=$fran['franchise_name'];
			$fran_mobile=$fran['login_mobile1'];
			$ordr_frm_usr=1;
		}

		$tmp_code = @$tmp_var[0];

		$tmp_code_arr= @$tmp_var[1];

		if($tmp_var[0]=='Y'|| $tmp_var[0]=='y'||$tmp_var[0]=='N'||$tmp_var[0]=='n' && $is_fran)
		{
			$this->confirm_usrordr($from,$msg);die();
		}
		// check if tmp code is valid voucher code for creating order by voucher.
		$tmp_code_vc = $this->db->query("select * from pnh_t_voucher_details where voucher_code=?",$tmp_code);
		if($tmp_code_vc->num_rows()!=0 || $tmp_code=='usebal')
		{

			$membr=$this->db->query("select * from pnh_member_info where mobile=?",$from);

			if($membr->num_rows()!=0)
			{

				$this->create_prepaidorder($from,$msg,$membr);
			}
			else{
				$this->pdie("Your not authorized");
			}
			die();
		}
		
		$msg=trim($msg);
		$msg=str_replace("* ","*",$msg);
		$msg=str_replace(" *","*",$msg);
		$msg=str_replace(" * ","*",$msg);

		$payload=explode(" ",$msg);
		foreach($payload as $p)
		{
			if(@$p{0}=="2")
				$mid=$p;
			if(@$p{0}=="3")
				$this->member_register($from, $msg);
			else
				$npayload[]=$p;
		}
		$payload=$npayload;
		$npayload=array();
		foreach($payload as $p)
		{
			if(stripos($p,",")===false)
			{
				$npayload[]=$p;
				continue;
			}
			$npp=trim($p);
			$npp=explode(",",$p);
			foreach($npp as $np)
				$npayload[]=$np;
		}
		$payload=$npayload;
		$items=array();
		foreach($payload as $p)
		{
			
			$pi=explode("*",$p);
			
			$it['pid']=trim($pi[0])+1-1;
			if(count($pi)!=2)
				$it['qty']=1;
			else
				$it['qty']=$pi[1];
			$items[]=$it;
		}
		if(empty($msg))
		{
			if($ordr_frm_usr==1)
				$this->erpm->pnh_sendsms($mem_mob,"Sorry !! .Please use the following format to place your Order.ProductID *Qty,ProductID *Qty ",$fran['franchise_id'],$mid,0);
			else
				$this->pdie("Sorry !! .Please use the following format to place your Order.ProductID *Qty,ProductID *Qty ");
		}

		$total=0;$d_total=0;
		$itemids=array();
		$itemnames=array();
		$menu_ofr_pricevalue=array();
		$ordered_menus_ttl=array();
		$menu_ttlval=array();
		$commision=0;

		
		foreach($items as $i=>$item)
		{
			if($item['pid']!=0)
			{
				$prod=$this->db->query("select i.*,d.publish,d.menuid,p.is_sourceable from king_dealitems i join king_deals d on d.dealid=i.dealid JOIN `m_product_deal_link` l ON l.itemid=i.id JOIN m_product_info p ON p.product_id=l.product_id where i.is_pnh=1 and i.pnh_id=? and i.pnh_id!=0",$item['pid'])->row_array();
				//echo $this->db->last_query();die();
				if(empty($prod))
					$this->pdie("There is no product with ID : ".$item['pid']);
				
				$ordered_menus_list[]=$prod['menuid'];
				$stock=$this->erpm->do_stock_check(array($prod['id']),array(1),true);
				$avail_det = array_values($stock);
			
				$prod_name=$prod['name'];
				
				if($avail_det[0][0]['stk']==0 && $prod['is_sourceable']==0)
				{
					$this->erpm->pnh_sendsms($from,"Thank you for your Order. We regret to inform you that the Product {$item['pid']}[{$avail_det[0][0]['product_name']}] is out of stock,please remove the Product  and resend the SMS",$fran['franchise_id'],0,0);
					$this->pdie("Thank you for your Order. We regret to inform you that the Product  {$item['pid']}[{$avail_det[0][0]['product_name']}]is out of stock,please remove the Product and resend the SMS");
				}
	
				$items[$i]['mrp']=$prod['orgprice'];
				if($fran['is_lc_store'])
					$items[$i]['price']=$prod['store_price'];
				else
					$items[$i]['price']=$prod['price'];
				$margin=$this->erpm->get_pnh_margin($fran['franchise_id'],$item['pid']);
	
				//echo '<pre>'.print_r($margin);exit;
				$items[$i]['itemid']=$prod['id'];
				if($prod['is_combo']=="1")
					$items[$i]['discount']=$items[$i]['price']/100*$margin['combo_margin'];
				else
					$items[$i]['discount']=$items[$i]['price']/100*$margin['margin'];
	
				$total+=$items[$i]['price']*$items[$i]['qty'];
				$d_total+=($items[$i]['price']-$items[$i]['discount'])*$items[$i]['qty'];
				$commision+=$items[$i]['discount']*$items[$i]['qty'];
				$menu_ofr_pricevalue[$prod['menuid']][]=$items[$i]['price']*$items[$i]['qty'];
	
				$itemids[]=$prod['id'];
				$itemnames[]=$prod['name'];
				$items[$i]['margin']=$margin;
				$items[$i]['tax']=$prod['tax'];
			}
		}
		if($d_total<=0)
			$this->pdie("Sorry !! . Please use the following format to place your Order.ProductID *Qty,ProductID *Qty ");


		foreach($menu_ofr_pricevalue as $i=>$t)
		{
			$menu_ttlval[$i]=array_sum($t);
		}
		$menu_list=array_unique($ordered_menus_list);
		$l_points=array();
		foreach($menu_ttlval as $l_menu_id=>$ttl_l_amt)
		{
			$points=@$this->db->query("SELECT points FROM pnh_loyalty_points WHERE menu_id=? AND ?>=amount AND is_active=1  ORDER BY amount DESC LIMIT 1",array($l_menu_id,$ttl_l_amt))->row()->points;
			$l_points[$l_menu_id]=$points*1;
		}
		$avail=$this->erpm->do_stock_check($itemids);
		

		foreach($itemids as $i=>$itemid)
		 if(!in_array($itemid,$avail))
			$this->pdie("$itemnames[$i] is out of stock");
		
		$fran_crdet = $this->erpm->get_fran_availcreditlimit($fran['franchise_id']);
		$fran['balance'] = $fran_crdet[3];
		if($fran['balance']<$d_total && $ordr_frm_usr==0 )
			$this->pdie("Insufficient balance! Balance in your account Rs {$fran['current_balance']} Total order amount : Rs $d_total");
		if($this->db->query("select 1 from pnh_member_info where pnh_member_id=?",$mid)->num_rows()==0)
		{
			if($this->db->query("select 1 from pnh_m_allotted_mid where ? between mid_start and mid_end and franchise_id=?",array($mid,$fran['franchise_id']))->num_rows()==0)
				$this->pdie("Member ID $mid is not allotted to you");
			$this->db->query("insert into king_users(name,is_pnh,createdon) values(?,1,?)",array("PNH Member: $mid",time()));
			$userid=$this->db->insert_id();
			$this->db->query("insert into pnh_member_info(user_id,pnh_member_id,franchise_id) values(?,?,?)",array($userid,$mid,$fran['franchise_id']));
			$npoints=$this->db->query("select points from pnh_member_info where user_id=?",$userid)->row()->points+PNH_MEMBER_FEE;
			$this->db->query("update pnh_member_info set points=? where user_id=? limit 1",array($npoints,$userid));
			$this->db->query("insert into pnh_member_points_track(user_id,transid,points,points_after,created_on) values(?,?,?,?,?)",array($userid,"",PNH_MEMBER_FEE,$npoints,time()));
			$this->erpm->pnh_fran_account_stat($fran['franchise_id'],1,PNH_MEMBER_FEE-PNH_MEMBER_BONUS,"50 Credit Points purchase for Member $mid","member",$mid);
		}
		else
			$userid=$this->db->query("select user_id from pnh_member_info where pnh_member_id=?",$mid)->row()->user_id;
		$transid=strtoupper("PNH".random_string("alpha",3).$this->p_genid(5));
		if($ordr_frm_usr==0)
			$this->db->query("insert into king_transactions(transid,amount,paid,mode,init,actiontime,is_pnh,franchise_id) values(?,?,?,?,?,?,?,?)",array($transid,$d_total,$d_total,3,time(),time(),1,$fran['franchise_id']));
		else
			$this->db->query("insert into user_order_transactions(transid,amount,paid,mode,init,actiontime,is_pnh,franchise_id) values(?,?,?,?,?,?,?,?)",array($transid,$d_total,$d_total,3,time(),time(),1,$fran['franchise_id']));

		foreach($items as $item)
		{
			if($item['pid']!=0)
			{
				$inp=array("id"=>$this->p_genid(10),"transid"=>$transid,"userid"=>$userid,"itemid"=>$item['itemid'],"brandid"=>"");
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
				$m_inp=array("transid"=>$transid,"itemid"=>$item['itemid'],"mrp"=>$item['mrp'],"price"=>$item['price'],"base_margin"=>$item['margin']['base_margin'],"sch_margin"=>$item['margin']['sch_margin'],"bal_discount"=>$item['margin']['bal_discount'],"qty"=>$item['qty'],"final_price"=>$item['price']-$item['discount']);
				if($ordr_frm_usr==0)
				{
					$this->db->insert("king_orders",$inp);
					$this->db->insert("pnh_order_margin_track",$m_inp);
				}
				else
				{
					$this->db->insert("king_user_orders",$inp);
					$this->db->insert("user_order_margin_track",$m_inp);
				}
			}
		}
		if($ordr_frm_usr==0)
		{
			$this->erpm->pnh_fran_account_stat($fran['franchise_id'],1, $d_total,"Order $transid - Total Amount: Rs $total","order",$transid);
			$balance=$this->db->query("select current_balance from pnh_m_franchise_info where franchise_id=?",$fran['franchise_id'])->row()->current_balance;
                        
                        // =================< Franchise SMS >=============================
			echo "Your order is placed successfully! Total order amount :Rs $total. Amount deducted is Rs $d_total. Your order ID is $transid Balance in your account Rs $balance";
			$this->erpm->sendsms_franchise_order($transid,$d_total);


			$points=$this->db->query("select points from pnh_loyalty_points where amount<? order by amount desc limit 1",$total)->row_array();
			if(!empty($points))
				$points=$points['points'];
			else $points=0;
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

			//do packing process
			$ttl_num_orders=count($items);
			$batch_remarks="StoreKing Order placed through SMS by $from";
			$updated_by=$userid;

			// Process to batch this transaction
//			$this->reservations->do_batching_process($transid,$ttl_num_orders,$batch_remarks,$updated_by);
//			$this->erpm->do_trans_changelog($transid,$batch_remarks);
		}

		//Alotting Loyalty Points
		$trans_order_det_res = $this->db->query("SELECT o.id AS order_id,o.itemid,d.menuid,o.i_price,o.quantity
													FROM king_dealitems i
													JOIN king_deals d ON d.dealid=i.dealid
													JOIN king_orders o ON o.itemid = i.id
													WHERE o.transid=?
													",$transid);
		if($trans_order_det_res->num_rows())
		{
			foreach($trans_order_det_res->result_array() as $trans_ord_det)
			{
				$menuid = $trans_ord_det['menuid'];
				$ord_loyal_pnt = (($trans_ord_det['i_price']*$trans_ord_det['quantity'])*$l_points[$menuid])/$menu_ttlval[$menuid];
				if($ordr_frm_usr==0)
					$this->db->query("update king_orders set loyality_point_value=? where  transid=? and id=? ",array($ord_loyal_pnt,$transid,$trans_ord_det['order_id']));
				elseif($ordr_frm_usr==1)
				$this->db->query("update king_user_orders set loyality_point_value=? where  transid=? and id=? ",array($ord_loyal_pnt,$transid,$trans_ord_det['order_id']));

			}
		}
		if($ordr_frm_usr==1)
		{
			$transid=$this->extract_numbers($transid);
			$transid=trim($transid);
                        
                        // ============= FRANCHISE SMS =====================
			$this->erpm->pnh_sendsms($is_mem['mobile'],"Congrats! Your Order [orderid:$transid] has been sucessfully placed.Please contact($fran_name-$fran_mobile) for any queries -Storeking Team",$fran['franchise_id'],0,'MEM_ORDER');
			echo "Congrats !Your Order [orderid:$transid] has been sucessfully placed.Please contact($fran_name-$fran_mobile) for any queries -Storeking Team.";
			
                        // ============= MEMBER SMS =====================
                        $this->erpm->pnh_sendsms($fran['login_mobile1'],"Hello $fran_name,[$mem_name $mem_mob] has placed order for Rs $d_total.order ID is $transid your Order Commision:Rs $commision Please type y $transid to 9243404342 to confirm order.",$fran['franchise_id'],0,'MEM_ORDER');
			echo "Hello $fran_name, [$mem_name $mem_mob] has placed order for Rs $d_total.order ID is $transid your Order Commision:Rs $commision Please type y $transid to 9243404342 to confirm order.";
		}
	}

}