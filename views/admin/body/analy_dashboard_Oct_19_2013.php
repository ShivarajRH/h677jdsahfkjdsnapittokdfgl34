<style>
.leftcont{display:none;}

/** StoreKing Custom Theme **/
.sk_theme{clear:both;padding:5px;}
.sk_theme .clearboth{clear: both}
.sk_theme .fl_left{float: left}
.sk_theme .fl_right{float: right}

.sk_theme .sidebar{width: 250px;height: 615px;background: #f1f1f1;}
.sk_theme .content{width: 75%;height: 615px;background: #fafafa}

.sk_theme .sk_module{padding:5px;}
.sk_theme .sk_module .filters{padding:5px;}
.sk_theme .sep {clear: both;padding:0px;}
.sk_theme .sep .frm_input{width:100%;}

.sk_theme .stats{padding:10px;background: #cfcaa8}

.auto_overflow{overflow: auto}

.sk_theme .list_view{padding:4px;background: #fefefe;}
.sk_theme .list_view .list_view_item{margin:3px 0px;background: #FFF;padding:3px;border-bottom: 1px dotted #dfdfdf;line-height: 20px;}

.sk_theme .list_view .list_view_item a{font-size: 13px;margin:2px;color: #888}
.sk_theme .list_view .list_view_item:hover{cursor: pointer;background: #dfdfdf}

.sk_theme .list_view .list_view_item .item_det{clear: both;padding:3px;}

.sk_theme .list_view .list_view_item .item_det span{font-size: 11px;}

.sk_theme .list_view .list_view_item .empty_result { font-size: 11px; background-color:tomato; color: #f1f1f1; text-align: center; margin-bottom: 5px;}
#fr_list{height: 400px;}
.border_warn{border-left:3px solid tomato}
.border_success{border-left:3px solid #004B91}

.sk_theme .sk_btn{padding:3px 6px;font-size: 11px;font-weight:bold;color: #fcfcfc;background: tomato;border:1px solid tomato;cursor: pointer}
.sk_theme .sk_btn_small{padding:3px;font-size: 11px;font-weight:bold;color: #fcfcfc;}
.googlemap_holder { padding:5px 10px 5px 25px;}
.click_to_know { float: right;}
</style>

<div id="analytics_dash" class="sk_theme sk_container" >
    <h2>Franchise Analytical Dashboard</h2>
    <!-- Analytics Content Start -->
    <div class="clearboth">
        
        <!-- Sidebar  Start -->
        <div class="sidebar fl_left">
            <div class="sk_module" >
                <div class="stats">
                    <b>List of Franchises</b> 
                    <a id="refine_fr_btn" class="fl_right sk_btn">Refine</a>
                </div>
                <div id="refine_frlist" class="filters" style="display:none">
                    <div class="sep">
                        <select class="frm_input" name="terr_id" id="terr_id" onchange="return load_towns_list(this);">
                            <option value="00">All Territory</option>
                            <?php
                            foreach ($pnh_terr as $terr) {
                                echo '<option value="'.$terr['id'].'">'.$terr['territory_name'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="sep">
                        <select class="frm_input" name="town_id" id="town_id" onchange="onchange_town(this)">
                            <option value="00">All Town</option>
                            <?php 
                            foreach ($pnh_towns as $town) {
                                echo '<option value="'.$town['id'].'">'.$town['town_name'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="sep">
                        <select class="frm_input" name="fran_status" id="fran_status" onchange="onchange_frn_status(this)">
                            <option value="00">All Franchises</option>
                            <option value="active">Active Franchises</option>
                            <option value="1">Suspended Franchises</option>
                        </select>
                    </div>
                </div>
                <div id="fr_list" class="list_view auto_overflow"></div> 
                
            </div>
        </div>
        <!-- Sidebar  End -->
        
        <!-- Content  Start -->
        <div class="content fl_left">
            <div class="stats" id="content_total_log"></diV>
            <div class="googlemap_holder fl_left"> <div id="googleMap" style="width:1298px;height:610px;"></div></div>
            <div class="content_holder"></div>
        </div>
        <!-- Content  End -->
    </div>
    <!-- Anytics Content End -->
</div>

<div class="click_to_know_container" style="display: none;" title="Basic dialog"></div>


<script type="text/javascript">
    $(document).ready(function() {
        load_franchise_details(0);
        get_fran_total_log();
    });
    
    /* Refine list start */
    function load_towns_list(e) {
        //e.preventDefault();
        var terr_id=e.value;
        $.post(site_url+"admin/ajax_suggest_townbyterrid/"+terr_id,{},function(resp) {
            if(resp.status=='success') {
                 //print(resp.towns);
                 var obj = jQuery.parseJSON(resp.towns);
                $("#town_id").html(objToOptions_terr(obj));
                //$(".list_view").html('');
                load_franchise_details(terr_id);
                $("#town_id").val($("#town_id option:nth-child(0)").val());
            }
            else {
                //$("#sel_franchise").val($("#sel_franchise option:nth-child(0)").val());
            }
        },"json");
    }
    function onchange_town(e) {
        var town_id=e.value;
        $("#town_id").val(town_id);
        load_franchise_details(0);
    }
    
    function onchange_frn_status(e) {
        var fran_status=e.value;
        $("#fran_status").val(fran_status);
        load_franchise_details(0);
    }
    function onclick_fran(e) {
        var fran_id=e.id;
        
        //$(".content_holder").html('');
        $("#"+fran_id).addClass("");
        $.post(site_url+"admin/ajax_get_franchise_details/"+fran_id, {}, function(resp) {
            
            var location_info=[];
            location_info.push({
                'store_name':resp.store_name
                ,'franchise_id':resp.franchise_id
                ,'franchise_name':resp.franchise_name
                ,'lat':resp.lat
                ,'long':resp.long
                ,'address':resp.address
                ,'created_on': _timestamp_to_date(resp.created_on)
            });
            
            var singlelocation=true;
            get_fran_google_map(location_info,singlelocation);
            
        }, "json");
        
    }
    function load_franchise_details(terr_id) {
        
        if(terr_id ==0 ) {
            var terr_id = $("#terr_id").val();
        }
        var town_id = $("#town_id").val();
        var fran_status = $("#fran_status").val();
        
        //alert(terr_id+"\n"+town_id+"\n"+fran_status+"");
        get_fran_total_log();
        
        $.post(site_url+"admin/ajax_get_all_franchise_details/",{terr_id:terr_id,town_id:town_id,franchise_status:fran_status},function(resp) {
            $(".list_view").print('');
            var rdata='<p><b>Showing:</b> '+resp.total_fran+'</p>';
            var franchise_info=[]; 
                
                if(resp.franchise) {
                    var total=resp.franchise.length;
                    $.each(resp.franchise, function(i,list) {
                        //var class_1 = ((i%2) ? "border_warn" : "border_success");
                        var class_1 = (list.is_suspended==1)?"border_warn" : "border_success";
                        var fr_reg_diff = Math.ceil( ( resp.time - list.created_on ) / (24*60*60) );//$.now()
                        if(fr_reg_diff <= 30)
                        {
                                fr_reg_level_color = '#cd0000';
                                fr_reg_level = 'Newbie';
                        }
                        else if(fr_reg_diff > 30 && fr_reg_diff <= 60)
                        {
                                fr_reg_level_color = 'orange';
                                fr_reg_level = 'Mid Level';
                        }else if(fr_reg_diff > 60)
                        {
                                fr_reg_level_color = 'green';
                                fr_reg_level = 'Experienced';
                        }
                        var currentDate = _timestamp_to_date(list.created_on);
                        
                        rdata +='<div class="list_view_item '+class_1+'" id="'+list.franchise_id+'" onclick="onclick_fran(this)" style="">\n\
                                <a href="javascript:void(0)"><b class="title">'+list.franchise_name+'</b> \n\
                                    <div class="item_det">\n\
                                        <span clss="color_green" style="font-size: 11px; color:'+fr_reg_level_color+' "><b>'+fr_reg_level+'</b></span>\n\
                                        <span class="color_green fl_right">'+currentDate+'</span>\n\
                                    </div>\n\
                                </a>\n\
                            </div>';
                            
                                franchise_info.push({
                                    'store_name':list.store_name
                                    ,'franchise_id':list.franchise_id
                                    ,'franchise_name':list.franchise_name
                                    ,'lat':list.lat
                                    ,'long':list.long
                                    ,'address':list.address
                                    ,'created_on': currentDate
                                });
                          /*if(i<total) { location_info.push('["'+i+'. '+list.store_name+'",'+list.lat+","+list.long+"]"); }
                            else { location_info.push('["'+i+'. '+list.store_name+'",'+list.lat+","+list.long+"]"); }*/
                    });
                    var singlelocation=false;
                    get_fran_google_map(franchise_info,singlelocation);
                }
                else {
                    rdata='<div class="list_view_item">\n\
                           <div class="empty_result">'+resp.error+'</div>\n\
                            </div>';
                }
                
                $(".list_view").html(rdata);
                return false;
                
        },'json');
        
        
        return false;
    }
    function _timestamp_to_date(created_on) {
        var fullDate = new Date(created_on*1000);//list.created_on;//new Date();
        var twoDigitMonth = ((fullDate.getMonth().length+1) === 1)? (fullDate.getMonth()+1) : '0' + (fullDate.getMonth()+1);
        var currentDate = fullDate.getDate() + "/" + twoDigitMonth + "/" + fullDate.getFullYear();
        return currentDate;
    }
    function get_fran_total_log() {
        var terr_id = $("#terr_id").val();
        var town_id = $("#town_id").val();
        //var fran_status = $("#fran_status").val();
        
        $.post(site_url+"admin/ajax_get_fran_total_log/",{terr_id:terr_id,town_id:town_id},function(resp) {
                $("#content_total_log").html('<span class="stat_total"><b>Franchises</b> : '+resp.total_fran+'</span>\n\
                <span class="stat_total"><b>Suspended</b> : '+resp.total_suspended_fran+'</span>\n\
                <span class="stat_total"><b>Active</b> : '+resp.total_active_fran+'</span>');
        },"json");
        
    }
    
    function objToOptions_terr(obj) {
        var output='';
            output += "<option value='00' selected>All Towns</option>\n";
        $.each(obj,function(key,elt){
            if(obj.hasOwnProperty(key)) {
                output += "<option value='"+elt.id+"'>"+elt.town_name+"</option>\n";
            }
        });
        return(output);
    }
    /* Refine list end */
    
    function reset_auto_overflow()
    {
        $('.auto_overflow').each(function(){
            $(this).height($(this).height());
        });
    }
    reset_auto_overflow();
    
    $('.list_view_item').hover(function(){
        $(this).addClass('h_over');
    },function(){
        $(this).removeClass('h_over');
    });
    $('#refine_fr_btn').click(function(e){
        e.preventDefault();
        if($('#refine_frlist').is(':visible'))
        {
            $(this).text();
        }   
        $('#refine_frlist').toggle();    
    });
    $(".click_to_know_container").dialog({
                height: 580
                ,width: 780
                ,autoOpen:false
//                ,autoOpen: false
//                ,modal: true
//                ,show: {effect: "blind",duration: 1000}
//                ,hide: {effect: "explode",duration: 1000}
        });
    function click_to_know(e) {
        
        var fran_id = $(e).attr("franchise_id");
        $(".click_to_know_container").dialog({autoOpen:true
            ,open:function(e,ui) {
                $.post(site_url+"admin/ajax_get_franchise_details/"+fran_id, {}, function(resp) {
            
                    var fr_reg_diff = Math.ceil( ( resp.time - resp.created_on ) / (24*60*60) );//$.now()
                    if(fr_reg_diff <= 30)
                    {
                            fr_reg_level_color = '#cd0000';
                            fr_reg_level = 'Newbie';
                    }
                    else if(fr_reg_diff > 30 && fr_reg_diff <= 60)
                    {
                            fr_reg_level_color = 'orange';
                            fr_reg_level = 'Mid Level';
                    }else if(fr_reg_diff > 60)
                    {
                            fr_reg_level_color = 'green';
                            fr_reg_level = 'Experienced';
                    }

                    $(".click_to_know_container").print('<div id="m_pop_content">'+
                                '<div id="siteNotice">'+
                                    '</div>'+
                                    '<h1 id="firstHeading" class="firstHeading">'+resp['franchise_name']+' <span clss="color_green" style="font-size: 11px; color:'+fr_reg_level_color+' ">(<b>'+fr_reg_level+'</b>)</span></h1>'+
                                    '<table width="100%" >'+
                                        '<tr>'+
                                        '<td width="60%">'+
                                                '<div class="tab_list" style="clear: both;">'+
                                                            '<ol>'+
                                                                    '<li><a class="load_type selected" id="all" href="javascript:void(0)">Basic Details</a><div class="all_pop"></div></li>'+
                                                                    '<li><a class="load_type" id="shipped" href="javascript:void(0)">Finance</a><div class="shipped_pop"></div></li>'+
                                                                    '<li><a class="load_type" id="unshipped" href="javascript:void(0)">UnShipped</a><div class="unshipped_pop"></div></li>'+
                                                            '</ol>'+
                                                '</div>'+
                                        '</td>'+
                                        '<td align="right">'+
                                    '</td>'+
                            '</tr>'+
                            '<tr>'+
                                '<td><div class="ttl_orders_status_listed"></div></td>'+
                                '<td align="right"></td>'+
                            '</tr>'+

                            '</table>'+
                                    '<div id="bodyContent">'+
                                    '<p><b>'+resp['store_name']+'</b>,<br> '+resp["address"]+'</p>'+
                                    ' Registed on '+resp['created_on']+'</p>'+
                                    '</div>'+
                                '</div>');

                }, "json");
            }
        });
        
        
        
    }
</script>

<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDY0kkJiTPVd2U7aTOAwhc9ySH6oHxOIYM&sensor=false"></script>
<script>
    function get_fran_google_map(locations,singlelocation) {
        if(singlelocation===true) {
            
             // Set location to centre on
            $.each(locations, function(i,location) {
                var myCenter=new google.maps.LatLng(location['lat'], location['long']);
           
                //apply location marker to centre on
                var mapProp = {
                    center:myCenter,
                    zoom:15,
                    mapTypeId:google.maps.MapTypeId.ROADMAP
                  };
                  
                var map=new google.maps.Map(document.getElementById("googleMap"),mapProp);

                var marker=new google.maps.Marker({
                    position:myCenter
                    ,title: location['store_name']
                    ,html: "DisplayText444"
                    ,draggable: false
                    ,size: new google.maps.Size(20, 16)
                    ,icon: site_url+"images/google-map-pointer-green-th.png"//"http://static.snapittoday.com/loading_maroon.gif"
                    
                });

                marker.setMap(map);
                //========================================
                var contentString = '<div id="m_pop_content">'+
                        '<div id="siteNotice">'+
                            '</div>'+
                            '<h1 id="firstHeading" class="firstHeading">'+location['franchise_name']+'</h1>'+
                            '<div id="bodyContent">'+
                            '<p><b>'+location['store_name']+'</b>,<br> '+location["address"]+'</p>'+
                            ' Registed on '+location['created_on']+'</p>'+
                            '<p><div class="click_to_know"><a href="javascript:void(0);" franchise_id="'+location["franchise_id"]+'" onclick="click_to_know(this)">Know more</a></div></p>'+
                            '</div>'+
                        '</div>';

                    var infowindow = new google.maps.InfoWindow({
                        height:800
                        ,width:800
                        ,content: contentString
                    });
                    
                google.maps.event.addListener(marker, 'load', function() {
                        infowindow.open(map,marker);
                });
                
            });
            return false;
        }
        else {
            var myCenter=new google.maps.LatLng(12.878478, 77.445319);
        }
        
        // Set other locations in array first title for marker, second coords
        /*locations = [ 
            ["marker title1", -13.530825,-71.957565],
            ["marker title2", -13.531211,-71.961921],
            ["marker title3", -13.531336,-71.960387],
            ["marker title4", -13.533099,-71.960151],
            ["marker title5", -13.533985,-71.960751],
            ["marker title6", -13.535289,-71.962929]
            ];*/
        var locations_arr=locations;
        //$(".content_holder").print(locations_arr);
        
        //apply location marker to centre on
        var mapProp = {
            center:myCenter
            ,zoom:8
            ,mapTypeId:google.maps.MapTypeId.ROADMAP
          };

        var map=new google.maps.Map(document.getElementById("googleMap"),mapProp);

        var marker=new google.maps.Marker({
            position:myCenter
            ,title: "Default location"
            ,size: new google.maps.Size(20, 16)
            ,icon: site_url+"images/google-map-pointer-green-th.png"//"http://static.snapittoday.com/loading_maroon.gif"
        });

        marker.setMap(map);
        
       
            // apply other location markers
            $.each(locations_arr, function(i,location) {
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(location['lat'], location['long'])
                    ,map: map
                    ,title: location['store_name']
                    //,icon: site_url+"images/google-map-pointer-green-th.png"//"http://static.snapittoday.com/loading_maroon.gif"
                });


                    //========================================
                    var contentString = '<div id="m_pop_content">'+
                            '<div id="siteNotice">'+
                                '</div>'+
                                '<h1 id="firstHeading" class="firstHeading">'+location['franchise_name']+'</h1>'+
                                '<div id="bodyContent">'+
                                '<p><b>'+location['store_name']+'</b>,<br> '+location["address"]+'</p>'+
                                ' Registed on '+location['created_on']+'</p>'+
                                '<p><div class="click_to_know"><a href="javascript:void(0);" franchise_id="'+location["franchise_id"]+'" onclick="click_to_know(this)">Know more</a></div></p>'+
                                '</div>'+
                            '</div>';

                        var infowindow = new google.maps.InfoWindow({
                            content: contentString
                        });

                    google.maps.event.addListener(marker, 'click', function() {
                            infowindow.open(map,marker);
                    });
            });
            //google.maps.event.addDomListener(window, "load", initialize);
    }
</script>
    