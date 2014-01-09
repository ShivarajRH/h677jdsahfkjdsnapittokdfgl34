<?php $user=$this->session->userdata("user");?>
<div style="width:600px;padding:10px;background:#fff;">
<div>
	<div style="font-weight:bold;clear:left;float:right;">You have to select atleast <span id="cws_min" class="green"></span> coworkers</div>
<h2 style="padding-bottom:5px;">Invite friends to get this deal</h2>
</div>

<div style="float:left;width:430px;padding:10px;" align="left">
	<a id="cws_fb_getdata" class="cws_fancylink" href="<?=site_url("fbinviteforbp")?>"></a>
	<a style="display:inline-block;text-align:center;color:inherit;text-decoration:none;" href="javascript:void(0)" onclick='cws_fblogin()'><img src="<?=IMAGES_URL?>facebook.png"><br>Facebook Friends</a>
</div>

<div style="clear:left;font-size:120%;width:600px;border-width:20px 0px 20px 20px;">
	<div id="cws_fbselected">
		You have selected <b><span id="cws_fb_sels"></span></b> facebook friends
	</div>
	<div id="cemails">
		<div style="padding:5px;font-size:90%;">
		Invite persons by email ID<br>
			<form id="cws_sel_form" style="padding-top:5px;"><input type="text" style="padding:5px;width:250px;" class="cws_em_inp"><input type="submit" value="Add"></form>
		</div>
		<div id="cws_emails"></div>
	</div>
	<div align="right" style="float:right;margin-top:10px;"><img src="<?=base_url()?>images/continue.png" style="cursor:pointer;" onclick='cws_done_trig()'></div>
</div>

<div class="clear"></div>
<div id="fb-root"></div>

</div>
<style>
#cws_fbselected{
display:none;
color:#fff;
background:#1A93DA;
padding:5px;
margin:3px;
float:left;
width:410px;
}
.slider .slider-nav li a{
line-height:10px !important;
}
#cemails{
width:590px;
float:left;
margin-top:5px;
border:1px solid #ccc;
background:#fafafa;
padding:5px;
}
#cws_emails{
height:60px;
position:relative;
overflow:auto;
}
.selcws{
float:left;
white-space:nowrap;
padding:5px 10px;
padding-right:5px;
-moz-border-radius:5px;
border-radius:5px;
background:#2B71D8;
color:#fff;
font-size:75%;
margin:3px;
font-weight:bold;
}
#cws_sel{
margin-bottom:5px;
float:left;
width:415px;
background:#fafafa;
position:relative;
height:140px;
overflow:auto;
border:1px solid #ccc;
}
.selcws .close{
font-weight:normal;
color:#eee;
margin:-3px 0px;
padding:3px 0px;
cursor:pointer;
padding-left:3px;
margin-left:5px;
float:right;
border-left:1px solid #fff;
}
</style>

<script>
var selectedcoworkers=[],cws_emails=[];

function selectcoworker(uid,name)
{
	for(i=0;i<selectedcoworkers.length;i++)
	{
		if(selectedcoworkers[i][0]==uid)
			return;
	}
	selectedcoworkers[selectedcoworkers.length]=[uid,name];
	$("#cws_sel").append('<div class="selcws"><div class="close" onclick=\'$(this).parent().remove();removesel('+uid+')\'>X</div>'+name+'</div>');
}

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

function removesel(uid)
{
	tmp=selectedcoworkers;
	selectedcoworkers=[];
	for(i=0;i<tmp.length;i++)
	{
		if(tmp[i][0]==uid)
		{
			i++;
			break;
		}
		selectedcoworkers.push(tmp[i]);
	}
	if(i<tmp.length)
	for(;i<tmp.length;i++)
			selectedcoworkers.push(tmp[i]);
}

function removeemail(uid)
{
	tmp=cws_emails;
	cws_emails=[];
	for(i=0;i<tmp.length;i++)
	{
		if(tmp[i][0]==uid)
		{
			i++;
			break;
		}
		cws_emails.push(tmp[i]);
	}
	if(i<tmp.length)
	for(;i<tmp.length;i++)
			cws_emails.push(tmp[i]);
}
  window.fbAsyncInit = function() {
	  FB.getLoginStatus(function(response) {
		  if (response.authResponse) {
			  fb_loggedin=1;
		  } else {
			  fb_loggedin=0;
		  }
		});
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

function initcs(){
	if(cws_loaded_once==0)
	{
	d=document;
    var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
    js = d.createElement('script'); js.id = id; js.async = true;
    js.src = "//connect.facebook.net/en_US/all.js";
    d.getElementsByTagName('head')[0].appendChild(js);

	cws_loaded_once=1;
	}

	$(".cws_fancylink").fancybox();

	if(fbs.length>0)
	{
		$("#cws_fb_sels").html(fbs.length);
		$("#cws_fbselected").show();
	}else $("#cws_fbselected").hide();
	
	if(cws_min<0)
		cws_min=0;
	if(cws_min!=0)
	$("#cws_min").html(cws_min);
	else
		$("#cws_min").parent().hide();
	$("#cws_sel_form").submit(function(){
		em=$(".cws_em_inp",$(this)).val();
		$(".cws_em_inp",$(this)).val("");
		if(!is_email(em))
		{
			alert("Please enter a valid email");
			return false;
		}
		for(i=0;i<cws_emails.length;i++)
		{
			if(cws_emails[i]==em)
				return false;
		}
		eid=cws_emails.length;
		cws_emails.push(em);
		$("#cws_emails").append('<div class="selcws"><div class="close" onclick=\'$(this).parent().remove();removeemail('+eid+')\'>X</div>'+em+'</div>');
		return false;
	});
//	$("#slider").sliderNav({height:'430',width:'300'});
}
function cws_done_trig()
{
	if(selectedcoworkers.length+cws_emails.length+fbs.length<cws_min)
	{
		alert("Please select atleast "+cws_min+" coworkers or friends");
		return;
	}
	cws_done(selectedcoworkers,cws_emails);
}
</script>

<?php
