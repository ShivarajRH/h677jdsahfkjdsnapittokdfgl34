<div class="container">
	<h3>Export Sales Report for Tally Import</h3>
	<form target="_hndl_sales_report_frm" action="<?php echo current_url();?>" method="post">
		<table>
			<tr>
				<td><b>Start Date</b></td>
				<td><input type="text" id="dp_stdate" name="stdate" value="<?php echo $stdate;?>" /> </td>
			</tr>
			<tr>
				<td><b>End Date</b></td>
				<td><input type="text" id="dp_endate" name="endate" value="<?php echo $endate;?>" /> </td>
			</tr>
			<tr>
				<td><b>Sales From</b></td>
				<td>
					<select name="sales_by">
						<option value="all">All</option>
						<option value="sit">Snapittoday</option>
						<option value="sit_part">Snapittoday & Partner Sales</option> 
						<option value="pnh">Paynearhome</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" value="Generate" />
				</td>
			</tr>
		</table>
	</form>
	<iframe name="_hndl_sales_report_frm" id="_hndl_sales_report_frm" style="width: 1px;height: 1px;border:0px;"></iframe>
</div>
<script>
  $(function() {
    $( "#dp_stdate" ).datepicker({
      defaultDate: new Date(),
      changeMonth: true,
      onClose: function( selectedDate ) {
        $( "#dp_endate" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#dp_endate" ).datepicker({
      defaultDate: new Date(),
      changeMonth: true,
      onClose: function( selectedDate ) {
        $( "#dp_stdate" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
  });
  </script>