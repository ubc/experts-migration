<?php
/*
Plugin Name: UBC Experts Migration commands
Author: Flynn O'Connor
Description: Custom WP CLI commands to migrate experts away from profile plugin.
Version: 0.1
*/

$plugin_dir = plugin_dir_path( __FILE__ );

if ( defined('WP_CLI') && WP_CLI ) {
    include __DIR__ . '/experts-migration-commands.php';
}

// let's create the function for the custom type
function olt_exerpts_register_taxonomies() {

	
	// custom post content type taxonomy
	$content_type_template = array(
		'tax_slug'     => 'profile_field',
		'tax_label'    => 'Fields',
		'tax_single'   => 'Field',
		'post_type'    => array('post'),
		'rewrite_slug' => 'field'
	);

	$args = array(
		'hierarchical' => true,     /* if this is true it acts like categories */
		'labels'       => array(
			'name'              => __( $content_type_template['tax_label'] ), /* name of the custom taxonomy */
			'singular_name'     => __( $content_type_template['tax_single'] ), /* single taxonomy name */
			'search_items'      => __( 'Search ' . $content_type_template['tax_label'] ), /* search title for taxomony */
			'all_items'         => __( 'All ' . $content_type_template['tax_label'] ), /* all title for taxonomies */
			'parent_item'       => __( 'Parent ' . $content_type_template['tax_single'] ), /* parent title for taxonomy */
			'parent_item_colon' => __( 'Parent ' . $content_type_template['tax_single'] . ':' ), /* parent taxonomy title */
			'edit_item'         => __( 'Edit ' . $content_type_template['tax_single'] ), /* edit custom taxonomy title */
			'update_item'       => __( 'Update ' . $content_type_template['tax_single'] ), /* update title for taxonomy */
			'add_new_item'      => __( 'Add New ' . $content_type_template['tax_single'] ), /* add new title for taxonomy */
			'new_item_name'     => __( 'New ' . $content_type_template['tax_single'] . ' Name' ) /* name title for taxonomy */
		),
		'show_ui'      => true,
		'query_var'    => true,
		'show_in_rest' => true,
		'rewrite'      => array( 'slug' => $content_type_template['rewrite_slug'] ),
	);
	register_taxonomy( $content_type_template['tax_slug'], $content_type_template['post_type'] , $args );

}

// adding the function to the Wordpress init
add_action( 'init', 'olt_exerpts_register_taxonomies' );
