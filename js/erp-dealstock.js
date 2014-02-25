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
        var REFRESH = base.options.refresh;
        var INTERVAL = base.options.intervals;
        var EVENT = base.options.eventname;
        
        var ITEMID = base.$el.attr("dealid");
        var IS_PNH = 1; //base.$el.attr("is_pnh");
        //function debug(e) { console.log(e); }
       
       /* base.dealStatusFn = function(e) {
            $.getJSON(site_url+'/admin/jx_pnh_deal_stock_status/'+ITEMID+"/"+IS_PNH,{},function(resp){
                    var HTML_DATA = '';
                    if(resp.status == 'fail')
                    {
                            HTML_DATA += "Error: "+resp.message;
                            console.log(HTML_DATA);
                    }
                    else
                    {
                            HTML_DATA += resp.message;
                            base.$el.html(HTML_DATA);
                            base.itemrow.css({"background-color": "'"+resp.background+"'"});
                    }
            });
        };
        
         base.init = function() {
            //this.id  and state= start(1) = pause(2), stop(0) 
            //clearInterval(base.id);
                if(REFRESH === true)
                    base.id = setInterval(base.dealStatusFn,base.INTERVAL);
                else
                    base.dealStatusFn();
        };*/
        
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
        ,classname : 'get_dealstock_pop_block'
        ,bgcolor: 'transparent' //'#CFCFCF'
        ,get_fn_deal_stock: function() {}
        ,refresh: false
        ,intervals: 10000
        ,eventname:"click" // eg. hover,click
    };
    
    // Calling function
    $.fn.dealstock = function(options) {
        
        return this.each(function() {
            var st = new $.dealstock(this,options);
            var CLASSNAME = st.options.classname; 
            var EVENT =  st.options.eventname;
            
            if(EVENT == 'click')
            {
                print(EVENT);
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
                
                    print(EVENT);
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

            //$("body").append('<style>.'+CLASSNAME+' .stock_det_close { float:right; color:red; cursor:pointer; } .error_msg { background-color:#5F5968; color:#ffffff; padding:10px 10px;float:left;width:100%; } </style>');
            
        });
        
    };
    
    //<!--============================================<< DEAL STOCK PLUGIN SETTINGS END >>===================================-->
    
})(jQuery);

//<!--============================================<< INITIALIZING THE PLUGIN BY DEFAULT >>===================================-->

$( function() {
    $("a.deal_stock").dealstock();
});
/*
$('.positionable').position({
	"my": "right top"       //  Horizontal then vertical, missing values default to center
	"at": "left bottom"     //  Horizontal then vertical, missing values default to center
	"of": $('#parent'),     //  Element to position against 
	"offset": "20 30"       //  Pixel values for offset, Horizontal then vertical, negative values OK
	"collision": "fit flip" //  What to do in case of 
	"bgiframe": true        //  Uses the bgiframe plugin if it is loaded and this is true
});*/