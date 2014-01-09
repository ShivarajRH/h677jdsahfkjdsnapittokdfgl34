<div>
    <!--<input type="radio" value="by_terr" name/>Process by franchise?-->
    
    <form method="post" action="">
        <table width="100%" border="0" cellspacing="4">
           <tr>
               <td>Select Territory:</td>
               <td>
                    <select id="dlg_sel_territory" name="dlg_sel_territory" style="width: 204px;">
                        <option value="00">All</option>
                        <?php  
                            foreach($pnh_terr as $terr) {
                                
                                if($given_terr_id == $terr['id']) {
                                        echo '<option value="'.$terr['id'].'" selected>'.$terr['territory_name'].'</option>';
                                }
                                else {
                                ?>
                                    <option value="<?php echo $terr['id'];?>"><?php echo $terr['territory_name'];?></option>
                    <?php       }
                            }
                    ?>
                    </select>
                   <?php
                    if($given_terr_id!='') {
                        
                        $rdata = $this->reservations->jx_terr_batch_group_status($given_terr_id);
                        echo $given_terr_id.'=>'.$rdata;
                        print_r($rdata); die("=TESTING=");
                        echo "<table class='datagrid'><th>Menu</th><th>Orders</th>".$rdata->detail_category_msg."</table>";
                    }
                    ?>
                </td>
            </tr>
            <?php /* <tr>
                <td>Un-Group Orders status :</td>
                <td> <div class="terr_batch_group_status">
                        
                            
                        
                    </div></td>
            </tr> */ ?>
            <tr>
                <td>Select Menu: <span class="mark">*</span></td>
                <td>
                    <select name="sel_batch_menu" id="sel_batch_menu" style="width: 204px;">
                        <option value="00">All</option>
                        <?php
                        foreach ($batch_conf as $conf) { 
                            
                            ?>
                            <option value="<?=$conf['id'];?>"><?=$conf['batch_grp_name'];?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Number of orders:</td>
                <td>
                    <!--<input type="hidden" name="assigned_menuids" id="assigned_menuids" value="" />-->
                    <input type="text" name="batch_size" id="batch_size" value="30" style="width: 30px;margin:10px 0 5px 5px" />
                    <!--<input type="hidden" name="assigned_uid" id="assigned_uid" value="" />-->
                </td>
            </tr>
            <tr>
                <td>Assigned to:</td>
                <td>
                        <select name="assigned_uid" id="assigned_uid" style="width: 204px;">
                            <option value="00">Choose</option>
                            <?php
                            foreach ($userslist as $user) {?>
                                <option value="<?=$user['id'];?>"><?=$user['username'];?></option>
                            <? } ?>
                        </select>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="sel_status"></div>
                    <div class="create_batch_msg_block"></div>
                </td>
            </tr>
        </table>
    </form>

</div>

<script>
    /*
$("#dlg_sel_town").live("change",function() { 
    var townid=$(this).find(":selected").val();
    var terrid=$("#dlg_sel_territory").find(":selected").val();
    $.post(site_url+"admin/jx_suggest_fran/"+terrid+"/"+townid,function(resp) {
            if(resp.status=='success') {
                 var obj = jQuery.parseJSON(resp.franchise);
                $("#dlg_sel_franchise").html(objToOptions_franchise(obj));
            }
            else {
                $("#dlg_sel_franchise").val($("#dlg_sel_franchise option:nth-child(0)").val());
            }
        },'json').done(done).fail(fail);

    return false;
});*/

 var arr_batch_size=[];
 
//ONCHANGE Territory
/*$("#dlg_sel_territory").chosen().live("change",function() {
    var terrid=$(this).find(":selected").val();*/
//        if(terrid=='00') {          $(".sel_status").html("Please select territory."); return false;        }
    /*$.post(site_url+"admin/jx_suggest_townbyterrid/"+terrid,function(resp) {
        if(resp.status=='success') {
             //print(resp.towns);
            var obj = jQuery.parseJSON(resp.towns);
            $("#dlg_sel_town").html(objToOptions_terr(obj));
        }
        else {
            $("#dlg_sel_town").val($("#dlg_sel_town option:nth-child(0)").val());
            $("#dlg_sel_franchise").val($("#dlg_sel_franchise option:nth-child(0)").val());
                        //$(".sel_status").html(resp.message);
        }
    },'json').done(done).fail(fail);*/
    /*if(terrid != 00) {
            $.post(site_url+"admin/jx_terr_batch_group_status/"+terrid,function(resp) {
                if(resp.status=='success') {
//                    $(".terr_batch_group_status").html("<div>There are <b>"+resp.total_orders+"</b> orders from <b>"+resp.total_categories+"</b> menu.</div>");
                    $(".terr_batch_group_status").html("<table class='datagrid'><th>Menu</th><th>Orders</th>"+resp.detail_category_msg+"</table>");
                            
                    $("#sel_batch_menu").html(objToOptions_menus(resp.arr_menus));
                    
                   arr_batch_size=[];
                    $.each(resp.arr_menus,function(ii,row){
                             arr_batch_size.push({ototal:row.ocount,menuid:row.menuid});
                    });
                    
                }
                else {
                    $(".terr_batch_group_status").html(resp.response);
                    $("#sel_batch_menu").html('<option value="00">No menu found</option>\n');
                    $("#batch_size").val("");
                }
            },"json").done(done).fail(fail);
    }
    return false;
});*/

//ONCHANGE sel_batch_menu
$("#sel_batch_menu").live("change",function() {
    var sel_batch_menu=$(this).find(":selected").val();
//        if(sel_batch_menu=='00') {          $(".sel_status").html("Please select menu."); return false;        }
//    if(sel_batch_menu!='00') {
        
        $.each(arr_batch_size,function(i,val) {
            if(sel_batch_menu == val.menuid) {
                // print(sel_batch_menu+"="+val.menuid);
                $("#batch_size").val(val.ototal);
                return false;
            }
            else {
                $("#batch_size").val("");
            }
        });
//    }
        /*$.post(site_url+"admin/jx_suggest_menus_groupid/"+sel_batch_menu,function(resp) { 
            if(resp.status == "success") {
                 //var obj = jQuery.parseJSON(resp.towns);
                $("#assigned_menuids").val(resp.assigned_menuid);
                $("#batch_size").val(resp.batch_size);
                //$("#assigned_uid").val(resp.assigned_uid);
                //var getlist = getlist(resp.assigned_uid);
                var parse_assigned_uid = jQuery.parseJSON(resp.group_assigned_uid);
                    $("#assigned_uid").html(objToOptions_users(parse_assigned_uid));
            } else {//$("#dlg_sel_town").val($("#dlg_sel_town option:nth-child(0)").val());
                //$("#dlg_sel_franchise").val($("#dlg_sel_franchise option:nth-child(0)").val());
            }
        },'json').done(done).fail(fail);*/
    return false;
});
/*** END BATCH PROCESS  **/
function fail(rdata) {
    console.log(rdata);
}
function done(data) { }
</script>