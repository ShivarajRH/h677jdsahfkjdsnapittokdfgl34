<?php
class Exotel extends Controller
{
	function Exotel()
	{
		parent::Controller();
	}
	
	/**
	 * function to process sms from pool with the return track status 
	 */
	function sms_pool()
	{
		// check for logged sms to be sent and send sms request based on the ban
	}
	
	function _send_sms($log_id)
	{
		
	}
	
	/**
	 * function to check for sms status 
	 */
	function _check_sms_status($log_id)
	{
		
	}
	
	
	/**
	 * function to log passthorugh info to log table
	 */
	function _log_passthrough($request)
	{
		if(!$request['CallSid'])
			return ;
		
		$ins = array();
		$ins['CallSid'] = $request['CallSid'];
		$ins['From'] = $request['From'];
		$ins['To'] = $request['To'];
		$ins['Direction'] = $request['Direction'];
		$ins['DialCallDuration'] = $request['DialCallDuration'];
		$ins['StartTime'] = $request['StartTime'];
		$ins['EndTime'] = $request['EndTime'];
		$ins['CallType'] = $request['CallType'];
		$ins['digits'] = $request['digits'];
		$ins['RecordingUrl'] = $request['RecordingUrl'];
		$ins['logged_on'] = date('Y-m-d H:i:s');
		$this->db->insert('l_exotel_calllog',$ins);
	}
	
	/**
	 * function to collect request params and process on type requested 
	 * @param unknown_type $type
	 */
	function pass_through($type = '')
	{
		
		if($type=='')
			die("asd");
		
		// Prepare data array 
		$this->request = $_REQUEST;
		
		// Log incomming request params dump to log file as a backup
		$this->_log_passthrough($this->request);
		
		switch($type)
		{
			case 'ivr_check_member' : $this->_ivr_check_member(); break;
			case 'ivr_ord_init' : $this->_ivr_ord_init(); break;
			case 'ivr_ord_gatherinp' : $this->_ivr_ord_gatherinp(); break;
			case 'ivr_ord_placeorder' : $this->_ivr_ord_placeorder(); break;
			case 'ivr_ord_greeting' : $this->_ivr_ord_greeting(); break;
		}
	}
	
	/**
	 * function to process order init process by ivr log request by call id  
	 */
	function _ivr_check_member()
	{
		$data = $this->request;
		
		$csid = $data['CallSid'];
		$from = $data['From'];
		
		if(substr($from, 0,1) == '0')
			$from = substr($from,1);
		
		// check if the mobile is member mobile 
		$this->load->model('member_model');
		$member_det = $this->member_model->get_memberbymob($from);
		if(!$member_det)
		{
			echo '302 Found';
		}else
		{
			echo '200 OK';
		}
	}
	
}