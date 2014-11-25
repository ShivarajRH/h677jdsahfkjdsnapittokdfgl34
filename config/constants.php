<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define("AUTH_LOGIN_DOMAIN",'snapittoday.com');

define('FOPEN_READ', 							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 					'ab');
define('FOPEN_READ_WRITE_CREATE', 				'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 			'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

define('APP_DOMAIN_LINK',"snapittoday.com");
define('IMAGES_URL',"http://static.snapittoday.com/");
define('ERP_IMAGES_URL',"http://static.snapittoday.com/erp_images/");
define('ERP_PHYSICAL_IMAGES',"images/erp_images/");
define('RESOURCE_IMAGES',"resources/returns_images/");
define('SNDBOX_RESOURCE_IMAGES',"http://sndev13.snapittoday.com/resources/returns_images/");

define('FB_APPID','127409457364009');
define('FB_SECRET','d85747b3cf547800c8653de33d0770ad');

define('TW_APPID','eX5CKmOfXvYEHYQHAcnyZw');
define('TW_SECRET','LiR1FJA1DsdtGajwRxeEWMVyIpoHWc7swcAIyXuaDc');

define("IN_APPID",'w99pxd2mhu3y');
define('IN_SECRET','PNTbSxUOoWTDKo8X');

define("GM_SITEID",'16118413262428341303');
define("GM_APPID",'16118413262428341303');
define('GM_SECRET','t6mpkZ5R0xo=');


define('GMAP_KEY','ABQIAAAAeNP67zzDfiHO9x8bftOY2hR2xb83VVuvy_z2dBdLSs4PrqlsDRTq2xdzrgkulx3f5mTw1MTDO-tH9Q');

define("CS_TELEPHONE","+91-92-4340-4342");
define("CS_EMAIL","hello@snapittoday.com");

define('REQUEST_URI',$_SERVER['REQUEST_URI']);
define("CRON_IMAGES_LOC","/home/snapitto/cron_images_updater/imgs/subfolder/images/");

//===========< SMS SETTINGS >=========================
define("EXOTEL_UID","snapittoday-222222");
define("EXOTEL_AUTHKEY","491140e9fbe5c507177228cf26cf2f09356e042c-test");
define("EXOTEL_MOBILE_NO",'9243404342-2222');
//===========< SMS SETTINGS >=========================

//===========< CUSTOMER TOLL FREE NUMBER >============
define("TOLL_FREE_NUMBER",'1800 200 1996');

define('HTTP_IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

define('GLOBAL_BATCH_ID', 7000);

// Default member recharge value
define('PNH_MEMBER_FREE_RECHARGE',100);
// Max feedback range value
define("MAX_RATE_VAL",5);
// member minimum order value
define("MEM_MIN_ORDER_VAL",500);

define("MIN_INSURANCE_PRODUCT_VALUE",5000);

// member minimum order value
define("API_USER_ID",60);

//vendor api resources 
define("VENDOR_CATALOG_FILE_PATH","resources/ven_order_files/");
define("VENDOR_ORDES_CSV_PATH","resources/ven_order_files/");

//Top sold products on number of quantity sold --- Default
define("QUANTITY_CONST",2);

//Reorder qty Default value
define("REORDER_QTY",60);

//OTP validity 
define("OTP_VALIDITY",86400);

//SMS API Constants
define("MEM_REG_OK","MEMBER Registered Successfully");
define("MEM_ALREG","MEMBER Already Registered");
define("MOB_INVALID","Invalid Mobile no");
define("PROD_INVALID","Invalid Product ID");
define("NO_CREDIT","You dont have enough credit to place this order");
define("MARKET_PLACE_SELLER_PROFIT_VAL",10);
/* End of file constants.php */
/* Location: ./system/application/config/constants.php */