=== Plugin Name ===
Contributors: Ryan Kosh (this should be a list of wordpress.org userid's)
Donate link: http://ryadcorp.com/
Tags: woocommerce, wholesale, pricing
Requires at least: 3.0.1
Tested up to: 4.2
Stable tag: 1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin adds wholesale price box to simple and variation products in woocommerce.

== Description ==

This plugin adds wholesale price box to simple and variation products in woocommerce.  It adds an input box 
to the simple and variation products named wholesale price which you can assign a whole sale price to the product.
It creates a user role named wholesale_customer which is same rights as woocommerce customrer role, 
you can then assign a user to that role and they will see wholesale pricing.  
There is admin settings screen which allows you to customize options for the plugin.

A few notes about the sections above:

*   "Tested Up To" - tested with the lastest version of wordpress 4.2 and latest version of woocommerce 2.2.10

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `woocommerce-wholesale-pricing` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('woocommerce-wholesale-pricing'); ?>` in your templates

== Frequently Asked Questions ==

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets 
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png` 
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 1.4 =
* Add option to show wholesale pricing to administrator.
* Stream line wholesale user role check code.
* Add option to disable coupon form for wholesale users.

= 1.3 =
* Streamline user role code.
* Fix display when product is on sale, assign regular price to sale price.

= 1.2 =
* Add wholesale user role tax class override and selection. Gives the ability to assign special tax class to wholesale user role.
* Add div and class tags to all html output.
* Add simple wholesale quick edit admin fuctions.
* Add admin options to everything including all variations.
* Tweak code.
= 1.1 =
* Break main file and admin code into seperate sections.
* Add new options and styles.
* Add variation price matrix to over come variation single variation price display.
* Fix mini cart for variable wholesale prices.
* Tweak code.
= 1.0 =
* First stabel release.

== Upgrade Notice ==
