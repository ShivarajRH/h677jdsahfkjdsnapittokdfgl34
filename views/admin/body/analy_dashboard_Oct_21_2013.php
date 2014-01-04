<style>
.leftcont{display:none;}

/** StoreKing Custom Theme **/
.sk_theme{clear:both;padding:5px;}
.sk_theme .clearboth{clear: both}
.sk_theme .fl_left{float: left}
.sk_theme .fl_right{float: right}

.sk_theme .sidebar{width: 250px;height: 615px;background: #f1f1f1;}
.sk_theme .content{width: 75%;height: auto;background: #fafafa}

.sk_theme .sk_module{padding:5px;}
.sk_theme .sk_module .filters{padding:5px;}
.sk_theme .sep {clear: both;padding:0px;}
.sk_theme .sep .frm_input{width:100%;}

.sk_theme .stats{padding:10px;background: #cfcaa8}

.auto_overflow{overflow: auto}

.sk_theme .list_view{padding:4px;background: #fefefe;}
.sk_theme .list_view .list_view_item{margin:0px 0px;background: #FFF;padding:3px;border-bottom: 1px dotted #dfdfdf;line-height: 20px;}

.sk_theme .list_view .list_view_item a{font-size: 13px;margin:2px;color: #888; }

.sk_theme .list_view .list_view_item:hover,.sk_theme .list_view .list_view_item.selected{cursor: pointer;background: #dfdfdf}


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
            <div id="map_canv" style="width:100%;height:610px;clear: both"></div>
        </div>
        <!-- Content  End -->
    </div>
    <!-- Anytics Content End -->
</div>

<div class="frn_container_popup" style="display: none;" title="Franchise Info"></div>

<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDY0kkJiTPVd2U7aTOAwhc9ySH6oHxOIYM&sensor=false&format=png"></script>

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
    var franchise_info={};
    var curr_franchise_id=0;
    var marker = {};
    
    
    function highlight_fran(fran_id) {
        
        // mark selected item in list 
        $(".list_view_item.selected").removeClass("selected");
        $("#div_fran_"+fran_id).addClass("selected");
        
        // open marker infowindow 
        /*var infowindow = new google.maps.InfoWindow({
                            height:800
                            ,minWidth:800
                            ,content:"contentString"});
        infowindow.open(map, marker[fran_id]);
        */
        
        // auto center and zoom to marker [street level] 
        map.setCenter(marker.getPosition());
        map.setZoom(17);
        
       
            
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
                
                if(resp.franchise) {
                    var total=resp.franchise.length;
                    $.each(resp.franchise, function(i,list) {i=list.franchise_id;
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
                        
                        rdata +='<div class="list_view_item '+class_1+'" id="div_fran_'+list.franchise_id+'" onclick="highlight_fran('+list.franchise_id+')" style="">\n\
                                <a href="javascript:void(0)"><b class="title">'+list.franchise_name+'</b> \n\
                                    <div class="item_det">\n\
                                        <span clss="color_green" style="font-size: 11px; color:'+fr_reg_level_color+' "><b>'+fr_reg_level+'</b></span>\n\
                                        <span class="color_green fl_right">'+currentDate+'</span>\n\
                                    </div>\n\
                                </a>\n\
                            </div>';
                            
                                //franchise_info.push({
                                //franchise_info[list.franchise_id]=[];
                                
                                franchise_info[i]={
                                    'store_name':list.store_name
                                    ,'franchise_id':list.franchise_id
                                    ,'franchise_name':list.franchise_name
                                    ,'lat':list.lat
                                    ,'lng':list.long 
                                    ,'address':list.address
                                    ,'created_on': currentDate
                                };
                                
                                
                    });
                    
                }
                else {
                    rdata='<div class="list_view_item">\n\
                           <div class="empty_result">'+resp.error+'</div>\n\
                            </div>';
                }
                
                $(".list_view").html(rdata);
                
                
                
                //map data
                $.each(franchise_info,function(i,list) {
                                var fr_det = franchise_info[i];
                                var fr_det_fid = franchise_info[i].franchise_id;
                                
                                 
                                var marker = new google.maps.Marker({
                                    position: new google.maps.LatLng(fr_det.lat, fr_det.lng)
                                    ,title: fr_det.franchise_name
                                });
                                
                               

                                  contentString = '<div id="m_pop_content">'+
                                        '<div id="siteNotice">'+
                                            '</div>'+
                                            '<h1 id="firstHeading" class="firstHeading">'+fr_det['franchise_name']+'</h1>'+
                                            '<div id="bodyContent">'+
                                            '<p><b>'+fr_det['store_name']+'</b>,<br> '+fr_det["address"]+'</p>'+
                                            ' Registed on '+fr_det['created_on']+'</p>'+
                                            '<p><div class="click_to_know"><a href="javascript:void(0);" franchise_id="'+fr_det["franchise_id"]+'" onclick="click_to_know(this)">Know more</a></div></p>'+
                                            '</div>'+
                                        '</div>';
                                    // open marker infowindow 
                                
                                   
                                //infowindow.open(map, franchise_info[list.franchise_id].marker);
                                
                                google.maps.event.addListener(marker, 'click', function() {
                            
                                        console.log("sdfhsdfkj");
                                        
                                        
                                            $(".frn_container_popup").dialog({
                                                autoOpen : true
                                                ,open:function() {
                                                    $(".frn_container_popup").html(contentString)
                                                }
                                            });
                                            
                                            var infowindow = new google.maps.InfoWindow({
                                                height:800
                                                ,minWidth:800
                                                ,position: new google.maps.LatLng(fr_det.lat, fr_det.lng)
                                                ,center: new google.maps.LatLng(fr_det.lat, fr_det.lng)
                                                ,content:"contentStringjhdshakshfdsahflkjsahdfdfskjadshf"
                                            });
                                            infowindow.open(map, marker);
                                        
                                });
                                /*
                                  fr_det.marker.infowindow = new google.maps.InfoWindow({
                                        height:800
                                        ,width:800
                                        ,content: contentString
                                    });
                                    
                                google.maps.event.addListener(fr_det.marker, 'click', function() {
                                     fr_det.marker.infowindow.open(map,fr_det.marker);
                                });*/

                            marker.setMap(map);
                  });          
                //end map data
                
                
                
                return false;
                
        },'json');
        
        
        return false;
    }
    function get_fran_total_log() {
        var terr_id = $("#terr_id").val();
        var town_id = $("#town_id").val();
        
        $.post(site_url+"admin/ajax_get_fran_total_log/",{terr_id:terr_id,town_id:town_id},function(resp) {
                $("#content_total_log").html('<span class="stat_total"><b>Franchises</b> : '+resp.total_fran+'</span>\n\
                <span class="stat_total"><b>Suspended</b> : '+resp.total_suspended_fran+'</span>\n\
                <span class="stat_total"><b>Active</b> : '+resp.total_active_fran+'</span>');
        },"json");
        
    }
    
    /* Refine list end */
    
    function reset_auto_overflow()
    {
        $('.auto_overflow').each(function(){
            $(this).height($(this).height());
        });
    }
    reset_auto_overflow();
    
    $('#refine_fr_btn').click(function(e){
        e.preventDefault();
        if($('#refine_frlist').is(':visible'))
        {
            $(this).text();
        }   
        $('#refine_frlist').toggle();    
    });
</script>


<script>
    
    
    function _timestamp_to_date(created_on) {
        var fullDate = new Date(created_on*1000);//list.created_on;//new Date();
        var twoDigitMonth = ((fullDate.getMonth().length+1) === 1)? (fullDate.getMonth()+1) : '0' + (fullDate.getMonth()+1);
        var currentDate = fullDate.getDate() + "/" + twoDigitMonth + "/" + fullDate.getFullYear();
        return currentDate;
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
    
    var map = null;
    function init_map() 
    {
        if(map != null)
            return false;
        
        var def_lat = 12.96949928505618;
        var def_lng = 77.59423840625004;


        var mapOptions = {
          zoom: 8
          ,position:new google.maps.LatLng(def_lat,def_lng)
          ,center: new google.maps.LatLng(def_lat,def_lng)
          ,mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        
        map = new google.maps.Map(document.getElementById('map_canv'),mapOptions);
        
    }

    init_map();
    
</script>
    