<?php

class Tagmodel extends Model
{
	
	function getrecenttags($p=0,$l=30)
	{
		$cat=0;
		if(isset($_COOKIE['disc_cat']))
			$cat=$_COOKIE['disc_cat'];
		$sql="select t.src_url,u.name as user,br.username,tb.created_on,b.name as board,b.url as boardurl,tb.tbid,t.pic,t.retags,tb.loves,tb.comments,tb.name,tb.url from king_tags_in_boards tb join king_tags t on t.tid=tb.tid join king_boards b on b.bid=tb.bid join king_users u on u.userid=tb.userid join king_boarders br on br.userid=tb.userid";
		if($cat)
			$sql.=" where b.catid=?";
		$sql.=" order by tb.tbid desc limit ".($p*$l).",$l";
		$tags=$this->db->query($sql,$cat)->result_array();
		foreach($tags as $i=>$t)
			$tags[$i]['comments_data']=$this->db->query("select u.name as user,c.comment,c.created_on from king_tag_comments c join king_users u on u.userid=c.userid where status=1 and tbid=?",$t['tbid'])->result_array();
		return $tags;
	}
	
	function loveitem($itemid)
	{
		$user=$this->session->userdata("user");
		if($this->db->query("select 1 from king_item_lovers where itemid=?",$itemid)->num_rows()!=0)
			return false;
		$this->db->query("insert into king_item_lovers(itemid,userid) values(?,?)",array($itemid,$user['userid']));
		$this->db->query("update king_dealitems set loves=loves+1 where id=? limit 1",$itemid);
		return true;
	}
	
	function geturlcont($url)
	{
		$opts= array(	//fast curl
				    CURLOPT_CONNECTTIMEOUT => 10,
				    CURLOPT_RETURNTRANSFER => true,
				    CURLOPT_TIMEOUT        => 60,
				    CURLOPT_USERAGENT      => 'snapittoday-discovery-0.1',
				   	CURLOPT_FOLLOWLOCATION => true
			    );
		$curl=curl_init($url);
		curl_setopt_array($curl, $opts);
		return curl_exec($curl);
	}
	
	function createpic($uid)
	{
		$this->load->library("thumbnail");

		$pic=randomChars(12);
		
		$user=$this->db->query("select * from king_users where userid=?",$uid)->row_array();
		if(empty($user))
			return "";

		switch($user['special'])
		{
			case 0:
			case 3:
			case 4:
				$profile=$this->db->query("select * from king_profiles where userid=?",$uid)->row_array();
				if(!empty($profile) && $profile['pic']!="")
					$pic=$profile['pic'];
				else
					copy("images/default_people.jpg","images/people/$pic.jpg");
			break;
			case 1:
				file_put_contents("images/people/$pic.jpg", $this->geturlcont("http://api.twitter.com/1/users/profile_image/{$user['special_id']}.json?size=bigger"));
			break;
			case 2:
				file_put_contents("images/people/$pic.jpg", $this->geturlcont("https://graph.facebook.com/{$user['special_id']}/picture?type=large"));
			break;
		}
		
		if(!isset($opic))
		{
			$img="images/people/$pic.jpg";
			$t_img="images/people/{$pic}_t.jpg";
			
			if($this->thumbnail->check($img))
			{
				$this->thumbnail->create(array("source"=>$img,"dest"=>$t_img,"width"=>50,"max_height"=>60));
				$this->thumbnail->create(array("source"=>$img,"dest"=>$img,"width"=>180,"max_height"=>190));
			}
			$opic=$pic;
		}
		$this->db->query("update king_profiles set pic=? where userid=? limit 1",array($opic,$uid));
		return $opic;
	}
	
	function newactivity($type,$userid,$boardid="",$tagid="",$userid2="")
	{
		$sql="insert into king_board_activity(type,userid,boardid,tagid,userid2) values(?,?,?,?,?)";
		$this->db->query($sql,array($type,$userid,$boardid,$tagid,$userid2));
	}
	
	function createtag($name,$pid,$bid,$src_url="",$retag=false,$from=0,$itemid=false)
	{
		$url=str_replace(" ","-",preg_replace('/[^a-zA-Z0-9_\-\s]/', '',substr($name,0,40)))."-".rand(10,999);
		$user=$this->session->userdata("user");
		
		$board=$this->db->query("select userid from king_boards where bid=?",$bid)->row_array();
		if(empty($board))
			return;
		if($board['userid']!=$user['userid'] && $board['public']==0)
			show_error("Unable to add a tag. Please try again later");
		
		if(!$retag)
		{
			$sql="insert into king_tags(userid,name,pic,itemid,src_url,created_on) values(?,?,?,?,?,?)";
			$this->db->query($sql,array($user['userid'],$name,$pid,$itemid,$src_url,time()));
			$tid=$this->db->insert_id();
		}else{
			$r_owner=$this->db->query("select u.name,u.email from king_boards b join king_users u on u.userid=b.userid where b.bid=?",$from)->row_array();
			$tid=$this->db->query("select tid from king_tags where pic=?",$pid)->row()->tid;
			$this->db->query("update king_tags set retags=retags+1 where tid=? limit 1",$tid);
			$r_tag=$this->db->query("select name,url from king_tags_in_boards where bid=? and tid=?",array($from,$tid))->row_array();
			$this->email($r_owner['email'],"Hi {$r_owner['name']}, {$user['name']} had re tagged your tag {$r_tag['name']}",$this->load->view("mails/discovery/retag",array("user2"=>array("name"=>$user['name'],"username"=>$user['username']),"user"=>$r_owner['name'],"tag"=>$r_tag),true));
		}
		$sql="insert into king_tags_in_boards(bid,tid,userid,name,url,`from`,created_on) values(?,?,?,?,?,?,?)";
		$this->db->query($sql,array($bid,$tid,$user['userid'],$name,$url,$from,time()));
		$tbid=$this->db->insert_id();
		if(!$retag)
			$this->newactivity(2, $user['userid'],$bid,$tbid);
		else
			$this->newactivity(3, $user['userid'],$from,$tbid); 
		$this->db->query("update king_boarders set tags=tags+1 where userid=? limit 1",$user['userid']);
		$this->db->query("update king_boards set tags=tags+1 where bid=? limit 1",$bid);
		return $url;
	}
	
	function createboard($name,$cat,$public)
	{
		$user=$this->session->userdata("user");
		$boarder=$this->session->userdata("boarder");
		$url=$boarder['username']."_".str_replace(" ","-",preg_replace('/[^a-zA-Z0-9_\-\s]/', '',substr($name,0,40))).rand(10,99);
		$this->db->query("insert into king_boards(name,catid,url,userid,public,created_on) values(?,?,?,?,?,?)",array($name,$cat,$url,$user['userid'],$public,time()));
		$this->newactivity(1,$user['userid'],$this->db->insert_id());
		$this->db->query("update king_boarders set boards=boards+1 where userid=?",$user['userid']);
		return $url;
	}
	
	function getboarderbyusername($username)
	{
		return $this->db->query("select u.*,b.* from king_boarders b join king_users u on u.userid=b.userid where b.username=?",$username)->row_array();
	}
	
	function setuser($userid,&$name,&$username,&$pic)
	{
		$username=$user=$pic="";
		$u=$this->db->query("select b.pic,u.name,b.username from king_users u join king_boarders b on b.userid=u.userid where u.userid=?",$userid)->row_array();
		if(!empty($u))
		{
			$pic=$u['pic'];
			$username=$u['username'];
			$name=$u['name'];
		}
	}
	
	function setboard($boardid,&$board,&$boardurl)
	{
		$board=$boardurl="";
		$b=$this->db->query("select b.name,b.url from king_boards b where bid=?",$boardid)->row_array();
		if(!empty($b))
		{
			$boardurl=$b['url'];
			$board=$b['name'];
		}
	}
	
	function settag($tagid,&$tag,&$tagurl,&$pic)
	{
		$tag=$tagurl="";
		$b=$this->db->query("select t.pic,b.name,b.url from king_tags_in_boards b join king_tags t on t.tid=b.tid where b.tbid=?",$tagid)->row_array();
		if(!empty($b))
		{
			$tagurl=$b['url'];
			$tag=breakstring($b['name'],20);
			$pic=$b['pic'];
		}
	}
	
	function parse_activity($acts)
	{
		if(empty($acts))
			return "";
		$ret="";
		foreach($acts as $act)
		{
			$ret.="<div>";
			switch ($act['type'])
			{
				case 1:
					$this->setuser($act['userid'],$name,$username,$pic);
					$this->setboard($act['boardid'],$board,$boardurl);
					$ret.="<a href='".site_url("discovery/board/$boardurl")."' class='img'><img src='".IMAGES_URL."people/".$pic."_t.jpg'></a><a href='".site_url("discovery/user/$username")."'>$name</a> created board <a href='".site_url("discovery/board/$boardurl")."'>$board</a>";
					break;
				case 2:
					$this->setuser($act['userid'],$name,$username,$pic);
					$this->settag($act['tagid'],$tag,$tagurl,$pic);
					$this->setboard($act['boardid'],$board,$boardurl);
					$ret.="<a class='img' href='".site_url("discovery/tag/$tagurl")."'><img src='".IMAGES_URL."tags/thumb/".$pic.".jpg'></a><b>$name</b> tagged <a href='".site_url("discovery/tag/$tagurl")."'>$tag</a> to <a href='".site_url("discovery/board/$boardurl")."'>$board</a>";
					break;
				case 3:
					$this->setuser($act['userid'],$name,$username,$pic);
					$this->settag($act['tagid'],$tag,$tagurl,$pic);
					$this->setboard($act['boardid'],$board,$boardurl);
					$ret.="<a class='img' href='".site_url("discovery/tag/$tagurl")."'><img src='".IMAGES_URL."tags/thumb/".$pic.".jpg'></a><b>$name</b> retagged <a href='".site_url("discovery/tag/$tagurl")."'>$tag</a> from <a href='".site_url("discovery/board/$boardurl")."'>$board</a>";
					break;
				case 4:
					$this->setuser($act['userid'],$name,$username,$pic);
					$this->setboard($act['boardid'],$board,$boardurl);
					$ret.="<a class='img' href='".site_url("discovery/board/$boardurl")."'><img src='".IMAGES_URL."people/".$pic."_t.jpg'></a><b>$name</b> is now following <a href='".site_url("discovery/board/$boardurl")."'>$board</a>";
					break;
				case 5:
					$this->setuser($act['userid'],$name,$username,$pic);
					$this->setuser($act['userid2'],$name2,$username2,$pic);
					$ret.="<a class='img' href='".site_url("discovery/user/$username2")."'><img src='".IMAGES_URL."people/".$pic."_t.jpg'></a><b>$name</b> is now following <a href='".site_url("discovery/user/$username2")."'>$name2</a>";
					break;
				case 6:
					$this->setuser($act['userid'],$name,$username,$pic);
					$this->settag($act['tagid'],$tag,$url,$pic);
					$ret.="<a class='img' href='".site_url("discovery/tag/$url")."'><img src='".IMAGES_URL."tags/thumb/".$pic.".jpg'></a><b>$name</b> loved the tag <a href='".site_url("discovery/tag/$url")."'>$tag</a>";
					break;
			}
			$ret.="<span style='display:block;' class='clear'></span></div>";
		}
		return $ret;
	}	
		
	function getactivity($userid)
	{
//		$sql="select a.*,b.name as board,c.name as owner,c.usernameu.name as user,t.name as tag from king_board_activity a outer join king_user u on u.userid=a.userid2 outer join king_boards b on b.bid=a.boardid outer join king_tags t on t.tid=a.tagid outer join king_users c on c.userid=a.userid where a.userid=? order by a.time desc limit 5"
		$sql="select * from king_board_activity where userid=? order by id desc limit 9";
		return $this->db->query($sql,$userid)->result_array();
	}
	
	function getallactivity($userid)
	{
		$sql="select a.* from king_boarder_followers bf join king_board_activity a on a.userid=bf.userid where bf.follower=? order by a.id desc limit 40";
		return $this->db->query($sql,array($userid,$userid))->result_array();
	}
	
	
	function getboards($userid)
	{
		$sql="select * from king_boards where userid=? order by bid desc";
		$boards=$this->db->query($sql,$userid)->result_array();
		foreach($boards as $i=>$b)
		{
			$pics_r=$this->db->query("select t.pic from king_tags_in_boards tb join king_tags t on t.tid=tb.tid where tb.bid=? order by tb.tbid desc limit 9",$b['bid'])->result_array();
			$pics=array();
			foreach($pics_r as $p)
				$pics[]=$p['pic'];
			$boards[$i]['imgs']=$pics;
		}
		return $boards;
	}
	
	function getboardbyurl($url)
	{
		$board=$this->db->query("select b.public,b.url,b.bid,b.followers,b.tags,b.name,u.name as user,br.username,br.pic as userpic from king_boards b join king_users u on u.userid=b.userid join king_boarders br on br.userid=b.userid where b.url=?",$url)->row_array();
		if(empty($board))
			return array();
		$sql="select t.src_url,u.name as user,br.username,tb.created_on,b.name as board,b.url as boardurl,tb.tbid,t.pic,t.retags,tb.loves,tb.comments,tb.name,tb.url from king_tags_in_boards tb join king_tags t on t.tid=tb.tid join king_boards b on b.bid=tb.bid join king_users u on u.userid=tb.userid join king_boarders br on br.userid=tb.userid where tb.bid=? order by tb.tbid desc";
		$tags=$this->db->query($sql,$board['bid'])->result_array();
		$board['tag_data']=$tags;
		return $board;
	}
	
	function follow_board($url)
	{
		$user=$this->session->userdata("user");
		$bid=$this->db->query("select bid from king_boards where url=?",$url)->row_array();
		if(empty($bid))
			return;
		$bid=$bid['bid'];
		$this->db->query("insert into king_board_followers(follower,bid) values(?,?)",array($user['userid'],$bid));
		$this->db->query("update king_boards set followers=followers+1 where bid=? limit 1",$bid);
		$this->db->query("update king_boarders set following=following+1 where userid=? limit 1",$user['userid']);
		$this->newactivity(4,$user['userid'],$bid);
	}
	
	function follow_boarder($url)
	{
		$user=$this->session->userdata("user");
		$boarder=$this->session->userdata("boarder");
		
		$userid=$this->db->query("select userid from king_boarders where username=?",$url)->row_array();
		if(empty($userid))
			return;
		$userid=$userid['userid'];
		
		$own=$this->db->query("select email,name from king_users where userid=?",$userid)->row_array();
		
		$this->db->query("insert into king_boarder_followers(userid,follower) values(?,?)",array($userid,$user['userid']));
		$this->db->query("update king_boarders set followers=followers+1 where userid=? limit 1",$userid);
		$this->db->query("update king_boarders set following=following+1 where userid=? limit 1",$user['userid']);
		$this->newactivity(5,$user['userid'],0,0,$userid);
		
		$this->email($own['email'],"Hi {$own['name']}, {$user['name']} is following you on Snapittoday.com",$this->load->view("mails/discovery/follow_user",array("user2"=>array("username"=>$boarder['username'],"name"=>$user['name']),"user"=>$own['name']),true));
	}
	
	function getuserfollowers($userid)
	{
		return $this->db->query("select br.username,br.pic,u.name from king_boarder_followers f join king_boarders br on br.userid=f.follower join king_users u on u.userid=f.follower where f.userid=?",$userid)->result_array();
	}
	
	function getuserfollowingboard($userid)
	{
		return $this->db->query("select b.url,b.name,u.pic from king_board_followers f join king_boards b on b.bid=f.bid join king_boarders u on u.userid=b.userid where f.follower=?",$userid)->result_array();
	}
	
	function getuserfollowing($userid)
	{
		return $this->db->query("select br.username,br.pic,u.name from king_boarder_followers f join king_boarders br on br.userid=f.follower join king_users u on u.userid=f.follower where f.follower=?",$userid)->result_array();
	}
	
	function getboardfollowers($bid)
	{
		return $this->db->query("select br.username,br.pic,u.name from king_board_followers f join king_boarders br on br.userid=f.follower join king_users u on u.userid=f.follower where f.bid=?",$bid)->result_array();
	}
	
	function rand_deals($n)
	{
		$sql="select rand() as rand, item.groupbuy,cat.name as category,cat.url as caturl,item.url,item.orgprice,item.name as itemname,item.available,item.id as itemid,item.quantity,brand.name as brandname,item.price,cat.name as category,deal.tagline,deal.description,deal.dealid,deal.startdate,deal.enddate,item.pic,brand.id as brandid,brand.logoid as brandlogoid,deal.dealtype,item.name from king_deals as deal join king_categories as cat on cat.id=deal.catid join king_brands as brand on brand.id=deal.brandid join king_dealitems as item on item.dealid=deal.dealid where deal.publish=1 and ".time()." between deal.startdate and deal.enddate order by rand asc limit $n";
		$ret=$this->db->query($sql)->result_array();
		return $ret;
	}

	function searchdeals($name,$url)
	{
		
		$key=trim(preg_replace('/[^a-zA-Z0-9_\-]/',' ',$name)." ".preg_replace('/[^a-zA-Z0-9_\-]/',' ',$url));
		

		$sql="select deal.catid,item.groupbuy,cat.name as category,cat.url as caturl,item.url,item.orgprice,item.name as itemname,item.available,item.id as itemid,item.quantity,brand.name as brandname,item.price,cat.name as category,deal.tagline,deal.dealid,deal.startdate,deal.enddate,item.pic,brand.id as brandid,brand.logoid as brandlogoid,deal.dealtype,item.name from king_search_index ind join king_dealitems as item on item.id=ind.itemid join king_deals as deal on deal.dealid=item.dealid join king_categories as cat on cat.id=deal.catid join king_brands as brand on brand.id=deal.brandid where MATCH (ind.name,ind.keywords) AGAINST (?) limit 25";
		$ret=$this->db->query($sql,array($key,$key))->result_array();
		return $ret;
		
		
//		$key="%$okey%";
//		$key=str_replace(" ","%", $key);
		$deals=$ret=array();
		if(!empty($url))
		{
		preg_match('@^(?:http://)?([^/]+)@i',$url, $matches);
		if(isset($matches[1]))
		{
			$host = $matches[1];
			$surl=substr($url,strlen($host)-1);
			$sql="select item.id,cat.name as category,cat.url as caturl,item.url,item.orgprice,item.name as itemname,item.price,item.pic,item.name from king_deals as deal join king_categories as cat on cat.id=deal.catid join king_dealitems as item on item.dealid=deal.dealid where item.url=? ";
			$sql.="and deal.publish=1 and ".time()." between deal.startdate and deal.enddate limit 10";
			$deals=$this->db->query($sql,$surl)->result_array();
		}
		}
		
		$okey=$name." ".str_replace("-"," ",str_replace("/"," ",str_replace("'","",$url)));
		$keys=explode(" ",$okey);
		
		$sql="select id from king_brands where ";
		$i=0;
		foreach($keys as $k)
		{
			$k=trim($k);
			if(empty($k) || strlen($k)<5)
				continue;
			if($i>0)
			$sql.=" or ";
			$sql.="name like '%".mysql_escape_string($k)."%'";
			$i++;
		}
		$brands_r=$this->db->query($sql)->result_array();
		$brands=array();
		foreach($brands_r as $b)
			$brands[]=$b['id'];
			
		if(!empty($brands))
		{	
			$sql="select item.id,cat.name as category,cat.url as caturl,item.url,item.orgprice,item.name as itemname,item.price,item.pic,item.name from king_deals as deal join king_categories as cat on cat.id=deal.catid join king_dealitems as item on item.dealid=deal.dealid where deal.brandid in (".implode(",",$brands).")";
			$sql.="and deal.publish=1 and ".time()." between deal.startdate and deal.enddate limit 10";
			$ret=$this->db->query($sql)->result_array();
			$deals=array_merge($deals,$ret);
		}
		
		if(count($deals)<10 && !empty($brands))
		{
			$cats_r=$this->db->query("select catid from king_catbrand where brandid in (".implode(",",$brands).")")->result_array();
			$cats=array();
			foreach($cats_r as $c)
				$cats[]=$c['catid'];
			if(!empty($cats))
			{
				$sql="select item.id,cat.name as category,cat.url as caturl,item.url,item.orgprice,item.name as itemname,item.price,item.pic,item.name from king_deals as deal join king_categories as cat on cat.id=deal.catid join king_dealitems as item on item.dealid=deal.dealid where deal.catid in (".implode(",",$cats).") ";
				$sql.="and deal.publish=1 and ".time()." between deal.startdate and deal.enddate limit 10";
				$ret=$this->db->query($sql)->result_array();
				$deals=array_merge($deals,$ret);
			}
		}
		
		
		if(count($deals)<10)
		{
		$sql="select cat.name as category,cat.url as caturl,item.url,item.orgprice,item.name as itemname,item.price,item.pic,item.name from king_deals as deal join king_categories as cat on cat.id=deal.catid join king_dealitems as item on item.dealid=deal.dealid where (";
		$i=0;
		foreach($keys as $k)
		{
			$k=trim($k);
			if(empty($k) || strlen($k)<5)
				continue;
			if($i>0)
			$sql.=" or ";
			$sql.="deal.keywords like '%$k%' or deal.tagline like '%$k%'";
			$i++;
		}
		$sql.=") and deal.publish=1 and ".time()." between deal.startdate and deal.enddate limit 10";
		$ret=$this->db->query($sql)->result_array();
		$deals=array_merge($deals,$ret);
		}
		
		$ret=array();
		$i=0;
		foreach($deals as $d)
		{
			if(!isset($ret[$d['url']]))
			{
				$ret[$d['url']]=$d;
				$i++;
				if($i==10) break;
			}
		}
		
		return $ret;
	}
	
	function createpicfortag($img,$pid)
	{
		$this->load->library("thumbnail");
		list($width,$height)=@getimagesize($img);
		if($width>590)
			$width=590;
		$this->thumbnail->create(array("source"=>$img,"dest"=>"images/tags/$pid.jpg","width"=>$width));
		$this->thumbnail->create(array("source"=>"images/tags/$pid.jpg","dest"=>"images/tags/small/$pid.jpg","width"=>175));
		$this->thumbnail->create(array("source"=>"images/tags/small/$pid.jpg","dest"=>"images/tags/thumb/$pid.jpg","width"=>50));
		return $pid;
	}
	
	function getloves($userid)
	{
		$sql="select t.src_url,u.name as user,br.username,tb.created_on,b.name as board,b.url as boardurl,tb.tbid,t.pic,t.retags,tb.loves,tb.comments,tb.name,tb.url from king_tag_lovers l join king_tags_in_boards tb on tb.tbid=l.tbid join king_tags t on t.tid=tb.tid join king_boards b on b.bid=tb.bid join king_users u on u.userid=tb.userid join king_boarders br on br.userid=tb.userid where l.userid=?";
		$tags=$this->db->query($sql,$userid)->result_array();
		if(empty($tags))
			return array();
		foreach($tags as $i=>$t)
			$tags[$i]['comments_data']=$this->db->query("select u.name as user,c.comment,c.created_on from king_tag_comments c join king_users u on u.userid=c.userid where status=1 and tbid=?",$t['tbid'])->result_array();
		return $tags;
	}
	
	
	function gettags($userid)
	{
		$sql="select t.src_url,u.name as user,br.username,tb.created_on,b.name as board,b.url as boardurl,tb.tbid,t.pic,t.retags,tb.loves,tb.comments,tb.name,tb.url from king_tags_in_boards tb join king_tags t on t.tid=tb.tid join king_boards b on b.bid=tb.bid join king_users u on u.userid=tb.userid join king_boarders br on br.userid=tb.userid where tb.userid=? order by tb.tbid desc";
		$tags=$this->db->query($sql,$userid)->result_array();
		if(empty($tags))
			return array();
		foreach($tags as $i=>$t)
			$tags[$i]['comments_data']=$this->db->query("select u.name as user,c.comment,c.created_on from king_tag_comments c join king_users u on u.userid=c.userid where status=1 and tbid=?",$t['tbid'])->result_array();
		return $tags;
	}
	
	function commenttag()
	{
		$user=$this->session->userdata("user");
		$boarder=$this->session->userdata("boarder");
		
		$url=$this->input->post("url");
		if(($tb=$this->db->query("select url,name,userid,tbid from king_tags_in_boards where url=?",$url)->row_array()) && empty($tb))
			return;

		$tbid=$tb['tbid'];
		
		$own=$this->db->query("select name,email from king_users where userid=?",$tb['userid'])->row_array();
		
		$comment=strip_tags($this->input->post("comment"));
		$sql="insert into king_tag_comments(tbid,userid,comment,status,created_on) values(?,?,?,?,?)";
		$this->db->query($sql,array($tbid,$user['userid'],$comment,1,time()));
		
		$msg=$this->load->view("mails/discovery/comment",array("user"=>$own['name'],"user2"=>array("username"=>$boarder['username'],"name"=>$user['name']),"tag"=>array("url"=>$tb['url'],"name"=>$tb['name'])),true);
		$this->email($own['email'], "{$user['name']} commented on your tag", $msg);
	}
	
	function gettag($url)
	{
		$sql="select t.tid,tb.from,tb.bid,tb.userid,f.name as fromboard,f.url as fromboardurl,fu.name as fromuser,fb.username as fromusername,br.pic as userpic,t.src_url,u.name as user,br.username,tb.created_on,b.name as board,b.url as boardurl,tb.tbid,t.pic,t.retags,tb.loves,tb.comments,tb.name,tb.url from king_tags_in_boards tb join king_tags t on t.tid=tb.tid join king_boards b on b.bid=tb.bid join king_users u on u.userid=tb.userid join king_boarders br on br.userid=tb.userid left outer join king_boards f on f.bid=tb.from left outer join king_users fu on fu.userid=f.userid left outer join king_boarders fb on fb.userid=f.userid where tb.url=?";
		$tags=$this->db->query($sql,$url)->row_array();
		$tags['comments_data']=$this->db->query("select br.username,br.pic,u.name as user,c.comment,c.created_on from king_tag_comments c join king_users u on u.userid=c.userid join king_boarders br on br.userid=c.userid where status=1 and tbid=?",$tags['tbid'])->result_array();
		$tags['lovers']=$this->db->query("select b.pic,b.username from king_tag_lovers l join king_boarders b on b.userid=l.userid where l.tbid=? limit 20",$tags['tbid'])->result_array();
		$tags['retagers']=$this->db->query("select tb.created_on,b.url as boardurl,b.name as board,u.name as user,br.pic,br.username from king_tags_in_boards tb join king_users u on u.userid=tb.userid join king_boards b on b.bid=tb.bid join king_boarders br on br.userid=tb.userid where tb.tid=?",$tags['tid'])->result_array();
		if(count($tags['retagers'])==1)
			unset($tags['retagers']);
		return $tags;
	}
	
	function loveit($url)
	{
		$user=$this->session->userdata("user");
		$tbid=$this->db->query("select tbid from king_tags_in_boards where url=?",$url)->row_array();
		if(empty($tbid))
			return;
		$tbid=$tbid['tbid'];
		if($this->db->query("select 1 from king_tag_lovers where userid=? and tbid=?",array($user['userid'],$tbid))->num_rows()!=0)
			return;
		$this->db->query("update king_tags_in_boards set loves=loves+1 where tbid=? limit 1",$tbid);
		$this->db->query("insert into king_tag_lovers(userid,tbid) values(?,?)",array($user['userid'],$tbid));
		$this->db->query("update king_boarders set loves=loves+1 where userid=?",$user['userid']);
		$this->newactivity(6, $user['userid'],0,$tbid);
	}
	
	
	function email($emails,$sub,$msg)
	{
		if(!is_array($emails))
			$emails=array($emails);
		foreach($emails as $email)
		{
			$config=array('mailtype'=>"html");
			$this->email->initialize($config);
			$this->email->from("support@snapittoday.com","Snapittoday");
			$this->email->to($email);
			$this->email->subject($sub);
			$this->email->message($msg);
			$this->email->send();
		}
	}
	
	
}