<div class="container">
	<h2>Bulk Cancel Orders</h2>
	<form method="post" id="cancel_bulkorders">
		<textarea rows="5" cols="45" style="width: 90%;" name="transids"></textarea>
		<br />
		
		<input type="submit" value="Submit">
	</form>
</div>

<script type="text/javascript">
	$('#cancel_bulkorders').submit(function(){
		var stat = '';
		var transids = $('textarea',this).val();
			transids = transids.replace(/\n/g,",");
			transids = transids.replace(/,,/g,",");
			$('textarea',this).val($.trim(transids));
			if(!transids)
			{
				alert("Please enter atlease one transaction id");	
				return false;
			}
			if(!confirm("Are you sure you want to proceed transaction cancellation"))
			{
				return false;
			}
	});
</script>