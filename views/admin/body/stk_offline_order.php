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
		<div class="stk_ordr_inpwrap"><b>Franchise<span class="red_star">*</span> : </b> <select class=" chzn-select" data-placeholder="Choose Franchise "  name="sel_fid" id="sel_fid" style="width:250px;" ></select></div>
		<div class="stk_ordr_inpwrap"><b>Order For<span class="red_star">*</span> : </b><select name="mid_entrytype" id="mid_entrytype" style="width:200px;"><option value="0">Registered Member</option><option value="1">Not Registered Member</option><option value="2">Key Member</option></select></div>
		<div class="mid_blk" style="background-color:#FAFAFA;padding-left: 100px;margin: 20px 0px;"><b>Member Id</b>  <input type="radio" value="1" name="mtype" checked="checked"> <b>Member Mobno</b><input type="radio" value="2" name="mtype"></div>
		<div class="stk_ordr_inpwrap mid_blk m_blk"><b> Id<span class="red_star">*</span>	 : </b><input style="font-size:120%" maxlength="8"  type="text" class="mid" name="mid" size=18 ></div>
		<div class="stk_ordr_inpwrap mmob_blk"><b> Mobno : <span class="red_star">*</span></b><input style="font-size:120%" maxlength="10"  type="text" class="mid" name="mid" size=18 ></div>
		<div class="reg_blk" style="float:right;font-weight: bold;"><a href="javascript:void(0)" onclick="mem_reg()">Register</a></div>
		<div id="signin" style="display:none;"><input type="button" value="Proceed" onclick='load_franchise_cart_page()' class="button button-rounded button-action"></div>
		</div>
	<div id="mid_det" title="Member Details"><div id="mem_fran"></div></div>
	<div id="authentiacte_blk" title="Franchisee Authentication" ><div id="franchise_det"></div></div>
	<div id="franchise_quickview"  title="Franchisee Info"><div id="fran_qvkview"></div></div>
	 <div id="reg_mem_dlg" title="Instant Member Registration" style="display:none;">
		<form id="reg_mem_frm" action="<?php echo site_url('admin/jx_reg_newmem')?>" method="post">
			<input type="hidden" name="franchise_id" value="" id="memreg_fid">
			<table cellpadding="10" cellspacing="0" border="0" style="border-collapse: collapse">
				<tr>
					<td style="text-align: right;"><b>Member Name</b><span class="red_star">*</span> : </td>
					<td><input type="text" name="memreg_name" id="memreg_name" ></td>
				</tr>
				<tr>
					<td style="text-align: right;"><b>Mobile Number</	b><span class="red_star">*</span> : </td>
					<td><input type="text" name="memreg_mobno" id="memreg_mobno" data-required="true" maxlength="10"></td>
				</tr>
				<tr>
					<td style="text-align: right;"><b>DOB</b> : </td>
					<td><input type="text" name="mem_dob" value="" id="dob" placeholder="YYYY-MM-DD"></td>
				</tr>
				<tr>
					<td style="text-align: right;"><b>Gender</b><span class="red_star">*</span> : </td>
					<td><input  type="radio" name="gender" value="0">Male <input type="radio" name="gender" value="1">Female  <?php echo form_error('gender','<span class="error_msg">','</span>');?></td>
				</tr>
				<tr>
					<td style="text-align: right;"><b>Marital Status</b><span class="red_star">*</span> : </td>
					<td><input type="radio" value="1" name="marital" checked="checked">
						Married
						<input type="radio" checked="checked" value="0" name="marital">
						Single
						<input type="radio" value="2" name="marital">Other</td>
						
				</tr>
				<tr>
					<td style="text-align: right;"><b>Address</b> : </td>
					<td><textarea name="mem_address" value=""></textarea></td>
				</tr>
				<tr>
					<td style="text-align: right;"><b>PinCode</b> : </td>
					<td><input type="text" name="pin_code" value="" size="18px"></td>
				</tr>
			</table>
			<table>
				
			</table>
		</form>
	</div>
</div>

<script>
$('.reg_blk').hide();
$('.mmob_blk').hide();
$(".fran_det").chosen();
$("#sel_fid").chosen();
$('#dob').datepicker();
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
		$('.reg_blk').hide();
		$('#signin').hide();
		
	}
	else if($(this).val()==1)
	{
		$('.reg_blk').show();
		$('.mid_blk').hide();
		$('.mmob_blk').hide();
		$('#signin').hide();
		
	}else
	{
		$('.mid_blk').hide();
		$('.mmob_blk').hide();
		$('.reg_blk').hide();
		$('#signin').show();
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
	if($(this).val().length!=0)
	{
		$('.mid').val($(this).val());
		$("#mid_det").data('mid',$(this).val()).dialog('open');
	}
});


$("#mid_det").dialog({
model:true,
width:'400',
height:'300',
autoOpen:false,
open:function()
{
	$('.ui-dialog-buttonpane').find('button:contains("Cancel")').css({"background":"tomato","color":"white","float":"right"});
	 $('.ui-dialog-buttonpane').find('button:contains("Proceed")').css({"float":"right"});
	dlg=$(this);
	$("#mem_fran").html("");
	 $.post(site_url+'/admin/jx_check_forvalid_mid',{mid:dlg.data('mid')},function(resp){
         if(resp.status == 'success')
         {
             $('.mid').val(resp.mem_det.pnh_member_id);
        	 $.post("<?=site_url("admin/jx_pnh_getmid")?>",{mid:dlg.data('mid'),more:1},function(data){
        			$("#mem_fran").html(data).show();
        		});
        	 $('.ui-dialog-buttonpane').find('button:contains("Proceed")').css({"display":"block"});
         }else
         {
			alert("Invalid Member Details!!!");
			$('.ui-dialog-buttonpane').find('button:contains("Proceed")').css({"display":"none"});
			return false;
          }
	 },'json');
	
},
buttons:{
	'Proceed':function(){
		$(this).dialog('close');
		fid=$("#sel_fid").val();
		mid=$(".mid").val();
		$("#hd").slideDown("slow");$(this).parent().hide();$("#prod_suggest_list").css({"top":"184px"});
		location="<?=site_url("admin/stk_offline_order_deals") ?>/"+fid+'/'+mid; 
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
	$( "#authentiacte_blk" ).dialog('close');
	
}

function load_franchisebyid()
{
	sel_state=$("#sel_state").val();
	sel_fran=$("#sel_fid").val();
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
		load_franchisebyid();
	else
		return;
});

/*$("#franchise_quickview").dialog({
	autoOpen:false,
	model:true,
	width:'448',
	height:'auto',
	open:function(){
			$('.ui-dialog-buttonpane .ui-dialog-buttonset').css({"display":"block","float":"none"});
			$('.ui-dialog-buttonpane').find('button:contains("Proceed")').css({"background":"#4AA02C","color":"white","float":"right"});
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
	
});*/

function load_franchise_cart_page()
{
	fid=$("#sel_fid").val();
	mid=$(".membrid").val();
	if($("#mid_entrytype").val() ==2)
	{ mid=0; }
	$("#hd").slideDown("slow");$(this).parent().hide();$("#prod_suggest_list").css({"top":"184px"});
	location="<?=site_url("admin/stk_offline_order_deals") ?>/"+fid+'/'+mid;
}

$("input[type='radio']").change(function(){
	if($(this).val()==2)
	{
		$('.mmob_blk').show();
		$('.m_blk').hide();
	}
	else
	{
		$('.mmob_blk').hide();
		$('.m_blk').show();
	}
		
});

function mem_reg()
{
	var fid=$("#sel_fid").val();
	if(fid<=0)
	{
		alert("Please select Franchise");
		return false;
	}else
		$('#reg_mem_dlg').data('fid',fid).dialog('open');
}
$('#reg_mem_dlg').dialog({
			autoOpen:false,
			width:600,
			modal:true,
			height:'auto',
			open:function(){
				$('.ui-dialog-buttonpane .ui-dialog-buttonset').css({"display":"block","float":"none"});
				$('.ui-dialog-buttonpane').find('button:contains("Register")').css({"float":"right","background":"#4AA02C","color":"white"});
				$('.ui-dialog-buttonpane').find('button:contains("Cancel")').css({"float":"right","background":"tomato","color":"white"});
				var dlg=$(this);
				var fid=$("#sel_fid").val();
					$('#reg_mem_frm input[name="franchise_id"]',this).val(fid);
				},
				buttons:{
					'Cancel':function(){
						$(this).dialog('close');
					},
					'Register':function(){
						$(this)
						var error_list = new Array();
						// register member 
						var mem_regname = $.trim($('input[name="memreg_name"]').val());
						var mem_mobno = parseInt($.trim($('input[name="memreg_mobno"]').val()));
						var mem_dob = $.trim($('input[name="mem_dob"]').val());
						var gender = $.trim($('input[name="gender"]').val());
						var marital = $.trim($('input[name="marital"]').val());
					
                    		if(mem_regname.length == 0 || mem_mobno.length == 0 || mem_mobno =='' || isNaN(mem_mobno) || gender == '' || marital == '')
                    		{
                             	error_list.push("Please Enter Valid Data.");
                    		}

							if(error_list.length)
	                        {
	                                alert(error_list.join("\r\n"));
	                                return false;
	                        }else
	                        {
                                $.post(site_url+'/admin/jx_reg_newmem',$('#reg_mem_frm').serialize(),function(resp){
                                        if(resp.status == 'success')
                                        {
                                        	  var mid=$('input[name="mid"]').val(resp.mid);
                                        	
                                            $.post("<?=site_url("admin/jx_pnh_getmid")?>",{mid:resp.mid},function(data){
                                            	 	$("#mid_det").data('mid',resp.mid).dialog('open');
                                        		});
                                            	$(".mid").val(resp.mid);
                                                $('#reg_mem_dlg').dialog('close');
                                        		
                                        }else
                                        {
                                                $('input[name="mid"]').val('');
                                                alert(resp.error);
                                        }
                                },'json');
                        }
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
    font-size: 13px;
    font-weight: bold;
    color:#000;
}
.stk_mem_detwrap a:HOVER {
    font-size: 13px;
    font-weight: bold;
    text-decoration: none;
    color:#000;
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