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

?>
