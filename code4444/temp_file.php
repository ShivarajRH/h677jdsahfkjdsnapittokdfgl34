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