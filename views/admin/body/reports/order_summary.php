<div align="left" style="padding:10px;min-height:400px;">
	<h3 class="page_title">Generate Ordersummary</h3>
	<div class="form_block" >
		<form target="hndl_ordersummary_frm" action="<?php echo site_url('admin/reports/generate_ordersummary')?>" method="post">
		
			<table>
				<tr>
					<td valign="top"><b>Choose Order Status</b>
					<br />
					<br />
						<a href="javascript:void(0)" onclick=$('input[name="sel_orderstat[]"]').attr('checked',true)>selectall</a> |
						<a href="javascript:void(0)" onclick=$('input[name="sel_orderstat[]"]').attr('checked',false)>unselectall</a>
					</td>
					<td colspan="2" valign="top">
						<br />
						<br />
						
						<?php 
							$order_status_list = $this->config->item('order_status');
							foreach($order_status_list as $order_stat=>$order_stat_text){
						?>
							<div><input type="checkbox" name="sel_orderstat[]" value="<?php echo $order_stat?>"> : <?php echo $order_stat_text?></div>
						<?php 		
							}
						?>
					</td>
				</tr>
				<tr>
					<td valign="top"><b>From</b></td>
					<td valign="top"><input type="text" id="fromdate" name="from"></td>
				 </tr>
				<tr>
					<td valign="top"><b>To</b></td>
					<td valign="top"><input type="text" id="todate" name="to"></td>
				</tr>
			</table>
			<input type="submit" value="Generate" name="submit" class="sbutton">
		</form>
		<iframe id="hndl_ordersummary_frm" name="hndl_ordersummary_frm" style="width:0px;height:0px;border: none;"></iframe>
	</div>
</div>
<style type="text/css">
h3.page_title{
	font-size: 16px;
	margin: 2px;
}
.form_block{
	padding:5px;
}
</style>
<script type="text/javascript">
var dates = $( "#fromdate, #todate" ).datepicker({
	dateFormat:'dd-mm-yy', 
	changeMonth: true,
	numberOfMonths: 1,
	onSelect: function( selectedDate ) {
		var option = this.id == "fromdate" ? "minDate" : "maxDate",
			instance = $( this ).data( "datepicker" ),
			date = $.datepicker.parseDate(
				instance.settings.dateFormat ||
				$.datepicker._defaults.dateFormat,
				selectedDate, instance.settings );
		dates.not( this ).datepicker( "option", option, date );
	}
});
</script>