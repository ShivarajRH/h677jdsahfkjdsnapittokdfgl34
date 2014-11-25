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
		<h2>Storeking Franchisee Mobile APP API Documentation</h2>
		
		<div class="api_list">
			<!-- ===============< API LIST BLOCK START >=========-->
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
					<br><b>Params</b> : username=<b>USERNAME</b>&amp;password=<b>PASSWORD</b> {mandatory}
					<br><b>Response Type </b> : JSON
					<br><b>Response </b> : {"status":"success",'auth':"<b>AUTHKEY</b>"}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2000","error_msg","Invalid Login Details Provided"}
				</code>
				<p class="api_desc">
					Request to process login authentication  
				</p>
			</div>
			
			<div class="api_det">
				<h4>Forgot Password </h4>
				<code>
					<?php echo site_url('api/forgot_pwd')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : username=<b>{FRANCHISEE_MOBILE_NUMBER}</b>   {mandatory}
					<br><b>Response Type </b> : JSON
					<br><b>Response </b> : {"status":"success","response":"Generated new password has been sent to your registered mobile number -Storeking"}
					<br><b>Response On Error </b> : {'status':"error","error_code":"2000","error_msg":"Invalid Username entered\n"}
				</code>
				<p class="api_desc">
					Request to handle forgot password - sms will be sent with password
				</p>
			</div>
			
			<div class="api_det">
				<h4>get user details by authkey </h4>
				<code>
					<?php echo site_url('api/userdet')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>     {mandatory}
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"authkey":"f462ffc86611cfa9c46dd12e388195b7","user_id":{"user_id":"1","username":"9902821315","type":"0"}}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2001","error_msg","Invalid AuthKEY"}
				</code>
				<p class="api_desc">
					Request to login details
				</p>
			</div>
			
			<div class="api_det">
				<h4>Get franchise info  </h4>
				<code>
					<?php echo site_url('api/get_franchise_info')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>   {mandatory}
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"franchise_details":{"franchise_id":"498","franchise_name":"Testing Franchise","contact_no":"9844772645","territory":"Bangalore Rural","town_name":"Ramnagara","is_prepaid":"0","menus":[{"id":"118","menu":"Computers & Peripherals"},{"id":"123","menu":"Footwears"},{"id":"104","menu":"Baby Care"},{"id":"105","menu":"Bags"},{"id":"103","menu":"Belts"},{"id":"122","menu":"Cameras & Accessories"},{"id":"113","menu":"Car & Auto Care"},{"id":"120","menu":"Chocolates & Snacks"},{"id":"132","menu":"Clocks"},{"id":"102","menu":"Health Care and Monitors"},{"id":"117","menu":"Home Appliances"},{"id":"133","menu":"Home Care & Cleaning"},{"id":"121","menu":"Jewellery"},{"id":"111","menu":"Kitchen appliances"},{"id":"114","menu":"Mens Clothing & Apparels"},{"id":"127","menu":"Mobile Accessories"},{"id":"101","menu":"Nutrition and Diet"},{"id":"134","menu":"Offers"},{"id":"129","menu":"Perfumes"},{"id":"128","menu":"Personal grooming"},{"id":"109","menu":"Pet Care"},{"id":"107","menu":"Sports & Fitness"},{"id":"110","menu":"Stationary & Office Supplies"},{"id":"106","menu":"Sunglasses"},{"id":"108","menu":"Toys"},{"id":"130","menu":"Travel and Luggage"},{"id":"116","menu":"TV, Audio, Video & Gaming"},{"id":"131","menu":"Wallets"},{"id":"126","menu":"Watches"},{"id":"119","menu":"Womens Clothing & Apparels"},{"id":"100","menu":"Beauty & Cosmetics"},{"id":"112","menu":"Mobiles & Tablets"},{"id":"115","menu":"Musical Insturments"},{"id":"118","menu":"Computers & Peripherals"},{"id":"123","menu":"Footwears"},{"id":"104","menu":"Baby Care"},{"id":"105","menu":"Bags"},{"id":"103","menu":"Belts"},{"id":"122","menu":"Cameras & Accessories"},{"id":"113","menu":"Car & Auto Care"},{"id":"120","menu":"Chocolates & Snacks"},{"id":"132","menu":"Clocks"},{"id":"102","menu":"Health Care and Monitors"},{"id":"117","menu":"Home Appliances"},{"id":"133","menu":"Home Care & Cleaning"},{"id":"121","menu":"Jewellery"},{"id":"111","menu":"Kitchen appliances"},{"id":"114","menu":"Mens Clothing & Apparels"},{"id":"127","menu":"Mobile Accessories"},{"id":"101","menu":"Nutrition and Diet"},{"id":"134","menu":"Offers"},{"id":"129","menu":"Perfumes"},{"id":"128","menu":"Personal grooming"},{"id":"109","menu":"Pet Care"},{"id":"107","menu":"Sports & Fitness"},{"id":"110","menu":"Stationary & Office Supplies"},{"id":"106","menu":"Sunglasses"},{"id":"108","menu":"Toys"},{"id":"130","menu":"Travel and Luggage"},{"id":"116","menu":"TV, Audio, Video & Gaming"},{"id":"131","menu":"Wallets"},{"id":"126","menu":"Watches"},{"id":"119","menu":"Womens Clothing & Apparels"},{"id":"100","menu":"Beauty & Cosmetics"},{"id":"112","menu":"Mobiles & Tablets"},{"id":"115","menu":"Musical Insturments"}],"payment_det":{"pending":-858353.9972,"uncleared":"10000"},"cart_total":"5"}}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2003","error_msg","No data found"}
				</code>
				<p class="api_desc">
					Request to get franchise details
				</p>
			</div>
			
			<div class="api_det">
				<h4>Get franchisee Group menu list  </h4>
				<code>
					<?php echo site_url('api/get_franchise_menus')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>    {mandatory}
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"menu_list":[{"group_id":"1","group_name":"Apparels","menuid":"119","group_icon": "http://sndev13.snapittoday.com/images/group_icons/1.png"},{"group_id":"2","group_name":"Baby Care & Toys","menuid":"104,108","group_icon": "http://sndev13.snapittoday.com/images/group_icons/2.png"},{"group_id":"3","group_name":"Bags, Belts & Watches","menuid":"103,105,126,130,131","group_icon": "http://sndev13.snapittoday.com/images/group_icons/3.png"},{"group_id":"4","group_name":"Beauty & Personal Care","menuid":"100,101,102,128,129","group_icon": "http://sndev13.snapittoday.com/images/group_icons/4.png"},{"group_id":"5","group_name":"Electronics","menuid":"111,112,116,117,118,122,127","group_icon": "http://sndev13.snapittoday.com/images/group_icons/5.png"},{"group_id":"6","group_name":"Footwear","menuid":"123","group_icon": "http://sndev13.snapittoday.com/images/group_icons/6.png"},{"group_id":"7","group_name":"Jewellery","menuid":"121","group_icon": "http://sndev13.snapittoday.com/images/group_icons/7.png"},{"group_id":"8","group_name":"Lens & Sunglasses","menuid":"106","group_icon": "http://sndev13.snapittoday.com/images/group_icons/8.png"},{"group_id":"9","group_name":"Sports Goods","menuid":"107","group_icon": "http://sndev13.snapittoday.com/images/group_icons/9.png"}]}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2003","error_msg","No data found"}
				</code>
				<p class="api_desc">
					Request to to get group menus 
				</p>
			</div>
			
			<div class="api_det">
				<h4>Get Menu list with categories under Group menu</h4>
				<code>
					<?php echo site_url('api/menus_cat_bygroupmenu')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : group_menuid=<b>Group Menu ID</b>   {mandatory}
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>
					<br><b>Params</b> : start=<b>RECORT START FROM</b>
					<br><b>Params</b> : limit=<b>HOW MANY RECORDS</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"menu_list":[{"119":{"menuid":"119","menu_name":"Womens Clothing & Apparels","menu_icon"=>'http://sndev13.snapittoday.com/images/menu_icons/119.png","ttl_cats":2,"914":{"catid":"914","cat_name":"Ethnic Clothing"},"913":{"catid":"913","cat_name":"Clothing"}}}],"menu_cat_list":{"1":{"119":{"menuid":"119","menu_name":"Womens Clothing & Apparels","menu_icon"=>'http://sndev13.snapittoday.com/images/menu_icons/119.png","ttl_cats":2,"914":{"catid":"914","cat_name":"Ethnic Clothing"},"913":{"catid":"913","cat_name":"Clothing"}}}}}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2003","error_msg","No data found"}
				</code>
				<p class="api_desc">
					Request to get Menu list,Category list under group menu
				</p>
			</div>
			
			<div class="api_det">
				<h4>Get brands list</h4>
				<code>
					<?php echo site_url('api/get_brand_list')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : menu_id=<b>MENU ID</b>     {optional}
					<br><b>Params</b> : cat_id=<b>CATEGORY ID</b>  {optional}
					<br><b>Params</b> : franchisee_id=<b>FRANCHISEE ID</b>
					<br><b>Params</b> : start=<b>RECORT START FROM</b>
					<br><b>Params</b> : limit=<b>HOW MANY RECORDS</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response </b> : {"status":"success","response":{"brand_list":[{"brandid":"18916582","brandname":"PayNearHome"},{"brandid":"25487288","brandname":"Lenovo"},{"brandid":"27927936","brandname":"Panasonic"},{"brandid":"31648385","brandname":"Sony"},{"brandid":"31728443","brandname":"Google"},{"brandid":"31758186","brandname":"Motorola"},{"brandid":"36737295","brandname":"Celkon"},{"brandid":"38335857","brandname":"Lava"},{"brandid":"44312644","brandname":"Ndura"},{"brandid":"46211514","brandname":"Idea"}]}}
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
					<br><b>Params</b> : menu_id=<b>MENU ID</b>      {mandatory}
					<br><b>Params</b> : brand_id=<b>BRAND ID</b>	{optional}
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>
					<br><b>Params</b> : start=<b>RECORT START FROM</b>
					<br><b>Params</b> : limit=<b>HOW MANY RECORDS</b>
					<br><b>Response Type </b> : JSON
					br><b>Response</b> : {"status":"success","response":{"category_list":[{"id":"75","name":"Body Wash","sub_catids":null,"sub_cat_names":null},{"id":"76","name":"Cleanser (Bath)","sub_catids":"75::,119::","sub_cat_names":"Body Wash::,Soap::"},{"id":"89","name":"Face Wash","sub_catids":null,"sub_cat_names":null},{"id":"94","name":"Hair Colour","sub_catids":null,"sub_cat_names":null},{"id":"114","name":"Shampoo","sub_catids":"226::","sub_cat_names":"Herbal Shampoo::"},{"id":"119","name":"Soap","sub_catids":null,"sub_cat_names":null},{"id":"130","name":"Hair Care","sub_catids":"94::,114::","sub_cat_names":"Hair Colour::,Shampoo::"},{"id":"165","name":"Fairness","sub_catids":"267::","sub_cat_names":"Fairness Cream::"},{"id":"226","name":"Herbal Shampoo","sub_catids":null,"sub_cat_names":null},{"id":"267","name":"Fairness Cream","sub_catids":null,"sub_cat_names":null}]}}
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
					<br><b>Params</b> : menu_id=<b>MENU ID</b>           {optional}
					<br><b>Params</b> : brand_id=<b>BRAND ID</b>		 {optional}
					<br><b>Params</b> : category_id=<b>CATEGORY ID</b>   {optional}
					<br><b>Params</b> : sort_by=<b>SORT BY</b>      (Latest added(sort_by='new')  Popular(sort_by='popular')  Recently Sold(sort_by='recently_sold') Maximum Price(sort_by='max_price') Minmum Price(sort_by='min_price')), Show Only InStock,Sold Out Deals(sort_by='instock' | 'soldout') {optional}
					<br><b>Params</b> : start=<b>RECORT START FROM</b>   {optional}
					<br><b>Params</b> : limit=<b>HOW MANY RECORDS</b>    {optional}
					<br><b>Params</b> : max_price=<b>Maximum Price value to sort{array}</b>    {optional}
					<br><b>Params</b> : min_price=<b>Minimum Price value to sort{array}</b>    {optional}
					<br><b>Params</b> : type=<b>Type</b>    {optional}  //deals,total,refine
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"deal_list":[{free_frame: "0",has_power: "0",pid: "1379864",size_chart: "",itemid: "2894839425",name: "BlackBerry Curve 9220 (Blue)",menu: "Mobiles & Tablets",menu_id: "112",gender_attr: "",category: "Smart Phone ",category_id: "1030",main_category: null,member_price: "9467.43",main_category_id: "0",brand: "Blackberry",brand_id: "83127212",mrp: "12403",price: "9515",store_price: "12403",is_combo: "0",image_url: "http://static.snapittoday.com/items/h9mgi5h1do4065f.jpg",small_image_url: "http://static.snapittoday.com/items/small/h9mgi5h1do4065f.jpg,ships_in: "24-48 Hrs",keywords: "BlackBerry Curve 9220 (Blue),BlackBerry,Curve,9220,Blue,",total_orders: "5",p_discount: "0.763318",d_sno: "13489",rel_ttl: "0"",images: [0],attributes: [0],pseller_id: "",mp_mem_max_qty: 0,mp_frn_max_qty: 0,max_ord_qty: 5,landing_cost: 9515,availability: }]
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2003","error_msg","No data found"}
				</code>
				<p class="api_desc">
					Request to get the deal list,it also support two combination of filters 1.menu by category,2.brand by category.if no records found returns empty index of response array.<br>
					it is also support the multiple brands by deals,if need a deal list by multiple brands then brand ids want to give input in the form of array[this is same for category also].
				</p>
			</div>
			
			<div class="api_det">
				<h4>Get Deal/Product availability and size before add into the cart</h4>
				<code>
					<?php echo site_url('api/get_deal_info')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : pid=<b>PRODUCT ID</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"deal_info":{"free_frame":"0","has_power":"0","item_id":"5295877761","size_chart":null,"pid":"19999477","is_group":"1","gender_attr":"","itemid":"5295877761","name":"Testing Dress","tagline":"","category":"Clothing","menu":"Womens Clothing & Apparels","menu_id":"119","category_id":"913","main_category":null,"main_category_id":"0","brand":"Western Brand","brand_id":"11337932","member_price":"100.00","mrp":"100","price":"100.00","store_price":"100","is_combo":"0","image_url":"http:\/\/static.snapittoday.com\/items\/0619ina38gj893o.jpg","description":"Testing Dress.","ships_in":"24-48 Hrs","keywords":"","is_stock":"1","is_enabled":"1","small_image_url":"http:\/\/static.snapittoday.com\/items\/small\/0619ina38gj893o.jpg","has_insurance":"0","publish":"1","images":[],"pseller_id":"","offer_note":"","attributes":"size:1,ProductID:235440||size:1,ProductID:235441","p_attr_stk":"235440:0,235441:0","price_type":1,"mp_frn_max_qty":"2","mp_mem_max_qty":"2","max_ord_qty":2,"landing_cost":100,"availability":1,"max_order_qty":2},"stock_status":"In Stock"}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2003","error_msg","No data found"}
				</code>
				<p class="api_desc">
					Request to get the deal info.if no records found returns empty index of response array.
				</p>
			</div>
			
			<!-----Cart Functions ------------------------>
			<div class="api_det">
				<h4>Add the item to cart</h4>
				<code>
					<?php echo site_url('api/cart_add')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : pid=<b>PRODUCT ID</b>  {Mandatory}
					<br><b>Params</b> : qty=<b>QTY</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b> {Mandatory}
					<br><b>Params</b> : attributes=<b>attributes[if any attributes for selected ,attributes parameter in the form of array,array value is ATTRIBUTE_ID:ATTRIBUTE_VALUE_ID EX:attr[0]=158:165]</b>
					<br><b>Params</b> : multiple=<b>MULTIPLE FLAG</b> {its a flag whether adding multiple items or not }
					<br><b>Params</b> : cart_items=<b>This api support the multiple items to add a cart,so gives the input in the form multi dimentional array EX:[array(0=>array(pid=>123,qty=>5,attributes=>array(0=>ATTRIBUTE_ID:ATTRIBUTE_VALUE_ID)))],if no attribute exist so give attribute index to empty array and no need of parameter for pid,qty,attributes and want set flag multiple to 1</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response </b> : {"status":"success","response":{"cart_msg":"Your item added to cart"}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2004","error_msg","Sorry! Please try again"}
				</code>
				<p class="api_desc">
					To add the item to cart.
				</p>
			</div>
			
			<div class="api_det">
				<h4>Get the cart item list</h4>
				<code>
					<?php echo site_url('api/get_cart_item_list')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>    {Mandatory}
					<br><b>Params</b> : pid=<b>PID</b>
					<br><b>Params</b> : member_type=<b>Member type(member type is cart products for new member or old member 1:old member,2:new member,3:key member.it will support to get a member fee and insurance details.)</b>
					<br><b>Params</b> : member_Id=<b>Member ID</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response </b> : {"status":"success","response":{"cart_items":[{"pid":"19999477","qty":"1","attributes":"size:1,undefined:undefined","deal_deails":{"free_frame":"0","has_power":"0","item_id":"5295877761","size_chart":null,"pid":"19999477","is_group":"1","gender_attr":"","itemid":"5295877761","name":"Testing Dress","tagline":"","category":"Clothing","menu":"Womens Clothing & Apparels","menu_id":"119","category_id":"913","main_category":null,"main_category_id":"0","brand":"Western Brand","brand_id":"11337932","member_price":"100.00","mrp":"100","price":"100.00","store_price":"100","is_combo":"0","image_url":"http:\/\/static.snapittoday.com\/items\/0619ina38gj893o.jpg","description":"Testing Dress.","ships_in":"24-48 Hrs","keywords":"","is_stock":"1","is_enabled":"1","small_image_url":"http:\/\/static.snapittoday.com\/items\/small\/0619ina38gj893o.jpg","has_insurance":"0","publish":"1","images":[],"pseller_id":"","offer_note":"","attributes":"size:1,ProductID:235440||size:1,ProductID:235441","p_attr_stk":"235440:0,235441:0","price_type":1,"mp_frn_max_qty":"2","mp_mem_max_qty":"2","max_ord_qty":2,"landing_cost":100,"availability":1,"max_order_qty":2,"discount":0,"qty":"1","final_price":100,"mem_fee":0,"insurance_fee":null,"is_sourceable":0,"live":1,"insurance_value":"","insurance_margin":"","margin":"0","imei_disc":0,"lcost":100,"confirm_stock":"","stock":0,"max_allowed_qty":"5","svd_cartqty":"1","super_sch":0,"allow_order":[5295877761],"is_publish":"1","key_member":0,"new_member_value":0,"existing_member_margin":0,"insurance_types":""},"cart_msg":"","place_order":1,"credit_msg":"<div class=\"type_title\">Credit Available : <\/div> <div class=\"type_value alert-proceed\">Rs 18,44,12,825.41<\/div>","pending_msg":"<div class=\"type_title\">Payment Pending : <\/div> <div class=\"type_value alert-proceed\">Rs 14,088.25<\/div>","new_mem":0,"mem_type":"1","mem_fee":0,"mem_type_config":{"1":"Old member","2":"New member","3":"key member"}}]}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2005","error_msg","No more items in cart"}
				</code>
				<p class="api_desc">
					Get the deal list in the cart,and also it support pid wise list.if no records found returns empty index of response array.
				</p>
			</div>
			
			<div class="api_det">
				<h4>Remove the item from cart</h4>
				<code>
					<?php echo site_url('api/cart_remove')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : pid=<b>PRODUCT ID</b>     {Mandatory}
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>   {Mandatory}
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"cart_msg":"Your item successfully removed from cart","cart_items":[{"pid":"1616543","qty":"1","attributes":"","deal_deails":{"free_frame":"0","has_power":"0","item_id":"2245838512","size_chart":null,"pid":"1616543","is_group":"0","gender_attr":"","itemid":"2245838512","name":"Nokia 112 (Dark Grey)","tagline":"","category":"Basic Phones","menu":"Mobiles & Tablets","menu_id":"112","category_id":"1028","main_category":null,"main_category_id":"0","brand":"Nokia","brand_id":"76916829","member_price":"2764.00","mrp":"3319","price":"2764.00","store_price":"2859","is_combo":"0","powered_by":null,"image_url":"http:\/\/static.snapittoday.com\/items\/48f13e3dnhe56e2.jpg","description":"&lt;h3 class=&quot;item_desc_title&quot;&gt;         &lt;b&gt; Specifications of Nokia 112 (Dark Grey)&lt;\/b&gt;     &lt;\/h3&gt;              &lt;table cellspacing=&quot;0&quot; class=&quot;fk-specs-type2&quot;&gt;             &lt;tr&gt;                 &lt;th class=&quot;group-head&quot; colspan=&quot;2&quot;&gt;GENERAL FEATURES&lt;\/th&gt;             &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;In Sales Package&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;Handset, Battery, Charger, Headset, User Manual, Warranty Card&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Form&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;Bar&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;SIM&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;Dual SIM, GSM + GSM&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Touch Screen&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;No, Not Applicable&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Keypad&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;Yes, Alphanumeric&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Call Features&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;Conference Call, Speed Dialing, Loudspeaker, Call Divert&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Handset Color&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;Dark Grey&lt;\/td&gt;                 &lt;\/tr&gt;                     &lt;\/table&gt;             &lt;table cellspacing=&quot;0&quot; class=&quot;fk-specs-type2&quot;&gt;             &lt;tr&gt;                 &lt;th class=&quot;group-head&quot; colspan=&quot;2&quot;&gt;Platform&lt;\/th&gt;             &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Operating Freq&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;GSM - 900, 1800&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;OS&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;(Series 40, 6th Edition)&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Java&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;Yes&lt;\/td&gt;                 &lt;\/tr&gt;                     &lt;\/table&gt;             &lt;table cellspacing=&quot;0&quot; class=&quot;fk-specs-type2&quot;&gt;             &lt;tr&gt;                 &lt;th class=&quot;group-head&quot; colspan=&quot;2&quot;&gt;Display&lt;\/th&gt;             &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Type&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;LCD&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Size&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;1.8 Inches&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Resolution&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;128 x 160 Pixels&lt;\/td&gt;                 &lt;\/tr&gt;                     &lt;\/table&gt;             &lt;table cellspacing=&quot;0&quot; class=&quot;fk-specs-type2&quot;&gt;             &lt;tr&gt;                 &lt;th class=&quot;group-head&quot; colspan=&quot;2&quot;&gt;Camera&lt;\/th&gt;             &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Primary Camera&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;Yes, 0.3 Megapixel&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Secondary Camera&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;No&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Video Recording&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;Yes, 176 x 144, 10 fps&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Zoom&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;Digital Zoom - 4x&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Other Camera Features&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;Auto and Manual White Balance, Exposure Compensation, Full Screen View Finder, Self Timer, Active Toolbar, Landscape Orientation, Image Editor&lt;\/td&gt;                 &lt;\/tr&gt;                     &lt;\/table&gt;             &lt;table cellspacing=&quot;0&quot; class=&quot;fk-specs-type2&quot;&gt;             &lt;tr&gt;                 &lt;th class=&quot;group-head&quot; colspan=&quot;2&quot;&gt;Dimensions&lt;\/th&gt;             &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Size&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;46.9x110.4x15.4 mm&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Weight&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;86 g&lt;\/td&gt;                 &lt;\/tr&gt;                     &lt;\/table&gt;             &lt;table cellspacing=&quot;0&quot; class=&quot;fk-specs-type2&quot;&gt;             &lt;tr&gt;                 &lt;th class=&quot;group-head&quot; colspan=&quot;2&quot;&gt;Battery&lt;\/th&gt;             &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Type&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;Li-Ion, 1400 mAh&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Talk Time&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;14 hrs (2G)&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Standby Time&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;839 hrs (2G)&lt;\/td&gt;                 &lt;\/tr&gt;                     &lt;\/table&gt;             &lt;table cellspacing=&quot;0&quot; class=&quot;fk-specs-type2&quot;&gt;             &lt;tr&gt;                 &lt;th class=&quot;group-head&quot; colspan=&quot;2&quot;&gt;Memory and Storage&lt;\/th&gt;             &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Internal&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;10 MB&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Expandable Memory&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;microSD, upto 32 GB&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Memory&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;16 MB RAM, 64 MB ROM&lt;\/td&gt;                 &lt;\/tr&gt;                     &lt;\/table&gt;             &lt;table cellspacing=&quot;0&quot; class=&quot;fk-specs-type2&quot;&gt;             &lt;tr&gt;                 &lt;th class=&quot;group-head&quot; colspan=&quot;2&quot;&gt;Internet &amp; Connectivity&lt;\/th&gt;             &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Internet Features&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;Email&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Preinstalled Browser&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;WAP 2.0&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;GPRS&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;Yes&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;EDGE&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;Yes, 236.8 kbps&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;WAP&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;Yes, v2&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;USB Connectivity&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;No&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Bluetooth&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;Yes, v2.1, Supported Profiles (EDR, DUN, FTP, GAP, GOEP, HFP, HSP, L2CAP, OPP, PAN, PBAP 1.0, SAP, SDAP, SDP, SPP 1.0)&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Audio Jack&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;3.5 mm&lt;\/td&gt;                 &lt;\/tr&gt;                     &lt;\/table&gt;             &lt;table cellspacing=&quot;0&quot; class=&quot;fk-specs-type2&quot;&gt;             &lt;tr&gt;                 &lt;th class=&quot;group-head&quot; colspan=&quot;2&quot;&gt;Multimedia&lt;\/th&gt;             &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Music Player&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;Yes, Supports MP3, WAV, MIDI&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Video Player&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;Yes, Supports 3GPP, H.263, ASF, AVI, H.264, AVC, VC-1, WMV, MP4, H.264&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;FM&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;Yes, with Recording&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Ringtone&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;MP3, WAV, MIDI, Polyphonic 24&lt;\/td&gt;                 &lt;\/tr&gt;                     &lt;\/table&gt;             &lt;table cellspacing=&quot;0&quot; class=&quot;fk-specs-type2&quot;&gt;             &lt;tr&gt;                 &lt;th class=&quot;group-head&quot; colspan=&quot;2&quot;&gt;Other Features&lt;\/th&gt;             &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;SAR Value&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;1.30 W\/Kg&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Call Memory&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;Yes&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;SMS Memory&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;Yes&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Phone Book Memory&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;1000&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Additional Features&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;GSM, Email, Bluetooth, FM Player, Music Player, MMS Enabled, Flight Mode, Nokia Life Tools, Digital Clock, Audio Recorder, Calculator, Clock, Calendar, Converter, Notes, Alarm Clock, Reminders, FOTA Firmware Over the Air, Automatic Redial, Call Barring, Call Waiting, Voice Mail, Dedicated Hardware Keys, Games, Noise Cancellation, DRM Support, Device Lock, Album Graphics Display, Voice Recorder&lt;\/td&gt;                 &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;Important Apps&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;Instant Messaging, Facebook, Twitter, Nokia Chat, Windows Live Messenger, Gmail&lt;\/td&gt;                 &lt;\/tr&gt;                     &lt;\/table&gt;             &lt;table cellspacing=&quot;0&quot; class=&quot;fk-specs-type2&quot;&gt;             &lt;tr&gt;                 &lt;th class=&quot;group-head&quot; colspan=&quot;2&quot;&gt;Warrantyy&lt;\/th&gt;             &lt;\/tr&gt;                                                                                           &lt;tr&gt;                     &lt;td class=&quot;specs-key&quot;&gt;&lt;\/td&gt;                     &lt;td class=&quot;specs-value fk-data&quot;&gt;1 year manufacturer warranty for Phone and 6 months warranty for in the box accessories&lt;\/td&gt;                 &lt;\/tr&gt;                     &lt;\/table&gt;","ships_in":"48-72 hrs","keywords":"Nokia 112 (Dark Grey),Nokia,112,Dark,Grey,","is_stock":"1","is_enabled":"1","small_image_url":"http:\/\/static.snapittoday.com\/items\/small\/48f13e3dnhe56e2.jpg","has_insurance":"0","publish":"1","images":[],"pseller_id":"","offer_note":"","attributes":"","p_attr_stk":"","price_type":1,"mp_frn_max_qty":"1","mp_mem_max_qty":"1","max_ord_qty":1,"landing_cost":2764,"availability":1,"max_order_qty":1,"discount":0,"qty":"1","final_price":2764,"mem_fee":0,"insurance_fee":0,"is_sourceable":"1","live":1,"insurance_value":"","insurance_margin":"","margin":"0","imei_disc":0,"key_imei_disc":0,"lcost":2764,"confirm_stock":"","stock":"8","max_allowed_qty":"5","svd_cartqty":"1","super_sch":0,"allow_order":["2245838512"],"is_publish":"1","key_member":1,"new_member_value":0,"existing_member_margin":0,"insurance_types":""}}],"place_order":1,"credit_msg":"<div class=\"type_title\">Credit Available : <\/div> <div class=\"type_value alert-proceed\">Rs 10,00,27,669.80<\/div>","pending_msg":"<div class=\"type_title\">Payment Pending : <\/div> <div class=\"type_value alert-proceed\">Rs 68,411.74<\/div>","new_mem":null,"member_type":null,"mem_fee":0,"franchise_current_balance":100027669.8077,"franchise_pending_payment":68411.745800001}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2004","error_msg","Sorry! Please try again"}
				</code>
				<p class="api_desc">
					Remove the ite from cart
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
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>   {Mandatory}
					<br><b>Params</b> : attributes=<b>attributes[if any attributes for selected ,attribures parameter in the form of array,array value is ATTRIBUTE_ID:ATTRIBUTE_VALUE_ID EX:attr[0]=158:165]</b> {optional}
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"cart_update_status":"success"}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2006","error_msg","Cart item not updated"}
				</code>
				<p class="api_desc">
					To update the cart items
				</p>
			</div>
			
			<div class="api_det">
				<h4>To validate the cart item</h4>
				<code>
					<?php echo site_url('api/validate_cart_items')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>    {Mandatory}
					<br><b>Params</b> : validation_type=<b>VALIDATION TYPE FLAG</b>  {Mandatory}
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"Validation":true}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2009","error_msg","No validation type found"}
				</code>
				<p class="api_desc">
					This api for validate the loged franchise cart items by given validation type,this api validate and given if it no error the return true else given validation error in the format of array
					it support n numbers of validation by type,
					Type 1:if validation type set 1 then this api validate the member and electronics items qty validation.
				</p>
			</div>
			<!-----Cart Functions ------------------------>
			
			<div class="api_det">
				<h4>Get member info</h4>
				<code>
					<?php echo site_url('api/get_member_info')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : member_id=<b>Member id</b>   {Mandatory}
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"member_details":[{"id":"215","user_id":"60119","pnh_member_id":"21111111","franchise_id":"17","points":"0","voucher_balance":"0","gender":"0","salute":"0","first_name":"Sridhar Gundaiah ","last_name":"","dob":"1969-12-31","address":"","city":"","pincode":"","mobile":"9980004542","mobile_network":null,"email":"","marital_status":"0","spouse_name":"","child1_name":"","child2_name":"","anniversary":"1969-12-31","child1_dob":"1969-12-31","child2_dob":"1969-12-31","profession":"","expense":"0","is_card_printed":"0","created_on":"0","modified_on":"0","created_by":"0","modified_by":"0","dummy":"0","voucher_bal_validity":null}]}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2007","error_msg","No member available for given member id"}
				</code>
				<p class="api_desc">
					To get the details about member for given member id
				</p>
			</div>
			
			<div class="api_det">
				<h4>Add member info</h4>
				<code>
					<?php echo site_url('api/add_member')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>
					<br><b>Params</b> : mobile_no=<b>MOBILENO</b>   {Mandatory}
					<br><b>Params</b> : member_name=<b>MEMBER NAME</b>    {Mandatory}
					<br><b>Params</b> : member_dob=<b>MEMBER DOB</b>
					<br><b>Params</b> : member_address=<b>MEMBER ADDRESS</b>
					<br><b>Params</b> : member_city=<b>MEMBER CITY</b>
					<br><b>Params</b> : member_pincode=<b>MEMBER PINCODE</b>
					<br><b>Params</b> : member_email=<b>MEMBER EMAIL</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"member_details":{"member_name":"storeking123456","member_id":22028815,"mobile_no":"8878785858"}}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2008","error_msg","Mobile no already registered"}
				</code>
				<p class="api_desc">
					To add the new member
				</p>
			</div>
			
			<div class="api_det">
				<h4>Get member by mobile number </h4>
				<code>
					<?php echo site_url('api/get_member_by_mobileno')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>
					<br><b>Params</b> : mobile_no=<b>MOBILE NO</b>   {Mandatory}
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"member_details":{"id":"32180","user_id":"122799","pnh_member_id":"22028815","franchise_id":"498","points":"0","voucher_balance":"0","gender":"0","salute":"0","first_name":"storeking123456","last_name":"","dob":null,"address":"","city":"","pincode":"","mobile":"8878785858","mobile_network":null,"email":"","marital_status":"0","spouse_name":"","child1_name":"","child2_name":"","anniversary":null,"child1_dob":null,"child2_dob":null,"profession":"","expense":"0","is_card_printed":"0","created_on":"1408530418","modified_on":"0","created_by":"0","modified_by":"0","dummy":null,"voucher_bal_validity":null}}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2010","error_msg","No member found for given mobile number"}
				</code>
				<p class="api_desc">
					get the memeber by mobile number.
				</p>
			</div>
			
			<!-- Search API ----------->
			<div class="api_det">
				<h4>Get deals by searching </h4>
				<code>
					<?php echo site_url('api/search')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : brand_id=<b>BRAND ID</b>		 {optional}
					<br><b>Params</b> : category_id=<b>CATEGORY ID</b>   {optional}
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>
					<br><b>Params</b> : sort_by=<b>SORT BY</b>      (Latest added(sort_by='new')  Popular(sort_by='popular')  Recently Sold(sort_by='recently_sold') Maximum Price(sort_by='max_price') Minmum Price(sort_by='min_price'))  {optional}
					<br><b>Params</b> : start=<b>RECORT START FROM</b>   {optional}
					<br><b>Params</b> : limit=<b>HOW MANY RECORDS</b>    {optional}
					<br><b>Params</b> : max_price=<b>Maximum Price value to sort{array}</b>    {optional}
					<br><b>Params</b> : min_price=<b>Minimum Price value to sort{array}</b>    {optional}
					<br><b>Params</b> : search_data=<b>SEARCHED KEYWORD</b>   {Mandatory}
					<br><b>Params</b> : type=<b>GET Parameter</b> //total,deals,refine
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"deal_list":[{"free_frame":"0","has_power":"0","pid":"15399397","size_chart":"","itemid":"4783522598","name":"Samsung 7562 Pouch","menu":"Mobile Accessories","menu_id":"127","gender_attr":"","category":"pouches","category_id":"499","main_category":"Mobile Accessories","member_price":"1299.00","main_category_id":"27","brand":"Samsung","brand_id":"82298176","mrp":"1299","price":"1299","store_price":"1299","is_combo":"0","image_url":"http:\/\/static.snapittoday.com\/items\/6gigl141290db20.jpg","small_image_url":"http:\/\/static.snapittoday.com\/items\/small\/6gigl141290db20.jpg","description":"Samsung 7562 Pouch","ships_in":"48-72 hrs","keywords":"Samsung 7562 Pouch,Samsung,7562,Pouch","total_orders":"2","p_discount":"1.000000","d_sno":"82817","rel_ttl":"2","products":{"product":[{"name":"Samsung 7562 Pouch","qty":"1"}]},"images":[],"attributes":[],"pseller_id":"","mp_mem_max_qty":0,"mp_frn_max_qty":0,"max_ord_qty":5,"landing_cost":1299,"availability":1},{"free_frame":"0","has_power":"0","pid":"19615398","size_chart":"","itemid":"2687147188","name":"Samsung Galaxy 7562 Battery","menu":"Computers & Peripherals","menu_id":"118","gender_attr":"","category":"Batteries","category_id":"319","main_category":"Computer Accessories","member_price":"1299.00","main_category_id":"29","brand":"Samsung","brand_id":"82298176","mrp":"1299","price":"1299","store_price":"1299","is_combo":"0","image_url":"http:\/\/static.snapittoday.com\/items\/04a4c5h8ij66218.jpg","small_image_url":"http:\/\/static.snapittoday.com\/items\/small\/04a4c5h8ij66218.jpg","description":"Samsung Galaxy 7562 Battery","ships_in":"48-72 hrs","keywords":"Samsung Galaxy 7562 Battery,Samsung,Galaxy,7562,Battery","total_orders":"1","p_discount":"1.000000","d_sno":"83937","rel_ttl":"2","products":{"product":[{"name":"Samsung Galaxy 7562 Battery","qty":"1"}]},"images":[],"attributes":[],"pseller_id":"","mp_mem_max_qty":0,"mp_frn_max_qty":0,"max_ord_qty":5,"landing_cost":1299,"availability":1}],"total_deals":0,"brand_list":[],"category_list":{"499":{"id":"499","name":"pouches"},"27":{"id":"27","name":"Mobile Accessories"},"319":{"id":"319","name":"Batteries"},"29":{"id":"29","name":"Computer Accessories"}},"cat_list":[],"price_list":[],"attr_list":[],"gender_list":[]}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2015","error_msg","Please enter search keyword"}
				</code>
				<p class="api_desc">
					Search brand,category,deals.if no records found returns empty index of response array.
				</p>
			</div>
			<!-- Search API ----------->
			
			<!-- Place Order API ----------->
			<div class="api_det">
				<h4>Create Order</h4>
				<code>
					<?php echo site_url('api/create_order')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : authkey=<b>AUTHKEY</b>
					<br><b>Params</b> : f_mobno=<b>FRANCHISE MOBILENO</b>
					<br><b>Params</b> : m_mobno=<b>MEMBER MOBILENO</b>       {Mandatory}
					<br><b>Params</b> : m_name=<b>MEMBER NAME</b>
					<br><b>Params</b> : m_id=<b>MEMBER ID</b> 
					<br><b>Params</b> : coupon_code=
					<br><b>Params</b> : pids[]=<b>PRODUCTS IN CART </b>       {Mandatory}
					<br><b>Params</b> : pqtys[]=<b>PRODUCT QTYS IN CART</b>   {Mandatory}
					<br><b>Params</b> : attr_pid[]=<b>PRODUCTS IN CART </b>
					<br><b>Params</b> : attr_pid_val[]=<b>SELECTED SIZE - PRODUCT ID </b>					
					<br><b>Params</b> : authkey=<b>AUTHKEY</b>
					<br><b>Params</b> : fid=<b>FranchiseID</b>
					<br><b>Params</b> : opt_insurance=<b>Insurance Opted PRODUCT IDs array</b>
					<br><b>Params</b> : insurance=<b>Insurance details array</b>  Example:: -- 
																					{insurance[city] = Bangalore
																					insurance[first_name]=Sridhar Gundaiah
																					insurance[insurance_deals]=17732816
																					insurance[last_name]=Gundaiah
																					insurance[mob_no]=9980004542
																					insurance[opted_insurance]=1
																					insurance[pincode]=560026
																					insurance[proof_id]=ABX12568
																					insurance[proof_name]=	
																					insurance[proof_type]=Aadhar Card}             
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : {"status":"error","error_code":2000,"error_msg":"No orders found."}
					<br><b>Response </b> : {"status":"success","response":{"trans":"PNH82744","orders":[{"order_id":"2291464544","product_id":"16679464","item_name":"Olay Natural White Light Instant Glowing Fairness Serum - 20g","mrp":"85","offer_price":85,"has_insurance":"0","insurance_fee":"0.00","franchise_price":72.25,"franchise_price_percentage":"15%","member_fee":0}],"total_member_fee":0,"order_for":"Non key Member"}}
				</code>
				<p class="api_desc">
					Function to create order via api 
				</p>
			</div>
			<!-- Place Order API ----------->
			
			<!-- Payment API ---------->
			<div class="api_det">
				<h4>Get the pending amount of franchisee</h4>
				<code>
					<?php echo site_url('api/fran_pending_payment')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>   {Mandatory}
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"pending_payment":"58,627.82"}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2045","error_msg","Please enter franchise id"}
				</code>
				<p class="api_desc">
					Api used to get the pending amount of the franchise.if no data found it return empty response.
				</p>
			</div>
			
			<div class="api_det">
				<h4>Get the uncleared amount of franchisee</h4>
				<code>
					<?php echo site_url('api/fran_uncleared_payment')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>    {Mandatory}
					<br><b>Params</b> : in_transit=<b>In Transit</b> (Uncleared(in_transit=0)  in_transit/uncleared(in_transit=1))  {Mandatory}
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"pending_payment":"58,627.82"}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2045","error_msg","Please enter franchise id"}
				</code>
				<p class="api_desc">
					Api used to get the uncleared amount of the franchisee.if no data found it return empty response.
				</p>
			</div>
			<div class="api_det">
				<h4>Get the recent payments done by franchisee</h4>
				<code>
					<?php echo site_url('api/fran_recent_payments')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>   {Mandatory}
					<br><b>Params</b> : start_date=<b>START DATE</b>
					<br><b>Params</b> : end_date=<b>END DATE</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"recent_payments":[{"receipt_id":"7352","bank_name":"Canara Bank","payment_mode":"cheque","receipt_amount":"33071","receipt_type":"1","instrument_no":"146883","instrument_date":"1398277800","status":"1","created_on":"23\/04\/2014","created_on_time":"04:36 PM","remarks":"Receipt In Hand"},{"receipt_id":"7351","bank_name":"Canara Bank","payment_mode":"cheque","receipt_amount":"14880","receipt_type":"1","instrument_no":"146884","instrument_date":"1398277800","status":"1","created_on":"23\/04\/2014","created_on_time":"04:35 PM","remarks":"Receipt In Hand"},{"receipt_id":"7117","bank_name":"canara bank","payment_mode":"cheque","receipt_amount":"9540","receipt_type":"1","instrument_no":"146862","instrument_date":"1396809000","status":"1","created_on":"07\/04\/2014","created_on_time":"02:47 PM","remarks":"Receipt In Hand"},{"receipt_id":"7015","bank_name":"canara bank","payment_mode":"cheque","receipt_amount":"92357","receipt_type":"1","instrument_no":"146856","instrument_date":"1395945000","status":"1","created_on":"29\/03\/2014","created_on_time":"10:45 AM","remarks":"Chq received -29.03.2014"},{"receipt_id":"6302","bank_name":"Axis  bank","payment_mode":"cheque","receipt_amount":"4220","receipt_type":"1","instrument_no":"283194","instrument_date":"1392316200","status":"1","created_on":"15\/02\/2014","created_on_time":"04:42 PM","remarks":"Deposited at ICICI Bank"},{"receipt_id":"5802","bank_name":"canara bank","payment_mode":"cheque","receipt_amount":"12200","receipt_type":"1","instrument_no":"193920","instrument_date":"1390242600","status":"1","created_on":"21\/01\/2014","created_on_time":"11:40 AM","remarks":"Receipt In Hand"},{"receipt_id":"5473","bank_name":"canara bank","payment_mode":"cheque","receipt_amount":"8000","receipt_type":"1","instrument_no":"193905","instrument_date":"1388860200","status":"1","created_on":"06\/01\/2014","created_on_time":"11:58 AM","remarks":"Receipt In Hand"},{"receipt_id":"5300","bank_name":"canara bank","payment_mode":"cheque","receipt_amount":"22222","receipt_type":"1","instrument_no":"189386","instrument_date":"1388169000","status":"1","created_on":"28\/12\/2013","created_on_time":"07:10 PM","remarks":"Receipt In Hand"},{"receipt_id":"5299","bank_name":"canara bank","payment_mode":"cheque","receipt_amount":"2101","receipt_type":"1","instrument_no":"189385","instrument_date":"1388169000","status":"1","created_on":"28\/12\/2013","created_on_time":"07:09 PM","remarks":"Receipt In Hand"},{"receipt_id":"5203","bank_name":"canara bank","payment_mode":"cheque","receipt_amount":"8750","receipt_type":"1","instrument_no":"189377","instrument_date":"1387477800","status":"1","created_on":"21\/12\/2013","created_on_time":"11:30 AM","remarks":"Receipt In Hand"}]}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2045","error_msg","Please enter franchise id"}
				</code>
				<p class="api_desc">
					Api used to get the recent payments done by franchisee.if no data found it return empty response.
				</p>
			</div>
			<div class="api_det">
				<h4>Get franchisee details by mobile number</h4>
				<code>
					<?php echo site_url('api/get_franchise_details')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : authkey=<b>AUTHKEY</b>
					<br><b>Params</b> : username=<b>FRANCHISE_MOBILE_NUMBER</b>   {Mandatory}
					<br><b>Response Type </b> : JSON
					<br><b>Response </b> : {"status":"success","response":{"franchise_details":{"franchise_id":"59","franchise_name":"3G Mobile World","contact_no":"9480205313","contact_no_2":"","territory":"Madikeri","town_name":"Virajpet","is_prepaid":"0","address":"shop no A6,emirates shopping center,Main road , near pvt bus stand, Virajpet-571218 Karnataka.","credit_limit":"526320","contact_person":"Umesh","payment_det":{"pending":449711.1318,"uncleared":"157300"},"cart_total":"21"}}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2003","error_msg","No data found"}
				</code>
				<p class="api_desc">
					Function for get the franchisee deatils by mobile number
				</p>
			</div>

			<div class="api_det">
				<h4>To make payment</h4>
				<code>
					<?php echo site_url('api/make_payment')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>
					<br><b>Params</b> : receipt_amt=<b>RECEIPT AMOUNT</b>   {Mandatory}
					<br><b>Params</b> : bank_name=<b>BANK NAME</b>          {Mandatory}
					<br><b>Params</b> : payment_type=<b>PAYMENT TYPE</b>    {Mandatory}
					<br><b>Params</b> : remarks=<b>REMARKS</b>
					<br><b>Params</b> : instrument_no=<b>INSTRUMENT NUMBER</b>{Mandatory}
					<br><b>Params</b> : instrument_date=<b>INSTRUMENT DATE</b>{Mandatory}
					<br><b>Params</b> : transit_type=<b>TRANSIT TYPE</b>{Mandatory}
					<br><b>Response Type </b> : JSON
					<br><b>Response </b> : {"status":"success","response":{"Receipt Info":{"receipt_id":"7733","franchise_id":"498","receipt_amount":"1000","unreconciliation_amount":"0","receipt_type":"1","payment_mode":"1","bank_name":"kotak","instrument_no":"12335","instrument_date":"1417717800","is_active":"1","is_submitted":"0","is_deposited":"0","status":"0","in_transit":"1","remarks":"Testing","created_by":"68","created_on":"1408531003","activated_by":"0","activated_on":"0","cheq_realized_on":null,"reason":"","modified_by":null,"modified_on":null,"unreconciled_value":null,"unreconciled_status":"pending","courier_name":"DTDC","awb":"AWB123","emp_name":"","contact_no":"0"}}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2045","error_msg","validation_errors"}
				</code>
				<p class="api_desc">
					Api used to make payment.if no receipt is added returns empty response.
				</p>
			</div>
			
			<div class="api_det">
				<h4>Get franchisee payment receipt details</h4>
				<code>
					<?php echo site_url('api/get_franchise_receipts')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>   {Mandatory}
					<br><b>Params</b> : start=<b>RECORT START FROM</b>
					<br><b>Params</b> : limit=<b>RESULT LIMIT</b>
					<br><b>Params</b> : RECEIPT_TYPE=<b>RECEIPT TYPE[types:in_process,processed,realized,cancelled]</b>  {Mandatory}
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"receipts_detail":{"franchise_account_summary":{"pending_payement":"58,627.81","uncleared_payement":0,"adjustments":"29,986","paid_till_date":"14,48,242","credit_note_rised":0,"cancelled":0,"unshipped":25,"shipped":"15,76,100.33","ordered":"17,25,996.74","credit_limit":""},"receipt_list":{"pending":{"total_receipts":"","total_value":"","receipts":"","payment_mode_config":""},"processed":{"total_receipts":"","total_value":"","receipts":"","payment_mode_config":""},"realized":{"total_receipts":65,"total_value":{"total":"1468242"},"receipts":[{"receipt_id":"7351","franchise_id":"220","receipt_amount":"14880","unreconciliation_amount":"0","receipt_type":"1","payment_mode":"1","bank_name":"Canara Bank","instrument_no":"146884","instrument_date":"24\/04\/14","is_active":"1","is_submitted":"1","is_deposited":"0","status":"1","in_transit":"0","remarks":"Receipt In Hand","created_by":"25","created_on":"1398251158","activated_by":"karunakar","activated_on":"25\/04\/14","cheq_realized_on":"2014-04-25","reason":"chq cleared at ICICI Bank","modified_by":"25","modified_on":"1398427678","unreconciled_value":"1","unreconciled_status":"partial","courier_name":null,"awb":null,"emp_name":null,"contact_no":null,"franchise_name":"Ravi Electronics","admin":"karunakar"},{"receipt_id":"7117","franchise_id":"220","receipt_amount":"9540","unreconciliation_amount":"0","receipt_type":"1","payment_mode":"1","bank_name":"canara bank","instrument_no":"146862","instrument_date":"07\/04\/14","is_active":"1","is_submitted":"1","is_deposited":"0","status":"1","in_transit":"0","remarks":"Receipt In Hand","created_by":"25","created_on":"1396862225","activated_by":"karunakar","activated_on":"10\/04\/14","cheq_realized_on":"2014-04-08","reason":"Chq cleared at ICICI Bank","modified_by":"25","modified_on":"1396958708","unreconciled_value":"0","unreconciled_status":"done","courier_name":null,"awb":null,"emp_name":null,"contact_no":null,"franchise_name":"Ravi Electronics","admin":"karunakar"},{"receipt_id":"7015","franchise_id":"220","receipt_amount":"92357","unreconciliation_amount":"0","receipt_type":"1","payment_mode":"1","bank_name":"canara bank","instrument_no":"146856","instrument_date":"28\/03\/14","is_active":"1","is_submitted":"1","is_deposited":"0","status":"1","in_transit":"0","remarks":"Chq received -29.03.2014","created_by":"25","created_on":"1396070110","activated_by":"karunakar","activated_on":"04\/04\/14","cheq_realized_on":"2014-04-04","reason":"Chq cleared at ICICI Bank","modified_by":null,"modified_on":null,"unreconciled_value":"0.05","unreconciled_status":"partial","courier_name":null,"awb":null,"emp_name":null,"contact_no":null,"franchise_name":"Ravi Electronics","admin":"karunakar"},{"receipt_id":"6302","franchise_id":"220","receipt_amount":"4220","unreconciliation_amount":"0","receipt_type":"1","payment_mode":"1","bank_name":"Axis  bank","instrument_no":"283194","instrument_date":"14\/02\/14","is_active":"1","is_submitted":"1","is_deposited":"0","status":"1","in_transit":"0","remarks":"Deposited at ICICI Bank","created_by":"25","created_on":"1392462767","activated_by":"karunakar","activated_on":"17\/02\/14","cheq_realized_on":null,"reason":"chq cleared at ICICI Bank -15.2.2014","modified_by":null,"modified_on":null,"unreconciled_value":"4220","unreconciled_status":"pending","courier_name":null,"awb":null,"emp_name":null,"contact_no":null,"franchise_name":"Ravi Electronics","admin":"karunakar"},{"receipt_id":"5802","franchise_id":"220","receipt_amount":"12200","unreconciliation_amount":"0","receipt_type":"1","payment_mode":"1","bank_name":"canara bank","instrument_no":"193920","instrument_date":"21\/01\/14","is_active":"1","is_submitted":"1","is_deposited":"0","status":"1","in_transit":"0","remarks":"Receipt In Hand","created_by":"25","created_on":"1390284657","activated_by":"karunakar","activated_on":"21\/01\/14","cheq_realized_on":null,"reason":"chq cleared at ICICI Bank-21.1.2014","modified_by":"25","modified_on":"1390307874","unreconciled_value":"12200","unreconciled_status":"pending","courier_name":null,"awb":null,"emp_name":null,"contact_no":null,"franchise_name":"Ravi Electronics","admin":"karunakar"},{"receipt_id":"5473","franchise_id":"220","receipt_amount":"8000","unreconciliation_amount":"0","receipt_type":"1","payment_mode":"1","bank_name":"canara bank","instrument_no":"193905","instrument_date":"05\/01\/14","is_active":"1","is_submitted":"1","is_deposited":"0","status":"1","in_transit":"0","remarks":"Receipt In Hand","created_by":"25","created_on":"1388989684","activated_by":"karunakar","activated_on":"08\/01\/14","cheq_realized_on":null,"reason":"chq cleared at ICICI Bank-7.1.2014","modified_by":"25","modified_on":"1389099519","unreconciled_value":"8000","unreconciled_status":"pending","courier_name":null,"awb":null,"emp_name":null,"contact_no":null,"franchise_name":"Ravi Electronics","admin":"karunakar"},{"receipt_id":"5299","franchise_id":"220","receipt_amount":"2101","unreconciliation_amount":"0","receipt_type":"1","payment_mode":"1","bank_name":"canara bank","instrument_no":"189385","instrument_date":"28\/12\/13","is_active":"1","is_submitted":"1","is_deposited":"0","status":"1","in_transit":"0","remarks":"Receipt In Hand","created_by":"25","created_on":"1388237982","activated_by":"karunakar","activated_on":"01\/01\/14","cheq_realized_on":null,"reason":"chq cleared at ICICI Bank- 30.12.2013","modified_by":"25","modified_on":"1388406251","unreconciled_value":"2101","unreconciled_status":"pending","courier_name":null,"awb":null,"emp_name":null,"contact_no":null,"franchise_name":"Ravi Electronics","admin":"karunakar"},{"receipt_id":"5300","franchise_id":"220","receipt_amount":"22222","unreconciliation_amount":"0","receipt_type":"1","payment_mode":"1","bank_name":"canara bank","instrument_no":"189386","instrument_date":"28\/12\/13","is_active":"1","is_submitted":"1","is_deposited":"0","status":"1","in_transit":"0","remarks":"Receipt In Hand","created_by":"25","created_on":"1388238028","activated_by":"karunakar","activated_on":"01\/01\/14","cheq_realized_on":null,"reason":"chq cleared at ICICI Bank- 30.12.2013","modified_by":"25","modified_on":"1388406368","unreconciled_value":"22222","unreconciled_status":"pending","courier_name":null,"awb":null,"emp_name":null,"contact_no":null,"franchise_name":"Ravi Electronics","admin":"karunakar"},{"receipt_id":"5203","franchise_id":"220","receipt_amount":"8750","unreconciliation_amount":"0","receipt_type":"1","payment_mode":"1","bank_name":"canara bank","instrument_no":"189377","instrument_date":"20\/12\/13","is_active":"1","is_submitted":"1","is_deposited":"0","status":"1","in_transit":"0","remarks":"Receipt In Hand","created_by":"25","created_on":"1387605629","activated_by":"karunakar","activated_on":"26\/12\/13","cheq_realized_on":null,"reason":"chq cleared at ICICI Bank-23.12.2013","modified_by":"25","modified_on":"1387800491","unreconciled_value":"8750","unreconciled_status":"pending","courier_name":null,"awb":null,"emp_name":null,"contact_no":null,"franchise_name":"Ravi Electronics","admin":"karunakar"},{"receipt_id":"5202","franchise_id":"220","receipt_amount":"9000","unreconciliation_amount":"0","receipt_type":"1","payment_mode":"1","bank_name":"canara bank","instrument_no":"189376","instrument_date":"20\/12\/13","is_active":"1","is_submitted":"1","is_deposited":"0","status":"1","in_transit":"0","remarks":"Receipt In Hand","created_by":"25","created_on":"1387605582","activated_by":"karunakar","activated_on":"26\/12\/13","cheq_realized_on":null,"reason":"chq cleared at ICICI Bank-23.12.2013","modified_by":"25","modified_on":"1387800472","unreconciled_value":"9000","unreconciled_status":"pending","courier_name":null,"awb":null,"emp_name":null,"contact_no":null,"franchise_name":"Ravi Electronics","admin":"karunakar"}],"payment_mode_config":["cash","Cheque","DD","Transfer"]},"cancelled":{"total_receipts":"","total_value":"","receipts":"","payment_mode_config":""}},"receipt_status_config":["Pending","Activated","Cancelled","Reversed"],"receipt_type_config":["Deposit","Topup"]}}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2016","error_msg","Franchise id not found"}
				</code>
				<p class="api_desc">
					get franchise payment receipt details,defaultly all the receipts record limit 10,want more receipt then give the which receipt[type] and record start from [start] and record limit [limit].
				</p>
			</div>
			<!-- Payment API ---------->
			
			<!-- IMEI Activation APIs --->
			<div class="api_det">
				<h4>Get imei details</h4>
				<code>
					<?php echo site_url('api/get_imei_details')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>
					<br><b>Params</b> : imei_no=<b>IMEI NO</b>    {Mandatory}
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"imei_detail":{"userid":"75880","invoice_no":"20141002799","product_name":"Samsung Galaxy Young S6312 (Metallic Silver)","transid":"PNHKIY25346","franchise_id":"220","franchise_name":"Ravi Electronics","name":"Samsung Galaxy Young S6312 (Metallic Silver)","imei_no":"354655054528602","status":"1","imei_scheme_id":"0","imei_reimbursement_value_perunit":"0.00","member_id":"20016851","ordered_on":"2013-06-03","is_imei_activated":"0","mobileno":"9886744646"}}}
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
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>   {Mandatory}
					<br><b>Params</b> : mobile_no=<b>MOBILE NO</b>   {Mandatory}
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"validation_res":{"member_id":"21111111","pending_activation":false,"status":1,"status_config":{"1":"Existing member","2":"New mobileno"}}}}
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
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>
					<br><b>Params</b> : mobile_no=<b>MOBILE NO</b>   {Mandatory}
					<br><b>Params</b> : imei_no=<b>IMEI NUMBER</b>   {Mandatory}
					<br><b>Params</b> : actv_confrim=<b>ACTIVATION CONFIRMATION FLAG[if activation require to purchased member then set flag to 1 or Activation require to another existing member then set flag to 1,if new member then no need of flag ]</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"IMEI_activation_res":"IMEI\/Serailno Activation processed"}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2036","error_msg","Sorry not able to process IMEI activation"}
				</code>
				<p class="api_desc">
					This api used to activate the IMEI number to member 
				</p>
			</div>
			<!-- IMEI Activation APIs --->
			
			<!----------- Offers API Start  ---------->
			<div class="api_det">
				<h4>Current Offers</h4>
				<code>
					<?php echo site_url('api/get_store_offers')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : store_menu_id=<b>GROUP ID</b>
					<br><b>Response Type </b> : JSON
					<br><b>Success Response </b> : {"status":"success","response":{"banners":[{"banner_name":"Electronics","banner_link":"http:\/\/snapittoday.com\/resources\/banners\/1.png"}],"offer_product_list":[{"id":"19","pid":"10018704","name":"LG Led 32 Inches-32LN5150","mrp":"32000","price":"32000.00","mp_max_allow_qty":"10","disc":"0","pimg_link":"http:\/\/static.snapittoday.com\/items\/small\/ahchdl9m75g681i.jpg","mp_offer_to":"2014-10-01 12:12:12"},{"id":"23","pid":"11582143","name":"Samsung ECA-P10CBECINU Car Charger","mrp":"1899","price":"1899.00","mp_max_allow_qty":"10","disc":"0","pimg_link":"http:\/\/static.snapittoday.com\/items\/small\/2bof6lgblj6j1po.jpg","mp_offer_to":"2014-10-01 12:12:12"},{"id":"18","pid":"10005650","name":"Apple iPhone 5C 16GB (White)","mrp":"41900","price":"41900.00","mp_max_allow_qty":"10","disc":"0","pimg_link":"http:\/\/static.snapittoday.com\/items\/small\/bk7eh39njmm4i2d.jpg","mp_offer_to":"2014-09-30 19:13:00"}]}}
			
				<p class="api_desc">
						function to get current offers
				</p>
				</code>
			</div>
			
			<div class="api_det">
				<h4>Upcoming Offers</h4>
				<code>
					<?php echo site_url('api/get_upcoming_offers')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : store_menu_id=<b>GROUP ID</b>
					<br><b>Response Type </b> : JSON
					<br><b>Success Response </b> : {"status":"success","response":{"banners":[{"banner_name":"Electronics","banner_link":"http:\/\/snapittoday.com\/resources\/banners\/1.png"}],"offer_product_list":[{"id":"20","pid":"10018995","name":"LG GL-408YVQ4 390 l Refrigerator ( Stainless Steel )","mrp":"38300","price":"38300.00","disc":"0","pimg_link":"http:\/\/static.snapittoday.com\/items\/small\/n840d6fed3cbe2f.jpg","mp_offer_from":"2014-10-02 12:12:12","mp_offer_to":"0000-00-00 00:00:00"},{"id":"22","pid":"16812167","name":"VARTA Power Line LED Light 4 AA","mrp":"990","price":"990.00","disc":"0","pimg_link":"http:\/\/static.snapittoday.com\/items\/small\/m3kef9idm6mi466.jpg","mp_offer_from":"2014-11-01 12:08:12","mp_offer_to":"0000-00-00 00:00:00"},{"id":"20","pid":"10018993","name":"LG GL-348PLQ4 335 l Refrigerator ( PV )","mrp":"36850","price":"36850.00","disc":"0","pimg_link":"http:\/\/static.snapittoday.com\/items\/small\/7k06e88gjp63l1e.jpg","mp_offer_from":"2014-09-29 21:12:12","mp_offer_to":"0000-00-00 00:00:00"},{"id":"18","pid":"10005904","name":"Combo of Nokia 105 X 6 Quantities and Micromax Bolt A27 (Black)","mrp":"12153","price":"10405.00","disc":"14","pimg_link":"http:\/\/static.snapittoday.com\/items\/small\/l6kk5503741fohl.jpg","mp_offer_from":"2014-12-14 12:12:12","mp_offer_to":"0000-00-00 00:00:00"}]}}
					
				<p class="api_desc">
						function to get upcoming offers
				</p>
				</code>
			</div>
			<!----------- Offers API End  ---------->
			
			<!-- Returns APIs --->
			<div class="api_det">
				<h4>To get invoice/imei details</h4>
				<code>
					<?php echo site_url('api/orders_details_byinvoiceno')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : authkey=<b>AUTHKEY</b>
					<br><b>Params</b> : invoice_no=<b>Invoice NO</b>
					<br><b>Params</b> : franchise_id=<b>Franchise ID</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"items_list":{"itemlist":{"6219812964":{"invoice_no":"20141025880","order_id":"6219812964","itemid":"1329383151","name":"Philips Dry Iron HD1134","quantity":"1","packed":"1","shipped":"1","shipped_on":"2014-06-24 12:02:24","transit_type":"0","invoice_status":"1","transid":"PNH71789","product_list":[[{"is_shipped":null,"shipped_on":null,"is_refunded":null,"is_stocked":null,"is_serial_required":"1","order_id":"6219812964","product_id":"12139","product_name":"Philips Dry Iron HD1134","qty":"1","pen_return_qty":"0","has_barcode":"1"}]]}}},"returns_condition":{"1":"Good Condition","2":"Duplicate product","3":"UnOrdered Product","4":"Late Shipment","5":"Address not found","6":"Faulty and needs service"}}}
				<p class="api_desc">
						Function to get product details by invoice/imei
				</p>
				</code>
			</div>
			
			<div class="api_det">
				<h4>get return list</h4>
				<code>
					<?php echo site_url('api/get_returns')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>   {Mandatory}
					<br><b>Params</b> : start=<b>RECORD START</b>
					<br><b>Params</b> : limit=<b>LIMIT</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"returns_list":{"530":{"return_id":"530","transid":"PNHUSU18475","invoice_no":"20141025483","returned_date":"18\/08\/2014 03:07 pm","status":"0","status_config":["Pending","Updated","Closed"],"products":[{"is_packed":"0","readytoship":"0","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"0","is_shipped":"0","is_stocked":"0","return_product_id":"713","return_id":"530","order_id":"3935425714","product_id":"156280","product_name":"Micromax Canvas 2.2 A114 (Random Color)","qty":"1","barcode":"","imei_no":"1567\/842847941","condition_type":"2","status":"3"},{"is_packed":"0","readytoship":"0","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"0","is_shipped":"0","is_stocked":"0","return_product_id":"714","return_id":"530","order_id":"9493457916","product_id":"156015","product_name":"Micromax Canvas Doodle 2 A240 (Random Color)","qty":"1","barcode":"","imei_no":"458778104720","condition_type":"3","status":"0"}],"prd_status_config":["Pending","Out for Service","Move to Warehouse Stock","Ready to ship"]},"529":{"return_id":"529","transid":"PNHMSJ88892","invoice_no":"20141025463","returned_date":"16\/08\/2014 11:37 am","status":"0","status_config":["Pending","Updated","Closed"],"products":[{"is_packed":"0","readytoship":"0","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"0","is_shipped":"0","is_stocked":"0","return_product_id":"712","return_id":"529","order_id":"8129517517","product_id":"8527","product_name":"Karbonn Jumbo K9 (Random Color)","qty":"1","barcode":"","imei_no":"5478947895476590486","condition_type":"2","status":"3"}],"prd_status_config":["Pending","Out for Service","Move to Warehouse Stock","Ready to ship"]},"528":{"return_id":"528","transid":"PNHJTS52642","invoice_no":"20141025471","returned_date":"08\/08\/2014 03:40 pm","status":"2","status_config":["Pending","Updated","Closed"],"products":[{"is_packed":"0","readytoship":"0","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"1","is_shipped":"0","is_stocked":"1","return_product_id":"711","return_id":"528","order_id":"5937831157","product_id":"155851","product_name":"Nokia 108 (Random color)","qty":"1","barcode":"","imei_no":"564896745152347851","condition_type":"3","status":"2"}],"prd_status_config":["Pending","Out for Service","Move to Warehouse Stock","Ready to ship"]},"527":{"return_id":"527","transid":"PNHXAV14394","invoice_no":"20141025421","returned_date":"12\/07\/2014 04:09 pm","status":"1","status_config":["Pending","Updated","Closed"],"products":[{"is_packed":"0","readytoship":"0","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"0","is_shipped":"0","is_stocked":"0","return_product_id":"709","return_id":"527","order_id":"6335714296","product_id":"230194","product_name":"REDTAPE Textured Finish Derby Shoes Brown (289867)-size:7","qty":"1","barcode":"","imei_no":null,"condition_type":"2","status":"0"},{"is_packed":"0","readytoship":"1","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"0","is_shipped":"0","is_stocked":"0","return_product_id":"710","return_id":"527","order_id":"6335714296","product_id":"230194","product_name":"REDTAPE Textured Finish Derby Shoes Brown (289867)-size:7","qty":"1","barcode":"","imei_no":null,"condition_type":"3","status":"3"}],"prd_status_config":["Pending","Out for Service","Move to Warehouse Stock","Ready to ship"]},"526":{"return_id":"526","transid":"PNHHSM17639","invoice_no":"20141025420","returned_date":"12\/07\/2014 03:53 pm","status":"0","status_config":["Pending","Updated","Closed"],"products":[{"is_packed":"0","readytoship":"0","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"0","is_shipped":"0","is_stocked":"0","return_product_id":"708","return_id":"526","order_id":"8346775382","product_id":"230194","product_name":"REDTAPE Textured Finish Derby Shoes Brown (289867)-size:7","qty":"1","barcode":"","imei_no":null,"condition_type":"2","status":"0"}],"prd_status_config":["Pending","Out for Service","Move to Warehouse Stock","Ready to ship"]},"525":{"return_id":"525","transid":"PNHBLH51567","invoice_no":"20141025419","returned_date":"12\/07\/2014 03:40 pm","status":"1","status_config":["Pending","Updated","Closed"],"products":[{"is_packed":"0","readytoship":"0","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"0","is_shipped":"0","is_stocked":"0","return_product_id":"706","return_id":"525","order_id":"5454199271","product_id":"152939","product_name":"Vaseline - Men Antispots Whitening Face Cream SPF 15 - 30g","qty":"1","barcode":"","imei_no":null,"condition_type":"3","status":"1"},{"is_packed":"0","readytoship":"0","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"1","is_shipped":"0","is_stocked":"1","return_product_id":"707","return_id":"525","order_id":"7232831821","product_id":"122066","product_name":"Olay Insta Glow Fairness Serum (20g)","qty":"1","barcode":"","imei_no":null,"condition_type":"3","status":"2"}],"prd_status_config":["Pending","Out for Service","Move to Warehouse Stock","Ready to ship"]},"524":{"return_id":"524","transid":"PNHBCX92941","invoice_no":"20141025418","returned_date":"12\/07\/2014 03:12 pm","status":"1","status_config":["Pending","Updated","Closed"],"products":[{"is_packed":"0","readytoship":"1","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"0","is_shipped":"0","is_stocked":"0","return_product_id":"703","return_id":"524","order_id":"1628223679","product_id":"185575","product_name":"Xolo A500 CLUB (Black)","qty":"1","barcode":"","imei_no":"911360651004315","condition_type":"5","status":"3"},{"is_packed":"0","readytoship":"0","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"1","is_shipped":"0","is_stocked":"1","return_product_id":"704","return_id":"524","order_id":"8525155248","product_id":"190918","product_name":"Samsung GALAXY Note 3 Neo (Black)","qty":"1","barcode":"","imei_no":"456456451200","condition_type":"3","status":"2"},{"is_packed":"0","readytoship":"0","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"0","is_shipped":"0","is_stocked":"0","return_product_id":"705","return_id":"524","order_id":"8779154497","product_id":"185576","product_name":"Xolo A500 Club (White) ","qty":"1","barcode":"","imei_no":"43543654756","condition_type":"4","status":"1"}],"prd_status_config":["Pending","Out for Service","Move to Warehouse Stock","Ready to ship"]},"523":{"return_id":"523","transid":"PNHFFR73524","invoice_no":"20141025416","returned_date":"12\/07\/2014 12:55 pm","status":"2","status_config":["Pending","Updated","Closed"],"products":[{"is_packed":"0","readytoship":"0","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"1","is_shipped":"0","is_stocked":"1","return_product_id":"700","return_id":"523","order_id":"1567886781","product_id":"225896","product_name":"Nokia X (Dual SIM, White)","qty":"1","barcode":"","imei_no":"352361061780623","condition_type":"3","status":"2"},{"is_packed":"0","readytoship":"0","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"1","is_shipped":"0","is_stocked":"1","return_product_id":"701","return_id":"523","order_id":"2931216384","product_id":"233365","product_name":"Micromax Bolt  A26 (Black)","qty":"1","barcode":"","imei_no":"911358300858453","condition_type":"2","status":"2"},{"is_packed":"0","readytoship":"1","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"0","is_shipped":"0","is_stocked":"0","return_product_id":"702","return_id":"523","order_id":"8767335577","product_id":"225699","product_name":"XOLO Q700S (Gold)","qty":"1","barcode":"","imei_no":"911353050479329","condition_type":"4","status":"3"}],"prd_status_config":["Pending","Out for Service","Move to Warehouse Stock","Ready to ship"]},"522":{"return_id":"522","transid":"PNHJDC65597","invoice_no":"20141025415","returned_date":"12\/07\/2014 11:16 am","status":"0","status_config":["Pending","Updated","Closed"],"products":[{"is_packed":"0","readytoship":"0","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"0","is_shipped":"0","is_stocked":"0","return_product_id":"696","return_id":"522","order_id":"2883499859","product_id":"217685","product_name":"Sony Xperia E1 Dual (Black)","qty":"1","barcode":"","imei_no":"9897689897876","condition_type":"2","status":"1"},{"is_packed":"0","readytoship":"0","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"0","is_shipped":"0","is_stocked":"0","return_product_id":"697","return_id":"522","order_id":"5273426268","product_id":"217685","product_name":"Sony Xperia E1 Dual (Black)","qty":"1","barcode":"","imei_no":"352946061830665","condition_type":"3","status":"0"},{"is_packed":"0","readytoship":"0","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"0","is_shipped":"0","is_stocked":"0","return_product_id":"698","return_id":"522","order_id":"5459767723","product_id":"8702","product_name":"Samsung Metro DUOS C3322 (Black)","qty":"1","barcode":"","imei_no":"3127481254","condition_type":"2","status":"0"},{"is_packed":"0","readytoship":"0","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"0","is_shipped":"0","is_stocked":"0","return_product_id":"699","return_id":"522","order_id":"7783537533","product_id":"8702","product_name":"Samsung Metro DUOS C3322 (Black)","qty":"1","barcode":"","imei_no":"79089068","condition_type":"5","status":"0"}],"prd_status_config":["Pending","Out for Service","Move to Warehouse Stock","Ready to ship"]},"521":{"return_id":"521","transid":"PNHZHP87848","invoice_no":"20141025414","returned_date":"11\/07\/2014 06:41 pm","status":"1","status_config":["Pending","Updated","Closed"],"products":[{"is_packed":"0","readytoship":"0","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"0","is_shipped":"0","is_stocked":"0","return_product_id":"690","return_id":"521","order_id":"1654473527","product_id":"217685","product_name":"Sony Xperia E1 Dual (Black)","qty":"1","barcode":"","imei_no":"9897689897876","condition_type":"2","status":"0"},{"is_packed":"0","readytoship":"0","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"0","is_shipped":"0","is_stocked":"0","return_product_id":"691","return_id":"521","order_id":"3323618454","product_id":"8702","product_name":"Samsung Metro DUOS C3322 (Black)","qty":"1","barcode":"","imei_no":"1445646","condition_type":"3","status":"1"},{"is_packed":"0","readytoship":"0","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"1","is_shipped":"0","is_stocked":"1","return_product_id":"692","return_id":"521","order_id":"5596857692","product_id":"225441","product_name":"Sony Xperia M Dual (Purple)","qty":"1","barcode":"","imei_no":"352709060541349","condition_type":"5","status":"2"},{"is_packed":"0","readytoship":"0","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"0","is_shipped":"0","is_stocked":"0","return_product_id":"693","return_id":"521","order_id":"6175943636","product_id":"225441","product_name":"Sony Xperia M Dual (Purple)","qty":"1","barcode":"","imei_no":"352709060541687","condition_type":"6","status":"0"},{"is_packed":"0","readytoship":"0","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"0","is_shipped":"0","is_stocked":"0","return_product_id":"694","return_id":"521","order_id":"6448281554","product_id":"8702","product_name":"Samsung Metro DUOS C3322 (Black)","qty":"1","barcode":"","imei_no":"83964782483989","condition_type":"4","status":"1"},{"is_packed":"0","readytoship":"1","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"0","is_shipped":"0","is_stocked":"0","return_product_id":"695","return_id":"521","order_id":"8786371543","product_id":"217685","product_name":"Sony Xperia E1 Dual (Black)","qty":"1","barcode":"","imei_no":"352946061830665","condition_type":"1","status":"3"}],"prd_status_config":["Pending","Out for Service","Move to Warehouse Stock","Ready to ship"]}},"ttl_returns":24}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2040","error_msg","FranchiseID require"}
				</code>
				<p class="api_desc">
					Api used to get the return product details,if no records found the return list returns empty
				</p>
			</div>
			
			<div class="api_det">
				<h4>view return details</h4>
				<code>
					<?php echo site_url('api/return_details')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : return_id=<b>RETURN ID</b>    {Mandatory}
					<br><b>Params</b> : franchise_id=<b>FRANCHISE ID</b>    {Mandatory}
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"return_details":{"return_details":{"530":{"return_id":"530","transid":"PNHUSU18475","invoice_no":"20141025483","returned_date":"18\/08\/2014 03:07 pm","status":"0","status_config":["Pending","Updated","Closed"],"return_remarks_update_res":[{"product_status":"Ready to ship","return_id":"530","created_on":"18\/08\/2014 03:10 PM","remarks":"<b>Amount Refunded to Franchise : 11304.0975 <\/b><br \/><b>DOA PO created successfully PO ID : 8399 <\/b><p><b>Remarks :<\/b>gfhgfhj<\/p>","transit_mode":null,"courier":null,"awb":null,"emp_phno":null,"emp_name":null,"logged_by":null,"is_refunded":"No","is_shipped":"No","is_stocked":"No"},{"product_status":"Move to Warehouse Stock","return_id":"530","created_on":"18\/08\/2014 03:09 PM","remarks":"<b>Service Center :<\/b>ghgfh<br \/><b>Job Sheet Number :<\/b>1223<br \/><b>Expected On :<\/b>2014-08-18 15:19<br \/><p><b>Remarks :<\/b>gfdhgdfh<\/p>","transit_mode":null,"courier":null,"awb":null,"emp_phno":null,"emp_name":null,"logged_by":null,"is_refunded":"No","is_shipped":"No","is_stocked":"No"},{"product_status":"Out for Service","return_id":"530","created_on":"18\/08\/2014 03:08 PM","remarks":"<b>Sent via :<\/b>Naveen Kumar G N<br \/><p><b>Remarks :<\/b>rtyrtyt<\/p>","transit_mode":null,"courier":null,"awb":null,"emp_phno":null,"emp_name":null,"logged_by":null,"is_refunded":"No","is_shipped":"No","is_stocked":"No"},{"product_status":"Pending","return_id":"530","created_on":"18\/08\/2014 03:07 PM","remarks":"  \nTesting","transit_mode":null,"courier":null,"awb":null,"emp_phno":null,"emp_name":null,"logged_by":null,"is_refunded":"No","is_shipped":"No","is_stocked":"No"},{"product_status":"Pending","return_id":"530","created_on":"18\/08\/2014 03:07 PM","remarks":"fdesfds","transit_mode":null,"courier":null,"awb":null,"emp_phno":null,"emp_name":null,"logged_by":null,"is_refunded":"No","is_shipped":"No","is_stocked":"No"}],"products":[{"is_packed":"0","readytoship":"0","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"No","is_shipped":"No","is_stocked":"No","return_product_id":"713","return_id":"530","order_id":"3935425714","product_id":"156280","product_name":"Micromax Canvas 2.2 A114 (Random Color)","qty":"1","barcode":"","imei_no":"1567\/842847941","condition_type":"Duplicate product","status":"na","transit_mode":null,"courier":null,"awb":null,"emp_phno":null,"emp_name":null,"logged_by":null},{"is_packed":"0","readytoship":"0","franchise_id":"498","franchise_name":"Testing Franchise","is_refunded":"No","is_shipped":"No","is_stocked":"No","return_product_id":"714","return_id":"530","order_id":"9493457916","product_id":"156015","product_name":"Micromax Canvas Doodle 2 A240 (Random Color)","qty":"1","barcode":"","imei_no":"458778104720","condition_type":"UnOrdered Product","status":"pending","transit_mode":null,"courier":null,"awb":null,"emp_phno":null,"emp_name":null,"logged_by":null}],"prd_status_config":["Pending","Out for Service","Move to Warehouse Stock","Ready to ship"]}},"ttl_returns":2}}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2040","error_msg","RETURN ID require"}
				</code>
				<p class="api_desc">
					Api used to view return details of a ticket raised,if no records found the returns  empty
				</p>
			</div>
			
			<div class="api_det">
				<h4>To add return</h4>
				<code>
					<?php echo site_url('api/add_pnh_invoice_return')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : authkey=<b>AUTHKEY</b>
					<br><b>Params</b> : invoice_no=<b>Invoice NO</b>
					<br><b>Params</b> : order_id=<b>ORDER ID</b>
					<br><b>Params</b> : prod_rcvd_pid=<b>PRODUCT ID</b>       {Mandatory}
					<br><b>Params</b> : prod_rcvd_qty=<b>PRODUCT QTY</b>	  {Mandatory}
					<br><b>Params</b> : prod_rcvd_bcd=<b>PRODUCT BARCODE</b>
					<br><b>Params</b> : prod_rcvd_cond=<b>PRODUCT CONDITION</b>
					<br><b>Params</b> : prod_rcvd_remarks=<b>REMARKS</b>
					<br><b>Params</b> : prod_rcvd_imeino=<b>PRODUCT Serial No</b>  {Mandatory if product have imei}
					<br><b>Params</b> : prod_rcvd_transtype=<b>PRODUCT TRANSIT TYPE</b> 1:Courier 2:executive   {Mandatory}
					<br><b>Params</b> : prod_rcvd_courierid=<b>COURIER ID</b>    {Mandatory}
					<br><b>Params</b> : prod_rcvd_awb=<b>AIR WAY BILL NUMBER</b>  {Mandatory}
					<br><b>Params</b> : prod_rcvd_empname=<b>EMPLOYEE Name</b>    {Mandatory}
					<br><b>Params</b> : prod_rcvd_empph=<b>EMPLOYEE Phone Number</b>  {Mandatory}
					<br><b>Params</b> : franchise_id=<b>FRANCHISEEE ID</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"Return ID":631,"Ticket ID":1748}}
					<p class="api_desc">
						function logs the return and raises ticket for the return 
					</p>
				</code>
			</div>
			<!-- Returns APIs --->
			
			<!-- Franchisee Orders/Shipments ------>
			<div class="api_det">
				<h4>Get transaction list </h4>
				<code>
					<?php echo site_url('api/get_transaction_list')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>
					<br><b>Params</b> : start=<b>RECORT START FROM</b>
					<br><b>Params</b> : limit=<b>RESULT LIMIT</b>
					<br><b>Params</b> : date_from=<b>DATE FROM[YY-MM-DD]</b>
					<br><b>Params</b> : date_to=<b>DATE TO[YY-MM-DD]</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"transactio_list":{"PNHQGI55391":{"orders":[{"order_id":"3934914172","member_id":"21111111","deal_name":"Samsung Galaxy Grand Neo GT-I9060 (White)","item_id":"2215134531","qty":"1","mrp":"19010","price":16366,"status":0}],"trans_id":"PNHQGI55391","trans_date":"19\/08\/2014 06:03 pm","amount":16366,"commission":334,"status":0,"order_status_config":["Pending","Pending","Shipped","Cancelled","Returned","Invoiced"],"trans_status_config":["UnShipped","Partitally Shipped","Shipped","Cancelled"]},"PNHWGX65199":{"orders":[{"order_id":"7715176585","member_id":"21111111","deal_name":"Lakme Perfect Radiance Intense Whitening Night Repair Cr","item_id":"3442136943","qty":"2","mrp":"299","price":240.96,"status":0}],"trans_id":"PNHWGX65199","trans_date":"18\/08\/2014 12:46 pm","amount":481.92,"commission":10.04,"status":0,"order_status_config":["Pending","Pending","Shipped","Cancelled","Returned","Invoiced"],"trans_status_config":["UnShipped","Partitally Shipped","Shipped","Cancelled"]},"PNHHDQ28297":{"orders":[{"order_id":"4467248924","member_id":"21111112","deal_name":"Lakme Perfect Radiance Intense Whitening Night Repair Cr","item_id":"3442136943","qty":"1","mrp":"299","price":240,"status":0}],"trans_id":"PNHHDQ28297","trans_date":"18\/08\/2014 12:44 pm","amount":240,"commission":10,"status":0,"order_status_config":["Pending","Pending","Shipped","Cancelled","Returned","Invoiced"],"trans_status_config":["UnShipped","Partitally Shipped","Shipped","Cancelled"]},"PNHUSB65895":{"orders":[{"order_id":"5132568545","member_id":"21111112","deal_name":"Samsung Star Pro S7262 (White)","item_id":"5717134917","qty":"1","mrp":"6470","price":5870.2,"status":0}],"trans_id":"PNHUSB65895","trans_date":"18\/08\/2014 12:30 pm","amount":5870.2,"commission":119.8,"status":0,"order_status_config":["Pending","Pending","Shipped","Cancelled","Returned","Invoiced"],"trans_status_config":["UnShipped","Partitally Shipped","Shipped","Cancelled"]},"PNHRZU54439":{"orders":[{"order_id":"9754153471","member_id":"21111112","deal_name":"Samsung Star Pro S7262 (White)","item_id":"5717134917","qty":"4","mrp":"6470","price":5870.2,"status":0}],"trans_id":"PNHRZU54439","trans_date":"18\/08\/2014 12:29 pm","amount":23480.8,"commission":119.8,"status":0,"order_status_config":["Pending","Pending","Shipped","Cancelled","Returned","Invoiced"],"trans_status_config":["UnShipped","Partitally Shipped","Shipped","Cancelled"]},"PNHCKX38947":{"orders":[{"order_id":"9417391258","member_id":"21111111","deal_name":"Samsung Star Pro S7262 (White)","item_id":"5717134917","qty":"1","mrp":"6470","price":4909.8,"status":0}],"trans_id":"PNHCKX38947","trans_date":"18\/08\/2014 12:06 pm","amount":4909.8,"commission":100.2,"status":0,"order_status_config":["Pending","Pending","Shipped","Cancelled","Returned","Invoiced"],"trans_status_config":["UnShipped","Partitally Shipped","Shipped","Cancelled"]},"PNHGAW55611":{"orders":[{"order_id":"9947718233","member_id":"22028503","deal_name":"Karbonn Jumbo K9 (Random Color)","item_id":"8456917382","qty":"1","mrp":"1690","price":1430,"status":0}],"trans_id":"PNHGAW55611","trans_date":"16\/08\/2014 11:59 am","amount":1430,"commission":0,"status":0,"order_status_config":["Pending","Pending","Shipped","Cancelled","Returned","Invoiced"],"trans_status_config":["UnShipped","Partitally Shipped","Shipped","Cancelled"]},"PNHXIE49375":{"orders":[{"order_id":"6768123149","member_id":"21111152","deal_name":"Himalaya Baby Shampoo - 200ml","item_id":"7466843812","qty":"1","mrp":"149","price":148.11,"status":0},{"order_id":"2655173653","member_id":"21111152","deal_name":"Himalaya Babycare Gift Series Basket (7 pcs)","item_id":"2118682199","qty":"1","mrp":"600","price":596.4,"status":0},{"order_id":"2254784457","member_id":"21111152","deal_name":"Huggies Total Protection Large 5 Pieces","item_id":"5956446396","qty":"1","mrp":"70","price":65,"status":0}],"trans_id":"PNHXIE49375","trans_date":"14\/08\/2014 07:35 pm","amount":809.51,"commission":0,"status":0,"order_status_config":["Pending","Pending","Shipped","Cancelled","Returned","Invoiced"],"trans_status_config":["UnShipped","Partitally Shipped","Shipped","Cancelled"]},"PNHJHJ91571":{"orders":[{"order_id":"7713644258","member_id":"22028503","deal_name":"Samsung Galaxy Grand Neo GT-I9060 (White)","item_id":"2215134531","qty":"1","mrp":"19010","price":15974,"status":2},{"order_id":"8733716976","member_id":"22028503","deal_name":"Samsung Galaxy Grand Neo GT-I9060 (White)","item_id":"2215134531","qty":"1","mrp":"19010","price":15974,"status":2}],"trans_id":"PNHJHJ91571","trans_date":"14\/08\/2014 07:10 pm","amount":31948,"commission":652,"status":2,"order_status_config":["Pending","Pending","Shipped","Cancelled","Returned","Invoiced"],"trans_status_config":["UnShipped","Partitally Shipped","Shipped","Cancelled"]},"PNHIEX79787":{"orders":[{"order_id":"8453376752","member_id":"22028503","deal_name":"Samsung Galaxy Grand Neo GT-I9060 (White)","item_id":"2215134531","qty":"1","mrp":"19010","price":15925,"status":0}],"trans_id":"PNHIEX79787","trans_date":"14\/08\/2014 07:09 pm","amount":15925,"commission":325,"status":0,"order_status_config":["Pending","Pending","Shipped","Cancelled","Returned","Invoiced"],"trans_status_config":["UnShipped","Partitally Shipped","Shipped","Cancelled"]}},"ttl_trans":546}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2016","error_msg","Franchise id not found"}
				</code>
				<p class="api_desc">
					get transaction list by franchise
				</p>
			</div>
			
			<div class="api_det">
				<h4>View Franchise Orders</h4>
				<code>
					<?php echo site_url('api/view_franchise_orders')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : authkey=<b>AUTHKEY</b>
					<br><b>Params</b> : username=<b>FRANCHISE_MOBILE_NUMBER</b>   {Mandatory} 
					<br><b>Params</b> : from=<b>FROM_DATE</b>(Optional) Format: DD/MM/YYYY
					<br><b>Params</b> : to=<b>TO_DATE</b>(Optional) Format: DD/MM/YYYY
					<br><b>Response Type </b> : JSON
					<br><b>Response </b> : {"status":"success","response":{"PNHXPZ31423":{"transid":"PNHXPZ31423","actiontime":"22\/10\/2013 01:48 pm","franchise_name":"Jamal Enterpriise","created_by":"kiran","orders":[{"itemid":"3453341159","orderid":"3897621187","pnh_id":"10005494","name":"Storeking android device1","quantity":"1","i_orgprice":"8000","amount":8000,"o_status":"yes"},{"itemid":"4364998264","orderid":"7773128745","pnh_id":"11563368","name":"Storeking monitor","quantity":"1","i_orgprice":"13500","amount":9500,"o_status":"yes"}]}}}
					<br><b>Response On Error </b> : {"status":"error","error_code":2000,"error_msg":"No orders found."}
				</code>
				<p class="api_desc">
					Function to view orders of the login franchise-view between date range or recent 10 orders
				</p>
			</div>
			
			<div class="api_det">
				<h4>Orders details for given transid</h4>
				<code>
					<?php echo site_url('api/orders_bytransid')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : authkey=<b>AUTHKEY</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISEEE ID</b>
					<br><b>Params</b> : transid=<b>TRANS ID</b>    {Mandatory}
					
					<br><b>Response Type </b> : JSON
					<br><b>Response </b> : {"status":"success","response":{"order_details":{"PNHAKS39887":{"transid":"PNHAKS39887","actiontime":"03\/04\/2014 03:26 pm","franchise_name":"Ultimate Electronic","created_by":"gurusidappa","trans_ttl_cost":34650.5,"franchise_address":"","order_for":"Member order","member_name":"Arun","member_address":"","pincode":"","member_email":"","member_mobile":"9060889333","orderfor_status":"0","pnh_member_fee":"0","ship_person":"Ultimate Electronic","ship_address":"Gulbarga, Shop No. 3, KHB Complex, Opp.Central Bus Stand,","orders":[{"itemid":"1594754298","orderid":"7666567698","pnh_id":"10005570","name":"LG 32LN571B LED Television","status":"2","quantity":"1","i_orgprice":"38500","image_url":"http:\/\/static.snapittoday.com\/items\/oe9g1gn1m0d3l1a.jpg","amount":34650.5,"status_msg":"Shipped","member_id":"22025118","member_name":"Arun","member_phno":"9060889333","member_address":"","mem_add_pincode":"","member_email":"","commission":"2349.5","invoice_no":"20141024095","member_fee":"0.00","has_insurance":"0","insurance_amount":"0.00","order_for":"Member order","orderfor_status":"0","transit_det":[{"invoice_no":"20141024095","inv_last_status":"Delivered","last_updated_on":"07\/04\/2014 06:36 pm"},{"invoice_no":"20141024095","inv_last_status":"Shipped","last_updated_on":"05\/04\/2014 06:38 pm"}]}]}}}}
					<p class="api_desc">
						function returns orders details for a transaction
					</p>
				</code>
			</div>
			<!-- Franchisee Orders/Shipments ------>
			
			
			<!-- Banks API ------>
			<div class="api_det">
				<h4>Get list of Banks </h4>
				<code>
					<?php echo site_url('api/bank_list')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : authkey=<b>AUTHKEY</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"bank_list":[{"id":"3","bank_name":"CORPORATION BANK","branch_name":"BANASHANKARI ","account_number":"201801601000130","ifsc_code":"CORP0002018","remarks":"LOCALCUBE COMMERCE PVT LTD"},{"id":"2","bank_name":"HDFC Bank","branch_name":"Kengeri Satelite town ","account_number":"50200000198194","ifsc_code":"HDFC0002858","remarks":""},{"id":"4","bank_name":"ICICI BANK - Kumbalagodu","branch_name":"Kumbalagodu","account_number":"263705000001","ifsc_code":"ICIC0002637","remarks":"New bank Details"},{"id":"5","bank_name":"KARNATAKA BANK LTD","branch_name":"Kengeri Satelite town ","account_number":"1072000110047901","ifsc_code":"KARB0000107","remarks":"LOCALCUBE COMMERCE PVT LTD"},{"id":"1","bank_name":"Kotak Bank","branch_name":"BSK 3rd Stage","account_number":"2211241104","ifsc_code":"KKBK0000427","remarks":""}]}}
					<br><b>Response On Error </b> : {"status":"error","error_code":2000,"error_msg":"No Data found."}
				</code>
				<p class="api_desc">
					Function to get list of banks,returns bank details.
				</p>
			</div>
			<!-- Banks API ------>
			
			<!-- RF/RMF details APIs --->
			<div class="api_det">
				<h4>RF's assigned under RMF</h4>
				<code>
					<?php echo site_url('api/get_rf_franchises')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>   {Mandatory}
					<br><b>Response Type </b> : JSON
					<br><b>Response </b> : {"status":"success","response":{"status":"success","franchise_type":"2","rf_list":[{"franchise_id":"92","franchise_name":"Ananya Fancy Store"},{"franchise_id":"62","franchise_name":"BenakaRaj Book & Stationery"},{"franchise_id":"45","franchise_name":"Cosmo World"},{"franchise_id":"312","franchise_name":"Pavan Store"},{"franchise_id":"18","franchise_name":"Pragathi Fancy store"},{"franchise_id":"5","franchise_name":"Shwetha Fancy & Gift Shop"},{"franchise_id":"7","franchise_name":"Sri Manjunatha Fancy & Gift Centre"},{"franchise_id":"2","franchise_name":"Sri Ponnumuthappa Store"},{"franchise_id":"306","franchise_name":"Sri Raghavendra T.V.Center"},{"franchise_id":"8","franchise_name":"Trend Gift World"}]}}
				</code>
				<p class="api_desc">
					Api used to get details of RF's under RMF
				</p>
			</div>
			<div class="api_det">
				<h4>RF's basic details assigned under RMF</h4>
				<code>
					<?php echo site_url('api/get_sub_franchises')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>   {Mandatory}
					<br><b>Params</b> : start=<b>START</b>
					<br><b>Params</b> : limit=<b>LIMIT</b>
					<br><b>Params</b> : srch_kwd=<b>Search Keyword</b>  {Search by town name, phone number, franchise name}
					<br><b>Response Type </b> : JSON
					<br><b>Response </b> : {"status":"success","response":{"status":"success","franchise_type":"2","rural_master_fid":"316","assigned_rf_details":[{"basic_det":{"franchise_id":"92","pnh_franchise_id":"32324789","franchise_name":"Ananya Fancy Store","address":"College Road, ","locality":"T.Narasipura","city":"T.Narasipura","postcode":"571124","state":"Karanataka","credit_limit":"0","town_name":"T Narsipura","town_id":"34","territory_name":"Mysore","login_mobile1":"9036412221","is_prepaid":"0","franchise_type":"1","assigned_rmfid":"316"},"sales_det":{"lastmnth_sales":"0.00","currmnth_sales":"0.00","currday_sales":"0.00"},"pmt_det":{"pending_pmt":"2,160","uncleard_pmt":"2,150.00"},"shipment_det":{"ttl_opnorders":"0"}},{"basic_det":{"franchise_id":"62","pnh_franchise_id":"39652736","franchise_name":"BenakaRaj Book & Stationery","address":"Mahatma Gandhi Circle","locality":"Hassan","city":"Chennarayapatna","postcode":"573116","state":"Karnataka","credit_limit":"0","town_name":"Chenarayapatna","town_id":"22","territory_name":"Hassan","login_mobile1":"9482704340","is_prepaid":"0","franchise_type":"1","assigned_rmfid":"316"},"sales_det":{"lastmnth_sales":"0.00","currmnth_sales":"0.00","currday_sales":"0.00"},"pmt_det":{"pending_pmt":"2,331","uncleard_pmt":"0.00"},"shipment_det":{"ttl_opnorders":"0"}},{"basic_det":{"franchise_id":"45","pnh_franchise_id":"34196194","franchise_name":"Cosmo World","address":"Kasturaba Road Post Box No 10","locality":"Hassan","city":"Hassan","postcode":"573201","state":"Karnataka","credit_limit":"0","town_name":"Hassan New","town_id":"20","territory_name":"Hassan","login_mobile1":"9343591441","is_prepaid":"0","franchise_type":"1","assigned_rmfid":"316"},"sales_det":{"lastmnth_sales":"0.00","currmnth_sales":"0.00","currday_sales":"0.00"},"pmt_det":{"pending_pmt":"9","uncleard_pmt":"0.00"},"shipment_det":{"ttl_opnorders":"0"}},{"basic_det":{"franchise_id":"312","pnh_franchise_id":"32482836","franchise_name":"Pavan Store","address":"Huliyar Road, Hiriyur, ","locality":"Hiriyur","city":"Hiriyur","postcode":"577598","state":"Karanataka","credit_limit":"0","town_name":"Hiriyur","town_id":"150","territory_name":"Chitradurga","login_mobile1":"9036755765","is_prepaid":"0","franchise_type":"1","assigned_rmfid":"316"},"sales_det":{"lastmnth_sales":"0.00","currmnth_sales":"0.00","currday_sales":"0.00"},"pmt_det":{"pending_pmt":"19,481","uncleard_pmt":"0.00"},"shipment_det":{"ttl_opnorders":"0"}},{"basic_det":{"franchise_id":"18","pnh_franchise_id":"33792684","franchise_name":"Pragathi Fancy store","address":"B.M Road,Near SBM bank,3rd cross","locality":"Channapatna","city":"Channapatna","postcode":"571501","state":"karnataka","credit_limit":"0","town_name":"Chennapatna","town_id":"3","territory_name":"Bangalore Rural","login_mobile1":"9844783636","is_prepaid":"0","franchise_type":"1","assigned_rmfid":"316"},"sales_det":{"lastmnth_sales":"0.00","currmnth_sales":"0.00","currday_sales":"0.00"},"pmt_det":{"pending_pmt":"658","uncleard_pmt":"0.00"},"shipment_det":{"ttl_opnorders":"0"}}],"total_franch":10,"srch_kwd":false,"start":0,"limit":5}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2000","error_msg","RMF not assigned"}{'status':"error","error_code"=>"2000","error_msg","No Data Found"}{'status':"error","error_code"=>"2000","error_msg","Franchise type is Normal"}
				</code>
				<p class="api_desc">
					Api used to get sales,shipment,payment details of RF's under RMF
				</p>
			</div>
			<!-- RF/RMF details APIs --->
			
			<!-- Brands API --->
			<div class="api_det">
				<h4>Brands List</h4>
				<code>
					<?php echo site_url('api/brands_bycharacter')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : chr=<b>CHARACTER -- Brand Alphabet</b>   {Mandatory} //10 : Top 10 brands  20:Top 20 brands  09: 0-9 alphabet brands etc 
					<br><b>Response Type </b> : JSON
					<br><b>Response </b> : {"status":"success","response":{"status":"success","brand_list":[{"id":"76916829","name":"Nokia"},{"id":"74323882","name":"Karbonn"},{"id":"88541163","name":"Himalaya Herbals"},{"id":"88541163","name":"Himalaya Herbals"},{"id":"83318435","name":"Axe"},{"id":"42916827","name":"Lakme"},{"id":"15967949","name":"Camologic"},{"id":"24716169","name":"Ponds"},{"id":"88541163","name":"Himalaya Herbals"},{"id":"11333824","name":"Parachute"}]}}
					<br><b>Response on error</b> : {'error_code'=>2003,'error_msg'=>"Sorry..No brands found..")}
				</code>
				<p class="api_desc">
					Api used to get brands
				</p>
			</div>
			<!-- Brands API --->
			
			<!-- Complaints/Requests APIs --->
			<div class="api_det">
				<h4>complaints List</h4>
				<code>
					<?php echo site_url('api/get_request_list')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b> 
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>   {Mandatory}
					<br><b>Params</b> : type=<b>11</b>      ( Complaint : 11 and Request : 10)   {Mandatory}
					<br><b>Response Type </b> : JSON
					<br><b>Response </b> : {"status":"success","response":{"tickets_det":[{"USER":"santhosh","ticket_id":"1703","ticket_no":"4788524717","user_id":"68","name":"Testing Franchise","mobile":"9844772645","email":"","transid":"","type":"Complaint","status":"Open","priority":"High","assigned_to":"0","created_on":"09\/08\/2014 10:39 am","updated_on":"09\/08\/2014 10:39 am","franchise_id":"498","related_to":"Shipment","req_mem_name":"0","req_mem_mobile":"0","from_app":"1","assignedto":null,"messages":[{"admin_user":null,"ticket_id":"1703","msg":"testing","msg_type":"11","medium":"0","from_customer":"1","support_user":"0","created_on":"09\/08\/2014 10:39 AM"}]},{"USER":null,"ticket_id":"1670","ticket_no":"1534217296","user_id":"0","name":"Testing Franchise","mobile":"9743537525","email":"","transid":"","type":"Complaint","status":"Open","priority":"High","assigned_to":"0","created_on":"03\/07\/2014 03:09 pm","updated_on":"03\/07\/2014 03:10 pm","franchise_id":"498","related_to":"Product","req_mem_name":"0","req_mem_mobile":"0","from_app":"1","assignedto":null,"messages":[{"admin_user":"sowmyashree","ticket_id":"1670","msg":"In progress","msg_type":"1","medium":"0","from_customer":"0","support_user":"61","created_on":"03\/07\/2014 15:10 PM"},{"admin_user":"sowmyashree","ticket_id":"1670","msg":"In progress","msg_type":"1","medium":"0","from_customer":"0","support_user":"61","created_on":"03\/07\/2014 15:10 PM"},{"admin_user":null,"ticket_id":"1670","msg":"vbshj hjsvcxhjs hsjcshj","msg_type":"11","medium":"0","from_customer":"1","support_user":"0","created_on":"03\/07\/2014 15:09 PM"}]}]}}
					<br><b>Response On Error </b> : {'status':"error","error_code"=>"2000","error_msg","No Complaints tickets found"}
				</code>
				<p class="api_desc">
					Api used to get complaint tickets raised 
				</p>
			</div>
			
			<div class="api_det">
				<h4>Type of Complaints/Requests</h4>
				<code>
					<?php echo site_url('api/get_services_req_list')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response </b> : {"status":"success","response":{"request_list":[{"id":"1","name":"Product"},{"id":"2","name":"Payment"},{"id":"3","name":"Service"},{"id":"4","name":"Insurance"},{"id":"5","name":"Shipment"},{"id":"6","name":"Technical"},{"id":"7","name":"Sourcing"}]}}
					<br><b>Response On Error </b> : {"status":"error","error_code":2000,"error_msg":"No tickets found.","response":{"error_code":2000,"error_msg":"No tickets found."}}
				</code>
				<p class="api_desc">
					Api used to get types of complaints/requests 
				</p>
			</div>
			
			<div class="api_det">
				<h4>Posting complaints/requests</h4>
				<code>
					<?php echo site_url('api/post_request')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISEE ID</b>       {Mandatory}
					<br><b>Params</b> : type=<b>TYPE</b> {10 :Request && 11:complaint}  {Mandatory}
					<br><b>Params</b> : desc=<b>COMPLAINT</b>                   {Mandatory}
					<br><b>Params</b> : related_to=<b>Related To</b> {1:Product, 2:Payment  3:Service  4:Insurance 5:Shipment 6:Technical 7:sourcing }		{Mandatory}
					<br><b>Response Type </b> : JSON
					<br><b>Response</b> : {"status":"success","response":{"ticket_id":1705,"message":"Thank You for submitting. We received your request. Please Expect our reply shortly"}}
					<br><b>Response On Error </b> : {"status":"error","error_code":2000,"error_msg":"Unknown Franchise.","response":{"error_code":2000,"error_msg":"Unknown Franchise."}}
				</code>
				<p class="api_desc">
					Api used to post complaints/requests 
				</p>
			</div>
			
			<div class="api_det">
				<h4>Complaint details</h4>
				<code>
					<?php echo site_url('api/get_request_byid')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : ticket_id=<b>Ticket ID</b>    {Mandatory}
					<br><b>Response Type </b> : JSON
					<br><b>Response </b> : {"status":"success","response":{"tickets_det":{"USER":"Lavanya Jayakumar","ticket_id":"544","ticket_no":"9852082903","user_id":"64593","name":"","mobile":"","email":"lavan.jaya@gmail.com","transid":"SNPUWI84298","type":null,"status":"Closed","priority":"Low","assigned_to":"12","created_on":"01\/03\/2013 12:30 am","updated_on":"03\/03\/2013 09:11 pm","franchise_id":"0","related_to":null,"req_mem_name":null,"req_mem_mobile":null,"from_app":"0","assignedto":"Kiran","messages":[{"admin_user":"Kiran","ticket_id":"544","msg":"Status changed to <b>Closed<\/b>","msg_type":"0","medium":"0","from_customer":"0","support_user":"12","created_on":"03\/03\/2013 21:11 PM"},{"admin_user":"Kiran","ticket_id":"544","msg":"closing the case as reply has already been sent to the user.","msg_type":"0","medium":"0","from_customer":"0","support_user":"12","created_on":"03\/03\/2013 21:11 PM"},{"admin_user":"Kiran","ticket_id":"544","msg":"Status changed to <b>In Progress<\/b>","msg_type":"0","medium":"0","from_customer":"0","support_user":"12","created_on":"03\/03\/2013 21:11 PM"},{"admin_user":"Kiran","ticket_id":"544","msg":"Ticket assigned to Kiran","msg_type":"2","medium":"0","from_customer":"0","support_user":"12","created_on":"03\/03\/2013 21:11 PM"},{"admin_user":null,"ticket_id":"544","msg":"SUBJECT<br \/>\n-----------------------------------------------------<br \/>\nRe: Important refund information for SNPUWI84298<br \/>\n<br \/>\nEMAIL CONTENT<br \/>\n-----------------------------------------------------<br \/>\n<p>Hi <\/p>\r\n<p>Please let me know when the amount will be refunded to me <\/p>\r\n<p><blockquote type=\"cite\">On 28-Feb-2013 5:20 PM, &quot;lavan jayakumar&quot; &lt;<a href=\"mailto:lavan.jaya@gmail.com\">lavan.jaya@gmail.com<\/a>&gt; wrote:<br><br><p>Hi <br>\r\nPlease process the redund to the earliest. Still money is not credited to my account. <br>\r\nTotally 473 has to be credit .. Pleas update me the status now . <\/p>\r\n<p>Lavanya<br>\r\n<\/p><p><font color=\"#500050\">&gt;<br>&gt; On 23-Feb-2013 11:52 AM, &quot;Snapittoday&quot; &lt;<a href=\"mailto:support@snapittoday.com\">support@snapittoday.com<\/a>&gt; wrote:<br>&gt;<br>&gt; Dear Lavanya Jayakum...<\/font><\/p>\r\n<\/blockquote><\/p>\r\n","msg_type":"1","medium":"0","from_customer":"1","support_user":"0","created_on":"01\/03\/2013 00:30 AM"}],"image_path":[{"image_url":"http:\/\/sndev13.snapittoday.com\/resources\/returns_images\/20141025496_1.jpg"},{"image_url":"http:\/\/sndev13.snapittoday.com\/resources\/returns_images\/20141025496_2.jpg"},{"image_url":"http:\/\/sndev13.snapittoday.com\/resources\/returns_images\/20141025496_3.jpg"}]}}}
					<br><b>Response On Error </b> : {"status":"error","error_code":2000,"error_msg":"No tickets found.","response":{"error_code":2000,"error_msg":"No tickets found."}}
				</code>
				<p class="api_desc">
					Api used to get tickets Details 
				</p>
			</div>
						
			<!-- Complaints APIs --->
			
			
			
			<!-- ===============< BLOCKS END >=========-->
		</div>
	</body>
</html>
