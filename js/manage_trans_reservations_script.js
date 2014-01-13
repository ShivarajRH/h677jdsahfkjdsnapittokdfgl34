//var GM_TIMING_END_CHUNK1=(new Date).getTime();
/**** CREATE BATCH PROCESS **/


//ONCHANGE TERRITORY IN BATCH GROUP CREATION
$("#sel_terr_id").live("change",function() {
    var sel_terr_id = $(this).val();
    if(sel_terr_id !='00') {
        $(".filter_terr").hide();
        $('.filter_terr_'+sel_terr_id).show();
    }
    else {
        $(".filter_terr").show();
    }
});

//ONCLICK PROCESS BY FRANCHISE
$("#show_by_group").live("click",function() {
        loadTransactionList(0);
});

//ONCLICK PROCESS INVOICE 
function process_pinvoices_by_fran(elt,franchise_id) {
    $("#pinvoices_form_"+franchise_id).submit();
}

//FRANCHISE GROUP PAGE > SHOW OR HIDE ORDER LIST
function show_orders_list(franid,from,to,batch_type) {
        //alert(franid+","+from+","+to+","+batch_type);return false;
        if($(".orders_info_block_"+franid).is(":visible")) {
            $(".orders_info_block_"+franid).toggle("slow").html("");
        }
        else {
            $.post(site_url+"admin/jx_get_franchise_orders/"+franid+"/"+from+"/"+to+"/"+batch_type, {}, function(rdata){
                $(".orders_info_block_"+franid).html(rdata).toggle("slow");
            }).fail(fail);
        }
        return true;
}

$("#dlg_create_group_batch_block").dialog({
        autoOpen: false,
        height: 450,
        width:550,
        modal: false,
        buttons: {
            "Create Batch":function() {
                    //$("form",this).submit();
                    var sel_batch_menu = $("#sel_batch_menu").find(":selected").val();
//                            var assigned_menuids = $("#assigned_menuids").val();
                    var batch_size = $("#batch_size").val();
                    var assigned_uid = $("#assigned_uid").find(":selected").val();
                    var territory_id = $("#dlg_sel_territory").find(":selected").val();
                    var townid = 0;//$("#dlg_sel_town").find(":selected").val();

                    //if(sel_batch_menu == '00') { show_output("ERROR : Please Select Menu"); return false; }
                    if(assigned_uid == '00') { 
                        if(!confirm("Warning :\n Are you sure you do not want to assign user for this batch?")) {
                            show_output("Warning :\n Batch will not assign to any user.");
                            return false;
                        }
                        assigned_uid = '0';
                    }
                    if(sel_batch_menu == '00') {sel_batch_menu=0;  }
                    if(territory_id == '00') {territory_id=0;  }
                    if(townid == '00') {townid=0;  }
//                            ,assigned_menuids:assigned_menuids
                    var postData = {sel_batch_menu:sel_batch_menu,batch_size:batch_size,assigned_uid:assigned_uid,territory_id:territory_id,townid:townid};
                    console.log(postData);
                    $.post(site_url+"admin/create_batch_by_group_config",postData,function(rdata) {
                            show_output(rdata);
                            loadTransactionList(0);
                            //$("#sel_batch_menu").val($("#sel_batch_menu option:nth-child(0)").val());

                    }).fail(fail);
            },
            Close: function() { 
              $(this).dialog( "close" );
            }
        },
        close: function() {
            $(this).dialog("close");
        },
        position: ['center', 'center'],
        title: "Create Group Batch"
});


/*$(".btn_cteate_group_batch").live("click",function(){
   var territory_id = $(this).attr("territory_id");
   var townid= $(this).attr("town_id");
   var franchise_id= $(this).attr("franchise_id");
});*/
function fn_cteate_group_batch(territory_id,townid,franchise_id) {
        batch_show_group(territory_id,townid,franchise_id);
}

function batch_show_group(territory_id,townid,franchise_id) {
    var postData={};
    postData = {territory_id : territory_id};
    
    $("#dlg_create_group_batch_block").html('');
    $.post(site_url+"admin/manage_reservation_create_batch_form",postData,function(hmtldata) {
         $("#dlg_create_group_batch_block").html(hmtldata).dialog("open");
    }).fail(fail);
    return false;
}

function show_output(rdata) {
    $(".create_batch_msg_block").slideDown().html(rdata);//.delay("6500").slideUp("slow");
}
//****PICKLIST 1 *****/
$("#show_picklist_block").dialog({
    autoOpen: false,
    open:function() {      //$("form",this).submit();  
    },
    height: 650,
    width:950,
    position: ['center', 'center'],
    modal: true
});
//CHECK OR UNCHECK TRANS FOR PICKLIST
function chkall_fran_orders(franid) {
        var checkBoxes=$(".chk_pick_list_by_fran_"+franid);
        if($("#pick_all_fran_"+franid).is(":checked")) {
            checkBoxes.attr("checked", !checkBoxes.attr("checked"));
        }
        else {
            checkBoxes.removeAttr("checked", checkBoxes.attr("checked"));
        }
}

function picklist_product_wise(elt,batch_id,franchise_id) {    //$("#picklist_by_fran_form_"+franchise_id).submit();
    //var p_invoice_ids_str = $("#picklist_by_fran_all_"+franchise_id).val();var postData = {pick_list_trans:p_invoice_ids_str};
    if(franchise_id== undefined) { franchise_id =''; }
    $.post(site_url+"admin/picklist_product_wise/"+batch_id+"/"+franchise_id,{},function(resp) {
            $("#show_picklist_block").html(resp).dialog("open").dialog('option', 'title', 'Pick List');
    });
}

function picklist_fran_wise(elt,batch_id,franchise_id) {    //$("#picklist_by_fran_form_"+franchise_id).submit();
    //var p_invoice_ids_str = $("#picklist_by_fran_all_"+franchise_id).val();var postData = {pick_list_trans:p_invoice_ids_str};
    if(franchise_id== undefined) { franchise_id =''; }
    $.post(site_url+"admin/picklist_fran_wise/"+batch_id+"/"+franchise_id,{},function(resp) {
            $("#show_picklist_block").html(resp).dialog("open").dialog('option', 'title', 'Pick List');
    });
}

$("#pick_all").live("change",function() {
    var checkBoxes=$(".pick_list_trans_ready");
    if($(this).is(":checked")) {
        checkBoxes.attr("checked", !checkBoxes.attr("checked"));
    }
    else {
        checkBoxes.removeAttr("checked", checkBoxes.attr("checked"));
    }
});
$("#btn_generate_pick_list").live("click",function(){
        if($("#show_by_group").is(":checked")) {
                var p_invoice_ids=[];
                //var pick_list_trans=$("input[name='chk_pick_list_by_fran']:checked").length;
                var pick_list_trans=$("input#chk_pick_list_by_fran:checked").length;
                
                if(pick_list_trans==0) { alert("Please select any of fransachise transaction to generate pick list."); return false;}
                 
                $.each($("input#chk_pick_list_by_fran:checked"),function() {
                    p_invoice_ids.push($(this).val());

                });
                $.unique(p_invoice_ids);
                var p_invoice_ids_str=p_invoice_ids.join(",");

//               $("#show_picklist_block input[name='pick_list_trans']").val(p_invoice_ids_str);$("#show_picklist_block").dialog("open").dialog('option', 'title', 'Pick List for '+p_invoice_ids.length+" proforma invoice/s");

                var postData = {pick_list_trans:p_invoice_ids_str};
                $.post(site_url+"admin/picklist_product_wise",postData,function(resp) {
                        $("#show_picklist_block").html(resp).dialog("open").dialog('option', 'title', 'Pick List for '+p_invoice_ids.length+" proforma invoice/s");
                });
                return false;
        }
        else {
                var p_invoice_ids=[];
                
                var pick_list_trans_ready=$("input.pick_list_trans_ready:checked").length;
                //var pick_list_trans_partial=$("input.pick_list_trans_partial:checked").length;
                //var total=(pick_list_trans_ready+pick_list_trans_partial);
                if(pick_list_trans_ready==0) { alert("Please select any of transaction to generate pick list"); return false;}
                
                $.each($("input.pick_list_trans_ready:checked"),function() {
                    p_invoice_ids.push($(this).val());
                });
                $.each($("input.pick_list_trans_partial:checked"),function() {
                    p_invoice_ids.push($(this).val());
                });
                var p_invoice_ids_str = p_invoice_ids.join(",");
                
                var postData = {pick_list_trans:p_invoice_ids_str};
                $.post(site_url+"admin/picklist_product_wise",postData,function(resp) {
                        $("#show_picklist_block").html(resp).dialog("open").dialog('option', 'title', 'Pick List for '+p_invoice_ids.length+" proforma invoice/s");
                });
                //$("#show_picklist_block input[name='pick_list_trans']").val(p_invoice_ids_str);$("#show_picklist_block").dialog("open").dialog('option', 'title', 'Pick List for '+p_invoice_ids.length+" proforma invoice/s");
        }
});
/* END OF PICKLIST CODE */

$(".reservation_action_status").dialog({
    autoOpen: false,
    open:function() {   //$("form",this).submit();
    },
    top:120,
    height: 450,
    width:633,
    position: ['center', 'center'],
    modal: true
});


function reallot_stock_for_all_transaction(userid,pg) {
    if(!confirm("Are you sure you want to reserve available stock for all pending or partial transactions?")) {
        return false;
    }
    var updated_by = userid;
    var rdata='';
    $('#trans_list_replace_block').html("<div class='loading'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Loading...</div>");
    
    $.post(site_url+"admin/jx_reserve_avail_stock_all_transaction/"+updated_by,"",function(resp) {
            if(resp.status == 'fail') {
                        rdata = resp.response+"";
            }
            else {
                rdata += "<div class='dash_bar'>Alloted : "+resp.alloted+"</div><div class='dash_bar_red'>No Stock : "+resp.nostock+"</div>";
                if(resp.alloted!=0) {
                    
                    rdata += "<div><div class='clear'></div><h3>Following transactions alloted:</h3> <table class='subdatagrid'><tr><th>Transactions</th><th>Products</th></tr>";
                    $.each(resp.alloted_msg,function(transid,row){
                        
                        rdata += "<tr><td><a href='"+site_url+"admin/trans/"+transid+"' target='_blank'>"+transid+"</a></td><td>";
                        $.each(row,function(i,val) {
                                rdata += "<a href='"+site_url+"admin/product/"+val.product_id+"' target='_blank'>"+val.product_id+"</a>"+"("+val.stock+"), ";
                        });
                        rdata += "</td></tr>";
                    });
                    rdata += "</table></div>";
                    
                    
                }
                else if(resp.nostock!=0){
                    rdata += "<div><div class='clear'></div><h3>Following transactions have no stock:</h3> <table class='subdatagrid'><tr><th>Transactions</th><th>Products</th></tr>";
                    $.each(resp.nostock_msg,function(transid,row){
                        
                        rdata += "<tr><td><a href='"+site_url+"admin/trans/"+transid+"' target='_blank'>"+transid+"</a></td><td>";
                        $.each(row,function(i,val) {
                                rdata += "<a href='"+site_url+"admin/product/"+val.product_id+"' target='_blank'>"+val.product_id+"</a>"+"("+val.stock+"), ";
                        });
                        rdata += "</td></tr>";
                    });
                    rdata += "</table></div>";
                }
//                $.each(resp.result,function(i,val_arr){$.each(val_arr,function(i,row){rdata += row;});});
            }
            loadTransactionList(pg);
            $(".reservation_action_status").html(rdata).dialog("open").dialog('option', 'title', 'Re-allot Transaction Reservation report');
    },"json");
    return false;
}
 
function reserve_stock_for_trans(userid,transid,pg) {
    if(!confirm("Are you sure you want to process \nthis transaction for batch?")) {
        return false;
        //var batch_remarks=prompt("Enter remarks?");
    }
    var ttl_num_orders=$("."+transid+"_total_orders").val();
    var batch_remarks='';
    var updated_by = userid;
    var rdata='';
    $.post('reserve_stock_for_trans/'+transid+'/'+ttl_num_orders+'/'+batch_remarks+'/'+updated_by+'',"",function(resp) {
        
            if(resp.status == 'fail') {
                    rdata = resp.response+"";
            }
            else {
                rdata += "<div class='dash_bar'>Alloted : "+resp.alloted+"</div><div class='dash_bar_red'>No Stock : "+resp.nostock+"</div>";
                if(resp.alloted!=0) {
                    
                    rdata += "<div><div class='clear'></div><h3>Following transactions alloted:</h3> <table class='subdatagrid'><tr><th>Transactions</th><th>Products</th></tr>";
                    $.each(resp.alloted_msg,function(transid,row){
                        
                        rdata += "<tr><td><a href='"+site_url+"admin/product/"+transid+"' target='_blank'>"+transid+"</a></td><td>";
                        $.each(row,function(i,val) {
                                rdata += "<a href='"+site_url+"admin/product/"+val.product_id+"' target='_blank'>"+val.product_id+"</a>"+"("+val.stock+"), ";
                        });
                        rdata += "</td></tr>";
                    });
                    rdata += "</table></div>";
                    
                    
                }
                else if(resp.nostock!=0){
                    rdata += "<div><div class='clear'></div><h3>Following transactions have no stock:</h3> <table class='subdatagrid'><tr><th>Transactions</th><th>Products</th></tr>";
                    $.each(resp.nostock_msg,function(transid,row){
                        
                        rdata += "<tr><td><a href='"+site_url+"admin/product/"+transid+"' target='_blank'>"+transid+"</a></td><td>";
                        $.each(row,function(i,val) {
                                rdata += "<a href='"+site_url+"admin/product/"+val.product_id+"' target='_blank'>"+val.product_id+"</a>"+"("+val.stock+"), ";
                        });
                        rdata += "</td></tr>";
                    });
                    rdata += "</table></div>";
                }
//                $.each(resp.result,function(i,val_arr){$.each(val_arr,function(i,row){rdata += row;});});
            }
            
            loadTransactionList(pg);
            $(".reservation_action_status").html(rdata).dialog("open").dialog('option', 'title', 'Re-allot Transaction Reservation report');

    },"json");
    return false;
}
function reallot_frans_all_trans(elt,userid,franchise_id,pg) {
    if(!confirm("Are you sure you want to process \nthis transaction for batch?")) {
            return false;
            //var batch_remarks=prompt("Enter remarks?");
    }
    var all_trans = $(elt).attr("all_trans");
    var batch_remarks='';
    var updated_by = userid;
    
    var rdata='';
    var  postData = {all_trans: all_trans};
    $.post(site_url+'admin/jx_reallot_frans_all_trans/'+batch_remarks+'/'+updated_by+'',postData,function(resp) {
            
             if(resp.status == 'fail') {
                    rdata = resp.response+"";
            }
            else {
                rdata += "<div class='dash_bar'>Alloted : "+resp.alloted+"</div><div class='dash_bar_red'>No Stock : "+resp.nostock+"</div>";
                if(resp.alloted != 0) {   
                    rdata += "<div><div class='clear'></div><h3>Following transactions alloted:</h3> <table class='subdatagrid'><tr><th>Transactions</th><th>Products</th></tr>";
                    $.each(resp.alloted_msg,function(transid,row){
                        
                        rdata += "<tr><td><a href='"+site_url+"admin/product/"+transid+"' target='_blank'>"+transid+"</a></td><td>";
                        $.each(row,function(i,val) {
                                rdata += "<a href='"+site_url+"admin/product/"+val.product_id+"' target='_blank'>"+val.product_id+"</a>"+"("+val.stock+"), ";
                        });
                        rdata += "</td></tr>";
                    });
                    rdata += "</table></div>";
                    
                    
                }
                else if(resp.nostock!=0){
                    rdata += "<div><div class='clear'></div><h3>Following transactions have no stock:</h3> <table class='subdatagrid'><tr><th>Transactions</th><th>Products</th></tr>";
                    $.each(resp.nostock_msg,function(transid,row){
                        
                        rdata += "<tr><td><a href='"+site_url+"admin/product/"+transid+"' target='_blank'>"+transid+"</a></td><td>";
                        $.each(row,function(i,val) {
                                rdata += "<a href='"+site_url+"admin/product/"+val.product_id+"' target='_blank'>"+val.product_id+"</a>"+"("+val.stock+"), ";
                        });
                        rdata += "</td></tr>";
                    });
                    rdata += "</table></div>";
                }
//                $.each(resp.result,function(i,val_arr){$.each(val_arr,function(i,row){rdata += row;});});
            }
            $(".reservation_action_status").html(rdata).dialog("open").dialog('option', 'title', 'Re-allot Transaction Reservation report');
            loadTransactionList(pg);

    },'json');
    return false;
}

function cancel_proforma_invoice(p_invoice_no,userid,pg) {
    if(!confirm("Are you sure you want to cancel proforma invoice?")) {
        return false;
    }
    var rdata='';
    $.post(site_url+"admin/cancel_reserved_proforma_invoice/"+p_invoice_no+"/"+userid,{},function(resp) {
            if(resp.status == 'success') {
                rdata = "Proforma invoice has cancelled. <br>Proforma Invoice Number: "+resp.p_invoice_no+" <br> Transid: "+resp.transid;
                //rdata=("Unable to cancel the proforma.");
            }
            else {
                rdata = resp.response;
            }
            rdata="<div class='block_info'>"+rdata+"</div>";
            $(".reservation_action_status").html(rdata).dialog("open").dialog('option', 'title', 'Cancel proforma invoice report');
            loadTransactionList(pg);
    });
    return false;
}
// Auto center the dialog boxes
$(window).resize(function() { //on resize window center the dialog
    $("#dlg_create_group_batch_block").dialog("option", "position", ['center', 'center']);
    $(".reservation_action_status").dialog("option", "position", ['center', 'center']);
    $("#show_picklist_block").dialog("option", "position", ['center', 'center']);
});

//filter box show/hide
$(".close_filters").toggle(function() {
    $(".close_filters .close_btn").html("Hide");
    $(".filters_block").slideDown();
//    $(".level1_filters").animate({"width":"100%"});
},function() {
    $(".filters_block").slideUp();
    $(".close_filters .close_btn").html("Show");
});

//Onchange limit
$("#limit_filter").live("change",function() {
    loadTransactionList(0);
    return false;
});
// Onclick tab button
$(".tab_list a").bind("click",function(e){
    $(".tab_list a.selected").removeClass('selected');
    $(this).addClass('selected');
    loadTransactionList(0);
});

//Show between date ranges
$("#trans_date_form").submit(function() {
    loadTransactionList(0);
    return false;
});
//ONCHANGE Batch_type
$("#batch_type").live("change",function() {
    loadTransactionList(0);
    return false;
});
//ONCHANGE Batch_type
$("#sel_old_new").live("change",function() {
    loadTransactionList(0);
    return false;
});

//ON CLICK Paginations link
$(".trans_pagination a").live("click",function(e) {
    e.preventDefault();
    $(".page_num").val=pg;
    $('#trans_list_replace_block').html("<div class='loading'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Loading...</div>");
    $.post($(this).attr("href"),{},function(rdata) {
        $("#trans_list_replace_block").html(rdata);
    });
    return false;
});

$("#sel_batch_group_type").live("change",function() {
    loadTransactionList(0);
    return false;
});
$("#sel_menu").live("change",function() {
        var menuid=$(this).find(":selected").val();

        $.post(site_url+"admin/jx_get_brandsbymenuid/"+menuid,{},function(resp) {
                if(resp.status=='success') {
                     var obj = jQuery.parseJSON(resp.brands);
                    $("#sel_brands").html(objToOptions_brands(obj));
                }
                else {
                    //$(".sel_status").html(resp.message);
                }
            },'json').done(done).fail(fail);
            $("#sel_territory").val($("#sel_territory option:nth-child(0)").val());
            $("#sel_town").val($("#sel_town option:nth-child(0)").val());
            $("#sel_franchise").val($("#sel_franchise option:nth-child(0)").val());
            $("#sel_brands").val($("#sel_brands option:nth-child(0)").val());

    loadTransactionList(0);
    return false;
});

$("#sel_brands").live("change",function() {
    loadTransactionList(0);
    return false;
});

$("#sel_alloted_status").live("change",function() {
    loadTransactionList(0);
    return false;
});
$("#sel_franchise").live("change",function() {
            /*var franchiseid=($("#sel_franchise").val()=='00')? 00 :$("#sel_franchise").val();
            if(franchiseid==00) {
                $(".sel_status").html("");
            }   $.post("<?php echo site_url("admin/jx_franchise_creditnote"); ?>"+"/"+franchiseid,{},function(resp) {
                if(resp.status=='success') {$(".sel_status").html(resp);}
                else {$(".sel_status").html(resp);}
            }).done(done).fail(fail);*/
    loadTransactionList(0);
    return false;
});

//ENTRY 6
$("#sel_town").live("change",function() { 
    var townid=$(this).find(":selected").val();
    var terrid=$("#sel_territory").find(":selected").val();
    $.post(site_url+"admin/jx_suggest_fran/"+terrid+"/"+townid,function(resp) {
            if(resp.status=='success') {
                 var obj = jQuery.parseJSON(resp.franchise);
                $("#sel_franchise").html(objToOptions_franchise(obj));
            }
            else {
                $("#sel_franchise").val($("#sel_franchise option:nth-child(0)").val());
                //$(".sel_status").html(resp.message);
            }
        },'json').done(done).fail(fail);
    loadTransactionList(0);
    return false;
});


//ONCHANGE Territory
$("#sel_territory").live("change",function() {
    var terrid=$(this).find(":selected").val();
//        if(terrid=='00') {          $(".sel_status").html("Please select territory."); return false;        }
   // $("table").data("sdata", {terrid:terrid});

    $.post(site_url+"admin/jx_suggest_townbyterrid/"+terrid,function(resp) {
        if(resp.status=='success') {
             //print(resp.towns);
             var obj = jQuery.parseJSON(resp.towns);
            $("#sel_town").html(objToOptions_terr(obj));
        }
        else {
            $("#sel_town").val($("#sel_town option:nth-child(0)").val());
            $("#sel_franchise").val($("#sel_franchise option:nth-child(0)").val());
                        //$(".sel_status").html(resp.message);
        }
    },'json').done(done).fail(fail);
    loadTransactionList(0);
    return false;
});


/*  *********************************************************************** */
/***  REPEATED FUNCTIONS ****************/


function objToOptions_brands(obj) {
    var output='';
        output += "<option value='00' selected>All Brands</option>\n";
    $.each(obj,function(key,elt){
        if(obj.hasOwnProperty(key)) {
            output += "<option value='"+elt.id+"'>"+elt.name+"</option>\n";
        }
    });
    return(output);
}
function objToOptions_terr(obj) {
    var output='';
        output += "<option value='00' selected>All Towns</option>\n";
    $.each(obj,function(key,elt){
        if(obj.hasOwnProperty(key)) {
            output += "<option value='"+elt.id+"'>"+elt.town_name+"</option>\n";
        }
    });
    return(output);
}
/*function objToOptions_franchise(obj) {
    var output='';
        output += "<option value='00' selected>All Franchise</option>\n";
    $.each(obj,function(key,elt){
        if(obj.hasOwnProperty(key)) {
            output += "<option value='"+elt.franchise_id+"'>"+elt.franchise_name+"</option>\n";
        }
    });
    return(output);
}*/
function objToOptions_users(obj) {
    var output='';
        output += "<option value='00' selected>Assigned to</option>\n";
    $.each(obj,function(key,elt){
        if(obj.hasOwnProperty(key)) {
            output += "<option value='"+elt.userid+"'>"+elt.username+"</option>\n";
        }
    });
    return(output);
}
function objToOptions_menus(obj) {
    var output='';
        output += "<option value='00' selected>Choose</option>\n";
        $.each(obj,function(key,elt){
            if(obj.hasOwnProperty(key)) {
                output += "<option value='"+key+"'>"+elt.menuname+"</option>\n";
            }
        });

    return(output);
}

 //By default load lists
loadTransactionList(0);
var pg=0;
    
function loadTransactionList(pg) {
    
    $(".pagination_top").html("");
    $(".ttl_trans_listed").html("");
    
    $(".re_allot_all_block").css({"padding":0}).html("");
    

    var batch_type = $('.tab_list .selected').attr('id');
//        var batch_type= ($("#batch_type").val() == "00")?0: $("#batch_type").val();
    var terrid= ($("#sel_territory").val()=='00')?0:$("#sel_territory").val();
    var townid=($("#sel_town").val()=='00')?0:$("#sel_town").val();
    var franchiseid=($("#sel_franchise").val()=='00')?0:$("#sel_franchise").val();
    var menuid=($("#sel_menu").val()=='00')?0:$("#sel_menu").val();
    var brandid=($("#sel_brands").val()=='00')?0:$("#sel_brands").val();

    var date_from= ($("#date_from").val() == '')?0:$("#date_from").val();
    var date_to= ($("#date_to").val() == '')?0:$("#date_to").val();


    var limit= $("#limit_filter").val();

    if(typeof pg != 'undefined')
        $(".page_num").val=pg;
    pg = (typeof pg== 'undefined') ? $(".page_num").val() : $(".page_num").val();

    var showbyfrangrp = ($("#show_by_group").is(":checked"))? 1:0;

    var batch_group_type=($("#sel_batch_group_type").val()=='00')? 0:$("#sel_batch_group_type").val();

    var sel_latest=($("#sel_old_new").find(":selected").val()==0) ? 0 : $("#sel_old_new").find(":selected").val();
    
    var sel_alloted_status = ($("#sel_alloted_status").find(":selected").val()==0) ? 0 : $("#sel_alloted_status").find(":selected").val();
    
    $('#trans_list_replace_block').html("<div class='loading'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Loading...</div>"); 
    $.post(site_url+'admin/jx_manage_trans_reservations_list/'+batch_type+'/'+date_from+'/'+date_to+'/'+terrid+'/'+townid+'/'+franchiseid+'/'+menuid+'/'+brandid+"/"+showbyfrangrp+"/"+batch_group_type+'/'+sel_latest+"/"+sel_alloted_status+"/"+limit+"/"+pg+"",{},function(rdata) {
        $("#trans_list_replace_block").html(rdata);
    });
    
}
function show_all_orders() {
        $.each($(".view_all_link"),function() {
            $(this).click();
        });
        return false;
}
   
function fail(rdata) {
    console.log(rdata);
}
function done(data) { }
function fail(xhr,status) { $('#trans_list_replace_block').print("Error: "+xhr.responseText+" "+xhr+" | "+status);}
function success(resp) {
        $('#trans_list_replace_block').html(resp);
}
 $(document).ready(function() {
        //FIRST RUN
        $( "#date_from").datepicker({
             changeMonth: true,
             dateFormat:'yy-mm-dd',
             numberOfMonths: 1,
             maxDate:0,// minDate: new Date(reg_date),
               onClose: function( selectedDate ) {
                 $( "#date_to" ).datepicker( "option", "minDate", selectedDate ); //selectedDate
             }
           });
        $( "#date_to" ).datepicker({
            changeMonth: true,
             dateFormat:'yy-mm-dd',// numberOfMonths: 1,
             maxDate:0,
             onClose: function( selectedDate ) {
               $( "#date_from" ).datepicker( "option", "maxDate", selectedDate );
             }
        });
    });
/*function batch_enable_disable(transid,flag,pg) {    var d_msg=(flag==1)?"enable":"disable";    if(confirm("Are you sure you want to "+d_msg+" for batch?")) { $.post(site_url+"admin/jx_batch_enable_disable/"+transid+"/"+flag,{},function(rdata) { loadTransactionList(pg);  }).done(done).fail(fail); } }
function f1(){var data = '21/12/2013';var pat= /[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}/;if (data.match(re));}*/