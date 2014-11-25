window.onload = function(){
	filter_form_submit();
}


$(document).ready(function() {
    //FIRST RUN
    var reg_date = "<?php echo date('m/d/Y',  time()*60*60*24);?>";
    
    $( "#frm_dt").datepicker({
         changeMonth: true,
         dateFormat:'yy-mm-dd',
         numberOfMonths: 1,
         maxDate:0,
//         minDate: new Date(reg_date),
           onClose: function( selectedDate ) {
             $( "#to_dt" ).datepicker( "option", "minDate", selectedDate ); //selectedDate
         }
       });
    $( "#to_dt" ).datepicker({
        changeMonth: true,
         dateFormat:'yy-mm-dd',
//         numberOfMonths: 1,
         maxDate:0,
         onClose: function( selectedDate ) {
           $( "#frm_dt" ).datepicker( "option", "maxDate", selectedDate );
         }
    });

    
    
});

$("select[name='terri']").change(function(){
	var sel_terri=$(this).val();
	 var twn_html="";
	 twn_html+='<option value="0">All</option>';
	 $.post(site_url+"/admin/jx_suggest_townbyterrid/"+sel_terri,function(resp){
		 $.each(resp.towns,function(i,a){
			 twn_html+='<option value="'+a.id+'">'+a.town_name+'</option>';
		 });
		 $("#town").html(twn_html);
	 },'json');
	
});

$("select[name='town']").change(function(){
	
	var sel_twn=$(this).val();
	var sel_terr=$("#terri").val();
	var fran_html='';
	fran_html+='<option value=0>All</option>';
	 $.post(site_url+"/admin/jx_suggest_fran/"+sel_terr+'/'+sel_twn,function(resp){
		 $.each(resp.franchise,function(i,a){
			 fran_html+='<option value="'+a.franchise_id+'">'+a.franchise_name+'</option>';
		 });
		 $("#franchisee").html(fran_html);
	 },'json');
});


function filter_form_submit()
{
	var terrid = $('select[name="terri"]').val()*1;
	var twnid = $('select[name="town"]').val()*1;
	var frid = $('select[name="franchisee"]').val()*1;
	var order_frm=$('select[name="order_frm"]').val()*1;
	var frm_dt=$("input[name='frm_dt']").val();
	var to_dt=$("input[name='to_dt']").val();
	var transid=$("input[name='transid']").val();
	if($("#cnfrm_status:checked").val()==2 && (frm_dt=='' || to_dt==''))
		{alert("Please input date range");return false;}
	else
		load_unconfirmedorderlist('');
	return false;
}


function load_unconfirmedorderlist()
{
	
	colcount=12;
	var terrid = $('select[name="terri"]').val()*1;
	var twnid = $('select[name="town"]').val()*1;
	var frid = $('select[name="franchisee"]').val()*1;
	var order_frm=$('select[name="order_frm"]').val()*1;
	var frm_dt=$("input[name='frm_dt']").val();
	var to_dt=$("input[name='to_dt']").val();
	var cnfrm_status=$("#cnfrm_status:checked").val();
	var transid=$("input[name='transid']").val();

	var trans_html='';
	
	terrid = isNaN(terrid)?0:terrid;
	twnid = isNaN(twnid)?0:twnid;
	frid = isNaN(frid)?0:frid;
	if(frm_dt=='')
		frm_dt=0;
	if(to_dt=='')
		to_dt=0;
	$('#uncrorder_list tbody').html('<tr><td colspan="'+colcount+'"><div align="center"><img src="'+base_url+'/images/loading_bar.gif'+'"></div></td></tr>');
	$('#uncrorder_list .pagination').html('');
		$.post(site_url+'/admin/jx_unconfirmd_orderlist',{terrid:terrid,twnid:twnid,frid:frid,order_frm:order_frm,frm_dt:frm_dt,to_dt:to_dt,cnfrm_status:cnfrm_status,transid:transid},function(resp){
			$(".total_orders").html('<b>Total Orders : '+resp.ttl_res+'</b>');	
		 if(resp.status=='error')
		 {
			 $('#uncrorder_list tbody').html('<tr><td colspan="'+colcount+'" align="center"><div><b>No Orders Found</b></div></td></tr>');
			 return false;
		 }
		if(resp.status=='success')
			{	
				$.each(resp.trans_list,function(i,a){
					
					
			
					if(!$('select[name="franchisee"] option#franchisee_'+a.franchise_id).length){
						if(a.franchise_id!=undefined){
							$('select[name="franchisee"]').append('<option id="franchisee_'+a.franchise_id+'" value="'+a.franchise_id+'">'+a.franchise_name+'</option>');
						}
					}
					
					trans_html+='<tr class="uncr_ordr_list">';
					if(cnfrm_status!=2)
						{
						trans_html+='<td><input type="checkbox" class="uncfrm_transid" value="'+a.transid+'"></td>';
						}
					else
						{
						trans_html+='<td></td>';
						}
					trans_html+='<td><a href="'+site_url+'/admin/pnh_franchise/'+a.franchise_id+'" target="_blank">'+a.franchise_name +'</a><!--<br><span class="level_wrapper" style="font-size: 11px;color:'+resp.fran_exp[a.franchise_id].f_color+'"><b>'+resp.fran_exp[a.franchise_id].f_level+'</b></span><br><span class="span_count_wrap">Uncleared amount :'+resp.uncleared_amt[a.franchise_id]+'</span>---!></td>';
					trans_html+='<td>'+a.time +'</td>';
					trans_html+='<td><a href="'+site_url+'/admin/trans/'+a.transid+'" target="_blank">'+a.transid +'</td>';
					trans_html+='<td>'+a.amount +'</td>';
					trans_html+='<td style="padding:0px;"><table class="subdatagrid" cellpadding="0" cellspacing="0"><thead><th>MemberID</th><th>ITEM</th><th>QTY</th><th>Amount</th></thead>';
					$.each(resp.uncr_ord_itemlist[i],function(b,c){
						trans_html+='<tr>';
						trans_html+='<td>'+c.member_id+'</td>';
						trans_html+='<td><a href="'+site_url+'/admin/pnh_deal/'+c.itemid+'" target="_blank">'+ c.name+'</a></td>';
						trans_html+='<td>'+ c.quantity+'</td>';
						trans_html+='<td>'+ c.i_price+'</td>';
					});
					trans_html+='</tr>';
					trans_html+='</table></td>';
					trans_html+='<td>'+ resp.uncleared_amt[a.franchise_id]+'</td>';
					trans_html+='<td>'+ resp.pending_amount[a.franchise_id]+'</td>';
					trans_html+='<td>'+ resp.open_orderamt[a.franchise_id]+'</td>';
					if(cnfrm_status!=2)
					{
						trans_html+='<td><textarea name="uncrtrans_remarks" class="transid_remarks" style="width:50;height:25;" value=""></textarea></td>';
						trans_html+='<td><a href="javascript:void(0)" class="button button-tiny button-rounded button-action " onclick="approve_trans(this)" transid="'+a.transid+'">Approve</a> <a href="javascript:void(0)" class="button button-tiny button-rounded button-caution" onclick="reject_trans(this)"  transid="'+a.transid+'">Reject</a></td>';
						$("#confrm_status_bloc").show();
					}
					else
					{
						trans_html+='<td>'+ a.remarks+'</td>';
						trans_html+='<td><b>Rejected By :</b>'+ a.rejected_by+'<p><b>Rejected on :</b>'+ a.rejected_on+'</p></td>';
						$("#confrm_status_bloc").hide();
						
					}
					trans_html+='</tr>';
					
				
					});		
	
			}
				
		$('#uncrorder_list tbody').html(trans_html);
		
		$('.orders_display_log').html('<span><b>Total Orders :' +resp.ttl_res+'</b></span>');
		
	},'json');
}

$(".chk_all").click(function(){
	if($(this).attr("checked"))
		$(".uncfrm_transid:visible").attr("checked",true);
	else
		$(".uncfrm_transid:visible").attr("checked",false);
});

function reject_trans(ele)
{
	transid=$(ele).attr('transid');
	remarks=$(ele).closest('tr').find(".transid_remarks").val();

	$.post(site_url+"/admin/jx_update_anauth_trans/",{transid:transid,remarks:remarks},function(resp){
		if(resp.status=='error')
		{
			alert(resp.msg);
			return false;
		}
		else
		{
			$(ele).closest('tr').hide();
		}
			
	},'json');
	
	filter_form_submit();
	return false; 
	
}


function approve_trans(ele)
{
	transid=$(ele).attr('transid');
	remarks=$(ele).closest('tr').find(".transid_remarks").val();
	$.post(site_url+"/admin/jx_update_auth_trans/",{transid:transid,remarks:remarks},function(resp){
		if(resp.status=='error')
		{
			alert(resp.msg);
			return false;
		}
		else
		{
			$(ele).closest('tr').hide();
		}
			
	},'json');
	
	filter_form_submit();
	return false; 
}
var sel_transids=[];
var sel_transremarks=[];
function confirm_ordrstatus(status)
{
	if($(".uncfrm_transid:checked").length==0)
	{alert("Select atleast one transaction");return false;}

	$(".uncfrm_transid:checked").each(function(){
		tr=$($(this).parents("tr").get(0));
		sel_transids.push($(this).val());
		if($('.transid_remarks').length)
		sel_transremarks.push($('.transid_remarks',tr).val());
	});
	sel_transids.join(',');
	sel_transremarks.join(',');

	$('#uncrorder_list tbody').html('<tr><td colspan="'+colcount+'"><div align="center"><img src="'+base_url+'/images/loading_bar.gif'+'"></div></td></tr>');

	$.post(site_url+"/admin/jx_bulk_update_unconfirmed_trans/",{transid:sel_transids,remarks:sel_transremarks,status:status},function(resp){
		if(resp.status=='error')
		{
			alert(resp.msg);
			return false;
		}
		else
		{
			filter_form_submit();
			//return false;
		}
			
	});
	return false; 
	
}