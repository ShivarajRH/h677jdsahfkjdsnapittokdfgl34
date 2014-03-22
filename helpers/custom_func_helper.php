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
		list($no,$dec) = explode('.',$num);
		
		$n = formatInIndianStyle($no);
		$n .= $d?'.'.str_pad($dec,$d,'0'):'';
		
		return $n;
	}
}
/**
* Function to display json error output and stop the execution
* @param type $msg String / Array
* @param type $rtype string
* @example if(!$itemid) $this->print_error("Item id doesnot exists!");
* @example if(!$itemid) $this->print_error(array("status"=>"fail","message"=>"Item id doesnot exists!" ));
*/
if(!function_exists('print_error')) {
    function print_error($msg,$rtype='json') {
        if(is_array($msg)) {
            $rdata = $msg;
        }
        else {
            $rdata = array("status"=>"fail","message"=>$msg);
        }
        echo json_encode($rdata);
        die();
    }
}
/**
 * Same as print_error() helper function
 */
if(!function_exists('print_msg')) {
    function print_msg($msg,$rtype='json') {
        print_error($msg,$rtype); // redirect to print_error();
    }
}
?>