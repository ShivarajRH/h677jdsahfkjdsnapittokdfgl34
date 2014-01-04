<div class="container">
    <div>
        <h2>Print Acknowledgements</h2>
    </div>

    <div class="level1_filters">
            <form id="trans_date_form" name="trans_date_form" method="post" action="<?php //echo site_url("admin/print_invoice_acknowledgementbydate"); ?>">
                <table cellpadding="5" cellspacing="0" border="0">

                    <tr>
                        <td valign="middle"><label for="sel_territory">Territory :</label></td>
                        <td colspan="3"><select id="sel_territory" name="sel_territory" style="width:200px;">
                                <option value="00">All Territory</option>
                                <?php foreach($pnh_terr as $terr):?>
                                        <option value="<?php echo $terr['id'];?>"><?php echo $terr['territory_name'];?></option>
                                <?php endforeach;  ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><label for="date_from">From:</label></td>
                        <td><input type="text" id="date_from" name="date_from" value="<?php echo date('Y-m-01',time()-60*60*24*7*4)?>" size="10" /></td>
                        <td><label for="date_to">To:</label></td>
                        <td><input type="text" id="date_to" name="date_to" value="<?php echo date('Y-m-d',time())?>"  size="10"/></td>
                    </tr>
                    
                    <tr>
                        <td><label for="consider_printed_ack">Do you want to consider already <br> printed acknowledgements?</label></td>
                        <td colspan="3" align="left"><input type="checkbox" id="consider_printed_ack" name="consider_printed_ack" value="y" checked/></td>
                    </tr>

                    <tr>
                        <td colspan="4" align="right"><input type="submit" value="Print"></td>
                    </tr>

                </table>
            </form>
    </div>
    <div class="clear"></div>

        <div id="acknowledgement_list_replace_block"></div>

</div>
<script>
// <![CDATA[
   $(document).ready(function() {
        //FIRST RUN
        $( "#date_from").datepicker({
             changeMonth: true,
             dateFormat:'yy-mm-dd',
             numberOfMonths: 1,
             maxDate:0,// minDate: new Date(reg_date),
               onClose: function( selectedDate ) {
                 $( "#date_to" ).datepicker( "option", "minDate", selectedDate ); //selectedDate
             }
           });
        $( "#date_to" ).datepicker({
            changeMonth: true,
             dateFormat:'yy-mm-dd',// numberOfMonths: 1,
             maxDate:0,
             onClose: function( selectedDate ) {
               $( "#date_from" ).datepicker( "option", "maxDate", selectedDate );
             }
        });
    });
    
        
    function loadAcknowledgementList() {
            var sel_territory = $("#sel_territory").find(":selected").val();
            var consider_printed_ack = $("#consider_printed_ack").prop("checked")?'y':'n';
            var date_from = $("#date_from").val();
            var date_to = $("#date_to").val();
            
            var postData = {sel_territory:sel_territory,date_from:date_from,date_to:date_to,consider_printed_ack:consider_printed_ack};
            
            //$.post(site_url+"admin/jx_get_acknowledgement_list",postData,function(resp) {
                //$("#acknowledgement_list_replace_block").html(resp);
            //});
            //return;
    }
    $("#trans_date_form").submit(function(e) {
            e.preventDefault();
            loadAcknowledgementList();
            return false;
    });
    
    
// ]]>
</script>

<?php
