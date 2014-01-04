	function savecart(){
		cn=prompt("Please enter cart name to save");
		if(cn.length==0)
		{
			alert("Please enter a name to save cart");
			return;
		}
		$("a#fanbhlink").attr("href",site_url+'jx/savecart/'+cn);
		$("#fanbhlink").click();
	}
	function cartlinks(){
		$("a.carthlink").click(function(){
			$("a#fanbhlink").attr("href",$(this).attr("href"));
			$("#fanbhlink").click();
			return false;
		});
	}
	var uctimer;
	function updatecartitems(){
		$.getJSON(site_url+"jx/shownocartitems",function(resp){
			$("#nocartitems").html(resp.items);
			$(".foretop .cartheader .total").html(resp.total);
		});
		window.clearTimeout(uctimer);
		uctimer=window.setTimeout("updatecartitems()",60000);
	}
	function loadcart(id)
	{
		$("#fanbhlink").attr("href",site_url+"jx/loadsavedcart/"+id);
		$("#fanbhlink").click();
	}
function resetHeight()
{
}

$(function(){
	
	$(".refine_but a").click(function(e){
		e.preventDefault();
	});

	$(".search_fltr_box").hover(function(){
		$(this).css("overflow","auto");
	},function(){
		$(this).css("overflow","hidden");
	});
	
	$(".home_exclu").hover(function(){
		$(this).css("overflow","auto");
	},function(){
		$(this).css("overflow","hidden");
	});
	
	$("a.fanblink").fancybox({
		'zoomOpacity'			: true,
		'zoomSpeedIn'			: 300,
		'zoomSpeedOut'			: 200,
		'callbackOnStart'		: resetHeight
	});
	$("a.vlink,a#infolink").fancybox({
		'zoomOpacity'			: true,
		'zoomSpeedIn'			: 300,
		'zoomSpeedOut'			: 200,
		'loadOnClose'			: resetHeight
	});
	$("a.vlink").click(function(){
		id=$(this).attr("vidid");
		$("div#video").html('<object width="560" height="340"><param name="movie" value="http://www.youtube.com/v/'+id+'&hl=en_US&fs=1&"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/'+id+'&hl=en_US&fs=1&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="560" height="340"></embed></object>');
	});
	if(typeof ed!="undefined")
	$('#countdown').countdown({
		layout: '{d<}{dn} <span>D</span> {d>} {hnn} <span>Hr</span> {mnn} <span>Min</span> {snn} <span>Sec</span>',
		until: ed});
	$(".com").hover(function(){$(this).css("background","#eee");},function(){$(this).css("background","transparent");});
//	$("a#cartlink").fancybox({
//		'onClosed'				: trig_cart_update
//	});
	$("#cartlink").click(function(){
		window.location=site_url+"shoppingcart";
	});
	$("#fanbhlink").fancybox({
		'callbackOnClose'		: resetHeight
	});
	$(".fancylink").fancybox({
		'padding'	:	"0px"
	});
	$("#subscr_form").submit(function(){
		subscr=$("#sub_emailmobile").val();
		if(!is_email(subscr) && !is_mobile(subscr))
			alert("Please enter a valid email or mobile number");
		else
			{
				post=$(this).serialize();
				$.post(site_url+"/jx_subscribe",post,function(){
					$("#sub_emailmobile").val("Thank you for subscribing!").addClass("highlistinp");
				});
			}
		return false;
	});
	$(".alertmetrig").click(function(){
		$(this).hide();
		$(".alertme,.requestme",$(this).parent()).show();
	});
	$(".alertme").submit(function(){
		mobile=$(".alrt_m_input",$(this)).val();
		email=$(".alrt_e_input",$(this)).val();
		if(!is_email(email))
			alert("Please enter a valid email");
		else if(!is_mobile(mobile))
			alert("Please enter a valid mobile");
		else{
			frm=$(this);
			post=$(this).serialize();
			$.post(site_url+"/jx_alert",post,function(){
				$(".alrt_m_input,.alrt_e_input",frm).val("");
				alert("Alert added");
			});
			$(".alertme").hide();
		}
		return false;
	});
	
	$("#fd_form textarea").focus(function(){
		if($(this).val()=="I wish you had...")
			$(this).val("");
	}).blur(function(){
		if($(this).val()=="")
			$(this).val("I wish you had...");
	});

	$("#fd_form .fd_email").focus(function(){
		if($(this).val()=="My email is")
			$(this).val("");
	}).blur(function(){
		if($(this).val()=="")
			$(this).val("My email is");
	});
	
	$("#fd_form").submit(function(){
		if(!is_email($(".fd_email").val()))
			alert("Please enter a Invalid email");
		else
		{
			pst=$(this).serialize();
			$.post(site_url+"jx_fdback",pst,function(){
				alert("Thank you for your feedback");
			});
			$("#fd_form textarea,#fd_form .fd_email").val("").focus();
		}
		return false;
	});
	
	$(".requestme").submit(function(){
		mobile=$(".alrt_m_input",$(this)).val();
		email=$(".alrt_e_input",$(this)).val();
		if(!is_email(email))
			alert("Please enter a valid email");
		else if(!is_mobile(mobile))
			alert("Please enter a valid mobile");
		else{
			$.fancybox.close();
			frm=$(this);
			post=$(this).serialize();
			$.post(site_url+"/jx_request",post,function(){
				$(".alrt_m_input,.alrt_e_input",frm).val("");
				alert("Request added");
			});
		}
		return false;
	});
	$("#sub_emailmobile,.alrt_input").focus(function(){
		if($(this).val()=="Enter mobile or email")
			$(this).val("");
		else	if($(this).hasClass("highlistinp"))
			$(this).val("").removeClass("highlistinp");
	}).blur(function(){
		if($(this).val()=="")
			$(this).val("Enter mobile or email");
	}).val("Enter mobile or email");
	
	$(".alrt_m_input").focus(function(){
		if($(this).val()=="Enter mobile")
			$(this).val("");
	}).blur(function(){
		if($(this).val()=="")
			$(this).val("Enter mobile");
	}).val("Enter mobile");
	
	$(".alrt_e_input").focus(function(){
		if($(this).val()=="Enter email")
			$(this).val("");
	}).blur(function(){
		if($(this).val()=="")
			$(this).val("Enter email");
	}).val("Enter email");

	$(".addtocartbut").click(function(){addtocart();});
});
$(document).ready(function(){ $(document).pngFix(); });

function addtocart()
{
	$(".infocnt").css("font-size","30px");
	$(".infoh").hide();
//	pd="bpid="+bpid+"&item="+itemid+"&qty="+$("#qty").val();
	pd={"bpid":bpid,"item":itemid,"qty":$("#qty").val(),"size":$("#sizing").val(),"rbuys":rbuys.join(",")};
	$.fancybox.showActivity();
	$.post(site_url+"jx/addtocart",pd,function(resp){
		$(this).attr("disabled",false);
		if(resp==0)
			$("#info").html("This sale is not valid anymore!");
		else if(resp==1)
			$("#info").html("Please reduce your quantity. Items are not available for given quantity");
		else if(resp==2)
			$("#info").html("This item is already present in your cart. If you want to edit it, please remove it first.");
		else if(resp==3)
			$("#info").html("This item added to cart!");
		else
			$("#info").html("Unknown error occured. Please try again. Sorry for this inconvenience."); 	
		if(resp==3 || resp==2)
		{
			$("#fancy_inner").css("height","100%");
//			if(instantcheckout==1)
//				location=site_url+"checkout";
//			else
				$("a#cartlink").click();
		}
		else
		{
			$(".infocnt").css("font-size","15px");
			$("#fancy_inner").css("height","30%");
			$("#fancy_div").html($("#infoc").html());			
			$(".infoh").show();
		}
	});
}
function getreviews(id,calbk)
{
	$.post(site_url+"jx/reviews","id="+id,calbk);
}
function showitemphoto(id)
{
	$(".itemphotos").hide();
	$("#itemphoto"+id).fadeIn("slow");
}

function checkoutsess(cond,type,extra,cb)
{

	var qs = new Array();
	
		if(type == 'get')
		{
			qs.push('cond='+cond);
		}
		else
		{
			qs.push('cond='+cond);
			qs.push(extra);
		}
	
	$.post(site_url+'/jx_checkoutcond',qs.join('&'),function(resp){
		if(typeof(cb)=='function')
			return cb(resp);
	},'json');
}


function set_gcrecpdet()
{
	var post_params = new Array();
	post_params.push("name="+$('input[name="recp_name"]').val());
	post_params.push("email="+$('input[name="recp_email"]').val());
	post_params.push("mobile="+$('input[name="recp_mobile"]').val());
	post_params.push("msg="+$('textarea[name="recp_msg"]').val());
	
	checkoutsess('gc_recp_det','set',post_params.join('&'),'');
}


function validate_recipient_det(){
	if($('#recipient_dets').length){
		var error_msg = '';
			if(!$('input[name="recp_name"]').val()){
				error_msg += ' -> Please enter Recipient name \n';
			}else if(!is_nohtml($('input[name="recp_name"]').val())){
				error_msg += ' -> special chars are not allowed in the name \n';
			}else{
				$('input[name="gc_recp_name"]').val($('input[name="recp_name"]').val());
				
			}
			
			if(!$('input[name="recp_email"]').val()){
				error_msg += ' -> Please enter Recipient email \n';
			}else if(!is_email($('input[name="recp_email"]').val())){
				error_msg += ' -> Please enter valid Recipient email \n';
			}else if(!is_nohtml($('input[name="recp_email"]').val())){
				error_msg += ' -> special chars are not allowed in the email \n';
			}else{
				$('input[name="gc_recp_email"]').val($('input[name="recp_email"]').val());
				
			}
			
			if(!$('input[name="recp_mobile"]').val()){
				error_msg += ' -> Please enter Recipient Mobileno \n';
			}else if(!is_mobile($('input[name="recp_mobile"]').val())){
				error_msg += ' -> Please enter valid Recipient Mobileno \n';
			}else if(!is_nohtml($('input[name="recp_mobile"]').val())){
				error_msg += ' -> special chars are not allowed in the Mobileno \n';
			}else{
				$('input[name="gc_recp_mobile"]').val($('input[name="recp_mobile"]').val());
				
			}
			
			if(!$('textarea[name="recp_msg"]').val()){
				error_msg += ' -> Please enter Recipient message \n';
			}else if(!is_nohtml($('input[name="recp_msg"]').val())){
				error_msg += ' -> special chars are not allowed in the message \n';
			}else{
				$('input[name="gc_recp_msg"]').val($('textarea[name="recp_msg"]').val());
				
			}
			
			if(error_msg){
				//alert('Recipient Details for GiftCard \n'+error_msg);
				if(!onload)
					alert('Please enter required fields of Recipient Details ');
				onload = 0;
				return false; 
			}
			
			set_gcrecpdet();
			return true; 
	}
	return true; 
}


$(function(){
	$(".search_right .link").click(function(){
		if(!$(".search_right").hasClass("sub_expanded"))
		{
			$(".search_right .cont").show();
			$(".search_right").animate({
				top:70,
				width:450,
				height:370
			},300,function(){
				$(".search_right .cont").show();
			});
			$(".search_right").addClass("sub_expanded");
		}
		else
		{
			$(".search_right").removeClass("sub_expanded");
//			$(".search_right .cont").hide();
			$(".search_right").animate({
				top:180,
				width:20,
				height:100
			},250);
		}
	});
	
	$("#shipbillcheck").change(function(){
			$("#billingaddr").toggle();
	}).attr("checked",true);
	$("#c2form").submit(function(){
		
		if(!validate_recipient_det()){
			return false;
		}
		
		if(!$("#check18yrs").attr("checked"))
			{
				alert("You have to agree to Terms & Conditions and you should be atleast 18 yrs old to place an order");
				return false;
			}
		ef=true;
		$(".mand",$(this)).filter(":not(:hidden)").each(function(i){
			v=($(this).val());
			if(!is_required(v))
			{
				ef=false;
				alert("All fields are mandatory");
				return false;
			}
		});
		if(ef)
			{
				if($("input[name=cpassword]:not(:hidden)",$(this)).length==1)
					{
						if(!is_required($("input[name=cpassword]",$(this)).val()))
							{
								alert("Please enter password from your user account");
								return false;
							}
						if($("input[name=cpassword]",$(this)).val()!=$("input[name=password]",$(this)).val())
							{
								alert("Passwords are not same. Please enter password for your user account and confirm again by entering same");
								return false;
							}
					}
			}
		if(ef)
		{
			if($("input[name=email]:not(:hidden)").length)
			{
				if(!is_email($("input[name=email]").val()))
				{
					ef=false;
					alert("Please enter a valid email");
				}
			}
		}
		if(ef)
		{
			if($("input[name=pincode]:not(:hidden)").length)
			{
				if(!is_numeric($("input[name=pincode]:not(:hidden)").val()))
				{
					ef=false;
					alert("Please enter a valid pincode");
				}
			}
		}
		if(ef)
		{
			if($("input[name=mobile]:not(:hidden)").length)
			{
				if(!is_mobile($("input[name=mobile]:not(:hidden)").val()))
				{
					ef=false;
					alert("Please enter a valid Mobile Number");
				}
			}
		}
		return ef;
	});
});
$(function(){
	$(".refine_pl_brand, .refine_pl_cat").click(function(e){
		location=$("input",$(this)).val();
		e.preventDefault();
		return false;
	});
	$(".menu .homelink").hover(function(){
		$("img",$(this)).toggle();
//		$("img",$(this)).attr("src",images_path+"home2.png");
	},function(){
		$("img",$(this)).toggle();
//		$("img",$(this)).attr("src",images_path+"home.png");
	});
	$(".paymetho").change(function(){
		if($("#cform .codmetho:checked").length==1)
			$(".codchge").toggle();
		else
		{
			$(".codchge").hide();
			$(".noncodchge").show();
		}
	});
	$("#cform .dfmetho").attr("checked",true);
	$("#whatruform").submit(function(){
		ef=true;
		$("#whatruform input:not([type=image])").each(function(){
			if(!is_required($(this).val()))
			{
				ef=false;
				alert("All fields mandatory. Please enter details as in the form.");
				return false;
			}
		});
		if(ef==false)
			return false;
		pst=$(this).serialize();
		$.fancybox.showActivity();
		$.post(site_url+"whatru",pst,function(){
			alert("Thank you for requesting a product. We will try our best to put your deal!");
		});
		$.fancybox.close();
		return false;
	});
	
	$(".attention .close").click(function(){
		$(".attention").hide();
		$.get(site_url+"jx/noannounce");
	});
	
	
	if(announce)
	$.get(site_url+"jx/getattention",function(data){
		if(data.length==0)
			return;
		$("#attentiontext").html(' <span ><b>Offer Of the Day</b> :: </span> '+data+' <span style="font-size:13px;float:right">Free Shipping | Dispatches in 24-48 hrs.</span>');
		$(".attention").slideDown();
	});
	
	$("#remindmefrm").submit(function(){
		if(!is_email($("input[name=email]").val()))
		{
			alert("Please enter your email");
			return false;
		}
		$.fancybox.showActivity();
		$.post(site_url+"jx/remindme",$(this).serialize(),function(){
			$.fancybox.hideActivity();
			alert("Thanks! We will notify you when this product is available");
		});
		return false;
	});
	
	$(".sm_prodt").bind("scrman",function(){
		img=$(".scrm_data",$(this)).text();
		$(".scrm_data",$(this)).remove();
		$(this).append('<img src="'+img+'" style="display:none;" width="200" class="scrm_live">');
		$(".scrm_live",$(this)).load(function(){
			$(".scrm_load",$(this).parent()).remove();
			$(this).fadeIn();
		});
	});
	
	$(".sm_f_prodt").bind("scrman",function(){
		img=$(".scrm_data",$(this)).text();
		$(".scrm_data",$(this)).remove();
		$(this).append('<img src="'+img+'" style="display:none;" width="300" class="scrm_live">');
		$(".scrm_live",$(this)).load(function(){
			$(".scrm_load",$(this).parent()).remove();
			$(this).fadeIn();
		});
	});
		$("#loadmoretrig").bind("scrman",function(){
			$(this).addClass("trigged");
			$(".tagsloading").show();
			$tp++;
			$.post(site_url+"discovery/jx_recent","p="+$tp,function(data){
				$(".tagsloading").hide();
				obj=$.parseJSON(data);
				if(obj.length==0)
					{
						$(".notagstoload").show();
						return;
					}
				$("#loadmoretrig").removeClass("trigged");
				i=0;
				$.each(obj,function(){
					i++;
					if(i>5)
						i=1;
					ht='<div class="d_s_tag"><a class="img" href="'+site_url+'discovery/tag/'+this.url+'"><img src="'+images_path+'tags/small/'+this.pic+'.jpg"></a><h3>'+this.name+'</h3><div class="bottom">Tagged onto <a href="'+site_url+'discovery/board/'+this.boardurl+'">'+this.board+'</a> by <a href="'+site_url+'discovery/user/'+this.username+'">'+this.user+'</a> on '+this.created_on+'</div></div>';
					$(".disc_tags_cont .col"+i).append(ht);
				});
			});
		});
	
	$(window).scroll(function(){
		scrollman();
	}).scroll();
});

function scrollman()
{
    var dvp = $(window).scrollTop();
    var dvb = dvp + $(window).height();
    $(".scrollman").each(function(i){
    	if($(this).filter(":visible").length==0)
    		return;
    var et = $(this).offset().top;
    var eb = et + $(this).height();
    if((eb >= dvp) && (et <= dvb) && (eb <= dvb) &&  (et >= dvp))
    	if(!$(this).hasClass("trigged"))
    		$(this).trigger("scrman").addClass("trigged");
    });
}




function snapit(product,callbk)
{
	$.get(site_url+"snapit/"+product,callbk);
}

function applycoupon(code)
{
	$.fancybox.showActivity();
	$.post(site_url+"jxcoupon","coupon="+code,function(){
		$("#cartlink").click();
	});
}

function clearcoupon()
{
	$.fancybox.showActivity();
	$.get(site_url+"jx/clearcoupon",function(){
		$("#cartlink").click();
	});
}

function trig_blink(jdom)
{
	jdom.focus();
	jdom.css("background","yellow");
	jdom.blur(function(){
		$(this).css("background","none").css("border","auto");
	});
}

function postparams(url,params)
{
	$("#disc_form").attr("action",url);
	inps="";
	$.each(params,function(key,val){
		inps+='<input type="hidden" name="'+key+'" value="'+val+'">';
	});
	$("#disc_form").html(inps);
	$("#disc_form").submit();
}
cart_updated=false;
trig_cart_update = function(){
};

function make_rem_redir(urlin)
{
	postparams(site_url+"/discovery/set_redirect",{url:urlin});
}

function refine()
{
	$.fancybox.showActivity();
	$("#noresults").hide();
	$(".dealdlist").hide();
	sels=[];
	$(".refine_brand input:checked").each(function(i){
		$.each(brands[$(this).val()],function(n,id){
			sels.push(id);
		});
	});
	pricsel=[];
	$(".refine_price input:checked").each(function(){
		$.merge(pricsel,prices[$(this).val()]);
	});
	catsel=[];
	$(".refine_cat input:checked").each(function(){
		$.merge(catsel,cats[$(this).val()]);
	});
	
	tsels=[];
	$.each(sels,function(n,i){
		if($.inArray(i,catsel)!=-1)
			tsels.push(i);
	});
	finalr=[];
	$.each(tsels,function(n,i){
		if($.inArray(i,pricsel)!=-1)
		{
			finalr.push(i);
			$("#deal"+i).show("fast");
		}
	});
	if(finalr.length==0)
		$("#noresults").show();
	$("#refine_numb").html($(".dealdlist:not(:hidden)").length);
	$.fancybox.hideActivity();
	scrollman();
}

function suggestive_search()
{
	q=$("#searchbox .srchinp").val();
	if(q.length<2)
		return;
	$("#sug_s_loading").show();
	$.post(site_url+"jx/sugst_search",{q:q,menu:$("#srchmenu").val()},function(data){
		$("#sug_s_loading").hide();
		o=$("#searchbox .srchinp").offset();
		$("#suggest_srch").css("top",parseInt(o.top+$("#searchbox .srchinp").height()+9)+"px");
		$("#suggest_srch").css("left",o.left+"px");
		$("#suggest_srch").html(data).show();
		$(".sug_s_links").click(function(){
			$("#sug_s_loading").hide();
			$.fancybox.showActivity();
		});
	});
}

var sug_srch_timer=0;

function selectsrchmenu(menu)
{
	$("#srchmenu").val(menu);
}

$(function(){
	$(".refine_but").hover(function(){
		$(this).addClass("refine_but_hover");
	},function(){
		$(this).removeClass("refine_but_hover");
	});
	$(".foretop_profile img").click(function(e){
		$(".foretop .profile_popup").show();
		e.stopPropagation();
		return false;
	});
	$(".dealdlist").hover(function(){
		$(this).addClass("product_hover");
	},function(){
		if(!$(this).hasClass("cartadded"))
			$(this).removeClass("product_hover");
	});
	$(".dealdlist .addcart_quick_cont .addtocart_quick").attr("disabled",true).attr("checked",false);
	$(".dealdlist .addcart_quick_cont").click(function(){
		if($(this).hasClass("addcart_quick_added"))
			return false;
		var o=$("#"+$(this).parent().attr("id"));
		$.getJSON(site_url+"api/buy/"+$(".addtocart_quick",$(this)).val(),function(resp){
			$("#nocartitems").html(resp.num);
			$(".totalcont .total").html(resp.total);
			$(".addcart_quick_cont",o).addClass("addcart_quick_added");
			$(".addcart_quick_cont label",o).html("Added to cart");
		});
		$(".addcart_quick_cont label span",o).html('<img src="'+images_path+'loader_gold.gif">');
		o.addClass("cartadded").addClass("product_hover");
	});
	$("body").click(function(){
			$(".foretop .profile_popup").hide();
			$("#suggest_srch").hide();
			$(".search .menu_list").hide();
			if($(".header_searchbar .smenu").hasClass("opened"))
				$(".header_searchbar .smenu").click();
	});

	$("#searchbox").submit(function(){
		if(!is_required($("input.srchinp",$(this)).val()) || $("input.srchinp").val().length<3)
			return false;
		return true;
	});
	$("#searchbox .srchinp").focus(function(){
		if($(this).val()=="Search brands, categories, products...")
			$(this).val("").css("color","#000");
	}).blur(function(){
		if($(this).val()=="")
			$(this).val("Search brands, categories, products...").css("color","#777");
	}).val("Search brands, categories, products...");
	$(".header_searchbar .smenu").click(function(e){
		if(!$(this).hasClass("opened"))
		{		
			o=$(".header_searchbar .smenu").offset();
			$(".header_searchbar .menu_list").css("top",parseInt(o.top+$(".header_searchbar .smenu").height()+16)+"px");
			$(".header_searchbar .menu_list").css("left",o.left+"px");
			$(".header_searchbar .menu_list").show();
			$(this).addClass("opened");
			e.stopPropagation();
		}else
		{
			$(".header_searchbar .menu_list").hide();
			$(this).removeClass("opened");
		}
	});
	$(".header_searchbar .menu_list a").click(function(){
			$(".header_searchbar .smenu").html($(this).html());
			$(".header_searchbar .menu_list").hide();
			$(".header_searchbar .smenu").removeClass("opened");
			$(".search .srchinp").focus();
	});
	$("#searchbox .srchinp").keypress(function(){
		window.clearTimeout(sug_srch_timer);
		if(sugst_srch_enabled)
			sug_srch_timer=window.setTimeout(suggestive_search,300);
	}).blur(function(){
		window.setTimeout(function(){
			$(".search .sugstsrch_onoff_cont").slideUp();
			$("#suggest_srch").hide();
		},1000);
	}).focus(function(){
		$(".search .sugstsrch_onoff_cont").slideDown();
	});
	$("#sugstsrch_onoff").change(function(){
		v="off";
		if($(this).attr("checked"))
		{
			v="on";
			sugst_srch_enabled=true;
		}
		else
			sugst_srch_enabled=false;
		$.cookie("sugst_search",v,{expires:3650,path:"/"});
	});

	$("#ts_subscribe_input").focus(function(){
		if($(this).val()=="Enter email to get a coupon")
			$(this).val("").css("color","#000");
	}).blur(function(){
		if($(this).val()=="")
			$(this).val("Enter email to get a coupon").css("color","#777");
	}).val("Enter email to get a coupon").css("color","#555");
	
	$("#ts_subscribe").submit(function(){
		if(!is_email($("#ts_subscribe_input").val()))
			{
				alert("Please enter your valid email address");return false;
			}
		$.fancybox.showActivity();
		$.post(site_url+"jx/subscribeaction",{email:$("#ts_subscribe_input").val()},function(){
			$.fancybox.hideActivity();
			$("#ts_subscribe").html("<b>Thank you for subscribing!</b>");
		});
		return false;
	});
	
	if($("ol.faqs").length!=0)
		{
			$("ol.faqs .q").click(function(){
				if($(this).hasClass("opened"))
					return;
				$("ol.faqs .a").slideUp("fast");
				$("ol.faqs .q").removeClass("opened");
				$(this).addClass("opened");
				$(".a",$(this).parent()).slideDown("medium");
			});
		}
	
});

function chkout_savefss()
{
	sels=[];
	$(".shoppingcart .fs_sels:checked").each(function(){
		sels.push($(this).val());
	});
	s=sels.join(",");
	$.fancybox.showActivity();
	$.post(site_url+"jx_savefs",{fsids:s},function(){
		$.fancybox.hideActivity();
		$("#fsselected_disp").html(sels.length);
	});
}
function showsidepopper()
{
	$(document).unbind("scroll",showsidepopper);
	$("#sidepopper").animate({
		left:-10
	},500,function(){
		$("#sidepopper").animate({
			left:-40
		},200);
	});
}

$(function(){
	if($(".featuredtiles").length==0)
		return;
	$(".featuredtiles .tile").click(function(){
		location=$("a",$(this)).attr("href");
	});
	$(".featuredproducts .tile").hover(function(){
		$(this).addClass("tile_hover");
		$(".snapit_tile",$(this)).show();
	},function(){
		$(this).removeClass("tile_hover");
		$(".snapit_tile",$(this)).hide();
	});
	$(".featuredproducts .tile .snapit_tile").click(function(e){
		$(".snapit_tile").hide();
		itemid=$(".itemid",$(this)).val();
		itempic=$(".itempic",$(this)).val();
		itemname=$(".itemname",$(this)).val();
		url=$(".url",$(this)).val();
		tagitem(url);
		e.stopPropagation();
		return false;
	});
	

});

$(function(){
	if($(".sidepoppercont").length==0)
		return;
	$(document).scroll(showsidepopper);
	$("#sidepopper .mlinks").click(function(e){
		e.stopPropagation();
		event.cancelBubble = true;
	});
	$("#sidepopper .close").click(function(){
		$("#sidepopper").hide();
		return false;
	});
	
	$("#sidepopper").click(function(){
		location=$(".sidepoppercont a",$(this)).attr("href");
		return false;
	});
});

var sugst_srch_enabled=false;

$(function(){
	if($.cookie("sugst_search")=="on")
		sugst_srch_enabled=true;
	if($("#header_menu_cont").length!=0)
	{
		$("#menu_loader").html($("#header_menu_cont").html());
	}

});