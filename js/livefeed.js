(function($) {
  $.timeago = function(timestamp) {
    if (timestamp instanceof Date) {
      return inWords(timestamp);
    } else if (typeof timestamp === "string") {
      return inWords($.timeago.parse(timestamp));
    } else {
      return inWords($.timeago.datetime(timestamp));
    }
  };
  var $t = $.timeago;

  $.extend($.timeago, {
    settings: {
      refreshMillis: 60000,
      allowFuture: false,
      strings: {
        prefixAgo: null,
        prefixFromNow: null,
        suffixAgo: "ago",
        suffixFromNow: "from now",
        seconds: "less than a minute",
        minute: "about a minute",
        minutes: "%d minutes",
        hour: "about an hour",
        hours: "about %d hours",
        day: "a day",
        days: "%d days",
        month: "about a month",
        months: "%d months",
        year: "about a year",
        years: "%d years",
        numbers: []
      }
    },
    inWords: function(distanceMillis) {
      var $l = this.settings.strings;
      var prefix = $l.prefixAgo;
      var suffix = $l.suffixAgo;
      if (this.settings.allowFuture) {
        if (distanceMillis < 0) {
          prefix = $l.prefixFromNow;
          suffix = $l.suffixFromNow;
        }
      }

      var seconds = Math.abs(distanceMillis) / 1000;
      var minutes = seconds / 60;
      var hours = minutes / 60;
      var days = hours / 24;
      var years = days / 365;

      function substitute(stringOrFunction, number) {
        var string = $.isFunction(stringOrFunction) ? stringOrFunction(number, distanceMillis) : stringOrFunction;
        var value = ($l.numbers && $l.numbers[number]) || number;
        return string.replace(/%d/i, value);
      }

      var words = seconds < 45 && substitute($l.seconds, Math.round(seconds)) ||
        seconds < 90 && substitute($l.minute, 1) ||
        minutes < 45 && substitute($l.minutes, Math.round(minutes)) ||
        minutes < 90 && substitute($l.hour, 1) ||
        hours < 24 && substitute($l.hours, Math.round(hours)) ||
        hours < 48 && substitute($l.day, 1) ||
        days < 30 && substitute($l.days, Math.floor(days)) ||
        days < 60 && substitute($l.month, 1) ||
        days < 365 && substitute($l.months, Math.floor(days / 30)) ||
        years < 2 && substitute($l.year, 1) ||
        substitute($l.years, Math.floor(years));

      return $.trim([prefix, words, suffix].join(" "));
    },
    parse: function(iso8601) {
      var s = $.trim(iso8601);
      s = s.replace(/\.\d\d\d+/,""); // remove milliseconds
      s = s.replace(/-/,"/").replace(/-/,"/");
      s = s.replace(/T/," ").replace(/Z/," UTC");
      s = s.replace(/([\+\-]\d\d)\:?(\d\d)/," $1$2"); // -04:00 -> -0400
      return new Date(s);
    },
    datetime: function(elem) {
      // jQuery's `is()` doesn't play well with HTML5 in IE
      var isTime = $(elem).get(0).tagName.toLowerCase() === "time"; // $(elem).is("time");
      var iso8601 = isTime ? $(elem).attr("datetime") : $(elem).attr("title");
      return $t.parse(iso8601);
    }
  });

  $.fn.timeago = function() {
    var self = this;
    self.each(refresh);

    var $s = $t.settings;
    if ($s.refreshMillis > 0) {
      setInterval(function() { self.each(refresh); }, $s.refreshMillis);
    }
    return self;
  };

  function refresh() {
    var data = prepareData(this);
    if (!isNaN(data.datetime)) {
      $(this).text(inWords(data.datetime));
    }
    return this;
  }

  function prepareData(element) {
    element = $(element);
    if (!element.data("timeago")) {
      element.data("timeago", { datetime: $t.datetime(element) });
      var text = $.trim(element.text());
      if (text.length > 0) {
        element.attr("title", text);
      }
    }
    return element.data("timeago");
  }

  function inWords(date) {
    return $t.inWords(distance(date));
  }

  function distance(date) {
    return (new Date().getTime() - date.getTime());
  }

  document.createElement("abbr");
  document.createElement("time");
}(jQuery));

var zend=0,ptitle="",feedc=0,wfocus=true;
$(function(){
	$(".itemcont").show();
	$(".time").timeago().removeClass("time");
	$(".snaplive .item").live("mouseover",function(){
		$(".scbutts .cont",$(this)).show();
	}).live("mouseout",function(){
		$(".scbutts .cont",$(this)).hide();
	}).fadeIn("slow");	
	ptitle=$("title").html();
	getfeed();
	initbuffer();
	$(window).focus(function(){
		feedc=0;
		window.setTimeout(function(){feedc=0;$("title").html(ptitle);},2000);
	});
	window.setTimeout("fixheight",15000);
	window.setTimeout("fixheight",50000);
	$(window).blur(function(){wfocus=false;});
	$(window).focus(function(){wfocus=true;});
	gapi.plusone.go();
});
function getfeed()
{
	if(wfocus)
		$(".itemcont .item").removeClass("highltitem");
	$.post(feed_url,"hash="+feed_hash,function(data){
		feed_hash=data.hash;
		if(data.feed.length==0)
			window.setTimeout("getfeed()",10000);
		for(i=0;i<data.feed.length;i++)
			$("#buffer").append(data.feed[i]);
		clearbuffer();
		feedc=feedc+data.feed.length;
		if(feedc!=0)
			$("title").html("("+feedc+") "+ptitle);
		$(".time").timeago().removeClass("time");
	},"json");
}
function initbuffer()
{
	ww=$(window).width();
	ol=$(".itemcont").offset().left;
	ot=$(".itemcont").offset().top;
	rt=ot;
	rl=ol;
	$(".itemcont .item").each(function(i){
		$(this).show();
		if(i==0)
			return;
		w=$(this).outerWidth(true);
		h=$(this).outerHeight(true);
		rl=rl+w;
		if(rl+w>=ww)
		{
			rt=rt+h;
			rl=ol;
			rh=rt+h;
			$(".itemcont").css("height",rh+"px");
		}
		$(this).css("left",rl+"px");
		$(this).css("top",rt+"px");
		$(this).css("z-index",zend);
		zend++;
	});
	fixheight();
}
function clearbuffer()
{
	ww=$(window).width();
	rl=ol=$(".itemcont").offset().left;
	rt=ot=$(".itemcont").offset().top;
	if($("#buffer .item").length==0)
	return;
	for(d=0;d<$("#buffer .item").length;d++)
	{
		$(".itemcont .item").each(function(i){
			w=$(this).outerWidth(true);
			h=$(this).outerHeight(true);
			rl=$(this).offset().left-5;
			rl=rl+w;
			if(rl+w>=ww)
			{
				rt=rt+h;
				rl=ol;
				rh=rt+h;
				$(".itemcont").css("height",rh+"px");
			}
//			if(rl==ol)
//				$(this).animate({left:rl,top:rt},1000);
//			else{
				$(this).css("left",rl+"px");
				$(this).css("top",rt+"px");
//			}
			if(i==0)
			{
				oll=rl;
				ott=rt;
			}
		});
		rl=oll;
		rt=ott;
	}
	rl=ol=$(".itemcont").offset().left;
	rt=ot;
	tops=[];
	lefts=[];
	$($("#buffer .item").get().reverse()).each(function(){
		tops.push(rt);
		lefts.push(rl);
		rl=rl+w;
		if(rl+w>=ww)
		{
			rl=ol;
			rt=rt+h;
		}
	});
	tops.reverse();lefts.reverse();
	$("#buffer .item").each(function(i){
		obj=$('<div class="item new">'+$(this).html()+"</div>").prependTo($(".itemcont"));
		obj.hide();
		obj.data("d-left",lefts[i]);
		obj.data("d-top",tops[i]);
		obj.css("z-index",zend);
		obj.addClass("highltitem");
		zend++;
	}).remove();
	showobj();
}
function showobj()
{
	obj=$(".itemcont .new").last();
	if(obj.length==0)
		return;
	obj.fadeIn("slow");
	rl=obj.data("d-left");
	rt=obj.data("d-top");
	obj.animate({left:rl,top:rt},200,"swing",function(){
		obj.removeClass("new");
		showobj();
		if($(".itemcont .new").length==0)
		{
			gapi.plusone.go();
			fixheight();
			window.setTimeout("getfeed()",10000);
		}
	});
}
function fixheight()
{
	max=Math.floor($(window).width()/$(".itemcont .item:first").outerWidth(true));
	$(".itemcont .item").each(function(i){
		if(i<max)
			return;
		ct=$(this).data("fh-top");
		if(typeof ct=="undefined")
			ct=$(this).offset().top;
		$(".itemcont .item").each(function(j){
			if(j==i-max)
			{
				uo=$(this);
				return false;
			}
		});
		ut=uo.data("fh-top");
		if(typeof ut=="undefined")
			ut=uo.offset().top;
		if(ut+uo.outerHeight(true)>ct || ut+uo.outerHeight(true)<ct)
			ct=ut+uo.outerHeight(true);
		$(this).data("fh-top",ct);
		$(this).animate({top:ct},500);
	});
	window.setTimeout("fixheight", 4000);
}

