<?php
	function export_offers_data($type,$exporttype='xls')
    {
            $user = $this->erpm->auth();
            echo $this->erpm->get_insurance_id();
            die("WORK IN PROGRESS...");
        
                $user=$this->auth();
		$sql="";
                if($type == 'insurance')
                {
                    #get all insurance offers
                    $sql = "SELECT * FROM pnh_member_offers WHERE offer_type=2";
                }
                elseif($type == 'recharge')
                {
                    # Get all recharge offers
                    $sql = "SELECT * FROM pnh_member_offers WHERE offer_type=1";
                }
                elseif($type == 'opted')
                {
                    # Get all opted insurance list
                    $sql = "SELECT * FROM pnh_member_offers WHERE offer_type=3";
                }
                else
                {
                    show_error("Invalid offer type");
                }
                $this->db->query($sql);
		//$fran_acc_stat_details=$this->db->query($sql,array($frm_dt,$to_dt))->result_array();
                
                die();
                #===============================
		$objPHPExcel = new PHPExcel();
		$F=$objPHPExcel->getActiveSheet();
		$Line=2;
		$objPHPExcel->getActiveSheet()->getStyle("A1:I1")->getFont()->setBold(true);
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Franchise ID');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Franchise Name');
		$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Date');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Document Type');
		$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Document refno');
		$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Value (Rs)');
		$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Remarks');


		$objPHPExcel->getActiveSheet()->getCellByColumnAndRow(A1)->getValue();

		foreach($fran_acc_stat_details as $i=>$Trs)
		{
			if($Trs['receipt_type'] == 0 && $Trs['action_type'] == 2)
				continue ;
			$doc_status = '';	
			$doc_refno=$Trs['invoice_no']!=0?$Trs['invoice_no']:$Trs['receipt_id'] ;

			$value = $Trs['credit_amt']!=0 ? $Trs['credit_amt']:$Trs['debit_amt']*-1 ;

			
			switch($Trs['action_type'])
			{
				case 1 :
					    if($Trs['credit_amt'] > 0)
							$doc_status = 'Invoice Cancelled';
						else
							$doc_status = 'Invoice';
					break;
				case 2:
				case 3:
						if($Trs['receipt_type'] == 0)
							$doc_status = 'Deposit';
						else
							$doc_status = 'Topup';
					break;
				case 4 : 	
						$doc_status = 'Member Registration';
					break;
				case 5 : 	
						$doc_status = 'Account Correction';
					break;
				case 7 : 
						$doc_status = 'CreditNote';
						break;	
			}
			
			$F->setCellValue('A'.$Line, $Trs['franchise_id'])
				->setCellValue('B'.$Line, $Trs['franchise_name'])//write in the sheet
				->setCellValue('C'.$Line,format_datetime($Trs['created_on']))
				->setCellValue('D'.$Line, $doc_status)
				->setCellValue('E'.$Line,$doc_refno)
				->setCellValue('F'.$Line,$value)
				->setCellValue('G'.$Line, $Trs['remarks']);
			++$Line;
		}
		$today_date=$this->db->query("SELECT DATE_FORMAT(CURDATE(),'%d %b %Y') AS today_dt")->row()->today_dt;
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Franchise_account_stat.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
        
    }

		
	function reset_dealstatusbyprod()
	{
		error_reporting(E_ALL);
		ini_set('memory_limit','512M');
		ini_set('max_execution_time','6000');
		
		$user = $this->erpm->auth();
		$prod_list = $this->db->query("select product_id from m_product_info order by product_id limit 69537,30000");
		echo $prod_list->num_rows().' '; 
		$i=0;
		foreach($prod_list->result_array() as $row)
		{
			$i++;
			$this->erpm->_upd_product_deal_statusbyproduct($row['product_id'],$user['userid'],'reset by script');
			echo $i.'<br>';
			flush();
		}
	}
	
	/** CONTROLLER
	 * function to view order details for given transid
	 * @author Shivaraj <shivaraj@storeking.in>
	 */
	function order_det_by_transid()
	{
		$this->_is_validauthkey();
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('transid','Transid','required');
		
		//$this->load->model('reservation_model','reservations');
		$franchise_id=$this->input->post('franchise_id');
		$transid=$this->input->post('transid');

		
		if($this->form_validation->run() === FALSE)
		{
			$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>validation_errors()));
		}else
		{
			//$is_valid_transid=$this->db->query("select ");
			$trans_orders = $this->order_model->get_order_det_by_transid($transid);//get_orders_of_trans($transid,'all');
			
			if(!$trans_orders)
			{
				$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>"No transid found") );
			}
			else
				$this->_output_handle('json',true,array('order_details'=>$trans_orders));

		}
	}
	
	/** MODEL
     * Function to return orders of the transaction
	 * @author Shivaraj <shivaraj@storeking.in>
     * @param type $transid varchar
     * @return type array 
	 * @usage Reservations Module and View Orders of franchise API
     */
    function get_order_det_by_transid($transid=0,$default='all') // if $default==all get all orders of a transaction id
	{
			if($transid == 0)
			{
				return false;
			}
			//$transid=0,
			$field_list = "o.id as orderid,DATE_FORMAT(FROM_UNIXTIME(o.time),'%b/%d/%Y %h:%i:%s %p') AS order_date
					,o.i_orgprice AS mrp
					,o.bill_person,o.bill_address,o.bill_city,o.bill_state,o.bill_pincode,0.bill_phone
					,o.ship_person,o.ship_address,o.ship_city,o.ship_state,o.ship_pincode,o.ship_phone
					,o.status,di.name,o.quantity,concat('".IMAGES_URL."items/',di.pic,'.jpg') as image_url
					,o.i_price AS offer_price
					,((o.i_price - o.i_coup_discount)*o.quantity) as subtotal_prepaid
					#,( ( o.i_orgprice - (o.i_discount + o.i_coup_discount) ) * o.quantity) AS sub_total
					#,( o.i_price - o.i_coup_discount ) as paid_each_qty_prepaid
					#,( o.i_orgprice - o.i_coup_discount ) AS paid_each_qty
					#,( ( o.i_price - o.i_coup_discount +o.pnh_member_fee + o.insurance_amount ) * o.quantity ) AS paid
					#,o.pnh_member_fee,o.insurance_amount
					#,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value
					#,o.shipped,o.itemid,o.brandid,o.member_id,o.is_ordqty_splitd
                    #,di.pnh_id,tr.is_pnh,tr.batch_enabled
                    #,pi.p_invoice_no,i.invoice_no,tr.transid,tr.order_for";
			
            $sql="SELECT $field_list
                    FROM king_orders o
                    JOIN king_transactions tr ON tr.transid = o.transid 
                    JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
                    LEFT JOIN king_invoice i ON o.id = i.order_id AND i.invoice_status = 1
                    LEFT JOIN proforma_invoices `pi` ON pi.order_id = o.id AND pi.invoice_status = 1 
                    JOIN king_dealitems di ON di.id = o.itemid 
                    WHERE tr.transid = ? ";
			$trans_orders_res = $this->db->query($sql,$transid);
			if($trans_orders_res->num_rows() > 0)
			{
				return $trans_orders_res->row_array();
			}
			else
			{
				echo '<pre>';print_r($sql);echo '</pre>'; die();
				return false;
			}
	}
	
	
	/**
	 * Function to insert request to request table
	 * @param type $username franchise login mobile
	 * @param type $type request type
	 */
	function insert_request($username,$franchise_id,$type,$title,$desc)
	{
			$this->load->model("employee_model","employee");
			$this->load->library("email");
			$this->load->model('viakingmodel',"vkm");
		
			// get franchise det by mobile number
			$fran_det_res = $this->db->query("SELECT f.franchise_id,f.franchise_name FROM pnh_m_franchise_info f where f.login_mobile1 = ? OR franchise_id = ? ",array($username,$franchise_id) );
			if($fran_det_res->num_rows())
			{
				$fran_det = $fran_det_res->row_array();
				$franchise_id = $fran_det['franchise_id'];
				$franchise_name = $fran_det['franchise_name'];
				$time = date("Y-m-d H:i:s",time());
				$status = 1;
				
				// insert
				$in_array = array("franchise_id"=>$franchise_id,"from" => '','type'=>$type,"title"=>$title,"desc"=>$desc,"status"=>$status,"created_on"=>$time);
				$this->db->insert("pnh_franchise_requests",$in_array);
				$ticket_id = $this->db->insert_id();
				if($ticket_id)
				{
					$dept_ids = '';
					//
					$dept_ids = $this->employee->get_deptids_by_request_type($type);
					//$dept_ids='1,2';
					if(!$dept_id)
					{
						$this->_output_handle('json',false,array('error_code'=>2009,'error_msg'=>"This request not linked to any departments or Invalid request id") );
					}
					//prduct
						// SEND EMAIL/SMS
						// =============================================
						if($dept_ids != '')
						{
							$emp_det_res = $this->employee->get_emp_det_by_deptids($dept_ids);

							if($emp_det_res)
							{
								foreach($emp_det_res as $emp_det)
								{
									echo $emp_det['name'].': '.$emp_det['email'];

									$arr_emails[] = $emp_det['email'];
								}
								//send emails
								$filename = base_url()."/resources/templates/template_request_foremail.html";
								$body_msg =  file_get_contents($filename);
								$body_msg = str_replace("%%requested_on%%",date("d/M/Y H:i:s e",time()) , $body_msg);
								$body_msg = str_replace("%%requested_by%%", $franchise_name , $body_msg);
								$body_msg = str_replace("%%title%%",$title , $body_msg);
								$body_msg = str_replace("%%request_type%%",$type , $body_msg);
								$body_msg = str_replace("%%description%%",$desc , $body_msg);

								//echo $body_msg;
								$this->vkm->email($arr_emails,"Request ticket #".$ticket_id,$body_msg,true);//array($arr_emails,$payload['bill_email'],$payload['ship_email'])
								return $ticket_id;
								
							}
							else
							{
								echo 'Token '.$ticket_id.' not assigned to any employee';
							}
						}
						else
						{
							$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>"No departments assigned.") );
						}
						// =============================================
				}
				else {
					$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>"Request not submitted.") );
				}
			}
			else
			{
				$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>"Unknown Franchise." ) );
			}
	}
	
	/**
	 * Function to register a franchise complaints about our service,product,payment or insurance and return ticket number
	 * @author Shivaraj <shivaraj@storeking.in>
	 */
	function post_complaint()
	{
		//	franchise give complaint on our service,product,payment or insurance
		//	return ticketid
		$this->_is_validauthkey();
		$username = $this->input->post('username');// or i.e Username=Mobile number
		$complaint_from = $this->input->post('complaint_from'); // request from web,mobile,
		$complaint_desc = $this->input->post('complaint_desc');  // request desc
		
		// get franchise det by mobile number
		$fran_det_res = $this->db->query("SELECT franchise_id FROM pnh_m_franchise_info where login_mobile1 = ?",array($username));
		if($fran_det_res->num_rows())
		{
			$fran_det = $fran_det_res->row_array();
			$franchise_id = $fran_det['franchise_id'];
			$token_id = "TKN".time();
			
			$time = date("Y-m-d H:i:s",time());
			$status = 1;
			$in_array = array("token_id"=>$token_id,"franchise_id"=>$franchise_id,"complaint_from" => $complaint_from,"complaint_desc"=>$complaint_desc,"priority"=>'high',"status"=>$status,"created_on"=>$time);
			$this->db->insert("pnh_franchise_complaints",$in_array);
			if($this->db->insert_id())
			{
				$this->_output_handle('json',true,array("token_id"=>$token_id,"msg"=>"Complaint registered"));
			}
			else {
				$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>"Request not submitted.") );
			}
		}
		else
		{
			$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>"Franchise information not found." ) );
		}
	}
	
	/**
	 * Function to return order details by orderid
	 * @author Shivaraj <shivaraj@storeking.in>
	 * @param orderid bigint
	 */
	function order_det_by_orderid()
	{
		$this->_is_validauthkey();
		$orderid = $this->input->post('orderid');

		$this->load->library('form_validation');
		$this->form_validation->set_rules('orderid','Orderid','required');
		
		if($this->form_validation->run() === FALSE)
		{
			$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>validation_errors()));
		}else
		{
			$order_det = $this->order_model->get_order_det_by_orderid($orderid);
			if( empty($order_det) ) {
				$this->_output_handle('json',false,array('error_code'=>2000,'error_msg'=>"No orders found." ) );
			}
			else {
				$this->_output_handle('json',true,array('order_det'=>$order_det));
			}
		}
	}
	
	
	
	
	#=========================================
	define("MEM_OFFER_CREATE_DATE",'2014-01-01');
	
	$ttl_orders_res=$this->db->query("SELECT COUNT(o.transid) AS l
												FROM king_orders o
												JOIN pnh_member_info mi ON mi.user_id = o.userid
												WHERE mi.pnh_member_id=?
												AND o.status NOT IN (3) AND mi.created_on > DATE(?)
												HAVING SUM(o.i_price*o.quantity) >= ?",array($mid,MEM_CREATE_DATE,MEM_MIN_ORDER_VAL));
	#===============================
	/*$date_before = "DATE('2014-01-01')";
			$is_new_member = (!($this->db->query("SELECT COUNT(*) AS t FROM pnh_member_info mi
														JOIN pnh_member_fee fee ON fee.member_id = mi.pnh_member_id AND fee.status=1
														JOIN king_orders o ON o.transid = fee.transid AND o.status != 3
														WHERE mi.created_on > $date_before AND fee.member_id=?",$mid)->row()->t))?1:0;*/
			/*if($is_new_member) {
				$pnh_member_fee = PNH_MEMBER_FEE;
				$mem_fee_applicable = 1;
			}
			else {
				$pnh_member_fee = 0;
				$mem_fee_applicable = 0;
			} */
	
	
//					$i['sales_30days']=$this->db->query("select ifnull(sum(o.quantity*l.qty),0) as s from m_product_deal_link l join king_orders o on o.itemid=l.itemid where l.product_id=? and o.time>".(time()-(24*60*60*30)).' and o.time < ?  ',array($i['product_id'],strtotime($po['created_on'])))->row()->s;
//					$i['sales_30days'] += $this->db->query("select ifnull(sum(o.quantity*l.qty),0) as s from m_product_group_deal_link l join king_orders o on o.itemid=l.itemid join products_group_orders pgo on pgo.order_id = o.id where pgo.product_id=? and o.time>".(time()-(24*60*60*30)).' and o.time < ?  ',array($i['product_id'],strtotime($po['created_on'])))->row()->s;
//					$i['sales_60days']=$this->db->query("select ifnull(sum(o.quantity*l.qty),0) as s from m_product_deal_link l join king_orders o on o.itemid=l.itemid where l.product_id=? and o.time>".(time()-(24*60*60*60)).' and o.time < ?  ',array($i['product_id'],strtotime($po['created_on'])))->row()->s;
//					$i['sales_60days'] += $this->db->query("select ifnull(sum(o.quantity*l.qty),0) as s from m_product_group_deal_link l join king_orders o on o.itemid=l.itemid join products_group_orders pgo on pgo.order_id = o.id where pgo.product_id=? and o.time>".(time()-(24*60*60*60)).' and o.time < ?  ',array($i['product_id'],strtotime($po['created_on'])))->row()->s;

?>
							
							<tr class="inst_payment">
								<td class="">Payment For</td><td> :</td>
								<td>
									<select name="transit_type">
											<option value="0">Self Invoices</option>
<?php
											if($fran['franchise_type']=='2')
											{
?>
												<option value="1">RF Invoices</option>
<?php
											}
?>
									</select>
								</td>
							</tr>
