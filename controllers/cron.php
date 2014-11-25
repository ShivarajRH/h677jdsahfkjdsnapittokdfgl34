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
	
	/**
	 * Function to send offer of the day sms to franchise 
	 * @modified by Shivaraj
	 */
	function enday_orderd_sms_tofranchise()
	{
		$this->cron_log(19,1);
		$franchise_info_res=$this->db->query("SELECT franchise_id,franchise_name,current_balance,login_mobile1,login_mobile2 FROM pnh_m_franchise_info WHERE is_suspended=0");
			
		if($franchise_info_res ->num_rows())
		{
			foreach($franchise_info_res->result_array() as $franchise_det)
			{
				$curr_date = "CURDATE()"; //'2014-05-27'
				$day_orderd_amt_res=$this->db->query("SELECT IFNULL(ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2),0) AS amt,a.transid
														FROM king_transactions a
														JOIN king_orders b ON a.transid = b.transid
														JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id
														WHERE a.franchise_id = ? AND c.is_suspended = 0 AND DATE(FROM_UNIXTIME(a.init)) = $curr_date
														GROUP BY a.transid",array($franchise_det['franchise_id']) );
				$day_orderd_amt=0;
				foreach($day_orderd_amt_res->result_array() as $day_orders)
				{
					$transid = $day_orders['transid'];
					$amt = $this->erpm->trans_fee_insu_value($transid,$day_orders['amt']);
					$day_orderd_amt += $amt;
				}
				
				if( $day_orderd_amt > 0 )
				{
					$franchise_name=$franchise_det['franchise_name'];
					$login_mobile1=$franchise_det['login_mobile1'];
					// =====================< FRANCHISE OFFER OF THE DAY SMS START >===================================
					//$this->erpm->pnh_sendsms($login_mobile1,"Congratulations!!!Dear Franchise $franchise_name, your placed order of the day -Rs.$day_orderd_amt Happy Franchising",$franchise_det['franchise_id']);
					$offer_of_day_sms = "Congratulations!!! Dear Franchise $franchise_name, your placed order of the day Rs.$day_orderd_amt Happy Franchising";
					//echo $offer_of_day_sms;
					$this->erpm->pnh_sendsms($login_mobile1,$offer_of_day_sms,$franchise_det['franchise_id']);
					// =====================< FRANCHISE OFFER OF THE DAY SMS START >===================================
				}
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
	
	/**
	 * fucntion to updte franchise app version
	 */
	function cron_update_app_version_old()
	{
		return;
		ini_set('max_execution_time',6000);
		ini_set('memory_limit','1024M');
		
		// get working versions 
		$res_vl = $this->db->query("select id,release_version,code_version,max(db_version) as v,max(db_changes_v) as dcv from (select * from m_apk_version order by id desc) as g group by  release_version order by id desc ");
		if($res_vl->num_rows())
		{
			foreach($res_vl->result_array() as $row_vl)
			{
				// check for version price updates and status updates.
				$res_v = $this->db->query("select a.id,a.dealid,a.orgprice,a.price,b.publish,ifnull(if(c.id,0,1),1) as is_new_deal,ifnull(c.id,0) as deal_v_id,
												ifnull(if(c.mrp=a.orgprice,0,a.orgprice),0) as mrp_diff,
												ifnull(if(c.price=a.price,0,a.price),0) as price_diff,
												ifnull(if(b.publish=c.is_publish,0,1),0) as status_diff
												from king_dealitems a 
												join king_deals b on a.dealid = b.dealid 
												join m_apk_store_menu_link d on d.menu_id = b.menuid 
												join m_apk_version v on v.id = d.store_id
												left join m_apk_version_deal_link c on c.item_id = a.id and c.version_id = v.id 
											where store_id = ? and (c.version_id = ? or c.version_id is null ) 
											having mrp_diff+price_diff+status_diff != 0
										",array($row_vl['release_version'],$row_vl['id']));
				
				if(!$res_v->num_rows())
					continue;
				
				$row_vl['dcv'] = $row_vl['dcv']+1;
				
				// reset db version index on 3 digits
				if($row_vl['dcv'] > 999)
				{
					$row_vl['v'] = $row_vl['v']+1;
					$row_vl['dcv'] = 1;
				}
					
				
				$inp = array($row_vl['release_version'].'.'.$row_vl['code_version'].'.'.$row_vl['v'].'.'.$row_vl['dcv'],$row_vl['release_version'],$row_vl['code_version'],$row_vl['v'],$row_vl['dcv']);
				
				// create new version 
				$this->db->query("insert into m_apk_version (version,release_version,code_version,db_version,db_changes_v,created_by,created_on) values (?,?,?,?,?,0,now())",$inp);
				$new_version_id = $this->db->insert_id();
				
				foreach($res_v->result_array() as $row_v)
				{
					// get price,status and new deal updates.
					$inp = array($new_version_id,$row_v['id'],$row_v['orgprice'],$row_v['price'],$row_v['publish'],$row_v['is_new_deal']);
					$this->db->query("insert into m_apk_version_deal_link (version_id,item_id,mrp,price,is_publish,is_new,created_on,created_by) values(?,?,?,?,?,?,now(),0) ",$inp);
				}
			}
		}
		
	}
	
	/**
	 * fucntion to updte franchise app version
	 */
	function cron_update_app_version()
	{
		ini_set('max_execution_time',6000);
		ini_set('memory_limit','1024M');
	
		// get working versions
		$res_vl = $this->db->query("select id,release_version,code_version,max(db_version) as v,max(db_changes_v) as dcv from (select * from m_apk_version order by id desc) as g group by  release_version having release_version > 14 and release_version < 100 order by id desc ");
		if($res_vl->num_rows())
		{
			foreach($res_vl->result_array() as $row_vl)
			{
				
				$new_version_id = 0;
				
				
				// check for deleted deals and mark as publish 0 
				
				$del_deal_res = $this->db->query("select a.item_id,a.mrp,a.price,a.is_publish,if(ifnull(b.id,0),1,0) as pub
														from m_apk_version_deal_link a
														left join king_dealitems b on a.item_id = b.id 
														where a.version_id = ? and b.id is null 
														having pub = 0 and is_publish = 1
												 ",$row_vl['id']) or die(mysql_error());
				
				if($del_deal_res->num_rows())
				{
					$new_version_id = $this->_get_new_versionid($row_vl);
					
					foreach($del_deal_res->result_array() as $row_v)
					{
						// get price,status and new deal updates.
						$inp = array($new_version_id,$row_v['item_id'],$row_v['mrp'],$row_v['price'],$row_v['pub'],0);
						$this->db->query("insert into m_apk_version_deal_link (version_id,item_id,mrp,price,is_publish,is_new,created_on,created_by) values(?,?,?,?,?,?,now(),0) ",$inp);
					}
					
				}
				
				
				
				
				// check for version price updates and status updates.
				$res_v = $this->db->query("select release_version as store_id,a.id,a.dealid,a.orgprice,a.price,b.publish
						from king_dealitems a
						join king_deals b on a.dealid = b.dealid
												join m_apk_version_deal_link c on c.item_id = a.id 
												join m_apk_version d on d.id = c.version_id 
												where release_version =  ? 
											group by a.id ",array($row_vl['release_version']));
				
				if($res_v->num_rows())
				{
					foreach($res_v->result_array() as $row_v)
					{
						// check if the deal is available in the version 
						$item_vdet_res = $this->db->query("select a.*
													from m_apk_version_deal_link a 
													join m_apk_version b on a.version_id = b.id
													where release_version = ? and a.item_id = ? 
												order by a.id desc 
												limit 1 ",array($row_v['store_id'],$row_v['id']));
						
						
						$ins_v = array();
						if($item_vdet_res->num_rows())
						{
							// check if there is any diff to log
							$item_vdet = $item_vdet_res->row_array();
							
							$process_tolog = 0;
							if($item_vdet['mrp']*1 != $row_v['orgprice'])
								$process_tolog = 1;
							if($item_vdet['price']*1 != $row_v['price'])
								$process_tolog = 1;
							if($item_vdet['is_publish']*1 != $row_v['publish'])
								$process_tolog = 1;
							
							$ins_v['mrp'] = $row_v['orgprice'];
							$ins_v['price'] = $row_v['price'];
							$ins_v['is_publish'] = $row_v['publish'];
							if(!$process_tolog)
								continue ;
							
							$ins_v['is_new'] = 0;
						}else
						{
							
							if(!$row_v['publish'])
								continue;
							
							$ins_v['mrp'] = $row_v['orgprice'];
							$ins_v['price'] = $row_v['price'];
							$ins_v['is_publish'] = $row_v['publish'];
							$ins_v['is_new'] = 1;
						}

						$ins_v['item_id'] = $row_v['id'];
						$ins_v['created_by'] = 0;
						$ins_v['created_on'] = $this->db->query('select now() as t')->row()->t;
						
						// check  if $new_version_id is created 
						if(!$new_version_id)
							$new_version_id = $this->_get_new_versionid($row_vl);
						
						$ins_v['version_id'] = $new_version_id;
						
						$this->db->insert("m_apk_version_deal_link",$ins_v);// or die(mysql_error());
						
					}
				}
			}
		}
	}
	
	/**
	 * function to generate new version id 
	 */
	function _get_new_versionid($row_vl)
	{
		$row_vl['dcv'] = $row_vl['dcv']+1;
		
		// reset db version index on 3 digits
		if($row_vl['dcv'] > 999)
		{
			$row_vl['v'] = $row_vl['v']+1;
			$row_vl['dcv'] = 1;
		}
		
		
		$inp = array($row_vl['release_version'].'.'.$row_vl['code_version'].'.'.$row_vl['v'].'.'.$row_vl['dcv'],$row_vl['release_version'],$row_vl['code_version'],$row_vl['v'],$row_vl['dcv']);
		
		// create new version
		$this->db->query("insert into m_apk_version (version,release_version,code_version,db_version,db_changes_v,created_by,created_on) values (?,?,?,?,?,0,now())",$inp);
		$new_version_id = $this->db->insert_id();
		
		return $new_version_id;
	}
	/**
	 * function to Subscription order creation for member
	 */
	function subscription_order_creation()
	{
		
	/*	
		
		$order_list = $this->db->query("SELECT o.id as planorderid,o.itemid,o.sub_franchise_id as franchise_id,f.franchise_name,f.address,f.postcode,f.city,f.state,f.login_mobile1 as phone,f.email_id,o.order_qty as quantity,o.order_amount as paid,d.brandid,d.vendorid FROM m_member_subscription_plan_orderlist o
							join pnh_member_info m on m.id = o.member_id
							join pnh_m_franchise_info f on f.franchise_id = o.sub_franchise_id
							join king_dealitems t on t.id = o.itemid
							join king_deals d on d.dealid = t.dealid
							WHERE DATE(order_date)<=NOW() and is_active=0 and order_id=0 ORDER BY o.id ASC");
		
		if($order_list)
		{
			$orderlist = $order_list->result_array();
			foreach($orderlist as $p)
			{	
				$snp="PNH";				
				$transid=$snp.random_string("alpha",3).random_string("nozero",5);
				$transid=strtoupper($transid);
				$orderid=random_string("numeric",10);
				$params=array("bpid","id","transid","userid","itemid","brandid","vendorid","bill_person","bill_address","bill_landmark","bill_city","bill_state","bill_phone","bill_telephone","bill_pincode","bill_country","ship_person","ship_address","ship_landmark","ship_city","ship_state","ship_pincode","ship_phone","ship_telephone","ship_country","quantity","bill_email","ship_email","buyer_options");
				$sql="insert into king_orders(".implode(",",$params).",paid,mode) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
				$inp = array('',$orderid,$transid,'',$p['itemid'],$p['brandid'],'',$p['franchise_name'],$p['address'],'',$p['city'],$p['state'],$p['phone'],$p['phone'],$p['postcode'],'',$p['franchise_name'],$p['address'],'',$p['city'],$p['state'],$p['postcode'],$p['phone'],$p['phone'],'',$p['quantity'],$p['email_id'],$p['email_id'],'',$p['paid'],'0');
				if($this->db->query($sql,$inp))
				$this->db->query("update m_member_subscription_plan_orderlist set order_id = ? where id = ? ",array($orderid,$p['planorderid']));

			}
		}*/
		
		
		
		//http://localhost/snapittoday_live/api/to_process_order?fid=17&mid=22006889&pid=11314779&qty=1&member=1&offer_type=1&insurance[opted_insurance]=&insurance[insurance_deals]=14967769&insurance[proof_type]=&insurance[proof_id]=&insurance[first_name]=&insurance[last_name]=&insurance[mob_no]=&insurance[address]=&insurance[city]=&insurance[pincode]=&insurance[proof_name]=&insurance[proof_address]=&redeem=
		
		//error_reporting(E_ALL);
		//ini_set('display_errors',1);
		
		$order_list = $this->db->query("SELECT o.id as planorderid,o.itemid,o.sub_franchise_id as franchise_id,m.mobile,f.franchise_name,f.address,f.postcode,f.city,f.state,f.login_mobile1 as phone,f.email_id,o.order_qty as quantity,o.order_amount as paid,d.brandid,d.vendorid,t.pnh_id FROM m_member_subscription_plan_orderlist o
				join pnh_member_info m on m.id = o.member_id
				join pnh_m_franchise_info f on f.franchise_id = o.sub_franchise_id
				join king_dealitems t on t.id = o.itemid
				join king_deals d on d.dealid = t.dealid
				WHERE DATE(order_date)<=NOW() and is_active=0 and order_id=0 ORDER BY o.id ASC");
	if($order_list)
	{
		$orderlist = $order_list->result_array();
	foreach($orderlist as $ord)
	{
		
		$updated_by=0;
		$fid=$ord['franchise_id'];
		$mid=$ord['mobile'];
		$pid=array($ord['pnh_id']);
		$qty=$ord['quantity'];
		$planorderid = $ord['planorderid'];
		$d_attr = "";
		$offr_sel_type="";
		$insurance=0;
		$redeem=0;
		$member=1;
		
		$redeem_points = $redeem?150:0;
		
		$has_super_scheme=0;
		$has_scheme_discount=0;
		$has_member_scheme=0;
		$has_offer=0;
		
		// get franchise details
		$fran=$this->franchise_model->get_franchise_details($fid,$fid);
		
		if($fran['is_suspended']==1 || $fran['is_suspended']==3)
			return array('status'=>'error','error_code'=>2046,'error_msg'=>"Franchisee is suspended");
		
		if($fran['is_suspended']==0)
			$batch_enabled = 1;
		else
			$batch_enabled = 0;
		
		//get order member details
		$mem_det = $this->member_model->get_member_details($mid);
		
		// check if key member order [franchise mobile no in mid]
		$fran_det_res = $this->db->query("select * from pnh_m_franchise_info where (login_mobile1=? or login_mobile2=?)",array($mid,$mid));
		if($fran_det_res->num_rows())
			$order_for=2;
		
		if(!$mem_det && !$fran_det_res->num_rows())
			return array('status'=>'error','error_code'=>2046,'error_msg'=>"Invalid Member ID");
		else
			$mid=@$mem_det['pnh_member_id'];
		
		$mem_det = $this->member_model->get_member_details($mid);
		$userid=$mem_det['user_id'];
		
		$new_member=$is_new_member=0;
		//flag to check is new member
		if($mem_det)
		{
			$ttl_member_orders=$this->member_model->get_memberorders_ttl($mid);
			if($ttl_member_orders)
				$order_for=$new_member=$is_new_member=0;
			else
				$order_for=$new_member=$is_new_member=1;
		}
		
		$key_member=($order_for==2)?1:0;
		
		$pids=array();
		$pids['available']=array();
		$pids['not_available']=array();
		
		foreach($pid as $p)
		{			
			$deal=$this->db->query("select d.menuid,b.name as brand,c.name as cat,i.id,i.is_combo,i.pnh_id as pid,i.live,i.orgprice as mrp,i.price,i.name,i.pic,d.publish,p.is_sourceable,i.has_insurance,CONCAT(print_name,'-',pnh_id) AS print_name from king_dealitems i join king_deals d on d.dealid=i.dealid  left join king_brands b on b.id = d.brandid join king_categories c on c.id = d.catid JOIN `m_product_deal_link` l ON l.itemid=i.id JOIN m_product_info p ON p.product_id=l.product_id where pnh_id=? and is_pnh=1",$p)->row_array();
			$avail=$this->erpm->do_stock_check(array($deal['id']),array(1),true);
			$avail_det = array_values($avail);
			if($avail_det[0][0]['stk']==0 && $deal['is_sourceable']==0)
				array_push($pids['not_available'], $p);
			else
				array_push($pids['available'], $p);
		
			$menu_det=$this->db->query("select d.menuid,m.default_margin as margin from king_dealitems i join king_deals d on d.dealid=i.dealid JOIN pnh_menu m ON m.id=d.menuid where i.is_pnh=1 and i.pnh_id=?",$p)->row_array();
		
			$super_scheme=$this->db->query("select * from pnh_super_scheme where menu_id=? and is_active=1 and franchise_id = ? limit 1",array($menu_det['menuid'],$fid))->row_array();
			//super scheme enabled for scheme discount
			if(!empty($super_scheme))
			{
				if($super_scheme['valid_from']<time() && $super_scheme['valid_to']>time() && $super_scheme['is_active'] == 1)
					$has_super_scheme=1;
			}
		
			$member_scheme=$this->db->query("select * from imei_m_scheme where is_active=1 and franchise_id=? and ? between sch_apply_from and scheme_to order by created_on desc limit 1",array($fid,time()))->row_array();
			//member scheme enabled for scheme discount
			if(!empty($member_scheme))
			{
				$has_member_scheme=1;
			}
		
		
			$offer_scheme=$this->db->query("select * from pnh_m_offers where menu_id=? and franchise_id=? and ? between offer_start and offer_end order by id desc limit 1",array($menu_det['menuid'],$fid,time()))->row_array();
			if(!empty($offer_scheme))
			{
				$has_offer=1;
			}
		
		}
		//print_r($pids);
		// check if some products are not available and return the same
		if($pids['not_available'])
			return array('status'=>'error','error_code'=>2046,'error_msg'=>"Below Products are out of stock : \r\n".implode("\r\n",$pids['not_available']));
		
		if($pids['available'])
			$pids=$pids['available'];
		$menu_ofr_pricevalue=array();
		$items=array();
		foreach($pids as $i=>$p)
			$items[]=array("pid"=>$p,"qty"=>$qty[$i]);
		
		$total=0;
		$d_total=0;
		$o_total=0;
		$commision=0;
		$item_pnt = @$redeem_points/count($items);
		$redeem_value = 0;
		//print_r($items);exit;
		foreach($items as $i=>$item)
		{
			$prod=$this->db->query("select i.*,d.publish,c.loyality_pntvalue,d.menuid from king_dealitems i join king_deals d on d.dealid=i.dealid JOIN pnh_menu c ON c.id = d.menuid where i.is_pnh=1 and  i.pnh_id=? and i.pnh_id!=0",$item['pid'])->row_array();
			
			$ordered_menus_list[]=$prod['menuid'];
		
			if(empty($prod))
				die("There is no product with ID : ".$item['pid']);
		
			$items[$i]['tax']=$prod['tax'];
			$items[$i]['mrp']=$prod['orgprice'];
			if($fran['is_lc_store'])
				$items[$i]['price']=$prod['store_price'];
			else
				$items[$i]['price']=$prod['price'];
			$items[$i]['itemid']=$prod['id'];
		
			$margin=$this->erpm->get_pnh_margin($fran['franchise_id'],$item['pid']);
		
			$imei_disc=$this->erpm->get_franimeischdisc_pid($fran['franchise_id'],$item['pid']);
			if($imei_disc==0 && $key_member==1 && $prod['price']<=5000 && $prod['menuid']==112)
			{
				if($margin['margin']>=0.5)
				{
					$margin['margin']=$margin['margin']-0.5;
					$margin['base_margin']=$margin['base_margin']-0.5;
				}
			}
		
			if($prod['is_combo']=="1")
				$items[$i]['discount']=$items[$i]['price']/100*$margin['combo_margin'];
			else
				$items[$i]['discount']=$items[$i]['price']/100*$margin['margin'];
		
			$items[$i]['billon_orderprice']=$prod['billon_orderprice'];
			$items[$i]['margin']=$margin;
			$total+=$items[$i]['price']*$items[$i]['qty'];
		
			$menu_ofr_pricevalue[$prod['menuid']][]=$items[$i]['price']*$items[$i]['qty'];
		
			$d_total+=($items[$i]['price']-$items[$i]['discount'])*$items[$i]['qty'];
			$o_total+=$items[$i]['price']*$items[$i]['qty'];
			$commision+=$items[$i]['discount']*$items[$i]['qty'];
			$itemids[]=$prod['id'];
			$itemnames[]=$prod['name'];
			$loyalty_pntvalue=$prod['loyality_pntvalue'];
		
			// offers check
		
			if($offr_sel_type == 2 || $insurance['opted_insurance'] == 1 || $key_member==1 )
			{
				$insurance['menuids'][$item['pid']] = $prod['menuid'];
				$insurance['order_value'][$item['pid']] = $items[$i]['price'];
			}
		}
		
		foreach($menu_ofr_pricevalue as $i=>$t)
		{
			$menu_ttlval[$i]=array_sum($t);
		}
		
		$l_points=array();
		foreach($menu_ttlval as $l_menu_id=>$ttl_l_amt)
		{
			$points=@$this->db->query("SELECT points FROM pnh_loyalty_points WHERE menu_id=? AND ?>=amount AND is_active=1  ORDER BY amount DESC LIMIT 1",array($l_menu_id,$ttl_l_amt))->row()->points;
			$l_points[$l_menu_id]=$points*1;
		}
		
		if($redeem)
			$redeem_value += $item_pnt_value = $item_pnt*$prod['loyality_pntvalue'];
		
		// check if franchise has enough balance to process this order
		$fran_crdet = $this->erpm->get_fran_availcreditlimit($fran['franchise_id']);
		//$fran_crdet = 100000;
		
		$fran['current_balance'] = $fran_crdet[3];
		
		//$fran['current_balance'] = 10000;
		if($fran['current_balance']<$d_total)
		{
			return array('status'=>'error','error_code'=>2046,'purchase_limit'=>format_price($fran_crdet[3],0),'error_msg'=>"Insufficient balance! Balance in your account Rs {$fran['current_balance']} Total order amount : Rs $d_total");
		}
		
		$transid=strtoupper("PNH".random_string("alpha",3).$this->erpm->p_genid(5));
		
		$pnh_member_fee=0;
		if($is_new_member == 1 && $key_member == 0 )
		{
			$pnh_member_fee=PNH_MEMBER_FEE;
			$fee_det = array($mid,$transid,'',$pnh_member_fee,1,$updated_by);
			$this->db->query("insert into pnh_member_fee (member_id,transid,invoice_no,amount,status,created_on,created_by) VALUES(?,?,?,?,?,now(),?)",$fee_det);
		}
		
		$this->db->query("insert into king_transactions(transid,amount,paid,mode,init,actiontime,is_pnh,franchise_id,trans_created_by,batch_enabled,order_for,pnh_member_fee)
				values(?,?,?,?,?,?,?,?,?,?,?,?)"
				,array($transid,$d_total,$d_total,3,time(),time(),1,$fran['franchise_id'],$this->login_userid,$batch_enabled,$order_for,$pnh_member_fee)) or die(mysql_error());
		
		
		foreach($items as $item)
		{
			// check if belongs to split invoice condiciton config
			$split_order=$this->db->query("SELECT i.*,d.publish,c.loyality_pntvalue FROM king_dealitems i JOIN king_deals d ON d.dealid=i.dealid JOIN pnh_menu c ON c.id = d.menuid WHERE i.is_pnh=1 AND  i.pnh_id=? AND i.pnh_id!=0 AND c.id IN(112,118,122)",$item['pid'])->row_array();
			//$split_order = 0;
			if($split_order)
			{
				$ttl_qty = $item['qty'];
				$p_qty = 1;
			}else
			{
				$ttl_qty = 1;
				$p_qty = $item['qty'];
			}
		
			for($qi=0;$qi<$ttl_qty;)
			{
		
				$qi = $qi+$p_qty;
		
				$inp=array("id"=>$this->erpm->p_genid(10,'order'),"transid"=>$transid,"userid"=>$userid,"itemid"=>$item['itemid'],"brandid"=>"");
		
				$item['qty'] = $p_qty;
		
				$inp["brandid"]=$this->db->query("select d.brandid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?",$item['itemid'])->row()->brandid;
				$brandid=$inp["brandid"];
				$catid=$this->db->query("select d.catid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?",$item['itemid'])->row()->catid;
				$menuid=$this->db->query("select d.menuid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=? and menuid2=0",$item['itemid'])->row()->menuid;
				$inp["bill_person"]=$inp['ship_person']=$fran['franchise_name'];
				$inp["bill_address"]=$inp['ship_address']=$fran['address'];
				$inp["bill_city"]=$inp['ship_city']=$fran['city'];
				$inp['bill_pincode']=$inp['ship_pincode']=$fran['postcode'];
				$inp['bill_phone']=$inp['ship_phone']=$fran['login_mobile1'];
				$inp['bill_email']=$inp['ship_email']=$fran['email_id'];
				$inp['bill_state']=$inp['ship_state']=$fran['state'];
				$inp['quantity']=$p_qty;//$item['qty'];
				$inp['time']=time();
				$inp['ship_landmark']=$inp['bill_landmark']=$fran['locality'];
				$inp['bill_country']=$inp['ship_country']="India";
				$inp['i_orgprice']=$item['mrp'];
				$inp['i_price']=$item['price'];
				$inp['i_discount']=$item['mrp']-$item['price'];
				$inp['i_coup_discount']=$item['discount'];
				$inp['redeem_value']=($item['price']/($total+$redeem_value))*$redeem_value;
				$inp['billon_orderprice']=$item['billon_orderprice'];
				$inp['member_id']=$mid;
		
				if($split_order && $key_member==1)
				{
		
					$membr_id=$this->erpm->_gen_uniquememberid();
					if($this->db->query("select * from pnh_member_info where pnh_member_id=?",$membr_id)->num_rows()==0);
					$inp['member_id']=$membr_id;
					$inp['is_ordqty_splitd']=1;
		
					$this->db->query("insert into king_users(name,is_pnh,createdon) values(?,1,?)",array("PNH Member: $membr_id",time()));
					$userid=$this->db->insert_id();
					$inp['userid']=$userid;
					$this->db->query("insert into pnh_member_info(pnh_member_id,user_id,franchise_id,created_by,created_on)values(?,?,?,?,?)",array($membr_id,$userid,$fid,$this->login_userid,time()));
		
					//KEY MEMBER
		
					if($key_member==1)
					{
		
						//echo $this->db->last_query();die();
						$item_total=$item['price']*$p_qty;
						if($item_total<=5000 && $menuid==112)
						{
							$inp['pnh_member_fee']=0;
							$inp['insurance_amount']=0;
							if($imei_disc==0)
							{
								$key_mem_imei=0.5;
								$this->db->query("insert into imei_m_scheme(franchise_id,menuid,categoryid,brandid,scheme_type,credit_value,scheme_from,scheme_to,sch_apply_from,created_on,created_by,is_active)values(?,?,?,?,?,?,unix_timestamp(curdate()),unix_timestamp(curdate()),unix_timestamp(curdate()),unix_timestamp(curdate()),?,1)",array($fid,$menuid,$catid,$brandid,1,$key_mem_imei,$updated_by));
								$key_imei_id=$this->db->insert_id();
								$inp['imei_reimbursement_value_perunit']=(($inp['i_orgprice']-($inp['i_discount']+$inp['i_coup_discount']))*$key_mem_imei/100);
								$inp['imei_scheme_id']=$key_imei_id;
		
								//Disabling IMEI scheme after key member order is placed;
								if($key_imei_id)
									$this->db->query("Update imei_m_scheme set is_active=0 where is_active=1 and id=?",$key_imei_id);
							}else
							{
								$inp['imei_scheme_id']=$imei_disc['id'];
		
								if($imei_disc['scheme_type']==0)
									$inp['imei_reimbursement_value_perunit']=$imei_disc['credit_value'];
								else
									$inp['imei_reimbursement_value_perunit']=(($inp['i_orgprice']-($inp['i_discount']+$inp['i_coup_discount']))*$imei_disc['credit_value']/100);
		
							}
						}
						if($item_total>5000 && $item_total<=10000)
						{
							$insurance_id = random_string("nozero", $len=2).time(); //$this->get_insurance_id();
							$insurance_deals=$item['itemid'];
							$inp['pnh_member_fee']=PNH_MEMBER_FEE;
							$inp['insurance_amount']=0;
							$inp['insurance_id']=$insurance_id;
							$d_total+=$inp['pnh_member_fee']+$inp['insurance_amount'];
							$pnh_member_fee+=$inp['pnh_member_fee'];
							$inp['imei_scheme_id']=0;
							$inp['imei_reimbursement_value_perunit']=0;
						}
		
						if($item_total>10000)
						{
							$insurance_id = random_string("nozero", $len=2).time();
							$inp['pnh_member_fee']=PNH_MEMBER_FEE;
							$insuranc_cost=(($item['price']-10000)*1)/100;
							$inp['insurance_amount']=$insuranc_cost;
							$inp['insurance_id']=$insurance_id;
							$inp['has_insurance']=1;
							$d_total+=$insuranc_cost+PNH_MEMBER_FEE;
							$pnh_member_fee+=$inp['pnh_member_fee'];
							$inp['imei_scheme_id']=0;
							$inp['imei_reimbursement_value_perunit']=0;
						}
					}
				}
		
				$inp['i_tax']=$item['tax'];
		
				if($has_member_scheme==1 && $key_member!=1)
				{
					//check item enabled for member scheme
					$check_mbrschdisableditem=$this->db->query("select * from pnh_membersch_deals where is_active=0 and ? between valid_from and valid_to and itemid=? order by created_on desc limit 1",array(time(),$item['itemid']))->row_array();
		
					$member_scheme_brand=$this->db->query("select * from imei_m_scheme where menuid=? and categoryid=? and brandid=? and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$catid,$brandid,$fid))->result_array();
					if(empty($member_scheme_brand))
						$member_scheme_brand=$this->db->query("select * from imei_m_scheme where menuid=? and categoryid=? and brandid=0 and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$catid,$fid))->result_array();
					if(empty($member_scheme_brand))
						$member_scheme_brand=$this->db->query("select * from imei_m_scheme where menuid=? and categoryid=0 and brandid=? and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$brandid,$fid))->result_array();
					if(empty($member_scheme_brand))
						$member_scheme_brand=$this->db->query("select * from imei_m_scheme where menuid=? and categoryid=0 and brandid=0 and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$fid))->result_array();
		
					if(!empty($member_scheme_brand)  && empty($check_mbrschdisableditem))
						foreach($member_scheme_brand as $member_scheme)
						{
							$inp['imei_scheme_id']=$member_scheme['id'];
							if($member_scheme['scheme_type']==0)
								$inp['imei_reimbursement_value_perunit']=$member_scheme['credit_value'];
							else
								$inp['imei_reimbursement_value_perunit']=(($inp['i_orgprice']-($inp['i_discount']+$inp['i_coup_discount']))*$member_scheme['credit_value']/100);
						}
				}
				//if super scheme is enabled
		
				if($has_super_scheme!=0)
				{
		
					//check item enabled for super scheme
					$check_superschdisableditem=$this->db->query("select * from pnh_superscheme_deals where is_active=0 and ? between valid_from and valid_to and itemid=? order by created_on desc limit 1",array(time(),$item['itemid']))->row_array();
		
					//$super_scheme_brand=$this->db->query("select * from pnh_super_scheme where brand_id=? and franchise_id = ? ",array($brandid,$fid))->result_array();
					$super_scheme_brand=$this->db->query("select * from pnh_super_scheme where menu_id=? and cat_id=? and brand_id=? and franchise_id = ? and is_active=1",array($menuid,$catid,$brandid,$fid))->result_array();
					if(empty($super_scheme_brand))
						$super_scheme_brand=$this->db->query("select * from pnh_super_scheme where menu_id=? and cat_id=0 and brand_id=? and franchise_id = ? and is_active=1 order by id desc limit 1",array($menuid,$brandid,$fid))->result_array();
					if(empty($super_scheme_brand))
						$super_scheme_brand=$this->db->query("select * from pnh_super_scheme where menu_id=? and cat_id=? and brand_id=0 and franchise_id = ? and is_active=1 order by id desc limit 1",array($menuid,$catid,$fid))->result_array();
					if(empty($super_scheme_brand))
						$super_scheme_brand=$this->db->query("select * from pnh_super_scheme where menu_id=? and cat_id=0 and brand_id=0 and franchise_id = ? and is_active=1 order by id desc limit 1",array($menuid,$fid))->result_array();
					//print_r($super_scheme_brand);
					if(!empty($super_scheme_brand) && empty($check_superschdisableditem))
					{
		
						foreach($super_scheme_brand as $super_scheme)
						{
							if($super_scheme['valid_from']<time() && $super_scheme['valid_to']>time() && $super_scheme['is_active'] == 1)
							{
		
								$inp['super_scheme_logid']=$super_scheme['id'];
								$inp['has_super_scheme']=1;
								$inp['super_scheme_target']=$super_scheme['target_value'];
								$inp['super_scheme_cashback']=$super_scheme['credit_prc'];
							}
						}
					}
				}
		
				if($has_offer==1)
				{
					$offer_det=$this->db->query("select * from pnh_m_offers where menu_id=? and cat_id=? and brand_id=? and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$catid,$brandid,$fid))->result_array();
		
					if(empty($offer_det))
						$offer_det=$this->db->query("select * from pnh_m_offers where menu_id=? and cat_id=? and brand_id=0 and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$catid,$fid))->result_array();
					if(empty($offer_det))
						$offer_det=$this->db->query("select * from pnh_m_offers where menu_id=? and cat_id=0 and brand_id=? and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$brandid,$fid))->result_array();
		
					if(empty($offer_det))
						$offer_det=$this->db->query("select * from pnh_m_offers where menu_id=? and cat_id=0 and brand_id=0 and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$fid))->result_array();
		
					if(!empty($offer_det))
					{
						foreach($offer_det as $offer)
						{
							$inp['has_offer']=1;
							$inp['offer_refid']=$offer['id'];
						}
					}
		
				}
				if($this->db->insert("king_orders",$inp))//// for member subscription plan values
						$this->db->query("update m_member_subscription_plan_orderlist set order_id = ?,transid=? where id = ? ",array($inp['id'],$transid,$planorderid));
				//echo $this->db->last_query(); exit;
				foreach($this->db->query("select group_id from m_product_group_deal_link where itemid=?",$inp['itemid'])->result_array() as $g)
				{
					$attr_n=array();
					$attr_v=array();
					foreach($this->db->query("select attribute_name_id from products_group_attributes where group_id=?",$g['group_id'])->result_array() as $p)
					{
						$attr_n[]=$p['attribute_name_id'];
						$attr_v[]=$this->input->post($item['pid']."_".$p['attribute_name_id']);
					}
					$sql="select product_id from products_group_pids where attribute_name_id=? and attribute_value_id=?";
					foreach($this->db->query($sql,array($attr_n[0],$attr_v[0]))->result_array() as $p)
					{
						$f=true;
						foreach($attr_n as $i=>$an)
							if($this->db->query("select 1 from products_group_pids where product_id=? and attribute_name_id=? and attribute_value_id=?",array($p['product_id'],$an,$attr_v[$i]))->num_rows()==0)
							$f=false;
						if($f)
							break;
					}
					$this->db->insert("products_group_orders",array("transid"=>$transid,"order_id"=>$inp['id'],"product_id"=>$p['product_id']));
				}
		
				// new
				if($this->db->query("select is_group from king_dealitems where id=? and is_group = 1 ",$inp['itemid'])->num_rows())
				{
					if(isset($d_attr[$inp['itemid']]))
					{
						// check if the product has default vendor linked
						$ven_id = @$this->db->query("select vendor_id from m_vendor_product_link where product_id = ? ",$d_attr[$inp['itemid']])->row()->vendor_id;
						$ven_id = $ven_id*1;
						// update ordered product_id
						$this->db->query("update king_orders set order_product_id = ?,order_from_vendor=? where id = ? ",array($d_attr[$inp['itemid']],$ven_id,$inp['id']));
					}
				}
		
				$bal_discount_amt = ($item['price']*$item['margin']['bal_discount']/100)*$item['qty'];
				$m_inp=array("transid"=>$transid,"itemid"=>$item['itemid'],"mrp"=>$item['mrp'],"price"=>$item['price'],"base_margin"=>$item['margin']['base_margin'],"sch_margin"=>$item['margin']['sch_margin'],"qty"=>$item['qty'],"final_price"=>$item['price']-$item['discount']);
				$this->db->insert("pnh_order_margin_track",$m_inp);
				$id=$this->db->insert_id();
			}
		}
		
		$bal_discount_amt_msg = '';
		if($bal_discount_amt)
			$bal_discount_amt_msg = ', Topup Damaka Applied : Rs'.$bal_discount_amt;
		
		$this->erpm->pnh_fran_account_stat($fran['franchise_id'],1, $d_total,"Order $transid - Total Amount: Rs $total".$bal_discount_amt_msg,"transaction",$transid);
		
		$balance=$this->db->query("select current_balance from pnh_m_franchise_info where franchise_id=?",$fran['franchise_id'])->row()->current_balance;
		
		
		$this->erpm->sendsms_franchise_order($transid,$d_total,$o_total);
		if($order_for!=2)
		{
			// ======================< MEMBER ORDER SMS >===================================
			if($mem_det['mobile'] != '' && strlen($mem_det['mobile'])>=10)
			{
				$mem_msg ="Thank you for ordering with StoreKing.";
				$this->erpm->pnh_sendsms($mem_det['mobile'],$mem_msg,0,$mid,'MEM_ORDER');
			}
		}
		// ======================< MEMBER ORDER SMS >===================================
		//Alotting Loyalty Points
		$trans_order_det_res = $this->db->query("SELECT o.id AS order_id,o.itemid,d.menuid,o.i_price,o.quantity,t.order_for
				FROM king_dealitems i
				JOIN king_deals d ON d.dealid=i.dealid
				JOIN king_orders o ON o.itemid = i.id
				join king_transactions t on t.transid=o.transid
				WHERE o.transid=?
				",$transid);
		if($trans_order_det_res->num_rows())
		{
			foreach($trans_order_det_res->result_array() as $trans_ord_det)
			{
				$menuid = $trans_ord_det['menuid'];
				$order_amt=$trans_ord_det['i_price']*$trans_ord_det['quantity'];
				if($key_member==0)
					$ord_loyal_pnt = (($trans_ord_det['i_price']*$trans_ord_det['quantity'])*$l_points[$menuid])/$menu_ttlval[$menuid];
				else
					$ord_loyal_pnt = $this->db->query("select points from pnh_loyalty_points where amount < ? order by amount desc limit 1",$order_amt)->row()->points;
		
				$this->db->query("update king_orders set loyality_point_value=? where  transid=? and id=? ",array($ord_loyal_pnt,$transid,$trans_ord_det['order_id']));
			}
		}
		// Process to batch this transaction
		$ttl_num_orders=count($items);
		$batch_remarks='Created by pnh offline order system';
		
		//$this->reservations->do_batching_process($transid,$ttl_num_orders,$batch_remarks,$updated_by);
		
		
		
		//===================< Implement the member offers START>============================//
		if($key_member==1)
		{
			$datetime=date("Y-m-d H:i:s",time());
			//check_for_insurancance applicable order
			$key_mem_insurance_items=$this->db->query("select * from king_orders where transid=? and insurance_id is not null",$transid);
			if($key_mem_insurance_items)
			{
				foreach($key_mem_insurance_items->result_array() as $kydl)
				{
					$key_mem_insu_id=$kydl['insurance_id'];
					$kydl['fid']=$fid;
					$key_mid=$kydl['member_id'];
					$itemid=$kydl['itemid'];
					$order_id=$kydl['id'];
					$insurance_value=$kydl['insurance_amount'];
					$ofr_towords=$kydl['i_price']-$kydl['i_coup_discount'];
		
					$this->db->query("insert into pnh_member_insurance(fid,mid,offer_type,opted_insurance,order_id,itemid,insurance_value,created_by,created_on)values(?,?,2,1,?,?,?,?,?)",array($fid,$key_mid,$order_id,$itemid,$insurance_value,$updated_by,$datetime));
					$insurance_id=$this->db->insert_id();
					$this->db->query("insert into pnh_member_offers(insurance_id,franchise_id,member_id,offer_type,order_id,transid_ref,offer_value,created_by,created_on,process_status,delivery_status,feedback_status,offer_towards,pnh_pid) values(?,?,?,2,?,?,?,?,?,0,0,0,?,?)",array($insurance_id,$fid,$key_mid,$order_id,$transid,$insurance_value,$updated_by,$datetime,$ofr_towords,$itemid));
					$this->db->query("update king_orders set insurance_id=? where transid=? and insurance_id=? and id=?",array($insurance_id,$kydl['transid'],$key_mem_insu_id,$kydl['id']));
				}
			}
		}
		$menu_list=array_unique($ordered_menus_list);
		$insurance['mid'] =$mid;
		$insurance['fid'] =$fid;
		$insurance['offer_type'] =$offr_sel_type;
		$insurance['transid'] = $transid;
		$insurance['created_by'] = $updated_by;
		
		// check is member fee paid?
		$insurance['pnh_member_fee'] = PNH_MEMBER_FEE;
		
		// =================< Check total member orders >======================
		$orders=$this->db->query("SELECT COUNT(DISTINCT(a.transid)) AS l FROM king_orders a
				join pnh_member_info b on b.user_id=a.userid
				WHERE b.pnh_member_id=?  AND a.status NOT IN (3)",$insurance['mid'])->row()->l;
		if($orders > 1)
			$insurance['mem_fee_applicable'] = 0;
		else
			$insurance['mem_fee_applicable'] = 1;
		
		$insurance['new_member']=$new_member;
		
		if($offr_sel_type == 2 && $insurance['opted_insurance'] == 1 && $new_member == 1)
		{
			//process insurance document and address details & get insurance process id
			$insu_id = $this->erpm->process_insurance_details($insurance);
			//echo '<pre>';print_r($insurance);die();
		}elseif($offr_sel_type == 3 && $new_member == 1)
		{
			$insurance['offer_type'] = 3;
			$insu_id = $this->erpm->process_insurance_details($insurance);
		}
		elseif($offr_sel_type == 2  && $new_member == 1)
		{
			$insurance['offer_type'] = 3;
			$offer_ret = $this->erpm->pnh_member_fee($d_total,$insurance);
		}
		elseif($offr_sel_type == 0 && $insurance['opted_insurance'] == 1 && $new_member == 0)
		{
			//process insurance document and address details & get insurance process id
			$insurance['mem_fee_applicable'] = 0;
			$insurance['pnh_member_fee'] = 0;
			$insu_id = $this->erpm->process_insurance_details($insurance);
		
		}
		
		elseif($offr_sel_type == 1 && $o_total >= MEM_MIN_ORDER_VAL && $new_member == 1)
		{
		
			$offer_ret = $this->erpm->pnh_member_recharge($o_total,$insurance);
		}
		
		//===================< Implement the member offers END>============================
		$ttl_insurance_amt=$this->db->query("select ifnull(sum(insurance_amount),0) as insurance_amount from king_orders where transid=?",$transid)->row()->insurance_amount;
		
		$order_res=$this->db->query("select order_for,o.*,di.name as item_name,pnh_id from king_orders o join king_transactions t on t.transid = o.transid join king_dealitems di on di.id = o.itemid where o.transid=?",$transid);
		$transid= '';
		$trans_amt = 0;
		$ttl_member_fee = 0;
		$order_det_arr=array();
		foreach($order_res->result_array() as $i=>$order_det)
		{
		
			$transid= $order_det['transid'];
		
			$order = array();
			$order['order_id'] = $order_det['id'];
			$order['product_id'] = $order_det['pnh_id'];
			$order['item_name'] = $order_det['item_name'];
			$order['mrp'] = $order_det['i_orgprice'];
			$order['offer_price'] = $order_det['i_orgprice']-$order_det['i_discount'];
			$order['has_insurance'] = $order_det['has_insurance'];
			$order['insurance_fee'] = $order_det['insurance_amount'];
			$order['franchise_price'] = $order_det['i_orgprice']-$order_det['i_discount']-$order_det['i_coup_discount'];
			$order['franchise_price_percentage'] = 100-($order['franchise_price']/$order['offer_price'])*100 .'%';
		
			// che ck if its key member order to process member for individual members by member id
			if($order_det['order_for'] != 2)
				$order['member_fee'] = @$this->db->query("select amount from pnh_member_fee where member_id = ? and transid = ? ",array($order_det['member_id'],$order_det['transid']))->row()->amount*1;
			else
				$order['member_fee'] = $order_det['pnh_member_fee']*(!$i); // reset for memberid for member fee
		
			$trans_amt += $order['franchise_price'];
			$ttl_member_fee += $order['member_fee'] ;
		
			$order_det_arr[] = $order;//array('transid'=>$order_det['transid'],'product_name'=>$prod_name,'Order_amt'=>$ordr_amt,'insurance_amt'=>$insu_fee,'member_fee'=>$mem_fee,'commission'=>$commission,'avail_bal'=>$avail_bal)
		
		}
		
		$trans_amt += $ttl_member_fee;
		
		//$this->_output_handle('json',true,array('transid'=>$order_det['transid'],'order_details'=>$memfee));
		$order_for=$order_for==2?'Key Member':'Non key Member';
		
		//return array('status'=>'success','trans'=>$transid,'orders'=>$order_det_arr,'total_member_fee'=>$ttl_member_fee,'order_for'=>$order_for);
	}
	}
		
	}
	
}