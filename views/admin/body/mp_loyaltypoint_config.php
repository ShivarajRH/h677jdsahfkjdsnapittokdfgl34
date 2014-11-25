<style>
	.leftcont{ display: none; }
	.listview_tbl .pagination a{padding:5px 10px;background: #dfdfdf;color: #000;font-weight: bold;font-size: 13px;}
	
	.listview_tbl .page_topbar select { width: 200px; }
	.listview_tbl .total_overview { margin-right:27%;  }
	.listview_tbl .total_overview .inlog_1 {background-color: #B68362; color:#FFFFFF;
						float: left;
						font-size: 16px;
						padding: 5px;
						text-align: center;
						margin: 0 0 0 8px; }
	.listview_tbl .deals_display_log { margin: 0 0 8px 0; }
	.listview_tbl .deals_display_log .inlog_1 { background-color: #85658B; color:#FFFFFF;
						float: left;
						font-size: 16px;
/*						width: 25%; */
						padding: 5px;
						text-align: center;
						margin: 0 0 0 8px;}
	.listview_tbl .pagination { float: right; }
	/*===========< Default filters style - Shivarj >==============*/
	.filters_block .filter select { width:180px;margin-top:5px;font-size: 12px; }
	/*===========< END Default filters style - Shivarj >==============*/
	.ui-dialog .ui-dialog-titlebar .ui-dialog-title { text-transform:capitalize; }
	#dlg_add_lp_config select { width: 200px; }
	#dlg_add_lp_config .status_txt_msg {margin: 5px;padding: 5px;/* background-color: aliceblue; */color: blueviolet;  }
</style>
<div class="page_wrap container" style="width: 98%;">
	<h2 class="page_title">MP Loyalty Point Config</h2>
	<a class="add_lp_config fl_right button button-small button-action button-rounded" href="javascript:void(0);" onclick="return fn_add_lp_config(this,0);">Add Loyalty Brand & category</a>
	
	<!--Page content code ends -->
	<div id="lp_list" class="page_content listview_tbl">
		
		<div class="page_topbar" >
			<form onsubmit="return filter_form_submit();">
				<div class="filters_block">
					<!--<div class="page_action_buttonss fl_right" align="right">-->
					<div class="filter">
						<!--<input type="hidden" name="sel_menu" id="sel_menu" value="0"/>-->
						<label style="margin-right: 18px;">Menu :</label>  <?php //echo ($i==0) ? "selected":""; ?>
						<select name="sel_menu" id="sel_menu">
							<option value="0">All</option>
							<?php foreach($this->db->query("select * from pnh_menu order by name asc")->result_array() as $i=>$m){?>
										<option value="<?=$m['id']?>"><?=$m['name']?></option>
							<?php }?>
						</select>

						<label style="margin-right: 15px;">Brand :</label>
						<select name="sel_brandid">
							<option value="0">All</option>
							<?php
								foreach($brand_list as $brand)
								{ echo '<option value="'.$brand['id'].'">'.$brand['name'].'</option>'; }
							?>
						</select>

						<label style="margin-right: 0px;">Category :</label> <select name="sel_catid"><option value="">All</option>
							<?php
								foreach($cat_list as $cat)
								{ echo '<option value="'.$cat['id'].'">'.$cat['name'].'</option>'; }
							?>
						</select>

						<input type="submit" value="Go" class="button button-action button-tiny button-rounded" />
					</div>
				</div>
			</form>
			
		</div>
		<div class="clear">&nbsp;</div>
		<div class="pagination fl_right"></div>
		<!--<div class="total_overview fl_right"></div>-->
		<div class="deals_display_log fl_left"></div>
		
		<table class="datagrid" width="100%">
			<thead>
				<tr>
					<th width="2%">ID</th>
					<th width="6%">Created on</th>
					<th width="10%">Menu</th>
					<th width="10%">Brand</th>
					<th width="10%">Category</th>
					<th width="25%">Template</th>
					<th width="5%"><small>Loyalty Point is active?</small></th>
					<th width="8%">Created By</th>
					<th width="10%">Actions</th>
				</tr>
			</thead>
			<tbody align="center">
			</tbody>
		</table>
		<div class="pagination fl_right"></div>
	</div>
</div>
<!-- =======================< DIALOG UI CODE STARTS >========================== -->
<div style="display:none;">
	<div id="dlg_add_lp_config">
		<input type="hidden" name="dlg_configid" class="dlg_configid" value="0"/>
		<table width="100%">
			<tr>
				<td><label style="margin-right: 18px;">Menu <span class="required">*</span>:</label> </td>
				<td><!--<input type="hidden" name="dlg_sel_menu" id="dlg_sel_menu" value="0"/>--> <?php //echo ($i==0) ? "selected":""; ?>
					<select name="dlg_sel_menu" class="dlg_sel_menu">
						<option value="0">All</option>
						<?php foreach($this->db->query("select * from pnh_menu order by name asc")->result_array() as $i=>$m){?>
									<option value="<?=$m['id']?>"><?=$m['name']?></option>
						<?php }?>
					</select>
				</td>
			</tr>
			<tr>
				<td><label style="margin-right: 15px;">Brand <span class="required">*</span>:</label></td>
				<td>
					<select name="dlg_sel_brandid" class="dlg_sel_brandid">
						<option value="0">All</option>
						<?php
								foreach($brand_list as $brand)
								{ echo '<option value="'.$brand['id'].'">'.$brand['name'].'</option>'; }
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td><label style="margin-right: 0px;">Category <span class="required">*</span>:</label></td>
				<td>
					<select name="dlg_sel_catid" class="dlg_sel_catid"><option value="">All</option>
					   <?php
						   foreach($cat_list as $cat)
						   { echo '<option value="'.$cat['id'].'">'.$cat['name'].'</option>'; }
					   ?>
				   </select>
				</td>
			</tr>
			<tr>
				<td><label style="margin-right: 15px;">Note Template <span class="required">*</span>:</label></td>
				<td>
					<textarea name="dlg_note_template" class="dlg_note_template" cols="30" rows="7" style="" placeholder="Please enter sample template text."></textarea>
					<br>*Sample Text: You will get %diff% loyalty points.
				</td>
			</tr>
			<tr>
				<td><label style="margin-right: 15px;">Is Active <span class="required">*</span>:</label></td>
				<td>
					<select name="dlg_is_active" class="dlg_is_active">
						<option value="1">Yes</option>
						<option value="0">No</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center"><div class="status_txt_msg">&nbsp;</div></td>
			</tr>
			<tr>
				<td colspan="2" align="center"><small>Note: <span class="required">*</span> means required field.</small></td>
			</tr>
		</table>
		
	</div>
</div>
<script>
	
	$(document).ready(function() {
		load_lp_content("");
	});
	// ===============================<< FILTER CHANGE CODE START >>==============================================
	// =========< select menus >=================
	$('select[name="sel_menu"]').change(function(){
		var elt=$(this);
		var menuid = elt.val();
		var bid = $('select[name="sel_brandid"]').val();
		var catid = $('select[name="sel_catid"]').val();
		
		
		// brand list
			
			
			if(menuid) {
			
				var tar_elt = $('select[name="sel_brandid"]');
				
				tar_elt.html('<option value="0">Loading...</option>');
				
				$.getJSON(site_url+'/admin/jx_getbrandsbymenu/'+menuid,'',function(resp){
					var brandlist_html = '<option value="0">All</option>';
					if(resp.brand_list.length)
					{
						$.each(resp.brand_list,function(a,b){
							brandlist_html += '<option value="'+b.brandid+'">'+b.name+'</option>';
						});
					}
					else
					{
						tar_elt.html('<option value="0">No item found</option>');
					}
					tar_elt.html(brandlist_html);	
				});
			}
			
			
			
			
			
		// category list
		if(menuid) {
			var cat_elt = $('select[name="sel_catid"]');
			cat_elt.html('<option value="0">Loading...</option>');

			$.getJSON(site_url+'/admin/jx_getcatbybrand/'+bid+'/'+menuid,'',function(resp){
				var catlist_html = '<option value="0">All</option>';
				if(resp.cat_list.length)
				{
					$.each(resp.cat_list,function(a,b){
						catlist_html += '<option value="'+b.id+'">'+b.name+'</option>';
					});
				}
				else
				{
					cat_elt.html('<option value="0">No item found</option>');
				}
				cat_elt.html(catlist_html);	
			});
		}
		
		$(".updt_brand_margin").html('<a href="javascript:void(0)" onclick="mp_percentage_bulkupdate();" class="button button-tiny button-primary">Update Percentage Margin for Brand</a>');
		
	});//.trigger('change');
	
	// =========< Select Category >=================
	$('select[name="sel_catid"]').change(function() {
		var menuid = $('select[name="sel_menu"]').val()*1;
		var bid = $('select[name="sel_brandid"]').val()*1;
		var catid = $(this).val();
		
		if(bid == 0) {
			// get  brand list
			var tar_elt = $('select[name="sel_brandid"]');

			tar_elt.html('<option value="0">Loading...</option>');

			$.getJSON(site_url+'/admin/jx_getbrandsbymenu/'+menuid+'/'+ catid,'',function(resp){
				var brandlist_html = '<option value="0">All</option>';
				if(resp.brand_list.length)
				{
					$.each(resp.brand_list,function(a,b){
						brandlist_html += '<option value="'+b.brandid+'">'+b.name+'</option>';
					});
				}
				else
				{
					brandlist_html += '<option value="0">No item found</option>';
				}
				tar_elt.html(brandlist_html);	
			});
		}
		
	});
	
	// =========< select brands >=================
	$('select[name="sel_brandid"]').change(function(){
		var menuid = $('select[name="sel_menu"]').val()*1;
		var catid = $('select[name="sel_catid"]').val()*1;
		var bid = $(this).val();
		
		var cat_elt = $('select[name="sel_catid"]');
		
		// update category list
		if(catid==0) {
				cat_elt.html('<option value="0">Loading...</option>');
				$.getJSON(site_url+'/admin/jx_getcatbybrand/'+bid+"/"+menuid,'',function(resp){
					var catlist_html = '<option value="0">All</option>';
						if(resp.cat_list.length)
						{
							$.each(resp.cat_list,function(a,b){
								catlist_html += '<option value="'+b.id+'">'+b.name+'</option>';
							});
						}
						else
						{
							catlist_html += '<option value="0">No item found</option>';
						}
						cat_elt.html(catlist_html);
				});
				
		}

	});
	
	function filter_form_submit() {
		load_lp_content('');
		return false;
	}
	// ===============================<< FILTER CHANGE CODE END >>==============================================
	
	function load_lp_content(page_url) {
		var colcount=8;
		var mid = $('select[name="sel_menu"]').val()*1;
		var bid = $('select[name="sel_brandid"]').val()*1;
		var cid = $('select[name="sel_catid"]').val()*1;
		if(page_url=='')
			url=site_url+'/admin/mp_loyaltypoint_config_jx/'+mid+'/'+bid+'/'+cid+'/0';
		else
			url = page_url;
		$("#lp_list tbody").html('<tr><td colspan="'+colcount+'"><div class="loading">&nbsp;&nbsp;&nbsp;&nbsp;Loading...</div></td></tr>');
		$.post(url,page_url,function(resp){
			if(resp==null) {
				$("#lp_list tbody").html('<tr><td colspan="'+colcount+'"><div>No data received.</div></td></tr>');
			}
			else {
				//print(resp);
				if(resp.status=='success')
				{
					var content="";
					$.each(resp.lp_det,function(i,lp) {
						var sno=(++i) + (resp.pg*1);
						content +="<tr>\n\
										<td>"+sno+"</td>\n\
										<td align='left'>"+lp.created_on+"</td>\n\
										<td align='left'>"+lp.menu+"</td>\n\
										<td align='left'><a href='"+site_url+"/admin/viewbrand/"+lp.brandid+"'>"+lp.brand+"</a></td>\n\
										<td align='left'><a href='"+site_url+"/admin/viewcat/"+lp.catid+"'>"+lp.category+"</a></td>\n\
										<td align='left'>"+lp.offernote_tmpl+"</td>";
										if(lp.is_active=='1') {
											content +="<td>Yes</td>";
										}
										else {
											content +="<td>No</td>";
										}
										content +="<td align='left'>"+$.ucfirst(lp.username)+"</td>\n\
													<td align='left'><a class='add_lp_config button button-small button-action button-rounded' href='javascript:void(0);' onclick='return fn_add_lp_config(this,\""+lp.id+"\");'>Edit</a></td>\n\
									</tr>";
					});
					
					$("#lp_list tbody").html(content);
					//$('#lp_list .total_overview').html('<span class="inlog_1">Total deals <b>'+resp.ttl+'</b></span>');
					
					$('#lp_list .deals_display_log').html('');
					if(resp.pg_count_msg != undefined) {
						$('#lp_list .deals_display_log').html('<span class="inlog_1">'+resp.pg_count_msg+'</span>');
					}
					$('#lp_list .pagination').html(resp.pagination);
				}
				else {
					$("#lp_list tbody").html('<tr><td colspan="'+colcount+'"><div>'+resp.message+'</div></td></tr>');
				}
			}
		},'json');
		return false;
	}
	
	$('#lp_list .pagination a').live("click",function(e) {
		e.preventDefault();
		load_lp_content( $(this).attr("href") );
		return false;
	});
	//========================<< ADD CONFIG CODE START >>================================
	
	$("#dlg_add_lp_config").dialog({
		modal:true,autoOpen:false,height:"auto",width:550
		,open:function(event,i) {
			var dlg = $(this);
			var configid = dlg.data("configid");
			$('.status_txt_msg',dlg).text("");
			
			$(".ui-dialog-buttonpane button:contains('Submit')").button("enable");
				//.button(this.checked ? "enable" : "disable");
				//alert(configid);
			if(configid == "0") {
				//Disable
				$(".dlg_is_active",dlg).attr("disabled",true);
				
				var menuid = $('select[name="sel_menu"]').val()*1;
				var bid = $('select[name="sel_brandid"]').val()*1;
				var catid = $('select[name="sel_catid"]').val()*1;
				
				
				$(".dlg_sel_menu",dlg).val(menuid);
				$(".dlg_sel_brandid",dlg).val(bid);
				$(".dlg_sel_catid",dlg).val(catid);
				$(".dlg_note_template",dlg).text("");
				
				//$(".dlg_is_active",dlg).val(lp.is_active);
				
			}
			else {
				$(".dlg_configid",dlg).val(configid);
				
				
				//get data be id
				$.post(site_url+"/admin/mp_loyaltypoint_config_data_jx/"+configid,{},function(lpconfig) {
					if(lpconfig.status == 'success') {
						var lp = lpconfig.lp_config;
						
						$(".dlg_sel_menu",dlg).val(lp.menuid);
						$(".dlg_sel_brandid",dlg).val(lp.brandid);//.trigger('change');
						$(".dlg_sel_catid",dlg).val(lp.catid);
						
						//get_filter_menu_cat_brand_list(lp.menuid,lp.brandid,lp.catid);
						
						$(".dlg_note_template",dlg).text(lp.offernote_tmpl);
						$(".dlg_is_active",dlg).val(lp.is_active);
						//disable
						$(".dlg_is_active",dlg).attr("disabled",false);
					}
					
				},'json');
			}
			
		}
		,buttons:{
			Submit:function(e,j) {
				var dlg = $(this);
				//var trelt = dlg.data("trelt");
				
				var configid = $(".dlg_configid",dlg).val();
				var dlg_sel_menu = $(".dlg_sel_menu",dlg).val();
				var dlg_sel_brandid = $(".dlg_sel_brandid",dlg).val();
				var dlg_sel_catid = $(".dlg_sel_catid",dlg).val();
				var dlg_note_template = $(".dlg_note_template",dlg).val();
				var dlg_is_active = $(".dlg_is_active",dlg).val();
				
				//Disabled
				$(".dlg_is_active",dlg).attr("disabled",false);
				
				if(dlg_sel_menu == '0' || dlg_sel_brandid=='0' || dlg_sel_catid=='0' ) {
					alert("All required fields need to be set");
					return false;
				}
				if(dlg_note_template=='')
				{
					alert("Please enter Note Template.");
					return false;
				}
				
				$('.status_txt_msg',dlg).text("Updating...");
				
				$.post(site_url+'/admin/mp_loyaltypoint_config_update_jx/',
							{configid:configid,menuid:dlg_sel_menu,brandid:dlg_sel_brandid,catid:dlg_sel_catid,note_template:dlg_note_template,is_active:dlg_is_active},function(resp){
					if(resp.status=='success') {
						//$('.p_src_status',trelt).text(resp.pstatus);
						load_lp_content("");
						$('.status_txt_msg',dlg).text("Updated.");
						dlg.dialog("close");
					}
					else {
						$('.status_txt_msg',dlg).text(resp.message);
					}
				},'json');
			}
			,Cancel:function(d,k) {
				var dlg = $(this);
				$(".dlg_is_active",dlg).attr("disabled",true);
				$(this).dialog("close");
			}
		}

	});
	
	function fn_add_lp_config(e,configid) {
		$("#dlg_add_lp_config").data('configid',configid).dialog('open').dialog("option","title","Add Loyalty Point Config");
		return false;
	}
	//========================<< ADD CONFIG CODE START >>================================
	
	// ===============================<< DIALOGUE FILTER CHANGE CODE START >>==============================================
	// =========< select menus >=================
	$('select[name="dlg_sel_menu"]').change(function(){
		var elt=$(this);
		var menuid = elt.val();
		var bid = $('select[name="dlg_sel_brandid"]').val();
		var catid = $('select[name="dlg_sel_catid"]').val();
		
		
		// brand list
			
			
			if(menuid) {
			
				var tar_elt = $('select[name="dlg_sel_brandid"]');
				
				tar_elt.html('<option value="0">Loading...</option>');
				
				$.getJSON(site_url+'/admin/jx_getbrandsbymenu/'+menuid,'',function(resp){
					var brandlist_html = '<option value="0">All</option>';
					if(resp.brand_list.length)
					{
						$.each(resp.brand_list,function(a,b){
							brandlist_html += '<option value="'+b.brandid+'">'+b.name+'</option>';
						});
					}
					else
					{
						tar_elt.html('<option value="0">No item found</option>');
					}
					tar_elt.html(brandlist_html);	
				});
			}
			
			
			
			
			
		// category list
		if(menuid) {
			var cat_elt = $('select[name="dlg_sel_catid"]');
			cat_elt.html('<option value="0">Loading...</option>');

			$.getJSON(site_url+'/admin/jx_getcatbybrand/'+bid+'/'+menuid,'',function(resp){
				var catlist_html = '<option value="0">All</option>';
				if(resp.cat_list.length)
				{
					$.each(resp.cat_list,function(a,b){
						catlist_html += '<option value="'+b.id+'">'+b.name+'</option>';
					});
				}
				else
				{
					cat_elt.html('<option value="0">No item found</option>');
				}
				cat_elt.html(catlist_html);	
			});
		}
		
		//$(".updt_brand_margin").html('<a href="javascript:void(0)" onclick="mp_percentage_bulkupdate();" class="button button-tiny button-primary">Update Percentage Margin for Brand</a>');
		
	});//.trigger('change');
	// ===================< Select Category >=================
	$('select[name="dlg_sel_catid"]').change(function() {
		var menuid = $('select[name="dlg_sel_menu"]').val()*1;
		var bid = $('select[name="dlg_sel_brandid"]').val()*1;
		var catid = $(this).val();
		
		if(bid == 0) {
			// get  brand list
			var tar_elt = $('select[name="dlg_sel_brandid"]');

			tar_elt.html('<option value="0">Loading...</option>');

			$.getJSON(site_url+'/admin/jx_getbrandsbymenu/'+menuid+'/'+ catid,'',function(resp){
				var brandlist_html = '<option value="0">All</option>';
				if(resp.brand_list.length)
				{
					$.each(resp.brand_list,function(a,b){
						brandlist_html += '<option value="'+b.brandid+'">'+b.name+'</option>';
					});
				}
				else
				{
					brandlist_html += '<option value="0">No item found</option>';
				}
				tar_elt.html(brandlist_html);	
			});
		}
		
	});
	
	// =========< select brands >=================
	$('select[name="dlg_sel_brandid"]').change(function(){
		var menuid = $('select[name="dlg_sel_menu"]').val()*1;
		var catid = $('select[name="dlg_sel_catid"]').val()*1;
		var bid = $(this).val();
		
		var cat_elt = $('select[name="dlg_sel_catid"]');
		
		// update category list
		if(catid==0) {
				cat_elt.html('<option value="0">Loading...</option>');
				$.getJSON(site_url+'/admin/jx_getcatbybrand/'+bid+"/"+menuid,'',function(resp){
					var catlist_html = '<option value="0">All</option>';
						if(resp.cat_list.length)
						{
							$.each(resp.cat_list,function(a,b){
								catlist_html += '<option value="'+b.id+'">'+b.name+'</option>';
							});
						}
						else
						{
							catlist_html += '<option value="0">No item found</option>';
						}
						cat_elt.html(catlist_html);
				});
		}

	});
	// =========< select category >=================
	$('select[name="dlg_sel_catid"]').change(function() {
		var menuid = $('select[name="dlg_sel_menu"]').val()*1;
		var bid = $('select[name="dlg_sel_brandid"]').val()*1;
		var configid = $('select[name="dlg_configid"]').val();
		var catid = $(this).val();
		var dlg=$("#dlg_add_lp_config");
		
		$.post(site_url+"/admin/mp_loyaltypoint_config_data_jx/0",{menuid:menuid,brandid:bid,catid:catid},function(lpconfig) {
			if(lpconfig.status == 'success') {
				var lp = lpconfig.lp_config;
				//print(lp);
//				$(".dlg_sel_menu",dlg).val(lp.menuid);
//				$(".dlg_sel_brandid",dlg).val(lp.brandid);
//				$(".dlg_sel_catid",dlg).val(lp.catid);
				$(".dlg_note_template",dlg).text(lp.offernote_tmpl);
				$(".dlg_is_active",dlg).val(lp.is_active);
				//disable
				$(".dlg_is_active",dlg).attr("disabled",false);
				//message
				$('.status_txt_msg',dlg).text("Already configured.");
				
				
				
				
				if(configid=='0') {
					$(".ui-dialog-buttonpane button:contains('Submit')").button("disable");
					//.button(this.checked ? "enable" : "disable");
					$('.status_txt_msg',dlg).text("Already configured.");
//					//$('select[name="dlg_sel_catid"]').trigger("change");
				}
				else {
					$(".ui-dialog-buttonpane button:contains('Submit')").button("enable");
					//.button(this.checked ? "enable" : "disable");
					$('.status_txt_msg',dlg).text("");
//					//$('select[name="dlg_sel_brandid"]').trigger("change");
					//$('select[name="dlg_sel_menu"]').trigger("change");
				}
			}
			else {
				$(".dlg_note_template",dlg).text("");
				$(".ui-dialog-buttonpane button:contains('Submit')").button("enable");
				$('.status_txt_msg',dlg).text("");
			}
			
			setTimeout(function() {//print("TEST");
				$('.status_txt_msg',dlg).delay("5000").html("");
			},20000);

		},'json');
	});
	// ===============================<< DIALOGUE FILTER CHANGE CODE END >>==============================================
</script>
