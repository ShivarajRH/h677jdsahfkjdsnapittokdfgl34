<div style="background:#43B9D6;padding-bottom:5px;" align="center">
	
	<div class="container">
		
		<div align="left" style="padding-top:10px;margin:10px;">
			<div style="border:1px solid #aaa;-moz-border-radius:10px;padding:">
				
				<div style="-moz-border-radius:10px 10px 0px 0px;background:url(<?=base_url()?>images/checkout_bg.png);padding:10px;font-size:18px;font-weight:bold;">
				CheckOut
				</div>
				
				<div style="background:#fff">
				
					<div style="padding:10px;">
					
						
						<div style="margin-bottom:10px;background:#FAFFD3;padding:10px;">
							<div style="border-bottom:1px solid #aaa;padding-bottom:3px;"><b>Express Checkout</b></div>
							<div style="padding-top:3px;">Don't have an Account? Are you a new user?</div>
							<div align="right" style="padding:5px 0px;"><img src="<?=base_url()?>images/continue_m.png" onclick='location="<?=site_url("checkout/step2")?>"' style="cursor:pointer;"></div>
						</div>
					
					
						<div style="border-bottom:1px solid #aaa;padding-bottom:3px;margin-top:25px;"><b>Snapittoday</b> Users signin</div>
					
						<form action="<?=site_url("emailsignin")?>" method="post">
							<div style="padding-right:10px;">
								<div style="padding-top:10px;">
									Email Address<br>
									<input type="text" name="email" style="border:1px solid #bbb;padding:0px;width:100%;">
								</div>
								<div style="padding-top:10px;">
									Password<br>
									<input type="password" name="password" style="border:1px solid #bbb;padding:0px;width:100%;">
								</div>
								<div align="right" style="padding:10px 0px;margin-right:0px;">
									<input type="image" src="<?=base_url()?>images/signin_only.png">
								</div>
							</div>
						</form>
					</div>
			</div>
		</div>
		
	</div>
</div>

<?php
