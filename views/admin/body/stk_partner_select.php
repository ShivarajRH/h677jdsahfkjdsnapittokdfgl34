<?php
/**
 * @author Shivaraj <shivaraj@storeking.in>_Sep_10_2014
 */
?>
<div id="container">
<h2 style="text-align: center">StoreKing Partner Stock Transfer</h2>
	<div class="stk_offlinecard" >
		<div class="stk_ordr_inpwrap"><b>Transfer : <span class="red_star">*</span></b>

			<input type="radio" name="transfer_option" class="transfer_option" value="1" checked="checked"/>To Partner
			
			<input type="radio" name="transfer_option" class="transfer_option" value="2"/>From Partner(Return)
		</div>
		<div class="stk_ordr_inpwrap"><b>Select Partner : <span class="red_star">*</span></b>

						<select name="partner_options" class="partner_options" style="width: 200px;">
								<option value="00">Choose</option>
								<?php 
								if(!$partner_info)
									echo 'No partners defined.';
								else {
										foreach($partner_info as $i=>$partner) { ?>

										<option value="<?=$partner['id']; ?>" name="partner_options" id="partner_options" partner_name="<?=$partner['name']; ?>"><?=$partner['name']; ?></option>

							<?php		}
									}
								?>
						</select>
		</div>
		
		<div id="signin"><input type="button" value="Proceed" onclick='load_franchise_cart_page()' class="button button-rounded button-action button-tiny"></div>
	</div>

</div>

<script>
$(".partner_options").chosen();
function load_franchise_cart_page()
{
	var transfer_option=$(".transfer_option:checked").val();
	var partner_id=$(".partner_options").val();
	$(".partner_options")
	if(partner_id=='00') {
		alert("Please select partner");
		return false;
	}
	location=site_url+"/admin/stk_partner_transfer/"+partner_id+"/"+transfer_option;
}

</script>

<style>

.leftcont{display:none;}

.stk_offlinecard
{
	background-color: #F7F7F7;
    border-radius: 2px 2px 2px 2px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
    margin: 0 auto 25px;
    padding: 20px 9px 50px 20px;
    width: 374px;
    height: auto;
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
width: 35% !important;
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