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
		list($no,$dec) = @explode('.',$num);
		
		$n = formatInIndianStyle($no);
		$n .= $d?'.'.str_pad($dec,$d,'0'):'';
		
		return $n;
	}
}
	
?>