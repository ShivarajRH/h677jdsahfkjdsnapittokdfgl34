<?php 
	$v=false;
	if(isset($fran))
		$v=$fran;
	
	if(!isset($selected_menu))
		$selected_menu = array();
	else
		$selected_menu = explode(',',$selected_menu);
	
	
	
	
$sec_q=array("What was your childhood nickname?","In what city were you born?","What is the name of the company of your first job?","In what year was your father born?","What was the name of your elementary / primary school?","What is your mother's maiden name?"," What is your oldest sibling's name?"," Who was your childhood hero?")
	
?>

<script>
var map = null;
function init_frmap() {
	if(map != null)
		return false;
	var def_lat = 12.96949928505618;
	var def_lng = 77.59423840625004;

	if($('input[name="lat"]').val())
	{
		def_lat = $('input[name="lat"]').val();
		def_lng = $('input[name="long"]').val();
	}
	
  var mapOptions = {
    zoom: 8,
    center: new google.maps.LatLng(def_lat,def_lng),
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  map = new google.maps.Map(document.getElementById('map_canv'),
      mapOptions);

  var marker = new google.maps.Marker({
      position: new google.maps.LatLng(def_lat,def_lng),
      map: map,
      draggable:true
      
  });

  	google.maps.event.addListener(marker, 'dragend', function(){
  			 
		      var lat = marker.getPosition().jb;
		      var lng = marker.getPosition().kb;
		      	$('input[name="lat"]').val(lat);
		      	$('input[name="long"]').val(lng);
	});

  	$('input[name="lat"]').val(def_lat);
  	$('input[name="long"]').val(def_lng);


  	$('.fran_menu').chosen();
  
}

</script>
<div class="container">
<h2><?=$v?"Edit":"Add new"?> PNH Franchise</h2>

<form method="post" autocomplete="off" id="pnh_af_form" date-validate="parsley">
<div class="tab_view">

<ul>
<li><a href="#v_details">Basic Details</a></li>
<li><a href="#v_login">Login Details</a></li>
<li><a href="#v_pnh_details">PNH Details</a></li>
<li><a href="#v_shop" onclick="init_frmap()">Shop Details</a></li>
<li><a href="#v_contacts">Contacts</a></li>
</ul>

<div id="v_details">
<table width="100%">
<tr><td>Name<span class="red_star">*</span></td><td>:</td><td><input type="text" name="name" class="inp mand" size="40" value="<?=$v?"{$v['franchise_name']}":""?>"></td></tr>
<tr><td>Address<span class="red_star">*</span></td><td>:</td><td><input type="text" name="address" class="inp mand" size="50" value="<?=$v?"{$v['address']}":""?>"></td></tr>
<tr><td>Locality<span class="red_star">*</span></td><td>:</td><td><input type="text" name="locality" size="30" class="inp mand" value="<?=$v?"{$v['locality']}":""?>"></td></tr>
<tr><td>City<span class="red_star">*</span></td><td>:</td><td><input type="text" name="city" class="inp mand" value="<?=$v?"{$v['city']}":""?>"></td></tr>
<tr><td>State<span class="red_star">*</span></td><td>:</td><td><input type="text" name="state" class="inp mand" value="<?=$v?"{$v['state']}":""?>"></td></tr>
<tr><td>Postcode<span class="red_star">*</span></td><td>:</td><td><input type="text" name="postcode" class="inp mand" value="<?=$v?"{$v['postcode']}":""?>"></td></tr>
</table>
</div>
 

<div id="v_login">
<table width="100%">
<tr><td>Login Mobile 1<span class="red_star">*</span></td><td>:</td><td><input type="text" maxlength="10" class="inp mand loginmob1" name="login_mobile1" value="<?=$v?"{$v['login_mobile1']}":""?>"><span id="mob1_error"></span></td></tr>
<tr><td>Login Mobile 2</td><td>:</td><td><input type="text" maxlength="10" class="inp loginmob2" name="login_mobile2" value="<?=$v?"{$v['login_mobile2']}":""?>"><span id="mob2_error"></span></td></tr>
<tr><td>Login Email</td><td>:</td><td><input type="text" class="inp mand login_email" name="login_email" size=30 value="<?=$v?"{$v['email_id']}":""?>"></td></tr>
</table>
</div>


<div id="v_shop">

<div id="map_canv" style="width: 400px;height: 300px;border:2px solid #cdcdcd;float: right;width: 400px;"></div>

<table cellspacing="5">
<tr><td>Shop Name : </td><td><input type="text" class="inp" name="shop_name" size=30 value="<?=$v?"{$v['store_name']}":""?>" data-required="true"></td></tr>
<tr><td>Type of Business</td><td><input type="text" class="inp" name="business_type" size=15 value="<?=$v?"{$v['business_type']}":""?>" data-required="true"></td></tr>
<tr><td>No. of employees : </td><td><input type="text" class="inp" name="shop_emps" size=4 value="<?=$v?"{$v['no_of_employees']}":""?>" data-required="true"></td></tr>
<tr><td>Area : </td><td><input type="text" class="inp" name="shop_area" size=4 value="<?=$v?"{$v['store_area']}":""?>" data-required="true">sqft</td></tr>
<tr><td>Latitude :</td><td><input type="text" class="inp" name="lat" size=15 value="<?=$v?"{$v['lat']}":""?>" data-required="true"></td></tr>
<tr><td>Longitude :</td><td><input type="text" class="inp" name="long" size=15 value="<?=$v?"{$v['long']}":""?>" data-required="true"></td></tr>
<tr>
	<td width="100" colspan="2">
	<div style="padding:7px 0px;">
		<div style="overflow: hidden;width:700px;">
			Configure Menu<span class="red_star">*</span>: 
			<br>
			<ol id="menu_list" style="margin:0px;">
				<?php foreach($fran_menu as $f_menu){
					$prepaid_menu='';
					if($f_menu['menu_id'] && $f_menu['is_active'])
						$prepaid_menu='<b> (Prepaid menu)</b>';
				?>
				<li style="list-style:none;width: 300px;float: left;">
					<input type="checkbox" name="fran_menu[]"  value="<?php echo $f_menu['id']?>" <?=in_array($f_menu['id'],$selected_menu)?"checked":""?> ><?php echo $f_menu['name'] . $prepaid_menu;?>
				</li>
				<?php }?>
			</ol>
		</div>
	</div>	
	</td>
</tr>
<tr><td>Working Time : </td><td>
<select name="shop_from">
<?php for($i=1;$i<=24;$i++){?>
<option value="<?=date("H:i:s",mktime($i,0,0))?>" <?=(($v&&date("H",strtotime($v['store_open_time']))==$i)?'selected':"")?>><?=date("g:ia",mktime($i,0))?></option>
<?php }?>
</select>
to 
<select name="shop_to">
<?php for($i=1;$i<=24;$i++){?>
	<option value="<?=date("H:i:s",mktime($i,0,0))?>" <?=(($v&&date("H",strtotime($v['store_close_time']))==$i)?'selected':"")?>><?=date("g:ia",mktime($i,0))?></option>
<?php }?>
</select>
</td></tr>


<tr><td>Own Property? :</td><td><input type="checkbox" name="own" value="1" <?=((($v&&$v['own_rented'])==1)?"checked":"")?> ></td></tr>

<tr><td>Website :</td><td><input type="text" name="website" size=50 value="<?=$v?$v['website_name']:""?>"></td></tr>
<tr><td>If Internet Available,mention ISP :</td><td><input type="text" name="internet" size=50 value="<?=$v?$v['internet_available']:""?>"></td></tr>
<tr><td>TIN No :</td><td><input type="text" name="shop_tin" class="inp" value="<?=$v?$v['store_tin_no']:""?>" size=30  maxlength="40"></td></tr> 
<tr><td>PAN No :</td><td><input type="text" name="shop_pan" class="inp" value="<?=$v?$v['store_pan_no']:""?>" size=30  maxlength="40"></td></tr> 
<tr><td>Service Tax No :</td><td><input type="text" name="shop_stax" class="inp" value="<?=$v?$v['store_service_tax_no']:""?>" size=30  maxlength="40"></td></tr> 
<tr><td>Registration No :</td><td><input type="text" name="shop_reg" class="inp" value="<?=$v?$v['store_reg_no']:""?>" size=30  maxlength="40"></td></tr> 
</table>

</div>

<div id="v_pnh_details">
<table cellpadding=5>
<tr><td>Is LC Store</td><td>:</td><td><input type="checkbox" name="is_lc_store" value="1" <?=($v&&$v['is_lc_store']==1)?"checked":""?>></td></tr>
<tr><td>Class</td><td>:</td><td>
<select name="class">
<?php foreach($this->db->query("select id,class_name from pnh_m_class_info order by class_name asc")->result_array() as $c){?>
<option value="<?=$c['id']?>" <?=$v&&$v['class_id']==$c['id']?"selected":""?>><?=$c['class_name']?></option>
<?php }?>
</select>
</td></tr>
<tr><td>Territory</td><td>:</td>
<td><select name="territory" id="pnh_terry">
<?php foreach($this->db->query("select * from pnh_m_territory_info order by territory_name asc")->result_array() as $t){?>
<option value="<?=$t['id']?>" <?=$v&&$v['territory_id']==$t['id']?"selected":""?>><?=$t['territory_name']?></option>
<?php }?>
</select>
</td></tr>

<tr><td>Town</td><td>:</td>
<td><div id="pnh_towns">
<?php if($v){
		echo "<select name='town'>";
		echo '<option value="">Choose</option>';
		foreach($this->db->query("select id,town_name from pnh_towns where territory_id=?",$v['territory_id'])->result_array() as $t)
			echo '<option value="'.$t['id'].'">'.$t['town_name'].'</option>';
		echo "</select>";
}?>
</div></td>
</tr>

</table>

<table cellpadding=3 style="background:#eee;margin-top:10px;">
<tr>
<td colspan="100%"><h3 style="margin:3px;">Authentication</h3></td>
</tr>
<tr>
<td>security question<span class="red_star">*</span></td><td>:</td><td><select name="sec_q" class="sec_q">
<?php foreach($sec_q as $i=>$q){?>
<option value="<?=$i?>" <?=$v&&$v['security_question']==$i?"selected":""?>><?=$q?></option>
<?php }?>
<option value="-1"  <?=$v&&$v['security_question']==-1?"selected":""?> style="font-style:italic">custom question</option>
</select>
</td>
</tr>
<tr class="sec_cq1" style="<?=$v&&$v['security_question']==-1?"display:none;":""?>"><td>Custom Question :</td><td><input type="text" class="inp" name="sec_cq" size="40" value="<?=$v&&$v['security_question']==-1?$v['security_custom_question']:""?>"></td></tr>
<tr><td>Answer</td><td>:</td><td><input type="text" class="inp sec_a" name="sec_a" size=40 value="<?=$v?$v['security_answer']:""?>"></td></tr>
</table>

<table cellpadding=3 style="background:#eee;margin-top:10px;">
<tr>
<td colspan="100%"><h3 style="margin:3px;">Authentication</h3></td>
</tr>
<tr>
<td>security question 2</td><td>:</td><td><select name="sec_q2" class="sec_q2">
<?php foreach($sec_q as $i=>$q){?>
<option value="<?=$i?>" <?=$v&&$v['security_question2']==$i?"selected":""?>><?=$q?></option>
<?php }?>
<option value="-1" <?=$v&&$v['security_question2']==-1?"selected":""?> style="font-style:italic">custom question</option>
</select>
</td>
</tr>
<tr class="sec_cq2"><td>Custom Question</td><td>:<td><input type="text" class="inp" name="sec_cq2" size="40" value="<?=$v&&$v['security_question2']==-1?$v['security_custom_question2']:""?>"></td></tr>
<tr><td>Answer</td><td>:</td><td><input type="text" class="inp sec_a" name="sec_a2" size=40 value="<?=$v?$v['security_answer2']:""?>"></td></tr>
</table>

<!--<h3>Device Details <input type="button" id="add_device" value="Add Device"></h3>
<table class="datagrid smallheader device_list">
<thead><tr><th>Device Serial No</th><th>Device Type</th></tr></thead>
<tbody>
<tr id="add_device_cont">
<td>
<input type="text"  name="dev_sno[]"></td>
<td><Select name="dev_type">
<?php foreach($this->db->query("select id,device_name from pnh_m_device_type order by device_name asc")->result_array() as $t){?>
<option value="<?=$t['id']?>"><?=$t['device_name']?></option>
<?php }?>
</Select>
</td>
</tr>
</tbody>
</table>
-->
</div>


<div id="v_contacts">
<input type="button" value="+ new contact" onclick='clone_vcnt()'>
<div id="v_contact_cont">
<?php $contacts=$this->db->query("select * from pnh_m_franchise_contacts_info where franchise_id=?",$v['franchise_id'])->result_array(); if($v){foreach($contacts as $c){?>

<table>
<tr><td>Name : </td><td><input type="text" class="inp" name="cnt_name[]" value="<?=$c['contact_name']?>"></td>
<td>Designation : </td><td><input type="text" class="inp" name="cnt_desgn[]" value="<?=$c['contact_designation']?>"></td>
</tr>
<tr>
<td>Mobile 1 : </td><td><input type="text" class="inp" name="cnt_mob1[]" value="<?=$c['contact_mobile1']?>"></td>
<td>Mobile 2 : </td><td><input type="text" class="inp" name="cnt_mob2[]" value="<?=$c['contact_mobile2']?>"></td>
</tr>
<tr>
<td>Telephone : </td><td><input type="text" class="inp" name="cnt_telephone[]" value="<?=$c['contact_telephone']?>"></td>
<td>FAX : </td><td><input type="text" class="inp" name="cnt_fax[]" value="<?=$c['contact_fax']?>"></td>
</tr>
<tr>
<td>Email 1 : </td><td><input type="text" class="inp" name="cnt_email1[]" value="<?=$c['contact_email1']?>"></td>
<td>Email 2 : </td><td><input type="text" class="inp" name="cnt_email2[]" value="<?=$c['contact_email2']?>"></td>
</tr>
</table>
<?php } }?>
</div>
</div>

</div>
<input type="submit" value="Submit">
</form>

</div>



<div style="display:none">
<table id="cnt_clone">
<tr><td>Name</td><td>:</td><td><input type="text" class="inp" name="cnt_name[]"></td>
<td>Designation</td><td>:</td><td><input type="text" class="inp" name="cnt_desgn[]"></td>
</tr>
<tr>
<td>Mobile 1</td><td>:</td><td><input type="text" class="inp" name="cnt_mob1[]"></td>
<td>Mobile 2</td><td>:</td><td><input type="text" class="inp" name="cnt_mob2[]"></td>
</tr>
<tr>
<td>Telephone </td><td>:</td><td><input type="text" class="inp" name="cnt_telephone[]"></td>
<td>FAX </td><td>:</td><td><input type="text" class="inp" name="cnt_fax[]"></td>
</tr>
<tr>
<td>Email 1 </td><td>:</td><td><input type="text" class="inp" name="cnt_email1[]"></td>
<td>Email 2 </td><td>:</td><td><input type="text" class="inp" name="cnt_email2[]"></td>
</tr>
</table>
</div>



<style>
#cnt_clone{
display:none;
}
#v_contact_cont table{
margin:10px;
border:1px solid #ccc;
padding:5px;
}
#v_searchres{
display:none;
position:absolute;
width:200px;
height:80px;
overflow:auto;
background:#eee;
border:1px solid #aaa;
}
#v_searchres a{
display:block;
padding:5px;
}
#v_searchres a:hover{
background:blue;
color:#fff;
}
</style>
<script>

$(function(){
	$("#add_device").click(function(){
		ht=$("#add_device_cont").html();
	});
	$("#sec_date").datepicker();
});

var p_added=[],b_added=[];
function clone_vcnt()
{
	$("#v_contact_cont").append("<table>"+$("#cnt_clone").html()+"</table>");
}
function addproduct(id,name,mrp,tax)
{
	$("#v_searchres").hide();
	if($.inArray(id,p_added)!=-1)
	{
		alert("Product already added");
		return;
	}
	p_added.push(id);
	template='<tr><td><input type="hidden" name="pproduct[]" value="'+id+'">'+name+'</td><td><input class="inp" type="text" name="pmrp[]" value="'+mrp+'"></td><td><input type="text" class="inp" name="pprice[]"></td><td><input type="text" class="inp" name="ptax[]" value="'+tax+'"></td><td><input type="text" class="inp" name="pminorder[]"></td><td><input type="text" name="ptat" class="inp"></td><td><input type="text" class="inp" name="premarks[]"></td></tr>';
	$("#v_lptable").append(template);
	$("#v_lpsearch").val("");
}
function addbrand(name,id)
{
	$("#v_searchresb").hide();
	if($.inArray(id,b_added)!=-1)
	{
		alert("brand already added");
		return;
	}
	b_added.push(id);
	i=b_added.length;
	template=$("#lb_template tbody").html();
	template=template.replace(/%brandid%/g,id);
	template=template.replace(/%brand%/g,name);
	template=template.replace(/%di%/g,i);
	$("#v_lbtable tbody").append(template);
	$(".lb_date"+i).datepicker();
	$(".lb_date"+i+"t").datepicker();
}
$(function(){

	$("#pnh_af_form").submit(function(){
		f=true;
		var validation_report=new Array();
		var error=0;

		if ($(this).find('input[name="fran_menu[]"]:checked').length == 0) 
		{	
			error=1;
			validation_report.push('Select at least one Menu ');
		    f=false;
		}
	
		if(mobok1!=1 || ($(".loginmob2").val().length!=0 && mobok2!=1))
		{
			error=1;
			validation_report.push('Check login mobile numbers');
		}
		$("input.mand",$(this)).each(function(){
			if($(this).val().length==0)
			{
				error=1;
				validation_report.push($("td:first",$(this).parents("tr").get(0)).text()+" is missing");
				f=false;
			}
		});
		if(f && $(".sec_a").val().length==0)
		{
			error=1;
			validation_report.push("Please enter security answer");
			f=false;
		}
		
		if(f && !is_email($(".login_email",$(this)).val()))
		{
			error=1;
			validation_report.push("Please enter valid login email");
			f=false;
		}
		
		if($('#pnh_towns select').val() == '')
		{
			error=1;
			validation_report.push("Please choose franchise town.");
			f=false;
		}

		if(error)
		{	var html_cnt='';
			$.each(validation_report,function(a,b){
				html_cnt+=(a+1)+" "+b+"\n";	
			});
			alert(html_cnt);
			return false;
		}
		
		

		//if("input [type="checkbox" name="fran_menu[]")
		return f;
	});

		
	$("#pnh_terry").change(function(){
		$("#pnh_towns").html("<option value=''>Loading...</option>");
		$.post("<?=site_url("admin/pnh_jx_loadtown")?>",{tid:$(this).val()},function(data){
			
			$("#pnh_towns").append(data);
			$("#pnh_towns select").prepend("<option value=''>Choose</option>");
			
			$("#pnh_towns select").val("<?=$v['town_id']?>");
		});
	})<?php if(!$v){?>.change()<?php }?>;
	
<?php if($v){?>
	$("#pnh_towns select").val("<?=$v['town_id']?>");
<?php }?>
	
	for(i=0;i<b_added.length;i++)
		$(".lb_date"+i+", .lb_date"+i+"t").datepicker();
		
	if(b_added.length==0)
		clone_vcnt();
	
	$("#v_lpsearch").keyup(function(){
		$.post("<?=site_url("admin/searchproducts")?>",{q:$(this).val()},function(data){
			$("#v_searchres").html(data).show();
		});
	});
	$("#v_lbsearch").keyup(function(){
		$.post("<?=site_url("admin/jx_searchbrands")?>",{q:$(this).val()},function(data){
			$("#v_searchresb").html(data).show();
		});
	}).focus(function(){
		if($("#v_searchresb").html().length!=0)
			$("#v_searchresb").show();
	});

	$(".inst_type").change(function(){
		$(".inst").hide();
		if($(this).val()=="1")
		{
			$(".inst").show().val("");
			$(".inst_no .label").html("Cheque No");
			$(".inst_date .label").html("Cheque Date");
		}
		else if($(this).val()=="2")
		{
			$(".inst").show().val("");
			$(".inst_no .label").html("DD No");
			$(".inst_date .label").html("DD Date");
		}
		else if($(this).val()=="3")
		{
			$(".inst").show().val("");
			$(".inst_date .label").html("Transfer Date");
			$(".inst_no").hide();
		}
	}).val("0").change();
	$(".loginmob1").change(function(){
		mobok1=0;
		if(!is_mobile($(this).val()))
		{
			alert("invalid mobile number");
			return;
		}
		$.post("<?=site_url("admin/jx_checkloginmob")?>",{mob:$(this).val()},function(data){
			if(data=="1")
			{
				$("#mob1_error").html("Ok").css("color","green");
				mobok1=1;
			}
			else
				$("#mob1_error").html("This mobile number is already in the system").css("color","red");
		});
	});
	$(".loginmob2").change(function(){
		mobok2=0;
		if(!is_mobile($(this).val()))
		{
			alert("invalid mobile number");
			return;
		}
		$.post("<?=site_url("admin/jx_checkloginmob")?>",{mob:$(this).val()},function(data){
			if(data=="1")
			{
				$("#mob2_error").html("Ok").css("color","green");
				mobok2=1;
			}
			else
				$("#mob2_error").html("This mobile number is already in the system").css("color","red");
		});
	});
	$(".sec_q").change(function(){
		if($(this).val()=="-1")
			$(".sec_cq1").show();
		else
			$(".sec_cq1").hide();
	}).change();
	$(".sec_q2").change(function(){
		if($(this).val()=="-1")
			$(".sec_cq2").show();
		else
			$(".sec_cq2").hide();
	}).change();
});
<?php if(!$v){?>
var mobok1=0;
var mobok2=0;
<?php }else{?>
var mobok1=1;
var mobok2=1;
<?php }?>

$('.tab_view').tabs();
</script>
<style>
#mob1_error,#mob2_error{
vertical-align:center;
color:red;
}
</style>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<?php
