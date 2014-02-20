//extend
$(function(){
	$.fn.toggleCheck=function(){
		if($(this).attr("checked"))
			$(this).attr("checked",false);
		else
			$(this).attr("checked",true);
	};
});


function makeacall(cust)
{
	$("#phone_booth").show();
	$(".pb_customer").val(cust);
	if($(".pb_agent").val().length!=0)
		$("#phone_booth form").submit();
}

$(function(){
	$.datepicker.setDefaults({ dateFormat: 'yy-mm-dd' });
//	$("body").append('<table id="temp_dg"></table>');
//	$("#temp_dg").html($(".datagrid").html());
//	$(".datagrid tr").remove();
//	$(".datagrid").html("<thead></thead><tbody></tbody><tfoot></tfoot>");
//	$(".datagrid thead").html("<tr>"+$("#temp_dg tr:first").html()+"</tr>");
//	$("#temp_dg tr:first").remove();
//	$(".datagrid tbody").html($("#temp_dg").html());
	//$(".datagrid").append("<tfoot></tfoot>");
	//$(".datagrid:not(.nofooter) tfoot").html("<tr><td colspan='100'><a href='javascript:void(0)' class='dg_print'>print</a></td></tr>");
	if(typeof submenu == "undefined")
	{
		$("#content .leftcont").html("<ul>"+$(".menu").html()+"<ul>");
		$("#content .leftcont ul li ul").remove();
	}
	else
	{
		$("#content .leftcont").html("<ul>"+$(".menu #"+submenu+"").html()+"</ul>");
		$("#content .leftcont ul li span").remove();
	}
	$(".datagrid tbody tr").live("mouseover",function(){
		$("td",$(this)).addClass("highlight_row");
		if($("td a.link",$(this)).length!=0)
			$("td",$(this)).css("cursor","pointer");
		if($("td .qe_trig",$(this)).length!=0 || $("td .qe_all_trig",$(this).parent()).length!=0)
			$("td",$(this)).css("cursor","url('../images/edit_cursor.gif') 1 24, -moz-grab");
	}).live("mouseout",function(){
		$("td",$(this)).removeClass("highlight_row");
	});
	$(".datagrid .dg_print").click(function(){
		var html="";
		prw=window.open("",'','width=10,height=10');
		if($("thead",$(this).parents(".datagrid")).length!=0)
			html=$("thead",$(this).parents(".datagrid")).html();	
		html=html+$("tbody",$(this).parents(".datagrid")).html();
		prw.document.write('<table border=1 width="100%" style="font-size:12px;font-family:arial;">'+html+'</table>');
		prw.focus();
		prw.print();
	});
	$(".datagrid tr").live("click",function(e){
		if($("td a.link",$(this)).length!=0)
		{
			link=$("a.link",$(this));
			if(link.attr("target")=="_blank")
				window.open(link.attr("href"), "_blank");
			else
				location=link.attr("href");
		}
	});
	$(".inp").live({
		mouseenter:	function(){
		$(this).addClass("inp_hglt");
					},
		mouseleave: function(){
		$(this).removeClass("inp_hglt");
					},
		focus: function(){
		$(this).addClass("inp_focus");
					},
		blur: function(){
		$(this).removeClass("inp_focus");
		}
	});
	$(".datagrid td").dblclick(function(){
		$d=$(this).parents(".datagrid");
		if($(".qe_trig",$d).length!=0)
			$(".qe_trig",$(this).parent()).trigger("dg-qe");
		$(".datagrid .qe_all_trig").trigger("dg-qe-all");
		if($("td a.dbllink",$(this).parent()).length!=0)
			location=$("a.dbllink",$(this)).attr("href");
	});
	$(".datagrid .qe_all_trig").click(function(){
		$(this).trigger("dg-qe-all");
	});
	$(".datagrid .qe_trig").bind("dg-qe",function(){
		$d=$(this).parents("tr").get(0);
		$("span",$d).hide();
		$(".inp_qe",$d).show();
	});
	$(".datagrid .qe_submit").click(function(){
		$d=$(this).parents("tr").get(0);
		callback=window[$(this).attr("id")];
		input={};
		$(".inp_qe",$d).each(function(){
			input[$(this).attr("name")]=$(this).val();
		});
		callback(input,$d);
		$(this).parent().append('<img src="'+images_url+'loading_maroon.gif" class="qe_busy">');
		$(this).val("saving").attr("disabled",true);
	});
	$(".datagrid .qe_all_trig").bind("dg-qe-all",function(){
		$d=$(this).parents(".datagrid").get(0);
		$("span",$d).hide();
		$(".inp_qe",$d).show();
	});
	$(".datagrid .inp_qe").hide();
	$(".datagrid td a,.datagrid td select,.datagrid td input").click(function(e){
		e.stopPropagation();
		return true;
	});
	$(".tabs ul:first").addClass("tabsul");
	$(".tabs ul li a").each(function(){
		$($(this).attr("href")).hide().addClass("tabcont");
	});
	$($(".tabs ul li:first a").attr("href")).show();
	$(".tabs ul li:first").addClass("selected");
	$(".tabs ul li a").click(function(){
		$(".tabs ul li").removeClass("selected");
		$(this).parent().addClass("selected");
		$(".tabs ul li a").each(function(){
			$($(this).attr("href")).hide();
		});
		$($(this).attr("href")).show();
		if($(this).attr("href").charAt(0)=="#")
		{
			location.hash=$(this).attr("href").substr(1)+"_ltr"+Math.random();
			return false;
		}
	});
	$(".ajax_loadresult a").live("click",function(){
		 if(!$(this).hasClass('selected'))
			 $(this).addClass("selected");
		else
			 $(this).removeClass("selected");

	});
	$("ul.menu ul li").hover(function(){
		if($(".submenuright",$(this)).length==0)
			return;
		p=$(this);
		o=$(".submenuright",$(this));
		$(".submenuright",p).show();
		if(o.offset().left+o.width()>$(window).width())
			o.css("margin-left","-"+(o.width())+"px");
	},function(){$(".submenuright",$(this)).hide()});
	if(location.hash.length!=0)
	{
		hash=location.hash;
		s=hash.search("_ltr");
		if(s!=-1)
			hash=hash.substr(0,s);
		$(".tabs ul li a[href="+hash+"]").click();
	}
	$(document).click(function(){
		$(".closeonclick").hide();
	}).keydown(function(e){
		if(e.which==27)
			$(".closeonclick").hide();
	});
	$(".dash_bar, .dash_bar_red, .dash_bar_right").each(function(){
		if($("a",$(this)).length!=0)
			$(this).css("cursor","pointer");
	});
	$(".dash_bar, .dash_bar_red, .dash_bar_right").hover(function(){
		if($("a",$(this)).length>0)
			$(this).addClass("dash_bar_hglt");
	},function(){
		$(this).removeClass("dash_bar_hglt");
	});
	$(".dash_bar, .dash_bar_red, .dash_bar_right").click(function(){
		if($("a",$(this)).length)
			location=$("a",$(this)).attr("href");
	});
	$(".page_print_button").click(function(){
		var w=window.open("","","height=100,width=100");
		wd=w.document;
		wd.write("<html><head>"+'<link type="text/css" rel="stylesheet" href="http://localhost/daydeal/css/admin.css"><link type="text/css" rel="stylesheet" href="http://localhost/daydeal/css/erp.css">'+"<script>function printpage(){window.print();window.close();}</script></head><body onload='printpage()'>");
		wd.write($("#content .rightcont").html());
		wd.write("</body></html>");
		wd.close();
	});
	
	if($("#pop_info").text().length!=0)
	{
		show_popup($("#pop_info").html());
	}
	$(".danger_link").click(function(e){
		if(!confirm("Are you sure?"))
		{
			e.preventDefault();
			return false;
		}
		return true;
	});
	
});

function show_popup(msg){
	$("#pop_info").text(msg);
	tp=$(window).height()/3;
	$("#pop_info").show();
	lt=($(window).width()/2)-($("#pop_info").width()/2);
	$("#pop_info").hide().css("top",tp+"px").css("left",lt+"px").fadeIn("slow");
	window.setTimeout(function(){
		$("#pop_info").fadeOut(1500,function(){
			$("#pop_info").hide();
		});
	},5000);
}

function qe_callback_done(row)
{
	$d=row;
	$(".qe_submit",$d).val("saved");
	$(".qe_busy",$d).remove();
	window.setTimeout(function(){
		$(".qe_submit",$d).val("save").attr("disabled",false);
	},2000);
}

$(function(){
	
	if($("#account_grn_present").length!=0)
	{
			$(".idate").datepicker();
			$(".sdiscount, .stype, .invoice").change(function(){
				$p=$(this).parents("tr").get(0);
				
				mrp=parseInt($(".mrp",$p).html());
				dp_price=parseInt($(".dp_price",$p).html());
				
				stype=parseInt($(".stype",$p).val());
				sdiscount=parseInt($(".sdiscount",$p).val());
				margin=parseInt($(".margin",$p).val());
				
				if(dp_price*1)
				{
				    price=dp_price-(dp_price*margin/100);
                    if(stype==1)
                        price=price-(dp_price*sdiscount/100);
                    else
                        price=price-sdiscount;    
				}else
				{
				    price=mrp-(mrp*margin/100);
                    if(stype==1)
                        price=price-(mrp*sdiscount/100);
                    else
                        price=price-sdiscount;
				}
				
				$(".pprice",$p).html(price);
				calc_vvalue();
			});
		
			$("#cv_form").submit(function(){
				var flag=true;
				$(".sdiscount").each(function(){
					$p=$(this).parents("tr").get(0);
					price=parseInt($(".pprice",$p).html());
					if(isNaN(price))
						flag=false;
				});
				if(!flag)
					alert("Check GRN table and update Dicounts correctly");
				return flag;
			});
	}
	

	if($("#createclientinvoice").length!=0)
	{
				
				$("#ci_form").submit(function(){
					flag=false;
					if($("#invoice_date").val().length==0)
					{
						alert("Please enter invoice date");
						return false;
					}
					f=true;
					$(".offer",$(this)).each(function(){
						if($(this).val().length==0 || $(this).val()==0)
						{
							alert("Please check offer price");
							f=false;
							return false;
						}
					});
					if(f==false)
						return false;
					$(".iqty",$(this)).each(function(){
						if($(this).val()!="0")
							flag=true;
					});
					if(!flag)
					{
						alert("No products to invoice");
						return false;
					}
					return true;
				});
				
				$(".iqty").live("keyup",function(){
					iqty=parseInt($(this).val());
					if((isNaN(iqty) || iqty<0) && $(this).val().length!=0)
					{
						alert("Enter valid integer");
						return;
					}
					stock=parseInt($(".stock",$(this).parents("tr").get(0)).html());
					if(iqty>stock)
					{
						alert("Stock not available for selected qty. Maximum allowed selected.");
						$(this).val(stock);
					}
				});
				$("#client_select").change(function(){
				});
	}
});	

function show_frm_processing(msg){
	if(!$('#processing_req').length){
		$('body').append('<div id="processing_req"></div>');	
	}
	$('#processing_req').html(msg);
}
function hide_frm_processing(){
	$('#processing_req').hide();
}


function formatDate(d){
	
	var monthNames = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun","Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
	
	var m = d.getMonth();
	var d1 = ((d.getDate() <= 9)?'0'+d.getDate():d.getDate());
	var y = d.getFullYear();
	
	 
	
	return d1+'-'+monthNames[m]+'-'+y;
}

function formatDateTime(d){
	
	var t = '';
	var h = d.getHours();
	var m = d.getMinutes();
	var apm = 'am';
		if(h > 12){
			h = h-12;
			apm = 'pm';
		}
		
		if(h < 10){
			h  = '0'+h;
		}
		
		if(m < 10){
			m  = '0'+m;
		}
		var monthNames = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun","Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
		
		var m1 = d.getMonth();
		var d1 = ((d.getDate() <= 9)?'0'+d.getDate():d.getDate());
		var y = d.getFullYear();
		
	
	return d1+'-'+monthNames[m1]+'-'+y+' '+h+':'+m+' '+apm;
	
}


function prepare_daterange(a,b){
	$( "#"+a ).datepicker({
	      changeMonth: true,
	      dateFormat:'yy-mm-dd',
	      numberOfMonths: 1,
	      onClose: function( selectedDate ) {
	        $( "#"+b ).datepicker( "option", "minDate", selectedDate );
	      }
	    });
	    $( "#"+b ).datepicker({
	      changeMonth: true,
	      dateFormat:'yy-mm-dd',
	      numberOfMonths: 1,
	      onClose: function( selectedDate ) {
	        $( "#"+a ).datepicker( "option", "maxDate", selectedDate );
	      }
	    });
}

/**
 * Function to convert unix time to date and time
 */
function get_unixtimetodatetime(utime)
{
	var date = new Date(utime * 1000);
	var y=date.getFullYear();
    var m=date.getMonth()+1;
    var d=date.getDate();
    var h=(date.getHours() > 9)?date.getHours()-12:date.getHours();
    var mi=date.getMinutes();
    var s=date.getSeconds();
    var datetime=d+'/'+m+'/'+y+' '+h+':'+mi+':'+s;
    return datetime;
}

/** This prototype function allows you to reset all form input types
 * $("#formname").clearForm();
 */
$.fn.clearForm = function() {
  return this.each(function() {
    var type = this.type, tag = this.tagName.toLowerCase();
    if (tag == 'form')
      return $(':input',this).clearForm();
    if (type == 'text' || type == 'password' || tag == 'textarea')
      this.value = '';
    else if (type == 'checkbox' || type == 'radio')
      this.checked = false;
    else if (tag == 'select')
      this.selectedIndex = 0;//this.selectedIndex = -1;
  });
};

/** This prototype function allows you to remove even array from array 
   var arr = [1,2,[1,1], 'abc']; 
   arr.remove([1,1]);console.log(arr) //[1, 2, 'abc']
   arr.remove(1); console.log(arr) //[2, [1,1], 'abc']
   arr.remove('abc'); console.log(arr) //[1, 2, [1,1]]*/
Array.prototype.remove = function(x) { 
    for(i in this){
        if(this[i].toString() == x.toString()){
            this.splice(i,1)
        }
    }
}

/**
 * This function allows you to remove decimal point to 2 digits, also preserver integer numbers
 * 5.386823 => 5.39
 */
function format_number(num,decimal) { //decimal is optional
    var deci = (decimal === undefined || decimal === null ) ? 2 : decimal;
    num = parseFloat(num);
    
    var final_num = ( num.toString().indexOf(".") !== -1) ? num.toFixed(deci) : num;
    return parseFloat(final_num);
}

/*( function ($) {
    "use strict";
    $.fn.get_dealstock = function(el,options) {
        this.css( "background-color", "green" );
        return this;
    };
    $.ltrim = function( str ) {
        return str.replace( /^\s+/, "" );
    };
    $.rtrim = function( str ) {
        return str.replace( /\s+$/, "" );
    };
});
$(".deal_stock").get_dealstock();*/
