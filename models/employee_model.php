<?php 
/**
 * All office employee related actions are stored here
 * @author Shivaraj <shivaraj@storeking.in>
 * @date Jun_05_2014
 * @modified_on -
 */
class Employee_model extends Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('erpmodel','erpm');
		$this->load->model('franchise_model');
		$this->load->model('member_model');
	}
	
	/**
	 * Function to return department list in company
	 * @author Shivaraj <shivaraj@storeking.in>
	 * @return boolean/array
	 */
	function get_departments_list()
	{
		$user = $this->erpm->auth();
		$dept_list_res = $this->db->query("SELECT id,`name`,keyword,`status` FROM m_departments ORDER BY `name` ASC");
				
		if($dept_list_res->num_rows()){
			return $dept_list_res->result_array();
		}else{
			return false;
		}
	}

	/**
	 * function to return department details
	 * @author Shivaraj <shivaraj@storeking.in>
	 * @param type $emp_id
	 * @return boolean
	 */
	function get_dept_det_by_empid($emp_id)
	{
		$user = $this->erpm->auth();
		$dept_res = $this->db->query("SELECT dept_id FROM m_employee_dept_link WHERE is_active = 1 AND employee_id = ?",$emp_id);
				
		if($dept_res->num_rows()){
			return $dept_res->row_array();
		}else{
			return false;
		}
	}

	/**
	 * Function to fetch user access roles
	 * @return boolean
	 */
	function get_emp_access_roles(){
		$user_authdet = $this->session->userdata('user_authdet');

		$access_list_res = $this->db->query("SELECT  role_id,role_name
												FROM m_employee_roles
												WHERE role_id > 1 and emp_role_status=1");
				
			
		if($access_list_res->num_rows()){
			return $access_list_res->result_array();
		}else{
			return false;
		}
	}
		
	/**
	 * function to get roles
	 * @return boolean
	 */
	function getRolesList(){
		$sql="select * from m_employee_roles";
		$res=$this->db->query($sql);
		if($res->num_rows()){
			return $res->result_array($sql);
		}
		return false;
	}
	
	/**
	 * Function to return Job Title by userid
	 * @param type $userid int
	 * @return type id
	 */
	function get_jobrolebyuid($userid)
	{
		return @$this->db->query("select job_title as role_id from m_employee_info where user_id = ? and is_suspended=0",$userid)->row()->role_id;
	}
	
	/**
	 * Function to get emp info
	 * @param unknown_type $emp_id
	 * @return boolean
	 */
	function get_empinfo($emp_id)
	{
		
		$sql = "SELECT *,b.role_name,c.role_name as role FROM `m_employee_info` a
				left JOIN `m_employee_roles`b ON b.role_id=a.job_title
				LEFT JOIN m_employee_roles c ON c.role_id=a.job_title2				
				WHERE a.`employee_id`=?
				";
		$res = $this->db->query($sql,$emp_id);
		
		if($res->num_rows()){
			return $res->row_array();
		}
		return false;
	}
	
	/**
	 * Function to return employee info under given departments
	 * @author Shivaraj <shivaraj@storeking.in>
	 * @param type $dept_ids comma seperated dept_id
	 * @return boolean Array/Bool
	 */
	function get_emp_det_by_deptids($dept_ids)
	{
		$emp_det_res = $this->db->query("SELECT emp.employee_id,emp.name,emp.email,emp.contact_no
			FROM m_departments dt
			JOIN m_employee_dept_link edl ON edl.dept_id = dt.id
			JOIN m_employee_info emp ON emp.employee_id = edl.employee_id
			WHERE dt.id IN (?) AND job_title=9 and emp.email !='' 
			HAVING emp.employee_id IS NOT NULL ",$dept_ids);
		
		if($emp_det_res->num_rows())
		{
			return $emp_det_res->result_array();
		}
		else
		{
		return false;
		}
	}
	
	/**
	 * Function to get comma seperated department ids by request type
	 * @author Shivaraj <shivaraj@storeking.in>
	 * @param type $type the service type: product,insurance,payment etc.
	 * @return boolean/array
	 */
	function get_deptids_by_request_type($type)
	{
		$dept_id_res = $this->db->query("SELECT GROUP_CONCAT(DISTINCT rlink.dept_id) AS dept_ids,rlink.type_id,rtype.name as request_name FROM m_dept_request_type_link rlink
											LEFT JOIN `m_dept_request_types` rtype ON rtype.id = rlink.type_id
											WHERE rlink.is_active=1 AND rlink.type_id = ?
											HAVING dept_ids IS NOT NULL	",array($type));
		if($dept_id_res->num_rows())
		{
			return $dept_id_res->row_array();
		}
		else
			return false;
	}
	
	/**
	 * Function to get dept_det by empid
	 * @author Shivaraj <shivaraj@storeking.in>
	 * @param type $emp_id
	 * @return boolean
	 */
	function get_dept_det_byemp($emp_id)
	{
		$dept_det_res=$this->db->query("SELECT ed.employee_id,ed.is_active,d.name AS dept_name,d.created_by
											FROM `m_employee_dept_link` ed
											JOIN m_departments d ON d.id = ed.dept_id
											WHERE ed.is_active = 1 and ed.employee_id= ?
											HAVING dept_name IS NOT NULL ",$emp_id);
		if($dept_det_res->num_rows() > 0)
			return $dept_det_res->row_array(); 
		else
			return false;
	}
	
	/**
	 * Function to return service request types 
	 * @author Shivaraj <shivaraj@storeking.in>_Jun_26_2014
	 * @return JSON
	 */
	function get_services_req_list()
	{
		$req_list_res = $this->db->query("SELECT * FROM `m_dept_request_types` ORDER BY id") or output_error( 'DBError:'.mysql_error(),1);
		if($req_list_res->num_rows()<=0)
		{
			return false;
		}
		else
		{
			return $req_list_res->result_array();
		}
	}
	
	/**
	 * Function to save request and department settings
	 * @author Shivaraj <shivaraj@storeking.in>_Jul_12_2014
	 * @param type $_POST data
	 */
	function do_depts_request_assign($_POST)
	{
		//echo "<pre>";print_r($_POST);echo '</pre>';
		$userid =  $this->input->post("userid");
		$dept_id =  $this->input->post("sel_dept_id");
		$type_ids = $this->input->post("sel_type_ids");
		
		//Reset link
		$up_arr = array('is_active'=>"0", 'modified_on'=> cur_datetime(), 'modified_by'=>$userid);
		$this->db->update("m_dept_request_type_link",$up_arr,array('dept_id'=>$dept_id));
		
		foreach($type_ids as $type_id )
		{
			
			
			//check link
			$is_dept_req_exits_res = $this->db->query("SELECT * FROM m_dept_request_type_link WHERE dept_id=? AND type_id=?",array($dept_id,$type_id) );
			//echo ("<br>=>".$is_dept_req_exits_res->num_rows());
			if($is_dept_req_exits_res->num_rows()) {
				//update link
				$up_arr = array('is_active'=>"1", 'modified_on'=> cur_datetime(), 'modified_by'=>$userid);
				$this->db->update("m_dept_request_type_link",$up_arr,array('dept_id'=>$dept_id,'type_id'=>$type_id));
			}
			else {
				//or insert link
				$in_arr = array('dept_id'=>$dept_id, 'type_id'=>$type_id,'is_active'=>1, 'created_on'=> cur_datetime(), 'created_by'=>$userid);
				$this->db->insert("m_dept_request_type_link",$in_arr);
			}
		}
		$this->session->set_flashdata("erp_pop_info","Requests for departments has updated.");
		redirect("/admin/depts_request");
	}
	
	/**
	 * Function to send email to employee  by department wise
	 * @author Shivaraj <shivaraj@storeking.in>_Aug_04_2014
	 * @param type $related_to int
	 * @param type $ticket_id int 
	 * @param type $ticket_no int
	 * @param type $franchise_name string
	 * @param type $req_mem_name string
	 * @param type $desc string
	 * @param type $tkt_status string
	 * @return string
	 */
	function send_dept_emails($related_to,$ticket_id,$ticket_no,$franchise_name='',$req_mem_name='',$desc='',$tkt_status = 'Open')
	{
		//$this->load->helper('text');
					
		$dept_ids = '';
		$dept_det = $this->employee->get_deptids_by_request_type($related_to);
		if(!$dept_det)
		{
			//$this->_output_handle('json',false,array('error_code'=>2009,'error_msg'=>"This request not linked to any departments or Invalid request id") );
			$output['error']="This request not linked to any departments or Invalid request id";
		}
		else
		{
			$dept_ids=$dept_det['dept_ids'];
			$type_name=$dept_det['request_name'];
			
			$link = '<a href="'.site_url("admin/ticket/".$ticket_id).'">TK'.$ticket_no.'</a>';
			// SEND EMAIL/SMS
			// =============================================
			$emp_det_res = $this->employee->get_emp_det_by_deptids($dept_ids);
			if($emp_det_res) 
			{
				foreach($emp_det_res as $emp_det) {
					//echo '<br>'.$emp_det['name'].': '.$emp_det['email'];
					$arr_emails[] = $emp_det['email'];
				}

				// ================< SEND DEPT EMAILS STARTS >=============================
				$filename = base_url()."/resources/templates/template_request_foremail.html";
				$body_msg =  file_get_contents($filename);
				$body_msg = str_replace("%%requested_on%%",date("d/M/Y H:i:s e",time()) , $body_msg);
				$body_msg = str_replace("%%requested_by%%", $franchise_name , $body_msg);
				$body_msg = str_replace("%%tkt_status%%",$tkt_status , $body_msg);
				$body_msg = str_replace("%%request_type%%",$type_name , $body_msg);
				$body_msg = str_replace("%%description%%",$desc , $body_msg);
				$body_msg = str_replace("%%link%%",$link , $body_msg);

				$subj = "[TK{$ticket_no}] Storeking Customer Support";//'New '.$type_name." request from ".$franchise_name." (TK".$ticket_no.') - '.word_wrap($desc,'20');
				$this->erpm->_notifybymail($arr_emails,$subj,$body_msg);

				// ================< SEND DEPT EMAILS ENDS >=============================
			}
			//$this->_output_handle('json',true,array("ticket_id"=>$ticket_id,'message'=>"Thank You for submitting. We received your request. Please Expect our reply shortly") );
			$output['message']="Thank You for submitting. We received your request. Please Expect our reply shortly";
			
		}
		// =============================================
		return $output;
	}
}
