<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| 	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['scaffolding_trigger'] = 'scaffolding';
|
| This route lets you set a "secret" word that will trigger the
| scaffolding feature for added security. Note: Scaffolding must be
| enabled in the controller in which you intend to use it.   The reserved 
| routes must come before any wildcard or regular expression routes.
|
*/

$route['default_controller'] = "deals";
$route['scaffolding_trigger'] = "oeifj8493utr8934j98t3ut89f34h89h3498t83ehfgwet89wufojwdi203920";

//$route['men']="deals/menu/Men-m2u";
//$route['women']="deals/menu/Women-m10u";
//$route['kids']="deals/menu/Kids-m3u";
//$route['home']="deals/menu/Home-m4u";
//$route['travel']="deals/menu/Travel-m1u";
//$route['electronics']="deals/menu/Electronics-m3u";
//$route['health']="deals/menu/Health-m2u";


$route['data_api/(:any)']="data/index/$1";
$route['data_api']="data/index";

$route['pnh/(:any)']="pnh/$1";
$route['pnh']="pnh";


$urs=array("claim_points","destroybodp","headtotoe","live","history","startinviting","jx_checkoutstat","jx_checkoutcond","weeklysavings","loadfavs","shoppingcart","favs","opsearch","brands","getverifiedbymob","editqty","yourcart","checkout_inter","getverified","inviteforbp","inviteforbp_nonc","updatecrp","dashboard","spotlight","processPayment","myorders","jx_subscribe","jx_fdback","jx_alert","jx_request","recent","loginpanel","joinhands","jxcoupon","whatru","emailsignup","emailsignin","nomobile","gomobile","profile","changepwd","changeaddr","jxforpass");
foreach($urs as $u)
	$route[$u]="deals/$u";
	
//jxs	
$urs=array("thumbsupreview","invitefbfriends","subscribeaction","useraccheck","noannounce","getattention","remindme","sugst_search","checkcodpin","gb_redirect","extendbp","updatemob","clearcoupon","viewcoworkers","startbuyprocess","lookingto","isemailavail","writereview","reviews");
foreach($urs as $ur)
	$route["jx/$ur"]="deals/jx_$ur";

$pages=array("faqs","about_us","returns","shipping_policy","cancellation_policy","disclaimer","contact_us","help_tags");
foreach($pages as $ui)
	$route[$ui]="deals/pages/$ui";
	
//socio
$urs=array("fbinviteforbp");
foreach($urs as $r)
	$route[$r]="socio/$r";	

$route['auth/(:any)']="socio/auth/$1";	
$route['twsignin']="socio/twsignin";

$route['jx_savefs']="deals/jx_savefs";

//one arg
$urs=array("viewbycat","viewbycatbrand","viewbybrandcat","viewbybrand","viewbymenucat","viewbymenubrand","viewbymenu","promo_emails","featured_brand","claim","reorder","jx_freesamples","transaction","opsearch","choosefav","selectfav","checkbps","campaign","getverified","");
foreach($urs as $u)
	$route["$u/(:any)"]="deals/$u/$1";

$route['api/buy/(:any)']="api/buypartitem/$1";
$route['headtotoe']="deals/productsforwholebody";
$route['headtotoe/(:any)']="deals/productsforwholebody/$1";

$route['deals/editqty']="deals/editqty";
$route['login']="deals";
$route['signup']="deals";
$route['register']="deals";

$route['snapit/(:any)']="deals/snapit/$1";
$route['buy/(:any)']="deals/buy/$1";
$route['verifyh/(:any)']="deals/verifyh/$1";

$route['rmitem/(:any)']="deals/rmitem/$1";
$route['resetpass/(:any)']="deals/resetpass/$1";

//$route['signin']="deals/signin";
$route['signout']="deals/signout";
$route['signout/(:any)']="deals/signout/$1";
$route["deal/(:any)"]="deals/show/$1";
$route['checkout']="deals/checkout";
$route['checkout/(:any)']="deals/checkout/$1";
//$route['deals']="deals/new_showall";
$route['fblogin']="socio/fblogin";
//$route["roomdeal/(:any)"]="deals/showroom/$1";
$route["invite"]="deals/invite";
$route['invite/(:any)']="deals/processinvite/$1";
$route['invitebyemail']="deals/invitebyemail";
//$route['myksale']="deals/myksale";
//$route['twsignin']="socio/twsignin";
//$route['gsignin']="deals/gsignin";
//$route['twredirect']="deals/twredirect";
//$route['twinvite']="deals/twinvite";
//$route['fbinvite']="deals/fbinvite";
//$route['fbgetperm']="deals/fbgetperm";
$route['category/(:any)']="deals/category/$1";
$route['brand/(:any)']="deals/brand/$1";
$route['viewcart']="deals/viewcart";
$route['upcoming']="deals/upcoming";
$route['processCheckout']="deals/processCheckout";
//$route['showcomments/(:any)']="deals/showcomments/$1";
//$route['preview/(:any)']="deals/showpreview/$1";
//$route['previewitem/(:any)/(:any)']="deals/showpreviewitem/$1/$2";
//$route['fbthis/(:any)']="deals/fbthis/$1";
//$route['twthis/(:any)']="deals/twthis/$1";
//$route['api/(:any)']="deals/api/$1";
$route['search']="deals/search";
//$route['groupsales']="deals/groupsales";
$route['deal/(:any)']="deals/deal/$1";
$route['agent']="deals/agent";
//$route['procagentlogin']="deals/procagentlogin";
$route['orders']="deals/orders";
$route['order/(:any)']="deals/order/$1";
$route['view_invoice/(:any)/(:any)']="deals/view_invoice/$1/$2";
//$route['salesreport']="deals/salesreport";
//$route['widget/(:any)']="deals/widget/$1";
$route['privacy_policy']="deals/privacy_policy";
$route['delivery_policy']="deals/delivery_p";
$route['terms']="deals/terms";
$route['newpricerequest']="deals/pricereq";
$route['checkextdet']="deals/checkextdet";
$route['updatedet']="deals/updatedet";
$route['readytobuy']="deals/readybuy";
//$route['showsaleitem/(:any)']="deals/showsaleitem/$1";

$route['comments/(:any)/(:any)/(:any)']="deals/showcomments/$3";
$route['sale/(:any)/(:any)/(:any)']="deals/showsale/$3";
$route['saleitem/(:any)/(:any)/(:any)/(:any)']="deals/showsaleitem/$4";
$route['saleitem/(:any)']="deals/showsaleitem/$1";

$route['pr/(:any)']="deals/procpr/$1";

$route['getstarted']="deals/getstarted";
$route['newuser_guide']="deals/newuser_guide";

$route['jx/sendmail']="deals/jxsendmail";
$route['jx/fbthisuser/(:any)']="deals/jxfbthisuser/$1";
$route['jx/viewsavedcarts']="deals/jxviewsavedcarts";
$route['jx/loadsavedcart/(:any)']="deals/jxloadsavedcart/$1";
$route['jx/viewsavedcart/(:any)']="deals/jxviewsavedcart/$1";
$route['jx/savecart/(:any)']="deals/jxsavecart/$1";
$route['jx/deletecartitem/(:any)']="deals/jxdeletecartitem/$1";
$route['jx/shownocartitems']="deals/jxshownocartitems";
$route['jx/destroycart']="deals/jxdestroycart";
$route['jx/showcart']="deals/jxshowcart";
$route['jx/addtocart/(:any)']="deals/jxaddtocart/$1";
$route['jx/addtocart']="deals/jxaddtocart";
$route['jx/editprofile']="deals/jxeditprofile";
$route['jx/fbinviteuser']="deals/jxfbinviteuser";


foreach(array("Hanmade-Soaps"=>"Handmade-Soaps") as $m=>$a)
{
	$route[$m]="deals/trig_url/$a";
	$route["(:any)/$m"]="deals/menuncat/$1/$a";
}

foreach(array("Health","Gifts","Beauty") as $u)
	$route[$u]="deals/special_index/$u";

/* */																																																							
$route['(:any)-p(:num)t']="deals/deal/$1-p$2t";
$route['(:any)-m(:num)u/(:any)-c(:num)y']="deals/menuncat/$1-m$2u/$3-c$4y";
$route['(:any)-m(:num)u']="deals/menu/$1-m$2u";
$route['(:any)-c(:num)y']="deals/category/$1-c$2y";

$route['admin']="admin";
$route['callcenter']="callcenter";
$route['cron']="cron";


$route['admin/reports/(:any)']="reports/$1";

$route['campaigns/(:any)']="newsltr/view/$1";
$route['newsltr/(:any)']="newsltr/$1";
$route['admin/(:any)']="admin/$1";
$route['callcenter/(:any)']="callcenter/$1";
$route['cron/(:any)']="cron/$1";
$route['statics/(:any)']="statics/$1";
$route['work/(:any)']="work/$1";

$route['discovery']="discovery/index";
$route['discovery/(:any)']="discovery/$1";


$route['trend/(:any)']="trends/trend/$1";

$route['(:any)/(:any)']="deals/menuncat/$1/$2";
$route['(:any)']="deals/trig_url/$1";

/* End of file routes.php */
/* Location: ./system/application/config/routes.php */
