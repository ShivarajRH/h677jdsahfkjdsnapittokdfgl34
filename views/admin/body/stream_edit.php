<div class="container">
    <h2>Edit stream</h2>
    
    <?php
    if(isset($status)) {
        echo '<h3>'.$message.'</h3>';
        
    }
    else {
    ?>
    <form id="add_stream" action="<?php echo site_url("admin/stream_edit").'/'.$streams['id']; ?>" method="post">
        <table cellpadding="5" cellpacing="5">
            <tr>
                <td width="10%"><label for="st_title">Stream Name: <span class="red_star">*</span></label></td>
                <td>
                    <input type="hidden" id="stream_id" size="40" class="inp" name="stream_id" value="<?=$streams['id']?>"/>
                    <input type="text" id="st_title" size="40" class="inp" name="st_title" value="<?=$streams['title']?>"/>
                </td>
            </tr>
            <tr>
                <td><label for="st_description">Short Description:</label>
                	<div class="hint">Optional</div>
                </td>
                <td>
                    <input type="text" id="st_description" class="inp" name="st_description" size="40" value="<?=$streams['description']?>"/>
                </td>
            </tr>
            <tr>
                <td><label>Assigned To: </label>
                	<div class="hint">Atleast one user need to selected</div>
                </td>
                <td>
                    <ul class="listview">
                        <?php
                            foreach ($adminusers as $adminuser): 
                                    $assign_users=$this->db->query("select su.stream_id,su.user_id,su.access,su.created_by from m_stream_users su 
                                                    where su.stream_id=?  and su.user_id=? order by su.user_id",array($streams['id'],$adminuser['id']))->row_array();
                                                $replacement= '<span style="font-size:15px;"><strong>'.substr($adminuser['username'], 0,1).'</strong></span>';
                        ?>
                        <li class="users_list" id="users_list_<?=$adminuser['id']?>">
                                        <label for="<?=$adminuser['id']?>_r"><?=substr_replace($adminuser['username'],$replacement ,0,1)?></label>
                                        <span class="perm">
                                        <?php 
                                                $ckd='';
                                                if($assign_users['user_id'] == $adminuser['id']) {
                                                    $ckd='checked';
                                                    echo '<input type="checkbox" id="'.$adminuser['id'].'_r" user_id="'.$adminuser['id'].'" name="permissions_r[]" '.$ckd.' value="'.$adminuser['id'].'"/>&nbsp;&nbsp;<span class="waiting"></span>';
                                                }
                                                else {
                                                    echo '<input type="checkbox" id="'.$adminuser['id'].'_r" user_id="'.$adminuser['id'].'" name="permissions_r[]" value="'.$adminuser['id'].'"/>&nbsp;&nbsp;<span class="waiting"></span>';
                                                }
                                        ?>
                                            <?php /*R<input type="checkbox" id="<?=$adminuser['id']?>_r" name="permissions_r[]" value="<?=$adminuser['id']?>"/>&nbsp;&nbsp;
                                            W<input type="checkbox" id="<?=$adminuser['id']?>_w" name="permissions_w[]" value="<?=$adminuser['id']?>"/>&nbsp;&nbsp;*/?>
                                        </span>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </td>
            </tr>
            <tr>
                <td><label for="st_description">Status:</label>
                	<div class="hint">Optional</div>
                </td>
                <td>
                    <select id="st_status" class="inp" name="st_status">
                        <option value="1" <?php  echo ($streams['status']=='1')?"selected='selected'":""; ?>>Active</option>
                        <option value="0" <?php  echo ($streams['status']=='0')?"selected='selected'":""; ?>>De-active</option>
                    </select>
                </td>
            </tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr>
                <td></td>
                <td>
                    <input type="submit" id="st_submit" name="st_submit"  class="button button-primary button-rounded" value="Submit"/>
                </td>
            </tr>
        </table>
    </form>
    <? } ?>
</div>
<style type="text/css">
.loading {
    text-align: center;
    margin: 0% 0 0 0%;
    padding-bottom: 5px; 
    visibility: visible; font-size: 10px; 
}
.hint{font-size: 9px;color:#666}
.leftcont {
    display:none;
}
.users_list {
    float: left;
    margin-right:5px;
    margin-bottom:5px;
    text-decoration: none;
    list-style-type:circle;
    width: 213px;
    overflow-x: hidden;
    text-transform: capitalize; 
}
.users_list label,.users_list checkbox {
    cursor: pointer;
}
.listview li{
    width: 250px;
    display: inline-block;
    background: #f1f1f1;
    padding:5px;
}
.listview li span.perm{
    float: right;
}
</style>

<script type="text/javascript">
     $(document).ready(function() {
        $('.perm input[type="checkbox"]').each(function() {
                check_options_each(this);
        });
        $('.perm input[type="checkbox"]').live("click",function() {
                check_options(this);
                
        });
       
        
    });
     function check_options_each(elt) {
        var userid=$("#"+elt.id).attr("user_id");
        var elt=$(elt);
        if(elt.is(":checked")) {
            $("#users_list_"+userid).css({"background-color":"goldenrod","color":"white"});
        }
        else {
            $("#users_list_"+userid).css({"background-color":"#DDDDDD","color":"black"});
        }
    }
     
     function check_options(elt) {
        var stream_id=$("#stream_id").val();
        var userid=$("#"+elt.id).attr("user_id");
        var elt=$(elt);
        if(elt.is(":checked")) {
            $("#users_list_"+userid+" .waiting").html('<span class="loading">&nbsp;</span>');
            $("#users_list_"+userid).css({"background-color":"goldenrod","color":"white"});
            save_assign_user(stream_id,userid);
        }
        else {
            $("#users_list_"+userid+" .waiting").html('<span class="loading">&nbsp;</span>');
            $("#users_list_"+userid).css({"background-color":"#DDDDDD","color":"black"});
            remove_assign_user(stream_id,userid);
        }
    }
    
    function save_assign_user(stream_id,userid) {
        var stream_data="stream_id="+stream_id+"&userid="+userid;
        $.post(site_url+"/admin/jx_save_assign_user",stream_data,function(rdata) {//print(rdata);
            $("#users_list_"+userid+" .waiting").html('');
        });
    }
    function remove_assign_user(stream_id,userid) {
        var stream_data="stream_id="+stream_id+"&userid="+userid;
        $.post(site_url+"/admin/jx_remove_assign_user",stream_data,function(rdata) { //print(rdata);
            $("#users_list_"+userid+" .waiting").html('');
        });
    }
    
    $('#add_stream').submit(function(){
            var title = $.trim($('#st_title').val());
            $('#st_title').val(title);
            if(!title)
            {
                    alert("Enter Stream name");$('#st_title').focus();
                    return false;
            }
            
            
            if(!$('.perm input[type="checkbox"]:checked').length)
            {
                    alert("Please Choose atleast one user from the list");
                    return false;
            }

            return true;
    });
</script>
