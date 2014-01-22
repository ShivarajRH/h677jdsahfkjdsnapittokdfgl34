<div class="container">

<a href="<?php echo site_url('admin/manifestolr_log')?>" style="float:right;" target="_blank" class="button button-flat button-flat-highlight ">View Log</a>
<h2>Bulk Update LR Details</h2>

<div style="float: left;clear: both">
<form  id="upd_lrno_list"action="<?php echo site_url('admin/update_bulk_lrdetails')?>" method="post">
<table class="datagrid" width="100%">
<thead><th>SL no</th><th>Manifesto ID (Comma separated)</th><th>LR No</th><th>No of Box's</th><th>Amount (Rs)</th></thead>
<tbody>
<?php for($i=1;$i<=20;$i++){?>
<tr class="chk_entry"><td><?php echo $i;?></td><td><input type="text" name="manifest_id[]" size="40px"  value="<?php echo set_value('manifest_id')?>"></td><td><input type="text" name="lr_no[]"  value="<?php echo set_value('lr_no')?>"></td><td><input type="text" size="5" name="no_ofboxes[]" value="<?php echo set_value('no_ofboxes')?>"></td><td><input type="text" size="10" name="amt[]" value="<?php echo set_value('amt')?>"></td></tr>
<?php }?>
</tbody>
</table>
<br>
<div style="clear:both">
<div style="float: right;">
	<input type="submit" value="Update LR Nos">
</div>
<div style="float: left;">
	<input name="tm" type="checkbox" value="1" checked>Notify Territory Manager &nbsp;&nbsp;
	<input name="be" type="checkbox" value="1" checked>Notify Bussiness Executives
</div>
</div>
</form>
</div>
</div>
<style>
tfoot{display:none}
.invalid_entry{border:1px solid #cd0000 !important;}
.datagrid td{padding:2px !important;}
</style>
<script>
	$('#upd_lrno_list').submit(function(){
		$('.invalid_entry',this).removeClass('invalid_entry');
		var ttl = 0; 
		$('.chk_entry',this).each(function(){
			
			var ele = $(this);
			// check if manifesto id is entered
			if($('input[name="manifest_id[]"]',ele).val())
			{
				if(!$('input[name="lr_no[]"]',ele).val())
					$('input[name="lr_no[]"]',ele).addClass('invalid_entry');
				
				var nbx = $('input[name="no_ofboxes[]"]',ele).val()*1;
					if(isNaN(nbx))
						$('input[name="no_ofboxes[]"]',ele).addClass('invalid_entry');
					if(!nbx)
						$('input[name="no_ofboxes[]"]',ele).addClass('invalid_entry');	
					
				var amt = $('input[name="amt[]"]',ele).val()*1;
					if(isNaN(amt))
						$('input[name="amt[]"]',ele).addClass('invalid_entry');
					if(!amt)
						$('input[name="amt[]"]',ele).addClass('invalid_entry');	
					
			}else
			{
				ttl++;
			}
		});
		
		if($('.invalid_entry',this).length)
		{
			alert("Please entry all details for manifesto entered");
			return false;
		}
		 
		if(ttl == $('.chk_entry',this).length)
		{
			alert("Please atleast one manifesto details ");
			return false;
		}
		
		
	});
</script>