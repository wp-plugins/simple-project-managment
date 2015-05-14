<?php
class Albdesign_Projects_Clients_Helpers{

	/*
	* Returns a list of invoices associated with client ... Used on extra columns and inside client CPT
	*/
	public static function get_invoices_for_client_extra_columns($clientid,$return_or_show='return'){
	
		if($clientid && ( $clientid > 0)){
			//echo $clientid;
			
			$invoiceStatusToDisplay = 'Not Set';

			//get all projects for this client
			$get_invoices_for_clients_params =array(
				'showposts'=>-1,
				'post_type' => 'albdesign_invoice',
				'post_status' => 'publish',
				'meta_key'=>'albdesign_projects_invoices_client_field_id',
				'meta_value'=> $clientid
			);
			$query_invoices_for_client = new WP_Query();
			$results_invoices_for_client = $query_invoices_for_client->query($get_invoices_for_clients_params);

			
			//if we have at least one invoice for this client
			if(sizeof($results_invoices_for_client)>=1){
			
				$invoicesAssociateWithClient='';
			
				foreach($results_invoices_for_client as $single_invoice_for_client){
					
					//Invoice edit link
					$invoicesAssociateWithClient.= '<a href="post.php?post='.$single_invoice_for_client->ID .'&action=edit">'. $single_invoice_for_client->post_title .'</a>';
					
					//Invoice Status
					if(get_post_meta($single_invoice_for_client->ID , '_albdesign_invoice_notes', true)){
					
						$invoiceStatus = get_post_meta($single_invoice_for_client->ID , '_albdesign_invoice_notes', true);

						$invoiceStatusToDisplay = (isset($invoiceStatus['status'])) ? ucfirst($invoiceStatus['status']) : 'Not set';
					}
					
					$invoicesAssociateWithClient.= ' ' . $invoiceStatusToDisplay ;
					$invoicesAssociateWithClient.= '<br>';
				} //end foreach
			
				
				return $invoicesAssociateWithClient;
				
			
			} //end sizeof

		}
		
		return false;
	}
	
	/*
	* Returns all clients as a <select>
	*/
	static function get_all_as_dropdown(){
	
		//check if we have a selected client 
		$selected_client = (isset($_POST['albdesign_projects_reports_projectTab_clients_list']) && $_POST['albdesign_projects_reports_projectTab_clients_list'] > 0 ) ? $_POST['albdesign_projects_reports_projectTab_clients_list'] : '';
	
		$start_of_dropdown = '<select name="albdesign_projects_reports_projectTab_clients_list">';
		$end_of_dropdown = '</select>';
		$dropdown_options ='<option value="-1"> All </option>';
	
		//if we have at least a client
		if(self::get_all() > 0 ){
			
			//get all clients
			$query = array('posts_per_page' => -1, 'post_type' => 'albdesign_client');
			$query_all = new WP_Query();
			$results_all = $query_all->query($query);
			
			foreach($results_all as $single_result_for_client){
			
				//if we have an ID .... return that as the selected OPTION on the SELECT
				$selected = ($selected_client == $single_result_for_client->ID) ? ' selected="selected" ' : '';

				$dropdown_options.='<option value='. $single_result_for_client->ID .' '. $selected . '>' . get_the_title($single_result_for_client->ID) .'</option>';
			
			}

			echo $start_of_dropdown . $dropdown_options . $end_of_dropdown;
			
		}else {
			echo $start_of_dropdown .  $end_of_dropdown;
		}
		
	}
	
	
	/*
	* Return number of all invoices by default , OPTIONS is array of additional WP_QUERY params
	*/
	
	static function get_all($options=array()){
	
		$query = array('posts_per_page' => -1, 'post_type' => 'albdesign_client');
		
		if (isset($options['args']) ) {
			$query = array_merge($query,(array)$options['args']);
		}

	
		//get all clients
		$query_all = new WP_Query();
		$results_all = $query_all->query($query);
		
		return sizeof($results_all) ;
	}
	
	
	/*
	* Return number of paid invoices
	*/
	static function get_paid(){
	
		$paidInvoicesCount = 0;
	
		//get all projects for this client
		$get_invoices_params =array(
			'showposts'=>-1,
			'post_type' => 'albdesign_invoice',
			'post_status' => 'publish',
		);
		$query_invoices = new WP_Query();
		$results_invoices = $query_invoices->query($get_invoices_params);

		
		//if we have at least one invoice
		if(sizeof($results_invoices)>=1){
		
			foreach($results_invoices as $single_invoice){
				
				//Invoice Status
				if(get_post_meta($single_invoice->ID , '_albdesign_invoice_notes', true)){
					$invoiceStatus = get_post_meta($single_invoice->ID , '_albdesign_invoice_notes', true);
					//print_r($invoiceStatus);
					//die();
					if(isset($invoiceStatus)){
						if($invoiceStatus['status']=='paid'){
							$paidInvoicesCount++;
						}
					}
				}

			} //end foreach

		} //end sizeof
		
		return $paidInvoicesCount;
		
	}		
	
	/*
	* Returns % of paid invoices 
	*/
	static function get_paid_invoices_percent(){
		
		if(self::get_all() <= 0 ){
			return 0;
		}
		
		return ( self::get_paid() / self::get_all() );
	}
	
	
	/*
	* Get invoices by status ... completed , ongoing , hold
	*/
	static function get_by_status($status){
	
		switch ($status){
		
			case 'unpaid':
				$which_status = 'unpaid';
				break;
				
			case 'paid':
				$which_status = 'paid';
				break;
				
			case 'overdue':
				$which_status = 'overdue';
				break;
				
			case 'cancelled':
				$which_status = 'cancelled';
				break;					

			default :
				$which_status = 'paid';				
				
		}
	

	
		$invoicesFound = 0;
	
		//get all projects for this client
		$get_invoices_params =array(
			'showposts'=>-1,
			'post_type' => 'albdesign_invoice',
			'post_status' => 'publish',
		);
		$query_invoices = new WP_Query();
		$results_invoices = $query_invoices->query($get_invoices_params);

		
		//if we have at least one invoice
		if(sizeof($results_invoices)>=1){
		
			foreach($results_invoices as $single_invoice){
				
				//Invoice Status
				if(get_post_meta($single_invoice->ID , '_albdesign_invoice_notes', true)){
					$invoiceStatus = get_post_meta($single_invoice->ID , '_albdesign_invoice_notes', true);
					if(isset($invoiceStatus)){
						if($invoiceStatus['status']==$which_status){
							$invoicesFound++;
						}
					}
				}

			} //end foreach

		} //end sizeof
		
		return $invoicesFound;	

	}			

	/*
	* Get client meta value by client ID
	*/
	static function get_client_meta_value_by_client_id($id,$meta=''){
		
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
	* Create INPUT FIELD
	*/
	static function create_input($input_name,$placeholder=''){
		
		$selected_value ='';
		
		//check if we have a value set for the field
		if(isset($_POST['albdesign_client_'.$input_name.'_field_id']) && $_POST['albdesign_client_'.$input_name.'_field_id']!=''){
			$selected_value = $_POST['albdesign_client_'.$input_name.'_field_id'];
		}	

		echo '<input type="text" name="albdesign_client_'.$input_name.'_field_id" placeholder="'.$placeholder.'" value="'.$selected_value.'">';
		
	}
	
	/*
	* Prepare the clients tab report listing
	*/
	static function get_results_for_report(){

		if(sizeof($_POST)<1 ){
			return;
		}	
		
	
	
		//default args to return all Clients
		$default_args = array(
			'showposts'=>-1,
			'post_type' => 'albdesign_client',
			'post_status' => 'publish',		
			
			'meta_query' => array(
				'relation' => 'AND',
			)
			
		);	
	
		//A client was selected on the client username dropdown
		//get the CPT that has that ID
		if(isset($_POST['albdesign_projects_reports_projectTab_clients_list']) && $_POST['albdesign_projects_reports_projectTab_clients_list']!='-1'){

			$default_args['p'] = $_POST['albdesign_projects_reports_projectTab_clients_list'];
			
		}
		

		//check if client name is set
		if(isset($_POST['albdesign_client_first_name_field_id']) && $_POST['albdesign_client_first_name_field_id']!=''){

			$prep_client_name = array(
							'key' => 'albdesign_client_first_name_field_id' , 
							'value' => $_POST['albdesign_client_first_name_field_id'] , 
							'compare' => 'LIKE'
							); 

			array_push( $default_args['meta_query'], $prep_client_name);
			
		}		
		
		
		//check if client surname is set
		if(isset($_POST['albdesign_client_last_name_field_id']) && $_POST['albdesign_client_last_name_field_id']!=''){

			$prep_client_surname = array(
							'key' => 'albdesign_client_last_name_field_id' , 
							'value' => $_POST['albdesign_client_last_name_field_id'] , 
							'compare' => 'LIKE'
							); 

			array_push( $default_args['meta_query'], $prep_client_surname);
			
		}	

		//check if client email is set
		if(isset($_POST['albdesign_client_email_field_id']) && $_POST['albdesign_client_email_field_id']!=''){

			$prep_client_email = array(
							'key' => 'albdesign_client_email_field_id' , 
							'value' => $_POST['albdesign_client_email_field_id'] , 
							'compare' => 'LIKE'
							); 

			array_push( $default_args['meta_query'], $prep_client_email);
			
		}			

		//check if client phone is set
		if(isset($_POST['albdesign_client_phone_field_id']) && $_POST['albdesign_client_phone_field_id']!=''){

			$prep_client_phone = array(
							'key' => 'albdesign_client_phone_field_id' , 
							'value' => $_POST['albdesign_client_phone_field_id'] , 
							'compare' => 'LIKE'
							); 

			array_push( $default_args['meta_query'], $prep_client_phone);
			
		}	


		//check if client mobile is set
		if(isset($_POST['albdesign_client_mobile_field_id']) && $_POST['albdesign_client_mobile_field_id']!=''){

			$prep_client_mobile = array(
							'key' => 'albdesign_client_mobile_field_id' , 
							'value' => $_POST['albdesign_client_mobile_field_id'] , 
							'compare' => 'LIKE'
							); 

			array_push( $default_args['meta_query'], $prep_client_mobile);
			
		}			
		

		//check if client skype is set
		if(isset($_POST['albdesign_client_skype_field_id']) && $_POST['albdesign_client_skype_field_id']!=''){

			$prep_client_skype = array(
							'key' => 'albdesign_client_skype_field_id' , 
							'value' => $_POST['albdesign_client_skype_field_id'] , 
							'compare' => 'LIKE'
							); 

			array_push( $default_args['meta_query'], $prep_client_skype);
			
		}	

		
		
	
		//get all clients
		$query_all_clients = new WP_Query();
		$results_all_clients = $query_all_clients->query($default_args);			
		$clients_found = (sizeof($results_all_clients)>1)  ? ' clients ' : ' client ' ; 
		
		echo '<div style="padding: 20px;padding-bottom:0px;"><strong>Found ' . sizeof($results_all_clients) .' '. $clients_found .'</strong></div>';

		
		//if we have at least one client ... show the table
		if(sizeof($results_all_clients)>=1){
			
			?>
			<div style="color:red;padding: 20px;">
			
			<table class="wp-list-table widefat fixed posts">
				<thead>
					<tr>
						<th scope="col"  class="check-column manage-column column-title sortable desc " style="padding-top:0px;width: 3em;">
							<a ><span><strong>ID</strong></span></a>
						</th>					
						<th scope="col" class="manage-column column-title sortable desc" >
							<a ><span><strong>Client</strong></span></a>
						</th>
						<th scope="col"  class="manage-column column-title sortable desc" >
							<a ><span><strong>Name</strong></span></a>
						</th>			

						<th scope="col" class="manage-column column-title sortable desc" >
							<a ><span><strong>Surname</strong></span></a>
						</th>							
						
						<th scope="col" class="manage-column column-title sortable desc" >
							<a ><span><strong>Email</strong></span></a>
						</th>
						<th scope="col" class="manage-column column-title sortable desc" >
							<a ><span><strong>Phone</strong></span></a>
						</th>	

						<th scope="col" class="manage-column column-title sortable desc" >
							<a ><span><strong>Mobile</strong></span></a>
						</th>
						<th scope="col" class="manage-column column-title sortable desc" >
							<a ><span><strong>Skype</strong></span></a>
						</th>
							
						
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th scope="col"  class="manage-column column-title sortable desc" style="width: 3em;">
							<a ><span><strong>ID</strong></span></a>
						</th>						
						<th scope="col"  class="manage-column column-title sortable desc" >
							<a ><span><strong>Client</strong></span></a>
						</th>
						<th scope="col"  class="manage-column column-title sortable desc" >
							<a ><span><strong>Name</strong></span></a>
						</th>					

						<th scope="col" class="manage-column column-title sortable desc" >
							<a ><span><strong>Surname</strong></span></a>
						</th>			
						
						<th scope="col" class="manage-column column-title sortable desc" >
							<a ><span><strong>Email</strong></span></a>
						</th>		
						<th scope="col" class="manage-column column-title sortable desc" >
							<a ><span><strong>Phone</strong></span></a>
						</th>		
						<th scope="col" class="manage-column column-title sortable desc" >
							<a ><span><strong>Mobile</strong></span></a>
						</th>	
						<th scope="col" class="manage-column column-title sortable desc" >
							<a ><span><strong>Skype</strong></span></a>
						</th>							

					</tr>
				</tfoot>	
				<tbody id="the-list">	
	
			<?php
			
			$row_counter = 0;
			$color = '';
			
			foreach ($results_all_clients as $single_result){ 
				
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
							<?php echo self::get_client_meta_value_by_client_id($single_result->ID,'albdesign_client_first_name_field_id') ;?>
					</td>		

					<td class="post-title page-title column-title">
							<?php echo self::get_client_meta_value_by_client_id($single_result->ID,'albdesign_client_last_name_field_id') ;?>
					</td>					

					<td class="post-title page-title column-title">
							<?php echo self::get_client_meta_value_by_client_id($single_result->ID,'albdesign_client_email_field_id') ;?>
					</td>	

					<td class="post-title page-title column-title">
							<?php echo self::get_client_meta_value_by_client_id($single_result->ID,'albdesign_client_phone_field_id') ;?>
					</td>					
					
					<td class="post-title page-title column-title">
							<?php echo self::get_client_meta_value_by_client_id($single_result->ID,'albdesign_client_mobile_field_id') ;?>
					</td>	
					<td class="post-title page-title column-title">
							<?php echo self::get_client_meta_value_by_client_id($single_result->ID,'albdesign_client_skype_field_id') ;?>
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