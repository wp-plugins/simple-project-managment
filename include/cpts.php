<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	
	class Albdesign_Register_CPT{

	
		function __construct($singular,$plural,$submenu_of = ''){

			$labels = array(
				'name'                => _x( $singular, 'Post Type General Name', 'albdesign_project' ),
				'singular_name'       => _x( $singular, 'Post Type Singular Name', 'albdesign_project' ),
				'menu_name'           => __( $plural, 'albdesign_project' ),
				'parent_item_colon'   => __( 'Parent '.$singular.' :', 'albdesign_project' ),
				'all_items'           => __( $plural, 'albdesign_project' ),
				'view_item'           => __( 'View ' .$singular, 'albdesign_project' ),
				'add_new_item'        => __( 'Add '.$singular, 'albdesign_project' ),
				'add_new'             => __( 'Add '. $singular, 'albdesign_project' ),
				'edit_item'           => __( 'Edit '.$singular, 'albdesign_project' ),
				'update_item'         => __( 'Update '.$singular, 'albdesign_project' ),
				'search_items'        => __( 'Search '.$plural, 'albdesign_project' ),
				'not_found'           => __( 'No '. strtolower($singular) .' found', 'albdesign_project' ),
				'not_found_in_trash'  => __( 'Not found in Trash', 'albdesign_project' ),
			);

			$args = array(
				'label'               => __( 'albdesign_'.$singular, 'albdesign_project' ),
				'description'         => __($plural, 'albdesign_project' ),
				'labels'              => $labels,
				'supports'            => array(),
				'taxonomies'          => array( 'project_taxonomy' ),
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				
				'show_in_menu'        => ($submenu_of != '') ? $submenu_of  : true,
				'show_in_nav_menus'   => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => 75,
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'capability_type'     => 'page',
				
			);
			register_post_type( 'albdesign_'.strtolower($singular), $args );

		
		} //end construct 
}