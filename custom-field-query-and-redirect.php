<?php
/**
 *
 * Plugin Name: Custom Field Query and Redirect
 * Plugin URI: https://alexhofstaetter.at
 * Description: WordPress Plugin to add custom field query var (matched by regex) and redirect to the post which has the queried custom field value.
 * Author: Alexander Hofstätter
 * Author URI: https://alexhofstaetter.at
 *
 * Version: 1.0.0
 * License: GPL2
 *
 * Text Domain: custom-field-query-and-redirect
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Add custom query variable
function cfqar_register_query_var( $vars )
{
    $vars[] = 'qr';
    return $vars;
}
add_filter( 'query_vars', 'cfqar_register_query_var' );

// Redirect to the query URL if the URL matches the regex
function cfqar_register_rewrite_rule()
{
	add_rewrite_rule('^(([0-9]{1,2})([a-z]|[A-Z])([0-9]))/?$', 'index.php?qr=$matches[1]', 'top');
}
add_action( 'init', 'cfqar_register_rewrite_rule' );

// Try to get the post with the queried custom field value and do the actual redirect (or throw an 404)
function cfqar_redirect()
{
    if( get_query_var( 'qr' ) )
    {
        $posts = get_posts([
	    	'post_type'			=> 'product',
	    	'meta_key'			=> 'wpcf-qr-code',
			'meta_value'		=> get_query_var( 'qr' )
		]); 
		 
		if($posts) {
			wp_redirect( get_permalink( $posts[0]->ID ) ); 
			exit();  
	    }
	    else {
		    global $wp_query;
		    $wp_query->set_404();
		    status_header(404);
	    }
    }
}
add_action( 'template_redirect', 'cfqar_redirect' );