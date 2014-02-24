<style type="text/css">
.clear {    clear: both; }
.button {    float: right; }
.button-tiny {    float: right;}
.ui-widget-content a {    color: #245A77;}
.container_div {    clear: both;    margin: 2% 12% 2% 0%;    width: 100%; }
.leftcont {    display:none; }
.ui-tabs .ui-tabs-nav li a{font-size: 90%;}
</style>

<div id="stream_block" class="container" style="visibility: hidden">
	<div style="clear:both;display:none">
		<div class="fl_right" style="padding:0px 0px;">
			<form id="srch_stream" >
				<input type="text" name="gbl_srch" size="30" value=""><input type="submit" value="Search">
			</form>
		</div>
	    <h2>Streams</h2>
    </div>
    
    <script type="text/javascript">
    	$('#srch_stream').submit(function(){
    	});
    </script>
    
    <br>
    <div  class="container_div" style="margin-top: 0px;">
        <?php 
        if(empty($streams)) { 
            echo '<h3>No streams</h3>'; die();
        }
        else { ?>
        <div class="tab_view" style="clear: both;">
            <ul id="tab_view_list_ul">
                <?php foreach($streams as $stream): ?>
                        <?php $count_elt=$this->db->query("select count(*) as total from m_stream_posts sp where sp.stream_id=? and sp.id NOT IN (select post_id from m_stream_post_reply)",$stream['stream_id'])->row_array();?>
                <li>
                    <a class="stream_link" title="<?=$stream['description']?>" id="<?=$stream['stream_id']?>" stream_id="<?=$stream['stream_id']?>" href="#stream_<?=$stream['stream_id']?>" onclick="load_streamdata(<?=$stream['stream_id']?>)"><?=ucwords($stream['title'])?></a>
                    
                    <a class="total_pop total_unreply_block" title="<?=$count_elt['total']?> Unreplied Comments" id="total_unreply_block_<?=$stream['stream_id']?>"><?=$count_elt['total']?> </a>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php foreach($streams as $stream): ?>
                <div id="stream_<?=$stream['stream_id']?>" style="padding:0px;">
                    <div class="stream_post_form">
                        <form action="" method="post" id="<?=$stream['stream_id']?>" class="stream_post">
                            <input type="hidden" name="stream_id" id="stream_id" value="<?=$stream['stream_id']?>" class="stream_id"/>
                            <input type="hidden" name="user_id" id="user_id" value="<?=$user['userid']?>" class="user_id"/>
                            <table>
                                <tr>
                                    <td colspan="2"><div class="stream_div_desc"><?=$stream['description']?></div></td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="text_description_block">
                                            <textarea cols="240" rows="5" name="description" id="description_<?=$stream['stream_id']?>" class="text_description" placeholder="Please enter description"></textarea>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" colspan="2">
                                    	<div class="fl_right">
                                        	<input type="submit" value="POST" name="submit" id="submit_<?=$stream['stream_id']?>" class="button button-action button-rounded" />
                                        </div>
                                    	<div class="assigned_to_div" >
                                            <span style="top: -10px;position: relative">Assigned To :</span> 
                                            <select name="assigned_to[]" data-placeholder="Choose" style="width:400px;" multiple="multiple" id="assigned_to_<?=$stream['stream_id']?>" class="post_users"></select>
                                        </div>
                                        
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    <hr style="margin:0px;">
                    <!--List Wrapper start-->
                    <div class="stream_post_list_wrapper">
                        <!--List overview start-->
                        <div class="stream_post_list_overview">
                            <div class="fl_left">
                                <div class="stream_post_total"><strong>Total: <span class="stream_post_list_total" id="stream_post_list_total_<?=$stream['stream_id']?>">0</span></strong></div>
                            </div>
                            <div class="fl_left">
                                <div class="stream_post_list_date_block"><span class="stream_post_list_date" id="stream_post_list_date_log_<?=$stream['stream_id']?>">&nbsp;</span></div>
                            
                            </div>
                            
<!--                            <div class="fl_left stream_post_whom_block">
                                <span><a href="" class="stream_post_me" onclick="return stream_post_whom(this,<?=$stream['stream_id']?>,'tome');">To me</a></span>
                                <span><a href="" class="stream_post_others" onclick="return stream_post_whom(this,<?=$stream['stream_id']?>,'toothers');">To others</a></span>
                            </div>-->
                            
                            <div class="fl_right" style="display: block">
                                <p>
                                    <form action="" name="date_form" id="date_form_<?=$stream['stream_id']?>" onsubmit="return date_form_submit(this,<?=$stream['stream_id']?>);">

    <!--                                    <label for="date_from_<?=$stream['stream_id']?>">From:</lable> -->
                                        <span>From:</span>
                                            <input type="text" name="date_from" id="date_from_<?=$stream['stream_id']?>" class="date_from" size="10" value=""/>
                                        <span>To:</span>
    <!--                                    <label for="date_to_<?=$stream['stream_id']?>">To:</lable>-->
                                        <input type="text" name="date_to" id="date_to_<?=$stream['stream_id']?>" class="date_to" size="10"  value=""/>

                                        <input type="submit" name="date_submit_<?=$stream['stream_id']?>" id="date_submit_<?=$stream['stream_id']?>" value="Submit"/>
                                    </form>
                                
                                </p>
                            </div>
                        </div>
                        <!--List overview End-->
                        
                        <div class="stream_post_list_block" id="stream_post_list_block_<?=$stream['stream_id']?>"></div>
                        
                    </div>
                    <!--List wrapper end-->
                </div>
            <?php endforeach; ?>
        </div>
        <?php } ?>
    </div>
    

</div>
<script>
	$(".tab_view").tabs();
	$('#stream_block').css('visibility','visible');
    function stream_post_whom(elt,stream_id,towhom) {
        $("#stream_post_list_block_"+stream_id).html("<span class='loading'>&nbsp;&nbsp;&nbsp;&nbsp;Loading...</span>");
        load_streamdata(stream_id);
        $(".total_unreply_block").css({"display":"none"});
        var date_from=$("#date_to").val();
        var date_to=$("#date_to").val();
        var dataStream='date_from='+date_from+"&date_to="+date_to+"&towhom="+towhom;
        $.post(site_url+"/admin/jx_get_streampostdetails/"+stream_id,$(elt).serialize(),function(rdata) {
            $("#stream_post_list_total_"+stream_id).html(rdata.total_items);
            $("#stream_post_list_block_"+stream_id).html(rdata.items);
            $("#stream_post_list_date_log_"+stream_id).html(rdata.date_output);
            //$("#stream_post_list_block_"+stream_id).print(rdata.post);
            $("#assigned_to_"+stream_id).trigger('liszt:updated');
            $("abbr.timeago").timeago();
            refresh_unreplied_posts(stream_id);
        },"json");
            
        return false;
    }
    
    $('select[name="assigned_to[]"]').live('change',function(){
    	var reset_all = 0;
    	$('option:selected',this).each(function(){
    		if($(this).val() == 0)
    			reset_all = 1;
    	});
    	
    	if(reset_all && $('option:selected',this).length > 1)
    	{
    		$('option:selected',this).each(function(){
    			if($(this).val() == 0)
	    			$(this).attr('selected',true);
	    		else
	    			$(this).attr('selected',false);
	    	});
    	}
    	
    	$(this).trigger('liszt:updated');
    });
    
    function date_form_submit(elt,stream_id) {
        $("#stream_post_list_block_"+stream_id).html("<span class='loading'>&nbsp;&nbsp;&nbsp;&nbsp;Loading...</span>");
        //load_streamdata(stream_id);
         $(".total_unreply_block").css({"display":"none"});
         
        $.post(site_url+"/admin/jx_get_streampostdetails/"+stream_id,$(elt).serialize(),function(rdata) {
            $("#stream_post_list_total_"+stream_id).html(rdata.total_items);
            $("#stream_post_list_block_"+stream_id).html(rdata.items);
            $("#stream_post_list_date_log_"+stream_id).html(rdata.date_output);
            //$("#stream_post_list_block_"+stream_id).print(rdata.post);
            $("#assigned_to_"+stream_id).trigger('liszt:updated');
            $("abbr.timeago").timeago();
            refresh_unreplied_posts(stream_id);
        },"json");
            
        return false;
    }
    
    $('select[name="assigned_to[]"]').chosen();

    
    
    function load_streamdata(streamid) { 
        set_field_date(streamid);
        $(".total_unreply_block").css({"display":"block"});
        $.post(site_url+"/admin/jx_get_streamdetails/"+streamid,{},function(rdata) {
        	rdata  = '<option value="0">All</option>'+rdata;
            $("#assigned_to_"+streamid).html(rdata);
            $("#assigned_to_"+streamid).chosen();
        },"html");
        load_stream_list(streamid);
    }
    
    //POST
    $(".stream_post").submit(function(e) {
        e.preventDefault();
        var streamid=$(this).attr('id');
        
        var url=site_url+"/admin/jx_stream_post";
            if($("#description_"+streamid).val() == '') 
            { 
                alert("Please enter description");
                $("#description_"+streamid).focus(); //.css({"border-color":"#000000"})
                return false; 
            }
            if($("#assigned_to_"+streamid).val() == null) {
                alert("Please assign some users to stream."); 
                return false; 
            }
            $("#submit_"+streamid).attr("disabled","disabled");
            $("#stream_post_list_block_"+streamid).html("<span class='loading'>&nbsp;&nbsp;&nbsp;&nbsp;Loading...</span>");
            $.post(url,$(this).serialize(),function(rdata) {
                //$("#stream_post_list_block_"+streamid).print($(this).serialize()+"\n"+rdata); return false;
                 reset_form(".stream_post");
                 load_stream_list(streamid);
                 $("#submit_"+streamid).removeAttr("disabled");
             },"html");             
        return false;
    });
    function load_stream_list(stream_id) {
        $.post(site_url+"/admin/jx_get_streampostdetails/"+stream_id,{},function(rdata) {
            $("#stream_post_list_total_"+stream_id).html(rdata.total_items);
            $("#stream_post_list_block_"+stream_id).html(rdata.items);
            $("#stream_post_list_date_log_"+stream_id).html(rdata.date_output);
            
            $("#assigned_to_"+stream_id).trigger('liszt:updated');
            $("abbr.timeago").timeago();
            
            refresh_unreplied_posts(stream_id);
            $(".total_block").css({"display":"none"});
        },"json");
    } 
    
    
function reply_block(e,replied_by,streamid) 
{
    $(".stream_item_reply_div").hide();
    replied_by=$("#user_id").val();
    var id=e.id;
    var url=site_url+'admin/jx_get_admindetails/'+replied_by;
    
    $("#stream_item_reply_div_"+id).show();
    $("#stream_item_reply_div_"+id).html("<span class='loading'>&nbsp;&nbsp;&nbsp;&nbsp;Loading...</span>");
    $.post(url,{},function(rdata) {  
         //alert("SUCCESS:"+rdata.img_url);return false;
        
        if(rdata.img_url=='' || rdata.img_url==null) { 
            var divimgurl='<div class="reply_image_div2"><img src="'+base_url+'images/unknown_man.jpg" alt="reply Image"/></div>'; 
        }
        else { 
            var divimgurl='<div class="reply_image_div2"><img src="'+rdata.img_url+'" alt="reply Image"/>'; 
        }
        //print(divimgurl);return false;
        
        $("#stream_item_reply_div_"+id).html("<div class='sub_reply_div'>\n\
                            <form method='post' name='subreply_form' id='subreply_form_"+id+"' class='subreply_form' onsubmit='return subreply_block(this,"+id+","+replied_by+","+streamid+");'>\n\
                                "+divimgurl+"\n\
                                <div class='reply_form_block'>\n\
                                    <div><a name='stream_li' id='"+id+"'><strong>"+rdata.username+"</strong></a></div>\n\
                                    <div class='reply_desc2'><textarea cols='100' rows='6' id='subreplay_description_"+id+"'></textarea></div>\n\
                                    <input type='reset' name='subreply' value='Close' class='button button-small' onclick='return close_subreply_box(this,"+id+")'/>\n\
                                    <input type='submit' name='subreply_submit' id='subreply_submit_"+id+"' style='margin-right:10px;' value='Reply' class='button button-small button-action button-rounded'/>\n\
                                </div>\n\
                            </form>\n\
                        </div>");
                    $("#subreplay_description_"+id).focus();
    },"json")
    .fail(function(rdata) {
        alert('Error occured:'+rdata);
    });
    return false;
}

function subreply_block(e,post_id,replied_by,streamid) {
    var id=e.id;
    var subreplay_description=$("#subreplay_description_"+post_id).val();
    if(subreplay_description == '') {$("#subreplay_description_"+post_id).focus(); alert("Enter reply description.");return false; }
    //POST
    var url=site_url+"admin/jx_store_subreplies/"+post_id;
    var formdata={description:""+subreplay_description+"",replied_by:""+replied_by+""};
    
    $("#subreply_submit_"+post_id).attr("disabled","disabled");
    $.post(url,formdata,function(rdata) {
        reset_form("#"+id);
        load_post_reply_list(post_id,streamid);
        
     },"html");
    return false;
}
    function load_post_reply_list(post_id,streamid) {
        var url=site_url+"admin/jx_post_reply_list/"+post_id;
        $.post(url,{},function(rdata) { 
            $("#sub_reply_list_"+post_id).html(rdata);
            refresh_unreplied_posts(streamid);
            $("abbr.timeago").timeago();
            
        });
        $("#subreply_submit_"+post_id).removeAttr("disabled");
        return false;
    }
    function refresh_unreplied_posts(stream_id) {
        //var stream_id=$("#stream_id").val();
        $.post(site_url+"/admin/jx_get_unreplied_posts/"+stream_id,{},function(rdata){
            $("#total_unreply_block_"+stream_id).html(rdata+"");
        });
    }
    function close_subreply_box(e,post_id) {
        $("#stream_item_reply_div_"+post_id).html("");
    }
    //TIME AGO CODE
    //jQuery(document).ready(function() {jQuery("abbr.timeago").timeago(); });
    function reset_form(formidclass) {
        $(formidclass).each(function() {
            this.reset();
            $("#assigned_to_"+$(this).attr('id')).trigger('liszt:updated');
        });
    }
    function set_field_date(stream_id) {
       //FIRST RUN
        var reg_date = "<?php echo date('m/d/Y',  time()*60*60*24);?>";
        
        $( "#date_from_"+stream_id).datepicker({
             changeMonth: true,
             dateFormat:'yy-mm-dd',
             numberOfMonths: 1,
             maxDate:0,
//             minDate: new Date(reg_date),
               onClose: function( selectedDate ) {
                 $( "#date_to_"+stream_id).datepicker( "option", "minDate", selectedDate ); //selectedDate
             }
           });
        $( "#date_to_"+stream_id).datepicker({
            changeMonth: true,
             dateFormat:'yy-mm-dd',
//             numberOfMonths: 1,
             maxDate:0,
             onClose: function( selectedDate ) {
               $( "#date_from_"+stream_id).datepicker( "option", "maxDate", selectedDate );
             }
        });

        prepare_daterange('date_from','date_to');
    }
    $(".stream_link").first().trigger("click");
</script>