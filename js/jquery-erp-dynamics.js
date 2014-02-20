/**
 * @description: Plugin to show tip box on mouse over the element
 */
( function ($) {
    "use strict";

    $.get_dealstock = function(el,options) {
        var base = this;
        base.$el = $(el);
        base.el=el;
        base.$el.data("get_dealstock",base);
        base.options = $.extend({}, $.get_dealstock.defaultOptions, options);
        
        var WIDTH = isNaN(base.options.width) ? base.options.width : base.options.width+"px";
        var HEIGHT = isNaN(base.options.height) ? base.options.height : base.options.height+"px";
        var CLASSNAME = base.options.classname;
        var BGCOLOR = base.options.bgcolor;
        var POSITION = base.options.position;
        
        var ITEMID = base.$el.attr("dealid");
        var IS_PNH = 1; //base.$el.attr("is_pnh");
        //function debug(e) { console.log(e); }
        base.init = function() {
            $.getJSON(site_url+'/admin/jx_pnh_deal_stock_status/'+ITEMID+"/"+IS_PNH,{},function(resp){
                    var HTML_DATA = '';
                    if(resp.status == 'fail')
                    {
                            HTML_DATA += "Error: "+resp.message;
                    }
                    else
                    {
                            HTML_DATA += resp.message;
                    }
                    base.$el.html(HTML_DATA);
            });
        };

        base.drawbox = function(e) {
            //debug(e);
            
            $.getJSON(site_url+'/admin/jx_pnh_deal_stock_det/'+ITEMID+"/"+IS_PNH,{},function(resp){
                    var HTML_DATA = '<span style="width:'+WIDTH+'; height:'+HEIGHT+'; background-color:'+BGCOLOR+'; position: '+POSITION+';" class="'+CLASSNAME+'">';
                    if(resp.status == 'fail')
                    {
                            HTML_DATA += "Error: "+resp.message;
                    }
                    else
                    {
                                HTML_DATA += '<span style="float:right;color:red;cursor:pointer" dealid="'+ITEMID+'" class="stock_det_close">X</span> <div style="float:left;width:100%">';
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
                    HTML_DATA += '</span>';
                    base.$el.append(HTML_DATA);
            });
            base.options.get_fn_deal_stock.call(this);
        };
        
        base.clearbox = function(e) {
            $("."+CLASSNAME,base.$el).remove();
        };
        base.init();
    };
    
    $.get_dealstock.defaultOptions = {
        width : 350
        ,height : 300
        ,position : "absolute"
        ,classname : 'get_dealstock_pop_block'
        ,bgcolor: 'transperant'//'#CFCFCF'
        ,get_fn_deal_stock: function() {}
    };
    
    $.fn.get_dealstock = function(options) {
        return this.each(function() {
            var st = new $.get_dealstock(this,options);
            
            $(this).hover(function(e) {
                st.drawbox(e);
            },function(e) {
                st.clearbox(e);
            });
           
           $(".stock_det_close",$(this)).click(function(e) {
                st.clearbox(e);
           });
        });
    };
})(jQuery);

/*$( function() {
    $(".deal_stock").get_dealstock({
        height:300,width:365
        //,get_fn_deal_stock:function() {print("hjghjgghj"); }
    });
});*/