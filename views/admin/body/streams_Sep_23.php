<style type="text/css">
.stream_post_whom_block {    margin-top: 6px;}
.stream_post_whom_block a {    text-transform: uppercase;    font-size:14px;     padding: 4px 8px;    border: 1px dotted #000000;    /*border-radius: 10px 10px;*/ }
.stream_post_whom_block a:hover {   text-decoration: none; background-color: #CCCC99; }

a.total_pop {    font-size: 11px;     text-align: center;    float: right;   padding: 3px 0px !important;     color: #FFFF00 !important;    background-color: #990033;     width: 25px; }
.total_pop { margin-top: -20px;}

.stream_div_desc {color:gray; margin:2px 0 0px 10px;}
.stream_post_form{background: #f1f1f1;padding:10px;}

.loading {    text-align: center;    margin: 5% 0 0 40%;    visibility: visible; font-size: 16px;  }
.post_users { width:auto; height:auto; }

.sub_reply_list {    color: #000000;    margin: 10px 0 0 36px;    width: 100%; }
.stream_post_list_overview .stream_post_total {    float: left;    background-color: #CCCC99;    color: #000000;   padding: 5px 4px; border: 1px dotted #000000; }
.stream_post_list_overview .stream_post_list_date_block {    float: left;    color: #000000;    padding: 3px 4px 5px 12px;     font-size: 18px; }

.sub_reply_list .subreply {    clear: both;    display: table;    width: inherit;    margin-top: 7px; }
.sub_reply_list .subreply .img_div {    float: left;     margin: 0px 10px 0 0px;     padding: 7px 0 0px 0px;   }
.sub_reply_list .subreply .img_div  img {    float: left;border: none;width: 15px;height: 15px;}
.sub_reply_list .subreply .desc,.subreply p {    margin: 0 0 0 15px;     padding: 10px;    font-size: 13px; width: 87%;    border-radius: 8px;    background-color: #99CC99;    color: #000000;    float: left; }
.sub_reply_list .subreply .action_block {    padding: 4px 0 0 0;    display: block;    color: #990033;    margin-left: 45px;    margin-bottom: 5px; }
.sub_reply_list .subreply .action_block a {    color: #990033;}
.sub_reply_list .subreply .action_block abbr {    font-size: 11px;    padding: 0 0 0 10px;}
.reply_form_block {    text-transform: capitalize;    float: left;}
.reply_desc2 {width: inherit;              font-size: 13px !important;           padding: 5px 5px;}
.reply_image_div2 {       padding: 0px 10px 10px 0px;float: left; }
.reply_image_div2 img {    width: 40px;height: 40px;     border: none;}

.button {    float: right;    margin-right: 12px;    height: 43px !important;    width: 147px; }
.button-tiny {    float: right;}
.ui-widget-content a {    color: #245A77;}
#tab_view_list_ul {    background-color: #245A77;}
.container_div {    clear: both;    margin: 2% 12% 2% 0%;    width: 100%; }
.leftcont {    display:none; }
.stream_post_list_block {    width: 98%;    clear: both;    padding: 12px 20px 10px 48px; }
.stream_post_list_block td {    padding: 10px 0px 0px 0px;    border-bottom: 1px solid #cccccc; }
.stream_item_admin_div {    float: left;    font-size: 16px;    width: 100%; }
.stream_item_admin_div .reply_image_div {    padding: 0px 10px 10px 0px;    float: left;    width: 4%; }
.stream_item_admin_div .reply_image_div img {   width: 40px;height: 40px;    border: none;}
.stream_item_admin_div .reply_box {    float: left; margin: 10px 0 0 10px;    font-size: 13px;    width: 91%;}
.stream_item_admin_div .reply_box .reply_date {    font-size: 11px;    padding: 5px;}
.stream_item_admin_div .reply_box .reply_link {    margin: 0 0 5px 0px;    font-size: 12px; cursor: pointer;}
.stream_item_admin_div .reply_box .reply_desc {     clear: both;     width: inherit;     margin:20px 0px 5px 5px;     padding:10px;    background:#CCCC99;     color: #000000;     border-radius: 10px; }
.stream_item_admin_div .reply_box .reply_actions {    color: #006699;}
.stream_item_admin_div .reply_box .title {    float: left;}
.stream_item_admin_div .reply_box .title_to {    float: left;    font-size: 14px;    clear: right; padding-top: 2px;}

.stream_post_list_overview {  margin:2% 4%;    }
.assigned_to_div {    width: 98%;    margin: 0 10px 16px 10px;}
.assigned_to_div select {    margin-top:20px;}
.text_description_block {    width: 98%;margin: 13px 10px 16px 10px; }
.text_description {    width: 100%;    padding:7px;    border: 1px solid #BBB; }
.stream_item_reply_div {    margin: 12px 0 5px 40px;    clear: both; float: left; }
.clear {    clear: both; }
</style>

<div class="container" >
    <h2>Streams </h2>
    <div class="container_div" style="margin-top: 0px;">
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
                    <a class="stream_link" title="<?=$stream['description']?>" id="<?=$stream['stream_id']?>" stream_id="<?=$stream['stream_id']?>" href="#stream_<?=$stream['stream_id']?>" onclick="load_streamdata(<?=$stream['stream_id']?>,0)"><?=ucwords($stream['title'])?></a>
                    
                    <a class="total_pop total_unreply_block" title="<?=$count_elt['total']?> Unreplied Comments" id="total_unreply_block_<?=$stream['stream_id']?>"><?=$count_elt['total']?> </a>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php foreach($streams as $stream): ?>
                <div id="stream_<?=$stream['stream_id']?>" style="padding:0px;">
                    <div class="stream_post_form">
                        <form action="" method="post" id="<?=$stream['stream_id']?>" class="stream_post">
                            <input type="text" name="stream_id" id="stream_id" value="<?=$stream['stream_id']?>" class="stream_id" size="12"/>
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
                                <div class="stream_post_list_date_block"><span class="stream_post_list_date" id="stream_post_list_date_log_<?=$stream['stream_id']?>">-</span></div>
                            
                            </div>
                            
<!--                            <div class="fl_left stream_post_whom_block">
                                <span><a href="" class="stream_post_me" onclick="return stream_post_whom(this,<?//=$stream['stream_id']?>,'tome');">To me</a></span>
                                <span><a href="" class="stream_post_others" onclick="return stream_post_whom(this,<?//=$stream['stream_id']?>,'toothers');">To others</a></span>
                            </div>-->
                            
                            <div class="fl_right" style="display: block">
                             
                                <form action="" name="date_form" id="date_form_<?=$stream['stream_id']?>" onsubmit="return date_form_submit(this,<?=$stream['stream_id']?>,0);">

                                    <span>From:</span>
                                        <input type="text" name="date_from" id="date_from_<?=$stream['stream_id']?>" class="date_from" value=""/>
                                    <span>To:</span>
                                    <input type="text" name="date_to" id="date_to_<?=$stream['stream_id']?>" class="date_to" value=""/>

                                    <input type="submit" name="date_submit_<?=$stream['stream_id']?>" id="date_submit_<?=$stream['stream_id']?>" value="Submit"/>
                                </form>
                                
                             
                            </div>

                        </div>
                        <!--List overview End-->
                        
                        <div class="stream_post_list_block" id="stream_post_list_block_<?=$stream['stream_id']?>"></div>
                        
                        <div id="stream_post_list_results_<?=$stream['stream_id']?>">&nbsp; </div>
                        <div class="animation_image" style="display:none" align="center">
                            <img src="<?=IMAGES_URL?>loader_gold.gif" />Loadinfngnk....
                        </div>
                        
                        <div class="stream_post_list_pagination" id="stream_post_list_pagination_<?=$stream['stream_id']?>"></div>
                        
                        
                    </div>
                    <!--List wrapper end-->
                </div>
            <?php endforeach; ?>
        </div>
        <?php } ?>
    </div>
    

</div>
<script>
    
//    function stream_post_whom(elt,stream_id,towhom) {
//        $("#stream_post_list_block_"+stream_id).html("<span class='loading'>&nbsp;&nbsp;&nbsp;&nbsp;Loading...</span>");
//        load_streamdata(stream_id);
//        $(".total_unreply_block").css({"display":"none"});
//        var date_from=$("#date_to").val();
//        var date_to=$("#date_to").val();
//        var dataStream='date_from='+date_from+"&date_to="+date_to+"&towhom="+towhom;
//        $.post(site_url+"/admin/jx_get_streampostdetails/"+stream_id,$(elt).serialize(),function(rdata) {
//            $("#stream_post_list_total_"+stream_id).html(rdata.total_items);
//            $("#stream_post_list_block_"+stream_id).html(rdata.items);
//            $("#stream_post_list_date_log_"+stream_id).html(rdata.date_output);
//            //$("#stream_post_list_block_"+stream_id).print(rdata.post);
//            $("#assigned_to_"+stream_id).trigger('liszt:updated');
//            $("abbr.timeago").timeago();
//            refresh_unreplied_posts(stream_id);
//        },"json");
//            
//        return false;
//    }
    
    function date_form_submit(elt,stream_id,pg) {
        $("#stream_post_list_block_"+stream_id).html("<span class='loading'>&nbsp;&nbsp;&nbsp;&nbsp;Loading...</span>");
        //load_streamdata(stream_id);
         $(".total_unreply_block").css({"display":"none"});
         
        $.post(site_url+"/admin/jx_get_streampostdetails/"+stream_id+"/"+pg,$(elt).serialize(),function(rdata) {
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
    $('select[name="assigned_to[]"').chosen();

    $(".tab_view").tabs();
    
    function load_streamdata(streamid,pg) { 
        $("table").data("test",{stream_id:streamid});
        set_field_date(streamid);
        $(".total_unreply_block").css({"display":"block"});
        $.post(site_url+"/admin/jx_get_assignto_list/"+streamid,{},function(rdata) {
            $("#assigned_to_"+streamid).html(rdata);
            $("#assigned_to_"+streamid).chosen();
        },"html");
        load_stream_list(streamid,pg);
    }
    
    //POST
    $(".stream_post").submit(function(e) {
        e.preventDefault();
        var streamid=$("table").data("test").streamid;
        
        var url=site_url+"/admin/jx_stream_post";
            if($("#description_"+streamid).val() == '') 
            { 
                alert("Please enter description");
                $("#description_"+streamid).focus(); //.css({"border-color":"#000000"})
                return false; 
            }
            if($("#assigned_to_"+streamid).val() == null) {
//                alert("Please assign some users to stream."); return false; 
            }
            $("#submit_"+streamid).attr("disabled","disabled");
            $("#stream_post_list_block_"+streamid).html("<span class='loading'>&nbsp;&nbsp;&nbsp;&nbsp;Loading...</span>").show();
            $.post(url,$(this).serialize(),function(rdata) {
                //$("#stream_post_list_block_"+streamid).print($(this).serialize()+"\n"+rdata); return false;
                 reset_form(".stream_post");
                 load_stream_list(streamid,0);
                 $("#submit_"+streamid).removeAttr("disabled");
             },"html");             
        return false;
    });
    $(".stream_post_list_pagination a").live("click",function() {
        var url=this.href; 
        var arr_url=url.split("/");
        
        var stream_id=arr_url[6];     var pg=arr_url[7];
        
        //alert("Streamid="+stream_id+"\npage="+pg);
        load_stream_list(stream_id,pg);
        return false;
    });
    function scroll_action_function(pg,total_items) {
        var loading  = false; //to prevents multipal ajax loads
        var stream_id=$("table").data("test").stream_id;
        
        $(window).scroll(function() { //detect page scroll

            if($(window).scrollTop() + $(window).height() == $(document).height())  //user scrolled to bottom of the page?
            {
                alert("Stream"+stream_id+"/pg="+pg+"/total="+total_items+"Str"+$("#stream_id").val());
                
                if( pg <= total_items && loading == false) {
                    
                    $.post(site_url+"/admin/jx_get_streampostdetails/"+stream_id+"/"+pg, {},function(rdata){
                        
                        loading = true; //prevent further ajax loading
                        $('.animation_image').html("<span class='loading'>&nbsp;&nbsp;&nbsp;&nbsp;Loading...</span>").show(); //show loading image

                        $("#stream_post_list_total_"+stream_id).html(rdata.total_items);

                        $("#stream_post_list_block_"+stream_id).append(rdata.items);

                        $("#stream_post_list_date_log_"+stream_id).html(rdata.date_output);
                        $("#stream_post_list_pagination_"+stream_id).html(rdata.pagination);

                        $("#assigned_to_"+stream_id).trigger('liszt:updated');
                        $("abbr.timeago").timeago();

                        refresh_unreplied_posts(stream_id);
                        
                        $('.animation_image').hide();
                        loading = false;
                        pg+=5;

                    },'json').fail(function(xhr, ajaxOptions, thrownError) { //any errors?

                        alert(thrownError); //alert with HTTP error
                        $('.animation_image').hide(); //hide loading image
                        loading = false;

                    });
                } 
                    
            }
        });
    }
    
    $(document).ready(function() {
        var loading  = false; //to prevents multipal ajax loads
        
        //var stream_id=$("#stream_id").val();
        var pg=5; //Already first page is printed so page start from 2nd
        var stream_id=$("table").data("test").stream_id;
        var obj=$.post(site_url+"/admin/jx_get_streampostdetails/"+stream_id+"/"+pg, {},function(rdata){
                total_items=rdata.total_items;
                alert(pg+","+stream_id+","+total_items);
                scroll_action_function(pg,stream_id,total_items);
               return false;
        },"json");
         return false;
    });

    
    function load_stream_list(stream_id,pg) {
        $("table").data("test",{stream_id:stream_id});
        $.post(site_url+"/admin/jx_get_streampostdetails/"+stream_id+"/"+pg,{},function(rdata) {
            $("#stream_post_list_total_"+stream_id).html(rdata.total_items);
            $("#stream_post_list_block_"+stream_id).html(rdata.items);
            $("#stream_post_list_date_log_"+stream_id).html(rdata.date_output);
            $("#stream_post_list_pagination_"+stream_id).html(rdata.pagination);
            
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
                                    <input type='reset' name='subreply' value='Close' class='button tiny-button' onclick='return close_subreply_box(this,"+id+")'/>\n\
                                    <input type='submit' name='subreply_submit' id='subreply_submit_"+id+"' value='Reply' class='button button-action button-rounded'/>\n\
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
        $.post(site_url+"admin/jx_get_unreplied_posts/"+stream_id,{},function(rdata){
            $("#total_unreply_block_"+stream_id).html(rdata+"");
        });
    }
    function close_subreply_box(e,post_id) {
        $("#stream_item_reply_div_"+post_id).html("");
    }
 
</script>

<script>
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