<div class="container">
<h2>Add new products group</h2>

<form method="post" id="pg_form">

<table class="datagrid noprint">
<tr><td style="vertical-align:middle">Group Name : </td><td><input type="text" class="inp" name="group_name" class="group_name" style="padding:5px;" size=50></td></tr>
<tr><td>Category :</td><Td><select name="catid">
<?php foreach($this->db->query("select * from products_group_category order by name asc")->result_array() as $c){?>
<option value="<?=$c['id']?>"><?=$c['name']?></option>
<?php }?>
</select>
</table>

<div id="products_attr_cont">
<h4 style="margin:30px 0px 0px 0px">Products Attributes</h4>
<table class="datagrid noprint" id="attributes_table">
<thead><tr><th>Attribute Name</th><th width="600" style="vertical-align:middle"><input onclick='addprodattributes()' type="button" value="Add New Attribute Class+" style="padding:0px;font-size:120%;margin:-3px;float:right;">Possible values</th></tr></thead>
<tbody>
</tbody>
</table>
<div style="padding:10px 0px;">
<input type="button" value="Next" onclick='show_addprod()'>
</div>
</div>

<div id="show_after_attr">
<h4 style="margin:30px 0px 0px 0px">Link Products</h4>
<div id="product_details">
Search : <input type="text" size="40" class="inp prod_search_add">
<div id="prods_list" class="srch_result_pop closeonclick"></div>
<div id="link_products_cont">
</div>
</div>
<br><br>
<input type="submit" value="Create Product Group">

</div>

</form>



<div style="display:none;">
<div  id="prods_link_thead_template">
<table class="datagrid">
<thead><tr><th width="300">Product%attr_names%</th><th></th></tr></thead>
<tbody>
</tbody>
</table>
</div>

<table>
<tbody id="prods_attr_template">
<tr>
<td><input type="text" class="attr_names inp" name="attr_name[]" size="30"><a href="javascript:void(0)" onclick='$(this).parent().parent().remove()'>remove</a></td>
<td>
<span class="attr_i" style="display:none;">%i%</span>
<input type="button" value="Add values+" style="float:right;margin:-3px;padding:0px;" onclick='addattrvalue(this)'>
<div class="attrvaluecont">
<input type="text" class="attrvalue inp attrvalue%i%" name="attr_values[%i%][]">
</div>
</td>
</tr>
</tbody>
</table>

<table>
<tbody id="prods_inv_prod">
<tR>
<td><input type="hidden" class="p_inv_pids" name="pids[]" value="%pid%">%pname% %attr_data%</td>
<td><a href="javascript:void(0)" onclick='remove_prow(this,"%pid%")'>remove</a></td>
</tR>
</tbody>
</table>

</div>

</div>

<script>
var attr_i=0;

var attr_names=[];
var attr_values=[];

var added_pids=[];

function remove_prow(o,pid)
{
	obj=$(o).parent().parent();
	obj.remove();
	t_pid=added_pids;
	added_pids=[];
	for(i=0;i<t_pid.length;i++)
	{
		if(pid!=t_pid[i])
			added_pids.push(t_pid[i]);
	}
}

function show_addprod()
{
	attr_names=[];
	attr_values=[];
	if(!confirm("Product attributes will be locked. Are you sure want to Continue?"))
		return;
	$("#attributes_table .attr_names").each(function(){
		v=$(this).val();
		attr_names.push(v);
	});
	if(attr_names.length==0)
	{
		alert("No attributes defined!");return;
	}
	for(i=0;i<attr_names.length;i++)
	{
		if(attr_names[i].length==0)
		{
			alert("Attribute name cannot be empty");return false;
		}
		attr_values[i]=[];
		$(".attrvalue"+i).each(function(){
			v=$(this).val();
			if(v.length!=0)
				attr_values[i].push(v);
		});
		if(attr_values[i].length==0)
		{
			alert(attr_names[i]+" is not having any values defined!");
			return;
		}
	}
	th="</th>";
	for(i=0;i<attr_names.length;i++)
	{
		th=th+"<th>"+attr_names[i]+"</th>";
	}
	template=$("#prods_link_thead_template").html();
	template=template.replace(/%attr_names%/,th);
	$("#link_products_cont").html(template);
	$("#products_attr_cont").hide();
	$("#show_after_attr").show();
}

function addattrvalue(o)
{
	obj=$(o).parent();
	a_i=$(".attr_i",obj).text();
	name=$(".attrvalue",obj).attr("name");
	$(".attrvaluecont",obj).append('<input type="text" class="attrvalue inp attrvalue'+a_i+'" name="'+name+'">');
}

function addproduct(id,name,mrp)
{
	$("#prods_list").hide();
	if($.inArray(id,added_pids)!=-1)
	{
		alert("Product already added");
		return;
	}
	added_pids.push(id);
	temp=$("#prods_inv_prod").html();
	temp=temp.replace(/%pid%/g,id);
	temp=temp.replace(/%pname%/g,name);
	attr_data="</td>";
	for(i=0;i<attr_values.length;i++)
	{
		attr_data=attr_data+"<td><select name='attr_"+id+"[]'>";
		for(i2=0;i2<attr_values[i].length;i2++)
			attr_data=attr_data+'<option value="'+i2+'">'+attr_values[i][i2]+'</option>';
		attr_data=attr_data+"</select></td>";
	}	
	temp=temp.replace(/%attr_data%/g,attr_data);
	$("#link_products_cont table tbody").append(temp);
}

function addprodattributes()
{
	template=$("#prods_attr_template").html();
	template=template.replace(/%i%/g,attr_i);

	$("#attributes_table tbody").append(template);
	attr_i=attr_i+1;
}

$(function(){
	
	$(".prod_search_add").keyup(function(){
		$.post('<?=site_url("admin/jx_searchproducts")?>',{q:$(this).val()},function(data){
			$("#prods_list").html(data).show();
		});
	}).attr("disabled",false);


	$("#pg_form").submit(function(){
		if(added_pids.length==0)
		{
			alert("No products were linked");
			return false;
		}
		if($(".group_name").val().length==0)
		{
			alert("Name the product group");
			return false;
		}
		return true;
	});
	
});

</script>
<style>
#show_after_attr{
display:none;
}
</style>
<?php
