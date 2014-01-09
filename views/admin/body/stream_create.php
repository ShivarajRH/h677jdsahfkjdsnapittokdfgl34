<div class="container">
    <h2>Create a stream</h2>
    
    <form id="add_stream" action="<?php echo site_url("admin/stream_create"); ?>" method="post">
        <table cellpadding="5" cellpacing="5">
            <tr>
                <td width="10%"><label for="st_title">Stream Name: <span class="red_star">*</span></label></td>
                <td>
                    <input type="text" id="st_title" size="40" class="inp" name="st_title"/>
                </td>
            </tr>
            <tr>
                <td><label for="st_description">Short Description:</label>
                	<div class="hint">Optional</div>
                </td>
                <td>
                    <input type="text" id="st_description" class="inp" name="st_description" size="40"/>
                </td>
            </tr>
            <tr>
                <td><label>Assigned To: </label>
                	<div class="hint">Atleast one user need to selected</div>
                </td>
                <td>
                    <ul class="listview">
                    <?php foreach ($adminusers as $adminuser): ?>
                        <li class="users_list" id="users_list_<?=$adminuser['id']?>">
                                        <label for="<?=$adminuser['id']?>_r">
                                            <?php $replacement=  '<span style="font-size:15px;"><strong>'.substr($adminuser['username'], 0,1).'</strong></span>'; ?>
                                            <?=substr_replace($adminuser['username'],$replacement ,0,1)?>
                                        </label>
                                        <span class="perm">
                                            <input type="checkbox" id="<?=$adminuser['id']?>_r" user_id="<?=$adminuser['id']?>" name="permissions_r[]" value="<?=$adminuser['id']?>"/>&nbsp;&nbsp;
                                            <?php /*R<input type="checkbox" id="<?=$adminuser['id']?>_r" name="permissions_r[]" value="<?=$adminuser['id']?>"/>&nbsp;&nbsp;
                                            W<input type="checkbox" id="<?=$adminuser['id']?>_w" name="permissions_w[]" value="<?=$adminuser['id']?>"/>&nbsp;&nbsp;*/?>
                                        </span>
                        </li>
                    <?php endforeach; ?>
                    </ul>
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
</div>
<style type="text/css">
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
    $('.perm input[type="checkbox"]').click(function() {
            var id=$("#"+this.id).attr("user_id");
            var elt=$(this);
            if(elt.is(":checked")) {
                $("#users_list_"+id).css({"background-color":"goldenrod","color":"white"});
            }
            else {
                $("#users_list_"+id).css({"background-color":"#DDDDDD","color":"black"});
            }
    });
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
