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
<script type="text/javascript" src="<?=base_url()?>/min/index.php?g=erp_js&<?php echo strtotime(date('Y-m-d'));?>&1=1"></script>
<script type="text/javascript" src="<?=base_url()?>/min/index.php?g=jqplot_js&<?php echo strtotime(date('Y-m-d'));?>&1=1"></script>


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
	.datagrid th{border:none !important;font-size: 12px;padding:7px 5px;text-align: left;}
	.datagrid td{border-right:none;border-left:none;border-bottom:1px dotted #ccc;font-size: 12px;line-height: 18px;
	font-size: 12px;
	padding: 8px 5px !important;}
	.datagrid td a{text-transform: capitalize}

</style>
<script type="text/javascript">
	$(function(){
		$('title').html($('h2:first').text());
	});
	
	function handle_quote_request_call(fr_id)
	{
		location.href = site_url+'/admin/pnh_quotes/'+fr_id;
	}
	
	function animate_strip(tick_width,tspan)
	{
		 
		$('#strip_itemlist_wrap .strip_itemlist').animate({left:-(tick_width+100)},tspan,'linear',function(){
				get_panel_alerts();
			}).queue(function(){
				var _this = $(this);
				    _this.dequeue();
			});
		$('#strip_itemlist_wrap .strip_itemlist').mouseenter(function(){
			$(this).stop();
		});
		
		$('#strip_itemlist_wrap .strip_itemlist').mouseleave(function(){
			animate_strip(tick_width,tspan);
		});
	}
	
	function get_panel_alerts()
	{
		
		 $.post(site_url+'/admin/get_panel_alerts','',function(resp){
		 	if(resp.status == 'success')
		 	{
		 		$('#strip_itemlist_wrap').show();
		 		var tick_width = resp.quote_list.length*500; 
		 		
		 		var ticker_html = '<div class="strip_itemlist" style="width:'+tick_width+'px;font-size:12px;position:relative;left:0px;">';
		 			$.each(resp.quote_list,function(a,b){
		 				ticker_html += '<div class="strip_item"  style="width:400px;display:inline;margin-right:10px;">  '+(a+1)+') '+b+' </div> ';
		 			});
		 			ticker_html += '</div>';
		 		$('#strip_itemlist_wrap').html(ticker_html);
		 		
		 		var tick_width = 0;
		 			$('#strip_itemlist_wrap .strip_item').each(function(){
		 				tick_width += ($(this).width()+10)*1;
		 			});
		 			var tspan = tick_width*20;
		 			
		 			if(tick_width < $(document).width())
		 				tick_width = $(document).width();
		 				
		 			$('#strip_itemlist_wrap .strip_itemlist').width(tick_width);
		 			
		 		animate_strip(tick_width,tspan);
		 	}else
		 	{
		 		$('#strip_itemlist_wrap').hide();
		 		setTimeout(function(){
		 			get_panel_alerts();
		 		},10000);
		 	}
		 },'json');
	}
	<?php 
	if($page != 'pnh_offline_order'){
		echo 'get_panel_alerts();';
	}
	?>
	
	var cur_server_status = 1;
	
	function updateConnectionStatus(statMsg,stat)
	{
		cur_server_status = stat;
		$('#connection_state').removeClass('network_offline').hide();
		$('#connection_state').html("Network status : "+statMsg+' <a href="javascript:void(0)" onclick="upd_server_status()">Check now</a>')
		if(!stat)
		{
			$('#connection_state').addClass('network_offline').show();
			$('#connection_state').show();
			
			$(window).on('beforeunload',function() {
			    return "Server Offline";
			});	
			
		}else
		{
			$('#connection_state').hide();
			$(window).unbind('beforeunload');
		}
		
	}
	function upd_server_status()
	{
		$('#connection_state').html("Checking server status...");
		
			
		$.ajax({
	          url: site_url+'/admin/ping/<?php echo $page?>',
	          success: function(result){
	            if(result == 'pong')
	            {
	            	updateConnectionStatus("",1);	
	            }else if(result == 'nodb')
	            {
	            	updateConnectionStatus("Database Connection Failed",0);
	            }else if(result == 'loggedout')
	            {
	            	updateConnectionStatus("Login Expired - <a href='"+site_url+"/admin' target='_blank' >Click here to relogin</a>",0);
	            }
	            setTimeout(function(){
					upd_server_status();
				},2000);
	          },     
	          error: function(result){
	             updateConnectionStatus("Server Offline",0);
	             setTimeout(function(){
					upd_server_status();
				},5000);
	          }
	       });
	}
	
	$(function(){
		//upd_server_status();	
	});
	
</script>


</body>
</html>
