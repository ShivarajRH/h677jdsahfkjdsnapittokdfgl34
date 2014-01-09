<?php
/**
 * ViaBazaar Facebook App
 * 
 * Controller for facebook app
 * 
 * @author vimal
 * @since Aug 2010
 * @version 0.0.1
 */


class Fb extends Controller{
	
//	private $fb_app="300389379755";
//	private $fb_secret="7092299fa8a946bcb1def3de6a31cba3";

	private $fb_url;

//	private $fb_app="236060061498";				//test (localhost)
//	private $fb_secret="9692638175a5b2d442672c53a9ce636b";	//test
	
	private $fb,$fb_session;
	
	function __construct()
	{
		parent::Controller();
		$this->load->library("facebook",array(
		  'cookie' => true,
		));	
		if($this->facebook->getApiSecret()=="9692638175a5b2d442672c53a9ce636b")
			$this->fb_url="test_vb";
		else
			$this->fb_url="viabazaar";
		$this->load->model("fbmodel","dbm");
		$this->load->model("ptmodel","pdbm");
		
		if($this->uri->segment(2)=="procinvite")
			$this->session->set_userdata("fb_invite",$this->uri->segment(3));
		
		$this->fb_session = $this->facebook->getSession();
		if(!$this->fb_session)
			$this->die_userlogin();
		  try {
			    $uid = $this->facebook->getUser();
		  } catch (FacebookApiException $e) {
		  		$this->die_userlogin();
		  }
		  
		$user=$this->session->userdata("fb_user");
		
		if($user==false && $this->uri->segment(2)!="login")
			$this->die_userlogin();
		
		if($user!=false && $this->uri->segment(2)!="checkextdet" && $this->uri->segment(2)!="updatedet")
		{
			if(!isset($user['mobile']))
				$user['mobile']=0;
			if($user['email']=="" || $user['mobile']==0)
				redirect("fb/checkextdet");
		}
	}
	
	function pr($id)
	{
		$this->session->set_userdata("fb_invite",$id);
		$this->die_userlogin();
	}
	
	private function die_userlogin()
	{
		$this->session->unset_userdata("fb_user");
		die("<script>top.location.href='".$this->facebook->getLoginUrl(array('req_perms'=>'publish_stream,email,user_birthday',"cancel_url"=>"http://apps.facebook.com/".$this->fb_url."/","next"=>"http://apps.facebook.com/".$this->fb_url."/login/"))."';</script>");
	}
	
	private function consts($d=array())
	{
		$d['fb_url']=$this->fb_url;
		$d['session']=$this->fb_session;
//		print_r($this->fb_session);
//		echo $_COOKIE['fbs_'.$this->facebook->getAppId()];
		return $d;
	}
	
	function index()
	{
		if($this->session->userdata("fb_user"))
			$this->showdeals();
		else
			$this->die_userlogin();
	}
	
	function invite_old()
	{
		$data=$this->consts();
		$user=$this->session->userdata("fb_user");
		$data['print']='<fb:serverFbml>
<script type="text/fbml">
<fb:fbml>
    <fb:request-form
        method=\'POST\'
        action=\''.site_url("fb").'/didInvite/\'
        type=\'Join Via Bazaar\'
        content=\'I am using Via Bazaar to buy branded products at discounted price. Would you like to join? 
            <fb:req-choice url="http://apps.facebook.com/'.$this->fb_url.'/procinvite/'.sha1($user['inviteid']).'" 
                label="Accept Invitation" />
            <fb:req-choice url="http://apps.facebook.com/'.$this->fb_url.'/about" 
                label="Whats ViaBazaar?" />\'
        <fb:multi-friend-selector
            actiontext="Invite your friends to join Via Bazaar.">
    </fb:request-form>
</fb:fbml>
</script>
</fb:serverFbml>
		';
		$this->load->view("fb_index",$data);
	}
	
	function login()
	{
		$data=$this->consts();
		try { $uid = $this->facebook->getUser(); }
		catch (FacebookApiException $e) { $this->die_userlogin(); }
		$fuser=$user=$this->pdbm->getuser($uid);
		$invid=false;
		$invfid=false;
		$share=false;
		if(empty($user))
		{
			try{ $me=$this->facebook->api("/me");
				$fri=$this->facebook->api(array("method"=>"friends.get","uids"=>$uid));
				$friends=count($fri);
			}
			catch(FacebookApiException $e){ $this->die_userlogin();	}
			$his="his";
			$gend="male";
			if(isset($me['gender']))
				$gend=$me['gender'];
			if($gend=="female")
				$his="her";
			$email="";
			if(isset($me['email']))
				$email=$me['email'];
			$rfid=0;
			$invid=$this->session->userdata("fb_invite");
			if($invid!=false && !empty($invid))
			{
				$inv=$this->pdbm->getuserbyinviteid($invid);
				$invfid=$inv['fid'];
				if(!empty($inv) && isset($inv['fid']))
				{
					$rfid=$inv['fid'];
					$this->pdbm->referral_incr($rfid);
					$this->pdbm->calcpts($rfid);
				}
			}
			$this->session->unset_userdata("fb_invite");
			$this->pdbm->createuser($uid,$gend,$rfid,$friends,$email);
			$fuser=$user=$this->dbm->getuser($uid);
			$share=true;
		}
		else
		{
			try{
				$me=$this->facebook->api("/me");
				$email="";
				if(isset($me['email']))
					$email=$me['email'];
			}catch(FacebookApiException $e)
			{
				$this->die_userlogin();
			}
			try{
				$fri=$this->facebook->api(array("method"=>"friends.get","uids"=>$uid));
				$friends=count($fri);
			}catch(FacebookApiException $e)
			{
				$friends=0;
			}
			$this->pdbm->updateuser($uid,$friends,$email);
		}
			$fb_user=$this->facebook->getUser();
			$userid=$this->dbm->checkspecialuser($fb_user,2);
			if($userid==false)
			{
				if($invid!=false && $invfid!=false)
					$inv=$invfid;
				else
					$inv=0;
				$share=true;
				$this->dbm->newspecialuser($fb_user,$me['name'],$me['email'],2,randomChars(10),$inv);
				$userid=$this->dbm->checkspecialuser($fb_user,2);
				$this->session->set_userdata("gotoinvite","yes");
			}
			if($userid!=false)
			{
				$this->dbm->updateuserlogin($userid,$me);
				$user=$this->dbm->getuserbyid($userid);
				$this->session->set_userdata(array("fb_user"=>$user));
			}
		if($share)
		{
			$r=$this->facebook->api("/me/feed","POST",array("picture"=>"http://viabazaar.in/images/logo.png","name"=>"Join ViaBazaar","caption"=>"Quality brands at discounted price!","description"=>"With huge user-base of Via, we can guarantee that you always get best deals in the country here.","link"=>"http://apps.facebook.com/viabazaar/pr/{$fuser['invite_id']}"));
			$this->pdbm->share_incr($uid,1);
		}
//		$this->session->sess_destroy();
		$this->session->unset_userdata("fb_invite");
		$user['fid']=$this->facebook->getUser();
		$user['inviteid']=$fuser['invite_id'];
		$user['friends']=$fuser['friends'];
		$user['points']=(integer)$fuser['loyalty_pts'];
		$this->session->set_userdata("fb_user",$user);
		echo "<script>top.location.href='http://apps.facebook.com/{$this->fb_url}/'</script>";
//		redirect("via");
	}
	
	function updatedet()
	{
		$user=$this->session->userdata("fb_user");
		if($user==false || !$_POST)
			redirect("fb");
		$uid=$user['userid'];
		$data=$this->consts();
		$this->dbm->updateext($uid,$this->input->post("email"),$this->input->post("mobile"));
		$this->session->unset_userdata("fb_user");
		$ud=$this->dbm->getuserbyid($uid);
		$this->session->set_userdata(array("fb_user"=>$ud));
		$data['page']="info";
		if(!$this->session->userdata("gotoinvite"))
		$data['info']=array("Thanks!","Your info saved<script>function redire(){top.location='http://apps.facebook.com/".$this->fb_url."';} window.setTimeout('redire()',2000);</script>");
		else
		{
			$data['info']=array("Thanks!","Your info saved<script>function redire(){top.location='http://apps.facebook.com/".$this->fb_url."/invite';} window.setTimeout('redire()',2000);</script>");
			$this->session->unset_userdata("gotoinvite");
		}
		$this->load->view("fb_index",$data);
	}
	
	
	function checkextdet()
	{
		$data=$this->consts();
		$user=$this->session->userdata("fb_user");
		if($user!=false)
		{
			if(!isset($user['mobile']))
				$user['mobile']=0;
			if($user['email']=="" || $user['mobile']==0)
			{
				$d='<form id="extform" action="'.site_url("fb/updatedet").'" method="post">
				<div style="color:red;display:none" id="exterror"></div>
				<table cellspacing="5" border="0">
				<tr><td align="left">Your Email </td><td>: <input type="text" name="email" value="'.$user['email'].'"></td></tr>
				<tr><td align="left">Your Mobile Number </td><td>: <input type="text" name="mobile" value="'.($user['mobile']==0?"":$user['mobile']).'"></td></tr>
				<tr><td></td><td align="right"><input type="submit" value="Submit"></td></tr></table>
				<div style="font-size:10px;">Please enter correct info. These details are used in checkout and order status notifications.<br>We won\'t share these info with anyone in this World!</div></form><script>
				$(function(){
				$("#cartlink").attr("href","javascript:void(0)").hide();
				$("#extform").submit(function(){
					msg="";
					if(!is_mobile_strict($("input[name=mobile]",$(this)).val()))
						msg="<div>Invalid mobile number</div>";
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
				$data['info']=array("Please enter these details",$d);
				$data['page']="info";
				$this->load->view("fb_index",$data);
				return;
			}
			else
			redirect("fb/deals");
		}
	}
	
	function showdeals()
	{
		$data=$this->consts();
		$user=$data['user']=$this->session->userdata("fb_user");
		if($user===false)
			redirect("fb");
		$data['menu']=$this->dbm->getmenu();
		$data['categories']=$data['menu'][0];
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
		$data['deals']=$ret;
		$data['brands']=$retb;
		$data['activedeals']=$this->dbm->getalldeals();
		$data['page']="showall";
		$this->load->view("fb_index",$data);
	}
	
	function groupdeals()
	{
		$data=$this->consts();
		$user=$data['user']=$this->session->userdata("fb_user");
		if($user===false)
			redirect("fb");
		$data['menu']=$this->dbm->getmenu();
		$data['categories']=$data['menu'][0];
		$deals=$this->dbm->getallactivegroupdeals();
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
		$data['deals']=$ret;
		$data['brands']=$retb;
		$data['activedeals']=$this->dbm->getalldeals();
		$data['page']="showall";
		$this->load->view("fb_index",$data);
	}
	
	function deal($url)
	{
		$id=$this->dbm->getitemforurl($url);
		if($id==0)
			show_404();
		$this->showsaleitem($id,$url);
	}
	
	function showsaleitem($id,$url="")
	{
		$data=$this->consts();
		$user=$this->session->userdata("fb_user");
		if($user!==false)
			$data['user']=$user;
		else
			$this->session->set_userdata(array("logred"=>"deal/".$url));
		$data['menu']=$this->dbm->getmenu();
					
		$itemdetails=$this->dbm->getitemdetails($id);
		if($itemdetails==false)
		{
			$data['info']=array("Deal not available","This sale item might be expired or deal not yet started");
			$data['page']="info";
			$this->load->view("fb_index",$data);
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
		$data['extradeals']=$this->dbm->getextradeals($itemdetails['catid'],$itemdetails['brandid'],$itemdetails['vendorid'],$itemdetails['id']);
		$data['itemdetails']=$itemdetails;
//		if($itemdetails['dealtype']==0)
		$data['page']="showitem";
//		else if($itemdetails['dealtype']==1)
//		$data['page']="groupsale";
		$data['title']="{$itemdetails['name']} for Rs {$itemdetails['price']} only";
		$this->load->view("fb_index",$data);
	}
	
	function didInvite()
	{
		$data=$this->consts();
		$info=array();
		if(!$this->input->post("ids"))
		{
			$info[0]="Please consider sending invites later";
			$info[1]="You get VIA points for sending invitation to friends. By building your friends community in ViaBazaar, its easy to get all benefits from VIA.";
		}
		else
		{
			try{ $uid=$this->facebook->getUser(); }
			catch(FacebookApiException $e){ $this->die_userlogin();	}
			$ids=$this->input->post("ids");
			$invs=count($ids);
			$this->pdbm->invite_incr($uid,$invs);
			$this->pdbm->calcpts($uid);
			$info[0]="Invitations sent";
			$info[1]="Thanks for sending invitation requests to your friends";
		}
		$data['info']=$info;
		$data['page']="info";
		$this->load->view("fb_index",$data);
	}
	
	function invite()
	{
		$data=$this->consts();
		$user=$this->session->userdata("fb_user");
		$data['print']='
<fb:serverFbml>
<script type="text/fbml">
<fb:fbml>
    <fb:request-form
        method=\'POST\'
        action=\''.site_url("fb").'/didInvite/\'
        type=\'Join ViaBazaar\'
        content=\'I am using ViaBazaar to buy quality brands (group sales and brand sales) at discounted price. I invite you to join and become my referal. 
            <fb:req-choice url="http://apps.facebook.com/'.$this->fb_url.'/pr/'.$user['inviteid'].'/" 
                label="Accept Invitation" />\'
        <fb:multi-friend-selector
            actiontext="Invite your friends to join ViaBazaar"/>
    </fb:request-form>
</fb:fbml>
</script>
</fb:serverFbml>
		';
		$data['ptsys']=$this->pdbm->getptsys();
		$data['page']="share";
		$this->load->view("fb_index",$data);
	}
	
	function invite_fr()
	{
		$data=$this->consts();
		$id=$this->input->post("id");
		if($id!=false)
		{
		$this->load->model("hoteldeals","hdbm");
		$data['deal']=$deal=$this->hdbm->getDeal($id);
		if(empty($deal))
		{
			$data['page']="info";
			$data['info']=array("Invalid offer","Sorry! the deal is not valid anymore");
			$this->load->view("fb_index",$data);
			return;
		}
		}
		$data['ptsys']=$this->pdbm->getptsys();
		$data['print']='<div style="padding:20px;">
<fb:serverFbml><script type="text/fbml">
<fb:fbml>
<style>
			.c-fb-submit{
				font-size:14px;
				font-weight:bold;
				color:#fff;
				background:#3B5998;
				padding:5px 10px;
			}
</style>
		<form action="'.site_url("fb/pr_invite_fr").'" id="share_fr_frm" method="post">';
		if($id!==false)
			$data['print'].='<input name="id" value="'.$id.'" type="hidden">';
		$data['print'].='<b>Please select your friends </b>
		<div>Start typing your friend\'s name and select as many as you want</div>
		<fb:multi-friend-input width="550px" border_color="#8496ba"/>
		<div align="center" style="margin-top:50px;"><input class="c-fb-submit" type="submit" value="Share with friends"></div>
		</form>
		</fb:fbml></script></fb:serverFbml>';
		$data['page']="share";
		$this->load->view("fb_index",$data);
	}
	
	function invite_wall()
	{
		$data=$this->consts();
		$user=$this->session->userdata("fb_user");
		$d_id=$id=$this->input->post("id");
		$fid=$this->facebook->getUser();
		if($d_id!=false)
		{
			$this->load->model("hoteldeals","hdbm");
			$data['deal']=$deal=$this->hdbm->getDeal($id);
			$msg=array("message"=>"Got a good hotel deal!","picture"=>"http://test2.via.com/resources/images/hotel_deal_images/thumbs/{$deal['image_name']}.jpg","name"=>$deal['hotel_deal_title'],"description"=>$deal['hotel_deal_desc'],"link"=>"http://test2.via.com/index.php/hotel/view/".$deal['hotel_id'],"caption"=>"Hotel Deal");
			if(empty($deal))
				die();
		}
		else
			$msg=array("picture"=>"http://viabazaar.in/images/logo.png","name"=>"Join ViaBazaar","caption"=>"High quality brands for sale!","description"=>"ViaBazaar brings you high quality brands in discounted price. With huge user-base of Via, we guarantee that you always get best deals in the country here","link"=>"http://apps.facebook.com/viadotcom/pr/{$user['inviteid']}");
		try{
			$r=$this->facebook->api("/me/feed","POST",$msg);
		}catch(FacebookApiException $e){die("Error occurred. Please try again");}
		$this->pdbm->share_incr($fid,$user['friends']);
		$this->pdbm->calcpts($fid);
		$data['print']="<div style='padding-top:10px;margin:30px 0px;'><h2>Thanks!</h2>"."Message posted in your wall</div>";
		$data['page']="share";
		$data['ptsys']=$this->pdbm->getptsys();
		$this->load->view("fb_index",$data);
	}

	function pr_invite_fr()
	{
		$data=$this->consts();
		if(!$_POST || $this->session->userdata("fb_user")==false)
			redirect("fb/invite");
		try{$fid=$this->facebook->getUser();}catch(FacebookApiException $e){$this->die_userlogin();}
		
		$user=$this->session->userdata("fb_user");
		if(!$this->session->userdata("fb_user"))
			die();
		$id=$this->input->post("id");
		if($id!=false)
		{
		$this->load->model("hoteldeals","hdbm");
		$data['deal']=$deal=$this->hdbm->getDeal($id);
		$msg=array("message"=>"Got a good hotel deal!","picture"=>"http://test2.via.com/resources/images/hotel_deal_images/thumbs/{$deal['image_name']}.jpg","name"=>$deal['hotel_deal_title'],"description"=>$deal['hotel_deal_desc'],"link"=>"http://test2.via.com/index.php/hotel/view/".$deal['hotel_id'],"caption"=>"Hotel Deal");
		if(empty($deal))
			die();
		}
		else
		$msg=array("message"=>"I am using ViaBazaar to buy high quality brand products at discounted price","picture"=>"http://viabazaar.in/images/logo.png","name"=>"Please join ViaBazaar and become my referral","description"=>"ViaBazaar brings you high quality brands in discounted price. With huge user-base of Via, we guarantee that you always get best deals in the country here","link"=>"http://apps.facebook.com/viadotcom/pr/{$user['inviteid']}");
		
		$ids=$this->input->post("ids");
		foreach($ids as $id)
		{
			try{
			$r=$this->facebook->api("/$id/feed","POST",$msg);
			}catch(FacebookApiException $e){die("Error occured. Please try again.");}
		}
		$this->pdbm->friend_share_incr($fid,count($ids));
		$this->pdbm->calcpts($fid);
		$data['print']="<div style='padding-top:10px;margin:30px 0px;'><h2>Thanks for sharing with friends</h2>"."You get VIA points for your referrals, which can be redeemed for VIA products</div>";
		$data['page']="share";
		$data['ptsys']=$this->pdbm->getptsys();
		$this->load->view("fb_index",$data);
	}
	
	
}