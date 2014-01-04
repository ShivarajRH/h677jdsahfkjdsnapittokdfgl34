<?php
/**
 * Recommend Api
 * 
 * @desc recommend api   
 * @author Shariff
 */
class recommend_api {
	private $api_url = 'http://emonkey.in/index.php';
	private $key = '';
	private $track_id = '';
	var $error_codes = array ('5001' => 'Tracking ID not found', '5002' => 'Invalid Authentication KEY', '5003' => 'Authentication is required' );
	
	/**
	 * Default Constructor 
	 */
	function __construct($key = '') {
		$this->key = $key;
		$this->track_id = NULL;
	}
	
	/**
	 * function to get current Visit ID 
	 */
	function get_trackerid() {
		if (isset ( $_SESSION ['track_id'] )) {
			return $_SESSION ['track_id'];
		}
		return FALSE;
	}
	
	/**
	 * function lto set tracker id 
	 */
	function set_trackerid($track_id) {
		if ($_SESSION ['track_id']) {
			unset ( $_SESSION ['track_id'] );
			$_SESSION ['track_id'] = $track_id;
		}
		$this->track_id = $track_id;
	}
	
	/**
	 * function to send purchase status by tracking id 
	 */
	function update_purchase($ref_item_id = 0) {
		$track_id = $this->get_trackerid ();
		if (! $track_id) {
			// Tracking ID not found and consider not to process anything
		} else {
			$api_url = $this->api_url . '/tracker/process_post_purcase';
			$post_param = array ();
			$post_param ['key'] = $this->key;
			$post_param ['track_id'] = $track_id;
			$post_param ['ref_item_id'] = $ref_item_id;
			$post_param ['ts'] = time ();
			$response = $this->_process_http ( $api_url, $post_param );
			//print_r ( $response );
		}
	}
	
	/**
	 * function to show error if any by error code 
	 *
	 * @param number $error_code error code defination found in public var $error_codes
	 * 
	 */
	function _send_error($error_code) {
		// return error message from error code 
		return $error_codes [$code];
	}
	
	/**
	 * function to build query string from array 
	 * 
	 * @param array $arr  
	 */
	function build_qs($arr) {
		$qs = array ();
		foreach ( $arr as $a => $b ) {
			array_push ( $qs, $a . '=' . urlencode ( $b ) );
		}
		
		return implode ( '&', $qs );
	}
	
	/**
	 * function to process purchase  
	 */
	function _process_http($api_url, $post_params) {
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $api_url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt ( $ch, CURLOPT_HEADER, FALSE );
		curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:11.0) Gecko/20100101 Firefox/11.0' );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		if ($post_params) {
			$post_data = $this->build_qs ( $post_params );
			curl_setopt ( $ch, CURLOPT_POST, true );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
		}
		$response = curl_exec ( $ch );
		$result = json_decode ( $response );
		curl_close ( $ch );
		return $result;
	}

}

