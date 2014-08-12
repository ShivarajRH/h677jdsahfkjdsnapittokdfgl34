<?php
/**
 * SMS Model to handle sms module  
 * 
 * @author shariff
 *
 */
class sms_model  extends Model
{
	/**
	 * Default Constructor 
	 */
	function sms_model()
	{
		parent::Model();
		$this->load->model('resource_model');
	}
	
	/**
	 * function to get sms template 
	 * @param unknown_type $type
	 * @return boolean
	 */
	function _get_sms_template($type)
	{
		$tmpl_res = $this->db->query("select * from m_sms_templates where type = ? and is_active = 1 ",$type);
		if($tmpl_res->num_rows())
			return $tmpl_res->row()->sms_template;
		return false;
	}
	
	/**
	 * function to get sent sms details by sid 
	 *  
	 * @param unknown_type $sid
	 * @return boolean
	 */
	function get_messagebysid($sid)
	{
		$sms_det_res = $this->db->query("select * from t_raw_sms_log where sms_sid = ? ",$sid);
		if($sms_det_res->num_rows())
			return $sms_det_res->row_array();
		return false;
	}

	/**
	 * function to log sms messages sent 
	 * 
	 * @param Array $log
	 */
	function log_sms_message($log)
	{
		$ins_det = array();
		$ins_det['sms_sid'] = $log['sid'];
		$ins_det['sms_to'] = $log['to'];
		$ins_det['sms_msg'] = $log['msg'];
		$ins_det['status'] = $log['sms_status'];
		$ins_det['status_update_feed'] = $log['sms_status_feed'];
		$ins_det['sms_response'] = json_encode($log['sms_det_arr']);
		$ins_det['logged_on'] = cur_datetime();
		$this->db->insert("t_raw_sms_log",$ins_det);
		return $this->db->insert_id();
	}
	
	/**
	 * fucntion to update sms status by sid and status
	 * 
	 * @param unknown_type $status
	 * @param unknown_type $sid
	 */
	function update_sms_statusbysid($status,$sid)
	{
		$this->db->query("update t_raw_sms_log set status = ? where sid = ? ",array($status,$sid));
	}
	
	/**
	 * function to prepare and send sms message notification 
	 * @param unknown_type $type
	 * @param unknown_type $params
	 */
	function send($type,$params)
	{
		$sms_template = $this->_get_sms_template($type);
		
		if(!$sms_template)
			log_message('error',"SMS Template not found");
		
		echo $sms_template;
		
	}
	
	
	function _process_message($to,$msg)
	{
	
		$exotel_sid=$this->resource_model->_get_config_param('EXOTEL_AUTH_SID');
		$exotel_token=$this->resource_model->_get_config_param('EXOTEL_AUTH_TOKEN');
		$post = array(
				'From'   => $this->resource_model->_get_config_param('EXOTEL_AUTH_PHNO'),
				'To'    => $to,
				'Body'  => $msg,
				'StatusCallback'=>$this->resource_model->_get_config_param('EXOTEL_SMS_CALLBACK')
		);
		$url = "https://".$exotel_sid.":".$exotel_token."@twilix.exotel.in/v1/Accounts/".$exotel_sid."/Sms/send";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FAILONERROR, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
		$http_result = curl_exec($ch);
		$error = curl_error($ch);
		$http_code = curl_getinfo($ch ,CURLINFO_HTTP_CODE);
		curl_close($ch);
	
		$this->load->plugin('xml2array');
		$sms_det_arr = xml2array($xmlStr,1);
	
		$sms_sid = '';
		$sms_status = '';
		if(isset($sms_det_arr['TwilioResponse']['SMSMessage']['Sid']))
		{
			$sms_sid = $sms_det_arr['TwilioResponse']['SMSMessage']['Sid'];
			$sms_status = $sms_det_arr['TwilioResponse']['SMSMessage']['Status'];
			$sms_status_feed = $sms_det_arr['TwilioResponse']['SMSMessage']['Status'].':'.cur_datetime();
		}
	
		$ins_det = array();
		$ins_det['sms_sid'] = $sms_sid;
		$ins_det['sms_to'] = $to;
		$ins_det['sms_msg'] = $msg;
		$ins_det['status'] = $sms_status;
		$ins_det['status_update_feed'] = $sms_status_feed;
		$ins_det['sms_response'] = json_encode($sms_det_arr);
		$ins_det['logged_on'] = cur_datetime();
		$this->db->insert("t_raw_sms_log",$ins_det);
	
	}
	
}


