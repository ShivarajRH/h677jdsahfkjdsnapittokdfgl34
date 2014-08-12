/**
 * @author Shivaraj <shivaraj@storeking.in>
 * @desc	Manage Offers page scripts
 * @date	May_29_2014
 */
var refresh_time = 3000;
$(function() {
    $( "#manage_offers_tab" ).tabs({
      beforeLoad: function( event, ui ) {
        ui.panel.html('<div class="loading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Loading...</div>');

        ui.jqXHR.error(function() {
          ui.panel.html("<p>Error while loading...</p>");
        });
      },load:function( event, ui ) { /* After page load*/ }
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
    $(".payment_status_filter").live("change",function(e){
            load_contents();
    });
    $(".detail_status_filter").live("change",function(e){
            load_contents();
    });
    $(".feedback_status_filter").live("change",function(e){
            load_contents();
    });
    $(".date_filter_submit").live("click",function(e){
            load_contents();
    });
    $(".territory_filter").live("change",function(e){
            load_contents();
    });
    $(".town_filter").live("change",function(e){
            load_contents();
    });
    $(".franchise_filter").live("change",function(e){
            load_contents();
    });
});

function get_url()
{
        var elt = $("#"+ $( "#manage_offers_tab .ui-tabs-panel.ui-widget-content:visible" ).attr("id") );
        var type = $( "#manage_offers_tab .ui-tabs-nav .ui-tabs-active a" ).attr("type");
        
        var order_status = $(".order_status_filter",elt).find(":selected").val();
        var offer_status = $(".offer_status_filter",elt).find(":selected").val();
        var payment_status = $(".payment_status_filter",elt).find(":selected").val();
        var detail_status = $(".detail_status_filter",elt).find(":selected").val();
        var feedback_status = $(".feedback_status_filter",elt).find(":selected").val();
        var srh_no = urlencode( $.trim( $(".srh_no",elt).val() ) );
        var from=$('.date_from',elt).val();
		var to=$('.date_to',elt).val();
        
        if(from=='' || from==undefined) from=0;
        if(to=='' || to==undefined) to=0;
        if(srh_no=='' || srh_no==undefined) srh_no=0;
        var territory_id = $(".territory_filter",elt).find(":selected").val();
        var town_id = $(".town_filter",elt).find(":selected").val();
        var franchise_id = $(".franchise_filter",elt).find(":selected").val();
        
		if(offer_status == undefined) offer_status = 'all';
		if(payment_status == undefined)	payment_status = 'all';
		if(detail_status == undefined)	detail_status = 'all';
		if(feedback_status == undefined) feedback_status = 'all';
		
        var filter_url = type+'/'+order_status+"/"+offer_status+"/"+payment_status+"/"+detail_status+"/"+feedback_status+"/"+from+"/"+to+"/"+territory_id+"/"+town_id+"/"+franchise_id+"/"+srh_no;
//        print(filter_url);return false;
        return filter_url;
}

function load_contents()
{
        var filter_url = get_url();
        var url_str = site_url+'/admin/jx_manage_offers_inner_view/'+filter_url+"/0";
        
        $( "#manage_offers_tab .ui-tabs-panel.ui-widget-content" ).html('<div class="loading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Loading...</div>');
        $( "#manage_offers_tab .ui-tabs-panel.ui-widget-content" ).load(url_str);
        
        return false;
}
//==================================================================

/**=====================================================================*/
/**==================< PROCESS MEMBER OFFERS START >=====================*/
/**=====================================================================*/
$("#dlg_notification_blk").dialog({
	modal:true
	,autoOpen:false
	,height:"auto"
	,width:400
	,open:function(event,i) {
		var dlg=$(this);
		$('#insurance_ids',dlg).val("");
	}
	,buttons:{
		"Print All":function() {
			var dlg=$(this);
			$("#print_bulk_insurance").submit();
		}
		,"Close":function() {
			$(this).dialog("close");
		}
	}
	,title: "View Processed Insurance details"
});
function process_offer(e)
{
    if( $(".chk_insurance:checked").length <= 0) {
        alert("Select any offer to process");  return false;
    }
	var params = {};
		params.member_id = new Array();
		params.offer_type = new Array();
		params.transid_ref = new Array();
		params.franchise_id = new Array();
		params.insurance_id = new Array();
    $(".chk_insurance:checked").each(function() {
            var im = $(this);
            var trEle = $(im).parents('tr:first');
			
            var member_id = trEle.attr('member_id');
            var offer_type = trEle.attr('offer_type');
            var transid_ref = trEle.attr('transid');
            var franchise_id = trEle.attr('franchise_id');
            var insurance_id = trEle.attr('insurance_id');
				params.member_id.push(member_id);
				params.offer_type.push(offer_type);
				params.transid_ref.push(transid_ref);
				params.franchise_id.push(franchise_id);
				params.insurance_id.push(insurance_id);
    });
	
	if(confirm("Are you sure you want to process these offers?")) {
		$.post(site_url+'/admin/process_member_offer',params,function(resp){
			if(resp.status == "success")
			{
				if(resp.ref_ids.length)
				{
					var insurance_ids = resp.ref_ids.join(',');
					var dlg = $("#dlg_notification_blk");
							$(".resp_title",dlg).html(resp.message);
							$(".insurance_ids",dlg).val(insurance_ids);
					
					dlg.dialog("open");
				}
				
				//setTimeout(reloadpg,3000);
				load_contents();//location.href=$(location).attr("href");
			}else
			{
					alert("Error: Failed to process Insurance");
			}
		},'json');
	}
			
    return false;
}

/*function process_recharge_offer(e) {
    if( $(".chk_recharge:checked").length <= 0) {
        alert("Select any offer to process");
        return false;
    }
	var params = {};
		params.member_id = new Array();
		params.offer_type = new Array();
		params.transid_ref = new Array();
		params.franchise_id = new Array();
		params.insurance_id = new Array();
		
    $(".chk_recharge:checked").each(function() {
            var im = $(this);
            var trEle = $(im).parents('tr:first');
			
            var member_id = trEle.attr('member_id');
            var offer_type = trEle.attr('offer_type');
            var transid_ref = trEle.attr('transid');
            var franchise_id = trEle.attr('franchise_id');
			
			params.member_id.push(member_id);
			params.offer_type.push(offer_type);
			params.transid_ref.push(transid_ref);
			params.franchise_id.push(franchise_id);
    });
	
	if(confirm("Are you sure you want to process these offers?")) {
			$.post(site_url+'/admin/process_member_offer',params,function(resp){
				if(resp.status=="success")
				{
						$('.notification_blk').html('Member information processed successfully').fadeIn().delay(refresh_time).fadeOut();
						//setTimeout(reloadpg,3000);
						load_contents(); //location.href=$(location).attr("href");
				}else
				{
						alert("Error: Failed to process Member information");
				}
			},'json');
	}
    return false;
}*/
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
        var can_edit = dlg.data("can_edit");
        $("#i_member_insu_id",dlg).val(insurance_id);
        
        $.post(site_url+"/admin/jx_get_insurance_det/"+insurance_id,{},function(resp) {
            if(resp.status == 'success')
            {
                var insu = resp.insurance_det;
                var member_id = insu.mid;
                var franchise_id = insu.franchise_id;
                $("#i_member_id",dlg).val(member_id);
                $("#i_franchise_id",dlg).val(franchise_id);
                
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
                if(insu.feedback_status == 0 && insu.delivery_status == 1 )
                {
                        $(".confirm_feedback",dlg).html('<span class="form_label_wrap">Feedback:</span><span class="form_input_wrap">\n\
	                            &nbsp;&nbsp;&nbsp;<a class="button button-tiny button-primary" member_id="" onclick="confirm_feedback('+member_id+');">Confirm Feedback</a>\n\
	                    </span>');
                }
                else if(insu.feedback_status == 1 )
                {
                    $(".confirm_feedback",dlg).html('<span class="form_label_wrap">Feedback:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; '+insu.feedback_value+'&nbsp;');
                }
                else if(  insu.delivery_status == 0 )
                {
                    $(".confirm_feedback",dlg).html('<span class="form_label_wrap">Feedback:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Not Delivered');
                }
                else
                {
                	$(".confirm_feedback",dlg).html('<span class="form_label_wrap">Feedback:</span><span class="form_input_wrap">\n\
                            &nbsp;&nbsp;&nbsp;<a class="button button-tiny button-primary" member_id="" onclick="confirm_feedback('+member_id+');">Confirm Feedback</a>\n\
                    </span>');
                }
                
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
            var can_edit = dlg.data("can_edit");

            if(can_edit == 0)
            {
                alert("The offers already processed, You cant update insurance details now.");
                return false;
            }
            var mob_no = $("#i_membermob",dlg).val();
            
            if( mob_no == '' )
            {
                alert("Mobile Number is required.");
                return false;
            }
            if( isNaN(mob_no) )
            {
                alert("Invalid mobile number");
                return false;
            }
            
            //post form input
            $.post(site_url+"/admin/jx_update_insurance_det",$("#crdet_insurance").serialize(),function(resp) {
                if(resp.status == 'success')
                {
                    alert(resp.message);
                    dlg.dialog("close");
                    load_contents(); //location.href=$(location).attr("href");
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

function update_member_insurance_details(insurance_id,can_edit)
{
    $("#dlg_update_member_insurance_details").data({'insurance_id':insurance_id,"can_edit":can_edit}).dialog("open");
}
/**==================< UPDATE MEMBER DETAILS ENDS >========================*/

/**=====================================================================*/
/**==================< REGISTER MOBILE DETAILS START >========================*/
/**=====================================================================*/
/*$("#dlg_register_mobile_details").dialog({
    modal:true
    ,autoOpen:false
    ,height:"auto"
    ,width:500
    ,open:function(event,i) {
        var dlg=$(this);
        var pnh_member_id = dlg.data("pnh_member_id");
        $("#pnh_member_id",dlg).val(pnh_member_id);
        
        $.post(site_url+"/admin/jx_get_member_det",{mid:pnh_member_id},function(resp) {
            if(resp.status == 'success')
            {
                var det=resp.i_memdet;
                alert(det.first_name);
            }
        },"json");
    },buttons:{
        "Update":function() {
            var dlg=$(this);
            //post form input
            $.post(site_url+"/admin/jx_register_member_mobile",$("#crdet_insurance").serialize(),function(resp) {
                if(resp.status == 'success')
                {
                    alert(resp.message);
                    dlg.dialog("close");
                }
            },'json');
            
        }
        ,"Close":function() {
            $(this).dialog("close");
        }
    },title: "Update member insurance details"
});
function update_member_details(pnh_member_id) {
    $("#dlg_register_mobile_details").data('pnh_member_id',pnh_member_id).dialog("open");
}
*/
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
function confirm_feedback(member_id)
{
    if(!confirm("Are you sure you want to update feedback status?")) {
        return false;
    }
        
    // Directly confirm the feedback for orders
    var rate_val = prompt("Please enter rate value between range of 1 to "+max_rate_val+" any number.");
    rate_val = parseInt(rate_val);
    //get rate value
    if(rate_val == '' || rate_val == null || isNaN(rate_val) || rate_val < 0 || rate_val > max_rate_val)
    {
        alert("Please enter rate value between range of 1 to "+max_rate_val+" any number.");
        return false;
    }
    //if(rate_val > max_rate_val){rate_val = max_rate_val;}
    
    $.post(site_url+"/admin/jx_confirm_feedback/"+member_id+"/"+rate_val,function(resp){
        if(resp.status == 'success')
        {
            alert(""+resp.message);
            load_contents(); //location.href = $(location).attr("href");
        }
        else{
            alert(""+resp.message);
        }
    },"json");
}
//===========< CONFIRM FEEDBACK END >=======================

//===================< OTHER TYPE OF PROOF NAME >=======================================
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
//===================< END OTHER TYPE OF PROOF NAME >=======================================

/**==================< RE-SEND FEEDBACK CODE STARTS >========================*/
function resend_mem_feedback(user_id,franchise_id)
{
    if(!confirm("Are you sure you want to Re-Send Feedback Request to member?"))
    {
        return false;
    }
    
    $.post(site_url+"/admin/jx_resend_mem_feedback_sms",{user_id:user_id,franchise_id:franchise_id},function(resp) {
            if(resp.status == 'success')
            {
                alert(resp.message);
                load_contents();
            }
            else
            {
                alert(resp.message)
            }
    },'json');
}
/**==================< RE-SEND FEEDBACK CODE ENDS >========================*/

    /**==================< MARK AS RECHARGED CODE STARTS >========================*/
    $("#dlg_mark_as_recharged").dialog({
        modal:true
        ,autoOpen:false
        ,height:"auto"
        ,width:400
        ,open:function(event,i) {
            var dlg=$(this);
            $('#recharge_remarks',dlg).val("");
        }
        ,buttons:{
            "Update":function() {
                var dlg=$(this);
                var key_elt = dlg.data("key_elt");
                
                    var im = $(key_elt);
                    var trEle = $(im).parents('tr:first');
                    var member_id = trEle.attr('member_id');
                    var offer_type = trEle.attr('offer_type');
                    var transid_ref = trEle.attr('transid');
                    var fid = trEle.attr('franchise_id');
                    
                    var remarks = $('#recharge_remarks',dlg).val();
                    if(remarks == '')
                    {
                        alert("Please enter remarks");
                        return false;
                    }
                    $.post(site_url+'/admin/process_member_offer',{member_id:member_id,transid_ref:transid_ref,fid:fid,offer_type:offer_type,remarks:remarks},function(resp){
                        if(resp.status=="success")
                        {
                                $('.notification_blk').html('Member information processed successfully').fadeIn().delay(refresh_time).fadeOut();
                                //setTimeout(reloadpg,3000);
                                $("#dlg_mark_as_recharged").dialog("close");
                                load_contents(); 
                        }else
                        {
                                alert(resp.message);
                        }
                    },'json');
                
            }
            ,"Close":function() {
                $(this).dialog("close");
            }
        }
        ,title: "Update Recharge Remarks"
    });
    

    function mark_as_recharged(e)
    {
        $("#dlg_mark_as_recharged").data("key_elt",e).dialog("open");
    }
    /**==================< MARK AS RECHARGED CODE ENDS >========================*/
    
    $(window).on("resize scroll",function() {
        $("#dlg_update_member_insurance_details,#dlg_mark_as_recharged,#dlg_notification_blk,#dlg_insurance_print_log").dialog("option","position",["center","center"]); 
    });
    //==
    
    // =============< Export options STARTS>================================
    $(".export_lnk").live("click",function() {
            
            var filter_url = get_url();
            var url_str = site_url+'/admin/export_offers_data/'+filter_url+"/0";
            
            window.location.href = url_str;
            print(url_str);
    });
    // =============< Export options Ends >================================
	
    // =============< INSURANCE PRINT LOG STARTS >================================
	$("#dlg_insurance_print_log").dialog({
        modal:true
        ,autoOpen:false
        ,height:"auto"
        ,width:460
        ,buttons:{
            "Close":function() {
                $(this).dialog("close");
            }
        }
        ,title: "Insurance Print Log"
    });
	
    function show_insurance_print_log(insurance_id) {
		$("#dlg_insurance_print_log h3").html("Print log for insurance id "+insurance_id);
		var html_data = '';
		
		$.post(site_url+'/admin/insurance_print_count/'+insurance_id,{},function(resp) {
			if(resp.status=="success")
			{
				$.each(resp.log,function(i,log) {
					if(log.status == 1)
						sts_msg = 'Yes';
					else
						sts_msg = 'No';
					html_data+='<tr><td>'+(++i)+'</td><td>'+$.ucfirst(log.username)+'</td><td>'+log.printcount+'</td><td>'+log.last_printed_on+'</td><td>'+sts_msg+'</td></tr>';
				});
				$("#dlg_insurance_print_log tbody").html(html_data);
				$("#dlg_insurance_print_log").dialog("open");
			}
			else
			{
				alert(resp.message);
			}
		},"json");
		
	}
    // =============< INSURANCE PRINT LOG ENDS >================================
	
// =============< IS INSURANCE SHIPPED CODE START >================================
	$(".is_insurance_shipped").live("change",function() {
		var im = $(this);
		var trEle = $(im).parents('tr:first');
		var status = 0;
		var sno = trEle.attr('sno');
		var insurance_id = trEle.attr('insurance_id');
		
		if(im.is(":checked") == true)
		{
			if(!confirm("Is Insuranceid #"+insurance_id+" Shipped?"))
			{
				return false;
			}
			$.post(site_url+"/admin/insurance_ship_status_update_jx",{sno:sno},function(rdata) {
				if(rdata.status == 'success')
				{
					status = 1;
					alert(""+rdata.message);
				}
				else
				{
					alert(""+rdata.message);
				}
			},"json");
		}
		else if(status == 1 && im.is(":checked") == false)
		{
			alert("You have already confirmed shipment status.");
		}
		
	});
// =============< IS INSURANCE SHIPPED CODE ENDS >================================

function srch_insurance_frm(e){
	load_contents();
	return false;
}

/**
 * Javascript function to encode url string
 * @param string str
 * @returns string
 */
function urlencode(str)
{
	return encodeURIComponent(str);
}

/**
 * Function to reset filters without page reload
 */
function fn_reset_filters()
{
	var elt = $("#"+ $( "#manage_offers_tab .ui-tabs-panel.ui-widget-content:visible" ).attr("id") );
	//var type = $( "#manage_offers_tab .ui-tabs-nav .ui-tabs-active a" ).attr("type");
	$(".order_status_filter",elt).val('all');
	$(".offer_status_filter",elt).val('all');
	$(".payment_status_filter",elt).val('all');
	$(".detail_status_filter",elt).val('all');
	$(".feedback_status_filter",elt).val('all');
	$(".srh_no",elt).val('');
	$('.date_from',elt).val('');
	$('.date_to',elt).val('');
	$(".territory_filter",elt).val(0);
	$(".town_filter",elt).val(0);
	$(".franchise_filter",elt).val(0);
	load_contents();
	return false;
}