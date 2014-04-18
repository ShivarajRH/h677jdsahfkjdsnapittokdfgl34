<style>
    h2 {width:60%;float:left;}
    .notification_blk
    {
        display:none;float:right;padding:4px;margin-top:23px;background: #f1f1f1;font-size: 16px;
    }
    .filters_block { margin-top:0px; float:right; margin-bottom:8px;}
    .filters_block .filter { float: left; padding: 0px 8px; }
    .filters_block .filter select { width:180px;margin-top:5px;font-size: 12px; }
     .filters_block b
     {
     	font-size: 13px;
     	margin:5px;
     }
     #insu_frm_date,#insu_to_date,#rech_frm_date,#rech_to_date,#fee_frm_date,#fee_to_date
     {
     	width:80px;
     	font-size: 11px;
     }
     .insu_date_filter,.rech_date_filter,.fee_date_filter
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
        .payment_pending { background:#F3F3CA;color:orange;font-weight:bold;font-size:13px;padding:4px 2px; }
        .view_insurance_lnk {float:right; margin-right: 25px;}
 </style>
 <div class="container">
	<h2>Manage Offers</h2>
        <div class="notification_blk" style=""></div>
		
		<div id="manage_offers_tab" style="width:100%;float:left">
			<ul><?php //echo site_url()."/admin/manage_offers/recharge_offers/0/0";#recharge_offers?>
				<li><a href="<?=site_url();?>/admin/jx_manage_offers_inner_view/insurance_offers/all/all/0" type="insurance_offers">Insurance Offers</a></li>
				<li><a href="<?php echo site_url()."/admin/jx_manage_offers_inner_view/recharge_offers/all/all/0";?>" type="recharge_offers">Recharge Offers</a></li>
				<li><a href="<?=site_url();?>/admin/jx_manage_offers_inner_view/fee_list/all/all/0" type="fee_list">Member with no offer</a></li>
			</ul>
		</div>
	</div>	
</div>
<script>
$(function() {
    $( "#manage_offers_tab" ).tabs({
      beforeLoad: function( event, ui ) {
        ui.panel.html('<div class="loading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Loading...</div>');
        
        ui.jqXHR.error(function() {
          ui.panel.html("<p>Error while loading...</p>");
        });
        
      }
    });
    // Pagination links
    $(".pagination_snip a").live("click",function(e){
            var url_str =$(this).attr("href");
            $( "#manage_offers_tab .ui-tabs-panel.ui-widget-content" ).html('<div class="loading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Loading...</div>');
            $( "#manage_offers_tab .ui-tabs-panel.ui-widget-content" ).load(url_str);
            return false;
    });
    // On Filter change
    $(".order_status_filter").live("change",function(e){
            load_contents();
    });
    $(".offer_status_filter").live("change",function(e){
            load_contents();
    });

});
function load_contents()
{
    var elt = $( "#manage_offers_tab .ui-tabs-panel.ui-widget-content" ).attr("area-hidden");
    
    var order_status = $(".order_status_filter",elt).find(":selected").val();
    var offer_status = $(".offer_status_filter",elt).find(":selected").val();
    
    var type = $( "#manage_offers_tab .ui-tabs-nav .ui-tabs-active a" ).attr("type");
    
    $( "#manage_offers_tab .ui-tabs-panel.ui-widget-content" ).html('<div class="loading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Loading...</div>');
    var url_str = site_url+'/admin/jx_manage_offers_inner_view/'+type+'/'+order_status+"/"+offer_status+"/0";
    $( "#manage_offers_tab .ui-tabs-panel.ui-widget-content" ).load(url_str);
    return false;
}
//==================================================================
</script>
<div id="dlg_update_member_insurance_details" style="display:none;">
    <h4 style="background-color:#F6F6F6;padding:5px;text-align:center;">Member Insurance Details</h4>
    <div id="insurance_option" title="Member Insurance Details" >
            <div id="crdet_insurance_blk">
            
		<div id="member_info_bloc">
			<form id="crdet_insurance" data-validate="parsley" method="post">
				<span class="form_label_wrap">Insurance Id:</span>
				<span class="form_input_wrap"><input class="" type="text" readonly="true" name="i_member_insu_id" id="i_member_insu_id" value=""></span> 
				
                                <span class="form_label_wrap">First Name <b class="red_star">*</b> :</span>
				<span class="form_input_wrap"><input class="max_width" type="text" name="i_memberfname" id="i_memberfname" value="" data-required="true"></span> 
				
				<span class="form_label_wrap">Last Name :</span>
				<span class="form_input_wrap"><input class="max_width" type="text" name="i_memberlname" id="i_memberlname" value="" ></span>
				
				<span class="form_label_wrap">Mobile <b class="red_star">*</b>: </span>
				<span class="form_input_wrap"><input class="max_width" type="text" name="i_membermob" id="i_membermob" value="" data-required="true"></span>
				
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
			</form>	
		</div>
	</div>
    </div>
</div>

<div id="dlg_register_mobile_details" style="display:none;">
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

<script>
    var refresh_time = 3000;

$('#insu_frm_date,#insu_to_date,#rech_frm_date,#rech_to_date,#fee_frm_date,#fee_to_date').datepicker();
//$('.insu_franchise_filter,.recharge_franchise_filter').chosen();
//$('.insu_territory_filter,.recharge_territory_filter').chosen();
//$('.insu_town_filter,.recharge_town_filter').chosen();

/**=====================================================================*/
/**==================< PROCESS MEMBER OFFERS START >=====================*/
/**=====================================================================*/
function process_offer(e)
{
    if( $(".chk_insurance:checked").length <= 0)
    {
        alert("Please check any one of offer");
        return false;
    }
    $(".chk_insurance:checked").each(function() {
            var im = $(this);
            var trEle = $(im).parents('tr:first');
            var member_id = trEle.attr('member_id');
            var offer_type = trEle.attr('offer_type');
            var transid_ref = trEle.attr('transid_ref');
            var fid = trEle.attr('franchise_id');
            //print(fid);
            
			if(confirm("Are you sure you want to process these offers?"))
		    {
	            $.post(site_url+'/admin/process_member_offer',{member_id:member_id,transid_ref:transid_ref,fid:fid,offer_type:offer_type},function(resp){
	                if(resp.status=="success")
	                {
	                        $('.notification_blk').html('Member information processed successfully').fadeIn().delay(refresh_time).fadeOut();
	                        //setTimeout(reloadpg,3000);
	                        //alert("Member information processed successfully");
	                        location.href=$(location).attr("href");
	                }else
	                {
	                        alert("Error: Failed to process Member information");
	                }
	            },'json');
			}
    });
    return false;
}

function process_recharge_offer(e)
{
    
    if( $(".chk_recharge:checked").length <= 0)
    {
        alert("Please check atleast one offer!");
        return false;
    }
    
    $(".chk_recharge:checked").each(function() {
            var im = $(this);
            var trEle = $(im).parents('tr:first');
            var member_id = trEle.attr('member_id');
            var offer_type = trEle.attr('offer_type');
            var transid_ref = trEle.attr('transid_ref');
            var fid = trEle.attr('franchise_id');
            //print(fid);
            
            if(confirm("Are you sure you want to process these offers?"))
            {
                    $.post(site_url+'/admin/process_member_offer',{member_id:member_id,transid_ref:transid_ref,fid:fid,offer_type:offer_type},function(resp){
                        if(resp.status=="success")
                        {
                                $('.notification_blk').html('Member information processed successfully').fadeIn().delay(refresh_time).fadeOut();
                                //setTimeout(reloadpg,3000);
                                //alert("Member information processed successfully");
                                location.href=$(location).attr("href");
                        }else
                        {
                                alert("Error: Failed to process Member information");
                        }
                    },'json');
            }
    });
    return false;
}
/**==================< PROCESS OFFERS ENDS >========================*/


/**=====================================================================*/
/**==================< UPDATE MEMBER DETAILS START >=====================*/
/**=====================================================================*/
$("#dlg_update_member_insurance_details").dialog({
    modal:true
    ,autoOpen:false
    ,height:"auto"
    ,width:700
    ,open:function(event,i) {
        var dlg=$(this);
        var insurance_id = dlg.data("insurance_id");
        $("#i_member_insu_id",dlg).val(insurance_id);
        
        $.post(site_url+"/admin/jx_get_insurance_det/"+insurance_id,{},function(resp) {
            if(resp.status == 'success')
            {
                var insu = resp.insurance_det;
                //i_memberfname i_memberlname i_membermob i_member_add crd_insurence_type proof_name crd_insurence_id crd_insurance_mem_address i_member_city i_member_pcode 
                //i_member_receipt_no i_member_receipt_amount i_member_receipt_date
                $("#i_memberfname",dlg).val(insu.first_name);
                $("#i_memberlname",dlg).val(insu.last_name);
                $("#i_membermob",dlg).val(insu.mob_no);
                $("#crd_insurence_type",dlg).val(insu.proof_type);

                $("#proof_type",dlg).val(insu.proof_type);
                $("#proof_name",dlg).val(insu.proof_name);
                $("#proof_id",dlg).val(insu.proof_id);
                $("#crd_insurance_mem_address",dlg).val(insu.proof_address);
                $("#i_member_city",dlg).val(insu.city);
                $("#i_member_pcode",dlg).val(insu.pincode);
                $("#i_member_receipt_no",dlg).val(insu.mem_receipt_no);
                $("#i_member_receipt_amount",dlg).val(insu.mem_receipt_amount);
                $("#i_member_receipt_date",dlg).val(insu.mem_receipt_date);
                
            }
            else
            {
                alert(resp.message);
            }
            
        },'json');
        
    }
    ,buttons:{
        "Update":function() {
            var dlg=$(this);
            //post form input
            $.post(site_url+"/admin/jx_put_insurance_det_update",$("#crdet_insurance").serialize(),function(resp) {
                if(resp.status == 'success')
                {
                    alert(resp.message);
                    dlg.dialog("close");
                    location.href=$(location).attr("href");
                }
                else
                {
                    alert(resp.message);
                }
            },'json');
            
        }
        ,"Close":function() {
            $(this).dialog("close");
        }
    }
    ,title: "Update member insurance details"
});//.css({ position: 'fixed' })

$("#i_member_receipt_date").datepicker();

function update_member_insurance_details(insurance_id)
{
    $("#dlg_update_member_insurance_details").data('insurance_id',insurance_id).dialog("open");
}
/**==================< UPDATE MEMBER DETAILS ENDS >========================*/

/**=====================================================================*/
/**==================< REGISTER MOBILE DETAILS START >========================*/
/**=====================================================================*/
$("#dlg_register_mobile_details").dialog({
    modal:true
    ,autoOpen:false
    ,height:"auto"
    ,width:500
    ,open:function(event,i) {
        var dlg=$(this);
        var pnh_member_id = dlg.data("pnh_member_id");
        $("#pnh_member_id",dlg).val(pnh_member_id);
        
        
    }
    ,buttons:{
        "Update":function() {
            var dlg=$(this);
            //post form input
            /*$.post(site_url+"/admin/jx_register_member_mobile",$("#crdet_insurance").serialize(),function(resp) {
                if(resp.status == 'success')
                {
                    alert(resp.message);
                    dlg.dialog("close");
                }
            },'json');
            */
        }
        ,"Close":function() {
            $(this).dialog("close");
        }
    }
    ,title: "Update member insurance details"
});
function update_member_details(pnh_member_id)
{
    $("#dlg_register_mobile_details").data('pnh_member_id',pnh_member_id).dialog("open");
}

/**==================< REGISTER MOBILE DETAILS ENDS >========================*/

/**=====================================================================*/
/**==================< CHECKBOX SELECT START >========================*/
/**=====================================================================*/
$(".chk_all_recharges").live("click",function(e) {
    if($(this).is(":checked"))
    {
        $(".chk_recharge").each(function() {
            $(this).attr("checked",true);
        });
    }
    else
    {
        $(".chk_recharge").each(function() {
            $(this).attr("checked",false);
        });
    }
    
});
$(".chk_all_insurances").live("click",function(e) {
    if($(this).is(":checked"))
    {
        $(".chk_insurance").each(function() {
            $(this).attr("checked",true);
        });
    }
    else
    {
        $(".chk_insurance").each(function() {
            $(this).attr("checked",false);
        });
    }
});
//===========< CHECKBOX SELECT END >=======================


/**=====================================================================*/
/**==================< CONFIRM FEEDBACK START >========================*/
/**=====================================================================*/
var max_rate_val = '<?= MAX_RATE_VAL; ?>';
function confirm_feedback(member_id)
{
    if(!confirm("Are you sure you want to update feedback status?")) {
        return false;
    }
        
    // Directly confirm the feedback for orders
    var rate_val = prompt("Please enter rate value between range of 1 to "+max_rate_val+" any number?");
    
    //get rate value
    if(rate_val == '')
    {
        alert("Please enter rate value between range of 1 to "+max_rate_val+" any number.");
        return false;
    }
    //alert(rate_val);
    if( isNaN(rate_val) )
    {
        alert("Please enter valid number.");
        return false;
    }
    
    if(rate_val > max_rate_val)
    {
        rate_val = max_rate_val;
    }
    
    $.post(site_url+"/admin/jx_confirm_feedback/"+member_id+"/"+rate_val,function(resp){
        if(resp.status == 'success')
        {
            alert(""+resp.message);
            location.href = $(location).attr("href"); //site_url+"/admin/"
        }
        else{
            alert(""+resp.message);
        }
    },"json");
}
//===========< CONFIRM FEEDBACK END >=======================

$(window).resize(function() {
    $("#dlg_update_member_insurance_details").dialog("option","position",["center","center"]); 
});

/**=====================================================================*/

$('.othrs_proofname').hide();
$("#crd_insurence_type").live('change',function(){
	if($(this).val()=='others')
	{
		$('.othrs_proofname').show();
	}
	else
	{
		$('.othrs_proofname').hide();
	}
});

</script>

<script>
/**=====================================================================*/
/**==================< FILTERS CODE START >========================*/
/**=====================================================================*/
// =========== INSURANCE FILTER ==========
$('.insu_franchise_filter').live("change",function(){
    var sort_franchise_id=$('.insu_franchise_filter').val();
    $('.insu_territory_filter').val(0).trigger('click');
    $('.insu_town_filter').val(0).trigger('click');
    var trname=$('.insurance_table');
    
    if(sort_franchise_id == 0)
    {
        trname.show();
    }
    else
    {
		trname.each(function() {
		  var fid=$(this).attr('franchise_id');   
		  if(parseInt(fid) == parseInt(sort_franchise_id) )
          {
              $(this).show();
          }
          else
          {
              $(this).hide();
          }
       });
    }
    return false;
});

$('.insu_territory_filter').live("change",function(){
    var sort_terr_id=$('.insu_territory_filter').val();
    var trname=$('.insurance_table');
   	$.getJSON(site_url+'/admin/jx_load_all_towns_byterrid/'+sort_terr_id+'/2','',function(resp){
		var town_html='';
		if(resp.status=='success')
		{
			town_html+='<option value="0">All</option>';
			$.each(resp.town_list,function(i,t){
				town_html+='<option value="'+t.id+'">'+t.town_name+'</option>';
			});
		}
		else
		{
			alert(resp.message);
		}
		$(".insu_town_filter").html(town_html).trigger("liszt:updated");
		//$(".recharge_town_filter").trigger('change');
 	});
 	
    if(sort_terr_id == 0)
    {
        trname.show();
    }
    else
    {
    	trname.each(function() {
		  var tr_id=$(this).attr('territory_id');   
		  if(parseInt(tr_id) == parseInt(sort_terr_id) )
          {
              $(this).show();
          }
          else
          {
              $(this).hide();
          }
		 });
    }
    return false;
});

$('.insu_town_filter').live("change",function(){
    var sort_town_id=$('.insu_town_filter').val();
    var trname=$('.insurance_table');
    
    $.getJSON(site_url+'/admin/jx_load_all_franch_bytownid/'+sort_town_id+'/2','',function(resp){
		var franch_html='';
		if(resp.status=='success')
		{
			franch_html+='<option value="0">All</option>';
			$.each(resp.fran_list,function(i,f){
				franch_html+='<option value="'+f.franchise_id+'">'+f.franchise_name+'</option>';
			});
		}
		else
		{
			alert(resp.message);
		}
		$(".recharge_franchise_filter").html(franch_html).trigger("liszt:updated");
		
 	});
 	
    if(sort_town_id == 0)
    {
        trname.show();
    }
    else
    {
    	
        trname.each(function() {
		  var tw_id=$(this).attr('town_id');   
		  if(parseInt(tw_id) == parseInt(sort_town_id) )
          {
              $(this).show();
          }
          else
          {
              $(this).hide();
          }

      });
    }
    return false;
});
// =========== RECHARGE FILTER ==========
$('.recharge_franchise_filter').live("change",function(){
	var sort_franchise_id=$('.recharge_franchise_filter').val();
    var trname=$('.recharge_table');
    if(sort_franchise_id == 0)
    {
        trname.show();
    }
    else
    {
		trname.each(function() {
		  var fid=$(this).attr('franchise_id');   
		  if(parseInt(fid) == parseInt(sort_franchise_id) )
          {
              $(this).show();
          }
          else
          {
              $(this).hide();
          }

       });
    }
    return false;
});

$('.recharge_territory_filter').live("change",function(){
    var sort_terr_id=$('.recharge_territory_filter').val();
    var trname=$('.recharge_table');
    $.getJSON(site_url+'/admin/jx_load_all_towns_byterrid/'+sort_terr_id+'/1','',function(resp){
		var town_html='';
		if(resp.status=='success')
		{
			town_html+='<option value="0">All</option>';
			$.each(resp.town_list,function(i,t){
				town_html+='<option value="'+t.id+'">'+t.town_name+'</option>';
			});
		}
		else
		{
			alert(resp.message);
		}
		$(".recharge_town_filter").html(town_html).trigger("liszt:updated");
		//$(".recharge_town_filter").trigger('change');
 	});
    
    if(sort_terr_id == 0)
    {
        trname.show();
    }
    else
    {
    	trname.each(function() {
		  var tr_id=$(this).attr('territory_id');   
		  if(parseInt(tr_id) == parseInt(sort_terr_id) )
          {
              $(this).show();
          }
          else
          {
              $(this).hide();
          }
		 });
    }
    return false;
});

$('.recharge_town_filter').live("change",function(){
    var sort_town_id=$('.recharge_town_filter').val();
    var trname=$('.recharge_table');
    
    $.getJSON(site_url+'/admin/jx_load_all_franch_bytownid/'+sort_town_id+'/1','',function(resp){
		var franch_html='';
		if(resp.status=='success')
		{
			franch_html+='<option value="0">All</option>';
			$.each(resp.fran_list,function(i,f){
				franch_html+='<option value="'+f.franchise_id+'">'+f.franchise_name+'</option>';
			});
		}
		else
		{
			alert("Error:"+resp.message);
		}
		$(".recharge_franchise_filter").html(franch_html).trigger("liszt:updated");
		
 	});
 	
        if(sort_town_id == 0)
        {
            trname.show();

        }
        else
        {
                trname.each(function() {
                      var tw_id=$(this).attr('town_id');   
                      if(parseInt(tw_id) == parseInt(sort_town_id) )
              {
                  $(this).show();
              }
              else
              {
                  $(this).hide();
              }

          });
        }
        return false;
});

// =========== FEE FILTER ==========
$('.fee_franchise_filter').live("change",function(){
	var sort_franchise_id=$('.fee_franchise_filter').val();
        var trname=$('.fee_table');
        if(sort_franchise_id == 0)
        {
            trname.show();
        }
        else
        {
		trname.each(function() {
		  var fid=$(this).attr('franchise_id');   
		  if(parseInt(fid) == parseInt(sort_franchise_id) )
          {
              $(this).show();
          }
          else
          {
              $(this).hide();
          }

       });
    }
    return false;
});

$('.fee_territory_filter').live("change",function(){
    var sort_terr_id=$('.fee_territory_filter').val();
    var trname=$('.fee_table');
    $.getJSON(site_url+'/admin/jx_load_all_towns_byterrid/'+sort_terr_id+'/1','',function(resp){
		var town_html='';
		if(resp.status=='success')
		{
			town_html+='<option value="0">All</option>';
			$.each(resp.town_list,function(i,t){
				town_html+='<option value="'+t.id+'">'+t.town_name+'</option>';
			});
		}
		else
		{
			alert(resp.message);
		}
		$(".fee_town_filter").html(town_html).trigger("liszt:updated");
		//$(".recharge_town_filter").trigger('change');
 	});
    
    if(sort_terr_id == 0)
    {
        trname.show();
    }
    else
    {
    	trname.each(function() {
		  var tr_id=$(this).attr('territory_id');   
		  if(parseInt(tr_id) == parseInt(sort_terr_id) )
          {
              $(this).show();
          }
          else
          {
              $(this).hide();
          }
		 });
    }
    return false;
});

$('.fee_town_filter').live("change",function(){
    var sort_town_id=$('.fee_town_filter').val();
    var trname=$('.fee_table');
    
    $.getJSON(site_url+'/admin/jx_load_all_franch_bytownid/'+sort_town_id+'/1','',function(resp){
		var franch_html='';
		if(resp.status=='success')
		{
			franch_html+='<option value="0">All</option>';
			$.each(resp.fran_list,function(i,f){
				franch_html+='<option value="'+f.franchise_id+'">'+f.franchise_name+'</option>';
			});
		}
		else
		{
			alert(resp.message);
		}
		$(".fee_franchise_filter").html(franch_html).trigger("liszt:updated");
		
 	});
 	
    if(sort_town_id == 0)
    {
        trname.show();
        
    }
    else
    {
    	trname.each(function() {
		  var tw_id=$(this).attr('town_id');   
		  if(parseInt(tw_id) == parseInt(sort_town_id) )
          {
              $(this).show();
          }
          else
          {
              $(this).hide();
          }

      });
    }
    return false;
});

// =========== DATE FILTER ==========
$('.insu_date_filter').live('click',function(){
	var from=$('#insu_frm_date').val();
	var to=$('#insu_to_date').val();
	var trname=$('.insurance_table');
	$.post(site_url+'/admin/jx_transids_delivered_status_bydate',{from:from,to:to,fids:insu_fids},function(resp){

            if(resp.status == 'success')
            {
                    var trname=$('.insurance_table');
                    $.each(resp.transids,function(i,t){
                            trname.each(function() {
                                var transid=$(this).attr('transid');
                                //alert(transid+"---"+t.transid);
                                if(t.transid == transid)
                                {
                                    $(this).show();
                                }
                                else
                                {
                                    $(this).hide();
                                }

                            });
                    });   
            }
            else
            {
                    alert("No details found.");
                    return false;
            }
	},'json');
});

$('.rech_date_filter').live('click',function(){
	
	var from=$('#rech_frm_date').val();
	var to=$('#rech_to_date').val();
	
	var trname=$('.insurance_table');
	$.post(site_url+'/admin/jx_transids_delivered_status_bydate',{from:from,to:to,fids:recharge_fids},function(resp){
			
            if(resp.status == 'success')
            {
                        var trname=$('.recharge_table');
                        $.each(resp.transids,function(i,t){
                                        trname.each(function() {
                                          var transid=$(this).attr('transid');
                                          //alert(transid+"---"+t.transid);
                                          if(t.transid == transid)
                                  {
                                      $(this).show();
                                  }
                                  else
                                  {
                                      $(this).hide();
                                  }

                              });
                        });
            }
            else
            {
                    alert("No details found");
                    return false;
            }
	},'json');
});

$('.fee_date_filter').live('click',function(){
	
	var from=$('#fee_frm_date').val();
	var to=$('#fee_to_date').val();
	
	var trname=$('.insurance_table');
	$.post(site_url+'/admin/jx_transids_delivered_status_bydate',{from:from,to:to,fids:fee_fids},function(resp){
			
            if(resp.status == 'success')
            {
                        var trname=$('.fee_table');
                        $.each(resp.transids,function(i,t){
                                        trname.each(function() {
                                          var transid=$(this).attr('transid');
                                          //alert(transid+"---"+t.transid);
                                          if(t.transid == transid)
                                  {
                                      $(this).show();
                                  }
                                  else
                                  {
                                      $(this).hide();
                                  }

                              });
                        });
            }
            else
            {
                    alert("No details found");
                    return false;
            }
	},'json');
});
/**==================< FILTERS CODE ENDS >========================*/
</script>
