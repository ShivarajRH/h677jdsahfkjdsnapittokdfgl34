<?php $user=$this->session->userdata("user");?>
<div style="width:770px;padding:10px;background:#fff;">
<div>
	<h2 style="padding-bottom:5px;">Invite your Facebook friends to get this deal</h2>
</div>

<div style="clear:right;float:right;background:#777;color:#fff;font-weight:bold;text-align:center;cursor:pointer;">
<?php $alphas=array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
foreach($alphas as $a){$a=strtolower($a);?>
<div style="padding:1px 2px 1px 2px;" onclick='$("#slider li").removeClass("selected");$("#sliderip_<?=$a?>").addClass("selected");$("#slider").scrollTop(document.getElementById("sliderip_<?=$a?>").offsetTop)'><?=strtoupper($a)?></div>
<?php }?>
</div>

<div id="slider" style="float:right;border-left:1px solid #333;width:300px;height:443px;position:relative;overflow:auto;">
    <div class="slider-content">
        <ul>
        <?php foreach($friends as $a=>$cws){?>
            <li id="sliderip_<?=$a?>"><a name="<?=$a?>" class="title"><?=strtoupper($a)?></a>
                <ul>
                <?php foreach($cws as $c){?>
                    <li>
                    <?php if($c['status']!="2"){?>
                    	<a href="javascript:void(0)" onclick='selectfriend("<?=$c['id']?>","<?=$c['name']?>")'><?=$c['name']?></a>
                    <?php }else{?>
                    	<a title="You can't invite this friend"><?=$c['name']?></a>
                    <?php }?>
                    </li>
                <?php }?>
                </ul>
            </li>
        <?php }?>
        </ul>
    </div>
</div>

<div style="clear:left;height:400px;font-size:120%;width:450px;border-width:20px 0px 20px 20px;">
	<div style="padding:5px;font-size:12px;">Select your friends from the right panel</div>
	<div style="padding:10px" id="cws_fb_sel">
	</div>
	<div align="right" style="float:right;padding-right:15px;margin-top:10px;"><img src="<?=base_url()?>images/continue.png" style="cursor:pointer;" onclick='cws_fb_done_trig()'></div>
</div>

<div class="clear"></div>

</div>
<style>
.slider .slider-nav li a{
line-height:10px !important;
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
#cws_fb_sel{
margin-bottom:5px;
float:left;
width:415px;
background:#fafafa;
position:relative;
height:240px;
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
var selectedfbs=[];

function selectfriend(id,name)
{
	for(i=0;i<selectedfbs.length;i++)
	{
		if(selectedfbs[i][0]==id)
			return;
	}
	selectedfbs[selectedfbs.length]=[id,name];
	$("#cws_fb_sel").append('<div class="selcws"><div class="close" onclick=\'$(this).parent().remove();removesel('+id+')\'>X</div>'+name+'</div>');
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

function cws_fb_done_trig()
{
	cws_fb_done(selectedfbs);
}
var fbemail="<?=$fbemail?>";
</script>

<?php
