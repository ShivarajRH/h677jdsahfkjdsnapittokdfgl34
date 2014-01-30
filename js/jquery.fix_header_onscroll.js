/*
 * jQuery Fixheader On Scroll Plugin by cdbrkpnt
 * Examples and usage: http://cdbrkpnt.com/jquery/fixheaderonscroll/
 * Requires: jQuery v1.2.6 or later
 * @version: 1.01  17-JAN-2014
 */

(function($) {
	$.fn.jq_fix_header_onscroll = function(options) {
	    
		var opts = $.extend({}, $.fn.jq_fix_header_onscroll.defaults, options);

		// Iterate on each element
		this.each(function() {
			var tObj = $(this);
				tObj.addClass('jq_fix_header_onscroll');
			$('thead th',tObj).each(function(){
				if($(this).text() == '')
					$(this).html('&nbsp;');
				$(this).css('width',$(this).width()+'px');
				$(this).css('left',$(this).offset().left+'px');
			});

			$('tbody td',tObj).each(function(){
				$(this).attr('width',$(this).width()+'px');
			});
			
		});
		
		$(window).bind('scroll',function() {
			$('.jq_fix_header_onscroll').each(function(){
					//console.log($(this).offset().top+' - '+$(window).scrollTop());
					if($(this).offset().top < $(window).scrollTop())
					{	
						if(!$('thead',this).hasClass('fix_head'))
							$('thead',this).addClass('fix_head');
					}else
					{
						if($('thead.fix_head',this).length)
							$('thead.fix_head',this).removeClass('fix_head');
					}
			});
		});
		
		$('body').append('<style>.jq_fix_header_onscroll thead.fix_head{position: fixed;top:0px;width:100%;}.jq_fix_header_onscroll thead.fix_head th{}</style>');
	};
	$.fn.jq_fix_header_onscroll.defaults = {};
})(jQuery);