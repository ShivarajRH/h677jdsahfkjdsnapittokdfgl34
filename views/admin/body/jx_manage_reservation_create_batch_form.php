<div>
    <form method="post" action="">
        <table width="100%" border="0" cellspacing="4">
           <tr>
               <td>Select Territory:</td>
               <td>
                    <select id="dlg_sel_territory" name="dlg_sel_territory" style="width: 204px;">
                        <option value="00">All Territory</option>
                        <?php  foreach($pnh_terr as $terr): ?>
                                <option value="<?php echo $terr['id'];?>"><?php echo $terr['territory_name'];?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Un-Group Orders status :</td><td> <div class="terr_batch_group_status"></div></td>
            </tr>
            <tr>
                <td>Select Menu: <span class="mark">*</span></td>
                <td>
                    <select name="sel_batch_menu" id="sel_batch_menu" style="width: 204px;">
                        <option value="00">Choose</option>
                        <?php
                        foreach ($batch_conf as $conf) {?>
                            <option value="<?=$conf['id'];?>"><?=$conf['batch_grp_name'];?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Number of orders:</td>
                <td>
                    <!--<input type="hidden" name="assigned_menuids" id="assigned_menuids" value="" />-->
                    <input type="text" name="batch_size" id="batch_size" value="" style="width: 30px;margin:10px 0 5px 5px" />
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
                    <div class="create_batch_msg_block"></div>
                </td>
            </tr>
        </table>
    </form>
    
</div>
