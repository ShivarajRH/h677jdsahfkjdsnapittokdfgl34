<style>
#hd{display:none;}
#content {
background: transparent;
min-height: 600px;
margin: 10px auto;
height:100%;
}
body{background: #f9f9f9 !important;}
.footerlinks2{display: none;}
.ci_error{margin:0px;padding:0px;}
.ci_error p{margin:0px;text-align: left;padding-left:15px;border-bottom:1px dotted #fff;text-align: center;}
</style>
	
<div align="center" style="margin:10% auto;width:400px;">
	<div align="center" style="background: #3A2E58;border-radius:5px 5px 0px 0px;">
		<a href="<?php echo site_url('admin/dashboard')?>">
			<img style="width:250px;" src="<?php echo base_url();?>/images/paynearhome.png">
		</a>
	</div>
	<div style="background: #FFF;border:1px solid #ccc;border-radius:0px 0px 5px 5px;">
		<?php if(validation_errors()){?>
		<div class="ci_error" style="text-align:center;background: tomato;color: #FFF;line-height: 30px;">
			<?php echo validation_errors();?>
		</div>
		<?php } ?>
		
		<div style="padding:5px 15px;line-height: 30px;padding-top:10px;">
			<form action="<?=site_url("admin/processLogin")?>" method="post" >
				<table width="100%" cellpadding="4" cellspacing="5">
					<tr><td width="70">User Name </td><td><input value="<?=set_value("explo_email")?>" name="explo_email" type="text" style="width:97%;padding:4px;border:1px solid #ddd"></td></tr>
					<tr><td>Password </td><td><input name="explo_password" type="password" style="width:97%;border:1px solid #ddd;padding: 4px;border-radius: 3px;"></td></tr>
					<tr><td align="right" colspan="2"><span class="fl_left" style="font-size: 11px;">Forgot password ?, <a href="javascript:void(0)" id="fgt_pwd_lnk">Click here</a> </span><input class="button  button-rounded button-action" type="submit" onclick="$('.ci_error').hide();this.value='Please wait...';" value="Login"></td></tr>
				</table>
			</form>
		</div>
	</div>
</div>

<div style="display: none">
	<div id="fgt_pwd_dlg"  title="Forgot your password ?">
		<div style="padding:10px;">
			<form onsubmit="return false">
				<b>Email ID:</b>
				<input type="text" class="inp" style="width: 250px;padding:4px;" name="fgt_pwd_email" value="" >
			</form>
		</div>
	</div>
</div>
 

<div align="center" style="bottom:0px;width: 100%;position: fixed;padding:5px 0px;">&copy; Storeking.in</div>