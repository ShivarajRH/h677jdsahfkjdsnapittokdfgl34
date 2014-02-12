<?php
include APPPATH.'/controllers/analytics.php';
class Stream extends Analytics 
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

            $ou_cond='';
			if(!$this->erpm->auth(true,true)) 
				$ou_cond=' and su.user_id='.$user['userid'];

                $data['streams']=$this->db->query("select s.*,su.* from m_streams s 
												join m_stream_users su on su.stream_id = s.id
                                                where status=1 ".$ou_cond." group by s.id order by s.title asc")->result_array();

                $data['users']=$this->db->query("select * from king_admin order by name asc")->result_array();
                $data['pg']=0;
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
	
	function jx_get_streampostdetails($streamid,$pg=0,$limit=5) 
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
                
                if( $this->input->post('date_to') != '') {
                    $dt_end= strtotime($this->input->post('date_to'));
                }
                else {
                    $dt_end= strtotime(date('Y-m-d 24:59:59',  time() ) );
                }
                $cond.="and (sp.posted_on between $dt_st and $dt_end )";
                
                if($this->input->post('search_text') != '') {
                        $search_text = $this->input->post('search_text');
                        $cond.=' and (sp.description like "%'.$search_text.'%" or spr.description like "%'.$search_text.'%")';
                }
               
                $output['date_output']="Posts from ".date("M/d/Y",$dt_st)." to ".date("M/d/Y",$dt_end);
         

				$sql="select sp.*,ka.id as userid,ka.username,ka.name,ka.email from m_stream_posts sp
	                                    join king_admin ka on ka.id=sp.posted_by
                                            left join m_stream_post_reply spr on spr.post_id=sp.id
	                                    where sp.stream_id=? and sp.status=1 $cond
	                                    group by sp.id order by sp.posted_on desc";
            
	    $total_items= $output['total_items']=$this->db->query($sql,array($streamid))->num_rows();
            
            
            $sql .=" limit $pg,$limit ";
            
            $arr_streams_rslt=$this->db->query($sql,array($streamid));
            $arr_streams=$arr_streams_rslt->result_array();
            
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
//                  PAGINATION
                    $date_from=date("Y-m-d",$st_ts);
                    $date_to=date("Y-m-d",$en_ts);
                    
                    $this->load->library('pagination');
                   
                    $config['base_url'] = site_url("admin/jx_get_streampostdetails/".$streamid); //site_url("admin/orders/$status/$s/$e/$orders_by/$limit");
                    $config['total_rows'] = $total_items;
                    $config['per_page'] = $limit;
                    $config['uri_segment'] = 4; 
                    $config['num_links'] = 5;
                    
                    $this->config->set_item('enable_query_strings',false); 
                    $this->pagination->initialize($config); 
                    $posts_pagination = $this->pagination->create_links();
                    $this->config->set_item('enable_query_strings',TRUE);
//                  PAGINATION ENDS
                    
                    $output['pagination'].='<div class="stream_posts_pagination">'.$posts_pagination."</div>";
                    if($output['items']=='') { $output['status']='<div class="no_more_posts" align="center"><strong>No more posts to display.</strong></div>'; }
	    } 
            else { $output['items'].=''; $output['status']='<div class="no_more_posts" align="center"><strong>No results found.</strong></div>'; } 
            
            echo json_encode($output);
        }
	 
	function jx_get_assignto_list($streamid) 
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

    /**
     * Store the stream post
     */
    function jx_stream_post() 
    {
        $user=$this->erpm->auth();
        if($_POST) 
            $this->erpm->do_stream_post($user);
    }

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
		$fortwkdaterange=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(DATE_ADD(CURDATE(),INTERVAL WEEKDAY(CURDATE())*-1 DAY), INTERVAL 4 WEEK),'%d %b') AS startdate,DATE_FORMAT(DATE_ADD(DATE(DATE_SUB(DATE_ADD(CURDATE(),INTERVAL WEEKDAY(CURDATE())*-1 DAY), INTERVAL 4 WEEK)), INTERVAL +6 DAY),'%d %b') AS endate")->row_array();
		$thirdwkdaterange=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(DATE_ADD(CURDATE(),INTERVAL WEEKDAY(CURDATE())*-1 DAY), INTERVAL 3 WEEK),'%d %b') AS startdate,DATE_FORMAT(DATE_ADD(DATE(DATE_SUB(DATE_ADD(CURDATE(),INTERVAL WEEKDAY(CURDATE())*-1 DAY), INTERVAL 3 WEEK)), INTERVAL +6 DAY),'%d %b') AS endate")->row_array();
		$secwkdaterange=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(DATE_ADD(CURDATE(),INTERVAL WEEKDAY(CURDATE())*-1 DAY), INTERVAL 2 WEEK),'%d %b') AS startdate,DATE_FORMAT(DATE_ADD(DATE(DATE_SUB(DATE_ADD(CURDATE(),INTERVAL WEEKDAY(CURDATE())*-1 DAY), INTERVAL 2 WEEK)), INTERVAL +6 DAY),'%d %b') AS endate")->row_array();
		$frstwkdaterange=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(DATE_ADD(CURDATE(),INTERVAL WEEKDAY(CURDATE())*-1 DAY), INTERVAL 1 WEEK),'%d %b') AS startdate,DATE_FORMAT(DATE_ADD(DATE(DATE_SUB(DATE_ADD(CURDATE(),INTERVAL WEEKDAY(CURDATE())*-1 DAY), INTERVAL 1 WEEK)), INTERVAL +6 DAY),'%d %b') AS endate")->row_array();
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
		
		//$fortwkdaterange_arr=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 4 WEEK),'%d %b') AS endate,DATE_FORMAT(DATE_ADD(DATE(DATE_SUB(CURDATE(), INTERVAL 4 WEEK)), INTERVAL -6 DAY),'%d %b') AS startdate")->row_array();
		$fortwkdaterange_arr=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(DATE_ADD(CURDATE(),INTERVAL WEEKDAY(CURDATE())*-1 DAY), INTERVAL 4 WEEK),'%d %b') AS startdate,DATE_FORMAT(DATE_ADD(DATE(DATE_SUB(DATE_ADD(CURDATE(),INTERVAL WEEKDAY(CURDATE())*-1 DAY), INTERVAL 4 WEEK)), INTERVAL +6 DAY),'%d %b') AS endate")->row_array();
		$fortwkdaterange=$fortwkdaterange_arr['startdate'].' - '.$fortwkdaterange_arr['endate'];
		
		$thirdwkdaterange_arr=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(DATE_ADD(CURDATE(),INTERVAL WEEKDAY(CURDATE())*-1 DAY), INTERVAL 3 WEEK),'%d %b') AS startdate,DATE_FORMAT(DATE_ADD(DATE(DATE_SUB(DATE_ADD(CURDATE(),INTERVAL WEEKDAY(CURDATE())*-1 DAY), INTERVAL 3 WEEK)), INTERVAL +6 DAY),'%d %b') AS endate")->row_array();
		$thirdwkdaterange=$thirdwkdaterange_arr['startdate'].' - '.$thirdwkdaterange_arr['endate'];
		
		$secwkdaterange_arr=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(DATE_ADD(CURDATE(),INTERVAL WEEKDAY(CURDATE())*-1 DAY), INTERVAL 2 WEEK),'%d %b') AS startdate,DATE_FORMAT(DATE_ADD(DATE(DATE_SUB(DATE_ADD(CURDATE(),INTERVAL WEEKDAY(CURDATE())*-1 DAY), INTERVAL 2 WEEK)), INTERVAL +6 DAY),'%d %b') AS endate")->row_array();
		$secwkdaterange=$secwkdaterange_arr['startdate'].' - '.$secwkdaterange_arr['endate'];
		
		$frstwkdaterange_arr=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(DATE_ADD(CURDATE(),INTERVAL WEEKDAY(CURDATE())*-1 DAY), INTERVAL 1 WEEK),'%d %b') AS startdate,DATE_FORMAT(DATE_ADD(DATE(DATE_SUB(DATE_ADD(CURDATE(),INTERVAL WEEKDAY(CURDATE())*-1 DAY), INTERVAL 1 WEEK)), INTERVAL +6 DAY),'%d %b') AS endate")->row_array();
		$frstwkdaterange=$frstwkdaterange_arr['startdate'].' - '.$frstwkdaterange_arr['endate'];
		
		$last_monthdesc=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH),'%b %Y') AS lastmnth")->row()->lastmnth;
		$last_secmonth=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 2 MONTH),'%b %Y') AS lastsecmnth")->row()->lastsecmnth;
		$last_thrdcmonth=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 3 MONTH),'%b %Y') AS lastthrdmnth")->row()->lastthrdmnth;
		$last_forthcmonth=$this->db->query("SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 4 MONTH),'%b %Y') AS lastforthdmnth")->row()->lastforthdmnth;
		
		
		$fr_sales_list = array();
		$fr_sales_heading="Franchise Hygenie Analyitics report";
		
		$fr_sales_list[] = '"Territory","Town","Franchise Name","Created on","Last Ordered on","Current Week Sales","'.$fortwkdaterange.'","'.$thirdwkdaterange.'","'.$secwkdaterange.'","'.$frstwkdaterange.'","'.$last_monthdesc.'","'.$last_secmonth.'","'.$last_thrdcmonth.'","'.$last_forthcmonth.'","Sales Till Date","Current Month Top Selling Category","Top most Selling Category last month(Sep)","2nd Most Selling Category last month(Sep)","Total Members","Current Pending Amount","Uncleared Cheque	","Credit Limit","Last Shipment Value","Last week No of Transactions","Last week No of Orders","Last week Total Order Qty","Last Month No of Transactions","Last Month No of Orders","Last Month Total Order Qty","Suspension Status"';
	
	
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
					$forth_wksales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE   WEEK(DATE(FROM_UNIXTIME(a.init)))=WEEK(DATE_SUB(CURDATE(), INTERVAL 4 WEEK)) and year(DATE(FROM_UNIXTIME(a.init))) = year(DATE_SUB(CURDATE(), INTERVAL 4 WEEK)) AND a. franchise_id=?",$row_f['franchise_id'])->row_array();
						$fr_sales_det[] = $forth_wksales['ttl_sales'];
					$thrd_wksales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE WEEK(DATE(FROM_UNIXTIME(a.init)))=WEEK(DATE_SUB(CURDATE(), INTERVAL 3 WEEK)) and year(DATE(FROM_UNIXTIME(a.init))) = year(DATE_SUB(CURDATE(), INTERVAL 3 WEEK)) AND a.franchise_id=?",$row_f['franchise_id'])->row_array();
						$fr_sales_det[] = $thrd_wksales['ttl_sales'];
					$secnd_wksales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE WEEK(DATE(FROM_UNIXTIME(a.init)))=WEEK(DATE_SUB(CURDATE(), INTERVAL 2 WEEK)) and year(DATE(FROM_UNIXTIME(a.init))) = year(DATE_SUB(CURDATE(), INTERVAL 2 WEEK)) AND a.franchise_id=?",$row_f['franchise_id'])->row_array();
						$fr_sales_det[] = $secnd_wksales['ttl_sales'];
					$one_wksales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE   WEEK(DATE(FROM_UNIXTIME(a.init)))=WEEK(DATE_SUB(CURDATE(), INTERVAL 1 WEEK)) and year(DATE(FROM_UNIXTIME(a.init))) = year(DATE_SUB(CURDATE(), INTERVAL 1 WEEK)) AND a.franchise_id=?",$row_f['franchise_id'])->row_array();
						$fr_sales_det[] = $one_wksales['ttl_sales'];
					$last_monthsales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE   MONTH(DATE(FROM_UNIXTIME(a.init)))=MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) and year(DATE(FROM_UNIXTIME(a.init))) = year(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND a.franchise_id=?",$row_f['franchise_id'])->row_array();
						$fr_sales_det[] = $last_monthsales['ttl_sales'];
					$last_secmonthsales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE   MONTH(DATE(FROM_UNIXTIME(a.init)))=MONTH(DATE_SUB(CURDATE(), INTERVAL 2 MONTH)) and year(DATE(FROM_UNIXTIME(a.init))) = year(DATE_SUB(CURDATE(), INTERVAL 2 MONTH)) AND a.franchise_id=?",$row_f['franchise_id'])->row_array();
						$fr_sales_det[] = $last_secmonthsales['ttl_sales'];
					$last_thrdmonthsales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE   MONTH(DATE(FROM_UNIXTIME(a.init)))=MONTH(DATE_SUB(CURDATE(), INTERVAL 3 MONTH)) and year(DATE(FROM_UNIXTIME(a.init))) = year(DATE_SUB(CURDATE(), INTERVAL 3 MONTH))  AND a.franchise_id=?",$row_f['franchise_id'])->row_array();
						$fr_sales_det[] = $last_thrdmonthsales['ttl_sales'];
					$last_forthdmonthsales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE   MONTH(DATE(FROM_UNIXTIME(a.init)))=MONTH(DATE_SUB(CURDATE(), INTERVAL 4 MONTH)) and year(DATE(FROM_UNIXTIME(a.init))) = year(DATE_SUB(CURDATE(), INTERVAL 4 MONTH)) AND a.franchise_id=?",$row_f['franchise_id'])->row_array();
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

	function gen_monthlyclosingstock($from='',$to='')
	{
		$this->erpm->auth(true);
		
		$comp_t = time();
		
		$dts = array();
		$dts[] = $comp_t = date('Y-m-01',$comp_t);
		$comp_t = strtotime($comp_t);
		for($i=0;$i<12;$i++)
		{
			$comp_t = $comp_t-(24*60*60);
			//$dts[] = date('Y-m-d',strtotime(' -'.$i.' Month',time()));
			$dts[] = $comp_t = date('Y-m-01',$comp_t);
			$comp_t = strtotime($comp_t);
		}
		
			
		
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
					$tmp[$dt] = $last_stk = isset($pstk_dts[$dt])?$pstk_dts[$dt]:$last_stk;
					 
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
	
	
	/**
	 * function to process imeino Activation form data
	 */
	function __activate_pending_imei($franid=0)
	{
		if(!$franid)
			die("Franchise ID is required ");
			
		$user = $this->erpm->auth();
		$imeino_list = array();
		
		// get pending imei for activations and are in scheme 
		$sql_i = "select a.*,b.member_id
							from t_imei_no a
							join king_orders b on a.order_id = b.id 
							join king_transactions c on c.transid = b.transid 
							join king_invoice d on d.order_id = a.order_id and d.invoice_status = 1 
							join shipment_batch_process_invoice_link e on e.invoice_no = d.invoice_no 
							where franchise_id = $franid and is_imei_activated = 0 and b.status in (1,2) 
							and date((e.shipped_on)) <= '2013-11-11' and e.shipped = 1 
						group by a.id ";
		
		$pen_imei_actv_res = $this->db->query($sql_i);
		$ttl_imei_actv_credit = 0;
		if($pen_imei_actv_res->num_rows())
		{
			foreach($pen_imei_actv_res->result_array() as $pen_imei)
			{
				$member_id = $pen_imei['member_id'];
				$mobno = "";
				$imeino = $pen_imei['imei_no'];
				$actv_confrim = 0;
				$mem_name = $this->input->post('mem_name');
					
				array_push($imeino_list,$imeino);
					
				//check if memberID is already reached  max activations defined.
				
				$this->db->query("update t_imei_no set activated_mob_no=?,activated_member_id=?,activated_by=?,is_imei_activated = 1,imei_activated_on = now() where is_imei_activated = 0 and status = 1 and imei_no = ? limit 1  ",array($mobno,$member_id,$user['userid'],$imeino));
				if($this->db->affected_rows())
				{
					$oid = $this->db->query('select order_id from t_imei_no where status = 1 and order_id != 0 and imei_no = ? ',$imeino)->row()->order_id;
					$imei_ref_id = $this->db->query('select id from t_imei_no where status = 1 and order_id != 0 and imei_no = ? ',$imeino)->row()->id;
					$invno = $this->db->query('select invoice_no from king_invoice where invoice_status = 1 and order_id = ? ',$oid)->row()->invoice_no;
						
					$imei_credit = $this->db->query('select imei_reimbursement_value_perunit as amt from king_orders a join t_imei_no b on a.id = b.order_id where b.imei_no = ? ',$imeino)->row()->amt;
					$ttl_imei_actv_credit += $imei_credit;
						
						
					$member_userid = $this->db->query("select user_id from pnh_member_info where pnh_member_id = ? ",$member_id)->row()->user_id;
					$this->db->query("update king_orders set userid=?,member_scheme_processed=1 where id=? and member_scheme_processed=0",array($member_userid,$oid));
						
					// create creditnote document entry
					$arr = array($franid,$imei_ref_id,$invno,$imei_credit,date('Y-m-d H:i:s'),$user['userid']);
					$this->db->query("insert into t_invoice_credit_notes (franchise_id,type,ref_id,invoice_no,amount,created_on,created_by) values(?,2,?,?,?,?,?)",$arr);
					$credit_note_id = $this->db->insert_id();
						
					//update credit note to account summary
					$arr = array($franid,7,$credit_note_id,$invno,$imei_credit,'imeino : '.$imeino,1,date('Y-m-d H:i:s'),$user['userid']);
					$this->db->query("insert into pnh_franchise_account_summary (franchise_id,action_type,credit_note_id,invoice_no,credit_amt,remarks,status,created_on,created_by) values(?,?,?,?,?,?,?,?,?)",$arr);
						
					$this->db->query("update t_imei_no set ref_credit_note_id = ? where imei_no = ? and ref_credit_note_id = 0 ",array($credit_note_id,$imeino));
					//echo $this->db->last_query();
				}
			}
			
			if($ttl_imei_actv_credit > 0)
			{
				$fran_det = $this->db->query("select franchise_id as id,login_mobile1 as mob from pnh_m_franchise_info where franchise_id = ? ",$franid)->row_array();
				
				//Compose IMEI/Serialno Activation Message
				$sms_msg = 'Congratulations!!! Your IMEINO '.implode(',',$imeino_list).', Total ('.(count($imeino_list)).') Activated';
				if($ttl_imei_actv_credit)
				{
					$sms_msg .= ' and Amount of Rs '.($ttl_imei_actv_credit).' has been credited to your account';
					//create franchise credit note and update the same to pnh franchise account summary.
					$this->erpm->pnh_sendsms($fran_det['mob'],$sms_msg,$fran_det['id']);
				}
				
				echo $sms_msg;
			}
			
		}
	
		redirect('admin/pnh_franchise_activate_imei','refresh');
	}
	
	/**
	 * function to load advanced stock correction module  
	 */
	function adv_stock_corr()
	{
		$this->erpm->auth(STOCK_CORRECTION);
		
		$data['page'] = 'adv_stock_corr';
		$this->load->view('admin',$data);
	}
	
	/**
	 * function to get product list by search tag [keywork,productname,barcode,product id,brand]
	 */
	function jx_suggestprodsbytag()
	{
		$this->erpm->auth();
		
		$tag = $this->input->post('tag');
		$pid = $this->input->post('pid');
		
		$cond = '';
		if($pid && $tag == '')
			$cond = ' and a.product_id = '.$pid;
		
		if($tag == '')
			$tag = $pid;
		
		// check if the tag is brand 
		$brandid = @$this->db->query("select * from king_brands where name = ? ",$tag)->row()->id;
		
		if($brandid)
			$tag = $brandid;
				
		$output = array();
		$prod_list_res = $this->db->query("select corr_status,b.name as brand_name,a.product_id,product_name,a.mrp,is_sourceable,ifnull(sum(available_qty),0) as stock
	from m_product_info a
	join king_brands b on a.brand_id = b.id 
	left join t_stock_info c on c.product_id = a.product_id
	where (a.product_id = ? or a.product_name like ? or a.brand_id = ? or c.product_barcode = ?  ) $cond 
	group by a.product_id
	order by corr_status,product_name 
	",array($tag,'%'.$tag.'%',$tag,$tag));
	//echo $this->db->last_query();
		if(!$prod_list_res->num_rows())
		{
			$output['status'] = 'error';
			$output['message'] = 'No Products found for your search';
		}else
		{
			$output['status'] = 'success';
			$output['prod_list'] = $prod_list_res->result_array();
		}
		echo json_encode($output);
	}
	
	
	function jx_loadprodstockdet($pid)
	{
		$this->erpm->auth();
		
		$output = array();
		$output['pid'] = $pid;
		$output['pname'] = $this->db->query('select product_name from m_product_info where product_id = ? ',$pid)->row()->product_name;
		$output['is_sourceable'] = $this->db->query('select is_sourceable from m_product_info where product_id = ? ',$pid)->row()->is_sourceable;
		$output['is_active'] = $this->db->query('select is_active from m_product_info where product_id = ? ',$pid)->row()->is_active;
		
		$sql = "select stock_id,product_id,product_barcode,a.location_id,a.rack_bin_id,mrp,available_qty as qty,concat(rack_name,bin_name) as location 
						from t_stock_info a 
						join m_rack_bin_info e on e.id = a.rack_bin_id
						join m_storage_location_info d on d.location_id = a.location_id   
						where product_id = ? 
						group by stock_id  
				";
		$stk_prod_list_res = $this->db->query($sql,$pid);
		if($stk_prod_list_res->num_rows())
		{
			$output['status'] = 'success';
			$output['pstk_list'] = $stk_prod_list_res->result_array();
		}else
		{
			$output['status'] = 'error';
			$output['message'] = 'stock entry not found';
		}
		
		echo json_encode($output);
	}

	function jx_updprodstkdet()
	{
		
		$user = $this->erpm->auth();
		
		$pid = $this->input->post('pid');
		
		$pstkid = $this->input->post('p_stkid');
		$pbc = $this->input->post('p_bc');
		$pmrp = $this->input->post('p_mrp');
		$pqty = $this->input->post('p_qty');
		
		 
		$output = array();
		$is_updated = 0;
		foreach($pstkid as $i=>$stkid)
		{
				
			if($stkid == 0)
			{
				$nsbc = $pbc[$i];
				$nsmrp = $pmrp[$i];
				$nsqty = $pqty[$i];
				
				$loc_id = 1;
				$rbid = 10;
				
				$this->erpm->_upd_product_stock($pid,$nsmrp,$nsbc,$loc_id,$rbid,0,$nsqty,0,1,0,-1,"Stock Correction - Bulk - New MRP ($nsmrp) Added");
				$is_updated++;
				
				$this->_prod_corr_updlog($pid,'corr','In of Rs'.($nsmrp).' - '.$nsqty.' Qty');
				$this->db->query('update m_product_info set corr_status = 1,corr_updated_on=now() where product_id = ? ',$pid);
				continue;	
			}
					
			$stk_info = $this->db->query('select * from t_stock_info where stock_id = ? ',$stkid)->row_array();
			$stk_pbc = $stk_info['product_barcode'];
			if(strlen($pbc[$i]) > 0 && strlen(trim($stk_info['product_barcode'])) == 0)
			{
				$this->db->query('update t_stock_info set product_barcode = ? where product_id = ? and trim(product_barcode) = "" ',array(trim($pbc[$i]),$stk_info['product_id']));
				$this->_prod_corr_updlog($pid,'corr','Barcode Added '.trim($pbc[$i]));
				$is_updated++;
			}
			
			if(!$this->db->query("select count(*) as t from t_stock_info where stock_id = ? and available_qty = ? ",array($stkid,$pqty[$i]))->row()->t)
			{
				
				$old_qty = $stk_info['available_qty'];
				$diff_qty = 0;
				$stock_mov_status = 0;
				if($old_qty < $pqty[$i])
				{
					$diff_qty = $pqty[$i]-$old_qty;
					$stock_mov_status = 1;
				}else
				{
					$diff_qty = $old_qty-$pqty[$i];
					$stock_mov_status = 0;
				}
				
				$this->erpm->_upd_product_stock($stk_info['product_id'],$stk_info['mrp'],$stk_info['product_barcode'],$stk_info['location_id'],$stk_info['rack_bin_id'],0,$diff_qty,0,$stock_mov_status,0,-1,"Stock Correction - Bulk");
				$this->db->query('update m_product_info set corr_status = 1,corr_updated_on=now() where product_id = ? ',$pid);
				
				$this->_prod_corr_updlog($pid,'corr',($stock_mov_status?'In':'Out').' of Rs'.($stk_info['mrp']).' - '.$diff_qty.' Qty');
							 
				$is_updated++;
			}
		}

		if($is_updated)
		{
			$output['status'] = 'success';
		}else
		{
			$this->_prod_corr_updlog($pid,'stk_valid',1);
			$this->db->query('update m_product_info set corr_status=1,corr_updated_on=now(),modified_on=now(),modified_by=? where product_id = ? ',array($user['userid'],$pid));
			
			$output['status'] = 'error';
			$output['message'] = 'No changes found to update';
		}
		
		
		
		echo json_encode($output);
		
	}
	
	function _prod_corr_updlog($pid,$type,$msg)
	{
		$user = $this->erpm->auth();
		$inp = array();
		$inp['product_id'] = $pid;
		$inp['type'] = $type;
		$inp['message'] = $msg;
		$inp['logged_by'] = $user['userid'];
		$inp['logged_on'] = cur_datetime();
		$this->db->insert('m_product_update_log',$inp);
	}

	function jx_updproddncstatus($pid,$is_active)
	{
		$user = $this->erpm->auth();
		
		$pname = $this->db->query('select product_name from m_product_info where product_id = ? ',$pid)->row()->product_name;
		
		if($is_active == 0)
		{
			if(!stristr('DNC',$pname))
				$pname = 'DNC '.$pname;
		}else
			$pname = str_ireplace('DNC', '', $pname);
		
		$this->db->query('update m_product_info set is_active=?,product_name = ?,modified_on=now(),modified_by=? where product_id = ? ',array($is_active,trim($pname),$user['userid'],$pid));
		
		$this->_prod_corr_updlog($pid,'dnc',($is_active?'Unmarked':'Marked'));
		
		$output = array();
		$output['pname'] = $this->db->query('select product_name from m_product_info where product_id = ? ',$pid)->row()->product_name;
		echo json_encode($output);
	}

	/**
	 * function to generate franchise imei activation sheets 
	 */
	function pnh_print_franchise_imeinos()
	{
		$this->erpm->auth();
		
		$data['page'] = 'print_franchise_imei_sheet';
		$this->load->view('admin',$data);
	}

	function jx_getpendingimeifrdet()
	{
		$this->erpm->auth();
		
		$sd = $this->input->post('sd');
		$ed = $this->input->post('ed');
		$trid = $this->input->post('trid');
		
		$cond = '';
		if($trid)
			$cond = ' and f.territory_id = '.$trid;
		
		
		$output = array();
		
		$fr_res = $this->db->query('SELECT t.franchise_id,franchise_name,count(*) as ttl  
										FROM t_imei_no i 
												Join king_orders o on o.id = i.order_id 
												JOIN king_transactions t ON t.transid=o.transid
												JOIN m_product_deal_link p ON p.itemid=o.itemid
												JOIN m_product_info l ON l.product_id=p.product_id
												JOIN king_invoice inv ON inv.order_id=o.id and inv.invoice_status = 1 
												JOIN imei_m_scheme r ON r.id=o.imei_scheme_id
												JOIN shipment_batch_process_invoice_link bi ON bi.invoice_no = inv.invoice_no 
												join pnh_m_franchise_info f on f.franchise_id = t.franchise_id 
												join pnh_m_territory_info ti on ti.id = f.territory_id 
										WHERE o.status in (1,2) and o.imei_scheme_id > 0 and is_imei_activated = 0  and bi.shipped_on between ? and ? '.$cond.'
										group by franchise_id
										order by franchise_name ',array($sd,$ed));
		if(!$trid)
		{
			$tr_res = $this->db->query('SELECT territory_id,territory_name,count(*) as ttl  
										FROM t_imei_no i 
												Join king_orders o on o.id = i.order_id 
												JOIN king_transactions t ON t.transid=o.transid
												JOIN m_product_deal_link p ON p.itemid=o.itemid
												JOIN m_product_info l ON l.product_id=p.product_id
												JOIN king_invoice inv ON inv.order_id=o.id and inv.invoice_status = 1 
												JOIN imei_m_scheme r ON r.id=o.imei_scheme_id
												JOIN shipment_batch_process_invoice_link bi ON bi.invoice_no = inv.invoice_no 
												join pnh_m_franchise_info f on f.franchise_id = t.franchise_id 
												join pnh_m_territory_info ti on ti.id = f.territory_id 
										WHERE o.status in (1,2) and o.imei_scheme_id > 0 and is_imei_activated = 0  and bi.shipped_on between ? and ? '.$cond.'
										group by territory_id
										order by territory_name ',array($sd,$ed));
		}											
		
																				
		if($fr_res->num_rows())
		{
			$output['status'] = 'success';
			$output['fr_list'] = $fr_res->result_array();
			if(!$trid)
				$output['tr_list'] = $tr_res->result_array();
		}else
		{
			$output['status'] = 'error';
			$output['msg'] = 'No franchises found';
		}
		echo json_encode($output);
	}


	function p_print_imei_activationsheet()
	{
		$this->erpm->auth();
		$sd = $this->input->post('st_date');
		$ed = $this->input->post('en_date');
		$trids = $this->input->post('tr_id');
		$frids = $this->input->post('fr_id');
		$output_type = $this->input->post('output');
		$trid = $trids[0];
		$frid = $frids[0];
		
		$cond = '';
		if($trid)
			$cond .= ' and f.territory_id = '.$trid;
		if($frid)
			$cond .= ' and f.franchise_id = '.$frid;
		
		$output = array();
		
		$fr_imei_res = $this->db->query('SELECT t.franchise_id,inv.invoice_no,o.member_id,franchise_name,shipped_on,l.product_id,l.product_name,i.imei_no,o.imei_reimbursement_value_perunit as cr_amt,territory_name,town_name   
										FROM t_imei_no i 
												Join king_orders o on o.id = i.order_id 
												JOIN king_transactions t ON t.transid=o.transid
												JOIN m_product_deal_link p ON p.itemid=o.itemid
												JOIN m_product_info l ON l.product_id=p.product_id
												JOIN king_invoice inv ON inv.order_id=o.id and inv.invoice_status = 1 
												JOIN imei_m_scheme r ON r.id=o.imei_scheme_id
												JOIN shipment_batch_process_invoice_link bi ON bi.invoice_no = inv.invoice_no 
												join pnh_m_franchise_info f on f.franchise_id = t.franchise_id 
												join pnh_m_territory_info ti on ti.id = f.territory_id
												join pnh_towns tw on tw.id = f.town_id 
										WHERE o.status in (1,2) and o.imei_scheme_id > 0 and is_imei_activated = 0  and bi.shipped_on between ? and ? '.$cond.'
										group by franchise_id,i.id
										order by franchise_name ',array($sd,$ed));
		if($fr_imei_res->num_rows())
		{
			$fr_imei_list = array();
			foreach($fr_imei_res->result_array() as $fr_imei_det)
			{
				if(!isset($fr_imei_list[$fr_imei_det['franchise_id']]))
				{
					$fr_imei_list[$fr_imei_det['franchise_id']] = array('name'=>$fr_imei_det['franchise_name'],'imei'=>array());
				}
				array_push($fr_imei_list[$fr_imei_det['franchise_id']]['imei'],$fr_imei_det);
			}
			
			if($output_type == 0)
			{
				$op_str = '';
				foreach($fr_imei_list as $fr_id=>$fr_imei)
				{
					$op_str .= '<div style="page-break-after:always">';
					$op_str .= '<table width="100%" cellpadding="5" cellspacing="0" >';
					$op_str .= '<tr><td colspan="3" align="center"> <h3>IMEI Activation Sheet</h3> </td></tr>';
					$op_str .= '<tr>
									<td width="30%" align="left">
										Franchise : '.($fr_imei['name']).' <br>
										Town : '.($fr_imei['imei'][0]['town_name']).' ('.$fr_imei['imei'][0]['territory_name'].') 
									</td>
									<td>&nbsp;</td>
									<td width="30%" align="right">
										Printed On : '.format_date(date('Y-m-d')).' 
									</td>
								</tr>';
					$op_str .= '<tr><td colspan="3">';
					$op_str .= '<table width="100%" border=1 style="border-collapse:collapse">
									<thead>
										<th>Slno</th>
										<th>Shipped Date</th>
										<th>Invoice</th>
										<th>Product Name</th>
										<th>IMEINO</th>
										<th>Credit (Rs)</th>
										<th>MemberID</th>
										<th>Customer Mobileno</th>
										<th>Customer Name</th>
									</thead>
									<tbody>
							';								
					foreach($fr_imei['imei']  as $i=>$imei_d)
					{
						$op_str .= '<tr>
										<td width="20" style="text-align:right;height:20px;">'.($i+1).'</td>
										<td width="60">'.format_date($imei_d['shipped_on']).'</td>
										<td width="60">'.($imei_d['invoice_no']).'</td>
										<td width="200">'.($imei_d['product_name']).'</td>
										<td width="100">'.($imei_d['imei_no']).'</td>
										<td width="40" style="text-align:right">'.($imei_d['cr_amt']).'</td>
										<td width="100">'.($imei_d['member_id']).'</td>
										<td width="150">&nbsp;</td>
										<td width="150">&nbsp;</td>
									</tr>';
					}
					$op_str .= '	<tbody>
								</table>';
					$op_str .= '</tr>';
					$op_str .= '</table>';
					$op_str .= '</div>';
				}
				
				
				$data['print_title'] = 'IMEI Activation Sheet';
				$data['print_data'] = $op_str;
				$data['auto_print'] = 1;
				$this->load->view('admin/body/print_bydata',$data);
			}else
			{
				$csv_data = array();
				$csv_data[] = implode(",",array('Slno','FranchiseID','Franchise Name','ShippedOn','Invoiceno','Product Name','Imeino','Credit Amt(Rs)','MemberID','Mobileno','Customer Name'));
				$k = 0;
				foreach($fr_imei_list as $fr_id=>$fr_imei)
				{
					
					foreach($fr_imei['imei'] as $imei_d)
					{
						$tmp = array();
						$tmp[] = ++$k;
						$tmp[] = $fr_id;
						$tmp[] = $fr_imei['name'];
						$tmp[] = format_date($imei_d['shipped_on']);
						$tmp[] = ($imei_d['invoice_no']);
						$tmp[] = ($imei_d['product_name']);
						$tmp[] = ($imei_d['imei_no']);
						$tmp[] = ($imei_d['cr_amt']);
						$tmp[] = ($imei_d['member_id']);
						$tmp[] = "                    ";
						$tmp[] = "                    ";
						$csv_data[] = implode(",",$tmp);	
					}
				}
				$csv = implode("\r\n",$csv_data);
				header('Content-Description: File Transfer');
				header('Content-Type: text/csv');
				header('Content-Disposition: attachment; filename='.('IMEI_Activation_Sheet_'.date("d_m_y_H\h:i\m").".csv"));
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . strlen($csv));
				echo $csv;
				exit;
			}

		}else
		{
			echo '<script>alert("No data found")</script>';
		}								
										
	}
}
