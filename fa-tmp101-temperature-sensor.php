<?php
/*
Plugin Name: TMP101 Temperature Sensor
Plugin URI: http://www.imx6rex.com/rexface/plugins/
Description: Reads and shows up the temperature of OpenRex
Version: 1.0
Author: Robert Feranec, FEDEVEL
Author URI: http://www.fedevel.com/about-robert
Text Domain: fa-tmp101-temperature-sensor
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
Copyright 2016 by FEDEVEL
 
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
 
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with this program; if not, see http://www.gnu.org/licenses/gpl-2.0.html
*/
 
//the following line must be here, it's blocking direct access to your plugin PHP files 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

//This will tell WordPress to call "fa_tmp101_temperature_sensor_admin_menu" which will create a link to our TMP101 Setting page in Admin menu
add_action( 'admin_menu', 'fa_tmp101_temperature_sensor_admin_menu' );
 
//This will add "TMP101" link into Admin menu
function fa_tmp101_temperature_sensor_admin_menu() {
    //add_menu_page ( page_title, menu_title, capability, __FILE__, function_which_we_call_to_create_our_TMP101_setting_page )
    add_menu_page('TMP101 Temperature Sensor', 'TMP101', 'administrator', __FILE__, 'fa_tmp101_temperature_sensor_admin_sensor_page');
 
    //you can set refresh rate in admin panel. this will help us to create refresh_rate "variable"
    add_action( 'admin_init', 'fa_register_tmp101_temperature_sensor_settings' );
}
 
//let wordpress to know about our refresh_rate setting which we will be able to set in the TMP101 Setting page of Admin section
function fa_register_tmp101_temperature_sensor_settings() {
    register_setting( 'fa-tmp101-temperature-sensor-settings-group', 'refresh_rate' );
}
 
//when our TMP101 Temperature Sensor plugin is activated, set the "refresh_rate" to default value of 1 minute
register_activation_hook( __FILE__, 'fa_tmp101_set_default_options' );
function fa_tmp101_set_default_options(){
    update_option('refresh_rate', '60000'); //default value for refresh_hrate will be 1 minute
}
 
//Here is the "HTML" of our Admin TMP101 Temperature Sensor Setting Page
function fa_tmp101_temperature_sensor_admin_sensor_page() {
        if ( !current_user_can( 'manage_options' ) )  {
                wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        ?>
        <div class="wrap">
        <h2>TMP101 Temperature Sensor - Setting page</h2>
 
        <div class="wrap">
                To use this plugin, create a new page and use shortcode:
		<ul style="list-style-type: circle; padding-left: 20px">
			<li>[fa-tmp101] to show the OpenRex temperature in Celsius</li>
			<li>[fa-tmp101 units='fahrenheit'] to show the OpenRex temperature in Fahrenheits</li>
		</ul>
        </div>

        <form method="post" action="options.php">
            <?php 
            settings_fields( 'fa-tmp101-temperature-sensor-settings-group' );
            do_settings_sections( 'fa-tmp101-temperature-sensor-settings-group' ); 
            $refresh_rate = esc_attr(get_option('refresh_rate'));
            ?>
            <table class="form-table">                 
                <tr valign="top">
                <th scope="row">Automatic Refresh rate:</th>
                <td>
                    <select name="refresh_rate">
                        <option value="10000" <?php echo ($refresh_rate == "10000")?"selected":""?> >10 seconds</option>
                        <option value="60000" <?php echo ($refresh_rate == "60000")?"selected":""?> >1 minute</option>
                        <option value="600000" <?php echo ($refresh_rate == "600000")?"selected":""?> >10 minutes</option>
                    </select> 
                </td> 
                </tr>             
            </table>
             
            <?php submit_button(); ?>
 
        </form>
        </div>
        <?php 
}

//[fa-tmp101] shortcode. It will read and show the value of TMP101 OpenRex sensor. 
//To get the temperature in Celsius, use [fa-tmp101]
//To get the temperature in Fahrenheit, use [fa-tmp101 units='fahrenheit']
add_shortcode("fa-tmp101", "fa_access_tmp101_sensor");
function fa_access_tmp101_sensor($atts, $content = null)
{
    extract( shortcode_atts( array(
        'units' => 'celsius', //default 'units' value is 'celsius'
    ), $atts ) );
  
    //$new_content will replace the [fa-tmp101] shortcode
    $new_content = '<span id="fa_tmp101">Reading ...</span>';
    $new_content .= '<div style="background-color:#cacaca;border: 1px solid #acacac; text-align:center; width:300px" onclick="fa_access_tmp101();" >Read Temperature Now</div>'; //button
    $new_content .= '<input type="hidden" id="fa_openrex_ip" value="'.$_SERVER['SERVER_ADDR'].'" />'; //transfer IP address to javascript
    $new_content .= '<input type="hidden" id="fa_tmp101_units" value="'.$units.'" />'; //transfer unites to javascript
  
    ?>
    <script>
 
        //when the page is loaded, read the temperature
        window.onload=init_fa_tmp101_scripts;
        function init_fa_tmp101_scripts()
        {
            fa_access_tmp101();
        }
 
        //SetInterval runs at the selected refresh rate
        setInterval(function()
        {
            fa_access_tmp101(); //read the temperature
        }, <?php echo esc_attr(get_option('refresh_rate')); ?>);
 
    </script>
    <?php
 
    //$new_content string is processed and will be shown instead of the shortcode
    $html_output = do_shortcode($new_content);
    return $html_output;
}

//we will be using javascript, so load the javascript file
add_action( 'wp_enqueue_scripts', 'fa_tmp101_scripts' );
function fa_tmp101_scripts() {
    wp_enqueue_script("jquery-fa-tmp101", plugins_url("/js/tmp101.js", __FILE__ ),array('jquery', 'jquery-ui-core'));
}
