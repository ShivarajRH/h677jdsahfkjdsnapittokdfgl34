<?php
if(!function_exists('cur_datetime')){
	function cur_datetime(){
		return date('Y-m-d H:i:s');
	}
}
if(!function_exists('date_diff_days')){
	function date_diff_days($date_1='',$date_2=''){
		return (strtotime($date_1)-strtotime($date_2))/(24*60*60);
	}
}

if(!function_exists('format_price')){
	function format_price($num,$d=2)
	{
		$num = round($num,$d);
		$pts = explode('.',$num);
		
		$no = isset($pts[0])?$pts[0]:0;
		$dec = isset($pts[1])?$pts[1]:0;
		
		$n = formatInIndianStyle($no);
		$n .= $d?'.'.str_pad($dec,$d,'0'):'';
		
		return $n;
	}
}
/**
* Function to display json error output and stop the execution
* @param type $msg String / Array
* @param type $rtype string
* @example if(!$itemid) output_error("Item id doesnot exists!");
* @example if(!$itemid) output_error(array("status"=>"fail","message"=>"Item id doesnot exists!" ));
* @Returns status:success/error, message:data output
*/
if(!function_exists('output_error')) {
    function output_error($msg,$rtype='json',$status='error') {
        if(is_array($msg)) {
            $msg['status'] = $status;
            $rdata = $msg;
        }
        else {
            $rdata = array("status"=>$status,"message"=>$msg);
        }
        echo json_encode($rdata);
        die();
    }
}
/**
 * Same as output_error() helper function ,input array or string
 * @Returns status:success/error, message:data output
 */
if(!function_exists('output_data')) {
    function output_data($msg,$rtype='json') {
        output_error($msg,$rtype,"success"); // redirect to output_error();
    }
}
?>