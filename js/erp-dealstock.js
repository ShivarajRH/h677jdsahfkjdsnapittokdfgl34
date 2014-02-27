/**
 * @description: Plugin to show tip box on mouse over the element
 */
( function ($) {
    //<!--============================================<< DEAL STOCK PLUGIN SETTINGS START >>===================================-->
    "use strict";
    // class and function definition
    $.dealstock = function(el,options) {
        var base = this;
        base.$el = $(el);
        base.el=el;
        base.$el.data("dealstock",base);
        base.options = $.extend({}, $.dealstock.defaultOptions, options);
        base.itemrow = base.$el.closest("tr");
        base.active = 0;
        var p =  base.$el.offset();
        var TOP= base.itemrow.offset().top - p.top;
        var LEFT=p.left;
        
        var WIDTH = isNaN(base.options.width) ? base.options.width : base.options.width+"px";
        var HEIGHT = isNaN(base.options.height) ? base.options.height : base.options.height+"px";
        var CLASSNAME = base.options.classname;
        var BGCOLOR = base.options.bgcolor;
        var POSITION = base.options.position;
        var INTERVAL = base.options.interval;
        var EVENT = base.options.eventname;
        
        var ITEMID = base.$el.attr("dealid");
        var IS_PNH = 1; //base.$el.attr("is_pnh");
        //function debug(e) { console.log(e); }
        
        base.drawbox = function(e) {
            //debug(e);
                // plug in as opened
                $.getJSON(site_url+'/admin/jx_pnh_deal_stock_det/'+ITEMID+"/"+IS_PNH,{},function(resp){ // height:'+HEIGHT+'; top:'+TOP+'px;  left:'+LEFT+'px;
                        var HTML_DATA = '<div style="float:left;width:100%"><div style="width:'+WIDTH+'; background:'+BGCOLOR+'; position: '+POSITION+'; " class="'+CLASSNAME+'">\n\
                                            <span dealid="'+ITEMID+'" class="stock_det_close">X</span>';
                        if(resp.status == 'fail')
                        {
                                HTML_DATA += '<div class="error_msg">Error: '+resp.message+'</div>';
                        }
                        else
                        {
                                    HTML_DATA += '<table width="100%" border=1 class="datagrid" cellpadding=3 cellspacing=0>';
                                    HTML_DATA += '<thead><tr><th>Product Name</th><th>Stock</th></tr></thead><tbody>';
                                    $.each(resp.prod_stk_det,function(a,b){
                                            HTML_DATA +='<tr>';
                                            HTML_DATA +='	<td width="80%" style="font-size:10px"><a href="'+site_url+'/admin/product/'+b.product_id+'" target="_blank">'+b.product_name+'</a></td>';
                                            HTML_DATA +='	<td width="20%" style="font-size:10px">'+b.stk+'</td>';
                                            HTML_DATA +='</tr>';
                                    });
                                    HTML_DATA += '</tbody></table></div>';
                        }
                        HTML_DATA += '</div>';
                        base.$el.after(HTML_DATA);
                        
                        //position
                        $("."+CLASSNAME).position({
                            my:"left top"
                            ,at:"right bottom"
                            ,of: base.$el
                            ,offset: "-20 -30"
                        });

                        base.active = 1;
                        
                        $("span.stock_det_close",base.itemrow).click(function(e) {
                                base.$el.trigger(EVENT);
                       });
                });
                base.options.get_fn_deal_stock.call(this);
           
        };
        
        base.clearbox = function(e) {
            base.active = 0;
            $("."+CLASSNAME,base.itemrow).remove();
        };
        //on page load (as soon as its ready) call JT_init
        //$(document).ready(base.init()); //
        //base.init();

    };
    
    //Settings
     $.dealstock.defaultOptions = {
        width : 350
        ,height : 300
        ,position : "absolute" //"relative"
        ,popup : true //weather to show pop up on trigger the element
        ,classname : 'get_dealstock_pop_block' 
        ,bgcolor: 'transparent' //'#CFCFCF' 
        ,get_fn_deal_stock: function() {} //override the function
        ,drawbox:function() {}
        ,loadstatus: true  // deal element status replace eg: <a>In Stock</a>
        ,autorefresh: true //true,false - Refresh deal status given interval time else on refresh
        ,interval: 300000 // if(autorefresh == true) interval must. eg: 1min == 60000, 5 min => 300000 (5 * 60000)
        ,eventname:"click" // eg: hover,click
        ,change:'row' // text or row change the color (tr == row)
    };

    // Calling function
    $.fn.dealstock = function(options) {
        
        // initial actions
        var base_core = $(this);
       // var st = $.dealstock;
        base_core.options = $.extend({}, $.dealstock.defaultOptions, options);
        var CLASSNAME = base_core.options.classname;
        var stylesheet = '.'+CLASSNAME+' .stock_det_close { float: right;color: #FFFFFF;cursor: pointer;background-color: #7E88AD;padding: 0 13px;font-weight: bold;margin-top: -4%; } \n\
                            .'+CLASSNAME+' .error_msg { background-color: #CCC4C4; color:#ffffff; padding:10px 10px;float:left; } \n\
                            .'+CLASSNAME+' .datagrid th { background-color:#7E88AD !important; }\n\
                            .in-stock { color:green;margin:10px;font-size: 14px; }\n\
                            .out-of-stock { color:red;margin:10px;font-size: 14px; }';
        
        $("body").append('<style>'+stylesheet+'</style>');

        var myVar = 0;
        return this.each(function() {
            var st = new $.dealstock(this,options);
            var CLASSNAME = st.options.classname; 
            var EVENT =  st.options.eventname;
            var POPUP = st.options.popup;
            
            // ========================================< AUTO REFRESH THE DEAL STATUS START >==========================================================
            if(myVar === 0) {
                myVar = 1;
                var LOADSTATUS = st.options.loadstatus;
                var AUTOREFRESH = st.options.autorefresh;
                var INTERVAL = st.options.interval;
               
                if( LOADSTATUS === true && AUTOREFRESH === true ) {
                            fn_dealstatus(base_core);
                            var myIntervalId = setInterval( function() { fn_dealstatus(base_core); } ,INTERVAL);
                            //clearInterval(st.intervalid)
                }
                else if(LOADSTATUS === true && AUTOREFRESH === false) {
                    fn_dealstatus(base_core);
                }
                else if(LOADSTATUS === false && AUTOREFRESH === true) {
                    var myIntervalId = setInterval( function() { fn_dealstatus(base_core); } ,INTERVAL);
                }
                
            }
            // ========================================< AUTO REFRESH THE DEAL STATUS END >==========================================================
            
            // ========================================< DRAW OR CLOSE POPUP WITH DEAL ITEMS START >==========================================================
            if(POPUP === true) {
                    if(EVENT == 'click')
                    {
                            // on Click on in stock text Open / Close plugin box
                            $(this).toggle(function(e) {

                                    //Close all other
                                    $("."+CLASSNAME).remove();
                                    st.drawbox(e);

                            },function(e) {
                                    if(st.active === 1) 
                                    {
                                            //st.active = 0;
                                            st.clearbox(e);
                                    }
                            });
                    }
                    else if(EVENT == 'hover')
                    {

                            // on Click on in stock text Open / Close plugin box
                            $(this).bind("mouseenter",function(e) {

                                    //Close all other
                                    $("."+CLASSNAME).remove();
                                    st.drawbox(e);

                            });
                            $(this).parent().bind("mouseleave",function(e) {
                                    if(st.active === 1) {
                                            //st.active = 0;
                                            st.clearbox(e);
                                    }

                            });

                    }

                    // on Escape key press close plugin box
                    $(document).keyup(function(e) {
                        if( e.keyCode == 27 ) {
                            if(st.active === 1) {
                                //st.active = 0;
                                var base = st.$el;
                                base.trigger(st.options.eventname);
                            }
                        }
                   });
            }
           // ========================================< DRAW OR CLOSE POPUP WITH DEAL ITEMS END >==========================================================
            
        });
        
    };
    
    /** send all dealids
    *   and put status to element
    */
    function fn_dealstatus(elt) {
        var ELTCLASS = elt.attr("class");  ELTCLASS = ELTCLASS == undefined ? '' : ELTCLASS;
            
        var CHANGE = elt.options.change;
        
        var arr_dealids = [];
        $.each(elt,function(i,elt) {
                var dealid = $(elt).attr("dealid");
                arr_dealids.push(''+dealid+'');
        });

        // request api
        var postData = {itemids: "'" +( arr_dealids.join(',') +"'" ) };
        //print(postData);

        $.post(site_url+'/admin/jx_pnh_deal_stock_status',postData,function(resp){

                $.each(elt,function(ii,ee) {

                        var base = $(ee);
                        base.itemrow = base.closest("tr");
                        var dealid =  base.attr("dealid");

                        $.each(resp,function(itemid,itemdata) {
                            
                            if(dealid == itemid) {
                                    //print("dealid = "+ dealid +" "+" itemid = "+ itemid);
                                    var HTML_DATA = '';

                                    if(itemdata.status == 'fail')
                                    {
                                            print("Error: "+itemdata.message);
                                    }
                                    else
                                    {
                                            HTML_DATA = itemdata.deal_status;
                                            
                                            if(CHANGE == 'text')
                                            {
                                                if(itemdata.deal_status == 'In Stock')
                                                {
                                                    base.removeClass();
                                                    base.addClass(ELTCLASS+" in-stock");
                                                }
                                                else 
                                                {
                                                    base.removeClass();
                                                    base.addClass(ELTCLASS+" out-of-stock");
                                                
                                                }
                                                base.html(HTML_DATA.toUpperCase());
                                            }
                                            else
                                            {
                                                var background_in = 'none repeat scroll 0 0 rgba(170, 255, 170, 0.8) !important;';
                                                var background_out = 'none repeat scroll 0 0 #FFAAAA !important;';
                                                if(itemdata.deal_status == 'In Stock')
                                                {  
                                                    //base.itemrow.addClass("bg-in-stock");
                                                    base.itemrow.css({"background-color": "'"+background_in+"'"});
                                                }
                                                else {
                                                    //base.itemrow.addClass("bg-out-of-stock"); //css({"background-color": "'"+itemdata.background+"'"});
                                                    base.itemrow.css({"background-color": "'"+background_out+"'"});
                                                }
                                                base.html(HTML_DATA);
                                                
                                            }
                                            
                                    }
                                    
                            }

                        });

                });
        },'json');
    }

    //<!--============================================<< DEAL STOCK PLUGIN SETTINGS END >>===================================-->
    
})(jQuery);

//<!--============================================<< INITIALIZING THE PLUGIN BY DEFAULT >>===================================-->

/*$( function() {
    $("a.deal_stock").dealstock();
});*/
/*
$('.positionable').position({
	"my": "right top"       //  Horizontal then vertical, missing values default to center
	"at": "left bottom"     //  Horizontal then vertical, missing values default to center
	"of": $('#parent'),     //  Element to position against 
	"offset": "20 30"       //  Pixel values for offset, Horizontal then vertical, negative values OK
	"collision": "fit flip" //  What to do in case of 
	"bgiframe": true        //  Uses the bgiframe plugin if it is loaded and this is true
});*/