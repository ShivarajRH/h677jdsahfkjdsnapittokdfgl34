<div class="container">
    <div style="float:right;">
        <button onclick="pdf_export('<?=$insuranceid;?>');">Export As PDF</button>
        <button onclick="print_preview();">Print</button>
    </div>
<?php 
     //echo '<pre>';print_r($insurance_det); die();
    $ins = $insurance_det[0];
    
    $filename=base_url()."resources/templates/template_insurance.php";
    $data =  file_get_contents($filename);
    $data = str_replace("%%invoice_no%%", $ins['invoice_no'], $data);
    $data = str_replace("%%created_on%%", date("d/m/Y",strtotime($ins['created_on'])), $data);
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