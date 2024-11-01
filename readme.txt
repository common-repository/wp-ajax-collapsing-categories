=== WP ajax Collapsing Categories ===
Contributors: ZHAO Xudong
Donate link: http://html5beta.com/donate/
Plugin URI: http://html5beta.com/wordpress/wp-ajax-collapsing-categories/
Tags: categories, sidebar, widget
Requires at least: 2.8
Tested up to: 3.10
Stable tag: 1.3

This plugin uses jQuery to expand or collapsable the set of posts for each category,
uses ajax to get the expandable content from server.

== Description ==

This plugin uses jQuery to expand or collapsable the set of posts for each category,
uses ajax to get the expandable content from server.

0.WHY i do this.

in my work,i need a widget like this,so l learned to write it.
and i fully tested it  in my site,it works fine.
since i am newbie,so there might be some bug or problem i do not know,
i appreciate it if you let me know.

1.About the Current Post

if the current post is in the category,the category link will has a "zxd_current_cat" class,
i certainly can add the css file in this little plugin,but i will not,because it is ugly.
you can customize it in your theme css file,like

#sidebar .zxd_current_cat{
        font-weight:bold;
		color:#08c
}

2.Suggestion

more clean way to use this widget is set it in your theme's function.php.
if you want to know how,visit http://html5beta.com/wordpress/wp-ajax-cllapsing-categories/,
i will explain it there.

== Installation ==
1. Upload the full directory into your wp-content/plugins directory or download it from plugin administration page
2. Activate the plugin at the plugin administration pagee

== Known bugs ==
none

== Frequently Asked Questions == 
None for Now

== Changelog ==
1.3 fix bugs,if user do not input or input wrong category slug list
show all top level categories

== Upgrade Notice ==
None for now

== Screenshots ==
None for now 

== License ==
This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

