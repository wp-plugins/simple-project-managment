<?php

//$post_id refers to project ID 

switch ( $column ) {
	
	case 'deadline':
		$deadline= get_post_meta($post_id,'albdesign_project_end_date_field_id',true);
		echo apply_filters('albwppm_projects_cpt_list_post_table_project_deadline_value',$deadline , $post_id);
		break;

	case 'client' :
		$client_id= get_post_meta($post_id,'albdesign_project_client_field_id',true);
		$client_name = get_the_title($client_id);
		echo apply_filters('albwppm_projects_cpt_list_post_table_project_client_name_value','<a href="'.get_edit_post_link($client_id).'">' . $client_name .' </a>' , $client_id);
		break;
	
	case 'earnings':
		$project_estimate = get_post_meta($post_id,'albdesign_project_estimate_field_id',true);
		$estimate_value = ($project_estimate ==''  ) ? 'Not set' : $project_estimate;
		echo apply_filters('albwppm_projects_cpt_list_post_table_project_estimate_value',$estimate_value, $post_id);
		break;
	
	case 'get_tasks_for_project':
		tasks_for_project_as_bullets($post_id);
		break;
	
	case 'status':
	
		$status = get_post_meta($post_id,'albdesign_project_status_field',true);
		$projectStatusToDisplay = '';

		switch($status){
			case 'project_status_not_set':
				$projectStatusToDisplay = apply_filters('albwppm_projects_cpt_list_post_table_project_status_not_set_value','Not Set',$status , $post_id);
				break;
			
			case 'project_status_lead':
				$projectStatusToDisplay =  apply_filters('albwppm_projects_cpt_list_post_table_project_status_lead_value','Lead',$status , $post_id);
				break;
				
			case 'project_status_on_hold':
				$projectStatusToDisplay = apply_filters('albwppm_projects_cpt_list_post_table_project_status_onhold_value','On hold',$status , $post_id);
				break;

			case 'project_status_waiting_feedback':
				$projectStatusToDisplay = apply_filters('albwppm_projects_cpt_list_post_table_project_status_awaiting_feedback_value','Awaiting feedback',$status , $post_id);
				break;			
			
			case 'project_status_ongoing':
				$projectStatusToDisplay = apply_filters('albwppm_projects_cpt_list_post_table_project_status_ongoing_value','Ongoing',$status , $post_id);
				break;
				
			case 'project_status_finished':
				$projectStatusToDisplay = apply_filters('albwppm_projects_cpt_list_post_table_project_status_finished_value','Finished',$status , $post_id);
				break;					
				
		}
		
		echo $projectStatusToDisplay ;
		
		break;			
}