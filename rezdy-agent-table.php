<?php
/*
Plugin Name: Rezdy Agent Table
Description: Plugin for request and display data from Rezdy Api.
Version: 1.0
Author: Arslan Sarakaev
Author URI: https://www.instagram.com/arslan_sarakaev/
Plugin URI: https://kwork.ru/user/arslansarakaev
*/
include_once(__DIR__ . '/includes/admin-menu.php');
include_once(__DIR__ . '/includes/admin-submenu.php');
include_once(__DIR__ . '/includes/shortcodes.php');

register_activation_hook( __FILE__, 'rezdy_agent_table_activate' );

function rezdy_agent_table_activate(){
  add_option('rezdy_apiKey', '');
  add_option('rezdy_names', []);
}

register_uninstall_hook( __FILE__, 'rezdy_agent_table_uninstall' );

function rezdy_agent_table_uninstall(){
  delete_option('rezdy_apiKey');
  delete_option('rezdy_names');
}