/*
	 * jQuery InlineClick Plugin by cdbrkpnt
	 * Examples and usage: http://cdbrkpnt.com/jquery/inlineclick/
	 * Requires: jQuery v1.2.6 or later
	 * @version: 1.01  25-FEB-2011
	 */

	(function($) {

	$.fn.inlineclick = function(options) {
	    
		var opts = $.extend({}, $.fn.inlineclick.defaults, options);

		// Iterate on each element

		this.each(function() {
			var $this = $(this);
			var inptext = $.trim($this.attr('title'));	
				$this.val(inptext);

				 
				$this.focus(function(){
					if($.trim($(this).val()) == inptext){
						if(opts.onlySelect)
							$(this).select();
						else
							$(this).val('');
						
					}
				}).blur(function(){
					if($.trim($(this).val()) == ''){
						$(this).val(inptext);
					}
				});				
		});
	};

	$.fn.inlineclick.defaults = {
			onlySelect:false
	};
	
	})(jQuery);