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
     #insu_frm_date,#insu_to_date,#rech_frm_date,#rech_to_date
     {
     	width:80px;
     	font-size: 11px;
     }
     .insu_date_filter,.rech_date_filter
     {
     	font-size: 11px !important;
	    height: 18px !important;
	    line-height: 14.4px !important;
	    padding: 0;
     }
 </style>
 <div class="container">
	<h2>Manage Offers</h2>
        <div class="notification_blk" style=""></div>
		
		<div id="manage_offers_tab" style="width:100%;float:left">
			<ul>
				<li><a href="#recharge_offers">Recharge Offers</a></li>
				<li><a href="#insurance_offers">Insurance Offers</a></li>
			</ul>

		<div id="insurance_offers">
			<?php if($insu_franchisee_arr.length != 0) {?>
				<div class="filters_block">
					<?php $terr_list=$this->db->query("select * from pnh_m_territory_info where id in ($insu_territory_arr) order by territory_name asc"); ?>
 						<div class="filter">
			                <b>Territory : </b>
			                <select class="insu_territory_filter">
			                    <option value="0">All</option>
			                    <?php foreach($terr_list->result_array() as $t) { ?>
			                    	<option value="<?=$t['id'];?>"><?=$t['territory_name'];?></option>
			                    <?php } ?>
			                </select>
			            </div>
		            
		            <?php $town_list=$this->db->query("select * from pnh_towns where id in ($insu_town_arr) order by town_name asc"); ?>
		            	<div class="filter">
			                <b>Town :</b>
			                <select class="insu_town_filter">
			                    <option value="0">All</option>
			                    <?php foreach($town_list->result_array() as $t) { ?>
			                    	<option value="<?=$t['id'];?>"><?=$t['town_name'];?></option>
			                    <?php } ?>
			                </select>
		            	</div>
		            
					<?php $fran_list=$this->db->query("select * from pnh_m_franchise_info where franchise_id in ($insu_franchisee_arr) order by franchise_name asc"); ?>
 					 	<div class="filter">
			                <b>Franchise :</b>
			                <select class="insu_franchise_filter">
			                    <option value="0">All</option>
			                    <?php foreach($fran_list->result_array() as $f) { ?>
			                    	<option value="<?=$f['franchise_id'];?>"><?=$f['franchise_name'];?></option>
			                    <?php } ?>
			                </select>
			            </div>
		            
		            <div class="filter">
		            	<form style="margin-top:5px;">
			                <b>Delivery from :</b><input type="date" id="insu_frm_date">
			                <b>Delivery to :</b><input type="date" id="insu_to_date">
			                <button type="button" class="button button-tiny insu_date_filter">Go</button>
		                </form>
		            </div>
		        </div>
				
				<table class="datagrid smallheader noprint datagridsort" width="100%">
					<thead>
						<tr>
							<th width="1%">Sl No.</th>
							<th width="4%">Registered Date</th>
							<th width="6%">Member Name</th>
							<th width="10%">Franchise_name</th>
							<th width="4%">TransID</th>
							<th width="4%">Offer towards</th>
							<th width="4%">Insurance Amount</th>
							<th width="4%">Status</th>
							<th width="4%">Actions</th>									
						</tr>	
					</thead>
					
					<tbody>
						<?php foreach($offers_insurance as $i=>$offer){ ?>
							<tr class="insurance_table"  member_id="<?=$offer['member_id'];?>" date=<?=$offer['date'];?> territory_id="<?=$offer['territory_id'];?>" town_id="<?=$offer['town_id'];?>" franchise_id="<?=$offer['franchise_id'];?>" transid="<?=$offer['transid_ref'];?>" offer_type="<?=$offer['offer_type'];?>" offer_value="<?=$offer['offer_value'];?>">
						<td><?=++$i?></td>
                                                <td><?=format_datetime_ts($offer['created_on']);?></td>
						<td><a href="<?=site_url("admin/pnh_viewmember/".$offer['user_id']);?>" target="_blank"><?=$offer['first_name'];?></a></td>
						<td><a href="<?=site_url("admin/pnh_franchise/".$offer['franchise_id']);?>" target="_blank"><?=$offer['franchise_name'];?></a></td>
						<td><a href="<?=site_url("admin/trans/".$offer['transid_ref'])?>" target="_blank"><?=$offer['transid_ref'];?></a></td>
						<td>Rs. <?=$offer['offer_towards'];?></td>
						<td>Rs. <?=$offer['offer_value'];?></td>
                                                <td><?php
                                                    $arr_offer_type = array(1=>"Free Recharge",2=>"Free Insurance",3=>"N/A or Not Opted",4=>"Requested for Insurance");
                                                    $arr_delivery_status = array(0=>"Not delivered",1=>"Order Delivered");
                                                    echo $arr_delivery_status[$offer['delivery_status']];
									?>
                                 </td>
                                                <td>
                                                    
                                                    <?php if($offer['delivery_status']==1){ ?>
                                                        <input type="checkbox" name="" value="1">
                                                       <?php }
                                                            else echo '--'; ?>
						</td>
					</tr>
                        <?php } ?>
					</tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="9">
                                                    <?php //if($offer['delivery_status']==1){ ?>
                                                        <button type="button" class="button button-tiny button-action process_status" onclick="process_offer(this)" >Process</button>
                                                       <?php //} else 
                                                       echo '--'; ?>
                                                </td>
                                            </tr>
                                        </tfoot>
				</table>

				<?php }else { ?>
					<b>No Insurance Offers Found</b>
				<?php } ?>			
		</div>

		<div id="recharge_offers">
  
                     <?php	if($recharge_franchisee_arr.length != 0) {?>	
            	<div class="filters_block">
            		<?php $terr_list_rech=$this->db->query("select * from pnh_m_territory_info where id in ($recharge_territory_arr) order by territory_name asc"); ?>
			            <div class="filter">
			                <b>Territory : </b>
			                <select class="recharge_territory_filter">
			                    <option value="0">All</option>
			                    <?php foreach($terr_list_rech->result_array() as $t) { ?>
			                    	<option value="<?=$t['id'];?>"><?=$t['territory_name'];?></option>
			                    <?php } ?>
			                </select>
			            </div>
		            
		             <?php $town_list_rech=$this->db->query("select * from pnh_towns where id in ($recharge_town_arr) order by town_name asc"); ?>
		            	<div class="filter">
		                	<b>Town :</b>
		                	<select class="recharge_town_filter">
		                    	<option value="0">All</option>
			                    <?php foreach($town_list_rech->result_array() as $t) { ?>
			                    	<option value="<?=$t['id'];?>"><?=$t['town_name'];?></option>
			                    <?php } ?>
		                </select>
		            </div>
		            
					<?php $fran_list_rech=$this->db->query("select * from pnh_m_franchise_info where franchise_id in ($recharge_franchisee_arr) order by franchise_name asc"); ?>
 					 	<div class="filter">
			                <b>Franchise :</b>
			                <select class="recharge_franchise_filter">
			                	<option value="0">All</option>
			                    <?php foreach($fran_list_rech->result_array() as $f) { ?>
			                    	<option value="<?=$f['franchise_id'];?>"><?=$f['franchise_name'];?></option>
			                    <?php } ?>
			                </select>
			            </div>
		            
		            <div class="filter">
		            	<form style="margin-top:5px;">
			                <b>Delivery from :</b><input type="date" id="rech_frm_date">
			                <b>Delivery to :</b><input type="date" id="rech_to_date">
			                <button type="button" class="button button-tiny rech_date_filter">Go</button>
		                </form>
		            </div>
		        </div>
		
                                <table class="datagrid smallheader noprint datagridsort" width="100%">
                                        <thead>
                                                <tr>
                                                    <th width="1%">Sl No.</th>
                                                    <th width="4%">Registered Date</th>
                                                    <th width="6%">Member Name</th>
                                                    <th width="10%">Franchise Name</th>
                                                    <th width="4%">TransID</th>
                                                    <th width="4%">Offer Towards</th>
                                                    <th width="4%">Recharge Amount</th>
                                                    <th width="4%">Status</th>
                                                    <th width="4%">Actions</th>									
                                                </tr>	
                                        </thead>
                        
                                        <tbody>
                                        <?php foreach($offers_talktime as $i=>$offer){ ?>
                                                <tr class="recharge_table" territory_id="<?=$offer['territory_id'];?>" date=<?=$offer['date'];?> town_id="<?=$offer['town_id'];?>" member_id="<?=$offer['member_id'];?>" franchise_id="<?=$offer['franchise_id'];?>" transid="<?=$offer['transid_ref'];?>" offer_type="<?=$offer['offer_type'];?>" offer_value="<?=$offer['offer_value'];?>">
                                                        <td><?=++$i?></td>
                                                        <td><?=format_datetime_ts($offer['created_on']);?></td>
                                                        <td><a href="<?=site_url("admin/pnh_viewmember/".$offer['user_id'])?>" target="_blank"><?=$offer['first_name'];?></a></td>
                                                        <td><a href="<?=site_url("admin/pnh_franchise/".$offer['franchise_id']);?>" target="_blank"><?=$offer['franchise_name'];?></a></td>
                                                        <td><a href="<?=site_url("admin/trans/".$offer['transid_ref'])?>" target="_blank"><?=$offer['transid_ref'];?></a></td>
                                                        <td>Rs. <?=$offer['offer_towards'];?></td>
                                                        <td>Rs. <?=$offer['offer_value'];?></td>
                                                        <td><?php
                                                            $arr_offer_type = array(1=>"Free Recharge",2=>"Free Insurance",3=>"N/A or Not Opted",4=>"Requested for Insurance");
                                                            $arr_delivery_status = array(0=>"Not delivered",1=>"Order Delivered");
                                                            echo $arr_delivery_status[$offer['delivery_status']];
									?>
								</td>
                                                        <td>
                                    <?php if($offer['delivery_status']==1) { ?>
                                                            <button type="button" class="button button-tiny button-action process_status" onclick="process_offer(this)" >Process</button>
                                                               <?php }
                                                               else echo '--';
                                                               ?>
                                                        </td>
                                                </tr>
                            <?php } ?>
                                        </tbody>
                                </table>
	              <?php }else { ?>
					<b>No Recharge Offers Found</b>
				<?php } ?>	
		</div>
	</div>
</div>
<!--<div id="upload_insurance_docs" class="hide">
     <form target="insurance_attach_form_hndl" action="<?=site_url("/admin/jx_submit_insurance_attach");?>" name="insurance_attach_form" id="insurance_attach_form" enctype="multipart/form-data" method="post">
         <table>
             <tr>
                <td>Upload file :</td>
                <td><input type="file" name="attach" id="attach" /></td>
            </tr>
            <tr>
               <td>Enter Remarks :</td><td><textarea name="remarks" id="remarks" rows="6" cols="36"></textarea></td>
            </tr>
         </table>
     </form>
     <iframe id="insurance_attach_form_hndl" name="insurance_attach_form_hndl" style="width: 0px;height: 0px;"></iframe>
 </div>-->
 <style>
     .hide { display:none; }
     textarea,input { padding: 2px 4px; }
 </style>
 
<script>
$('#manage_offers_tab').tabs();
$('#insu_frm_date,#insu_to_date,#rech_frm_date,#rech_to_date').datepicker();
//$('.insu_franchise_filter,.recharge_franchise_filter').chosen();
//$('.insu_territory_filter,.recharge_territory_filter').chosen();
//$('.insu_town_filter,.recharge_town_filter').chosen();

var refresh_time = 3000;

/*function hndl_insurance_upload_response(resp)
{
    alert("OK");
    console.log(resp);
    if(resp.status=='error')
    {
        alert("Error:"+resp.response);
        return false;
    }
    else
    {
        
    }
}*/

$("#upload_insurance_docs").dialog({
    autoOpen:false
    ,width:600
    ,height:300
    ,buttons:{
        "Upload":function() {
           
           $("#insurance_attach_form").submit();
           
           //alert("Form Submitted");
           
           /*
                    if($("#remarks").val() == '')
                    {
                        alert("Please enter remarks.");
                        return false;
                    }
                    
                    $.post(site_url+"/admin/jx_submit_insurance_attach",$("#insurance_attach_form").serialize(),function(resp) {
                            print(resp);
                    },"json");
                    */
                }
        }
        ,title:"Upload documents form"
        ,close:function(){
            $(this).close();
        }
});




//process member offers
function process_offer(e)
{
    //$("#upload_insurance_docs").dialog("open");return false;
    
    var trEle = $(e).parents('tr:first');
    var member_id = trEle.attr('member_id');
    var offer_type = trEle.attr('offer_type');
    var transid_ref = trEle.attr('transid_ref');
    var fid = trEle.attr('franchise_id');
    
    var btn_elt = $(e);
   
    var tagid='';
    if(offer_type == 1)
        tagid ='#talktime_offers';
    else if(offer_type == 2)
        tagid ='#insurance_offers';
    
    if(confirm("Are you sure you want to process this offer?"))
    {
        btn_elt.html("Ready for process");
        //location.hash = tagid;
        $.post(site_url+'/admin/process_member_offer',{member_id:member_id,transid_ref:transid_ref,fid:fid,offer_type:offer_type},function(resp){
                if(resp.status=="error")
                {
                        alert("Error: Failed to process Member information");
                }else
                {
                        $('.notification_blk').html('Member information processed successfully').fadeIn().delay(refresh_time).fadeOut();
                        //setTimeout(reloadpg,3000);
                        //alert("Member information processed successfully");
                        location.href=$(location).attr("href");
                }
        },'json');
    }
    return false;
}

function reloadpg()
{
    location.href=$(location).attr("href");
}

$('.insu_franchise_filter').change(function(){
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

$('.insu_territory_filter').change(function(){
    var sort_terr_id=$('.insu_territory_filter').val();
    var trname=$('.insurance_table');
   	$.getJSON(site_url+'/admin/jx_load_all_towns_byterrid/'+sort_terr_id+'/2','',function(resp){
		var town_html='';
		if(resp.status=='error')
		{
			alert(resp.message);
		}
		else
		{
			town_html+='<option value="0">All</option>';
			$.each(resp.town_list,function(i,t){
				town_html+='<option value="'+t.id+'">'+t.town_name+'</option>';
			});
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

$('.insu_town_filter').change(function(){
    var sort_town_id=$('.insu_town_filter').val();
    var trname=$('.insurance_table');
    
    $.getJSON(site_url+'/admin/jx_load_all_franch_bytownid/'+sort_town_id+'/2','',function(resp){
		var franch_html='';
		if(resp.status=='error')
		{
			alert(resp.message);
		}
		else
		{
			franch_html+='<option value="0">All</option>';
			$.each(resp.fran_list,function(i,f){
				franch_html+='<option value="'+f.franchise_id+'">'+f.franchise_name+'</option>';
			});
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
    
$('.recharge_franchise_filter').change(function(){
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

$('.recharge_territory_filter').change(function(){
    var sort_terr_id=$('.recharge_territory_filter').val();
    var trname=$('.recharge_table');
    $.getJSON(site_url+'/admin/jx_load_all_towns_byterrid/'+sort_terr_id+'/1','',function(resp){
		var town_html='';
		if(resp.status=='error')
		{
			alert(resp.message);
		}
		else
		{
			town_html+='<option value="0">All</option>';
			$.each(resp.town_list,function(i,t){
				town_html+='<option value="'+t.id+'">'+t.town_name+'</option>';
			});
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

$('.recharge_town_filter').change(function(){
    var sort_town_id=$('.recharge_town_filter').val();
    var trname=$('.recharge_table');
    
    $.getJSON(site_url+'/admin/jx_load_all_franch_bytownid/'+sort_town_id+'/1','',function(resp){
		var franch_html='';
		if(resp.status=='error')
		{
			alert(resp.message);
		}
		else
		{
			franch_html+='<option value="0">All</option>';
			$.each(resp.fran_list,function(i,f){
				franch_html+='<option value="'+f.franchise_id+'">'+f.franchise_name+'</option>';
			});
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

    
$('.insu_date_filter').live('click',function(){
	
	var from=$('#insu_frm_date').val();
	var to=$('#insu_to_date').val();
	var fids=[];
		fids='<?php echo $insu_franchisee_arr ?>';
	 var trname=$('.insurance_table');
	$.post(site_url+'/admin/jx_transids_delivered_status_bydate',{from:from,to:to,fids:fids},function(resp){
    
	if(resp.status == 'error')
		{
			alert("No details found");
			return false;
	    }
		else
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
        },'json');
});

$('.rech_date_filter').live('click',function(){
	
	var from=$('#rech_frm_date').val();
	var to=$('#rech_to_date').val();
	var fids=[];
		fids='<?php echo $recharge_franchisee_arr ?>';
	 var trname=$('.insurance_table');
	$.post(site_url+'/admin/jx_transids_delivered_status_bydate',{from:from,to:to,fids:fids},function(resp){
			
	if(resp.status == 'error')
		{
			alert("No details found");
			return false;
    }
		else
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
			//$('.jq_alpha_sort_alphalist_itemlist').html(b_list);
		}
	},'json');
});

</script>