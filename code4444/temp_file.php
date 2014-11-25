<?php

$fran_type = $this->erpm->fran_menu_type($fran['franchise_id']);
if($fran_type['menu_type'] == 'electonics')
{
    //order menu having electronics
    $mem_msg = "Hi $membr_name, Welcome to StoreKing – Hurry up!! Get Free Insurance on the 1st Electronic* product you buy. Your Member ID is $membr_id. Offer Valid only after Registration fee Of Rs ".PNH_MEMBER_FEE."/- is paid to Storeking Franchisee.";
            echo "Hi $membr_name, Welcome to StoreKing – Hurry up!! Get Free Insurance on the 1st Electronic* product you buy. Your Member ID is $membr_id. Offer Valid only after Registration fee Of Rs ".PNH_MEMBER_FEE."/- is paid to Storeking Franchisee.";
}
else
{
    $mem_msg = "Hi $membr_name, Welcome to StoreKing – Hurry up!! Get Free Talk Time worth Rs.".PNH_MEMBER_FREE_RECHARGE." on your 1st purchase above Rs ".MEM_MIN_ORDER_VAL.". Don’t forget your Member ID is $membr_id. Please deposit Rs".PNH_MEMBER_FEE."/- Registration fee with Storeking Franchisee to avail this offer";
            echo "Hi $membr_name, Welcome to StoreKing – Hurry up!! Get Free Talk Time worth Rs.".PNH_MEMBER_FREE_RECHARGE." on your 1st purchase above Rs ".MEM_MIN_ORDER_VAL.". Don’t forget your Member ID is $membr_id. Please deposit Rs".PNH_MEMBER_FEE."/- Registration fee with Storeking Franchisee to avail this offer";
}

    /**
    * Function to return franchise menu details
    * @param type $fran_id int
    * @return string array
    * @author Shivaraj
    */
   function fran_menu_type($fran_id)
   {
       $is_fran_type_electronic = $this->erpm->_get_config_param("FRAN_TYPE_ELECTRONIC");
       $arr_frn_menus_res = $this->db->query("SELECT m.id,m.name AS menu,find_in_set(m.id,?) as status FROM `pnh_franchise_menu_link`a JOIN pnh_m_franchise_info b ON b.franchise_id=a.fid JOIN pnh_menu m ON m.id=a.menuid WHERE a.status=1 AND b.franchise_id=? ORDER BY status DESC",array($is_fran_type_electronic,$fran_id));

       if($arr_frn_menus_res->num_rows() > 0 )
       {
           $arr_frn_menus = $arr_frn_menus_res->result_array();

           // check if status is set
           if($arr_frn_menus[0]["status"])
           {
               $data =  array('status'=>"success","menus"=>$arr_frn_menus,"menu_type"=>'electonics',"menu_msg"=>"Only electronic items alloted");
           }
           else
           {
               $data =  array('status'=>"success","menus"=>$arr_frn_menus,"menu_type"=>'beauty',"menu_msg"=>"Beauty products");
           }
       }
       else
       {
           $data =  array('status'=>"error","menu_type"=>0,"menu_msg"=>"No menus");
       }

       return $data;
//            echo "<pre>"; print_r($data);
   }

   
   
   if (!function_exists('http_response_code')) {
        function http_response_code($code = NULL) {

            if ($code !== NULL) {

                switch ($code) {
                    case 100: $text = 'Continue'; break;
                    case 101: $text = 'Switching Protocols'; break;
                    case 200: $text = 'OK'; break;
                    case 201: $text = 'Created'; break;
                    case 202: $text = 'Accepted'; break;
                    case 203: $text = 'Non-Authoritative Information'; break;
                    case 204: $text = 'No Content'; break;
                    case 205: $text = 'Reset Content'; break;
                    case 206: $text = 'Partial Content'; break;
                    case 300: $text = 'Multiple Choices'; break;
                    case 301: $text = 'Moved Permanently'; break;
                    case 302: $text = 'Moved Temporarily'; break;
                    case 303: $text = 'See Other'; break;
                    case 304: $text = 'Not Modified'; break;
                    case 305: $text = 'Use Proxy'; break;
                    case 400: $text = 'Bad Request'; break;
                    case 401: $text = 'Unauthorized'; break;
                    case 402: $text = 'Payment Required'; break;
                    case 403: $text = 'Forbidden'; break;
                    case 404: $text = 'Not Found'; break;
                    case 405: $text = 'Method Not Allowed'; break;
                    case 406: $text = 'Not Acceptable'; break;
                    case 407: $text = 'Proxy Authentication Required'; break;
                    case 408: $text = 'Request Time-out'; break;
                    case 409: $text = 'Conflict'; break;
                    case 410: $text = 'Gone'; break;
                    case 411: $text = 'Length Required'; break;
                    case 412: $text = 'Precondition Failed'; break;
                    case 413: $text = 'Request Entity Too Large'; break;
                    case 414: $text = 'Request-URI Too Large'; break;
                    case 415: $text = 'Unsupported Media Type'; break;
                    case 500: $text = 'Internal Server Error'; break;
                    case 501: $text = 'Not Implemented'; break;
                    case 502: $text = 'Bad Gateway'; break;
                    case 503: $text = 'Service Unavailable'; break;
                    case 504: $text = 'Gateway Time-out'; break;
                    case 505: $text = 'HTTP Version not supported'; break;
                    default:
                        exit('Unknown http status code "' . htmlentities($code) . '"');
                    break;
                }

                $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

                header($protocol . ' ' . $code . ' ' . $text);

                $GLOBALS['http_response_code'] = $code;

            } else {

                $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);

            }

            return $code;

        }
    }
	
	
	
	
	/**
	 * Function to return deal pending order qty 
	 * @author Shivaraj <Shivaraj@storeking.in>_Sep_05_2014
	 * @param INTEGER $itemid
	 * @return INTEGER 
	 */
	function _get_deal_pending_orderqty($itemid,$type='all')
	{
		
		// Prepare condtion for order by [PNH,SNP,PARTNER] orders
		$cond = '';
		if($type == 'pnh')
			$cond = ' and a.franchise_id != 0 ';	
		else if($type == 'snp')
			$cond = ' and a.franchise_id = 0 and a.partner_id = 0 ';
		else if($type == 'part')
			$cond = ' and a.partner_id != 0 ';
		else if($type == 'all')
			$cond = ' ';
		
		return $this->db->query("SELECT SUM(pstk.qty) AS partner_pending_stk,SUM(rs.qty) AS pnh_pending_stk
									FROM t_partner_reserved_batch_stock pstk
									LEFT JOIN t_reserved_batch_stock rs ON rs.product_id=pstk.product_id AND rs.status=0
									WHERE pstk.status=0 AND pstk.itemid=? $cond 
								",$itemid)->row()->total;
	}
    
	
	
//	$s_imeino_list = '';
	$type=0;
	$st_pmsg = 'Stock Transfered To '.$product_id.' Qty '.$scan_qty.' '.($s_imeino_list?('<b>IMEI LIST </b><br>'.implode('<br>',$s_imeino_list)):'').' <br> Note:'.$transfer_remarks;
	$from_stk_id=$this->erpm->_upd_product_stock($product_id,$mrp,$bc,$loc_id,$rb_id,$stock_info_id,$scan_qty,7,$type,$transfer_id,-1,$st_pmsg);	
	$this->erpm->_upd_product_deal_statusbyproduct($product_id,$userid,$msg);
	//echo "<br>fromTEMPLOCA::::$product_id,$mrp,$bc,$loc_id,$rb_id,0,$scan_qty,0,$type,0,-1,$st_pmsg";

	
	
	$submenu['stock_transfer']=array('stk_partner_select'=>"Create Stock Transfer",'partner_transfer_list'=>"Stock Transfer Summary","partner_stk_transfer_create"=>"Partner Stock Transfer(Deprecated)");
?>
<select name="mob_nk" id="mob_nk" class="inp mand mob_nk">
    <option value="0">Select</option>
    <option value="1">BSNL</option>
    <option value="2">Idea Cellular</option>
    <option value="3">Tata Docomo</option>
    <option value="4">Reliance</option>
    <option value="5">Aircel</option>
    <option value="6">Airtel</option>
    <option value="7">Spice</option>
    <option value="8">Uninor</option>
    <option value="9">Vodaphone</option>
    <option value="10">MTS</option>
    <option value="11">Other</option>
</select>
\



/*** Text Blinker CSS ************/
@-webkit-keyframes blinker {  
  from { opacity: 1.0; }
  to { opacity: 0.0; }
}
.blink 
{
  -webkit-animation-name: blinker;  
  -webkit-animation-iteration-count: infinite;  
  -webkit-animation-timing-function: cubic-bezier(1.0,0,0,1.0);
  -webkit-animation-duration: 1s; 
}