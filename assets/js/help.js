/** This file is part of the ebox plugin and was generated automatically */
!function(e,n,o){function a(){var e=n.getElementsByTagName("script")[0],o=n.createElement("script");o.type="text/javascript",o.async=!0,o.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(o,e)}if(e.Beacon=o=function(n,o,a){e.Beacon.readyQueue.push({method:n,options:o,data:a})},o.readyQueue=[],"complete"===n.readyState)return a();e.attachEvent?e.attachEvent("onload",a):e.addEventListener("load",a,!1)}(window,document,window.Beacon||function(){}),window.Beacon("init","1418fe60-cd03-4691-a765-66e6166f1695"),jQuery((function(e){e(document).on("submit","#search-form",(function(n){n.preventDefault();var o={};e.each(e(this).serializeArray(),(function(e,n){o[n.name]=n.value})),o.keyword.length>0&&(Beacon("open"),Beacon("search",o.keyword))})),e(document).on("click",".answers .item",(function(n){n.preventDefault();var o=e(this).data("id");Beacon("open"),Beacon("navigate","/docs/search?query=category:"+o)})),Beacon("on","ready",(function(){e("body").append('<div class="beacon-background"></div>')})),Beacon("on","article-viewed",(function(){e("body").addClass("beacon-open"),setTimeout((function(){var n=setInterval((function(){e(".beacon-open .Beacon #BeaconInlineArticlesFrame, .Beacon .BeaconContainer-enter-done").length<1?(e("body").removeClass("beacon-open"),clearInterval(n)):e("body").addClass("beacon-open")}),200)}),300)})),Beacon("on","open",(function(){e("body").addClass("beacon-open")})),Beacon("on","close",(function(){e("body").removeClass("beacon-open")}))}));