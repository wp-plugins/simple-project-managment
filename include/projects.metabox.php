<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	
	
	
/* 
* configure PROJECTS metaboxes
*/

$prefix = 'albdesign_project_';

$config = array(
	'id'             => 'projects_meta_box',         
	'title'          => apply_filters('alwppm_project_cpt_project_infos_metabox_title','Project Infos'),          
	'pages'          => array('albdesign_project'),    
	'context'        => 'normal',           
	'priority'       => 'high',            
	'fields'         => array(),          
	'local_images'   => false,         
	'use_with_theme' => false         
);


/*
* Initiate your meta box
*/
$projects_meta =  new AT_Meta_Box($config);

$projects_meta->addText($prefix.'estimate_field_id',array(
														'name'	=>	apply_filters('albwppm_project_estimate_label_text','Estimate')
													)
												);

$projects_meta->addTextarea($prefix.'private_notes_field_id',array(
														'name'	=> 	apply_filters('albwppm_project_private_notes_label_text','Private Project Notes'), 
														'std'	=>	'',
														'group'	=>	'start'
													)
												);


$projects_meta->addTextarea($prefix.'public_notes_field_id',array(
														'name'	=>	apply_filters('albwppm_project_public_notes_label_text','Public Project Notes'), 
														'std'	=>	'',
														'group'	=>	'end'
													)
												);

//Project associated to client
	$projects_meta->addPosts($prefix.'client_field_id',array(
															'post_type' => 'albdesign_client'
														),
														array(
															'name'			=> apply_filters('albwppm_project_associate_to_client_label_text','Associate to client '),
															'emptylabel'	=> apply_filters('albwppm_project_associate_to_client_no_client_selected_label_text','No Client selected')
														)
											);



//Project Start Date , end date 
	$projects_meta->addDate($prefix.'start_date_field_id',array(
											'name'=> apply_filters('albwppm_project_start_date_label_text','Start Date ( i.e 24-12-2015 )'),
											'format' => 'd-m-yy',
											'group' => 'start')
										);
										
	$projects_meta->addDate($prefix.'target_end_date_field_id',array(
											'name'=> apply_filters('albwppm_project_target_end_date_label_text','Target End Date ( i.e 24-12-2015 )'),
											'format' => 'd-m-yy')
										);
	
	$projects_meta->addDate($prefix.'end_date_field_id',array(
											'name'=> apply_filters('albwppm_project_actual_end_date_label_text','Actual End Date ( i.e 24-12-2015 )'),
											'format' => 'd-m-yy',
											'group' => 'end')
										);

//Project Status .... Lead , Ongoing , Finished
	$projects_meta->addHidden($prefix.'text_field_id',array('name'=> 'albdesignDummyGroupStartText' , 'std' => 'albdesignDummyGroupStartText','group' => 'start'));

	$projects_meta->addSelect($prefix.'status_field',
								array(
									'project_status_not_set'			=>	apply_filters('albwppm_project_status_dropdown_not_set_text','Not set') , 
									'project_status_lead'				=>	apply_filters('albwppm_project_status_dropdown_lead_text','Lead') , 
									'project_status_ongoing'			=>	apply_filters('albwppm_project_status_dropdown_ongoing_text','Ongoing') , 
									'project_status_on_hold' 			=> 	apply_filters('albwppm_project_status_dropdown_on_hold_text','Onhold') , 
									'project_status_waiting_feedback' 	=> 	apply_filters('albwppm_project_status_dropdown_awaiting_feedback_text','Awaiting Feedback') , 
									'project_status_finished'			=>	apply_filters('albwppm_project_status_dropdown_completed_text','Completed') ,
								),
								array(
									'name'	=>	apply_filters('albwppm_project_status_dropdown_label_text','Project Status '), 
									'std'	=>	array('project_status_not_set')
									)
							);
							
							
	
//Project progress	
	$projects_meta->addSelect($prefix.'progress_field',array(
													'not_set'	=>	apply_filters('albwppm_project_progress_dropdown_not_set_text','Not set') ,
													'10'		=>	apply_filters('albwppm_project_progress_dropdown_10_percent_text','10 %') ,
													'20'		=>	apply_filters('albwppm_project_progress_dropdown_20_percent_text','20 %') ,
													'30'		=>	apply_filters('albwppm_project_progress_dropdown_30_percent_text','30 %') ,
													'40' 		=> 	apply_filters('albwppm_project_progress_dropdown_40_percent_text','40 %') ,
													'50'		=>	apply_filters('albwppm_project_progress_dropdown_50_percent_text','50 %') ,
													'60'		=>	apply_filters('albwppm_project_progress_dropdown_60_percent_text','60 %') ,
													'70'		=>	apply_filters('albwppm_project_progress_dropdown_70_percent_text','70 %') ,
													'80'		=>	apply_filters('albwppm_project_progress_dropdown_80_percent_text','80 %') ,
													'90'		=>	apply_filters('albwppm_project_progress_dropdown_90_percent_text','90 %') ,
													'100'		=>	apply_filters('albwppm_project_progress_dropdown_100_percent_text','100 %') ,
												),
												array(
													'name'	=>	apply_filters('albwppm_project_progress_dropdown_label_text','Project Progress '), 
													'std'	=>	array('not_set')
												)
											);	

//Project priority
	$projects_meta->addSelect($prefix.'priority_field',array(
														'project_priority_not_set'	=>	apply_filters('albwppm_project_priority_dropdown_not_set_text','Not Set'),
														'project_priority_low'		=>	apply_filters('albwppm_project_priority_dropdown_low_text','Low'),
														'project_priority_normal'	=>	apply_filters('albwppm_project_priority_dropdown_normal_text','Normal'),
														'project_priority_high' 	=>	apply_filters('albwppm_project_priority_dropdown_high_text','High'),
													),
													array(
														'name'	=>	apply_filters('albwppm_project_priority_dropdown_label_text','Project Priority '), 
														'std'	=>	array('project_priority_not_set')
													)
												);	
	
	$projects_meta->addHidden($prefix.'text_field_id',array(
													'name'	=>	'albdesignDummyGroupEndText' , 
													'std' 	=>	'albdesignDummyGroupEndText',
													'group' =>	'end'
													)
												);


	//Finish Meta Box Declaration 
	$projects_meta->Finish();



/*
* Project has the following tasks 
*/


	$projects_tasks_config = array(
		'id'             => 'project_tasks_meta_box',          // meta box id, unique per meta box
		'title'          => apply_filters('albwppm_projects_cpt_project_tasks_metabox_title','Project Tasks'),          // meta box title
		'pages'          => array('albdesign_project'),      // post types, accept custom post types as well, default is array('post'); optional
		'context'        => 'side',            // where the meta box appear: normal (default), advanced, side; optional
		'priority'       => 'low',            // order of meta box: high (default), low; optional
		'fields'         => array(),            // list of meta fields (can be added by field arrays)
		'local_images'   => false,          // Use local or hosted images (meta box images for add/remove)
		'use_with_theme' => false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
	);


	$tasksAssociateWithProject = apply_filters('albwppm_projects_cpt_project_tasks_metabox_no_tasks_yet','No tasks');

	//If we have a project ID , look for existing tasks associated with it
	if(isset($_GET['post'])){
		
		$taskIDForProjects = $_GET['post'];

		$taskStatusToDisplay = 'Not Set';

		//get all projects for this client
		$get_tasks_for_project_params =array(
			'showposts'		=>	-1,
			'post_type' 	=>	'albdesign_task',
			'post_status' 	=>	'publish',
			'meta_key'		=>	'albdesign_task_for_project_field',
			'meta_value'	=>	$taskIDForProjects
		);
		
		$query_tasks_for_project = new WP_Query();
		
		$results_tasks_for_project = $query_tasks_for_project->query($get_tasks_for_project_params);

		//if we have at least one project for this client
		if(sizeof($results_tasks_for_project)>=1){
		
			$tasksAssociateWithProject='';
		
			foreach($results_tasks_for_project as $single_task_for_project){
				
				//Task Status
				if(get_post_meta($single_task_for_project->ID , 'albdesign_task_status_task_field', true)){
				
					$taskStatus = get_post_meta($single_task_for_project->ID , 'albdesign_task_status_task_field', true);

					$taskStatusToDisplay = albdesign_get_human_task_status_by_meta_value_as_bullet($taskStatus);
				}
				
				$tasksAssociateWithProject.= $taskStatusToDisplay ;
				
				//Task edit link
				$tasksAssociateWithProject.= apply_filters ( 'albwppm_projects_cpt_project_single_task_metabox_link' , '<a href="'.get_edit_post_link($single_task_for_project->ID).'">'. $single_task_for_project->post_title .'</a>' , $single_task_for_project->ID , $single_task_for_project->post_title );				
				$tasksAssociateWithProject.= '<br>';
				
			} 
		
		} 

		

	} //end if isset post id 


	$tasks_projects_metabox =  new AT_Meta_Box($projects_tasks_config);
	
	$tasks_projects_metabox->addParagraph('button_id',array('value' => $tasksAssociateWithProject));
	
	$tasks_projects_metabox->Finish();