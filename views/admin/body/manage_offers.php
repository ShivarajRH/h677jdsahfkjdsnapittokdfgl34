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
				<li><a href="#fee_list">Member with no offer</a></li>
			</ul>
                        <!-- ====================================< INSURANCE BLOCK  >==================================== -->
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
							<th width="4%">Actions <br><label for="chk_all_insurances">Check All</label><input type="checkbox" name="chk_all_insurances" id="chk_all_insurances" class="chk_all_insurances"/></th>									
					   </tr>	
                                        </thead>
					
					<tbody>
						<?php foreach($offers_insurance as $i=>$offer){ ?>
						<tr class="insurance_table"  member_id="<?=$offer['member_id'];?>" date=<?=$offer['date'];?> territory_id="<?=$offer['territory_id'];?>" town_id="<?=$offer['town_id'];?>" franchise_id="<?=$offer['franchise_id'];?>" transid="<?=$offer['transid_ref'];?>" offer_type="<?=$offer['offer_type'];?>" offer_value="<?=$offer['offer_value'];?>">
							<td><?=++$i?></td>
	                        <td><?=format_datetime($offer['created_on']);?></td>
							<td><a href="<?=site_url("admin/pnh_viewmember/".$offer['user_id']);?>" target="_blank"><?=$offer['first_name'];?></a></td>
							<td><a href="<?=site_url("admin/pnh_franchise/".$offer['franchise_id']);?>" target="_blank"><?=$offer['franchise_name'];?></a></td>
							<td><a href="<?=site_url("admin/trans/".$offer['transid_ref'])?>" target="_blank"><?=$offer['transid_ref'];?></a></td>
							<td>Rs. <?=$offer['offer_towards'];?></td>
							<td> Rs. <?php echo $offer['offer_value']."&nbsp;&nbsp;&nbsp;";
                                                
                                                            if($offer['process_status'] == '1') { ?>
                                                                   <a href="<?=site_url("admin/insurance_print_view/".$offer['insurance_id']);?>" target="blank" style="float:right; margin-right: 25px;">View</a>
                                                            <?php }
                                                                //else echo '--';?>
                                                        </td>
                                                        <td><?php
                                                                $arr_offer_type = array(1=>"Free Recharge",2=>"Free Insurance",3=>"N/A or Not Opted",4=>"Requested for Insurance");
                                                                $arr_delivery_status = array(0=>"Not delivered",1=>"Order Delivered");
                                                                                                    echo $arr_delivery_status[$offer['delivery_status']];
                                                                                            ?>
                                                         </td>
                                                         <td>
                                                                <?php 
                                                                $have_insu_details = $this->db->query("SELECT COUNT(*) AS t FROM pnh_member_insurance WHERE ( proof_id= '' OR proof_type='' OR proof_address=''
                                                                            OR first_name='' OR city='' OR mem_receipt_no='' OR mem_receipt_date='' OR mem_receipt_amount='') AND sno= ? ",$offer['insurance_id'])->row()->t;
                                                                
                                                                if($have_insu_details != 0)
                                                                {
                                                                            echo '<span style="color:orange;font-weight:bold;font-size:13px"><a class="button button-tiny button-action" onclick="update_member_insurance_details(\''.$offer['insurance_id'].'\');">Update Member Details</a></span>';
                                                                }
                                                                else
                                                                {
                                                                    if($offer['feedback_status'] == 1 && $offer['delivery_status'] == 1 && $offer['process_status'] == 0)
                                                                    {
?>
                                                                        <input type="checkbox" name="chk_insurance" class="chk_insurance" value="1"> Process
<?php                                                               }
                                                                    else if($offer['process_status'] == "1")
                                                                    {
                                                                            echo "<span style='color:green;font-weight:bold;font-size:13px'>Processed</span>";
                                                                    }
                                                                    else if($offer['process_status'] == "0" && $offer['feedback_status'] == "0" && $offer['delivery_status'] == "1")
                                                                    {
                                                                            echo '<span style="color:orange;font-weight:bold;font-size:13px"><a class="button button-tiny button-primary" onclick="confirm_feedback(\''.$offer['member_id'].'\');">Confirm Feedback</a></span>';
                                                                    }
                                                                    else
                                                                        echo '--'; 
                                                                }
                                                            ?>
							</td>
							</tr>
                        <?php } ?>
					</tbody>
				</table>
                                <div align="right">
                                    <?php //if($offer['delivery_status']==0){ ?>
                                        <button type="button" class="button button-tiny button-action process_status" onclick="process_offer(this)" >Process</button>
                                    <?php //} else echo '--'; ?>
                                </div>

				<?php }else { ?>
					<b>No Insurance Offers Found</b>
				<?php } ?>			
			</div>
                        <!-- ====================================< RECHARGE BLOCK >==================================== -->
			<div id="recharge_offers">
  
  	          <?php	if( count($recharge_franchisee_arr) != 0) {?>	
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
                                    <th width="4%">Actions<label for="chk_all_recharges">Check All</label><input type="checkbox" name="chk_all_recharges" id="chk_all_recharges" class="chk_all_recharges"/></th>									
                            </tr>	
                        </thead>
                        
                        <tbody>
                        <?php  foreach($offers_talktime as $i=>$offer){ ?>
                         	<tr class="recharge_table" territory_id="<?=$offer['territory_id'];?>" date=<?=$offer['date'];?> town_id="<?=$offer['town_id'];?>"  member_id="<?=$offer['member_id'];?>" franchise_id="<?=$offer['franchise_id'];?>" transid="<?=$offer['transid_ref'];?>" offer_type="<?=$offer['offer_type'];?>" offer_value="<?=$offer['offer_value'];?>">
                                <td><?=++$i?></td>
                                <td><?=format_datetime($offer['created_on']);?></td>
                                <td><a href="<?=site_url("admin/pnh_viewmember/".$offer['user_id'])?>" target="_blank"><?=$offer['first_name'];?></a></td>
                                                        <td><a href="<?=site_url("admin/pnh_franchise/".$offer['franchise_id']);?>" target="_blank"><?=$offer['franchise_name'];?></a></td>
                                <td><a href="<?=site_url("admin/trans/".$offer['transid_ref'])?>" target="_blank"><?=$offer['transid_ref'];?></a></td>
                                <td>Rs. <?=$offer['offer_towards'];?></td>
                                <td>Rs. <?=$offer['offer_value'];?></td>
                                <td><?php
                                                            //$arr_offer_type = array(1=>"Free Recharge",2=>"Free Insurance",3=>"N/A or Not Opted",4=>"Requested for Insurance");
	                                    $arr_delivery_status = array(0=>"Not delivered",1=>"Order Delivered");
										echo $arr_delivery_status[$offer['delivery_status']];
									?>
								</td>
                                <td>
                                   <?php if($offer['feedback_status'] == 1 && $offer['delivery_status'] == 1 && $offer['process_status'] == 0) { ?>
                                                <input type="checkbox" name="chk_recharge" class="chk_recharge" value="1"> Process
                                   <?php }
										else if($offer['process_status'] == "1"){
											echo "<span style='color:green;font-weight:bold;font-size:13px'>Processed</span>";
										}
										else if($offer['process_status'] == "0" && $offer['feedback_status'] == "0" && $offer['delivery_status'] == "1"){
											echo "<span style='color:orange;font-weight:bold;font-size:13px'>No Feedback</span>";
										}
										else if($offer['offer_type'] == "3" && $offer['mem_fee_applicable']== "1" && $offer['feedback_status'] == "0" && $offer['delivery_status'] == "0"){
											echo "<span style='color:orange;font-weight:bold;font-size:13px'>-N/A-</span>";
										} 
										else
											echo '--'; 
                                                        ?>
								</td>
                         	</tr>
                            <?php } ?>
                        </tbody>
                </table>
	              <?php }else { ?>
					<b>No Recharge Offers Found</b>
				<?php } ?>
				<div align="right">
                    <?php //if($offer['delivery_status']==0){ ?>
                        <button type="button" class="button button-tiny button-action process_status" onclick="process_recharge_offer(this)" >Process</button>
                    <?php //} else echo '--'; ?>
                </div>	
		</div>
		
                <!-- ====================================< MEMBER FEE BLOCK  >==================================== -->
		<div id="fee_list">
				
				<table class="datagrid smallheader noprint datagridsort" width="100%">
					<thead>
	                   <tr>
							<th width="1%">Sl No.</th>
							<th width="4%">Registered Date</th>
							<th width="6%">Member Name</th>
							<th width="10%">Franchise_name</th>
							<th width="4%">TransID</th>
							<th width="4%">Offer towards</th>
							<!--<th width="4%">Insurance Amount</th>-->
							<th width="4%">Status</th>
							<th width="4%">Actions <br><label for="chk_all_insurances">Check All</label><input type="checkbox" name="chk_all_insurances" id="chk_all_insurances" class="chk_all_insurances"/></th>									
					   </tr>	
	                </thead>
					
					<tbody>
						<?php foreach($member_fee_list as $i=>$offer){ ?>
						<tr class="insurance_table"  member_id="<?=$offer['member_id'];?>" date=<?=$offer['date'];?> territory_id="<?=$offer['territory_id'];?>" town_id="<?=$offer['town_id'];?>" franchise_id="<?=$offer['franchise_id'];?>" transid="<?=$offer['transid_ref'];?>" offer_type="<?=$offer['offer_type'];?>" offer_value="<?=$offer['offer_value'];?>">
							<td><?=++$i?></td>
	                        <td><?=format_datetime($offer['created_on']);?></td>
							<td><a href="<?=site_url("admin/pnh_viewmember/".$offer['user_id']);?>" target="_blank"><?=$offer['first_name'];?></a></td>
							<td><a href="<?=site_url("admin/pnh_franchise/".$offer['franchise_id']);?>" target="_blank"><?=$offer['franchise_name'];?></a></td>
							<td><a href="<?=site_url("admin/trans/".$offer['transid_ref'])?>" target="_blank"><?=$offer['transid_ref'];?></a></td>
							<td>Rs. <?=$offer['offer_towards'];?></td>
                            <td><?php
                                    $arr_offer_type = array(1=>"Free Recharge",2=>"Free Insurance",3=>"N/A or Not Opted",4=>"Requested for Insurance");
                                    $arr_delivery_status = array(0=>"Not delivered",1=>"Order Delivered");
                                        echo $arr_delivery_status[$offer['delivery_status']];
                                ?>
                             </td>
                             <td>
                                 <?php
										echo 'Rs. 50 '; 
                                                    ?>
							</td>
							</tr>
                        <?php } ?>
					</tbody>
				</table>
            </div>
	</div>	
</div>

 <style>
     .hide { display:none; }
     textarea,input { padding: 2px 4px; }
     .process_status {
         margin:20px;
     }
 </style>
 
<script>
var refresh_time = 3000;
$('#manage_offers_tab').tabs();
$('#insu_frm_date,#insu_to_date,#rech_frm_date,#rech_to_date').datepicker();
//$('.insu_franchise_filter,.recharge_franchise_filter').chosen();
//$('.insu_territory_filter,.recharge_territory_filter').chosen();
//$('.insu_town_filter,.recharge_town_filter').chosen();

//process member offers
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
    });
    return false;
}

function process_recharge_offer(e)
{
    
    if( $(".chk_recharge:checked").length <= 0)
    {
        alert("Please check any one of offer");
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
    });
    return false;
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
                alert("No details found.");
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
$(".chk_all_insurances").live("click",function(e) {
    if($(this).is(":checked"))
    {
        //alert("is ckecked");
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


$(".chk_all_recharges").live("click",function(e) {
    if($(this).is(":checked"))
    {
        //alert("is ckecked");
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
var max_rate_val = '<?= MAX_RATE_VAL; ?>';
function confirm_feedback(member_id)
{
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
</script>

<div id="dlg_update_member_insurance_details" style="display:none;">
    <h4 style="background-color:#F6F6F6;padding:5px;text-align:center;">Member Insurance Details</h4>
    <div id="insurance_option" title="Payable Insurance" >
            <div id="crdet_insurance_blk">
            
		<div id="member_info_bloc">
			<form id="crdet_insurance" data-validate="parsley" method="post">
				<span class="form_label_wrap">Insurance Id:</span>
				<span class="form_input_wrap"><input class="" type="text" readonly="true" name="i_member_insu_id" id="i_member_insu_id" value=""></span> 
				
                                <span class="form_label_wrap">First Name <span class="red_star">*</span>:</span>
				<span class="form_input_wrap"><input class="max_width" type="text" name="i_memberfname" id="i_memberfname" value="" data-required="true"></span> 
				
				<span class="form_label_wrap">Last Name :</span>
				<span class="form_input_wrap"><input class="max_width" type="text" name="i_memberlname" id="i_memberlname" value="" ></span>
				
				<span class="form_label_wrap">Mobile <span class="red_star">*</span>:</span>
				<span class="form_input_wrap"><input class="max_width" type="text" readonly="true" name="i_membermob" id="i_membermob" value="" data-required="true"></span>
				
				<span class="form_label_wrap">Proof Type:</span>
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
		 		<span class="othrs_proofname form_label_wrap">Proof Name :</span>
				<span class="othrs_proofname form_input_wrap"><input class="max_width" type="text" name="proof_name" id="proof_name" value=""></span>
				
				<span class="form_label_wrap">Proof Id:</span>
				<span class="form_input_wrap"><input class="max_width" type="text" name="crd_proof_val" id="crd_proof_val" value=""></span>
				
				<span class="form_label_wrap" style="height:63px !important">Proof Address:</span>
				<span class="form_input_wrap" style="height:63px !important"><textarea class="max_width" name="crd_insurance_mem_address" id="crd_insurance_mem_address"></textarea></span>
				
				<span class="form_label_wrap">City :</span>
				<span class="form_input_wrap"><input class="max_width" type="text" name="i_member_city" id="i_member_city" value=""></span>
				
				<span class="form_label_wrap">PinCode :</span>
				<span class="form_input_wrap"><input class="max_width" type="text" name="i_member_pcode" id="i_member_pcode" value=""></span>
				
				<span class="form_label_wrap">Member Receipt No. :</span>
				<span class="form_input_wrap"><input class="max_width" type="text" name="i_member_receipt_no" id="i_member_receipt_no" value=""></span>
				
				<span class="form_label_wrap">Franchise Receipt Amount :</span>
				<span class="form_input_wrap"><input class="max_width" type="text" name="i_member_receipt_amount" id="i_member_receipt_amount" value=""></span>
				
				<span class="form_label_wrap">Franchise Receipt Date :</span>
				<span class="form_input_wrap"><input class="max_width" type="text" name="i_member_receipt_date" id="i_member_receipt_date" value=""></span>
			</form>	
		</div>
	</div>
    </div>
</div>

<script>
    $("#i_member_receipt_date").datepicker();
    
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
                $("#crd_proof_val",dlg).val(insu.crd_proof_val);
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
                }
            },'json');
            
        }
        ,"Close":function() {
            $(this).dialog("close");
        }
    }
    ,title: "Update member insurance details"
});//.css({ position: 'fixed' })

function update_member_insurance_details(insurance_id)
{
    $("#dlg_update_member_insurance_details").data('insurance_id',insurance_id).dialog("open");
}
$(window).resize(function() {
       $("#dlg_update_member_insurance_details").dialog("option","position",["center","center"]); 
    });

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
<style>
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
</style>