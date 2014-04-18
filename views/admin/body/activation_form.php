<div id="container">
<h2 class="page_title">SMS Alternative Activation Form</h2>
		
	<div class="page_content">
		<div class="tab_view">
			<ul class="fran_tabs">
				<li><a class="<?php echo (($type=='mem_reg')?'selected':'')?>" href="#member_reg">Member Registeration</a></li>
				<li><a class="<?php echo (($type=='coup_actv')?'selected':'')?>" href="#coupon_activation">Coupon Activation</a></li>
				<li><a class="<?php echo (($type=='coup_redeem')?'selected':'')?>" href="#coupon_redeemtion">Coupon Redeemtion</a></li>
				<li><a class="<?php echo (($type=='imei_actv')?'selected':'')?>" href="#imei_activation">IMEI Activation</a></li>
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
												<tr>
													<td><b>DOB</b></td>
													<td><input type="text" name="mem_dob" value=""></td>
												</tr>
												<tr>
													<td><b>Marital Status</b></td>
													<td><input type="radio" value="1" name="marital" checked="checked">
														Married
														<input type="radio" checked="checked" value="0" name="marital">
														Single
														<input type="radio" value="2" name="marital">Other</td>
														
												</tr>
												<tr>
													<td><b>Address</b></td>
													<td><textarea name="mem_address" value=""></textarea></td>
												</tr>
												<tr>
													<td><b>PinCode</b></td>
													<td><input type="text" name="pin_code" value="" size="18px"></td>
												</tr>
												<tr>
													<td><b style="font-style: 9px;">Monthy Shopping Expense</b></td>
													<td>
														<ul>
															<li><input type="radio" checked="checked" value="0" name="expense">&lt; Rs. 2000</li>
															<li><input type="radio" value="1" name="expense">Rs 2001 - Rs 5000</li>
															<li><input type="radio" value="1" name="expense">Rs 5001 - Rs 10000</li>
															<li><input type="radio" value="1" name="expense"> &lt; Rs. 10000</li>		
														</ul>					
													</td>
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
														id="mobno_resp_msg" style="font-size: 9px"></span> <?php echo form_error('mem_mobno','<span class="error_msg">','</span>');?>
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
		<!-- IMEI Activation Form Start -->
		<div id="imei_activation">
			<div class="tab_view tab_view_inner">
				<ul>
					<li><a href="#imei_activation">SK IMEI Activation</a></li>
					<li><a href="#non_skimei_activation">NON SK IMEI Activation</a></li>
				</ul>
			
			<!-- SK IMEI Block Start -->
			<div id="imei_activation">
			<table width="100%" cellpadding="0">
			<tr>
				<td width="30%">
					<div class="form" style="background: #fafafa;margin-right:20px;padding:10px;">
						<form action="<?php echo site_url('admin/pnh_process_franchise_imei_activation');?>" id="frm_franimeiactv" method="post">
							<table cellpadding="10" cellspacing="0" border="0" style="border-collapse: collapse">
								<tr style="background: #f1f1f1">
									<td><b style="padding:5px;">Enter IMEI</b> <span class="red_star">*</span></td>	
									<td><input type="text" style="width: 200px;" value="<?php echo set_value('imei_no');?>" name="imei_no" >
										<?php echo form_error('imei_no','<span class="error_msg">','</span>');?>
									</td>
								</tr>
								<tr id="imei_det"></tr>
								<tr>
									<td><b>Mobileno</b> <span class="red_star">*</span></td>	
									<td>
										<input type="hidden" name="franchise_id" value="0">
										<input type="hidden" name="member_id" value="0">
										<input type="hidden" name="imei_hasinsurance" value="0">
										
										<input maxlength="10" type="text" style="width: 200px;" value="<?php echo set_value('imei_mobno');?>" name="imei_mobno" >
										<span id="mobno_resp_msg" style="font-size: 9px"></span>
										<?php echo form_error('imei_mobno','<span class="error_msg">','</span>');?>
									</td>
									
								</tr>
								<tr id="new_memname_imei" style="display:none">
									<td><b>Name</b></td>	
									<td>
										<input type="text" style="width: 200px;" name="mem_name" value="">
									</td>
								</tr>
								<tr class="insu" style="display:none">
									<td><b>Proof Type</b></td>	
									<td><select name="insurance[proof_type]">
										<option value="">Select</option>
                                                    <?php $insurance_types=$this->db->query("select * from insurance_m_types order by name asc")->result_array();
                                                            if($insurance_types){
                                                            foreach($insurance_types as $i_type){
                                                    ?>
                                                            <option value="<?php echo $i_type['id']?>"><?php echo $i_type['name']?></option>
                                                    <?php }}?>
                                         </select>
									</td>
								</tr>
								<tr class="insu" style="display:none">
									<td><b>Proof ID</b></td>	
									<td>
										<input type="text" style="width: 200px;" name="insurance[proof_id]" value="">
									</td>
								</tr>
								<tr class="insu" style="display:none">
									<td><b>Address</b></td>	
									<td>
										<textarea type="text"  name="insurance[proof_address]" value=""></textarea>
									</td>
								</tr>
								<tr class="insu" style="display:none">
									<td><b>City</b></td>	
									<td>
										<input type="text" width="200px"  name="insurance[proof_city]" value=""></textarea>
									</td>
								</tr>
								<tr class="insu" style="display:none">
									<td><b>Pin Code</b></td>	
									<td>
										<input type="text" style="width: 200px;" name="insurance[proof_pincode]" value="">
									</td>
								</tr>		
								<tr class="insu" style="display:none">
									<td><b>Franchise Receipt No</b></td>	
									<td>
										<input type="text" style="width: 200px;" name="insurance[fran_receipt_no]" value="">
									</td>
								</tr>	
								<tr class="insu" style="display:none">
									<td><b>Franchise Receipt Amount</b></td>	
									<td>
										<input type="text" style="width: 200px;" name="insurance[fran_receipt_amt]" value="">
									</td>
								</tr>	
								<tr class="insu" style="display:none">
									<td><b>Franchise Receipt Date</b></td>	
									<td>
										<input type="text" style="width: 200px;" id="fran_receiptdate" name="insurance[fran_receipt_date]" value="">
									</td>
								</tr>				
								<tr id="mobno_det"></tr>
								<tr>
									<td colspan="2" align="left">
										<input type="submit" disabled="" id="actv_submit_btn" class="button button-flat-royal button-small button-rounded" value="Activate IMEI/Serialno" > 
									</td>
								</tr>
							</table>
						</form>
					</div>
				</td>
				<td valign="top" width="70%" align="left">
					 <div>
						<?php
							$imei_actv_list = $this->db->query("select f.invoice_no,b.userid as user_id,e.username as activated_byname,imei_activated_on,activated_by,activated_mob_no,activated_member_id,d.franchise_id,franchise_name,imei_no,imei_reimbursement_value_perunit as imei_credit_amt from t_imei_no a join king_orders b on a.order_id = b.id join king_transactions c on c.transid = b.transid join pnh_m_franchise_info d on d.franchise_id = c.franchise_id left join king_admin e on e.id = a.activated_by join king_invoice f on f.order_id = b.id where a.is_imei_activated = 1 order by imei_activated_on desc limit 10");
						?>
						<h3 style="margin:5px 0px">Latest IMEI/Serialno Activations</h3>
						<table class="datagrid" width="100%">
							<thead>
								<th width="20" style="text-align: left">Slno</th>
								<th width="130"  style="text-align: left">Activated On</th>
								<th width="70"  style="text-align: left">Activated By</th>
								<th  style="text-align: left">Franchise</th>
								<th  style="text-align: left" width="80">Invoiceno</th>
								<th  style="text-align: left" width="150">IMEI/Serial no</th>
								<th  style="text-align: left" width="100">Mobile no</th>
								<th  style="text-align: left" width="100">Activated MemberID</th>
								<th  style="text-align: left" width="30">Credit</th>
							</thead>
							<tbody>
								<?php
									$i=0;
									foreach($imei_actv_list->result_array() as $imei_det)
									{
								?>
										<tr>
											<td><?php echo ++$i ?></td>
											<td><?php echo format_datetime($imei_det['imei_activated_on']) ?></td>
											<td><?php echo ($imei_det['activated_byname']?$imei_det['activated_byname']:'SMS') ?></td>
											<td><?php echo anchor('admin/pnh_franchise/'.$imei_det['franchise_id'],$imei_det['franchise_name'],'target="_blank"') ?></td>
											<td><a href="<?php echo site_url('admin/invoice/'.$imei_det['invoice_no']);?>" target="_blank"><?php echo $imei_det['invoice_no'] ?></a></td> 
											<td><?php echo $imei_det['imei_no'] ?></td>
											<td><?php echo $imei_det['activated_mob_no'] ?></td>
											<td><?php echo anchor('admin/pnh_viewmember/'.$imei_det['user_id'],$imei_det['activated_member_id'],'target="_blank"') ?></td>
											<td><?php echo $imei_det['imei_credit_amt'] ?></td>
										</tr>
								<?php				
									}
								?>
							</tbody>
						</table>
					</div>
				</td>
			</tr>
		</table>
		</div>
		<!-- SK IMEI Block End -->
		<!-- NON SK IMEI Block Start -->
		
		<div id="non_skimei_activation">
		
		<div align="left" style="float: left; width: 35%;">
			<form action="<?php echo site_url('admin/pnh_process_nonsk_imei_activation');?>" method="post" data-validate="parsley">
					
							<div  class="nonsk_imeiwrap">
								<div class="fran_imeilabel"><b >Enter IMEI :</b> <span class="red_star">*</span></div>	
								<div class="imei_inp"><input type="text" style="width: 200px;" value="<?php echo set_value('nonsk_imeino');?>" name="nonsk_imeino" data-required="true" >
									<?php echo form_error('nonsk_imeino','<span class="error_msg">','</span>');?>
								</div>
								<div  class="fran_imeilabel"><span id="nonskimei_overview"></span></div>
							</div>
							
							
							<div  class="nonsk_imeiwrap">
								 <div class="fran_imeilabel"><b>Franchise :</b><span class="red_star">*</span></div>
									<div class="imei_inp"><select  name="non_imei_fid" id="non_imei_fid" style="width:200px;" data-required="true">
									<option value="">Choose</option>
									<?php
										if($fran_list->num_rows())
										{
											foreach($fran_list->result_array() as $fran)
											{
												echo '<option '.set_select('imei_fran_id',$fran['franchise_id']).' value="'.$fran['franchise_id'].'">'.$fran['franchise_name'].'</option>';
											}
										}
										?>
									
										</select>
									</div>
							</div>
							<div class="nonsk_imeiwrap">	
								<div class="fran_imeilabel"><b>Order ID :</b><span class="red_star">*</span></div>
								<div class="imei_inp"><input type="text" style="width:200px;" name="order_id" data-required="true"></div>
								<!--  <div style="float:right;margin:-70px 279px;" id="nonskimeireplcmnt_orderdet"></div>-->
								<div style="float:left;font-size: 10px;" id="nonskimeireplcmnt_orderdet"></div>
								
							</div>
							<div class="nonsk_imeiwrap">
								<div class="fran_imeilabel"><b>Mobileno :</b> <span class="red_star">*</span></div>	
								<div class="imei_inp">
									<input maxlength="10" type="text" style="width: 200px;" value="<?php echo set_value('nonskimei_mobno');?>" name="nonskimei_mobno" data-required="true" >
								</div>
								<span  id="mem_det"></span>
							</div>
							
							<div class="nonsk_imeiwrap nonsk_imei_newmem" >
								<div class="fran_imeilabel"><b>Name :</b> <span class="red_star">*</span></div>	
								<div class="imei_inp">
									<input type="text" style="width: 200px;" value="<?php echo set_value('nonskimei_memname');?>" name="nonskimei_memname" data-required="true" >
								</div>
								<div style="float:left;font-size: 10px;" id="mem_det"></div>
							</div>
													 
							 <div class="nonsk_imeiwrap">
								<div class="fran_imeilabel"><b>Model No :</b><span class="red_star">*</span></div>
								<div class="imei_inp"><input type="text" name="nonskimei_modalno" value="<?php echo set_value('nonskimei_modalno');?>" style="width: 200px;" data-required="true"></div>
							</div>
										
							<div class="nonsk_imeiwrap">
								<div class="fran_imeilabel"><b>Receipt No :</b><span class="red_star">*</span></div>	
								<div class="imei_inp">
									<input  type="text" style="width: 200px;" value="<?php echo set_value('nonskimei_fran_receiptno');?>" name="nonskimei_fran_receiptno" data-required="true" >
								</div>
							</div>
							<div class="nonsk_imeiwrap">
								<div class="fran_imeilabel"><b>Amount :</b> <span class="red_star">*</span></div>	
								<div class="imei_inp">
									<input  type="text" style="width: 200px;" value="<?php echo set_value('nonskimei_fran_receiptamt');?>" name="nonskimei_fran_receiptamt" data-required="true" >
								</div>

	</div>
							<div class="nonsk_imeiwrap">
								<div class="fran_imeilabel"><b>Date :</b> <span class="red_star">*</span></div>	
								<div class="imei_inp">
									<input type="text" style="width: 200px;" value="<?php echo set_value('nonskimei_receipt_date');?>" name="nonskimei_receipt_date" data-required="true" >
								</div>
							</div>
							<div class="nonsk_imeiwrap">
								<div class="fran_imeilabel"><b>Proof Type :</b><span class="red_star">*</span></div>
											<div class="imei_inp"><select name="nonsk_imei_prooftype">
													<option value="">Select</option>
                                                    <?php $insurance_types=$this->db->query("select * from insurance_m_types order by name asc")->result_array();
                                                            if($insurance_types){
                                                            foreach($insurance_types as $i_type){
                                                    ?>
                                                            <option value="<?php echo $i_type['id']?>"><?php echo $i_type['name']?></option>
                                                    <?php }}?>
                                                    </select>
                                         	</div>			
								</div>
								<div class="nonsk_imeiwrap">
									<div class="fran_imeilabel"><b>Proof ID :</b><span class="red_star">*</span></div>	
									<div class="imei_inp">
										<input  type="text" style="width: 200px;" value="<?php echo set_value('nonskimei_fran_proofid');?>" name="nonskimei_fran_receiptno" data-required="true" >
									</div>
								</div>
								<div class="nonsk_imeiwrap">
									<div class="fran_imeilabel"><b>Address :</b> <span class="red_star">*</span></div>	
										<div class="imei_inp">
											<textarea  style="width: 200px;" value="<?php echo set_value('nonskimei_proof_add');?>" name="nonskimei_proof_add" data-required="true" ></textarea>
									</div>
								</div>			
								<div class="nonsk_imeiwrap">
									<div class="fran_imeilabel"><b>City :</b> <span class="red_star">*</span></div>	
										<div class="imei_inp">
											<input type="text"  style="width: 200px;" value="<?php echo set_value('nonskimei_proof_city');?>" name="nonskimei_proof_city" data-required="true" >
									</div>
								</div>
								<div class="nonsk_imeiwrap">
									<div class="fran_imeilabel"><b>Pincode :</b> <span class="red_star">*</span></div>	
										<div class="imei_inp">
											<input type="text"  style="width: 200px;" value="<?php echo set_value('nonskimei_proof_pincode');?>" name="nonskimei_proof_pincode" data-required="true" >
									</div>
								</div>
							
								<div class="nonsk_imeiwrap" style="float:right;">	
									<input class="button button-flat-royal button-small button-rounded" type="submit" value="Submit" id="nonskimei_btn">
								</div>
					</form>
			</div>
			
			 <div align="right" style="display: inline-block;width:65%;">
						<?php
							$imei_actv_list = $this->db->query("SELECT i.franchise_id,i.nonsk_imei_no as imei_no,f.franchise_name,m.pnh_member_id AS activated_member_id,m.mobile AS activated_mob_no,i.insurance_amount,n.offer_status,i.created_on AS activated_on,a.name AS activated_byname,m.user_id  FROM `non_sk_imei_insurance_orders`i JOIN pnh_m_franchise_info f ON f.franchise_id=i.franchise_id JOIN pnh_member_info m ON m.user_id=i.userid  JOIN king_orders k ON k.id=i.order_id JOIN `pnh_member_insurance` n ON n.order_id=i.order_id JOIN king_admin a ON a.id=i.created_by  order by activated_on desc limit 10");

						?>
						<h3 style="margin:5px 0px;float:left;">Latest Non SK IMEI/Serialno Activations</h3>
						<table class="datagrid" width="100%">
							<thead>
								<th width="20" style="text-align: left">Slno</th>
								<th width="130"  style="text-align: left">Activated On</th>
								<th width="70"  style="text-align: left">Activated By</th>
								<th  style="text-align: left">Franchise</th>
								<!--  <th  style="text-align: left" width="80">Invoiceno</th>-->
								<th  style="text-align: left" width="150">IMEI/Serial no</th>
								<th  style="text-align: left" width="100">Mobile no</th>
								<th  style="text-align: left" width="100">Activated MemberID</th>
								<th  style="text-align: left" width="30">Insurance Amount</th>
							</thead>
							<tbody>
								<?php
									$i=0;
									foreach($imei_actv_list->result_array() as $imei_det)
									{
								?>
										<tr>
											<td><?php echo ++$i ?></td>
											<td><?php echo format_datetime($imei_det['activated_on']) ?></td>
											<td><?php echo ($imei_det['activated_byname']?$imei_det['activated_byname']:'SMS') ?></td>
											<td><?php echo anchor('admin/pnh_franchise/'.$imei_det['franchise_id'],$imei_det['franchise_name'],'target="_blank"') ?></td>
											<!--  <td><a href="<?php echo site_url('admin/invoice/'.$imei_det['invoice_no']);?>" target="_blank"><?php echo $imei_det['invoice_no'] ?></a></td>--> 
											<td><?php echo $imei_det['imei_no'] ?></td>
											<td><?php echo $imei_det['activated_mob_no'] ?></td>
											<td><?php echo anchor('admin/pnh_viewmember/'.$imei_det['user_id'],$imei_det['activated_member_id'],'target="_blank"') ?></td>
											<td><?php echo $imei_det['insurance_amount'] ?></td>
										</tr>
								<?php				
									}
								?>
							</tbody>
						</table>
		
		</div>
			
		<!-- NON SK IMEI Block End -->
		</div>
	</div>
		<!-- IMEI Activation Form END -->
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

.nonsk_imei_det{font-size: 10px;color: #FFF;padding:3px;border-radius:3px;display: inline-block;}

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

$('.fran_id,#non_imei_fid').chosen();
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

$('input[name="mem_dob"]').datepicker();

$('#fran_receiptdate').datepicker();

$('input[name="nonskimei_receipt_date"]').datepicker();

$('input[name="imei_no"]').change(function(){
	
	$('input[name="member_id"]').val(0);
	$('input[name="franchise_id"]').val(0);
	
	
	$('#new_memname_imei').hide();
	$('input[name="mem_name"]').val('');
	
	$('#mobno_overview').html('').hide();
	
	$('input[name="mobno"]').val('').attr('disabled',true);
	
	$('#imei_det').html('<td colspan="2" style="background: #ffffa0;padding:10px;color:#333"><div id="imei_overview">Loading...</div></td>');
	
	$('#actv_submit_btn').attr('disabled',true); 
	$.post(site_url+'/admin/jx_getimeidet','imeino='+$(this).val(),function(resp){
		if(resp.error)
		{
			$('#imei_overview').html(resp.error);
		}else
		{
			var html = '<table cellpadding="3" style="font-size: 12px">'
				html +=	'	<tr><td width="120"><b>Franchise</b></td><td><a target="_blank" href="'+site_url+'/admin/pnh_franchise/'+resp.det.franchise_id+'">'+resp.det.franchise_name+'</a></td></tr>'
				html +=	'	<tr><td><b>Product</b></td><td>'+resp.det.product_name+'</td></tr>'
				html +=	'	<tr><td><b>MemberID</b></td><td>'+resp.det.member_id+'</td></tr>'
				html +=	'	<tr><td><b>Invoiceno</b></td><td><a target="_blank" href="'+site_url+'/admin/invoice/'+resp.det.invoice_no+'">'+resp.det.invoice_no+'</a></td></tr>'
				html +=	'	<tr><td><b>TransID</b></td><td><a target="_blank" href="'+site_url+'/admin/trans/'+resp.det.transid+'">'+resp.det.transid+'</a>'+' - ('+resp.det.ordered_on+')'+'</td></tr>';
				html +=	'	<tr><td><b>Scheme Enabled</b></td><td>'+((resp.det.imei_scheme_id*1)?'Yes':'No')+'</td></tr>';
				html +=	'	<tr><td><b>Insurance Applicable</b></td><td>'+((resp.det.insurance_id*1)?'Yes':'No')+'</td></tr>';
				if(resp.det.imei_scheme_id!=0)
				{
					$('.insu').hide();
					html +=	'	<tr><td><b>Activation Credit</b></td><td>'+resp.det.imei_reimbursement_value_perunit+'</td></tr>';
					html +=	'	<tr><td><b>Status</b></td><td>'+((resp.det.is_imei_activated*1)?'<b style="color:#cd0000">Already Activated<b>':'<b style="color:green">Not Activated</b>')+'</td></tr>';	
				}
				if(resp.det.insurance_id!=null )
				{
					$('.insu').show();
					$('input[name="imei_hasinsurance"]').val('1');
					html +=	'	<tr><td><b>Insurancre Cost</b></td><td>'+resp.det.insurance_amount+'</td></tr>';
					html +=	'	<tr><td><b>Insurance</b></td><td>'+((resp.det.process_status*1)?'<b style="color:#cd0000">Already Activated<b>':'<b style="color:green">Not Activated</b>')+'</td></tr>';
				}
				html +=	'</table>';
			
			$('input[name="member_id"]').val(resp.det.member_id);
			$('input[name="franchise_id"]').val(resp.det.franchise_id);
			
			if(resp.det.is_imei_activated*1)
			{
				$('input[name="mobno"]').attr('disabled',true);
			}else
			{
				$('input[name="mobno"]').attr('disabled',false);
			}
			$('#imei_overview').html(html);
		}
	},'json');	
	 
});
//IMEI Activation related script--START
$('input[name="imei_mobno"]').change(function(){
	
	$('#new_memname_imei').hide();
	$('input[name="mem_name"]').val('');
	$('#actv_submit_btn').attr('disabled',true);
	$('#mobno_det').html('<td colspan="2" style="padding:0px;"><div id="mobno_overview" style="background: #ffffd0;padding:10px;color:#333">Loading...</div></td>');
	
	var mobno=$(this).val();
	var params = {fid:$('input[name="franchise_id"]').val(),mobno:$(this).val(),mid:$('input[name="member_id"]').val()}
		$.post(site_url+'/admin/jx_validate_mobno_imei',params,function(resp){
			if(resp.error != undefined)
			{
				$('#mobno_overview').html(resp.error);
			}else
			{
				
				var html = '';
					if(resp.member_id*1 != $('input[name="member_id"]').val() && (resp.member_name != undefined))
					{
						html = "<div>Mobileno "+mobno+" is already registered to "+resp.member_id+" </div>";
						
						if(resp.pen_ttl_actv)
						{
							html += "<div><br> Do you want to Activate IMEI to this mobile <input type=checkbox name='actv_confrim' value='1' > </div>";
							$('#actv_submit_btn').attr('disabled',false);
						}else
						{
							html += "<div><br> Activation Limit Ended for this MemberID</div>";
							$('#actv_submit_btn').attr('disabled',true);
						}
						
					}else
					{
						if(resp.pen_ttl_actv)
						{
							
							$('#new_memname_imei').show();
							$('#actv_submit_btn').attr('disabled',false);
						}else
						{
							html += "<div><br> Activation Limit Ended for this mobileno</div>";
							$('#actv_submit_btn').attr('disabled',true);
						}
					}
					
					if(html)
						$('#mobno_overview').html(html).show();
					else
						$('#mobno_overview').html('').hide();
			}
		},'json');
});

$('input[name="actv_confrim"]').live('change',function(){
	if($(this).attr('checked'))
	{
		$('#actv_submit_btn').attr('disabled',false);
	}else
	{
		$('#actv_submit_btn').attr('disabled',true);
	}
});
//IMEI Activation related script--END

$('input[name="nonskimei_mobno"]').change(function(){
	 $.post("<?=site_url("admin/jx_pnh_getmid")?>",{mid:$(this).val()},function(data){
			$("#mem_det").html(data).show();
			
		});
});

$('input[name="nonsk_imeino"]').change(function(){
	$.post(site_url+'/admin/jx_get_nonsk_imei_det','imeino='+$(this).val(),function(resp){
		if(resp.error)
		{
			$('#nonskimei_overview').html('<span class="error_msg">'+resp.error+'</span>');
			$('#nonskimei_btn').attr('disabled',true);
			
		}else
		{
			$('#nonskimei_overview').html(resp.success);
			$('#nonskimei_btn').attr('disabled',false);
		}
	},'json');
});

$('input[name="order_id"]').live('change',function(){

	if($("#non_imei_fid").val()=='')
	{
		alert("Please Select Franchise");
		return false;
	}
	
	$.post(site_url+'/admin/jx_check_valid_imeireplcmnt_order',{transid:$(this).val(),fid:$("#non_imei_fid").val()},function(resp){
			if(resp.status =='error')
			{
				$('#nonskimeireplcmnt_orderdet').html('<span class="error_msg">'+resp.msg+'</span>');
				$('#nonskimei_btn').attr('disabled',true);
			}else
			{
				var html = '<span style="font-size:10px;">';
				html +=	'	<b>Order Details</b><a target="_blank" href="'+site_url+'/admin/trans/'+resp.transdet.transid+'">'+resp.transdet.transid+' - ('+resp.transdet.ordererd_on+')'+' '+'Order Amount :'+resp.transdet.order_total+'</b>';
				html +=	'   </span';
				$('#nonskimei_btn').attr('disabled',false);
			}
			$('#nonskimeireplcmnt_orderdet').html(html);
		},'json');
	
});
</script>
<style>
ul
{
    list-style-type: none;
}
.fran_imeilabel
{
float: left;
    width: 41%;
}
.imei_inp
{
float: left;
text-align: left;
width: 25%;
}

.nonsk_imeiwrap{
float: left;
    margin: 17px 0;
    width: 53%;
}

.nonsk_imeidet{
float: left; width: 100%; margin: 5px 0px;
}
#mem_det
{
  margin:5px 2px;
  font-size: 10px;
    margin: 5px 2px;
    float:left;
}
.nonsk_imei_bloc
{
background-color: #FAFAFA;
}
</style>