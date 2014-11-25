<html>
	<head>
		<title><?php echo $title?></title>
		<script type="text/javascript" src="<?php echo base_url().'/js/jquery.js'?>"></script>
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
			.json_preview{padding: 10px;
background: purple;
color: #FFF;} 
			
		</style>
	</head>
	<body>
		<h2>Storeking TAB Api Documentation</h2>
		
		<div class="api_list">
			<!-- ===============< API LIST BLOCK START >=========-->
			<div class="api_det">
				<h4>API documentation URL</h4>
				<code>
					<?php echo site_url('api/doc')?>
				</code>
				<p class="api_desc">
					Request to view api documentation 
				</p>
			</div>
			
			<div class="api_det">
				<h4>Access Login Authentication </h4>
				<code>
					<?php echo site_url('api/login')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : username=<b>USERNAME</b>&amp;password=<b>PASSWORD</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response </b> : <pre class="json_preview">{"status":"success","auth":"<b>AUTHKEY</b>"}</pre>
					<b>Response On Error </b> : 
					<pre class="json_preview">{"status":"error","error_code":2000,"error_msg":"Invalid Password entered\n"}</pre>
					<b>Response On Success </b> : 
					<pre class="json_preview">{"status":"success","response":{"authkey":"fef56cae0dfbabedeadb64bf881ab64f","user_id":"1","emp_id":"","franchise_name":"Testing Franchise","franchise_mobno":"9844772645","franchise_id":"498"}}</pre>
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
					<br><b>Params</b> : username=<b>{FRANCHISE_MOBILE_NUMBER}</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : <pre class="json_preview">{'status':"error","error_code":"2000","error_msg":"Invalid Username entered\n"}</pre>
					<br><b>Response On Success </b> : <pre class="json_preview">{"status":"success","response":"Generated new password has been sent to your registered mobile number -Storeking"}</pre>
				</code>
				<p class="api_desc">
					Request to handle forgot password - sms will be sent with password
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
					<br><b>Response On Error </b> : <pre class="json_preview">{'status':"error","error_code"=>"2007","error_msg","No member available for given member id"}</pre>
					<br><b>Response On Success </b> : <pre class="json_preview">{"status":"success","response":{"member_details":[{"id":"485","user_id":"61539","pnh_member_id":"21111113","franchise_id":"17","points":"766","voucher_balance":"0","gender":"0","salute":"0","first_name":"shariff","last_name":"satyala","dob":"1985-06-01","address":"jayanagar 9th block,bangalore","city":"Bangalore","pincode":"560041","mobile":"9740793973","mobile_network":null,"email":"sharif@localcircle.in","marital_status":"1","spouse_name":"","child1_name":"","child2_name":"","anniversary":"1969-12-31","child1_dob":"1969-12-31","child2_dob":"1969-12-31","profession":"Corporate Employee","expense":"2","is_card_printed":"1","created_on":"1355992410","modified_on":"1371887861","created_by":"3","modified_by":"6","dummy":"0","voucher_bal_validity":null,"mem_image_url":null}]}}</pre>
				</code>
				<p class="api_desc">
					To get the details about member for given member id
				</p>
			</div>
			
			<div class="api_det">
				<h4>Register member </h4>
				<code>
					<?php echo site_url('api/add_member')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : user_id=<b>USERID</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISE ID</b>
					<br><b>Params</b> : mobile_no=<b>MOBILENO</b>
					<br><b>Params</b> : member_name=<b>MEMBER NAME</b>
					<br><b>Params</b> : image=<b>MEMBER PROFILE PIC </b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : <pre class="json_preview">{'status':"error","error_code"=>"2008","error_msg","Mobile no already registered"}</pre>
					<br><b>Response On Success </b> : <pre class="json_preview">{"status":"success","response":{"member_details":[{"id":"485","user_id":"61539","pnh_member_id":"21111113","franchise_id":"17","points":"766","voucher_balance":"0","gender":"0","salute":"0","first_name":"shariff","last_name":"satyala","dob":"1985-06-01","address":"jayanagar 9th block,bangalore","city":"Bangalore","pincode":"560041","mobile":"9740793973","mobile_network":null,"email":"sharif@localcircle.in","marital_status":"1","spouse_name":"","child1_name":"","child2_name":"","anniversary":"1969-12-31","child1_dob":"1969-12-31","child2_dob":"1969-12-31","profession":"Corporate Employee","expense":"2","is_card_printed":"1","created_on":"1355992410","modified_on":"1371887861","created_by":"3","modified_by":"6","dummy":"0","voucher_bal_validity":null,"mem_image_url":null}]}}</pre>
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
					<br><b>Params</b> : franchise_id=<b>FRANCHISE ID</b>
					<br><b>Params</b> : mobile_no=<b>MOBILE NO</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : <pre class="json_preview">{'status':"error","error_code"=>"2010","error_msg","No member found for given mobile number"}</pre>
					<br><b>Response On Success  </b> : <pre class="json_preview">{"status":"success","response":{"member_details":{"id":"32180","user_id":"122799","pnh_member_id":"22028815","franchise_id":"498","points":"0","voucher_balance":"0","gender":"0","salute":"0","first_name":"storeking123456","last_name":"","dob":null,"address":"","city":"","pincode":"","mobile":"8878785858","mobile_network":null,"email":"","marital_status":"0","spouse_name":"","child1_name":"","child2_name":"","anniversary":null,"child1_dob":null,"child2_dob":null,"profession":"","expense":"0","is_card_printed":"0","created_on":"1408530418","modified_on":"0","created_by":"0","modified_by":"0","dummy":null,"voucher_bal_validity":null}}}</pre>
				</code>
				<p class="api_desc">
					get the member by mobile number.
				</p>
			</div>
			
			<div class="api_det">
				<h4>Check Stock Api</h4>
				<code>
					<?php echo site_url('api/check_deal_update')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : auth=<b>AUTHKEY</b>
					<br><b>Params</b> : pid=<b>PRODUCTID</b>
					<br><b>Params</b> : item_id=<b>ITEMID ID</b>
					<br><b>Params</b> : fid=<b>FranchiseID</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : <pre class="json_preview">{"status":"error","error_code":2046,"error_msg":"Sold Out","response":{"error_code":2046,"error_msg":"Sold Out"}}</pre>
					<br><b>Response On Success </b> : <pre class="json_preview">{"status":"success","response":{"item_id":"5425117564","pid":"1826682","orgprice":"1405","price":"999","publish":"1","live":"1","pimages":null,"attributes":"","offer_note":"","powered_by":"storeking","lens_pkg_list":"","has_power":"0","shipsin":"48-72 hrs","max_order_qty":5,"price_type":0}}</pre>
				</code>
				<p class="api_desc">
					check deal stock and price  
				</p>
			</div>
			
			<div class="api_det">
				<h4>Search Deals</h4>
				<code>
					<?php echo site_url('api/search')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : brand_id[]=BRANDID
					<br><b>Params</b> : category_id[]=CATID
					<br><b>Params</b> : franchise_id=FranchiseID
					<br><b>Params</b> : search_data=SEARCHKWD
					<br><b>Params</b> : min_price=MIN_PRICE
					<br><b>Params</b> : max_price=MAX_PRICE
					<br><b>Params</b> : prd_id=PRODUCTID
					<br><b>Params</b> : discount=SHOWDISCOUNTED DEALS
					<br><b>Params</b> : start=PAGINCATION START INDEX
					<br><b>Params</b> : limit=PAGINCATION LIMIT 
					<br><b>Params</b> : sortby=popular|new
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : <pre class="json_preview">{'status':"error","error_code"=>"2015","error_msg","no deals found"}</pre>
				</code>
				<p class="api_desc">
					Search deals by keyword :
					<code>
						<br><b>Total Response</b> : <pre class="json_preview">{"status":"success","response":{"total_deals":"29"}}</pre>
						<br><b>DEALS Response </b> : <pre class="json_preview">{"status":"success","response":{"deal_list":[{"pid":"16679464","size_chart":"","itemid":"8124562395","name":"Olay Natural White Light Instant Glowing Fairness Serum - 20g","menu":"Beauty & Cosmetics","menu_id":"100","gender_attr":"","category":"Fairness Cream","category_id":"267","main_category":"Fairness","member_price":"85.00","main_category_id":"165","brand":"Olay","brand_id":"78892514","mrp":"85","price":"85","store_price":"85","is_combo":"0","image_url":"http:\/\/static.snapittoday.com\/items\/dh17j91igki00eb.jpg","small_image_url":"http:\/\/static.snapittoday.com\/items\/small\/dh17j91igki00eb.jpg","description":"This daily Instant Glowing Fairness Serum with triple vitamin system & non-greasy formula works immediately to brighten your face to give you fair-looking skin that glows.","ships_in":"24-48 hrs","keywords":"Olay,Natural,White,Light,Instant,Glowing,Fairness,Serum,20g,Olay Natural White Light Instant Glowing Fairness Serum - 20g","total_orders":"19","p_discount":"1.000000","d_sno":"67882","rel_ttl":"0","products":{"product":[{"name":"Olay Natural White Instant Glowing Fairness, 20gm","qty":"1"}]},"images":[],"attributes":[],"pseller_id":"","mp_mem_max_qty":0,"mp_frn_max_qty":0,"max_ord_qty":5,"landing_cost":72.25,"availability":1},{"pid":"10037763","size_chart":"","itemid":"2726363947","name":"Olay Natural White Instant Glowing Fairness, 20gm","menu":"Beauty & Cosmetics","menu_id":"100","gender_attr":"","category":"Fairness Cream","category_id":"267","main_category":"Fairness","member_price":"85.00","main_category_id":"165","brand":"Olay","brand_id":"78892514","mrp":"85","price":"85","store_price":"85","is_combo":"0","image_url":"http:\/\/static.snapittoday.com\/items\/ni6pk18fp81l38o.jpg","small_image_url":"http:\/\/static.snapittoday.com\/items\/small\/ni6pk18fp81l38o.jpg","description":"description","ships_in":"24-48 hrs","keywords":"Olay Instant Glowing Fairness Cream, Natural White 20 g,Face,Olay,Instant,Glowing,Fairness,Cream,Natural,White,20,g","total_orders":"10","p_discount":"1.000000","d_sno":"119429","rel_ttl":"0","products":{"product":[{"name":"Olay Natural White Instant Glowing Fairness, 20gm","qty":"1"}]},"images":[],"attributes":[],"pseller_id":"","mp_mem_max_qty":0,"mp_frn_max_qty":0,"max_ord_qty":5,"landing_cost":72.25,"availability":1},{"pid":"10000673","size_chart":"","itemid":"658643369524","name":"Olay Total Effects 7-in-1 Anti-ageing Cream Normal - (50gm)","menu":"Beauty & Cosmetics","menu_id":"100","gender_attr":"","category":"Anti-Ageing","category_id":"64","main_category":"Face care","member_price":"749.00","main_category_id":"270","brand":"Olay","brand_id":"78892514","mrp":"749","price":"749","store_price":"749","is_combo":"0","image_url":"http:\/\/static.snapittoday.com\/items\/7408f06672p23i5.jpg","small_image_url":"http:\/\/static.snapittoday.com\/items\/small\/7408f06672p23i5.jpg","description":"description","ships_in":"24-48 hrs","keywords":"Olay, Anti-Ageing,Olay Total Effects 7-in-1 Anti-ageing Cream Normal - (50gm),anti-wrinkle cream,wrinkles,skin dryness,face moisturiser,","total_orders":"9","p_discount":"1.000000","d_sno":"6936","rel_ttl":"0","products":{"product":[{"name":"Olay Total Effects Day Cream Normal (50g)","qty":"1"}]},"images":[],"attributes":[],"pseller_id":"","mp_mem_max_qty":0,"mp_frn_max_qty":0,"max_ord_qty":5,"landing_cost":636.65,"availability":1},{"pid":"11439498","size_chart":"","itemid":"4653866756","name":"Olay Total Effects Day Cream 7 in 1 Normal SPF 15 (8g)","menu":"Beauty & Cosmetics","menu_id":"100","gender_attr":"","category":"Fairness Cream","category_id":"267","main_category":"Fairness","member_price":"149.00","main_category_id":"165","brand":"Olay","brand_id":"78892514","mrp":"149","price":"149","store_price":"149","is_combo":"0","image_url":"http:\/\/static.snapittoday.com\/items\/.jpg","small_image_url":"http:\/\/static.snapittoday.com\/items\/small\/.jpg","description":"","ships_in":"24-48 hrs","keywords":"Olay Total Effects Day Cream 7 in 1 Normal SPF 15 (8g),Face Packs, Gels & Masks,Olay,Olay,Total,Effects,Day,Cream,7,in,1,Normal,SPF,15,8g,","total_orders":"4","p_discount":"1.000000","d_sno":"31076","rel_ttl":"0","products":{"product":[{"name":"Olay Total Effects Day Cream 7 in 1 Normal SPF 15 (8g)","qty":"1"}]},"images":[],"attributes":[],"pseller_id":"","mp_mem_max_qty":0,"mp_frn_max_qty":0,"max_ord_qty":5,"landing_cost":126.65,"availability":1},{"pid":"10000634","size_chart":"","itemid":"497855496777","name":"Olay Moisturizing Cream (100g)","menu":"Beauty & Cosmetics","menu_id":"100","gender_attr":"","category":"Moisturiser","category_id":"106","main_category":"Lotion & Moisturizer","member_price":"399.00","main_category_id":"273","brand":"Olay","brand_id":"78892514","mrp":"399","price":"399","store_price":"360","is_combo":"0","image_url":"http:\/\/static.snapittoday.com\/items\/9hl97lmn7f40g6c.jpg","small_image_url":"http:\/\/static.snapittoday.com\/items\/small\/9hl97lmn7f40g6c.jpg","description":"This cream provides long lasting moisturization and improves skin softness and smoothness to leave it youthful looking. Details This cream goes beyond skin replenishment, to provide moisture nourishment, for up to 12 hours. It significantly improves skin smoothness and helps reduce lines and wrinkles to leave even dry skin youthful looking. Its formula Contains a special moisture binding system that locks in moisture where it's needed most. Contains fluids so similar to the nature fluids in young skin that skin readily claims them as its own. How to Use Smooth onto face and neck twice per day. Qty 100g","ships_in":"24-48 hrs","keywords":"Olay, Moisturiser,Olay Moisturizing Cream (100g)face care,skin nourishment,","total_orders":"2","p_discount":"1.000000","d_sno":"6897","rel_ttl":"0","products":{"product":[{"name":"Olay Moisturising Cream (100g)","qty":"1"}]},"images":[],"attributes":[],"pseller_id":"","mp_mem_max_qty":0,"mp_frn_max_qty":0,"max_ord_qty":5,"landing_cost":339.15,"availability":1}],"total_deals":0,"brand_list":[],"category_list":{"267":{"id":"267","name":"Fairness Cream"},"165":{"id":"165","name":"Fairness"},"64":{"id":"64","name":"Anti-Ageing"},"270":{"id":"270","name":"Face care"},"106":{"id":"106","name":"Moisturiser"},"273":{"id":"273","name":"Lotion & Moisturizer"}},"cat_list":[],"price_list":[],"attr_list":[],"gender_list":[]}}</pre>
						<br><b>Refinement Response </b> : <pre class="json_preview">{"status":"success","response":{"deal_list":"","total_deals":29,"brand_list":[{"id":"78892514","name":"Olay"}],"category_list":[],"cat_list":[{"id":"64","name":"Anti-Ageing"},{"id":"65","name":"Anti-Dark Circle"},{"id":"67","name":"Anti-Wrinkle"},{"id":"75","name":"Body Wash"},{"id":"1128","name":"Cleanser"},{"id":"89","name":"Face Wash"},{"id":"267","name":"Fairness Cream"},{"id":"106","name":"Moisturiser"},{"id":"111","name":"Night Cream"},{"id":"164","name":"Serum"}],"price_list":{"min":"69","max":"1499"},"attr_list":[],"gender_list":[]}}</pre>
					</code>
				</p>
			</div>
 
			
			<div class="api_det">
				<h4>Check cart items API</h4>
				<code>
					<?php echo site_url('api/check_cart_items')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : f_mobno=Franchise mobile no
					<br><b>Params</b> : m_mobno=MEMBER mobile no
					<br><b>Params</b> : m_name=MEMBER NAME
					<br><b>Params</b> : m_id=MEMBERID
					<br><b>Params</b> : max_price=MAX_PRICE
					<br><b>Params</b> : pids[]:PRODUCTS IN CART
					<br><b>Params</b> : pqtys[]:PRODUCT QTYS IN CART
					<br><b>Params</b> : fid:FranchiseID
					<br><b>Params</b> : promo_code=PROMO_CODE [pass promo code to apply on cart items]
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : <pre class="json_preview">{'status':"error","error_code"=>"2015","error_msg","no deals found"}</pre>
					<br><b>Response On Success </b> : <pre class="json_preview">{"status":"success","response":{"franchise_id":17,"franchise_name":"Banashankari PayNearHome - 11feet ecommerce Pvt Ltd","purchase_limit":"6,04,207","member_id":"21111113","member_name":"shariff","member_mobno":"9740793973","fran_mobno":"9740793973","is_new_mem":0,"is_first_order":0,"member_fee":0,"items":{"16679464":{"itemid":"8124562395","lens_type":"","menuid":"100","is_combo":"0","pid":"16679464","orgprice":"85","price":"85","publish":"1","has_insurance":"0","inv_val":"0","inv_marg":"0","has_power":"0","price_type":0,"max_order_qty":5,"lens_package_list":[]}}}}</pre>
					<br><b>Response On Success with PROMO CODE </b> : <pre class="json_preview">{"status":"success","response":{"franchise_id":498,"franchise_name":"Testing Franchise","avail_min_credit":19782.19,"avail_max_credit":19782.19,"purchase_limit":"19,782","member_id":"21111113","member_name":"shariff","member_mobno":"9740793973","fran_mobno":"9743537525","is_new_mem":0,"is_first_order":0,"member_points":0,"member_points_avail":"140","member_points_redeem":0,"member_dob":"06\/01\/1985","member_fee":0,"items":{"10047381":{"free_frame":"1","menu_id":null,"brandid":"76916829","catid":"1028","itemid":"8156487488","lens_type":"","menuid":"112","is_combo":"0","pid":"10047381","orgprice":"2999","price":"2682","publish":"1","has_insurance":"0","inv_val":"0","inv_marg":"0","has_power":"1","price_type":0,"max_order_qty":5,"lens_package_list":[{"package_id":"1","package_for":"Full-Rim","package_name":"Regular Lens","package_desc":"Regular Lens","package_price":"499","package_itemid":"8722368374","created_on":null,"created_by":"0","modified_on":null,"modified_by":"0"},{"package_id":"2","package_for":"Full-Rim","package_name":"Regular Anit-Glare","package_desc":"Regular Anit-Glare","package_price":"699","package_itemid":"8722368374","created_on":null,"created_by":"0","modified_on":null,"modified_by":"0"},{"package_id":"3","package_for":"Full-Rim","package_name":"Hydrophobic Anti-Glare","package_desc":"Hydrophobic Anti-Glare","package_price":"990","package_itemid":"8722368374","created_on":null,"created_by":"0","modified_on":null,"modified_by":"0"},{"package_id":"4","package_for":"Full-Rim","package_name":"Thin HD Unbreakable","package_desc":"Thin HD Unbreakable","package_price":"1390","package_itemid":"8722368374","created_on":null,"created_by":"0","modified_on":null,"modified_by":"0"},{"package_id":"5","package_for":"Full-Rim","package_name":"American Optical Thin","package_desc":"American Optical Thin","package_price":"1690","package_itemid":"8722368374","created_on":null,"created_by":"0","modified_on":null,"modified_by":"0"},{"package_id":"6","package_for":"Full-Rim","package_name":"AO Super Thin","package_desc":"AO Super Thin","package_price":"3560","package_itemid":"8722368374","created_on":null,"created_by":"0","modified_on":null,"modified_by":"0"},{"package_id":"7","package_for":"Full-Rim","package_name":"Kodak Thin","package_desc":"Kodak Thin","package_price":"2290","package_itemid":"8722368374","created_on":null,"created_by":"0","modified_on":null,"modified_by":"0"},{"package_id":"8","package_for":"Full-Rim","package_name":"Photochromic","package_desc":"Photochromic","package_price":"1490","package_itemid":"8722368374","created_on":null,"created_by":"0","modified_on":null,"modified_by":"0"}],"promo_code_applied":1,"promo_code_disc":13410}},"promo_code_total_disc":134.1,"promo_code_status":"valid","promo_code":"1234","cartid":"056768141094"}}</pre> 
				</code>
			</div>
			
			<div class="api_det">
				<h4>Create Order</h4>
				<code>
					<?php echo site_url('api/create_order')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : authkey=<b>AUTHKEY</b>
					<br><b>Params</b> : fid=<b>FranchiseID</b>
					<br><b>Params</b> : f_mobno=<b>FRANCHISE MOBILENO</b>
					<br><b>Params</b> : m_id=<b>MEMBER ID</b> 
					<br><b>Params</b> : pids[]=<b>PRODUCTS IN CART </b>
					<br><b>Params</b> : pqtys[]=<b>PRODUCT QTYS IN CART</b> 
					<br><b>Params</b> : redeem=<b>Redeem Points</b>
					<br><b>Params</b> : attr_pid[]=<b>PRODUCTS IN CART </b>
					<br><b>Params</b> : attr_pid_val[]=<b>SELECTED SIZE - PRODUCT ID </b>					
					<br><b>Params</b> : opt_insurance[]=<b>Array of pids which are marked for insurance</b>
					<br><b>Params</b> : promo_code=PROMO_CODE [pass promo code to apply on cart items]
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : <pre class="json_preview">{"status":"error","error_code":2000,"error_msg":"No orders found."}</pre>
					<br><b>Response </b> :<pre class="json_preview"> {"status":"success","response":{"trans":"PNH82744","orders":[{"order_id":"2291464544","product_id":"16679464","item_name":"Olay Natural White Light Instant Glowing Fairness Serum - 20g","mrp":"85","offer_price":85,"has_insurance":"0","insurance_fee":"0.00","franchise_price":72.25,"franchise_price_percentage":"15%","member_fee":0}],"total_member_fee":0,"order_for":"Non key Member"}}</pre>
				</code>
				<p class="api_desc">
					Function to create order via api 
				</p>
			</div>
			
			<div class="api_det">
				<h4>Sync Menu,Category and Brands by version API</h4>
				<code>
					<?php echo site_url('api/sync_menubrandcats')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : version=VersionNO
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : <pre class="json_preview">{"status":"error","error_code":2046,"error_msg":"Given version details not found"}</pre>
					<br><b>Response On Success </b> : <pre class="json_preview">{"status":"success","response":{"cat_list":{"1194":{"id":"1194","name":"Babydoll and Chemise Set","menu_id":"135","menu_name":"Innerwear","parent_id":"0","is_active":1},"1188":{"id":"1188","name":"Brief","menu_id":"135","menu_name":"Innerwear","parent_id":"0","is_active":1},"1192":{"id":"1192","name":"Sleepwear","menu_id":"135","menu_name":"Innerwear","parent_id":"0","is_active":1}},"brand_list":{"61827923":{"id":"61827923","name":"Heart 2 Heart","menu_id":"135","menu_name":"Innerwear","is_active":1},"55156235":{"id":"55156235","name":"N-Gal","menu_id":"135","menu_name":"Innerwear","is_active":1},"19561527":{"id":"19561527","name":"Eminence","menu_id":"135","menu_name":"Innerwear","is_active":1},"82461869":{"id":"82461869","name":"Floret","menu_id":"135","menu_name":"Innerwear","is_active":1}}}}</pre> 
				</code>
			</div>
			
			<div class="api_det">
				<h4>Product Availablity/Price Updates</h4>
				<code>
					<?php echo site_url('api/check_updates')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : version=VersionNO
					<br><b>Params</b> : update_flag=Update TYPE [1:Price Update,Availability:2]
					<br><b>Response Type </b> : JSON
					<br><b>Response On Error </b> : <pre class="json_preview">{"status":"error","error_code":2046,"error_msg":"Given version details not found"}</pre>
					<br><b>Response On Success </b> :  Please refer in reset client 
				</code>
			</div>
			
			<div class="api_det">
				<h4>Product Request API</h4>
				<code>
					<?php echo site_url('api/post_request')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : authkey=<b>AUTHKEY</b>
					<br><b>Params</b> : franchise_id=<b>FRANCHISE_MOBILE_NUMBER</b>
					<br><b>Params</b> : type=10
					<br><b>Params</b> : desc=<b>Request message</b>
					<br><b>Params</b> : pid=<b>Product</b>
					<br><b>Params</b> : related_to=<b>Product</b>
					<br><b>Params</b> : req_mem_name=<b>Member name</b>
					<br><b>Params</b> : req_mem_mobile=<b>Member name</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response On Success</b> : <pre class="json_preview">{"status":"success","response":"Request submitted"}
				</code>
				<p class="api_desc">
					Api to place product request 
				</p>
			</div>
			<div class="api_det">
				<h4>Get Current running offers by Group Menu</h4>
				<code>
					<?php echo site_url('api/get_store_offers')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : authkey=<b>AUTHKEY</b>
					<br><b>Params</b> : store_menu_id=<b>Group Menu ID</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response </b> : <pre class="json_preview">{
						    "status": "success",
						    "response": {
						        "offer_product_list": [
						            {
						                "id": "18",
						                "pid": "10019509",
						                "name": "Nokia X (Yellow)",
						                "mrp": "9499",
						                "price": "7151.25",
						                "disc": "25",
						                "pimg_link": "http://static.snapittoday.com/items/small/jb981jf5f94a4hh.jpg",
						                "mp_offer_to": "2014-08-31 16:15:00"
						            },
						            {
						                "id": "18",
						                "pid": "10042272",
						                "name": "Karbonn Titanium S5 Plus (Pearl White)",
						                "mrp": "12990",
						                "price": "9000.00",
						                "disc": "31",
						                "pimg_link": "http://static.snapittoday.com/items/small/gjieg0lnh4eb039.jpg",
						                "mp_offer_to": "2014-08-29 14:06:00"
						            },
						            {
						                "id": "18",
						                "pid": "10029039",
						                "name": "Karbonn A19 (White)",
						                "mrp": "6990",
						                "price": "5000.00",
						                "disc": "28",
						                "pimg_link": "http://static.snapittoday.com/items/small/j7gb3680819503d.jpg",
						                "mp_offer_to": "2014-08-28 14:06:00"
						            },
						            {
						                "id": "18",
						                "pid": "10042276",
						                "name": "Nokia Lumia 1320 (Black)",
						                "mrp": "23500",
						                "price": "19338.25",
						                "disc": "18",
						                "pimg_link": "http://static.snapittoday.com/items/small/599m20nifi1733i.jpg",
						                "mp_offer_to": "2014-08-27 14:06:00"
						            },
						            {
						                "id": "18",
						                "pid": "18001318",
						                "name": "Samsung Galaxy Young S6312 (White)",
						                "mrp": "8660",
						                "price": "5500.00",
						                "disc": "36",
						                "pimg_link": "http://static.snapittoday.com/items/small/996b7jpj7pd40g3.jpg",
						                "mp_offer_to": "2014-08-27 14:06:00"
						            },
						            {
						                "id": "18",
						                "pid": "10044948",
						                "name": "Nokia X (Black)",
						                "mrp": "9499",
						                "price": "7151.25",
						                "disc": "25",
						                "pimg_link": "http://static.snapittoday.com/items/small/adk99obljf8h977.jpg",
						                "mp_offer_to": "2014-08-31 16:15:00"
						            }
						        ]
						    }
						}</pre>
				</code>
				<p class="api_desc">
					Api to Get Current running offers by Group Menu ID
				</p>
			</div>
			<div class="api_det">
				<h4>Get Upcoming offers by Group Menu</h4>
				<code>
					<?php echo site_url('api/get_upcoming_offers')?>
					<br><b>Type</b> : POST
					<br><b>Params</b> : authkey=<b>AUTHKEY</b>
					<br><b>Params</b> : store_menu_id=<b>Group Menu ID</b>
					<br><b>Response Type </b> : JSON
					<br><b>Response </b> : <pre class="json_preview">{
											    "status": "success",
											    "response": {
											        "offer_product_list": [
											            {
											                "id": "5",
											                "pid": "10043485",
											                "name": "Advance Baby Flower Print Mattress with Frill (Pink)",
											                "mrp": "625",
											                "price": "621.25",
											                "disc": "1",
											                "pimg_link": "http://static.snapittoday.com/items/small/03c34286ni98lp1.jpg",
											                "mp_offer_from": "2014-09-25 21:11:00",
											                "mp_offer_to": "2014-09-29 15:07:00"
											            }
											        ]
											    }
											}</pre>
				</code>
				<p class="api_desc">
					Api to Get Upcoming offers by Group Menu ID
				</p>
			</div>
			
			<div class="api_det">
				<h4>Franchisee Requesting OTP</h4>
				<code>
					<?php echo site_url('api/request_franchise_otp')?>
					<br><b>Type</b> : GET
					<br><b>Params</b> : franchise_mobno=<b>FRANCHISEEE MOBILENO</b> OR <b>TAB Sim number</b>
					<br><b>Response Type </b> : JSON
					<br><b>Success Response </b> : <pre class="json_preview">{"status":"success","response":{"status":"success","franchise_id":"108","franchise_name":"A1 FANCY STORE","login_mobile":"9740503800","OTP_Number":"8346","valid_till":"21\/08\/2014 05:10 pm"}}</pre>
					<br><b>Error Response </b> : <pre class="json_preview">{"status":"error","error_code":2003,"error_msg":"Invalid franchise mobile number"}}</pre>
					<p class="api_desc">
						function franchisee requsting one time password
					</p>
				</code>
			</div>
			
			<div class="api_det">
				<h4>Validating Franchisee OTP</h4>
				<code>
					<?php echo site_url('api/validate_franchise_otp')?>
					<br><b>Type</b> : GET
					<br><b>Params</b> : franchise_mobno=<b>FRANCHISEEE MOBILENO</b> OR <b>TAB Sim number</b>
					<br><b>Params</b> : otpno=<b>OTP number</b> 
					<br><b>Response Type </b> : JSON
					<br><b>Success Response </b> : <pre class="json_preview">{"status":"success","response":{"status":"success","franchise_id":"108","franchise_name":"A1 FANCY STORE","login_mobile":"9740503800"}}</pre>
					<br><b>Error Response </b> : <pre class="json_preview">{"status":"error","error_code":2003,"error_msg":"Invalid franchise mobile number"}}</pre>
					<br><b>Error Response </b> : <pre class="json_preview">{"status":"error","error_code":2003,"error_msg":"Invalid OTP number\/OTP number is Expired","response":{"error_code":2003,"error_msg":"Invalid OTP number\/OTP number is Expired"}}</pre>
					
					<p class="api_desc">
						function to validate franchise mobno and franchisee OTP
					</p>
				</code>
			</div>
			
			<!-- ===============< BLOCKS END >=========-->
		</div>
		
		<script type="text/javascript">
		if (!library)
			   var library = {};

			library.json = {
			   replacer: function(match, pIndent, pKey, pVal, pEnd) {
				      	var key = '<span class=json-key>';
				      	var val = '<span class=json-value>';
				      	var str = '<span class=json-string>';
				      	var r = pIndent || '';
					     	if (pKey)
					        	r = r + key + pKey.replace(/[": ]/g, '') + '</span>: ';
					      	if (pVal)
					        	r = r + (pVal[0] == '"' ? str : val) + pVal + '</span>';
			      		return r + (pEnd || '');
			      },
				  prettyPrint: function(obj) {
				  	var jsonLine = /^( *)("[\w]+": )?("[^"]*"|[\w.+-]*)?([,[{])?$/mg;
				    	return JSON.stringify(obj, null, 3)
				        	.replace(/&/g, '&amp;').replace(/\\"/g, '&quot;')
				        	.replace(/</g, '&lt;').replace(/>/g, '&gt;')
				        	.replace(jsonLine, library.json.replacer);
				      	}
				};
				$(function(){
					$('.json_preview').each(function(){
						var jsp_text = jQuery.parseJSON($(this).text());
						$(this).html(JSON.stringify(jsp_text, null, 4));
					});
				})
			
		</script>
	</body>
</html>
