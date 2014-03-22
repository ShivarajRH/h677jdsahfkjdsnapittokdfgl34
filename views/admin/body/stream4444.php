<?php
include APPPATH.'/controllers/voucher.php';
class Stream extends Voucher 
{
    /**
     * Function to get count of unreplied comments
     * @param type $stream_id
     */
    function jx_get_unreplied_posts($stream_id) {
        $user=$this->erpm->auth(true,true);
        $count_elt=$this->db->query("select count(*) as total from m_stream_posts sp where sp.stream_id=? and sp.id NOT IN (select post_id from m_stream_post_reply)",$stream_id)->row_array();
        echo $count_elt['total'];
    }
    
    /**
     * Function to store assigned user 
     */
    function jx_save_assign_user() {
        $user=$this->erpm->auth(ADMINISTRATOR_ROLE,true);
        $this->erpm->do_save_assign_user($user);
    }
    
    /**
     * Function to remove assigned user 
     */
    function jx_remove_assign_user() {
        $user=$this->erpm->auth(ADMINISTRATOR_ROLE,true);
        $this->erpm->do_remove_assign_user($user);
    }
    
    /**
     * Function to edit streams
     * @param type $streamid
     */
    function stream_edit($streamid='') {
        $user=$this->auth(ADMINISTRATOR_ROLE);
        if($_POST) {
            $this->erpm->do_updatestream($user);
        }
        if($streamid!='') { 
            $user=$this->auth(ADMINISTRATOR_ROLE);
            $data['streams']=$this->db->query("Select s.*,ka.username,ka.email,ka.mobile from m_streams s
                                join king_admin ka on ka.id=s.created_by
                                where s.id=?
                                order by s.created_time desc",$streamid)->row_array();

            $data['adminusers']=$this->db->query("select id,user_id,name,username from king_admin where account_blocked!=1 order by username asc")->result_array();
        }
        else {
            $data['status']='fail';
            $data['message']='Undefined streamid.';
        }

        $data['page']="stream_edit";
        $this->load->view("admin",$data);
    }
    
    /**
     * Manage Streams
     */
    function streams_manager() 
    {
        $user=$this->auth(ADMINISTRATOR_ROLE);
        $data['streams']=$this->db->query("Select s.*,ka.username,ka.email,ka.mobile from m_streams s
                                join king_admin ka on ka.id=s.created_by
                                order by s.created_time desc")->result_array();
                                //where s.created_by=?,$user['userid']

        $data['page']="streams_manager";
        $this->load->view("admin",$data);
    }
	
    /**
     * Function to display streams
     */
    function streams() 
    {
    	$data['user']=$user=$this->erpm->auth();

		$oh_cond='';
        if(!$this->erpm->auth(true,true)) 
            $oh_cond=' and su.user_id='.$user['userid'];

	        $data['streams']=$this->db->query("select s.*,su.* from m_streams s 
                                            join m_stream_users su on su.stream_id = s.id
                                                where status=1 ".$oh_cond." group by s.id order by s.title asc")->result_array();

                $data['users']=$this->db->query("select * from king_admin order by name asc")->result_array();
                $data['page']="streams";
                $this->load->view("admin",$data);
	    
	}
        
	/**
     * Function to add stream
     */
	function stream_create() 
	{
	    $user=$this->erpm->auth(ADMINISTRATOR_ROLE);
	        if($_POST) {
	            $this->erpm->do_addstream($user);
	        }
		$data['adminusers']=$this->db->query("select id,user_id,name,username from king_admin where account_blocked!=1 order by username asc")->result_array();
	    $data['page']="stream_create";
	    $this->load->view("admin",$data);
	}
	
	
	/**
	 * Function to get user stream notifications
	 * @param type $userid
	 */
		function jx_get_stream_notifications($userid,$update='') {
            $user=$this->erpm->auth();
	    if($update == 1) {
	        $this->db->query("update m_stream_post_assigned_users set viewed=1 where assigned_userid=?",$userid);
	    }
	    $rslt=  $this->db->query("select * from m_stream_post_assigned_users spau
	                                where spau.viewed=0 and spau.assigned_userid=?",$userid);
	    if($rslt->num_rows()) {
	        echo $rslt->num_rows();
	    }
	    else echo '';
	}
	
	/**
	* Replace links in text with html links
	*
	* @param  string $text
	* @return string
	*/
	function auto_link_text($text)
	{//'@(http)?(s)?(://)?(([-\w]+\.)+([^\s]+)+[^,.\s])@'
	  $data=preg_replace('@(http)?(s)?(://)+(([-\w]+\.?)+([^\s]+)+[^,.\s])@', '<a href="http$2://$4" target="_blank">$1$2$3$4</a>', $text);
	  return trim(nl2br($data));
	}
	
	function jx_store_subreplies($post_id) {
	    $user=$this->erpm->auth();
	    if($_POST) {
	        $this->erpm->do_store_post_reply($post_id);
	    }
	}
	
	function jx_get_admindetails($id) {
	    $user=$this->erpm->auth();
	    $rdata = $this->db->query('select id,name,username,usertype,access,email,mobile,gender,city img_url from king_admin where account_blocked="0" and id = ? limit 1',$id)->row_array();
	    echo json_encode($rdata);
	}
	
	function post_reply($post_id) {
	    $user=$this->erpm->auth();
	    $arr_replies = $this->db->query('select spr.*,ka.id,ka.username,ka.email,ka.img_url from m_stream_post_reply spr
	                                        join king_admin ka on ka.id=spr.replied_by
	                                        where status=1 and post_id = ? and account_blocked!=1 
	                                        order by replied_on desc limit 0,10',$post_id)->result_array(); 
	    if($arr_replies['img_url']=='' || $arr_replies['img_url']==null) 
	    { 
	        $divimgurl='<img src="'.base_url().'images/icon_comment.gif" alt="Reply"/>'; 
	    }
	    else 
	    { 
	        $divimgurl='<img src="'.$post['img_url'].'" alt="Image"/>'; 
	    }
	    $outdata='';
	    foreach($arr_replies as $replydata) {
	        $outdata.='<div class="subreply">
	                        <div class="img_div">'.$divimgurl.'</div>
	                        <div class="desc">'.$this->auto_link_text($replydata['description']).'  </div>
                                    <div class="clear"></div>
                                <div class="action_block"><a href="#_'.$replydata['id'].'">'.ucfirst($replydata['username']).'</a>
                                    <abbr class="timeago" title="'.date("Y-m-d H:i:s",$replydata['replied_on']).'">&nbsp;</abbr>
                                </div>
                               
                            </div>';
	    }
	    return $outdata;
	}

	function get_post_reply_list($post_id) {
	    $user=$this->erpm->auth();
	    $outdata=$this->post_reply($post_id);
	    return $outdata;
	}
	
	function jx_post_reply_list($post_id) {
	    $user=$this->erpm->auth();
	    $outdata=$this->post_reply($post_id);
	    echo $outdata;
	}
	
	function jx_get_streampostdetails($streamid) 
	{
		
		
		$user=$this->erpm->auth();
                $date_cond='';
                if(isset($_POST['date_from'])) {
                    $dt_st = strtotime($this->input->post('date_from').' 00:00:00');
                    $dt_end=  strtotime($this->input->post('date_to').' 23:59:59');
                }
                else {
                    $dt_st = strtotime(date('Y-m-d 00:00:00',  time()-60*60*24*30));
                    $dt_end=  strtotime(date('Y-m-d 23:59:59',  time()));
                }

                $date_cond="and (sp.posted_on between $dt_st and $dt_end )";

                $output['date_output']="Posts from ".date("M/d/Y",$dt_st)." to ".date("M/d/Y",$dt_end);
            
	    
                $arr_streams_rslt=$this->db->query("select sp.*,ka.id as userid,ka.username,ka.name,ka.email from m_stream_posts sp
	                                    join king_admin ka on ka.id=sp.posted_by
	                                    where sp.stream_id=? and sp.status=1 $date_cond
	                                    order by sp.posted_on desc",$streamid);
	                                    
	                            // echo $this->db->last_query();       
	    $arr_streams=$arr_streams_rslt->result_array();
	    $total_items= $output['total_items']=$arr_streams_rslt->num_rows();
	    if($total_items>0) {
	        $output['items']="<table border='0' width='100%'>
	                        <thead><tr><th></th></tr></thead>
	                        <tbody>"; 
	        foreach($arr_streams as $post) 
	        {
	            $streamed_users_list='';
	            $arr_streamed_users_list= $this->db->query("select sau.*,ka.name,ka.username,ka.email,ka.mobile,ka.gender,ka.img_url from m_stream_post_assigned_users sau
	 join king_admin ka on ka.id=sau.assigned_userid where ka.account_blocked!=1 and sau.post_id=?",$post['id'])->result_array();
	            $i=1;
	            foreach($arr_streamed_users_list as $sau) {
	                if($sau['assigned_userid']==$user['userid']) {
	                    $streamed_users_list.='<a href="" class="stream_assigned_users" id="'.$sau['assigned_userid'].'">you</a>';
	                }
	                else { 
	                    $streamed_users_list.='<a href="" class="stream_assigned_users" id="'.$sau['assigned_userid'].'">'.ucfirst($sau['username']).'</a>'; 
	                }
	                if($i< count($arr_streamed_users_list)) {
	                    $streamed_users_list.=', ';
	                }
	                $i++;
	            }
	            $streamed_users_list=($streamed_users_list=='')?'all':$streamed_users_list;
	            $username=($post['userid']==$user['userid'])?'Me':ucfirst($post['username']);
	            
	            if($post['img_url']=='' || $post['img_url']==null) 
	                $divimgurl='<img src="'.base_url().'images/unknown_man.jpg" alt="Image"/>'; 
	            else 
	                $divimgurl='<img src="'.$post['img_url'].'" alt="Image"reply_image"/>'; 
	            
	            $post_replies_arr=$this->get_post_reply_list($post['id']);
	            
	            $output['items'].='<tr>
	                                    <td width="100%"><div class="stream_item_admin_div">
	                                            <div class="reply_image_div">'.$divimgurl.'</div>
	                                            <div class="reply_box">
	                                                    <div class="title">
	                                                    <a name="stream_li" id="'.$post['id'].'">
	                                                        <strong>'.$username.'</strong>
	                                                    </a>
	                                                    </div>
	                                                    <div class="title_to"> &nbsp;&nbsp;to '.($streamed_users_list).'</div>
	                                                    
	                                                    <p class="reply_desc">'. $this->auto_link_text($post['description']). '</p>
	                                                    <div class="reply_actions">
	                                                        <abbr class="timeago reply_date" title="'.date("Y-m-d H:i:s",$post['posted_on']).'">&nbsp;</abbr>
	                                                        <span class="reply_link">
	                                                            <a href="javascript:void(0)" id="'.$post['id'].'" onclick="return reply_block(this,'.$post['userid'].','.$streamid.')" >Reply</a>
	                                                        </span>
	                                                    </div>
	                                                    <div class="sub_reply_list" id="sub_reply_list_'.$post['id'].'">'.$post_replies_arr.'</div>
	                                                    <div class="stream_item_reply_div" id="stream_item_reply_div_'.$post['id'].'"></div>
	                                            </div>
	                                    </td>
	                                </tr>';
	        }
	        $output['items'].='</tbody>
	            </table>';
	    } 
            else { $output['items']='<h4 align="center">No Stream posts found</h4>'; }
                
                echo json_encode($output);
        }
	  
	function jx_get_streamdetails($streamid) 
	{
            $user=$this->erpm->auth();
            $output='';
    		$arr_userids=$this->db->query("select su.*,ka.name,ka.username from m_stream_users as su 
                                    join king_admin ka on ka.id=su.user_id 
                                    where stream_id=?
                                    group by su.user_id order by ka.name",$streamid)->result_array();
//            $output.="<option value='00'>All</option>";
    foreach($arr_userids as $assigneduser) {
        if($user['userid'] == $assigneduser['user_id']) {
            $output.="";
        }
        else {
            $output.="<option value='".$assigneduser['user_id']."'>".$assigneduser['name']."</option>";
            }
        }
        echo $output;
    }
	
    function jx_stream_post() 
    {
        $user=$this->erpm->auth();
        if($_POST) 
            $this->erpm->do_stream_post($user);
    }


	/**
	 * function to load franchise sales report
	 */
	function fr_hyg_anlytcs_report($trid='',$twnid='',$fid='',$menuid='')
	{
		$cond='';
		if($trid !=0)
			$cond.= ' and f.territory_id='.$trid;
		if($twnid !=0)
			$cond.= ' and f.town_id='.$twnid;
		if($fid!=0)
			$cond.= ' and f.franchise_id='.$fid;
		if($menuid)
			$cond.= ' and menuid in '.'('.$menuid.')';
		$fran_bio_res=$this->db->query("SELECT f.franchise_name,f.franchise_id,f.town_id,f.territory_id,f.is_suspended,t.territory_name,tw.town_name,f.created_on,f.credit_limit FROM pnh_m_franchise_info f JOIN pnh_m_territory_info t ON t.id=f.territory_id JOIN pnh_towns tw ON tw.id=f.town_id WHERE 1 $cond GROUP BY f.franchise_id order by franchise_name asc ")->result_array();
		$fran_bio_res=$this->db->query("SELECT f.franchise_name,f.franchise_id,f.town_id,f.territory_id,f.is_suspended,t.territory_name,tw.town_name,f.created_on,f.credit_limit,GROUP_CONCAT(m.menuid) as menuid
										FROM pnh_m_franchise_info f
										JOIN pnh_m_territory_info t ON t.id=f.territory_id
										JOIN pnh_towns tw ON tw.id=f.town_id
										LEFT JOIN `pnh_franchise_menu_link` m ON m.fid=f.franchise_id AND m.status=1
				 						WHERE 1 $cond
										GROUP BY f.franchise_id
										ORDER BY franchise_name ASC ")->result_array();
		$fortwkdaterange=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 4 WEEK),'%d %b') AS endate,DATE_FORMAT(DATE_ADD(DATE(DATE_SUB(CURDATE(), INTERVAL 4 WEEK)), INTERVAL -6 DAY),'%d %b') AS startdate")->row_array();
		$thirdwkdaterange=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 3 WEEK),'%d %b') AS endate,DATE_FORMAT(DATE_ADD(DATE(DATE_SUB(CURDATE(), INTERVAL 3 WEEK)), INTERVAL -6 DAY),'%d %b') AS startdate")->row_array();
		$secwkdaterange=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 2 WEEK),'%d %b') AS endate,DATE_FORMAT(DATE_ADD(DATE(DATE_SUB(CURDATE(), INTERVAL 2 WEEK)), INTERVAL -6 DAY),'%d %b') AS startdate")->row_array();
		$frstwkdaterange=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 WEEK),'%d %b') AS endate,DATE_FORMAT(DATE_ADD(DATE(DATE_SUB(CURDATE(), INTERVAL 1 WEEK)), INTERVAL -6 DAY),'%d %b') AS startdate")->row_array();
		$last_monthdesc=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH),'%b %Y') AS lastmnth")->row()->lastmnth;
		$last_secmonth=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 2 MONTH),'%b %Y') AS lastsecmnth")->row()->lastsecmnth;
		$last_thrdcmonth=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 3 MONTH),'%b %Y') AS lastthrdmnth")->row()->lastthrdmnth;
		$last_forthcmonth=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 4 MONTH),'%b %Y') AS lastforthdmnth")->row()->lastforthdmnth;
		
		$data['last_monthdesc']=$last_monthdesc;
		$data['last_secmonth']=$last_secmonth;
		$data['last_thrdcmonth']=$last_thrdcmonth;
		$data['last_forthcmonth']=$last_forthcmonth;

		$data['fortwkdaterange']=$fortwkdaterange;
		$data['thirdwkdaterange']=$thirdwkdaterange;
		$data['secwkdaterange']=$secwkdaterange;
		$data['frstwkdaterange']=$frstwkdaterange;
		$data['fran_bio_res']=$fran_bio_res;
		$data['page']='fr_hygine_anlytics_report';
		$data['fid']=$fid;
		$data['twnid']=$twnid;
		$data['menuid']=$menuid;
		$data['trid']=$trid;
		$this->load->view("admin",$data);
	}
	
	function pnh_export_franchise_analytics_report()
	{
		$fran_bio_res=$this->db->query("SELECT f.franchise_name,f.franchise_id,f.town_id,f.territory_id,f.is_suspended,t.territory_name,tw.town_name,f.created_on,f.credit_limit FROM pnh_m_franchise_info f JOIN pnh_m_territory_info t ON t.id=f.territory_id JOIN pnh_towns tw ON tw.id=f.town_id GROUP BY f.franchise_id order by franchise_name asc");
		$fortwkdaterange=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 4 WEEK),'%d %b') AS endate,DATE_FORMAT(DATE_ADD(DATE(DATE_SUB(CURDATE(), INTERVAL 4 WEEK)), INTERVAL -6 DAY),'%d %b') AS startdate")->row_array();
		$thirdwkdaterange=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 3 WEEK),'%d %b') AS endate,DATE_FORMAT(DATE_ADD(DATE(DATE_SUB(CURDATE(), INTERVAL 3 WEEK)), INTERVAL -6 DAY),'%d %b') AS startdate")->row_array();
		$secwkdaterange=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 2 WEEK),'%d %b') AS endate,DATE_FORMAT(DATE_ADD(DATE(DATE_SUB(CURDATE(), INTERVAL 2 WEEK)), INTERVAL -6 DAY),'%d %b') AS startdate")->row_array();
		$frstwkdaterange=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 WEEK),'%d %b') AS endate,DATE_FORMAT(DATE_ADD(DATE(DATE_SUB(CURDATE(), INTERVAL 1 WEEK)), INTERVAL -6 DAY),'%d %b') AS startdate")->row_array();
		$last_monthdesc=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH),'%b %Y') AS lastmnth")->row()->lastmnth;
		$last_secmonth=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 2 MONTH),'%b %Y') AS lastsecmnth")->row()->lastsecmnth;
		$last_thrdcmonth=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 3 MONTH),'%b %Y') AS lastthrdmnth")->row()->lastthrdmnth;
		$last_forthcmonth=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 4 MONTH),'%b %Y') AS lastforthdmnth")->row()->lastforthdmnth;

		$thrd_wkdt=$thirdwkdaterange['startdate'].'-'.$thirdwkdaterange['endate'];
		
		$fran_status_arr=array();
		$fran_status_arr[0]="Live";
		$fran_status_arr[1]="Permanent Suspension";
		$fran_status_arr[2]="Payment Suspension";
		$fran_status_arr[3]="Temporary Suspension";
		
		$fr_sales_list = array();
		$fr_sales_heading="Franchise Hygenie Analyitics report";
		
		$fr_sales_list[] = '"Territory","Town","Franchise Name","Created on","Last Ordered on","Current Week Sales","Week 4","Week 3","Week 2","Week 1","Month 4(Sep)","Month 3(Aug)","Month 2(July)","Month 1(June)","Sales Till Date","Current Month Top Selling Category","Top most Selling Category last month(Sep)","2nd Most Selling Category last month(Sep)","Total Members","Current Pending Amount","Uncleared Cheque	","Credit Limit","Last Shipment Value","Last week No of Transactions","Last week No of Orders","Last week Total Order Qty","Last Month No of Transactions","Last Month No of Orders","Last Month Total Order Qty","Suspension Status"';
	
	
		 if($fran_bio_res->num_rows())
			{
				foreach($fran_bio_res->result_array() as $row_f)
				{
					$fr_sales_det = array();
					
					$fr_sales_det[] = ucwords($row_f['territory_name']);
					$fr_sales_det[] = ucwords($row_f['town_name']);
					$fr_sales_det[] = ucwords($row_f['franchise_name']);
					$fr_sales_det[] = format_date_ts($row_f['created_on']);
					$last_ordate=$this->db->query("SELECT t.init FROM king_orders o  JOIN king_transactions t ON t.transid=o.transid  where franchise_id=? ORDER BY t.init DESC LIMIT 1",$row_f['franchise_id'])->row_array() ;
						$fr_sales_det[] = format_date_ts($last_ordate['init']);
					$curwk_sales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE   WEEK(DATE(FROM_UNIXTIME(a.init)))=WEEK(CURDATE()) AND a.franchise_id=?",$row_f['franchise_id'])->row_array();
						$fr_sales_det[] = $curwk_sales['ttl_sales'];
					$forth_wksales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE   WEEK(DATE(FROM_UNIXTIME(a.init)))=WEEK(DATE_SUB(CURDATE(), INTERVAL 4 WEEK)) AND a. franchise_id=?",$row_f['franchise_id'])->row_array();
						$fr_sales_det[] = $forth_wksales['ttl_sales'];
					$thrd_wksales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE WEEK(DATE(FROM_UNIXTIME(a.init)))=WEEK(DATE_SUB(CURDATE(), INTERVAL 3 WEEK)) AND a.franchise_id=?",$row_f['franchise_id'])->row_array();
						$fr_sales_det[] = $thrd_wksales['ttl_sales'];
					$secnd_wksales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE WEEK(DATE(FROM_UNIXTIME(a.init)))=WEEK(DATE_SUB(CURDATE(), INTERVAL 2 WEEK)) AND a.franchise_id=?",$row_f['franchise_id'])->row_array();
						$fr_sales_det[] = $secnd_wksales['ttl_sales'];
					$one_wksales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE   WEEK(DATE(FROM_UNIXTIME(a.init)))=WEEK(DATE_SUB(CURDATE(), INTERVAL 1 WEEK)) AND a.franchise_id=?",$row_f['franchise_id'])->row_array();
						$fr_sales_det[] = $one_wksales['ttl_sales'];
					$last_monthsales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE   MONTH(DATE(FROM_UNIXTIME(a.init)))=MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND a.franchise_id=?",$row_f['franchise_id'])->row_array();
						$fr_sales_det[] = $last_monthsales['ttl_sales'];
					$last_secmonthsales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE   MONTH(DATE(FROM_UNIXTIME(a.init)))=MONTH(DATE_SUB(CURDATE(), INTERVAL 2 MONTH)) AND a.franchise_id=?",$row_f['franchise_id'])->row_array();
						$fr_sales_det[] = $last_secmonthsales['ttl_sales'];
					$last_thrdmonthsales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE   MONTH(DATE(FROM_UNIXTIME(a.init)))=MONTH(DATE_SUB(CURDATE(), INTERVAL 3 MONTH)) AND a.franchise_id=?",$row_f['franchise_id'])->row_array();
						$fr_sales_det[] = $last_thrdmonthsales['ttl_sales'];
					$last_forthdmonthsales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE   MONTH(DATE(FROM_UNIXTIME(a.init)))=MONTH(DATE_SUB(CURDATE(), INTERVAL 4 MONTH)) AND a.franchise_id=?",$row_f['franchise_id'])->row_array();
						$fr_sales_det[] = $last_forthdmonthsales['ttl_sales'];
					$ttlsales_tildate=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE a.franchise_id=?",$row_f['franchise_id'])->row_array();
						$fr_sales_det[] = formatInIndianStyle($ttlsales_tildate['ttl_sales']);
					$curmonth_topcat=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*o.quantity),2) AS ttl_sales,d.menuid,m.name AS menu,SUM(o.quantity) AS sold FROM king_deals d JOIN king_dealitems i ON i.dealid=d.dealid JOIN king_categories c ON c.id=d.catid JOIN king_orders o ON o.itemid=i.id  JOIN king_transactions t ON t.transid=o.transid AND t.is_pnh=1 JOIN pnh_menu m ON m.id=d.menuid WHERE i.is_pnh=1 AND MONTH(DATE(FROM_UNIXTIME(t.init)))=MONTH(CURDATE()) AND t.franchise_id=?  ORDER BY sold DESC LIMIT 1",$row_f['franchise_id'])->row_array();
					$curmnth_topcatsls=	$curmonth_topcat['menu'] .'Rs'.formatInIndianStyle($curmonth_topcat['ttl_sales']);
					$fr_sales_det[] = $curmnth_topcatsls;
					$lastmonth_topcat=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*o.quantity),2) AS ttl_sales,d.menuid,m.name AS menu,SUM(o.quantity) AS sold
																FROM king_deals d JOIN king_dealitems i ON i.dealid=d.dealid  
																JOIN king_categories c ON c.id=d.catid  
																JOIN king_orders o ON o.itemid=i.id 
																JOIN king_transactions t ON t.transid=o.transid AND t.is_pnh=1 
																JOIN pnh_menu m ON m.id=d.menuid 
																WHERE i.is_pnh=1 AND MONTH(DATE(FROM_UNIXTIME(t.init)))=MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND t.franchise_id=? 
																ORDER BY sold DESC
																LIMIT 2",$row_f['franchise_id'])->result_array();
						$lastmnth_topcatsales=$lastmonth_topcat[0]['menu'].'Rs'.formatInIndianStyle($lastmonth_topcat[0]['ttl_sales']);
						$lastmnth_sectopcatsales=$lastmonth_topcat[1]['menu'].'Rs'.formatInIndianStyle($lastmonth_topcat[1]['ttl_sales']);
						$fr_sales_det[] = $lastmnth_topcatsales;
						$fr_sales_det[] = $lastmnth_sectopcatsales;
					
					$ttl_mem_reg=$this->db->query("SELECT count(*) as ttl_regmem FROM pnh_member_info  WHERE franchise_id=? ",$row_f['franchise_id'])->row_array();
					$fr_sales_det[] = $ttl_mem_reg['ttl_regmem'];
					$acc_statement = $this->erpm->get_franchise_account_stat_byid($row_f['franchise_id']);
						$net_payable_amt = $acc_statement['net_payable_amt'];
						$credit_note_amt = $acc_statement['credit_note_amt'];
						$shipped_tilldate = $acc_statement['shipped_tilldate'];
						$paid_tilldate = $acc_statement['paid_tilldate'];
						$uncleared_payment = $acc_statement['uncleared_payment'];		
						$cancelled_tilldate = $acc_statement['cancelled_tilldate'];
						$ordered_tilldate = $acc_statement['ordered_tilldate'];
						$acc_adjustments_val = $acc_statement['acc_adjustments_val'];
						$pending_payment = formatInIndianStyle($shipped_tilldate-($paid_tilldate+$acc_adjustments_val+$credit_note_amt),2);
										
					$fr_sales_det[] = $pending_payment;
					$fr_sales_det[] = formatInIndianStyle($uncleared_payment);
					$fr_sales_det[] = formatInIndianStyle($row_f['credit_limit']);
					$last_shipped_amt=$this->db->query("SELECT DISTINCT d.franchise_id,c.amount,o.transid
														FROM shipment_batch_process_invoice_link sd
														JOIN proforma_invoices b ON sd.p_invoice_no = b.p_invoice_no
														JOIN king_transactions c ON c.transid = b.transid
														JOIN king_orders o ON o.id = b.order_id  
														JOIN pnh_member_info pu ON pu.user_id=o.userid 
														JOIN pnh_m_franchise_info d ON d.franchise_id = c.franchise_id
														JOIN pnh_m_territory_info f ON f.id = d.territory_id
														JOIN pnh_towns e ON e.id = d.town_id 
														JOIN king_dealitems dl ON dl.id=o.itemid
														JOIN king_deals deal ON deal.dealid=dl.dealid
														JOIN king_brands br ON br.id = deal.brandid 
														JOIN pnh_menu m ON m.id = deal.menuid 
														WHERE o.status = 2 AND sd.shipped = 1 AND c.is_pnh = 1 AND d.franchise_id=? 
														GROUP BY b.transid 
														ORDER BY sd.shipped_on DESC LIMIT 1 ",$row_f['franchise_id'])->row_array();
					$fr_sales_det[] = formatInIndianStyle($last_shipped_amt['amount']);					
					$lst_wk_no_of_trans=$this->db->query("SELECT COUNT(*) AS ttl,WEEK(DATE(FROM_UNIXTIME(init))) FROM king_transactions WHERE WEEK(DATE(FROM_UNIXTIME(init)))=WEEK(DATE_SUB(CURDATE(),INTERVAL 1 WEEK)) AND franchise_id=?",$row_f['franchise_id'])->row_array();
					$fr_sales_det[] =$lst_wk_no_of_trans['ttl'];
					$last_wk_ttl_orders=$this->db->query("SELECT COUNT(*) AS ttl,WEEK(DATE(FROM_UNIXTIME(time))) FROM king_orders o join king_transactions t on t.transid=o.transid WHERE WEEK(DATE(FROM_UNIXTIME(time)))=WEEK(DATE_SUB(CURDATE(),INTERVAL 1 WEEK)) AND franchise_id=?",$row_f['franchise_id'])->row_array();
					$fr_sales_det[] =$last_wk_ttl_orders['ttl'];
					
					$lst_wk_ttl_qty=$this->db->query("SELECT SUM(o.quantity) AS ttl,WEEK(DATE(FROM_UNIXTIME(time))) FROM king_orders o join king_transactions t on t.transid=o.transid WHERE WEEK(DATE(FROM_UNIXTIME(time)))=WEEK(DATE_SUB(CURDATE(),INTERVAL 1 WEEK)) AND franchise_id=?",$row_f['franchise_id'])->row_array();
					$fr_sales_det[] =$lst_wk_ttl_qty['ttl'];
					
					$lst_mnth_no_of_trans=$this->db->query("SELECT COUNT(*) AS ttl,MONTH(DATE(FROM_UNIXTIME(init))) FROM king_transactions WHERE MONTH(DATE(FROM_UNIXTIME(init)))=MONTH(DATE_SUB(CURDATE(),INTERVAL 1 MONTH)) AND franchise_id=?",$row_f['franchise_id'])->row_array();
					$fr_sales_det[] =$lst_mnth_no_of_trans['ttl'];
					
					$last_mnth_ttl_orders=$this->db->query("SELECT COUNT(*) AS ttl,MONTH(DATE(FROM_UNIXTIME(time))) FROM king_orders o join king_transactions t on t.transid=o.transid WHERE MONTH(DATE(FROM_UNIXTIME(time)))=MONTH(DATE_SUB(CURDATE(),INTERVAL 1 MONTH)) AND franchise_id=?",$row_f['franchise_id'])->row_array();
					$fr_sales_det[] =$last_mnth_ttl_orders['ttl'];
					
					$lst_month_ttl_qty=$this->db->query("SELECT SUM(o.quantity) AS ttl,MONTH(DATE(FROM_UNIXTIME(time))) FROM king_orders o join king_transactions t on t.transid=o.transid WHERE MONTH(DATE(FROM_UNIXTIME(time)))=MONTH(DATE_SUB(CURDATE(),INTERVAL 1 MONTH)) AND franchise_id=?",$row_f['franchise_id'])->row_array();
					$fr_sales_det[] =$lst_month_ttl_qty['ttl'];
					$fr_sales_det[] =$fran_status_arr[$row_f['is_suspended']];
					$fr_sales_list[]='"'.implode('","',$fr_sales_det).'"';
				}
			
			}
			header('Content-Type: application/csv');
			header('Content-Disposition: attachment; filename=Franchise_Hygenie_Analyitics_report'.date('d_m_Y_H_i').'.csv');
			header('Pragma: no-cache');
			echo implode("\r\n",$fr_sales_list);
		exit;
	}

	
	function clean_stock()
	{
		// remove invalid mrp entries for null and 0 and also check for any reservation on the stock entry. 
		
		$sql_mrp = "select a.stock_info_id 
						from t_reserved_batch_stock a
						join t_stock_info b on a.stock_info_id = b.stock_id
						where (mrp = 0 or mrp is null) 
					";
		$res_mrp = $this->db->query($sql_mrp);
		if($res_mrp->num_rows())
		{
			echo 'Clean Stock Process Stopped - invalid MRP [0,NULL] has in resererved entry';
			exit;
		}else
		{
			$sql_loc = "select a.stock_info_id 
						from t_reserved_batch_stock a
						join t_stock_info b on a.stock_info_id = b.stock_id
						where (location_id = 0 or location_id is null) 
					";
			$res_loc = $this->db->query($sql_loc);
			
			if($res_loc->num_rows())
			{
				
			}
			
			
			
			
			
			$sql_dup1 = "select a.product_id,a.stock_id as src_stkid,b.stock_id as dest_stkid,
									a.mrp,b.mrp,a.product_barcode,b.product_barcode,a.location_id,b.location_id,
									a.rack_bin_id,b.rack_bin_id,a.available_qty,b.available_qty
								from t_stock_info a
								join t_stock_info b on a.product_id = b.product_id 
								and a.stock_id != b.stock_id 
								and a.mrp = b.mrp and a.product_barcode = b.product_barcode
								and a.location_id = b.location_id
								and a.rack_bin_id = b.rack_bin_id and b.stock_id > a.stock_id 
							where 1 
							order by product_id,a.stock_id 
						";
						
			$res_dup1 = $this->db->query($sql_dup1);
			
			if($res_dup1->num_rows())
			{
				foreach($res_dup1->result_array() as $row)
				{
					//
					$src_stkid=$row['src_stkid'];
					$dest_stkid=$row['dest_stkid'];
					
					$valid_src_stkid = $this->db->query("select count(*) as t from t_stock_info where stock_id = ? ",$src_stkid)->row()->t;
					if(!$valid_src_stkid)
						continue;
						
						
						
					
					// Check if dest stock id has proforma entries 
					$dest_resv_res = $this->db->query("select * from t_reserved_batch_stock where stock_info_id = ? ",$dest_stkid);
					if($dest_resv_res->num_rows())
					{
						// update dest with src stock ref id 
						$this->db->query("update t_reserved_batch_stock set stock_info_id = ?,tmp_prev_stk_id = ?  where stock_info_id = ? ",array($src_stkid,$dest_stkid,$dest_stkid));
					}
					
					
					$this->db->query("update t_imei_no set stock_id = ?  where stock_id = ? ",array($src_stkid,$dest_stkid));
					
					$this->db->query("update t_grn_product_link set ref_stock_id = ?  where ref_stock_id = ? ",array($src_stkid,$dest_stkid));
					
					// before deleting dest stk id, consider existing stock qty to src stk id from dest stk id
					$new_stkqty = $this->db->query("select sum(available_qty) as n from t_stock_info where stock_id in (?,?) ",array($src_stkid,$dest_stkid))->row()->n;
					if($new_stkqty < 0)
						$new_stkqty = 0;
					
					$this->db->query("update t_stock_info set available_qty = ? where stock_id = ? ",array($new_stkqty,$src_stkid));
					
					$this->db->query("delete from t_stock_info where stock_id = ? ",array($dest_stkid));
					
					
					$stk_det = $this->db->query('select * from t_stock_info where stock_id = ? ',$src_stkid)->row_array();
					$this->erpm->_upd_product_stock($stk_det['product_id'],$stk_det['mrp'],$stk_det['product_barcode'],$stk_det['location_id'],$stk_det['rack_bin_id'],0,1,0,1,0,-1,"Automatic Correction - IN ");
					$this->erpm->_upd_product_stock($stk_det['product_id'],$stk_det['mrp'],$stk_det['product_barcode'],$stk_det['location_id'],$stk_det['rack_bin_id'],0,1,0,0,0,-1,"Automatic Correction - OUT ");
					
				}
			}	
			
			
		}
	}

	function fix_imei_scheme()
	{
		error_reporting(E_ALL);
		$scheme_list = array();		
		$scheme_list[] = array('from'=>'2013-08-01 00:00:00','to'=>'2013-08-31 23:59:59','margin'=>1.5,'type'=>'perc','brands'=>'82298176,74323882','cat'=>9);
		
		$scheme_list[] = array('from'=>'2013-09-01 00:00:00','to'=>'2013-09-30 23:59:59','margin'=>1,'type'=>'perc','brands'=>'74323882,82298176,31648385,98785343','cat'=>'9');
		$scheme_list[] = array('from'=>'2013-09-01 00:00:00','to'=>'2013-09-30 23:59:59','margin'=>0.5,'type'=>'perc','brands'=>'38335857,52596596','cat'=>'9');
		$scheme_list[] = array('from'=>'2013-09-01 00:00:00','to'=>'2013-09-30 23:59:59','margin'=>50,'type'=>'rup','brands'=>'74323882','cat'=>'1028,1030');
		$scheme_list[] = array('from'=>'2013-09-01 00:00:00','to'=>'2013-09-30 23:59:59','margin'=>30,'type'=>'rup','brands'=>'76916829','cat'=>'9');
		
		$scheme_list[] = array('from'=>'2013-10-01 00:00:00','to'=>'2013-10-31 23:59:59','margin'=>0.5,'type'=>'perc','brands'=>'82298176,74323882,38335857,52596596,98785343,31648385','cat'=>'9');
		$scheme_list[] = array('from'=>'2013-10-01 00:00:00','to'=>'2013-10-31 23:59:59','margin'=>50,'type'=>'rup','brands'=>'74323882','cat'=>'1028,1030');
		$scheme_list[] = array('from'=>'2013-10-01 00:00:00','to'=>'2013-10-31 23:59:59','margin'=>30,'type'=>'rup','brands'=>'76916829','cat'=>'1028,1030');
		
		foreach($scheme_list as $sch)
		{
			$sql = "select id,product_name,landing 
						from (
						select b.id,date(from_unixtime(c.init)),imei_no,product_name,b.status,(b.i_orgprice-(i_discount+i_coup_discount)) as landing 
							from t_imei_no a
							join king_orders b on a.order_id = b.id
							join king_transactions c on c.transid = b.transid 
							join m_product_info d on a.product_id = d.product_id 
							join king_dealitems e on e.id = b.itemid
							join king_deals f on f.dealid = e.dealid 
							where 1 and f.brandid in (".$sch['brands'].") and f.catid in (".$sch['cat'].") and c.init between unix_timestamp(?) and unix_timestamp(?)
							and imei_scheme_id = 0 
							order by c.init  
						) as g 
					";
			$res = $this->db->query($sql,array($sch['from'],$sch['to']));
			//echo $this->db->last_query();
			if($res->num_rows())
			{
				foreach($res->result_array() as $row)
				{
					$amt = $sch['margin'];
					if($sch['type'] == 'perc')
						$amt = $row['landing']*$sch['margin']/100;
					
					$this->db->query("update king_orders set imei_reimbursement_value_perunit = ? ,imei_scheme_id = 99919 where imei_scheme_id = 0 and id = ? limit 1 ",array($amt,$row['id']));
					//echo $this->db->last_query();					
				}
			}
		}
		
	}


	function fix_entries()
	{
		$sql = "select a.product_id,product_name,b.mrp,count(*) as t,group_concat(distinct c.stock_id) as stk_entry 
					from t_imei_no a 
					join m_product_info b on a.product_id = b.product_id 
					join t_stock_info c on c.product_id = b.product_id  
					where status = 0  
					group by product_id
					order by product_name ";
		$res = $this->db->query($sql);
		if($res->num_rows())
		{
			foreach($res->result_array() as $row)
			{
				$stk_ref_ids = explode(',',$row['stk_entry']);
				
				
				$imei_qty = @$this->db->query("select count(*) as t 
					from t_imei_no a 
					join m_product_info b on a.product_id = b.product_id 
					where status = 0 and a.product_id = ? ",$row['product_id'])->row()->t;
				
				$p_stk_res = $this->db->query('select * from t_stock_info where product_id = ? ',$row['product_id']);
				
				// Clear All Stock Entries 
				foreach($p_stk_res->result_array() as $p_stk)
				{
					$stk_id = $p_stk['stock_id'];
					
					// remove current available stock entry.
					$stk_det = @$this->db->query('select * from t_stock_info where stock_id = ? ',$stk_id)->row_array();
					if($stk_det)
						$this->erpm->_upd_product_stock($stk_det['product_id'],$stk_det['mrp'],$stk_det['product_barcode'],$stk_det['location_id'],$stk_det['rack_bin_id'],$stk_det['stock_id'],$stk_det['available_qty'],0,0,0,-1,'Correction - OUT');
					
				}
				
				$upd_stk_id = @$this->db->query('select stock_id from t_stock_info where product_id = ? and mrp= ? ',array($row['product_id'],$row['mrp']))->row()->stock_id;
				if($upd_stk_id)
					$stk_det = @$this->db->query('select * from t_stock_info where stock_id = ? ',$upd_stk_id)->row_array();
				else
					$stk_det = $this->db->query('select * from t_stock_info where product_id = ? order by stock_id asc limit 1 ',array($row['product_id'],$row['mrp']))->row_array();
				
					
				$this->erpm->_upd_product_stock($stk_det['product_id'],$row['mrp'],$stk_det['product_barcode'],$stk_det['location_id'],$stk_det['rack_bin_id'],$upd_stk_id*1,$imei_qty,0,1,0,-1,'Correction - IN');
				 
				$this->db->query("update t_imei_no set stock_id = ? where product_id = ? and status = 0 ",array($upd_stk_id,$stk_det['product_id']));
					
				echo anchor_popup("admin/product/".$stk_det['product_id'],"Product ID - ".$stk_det['product_id']).'<br>';
				
			}
		}				
	}


	function gen_monthlyclosingstock($from='',$to='')
	{
		$this->erpm->auth(true);
		
		$sql = "select product_id,product_name,mrp,brand,group_concat(product_id,':',yr,'-',mn,':',ttl) as purchase from (
					select product_id,product_name,mrp,brandid,brand,created_on,year(created_on) as yr,month(created_on) as mn,substring_index(group_concat(current_stock order by created_on desc),',',1) as ttl from (
					select  b.product_id,product_name,b.mrp,c.id as brandid,c.name  as brand,a.created_on,concat(year(a.created_on),month(a.created_on)) as ym,current_stock  
						from t_stock_update_log a
						join m_product_info b on a.product_id = b.product_id 
						join king_brands c on c.id = b.brand_id 
						where 1 
					group by a.id  
					) as g
					group by product_id,g.ym 
					) as f 
					group by product_id
					order by brand,product_name,created_on
					 
				";
		$res = $this->db->query($sql);
		if($res->num_rows())
		{
			$head = array();
			$head[] = 'Slno';
			$head[] = 'Brand';
			$head[] = 'Product ID';
			$head[] = 'Product Name';
			$head[] = 'MRP';
			
			$dts = array();
			for($i=0;$i<=12;$i++)
				$dts[] = date('Y-m-01',strtotime('-'.$i.' Month'));
				
			$dts = array_reverse($dts);	
				
			foreach($dts as $dt)	
				$head[] = date('M-Y',strtotime($dt)); 
			
			$cont = array();
			foreach($res->result_array() as $row)
			{
				$tmp = array();
				$tmp[] = count($cont)+1;
				$tmp[] = $row['brand'];
				$tmp[] = $row['product_id'];
				$tmp[] = $row['product_name'];
				$tmp[] = $row['mrp'];
				
				$pstk_d_arr = explode(',', $row['purchase']);
				
				$pstk_dts = array();
				foreach($pstk_d_arr as $pstk)
				{
					$arr = explode(':',$pstk);
					
					list($y,$m) = explode('-',$arr[1]);
					
					$mfix = ($m<10?'0'.$m:$m);
					
					$pstk_dts["$y-$mfix-01"] = $arr[2];
				}
				$last_stk = 0;
				foreach($dts as $dt)
				{
					$tmp[$dt] = isset($pstk_dts[$dt])?$pstk_dts[$dt]:$last_stk; 
				}
					
				$cont[] = $tmp;
			}
			
			
			ob_start();
			$f=fopen("php://output","w");
			fputcsv($f, $head);
		
			$csv = array();  
			$csv[] = $head;
			
			foreach($cont as $c)
				fputcsv($f, $c);
			
			fclose($f);
			$csv=ob_get_clean();
			@ob_clean();
		    header('Content-Description: File Transfer');
		    header('Content-Type: text/csv');
		    header('Content-Disposition: attachment; filename=product_monthly_closing_stock.csv');
		    header('Content-Transfer-Encoding: binary');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    header('Content-Length: ' . strlen($csv));
		    @ob_clean();
		    flush();
		    echo $csv;
		    exit;
		}	
				
	}

	function print_franlabels()
	{
		$this->erpm->auth();
		$res = $this->db->query("select * from pnh_m_franchise_info where is_suspended != 1 order by franchise_name ");
		if($res->num_rows())
		{
			$data['fr_details'] = $res->result_array();
			$this->load->view('admin/body/pnh_print_fran_deliverylabel',$data);	
		}
	}
	
	 
    
}
