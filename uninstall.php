<?php
/**
 * This file runs when the plugin in uninstalled (deleted).
 * This will not run when the plugin is deactivated.
 * Ideally you will add all your clean-up scripts here
 * that will clean-up unused meta, options, etc. in the database.
 *
 * @package WP Plugin Template/Uninstall
 */

// If plugin is not being uninstalled, exit (do nothing).
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

/**
 * Making WPDB as global
 * to access database information.
 */
global $wpdb;

/**
 * Name of tables to be dropped.
 *
 * @var array $wp_plugin_template_tables
 */
$wp_plugin_template_tables = array(
	// 'wp_plugin_template'
);

if ( isset( $wp_plugin_template_tables ) && is_array( $wp_plugin_template_tables ) ) {
	// drop the table(s) from the database.
	foreach ( $wp_plugin_template_tables as $wp_plugin_template_table ) {
		$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %s', $wp_plugin_template_table ) );
	}
}

// Remove the plugin version number.
$wp_plugin_template_prefix = 'WP_Plugin_Template';
delete_option( $wp_plugin_template_prefix . '_version' );
