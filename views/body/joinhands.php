<div class="joinhands">
	<div class="headtop" style="background:#eee;">
		<div class="container"><h1 style="color:#000">Supplier Contact form</h1></div>
	</div>
	<div class="container">
		<div class="handscont">
			<div style="float:right"><img src="<?=base_url()?>images/supplierform.png"></div>
			<h2>Contact Us</h2>
			<div style="padding:20px;font-weight:bold;">
				<form method="post" id="supplierform">
					<table width="500" cellpadding=5>
						<tr>
							<td>Name</td>
							<td align="right"><input class="mand" type="text" name="name" style="width:100%"></td>
						</tr>
						<tr>
							<td>Business Name</td>
							<td align="right"><input class="mand" type="text" name="business" style="width:100%"></td>
						</tr>
						<tr>
							<td>Contact Number</td>
							<td align="right"><input class="mand" type="text" name="contact" style="width:100%"></td>
						</tr>
						<tr>
							<td>Email</td>
							<td align="right"><input class="mand" type="text" name="email" style="width:100%"></td>
						</tr>
						<tr>
							<td>Location</td>
							<td align="right"><input class="mand" type="text" name="location" style="width:100%"></td>
						</tr>
						<tr>
							<td></td>
							<td align="right"><input type="image" src="<?=base_url()?>images/submit.png"></td>
						</tr>
					</table>
				</form>
			</div>
			<div style="clear:both"></div>
		</div>
	</div>
</div>
<script>
$(function(){
	$("#supplierform").submit(function(){
		ef=true;
		$("input.mand",$(this)).each(function(){
			if(!is_required($(this).val()))
			{
				ef=false;
				alert("All fields are mandatory");
				return false;
			}
		});
		if(ef && !is_email($("input[name=email]",$(this)).val()))
		{
			ef=false;
			alert("Please enter a valid email");
		}
		return ef;
	});
});
</script>