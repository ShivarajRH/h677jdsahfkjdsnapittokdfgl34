<?php
/**
 * Description of Pending_functions_to_add_with_server
 *
 * @author User
 */
class Pending_functions_to_add_with_server {
    		
    function pnh_calls_log($pg=0)
    {
            $this->erpm->auth();
            $data['page']='pnh_calls_log';
            $data['pg']=$pg;
            $this->load->view("admin",$data);
    }
    function calls_fun1($p1,$p2,$c) {
        if($p1=='callsmade') {
                switch($c) {
                    case 'tofranchise': $sql='select frn.franchise_id callerid,frn.franchise_name callername,exa.from mobile,exa.callsid,exa.dialwhomno as towhom,exa.status,exa.created_on as calledtime from t_exotel_agent_status exa 
join pnh_m_franchise_info frn on frn.login_mobile1 = substr(exa.from,2) ';
                        break;
                    case 'toexecutive': $sql='select emp.employee_id callerid,emp.name callername,exa.from mobile,exa.callsid,exa.dialwhomno as towhom,exa.status,exa.created_on as calledtime from t_exotel_agent_status exa 
join m_employee_info emp on emp.contact_no = substr(exa.from,2) ';
                        break;
                    case 'tounknown': $sql='select emp.employee_id callerid,emp.name callername,exa.from mobile,exa.callsid,exa.dialwhomno as towhom,exa.status,exa.created_on as calledtime from t_exotel_agent_status exa
LEFT join m_employee_info emp on emp.contact_no = substr(exa.from,2)
LEFT join pnh_m_franchise_info frn on frn.login_mobile1 = substr(exa.from,2)
WHERE emp.employee_id IS NOT NULL and emp.name IS NOT NULL ';
                        break;
                    default:$this->json_error_show("Invalid input. <br>$p1,$p2,$c,$pg");
                        break;
                }
                return $sql;
        }
        elseif($p1=='receivedcalls') {
                switch($c) {
                    case 'tofranchise': $sql='select frn.franchise_id callerid,frn.franchise_name callername,exa.from mobile,exa.callsid,exa.dialwhomno as towhom,exa.status,exa.created_on as calledtime from t_exotel_agent_status exa 
join pnh_m_franchise_info frn on frn.login_mobile1 = substr(exa.dialwhomno,2) ';
                        break;
                    case 'toexecutive': $sql='select emp.employee_id callerid,emp.name callername,exa.from mobile,exa.callsid,exa.dialwhomno as towhom,exa.status,exa.created_on as calledtime from t_exotel_agent_status exa 
join m_employee_info emp on emp.contact_no = substr(exa.dialwhomno,2) ';
                        break;
                    case 'tounknown': $sql='select emp.employee_id callerid,emp.name callername,exa.from mobile,exa.callsid,exa.dialwhomno as towhom,exa.status,exa.created_on as calledtime from t_exotel_agent_status exa
LEFT join m_employee_info emp on emp.contact_no = substr(exa.dialwhomno,2)
LEFT join pnh_m_franchise_info frn on frn.login_mobile1 = substr(exa.dialwhomno,2)
WHERE emp.employee_id IS NULL OR emp.name IS NULL ';
                        break;
                    default:$this->json_error_show("Invalid input. <br>$p1,$p2,$c,$pg");
                        break;
                }
        }
        else {
            $this->json_error_show("1. Invalid input. <br>$p1,$p2,$c,$pg");
        }
        return $sql;
    }
    function json_error_show($string) {
        echo json_encode(array("status"=> "fail","response" => $string)); 
        die();
    }
    /**
     * Ajax function to load pnh calls log details by type and territory
     * @param unknown_type $p1 (parent 1)
     * @param unknown_type $p2 (parent 2)
     * @param unknown_type $c (Child)
     * @param unknown_type $pg (page)
     */
    function jx_getpnh_calls_log($p1,$p2,$c,$pg=0)
    {
        //$this->json_error_show("$p1,$p2,$c,$pg");
        $presql='';
        $limit = 25;
        $tbl_total_rows=0;
//                    if($p1=='callsmade') {
                //$presql=" join m_employee_info emp on emp.contact_no = substr(exa.from,2) ".$presql;

                $presql.=$this->calls_fun1($p1,$p2,$c);
                if($p2=='all_calls') {
                    $presql.=' ';

                }
                elseif($p2=='busy_calls') {
                    $presql.=' and exa.status="busy" ';

                }
                elseif($p2=='attended_calls') {
                    $presql.=' and exa.status="free" ';

                }
                else $this->json_error_show("2. Invalid input. <br>$p1,$p2,$c,$pg");

                $sql_total = $presql;

                //$this->json_error_show("$sql_total");

                $tbl_total_rows = $this->db->query($sql_total)->num_rows();

                $sql = $sql_total." order by calledtime DESC limit $pg,$limit";


                    $log_calls_details_res=$this->db->query($sql);

                    $tbl_head = array('slno'=>'Slno','callerid'=>'Caller ID','callername'=>'Caller Name','mobile'=>'Mobile Num.','callsid'=>'Calls ID','towhom'=>'To Whom','status'=>'Status','calledtime'=>'Called Time');

                    if($log_calls_details_res->num_rows())
                    {
                            foreach($log_calls_details_res->result_array() as $i=>$log_det)
                            {
                                    $tbl_data[] = array('slno'=>$i+1,
                                        'callerid'=>$log_det['callerid'],
                                        'callername'=> ($log_det['callername']!='') ? anchor('admin/view_employee/'.$log_det['callerid'],$log_det['callername']) : '',
                                        'mobile'=>$log_det['mobile'],
                                        'callsid'=>$log_det['callsid'],
                                        'towhom'=>$log_det['towhom'],
                                        'status'=>$log_det['status'],
                                        'calledtime'=>$log_det['calledtime']);
                            }
                    }

            //$this->json_error_show(wordwrap($sql,70,'<br>')."<br>$tbl_total_rows<br>$p1,$p2,$c,$pg");
            if(count($tbl_data)) {
                    $tbl_data_html = '<div class="dash_bar" id="dash_bar">Showing <strong>'.($pg+1).'</strong> to <strong>'.($pg+1*$limit).'</strong> of <strong>'.$tbl_total_rows.'</strong></div>';
                    $tbl_data_html .= '<table cellpadding="5" cellspacing="0" class="datagrid datagridsort">';
                    $tbl_data_html .= '<thead>';
                    foreach($tbl_head as $th)
                            $tbl_data_html .= '<th>'.$th.'</th>';

                    $tbl_data_html .= '</thead>';
                    $i = $pg;
                    $tbl_data_html .= '<tbody>';
                    foreach($tbl_data as $tdata)
                    {
                            $tbl_data_html .= '<tr>';
                            foreach(array_keys($tbl_head) as $th_i)
                            {
                                    if($th_i == 'slno')
                                            $tdata[$th_i] = $i+1;

                                    $tbl_data_html .= '	<td>'.$tdata[$th_i].'</td>';
                            }
                            $tbl_data_html .= '</tr>';

                            $i = $i+1;
                    }
                    $tbl_data_html .= '</tbody>';
                    $tbl_data_html .= '</table>';


                    $this->load->library('pagination');

                    $config['base_url'] = site_url('admin/jx_getpnh_calls_log/'.$p1.'/'.$p2.'/'.$c);
                    $config['total_rows'] = $tbl_total_rows;
                    $config['per_page'] = $limit;
                    $config['uri_segment'] = 6;

                    $this->config->set_item('enable_query_strings',false);
                    $this->pagination->initialize($config);
                    $pagi_links = $this->pagination->create_links();
                    $this->config->set_item('enable_query_strings',true);

                    $pagi_links = '<div class="log_pagination">'.$pagi_links.'</div>
                                        ';

                    echo json_encode(array('status'=>"success",'log_data'=>$tbl_data_html,'tbl_total_rows'=> $tbl_total_rows,'limit'=>(($pg+1)*$limit),'newpg'=>($pg+1),'pagi_links'=>$pagi_links,'p1'=>$p1,'p2'=>$p2,
                        'c'=>$c,'pg'=>$pg,'items_info'=>""));

            }
            else {
                    $tbl_data_html = '<div align="center"> No data found</div>';
                    echo json_encode(array('status'=>"fail",'response'=>$tbl_data_html,'tbl_total_rows'=>$tbl_total_rows,'limit'=>$limit,'pagi_links'=>'','p1'=>'','p2'=>'','c'=>'','pg'=>0,'items_info'=>"Showing <strong>0</strong>"));
            }
    }
    //END PNH Calls log files
    
    function addproduct()
    {
            $user=$this->auth(PRODUCT_MANAGER_ROLE);
            $input_fields=array('pname',"pdesc","psize","puom","pmrp","pvat","pcost","pbarcode","pisoffer","pissrc","pbrand","prackbin","pmoq","prorder","prqty","premarks","pissno");

            $this->form_validation->set_rules('pname', 'Product Name', 'required');
            $this->form_validation->set_rules('pdesc', 'Product Description', 'required');

// 		$this->form_validation->set_rules('psize', 'Size', 'required');
// 		$this->form_validation->set_rules('puom', 'Unit of Measurment', 'required');
// 		$this->form_validation->set_rules('pmrp', 'MRP', 'required');
// 		$this->form_validation->set_rules('pvat', 'VAT', 'required');
// 		$this->form_validation->set_rules('pcost', 'Purchase Cost', 'required');
// 		$this->form_validation->set_rules('pbarcode', 'Barcode', 'required');
// 		$this->form_validation->set_rules('pisoffer', 'Is Offer', 'required');
// 		$this->form_validation->set_rules('pissrc', 'Is sourceable', 'required');
// 		$this->form_validation->set_rules('pbrand', 'Brand', 'required');
// 		$this->form_validation->set_rules('prackbin', 'prackbin', 'required');
// 		$this->form_validation->set_rules('pmoq', 'MOQ', 'required');
// 		$this->form_validation->set_rules('prorder', 'Reorder Level', 'required');
// 		$this->form_validation->set_rules('prqty', 'Reorder Qty', 'required');
// 		$this->form_validation->set_rules('premarks', 'Remarks', 'required');
// 		$this->form_validation->set_rules('pissno', 'Is Active', 'required');

            if ($this->form_validation->run() == FALSE) {
                    //ERRORS
            } 
            else {// No errors
                            $inp=array("P".rand(10000,99999));
                            foreach($input_fields as $i) 
                                    $inp[]=$this->input->post($i);

                            $inp[] = $user['userid'];	
                            $this->db->query("insert into m_product_info(product_code,product_name,short_desc,size,uom,mrp,vat,purchase_cost,barcode,is_offer,is_sourceable,brand_id,default_rackbin_id,moq,reorder_level,reorder_qty,remarks,is_serial_required,created_on,created_by)
                                                                                                                                                                            values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,now(),?)",$inp);
                            $pid=$this->db->insert_id();
                            $rackbin=0;$location=0;
                            $raw_rackbin=$this->db->query("select l.location_id as default_location_id,l.id as default_rack_bin_id from m_rack_bin_brand_link b join m_rack_bin_info l on l.id=b.rack_bin_id where b.brandid=?",$this->input->post("pbrand"))->row_array();
                            if(!empty($raw_rackbin))
                            {
                                    $rackbin=$raw_rackbin['default_rack_bin_id'];
                                    $location=$raw_rackbin['default_location_id'];
                            }
                            $this->db->query("insert into t_stock_info(product_id,location_id,rack_bin_id,mrp,available_qty,product_barcode) values(?,?,?,?,0,'')",array($pid,$location,$rackbin,$pmrp));
                            redirect("admin/products");
            }
            $data['page']="addproduct";
            $this->load->view("admin",$data);
    }
    
    function products($pg=0)
	{
                $user=$this->auth(PRODUCT_MANAGER_ROLE);
		$data['page']="products";
		$this->load->view("admin",$data);
	}
        function jx_products($pg=0) {
            //print_r($_POST); die();
            $user=$this->auth(PRODUCT_MANAGER_ROLE);
		$data['products']=$this->erpm->getproducts();
                
                $output='';
                $limit=30;
               
                //$sql="select sum(s.available_qty) as stock,p.*,b.name as brand from m_product_info p join king_brands b on b.id=p.brand_id left outer join t_stock_info s on s.product_id=p.product_id group by p.product_id order by p.product_id desc ";
                if($_POST['idname'] != '' or $_POST['classname'] != '') {
                    $idname = $_POST['idname'];
                    $classes = explode(' ', trim($_POST['classname']));
                    
                    $classname=$classes[1];
                    
                    switch($idname) {
                        case 'th_pname': 
                            if($classname == 'headerSortDown') { 
                                $oderby=" p.product_name desc "; 
                            }
                            else { 
                                $oderby=" p.product_name ASC ";  
                            }
                            break;
                        case 'th_mrp' : 
                            if($classname == 'headerSortDown') { 
                                $oderby=" s.mrp ASC "; 
                            }   
                            else {   
                                $oderby=" s.mrp desc "; 
                            }
                            break;
                        case 'th_stock': 
                            if($classname == 'headerSortDown') { 
                                $oderby=" s.available_qty ASC "; 
                            }   
                            else {   
                                $oderby=" s.available_qty desc "; 
                            }
                            break;
                        case 'th_barcode': 
                            if($classname == 'headerSortDown') { 
                                $oderby=" s.barcode ASC "; 
                            }   
                            else {   
                                $oderby=" s.barcode desc "; 
                            }
                            break;
                        case 'th_brand' : 
                            if($classname == 'headerSortDown') { 
                                $oderby=" b.name ASC "; 
                            }   
                            else {   
                                $oderby=" b.name desc "; 
                            }
                            break;
                        default :
                            $oderby=" p.product_id desc ";
                            break;
                    }
                } else { $oderby=" p.product_id desc ";  }
                
               
                $sql="select sum(s.available_qty) as stock,p.*,b.name as brand from m_product_info p join king_brands b on b.id=p.brand_id left outer join t_stock_info s on s.product_id=p.product_id group by p.product_id order by".$oderby;
               
                //echo $output.= "<br>".$oderby."</br>"; die();
                
		$total_products=$this->db->query($sql)->num_rows();
		$sql.=" limit $pg , 30 ";
		//$data['products']=
                $products=$this->db->query($sql)->result_array();
		
                //echo json_encode($data['products']); die();
		
                //pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url("admin/jx_products");
		$config['total_rows'] = $total_products;
		$config['per_page'] = $limit;
		$config['uri_segment'] = 3;
		$config['num_links'] = 5;
                $config['enable_query_strings']=false;
                
                $config['full_tag_open'] = '<div class="link"><strong>';
	        $config['full_tag_close'] = '</strong></div>';
                
                $this->pagination->initialize($config);
		$pagination = $data['pagination'] = $this->pagination->create_links();
                $config['enable_query_strings']=true;
		//pagination end//
               
                

                foreach($products as $p){
                    $style=$p['is_sourceable'] ? "#aaffaa" : "#ffaaaa;";
                     
                    $output.= '<tr style="background:'.$style.'">
                    <td><input type="checkbox" value="'.$p['product_id'].'" class="p_check"></td>
                    <td><a class="link" href="'.site_url("admin/product/{$p['product_id']}").'">'.$p['product_name'].'</a></td>
                    <td>'.$p['mrp'].'</td>
                    <td>'.$p['stock'].'</td>
                    <td>
                            <img src="'.IMAGES_URL.'loading_maroon.gif" class="busy">
                        <form action="'.site_url("admin/update_barcode").'" method="post" class="barcode_forms">
                            <input type="hidden" name="pid" value="'.$p['product_id'].'">
                            <input type="text" class="barcode_inp" name="barcode" value="'.(string)$p['barcode'].'" size=10>
                        </form>
                    </td>
                    <td>'.$p['brand'].'</td>
                    <td>
                    <a href="'.site_url("admin/editproduct/{$p['product_id']}").'">edit</a> &nbsp;&nbsp;&nbsp;&nbsp; 
                    <a href="'.site_url("admin/viewlinkeddeals/{$p['product_id']}").'">view linked deals</a>
                    </td>
                    </tr>';
                }
                
                $output.= '<tr>
                        <td colspan="8" align="left" class="pagination">'.$pagination.' <div class="loading">&nbsp;</div></td>
                </tr>
                
                ';
                echo $output;
                
        }
        
        
//	function jx_get_group_attibutes($limit=100)
//	{
//		$user=$this->auth();
//                $product_id=$this->input->post("product_id");
//                $group_id=$this->input->post("group_id");
//                $result= $this->db->query("select pgp.id,pgp.group_id,pgp.product_id,pga.attribute_name_id as name_id,pga.attribute_name as name_val,pgav.attribute_value_id as att_val_id,pgav.attribute_value as att_value from products_group_pids as pgp  
//                        join products_group_attributes pga on pga.group_id=pgp.group_id
//                        join products_group_attribute_values pgav on pgav.group_id=pgp.group_id
//                        where pgp.group_id in (?)
//                        group by att_val_id",$group_id)->result_array();
//                
//                if(in_array($product_id,$result)) {
//                    $res['status']='fail';
//                    $res['result'] ="Product id already exists.";
//                }
//                else {
//                    $res['status']='success';
//                    $res['result'] =$result;
//                }
//                //$res['query']=$this->db->last_query(); 
//                
//                echo json_encode($res);
//        }

        //STOCK UPDATE CODE
       /**
        * function to get product current stock from table
        */
       function _get_product_stock($product_id = 0)
       {
           return @$this->db->query("select sum(available_qty) as t from t_stock_info where product_id = ? and available_qty >= 0 ",$product_id)->row()->t;
       }
       
       function chk_bc_assign()
        {
           //$prod_id=0,$mrp=0,$bc='',$loc_id=0,$rb_id=0,$p_stk_id=0,$qty=0,$update_by=0,$stk_movtype=0,$update_by_refid=0,$mrp_change_updated=-1,$msg=''
        if($this->erpm->_upd_product_stock(2360,8000,'8806085354760',1,1,0,3,1,1,12312))
        {
        echo 'ok';
        }else
        {
        echo 'failed';
        }

        echo $this->_get_product_stock(8698);
        }
       //END STOCK UPDATE CODE
}

?>
<script>
    var obj = {};

$.getJSON("displayjson.php",function (data) {
    $.each(data.news, function (i, news) {
        obj[news.title] = news.link;
    });                      
});

// later:
$.each(obj, function (index, value) {
    alert( index + ' : ' + value );
});
In JavaScript, objects fulfill the role of associative arrays. Be aware that objects do not have a defined "sort order" when iterating them (see below).

However, In your case it is not really clear to me why you transfer data from the original object (data.news) at all. Why do you not simply pass a reference to that object around?

You can combine objects and arrays to achieve predictable iteration and key/value behavior:

var arr = [];

$.getJSON("displayjson.php",function (data) {
    $.each(data.news, function (i, news) {
        arr.push({
            title: news.title, 
            link:  news.link
        });
    });                      
});

// later:
$.each(arr, function (index, value) {
    alert( value.title + ' : ' + value.link );
});
</script>

                    <select>
                            <option timeZoneId="1" gmtAdjustment="GMT-12:00" useDaylightTime="0" value="-12">(GMT-12:00) International Date Line West</option>
                            <option timeZoneId="2" gmtAdjustment="GMT-11:00" useDaylightTime="0" value="-11">(GMT-11:00) Midway Island, Samoa</option>
                            <option timeZoneId="3" gmtAdjustment="GMT-10:00" useDaylightTime="0" value="-10">(GMT-10:00) Hawaii</option>
                            <option timeZoneId="4" gmtAdjustment="GMT-09:00" useDaylightTime="1" value="-9">(GMT-09:00) Alaska</option>
                            <option timeZoneId="5" gmtAdjustment="GMT-08:00" useDaylightTime="1" value="-8">(GMT-08:00) Pacific Time (US & Canada)</option>
                            <option timeZoneId="6" gmtAdjustment="GMT-08:00" useDaylightTime="1" value="-8">(GMT-08:00) Tijuana, Baja California</option>
                            <option timeZoneId="7" gmtAdjustment="GMT-07:00" useDaylightTime="0" value="-7">(GMT-07:00) Arizona</option>
                            <option timeZoneId="8" gmtAdjustment="GMT-07:00" useDaylightTime="1" value="-7">(GMT-07:00) Chihuahua, La Paz, Mazatlan</option>
                            <option timeZoneId="9" gmtAdjustment="GMT-07:00" useDaylightTime="1" value="-7">(GMT-07:00) Mountain Time (US & Canada)</option>
                            <option timeZoneId="10" gmtAdjustment="GMT-06:00" useDaylightTime="0" value="-6">(GMT-06:00) Central America</option>
                            <option timeZoneId="11" gmtAdjustment="GMT-06:00" useDaylightTime="1" value="-6">(GMT-06:00) Central Time (US & Canada)</option>
                            <option timeZoneId="12" gmtAdjustment="GMT-06:00" useDaylightTime="1" value="-6">(GMT-06:00) Guadalajara, Mexico City, Monterrey</option>
                            <option timeZoneId="13" gmtAdjustment="GMT-06:00" useDaylightTime="0" value="-6">(GMT-06:00) Saskatchewan</option>
                            <option timeZoneId="14" gmtAdjustment="GMT-05:00" useDaylightTime="0" value="-5">(GMT-05:00) Bogota, Lima, Quito, Rio Branco</option>
                            <option timeZoneId="15" gmtAdjustment="GMT-05:00" useDaylightTime="1" value="-5">(GMT-05:00) Eastern Time (US & Canada)</option>
                            <option timeZoneId="16" gmtAdjustment="GMT-05:00" useDaylightTime="1" value="-5">(GMT-05:00) Indiana (East)</option>
                            <option timeZoneId="17" gmtAdjustment="GMT-04:00" useDaylightTime="1" value="-4">(GMT-04:00) Atlantic Time (Canada)</option>
                            <option timeZoneId="18" gmtAdjustment="GMT-04:00" useDaylightTime="0" value="-4">(GMT-04:00) Caracas, La Paz</option>
                            <option timeZoneId="19" gmtAdjustment="GMT-04:00" useDaylightTime="0" value="-4">(GMT-04:00) Manaus</option>
                            <option timeZoneId="20" gmtAdjustment="GMT-04:00" useDaylightTime="1" value="-4">(GMT-04:00) Santiago</option>
                            <option timeZoneId="21" gmtAdjustment="GMT-03:30" useDaylightTime="1" value="-3.5">(GMT-03:30) Newfoundland</option>
                            <option timeZoneId="22" gmtAdjustment="GMT-03:00" useDaylightTime="1" value="-3">(GMT-03:00) Brasilia</option>
                            <option timeZoneId="23" gmtAdjustment="GMT-03:00" useDaylightTime="0" value="-3">(GMT-03:00) Buenos Aires, Georgetown</option>
                            <option timeZoneId="24" gmtAdjustment="GMT-03:00" useDaylightTime="1" value="-3">(GMT-03:00) Greenland</option>
                            <option timeZoneId="25" gmtAdjustment="GMT-03:00" useDaylightTime="1" value="-3">(GMT-03:00) Montevideo</option>
                            <option timeZoneId="26" gmtAdjustment="GMT-02:00" useDaylightTime="1" value="-2">(GMT-02:00) Mid-Atlantic</option>
                            <option timeZoneId="27" gmtAdjustment="GMT-01:00" useDaylightTime="0" value="-1">(GMT-01:00) Cape Verde Is.</option>
                            <option timeZoneId="28" gmtAdjustment="GMT-01:00" useDaylightTime="1" value="-1">(GMT-01:00) Azores</option>
                            <option timeZoneId="29" gmtAdjustment="GMT+00:00" useDaylightTime="0" value="0">(GMT+00:00) Casablanca, Monrovia, Reykjavik</option>
                            <option timeZoneId="30" gmtAdjustment="GMT+00:00" useDaylightTime="1" value="0">(GMT+00:00) Greenwich Mean Time : Dublin, Edinburgh, Lisbon, London</option>
                            <option timeZoneId="31" gmtAdjustment="GMT+01:00" useDaylightTime="1" value="1">(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option>
                            <option timeZoneId="32" gmtAdjustment="GMT+01:00" useDaylightTime="1" value="1">(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague</option>
                            <option timeZoneId="33" gmtAdjustment="GMT+01:00" useDaylightTime="1" value="1">(GMT+01:00) Brussels, Copenhagen, Madrid, Paris</option>
                            <option timeZoneId="34" gmtAdjustment="GMT+01:00" useDaylightTime="1" value="1">(GMT+01:00) Sarajevo, Skopje, Warsaw, Zagreb</option>
                            <option timeZoneId="35" gmtAdjustment="GMT+01:00" useDaylightTime="1" value="1">(GMT+01:00) West Central Africa</option>
                            <option timeZoneId="36" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="2">(GMT+02:00) Amman</option>
                            <option timeZoneId="37" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="2">(GMT+02:00) Athens, Bucharest, Istanbul</option>
                            <option timeZoneId="38" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="2">(GMT+02:00) Beirut</option>
                            <option timeZoneId="39" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="2">(GMT+02:00) Cairo</option>
                            <option timeZoneId="40" gmtAdjustment="GMT+02:00" useDaylightTime="0" value="2">(GMT+02:00) Harare, Pretoria</option>
                            <option timeZoneId="41" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="2">(GMT+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius</option>
                            <option timeZoneId="42" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="2">(GMT+02:00) Jerusalem</option>
                            <option timeZoneId="43" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="2">(GMT+02:00) Minsk</option>
                            <option timeZoneId="44" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="2">(GMT+02:00) Windhoek</option>
                            <option timeZoneId="45" gmtAdjustment="GMT+03:00" useDaylightTime="0" value="3">(GMT+03:00) Kuwait, Riyadh, Baghdad</option>
                            <option timeZoneId="46" gmtAdjustment="GMT+03:00" useDaylightTime="1" value="3">(GMT+03:00) Moscow, St. Petersburg, Volgograd</option>
                            <option timeZoneId="47" gmtAdjustment="GMT+03:00" useDaylightTime="0" value="3">(GMT+03:00) Nairobi</option>
                            <option timeZoneId="48" gmtAdjustment="GMT+03:00" useDaylightTime="0" value="3">(GMT+03:00) Tbilisi</option>
                            <option timeZoneId="49" gmtAdjustment="GMT+03:30" useDaylightTime="1" value="3.5">(GMT+03:30) Tehran</option>
                            <option timeZoneId="50" gmtAdjustment="GMT+04:00" useDaylightTime="0" value="4">(GMT+04:00) Abu Dhabi, Muscat</option>
                            <option timeZoneId="51" gmtAdjustment="GMT+04:00" useDaylightTime="1" value="4">(GMT+04:00) Baku</option>
                            <option timeZoneId="52" gmtAdjustment="GMT+04:00" useDaylightTime="1" value="4">(GMT+04:00) Yerevan</option>
                            <option timeZoneId="53" gmtAdjustment="GMT+04:30" useDaylightTime="0" value="4.5">(GMT+04:30) Kabul</option>
                            <option timeZoneId="54" gmtAdjustment="GMT+05:00" useDaylightTime="1" value="5">(GMT+05:00) Yekaterinburg</option>
                            <option timeZoneId="55" gmtAdjustment="GMT+05:00" useDaylightTime="0" value="5">(GMT+05:00) Islamabad, Karachi, Tashkent</option>
                            <option timeZoneId="56" gmtAdjustment="GMT+05:30" useDaylightTime="0" value="5.5">(GMT+05:30) Sri Jayawardenapura</option>
                            <option timeZoneId="57" gmtAdjustment="GMT+05:30" useDaylightTime="0" value="5.5">(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi</option>
                            <option timeZoneId="58" gmtAdjustment="GMT+05:45" useDaylightTime="0" value="5.75">(GMT+05:45) Kathmandu</option>
                            <option timeZoneId="59" gmtAdjustment="GMT+06:00" useDaylightTime="1" value="6">(GMT+06:00) Almaty, Novosibirsk</option>
                            <option timeZoneId="60" gmtAdjustment="GMT+06:00" useDaylightTime="0" value="6">(GMT+06:00) Astana, Dhaka</option>
                            <option timeZoneId="61" gmtAdjustment="GMT+06:30" useDaylightTime="0" value="6.5">(GMT+06:30) Yangon (Rangoon)</option>
                            <option timeZoneId="62" gmtAdjustment="GMT+07:00" useDaylightTime="0" value="7">(GMT+07:00) Bangkok, Hanoi, Jakarta</option>
                            <option timeZoneId="63" gmtAdjustment="GMT+07:00" useDaylightTime="1" value="7">(GMT+07:00) Krasnoyarsk</option>
                            <option timeZoneId="64" gmtAdjustment="GMT+08:00" useDaylightTime="0" value="8">(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi</option>
                            <option timeZoneId="65" gmtAdjustment="GMT+08:00" useDaylightTime="0" value="8">(GMT+08:00) Kuala Lumpur, Singapore</option>
                            <option timeZoneId="66" gmtAdjustment="GMT+08:00" useDaylightTime="0" value="8">(GMT+08:00) Irkutsk, Ulaan Bataar</option>
                            <option timeZoneId="67" gmtAdjustment="GMT+08:00" useDaylightTime="0" value="8">(GMT+08:00) Perth</option>
                            <option timeZoneId="68" gmtAdjustment="GMT+08:00" useDaylightTime="0" value="8">(GMT+08:00) Taipei</option>
                            <option timeZoneId="69" gmtAdjustment="GMT+09:00" useDaylightTime="0" value="9">(GMT+09:00) Osaka, Sapporo, Tokyo</option>
                            <option timeZoneId="70" gmtAdjustment="GMT+09:00" useDaylightTime="0" value="9">(GMT+09:00) Seoul</option>
                            <option timeZoneId="71" gmtAdjustment="GMT+09:00" useDaylightTime="1" value="9">(GMT+09:00) Yakutsk</option>
                            <option timeZoneId="72" gmtAdjustment="GMT+09:30" useDaylightTime="0" value="9.5">(GMT+09:30) Adelaide</option>
                            <option timeZoneId="73" gmtAdjustment="GMT+09:30" useDaylightTime="0" value="9.5">(GMT+09:30) Darwin</option>
                            <option timeZoneId="74" gmtAdjustment="GMT+10:00" useDaylightTime="0" value="10">(GMT+10:00) Brisbane</option>
                            <option timeZoneId="75" gmtAdjustment="GMT+10:00" useDaylightTime="1" value="10">(GMT+10:00) Canberra, Melbourne, Sydney</option>
                            <option timeZoneId="76" gmtAdjustment="GMT+10:00" useDaylightTime="1" value="10">(GMT+10:00) Hobart</option>
                            <option timeZoneId="77" gmtAdjustment="GMT+10:00" useDaylightTime="0" value="10">(GMT+10:00) Guam, Port Moresby</option>
                            <option timeZoneId="78" gmtAdjustment="GMT+10:00" useDaylightTime="1" value="10">(GMT+10:00) Vladivostok</option>
                            <option timeZoneId="79" gmtAdjustment="GMT+11:00" useDaylightTime="1" value="11">(GMT+11:00) Magadan, Solomon Is., New Caledonia</option>
                            <option timeZoneId="80" gmtAdjustment="GMT+12:00" useDaylightTime="1" value="12">(GMT+12:00) Auckland, Wellington</option>
                            <option timeZoneId="81" gmtAdjustment="GMT+12:00" useDaylightTime="0" value="12">(GMT+12:00) Fiji, Kamchatka, Marshall Is.</option>
                            <option timeZoneId="82" gmtAdjustment="GMT+13:00" useDaylightTime="0" value="13">(GMT+13:00) Nuku'alofa</option>
                    </select>											


#ISO-3366-1: Alpha-2 Codes

<select>
	<option value="AF">Afghanistan</option>
	<option value="AX">Åland Islands</option>
	<option value="AL">Albania</option>
	<option value="DZ">Algeria</option>
	<option value="AS">American Samoa</option>
	<option value="AD">Andorra</option>
	<option value="AO">Angola</option>
	<option value="AI">Anguilla</option>
	<option value="AQ">Antarctica</option>
	<option value="AG">Antigua and Barbuda</option>
	<option value="AR">Argentina</option>
	<option value="AM">Armenia</option>
	<option value="AW">Aruba</option>
	<option value="AU">Australia</option>
	<option value="AT">Austria</option>
	<option value="AZ">Azerbaijan</option>
	<option value="BS">Bahamas</option>
	<option value="BH">Bahrain</option>
	<option value="BD">Bangladesh</option>
	<option value="BB">Barbados</option>
	<option value="BY">Belarus</option>
	<option value="BE">Belgium</option>
	<option value="BZ">Belize</option>
	<option value="BJ">Benin</option>
	<option value="BM">Bermuda</option>
	<option value="BT">Bhutan</option>
	<option value="BO">Bolivia, Plurinational State of</option>
	<option value="BQ">Bonaire, Sint Eustatius and Saba</option>
	<option value="BA">Bosnia and Herzegovina</option>
	<option value="BW">Botswana</option>
	<option value="BV">Bouvet Island</option>
	<option value="BR">Brazil</option>
	<option value="IO">British Indian Ocean Territory</option>
	<option value="BN">Brunei Darussalam</option>
	<option value="BG">Bulgaria</option>
	<option value="BF">Burkina Faso</option>
	<option value="BI">Burundi</option>
	<option value="KH">Cambodia</option>
	<option value="CM">Cameroon</option>
	<option value="CA">Canada</option>
	<option value="CV">Cape Verde</option>
	<option value="KY">Cayman Islands</option>
	<option value="CF">Central African Republic</option>
	<option value="TD">Chad</option>
	<option value="CL">Chile</option>
	<option value="CN">China</option>
	<option value="CX">Christmas Island</option>
	<option value="CC">Cocos (Keeling) Islands</option>
	<option value="CO">Colombia</option>
	<option value="KM">Comoros</option>
	<option value="CG">Congo</option>
	<option value="CD">Congo, the Democratic Republic of the</option>
	<option value="CK">Cook Islands</option>
	<option value="CR">Costa Rica</option>
	<option value="CI">Côte d'Ivoire</option>
	<option value="HR">Croatia</option>
	<option value="CU">Cuba</option>
	<option value="CW">Curaçao</option>
	<option value="CY">Cyprus</option>
	<option value="CZ">Czech Republic</option>
	<option value="DK">Denmark</option>
	<option value="DJ">Djibouti</option>
	<option value="DM">Dominica</option>
	<option value="DO">Dominican Republic</option>
	<option value="EC">Ecuador</option>
	<option value="EG">Egypt</option>
	<option value="SV">El Salvador</option>
	<option value="GQ">Equatorial Guinea</option>
	<option value="ER">Eritrea</option>
	<option value="EE">Estonia</option>
	<option value="ET">Ethiopia</option>
	<option value="FK">Falkland Islands (Malvinas)</option>
	<option value="FO">Faroe Islands</option>
	<option value="FJ">Fiji</option>
	<option value="FI">Finland</option>
	<option value="FR">France</option>
	<option value="GF">French Guiana</option>
	<option value="PF">French Polynesia</option>
	<option value="TF">French Southern Territories</option>
	<option value="GA">Gabon</option>
	<option value="GM">Gambia</option>
	<option value="GE">Georgia</option>
	<option value="DE">Germany</option>
	<option value="GH">Ghana</option>
	<option value="GI">Gibraltar</option>
	<option value="GR">Greece</option>
	<option value="GL">Greenland</option>
	<option value="GD">Grenada</option>
	<option value="GP">Guadeloupe</option>
	<option value="GU">Guam</option>
	<option value="GT">Guatemala</option>
	<option value="GG">Guernsey</option>
	<option value="GN">Guinea</option>
	<option value="GW">Guinea-Bissau</option>
	<option value="GY">Guyana</option>
	<option value="HT">Haiti</option>
	<option value="HM">Heard Island and McDonald Islands</option>
	<option value="VA">Holy See (Vatican City State)</option>
	<option value="HN">Honduras</option>
	<option value="HK">Hong Kong</option>
	<option value="HU">Hungary</option>
	<option value="IS">Iceland</option>
	<option value="IN">India</option>
	<option value="ID">Indonesia</option>
	<option value="IR">Iran, Islamic Republic of</option>
	<option value="IQ">Iraq</option>
	<option value="IE">Ireland</option>
	<option value="IM">Isle of Man</option>
	<option value="IL">Israel</option>
	<option value="IT">Italy</option>
	<option value="JM">Jamaica</option>
	<option value="JP">Japan</option>
	<option value="JE">Jersey</option>
	<option value="JO">Jordan</option>
	<option value="KZ">Kazakhstan</option>
	<option value="KE">Kenya</option>
	<option value="KI">Kiribati</option>
	<option value="KP">Korea, Democratic People's Republic of</option>
	<option value="KR">Korea, Republic of</option>
	<option value="KW">Kuwait</option>
	<option value="KG">Kyrgyzstan</option>
	<option value="LA">Lao People's Democratic Republic</option>
	<option value="LV">Latvia</option>
	<option value="LB">Lebanon</option>
	<option value="LS">Lesotho</option>
	<option value="LR">Liberia</option>
	<option value="LY">Libya</option>
	<option value="LI">Liechtenstein</option>
	<option value="LT">Lithuania</option>
	<option value="LU">Luxembourg</option>
	<option value="MO">Macao</option>
	<option value="MK">Macedonia, the former Yugoslav Republic of</option>
	<option value="MG">Madagascar</option>
	<option value="MW">Malawi</option>
	<option value="MY">Malaysia</option>
	<option value="MV">Maldives</option>
	<option value="ML">Mali</option>
	<option value="MT">Malta</option>
	<option value="MH">Marshall Islands</option>
	<option value="MQ">Martinique</option>
	<option value="MR">Mauritania</option>
	<option value="MU">Mauritius</option>
	<option value="YT">Mayotte</option>
	<option value="MX">Mexico</option>
	<option value="FM">Micronesia, Federated States of</option>
	<option value="MD">Moldova, Republic of</option>
	<option value="MC">Monaco</option>
	<option value="MN">Mongolia</option>
	<option value="ME">Montenegro</option>
	<option value="MS">Montserrat</option>
	<option value="MA">Morocco</option>
	<option value="MZ">Mozambique</option>
	<option value="MM">Myanmar</option>
	<option value="NA">Namibia</option>
	<option value="NR">Nauru</option>
	<option value="NP">Nepal</option>
	<option value="NL">Netherlands</option>
	<option value="NC">New Caledonia</option>
	<option value="NZ">New Zealand</option>
	<option value="NI">Nicaragua</option>
	<option value="NE">Niger</option>
	<option value="NG">Nigeria</option>
	<option value="NU">Niue</option>
	<option value="NF">Norfolk Island</option>
	<option value="MP">Northern Mariana Islands</option>
	<option value="NO">Norway</option>
	<option value="OM">Oman</option>
	<option value="PK">Pakistan</option>
	<option value="PW">Palau</option>
	<option value="PS">Palestinian Territory, Occupied</option>
	<option value="PA">Panama</option>
	<option value="PG">Papua New Guinea</option>
	<option value="PY">Paraguay</option>
	<option value="PE">Peru</option>
	<option value="PH">Philippines</option>
	<option value="PN">Pitcairn</option>
	<option value="PL">Poland</option>
	<option value="PT">Portugal</option>
	<option value="PR">Puerto Rico</option>
	<option value="QA">Qatar</option>
	<option value="RE">Réunion</option>
	<option value="RO">Romania</option>
	<option value="RU">Russian Federation</option>
	<option value="RW">Rwanda</option>
	<option value="BL">Saint Barthélemy</option>
	<option value="SH">Saint Helena, Ascension and Tristan da Cunha</option>
	<option value="KN">Saint Kitts and Nevis</option>
	<option value="LC">Saint Lucia</option>
	<option value="MF">Saint Martin (French part)</option>
	<option value="PM">Saint Pierre and Miquelon</option>
	<option value="VC">Saint Vincent and the Grenadines</option>
	<option value="WS">Samoa</option>
	<option value="SM">San Marino</option>
	<option value="ST">Sao Tome and Principe</option>
	<option value="SA">Saudi Arabia</option>
	<option value="SN">Senegal</option>
	<option value="RS">Serbia</option>
	<option value="SC">Seychelles</option>
	<option value="SL">Sierra Leone</option>
	<option value="SG">Singapore</option>
	<option value="SX">Sint Maarten (Dutch part)</option>
	<option value="SK">Slovakia</option>
	<option value="SI">Slovenia</option>
	<option value="SB">Solomon Islands</option>
	<option value="SO">Somalia</option>
	<option value="ZA">South Africa</option>
	<option value="GS">South Georgia and the South Sandwich Islands</option>
	<option value="SS">South Sudan</option>
	<option value="ES">Spain</option>
	<option value="LK">Sri Lanka</option>
	<option value="SD">Sudan</option>
	<option value="SR">Suriname</option>
	<option value="SJ">Svalbard and Jan Mayen</option>
	<option value="SZ">Swaziland</option>
	<option value="SE">Sweden</option>
	<option value="CH">Switzerland</option>
	<option value="SY">Syrian Arab Republic</option>
	<option value="TW">Taiwan, Province of China</option>
	<option value="TJ">Tajikistan</option>
	<option value="TZ">Tanzania, United Republic of</option>
	<option value="TH">Thailand</option>
	<option value="TL">Timor-Leste</option>
	<option value="TG">Togo</option>
	<option value="TK">Tokelau</option>
	<option value="TO">Tonga</option>
	<option value="TT">Trinidad and Tobago</option>
	<option value="TN">Tunisia</option>
	<option value="TR">Turkey</option>
	<option value="TM">Turkmenistan</option>
	<option value="TC">Turks and Caicos Islands</option>
	<option value="TV">Tuvalu</option>
	<option value="UG">Uganda</option>
	<option value="UA">Ukraine</option>
	<option value="AE">United Arab Emirates</option>
	<option value="GB">United Kingdom</option>
	<option value="US">United States</option>
	<option value="UM">United States Minor Outlying Islands</option>
	<option value="UY">Uruguay</option>
	<option value="UZ">Uzbekistan</option>
	<option value="VU">Vanuatu</option>
	<option value="VE">Venezuela, Bolivarian Republic of</option>
	<option value="VN">Viet Nam</option>
	<option value="VG">Virgin Islands, British</option>
	<option value="VI">Virgin Islands, U.S.</option>
	<option value="WF">Wallis and Futuna</option>
	<option value="EH">Western Sahara</option>
	<option value="YE">Yemen</option>
	<option value="ZM">Zambia</option>
	<option value="ZW">Zimbabwe</option>
</select>

# ISO-3366-1: Numeric Codes
<select>
	<option value="4">Afghanistan</option>
	<option value="248">Åland Islands</option>
	<option value="8">Albania</option>
	<option value="12">Algeria</option>
	<option value="16">American Samoa</option>
	<option value="20">Andorra</option>
	<option value="24">Angola</option>
	<option value="660">Anguilla</option>
	<option value="10">Antarctica</option>
	<option value="28">Antigua and Barbuda</option>
	<option value="32">Argentina</option>
	<option value="51">Armenia</option>
	<option value="533">Aruba</option>
	<option value="36">Australia</option>
	<option value="40">Austria</option>
	<option value="31">Azerbaijan</option>
	<option value="44">Bahamas</option>
	<option value="48">Bahrain</option>
	<option value="50">Bangladesh</option>
	<option value="52">Barbados</option>
	<option value="112">Belarus</option>
	<option value="56">Belgium</option>
	<option value="84">Belize</option>
	<option value="204">Benin</option>
	<option value="60">Bermuda</option>
	<option value="64">Bhutan</option>
	<option value="68">Bolivia, Plurinational State of</option>
	<option value="535">Bonaire, Sint Eustatius and Saba</option>
	<option value="70">Bosnia and Herzegovina</option>
	<option value="72">Botswana</option>
	<option value="74">Bouvet Island</option>
	<option value="76">Brazil</option>
	<option value="86">British Indian Ocean Territory</option>
	<option value="96">Brunei Darussalam</option>
	<option value="100">Bulgaria</option>
	<option value="854">Burkina Faso</option>
	<option value="108">Burundi</option>
	<option value="116">Cambodia</option>
	<option value="120">Cameroon</option>
	<option value="124">Canada</option>
	<option value="132">Cape Verde</option>
	<option value="136">Cayman Islands</option>
	<option value="140">Central African Republic</option>
	<option value="148">Chad</option>
	<option value="152">Chile</option>
	<option value="156">China</option>
	<option value="162">Christmas Island</option>
	<option value="166">Cocos (Keeling) Islands</option>
	<option value="170">Colombia</option>
	<option value="174">Comoros</option>
	<option value="178">Congo</option>
	<option value="180">Congo, the Democratic Republic of the</option>
	<option value="184">Cook Islands</option>
	<option value="188">Costa Rica</option>
	<option value="384">Côte d'Ivoire</option>
	<option value="191">Croatia</option>
	<option value="192">Cuba</option>
	<option value="531">Curaçao</option>
	<option value="196">Cyprus</option>
	<option value="203">Czech Republic</option>
	<option value="208">Denmark</option>
	<option value="262">Djibouti</option>
	<option value="212">Dominica</option>
	<option value="214">Dominican Republic</option>
	<option value="218">Ecuador</option>
	<option value="818">Egypt</option>
	<option value="222">El Salvador</option>
	<option value="226">Equatorial Guinea</option>
	<option value="232">Eritrea</option>
	<option value="233">Estonia</option>
	<option value="231">Ethiopia</option>
	<option value="238">Falkland Islands (Malvinas)</option>
	<option value="234">Faroe Islands</option>
	<option value="242">Fiji</option>
	<option value="246">Finland</option>
	<option value="250">France</option>
	<option value="254">French Guiana</option>
	<option value="258">French Polynesia</option>
	<option value="260">French Southern Territories</option>
	<option value="266">Gabon</option>
	<option value="270">Gambia</option>
	<option value="268">Georgia</option>
	<option value="276">Germany</option>
	<option value="288">Ghana</option>
	<option value="292">Gibraltar</option>
	<option value="300">Greece</option>
	<option value="304">Greenland</option>
	<option value="308">Grenada</option>
	<option value="312">Guadeloupe</option>
	<option value="316">Guam</option>
	<option value="320">Guatemala</option>
	<option value="831">Guernsey</option>
	<option value="324">Guinea</option>
	<option value="624">Guinea-Bissau</option>
	<option value="328">Guyana</option>
	<option value="332">Haiti</option>
	<option value="334">Heard Island and McDonald Islands</option>
	<option value="336">Holy See (Vatican City State)</option>
	<option value="340">Honduras</option>
	<option value="344">Hong Kong</option>
	<option value="348">Hungary</option>
	<option value="352">Iceland</option>
	<option value="356">India</option>
	<option value="360">Indonesia</option>
	<option value="364">Iran, Islamic Republic of</option>
	<option value="368">Iraq</option>
	<option value="372">Ireland</option>
	<option value="833">Isle of Man</option>
	<option value="376">Israel</option>
	<option value="380">Italy</option>
	<option value="388">Jamaica</option>
	<option value="392">Japan</option>
	<option value="832">Jersey</option>
	<option value="400">Jordan</option>
	<option value="398">Kazakhstan</option>
	<option value="404">Kenya</option>
	<option value="296">Kiribati</option>
	<option value="408">Korea, Democratic People's Republic of</option>
	<option value="410">Korea, Republic of</option>
	<option value="414">Kuwait</option>
	<option value="417">Kyrgyzstan</option>
	<option value="418">Lao People's Democratic Republic</option>
	<option value="428">Latvia</option>
	<option value="422">Lebanon</option>
	<option value="426">Lesotho</option>
	<option value="430">Liberia</option>
	<option value="434">Libya</option>
	<option value="438">Liechtenstein</option>
	<option value="440">Lithuania</option>
	<option value="442">Luxembourg</option>
	<option value="446">Macao</option>
	<option value="807">Macedonia, the former Yugoslav Republic of</option>
	<option value="450">Madagascar</option>
	<option value="454">Malawi</option>
	<option value="458">Malaysia</option>
	<option value="462">Maldives</option>
	<option value="466">Mali</option>
	<option value="470">Malta</option>
	<option value="584">Marshall Islands</option>
	<option value="474">Martinique</option>
	<option value="478">Mauritania</option>
	<option value="480">Mauritius</option>
	<option value="175">Mayotte</option>
	<option value="484">Mexico</option>
	<option value="583">Micronesia, Federated States of</option>
	<option value="498">Moldova, Republic of</option>
	<option value="492">Monaco</option>
	<option value="496">Mongolia</option>
	<option value="499">Montenegro</option>
	<option value="500">Montserrat</option>
	<option value="504">Morocco</option>
	<option value="508">Mozambique</option>
	<option value="104">Myanmar</option>
	<option value="516">Namibia</option>
	<option value="520">Nauru</option>
	<option value="524">Nepal</option>
	<option value="528">Netherlands</option>
	<option value="540">New Caledonia</option>
	<option value="554">New Zealand</option>
	<option value="558">Nicaragua</option>
	<option value="562">Niger</option>
	<option value="566">Nigeria</option>
	<option value="570">Niue</option>
	<option value="574">Norfolk Island</option>
	<option value="580">Northern Mariana Islands</option>
	<option value="578">Norway</option>
	<option value="512">Oman</option>
	<option value="586">Pakistan</option>
	<option value="585">Palau</option>
	<option value="275">Palestinian Territory, Occupied</option>
	<option value="591">Panama</option>
	<option value="598">Papua New Guinea</option>
	<option value="600">Paraguay</option>
	<option value="604">Peru</option>
	<option value="608">Philippines</option>
	<option value="612">Pitcairn</option>
	<option value="616">Poland</option>
	<option value="620">Portugal</option>
	<option value="630">Puerto Rico</option>
	<option value="634">Qatar</option>
	<option value="638">Réunion</option>
	<option value="642">Romania</option>
	<option value="643">Russian Federation</option>
	<option value="646">Rwanda</option>
	<option value="652">Saint Barthélemy</option>
	<option value="654">Saint Helena, Ascension and Tristan da Cunha</option>
	<option value="659">Saint Kitts and Nevis</option>
	<option value="662">Saint Lucia</option>
	<option value="663">Saint Martin (French part)</option>
	<option value="666">Saint Pierre and Miquelon</option>
	<option value="670">Saint Vincent and the Grenadines</option>
	<option value="882">Samoa</option>
	<option value="674">San Marino</option>
	<option value="678">Sao Tome and Principe</option>
	<option value="682">Saudi Arabia</option>
	<option value="686">Senegal</option>
	<option value="688">Serbia</option>
	<option value="690">Seychelles</option>
	<option value="694">Sierra Leone</option>
	<option value="702">Singapore</option>
	<option value="534">Sint Maarten (Dutch part)</option>
	<option value="703">Slovakia</option>
	<option value="705">Slovenia</option>
	<option value="90">Solomon Islands</option>
	<option value="706">Somalia</option>
	<option value="710">South Africa</option>
	<option value="239">South Georgia and the South Sandwich Islands</option>
	<option value="728">South Sudan</option>
	<option value="724">Spain</option>
	<option value="144">Sri Lanka</option>
	<option value="729">Sudan</option>
	<option value="740">Suriname</option>
	<option value="744">Svalbard and Jan Mayen</option>
	<option value="748">Swaziland</option>
	<option value="752">Sweden</option>
	<option value="756">Switzerland</option>
	<option value="760">Syrian Arab Republic</option>
	<option value="158">Taiwan, Province of China</option>
	<option value="762">Tajikistan</option>
	<option value="834">Tanzania, United Republic of</option>
	<option value="764">Thailand</option>
	<option value="626">Timor-Leste</option>
	<option value="768">Togo</option>
	<option value="772">Tokelau</option>
	<option value="776">Tonga</option>
	<option value="780">Trinidad and Tobago</option>
	<option value="788">Tunisia</option>
	<option value="792">Turkey</option>
	<option value="795">Turkmenistan</option>
	<option value="796">Turks and Caicos Islands</option>
	<option value="798">Tuvalu</option>
	<option value="800">Uganda</option>
	<option value="804">Ukraine</option>
	<option value="784">United Arab Emirates</option>
	<option value="826">United Kingdom</option>
	<option value="840">United States</option>
	<option value="581">United States Minor Outlying Islands</option>
	<option value="858">Uruguay</option>
	<option value="860">Uzbekistan</option>
	<option value="548">Vanuatu</option>
	<option value="862">Venezuela, Bolivarian Republic of</option>
	<option value="704">Viet Nam</option>
	<option value="92">Virgin Islands, British</option>
	<option value="850">Virgin Islands, U.S.</option>
	<option value="876">Wallis and Futuna</option>
	<option value="732">Western Sahara</option>
	<option value="887">Yemen</option>
	<option value="894">Zambia</option>
	<option value="716">Zimbabwe</option>
</select>

//web lab,sycopaint,mysms,text,fluid UI, talk to messenger, want

//This prototype function allows you to remove even array from array
                Array.prototype.remove = function(x) { 
                    for(i in this){
                        if(this[i].toString() == x.toString()){
                            this.splice(i,1)
                        }
                    }
                }
                var arr = [1,2,[1,1], 'abc'];
                arr.remove([1,1]);
                console.log(arr) //[1, 2, 'abc']

                var arr = [1,2,[1,1], 'abc'];
                arr.remove(1);
                console.log(arr) //[2, [1,1], 'abc']

                var arr = [1,2,[1,1], 'abc'];
                arr.remove('abc');
                console.log(arr) //[1, 2, [1,1]]
                
                
                
                