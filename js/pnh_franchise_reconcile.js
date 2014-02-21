
/* $("#top_form") code
var sts = validate_selected_invoice_val();
if(sts !== true) {
    error_msgs.push(sts);
}
var reconciled_total= format_number( $("abbr",".reconciled_total").html() );
$("#total_val_reconcile").val(reconciled_total);
if(error_msgs.length)
{
        alert("Errors:\n"+error_msgs.join("\n"));
        return false;
}
*/
             
/** RECONCILE Code Block 1**/

        function validate_selected_invoice_val() {
            var err_status=true;
            $(".amt_unreconcile").each(function(i,row){
                if($(row).val() != '') { // selected invoice
                    var addi_val = $(".amt_adjusted:eq("+i+")");
                    if(addi_val.val() == '') { //additional value is empty
                        //$(".error_status").html("Please enter additional amount!");
                        return err_status = "Please specify adjusted amount for selected invoices";
                        addi_val.focus();
                    }
                }
            });
            return err_status;
        }
        
        var icount=0;
        
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
                            <td><select id='document_type' name='document_type[]' onchange=\"recon_change_document_type(this,'selected_invoices_"+icount+"','reconcile_row_"+icount+"');\"><option value='inv' selected>Invoice</option><option value='dr'>Debit Note</option></select></td>\n\
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
        
        function recon_change_document_type(elt,sel_invoices,row_name) {
            var row = $("#"+row_name);
            var invs_id = $("#"+sel_invoices,row); //, $("#"+row)
            var document_type = $(elt).find(":selected").val(); 
            
            if(document_type == 'inv') {
                load_unconciled_invoices(invs_id);
            }
            else if(document_type == 'dr') {
                load_unconciled_debit_notes(invs_id);
            }
            
        }
        
        function load_unconciled_invoices(invs_sel) {
            //on click
            var invs = "<option value='00'>Choose</option>\n";
            $.post(site_url+"/admin/jx_get_unreconciled_invoice_list/"+franchise_id,{},function(resp) {
                    if(resp.fran_invoices.length) {
                        $.each(resp.fran_invoices,function(i,invoice) {
                            invs += "<option value='"+invoice.invoice_no+"' inv_amount='"+invoice.inv_amount+"'>"+invoice.invoice_no+" (Rs."+invoice.inv_amount+") </option>\n";
                        });
                    }
                    else 
                        alert(resp);
                    invs_sel.html(invs);
                    invs_sel.chosen();
                    invs_sel.trigger('liszt:updated');
            },'json');
        }
        
        function load_unconciled_debit_notes(invs_sel) {
            
            //on click
            var invs = "<option value='00'>Choose</option>\n";
            $.post(site_url+"/admin/jx_get_unreconciled_debit_notes_list/"+franchise_id,{},function(resp) {
                    if(resp.fran_debit_notes.length) {
                        $.each(resp.fran_debit_notes,function(i,invoice) {
                            invs += "<option value='"+invoice.debit_note_id+"' inv_amount='"+invoice.inv_amount+"'>"+invoice.debit_note_id+" (Dr. Rs."+invoice.amount+") </option>\n";
                        });
                    }
                    else 
                        alert(resp);
                    invs_sel.html(invs);
                    invs_sel.chosen();
                    invs_sel.trigger('liszt:updated');
            },'json');
        }
        
    var arr_invs = [];
    function remove_row(rowid) {
        var tbody=$("#reconcile_row");
        update_values(rowid);
        $("#reconcile_row_"+rowid,tbody).remove();

    }
    
    function update_values(rowid) {
        //remove invoice
        //subtract ajusted amount
        var sel_inv = $("#selected_invoices_"+rowid).find(':selected').val();
        if(arr_invs.length)
            arr_invs.remove(sel_inv);
//            else $("#reconcile_row").html("");

        icount = icount - 1;
        if(icount == 0) {
            $("#reconcile_row").html("");
        }
        var amt_adjusted = $("#amt_adjusted_"+rowid).val();
        var reconciled_total= parseFloat( $("abbr",".reconciled_total").html() );
        var sub_amount = reconciled_total - amt_adjusted;
        
        sub_amount = sub_amount ? sub_amount : 0
        $("abbr",".reconciled_total").html(sub_amount);

        if(rowid == 0)
            $("#reconcile_row").parent().html("");
    }
        
    function fn_inv_selected(elt,count) {

        var receipt_amount = $("#receipt_amount").val();
        var error_status = $(".error_status");
        var sel_invoice_dropbox = $("#selected_invoices_"+count);
        var reconciled_total= parseFloat( $("abbr",".reconciled_total").html() );
        var sel_inv = $(elt).find(':selected').val();
        var sel_inv_amount = parseFloat( $(elt).find(':selected').attr("inv_amount") );
        var invoice_row = $("#reconcile_row_"+count);
        
        error_status.html("");
        if(receipt_amount == 0) {
            error_status.html("Please specify receipt amount"); $("#receipt_amount:first:visible").focus(); return false;
        }
        
                if($(".invoice_row").hasClass("inv_"+sel_inv)) {//if row having inv_{invid} class then the invoice already selected
                    sel_invoice_dropbox.val("").trigger("liszt:updated");
                    error_status.html("Already this invoice selected"); return false;
               }
               else {
                    invoice_row.removeClass();
                    invoice_row.addClass("invoice_row inv_"+sel_inv);
                    arr_invs.push(sel_inv);
               }
           
        //if(reconciled_total < receipt_amount) {

            var i_sub_total = sel_inv_amount + reconciled_total;
            if(i_sub_total < receipt_amount) {
                if(count) {
                    $("#amt_unreconcile_"+count).val(sel_inv_amount);
                    $("#amt_adjusted_"+count).val(sel_inv_amount);
                }
                /*else {$("#amt_unreconcile").val(sel_inv_amount);$("#amt_adjusted").val(sel_inv_amount);}*/
            }
            else {
                var add_inv_btn = false;
                var i_sub_total = receipt_amount - reconciled_total;
                $("#amt_unreconcile_"+count).val(sel_inv_amount);
                $("#amt_adjusted_"+count).val(i_sub_total);
                //alert("Invoice amount cannot be more than the receipt amount!");
            }
            $(".amt_adjusted").trigger("change");
            //} else { alert("Invoice amount cannot be more than the receipt!"); return false; }
    }
        
        
    $("#receipt_amount").keyup(function() {
       $(".error_status").html(""); 
    });

    $(".amt_adjusted").live("change",function() {
        show_unconcile_total();
    });
        
    function show_unconcile_total() {
        var invs_total = 0;
        $(".amt_adjusted").each(function(i,row) {
            var amount = $(this).val();
            if(amount!='') {
                //print( parseFloat(amount ) );
                invs_total += parseFloat(amount);
            }
        });

        $("abbr",".reconciled_total").html( format_number ( invs_total ) );
    }

    function myInArray(needle, haystack) {
        return $.inArray(needle, haystack) !== -1;
    }

/** RECONCILE Code Block 2**/

    $("#dlg_unreconcile_view_list").dialog({
        modal:true,
	autoOpen:false,
	width:600,
	height:506,
	autoResize:true
        ,buttons:{
            "Close":function() {
                $(this).dialog("close");
            }
        }
    });
    
    function clk_view_reconciled(elt,receipt_id,franchise_id) {
        var recon_list='';
        $.post(site_url+"/admin/jx_get_fran_reconcile_list/"+receipt_id+"/"+franchise_id,{},function(resp) {
            if(resp.status == 'success') {
                recon_list += "<h3>View reconciled list for Receipt #"+receipt_id+"</h3>\n\
                                <table class='datagrid1'>\n\
                                    <tr><td>Receipt #</td><th>"+resp.receipt_det.receipt_id+"</th></tr>\n\
                                    <tr><td>Receipt Amount</td><th>Rs. "+format_number(resp.receipt_det.receipt_amount)+"</th></tr>\n\
                                    <tr><td>Un reconciled Amount</td><th>Rs. "+format_number(resp.receipt_det.unreconciled_value)+" </th></tr>\n\
                                    <tr><td>Created On</td><th>"+resp.receipt_det.created_date+"</th></tr></table>";
                recon_list += "<br><table width='100%' class='datagrid'><tr><th>#</th><th>Invoice No</th><th>Invoice Amount</th><th>Reconciled Value</th><th>Unreconciled Amount</th><th>Created By</th><th>Created On</th></tr>";
                $.each(resp.reconcile_list,function(i,recon) {
                    recon_list += "<tr><td>"+(++i)+"</td><td>"+recon.invoice_no+"</td><td>"+recon.inv_amount+"</td><td>"+recon.reconcile_amount+"</td><td>"+recon.unreconciled+"</td><td>"+recon.username+"</td><td>"+recon.created_date+"</td>";
                });
                recon_list += "</table>";
            }
            else if(resp.status == 'fail') {
                recon_list += resp.response;
            }
            else {
                alert(resp);return false;
            }
            $("#dlg_unreconcile_view_list").html(recon_list).dialog('open').dialog("option","title","Reconciled list of Receipt #"+receipt_id);
        },'json');
    }
    
    var dg_icount = 1;
    $("#dlg_unreconcile_form").dialog({
        modal:true,
	autoOpen:false,
	width:"600",
	height:"630",
	autoResize:true
        ,buttons:{
            "Reconcile":function() {
                var dl_submit_reconcile_form= $("#dl_submit_reconcile_form");
                if(dl_submit_reconcile_form.parsley('validate')) {
                    $.post(site_url+"/admin/jx_dl_submit_reconcile_form/"+franchise_id,dl_submit_reconcile_form.serialize(),function(resp) {
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
                var dlg = $(this);
                //$("#dl_submit_reconcile_form").clearForm();
                $(".dg_amt_unreconcile,.dg_amt_adjusted,.dg_l_total_adjusted_val,.dg_ttl_unreconciled_after",dlg).val(0);
                $(".dg_sel_invoices",dlg).val("").trigger("liszt:updated");
                $(".dg_invoice_row",dlg).removeClass().addClass("dg_invoice_row");
                dg_icount = 1;
                $(this).dialog("close");
            }
        }
    });
    
    function clk_reconcile_action(elt,receipt_id,franchise_id,receipt_amount,unreconciled_value) {
            // set data
            var dlg = $("#dlg_unreconcile_form");
            $("#dg_i_receipt_id",dlg).val(receipt_id);$("#dg_i_receipt_amount",dlg).val(receipt_amount);$("#dg_i_unreconciled_value",dlg).val(unreconciled_value);
            dlg.dialog('open').dialog("option","title","Reconcile the Receipt #"+receipt_id);
            var invs_id = $("#dlg_selected_invoices_1");
            load_unconciled_invoices(invs_id);
    }
    
    function dg_add_invoice_row(elt,row_name,rowparent,dlgname) {
            var dlg = $("#"+dlgname);
            dg_icount = dg_icount + 1;
            var rowname = row_name+"_"+dg_icount;
            var dg_rowname = $("#"+row_name);
            var dg_rowparent = $("."+rowparent);
            
            var recon_list = '';
            
            var dg_i_unreconciled_value = format_number( $("#dg_i_unreconciled_value",dlg).val() );
            var dg_l_total_adjusted_val= format_number( $(".dg_l_total_adjusted_val",dlg).val() );
            
            if( dg_i_unreconciled_value == dg_l_total_adjusted_val ) { // if unredconciled and adjusted amount is same no more adjustments
                alert("All amount adjusted."); return false;
            }
            //alert(dg_icount);
            recon_list += "<tr id='"+rowname+"' class='dg_invoice_row'>\n\
                            <td>"+dg_icount+"</td>\n\
                            <td><select id='document_type' name='document_type[]' onchange=\"dg_recon_change_document_type(this,'dlg_selected_invoices_"+dg_icount+"','"+rowname+"','"+rowparent+"','"+dlgname+"');\"><option value='inv' selected>Invoice</option><option value='dr'>Debit Note</option></select></td>\n\
                            <td>\n\
                                <select size='2' name='sel_invoice[]' id='dlg_selected_invoices_"+dg_icount+"' class='dg_sel_invoices' onchange=\"dg_fn_inv_selected(this,'"+dg_icount+"','"+rowname+"','"+rowparent+"','"+dlgname+"');\"></select>\n\
                            </td>\n\
                            <td><input type='text' readonly='true' class='inp dg_amt_unreconcile money' name='amt_unreconcile[]' id='dg_amt_unreconcile_"+dg_icount+"' size=6></td>\n\
                            <td><input type='text' class='inp dg_amt_adjusted money' name='amt_adjusted[]' id='dg_amt_adjusted_"+dg_icount+"' size=6 value='' onchange=\"dg_show_unconcile_total('"+dlgname+"');\"></td>\n\
                            <td><a href='javascript:void(0);' class='button button-tiny_wrap cursor button-caution' onclick=\"dg_remove_row("+dg_icount+",'"+row_name+"','"+rowparent+"','"+dlgname+"');\">-</a></td>\n\
                        </tr>";
                //var dlg = $("#dlg_unreconcile_form");
                dg_rowparent.append(recon_list);
                
                var invs_id = $("#"+rowname+" #dlg_selected_invoices_"+dg_icount,dg_rowparent);
                
                load_unconciled_invoices(invs_id);
    }
    
    var dg_add_inv_btn = true;
    function dg_fn_inv_selected(elt,dg_icount,row_name,rowparent,dlgname)
    {
            var dlg = $("#"+dlgname);
            var rowname = "#"+row_name;//+"_"+dg_icount;
            var dg_rowname = $("#"+row_name);
            var dg_rowparent = $("."+rowparent);
            
            var row =  $("#"+row_name+"_"+dg_icount);
        
            var rpt_unreconciled_value = $("#dg_i_unreconciled_value",dlg).val();
            var error_status = $(".dg_error_status",dlg).html("");
            var reconciled_total= format_number( $(".dg_l_total_adjusted_val",dlg).val() );
            
            
            
            if(rpt_unreconciled_value == 0) {
                error_status.html("No Unreconciled amount."); return false;
            }
            
            var amt_unreconcile = $(rowname+" #dg_amt_unreconcile_"+dg_icount,dg_rowparent);
            var amt_adjusted = $(rowname+" #dg_amt_adjusted_"+dg_icount,dg_rowparent);
            
            var invoice_row = $("#dg_reconcile_row_"+dg_icount,dlg);
            var sel_invoice_dropbox = $("#dlg_selected_invoices_"+dg_icount,dlg);
            
            //var amt_unreconcile_value= format_number( amt_unreconcile.val() );
            //alert(amt_unreconcile_value);
            
            var sel_inv = $(elt).find(':selected').val();
            var sel_inv_amount = format_number( $(elt).find(':selected').attr("inv_amount") );
            
            //if(reconciled_total < rpt_unreconciled_value) {
                   
                    if($("."+row_name).hasClass("inv_"+sel_inv)) { //if row having inv_{invid} class then the invoice already selected
                         sel_invoice_dropbox.val("").trigger("liszt:updated");
                         error_status.html("Already this invoice selected"); return false;
                    }
                    else 
                         dg_rowname.removeClass().addClass(row_name+" inv_"+sel_inv);

                    //new 
                    var i_sub_total = sel_inv_amount + reconciled_total;
                    if(i_sub_total < rpt_unreconciled_value) {
                        if(dg_icount) {
                            amt_unreconcile.val(sel_inv_amount);
                            amt_adjusted.val(sel_inv_amount);
                        }
                        /*else {$("#amt_unreconcile").val(sel_inv_amount);$("#amt_adjusted").val(sel_inv_amount);}*/
                    }
                    else {
                        //alert(i_sub_total);
                        var i_sub_total = rpt_unreconciled_value - reconciled_total;
                        amt_unreconcile.val(sel_inv_amount);
                        amt_adjusted.val(i_sub_total);
                        //alert("Invoice amount cannot be more than the receipt amount!");
                    }
                    dg_show_unconcile_total(dlgname); //$(".dg_amt_adjusted",rowparent).trigger("change");

            //} else {   alert("Invoice amount cannot be more than the receipt!");  }
            return false;
        }
                
        //$(".dg_amt_adjusted").live("change",function() {
         //   dg_show_unconcile_total();
        //});
        
        function dg_show_unconcile_total(dlgname) { //,'dg_reconcile_row','dlg_invs_list'
            var dlg = $("#"+dlgname);
            var invs_total = 0;
            $(".dg_amt_adjusted",dlg).each(function(i,row) {
                var amount = $(this).val();
                if(amount!='' ) {
                    if( !isNaN(amount) ) {
                        invs_total += format_number(amount);
                    }
                    else {
                        $(this).focus();
                        alert("Invalid number entered for adjustment amount.");return false;
                    }
                }
            });
            //var ttl_unreconciled_after = parseFloat( $(".dg_ttl_unreconciled_after",dlg).val() );
            var unreconcile_receipt_amount = format_number( $("#dg_i_unreconciled_value",dlg).val() );
            
            $(".dg_l_total_adjusted_val",dlg).val( format_number ( invs_total ) );
            $(".dg_ttl_unreconciled_after",dlg).val( format_number ( unreconcile_receipt_amount - invs_total ) );
        }

    var dg_arr_invs = [];
    function dg_remove_row(rowid,row_name,rowparent,dlgname) {
        var dlg = $("#"+dlgname);
        var tbody=$("."+rowparent,dlg);
        dg_update_values(rowid,dlgname);
        dg_icount = dg_icount - 1;
        $("#"+row_name+"_"+rowid,tbody).remove();

    }
    
    function dg_update_values(rowid,dlgname) {
        var dlg = $("#"+dlgname);
        //Remove invoice
        //Subtract ajusted amount
        var sel_inv = $("#dlg_selected_invoices_"+rowid,dlg).find(':selected').val();
        var reconciled_total= format_number( $(".dg_l_total_adjusted_val",dlg).val() );
        //if(dg_arr_invs.length) dg_arr_invs.remove(sel_inv);
//            else $("#reconcile_row").html("");

        var amt_adjusted = $("#dg_amt_adjusted_"+rowid,dlg).val();
        
        var sub_amount = reconciled_total - amt_adjusted;
       $(".dg_l_total_adjusted_val",dlg).val( format_number( sub_amount ) );
    }
    
    $(window).resize(function() {
       $("#dlg_unreconcile_form,#dlg_unreconcile_view_list,#dlg_credit_note_block").dialog("option","position",["center","center"]); 
    });

    $("#dlg_credit_note_block").dialog({
        autoOpen:false
        ,modal:true
        ,width:"682"
        ,height:"617"
        ,buttons:{
            "Reconcile":function() {
                var dl_submit_reconcile_form= $("#dg_credit_note_form");
                if(dl_submit_reconcile_form.parsley('validate')) {
                    $.post(site_url+"/admin/jx_creditnote_reconcile_form_submit/"+franchise_id,dl_submit_reconcile_form.serialize(),function(resp) {
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
                var dlg = $("#dlg_credit_note_block");
                
                //$("#dl_submit_reconcile_form").clearForm();
                $(".dg_amt_unreconcile,.dg_amt_adjusted,.dg_l_total_adjusted_val,.dg_ttl_unreconciled_after",dlg).val(0);
                $(".dg_sel_invoices",dlg).val("").trigger("liszt:updated");
                $(".dg_credit_row",dlg).removeClass().addClass("dg_invoice_row");
                dg_icount = 1;
                $(this).dialog("close");
            }
        }
    });
    
    function reconcile_cr_amount(elt,credit_note_id,credit_amt,unreconciled_amount) {
        var dlg = $("#dlg_credit_note_block");
        $("#dg_i_credit_note_id",dlg).val(credit_note_id);$("#dg_i_credit_amount",dlg).val(credit_amt);$("#dg_i_unreconciled_value",dlg).val(unreconciled_amount);
        $("#dlg_credit_note_block").dialog('open').dialog("option","title","Reconcile the Credit Note id #"+credit_note_id);
        var invs_id = $("#dlg_selected_invoices_1",dlg);
        load_unconciled_invoices(invs_id);
    }
    
    function dg_recon_change_document_type(elt,sel_invoices,row_name,parent,dlgname) {
            var dlg = $("#"+dlgname);
            
            var invs_id = $("#"+sel_invoices,dlg); //, $("#"+row)
            var document_type = $(elt).find(":selected").val(); 
            
            if(document_type == 'inv') {
                load_unconciled_invoices(invs_id);
            }
            else if(document_type == 'dr') {
                load_unconciled_debit_notes(invs_id);
            }
            
    }

    