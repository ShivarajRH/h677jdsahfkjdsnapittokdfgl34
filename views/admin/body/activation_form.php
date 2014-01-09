<div id="container">
<h2 class="page_title">SMS Alternative Activation Form</h2>
		
	<div class="page_content">
		<div class="tab_view">
			<ul class="fran_tabs">
				<li><a class="<?php echo (($type=='mem_reg')?'selected':'')?>" href="#member_reg">Member Registeration</a></li>
				<li><a class="<?php echo (($type=='coup_actv')?'selected':'')?>" href="#coupon_activation">Coupon Activation</a></li>
				<li><a class="<?php echo (($type=='coup_redeem')?'selected':'')?>" href="#coupon_redeemtion">Coupon Redeemtion</a></li>
			</ul>
		<!-- Member registeration START -->
			<div id="member_reg">
				<h4>Member Registeration</h4>
				<div class="tab_content">
					<div class="page_content">
						<table width="100%" cellpadding="0">
							<tr>
								<td width="30%">
									<div class="form"
										style="background: #fafafa; margin-right: 20px; padding: 10px;">
										<form action="<?php echo site_url('admin/pnh_process_franchise_memreg');?>" id="frm_franimeiactv" method="post">
											<table cellpadding="10" cellspacing="0" border="0" style="border-collapse: collapse">
												<tr>
													<td><b>Franchise</b> <span class="red_star">*</span>
													</td>
													<td><select name="fran_id" style="width: 210px;" class="fran_id">
															<option value="">Choose</option>
															<?php
															if($fran_list->num_rows())
															{
																foreach($fran_list->result_array() as $fran)
																{
																	echo '<option '.set_select('fran_id',$fran['franchise_id']).' value="'.$fran['franchise_id'].'">'.$fran['franchise_name'].'</option>';
																}
															}
															?>
													</select> <?php
													echo form_error('fran_id','<span class="error_msg">','</span>');
													?>

														<div id="fran_actv_summary"></div>
													</td>
												</tr>
												<tr>
													<td><b>Mobileno</b> <span class="red_star">*</span></td>
													<td><input maxlength="10" type="text" style="width: 200px;"
														value="<?php echo set_value('mobno');?>" name="mobno"> <span
														id="mobno_resp_msg" style="font-size: 9px"></span> <?php echo form_error('mobno','<span class="error_msg">','</span>');?>
													</td>
												</tr>
												<tr>
													<td><b>Name</b><span class="red_star">*</span></td>
													<td><input type="text" name="member_name" value="<?php echo set_value('member_name');?>"><?php echo form_error('member_name','<span class="error_msg">','</span>');?></td> 
												</tr>
												<tr><td><b>Gender</b><span class="red_star">*</span></td><td><input  type="radio" name="gender" value="0">Male <input type="radio" name="gender" value="1">Female  <?php echo form_error('gender','<span class="error_msg">','</span>');?></td></tr>
												<tr>
													<td colspan="2" align="left"><input type="submit" class="button button-flat-royal button-small button-rounded" value="Register Member">
													</td>
												</tr>

											</table>
										</form>
									</div>
								</td>
								<td valign="top" width="70%" align="left">
								<div class="dash_bar_right">Today Registered :<?php echo $this->db->query("SELECT COUNT(*) AS ttl FROM pnh_member_info m JOIN pnh_m_franchise_info f ON f.franchise_id=m.franchise_id WHERE DATE(FROM_UNIXTIME(m.created_on))=DATE(NOW())")->row()->ttl;?></b>&nbsp;&nbsp;&nbsp;</div>
								<div class="dash_bar_right">Current Month Registered :<?php echo $this->db->query("SELECT COUNT(*) AS ttl FROM pnh_member_info m JOIN pnh_m_franchise_info f ON f.franchise_id=m.franchise_id WHERE MONTH(FROM_UNIXTIME(m.created_on)) = MONTH(CURDATE()) ")->row()->ttl;?></div>
								<div class="dash_bar_right">Total Member Registered :<?php echo $this->db->query("SELECT COUNT(*) AS ttl FROM pnh_member_info m JOIN pnh_m_franchise_info f ON f.franchise_id=m.franchise_id ")->row()->ttl;?></div>
								<div>
								
								<?php $activation_list=$this->db->query("SELECT m.*,f.franchise_name FROM pnh_member_info m JOIN pnh_m_franchise_info f ON f.franchise_id=m.franchise_id 
																		ORDER BY m.created_on DESC LIMIT 10")
								?>
								
								
								<table class="datagrid" width="100%">
								<h3>Latest Registered Member log</h3>
								<thead>
								<th width="20" style="text-align: left">slno</th>
								<th width="130"  style="text-align: left">Franchise Name</th>
								<th width="70"  style="text-align: left">Memeber Name</th>
								<th width="70"  style="text-align: left">Member ID</th>
								<th width="70"  style="text-align: left">Registered On</th>
								</thead>
								
											<tbody>
												<?php
													 $i=1;
													if($activation_list){
													foreach($activation_list->result_array() as $m){
												?>
												<tr>
													<td><?php echo $i;?></td>
													<td><a target="_blank" href="<?php echo site_url('/admin/pnh_franchise/'.$m['franchise_id'])?>"><?php echo $m['franchise_name']?></a></td>
													<td><?php echo $m['first_name'].''.$m['last_name']?></td>
													<td><a target="_blank" href="<?php echo site_url('/admin/pnh_viewmember/'.$m['user_id'])?>"><?php echo $m['pnh_member_id']?></a></td>
													<td><?php echo format_datetime_ts($m['created_on']);?></td>
												</tr>
												<?php $i++;}}?>
											</tbody>
								</table>
								</div>
								</td>
							
							</tr>
						</table>
					</div>


				</div>
			</div>
			<!-- Member registeration END -->
			
			<!-- Coupon Activation START -->
			<div id="coupon_activation">
			<h4>Coupon  Activation</h4> 
			<div class="tab_content">
					<div class="page_content">
						<table width="100%" cellpadding="0">
							<tr>
								<td width="30%">
									<div class="form"
										style="background: #fafafa; margin-right: 20px; padding: 10px;">
										<form action="<?php echo site_url('admin/pnh_franchise_coupon_activation');?>"  method="post">
											<table cellpadding="10" cellspacing="0" border="0" style="border-collapse: collapse">
												<tr>
													<td><b>Franchise</b> <span class="red_star">*</span>
													</td>
													<td><select name="voucher_fid" style="width: 210px;" class="fran_id">
															<option value="">Choose</option>
															<?php
															if($prepaid_franlist->num_rows())
															{
																foreach($prepaid_franlist->result_array() as $vfran)
																{
																	
																	echo '<option '.set_select('voucher_fid',$vfran['franchise_id']).' value="'.$vfran['franchise_id'].'">'.$vfran['franchise_name'].'</option>';
																}
															}
															?>
													</select> <?php
													echo form_error('voucher_fid','<span class="error_msg">','</span>');
													?>

														<div id="fran_actv_summary"></div>
													</td>
												</tr>
												
												<tr>
													<td><b>Voucher Serial no</b> <span class="red_star">*</span></td>
													<td><input  type="text" style="width: 200px;"
														value="<?php echo set_value('voucher_slno');?>" name="voucher_slno"> <span
														id="mobno_resp_msg" style="font-size: 9px"></span> <?php echo form_error('voucher_slno','<span class="error_msg">','</span>');?>
													</td>
												</tr>

												<tr>
													<td width="150"><b>Member Type</b> <span class="red_star">*</span>
													</td>
													<td><select name="mem_type" class="mem_type">
															<?php
															
																echo '<option value="0" '.(set_select('mem_type',0)).' >New Member</option>';
															
																echo '<option value="1" '.(set_select('mem_type',1)).' >Already Registered</option>';
															?>
													</select> <?php echo form_error('mem_type','<span class="error_msg">','</span>');?>
													</td>
												</tr>
												<tr>
													<td><b>Mobileno</b> <span class="red_star">*</span></td>
													<td><input maxlength="10" type="text" style="width: 200px;"
														value="<?php echo set_value('v_mobno');?>" name="v_mobno"> <span
														id="mobno_resp_msg" style="font-size: 9px"></span> <?php echo form_error('v_mobno','<span class="error_msg">','</span>');?>
													</td>
												</tr>
												<tr id="new_memname">
													<td><b>Name</b></td>
													<td><input type="text" name="voucher_mname" value="<?php echo set_value("voucher_mname");?>"></td>	
												</tr>

												<tr>
													<td colspan="2" align="left"><input type="submit"
														class="button button-flat-royal button-small button-rounded"
														value="Activate Coupon">
													</td>
												</tr>

											</table>
										</form>
									</div>
								</td>
								<td valign="top" width="70%" align="left">
								<div class="dash_bar_right">Total Coupon Activated Today:<?php echo $this->db->query("SELECT count(*) as ttl FROM pnh_t_voucher_details t JOIN pnh_member_info m ON m.pnh_member_id=t.member_id JOIN pnh_m_franchise_info f ON f.franchise_id=t.franchise_id  WHERE DATE(activated_on)=CURDATE() and t.status >=3 order by activated_on desc")->row()->ttl?></b>&nbsp;&nbsp;&nbsp;</div>
								<div class="dash_bar_right">Current Month Coupon Activation:<?php echo  $this->db->query("SELECT COUNT(*) as ttl FROM pnh_t_voucher_details t JOIN pnh_member_info m ON m.pnh_member_id=t.member_id JOIN pnh_m_franchise_info f ON f.franchise_id=t.franchise_id WHERE MONTH(activated_on) = MONTH(CURDATE()) AND t.status >=3 ORDER BY activated_on DESC")->row()->ttl?></div>
								<div class="dash_bar_right">Total Coupon Activated:<?php echo  $this->db->query("SELECT COUNT(*) as ttl FROM pnh_t_voucher_details t JOIN pnh_member_info m ON m.pnh_member_id=t.member_id JOIN pnh_m_franchise_info f ON f.franchise_id=t.franchise_id WHERE  t.status >=3 ORDER BY activated_on DESC")->row()->ttl?></div>
								<br><br>
								<div>
								
								<?php $activation_list=$this->db->query("SELECT t.*,m.pnh_member_id,m.first_name,m.last_name,f.franchise_name,m.user_id 
																			FROM pnh_t_voucher_details t JOIN pnh_member_info m ON m.pnh_member_id=t.member_id 
																			JOIN pnh_m_franchise_info f ON f.franchise_id=t.franchise_id  WHERE t.status >=3
																			ORDER BY activated_on DESC LIMIT 10");?>
								
								
								<table class="datagrid" width="100%">
								<h3>Latest  Coupon  Activation log</h3>
											<thead>
												<th>Slno</th>
												<th>Voucher Serialno</th>
												<th>Member Id</th>
												<th>Member Name</th>
												<th>Franchise name</th>
												<th>Voucher Value</th>
												<th>Activated On</th>
											</thead>
											
											<tbody>
											<?php 
											
											if($activation_list){
											$i=1;
											foreach($activation_list->result_array() as $c){?>
											
											<tr>
											<td><?php echo $i;?></td>
											<td><?php echo $c['voucher_serial_no'];?></td>
											<td><a target="_blank" href="<?php echo site_url('/admin/pnh_viewmember/'.$c['user_id'])?>"><?php echo $c['pnh_member_id'];?></a></td>
											<td><?php echo $c['first_name'].''.$c['last_name'];?></td>
											<td><a target="_blank" href="<?php echo site_url('/admin/pnh_franchise/'.$c['franchise_id'])?>"><?php echo $c['franchise_name'];?></a></td>
											<td><?php echo $c['customer_value'];?></td>
											<td><?php echo format_datetime($c['activated_on']);?></td>
											</tr>
											<?php $i++; }}?>
											</tbody>
																						
									</table>
								</div>
								</td>
							</tr>
						</table>
					</div>
			
			</div>
			</div>
			<!-- Coupon Activation END -->
			<div id="coupon_redeemtion">
			
			<div class="tab_view tab_view_inner">
			<div class="dash_bar_right">Total Coupon Redeemed Today:<?php echo $this->db->query("SELECT count(*) as ttl FROM pnh_t_voucher_details t JOIN pnh_member_info m ON m.pnh_member_id=t.member_id JOIN pnh_m_franchise_info f ON f.franchise_id=t.franchise_id  WHERE DATE(redeemed_on)=CURDATE() and t.status >=3 order by redeemed_on desc")->row()->ttl?>&nbsp;&nbsp;&nbsp;</div>
			<div class="dash_bar_right">Current Month Coupon Redeemtion:<?php echo  $this->db->query("SELECT COUNT(*) as ttl FROM pnh_t_voucher_details t JOIN pnh_member_info m ON m.pnh_member_id=t.member_id JOIN pnh_m_franchise_info f ON f.franchise_id=t.franchise_id WHERE MONTH(redeemed_on) = MONTH(CURDATE()) AND t.status >=3 ORDER BY redeemed_on DESC")->row()->ttl?></div>
			<div class="dash_bar_right">Total Coupon redeemed:<?php echo  $this->db->query("SELECT COUNT(*) as ttl FROM pnh_t_voucher_details t JOIN pnh_member_info m ON m.pnh_member_id=t.member_id JOIN pnh_m_franchise_info f ON f.franchise_id=t.franchise_id  where t.status >=3 ORDER BY redeemed_on DESC")->row()->ttl?></div>	
		
			<ul>
			<li><a href="#coupon_redeemtion">Redeem Coupon</a></li>
			<li><a href="#coupon_redeemlog">Latest Coupon Redeemtion Log</a></li>
			</ul>
			<div id="coupon_redeemlog">
			
							<table>
								<tr>
									<td valign="top" width="100%" align="left" colspan="3">
								<div>
								
								<?php $activation_list=$this->db->query("SELECT t.*,m.first_name,m.last_name,f.franchise_name,m.user_id FROM pnh_t_voucher_details t
																			JOIN pnh_member_info m ON m.pnh_member_id=t.member_id
																			JOIN pnh_m_franchise_info f ON f.franchise_id=t.franchise_id
																			WHERE STATUS>3 
																			ORDER BY redeemed_on DESC
																			LIMIT 10");?>
																											
								
								<table class="datagrid" width="100%">
								<h3>Latest  Coupon  Redeemtion log</h3>
											<thead>
												<th>Slno</th>
												<th>Voucher Serialno</th>
												<th>Member Id</th>
												<th>Member Name</th>
												<th>Franchise name</th>
												<th>Voucher Value</th>
												<th>Activated On</th>
											</thead>
											
											<tbody>
											<?php 
											
											if($activation_list){
											$i=1;
											foreach($activation_list->result_array() as $c){?>
											
											<tr>
											<td><?php echo $i;?></td>
											<td><?php echo $c['voucher_serial_no'];?></td>
											<td><a target="_blank" href="<?php echo site_url('/admin/pnh_viewmember/'.$c['user_id'])?>"><?php echo $c['member_id'];?></a></td>
											<td><?php echo $c['first_name'].''.$c['last_name'];?></td>
											<td><a target="_blank" href="<?php echo site_url('/admin/pnh_franchise/'.$c['franchise_id'])?>"><?php echo $c['franchise_name'];?></a></td>
											<td><?php echo $c['customer_value'];?></td>
											<td><?php echo format_datetime($c['redeemed_on']);?></td>
											</tr>
											<?php $i++; }}?>
											</tbody>
																						
									</table>
								</div>
								</td>
							</tr>
										
								</table>
							</div>
			
			
				<div id="coupon_redeemtion">
				<h4>Coupon Redeemtion</h4>
				<div class="tab_content">
					<div class="page_content">
						<div class="page_content">
						<table width="100%" cellpadding="0" style="clear:both;">
							<tr>
								<td width="30%">
									<div class="form"
										style="background: #fafafa; margin-right: 20px; padding: 10px;">
										<form action="<?php echo site_url('admin/pnh_franchise_coupon_redeemtion');?>" id="couponredemtion_frm" method="post">
											<table cellpadding="10" cellspacing="0" border="0" style="border-collapse: collapse">
												
												<tr>
													<td ><b>Member Mobileno</b><span class="red_star">*</span></td>
													
													<td colspan="2"><input maxlength="10" type="text" style="width: 200px;" class="member_mobno"
														value="<?php echo set_value('mem_mobno');?>" name="mem_mobno"> <span
														id="mobno_resp_msg" style="font-size: 9px"></span> <?php echo form_error('mobno','<span class="error_msg">','</span>');?>
														<div id="mem_fran" style="background: #fcfcfc;"></div>
													</td>
												<!--  </tr>
												
												<tr>-->
													<td><b>Voucher Secret code</b> <span class="red_star">*</span></td>
													
													<td  colspan="2"><input  type="text" style="width: 200px;" class="voucher_code"
														value="<?php echo set_value('voucher_code');?>" name="voucher_code"> <span
														id="mobno_resp_msg" style="font-size: 9px"></span> <?php echo form_error('voucher_code','<span class="error_msg">','</span>');?>
													<div id=voucher_det style="background: #fcfcfc;"></div>
													</td>
													
												</tr>
												</table>
												<table>
												<tr>
												<td><b>Place Order to Redeem</b> <span class="red_star">*</span></td>
												</tr>
												
												<tr>
													<td >
														<table>
														<tr><td>Search Deal</td> <td>:</td><td> <div id="srch_results"></div><input type="text" class="inp" style="width:320px;" id="p_srch" autocomplete="off" ></td></tr>
														<tr><td>Product ID</td><td> :</td><td> <input type="text" class="inp" maxlength="8" size=32 id="p_pid" autocomplete="off" ><input type="button" value="Add" class="add_product"></td></tr>
														</table>
													</td>
													<td>
														<table class="datagrid" id="prods_order" width="135%" style="cellspacing:0px;clear:both;">
															<thead>
																<tr>
																	
																	<th>Product Image-PID</th>
																	<th>Product Name</th>
																	<th>MRP</th>
																 <th>Offer price / DP price</th>
																	<!--  <th>Landing Cost</th>-->
																	 <th>Customer Price</th>
																	 <th>Franchise Price</th>
																	<th>Qty</th>
																	<th>Sub Total</th>
																	<th>Actions</th>
																</tr>
																
															</thead>
															<tbody>
															
															</tbody>
															
														</table>
													</td>
													
													<tr>
													
													<td> </td>
													<td align="left" id="ttl_value">Total Billing Amount:<b></b></td>
													<td align="right" width="20%"  id="bilingttl_value">Total Order Value:<b></b></td>
													<td></td>
													</tr>
													<tr>
													<td  align="right" ><input type="submit" id="coupon_redeem" class="button button-flat-royal button-small button-rounded" onclick='final_confirm()'value="Redeem Coupon" style="margin-right: -860px;">
													</td>
													</tr>
													</tr>
													
												</table>
												</div>
							
							</form>
									</div>
								</td>
							</tr>
						</table>
					</div>
					
					</div>
				
			</div>
</div>
		</div>
		</div>

	</div>
	
</div>
	<table id="template" style="display: none">
		<tbody>
			<tr pid="%pid%" pimage="%pimage% %pid%" pname="%pname%" mrp="%mrp%"
				price="%price%" lcost="%lcost%" margin="%margin%" menuid="%menuid%">
				<!--  <td>%sno%</td>-->
				<td><img alt="" height="100" src="<?=IMAGES_URL?>items/%pimage%.jpg" 	style="float: right; margin-right: 20px;"> 
					
					<div class="p_extra">
					<b>PID :</b>%pid%
					</div>
				</td>
			
				
				<td><input class="pids" type="hidden" name="pid[]" value="%pid%"><span>%pname%</span>
					 <input type="hidden" name="menu[]" value="%menuid%" class="menuids">
				<div style="margin-top: 5px; font-size: 12px;">
						<div class="p_extra">
							<b>Category :</b> %cat%
						</div>
						<div class="p_extra">
							<b>Brand:</b> %brand%
						</div>

						<div class="p_stk">Stock Suggestion: %stock%</div>
						<div class="p_attr">%attr%</div>
						<div class="p_attr">%confirm_stock%</div>
					</div>
				</td>

				<td><b style="font-size: 13px">%mrp%</b>
					<div class="p_extra"
						style="display: %dspmrp%; font-size: 11px; margin-top: 10px; line-height: 19px; padding: 10px; font-weight: bold; background: wheat !important; text-align: center;">
						<b>OldMRP:</b> <span
							style="color: #cd0000; font-size: 13px;">%oldmrp%</span>
					</div>
				</td>
				<td><span class="off_price">%price%</span></td>
				<td><span class="price">%price%</span></td>
				
				  <td><span style="background-color: #89c403; display: block; padding: 12px 15px;">
					<b class="lcost">%lcost%</b> 
					</span>
			</td>
				
																
				
				<td><input type="text" class="qty" size=2 name="qty[]" value="1"></td>
				  <td><span class="stotal">%lcost%</span></td>
					<td><a href="javascript:void(0)" onclick='$($(this).parents("tr").get(0)).remove();remove_pid("%pid%")'>remove</a><br>
					<a href="<?=site_url("admin/pnh_deal")?>/%pid%" target="_blank">view</a>
					</td>
				</tr>
			</tbody>
		</table>
<style>
.tabs {
	padding: 0px;
}

.tabcont {
	padding: 5px;
}
.error_msg{font-size: 10px;background: rgba(205, 0, 0, 0.6);color: #FFF;padding:3px;border-radius:3px;display: inline-block;}

#srch_results{
	margin-left:-1px;
	position: absolute;
	width: 400px;
	background: #EEE;
	border: 1px solid #AAA;
	max-height: 200px;
	min-width: 300px;
	max-width: 326px;
	margin-top: 24px;
}
#srch_results a{
	padding: 5px 10px;
	font-size: 14px;
	display: inline-table;
	width: 400px;
	text-transform: capitalize;
	border-bottom: 1px dotted #DDD;
	background: white;
} 
#srch_results a:hover{
background: #CCC;
color: black;
text-decoration: none;
}

#mob_error{
vertical-align:center;
color:red;
}

#mem_fran{
color:blue;
background:#eee;
padding:5px;
font-size:70%;
font-weight:bold;
margin:5px 0px;

}
#voucher_det
{
color:blue;
background:#eee;
padding:5px;
font-size:70%;
font-weight:bold;
margin:5px 0px;
}
</style>
<script>
var mobok=0;
$(".member_mobno").change(function(){
$.post("<?=site_url("admin/jx_pnh_getvouchermid")?>",{member_mobno:$(this).val(),more:1},function(data){
	$("#mem_fran").html(data).show();
});
	
});

															
$("#p_pid").focus();
var jHR=0,search_timer;
$(".leftcont").hide();
$('.tab_view').tabs();

$('.fran_tabs .selected').trigger('click');

$(".mem_type").change(function(){
	if($(this).val()==0)
		$('#new_memname').show();
	else
		$('#new_memname').hide();
});
$("#p_pid").keydown(function(e){
	if(e.which==13)
	{
		$(".add_product").click();
		e.preventDefault();
		e.stopPropagation();
		return false;
	}
	return true;
});
function trig_loadpnh(pid)
{
	$("#p_pid").val(pid);
	$(".add_product").click();
}

function add_deal_callb(name,pid,mrp,price,store_price)
{
	$('#srch_results').html('').hide();
	
	$("#p_srch").val("").focus();
	$("#p_pid").val(pid);
	$(".add_product").click();
	
}

$("#p_srch").keyup(function(){
	q=$(this).val();
	var	vcode=$('.voucher_code').val();
	var mem_mobno=$('.member_mobno').val();
	var type='v_redeem';
	if(q.length<3)
		return true;
	if(jHR!=0)
		jHR.abort();
	window.clearTimeout(search_timer);
	search_timer=window.setTimeout(function(){
	jHR=$.post('<?=site_url("admin/pnh_jx_searchdeals")?>',{q:q,vcode:vcode,mem_mobno:mem_mobno,type:type},function(data){
		$("#srch_results").html(data).show();
	});},200);
});

var pids=[];

function remove_pid(pid)
{
	var t_pids=pids;
	pids=[];
	for(i=0;i<t_pids.length;i++)
		if(pid!=t_pids[i])
			pids.push(t_pids[i]);
}

function remove_psel(ele)
{
	$($(ele).parents("tr").get(0)).remove();
	remove_pid("%pid%");
	
	$('#prods_order tbody tr').each(function(i,itm){
		$('td:first',this).text(i+1);
	});
	
}





$(".add_product").click(function(){

	
	var vcode=$(".voucher_code").val();
	
	pid=$("#p_pid").val();
	if($.inArray(pid,pids)!=-1)
	{
		alert("Product already added");return;
	}
	if(pid.length==0)
	{alert("Enter product id");return;}
	$("#p_pid").attr("disabled",true);
	var mmob=$('.member_mobno').val();
	$.post("<?=site_url("admin/jx_pnh_load_voucherprod")?>",{pid:pid,vcode:vcode,mmob:mmob},function(data){
	
		i=pids.length;
		obj=p=$.parseJSON(data);
		$("#p_pid").attr("disabled",false);
		if(obj.error1!=undefined)
		{
			if(obj.error1==1)
			{
				alert(obj.msg);
				return;
			}
		}
			
		if(obj.length==0)
		{	alert("The product is DISABLED \nor\nNo product available for given id");return;}
		
		if(obj.error != undefined)
		{
			alert(obj.error);
			return ;
		}
		
		//show_prod_suggestion(p.pid);
		
		//load_frans_cancelledorders(pid);
		if(p.live==0)
		{	alert("The product is out of stock or not sourceable");return false; }
		$("#p_pid").val("");
		template=$("#template tbody").html();
		template=template.replace(/%pimage%/g,p.pic);
		template=template.replace(/%pid%/g,p.pid);
		template=template.replace(/%menuid%/g,p.menuid);
		template=template.replace(/%attr%/g,p.attr);
		template=template.replace(/%pname%/g,p.name);
		template=template.replace(/%cat%/g,p.cat);
		template=template.replace(/%brand%/g,p.brand);
		template=template.replace(/%margin%/g,p.margin);
		if(p.oldmrp == '-')
			template=template.replace(/%dspmrp%/g,'none');
		else
			template=template.replace(/%dspmrp%/g,'block');
		
		template=template.replace(/%oldmrp%/g,p.oldmrp);
		template=template.replace(/%newmrp%/g,p.mrp);
		template=template.replace(/%mrp%/g,p.mrp);
		template=template.replace(/%price%/g,p.price);
		template=template.replace(/%lcost%/g,p.lcost);
		template=template.replace(/%stock%/g,p.stock);
		template=template.replace(/%confirm_stock%/g,p.confirm_stock);
		
		
//		template=template.replace(/%src%/g,p.src);
		template=template.replace(/%mrp%/g,p.mrp);
		$("#prods_order tbody").append(template);
		pids.push(p.pid);

		compute_ttl();
		
		compute_ttlbillingamt();
		
		
			
	});
});

$("#prods_order .qty").live("change",function(){
	p=$(this).parents("tr").get(0);
	$(".stotal",p).html(parseFloat($(".lcost",p).text())*parseInt($(".qty",p).val()));
	$(".price",p).html(parseFloat($(".off_price",p).text())*parseInt($(".qty",p).val()));
	compute_ttl();
	
});

$("#prods_order .qty").live("change",function(){
	p=$(this).parents("tr").get(0);
	$(".price",p).html(parseFloat($(".off_price",p).text())*parseInt($(".qty",p).val()));
	compute_ttlbillingamt();
});

function compute_ttl()
{
	total=0;
	$("#ttl_value b").html("");
	$("#prods_order .stotal").each(function(){
		p=$(".qty").parents("tr").get(0);
		total+=parseFloat($(this).html())*parseInt($(".qty",p).val());
		
	});
	
	$("#ttl_value b").html(total);
		
}

function compute_ttlbillingamt()
{
	biling_ttl=0;
	$("#bilingttl_value b").html("");
	$("#prods_order .price").each(function(){
		p=$(".qty").parents("tr").get(0);
		biling_ttl+=parseFloat($(this).html())*parseInt($(".qty",p).val());
		
	});

	$("#bilingttl_value b").html(biling_ttl);
}

$('#p_srch').mouseover(function(){
	
	if($(this).val().length)
		$('#srch_results').show();
	else
		$('#srch_results').html('').hide();
}).focus(function(){
	$('#srch_results').show();
});

$('#srch_results').mouseleave(function(){
	$('#srch_results').hide(); 
});

$('#coupon_redeem').click(function(){

	total=0;
	ppids=[];
	qty=[];
	
	
	$("#prods_order .stotal").each(function(){
		total+=parseFloat($(this).html());
	});

	
	
	$("#prods_order .pids").each(function(){
		ppids.push($(this).val());
	});
	
	
	$("#prods_order .qty").each(function(){
		qty.push($(this).val());
	});

	if(ppids.length==0)
	{alert("There are no products in the order");return false;}

	if(confirm("Total order value : Rs "+total+"\nAre you sure want to place the order?"))
	{ 
		$('#couponredemtion_frm').submit();
	}else
	{
		return false;
	}
});
$('.fran_id').chosen();
$('.voucher_code').change(function(){
	var mem_mobno=$(".member_mobno").val();
	
	$.post("<?=site_url("admin/jx_pnh_getmemvoucherdet")?>",{vcode:$(this).val(),mem_mobno:mem_mobno},function(data){
		$("#voucher_det").html(data).show();
		console.log(data);
	});
});

$( ".fran_tabs a" ).click(function(){
	window.location.hash = $(this).attr('href');   
	window.scrollTo(0,0); 
});

</script>