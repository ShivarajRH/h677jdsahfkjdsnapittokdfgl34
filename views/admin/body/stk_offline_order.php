<div id="container">
<h2 style="text-align: center">StoreKing Offline Order</h2>
	<div class="stk_offlinecard" >
		<div class="stk_ordr_inpwrap"><b>State : <span class="red_star">*</span></b><select class="fran_det chzn-select" data-placeholder="Choose State" name="sel_state" id="sel_state" style="width: 250px;">
														<option value=""></option>
														<?php $states=$this->db->query("select state_id,state_name from pnh_m_states group by state_id order by state_name asc ")->result_array();
																if($states){foreach($states as $state){?>
														<option value="<?=$state['state_id']?>"><?=$state['state_name']?></option>
														<?php }}?></select>
		</div>
		<div  class="stk_ordr_inpwrap"><b>Territory : </b><select class="fran_det chzn-select" data-placeholder="Choose Territory" name="sel_terr" id="sel_terr" style="width: 250px;"></select></div>
		<div class="stk_ordr_inpwrap"><b>Franchise : <span class="red_star">*</span></b> <select class=" chzn-select" data-placeholder="Choose Franchise "  name="sel_fid" id="sel_fid" style="width:250px;" ></select></div>
		<div class="stk_ordr_inpwrap"><b>Order For : <span class="red_star">*</span></b><select name="mid_entrytype" id="mid_entrytype" style="width:200px;"><option value="0">Registered Member</option><option value="1">Not Registered Member</option></select></div>
		<div class="stk_ordr_inpwrap mid_blk"><b>Member Id : <span class="red_star">*</span></b><input style="font-size:120%" maxlength="8"  type="text" class="mid" name="mid" size=18 ></div>
		<div id="signin" style="display:none;"><input type="button" value="Proceed" onclick='load_franchisebyid()' class="button button-rounded button-action"></div>
	</div>
	<div id="mid_det" title="Member Details"><div id="mem_fran"></div></div>
	<div id="authentiacte_blk" title="Franchisee Authentacation" ><div id="franchise_det"></div></div>
	<div id="franchise_quickview"  title="Franchisee Info"><div id="fran_qvkview"></div></div>
		
			
		
	
</div>

<script>
$("#franchise_quickview").hide();
$(".fran_det").chosen();
$("#sel_fid").chosen();

$("#sel_state").change(function(){
	$("#sel_terr").html('').trigger("liszt:updated");
	var sel_stateid=$("#sel_state").val();
	if(sel_stateid!=0)
	{
		$.getJSON(site_url+'/admin/jx_load_all_territories_bystate/'+sel_stateid,'',function(resp){
			var state_html='';
			if(resp.status=='error')
			{
				alert(resp.message);
			}
			else
			{
				state_html+='<option value=""></option>';
				state_html+='<option value="0">All</option>';
				$.each(resp.terrs_bystate,function(i,b){
					state_html+='<option value="'+b.id+'">'+b.territory_name+'</option>';
				});
			}
			$("#sel_terr").html(state_html).trigger("liszt:updated");
			$("#sel_terr").trigger('change');
			});
	}
});

$(".fran_det").change(function(){
	$("#sel_fid").html('').trigger("liszt:updated");
	var sel_stateid=$("#sel_state").val();
	var sel_terrid=$("#sel_terr").val();
	if(sel_stateid!=0)
	{
		$.post(site_url+'/admin/jx_load_all_franchise_bystate_territory',{stateid:sel_stateid,terrid:sel_terrid},function(resp){
			var state_html='';
			if(resp.status=='error')
			{
				alert(resp.message);
			}
			else
			{
				state_html+='<option value=""></option>';
				state_html+='<option value="0">All</option>';
				$.each(resp.fran_bystateterry,function(i,b){
					state_html+='<option value="'+b.franchise_id+'">'+b.franchise_name+'</option>';
				});
			}
			$("#sel_fid").html(state_html).trigger("liszt:updated");
			$("#sel_fid").trigger('change');
			},'json');
	}
	
});


$('#mid_entrytype').change(function(){
	$('input[name="mid"]').val("");
	if($(this).val()==0)
	{
		$('.mid_blk').show();
		$("#signin").hide();
	}
	else
	{
		$('.mid_blk').hide();
		$("#signin").show();
	}
});

$(".mid").change(function(){
	sel_state=$("#sel_state").val();
	sel_fran=$("#sel_fid").val();
	sel_mtype=$("#mid_entrytype").val();
	if(sel_state=='' || sel_state==0)
		{$('select[name="sel_state"]').addClass('error_inp');return;}
	if(sel_fran=='' || sel_fran==0)
		{$("#sel_fid").addClass('error_inp');return;}
	if($(".mid").val().length!=0)
	{
		$("#mid_det").data('mid',$(this).val()).dialog('open');
	//	$(".stk_offlinecard").hide();
	}
});

$("#mid_det").dialog({
model:true,
width:'400',
height:'300',
autoOpen:false,
open:function()
{
	$('.ui-dialog-buttonpane').find('button:contains("Cancel")').css({"background":"tomato","color":"white"});
	
	dlg=$(this);
	$("#mem_fran").html("");
	$.post("<?=site_url("admin/jx_pnh_getmid")?>",{mid:dlg.data('mid'),more:1},function(data){
		$("#mem_fran").html(data).show();
	});
},
buttons:{
	'Proceed':function(){
		$(this).dialog('close');
		load_franchisebyid();
		},
	'Cancel':function(){
		$(this).dialog('close');
		$(".stk_offlinecard").show();
	},
}
	
});
function select_fran(fid)
{
	fid=$("#sel_fid").val();
	mid=$(".mid").val();
	if($("#mid_entrytype").val() == 1)
		{ mid=0; }
	$("#hd").slideDown("slow");$(this).parent().hide();$("#prod_suggest_list").css({"top":"184px"});
	location="<?=site_url("admin/stk_offline_order_deals") ?>/"+fid+'/'+mid; 
}

function load_franchisebyid()
{
	sel_state=$("#sel_state").val();
	sel_fran=$("#sel_fid").val();
	sel_mtype=$("#mid_entrytype").val();
	if(sel_state=='' || sel_state==0)
		return;
	if(sel_fran=='' || sel_fran==0)
		return;
	if(sel_mtype==0)
	{
		if($(".mid").val().length==0)
			return;
	}
		$("#authentiacte_blk").dialog('open');
		
}

$( "#authentiacte_blk" ).dialog({
	modal:true,
	autoOpen:false,
	width:1000,
	height:670,
	autoResize:true,
	open:function(){
	dlg = $(this);
	$.post("<?=site_url("admin/pnh_jx_loadfranchisebyid")?>",{fid:$("#sel_fid").val()},function(data){
		$("#franchise_det").html(data).show();
		});
		$(".stk_offlinecard").show();
	},
	
});

$("#sel_fid").change(function(){
	
	if($(this).val()>0)
	{
		$("#franchise_quickview").data('fid',$(this).val()).dialog('open');
			}
	else
		$("#franchise_quickview").hide();
});

$("#franchise_quickview").dialog({
	autoOpen:false,
	model:true,
	width:'448',
	height:'auto',
	open:function(){
			$('.ui-dialog-buttonpane').find('button:contains("Proceed")').css({"background":"#4AA02C","color":"white"});
			dlg=$(this);
			$("#fran_qvkview").html("");
			$.post(site_url+'/admin/jx_load_franchise_qvkview',{fid:dlg.data('fid')},function(data){
			$("#fran_qvkview").html(data).show();
		});
	},

		buttons:{
		'Proceed':function(){
				$(this).dialog('close');

			},

			}
	
});

</script>

<style>

.leftcont{display:none;}

.stk_offlinecard
{
	background-color: #F7F7F7;
    border-radius: 2px 2px 2px 2px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
    margin: 0 auto 25px;
    padding: 20px 9px 5px 20px;
    width: 374px;
    height: 317px;
 }
.stk_ordr_inpwrap
{
margin: 20px 0px;
}
.stk_ordr_inpwrap b
{
float: left;
padding: 3px 5px 0px;
text-align: right;
width: 25% !important;
}
.stk_mem_detwrap
{
margin: 20px 0px;
}
.stk_mem_detwrap b {
    float: left;
    font-weight: normal;
    padding: 2px 5px 0;
    text-align: right;
    width: 35%;
}
.stk_mem_detwrap a {
    font-size: 16px;
    font-weight: bold;
}
.stk_mem_detwrap a:HOVER {
    font-size: 16px;
    font-weight: bold;
    text-decoration: none;
}

#signin{
float: right;
}
.error_inp{border:1px solid #cd0000 !important;}
.span_count_wrap {
   /* background: none repeat scroll 0 0 #EAEAEA;*/
    float: left;
    font-size: 11px;
    margin: 10px;
    padding: 5px;
    text-align: center;
 
    }
.level_wrapper
{
	font-size: 10px;
	color:#fff;
	padding:2px 3px;
	border-radius:3px;
	margin-right: 7px;
	width:auto !important;
}
</style>