=== TMP101 Temperature Sensor ===
Contributors: robertferanec
Tags: tmp101, temperature, sensor
Requires at least: 3.0.1
Tested up to: 4.4.2
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
Shows up temperature of OpenRex board by reading the on board TMP101 sensor.
 
== Description ==
 
This plugin will create a shortcode [fa-tmp101]. When you use this shortcode on a page, it will show up temperature of OpenRex. You can choose between Celsius (use [fa-tmp101]) or Fahrenheits (use [fa-tmp101 units='fahrenheit']). The complete step-by-step tutorial about how to create this plugin can be found at: http://www.imx6rex.com/open-rex/software/how-to-create-rexface-plugin/
 
== Installation ==
 
1. Upload the plugin files to the WordPress plugin directory (e.g. '/usr/local/apache2/htdocs/wp-content/plugins/') or clone it from our github:

	cd /usr/local/apache2/htdocs/wp-content/plugins/
	git clone https://github.com/FEDEVEL/fa-tmp101-temperature-sensor.git

2. Give the plugin permissions to use 'i2cget' and '/dev/i2c-1'. For example, run 'visudo' and add following lines at the end of the file:

	#enable RexFace to read temperature sensor
	daemon ALL=(ALL:ALL)  NOPASSWD: /usr/sbin/i2cget
	daemon ALL=(ALL:ALL)  NOPASSWD: /dev/i2c-1

3. Activate the plugin through the 'Plugins' screen in WordPress
4. Use the Admin->TMP101 to configure the plugin

== Frequently Asked Questions ==

= How do I use the plugin? =

Create a new page and use shortcode [fa-tmp101]

== Changelog ==
 
= 1.0 =
* Initial version

