<?php
/**
 * Fired when the plugin is uninstalled.
 *
 *
 * @link       http://morkva.co.ua/
 * @since      1.0.0
 *
 * @package    morkvanp-plugin
 */
// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Clear Database stored data
$books = get_posts( array( 'post_type' => 'book', 'numberposts' => -1 ) );

foreach( $books as $book ) {
	wp_delete_post( $book->ID, true );
}

// Access the database via SQL
global $wpdb;
$wpdb->query( "DELETE FROM ".$wpdb->prefix."posts WHERE post_type = 'book'" );
$wpdb->query( "DELETE FROM ".$wpdb->prefix."postmeta WHERE post_id NOT IN (SELECT id FROM ".$wpdb->prefix."posts)" );
$wpdb->query( "DELETE FROM ".$wpdb->prefix."term_relationships WHERE object_id NOT IN (SELECT id FROM ".$wpdb->prefix."posts)" );