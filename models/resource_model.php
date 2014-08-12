<?php
class resource_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	
	/**
	 * function to get config parameters from Database
	 */
	function _get_config_param($name)
	{
		return $this->db->query("select value from m_config_params where name = ?  ",$name)->row()->value;
	}
	
	/**
	 * function to set config paramaters based on name and value pair
	 */
	function _set_config_param($name,$value)
	{
		$this->db->query("update m_config_params set value = ? where name = ? limit 1",array($value,$name));
	}
	
	/**
	 * function to send sms 
	 * @param unknown_type $to
	 * @param unknown_type $msg
	 * @param unknown_type $fid
	 * @param unknown_type $empid
	 * @param unknown_type $type
	 * @return unknown
	 */
	function sendsms($to,$msg,$fid=0,$empid=0,$type=0)
	{
		$sms_log_id = 0;
		if($fid!=0)
		{
			$inp=array("to"=>$to,"msg"=>$msg,"franchise_id"=>$fid,"pnh_empid"=>$empid,"type"=>$type,"sent_on"=>time());
			$this->db->insert("pnh_sms_log_sent",$inp);
			$sms_log_id = $this->db->insert_id();
		}
		
		$exotel_sid=EXOTEL_UID;
		$exotel_token=EXOTEL_AUTHKEY;
		$post = array(
				'From'   => EXOTEL_MOBILE_NO,
				'To'    => $to,
				'Body'  => $msg
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
		
		if($sms_log_id)
			return $sms_log_id;
	}
	
}