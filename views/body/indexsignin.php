<div style="">

	<div class="container">
		<div class="indexsigninup">
			<h1>Sign in</h1>
			<h3>you're ready to get awesome deals</h3>
			<div class="panel">
				<div class="links">
					<a href="<?=site_url("")?>" class="first selected">Create an account</a>
					<a href="javascript:void(0)" id="ind_signin">Sign In</a>
				</div>
				<div class="indcont" id="ind_signin_cont">
					<form action="<?=site_url("emailsignin")?>" method="post">
						<table class="indemailtable indsignintable" style="width:500px;">
				<?php if($_POST){?>
							<tr><td colspan="2"><div style="color:red;"><b>Invalid email or password</b></div></td></tr>
				<?php }?>
							<tr>
								<td>Email</td>
								<td><input type="text" name="email">
								</td>
							</tr>
							<tr>
								<td>Password</td>
								<td><input type="password" name="password"></td>
							</tr>
							<tr>
							<td></td>
							<td><input type="image" src="<?=base_url()?>images/signin.png" style="border:0px;width:auto;"></td>
							</tr>
						</table>
					</form>
				</div>
			</div>
		</div>
	</div>

</div>

<script>
$(function(){
	$("#indemailfrm2").submit(function(){
		ef=true;
		$("input:not([type=image])",$(this)).each(function(){
			if(!is_required($(this).val()))
			{
				alert("All fields mandatory");
				ef=false;
				return false;
			}
		});
		if(!ef)
			return false;
		if(!is_mobile($("input[name=mobile]",$(this)).val()))
		{
			alert("Please enter a valid mobile number");
			return false;
		}
		if($("input[name=password]",$(this)).val()!=$("input[name=cpassword]",$(this)).val())
		{
			alert("Passwords are not same. Please type again");
			return false;
		}
		if(!is_email($("input[name=email]",$(this)).val()))
		{
			alert("Please enter a valid email");
			return false;
		}
		return true;
	});
	$("#indemailfrm").submit(function(){
		if(!is_email($(".indemail").val()))
		{
			$("#invalidemail").show();
			return false;
		}
		$(this).hide();
		$("#indemailfrm2").show();
		$("#indemailhidden").val($(".indemail").val());
		return false;
	});
	$(".links a").click(function(){
		$(".links a").removeClass("selected");
		$(this).addClass("selected");
		$(".indcont").hide();
		$("#"+$(this).attr("id")+"_cont").show();
	});
});
</script>

<style>
.indexsigninup{
padding:20px 10px;
}
.indexsigninup h1{
margin-bottom:0px;font-size:250%;
}
.indexsigninup h3{
padding-left:5px;
padding-top:5px;
}
.indexsigninup .panel{
 padding:30px;
}
.indexsigninup .panel a{
display:inline-block;
font-size:150%;
color:#43B9D6;
padding:10px;
text-decoration:none;
font-weight:bold;
margin:0px 5px;
}
.indexsigninup .panel a.selected{
color:#183C44;
}
.panel a.first{
border-right:2px solid #aaa;
padding-right:15px;
}
.indexsigninup .links{
width:70%;
background:#eee;
color:#000;
padding-left:50px;
border-bottom:2px solid #aa8;
opacity:0.8;
}
#indemailfrm{
color:#163C44;
font-weight:bold;
margin:40px;
font-size:120%;
}
.indemail{
margin-left:10px;
width:350px;
font-size:100%;
padding:7px 10px;
-moz-border-radius:5px;
border-radius:5px;
}

.indemailtable{
background:#F4F4F4;
color:#000;
width:600px;
font-weight:bold;
margin:20px;
padding:5px;
padding-right:10px;
}
.indemailtable td{
width:50%;
padding:5px;
}
.indemailtable input{
width:98%;
padding:5px;
border:1px solid #D8D8D8;
}
.indsignintable{
padding:10px;
padding-right:20px;
}
.indsignintable td{
font-size:130%;
width:auto;
}
#invalidemail{
color:red;
font-size:70%;
padding-bottom:5px;
display:none;
}
</style>