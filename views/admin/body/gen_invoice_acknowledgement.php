<?php
	$this->load->plugin('barcode');
        $userid = $user['userid'];
?>
<style>
    .leftcont { display: none;}
    .datagrid th { background: #443266;color: #C3C3E5; }
    /*table.datagridsort tbody td { padding: 4px; }
    .datagrid td { padding: 1px; }
    .datagrid td:hover { background-color: rgb(248, 249, 250) !important; }
    .subdatagrid {    width: 100%; }
    .subdatagrid th {padding: 4px 0 2px 4px !important;font-size: 11px !important;color: #130C09;background-color: rgba(112, 100, 151, 0.51);}
    .subdatagrid td {padding: 4px !important;}*/
    
    .loading { display: block; padding-left: 20px;}
    .block {
        /*float: left;*/
        margin: 5px 5px;
        padding: 5px 8px;
        background-color: #8D8CB1;
    }
    .block:hover {
        background-color:#706EC7;
    }
    .block_link {
        padding: 3px 3px;
        font-size: 11px;
        background-color: #999C98;
    }
    .block_link:hover {
        background-color:#706EC7;
    }
    .block_link_done { padding: 3px 3px;
        font-size: 11px;
        background-color:#B3C4B4;
    }
    .anchor a {
        color: #f1f1f1;
    }
    .anchor a:hover {
        text-decoration: none;
    }
    .label_text {
        margin-top: 10px;
        margin-right: 10px;
        font-size: 16px;
    }
    .hide {
        display: none;
    }
    .page_topbar { margin-right: 12%;}
    .block_list_acknowledgements { float: left; width: 88%;}
    .right_static_block { float: right; position: fixed; right: 25px; width: 12%; text-align: right; }
    .btn_generate_ack,.btn_generate_imei { float: right;padding: 8px; }
    /*.btn_generate_imei { margin:15px 0;  }*/
</style>
<div class="page_wrap container">
	
	<div class="page_topbar" >
		<h2 class="page_title fl_left">Manage Acknowledgements</h2>
                <div style="clear:both">&nbsp;</div>
                <div class="fl_left">
                    <select id="sel_territory" name="sel_territory" style="width:200px;">
                        <option value="00">All Territory</option>
                        <?php foreach($terr_info as $terr) {
                                if($terr['id'] == $territory_id) {
                                        echo '<option value="'.$terr['id'].'" selected>'.$terr['territory_name'].'</option>';
                                }
                                else {
                            ?>
                                <option value="<?=$terr['id'];?>"><?=$terr['territory_name'];?></option>
                        <?php   } 
                            }
                        ?>
                    </select>
                    
                </div>
                
		<div class="page_action_buttons fl_left" align="right">
                    <form id="trans_date_form" name="trans_date_form" method="post" action="<?php echo site_url("admin/print_invoice_acknowledgementbydate"); ?>">
                        <table cellpadding="5" cellspacing="0" border="0">
                            <tr>
                                <td><label for="date_from">From:</label></td><?php //date('Y-m-01',time()-60*60*24*7*4); ?>
                                <td><input type="text" id="date_from" name="date_from" value="<?php echo $date_from; ?>" size="10" />
                                    <input type="hidden" id="date_to" name="date_to" value="<?php  echo $date_to; ?>"  size="10"/>
                                    <input type="submit" value="Submit">
                                </td>
<!--                        <td><label for="date_to">To:</label></td>
                            <td colspan="4" align="left"><label for="consider_printed_ack">Do you want to consider already <br> printed acknowledgements?</label><input type="checkbox" id="consider_printed_ack" name="consider_printed_ack" value="y" checked/></td>-->
                        </table>
                    </form>
		</div>
                <div class="fl_right"><?php
                    $l_date_to = $date_to;//
                    if( strtotime($l_date_to) < time() ) {
                    ?>
                        <div class="block fl_right anchor"><a href="<?=site_url("admin/print_invoice_acknowledgementbydate/".$l_date_to);?>" title="Show Next 7 Days Shipment Log"> Next </a></div>
                    <?php
                    }
                    ?>
                    <div class="block fl_right anchor"><a href="<?=site_url("admin/print_invoice_acknowledgementbydate/".date("Y-m-d",strtotime($date_from)-(60*60*24*6)));?>" title="Show Last 7 Days Shipment Log"> Prev </a></div>
                    
                </div>
                <div class="fl_right">
                    <div class="label_text">Showing from <b><?=$date_from;?></b> to <b><?=$date_to;?></b></div>
                </div>
	</div>
	<div style="clear:both">&nbsp;</div>
	<div class="page_content">
            <form action='' name='' id=''>
                
                <?php
                    if(!isset($terr_info)) {?>
                        <h2>No records found change date range</h2>
                <?php }
                    else {
                ?>
                   
                    <div class="block_list_acknowledgements">
                        <table class='datagrid' cellspacing='4' cellpadding='4' width='100%'>
                            <tr>
                                <th width="25">#</th>
                                <th>Territory Name</th>
                                <th>
                                    <abbr title='Territory Manager'>Territory Manager</abbr>
                                </th>
                                <th>
                                    <abbr title='Business Executive'>Business Executives</abbr>
                                </th>
                               
                                <th width="200"><span><?=date("d / M / Y",strtotime($slabs_data['slab1']["date_from"]) ); ?> - <?=date("d / M / Y",strtotime($slabs_data['slab1']["date_to"]) );?></span></th>
                                <th width="200"><span><?=date("d / M / Y",strtotime($slabs_data['slab2']["date_from"]) ); ?> - <?=date("d / M / Y",strtotime($slabs_data['slab2']["date_to"]) );?></span></th>
                                <th width="200"><span><?=date("d / M / Y",strtotime($slabs_data['slab3']["date_from"]) ); ?> - <?=date("d / M / Y",strtotime($slabs_data['slab3']["date_to"]) );?></span></th>
                                <th width="50"><input type='checkbox' name='' id='' title='Pick all for printing' class='chk_all_terr_print'></th>
                            </tr>
                            <?php // echo '<pre>';print_r($terr_info);
                            
                            foreach($terr_info as $i=>$terr) {
                                ?>
                                <tr id="terr_<?=$terr['id'];?>" class="terr_row">
                                    <td><?=++$i;?></td>
                                    <td><?=$terr['territory_name'];?></td>
                                    <td>
                                        <?php
                                            $tm_info = $this->erpm->get_territory_managers($terr['id']);
                                            if(empty($tm_info)) {                                                
                                                echo '--'; 
                                            }
                                            else {
                                                foreach($tm_info as $tm) { ?>
                                                    <input type="hidden" name="tm_emp_id[]" id="tm_emp_id_<?=$terr['id'];?>" value="<?=$tm['employee_id'];?>"/>
                                                    <div class=""><a target="_blank" href="<?=site_url('admin/view_employee/'.$tm['employee_id'])?>"><?=$tm['name'];?></a></div>
                                        <?php   } 
                                            }?>
                                    </td>
                                    <td>
                                        <?php
                                            $executive_info = $this->erpm->get_town_executives($terr['id']);
                                            if(empty($executive_info)) {                                                
                                                echo '--'; 
                                            }
                                            else {
                                                foreach($executive_info as $be) { ?>
                                                    <input type="hidden" name="be_emp_id[]" id="be_emp_id_<?=$terr['id'];?>" value="<?=$be['employee_id'];?>"/>
                                                    <div class=""><a target="_blank" href="<?=site_url('admin/view_employee/'.$be['employee_id'])?>"><?=$be['name'];?></a></div>
                                        <?php   }
                                            }?>
                                    </td>
                                    <?php $str_invs='';$arr_invs = array();
                                    
                                    if(count($slabs_data)>0) {
                                        
                                        foreach($slabs_data as $slab_name=>$slab) {
                                            
                                            
                                    ?>
                                    <td>
                                        <div class="ack_det">
                                            <?php $data = '';
                                                $elt = $slabs_data[$slab_name]['result'][$terr['id']][0];
                                                $total_invs = $elt['ttl_invs'];
                                                $ttl_franchises = $elt['ttl_franchises'];

                                                if($ttl_franchises != 0) {


                                                    $data .= '<b>'.$ttl_franchises.'</b> Franchises<br>
                                                            <b>'.$total_invs.'</b> Invoices';


                                                    $arr_print_log = $this->erpm->is_acknowledgement_printed($elt['invoice_no_str'],$userid);
                                                    /**
                                                     * if print ack already generated
                                                     */
                                                    if($arr_print_log['count']>0) {
                                                        $data .= '<span class="label_fld fl_right block_link_done anchor">
                                                                    <a href="javascript:void(0);" onclick="return show_acknowledgement_log(this,\''.$terr["id"].'\',\''.$slab_name.'\');">Generated '.$arr_print_log['count'].' times</a>
                                                                </span>';
                                                    }
                                                    else {
                                                        $arr_invs[] = $elt['invoice_no_str']; //consider only if not printed already...
                                                        
                                                        $data .= '<span class="label_fld fl_right block_link anchor">
                                                                        <a href="javascript:void(0);" onclick="return print_acknowledgement_invoices(this,\''.$terr["id"].'\',\''.$slab_name.'\');">Generate</a>
                                                                </span>';
                                                    }
                                                    $data .= '<input type="hidden" name="grp_invoices_'.$slab_name.'_'.$terr['id'].'" id="grp_invoices_'.$slab_name.'_'.$terr['id'].'" class="grp_invoices_set" value="'.$elt['invoice_no_str'].'" date_from="'.$slabs_data[$slab_name]["date_from"].'" date_to="'.$slabs_data[$slab_name]["date_to"].'" />';
                                                }
                                                else
                                                {
                                                    $data .=  '--';
                                                }
                                                echo $data;
                                            ?>
                                        </div>
                                   </td>
                                <?php   }
                                        $str_invs = implode(",",$arr_invs);
                                        
                                    }
                                   ?>
                                   
                                    <td>
                                        <?php
                                            if($str_invs !='') {
                                        ?>
                                            <input type="hidden" name="str_invs_<?=$terr['id'];?>" id="str_invs_<?=$terr['id'];?>" value="<?=$str_invs;?>" class="str_invs" />
                                            <input type='checkbox' name='chk_terr_<?=$terr['id'];?>' id='chk_terr_<?=$terr['id'];?>' class='chk_terr_print' territory_id="<?=$terr['id'];?>" />
                                        <?php } ?>
                                    </td>
                                </tr>

                    <?php } ?>

                                <tr><td colspan='8' align='right'></td></tr>
                        </table>
                    </div>
                        
                    <div class="right_static_block">
                        <div>
                            <input type="hidden" name="userid" id="userid" value="<?=$userid;?>" class="userid" />
                            <input type='submit' value='Generate Acknowledgement' name='btn_generate_ack' id='btn_generate_ack' class='btn_generate_ack'/>
                        </div><div class="clear"></div>
                        <div class="print_status"></div>
                    </div>
            </div>    
            </form>
            <?php } ?>
        
        
</div>

<div id="dlg_block" name="dlg_block" style="display:none;"></div>

<script type="text/javascript">
// <![CDATA[
    
    /**
     * On click Generate Acknowledgement button
     */
    $(".btn_generate_ack").click(function(e){
        e.preventDefault();
        var total_terr =  $(".chk_terr_print").length;
        var selected_terr = $(".chk_terr_print:checked").length;
        //alert(total_terr+"-"+selected_terr);
        if(selected_terr == 0) {
            alert("Warning:\nSelect any territories to generate acknowledgement."); return false;
        }
            var group_str_invs=[];

            $.each($(".chk_terr_print:checked"),function(i,row) {
                    var territory_id = $(this).attr("territory_id");
//                    var tm_id = $("#tm_emp_id_"+territory_id).val();
//                    var be_id = $("#be_emp_id_"+territory_id).val();
                    group_str_invs[i] = $("#str_invs_"+territory_id).val();

            });
            var p_invoice_ids_str = (group_str_invs.join(","));
            
            var date_from = $("#date_from").val();
            var date_to = $("#date_to").val();

            var postData = {p_invoice_ids_str:p_invoice_ids_str,date_from:date_from,date_to:date_to};

            $.post(site_url+"/admin/jx_get_acknowledgement_list",postData,function(resp) {
                    //show dialog
                    $("#dlg_block").html(resp).dialog("open").dialog('option', 'title', 'Print Acknowledgement list');
            },'html');
            return false;
    });
    
    /**
     *Onclick Check box - select all ready territories
     */
    $(".chk_all_terr_print").bind("click",function() {
        var elt = $(this);
        if(elt.is(":checked")) {
            $(".chk_terr_print").attr("checked",true);
        }
        else {
            $(".chk_terr_print").attr("checked",false);
        }
    });
    
    /**
     *Dialog box config
     */
    $("#dlg_block").dialog({
        autoOpen: false,
        height: 650,
        width:950,
        position: ['center', 'center'],
        modal: true
    });
    
    /**
     *Onchange territory id
     */
    $("#sel_territory").change(function(e) {
        e.preventDefault();
        var date_from = $("#date_from").val();
        var terr_id = $(this).find(":selected").val();

        if(terr_id== '00') {
            $(".terr_row").removeClass("hide");
            $(".chk_all_terr_print").attr("disabled",false);
        }
        else {
            $(".chk_all_terr_print").attr("disabled",true);
            $(".terr_row").addClass("hide");
            $("#terr_"+terr_id+"").removeClass("hide");
        }
    });
    /**
     * Print acknowledgements
     */
    function print_acknowledgement_invoices(e,territory_id,set) {
        var lnk = $("#grp_invoices_"+set+"_"+territory_id)
        var p_invoice_ids_str = lnk.val();
        var date_from = lnk.attr("date_from");
        var date_to = lnk.attr("date_to");

        get_acknowledgement_list(p_invoice_ids_str,date_from,date_to);
    }
    /**
     * Re-Print acknowledgements
     */
    function re_print_acknowledgement_invoices(p_invoice_ids_str,date_from,date_to) {
        get_acknowledgement_list(p_invoice_ids_str,date_from,date_to);
    }
    
    function get_acknowledgement_list(p_invoice_ids_str,date_from,date_to) {
        var postData = {p_invoice_ids_str:p_invoice_ids_str,date_from:date_from,date_to:date_to};

        $.post(site_url+"/admin/jx_get_acknowledgement_list",postData,function(resp) {
                $("#dlg_block").html(resp).dialog("open").dialog('option', 'title', 'Print Acknowledgement list');
        },'html');
        return false;
    }
    
    function show_acknowledgement_log(e,territory_id,set) {
        var lnk = $("#grp_invoices_"+set+"_"+territory_id)
        var p_invoice_ids_str = lnk.val();
        var date_from = lnk.attr("date_from");
        var date_to = lnk.attr("date_to");

        var postData = {p_invoice_ids_str:p_invoice_ids_str};
        var html='';
        $.post(site_url+"/admin/jx_show_acknowledgement_log",postData,function(resp) {
            
            if(resp.status == 'success') {
                html += "<h2>Acknowledgement Print Log</h2>\n\
                        <table colspan='4' class='datagrid' width='100%'><tr>\n\
                            <th>#</th>\n\
                            <th>Log Id</th>\n\
                            <th>Territory Manager</th>\n\
                            <th>Business Executives</th>\n\
                            <th>Printed By</th>\n\
                            <th>Print Count</th>\n\
                            <th>Print Confirm</th>\n\
                            <th>Printed On</th>\n\
                            <th>Action</th>\n\
                          </tr>";
                //<th>Invoice No</th>\n\html += "<td>"+row.p_inv_no+"</td>";
                var i=1;
                $.each(resp.result,function(x,row) {
                    html += "<tr><td>"+(i)+"</td>";
                    html += "<td>"+row.log_id+"</td>";
                    html += "<td>"+row.tm_name+"</td>";
                    html += "<td>"+row.be_name+"</td>";
                    html += "<td>"+row.created_by+"</td>";
                    html += "<td>"+row.count+"</td>";
                    html += "<td>"+(row.status?'Yes':'No')+"</td>";
                    html += "<td>"+row.created_on+"</td>";
                    
                    html += "<td><span class='block_link anchor'><a href='javascript:void(0);' class='link' onclick='return re_print_acknowledgement_invoices(\""+$.trim(row.p_inv_no)+"\",\""+date_from+"\",\""+date_to+"\");'>Re-Generate</a></span></td></tr>";
                    i++;
                });
                html += "</table>";//html += "<pre>"+resp.lastqry;
            }
            else if(resp.status == 'fail') {
                html += resp.response;
            }
            
            $("#dlg_block").html(html).dialog("open").dialog('option', 'title', 'Show Printed Acknowledgement Log');

        },'json');
        return false;
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
 
    // Auto center the dialog boxes
    $(window).resize(function() { //on resize window center the dialog
        $("#dlg_block").dialog("option", "position", ['center', 'center']);
    });
// ]]>
</script>