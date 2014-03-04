<?php

class Cron extends Controller{
	
	function __construct()
	{
		parent::__construct();
		$this->load->library("email");
		$this->load->model('erpmodel','erpm');
	}

	function test_c()
	{
		echo 'ok';
	}
	
	function check()
	{
		$this->cron_log(2,1);
		
		$valid=array("1"=>96,"2"=>1,"3"=>2,"4"=>4,"5"=>1440,"6"=>1440,"7"=>1440,"8"=>1440,"9"=>4,"10"=>24,"11"=>96,"12"=>288,"13"=>288,"14"=>1440,"15"=>1,"16"=>1,"17"=>1,"18"=>1,"19"=>1,"20"=>1,"21"=>1,"22"=>1);
		$names=array("1"=>"Cashback (Group buy)","2"=>"Log Check","3"=>"Sr(i)Sitemap","4"=>"Dubious","5"=>"Promommer","6"=>"Short Message Service","7"=>"FBUser-er","8"=>"FB Mailer","9"=>"Search Indexer","10"=>"Cashback","11"=>"Failed transaction notifier","12"=>"Ticket Mail Crawler","13"=>"Out of stock marker","14"=>"Picasso","15"=>"PNH Executive Paid SMS Notification","16"=>"Payment Collection Notification SMS","17"=>"SMS Franchise Current Balance","18"=>"Employee Task Status Update","19"=>"End day Franchise order notification","20"=>"Total Sales to Executive-Endday","21"=>"Total Sales to TM-Endday","22"=>"Task Remainder");
		
		$counts=$this->db->query("select * from cron_log")->result_array();
		$this->db->query("update cron_log set count=0,start=0");
		$emails=array("shariff@localcircle.in","sri@localcircle.in","sushma@thecouch.in","gova@localcircle.in");
		$msg=$this->load->view("mails/cronlog",array("valid"=>$valid,"names"=>$names,"counts"=>$counts),true);
		$this->email($emails,"Cron status report ".date("r"), $msg, array("cronlog@snapittoday.com","Cron Logger"));
		$this->cron_log(2);
	}
	
	function mail()
	{
		mail("vimal@localcircle.in","test","working");
	}
	
	function cshbk($pass="")
	{
		if($pass!="iuiisan9sdfsdfs9ufd9sodfoo903")
			die;
			die;
		$this->cron_log(1,1);
		$refunds=array();
		$coupons=array();
		$bps=$this->db->query("select * from king_m_buyprocess where status=0")->result_array();
		foreach($bps as $bp)
		{
			$expired=false;
			if($bp['expires_on']<time())
				$expired=true;
			else if($this->db->query("select 1 from king_buyprocess where bpid=? and status=0",$bp['id'])->num_rows()>0)
				continue;
			
			$bpid=$bp['id'];
			if($bp['quantity']==$bp['quantity_done'])
				$refunds[]=array("refund"=>$bp['refund'],"bpid"=>$bp['id'],"itemid"=>$bp['itemid']);
			else 
			{
				$item=$this->db->query("select price,slots from king_dealitems where id=?",$bp['itemid'])->row_array();
				$buyers=$bp['quantity_done'];
				$slots=unserialize($item['slots']);
		     	$nslots=array();
		     	$nslotprice=array();
		     	if(is_array($slots))
		     	foreach($slots as $sno=>$srs)
		     	{
		     		$nslots[]=$sno;
		     		$nslotprice[]=$srs;
		     	}
				$si=4053444;
		     	foreach($nslots as $si=>$ns)
				{
					if($buyers<$ns)
						break;
				}
				if(!isset($nslotprice[$si]))
					$slotprice=0;
				else
					$slotprice=$nslotprice[$si];
				if($slotprice!=0)
					$refund=$item['price']-$slotprice;
				else
					$refund=0;
				if($refund>0)
					$refunds[]=array("refund"=>$refund,"bpid"=>$bp['id'],"itemid"=>$bp['itemid']);
				else if($expired)
					$this->db->query("update king_m_buyprocess set status=2 where id=? limit 1",$bp['id']);
			}
		}
		$alphas=array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
		foreach($refunds as $r)
		{
			$refund=$r['refund'];
			$bpid=$r['bpid'];
			$itemid=$r['itemid'];
			foreach($this->db->query("select * from king_buyprocess where bpid=? and isrefund=1",$bpid)->result_array() as $bpu)
			{
				$code="SNP".rand(100,999).$alphas[rand(0,25)].$alphas[rand(0,25)].$alphas[rand(0,25)].rand(1,9);
				$userid=$bpu['userid'];
				$expires=mktime(0,0,0)+((COUPON_EXP_DAYS+1)*24*60*60);
				$inps=array($code,$refund*$bpu['quantity'],$userid,$itemid,time(),$expires);
				$this->db->query("insert into king_coupons(code,value,userid,itemid,created,expires) values(?,?,?,?,?,?)",$inps);
				$mail=array("refund"=>$refund*$bpu['quantity'],"coupon"=>$code);
				$mail['item']=$this->db->query("select name from king_dealitems where id=?",$itemid)->row()->name;
				$user=$this->db->query("select name,email from king_users where userid=?",$userid)->row();
				$mail['name']=$user->name;
				$this->email($user->email,"Your cashback for buying '{$mail['item']}' : {$code}",$this->load->view("mails/coupon",$mail,true));
			}
			$this->db->query("update king_m_buyprocess set refund_given=?,status=1 where id=? limit 1",array($refund,$bpid));
		}
		$this->cron_log(1);
	}
	
	function cashback($pass="")
	{
		if($pass!="242fwer23iwuefjw9teg")
			show_404();
		$this->cron_log(10,1);
		$ps=$this->db->query("select * from king_pending_cashbacks where status=0")->result_array();
		foreach($ps as $p)
		{
			if($p['orderid']==0)
			{
				$n_orders=$this->db->query("select 1 from king_orders where transid=?",$p['transid'])->num_rows();
				$c_orders=$this->db->query("select 1 from king_orders where status=3 and transid=?",$p['transid'])->num_rows();
				$s_orders=$this->db->query("select 1 from king_orders where status=2 and transid=?",$p['transid'])->num_rows();
				if($s_orders==$n_orders)
					$cutpc=0;
				elseif($c_orders==$n_orders)
					$cutpc=-1;
				elseif($s_orders+$c_orders==$n_orders)
				{
					$total=$this->db->query("select sum(quantity*i_price) as s from king_orders where transid=?",$p['transid'])->row()->s;
					$c_total=$this->db->query("select sum(quantity*i_price) as s from king_orders where transid=?",$p['transid'])->row()->s;
					$cutpc=$total-$c_total/$total*100; 
				}
				else
					continue;
				if($cutpc==-1)
					$this->db->query("update king_pending_cashbacks set status=2,actiontime=".time()." where transid=?",$p['transid']);
				else
				{
					$expires=$p['expires']+(ceil((time()-$p['time'])/24/60/60)*24*60*60);
					if($cutpc==0)
					$value=$p['value'];
					else
					$value=$p['value']-($p['value']*100/$cutpc);
					$this->db->query("update king_pending_cashbacks set status=1,actiontime=".time()." where id=? limit 1",$p['id']);
					$c=array($p['code'],0,$value,1,$p['userid'],$p['itemid'],$p['min'],time(),$expires,"cashback");
					$this->db->query("insert into king_coupons(code,type,value,mode,userid,itemid,min,created,expires,remarks)
																			values(?,?,?,?,?,?,?,?,?,?)",$c);
				}
			}
			else
			{
				$status=$this->db->query("select status from king_orders where id=?",$p['orderid'])->row()->status;
				if($status==2)
				{
					$this->db->query("update king_pending_cashbacks set status=1,actiontime=".time()." where id=? limit 1",$p['id']);
					$expires=$p['expires']+(ceil((time()-$p['time'])/24/60/60)*24*60*60);
					$value=$p['value'];
					$this->db->query("update king_pending_cashbacks set status=1,actiontime=".time()." where id=? limit 1",$p['id']);
					$c=array(strtoupper($p['code']),0,1,$value,$p['userid'],$p['itemid'],$p['min'],time(),$expires,"cashback");
					$this->db->query("insert into king_coupons(code,type,value,mode,userid,itemid,min,created,expires,remarks)
																			values(?,?,?,?,?,?,?,?,?,?)",$c);
				}
				elseif($status==3)
					$this->db->query("update king_pending_cashbacks set status=2,actiontime=".time()." where id=? limit 1",$p['id']);
			}
		}
		
		$ps=$this->db->query("select * from king_cashbacks where status=0")->result_array();
		foreach($ps as $p)
		{
				$n_orders=$this->db->query("select 1 from king_orders where transid=?",$p['transid'])->num_rows();
				$c_orders=$this->db->query("select 1 from king_orders where status=3 and transid=?",$p['transid'])->num_rows();
				$s_orders=$this->db->query("select 1 from king_orders where status=2 and transid=?",$p['transid'])->num_rows();
				if($s_orders==$n_orders)
					$cutpc=0;
				elseif($c_orders==$n_orders)
					$cutpc=-1;
				elseif($s_orders+$c_orders==$n_orders)
				{
					$total=$this->db->query("select sum(quantity*i_price) as s from king_orders where transid=?",$p['transid'])->row()->s;
					$c_total=$this->db->query("select sum(quantity*i_price) as s from king_orders where transid=?",$p['transid'])->row()->s;
					$cutpc=$total-$c_total/$total*100; 
				}
				else
					continue;
				if($cutpc==-1)
					$this->db->query("update king_cashbacks set status=3 where transid=?",$p['transid']);
				else
				{
					if($cutpc==0)
					$value=$p['amount'];
					else
					$value=$p['amount']-($p['amount']*100/$cutpc);
					$this->db->query("update king_cashbacks set amount={$value},status=1 where id=? limit 1",$p['id']);
				}
		}
		
		$ps=$this->db->query("select * from king_points where status=0")->result_array();
		
		foreach($ps as $p)
		{
			$orders=$this->db->query("select 1 from king_orders where transid=?",$p['transid'])->num_rows();
			$d_orders=$this->db->query("select 1 from king_orders where (status=2 or status=3) and transid=?",$p['transid'])->num_rows();
			if($orders == $d_orders)
			{
				$this->db->query("update king_points set status=1 where id=? limit 1",$p['id']);
				$this->db->query("update king_users set points=points+{$p['points']} where userid=? limit 1",$p['userid']);
			}
		}
		
		$this->cron_log(10);
	}
	
	function sitemap()
	{
		$this->cron_log(3,1);
		
		$menu=$this->db->query("select * from king_menu where status=1")->result_array();
		$cnt="";
		$vcnt="";
		foreach($menu as $m)
		{
			$cnt.=site_url($m['url'])."\n";
			$vcnt.=site_url("viewbymenu/".$m['url'])."\n";
			$vcnt.=site_url("viewbymenu/".$m['url'])."/page-2\n";
			$vcnt.=site_url("viewbymenu/".$m['url'])."/page-3\n";
			$vcnt.=site_url("viewbymenu/".$m['url'])."/page-4\n";
			$vcnt.=site_url("viewbymenu/".$m['url'])."/page-5\n";
			$vcnt.=site_url("viewbymenu/".$m['url'])."/page-6\n";
			$vcnt.=site_url("viewbymenu/".$m['url'])."/page-7\n";
			$vcnt.=site_url("viewbymenu/".$m['url'])."/page-8\n";
			$vcnt.=site_url("viewbymenu/".$m['url'])."/page-9\n";
			$vcnt.=site_url("viewbymenu/".$m['url'])."/page-10\n";
			$vcnt.=site_url("viewbymenu/".$m['url'])."/page-11\n";
			$vcnt.=site_url("viewbymenu/".$m['url'])."/page-12\n";
			$vcnt.=site_url("viewbymenu/".$m['url'])."/page-13\n";
			$vcnt.=site_url("viewbymenu/".$m['url'])."/page-14\n";
			$vcnt.=site_url("viewbymenu/".$m['url'])."/page-15\n";
		}
		$cats=$this->db->query("select * from king_categories")->result_array();
		foreach($cats as $c)
		{
			$cnt.=site_url($c['url'])."\n";
			$vcnt.=site_url("viewbycat/".$c['url'])."\n";
			$vcnt.=site_url("viewbycat/".$c['url'])."/page-2\n";
			$vcnt.=site_url("viewbycat/".$c['url'])."/page-3\n";
			$vcnt.=site_url("viewbycat/".$c['url'])."/page-4\n";
			$vcnt.=site_url("viewbycat/".$c['url'])."/page-5\n";
		}
			
		$brands=$this->db->query("select * from king_brands")->result_array();
		foreach($brands as $c)
		{
			$cnt.=site_url($c['url'])."\n";
			$vcnt.=site_url("viewbybrand/".$c['url'])."\n";
			$vcnt.=site_url("viewbybrand/".$c['url'])."/page-2\n";
			$vcnt.=site_url("viewbybrand/".$c['url'])."/page-3\n";
		}
			

		$deals=$this->db->query("select b.url as burl,m.url as murl, c.url as curl, i.url as url from king_deals d join king_dealitems i on i.dealid=d.dealid join king_menu m on m.id=d.menuid or m.id=d.menuid2 join king_brands b on b.id=d.brandid join king_categories c on c.id=d.catid where d.startdate<".time())->result_array();
		
		$ucats=array();
		foreach($deals as $i=>$d)
		{
			$deals[$i]['url']=str_replace("\n","",$d['url']);
			if(!isset($ucats[$d['murl']]))
				$ucats[$d['murl']]=array();
			if(!in_array($d['curl'],$ucats[$d['murl']]))
			{
				$ucats[$d['murl']][]=$d['curl'];
				$cnt.=site_url($d['murl']."/".$d['curl'])."\n";
				$vcnt.=site_url("viewbymenucat/".$d['murl']."/".$d['curl'])."\n";
			}
		}
		
		$ubrands=array();
		foreach($deals as $d)
		{
			if(!isset($ubrands[$d['murl']]))
				$ubrands[$d['murl']]=array();
			if(!in_array($d['burl'],$ubrands[$d['murl']]))
			{
				$ubrands[$d['murl']][]=$d['burl'];
				$cnt.=site_url($d['murl']."/".$d['burl'])."\n";
				$vcnt.=site_url("viewbymenubrand/".$d['murl']."/".$d['burl'])."\n";
			}
		}
		
		foreach($deals as $d)
			$cnt.=site_url($d['url'])."\n";
			
//		foreach($deals as $d)
//			$cnt.=site_url($d['murl']."/".$d['curl'])	

		$file=fopen(SITEMAP_LOC."sitemap.txt","w");
//		if(!$file)
//			die();
		fwrite($file, $cnt);
		fclose($file);
		
		$cnt="";
		$bcats=array();
		foreach($deals as  $d)
		{
			if(!isset($bcats[$d['curl']]))
				$bcats[$d['curl']]=array();
			if(!in_array($d['burl'],$bcats[$d['curl']]))
			{
				$bcats[$d['curl']][]=$d['burl'];
				$cnt.=site_url($d['curl']."/".$d['burl'])."\n";
				$vcnt.=site_url("viewbycatbrand/".$d['curl']."/".$d['burl'])."\n";
				$vcnt.=site_url("viewbybrandcat/".$d['burl']."/".$d['curl'])."\n";
			}
		}
		
		foreach($deals as $d)
			$cnt.=site_url("{$d['murl']}/{$d['curl']}/{$d['url']}")."\n";
		
		foreach($deals as $d)
			$cnt.=site_url("{$d['curl']}/{$d['burl']}/{$d['url']}")."\n";
		
		foreach($deals as $d)
			$cnt.=site_url("{$d['murl']}/{$d['curl']}/{$d['burl']}/{$d['url']}")."\n";

		$file=fopen(SITEMAP_LOC."sitemap2.txt","w");
//		if(!$file)
//			die();
		fwrite($file, $cnt);
		fclose($file);
		
		$cnt="";
		$trends=$this->db->query("select name from king_trends")->result_array();
		foreach($trends as $t)
			$cnt.=site_url("trend/{$t['name']}")."\n";

		$file=fopen(SITEMAP_LOC."sitemap3.txt","w");
		if(!$file)
			die();
		fwrite($file, $cnt);
		fclose($file);
			
		$sindex='<?xml version="1.0" encoding="UTF-8"?>

<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

   <sitemap>

      <loc>http://snapittoday.com/sitemap_static.xml</loc>

      <lastmod>2011-08-23</lastmod>

   </sitemap>

   <sitemap>

      <loc>http://snapittoday.com/sitemap.txt</loc>
      <lastmod>(%lmod%)</lastmod>

   </sitemap>
   
   <sitemap>

      <loc>http://snapittoday.com/sitemap2.txt</loc>
      <lastmod>(%lmod%)</lastmod>

   </sitemap>
   
   <sitemap>

      <loc>http://snapittoday.com/sitemap3.txt</loc>
      <lastmod>(%lmod%)</lastmod>

   </sitemap>
   
</sitemapindex>
		';
		$sindex=str_ireplace("(%lmod%)", date("c"), $sindex);
		
			$file=fopen(SITEMAP_LOC."sitemap.xml","w");
			if(!$file)
				die();
			fwrite($file,$sindex);
			fclose($file);
		
			$file=fopen(SITEMAP_LOC."sitemap4.txt","w");
			if(!$file)
				die();
			fwrite($file,$vcnt);
			fclose($file);
			
		$this->cron_log(3);
	}
	
	function dubious()
	{
		//4
	}
	
	function spammer($pass="")
	{
		if($pass!="sdfsfwrojwefidsfjdskfmcsdf3refdfc")
			die;
		$this->cron_log(5,1);
			
		$emailorder=1;
		$msg=$this->load->view("mails/intro",array(),true);

		$emails_rw=$this->db->query("select id,email from promo_email where un_subscribe=0 and count<$emailorder limit 500")->result_array();
		$emails=array();
		$ids=array();
		foreach($emails_rw as $em)
		{
			$ids[]=$em['id'];
			$emails[]=$em['email'];
		}
		
//		$this->email(array("vimal@localcircle.in","sri@localcircle.in","sri.yulop@gmail.com","sushmag2785@gmail.com","govardhan.k@gmail.com","leelab38@ymail.com","manju-19851@hotmail.com","mahesh.mm40@yahoo.in","arathi.kk@hotmail.com","vimalsudhan@gmail.com"),"Snapittoday.com - Introducing brand new way to shop with your coworkers", $msg, array("campaign@snapittoday.com","Snapittoday"));
		
		if(!empty($ids))
		{
			$this->email($emails,"Snapittoday.com - Introducing brand new way to shop with your coworkers", $msg, array("campaign@snapittoday.com","Snapittoday"));
			$sql="update promo_email set count=?,lastsent=? where id in (".implode(",",$ids).")";
			$this->db->query($sql,array($emailorder,time()));
		}
		
		if(count($ids)!=0 && count($ids)<500)
			$this->email(array("vimal@localcircle.in","sri@localcircle.in","sushma@thecouch.in","gova@localcircle.in","v@localcircle.in"),"Promotion alert","Done with spamming at ".date("r"),array("promommer@snapittoday.com","Promommer"));
		
		$this->cron_log(5);
	}
	
	function sms_serv($pass="")
	{
		if($pass!="2342sdfw3rwfd4tg4546t4rt")
			die;
		$this->cron_log(6,1);
		$sql="select * from sms_queue limit 10";
		$smss=$this->db->query($sql)->result_array();
		foreach($smss as $sms)
		{
			$this->sms($sms['number'],$sms['msg']);
			$this->db->query("insert into sms_done(msg,number,sent_on) values(?,?,?)",array($sms['msg'],$sms['number'],time()));
			$this->db->query("delete from sms_queue where id=? limit 1",$sms['id']);
		}
		$this->cron_log(6);
	}
	
	function fb_user($pass="")
	{
		if($pass!="2342sdfw3rwfd4tg4546t4rt")
			die;
		die;
		$this->cron_log(7,1);
		$this->load->library("facebook",array('appId'=>FB_APPID,'secret'=>FB_SECRET));
		$sql="select 1 from king_facebookers where status=0";
		$p=$this->db->query($sql)->row_array();
		if(!empty($p))
		{
			$sql="select fbid as id from king_facebookers where status=0 limit 100";
			$data=array();$d=array();
			$c=0;
			foreach($this->db->query($sql)->result_array() as $fb)
			{
				$d[]=$fb['id'];
				$c++;
				if($c>10)
				{
					$c=0;
					$data[]=$d;
					$d=array();
				}
			}
			if(!empty($d))
			$data[]=$d;
			foreach($data as $d)
			{
				if(empty($d))
					continue;
				$ids=implode(",",$d);
				try{
					$resp=$this->facebook->api("?ids=".$ids);
				}catch(FacebookApiException $e)
				{continue;}
				foreach($resp as $r)
				{
					if(isset($r['username']))
						$this->db->query("update king_facebookers set username=?,status=1 where fbid=?",array($r['username'],$r['id']));
					else
						$this->db->query("update king_facebookers set status=2 where fbid=?",array($r['id']));
				}
			}
		}
		$this->cron_log(7);
	}
	
	function fb_mail($pass="")
	{
		if($pass!="2342sdfw3rwfd4tg4546t4rt")
			die;
		die;
		$this->cron_log(8,1);
		
		$this->db->query("update king_fb_mails set status=2 where expires_on<".time());
		
		$p=$this->db->query("select 1 from king_fb_mails where status=0 limit 1")->row_array();
		
		if(!empty($p))
		{
			$mails=$this->db->query("select m.*,fb.fbid,fb.username from king_fb_mails m join king_facebookers fb on fb.fbid=m.to and fb.status=1 where m.status=0 limit 50")->result_array();
			foreach($mails as $mail)
			{
				$this->email($mail['username']."@facebook.com",$mail['sub'],$mail['msg'],array($mail['from'],substr($mail['from'],0,strpos($mail['from'],"@"))));
				$this->db->query("update king_fb_mails set status=1 where id=?",$mail['id']);
			}
		}
		$this->cron_log(8);
	}
	
	function search_index($pass="")
	{
		if($pass!="asdadadasdaskfwerjwklfwewe")
			die;
		$this->cron_log(9,1);
		
		$deals=$this->db->query("select i.description1 as description,i.id as itemid,b.name as brand,c.name as category,i.name as tagline,d.keywords from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories c on c.id=d.catid where ".time()." between d.startdate and d.enddate and d.publish=1")->result_array();

		$ids=array();
		foreach($deals as $d)
			$ids[]=$d['itemid'];

		$eids_raw=$this->db->query("select itemid from king_search_index")->result_array();
		$eids=array();
		foreach($eids_raw as $e)
			$eids[]=$e['itemid'];

		$news=array();
		foreach($ids as $i)
			if(!in_array($i, $eids))
				$news[]=$i;
		
		foreach($eids as $e)
			if(!in_array($e,$ids))
				$this->db->query("delete from king_search_index where itemid=?",$e);
		
//		$this->db->query("TRUNCATE TABLE `king_search_index`");
		
		$bsql="insert into king_search_index(itemid,name,keywords) values";
		$values=$vals=array();
		foreach($deals as $deal)
		{
			if(!in_array($deal['itemid'], $news))
				continue;
			if(count($values)>4)
			{
				$sql=$bsql.implode(",",$values);
				$this->db->query($sql,$vals);
				$values=$vals=array();
			}
			$name=preg_replace('/[^a-zA-Z0-9_\-]/',' ',$deal['tagline']);
			$keywords=preg_replace('/[^a-zA-Z0-9_\-]/',' ',$deal['keywords']);
			$keywords=str_replace(","," ",$keywords);
			$name.=" ".preg_replace('/[^a-zA-Z0-9_\-]/',' ',$deal['brand']);
			$name.=" ".preg_replace('/[^a-zA-Z0-9_\-]/',' ',$deal['category']);
			
			$extra=substr(strip_tags(html_entity_decode($deal['description'])),0,200);
			$extra=preg_replace('/[^a-zA-Z0-9_\- ]/','',$extra);
			
			$exs=explode(" ",$extra);
			foreach($exs as $e)
				if(strlen($e)>4)
					$ex[]=$e;
			$extra="$keywords ".implode(" ",$ex);	
					
			$values[]="(?,?,?)";
			$vals[]=$deal['itemid'];
			$vals[]="$name";
			$vals[]=$extra;
		}
		if(!empty($values))
		{
				$sql=$bsql.implode(",",$values);
				$this->db->query($sql,$vals);
				$values=$vals=array();
		}
		
		$this->cron_log(9);
	}
	
	function ponr_transaction($pass="")
	{
		if($pass!="asdasd2wwjweiowfrmkl")
			show_404();
		$this->cron_log(11,1);
		$trans=$this->db->query("select transid from king_transactions where status=0 and init<".(time()-900)." and init > ".mktime(0,0,0,2,29,2012))->result_array();
		foreach($trans as $tran)
		{
			$transid=$tran['transid'];
			if($this->db->query("select 1 from king_orders where transid=?",$transid)->num_rows()!=0)
				continue;
			if($this->db->query("select 1 from king_failed_transactions_notify where transid=?",$transid)->num_rows()!=0)
				continue;
			$orders=$this->db->query("select o.*,i.name from king_tmp_orders o join king_dealitems i on i.id=o.itemid where o.transid=?",$transid)->result_array();
			$order=$orders[0];
			if($this->db->query("select 1 from king_orders where transid!=? and time>? and userid=?",array($transid,$order['time'],$order['userid']))->num_rows()!=0)
				continue;
			$this->email($order['ship_email'],"Please complete your order : $transid", $this->load->view("mails/ponr_transaction",array("transid"=>$transid,"orders"=>$orders),true));
			$this->db->query("insert into king_failed_transactions_notify(transid,time) values(?,?)",array($transid,time()));
		}
		$this->cron_log(11);
	}
		
	function mailcheck($pass="")
	{
		if($pass!="qwiru238r2ir823r2d2wr23r23r2")
			die;
		$this->cron_log(12,1);
		$this->load->model("erpmodel","erpm");
		$this->load->library("imap");
		$luid=$this->db->query("select im_uid from auto_readmail_uid order by id desc limit 1")->row_array();
		if(empty($luid))
			die("no starting uid specified");

		$luid=$luid['im_uid'];
		$this->imap->login("care@snapittoday.com","snap123rty");
		$nuid=$this->imap->is_newmsg($luid);
		if(!$nuid)
			die("no new mail");
		$mails=array();
		for($i=$luid+1;$i<=$nuid;$i++)
			$mails[]=$this->imap->readmail($i);
			
		foreach($mails as $m)
		{
			$ticket=array();
			$userid=0;
			$ticket_no=0;
			$transid="";
			if(empty($m))
				continue;
			preg_match("/(TK\d{10})/i",$m['subject'],$matches);
			if(empty($matches))
				preg_match("/(TK\d{10})/i",$m['msg'],$matches);
			if(!empty($matches))
			{
				$ticket_no=substr($matches[0],2);
				if($this->db->query("select count(1) as l from support_tickets where ticket_no=? limit 1",$ticket_no)->row()->l==0)
					$ticket_no=0;
			}
			if($ticket_no==0)
			{
				preg_match("/([A-Za-z]{6}\d{5})/i",$m['subject'],$matches);
				if(empty($matches))
					preg_match("/([A-Za-z]{6}\d{5})/i",$m['msg'],$matches);
				if(!empty($matches))
					$transid=$matches[0];
			}
			$customer=$this->db->query("select userid from king_users where email=?",$m['from'])->row_array();
			if(!empty($customer))
				$userid=$customer['userid'];
			$msg=nl2br("SUBJECT\n-----------------------------------------------------\n".$m['subject']."\n\nEMAIL CONTENT\n-----------------------------------------------------\n").$m['msg'];
			$no=rand(1000000000,9999999999);
			if($ticket_no==0)
			{
				$this->db->query("insert into support_tickets(ticket_no,user_id,email,transid,created_on) values(?,?,?,?,now())",array($no,$userid,$m['from'],$transid));
				$tid=$this->db->insert_id();
			}
			else 
			{
				$tid=$this->db->query("select ticket_id as id from support_tickets where ticket_no=?",$ticket_no)->row()->id;
				$this->db->query("update support_tickets set status=1 where ticket_no=? and assigned_to!=0 limit 1",$ticket_no);
				$this->db->query("update support_tickets set status=0 where ticket_no=? and assigned_to=0 limit 1",$ticket_no);
			}
			$this->erpm->addnotesticket($tid,1,0,$msg,1);
			$this->db->query("insert into auto_readmail_log(ticket_id,subject,msg,`from`,created_on) values(?,?,?,?,now())",array($tid,$m['subject'],$msg,$m['from']));
			if($ticket_no!=0)
				$this->erpm->addnotesticket($tid,0,1,"Status reset after reply mail from customer");
		}
		$this->db->query("insert into auto_readmail_uid(im_uid,time) values(?,now())",$nuid);
		$this->cron_log(12);
	}
	
	function ofs_marker($pass="")
	{
		if($pass!="efwserwerwer44432wrewr")
			die;
		$this->cron_log(13,1);
		$this->load->model("erpmodel","erpm");
		if(date("i")%45==0)
		{
			$raw_itemid=$this->db->query("select i.id from king_deals d join king_dealitems i on i.dealid=d.dealid where ".time()." between d.startdate and d.enddate and d.publish=1 and i.live=0")->result_array();
			$itemids=array();
			foreach($raw_itemid as $i)
				$itemids[]=$i['id'];
			if(!empty($itemids))
			{
				$avail=$this->erpm->do_stock_check($itemids);
				foreach($itemids as $id)
					if(in_array($id,$avail))
						$this->db->query("update king_dealitems set live=1 where id=? limit 1",$id);
			}
		}elseif(date("i")%3==0){
			$raw_itemid=$this->db->query("select i.id from king_deals d join king_dealitems i on i.dealid=d.dealid where ".time()." between d.startdate and d.enddate and d.publish=1 and i.live=1")->result_array();
			$itemids=array();
			foreach($raw_itemid as $i)
				$itemids[]=$i['id'];
			if(!empty($itemids))
			{
				$avail=$this->erpm->do_stock_check($itemids);
				foreach($itemids as $id)
					if(!in_array($id,$avail))
						$this->db->query("update king_dealitems set live=0 where id=? limit 1",$id);
			}
		}else{
			$raw_itemid=$this->db->query("select itemid as id from king_orders where time>?",time()-(6*60))->result_array();
			$itemids=array();
			foreach($raw_itemid as $i)
				$itemids[]=$i['id'];
			$raw_itemid=$this->db->query("select i.id from t_stock_info s join m_product_deal_link l on l.product_id=s.product_id join king_dealitems i on i.id=l.itemid where s.created_on>?",date("Y-m-d H:i:s",time()-(10*60)))->result_array();
			foreach($raw_itemid as $i)
				$itemids[]=$i['id'];
			$itemids=array_unique($itemids);
			
			if(!empty($itemids))
			{
				$avail=$this->erpm->do_stock_check($itemids);
				foreach($itemids as $id)
				{
					if(!in_array($id,$avail))
						$this->db->query("update king_dealitems set live=0 where id=? limit 1",$id);
					else
						$this->db->query("update king_dealitems set live=1 where id=? limit 1",$id);
				}
			}
		}
		$this->cron_log(13);
	}
	
	function image_updater($pass="")
	{
		if($pass!="sdkasdihk23rhwenwsf")
			die;
		$this->cron_log(14,1);
		if($this->db->query("select is_locked as l from cron_image_updater_lock")->row()->l==1)
		{
			$this->cron_log(14);
			die;
		}
		if(!defined("HOME_DIR"))
			define("HOME_DIR","");
		$hdir=HOME_DIR;
		
		$dir=CRON_IMAGES_LOC;
		$f_dir=$dir."failed/";
		$limit=10;
		$images=array();
		if (is_dir($dir)) {
		    if ($dh = opendir($dir)) {
		        while (($file = readdir($dh)) !== false) {
		        	if($file!="." && $file!=".." && is_file($dir.$file))
		        		$images[]=$dir.$file;
		        }
		        closedir($dh);
		    }
		    if(empty($images))
		    	$this->db->query("update cron_image_updater_lock set finished_on=?,finish_status=1,is_locked=1,modified_by=0,modified_on=?",array(time(),time()));
		    $c=0;
		    $pending=0;
		    $this->load->library("thumbnail");
		    foreach($images as $img)
		    {
		    	$img_name=pathinfo($img,PATHINFO_FILENAME);
		    	$pl=explode("_",$img_name);
		    	if(count($pl)>=2)
		    		$img_name=$pl[0];
		    	$iid=$itemid=0;
		    	$r_item=$this->db->query("select id from king_dealitems where id=?",$img_name)->row_array();
		    	if(!empty($r_item))
		    		$iid=$itemid=$r_item['id'];
				if($itemid!=0 && $this->thumbnail->check($img))
				{		
					$imgname=randomChars(15);
					$this->thumbnail->create(array("source"=>$img,"dest"=>$hdir."images/items/300/$imgname.jpg","width"=>300));
					$this->thumbnail->create(array("source"=>$img,"dest"=>$hdir."images/items/small/$imgname.jpg","width"=>200));
					$this->thumbnail->create(array("source"=>$img,"dest"=>$hdir."images/items/thumbs/$imgname.jpg","width"=>50,"max_height"=>50));
					$this->thumbnail->create(array("source"=>$img,"dest"=>$hdir."images/items/$imgname.jpg","width"=>400));
					$this->thumbnail->create(array("source"=>$img,"dest"=>$hdir."images/items/big/$imgname.jpg","width"=>1000));
					$did=$this->db->query("select dealid from king_dealitems where id=?",$iid)->row()->dealid;
					if(count($pl)>=2)
						$this->db->insert("king_resources",array("dealid"=>$did,"itemid"=>$itemid,"type"=>0,"id"=>$imgname));
					else{
					$this->db->query("update king_dealitems set pic=? where id=? limit 1",array($imgname,$iid));
					$this->db->query("update king_deals set pic=? where dealid=? limit 1",array($imgname,$did));
					$this->db->query("update deals_bulk_upload_items set is_image_updated=1,updated_on=".time().",updated_by=0 where item_id=?",$iid);
					$bid=$this->db->query("select bulk_id from deals_bulk_upload_items where item_id=?",$iid)->row_array();
					if(empty($bid))
						$bid=$bid['bulk_id'];
					if($this->db->query("select 1 from deals_bulk_upload_items where bulk_id=? and is_image_updated=0",$bid)->num_rows()==0)
						$this->db->query("update deals_bulk_upload set is_all_image_updated=1 where id=? limit 1",$bid);
					}
					$failed=0;
				}
				else $failed=1;
		    	if($failed)
		    		rename($img,$f_dir.basename($img));
		    	else 
		    	{
		    		$this->db->query("update cron_image_updater_lock set images_updated=images_updated+1,finished_on=?",time());
		    		unlink($img);
		    	}
		    	$c++;
		    	if($c>=$limit)
		    	{
		    		$pending=1;break;
		    	}
		    }
		    if($pending==0)
		    	$this->db->query("update cron_image_updater_lock set finished_on=?,finish_status=1,is_locked=1,modified_by=0,modified_on=?",array(time(),time()));
		}
		else die("no dir $dir");
		$this->cron_log(14);
	}
	
	
	private function email($emails,$sub,$msg,$from=array())
	{
		if(empty($from))
			$from=array("support@snapittoday.com","Snapittoday");
		if(!is_array($emails))
			$emails=array($emails);
		foreach($emails as $email)
		{
			$config=array('mailtype'=>"html");
			$this->email->initialize($config);
			$this->email->from($from[0],$from[1]);
			$this->email->to($email);
			$this->email->subject($sub);
			$this->email->message($msg);
			$this->email->send();
		}
	}
	
	private function sms($no,$msg)
	{
		$url="http://72.55.146.179/pfile/record.php?username=<username>&password=<password>&To=<mobile_number>&Text=<message_content>&senderid=<senderid>";
		$params=array(
				'username'=>'local',
				'password'=>'local12',
				'senderid'=>'SNAP-IT',
				'message_content'=>urlencode($msg)
				);
		foreach($params as $r=>$v)
			$url=str_replace("<{$r}>", $v, $url);
		if($no==0)
			return;
		$lurl=str_replace("<mobile_number>",$n,$url);
	//	file_get_contents($lurl);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $lurl);
		curl_setopt($ch, CURLOPT_HEADER, false);
      	curl_setopt($ch,  CURLOPT_RETURNTRANSFER, true); 			
		curl_exec($ch);
		curl_close($ch);
	}
	
	
	private function cron_log($type,$start=0)
	{
		$p="count";
		if($start)
			$p="start";
		$this->db->query("update cron_log set $p=$p+1 where cron=?",$type);
		if($this->db->affected_rows()==0)
			$this->db->query("insert into cron_log(cron,$p) values(?,1)",$type);
	}
	
	
	/**
	 * function to update pnh employee task status for ended tasks 
	 */
	function task_status_update($key='upd9810310928390')
	{
		
		$this->cron_log(18,1);
		/*
		$date_diff_list=$this->db->query('SELECT id,task_type,on_date,due_date,task_type
											FROM pnh_m_task_info
											WHERE due_date < DATE(CURDATE())')->result_array();
		if($date_diff_list)
		{
			foreach($date_diff_list as $date_diff_det)
			{
				$sql=$this->db->query("update pnh_m_task_info set task_status=2 where is_active=1 and task_status=1 and id=? ",$date_diff_det['id']);
			
				$task_types_arr = explode(',',$date_diff_det['task_type']);
				foreach($task_types_arr as $task_type)
				{
					if($task_type==1)
					{
						$franchise_info=$this->db->query("select f_id from pnh_m_sales_target_info where task_id=?",array($date_diff_det['id']))->result_array();			
						
						foreach($franchise_info as $f_det)
						{
							$actual_target_amt=$this->db->query('SELECT SUM(i_orgprice-i_coup_discount-i_discount) AS amount 
												FROM king_transactions a 
												JOIN king_orders b ON a.transid = b.transid 
												WHERE franchise_id = ? AND a.init BETWEEN UNIX_TIMESTAMP(?) AND UNIX_TIMESTAMP(?) ',array($f_det['f_id'],$date_diff_det['due_date'],$date_diff_det['on_date']))->row()->amount;
							$sql=$this->db->query('update pnh_m_sales_target_info set actual_target =?,status=0 where task_id=? and f_id=?',array($actual_target_amt,$date_diff_det['id'],$f_det['f_id']) );

						}
						
					}
					
				}
			}
		}
		*/
		$this->cron_log(18);
	}
	
	/**
 	* List of franchise with thr current_balance
 	*/

	function sms_currentbalance($authkey='ZSDFFFFFF')
	{
		
		die();

		if($authkey != '123871jnsnsd12312')
		{
			die();
		}
			
		$this->cron_log(17,1);
		
		$current_balance_list_res=$this->db->query("SELECT distinct franchise_id,franchise_name,current_balance,login_mobile1,login_mobile2 FROM pnh_m_franchise_info a WHERE is_suspended !=1 ");
		
		if($current_balance_list_res->num_rows())
		{
			foreach($current_balance_list_res->result_array() as $current_balance_det)
			{
				$login_mobile1=$current_balance_det['login_mobile1'];
				$franchise_name=$current_balance_det['franchise_name'];
				$balance=$current_balance_det['current_balance'];
				
				$acc_statement = $this->erpm->get_franchise_account_stat_byid($current_balance_det['franchise_id']);
				$net_payable_amt = $acc_statement['net_payable_amt'];
				$credit_note_amt = $acc_statement['credit_note_amt'];
				$shipped_tilldate = $acc_statement['shipped_tilldate'];
				$paid_tilldate = $acc_statement['paid_tilldate'];
				$uncleared_payment = $acc_statement['uncleared_payment'];
				$cancelled_tilldate = $acc_statement['cancelled_tilldate'];
				$ordered_tilldate = $acc_statement['ordered_tilldate'];
				$not_shipped_amount = $acc_statement['not_shipped_amount'];
				$acc_adjustments_val = $acc_statement['acc_adjustments_val'];
				
				
				$current_balance = $shipped_tilldate-($paid_tilldate+$acc_adjustments_val+$credit_note_amt);
				
			 	$current_balance = 	format_price($current_balance);
				$uncleared_payment = format_price($uncleared_payment);
				
				$sms_msg = "Dear $franchise_name,Your current balance is Rs.$current_balance,amount under clearance is Rs $uncleared_payment ,please make timely payments to avoid hold up in supplies.Happy Shopping - Store King";
				$this->erpm->pnh_sendsms($login_mobile1,$sms_msg,$current_balance_det['franchise_id'],0,'CUR_BALANCE');	
				
			}
	
		}
		$this->cron_log(17);
	}
	
	//function to send sms to the bussiness executive or terrirory managers to make payment collection based on franchise	
	function sms_paymentcollection()
	{
			$this->cron_log(16,1);
			$franchise_employee_details=$this->db->query("SELECT b.employee_id,a.assigned_to,a.asgnd_town_id,e.town_name,f.territory_name,b.name,b.contact_no,c.franchise_id,c.franchise_name,a.on_date,a.due_date 
															FROM pnh_m_task_info a
															JOIN m_employee_info b ON b.employee_id=a.assigned_to
															LEFT JOIN pnh_m_franchise_info c ON c.town_id=a.asgnd_town_id
															left JOIN `pnh_towns`e ON e.id=a.asgnd_town_id
															JOIN `pnh_m_territory_info`f ON f.id=e.territory_id
															WHERE b.is_suspended=0 AND c.is_suspended=0  AND (b.job_title2=4 OR b.job_title2=5) AND DATE(NOW()) BETWEEN date(on_date) AND date(due_date)
															GROUP BY franchise_id
															ORDER BY b.name");
											
			
			if($franchise_employee_details->num_rows())
			{
				$sms_pcbyemp = array();
				foreach($franchise_employee_details->result_array() as $fran_emp_det)
				{
					
					if(!isset($sms_pcbyemp[$fran_emp_det['employee_id']]))
						$sms_pcbyemp[$fran_emp_det['employee_id']] = array('mob'=>$fran_emp_det['contact_no'],"grp_pcmsg"=>"");
					
					$pnh_emp_name=$fran_emp_det['name'];
					$franchise_name=$fran_emp_det['franchise_name'];
					$territory_name=$fran_emp_det['territory_name'];
					$fid=$fran_emp_det['franchise_id'];
					$empid=$fran_emp_det['employee_id'];
					
					$acc_statement = $this->erpm->get_franchise_account_stat_byid($fran_emp_det['franchise_id']);	
					$shipped_tilldate = $acc_statement['shipped_tilldate'];
					$paid_tilldate = $acc_statement['paid_tilldate'];
					$uncleared_payment = $acc_statement['uncleared_payment'];		
					$ordered_tilldate = $acc_statement['ordered_tilldate'];
					$payment_pending = $acc_statement['payment_pending'];
				 	
				 	if(!$payment_pending && !$uncleared_payment)
						continue;	
					
					$payment_pending = 	formatInIndianStyle($payment_pending);
					$uncleared_payment = formatInIndianStyle($uncleared_payment);
				
					
					$sms_pcbyemp[$fran_emp_det['assigned_to']]['grp_pcmsg'] .= $territory_name." Payments - $franchise_name:$payment_pending,cl amt($uncleared_payment) "."\r\n";
					
				
				}
				
				// send payment colletion group sms msg for employee 
				foreach ($sms_pcbyemp as $empid=>$pc_msgdet)
				{
					foreach(explode(',',$pc_msgdet['mob']) as $mob_no)
					{
						$this->erpm->pnh_sendsms($mob_no,$pc_msgdet['grp_pcmsg'],$empid);
						//echo $pc_msgdet['mob'].",".$pc_msgdet['grp_pcmsg'].'<br><br><br><br>';
						$this->db->query('insert into pnh_employee_grpsms_log(emp_id,contact_no,type,grp_msg,created_on)values(?,?,?,?,now())',array($empid,$mob_no,1,$pc_msgdet['grp_pcmsg'],date('Y-m-d H:i:s')));
						break;
					}
				}
			}
			$this->cron_log(16);
		}

		function send_paidamt_mail()
		{
			
			$this->cron_log(15,1);
			
			$finance_role_access_no = $this->db->query("select value from user_access_roles where const_name = 'FINANCE_ROLE' ")->row()->value; 
			
			// fetch default emails based on finance roles 
			$finance_email_addrs = $this->db->query("SELECT GROUP_CONCAT(email) AS f_emails FROM king_admin WHERE access&".$finance_role_access_no." != 0 ")->row()->f_emails;
			$finance_email_addrs = explode(',',$finance_email_addrs);
			
			$terr_list_res = $this->db->query("SELECT b.territory_id
														FROM `pnh_executive_accounts_log` a 
														JOIN m_town_territory_link b ON a.emp_id = b.employee_id 
														WHERE DATE(logged_on) = CURDATE()
														GROUP BY b.territory_id;");
			
			$terr_list = array();
			foreach ($terr_list_res->result_array() as $terr)
				$terr_list[] = $terr['territory_id'];
			if(!count($terr_list))
				die();
			
			$terr_manager_list = $this->db->query("SELECT a.employee_id,a.name,a.email,b.territory_id FROM `m_employee_info` a JOIN m_town_territory_link b ON a.employee_id = b.employee_id WHERE a.is_suspended=0 and a.job_title=4 AND b.territory_id IN (".implode(',',$terr_list)."); ");
			
			if($terr_manager_list->num_rows())
			{
				foreach($terr_manager_list->result_array() as $terr_det)
				{
					$paid_details_res=$this->db->query("SELECT f.town_name,g.territory_name,a.type,d.name AS employee_name,d.email AS emp_mail,a.msg,a.remarks,c.email,c.name AS superior_name,a.logged_on 
														FROM pnh_executive_accounts_log a
														JOIN m_employee_rolelink b ON b.employee_id=a.emp_id
														JOIN m_employee_info c ON c.employee_id=b.parent_emp_id
														JOIN m_employee_info d ON d.employee_id=a.emp_id
														JOIN m_town_territory_link e ON e.employee_id = a.emp_id
														JOIN pnh_towns f on e.town_id = f.id
														JOIN pnh_m_territory_info g on e.territory_id = g.id  
								  						where e.territory_id = ? and DATE(a.logged_on) = CURDATE() and c.is_suspended=0  
											            order by a.logged_on 
									  					",$terr_det['territory_id']);
					if($paid_details_res->num_rows())
					{
						$cc_email_address = array();
						
						$to_email_address = '';
						
						$paid_details_rows = $paid_details_res->result_array();
						
						$tbl_data = '<h3 style="margin:5px 0px;">PNH Executive Daily Log - '.$paid_details_rows[0]['territory_name'].' - '.format_date($paid_details_rows[0]['logged_on']).' </h3>';
						
						$tbl_data .= '<table border=1 cellpadding=3 style="font-size:12px;font-family:arial"><thead><th>Loggedon</th><th>Executive</th><th>Town</th><th>Territory</th><th>Message</th></thead>';
						$tbl_data .= '<tbody>';
						foreach($paid_details_res->result_array() as $paid_details_det)
						{
							if(!count($cc_email_address))
								$cc_email_address = $finance_email_addrs;
							
							$executive_mailid=$paid_details_det['emp_mail'];
							$terry_mgr_mailid=$paid_details_det['email'];
							$to_email_address = $terry_mgr_mailid;
							
							$msg=$paid_details_det['msg'];
				
							array_push($cc_email_address,$executive_mailid);
							
							$tbl_data .= '<tr><td>'.format_datetime($paid_details_det['logged_on']).'</td><td>'.$paid_details_det['employee_name'].'</td><td>'.$paid_details_det['town_name'].'</td><td>'.$paid_details_det['territory_name'].'</td><td>'.$paid_details_det['msg'].'</td></tr>';
							
						}
						$tbl_data .= '</tbody>';
						$tbl_data .= '</table>';
						
						$config=array('mailtype'=>"html");
						$this->email->initialize($config);
						$this->email->from('notify@snapittoday.com');
						$this->email->to($to_email_address);
						$this->email->cc($cc_email_address);
						$this->email->subject('PNH Executive Daily Log - '.$paid_details_rows[0]['territory_name'].' - '.format_date($paid_details_rows[0]['logged_on']));
						$this->email->message($tbl_data);
						$this->email->send();
						
					}
				}
			}
			
			$this->cron_log(15);
		}
		
	function enday_orderd_sms_tofranchise()
	{
		$this->cron_log(19,1);
		$franchise_info_res=$this->db->query("SELECT franchise_id,franchise_name,current_balance,login_mobile1,login_mobile2 FROM pnh_m_franchise_info WHERE is_suspended=0");
			
		if($franchise_info_res ->num_rows())
		{
			foreach($franchise_info_res->result_array() as $franchise_det)
			{
				$day_orderd_amt=$this->db->query("SELECT IFNULL(ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2),0) AS amt 
														FROM king_transactions a
														JOIN king_orders b ON a.transid = b.transid
														JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id
														WHERE a.franchise_id = ? AND c.is_suspended = 0 AND DATE(FROM_UNIXTIME(a.init)) = CURDATE() 
														",$franchise_det['franchise_id'])->row()->amt;
					
				$franchise_name=$franchise_det['franchise_name'];
				$login_mobile1=$franchise_det['login_mobile1'];
				if($day_orderd_amt['amt']>0)
					$this->erpm->pnh_sendsms($login_mobile1,"Congratulations!!!Dear Franchise $franchise_name, your placed order of the day -Rs.$day_orderd_amt Happy Franchising",$franchise_det['franchise_id']);
				//echo $login_mobile1,"Congratulations!!!Dear Franchise $franchise_name, your placed order of the day -Rs.$day_orderd_amt Happy Franchising".'<br>';
			}
		}
		$this->cron_log(19);
	}
	
	//End day SMS to Executive:Total sales in town
	function enday_sms_toexec()
	{
		$this->cron_log(20,1);
		//employee town territory details assigned for the particular day
		$exec_details=$this->db->query("SELECT a.assigned_to,a.asgnd_town_id,e.town_name,f.territory_name,b.name,b.contact_no,a.on_date,a.due_date
										FROM pnh_m_task_info a
										JOIN m_employee_info b ON b.employee_id=a.assigned_to
										JOIN `pnh_towns`e ON e.id=a.asgnd_town_id
										JOIN `pnh_m_territory_info`f ON f.id=e.territory_id
										WHERE 1 AND b.is_suspended=0 AND  b.job_title2=5 AND DATE(NOW()) BETWEEN date(on_date) AND date(due_date)");
		if($exec_details->num_rows())
		{
			foreach($exec_details->result_array() as $exec_det)
			{
				$emp_phno = $exec_det['contact_no'];
				$emp_id = $exec_det['assigned_to'];
				$town_name=$exec_det['town_name'];
				//Total sales achieved for the day in assigned town
				$ttl_sales=@$this->db->query("SELECT SUM((o.i_orgprice-o.i_discount-o.i_coup_discount)*o.quantity) AS total_order_value
												FROM pnh_m_franchise_info b 
												JOIN king_transactions a ON a.franchise_id = b.franchise_id AND is_pnh = 1 
												JOIN king_orders o ON o.transid = a.transid
												WHERE b.town_id=? AND o.transid IS NOT NULL AND is_pnh = 1 and o.status != 3 
												AND date(from_unixtime(a.init)) = curdate()
												",$exec_det['asgnd_town_id'])->row()->total_order_value;
				$ttl_month_sales=@$this->db->query("SELECT SUM((o.i_orgprice-o.i_discount-o.i_coup_discount)*o.quantity) AS total_order_value
						FROM pnh_m_franchise_info b
						JOIN king_transactions a ON a.franchise_id = b.franchise_id AND is_pnh = 1
						JOIN king_orders o ON o.transid = a.transid
						WHERE b.town_id=? AND o.transid IS NOT NULL AND is_pnh = 1 and o.status != 3
						AND date(from_unixtime(a.init)) >= date(?)  
						",array($exec_det['asgnd_town_id'],date('Y-m-01')))->row()->total_order_value;
				
				if(1)
				{
					$ttl_sales = 'Rs '.round($ttl_sales*1);
					$ttl_month_sales = 'Rs '.round($ttl_month_sales*1);
					$grp_msg = 	"Today Total Sales: $town_name-$ttl_sales";
					$grp_msg .= " Total Sales this month : $ttl_month_sales";
					
					$emp_mobnos = explode(',',$emp_phno);
					foreach($emp_mobnos as $emp_phno)
					{
						$this->erpm->pnh_sendsms($emp_phno,$grp_msg);
						$this->db->query('insert into pnh_employee_grpsms_log(emp_id,contact_no,type,grp_msg,created_on)values(?,?,?,?,now())',array($emp_id,$emp_phno,3,$grp_msg,date('Y-m-d H:i:s')));
						break;
					}
				}
				
			}
		}
		$this->cron_log(20);
	}
	
	
	//End day SMS to Territory Manager:Total sales in territory
	function enday_sms_totmgr()
	{
		$this->cron_log(21,1);
		//employee town territory details assigned for the particular day
		$tm_details=$this->db->query("SELECT a.assigned_to,a.asgnd_town_id,e.town_name,e.territory_id,f.territory_name,b.name,b.contact_no,a.on_date,a.due_date
										FROM pnh_m_task_info a
										JOIN m_employee_info b ON b.employee_id=a.assigned_to
										JOIN `pnh_towns`e ON e.id=a.asgnd_town_id
										JOIN `pnh_m_territory_info`f ON f.id=e.territory_id
										WHERE 1 AND b.is_suspended=0 AND  b.job_title2=4 AND DATE(NOW()) BETWEEN(on_date) AND (due_date)");
		if($tm_details->num_rows())
		{
			foreach($tm_details->result_array() as $tm_det)
			{
	
				$territory_name=$tm_det['territory_name'];
				$emp_phno=$tm_det['contact_no'];
				$emp_id=$tm_det['assigned_to'];
				//Total sales achieved for the day in assigned territory
				$ttl_sales=@$this->db->query("SELECT SUM((o.i_orgprice-o.i_discount-o.i_coup_discount)*o.quantity) AS total_order_value
												FROM pnh_m_franchise_info b
												JOIN king_transactions a ON a.franchise_id = b.franchise_id AND is_pnh = 1
												JOIN king_orders o ON o.transid = a.transid
												WHERE b.territory_id=? AND o.transid IS NOT NULL and o.status != 3  
												AND date(from_unixtime(a.init)) = curdate()
											",$tm_det['territory_id'])->row()->total_order_value;
				
				$ttl_month_sales=@$this->db->query("SELECT SUM((o.i_orgprice-o.i_discount-o.i_coup_discount)*o.quantity) AS total_order_value
						FROM pnh_m_franchise_info b
						JOIN king_transactions a ON a.franchise_id = b.franchise_id AND is_pnh = 1
						JOIN king_orders o ON o.transid = a.transid
						WHERE b.territory_id=? AND o.transid IS NOT NULL and o.status != 3
						AND date(from_unixtime(a.init)) >= date(?)
						",array($tm_det['territory_id'],date('Y-m-01')))->row()->total_order_value;
				
				if(1)
				{
					$ttl_sales = 'Rs '.round($ttl_sales*1);
					$ttl_month_sales = 'Rs '.round($ttl_month_sales*1);
					$grp_msg = 	"Today Total Sales: $territory_name-$ttl_sales";
					$grp_msg .= " Total Sales this month : $ttl_month_sales";
					
					$emp_mobnos = explode(',',$emp_phno);
					foreach($emp_mobnos as $emp_phno)
					{
						$this->erpm->pnh_sendsms($emp_phno,$grp_msg);
						$this->db->query('insert into pnh_employee_grpsms_log(emp_id,contact_no,type,grp_msg,created_on)values(?,?,?,?,now())',array($emp_id,$emp_phno,3,$grp_msg,date('Y-m-d H:i:s')));
						break;
					}
				}
				
			}
			
		}
		$this->cron_log(21);
	}
	
	/**
	 *Tommorow  Task remainder SMS to executive or TM
	 */	
	function task_remainder()
	{
		$this->cron_log(22,1);
		$cfg_task_types_arr = array();
		$task_type_list=$this->db->query("SELECT * FROM `pnh_m_task_types` ")->result_array();
		foreach($task_type_list as $tsk_type_det)
		{
			$cfg_task_types_arr[$tsk_type_det['id']] =$tsk_type_det['short_form'];
		}
		//get all tomorrows task
		$task_details=$this->db->query("SELECT a.id,b.town_name,c.territory_name,a.assigned_to AS emp_id,d.name AS assigned_toname,f.role_name,e.name AS assigned_byname,a.task_type,DATE(a.on_date) AS on_date,a.due_date,a.task,a.ref_no,d.contact_no
												FROM pnh_m_task_info a
												JOIN pnh_towns b ON b.id=a.asgnd_town_id
												JOIN pnh_m_territory_info c ON c.id=b.territory_id
												JOIN m_employee_info d ON d.employee_id = a.assigned_to
												JOIN m_employee_info e ON e.employee_id = a.assigned_by
												JOIN m_employee_roles f ON f.role_id=d.job_title
												WHERE 1 AND a.is_active = 1 AND a.task_status=1 and d.is_suspended=0
												AND (CURDATE() + INTERVAL 1 DAY ) BETWEEN a.on_date AND a.due_date
											order by emp_id 
										");
		
		if($task_details->num_rows())
		{
			foreach($task_details->result_array() as $task_det)
			{
				$emp_phno=$task_det['contact_no'];
				$task_id=$task_det['ref_no'];
				$empid=$task_det['emp_id'];
				$town_name=$task_det['town_name'];
				
				if($this->db->query("select count(*) as t from m_employee_info where is_suspended = 1 and employee_id = ? ",$empid)->row()->t)
					continue;
				
				$sub_task_list=explode(',',$task_det['task_type']);
				
				$task_type = in_array(1, $sub_task_list)?'E':'N'; 
				
				
				
				$task_grp_sms_arr = array();
				$task_type_desc_res=$this->db->query('SELECT c.assigned_to,task_id,request_msg,task_type_id,b.short_form AS task_type_name,b.task_for FROM `pnh_task_type_details`a
														JOIN `pnh_m_task_types`b ON b.id=a.task_type_id
														JOIN pnh_m_task_info c ON c.id=a.task_id
														WHERE a.task_id=? AND c.is_active=1 AND c.task_status=1 
														GROUP BY task_type_id',array($task_det['id']));
				if($task_type_desc_res->num_rows())
					foreach($task_type_desc_res->result_array() as $task_desc)
					{
						$task_grp_sms_arr[] = $cfg_task_types_arr[$task_desc['task_type_id']].':'.$task_desc['request_msg'];
					}
				
				
				$smsg = "Task ID:$task_id ,$town_name task - $task_type - ".implode(',',$task_grp_sms_arr);
				
				foreach(explode(',',$emp_phno) as $emp_mob)
				{
					$this->erpm->pnh_sendsms($emp_mob,$smsg);
	 				$this->db->query('insert into pnh_employee_grpsms_log(emp_id,contact_no,type,grp_msg,created_on)values(?,?,?,?,now())',array($empid,$emp_mob,2,$smsg,date('Y-m-d H:i:s')));
					break;
				}
			}
		}

		$this->cron_log(22);
				
	}

	/**
	 * function to update Purchase order status flag by delivery date 
	 * 
	 * @desc executed via cron every day at 5:00 AM 
	 * @author Suresh
	 */
	function update_postatus_bydeliverydate()
	{
		// fetch pending and partial open po list and date of delivery is expired   
		$sql = "select * from t_po_info where date_of_delivery < curdate() and date_of_delivery is not null and po_status in (0,1) ";
		$res = $this->db->query($sql);
		if($res->num_rows())
		{
			foreach($res->result_array() as $po)
			{
				// update all open pos to cancelled status and partial po to closed status 
				$this->db->query("update t_po_info set po_status=?,status_remarks=?,modified_on=now(),modified_by=? where po_id=? limit 1",array((($po['po_status']==1)?2:3),'By System',0,$po['po_id']));
			}
		}
	}

}