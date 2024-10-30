=== LH Buddypress Multi Network ===
Contributors: shawfactor
Donate link: https://lhero.org/portfolio/lh-buddypress-multi-network/
Tags: buddypress, multiple, multisite, network, saas
Requires at least: 5.0
Tested up to: 5.5
Stable tag: trunk
License: GPLv2 or later

Segregate your multsite into multiple buddypress social networks.

== Description ==

Requires BuddyPress 5.0 or greater.

This plugin segregates BuddyPress social networks in a multi network WordPress install so that each WP network has a different social network. 

All functionality, eg groups, friends, profile fields, activity etc are now independent for each site

**Like this plugin? Please consider [leaving a 5-star review](https://wordpress.org/support/view/plugin-reviews/lh-buddypress-multi-network/).**

**Love this plugin or want to help the LocalHero Project? Please consider [making a donation](https://lhero.org/portfolio/lh-buddypress-multi-network/).**

== Frequently Asked Questions ==

= Why did you write this plugin?  =

Because the other two plugins that purport to do this, are not maintained and don´t work properly.

= Can you give me some examples of sites running this plugin?  =

The [LocalHero project website](https://lhero.org) and the [Melbourne Touch rugby association](https://princesparktouch.com) I founded both run on the same multisite that is powered by this plugin.

= What is something does not work?  =

LH Buddypress Multi Network, and all [LocalHero](https://lhero.org) plugins are made to WordPress standards. Therefore they should work with all well coded plugins and themes. However not all plugins and themes are well coded (and this includes many popular ones). 

If something does not work properly, firstly deactivate ALL other plugins and switch to one of the themes that come with core, e.g. twentyfirteen, twentysixteen etc.

If the problem persists please leave a post in the support forum: [https://wordpress.org/support/plugin/lh-buddypress-multi-network/](https://wordpress.org/support/plugin/lh-buddypress-multi-network/). I look there regularly and resolve most queries.

= What if I need a feature that is not in the plugin?  =

Please contact me for custom work and enhancements here: [https://shawfactor.com/contact/](https://shawfactor.com/contact/).

== Installation ==

1. Upload the `lh-buddypress-multi-network/` folder to the `/wp-content/plugins/` directory
1. Create a bp-custom.php: [https://codex.buddypress.org/themes/bp-custom-php/](https://codex.buddypress.org/themes/bp-custom-php/) file and add the line: define( 'BP_ENABLE_MULTIBLOG', true );
1. Network activate the plugin through the 'Plugins' menu in WordPress
1. That is it. 

== Changelog ==

**1.00 October 14, 2020** 
* Initial release

**1.01 March 24, 2020** 
* Minor privacy improvement