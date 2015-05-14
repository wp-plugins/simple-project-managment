<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* 
* Invoices metaboxes prefix
*/

	$prefix_invoices = 'albdesign_projects_invoices_';

	
/*
* List all clients,project, tasks
*/

	$list_all_clients_info_config = array(
		'id'             	=> 	'all_clients_meta_box',          // meta box id, unique per meta box
		'title'          	=> 	apply_filters('albwppm_invoices_cpt_invoice_infos_metabox_title','Prepare invoices'), // meta box title
		'pages'          	=> 	array('albdesign_invoice'),      // post types, accept custom post types as well, default is array('post'); optional
		'context'        	=> 	'normal',            // where the meta box appear: normal (default), advanced, side; optional
		'priority'       	=> 	'high',            // order of meta box: high (default), low; optional
		'fields'        	=> 	array(),            // list of meta fields (can be added by field arrays)
		'local_images'  	=> 	false,          // Use local or hosted images (meta box images for add/remove)
		'use_with_theme'	=> 	false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
	);
	
	$all_clients_info_metabox =  new AT_Meta_Box($list_all_clients_info_config);

	//Associate invoice to client,project task
	$all_clients_info_metabox->addPosts($prefix_invoices.'client_field_id',
										array(
											'post_type' =>	'albdesign_client',
											'args' 		=> 	array('orderby' => 'title', 'order' => 'ASC')
										),
										array(
											'name'			=>	apply_filters('albwppm_invoices_cpt_invoice_infos_metabox_send_to_client_label','Send to client'),
											'emptylabel'	=>	apply_filters('albwppm_invoices_cpt_invoice_infos_metabox_send_to_client_no_client_selected_option','No client selected'),
											'group' 		=> 'start'
										)
									);
	
	$all_clients_info_metabox->addPosts($prefix_invoices.'project_field_id',
										array(
											'post_type'		=>	'albdesign_project',
											'args' 			=>	array('orderby' => 'title', 'order' => 'DESC')
										),
										array(
											'name'			=>	apply_filters('albwppm_invoices_cpt_invoice_infos_metabox_related_to_project_label','Related to project'),
											'emptylabel'	=>	apply_filters('albwppm_invoices_cpt_invoice_infos_metabox_related_to_project_no_project_selected','No project selected'),
										)
									);
	
	$all_clients_info_metabox->addPosts($prefix_invoices.'task_field_id',
										array(
											'post_type' => 'albdesign_task',
											'args' 		=> array('orderby' => 'title', 'order' => 'DESC')
										),
										array(
											'name'			=>	apply_filters('albwppm_invoices_cpt_invoice_infos_metabox_related_to_task_label','Related to task'),
											'emptylabel'	=>	apply_filters('albwppm_invoices_cpt_invoice_infos_metabox_related_to_project_no_task_selected','No task selected'),
											'group' 		=>	'end'
										)
									);	

	$all_clients_info_metabox->Finish();

	
//Add our custom metabox that will contain the invoice table 	