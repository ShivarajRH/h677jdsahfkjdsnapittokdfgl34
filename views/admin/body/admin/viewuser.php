<script>
$(function(){
	$('.showeditadmin').click(function(){	
		i=$(this).attr("rel");
		$('#brandadminname'+i).hide();
	$('#brandadminnameedit'+i).show();});
	$('.submiteditadmin').click(function(){
		a=$(this).attr("alt");
		$('#formadmin_'+a).submit();});
	});
</script>
<style>
table{
width:80%;
}
tr td{
width:auto;
height:20px;
font-family: arial;
font-size: 11px;
}
label{
font-family: arial;
font-size: 11px;
color:black;
}
</style>
<style>
#viewagent table td{
-moz-background-clip:border;
-moz-background-inline-policy:continuous;
-moz-background-origin:padding;
background:#CFCAA8 url(<?php echo base_url()?>images/gradback.png) repeat-x scroll 0 0;
color:#222;
padding:8px;

}

#viewagent table th{
-moz-background-clip:border;
-moz-background-inline-policy:continuous;
-moz-background-origin:padding;
background:#484228 url(<?php echo base_url()?>images/gradhead.png) repeat-x scroll 0 0;
border-bottom:1px solid #FFFFFF;
#border-top:2px solid #D3DDFF;
color:#EDEDED;
font-size:13px;
font-weight:normal;
padding:8px;
font-weight: bold;
font-family:arial;
text-align:left;
}
#viewagent td{
font-weight:bold;
font-size:12px;
font-family:arial;
border-bottom:1px solid #fff;
text-align:left;
}

#viewagent table tr:hover td {
-moz-background-clip:border;
-moz-background-inline-policy:continuous;
-moz-background-origin:padding;
background:#A39D7E url(<?php echo base_url()?>images/gradhover.png) repeat-x scroll 0 0;
font-family:arial;
}

#viewagent table{
width: 600px;

}
#viewagent table td a{
color:#00f;
text-decoration:none;
font-weight:normal;
font-size:11px;
}
#viewagent table td a:hover{
text-decoration:underline;
}
div.nextprev{
color:#00f;
margin-right: 75px;
margin-bottom:0px;
padding: 5px;
font-family: arial;
font-weight:bold;
font-size: 11px;
}
div.nextprev img{
margin-bottom:-4px;
}
</style>
<script>
$(function(){
$(".confirmdel").click(confirmdelete);
});
function confirmdelete()
{
	if(confirm("Are you sure want to delete the User?")==true)
		return true;
	else
		return false;
}
</script>
<div class="heading" style="margin-bottom:20px;">
<div class="headingtext container">View Users</div>
</div>
<div id="viewagent" style="margin-top:0px;margin-left:0px;clear:both;">
<div align="right" class="nextprev">
<?php
//echo $p;exit;
if($p!=1)
{
?>
<span style="cursor:pointer" onclick='window.location="<?=site_url("admin/viewUser")?>/<?=($p-1)?>"'><img src="<?=base_url()?>images/resultset_previous.png">Previous</span>&nbsp;&nbsp;
<?php } 
//print_r($userdetails);
if($userdetails!=FALSE)
{
?>
<span style="cursor:pointer" onclick='window.location="<?=site_url("admin/viewUser")?>/<?=($p+1)?>"'>Next<img src="<?=base_url()?>images/resultset_next.png"></span>
<?php }?>
</div>
<?php
if($userdetails!==FALSE)
{
?>
<table cellspacing="0" cellpadding="0">
<tr>
<th>
User Name
</th>
<th>
Brand
</th>
<th style="text-align:right;padding-right:70px;">
Actions
</th>
</tr>
<?php 
/*$name=ucfirst(strtolower($details->name));
echo $name;exit;*/
$i=0;
foreach($userdetails as $details){
?>
<tr>
<td align="center" valign="center" >
<form id="formadmin_<?=$i?>" action="<?=site_url('admin/editbranduser')?>" method="post">
<input type="hidden" name="userid" value="<?=$details->user_id?>">
<div id="brandadminname<?=$i?>"><?=ucfirst(strtolower($details->branduser));?></div>
<div id="brandadminnameedit<?=$i?>" style="display: none;"><div><input name="branduser" type="text" id="brandadmin" value="<?=ucfirst(strtolower($details->branduser));?>"><img alt="<?=$i?>" class="submiteditadmin" style="margin-left: 3px;" src="<?=base_url().'images/OK.png'?>"><div></div>
</form>
</td>
<td align="center" valign="center" >
<div><?=ucfirst(strtolower($details->brandname));?><div>
</td>
<td align="right" style="text-align:right">
<a class="showeditadmin" rel="<?=$i?>" style="margin-left:5px;" href="#">EditUser</a>
|
<a href="<?=site_url('admin/changepwd/'.$details->user_id)?>">Change Password</a>
|
<a class="confirmdel" style="margin-left:10px;" href="<?=site_url('admin/removeadmin/'.$details->user_id.'/'.$details->usertype)?>">Delete</a>
</td>
</tr>
<?php
$i++;}
?>
</table>
<?php
}
else {
?>
<p style="font-size:13px;color:#222;font-weight:bold;font-family:arial;">No Users Found. Please add!</p>
<?} ?>
</div>
<br style="clear:both;">