<?php
/**
 * Description of analytics
 * PNH Franchise orders analytics
 * @author Shiv
 */
include APPPATH.'/controllers/voucher.php';
class Analytics extends Voucher 
{    
    function __construct() {
        parent::__construct();
        $this->load->library("input");
    }
    
    /**
     * PNH analytics dashboard (Default)
     */
    function analytics($pg='dashboard') {
        
        $data['user']=$user=$this->erpm->auth();//ADMINISTRATOR_ROLE
                
        $data['pnh_terr'] = $this->db->query("select * from pnh_m_territory_info order by territory_name")->result_array();
        $data['pnh_towns'] = $this->db->query("select id,town_name from pnh_towns order by town_name")->result_array();

        $data['page']='analy_dashboard';
        $this->load->view("admin",$data);
    }
    /**
    * function to generate franchise list by territoryid and townid
    * @param type $townid
    */
   function ajax_get_all_franchise_details()
   {
       $this->erpm->auth();
       //print_r($_POST);
       $terr_id=$this->input->post('terr_id');
       $town_id=$this->input->post('town_id');
       $franchise_status=$this->input->post('franchise_status');

       $output = ''; $and_cond='1=1';
       if($terr_id!='00') {
           $and_cond .=' and fi.territory_id='.$terr_id;
       }
       if($town_id!='00') {
           $and_cond .=' and fi.town_id='.$town_id;
       }
       if($franchise_status!='00') {
           if($franchise_status == 'active') {
               $franchise_status=0;
           }
           $and_cond .=' and fi.is_suspended='.$franchise_status;
       }


       // populate all towns in territory 
       $list_res = $this->db->query("select * from pnh_m_franchise_info fi where $and_cond order by fi.created_on asc");

       if($list_res->num_rows())
       {
           $output['franchise']=$list_res->result_array();
           $output['time']=time();
           //$output['last_query']=  $this->db->last_query();
           $output['total_fran'] = $list_res->num_rows();
            //$output['total_suspended_fran'] = $this->db->query("select count(*) as total from pnh_m_franchise_info fi where fi.is_suspended=1 $and_cond")->row()->total;
            //$output['total_active_fran'] = $this->db->query("select count(*) as total from pnh_m_franchise_info fi where fi.is_suspended=0 $and_cond")->row()->total;
       }
       else {
           $output['error'] = 'No data.';
       }

       echo json_encode($output);//$this->db->last_query().
   }
   
    /**
    * function to generate franchise details
    * @param $fran_id
    */
   /*function ajax_get_franchise_details($fran_id)
   {
        $this->erpm->auth();
        //print_r($_POST);
       
        // populate franchise details
        $list_res = $this->db->query("select * from pnh_m_franchise_info fi where fi.franchise_id=? limit 1",$fran_id);
        
        $output=$list_res->row_array();
        $output['time']=time();
       
        echo json_encode($output);//$this->db->last_query().
   }*/
   /**
    * Get all franchise count based on territory and town ids
    */
   function ajax_get_fran_total_log() {
       $this->erpm->auth();
       $terr_id=$this->input->post('terr_id');
       $town_id=$this->input->post('town_id');
       
       $and_cond='';
       if($terr_id!='00') {
           $and_cond .=' and fi.territory_id='.$terr_id;
       }
       if($town_id!='00') {
           $and_cond .=' and fi.town_id='.$town_id;
       }

       $output['total_fran'] = $this->db->query("select count(*) as total from pnh_m_franchise_info fi where 1=1 $and_cond")->row()->total;
       $output['total_suspended_fran'] = $this->db->query("select count(*) as total from pnh_m_franchise_info fi where fi.is_suspended=1 $and_cond")->row()->total;
       $output['total_active_fran'] = $this->db->query("select count(*) as total from pnh_m_franchise_info fi where fi.is_suspended=0 $and_cond")->row()->total;
       echo json_encode($output);
   }
   
   /**
    * function to generate town list by territoryid 
    * @param type $townid
    */
   function ajax_suggest_townbyterrid($terrid)
   {
       $this->erpm->auth();

       $output = array();
       // populate all towns in territory 
       $town_list_res = $this->db->query("select id,town_name from pnh_towns where territory_id = ? order by town_name ",$terrid);
       if($town_list_res->num_rows())
       {
           $output['status'] = 'success';
           $output['towns'] = json_encode($town_list_res->result_array());
       }else
       {
           $output['status'] = 'error';
           $output['message'] = 'No towns for territory';
       }
       echo json_encode($output);
   }

}
