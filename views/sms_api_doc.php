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
		<h2>Storeking TAB SMS Api Documentation</h2>
	
	<div class="api_list">
			<!-- ===============< API LIST BLOCK START >=========-->
			<div class="api_det">
				<h4>API documentation URL</h4>
				<code>
					<?php echo site_url('api/sms_api_doc')?>
				</code>
				<p class="api_desc">
					Request to view api documentation 
				</p>
			</div>
			
			<div class="api_det">
			<h4>  SMS Short Code with Description</h4>
				<code>
					
					<short_code><b>MEM_REG_OK</b>&nbsp;=><Desc> MEMBER Registered Successfully
					<br>
					<short_code><b>MEM_ALREG</b>&nbsp;=><Desc> MEMBER Already Registered
					<br> 
					<short_code><b>MEM_REG_NO</b>&nbsp;=><Desc> Unable to register member
					<br>
					<short_code><b>PROD_INVALID</b>&nbsp;=><Desc> Invalid Product ID
					<br>
					<short_code><b>MOB_INVALID</b>&nbsp;=><Desc> Invalid Mobile no
					<br>
					<short_code><b>NO_CREDIT</b>&nbsp;=><Desc> You dont have enough credit to place this order
					<br>
					<short_code><b>UNCONFIRMED_CREDITORDER</b>&nbsp;=><Desc> We shall confirm this to you in a short while based on your acceptable credit Limit! 
				</code>
				<p class="api_desc">
					Request to Register Member  
				</p>
			</div>
			
			<div class="api_det">
				<h4>Login</h4>
				<code>
					<br><b>Request Template </b> : FMT:LG&nbsp;tokenid&nbsp;username&nbsp;password
					<br><b>Success Template</b> : LG||tokenid||OK||AUTHKEY||FranchiseID||FRANCHISE_NAME||FRAN_MOBNO||LG
					<br><b>Error Template</b> : LG||tokenid||ER||ERROR_MSG||ERROR_CODE||LOGIN
					<br><b>Response On Success  </b> :	LG||1236||OK||a9cc6694dc40736d7a2ec018ea566113||465||Bharath Enterprises-CKM||9448854623||LOGIN
					<br><b>Response On Error </b> : LG||0||ER||Please enter valid username and password||500||LOGIN
				</code>
				<p class="api_desc">
					Request to Login 
				</p>
			</div>
					
			<div class="api_det">
			<h4>  Register MEMBER</h4>
				<code>
					<br><b>Request Template </b> : FMT:MR&nbsp;tokenid&nbsp;authkey&nbsp;MEM_MOBNO&nbsp;MEM_NAME
					<br><b>Success Template</b> : MR||tokenid||OK||AUTHKEY||MEM_NAME||MEM_MOBNO||POINTS||MEMBER
					<br><b>Error Template</b> : MR||tokenid||ER||ERROR_MSG||ERROR_CODE||MEMBER
					<br><b>Response On Success </b> :MR||12345||OK||22030442||nithya||8425667896||0||MEMBER
					<br><b>Response On Error </b> : MR||12345||ER||Member Already Registered||200||MEMBER
					
				</code>
				<p class="api_desc">
					Request to Register Member  
				</p>
			</div>
			
			<div class="api_det">
			<h4>  Register Member INFO</h4>
				<code>
					<br><b>Request Template </b> : FMT:MI&nbsp;tokenid&nbsp;authkey&nbsp;MEM_MOBNO
					<br><b>Success Template</b> : MI||tokenid||OK||MemberID||Member name||MEM_NO||Points||MEMBER
					<br><b>Error Template</b> :   MI||tokenid||ER||ERROR_MSG||ERROR_CODE||MEMBER
					<br><b>Response On Error </b> :	MI||12346||ER||No,not Registered||200||MEMBER
					<br><b>Response On Success </b> : MI||12346||OK||22030442||nithya||8425667896||0||MEMBER
				</code>
				<p class="api_desc">
					Request to Member Registerd Details  
				</p>
			</div>
			
			<div class="api_det">
			<h4> Price/Stock Enquiry</h4>
				<code>
					<br><b>Type</b> : POST
					<br><b>Request Template </b> : FMT:PR&nbsp;TOKENID&nbsp;AUTHKEY&nbsp;PID
					<br><b>Success Template</b> : PR||tokenid||OK||PID||MRP||OFFER PRICE||LandingCost||STOCK AVAILABILITY||SIZEDSTOCK||PRODUCT
					<br><b>Error Template</b> :   PR||tokenid||ER||ERROR_MSG||ERROR_CODE||PRODUCT
					<br><b>Response On Error </b> : PR||12346||ER||Invalid ProductID Entered||500||PRODUCT
					<br><b>Response On Success </b> : PR||12346||OK||10004813||165||DP/OP:Rs 165||158.4||Available||0||PRODUCT
				</code>
				<p class="api_desc">
					Request to process price enquiry  
				</p>
			</div>
			

					
			<div class="api_det">
			<h4>Request OTP</h4>
				<code>
					<br><b>Type</b> : POST
					<br><b>Params</b> : franchise_mobno=<b>FROM</b> &amp; message=<b>MESSAGE</b>
				<br><b>Request Template </b> : FMT:ROTP||tokenid||authkey
				<br><b>Error Template </b> : ROTP||tokenid||ER||ERROR_MSG||ERROR_CODE||OTP
				<br><b>Success Template</b> : 	ROTP||tokenid||Status||OTP||Valid Till||OTP
				<br><b>Response On Success </b> : ROTP||12346||OK||282806||30/09/2014 12:20 pm||OTP
				</code>
				<p class="api_desc">
					Request for OTP 
				</p>
			</div>
	
			<div class="api_det">
			<h4>Validate  OTP</h4>
				<code>
				<br><b>Type</b> : POST
				<br><b>Request Template </b> : FMT:VOTP&nbsp;tokenid&nbsp;authkey&nbsp;OTPNO 
				<br><b>Success Template</b> : 	VOTP||tokenid||Status||OTPNO||OTP validity||OTP
				<br><b>Error Template</b> :VOTP||tokenid||ER||ERROR_MSG||ERROR_CODE||OTP
				<br><b>Response On Error </b> : VOTP||12346||ER||Invalid OTP number/OTP number is Expired||500||OTP
				<br><b>Response On Success </b> : VOTP||12346||OK||282806||30/09/2014 12:20 pm||OTP
				</code>
				<p class="api_desc">
					Validate  OTP 
				</p>
			</div>
			<div class="api_det">
			<h4>Check Cart Items</h4>
				<code>
					<br><b>Type</b> : POST
					<br><b>Request Template </b> : FMT:CC 123 AUTHKEY PID1*PQTY1,PID2*PQTY2 MEMID/MEM_MOBNO FRAN_ID/FRAN_MOBNO MEM_POINTS PROMOCODE 
					<br><b>Response Template</b> : 	CC||123||OK||CARTID||PID1:QTY1:MRP-PRICE:DISCOUNT:ATTRS:INSURANCE,PID1:QTY1:MRP-PRICE:DISCOUNT:ATTRS:INSURANCE||MEMID||FRID||POINTS||REDEEM_POINTS||FRANCHISE_BALANCE_AMT||PC_CODE:PC_VAL||CART
					<br><b>Response On Error </b> : CC||123||ER||ERROR_MESSAGE||ERROR_CODE||CART
					<br><b>Response On Success </b> : CC||123||OK||638953141182||1316968:5:12489-12489-0:0:1-50,10017215:100:395-395-0:0:0-0||21111113||17||140||0||504442.4677||0||CART
				</code>
				<p class="api_desc">
					Check cart items API    
				</p>
			</div>

			<div class="api_det">
			<h4>Create Order</h4>
				<code>
				<br><b>Type</b> : POST
					<br><b>Request Template </b> : FMT:ORD 123 AUTHKEY CARTID PID1*PQTY1,PID2*PQTY2 MEMID/MEM_MOBNO FRAN_ID/FRAN_MOBNO MEM_POINTS PROMOCODE 
					<br><b>Response Template</b> : 	ORD||123||OK||CARTID||TRANSID||AMOUNT||STATUS||FRID||MEMID||MESSAGE||ORDER
					<br><b>Response On Error </b> : ORD||123||ER||ERROR_MESSAGE||ERROR_CODE||ORD
				</code>
				<p class="api_desc">
					Api to place order  
				</p>
			</div>
	</div>
	
	</body>
</html>