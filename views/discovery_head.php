<?php $user=$this->session->userdata("user"); 
$cats=$this->db->query("select name,id from king_board_cats where status=1 order by name asc")->result_array();
?>
<?php if($this->session->userdata("boarder")){
	$boarder=$this->session->userdata("boarder");
?>
		<div style="display:none">
			<div id="disc_add">
				<h4 style="margin-bottom:5px;"><img src="<?=IMAGES_URL?>heart.png" style="margin-bottom:-7px;float:left;">Add a tag</h4>
				<div style="padding:5px;">
				How would you want to tag your product?
				<div style="padding:10px;width:500px;margin-top:10px;">
				<a href="#disc_add_upload" class="fancylink" style="display:block;float:left;margin-left:50px;padding:20px 40px;border:1px solid #aaa;">Upload a pic</a>
				<a href="#disc_add_url" class="fancylink" style="display:block;float:left;margin-right:50px;padding:20px 40px;border:1px solid #aaa;">From a URL</a>
				<div class="clear"></div>
				</div>
				</div>
			</div>
			<div id="disc_add_upload" style="width:400px">
				<h4 style="margin-bottom:5px;">Add tag by uploading photo</h4>
				Please select your product picture <br>
				<form target="uploadpic" action="<?=site_url("discovery/jx_upload")?>" method="post" enctype="multipart/form-data">
				<div style="padding:15px;" align="center">
					<input type="file" name="img" onchange='$(this).parent().parent().submit();$(this).val("");$.fancybox.showActivity();'>
				</div>
				</form>
				<iframe style='display:none' name="uploadpic" id="uploadpic"></iframe>
			</div>
			<div id="disc_add_url">
			<form id="disc_add_url_form">
				<h4 style="margin-bottom:5px;">Add tag through URL</h4>
				Please enter the url : <input type="text" id="disc_url" class="disc_inp" size=50">
				<div align="center" style="padding-top:15px"><input type="submit" value="Next"></div>
			</form>
			</div>
			<a href="#disc_add_final" class="fancylink" id="disc_add_final_trig"></a>
			<div id="disc_add_final" style="width:700px;">
				<h4>Name your tag and where?</h4>
				<div class="img"></div>
				<div class="disc_imglinks" style="display:none">
					<a onclick='imgnext()' href="javascript:void(0)" id="disc_next" style="float:right">next</a>
					<a onclick='imgprev()' href="javascript:void(0)" id="disc_prev" style="display:none">previous</a>
				</div>
				<form action="<?=site_url("discovery/createtag")?>" method="post">
					<table style="margin-top:20px;margin-left:15px;" cellpadding=10>
						<tr>
							<td>Where?</td><td>
								<input type="hidden" name="itempic" id="frm_itempic">
								<input type="hidden" name="itemid" id="frm_itemid">
								<input type="hidden" name="retag" id="frm_retag">
								<input type="hidden" name="from" id="frm_from">
								<input type="hidden" name="url" id="frm_url">
								<input type="hidden" name="img" id="frm_img">
								<input type="hidden" name="pid" value="" id="tag_pid">
								<select id="frm_board" style="padding:5px;font-size:14px;" name="board">
									<?php foreach($this->db->query("select name,bid from king_boards where userid=?",$user['userid'])->result_array() as $b){?><option value="<?=$b['bid']?>"><?=$b['name']?></option><?php }?>
								</select>
							</td>
						</tr>
						<tr>
							<td>Name your tag</td>
							<td><input type="text" class="disc_inp" id="frm_tagname" name="tagname" size=40></td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" value="Create Tag">
						</tr>
					</table>
				</form>
				<div class="clear"></div>
			</div>
			<div id="disc_create">
			<form action="<?=site_url("discovery/createboard")?>" method="post">
				<h4>Create new board</h4>
				<div style="border:1px solid #aaa;padding:5px;">
				<table width="100%" cellpadding=10>
					<tr>
						<td>Board Name</td><td><input type="text" name="bname" class="disc_inp" size=50></td>
					</tr>
					<tr>
						<td>Category</td><td><select name="bcat" style="padding:5px;"><?php foreach($cats as $cat){?><option value="<?=$cat['id']?>" <?=$cat['name']=="Lifestyle"?"selected":""?>><?=$cat['name']?></option><?php }?></select></td>
					</tr>
					<tr>
						<td>Who can tag?</td><td><input checked="checked" type="radio" value="no" name="bpublic"> Me Only &nbsp; &nbsp; &nbsp; <input type="radio" name="bpublic" value="yes">Me + Anyone</td>
					</tr>
					<tr>
						<td></td><td><input type="submit" value="Create Board"></td>
					</tr>
				</table>
				</div>
			</form>
			</div>
		</div>

<script>pid="";function upload_done(s,p){
	if(s!=1)
	{
		alert("Uploaded file was not a valid image");
		return;
	}
	pid=p;
	$("#tag_pid").val(pid);
	$("#frm_retag,#frm_itemid").val("");
	$("#disc_add_final .img").html('<img src="<?=IMAGES_URL?>tags/small/'+pid+'.jpg">');
	$("#frm_url,#frm_img").val("");
	$("#disc_add_final_trig").click();
}
$(function(){
	$("#disc_add_url_form").submit(function(){
		if(!is_required($("#disc_url").val()))
		{
			alert("Please enter a url");
			return false;
		}
		url=$("#disc_url").val();
		$.fancybox.showActivity();
		$.post("<?=site_url("discovery/jx_getimages")?>","url="+url,function(data){
			$.fancybox.hideActivity();
			if(data.length==0)
			{
				alert("No images found. Please check the url.");
				return false;
			}
			if(data[0]=="inv")
			{
				alert("Invalid URL. Please check");
				return false;
			}
			if(data[0]=="bla")
			{
				alert("Sorry! This url is not allowed. Please change url.");
				return false;
			}
			html="";
			images=data;
			for(i=0;i<images.length;i++){
				img=images[i];
				html+='<div align="center" class="urlimg urlimg'+i+'"><img src="'+img+'" style="max-width:200px;"></div>';
			}
			$("#disc_add_final .img").html(html);
			$(".urlimg").hide();$(".urlimg0").show();
			if(images.length>1)
				$(".disc_imglinks").show();
			$("#tag_pid,#frm_itemid").val("");
			$("#frm_url").val(url);
			$("#frm_img").val(images[0]);
			$("#frm_retag").val("no");
			$("#disc_add_final_trig").click();
		},"json");
		return false;
	});
});
var images=[];
var cimg=0,url="";
function imgnext()
{
	cimg++;
	$(".urlimg").hide();
	$(".urlimg"+cimg).show();
	if(cimg+1==images.length)
		$("#disc_next").hide();
	$("#disc_prev").show();
	$("#frm_img").val(images[cimg]);
	$("#frm_retag").val("no");
}
function imgprev()
{
	$("#frm_retag").val("no");
	$("#disc_next").show();
	cimg--;
	$(".urlimg").hide();
	$(".urlimg"+cimg).show();
	if(cimg==0)
		$("#disc_prev").hide();
	$("#frm_img").val(images[cimg]);
}
function retag(pid,bid)
{
	$("#frm_from").val(bid);
	$("#frm_retag").val("yes");
	$("#frm_img,#frm_url").val("");
	$("#tag_pid").val(pid);
	$("#disc_add_final .img").html('<img src="<?=IMAGES_URL?>tags/small/'+pid+'.jpg">');
	$("#disc_add_final_trig").click();
}
function tagitem(url)
{
	$("#frm_img,#frm_from,#frm_retag,#tag_pid").val("");
	$("#disc_add_final .img").html('<img src="<?=IMAGES_URL?>items/'+itempic+'.jpg" width="200">');
	$("#disc_add_final #frm_tagname").val(itemname);
	$("#frm_itemid").val(itemid);
	$("#frm_itempic").val(itempic);
	if(!url)
		$("#frm_url").val(window.location);
	else
		$("#frm_url").val(url);
	$("#disc_add_final_trig").click();
}

function loveit(url)
{
	$.fancybox.showActivity();
	$("#disc_form").html('<input type="hidden" name="url" value="'+url+'">');
	$("#disc_form").attr("action","<?=site_url("discovery/jx_loveit")?>");
	$("#disc_form").submit();
///	$.post("","url="+url,function(){
		//$.fancybox.hideActivity();
	//});
}
function addtagtoboard(bid,name)
{
	$("#frm_board").html('<option value="'+bid+'">'+name+"</option>");
	$("#frm_board").val("bid");
	$("#disc_add_trig").click();
}
function loveitem()
{
	$.fancybox.showActivity();
	$.post("<?=site_url("discovery/jx_love_item")?>",{itemid:itemid},function(resp){
		$.fancybox.hideActivity();
		if(resp=="hmm")
			alert("Your love added to this product. Thanks!");
		else if(resp=="tbf")
			alert("You have already expressed love for this product. Thanks!");
		else
			alert("Err! Something went wrong!! Please try again later");
	});
}
</script>
<?php }elseif($user){?>
<script>
function loveitem()
{
	location="<?=site_url("discovery")?>";
//	alert("Your account is not activated for creating boards and tags. Please visit 'Tag your lifestyle' page to activate your account");
}
function tagitem()
{
	location="<?=site_url("discovery")?>";
//	alert("Your account is not activated for creating boards and tags. Please visit 'Tag your lifestyle' page to activate your account");
}
</script>
<?php }else{?>
<script>
function loveitem()
{
	alert("Please login to love this product");
	make_rem_redir('<?=$this->uri->uri_string()?>');
}
function tagitem()
{
	alert("Please login to tag this product to your board");
	make_rem_redir('<?=$this->uri->uri_string()?>');
}
function retag(pid,bid)
{
	alert("Please login to retag this tag!");
	make_rem_redir('<?=$this->uri->uri_string()?>');
}
function loveit()
{
	alert("Please login to love this tag!");
	make_rem_redir('<?=$this->uri->uri_string()?>');
}
function addtagtoboard()
{
	alert("Please login to add a tag to this board!");
	make_rem_redir('<?=$this->uri->uri_string()?>');
}
</script>
<div style="display:none">
<div id="disc_add">
<h4>Please login to add/share a tag</h4>
<div align="right" style="width:450px;padding:5px;"><a href="<?=site_url("login")?>"><img src="<?=IMAGES_URL?>login.png"></a></div>
</div>
<div id="disc_create">
<h4>Please login to create a board</h4>
<div align="right" style="width:450px;padding:5px;"><a href="<?=site_url("login")?>"><img src="<?=IMAGES_URL?>login.png"></a></div>
</div>
</div>
<?php }?>


<script>
$(function(){
	$("#disc_cat").change(function(){
		$("#disc_form").html('<input type="hidden" name="disc_cat" value="'+$(this).val()+'">');
		$("#disc_form").attr("action","<?=site_url('discovery/change_cat')?>").submit();
	});
});
</script>
<form method="post" id="disc_form"></form>
<?php if($this->uri->segment(1)=="discovery"){?>
<style>.container{width:990px;}</style>
<?php if(0){//!$this->session->userdata("boarder")){?>
	<div class="disc_head_cont">
	<div class="container disc_head">
	</div>
	</div>
<?php }?>
<div class="disc_op_links_cont">
	<div class="container disc_op_links">
		select category : <select id="disc_cat"><option value="0">None</option>
<?php
$scat=0;
if(isset($_COOKIE['disc_cat']))
	$scat=$_COOKIE['disc_cat'];
 foreach($cats as $cat){?>
		<option value="<?=$cat['id']?>" <?=$scat==$cat['id']?"selected":""?>><?=$cat['name']?></option>
<?php }?>
		</select>
		<div class="disc_head_links">
			<a href="<?=site_url("discovery")?>">home</a>
			<a href="#disc_add" id="disc_add_trig" class="fancylink"><img src="<?=IMAGES_URL?>heart.png">Add Tag</a>
			<a href="#disc_create" class="fancylink"><img src="<?=IMAGES_URL?>create_board.png">Create Board</a>
<?php if($this->session->userdata("boarder")){?>
			<a href="<?=site_url("discovery/user/{$boarder['username']}/feed")?>">@<?=$boarder['username']?></a>
<?php }?>
			<a href="<?=site_url("help_tags")?>">help</a>
		</div>
		<div class="clear"></div>
	</div>
</div>



<?php }?>

<script>
$(function(){
$("#item_disc_love").click(function(){
	loveitem();
});
$("#item_disc_tag").click(function(){
	tagitem();
});
});
</script>
