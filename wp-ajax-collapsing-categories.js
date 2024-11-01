/*
+----------------------------------------------------------------+
|																							|
|	WordPress  Plugin: WP ajax collapsing categories										|
|	Copyright (c) 2011 ZHAO Xudong									|
|																							|
|	File Written By:																	|
|	- ZHAO Xudong														|
|	- http://html5beta.com														|
|																							|
|	File Information:																	|
|	- WP ajax collapsing categories Javascript File													|
|	- wp-content/plugins/wp-ajax-collapsing-categories/wp-ajax-collapsing-categories.js	 	|
|																							|
+----------------------------------------------------------------+
*/
jQuery( function($) {
	var zxdExpander = function() {
		var zxdExpand =$(this);
		var exp ='[—]';
		var exp2 = '[+]';
		if(zxdExpand.html() ==exp){
			zxdExpand.parent('.zxd_ajax_cc_li').children('ul').hide("normal"); 
			zxdExpand.html(exp2);
		}
		else{
			zxdExpand.html(exp);
			var zxdExpandChild =  zxdExpand.parent('.zxd_ajax_cc_li').children('.child_cat');
			if(zxdExpandChild.length>0) zxdExpandChild.show("normal");
			else{
				var zxdIdDiv = $('.zxd_ajax_cc').eq(0).attr("zid");
				var zxdID =zxdIdDiv?zxdIdDiv:0;
				var zxdCatLink = zxdExpand.parent('.zxd_ajax_cc_li').children('a').attr("href");
				$.ajax({
					type: 'POST',
					url:ajaxCC.ajaxurl,
					data: {
						action : 'zxd_cc_submit',
						catZxd: zxdCatLink,
						currentID:zxdID
					},
					success:function(data) {
						zxdExpand.parent('.zxd_ajax_cc_li').append(data).children('.child_cat').show("normal");
					} ,
					dataType:"html"
				});
			}
		}
	};
	$(".zxd_expand").live ('click',zxdExpander);
})
