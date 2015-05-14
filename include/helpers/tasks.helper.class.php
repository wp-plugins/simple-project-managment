<?php
class Albdesign_Projects_Tasks_Helpers{
	
	/*
	* Return number of all tasks by default , OPTIONS is array of additional WP_QUERY params
	*/
	
	static function get_all($options=array()){
	
		$query = array('posts_per_page' => -1, 'post_type' => 'albdesign_task');
		
		if (isset($options['args']) ) {
			$query = array_merge($query,(array)$options['args']);
		}

	
		//get all tasks
		$query_all = new WP_Query();
		$results_all = $query_all->query($query);
		
		return sizeof($results_all) ;
	}
	
	
	/*
	* Return number of completed tasks
	*/
	static function get_completed(){
	
		$k['args'] = array( 'meta_key'=>'albdesign_task_status_task_field',
							'meta_value'=> 'task_status_finished'
						 );
		
		return self::get_all($k) ;
	}	
	
	
	/*
	* Get task by status ... completed , ongoing , hold
	*/
	static function get_by_status($status){
	
		switch ($status){
		
			case 'not_started':
				$which_status = 'task_status_not_started';
				break;
				
			case 'ongoing':
				$which_status = 'task_status_ongoing';
				break;
				
			case 'onhold':
				$which_status = 'task_status_onhold';
				break;
				
			case 'completed':
				$which_status = 'task_status_finished';
				break;					

			default :
				$which_status = 'task_status_finished';				
				
		}
	
		$k['args'] = array( 'meta_key'   =>  'albdesign_task_status_task_field',
							'meta_value' => $which_status
						 );
		
		return self::get_all($k) ;
	}		


} //end class
