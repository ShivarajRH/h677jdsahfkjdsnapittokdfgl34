<div class="container">
    <div style="float:right;">
        <!--<button onclick="pdf_export('<?=$insuranceid;?>');">Export As PDF</button>-->
        <button onclick="print_preview();">Print</button>
    </div>
<?php 
     //echo '<pre>';print_r($insurance_det); die();
    $ins = $insurance_det[0];
    
    //
    
    $item_det = $this->db->query("select di.name as dealname,di.pnh_id,d.menuid,mn.name as menuname,d.brandid,b.name as brandname,d.catid,c.name as catname from king_dealitems di
                                    join king_deals d on d.dealid=di.dealid
                                    join pnh_menu mn on mn.id=d.menuid
                                    join king_brands b on b.id = d.brandid
                                    join king_categories c on c.id = d.catid
                                    where di.id=?",$ins['itemid'])->row_array();
    
        //$ins['transid']
    $filename = base_url()."/resources/templates/template_insurance.php";
    $data =  file_get_contents($filename);
    $data = str_replace("%%itemid%%", $ins['orderid'], $data);
    $data = str_replace("%%created_on%%", date("d/m/Y",strtotime($ins['mem_receipt_date'])), $data);
    $data = str_replace("%%insured_product%%", $item_det['dealname'], $data);
    $data = str_replace("%%product_type%%", $item_det['catname'], $data);
    $data = str_replace("%%imei_serial_no%%", $ins['imei_no'], $data);
    
   $fdet = $this->db->query("
						select bill_person,bill_address,bill_city,bill_landmark,bill_state,bill_pincode,a.login_mobile1
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
    echo $data;
    ?>
<!-- %%invoice_no%% %%created_on%%-->
</div>
<script>

function print_preview() {
    $('#insurance_block_main').printElement({
        printMode:"popup"
        ,pageTitle:"View Insurance"
        ,leaveOpen:false
        /*,printBodyOptions: { styleToAdd:'padding:10px;margin:10px;color:#FFFFFF !important;',classNameToAdd : 'wrapper2'}*/
    });
    log_printcount();
}
function pdf_export(insuranceid)
{
    location.href = site_url+"/admin/insurance_aggreement_copy/"+insuranceid;
}
function log_printcount()
{
    /*var id = $("#id").val();
    $.post(site_url+'/admin/jx_update_picklist_print_log','id='+id,function(resp){ 
        if(resp.status == 'success') {
            $(".print_count_blk").html(resp.printcount+" times printed.");
        }
        else { alert(resp.response+""); }
    },'json');*/
}
</script>