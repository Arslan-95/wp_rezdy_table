<?php
/*
Plugin Name: Rezdy Agent Table
*/
include_once(__DIR__ . '/includes/admin-menu.php');
include_once(__DIR__ . '/includes/shortcodes.php');

register_activation_hook( __FILE__, 'rezdy_agent_table_activate' );

function rezdy_agent_table_activate(){
  add_option('rezdy_apiKey', '');
  add_option('rezdy_names', []);
}

register_deactivation_hook( __FILE__, 'rezdy_agent_table_activate' );

function rezdy_agent_table_deactivate(){
  delete_option('rezdy_apiKey');
}