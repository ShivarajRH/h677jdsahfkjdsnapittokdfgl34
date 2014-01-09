<?php $user=$this->session->userdata("user");
?>
<div>
	<div class="container">
	<div style="padding:40px;font-size:110%;">
		<h1>Get Verified</h1>
		<div style="padding:10px;font-weight:bold;">Only verfied accounts can make checkouts!</div>
		<div style="padding:0px 10px 10px 10px;">We have sent you an access code.<br>Please check your mail at <b><?=$user['email']?></b> and messages at <b><?=$user['mobile']?></b> for access code</div>
		<form method="post">
			<table cellpadding=10 style="font-size:120%;font-weight:bold;background:#eee;border:1px solid #aaa;padding-top:10px;margin:10px;margin-left:50px;">
				<?php if(isset($error)){?>
				<tr>
					<td colspan=3 style="color:red;font-weight:bold;"><?=$error?></td>
				</tr>
				<?php }?>
				<tr>
					<td>Enter access Code</td><td>:</td><td><input type="text" name="code" style="width:200px;"></td>
				</tr>
				<tr>
					<td colspan=2></td><td><input type="submit" value="Get Verified!"></td>
				</tr>
			</table>
		</form>
	</div>
	</div>
</div>
<?php
