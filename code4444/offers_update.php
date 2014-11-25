<?php
/**
 * @author Shivaraj <shivaraj@storeking.in>_Oct_03_2014
 */
?>
<div class="container">
	<h2>Offers update form</h2>
	
	<div align="left" style="float: left;">
		<form onsubmit="return fn_get_trans_det(this);" action="<?php echo site_url('admin/offers_update_process');?>" method="post" data-validate="parsley">
			<table width= "100%">
				<tr>
					<td><div class="fran_imeilabel"><b>Trans. ID :</b><span class="red_star">*</span></div></td>
					<td>
						<div class="imei_inp"><input type="text" style="width:200px;" name="transid" class="transid" data-required="true" value="<?=$transid;?>" ></div>
						<div style="float:left;font-size: 10px;" id="memreplcmnt_orderdet"></div>
					</td>
					<td>
						<input type="submit" value="Submit">
					</td>
				</tr>
				
			</table>
				
		</form>	
				
			<?php /*	<tr>
					<td><div class="fran_imeilabel"><b>Mobileno :</b> <span class="red_star">*</span></div>	</td>
					<td><input maxlength="10" type="text" style="width: 200px;" value="<?php echo set_value('mem_mobno');?>" name="insurance[mem_mobno]" data-required="true" >
						<span  id="mem_det"></span></td>
				</tr>
				<tr>
					<td><div class="fran_imeilabel"><b>Name :</b> <span class="red_star">*</span></div>	</td>
					<td><input type="text" style="width: 200px;" value="<?php echo set_value('mem_memname');?>" name="insurance[memname]" data-required="true" >
						<span  id="mem_det"></span></td>
				</tr>
				<tr>
					<td><div class="fran_imeilabel"><b>Deal :</b><span class="red_star">*</span></div>	</td>
					<td><input type="text" name="ec_dealname" id="ec_deal_search" style="width: 200px;" autocomplete="off">
							<div id="ecdeal_results"></div></td>
				</tr>
				<tr>
					<td><div class="fran_imeilabel"><b>Receipt No :</b><span class="red_star">*</span></div>	</td>
					<td><input  type="text" style="width: 200px;" value="<?php echo set_value('mem_fran_receiptno');?>" name="insurance[mem_fran_receiptno]" data-required="true" autocomplete="off"></td>
				</tr>
				<tr>
					<td><div class="fran_imeilabel"><b>Amount :</b> <span class="red_star">*</span></div></td>
					<td><input  type="text" style="width: 200px;" value="<?php echo set_value('mem_fran_receiptamt');?>" name="insurance[mem_fran_receiptamt]" data-required="true" autocomplete="off"></td>
				</tr>
				<tr>
					<td><div class="fran_imeilabel"><b>Date :</b> <span class="red_star">*</span></div></td>
					<td><input type="text" style="width: 200px;" value="<?php echo set_value('mem_receipt_date');?>" name="insurance[mem_receipt_date]" data-required="true" ></td>
				</tr>
				<tr>
					<td><div class="fran_imeilabel"><b>Proof Type :</b><span class="red_star">*</span></div></td>
					<td>
						<div class="imei_inp"><select name="insurance[prooftype]">
								<option value="">Select</option>
								<?php $insurance_types=$this->db->query("select * from insurance_m_types order by name asc")->result_array();
										if($insurance_types){
										foreach($insurance_types as $i_type){
								?>
										<option value="<?php echo $i_type['id']?>"><?php echo $i_type['name']?></option>
								<?php }}?>
								</select>
						</div>
					</td>
				</tr>
				<tr>
					<td><div class="fran_imeilabel"><b>Proof ID :</b><span class="red_star">*</span></div></td>
					<td><input  type="text" style="width: 200px;" value="<?php echo set_value('mem_fran_proofid');?>" name="insurance[mem_fran_proofid]" data-required="true" autocomplete="off" ></td>
				</tr>
				<tr>
					<td><div class="fran_imeilabel"><b>Address :</b> <span class="red_star">*</span></div>	</td>
					<td><textarea  style="width: 200px;" value="<?php echo set_value('mem_proof_add');?>" name="insurance[mem_proof_add]" data-required="true" ></textarea></td>
				</tr>
				<tr>
					<td><div class="fran_imeilabel"><b>City :</b> <span class="red_star">*</span></div></td>
					<td><input type="text"  style="width: 200px;" value="<?php echo set_value('mem_proof_city');?>" name="insurance[mem_proof_city]" data-required="true" ></td>
				</tr>
				<tr>
					<td><div class="fran_imeilabel"><b>Pincode :</b> <span class="red_star">*</span></div></td>
					<td><div class="imei_inp">
							<input type="text"  style="width: 200px;" value="<?php echo set_value('mem_proof_pincode');?>" name="insurance[mem_proof_pincode]" data-required="true" >
						</div></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td></td>
				</tr>
				<tr>
					<td></td>
					<td><div class="nonsk_imeiwrap" style="">	
							<input class="button button-flat-royal button-small button-rounded" type="submit" value="Submit" id="mem_btn">
						</div></td>
				</tr>*/?>
			
	</div>
</div>
<script>
	function fn_get_trans_det(elt)
	{
		var frm=$(elt);
		var transid=$(".transid",frm).val();
		if(transid=='') { 
			alert("Please enter transaction id."); return false; 
		}
		$.post(site_url+"/admin/member_offer_trans_det_jx/"+transid,{},function(resp){
			if(resp.status=='success')
			{
				//$
			}
			else
			{
				alert("Error: "+resp.message);
			}
		},'json');
		return false;
	}
</script>
<?php

#=========================================================
	/**
	 * Function to created insurance offers for the transaction orders
	 * @author Shivaraj<shivaraj@storeking.in>_Sep_22_2014
	 * @param mixed $transid
	 */
	function offers_update($transid)
	{
		if($_POST)
		{
			$member_id=$this->input->post("member_id");
			$opted_insurance=$this->input->post("opted_insurance");
			$is_tmp_order=0;
			
			// Check member is newly registered member by member orders
			$data['new_member'] = $this->erpm->chk_is_new_member($member_id,$is_tmp_order);
			$data['member_id'] = $member_id;
			$data['transid'] = $transid;
			$data['opted_insurance'] = $opted_insurance;
					
		}
		else {
			show_error("No inputs are set.");
		}
		
		$data['page']='offers_update';
		$this->load->view("admin",$data);
	}
	
	function offers_update_process()
	{
		$fields_list="tr.amount,tr.order_for,f.is_lc_store
												,o.id,o.itemid,o.brandid,o.bill_person,o.quantity,o.status,o.shipped,o.time,o.i_orgprice AS mrp,o.i_price AS price,o.is_memberprice,o.mp_logid";
				$insu_res=$this->db->query("SELECT * FROM (
												SELECT di.pnh_id,di.has_insurance
												FROM king_orders o
												JOIN king_transactions tr ON tr.transid=o.transid
												JOIN king_dealitems di ON di.id=o.itemid
												JOIN pnh_m_franchise_info f ON f.franchise_id=tr.franchise_id
												WHERE tr.is_pnh=1 
														AND (o.insurance_id IS NULL OR o.insurance_id ='')
														AND tr.transid=?
											 )
											 AS g
											 WHERE g.has_insurance=1
									",$transid);

				// no insurance products found
				if( $insu_res->num_rows() )
				{
					$insu=$insu_res->result_array();

					$insurance['transid']=$transid;
					$insurance['opted_insurance']=1;
					$insurance['new_member']=0;

		//			$insurance['proof_type']$insurance['proof_name'];$insurance['fid'];$insurance['mid']$insurance['offer_type']$insurance['proof_id']$insurance['proof_address']$insurance['opted_insurance']
		//						$insurance["first_name"]$insurance["last_name"]$insurance["mob_no"]$insurance["city"]$insurance["pincode"]$insurance['created_by']$insurance['mem_fee_applicable']$insurance['pnh_member_fee']

					foreach($insu as $row)
					{
						$pnhid=$row['pnh_id'];

						$dealids[]=$pnhid;


						$prod=$this->db->query("SELECT i.*,d.publish,c.loyality_pntvalue,d.menuid 
													FROM king_dealitems i 
													JOIN king_deals d ON d.dealid=i.dealid 
													JOIN pnh_menu c ON c.id = d.menuid 
													WHERE i.is_pnh=1 AND i.pnh_id=? AND i.pnh_id!=0",$pnhid)->row_array();

						
						
						if($prod['is_lc_store'])
							$price=$prod['store_price'];
						else
							$price=$prod['price'];

						$insurance['menuids'][$pnhid]=$prod['menuid'];
						$insurance['order_value'][$pnhid]=$price;

					}

					$insurance['insurance_deals']=implode(',',$dealids);
					//$insu_id = $this->erpm->process_insurance_details($insurance,$is_tmp_order);
					echo '<pre>';
					print_r($insurance);
					die();

			}
			else
			{
				show_error("Insurance products not found OR already insurance processed");
			}
	}
	
	function member_offer_trans_det_jx($transid)
	{
		$insu_res=$this->db->query("SELECT * FROM (
												SELECT di.pnh_id,di.has_insurance
												FROM king_orders o
												JOIN king_transactions tr ON tr.transid=o.transid
												JOIN king_dealitems di ON di.id=o.itemid
												JOIN pnh_m_franchise_info f ON f.franchise_id=tr.franchise_id
												WHERE tr.is_pnh=1 
														AND (o.insurance_id IS NULL OR o.insurance_id ='')
														AND tr.transid=?
											 )
											 AS g
											 WHERE g.has_insurance=1
									",$transid);
		
		// no insurance products found
			if( $insu_res->num_rows() )
			{
				$insu=$insu_res->result_array();

				$insurance['transid']=$transid;
				$insurance['opted_insurance']=1;
				$insurance['new_member']=0;

	//			$insurance['proof_type']$insurance['proof_name'];$insurance['fid'];$insurance['mid']$insurance['offer_type']$insurance['proof_id']$insurance['proof_address']$insurance['opted_insurance']
	//						$insurance["first_name"]$insurance["last_name"]$insurance["mob_no"]$insurance["city"]$insurance["pincode"]$insurance['created_by']$insurance['mem_fee_applicable']$insurance['pnh_member_fee']

				foreach($insu as $row)
				{
					$pnhid=$row['pnh_id'];

					$dealids[]=$pnhid;


					$prod=$this->db->query("SELECT i.*,d.publish,c.loyality_pntvalue,d.menuid 
												FROM king_dealitems i 
												JOIN king_deals d ON d.dealid=i.dealid 
												JOIN pnh_menu c ON c.id = d.menuid 
												WHERE i.is_pnh=1 AND i.pnh_id=? AND i.pnh_id!=0",$pnhid)->row_array();
					$insurance_list[]=$prod;
				}
				$rslt_arr['insurance_list']=$insurance_list;
				output_data($rslt_arr);
			}
			else
			{
				output_error("No insurance deals found.");
			}
	}
	#=========================================================
	
	
	
	//		else {
		?>
<!--			<form name="offers_update_form" action="<?=site_url('admin/offers_update/'.$order['transid']);?>" method="post" target="_blank">
				<input type="text" name="new_member" value="0">
				<input type="text" name="member_id" value="<?=$o['member_id'];?>">
				<input type="text" name="opted_insurance" value="1">
				<input type="submit" class="button button-tiny button-rounded button-action" value="Generate Insurance">
			</form>-->
		<?php
//		}
?>