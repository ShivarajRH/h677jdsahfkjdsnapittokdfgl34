<script>
$(function(){
	 $('#uploadcatpic').attr('disabled', true);
	$("#dialogBox").dialog({
		resizable: false,
		autoOpen: false,
		modal: true,
		height:385,
		width:350,
		overlay: {
			backgroundColor: '#000',
			opacity: 0
		},
		buttons: {
			Cancel: function() {
				$(this).dialog('close');
			},
			'Add': function() {
				$(this).dialog("close");
				$('#addnewcatform').submit();
//				alert("submit");
//				addcategory();
			}
		}
	});
	$("#dialogBox1").dialog({
		resizable: false,
		autoOpen: false,
		modal: true,
		height:275,
		width:350,
		overlay: {
			backgroundColor: '#000',
			opacity: 0
		},
		buttons: {
			Cancel: function() {
				$(this).dialog('close');
			},
			'Add': function() {
				$(this).dialog("close");
				addbrand();
			}
		}
	});
	/*$('#addnewbrand').click(function(){
		$("#dialogBox1").dialog("option","title","<b style='color:#fff;'>Add New Brand</b>");
		$('#dialogBox1').dialog('open');	
	});*/
	$('#addnewcat').click(function(){
		$("#dialogBox").dialog("option","title","<b style='color:#fff;'>Add New Category</b>");
		$('#dialogBox').dialog('open');	
	});	
	$('.deletebrandundercat').click(confirmdeletebrandundercat);
})
function confirmdeletebrandundercat()
{
	if(confirm("Are you sure want to delete this Brand Under this Category?")==true)
		return true;
	else
		return false;
}
function disable()
{
	if ($('#toggleElement').is(':checked')) {
	    $('#uploadcatpic').removeAttr('disabled');
	} else {
		 $('#uploadcatpic').attr('disabled', true);
	}  
}
function addcategory(){
	newcatname=$('#dialogcatname').val();
	newcatdesc=$('#dialogcatdesc').val();
	//newcatpic=$('#catpic').val();
	//alert(newcatpic);
	if(newcatname!="")
	{
		var url='<?=site_url('admin/insnewcategory')?>';
		var data="catname="+newcatname+"&"+"catdesc="+newcatdesc;
		alert(data);
		$.post(url,data,function(resp){
			if(resp!="0")
			{		
				alert(resp);
			$("#newcat").html(resp);
			catbool=true;
			}
			else
				catbool=false;
			});
	}
}
function loadcategories(str)
{
	$("#newcat").html(str);
}
function getpicpath(path)
{
	alert(path);
	$('#hidpicpath').val(path);
}
function addbrand(){
	newbrandname=$('#dialogbrandname').val();
	newbranddesc=$('#dialogbranddesc').val();
	if(newbrandname!="")
	{
		var url='<?=site_url('admin/insnewbrand')?>';
		var data="brandname="+newbrandname+"&"+"branddesc="+newbranddesc;
		alert(data);
		$.post(url,data,function(resp){
			if(resp!="0")
			{		
//				alert(resp);
			$("#overritebrands").html(resp);
			brandbool=true;
			}
			else
				brandbool=false;
			});
	}
}
function addbrandsundercat()
{
	catid=$('#hidcatid').val();
	catname=$('#catname').val();
	brandid=$('#brandname').val();
	if(catid!="" && brandid!=0)
	{
		var url='<?=site_url('admin/insbrandundercat')?>';
		var data="catid="+catid+"&"+"brandid="+brandid+"&"+"catname="+catname;
//		alert(data);
		$.post(url,data,function(resp){
			if(resp!="0")
			{
			$("#overridebrandsundercat").html(resp);			
			$("#overridebrandsundercat").show();
			brandbool=true;
			}
			else
				brandbool=false;
			$('.deletebrandundercat').click(confirmdeletebrandundercat);
			});
	}
}
</script>
<style>
.ui-datepicker {
	font-size: 10px;
}

#main {
	font-family: arial;
	font-size: 11px;
	color: #000000;
}

.maindiv {
	width: 900px;
	margin-top: 40px;
	margin-bottom: 10px;
}

.innerdiv {
	font-family:arial;
	clear: both;
}

.innerdiv label {
	font-weight: bold;
	font-size: 13px;
}

.span {
	float: left;
	margin-bottom: 10px;
	text-align: justify;
}

.span1 {
	float: left;
	margin-bottom: 10px;
	text-align: justify;
}

.form input {
	border: 1px solid #9BCD9B; 
	width: 300px;
	margin-left:20px;
}

textarea {
	-moz-border-radius: 5px;
	border: 1px solid #9BCD9B; 
	background: : #EBF8DC;
}

.form {
	background:#fff url(<?=base_url ()?>/images/bg.gif )
		repeat-x scroll left top;
	border: #CCCCCC 1px solid;
	-moz-border-radius: 10px;
	float: left;
	padding: 10px;
	font-family:arial;
}

.sidediv {
	float: left;
}
.dialoglabel{
font-size: 12px;
font-family: arial;
}
.ui-dialog .ui-dialog-titlebar {
padding:-1.5em 0.3em 0.3em 1em;
position:relative;
}
.ui-dialog-titlebar{
font-size:13px;
color:#fff;
}
.form .header{
font-size:18px;
padding-bottom:10px;
}
.ui-dialog .ui-dialog-buttonpane button{
font-weight:bold;
font-size:12px;
}
</style>
<div class="heading" style="margin-bottom: 20px;">
<div class="headingtext container">Categories & Brands</div>
</div>
<div class="container">
<div class="form" style="width: 20%;">
<?php 
if(!isset($specificcatdetails) && !isset($brandsundercat))
{
	?>
<?php if(isset($info)){?>
<div style="font-family:arial;font-size:13px;color:#fa0;"><?=$info?></div>
<?php } if(isset($error)){?>
<div style="font-family:arial;font-size:13px;color:#f00;"><?=$error?></div>
<?php }?>
<?php }?>
<div class="header">Available Categories</div>
<div id="newcat">
	<?php
//	print_r($categories);
	if ($categories != FALSE) {
		foreach ( $categories as $cat ) {
			?>
	<div style="padding:2px;"><label>
	<a href="<?=site_url ( 'admin/categories/' . $cat->id )?>"
	style="margin-left: 5px; font-size: 13px;" onclick="showdiv();"><?=$cat->name?></a></label>
</div>
	<?php
		}
	}
	?>
	</div>
<br>
<div align="right"><span id="addnewcat"
	style="font-family: arial; font-size: 11px; color: #2D6A2E; cursor: pointer; border-bottom: 1px solid #2D6A2E;">Add
New Category</span></div>
</div>
<?php 
if(!isset($specificcatdetails) && !isset($brandsundercat))
{
	?>
<div class="form" style="margin-left:10px;width:350px;">
<div class="header" style="font-size:15px;padding:30px;">Please select a Category to edit and assign brands</div>
</div>
	<?php 
}
if(isset($specificcatdetails))
{
?>
<div class="form" id="catdiv" style="width:350px;margin-left: 10px; <?php if(!isset($specificcatdetails)) echo 'display:none;'?>">
<form action="<?=site_url('admin/updatecategory')?>" method="post">
<?php
if ($specificcatdetails != FALSE) {
	?>
<input name="catid" type="hidden" value="<?=$specificcatdetails->id?>">
<div class="innerdiv">
<?php if(isset($info)){?>
<div style="font-family:arial;font-size:13px;color:#fa0;"><?=$info?></div>
<?php } if(isset($error)){?>
<div style="font-family:arial;font-size:13px;color:#f00;"><?=$error?></div>
<?php }?>
<div class="header">Edit Category - <?=$specificcatdetails->name?></div>
<div class="span"><label>Category Name</label></div>
<br>
<div class="span1"><input style="width: 300px;" name="catname"
	id="catname" value="<?=$specificcatdetails->name?>"></div>
</div>
<div class="innerdiv">
<div class="span"><label>Description</label></div>
<br>
<div class="span1" style="margin-left: -60px;"><textarea style="width: 300px;" name="description"
	id="description"><?=$specificcatdetails->description?></textarea></div>
</div>
<div class="innerdiv">
<div class="span"><label>Main category</label></div>
<br>
<div class="span1" style="margin-left: -60px;">
<?php if(isset($canbemain)){?>
<select name="mainc">
<option value="0">none</option>
<?php foreach($maincats as $cat){?>
<option value="<?=$cat['id']?>" <?php if($cat['id']==$specificcatdetails->type) echo "selected";?>><?=$cat['name']?></option>
<?php }?>
</select>
<?php }else {?>
<input type="hidden" value="0" name="mainc">
<?php }?>
</div>
</div>
<div class="innerdiv">
<!--<div class="span"><label>Category Pic</label>-->
<!--<div style="margin-top: 3px;">-->
<input type="hidden" name="hidcatimg" id="hidcatimg" value="<?=$specificcatdetails->catimage ?>">
<!--<img style="width: 75px;height: 75px;" src="<?=base_url().'images/catlogo/'.$specificcatdetails->catimage.'.jpg'?>">-->
<!--</div>-->
<!--</div>-->
<!--<div class="span1"><div><label style="font-weight: normal;"><input id="toggleElement" onclick="disable()" style="width:auto;" type="checkbox" name="check"> Check here to Update image</label></div><input id="uploadcatpic" type="file" name="catpic"></div>-->

</div>	
		<?php
}
?>
		<div class="innerdiv">
<div><input style="width: auto; float: right;" name="btnsubmit"
	type="submit" value="Update"></div>
</div>
</form>
</div>
<?php }
if(isset($brandsundercat)){
?>
<div class="form" id="branddiv"
	style="margin-left: 10px; <?php if(!isset($brandsundercat)) echo 'display:none;'?>width: 30%;">
<div id="overridebrandsundercat">
<div class="header">Brands Under <?php if($specificcatdetails != FALSE) echo $specificcatdetails->name;?></div>
	
	<?php
	//print_r($brandsundercat);exit;
	if ($brandsundercat != FALSE) { ?>
		<?php foreach ( $brandsundercat as $brand ) {
			?>
	<div><label><a href="<?=site_url('admin/dealsforbrand/'.$brand->brandid)?>"
	style="margin-left: 5px; color: #000; font-size: 13px;"><?=$brand->brandname?></a>
	<a class="deletebrandundercat" href="<?=site_url('admin/deletebrandfromcat/'.$brand->brandid."/{$specificcatdetails->id}")?>" style="margin-left: 5px; color: blue; font-size: 11px;">Remove</a></label>
</div>
	<?php
		}
	} else {
		?>
		<div align="center" id="branderr"><span
	style="text-align: center; font-size: 11px; font-family: arial;color: red;">No
Brands Under this category please add!!!</span></div>
	<?php
	}
	?>
	</div><br>
	<?php
	//print_r($brands);exit;
if ($brands != FALSE) {
	?>
<div class="innerdiv">
<div style="padding-bottom:5px;">Add brand for  <?php if($specificcatdetails != FALSE) echo $specificcatdetails->name;?> category</div>
<input type="hidden" name="hidcatid" id="hidcatid" value="<?=$specificcatdetails->id?>">
<div class="span" id="overritebrands">
<label>Choose brand :</label>
<select style="margin-left: 15px;" name="brandname" id="brandname">
<option value="0" selected="selected">---Select---</option>
<?php foreach($brands as $brand)
{
?>
<option value="<?=$brand->id?>"><?=$brand->name?></option>
<?php
}?>
</select>
</div>
<div align="right"><input style="width: auto;" type="button" value="Add Brand" onclick="addbrandsundercat();"></div>
</div>
		<?php
}
?>
	<br>
<div align="right"><a href="<?=site_url('admin/addbrand')?>" id="addnewbrand"
	style="font-family: arial; font-size: 11px; color: #2D6A2E;">Create
new brand</a></div>
</div>
<?php }?>
		<div id="dialogBox">
		<form id="addnewcatform" action="<?=site_url('admin/insnewcategory')?>" method="post" enctype="multipart/form-data">
			<div id="addcat">
			<div>
			<label class="dialoglabel"> Category Name</label>
			<input type="text" style="width:270px;margin-top: 10px;" name="catname" id="dialogcatname">
			</div>
			<div style="padding-top:20px;">
			<label class="dialoglabel">Description</label><br>
			<textarea rows="3" cols="" style="margin-left:30px;width:270px;margin-top: 10px;" name="catdesc" id="dialogcatdesc"></textarea>
			</div>
			<div style="padding-top:10px;">
			<label class="dialoglabel">Main Category : </label><br>
			<select name="mainc" style="font-size:13px;">
			<option value="0">none</option>
			<?php foreach($maincats as $cat){?>
			<option value="<?=$cat['id']?>"><?=$cat['name']?></option>
			<?php }?>
			</select>
			</div>
<!--			<div>-->
<!--			<label class="dialoglabel">Category Image</label><br>-->
<!--			<input type="file" name="catpic" id="catpic">-->
<!--			</div>-->
			</div>
			</form>
		</div>
				<div id="dialogBox1">
			<div id="addbrand">
			<div>
			<label class="dialoglabel"> Please Enter New Brand</label>
			<input type="text" style="width:270px;margin-top: 10px;" name="dialogbrandname" id="dialogbrandname">
			</div>
			<div>
			<label class="dialoglabel">Description</label><br>
			<textarea rows="3" cols="" style="width:270px;margin-top: 10px;" name="dialogbranddesc" id="dialogbranddesc"></textarea>
			</div>
			</div>
		</div>
		<iframe name="icatpic" id="icatpic" style="display:none;"></iframe>
</div>