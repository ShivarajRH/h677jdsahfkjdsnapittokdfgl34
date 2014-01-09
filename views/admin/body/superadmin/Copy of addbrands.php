<script>
$(function()
		{
	$('#selcatname').val(0);
	//$('#brandcat').submit();
	$('#addbrand').attr('disabled', true);
	$("#dialogBox").dialog({
		resizable: false,
		autoOpen: false,
		modal: true,
		width:550,
		overlay: {
			backgroundColor: '#000',
			opacity: 0.7
		},
	});
		});
function show_prompt()
{
var name=prompt("Please Enter New Category","");
if (name!=null && name!="")
  {
	newcatname=name;
	var url='<?=site_url('admin/insnewcat')?>';
	//alert(url);
	var data="catname="+newcatname;
	//alert(data);
	$.post(url,data,function(resp){
		if(resp!="0")
		{		
		//alert(resp);
		$('#addcat').hide();
		$("#cat").html(resp);
		//$("#subcat").css("width","200px");
		//$("#subcat").show();
		catbool=true;
		}
		else
			catbool=false;
		});
  }
}
function show_promptforsubcat()
{
var name=prompt("Please Enter New SubCategory","");
catid=$('#selcatname').val();
//alert(name);
if (name!=null && name!="")
  {
	newsubcatname=name;
	var url='<?=site_url('admin/insnewsubcat')?>';
	//alert(url);
	var data="subcatname="+newsubcatname+"&"+"catid="+catid;
	alert(data);
	$.post(url,data,function(resp){
		if(resp!="0")
		{		
			alert(resp);
		$('#addsubcat').hide();
		$("#subcat").html(resp);
		$("#subcat").show();
		$("#addsubcattonewcat").hide();
		catbool=true;
		}
		else
			catbool=false;
		});
  }
}
function addcategory(){
	newcatname=$('#catname').val();
	//alert(newcatname);
	if(newcatname!="")
	{
		var url='<?=site_url('admin/insnewcat')?>';
		//alert(url);
		var data="catname="+newcatname;
		//alert(data);
		$.post(url,data,function(resp){
			if(resp!="0")
			{		
				//alert(resp);
			$('#addcat').hide();
			$("#cat").html(resp);
			//$("#subcat").css("width","200px");
			//$("#subcat").show();
			catbool=true;
			}
			else
				catbool=false;
			});
	}
}
function addsubcategory(){
	newsubcatname=$('#subcatnm').val();
	catid=$('#selcatname').val();
	//alert(newsubcatname);
	if(newsubcatname!="")
	{
       	var url='<?=site_url('admin/insnewsubcat')?>';
    	var data="subcatname="+newsubcatname+"&"+"catid="+catid;
    	//alert(data);
		$.post(url,data,function(resp){
			if(resp!="0")
			{		
				//alert(resp);
			$('#addsubcat').hide();
			$("#subcat").html(resp);
			catbool=true;
			}
			else
				catbool=false;
			});
	}
}
function addsubcat(){
	$("#addsubcat").hide();
	subcatid=$('#subcatid').val();
	//alert(subcatid);
	if(subcatid=='others'){
		//$("#addsubcat").show();
		show_promptforsubcat();
		}
}
function addsubcatfornewcat(){
		//$("#addsubcat").show();
	show_promptforsubcat();
}
function showsubcat(){
	//alert('hi');
	$("#addsubcattonewcat").hide();
	$('#addcat').hide();
	$("#subcat").hide();
	$("#addsubcat").hide();
	catid=$('#selcatname').val();
	//alert(catid);
	if( (catid != 0) && (catid != 'others') )
	{
		$('#addbrand').removeAttr('disabled');
	var url='<?=site_url('admin/getsubcatforsuperadmin')?>';
	//alert(url);
	var data="cat_id="+catid;
	$.post(url,data,function(resp){
		if(resp!="0")
		{		
		$("#subcat").html(resp);
		//$("#subcat").css("width","200px");
		$("#subcat").show();
		subcatbool=true;
		}
		else
		{
			subcatbool=false;
		$("#addsubcattonewcat").show();
		}
		});
	}
	if(catid==0)
	{
		alert('Please select a category');
		$('#addbrand').attr('disabled', true);
	}
	if(catid=="others")
	{
		show_prompt();
/*		$("#subcat").hide();
		$('#addcat').show();
		$('#addbrand').attr('disabled', true);*/
	}
	
}
</script>
<style>
#main{
font: 13px;
font-family:'trebuchet ms';
}
.form{
border:2px solid #D2B48C;
color:#484228;
font-weight:normal;
padding:10px;
width:550px;
background: #EBDECC;
-moz-border-radius:10px;
}
.maindiv{
width: 900px;
margin-top: 40px;
}
.innerdiv{
width: 500px;
clear: both;
margin-left: 50px;
}
.innerdiv label{
font-weight: bold;
font-size: 13px;
}
.span{
width:150px;
float: left;
margin-bottom: 10px;
text-align:justify;
}
.span1{
width:350px;
float: left;
margin-bottom: 10px;
text-align:justify;
}
input{
width:250px;
}
</style>
<?php //print_r($categories);exit;?>
<div class="heading" style="margin-bottom:10px;margin-top: 40px;">
<div class="headingtext container">Add Brand</div>
</div>
<div id="main" class="form" align="left">
<span style="text-align:right;width:200px;"><? if(strlen(validation_errors())>0) echo '<span style="margin-left:5px;color:#f00;float:right">:(</span>'.validation_errors('<span style="font-size:11px;color:#f00;">','</span><br>');?></span>
<form id="brandcat" action="<?=site_url("admin/addbrandstocategory")?>" method="post">
<div>
<div class="innerdiv">
<div class="span"><label>Category Name</label></div>
<div id="cat" class="span1">
<select  name="selcatname" id="selcatname" onchange="showsubcat();">
<option value="0">------Select-------</option>
<?php
if($adm_categories!=FALSE){ 
foreach($adm_categories as $cat){
	?>
	<option value="<?=$cat->id?>"><?=$cat->name?></option>
<?php 	
}
}
?>
<option value="others" style="font-style: italic;">newcategory</option>
</select>
<span id="addsubcattonewcat" style="font-family:arial;font-size:10px;margin-left:15px;color:#2D6A2E;border-bottom:1px solid #2D6A2E;cursor:pointer; display: none;" onclick="addsubcatfornewcat()">Add SubCategory</span>
</div>
</div>
<div id="dialogBox">
</div>
<div id="addcat" style="display: none;margin-left:200px;">
<input type="text" style="width:175px;" name="catname" id="catname">
<input type="button" name="btnaddcat" value="Add category" style="width:125px;font-family:verdana;background:#2D6A2E;padding:3px 5px;color:#efefef;font-weight:bold;font-size:15px;" onclick="addcategory()">
</div>

<div id="subcat" class="innerdiv" style="display: none;">
</div>
<div id="addsubcat" style="display: none;margin-left:200px;">
<input type="text" style="width:175px;" name="subcatnm" id="subcatnm">
<input type="button" name="btnsubaddcat" value="Add" style="width:75px;font-family:verdana;background:#2D6A2E;padding:3px 5px;color:#efefef;font-weight:bold;font-size:15px;" onclick="addsubcategory()">
</div>
<!--<div class="innerdiv">
<div class="span"><label>Sub Category Name</label></div>
<div class="span1"><select type="text" style="width:250px;" name="subcatname" id="subcatname"></div>
</div>
-->
<div class="innerdiv">
<div class="span"><label style="vertical-align:middle;">Brands</label></div>
<div class="span1"><textarea rows="4" style="width:250px;" name="brandname" id="brandname"></textarea></div>
</div>
</div>
<div align="right"><input id="addbrand" type="submit" style="width:110px;font-family:verdana;background:#2D6A2E;padding:3px 5px;color:#efefef;font-weight:bold;font-size:15px;" value="Add Brands"></div>
</form>
</div>