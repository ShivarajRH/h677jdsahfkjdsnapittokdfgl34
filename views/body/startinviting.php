<div style="background:#fff;padding:0px;">
<div class="container">
<div id="tabs" style="padding:0px;">
	<div id="invite">
		<div style="padding:0px;">
		<form action="<?=site_url("invite")?>" id="invitefrm" method="post">
			<div style="margin:20px;width:935px;margin-left:20px;height:910px;background:url(<?=IMAGES_URL?>invite_fb_bg.png) no-repeat;">
			<div style="padding-top:415px;padding-left:37px;">
			<h2>Start Inviting</h2>
			<div style="padding-left:20px;">
				<h4 style="color:#A8518A;margin-top:20px;">Invite your Facebook friends</h4>
				<a href="javascript:void(0)" onclick='cws_fblogin()' id="invitefb" style="color:blue"><img src="<?=IMAGES_URL?>fblogin.gif"></a>
			</div>	
				<a id="cws_fb_getdata" class="cws_fancylink" href="<?=site_url("fbinviteforbp")?>"></a>
			</div>
			</div>
		</form>
		</div>
	</div>
	
	
</div>	

</div>
</div>

<a href="#invitefb_lastcont" id="invitefb_last"></a>
<div style="display:none">
<div id="invitefb_lastcont" style="width:500px;">
<h2>Please enter your message</h2>
<div style="padding:10px 5px;">
<textarea id="invitefb_msg" style="width:450px;height:120px;">I invite you to join Snapittoday.com, a one stop destination for all your fashion, cosmetics & beauty products.</textarea>
</div>
<div align="right">
<input type="button" value="Invite my Facebook friends" onclick='final_invitefb_proc()' style="font-size:120%;">
</div>
</div>
</div>

<script>
var bpid=0,cws_min=0,cws_loaded_once=0,fbs=[];

var fb_loggedin=0;

function cws_done(selectedcoworkers,cws_emails)
{
	if(selectedcoworkers.length==0 && cws_emails==0)
	{
		alert("You haven't selected any coworkers");
		return;
	}
	$.fancybox.showActivity();
	pst="bpid="+bpid+"&cws="+selectedcoworkers.join(",")+"&emails="+cws_emails.join(",");
	$.post("<?=site_url("jx/extendbp")?>",pst,function(){
		location.reload(true);
	});
}
function setbpid(a_bpid)
{
	bpid=a_bpid;
}

function final_invitefb_proc()
{
	$.fancybox.close();
	$.fancybox.showActivity();
	$.post('<?=site_url("jx/invitefbfriends")?>','fbs='+fbstr+"&msg="+$("#fancybox-content #invitefb_msg").val(),function(){
		$.fancybox.hideActivity();
		alert("Your friends are invited. Thanks for inviting!");
	});
}

function cws_fb_done(selfbs)
{
	fbs=[];
	for(i=0;i<selfbs.length;i++)
		fbs.push(selfbs[i][0]);
	fbstr=fbs.join(",");
	if(fbs.length==0)
		alert("You haven't selected any friends");
	else
	$("#invitefb_last").click();
}
window.fbAsyncInit = function() {
	   FB.init({
		    appId  : '<?=FB_APPID?>',
		    status : true, // check login status
		    cookie : true, // enable cookies to allow the server to access the session
		    oauth  : true // enable OAuth 2.0
		  });
	  FB.getLoginStatus(function(response) {
		  if (response.authResponse) {
			  fb_loggedin=1;
		  } else {
			  fb_loggedin=0;
		  }
		});
};


function cws_fblogin()
{
	if(fb_loggedin==1)
	{
		$.fancybox.showActivity();
		$("#cws_fb_getdata").click();
		return;
	}
	 FB.login(function(response) {
		   if (response.authResponse) {
			   $.fancybox.showActivity();
			   $("#cws_fb_getdata").click();
		   } else {
			  alert("Please login to your Facebook Account to invite your friends");
		   }
		 }, {scope: 'email,publish_stream'});
}


var direct_fb_loading=true;
var cws_min=0;

$(function(){
	d=document;
    var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
    js = d.createElement('script'); js.id = id; js.async = true;
    js.src = "//connect.facebook.net/en_US/all.js";
    d.getElementsByTagName('head')[0].appendChild(js);

	cws_loaded_once=1;

	window.scrollTo(0,0);

	$("#cws_fb_getdata, #invitefb_last").fancybox();

	$("#pr_invitebp_trig").fancybox({
		'onComplete':function(){initcs();return false;}
	});

	$(".pr_invmore").click(function(){
		$("#pr_invitebp_trig").click();
	});
	$("#pro_cpass").submit(function(){
		if($("input[name=password]",$(this)).val()=="")
		{
			alert("Enter new password");
			return false;
		}
		if($("input[name=password]",$(this)).val()!=$("input[name=cpassword]",$(this)).val())
		{
			alert("Passwords are not same");
			return false;
		}
		return true;
	});

	if(location.hash!="")
		$("#tabs ul li a[href="+location.hash+"]").click();

	$(".faqs li .q:first").click();
});
</script>
<div  style="display:none;">
<div id="chnpss_cont" style="padding:10px">
<h3>Change Password</h3>
					<form action="<?=site_url("changepwd")?>" method="post" id="pro_cpass">
<table width=400 cellpadding=5>
<tr>
						<td>New Password :</td><td><input type="password" name="password"></td>
</tr>
<tr>
						<td>Confirm Password :</td><td><input type="password" name="cpassword"></td>
</tr>
<tr>
						<td></td><td><input type="submit" value="Update"></td>
						</tr>
</table>					
					</form>
</div></div>
<div id="fb-root"></div>