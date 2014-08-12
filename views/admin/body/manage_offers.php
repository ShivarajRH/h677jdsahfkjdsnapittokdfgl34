<style>
    h2 {width:60%;float:left;}
    .notification_blk {
        display:none;float:right;padding:4px;margin-top:23px;background: #f1f1f1;font-size: 16px;
    }
    .filters_block { margin-top:0px; margin-bottom:8px; width: 100%; display: block;}
    .filters_block .filter { float: left; background-color: #FCF5F5; border:1px solid #dddddd; margin: 5px 5px; padding: 11px 13px; }
    .filters_block .filter select { width:180px;margin-top:5px;font-size: 12px; }
     .filters_block b
     {
     	font-size: 13px;
     	margin:5px;
     }
     #date_from,#date_to
     {
     	width:80px;
     	font-size: 11px;
     }
     .date_filter_submit
     {
     	font-size: 11px !important;
	    height: 18px !important;
	    line-height: 14.4px !important;
	    padding: 0;
     }
     .hide { display:none; }
     textarea,input { padding: 2px 4px; }
     .process_status {
         margin:20px;
     }
    .date_filter_submit {
        color: #381212 !important;
        cursor: pointer;
    }
    .button {
		color: #FFFFFF !important;
        cursor: pointer;
    }
    .block_notify {
        background: #95DB95;padding:3px 6px;border-radius:3px; width: auto; float: left;margin-top: 7px;
        color: white;font-size: 12px;
    }
    /************************ Insurance details form css ***********************************/
    #insurance_option span
    {
            margin-top:5px;
    }
    #crdet_insurance .form_label_wrap
    {
            float: left;
        font-size: 13px;
        font-weight: bold;
        height: 30px;
        text-align: right;
        width: 30%;
    }
    #crdet_insurance .form_input_wrap
    {
             float: right;
        text-align: left;
        width: 70%;
        height: 30px;
            font-size: 11px;
        font-weight: bold;
    }
    #crdet_insurance .form_input_wrap .max_width
    {
            width:40% !important;
    }
    .payment_pending {color:orange;font-weight:bold;font-size:13px;padding:4px 2px; }
    .view_insurance_lnk {float:right; margin-right: 25px;}
    .block-name
    { 
        padding: 4px 5px;
        background-color: aliceblue;
        text-align:center;
    }
    .details_pending {
        background: #F8F8F2;
        color: rgb(235, 131, 111);
        font-weight: bold;
        font-size: 11px;
        padding: 4px 2px;
    }
    .delivery_pending {
        background: #F1F1D9;
        color: #754D4D;
        font-weight: bold;
        font-size: 13px;
        padding: 4px 2px;
    }
    .export_block { font-weight: bold;margin: 6px 0 0px 1px;}
    .export_lnk { color:#000000 !important; }
	
 </style>
 <div class="container">
	<h2>Manage Offers</h2>
	<div class="btn_reset_filters fl_right button button-flat-highlight" style="margin: 0 8% 0 0;" onclick="return fn_reset_filters();">Reset Filters</div>
	<div class="notification_blk" style=""></div>

	<div id="manage_offers_tab" style="width:100%;float:left">
		<ul>
			<li><a href="<?=site_url();?>/admin/jx_manage_offers_inner_view/insurance_offers/all/all/all/all/all/0/0/0/0/0/0/0" type="insurance_offers">Insurance Offers</a></li>
			<li><a href="<?php echo site_url()."/admin/jx_manage_offers_inner_view/recharge_offers/all/all/all/all/all/0/0/0/0/0/0/0";?>" type="recharge_offers">Recharge Offers</a></li>
			<li><a href="<?=site_url();?>/admin/jx_manage_offers_inner_view/fee_list/all/all/all/all/all/0/0/0/0/0/0/0" type="fee_list">Member with no offer</a></li>
		</ul>
	</div>
 </div>	
<!--===========================================< Dialog Box html Code STARTS >=========================================== -->
<div  style="display:none;">
    <div id="dlg_update_member_insurance_details">
        <h4 style="background-color:#F6F6F6;padding:5px;text-align:center;">Member Insurance Details</h4>
        <div id="insurance_option" title="Member Insurance Details" >
                <div id="crdet_insurance_blk">

                    <div id="member_info_bloc">
                            <form id="crdet_insurance" data-validate="parsley" method="post">
                                    <span class="form_label_wrap">Insurance Id:</span>
                                    <span class="form_input_wrap"><input class="" type="text" readonly="true" name="i_member_insu_id" id="i_member_insu_id" value="">
                                        <input class="max_width" type="hidden" name="i_member_id" id="i_member_id" value="" max-width="12" data-required="true">
                                        <input class="max_width" type="hidden" name="i_franchise_id" id="i_franchise_id" value="" max-width="12" data-required="true">
                                    </span> 
                                    
                                    <!-- ===================< Member Details >==================== -->
                                    <div class="clear block-name">Member Details</div>
                                    
                                    <span class="form_label_wrap">Mobile <b class="red_star">*</b>: </span>
                                    <span class="form_input_wrap"><input class="max_width" type="text" name="i_membermob" id="i_membermob" value="" maxlength="10" data-required="true"></span>
                                    
                                    <span class="form_label_wrap">First Name <b class="red_star">*</b> :</span>
                                    <span class="form_input_wrap">
                                        <input class="max_width" type="text" name="i_memberfname" id="i_memberfname" value="" data-required="true">
                                    </span>

                                    <span class="form_label_wrap">Last Name :</span>
                                    <span class="form_input_wrap"><input class="max_width" type="text" name="i_memberlname" id="i_memberlname" value="" ></span>

 
                                    <!-- ===================< Insurance Details >==================== -->
                                    <div class="clear block-name">Insurance Details</div>
                                    
                                    <span class="form_label_wrap">Proof Type <b class="red_star">*</b> :</span>
                                    <span class="form_input_wrap">
                                            <select name="crd_insurence_type" class="max_width" id="crd_insurence_type">
                                                        <option value="">Select</option>
                                                        <?php $insurance_types=$this->db->query("select * from insurance_m_types order by name asc")->result_array();
                                                                if($insurance_types){
                                                                foreach($insurance_types as $i_type){
                                                        ?>
                                                                <option value="<?php echo $i_type['id']?>"><?php echo $i_type['name']?></option>
                                                        <?php }}?>
                                                        <option value="others">Others</option>
                                            </select>
                                    </span>
                                    <span class="othrs_proofname form_label_wrap">Proof Name <b class="red_star">*</b>:</span>
                                    <span class="othrs_proofname form_input_wrap"><input class="max_width" type="text" name="proof_name" id="proof_name" value=""></span>

                                    <span class="form_label_wrap">Proof Id <b class="red_star">*</b>:</span>
                                    <span class="form_input_wrap"><input class="max_width" type="text" name="proof_id" id="proof_id" value=""></span>

                                    <span class="form_label_wrap" style="height:63px !important">Proof Address <b class="red_star">*</b>:</span>
                                    <span class="form_input_wrap" style="height:63px !important"><textarea class="max_width" name="crd_insurance_mem_address" id="crd_insurance_mem_address"></textarea></span>

                                    <span class="form_label_wrap">City  <b class="red_star">*</b> :</span>
                                    <span class="form_input_wrap"><input class="max_width" type="text" name="i_member_city" id="i_member_city" value=""></span>

                                    <span class="form_label_wrap">PinCode :</span>
                                    <span class="form_input_wrap"><input class="max_width" type="text" name="i_member_pcode" id="i_member_pcode" value=""></span>

                                    <span class="form_label_wrap">Retailer Invoice No. <b class="red_star">*</b>:</span>
                                    <span class="form_input_wrap"><input class="max_width" type="text" name="i_member_receipt_no" id="i_member_receipt_no" value=""></span>

                                    <span class="form_label_wrap">Retailer Invoice Amount <b class="red_star">*</b>:</span>
                                    <span class="form_input_wrap"><input class="max_width" type="text" name="i_member_receipt_amount" id="i_member_receipt_amount" value=""></span>

                                    <span class="form_label_wrap">Retailer Invoice Date <b class="red_star">*</b>:</span>
                                    <span class="form_input_wrap"><input class="max_width" type="text" name="i_member_receipt_date" id="i_member_receipt_date" value=""></span>

                                    <span class="form_label_wrap confirm_feedback"></span>
                                    
                            </form>
                    </div>
            </div>
        </div>
    </div>

    <div id="dlg_register_mobile_details">
        <h4 style="background-color:#F6F6F6;padding:5px;text-align:center;">Register Member Details</h4>
        <div id="insurance_option" title="Payable Insurance" >
                <div id="crdet_insurance_blk">

                    <div id="member_info_bloc">
                            <form id="mem_register_form" data-validate="parsley" method="post">
                                    <input class="" type="hidden" name="i_pnh_member_id" id="i_pnh_member_id" value="">

                                    <span class="form_label_wrap">Mobile No:</span>
                                    <span class="form_input_wrap"><input class="" type="text" name="member_mobile_no" id="member_mobile_no" value=""></span>
                            </form>	
                    </div>
            </div>
        </div>
    </div>

    <div id="dlg_mark_as_recharged">
        <form>
            <div><label>Remarks : </label></div>
            <textarea name="" id="recharge_remarks" cols="26" rows="5" style="width:98%"></textarea>
        </form>
    </div>

	<div id="dlg_notification_blk">
		<h3 class="resp_title"></h3>
		<form id="print_bulk_insurance" action="<?=site_url('admin/insurance_print_view');?>" method="post" target="_blank">
			<input type="hidden" name="insurance_ids" class="insurance_ids" value="">
		</form>
	</div>
	<div id="dlg_insurance_print_log" >
		<h3></h3>
		<table class="datagrid" width="100%">
			<thead>
				<tr>
					<th>#</th>
					<th>Username</th>
					<th>Print Count</th>
					<th>Last printed On</th>
					<th>Print Status</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>
 <!--===========================================< Dialog Box html Code ENDS >===========================================-->
<script type="text/javascript" src="<?=base_url()?>/min/index.php?g=offers_js&<?php echo strtotime(date('Y-m-d'));?>&1=1"></script>
<script>
// <![CDATA[
   var max_rate_val = '<?= MAX_RATE_VAL; ?>';
// ]]>
</script>