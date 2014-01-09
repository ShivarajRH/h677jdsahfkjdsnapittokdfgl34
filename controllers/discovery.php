<?php

class Discovery extends Controller{

	public static $CURL_OPTS = array(	//fast curl
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 60,
    CURLOPT_USERAGENT      => 'snapittoday-discovery-0.1',
    CURLOPT_HTTPHEADER 	   => array('Expect:'),
   	CURLOPT_FOLLOWLOCATION => true
    );
	
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
		$this->load->model("tagmodel","tdbm");
		$this->load->library("pettakam",array("repo"=>"cache","ext"=>"pkm_snp"));
		$this->session->unset_userdata("login_redirect");
		if($this->session->userdata("user"))
			$this->checknewuser();
	}
	
	private function checknewuser()
	{
		if($this->uri->segment(2)=="jx_checkua")
			return;
		if($this->session->userdata("boarder"))
			return;
		$user=$this->session->userdata("user");
		$boarder=$this->db->query("select br.username,br.pic,u.name,u.userid from king_boarders br join king_users u on u.userid=br.userid where br.userid=?",$user['userid'])->row_array();
		if(empty($boarder))
		{
			if($_POST)
			{
				$username=$this->input->post("username");
				if(strlen($username)>=5 && $this->db->query("select 1 from king_boarders where username=?",$username)->num_rows()==0)
				{
					$pic=$this->tdbm->createpic($user['userid']);
					$this->db->query("insert into king_boarders(userid,pic,username,created_on) values(?,?,?,?)",array($user['userid'],$pic,$username,time()));
					$boarder=$this->db->query("select br.username,br.pic,u.name,u.userid from king_boarders br join king_users u on u.userid=br.userid where br.userid=?",$user['userid'])->row_array();
					$this->session->set_userdata("boarder",$boarder);
					$this->tdbm->createboard("Love to snap!",1,false);
					redirect("discovery/user/{$username}/feed");
				}
			}
			$username=strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', $user['name']));
			if($this->db->query("select 1 from king_boarders where username=?",$username)->num_rows()==0)
				$data['username']=$username;
			$data['page']="discovery/newuser";
			die($this->load->view("index",$data,true));
		}else
		$this->session->set_userdata("boarder",$boarder);
	}
	
	function tag($url="")
	{
		if(empty($url))
			show_404();
		$data['tag']=$tag=$this->tdbm->gettag($url);
		if(empty($tag))
			show_404();
		$data['page']="discovery/tag";
		$this->load->view("index",$data);
	}
	
	function board($url="",$followers="")
	{
		if(empty($url))
			show_404();
		$data['board']=$board=$this->tdbm->getboardbyurl($url);
		if(empty($board))
			show_404();
		if(!empty($followers))
			$data['followers']=$this->tdbm->getboardfollowers($board['bid']);
		$data['page']="discovery/board";
		$this->load->view("index",$data);
	}
	
	function relatedeals($url)
	{
		$name=$this->db->query("select tb.name,t.src_url as url from king_tags_in_boards tb join king_tags t on t.tid=tb.tid where tb.url=?",$url)->row_array();
		if(empty($name))
			die;
		$deals=$this->tdbm->searchdeals($name['name'],$name['url']);
		if(count($deals)<10)
		{
			$rdeals=$this->tdbm->rand_deals(10-count($deals));
			$deals=array_merge($deals,$rdeals);
		}
		$this->load->view("body/discovery/subs/rel_deals",array("deals"=>$deals));
	}
	
	function user($username="",$page="boards")
	{
		if(empty($username))
			show_404();
		
		$user=$this->session->userdata("user");
		$data['boarder']=$boarder=$this->tdbm->getboarderbyusername($username);
		if(empty($data['boarder']))
			show_404();
			
		if($page=="boards")
			$data['boards']=$this->tdbm->getboards($boarder['userid']);
		elseif($page=="tags")
			$data['tags']=$this->tdbm->gettags($boarder['userid']);
		elseif($page=="feed" && $user && $user['userid']==$boarder['userid'])
			$data['feeds']=$this->tdbm->parse_activity($this->tdbm->getallactivity($boarder['userid']));
		elseif($page=="loves")
			$data['loves']=$this->tdbm->getloves($boarder['userid']);
		elseif($page=="followers")
			$data['followers']=$this->tdbm->getuserfollowers($boarder['userid']);
		elseif($page=="following")
		{
			$data['following']=$this->tdbm->getuserfollowing($boarder['userid']);
			$data['bfollowing']=$this->tdbm->getuserfollowingboard($boarder['userid']);
		}
		else
			show_404();

		$data['activity']=$this->tdbm->parse_activity($this->tdbm->getactivity($boarder['userid']));
		
		$data['page']="discovery/user";
		$this->load->view("index",$data);
	}
	
	function jx_checkua()
	{
		if(!$_POST)
			die;
		$username=$this->input->post("username");
		if(strlen($username)<5)
			die("len");
		if(preg_match('/[^A-Za-z0-9_]/',$username))
			die('fmt');
		if($this->db->query("select 1 from king_boarders where username=?",$username)->num_rows()!=0)
			die('nop');
		die('ava');
	}
	
	function index()
	{
		$data['pagetitle']="Discover new lifestyle";
		$data['tags']=$this->tdbm->getrecenttags();
		$data['page']="discovery/tags";
		$this->session->set_userdata("visited","yes");
		$this->load->view("index",$data);
	}
	
	function jx_recent()
	{
		if(!$this->session->userdata("visited"))
			die(json_encode(array()));
		$p=$this->input->post("p");
		$tags=$this->tdbm->getrecenttags($p,20);
		foreach($tags as $i=>$t)
			$tags[$i]['created_on']=date("g:ia d/m/y",$t['created_on']);
		echo json_encode($tags);
	}
	
	function jx_getimages()
	{
		$user=$this->checkauth();
		$url=$this->input->post("url");
		if(empty($url))
		{
			echo json_encode(array());
			die;
		}
		
		preg_match('@^(?:http://)?([^/]+)@i',$url, $matches);
		if(!isset($matches[1]))
			die(json_encode(array("inv")));

		$host = $matches[1];
		if(stripos($host,".xxx")!==false || stripos($host,"porn")!==false || stripos($host,"sex")!==false)
			die(json_encode(array("bla")));
			
		$imgs=array();
		$curl=curl_init($url);
		curl_setopt_array($curl, self::$CURL_OPTS);
		$html=curl_exec($curl);
		$header=curl_getinfo($curl,CURLINFO_CONTENT_TYPE);
		if(stristr($header,"image"))
			$imgs[]=$url;
		if(!empty($html))
		{
			$doc=new DOMDocument();
			@$doc->loadHTML($html);
			$xml=@simplexml_import_dom($doc);
			$images=@$xml->xpath('//img');
			foreach ($images as $img) 
			{
				$url=(string)$img['src'];
				if(!startsWith($url, "http"))
				{
					if(!isset($url{0}))
						continue;
					if($url{0}!="/")
						$url="/$url";
					$url="http://$host".$url;
				}
				$imgs[]=$url;
			}
		}
		echo json_encode($imgs);
	}
	
	function jx_upload()
	{
		if(!isset($_FILES['img']))
			die('');
		$this->load->library("thumbnail");
		if($this->thumbnail->check($_FILES['img']['tmp_name']))
		{
			$pid=randomChars(16);
			$this->tdbm->createpicfortag($_FILES['img']['tmp_name'],$pid);
			die("<script>window.top.upload_done(1,'$pid')</script>");
		}else
		die("<script>top.upload_done(0,'0')</script>");
	}
	
	function createtag()
	{
//		print_r($_POST);die;
		$retag=$item=false;
		$user=$this->checkauth();
		
		if($this->input->post("retag")=="yes")
			$retag=true;
			
		$itemid=$this->input->post("itemid");
		if(!empty($itemid))
			$item=true;
		else
			$itemid=false;
			
		$pid=$this->input->post("pid");
		if(!empty($_POST['img']) && !$retag && !$item)
		{
			$pid=randomChars(16);
			file_put_contents("images/tags/$pid.jpg",$this->tdbm->geturlcont($this->input->post("img")));
			$this->tdbm->createpicfortag("images/tags/$pid.jpg",$pid);
		}
		if($item)
		{
			$pid=randomChars(16);
			$itempic=$this->input->post("itempic");
			copy("images/items/big/$itempic.jpg","images/tags/$pid.jpg");
			$this->tdbm->createpicfortag("images/tags/$pid.jpg",$pid);
		}
		
		$url=$this->tdbm->createtag($this->input->post("tagname"),$pid,$this->input->post("board"),$this->input->post("url"),$retag,$this->input->post("from"),$itemid);
		redirect("discovery/tag/$url");
	}
	
	function createboard()
	{
		$user=$this->checkauth();
		redirect("discovery/board/".$this->tdbm->createboard($this->input->post("bname"),$this->input->post("bcat"),$this->input->post("bpublic")=="yes"?true:false));
	}

	function writecomment()
	{
		$user=$this->checkauth();
		if(!$_POST)
			die;
		$this->tdbm->commenttag();
		redirect("discovery/tag/".$this->input->post("url"));
	}
	
	function follow_boarder()
	{
		$user=$this->checkauth();
		if(!$_POST)
			die;
		$url=$this->input->post("url");
		$this->tdbm->follow_boarder($url);
		redirect("discovery/user/$url");
	}
	
	function follow_board()
	{
		$user=$this->checkauth();
		if(!$_POST)
			redirect("discovery");
		$url=$this->input->post("url");
		$this->tdbm->follow_board($url);
		redirect("discovery/board/$url");
	}
	
	function jx_loveit()
	{
		$user=$this->checkauth();
		if(!$_POST)
			die;
		$turl=$this->input->post("url");
		$this->tdbm->loveit($turl);
		redirect("discovery/tag/$turl");
	}
	
	function change_cat()
	{
		if(!$_POST)
			redirect("discovery");
		$cat=$this->input->post("disc_cat");
		setcookie('disc_cat',$cat,time()+(10*360*24*60*60),"/");
		redirect("discovery");
	}
	
	function set_redirect()
	{
		if($_POST)
		{
			if($this->input->post("url")=="")
				$_POST['url']="#redirected";
			$this->session->set_userdata("login_redirect",$this->input->post("url"));
		}
		redirect("signup");
	}
	
	function jx_love_item()
	{
		$user=$this->checkauth();
		if(!$_POST || ($itemid=$this->input->post("itemid"))==false)
			die("Err!");
		$b=$this->tdbm->loveitem($itemid);
		if($b)
			echo "hmm";
		else echo "tbf";
	}
	
	private function checkauth()
	{
		if(!$this->session->userdata("user"))
			redirect("discovery");
		if(!$this->session->userdata("boarder"))
			redirect("discovery");	
		return $this->session->userdata("user");
	}
}