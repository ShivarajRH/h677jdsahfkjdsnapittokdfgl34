<html>
<head>
<title>Snapittoday.com</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
</head>
<body style="background: #d0d0d0;padding: 0px;margin: 0px;font-size: 12px;font-family:  Helvetica,tahoma,Arial,Verdana,sans-serif;color: #494948;">
	<div id="wrapper" style="width: 800px;margin: 0px auto;background: #fff;">
		 
		<div id="header" style="background: #FFF;color: #FFF" > 
			
			<div style="background: #FFF">
				<p align="center" style="font-size: 10px;margin: 0px;background: #e7e7e7;color: #000">
					To ensure delivery to inbox, add <a href="mailto:contact@snapittoday.com" style="color: #3E3D45;">contact@snapittoday.com</a> to your address book<br />
					If you cannot see this message <a href="<?php echo current_url();?>" style="color: #3E3D45;">click here</a>
				</p>
				<div id="sitelogo" align="center" title="snapittoday.com - redefine your lifestyle" style="background: #FFF">
					<a href="<?php echo $campaign_det['banner_link']?>" style="font-size: 30px;color:#AF1A2D;text-decoration: none;border: none; ">
						<img src="<?php echo IMAGES_URL?>newsletter/banners/<?php echo $campaign_det['banner_image']?>" alt="<?php echo $campaign_det['title']?>"  style="border: none;margin-top: 10px;position: relative;top: 5px;"  />
					</a>	
				</div>
				<?php /*?> 
				<div  id="sitelogo" align="center" title="snapittoday.com - redefine your lifestyle" style="background: #000;padding:15px;padding-left: 20px;height: 90px;display: none;">
					<div style="color: #FEFEFE;font-size: 12px;letter-spacing: 0.3px;padding: 5px;text-align: left;float: right;width: 425px;color: #eee;padding-left: 16px;">
						<div style="padding:6px 10px 10px;line-height: 19px;"><a href="http://snapittoday.com" style="color: #FFF;text-decoration: none;"><span style="color: #FFED00;">Snap</span>ittoday.com</a> is one stop destination for your personal care, fashion and lifestyle essentials. 
							It offers products from top brands.
						</div> 
						<span style="float: right;position: relative;top: 10px">
							<b style="position: relative;top: 0px;font-size: 16px">Like us on  : </b>
							<a style="color: #FFF;margin-left: 8px;" href="http://www.facebook.com/snapittoday"><img height="32" src="<?php echo IMAGES_URL.'/facebook.png'?>" title="on facebook" alt="on facebook" /></a>
							
							<a style="color: #FFF;margin-left: 8px;" href="http://www.twitter.com/snapittoday" ><img height="32" src="<?php echo IMAGES_URL.'/twitter.png'?>" title="on twitter" alt="on twitter" /></a>
						</span>
					</div>
					<a href="<?php echo site_url('')?>" style="font-size: 30px;color:#FFED00;text-decoration: none; ">
						<img src="<?php echo IMAGES_URL?>newsletter/logo-medium.png" alt="Snapittoday.com"   />
					</a>	
				</div>
				
				
			<div style="margin-top: 5px;font-size: 8px;padding:5px;color: #FFF;border-top: 3px solid #FFF;display: none;" align="center">
				 <table width="750" align="center" style="color: #FFF">
				 	<tr>
				 		<td>
				 			<b class="banner-text2">Upto 40% <span>discount</span> </b> 
				 		</td>
				 		<td  width="100" align="center"> <b class="arrow-style"> &gt; </b> </td>
				 		<td width="">
				 			<b class="banner-text2">4000 <span>products</span> </b>   
				 		</td>
				 		<td  width="100" align="center"> <b class="arrow-style">&gt;</b> </td>
				 		<td>
				 			<b class="banner-text2">door <span>delivery</span> </b>   
				 		</td>
				 		 
				 	</tr>
				 </table>
			</div> 
			<?*/?>
			
			<div style="margin-top: 5px;font-size: 8px;color: #999;border-top: 3px solid #FFF;font-size: 18px;padding:3px 0px" align="center">
				 <img style="width: 798px" alt="4000 Products - Free Shipping - Cash on Delivery - Dispatches in 24 Hours " title="4000 Products - Free Shipping - Cash on Delivery - Dispatches in 24 Hours" src="<?php echo base_url().'/images/newsletter/site-text.png'?>"  />
			</div> 
			
			</div> 
			<br />
			<div style="background:#fff;color:#000;" align="center">
				 
				  
					 
					<?php 
						
						if($deal_list){
					?>
						 <table cellpadding="5" cellspacing="20" width="100%">
						 <tr>
						 	<?php 
						 		$i = 0;
								foreach($deal_list as $row){
									if($i%3==0)
										echo '</tr><tr>';
									 
							?>
								<td valign="top" align="center" title="<?php echo $row['name']?>" style="border:1px solid #e3e3e3;background: #f1f1f1">
										<div style="width: 230px;">
											<div align="center" style="overflow: hidden;background: #FFF;padding:10px;">
												<a href="<?php echo site_url($row['url'])?>" style="color: #777;font-size:12px;;text-decoration: none;cursor: pointer;height: 125px;display: block;">
													<img title="<?php echo $row['name'].' Rs '.$row['price']?>" alt="<?php echo $row['name'].' - Rs '.$row['price']?>" src="<?php echo IMAGES_URL.'/items/small/'.$row['pic'].'.jpg'?>"  />
												</a>
											</div>
											<div style="padding:5px;">
												 
												<div style="font-size: 12px;">
													<div style="height: 20px">
													<a href="<?php echo site_url($row['url'])?>" style="color: #444;text-decoration: none;cursor: pointer;">
														<?php echo strip_string(ucfirst($row['name']), 100)?>
													</a>
													</div>
													<br />
													
													<a href="<?php echo site_url($row['url'])?>" style="color: #444;text-decoration: none;cursor: pointer;">
														
														<b style="color: #cd0000;font-size: 16px;">Pay Rs <?php echo $row['price']?></b> 
													<?php 
														if($row['orgprice'] > $row['price']){
													?>
													&nbsp;
													(<strike style="color: #5B9F33">Rs <?php echo $row['orgprice']?></strike>)  
													<br /> 
													</a>
													<div style="font-size: 14px;margin-top: 4px;">
														You Save <b>Rs <?php echo $row['orgprice']-$row['price']?></b>
													</div>
													<?php } ?>
												</div>
											</div>
										</div>
								</td>
							<?php 
								$i++;								
								}
							?>	
							</tr>
						 </table>
					<?php 		
						}
					?>
				
			</div>
			<br />
			<div style="border-bottom: 1px solid #e3e3e3;padding-bottom: 5px;margin-bottom: 20px;">
				 <img src="<?php echo base_url().'/images/'?>newsletter/popular-brands.png" alt="Popular Brands"  title="Popular Brands on snapittoday.com" />
			</div>
		</div>
		 
		 
		<div id="body-content" align="center" >
			<div style="width: 750px;">
			<fieldset style="color: #494948">
				<legend>
			<div class="bannertext" align="left" style="padding:21px 40px;cursor: pointer;"  title="snapittoday.com - redefine your lifestyle">
				<span>
					Introducing 
				</span>
				<span><b style=" font-size: 18px;margin-left: 23px;margin-right: 15px;"> &rdquo; Tag your discovery &rdquo;</b></span>
				<span style="float: right">
					<a href="http://snapittoday.com/discovery" style="font-size: 18px;text-decoration: none;color:#494948;" target="_blank"><img  src="<?php echo IMAGES_URL?>newsletter/banner-text2.png" alt="Share and discover things you love" /></a>
					
				</span>
			</div>
			 </legend>
			<div align="center" >
 <br />
					<a style="overflow: hidden;;padding:10px;background: #f8f8f8;color:#494948;" href="http://snapittoday.com/discovery" target="_blank" title="snapittoday.com - redefine your lifestyle">
						<img src="<?php echo IMAGES_URL?>newsletter/products-overview.png" alt=" " style="border:none;"  />
					</a>
				  <br />
					<br />
					<br />
				<a href="http://snapittoday.com/discovery" style="font-size: 18px;text-decoration: none;color:#494948;" target="_blank"><img src="<?php echo IMAGES_URL?>newsletter/flow.png" alt="Create a board  tag your interest  discover new interests" /></a>
					
			</div>
			
			</fieldset>
			</div>
			
			 <br />
			  
			<div style="border-bottom: 1px solid #ccc">&nbsp;</div>
		</div>
		<div id="footer" style="padding:10px;">
			<table width="100%" style="font-size: 13px;">
				<tr>
					<td align="center" style="font-size: 12px;color: #2A00F4">
						&copy; Snapittoday,All rights reserved. Refer Terms & conditions - <a href="mailto:contact@snapittoday.com" style="color: #2A00F4">contact@snapittoday.com</a> 
					</td>
				</tr>
			</table>
		</div>
	</div>
	<br />
</body>
</html>