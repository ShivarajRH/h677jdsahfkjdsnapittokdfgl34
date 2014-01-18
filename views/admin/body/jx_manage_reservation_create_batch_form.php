<div>
    <!--<input type="radio" value="by_terr" name/>Process by franchise?-->
    <?php
    if($sel_town_id!=0)
        echo "Town : <b>".$this->db->query("select town_name from pnh_towns where id = ? ",$sel_town_id)->row()->town_name.'</b>';
//    else echo '-Not set-';
    if($sel_fran_id!=0)
        echo "<div>Franchise selected : <b>".$this->db->query("select franchise_name from pnh_m_franchise_info where franchise_id = ? ",$sel_fran_id)->row()->franchise_name."</b></div>";
    ?>
    
    <form method="post" action="">
        <table width="100%" border="0" cellspacing="0" cellpadding="5">
           <tr>
               <td>Select Territory:</td>
               <td>
                   <input type="hidden" name="dlg_sel_town" id="dlg_sel_town" value="<?=$sel_town_id;?>" />
                   <input type="hidden" name="dlg_sel_franchise" id="dlg_sel_franchise" value="<?=$sel_fran_id;?>" />
                   
                    <select id="dlg_sel_territory" name="dlg_sel_territory" style="width: 204px;">
                        <option value="00">All</option>
                        <?php  
                            foreach($pnh_terr as $terr) {
                                
                                if($sel_terr_id == $terr['id']) {
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
                        echo "<table class='datagrid'><th>Menu</th><th>Orders</th>".$rdata->detail_category_msg."</table>";
                    }
                    ?>
                </td>
            </tr>
            <?php
            /* <tr>
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
                <td>Available transactions:</td>
                <td>
                    <span style="margin:10px 0 5px 5px; font-weight: bold;" class="batch_enable_all_orders">0</span>
                </td>
            </tr>
            <tr>
                <td>Max Batch Size:</td>
                <td>
                    <!--<input type="hidden" name="assigned_menuids" id="assigned_menuids" value="" /><input type="hidden" name="assigned_uid" id="assigned_uid" value="" />-->
                    <span style="margin:10px 0 5px 5px;"><input type="text" name="batch_size" id="batch_size" value="20" style="width: 30px;" /></span>
                </td>
            </tr>
            <tr>
                <td>Assigned to a user:</td>
                <td>
                        <select name="assigned_uid" id="assigned_uid" style="width: 204px;" class="assigned_uid"></select>
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
 var arr_batch_size=[];
 
//ONCHANGE sel_batch_menu
$("#sel_batch_menu").live("change",function(e) {
        
        var ele  = $(this).parent().find('option:selected');
        $("#batch_size").val(ele.attr('default_batch_size'));
        $(".batch_enable_all_orders").html(ele.attr('ordcount'));
        if($(this).val() != '00')
        {
            $('#assigned_uid').find('option:gt(0)').hide();
            $.each(ele.attr('batch_userids').split(','),function(a,uid){
                $('#assigned_uid option.bc_uid_'+uid).show();
            });

        }else
        {
            $('#assigned_uid').find('option').show();

        }

        
        
});

//ONCHANGE Territory
$("#dlg_sel_territory").live("change",function() {
    var terrid=$(this).find(":selected").val();
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
            $.post(site_url+"admin/jx_terr_batch_group_status/"+terrid,function(resp) {
                if(resp.status=='success') {
                    var menulist_opts = '<option value="00" default_batch_size="20" >All</option>';
                        $.each(resp.arr_menus,function(ii,row){
                          menulist_opts += '<option batch_userids="'+row.bc_group_uids+'" value="'+row.menuid+'" ordcount="'+row.ordcount+'" default_batch_size="'+row.batch_size+'">'+row.menuname+'</option>';
                        });
                        
                        $("#sel_batch_menu").html(menulist_opts);//.trigger('liszt:updated');
                        $("#sel_batch_menu").trigger('change');
                    
                    var userlist_opts = '<option value="00" class="" >Choose</option>';
                        $.each(resp.bc_userids,function(uid,uname){
                            userlist_opts += '<option value="'+uid+'" class="bc_uid_'+uid+'">'+uname+'</option>';
                        });
                        $("#assigned_uid").html(userlist_opts);      
                        
                        $(".batch_enable_all_orders").html(resp.total_orders);
                }
                else
                {
                    $("#sel_batch_menu").html('<option value="00">No menu found</option>\n');
                    $("#assigned_uid").html('<option value="00">No users found</option>');
                    $("#batch_size").val("");
                }
                
                $('#dlg_sel_territory').trigger("liszt:updated");
                
            },"json");
    return false;
});


$("#dlg_sel_territory").trigger("change");
//$("#sel_batch_menu").chosen();
//$('#dlg_sel_territory').chosen();

</script>

