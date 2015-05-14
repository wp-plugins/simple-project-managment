<?php
class Albdesign_Projects_Project_Helpers{
	
	
	/*
	* Returns project status as text
	*/
	public static function albdesign_get_human_project_status_by_meta_value_as_text($status){

		$projectStatusToDisplay = 'Not Set';

		switch($status){
			case 'project_status_not_set':
				$projectStatusToDisplay = apply_filters('albwppm_single_project_status_not_set','Not Set');
				break;
			
			case 'project_status_lead':
				$projectStatusToDisplay = 'Lead';
				break;
				
			case 'project_status_on_hold':
				$projectStatusToDisplay = 'On Hold';
				break;

			case 'project_status_waiting_feedback':
				$projectStatusToDisplay = 'Awaiting Feedback';
				break;			
			
			case 'project_status_ongoing':
				$projectStatusToDisplay = 'Ongoing';
				break;
				
			case 'project_status_finished':
				$projectStatusToDisplay = 'Completed';
				break;					
				
		}
		
		return $projectStatusToDisplay ;
	}			

	/*
	* Returns a list of projects associated with client ... Used on extra columns and inside client CPT
	*/
	public static function get_projects_for_client_extra_columns($clientid,$return_or_show='return'){
	
		if($clientid && ( $clientid > 0)){
			
			$projectsAssociateWithClient= apply_filters('albwppm_no_projects_from_this_client_yet','No projects');
			
			//get all projects for this client
			$get_projects_for_clients_params =array(
				'showposts'		=>	-1,
				'post_type' 	=> 	'albdesign_project',
				'post_status' 	=> 	'publish',
				'meta_key'		=>	'albdesign_project_client_field_id',
				'meta_value'	=> 	$clientid
			);
			
			$query_projects_for_client = new WP_Query();
			
			$results_projects_for_client = $query_projects_for_client->query($get_projects_for_clients_params);

			//if we have at least one project for this client
			if(sizeof($results_projects_for_client)>=1){
			
				$projectsAssociateWithClient='';
			
				foreach($results_projects_for_client as $single_project_for_client){
					
					//Project edit link
					$projectsAssociateWithClient.= '<a href="post.php?post='.$single_project_for_client->ID .'&action=edit">'. $single_project_for_client->post_title .'</a>';
					
					//Project Status
					if(get_post_meta($single_project_for_client->ID , 'albdesign_project_status_field', true)){
					
						$projectStatus = get_post_meta($single_project_for_client->ID , 'albdesign_project_status_field', true);

						$projectStatusToDisplay = self::albdesign_get_human_project_status_by_meta_value_as_text($projectStatus);
					}
					
					$projectsAssociateWithClient.= ' ' . $projectStatusToDisplay ;
					
					$projectsAssociateWithClient.= '<br>';
					
				} //end foreach
			
			} else{
				//existing client but no projects associated with him
				$projectsAssociateWithClient= apply_filters('albwppm_no_projects_from_this_client_yet','No projects','no_projects_found_for_client');
			}

			return $projectsAssociateWithClient;

		}
		
		return false;
	}
	

	
	/*
	* Return number of all projects by default , OPTIONS is array of additional WP_QUERY params
	*/
	
	static function get_all($options=array()){
	
		$query = array('posts_per_page' => -1, 'post_type' => 'albdesign_project');
		
		if (isset($options['args']) ) {
			$query = array_merge($query,(array)$options['args']);
		}

	
		//get all projects
		$query_all_projects = new WP_Query();
		$results_all_projects = $query_all_projects->query($query);
		
		return sizeof($results_all_projects) ;
	}
	


	/*
	* Get projects by status ... completed , cancelled , hold
	*/
	static function get_by_status($status){
	
		switch ($status){
		
			case 'completed':
				$which_status = 'project_status_finished';
				break;
				
			case 'not_set':
				$which_status = 'project_status_not_set';
				break;
				
			case 'lead':
				$which_status = 'project_status_lead';
				break;
				
			case 'ongoing':
				$which_status = 'project_status_ongoing';
				break;	

			case 'onhold':
				$which_status = 'project_status_on_hold';
				break;	
				
			case 'awaiting_feedback':
				$which_status = 'project_status_waiting_feedback';
				break;					

			default :
				$which_status = 'project_status_finished';				
				
		}
	
		$k['args'] = array( 'meta_key'   =>  'albdesign_project_status_field',
							'meta_value' => $which_status
						 );
		
		return self::get_all($k) ;
	}	
	
	
	/*
	* Get human project status by project ID 
	*/
	static function get_human_status_by_project_id($id){
		
		if($id==''){
			return 'No ID specified';
		}

		$status = self::get_project_meta_value_by_project_id($id,'albdesign_project_status_field');

		return  self::albdesign_get_human_project_status_by_meta_value_as_text($status);

	}
		
	
	/*
	* Get client name by project ID
	*/
	static function get_client_name_by_project_id($id){
		
		if($id==''){
			return 'No ID specified';
		}
	
		$client_id = get_post_meta($id,'albdesign_project_client_field_id',true);

		if($client_id > 0 ){
			return get_the_title($client_id);
		}
		
		return 'No Client set';
	}

	
	/*
	* Get project meta value by project ID
	*/
	static function get_project_meta_value_by_project_id($id,$meta){
		
		if($id=='' || $meta == ''){
			return 'Not Set';
		}

		$meta_value = get_post_meta($id,$meta,true);
		
		if($meta_value=='not_set'){
			return 'Not Set';	
		}
		
		return ($meta_value) ? $meta_value : 'Not Set';
		
	}
	
	
	/*
	* Return human project priority by project ID 
	*/
	static function get_human_priority_by_project_id($id){
		
		$priority = self::get_project_meta_value_by_project_id($id,'albdesign_project_priority_field');
		
		return self::convert_priority_to_human_priority($priority);
		
	}
	
	
	/*
	* Converts a non-human priority meta to human text
	*/
	static function convert_priority_to_human_priority($priority){
		
		$return = 'Not Set';
		
		if($priority==''){
			return $return;
		}
		
		switch ($priority){
		
			case 'project_priority_not_set':
				$return = 'Not Set';
				break;
				
			case 'project_priority_low':
				$return = 'Low';
				break;
				
			case 'project_priority_normal':
				$return = 'Normal';
				break;
				
			case 'project_priority_high':
				$return = 'High';
				break;	

			default :
				$return = 'Not set';				
				
		}
		
		return $return;
		
	}
	
	
	/*
	* Convert unix to human date
	*/
	static function convert_unix_date_to_human_by_project_id($id,$which_date){
		
		$date = self::get_project_meta_value_by_project_id($id,$which_date);
		
		if(!is_numeric($date)){
			return 'Not Set';
		}
		
		$return_date =  date('d-M-Y',$date);
	
		if($return_date > 0){
			return $return_date;
		}else{
			return 'Not Set';
		}
	
	}
	
	
	/*
	* Lists possible project statuses as dropdown
	*/
	
	static function dropdown_statuses(){
	
		$selected ='';
	
		//check if we have a STATUS selected 
		if(isset($_POST['albdesign_project_status_field']) && $_POST['albdesign_project_status_field']){
			$selected = $_POST['albdesign_project_status_field'];
		}
		
		?><select name="albdesign_project_status_field">
			<option  value="" <?php echo ($selected == '') ? ' selected = "selected" ' : ''; ?> >All</option>
			<option value="project_status_not_set" <?php echo ($selected == 'project_status_not_set') ? ' selected = "selected" ' : ''; ?> >Not set</option>
			<option value="project_status_lead" <?php echo ($selected == 'project_status_lead') ? ' selected = "selected" ' : ''; ?> >Lead</option>
			<option value="project_status_ongoing"  <?php echo ($selected == 'project_status_ongoing') ? ' selected = "selected" ' : ''; ?> >Ongoing</option>
			<option value="project_status_on_hold" <?php echo ($selected == 'project_status_on_hold') ? ' selected = "selected" ' : ''; ?> >On Hold</option>
			<option value="project_status_waiting_feedback" <?php echo ($selected == 'project_status_waiting_feedback') ? ' selected = "selected" ' : ''; ?> >Waiting Feedback</option>
			<option value="project_status_finished" <?php echo ($selected == 'project_status_finished') ? ' selected = "selected" ' : ''; ?> >Completed</option>
		</select>
		
		<?php
	}
	
	
	
	/*
	* Lists possible project priorities as dropdown
	*/
	
	static function dropdown_priorities(){
	
		$selected ='';
	
		//check if we have a PRIORITY selected 
		if(isset($_POST['albdesign_project_priority_field']) && $_POST['albdesign_project_priority_field']){
			$selected = $_POST['albdesign_project_priority_field'];
		}
		
		?><select name="albdesign_project_priority_field">
			<option value="">All</option>
			<option value="project_priority_not_set"  <?php echo ($selected == 'project_priority_not_set') ? ' selected = "selected" ' : ''; ?> >Not set</option>
			<option value="project_priority_low"  <?php echo ($selected == 'project_priority_low') ? ' selected = "selected" ' : ''; ?> >Low</option>
			<option value="project_priority_normal" <?php echo ($selected == 'project_priority_normal') ? ' selected = "selected" ' : ''; ?> >Normal</option>
			<option value="project_priority_high" <?php echo ($selected == 'project_priority_high') ? ' selected = "selected" ' : ''; ?> >High</option>
		</select>
		
		<?php
	}	
	
	
	
	/*
	* Returns all projects as a <select>
	*/
	static function get_all_as_dropdown(){
	
		//check if we have a selected project 
		$selected_project = (isset($_POST['albdesign_projects_invoices_project_field_id']) && $_POST['albdesign_projects_invoices_project_field_id'] > 0 ) ? $_POST['albdesign_projects_invoices_project_field_id'] : '';
	
		$start_of_dropdown = '<select name="albdesign_projects_invoices_project_field_id">';
		$end_of_dropdown = '</select>';
		$dropdown_options ='<option value="-1"> All </option>';
	
		//if we have at least a project
		if(self::get_all() > 0 ){
			
			//get all projects
			$query = array('posts_per_page' => -1, 'post_type' => 'albdesign_project');
			$query_all = new WP_Query();
			$results_all = $query_all->query($query);
			
			foreach($results_all as $single_result_for_client){
			
				//if we have an ID .... return that as the selected OPTION on the SELECT
				$selected = ($selected_project == $single_result_for_client->ID) ? ' selected="selected" ' : '';

				$dropdown_options.='<option value='. $single_result_for_client->ID .' '. $selected . '>' . get_the_title($single_result_for_client->ID) .'</option>';
			
			}

			echo $start_of_dropdown . $dropdown_options . $end_of_dropdown;
			
		}else {
			echo $start_of_dropdown .  $end_of_dropdown;
		}
		
	}
	
	
	/*
	* Lists possible project PROGRESS as dropdown
	*/
	
	static function dropdown_progress(){
	
		$selected ='';
	
		//check if we have a PROGRESS selected 
		if(isset($_POST['albdesign_project_progress_field']) && $_POST['albdesign_project_progress_field']){
			$selected = $_POST['albdesign_project_progress_field'];
		}
		
		?><select  name="albdesign_project_progress_field">
			<option value="">All</option>
			<option value="10"  <?php echo ($selected == '10') ? ' selected = "selected" ' : ''; ?>>10 %</option>
			<option value="20"  <?php echo ($selected == '20') ? ' selected = "selected" ' : ''; ?>>20 %</option>
			<option value="30"  <?php echo ($selected == '30') ? ' selected = "selected" ' : ''; ?>>30 %</option>
			<option value="40"  <?php echo ($selected == '40') ? ' selected = "selected" ' : ''; ?>>40 %</option>
			<option value="50"  <?php echo ($selected == '50') ? ' selected = "selected" ' : ''; ?>>50 %</option>
			<option value="60"  <?php echo ($selected == '60') ? ' selected = "selected" ' : ''; ?>>60 %</option>
			<option value="70"  <?php echo ($selected == '70') ? ' selected = "selected" ' : ''; ?>>70 %</option>
			<option value="80"  <?php echo ($selected == '80') ? ' selected = "selected" ' : ''; ?>>80 %</option>
			<option value="90"  <?php echo ($selected == '90') ? ' selected = "selected" ' : ''; ?>>90 %</option>
			<option value="100" <?php echo ($selected == '100') ? ' selected = "selected" ' : ''; ?>>100 %</option>
		</select>
		
		<?php
	}	

	
	
	/*
	* Create dropdown for dates .... BEFORE , EXACTLY, AFTER
	*/
	
	static function dropdown_before_exactly_after($select_name){
		
		$selected ='';
		$selected_value='';
	
		//check if we have a after,before,exactly selected 
		if(isset($_POST['albdesign_project_'.$select_name.'_before_exactly_after']) && $_POST['albdesign_project_'.$select_name.'_before_exactly_after']){
			$selected = $_POST['albdesign_project_'.$select_name.'_before_exactly_after'];
		}
		
		//check if we have a value for the date
		if(isset($_POST['albdesign_project_'.$select_name.'_field_id']) && $_POST['albdesign_project_'.$select_name.'_field_id']){
			$selected_value = $_POST['albdesign_project_'.$select_name.'_field_id'];
		}		
		
		?>
		<div class="albdesign_input_header">
		<select name="albdesign_project_<?php echo $select_name;?>_before_exactly_after" id="albdesign_<?php echo $select_name;?>_before_exactly_after">
			<option value="0" <?php echo ($selected == '0') ? ' selected = "selected" ' : ''; ?>> All </option>
			<option value="before" <?php echo ($selected == 'before') ? ' selected = "selected" ' : ''; ?>> Before </option>
			<option value="exactly" <?php echo ($selected == 'exactly') ? ' selected = "selected" ' : ''; ?>> Exactly </option>
			<option value="after" <?php echo ($selected == 'after') ? ' selected = "selected" ' : ''; ?>> After </option>
		</select>
		</div>
		<input type="text" name="albdesign_project_<?php echo $select_name;?>_field_id" placeholder="dd-mm-yyyy" value="<?php echo $selected_value;?>">
	
		<?php
	}
	
	
	
	
	/*
	* Prepare the project tab report listing
	*/
	static function get_results_for_report(){
		
		if(sizeof($_POST)<1 ){
			return;
		}
		
		$default_args =array();
		
		//default args to return all Projects
		$default_args = array(
			'showposts'=>-1,
			'post_type' => 'albdesign_project',
			'post_status' => 'publish',		
			
			'meta_query' => array(
				'relation' => 'AND',
			)
			
		);
		
		
		//check if client is set
		if(isset($_POST['albdesign_projects_reports_projectTab_clients_list']) && $_POST['albdesign_projects_reports_projectTab_clients_list']>0){

			$prep_client = array(
							'key' => 'albdesign_project_client_field_id' , 
							'value' => $_POST['albdesign_projects_reports_projectTab_clients_list'] , 
							'compare' => '='
							); 

			array_push( $default_args['meta_query'], $prep_client);
			
		}
		
		
		// check if project priority is set
		if(isset($_POST['albdesign_project_priority_field']) && $_POST['albdesign_project_priority_field']!=''){

			$prep_project_priority = array(
							'key' => 'albdesign_project_priority_field' , 
							'value' => $_POST['albdesign_project_priority_field'] , 
							'compare' => '='
							); 

			array_push( $default_args['meta_query'], $prep_project_priority);
			
		}
		
		
		// check if project PROGRESS is set
		if(isset($_POST['albdesign_project_progress_field']) && $_POST['albdesign_project_progress_field']!=''){

			$prep_project_progress = array(
							'key' => 'albdesign_project_progress_field' , 
							'value' => $_POST['albdesign_project_progress_field'] , 
							'compare' => '='
							); 

			array_push( $default_args['meta_query'], $prep_project_progress);
			
		}		
		
		
		// check if project STATUS is set
		if(isset($_POST['albdesign_project_status_field']) && $_POST['albdesign_project_status_field']!=''){

			$prep_project_status = array(
							'key' => 'albdesign_project_status_field' , 
							'value' => $_POST['albdesign_project_status_field'] , 
							'compare' => '='
							); 

			array_push( $default_args['meta_query'], $prep_project_status);
			
		}
		
		
		
		// check if after,exactly,before  for START DATE is set
		if(isset($_POST['albdesign_project_start_date_before_exactly_after']) && $_POST['albdesign_project_start_date_before_exactly_after']!='0' && isset($_POST['albdesign_project_start_date_field_id']) && $_POST['albdesign_project_start_date_field_id']!=''){

			$start_date_before_exactly_after_from_post = $_POST['albdesign_project_start_date_before_exactly_after'];
			
			if($start_date_before_exactly_after_from_post =='before'){
				$start_date_before_exactly_after = '<';
			}
			
			if($start_date_before_exactly_after_from_post =='exactly'){
				$start_date_before_exactly_after = '=';
			}			
		
			if($start_date_before_exactly_after_from_post =='after'){
				$start_date_before_exactly_after = '>';
			}			
		
			$prep_project_start_date_status = array(
							'key' => 'albdesign_project_start_date_field_id_timestamp' , 
							'value' => Albdesign_Project_Management::convert_human_date_to_unix($_POST['albdesign_project_start_date_field_id']) , 
							'compare' => $start_date_before_exactly_after
							); 

			array_push( $default_args['meta_query'], $prep_project_start_date_status);
			
		}		
		
		
		
		// check if after,exactly,before  for TARGET END DATE is set
		if(isset($_POST['albdesign_project_target_end_date_before_exactly_after']) && $_POST['albdesign_project_target_end_date_before_exactly_after']!='0' && isset($_POST['albdesign_project_target_end_date_field_id']) && $_POST['albdesign_project_target_end_date_field_id']!=''){

			$end_date_before_exactly_after_from_post = $_POST['albdesign_project_target_end_date_before_exactly_after'];
			
			if($end_date_before_exactly_after_from_post =='before'){
				$end_date_before_exactly_after = '<';
			}
			
			if($end_date_before_exactly_after_from_post =='exactly'){
				$end_date_before_exactly_after = '=';
			}			
		
			if($end_date_before_exactly_after_from_post =='after'){
				$end_date_before_exactly_after = '>';
			}			
		
			$prep_project_end_date_status = array(
							'key' => 'albdesign_project_target_end_date_field_id_timestamp' , 
							'value' => Albdesign_Project_Management::convert_human_date_to_unix($_POST['albdesign_project_target_end_date_field_id']) , 
							'compare' => $end_date_before_exactly_after
							); 

			array_push( $default_args['meta_query'], $prep_project_end_date_status);
			
		}			
		
		
		
		// check if after,exactly,before  for ACTUAL END DATE is set
		if(isset($_POST['albdesign_project_actual_end_date_before_exactly_after']) && $_POST['albdesign_project_actual_end_date_before_exactly_after']!='0' && isset($_POST['albdesign_project_actual_end_date_field_id']) && $_POST['albdesign_project_actual_end_date_field_id']!=''){

			$actual_end_date_before_exactly_after_from_post = $_POST['albdesign_project_actual_end_date_before_exactly_after'];
			
			if($actual_end_date_before_exactly_after_from_post =='before'){
				$actual_end_date_before_exactly_after = '<';
			}
			
			if($actual_end_date_before_exactly_after_from_post =='exactly'){
				$actual_end_date_before_exactly_after = '=';
			}			
		
			if($actual_end_date_before_exactly_after_from_post =='after'){
				$actual_end_date_before_exactly_after = '>';
			}			
		
			$prep_project_actual_end_date_status = array(
							'key' => 'albdesign_project_end_date_field_id_timestamp' , 
							'value' => Albdesign_Project_Management::convert_human_date_to_unix($_POST['albdesign_project_actual_end_date_field_id']) , 
							'compare' => $actual_end_date_before_exactly_after
							); 

			array_push( $default_args['meta_query'], $prep_project_actual_end_date_status);
			
		}		
		
		//print_r($default_args);
		
		//get all projects
		$query_all_projects = new WP_Query();
		$results_all_projects = $query_all_projects->query($default_args);		
		
		$projects_found = (sizeof($results_all_projects)>1)  ? ' projects ' : ' project ' ; 
		
		echo apply_filters('albwppm_reports_project_page_found_projects_title','<h3>Found ' . sizeof($results_all_projects) .' '. $projects_found .'</h3>', sizeof($results_all_projects) , $projects_found);
	

		
		//if we have at least one project ... show the table
		if(sizeof($results_all_projects)>=1){
			
			?>
			<div style="padding: 20px;">
			
			<table class="wp-list-table widefat fixed posts albwppm_reports_project_page">
				<thead>
					<tr>
						<th scope="col"  class="check-column manage-column column-title sortable desc " style="padding-top:0px;width: 3em;">
							<a class="table_header_text_link"><span><strong>ID</strong></span></a>
						</th>					
						<th scope="col" class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong>Title</strong></span></a>
						</th>
						<th scope="col"  class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong>Client</strong></span></a>
						</th>			

						<th scope="col" class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong>Progress %</strong></span></a>
						</th>							
						
						<th scope="col" class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong>Status</strong></span></a>
						</th>
						<th scope="col" class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong>Priority</strong></span></a>
						</th>	

						<th scope="col" class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong>Start date</strong></span></a>
						</th>
						<th scope="col" class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong>Target end date</strong></span></a>
						</th>
						<th scope="col" class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong>Actual end date</strong></span></a>
						</th>						
						
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th scope="col"  class="manage-column column-title sortable desc" style="width: 3em;">
							<a class="table_header_text_link"><span><strong>ID</strong></span></a>
						</th>						
						<th scope="col"  class="manage-column column-title sortable desc">
							<a class="table_header_text_link"><span><strong>Title</strong></span></a>
						</th>
						<th scope="col"  class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong>Client</strong></span></a>
						</th>					

						<th scope="col" class="manage-column column-title sortable desc">
							<a class="table_header_text_link"><span><strong>Progress %</strong></span></a>
						</th>							
						
						<th scope="col" class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong>Status</strong></span></a>
						</th>			
						<th scope="col" class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong>Priority</strong></span></a>
						</th>	

						<th scope="col" class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong>Start date </strong></span></a>
						</th>
						<th scope="col" class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong>Target end date</strong></span></a>
						</th>
						<th scope="col" class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong>Actual End date</strong></span></a>
						</th>	
						
					</tr>
				</tfoot>	
				<tbody id="the-list">	
					
				
				

			
			<?php
			
			$row_counter = 0;
			$color = '';
			
			foreach ($results_all_projects as $single_result){ 
				
				if($row_counter%2==0){
					$color='alternate';
				}else{
					$color='';
				}
			
				?>
				
				<tr class=" hentry  iedit <?php echo $color ;?> widefat">
					<td class="check-column" style="padding:9px 0px 8px 10px;">
						<a href="<?php echo get_edit_post_link($single_result->ID);?>"><?php echo $single_result->ID ;?></a>
					</td>

					<td class="post-title page-title column-title">
							<a href="<?php echo get_edit_post_link($single_result->ID);?>"><strong><?php echo $single_result->post_title ;?></strong></a>
					</td>	
					
					<td class="post-title page-title column-title">
							<?php echo self::get_client_name_by_project_id($single_result->ID) ;?>
					</td>		

					<td class="post-title page-title column-title">
							<?php echo self::get_project_meta_value_by_project_id($single_result->ID,'albdesign_project_progress_field') ;?>
					</td>					
					
					<td class="post-title page-title column-title"> 
							<?php echo self::get_human_status_by_project_id($single_result->ID) ;?>
					</td>	
					
					<td class="post-title page-title column-title">
							<?php echo self::get_human_priority_by_project_id($single_result->ID,'albdesign_project_priority_field') ;?>
					</td>					
	

					<td class="post-title page-title column-title">
							<?php echo self::convert_unix_date_to_human_by_project_id($single_result->ID,'albdesign_project_start_date_field_id_timestamp') ;?>
					</td>
					<td class="post-title page-title column-title">
							<?php echo self::convert_unix_date_to_human_by_project_id($single_result->ID,'albdesign_project_target_end_date_field_id_timestamp') ;?>
					</td>
					<td class="post-title page-title column-title">
							<?php echo self::convert_unix_date_to_human_by_project_id($single_result->ID,'albdesign_project_end_date_field_id_timestamp') ;?>
					</td>					
					
					
				</tr>
				
				<?php
				
				$row_counter++ ; 
				
			} //end foreach
			
			?>
			
					</tbody>
				</table>	
			</div>
			
			<?php
		}
	}
} //end class