pnh_franchise.php
=>
<a href="javascript:void(0);" class="button button-tiny_wrap cursor button-primary clone_rows_invoice">+</a>

=>
<div id="dlg_debit_note_block">
                <h3>Reconcile the debit note</h3>
                <form id="dg_debit_note_form">
                    <table class="datagrid1" width="100%">
                        <tr><td width="150">Debit Note id #</td><th>
                                <input type="text" readonly='true' id="dg_i_debit_note_id" name="dg_i_debit_note_id" value="" size="6" class="inp"/></th></tr>
                        <tr><td width="150">Debit Amount</td><th>
                                Rs. <input type="text" readonly='true' id="dg_i_debit_amount" name="dg_i_debit_amount" value="" size="6" class="inp money"/></th></tr>
                        <tr><td width="150">Unreconcile Amount</td><th>
                                Rs. <input type="text" readonly='true' id="dg_i_unreconciled_value" name="dg_i_unreconciled_value" value="" size="6" class="inp money"/></th></tr>
                        <tr><td width="150">Reconcile Remarks</td><th>
                                <textarea id="dg_i_remarks" name="dg_i_remarks" class="textarea" style="width:193px; height: 70px;"></textarea></th></tr>
                    </table>
                </form>
            </div>

# pnh_franchise_reconcile.js
<script>
     $(".clone_rows_invoice").live("click",function() {
            
            var rtype = $("#r_type").find(":selected").val();
            if(rtype == 0) { alert("Security Deposit of type can not reconcile the Amount."); return false; }
            
            var receipt_amount = $("#receipt_amount").val();
            if( $(".error_status").html() != '' && receipt_amount != '') {
                return false;
            }
            
            var reconciled_total= parseFloat( $("abbr",".reconciled_total").html() );
            if(receipt_amount != '' && reconciled_total == receipt_amount) {
                alert("All Amount Adjusted.");
                return false;
            }
            //alert(icount);
            var html='';
            
            if(icount == 0)
                html +="<tr><th>#</th><th>Type</th><th>Document No</th><th>Un-reconcile Amount</th><th>Adjusted Amount</th><th>&nbsp;</th></tr>";
            
            icount = icount+1;
            html += "<tr class='invoice_row' id='reconcile_row_"+icount+"'>\n\
                            <td>"+icount+"</td>\n\
                            <td><select id='document_type' name='document_type[]' onchange='recon_change_document_type(this,"+icount+");'><option value='inv' selected>Invoice</option><option value='dr'>Debit Note</option></select></td>\n\
                            <td>\n\
                                <select size='2' name='sel_invoice[]' id='selected_invoices_"+icount+"' class='sel_invoices' onchange='fn_inv_selected(this,"+icount+");'>\n\
                                </select>\n\
                            </td>\n\
                            <td><input type='text' class='inp amt_unreconcile money' name='amt_unreconcile[]' id='amt_unreconcile_"+icount+"' size=6></td>\n\
                            <td><input type='text' class='inp amt_adjusted money' name='amt_adjusted[]' id='amt_adjusted_"+icount+"' size=6 value=''></td>\n\
                            <td><span class='button button-tiny_wrap cursor button-caution' onclick='remove_row("+icount+");'>-</span></td>\n\
                        </tr>";

                $("#reconcile_row").append(html);
                var invs_id = $("#selected_invoices_"+icount);
                load_unconciled_invoices(invs_id);
        });
        
        function recon_change_document_type(elt,icount,row) {
            
            var invs_id = $("#selected_invoices_"+icount, $("#"+row) );
            var document_type = $(elt).find(":selected").val(); 
            
            if(document_type == 'inv') {
                load_unconciled_invoices(invs_id);
            }
            else if(document_type == 'dr') {
                load_unconciled_debit_notes(invs_id);
            }
            
        }
    
    
    $("#dlg_debit_note_block").dialog({
        autoOpen:false
        ,modal:true
        ,width:"382"
        ,height:"317"
        ,buttons:{
            "Reconcile":function() {
                var dl_submit_reconcile_form= $("#dl_submit_reconcile_form");
                if(dl_submit_reconcile_form.parsley('validate')) {
                    $.post(site_url+"/admin/jx_debitnote_reconcile_form_submit/"+franchise_id,dl_submit_reconcile_form.serialize(),function(resp) {
                        if(resp.status=='success') {
                            //load_receipts(this,'unreconcile',0,franchise_id,100);
                            alert("Receipt reconcilation done.");
                            $("#dlg_unreconcile_form").dialog("close");
                            window.location.reload();
                            //history.go(0);window.location.href=window.location.href;
                        }
                        else {
                            print(resp.message);
                        }
                        
                    },'json');
                }
                else {
                    alert("All fields are required.");
                }
            }
            ,"Close":function() {
                $(".dg_amt_unreconcile,.dg_amt_adjusted,.dg_l_total_adjusted_val,.dg_ttl_unreconciled_after").val(0);
                $(".dg_sel_invoices").val("").trigger("liszt:updated");
                $(".dg_invoice_row").removeClass().addClass("dg_invoice_row");
                dg_icount = 1;
                $(this).dialog("close");
            }
        }
    });
    
    function reconcile_dr_amount(elt,debit_note_id,debit_amt,unreconciled_amount) {
        var dlg = $("#dg_debit_note_form");
        $("#dg_i_debit_note_id",dlg).val(debit_note_id);$("#dg_i_debit_amount",dlg).val(debit_amt);$("#dg_i_unreconciled_value",dlg).val(unreconciled_amount);

        $("#dlg_debit_note_block").dialog('open').dialog("option","title","Reconcile the Debit note id #"+debit_note_id);

        var invs_id = $("#dlg_selected_invoices_1");
        load_unconciled_invoices(invs_id);
    }
    
</script>

    jx_pnh_franchise_receipts.php
    
    <tr>
		<td><?php echo $ac_st['remarks']?></td>
		<td><?php echo $ac_st['credit_amt']?></td>
		<td><?php echo $ac_st['debit_amt']?></td>
		<td><?php echo format_datetime($ac_st['created_on'])?></td>
                <td>
                    <?php
                    if($ac_st['type'] == '1' && $ac_st['debit_amt'] > 0 ) { //Only if debit entry
                        echo '<a href="javascript:void(0);" onclick="reconcile_dr_amount(this,\''.$ac_st["acc_correc_id"].'\',\''.$ac_st["debit_amt"].'\',\''.$ac_st["unreconciled_amount"].'\')" class="button button-tiny_wrap cursor button-primary">Reconcile</a>';
                    }
                    else {
                        echo '--';
                    }?>
                </td>
	</tr>
        <?php
        # --- erp.php
        
        function jx_debitnote_reconcile_form_submit($fid) {
            
            foreach(array('dg_i_debit_note_id','dg_i_debit_amount','dg_i_unreconciled_value') as $i )
                 $$i =$this->input->post($i);

            $unreconcile_amt = $amt_unreconcile[$i];
            $adjusted_amt = $amt_adjusted[$i];
            if($invoice_no!='' && $unreconcile_amt!='' && $adjusted_amt!='') {
                 $invoice_arr['invoices'][$i]["debit_note_id"] = $sel_invoice[$i];
                 $invoice_arr['invoices'][$i]["invoice_no"] = 0;
                 $invoice_arr['invoices'][$i]["dispatch_id"] = 0;
             }
             $invoice_arr['invoices'][$i]["invoice_amt"] = $unreconcile_amt;
             $invoice_arr['invoices'][$i]["adjusted_amt"] = $adjusted_amt;
             $invoice_arr['invoices'][$i]["unreconciled_amt"] = $sub_val;
//                 }
//             }
             $invoice_arr['userid'] = $user['userid'];
             $invoice_arr['receipt_id'] = $recpt_id;
             $invoice_arr['amount']=$amount;
             $invoice_arr['total_reconcile_val'] = $total_val_reconcile;
             $invoice_arr['unreconciled_value'] = $unreconciled_value;
             $invoice_arr['fid'] = $fid;

             //echo '<pre>'; print_r($invoice_arr);die();

             $rdata = $this->erpm->reconcile_receipt($invoice_arr);
        }

 #-- erp.php - jx_pnh_franchise_reports()

}else if($type=='acct_stat')
{
        $sql="SELECT a.acc_correc_id,fcs.type,a.debit_amt,a.credit_amt,a.remarks,status,a.created_on,rcon.unreconciled,if(rcon.unreconciled is null, round( fcs.amount, 2),rcon.unreconciled) as unreconciled_amount
                                FROM `pnh_franchise_account_summary` a
                                left join pnh_franchise_account_stat fcs on fcs.id = a.acc_correc_id
                                left join pnh_t_receipt_reconcilation rcon on rcon.debit_note_id = fcs.id
                                WHERE a.franchise_id= ? and (a.action_type = 5 or a.action_type = 6)
                                order by a.created_on desc";

        $total_records=$this->db->query($sql,$fid)->num_rows;

        $sql.=" limit $pg , $limit ";

        $data['account_stat']=$this->db->query($sql,$fid)->result_array();

}
?>

        <script language="javascript" type="text/javascript">

        function ValidateComplain() {

        var cn=document.form1.cm_name;
        var cmail=document.form1.cm_mail;
        var cl=document.form1.cm_location;

        var ct=document.form1.cm_type;
        var cp=document.form1.cm_problem;
        var cm = document.form1.mail;

        if ((cn.value==null)||(cn.value=="")){
            alert("Enter Your Name");
            cn.focus();
            return false;
        }
        if (checkAddress(cn.value)==false){
            cn.value="";
            alert("Invalid Name!");
            cn.focus();
            return false;
        }
        if ((cmail.value==null)||(cmail.value=="")||(cmail.length()<1)){
            alert("Enter Mail Id");
            cmail.focus();
            return false;
        }
        if (validateEmail(cmail.value)==false){
            cmail.value="";
            alert("Invalid Mail ID!");
            cmail.focus();
            return false;
        }
        if((cl.value==null)||(cl.value==""))
        {
            alert("Enter Location");
            cl.focus();
            return false;
        }
        if (checkAddress(cl.value)==false){
            cl.value="";
            alert("Invalid Location!");
            cl.focus();
            return false;
        }


        if ((ct.value==null)||(ct.value=="")){
            alert("Enter Type ");
            ct.focus();
            return false;
        }
        if (checkAddress(ct.value)==false){
            ct.value="";
            alert("Invalid Type!");
            ct.focus();
            return false;
        }
        //cp.value.length==1
        if ((cp.value==null)||(cp.value<=1)){
            alert("Enter Problem Details");
            cp.focus();
            return false;
        }
        if (checkAddress(cp.value)==false){
            cp.value="";
            alert("Invalid Problem !");
            cp.focus();
            return false;
        }
        if ((cm.value==null)||(cm.value=="")){
            alert("Select Designation And Name");
            cm.focus();
            return false;
        }
        if (checkAddress(cm.value)==false){
            cm.value="";
            alert("Invalid Desgination Or Name!");
            cm.focus();
            return false;
        }
        else
            return true;


    }
    
function validateEmail(email)
{
    var splitted = email.match("^(.+)@(.+)$");
    if (splitted == null) return false;
    if (splitted[1] != null)
    {
        var regexp_user = /^\"?[\w-_\.]*\"?$/;
        if (splitted[1].match(regexp_user) == null) return false;
    }
    if (splitted[2] != null)
    {
        var regexp_domain = /^[\w-\.]*\.[A-Za-z]{2,4}$/;
        if (splitted[2].match(regexp_domain) == null)
        {
            var regexp_ip = /^\[\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\]$/;
            if (splitted[2].match(regexp_ip) == null) return false;
        } // if
        return true;
    }
    return false;
}

</script>