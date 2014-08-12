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
if(!function_exists('output_error')) {
    /**
    * Function to display json error output and stop the execution
	* @author Shivaraj <shivaraj@storeking.in>
    * @param type $msg String / Array
    * @param type $rtype string
    * @example if(!$itemid) output_error("Item id doesnot exists!");
    * @example if(!$itemid) output_error(array("status"=>"error","message"=>"Item id doesnot exists!" ));
    * @Returns status:success/error, message:data output
    */
    function output_error($msg,$rtype='json',$status='error') {
        if(is_array($msg)) {
            $msg['status'] = $status;
            $rdata = $msg;
        }
        else {
            $rdata = array("status"=>$status,"message"=>$msg);
        }
		if($rtype == 'json')
			echo json_encode($rdata);
		elseif($rtype == 'array' || $rtype === 1) {
			return $rdata;
		}
        die();
    }
}
if(!function_exists('output_data')) {
    /**
	 * Same as output_error() helper function ,input array or string
	 * @author Shivaraj <shivaraj@storeking.in>
	 * @Returns status:success/error, message:data output
	 */
    function output_data($msg,$rtype='json') {
        output_error($msg,$rtype,"success"); // redirect to output_error();
    }
}

if(!function_exists('valid_mobileno'))
{
	/**
	 * function to validate given mobile no is valid or not
	 */
	function valid_mobileno($no)
	{
		$stat = true;
		$no = (INT)trim($no);
		if(!is_integer($no))
			$stat = false;
		else if(strlen($no) != 10)
			$stat = false;
		return true;
	}
}

if(!function_exists('is_company_email') )
{
	/**
	 * Helper function to check is given email is company generated emails (storeking.in,localcircle.in)
	 * @author Shivaraj <shivaraj@storeking.in>
	 * @param type $email string
	 * @return boolean TRUE/FALSE
	 */
	function is_company_email($email)
	{
		$domain = '';
		$company_domains_lst = array('storeking.in','localcircle.in');
		
		// make sure a valid email
		if( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			$domain = array_pop(explode('@', $email ));
			//or 
			//$domain = substr(strrchr($email, "@"), 1);
		}
		
		if(in_array($domain,$company_domains_lst) )
				return true;
		else
				return false;
	}
}

if( !function_exists('create_csv_file') )
{
	/**
	 * Function to generate CSV file by result array
	 * @Author Shivaraj <shivaraj@storeking.in>
	 * @param type $reports array of data to be exported
	 * @param type $filename Name of export file
	 */
	function create_csv_file($reports,$filename)
	{
			ob_start();
			$f=fopen("php://output","w");
			foreach($reports as $i=>$r)
			{
				if(!$i)
					fputcsv($f,array_map("_gen_header_name",array_keys($r)));
				fputcsv($f, $r);
			}
			fclose($f);
			$csv=ob_get_clean();
			ob_clean();
			header('Content-Description: File Transfer');
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename='.($filename.".csv"));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . strlen($csv));
			ob_clean();
			flush();
			echo $csv;
			exit;
	}
}

if(!function_exists("search_in_array"))
{
	/**
	 * Helper function to search key value in Array/Multidimentional array
	 * @author Shivaraj <shivaraj@storeking.in>_Jun_19_2014
	 * @param type $srchvalue string
	 * @param type $array array
	 * @return type array
	 */
	function search_in_array($srchvalue, $array)
	{
		if (is_array($array) && count($array) > 0)
		{
			$foundkey = array_search($srchvalue, $array);
			if ($foundkey === FALSE)
			{
				foreach ($array as $key => $value)
				{
					if (is_array($value) && count($value) > 0)
					{
						$foundkey = search_in_array($srchvalue, $value);
						if ($foundkey != FALSE)
							return $foundkey;
					}
				}
			}
			else
				return $foundkey;
		}
	}
}

if(!function_exists("match_in_list"))
{
	/**
	 * Helper function to match all comma seperated values with given value
	 * @author Shivaraj <shivaraj@storeking.in>_Jun_19_2014
	 * @param type $var string
	 * @param type $list string
	 * @example Check delivery status like::: match_in_list($delivery,"0,1" )- it will return true if $delivery value is 0 or 1
	 * @equivalant_to if($delivery==0 || $delivery==1)
	 * @return boolean
	 */
	function match_in_list($var,$list)
	{
		$list_arr = explode(',',$list);
		
		foreach($list_arr as $val)
		{
			if($var == $val )
			{
				return true;
			}
		}
		return FALSE;
	}
		
	/**
	 * Function to Convert slash format date to general format
	 * @author Shivaraj <shivaraj@storeking.in>_Jul_01_2014
	 * @param type $date
	 * @example 01/Jul/2014 14:00 TO 2014-07-01 14:00:00
	 * @return type String
	 */
	function date_convert($date='01/Jul/2014 14:00:00')
	{
		if($date=='' || $date=='0') return $date;
		$odate = str_replace('/', '-', $date);
		$odate = date("Y-m-d H:i:s", strtotime($odate) );
		return $odate; //
	}
}

?>