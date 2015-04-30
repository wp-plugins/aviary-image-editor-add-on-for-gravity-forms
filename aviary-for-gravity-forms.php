<?php
/*
Plugin Name: Adobe Creative SDK / Aviary Editor Addon For Gravity Forms
Plugin URI: http://netherworks.com/gform-aviary-addon
Description: A free plugin that integrates the awesome Adobe Creative SDK (formerly Aviary) Photo / Image Editor with the Gravity Forms Plugin. 
Version: 3.0 (Beta r3)
Author: Leon Kiley - NetherWorks, LLC
Author URI: http://netherworks.com
*/
/*
    *  Copyright (C) 2011-2014  Leon Kiley
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
          include 'includes/gform_aviary_field.php';
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
