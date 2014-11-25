<link rel="stylesheet"	href="<?php echo site_url(); ?>css/datatable/manage.member.css" type="text/css" />
<script	src="<?php echo site_url(); ?>js/datatable/manage.member.js" type="text/javascript"></script>
<script type="text/javascript" charset="utf-8">  
$(document).ready(function() {
	js_members_planlist();
});
</script>
<h2 class="page_title">Member plan List</h2>
 <form method="post" id="comboadd_amount" action="<?php echo site_url('admin/update_planamount_detail');?>" >
<div class="container page_wrap">
	<div class="page_top">
		
	
	</div>

		<table id="memberplan_table" class="display " cellspacing="0" width="100%">
			<thead>
				<tr>
				    <th>Member Plan Id</th>
					<th>Member ID</th>
					<th class="franchisename">Franchise Name</th>
					<th>Member Name</th>
					<th>Mobile No</th>
					<th>Plan Type</th>
					<th>Installment</th>
					<th>Plan Amount</th>
					<th>Receipt Amount</th>
					<th>Started Month</th>
					<th>End Month</th>
					<th>Status</th>
					<th> <input type="checkbox" id="flowcheckall" value="" />&nbsp;Bulk Active</th>
					<th>Action</th>
					
				</tr>
			</thead>
		</table>
</div>
    <div id="dialog-form" style="display:none;" >
        <fieldset style="background-color:#dfe4ff;"> 
            <table>
                <tr>
                 <td style="text-align:left;width: 50%;">
                 Member Amount
                 </td>
              
                    <td>
                    <input type="text" id="totalamount"  name="totalamount" readonly>
                    </td>
                </tr>
            </table>
        </fieldset>
          <br />
        <fieldset style="background-color:#dfe4ff;"> 
            <table>
                <tr>
                 <td style="text-align:left;width: 39%;">Reciept Value</td>
                   <td style="width: 20%;" >
                      <input type="text" id="receiptidval"  name="receiptidval">
                    </td>
                     <td >
                      <button type="text" id="validatereceipt"  name="validatereceipt">OK</button>
                    </td>
                     <td>
                    
                    </td>
                </tr>
            </table>
             <span class="errormsg1" style="color: red;display:none;"></span>
        </fieldset>
        <br />
        <fieldset style="background-color:#dfe4ff;"> 
            <table>
                <tr>
                <td>Reciept Amount</td>
                    <td style="text-align:left;width: 50%;">
                      <input type="text" id="receiptval"  name="receiptval" readonly><span class="errormsg" style="color: red;display:none;">Receipt value field is empty</span>
                    </td>
                </tr>
            </table>
        </fieldset>
        <!--    </br>
        <fieldset style="background-color:#dfe4ff;"> 
            <table>
                <tr>
                 <td style="text-align:left;">
                 Receipt Balance
                 </td>
              
                    <td>
                    <input type="text" id="balanceval"  name="balanceval" readonly><span class="errorbal" style="color: red;display:none;">Receipt Balance value is wrong</span>
                    </td>
                </tr>
            </table>          
        </fieldset>-->
       </br>
        <fieldset style="background-color:#dfe4ff;"> 
            <table>
                <tr>
                 <td style="text-align:left;width: 50%;">
                 Unreconcile Amount
                 </td>
              
                    <td>
                    <input type="text" id="balanceval"  name="balanceval" readonly><span class="errorbal" style="color: red;display:none;">Receipt Balance value is wrong</span>
                    </td>
                </tr>
            </table>          
        </fieldset>
</div>
 </form>
