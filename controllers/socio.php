<?php

include_once APPPATH.'controllers/tw/twitteroauth.php';
include_once APPPATH.'controllers/in/linkedin.php';


class Socio extends Controller{

	function __construct()
	{
		parent::Controller();
		header("Content-Type: text/html; charset=UTF-8");
		header("Cache-Control: private, no-cache, no-store, must-revalidate");
		header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
		header("Pragma: no-cache");

		$this->tw_apikey=TW_APPID;
		$this->tw_secretkey=TW_SECRET;
		
		$this->load->library("cart");
		$this->load->library("email");
		$this->load->library("form_validation");
		
		$this->load->model("viakingmodel","dbm");
		$this->load->model("sociomodel","sdbm");
		
		$this->load->library("pettakam",array("repo"=>"cache","ext"=>"pkm_snp"));
	}
	
	function fbinviteforbp()
	{
		$this->load->library("facebook",array('appId'=>FB_APPID,'secret'=>FB_SECRET));
		
		try{
				
				$user=$this->session->userdata("user");
							
				$fb=$this->facebook->getUser();
				if(!$fb)
					throw new FacebookApiException(null);

				$fbuser=$this->facebook->api("me");
				$data['fbemail']=$fbuser['email'];
					
				if(!$user)
				{
					if($this->db->query("select 1 from king_users where email=?",$fbuser['email'])->num_rows()!=0)
						$this->db->query("update king_users set special_id=? where email=?",array($fb,$fbuser['email']));
					else
						$this->dbm->newspecialuser($fbuser['name'],$fbuser['email'],randomChars(8),2,$fb);
					$user=$this->dbm->getuserbyemail($fbuser['email']);
				}
				
				if($this->sdbm->do_fb_friends($user['userid']))
				{
				$friends=$this->facebook->api("me/friends");
				if(isset($friends['data']))
					$friends=$friends['data'];
				else $friends=array();

				$friendslist=$this->insertnewfacebookers($friends);
				
				if($this->db->query("select 1 from king_fb_friends where userid=?",$user['userid'])->num_rows()==0)
					$this->db->query("insert into king_fb_friends(friends,userid,update_on) values(?,?,?)",array(implode(",",$friendslist),$user['userid'],time()));
				else 
					$this->db->query("update king_fb_friends set friends=?,update_on=? where userid=?",array(implode(",",$friendslist),time(),$user['userid']));
				}
					
				$friends=$this->sdbm->get_fb_friends($user['userid']);

				$as=array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
				foreach($as as $alpha)
					$ret[$alpha]=array();
				foreach($friends as $c)
				{
					$alpha=strtolower($c['name']{0});
					if(!isset($ret[$alpha]))
					{
						$ret[$alpha]=array();
					}
					$ret[$alpha][]=$c;
				}
				$data['friends']=$ret;
				
				$this->load->view("body/cws_fb_sel",$data);
				
		}catch(FacebookApiException $e)	{
			$data['info']=array("Facebook Login Error","Sorry, you didn't authorize us or some problem in contacting Facebook. Please try again later");
			$r=$this->load->view("body/info",$data,true);
			echo '<div style="background:#fff;">'.$r.'</div>';
			return;
		}
	}
	
	function auth($id="")
	{
		$id=strtolower($id);
		switch($id)
		{
			case "fb":
				$this->fbauth();
				return;
			case "gm":
				$this->gmauth();
				return;
			case "in":
				$this->inauth();
				return;
			case "tw":
				$this->twauth();
				return;
			default:
				show_404();
		}
	}
	
	private function gmauth()
	{
		if(isset($_COOKIE['fcauth'.GM_SITEID]))
		{
			$auth=$_COOKIE['fcauth'.GM_SITEID];
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
				$this->dbm->newspecialuser($resp->displayName,"",randomChars(10),3,$uid);
				$userid=$this->dbm->checkspecialuser($uid,3);
			}
			if($userid!=false)
			{
				$this->login_user($userid);
				$this->dbm->redirect_login();
			}
		}
		else
			redirect("");
	}
	
	private function inauth()
	{
		$API_CONFIG = array(
					    'appKey'       => IN_APPID,
						'appSecret'    => IN_SECRET,
	  					'callbackUrl'  => site_url('auth/in')."?".LINKEDIN::_GET_RESPONSE."=true"
  						);
		define('CONNECTION_COUNT', 20);
		define('PORT_HTTP', '80');
		define('PORT_HTTP_SSL', '443');
		define('UPDATE_COUNT', 10);

		$linkedin = new LinkedIn($API_CONFIG);
	try{
      $_GET[LINKEDIN::_GET_RESPONSE] = (isset($_GET[LINKEDIN::_GET_RESPONSE])) ? $_GET[LINKEDIN::_GET_RESPONSE] : '';
      if(!$_GET[LINKEDIN::_GET_RESPONSE]) {
        $response = $linkedin->retrieveTokenRequest();
        if($response['success'] === TRUE) {
          // store the request token
          $session['oauth']['linkedin']['request'] = $response['linkedin'];
          $this->session->set_userdata("LIN_session",$session);
          // redirect the user to the LinkedIn authentication/authorisation page to initiate validation.
          header('Location: ' . LINKEDIN::_URL_AUTH . $response['linkedin']['oauth_token']);
        } else 
        	show_error("Unable to contact LinkedIN. Please try again later");
      } else {
      	$session=$this->session->userdata("LIN_session");
        // LinkedIn has sent a response, user has granted permission, take the temp access token, the user's secret and the verifier to request the user's real secret key
        $response = $linkedin->retrieveTokenAccess($session['oauth']['linkedin']['request']['oauth_token'], $session['oauth']['linkedin']['request']['oauth_token_secret'], $_GET['oauth_verifier']);
        if($response['success'] === TRUE) {
          // the request went through without an error, gather user's 'access' tokens
          $session=$this->session->userdata("LIN_session");
          $session['oauth']['linkedin']['access'] = $response['linkedin'];
          // set the user as authorized for future quick reference
          $session['oauth']['linkedin']['authorized'] = TRUE;
          $this->session->set_userdata("LIN_session",$session);
          
        } else 
        	show_error("Unable to contact LinkedIN. Please try again later");
         $response = $linkedin->profile('~:(id,first-name,last-name,picture-url)');
         if($response['success'] === TRUE) {

          $resp=json_decode($response['linkedin'],true);
          
		if(!($userid=$this->dbm->checkspecialuser($resp['id'],4)))
		{
          $this->dbm->newspecialuser($resp['firstName']." ".$resp['lastName'],"",randomChars(8),4,$resp['id']);
          $userid=$this->dbm->checkspecialuser($resp['id'],4);
		} 
          $this->login_user($userid);
          $this->dbm->redirect_login();
         } else  
        	show_error("Unable to contact LinkedIN. Please try again later");
      }
	}catch(LinkedInException $e)
	{
		echo $e->getMessage();
		show_error("Sorry! something went wrong while contacting LinkedIn. Please try again later.");
	}
	}
	
	private function fbauth()
	{
		$this->load->library("facebook",array('appId'=>FB_APPID,'secret'=>FB_SECRET));
		$user=$this->facebook->getUser();
		if($user)
		{
			$this->fblogin();
			return;
		}
		$params=array(
						'scope'	=>	"email, publish_stream",
						'redirect_uri' => site_url("fblogin")
					);
		$fb_url=$this->facebook->getLoginUrl($params);
		redirect($fb_url);
		
		//dont execute
		$data['page']="echo";
		$data['echo']='<h2 style="margin-top:20px;margin-bottom:40px;">Please wait while we redirect you to Facebook</h2><script>$(function(){location.href="'.$fb_url.'";});</script>';
		$this->load->view("index",$data);
	}
	
	function fblogin()
	{
		$this->load->library("facebook",array('appId'=>FB_APPID,'secret'=>FB_SECRET));
		$fb=$this->facebook->getUser();
		if(!$fb)
		{
			$data['info']=array("Err! Login Failed!","Please login to your Facebook account and give permission. Click <a href='".site_url("fbauth")."'>here to contine</a>");
			$data['page']="info";
			$this->load->view("index",$data);
			return;
		}
		$login=false;
		$user=$this->dbm->getspecialuser($fb);
		if(empty($user))
		{
			try{
				$fbuser=$this->facebook->api("/me");
				$friends=$this->facebook->api("/me/friends");
				if(isset($friends['data']))
					$friends=$friends['data'];
				else $friends=array();
				$email=$fbuser['email'];
				$uid=$this->db->query("select userid from king_users where email=?",$email)->row_array();
				if(!empty($uid))
				{
					$this->db->query("update king_users set special_id=? where userid=? limit 1",array($fb,$uid['userid']));
					$login=true;
				$friendslist=$this->insertnewfacebookers($friends);
				if($this->db->query("select 1 from king_fb_friends where userid=?",$uid['userid'])->num_rows()==0)
					$this->db->query("insert into king_fb_friends(friends,userid,update_on) values(?,?,?)",array(implode(",",$friendslist),$uid['userid'],time()));
				else 
					$this->db->query("update king_fb_friends set friends=?,update_on=? where userid=?",array(implode(",",$friendslist),time(),$uid['userid']));
				}
			}catch (FacebookApiException $e)
			{
				show_error("Unable to connect to Facebook. Please try again later");
			}
		}
		else
			$login=true;
		if(!$login)
		{
			try{
				$fbuser=$this->facebook->api("/me");
				$friends=$this->facebook->api("/me/friends");
				if(isset($friends['data']))
					$friends=$friends['data'];
				else $friends=array();
			}catch (FacebookApiException $e)
			{
				show_error("Unable to connect to Facebook. Please try again later");
			}
			
			$uid['userid']=$this->dbm->newspecialuser($fbuser['name'],$fbuser['email'],randomChars(8),2,$fb);

			$friendslist=$this->insertnewfacebookers($friends);
			if($this->db->query("select 1 from king_fb_friends where userid=?",$uid['userid'])->num_rows()==0)
				$this->db->query("insert into king_fb_friends(friends,userid,update_on) values(?,?,?)",array(implode(",",$friendslist),$uid['userid'],time()));
			else 
				$this->db->query("update king_fb_friends set friends=?,update_on=? where userid=?",array(implode(",",$friendslist),time(),$uid['userid']));
		}
		$userid=$this->db->query("select userid from king_users where special_id=?",$fb)->row()->userid;
		$this->login_user($userid);
		$this->dbm->redirect_login();
	}
	
	private function insertnewfacebookers($friends)
	{
			$friendslist=array();
			foreach($friends as $f)
				$friendslist[]=$f['id'];
			if(!empty($friendslist))
				$facebookers_raw=$this->db->query("select fbid from king_facebookers where fbid in (".implode(",",$friendslist).")")->result_array();
			else 
				$facebookers_raw=array();
			$facebookers=array();
			foreach($facebookers_raw as $face)
				$facebookers[]=$face['fbid'];
			foreach($friends as $friend)
			{
				if(array_search($friend['id'],$facebookers)!==false)
					continue;
				$this->db->query("insert into king_facebookers(fbid,name) values(?,?)",array($friend['id'],$friend['name']));
			}
			return $friendslist;
	}
	
	private function twauth()
	{
		$twitter = new TwitterOAuth($this->tw_apikey, $this->tw_secretkey);		
		$request_token = $twitter->getRequestToken();
		if($request_token==false)
			show_error("Unable to connect to Twitter. Please try again by refreshing page");
//		var_dump($request_token);exit;
		$this->session->set_userdata("tw_token",$request_token);
		$token=$request_token['oauth_token'];
		$tw_authUrl= $twitter->getAuthorizeURL($token);		
		header("location:$tw_authUrl");
		echo "Connecting Twitter... Please wait";
	}
	
	function twsignin()
	{
			if(empty($_GET))
				redirect("");
			$token=$_GET['oauth_token'];
			$tw_token=$this->session->userdata("tw_token");
			$twitter = new TwitterOAuth($this->tw_apikey, $this->tw_secretkey, $token,$tw_token['oauth_token_secret']);
			$access_token=$twitter->getAccessToken();
			if($access_token==false)
				redirect("");
			$this->session->set_userdata(array("tw_redirect"=>true));
			$this->session->set_userdata(array("tw_accesstoken"=>$access_token));
			$content = $twitter->get('account/verify_credentials');
			$uid=$content->id;
			$userid=$this->dbm->checkspecialuser($uid,1);
			if($userid==false)
			{
				$inps=array($content->name,"",md5(randomChars(8)),1,$uid,randomChars(10));
				$this->db->query("insert into king_users(name,email,password,special,special_id,inviteid) values(?,?,?,?,?,?)",$inps);
				$userid=$this->dbm->checkspecialuser($uid,1);
				
				$this->dbm->createreferralcoupon($inps[0],$userid);
			}
			if($userid!=false)
			{
				$this->login_user($userid);
				$this->dbm->redirect_login();
			}
	}
	
	
	private function login_user($uid,$email=false)
	{
		$user=$this->dbm->getuserbyid($uid);
		$user['corp']="None";
		$this->session->set_userdata(array("user"=>$user));
		$boarder=$this->db->query("select br.username,br.pic,u.name,u.userid from king_boarders br join king_users u on u.userid=br.userid where br.userid=?",$user['userid'])->row_array();
		if(!empty($boarder))
			$this->session->set_userdata("boarder",$boarder);
		$this->dbm->userlog($user['userid']);
		$this->dbm->loadcart();
	}
	
	
	
	
}