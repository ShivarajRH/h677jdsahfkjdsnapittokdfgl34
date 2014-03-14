<style>
    h2 {width:60%;float:left;}
    /**PROGRESS BAR CODE START*/
    form { display: block; margin: 20px auto; background: #eee; border-radius: 10px; padding: 15px ;width: 400px;}
    .progress { position:relative; width:400px; border: 1px solid #ddd; padding: 1px; border-radius: 3px; height: 2px;}
    .bar { background-color: #B4F5B4; width:0%; height:2px; border-radius: 3px; }
    .percent { position:absolute; display:inline-block; top:3px; left:48%; }
    #status{margin-top: 30px;}
    /**PROGRESS BAR CODE END*/
    
    .notification_blk
    {
        display:none;float:right;padding:4px;margin-top:23px;background: #f1f1f1;font-size: 16px;
    }
    .filters_block { margin-top:0px; float:right; }
    .filters_block .filter { float: left; padding: 0px 8px; }
    .filters_block .filter select { width:225px; }
 </style>
 <?php foreach($offers_insurance as $insu)
 {
    $fran_insu_list[$insu['franchise_id']]['franchise_id']= $insu['franchise_id'];
    $fran_insu_list[$insu['franchise_id']]['franchise_name']= $insu['franchise_name'];
 }
 
 foreach($offers_talktime as $rechg)
 {
     $fran_insu_list[$rechg['franchise_id']]['franchise_id']= $rechg['franchise_id'];
     $fran_insu_list[$rechg['franchise_id']]['franchise_name']= $rechg['franchise_name'];
 }
 ?>
<div class="container">
	<h2>Manage Offers</h2>
        <div class="notification_blk" style=""></div>
        
        <div class="filters_block">
            
            <div class="filter">
                <b>Select Franchise</b>:<select class="franchise_filter">
                    <option value="0"> --All-- </option>
                    <?php 
                    foreach($fran_insu_list as $f)
                    { ?>
                    <option value="<?=$f['franchise_id'];?>"><?=$f['franchise_name'];?></option>
                    <?php } ?>
                </select>
            </div>
            
            <div class="filter">
                <b>Select Territory</b>:<select class="franchise_filter">
                    <option value="0"> --All-- </option>
                    <?php
                    foreach($fran_insu_list as $f)
                    { ?>
                    <option value="<?=$f['franchise_id'];?>"><?=$f['franchise_name'];?></option>
                    <?php } ?>
                </select>
            </div>
            
        </div>
        
	<div id="manage_offers_tab" style="width:100%;float:left">
		<ul>
			<li><a href="#recharge_offers">Recharge Offers</a></li>
			<li><a href="#insurance_offers">Insurance Offers</a></li>
                </ul>
		<div id="insurance_offers">
			<table class="datagrid smallheader noprint datagridsort" width="100%">
				<thead>
                                       <tr>
						<th width="4">#</th>
						<th width="10%">Registered Date</th>
						<th width="">Member Name</th>
						<th width="">TransID</th>
						<th width="">Offer towards</th>
						<th width="">Insurance Amount</th>
                                                <th width="">Status</th>
						<th width="">Actions</th>									
					</tr>	
                                </thead>
				<tbody>
				<?php foreach($offers_insurance as $i=>$offer)
                                    {
                                    ?>
					<tr class="insurance_table"  member_id="<?=$offer['member_id'];?>" franchise_id="<?=$offer['franchise_id'];?>" transid="<?=$offer['transid_ref'];?>" offer_type="<?=$offer['offer_type'];?>" offer_value="<?=$offer['offer_value'];?>">
						<td><?=++$i?></td>
                                                <td><?=format_datetime_ts($offer['created_on']);?></td>
						<td><a href="<?=site_url("admin/pnh_viewmember/".$offer['user_id']);?>" target="_blank"><?=$offer['first_name'];?></a></td>
						<td><a href="<?=site_url("admin/trans/".$offer['transid_ref'])?>" target="_blank"><?=$offer['transid_ref'];?></a></td>
						<td>Rs. <?=$offer['offer_towards'];?></td>
						<td>Rs. <?=$offer['offer_value'];?></td>
                                                <td><?php
                                                    $arr_offer_type = array(1=>"Free Recharge",2=>"Free Insurance",3=>"N/A or Not Opted",4=>"Requested for Insurance");
                                                    $arr_delivery_status = array(0=>"Not delivered",1=>"Order Delivered");

                                                    echo $arr_delivery_status[$offer['delivery_status']];

                                                    ?></td>
                                                <td>
                                                   <?php
                                                        if($offer['delivery_status']==1)
                                                        {
                                                     ?>
                                                    <button type="button" class="button button-tiny button-action process_status" onclick="process_offer(this)" >Process</button>
                                                        <!--<button type="button" class="button button-tiny button-caution discard_offer" onclick="discard_offer(this)" >Discard</button>-->
                                                       <?php }
                                                            else echo '--'; ?>
						</td>
					</tr>
                                        <?php
                                        }
                                        ?>
				</tbody>
			</table>
		</div>
		<div id="recharge_offers">
                     <!--   <ul>
                                <li><a href="#new_offers">New Offers</a></li>
                                <li><a href="#referred_offers">Referred Offers</a></li>
                        </ul><div id="new_offers">-->
                                <table class="datagrid smallheader noprint datagridsort" width="100%">
                                        <thead>
                                                <tr>
                                                        <th width="4">#</th>
                                                        <th width="10%">Registered Date</th>
                                                        <th width="">Member Name</th>
                                                        <th width="">Transid</th>
                                                        <th width="">Offer Towards</th>
                                                        <th width="">Recharge Amount</th>
                                                        <th width="">Status</th>
                                                        <th width="">Actions</th>									
                                                </tr>	
                                        </thead>
                                        <tbody>
                                        <?php 
                                            foreach($offers_talktime as $i=>$offer)
                                            {
                                            ?>
                                                <tr class="recharge_table" member_id="<?=$offer['member_id'];?>" franchise_id="<?=$offer['franchise_id'];?>" transid="<?=$offer['transid_ref'];?>" offer_type="<?=$offer['offer_type'];?>" offer_value="<?=$offer['offer_value'];?>">
                                                        <td><?=++$i?></td>
                                                        <td><?=format_datetime_ts($offer['created_on']);?></td>
                                                        <td><a href="<?=site_url("admin/pnh_viewmember/".$offer['user_id'])?>" target="_blank"><?=$offer['first_name'];?></a></td>
                                                        <td><a href="<?=site_url("admin/trans/".$offer['transid_ref'])?>" target="_blank"><?=$offer['transid_ref'];?></a></td>
                                                        <td>Rs. <?=$offer['offer_towards'];?></td>
                                                        <td>Rs. <?=$offer['offer_value'];?></td>
                                                        <td><?php
                                                            $arr_offer_type = array(1=>"Free Recharge",2=>"Free Insurance",3=>"N/A or Not Opted",4=>"Requested for Insurance");
                                                            $arr_delivery_status = array(0=>"Not delivered",1=>"Order Delivered");

                                                            echo $arr_delivery_status[$offer['delivery_status']];

                                                            ?></td>
                                                        <td>
                                                            <?php
                                                                if($offer['delivery_status']==1)
                                                                {
                                                             ?>
                                                            <button type="button" class="button button-tiny button-action process_status" onclick="process_offer(this)" >Process</button>
                                                                <!--<button type="button" class="button button-tiny button-caution discard_offer" onclick="discard_offer(this)" >Discard</button>-->
                                                               <?php }
                                                               else echo '--';
                                                               ?>
                                                        </td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                </table>
                        <!--</div>-->
<!--                        <div id="referred_offers">
                                <table class="datagrid smallheader noprint" width="100%">
                                                <tr>
                                                        <th width="4%">Sl No.</th><th width="4%">Member Id</th><th width="4%">Referred Members</th><th width="4%">Recharge Amount</th><th width="4%">Actions</th>									
                                                </tr>	
                                        <?php /*foreach($referral_offers as $i=>$offer) {?>
                                                <tr member_id="<?=$offer['member_id'];?>" transid="<?=$offer['transid_ref'];?>" offer_type="<?=$offer['offer_type'];?>" offer_value="<?=$offer['offer_value'];?>">
                                                        <td><?=++$i?></td><td><?=$offer['referred_by'];?></td><td><?=$offer['num_referred'];?></td><td><?=$offer['offer_value'];?></td><td><button type="button" class="button button-tiny button-action" onclick="process_offer(this)" >Process</button>
                                                                <button type="button" class="button button-tiny button-caution discard_offer" onclick="discard_offer(this)" >Discard</button>
                                                        </td>
                                                </tr>
                                            <?php
                                            }*/?>
                                </table>
                        </div>-->
		</div>
	</div>
        
</div>
 <div id="upload_insurance_docs" class="hide">
     <form action="<?=site_url("/admin/jx_submit_insurance_attach");?>" name="insurance_attach_form" id="insurance_attach_form" enctype="multipart/form-data" method="post">
         <table>
             <tr>
            <td>
                <input type="hidden" name="MAX_FILE_SIZE" value="1000000">
                Upload file :</td>
                <td><input type="file" name="attach" id="attach" /></td>
         </tr>
         <tr>
            <td>Enter Remarks :</td><td><textarea name="remarks" id="remarks"></textarea></td>
         </tr>
         <tr>
             <td colspan="2">
                <div class="progress">
                    <div class="bar"></div >
                    <div class="percent">0%</div >        
                    <div id="status"></div>   
                </div>
             </td>
         </tr>
         </table>
         
     </form>
     <!--<script src="http://malsup.github.com/jquery.form.js"></script>
     <script>
     (function() {
                 var bar = $('.bar');
                var percent = $('.percent');
                var status = $('#status');

                $('#insurance_attach_form').ajaxForm({
                    beforeSend: function() {
                        alert("Before send");
                        status.empty();
                        var percentVal = '0%';
                        bar.width(percentVal)
                        percent.html(percentVal);
                    },
                    uploadProgress: function(event, position, total, percentComplete) {
                        alert("Upload");
                        var percentVal = percentComplete + '%';
                        bar.width(percentVal)
                        percent.html(percentVal);
                    },
                    success: function() {
                        var percentVal = '100%';
                        bar.width(percentVal)
                        percent.html(percentVal);
                    },
                    complete: function(xhr) {
                        status.html(xhr.responseText);
                    }
                });
            })();
            </script>-->
 </div>
 <style>
     .hide { display:none; }
 </style>
 
<script>
$('#manage_offers_tab').tabs();

var refresh_time = 3000;

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
    $("#upload_insurance_docs").dialog("open");
    
    return false;
    
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

$('.franchise_filter').change(function(){
    var sort_franchise_id=$('.franchise_filter').val();
    
    var trname=$('.recharge_table,.insurance_table');
    
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

//Discard member offers
/*function discard_offer(e)
{
    var trEle = $(e).parents('tr:first');
    var member_id = trEle.attr('member_id');
    var offer_type = trEle.attr('offer_type');
    var fid = trEle.attr('franchise_id');
    var tagid='';
    if(offer_type == 1)
        tagid ='#talktime_offers';
    else if(offer_type == 2)
        tagid ='#insurance_offers';
    
    alert(offer_type+"-"+tagid);
    location.hash = tagid;
    
    var transid_ref = trEle.attr('transid_ref');
    if(confirm("Are you sure you want to disable this member offer?"))
    {
        $.post(site_url+'/admin/discard_member_offer',{member_id:member_id,transid_ref:transid_ref,fid:fid,offer_type:offer_type},function(resp){
                if(resp.status=="error")
                {
                        alert("Error: Failed to discard Member information");
                }else
                {
                        $('.notification_blk').html('Member information discarded successfully').fadeIn().delay(refresh_time).fadeOut();
                        //alert("Member information discarded successfully");
                        setTimeout(reloadpg,refresh_time);
                }
        },'json');
    }
    return false;
}*/
</script>