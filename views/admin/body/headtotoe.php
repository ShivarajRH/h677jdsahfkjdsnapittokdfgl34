<div class="container" style="padding-bottom:20px;">

<div style="padding:10px;float:right;">
Search : <input type="text" class="headtotoe_srch"> <input type="button" class="ht_srch_trig" value="Go">
</div>

<div id="srchcont">

</div>

<h2>Head to toe</h2>

<form id="headtotoeform" method="post">
Sex: 
<select name="bodyparts">
<option value="1">Male</option>
<option value="2">Female</option>
</select>

<input type="hidden" name="ids" value="">
</form>

<div id="target">
</div>


<div class="clear"></div>

<div style="background:#eee;padding:10px;float:left;width:600px;margin-top:20px;">
<div>Min Total : Rs<span id="mintotal"></span></div>
<div>Max Total : Rs<span id="maxtotal"></span></div>

<div>
<input type="button" value="Submit" onclick='submithtform()'>
</div>
</div>

</div>

<script>
var iids=new Array();
var catids=new Array();
var idata=new Array();

function submithtform()
{
	$("input[name=ids]").val(iids.join(","));
	$("#headtotoeform").submit();
}

$(function(){
	$(".ht_srch_trig").click(function(){
	$.post('<?=site_url("admin/jxsrchforht")?>',"q="+$(".headtotoe_srch").val(),function(data){
		$("#srchcont").html(data);
		});
	});
});
function addcont(itemid,name,catid,catname,price)
{
	if($.inArray(itemid,iids)!=-1)
	{
		alert("already added");return;
	}
	iids.push(itemid);
	var i=new Array(itemid,name,catid,catname,price);
	idata.push(i);
	if($.inArray(catid,catids)==-1)
	{
		catids.push(catid);
		$("#target").append('<div class="cat" id="cat'+catid+'"><h3>'+catname+'</h3><div class="cont"></div><div style="clear:both;"></div></div>');
	}
	$("#cat"+catid+" .cont").append('<div class="item" id="item'+itemid+'"><h4>'+name+'<br>Rs '+price+'</h4><a href="javascript:void(0)" onclick="removeprod('+itemid+')">remove</a></div>');
	calc_total();
}
function removeprod(item)
{
	$("#item"+item).remove();
	var n_iids=iids;
	iids=[];
	$.each(n_iids,function(i,id){
		if(item!=id)
			iids.push(id);
	});
	var n_idata=idata;
	idata=[];
	$.each(n_idata,function(i,ida){
		if(ida[0]!=item)
			idata.push(ida);
	});
	n_catids=catids;
	catids=[];
	$.each(n_catids,function(i,catid){
		c=0;
		$.each(idata,function(i,ida){
			if(ida[2]==catid)
				c++;
		});
		if(c>0)
			catids.push(catid);
		else
			$("#cat"+catid).remove();
	});
	calc_total();
}
function calc_total()
{
	var mntotal=0;
	var mxtotal=0;
	$.each(catids,function(i,catid){
		var ps=new Array();
		$.each(idata,function(i,item){
			if(item[2]==catid)
				ps.push(parseInt(item[4]));
		});
		min=max=ps[0];
		$.each(ps,function(i,p){
			if(p<min)
				min=p;
			if(p>max)
				max=p;
		});
		mxtotal=mxtotal+max;
		mntotal=mntotal+min;
	});
	$("#mintotal").html(mntotal);
	$("#maxtotal").html(mxtotal);
}
</script>

<style>
#srchcont{
float:right;clear:right;width:200px;height:500px;overflow:auto;
background:#f1f1fa;
padding:5px;
border:1px solid #ccc;
}
#srchcont a{
display:block;
padding:3px;
}
#srchcont a:hover{
background:#ddd;
}
#target .cat{
width:800px;
float:left;
clear:left;
margin:10px 0px;
border-bottom:1px solid #aaa;
}
#target .item{
border:1px solid #aaa;
margin:5px;
padding:5px;
float:left;
}
</style>

<?php
