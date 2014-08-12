<div class="container">
    <div style="float:right;">
        <!--<button onclick="pdf_export('<?=$insuranceid;?>');">Export As PDF</button>-->
        <button onclick="return print_preview();">Print</button>
        <input type="hidden" name="insurance_id" id="insurance_id" value="<?=$insurance_ids;?>" />
        <div class="print_count_blk"></div>
    </div>
	<div id="insurance_list">
		<style type="text/css">
			@media print {
				thead { display: table-header-group; text-align: center; font-size: 11px;}
				tfoot { display: table-footer-group; text-align: center; font-size: 11px;}
			}
			@media screen {
				thead { display: block; text-align: center; font-size: 11px;}
				tfoot { display: block; text-align: center; font-size: 11px;}
			}
			
			table { margin: 0; padding: 0; }
		</style>
<?php

	$filename = base_url()."/resources/templates/template_insurance.html";
	$tmpl_data =  file_get_contents($filename);
		
     //echo '<pre>';print_r($insurance_det); die();
	foreach($insurance_list as $ins)
	{
		$insurance_id = $ins['sno'];
		$item_det = $this->db->query("select di.name as dealname,di.pnh_id,d.menuid,mn.name as menuname,d.brandid,b.name as brandname,d.catid,c.name as catname from king_dealitems di
										join king_deals d on d.dealid=di.dealid
										join pnh_menu mn on mn.id=d.menuid
										join king_brands b on b.id = d.brandid
										join king_categories c on c.id = d.catid
										where di.id=?",$ins['itemid'])->row_array();

			// set template data to local data variable  
			$data = $tmpl_data;
			$data = str_replace("%%itemid%%", $ins['orderid'], $data);
			$data = str_replace("%%created_on%%", date("d/m/Y",strtotime($ins['mem_receipt_date'])), $data);
			$data = str_replace("%%insured_product%%", $item_det['dealname'], $data);
			$data = str_replace("%%product_type%%", $item_det['catname'], $data);
			$data = str_replace("%%imei_serial_no%%", $ins['imei_no'], $data);

			//=====================< header-footers >============================
			$data = str_replace("%%footer_2%%", 'Insurance #'.$insurance_id, $data);
			$data = str_replace("%%footer_3%%", 'Insurance #'.$insurance_id, $data);
			$data = str_replace("%%header_4%%", 'Insurance #'.$insurance_id, $data);
			
			//=====================< header-footers >============================
			
			$fdet = $this->db->query("select bill_person,bill_address,bill_city,bill_landmark,bill_state,bill_pincode,a.login_mobile1
										from pnh_m_franchise_info a 
										join pnh_towns b on b.id = a.town_id 
										join pnh_m_territory_info c on b.territory_id = c.id 
										join king_transactions d on d.franchise_id = a.franchise_id 
										join king_orders e on e.transid = d.transid
										where a.franchise_id = ? and e.id = ? ",array($ins['fid'],$ins['orderid']))->row_array();

		   $data = str_replace("%%franchise_name%%", $fdet['bill_person'], $data);
		   $data = str_replace("%%franchise_address%%", $fdet['bill_address'], $data);
		   $data = str_replace("%%franchise_landmark%%", $fdet['bill_landmark'], $data);
		   $data = str_replace("%%franchise_city%%", $fdet['bill_city'], $data);
		   $data = str_replace("%%franchise_state%%", $fdet['bill_state'], $data);
		   $data = str_replace("%%franchise_postcode%%", $fdet['bill_pincode'], $data);
		   $data = str_replace("%%franchise_mobile%%", $fdet['login_mobile1'], $data);
		   //echo '<pre>';print_r($ins);echo '</pre>';
		   ?>
	   	<div class="insurance_block_main" style="page-break-after:always;clear:both;">
		<table>
			<thead><tr><td>Insurance #<?=$insurance_id;?></td></tr></thead>
			<tbody>
			  <tr><td>
			  <?php echo $data; ?>
			  </td></tr>
			</tbody>
			<tfoot><tr><td>
			
					<img class="pnh_logo" src="<?=IMAGES_URL?>paynearhome.png">
					<br>
					<?php echo 'LocalCube Commerce Pvt Ltd<br>Plot 3B1,KIADB Industrial Area,Kumbalagudu 1st Phase,Mysore Road,Bangalore -560074'; ?>
		
				</td></tr></tfoot>
		 </table>
		 </div>
<?php

	}
   ?>
<!-- %%invoice_no%% %%created_on%%-->
	</div>
</div>
<script>

function print_preview() {
    $('#insurance_list').printElement({
        printMode:"popup"
        ,pageTitle:"View Insurance"
        ,leaveOpen:false
        /*,printBodyOptions: { styleToAdd:'padding:10px;margin:10px;color:#FFFFFF !important;',classNameToAdd : 'wrapper2'}*/
    });
    log_printcount();
}
function pdf_export()
{
    var insurance_id = $("#insurance_id").val();
    location.href = site_url+"/admin/insurance_aggreement_copy/"+insurance_id;
    log_printcount();
}
function log_printcount()
{
	var r = confirm("Is document printed successfully?");
	if (r == true) {
		is_success = 1;
	} else {
		is_success = 0;
	}
    var insurance_id = $("#insurance_id").val();
    $.post(site_url+'/admin/insurance_print_log_update_jx',{insurance_id:insurance_id,status:is_success},function(resp){
        print(resp.result);
        if(resp.status == 'success') {
			$.each(resp.result,function(i,data) {
				$(".print_count_blk").html("Insurance#"+data.insurance_id+" printed "+data.printcount+" times.");
			});
        }
        else { alert(resp.response+""); }
    },'json');
}
</script>