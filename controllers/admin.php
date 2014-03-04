<?php

include APPPATH.'/controllers/erp.php';
/**
 * Admin file
 *
 * @author Vimal <vimal@viaadz.com>
 * @version 0.9
 * @package snapittoday
 * @subpackage adminpanel
 *
 */
class Admin extends Erp {
	private $error=NULL;

	private $info;
	function Admin() {
		//		error_reporting(0);
		parent::__construct();
		$this->load->library ( 'upload' );
		$this->load->library ( 'form_validation' );
		$this->load->model ( 'adminmodel');
		$this->load->model ( 'adminmodel' ,"dbm");
		$this->load->model('viakingmodel',"vkm");
		$this->load->model("erpmodel","erpm");
		$this->load->model("reservation_model","reservations");
		$this->load->library("email");
		$this->erpm->loadroles();

		if($this->erpm->auth(false,true))
			$this->erpm->log_admin_activity();
		
		/*
		if($_SERVER['HTTP_HOST']!="localhost" && $_SERVER['HTTP_HOST']!="sand43.snapittoday.com" && $_SERVER['HTTP_HOST']!="erp69.sand43.snapittoday.com" && $_SERVER['HTTP_HOST']!="dashboard.snapittoday.com")
		if((!isset($_COOKIE['admauth']) || $_COOKIE['admauth']!=$this->session->userdata("admkey")) && $this->uri->segment(2)!="key")
			show_404();
		*/

		if($_SERVER['HTTP_HOST']!="localhost" && $_SERVER['HTTP_HOST']!="sand43.snapittoday.com" && $_SERVER['HTTP_HOST']!="erp69.sand43.snapittoday.com")
		{
			/*
			if($this->uri->segment(2)=="key"){

			}else{
				 
				if(!isset($_COOKIE['admauth']))
				{
					show_404();
				}
				else
				{
					if($_COOKIE['admauth']!=$this->session->userdata("admkey"))
					{
						if($this->uri->segment(2)!="key"){
							$this->session->set_userdata("admkey",$_COOKIE['admauth']);
							//show_error("Cookie auth expired,please login again from secure link");
						}

					}	
				}
				 
			}
			*/

			if($this->uri->segment(2)=="key"){

			}else{
				if(strlen($this->session->userdata("admkey")) < 5 )
				{
					//show_404();
				}
			}

		}
		
	}

	function key($hash)
	{
		$this->session->set_userdata("admkey",$hash);
		redirect("admin");
	}

	/**
	 * function that checks the usertype and loads the page based on usertype.
	 * On unsuccessful login redirects to login page
	 *
	 */
	function index() 
	{
		if($this->erpm->auth(false,true))
			redirect('admin/dashboard','refresh');
		else
			redirect('admin/login','refresh');
	}
	
	/** function to load login page
	 * 
	 */
	function login()
	{
		if($this->erpm->auth(false,true)) 
			redirect('admin/dashboard','refresh');
		
		$this->load->library ( "form_validation" );
		$data ['smallheader'] = 'smallheader';
		$data ['page'] = 'adminlogin';
		$data ['smallheader'] = true;
		$this->load->view ( 'admin', $data );
	}
	
	// login details validation used for form validation.  
	function _validate_adminlogin($str)
	{
		$un = $this->input->post('explo_email');
		$pwd = $this->input->post('explo_password');
		
		$user_det_res = $this->db->query("select * from king_admin where username = ? and password = md5(?) ",array($un,$pwd));
		if($user_det_res->num_rows())
		{
			$userdetails = $user_det_res->row();
			
			if($userdetails->block_ip_addr)
			{
				$all_ip_addrs = explode(',',$this->db->query("select value from m_config_params where name = 'ALLOWED_IP_ADDR'")->row()->value);
				if(!in_array($_SERVER['REMOTE_ADDR'],$all_ip_addrs))
				{
					$this->form_validation->set_message('_validate_adminlogin','Your Access denied');
					return false;
				}
			}
			
			$sessionData = array ("userid"=>$userdetails->id,'username' => $userdetails->name, 'login_flag' => true, 'usertype' => $usertype, 'brandid' => $brandid ,'access'=>$userdetails->access,'block_ip_addr'=>1);
			$this->session->set_userdata ( array("admin_user" => $sessionData) );
			return true;
		}else
		{
			$this->form_validation->set_message('_validate_adminlogin','Invalid username and password');
			return false;
		}
	}
	
	/**
	 * function to validate the user and redirect him to respective forms.
	 *
	 */
	function processLogin() {
		//$data['smallheader']=true;
		$user = $this->input->post ( "explo_email" );
		$pass = $this->input->post ( "explo_password" );
		//print_r($user);exit;
		$this->load->library ( "form_validation" );
		$this->form_validation->set_rules ( "explo_email", "User name", "required|min_length[4]|max_length[20]|alpha_numeric|trim" );
		$this->form_validation->set_rules ( "explo_password", "Password", "required|min_length[4]|max_length[15]|callback__validate_adminlogin" );
		if ($this->form_validation->run () == FALSE) 
		{
			$this->login();
		}else 
		{
			$this->session->set_flashdata('notify_msg',"Logged In Successfully");
			redirect('admin/dashboard','refresh');
		}
	}
	
	
	
	/**
	 * function to update new password and send email.
	 */
	function jx_reset_adminpwd()
	{
		error_reporting(0);
		$this->load->helper('email');
		$output = array();
		if($this->erpm->auth(false,true))
		{
			$output['status'] = 'error';
			$output['message'] = 'You are already loggedin';
		}else
		{
			$email = $this->input->post('fgt_pwd_email');
			if(!valid_email($email))
			{
				$output['status'] = 'error';
				$output['message'] = 'Invalid Email address entered';
			}else
			{
				// check if email is registered in system.
				$res = $this->db->query("select * from king_admin where email = ? ",$email);
				if($res->num_rows())
				{
					$username = $res->row()->username;
					$password=randomChars(6);
	
					$this->db->query("update king_admin set password=md5(?) where id=? limit 1",array($password,$res->row()->id));
					$this->vkm->email($email,"Your Storeking ERP account password updated","Hi $name,<br><br>Your ERP account password has been changed<br><br>Username : $username<br>Password : $password<br><br>ERP Team Storeking");
					$this->session->set_flashdata("erp_pop_info","Please check your mail($email) for recovering your account password.");
	
					$output['status'] = 'success';
				}else
				{
					$output['status'] = 'error';
					$output['message'] = 'Invalid Email address entered';
				}
			}
		}
	
		echo json_encode($output);
	}

	/**
	 * function to validate the user and redirect him to respective forms.
	 *
	 */
	function processLogin_old() {
		//$data['smallheader']=true;
		$user = $this->input->post ( "explo_email" );
		$pass = $this->input->post ( "explo_password" );
		//print_r($user);exit;
		$this->load->library ( "form_validation" );
		$this->form_validation->set_rules ( "explo_email", "User name", "required|min_length[4]|max_length[20]|alpha_numeric|trim" );
		$this->form_validation->set_rules ( "explo_password", "Password", "required|min_length[4]|max_length[15]|callback_authenticate" );
		if ($this->form_validation->run () !== FALSE) {
			//print_r($user);echo '<br>';
			$userid = md5 ( strtolower ( $user ) );
			//print_r($userid);echo '<br>';
			$userdetails = $this->adminmodel->getUser ( $userid );
			//print_r($userdetails);
			//print_r(md5($pass));exit;
			if ($userdetails != FALSE) {
				$userPass = $userdetails->password;
				$usertype = $userdetails->usertype;
				$brandid = $userdetails->brandid;
				//print_r($userdetails);
				//print_r(md5($pass));exit;
			}
			if (isset ( $usertype ) && isset ( $userPass ) !== FALSE && $userPass == md5 ( $pass )) {
				//print_r($usertype);echo 'sd';exit;
				$sessionData = array ("userid"=>$userdetails->id,'username' => $userdetails->name, 'login_flag' => true, 'usertype' => $usertype, 'brandid' => $brandid ,'access'=>$userdetails->access);
				$this->session->set_userdata ( array("admin_user" => $sessionData) );

				redirect("admin");

				//echo '<div align=left><a href="">logout</a>';
				//echo  '<div align=right><b>Welcome:</b>'.$this->session->userdata('uname')."</div>";


				///// dont want to execute below. Just redirect !!!

				if ($usertype == 1) {
					$this->loaddashboardpage();
				}
				if ($usertype == 2) {
					$brandid = $this->session->userdata ( 'brandid' );
					//echo $brandid;exit;
					$categories = $this->adminmodel->getcategoryforusertype ( $brandid );
					//print_r($categories);exit;
					$data ['adm_categories'] = $categories;
					$data ['adminheader'] = TRUE;
					$data ['page'] = "addhotels";
					$this->load->view ( 'admin', $data );
				}
				if ($usertype == 3) {
					$brandid = $this->session->userdata ( 'brandid' );
					//echo $brandid;exit;
					$categories = $this->adminmodel->getcategoryforusertype ( $brandid );
					//print_r($categories);exit;
					$data ['categories'] = $categories;
					$data ['adminheader'] = TRUE;
					$data ['page'] = "addhotels";
					$this->load->view ( 'admin', $data );
				}

			} else {
				$data ['autherror'] = "Invalid user name or password";
				$data ['smallheader'] = 'smallheader';
				$data ['page'] = 'adminlogin';
				$this->load->view ( 'admin', $data );
			}
		} else {
			$data ['smallheader'] = 'smallheader';
			$data ['page'] = 'adminlogin';
			$this->load->view ( 'admin', $data );
		}
	}

	function delreview($rid)
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		$this->db->query("update king_reviews set status=0 where id=? limit 1",$rid);
		redirect("admin/review");
	}
	
	function headtotoe()
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		if($_POST)
		{
			$type=$_POST['bodyparts'];
			$ids=explode(",",$_POST['ids']);
			$this->db->query("update king_dealitems set bodyparts=0 where bodyparts=$type");
			foreach($ids as $id)
				$this->db->query("update king_dealitems set bodyparts=$type where id=?",$id);
			die("HEAD TO TOE RESETTED TO GIVEN PRODUCTS<br><a href='".site_url("admin")."'>home</a>");
		}
		$data['page']="headtotoe";
		$this->load->view("admin",$data);
	}
	
	function jxsrchforht()
	{
		$auth=$this->auth();
		$q="%".$this->input->post("q")."%";
		$r=$this->db->query("select i.name,i.price,c.name as cat,c.id as catid,i.id from king_deals d join king_dealitems i on i.dealid=d.dealid join king_categories sc on sc.id=d.catid join king_categories c on c.id=sc.type or (c.id=sc.id and sc.type=0) where d.tagline like ? and ? between d.startdate and d.enddate and d.publish=1 limit 30",array($q,time()))->result_array();
		$ret="";
		foreach($r as $i)
			$ret.='<div><a href="javascript:void(0)" onclick=\'addcont("'.$i['id'].'","'.htmlspecialchars($i['name']).'","'.$i['catid'].'","'.$i['cat'].'","'.$i['price'].'")\'>'.$i['name'].'</a>';
		echo $ret;
	}
	
	function create_fnewsletter()
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		$this->load->model("viakingmodel","vdbm");
		$brands=$this->vdbm->getfeaturedbrands();
		$items=$this->vdbm->getfeatured();
		$bids=$iids=array();
		$i=0;
		foreach($brands as $b)
		{
			$bids[]=$b['brandid'];
			$i++;
			if($i>7)
				break;
		}
		foreach($items as $item)
			$iids[]=$item['id'];
		$bids=implode(",",$bids);
		$iids=implode(",",$iids);
		$this->db->query("insert into king_featured_mails(url,brands,items,time) values(?,?,?,?)",array(randomChars(12),$bids,$iids,time()));
		redirect("admin/featured_newsletter");
	} 
	
	function authorize_trans($transid)
	{
		$user=$this->auth(ADMINISTRATOR_ROLE);
		$this->load->model("viakingmodel","vdbm");
		$this->load->library("cart");
		$trans=$this->db->query("select * from king_transactions where transid=?",$transid)->row_array();
		if(empty($trans) || $trans['status'])
			show_error("$transid is not a valid transaction for manual authorization");
		if($this->db->query("select 1 from king_tmp_orders where transid=?",$transid)->num_rows()==0)
			show_error("$transid is not a PONR transaction");
		$s_user=$this->session->userdata("user");
		if($s_user)
			$this->session->unset_userdata("user");
		$st_user=$this->vdbm->getuserbyemail($this->db->query("select u.email from king_tmp_orders t join king_users u on u.userid=t.userid where t.transid=?",$transid)->row()->email);
		$this->session->set_userdata("user",$st_user);
		$this->vdbm->authorder($transid,0,$trans['mode']);
		$this->db->query("update king_transactions set status=1 where transid=? limit 1",$transid);
		$this->vdbm->email(array("vimal@localcircle.in","sri@localcircle.in","gova@localcircle.in","sushma@thecouch.in"),"Transaction authorized manually","Transaction $transid was authorized manually on ".date("g:ia d/m/y"));
		if($s_user)
			$this->session->set_userdata("user",$s_user);
		$this->erpm->do_trans_changelog($transid,"Manually authorized the Order");
		redirect("admin/trans/$transid");
	}
	
	function featured_newsletter()
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		$data['mails']=$this->db->query("select * from king_featured_mails order by id desc")->result_array();
		$data['page']="featured_newsletter";
		$this->load->view("admin",$data);
	}

	function review()
	{
		$user=$this->auth(CALLCENTER_ROLE|DEAL_MANAGER_ROLE|PRODUCT_MANAGER_ROLE);
		if($_POST)
		{
			$this->db->query("insert into king_reviews(name,review,rating,itemid) values(?,?,?,?)",array("SnapItToday",$this->input->post("reply"),5,$this->input->post("itemid")));
			redirect("admin/review");
		}
		$data['reviews']=$this->db->query("select r.*,item.name as product,item.dealid from king_reviews r left outer join king_dealitems item on item.id=r.itemid order by r.id desc")->result_array();
		$data['page']="reviews";
		$this->load->view("admin",$data);
	}

	function corporate($cid=false)
	{
		$user=$this->auth(true);

		if($_POST)
		{
			if($this->input->post("name"))
			{
				$name=$this->input->post("name");
				$this->db->query("update king_corporates set name=? where id=?",array($name,$cid));
				redirect("admin/corporate/$cid");
			}
			if($this->input->post("alias"))
			{
				$alias=$this->input->post("alias");
				$this->db->query("update king_corporates set alias=? where id=? limit 1",array($alias,$cid));
				$this->db->query("update king_users set corpid=? where corpid=?",array($alias,$cid));
				foreach($this->db->query("select * from king_corporates where alias=?",$cid)->result_array() as $c)
				{
					$this->db->query("update king_corporates set alias=? where id=?",array($alias,$c['id']));
					$this->db->query("update king_users set corpid=? where corpid=?",array($alias,$c['id']));
				}
				redirect("admin/corporate/$alias");
			}
		}

		$data['page']="corps";

		$corps=$this->db->query("select * from king_corporates where alias=0 order by name asc")->result_array();
		if($cid)
		{
			$data['corp']=$this->db->query("select * from king_corporates where id=?",$cid)->row_array();
			$data['aliases']=$this->db->query("select * from king_corporates where alias=?",$cid)->result_array();
			$data['len']=$this->db->query("select count(1) as l from king_users where corpid=?",$cid)->row()->l;
			$data['page']="corp";
		}else
		foreach($corps as $i=>$corp)
		{
			$corps[$i]['len']=$this->db->query("select count(1) as l from king_users where corpid=?",$corp['id'])->row()->l;
			$corps[$i]['aliases']=$this->db->query("select count(1) as l from king_corporates where alias=?",$corp['id'])->row()->l;
		}
		$data['corps']=$corps;
		$this->load->view("admin",$data);
	}

	/**
	 * Logout
	 * function to kill all the session variables and redirect the user to login page
	 *
	 */
	function logout() 
	{
		$user=$this->session->userdata("admin_user");
		if ($user!= false) {
			//			$this->session->unset_userdata ( "login_flag" );
			//			$this->session->unset_userdata ( "username" );
			//			$this->session->unset_userdata ( "usertype" );
			$this->session->unset_userdata("admin_user");
		}
		redirect('admin','refresh');
	}

	protected function auth($super=false)
	{
		return $this->erpm->auth($super);
	}
	
	function procgencoupons()
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		$num=(int)$this->input->post("number");
		if($num==0)
		redirect("admin/gencoupons");
		$per1=$this->input->post("per1");
		$per2=$this->input->post("per2");
		$cat=$this->input->post("category");
		if($user['usertype']==1)
		$brand=$this->input->post("brand");
		else
		$brand=$user['brandid'];
		if(strlen($per2)==1)
		$per2=$per2."0";
		$perc=($per1*100)+($per2);
		$ar=$this->adminmodel->generatecoupons($num,$perc,$cat,$brand,md5($user['username']));
		redirect("admin/getcoupons/{$ar[0]}/{$ar[1]}");
	}

	function gencoupons()
	{
		$this->coupons();
	}

	function getcoupons($start=null,$end=null)
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		if($start!=null && $end!=null)
		{
			$data['start']=$start;
			$data['coupons']=$cps=$this->adminmodel->getdetailedcoupons($start,$end,$user['brandid']);
			$stend=0;
			foreach($cps as $cp)
			{
				if($stend<$cp['sno'])
				$stend=$cp['sno'];
			}
			$data['end']=$stend==0?$end:$stend;
		}
		$data['couponshistory']=$this->adminmodel->getcouponshistory($user['brandid']);
		if($user['usertype']==1)
		$data['superadmin']=true;
		else
		$data['adminheader']=true;
		$data['page']="superadmin_coupons";
		$this->load->view("admin",$data);
	}
	
	function pointsys()
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		if($_POST)
		{
			foreach(array("amount","points") as $i)
				$$i=$this->input->post("$i");
			$this->db->query("truncate king_points_sys");
			foreach($amount as $i=>$a)
				if(!empty($a))
					$this->db->query("insert into king_points_sys(amount,points) values(?,?)",array($a,$points[$i]));
			redirect("admin/pointsys");
		}
		$data['sys']=$this->db->query("select * from king_points_sys order by amount asc")->result_array();
		$data['page']="pointsys";
		$this->load->view("admin",$data);
	}

	function dndcoupons($type,$start,$end)
	{
		$user=$this->auth();
		$this->load->helper("download");
		if($type=="2")
		{
			$coupons=$this->adminmodel->getcoupons($start,$end,$user['brandid']);
			$op="";
			foreach($coupons as $cp)
			$op.=$cp['id']."\n";
			force_download("coupons$start-$end.txt",$op);
		}
		elseif($type=="1")
		{
			$coupons=$this->adminmodel->getdetailedcoupons($start,$end,$user['brandid']);
			$op='"Coupon Id (16 digits)*","Discount (%)","Brand","Category","*please change format of first column to ""number with no decimal"" to display all 16 digits"'."\n";
			foreach($coupons as $cp)
			$op.='"'.$cp['id'].'","'.sprintf("%.2f",($cp['value']/100)).'","'.$cp['brandname'].'","'.$cp['catname'].'",""'."\n";
			force_download("coupons$start-$end.csv",$op);
		}
	}

	function jx_getbrand($cat)
	{
		$user=$this->auth();
		$brands=$this->dbm->getbrandsforcategory($cat);
		echo '<select name="brand">';
		foreach($brands as $brand)
		echo '<option value="'.$brand['id'].'">'.$brand['name'].'</option>';
		echo "</select>";
	}

	function menu()
	{
		$user=$this->auth(DEAL_MANAGER_ROLE|PRODUCT_MANAGER_ROLE);
		$data['menu']=$this->db->query("select * from king_menu order by name asc")->result_array();
		$data['page']="menu";
		$this->load->view ( 'admin', $data );
	}

	function addmenu()
	{
		$user=$this->session->userdata ( "admin_user" );
		if ($user== false)
		redirect ( "admin" );
		if($_POST)
		{
			$name=trim($this->input->post("name"));
			if(!empty($name))
			$this->dbm->addmenu($name);
			redirect("admin/menu");
		}
		$data['page']="addmenu";
		$this->load->view("admin",$data);
	}

	function menustatus($id,$stat=1)
	{
		$user=$this->session->userdata ( "admin_user" );
		if ($user== false)
		redirect ( "admin" );
		$this->db->query("update king_menu set status=? where id=? limit 1",array($stat,$id));
		redirect("admin/menu");
	}

	/**
	 *Load Add Deals page
	 *
	 * function to redirect to add deals page by a particular brand admin
	 *
	 */
	function adddeal()
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		$brandid = $user["brandid"];
		$categories = $this->adminmodel->getcategoryforusertype ( $brandid );
		$data ['adm_categories'] = $categories;
		//$categories = $this->adminmodel->getcategoryforusertype ( $brandid );
		//print_r($categories);exit;
		//$data ['admin_categories'] = $categories;
		if($this->error!=NULL)
		$data['error']=$this->error;
		$data['menu']=$this->dbm->getallmenu();
		$data['brandid']=$brandid;
		$data ['adminheader'] = TRUE;
		$data ['page'] = "adddeal";
		$this->load->view ( 'admin', $data );
	}
	/**
	 * Load addbrands page
	 *
	 * function to load add brands page where a superadmin can add new categories and sub categories
	 * and assign a brand for a particular category.
	 */
	function loadaddbrandspage() {
		$user=$this->session->userdata ( "admin_user" );
		if ($user== false) {
			redirect ( "admin" );
			die ();
		}
		$categories = $this->adminmodel->getcategory ();
		//print_r($categories);exit;
		$data ['adm_categories'] = $categories;
		$data ['superadmin'] = TRUE;
		$data['error']=$this->error;
		$data ['page'] = "superadmin_addbrands";
		$this->load->view ( 'admin', $data );
	}
	/**
	 * Adds Brands to a category
	 *
	 *function to add brands for a particular category by superadmin
	 */
	function addbrandstocategory() {
		$user=$this->session->userdata ( "admin_user" );
		if ($user== false) {
			redirect ( "admin" );
			die ();
		}
		if ($this->input->post ( 'brandname' ) != FALSE && $this->input->post ( 'selcatname' )) {
			$brandname = $this->input->post ( 'brandname' );
			$brands = explode ( ',', $brandname );
			//print_r($brands);exit;
			$catid = $this->input->post ( 'selcatname' );
			for($i = 0; $i < count ( $brands ); $i ++) {
				$this->adminmodel->insertbrand ( $brands [$i], $catid );
				$this->db->close ();
			}
			redirect ( 'admin/loadaddbrandspage' );
		} else {
			$this->error="Enter a brandadmin";
			$data['error']=$this->error;
			$this->loadaddbrandspage();
			return;
		}
	}

	function comments($p=1)
	{
		$user=$this->session->userdata("admin_user");
		if($user==false || $user['usertype']!=1)
		redirect("admin");
		$data['superadmin']=true;
		$data['p']=$p;
		$data['len']=$this->adminmodel->getcommentslen();
		$data['nexturl']=site_url("admin/comments/".($p+1));
		$data['prevurl']=site_url("admin/comments/".($p-1));
		$data['comments']=$this->adminmodel->getcomments();
		$data['page']="superadmin_comments";
		$this->load->view("admin",$data);
	}

	function commentsmoderate()
	{
		$user=$this->session->userdata("admin_user");
		if($user==false || $user['usertype']!=1)
		redirect("admin");
		if($this->input->post("action")!="accept"&&$this->input->post("action")!="flag")
		redirect("admin");
		$this->adminmodel->moderatecomments(explode(",",$this->input->post("cids",true)),$this->input->post("action",true));
		redirect("admin/comments");
	}

	function commentsbystatus($status,$p=1)
	{
		$user=$this->session->userdata("admin_user");
		if($user==false || $user['usertype']!=1)
		redirect("admin");
		$data['superadmin']=true;
		$data['p']=$p;
		$data['len']=$this->adminmodel->getcommentslenbystatus($status);
		$data['nexturl']=site_url("admin/commentsbystatus/".($p+1));
		$data['prevurl']=site_url("admin/commentsbystatus/".($p-1));
		$data['comments']=$this->adminmodel->getcommentsbystatus($status);
		$data["pagetitle"]=ucfirst($status)." comments";
		$data['page']="superadmin_comments";
		$this->load->view("admin",$data);

	}

	/**
	 * List the deals
	 *
	 * @param int $p By default $p is set to one which signifies page number and displays the first page.
	 */
	function deals($p=1) {
		$user=$this->auth(DEAL_MANAGER_ROLE);
		$brandid = $user["brandid"];
		//print_r($this->session->userdata ( "brandid" ));
		if ($brandid != 0) {
			$deals = $this->adminmodel->getdealslist ( $brandid,$p,true );
			$data ['adminheader'] = TRUE;
		} else {
			$deals = $this->adminmodel->getdealslistforsuperadmin ($p);
			$data ['superadmin'] = TRUE;
		}
		//print_r($deals);exit;
		$dealsarray = array ();
		$hoteldeals = array ();
		$roomsdeals = array ();
		if ($deals != FALSE) {
			foreach ( $deals as $deal ) {
				$catid = $deal->catid;
				$dealid = $deal->dealid;
				$dealsarray [$dealid] = $this->adminmodel->getdealitems ( $dealid );
			}
		}
		$data['menu']=$this->dbm->getallmenu();
		$data ['deals'] = $deals;
		$data['categories']=$this->adminmodel->getbrandcategories($brandid,true);
		$data['brands']=$this->adminmodel->getallbrands();
		$data ['dealitems'] = $dealsarray;
		$data['p']=$p;
		$data['len']=$this->adminmodel->getdealslen($brandid);
		$data['nexturl']=site_url("admin/deals/".($p+1));
		$data['prevurl']=site_url("admin/deals/".($p-1));
		$data ['page'] = "admin_viewdeals";
		$this->load->view ( 'admin', $data );
	}

	function deal($dealid) {
		if ($this->session->userdata ( "admin_user" ) == false)
		redirect ( "admin" );
		$user=$this->session->userdata("admin_user");
		$r_itemid=$this->db->query("select dealid from king_dealitems where id=?",$dealid)->row_array();
		if(!empty($r_itemid))
			$dealid=$r_itemid['dealid'];
		if($this->db->query("select is_pnh from king_dealitems where dealid=?",$dealid)->row()->is_pnh==1)
			redirect("admin/pnh_deal/".$this->db->query("select id from king_dealitems where dealid=?",$dealid)->row()->id);
		$brandid = $user["brandid"];
		if($brandid!=0)
		$data['adminheader']=true;
		if($user['usertype']==1)
		$data['superadmin']=true;
		$deals=$this->adminmodel->getdeal($dealid);
		if($deals[0]->vendorid!=$brandid && $brandid!=0)
		redirect("admin/deals");
		$dealsarray = array ();
		$hoteldeals = array ();
		$roomsdeals = array ();
		if ($deals != FALSE) {
			foreach ( $deals as $deal ) {
				$catid = $deal->catid;
				$dealid = $deal->dealid;
				$dealsarray [$dealid] = $this->adminmodel->getdealitems ( $dealid );
			}
		}
		$data ['deals'] = $deals;
		$data['categories']=$this->adminmodel->getbrandcategories($brandid,true);
		$data['brands']=$this->adminmodel->getallbrands();
		$data ['dealitems'] = $dealsarray;
		$data['pagetitle']="Deal - ".$deals[0]->tagline;
		$data ['page'] = "admin_viewdeals";
		$this->load->view ( 'admin', $data );
	}

	function dealsbymenu($menu,$start=0,$p=1)
	{
		if($start!=0)
		$start=str_replace(":","/",$start);
		if ($this->session->userdata ( "admin_user" ) == false)
		redirect ( "admin" );
		$user=$this->session->userdata("admin_user");
		$brandid = $user["brandid"];
		$dealslen=$this->adminmodel->getdealslenbymenu($menu,$start,$brandid);
		if ($brandid != 0) {
			$data ['adminheader'] = TRUE;
		} else {
			$data ['superadmin'] = TRUE;
		}
		$deals = $this->adminmodel->getdealsbymenu($menu,$start,$brandid,$p);
		$dealsarray = array ();
		if ($deals != FALSE) {
			foreach ( $deals as $deal ) {
				$catid = $deal->catid;
				$dealid = $deal->dealid;
				$dealsarray [$dealid] = $this->adminmodel->getdealitems ( $dealid );
			}
		}
		$data ['deals'] = $deals;
		$data['menu']=$this->dbm->getallmenu();
		$data['categories']=$this->adminmodel->getbrandcategories($brandid,true);
		$data['brands']=$this->adminmodel->getallbrands();
		$data ['dealitems'] = $dealsarray;
		$data['len']=$dealslen;
		$data['p']=$p;
		$data['nexturl']=site_url("admin/dealsbymenu/$menu/$start/".($p+1));
		$data['prevurl']=site_url("admin/dealsbymenu/$menu/$start/".($p-1));
		$data['pagetitle']="Deals by menu";
		$data ['page'] = "admin_viewdeals";
		$this->load->view ( 'admin', $data );
	}

	function dealsbystatus($status,$p=1)
	{
		if ($this->session->userdata ( "admin_user" ) == false)
		redirect ( "admin" );
		if($status!="active"&&$status!="inactive"&&$status!="expired"&&$status!="unpublished"&&$status!="published")
		redirect("admin");
		$user=$this->session->userdata("admin_user");
		$brandid = $user["brandid"];
		$dealslen=$this->adminmodel->getdealslenbystatus($status,$brandid);
		if ($brandid != 0) {
			$data ['adminheader'] = TRUE;
		} else {
			$data ['superadmin'] = TRUE;
		}
		$deals = $this->adminmodel->getdealsbystatus ($status,$p,$brandid,true);
		$dealsarray = array ();
		if ($deals != FALSE) {
			foreach ( $deals as $deal ) {
				$catid = $deal->catid;
				$dealid = $deal->dealid;
				$dealsarray [$dealid] = $this->adminmodel->getdealitems ( $dealid );
			}
		}
		$data ['deals'] = $deals;
		$data['categories']=$this->adminmodel->getbrandcategories($brandid,true);
		$data['brands']=$this->adminmodel->getallbrands();
		$data ['dealitems'] = $dealsarray;
		$data['len']=$dealslen;
		$data['p']=$p;
		$data['menu']=$this->dbm->getallmenu();
		$data['nexturl']=site_url("admin/dealsbystatus/$status/".($p+1));
		$data['prevurl']=site_url("admin/dealsbystatus/$status/".($p-1));
		$data['pagetitle']=ucfirst($status)." deals";
		$data ['page'] = "admin_viewdeals";
		$this->load->view ( 'admin', $data );
	}
	function dealsforcategory($category,$p=1) {
		if ($this->session->userdata ( "admin_user" ) == false) {
			redirect ( "admin" );
			die ();
		}
		$user=$this->session->userdata("admin_user");
		$brandid = $user["brandid"];
		if ($brandid != 0) {
			$data ['adminheader'] = TRUE;
		} else {
			$data ['superadmin'] = TRUE;
		}
		$cat=$this->adminmodel->getcategoryforid($category);
		if($cat==false)
		redirect("admin");
		$deals = $this->adminmodel->getdealsforcategory ($category,$p,$brandid,true);
		$dealsarray = array ();
		$hoteldeals = array ();
		$roomsdeals = array ();
		if ($deals != FALSE) {
			foreach ( $deals as $deal ) {
				$catid = $deal->catid;
				$dealid = $deal->dealid;
				$dealsarray [$dealid] = $this->adminmodel->getdealitems ( $dealid );
			}
		}
		$data ['deals'] = $deals;
		$data['categories']=$this->adminmodel->getbrandcategories($brandid,true);
		$data['brands']=$this->adminmodel->getallbrands();
		$data ['dealitems'] = $dealsarray;
		$data['p']=$p;
		$data['len']=$this->adminmodel->getdealslenforcategory($category,$brandid);
		$data['menu']=$this->dbm->getallmenu();
		$data['nexturl']=site_url("admin/dealsforcategory/$category/".($p+1));
		$data['prevurl']=site_url("admin/dealsforcategory/$category/".($p-1));
		$data['pagetitle']="Deals in ".$cat['name'];
		$data ['page'] = "admin_viewdeals";
		$this->load->view ( 'admin', $data );
	}

	function dealsforbrand($brandid,$p=1) {
		if ($this->session->userdata ( "admin_user" ) == false)
		redirect ( "admin" );
		$user=$this->session->userdata("admin_user");
		if($user['usertype']!=1)
		redirect("admin");
		$brand=$this->adminmodel->getbrand($brandid);
		if($brand==false)
		redirect("admin");
		$deals = $this->adminmodel->getdealslist ( $brandid);
		$data ['superadmin'] = TRUE;
		$dealsarray = array ();
		$hoteldeals = array ();
		$roomsdeals = array ();
		if ($deals != FALSE) {
			foreach ( $deals as $deal ) {
				$catid = $deal->catid;
				$dealid = $deal->dealid;
				$dealsarray [$dealid] = $this->adminmodel->getdealitems ( $dealid );
			}
		}
		$data ['deals'] = $deals;
		$data['categories']=$this->adminmodel->getcategoriesfordashboard(1);
		$data['brands']=$this->adminmodel->getallbrands();
		$data ['dealitems'] = $dealsarray;
		$data['pagetitle']="Deals from {$brand['name']}";
		$data ['page'] = "admin_viewdeals";
		$data['len']=$this->adminmodel->getdealslen($brandid);
		$data['p']=$p;
		$data['nexturl']=site_url("admin/dealsforbrand/$brandid/".($p+1));
		$data['prevurl']=site_url("admin/dealsforbrand/$brandid/".($p-1));
		$this->load->view ( 'admin', $data );
	}
	/**
	 * Load Add User page where a brand admin can add users to specific brand
	 *
	 */
	function addUser() {
		$user=$this->session->userdata ( "admin_user" );
		if ($user== false || $user["usertype"]==1 || $user["usertype"]==3)
		redirect ( "admin" );
		$data ['adminheader'] = TRUE;
		$data ['page'] = 'adduser';
		$this->load->view ( 'admin', $data );
	}
	/**
	 *Load view Users for a particular brand by admin of that brand.
	 *
	 * @param int $p By default $p is set to one which signifies page number and displays the first page.
	 */
	function viewUser($p=1) {
		$user=$this->session->userdata ( "admin_user" );
		if ($user== false || $user["usertype"]==1 ||$user["usertype"]==3 ) {
			redirect ( "admin" );
			die ();
		}
		$brandid = $user["brandid" ];
		//print_r($brandid);exit;
		$result = $this->adminmodel->getuserdetails ( $brandid,$p );
		//print_r($result);exit;
		$data ['userdetails'] = $result;
		$data['p']=$p;
		$data ['adminheader'] = 'adminheader';
		$data ['page'] = 'admin_viewuser';
		$this->load->view ( 'admin', $data );
	}

	function brandsforcategory($catid,$p=1)
	{
		$user=$this->session->userdata ( "admin_user" );
		if ($user== false)
		redirect ( "admin" );
		$data['len']=$this->adminmodel->getbrandslen($catid);
		$result = $this->adminmodel->getbrandadmindetailsforcat($catid,$p);
		$data['categories']=$this->adminmodel->getcategoriesfordashboard(1);
		foreach($data['categories'] as $category)
		{
			if($category->id==$catid)
			$data['curcategory']=$category;
		}
		$data ['userdetails'] = $result;
		$data['p']=$p;
		$data ['page'] = 'superadmin_viewadmin';
		$this->load->view ( 'admin', $data );
	}

	/**
	 * Add admin for a brand/users for a brand
	 *
	 *
	 */
	function procaddbrand() {
		if ($this->session->userdata ( "admin_user" ) === false)
		redirect ( "admin" );
		$user=$this->session->userdata("admin_user");
		$usertype = $user["usertype"];
		//		if($user['usertype']!=1)
		//			redirect("admin");
		$uname = $this->input->post ( 'username' );
		$userid = md5 ( strtolower ( $this->input->post ( 'username' ) ) );
		$pwd = md5 ( $this->input->post ( 'password' ) );
		$confirmpwd = md5 ( $this->input->post ( 'confirmpwd' ) );
		$adminemail=$this->input->post("adminemail");
		if($pwd==$confirmpwd)
		$mailpassword=$this->input->post ( 'password' );
		$this->load->library ( "form_validation" );
		//		$this->form_validation->set_rules ( "username", "User Name", "required|min_length[5]|max_length[20]|trim|xss_clean" );
		//		$this->form_validation->set_rules ( "password", "Password", "required|min_length[5]|max_length[30]|trim|xss_clean" );
		//		$this->form_validation->set_rules ( "confirmpwd", "Confirm Password", "required|min_length[5]|max_length[30]|trim|xss_clean|matches[password]" );
		//		$this->form_validation->set_rules ( "adminemail", "Admin Email", "required|valid_email" );
		//		$this->form_validation->set_rules ( "email", "Email", "required|valid_email" );
		$this->form_validation->set_rules ( "brandname", "Brand name", "required" );
		//		$this->form_validation->set_rules ( "branddescription", "Description", "required" );
		//		$this->form_validation->set_rules ( "website", "Website", "required" );
		if ($this->form_validation->run () !== FALSE) {
			if ($this->input->post('brandname') != FALSE) {
				$cidss=substr($this->input->post('cats'),1);
				$cids=explode(",",$cidss);
				$brandname = $this->input->post ( 'brandname' );
				$branddescription=$this->input->post ( 'branddescription' );
				$website=$this->input->post ( 'website' );
				$email=$this->input->post ( 'email' );
				if (isset ( $_FILES ['brandlogo'] ) && $_FILES ['brandlogo'] ['error'] == 0) {
					$imgname = $this->randomChars ( 15 );
					$img = $this->storelogostoserver ( $_FILES ['brandlogo'] ['tmp_name'], $imgname );
				}
				else
				$img=NULL;
				//			if (isset ( $_FILES ['brandlogo2'] ) && $_FILES ['brandlogo2'] ['error'] == 0) {
				//			$imgname1 = $this->randomChars ( 15 );
				//			$img1 = $this->storelogostoserver ( FALSE, $_FILES ['brandlogo2'] ['tmp_name'], $imgname1 );
				//			}
				$logoid=$img;
				//print_r($logoid);exit;
				$utype = 2;
				$brandid=$this->adminmodel->genbrandid();
				$insbrand=$this->adminmodel->insertnewbrand($brandid,$brandname,$branddescription,$logoid,$website,$email,$cids,$userid, $uname, $pwd, $adminemail);
				//print_r($insbrand);exit;
				//$res = $this->adminmodel->getadmindetails ( $brandid, $utype );
				//print_r ( $res [0]);exit ();
				//					$this->db->close ();
				/*					if ($res [0] == False) {
				 $result = $this->adminmodel->insert_userdetails ( $userid, $uname, $pwd, $utype, $brandid );
				 if ($result == TRUE) {
				 redirect ( 'admin/viewbrandadmin' );
				 } else {
				 echo 'could not insert brand admin';
				 exit ();
				 }
					} else {
					echo 'Already Admin Exist for this brand';
					exit ();
					}*/

				//mailcode
				/*$subject = 'ViaKingSale Login details ' ;
				 $Name = 'ViaKing Sale'; //senders name
				 $mailemail = 'viakingsale@viaadz.com'; //senders e-mail adress
				 $recipient = $email; //recipient
				 $mail_body = 'Your user name is'. $uname .'and password is'. $mailpassword ; //mail body
				 $header = "From: " . $Name . " <" . $mailemail . ">\r\n"; //optional headerfields
				 mail ( $recipient, $subject, $mail_body, $header ); //mail command :)
				 echo "<script>alert('Registered Successfully! Password has been sent to your email ID'); location.href='" .site_url('admin/viewbrandadmin') . "';</script>";*/
				redirect('admin/brands');
			}
			/*			if ($usertype == 2) {
				$brandid = $user[ 'brandid' ];
				$utype = 3;
				$result = $this->adminmodel->insert_userdetails ( $userid, $uname, $pwd, $utype, $brandid );
				if ($result == TRUE) {
				redirect ( 'admin/viewUser' );
				} else {
				echo 'could not insert user for this brand';
				exit ();
				}
				}*/
			/*if($this->input->post ( 'brandname' )==0){
				echo 'Please select a Brand';exit;
				}*/

		} else {
			//echo $usertype;
			$data['category']=$this->adminmodel->getcategoriesfordashboard(1);
			$data ['page'] = 'superadmin_addbrand';
		}
		$this->load->view ( 'admin', $data );
	}
	/**
	 * remove a brand admin by superadmin
	 *
	 * @param int $id and optional
	 * @param int $usertype it is used to check the $usertype
	 */
	function removeadmin($id = 'nil', $usertype) {
		if ($this->session->userdata ( "admin_user" ) === false) {
			redirect ( "admin" );
			die ();
		}
		if ($id == "nil") {
			redirect ( "admin" );
			die ();
		}
		if ($usertype == 2) {
			//echo 'hi';exit;
			$this->adminmodel->delete_admindetails ( $id, $usertype );
			redirect ( "admin/brands" );
		} else {
			//echo 'hello';exit;
			$this->adminmodel->delete_admindetails ( $id, $usertype );
			redirect ( "admin/viewuser" );
		}
	}
	/**
	 *Load change password page for a superadmin/particular admin where super admin can reset a brand admin's password.
	 *
	 * @param int $id  is optional if super admin wants to change selfs password else its necessary
	 */
	function adminchangepwd($id = 'nil') {
		$user=$this->session->userdata ( "admin_user" );
		if ($user == false || $user["usertype"]!=1) {
			redirect ( "admin" );
			die ();
		}
		if ($id == "nil")
		$id = md5 ( strtolower ( $user[ "username" ] ) );
			
		//print_r($id);exit;
		$data ['id'] = $id;
		$data ['superadmin'] = TRUE;
		$data ['page'] = 'superadmin_changepwd';
		$this->load->view ( 'admin', $data );
	}
	/**
	 * Change superadmin's self password or a particular brand admins password by superadmin
	 *
	 */
	function superadminchangepassword() {
		$user=$this->session->userdata ( "admin_user" );
		if ($user== false)
		redirect ( "admin" );
		$id = $this->input->post ( 'id' );
		$oldpwd = md5 ( $this->input->post ( 'oldpwd' ) );
		$newpwd = $this->input->post ( 'newpwd' );
		$confirmpwd = $this->input->post ( 'confirmpwd' );
		$this->load->library ( "form_validation" );
		if ($id == false) {
			$this->form_validation->set_rules ( "oldpwd", "Old Password", "required|xss_clean|trim" );
			if ($user["usertype" ] == 1)
			$id = md5 ( strtolower ( $user[ "username" ] ) );
		}
		$this->form_validation->set_rules ( "newpwd", "New Password", "required|min_length[5]|xss_clean|trim" );
		$this->form_validation->set_rules ( "confirmpwd", "Confirm Password", "required|min_length[5]|trim|xss_clean|matches[newpwd]" );
		if ($this->form_validation->run () !== FALSE) {
			if ($user["usertype" ] == 1 && $this->input->post ( "id" ) != FALSE) {
				$newpwd = md5 ( $this->input->post ( 'newpwd' ) );
				$this->adminmodel->changepassword ( $newpwd, $id );
				redirect ( 'admin/brands' );
				die ();
			} else {
				$id = md5 ( strtolower ( $user[ "username" ] ) );
				if ($oldpwd === $this->adminmodel->getpassword ( $id )) {
					$newpwd = md5 ( $this->input->post ( 'newpwd' ) );
					$this->adminmodel->changepassword ( $newpwd, $id );
					redirect ( 'admin/logout' );
					die ();
				} else
				$data ['error'] = "Old password is wrong";
			}
		}
		if ($id != false)
		$data ['id'] = $id;
		$data ['superadmin'] = TRUE;
		$data ['page'] = 'superadmin_changepwd';
		$this->load->view ( 'admin', $data );
	}
	/**
	 *Load change password page for a particular brandadmin/particular user of a brand.
	 *
	 * @param int $id is optional if super admin wants to change selfs password else its necessary
	 */
	function changepwd($id = 'nil') {
		$user=$this->session->userdata ( "admin_user" );
		if ($user == false || $user["usertype"]==1) {
			redirect ( "admin" );
			die ();
		}
		if ($id == "nil")
		$id = md5 ( strtolower ( $user["username"] ) );

		$data ['id'] = $id;
		$data ['adminheader'] = 'adminheader';
		$data ['page'] = 'admin_changepwd';
		$this->load->view ( 'admin', $data );
	}
	//
	/**
	* Change brandadmin's self password or a particular user's password by brandadmin under that brand
	*
	*
	*/
	function changepassword() {
		$user=$this->session->userdata ( "admin_user" );
		if ($user== false) {
			redirect ( "admin" );
			die ();
		}
		$id = $this->input->post ( 'id' );
		$oldpwd = md5 ( $this->input->post ( 'oldpwd' ) );
		$newpwd = $this->input->post ( 'newpwd' );
		$confirmpwd = $this->input->post ( 'confirmpwd' );
		$this->load->library ( "form_validation" );
		if ($id == false) {
			$this->form_validation->set_rules ( "oldpwd", "Old Password", "required|xss_clean|trim" );
			if ($user[ "usertype" ] == 2)
			$id = md5 ( strtolower ( $user["username"] ) );
		}
		$this->form_validation->set_rules ( "newpwd", "New Password", "required|min_length[5]|xss_clean|trim" );
		$this->form_validation->set_rules ( "confirmpwd", "Confirm Password", "required|min_length[5]|trim|xss_clean|matches[newpwd]" );
		if ($this->form_validation->run () !== FALSE) {
			if ($user[ "usertype" ] == 2 && $this->input->post ( "id" ) != FALSE) {
				$newpwd = md5 ( $this->input->post ( 'newpwd' ) );
				$this->adminmodel->changepassword ( $newpwd, $id );
				redirect ( 'admin/viewUser' );
				die ();
			} else {
				$id = md5 ( strtolower ( $user[ "username" ] ) );
				if ($oldpwd === $this->adminmodel->getpassword ( $id )) {
					$newpwd = md5 ( $this->input->post ( 'newpwd' ) );
					$this->adminmodel->changepassword ( $newpwd, $id );
					redirect ( 'admin/logout' );
					die ();
				} else
				$data ['error'] = "Old password is wrong";
			}
		}
		if ($id != false)
		$data ['id'] = $id;
		$data ['adminheader'] = 'adminheader';
		$data ['page'] = 'admin_changepwd';
		$this->load->view ( 'admin', $data );
	}
	/**
	 * Add new deals by a particular brand admin.
	 *
	 */
	function additems() {
		if ($this->session->userdata ( "admin_user" ) === false)
		redirect ( "admin" );
		$this->load->library ( "image_lib" );
		$flag=false;
		$this->form_validation->set_rules ( "startdate", "Startdate", "required|trim" );
		$this->form_validation->set_rules ( "enddate", "Enddate", "required|trim" );
		$this->form_validation->set_rules ( "email", "Email", "valid_email" );
		$this->form_validation->set_rules ( "tagline", "Tagline", "required|min_length[10]|max_length[200]|trim" );
		//		$this->form_validation->set_rules ( "description", "Description", "required|min_length[10]|trim" );
		if($this->form_validation->run()==false)
		$flag=true;
		$imgname = $this->randomChars ( 15 );
		if($this->error==NULL)
		{
			$description = $this->input->post ( 'description' );
			$catid = $this->input->post ( 'categoryname' );
			$website = $this->input->post ( 'website' );
			$email = $this->input->post ( 'email' );
			$mobile = $this->input->post ( 'mobile' );
			$phone = $this->input->post ( 'phone' );
			$address = $this->input->post ( 'address' );
			$city = $this->input->post ( 'city' );
			$state = $this->input->post ( 'state' );
			$pincode = $this->input->post ( 'pincode' );
			$rooms= $this->input->post ( 'rooms' );
			$publish=0;
			if($catid==0)
			{
				$this->error .="Please select a category<br>";
				$flag=true;
			}
			if($rooms==0)
			{
				$this->error .="Please select no of items for sale<br>";
				$flag=true;
			}
			if (isset ( $_FILES ['pic'] ) && $_FILES ['pic'] ['error'] == 0) {
				//			if ($this->input->post ( 'categoryname' ) != FALSE) {
				//print_r($catid);exit;
				/*
				 if ($catid == 4) {
					$amenities = array ('parking', 'spa', 'gym', 'pool', 'bar', 'doctor', 'carrentals', 'internet', 'roomservice', 'restaurant' );
					$i = 0;
					$amenity = array ();
					foreach ( $amenities as $res ) {
					if ($this->input->post ( $res ) == FALSE)
					$amenity [$i] = 0;
					else
					$amenity [$i] = 1;
					$i ++;
					}
					$amenities = implode ( "/", $amenity );
					$startdate = $this->input->post ( 'startdate' );
					$starthrs = $this->input->post ( 'starthrs' );
					list ( $year, $month, $day ) = split ( '[/.-]', $startdate );
					if ($starthrs != 24)
					$sdate = mktime ( $starthrs, 0, 0, $month, $day, $year );
					else
					$sdate = mktime ( 23, 59, 0, $month, $day, $year );
					$enddate = $this->input->post ( 'enddate' );
					$endhrs = $this->input->post ( 'endhrs' );
					list ( $year, $month, $day ) = split ( '[/.-]', $enddate );
					if ($starthrs != 24)
					$edate = mktime ( $endhrs, 0, 0, $month, $day, $year );
					else
					$edate = mktime ( 23, 59, 0, $month, $day, $year );
					$data ['rooms'] = $this->input->post ( 'rooms' );
					$brandid = $this->input->post ( 'brandid' );
					$latitude = $this->input->post ( 'latitude' );
					$longitude = $this->input->post ( 'longitude' );
					$latlong = $latitude . ',' . $longitude;
					$heading = $this->input->post ( 'heading' );
					$tagline = $this->input->post ( 'tagline' );
						
					//$dealid=$this->adminmodel->addmainitems ( $catid,$brandid, $sdate, $edate,$imgname, $address, $latlong, $phone, $email,  $city, $heading, $tagline, $amenities );
					if($sdate<$edate)
					{
					$img = $this->storeimagestoserver ( FALSE, $_FILES ['pic'] ['tmp_name'], $imgname );
					$dealid = $this->adminmodel->addmainitems ( $catid, $brandid, $sdate, $edate, $imgname, $description,$publish,$website,$email,$mobile,$phone,$address,$city,$state,$pincode );
					$this->db->close ();
					if ($dealid != FALSE)
					$this->adminmodel->inserthoteldetails ( $dealid,$latlong,$heading, $tagline, $amenities );
					}
					else {
					$this->error="Start date and end date are invalid. Please check.";
					$this->adddeal();
					return;
					}
					} else {
					*/
				//echo $catid.'<br>';echo $subcat;exit;
				$brandid = $this->input->post ( 'brandid' );
				//echo $brandid;exit;
				$startdate = $this->input->post ( 'startdate' );
				$starthrs = $this->input->post ( 'starthrs' );
				$enddate = $this->input->post ( 'enddate' );
				$endhrs = $this->input->post ( 'endhrs' );
				if(count(explode( '-', $startdate ))!=3 || count(explode ( '-', $enddate ))!=3)
				{
					$this->error.="Date is in invalid format. Format : YYYY-MM-DD<br>";
					$flag=true;
				}
				if(!$flag)
				{
					list ( $year, $month, $day ) = explode ( '-', $startdate );
					if ($starthrs != 24)
					$sdate = mktime ( $starthrs, 0, 0, $month, $day, $year );
					else
					$sdate = mktime ( 23, 59, 0, $month, $day, $year );
					list ( $year, $month, $day ) = explode ( '-', $enddate );
					if ($starthrs != 24)
					$edate = mktime ( $endhrs, 0, 0, $month, $day, $year );
					else
					$edate = mktime ( 23, 59, 0, $month, $day, $year );
					if($sdate<$edate)
					{
						$_POST['dealid']=$this->adminmodel->gendealid();
						$img = $this->storeimagestoserver ( TRUE, $_FILES ['pic'] ['tmp_name'], $imgname );
						$_POST['picid']=$imgname;
						//							$dealid = $this->adminmodel->addmainitems ( $subcat, $brandid, $sdate, $edate, $imgname, $description,$publish,$website,$email,$mobile,$phone,$address,$city,$state,$pincode );
					}
					else
					{
						$this->error.="Start date and end date are invalid. Please check<br>";
						$flag=true;
						//						echo '<script> alert("StartDate cannot br greater than enddate"); </script>';
					}
				}
				//				}
				$this->db->close();
				//			}
				$data ['catid'] = $this->input->post ( 'categoryname' );
				$data ['rooms'] = $this->input->post ( 'rooms' );
				//$dealid = $this->adminmodel->addhoteldetails ( $name, $address, $latlong, $phone, $email, $imgname, $city, $heading, $tagline, $sdate, $edate, $amenities );
				//print_r($dealid);exit;
			} else {
				$this->error.="Please upload profile pic<br>";
				$flag=true;
			}
		}
		//			$dealdetails=$this->adminmodel->getdealdetailsforid($dealid);
		//			if($dealdetails!=FALSE)
		//			$data['dealdetails']=$dealdetails[0];
		//			$data ['dealid'] = $dealid;
		if($flag)
		{
			$this->adddeal();
			return;
		}
		if($this->error!=null)
		$data['error']=$this->error;
		$data['rooms']=$this->input->post("rooms");
		$data ['adminheader'] = 'adminheader';
		$data ['page'] = 'additems';
		$this->load->view ( 'admin', $data );

	}
	/**
	 *Loadresourcespage for a particular deal
	 *
	 * @param int $dealid fetch the item names bassed on dealid
	 */
	function loadresourcespage($dealid) {
		$itemdetails = $this->adminmodel->getroomid ( $dealid );
		$data ['itemdetails'] = $itemdetails;
		$data ['dealid'] = $dealid;
		$data ['adminheader'] = 'adminheader';
		$data ['page'] = 'addpics';
		$this->load->view ( 'admin', $data );
	}
	/**
	 *Add item details for a particular deal
	 *
	 *
	 */
	function finishdeal() {
		$user=$this->auth(DEAL_MANAGER_ROLE);
		$eflag=FALSE;
		$catid = $this->input->post ( 'categoryname' );
		if($this->input->post("dealtype")=="groupsale")
		$dealtype="1";
		else
		$dealtype="0";
		$catid=$this->input->post("categoryname");
		$vendorid=$user['brandid'];
		$brandid=$this->input->post("brand");
		$startdate = $this->input->post ( 'startdate' );
		$starthrs = $this->input->post ( 'starthrs' );
		list ( $year, $month, $day ) = explode ( '-', $startdate );
		if ($starthrs != 24)
		$sdate = mktime ( $starthrs, 0, 0, $month, $day, $year );
		else
		$sdate = mktime ( 23, 59, 0, $month, $day, $year );
		$enddate = $this->input->post ( 'enddate' );
		$endhrs = $this->input->post ( 'endhrs' );
		list ( $year, $month, $day ) = explode ( '-', $enddate );
		if ($starthrs != 24)
		$edate = mktime ( $endhrs, 0, 0, $month, $day, $year );
		else
		$edate = mktime ( 23, 59, 0, $month, $day, $year );
		$dname=$this->input->post("tagline",true);
		$description=$this->input->post("description",true);
		$website=$this->input->post("website");
		$email=$this->input->post("email");
		$description1 = $this->input->post ( 'bdescription');
		$description2 = $this->input->post ( 'description');
		$keywords=$this->input->post("keywords");
		$shipsto=strtolower($this->input->post("shipsto"));
		$imgname = $this->randomChars ( 15 );
		if (isset ( $_FILES ['pic'] ) && $_FILES ['pic'] ['error'] == 0)
		{
			$this->load->library("thumbnail");
			$img=$_FILES['pic']['tmp_name'];
			if($this->thumbnail->check($img))
			{
				$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/300/$imgname.jpg","width"=>300));
				$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/small/$imgname.jpg","width"=>200));
				$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/thumbs/$imgname.jpg","width"=>50,"max_height"=>50));
				$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/$imgname.jpg","width"=>400));
				$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/big/$imgname.jpg","width"=>1000));
			}
		}
		else
		$imgname=NULL;
		$menu=$this->input->post("menu");
		$menu2=$this->input->post("menu2");
		
		
		$is_giftcard=$this->input->post("is_giftcard"); 
		$is_coup_applicable=$this->input->post("is_coup_applicable");
		
		$dealid = $this->adminmodel->adddeal(0,$catid, $vendorid,$brandid,$dname, $sdate, $edate, $imgname, $description,$website,$email,$dealtype,$keywords,$menu,$menu2,$is_giftcard,$is_coup_applicable);
		$price = $this->input->post ( 'price');
		$nlc=$this->input->post("nlc");
		$phc=$this->input->post("phc");
		$shc=$this->input->post("shc");
		$rsp=$this->input->post("rsp");
		$shipsin=$this->input->post("shipsin");
		$mrp = $this->input->post ("mrp");
		$viaprice=$this->input->post("viaprice");
		$quantity = $this->input->post ( "quantity");
		$itemcode=$this->input->post("itemcode");
		$model=$this->input->post("model");
		$fcp=$this->input->post("fcp");
		$tax=$this->input->post("tax")*100;
		$service_tax=$this->input->post("service_tax")*100;
		$service_tax_cod=$this->input->post('service_tax_cod');
		$groupbuy=$this->input->post("groupbuy");
		$barcode=strtolower($this->input->post("barcode"));
		
		

		if($quantity==false)
		$quantity="4294967295";
		$si=array("slot1","slot2","slot3","slot4");
		$slots=array();
		foreach($si as $s)
		{
			if($this->input->post("$s")=="")
			break;
			$slots[$this->input->post("$s")]=$this->input->post("{$s}price");
		}
		$slots=serialize($slots);

		$bp_expires=$this->input->post("bp_expires");

		$id = $this->adminmodel->additem ( $dealid, $price, $viaprice,$mrp, $dname, $quantity, $imgname, $description1,$description2,$itemcode,$model,$nlc,$phc,$shc,$rsp,$shipsin,$fcp,$tax,$service_tax,$service_tax_cod,$slots,$bp_expires,$shipsto,$barcode);
		

		$this->adminmodel->addactivity(md5(strtolower($user['username'])),"New deal '$dname' added",$dealid,$user['brandid']);
		
		$_POST['dealid']=$dealid;
		$this->adminmodel->update_deal_product_links();

		redirect("admin/addpicsandvideos/$dealid");

		//Dont execute
		$itemdetails = $this->adminmodel->getroomid ( $dealid );
		$data ['itemdetails'] = $itemdetails;
		$data ['dealid'] = $dealid;
		$data ['title'] = 'Add more pis for hotels';
		$data ['adminheader'] = TRUE;
		$data ['page'] = 'addpics';
		$this->load->view ( 'admin', $data );
	}
	/**
	 * Add resources
	 *
	 * Add resources for a item under a particular deal
	 */
	function addresources() {
		//		print_r ($_FILES ['pic_0']);exit;
		$this->load->library("thumbnail");
		$this->load->library ( "image_lib" );
		$this->load->library ( "form_validation" );
		$video=$this->input->post("video");
		//		if(strlen($video[0])!=0)
		//		$this->form_validation->set_rules ( "video[]", "Video", "trim|min_length[11]|alpha_numeric" );
		if ($this->input->post ( 'dealid' ) != FALSE && $this->input->post ( 'roomtype' ) != FALSE) {
			$dealid = $this->input->post ( 'dealid' );
			$itemid = $this->input->post ( 'roomtype' );
			$video = $this->input->post ( 'video' );
			//print_r($itemid);exit;
			//echo $totalimages;exit;
			for($i = 0; $i <= 10; $i ++) {
				$imgname = $this->randomChars ( 15 );
				if (! isset ( $_FILES ['pic_' . $i] ))
				break;
				if (isset ( $_FILES ['pic_' . $i] ['name'] ) && $_FILES ['pic_' . $i] ['error'] == 0) {
					$img=$_FILES['pic_' . $i]['tmp_name'];
					$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/thumbs/$imgname.jpg","width"=>60,"max_height"=>50));
					$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/small/$imgname.jpg","width"=>200));
					$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/300/$imgname.jpg","width"=>300));
					$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/$imgname.jpg","width"=>400));
					$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/big/$imgname.jpg","width"=>1000));
					//					$this->storeimagestoserver ( TRUE, $_FILES ['pic_' . $i] ['tmp_name'], $imgname );
					$q = $this->adminmodel->addresources ( $dealid, $itemid, 0, $imgname );
				}
			}
			foreach ( $video as $res ) {
				$res = trim ( $res );
				if (strlen ( $res ) != 0)
				$q = $this->adminmodel->addresources ( $dealid, $itemid, 1, $res );
				$this->db->close ();
			}
			if ($this->input->post ( 'picpage' ) != FALSE) {
				$this->loadresourcespage ( $dealid );
			} else
			redirect ( 'admin' );
		}
		if($this->input->post("picpage")=="yes")
		redirect("admin/addpicsandvideos/$dealid");
		else
		redirect("admin/deals");
		$itemdetails = $this->adminmodel->getroomid ( $dealid );
		$data ['itemdetails'] = $itemdetails;
		$data ['dealid'] = $dealid;
		$data ['title'] = 'Add more pis for hotels';
		$data ['adminheader'] = 'adminheader';
		$data ['page'] = 'addpics';
		$this->load->view ( 'admin', $data );
	}
	/**
	 * Resize the pic and store it to server file
	 *
	 * @param bool $itembool
	 * @param string $picloc path
	 * @param string $imgname name of the pic
	 * @return $picloc
	 */
	function storeimagestoserver($itembool, $picloc, $imgname) {
		//echo $picloc;exit;
		//echo 'hi';exit;
		//$this->image_lib->clear ();
		$imagemanip ['image_library'] = 'gd2';
		$imagemanip ['source_image'] = $picloc;
		//echo $picloc;exit;
		if ($itembool == False) {
			$imagemanip ['new_image'] = realpath(APPPATH) . "/images/items/big/" . $imgname . ".jpg";
			$imagemanip ['width'] = 945;
			$imagemanip ['height'] = 945;
			$imagemanip ['maintain_ratio'] = TRUE;
			$imagemanip ['master_dim'] = 'auto';
			$imagemanip ['quality'] = '90%';
			//print_r($imagemanip);
			$this->image_lib->initialize ( $imagemanip );
			$this->image_lib->resize ();
		} else {
			$thumbnail = $picloc;
			$this->image_lib->clear ();
			$imagemanip ['image_library'] = 'gd2';
			$imagemanip ['source_image'] = $picloc;
			$imagemanip ['new_image'] = "images/items/" . $imgname . ".jpg";
			//echo $imagemanip ['new_image'];
			$imagemanip ['maintain_ratio'] = TRUE;
			$imagemanip ['width'] = 300;
			$imagemanip ['height'] = 300;
			$imagemanip ['maintain_ratio'] = TRUE;
			$imagemanip ['master_dim'] = 'auto';
			$imagemanip ['quality'] = '100%';
			$this->image_lib->initialize ( $imagemanip );
			$this->image_lib->resize ();
		}
		return $picloc;
	}

	/**
	 *Generate random characters to name a pic
	 *
	 * @param int $len integer of length 15 to generate random characters
	 * @return string $str
	 */
	function randomChars($len) {
		$str = "";
		$charcode = ord ( "a" );
		$i = 0;
		while ( $i < $len ) {
			$rad = rand ( 0, 3 );
			if ($rad == 0 || $rad == 1)
			$str = $str . chr ( $charcode + rand ( 0, 15 ) );
			else
			$str = $str . rand ( 0, 9 );
			$i = $i + 1;
		}
		return $str;
	}
	/**
	 * Load Edit deal details page
	 *
	 */
	function edit($id = "nil") {
		$user=$this->auth(DEAL_MANAGER_ROLE);
		//echo $catid.'<br>';
		//echo $brandid;exit;
		//$result = $this->adminmodel->edithoteldetails ( $id );
		$result = $this->adminmodel->editdeals ( $id );
		//print_r($result);exit;
		if ($result === FALSE) {
			redirect ( "admin" );
			die ();
		}
		if (isset ( $result [0] )) {
			$data ['dealdetails'] = $result [0];
		}
		//print_r($this->session->userdata('usertype'));exit;
		if ($user['usertype' ] == 1) {
			$data ['superadmin'] = TRUE;
		} else {
			$data ['adminheader'] = TRUE;
		}

		$brandid = $user["brandid"];
		$data ['adm_categories'] = $this->adminmodel->getcategoryforusertype ( $brandid );

		$data['menu']=$this->dbm->getallmenu();
		$data['error']=$this->error;
		$data['brandid']=$result[0]->brandid;
		$data ['dealcatid'] = $catid=$result[0]->catid;
		$data['brands']=$this->db->query("select b.* from king_brands b order by b.name asc")->result_array();
		$data ['is_edit'] = true;
		$data ['page'] = "adddeal";
		$this->load->view ( 'admin', $data );
	}
	/**
	 * Update Deal
	 *
	 * a specific deal is updated and changes made is carried to the viewdeals page
	 */
	function updatedeal() {
		if ($this->session->userdata ( "admin_user" ) === false)
		redirect ( "admin" );
		$user=$this->session->userdata("admin_user");
		$eflag=FALSE;
		$this->load->library ( "image_lib" );
		if ($this->input->post ( "catid" ) != false) {
			foreach($_POST as $key=>$val)
			$$key=$this->input->post($key);
			$catid = $this->input->post ( 'catid' );
			$dealid = $this->input->post ( 'dealid' );
			$brandid = $this->input->post ( 'brandid' );
			$description = $this->input->post ( 'description' );
			$website = $this->input->post ( 'website' );
			$email = $this->input->post ( 'email' );
			$tagline=$this->input->post("tagline");
			$startdate = $this->input->post ( 'startdate' );
			$starthrs = $this->input->post ( 'starthrs' );
			$bd=$this->input->post("bdescription");
			$d=$this->input->post("description");
			$p=$this->input->post("price");
			$mp=$this->input->post("mrp");
			$vp=$this->input->post("viaprice");
			$qua=$this->input->post("quantity");
			$shipsto=strtolower($this->input->post("shipsto"));
			
			$is_giftcard=strtolower($this->input->post("is_giftcard"));
			$is_coupon_applicable=strtolower($this->input->post("is_coupon_applicable"));
			if($qua===false)
			$qua="4294967295";
			$keywords=$this->input->post("keywords");
			$fcp=$this->input->post("fcp");
			list ( $year, $month, $day ) = explode ( '-', $startdate );
			if ($starthrs != "24")
			$sdate = mktime ( $starthrs, 0, 0, $month, $day, $year );
			else
			$sdate = mktime ( 23, 59, 0, $month, $day, $year );
			$enddate = $this->input->post ( 'enddate' );
			$endhrs = $this->input->post ( 'endhrs' );
			list ( $year, $month, $day ) = explode ( '-', $enddate );
			if ($endhrs != "24")
			$edate = mktime ( $endhrs, 0, 0, $month, $day, $year );
			else
			$edate = mktime ( 23, 59, 0, $month, $day, $year );
			//echo $dealid;exit;
			//				if(strlen(trim($description))==NULL)
			//					{
			//						$this->error .="Please enter the Description.";
			//						$eflag=TRUE;
			//					}
			if($sdate>$edate)
			{
				$this->error .="Start date and end date are invalid. Please check.";
				$eflag=TRUE;
			}
			if($eflag==true)
			{
				$this->edit($dealid,$catid,$brandid);
				return;
			}
			$userid=md5(strtolower($user['username']));
			$data=array("startdate"=>$sdate,"enddate"=>$edate,"tagline"=>$tagline,"description"=>$description,"website"=>$website,"email"=>$email);

				
			$changes=$this->adminmodel->getdealchanges($dealid,$data);
			$msg="Deal details changed. The following details in deal are changed :<div>";
			foreach($changes as $c)
			$msg.=$c."<br>";
			$data=array("menuid"=>$this->input->post("menu"),"nlc"=>$this->input->post("nlc"),"phc"=>$this->input->post("phc"),"menuid2"=>$this->input->post("menu2"),"price"=>$p,"orgprice"=>$mp,"name"=>$tagline,"available"=>$qua,"description1"=>$bd,"description2"=>$d,"cod"=>$this->input->post("cod"),"groupbuy"=>$this->input->post("groupbuy"));
			$changes=$this->adminmodel->getitemchanges($this->db->query("select id from king_dealitems where dealid=?",$dealid)->row()->id,$data);
			if(!empty($changes))
			$msg.="</div><br><br>Item details changed. The following details in item '<b>$tagline</b>' are changed :<div>";
			foreach($changes as $c)
			$msg.=$c."<br>";


			$taxin=array("tax","service_tax","service_tax_cod","bp_expires");
			foreach($taxin as $t)
			$$t=$this->input->post($t);

			$tax*=100;
			$service_tax*=100;
				
			$this->db->query("update king_deals set menuid=?,menuid2=?,is_giftcard=?,is_coupon_applicable=? where dealid=? limit 1",array($this->input->post("menu"),$this->input->post("menu2"),$is_giftcard,$is_coupon_applicable,$dealid));
			$this->db->query("update king_deals set catid=?,brandid=? where dealid=? limit 1",array($this->input->post("categoryname"),$this->input->post("brandname"),$dealid));
			
			// updated barcode info of product  
			$barcode=strtolower($this->input->post("barcode"));
			$p_itemid = $this->db->query('select id from king_dealitems where dealid = ? ',$dealid)->row()->id;
			
			$this->db->where('itemid',$p_itemid);
				
			
			
			@list($fy,$fm,$fd)=@explode("-",$_POST['fstartdate']);
			$fstart=mktime($_POST['fstarthrs'],0,0,$fm,$fd,$fy);
			@list($fy,$fm,$fd)=@explode("-",$_POST['fenddate']);
			$fend=mktime($_POST['fendhrs'],0,0,$fm,$fd,$fy);
			$this->db->query("update king_deals set featured_start=?,featured_end=? where dealid=?",array($fstart,$fend,$dealid));

			$si=array("slot1","slot2","slot3","slot4");
			$slots=array();
			foreach($si as $s)
			{
				if($this->input->post("$s")=="")
				break;
				$slots[$this->input->post("$s")]=$this->input->post("{$s}price");
			}
			$slots=serialize($slots);
			$bp_expires=$bp_expires*24*60*60;
			$this->db->query("update king_dealitems set bp_expires=?,tax=?,service_tax=?,service_tax_cod=?,slots=?,shipsto=? where dealid=?",array($bp_expires,$tax,$service_tax,$service_tax_cod,$slots,$shipsto,$dealid));
				

			$cod=$this->input->post("cod");
			$sizing_type=$this->input->post("sizing");
			$types=array("none"=>0,"numbering"=>1,"wording"=>2);
			$type=$types[$sizing_type];
			if($type==1)
			{
				$numbers=explode(",",$this->input->post("sizing_numbers"));
				foreach($numbers as $i=>$n)
				$numbers[$i]=trim($n);
				$sizing="1:".implode(",",$numbers);
			}
			elseif($type==2)
			$sizing="2:".implode(",",$this->input->post("sizing_words"));
			else
			$sizing="0";

			$groupbuy=$this->input->post("groupbuy");
			$this->db->query("update king_dealitems set sizing=?,cod=?,groupbuy=? where dealid=? limit 1",array($sizing,$cod,$groupbuy,$dealid));
				
			
			if(isset($_POST['prods_id']))
			{
				$product_det=$_POST['prods_id'];
				$prd_qty=$_POST['prods_qty'];
				$user_det=$this->session->userdata("admin_user");
				if($product_det)
				{
					foreach($product_det as $i=>$product)
					{
						$sql1="select * from m_product_deal_link where itemid=? and product_id=?;";
						$sit_deal_prd_link_det=$this->db->query($sql1,array($p_itemid,$product))->row_array();
						if(!$sit_deal_prd_link_det)
						{
							$mrp=$this->db->query("select mrp from m_product_info where product_id=?",$product)->row()->mrp;
							$this->db->query("insert into m_product_deal_link(product_id,itemid,qty,created_on,created_by,product_mrp) values(?,?,?,?,?,?)",array($product,$p_itemid,$prd_qty[$i],date('Y-m-d H:i:s'),$user_det['userid'],$mrp));
							$this->db->query("insert into t_upd_product_deal_link_log(itemid,product_id,qty,perform_on,perform_by,is_updated,product_mrp,is_sit)values(?,?,?,?,?,?,?,?)",array($p_itemid,$product,$prd_qty[$i],date('Y-m-d H:i:s'),$user_det['userid'],2,$mrp,1));
						}
					}
				}
				
			}
			//$this->adminmodel->update_deal_product_links();
			
			$imgname = $this->randomChars ( 15 );
			if (isset ( $_FILES ['pic'] ) && $_FILES ['pic'] ['error'] == 0) {
				$im= $_FILES ['pic'] ['tmp_name'];
				$this->load->library("thumbnail");
				if($this->thumbnail->check($im))
				{
					$this->thumbnail->create(array("source"=>$im,"dest"=>"images/items/small/$imgname.jpg","width"=>200));
					$this->thumbnail->create(array("source"=>$im,"dest"=>"images/items/300/$imgname.jpg","width"=>300));
					$this->thumbnail->create(array("source"=>$im,"dest"=>"images/items/thumbs/$imgname.jpg","width"=>60,"max_height"=>50));
					$this->thumbnail->create(array("source"=>$im,"dest"=>"images/items/$imgname.jpg","width"=>400));
					$this->thumbnail->create(array("source"=>$im,"dest"=>"images/items/big/$imgname.jpg","width"=>1000));
				}
				//					$img = $this->storeimagestoserver ( TRUE, $_FILES ['pic'] ['tmp_name'], $imgname );
				$this->adminmodel->updatedealsimg ( $brandid, $sdate, $edate, $imgname, $tagline, $description,$website,$email,$dealid,$bd,$d,$p,$mp,$vp,$qua,$keywords,$itemcode,$model,$nlc,$phc,$shc,$rsp,$shipsin,$fcp);
				$msg.="profile pic<br>";
				$this->adminmodel->addactivity($userid,$msg."</div>",$dealid,$brandid);
				$this->db->close ();
				redirect ( "admin/deal/$dealid" );
			}
			if (! isset ( $_FILES ['pic'] )) {
				//					print_r($sdate);echo '<br>';print_r($edate);exit;
				$this->adminmodel->updatedealsnoimg ( $brandid, $sdate, $edate,$tagline, $description,$website,$email,$dealid,$bd,$d,$p,$mp,$vp,$qua,$keywords,$itemcode,$model,$nlc,$phc,$shc,$rsp,$shipsin,$fcp);
				if(count($changes)>0)
				$this->adminmodel->addactivity($userid,$msg."</div>",$dealid,$brandid);
				$this->db->close ();
				redirect ( "admin/deal/$dealid" );
			}
		}
	}
	/**
	 * Remove a deal
	 *
	 * @param int $id optional
	 * @param int $catid  signifies to which category a deal belongs to
	 */
	function removedeal($id = 'nil', $catid) {
		if ($this->session->userdata ( "admin_user" ) === false) {
			redirect ( "admin" );
			die ();
		}
		if ($id == "nil") {
			redirect ( "admin" );
			die ();
		}
		if ($catid == 4) {
			$this->adminmodel->deletehoteldeal ( $id );
		} else {
			$this->adminmodel->deleteitemdeal ( $id );
		}
		redirect ( "admin/deals" );
	}
	/**
	 * Loads a edit deal item page
	 *
	 * @param int $id could be optinal
	 * @param int $catid signifies to which category the deal item belongs to
	 */
	function edititem($id = 'nil') {
		$user=$this->session->userdata ( "admin_user" );
		if ($user== false) {
			redirect ( "admin" );
			die ();
		}
		if ($id == "nil") {
			redirect ( "admin" );
			die ();
		}
		$result = $this->adminmodel->getdealitemdetailsforid ( $id );
		if (isset ( $result [0] ))
		//print_r($result[0]);exit;
		if ($user['usertype' ] == 1)
		$data ['superadmin'] = TRUE;
		else
		$data ['adminheader'] = TRUE;
		$data['error']=$this->error;
		$data ['roomdetails'] = $result [0];
		$data ['page'] = 'admin_editroom';
		$this->load->view ( 'admin', $data );
	}
	
	function vars()
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		if($_POST)
		{
			$this->db->query("update king_vars set value=? where id=? limit 1",array(htmlspecialchars_decode($_POST['var']),$_POST['id']));
			redirect("admin/vars");
		}
		$data['vars']=$this->db->query("select * from king_vars order by id asc")->result_array();
		$data['page']="vars";
		$this->load->view("admin",$data);
	}
	
	/**
	 * Update Item details
	 *
	 * Updates a specific deal item of a deal and changes made are reflected in view deals page
	 */
	function updateitemdetails() {
		if ($this->session->userdata ( "admin_user" ) === false) {
			redirect ( "admin" );
			die ();
		}
		//		print_r($_POST);die;
		$user=$this->session->userdata("admin_user");
		$eflag=FALSE;
		$this->load->library ( "image_lib" );
		$imgname = $this->randomChars ( 15 );
		$id = $this->input->post ( 'roomid' );
		$dealid = $this->input->post ( 'dealid' );
		$brandid=$this->input->post("brandid");
		$name = $this->input->post ( 'roomname' );
		$price = $this->input->post ( 'price' );
		$originalprice = $this->input->post ( 'originalprice' );
		$description1 = $this->input->post ( 'description1' );
		$description2=$this->input->post("description2");
		$catid = $this->input->post ( 'catid' );

		if(strlen(trim($name))==0)
		{
			$this->error .='Please Enter the name'.'<br>';
			$eflag=TRUE;
		}
		if(strlen(trim($description1))==0)
		{
			$this->error .='Please Enter brief description'.'<br>';
			$eflag=TRUE;
		}
		if(strlen(trim($price))==0)
		{
			$this->error .='Please Enter the price'.'<br>';
			$eflag=TRUE;
		}
		if(strlen(trim($originalprice))==0)
		{
			$this->error .='Please Enter the originalprice'.'<br>';
			$eflag=TRUE;
		}
		/*		if ($catid == 4) {
			$heading = $this->input->post ( 'heading' );
			$tagline = $this->input->post ( 'tagline' );
			if(strlen(trim($heading))==0)
			{
			$this->error .='Please enter the heading'.'<br>';
			$eflag=TRUE;
			}
			if(strlen(trim($tagline))==0)
			{
			$this->error .='Please enter the tagline'.'<br>';
			$eflag=TRUE;
			}
			$startdate = $this->input->post ( 'startdate' );
			$enddate = $this->input->post ( 'enddate' );
			$dates = array ();
			$i2 = 0;
			foreach ( $startdate as $sdate ) {
			if (strlen ( trim ( $enddate [$i2] ) ) != 0)
			$str = $sdate . "-" . $enddate [$i2];
			else
			$str = $sdate . "-" . $sdate;
			$i2 ++;
			array_push ( $dates, $str );
			}
			$strdates = implode ( ",", $dates );
			$quantity = 0;
			list ( $year, $month, $day ) = split ( '[/.-]', $startdate );
			$stdate = mktime ( 0, 0, 0, $month, $day, $year );
			list ( $year, $month, $day ) = split ( '[/.-]', $enddate );
			$edate = mktime ( 0, 0, 0, $month, $day, $year );
			if($stdate>$edate)
			{
		 $this->error .="Start date and end date are invalid. Please check.".'<br>';
		 $eflag=TRUE;
		 }
			if($eflag==true)
			{
			$this->edititem($dealid);
			return;
			}
			if (isset ( $_FILES ['pic'] ) && $_FILES ['pic'] ['error'] == 0) {
			$img = $this->storeimagestoserver ( TRUE, $_FILES ['pic'] ['tmp_name'], $imgname );
			//$q = $this->adminmodel->updateroomdetailsimg ( $roomname, $heading, $tagline, $strdates, $price, $originalprice, $imgname, $roomid );
			$this->adminmodel->updatedealitemdetailsimg ( $id, $dealid, $price, $originalprice, $name, $quantity, $imgname, $description );
			$this->db->close ();
			$this->adminmodel->updateroomdetailsimg ( $id, $dealid, $heading, $tagline, $strdates );
			}
			if (! isset ( $_FILES ['pic'] )) {
			$this->adminmodel->updatedealitemdetailsnoimg ( $id, $dealid, $price, $originalprice, $name, $quantity, $description );
			$this->db->close ();
			$this->adminmodel->updateroomdetailsimg ( $id, $dealid, $heading, $tagline, $strdates );
			}
			} else {
			*/
		$quantity = $this->input->post ( 'quantity' );
		if(strlen(trim($quantity))==0)
		{
			$this->error .='Please enter the quantity'.'<br>';
			$eflag=TRUE;
		}
		if($eflag==true)
		{
			$this->edititem($dealid);
			return;
		}
		$itemid=$id;
		$userid=md5(strtolower($user['username']));
		$data=array("price"=>$price,"orgprice"=>$originalprice,"name"=>$name,"available"=>$quantity,"description1"=>$description1,"description2"=>$description2,"cod"=>$this->input->post("cod"),"groupbuy"=>$this->input->post("groupbuy"));
		$changes=$this->adminmodel->getitemchanges($itemid,$data);
		$msg="Item details changed. The following details in item '<b>$name</b>' are changed :<div>";
		foreach($changes as $c)
		$msg.=$c."<br>";
		if (isset ( $_FILES ['pic'] ) && $_FILES ['pic'] ['error'] == 0) {
			$img = $this->storeimagestoserver ( TRUE, $_FILES ['pic'] ['tmp_name'], $imgname );
			$this->adminmodel->updatedealitemdetailsimg ( $id, $dealid, $price, $originalprice, $name, $quantity, $imgname, $description1,$description2 );
			$msg.="profile pic";
			$this->adminmodel->addactivity($userid,$msg."</div>",$dealid,$brandid);
		}
		if (! isset ( $_FILES ['pic'] )) {
			$this->adminmodel->updatedealitemdetailsnoimg ( $id, $dealid, $price, $originalprice, $name, $quantity, $description1,$description2 );
			$this->adminmodel->addactivity($userid,$msg."</div>",$dealid,$brandid);
		}
		//		}
		redirect ( "admin/deal/$dealid" );
	}
	/**
	 *Loads remove dealitem page
	 *
	 * @param int $id optional
	 * @param int $catid indicates to which category it belongs to
	 */
	function removeroom($id = 'nil', $catid) {
		if ($this->session->userdata ( "admin_user" ) === false) {
			redirect ( "admin" );
			die ();
		}
		if ($id == "nil") {
			redirect ( "admin" );
			die ();
		}
		if ($catid == 4) {
			$this->adminmodel->deleteroomdetails ( $id );
		} else {
			$this->adminmodel->deleteitemdealdetails ( $id );
		}
		redirect ( "admin/deals" );
	}

	function transpic()
	{
		$this->load->library("thumbnail");
		$deals=$this->db->query("select pic from king_dealitems")->result_array();
		foreach($deals as $deal)
		{
			@unlink("images/items/".$deal['pic'].".jpg");
			$this->thumbnail->create(array("source"=>"images/items/big/".$deal['pic'].".jpg","dest"=>"images/items/".$deal['pic'].".jpg","width"=>300,"max_height"=>300));
		}
	}

	/**
	 * Loads pics and videos for a deal
	 *
	 * Gets pics and videos for a particular deal
	 *
	 * @param int $id optional
	 */
	function getpicsandvideos($id = 'nil') {
		$user=$this->session->userdata ( "admin_user" );
		if ($user== false) {
			redirect ( "admin" );
			die ();
		}
		if ($id == "nil") {
			redirect ( "admin" );
			die ();
		}
		$pictures = $this->adminmodel->getpicsforhotel ( $id );
		$this->db->close ();
		//print_r($pictures);exit;
		$videos = $this->adminmodel->getvideosforhotel ( $id );
		//print_r($videos);exit;
		if ($user[ 'usertype' ] == 1)
		$data ['superadmin'] = TRUE;
		else
		$data ['adminheader'] = TRUE;
		$data ['pictures'] = $pictures;
		$data ['videos'] = $videos;
		$data ['page'] = 'admin_deletepics';
		$this->load->view ( 'admin', $data );
	}

	/**
	 * Adds more pics and videos for a particular deal item
	 *
	 * @param int $id optional
	 * @param int $catid indicates the category to which the deal belongs to
	 */
	function addpicsandvideos($id = 'nil') {
		$user=$this->session->userdata("admin_user");
		if ($this->session->userdata ( "admin_user" ) === false)
		redirect ( "admin" );
		if ($id == "nil")
		redirect ( "admin" );
		$rooms = $this->adminmodel->getroomslist ( $id );
		if ($user['usertype'] == 1)
		$data ['superadmin'] = TRUE;
		else
		$data ['adminheader'] = TRUE;
		$data ['is_add'] = 'add';
		$data ['rooms'] = $rooms;
		$data ['id'] = $id;
		$data ['page'] = 'addpics';
		$this->load->view ( 'admin', $data );
	}

	/**
	 * Deletes pics for a particular deal
	 *
	 * @param int $id optional
	 * @param int $dealid
	 * @param int $itemid
	 */
	function deletehotelpic($id, $dealid, $itemid) {
		if ($this->session->userdata ( "admin_user" ) === false) {
			redirect ( "admin" );
			die ();
		}
		if ($id == "nil") {
			redirect ( "admin" );
			die ();
		}
		$this->adminmodel->deletepicandvideos ( $id, $itemid, $dealid );
		unlink ( realpath ( APPPATH ) . '/images/items/' . $id . '.jpg' );
		redirect ( "admin/getpicsandvideos/$dealid" );
	}
	/**
	 * Deletes Videos of a particular deal
	 *
	 * @param int $id
	 * @param int $dealid
	 * @param int $itemid
	 */
	function deletehotelvideo($id, $dealid, $itemid) {
		if ($this->session->userdata ( "admin_user" ) === false) {
			redirect ( "admin" );
			die ();
		}
		if ($id == "nil") {
			redirect ( "admin" );
			die ();
		}
		$this->adminmodel->deletepicandvideos ( $id, $itemid, $dealid );
		redirect ( "admin/getpicsandvideos/$dealid" );
	}
	/**
	 * Gets sub category
	 *
	 * gets the list of subcategory for particular category
	 */
	function getsubcat() {
		if ($this->session->userdata ( "admin_user" ) === false) {
			redirect ( "admin" );
			die ();
		}
		//echo 'hi';exit;
		if ($this->input->post ( 'cat_id', TRUE ) != '') {
			$catid = $this->input->post ( 'cat_id' );
			$subcatlist = $this->adminmodel->getsubcategories ( $catid );
			//			print_r($subcatlist);exit;
			$output = '';
			if ($subcatlist != FALSE) {
				$output .= '<div class="span"><label>Sub Category</label></div><div class="span1">';
				$output .= '<select name="subcatname" id="subcatid">';
				$output .= "<option value='0'>------Select------</option>";
				foreach ( $subcatlist as $subcat ) {
					$output .= '<option value="' . $subcat->id . '">' . $subcat->name . '</option>';
				}
				$output .= '</select></div>';
				echo $output;
			} else
			echo "0";
		}

	}
	/**
	 * Gets Subcategory for superadmin
	 *
	 */
	function getsubcatforsuperadmin() {
		$user=$this->session->userdata ( "admin_user" );
		if ($user== false ||$user["usertype"]!=1) {
			redirect ( "admin" );
			die ();
		}
		//echo 'hi';exit;
		if ($this->input->post ( 'cat_id', TRUE ) != '') {
			$catid = $this->input->post ( 'cat_id' );
			$subcatlist = $this->adminmodel->getsubcategories ( $catid );
			//			print_r($subcatlist);exit;
			$output = '';
			if ($subcatlist != FALSE) {
				$output .= '<div class="span"><label>Sub Category</label></div><div id="subcat" class="span1">';
				$output .= '<select name="subcatname" id="subcatid" onchange="addsubcat()">';
				$output .= "<option value='0'>------Select-----</option>";
				foreach ( $subcatlist as $subcat ) {
					$output .= '<option value="' . $subcat->id . '">' . $subcat->name . '</option>';
				}
				$output .= "<option value='others' style='font-style: italic;'>newsubcategory</option>";
				$output .= '</select></div>';
				echo $output;
			} else
			echo "0";
		}

	}
	/**
	 * Inserts New subcategory
	 *
	 */
	function insnewsubcat() {
		$user=$this->session->userdata ( "admin_user" );
		if ($user== false ||$user["usertype"]!=1) {
			redirect ( "admin" );
			die ();
		}
		if ($this->input->post ( 'subcatname', TRUE ) != '') {
			$subcatname = $this->input->post ( 'subcatname' );
			$type = $this->input->post ( 'catid' );
			// print_r($type);exit;
			$res = $this->adminmodel->insertsubcategories ( $type, $subcatname );
			if ($res != FALSE)
			$subcatlist = $this->adminmodel->getsubcategories ( $type );
			$output = '';
			if ($subcatlist != FALSE) {
				$output .= '<div class="span"><label>Sub Category</label></div><div id="subcat" class="span1">';
				$output .= '<select name="subcatname" id="subcatid"  onchange="addsubcat();">';
				$output .= "<option value='0'>------Select------</option>";
				foreach ( $subcatlist as $subcat ) {
					$output .= '<option value="' . $subcat->id . '">' . $subcat->name . '</option>';
				}
				$output .= "<option value='others' style='font-style: italic;'>newsubcategory</option>";
				$output .= '</select></div>';
				echo $output;
			} else
			echo "0";
		}
	}
	/**
	 * Gets the list of categories
	 *
	 */
	function addcategory() {
		$user=$this->session->userdata ( "admin_user" );

		if ($user== false ||$user["usertype"]!=1) {
			redirect ( "admin" );
			die ();
		}
		$categories = $this->adminmodel->getcategory ();
	}
	/**
	 *Publish a deal
	 *
	 * @param int $dealid
	 * @param int $catid
	 * @param int $publish
	 */
	function publishdeal($dealid, $catid, $itemid,$publish) {
		$user=$this->session->userdata ( "admin_user" );
		if ($user== false ||$user["usertype"]!=1) {
			redirect ( "admin" );
			die ();
		}
		$live=$this->input->post("live")?"1":"0";
		$this->adminmodel->publish ( $dealid, $catid, $itemid,$publish,$this->input->post("agentcom"),$live);
		redirect ( 'admin/deal/'.$dealid );
	}

	function changecom()
	{
		$user=$this->session->userdata ( "admin_user" );
		if ($user== false ||$user["usertype"]!=1 || !$_POST)
		redirect ( "admin" );
		$itemid=$this->input->post("itemid");
		$com=$this->input->post("agentcom");
		$d=$this->input->post("dealid");
		$this->dbm->changecom($itemid,$com);
		redirect("admin/deal/$d");
	}

	function livedeal($dealid,$itemid,$live)
	{
		$user=$this->session->userdata ( "admin_user" );
		if ($user== false ||$user["usertype"]!=1)
		redirect ( "admin" );
		$this->adminmodel->livedeal($itemid,$live);
		redirect("admin/deal/".$dealid);
	}

	function getbrandsunderscategory($catid)
	{
		$user=$this->session->userdata ( "admin_user" );
		if ($user== false ||$user["usertype"]!=1)
		redirect ( "admin" );
		//echo $catid;
		//$brands=$this->adminmodel->getbrandsfordashboard(1);
		$brands=$this->adminmodel->getbrandlistnotunderacat($catid);
		//print_r($brands);exit;
		$data['brands']=$brands;
		$categories=$this->adminmodel->getcategoriesfordashboard(1);
		if($categories!=FALSE)
		$data['categories']=$categories;
		$specificcatdetails=$this->adminmodel->getspecificcategorydetails($catid);
		//print_r($specificcatdetails);exit;
		$data['specificcatdetails']=$specificcatdetails[0];
		$brandsundercat=$this->adminmodel->getbrandslistforspecificcategory($catid);
		$data['brandsundercat']=$brandsundercat;
		$data['superadmin']=TRUE;
		if($this->info)
		$data['info']=$this->info;
		if($this->error)
		$data['error']=$this->error;
		$data['page']='superadmin_editaddcategories';
		$this->load->view('admin',$data);
	}

	function users($p=1,$sort="created",$order="d")
	{
		$user=$this->auth(CALLCENTER_ROLE);
		$data['users']=$this->adminmodel->getsiteusers($p,$sort,$order);
		$data['p']=$p;
		$data['len']=$data['totalusers']=$this->adminmodel->getsiteuserslen();
		$data['page']="superadmin_users";
		$data['superadmin']=true;
		$data['facebookusers']=$this->adminmodel->getuserslenbylogin(2);
		$data['googleusers']=$this->adminmodel->getuserslenbylogin(3);
		$data['twitterusers']=$this->adminmodel->getuserslenbylogin(1);
		$data['corporates']=$this->adminmodel->getcorporateslen();
		//		$data['big_corporate']=$this->adminmodel->getbigcorporate();
		$data['prevurl']=site_url("admin/users/".($p-1)."/$sort/$order");
		$data['nexturl']=site_url("admin/users/".($p+1)."/$sort/$order");
		$data['url']=site_url("admin/users");
		$this->load->view("admin",$data);
	}

	function usersbylogin($type,$p=1,$sort="created",$order="d")
	{
		$user=$this->session->userdata("admin_user");
		if($user==false || $user['usertype']!=1)
		redirect("admin");
		switch($type)
		{
			case "normal":
				$typeid=0;break;
			case "facebook":
				$typeid=2;break;
			case "google":
				$typeid=3;break;
			case "twitter":
				$typeid=1;break;
			default:
				$typeid=2124;break;
		}
		if($typeid==2124)
		redirect("admin/users");
		$data['users']=$this->adminmodel->getsiteusersbylogin($typeid,$p,$sort,$order);
		$data['p']=$p;
		$data['totalusers']=$this->adminmodel->getsiteuserslen();
		$data['page']="superadmin_users";
		$data['superadmin']=true;
		$data['pagetitle']=ucfirst($type)." users";
		$data['facebookusers']=$this->adminmodel->getuserslenbylogin(2);
		$data['googleusers']=$this->adminmodel->getuserslenbylogin(3);
		$data['twitterusers']=$this->adminmodel->getuserslenbylogin(1);
		$data['len']=$this->adminmodel->getuserslenbylogin($typeid);
		$data['prevurl']=site_url("admin/usersbylogin/$type/".($p-1)."/$sort/$order");
		$data['nexturl']=site_url("admin/usersbylogin/$type/".($p+1)."/$sort/$order");
		$data['url']=site_url("admin/usersbylogin/$type");
		$this->load->view("admin",$data);
	}

	function usersbycorp($corp,$p=1,$sort="created",$order="d")
	{
		$user=$this->session->userdata("admin_user");
		if($user==false || $user['usertype']!=1)
		redirect("admin");
		$data['users']=$this->adminmodel->getsiteusersbycorp($corp,$p,$sort,$order);
		$data['p']=$p;
		$data['totalusers']=$this->adminmodel->getsiteuserslen();
		$data['page']="superadmin_users";
		$data['superadmin']=true;
		$data['pagetitle']="Members of ".$this->db->query("select name from king_corporates where id=?",$corp)->row()->name;
		$data['facebookusers']=$this->adminmodel->getuserslenbylogin(2);
		$data['googleusers']=$this->adminmodel->getuserslenbylogin(3);
		$data['twitterusers']=$this->adminmodel->getuserslenbylogin(1);
		$data['len']=$this->adminmodel->getuserslenbycorp($corp);
		$data['prevurl']=site_url("admin/usersbycorp/$corp/".($p-1)."/$sort/$order");
		$data['nexturl']=site_url("admin/usersbycorp/$corp/".($p+1)."/$sort/$order");
		$data['url']=site_url("admin/usersbycorp/$corp");
		$this->load->view("admin",$data);
	}

	function user($uid)
	{
		$user=$this->auth(CALLCENTER_ROLE);
		$data['userdet']=$suser=$this->adminmodel->getsiteuser($uid);
		if($suser==false)
		redirect("admin/users");
		if($this->db->query("select 1 from pnh_member_info where user_id=?",$uid)->num_rows()!=0)
			redirect("admin/pnh_viewmember/$uid");
		$data['pagetitle']="User Details";
		$data['page']="viewuser";
		$data['referrals']=$this->adminmodel->getreferrals($uid);
		$data['orders']=$this->adminmodel->recentordersbyuser($uid);
		$data['ftrans']=$this->adminmodel->recentftransactionsbyuser($uid);
		$data['tickets']=$this->adminmodel->recentticketsbyuser($uid);
		$this->load->view("admin",$data);
	}

	function blockuser()
	{
		$user=$this->session->userdata("admin_user");
		if($user==false || $user['usertype']!=1)
		redirect("admin");
		if($this->input->post("userid")!==false && $this->input->post("action")!==false)
		{
			$this->adminmodel->blocksiteuser($this->input->post("userid",true),$this->input->post("action",true));
			redirect("admin/user/".$this->input->post("userid"));
		}
	}

	/*function invoice($transid,$oids="")
	 {
		$sql="select item.nlc,item.phc,ordert.*,item.price,item.tax,item.service_tax,
		item.service_tax_cod,item.name,in.invoice_no,
		brand.name as brandname,in.phc,
		in.nlc,in.service_tax,in.tax
		from king_orders as ordert
		join king_dealitems as item on item.id=ordert.itemid
		join king_deals as deal on deal.dealid=item.dealid
		join king_brands as brand on brand.id=deal.brandid
		left outer join king_invoice `in` on in.transid=ordert.transid and in.order_id=ordert.id
		where ordert.transid=?";
		$q=$this->db->query($sql,array($transid));
		$data['page']="../../body/invoice";
		$data['orders']=$orders=$q->result_array();
		if(!empty($oids))
		{
			
		$data['includes']=$ins=explode(",",$oids);
			
		$invno_suffix = 1;
			
		$this->db->where('orders',implode(",",$ins));
		$this->db->where('invoice_no',$orders[0]['invoice_no']);
		$total_sub_inv_available = $this->db->count_all_results('king_sub_invoice');
			
			
		$sql = " select invoice_no,sub from king_sub_invoice where invoice_no = ? and orders in (".implode(",",$ins).") ";
		$res = $this->db->query($sql,$orders[0]['invoice_no']);
		$total_sub_inv_available = $res->num_rows();
			
		if($total_sub_inv_available){
		$row = $res->result_array();
		$data['invoice_no']=$orders[0]['invoice_no']."-".$row[0]['sub'];
		}else{
		$this->db->where('invoice_no',$orders[0]['invoice_no']);
		$total_avail = $this->db->count_all_results('king_sub_invoice') + 1;

		$invno_suffix = 65+$total_avail;

		$data['invoice_no']=$orders[0]['invoice_no']."-".$invno_suffix;
		$this->db->query("insert into king_sub_invoice(invoice_no,sub,orders,time) values(?,?,?,?)",array($orders[0]['invoice_no'],$invno_suffix,implode(",",$ins),time()));
			
		}
			
			
		//$r=rand(1,99);
			
			
		}
		$data['trans']=$this->db->query("select * from king_transactions where transid=?",$transid)->row_array();
		$this->load->view("admin",$data);
		}*/


	function invoice($invoice_no,$inv_type='customer')
	{
		$this->erpm->auth();
		
		$inv_bygrpno = $this->db->query("select distinct split_inv_grpno 
	from shipment_batch_process_invoice_link a 
	join proforma_invoices b on a.p_invoice_no = b.p_invoice_no 
	join king_invoice c on c.invoice_no = a.invoice_no  
where (b.dispatch_id = ? or c.ref_dispatch_id = ? ) 
	
",array($invoice_no,$invoice_no))->row()->split_inv_grpno;

	if($inv_bygrpno)
		$invoice_no = $inv_bygrpno;
		 
		
//		$batch=$this->db->query("select * from shipment_batch_process_invoice_link where invoice_no=?",$invoice_no)->row_array();
//		if(!empty($batch) && $batch['packed']==0 && $this->db->query("select invoice_status as s from king_invoice where invoice_no=?",$invoice_no)->row()->s==1)
//			show_error("Invoice is not available for printing until its packed");
		
		$sql="select in.invoice_no,item.nlc,item.phc,ordert.*,
							item.service_tax_cod,item.name,in.invoice_no,
							brand.name as brandname,
							in.mrp,in.tax as tax,
							in.discount,
							in.phc,in.nlc,
							in.service_tax,
							item.pnh_id,f.*
						from king_orders as ordert
						join king_dealitems as item on item.id=ordert.itemid 
						join king_deals as deal on deal.dealid=item.dealid 
						left join king_brands as brand on brand.id=deal.brandid
						left join pnh_m_offers f on f.id= ordert.offer_refid
						join king_invoice `in` on in.transid=ordert.transid and in.order_id=ordert.id  
						where (in.invoice_no=? or split_inv_grpno = ? or ref_dispatch_id = ? )
						group by in.invoice_no  
				";
		$q=$this->db->query($sql,array($invoice_no,$invoice_no,$invoice_no));
		
	 
		$data['page']="../../body/invoice";
		$data['invoice_list']=$orders=$q->result_array();
		$data['invoice_no']=$invoice_no;
		$data['trans']=$this->db->query("select * from king_transactions where transid=?",$orders[0]['transid'])->row_array();
		
		$data['inv_type'] = $inv_type;
		$this->load->view("admin",$data);
	}
	
	
	
	function print_allinv($is_active=1,$from="",$to="",$limit='',$st='',$fid=0){
		
		$inv_type='customer';
		

//		$is_active = $is_active?1:0; 
		$from = $from?$from:date('Y-m-d');
		$to = $to?$to:date('Y-m-d');

		$cond = '';
		if($fid)
			$cond.= ' and franchise_id = '.$fid;	
		$inv_type = 'aud11iting';
		$sql = "select distinct a.invoice_no from king_invoice a  join king_orders b on a.order_id = b.id join king_transactions c on c.transid = b.transid where date(from_unixtime(createdon)) >= ? and date(from_unixtime(createdon)) <= ?  and invoice_status = ? $cond group by a.invoice_no order by a.invoice_no ";

		if($limit !== ''){
			if($st !== '')
				$sql .= " limit $st,$limit ";
			else
				$sql .= " limit $limit ";
		}


		$res = $this->db->query($sql,array($from,$to,$is_active));
		
		//echo $this->db->last_query();

		

		foreach($res->result_array() as $i=>$row){
			
			$invoice_no = $row['invoice_no'];
			
			$sql="select item.nlc,item.phc,ordert.*,
			item.service_tax_cod,item.name,in.invoice_no,
			 
			in.mrp,ordert.i_tax as tax,in.discount,in.phc,in.nlc,in.service_tax
			from king_orders as ordert
			join king_dealitems as item on item.id=ordert.itemid
			join king_deals as deal on deal.dealid=item.dealid
		 
			join king_invoice `in` on in.transid=ordert.transid and in.order_id=ordert.id
			where in.invoice_no=?  ";
			//$q=$this->db->query($sql,array($invoice_no));
			
			
			$sql="select in.invoice_no,item.nlc,item.phc,ordert.*,
							item.service_tax_cod,item.name,in.invoice_no,
							brand.name as brandname,
							in.mrp,in.tax as tax,
							in.discount,
							in.phc,in.nlc,
							in.service_tax,
							item.pnh_id,f.*
						from king_orders as ordert
						join king_dealitems as item on item.id=ordert.itemid 
						join king_deals as deal on deal.dealid=item.dealid 
						left join king_brands as brand on brand.id=deal.brandid
						left join pnh_m_offers f on f.id= ordert.offer_refid
						join king_invoice `in` on in.transid=ordert.transid and in.order_id=ordert.id  
						where (in.invoice_no=? )
						group by in.invoice_no  
				";
		$q=$this->db->query($sql,array($invoice_no,$invoice_no));
		
			if($q->num_rows()){
				//$data['page_top'] = 900*$i+20;
				//$data['page']="../../body/invoice";
				//$data['invoice_list']=$orders=$q->result_array();
				//$data['invoice_no']=$orders[0]['invoice_no'];
				//$data['trans']=$this->db->query("select * from king_transactions where transid=?",$orders[0]['transid'])->row_array();
				//$data['inv_type'] = $inv_type;
				
				echo '<div style="page-break-after:always">';
				$data['page']="../../body/invoice_bulk";
				$data['invoice_list']=$orders=$q->result_array();
				$data['invoice_no']=$invoice_no;
				$data['trans']=$this->db->query("select * from king_transactions where transid=?",$orders[0]['transid'])->row_array();
		
				$data['inv_type'] = $inv_type;
		
				$this->load->view("body/invoice_bulk",$data);
				
				echo '</div>';
			}
		}
		
		
	}
	
	
	function generate_sales_register($invoice_status=1,$from="",$to="",$limit='',$st=''){
	
		if($invoice_status == 'active')
		{
			$invoice_status = 1;
		}elseif($invoice_status == 'cancelled'){
			$invoice_status = 2;
		}else{
			$invoice_status = 0;
		}
	 
		$from = $from?$from:date('Y-m-d');
		$to = $to?$to:date('Y-m-d');
	
		/*
		$inv_type = 'auditing';
		$sql = "select distinct invoice_no from king_invoice 
						where date(from_unixtime(createdon)) >= ? and date(from_unixtime(createdon)) <= ?  
						";
		if($invoice_status)
		{
			$sql .= " and invoice_status = ".$invoice_status;
		}
			$sql .= "  order by invoice_no ";
	
		if($limit !== ''){
			if($st !== '')
				$sql .= " limit $st,$limit ";
			else
				$sql .= " limit $limit ";
		}
	
	
		$res = $this->db->query($sql,array($from,$to));
		*/
	
		$inv_sales_report_data = array();
	
	
		//foreach($res->result_array() as $i=>$row){
		if(1){
	
			//$invoice_no = $row['invoice_no'];
	
			$sql="	select inv.createdon,inv.invoice_no,
						inv.invoice_status,ship_state as state, 
						sum(inv.mrp*ordert.quantity) as mrp_total , 
						sum(inv.discount*ordert.quantity) as discount_total, 
						inv.tax/100 as tax, 
						(inv.cod+inv.ship+inv.giftwrap_charge) as total_charge,
						inv.service_tax/100  as service_tax
					from king_orders as ordert 
					join king_dealitems as item on item.id=ordert.itemid 
					join king_deals as deal on deal.dealid=item.dealid 
					join king_invoice `inv` on inv.transid=ordert.transid and inv.order_id=ordert.id 
					where date(from_unixtime(createdon)) >= ? and date(from_unixtime(createdon)) <= ? 
					group by inv.invoice_no, inv.tax order by inv.invoice_no,total_charge desc; 
				";
			$q=$this->db->query($sql,array($from,$to));
			
			//echo $this->db->last_query();
			if($q->num_rows()){
			
				$stat = 0;
				$inv_no = 0;
				$res_set = $q->result_array();
				foreach($res_set as $orders){
				//	print_r($orders);
				
					if(!$inv_no){
						$inv_no = $orders['invoice_no'];
					}else if($inv_no != $orders['invoice_no']){
						$stat = 0;
						$inv_no = $orders['invoice_no'];
					}
					
					$tmp = array();
					$tmp['date'] =  date('Y-m-d',$orders['createdon']);
					$tmp['invoice_no'] =  $orders['invoice_no'];
					if($orders['invoice_status'] == 1)
						$tmp['invoice_status'] = 'Active';
					elseif($orders['invoice_status'] == 2)
						$tmp['invoice_status'] = 'Cancelled';
					elseif($orders['invoice_status'] == 0)
						$tmp['invoice_status'] = 'Invalid';
					
					$tmp['state'] =  $orders['state'];
					$tmp['basic_amount'] =  $orders['mrp_total'];
					$tmp['less_discount'] =  round($orders['discount_total'],2);
					$tmp['net_amount'] =  round($orders['mrp_total']- $orders['discount_total'],2);
					$tmp['taxable_amount'] =  round($tmp['net_amount']/(100+$orders['tax'])*100,2);
					$tmp['rate_tax'] =  $orders['tax'];
					$tmp['tax_amount'] =  round($tmp['taxable_amount']*$tmp['rate_tax']/100,2);
					
					if($stat==0){
						$total_charge = $orders['total_charge'];
						$stat = 1; 
					}else{
						$total_charge = 0;
					}
					
					 
					
					
					$handling_charge = round($total_charge/(100+$orders['service_tax'])*100,2);
					$service_charge =round($handling_charge*$orders['service_tax']/100,2); 
					
					$tmp['handling_charges'] =  $handling_charge;
					$tmp['service_tax'] =  $service_charge;
					$tmp['total_amount'] =  round($tmp['taxable_amount']+$tmp['tax_amount']+$handling_charge+$service_charge);
					
					array_push($inv_sales_report_data,$tmp);
				}
				
			}
		}
		
		if(count($inv_sales_report_data))
		{
			$this->load->helper('csv');
			$headers = array('Date','Invoice No','Invoice Status','State','Basic Amount ','Less Discount','Net Amount','Taxable Amount','Rate Of Tax','Tax Amount','Handling Charges','Service Tax','Total Bill Amount');
			array_to_csv($inv_sales_report_data,"LC Sales Regiser.csv",',',$headers); 
		}
		else
		{
			echo "No data found";	
		}
		
		
		
	
	
	}
		
	
	function bulk_orders($status=0,$p = 1, $sort = "ordertime", $order = "d", $from = "", $to = "",$priority = 0, $stock_avail = 0)
	{
		$status = 'pending';
		
		$user=$this->session->userdata("admin_user");
		if($user==false)
			redirect("admin");
		$brandid=$user['brandid'];
		if($user['usertype']==1)
			$data['superadmin']=true;
		else
			$data['adminheader']=true;
		
		$sql = "select ifnull(s.available,0) as avail_qty,
						orders.is_giftcard,orders.ship_city,t.status as trans_status,
						t.admin_trans_status,orders.admin_order_status,
						orders.transid,brand.name as brandname,
						item.id as itemid,item.dealid,
						item.name as itemname,orders.quantity,
						orders.id,orders.status,orders.actiontime,
						orders.time,user.name as username 
					from king_orders as orders 
					join king_users as user on user.userid=orders.userid 
					join king_dealitems as item on item.id=orders.itemid 
					join king_brands as brand on brand.id=orders.brandid 
					join king_transactions t on t.transid = orders.transid
					left join king_stock s on s.itemid=orders.itemid and s.available >= orders.quantity 
					where orders.admin_order_status = 0  
					order by orders.priority desc 
				";
		
		$data ['orders'] = $this->db->query($sql)->result();
		$data ['page'] = 'admin_bulk_orders';
		
		$this->load->view ( "admin", $data );
	}
	
	
	
	function allotment_list($pg=0){
		//$data ['orders'] = $this->db->query($sql)->result();
		
		$ttl_allotments = $this->db->count_all_results("king_bulkorders_invoices");
		$data['allot_list_res'] = $this->db->query("select tot_printed,allotment_no,invoice_nos,created_on from king_bulkorders_invoices order by created_on desc limit $pg,10");

		$this->config->set_item('enable_query_strings',FALSE);
		$this->load->library('pagination');
		$config['base_url'] = site_url('admin/allotment_list');
		$config['total_rows'] = $ttl_allotments;
		$config['per_page'] = 10;
		$config['uri_segment'] = 3;
		
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		
		$this->config->set_item('enable_query_strings',TRUE);
		
		$data ['pg'] = $pg;
		$data ['page'] = 'admin_allotment_list';
		$this->load->view ( "admin", $data );
	}
	
	
	/**
	 * function to product_stock_unavailble_report 
	 */
	function product_stock_unavailble_report()
	{
		
	
	
		//$statlist = explode(',',$statlist);
		//$statlist = array_filter($statlist);
	
		$from = date('Y-m-d',strtotime('2011-01-01'));
		$to = date('Y-m-d',time());
	
		
		$q_statlist_str = 0;
	
		$sql = "select concat(o.itemid,' ') as itemid, o.brandid, b.name as brand, i.name as deal, o.i_price as offer_price, 
							o.i_orgprice as mrp, sum(o.quantity) as pending_order_qty, 
							ifnull(stk.available,0) as available_stock,(sum(o.quantity)-ifnull(stk.available,0)) as required_stock 
					from king_orders o
					join king_dealitems i on i.id = o.itemid
					join king_brands b on b.id = o.brandid
					left join king_stock stk on stk.itemid = i.id and stk.available < o.quantity  
					where date(from_unixtime(o.time)) >=  date(?) and date(from_unixtime(o.time)) <=  date(?) 
					and admin_order_status in (".$q_statlist_str.")
					group by o.itemid  order by b.name, i.name;";
	
		$query = $this->db->query($sql,array($from,$to));
	
	
		if($query->num_rows())
		{
			$delimiter = ",";
			$newline = "\r\n";
	
	
			$filename = 'StockReport_';
			if($from == $to)
			{
				$filename .= date('dmY',strtotime($from));
			}else{
				$filename .= date('dmY',strtotime($from)).'_'.date('dmY',strtotime($to));
			}
			$filename .= '.csv';
			// send response headers to the browser
			header( 'Content-Type: text/csv' );
			header( 'Content-Disposition: attachment;filename='.$filename);
			$fp = fopen('php://output', 'w');
	
	
	
			$this->load->dbutil();
			echo $this->dbutil->csv_from_result($query, $delimiter, $newline);
	
	
		}
		else
		{
			echo '<script type="text/javascript">alert("No Orders Found");window.close();</script>';
		}
	
	
	
	}
	
	

		function transbystatus($status, $p = 1, $sort = "ordertime", $order = "d", $from = "", $to = "",$priority=0,$stock_avail=0) {
			$user = $this->session->userdata ( "admin_user" );
			if ($user == false)
			redirect ( "admin" );
			$brandid = $user ['brandid'];
			if ($user ['usertype'] == 1)
			$data ['superadmin'] = true;
			else
			$data ['adminheader'] = true;

			$statuses = array ("pending", "pinvoiced", "invoiced", "pshipped", "shipped", "closed", "cancelled");

			if (! in_array ( $status, $statuses ))
			redirect ( "admin/orders" );
			if ($user ['usertype'] == 1)
			$data ['brands'] = $this->adminmodel->getallbrands ();
			$data ['orders'] = $this->adminmodel->gettransbystatus ( $status, $brandid, $p, $sort, $order, $from, $to,$priority,$stock_avail );
			$data ['p'] = $p;
			$data ['len'] = $this->adminmodel->gettranslenbystatus ( $brandid, $status, $from, $to,$priority,$stock_avail );
			$data ['page'] = "admin_orders";
			$data ['url'] = site_url ( "admin/transbystatus/$status" );
			$data ['nexturl'] = site_url ( "admin/transbystatus/$status/" . ($p + 1) . "/$sort/$order/$from/$to/$priority/$stock_avail" );

			$data ['navurl'] = site_url ( "admin/transbystatus/$status/PAGINATE/$sort/$order/$from/$to/$priority/$stock_avail" );

			$data ['prevurl'] = site_url ( "admin/transbystatus/$status/" . ($p - 1) . "/$sort/$order/$from/$to/$priority/$stock_avail" );

			if($status == 'pinvoiced'){
				$status = 'partial invoiced';
			}elseif($status == 'pinvoiced'){
				$status = 'partial shipped';
			}
			$data ['pagetitle'] = ucfirst ( $status ) . " Transactions ";

			$data ['from'] = $from;
			$data ['to'] = $to;
			$data ['order'] = $order;
			$data ['sort'] = $sort;

			$data['high_priority'] = $priority;
			$data['stock_avail'] = $stock_avail;

			$this->load->view("admin",$data);
		}

		function orderlist($status,$from="",$to="",$readytoship=0)
		{
			if($status=="priority")
			{
				$status="notshipped";
				$priority = 1;
			}
		//	$readytoship = 1;
			
			$sql="select t.id as si,i.orgprice,i.price,i.name,o.* from king_orders o join king_transactions t on t.transid=o.transid join king_dealitems i on i.id=o.itemid";
			if($readytoship)
			$sql.=" join king_stock s on s.itemid=o.itemid and (s.available+1)>=o.quantity";
			if($status!="all")
			{
				$sql.=" where ";
				switch($status)
				{
					case "pending" :
						$sql.="o.admin_order_status=0";
						break;
					case "notshipped" :
						$sql.="o.admin_order_status=1";
						break;
					 
					case "shipped":
						$sql.="o.admin_order_status=3";
						break;
					case "delivered" :
						$sql.="o.admin_order_status=4";
						break;	
					case "rejected" :
						$sql.="o.admin_order_status=6";
						break;
					case "returned" :
						$sql.="o.admin_order_status=5";
						break;  
				}
			}
			else
			$sql.=" where 1";
			if($priority)
			$sql.=" and o.priority=1";
			if(!empty($from) && !empty($to))
			{
				list($d,$m,$y)=explode("-",$from);
				$from=mktime(0,0,0,$m,$d,$y);
				list($d,$m,$y)=explode("-",$to);
				$to=mktime(23,59,59,$m,$d,$y);
				$sql.=" and o.time between $from and $to";
			}
			$sql.=" order by o.time asc";
			$data=$this->db->query($sql)->result_array();
			$ret=array();
			foreach($data as $d)
			{
				if(!isset($ret[$d['transid']]))
				$ret[$d['transid']]=array();
				$ret[$d['transid']][]=$d;
			}
			$data['readytoship']=$readytoship;
			$data['status']=$status;
			$data['from']=$from;
			$data['to']=$to;
			$data['list']=$ret;
			$this->load->view("admin/body/orderlist",$data);
		}

		function prodlist($status,$from="",$to="")
		{
			$priority = 0;
			if($status=="priority")
			{
				$status="notshipped";
				$priority = 1;
			}
			$readytoship = 1;
			$sql="select t.id as si,i.orgprice,i.price,i.id as itemid,i.name,o.* from king_orders o join king_transactions t on t.transid=o.transid join king_dealitems i on i.id=o.itemid";
			if($readytoship)
			$sql.=" join king_stock s on s.itemid=o.itemid and (s.available+1)>=o.quantity";
			if($status!="all")
			{
				$sql.=" where ";
				switch($status)
				{
					case "pending" :
						$sql.="o.admin_order_status=0";
						break;
					case "notshipped" :
						$sql.="o.admin_order_status=1";
						break;
					 
					case "shipped":
						$sql.="o.admin_order_status=3";
						break;
					case "delivered" :
						$sql.="o.admin_order_status=4";
						break;	
					case "rejected" :
						$sql.="o.admin_order_status=6";
						break;
					case "returned" :
						$sql.="o.admin_order_status=5";
						break; 
				}
			}
			else
			$sql.=" where 1";
			if($priority)
			$sql.=" and o.priority=1";
			if(!empty($from) && !empty($to))
			{
				list($d,$m,$y)=explode("-",$from);
				$from=mktime(0,0,0,$m,$d,$y);
				list($d,$m,$y)=explode("-",$to);
				$to=mktime(23,59,59,$m,$d,$y);
				$sql.=" and o.time between $from and $to";
			}
			$sql.=" order by o.time asc";
			$data=$this->db->query($sql)->result_array();
			$prods=$ret=array();
			foreach($data as $d)
			{
				$ind="p{$d['itemid']}";
				$name=$d['name'];
				$buyer_options=unserialize($d['buyer_options']);
				if(is_array($buyer_options))
				foreach($buyer_options as $mean=>$opt){
					$name.=" (<b>Buyer options : $mean:$opt</b>),";
					$ind.="$mean:$opt";
				}
				if(!isset($ret[$ind]))
				$ret[$ind]=array("name"=>$name,"qty"=>0,"itemid"=>$d['itemid']);
				$ret[$ind]['qty']+=$d['quantity'];
			}

			$t_ret=$ret;
			$ret=array();
			foreach($t_ret as $ind=>$r)
			{
				$s=$this->db->query("select available from king_stock where itemid=?",$r['itemid'])->row_array();
				if(empty($s))
				$s=0;
				else
				$s=$s['available'];
				if($r['qty']<=$s)
				continue;
				$r['qty']-=$s;
				$prods[$ind]=$r['name'];
				$ret[$ind]=$r;
			}

			asort($prods);
			$tret=$ret;
			$ret=array();
			foreach($prods as $n=>$p)
			$ret[$n]=$tret[$n];

			$data['status']=$status;
			$data['from']=$from;
			$data['to']=$to;
			$data['list']=$ret;
			$this->load->view("admin/body/prodlist",$data);
		}

		function ordersbyuser($uid,$p=1,$sort="ordertime",$order="d",$from="",$to="",$priority=1,$stock_avail=0)
		{
			$user=$this->session->userdata("admin_user");
			if($user==false)
			redirect("admin");
			$brandid=$user['brandid'];
			if($user['usertype']==1)
			$data['superadmin']=true;
			else
			$data['adminheader']=true;
			$sus=$this->adminmodel->getsiteuser($uid);
			if(!isset($sus[0]->name))
			exit;
			$suser=$sus[0]->name;
			if($user['usertype']==1)
			$data['brands']=$this->adminmodel->getallbrands();
			$data['orders']=$this->adminmodel->getordersbyuser($uid,$brandid,$p,$sort,$order,$from,$to);
			$data['p']=$p;
			$data['len']=$this->adminmodel->getorderslenbyuser($brandid,$uid,$from,$to);
			$data['page']="admin_orders";
			$data['url']=site_url("admin/ordersbyuser/$uid");
			//$data['nexturl']=site_url("admin/ordersbyuser/$uid/".($p+1)."/$sort/$order/$from/$to");
			//$data['prevurl']=site_url("admin/ordersbyuser/$uid/".($p-1)."/$sort/$order/$from/$to");
			
			
			$data ['nexturl'] = site_url ( "admin/ordersbyuser/$uid/" . ($p + 1) . "/$sort/$order/$from/$to/$priority/$stock_avail" );
			
			$data ['navurl'] = site_url ( "admin/ordersbyuser/$uid/PAGINATE/$sort/$order/$from/$to/$priority/$stock_avail" );
			
			$data ['prevurl'] = site_url ( "admin/ordersbyuser/$uid/" . ($p - 1) . "/$sort/$order/$from/$to/$priority/$stock_avail" );
			
			
			
			$data['high_priority'] = $priority;
			$data['stock_avail'] = $stock_avail;
			$data['pagetitle']="Orders by user : ".$suser;

			$data['to']=$to;
			$data['from']=$from;
			$data['sort']=$sort;
			$data['order']=$order;

			$this->load->view("admin",$data);
		}

		function ordersfordeal($itemid,$p=1,$sort="ordertime",$order="a")
		{
			$user=$this->auth();
			$deal=$this->db->query("select name from king_dealitems where id=?",$itemid)->row_array();
			$data['orders']=$this->adminmodel->getordersfordeal($itemid,$p,$sort,$order,0);
			$data['p']=$p;
			$data['sort']=$sort;
			$data['order']=$order;
			
			$data['len']=$this->adminmodel->getorderslenfordeal($itemid);
			$data['from']='';
			$data['to']='';
			$data['high_priority']=0;
			$data['stock_avail']=0;
			
			$data['page']="order_list";
			$data['url']=site_url("admin/ordersfordeal/$itemid");
			$data['nexturl']=site_url("admin/ordersfordeal/$itemid/".($p+1)."/$sort/$order");
			$data ['navurl'] = site_url ( "admin/ordersfordeal/$itemid/PAGINATE/$sort/$order" );
			$data['prevurl']=site_url("admin/ordersfordeal/$itemid/".($p-1)."/$sort/$order");
			$data['pagetitle']="Orders for ".ucfirst($deal['name']);
			$this->load->view("admin",$data);
		}

		function ordersforbrand($brandid,$p=1,$sort="ordertime",$order="d")
		{
			$user=$this->session->userdata("admin_user");
			if($user==false)
			redirect("admin");
			$vendorid=$user['brandid'];
			if($user['usertype']==1)
			$data['superadmin']=true;
			else
			$data['adminheader']=true;
			$brand=$this->adminmodel->getbrand($brandid);
			if($brand==false)
			redirect("admin/orders");
			$data['superadmin']=true;
			$data['brands']=$this->adminmodel->getallbrands();
			$data['orders']=$this->adminmodel->getorders($brandid,$p,$sort,$order,$vendorid);
			
			
			$data['p']=$p;
			$data['sort']=$sort;
			$data['order']=$order;
			
			$data['len']=$this->adminmodel->getorderslen($brandid);
			$data['from']='';
			$data['to']='';
			$data['high_priority']=0;
			$data['stock_avail']=0;
			
			$data['page']="admin_orders";
			$data['url']=site_url("admin/ordersforbrand/$brandid");
			$data['nexturl']=site_url("admin/ordersforbrand/$brandid/".($p+1)."/$sort/$order");
			$data ['navurl'] = site_url ( "admin/ordersforbrand/$brandid/PAGINATE/$sort/$order" );
			$data['prevurl']=site_url("admin/ordersforbrand/$brandid/".($p-1)."/$sort/$order");
			$data['pagetitle']="Orders for ".ucfirst($brand['name']);
			$this->load->view("admin",$data);
		}

		private function dashboardforbrandadmin()
		{
			$user=$this->session->userdata("admin_user");
			$brandid=$user['brandid'];
			$data['pendingorders']=$this->adminmodel->getpendingorderscount($brandid);
			$data['orders']=$this->adminmodel->getordersfordashboard($brandid,7);
			$data['dealslist']=$this->adminmodel->getdealslistfordashboard($brandid);
			$data['expdealslist']=$this->adminmodel->getexpdealslistfordashboard($brandid);
			$data['soldoutitems']=$this->adminmodel->getsoldoutitemsfordashboard($brandid);
			$data['adminheader']=true;
			$data['page']="admin_default";
			$this->load->view("admin",$data);
		}

		function viewactivity($actid)
		{
			$user=$this->session->userdata("admin_user");
			if($user==false || $user['usertype']!=1)
			redirect("admin");
			$data['page']="superadmin_activity";
			$data['activity']=$activity=$this->adminmodel->getactivity($actid);
			$data['brands']=$this->adminmodel->getallbrands();
			$data['superadmin']=true;
			$data['len']=1;
			$data['p']=1;
			$data['pagetitle']="Activity on ".date("g:ia d M",$activity[0]->time);
			$this->load->view("admin",$data);
		}

		function activityfordeal($dealid)
		{
			$user=$this->session->userdata("admin_user");
			if($user==false || $user['usertype']!=1)
			redirect("admin");
			$deal=$this->adminmodel->getdeal($dealid);
			if($deal==false)
			redirect("admin/activity");
			$data['page']="superadmin_activity";
			$data['activity']=$this->adminmodel->getactivityfordeal($dealid);
			$data['superadmin']=true;
			$data['pagetitle']="Activity for <span style='font-size:17px;'>{$deal[0]->tagline}</span>";
			$data['brands']=$this->adminmodel->getallbrands();
			$this->load->view("admin",$data);

		}

		function activitybybrand($brandid,$p=1)
		{
			$user=$this->session->userdata("admin_user");
			if($user==false || $user['usertype']!=1)
			redirect("admin");
			$brand=$this->adminmodel->getbrand($brandid);
			if($brand==false)
			redirect("admin/activity");
			$data['page']="superadmin_activity";
			$data['activity']=$this->adminmodel->getactivitybybrand($brandid,$p);
			$data['pagetitle']="Activity by {$brand['name']}";
			$data['prevurl']=site_url("admin/activitybybrand/$brandid/".($p-1));
			$data['nexturl']=site_url("admin/activitybybrand/$brandid/".($p+1));
			$data['p']=$p;
			$data['superadmin']=true;
			$data['len']=$this->adminmodel->getactivitylenbybrand($brandid);
			$data['brands']=$this->adminmodel->getallbrands();
			$this->load->view("admin",$data);
		}

		function activity($p=1)
		{
			$user=$this->auth(ADMINISTRATOR_ROLE);
			$data['page']="superadmin_activity";
			$data['brands']=$this->adminmodel->getallbrands();
			$data['activity']=$this->adminmodel->getactivitys($p);    //thats not a typo
			$data['p']=$p;
			$data['superadmin']=true;
			$data['prevurl']=site_url("admin/activity/".($p-1));
			$data['nexturl']=site_url("admin/activity/".($p+1));
			$data['len']=$this->adminmodel->getactivitylen();
			$this->load->view("admin",$data);
		}

		function pricereqs($stat="pending")
		{
			$user=$this->auth(true);
			$s=array("pending"=>0,"accepted"=>1,"denied"=>2,"completed"=>3);
			$data['counts']=$this->dbm->getpricereqcounts();
			$data['reqs']=$this->dbm->getpricereqs($s[$stat]);
			$data['page']="pricereqs";
			$data['pagetitle']=ucfirst($stat);
			$this->load->view("admin",$data);
		}

		function prchange($id,$sta)
		{
			$user=$this->auth(true);
			if($sta!="deny" && $sta!="accept")
			redirect("admin");
			$stam=array("accept"=>1,"deny"=>2);
			$st=$stam[$sta];
			$url=randomChars(32);
			$pr=$this->dbm->getpr($id);
			if(empty($pr))
			redirect("admin");
			if($this->input->post("nprice")!=false)
			$this->dbm->negotiateprice($id,$pr['reqprice'],$this->input->post("nprice"));
			$this->dbm->prchange($id,$st,$url);
			if($st==1)
			{
				$msg='Dear '.$pr['user'].',<br><br>
			We are pleased to inform that your quoted price for the Item ‘'.$pr['name'].'’ has been accepted.<br>';
				if($this->input->post("nprice")!=false)
				$msg.="<b>Negotiated price : Rs ".$this->input->post("nprice").'</b><br>';
				$msg.='Please click on the below link to purchase the item<br>
			<a href="'.site_url("pr/$url").'">'.site_url("pr/$url").'</a><br><br>
			Happy Shopping!<br><br>
			Team Viabazaar';
			}
			else
			$msg='Dear '.$pr['user'].',<br><br>
			We are sorry to inform that your quoted price for the Item ‘'.$pr['name'].'’ has been declined.<br> 
			However, if you would like to revise the rate, please click on <a href="'.site_url("deal/".$pr['itemurl']).'">'.site_url("deal/".$pr['itemurl']).'</a><br><br>
			Team Viabazaar';
			if($st==1)
			$ms="Price request accepted";
			else
			$ms="Price request denied";
			$this->session->set_flashdata("prchange",$ms);
			$this->load->library("email");
			$config['mailtype']="html";
			$this->email->initialize($config);
			$this->email->from("support@viabazaar.in","ViaBazaar");
			$this->email->to($pr['email']);
			$this->email->subject("Your price request for item '".$pr['name']."'");
			$this->email->message($msg);
			$this->email->send();
			redirect("admin/pricereqs");
		}

		function storelogostoserver($picloc, $imgname) {
			$imagemanip ['image_library'] = 'gd2';
			$imagemanip ['source_image'] = $picloc;
			/*		echo $picloc .'<br>';
			 echo $itembool.'<br>';
			 echo $imgname.'<br>';*/
			$thumbnail = $picloc;
			$imagemanip ['image_library'] = 'gd2';
			$imagemanip ['source_image'] = $picloc;
			$imagemanip ['new_image'] = "images/brands/" . $imgname . ".jpg";
			//			else
			//			$imagemanip ['new_image'] = APPPATH . "images/catlogo/" . $imgname . ".jpg";
			//echo $imagemanip ['new_image'];
			$imagemanip ['maintain_ratio'] = TRUE;
			$imagemanip ['width'] = 150;
			$imagemanip ['height'] =150;
			$imagemanip ['maintain_ratio'] = TRUE;
			$imagemanip ['master_dim'] = 'auto';
			$imagemanip ['quality'] = '100%';
			$this->load->library('image_lib');
			$this->image_lib->initialize ( $imagemanip );
			$this->image_lib->resize ();
			return $imgname;
		}
		function insnewcategory() {
			$user=$this->session->userdata ( "admin_user" );
			if ($user== false ||$user["usertype"]!=1) {
				redirect ( "admin" );
				die ();
			}
			if ($this->input->post ( 'catname', TRUE ) != '') {
				$catname = $this->input->post ( 'catname' );
				$catdesc=$this->input->post("catdesc");
				$maincat=$this->input->post("mainc");
				$cat=$this->adminmodel->getcategorybyname($catname);
				if($cat!=false)
				{
					$this->error="Category $catname already exists";
					$this->catEgories();
					return;
				}
				$res = $this->adminmodel->insertcategories ( $catname,$catdesc,$maincat );
				$this->info="New category $catname added";
				//			redirect("admin/categories");
			}
			else
			$this->error="Enter category name";
			$this->categories();
		}
		/*
		 function insnewcategory() {
		 $user=$this->session->userdata ( "admin_user" );
		 if ($user== false ||$user["usertype"]!=1) {
			redirect ( "admin" );
			die ();
			}
			$catname = $this->input->post ( 'catname' );
			$catdesc=$this->input->post('catdesc');
			$catdetails=$this->adminmodel->getspecificcategorydetailsforcatname($catname);
			//print_r($_POST);exit;
			if($catdetails==FALSE)
			{
			$imgname = $this->randomChars ( 15 );
			//			print_r($_FILES);exit;
			if (isset ( $_FILES ['catpic'] ) && $_FILES ['catpic'] ['error'] == 0) {
			$img = $this->storelogostoserver ( TRUE, $_FILES ['catpic'] ['tmp_name'], $imgname );
			$res = $this->adminmodel->insertcategories ( $catname ,$catdesc,$img);
			}
			if ($res != FALSE)
			$catlist = $this->adminmodel->getcategory ();
			$output = '';
			if ($catlist != FALSE) {
			foreach ( $catlist as $subcat ) {
			$output.= '<div><label><a href="'.site_url ( 'admin/categories/' . $subcat->id ).'"	style="margin-left: 5px; color: #000; font-size: 13px;" onclick="showdiv();">'.$subcat->name.'</a></label></div>';
			}
			echo '<script>parent.loadcategories(\''.$output.'\');</script>';
			} else
			echo "0";
			}else {
			$catlist = $this->adminmodel->getcategory ();
			if ($catlist != FALSE) {
			foreach ( $catlist as $subcat ) {
			$output.= '<div><label><a href="'.site_url ( 'admin/categories/' . $subcat->id ).'"	style="margin-left: 5px; color: #000; font-size: 13px;" onclick="showdiv();">'.$subcat->name.'</a></label></div>';
			}
			$output .='<div><span style="margin-top:10px;color:red;font-size:12px;font-family:arial;">Sorry there is Category exsisting with this name...!!</span></div>';
			echo '<script>parent.loadcategories(\''.$output.'\');</script>';
			}
			}

			}
			**/
		function insnewbrand() {
			$user=$this->session->userdata ( "admin_user" );
			if ($user== false ||$user["usertype"]!=1) {
				redirect ( "admin" );
				die ();
			}
			if ($this->input->post ( 'brandname', TRUE ) != '') {
				$brandname = $this->input->post ( 'brandname' );
				$branddesc=$this->input->post('branddesc');
				$res = $this->adminmodel->insertnewbrand ( $brandname ,$branddesc);
				if ($res != FALSE)
				$brands = $this->adminmodel->getbrandsfordashboard (1);
				$output = '';
				if ($brands != FALSE) {
					$output .='<label>Exsisting Brands</label><select style="margin-left: 15px;" name="brandname" id="brandname">
<option value="0" selected="selected">---Select---</option>';
					foreach ( $brands as $brand ) {
						$output.= '<option value="'.$brand->id.'">'.$brand->name.'</option>';
					}
					echo $output;
				} else
				echo "0";
			}
		}
		function insbrandundercat()
		{
			$user=$this->session->userdata ( "admin_user" );
			if ($user== false ||$user["usertype"]!=1) {
				redirect ( "admin" );
				die ();
			}
			if ($this->input->post ( 'brandid', TRUE ) != '') {
				$brandid = $this->input->post ( 'brandid' );
				$catid=$this->input->post('catid');
				$catname=$this->input->post('catname');
				$res = $this->adminmodel->insertbrandtocat ( $brandid ,$catid);
				if ($res != FALSE)
				$brands = $this->adminmodel->getbrandslistforspecificcategory ($catid);
				$output = '';
				if ($brands != FALSE) {
					$output .='<div class="header">Brands Under '.$catname.'</div>';
					foreach ( $brands as $brand ) {
						$output.= '<div><label><a href="'.site_url('admin/dealsforbrand/'.$brand->brandid).'" style="margin-left: 5px; color: #000; font-size: 13px;">'.$brand->brandname.'</a>
						<a class="deletebrandundercat" href="'.site_url('admin/deletebrandfromcat/'.$brand->brandid."/".$catid).'" style="margin-left: 5px; color: blue; font-size: 13px;">Delete</a></label></div>';				
					}
				} else{
					$output .='<div align="center" id="branderr"><span
	style="text-align: center; font-size: 11px; font-family: arial;color: red;">No
Brands Under this category please add!!!</span></div>';
				}
				echo $output;
			}
		}
		function updatecategory()
		{
			//		print_r($_POST);exit;
			$user=$this->session->userdata ( "admin_user" );
			if ($user== false)
			redirect ( "admin" );
			//		var_dump($this->input->post("catname"));die();
			if($this->input->post('catid')===false ||$this->input->post("description")===false)
			//			die("asdas");
			redirect("admin/categories");
			$catid=$this->input->post('catid');
			$catname=trim($this->input->post('catname'));
			if(strlen($catname)==0)
			{
				$this->error="Enter category name";
				$this->categories($catid);
				return;
			}
			$description=$this->input->post('description');
			//		$type=$this->input->post('cattype');
			//		$hidcatimg=$this->input->post('hidcatimg');
			//print_r($hidcatimg);exit;
			//		$imgname = $this->randomChars ( 15 );
			//		if (isset ( $_FILES ['catpic'] ) && $_FILES ['catpic'] ['error'] == 0) {
			//			$img = $this->storelogostoserver ( $_FILES ['catpic'] ['tmp_name'], $imgname );
			//			unlink ( realpath ( APPPATH ) . '/images/catlogo/' . $hidcatimg . '.jpg' );
			//			$res = $this->adminmodel->updatecategorywithlogo($type,$catname ,$description,$img,$catid);
			//			}
			//			else {
			$type=$this->input->post("mainc");
			$res=$this->adminmodel->updatecategory($catname,$description,$catid,$type);
			//			}
			$this->info="Category updated successfully";
			$this->categories($catid);
		}
		function updatebrand()
		{
			//print_r($_POST);exit;
			$user=$this->session->userdata ( "admin_user" );
			if ($user== false) {
				redirect ( "admin" );
				die ();
			}
			$brandid=$this->input->post('brandid');
			$brandname=$this->input->post('brandname');
			$description=$this->input->post('branddescription');
			$website=$this->input->post('brandwebsite');
			$email=$this->input->post('brandemail');
			if(isset ( $_FILES ['brandlogo'] ))
			{
				if (isset ( $_FILES ['brandlogo'] ) && $_FILES ['brandlogo'] ['error'] == 0) {
					//print_r($_FILES['brandlogo1']);exit;
					$imgname = $this->randomChars ( 15 );
					$img = $this->storelogostoserver ($_FILES ['brandlogo'] ['tmp_name'], $imgname );
					$res=$this->adminmodel->updatebrandwithlogo($brandname,$description,$img,$website,$email,$brandid);
				}
			}
			else {
				$res=$this->adminmodel->updatebrand($brandname,$description,$website,$email,$brandid);
			}
			
			@list($fy,$fm,$fd)=@explode("-",$_POST['fstartdate']);
			$fstart=mktime($_POST['fstarthrs'],0,0,$fm,$fd,$fy);
			@list($fy,$fm,$fd)=@explode("-",$_POST['fenddate']);
			$fend=mktime($_POST['fendhrs'],0,0,$fm,$fd,$fy);
			$this->db->query("update king_brands set featured_start=?,featured_end=? where id=?",array($fstart,$fend,$brandid));
			
			$this->info="Brand updated";
			$this->editbrand($brandid);
		}
		function loadeditcatpage($catid)
		{
			$user=$this->session->userdata ( "admin_user" );
			if ($user== false ||$user["usertype"]!=1) {
				redirect ( "admin" );
				die ();
			}
			$data['edit_cat']=TRUE;
			$catdetails=$this->adminmodel->getspecificcategorydetails($catid);
			//print_r($branddetails);exit;
			$data['specificcatdetails']=$catdetails[0];
			$data['page']='superadmin_editcatandbrand';
			$data['superadmin']=TRUE;
			$this->load->view('admin',$data);
		}
		function deletebrandfromcat($brandid,$catid)
		{
			$user=$this->session->userdata ( "admin_user" );
			if ($user== false ||$user["usertype"]!=1) {
				redirect ( "admin" );
				die ();
			}
			$this->adminmodel->deletebrandundercat($brandid);
			redirect('admin/categories/'.$catid);
		}
		function editbrandadmin(){
			//print_r($_POST);exit;
			$userid=$this->input->post('userid');
			$brandadminnamae=$this->input->post('brandadmin');
			$changeuserid=md5(strtolower($brandadminnamae));
			$this->adminmodel->updatebrandadmin($changeuserid,$brandadminnamae,$userid);
			redirect('admin/brands');
		}
		function editbranduser(){
			//print_r($_POST);exit;
			$userid=$this->input->post('userid');
			$brandadminnamae=$this->input->post('branduser');
			$changeuserid=md5(strtolower($brandadminnamae));
			$this->adminmodel->updatebrandadmin($changeuserid,$brandadminnamae,$userid);
			redirect('admin/viewUser');
		}
		function dssssealsforbrand($brandid,$p=1)
		{
			$user=$this->session->userdata ( "admin_user" );

			if ($user== false ||$user["usertype"]!=1) {
				redirect ( "admin" );
				die ();
			}
			$branddetails=$this->adminmodel->getspecificbranddetails($brandid);
			//print_r($branddetails);exit;
			$data['specificbranddetails']=$branddetails[0];
			$deals = $this->adminmodel->getdealslist ( $brandid,$p );
			//print_r($deals);exit;
			$dealsarray = array ();
			$hoteldeals = array ();
			$roomsdeals = array ();
			if ($deals != FALSE) {
				foreach ( $deals as $deal ) {
					$catid = $deal->catid;
					$dealid = $deal->dealid;
					if ($catid == 4) {
						$hoteldeals [$dealid] = $this->adminmodel->gethoteldeals ( $dealid );
						$this->db->close ();
						foreach ( $hoteldeals [$dealid] as $room ) {
							$dealidforroom = $room->dealid;
							$roomdetails [$dealidforroom] = $this->adminmodel->getroomdealsforhotel ( $dealidforroom );
							$this->db->close ();
						}
							
						$data ['hoteldeals'] = $hoteldeals;
						$data ['roomdetails'] = $roomdetails;
					}
					$dealsarray [$dealid] = $this->adminmodel->getdealitems ( $dealid );
					//print_r($dealsarray);exit;
					$this->db->close ();
				}
			}
			$data ['deals'] = $deals;
			$data ['dealitems'] = $dealsarray;
			$data['p']=$p;

			$data['superadmin']=TRUE;
			$data['page']='superadmin_productsunderbrand';
			$this->load->view('admin',$data);
		}

		function cache_control()
		{
			$user=$this->auth(DEAL_MANAGER_ROLE);
			if($_POST)
			{
				$this->load->library("pettakam",array("repo"=>"cache","ext"=>"pkm_snp"));
				if($this->input->post("menu"))
				$this->pettakam->clear("menu");
				else if($this->input->post("deals"))
				$this->pettakam->clear_group("deals");
				else if($this->input->post("clearall"))
				$this->pettakam->clear_all();
				$data['msg']="Cache ".$this->input->post("msg")." cleared";
			}
			$data['page']="cache-control";
			$this->load->view("admin",$data);
		}

		function franchisee()
		{
			$user=$this->auth(true);
			$data['frans']=$this->db->query("select * from king_franchisee")->result_array();
			$data['page']="franchisee";
			$this->load->view("admin",$data);
		}

		function addfran()
		{
			$user=$this->auth(true);
			if($_POST)
			{
				$vars=array("name","uname","password","email","address","city","balance","number","dtype","mobile");
				foreach($vars as $var)
				$$var=$this->input->post($var);
				$username=$uname;
				if($this->db->query("select 1 from king_franchisee where username=?",$uname)->num_rows()!=0)
				$data['error']=$err="Username already exists";
				if(!isset($err))
				{
					$this->db->query("insert into king_users(name,email,mobile,password,balance,inviteid,address,city,createdon) values(?,?,?,?,?,?,?,?,?)",array($name,$email,$mobile,md5(randomChars(10)),$balance,randomChars(6),$address,$city,time()));
					$uid=$this->db->insert_id();
					$this->db->query("insert into king_franchisee(username,password,name,email,address,city,balance,userid,time) values(?,?,?,?,?,?,?,?,?)",array($username,md5($password),$name,$email,$address,$city,$balance,$uid,time()));
					$fid=$this->db->insert_id();
					$this->db->query("insert into king_franch_transactions(franid,name,deposit,balance,time) values(?,?,?,?,?)",array($fid,"Initial balance",$balance,$balance,time()));
					$this->dbm->audit(0,"Initial account balance for Franchisee ($name)",$balance,"Mode : $dtype ID no : $number",$user['username']." (sales)");
					redirect("admin/franchisee");
				}
			}
			$data['page']="addfran";
			$this->load->view("admin",$data);
		}

		function audit($sd="",$ed="")
		{
			$user=$this->auth(true);
			$sdate=$edate=0;
			if($ed!="")
			{
				$e=explode("-",$ed);
				if(count($e)==3)
				$edate=mktime(23,59,59,$e[1],$e[0],$e[2]);
			}
			if($sd!="")
			{
				$e=explode("-",$sd);
				if(count($e)==3)
				$sdate=mktime(0,0,0,$e[1],$e[0],$e[2]);
			}
			$sql="select * from king_audit order by id desc";
			if($sdate!=0 && $edate!=0)
			$sql="select * from king_audit where time between '$sdate' and '$edate' order by id desc";
			if($sdate==0 && $edate!=0)
			$sql="select * from king_audit where time < $edate order by id desc";
			if($edate==0 && $sdate!=0)
			$sql="select * from king_audit where time > $sdate order by id desc";
				
			$data['sdate']=$sdate;
			$data['edate']=$edate;
			$data['audits']=$audits=$this->db->query($sql)->result_array();
			$income=$expense=0;
			foreach($audits as $audit)
			{
				$income+=$audit['credit'];
				$expense+=$audit['debit'];
			}
			$data['income']=$income;
			$data['expense']=$expense;
			$data['page']="audit";
			$this->load->view("admin",$data);
		}

		function franaddbal($id)
		{
			$user=$this->auth(true);
			if($_POST)
			{
				$add=$this->input->post("addbal");
				$dtype=$this->input->post("dtype");
				$number=$this->input->post("number");
				$m=$this->db->query("select name,userid,balance from king_franchisee where id=?",$id);
				if($m->num_rows()==0)
				show_404();
				$r=$m->row_array();
				$bal=$r['balance']+$add;
				$uid=$r['userid'];
				$name=$r['name'];
				$this->db->query("update king_franchisee set balance=balance+? where id=?",array($add,$id));
				$this->db->query("update king_users set balance=? where userid=?",array($bal,$uid));
				$this->db->query("insert into king_franch_transactions(name,deposit,balance,time,franid) values(?,?,?,?,?)",array("balance topup",$add,$bal,time(),$id));
				$this->dbm->audit(0,"Balance Topup for franchisee($name)",$add,"Mode : $dtype ID no : $number",$user['username']." (sales)");
				redirect("admin/franchisee");
			}
			$data['page']="franaddbal";
			$this->load->view("admin",$data);
		}

		function editfran($id)
		{
			$user=$this->auth(true);
			if($_POST)
			{
				$vars=array("name","email","address","city" ,"mobile");
				foreach($vars as $var)
				$$var=$this->input->post($var);
				$this->db->query("update king_franchisee set name=?, email=?, address=?, city=? where id=? limit 1",array($name,$email,$address,$city,$id));
				$uid=$this->db->query("select userid from king_franchisee where id=?",$id)->row()->userid;
				$this->db->query("update king_users set email=?,mobile=? where userid=? limit 1",array($email,$mobile,$uid));
				redirect("admin/franchisee");
			}
			$data['fran']=$this->db->query("select * from king_franchisee where id=?",$id)->row_array();
			$data['page']="addfran";
			$this->load->view("admin",$data);
		}

		function crsmall()
		{
			$this->load->library("thumbnail");
			$deals=$this->db->query("select pic from king_dealitems")->result_array();
			foreach($deals as $deal)
			{
				echo $deal['pic']."<br>";
				$this->thumbnail->create(array("source"=>"images/items/big/".$deal['pic'].".jpg","dest"=>"images/items/small/".$deal['pic'].".jpg","width"=>160));
			}
			foreach($this->db->query("select id from king_resources where type=0")->result_array() as $r)
			{
				echo $r['id']."<br>";
				$this->thumbnail->create(array("source"=>"images/items/big/".$r['id'].".jpg","dest"=>"images/items/small/".$r['id'].".jpg","width"=>160));
			}
		}

		function newann()
		{
			$data['page']='newann';
			$this->load->view("admin",$data);
		}

		function disenann($id,$stat)
		{
			$stat=!$stat;
			$this->db->query("update king_announcements set enable=? where id=? limit 1",array($stat,$id));
			redirect("admin/announcements");
		}

		function editann($id)
		{
			$a=$this->db->query("select * from king_announcements where id=?",$id)->row_array();
			$data['a']=$a;
			$data['page']="newann";
			$this->load->view("admin",$data);
		}

		function announcements()
		{
			$user=$this->auth(DEAL_MANAGER_ROLE);
			if($_POST)
			{
				foreach(array("text","url","id") as $i)
				$$i=$this->input->post($i);
				if($id)
				$this->db->query("update king_announcements set text=?, url=? where id=?",array($text,$url,$id));
				else
				$this->db->query("insert into king_announcements(text,url) values(?,?)",array($text,$url));
			}
			$data['aas']=$this->db->query("select * from king_announcements order by id desc")->result_array();
			$data['page']="announcements";
			$this->load->view("admin",$data);
		}
		
		function campaign($action = 'show', $campaign_no = '') {
			$user = $this->auth ();
		
			if ($this->input->post ( 'campaign_no' )) {
		
				$ins_data = array ();
				$ins_data ['campaign_no'] = $campaign_no = $this->input->post ( 'campaign_no' );
				$ins_data ['campaign_type'] = $this->input->post ( 'campaign_type' );
				$ins_data ['campaign_cycle'] = $this->input->post ( 'campaign_cycle' );
				$ins_data ['title'] = $this->input->post ( 'campaign_title' );
				if ($this->input->post ( 'default_banner' )) {
					$ins_data ['banner_image'] = 'default_banner.png';
				} else {
		
					$rand_chrs = chr ( rand ( 65, 90 ) ) . chr ( rand ( 65, 90 ) ) . chr ( rand ( 65, 90 ) ) . chr ( rand ( 65, 90 ) );
		
					$allowed_filetypes = array ('XSDFFFF', 'jpg', 'png' );
		
					$im_nameparts = explode ( '.', $_FILES ["campaign_other_img"] ["name"] );
		
					$img_extn = end ( $im_nameparts );
		
					$img_filename = substr ( $_FILES ["campaign_other_img"] ["name"], 0, strrpos ( $_FILES ["campaign_other_img"] ["name"], $img_extn ) - 1 );
		
					if ($_FILES ["campaign_other_img"] ["name"]) {
		
						$img_name = $im_nameparts [count ( $im_nameparts ) - 1];
		
						$c_filename = $img_filename . $rand_chrs . '.' . $img_extn;
						move_uploaded_file ( $_FILES ["campaign_other_img"] ["tmp_name"], NEWSLETTER_BANNERS . '/' . $c_filename );
						$ins_data ['banner_image'] = $c_filename;
					}
				}
		
				$ins_data ['banner_link'] = $this->input->post ( 'campaign_banner_link' );
				$ins_data ['template_id'] = $this->input->post ( 'template_id' );
				$ins_data ['is_active'] = $this->input->post ( 'is_active' );
		
				if ($action == 'create') {
					$ins_data ['created_on'] = date ( 'Y-m-d H:i:s' );
					$this->db->insert ( 'king_campaigns', $ins_data );
				} else {
					$ins_data ['modified_on'] = date ( 'Y-m-d H:i:s' );
					$this->db->where ( 'campaign_no', $campaign_no );
					$this->db->update ( 'king_campaigns', $ins_data );
				}
				$camp_deal_list = $this->input->post ( 'camp_deal_list' );
		
				$this->db->query ( "delete from king_campaigns_deals where campaign_no = ? ", $campaign_no );
		
				foreach ( $camp_deal_list ['id'] as $i => $camp_dealid ) {
		
					if (! $camp_dealid) {
						continue;
					}
		
					$ins_campdata = array ();
					$ins_campdata ['campaign_no'] = $campaign_no;
					$ins_campdata ['deal_id'] = $camp_deal_list ['id'] [$i];
					$ins_campdata ['relative_link'] = $camp_deal_list ['relative_link'] [$i];
					$ins_campdata ['order'] = $camp_deal_list ['order'] [$i];
					$ins_campdata ['is_active'] = $camp_deal_list ['status'] [$i];
					$ins_campdata ['created_on'] = date ( 'Y-m-d H:i:s' );
					$this->db->insert ( 'king_campaigns_deals', $ins_campdata );
		
				}
		
				if ($action == 'create') {
					$this->session->set_flashdata ( 'campaign_notify', 'New Campaign created  successfully' );
				} else if ($action == 'edit') {
					$this->session->set_flashdata ( 'campaign_notify', 'Campaign Details updated successfully' );
				}
		
				redirect ( 'admin/campaign', 'refresh' );
				exit ();
			}
		
			if ($action == 'edit' || $action == 'view') {
				$data ['campaign_det'] = $this->db->query ( "select * from king_campaigns  where campaign_no = ? ", $campaign_no )->row_array ();
				$data ['campaign_deal_list'] = $this->db->query ( "select * from king_campaigns_deals  where campaign_no = ? order by `order` asc ", $campaign_no )->result_array ();
		
			}
		
			$data ['action'] = $action;
			$data ['page'] = "campaign";
			$this->load->view ( "admin", $data );
		}
		
		function cr_campaign_tmpl() {
			$tmpl_name = $this->input->post ( 'tmpl_name' );
			$tmpl_filename = $this->input->post ( 'tmpl_filename' );
			$tmpl_isactive = $this->input->post ( 'tmpl_is_active' );
		
			$ins_data = array ();
			$ins_data ['template_name'] = $tmpl_name;
			$ins_data ['template_filename'] = $tmpl_filename;
			$ins_data ['is_active'] = $tmpl_isactive;
			$ins_data ['created_on'] = date ( 'Y-m-d H:i:s' );
			$this->db->insert ( 'king_campaign_templates', $ins_data );
		
			$this->db->where ( 'is_active', 1 );
			$this->db->order_by ( "id", "asc" );
			$resp = $this->db->get ( 'king_campaign_templates' )->result_array ();
		
			echo json_encode ( array ('tmpl_list' => $resp ) );
		}


		function getdealdetbyurl(){
			$url = $this->input->post('url');
			$deal_det = $this->db->query("select dealid from king_dealitems where  url = ? ",$url)->result_array();
			echo json_encode(array('status'=>1,'deal_det'=>$deal_det[0]));
		}



		function changeqty($oid)
		{
			$this->auth();
			$order=$this->db->query("select * from king_orders where id=?",$oid)->row_array();
			$qty=$this->input->post("qty");
			$transid=$order['transid'];
			if(!$qty)
			redirect("admin/trans/$transid");
			
			$norder = $order;
			
			$norder['userid']=$order['userid'];
			//$norder['itemid']=$order['itemid'];
			$norder['quantity']=$order['quantity']-$qty;
			//$norder ['bill_address'] = $norder ['ship_address'] = "{$norder['quantity']} QTY CANCEL AGAINST ORDER : {$oid}";

			//$norder ['bill_address'] = $order['bill_address'];
			//$norder ['ship_address'] = $order['ship_address'];

			$norder['id']=random_string("numeric",10);
			//$norder['transid']=$transid;
			
			
			
			
			/* $inp=array();
			foreach(array("id","userid","itemid","quantity","bill_address","ship_address","transid","brandid","vendorid","bill_person","ship_person","bill_email","ship_email","bill_city","ship_city","bill_pincode","ship_pincode","bill_phone","ship_phone","bill_state","ship_state","paid","mode","shipped","buyer_options") as $i)
			$inp[]=$norder[$i];
			$inp[]=time();
			$inp[]=time();

			$inp [] = $order['i_orgprice'];
			$inp [] = $order['i_price'];
			$inp [] = $order['i_discount'];
			$inp [] = $order['i_tax'];
			$inp [] = $order['i_nlc'];
			$inp [] = $order['i_phc'];
			$inp [] = $order['i_coup_discount'];
			$inp [] = $order['i_discount_applied_on']; */

			unset($norder['sno']);
			$norder['time'] = time();
			$norder['actiontime'] = time();
			$norder['status'] = 3;
			$norder['admin_order_status'] = 6;
			
			
				

			$this->db->insert('king_orders',$norder);

			//$this->db->query ( "insert into king_orders(id,userid,itemid,quantity,bill_address,ship_address,transid,brandid,vendorid,bill_person,ship_person,bill_email,ship_email,bill_city,ship_city,bill_pincode,ship_pincode,bill_phone,ship_phone,bill_state,ship_state,paid,mode,shipped,buyer_options,time,actiontime,status,admin_order_status,i_orgprice,i_price,i_discount,i_tax,i_nlc,i_phc,i_coup_discount,i_discount_applied_on) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,3,6,?,?,?,?,?,?,?,?)", $inp );
			
			
			
			
			$inv=$this->db->query("select * from king_invoice where order_id=?",$oid)->row_array();
			if(!empty($inv))
			{
				$inp=array($inv['invoice_no'],$transid,$norder['id'],$inv['nlc'],$inv['phc'],$inv['tax'],$inv['service_tax']);
				$this->db->query("insert into king_invoice(invoice_no,transid,order_id,nlc,phc,tax,service_tax) values(?,?,?,?,?,?,?)",$inp);
			}
			$this->db->query("update king_orders set quantity=?,actiontime=? where id=? limit 1",array($qty,time(),$oid));
			redirect("admin/trans/$transid");
		}
		
		function favdeal($dealid,$fav)
		{
			$this->db->query("update king_dealitems set favs=? where dealid=? limit 1",array($fav,$dealid));
			redirect("admin/deal/$dealid");
		}

		function stock($item_name='')
		{
			$this->auth();
			if($_POST)
			{
				foreach(array("qty","itemid","remarks","type","date","vendor","amount","reference") as $i)
				$$i=$this->input->post("$i");
				$stockids=array();
				foreach($qty as $i=>$q)
				{
					if(empty($itemid[$i]) || empty($q))
						continue;
					$s=$this->db->query("select * from king_stock where itemid=?",$itemid[$i])->row_array();
					if(empty($s))
					{
						$this->db->query("insert into king_stock(itemid) values(?)",$itemid[$i]);
						$s=$this->db->query("select * from king_stock where itemid=?",$itemid[$i])->row_array();
					}
					if($type=="in")
					{
						$s['available']+=$q;
						$s['ins']+=$q;
					}
					else
					{
						$s['available']-=$q;
						$s['outs']+=$q;
					}
					$this->db->query("update king_stock set available=?, ins=?, outs=? where itemid=? limit 1",array($s['available'],$s['ins'],$s['outs'],$itemid[$i]));
					$stockids[]=$s['id'];
				}
				$inp=array(
				implode(",",$stockids),
				$type=="in"?0:1,
				$remarks,$reference,$date,$vendor,$amount
				);
				$this->db->query("insert into king_stock_activity(stockids,type,remarks,reference_no,purchase_date,vendor,amount)
																values(?,?,?,?,?,?,?)",$inp);
				
				$this->session->set_flashdata('notify_msg',"Stock Details updated");
				
				redirect("admin/stock","refresh");
				exit;
				
			}
			$data['page']="stock";
			$this->load->view("admin",$data);
		}

		function jx_searchitem()
		{
			$q=$this->input->post("p");
			$items=$this->db->query("select dealid,orgprice as mrp,name,id from king_dealitems where name like ? limit 20","%$q%")->result_array();
			foreach($items as $i)
			{
				$s=$this->db->query("select available from king_stock where itemid=?",$i['id'])->row_array();
				if(!empty($s))
				$s=$s['available'];
				else
				$s=0;
				echo "<a href='javascript:void(0)' onclick='selitem({$i['id']},{$i['mrp']},\"".htmlspecialchars($i['name'],ENT_QUOTES)."\",$s,{$i['dealid']})'>{$i['name']}</a>";
			}
		}
		
		
		function jx_suggest_items()
		{
			$q=$this->input->get("query");
		
		
			$item_list = array();
			$data_list = array();
			$items=$this->db->query("select dealid,orgprice as mrp,name,id from king_dealitems where name like ? limit 10","%$q%")->result_array();
			foreach($items as $i)
			{
		
		
				$s=$this->db->query("select available from king_stock where itemid=?",$i['id'])->row_array();
				if(!empty($s))
					$s=$s['available'];
				else
					$s=0;
				//echo "<a href='javascript:void(0)' onclick='selitem({$i['id']},{$i['mrp']},\"".htmlspecialchars($i['name'],ENT_QUOTES)."\",$s,{$i['dealid']})'>{$i['name']}</a>";
		
				$i['avail_qty'] = $s;
				$item_list[$i['id']] = $i['name'];
				$data_list[$i['id']] = $i;
		
			}
		
		
			$json_data = array();
			$json_data ['query'] = $q;
			$json_data ['suggestions'] = array_values($item_list);
			$json_data ['data'] = array_values($data_list);
		
		
			echo json_encode($json_data);
		
		}

		function trans_note($transid)
		{
			if($_POST)
			{
				foreach(array("priority","pnote","note") as $i)
				$$i=$this->input->post("$i");
				if($priority=="yes")
				$priority=1;
				else
				$priority=0;
				$pnote=str_replace("'", "", str_replace('"',"",$pnote));
				$note=str_replace("'", "", str_replace('"',"",$note));
				$this->db->query("update king_transactions set priority=?,priority_note=?,note=? where transid=? limit 1",array($priority,$pnote,$note,$transid));
				$this->db->query("update king_orders set priority=? where transid=?",array($priority,$transid));
				redirect("admin/trans/$transid");
			}
			$t=$this->db->query("select priority_note,priority,note from king_transactions where transid=?",$transid)->row_array();
			$data['pnote']=$t['priority_note'];
			$data['note']=$t['note'];
			$data['priority']=$t['priority'];
			$data['for']="transaction $transid";
			$data['page']="order_note";
			$this->load->view("admin",$data);
		}

		function order_note($oid)
		{
			if($_POST)
			{
				foreach(array("priority","pnote","note") as $i)
				$$i=$this->input->post("$i");
				if($priority=="yes")
				$priority=1;
				else
				$priority=0;
				$pnote=str_replace("'", "", str_replace('"',"",$pnote));
				$note=str_replace("'", "", str_replace('"',"",$note));
				$this->db->query("update king_orders set priority=?,priority_note=?,note=? where id=? limit 1",array($priority,$pnote,$note,$oid));
				$transid=$this->db->query("select transid from king_orders where id=?",$oid)->row()->transid;
				redirect("admin/trans/$transid");
			}
			$t=$this->db->query("select priority_note,priority,note from king_orders where id=?",$oid)->row_array();
			$data['pnote']=$t['priority_note'];
			$data['note']=$t['note'];
			$data['priority']=$t['priority'];
			$data['for']="order $oid";
			$data['page']="order_note";
			$this->load->view("admin",$data);
		}

		function offline() {
			
			$a_user = $this->auth (CALLCENTER_ROLE|STOCK_INTAKE_ROLE);
			if ($_POST) {
				$this->load->library("cart");	
				$this->load->model ( "viakingmodel" );
				
				if(empty($_POST['email']))
					show_error("no email entered");
				
				foreach ( array ("person", "address", "landmark", "city", "state", "pincode", "mobile", "telephone", "email" ) as $p )
				$_POST ['bill_' . $p] = $_POST [$p];
					
				$user = $this->viakingmodel->getuserbyemail ( $this->input->post ( "bill_email" ) );
				if (! $user) {
					$p_email = $this->input->post ( "email" );
					$this->viakingmodel->newuser ( $p_email, substr ( $p_email, 0, strpos ( $p_email, "@" ) ), randomChars ( 8 ), $this->input->post ( "bill_mobile" ), rand ( 2030, 424321 ), 0, "", 0 );
					$this->viakingmodel->sendpassword ( $p_email );
					$user = $this->viakingmodel->getuserbyemail ( $p_email );
				}
				$ouser=$this->session->userdata("user");
				$this->session->set_userdata("user",$user);
				$uid = 0;
				if (! empty ( $user ))
				$uid = $user ['userid'];
					
				$itemids = $this->input->post ( "itemid" );
				$qty = $this->input->post ( "qty" );
					
				$items = array ();
				$mrp_total = $total = 0;
					
				foreach ( $itemids as $i => $itemid ) {
					if (empty ( $itemid ))
					continue;
					$items [] = array ("id" => $itemid, "qty" => $qty [$i], "itemid" => $itemid );
					$prod = $this->db->query ( "select i.price,d.catid,d.brandid,i.orgprice as mrp,i.shipsto from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?", $itemid )->row_array ();
					$total += $prod ['price'] * $qty [$i];
					$mrp_total += $prod ['mrp'] * $qty [$i];
				}
				$amount = $ttotal = $total;
					
				$coupon = $couponid = trim ( $this->input->post ( "coupon" ) );
				if (! empty ( $coupon )) {
					$c = $this->db->query ( "select * from king_coupons where code=? and status=0", array ($coupon ) )->row_array ();
					if (empty ( $c ))
					$coupon = array ();
					else
					$coupon = array ("mode" => $c ['mode'], "brandid" => $c ['brandid'], "catid" => $c ['catid'], "type" => $c ['type'], "min" => $c ['min'], "value" => $c ['value'], "expires" => $c ['expires'], "code" => $c ['code'] );
					if (! empty ( $coupon )) {
						$app = 0;
						if ($coupon ['expires'] < time ())
						$msg = "Invalid coupon : coupon expired!";
						elseif ($coupon ['min'] > $total)
						$msg = "Coupon is valid only for a minimum amount of Rs {$coupon['min']}";
						else {
							$msg = "Coupon applied";
							$app = 1;
						}
					} else {
						$coupon = false;
						$msg = "Invalid Coupon $couponid";
					}
				} else
				$coupon = false;
					
				if ($coupon) {
					$cats = $brands = array ();
					$mrp_total = 0;
					$prods = array ();
					foreach ( $items as $i => $item ) {
						$prod = $this->db->query ( "select i.price,d.catid,d.brandid,i.orgprice as mrp,i.shipsto from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?", $item ['id'] )->row_array ();
						$shipsto = $prod ['shipsto'];
						$prod ['qty'] = $item ['qty'];
						$items [$i] ['price'] = $prod ['price'];
						$prod ['price'] = $prod ['price'];
						$mrp_total += $prod ['mrp'] * $item ['qty'];
						$prods [] = $prod;
					}
					$c_total = $total;
					if ($coupon ['mode'] == 1)
					$c_total = $mrp_total;

					if ($coupon ['catid'] != "") {
						$cats = explode ( ",", $coupon ['catid'] );
						$c_total = 0;
						foreach ( $prods as $p )
						if (in_array ( $p ['catid'], $cats ))
						$c_total += ($coupon ['mode'] == 1 ? ($p ['mrp'] * $p ['qty']) : ($p ['price'] * $p ['qty']));
					}

					if ($coupon ['brandid'] != "") {
						$brands = explode ( ",", $coupon ['brandid'] );
						$c_total = 0;
						foreach ( $prods as $p )
						if (in_array ( $p ['brandid'], $brands ))
						$c_total += ($coupon ['mode'] == 1 ? ($p ['mrp'] * $p ['qty']) : ($p ['price'] * $p ['qty']));
					}

					if ($coupon ['type'] == 0) {
						$cd_total = $c_total - $coupon ['value'];
						if ($cd_total < 0)
						$cd_total = 0;
					} elseif ($coupon ['type'] == 1)
					$cd_total = $c_total - floor ( $c_total * $coupon ['value'] / 100 );

					if ($coupon ['mode'] == 1) {
						$total = $mrp_total + $cd_total - $c_total;
						foreach ( $prods as $p )
						if (! empty ( $brands ) || ! empty ( $cats ))
						if (! in_array ( $p ['brandid'], $brands ) && ! in_array ( $p ['catid'], $cats ))
						$total -= ($p ['mrp'] - $p ['price']) * $p ['qty'];
					} else
					$total = $ttotal + $cd_total - $c_total;

					if ($total > $ttotal)
					$total = $ttotal;
						
				}
				if ($total < 0)
				$total = 0;
				$amount = $total;
					
					
					
					
				$ship = $cod = 0;
					
				$mode = 1;
				$cod = $this->input->post ( "cod" );
				$amount += $cod;
					
					
					
					
				$snp = "SNP";
				if ($mode == 1)
				$snp = "SNC";
					
				$transid = $snp . random_string ( "alpha", 3 ) . random_string ( "nozero", 5 );
				$transid = strtoupper ( $transid );
				$sql = "insert into king_transactions(transid,amount,init,mode,status,cod,ship,offline) values(?,?,?,?,0,?,?,1)";
				$this->db->query ( $sql, array ($transid, $amount, time (), $mode, $cod, $ship ) );
					
				$coupon_price_dets = array ();
				$total_itm_applicable_for_coupon = 0;
				$total_offer_discount = 0;
					
				$coup_det = $coupon;
					
				$by_percent = 0;
				if ($coup_det ['type']) {
					$by_percent = 1;
				}
					
				//echo "Coupon Details :: ",print_r($coup_det),'<br />';
				foreach ( $items as $item ) {
					$is_allowed = 0;
					$item_det = $this->db->query ( "select d.brandid,i.orgprice,i.price from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?", $item ['id'] )->result_array ();

					$item_det = $item_det [0];
					$item_brandid = $item_det ['brandid'];

					/*if($coup_det['itemid'] == $item['id']){
					 $is_allowed  = 1;
					 }*/

					if ($coup_det ['brandid'] != '') {
						if ($coup_det ['brandid'] == $item_brandid)
						$is_allowed = 1;
					} else {
						$is_allowed = 1;
					}

					if ($is_allowed) {
						$coupon_price_dets [$item ['id']] = array ();
						$coupon_price_dets [$item ['id']] ['mrp'] = $item_det ['orgprice'];
						$coupon_price_dets [$item ['id']] ['offerprice'] = $item_det ['price'];
						$coupon_price_dets [$item ['id']] ['qty'] = $item ['qty'];
							
						$total_offer_discount += ($item_det ['orgprice'] - $item_det ['price']) * $item ['qty'];
							
						if ($coup_det ['mode']) {
							$total_itm_applicable_for_coupon += $item_det ['orgprice'] * $item ['qty'];
						} else {
							$total_itm_applicable_for_coupon += $item_det ['price'] * $item ['qty'];
						}

					} else {
						$coupon_price_dets [$item ['id']] = 0;
					}
				}
					
				$by_mrp = 0;
				if ($coup_det ['mode']) {
					$by_mrp = 1;
				}
					
				if ($by_percent) {
					$c_value = floor ( $total_itm_applicable_for_coupon * $coup_det ['value'] / 100 );
				} else {
					$c_value = $coup_det ['value'];
				}
					
				/*echo "Total Amount :: ".$total_itm_applicable_for_coupon.'<br />';
				 print_r($coupon_price_dets);
				 echo '<br>';
				 echo 'Coupon Value ::'.$c_value;
				 echo '<br>';
				 echo 'Total Offer Discount ::'.$total_offer_discount;*/
				$tt = 0;
				$c_disc_t = 0;
					
				if (($total_offer_discount < $c_value && $by_mrp == 1) || ($by_mrp == 0)) {
					foreach ( $coupon_price_dets as $c_item_id => $coup_pd ) {
						if (is_array ( $coup_pd )) {
							if ($coupon_price_dets [$c_item_id]) {
								if ($by_mrp) {
									$t_amount = $coupon_price_dets [$c_item_id] ['mrp'];
								} else {
									$t_amount = $coupon_price_dets [$c_item_id] ['offerprice'];
								}
									
								$coupon_price_dets [$c_item_id] ['discount_applied_amount'] = $t_amount;
									
								// $amount =  $amount*$coupon_price_dets[$c_item_id]['qty'];
									

								if ($by_mrp) {
									$total_applicable_discount = ($c_value - $total_offer_discount);
									$tt += $c_disc = $total_applicable_discount * $t_amount / $total_itm_applicable_for_coupon;
								} else {
									$tt += $c_disc = $c_value * $t_amount / $total_itm_applicable_for_coupon;
								}
									
								$c_disc_t += ($c_disc + ($coupon_price_dets [$c_item_id] ['mrp'] - $coupon_price_dets [$c_item_id] ['offerprice'])) * $coupon_price_dets [$c_item_id] ['qty'];
								$coupon_price_dets [$c_item_id] ['coup_discount'] = $c_disc;
							}
						}
					}
				}
					
				foreach ( $items as $item ) {
					$buyer_options = array ();
					$this->db->query ( "insert into king_m_buyprocess(itemid,quantity,refund,status,started_by,started_on) values(?,?,0,0,{$user['userid']},?)", array ($item ['itemid'], $item ['qty'], time () ) );
					$bpid = $this->db->insert_id ();
					$this->db->query ( "insert into king_buyprocess(bpid,quantity,userid,isrefund,status) values(?,?,?,0,0)", array ($bpid, $item ['qty'], $user ['userid'] ) );
					$bpuid = $this->db->insert_id ();

					$itemid = $item ['itemid'];

					$this->db->query ( "update king_buyprocess set userid=? where id=? limit 1", array ($user ['userid'], $bpuid ) );

					

					/*  $brandid=$this->db->query("select d.brandid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?",$itemid)->row()->brandid;
					 $vendorid=$this->db->query("select d.vendorid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?",$itemid)->row()->vendorid;
					 	
					 $orderid=random_string("numeric",10);
					 	
					 $sql="insert into king_tmp_orders(id,userid,itemid,brandid,vendorid,bpid,bill_person,bill_address,bill_landmark,bill_city,bill_state,bill_pincode,bill_phone,bill_telephone,bill_email,ship_person,ship_address,ship_landmark,ship_city,ship_state,ship_pincode,ship_phone,ship_telephone,ship_email,quantity,amount,time,buyer_options,transid)" .
					 " values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
					 $inp=array($orderid,$uid,$itemid,$brandid,$vendorid,$bpid);
					 $params=array("bill_person","bill_address","bill_landmark","bill_city","bill_state","bill_pincode","bill_mobile","bill_telephone","bill_email","person","address","landmark","city","state","pincode","mobile","telephone","email");
					 foreach($params as $p)
					 {
						$$p=$_POST[$p];
						$inp[]=$_POST[$p];
						}
						$inp[]=$item['qty'];
						$inp[]=$price;
						$inp[]=time();
						$inp[]=serialize($buyer_options);
						$inp[]=$transid;
						$this->db->query($sql,$inp);*/

					$item_det = $this->db->query ( "select d.brandid,d.vendorid,i.orgprice,i.price,i.nlc,i.phc,i.tax from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?", $itemid )->row ();

					$brandid = $item_det->brandid;
					$vendorid = $item_det->vendorid;
					
					$price = $item_det->price * $item ['qty'];

					$orderid = random_string ( "numeric", 10 );

					$sql = "insert into king_tmp_orders(id,userid,itemid,brandid,vendorid,bpid,bill_person,bill_address,bill_landmark,bill_city,bill_state,bill_pincode,bill_phone,bill_telephone,bill_email,ship_person,ship_address,ship_landmark,ship_city,ship_state,ship_pincode,ship_phone,ship_telephone,ship_email,quantity,amount,time,buyer_options,transid,i_orgprice,i_price,i_nlc,i_phc,i_tax,i_discount,i_coup_discount,i_discount_applied_on)" . " values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
					$inp = array ($orderid, $uid, $itemid, $brandid, $vendorid, $bpid );
					$params = array ("bill_person", "bill_address", "bill_landmark", "bill_city", "bill_state", "bill_pincode", "bill_mobile", "bill_telephone", "bill_email", "person", "address", "landmark", "city", "state", "pincode", "mobile", "telephone", "email" );
					foreach ( $params as $p ) {
						$$p = $this->input->post ( $p );
						$inp [] = $this->input->post ( $p );
					}
					$inp [] = $item ['qty'];
					$inp [] = $price;
					$inp [] = time ();
					$inp [] = serialize ( $buyer_options );
					$inp [] = $transid;

					$inp [] = $item_det->orgprice;
					$inp [] = $item_det->price;
					$inp [] = $item_det->nlc;
					$inp [] = $item_det->phc;
					$inp [] = $item_det->tax;
					$inp [] = ($item_det->orgprice - $item_det->price);
					if (isset ( $coupon_price_dets [$itemid] ['coup_discount'] ))
					$inp [] = $coupon_price_dets [$itemid] ['coup_discount'];
					else
					$inp [] = 0;

					if (isset ( $coupon_price_dets [$itemid] ['discount_applied_amount'] ))
					$inp [] = $coupon_price_dets [$itemid] ['discount_applied_amount'];
					else
					$inp [] = 0;

					$this->db->query ( $sql, $inp );
						
				}
					
					
					
					
				if ($coupon)
				$this->db->query ( "insert into king_used_coupons(coupon,transid) values(?,?)", array ($coupon ['code'], $transid ) );
					
				$umail = $this->viakingmodel->authorder( $transid, $amount, 1 ,$this->input->post("nomail"));
					
				$data ["info"] = array ("<b>New Transaction Created Via Offline </b>", " <div>
									<b>TRANSACTION ID </b>: $transid <br>
									<b>Amount  </b> : '.$amount 
									<br />
									<a href='" . site_url ( 'admin/trans/' . $transid ) . "'>Click here to view transaction</a> 
							  </div> 		
							" );
					
				$data ['page'] = "../../body/info";
				$this->load->view ( "admin", $data );
				$this->session->set_userdata("user",$ouser);
				
			} else {
				$data ['page'] = "offline";
				$this->load->view ( "admin", $data );
			}
		}
		
		function jx_getshipaddr()
		{
			if(!$_POST)
				die;
			$email=$this->input->post("email");
			$payload=$this->db->query("select * from king_users where email=?",$email)->row();
			echo json_encode($payload);
		}

		function jx_applycoupon($coupon=false,$ids=false,$qty=false,$silent=false)
		{
			$app=0;
			if(!$silent)
			{
				$coupon=$this->input->post("c");
				$ids=$this->input->post("ids");
				$qty=explode(",",$this->input->post("qty"));
			}

			$mrp_total=$total=0;
			$prods=array();
			$itemids=explode(",",$ids);
			foreach($itemids as $i=>$itemid)
			{
				$prod=$this->db->query("select i.price,d.catid,d.brandid,i.orgprice as mrp,i.shipsto from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?",$itemid)->row_array();
				$shipsto=$prod['shipsto'];
				$prod['qty']=$qty[$i];
				$total+=$prod['price']*$qty[$i];
				$mrp_total+=$prod['mrp']*$qty[$i];
				$prods[]=$prod;
			}

			$coupon=$couponid=trim($coupon);
			$c=$this->db->query("select * from king_coupons where code=? and status=0",array($coupon))->row_array();
			if(empty($c))
			$coupon=array();
			else
			$coupon=array("mode"=>$c['mode'],"brandid"=>$c['brandid'],"catid"=>$c['catid'],"type"=>$c['type'],"min"=>$c['min'],"value"=>$c['value'],"expires"=>$c['expires'],"code"=>$c['code']);
			if(!empty($coupon))
			{
				$app=0;
				if($coupon['expires']<time())
				$msg="Invalid coupon : coupon expired!";
				elseif($coupon['min']>$total)
				$msg="Coupon is valid only for a minimum amount of Rs {$coupon['min']}";
				else
				{
					$msg="Coupon applied";
					$app=1;
				}
			}else{
				$msg="Invalid Coupon $couponid";
			}
			$ttotal=$total;
			$c_total=$cd_total=0;
			$brands=$cats=array();
			$bvalue=$avalue=$disc=0;
			if($coupon)
			{
				$c_total=$total;
				if($coupon['mode']==1)
				$c_total=$mrp_total;

				if($coupon['catid']!="")
				{
					$cats=explode(",",$coupon['catid']);
					$c_total=0;
					foreach($prods as $p)
					if(in_array($p['catid'], $cats))
					$c_total+=($coupon['mode']==1?($p['mrp']*$p['qty']):($p['price']*$p['qty']));
				}

				if($coupon['brandid']!="")
				{
					$brands=explode(",",$coupon['brandid']);
					$c_total=0;
					foreach($prods as $p)
					if(in_array($p['brandid'], $brands))
					$c_total+=($coupon['mode']==1?($p['mrp']*$p['qty']):($p['price']*$p['qty']));
				}

				if($coupon['type']==0)
				{
					$cd_total=$c_total-$coupon['value'];
					if($cd_total<0)
					$cd_total=0;
				}
				elseif($coupon['type']==1)
				$cd_total=$c_total-floor($c_total*$coupon['value']/100);

				if($coupon['mode']==1)
				{
					$total=$mrp_total+$cd_total-$c_total;
					foreach($prods as $p)
					if(!empty($brands) || !empty($cats))
					if(!in_array($p['brandid'],$brands) && !in_array($p['catid'],$cats))
					$total-=($p['mrp']-$p['price'])*$p['qty'];
				}
				else
				$total=$total+$cd_total-$c_total;

			}
			if($total>$ttotal)
			$total=$ttotal;
			if($total<0)
			$total=0;

			$ret=array("apply"=>$app,"msg"=>$msg,"btotal"=>$mrp_total,"atotal"=>$total,"disc"=>$mrp_total-$total);
			if($silent)
			return $ret;
			echo json_encode($ret);

		}

		function jx_searchlive()
		{
			$okey=$this->input->post("p");
			
			/* $all=true;

			$okey=str_replace(","," ",$okey);
			$keys=explode(" ",$okey);
			$fkey=array();
			foreach($keys as $k)
			{
				//			$k=str_replace("'","",$k);
				$fkey[]=$k;
			}
			if($all)
			$key="+".implode(" +",$fkey);
			else
			$key=implode(" ",$fkey); */
			
			
			$key = '%'.$okey.'%';
			 
			
			$sql="select deal.catid,item.live,item.groupbuy,cat.name as category,
						cat.url as caturl,item.url,item.orgprice,item.name as itemname,
						item.available,item.id as itemid,item.quantity,brand.name as brandname,item.price,
						cat.name as category,deal.tagline,deal.dealid,deal.startdate,deal.enddate,
						item.pic,brand.id as brandid,brand.logoid as brandlogoid,deal.dealtype,
						item.name from king_dealitems as item 
					join king_deals as deal on deal.dealid=item.dealid 
					join king_categories as cat on cat.id=deal.catid 
					join king_brands as brand on brand.id=deal.brandid 
					where (deal.tagline like ? or deal.description like ? or deal.keywords like ? or brand.name like ? ) and item.live=1";
			$ret=$this->db->query($sql,array($key,$key,$key,$key))->result_array();
			echo json_encode($ret);
		}
		
		function new_cashback()
		{
			$user=$this->auth();
			if($_POST)
				$this->dbm->createnewcashback();
			$data['page']="new_cashback";
			$this->load->view("admin",$data);
		}
		
		function togglecashbackstatus($id,$status)
		{
			$status=!$status;
			$this->db->query("update king_cashback_campaigns set status=? where id=? limit 1",array($status,$id));
			redirect("admin/cashback_campaigns");
		}
		
		function cashback_campaigns()
		{
			$user=$this->auth(DEAL_MANAGER_ROLE);
			$data['page']="cashback_campaigns";
			$camps[0]=$this->db->query("select * from king_cashback_campaigns where status=1 and ".time()." between starts and expires")->result_array();
			$camps[1]=$this->db->query("select * from king_cashback_campaigns where status=1 and starts >".time())->result_array();
			$camps[2]=$this->db->query("select * from king_cashback_campaigns where status=1 and expires <".time())->result_array();
			$camps[3]=$this->db->query("select * from king_cashback_campaigns where status=0")->result_array();
			$data['camps_raw']=$camps;
			$this->load->view("admin",$data);
		}
		
		function define_cashback($dealid)
		{
			$user=$this->auth(DEAL_MANAGER_ROLE);
			if($_POST)
				$this->dbm->cashbackdef($dealid);
			$data['pres']=$this->db->query("select cash.* from king_dealitems i join king_product_cashbacks cash on cash.itemid=i.id where i.dealid=?",$dealid)->result_array();
			$data['page']="define_cashback";
			$this->load->view("admin",$data);
		}

		function freesamples()
		{
			$user=$this->auth(DEAL_MANAGER_ROLE);
			$data['config']=$this->db->query("select * from king_freesamples_config order by min desc")->result_array();
			$data['samples']=$this->db->query("select * from king_freesamples")->result_array();
			$data['page']="freesamples";
			$this->load->view("admin",$data);
		}

		function fsconfig()
		{
			$user=$this->auth(DEAL_MANAGER_ROLE);
			if($_POST)
			{
				$this->db->query("delete from king_freesamples_config");
				foreach(array("min","limit") as $i)
				$$i=$this->input->post($i);
				foreach($min as $i=>$m)
				{
					if(empty($m) || empty($limit[$i]))
					continue;
					$this->db->query("insert into king_freesamples_config(min,`limit`) values(?,?)",array($m,$limit[$i]));
				}
				redirect("admin/freesamples");
			}
			$data['config']=$this->db->query("select * from king_freesamples_config order by min desc")->result_array();
			$data['page']="fsconfig";
			$this->load->view("admin",$data);
		}

		function fsedit($id=false)
		{
			$user=$this->auth(DEAL_MANAGER_ROLE);
			if($_POST)
			{
				$pic = $this->randomChars ( 15 );
				if (isset ( $_FILES ['pic'] ) && $_FILES ['pic'] ['error'] == 0)
				{
					$imgname=$pic;
					$this->load->library("thumbnail");
					$img=$_FILES['pic']['tmp_name'];
					if($this->thumbnail->check($img))
					{
						$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/small/$imgname.jpg","width"=>160));
						$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/thumbs/$imgname.jpg","width"=>95,"max_height"=>50));
					}
				}
				else
				$pic="";
				$inp=array($_POST['name'],$_POST['min'],$_POST['available'],$pic,$id);
				if($id)
				{
					if($pic=="")
					{
						unset($inp[3]);
						$this->db->query("update king_freesamples set name=?,min=?,available=? where id=?",$inp);
					}else
					$this->db->query("update king_freesamples set name=?,min=?,available=?,pic=? where id=?",$inp);
				}else
				$this->db->query("insert into king_freesamples(name,min,available,pic) values(?,?,?,?)",$inp);
				redirect("admin/freesamples");
			}
			$data['fs']=array("name"=>"","min"=>"","pic"=>"","available"=>"1");
			if($id)
			$data['fs']=$this->db->query("select * from king_freesamples where id=?",$id)->row_array();
			$data['page']="fsedit";
			$this->load->view("admin",$data);
		}
		
		function coupon_usage_history()
		{
			if($_POST)
			{
				foreach(array("ref","status","type","mode","unlimited") as $inp)
					$$inp=$this->input->post($inp);
				$c=$this->dbm->searchcoupons($ref,$status,$type,$mode,$unlimited);
				
				$sql="";
				if($ref!="any")
					$sql.=" and c.remarks like ?";
				if($status!=0)
				{
					if($status-1==0)
					$sql.=" and c.used=".($status-1);
					else 
					$sql.=" and c.used>=".($status-1);
				}
				if($type!=0)
					$sql.=" and c.type=".($status-1);
				if($mode!=0)
					$sql.=" and c.status=".($mode-1);
				if($unlimited!=0)
					$sql.=" and c.status=".($unlimited-1);
				$sub=$sql;
				

				$sql="select c.*,o.transid,u.userid,u.name,u.email,o.time from king_coupons c join king_used_coupons uc on uc.coupon=c.code join king_orders o on o.transid=uc.transid left outer join king_users u on u.userid=o.userid where 1 $sub group by o.transid";
				$data['usage']=$this->db->query($sql,$ref)->result_array();
				
				$data['page']="coupon_usage";
				
				$data['title']="coupons by specific search";
				$this->load->view("admin",$data);
			}
		}
			
		function coupons()
		{
			$auth=$this->auth(TRUE);
			if($_POST)
			{
				foreach(array("ref","status","type","mode","unlimited") as $inp)
					$$inp=$this->input->post($inp);
				$c=$this->dbm->searchcoupons($ref,$status,$type,$mode,$unlimited);
				$data['found']=$c[0];
				$data['coupons']=$c[1];
				$data['title']="coupons by specific search";
			}
			else
			{
				$data['coupons']=$this->dbm->getrecentcoupons();
				$data['found']=count($data['coupons']);
				$data['title']="Recent Coupons";
			}
			$data['refs']=$this->dbm->getcouponrefs();
			$data['page']="coupons";
			$this->load->view("admin",$data);
		}
	
		function createcoupons()
		{
			$user=$this->auth(DEAL_MANAGER_ROLE);
			if($_POST)
				$this->dbm->proccreatecoupon();
			$data['brands']=$this->db->query("select * from king_brands order by name asc")->result_array();
			$data['cats']=$this->db->query("select * from king_categories order by name asc")->result_array();
			$data['page']="create_coupons";
			$this->load->view("admin",$data);
		}
		
		function editcoupon($id)
		{
			$user=$this->auth(DEAL_MANAGER_ROLE);
			$data['coupon']=$c=$this->db->query("select * from king_coupons where id=?",$id)->row_array();
			$data['brands']=$this->db->query("select * from king_brands order by name asc")->result_array();
			$data['cats']=$this->db->query("select * from king_categories order by name asc")->result_array();
			if($_POST)
			{
				foreach(array("type","value","mode","expires","min","brandcats","brands","cats","remarks","unlimited") as $i)
					$$i=$this->input->post($i);
		
				list($d,$m,$y)=explode("-",$expires);
				$expires=mktime(23,59,59,$m,$d,$y);
				if(!empty($brands))
					$brands=implode(",",$brands);
				else
					$brands="";
				if(!empty($cats))
					$cats=implode(",",$cats);
				else
					$cats="";
				$this->db->query("update king_coupons set type=?,value=?,mode=?,expires=?,min=?,brandid=?,catid=?,remarks=?,unlimited=? where id=?",array($type,$value,$mode,$expires,$min,$brands,$cats,$remarks,$unlimited,$id));
				$inp=array($c['code'],$type,$value,$mode,$expires,$min,$unlimited,time());
				$this->db->query("insert into king_coupon_activity(code,type,value,mode,expires,min,unlimited,time) values(?,?,?,?,?,?,?,?)",$inp);
				redirect("admin/coupon/$id");
			}
			$data['page']="create_coupons";
			$this->load->view("admin",$data);
		}

		function coupon($code)
		{
			$c=$data['coupon']=$this->db->query("select * from king_coupons where code=? or id=?",array($code,$code))->row_array();
			if(empty($c))
				show_error("coupon was not found");
				
			if($c['brandid']!="")
				$data['brands']=$this->db->query("select * from king_brands where id in (".$c['brandid'].")")->result_array();
			
			if($c['catid']!="")
				$data['cats']=$this->db->query("select * from king_categories where id in (".$c['catid'].")")->result_array();
				
			$data['acts']=$this->db->query("select * from king_coupon_activity where code=? order by id desc",$c['code'])->result_array();
			
			$data['used']=$this->db->query("select u.*,t.init as date from king_used_coupons u join king_transactions t on t.transid=u.transid where coupon=?",$code)->result_array();
			$data['page']="coupon";
			$this->load->view("admin",$data);
		}
		
		function new_invoice()
		{
			$invs=$this->db->query("select o.time,o.transid,i.orgprice,v.id from king_invoice v join king_orders o on o.id=v.order_id join king_dealitems i on i.id=o.itemid")->result_array();
			foreach($invs as $inv)
			{
				$transid=$inv['transid'];

				$trans=$this->db->query("select * from king_transactions where transid=?",$transid)->row_array();
				$t_amount=$trans['amount']-$trans['cod']-$trans['ship'];
					

				$m_amount=$this->db->query("select sum(i.orgprice*o.quantity) as s from king_orders o join king_dealitems i on i.id=o.itemid where o.transid=?",$transid)->row()->s;
				$discount=$m_amount-$t_amount;
					
				$o_discount=$inv['orgprice']*$discount/$m_amount;
					
				$nlc=($inv['orgprice']-$o_discount)*100/100;
				$phc=0;
				$cod=$trans['cod'];
				$ship=$trans['ship'];
					
				$this->db->query("update king_invoice set cod=?,ship=?,mrp=?,discount=?,nlc=?,phc=?,createdon=? where id=?",array($cod,$ship,$inv['orgprice'],$o_discount,$nlc,$phc,$inv['time'],$inv['id']));
			}
		}

		function del_pinvoice()
		{
			foreach($this->db->query("select id from king_orders where status=0")->result_array() as $r)
			$this->db->query("delete from king_invoice where order_id=? limit 1",$r['id']);
		}

		function upd_orders()
		{
			$orders=$this->db->query("select o.*,i.orgprice,i.price from king_orders o join king_dealitems i on i.id=o.itemid")->result_array();
			foreach($orders as $order)
			{
				$transid=$order['transid'];
					
				$trans=$this->db->query("select * from king_transactions where transid=?",$transid)->row_array();
				$t_amount=$trans['amount']-$trans['cod']-$trans['ship'];
					
				$m_amount=$this->db->query("select sum(i.orgprice*o.quantity) as s from king_orders o join king_dealitems i on i.id=o.itemid where o.transid=?",$transid)->row()->s;
				$discount=$m_amount-$t_amount;
					
				$o_discount=$order['orgprice']*$discount/$m_amount;
				$nlc=($order['orgprice']-$o_discount)*100/100;
				$phc=0;
				$mrp=$order['orgprice'];
				$price=$order['price'];
					
				$inp=array($mrp,$price,$nlc,$phc,14,0,$o_discount,$order['id']);
				$this->db->query("update king_orders set i_orgprice=?,i_price=?,i_nlc=?,i_phc=?,i_tax=?,i_discount=?,i_coup_discount=? where id=?",$inp);
			}
		}

		function new_trans()
		{
			$transs=$this->db->query("select * from king_transactions")->result_array();
			foreach($transs as $trans)
			{
				$ship=0;
				$cod=0;
				if($trans['amount']<MIN_AMT_FREE_SHIP)
				$ship=SHIPPING_CHARGES;
				if($trans['amount']>MIN_AMT_FREE_SHIP && $trans['mode']==1)
				$cod=COD_CHARGES;
				$this->db->query("update king_transactions set ship=?, cod=? where transid=?",array($ship,$cod,$trans['transid']));
			}
		}

		function stats($from="",$to="")
		{
			$user=$this->auth(TRUE);

			if($from=="" or $to=="")
			$from=$to="";

			if(!empty($from) && !empty($to))
			{
				list($d,$m,$y)=explode("-",$from);
				$from=mktime(0,0,0,$m,$d,$y);
				list($d,$m,$y)=explode("-",$to);
				$to=mktime(23,59,59,$m,$d,$y);
			}else {$from=0;$to=243872934828428492004;}
				

			$data['addtocarts']=$this->db->query("select count(1) as c from king_buyprocess b join king_m_buyprocess m on m.started_on between $from and $to and m.id=b.bpid")->row()->c;
			$data['bought']=$this->db->query("select count(1) as c from king_buyprocess b join king_m_buyprocess m on m.started_on between $from and $to and m.id=b.bpid where b.status=1")->row()->c;

			$data['mostpopular']=$this->db->query("select b.itemid,i.name,sum(bp.quantity) as c from king_buyprocess bp join king_m_buyprocess b on b.id=bp.bpid and b.started_on between $from and $to join king_dealitems i on i.id=b.itemid group by b.itemid order by sum(bp.quantity) desc limit 1")->row()->name;
			$data['mostbought']=$this->db->query("select b.itemid,i.name,sum(bp.quantity) as c from king_buyprocess bp join king_m_buyprocess b on b.id=bp.bpid and b.started_on between $from and $to join king_dealitems i on i.id=b.itemid where bp.status=1 group by b.itemid order by sum(bp.quantity) desc limit 1")->row()->name;
			$data['mostunlucky']=$this->db->query("select b.itemid,i.name,sum(bp.quantity) as c from king_buyprocess bp join king_m_buyprocess b on b.id=bp.bpid and b.started_on between $from and $to join king_dealitems i on i.id=b.itemid where bp.status=0 group by b.itemid order by sum(bp.quantity) desc limit 1")->row()->name;

			$data['trans']=$this->db->query("select count(1) as c from king_transactions where init between $from and $to")->row()->c;
			$data['strans']=$this->db->query("select count(1) as c from king_transactions where (status=1 or mode=1) and init between $from and $to")->row()->c;
			$data['pgs']=$this->db->query("select count(1) as c from king_transactions where mode=0 and init between $from and $to")->row()->c;
			$data['spgs']=$this->db->query("select count(1) as c from king_transactions where mode=0 and status=1 and init between $from and $to")->row()->c;
			$data['cods']=$this->db->query("select count(1) as c from king_transactions where mode=1 and init between $from and $to")->row()->c;

			$data['amount']=$this->db->query("select sum(amount) as s from king_transactions where init between $from and $to")->row()->s;
			$data['pgamount']=$this->db->query("select sum(amount) as s from king_transactions where mode=0 and init between $from and $to")->row()->s;
			$data['spgamount']=$this->db->query("select sum(amount) as s from king_transactions where mode=0 and status=1 and init between $from and $to")->row()->s;
			$data['codamount']=$this->db->query("select sum(amount) as s from king_transactions where mode=1 and init between $from and $to")->row()->s;

			$data['orders']=$this->db->query("select count(1) as s from king_orders where time between $from and $to")->row()->s;
			$data['porders']=$this->db->query("select count(1) as s from king_orders where status=0 and time between $from and $to")->row()->s;
			$data['prorders']=$this->db->query("select count(1) as s from king_orders where status=1 and time between $from and $to")->row()->s;
			$data['sorders']=$this->db->query("select count(1) as s from king_orders where status=2 and time between $from and $to")->row()->s;
			$data['rorders']=$this->db->query("select count(1) as s from king_orders where status=3 and time between $from and $to")->row()->s;
		
			$data['page']="stats";
			$this->load->view("admin",$data);
		}
		
		
		function statistics($from="",$to="")
		{
			$user=$this->auth(DEAL_MANAGER_ROLE);
		
			if($from=="" or $to=="")
				$from=$to="";
		
			if(!empty($from) && !empty($to))
			{
				list($d,$m,$y)=explode("-",$from);
				$from=mktime(0,0,0,$m,$d,$y);
				list($d,$m,$y)=explode("-",$to);
				$to=mktime(23,59,59,$m,$d,$y);
			}else {$from=0;$to=243872934828428492004;
			}
		
		
			$data['addtocarts']=$this->db->query("select count(1) as c from king_buyprocess b join king_m_buyprocess m on m.started_on between $from and $to and m.id=b.bpid")->row()->c;
			$data['bought']=$this->db->query("select count(1) as c from king_buyprocess b join king_m_buyprocess m on m.started_on between $from and $to and m.id=b.bpid where b.status=1")->row()->c;
		
			if($this->db->query("select b.itemid,i.name,sum(bp.quantity) as c from king_buyprocess bp join king_m_buyprocess b on b.id=bp.bpid and b.started_on between $from and $to join king_dealitems i on i.id=b.itemid group by b.itemid order by sum(bp.quantity) desc limit 1")->num_rows()){
			$data['mostpopular']=$this->db->query("select b.itemid,i.name,sum(bp.quantity) as c from king_buyprocess bp join king_m_buyprocess b on b.id=bp.bpid and b.started_on between $from and $to join king_dealitems i on i.id=b.itemid group by b.itemid order by sum(bp.quantity) desc limit 1")->row()->name;
			$data['mostbought']=$this->db->query("select b.itemid,i.name,sum(bp.quantity) as c from king_buyprocess bp join king_m_buyprocess b on b.id=bp.bpid and b.started_on between $from and $to join king_dealitems i on i.id=b.itemid where bp.status=1 group by b.itemid order by sum(bp.quantity) desc limit 1")->row()->name;
//			$data['mostunlucky']=$this->db->query("select b.itemid,i.name,sum(bp.quantity) as c from king_buyprocess bp join king_m_buyprocess b on b.id=bp.bpid and b.started_on between $from and $to join king_dealitems i on i.id=b.itemid where bp.status=0 group by b.itemid order by sum(bp.quantity) desc limit 1")->row()->name;
			}
			else
			{
				$data['mostpopular']= '';
				$data['mostbought']= '';
			}
		
			$data['trans']=$this->db->query("select count(1) as c from king_transactions where init between $from and $to")->row()->c;
			$data['strans']=$this->db->query("select count(1) as c from king_transactions where (status=1 or mode=1) and init between $from and $to")->row()->c;
			$data['pgs']=$this->db->query("select count(1) as c from king_transactions where mode=0 and init between $from and $to")->row()->c;
			$data['spgs']=$this->db->query("select count(1) as c from king_transactions where mode=0 and status=1  and init between $from and $to")->row()->c;
			$data['cods']=$this->db->query("select count(1) as c from king_transactions where mode=1 and init between $from and $to")->row()->c;
		
			//$data['amount']=$this->db->query("select sum(amount) as s from king_transactions where init between $from and $to")->row()->s;
			//$data['pgamount']=$this->db->query("select sum(amount) as s from king_transactions where mode=0 and init between $from and $to")->row()->s;
			//$data['spgamount']=$this->db->query("select sum(amount) as s from king_transactions where mode=0 and status=1 and init between $from and $to")->row()->s;
			//$data['codamount']=$this->db->query("select sum(amount) as s from king_transactions where mode=1 and init between $from and $to")->row()->s;
			
			
			$data['total_amount']=$this->db->query("select sum(o.quantity*i_orgprice) as s from king_orders o where admin_order_status between 3 and 5 and time between $from and $to")->row()->s;
		
			//$data['orders']=$this->db->query("select count(1) as s from king_orders where time between $from and $to")->row()->s;
			$data['porders']=$this->db->query("select count(1) as s from king_orders where admin_order_status<3 and time between $from and $to")->row()->s;
			//$data['prorders']=$this->db->query("select count(1) as s from king_orders where admin_order_status=1 and time between $from and $to")->row()->s;
			$data['sorders']=$this->db->query("select count(1) as s from king_orders where admin_order_status>=3 and admin_order_status<=5 and time between $from and $to")->row()->s;
			//$data['rorders']=$this->db->query("select count(1) as s from king_orders where status=5 and time between $from and $to")->row()->s;
		
			$data['top_ten_city'] = $this->db->query("select ship_city,count(*) as total from king_orders where admin_order_status>=3 and admin_order_status<=5 and time between $from and $to group by ship_city order by total desc  limit 10")->result_array();

			
			
			$data['from'] = $this->uri->segment(3);
			$data['to'] = $this->uri->segment(4);
			
			$data['page']="statistics";
			$this->load->view("admin",$data);
		}
		
		function get_monthsummary($pagi=0)
		{
			
			 
			$tm = time();
			
			$month = date('m',time());
			$year = date('Y',time()); 
			
			$dstr = $year.'-'.$month.'-01';
			
			
			 
			$tm = strtotime('-'.$pagi.' month', strtotime($dstr));
			
			$month = date('m',$tm);
			$year = date('Y',$tm);
			
			$dstr = $year.'-'.$month.'-01'; 
			
			$sql = " select monthname(FROM_UNIXTIME(time)) as month,
							year(FROM_UNIXTIME(time)) as year,
							count(1) as total_orders,
							count(distinct o.transid) as total_trans,
							sum(o.quantity*i_orgprice) as total_amount
					    from king_orders o
					    where `time` <= unix_timestamp(?) 
					    and o.admin_order_status between 3 and 5 
					    group by month,year
					    order by time desc 
						limit 3";
			$res = $this->db->query($sql,$dstr);
			
			// echo $this->db->last_query();
			$month_summary_list = $res->result_array();
			$month_summary_list = array_reverse($month_summary_list);
			echo json_encode(array('summary_det'=>$month_summary_list));
		}
		
		function generate_barcode($number){
			 
			exit;
			 
			
			$barcode_font = APPPATH.'/fonts/FREE3OF9.TTF';
			$plain_font   = APPPATH.'/fonts/pirulen.ttf';
			
			$width = 200;
			$height = 80;
			
			$img = imagecreate($width, $height);
			
			// First call to imagecolorallocate is the background color
			$white = imagecolorallocate($img, 255, 255, 255);
			$black = imagecolorallocate($img, 0, 0, 0);
			
			// Reference for the imagettftext() function
			// imagettftext($img, $fontsize, $angle, $xpos, $ypos, $color, $fontfile, $text);
			imagettftext($img, 36, 0, 10, 50, $black, $barcode_font, $number);
			
			//imagettftext($img, 14, 0, 40, 70, $black, $plain_font, $number);
			
			header('Content-type: image/png');
			
			imagepng($img);
			imagedestroy($img);
			
		 
		}
		
		/**
		 * function to update invoice print count 
		 * @param unknown_type $inv_no
		 */
		function upd_printcnt($inv_no='')
		{
			if(!$inv_no)
			{
				show_error("Invalid invoice no");
			}	
			
			
			$this->db->query("update king_invoice set total_prints = total_prints+1 where invoice_no = ? ",$inv_no);
			
			$this->db->query('insert into king_invoice_prints set invoice_no = ?,printed_on=now()',$inv_no);
			
			echo $this->db->query('select total_prints from king_invoice where invoice_no = ? ',$inv_no)->row()->total_prints;
		}
		
		/**
		 * function to allow outscan invoice 
		 */
		function outscan_invoice()
		{
			$data['page']="outscan_invoice";
			$this->load->view("admin",$data);
		}
		
		
		function p_outscaninvoice()
		{
			
			$order_status_flags = $this->config->item('order_status');
			$invoice_no = $this->input->post('o_barcode');
			
			if(!$invoice_no)
			{
				show_error("Invalid invoiceno");
			}	
			
			
			
			$order_det = $this->db->query('select is_outscanned,outscanned_on,outscanned_on,invoice_status,i.invoice_no,o.id as order_id,o.i_orgprice as o_mrp,o.transid as trans_id,d.tagline  as product_name,o.admin_order_status  as order_status
					from king_orders o 
					join king_dealitems di on di.id = o.itemid
					join king_deals d on d.dealid = di.dealid
					join king_invoice i on i.order_id = o.id 
					where i.invoice_no = ? ',$invoice_no); 
			
			
			 
			if($order_det->num_rows()){
				$order_det_arr = $order_det->result_array();
				if($order_det_arr[0]['invoice_status'] == 1)
				{
					
					if($order_det_arr[0]['is_outscanned'] == 0)
					{
						$odet_arr = array();
						
						foreach($order_det_arr as $order_details)
						{
							$this->db->query("update king_orders set admin_order_status = 2,actiontime=? where admin_order_status = 1 and id = ? ",array(time(),$order_details['order_id']));
							
							$odet = array();
							$odet['invoice_no'] = $order_details['invoice_no'];
							$odet['order_id'] = $order_details['order_id'];
							$odet['trans_id'] = $order_details['trans_id'];
							$odet['product_name'] = $order_details['product_name'];
							$odet['order_status'] = $order_status_flags[2];
							$odet['o_mrp'] = $order_details['o_mrp'];
							array_push($odet_arr,$odet);
						}
						
						$this->db->where('is_outscanned',0);
						$this->db->where('invoice_no',$invoice_no);
						$this->db->update('king_invoice',array('outscanned_on'=>time(),'is_outscanned'=>1));
						
						$this->update_transaction_status($order_det_arr[0]['trans_id']);
						$this->log_transactivity($order_det_arr[0]['trans_id'],"Orders in Invoice:".$invoice_no.' are <b>OUTSCANNED</b> on '.date('dMY h:i a').'<br />');
						
						
	
						$json_det = json_encode($odet_arr);
						
						echo '<script>parent.window.add_tolist('.$json_det.')</script>';
					}
					else
					{
						echo '<script>parent.window.show_error("Invoice Already Outscanned on '.date('dMY h:i a',$order_det_arr[0]['outscanned_on']).'")</script>';
					}
				}
				else
				{
					echo '<script>parent.window.show_error("Invoice Already Cancelled")</script>';
				}
				
				
			}
			else
			{
				echo '<script>parent.window.show_error("Invalid Invoiceno")</script>';
			}
							
			
		}


		function update_shipmentbyfile()
		{
			$data['page']= 'upd_shipmentbyfile';
			$this->load->view('admin',$data);
		}
		
		
		
		function p_updateshipmentbyfile()
		{
			$import_file_det = $_FILES['import_file'];
			
			$output=array();

			$fname_parts = explode('.',$import_file_det['name']);
			 
			$f_ext = end($fname_parts);
			
			if($f_ext != 'csv')
			{
				$output['status'] = 0;
				$output['message']="Invalid File type uploaded";
			}
			else
			{
			
			
				$import_file = $import_file_det['tmp_name'];
	
				
				$import_file_data = array();
				
				$row = 0;
				if (($handle = fopen($import_file, "r")) !== FALSE) 
				{
				    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
				    {
				    	if($row)
				    	{
				    		
				    		if(count(array_filter($data)))
				    		{
				    			$tmp = array();
				    			$tmp['file_name'] = $import_file_det['name'];
				    			$tmp['invoice_no'] = trim($data[0]);
				    			$tmp['awb_no'] = $data[1];
				    			$tmp['courier_name'] = $data[2];
				    			$tmp['ship_date'] = date('Y-m-d',strtotime($data[3]));
				    			$tmp['notify_customer'] = $data[4];
				    			array_push($import_file_data,$tmp);
				    		}
				    		
				    		
				    	}
				        $row++;
				        
				    }
				    fclose($handle);
				}
				
			//	print_r($import_file_data);
				
				
				$uniq_identifier = time();
				
				foreach($import_file_data as $supd_det)
				{
					$status = 0;
					$action = "new_shipment";
					
					if($supd_det['invoice_no'] != '')
					{
					
						$inv_det_res = $this->db->query('select * from king_invoice where invoice_no = ?', trim($supd_det['invoice_no']));
						if($inv_det_res->num_rows())
						{
							$inv_det_row = $inv_det_res->row_array();
							if($inv_det_row['invoice_status'] == 1){
								if($inv_det_row['tracking_id'])
								{
									$action = "update_shipment";
								}
							}
							else
							{
								$status = 2;
								$message = "Cancelled Invoice";
							}
						}else{
							$status = 2;
							$message = "Invalid Invoice no";
						}
						
						$supd_det['action'] = $action;
					}
					else
					{
						$status = 2;
						$action = "Invoice no is required";
					}	
					
					
					
					
					$ins_data = array();
					$ins_data['uniq_id'] = $uniq_identifier;
					$ins_data['file_name'] = $supd_det['file_name'];
					$ins_data['invoice_no'] = $supd_det['invoice_no'];
					$ins_data['awb_no'] = $supd_det['awb_no'];
					$ins_data['courier_name'] = $supd_det['courier_name'];
					$ins_data['ship_date'] = $supd_det['ship_date'];
					$ins_data['notify_customer'] = $supd_det['notify_customer'];
					$ins_data['status'] = $status;
					$ins_data['message'] = $action;
					
					$ins_data['logged_on'] = date('Y-m-d H:i:s');
					
					$this->db->insert('king_shipment_update_filedata',$ins_data);
					
					$upd_data = array();
					
					if($status==0){
						$upd_resp = $this->_process_bulk_shipupdate($supd_det);
						
						$upd_data['status'] = 1;
						$upd_data['message'] = $upd_resp['message']; 
					}else{
						$upd_data['status'] = $status;
						$upd_data['message'] = $message;
					}
					
					
					
					$this->db->where('id',$this->db->insert_id());
					$this->db->update('king_shipment_update_filedata',$upd_data);
					
				}
				
				
				$output['status'] = 1;
				$output['message']="File Imported Successfully";
			
			}

			$jsondata = json_encode($output); 
			echo "<script>window.parent.show_updlog($jsondata)</script>";
			
		}
		
		
		function _process_bulk_shipupdate($shipdet)
		{
			$user=$this->auth();
			
			$ship_invoice_no=$shipdet["invoice_no"];
			$inv_no = $ta_invoice = $ship_invoice_no;
			
			$action=$shipdet["action"];
			$track=$shipdet["awb_no"];
			$dmed=$shipdet["courier_name"];
			$shipdate=$shipdet["ship_date"];
			
			$notify_customer=$shipdet["notify_customer"];
			
			$output = array();
			$ta_invoiceno = '';
			
			$ta_invdet = array();
			$transid = '';
			$ta_shipped_orders = array();
			$inv_det = $this->db->query("select * from king_invoice where invoice_no = ? ",$inv_no);
			if($inv_det->num_rows()){
				$inv_det = $inv_det->result_array();
			
				$transid = $inv_det[0]['transid'];
			
				if($inv_det[0]['invoice_status'] == 1){
					foreach($inv_det as $inv){
			
			
						$ta_invdet_old = $inv;
						$time = time();
			
						array_push($ta_shipped_orders,$inv['order_id']);
						$ta_invoiceno = $inv['invoice_no'];
						$oid = $inv['order_id'];
						
						 
						
						
						
			
						if($action == 'update_shipment'){
							$this->db->query("update king_orders
									set shipid=?,
									shiptime=?,
									medium=?,
									actiontime=?
									where id=? ",array($track,strtotime($shipdate),$dmed,$time,$inv['order_id']));
			
			
							$this->db->query("update king_invoice
									set
									delivery_medium=?,
									tracking_id=?,
									shipdatetime=?,
									notify_customer=?
									where invoice_no=? ",array($dmed,$track,($shipdate),$notify_customer,$inv['invoice_no']));
			
							//$this->log_orderstatus($transid,'','',$inv['invoice_no'],1,$time, "Tracking ID Updated");
			
						}else{
							$this->db->query("update king_orders
									set status=2,
									admin_order_status =  3,
									shipped=1,
									shipid=?,
									shiptime=?,
									medium=?,
									actiontime=?
									where id=? ",array($track,strtotime($shipdate),$dmed,$time,$inv['order_id']));
			
			
							$this->db->query("update king_invoice
									set delivery_medium=?,
									tracking_id=?,
									shipdatetime=?,
									notify_customer=?
			
									where invoice_no=? ",array($dmed,$track,($shipdate),$notify_customer,$inv['invoice_no']));
			
			
			
			
			
			
						}
			
			
			
						if($notify_customer){
							// Send Mail to customer
							$msg = "Shipment Status Change notification sent to customer";
			
							
			
						}else{
							$msg = "Shipment Status Change notification not sent to customer";
						}
			
			
					}
					$output['status'] = 1;
			
			
					if($action == 'update_shipment'){
						$output['message'] = " Shipment Details updated,
													<br />
													<table width=98% cellpadding=0 cellspacing=0 style='font-size:11px;border:1px solid #e3e3e3;margin:10px;'>
													<tr>
													<th>Changed</th>
													<th>From</th>
													<th>To</th>
													</tr>
													<tr>
													<td>Medium</td>	
													<td><b>{$ta_invdet_old['delivery_medium']}</b></td>
													<td><b>$dmed</b> </td>
													</tr>
													<tr>
													<td>Trackingid</td>
													<td><b>{$ta_invdet_old['tracking_id']}</b></td>
													<td><b>$track</b> </td>
													</tr>
													<tr>
													<td>Shipdate</td>
													<td><b>{$ta_invdet_old['shipdatetime']}</b></td>
													<td><b>$shipdate</b> </td>
													</tr>
													</table>
													";
						}else{
							$output['message'] = "Shipment Details updated and orders marked as Shipped ";
						}
			
			
						// log transaction activity
						if(count($ta_shipped_orders)){
			
						$ta_message = $output['message']." for Invoice # ".$ta_invoice;
			
			
			
						if($notify_customer){
							$shipped_orders_notify = '<br /><table border=1 cellpadding=4 cellspacing=3>';
							$shipped_orders_notify .= '<tr><td><b>Order</b></td><td><b>Status</b></td></tr>';
						foreach($ta_shipped_orders as $ta_orderid)
						{
							$ta_order_itemname = $this->db->query('select name from king_dealitems a join king_orders b on b.itemid = a.id where b.id = ? ',$ta_orderid)->row()->name;
							$shipped_orders_notify .= '<tr><td><b>'.$ta_order_itemname.'</b></td><td>Shipped</td></tr>';
						}
						$shipped_orders_notify .= '</table>';
			
			
						$emails=$this->db->query("select itemid,transid, bill_email as email1, ship_email as email2 from king_orders where id=?",$oid)->row_array();
						$msg=$this->load->view("mails/ordership",array('orderid'=>$oid,'transid'=>$emails['transid'],'shippedon'=>strtotime($shipdate),'courier'=>$dmed,'awn'=>$track,'item'=>$this->db->query('select name from king_dealitems where id=?',$emails['itemid'])->row()->name),true);
			//			$this->dbm->sendmail(array($emails['email1'],$emails['email2']),"Alert: Your order (transid: {$emails['transid']}) was shipped!",$msg);
			
						$send_to_emails = array();
						$send_to_emails[] = $emails['email1'];
						$send_to_emails[] = $emails['email2'];
						$send_to_emails[] = $this->input->post('ship_email');
			
						$send_to_emails = array_unique($send_to_emails);
			
						$send_to_emails = array_filter($send_to_emails);
			
						$this->dbm->email ($send_to_emails, "Snapittoday.com -  Your order (transid: {$emails['transid']}) was shipped!(Invoiceno : $inv_no ) ", nl2br (str_replace('TBL_DISPLAY_SHIPPED_ORDERS',$shipped_orders_notify,$msg)));
			
			
			
						$ta_message .= ' <br /> Mail sent to '.implode(',',$send_to_emails);
						}
			
			
						$this->log_transactivity($transid,$ta_message);
			
						}
			
						}else{
							$output['status'] = 2;
							$output['message'] = "Please note , cannot update shipment details, invoice already cancelled";
						}
			
			$this->update_transaction_status($transid);
			
			}else{
				$output['status'] = 2;
				$output['message'] = "Invoice not found";
			}
			
			
			
			
			return $output;
			
			
			//echo json_encode($output);
		}
		
		
		function download_shpupdfile($encoded_hash)
		{
			
			
			
			$res = $this->db->query("select * from king_shipment_update_filedata where md5(uniq_id) = ? ",$encoded_hash);
			if(!$res->num_rows())
			{
				show_error("Invalid Download file");
				exit;
			}	
			
			
			
			header ( 'Content-Type: application/csv' );
			header ( 'Content-Disposition: attachement; filename="updated_shipmentfile.csv"' );
			
			$this->load->dbutil();
			echo $this->dbutil->csv_from_result($res); 
		}
		
		 
	}
	?>