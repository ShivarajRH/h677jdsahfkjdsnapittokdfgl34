<html>
	<head>
		<title><?php echo $title?></title>
		<style>
			*{margin:0px;padding:0px}
			body{
				font-family: arial;
				font-size: 12px;
				padding:10px;
				width: 970px;
				margin:0px auto;
				background: #FAFAFA;
			}
			h2{margin:7px 0px;}
			
			.api_list{
				line-height: 16px;
			}
			.api_list .api_det{border-bottom:3px dotted #FFF;background: #F3F3F3;
				padding:5px 10px;}
			.api_list .api_det h4{
				margin:3px 0px;
				font-weight: bold;
				font-size: 13px;
			}
			
			.api_list .api_det code{
				background: #fcfcfc;
			}
			
			.api_list .api_det p{
				margin:7px 0px;
				font-size: 96%;
			}
		</style>
	</head>
	<body>
		<h2>Storeking Api Documentation</h2>
		
		<div class="api_list">
			<div class="api_det">
				<h4>API documentation URL</h4>
				<code>
					<?php echo site_url('api/doc')?>
				</code>
				<p class="api_desc">
					Request to view view api documentation 
				</p>
			</div>
			
			<div class="api_det">
				<h4>Access Login Authentication </h4>
				<code>
					<?php echo site_url('api/login')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : username=<b>USERNAME</b>&amp;password=<b>PASSWORD</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response </b> : {"status":"success",'auth':"<b>AUTHKEY</b>"}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2000","error_msg","Invalid Login Details Provided"}
				</code>
				<p class="api_desc">
					Request to process login authentication  
				</p>
			</div>
			
			<div class="api_det">
				<h4>get user details by authkey </h4>
				<code>
					<?php echo site_url('api/userdet')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2001","error_msg","Invalid AuthKEY"}
				</code>
				<p class="api_desc">
					Request to process login authentication  
				</p>
			</div>
			
			<div class="api_det">
				<h4>Get franchise info  </h4>
				<code>
					<?php echo site_url('api/get_franchise_info')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISE ID</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2003","error_msg","No data found"}
				</code>
				<p class="api_desc">
					Request to process login authentication  
				</p>
			</div>
			
			<div class="api_det">
				<h4>Get franchise menu list  </h4>
				<code>
					<?php echo site_url('api/get_franchise_menus')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISE ID</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2003","error_msg","No data found"}
				</code>
				<p class="api_desc">
					Request to process login authentication  
				</p>
			</div>
			
			<div class="api_det">
				<h4>Get brands list</h4>
				<code>
					<?php echo site_url('api/get_brand_list')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : menu_id=<b>MENU ID</b>
					<br><b>Params</b> : cat_id=<b>CATEGORY ID</b>
					<br><b>Params</b> : start=<b>RECORT START FROM</b>
					<br><b>Params</b> : LIMIT=<b>HOW MANY RECORDS</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2003","error_msg","No data found"}
				</code>
				<p class="api_desc">
					Request to get the brands list,it also support two combination of filters 1.menu by brand,2.category by brand,if no records found returns empty index of response array.
				</p>
			</div>
			
			<div class="api_det">
				<h4>Get category list</h4>
				<code>
					<?php echo site_url('api/get_category_list')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : menu_id=<b>MENU ID</b>
					<br><b>Params</b> : brand_id=<b>BRAND ID</b>
					<br><b>Params</b> : start=<b>RECORT START FROM</b>
					<br><b>Params</b> : LIMIT=<b>HOW MANY RECORDS</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2003","error_msg","No data found"}
				</code>
				<p class="api_desc">
					Request to get the category list,it also support two combination of filters 1.menu by category,2.brand by category.if no records found returns empty index of response array.
				</p>
			</div>
			
			<div class="api_det">
				<h4>Get deal list</h4>
				<code>
					<?php echo site_url('api/get_deal_list')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : menu_id=<b>MENU ID</b>
					<br><b>Params</b> : brand_id=<b>BRAND ID</b>
					<br><b>Params</b> : category_id=<b>CATEGORY ID</b>
					<br><b>Params</b> : start=<b>RECORT START FROM</b>
					<br><b>Params</b> : LIMIT=<b>HOW MANY RECORDS</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2003","error_msg","No data found"}
				</code>
				<p class="api_desc">
					Request to get the deal list,it also support two combination of filters 1.menu by category,2.brand by category.if no records found returns empty index of response array.
				</p>
			</div>
			
			<div class="api_det">
				<h4>Get deal info</h4>
				<code>
					<?php echo site_url('api/get_deal_info')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : pid=<b>PRODUCT ID</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2003","error_msg","No data found"}
				</code>
				<p class="api_desc">
					Request to get the deal info.if no records found returns empty index of response array.
				</p>
			</div>
			
			<div class="api_det">
				<h4>Add the item to cart</h4>
				<code>
					<?php echo site_url('api/cart_add')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : pid=<b>PRODUCT ID</b>
					<br><b>Params</b> : qty=<b>QTY</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISE ID</b>
					<br><b>Params</b> : attributes=<b>attributes[if any attributes for selected ,attributes parameter in the form of array,array value is ATTRIBUTE_ID:ATTRIBUTE_VALUE_ID EX:attr[0]=158:165]</b>
					<br><b>Params</b> : multiple=<b>MULTIPLE FLAG</b>
					<br><b>Params</b> : cart_items=<b>This api support the multiple items to add a cart,so gives the input in the form multi dimentional array EX:[array(0=>array(pid=>123,qty=>5,attributes=>array(0=>ATTRIBUTE_ID:ATTRIBUTE_VALUE_ID)))],if no attribute exist so give attribute index to empty array and no need of parameter for pid,qty,attributes and want set flag multiple to 1</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2004","error_msg","Sorry! Please try again"}
				</code>
				<p class="api_desc">
					To add the item to cart.
				</p>
			</div>
			
			<div class="api_det">
				<h4>Remove the item from cart</h4>
				<code>
					<?php echo site_url('api/cart_remove')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : pid=<b>PRODUCT ID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISE ID</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2004","error_msg","Sorry! Please try again"}
				</code>
				<p class="api_desc">
					Remove the ite from cart
				</p>
			</div>
			
			<div class="api_det">
				<h4>Get the cart item list</h4>
				<code>
					<?php echo site_url('api/get_cart_item_list')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISE ID</b>
					<br><b>Params</b> : pid=<b>PID</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2005","error_msg","No more items in cart"}
				</code>
				<p class="api_desc">
					Get the deal list in the cart,and also it support pid wise list.if no records found returns empty index of response array.
				</p>
			</div>
			
			<div class="api_det">
				<h4>Update the cart item</h4>
				<code>
					<?php echo site_url('api/cart_update')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : pid=<b>PRODUCT ID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISE ID</b>
					<br><b>Params</b> : attributes=<b>attributes[if any attributes for selected ,attribures parameter in the form of array,array value is ATTRIBUTE_ID:ATTRIBUTE_VALUE_ID EX:attr[0]=158:165]</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2006","error_msg","Cart item not updated"}
				</code>
				<p class="api_desc">
					To update the cart items
				</p>
			</div>
			
			<div class="api_det">
				<h4>Get member info</h4>
				<code>
					<?php echo site_url('api/get_member_info')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : member_id=<b>Member id</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISE ID</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2007","error_msg","No member available for given member id"}
				</code>
				<p class="api_desc">
					To get the details about member for given member id
				</p>
			</div>
			
			<div class="api_det">
				<h4>Get member info</h4>
				<code>
					<?php echo site_url('api/add_member')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISE ID</b>
					<br><b>Params</b> : mobile_no=<b>MOBILENO</b>
					<br><b>Params</b> : member_name=<b>MEMBER NAME</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2008","error_msg","Mobile no already registered"}
				</code>
				<p class="api_desc">
					To add the new member
				</p>
			</div>
			
			<div class="api_det">
				<h4>To validate the cart item</h4>
				<code>
					<?php echo site_url('api/validate_cart_items')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISE ID</b>
					<br><b>Params</b> : validation_type=<b>VALIDATION TYPE FLAG</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2009","error_msg","No validation type found"}
				</code>
				<p class="api_desc">
					This api for validate the loged franchise cart items by given validation type,this api validate and given if it no error the return true else given validation error in the format of array
					it support n numbers of validation by type,
					Type 1:if validation type set 1 then this api validate the member and electronics items qty validation.
				</p>
			</div>
			
			<div class="api_det">
				<h4>Get member by mobile number </h4>
				<code>
					<?php echo site_url('api/get_member_by_mobileno')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISE ID</b>
					<br><b>Params</b> : mobile_no=<b>MOBILE NO</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2010","error_msg","No member found for given mobile number"}
				</code>
				<p class="api_desc">
					get the memeber by mobile number.
				</p>
			</div>
			
			<div class="api_det">
				<h4>Get deals by searching </h4>
				<code>
					<?php echo site_url('api/search')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISE ID</b>
					<br><b>Params</b> : start=<b>RECORT START FROM</b>
					<br><b>Params</b> : limit=<b>RESULT LIMIT</b>
					<br><b>Params</b> : search_data=<b>SEARCHED KEYWORD</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2015","error_msg","Please enter search keyword"}
				</code>
				<p class="api_desc">
					get the memeber by mobile number.if no records found returns empty index of response array.
				</p>
			</div>
			
			<div class="api_det">
				<h4>to placing order</h4>
				<code>
					<?php echo site_url('api/place_order')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISE ID</b>
					<br><b>Params</b> : member_name=<b>MEMBER NAME [optional:if a new member]</b>
					<br><b>Params</b> : mobile_no=<b>MEMBER MOBILE NUMBER[if a new member order then mobile number must be require]</b>
					<br><b>Params</b> : member_id=<b>MEMBER ID[if already exist member]</b>
					<br><b>Params</b> : mem_entry_type=<b>Entered member type want to set flag,if new member set to 1 or already exist member then set to 0</b>
					<br><b>Params</b> :is_voucher_order=<b>if order placing by a voucher then this flag to set 1 else 0</b>
					<br><b>Params</b> :voucher_code=<b>if is_voucher_order flag set then voucher codes require</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2015","error_msg","Please enter search keyword"}
				</code>
				<p class="api_desc">
					this api to help a placing order for loged franchise cart items.
				</p>
			</div>
			
			<div class="api_det">
				<h4>Get transaction list </h4>
				<code>
					<?php echo site_url('api/get_transaction_list')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISE ID</b>
					<br><b>Params</b> : start=<b>RECORT START FROM</b>
					<br><b>Params</b> : limit=<b>RESULT LIMIT</b>
					<br><b>Params</b> : date_from=<b>DATE FROM[YY-MM-DD]</b>
					<br><b>Params</b> : date_to=<b>DATE TO[YY-MM-DD]</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2016","error_msg","Franchise id not found"}
				</code>
				<p class="api_desc">
					get transaction list by franchise
				</p>
			</div>
			
			<div class="api_det">
				<h4>Get franchise payment receipt details</h4>
				<code>
					<?php echo site_url('api/get_franchise_receipts')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISE ID</b>
					<br><b>Params</b> : start=<b>RECORT START FROM</b>
					<br><b>Params</b> : limit=<b>RESULT LIMIT</b>
					<br><b>Params</b> : RECEIPT_TYPE=<b>RECEIPT TYPE[types:in_process,processed,realized,cancelled]</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2016","error_msg","Franchise id not found"}
				</code>
				<p class="api_desc">
					get franchise payment receipt details,defaultly all the receipts record limit 10,want more receipt then give the which receipt[type] and record start from [start] and record limit [limit].
				</p>
			</div>
			
			<div class="api_det">
				<h4>Get voucher details by given member id and voucher code</h4>
				<code>
					<?php echo site_url('api/get_voucher_details')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISE ID</b>
					<br><b>Params</b> : member_id=<b>MEMBER ID</b>
					<br><b>Params</b> : vouchers_code=<b>VOUCHER CODE,VOUCHER CODE</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2018","error_msg","Member id and voucher code must be require"}
				</code>
				<p class="api_desc">
					get voucher details by given member id and voucher code
				</p>
			</div>
			
			<div class="api_det">
				<h4>Get imei details</h4>
				<code>
					<?php echo site_url('api/get_imei_details')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISE ID</b>
					<br><b>Params</b> : imei_no=<b>IMEI NO</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2031","error_msg","IMEINO Details not found"}
				</code>
				<p class="api_desc">
					get the imei details by given imei number
				</p>
			</div>
			
			<div class="api_det">
				<h4>Validate imei activation form mobile number</h4>
				<code>
					<?php echo site_url('api/validate_imei_mobileno')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISE ID</b>
					<br><b>Params</b> : mobile_no=<b>MOBILE NO</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2032","error_msg","Invalid Mobileno entered"}
				</code>
				<p class="api_desc">
					This api used to validate the imei activation form mobile nummber,it returns member id and status flag 1 for if member exist for given number other wise member id empty and status flag 2 
				</p>
			</div>
			
			<div class="api_det">
				<h4>IMEI activation to member</h4>
				<code>
					<?php echo site_url('api/franchise_imei_activation')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISE ID</b>
					<br><b>Params</b> : mobile_no=<b>MOBILE NO</b>
					<br><b>Params</b> : imei_no=<b>IMEI NUMBER</b>
					<br><b>Params</b> : actv_confrim=<b>ACTIVATION CONFIRMATION FLAG[if activation require to purchased member then set flag to 1 or Activation require to another existing member then set flag to 1,if new member then no need of flag ]</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2036","error_msg","Sorry not able to process IMEI activation"}
				</code>
				<p class="api_desc">
					This api used to activate the IMEI number to member 
				</p>
			</div>
			
			<div class="api_det">
				<h4>Posting complaints</h4>
				<code>
					<?php echo site_url('api/post_complaints')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISE ID</b>
					<br><b>Params</b> : complaint_name=<b>COMPLAINT NAME</b>
					<br><b>Params</b> : complaint=<b>COMPLAINT</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2038","error_msg","Your complaint not posted"}
				</code>
				<p class="api_desc">
					Api used to post the complaints 
				</p>
			</div>
			
			<div class="api_det">
				<h4>get offer list</h4>
				<code>
					<?php echo site_url('api/get_offer_list')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISE ID</b>
					<br><b>Params</b> : start=<b>RECORD START</b>
					<br><b>Params</b> : LIMIT=<b>LIMIT</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2039","error_msg","FranchiseID require"}
				</code>
				<p class="api_desc">
					Api used to get the offers details,if no records found the offer list returns empty
				</p>
			</div>
			
			<div class="api_det">
				<h4>get return list</h4>
				<code>
					<?php echo site_url('api/get_returns_details')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISE ID</b>
					<br><b>Params</b> : start=<b>RECORD START</b>
					<br><b>Params</b> : LIMIT=<b>LIMIT</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2040","error_msg","FranchiseID require"}
				</code>
				<p class="api_desc">
					Api used to get the return product details,if no records found the return list returns empty
				</p>
			</div>
			
			<div class="api_det">
				<h4>get the updated product details by version</h4>
				<code>
					<?php echo site_url('api/check_updates')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISE ID</b>
					<br><b>Params</b> : verion=<b>VERSION</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2045","error_msg","Given version details not found"}
				</code>
				<p class="api_desc">
					Api used to get the updated product details,if no records found then returns  empty array
				</p>
			</div>
						
			
		</div>
		
	</body>
	
</html>
