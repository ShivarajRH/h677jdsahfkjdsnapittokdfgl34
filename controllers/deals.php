<?php
/**
 * Controller for front end of ViaBazaar aka ViaKingSale
 * 
 * @package viakingsale
 * @subpackage frontend
 * @author Vimal <vimal@viaadz.com>
 * @since 2009/12
 * @version 0.9.4
 */

//include_once APPPATH.'libraries/mobdetect.php';

/**
 * Deals Controller class
 * 
 * @version 0.9.4
 */
class Deals extends Controller
{
	private $fb_apikey="7d7a395938b7475be93415f68933f2e0";   //sand43
	private $fb_secretkey="fc84335555f2678407115502a0c2c576"; //sand43

//	private $fb_apikey="6268b8327b1078e13f519c7afff006cb";			//live
//	private $fb_secretkey="08411ff7abe8513e61b5cbb7f2d1baf8";		//live
	
	
	/**
	 * 
	 * @var String Twitter api key
	 * @access private
	 */
//	private $tw_apikey="lSl6jg8kz5RmPHiMbmpByg";								//server
	/**
	 * @var String Twitter secret key
	 * @access private
	 */
//	private $tw_secretkey="GpZVcmd1MAjqLQFPD8lmmoKcJ2bosA1zzs2khJNhb8";
	
	
//	private $g_site="15415109912849810658";				
	
	/**
	 * 
	 * @var int Google site id
	 * @access private
	 */
	private $g_site="03950828367555161159";			//server
	
	/**
	 * Constructor for deals controller
	 * 
	 * Loads cart library and explo model.
	 * 
	 */
	function __construct()
	{
//		ini_set("display_errors","on");
//		error_reporting(0);
		parent::Controller();
		header("Content-Type: text/html; charset=UTF-8");
		header("Cache-Control: private, no-cache, no-store, must-revalidate");
		header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
		header("Pragma: no-cache");
		
		$this->load->library("cart");
		$this->load->library("email");
		$this->load->library("form_validation");
		$this->load->model("viakingmodel","dbm");
		$this->load->library("pettakam",array("repo"=>"cache","ext"=>"pkm_snp"));
		if($this->uri->segment(1)!="jx")
			$this->checkforemail();
	}
	
	function getverifiedbymob()
	{
		$user=$this->checkauth();
		if($user['verified'])
			redirect("spotlight");
		$code=$this->db->query("select verify_code as code from king_users where userid=?",$user['userid'])->row()->code;
		if($_POST)
		{
			$icode=trim($this->input->post("code"));
			if($icode==$code)
				redirect("verifyh/".md5($code));
			else
				$data['error']="Access code is not valid. Please enter correct one";
		}else{
			$this->dbm->sms($user['mobile'],"Access code for your account verification is {$code}. Please enter this code on 'Get Verified' page.");
//			$this->dbm->email($user['email'],"Access code for your account",$this->load->view("mails/accesscode",array("name"=>$user['name'],"code"=>$code),true));
		}
		$data['page']="getverified";
		$this->load->view("index",$data);
	}
	
	function getverified($act=false)
	{
		$user=$this->checkauth();
		if($user['verified'])
			redirect("spotlight");
		$code=$this->db->query("select verify_code as code from king_users where userid=?",$user['userid'])->row()->code;
		if($_POST)
		{
			$icode=trim($this->input->post("code"));
			if($icode==$code)
				redirect("verifyh/".md5($code));
			else
				$data['error']="Access code is not valid. Please check again";
		}
		if($act=="sendmail")
			$this->dbm->email($user['email'],"Access code for your account",$this->load->view("mails/accesscode",array("name"=>$user['name'],"code"=>$code),true),true);
		if($act=="sendsms")
			$this->dbm->sms($user['mobile'],"Access code for your account verification is {$code}. Please enter this code on 'Get Verified' page.");
		if($act)
			die;
		$data['page']="getverifiedmail";
		$this->load->view("index",$data);
	}
	
	function live()
	{
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']))
			$this->dbm->getlivefeed();
		else if(!$this->session->userdata("live_hash_part"))
			$this->session->set_userdata("live_hash_part",array(randomChars(10),rand(1000,9999)));
		$feed_data=$this->dbm->getlivefeed();
		$data['feed_hash']=$feed_data['hash'];
		$data['feed']=$feed_data['feed'];
		$data['page']="livefeed";
		$data['title']="Live Feed!";
		$this->load->view("index",$data);
	}
	
	function brands()
	{
		$brands=$this->dbm->getbrands();
		
		$as=$alphas=array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
		foreach($as as $alpha)
			$ret[$alpha]=array();
		foreach($brands as $c)
		{
			$alpha=strtolower($c['name']{0});
			if(!isset($ret[$alpha]))
			{
				$ret[$alpha]=array();
				$alphas[]=$alpha;
			}
			$ret[$alpha][]=$c;
		}
		for($i=0;$i<5;$i++)
			$r[$i]=array();
			
		$i=0;
		foreach($ret as $a=>$rt)
		{
			$r[$i][$a]=$rt;
			$i++;
			if($i>4)
				$i=0;
		}
		$data['alphas']=$alphas;
		$data['brands']=$r;
		$data['page']="brands";
		$this->load->view("index",$data);
	}
	
	private function checkforemail()
	{
		if($this->uri->segment(1)=="signout")
			return;
		$user=$this->session->userdata("user");
		if($user!=false)
		{
			if(!isset($user['mobile']))
				$user['mobile']=0;
			if($user['email']=="")
			{
				if($_POST)
					$this->updatedet();
				else
					$this->checkextdet();
			}
//			if($user['verified']==0)
//			{
//				if($_POST)
//					$this->updatevercode();
//				else
//					$this->checkvercode();
//			}
			
		}
	}
	
	
	/**
	 * Google signin page
	 * 
	 * Gets details from signed-in Google user account. 
	 * Creates new internal account for the user if not exists. 
	 * Signs in the user with his/her Google account.
	 * 
	 */
	function old_gsignin()
	{
//				$this->reg_closed();
		
		if(isset($_COOKIE['fcauth'.$this->g_site]))
		{
			$auth=$_COOKIE['fcauth'.$this->g_site];
			$url="http://www.google.com/friendconnect/api/people/@viewer/@self?fcauth=$auth";
			$c=curl_init($url);
			curl_setopt($c,CURLOPT_RETURNTRANSFER,1);			// get response data not to stdout
			$d=curl_exec($c);
			$d1=json_decode($d);
			$resp=$d1->entry;
			$uid=$resp->id;
			$userid=$this->dbm->checkspecialuser($uid,3);
			if($userid==false)
			{
				$this->dbm->newspecialuser($uid,$resp->displayName,3,randomChars(10));
				$userid=$this->dbm->checkspecialuser($uid,3);
			}
			if($userid!=false)
			{
				$user=$this->dbm->getuserbyid($userid);
				$this->session->set_userdata(array("user"=>$user));
				if($this->cart->total()==0)
					redirect("");
				redirect("checkout");
				$data['smallheader']=true;
				$data['page']="signedin";
				$this->load->view("index",$data);
			}
		}
		else
			redirect("");
	}
	
	function checkbps($itemid=false)
	{
		if(!$itemid)
			redirect("");
		$user=$this->checkauth();
		$bps=$this->session->userdata("bps");
		$bpid=$bps['bpid'];
		if($bps['itemid']!=$itemid)
			redirect("");
		foreach($this->cart->contents() as $c)
			if($c['id']==$itemid)
			{
				$rowid=$c['rowid'];
				$opts=array();
				if($this->cart->has_options($rowid))
					$opts=$this->cart->product_options($rowid);
				$opts['bpid']=$bpid;
				$this->cart->update(array("rowid"=>$rowid,"options"=>$opts));
				redirect("checkout");
			}
		$itemdetails=$this->dbm->getitemdetails($itemid);
		$name=$itemdetails['name'];
		$cart=array("id"=>$itemid,'qty'=>1,"price"=>$itemdetails['price'],"name"=>str_replace("&","-",$name),"options"=>array("bpid"=>$bpid));
		$this->cart->insert($cart);
		$this->dbm->savecart();
		redirect("checkout");
	}
	
	function buy($hash)
	{
		$bp=$this->dbm->getbpbyhash($hash);
		if(empty($bp))
			show_404();
		if($bp['bpu_status']!=0)
		{
			$data['page']="info";
			$data['info']=array("Oops!","This link is not valid anymore");
			$this->load->view("index",$data);
			return;
		}
		if(time()>$bp['expires_on'])
		{
			$data['page']="info";
			$data['info']=array("Buy Process Expired!","Sorry! This buy process has expired. You didn't finish it on time");
			$this->load->view("index",$data);
			return;
		}
		$user=$this->session->userdata("user");
		if(empty($user))
		{
			$this->session->set_userdata("bps",array("hash"=>$hash,"bpid"=>$bp['bpid'],"itemid"=>$bp['itemid']));
			redirect("signup");
		}
		if($user['userid']==$bp['userid'] || $bp['userid']==0)
		{
			$this->db->query("update king_buyprocess set userid=? where id=? limit 1",array($user['userid'],$bp['bpuid']));
			$item=$bp['itemid'];
			$qty=1;
			$bpid=$bp['bpid'];
			$itemdetails=$this->dbm->getitemdetails($item);
			$flag=true;
			if($itemdetails==false)
				$flag=false;
			if($flag && ($qty>$itemdetails['quantity'] || $itemdetails['live']==0))
				$flag=false;
			if(!$flag)
			{
				$this->session->unset_userdata("bps");
				$data['page']="info";$data['info']=array("Product Not Available","Sorry, product is not available now");
				$this->load->view("index",$data);return;
				redirect("");
			}
			$name=str_replace("'"," ",$itemdetails['name']);
			$cart=array("id"=>$item,'qty'=>$qty,"price"=>$itemdetails['price'],"name"=>str_replace("&","-",$name),"options"=>array("bpid"=>$bpid));
			$flag=false;
			foreach($this->cart->contents() as $cartitem)
			{
				if($cartitem['id']==$item)
				{
					$flag=true;break;
				}
			}
			if($flag)
			{
				$up=array('qty'=>$qty,"price"=>$itemdetails['price'],'rowid'=>$cartitem['rowid'],'options'=>array("bpid"=>$bpid));
				$this->cart->update($up);
				redirect("checkout");
			}
			$this->cart->insert($cart);
			$this->dbm->savecart();
			if($m)
				redirect(site_url("checkout"),"refresh");
			redirect($this->dbm->getitemurl($bp['itemid']));
		}
	}
	
	function updatecrp()
	{
		$user=$this->checkauth();
		if(!$_POST)
			redirect("profile");
		if($_FILES)
		{
			$this->load->library("thumbnail");
			$img=$_FILES['pic']['tmp_name'];
			if($this->thumbnail->check($img))
			{
				$pic=randomChars(12);
				$this->thumbnail->create(array("source"=>$img,"dest"=>"images/people/$pic.jpg","width"=>180,"max_height"=>190));
				$this->thumbnail->create(array("source"=>$img,"dest"=>"images/people/{$pic}_t.jpg","width"=>50,"max_height"=>60));
				$this->db->query("update king_profiles set pic=? where userid=? limit 1",array($pic,$user['userid']));
				$this->db->query("update king_boarders set pic=? where userid=? limit 1",array($pic,$user['userid']));
				if($this->session->userdata("boarder"))
				{
					$boarder=$this->session->userdata("boarder");
					$boarder['pic']=$pic;
					$this->session->set_userdata("boarder",$boarder);
				}
			}
		}
		$inps=array("designation","location","department","employee_no","desk_no","linkedin","facebook","twitter");
		foreach($inps as $inp)
			$$inp=strip_tags($this->input->post($inp));
		$this->db->query("update king_profiles set designation=?,department=?,location=?,employee_no=?,desk_no=?,linkedin=?,facebook=?,twitter=? where userid=?",array($designation,$department,$location,$employee_no,$desk_no,$linkedin,$facebook,$twitter,$user['userid']));
		$this->db->query("update king_boarders set facebook=?,twitter=?,linkedin=? where userid=? limit 1",array($facebook,$twitter,$linkedin,$user['userid']));
		if($this->input->post("optin")=="yes")
		      $this->db->query("update king_users set optin=1 where userid=? limit 1",$user['userid']);
     	else
      			$this->db->query("update king_users set optin=0 where userid=? limit 1",$user['userid']);
		redirect("profile#profile");
	}
	
	function transaction($transid)
	{
		$user=$this->auth();
		
		$data['trans']=$trans=$this->db->query("select t.* from king_transactions t join king_orders o on o.transid = t.transid where t.transid=? and o.userid = ? ",array($transid,$user['userid']))->row_array();
		if(empty($trans))
			show_404();
		else{
			$data['orders']=$this->db->query("select o.*,i.name as item from king_orders o join king_dealitems i on i.id=o.itemid where transid=? and userid=?",array($transid,$user['userid']))->result_array();
			$data['fss'] = $this->db->query("select fso.*,fs.name,fs.pic,fs.available from king_freesamples_order fso join king_freesamples fs on fs.id = fso.fsid where transid = ? ",$trans['transid'])->result_array();
			$data['page']="transaction";
		}
		$this->load->view("index",$data);
	}


	function profile()
	{
		$user=$this->session->userdata("user");
		if(!$user)
			redirect("");
		$data['p']=$this->dbm->getprofile($user['userid']);
		$data['orders']=$this->dbm->getallorders($user['userid']);
		foreach($data['orders'] as $i=>$o)
		{
			$data['orders'][$i]['totalinvites']=$this->db->query("select count(1) as c from king_buyprocess where bpid=?",$o['bpid'])->row()->c;
			$data['orders'][$i]['boughtinvites']=$this->db->query("select count(1) as c from king_buyprocess where bpid=? and status=1",$o['bpid'])->row()->c;
		}
		$data['favs']=$this->dbm->getfavsforuser($user['userid']);
		$data['loves']=$this->db->query("select i.name,i.pic,i.orgprice,i.url from king_item_lovers l join king_dealitems i on i.id=l.itemid where l.userid=?",$user['userid'])->result_array();
		$data['workemail']=$this->db ->query("select corpemail from king_users where userid=?",$user['userid'])->row()->corpemail;
		$data['cashbacks']=$this->dbm->getcashbacks($user['userid']);
		$data['referrals']=$this->db->query("select r.ncoupon,r.time,u.name from king_referral_coupon_track r join king_users u on u.userid=r.userid where r.referral=?",$user['userid'])->result_array();
		$data['usedcoupons']=$this->dbm->getusedcoupons($user['userid']);
		$data['page']="profile";
		$data['profile']=$this->dbm->getprofile($user['userid']);
		$data['addr']=$this->db->query("select address,city,pincode,landmark,state from king_users where userid=?",$user['userid'])->row_array();
		$data['points']=$this->db->query("select points from king_users where userid=?",$user['userid'])->row()->points;
		$this->load->view("index",$data);
	}
	
	function jxforpass()
	{
		if(!$_POST || !$this->session->userdata("visited"))
			die();
		$email=$this->input->post("email");
		$sql="select email,userid,corpemail from king_users where (email=? or corpemail=?) limit 1";
		$r=$this->db->query($sql,array($email,$email))->row_array();
		if(empty($r))
			die("User with email id doesn't exist");
		$hash=random_string("unique");
		$this->db->query("insert into king_password_forgot(hash,userid) values(?,?)",array($hash,$r['userid']));
		$ems=array($r['email']);
		if($r['corpemail']!=$r['email'])
			$ems[]=$r['corpemail'];
		$this->dbm->email($ems,"Password Reset","Hi,<br><br>As requested, we are resetting your password.<br>Please click on below link to reset your password.<br><br><a href='".site_url("resetpass/$hash")."'>".site_url("resetpass/$hash")."</a><br><br>Please ignore this email, if this action was done without your knowledge.<br><br>Warm Regards,<br>Snapittoday.com",true);
		echo "Password reset email is sent. Please click on the link in email to reset your password.";
	}
	
	function resetpass($hash)
	{
		if($_POST)
		{
			$user=$this->db->query("select userid from king_password_forgot where hash=?",$hash)->row_array();
			if(empty($user))
				redirect();
			$userid=$user['userid'];
			$password=$this->input->post("password");
			$this->db->query("update king_users set password=?,verified=1 where userid=? limit 1",array(md5($password),$userid));
			$this->db->query("delete from king_password_forgot where userid=?",$userid);
			$data['info']=array("Password Reset","Your password was changed. Please login using new password");
			$data['page']="info";
			$this->load->view("index",$data);
			return;
		}
		$r=$this->db->query("select userid from king_password_forgot where hash=?",$hash)->result_array();
		if(empty($r) || count($r)!=1)
			show_error("Invalid link<br>This link is not valid anymore");
		$r=$r[0];
		$html='<div><h2>Reset Password</h2><form id="resetpassfo" method="post">' .
				'<table cellpadding=5 style="margin:20px;font-size:120%;">' .
				'<tR><td>New Password : </td><td><input type="password" name="password"></td></tr>' .
				'<tr><td>Confirm Password : </td><td><input type="password" name="cpassword"></td></tr>' .
				'<tr><td></td><td><input type="submit" value="Change Password"></td></tr>' .
				'</table>' .
				'</form></div>' .
				'<script>' .
				'$(function(){
					$("#resetpassfo").submit(' .
					'function(){' .
					'if($("input[name=password]",$(this)).val().length<5){' .
					'alert("Password should be atleast five characters");return false;}
						if($("input[name=password]",$(this)).val()!=$("input[name=cpassword]",$(this)).val())' .
						'{' .
						'alert("Passwords are not same");' .
						'return false;' .
						'}
					}' .
					');
				})' .
				'</script>';
		$data['page']="echo";
		$data['echo']=$html;
		$this->load->view("index",$data);
	}
	
	function changeaddr()
	{
		$user=$this->session->userdata("user");
		if(!$user || !$_POST)
			redirect("");
		$inps=array("name","address","landmark","city","state","pincode");
		foreach($inps as $inp)
			$$inp=strip_tags($this->input->post($inp));
		$this->db->query("update king_users set name=?,address=?,landmark=?,city=?,state=?,pincode=? where userid=?",array($name,$address,$landmark,$city,$state,$pincode,$user['userid']));
		$user['name']=$name;
		$this->session->set_userdata("user",$user);
		redirect("profile#profile");
	}
	
	function fbsignin()
	{
		
	}
	
	function joinhands()
	{
		if($_POST)
		{
			$this->load->library("form_validation");
			$inps=array("name","business","contact","email","location");
			foreach($inps as $inp)
				$$inp=$this->input->post($inp);
			if($this->form_validation->valid_email($email))
			{
				$this->db->query("insert into king_supplier_contacts(name,business,contact_number,email,location) values(?,?,?,?,?)",array($name,$business,$contact,$email,$location));
				$msg="";
				foreach($_POST as $name=>$val)
					$msg.="$name : $val<br>";
				$this->dbm->email(array("admin@localcircle.in","sri@localcircle.in","hello@snapittoday.com"),"Supplier contact",$msg);
			}
			$data['page']="echo";
			$data['echo']="<h2 style='padding:20px 0px;padding-bottom:90px;'>Thank you!<br>Contact details added.<br>We will contact you at the earliest</h2>";
		}
		else
		$data['page']="joinhands";
		$this->load->view("index",$data);
	}
	
	function addressbook()
	{
		$user=$this->checkauth();
	}
	
	function promo_emails($url="")
	{
		if(empty($url))
			show_404();
		$data['maildata']=$md=$this->dbm->getfeaturedmail($url);
		if(empty($md))
			show_404();
		$data['brands']=$md['brands'];
		$data['items']=$md['items'];
		$this->load->view("mails/featured_mail",$data);
	}
	
	function featured()
	{
		$data['deals']=$this->dbm->getdealsforspotlight();
		$data['tops']=$this->dbm->gettopproducts();
		$data['sidepane']=$this->dbm->getsidepaneforspotlight();
		$data['brands']=$this->dbm->getfeaturedbrands();
		$data['featured']=$this->dbm->getfeatured();
		$data['recent']=$this->dbm->getrecentsold();
		$data['new']=$this->dbm->getnewproducts();
		$data['page']="featured";
		$data['title']="Discover new products in Beauty, Wellness and Healthcare in India";
		$this->load->view("index",$data);
	}
	
	/**
	 * Index page for viakingsale
	 * 
	 * Checks whether an user is signed-in or not.
	 * If not, loads up signin page or else redirect to deals listout page.
	 * 
	 */
	function index()
	{
		$this->session->set_userdata("visited","true");
		$user=$this->session->userdata("user");
		
		if(($user || $this->uri->segment(1)!="login") && $this->uri->segment(1)!="signup" && $this->uri->segment(1)!="register")
		{
			$this->featured();
			return;
		}
			
		$data['title']="Login to your account or create new account";
		if($this->uri->segment(1)=="signup")
		$data['page']="signup_alone";
		else
		$data['page']="indexsignup";
		
		$data['noheader']=true;
		
		$data['socio']=true;
		$this->load->view("index",$data);
	}
	
	function changepwd()
	{
		$user=$this->session->userdata("user");
		if(!$user || !$_POST)
			redirect("");
		$password=$this->input->post("password");
		$this->db->query("update king_users set password=? where userid=? limit 1",array(md5($password),$user['userid']));
		redirect("profile");
	}
	
	function whatru()
	{
		if(!$_POST)
			die();
		$inps=array("product","name","contact");
		foreach($inps as $inp)
			$$inp=$this->input->post("$inp");
		$this->load->library("form_validation");
		if($this->form_validation->valid_email($contact)||($this->form_validation->is_natural_no_zero($contact) && strlen($contact)==10))
			$this->db->query("insert into king_interested_products(product,name,contact) values(?,?,?)",array($product,$name,$contact));
	}
	
	function snapit($itemid)
	{
		$this->db->query("update king_dealitems set snapits=snapits+1 where id=? limit 1",$itemid);
		if($this->db->affected_rows()==1)
		echo $this->db->query("select snapits from king_dealitems where id=?",$itemid)->row()->snapits;
	}
	
	function emailsignup()
	{
		if(!$_POST)
			redirect();
		$inps=array("email","password","cpassword","corpemail","firstname");
		foreach($inps as $inp)
		{
			$v=$this->input->post($inp);
			if(empty($v) && $inp!="corpemail")
				redirect("");
			$$inp=$this->input->post($inp);
		}
		$mobile=0;
		$user=$this->dbm->getuserbyemail($email);
		if(!$user)
		{
			$data['info']=array("Registered!","Your account is registered. Please check your email and confirm your email id by entering access code.");
			$process=true;
			$corpid=0;
			if(!empty($corpemail))
			{
				$corpid=$this->dbm->getcreatecorp($corpemail);
				if($corpid==0)
				{
					$process=false;
					$data['info']=array("Oops!","Please use your corporate email id to get registered. We won't share your corporate email. You used : $corpemail");
				}
			}
			if($process)
			{
				$this->dbm->newuser($email,$firstname,$password,$mobile,rand(2030,424321),0,$corpemail,$corpid);
				$user=$this->dbm->getuserbyemail($email);
				$user['corp']=$this->dbm->getcorpname($user['corpid']);
				$this->session->set_userdata("user",$user);
				$boarder=$this->db->query("select br.username,br.pic,u.name,u.userid from king_boarders br join king_users u on u.userid=br.userid where br.userid=?",$user['userid'])->row_array();
				if(!empty($boarder))
					$this->session->set_userdata("boarder",$boarder);
				if($this->session->userdata("bps"))
				{
					$bps=$this->session->userdata("bps");
					redirect("buy/".$bps["hash"]);
				}
				if($this->session->userdata("login_redirect"))
				{
					$r=$this->session->userdata("login_redirect");
					$this->session->unset_userdata("login_redirect");
					redirect($r);
				}
				redirect("spotlight");
			}
		}
		else
			$data['info']=array("Oops!","Your email already exists. Please try signing in.");
		$data['page']="info";
		$mobdet=$this->checkmobile();
		$this->load->view($mobdet?"wap":"index",$data);
	}
	
	function dashboard()
	{	
		$user=$this->checkauth();
		if($user['corpid']==0)
			redirect("profile");
		$data['page']="dashboard";
		$r=$this->db->query("select sum(products) as p, sum(reviews) as r from king_profiles where corpid=?",$user['corpid'])->row();
		$data['totalbuy']=$r->p;
		$data['totalreview']=$r->r;
		$data['profile']=$this->dbm->getprofile($user['userid']);
		$data['coworkerslen']=$this->dbm->getcoworkerslen($user['userid'],$user['corpid']);
		$data['coworkers']=$this->dbm->gettencoworkers($user['userid'],$user['corpid']);
		$data['purchases']=$this->dbm->getcwpurchases($user['userid'],$user['corpid']);
		$this->load->view("index",$data);
	}
	
	function emailsignup_asdas()
	{
		if(!$_POST)
			redirect();
		$inps=array("email","password","cpassword","mobile");
		foreach($inps as $inp)
			$$inp=$this->input->post($inp);
		if($this->form_validation->valid_email($email))
		{
			$data['info']=array("Registered!","Your account registered. Please sign in using your info"); 
			if($this->db->query("select 1 from king_users where email=?",$email)->num_rows()!=0)
				$data['info']=array("Failed","Your email id already exists");
			else
				$this->dbm->newuser($email,$email,$password,$mobile,rand(2030,424321),0);
		}
		else
			$data['info']=array("Registration Failed","Sorry! Not able to register your account. Contact Support");
		$data['page']="info";
		$mobdet=$this->checkmobile();
		$this->load->view($mobdet?"wap":"index",$data);
	}
	
	function rmitem($rid)
	{
		$this->load->library("cart");
		$this->cart->update(array(
               'rowid' => $rid,
               'qty'   => 0
            ));
        if($this->cart->total()==0)
        	redirect("");
        redirect("yourcart");    
	}
	
	function yourcart()
	{
		$mobdet=$this->checkmobile();
		if(!$mobdet)
			redirect("checkout");
		$data['page']="yourcart";
		$this->load->view("wap",$data);
	}
	
	function emailsignin()
	{
		$mobdet=$this->checkmobile();
		if(!$_POST && $mobdet)
		{
			$data['page']="new/loginpanel";
			$this->load->view($mobdet?"wap":"index",$data);
			return;
		}
		if(!$_POST)
			die();
		$inps=array("email","password");
		foreach($inps as $inp)
			$$inp=$this->input->post("$inp");
		if($this->db->query("select 1 from king_users where (email=? or corpemail=?) and password=?",array($email,$email,md5($password)))->num_rows()==1)
		{ 
			$ur=$this->db->query("select email from king_users where (email=? or corpemail=?) and password=?",array($email,$email,md5($password)))->row();
			$email=$ur->email;
			$user=$this->dbm->getuserbyemail($email);
			$user['corp']=$this->dbm->getcorpname($user['corpid']);
			$this->session->set_userdata(array("user"=>$user));
			$boarder=$this->db->query("select br.username,br.pic,u.name,u.userid from king_boarders br join king_users u on u.userid=br.userid where br.userid=?",$user['userid'])->row_array();
			$this->session->set_userdata("boarder",$boarder);
			$this->dbm->userlog($user['userid']);
			
			$this->dbm->loadcart();
			
			if($this->session->userdata("bps"))
			{
				$bps=$this->session->userdata("bps");
				redirect("buy/".$bps["hash"]);
			}
			if(($r=$this->session->userdata("login_redirect")))
			{
				$this->session->unset_userdata("login_redirect");
				redirect($r);
			}
			if($this->cart->total()!=0)
			{
				if($mobdet)
				{
					foreach($this->cart->contents() as $item)
					{
						$opts=$this->cart->product_options($item['rowid']);
						$sql="update king_buyprocess set userid=? where id=? limit 1";
						$this->db->query($sql,array($user['userid'],$opts['bpuid']));
					}
					redirect("yourcart");
				}
				redirect("checkout");
			}
			redirect("spotlight");
		}
//		$this->load->library("facebook",array());
//		$data['fburl']=$this->facebook->getLoginUrl(array("next"=>site_url("signin"),"cancel_url"=>base_url()));
		$data['error']=true;
		$data['page']="indexsignin";
		if(!$mobdet)
		{
			$this->session->set_flashdata("vloginerr","yes");
			redirect("login");
		}
		$this->load->view($mobdet?"wap":"index",$data);
	}
	
	function fblogin()
	{
//				$this->reg_closed();
		
		$this->load->library("form_validation");
		$data['tw_authUrl'] = site_url("twredirect");		
		$data['g_redirect']="gsignin";
		$data['g_site']=$this->g_site;
		
		$data['apikey']=$this->fb_apikey;
		$fb=new Facebook($this->fb_apikey,$this->fb_secretkey);
		$data['fb_user']=$fb_user=$fb->get_loggedin_user();
		if($fb_user && !$this->session->userdata("user"))
			redirect("signin");
		$data['smallheader']=true;
		if($this->session->userdata("user")==false)
			$data['page']="default";
		else
			redirect("deals");
		$data['fb_init']=true;
		$data['fblogin']=true;
		
		$this->load->view("index",$data);
	}
	
	function order($transid)
	{
		$user=$data['user']=$this->auth();
		$data['orders']=$this->dbm->getorder($transid,$user['userid']);
		if(empty($data['orders']))
			show_404();
		$data['trans']=$this->db->query("select * from king_transactions where transid=?",$this->db->query("select transid from king_orders where id=?",$transid)->row()->transid)->row_array();
		$data['notrans']=true;
		$data['page']="invoice";
		$this->load->view("index",$data);
	}
	
	
	function view_invoice($transid='',$invoice_no='')
	{
		$user=$data['user']=$this->auth();
		
		
		if(!$transid||!$invoice_no)
		{
			show_404();
			exit;
		}
		
		$sql="select item.nlc,item.phc,ordert.*,
					item.service_tax_cod,item.name,in.invoice_no,
					brand.name as brandname,
					in.mrp,ordert.i_tax as tax,in.discount,in.phc,in.nlc,in.service_tax
					from king_orders as ordert
					join king_dealitems as item on item.id=ordert.itemid
					join king_deals as deal on deal.dealid=item.dealid
					join king_brands as brand on brand.id=deal.brandid
					join king_invoice `in` on in.transid=ordert.transid and in.order_id=ordert.id
					where in.invoice_no=? and in.transid=? and in.is_b2b = 0 ";
		
		$q=$this->db->query($sql,array($invoice_no,$transid));
		
		if($q->num_rows()){
			$data['page']="invoice";
			$data['orders']=$orders=$q->result_array();
			$data['invoice_no']=$orders[0]['invoice_no'];
			$data['trans']=$this->db->query("select * from king_transactions where transid=?",$transid)->row_array();
			$data['inv_type'] = 'customer';
		}else{
			$data['page']="info";
			$data['info']=array("Please note","<b>Invalid Invoice no in reference</b>");
		}
		
		$this->load->view("index",$data); 
		
	}
	
	function myorders()
	{
		$user=$data['user']=$this->auth();
		$data['notrans']=true;
		$data['orders']=$this->dbm->getorders($user['userid']);
		$data['page']="orders";
		$this->load->view("index",$data);
	}
	
	function procagentlogin()
	{
		$this->load->library("encrypt");
		if($this->session->userdata("user")!==false)
			redirect("deals");
		if(!$_POST)
			redirect("agent");
		$this->load->library("form_validation");
		$this->form_validation->set_rules("via_username","User Name","required|trim");
		$this->form_validation->set_rules("via_password","Password","required");
		$this->form_validation->set_message("required","%s is required");
		$data['page']="agentlogin";
		$data['smallheader']=true;
		if($this->form_validation->run()==false)
		{
			$this->load->view("index",$data);
			return;
		}
		$username=$this->input->post("via_username");
		$password=$this->input->post("via_password");
//		echo $password;
		$this->load->library("agentapi");
		$auth=$this->agentapi->authenticate(array("username"=>$username,"password"=>$password));
		if($auth==false || empty($auth) || !isset($auth['AuthenticateUser']['Authenticated']) || $auth['AuthenticateUser']['Authenticated']!="Y")
		{
			$data['autherror']=true;
			$this->load->view("index",$data);
			return;
		}
		$agent=$auth['AuthenticateUser'];
		$uid=$agent['UserId'];
		$name=$agent['UserName'];
		$balance=$agent['Deposit'];
		$email=$agent['Email'];
		$mobile=$agent['Mobile'];
		$address=$agent['Street'];
		$state=$agent['State'];
		$city=$agent['City'];
		$pincode=$agent['Pincode'];
		$agent=$this->dbm->getagent($uid);
		if(empty($agent))
		{
			$this->dbm->createagent($uid,$name,$balance);
			$agent=$this->dbm->getagent($uid);
		}
		$agent['via_username']=$username;
		$agent['via_password']=$this->encrypt->encode($password,"ViMaL-VB-ViA-AiRlInE");
		$agent['email']=$email;
		$agent['mobile']=$mobile;
		$this->dbm->updateagent($agent['userid'],$uid,$name,$balance,$mobile,$email,$address,$city,$state,$pincode);
		$this->session->set_userdata(array("user"=>$agent));
		$logred=$this->session->userdata("logred");
		$this->session->unset_userdata("logred");
		if($logred!==false)
			redirect($logred);
		redirect("deals");
	}

	function agent()
	{
		$this->index();return;
		$user=$this->session->userdata("user");
		if($user!==false)
		{
			$data['info']=array("Already logged in","You are currently logged in as normal user. Please <a href='".site_url("signout")."' style='color:blue'>sign out</a> from current session to login as Agent");
			$data['page']="info";
			$this->load->view("index",$data);
			return;
		}
//			redirect("deals");
		$data['page']="agentlogin";
		$data['smallheader']=true;
		$this->load->view("index",$data);
	}
	
	function salesreport()
	{
		$user=$this->auth();
		if(!isset($user['aid']))
			redirect("");
		$data['user']=$user;
		$data['info']=array("Under Construction","Why so hurry?");
		$data['page']="salesreport";
		if($this->input->post("start")!==false && $this->input->post("end")!==false)
			$data['salesrep']=$this->dbm->getsalesreport($user['aid'],strtotime($this->input->post("start")),strtotime($this->input->post("end"))+(24*60*60));
		$this->load->view("index",$data);
	}
	
//	function api($salt)
//	{
//		$api=$this->dbm->getapi($salt);
//		if(empty($api))
//			die();
//		$data['item']=$this->dbm->getitemdetails($api['itemid']);
//		$this->load->view("widget",$data);
//	}
	
	function widget($salt)
	{
		$widget=$this->dbm->getwidget($salt);
		if(empty($widget))
			die();
		$raw_deals=$this->dbm->getdealsforwidget();
		foreach($raw_deals as $deal)
		{
			if(!isset($deals[$deal['category']]))
				$deals[$deal['category']]=array();
			$deals[$deal['category']][]=$deal;
		}
		$data['deals']=$deals;
		$this->load->view("widget",$data);
	}
	
	/**
	 * Twitter signin page
	 * 
	 * Gets details from twitter user account.
	 * If invite session flag is set, redirects user to send invite page.
	 * Or else creates an internal account for the new user, if not exists.
	 * Signs in the user with his/her twitter account.
	 * 
	 * @param string $token Token from twitter.com for api access
	 */
	private function reg_closed()
	{
		$data['info']=array("Registrations closed!","Thanks for your interest in ViaBazaar but registrations are currently not available.<br>Please contact support@viabazaar.in, if you are interested to become a franchisee.");
		$data['page']="info";
		die($this->load->view("index",$data,true));
	}
	
	/**
	 * Sign Up page
	 * 
	 * Validates for user session flags, invite id and form input.
	 * If success, creates a new account for the user and links up a referal with respect to invite ID.
	 * 
	 * @param string $id Invite id from user
	 */
	function processinvite($id)
	{
		
		$this->reg_closed();
		
		
		$this->load->library("form_validation","","fmv");
		$data['apikey']=$this->fb_apikey;
		$inviteUser=$this->dbm->getuserbyinviteid($id);
		$user=$this->session->userdata("user");
		if($inviteUser==false)
			$info=array("Invalid Invitation","Please check invitation URL");
		else if($user!=false && $user['userid']==$inviteUser['userid'])
			$info=array("Your invitation page","Please use this URL to invite your friends to Via Bazaar. Your friends can sign up from this page.<br>".site_url("invite/".$user['inviteid']));
		else if($user!=false)
			$info=array("Sign Up?","You are already signed in");
		if(!isset($info))
		{
			$fb=new Facebook($this->fb_apikey,$this->fb_secretkey);
			$data['fb_user']=$fb_user=$fb->get_loggedin_user();
			if($fb_user)
			{
				$response=$fb->api_client->users_getinfo($fb_user,"name,proxied_email");
//				var_dump($response);exit;
				$name=$response[0]['name'];
				$this->fmv->set_rules("email","email","required|valid_email");
				if(!isset($_POST['email']))
				{
				$data['fb_init']=true;
				$data['info']=array("Account Sign Up","Your Via Bazaar account will be created with following details.<br><br>Name : $name<br><form method='post'>Email : <input type='text' name='email'><br><br>And you will be able to login only from facebook");
				$data['info'][1].='<div align="right"><input type="submit" value="Create Account" style="padding: 3px 5px; background: rgb(51, 51, 51) none repeat scroll 0% 0%; font-family: verdana; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; color: rgb(239, 239, 239); font-weight: bold; font-size: 14px;"></div>';
				$data['page']="info";
				}
				else if($this->fmv->run()==false)
				{
					$data['info']=array("Sign Up Failed","Please enter a valid email");
					$data['page']="info";
				}
				else
				{
					$this->dbm->newfbuser($name,$this->input->post("email"),randomChars(10),$inviteUser['userid'],$fb_user);
					$data['page']="signedin";
//					$this->dbm->
				}
			}
			else
			{
			$data['fb_init']=true;
			if($_POST)
			{
				$this->fmv->set_rules("explo_email","Email","required|trim|valid_email||callback_checkuser");
				$this->fmv->set_rules("explo_name","Name","required|trim");
				$this->fmv->set_rules("explo_password","Password","required|trim|min_length[6]");
				$this->fmv->set_rules("explo_cpassword","Confirm Password","required|trim|min_length[6]|matches[explo_password]");
				$this->fmv->set_message("required","%s is required");
				$this->fmv->set_message("valid_email","Please enter a valid email");
				$this->fmv->set_message("numeric","%s should contain only numbers");
				$this->fmv->set_message("exact_length","%s should contain ten numbers");
				$this->fmv->set_message("matches","Passwords are not same");
				$this->fmv->set_message("checkuser","Email already exists. Please choose another.");
				if(strlen(trim($this->input->post("explo_mobile")))>0)
				$this->fmv->set_rules("explo_mobile","Mobile","trim|numeric|exact_length[10]");
				if($this->fmv->run()==false)
					$data['page']="signup";
				else
				{
					$bool=$this->dbm->newuser($this->input->post("explo_email"),$this->input->post("explo_name"),$this->input->post("explo_password"),$this->input->post("explo_mobile"),randomChars(10),$inviteUser['userid']);
					if($bool==true)
						$data['info']=array("Welcome {$this->input->post('explo_name')}","Successfully registered.<br>Please <a style='color:#00f' href='".site_url("signin")."'>sign in</a> and check out exclusive deals from ViaBazaar");
					else
						$data['info']=array("Argh! Error","Please try again later. Something went wrong :(");
					$data['page']="info";
				}
			}
			else
			$data['page']="signup";
			}
			$data['smallheader']=true;
		}
		else 
		{
			if($inviteUser!=false)
				$data['user']=$user;
			else 
				$data['smallheader']=true;
			$data['page']="info";
			$data['info']=$info;
		}
		$this->load->view("index",$data);
	}
	
	function startinviting()
	{
		$data['page']="startinviting";
		$this->load->view("index",$data);
	}
	
	/**
	 * Send Invite page
	 * 
	 * Page to send invitation through mail, Google, Twitter and Facebook.
	 */
	function invite()
	{
		$user=$this->checkauth();
		if(!$_POST)
			redirect("");
		$em=$this->input->post("emails");
		$emails=explode(",", $em);
		
		$coupon=$this->db->query("select code from king_coupons where referral=?",$user['userid'])->row_array();
		if(!empty($coupon))
			$coupon=$coupon['code'];
		
		$this->dbm->email($emails,"{$user['name']} has invited you to shop at snapittoday.com and also has shared a free snapcoupon",$this->load->view("mails/invite",array("name"=>$user['name'],"coupon"=>$coupon),true),true);
		$data["page"]="info";
		$data['info']=array("Invitation sent","Thanks for inviting your coworkers/friends");
		$this->load->view("index",$data);
	}
	
	function jx_viewcoworkers()
	{
		$user=$this->checkauth();
		$cws=$this->dbm->getcoworkers($user['userid'],$user['corpid']);
		$alpha=array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
		foreach($alpha as $a)
			$ret[$a]=array();
		foreach($cws as $c)
		{
			$al=strtolower(substr($c['name'],0,1));
			if(!isset($ret[$al]))
				$ret[$al]=array();
			$ret[$al][]=$c;
		}
		$this->load->view("index",$data);
	}
	
	
	function jx_writereview()
	{
		$visited=$this->session->userdata("visited");
		$user=$this->session->userdata("user");
		if(!$_POST || !$visited)
			die;
		$itemid=$this->input->post("itemid");
		$rating=$this->input->post("rating");
		$review=strip_tags($this->input->post("review"));
		if($user)
		$name=$user['name'];
		else
		$name=$this->input->post("uname");
		$this->dbm->newreview($itemid,$name,$rating,$review,$user);
	}
	
	function pricereq()
	{
		$user=$this->session->userdata("user");
		if($user==false || !$_POST)
			redirect("");
		$price=$this->input->post("price");
		$quantity=$this->input->post('qua');
		$url=$this->input->post("deal");
		$deal=$this->dbm->getitemforurl($url);
		if($deal==0)
			die;
		if($this->dbm->checkpricereq($user['userid'],$deal))
		{
			$data['info']=array("Already placed","You have already placed a price request for this deal. Please wait for response to this request.");
			$data['page']="info";
			$this->load->view("index",$data);
			return;
		}
		$this->dbm->newpricereq($user['userid'],$deal,$price,$quantity);
		$data['info']=array("Price request placed","We will get back to you through mail/sms. Please wait for response. Thanks!");
		$data['page']="info";
		$this->load->view("index",$data);
	}
	
	/**
	 * Twitter invitation page
	 * 
	 * Gets access token for twitter api.
	 * If twitter user logged in, sends tweet to twitter user page with invitation url.
	 * 
	 */
	function twinvite()
	{
		$data['user']=$user=$this->session->userdata("user");
		if($data['user']==false)
			redirect("");
		$token=$this->session->userdata("tw_token");
		$access_token=$this->session->userdata("tw_accesstoken");
		if($access_token!=false)
		{
			$this->session->unset_userdata("tw_redirect");
			$twitter = new TwitterOAuth($this->tw_apikey, $this->tw_secretkey,$access_token['oauth_token'],$access_token['oauth_token_secret']);
			$msgs=array("Hot private sale in ","Cool private deals in ","Check out great deals in ViaBazaar ","Private invitation link for ViaBazaar ","Join me as friend and enjoy great sale ");
			$msg=$msgs[rand(0,3)].site_url("invite/".$user['inviteid']);
			$content=$twitter->post("statuses/update",array("status"=>$msg));
//			$content = $twitter->OAuthRequest('https://twitter.com/statuses/update.xml', 'POST',array('status' => $msg));
//			var_dump($content);
			$data['page']="info";
			$data['info']=array("Tweeted","Your invitation URL has been posted in your status");
		}
		else
		{
			$twitter = new TwitterOAuth($this->tw_apikey, $this->tw_secretkey);
			$request_token = $twitter->getRequestToken();
			if($request_token==false)
				die("Unable to connect to Twitter. Please try again by refreshing page");
			$this->session->set_userdata(array("tw_token"=>$request_token));
			$this->session->set_userdata(array("tw_inviteredirect"=>"yes"));
			$authUrl= $twitter->getAuthorizeURL($request_token);
			$data['page']="info";
			$data['info']=array("Twitter Sign In","Please sign in to your twitter account by clicking the below button<div align='center' style='padding-top:5px;'><a href='{$authUrl}'><img src='".base_url()."images/twitter_signin.png'></a></div>");
		}
		$this->load->view("index",$data);
	}
	
	/**
	 * Facebook permission request page
	 * 
	 * If signed-in facebook user has not given permission to publish post on his or friends' wall, requests persmission for that.
	 * If permission is given, redirects user to facebook invite page.
	 *  
	 */
	function fbgetperm()
	{
		$user=$this->session->userdata("user");
		if($user==false)
			redirect("");
		$fb=new Facebook($this->fb_apikey,$this->fb_secretkey);
		$fb_user=$fb->get_loggedin_user();
		if(!$fb_user)
			redirect("fbinvite");
		if($fb->api_client->users_hasAppPermission("publish_stream")==true)
			redirect("fbinvite");
		$data['fb_init']=true;
		$data['apikey']=$this->fb_apikey;
		$data['user']=$this->session->userdata("user");
		$data['info']=array("We need your permission","ViaBazaar will post your invitation url in your friend's wall. Please click below button to give permission.<br><div align='center'><fb:prompt-permission perms=\"publish_stream\" next_fbjs=\"location='".site_url("fbinvite")."'\"> Grant permission </fb:prompt-permission></div>");
		$data['page']="info";
		$data['title']="Invite friends";
		$this->load->view("index",$data);
	}
	
	/**
	 * Facebook invite page
	 * 
	 * If user signed-in with facebook account and given permission to post on walls, then loads view page to post invite.
	 */
	function fbinvite()
	{
		$data['user']=$this->session->userdata("user");
		if($data['user']==false)
			redirect("");
		$fb=new Facebook($this->fb_apikey,$this->fb_secretkey);
		$fb_user=$fb->get_loggedin_user();
		if(!$fb_user)
		{
			$data['info']=array("Facebook Sign in","Please sign in to your facebook account by clicking the below button<div align='center' style='padding-top:5px;'><fb:login-button length=\"long\" background=\"light\" size=\"xlarge\"></fb:login-button></div>");
			$data['fb_init']=true;
			$data['apikey']=$this->fb_apikey;
			$data['page']="info";
			$this->load->view("index",$data);return;
		}
		
		if($fb->api_client->users_hasAppPermission("publish_stream")!=true)
			redirect("fbgetperm");
		if($fb_user)
		{
			$friends_get=$fb->api_client->friends_get();
			$friendstr=implode(",",$friends_get);
			$friends=$fb->api_client->users_getinfo($friendstr,"name");
		}
		else
			redirect("");
		
			$data['friends']=$friends;
		$data['page']="fbinvite";
		$data['title']="Invite Facebook friends";
		$this->load->view("index",$data);
	}
	
	/**
	 * Write on facebook user wall
	 * 
	 * AJAX request to write invite url on friend's wall.
	 * Uses POST input which contains facebook user-ids to post on wall.
	 * 
	 */
	
	function jxfbinviteuser()
	{
		$user=$this->session->userdata("user");
		if($this->input->post("uids")==false || $user==false)
			die("Error");
		$fb=new Facebook($this->fb_apikey,$this->fb_secretkey);
		$uids=explode(",",$this->input->post("uids"));
		$url=site_url("deals/invite/{$user['inviteid']}");
		$action[0]=array('text'=>"Invitation link","href"=>$url);
		foreach($uids as $uid)
			$fb->api_client->stream_publish("Check out great deals in ViaBazaar. My invitation link : $url Please click to sign up for free and join me as friend.",null,json_encode($action),$uid);
	}
	
	function jxfbthisuser($dealid,$itemid=null)
	{
		$user=$this->session->userdata("user");
		if($this->input->post("uids")==false || $user==false)
			die("Error");
		if($itemid!=null)
		{
			$item=$this->dbm->getitemdetails($itemid);
			if($item==false)
				die("Sale item not available. Sale might be expired");
			$dis=ceil(($item['orgprice']-$item['price'])/$item['orgprice']*100);
		}
		else
		{
			$deals=$this->dbm->getdealdetails($dealid);
			$deal=$deals[0];
			if($deals==false)
				die("Sale doesn't exist. Might be expired");
			$dis=ceil(($deal['orgprice']-$deal['price'])/$deal['orgprice']*100);
		}
		if($itemid==null)
			$msg="{$deal['brandname']} sale starting from Rs. {$deal['price']} at $dis% discount.";
		else
			$msg="{$item['name']} from {$item['brandname']} is available for Rs {$item['price']} at {$dis}% discount.";

		$previewid=rand(300,1293994971927);
		$this->dbm->newpreview($previewid,$dealid,$user['userid']);
		if($itemid!=null)
			$url=site_url("previewitem/$previewid/$itemid");
		else
			$url=site_url("preview/$previewid");
			
		$fb=new Facebook($this->fb_apikey,$this->fb_secretkey);
		$uids=explode(",",$this->input->post("uids"));
		$action[0]=array('text'=>"Sale link","href"=>$url);
		foreach($uids as $uid)
			$fb->api_client->stream_publish("I liked this deal in ViaBazaar. {$msg} Please take a look at this : $url",null,json_encode($action),$uid);
	}
	
	function selffbthis($uid,$dealid,$itemid=null)
	{
		$user=$this->session->userdata("user");
		if($itemid!=null)
		{
			$item=$this->dbm->getitemdetails($itemid);
			if($item==false)
				die("Sale item not available. Sale might be expired");
			$dis=ceil(($item['orgprice']-$item['price'])/$item['orgprice']*100);
		}
		else
		{
			$deals=$this->dbm->getdealdetails($dealid);
			$deal=$deals[0];
			if($deals==false)
				die("Sale doesn't exist. Might be expired");
			$dis=ceil(($deal['orgprice']-$deal['price'])/$deal['orgprice']*100);
		}
		if($itemid==null)
			$msg="{$deal['brandname']} sale starting from Rs. {$deal['price']} at $dis% discount.";
		else
			$msg="{$item['name']} from {$item['brandname']} is available for Rs {$item['price']} at {$dis}% discount.";

		$previewid=rand(300,1293994971927);
		$this->dbm->newpreview($previewid,$dealid,$user['userid']);
		if($itemid!=null)
			$url=site_url("previewitem/$previewid/$itemid");
		else
			$url=site_url("preview/$previewid");
			
		$fb=new Facebook($this->fb_apikey,$this->fb_secretkey);
		$action[0]=array('text'=>"Sale link","href"=>$url);
		$fb->api_client->stream_publish("I liked this deal in ViaBazaar. {$msg} Please take a look at this : $url",null,json_encode($action),$uid);
	}
	
	function updatevercode()
	{
		$user=$this->session->userdata("user");
		$code=$this->input->post("code");
		$this->db->query("update king_users set verified=1 where verify_code=? limit 1",$code);
		if($this->db->query("select verify_code from king_users where userid=?",$user['userid'])->row()->verify_code!=$code)
			$this->checkvercode();
		else
		{
			$user['verified']=1;
			$this->session->set_userdata("user",$user);
			redirect("");
		}
	}
	
	function updatedet()
	{
		$user=$this->session->userdata("user");
		if($user==false || !$_POST)
			redirect("");
		$uid=$user['userid'];
		if($this->db->query("select 1 from king_users where email=?",trim($this->input->post("email")))->num_rows()!=0)
			$this->checkextdet();
		$this->dbm->updateext($uid,trim($this->input->post("email")));
		$this->session->unset_userdata("user");
		$ud=$this->dbm->getuserbyid($uid);
		$ud['corp']="None";
		$this->session->set_userdata(array("user"=>$ud));
		$data['page']="info";
		$data['info']=array("Thanks!","Your info saved<script>function redire(){window.location='".current_url()."';} window.setTimeout('redire()',2000);</script>");
		die($this->load->view("index",$data,true));
	}
	
	function checkvercode()
	{
		$user=$this->session->userdata("user");
		if($user!=false)
		{
				$d='<form id="extform" method="post">' .
						'Welcome to Snapittoday.com<br>Please check your mail at '.$user['email'].' to enter access code.'.
				'<div style="color:red;display:none" id="exterror"></div>
				<table cellspacing="5" border="0" style="padding:10px;">';
				if($_POST)
					$d.='<tr><td colspan=2 style="color:red;"><b>Invalid code</b></td></tr>';
				$d.='<tr><td align="left">Enter Access Code </td><td>: <input type="text" name="code" value=""></td></tr>
				<tr><td></td><td align="right"><input type="image" src="'.base_url().'images/submit.png"></td></tr></table>
				<div style="font-size:10px;margin-bottom:50px;display:none;">Please enter correct info. These details are used in checkout and order status notifications.<br>We won\'t share these info with anyone in this World!</div></form><script>
				$(function(){
				$("#cartlink").attr("href","javascript:void(0)").hide();
				$("#extform").submit(function(){
					msg="";
					if(!is_require($("input[name=code]",$(this)).val()))
						msg="<div>Please enter access code</div>";
					if(msg!="")
					{
						$("#exterror").html(msg).show();
						return false;
					}
					return true;
				});
				});
				</script>';
				$data['info']=array("Welcome to Snapittoday.com",$d);
				$data['page']="info";
				die($this->load->view("index",$data,true));
				return;
			}
		else
			die("user not logged in");
	}
	
	function exclusive()
	{
		$dod=$this->dbm->getdod();
		$data['deals']=$this->dbm->getexclusivedeals();
		if(!empty($dod))
		{
			$ds=$data['deals'];
			$data['deals']=array($dod);
			foreach($ds as $d)
				$data['deals'][]=$d;
		}
		$data['exclusive']=true;
		$data['page']="home";
		$this->load->view("index",$data);
	}
	
	function spotlight()
	{
		$dod=$this->dbm->getdod();
		$data['deals']=$this->dbm->getdealsforspotlight();
		$data['edeals']=$this->dbm->getexclusivedeals();
		$data['sidepane']=$this->dbm->getsidepaneforspotlight();
		$data['recent']=$this->dbm->getrecentsold();
		$data['tops']=$this->dbm->gettopproducts();
		$data['page']="home";
		$this->load->view("index",$data);
	}
	
	function trig_url($url)
	{
		if($this->dbm->getmenuforurl($url))
			$this->menu($url);
		else if($this->dbm->getcatforurl($url))
			$this->category($url);
		else if($this->dbm->getbrandforurl($url))
			$this->brand($url);
		else
			show_404();
	}
	
	function destroybodp()
	{
		$this->cart->destroy();
		$this->session->unset_userdata("bodyparts_checkout");
		$this->dbm->loadcart();
		redirect("shoppingcart");
	}
	
	private function brand($url)
	{
		$data['pagetitle']=$cat=$this->dbm->getbrandforurl($url);
		if(!$cat)
			show_404();
		$data['title']="Buy $cat products in India";
		$data['brandlogo']=$this->db->query("select logoid from king_brands where url=?",$url)->row()->logoid;
		$data['deals']=$this->dbm->getdealsbybrandurl($url);
		$data['page']="bycategory";
		$mobile=$this->checkmobile();
		$this->load->view($mobile?"wap":"index",$data);
	}
	
	function category($url)
	{
		$data['pagetitle']=$cat=$this->dbm->getcatforurl($url);
		$data['title']="Buy $cat products in India";
		if(!$cat)
			show_404();
		$deals=$this->dbm->getdealsbycaturl($url);
		$cdeals=$dbrands=$brands=array();
		foreach($deals as $d)
		{
			if(array_search($d['brandid'], $brands)===false)
				$brands[]=$d['brandid'];
			if(!isset($cdeals[$d['brandid']]))
				$cdeals[$d['brandid']]=array();
			$cdeals[$d['brandid']][]=$d;
		}
		foreach($brands as $b)
		{
			$dbrands[$b]=$this->db->query("select name,url from king_brands where id=?",$b)->row_array();
			$dbrands[$b]['num']=$this->db->query("select count(1) as num from king_deals where ".time()." between startdate and enddate and publish=1 and brandid=?",$b)->row()->num;
		}

		$data['brands']=array();
		if(!empty($brands))
			$data['sbrands']=$this->db->query("select name,url,logoid from king_brands where id in (".implode(",",$brands).")")->result_array();
		
		$data['dbrands']=$dbrands;
		aarsort($cdeals);
		$data['deals']=$deals;
		$data['page']="bycategory";
		
		$data['menupic']=strtolower($this->db->query("select rand() as rid,m.name from king_categories c join king_deals d on d.catid=c.id and ".time()." between d.startdate and d.enddate join king_menu m on m.id=d.menuid where c.url=? order by rid asc limit 1",$url)->row()->name);
		
		$mobile=$this->checkmobile();
		if($mobile)
			$data['page']="bycategory";
		$this->load->view($mobile?"wap":"index",$data);
	}
	
	private function menunbrand($murl,$burl)
	{
		$brand=$this->dbm->getbrandforurl($burl);
		$menu=$this->dbm->getmenuforurl($murl);
		$data['pagetitle']="Buy $brand products"." ".$menu;
		$data['title']="Buy $brand products"." ".$menu." in India";
		if(!$brand)
			show_404();
		$data['brandlogo']=$this->db->query("select logoid from king_brands where url=?",$burl)->row()->logoid;
		$data['deals']=$this->dbm->getdealsbymenubrandurl($murl,$burl);
		$data['page']="bycategory";
		$mobile=$this->checkmobile();
		$this->load->view($mobile?"wap":"index",$data);
	}
	
	private function catnbrand($curl,$burl)
	{
		$cat=$this->dbm->getcatforurl($curl);
		$brand=$this->dbm->getbrandforurl($burl);
		$data['pagetitle']="Buy $brand products"." for ".$cat;
		$data['title']="Buy $brand products"." for ".$cat." in India";
		if(!$brand)
			show_404();
		$data['deals']=$this->dbm->getdealsbycatbrandurl($curl,$burl);
		$data['page']="bycategory";
		$mobile=$this->checkmobile();
		$this->load->view($mobile?"wap":"index",$data);
	}
	
	function menuncat($murl,$curl)
	{
		$cat=$this->dbm->getcatforurl($curl);
		$menu=$this->dbm->getmenuforurl($murl);
		$brand=$this->dbm->getbrandforurl($curl);
		$cat2=$this->dbm->getcatforurl($murl);
		if($menu=="" && $cat2!="" && $brand!="")
		{
			$this->catnbrand($murl, $curl);
			return;
		}
		if($menu=="" && $cat!="")
		{
			$this->category($curl);
			return;
		}
		if($menu!="" && $brand!="")
		{
			$this->menunbrand($murl, $curl);
			return;
		}
		if($menu=="" && $cat=="" && $cat2=="" && $brand!="")
		{
			$this->brand($curl);
			return;
		}
		if($cat=="" || $menu=="")
			show_404();
		$data['pagetitle']="Buy ".$cat." products ".$menu;
		$data['title']="Buy ".$cat." products ".$menu." in India";
		$deals=$this->dbm->getdealsbycatnmenuurl($murl,$curl);
		$brands=array();
		foreach($deals as $d)
		{
			if(array_search($d['brandid'], $brands)===false)
				$brands[]=$d['brandid'];
			if(!isset($cdeals[$d['brandid']]))
				$cdeals[$d['brandid']]=array();
			$cdeals[$d['brandid']][]=$d;
		}
		foreach($brands as $b)
		{
			$dbrands[$b]=$this->db->query("select name,url from king_brands where id=?",$b)->row_array();
			$dbrands[$b]['num']=$this->db->query("select count(1) as num from king_deals where ".time()." between startdate and enddate and publish=1 and brandid=?",$b)->row()->num;
		}

		$data['brands']=array();
		if(!empty($brands))
			$data['sbrands']=$this->db->query("select name,url,logoid from king_brands where id in (".implode(",",$brands).")")->result_array();
		if(isset($dbrands))
		$data['dbrands']=$dbrands;
		$data['deals']=$deals;
		$data['page']="bycategory";
		$data['menupic']=strtolower($this->db->query("select name from king_menu where url=?",$murl)->row()->name);
		
		$mobile=$this->checkmobile();
		if($mobile)
			$data['page']="bycategory";
		$this->load->view($mobile?"wap":"index",$data);
	}
	
	function menu($url)
	{
		$data['pagetitle']=$title=$cat=$this->dbm->getmenuforurl($url);
		$data['title']="Buy ".str_ireplace("for ", "", str_ireplace(" in "," ",$cat))." products in India";
		if(!$cat)
			show_404();
			
		$data['deals']=array();
		$deals=$this->dbm->getdealsbymenuurl($url);
		foreach($deals as $d)
		{
			if(!isset($data['deals'][$d['caturl']]))
				$data['deals'][$d['caturl']]=array();
			$data['deals'][$d['caturl']][]=$d;
		}
		$i=0;
		foreach($data['deals'] as $d)
		{
			$br=array();
			$brandscount[$i]=0;
			foreach($d as $deal)
			{
				if(array_search($deal['brandid'], $br)===false)
					$brandscount[$i]++;
				$br[]=$deal['brandid'];
			}
			$i++;
		}
		
		$brands=array();
		foreach($data['deals'] as $d)
			foreach($d as $r)
			{
					$brands[]=$r['brandid'];
					$cats[]=$r['catid'];
			}
		$brands=array_unique($brands);
		$cats=array_unique($cats);
		
		$data['brands']=array();
		if(!empty($brands))
			$data['brands']=$this->db->query("select name,url,logoid from king_brands where id in (".implode(",",$brands).") order by name asc")->result_array();
		
		$data['cats']=array();
		if(!empty($brands))
			$data['cats']=$this->db->query("select name,url from king_categories where id in (".implode(",",$cats).") order by name asc")->result_array();
		
		aarsort($data['deals']);

		$heads=array();
		foreach($data['deals'] as $curl=>$deal)
			$heads[]=$this->dbm->getcatforurl($curl)." ".$title;
			
		$data['menuurl']=$url;
		$data['heads']=$heads;
		$data['page']="menu_deals";
		$data['brandscount']=$brandscount;
		$data['menupic']=strtolower($this->db->query("select name from king_menu where url=?",$url)->row()->name);

		$mobile=$this->checkmobile();
		if($mobile)
		{
			$data['deals']=$deals;
			$data['page']="bycategory";
		}
		$this->load->view($mobile?"wap":"index",$data);
	}
	
	function checkextdet()
	{
		$user=$this->session->userdata("user");
		if($user!=false)
		{
			if(!isset($user['mobile']))
				$user['mobile']=0;
			if($user['email']=="")
			{
				$d='<form id="extform" method="post">' .
						'We are collecting this info only for the first time.
				<div style="color:red;display:none" id="exterror"></div>
				<div align="center" style="padding:5px 0px;">
				<table cellspacing="0" cellpadding="10" border="0" style="margin:10px 20px;padding:5px;">';
				if($_POST)
					$d.='<tr><td colspan=2 style="color:red;"><b>Email id already exists</b></td></tr>';
				$d.='<tr style="background:#efefff;"><td align="left">Your Email </td><td>: <input type="text" name="email" style="width:250px;" value="'.$user['email'].'"></td>
				<td align="right"><input type="image" src="'.base_url().'images/submit.png"></td></tr>
				<tr>
				<td align="left" colspan="3"><div style="font-size:10px;margin-bottom:10px;">Please enter correct info. These details are used in checkout and order status notifications<br>We won\'t share these info with anyone in this World!</div></td>
				</tr>
				</table>
				</div>
				</form>
				<script>
				$(function(){
				$("#cartlink").attr("href","javascript:void(0)").hide();
				$("#extform").submit(function(){
					msg="";
					if(!is_email($("input[name=email]",$(this)).val()))
						msg+="<div>Invalid email address</div>";
					if(msg!="")
					{
						$("#exterror").html(msg).show();
						return false;
					}
					return true;
				});
				});
				</script>';
				$data['info']=array("Welcome to Snapittoday.com",$d);
				$data['page']="info";
				die($this->load->view("index",$data,true));
				return;
			}
			else
			redirect("");
		}
		else
			die("user not logged in");
	}
	
	/**
	 * Sign In page
	 * 
	 * Page to handle different types of sign-ins by facebook, google, twitter and normal account.
	 * Creates an internal account if no account exists for signed-in facebook user.
	 * Loads user session.
	 */
	function signin()
	{
//		include_once "fb/facebook.php";
		$this->load->library("facebook",array());
		$data['g_site']=$this->g_site;
		$data['apikey']=$this->fb_apikey;
		$data['smallheader']=true;
		$data['fb_init']=true;
		$data['g_redirect']="signin";
//		$fb=new Facebook($this->fb_apikey,$this->fb_secretkey);
		$fid=$this->facebook->getUser();
//		$data['fb_user']=$fb_user=$fb->get_loggedin_user();
		$data['fb_user']=$fb_user=$fid;
		if(!$_POST && $this->facebook->getSession())
		{
//					$this->reg_closed();
			
			$userid=$this->dbm->checkspecialuser($fb_user,2);
			if($userid==false)
			{
//				$response=$fb->api_client->users_getinfo($fb_user,"name");
				try{
				$resp=$this->facebook->api("/me");
				}catch(FacebookAPIException $e){die;}
				$this->dbm->newspecialuser($fb_user,$resp['name'],2,randomChars(10));
				$userid=$this->dbm->checkspecialuser($fb_user,2);
//				$data['info']=array("Sign-In failed","We are unable to find an Explovia account connected to your Facebook account<br>Please <a style='color:#00f;' onclick='FB.Connect.logout(function() { location=\"".base_url()."\"; }); return false;' href='".base_url()."'>sign in</a> again");
//				$data['page']="info";
			}
			if($userid!=false)
			{
				$user=$this->dbm->getuserbyid($userid);
				$this->dbm->updateuserlogin($user['userid']);
				$this->session->set_userdata(array("user"=>$user));
				$this->session->set_userdata(array("fb_signin"=>"yes"));
				if($this->cart->total()==0)
					redirect("");
				redirect("checkout");
				$data['fbuser']=true;
				$data['title']="Sign in";
				$data['page']="signedin";
			}
			else exit;
		}
		else
		{
		$this->load->library("form_validation","","fmv");
		$this->fmv->set_rules("explo_email","Email","required");
		$this->fmv->set_rules("explo_password","Password","required|callback_authenticate");
		$this->fmv->set_message("required","%s is required");
		$this->fmv->set_message("valid_email","Please enter a valid email");
		$this->fmv->set_message("authenticate","Incorrect email or password");
		$email=$this->input->post("explo_email",true);
		if($this->fmv->run()===true)
		{
			$user=$this->dbm->getuserbyemail($email);
//			$this->dbm->updateuserlogin($user['userid']);
			$this->session->set_userdata(array("user"=>$user));
			$data['page']="signedin";
		}else
		{
		$twitter = new TwitterOAuth($this->tw_apikey, $this->tw_secretkey);		
		$request_token = $twitter->getRequestToken();
//		var_dump($request_token);exit;
		$this->session->set_userdata("tw_token",$request_token);
		$token=$request_token['oauth_token'];
		$data['tw_authUrl'] = $twitter->getAuthorizeURL($token);		
			
			$data['page']="default";
		}
		}
		$this->load->view("index",$data);
	}
	
	function fbsignout()
	{
		$fb=new Facebook($this->fb_apikey,$this->fb_secretkey);
		$fb->expire_session();
		$fb->logout(base_url());
	}
	
	/**
	 * Sign out page
	 * 
	 * Destroys session and flags for user/special user logins.
	 */
	function signout()
	{
		if(isset($_COOKIE['fcauth'.$this->g_site]))
			unset($_COOKIE['fcauth'.$this->g_site]);
		$data['apikey']=$this->fb_apikey;
		$this->dbm->savecart();
		$this->session->unset_userdata("user");
		$this->session->sess_destroy();
		redirect("");
		
		if($this->session->userdata("fb_signin")!=false)
		{
			$data['fb_init']=true;
			$data['fb_signout']=true;
			$this->session->unset_userdata("fb_signin");
//			set_cookie($this->fb_apikey."_user",0,time()-60);
//			unset($_COOKIE[$this->fb_apikey]);
//			unset($_COOKIE[$this->fb_apikey."_user"]);
//			die("cookie cleared");
//			$fb=new Facebook($this->fb_apikey,$this->fb_secretkey);
//			$fb->logout(site_url("signout"));
//		exit;
		}
		$this->session->sess_destroy();
		$this->cart->destroy();
		$data['smallheader']=true;
		$data['page']="signedin";
		$data['signout']=true;
		$this->load->view("index",$data);
	}
	
	private function auth()
	{
		$user=$this->session->userdata("user");
		if($user===false)
			redirect("");
		return $user;
	}
	
	private function auth_red($url)
	{
		$user=$this->session->userdata("user");
		if($user===false)
		{
			$this->session->set_userdata("logred",$url);
			redirect("");
		}	
		return $user;
	}
	
	function claim_points()
	{
		$user=$this->auth();
		$points=$this->db->query("select points from king_users where userid=?",$user['userid'])->row()->points;
		if($points<POINTS_REDEEMABLE_MIN)
		{
			$data['info']=array("Not enough loyalty points","You should have atleast ".POINTS_REDEEMABLE_MIN." points to redeem");
			$data['page']="info";
			$this->load->view("index",$data);return;
		}
		$config=$data['config']=$this->db->query("select * from king_cashbacks_config order by value desc")->result_array();
		if($_POST)
		{
			$c_vs=array();
			foreach($config as $c)
				$c_vs[]=$c['value'];
			$nums=$this->input->post("nums");
			$vs=$this->input->post("v");
			foreach($vs as $v)
				if(!in_array($v, $c_vs))
				{
					$data['info']=array("Error!","We are not able to complete your request. Please try again after sometime");
					$data['page']="info";
					$this->load->view("index",$data);return;
				}
			$c=0;
			foreach($nums as $i=>$n)
				$c+=$n*$config[$i]['value'];
			if($c>$points)
			{
				$data['info']=array("Insufficient loyalty points!","We are not able to complete your request. You don't have enough points to redeem.");
				$data['page']="info";
				$this->load->view("index",$data);return;
			}
			foreach($nums as $i=>$n)
			{
				$value=$config[$i]['value'];
				$min=$config[$i]['min'];
				$time=mktime(23,59,59)+($config[$i]['validity']*24*60*60);
				for($i=0;$i<$n;$i++)
				{
					$code="CB".strtoupper(randomChars(8));
					$this->db->query("insert into king_coupons(code,type,value,mode,userid,min,created,expires) values(?,?,?,?,?,?,?,?)",array($code,0,$value,1,$user['userid'],$min,time(),$time));
					$this->db->query("insert into king_points_track(coupon,userid,time) values(?,?,?)",array($code,$user['userid'],time()));
				}
			}
			$this->db->query("update king_users set points=points-$c where userid=?",array($user['userid']));
			redirect("profile#cashbacks");
		}
		$data['points']=$points;
		$data['page']="claim";
		$this->load->view("index",$data);
	}
	
	function claim($url="")
	{
		$user=$this->auth();
		$cashback=$this->db->query("select * from king_cashbacks where url=? and userid=? and status=1",array($url,$user['userid']))->row_array();
		$config=$data['config']=$this->db->query("select * from king_cashbacks_config order by value desc")->result_array();
		if(empty($cashback))
			show_404();
		if($_POST)
		{
			$c_vs=array();
			foreach($config as $c)
				$c_vs[]=$c['value'];
			$nums=$this->input->post("nums");
			$vs=$this->input->post("v");
			foreach($vs as $v)
				if(!in_array($v, $c_vs))
				{
					$data['info']=array("Error!","We are not able to complete your request. Please try again after sometime");
					$data['page']="info";
					$this->load->view("index",$data);return;
				}
			$c=0;
			foreach($nums as $i=>$n)
				$c+=$n*$config[$i]['value'];
			if($c>$cashback['amount'])
			{
				$data['info']=array("Insufficient Cashback amount!","We are not able to complete your request. You don't have enough cashback to redeem.");
				$data['page']="info";
				$this->load->view("index",$data);return;
			}
			foreach($nums as $i=>$n)
			{
				$value=$config[$i]['value'];
				$min=$config[$i]['min'];
				$time=mktime(23,59,59)+($config[$i]['validity']*24*60*60);
				for($i=0;$i<$n;$i++)
				{
					$code="CB".strtoupper(randomChars(8));
					$this->db->query("insert into king_coupons(code,type,value,mode,userid,min,created,expires) values(?,?,?,?,?,?,?,?)",array($code,0,$value,1,$user['userid'],$min,time(),$time));
					$this->db->query("insert into king_cashbacks_track(coupon,transid,userid,time) values(?,?,?,?)",array($code,$cashback['transid'],$user['userid'],time()));
				}
			}
			$this->db->query("update king_cashbacks set status=2,claim_time=? where url=?",array(time(),$url));
			redirect("profile#cashbacks");
		}
		$data['cashback']=$cashback;
		$data['page']="claim";
		$this->load->view("index",$data);
	}
	
	
	function editqty()
	{
		$user=$this->session->userdata("user");
		if(!$user)
		{
			$user=$this->dbm->getuserbyemail("guest@localcircle.in");
			$user['corp']=$this->dbm->getcorpname($user['corpid']);
		}
		if(!$_POST || $this->session->userdata("specialcot")!=false)
			redirect("");
		$qty=$this->input->post("qty");
		$id=$this->input->post("id");
		
		$opts=$this->cart->product_options($id);
		
		foreach($this->cart->contents() as $item)
			if($id==$item['rowid'])
				break;
		$itemid=$item['id'];
		
		$bpid=$opts['bpid'];
		$bpuid=$opts['bpuid'];
		$itemdetails=$this->dbm->getitemdetails($itemid);
		
		if($qty>$itemdetails['max_allowed_qty'])
			$qty=$itemdetails['max_allowed_qty'];
		
		
		$price=$itemdetails['price'];
		$i_price=$item['price'];
		
		$item=array("rowid"=>$id,"qty"=>0);
		$this->cart->update($item);
		
		$this->db->query("update king_buyprocess set isrefund=1,quantity=? where bpid=? and userid=? limit 1",array($qty,$bpid,$user['userid']));
		if($this->db->query("select count(1) as l from king_buyprocess where bpid=?",$bpid)->row()->l==1 && $i_price==$itemdetails['price'])
			$this->db->query("update king_buyprocess set isrefund=0 where bpid=? and userid=? limit 1",array($bpid,$user['userid']));
		
		$name=$itemdetails['name'];
		$item=array("id"=>$itemid,"qty"=>$qty,"options"=>$opts,"name"=>str_replace("&","-",$name),"price"=>$price);
		
		$this->cart->insert($item);
		$this->dbm->savecart();
		
		$coupon=$this->session->userdata("coupon");
		if($coupon)
		{
			$this->session->unset_userdata("coupon");
			$couponid=$coupon['code'];
			$this->apply_coupon($couponid);
		}
		
		unset($_POST['qty']);
		unset($_POST['id']);
		
		$this->session->unset_userdata("fsselected");
		
		if($this->input->post("mobile"))
		{ 
			redirect("yourcart");
			//the end
			$data['echo']='<form id="coupform" action="'.site_url("checkout/step3").'" method="post">';
			foreach($_POST as $n=>$v)
				$data['echo'].='<input type="hidden" name="'.$n.'" value="'.htmlspecialchars($v).'">';
			$data['echo'].='<h2>Please wait</h2>';
			$data['echo'].='<script>$(function(){$("#coupform").submit();});</script>';
			$data['page']="echo";
			$this->load->view("wap",$data);
		}
//		redirect("viewcart");
	}
	
	/**
	 * Send invite emails
	 * 
	 * Sends invitation mails to all email id given through POST input.
	 * Invitation urls are placed inside emails.
	 * Uses email library of CI.
	 */
	function invitebyemail()
	{
		$user=$this->session->userdata("user");
		if($user==false)
		{
			redirect("");exit;
		}
		if($this->input->post("explo_inviteemails")!=false)
		{
			$this->load->helper("email");
			$this->load->library("email");
			$inemails=trim($this->input->post("explo_inviteemails"));
			$emails=explode(",",$inemails);
			$count=0;
			$arr=array();
			foreach($emails as $email)
			{
				$email=trim($email);
				if(valid_email($email))
				{
					$this->email->clear();
					$config['mailtype']="html";
					$this->email->initialize($config);
					$this->email->to($email);
					$this->email->from($user['email'],$user['name']);
					$this->email->subject("Great deals in ViaBazaar. Sign up for free!");
					$msg='<div style="background:#fff;padding:30px 15px 0px 20px;font-family:\'trebuchet ms\';font-size:13px;color:#fff;"><div align="left"><img src="'.base_url().'images/logo.png"></div>
							<div align="center" style="padding-top:10px;"><div style="margin-top:15px;background:#ddd;width:600px;border:1px solid #aaa;padding:10px;font-size:15px;color:#444;margin-left:20px;font-weight:bold;-moz-border-radius:5px;border-radius:5px;font-family:\'trebuchet ms\';" align="left">Hi '.$email.',<br>
							<br>Exclusive deals are available in ViaBazaar. I would like you to sign up and check out those fabulous deals. Please click below link to get signed up.
							<br><br>
							<Div align="center"><a style="color:#00f;" href="'.site_url("invite/{$user['inviteid']}").'">'.site_url("invite/{$user['inviteid']}").'</a></div></div></div>
							<div align="right" style="color:#606060;padding-bottom:3px;padding-top:50px;font-size:11px;font-family:arial;">This email was sent by ViaBazaar on behalf of '.$user['email'].'</div></div>';
					$this->email->message($msg);
					$this->email->send();
					$count++;
				}
				else
					array_push($arr,$email);
			}
			if(count($emails)==0)
				$data['info']=array("?","Enter email id of your friends");
			else if($count==0)
				$data['info']=array("Failed!","Invalid email. Please check recipients email id.");
			else
			{
				$info="Your invitation mail sent to $count recipients";
				if(count($arr)>0)
				{
					$info.="<br>Unable to send email to following recipients<br><i>";
					$info.=implode("<br>",$arr);
					$info.="</i><br>Please check email address";
				}
				$data['info']=array("Email sent",$info);
			}
				$data['page']="info";
		}
		else
			redirect("");
		$data['user']=$user;
		$this->load->view("index",$data);
	}
	
	/**
	 * Show room details
	 * 
	 * loads up room details for the given room id.
	 * This includes extra photos, videos and amenities available for given room.
	 * 
	 * @param int $id Room id
	 * @deprecated
	 */
	function showroom($id)
	{
				
		$user=$data['user']=$this->session->userdata("user");
		if($data['user']==false)
		{
			redirect("");exit;
		}
		$room=$data['roomDetail']=$this->dbm->getroom($id);
		$data['hotelDeal']=$this->dbm->gethoteldeal($data['roomDetail']['dealid']);
		$this->dbm->updateviewdeal($user['userid'],$room['dealid']);
		$data['roomResources']=$this->dbm->getresources($id);
		$data['amenities']=array(
						'parking'=>"Parking",
						'spa'=>"Spa",
						'gym'=>"Gym",
						'pool'=>"Pool",
						'bar'=>"Bar",
						'doctor'=>"Doctor",
						'carrentals'=>"Car Rentals",
						'internet'=>"Internet",
						'roomservice'=>"Room Service",
						'restaurant'=>"Restaurant"
		);
		$ranges=explode(",",$data['roomDetail']['availability']);
		list($startdate,$d)=explode("-",$ranges[0]);
		list($d,$enddate)=explode("-",$ranges[count($ranges)-1]);
		$data['startDate']=strtotime($startdate);
		$data['endDate']=strtotime($enddate);
		$data['hotelAmenities']=explode("/",$data['hotelDeal']['amenities']);
		$data['page']="showroom";
		$this->load->view("index",$data);
	}
	
	/**
	 * Show hotel deal details
	 * 
	 * Show details and rooms of hotel for given hotel id.
	 * 
	 * @param int $id Hotel id
	 * @deprecated
	 */
	function show($id)
	{
				
		$user=$data['user']=$this->session->userdata("user");
		if($data['user']==false)
		{
			redirect("");exit;
		}		
		$hotel=$data['hotelDeal']=$this->dbm->gethoteldeal($id);
		$data['roomDetails']=$this->dbm->getrooms($id);
		$this->dbm->updateviewdeal($user['userid'],$hotel['dealid']);
		$data['page']="show";
		$this->load->view("index",$data);
	}
	
	
	function deal($url)
	{
		$uris=explode("/",$this->uri->uri_string());
		$url=array_pop($uris);
//		$url=html_entity_decode($url);
		$this->benchmark->mark('url');
		$id=$this->dbm->getitemforurl($url);
		$this->benchmark->mark('url1');
		if($id==0)
			show_404();
		$this->benchmark->mark('item');
		$this->showsaleitem($id,$url);
		$this->benchmark->mark('item1');
	}
	
	function delivery_p()
	{
		$data['notrans']=true;
		$user=$this->session->userdata("user");
		if($user!==false)
			$data['user']=$user;
		$data['page']="delivery";
		$this->load->view("index",$data);
	}
	
	function privacy_policy()
	{
		$data['notrans']=true;
		$user=$this->session->userdata("user");
		if($user!==false)
			$data['user']=$user;
		$data['page']="privacy";
		$this->load->view("index",$data);
	}
	
	function pages($page)
	{
		$user=$this->session->userdata("user");
		$data['notrans']=true;
		if($user!==false)
			$data['user']=$user;
		$pager=array(
					"about_us"=>"aboutus",
					"shipping_policy"=>"shipping",
					"cancellation_policy"=>"cancel",
					"disclaimer"=>"disclaimer",
					"contact_us"=>"contact",
					"faqs"=>"faqs",
					"help_tags"=>"disc_help"
					);
		$data['page']="pages/".$pager[$page];
		$this->load->view("index",$data);
	}
	
	function terms()
	{
		$data['notrans']=true;
		$user=$this->session->userdata("user");
		if($user!==false)
			$data['user']=$user;
		$data['page']="terms";
		$this->load->view("index",$data);
	}
	
	function jx_updatemob()
	{
		$user=$this->checkauth();
		if(!$_POST)
			die;
		$mobile=$this->input->post("mobile");
		$this->db->query("update king_users set mobile=? where userid=?",array($mobile,$user['userid']));
		$user['mobile']=$mobile;
		$this->session->set_userdata("user",$user);
		redirect("getverified");
	}
	
	function jx_isemailavail()
	{
		if(!$this->session->userdata("visited"))
			die;
		$email=$this->input->post("email");
		if($this->db->query("select 1 from king_users where corpemail=?",$email)->num_rows()==0)
		{
			list($dasd,$cfrag)=explode("@",$email);
			$corp=$this->db->query("select * from king_corporates where email=?",$cfrag)->row_array();
			if(!empty($corp) && $corp['alias']!=0)
				$corp=$this->db->query("select * from king_corporates where id=?",$corp['alias'])->row_array();
			if(empty($corp))
			{
			 	die("<b class='blue'>'$email'</b> is the first account to be registered under the <b class='blue'>'{$cfrag}'</b> community profile. Fill up the below details to be become the first member.");
			}else{
				$count=$this->db->query("select count(1) as l from king_users where corpid=?",$corp['id'])->row()->l;
				die("<b class='blue'>'$email'</b> will be grouped under the '<b class='blue'>{$corp['name']}</b>' community profile.<br>On filling up the below details, you will be connected to <b class='green'>'{$count}' members</b> of <b class='blue'>{$corp['name']}</b> worldwide");
			}
			$flag="true";
		}
		else
			die("false");
		
	}

	function jx_request()
	{
		if(!$_POST)
			die;
		$id=$this->input->post("id");
		$mobile=$this->input->post("mobile");
		$email=$this->input->post("email");
		$this->load->library("form_validation");
		if($this->form_validation->valid_email($mobile)||($this->form_validation->is_natural_no_zero($param) && strlen($param)==10))
			$this->dbm->dealrequest($id,$mobile,$email);
	}
	
	function jx_alert()
	{
		if(!$_POST)
			die;
		$id=$this->input->post("id");
		$mobile=$this->input->post("mobile");
		$email=$this->input->post("email");
		$this->load->library("form_validation");
		if($this->form_validation->valid_email($mobile)||($this->form_validation->is_natural_no_zero($param) && strlen($param)==10))
			$this->dbm->dealalert($id,$mobile,$email);
	}
	
	function jx_subscribe()
	{
		if(!$_POST)
			die;
		$param=$this->input->post("subscr");
		$this->load->library("form_validation");
		if($this->form_validation->valid_email($param))
			$this->dbm->subscr_email($param);
		elseif($this->form_validation->is_natural_no_zero($param) && strlen($param)==10)
			$this->dbm->subscr_mobile($param);
	}
	
	
	/**
	 * Show sale item
	 * 
	 * Gets item details for given item id from db.
	 * Loads view page with details of item and sale that belongs to.
	 * 
	 * @param $id
	 */
	private function showsaleitem($id,$url="")
	{
//		$user=$this->checkauth();
		$this->benchmark->mark('itemdb');
		$this->session->set_userdata("visited","true");
		$user=$this->session->userdata("user");
		if($user!==false)
			$data['user']=$user;
		else
			$this->session->set_userdata(array("logred"=>"deal/".$url));
		if($this->session->userdata("specialcot") && $this->cart->total_items()==0)
			$this->session->unset_userdata("specialcot");
		$data['notrans']=true;
		
		$itemdetails=$this->dbm->getitemdetails($id,false);

		if($itemdetails==false || $itemdetails['discontinued']==1)
		{
			header("HTTP/1.1 404 Not Found");
			$data['info']=array("Deal not available","You missed it!<br>This sale item might be expired or product not available now");
			$data['page']="info";
			$this->load->view("index",$data);
			return;
		}
		if($itemdetails['publish']==0)
			$itemdetails['live']=0;
		if($itemdetails['enddate']<time())
		{
			$data['info']=array("Deal expired!","You missed it!<br>This product deal has expired");
			$data['page']="info";
			$this->load->view("index",$data);
			return;
		}
		$data['category']=$itemdetails['category'];
		$itemresources=$this->dbm->getitemresources($id);
		$res[0]=$res[1]=array();
		if($itemresources!=false)
		foreach($itemresources as $r)
		{
			if($r['type']==0)
				array_push($res[0],$r);
			else
				array_push($res[1],$r);
		}
		$data['itemresources']=$res;
		$data['extradeals']=$this->dbm->getextradeals($itemdetails['catid'],$itemdetails['brandid'],$itemdetails['vendorid'],$itemdetails['id']);
//		if($itemdetails['dealtype']==0)
//		if($itemdetails['enddate']<time() || $itemdetails['quantity']<=$itemdetails['available'])
//			$data['page']="expired";
//		else
			//$data['page']="showitem";
//		else if($itemdetails['dealtype']==1)
//		$data['page']="groupsale";
		if($itemdetails['is_giftcard'])
			$data['page']="giftcard_item";
		else
			$data['page']="showitem";
		
		if(!empty($itemdetails['shipsto']))
		{
			$shipsto=explode(",",$itemdetails['shipsto']);
			foreach($shipsto as $i=>$s)
				$shipsto[$i]=ucfirst(trim($s));
			$itemdetails['shipsto']=implode(", ",$shipsto);
		}
		if($itemdetails['sizing']==0 && $itemdetails['live']==1 && !($itemdetails['quantity']<=$itemdetails['available']))
			$data['relateds']=$this->dbm->getrelated($itemdetails['id'],$itemdetails['catid'],$itemdetails['brandid']);
		if($this->session->userdata("trig_gb")==$url)
			$data['init_gb']=true;
		$this->session->unset_userdata("trig_gb");
		$this->dbm->addhistory($itemdetails['id']);
		$data['itemdetails']=$itemdetails;
		$data['title']="Buy {$itemdetails['name']} online in India";
		$mobile=$this->checkmobile();
		$this->load->view($mobile?"wap":"index",$data);
		$this->benchmark->mark('itemdb1');
	}
	
	function jx_gb_redirect()
	{
		$url=$this->input->post("url");
		$this->session->set_userdata("login_redirect",$url);
		$this->session->set_userdata("trig_gb",$url);
	}
	
	function jx_reviews()
	{
		if(!$_POST)
			die;
		$id=$this->input->post("id");
		$data['reviews']=$this->dbm->getreviews($id);
		$this->load->view("body/reviews",$data);
	}
	
	function jx_invitefbfriends()
	{
		if(!$_POST)
			die;
		$this->load->library("facebook",array('appId'=>FB_APPID,'secret'=>FB_SECRET));
		
		$fb = $this->facebook->getUser();
		if(!$fb)
			die;
		
		$user=$this->db->query("select * from king_users where special_id=?",$fb)->row_array();
		if(empty($user))
			die;
			
		$fbs=explode(",",$this->input->post("fbs"));
		$msg=$this->input->post("msg");
		$off=COUPON_REFERRAL_VALUE;
		$code=$this->db->query("select code from king_coupons where referral=?",$user['userid'])->row_array();
		if(!empty($code))
			$code=$code['code'];
		else	$code="";
		foreach($fbs as $fbid)
		{
				try {
		        	$ret_obj = $this->facebook->api("/$fbid/feed", 'POST',
		                                    array(
		                                      'link' => 'http://snapittoday.com',
		                                      'message' => "$msg Please use snapcoupon $code to get Rs $off off on your first order"
		                                 ));
		      } catch(FacebookApiException $e) {
		      }
		}
	}
	
	function inviteforbp_nonc()
	{
		$this->load->view("body/cws_sel_noncorp");
	}
	
	function inviteforbp()
	{

		$user=$this->checkauth();
		$coworkers=$this->dbm->getcoworkers($user['userid'],$user['corpid']);
		$as=array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
		foreach($as as $alpha)
		{
			$ret[$alpha]=array();
//			for($i=0;$i<15;$i++)
//				$ret[$alpha][]=array('name'=>$alpha.randomChars(10),"userid"=>rand(3214324,242342342));
		}	
		foreach($coworkers as $c)
		{
			$alpha=strtolower($c['name']{0});
			if(!isset($ret[$alpha]))
			{
				$ret[$alpha]=array();
			}
			$ret[$alpha][]=$c;
		}
		$data['coworkers']=$ret;
		$this->load->view("body/cws_sel",$data);
	}
	
	private function checkmobile()
	{
		if(isset($_COOKIE['nomobile']))
			return false;
		return mobile_device_detect(true,false,true,true,true,true,true,false,false);
	}
	
	function campaign($key=false)
	{
		$keys=array("5928jf093foj390fi34fj309f","owu3rowjr09wjfi934rwjeifjsdf");
		if(!in_array($key, $keys))
			die;
		if($key=="owu3rowjr09wjfi934rwjeifjsdf")
		{
			$this->newsletter(1);
			return;
		}
		$data['dod']=$this->dbm->getdod();
		$data['deals']=$this->dbm->getrandomdeals(4);
//		$msg=$this->load->view("mails/newsletter",$data,true);
//		$this->dbm->email("vimal@localcircle.in","dsasd",$msg);
		$this->load->view("mails/newsletter",$data);
	}
	
	private function newsletter($id=1)
	{
		$this->load->view("newsletters/$id");
	}
	
	function showpreviewitem($pid,$id)
	{
		$preview=$this->dbm->getpreview($pid);
		if($preview==false)
			return;
		$inviteuser=$this->dbm->getuserbyid($preview['userid']);
		$data['inviteuser']=$inviteuser['name'];
		$data['inviteid']=$inviteuser['inviteid'];
			
		$itemdetails=$this->dbm->getitemdetails($id);
//		$data['user']=$this->session->userdata("user");
//		if($data['user']!=false)
//			redirect("");
		if($itemdetails==false)
		{
			$data['info']=array("Item not available","This sale item might be expired or deal not yet started");
			$data['page']="info";
			$this->load->view("index",$data);
			return;
		}
			if(time()>$itemdetails['startdate'])
				$duration=$itemdetails['enddate']-time();
			else
				$duration=$itemdetails['startdate']-time();
			$durmin=floor($duration/60);
			$durhr=floor($duration/60/60);
			$durday=floor($duration/24/60/60);
			$durmin=floor($durmin-$durhr*60);
			$durhr=floor($durhr-$durday*24);
			if($durday==0)
				$left="";
			else if($durday==1)
				$left="1 day ";
			else
				$left="$durday days ";
			if($durhr==0)
				$left.="<Br>{$durmin} mins";
			else if($durhr==1)
				$left.=$durmin!=0?"<br>{$durhr} hr <br>{$durmin} mins":"<br>{$durhr} hr";
			else
				$left.=$durmin!=0?"<br>{$durhr} hrs <br>{$durmin} mins":"<br>{$durhr} hrs";
			$itemdetails['left']=$left;
		$data['category']=$itemdetails['category'];
		$itemresources=$this->dbm->getitemresources($id);
		$res[0]=$res[1]=array();
		if($itemresources!=false) foreach($itemresources as $r)
		{
			if($r['type']==0)
				array_push($res[0],$r);
			else
				array_push($res[1],$r);
		}
		$data['itemresources']=$res;
		$data['lastcomment']=$this->dbm->getlastcommentforitem($id);
		if($data['lastcomment']!=false)
		$data['lastcomment']['comment']=breakstring($data['lastcomment']['comment'],190);
		$data['itemdetails']=$itemdetails;
		$data['preview']=true;
		$data['page']="showitem";
		$data['title']="{$itemdetails['name']} preview";
		$this->load->view("index",$data);
	}
	
	function showupcoming()
	{
		$data['user']=$user=$this->session->userdata("user");
		if($data['user']==false)
			redirect("");
		$deals=$this->dbm->getupcoming();
		$sdeals=array();
		foreach($deals as $deal)
		{
			$st=$deal['startdate'];
			$i=date("l d/m",$st);
			if(!isset($sdeals[$i]))
			$sdeals[$i]=array();
			array_push($sdeals[$i],$deal);
		}
		$data['deals']=$sdeals;
		$data['page']="upcoming";
		$data['title']="Upcoming Sales";
		$this->load->view("index",$data);
	}
	
	function showcomments($id)
	{
		$data['user']=$user=$this->session->userdata("user");
		if($data['user']==false)
			redirect("");
		$itemdetails=$this->dbm->getitemdetailsforcomments($id);
		if($this->input->post('comdata')!=false)
			$this->dbm->postcomment(htmlspecialchars($this->input->post('comdata')),$user['userid'],$itemdetails['id'],$itemdetails['dealid']);
		$data['category']=$itemdetails['category'];
//		$data['comments']=$this->dbm->getcomments($id);
		$comments=$this->dbm->getcomments($id);
		$data['comments']=array();
		foreach($comments as $comment)
		{
			$d=$sec=time()-$comment['time'];
			if($d=floor($sec/24/60/60)>=1)
				$comment['time']="{$d} day";
			elseif($d=floor($sec/60/60)>=1)
				$comment['time']="{$d} hr";
			elseif($d=floor($sec/60)>=1)
			    $comment['time']=floor($sec/60)." min";
			else
				$comment['time']=$sec." sec";
			if($d>1)
				$comment['time'].="s";
			array_push($data['comments'],$comment);
		}
		if($itemdetails['enddate']<time())
			$data['itemexpired']=true;
		$data['itemdetails']=$itemdetails;
		$data['page']="showcomments";
		$data['title']="{$itemdetails['name']} comments";
		$this->load->view("index",$data);
	}
	
	function loginpanel()
	{
		$this->load->library("facebook",array());
		$fburl=$this->facebook->getLoginUrl(array("next"=>site_url("signin"),"cancel_url"=>base_url()));
		$this->load->view("body/new/loginpanel",array("fburl"=>$fburl));
	}
	
	function recent(){
		$user=$this->checkauth();
		$mobiledet=$this->checkmobile();
		$data['deals']=$this->dbm->getrecent();
		$data['page']="new/recent";
		$this->load->view($mobiledet?"wap":"index",$data);
	}
	
	function upcoming(){
		$user=$this->checkauth();
		$mobiledet=$this->checkmobile();
		$data['deals']=$this->dbm->getupcoming();
		$data['page']="new/upcoming";
		$this->load->view($mobiledet?"wap":"index",$data);
	}
	
	
	function procpr($id)
	{
		$user=$this->auth_red("pr/$id");
		$data['user']=$user;
		$pr=$this->dbm->getpr($id,$user['userid']);
		if(empty($pr) || $pr['userid']!=$user['userid'])
		{
			$data['page']="info";
			$data['info']=array("Invalid Price Request","This is not a valid price request. Please place a new price request from product page.");
			$this->load->view("index",$data);
			return;
		}
		$this->cart->destroy();
		$this->session->set_userdata("specialcot",$id);
		$item=$this->dbm->getitemdetails($pr['itemid']);
		if($item==false || $item['enddate']<time())
		{
			$data['page']="info";
			$data['info']=array("Sorry","This deal already ended!");
			$this->load->view("index",$data);
			return;
		}
		$name=str_replace("'"," ",$item['name']);
		$cart=array("id"=>$pr['itemid'],'qty'=>$pr['quantity'],"price"=>$pr['reqprice'],"name"=>str_replace("&","-",$name));
		$this->cart->insert($cart);
		$this->dbm->savecart();
		redirect("viewcart");
	}

	/**
	 * Clear shopping cart
	 * 
	 * AJAX request to destroy current active shopping cart.
	 * 
	 */
	function jxdestroycart()
	{
		if($this->session->userdata("user")==false)
			die("Authorization needed");
		$this->load->library("cart");
		$this->cart->destroy();
		$this->session->unset_userdata("specialcot");
		$this->jxshowcart();
//		echo "All items in your cart removed!";
	}
	
	/**
	 * Save shopping cart
	 * 
	 * AJAX request to save current shopping cart in DB.
	 * 
	 * @param string $name shopping cart name to save in db
	 */
	function jxsavecart($name)
	{
		if($this->session->userdata("user")==false)
			redirect("");
		$this->load->library("cart");
		$user=$this->session->userdata("user");
		$userid=$user['userid'];
		$cartid=$this->dbm->savecart($userid,$name);
		$items=$this->cart->contents();
		$this->dbm->savecartitems($cartid,$userid,$items);
		$this->cart->destroy();
		echo '<div style="font-family:trebuchet ms;font-size:15px;"><div style="font-size:25px;font-weight:bold;color:#ff9900;padding-bottom:20px;">Cart Saved</div>';
		echo('Your cart is saved!</div><script>updatecartitems();$("#fancy_inner").css("height","30%");</script>');
	}
	
	/**
	 * Delete item in shopping cart
	 * 
	 * AJAX request to delete an item in shopping cart.
	 * After deleting, loads details of the cart to show.
	 * 
	 * @param int $id item id to delete in cart
	 */ 
	
	function jxdeletecartitem($id)
	{
//		if($this->session->userdata("user")==false)
//			redirect("");		
		$this->load->library("cart");
		$items=$this->cart->contents();
		$flag=false;
		foreach($items as $item)
		{
			if($item['id']==$id)
			{
				$flag=true;
				break;
			}
		}
		if($flag)
		{
			$item['qty']=0;
			$this->cart->update($item);
		}
		$this->jxshowcart();
	}
	
	/**
	 * Process check out
	 * 
	 * Validates user post inputs, cart.
	 * On success, processes check out by ordering cart items to DB.
	 * Also updates address and mobile of user in DB. 
	 */
	function processCheckout()
	{
		$user=$this->auth();
		if($this->session->userdata("user")==false)
			redirect("");
		$data['items']=$items=$this->cart->contents();
		if(count($items)==0)
		{
			$this->checkout();
			return;
		}
		 $ar=array("person","phone","address","city","pincode","bill_person","bill_phone","bill_address","bill_city","bill_pincode");
		foreach($ar as $a)
		{
			$inp[$a]=$this->input->post($a);
			if($inp[$a]===false)
				die("Input missing ".$a);
		}
		if(!isset($user['aid']) && !$this->session->userdata("fran_auser"))
		{
		if($this->input->post("s_adrid")!=0)
		{
			$adr=$this->dbm->getaddress($this->input->post("s_adrid"));
			if(empty($adr))
				die("Sorry! Something went wrong somewhere... Please try again from start");
			$inp['person']=$adr['name'];
			$inp['phone']=$adr['phone'];
			$inp['address']=$adr['address'];
			$inp['city']=$adr['city'];
			$inp['pincode']=$adr['pincode'];
		}
		else
			$this->dbm->createaddress($inp,0,$user['userid']);
		if($this->input->post("billship")==false)
		{
		if($this->input->post("b_adrid")!=0)
		{
			$adr=$this->dbm->getaddress($this->input->post("b_adrid"));
			if(empty($adr))
				die("Sorry! Something went wrong somewhere... Please try again from start");
			$inp['bill_person']=$adr['name'];
			$inp['bill_phone']=$adr['phone'];
			$inp['bill_address']=$adr['address'];
			$inp['bill_city']=$adr['city'];
			$inp['bill_pincode']=$adr['pincode'];
		}
		else
			$this->dbm->createaddress($inp,1,$user['userid']);
		}
		else{
			$inp['bill_person']=$inp['person'];
			$inp['bill_address']=$inp['address'];
			$inp['bill_city']=$inp['city'];
			$inp['bill_pincode']=$inp['pincode'];
			$inp['bill_phone']=$inp['phone'];
		}
		}else{
			if($this->input->post("shiptome")!==false)
			{
				$inp['person']=$inp['bill_person'];
				$inp['address']=$inp['bill_address'];
				$inp['city']=$inp['bill_city'];
				$inp['pincode']=$inp['bill_pincode'];
				$inp['phone']=$inp['bill_phone'];
			}
			$this->dbm->createaddress($inp,1,$user['userid']);
		}
		$this->load->library("cart");
		$data['items']=$items=$this->cart->contents();
		if(count($items)==0)
		{
			$this->checkout();
			return;
		}
		$data['page']="processCheckout";
		$data['user']=$user=$this->session->userdata("user");
		$itemsquantity=$this->dbm->getitemsquantity($items);
		$data['items']=array();
		foreach($items as $item)
		{
			if($itemsquantity[$item['id']]<$item['qty'])
				$naitem=true;
			else
				array_push($data['items'],$item);
		}
		if(isset($naitem))
		{
			$this->viewcart();
			return;
		}
		if(trim($this->input->post("buy_mobile"))!="")	
			$inp['ship_phone']=$this->input->post("buy_mobile");
		if($this->input->post("buy_email")!="")	
			$inp['email']=$this->input->post("buy_email");
		else 
			$inp['email']=$user['email'];

		$mobile=$this->input->post("mobile");
		$address=$this->input->post("address");
//		if(isset($user['aid']))
//		{
			$this->load->library("agentapi");
//			$auth=$this->agentapi->authenticate(array("username"=>$user['via_username'],"password"=>$this->encrypt->decode($user['via_password'],"ViMaL-VB-ViA-AiRlInE")));
//			$ag=$auth['AuthenticateUser'];
//			$agent['balance']=$ag['Deposit'];
//			$agent['sid']=$ag['SessionId'];
//		}
		$agent['sid']=$this->session->userdata("via_sid");
		$suc=array();
		$oids=array();
		$fids=array();
		$this->load->model("adminmodel","adbm");
		foreach($items as $item)
		{
			$itm=$this->dbm->getitemforcheckout($item['id']);
			if(isset($user['aid']))
			{
				$amount=($item['price']-$itm['agentcom'])*$item['qty'];
				$price=$amount;
				$trans=$this->agentapi->transaction(array("comment"=>"ViaBazaar","amount"=>$amount,"sid"=>$agent['sid']));
				if($trans==false || empty($trans) || !isset($trans['ECommerceBook']['ReferenceId']) || !isset($trans['ECommerceBook']['Success']) || $trans['ECommerceBook']['Success']!="Y")
				{
					$data['info']=array("Failed","Sorry, Unable to do transaction for the product item : ".$item['name']."<br> Successful transactions : ");
					if(empty($suc))
						$data['info'][1].="<i>none</i>";
					foreach($suc as $s)
						$data['info'][1].=$s."<br>";
					$data['page']="info";
					$this->load->view("index",$data);
					return;
				}
				$tran=$trans['ECommerceBook'];
			}
			elseif($this->session->userdata("fran_auser"))
			{
				$fran=$this->session->userdata("fran_auser");
				$fran_balance=$this->db->query("select balance from king_franchisee where id=?",$fran['id'])->row()->balance;
				$price=$item['price']*$item['qty'];
				if(!$this->session->userdata("specialcot"))
				{
					$item['price']=$rprice=$this->db->query("select price from king_dealitems where id=?",$item['id'])->row()->price;
					$price=$rprice*$item['qty'];
				}
				$this->db->query("update king_franchisee set balance=? where id=?",array($fran_balance-$price,$fran['id']));
				$this->db->query("update king_users set balance=? where userid=?",array($fran_balance-$price,$fran['userid']));
				$user['balance']=$fran_balance-$price;
				$this->session->set_userdata("user",$user);
			}
			$bool=$oid=$this->dbm->checkout($user['userid'],array($item),$inp);
			if(isset($user['aid']))
			{
				$this->dbm->newagenttrans($user['aid'],$item['qty'],$itm['price']*$item['qty'],$itm['agentcom']*$item['qty'],$amount,$tran['ReferenceId'],$oid,$item['id']);
				$fids[]=$tran['ReferenceId'];
				$this->adbm->audit(1,"Order by travel agent {$user['name']}",$price,"Item name : {$item['name']}<br> Customer name : {$inp['person']}",$user['name']." (Agent)");
			}elseif($this->session->userdata("fran_auser"))
			{
				$this->db->query("insert into king_franch_transactions(franid,name,withdrawal,balance,time) values(?,?,?,?,?)",array($fran['id'],"order for {$item['name']}",$price,$fran_balance-$price,time()));
				$this->adbm->audit(1,"Order by Franchisee {$user['name']}",$price,"Item name : {$item['name']}<br>Frachisee balance before trans : {$fran_balance}<br> Customer name : {$inp['person']}",$fran['name']." (Franchisee)");
			}
			$suc[]=$item['name'];
			$oids[]=$oid;
		}
		if($bool==false)
		{
			$data['page']="info";
			$data['info']=array("Checkout Failed","Something went wrong somewhere. Sorry for this convenience. Please try again");
			$this->load->view("index",$data);
			return;
		}
		
		
		
//		$this->dbm->updateuseraddress($user['userid'],$mobile,$address);
		$this->cart->destroy();
			$data['page']="info";
			$data['info']=array("Success","Your order was placed successfully. We will get back to you through email/sms shortly, when your order is processed and shipped. <br>Thank you for shopping!");
//			if(isset($user['aid']))
//			{
				$data['info'][1].="<div align='center' style='padding:10px;'>".'<table border="1" cellspacing="2" cellpadding="4" style="background:1px solid #000;background:#fff;"><tr><th>Item Name</th><th>Order Id</th>';
				if(isset($user['aid']))
					$data['info'][1].="<th>VIA Trans ID</th>";
				$data['info'][1].="</tr>";
				foreach($suc as $i=>$s)
				{
					$data['info'][1].="<tr><td>{$s}</td><td>{$oids[$i]}</td>";
					if(isset($user['aid']))
						$data['info'][1].="<td>{$fids[$i]}</td>";
					$data['info'][1].="</tr>";
				}
				$data['info'][1].="</table></div>";
//			}
		$msg='Dear '.$user['name'];
		$msg.="<br><br>We thank you for shopping with Viabazaar.in<br>";
		$msg.='<div style="padding:10px;">';
		$msg.='<table cellpadding="5" cellspacing="0" border="1" width="500" style="font-size:14px;">';
		$msg.='<tr><th style="background:#000;color:#fff;font-size:14px;">Your order summary is provided below</th</tr>';
		$msg.="<tr><td>";
		$msg.="<table cellpadding='7' cellspacing='0' border='1' width='100%'>";
		$msg.='<tr><th>Order Id</th><th>Item Name</th><th>Unit Price</th><th>Qty</th><th>Total Rs</th></tr>';
		$i=0;
		$t=0;
		foreach($items as $item){
			$msg.='<tr><td>'.$oids[$i].'</td><td>'.$suc[$i].'</td><td>'.$item['price'].'</td><td>'.$item['qty'].'</td><td>'.($item['price']*$item['qty']).'</td></tr>';
			$t+=$item['price']*$item['qty'];
		}
		$msg.='<tr><td style="background:#ddd;height:30px;">Grand Total</td><td style="background:#ddd" align="right" colspan="4">'.$t.'</td></tr>';
		$msg.='<tr><td>Shipping Address</td><td align="left" colspan="4">'.$inp['person']."<br><br>".nl2br($inp['address'])."<br>".$inp['city']." ".$inp['pincode']."<br>Contact No. ".$inp['ship_phone']."</td></tr>";
		$msg.='</table>';
		$msg.="</td></tr></table>";
		$msg.="<br><br>To make sure you know what's happening with your order, we'll send you a series of email including:";
		$msg.='<ul><li>An update when your order is processed</li><li>A note to let you know your order has shipped</li></ul>';
		$msg.='For any comments/ compliments/ suggestion/ feedback, please write to us at support@viabazaar.in';
		$this->load->library("email");
		$config['mailtype']="html";
		$this->email->initialize($config);
		$this->email->from("support@viabazaar.in","ViaBazaar");
		$this->email->to($inp['email']);
		$this->email->subject("Your order summary");
		$this->email->message($msg);
		$this->email->send();
		if($this->session->userdata("specialcot")!=false)
			$this->dbm->donespecialcot($this->session->userdata("specialcot"));
		$this->session->unset_userdata("specialcot");
//		echo $inp['email'];
//		echo $this->email->print_debugger();
		$this->load->view("index",$data);
//		}
//		else
//		$this->checkout();
	}
	
	function couponen()
	{
		if($this->session->userdata("user")==false)
			redirect("");
		$this->load->library("cart");
		$coupon=$this->input->post("coupon");
		$rowid=$this->input->post("id");
		$cp=$this->dbm->getcoupon($coupon);
		if(empty($cp))
			die("0");
		$flag=false;
		foreach($this->cart->contents() as $item)
			if($item['rowid']==$rowid){$flag=true;break;}
		if($flag==false)
			die("0");
		$citem=$item;
		$item=$this->dbm->getitemdetails($citem['id']);
		if($cp['used']==1)
			die("1");
		if($item['brandid']==$cp['brandid'] && $cp['catid']==$item['catid'])
		{
			foreach($this->cart->contents() as $ci){
				if($this->cart->has_options($ci['rowid']))
				{
					$o=$this->cart->product_options($ci['rowid']);
					$a=$o['couponid'];
					if($a==$coupon)
						die("4");
				}
			}
			$up=array("rowid"=>$rowid,"qty"=>0);
			$this->cart->update($up);
			unset($citem['rowid']);
			$citem['options']['coupon']=$cp['value'];
			$citem['options']['couponid']=$cp['id'];
			$this->cart->insert($citem);
			$this->dbm->savecart();
			die("2");
		}
		die("3");
	}
	
	/**
	 * Checkout page
	 * 
	 * Loads cart items and price.
	 * Shows form for checkout.
	 */
	function viewcart()
	{
		if($this->session->userdata("user")==false)
			redirect("");
		$this->load->library("form_validation");
		$this->load->library("cart");
		$data['user']=$user=$this->session->userdata("user");
		$data['page']="checkout";
		$data['items']=$items=$this->cart->contents();
		$data['userdb']=$this->dbm->getuserbyid($user['userid']);
		if($data['userdb']['mobile']==0)
			$data['userdb']['mobile']="";
		if(count($items)==0)
		{
			$data['info']=array("Checkout","No items in cart. Please add items to check out");
			$data['page']="info";
		}
		$itemsquantity=$this->dbm->getitemsquantity($items);
		$data['items']=array();
		foreach($items as $item)
		{
			if($itemsquantity[$item['id']]['quantity']<($item['qty']+$itemsquantity[$item['id']]['available']) || $itemsquantity[$item['id']]['na']==true)
			{
				$item['na']=true;
				$data['naitem']=true;
			}
			if($this->cart->has_options($item['rowid'])==true)
			{
				$opt=$this->cart->product_options($item['rowid']);
				if(isset($opt['coupon']))
					$item['coupon']=$opt['coupon'];
			}
			$item['orgprice']=$itemsquantity[$item['id']]['orgprice'];
			$item['shipsin']=$itemsquantity[$item['id']]['shipsin'];
			$item['pic']=$itemsquantity[$item['id']]['pic'];
			array_push($data['items'],$item);
		}		
		$data['title']="Checkout";
		$this->load->view("index",$data);
	}
	
	function nomobile()
	{
		setcookie("nomobile","yes",0,"/");
		redirect("");
	}
	
	function gomobile()
	{
		setcookie("nomobile","yes",time()-439203,"/");
		redirect("");
	}
	
	function checkout($step="step1")
	{
		$user=$this->session->userdata("user");
		if(!$user)
		{
			$user=$this->dbm->getuserbyemail("guest@localcircle.in");
			$user['corp']=$this->dbm->getcorpname($user['corpid']);
		}
		$items=$this->cart->contents();
		if(empty($items))
		{
			$data['page']="info";
			$data['info']=array("Shopping cart empty!","There are no products in your cart to make checkout");
			$this->load->view("index",$data);
			return;
		}
		foreach($items as $item)
		{
			$itemdetails=$this->db->query("select i.quantity,i.available,i.live,d.startdate,d.enddate,d.publish from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?",$item['id'])->row_array();
			if($itemdetails['quantity']<=$itemdetails['available'] || $itemdetails['quantity']+$item['qty']<$itemdetails['available'] || $itemdetails['live']!=1 || $itemdetails['startdate']>time() || $itemdetails['enddate']<time() || $itemdetails['publish']!=1)
			{
				$data['page']="info";
				$data['info']=array("Product not available now!","Sorry, the product '<b style='color:red'>{$item['name']}</b>' is not available now. Please remove the product from your cart and continue checkout'");
				$this->load->view("index",$data);
				return;
			}
		}
		if($this->cart->total()<MIN_ORDER_AMOUNT)
		{
			$data['info']=array("Minimum Order Amount Required","Sorry, we are not able to process your checkout. A minimum order of Rs ".MIN_ORDER_AMOUNT." is required. Please fill in more items into the cart to do the checkout.");
			$data['page']="info";
			$this->load->view("index",$data);
			return;
		}
		
/*		if(!$user['verified'])
		{
			$this->session->set_userdata("vredir",site_url("checkout"));
			redirect("getverified");
		}*/
		if($user!==false && $step=="step1")
			$step="step2";
		if($step=="step1")
		{
			$data['page']="new/checkout";
		}
		elseif($step=="step2")
		{
			$this->jxshowcart();return;
			$cities=$this->dbm->getshipcity($this->cart->contents());
			if($cities!=false)
				$data['cities']=$cities;
			if(empty($cities) && is_array($cities))
			{
				$data['info']=array("Oops! Shipment location Error!","Sorry, we are not possible to process your checkout. There some products in your cart which are not possible to ship to a particular location.");
				$data['page']="info";
				$mobile=$this->checkmobile();
				$this->load->view($mobile?"wap":"index",$data);return;
			}
			$user=$this->session->userdata("user");
			if($user)
				$data['addrdet']=$this->db->query("select address,state,city,pincode,landmark,telephone from king_users where userid=?",$user['userid'])->row_array();
			$data['page']="new/checkout2";
		}
		elseif($step=="step3")
		{
			if(!$_POST)
				show_404();
			if($this->input->post("shipbillcheck"))
			{
				$params=array("person","address","state","city","landmark","pincode","mobile","email","country");
				foreach($params as $p)
					$_POST["bill_$p"]=$this->input->post("$p");
			}
			if($this->dbm->checkordercoupon())
			{
				$coupon=$this->session->userdata("coupon");
				$data['page']="info";
				$data['info']=array("Coupon already used!","You have already used this coupon {$coupon['code']} in your previous orders.<br>Please remove coupon and make your checkout.");
				$mobile=$this->checkmobile();
				$this->load->view($mobile?"wap":"index",$data);
				return;
			}
			if($this->dbm->checkcod($this->input->post("pincode")) && $this->dbm->is_cod_available())
				$data['codavailable']=true;
			$data['cod']=COD_CHARGES;
			$data['page']="new/checkout3";
			$step="final";
		}
		if($step=="final")
		{
			if(!$_POST)
				show_404();
			$this->process_checkout();return;
		}
		else
			show_404();
		$mobile=$this->checkmobile();
		$this->load->view($mobile?"wap":"index",$data);
	}
	
	private function process_checkout()
	{
		$mobdet=$this->checkmobile();
		$this->load->library("ebs");
		$items=$this->cart->contents();
		if(empty($items))
			show_error("No items in cart");
		if($this->input->post("paytype")=="cod" && !$this->dbm->is_cod_available())
      show_error("Error! Unable to complete your request. Please try again later.");
		
		$user=$this->session->userdata("user");
		$paydata=$this->dbm->neworder();
		if($this->session->userdata("coupon"))
		{
			$coupon=$this->session->userdata("coupon");
			$this->db->query("insert into king_used_coupons(coupon,transid) values(?,?)",array($coupon['code'],$paydata['reference_no']));
		}
		$this->session->unset_userdata("coupon");
		$user=$this->session->userdata("user");
		if($user)
		{
			$addrdet=array();
			$inps=array("bill_address","bill_city","bill_state","bill_pincode","bill_landmark","bill_telephone","bill_mobile","bill_country");
			foreach($inps as $inp)
				$addrdet[]=$_POST[$inp];
			$addrdet[]=$user['userid'];
			$this->db->query("update king_users set address=?,city=?,state=?,pincode=?,landmark=?,telephone=?,mobile=?,country=? where userid=?",$addrdet);
		}
		
		
		
		//clear checkoutpage sessions if any 
		$this->_clear_checkoutsess();
		
		if($this->input->post("paytype")=="cod")
		{
			$umail=$this->dbm->authorder($paydata['reference_no'],$paydata['amount']>MIN_AMT_FREE_SHIP?$paydata['amount']:$paydata['amount'],1);
			$data['info']=array("Order Placed","Your order is placed. We will send you product at the earliest.<br>Please take a note of your transaction ID : {$paydata['reference_no']}<br>You can contact our customer support to know status of your order.<br>Thank you for shopping with us!<br><br>$umail");
			if(!$this->session->userdata("user")) 
				$data['info'][1].="<br><br>This guest checkout has been linked to your account. Please check your mail";
			$data['thankyou']=true;
			$data['page']="info";
			$data['ga_data']=$this->dbm->gen_ga_data($paydata['reference_no']);
			$this->load->view($mobdet?"wap":"index",$data);return;
		}
		if($paydata['amount']==0)
		{
			$umail=$this->dbm->authorder($paydata['reference_no'],$paydata['amount'],0);
			$data['thankyou']=true;
			$data['info']=array("Order Placed","Your order is placed. We will send you product at the earliest.<br>Please take a note of your transaction ID : {$paydata['reference_no']}<br>You can contact our customer support to know status of your order.<br>Thank you for shopping with us!<br><br>$umail"); 
			$data['page']="info";
			$this->load->view($mobdet?"wap":"index",$data);return;
		}
		$data['page']="echo";
		$data['echo']="<div align='center' style='text-align:left;height:200px;padding:20px 0px;'><h1>Please wait while we transfer you to Payment Gateway...</h1>";
		$data['echo'].=$this->ebs->getform($paydata);
		$data['echo'].='</div><script>' .
				'$(function(){' .
				'window.setTimeout(function(){$("#payform").submit()},100);' .
				'});' .
				'</script>';
		$this->load->view($mobdet?"wap":"index",$data);
	}
	
	function processPayment()
	{
		$dr=$this->input->get_post("DR");
		if(!$dr)
			show_404();
		$this->load->library("ebs");
		$resp=$this->ebs->getresponse($dr);
		$paid=$this->dbm->updatetrans($resp);
		if($paid)
		{
			$umail=$this->dbm->authorder($resp['MerchantRefNo'],$resp['Amount'],0);
			$data['page']="info";
			$data['thankyou']=true;
			$data['info']=array("Payment Successfull!","Thanks for shopping with us. Please take note of your transaction ID : {$resp['MerchantRefNo']}<br><script>window.setTimeout(function(){location='".site_url("dashboard")."';},4000);</script><br><br>$umail");
			$data['ga_data']=$this->dbm->gen_ga_data($resp['MerchantRefNo']);
			$this->cart->destroy();
		}
		else
		{
			$data['page']="info";
			$data['info']=array("Payment Failed!","Sorry! we are unable to process the payment. Please contact customer support.");
		}
		$this->load->view("index",$data);
	} 
	
	function checkout_old()
	{
		if($this->session->userdata("user")==false)
			redirect("");
		$this->load->library("form_validation");
		$this->load->library("cart");
		$user=$this->session->userdata("user");
		$fran=$this->session->userdata("fran_auser");
		if($fran)
		{
			$user['balance']=$this->db->query("select balance from king_franchisee where id=?",$fran['id'])->row()->balance;
			$this->session->set_userdata("user",$user);
		}
		$data['user']=$user=$this->session->userdata("user");
		$data['page']="checkout_step2";
		$data['items']=$items=$this->cart->contents();
		$data['userdb']=$this->dbm->getuserbyid($user['userid']);
		$data['addresses']=$this->dbm->getaddresses($user['userid']);
		if($data['userdb']['mobile']==0)
			$data['userdb']['mobile']="";
		if(count($items)==0)
		{
			$data['info']=array("Checkout","No items in cart. Please add items to check out");
			$data['page']="info";
		}
		$save=$t=$payable=$comm=0;
		$itemsquantity=$this->dbm->getitemsquantity($items);
		$data['items']=array();
		foreach($items as $item)
		{
			if($itemsquantity[$item['id']]['quantity']<($item['qty']+$itemsquantity[$item['id']]['available']) || $itemsquantity[$item['id']]['na']==true)
			{
				redirect("viewcart");
				$item['na']=$na=true;
				$data['naitem']=true;
			}
			if($this->cart->has_options($item['rowid'])==true)
			{
				$opt=$this->cart->product_options($item['rowid']);
				if(isset($opt['coupon']))
					$item['coupon']=$opt['coupon'];
			}
			$item['orgprice']=$itemsquantity[$item['id']]['orgprice'];
			$item['pic']=$itemsquantity[$item['id']]['pic'];
			$item['shipsin']=$itemsquantity[$item['id']]['shipsin'];
			$item['agentcom']=$itemsquantity[$item['id']]['agentcom'];
			array_push($data['items'],$item);
			$save+=($item['orgprice']-$item['price'])*$item['qty'];
			$t+=$item['orgprice']*$item['qty'];
			$payable+=$item['price']*$item['qty'];
			$comm=$item['agentcom']*$item['qty'];
		}
		if($t!=0)
		$data['per']=ceil($save/$t*100);
		$data['save']=$save;
		$data['total']=$t;
		$data['payable']=$payable;
		if($this->session->userdata("specialcot")==false)
		$data['comm']=$comm;
		else
		$data['comm']=0;
		$fran=$this->session->userdata("fran_auser");
		if($fran)
		{
			$fran_balance=$this->db->query("select balance from king_franchisee where id=?",$fran['id'])->row()->balance;
			if($payable>$fran_balance)
			{
				$data['info']=array("Not enough balance","Insufficient balnce in your account to do this checkout. <br>Amount needed : Rs $t<br>Please contact support/callcenter");
				$data['page']="info";
				$this->load->view("index",$data);
				return;
			}
		}
		if(isset($user['aid']))
		{
			$this->load->library("agentapi");
			$auth=$this->agentapi->authenticate(array("username"=>$user['via_username'],"password"=>$this->encrypt->decode($user['via_password'],"ViMaL-VB-ViA-AiRlInE")));
			if(!isset($auth['AuthenticateUser']['Deposit']) || !isset($auth['AuthenticateUser']['Authenticated']) || $auth['AuthenticateUser']['Authenticated']!="Y")
			{
				$this->session->unset_userdata("user");
				$data['page']="info";
				$data['info']=array("Authentication Failed","Please <a href='".site_url("agent")."' style='color:blue'>signin</a> again");
				$this->load->view("index",$data);
				return;
			}
			$this->session->set_userdata("via_sid",$auth['AuthenticateUser']['SessionId']);
			$data['agent_balance']=$bal=$auth['AuthenticateUser']['Deposit'];
			if($bal<($t-$comm)*100)
			{
				$data['page']="info";
				$data['info']=array("Insufficient Balance","Sorry, you don't have enough balance in your account to do this transaction. Please contact VIA support.<br>Your current balance : Rs ".sprintf("%.2f",$bal/100));
				$this->load->view("index",$data);
				return;
			}
		}
		$data['title']="Checkout";
		$this->load->view("index",$data);
	}
	
	private function proc_coupon($coupon,$amount,$type,$email)
	{
		$user=$this->session->userdata("user");
		$coupon=trim($coupon);
		$c=$this->db->query("select * from king_coupons where code=? and status=0",array($coupon))->row_array();
		if(empty($c))
			return array();
		if($c['userid']!=0 && $c['userid']!=$user['userid'])
			return array();
		return array("mode"=>$c['mode'],"brandid"=>$c['brandid'],"catid"=>$c['catid'],"type"=>$c['type'],"min"=>$c['min'],"value"=>$c['value'],"expires"=>$c['expires'],"code"=>$c['code'],"referral"=>$c['referral'],"gift_voucher"=>$c['gift_voucher']);
	}
	
	function jx_useraccheck()
	{
		if(!$_POST)
			die;
		$em=$this->input->post("em");
		if($this->db->query("select 1 from king_users where email=?",$em)->num_rows()==0)
			echo "no";
		else
			echo "yes";
	}
	
	function jx_freesamples($min)
	{
		$data['fss']=$this->dbm->getfreesamples($min);
		$data['fsconfig']=$this->dbm->getfsconfig($min);
		$this->load->view("body/freesamples",$data);
	}
	
	function jx_savefs()
	{
		
		// freesample selected to process for addition or removing from the list 
		$fs_id = $this->input->post('fsids');
		
		$output = array();
		
		$fsselected_list = $this->session->userdata("fsselected");
		
		// convert to array
		$fsselected_list_arr = explode(',',$fsselected_list);
		$fsselected_list_arr = array_filter($fsselected_list_arr);
		$this->session->set_userdata("fsselected",implode(',',$fsselected_list_arr));
		
		$total_fsselected = count($fsselected_list_arr);
		
		
		
		
		
		
		 
		
		// iterate fs to new array - need for fs removal.
		$new_fslist = array();
		$is_remove_fs = 0;
		foreach($fsselected_list_arr as $s_fsid){
			if($s_fsid == $fs_id){
				$is_remove_fs = 1;
			}else{
				array_push($new_fslist,$s_fsid);
			}
		}
		
		
		
		$update_selected_fs = 0;
		$total_cartval = ($this->session->userdata("total_cartval"));
		if(!$total_cartval)
			$total_cartval = 0;
		
		$total_allowed = 0;
		$fsconfig=$this->dbm->getfsconfig($total_cartval);
		if(isset($fsconfig['limit'])){
			$total_allowed = $fsconfig['limit'];
		}
		
		 
		// if set for removal then update session variable 
		if($is_remove_fs){
			$total_fsselected--;
			$this->session->set_userdata("fsselected",implode(',',$new_fslist));
			$output['status'] = 2;
			
			$remaining = ($total_allowed-$total_fsselected);
			if($remaining){
				if($remaining == $total_allowed){
					$output['message'] = "You can choose ".$remaining." Free samples, Please Increase your cart value to avail more free samples " ;
				}else{
					$output['message'] = "You can still choose ".$remaining." Free samples, Please Increase your cart value to avail more free samples " ;
				}
			}else{
				$output['message'] = "You have choosed <b>".$total_allowed."</b> Free samples, Please Increase your cart value to avail more free samples " ;
			}
			
			
		}else{
			
			
			
			
			$sql  = "select * from king_freesamples where id = $fs_id ";
			$fs_sellist = $this->db->query($sql)->row_array();
			
			 
			
			if($total_allowed){
				if($total_allowed > $total_fsselected){
					if($fs_sellist['min'] <= $total_cartval){
						array_push($fsselected_list_arr,$fs_id);
						
						
						$total_fsselected++;
						$this->session->set_userdata("fsselected",implode(',',$fsselected_list_arr));
						$output['status'] = 1;
						
						$remaining = ($total_allowed-$total_fsselected); 
						if($remaining){
							
							if($remaining == $total_allowed){
								$output['message'] = "You can choose ".$remaining." Free samples, Please Increase your cart value to avail more free samples " ;
							}else{
								$output['message'] = "You can still choose ".$remaining." Free samples, Please Increase your cart value to avail more free samples " ;
							}
							
						}else{
							$output['message'] = "You have choosed <b>".$total_allowed."</b> Free samples, Please Increase your cart value to avail more free samples " ;
						}
						
						
					}else{
						$output['status'] = 0;
						$output['message'] = "Please Increase your cart value to avail this free sample";
					}
					
				}else{
					$output['status'] = 0;
					$output['message'] = "You can avail only <b>".$total_allowed."</b> Free samples, Please Increase your cart value to avail more free sample " ;
				}
			}else{
				$output['status'] =0;
				$output['message'] = "Please Increase your cart value to avail this free sample";
			}
		}
		
		$output['total_selected'] = $total_fsselected ; 
		echo json_encode($output); 
		
	}
	
	function jx_checkoutstat(){
		
		if(isset($_POST['action'])){
			if($_POST['action'] == 'check')
				echo $this->session->userdata("checkoutstat");
			exit;
		}else{
			
			if(isset($_POST['status'])){
				$this->session->set_userdata("checkoutstat",$_POST['status']);
				echo $this->session->userdata("checkoutstat");
			}else{
				echo $this->session->unset_userdata("checkoutstat");
			}
		}
	}
	
	function jx_checkcodpin()
	{
		if(!$_POST)
			die;
		$avail=$this->dbm->checkcod($this->input->post("pin"));
		$echo=$this->input->post("echo");
		if(!$echo)
		{
		if($avail)
			echo "Cash on delivery is available in your location";
		else 
			echo "Cash on delivery is not available in your location";
		}else 
		{
			$ret='<div class="payselect" style="font-weight:bold;">

<div class="paysel"><label><input type="radio" name="paytype" value="card" checked class="paymetho dfmetho"> Credit Card / Debit Card</label>
<div style="margin-left:25px;margin-top:5px;"><img src="'.IMAGES_URL.'mastercard.png"><img src="'.IMAGES_URL.'visa.png"></div>
</div>

<div class="paysel" ><label><input type="radio" name="paytype" value="net" class="paymetho"> Internet Banking<div style="padding-left:25px;font-size:80%;color:#888;">Select your bank and pay online</div></label>
<div style="margin-left:25px;margin-top:5px;"><img src="'.IMAGES_URL.'banks.jpg"></div>
</div>';

if($avail)
$ret.='<div class="paysel">
	<label><input type="radio" name="paytype" value="cod" class="paymetho codmetho"> Cash on Delivery<div style="padding-left:25px;font-size:80%;color:#888;">COD available to your location.<br /> <div style="color: #cd0000;margin-top: 3px;">COD Charges Rs '.COD_CHARGES.' applicable </div></div></label>
</div>';
$ret.='</div>';
			echo $ret;
		}
	}
	
	function jx_sugst_search()
	{
		if(!$_POST)
			die;
		$q=trim($this->input->post("q"));
		if(strlen($q)<2)
			die("");

		$menu=false;
		if($this->input->post("menu"))
		{
			$m=$this->db->query("select id from king_menu where url=?",$this->input->post("menu"))->row_array();
			if(!empty($m))
				$menu=$m['id'];
		}
			
		$keys=array();
			
		$srchs=$this->db->query("select b.name from king_brands b join king_deals d on d.brandid=b.id and ".time()." between d.startdate and d.enddate and d.publish=1".($menu?" and (d.menuid=$menu or d.menuid2=$menu)":"")." where b.name like ? limit 10","$q%")->result_array();
		foreach($srchs as $s)
		{
			$n=$s['name'];
			$f=substr($n,0,stripos($n,$q))."<b>".substr($n,stripos($n,$q),strlen($q))."</b>".substr($n,stripos($n,$q)+strlen($q));
			if(!empty($f) && !in_array($f, $keys))
				$keys[]=$f;
		}
			
		$srchs=$this->db->query("select c.name from king_categories c join king_deals d on d.catid=c.id and ".time()." between d.startdate and d.enddate and d.publish=1".($menu?" and (d.menuid=$menu or d.menuid2=$menu)":"")." where c.name like ? limit 10","$q%")->result_array();
		foreach($srchs as $s)
		{
			$n=$s['name'];
			$f=substr($n,0,stripos($n,$q))."<b>".substr($n,stripos($n,$q),strlen($q))."</b>".substr($n,stripos($n,$q)+strlen($q));
			if(!empty($f) && !in_array($f, $keys))
				$keys[]=$f;
		}
		$srchs=$this->db->query("select i.name from king_search_index si join king_dealitems i on i.id=si.itemid".($menu?" join king_deals d on d.dealid=i.dealid and (d.menuid=$menu or d.menuid2=$menu)":"")." where si.name like ? limit 10",array("%$q%"))->result_array();
		foreach($srchs as $s)
		{
			$n=$s['name'];
//			$f=stristr($n,$q);
//			$l=strpos($f, " ",strlen($q)+1);
//			$l=strpos($f," ",$l+1);
//			$l=strpos($f," ",$l+1);
//			if(strlen($f)>$l-1)
//			$f=substr($f, 0,$l);
			$f=substr($n,0,stripos($n,$q))."<b>".substr($n,stripos($n,$q),strlen($q))."</b>".substr($n,stripos($n,$q)+strlen($q));
			if(!empty($f) && !in_array($f, $keys))
				$keys[]=$f;
		}
		foreach($keys as $k)
			echo '<div><a href="javascript:void(0);" onclick="$(\'#searchbox .srchinp\').val(\''.strip_tags($k).'\');$(\'#searchbox\').submit();" class="sug_s_links">'.$k.'</a></div>';
	}
	
	function jx_clearcoupon()
	{
		$this->session->unset_userdata("coupon");
	}
	
	private function apply_coupon($couponid)
	{
		$coupon=$this->proc_coupon($couponid,$this->cart->total(),"check","asdasf@dssad.com");
		if(!empty($coupon))
		{
			if($coupon['expires']<time())
				$this->session->set_flashdata("couponmsg","Invalid coupon : coupon expired!");
			elseif(!$this->dbm->checkcouponmin($coupon)){
				$this->session->set_flashdata('coupon_errorcode',1111);
				$this->session->set_flashdata("couponmsg","Coupon is valid only for a minimum amount of Rs {$coupon['min']}");
			}elseif(($user=$this->session->userdata("user")) && $this->dbm->checkusedcoupon($user['userid'],$coupon['code']))
				$this->session->set_flashdata("couponmsg","You have already used this coupon!");
			elseif($coupon['referral']!=0 && !$this->session->userdata("user"))
				$this->session->set_flashdata("couponmsg","Please sign in/sign up to use this coupon");
			elseif($coupon['referral']!=0 && $this->session->userdata("user") && !$this->dbm->firstorder())
				$this->session->set_flashdata("couponmsg","This coupon is valid only for your first order");
			else
				$this->session->set_userdata("coupon",$coupon);
		}else{
			$this->session->set_flashdata("couponmsg","Invalid Coupon $couponid");
		}
	}
	
	function jxcoupon()
	{
		$mobdet=$this->checkmobile();
		$couponid=$this->input->post("coupon");
		if($couponid)
		{
			$this->apply_coupon($couponid);
		}else
			$this->session->set_flashdata("couponmsg","Invalid Coupon $coupon");
		if($mobdet)
		{
			$data['echo']='<form id="coupform" action="'.site_url("checkout/step3").'" method="post">';
			foreach($_POST as $n=>$v)
				$data['echo'].='<input type="hidden" name="'.$n.'" value="'.htmlspecialchars($v).'">';
			$data['echo'].='<h2>Please wait</h2>';
			$data['echo'].='<script>$(function(){$("#coupform").submit();});</script>';
			$data['page']="echo";
			$this->load->view("wap",$data);
		}
	}
	
	/**
	 * Show cart
	 * 
	 * AJAX request to show cart.
	 * Get cart items and show details including total price.
	 */
	function jxshowcart()
	{
//		if($this->session->userdata("user")==false)
//			redirect("");
		$this->load->library("cart");
		$data['page']="cart";
		$this->load->view("index",$data);
	}
	
	function shoppingcart()
	{
		$this->jxshowcart();
	}
	
	function jx_extendbp()
	{
		$user=$this->checkauth();
		if(!$_POST)
			die;
		$bpid=$this->input->post("bpid");
		$cws_rw=explode(",",$this->input->post("cws"));
		$emails=explode(",",$this->input->post("emails"));
		$this->dbm->extendbuyprocess($bpid,$cws_rw,$emails);
	}
	
	function jx_fdback()
	{
		$comment=$this->input->post("comment");
		$this->load->library("form_validation");
		$email=$this->input->post("email");
		if($this->form_validation->valid_email($email))
		{ 
			$this->db->query("insert into king_feedback(comment,email,time) values(?,?,?)",array($comment,$email,time()));
			mail("feedback@snapittoday.com","Feedback","From : $email\n\nMessage: $comment");
		}
	}
	
	/**
	 * Show number of cart items
	 * 
	 * AJAX request to update number of cart items in header link.
	 * Echoes number of cart items so that it will be updated in cart header link within brackets by jQuery.
	 * @return int
	 */
	function jxshownocartitems()
	{
//		if($this->session->userdata("user")==false)
//			redirect("");
		$this->load->library("cart");
		$ret['items']=$this->cart->total_items();
		$ret['total']=number_format($this->dbm->calc_cart_total());
		echo json_encode($ret);
	}
	
	function loadfavs()
	{
		$user=$this->session->userdata("user");
		if(!$user)
			redirect("shoppingcart");
		$favs=$this->dbm->getallfavids();
		if(empty($favs))
			redirect("shoppingcart");
		$rbuys=$favs;
		$cart_data=array();
		foreach($rbuys as $rbuy)
		{
			$r_id=$rbuy;
			$r_qty=1;
			$r_item=$this->dbm->getitemdetails($r_id);
			if(empty($r_item) || $r_item['enddate']<time() || $r_item['live']!=1 || $r_item['quantity']<=$r_item['available'])
				continue;
			$r_bpid=$this->dbm->startbuyprocess($r_qty,array(),0,$r_item,array(),array(),true);
			$opts['bpid']=$r_bpid;
			$opts['bpuid']=$this->db->query("select id from king_buyprocess where bpid=? and userid=?",array($r_bpid,$user['userid']))->row()->id;
			$cart_data[]=array("id"=>$r_id,"name"=>$r_item['name'],"price"=>$r_item['price'],'qty'=>$r_qty,"options"=>$opts);
		}
		foreach($cart_data as $c)
		{
			foreach($this->cart->contents() as $i)
				if($i['id']==$c['id'])
					$this->cart->update(array("rowid"=>$i['rowid'],"qty"=>0));
			$this->cart->insert($c);
		}
		$this->dbm->savecart();
		//set is_favsloaded status 
		$this->session->set_userdata('is_favsloaded',1);
		
		redirect("shoppingcart");
	}
	
	function reorder($transid)
	{
		$user=$this->auth();
		$orders=$this->db->query("select o.itemid,o.quantity,i.name,i.price from king_orders o join king_dealitems i on i.id=o.itemid and i.live=1 and i.quantity>i.available join king_deals d on d.dealid=i.dealid and ? between d.startdate and d.enddate and d.publish=1 where o.transid=? and o.userid=?",array(time(),$transid,$user['userid']))->result_array();
		if(empty($orders))
		{
			$data['page']="info";
			$data['info']=array("Products not available","We are sorry. Products in your transaction are not available now.");
			$this->load->view("index",$data);
			return;
		}
		$cart=array();
		$ids=array();
		foreach($orders as $o)
		{
			$r_id=$o['itemid'];
			$r_qty=$o['quantity'];
			$r_item=$this->dbm->getitemdetails($o['itemid']);
			if($r_qty>$r_item['max_allowed_qty'])
				$r_qty=$r_item['max_allowed_qty'];
			$r_bpid=$this->dbm->startbuyprocess($r_qty,array(),0,$r_item,array(),array(),true);
			$opts['bpid']=$r_bpid;
			$opts['bpuid']=$this->db->query("select id from king_buyprocess where bpid=? and userid=?",array($r_bpid,$user['userid']))->row()->id;
			$cart[]=array("id"=>$r_id,"name"=>$r_item['name'],"price"=>$r_item['price'],'qty'=>$r_qty,"options"=>$opts);
			$ids[]=$r_id;
		}
		foreach($this->cart->contents() as $c)
			if(in_array($c['id'], $ids))
				$this->cart->update(array("rowid"=>$c['rowid'],"qty"=>0));
		$bool=$this->cart->insert($cart);
		$this->dbm->savecart();
		redirect("shoppingcart");
	}
	
	/**
	 * Add to cart
	 * 
	 * AJAX request to add an item into cart.
	 * Gets item id & quantity from user post input and adds the item into shopping cart.
	 */
	function jxaddtocart($m=false)
	{
		$user=$this->session->userdata("user");
		if(!$user)
		{
			$user=$this->dbm->getuserbyemail("guest@localcircle.in");
			$user['corp']=$this->dbm->getcorpname($user['corpid']);
		}
		$this->load->library("cart");
		
    	$rbuys=$this->input->post("rbuys");
    	if(!empty($rbuys))
    	{
		$rbuys=explode(",",$this->input->post("rbuys"));
		$cart_data=array();
		foreach($rbuys as $rbuy)
		{
			list($r_id,$r_qty)=explode("-",$rbuy);
			$r_item=$this->dbm->getitemdetails($r_id);
			if($r_qty>$r_item['max_allowed_qty'])
				$r_qty=$r_item['max_allowed_qty'];
			$r_bpid=$this->dbm->startbuyprocess($r_qty,array(),0,$r_item,array(),array(),true);
			$opts['bpid']=$r_bpid;
			$opts['bpuid']=$this->db->query("select id from king_buyprocess where bpid=? and userid=?",array($r_bpid,$user['userid']))->row()->id;
      $name=preg_replace('/[^a-zA-Z0-9 ]/','',$r_item['name']);
			$cart_data[]=array("id"=>$r_id,"name"=>$name,"price"=>$r_item['price'],'qty'=>$r_qty,"options"=>$opts);
		}
		foreach($cart_data as $c)
		{
			foreach($this->cart->contents() as $i)
				if($i['id']==$c['id'])
					$this->cart->update(array("rowid"=>$i['rowid'],"qty"=>0));
			$this->cart->insert($c);
		}
		}
    	
    	
    	if($this->input->post("item")==false || $this->input->post("qty")==false)
			die("XX");
		$itemid=$this->input->post("item");
		if(!$itemid)
			die ("3");
		$qty=$this->input->post("qty");
		$bpid=$this->input->post("bpid");
		$sizing=$this->input->post("size");
		$opts=array();
		if($sizing!="0")
			$opts=array("sizing"=>$sizing);
		$gift_email=$this->input->post("gift_email");
		if($gift_email)
			$opts=array("email"=>$gift_email);
		$itemdetails=$this->dbm->getitemdetails($itemid);
		
		if($qty>$itemdetails['max_allowed_qty'])
			$qty=$itemdetails['max_allowed_qty'];
		
//		$itemdetails['name']=str_replace("/", " ", $itemdetails['name']);
//		$itemdetails['name']=str_replace("(", " ", $itemdetails['name']);
//		$itemdetails['name']=str_replace(")", " ", $itemdetails['name']);
		
		if(!$bpid)
		{
			$uids=$emails=array();
			$buyers=$qty;
			
			$slots=unserialize($itemdetails['slots']);
	     	$nslots=array();
	     	$nslotprice=array();
	     	if(is_array($slots))
	     	foreach($slots as $sno=>$srs)
	     	{
	     		$nslots[]=$sno;
	     		$nslotprice[]=$srs;
	     	}
			foreach($nslots as $si=>$ns)
			{
				if($buyers<$ns)
					break;
			}
			$slotprice=$nslotprice[$si];
			if($slotprice!=0)
			$refund=$item['price']-$slotprice;
			else
			$refund=0;
			
			$bpid=$this->dbm->startbuyprocess($qty,$uids,$refund,$itemdetails,$emails,array(),true);
		}
		
		if($bpid)
		{
			$bpuid=$this->db->query("select id from king_buyprocess where bpid=? and userid=?",array($bpid,$user['userid']))->row()->id;
			$bp=$this->db->query("select quantity,refund from king_m_buyprocess where id=?",$bpid)->row();
			$opts["bpid"]=$bpid;
			$opts['bpuid']=$bpuid;
		}
		else {$bp->refund=0;$bp->quantity=0;}
		$refund=$bp->refund;
		if($itemdetails==false)
			die("0");
		if($qty>$itemdetails['quantity'])
			die("1");
		$name=preg_replace('/[^a-zA-Z0-9 ]/',' ',$itemdetails['name']);
		$mark=0;
		$price=$itemdetails['price'];
		if($qty>=$bp->quantity)
			$price=$itemdetails['price']-$refund;
		$cart=array("id"=>$itemdetails['id'],'qty'=>$qty,"price"=>$price,"name"=>$name,"options"=>$opts);
		$flag=false;
		foreach($this->cart->contents() as $cartitem)
		{
			if($cartitem['id']==$itemid)
			{
				$flag=true;break;
			}
		}
		if($flag)
		{
			$up=array("qty"=>0,"rowid"=>$cartitem['rowid']);
			$this->cart->update($up);
			$up=array("id"=>$itemdetails['id'],'qty'=>$qty,'price'=>$price,'options'=>$opts,"name"=>$name);
			$this->cart->insert($up);
		
			$this->dbm->savecart();
			
			if($m)
			{
				if($this->session->userdata("user"))
					redirect(site_url("yourcart"),"refresh");
				redirect("checkout_inter","refresh");
			}
			die("2");
		}
		$ret=$this->cart->insert($cart);
		
		$this->dbm->savecart();
		
		if($m)
		{
			if($this->session->userdata("user"))
				redirect(site_url("yourcart"),"refresh");
			redirect("checkout_inter","refresh");
		}
		echo "3";
	}
	
	
	
	function jx_checkoutcond()
	{
		$output = array();
		 
		
		$cond = $this->input->post('cond');
		switch($cond)
		{
			case 'gc_stat' : if(isset($_POST['set_stat']))
								{
									$this->session->unset_userdata('gc_checkout_stat');
									$this->session->set_userdata('gc_checkout_stat',$this->input->post('set_stat'));
									$output['stat'] = $this->input->post('set_stat'); 
								}
								else
								{	
									$output['stat'] = $this->session->userdata('gc_checkout_stat');
								} 
								
								break;
			case 'gc_recp_det' : 
								if(isset($_POST['name']))
								{
									$gc_recp_det_arr = array(); 
									$gc_recp_det_arr['name'] = $this->input->post('name');
									$gc_recp_det_arr['email'] = $this->input->post('email');
									$gc_recp_det_arr['mobile'] = $this->input->post('mobile');
									$gc_recp_det_arr['msg'] = $this->input->post('msg');
									
									$this->session->unset_userdata('gc_recp_details');
									$this->session->set_userdata('gc_recp_details',$gc_recp_det_arr);
									$output['gc_recp_det'] = $gc_recp_det_arr;
								}
								else
								{
									$output['gc_recp_det'] = $this->session->userdata('gc_recp_details');
								}
								
								break;
		}
		
		 
		
		echo json_encode($output);
	}
	
	
	function _clear_checkoutsess()
	{
		
		//clear giftcard recp det from session 
		if($this->session->userdata('gc_recp_details'))
			$this->session->unset_userdata('gc_recp_details');
		 
		
		//clear giftcard selected status from session 
		if($this->session->userdata('gc_checkout_stat'))
			$this->session->unset_userdata('gc_checkout_stat');
		
		//clear fav products loaded status
		if($this->session->userdata('is_favsloaded'))
			$this->session->unset_userdata('is_favsloaded');
		
		//clear checkoutstatus, used in checkout page for form activity
		if($this->session->userdata('checkoutstat'))
			$this->session->unset_userdata('checkoutstat');
		
		//clear cart total, used in checkout page for handling fs activity
		if($this->session->userdata('total_cartval'))
			$this->session->unset_userdata('total_cartval');
		
	}
	
	function checkout_inter()
	{
		$data['page']="checkout_inter";
		$this->load->view("wap",$data);
	}
	
	function opsearch($q="")
	{
		if(empty($q))
			redirect("");
		
		$keyword=urldecode($q);	
			
		if(empty($keyword))
			$data['deals']=$deals=array();
		else
			$data['deals']=$deals=$this->dbm->searchdeals($keyword);
		if(count($deals)==1)
			redirect($deals[0]['url']);	
			
			
		$data['pagetitle']="Search products for keyword - $keyword";
		$data['page']="bycategory";
		$this->load->view("index",$data);
			
	}

	
	function search()
	{
		$keyword=$this->input->post("snp_q");
		
		$this->db->query("insert into king_search_log(query,time) values(?,?)",array($keyword,time()));
		
		$all=true;
		if($this->input->post("all") && $this->input->post("all")=="no")
		$all=false;

//		if(($data['user']=$this->session->userdata("user"))==false && !$this->checkmobile())
//			redirect("");
		
		if(empty($keyword))
			$data['deals']=$deals=array();
		else
			$data['deals']=$deals=$this->dbm->searchdeals($keyword,$all);
//		if(count($deals)==1)
//			redirect($deals[0]['url']);	
			
			
//		$data['cats']=$this->dbm->searchcats($keyword);
//		$data['brands']=$this->dbm->searchbrands($keyword);
		$data['pagetitle']="Search products for keyword - $keyword";
		$data['page']="bycategory";
		$data['search']=true;
		
		$mobile=$this->checkmobile();
		if($mobile)
		{
			$data['pagetitle']="Search Results";
			$data['page']="bycategory";
		}
		$this->load->view($mobile?"wap":"index",$data);
	}
	
	
	/**
	 * Show sale
	 * 
	 * Show sale details for given sale id.
	 * This includes brief item details too.
	 * 
	 * @param int $id Sale id
	 */
	function showsale($id)
	{
		if($this->session->userdata("user")==false)
			redirect("");		
					
		$dealdetails=$this->dbm->getdealdetails($id);
		$data['category']=$dealdetails[0]['category'];

			if(time()>$dealdetails[0]['startdate'])
				$duration=$dealdetails[0]['enddate']-time();
			else
				$duration=$dealdetails[0]['startdate']-time();
			$durmin=floor($duration/60);
			$durhr=floor($duration/60/60);
			$durday=floor($duration/24/60/60);
			$durmin=floor($durmin-$durhr*60);
			$durhr=floor($durhr-$durday*24);
			if($durday==0)
				$left="";
			else if($durday==1)
				$left="1 day ";
			else
				$left="$durday days ";
			if($durhr==0)
				$left.="<Br>{$durmin} mins";
			else if($durhr==1)
				$left.=$durmin!=0?"<br>{$durhr} hr <br>{$durmin} mins":"<br>{$durhr} hr";
			else
				$left.=$durmin!=0?"<br>{$durhr} hrs <br>{$durmin} mins":"<br>{$durhr} hrs";
			$dealdetails[0]['left']=$left;
		$dealdetails[0]['dealid']=$id;
		$data['prices']=$this->dbm->getminmaxprice($id);
		$data['dealdetails']=$dealdetails;
		if($dealdetails[0]['startdate']<time() && $dealdetails[0]['enddate']>time())
			$data['dealstatus']="active";
		else if($dealdetails[0]['startdate']>time())
			$data['dealstatus']="inactive";
		else
			$data['dealstatus']="expired";
		$data['comment']=$this->dbm->getlastcommentfordeal($id);
		$data['user']=$user=$this->session->userdata("user");
		$data['page']="sale";
		$data['title']=$dealdetails[0]['tagline'];
		$this->load->view("index",$data);
	}
	
	function fbthis($dealid,$itemid=NULL)
	{
//		echo "under dev";
		$data['user']=$this->session->userdata("user");
		if($data['user']==false)
			redirect("");
		$data['noheader']=true;
		$fb=new Facebook($this->fb_apikey,$this->fb_secretkey);
		$fb_user=$fb->get_loggedin_user();
		if(!$fb_user)
		{
			$data['info']=array("Facebook Sign in","Please sign in to your facebook account by clicking the below button<div align='center' style='padding-top:5px;'><fb:login-button length=\"long\" background=\"light\" size=\"xlarge\"></fb:login-button></div>");
			$data['fb_init']=true;
			$data['apikey']=$this->fb_apikey;
			$data['page']="info";
			$this->load->view("index",$data);return;
		}
		try{
		if($fb->api_client->users_hasAppPermission("publish_stream")!=true)
		{
			$data['fb_init']=true;
			$data['apikey']=$this->fb_apikey;
			$data['user']=$this->session->userdata("user");
			$data['info']=array("We need your permission","Via Bazaar will post this sale url. Please click below button to give permission. You can select which friends to post to in next step.<br><div align='center'><fb:prompt-permission perms=\"publish_stream\" next_fbjs=\"location='".site_url("fbthis/$dealid/$itemid")."'\"> Grant permission </fb:prompt-permission></div>");
			$data['page']="info";
			$this->load->view("index",$data);
			return;
		}
		}catch(Exception $e){
//			$fb->set_user(null,null);
//			$fb->expire_session();
//			$fb->clear_cookie_state();
			$this->load->helper('cookie');		
       $cookies = array('user', 'session_key', 'expires', 'ss');
       foreach ($cookies as $name) {
         delete_cookie($this->fb_apikey . '_' . $name);
         unset($_COOKIE[$this->fb_apikey . '_' . $name]);
       }
			redirect("fbthis/$dealid/$itemid");
			return;
		}
		if($fb_user)
		{
			$friends_get=$fb->api_client->friends_get();
			$friendstr=implode(",",$friends_get);
			$friends=$fb->api_client->users_getinfo($friendstr,"name");
		}
		else
			redirect("");
//		$action[0]=array('text'=>"Sale link","href"=>$url);
//		$fb->api_client->stream_publish("I liked this deal in ViaBazaar. {$msg} Please take a look at this : $url",null,json_encode($action),$fb_user);
		$this->selffbthis($fb_user,$dealid,$itemid);
		$data['friends']=$friends;
		$data['noheader']=true;
		$data['dealid']=$dealid;
		$data['itemid']=$itemid;
		$data['page']="fbthis";
		$this->load->view("index",$data);
	}
	
	function verifyh($hash)
	{
		$user=$this->session->userdata("user");
		if($user && $user['verified'])
			redirect("spotlight");
		$this->db->query("update king_users set verified=1 where md5(verify_code)=?",$hash);
		if($this->db->affected_rows()==1)
		{
			$user=$this->session->userdata("user");
			$redir=site_url("dashboard");
			if($this->session->userdata("vredir"))
			{
				$redir=$this->session->userdata("vredir");
				$this->session->unset_userdata("vredir");
			}
			$data['info']=array("Verified!","Your account is verified. Happy Shopping!<script>window.setTimeout(function(){location='$redir';},3000)</script>");
			if($user)
			{
				$user['verified']=1;
				$this->session->set_userdata("user",$user);
			}
		}
		else 
			$data['info']=array("Verification failed","Sorry, we are not able to verify your account");
		$data['page']="info";
		$this->load->view("index",$data);
	}
	
	function jx_remindme()
	{
		if(!$this->session->userdata("visited"))
			die;
		$id=$this->input->post("id");
		$email=$this->input->post("email");
		$mobile=$this->input->post("mobile");
		
		$live=$this->db->query("select live from king_dealitems where id=?",$id)->row();
		if(empty($live) || $live->live)
			die;
		if($this->db->query("select 1 from king_remindme where itemid=? and email=?",array($id,$email))->num_rows()==0)
			$this->db->query("insert into king_remindme(itemid,email,mobile,time) values(?,?,?,?)",array($id,$email,$mobile,time()));
	}
	
	function jx_getattention()
	{
		$a=$this->db->query("select * from king_announcements where enable=1 order by id desc limit 1")->row_array();
		if(!empty($a))
		{
			echo $a['text'];
			if(!empty($a['url']))
				echo '<a href="'.$a['url'].'">(Details)</a>';
		}
	}
	
	function jx_noannounce()
	{
		setcookie("noannounce","yes",0);
	}	
	
	function jx_lookingto()
	{
		$user=$this->checkauth();
		$inps=array("when","product","uids","emails");
		foreach($inps as $inp)
			$$inp=$this->input->post("$inp");
		$this->db->query("insert into king_lookingto(userid,product,uids,emails,time) values(?,?,?,?,?)",array($user['userid'],$product,$uids,$emails,time()));
	}
	
	function twthis($dealid,$itemid=NULL)
	{
//		echo "under dev";
		$data['user']=$user=$this->session->userdata("user");
		if($data['user']==false)
			redirect("");
		$data['noheader']=true;
		if($itemid!=null)
		{
			$item=$this->dbm->getitemdetails($itemid);
			if($item==false)
				die("Sale item not available. Sale might be expired");
			$dis=ceil(($item['orgprice']-$item['price'])/$item['orgprice']*100);
		}
		else
		{
			$deals=$this->dbm->getdealdetails($dealid);
			$deal=$deals[0];
			if($deals==false)
				die("Sale doesn't exist. Might be expired");
			$dis=ceil(($deal['orgprice']-$deal['price'])/$deal['orgprice']*100);
		}
		$token=$this->session->userdata("tw_token");
		$access_token=$this->session->userdata("tw_accesstoken");
		if($access_token!=false)
		{
			$previewid=rand(300,1293994971927);
			$this->dbm->newpreview($previewid,$dealid,$user['userid']);
			if($itemid!=null)
				$url=site_url("previewitem/$previewid/$itemid");
			else
				$url=site_url("preview/$previewid");
			$this->session->unset_userdata("tw_redirect");
			$twitter = new TwitterOAuth($this->tw_apikey, $this->tw_secretkey,$access_token['oauth_token'],$access_token['oauth_token_secret']);
			$msgs=array("Hot private sale in ","Cool private deals in ","Check out great deals in ViaBazaar ","Private invitation link for ViaBazaar ","Join me as friend and enjoy great sale ");
//			$msg=$msgs[rand(0,3)].$url;
			if($itemid==null)
			$msg="{$deal['brandname']} sale starting from Rs. {$deal['price']} at $dis% discount ".$url;
			else
			$msg="Sale! {$item['name']} from {$item['brandname']} for Rs {$item['price']} at {$dis}% discount ".$url;
			$content=$twitter->post("statuses/update",array("status"=>$msg));
//			$content = $twitter->OAuthRequest('https://twitter.com/statuses/update.xml', 'POST',array('status' => $msg));
//			var_dump($content);
			$data['page']="info";
			$data['info']=array("Tweeted","This Sale URL has been posted in your status");
		}
		else
		{
			$twitter = new TwitterOAuth($this->tw_apikey, $this->tw_secretkey);
			$request_token = $twitter->getRequestToken();
			if($request_token==false)
				show_error("Unable to connect to Twitter. Please try again by refreshing page");
			$this->session->set_userdata(array("tw_token"=>$request_token));
			$this->session->set_userdata(array("tw_thisredirect"=>"$dealid/$itemid"));
			$authUrl= $twitter->getAuthorizeURL($request_token);
			$data['page']="info";
			$data['info']=array("Twitter Sign In","Please sign in to your twitter account by clicking the below button<div align='center' style='padding-top:5px;'><a href='{$authUrl}'><img src='".base_url()."images/twitter_signin.png'></a></div>");
		}
		$this->load->view("index",$data);
	}
	
	function showpreview($pid)
	{
		$preview=$this->dbm->getpreview($pid);
		if($preview==false)
			return;
		$id=$preview['dealid'];
		$inviteuser=$this->dbm->getuserbyid($preview['userid']);
		$data['inviteuser']=$inviteuser['name'];
		$data['inviteid']=$inviteuser['inviteid'];
		$dealdetails=$this->dbm->getdealdetails($id);
		$data['previewid']=$pid;
		$data['category']=$dealdetails[0]['category'];
//		if($this->session->userdata("user")==false)
//			redirect("");
			if(time()>$dealdetails[0]['startdate'])
				$duration=$dealdetails[0]['enddate']-time();
			else
				$duration=$dealdetails[0]['startdate']-time();
			$durmin=floor($duration/60);
			$durhr=floor($duration/60/60);
			$durday=floor($duration/24/60/60);
			$durmin=floor($durmin-$durhr*60);
			$durhr=floor($durhr-$durday*24);
			if($durday==0)
				$left="";
			else if($durday==1)
				$left="1 day ";
			else
				$left="$durday days ";
			if($durhr==0)
				$left.="<Br>{$durmin} mins";
			else if($durhr==1)
				$left.=$durmin!=0?"<br>{$durhr} hr <br>{$durmin} mins":"<br>{$durhr} hr";
			else
				$left.=$durmin!=0?"<br>{$durhr} hrs <br>{$durmin} mins":"<br>{$durhr} hrs";
			$dealdetails[0]['left']=$left;
		$dealdetails[0]['dealid']=$id;
		$data['prices']=$this->dbm->getminmaxprice($id);
		$data['dealdetails']=$dealdetails;
		if($dealdetails[0]['startdate']<time() && $dealdetails[0]['enddate']>time())
			$data['dealstatus']="active";
		else if($dealdetails[0]['startdate']>time())
			$data['dealstatus']="inactive";
		else
			$data['dealstatus']="expired";
		$data['comment']=$this->dbm->getlastcommentfordeal($id);
		$data['page']="sale";
		$data['preview']=true;
		$data['title']="{$dealdetails[0]['tagline']} sale preview";
		$this->load->view("index",$data);
	}
	
	function new_showall()
	{
//		$user=$data['user']=$this->session->userdata("user");
//		if($user===false)
//			redirect("");
		$data['categories']=$data['menu'][0];
//		$user=$data['user']=$this->session->userdata("user");
		$deals=$this->dbm->getallactivedeals();
		$fran=$this->session->userdata("fran_auser");
		if($fran)
		{
			$itemids=array();
			foreach($deals as $deal)
				$itemids[]=$deal['id'];
			$m=$this->db->query("select mark,itemid from king_franch_marks where itemid in (".implode(",",$itemids).") and franid=?",$fran['id'])->result_array();
			foreach($m as $mrk)
			{
				$marks[$mrk['itemid']]=$mrk['mark'];
			}
			foreach($deals as $i=>$deal)
			{
				$mark=0;
				if(isset($marks[$deal['id']]))
					$mark=$marks[$deal['id']];
				$deals[$i]['price']+=$mark;
			}
		}
		$ret=$retb=array();
		foreach($deals as $deal)
		{
			$brand=$deal['brand'];
			$brandlogo=$deal['brandlogo'];
			$cat=$deal['category'];
			if(!isset($ret[$cat]))
				$ret[$cat]=array();
			if(!isset($retb[$brand]))
				$retb[$brand]=array();
			array_push($retb[$brand],$deal);
			array_push($ret[$cat],$deal);
		}
		foreach($ret as $cat=>$r)
		{
			if(count($r)<3)
				unset($ret[$cat]);
		}
		$data['ddeal']=$this->db->query("select i.*,d.* from king_deals as d join king_dealitems as i on i.dealid=d.dealid where d.startdate>=? and d.enddate<=? and d.publish=1",array(mktime(0,0,0),mktime(23,59,59)))->result_array();
		$data['deals']=$ret;
		$data['brands']=$retb;
		$data['activedeals']=$this->dbm->getalldeals();
		$data['page']="showall";
		$this->load->view("index",$data);
	}
		
	function readybuy()
	{
		$user=$data['user']=$this->session->userdata("user");
		if($data['user']===false)
			redirect("");
		$data['categories']=$data['menu'][0];
		$user=$data['user']=$this->session->userdata("user");
		$deals=$this->dbm->getallreadydeals();
		$ret=$retb=array();
		foreach($deals as $deal)
		{
			$brand=$deal['brand'];
			$brandlogo=$deal['brandlogo'];
			$cat=$deal['category'];
			if(!isset($ret[$cat]))
				$ret[$cat]=array();
			if(!isset($retb[$brand]))
				$retb[$brand]=array();
			array_push($retb[$brand],$deal);
			array_push($ret[$cat],$deal);
		}
//		$ret['Others']=array();
/*		foreach($ret as $cat=>$r)
		{
			if(count($r)<3 && $cat!="Others")
			{
				foreach($ret[$cat] as $r)
					$ret['Others'][]=$r;
				unset($ret[$cat]);
			}
		}*/
		$data['deals']=$deals;
		$data['brands']=$retb;
//		$data['activedeals']=$this->dbm->getalldeals();
		$data['page']="readybuy";
		$this->load->view("index",$data);
	}
		
	/**
	 * Show all sales
	 * 
	 * Loads active and inactive sale details in view page.
	 * This is the page after user sign in.
	 * 
	 */
	function showall()     ///deprecated
	{
		$data['categories']=$data['menu'][0];
		$user=$data['user']=$this->session->userdata("user");
		if($data['user']==false)
		{
			redirect("");
		}
//		$data['hotelDeals']=$this->dbm->gethoteldeals();
		$deals=$this->dbm->getalldeals();
		$activedeals=array();
		$inactivedeals=array();
		foreach($deals as $deal)
		{
			if(time()>$deal['startdate'])
				$duration=$deal['enddate']-time();
			else
				$duration=$deal['startdate']-time();
			$durmin=floor($duration/60);
			$durhr=floor($duration/60/60);
			$durday=floor($duration/24/60/60);
			$durmin=floor($durmin-$durhr*60);
			$durhr=floor($durhr-$durday*24);
			if($durday==0)
				$left="";
			else if($durday==1)
				$left="1 day ";
			else
				$left="$durday days ";
			if($durhr==0)
				$left.="{$durmin}mins";
			else if($durhr==1)
				$left.=$durmin!=0?"{$durhr} hr {$durmin} mins":"{$durhr} hr";
			else
				$left.=$durmin!=0?"{$durhr} hrs {$durmin} mins":"{$durhr} hrs";
			$dl=$deal;
			$dl['left']=$left;
			if(time()>$deal['startdate'])
			{
				$prices=$this->dbm->getminmaxprice($deal['dealid']);
				$dl['minprice']=$prices['min'];
				$dl['maxprice']=$prices['max'];
				$dl['minorgprice']=$prices['minorg'];
				$dl['maxorgprice']=$prices['maxorg'];
				$items['c_'.$deal['dealid']]=$this->dbm->getdealdetails($deal['dealid']);
			}
			if(time()>$deal['startdate'])
			array_push($activedeals,$dl);
			else
			array_push($inactivedeals,$dl);
		}
		$data['dealitems']=$items;
		$comments=$this->dbm->getlastcomments();
		$pcomments=array();
		foreach($comments as $comment)
		{
			$comment['comment']=breakstring($comment['comment'],100);
			array_push($pcomments,$comment);
		}
		$data['comments']=$pcomments;
		$data['activedeals']=$activedeals;
		$data['inactivedeals']=$inactivedeals;
		$data['page']="showall";
		$data['title']="Current deals";
		$this->load->view("index",$data);
	}

	/**
	 * Function to check for user in DB
	 * 
	 * Checks whether user account exists for given email id.
	 * 
	 * @param $email
	 * @return bool true on user email exists
	 */
	function checkuser($email)
	{
		if($this->dbm->getuserbyemail($email)==false)
			return true;
		return false;
	}
	
	/**
	 * Edit user profile
	 * 
	 * AJAX request to edit user profile details.
	 */
	function jxeditprofile()
	{
		$user=$this->session->userdata("user");
		if($user==false)
			redirect("");
		$bool=false;
		if($this->input->post("explo_name")!=false && $this->input->post("explo_mobile")!=false)
		{
			$userfdb=$this->dbm->getuserbyid($user['userid']);
			$name=trim($this->input->post("explo_name"));
			$mobile=trim($this->input->post("explo_mobile"));
			$password=trim($this->input->post("explo_password"));
			$cpassword=trim($this->input->post("explo_cpassword"));
			if($password!=$cpassword || strlen($password)<5)
				$password=$userfdb['password'];
			else
				$password=md5($password);
			$bool=$this->dbm->edituser($user['userid'],$name,$password,$mobile);
			if($bool==true)
			{
				$user=$this->dbm->getuserbyemail($user['email']);
				$this->session->set_userdata(array("user"=>$user));
				echo "1";
			}
			else
				echo "0";
		}
	}
	
	/**
	 * My Ksale page
	 * 
	 * Loads user acount page with details of referals, saved carts and orders.
	 * 
	 */
	function myksale()
	{
				
		$data['user']=$this->session->userdata("user");
		if($data['user']==false)
			redirect("");
		$userid=$data['user']['userid'];
		$data['userfdb']=$this->dbm->getuserbyid($data['user']['userid']);
		$data['referals']=$this->dbm->getreferals($data['user']['userid']);
		$data['savedcarts']=$this->dbm->getsavedcarts($userid);
		$data['orders']=$this->dbm->getorders($userid);
		$data['page']="myexplo";
		$data['title']="My K-Sale";
		$this->load->view("index",$data);
	}
	
	/**
	 * Load saved cart
	 * 
	 * AJAX request to load a saved cart from DB.
	 * Gets content of saved cart and loads up in current shopping cart, after destroying it.
	 * 
	 * @param int $id Cart id
	 */
	function jxloadsavedcart($id)
	{
		if($this->session->userdata("user")==false)
			redirect("");		
		$this->cart->destroy();
		$user=$this->session->userdata("user");
		$items=$this->dbm->getsavedcart($id);
		foreach($items as $item)
		{
			$arr['id']=$item['id'];
			$arr['name']=$item['name'];
			$arr['qty']=$item['qty'];
			$arr['price']=$item['price'];
			$this->cart->insert($arr);
		}
		$this->jxshowcart();
	}
	
	/**
	 * View all saved carts
	 * 
	 * AJAX request to view all saved carts in DB.
	 * Content is displayed inside fancybox.
	 * 
	 */
	function jxviewsavedcarts()
	{
		if($this->session->userdata("user")==false)
			redirect("");		
		$user=$this->session->userdata("user");
		$savedcarts=$this->dbm->getsavedcarts($user['userid']);
		echo '<div style="font-family:trebuchet ms;font-size:15px;"><div style="font-size:25px;font-weight:bold;color:#ff9900;padding-bottom:20px;">Your Saved Carts</div>';
		foreach($savedcarts as $cart) {
			echo '<div style="border:1px solid #ccc;margin:5px;background:#eee;padding:2px;padding-right:10px;"><b>'.$cart['name'].'</b><a style="color:#00f;float:right;font-size:11px;" href="'.site_url("jx/viewsavedcart/{$cart['cartid']}").'" class="carthlink">View Cart</a></div>';
		}
		if(count($savedcarts)==0)
			echo 'No carts saved!<script>cartlinks();$("#fancy_inner").css("height","30%");</script>';
		else
		echo '<script>cartlinks();$("#fancy_inner").css("height","100%");</script>';
	}
	
	function jxsendmail()
	{
		if($this->session->userdata("user")==false)
			redirect("");
		$user=$this->session->userdata("user");
		if(!$this->input->post("email")||!$this->input->post("deal"))
			return;
		$previewid=rand(300,1293994971927);
		$this->dbm->newpreview($previewid,$this->input->post("deal"),$user['userid']);
		$this->load->helper("email");
		$this->load->library("email");
		$emails=explode(",",$this->input->post("email"));
		$count=0;
		$itemid=$this->input->post("item");
		if($itemid!=false)
			$previewurl=site_url("previewitem/$previewid/$itemid");
		else
			$previewurl=site_url("preview/$previewid");
		$arr=array();
		foreach($emails as $email)
		{
			$email=trim($email);
			if(valid_email($email))
			{
				$this->email->clear();
				$config['mailtype']="html";
				$this->email->initialize($config);
				$this->email->to($email);
				$this->email->from($user['email'],$user['name']);
				$this->email->subject("I have found you a good deal");
				$msg='<div style="background:#fff;padding:30px 15px 0px 20px;font-family:\'trebuchet ms\';font-size:13px;color:#333;"><div align="left"><img src="'.base_url().'images/logo.png"></div>
						<div align="center" style="padding-top:10px;"><div style="margin-top:15px;background:#ddd;width:600px;border:1px solid #aaa;padding:10px;font-size:15px;color:#444;margin-left:20px;font-weight:bold;-moz-border-radius:5px;border-radius:5px;font-family:\'trebuchet ms\';" align="left">Hi,<br>
						<br>I thought you might be interested in this sale. 
						<br>Please click below link to preview sale.
						<br><a style="color:#00f" href="'.$previewurl.'">'.$previewurl.'</a>
						<br><br>
						You can also sign up with viabazaar by clicking below link and become my friend.
						<Div align="left"><a style="color:#00f;" href="'.site_url("invite/{$user['inviteid']}").'">'.site_url("invite/{$user['inviteid']}").'</a></div></div></div>
						<div align="right" style="padding-bottom:3px;padding-top:50px;font-size:11px;font-family:arial;">This email was sent by ViaBazaar on behalf of '.$user['email'].'</div></div>';
				$this->email->message($msg);
				$this->email->send();
				$count++;
			}
			else
				array_push($arr,$email);
		}
	}
	
	/**
	 * View content of saved cart
	 * 
	 * AJAX request to view content of saved cart in DB.
	 * Loads up content of saved cart with item name, quantity and price.
	 * Content is displayed inside fancybox.
	 * 
	 * @param $id
	 */
	function jxviewsavedcart($id)
	{
		if($this->session->userdata("user")==false)
			redirect("");
		$user=$this->session->userdata("user");
		$items=$this->dbm->getsavedcart($id);
		echo '<div style="font-family:trebuchet ms;font-size:15px;"><div style="font-size:25px;font-weight:bold;color:#ff9900;padding-bottom:20px;">Your Saved Cart - '.$items[0]['cartname'].'</div>';
		if(count($items)==0)
			die('Your cart is empty!</div><script>updatecartitems();$("#fancy_inner").css("height","30%");</script>');
		echo '<table width="100%" style="border:1px solid #eee;background:#f7f7f7;"><tr style="font-weight:bold;"><td>Item Name</td><td>Quantity</td><td>Price</td><td></td></tr>';
		$total=0;
		foreach($items as $item)
		{
			echo "<tr><td>{$item['name']}</td><td>{$item['qty']}</td><td>".($item['price']*$item['qty'])."</td></tr>";
			$total+=$item['price']*$item['qty'];
		}
		echo '</table>';
		echo '<div style="padding:10px 20px;" align="right">Total : <b>Rs '.$total.'</b></div>';
		echo '<div style="padding-top:10px;"><div style="padding-bottom:13px;"><a class="carthlink" href="'.site_url("jx/viewsavedcarts").'"><span style="background:#36f;font-family:\'trebuchet ms\';color:#fff;font-weight:bold;padding:3px 5px;font-size:12px;">View all saved carts</span></a><a href="#" onclick="loadcart('.$items[0]['cartid'].')" style="float:right;margin-right:15px;"><span style="background:#ffaa00;font-family:\'trebuchet ms\';color:#fff;font-weight:bold;padding:3px 5px;font-size:20px;">Load Cart</span></a></div>';
		echo '<script>cartlinks();</script>';
	}
	
	/**
	 * Function to authenticate user for login
	 * 
	 * This private function is called from form validation to validate for email and password with user account.
	 * 
	 * @access private
	 * @param string $password password from user POST input
	 * @return bool True on success
	 */
	function authenticate($password)
	{
		$email=$this->input->post("explo_email",true);
		return $this->dbm->checkuser($email,$password);
	}
	
	function featured_brand($url)
	{
		$bid=$this->db->query("select id,name from king_brands where url=?",$url)->row_array();
		if(empty($bid))
			show_404();
		$data['title']="Buy featured products from ".$bid['name'];
		$data['brand']=$bid['name'];
		$data['featured']=$this->dbm->getdealsbybrandurl($url);
		$data['page']="featured_brand";
		$this->load->view("index",$data);
	}
	
	
	function productsforwholebody($sex="")
	{
		if(empty($sex))
			show_404();
		if($sex!="female" && $sex!="male")
			show_404();
		if($_POST)
		{
			$this->dbm->dobpcheckout($sex);
			return;
		}
		$data['parts']=$this->dbm->getbodyparts($sex);
		$data['page']="bodyparts";
		$this->load->view("index",$data);
	}
	
	function favs()
	{
		$this->load->model("favmodel","fdbm");
		$data['locked']=$this->fdbm->getlockedcats();
		$data['cats']=$this->fdbm->getfavcats();
		$data['page']="favs/favs";
		$data['title']="Choose your ".FAV_LIMIT." FAVs to get ".FAV_DISCOUNT."% discount";
		$this->load->view("index",$data);
	}
	
	function choosefav($caturl)
	{
		$this->load->model("favmodel","fdbm");
		$data['prods']=$this->fdbm->getprods($caturl);
		if(empty($data['prods']))
			show_404();
		$cat=$data['prods'][0]['cat'];
		$data['page']="favs/choose";
		$data['title']="Choose your FAV in $cat to get ".FAV_DISCOUNT."% discount";
		$this->load->view("index",$data);
	}
	
	function weeklysavings()
	{
		$data['prods']=$this->dbm->getweeklysavings();
		$data['page']="weeklysavings";
		$data['title']="Weekly Savings";
		$this->load->view("index",$data);
	}
	
	function selectfav($id)
	{
		$user=$this->session->userdata("user");
		if(!$user)
		{
			$data['info']=array("Sign In required","Please login to select this product as your FAV. You will be redirected to signin/signup page in 3 seconds...<script>$(function(){window.setTimeout(function(){make_rem_redir('".site_url("selectfav/$id")."');},3000);});</script>");
			$data['page']="info";
			$this->load->view("index",$data);
			return;
		}
		$this->load->model("favmodel","fdbm");
		$cat=$this->fdbm->getfavcatforprod($id);
		if(!$cat)
			show_404();
		$favs=$this->fdbm->getallfavs();
		if(count($favs)>=FAV_LIMIT)
		{
			$data['info']=array("All FAVs selected","You have already selected maximum number of FAVs allowed<br>Number of FAVs allowed : ".FAV_LIMIT);
			$data['page']="info";
			$this->load->view("index",$data);
			return;
		}	
			
		$prod=$this->fdbm->getprodforfavcatselcted($cat);
		if(!empty($prod))
		{
			$data['info']=array("Category is locked","You have already selected a product in this category and its locked for ".FAV_EXPIRY." days.<br>You can't select another product with same type.");
			$data['page']="info";
			$this->load->view("index",$data);
			return;
		}
		$this->fdbm->setfav($id);
		redirect("favs");
	}
	
	function checkauth()
	{
		$user=$this->session->userdata("user");
		if($user)
			return $user;
		redirect("");
	}
	
	function history()
	{
		if($_POST)
		{
			$this->dbm->deletehistory($this->input->post("id"));
			die;
		}
		$data['title']="Your History";
		$data['history']=$this->dbm->gethistory();
		$data['page']="history";
		$this->load->view("index",$data);
	}
	
	function jx_subscribeaction()
	{
		$this->load->library("form_validation");
		if($this->form_validation->valid_email($this->input->post("email")))
			$this->dbm->subscr_email($this->input->post("email"));
	}
	
	function jx_startbuyprocess()
	{
		$user=$this->session->userdata("user");
		if(!$user)
		{
			$user=$this->dbm->getuserbyemail("guest@localcircle.in");
			$user['corp']=$this->dbm->getcorpname($user['corpid']);
		}
		$inps=array("item","qty","uids","emails","fbs","fbemail");
		foreach($inps as $inp)
		{
//			if($this->input->post($inp)==false)
//				die();
			$$inp=$this->input->post($inp);
		}
		$itemid=$item;
		$item=$this->dbm->getitemdetails($itemid);
		if($uids=="")
			$uids=array();
		else
			$uids=explode(",",$uids);

		if($emails=="")
			$emails=array();
		else
			$emails=explode(",",$emails);
			
		if($fbs=="")
			$fbs=array();
		else
			$fbs=explode(",",$fbs);	
		
		$buyers=$qty+count($uids)+count($emails)+count($fbs);
		
		$slots=unserialize($item['slots']);
     	$nslots=array();
     	$nslotprice=array();
     	$slotprice=0;
     	if(is_array($slots) && $item['groupbuy']==1)
     	foreach($slots as $sno=>$srs)
     	{
     		$nslots[]=$sno;
     		$nslotprice[]=$srs;
     	}
		foreach($nslots as $si=>$ns)
		{
			if($buyers<$ns)
				break;
		}
     	if(isset($si))
		$slotprice=$nslotprice[$si];
		if($slotprice!=0)
		$refund=$item['price']-$slotprice;
		else
		$refund=0;
		
		$this->dbm->startbuyprocess($qty,$uids,$refund,$item,$emails,array("ids"=>$fbs,"email"=>$fbemail),false);
	}
	
	function viewbymenu($url,$p="page-1")
	{
		@list($x,$p)=@explode("-",$p);
		$p=$p+1-1;
		if(empty($p) || $p<=0)
			$p=1;
		$menu=$this->db->query("select id,name from king_menu where url=?",$url)->row_array();
		if(empty($menu))
			show_404();
		$data['p']=$p;
		$data['cats']=$this->dbm->getcatsformenu($menu['id']);
		$data['brands']=$this->dbm->getbrandsformenu($menu['id']);
		$data['count']=$this->dbm->getdealsbymenu_count($menu['id']);
		$data['deals']=$this->dbm->getdealsbymenu_paged($menu['id'],$p);
		$data['prevurl']=site_url("viewbymenu/$url/page-".($p-1));
		$data['nexturl']=site_url("viewbymenu/$url/page-".($p+1));
		$data['page']="paged_list";
		$data['uri_part']="menu";
		$data['title']="Buy {$menu['name']} products online in India";
		$data['pagetitle']="Buy {$menu['name']} products online";
		$this->load->view("index",$data);
	}

	
	function viewbymenubrand($murl,$burl,$p="page-1")
	{
		@list($x,$p)=@explode("-",$p);
		$p=$p+1-1;
		if(empty($p) || $p<=0)
			$p=1;
		$menu=$this->db->query("select id,name from king_menu where url=?",$murl)->row_array();
		if(empty($menu))
			show_404();
		$brand=$this->db->query("select id,name from king_brands where url=?",$burl)->row_array();
		if(empty($brand))
			show_404();
		$data['p']=$p;
		$data['cats']=$this->dbm->getcatsformenubrand($menu['id'],$brand['id']);
		$data['count']=$this->dbm->getdealsbymenubrand_count($menu['id'],$brand['id']);
		$data['deals']=$this->dbm->getdealsbymenubrand_paged($menu['id'],$brand['id'],$p);
		$data['prevurl']=site_url("viewbymenubrand/$murl/$burl/page-".($p-1));
		$data['nexturl']=site_url("viewbymenubrand/$murl/$burl/page-".($p+1));
		$data['page']="paged_list";
		$data['title']="Buy {$brand['name']} products for {$menu['name']} online in India";
		$data['uri_part']="menu";
		$data['pagetitle']="Buy {$brand['name']} products for {$menu['name']}";
		$this->load->view("index",$data);
	}
	
	function viewbymenucat($murl,$curl,$p="page-1")
	{
		@list($x,$p)=@explode("-",$p);
		$p=$p+1-1;
		if(empty($p) || $p<=0)
			$p=1;
		$menu=$this->db->query("select id,name from king_menu where url=?",$murl)->row_array();
		if(empty($menu))
			show_404();
		$cat=$this->db->query("select id,name from king_categories where url=?",$curl)->row_array();
		if(empty($cat))
			show_404();
		$data['p']=$p;
		$data['brands']=$this->dbm->getbrandsformenucat($menu['id'],$cat['id']);
		$data['count']=$this->dbm->getdealsbymenucat_count($menu['id'],$cat['id']);
		$data['deals']=$this->dbm->getdealsbymenucat_paged($menu['id'],$cat['id'],$p);
		$data['prevurl']=site_url("viewbymenucat/$murl/$curl/page-".($p-1));
		$data['nexturl']=site_url("viewbymenucat/$murl/$curl/page-".($p+1));
		$data['page']="paged_list";
		$data['title']="Buy {$cat['name']} products for {$menu['name']} online in India";
		$data['uri_part']="menu";
		$data['pagetitle']="Buy {$cat['name']} products for {$menu['name']}";
		$this->load->view("index",$data);
	}
	
	function viewbybrand($url,$p="page-1")
	{
		@list($x,$p)=@explode("-",$p);
		$p=$p+1-1;
		if(empty($p) || $p<=0)
			$p=1;
		$brand=$this->db->query("select id,name from king_brands where url=?",$url)->row_array();
		if(empty($brand))
			show_404();
		$data['p']=$p;
		$data['cats']=$this->dbm->getcatsforbrand($brand['id']);
		$data['count']=$this->dbm->getdealsbybrand_count($brand['id']);
		$data['deals']=$this->dbm->getdealsbybrand_paged($brand['id'],$p);
		$data['prevurl']=site_url("viewbybrand/$url/page-".($p-1));
		$data['nexturl']=site_url("viewbybrand/$url/page-".($p+1));
		$data['page']="paged_list";
		$data['uri_part']="brand";
		$data['title']="Buy {$brand['name']} products online in India";
		$data['pagetitle']="Buy {$brand['name']} products";
		$this->load->view("index",$data);
	}
	
	function viewbybrandcat($burl,$curl="",$p="page-1")
	{
		@list($x,$p)=@explode("-",$p);
		$p=$p+1-1;
		if(empty($p) || $p<=0)
			$p=1;
		$brand=$this->db->query("select id,name from king_brands where url=?",$burl)->row_array();
		if(empty($brand))
			show_404();
		$cat=$this->db->query("select id,name from king_categories where url=?",$curl)->row_array();
		if(empty($cat))
			show_404();
		$data['p']=$p;
		$data['count']=$this->dbm->getdealsbycatbrand_count($cat['id'],$brand['id']);
		$data['deals']=$this->dbm->getdealsbycatbrand_paged($cat['id'],$brand['id'],$p);
		$data['prevurl']=site_url("viewbybrandcat/$burl/$curl/page-".($p-1));
		$data['nexturl']=site_url("viewbybrandcat/$burl/$curl/page-".($p+1));
		$data['page']="paged_list";
		$data['title']="Buy {$brand['name']} products in {$cat['name']} online in India";
		$data['pagetitle']="Buy {$brand['name']} products in {$cat['name']}";
		$data['uri_part']="brand";
		$this->load->view("index",$data);
	}
	
	
	function viewbycat($url,$p="page-1")
	{
		@list($x,$p)=@explode("-",$p);
		$p=$p+1-1;
		if(empty($p) || $p<=0)
			$p=1;
		$cat=$this->db->query("select id,name from king_categories where url=?",$url)->row_array();
		if(empty($cat))
			show_404();
		$data['p']=$p;
		$data['brands']=$this->dbm->getbrandsforcat($cat['id']);
		$data['count']=$this->dbm->getdealsbycat_count($cat['id']);
		$data['deals']=$this->dbm->getdealsbycat_paged($cat['id'],$p);
		$data['prevurl']=site_url("viewbycat/$url/page-".($p-1));
		$data['nexturl']=site_url("viewbycat/$url/page-".($p+1));
		$data['page']="paged_list";
		$data['uri_part']="cat";
		$data['title']="Buy {$cat['name']} products online in India";
		$data['pagetitle']="Buy {$cat['name']} products";
		$this->load->view("index",$data);
	}
	
	function viewbycatbrand($curl,$burl="",$p="page-1")
	{
		@list($x,$p)=@explode("-",$p);
		$p=$p+1-1;
		if(empty($p) || $p<=0)
			$p=1;
		$brand=$this->db->query("select id,name from king_brands where url=?",$burl)->row_array();
		if(empty($brand))
			show_404();
		$cat=$this->db->query("select id,name from king_categories where url=?",$curl)->row_array();
		if(empty($cat))
			show_404();
		$data['p']=$p;
		$data['count']=$this->dbm->getdealsbycatbrand_count($cat['id'],$brand['id']);
		$data['deals']=$this->dbm->getdealsbycatbrand_paged($cat['id'],$brand['id'],$p);
		$data['prevurl']=site_url("viewbycatbrand/$curl/$burl/page-".($p-1));
		$data['nexturl']=site_url("viewbycatbrand/$curl/$burl/page-".($p+1));
		$data['page']="paged_list";
		$data['title']="Buy {$cat['name']} products in {$brand['name']} online in India";
		$data['pagetitle']="Buy {$cat['name']} products in {$brand['name']}";
		$data['uri_part']="cat";
		$this->load->view("index",$data);
	}
	
	
	function buypartitem($id_url="")
	{
		 
		if(empty($id_url))
			show_404();
	
		header("Content-Type: text/html; charset=UTF-8");
		header("Cache-Control: private, no-cache, no-store, must-revalidate");
		header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
		header("Pragma: no-cache");
		$this->load->model("viakingmodel","dbm");
	
		$id_url=html_entity_decode($id_url);
		$d_item=$this->db->query("select i.id,i.url,d.publish,d.startdate,d.enddate,i.live,i.quantity,i.available from king_dealitems i join king_deals d on d.dealid=i.dealid where i.url=? or i.id=? and i.id!=0",array($id_url,$id_url))->row_array();
		if(empty($d_item))
			show_404();
		if($d_item['startdate']>time() || $d_item['enddate']<time() || $d_item['publish']!=1 || $d_item['live']!=1 || $d_item['quantity']<=$d_item['available'])
			redirect($d_item['url']);
	
		if($this->session->userdata("bodyparts_checkout"))
		{
			$data['info']=array("Please finish your special checkout","You have a special checkout in progress. Please finish your checkout or clear it before adding this product");
			$data['page']="info";
			$this->load->view("index",$data);return;
		}
	
		$itemid=$d_item['id'];
	
		$user=$this->session->userdata("user");
		if(!$user)
		{
			$user=$this->dbm->getuserbyemail("guest@localcircle.in");
			$user['corp']=$this->dbm->getcorpname($user['corpid']);
		}
		$this->load->library("cart");
	
		$qty=1;
	
		$opts=array();
	
		$itemdetails=$this->dbm->getitemdetails($itemid);
	
		if($qty>$itemdetails['max_allowed_qty'])
			$qty=$itemdetails['max_allowed_qty'];
	
		$uids=$emails=array();
		$buyers=$qty;
		$refund=0;
	
		$bpid=$this->dbm->startbuyprocess($qty,$uids,$refund,$itemdetails,$emails,array(),true);
	
		if($bpid)
		{
			$bpuid=$this->db->query("select id from king_buyprocess where bpid=? and userid=?",array($bpid,$user['userid']))->row()->id;
			$bp=$this->db->query("select quantity,refund from king_m_buyprocess where id=?",$bpid)->row();
			$opts["bpid"]=$bpid;
			$opts['bpuid']=$bpuid;
		}
		else {$bp->refund=0;$bp->quantity=0;
		}
		$refund=$bp->refund;
	
		$name=str_replace("'"," ",$itemdetails['name']);
		$mark=0;
		$price=$itemdetails['price'];
		if($qty>=$bp->quantity)
			$price=$itemdetails['price']-$refund;
		$cart=array("id"=>$itemdetails['id'],'qty'=>$qty,"price"=>$price,"name"=>str_replace("&","-",$name),"options"=>$opts);
		$flag=false;
		foreach($this->cart->contents() as $cartitem)
		{
			if($cartitem['id']==$itemid)
			{
				$flag=true;break;
			}
		}
		if($flag)
		{
			$up=array("qty"=>0,"rowid"=>$cartitem['rowid']);
			$this->cart->update($up);
			$up=array("id"=>$itemdetails['id'],'qty'=>$qty,'price'=>$price,'options'=>$opts,"name"=>str_replace("&","-",$itemdetails['name']));
			$this->cart->insert($up);
			$this->dbm->savecart();
			if(isset($_SERVER['HTTP_X_REQUESTED_WITH']))
			{
				$ret=array("num"=>$this->cart->total_items(),"total"=>number_format($this->dbm->calc_cart_total()));
				die(json_encode($ret));
			}
			redirect("shoppingcart");
		}
		$ret=$this->cart->insert($cart);
	
		$this->dbm->savecart();
	
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']))
		{
			$ret=array("num"=>$this->cart->total_items(),"total"=>number_format($this->dbm->calc_cart_total()));
			die(json_encode($ret));
		}
	
		redirect("shoppingcart");
	}
	
}
