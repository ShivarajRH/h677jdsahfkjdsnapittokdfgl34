<?php 
/**
 * Controller for front end of LocalSquare aka ViaKingSale
 * 
 * @package viakingsale
 * @subpackage frontend
 * @author Vimal <vimal@viaadz.com>
 * @since 2009/12
 * @version 0.9.4
 */

include_once "fb/facebook.php";
include_once 'tw/twitteroauth.php';

/**
 * Deals Controller class
 * 
 * @version 0.9.4
 */
class Deals extends Controller
{
//	private $fb_apikey="78ec1ca41db6a0f75b7608a7dbc9f26b";
//	private $fb_secretkey="8345116666c6faff4a0a0e084512cfea";
	
	/**
	 * facebook api key
	 * @var string 
	 * @access private
	 */
	private $fb_apikey="6268b8327b1078e13f519c7afff006cb";			//SERVER

	/**
	 * @var string facebook secret key
	 * @access private
	 */
	private $fb_secretkey="08411ff7abe8513e61b5cbb7f2d1baf8";
	
//	private $tw_apikey="5bJnS7RPHmXUSFf2TL79g";								//Don't use
//	private $tw_secretkey="ki9v9Kur9sIgWCDdKmICjmCHauqvldExvl4TQMfxIY";		//Don't use

	private $tw_apikey="T8LFin71Y9ZIzUIWbAA26g";								//for alpha test
	private $tw_secretkey="p0b4re0HAXwj9FN83ksl4pso12VIBVs8TGsVVVU8";		//for alpha test
	
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
	private $g_site="05864707894925737706";			//server
	
	/**
	 * Constructor for deals controller
	 * 
	 * Loads cart library and explo model.
	 * 
	 */
	function Deals()
	{
//		ini_set("display_errors","on");
//		error_reporting(0);
		parent::Controller();
		$this->load->library("cart");
		$this->load->model("viakingmodel","dbm");
	}
	
	
	/**
	 * Google signin page
	 * 
	 * Gets details from signed-in Google user account. 
	 * Creates new internal account for the user if not exists. 
	 * Signs in the user with his/her Google account.
	 * 
	 */
	function gsignin()
	{
		if(isset($_COOKIE['fcauth'.$this->g_site]))
		{
			$auth=$_COOKIE['fcauth'.$this->g_site];
			$url="http://www.google.com/friendconnect/api/people/@viewer/@self?fcauth=$auth";
			$c=curl_init($url);
			curl_setopt($c,CURLOPT_RETURNTRANSFER,1);			// get response data not to stdout
			$d=curl_exec($c);
			$d1=json_decode($d);
			$resp=$d1->entry;
//			print_r($data);exit;
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
				$this->session->set_userdata("user",$user);
				$data['smallheader']=true;
				$data['page']="signedin";
				$this->load->view("index",$data);
			}
		}
		else
			redirect("");
	}
	
	function fbsignin()
	{
		
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
		$this->load->library("form_validation");
		
		$data['tw_authUrl'] = site_url("twredirect");		
		
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
		$data['g_redirect']="gsignin";
		$data['g_site']=$this->g_site;
		$this->load->view("index",$data);
	}
	
	function api($salt)
	{
		$api=$this->dbm->getapi($salt);
		if(empty($api))
			die();
		$data['item']=$this->dbm->getitemdetails($api['itemid']);
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
	function twsignin($token)
	{
			$tw_token=$this->session->userdata("tw_token");
			$twitter = new TwitterOAuth($this->tw_apikey, $this->tw_secretkey, $token,$tw_token['oauth_token_secret']);
			$access_token=$twitter->getAccessToken();
			if($access_token==false)
				redirect("");
			$this->session->set_userdata(array("tw_redirect"=>true));
			$this->session->set_userdata(array("tw_accesstoken"=>$access_token));
			if($this->session->userdata("tw_inviteredirect")!=false)
			{
				$this->session->unset_userdata("tw_inviteredirect");
				redirect("twinvite");
			}
			if($this->session->userdata("tw_thisredirect")!=false)
			{
				$redir=$this->session->userdata("tw_thisredirect");
				$this->session->unset_userdata("tw_thisredirect");
				redirect("twthis/".$redir);
			}
			$content = $twitter->get('account/verify_credentials');
			$uid=$content->id;
			$userid=$this->dbm->checkspecialuser($uid,1);
			if($userid==false)
			{
				$this->dbm->newspecialuser($uid,$content->name,1,randomChars(10));
				$userid=$this->dbm->checkspecialuser($uid,1);
			}
			if($userid!=false)
			{
				$user=$this->dbm->getuserbyid($userid);
//				print_r($user);
				$this->session->set_userdata("user",$user);
				$data['smallheader']=true;
				$data['page']="signedin";
				$this->load->view("index",$data);
			}
	}
	
	/**
	 * Twitter redirect page
	 * 
	 * Gets request token from Twitter for OAUTH.
	 * Redirects user to Twitter.com login page to get access token for api.
	 * 
	 */
	
	function twredirect()
	{
		$twitter = new TwitterOAuth($this->tw_apikey, $this->tw_secretkey);		
		$request_token = $twitter->getRequestToken();
		if($request_token==false)
			die("Unable to connect to Twitter. Please try again by refreshing page");
//		var_dump($request_token);exit;
		$this->session->set_userdata("tw_token",$request_token);
		$token=$request_token['oauth_token'];
		$tw_authUrl= $twitter->getAuthorizeURL($token);		
		header("location:$tw_authUrl");
		echo "Connecting Twitter... Please wait";
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
		$data['menu']=$this->dbm->getmenu();
		
		$this->load->library("form_validation","","fmv");
		$data['apikey']=$this->fb_apikey;
		$inviteUser=$this->dbm->getuserbyinviteid($id);
		$user=$this->session->userdata("user");
		if($inviteUser==false)
			$info=array("Invalid Invitation","Please check invitation URL");
		else if($user!=false && $user['userid']==$inviteUser['userid'])
			$info=array("Your invitation page","Please use this URL to invite your friends to LocalSquare. Your friends can sign up from this page.<br>".site_url("invite/".$user['inviteid']));
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
				$data['info']=array("Account Sign Up","Your LocalSquare account will be created with following details.<br><br>Name : $name<br><form method='post'>Email : <input type='text' name='email'><br><br>And you will be able to login only from facebook");
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
						$data['info']=array("Welcome {$this->input->post('explo_name')}","Successfully registered.<br>Please <a style='color:#00f' href='".site_url("signin")."'>sign in</a> and check out exclusive deals from LocalSquare");
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
	
	/**
	 * Send Invite page
	 * 
	 * Page to send invitation through mail, Google, Twitter and Facebook.
	 */
	function invite()
	{
		$data['menu']=$this->dbm->getmenu();
				
		$data['user']=$user=$this->session->userdata("user");
		if($data['user']==false)
			redirect("");
		$data['page']="invite";
		$data['g_site']=$this->g_site;
		$data['g_init']=true;
		$data['g_specialnext']='showsendinvite()';
		$data['g_invitemsg']='Please click the following url to get signed up with LocalSquare and become my friend. <a href="'.site_url("invite/{$user['inviteid']}").'">'.site_url("invite/{$user['inviteid']}").'</a> Exclusive private deals are available in LocalSquare. I want to share this with you.</div>';
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
			$msgs=array("Hot private sale in ","Cool private deals in ","Check out great deals in LocalSquare ","Private invitation link for LocalSquare ","Join me as friend and enjoy great sale ");
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
		$data['info']=array("We need your permission","LocalSquare will post your invitation url in your friend's wall. Please click below button to give permission.<br><div align='center'><fb:prompt-permission perms=\"publish_stream\" next_fbjs=\"location='".site_url("fbinvite")."'\"> Grant permission </fb:prompt-permission></div>");
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
			$fb->api_client->stream_publish("Check out great deals in LocalSquare. My invitation link : $url Please click to sign up for free and join me as friend.",null,json_encode($action),$uid);
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
			$fb->api_client->stream_publish("I liked this deal in LocalSquare. {$msg} Please take a look at this : $url",null,json_encode($action),$uid);
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
		$data['g_site']=$this->g_site;
		$twitter = new TwitterOAuth($this->tw_apikey, $this->tw_secretkey);		
		$request_token = $twitter->getRequestToken();
//		var_dump($request_token);exit;
		$this->session->set_userdata("tw_token",$request_token);
		$token=$request_token['oauth_token'];
		$data['tw_authUrl'] = $twitter->getAuthorizeURL($token);		
		
		$data['apikey']=$this->fb_apikey;
		$data['smallheader']=true;
		$data['fb_init']=true;
		$data['g_redirect']="signin";
		$fb=new Facebook($this->fb_apikey,$this->fb_secretkey);
		$data['fb_user']=$fb_user=$fb->get_loggedin_user();
		if($fb_user)
		{
			$userid=$this->dbm->checkspecialuser($fb_user,2);
			if($userid==false)
			{
				$response=$fb->api_client->users_getinfo($fb_user,"name");
				$this->dbm->newspecialuser($fb_user,$response[0]['name'],2,randomChars(10));
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
				$data['fbuser']=true;
				$data['title']="Sign in";
				$data['page']="signedin";
			}
			else exit;
		}
		else
		{
		$this->load->library("form_validation","","fmv");
		$this->fmv->set_rules("explo_email","Email","required|valid_email");
		$this->fmv->set_rules("explo_password","Password","required|callback_authenticate");
		$this->fmv->set_message("required","%s is required");
		$this->fmv->set_message("valid_email","Please enter a valid email");
		$this->fmv->set_message("authenticate","Incorrect email or password");
		$email=$this->input->post("explo_email",true);
		if($this->fmv->run()===true)
		{
			$user=$this->dbm->getuserbyemail($email);
			$this->dbm->updateuserlogin($user['userid']);
			$this->session->set_userdata(array("user"=>$user));
			$data['page']="signedin";
		}else
			$data['page']="default";
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
		$this->session->unset_userdata("user");
		if($this->session->userdata("fb_signin")!=false)
		{
			$data['fb_init']=true;
			$data['fb_signout']=true;
			$this->session->unset_userdata("fb_signin");
//			unset($_COOKIE[$this->fb_apikey]);
//			unset($_COOKIE[$this->fb_apikey."_user"]);
//			$fb=new Facebook($this->fb_apikey,$this->fb_secretkey);
//			$fb->logout(site_url("signout"));
//		exit;
		}
		$data['smallheader']=true;
		$data['page']="signedin";
		$data['signout']=true;
		$this->load->view("index",$data);
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
					$this->email->subject("Great deals in LocalSquare. Sign up for free!");
					$msg='<div style="background:#222;padding:30px 15px 0px 20px;font-family:\'trebuchet ms\';font-size:13px;color:#fff;"><div align="left"><img src="'.base_url().'images/logo.png"></div>
							<div align="center" style="padding-top:10px;"><div style="margin-top:15px;background:#ddd;width:600px;border:1px solid #aaa;padding:10px;font-size:15px;color:#444;margin-left:20px;font-weight:bold;-moz-border-radius:5px;border-radius:5px;font-family:\'trebuchet ms\';" align="left">Hi '.$email.',<br>
							<br>Exclusive hotel deals and tour packages are available in LocalSquare. I would like you to sign up and check out those fabulous deals. Please click below link to get signed up.
							<br><br>
							<Div align="center"><a style="color:#00f;" href="'.site_url("invite/{$user['inviteid']}").'">'.site_url("invite/{$user['inviteid']}").'</a></div></div></div>
							<div align="right" style="padding-bottom:3px;padding-top:50px;font-size:11px;font-family:arial;">This email was sent by LocalSquare on behalf of '.$user['email'].'</div></div>';
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
		$data['menu']=$this->dbm->getmenu();
				
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
		$data['menu']=$this->dbm->getmenu();
				
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
	
	function brand($name)
	{
		$data['menu']=$this->dbm->getmenu();
		$data['user']=$user=$this->session->userdata("user");
		if($user==false)
			redirect("");
		$brand=$this->dbm->getbrand($name);
		if($brand==false)
		{
			$data['info']=array($name."?","The brand you are looking for doesnot exist");
			$data['page']="info";
			$this->load->view("index",$data);
			return;
		}
		$deals=$this->dbm->getdealsforbrand($brand['id']);
		if($deals==false)
		{
			$data['page']="info";
			$data['info']=array("No Sale","Currently there are no sales available for {$brand['name']}");
			$this->load->view("index",$data);
			return;
		}
		$activedeals=array();
		$inactivedeals=array();
		if(isset($deals[0])) foreach($deals as $deal)
		{
			if(time()>$deal['startdate'])
				$duration=$deal['enddate']-time();
			else
				$duration=$deal['startdate']-time();
			$duration=$deal['enddate']-time();
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
			$dl['name']=$brand['name'];
			$dl['left']=$left;
			if(time()>$deal['startdate'])
			array_push($activedeals,$dl);
			else
			array_push($inactivedeals,$dl);
		}
//		print_r($activedeals);
		$data['items']=array();
		foreach($activedeals as $deal)
			$data['items']=array_merge($data['items'],$this->dbm->getdealdetails($deal['dealid']));
		$data['brand']=$brand;
		$data['activedeals']=$activedeals;
		$data['inactivedeals']=$inactivedeals;
		//		print_r($data['deals']);
		$data['page']="showforbrand";
		$data['title']="{$brand['name']} Sale";
		$this->load->view("index",$data);
	}
	
	
	/**
	 * Show sales for category
	 * 
	 * Shows active and inactive sales for current category.
	 * Gets category id for category name from db.
	 * Loads up view pages with details of current sales.
	 * 
	 * @param string $name Category name
	 */
	function category($name)
	{
		
		$data['menu']=$this->dbm->getmenu();
				
		$data['user']=$user=$this->session->userdata("user");
		if($user==false)
			redirect("");
		
		$cat=$this->dbm->getcategory($name);
		if($cat==false)
		{
			$data['info']=array($name."?","The category you are looking for doesnot exist");
			$data['page']="info";
			$this->load->view("index",$data);
			return;
		}
		$id=$cat['id'];
		$data['category']=$category=$cat['name'];
//		$data['category']=$this->dbm->getcategoryname($id);
		$deals=$this->dbm->getdealsbycategory($id);
		if($deals==false)
		{
			$data['page']="info";
			$data['info']=array("No Sale","Currently there are no sales available in {$data['category']} category");
			$this->load->view("index",$data);
			return;
		}
		$activedeals=array();
		$inactivedeals=array();
		if(isset($deals[0])) foreach($deals as $deal)
		{
			if(time()>$deal['startdate'])
				$duration=$deal['enddate']-time();
			else
				$duration=$deal['startdate']-time();
			$duration=$deal['enddate']-time();
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
			array_push($activedeals,$dl);
			else
			array_push($inactivedeals,$dl);
		}
		$data['activedeals']=$activedeals;
		$data['inactivedeals']=$inactivedeals;
		//		print_r($data['deals']);
		$data['page']="showbycategory";
		$data['title']="Category $category";
		$this->load->view("index",$data);
	}
	
	/**
	 * Show sale item
	 * 
	 * Gets item details for given item id from db.
	 * Loads view page with details of item and sale that belongs to.
	 * 
	 * @param $id
	 */
	function showsaleitem($id)
	{
		$data['user']=$this->session->userdata("user");
		if($data['user']==false)
			redirect("");
		$data['menu']=$this->dbm->getmenu();
					
		$itemdetails=$this->dbm->getitemdetails($id);
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
//		print_r($itemdetails);
//		print_r($itemresources);
		$data['extradeals']=$this->dbm->getextradeals($itemdetails['catid'],$itemdetails['brandid'],$itemdetails['id']);
		$data['itemdetails']=$itemdetails;
		if($itemdetails['dealtype']==0)
		$data['page']="showitem";
		else if($itemdetails['dealtype']==1)
		$data['page']="groupsale";
		$data['title']="{$itemdetails['name']} for Rs {$itemdetails['price']} only";
		$this->load->view("index",$data);
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
//		print_r($itemdetails);
//		print_r($itemresources);
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
		$data['menu']=$this->dbm->getmenu();
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
		$data['menu']=$this->dbm->getmenu();
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
//		print_r($itemdetails);
//		print_r($itemresources);
		if($itemdetails['enddate']<time())
			$data['itemexpired']=true;
		$data['itemdetails']=$itemdetails;
		$data['page']="showcomments";
		$data['title']="{$itemdetails['name']} comments";
		$this->load->view("index",$data);
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
		if($this->session->userdata("user")==false)
			redirect("");		
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
		if($this->session->userdata("user")==false)
			redirect("");
		$this->load->library("form_validation","","fmv");
		$this->fmv->set_rules("mobile","Mobile number","required|trim|numeric|exact_length[10]");
		$this->fmv->set_rules("address","Delivery Address","required|trim");
		$this->fmv->set_message("numeric","Invalid mobile number");
		$this->fmv->set_message("exact_length","Invalid mobile number. Should contain ten digits. Ex : 9846329302");
		$this->fmv->set_message("required","Please enter %s");
		if($this->fmv->run()!=false)
		{
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
			$this->checkout();
			return;
		}
		$mobile=$this->input->post("mobile");
		$address=$this->input->post("address");
		$bool=$this->dbm->checkout($user['userid'],$items,$address);
		if($bool==false)
		{
			$data['page']="info";
			$data['info']=array("Checkout Failed","Something went wrong somewhere. Sorry for this convenience. Please try again");
			$this->load->view("index",$data);
			return;
		}
		$this->dbm->updateuseraddress($user['userid'],$mobile,$address);
		$this->cart->destroy();
		$this->load->view("index",$data);
		}
		else
		$this->checkout();
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
	function checkout()
	{
		if($this->session->userdata("user")==false)
			redirect("");		
		$this->load->library("form_validation");
		$this->load->library("cart");
		$data['menu']=$this->dbm->getmenu();
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
			$item['pic']=$itemsquantity[$item['id']]['pic'];
			array_push($data['items'],$item);
		}		
		$data['title']="Checkout";
		$this->load->view("index",$data);
	}
	
	/**
	 * Show cart
	 * 
	 * AJAX request to show cart.
	 * Get cart items and show details including total price.
	 */
	function jxshowcart()
	{
		if($this->session->userdata("user")==false)
			redirect("");
		$this->load->library("cart");
		$items=$this->cart->contents();
		echo '<div style="font-family:trebuchet ms;font-size:15px;"><div style="padding-right:10px;font-size:25px;font-weight:bold;color:#ff9900;padding-bottom:20px;float:left;padding-top:10px;">Your Shopping Cart</div><img src="'.base_url().'images/cart.png" style="float:left"><br style="clear:both">';
		if(count($items)==0)
			die('Your cart is empty!</div><div align="right" style="padding-right:20px;"><a class="carthlink" href="'.site_url("jx/viewsavedcarts").'"><span style="background:#36f;font-family:\'trebuchet ms\';color:#fff;font-weight:bold;padding:3px 5px;font-size:12px;">Load saved cart</span></a></div><script>cartlinks();updatecartitems();$("#fancy_inner").css("height","35%");</script>');
		echo '<table width="100%" style="border:1px solid #eee;background:#f7f7f7;"><tr style="font-weight:bold;"><td>Item Name</td><td>Quantity</td><td>Price</td><td></td></tr>';
		$total=0;
		foreach($this->cart->contents() as $item)
		{
			echo "<tr><td>{$item['name']}</td><td>{$item['qty']}</td><td>".($item['price']*$item['qty'])."</td><td><a class='carthlink' href='".site_url("jx/deletecartitem/".$item['id'])."'><img src='".base_url()."images/remove.png'></a></td></tr>";
			$total+=$item['price']*$item['qty'];
		}
		echo '</table>';
		echo '<div style="padding:10px 20px;" align="right">Total : <b>Rs '.$total.'</b></div>';
		echo '<div style="padding:0px 0px;padding-top:10px;">';
		echo '<div style="float:right"><div><a href="'.site_url("checkout").'"><span style="background:#ffaa00;font-family:\'trebuchet ms\';color:#fff;font-weight:bold;padding:3px 5px;font-size:20px;">Checkout</span></a></div><div style="padding-top:10px;font-size:12px;clear:both"><a href="'.site_url("deals").'" style="color:#00f;">Or continue shopping</a></div></div></div>';
		echo '<div style="padding-bottom:13px;"><a href="javascript:void(0)" onclick="savecart()"><span style="background:#36f;font-family:\'trebuchet ms\';color:#fff;font-weight:bold;padding:3px 5px;font-size:12px;">Save & create new cart</span></a></div>';
		echo '<div style="padding-bottom:13px;"><a class="carthlink" href="'.site_url("jx/viewsavedcarts").'"><span style="background:#36f;font-family:\'trebuchet ms\';color:#fff;font-weight:bold;padding:3px 5px;font-size:12px;">Load saved cart</span></a>';
		echo '<a class="carthlink" style="margin-left:10px;" href="'.site_url("jx/destroycart").'"><span style="background:#f43;font-family:\'trebuchet ms\';color:#fff;font-weight:bold;padding:3px 5px;font-size:12px;">Empty Cart</span></a></div>';
		echo '<script>cartlinks();updatecartitems();$("#fancy_inner").css("height","100%");</script>';
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
		if($this->session->userdata("user")==false)
			redirect("");
		$this->load->library("cart");
		echo $this->cart->total_items();
	}
	
	/**
	 * Add to cart
	 * 
	 * AJAX request to add an item into cart.
	 * Gets item id & quantity from user post input and adds the item into shopping cart.
	 */
	function jxaddtocart()
	{
		if($this->session->userdata("user")==false)
			redirect("");		
		$this->load->library("cart");
		if($this->input->post("item")==false || $this->input->post("qty")==false)
			die("XX");
		$item=$this->input->post("item");
		$qty=$this->input->post("qty");
		$itemdetails=$this->dbm->getitemdetails($item);
		if($itemdetails==false)
			die("0");
		if($qty>$itemdetails['quantity'])
			die("1");
		$cart=array("id"=>$item,'qty'=>$qty,"price"=>$itemdetails['price'],"name"=>$itemdetails['name']);
		$flag=false;
		foreach($this->cart->contents() as $cartitem)
		{
			if($cartitem['id']==$item)
			{
				$flag=true;break;
			}
		}
		if($flag)
			die("2");
		$this->cart->insert($cart);
		echo "3";
//		print_r($this->cart->contents());
	}

	
	function search()
	{
		$keyword=$this->input->post("keyword");
		if(($data['user']=$this->session->userdata("user"))==false)
			redirect("");
		$data['menu']=$this->dbm->getmenu();
		$data['deals']=$this->dbm->searchdeals($keyword);
		$data['category']=$this->dbm->searchcats($keyword);
		$data['brands']=$this->dbm->searchbrands($keyword);
		$data['page']="search";
		$this->load->view("index",$data);
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
		$data['menu']=$this->dbm->getmenu();
					
		$dealdetails=$this->dbm->getdealdetails($id);
		$data['category']=$dealdetails[0]['category'];

//		print_r($dealdetails);
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
			$data['info']=array("We need your permission","Local Square will post this sale url. Please click below button to give permission. You can select which friends to post to in next step.<br><div align='center'><fb:prompt-permission perms=\"publish_stream\" next_fbjs=\"location='".site_url("fbthis/$dealid/$itemid")."'\"> Grant permission </fb:prompt-permission></div>");
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
		$data['friends']=$friends;
		$data['noheader']=true;
		$data['dealid']=$dealid;
		$data['itemid']=$itemid;
		$data['page']="fbthis";
		$this->load->view("index",$data);
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
			$msgs=array("Hot private sale in ","Cool private deals in ","Check out great deals in LocalSquare ","Private invitation link for LocalSquare ","Join me as friend and enjoy great sale ");
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
				die("Unable to connect to Twitter. Please try again by refreshing page");
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
//		print_r($dealdetails);
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
		$data['menu']=$this->dbm->getmenu();
		$data['categories']=$data['menu'][0];
		$user=$data['user']=$this->session->userdata("user");
		$deals=$this->dbm->getallactivedeals();
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
		$data['deals']=$ret;
		$data['brands']=$retb;
		$data['activedeals']=$this->dbm->getalldeals();
		$data['page']="showall";
		$this->load->view("index",$data);
	}
		
		/**
	 * Show all sales
	 * 
	 * Loads active and inactive sale details in view page.
	 * This is the page after user sign in.
	 * 
	 */
	function showall()
	{
		$data['menu']=$this->dbm->getmenu();
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
		$data['menu']=$this->dbm->getmenu();
				
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
				$msg='<div style="background:#fff;padding:30px 15px 0px 20px;font-family:\'trebuchet ms\';font-size:13px;color:#333;"><div align="left"><img src="'.base_url().'images/kinglogo.png"></div>
						<div align="center" style="padding-top:10px;"><div style="margin-top:15px;background:#ddd;width:600px;border:1px solid #aaa;padding:10px;font-size:15px;color:#444;margin-left:20px;font-weight:bold;-moz-border-radius:5px;border-radius:5px;font-family:\'trebuchet ms\';" align="left">Hi,<br>
						<br>I thought you might be interested in this sale. 
						<br>Please click below link to preview sale.
						<br><a style="color:#00f" href="'.$previewurl.'">'.$previewurl.'</a>
						<br><br>
						You can also sign up with localsquare by clicking below link and become my friend.
						<Div align="left"><a style="color:#00f;" href="'.site_url("invite/{$user['inviteid']}").'">'.site_url("invite/{$user['inviteid']}").'</a></div></div></div>
						<div align="right" style="padding-bottom:3px;padding-top:50px;font-size:11px;font-family:arial;">This email was sent by LocalSquare on behalf of '.$user['email'].'</div></div>';
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
	
}

?>
