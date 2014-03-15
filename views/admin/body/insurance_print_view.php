<div class="container">
    
    <h2>View Insurance</h2>
    
    <div align="right">
        <button onclick="print_preview();">Print</button>
    </div>
    
    <style>
    table{
            font-size:12px;
    }
    .showinprint{
                    display: none;
    }

    @media print {
            .hideinprint{
                    display:none;
                    visibility: hidden;
            }
            .showinprint{
                    display: block;
            }
    }
 

    </style>
    <div id="insurance_block" class="insurance_block" style="padding: 10px; page-break-after: always;">
        <div style="font-family:arial;font-size:12px;">
            <div align="right">
                <table>
                    <tr>
                        <td>Date:</td><td>13/03/2014</td>
                    </tr>
                </table> 
            </div>
            
            <table width="100%" cellspacing="0" cellpadding="5" border="1">
                <tr>
                    <td>Memberid</td>
                    <td>6663246</td>
                </tr>
                <tr>
                    <td>Insurance To</td>
                    <td>Nokia 411</td>
                </tr>
                <tr>
                    <td>Address</td>
                    <td>Localcube, kumbalodu industrial estate, kengeri hobali, Bangalore</td>
                </tr>
                <tr>
                    <td>Transid</td>
                    <td>PNH4375643</td>
                </tr>
                <tr>
                    <td>Menu Name</td>
                    <td>Mobile & Tablets</td>
                </tr>
                <tr>
                    <td>Brand Name</td>
                    <td>Nokia</td>
                </tr>
                    <td>Insurance Value</td>
                    <td>Rs. 50</td>
                </tr>

            </table>
        </div>
        
        <div class="block signature_default" style="">
                <br>
                <span style="margin:22px 0px 0px;float:right;"><b>Validated By</b> : _______________<br /></span><br />
                <span style="margin:7px 0px;float:left;;"><b>Processed By</b> : _____________________<br /></span>
        </div>
        <p> &nbsp;</p>
        
    </div>
    
</div>
<script>

function print_preview() {
    $('#insurance_block').printElement({
        printMode:"popup"
        ,pageTitle:"View Insurance"
        ,leaveOpen:false
        /*,printBodyOptions: { styleToAdd:'padding:10px;margin:10px;color:#FFFFFF !important;',classNameToAdd : 'wrapper2'}*/
    });
    log_printcount();
}

function log_printcount()
{
    /*var batch_id = $("#batch_id").val();
    $.post(site_url+'/admin/jx_update_picklist_print_log','batch_id='+batch_id,function(resp){ 
        if(resp.status == 'success') {
            $(".print_count_blk").html(resp.printcount+" times printed.");
        }
        else {
            alert(resp.response+"");
        }
    },'json');*/
}
</script>