<?php
/*
Plugin Name: Aviary Editor Addon For Gravity Forms Deluxe
Plugin URI: http://netherworks.com/gform-aviary-addon
Description: A premium plugin that integrates the awesome Aviary Photo / Image Editor with the Gravity Forms Plugin. 
Version: 1.0
Author: Leon Kiley - NetherWorks, LLC
Author URI: http://netherworks.com
*/
/*
    *  Copyright (C) 2011-2013  Leon Kiley
    *  http://watersedgeweb.com
    *  support@watersedgeweb.com

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/* Disallow direct access to the plugin file */
// Add a custom field button to the advanced to the field editor

// Add a custom field button to the advanced to the field editor
if (!class_exists('GFAviaryEditor')) {
    class GFAviaryEditor {
        public function __construct() {
          include 'includes/class.gf_aviary_field.php';
          new GFAviaryField();
          add_filter("gform_addon_navigation", array(&$this,"add_menu_item"));          
        }
        function add_menu_item($menu_items){
            $menu_items[] = array("name" => "gf_aviary_options", "label" => "Aviary Options", "callback" => array(&$this, "aviary_options_page"), "permission" => "edit_posts");
            return $menu_items;
        }        
        function aviary_options_page(){
          include 'includes/options.php';
        }        
    }    
}
add_action('init', 'gf_aviary_editor');
function gf_aviary_editor(){
  new GFAviaryEditor();
}
?>
<?php
/* Display a notice that can be dismissed 
add_action('admin_notices', 'settings_admin_notice');
function settings_admin_notice() {
	global $current_user ;
        $user_id = $current_user->ID;
        // Check that the user hasn't already clicked to ignore the message 
	if ( ! get_user_meta($user_id, 'settings_ignore_notice') ) {
        echo '<div class="updated"><p>';
        printf(__('<b>Aviary Editor Addon For Gravity Forms: </b> Please make sure your <a href="admin.php?page=gf_aviary_options">plugin settings</a> are complete so that the plugin functions as expected. | <a href="%1$s">Hide Notice</a>'), '?settings_nag_ignore=0');
        echo "</p></div>";
	}
}
add_action('admin_init', 'settings_nag_ignore');
function settings_nag_ignore() {
	global $current_user;
        $user_id = $current_user->ID;
        // If user clicks to ignore the notice, add that to their user meta 
        if ( isset($_GET['settings_nag_ignore']) && '0' == $_GET['settings_nag_ignore'] ) {
             add_user_meta($user_id, 'settings_ignore_notice', 'true', true);
	}
}
*/
?>