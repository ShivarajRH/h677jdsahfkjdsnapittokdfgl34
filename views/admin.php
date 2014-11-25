<?php 
if(!defined("APL_VER"))
	define("APL_VER",rand(0,9).".".rand(1,40).".".rand(100,999));
?>
<?php 
	$user=$this->session->userdata("admin_user");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Snap It Today - Admin</title>

<meta http-equiv="expires" content="<?php echo date("r", time()+60*60*24); ?>" />

<script type="text/javascript">
	var base_url = '<?php echo base_url()?>';
	var site_url = '<?php echo site_url()?>';
	var images_url = '<?=IMAGES_URL?>';
</script>
<?php 
/*
  
<link type="text/css" rel="stylesheet" href="<?=base_url()?>css/admin.css?v=<?=str_replace(".","",APL_VER)?>">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>css/erp.css?v=<?=str_replace(".","",APL_VER)?>">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>css/jquery-ui-lib/jquery-ui-1.10.2.custom.min.css?v=<?=str_replace(".","",APL_VER)?>">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>css/chosen.css?v=<?=str_replace(".","",APL_VER)?>">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>css/jquery.qtip.min.css?v=<?=str_replace(".","",APL_VER)?>">
 
<script type="text/javascript" src="<?=base_url()?>js/jquery-1.8.1.min.js?v=<?=str_replace(".","",APL_VER)?>"></script>
<script type="text/javascript" src="<?=base_url()?>js/func.js?v=<?=str_replace(".","",APL_VER)?>"></script>
<script type="text/javascript" src="<?=base_url()?>js/jquery-ui-1.10.2.custom.min.js?v=<?=str_replace(".","",APL_VER)?>"></script>
<script type="text/javascript" src="<?=base_url()?>js/jqmanageList.js?v=<?=str_replace(".","",APL_VER)?>"></script>

<script type="text/javascript" src="<?=base_url()?>js/chosen.jquery.min.js?v=<?=str_replace(".","",APL_VER)?>"></script>
<script type="text/javascript" src="<?=base_url()?>js/jquery.inlineclick.js?v=<?=str_replace(".","",APL_VER)?>"></script>
<script type="text/javascript" src="<?=base_url()?>js/jquery.qtip.min.js?v=<?=str_replace(".","",APL_VER)?>"></script>
<script type="text/javascript" src="<?=base_url()?>js/jquery.tablesorter.js?v=<?=str_replace(".","",APL_VER)?>"></script>
<script type="text/javascript" src="<?=base_url()?>js/parsley.js?v=<?=str_replace(".","",APL_VER)?>"></script>
<script type="text/javascript" src="<?=base_url()?>js/erp.js?v=<?=str_replace(".","",APL_VER)?>"></script>
 
*/

?>

<link type="text/css" rel="stylesheet" href="<?=base_url()?>/min/index.php?g=erp_css&<?php echo strtotime(date('Y-m-d'));?>&1=1">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/min/index.php?g=jqplot_css&<?php echo strtotime(date('Y-m-d'));?>&1=1">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/min/index.php?g=datatable_css&<?php echo strtotime(date('Y-m-d'));?>&1=1">


<script type="text/javascript" src="<?=base_url()?>/min/index.php?g=erp_js&<?php echo strtotime(date('Y-m-d'));?>&1=1"></script>
<script type="text/javascript" src="<?=base_url()?>/min/index.php?g=jqplot_js&<?php echo strtotime(date('Y-m-d'));?>&1=1"></script>
<script type="text/javascript" src="<?=base_url()?>/min/index.php?g=datatable_js&<?php echo strtotime(date('Y-m-d'));?>&1=1"></script>


<style>
table{font-size:inherit;}
</style>
</head>
<body <?php if(isset($smallheader)) echo 'class="maincontainer"'?>>

<div id="pop_info"><?php if($this->session->flashdata("erp_pop_info")){ ?><?=ucfirst($this->session->flashdata("erp_pop_info"))?><?php }?></div>
<div id="connection_state" class="network_status"></div>
<div align="center" class="maincontainer container">
<div align="center">
<?php 
	$this->load->view("admin/header");
?>
<div id="content"  align="center">
<table width="100%" cellpadding=0 cellspacing=0 class="contenttable" height="550">
<tr>
<?php if($user){?>
<td class="leftcont">
</td>
<?php }?>
<td class="rightcont">
<?php
	switch($page)
	{
		case "default" :
				$this->load->view("admin/body/default");
				break;
		case "signedin" :
				$this->load->view("admin/body/signedin");
				break;
		case "invite" :
				$this->load->view("admin/body/invite");
				break;
		case "show" :
				$this->load->view("admin/body/show");
				break;
		case "showall" :
				$this->load->view("admin/body/showall");
				break;
		case "showroom" :
				$this->load->view("admin/body/showroom");
				break;
		case "info":
				$this->load->view("admin/body/info");
				break;
		case "signup":
				$this->load->view("admin/body/signup");
				break;
		case "myexplo":
				$this->load->view("admin/body/myexplo");
				break;
//superadmin	
		case "superadmin_default":
				$this->load->view("admin/body/superadmin/default");
				break;			
		case "superadmin_addbrand":
				$this->load->view("admin/body/superadmin/addbrand");
				break;
		case "superadmin_viewadmin":
				$this->load->view("admin/body/superadmin/viewbrandadmin");
				break;
		case "superadmin_changepwd":
				$this->load->view("admin/body/superadmin/superadminchangepwd");
				break;
		case "superadmin_addbrands":
				$this->load->view("admin/body/superadmin/addbrands");
				break;
		case "superadmin_editaddcategories":
				$this->load->view("admin/body/superadmin/editaddcategories");
				break;	
		case "superadmin_editcatandbrand":
				$this->load->view("admin/body/superadmin/editspecificcatandbrand");
				break;	
        case "superadmin_productsunderbrand":
				$this->load->view("admin/body/superadmin/productsunderbrand");
				break;
        case "superadmin_comments":
        		$this->load->view("admin/body/superadmin/comments");
        		break;
        case "superadmin_activity":
        		$this->load->view("admin/body/superadmin/activity");
        		break;
        case "superadmin_brand":
        		$this->load->view("admin/body/superadmin/brand");
        		break;
        case "superadmin_users":
        		$this->load->view("admin/body/superadmin/users");
        		break;
        case "superadmin_userdetails":
        		$this->load->view("admin/body/superadmin/userdetails");
        		break;
        case "superadmin_coupons":
        		$this->load->view("admin/body/superadmin/coupons");
        		break;
        	
//common
        case "search":
        		$this->load->view("admin/body/admin/search");
        		break;
       
//admin
        case "admin_orders" :
        		$this->load->view("admin/body/admin/orders");
        		break;
        case "admin_bulk_orders" :
        		$this->load->view("admin/body/admin/bulk_orders");
        		break;
        case "admin_vieworder":
        		$this->load->view("admin/body/admin/vieworder");
        		break;
		case "admin_allotment_list":
        		$this->load->view("admin/body/admin/allotment_list");
        		break;
		case "adminlogin" :
				$this->load->view("admin/body/admin/adminlogin");
				break;
		case "adddeal" :
				$this->load->view("admin/body/admin/adddeals");
				break;	
		case "adduser" :
				$this->load->view("admin/body/admin/adduser");
				break;
		case "admin_viewuser" :
				$this->load->view("admin/body/admin/viewuser");
				break;
		case "admin_changepwd" :
				$this->load->view("admin/body/admin/changepassword");
				break;
		case "admin_viewdeals" :
				$this->load->view("admin/body/admin/viewdeals");
				break;
		case "additems" :
				$this->load->view("admin/body/admin/additems");
				break;	
		case "admin_editroom" :
				$this->load->view("admin/body/admin/editroom");
				break;
		case "admin_default":
				$this->load->view("admin/body/admin/default");
				break;
		case "admin_deletepics" :
				$this->load->view("admin/body/admin/deletepicvideo");
				break;
		case "addpics" :
				$this->load->view("admin/body/admin/addmorepics");
				break;
		case "admin_signin" :
				$this->load->view("admin/body/admin/adminsignin");
				break;
		case "sms_template":
				$this->load->view("admin/body/sms_template.php");
				break;
		case "pnh_exsms_log":
			$this->load->view("admin/body/pnh_exsms_log.php");
			break;
		case "pnh_bulk_offeradd":
			$this->load->view("admin/body/pnh_bulk_offeradd.php");
			break;
		case "pnh_activation":
			$this->load->view("admin/body/activation_form.php");
			break;
		default:
				$this->load->view("admin/body/$page");
	}
?>
</td>
</tr>
</table>
<a href="javascript:void(0)" class="page_print_button">print</a>
</div>
</div>

<div id="strip_itemlist_wrap" style="display: none"></div>
<div align="center">
<?php 
	$this->load->view("admin/footer");
?>
</div>

<?php if(!isset($smallheader)){?>
</div>
<?php }?>
<style>
	.network_status{display: none;background: #ffffa0;padding:5px;font-weight: bold;font-size: 18px;text-align: center;position: fixed;top: 0px;width: 100%;}
	.network_offline{background: tomato !important;color: #FFF; !important}
	.network_offline a{color: #222;font-size: 12px;}
	#strip_itemlist_wrap{padding:8px 1%;position: fixed;bottom:0px;width:98%;left:0px;background: #FFFFCC;color:#000}
	#strip_itemlist_wrap a{font-size: 11px;color: maroon}
	.strip_itemlist{text-align: right}
	.ui-widget{font-family: arial}

	.datagrid {border-collapse: collapse;border:none !important}
	.datagrid th{border:none !important;font-size: 12px;padding:5px 5px;text-align: left;}
	.datagrid td{border-right:none;border-left:none;border-bottom:1px dotted #ccc;font-size: 12px;line-height: 18px;
	font-size: 12px;
	padding: 6px 5px !important;}
	.datagrid td a{text-transform: capitalize}

</style>
<script type="text/javascript">
	<?php 
	if($page != 'pnh_offline_order'){
		echo 'get_panel_alerts();';
	}
	?>
</script>


</body>
</html>
